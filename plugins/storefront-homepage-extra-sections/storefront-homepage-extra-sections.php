<?php
/**
 * Plugin Name:         Storefront Homepage Extra Sections
 * Plugin URI:          http://wpdevhq.com/portfolio/storefront-homepage-extra-sections/
 * Description:         A simple plugin that adds custom homepage sections to the Storefront theme - includes a slider configurable via the Customizer.
 * Version:             1.0.1
 * Author:              WPDevHQ
 * Author URI:          http://wpdevhq.com/
 * Requires at least:   4.0
 * Tested up to:        4.5.3
 *
 * Text Domain: storefront-homepage-extra-sections
 * Domain Path: /languages/
 *
 * @package Storefront_Homepage_Extra_Sections
 * @category Core
 * @author WPDevHQ
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Storefront_Homepage_Extra_Sections to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Storefront_Homepage_Extra_Sections
 */
function Storefront_Homepage_Extra_Sections() {
	return Storefront_Homepage_Extra_Sections::instance();
} // End Storefront_Homepage_Extra_Sections()

Storefront_Homepage_Extra_Sections();

/**
 * Main Storefront_Homepage_Extra_Sections Class
 *
 * @class Storefront_Homepage_Extra_Sections
 * @version	1.0.0
 * @since 1.0.0
 * @package	Storefront_Homepage_Extra_Sections
 */
final class Storefront_Homepage_Extra_Sections {
	/**
	 * Storefront_Homepage_Extra_Sections The single instance of Storefront_Homepage_Extra_Sections.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->token 			= 'storefront-homepage-extra-sections';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.2';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'shes_load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'shes_setup' ) );
		
		add_action( 'widgets_init', array( $this, 'shes_section_widgets' ), 999 );
	}

	/**
	 * Main Storefront_Homepage_Extra_Sections Instance
	 *
	 * Ensures only one instance of Storefront_Homepage_Extra_Sections is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Storefront_Homepage_Extra_Sections()
	 * @return Main Storefront_Homepage_Extra_Sections instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function shes_load_plugin_textdomain() {
		load_plugin_textdomain( 'storefront-homepage-extra-sections', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'storefront-homepage-extra-sections' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'storefront-homepage-extra-sections' ), '1.0.0' );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();

		// get theme customizer url
		$url = admin_url() . 'customize.php?';
		$url .= 'url=' . urlencode( site_url() . '?storefront-customizer=true' ) ;
		$url .= '&return=' . urlencode( admin_url() . 'plugins.php' );
		$url .= '&storefront-customizer=true';

		$notices 		= get_option( 'shes_activation_notice', array() );
		$notices[]		= sprintf( __( '%sThanks for installing the Storefront Homepage Extra Sections extension. To get started, visit the %sCustomizer%s.%s %sOpen the Customizer%s', 'storefront-homepage-extra-sections' ), '<p>', '<a href="' . esc_url( $url ) . '">', '</a>', '</p>', '<p><a href="' . esc_url( $url ) . '" class="button button-primary">', '</a></p>' );

		update_option( 'shes_activation_notice', $notices );
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Setup all the things.
	 * Only executes if Storefront or a child theme using Storefront as a parent is active and the extension specific filter returns true.
	 * Child themes can disable this extension using the storefront_homepage_extra_sections_supported filter
	 * @return void
	 */
	public function shes_setup() {

		if ( 'storefront' == get_option( 'template' ) && apply_filters( 'storefront_homepage_extra_sections_supported', true ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'shes_styles' ), 999 );
			add_action( 'wp_enqueue_scripts', array( $this, 'shes_scripts' ) );
			add_action( 'customize_register', array( $this, 'shes_customize_register' ) );
			add_action( 'admin_notices', array( $this, 'shes_customizer_notice' ) );
			add_action( 'homepage', array( $this, 'storefront_homepage_slider' ), 1 );
			add_action( 'homepage', array( $this, 'storefront_tripple_widgets' ), 5 );
			add_action( 'homepage', array( $this, 'storefront_fullwidth_widget' ), 90 );

			// Hide the 'More' section in the customizer
			add_filter( 'storefront_customizer_more', '__return_false' );
		} else {
			add_action( 'admin_notices', array( $this, 'shes_install_storefront_notice' ) );
		}
	}

	/**
	 * Admin notice
	 * Checks the notice setup in install(). If it exists display it then delete the option so it's not displayed again.
	 * @since   1.0.0
	 * @return  void
	 */
	public function shes_customizer_notice() {
		$notices = get_option( 'shes_activation_notice' );

		if ( $notices = get_option( 'shes_activation_notice' ) ) {

			foreach ( $notices as $notice ) {
				echo '<div class="notice is-dismissible updated">' . $notice . '</div>';
			}

			delete_option( 'shes_activation_notice' );
		}
	}

	/**
	 * Storefront install
	 * If the user activates the plugin while having a different parent theme active, prompt them to install Storefront.
	 * @since   1.0.0
	 * @return  void
	 */
	public function shes_install_storefront_notice() {
		echo '<div class="notice is-dismissible updated">
				<p>' . __( 'Storefront Homepage Extra Sections requires that you use Storefront as your parent theme.', 'storefront-homepage-extra-sections' ) . ' <a href="' . esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-theme&theme=storefront' ), 'install-theme_storefront' ) ) .'">' . __( 'Install Storefront now', 'storefront-homepage-extra-sections' ) . '</a></p>
			</div>';
	}
	
	/**
	 * Register our widget areas
	 */
	public function shes_section_widgets() {
		/* Include the widget register initiator */
        include_once( plugin_dir_path( __FILE__ ) . 'assets/widgets-init.php' );
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function shes_customize_register( $wp_customize ) {
		/* Include the customizer class here via require_once call */
        include_once( plugin_dir_path( __FILE__ ) . 'assets/customizer.php' );
	}

	/**
	 * Enqueue CSS and custom styles.
	 * @since   1.0.0
	 * @return  void
	 */
	public function shes_styles() {
		wp_enqueue_style( 'shes-styles', plugins_url( '/assets/css/style.css', __FILE__  ) );
		
		wp_enqueue_style( 'shes-flexslider-css', plugins_url( '/assets/css/flexslider.min.css', __FILE__  ) );		
	}
	
	/**
	 * Enqueue JS and custom scripts.
	 * @since   1.0.0
	 * @return  void
	 */
	public function shes_scripts() {

	    wp_enqueue_script( 'shes-flexslider-js', plugins_url( '/assets/js/jquery.flexslider-min.js', __FILE__ ), array( 'jquery' ), '', true );
	    
		wp_register_script( 'shes-slider-js', plugins_url( '/assets/js/slider.js', __FILE__ ), array( 'shes-flexslider-js' ), '', true );
		
		if ( get_theme_mod( 'shes_slider_direction' ) === 'true' ) {
		    $direction = (bool)get_theme_mod( 'shes_slider_direction' );
		} else {
			$direction = false;
		}		
		
		$data = array (
		    'shes_slider_options' => array (
                'animation' => esc_html( get_theme_mod( 'shes_slider_animation' )),
				'reverse' => $direction //(bool)get_theme_mod( 'shes_slider_direction' ),
			),		
		);
		wp_localize_script( 'shes-slider-js', 'shesVars', $data );
		
		wp_enqueue_script( 'shes-slider-js' );
	}
	
	/**
	 * Homepage Slider section
	 * @since   1.0.0
	 * @return 	void
	 */
	public static function storefront_homepage_slider() {
	    if ( get_theme_mod( 'shes_slider_content' ) == 'products' ) {
		    include_once( plugin_dir_path( __FILE__ ) . 'sections/product-slider.php' );
		} elseif ( get_theme_mod( 'shes_slider_content' ) == 'posts' ) {
			include_once( plugin_dir_path( __FILE__ ) . 'sections/post-slider.php' );
		}		
	}

	/**
	 * Tripple Widgets section
	 * @since   1.0.0
	 * @return 	void
	 */
	public static function storefront_tripple_widgets() {
	    include_once( plugin_dir_path( __FILE__ ) . 'sections/tripple-widgets.php' );
	}
	
	/**
	 * Fullwidth Widget section
	 * @since   1.0.0
	 * @return 	void
	 */
	public static function storefront_fullwidth_widget() {
	    include_once( plugin_dir_path( __FILE__ ) . 'sections/fullwidth-widget.php' );
	}
	
} // End Class