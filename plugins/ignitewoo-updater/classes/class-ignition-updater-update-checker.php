<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Ignition Updater - Plugins/Themes Updater Class
 *
 * The Ignition Updater - plugins/theme updater class.
 *
 * @package WordPress
 * @subpackage Ignition Updater
 * @category Core
 * @author Ignition
 * @since 1.5.0
 */
class Ignition_Updater_Update_Checker {

	/**
	 * URL of endpoint to check for product/changelog info
	 * @var string
	 */
	private $api_url = 'http://ignitewoo.com/api2/';

	/**
	 * URL of endpoint to check for updates
	 * @var string
	 */
	private $update_check_url = 'http://ignitewoo.com/api2/?api=installer-api&';

	/**
	 * Array of plugins info
	 * @var array
	 */
	private $plugins; // 0=file, 1=product_id, 2=file_id, 3=license_hash, 4=version

	/**
	 * Array of themes info
	 * @var array
	 */
	private $themes; // 0=file, 1=product_id, 2=file_id, 3=license_hash, 4=version

	/**
	 * Array of errors during update checks
	 * @var array
	 */
	private $errors = null;

	/**
	 * Plugin version
	 * @var string
	 */
	private $version;
	
	private $token; 

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct ( $plugins, $themes ) {
		global $ignition_updater_token, $ignition_updater;
		
		$this->token = $ignition_updater_token;
		
		$this->version = $ignition_updater->version;
		$this->plugins = $plugins;
		$this->themes = $themes;

		$this->init();
	} // End __construct()

	/**
	 * Initialise the update check process.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function init () {
	
		// Check For Updates
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'plugin_update_check' ), 20, 1 );
		//add_filter( 'pre_set_site_transient_update_themes', array( $this, 'theme_update_check' ), 20, 1 );

		// Check For Plugin Information
		add_filter( 'plugins_api', array( $this, 'plugin_information' ), 20, 3 );

		// Clear the cache when a force update is done via WP
		if ( isset( $_GET['force-check'] ) && 1 == $_GET['force-check'] ) {
			delete_transient( 'ignition_helper_updates' );
		}

		// Clear the cache when a plugin is updated to avoid showing updates for already updated products.
		if ( isset( $_GET['action'] ) && ( 'do-plugin-upgrade' == $_GET['action'] || 'upgrade-plugin' == $_GET['action'] || 'do-theme-upgrade' == $_GET['action'] ) ) {
			delete_transient( 'ignition_helper_updates' );
			
			// This is the version cache for the specific plugin, delete that too.
			if ( !empty( $_REQUEST['checked'] ) && isset( $_REQUEST['checked'][0] ) )
				delete_transient( 'ign_' . esc_attr( sanitize_title( $_REQUEST['checked'][0] ) ) . '_latest_version' );
		}
	} // End init()

	/**
	 * Make a call to IgniteWoo.com and fetch update info for all products and put in transient for 30min
	 * @return bool|array
	 */
	public function fetch_remote_update_data() {
		global $ignition_updater, $ignitewoo_queued_updates;
		$plugins_to_fetch_updates_for = array();

		// Loop through all Ignition plugins/extensions
		foreach ( $this->plugins as $plugin ) {
			// $plugin - 0=file, 1=product_id, 2=file_id, 3=license_hash, 4=version
			// Always fetch all plugins data in one call, we loop to append the url
			$plugin[] = esc_url( home_url( '/' ) );
			$plugins_to_fetch_updates_for[] = $plugin;
		}

		$themes_to_check_updates_for = array();
		// Loop through all Ignition themes
		foreach ( $this->themes as $theme ) {
			// $theme - 0=file, 1=product_id, 2=file_id, 3=license_hash, 4=version
			// Always fetch all theme data in one call, we loop to append the url
			$theme[0] = str_replace( '/style.css', '', $theme[0] );
			$theme[] = esc_url( home_url( '/' ) );
			$themes_to_check_updates_for[] = $theme;
		}

		$helper_update_info = array( plugin_basename( $ignition_updater->file ), $ignition_updater->version, $ignition_updater->admin->licence_hash );

		// Make sure we have data to check for updates
		if ( empty( $plugins_to_fetch_updates_for ) && empty( $helper_update_info ) && empty( $themes_to_check_updates_for ) ) {
			return false;
		}

		$args = array();
		if ( ! empty( $plugins_to_fetch_updates_for ) ) {
			$args['plugins'] = $plugins_to_fetch_updates_for;
		}
		if ( ! empty( $themes_to_check_updates_for ) ) {
			$args['themes'] = $themes_to_check_updates_for;
		}
		if ( ! empty( $helper_update_info ) ) {
			$args['helper'] = $helper_update_info;
		}

		// We store the update data in a cache for X minutes, to avoid multiple calls to IgniteWoo.com as
		// this transient filter fires multiple times when checking for updates in WP. Cache can be cleared
		// by using the check for updates button on the core updates page in WP
		if ( FALSE == $response = get_transient( 'ignition_helper_updates' ) ) {
			$response = $this->request( $args , 'updates' );
			set_transient( 'ignition_helper_updates', $response, 30 * MINUTE_IN_SECONDS );
		}
		return $response;
	} // End fetch_remote_update_data()


	/**
	 * Inject plugin updates into update_plugins transient
	 *
	 * @access public
	 * @since  1.0.0
	 * @param  object $transient
	 * @return object $transient
	 */
	public function plugin_update_check ( $transient ) {

		// Eliminate empty objects taht don't need updates. Not sure how one might get in there but it does.
		$no_updates = isset( $transient->no_update ) ? $transient->no_update : array();
		
		// Failsafe in case the $transient->no_update is set but is not an array
		if ( empty( $no_updates ) )
			$no_updates = array();
			
		$transient->no_update = array();
		foreach( $no_updates as $k => $v ) { 
			if ( !empty( $k ) && !empty( $v ) )
				$transient->no_update[ $k ] = $v;
		}
	
		$response = $this->fetch_remote_update_data();

		if ( FALSE == $response ) {
			return $transient;
		}

		// Set plugin update info into transient
		if ( isset( $response->plugins ) ) {
 			$activated_products = get_option( $this->token . '-activated', array() );

 			if ( empty( $activated_products ) )
				$activated_products = array();
				
				
			foreach ( $response->plugins as $plugin_key => $plugin ) {

				if ( empty( $plugin_key ) )
					continue;
					
				if ( isset( $plugin->no_update ) ) {
					if ( isset( $plugin->license_expiry_date ) ) {
						$activated_products[ $plugin_key ][3] = $plugin->license_expiry_date;
					}
					$transient->no_update[ $plugin_key ] = $plugin;

					// Make sure we have a slug, and that the value reflects the directory name for each plugin only.
					if ( isset( $transient->no_update[$plugin_key]->slug ) ) {
						$transient->no_update[$plugin_key]->slug = dirname( $transient->no_update[$plugin_key]->slug );
					} else {
						$transient->no_update[$plugin_key]->slug = dirname( $plugin_key );
					}
					
				// Deactivate a product
				} elseif ( isset( $plugin->deactivate ) ) {

					$this->errors[] = $plugin->deactivate;
					global $ignition_updater;
					$ignition_updater->admin->deactivate_product( $plugin_key, true );
					
				// If there is an error returned, log that no update is available.
				} elseif ( isset( $plugin->error ) ) {
					$this->errors[] = $plugin->error;
					$transient->no_update[ $plugin_key ] = $plugin;
					
				// If there is a new version, check the license expiry date and update it locally.
				} elseif ( isset( $plugin->new_version ) && ! empty( $plugin->new_version ) ) {
					if ( isset( $plugin->license_expiry_date ) ) {
						$activated_products[ $plugin_key ][3] = $plugin->license_expiry_date;
						unset( $plugin->license_expiry_date );
					}
					$transient->response[ $plugin_key ] = $plugin;
					
				} else {
					if ( isset( $plugin->license_expiry_date ) ) {
						$activated_products[ $plugin_key ][3] = $plugin->license_expiry_date;
					}
					$transient->no_update[ $plugin_key ] = $plugin;
				}
				
				// Make sure we have a slug, and that the value reflects the directory name for each plugin only.
				if ( isset( $transient->response[$plugin_key] ) ) {
					$transient->response[$plugin_key]->slug = dirname( $plugin_key );
					
				} else if ( isset( $transient->no_update[$plugin_key]  ) ) {
					$transient->no_update[$plugin_key]->slug = dirname( $plugin_key );
					
				} else {
					if ( '' != $plugin_key && isset( $transient->response[$plugin_key] ) ) {
						$transient->response[$plugin_key]->slug = dirname( $plugin_key );
					}
				}
			}
//var_dump( 'AP:' , $activated_products ); 
			update_option( $this->token . '-activated', $activated_products );
		}

		// Set Ignition Helper update info into transient
		if ( isset( $response->helper ) ) {
			foreach ( $response->helper as $plugin_key => $plugin ) {
				if ( isset( $plugin->no_update ) ) {
					$transient->no_update[ $plugin_key ] = $plugin;
				} elseif ( isset( $plugin->error ) ) {
					$this->errors[] = $plugin->error;
					$transient->no_update[ $plugin_key ] = $plugin;
				} elseif ( isset( $plugin->new_version ) && ! empty( $plugin->new_version ) ) {
					$transient->response[ $plugin_key ] = $plugin;
				} else {
					$transient->no_update[ $plugin_key ] = $plugin;
				}
			}
		}

		// Check if we must output error messages
		if ( count( $this->errors ) > 0 ) {
			add_action( 'admin_notices', array( $this, 'error_notices') );
		}

		return $transient;
	} // End plugin_update_check()

	/**
	 * Inject theme updates into update_themes transient
	 *
	 * @access public
	 * @since  1.5.0
	 * @param  object $transient
	 * @return object $transient
	 */
	public function theme_update_check( $transient ) {
		$response = $this->fetch_remote_update_data();

		if ( FALSE == $response ) {
			return $transient;
		}

		if ( isset( $response->themes ) ) {
			$activated_products = get_option( $this->token . '-activated', array() );
			foreach ( $response->themes as $theme_key => $theme ) {
				if ( isset( $theme->new_version ) ) {
					if ( isset( $theme->license_expiry_date ) ) {
						$activated_products[ $theme_key ][3] = $theme->license_expiry_date;
					}
					$transient->response[ $theme_key ]['new_version'] = $theme->new_version;
		        	$transient->response[ $theme_key ]['url'] = 'http://ignitewoo.com/';
		        	$transient->response[ $theme_key ]['package'] = $theme->package;
				} elseif ( isset( $theme->error ) ) {
					$this->errors[] = $theme->error;
				} elseif ( isset( $theme->deactivate ) ) {
					$this->errors[] = $theme->deactivate;
					global $ignition_updater;
					$ignition_updater->admin->deactivate_product( $theme_key . '/style.css', true );
				} else {
					if ( isset( $theme->license_expiry_date ) ) {
						$activated_products[ $theme_key ][3] = $theme->license_expiry_date;
					}
				}
			}
			update_option( $this->token . '-activated', $activated_products );
		}

		// Check if we must output error messages
		if ( count( $this->errors ) > 0 ) {
			add_action( 'admin_notices', array( $this, 'error_notices') );
		}

		return $transient;
	} // End theme_update_check()

	/**
	 * Display an error notice
	 * @param  strin $message The message
	 * @return void
	 */
	public function error_notices () {
		if ( isset( $this->errors ) && count( $this->errors ) ) {
			$messages = array();
			foreach ( $this->errors as $error ) {
				$messages[] = '<p>' . $error . '</p>';
			}
			echo '<div id="message" class="error">' . implode( '', $messages ) . '</div>';
			$this->errors = null;
		}
	} // End error_notices()

	/**
	 * Check for the plugin's data against the remote server.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return object $response
	 */
	public function plugin_information ( $false, $action, $args ) {
		global $ignition_updater;
		
		$transient = get_site_transient( 'update_plugins' );
		$found = false;
		$found_plugin = array();

		// Make sure a slug is set
		if ( ! isset( $args->slug ) ) {
			return $false;
		}

		if ( 'ignitewoo-updater' == $args->slug ) { 
		
			$found = true;
			$found_plugin = array(
				0 => $ignition_updater->admin->slug,
				1 => 'Updater', // product ID
				2 => $ignition_updater->admin->licence_hash, // secret key,
				3 => $ignition_updater->admin->licence_hash, // hash,
				4 => $ignition_updater->version, // version
				5 => $args->slug, // slug without file name
			);
	
		} else { 
			
			// Loop through all plugins
			foreach ( $this->plugins as $plugin ) {
				// $plugin - 0=file, 1=product_id, 2=file_id, 3=license_hash

				// Check if this plugins API is about one of our plugins
				if ( $args->slug == $plugin[5] /*&& isset( $transient->checked[ $plugin[0] ] )*/ ) {
					$found = true;
					$found_plugin = $plugin;
				}
			}
		}

		// If the plugin info is not about any of our plugins, bail!
		if ( ! $found ) {
			return $false;
		}
		
		$ver = isset( $transient->checked[ $found_plugin[0] ] ) ? $transient->checked[ $found_plugin[0] ] : '';
		if ( empty( $ver ) && 'ignitewoo-updater' == $args->slug )
			$ver = $ignition_updater->version;

		// POST data to send to your API
		$args = array(
			'request' => 'plugininformation',
			'plugin_name' => $found_plugin[0],
			'version' => $ver,
			'product_id' => $found_plugin[1],
			'file_id' => $found_plugin[2],
			'license_hash' => $found_plugin[3],
			'url' => esc_url( home_url( '/' ) ),
		);
		
		if ( 'ignitewoo-updater' == $found_plugin[5] )
			$args['slug'] = $found_plugin[5];

		// Send request for detailed information
		$response = $this->request( $args );

		$response->sections = (array)$response->sections;

		// Make sure we have the changelog set, if not try to populate via changelog file
		/* Deprecated. No longer used.
		if ( ! isset( $response->sections['changelog'] ) ) {
			$changelog_url = '';
			if ( isset( $response->changelog_url ) ) {
				$changelog_url = esc_url( $response->changelog_url );
			} else {
				$slug = explode( '/', $args['plugin_name'] );
				if ( isset( $slug[0] ) ) {
					$slug = sanitize_title( $slug[0] );
					$changelog_url = 'http://dzv365zjfbd8v.cloudfront.net/changelogs/' . $slug . '/changelog.txt';
				}
			}
			if ( '' != $changelog_url ) {
				$changelog_content = wp_remote_get( $changelog_url );
				if ( ! is_wp_error( $changelog_content ) ) {
					$changelog_content = wp_remote_retrieve_body( $changelog_content );
					$changelog_lines = explode( "\n", $changelog_content );
					$changelog_html = '';
					foreach ( $changelog_lines as $line ) {
						$changelog_html .= $this->parse_changelog_line_to_html( $line );
					}
					$response->sections['changelog'] = $changelog_html;
				} else {
					$response->sections['changelog'] = "<p>Sorry, there was a problem fetching the changelog details: " . $changelog_content->get_error_message() . "</p>";
				}
			} else {
				$response->sections['changelog'] = "<p>Sorry, there was a problem fetching the changelog details, please try again.</p>";
			}
		}
		*/
		
		$response->compatibility = isset( $response->compatibility ) ? (array)$response->compatibility : array();
		$response->tags = isset( $response->tags ) ? (array)$response->tags : array();
		$response->contributors = isset( $response->contributors ) ? (array)$response->contributors : array();

		if ( count( $response->compatibility ) > 0 ) {
			foreach ( $response->compatibility as $k => $v ) {
				$response->compatibility[$k] = (array)$v;
			}
		}

		// Set a banner if one not provided from the API request.
		// This needs to be an array, so convert it if returned.
		if ( isset( $response->banners ) ) {
		
			$b = $response->banners;
			
			$response->banners = array(
				'low' => $b->low,
				'high' => $b->high,
			);
			
		} else if ( ! isset( $response->banners ) ) {
			$response->banners['low'] = '//ignitewoo.com/api2/ignitewoo-banner-1544x500.png';
			$response->banners['high'] = '//ignitewoo.com/api2/ignitewoo-banner-1544x500.png';
		}
		
		
		return $response;
	} // End plugin_information()

	/**
	 * Generic request helper.
	 *
	 * @access private
	 * @since  1.0.0
	 * @param  array $args
	 * @return object $response or boolean false
	 */
	protected function request ( $args, $api = 'info' ) {

		$url = ( $api == 'info' ) ? $this->api_url : $this->update_check_url;
		
		$url = add_query_arg( array( 'wc-api' => 'product-key-api' ), $url );
		$url = add_query_arg( array( 'home_url' => home_url( '/' ) ), $url );

		// Send request
		$request = wp_remote_post( $url, array(
			'method' => 'POST',
			'timeout' => 15,
			'redirection' => 5,
			'httpversion' => '1.0',
			'headers' => array( 'user-agent' => 'IgnitionUpdater/' . $this->version ),
			'body' => $args,
			'sslverify' => false
			) );

		// Make sure the request was successful
		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			trigger_error( __( 'An unexpected error occurred. Something may be wrong with IgniteWoo.com or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://ignitewoo.com/hc/en-us">help center</a>.', 'ignition-updater' ) . ' ' . __( '(WordPress could not establish a secure connection to IgniteWoo.com. Please contact your server administrator.)', 'ignition-updater' ), headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE );
			return false;
		}
		// Read server response, which should be an object
		if ( $request !== '' ) {
			$response = json_decode( wp_remote_retrieve_body( $request ) );
		} else {
			$response = false;
		}

		if ( is_object( $response ) && isset( $response->payload ) ) {
			return $response->payload;
		} else {
			// Unexpected response
			return false;
		}
	} // End request()

	/**
	 * Parse changelog lines and convert to html
	 *
	 * @since  1.5.0
	 * @param  string $text plain text string
	 * @return string html version of the plain text string
	 */
	public function parse_changelog_line_to_html( $text ) {
		// Skip heading
		if ( '***' == substr( $text, 0, 3 ) ) {
			return '';
		}

		// Check for date and version
		if ( '20' == substr( $text, 0, 2 ) ) {
			return '<h4>' . $text . '</h4>';
		}

		// Check if listitem
		if ( ' * ' == substr( $text, 0, 3 ) || '* ' == substr( $text, 0, 2 ) ) {
			return '<li>' . trim( $text, ' * ' ) . '</li>';
		}

		return $text;
	} // End parse_changelog_line_to_html()

} // End Class
?>