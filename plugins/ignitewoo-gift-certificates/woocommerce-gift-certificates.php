<?php
/*
Plugin Name: WooCommerce Gift Certificates Pro
Plugin URI: http://ignitewoo.com
Description: WooCommerce Gift Certificates Pro allows you to sell gift certificates / store credits / coupon codes as products in your store. You can also offer gift certificates / coupons as an additional item to an existing product.
Version: 3.5.26
NOTE: ***** Set $this->version *****
Author: IgniteWoo.com
Author URI: http://ignitewoo.com

Copyright (c) 2012-2014 IgniteWoo.com - All Rights Reserved

Be sure and purchase a license for each site you wish to use this on.
*/


if ( !class_exists( 'Ignite_Gift_Certs' ) ) {
    
	class Ignite_Gift_Certs {

		var $admin = null;

		public function __construct() {

			$this->version = '3.4'; // used to determine when data updates or template updates are required 

			$this->plugin_url = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '' , plugin_basename( __FILE__ ) );
			
			$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );

			if ( defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN == true )
				$this->plugin_url = str_replace( 'http://', 'https://', $this->plugin_url );
			
			$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
			
			if ( is_admin() )
				require_once( dirname( __FILE__ ) . '/classes/class-product-settings.php' );
				
			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			
			$this->admin_settings = get_option( 'woocommerce_woocommerce_gift_certificates_settings' );

			add_action( 'init', array( &$this, 'load_plugin_textdomain' ) );

			// Check for mininum WooCommerce version
			add_action( 'init', array( &$this, 'software_tests' ), 99991 );

			// Load pricer
			add_action( 'init', array( &$this, 'pricer_init' ), 1 );
			
			// Taxo
			add_action( 'init', array( &$this, 'register_taxonomy' ), 100 );
			
			// DL
			add_action( 'init', array( &$this, 'download_voucher' ), 10 );
			
			// scripts for checkout
			add_action( 'wp', array( &$this, 'wp' ), 100 );
			
			// Maybe run upgrade
			add_action( 'init', array( &$this, 'maybe_run_upgrader' ), 9999 );
			
			add_action( 'admin_init', array( &$this, 'admin_init' ), 20 );
			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ), 20 );
			
			// Add coupon type
			add_filter( 'woocommerce_coupon_discount_types', array( &$this, 'add_coupon_code_type' ) );
			
			// Add my account page data
			add_action( 'woocommerce_after_my_account', array( &$this, 'my_account' ) );
			
			// Check code validity
			add_filter( 'woocommerce_coupon_is_valid', array( &$this, 'is_code_valid' ), 10, 2 );

			// add_action( 'woocommerce_process_product_meta_variable', array( &$this, 'process_product_meta' ) );
			
			// add_action( 'save_post', array( &$this, 'save_post_first' ), 1, 1 );
			// add_action( 'save_post', array( &$this, 'save_original_post_title' ), 999, 1 );

			// Issue vouchers / Process vouchers
			if ( empty( $this->admin_settings['order_status_trigger'] ) || 'completed' ==  $this->admin_settings['order_status_trigger'] ) {
			
				// If triggered on Completed status then add the voucher, and set a hook to deactivate 
				// if status goes to Processing - this is the original behavior of earlier versions
				add_action( 'woocommerce_order_status_completed', array( &$this, 'add_vouchers' ), 20, 1 );
				add_action( 'woocommerce_order_status_processing', array( &$this, 'deactivate_vouchers' ), 20, 1 );
				
			} else if ( 'processing' ==  $this->admin_settings['order_status_trigger'] ) { 
			
				// If the trigger is Processing, add a voucher when the order gains that status
				// Trigger on both statuses because sometimes an order might be marked Processing if
				// other items in the order must be shipped, and sometimes Completed if all items are virtual
				add_action( 'woocommerce_order_status_processing', array( &$this, 'add_vouchers' ), 20, 1 );
				add_action( 'woocommerce_order_status_completed', array( &$this, 'add_vouchers' ), 20, 1 );
			}
			
			// Credit a voucher if one is used and was previously refunded
			add_action( 'woocommerce_order_status_completed', array( &$this, 'debit_voucher' ), 20, 1 );
			add_action( 'woocommerce_order_status_pending', array( &$this, 'debit_voucher' ), 20, 1 );
			add_action( 'woocommerce_order_status_processing', array( &$this, 'debit_voucher' ), 20, 1 );
			add_action( 'woocommerce_order_status_on-hold', array( &$this, 'debit_voucher' ), 20, 1 );

			// Issue refunds when a voucher was used
			add_action( 'woocommerce_order_status_refunded', array( &$this, 'refund_vouchers' ), 20, 1 );
			add_action( 'woocommerce_order_status_cancelled', array( &$this, 'refund_vouchers' ), 20, 1 );

			// Deactivate vouchers
			add_action( 'woocommerce_order_status_pending', array( &$this, 'deactivate_vouchers' ), 20, 1 );
			add_action( 'woocommerce_order_status_on-hold', array( &$this, 'deactivate_vouchers' ), 20, 1 );
			add_action( 'woocommerce_order_status_failed', array( &$this, 'deactivate_vouchers' ), 20, 1 );
			
			// Add note to the public product page when the product comes with codes
			add_action( 'woocommerce_after_add_to_cart_button', array( &$this, 'show_product_vouchers' ), 9999999 );

			// Add forms to checkout page, verify form data entry
			add_action( 'woocommerce_checkout_shipping', array( &$this, 'recipient_detail_form' ), 999, 5 );
			add_action( 'woocommerce_before_checkout_process', array( &$this, 'verify_recipient_details' ), 11 );
/*
TEST FOR WC VERSION AND ADD COMPATIBLE HOOKS - DUPLICATE OLD FUNCTIONS AND FALLBACK TO THOSE WHEN THE PLUGIN SETTINGS HAVE NOT BEEN UPDATED - store a flag so we know this version has settings updated, this way we know the admin say the message on the settings screen about reviewing their custom template files. What about backward compatibility for previous orders? Recipient info is now stored in item meta, not order meta - so add some checking for that to see if order meta is set, if so its an old order so process it with the old routines, otherwise use the new routines. 

Can we somehow hook into tax calcs and not remove tax if its a gift cert produuct.
*/

			// Process new order data
			add_action( 'woocommerce_checkout_update_order_meta', array( &$this, 'maybe_save_recipient_info' ), 1, 1 );

			// Remove gift certificates / store credits / coupon codes if config'd to do so
			add_action( 'woocommerce_new_order', array( &$this, 'maybe_update_or_remove' ), 10, 1 );

			//add_filter( 'woocommerce_coupon_get_discount_amount', array( &$this, 'get_discount_amount' ), 10, 5 );
			//add_filter( 'woocommerce_coupon_is_valid_for_cart', array( &$this, 'is_valid_for_cart' ), 10, 2 );
			
			add_filter( 'woocommerce_apply_with_individual_use_coupon', array( &$this, 'maybe_allow_individual_use_coupons' ), 99, 4 );
			add_filter( 'woocommerce_apply_individual_use_coupon', array( &$this, 'maybe_apply_individual_use_coupon' ), 1, 3 );
			add_filter( 'woocommerce_apply_individual_use_coupon', array( &$this, 'maybe_apply_individual_use_coupon' ), 9999, 3 );
			
				
			
			// Add section to My Account page to show balance to customer when necessary
			add_action( 'woocommerce_before_my_account', array( &$this, 'show_customer_vouchers' ) );

			if ( defined( 'DOING_AJAX' ) ) { 

				add_action( 'wp_ajax_ign_gc_preview', array( &$this, 'preview' ) );
				
				add_action( 'wp_ajax_update_voucher_balance', array( &$this, 'update_voucher_balance') );
			}

			if ( is_admin() ) {

				require_once( dirname( __FILE__ ) . '/classes/class-welcome.php' );
				
				add_action( 'admin_menu', array( &$this, 'init_gc_integration') );

			}
			
			add_image_size( 'ignitewoo_voucher_thumb_size', '100' );

			add_filter( 'single_template', array( &$this, 'ign_vouchers_locate_preview_template' ) );
			
			$this->title_to_lowercase();

		}

		// Fix coupon codes to lower case since WC changed things AGAIN. 
		function title_to_lowercase() { 
			global $wpdb;
			
			if ( get_option( 'ignitewoo_coupon_lowered' ) )
				return;
							
			$sql = 'update ' . $wpdb->posts . ' set post_title = replace( post_title, post_title, LOWER( post_title ) ) where post_type = "shop_coupon"';
			
			$wpdb->query( $sql );
			
			update_option( 'ignitewoo_coupon_lowered', 1 );
			
		
		}
		
		function load_plugin_textdomain() {

			$locale = apply_filters( 'plugin_locale', get_locale(), 'ignitewoo_gift_certs' );

			load_textdomain( 'ignitewoo_gift_certs', WP_LANG_DIR.'/woocommerce/ignitewoo_gift_certs-'.$locale.'.mo' );

			$plugin_rel_path = apply_filters( 'ignitewoo_translation_file_rel_path', dirname( plugin_basename( __FILE__ ) ) . '/languages' );

			load_plugin_textdomain( 'ignitewoo_gift_certs', false, $plugin_rel_path );

		}

		
		function activate() {
		
			set_transient( '_ign_gc_activation_redirect', 1, 60 * 60 );
			
		}

		
		function updater_notices() { 
			global $woocommerce;
			
			if ( version_compare( $woocommerce->version, '2.1' ,'<' ) )  
				$url = add_query_arg( 'do_update_ign_certs', 'true', admin_url('admin.php?page=woocommerce_settings&tab=gift_cert') );
			else 
				$url = add_query_arg( 'do_update_ign_certs', 'true', admin_url('admin.php?page=wc-settings&tab=gift_cert') );
		
			?>
			<div id="message" class="updated" style="border-left: 4px solid #ffba00">
				<div class="squeezer" style="max-width:100%">
					<h4><?php _e( '<strong>Gift Certificates v' . $this->version . ' - Data Update Required!</strong> &#8211; We just need to update your install to the latest version', 'ignitewoo_gift_certs' ); ?>
					<span class="submit" style="padding-left: 1em;"><a href="<?php echo $url ?>" class="button"><?php _e( 'Run the updater', 'ignitewoo_gift_certs' ); ?></a></span>
					</h4>
				</div>
			</div>
			<script type="text/javascript">
				jQuery('.ign-gc-update-now').click('click', function(){
					var answer = confirm( '<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'ignitewoo_gift_certs' ); ?>' );
					return answer;
				});
			</script>
			<?php
			
		}
		
		
		function updater_templates() { 
			global $user_ID, $woocommerce;

			if ( version_compare( $woocommerce->version, '2.1' ,'<' ) ) 
				$url = add_query_arg( 'ign_certs_remove_nag', 'true', admin_url('admin.php?page=woocommerce_settings&tab=gift_cert') );
			else 
				$url = add_query_arg( 'ign_certs_remove_nag', 'true', admin_url('admin.php?page=wc-settings&tab=gift_cert') );
				
			$vstring = str_replace( '.' , '_', $this->version );
			
			if ( 1 != get_option( '_ign_gc_needs_update' ) && '' != get_user_meta( $user_ID, 'ign_gc_admins_notified_' . $vstring, true ) )
				return;
		
			?>
			<div id="message" class="updated" style="border-left: 4px solid #ffba00">
				<div class="squeezer" style="max-width:100%">
					<h4><?php _e( '<strong>Gift Certificates v' . $this->version . ' - Template Updates?</strong> &#8211; Check your gift certificate / voucher templates for any necessary changes. Be sure to review the plugin\'s changelog on our site!', 'ignitewoo_gift_certs' ); ?>
					<br/>
					<span class="submit" style="text-align:left"><a href="<?php echo $url; ?>" class="button"><?php _e( 'I checked already', 'ignitewoo_gift_certs' ); ?></a></span>
					<span class="submit" style="text-align:left"><a href="http://ignitewoo.com/ignitewoo-software-documentation/" target="_blank" class="button"><?php _e( 'View Documentation', 'ignitewoo_gift_certs' ); ?></a></span>
					</h4>
				</div>
			</div>
			<?php if ( 1 == get_option( '_ign_gc_needs_update' ) ) { ?>
			<script type="text/javascript">
				jQuery('.ign-gc-update-templates').click('click', function(){
					var answer = confirm( '<?php _e( 'This message will continue to appear until you run the data  upgrader', 'ignitewoo_gift_certs' ); ?>' );
					return answer;
				});
			</script>
			<?php } ?>
			<?php
			
		}
		
		function updater_complete() { 
			?>
			<div id="message" class="updated">
				<div class="squeezer" style="max-width:100%">
					<h4><?php _e( '<strong>Gift Certificates - Data Update Complete!', 'ignitewoo_gift_certs' ); ?></h4>
				</div>
			</div>
			
			<?php
			
		}
		
		
		function preview() {
			global $ignite_gift_certs;

			if ( !wp_verify_nonce( $_POST['n'], 'ign_gc_preview' ) )
				die;

			$msg_details = array( 'voucher_to_email' => __( 'to@thisperson.com', 'ignitewoo_gift_certs' ),
						'voucher_from_email' => __( 'from@thisotherperson.com', 'ignitewoo_gift_certs' ),
						'voucher_from_name' => __( 'Mr. Sender', 'ignitewoo_gift_certs' ),
						'voucher_to_name' => __( 'Mr. Recipient', 'ignitewoo_gift_certs' ),
						'voucher_message' => __( "This would be the custom message text entered by the buyer and sent to the recipient as part of the overall email message from your store. ", 'ignitewoo_gift_certs' ),
					);
			echo $ignite_gift_certs->send_voucher( 'SAMPLECODE123', '100', 'ign_store_credit', '31 December 2020', $msg_details, 7777, null, 0, false, true );

			die;
		}
		
		
		function update_voucher_balance() { 
			global $wpdb;

			if ( !wp_verify_nonce( $_POST['n'], 'ign_update_voucher_balance' ) )
				die( '1' );
			
			if ( !isset( $_POST['balance' ] ) || !isset( $_POST['vid'] ) || empty( $_POST['vid'] ) )
				die( '2' );
				
			$balance = floatval( $_POST['balance'] );
			
			$pid = absint( $_POST['vid'] );
			
			if ( empty( $pid ) || is_null( $balance ) )
				die( '3' );
				
			update_post_meta( $pid, 'coupon_amount', $balance );
				
			die( 'ok' );
			
		}
		
		function init_gc_integration() {

			require_once( dirname( __FILE__ ) . '/classes/class-admin-settings.php' );
			
			if ( class_exists( 'IgniteWoo_GC_Admin' ) )
				$this->admin = new IgniteWoo_GC_Admin();
			
			require_once( dirname( __FILE__ ) . '/classes/class-gc-reports.php' );
			
			if ( class_exists( 'IgniteWoo_Event_Reports' ) ) 
				$this->reports = new IgniteWoo_Event_Reports();

			
		}


		// Make sure the site is running WooCommerce 2.0 or later, check for necessary upgrades
		function software_tests() { 
			global $woocommerce, $user_ID;

			if ( !$woocommerce ) 
				return;
				
			if ( !is_admin() ) 
				return;
				
			if ( !current_user_can( 'administrator' ) && !current_user_can( 'shop_manager' ) )
				return;
				
			if ( '' == get_option( 'ign_gc_version' ) || ( version_compare( get_option( 'ign_gc_version' ), $this->version ) < 0 ) ) {

				update_option( '_ign_gc_needs_update', 1 ); // puts up an admin notice for upgrading
				
				$vstring = str_replace( '.' , '_', $this->version );
				
				delete_user_meta( $user_ID, 'ign_gc_admins_notified_' . $vstring ); // puts up an admin notice about template updates
			}

			if ( ( file_exists( get_stylesheet_directory( 'templates/checkout-form.php' ) ) || file_exists( get_stylesheet_directory( 'templates/my-account-certs.php' ) ) || file_exists( get_stylesheet_directory( 'templates/voucher_email.php' ) ) ) && ( current_user_can( 'administrator' ) || current_user_can( 'shop_manager' ) ) ) {

				$vstring = str_replace( '.' , '_', $this->version );
				
				if ( '' == get_user_meta( $user_ID, 'ign_gc_admins_notified_' . $vstring, true ) ) { 
	
					wp_enqueue_style( 'woocommerce-activation', $woocommerce->plugin_url() . '/assets/css/activation.css' );
					
					add_action( 'admin_notices', array( &$this, 'updater_templates' ) );
					
				}
			
			}

			if ( version_compare( $woocommerce->version, '2.0' ) < 0 )
				add_action( 'admin_notices', array( &$this, 'version_nag' ) );
				
			if ( 1 == get_option( '_ign_gc_needs_update' ) ) {

				wp_enqueue_style( 'woocommerce-activation', $woocommerce->plugin_url() . '/assets/css/activation.css' );
				
				add_action( 'admin_notices', array( &$this, 'updater_notices' ) );
			}		

		}


		function maybe_run_upgrader() { 
			global $user_ID, $woocommerce;
			
			if ( !is_admin() || ( !current_user_can( 'administrator' ) && !current_user_can( 'shop_manager' ) ) )
				return; 
				
			$vstring = str_replace( '.' , '_', $this->version );
			
			if ( !empty( $_GET['ign_certs_remove_nag'] ) )
				update_user_meta( $user_ID, 'ign_gc_admins_notified_' . $vstring, 1 );
				
			if ( empty( $_GET['do_update_ign_certs'] ) )
				return;

			require_once( dirname( __FILE__ ) . '/classes/class-ign-gc-upgrader.php' );
			
			$upgrader = new IGN_GC_Upgrader(); 
			
			$upgrade_func_slug = 'do_' . $vstring;

			if ( method_exists( $upgrader, $upgrade_func_slug ) ) {
				
				if ( $upgrader->do_upgrade() ) {
				
					wp_enqueue_style( 'woocommerce-activation', $woocommerce->plugin_url() . '/assets/css/activation.css' );
				
					add_action( 'admin_notices', array( &$this, 'updater_complete' ) );
				
					update_option( 'ign_gc_version', $this->version );
			
					delete_option( '_ign_gc_needs_update' );
				}
				
			} else { 
			
				wp_enqueue_style( 'woocommerce-activation', $woocommerce->plugin_url() . '/assets/css/activation.css' );
				
				add_action( 'admin_notices', array( &$this, 'updater_complete' ) );
			
				update_option( 'ign_gc_version', $this->version );
		
				delete_option( '_ign_gc_needs_update' );
			
			}
		}
		
		
		function pricer_init() { 

			require_once( dirname( __FILE__ ) . '/classes/class-pricer.php' );
		
			$this->pricer = new ign_gc_pricer();
			
			
			// Update cart when a gift certificate or store credit is being used
			if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '>=' ) )
				add_action( 'woocommerce_after_calculate_totals', array( &$this, 'adjust_cart_total' ), 1, 1 );
			else 
				add_action( 'woocommerce_calculate_totals', array( &$this, 'adjust_cart_total' ) );

			
		}
		
		
		// Display notification when version requirement is not met
		function version_nag() { 
			global $woocommerce;

			echo '<div style="background-color:#cf0000;color:#fff;font-weight:bold;font-size:16px;margin: -1px 15px 0 5px;padding:5px 10px">';

			_e( 'The WooCommerce Gift Certificates Pro plugin requires WooCommerce 2.0 or newer to work correctly. You\'re using version', 'ignitewoo_gift_certs' );

			echo ' ' . $woocommerce->version; 

			echo '</div>';

		}

		function register_taxonomy() {

			if ( current_user_can( 'manage_woocommerce' ) ) 
				$show_in_menu = 'woocommerce'; 
			else 
				$show_in_menu = true;

			register_post_type( 'ign_voucher',
				array(
					'labels' => array(
							'name'               => __( 'Voucher Templates', 'ignitewoo_gift_certs' ),
							'singular_name'      => __( 'Voucher', 'ignitewoo_gift_certs' ),
							'menu_name'          => _x( 'Voucher Templates', 'Admin menu name', 'ignitewoo_gift_certs' ),
							'add_new'            => __( 'Add Voucher Template', 'ignitewoo_gift_certs' ),
							'add_new_item'       => __( 'Add New Voucher Template', 'ignitewoo_gift_certs' ),
							'edit'               => __( 'Edit', 'ignitewoo_gift_certs' ),
							'edit_item'          => __( 'Edit Voucher Template', 'ignitewoo_gift_certs' ),
							'new_item'           => __( 'New Voucher Template', 'ignitewoo_gift_certs' ),
							'view'               => __( 'View Voucher Templates', 'ignitewoo_gift_certs' ),
							'view_item'          => __( 'View Voucher Template', 'ignitewoo_gift_certs' ),
							'search_items'       => __( 'Search Voucher Templates', 'ignitewoo_gift_certs' ),
							'not_found'          => __( 'No Vouchers found', 'ignitewoo_gift_certs' ),
							'not_found_in_trash' => __( 'No Vouchers found in trash', 'ignitewoo_gift_certs' ),
						),
					'description'     => __( 'This is where you can add new voucher templates.', 'ignitewoo_gift_certs' ),
					'public'          => true,
					'show_ui'         => true,
					'capability_type' => 'post',
					'publicly_queryable'  => true,
					'exclude_from_search' => true,
					'show_in_menu'        => $show_in_menu,
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => array( 'title' ),
					'show_in_nav_menus'   => false,
				)
			);
			
			require_once( dirname( __FILE__ ) . '/classes/class-vouchers.php' );
		}
		
		// For image popup on the checkout page
		function wp() { 
			global $woocommerce;
			
			if ( !is_checkout() )
				return;
				
			$suffix = '';
				
			wp_enqueue_script( 'prettyPhoto', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.5', true );
			
			wp_enqueue_script( 'prettyPhoto-init', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array( 'jquery' ), $woocommerce->version, true );
			
			wp_enqueue_style( 'woocommerce_prettyPhoto_css', $woocommerce->plugin_url() . '/assets/css/prettyPhoto.css' );
			
		}
		
		function admin_init() { 
		
			require_once( dirname( __FILE__ ) . '/classes/post-types/writepanels/writepanels-init.php' );

			require_once( dirname( __FILE__ ) . '/classes/post-types/ign_vouchers.php' );


		}
		
		
		function admin_enqueue_scripts() { 

			require_once( dirname( __FILE__ ) . '/classes/class-admin-init.php' );
		}
		
		// Add the new coupon type
		function add_coupon_code_type( $types ) {

			$types['ign_store_credit'] = __('Gift Certificate / Store Credit', 'ignitewoo_gift_certs' );

			return $types;
		}


		function add_error( $msg = '' ) { 
			if ( function_exists( 'wc_add_notice' ) ) {
				wc_add_notice( $msg, 'error' );
			} else { 
				$this->add_error();
			}
		
		}
		
		// Display a form to collect info about the voucher recipient
		function recipient_detail_form() {
			global $woocommerce;

			if ( !empty( $this->admin_settings['email_vouchers'] ) && 'no' == $this->admin_settings['email_vouchers'] )
				return;
				
			$maybe_show_form = false;
			
			foreach ( $woocommerce->cart->cart_contents as $product ) {

				$gc_enabled = get_post_meta( $product['product_id'], 'ignite_gift_enabled', true );

				if ( $gc_enabled )
					$maybe_show_form = true;

			}

			if ( true == $maybe_show_form ) {
			
				$template = locate_template( array( 'templates/checkout-form.php' ), false, false );

				ob_start();

				if ( '' != $template ) 
					require( $template );
				else 
					require( dirname( __FILE__ ) . '/templates/checkout-form.php' );

				$form_body = ob_get_contents();

				ob_end_clean();
				
				echo $form_body;
				
			}
			
			
			/*
			if ( $maybe_show_form == true ) {

				?>

				</div>
				</div>

				<div class="gift-certificate">
					<div class="receiver-form">

				<h3><?php _e( "Gift Certificate / Store Credit / Coupon receiver's details", 'ignitewoo_gift_certs' ) ?></h3>
				<p><?php _e( 'Leave the recipient name and email address blank to receive the voucher yourself. Or, enter details below to send the voucher(s) to someone else.', 'ignitewoo_gift_certs' ) ?></p>


				<p id="order_comments_field" class="form-row notes">
					<label class="" for="ign_receiver_name"><?php _e( 'Recipient Name', 'ignitewoo_gift_certs' ) ?></label>
					<input id="ign_receiver_name" type="text" name="ign_receiver_name" value="" />
				</p>

				<p id="order_comments_field" class="form-row notes">
					<label class="" for="ign_receiver_email"><?php _e( 'Recipient Email Address', 'ignitewoo_gift_certs' ) ?></label>
					<input id="ign_receiver_email" type="text" name="ign_receiver_email" value="" />
				</p>

				<p id="order_comments_field" class="form-row notes">
					<label class="" for="ign_receiver_message"><?php _e( 'Message to Recipient', 'ignitewoo_gift_certs' ) ?></label>
					<textarea rows="2" cols="5" id="ign_receiver_message" class="input-text" name="ign_receiver_message"></textarea>
				</p>

				<?php

			}
			*/
		}


		// Verify gift credit form details
		function verify_recipient_details() {
			global $woocommerce;

			if ( empty( $_POST['total_gift_cert_count'] ) || absint( $_POST['total_gift_cert_count'] ) <= 0 )
				return;
			
			$total_certs = absint( $_POST['total_gift_cert_count'] );

			for ( $x = 0; $x < $total_certs; $x++ ) {  
			
				$name_present = false;
				
				// check for valid email
				if ( !empty( $_POST['ign_receiver_email'][ $x ] ) && !is_email( $_POST['ign_receiver_email'][ $x ] ) ) {

					$this->add_error( __( "Error: The email address for recipient" . ' ' . ($x+1) . ' ' . "is invalid.", 'ignitewoo_gift_certs' ) );

				}
				
				// check for name if email is set
				if ( !empty( $_POST['ign_receiver_email'][ $x ] ) ) { 

					if ( empty( $_POST['ign_receiver_name'][ $x ] ) || '' == trim( strip_tags( html_entity_decode( $_POST['ign_receiver_name'][ $x ] ) ) ) )
					{

						$this->add_error( __( "Error: You must enter the recipient" . ' ' . ($x+1) . ' ' . "name or delete the recipient's email address.", 'ignitewoo_gift_certs' ) );

					} else 
						$name_present = true;

				} 

				// check to ensure email is set if name is set
				if ( ( $name_present || !empty( $_POST['ign_receiver_name'][ $x ] ) ) && empty( $_POST['ign_receiver_email'][ $x ] ) ) {

					$this->add_error( sprintf( __( "Error: You must enter the recipient %s email address, or delete the recipient's name.", 'ignitewoo_gift_certs' ), ($x+1) ) );

				}
			
			}

		}


		// Maybe add the voucher receiver's details to the order meta
		function maybe_save_recipient_info( $order_id ) {

			if ( empty( $_POST['total_gift_cert_count'] ) || absint( $_POST['total_gift_cert_count'] ) <= 0 )
				return;
	
			$total_certs = count( $_POST['ign_receiver_email'] );
			
			$receiver = array();
			
			$order = new WC_Order( $order_id );
			
			$_POST['ign_receiver_set'] = array();

			foreach( $order->get_items() as $item_key => $item ) { 

				if ( !get_post_meta( $item['product_id'], 'ignite_gift_enabled', true ) )
					continue;

				$override_product_price = get_post_meta( $item['product_id'], 'ignite_gc_sold_as_voucher', true );
				
				// In case the admin checks the box to override price AND checks the box to let the buyer set the price.
				// These 2 settings are mutually exclusive, therefore "buyer sets price" takes precendence.
				
				$buyer_sets_price = get_post_meta( $item['product_id'], 'ignite_buyer_sets_price', true );
				
				if ( '1' == $buyer_sets_price )
					$override_product_price = false;

				for ( $q = 0; $q < $item['qty']; $q++ ) {

					for ( $i = 0; $i < $total_certs; $i++ ) { 

						// Don't save anything unless the buyer specified a recipient
						//if ( $_POST['billing_email'] == $_POST['ign_receiver_email'][$i] )
						//	continue;

						// have we processed this receiver yet? 
						if ( !empty( $_POST['ign_receiver_set'][$i] ) && '1' == $_POST['ign_receiver_set'][$i] )
							continue;

						$item_cert_amt = ( $item['line_total'] / $item['qty'] );

						$email = $name = $msg = $amt = $style = '';
						
						// Does the item amount match the posted amount for the receiver? 
						// If so, save some item meta with the item
						// Also check if overrice is set, if so check against coupon amount
						if ( !$override_product_price && $item_cert_amt != $_POST['ign_receiver_index'][$i] ) {
							continue; 							
						} 
						
						// If price is being overridden to issue a cert with an amount different than the product price
						// then the amount must be what is set in the coupon amount within the product settings
						if ( $override_product_price ) { 
						
							$coupon_amount = get_post_meta( $item['product_id'], 'coupon_amount', true );
							
							if ( $coupon_amount != $_POST['ign_receiver_index'][$i] )
								continue;
						}
						
							
						// Assign it to the buyer
						if ( !empty( $_POST['ign_receiver_email'][$i] ) )
							$email = trim( $_POST['ign_receiver_email'][$i] );
						else 
							$email = $order->billing_email;

						// Assign it to the buyer
						if ( !empty( $_POST['ign_receiver_name'][$i] ) ) 
							$name = trim( strip_tags( html_entity_decode( $_POST['ign_receiver_name'][$i] ) ) );
						else 
							$name = $order->billing_first_name . ' ' . $order->billing_last_name;
						
						if ( !empty( $_POST['ign_receiver_message'][$i] ) )
							$msg = trim( strip_tags( html_entity_decode( $_POST['ign_receiver_message'][$i] ) ) );
						
						// No style selected? Then default to the first available style
						if ( !empty( $_POST['ign_receiver_style'][$i] ) )
							$style = trim( strip_tags( absint( $_POST['ign_receiver_style'][$i] ) ) );
						else { 
						
							$styles = get_post_meta( $item['product_id'], 'voucher_styles', true );
							
							if ( !empty( $styles ) )
								$style = $styles[0];
								
						}
							
						if ( !empty( $_POST['ign_receiver_index'][$i] ) )
							$amt = round( floatval( $_POST['ign_receiver_index'][$i] ), 2 );
						
						if ( version_compare( WOOCOMMERCE_VERSION, '2.4', '>=' ) )
							$r = wc_get_order_item_meta( $item_key, '_voucher_recievers', true );
						else 
							$r = woocommerce_get_order_item_meta( $item_key, '_voucher_recievers', true );

						if ( empty( $r ) )
							$r = array();
							
						$r[] = array( 'email' => $email, 'name' => $name, 'msg' => $msg, 'amt' => $amt, 'style' => $style );

						if ( version_compare( WOOCOMMERCE_VERSION, '2.4', '>=' ) )
							wc_update_order_item_meta( $item_key, '_voucher_recievers', $r );
						else 
							woocommerce_update_order_item_meta( $item_key, '_voucher_recievers', $r );

						$_POST['ign_receiver_set'][$i] = '1';
						
						break;
					}
				}

			}

			
		}


		// Maybe show vouchers that are included with the product purchase
		function show_product_vouchers() {
			global $post, $woocommerce, $product; 

			$coupon = get_post_meta( $post->ID );

			if ( !$coupon || !is_array( $coupon ) || empty( $coupon['ignite_gift_enabled'][0] ) ) 
				return;
				
			if ( !empty( $coupon['display_included_vouchers'][0] ) && 'no' == $coupon['display_included_vouchers'][0] )
				return;

			if ( get_post_meta( $post->ID, 'ignite_buyer_sets_price', true ) )
				return;
			
			echo '<div class="clear"></div>';

			echo '<div class="product_vouchers">';

			echo '<p>' . __( "This product comes with the following vouchers:", 'ignitewoo_gift_certs' );

			if ( 'ign_store_credit' == $coupon['discount_type'][0] ) {

				$amount = $coupon['coupon_amount'][0];
				
				/* DO NOT DO THIS, ADMIN SHOULD SET THE PRODUCT AS NON-TAXABLE INSTEAD
				if ( $product->is_taxable() ) {

					// is tax displayed including or excluding?
					$amount = $coupon['coupon_amount'][0];
					
					$amount = $product->get_price_excluding_tax( 1, $amount );
				
				} else { 
				
					$amount = $coupon['coupon_amount'][0];
				
				}
				*/
				$value = __( 'Gift Certificate / Store credit of ', 'ignitewoo_gift_certs' ) . woocommerce_price( $amount );
			}

			else if ( 'percent_product' == $coupon['discount_type'][0] )						
				$value = $coupon['coupon_amount'][0] . '%' . __( ' discount on a product.', 'ignitewoo_gift_certs' );
					
			else if ( 'percent' == $coupon['discount_type'][0] )
				$value = $coupon['coupon_amount'][0] . '%' . __( ' discount on your entire purchase.', 'ignitewoo_gift_certs' );

			else if ( 'fixed_cart' == $coupon['discount_type'][0] )
				$value = woocommerce_price( $coupon['coupon_amount'][0] ) . __( ' discount on your entire purchase.', 'ignitewoo_gift_certs' );

			else if ( 'fixed_product' == $coupon['discount_type'][0] )
				$value = woocommerce_price( $coupon['coupon_amount'][0] ) . __( ' discount on a product.', 'ignitewoo_gift_certs' );

			echo '<ul><li>' . $value ; 

			if ( '' != $coupon['expiry_date'][0] ) {

				echo ' &mdash; <span class="voucher_expires">';

				_e( 'Voucher expires:', 'ignitewoo_gift_certs' );

				echo ' ' . $coupon['expiry_date'][0];

				echo '<span>';
			}

			echo '</li></ul></div>';

		}

		
		/* Hook call for these are deprecated by WC devs
		function get_discount_amount( $discount = null, $discounting_amount = null, $cart_item = null, $single = false, $_coupon = null ) { 
			global $woocommerce;

			if (  'ign_store_credit' != $_coupon->type )
				return $discount;

			$discount_percent = 0;

			//if ( WC()->cart->subtotal_ex_tax ) {
					$discount_percent = ( $cart_item['data']->get_price_excluding_tax() * $cart_item['quantity'] ) / WC()->cart->subtotal_ex_tax + WC()->cart->shipping_total + WC()->cart->shipping_tax_total;
			//}

			$discount = min( ( $_coupon->coupon_amount * $discount_percent ) / $cart_item['quantity'], $discounting_amount );
			
			return $discount;
		
		}

		function is_valid_for_cart( $bool, $_coupon ) {

			if (  'ign_store_credit' != $_coupon->type )
				return $bool;
				
			return true;
		
		}
		*/
		
		
		// Check if a gift certificate / store credit is being used, and if so apply it to the cart totals
		function adjust_cart_total() {
			global $woocommerce;

			$woocommerce->cart->credit_used = array();
		
			if ( !$woocommerce->cart->applied_coupons )
				return;

			$temp_total = 0;

			foreach ( $woocommerce->cart->applied_coupons as $cc ) {
				
				$coupon = new WC_Coupon( $cc );
				
				if ( !$coupon->is_valid() ) 
					continue;

				if (  'ign_store_credit' != $coupon->type )
					continue; 

				$total = $woocommerce->cart->cart_contents_total + $woocommerce->cart->tax_total + $woocommerce->cart->shipping_total + $woocommerce->cart->shipping_tax_total + $woocommerce->cart->fee_total;

				if ( empty( $temp_total ) )
					$temp_total = $total;

				if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<=' ) ) {

					// If coupon amount is more than the order total then 
					// adjust the coupon amount to be the order total
					if ( $coupon->amount > $temp_total ) {

						$coupon->amount = $coupon->amount - $temp_total;

						$temp_total = $total;
		
						$woocommerce->cart->discount_total = $woocommerce->cart->discount_total + $temp_total;

						$woocommerce->cart->credit_used[ $cc ] = $temp_total;
					
					// Otherwise subtract the remaining balancing of the coupon from the order total
					} else {
			
						$temp_total = $temp_total - $coupon->amount;

						$woocommerce->cart->discount_total = $woocommerce->cart->discount_total + $coupon->amount;

						$woocommerce->cart->credit_used[ $cc ] = $coupon->amount;
					
					} 
					
				} else { 
					
					// If coupon amount is more than the order total then 
					// adjust the coupon amount to be the order total
					// and forcefully set the total to zero
					if ( $coupon->amount > $temp_total ) {

						$coupon->amount = $coupon->amount - $temp_total;

						//$temp_total = $total;
		
						$woocommerce->cart->discount_cart = $woocommerce->cart->discount_cart + $temp_total;
						
						$woocommerce->cart->coupon_discount_amounts[ $coupon->code ] = $temp_total;

						$woocommerce->cart->credit_used[ $cc ] = $temp_total;
						
						$woocommerce->cart->total = 0;
						
					// Otherwise subtract the remaining balancing of the coupon from the order total
					// and adjust the order total to be correct
					} else {

						$temp_total = $temp_total - $coupon->amount;

						$woocommerce->cart->discount_cart = $woocommerce->cart->discount_cart + $coupon->amount;
						//$woocommerce->cart->discount_cart = $woocommerce->cart->discount_cart + $coupon->amount;
						
						$woocommerce->cart->coupon_discount_amounts[ $coupon->code ] = $coupon->amount;

						$woocommerce->cart->credit_used[ $cc ] = $coupon->amount;

						$woocommerce->cart->total = $temp_total;

					} 
					
				}

				$woocommerce->cart->credit_used[ $cc ] = round( $woocommerce->cart->credit_used[ $cc ], 2 );
			}

		}
		

		// Maybe update gift certificate / store credit balance and/or maybe delete coupon codes
		function maybe_update_or_remove( $order_id ) {
			global $woocommerce;

			if ( !$woocommerce->cart->applied_coupons )
				return;
//var_dump( $woocommerce->cart->credit_used ); die;
			foreach( $woocommerce->cart->applied_coupons as $cc ) {

				$coupon = new WC_Coupon( $cc );

				if ( !$coupon )
					continue;

				$del_cc = get_post_meta( $coupon->id, 'ignite_delete_coupon', true );

				$del_gc = get_post_meta( $coupon->id, 'ignite_delete_gift_cert', true );

				if ( 'ign_store_credit' == $coupon->type ) {

					$credit_remaining = max( 0, ( $coupon->amount - $woocommerce->cart->credit_used[ $cc ] ) );
					
					$credit_remaining = round( $credit_remaining, 2 );
					
					if ( $credit_remaining <= 0 && 1 == $del_gc )
						wp_delete_post( $coupon->id );
					else
						update_post_meta( $coupon->id, 'coupon_amount', $credit_remaining );


					$codes_used = get_post_meta( $order_id, 'ign_store_credit_codes', true );

					if ( !is_array( $codes_used ) )
						$codes_used = array();

					$codes_used[] = array( 'code' => $cc, 'amount' => round( $woocommerce->cart->credit_used[ $cc ], 2 ) );
					
					update_post_meta( $order_id, 'ign_store_credit_codes', $codes_used );

					
				} else if ( 1 == $del_cc ) { 

					wp_delete_post( $coupon->id );

				}

			}


		}
		

		// Return validity of the code used
		function is_code_valid( $valid, $coupon ) {
			global $woocommerce;

			if ( !$valid ) 
				return $valid;

			if ( 'ign_store_credit' != $coupon->type )
				return $valid; 

			if ( $coupon->amount <= 0 ) {

				$this->add_error( __('There is no balance remaining for the code you entered.', 'ignitewoo_gift_certs' ) );

				return false;
			}

			
			
			return $valid;
		}

		// $the_coupon is the one being applied, $coupon is a coupon already in the cart
		// This runs when a coupon is in the cart and someone tries to apply a gift cert code.
		// This ensures that the gift cert can be applied even though another coupon with individual use 
		// set is already in the cart. But this only allows the gift cert if the it's not set for individual_use
		function maybe_allow_individual_use_coupons( $allow, $the_coupon = '', $coupon = '', $applied_coupons = array() ) { 

			if ( 'ign_store_credit' == $the_coupon->type && 'yes' !== $the_coupon->individual_use )
				return true;
				
			return $allow;
		}
		
		// This runs when a coupon is in the cart and another coupon is being applied after it.
		// This ensures that gift cert codes do not get removed, unless they're set for individual use.
		function maybe_apply_individual_use_coupon( $x = array(), $the_coupon = '', $applied_coupons ) { 
			
			if ( empty( $applied_coupons ) ) 
				return $applied_coupons;
			
			foreach( $applied_coupons as $key => $code ) { 
				$c = new WC_Coupon( $code );
				if ( 'ign_store_credit' !== $c->type && 'yes' !== $c->individual_use ) 
					unset( $applied_coupons[ $key ] );
				else if ( 'ign_store_credit' == $c->type && 'yes' == $c->individual_use )
					unset( $applied_coupons[ $key ] );
			}	
		
			return $applied_coupons;
		}
		
		function my_account() { 
			global $user_ID;
			
			if ( empty( $user_ID ) )
				return;
				
			$customer_vouchers = $this->get_all_customer_vouchers( $user_ID );
		
			$template = locate_template( array( 'templates/my-account-certs.php' ), false, false );

			if ( '' != $template ) 
				require( $template );
			else 
				require( dirname( __FILE__ ) . '/templates/my-account-certs.php' );
				
				
		}
		
		// Generate a voucher code 
		function create_voucher( $order_id, $prefix, $meta, $email_addr, $restrict_to_buyer, $coupon_amount = '', $voucher_template_id = '', $product_id ) {

			$voucher_code = '';
			
			// Maybe use a custom GC code
			if ( 'yes' == get_post_meta( $product_id, 'use_custom_gc_codes', true ) ) { 
			
				$per_product_codes = false;
				
				$available_codes = get_post_meta( $product_id, 'custom_gc_codes', true );
								
				if ( empty( $available_codes ) )
					$available_codes = $this->admin_settings['custom_codes'];
				else
					$per_product_codes = true;
				
				if ( !empty( $available_codes ) )
					$available_codes = explode( "\n", $available_codes );

				// get rid of empty lines
				$available_codes = array_map( 'trim', $available_codes );
				
				// Less than 10 custom codes remain, notify the admin.
				// This will happen each time a new voucher is created until the product setting option
				// is turned off or the admin inserts more codes
				if ( count( $available_codes ) <= 10 ) { 
				
					$sitename = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
				
					if ( count( $available_codes ) > 0 ) 
						$message = "\n" . sprintf( __( 'You only have %s custom voucher codes remaining in your store!', 'ignitewoo_gift_certs' ), count( $available_codes ) );
					else 
						$message = "\n" . sprintf( __( 'You have NO CUSTOM VOUCHER CODES REMAINING in your store! The store is now generating its own unique codes.', 'ignitewoo_gift_certs' ), count( $available_codes ) );
					
					$message .= "\n" . sprintf( __( 'Login to "%s" and add more custom gift certificate / voucher codes as soon as possible.', 'ignitewoo_gift_certs' ), $sitename ) . "\n";
					
					$store_admin = get_option( 'woocommerce_new_order_email_recipient', false );
				
					if ( !$store_admin )
						$store_admin = get_option( 'admin_email', false );
						
					if ( !empty( $store_admin ) ) {
						
						$subject = __( '*** Low Voucher Code Notice ***', 'ignitewoo_gift_certs' );
						
						wp_mail( $store_admin, $subject, $message );
					}
				
				}
				
				
				if ( count( $available_codes ) >= 1 ) { 
				
					$voucher_code = $available_codes[0];
					
					unset( $available_codes[0] );
					
					if ( !empty( $available_codes ) )
						$available_codes = implode( "\n", (array)$available_codes );
					else
						$available_codes = '';
						
					if ( !$per_product_codes ) {
						
						$this->admin_settings['custom_codes'] = $available_codes;
						
						update_option( 'woocommerce_woocommerce_gift_certificates_settings', $this->admin_settings );
						
					} else {
	
						update_post_meta( $product_id, 'custom_gc_codes', $available_codes );
					
					}
				}
			
			}
			
			// Create a semi-random unique ID code
			if ( empty( $voucher_code ) )
				$voucher_code = strtolower( uniqid( $prefix ) );

			$args = array(
					'post_title' 	=> $voucher_code,
					'post_content' 	=> '',
					'post_status' 	=> 'publish',
					'post_author' 	=> 1,
					'post_type'	=> 'shop_coupon'
			);
				
			$pid = wp_insert_post( $args );

			if ( !$pid || is_wp_error( $pid ) ) 
				return false;

			// Std WooCom meta
			if ( empty( $coupon_amount ) )
				update_post_meta( $pid, 'coupon_amount', $meta['coupon_amount'][0] );
			else
				update_post_meta( $pid, 'coupon_amount', $coupon_amount );

			if ( empty( $voucher_template_id ) )
				update_post_meta( $pid, 'voucher_template_id', $voucher_template_id );
			else
				update_post_meta( $pid, 'voucher_template_id', $voucher_template_id );
				
			update_post_meta( $pid, 'discount_type', $meta['discount_type'][0] );

			update_post_meta( $pid, 'individual_use', $meta['individual_use'][0] );

			update_post_meta( $pid, 'apply_before_tax', $meta['apply_before_tax'][0] );

			update_post_meta( $pid, 'free_shipping', $meta['free_shipping'][0] );

			update_post_meta( $pid, 'product_ids', $meta['product_ids'][0] );

			update_post_meta( $pid, 'exclude_product_ids', $meta['exclude_product_ids'][0] );
			
			update_post_meta( $pid, 'product_categories', maybe_unserialize( $meta['product_categories'][0] ) );

			update_post_meta( $pid, 'exclude_product_categories', maybe_unserialize( $meta['exclude_product_categories'][0] ) );

			update_post_meta( $pid, 'exclude_sale_items', $meta['exclude_sale_items'][0] );
			
			update_post_meta( $pid, 'minimum_amount', $meta['minimum_amount'][0] );
			
			update_post_meta( $pid, 'usage_limit', $meta['usage_limit'][0] );

			if ( isset( $meta['_expiration_days'][0] ) && !empty( $meta['_expiration_days'][0] ) && absint( $meta['_expiration_days'][0] ) > 0 ) { 
			
				$days = absint( $meta['_expiration_days'][0] );
				
				$exp = strtotime( '+' . $days . ' days', current_time( 'timestamp', false ) );
				
				$expiry_date = date( 'Y-m-d', $exp );

			} else 
			
				$expiry_date = $meta['expiry_date'][0];
			
			update_post_meta( $pid, 'expiry_date', $expiry_date );

			if ( $restrict_to_buyer )
				update_post_meta( $pid, 'customer_email', array( $email_addr ) );


			// Plugin's extra meta
			update_post_meta( $pid, 'associated_order_id', $order_id );

			if ( !empty(  $meta['ignite_delete_coupon'][0] ) )
				update_post_meta( $pid, 'ignite_delete_coupon', $meta['ignite_delete_coupon'][0] );

			if ( !empty( $meta['ignite_delete_gift_cert'][0] ) )
				update_post_meta( $pid, 'ignite_delete_gift_cert', $meta['ignite_delete_gift_cert'][0] );
				
			return $voucher_code;
				
		}


		// Get existing "coupon" items based on type and order ID 
		function get_existing_vouchers( $type, $order_id ) { 

			$args = array(
					'post_type' => 'shop_coupon',
					'post_status' => $type,
					'posts_per_page' => -1,
					'meta_query' => array(
								array(
									'key' 	=> 'associated_order_id',
									'value' 	=> $order_id,
									'compare'	=> '='
								)
						)
				    );

			$existing_coupons = get_posts( $args );

			return $existing_coupons;

		}

		
		function get_all_customer_vouchers( $customer_id = null ) { 
		
			if ( empty( $customer_id ) )
				return array();
				
			if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '>=' ) ) {
				if ( empty( $this->admin_settings ) )
					$this->admin_settings = get_option( 'woocommerce_woocommerce_gift_certificates_settings' );
					
				if ( 'processing' == $this->admin_settings['order_status_trigger'] )
					$statuses = array( 'wc-processing', 'wc-completed' );
				else 
					$statuses = 'wc-processing';
			} else { 
				$statuses = 'publish';
			}

			$args = array(
				'post_type' => 'shop_order',
				'post_status' => $statuses,
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
						'key' 	=> '_customer_user',
						'value' 	=> $customer_id,
						'compare'	=> '='
					)
				)
			);
			
			$existing_orders = get_posts( $args );
			
			if ( empty( $existing_orders ) || is_wp_error( $existing_orders ) )
				return array();
				
			$all_coupons = array();
			
			foreach( $existing_orders as $o ) { 
			
				$coupons = $this->get_existing_vouchers( array( 'publish' ), $o->ID );
				
				if ( !empty( $coupons ) && !is_wp_error( $coupons ) )
					$all_coupons = array_merge( $all_coupons, $coupons );
			
			}
		
			return $all_coupons;
		
		}

		// Deduct amount from voucher when order status is changed, 
		function maybe_charge_voucher( $order_id ) { 
			global $wpdb;

			$refunded = get_post_meta( $order_id, 'coupons_refunded', true );

			// Not refunded, then don't re-debit the voucher
			if ( !$refunded ) 
				return;

			$codes_used = get_post_meta( $order_id, 'ign_store_credit_codes', true );

			if ( !$codes_used || !is_array( $codes_used ) )
				return;

			foreach( $codes_used as $key => $vals ) { 

				$sql = 'select ID from ' . $wpdb->posts . ' where post_type = "shop_coupon" and post_title = "' . $vals['code'] . '"';

				$coupon_id = $wpdb->get_var( $wpdb->prepare( $sql ) );

				if ( $coupon_id ) { 

					$balance = get_post_meta( $coupon_id, 'coupon_amount', true );

					if ( !$balance ) 
						$balance = 0;

					$balance = $balance - $vals['amount'];

					update_post_meta( $coupon_id, 'coupon_amount', $balance );

					unset( $refunded[ $coupon_id ] );
					
					update_post_meta( $order_id, 'coupons_refunded', $refunded );

				}

			}

		}


		// Handle refund for the voucher
		function process_voucher_refund( $order_id ) {
			global $wpdb;

			// get amount used, add it back to the corresponding voucher post
			$codes_used = get_post_meta( $order_id, 'ign_store_credit_codes', true );
//var_dump( $codes_used ); die;
			if ( !$codes_used || !is_array( $codes_used ) || !count( $codes_used ) > 0 )
				return;

			$refunded = get_post_meta( $order_id, 'coupons_refunded', true );
			
			if ( empty( $refunded ) )
				$refunded = array();
				
			foreach( $codes_used as $key => $vals ) { 

				$sql = 'select ID from ' . $wpdb->posts . ' where post_type = "shop_coupon" and post_title = "' . $vals['code'] . '"';

				$coupon_id = $wpdb->get_var( $wpdb->prepare( $sql ) );

				if ( $coupon_id ) { 

					//$refunded = get_post_meta( $order_id, 'coupons_refunded', true );

					// Not refunded yet? Then do refund back to GC code
					if ( !$refunded[ $coupon_id ] ) { 

						$coupon_balance = get_post_meta( $coupon_id, 'coupon_amount', true ); 

						if ( !$coupon_balance ) 
							$coupon_balance = 0;

						$coupon_balance = $coupon_balance + $vals['amount']; 

						$refunded[ $coupon_id ] = 1;
						
						update_post_meta( $order_id, 'coupons_refunded', $refunded );

						update_post_meta( $coupon_id, 'coupon_amount', $coupon_balance );

						$wpdb->update( $wpdb->posts, array( 'post_status' => 'publish' ), array( 'ID' => $coupon_id ) );

					}

				}

			}

		}

		// Add or update a voucher
		function adjust_voucher( $coupon_prefix, $mode, $msg_details, $order_id, $order, $product_id, $variation_id, $item, $item_id, $product, $total_needed, $total_to_create ) {
			global $wpdb;

			// $total_needed is the total number of vouchers for the order 
			// $total_to_create is the number to create for the item in the order 

			//if ( !class_exists( 'WC_Coupon' ) )
			//	require_once( WP_PLUGIN_DIR . '/woocommerce/classes/classes/class-wc-coupon.php' );

			$coupon_title = '';
			
			$msg_details['voucher_to_email'] = trim( $msg_details['voucher_to_email'] );

			$restrict_to_buyer = get_post_meta( $product_id, 'ignite_restrict_to_buyer', true );
			
			$coupon = get_post_meta( $product_id );

			// gift cert and store credit codes
			if ( 'ign_store_credit' == $coupon['discount_type'][0] && 'add_voucher' == $mode ) {

				$existing_coupons = $this->get_existing_vouchers( array( 'draft', 'publish' ), $order_id );

				if ( !is_wp_error( $existing_coupons ) && count( $existing_coupons ) > 0 ) { 

					foreach( $existing_coupons as $key => $ec ) { 

						$wpdb->update( $wpdb->posts, array( 'post_status' => 'publish' ), array( 'ID' => $ec->ID ) );

						//break;

					}

				} 

				if ( count( $existing_coupons ) >= $total_needed ) 
					return;

				// Did the buyer set the amount? If not, use coupon amount setting
				if ( '1' != get_post_meta( $product->id, 'ignite_buyer_sets_price', true ) ) { 
					
					// Variable products get a voucher value of regular price
					// Simple products get a voucher value of the defined coupon amount
					if ( !empty( $variation_id ) ) 
						$coupon_amount = get_post_meta( $variation_id, '_regular_price', true );
					else 
						$coupon_amount = $coupon['coupon_amount'][0];
					
				} else { 
					
					/*
					Get the amount from the order itself because that it what was paid for. 
					*/
					$inc_tax = get_post_meta( $product_id, 'ignite_include_tax', true );
					
					if ( isset( $inc_tax ) && 'yes' == $inc_tax )
						$coupon_amount = $order->get_item_total( $item, true ); // per qty price with tax
					else 
						$coupon_amount = $order->get_item_total( $item, false ); // per qty price without tax
						
					/*
					$sql = 'select meta_value from ' . $wpdb->prefix . 'woocommerce_order_itemmeta where order_item_id = ' . $item_id . ' and meta_key = "_line_subtotal"';

					$res = $wpdb->get_var( $sql );
					
					if ( empty( $res ) )
						$coupon_amount = '';
					else
						$coupon_amount = $res;
						
					$coupon_amount = round( $coupon_amount / $total_to_create, 2 );
					*/ 
				
				}
				
				/* NO DO NOT DO THIS - ADMIN SHOULD SET THE PRODUCT AS NON-TAXABLE
				// If taxing is turned on then recalc price considering tax
				// because some sites display prices including tax, and in that case the gift cert is sold
				// excluding tax so that tax can be collected on purchases at the time of checkout,
				// otherwise taxes may not be calculated correct. Example: Sell a gift cert for $10, which includes
				// .20 cents tax; user buys that and returns to the store later to spend it and puts $100 of items in
				// the cart, applies gift cert which already includes .20 cents tax, but WC will calc tax on the
				// entire cart contents without knowing that .20 cents tax is already paid.
				if ( $product->is_taxable() ) {

					// is tax displayed including or excluding?
					$amount = $coupon['coupon_amount'][0];
					
					$amount = $product->get_price_excluding_tax( 1, $amount );
				
				}
				*/
				
				$item_receivers_meta = (array)woocommerce_get_order_item_meta( $item_id, '_voucher_recievers', true );

				for ( $i = 0; $i < $total_to_create; $i++ ) { 
				
					// Set initial recipient values for email message
					$msg_details = array( 
							'voucher_from_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
							'voucher_from_email' => $order->billing_email,
							'voucher_to_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
							'voucher_to_email' => $order->billing_email,
							'voucher_message' => '',
							'voucher_template_id' => ''
					);

					// If there is a receivers list send one and continue the loop
					if ( !empty( $item_receivers_meta ) ) {

						$msg_details['voucher_message'] = isset( $item_receivers_meta[0]['msg'] ) ? $item_receivers_meta[0]['msg'] : '';
						
						$msg_details['voucher_to_name'] = isset( $item_receivers_meta[0]['name'] ) ? $item_receivers_meta[0]['name'] : '';

						$msg_details['voucher_to_email'] = isset( $item_receivers_meta[0]['email'] ) ? $item_receivers_meta[0]['email'] : '';
						
						$msg_details['voucher_template_id'] = isset( $item_receivers_meta[0]['style'] ) ? $item_receivers_meta[0]['style'] : '';
						
						// remove the receiver from the list
						unset( $item_receivers_meta[0] );

						// reindex array for zero offset so the array key index for msg, name, email work
						// if and when this code retriggers for this order item
						$item_receivers_meta = array_values( $item_receivers_meta );
					}
					
					// If the "to email" is empty due to it not being set in the receivers data then send to buyer
					if ( empty( $msg_details['voucher_to_email'] ) ) {
						$msg_details = array( 
								'voucher_from_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
								'voucher_from_email' => $order->billing_email,
								'voucher_to_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
								'voucher_to_email' => $order->billing_email,
								'voucher_message' => '',
								'voucher_template_id' => ''
						);
					}

					$coupon_title = $this->create_voucher( $order_id, $coupon_prefix, $coupon, $msg_details['voucher_to_email'], $restrict_to_buyer, $coupon_amount, $msg_details['voucher_template_id'], $product_id );

					$coupon_data = get_post_meta( $this->get_coupon_id( $coupon_title ) );
					
					// Save code for printing from the order itself
					//$msg_details['voucher_code'] = $coupon_title; 
					
					//woocommerce_update_order_item_meta( $item_id, '_voucher_recievers', $msg_details );
					
					//woocommerce_delete_order_item_meta( $item_id, '_voucher_code_' . $item_id );
					
					// Use an array so it becomes serialized, which WC automatic won't display in order details ;-)
					// woocommerce_update_order_item_meta( $item_id, '_voucher_code_' . $item_id, array( $coupon_title ) );

					woocommerce_delete_order_item_meta( $item_id, 'Voucher Code ' . ( $i + 1 ) );
					
					woocommerce_update_order_item_meta( $item_id, 'Voucher Code ' . ( $i + 1 ), $coupon_title );
					
					woocommerce_delete_order_item_meta( $item_id, 'Voucher Code ' . ( $i + 1 ) . ' Sent To' );
					
					woocommerce_update_order_item_meta( $item_id, 'Voucher Code ' . ( $i + 1 ) . ' Sent To', $msg_details['voucher_to_name'] . ' (' . $msg_details['voucher_to_email'] .')' );
					
					if ( !empty( $msg_details['voucher_template_id'] ) )
						$create_pdf = true;
					else
						$create_pdf = false;
						
					$this->send_voucher( $coupon_title, $coupon_amount, $coupon_data['discount_type'][0], $coupon_data['expiry_date'][0], $msg_details, $order_id, $item, $product_id, $create_pdf );
			
				}


			// regular coupon codes
			} else {

				if ( 'add_voucher' == $mode ) {

					// Use both statuses because this code might run twice, once for order status Processing and once for Completed - if the admin set the trigger to Processing
					$existing_coupons = $this->get_existing_vouchers( array( 'draft', 'publish' ), $order_id );

					// Check for existing unpublished coupon codes and publish one if one exists
					// otherwise create a new one, publish it, and email it

					if ( !is_wp_error( $existing_coupons) && count( $existing_coupons ) > 0 ) { 

						foreach( $existing_coupons as $key => $ec ) { 

							$wpdb->update( $wpdb->posts, array( 'post_status' => 'publish' ), array( 'ID' => $ec->ID ) );

							//break;

						}

					}

					if ( count( $existing_coupons ) == $total_needed ) 
						return;
					
					$item_receivers_meta = (array)woocommerce_get_order_item_meta( $item_id, '_voucher_recievers', true );
					
					for ( $i = 0; $i < $total_to_create; $i++ ) { 
				
						// Set initial recipient values for email message
						$msg_details = array( 
								'voucher_from_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
								'voucher_from_email' => $order->billing_email,
								'voucher_to_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
								'voucher_to_email' => $order->billing_email,
								'voucher_message' => '',
								'voucher_template_id' => ''
						);

						// If there is a receivers list send one and continue the loop
						if ( !empty( $item_receivers_meta ) ) {

							$msg_details['voucher_message'] = $item_receivers_meta[0]['msg'];
							
							$msg_details['voucher_to_name'] = $item_receivers_meta[0]['name'];

							$msg_details['voucher_to_email'] = $item_receivers_meta[0]['email'];
							
							$msg_details['voucher_template_id'] = $item_receivers_meta[0]['style'];
							
							// remove the receiver from the list
							unset( $item_receivers_meta[0] );

							// reindex array for zero offset so the array key index for msg, name, email work
							// if and when this code retriggers for this order item
							$item_receivers_meta = array_values( $item_receivers_meta );
						}
						
						// If the "to email" is empty due to it not being set in the receivers data then send to buyer
						if ( empty( $msg_details['voucher_to_email'] ) ) {
							$msg_details = array( 
									'voucher_from_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
									'voucher_from_email' => $order->billing_email,
									'voucher_to_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
									'voucher_to_email' => $order->billing_email,
									'voucher_message' => '',
									'voucher_template_id' => ''
							);
						}

						
						$amount = $coupon['coupon_amount'][0];

						$coupon_title = $this->create_voucher( $order_id, $coupon_prefix, $coupon, $msg_details['voucher_to_email'], $restrict_to_buyer, $amount, $msg_details['voucher_template_id'], $product_id );

						$coupon_data = get_post_meta( $this->get_coupon_id( $coupon_title ) );
						
						woocommerce_delete_order_item_meta( $item_id, 'Voucher Code 1' );
						
						woocommerce_update_order_item_meta( $item_id, 'Voucher Code 1', $coupon_title );
											
						$this->send_voucher( $coupon_title, $amount, $coupon_data['discount_type'][0], $coupon_data['expiry_date'][0], $msg_details, $order_id, $item, $product_id, true );

					}

				} elseif ( 'deactivate_voucher' == $mode ) {

					$existing_coupons = $this->get_existing_vouchers( 'publish', $order_id );

					if ( !is_wp_error( $existing_coupons) && count( $existing_coupons ) > 0 ) { 

						foreach( $existing_coupons as $key => $ec ) { 

							$ec->post_status = 'draft';

							$wpdb->update( $wpdb->posts, array( 'post_status' => 'draft' ), array( 'ID' => $ec->ID ) );

							//break;

						}

					}

				} 
			
			}

			return $coupon_title;

		}
		

		// Process vouchers when order status changes
		function handle_vouchers( $order_id, $mode ) {

			$total_needed = 0;
			
			$order = new WC_Order( $order_id );

			$this->order = $order; 
			
			$meta = get_post_meta( $order_id );

			$order_items = ( array )$order->get_items(); 
				
			// Count the number of vouchers in this order
			foreach( $order_items as $item_id => $item ) { 

				$product = $order->get_product_from_item( $item );

				if ( 'variation' == $product->product_type ) {
					$pid = $product->parent->id;
					$vid = $product->variation_id;
				} else {
					$pid = $product->id;
					$vid = null;
				}

				if ( empty( $pid ) )
					continue;
				
				$gc_enabled = get_post_meta( $pid, 'ignite_gift_enabled', true );

				if ( !$gc_enabled )
					continue;
			
				$total_needed += $item['qty'];
			}


			foreach( $order_items as $item_id => $item ) { 

				$product = $order->get_product_from_item( $item );

				if ( 'variation' == $product->product_type ) {
					$pid = $product->parent->id;
					$vid = $product->variation_id;
				} else {
					$pid = $product->id;
					$vid = null;
				}

				if ( !get_post_meta( $pid, 'ignite_gift_enabled', true ) )
					continue;

				$coupon_prefix = get_post_meta( $pid, '_coupon_prefix', true );

				if ( !$coupon_prefix )
					$coupon_prefix = '';

				$msg_details = array( 
						'voucher_from_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
						'voucher_from_email' => $order->billing_email,
						'voucher_to_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
						'voucher_to_email' => $order->billing_email,
						'voucher_message' => ''
				);

				$new_coupon = $this->adjust_voucher( $coupon_prefix, $mode, $msg_details, $order_id, $order, $pid, $vid, $item, $item_id, $product, $total_needed, $item['qty'] );

			}

		}


		// Add voucher codes when an order requires that due to purchase
		function add_vouchers( $order_id ) {

			$this->handle_vouchers( $order_id, 'add_voucher' );

		}


		// Remove voucher codes when if any were issued as a result of a purchase
		function deactivate_vouchers( $order_id ) {

			$this->handle_vouchers( $order_id, 'deactivate_voucher' );

		}

		// Refund voucher codes used during a purchase
		function refund_vouchers( $order_id ) {

			$this->process_voucher_refund( $order_id );

		}


		// Check to determine if a voucher needs to be debited, happens if an order was previously marked as refunded or cancelled
		function debit_voucher( $order_id ) { 

			$this->maybe_charge_voucher( $order_id ); 

		}


		function ignite_gc_qrcode( $order_id = '', $preview = false, $voucher_code = null ) {
			global $wpdb;
			
			if ( empty( $this->admin_settings ) )
				$this->admin_settings = get_option( 'woocommerce_woocommerce_gift_certificates_settings' );
		
			if ( 'yes' != $this->admin_settings['use_qr'] )
				return;
			
			if ( $preview )
				$url = admin_url( '/edit.php?post_type=shop_order' );
			else
				$url = urlencode( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) );

			if ( 'coupon' == $this->admin_settings['qr_links_to'] ) { 
			
				$sql = 'select ID from ' . $wpdb->posts . ' where post_title="' . $voucher_code . '" and post_type="shop_coupon" and post_status="publish" limit 1';
				
				$id = $wpdb->get_var( $sql );
				
				$url = urlencode( admin_url( 'post.php?post=' . $id . '&action=edit' ) );
				
			}
		
			$size = apply_filters( 'ignitewoo_gift_certs_qr_code_size', array( 150, 150 ) );
			
			$size = $size[0] . 'x' . $size[1];
			
			// Docs: https://developers.google.com/chart/infographics/docs/qr_codes?csw=1
			$url = 'https://chart.googleapis.com/chart?chs=' . $size . '&cht=qr&chl=' . $url . '&choe=UTF-8';

			$url = apply_filters( 'ignitewoo_gift_certs_qr_code_url', $url, $order_id ); 
			
			?>

			<img class="ignitewoo_gc_qr_code" src="<?php echo $url; ?>">

			<?php 

		}
		

		function ign_vouchers_locate_preview_template( $locate ) {

			$post_type = get_query_var( 'post_type' );

			if ( 'ign_voucher' == $post_type && strpos( $locate, 'single.php' ) ) {
				$locate = $this->plugin_path . '/templates/single-ign_voucher.php';
			}

			return $locate;
		}
		
		
		function get_coupon_id( $voucher_code = '' ) { 
			global $wpdb;
			
			if ( empty( $voucher_code ) )
				return;
				
			$sql = 'select ID from ' . $wpdb->posts . ' where post_type="shop_coupon" and post_title="' . $voucher_code . '"';
			
			$id = $wpdb->get_var( $sql );
			
			if ( !empty( $id ) && !is_wp_error( $id ) )
				return $id;
				
		
		}
		
		
		// Send a msg to the recipient of the voucher code and record the voucher code in the item meta
		function send_voucher( $voucher_code, $amount, $type, $expires, $msg_details = array(), $order_id, $item, $product_id, $create_pdf = false, $preview = false ) {
			global $woocommerce, $heading;
			
			if ( !empty( $this->admin_settings['email_vouchers'] ) && 'no' == $this->admin_settings['email_vouchers'] )
				return;

			$pid = $product_id;
			
			if ( empty( $this->admin_settings ) )
				$this->admin_settings = get_option( 'woocommerce_woocommerce_gift_certificates_settings' );

			$sitename = get_option( 'blogname' );

			// handle special chars in the site name
			$sitename = wp_specialchars_decode( $sitename , ENT_QUOTES );

			$to = $msg_details['voucher_to_email'];

			$from = $msg_details['voucher_from_name'] . ' ( ' . $msg_details['voucher_from_email'] . ' )';
							
			$subject = $this->admin_settings['email_subject'];

			// maybe set intial option values
			if ( empty( $subject ) || !$subject ) {

				require_once( dirname( __FILE__ ) . '/classes/class-admin-settings.php' );
				
				$this->admin = new IgniteWoo_GC_Admin();
				
				$settings = $this->admin->init_form_fields();
				
				foreach( $this->admin->form_fields as $k => $v ) {
				
					if ( 'title' == $v['type'] )
						continue;
						
					if ( !get_option( 'woocommerce_' . $this->admin->id . $k ) )
						update_option( 'woocommerce_' . $this->admin->id . $k, $v['default'] );
						
				}
			}

			$subject = sprintf( $subject, $from );

			$message = '';

			if ( isset( $msg_details['voucher_to_name'] ) && '' != $msg_details['voucher_to_name'] ) 
				$recipient_name = $msg_details['voucher_to_name'];
			else 
				$recipient_name = '';

			if ( empty( $heading ) )
				$heading = '';
				
			// define WooCommmerce $heading var for use in the email template file
			if ( 'ign_store_credit' == $type )
				$heading =  sprintf( $this->admin_settings['heading_store_credit'], woocommerce_price( $amount ) );

			else if ( 'percent_product' == $type )
				$heading =  sprintf( $this->admin_settings['heading_percent_product'], $amount );

			else if ( 'percent' == $type )
				$heading =  sprintf( $this->admin_settings['heading_percent'], $amount );

			else if ( 'fixed_cart' == $type )
				$heading =  sprintf( $this->admin_settings['heading_fixed_cart'], woocommerce_price( $amount ) );

			else if ( 'fixed_product' == $type )
				$heading =  sprintf( $this->admin_settings['heading_fixed_product'], woocommerce_price( $amount ) );

			if ( isset( $msg_details['voucher_message'] ) && '' != trim( $msg_details['voucher_message'] ) ) {

				$message .= '<p>' . sprintf( $this->admin_settings['buyer_template_prefix'], $from ) . '</p>';

				$message .= wpautop( sprintf( $this->admin_settings['buyer_template'], $msg_details['voucher_message'] ) );


			}

			if ( !empty( $expires ) )
				$voucher_expires = $expires;
			else
				$voucher_expires = '';

			if ( $preview ) {
				$mailer = $woocommerce->mailer(); // because we need hooks loaded
				$preview = true;
			}

			add_action( 'ignite_gc_qrcode', array( &$this, 'ignite_gc_qrcode' ), 1, 3 );

			
			// Make sure hooks are in place - reload the class object

			$mailer = $woocommerce->mailer(); // woocommerce_email = new WC_Emails();

			if ( !has_action( 'woocommerce_email_header', array( $mailer, 'email_header' ) ) )
				add_action( 'woocommerce_email_header', array( $mailer, 'email_header' ) );
				
			if ( !has_action( 'woocommerce_email_footer', array( $mailer, 'email_footer' ) ) )
				add_action( 'woocommerce_email_footer', array( $mailer, 'email_footer' ) );
				
			if ( !has_action( 'woocommerce_email_order_meta', array( $mailer, 'order_meta' ) ) )
				add_action( 'woocommerce_email_order_meta', array( $mailer, 'order_meta' ), 10, 3 );
				
			$template = locate_template( array( 'templates/voucher_email.php' ), false, false );

			
			if ( $create_pdf ) {
				
				$attachments = $this->create_pdf( $order_id, $msg_details['voucher_template_id'], $msg_details, $item, $voucher_code, $amount, $product_id );
				
				$has_pdf = true;
				
				// Store file path
				$vid = $this->get_coupon_id( $voucher_code );
				
				if ( !empty( $vid ) )
					update_post_meta( $vid, 'voucher_file_path', $attachments );
				
			} else {
			
				$attachments = null;
				
				$has_pdf = false;
			}
				
				
			ob_start();

			if ( '' != $template ) 
				require( $template );
			else 
				require( dirname( __FILE__ ) . '/templates/voucher_email.php' );

			$msg_body = ob_get_contents();

			ob_end_clean();

			if ( $preview ) {
				echo '<p style="margin-top: 10px"><span style="font-weight: bold">'; _e( 'Message Subject: ', 'ignitewoo_gift_certs' ); echo '</span>';
				echo $subject . '</p>';
				echo $msg_body;
				die;
			}
			
			$headers = apply_filters( 'ignitewoo_voucher_email_headers', null ); 
				
			woocommerce_mail( $to, $subject, $msg_body, $headers, $attachments );

			if ( 'yes' ==  $this->admin_settings['email_admin'] ) {
			
				$store_admin = get_option( 'woocommerce_new_order_email_recipient', false );
				
				if ( empty( $store_admin ) ) { 
					$store_admin = get_option( 'woocommerce_new_order_settings', false );
					if ( !empty( $store_admin['recipient'] ) )
						$store_admin = $store_admin['recipient'];
				}
				
				if ( !$store_admin )
					$store_admin = get_option( 'admin_email', false );
					
				if ( !empty( $store_admin ) ) {
					$subject = '[COPY] ' . $subject;
					woocommerce_mail( $store_admin, $subject, $msg_body, $headers, $attachments );
				}
			}
		}

		
		function create_pdf( $order_id, $voucher_id, $msg_details = array(), $item, $voucher_code, $amount, $product_id ) { 
			global $wpdb;

			require_once( dirname( __FILE__ ) . '/lib/fpdf/fpdf.php' );
			
			require_once( dirname( __FILE__ ) . '/classes/class-vouchers.php' );
			
			$images = get_post_meta( $voucher_id, '_image_ids', true );
			
			if ( empty( $images ) )
				return null;

			$expiry = get_post_meta( $this->get_coupon_id( $voucher_code ), 'expiry_date', true );
			
			$desc = $wpdb->get_var( 'select post_excerpt from ' . $wpdb->posts . ' where ID = ' . $product_id );

			$item_data = array( 
				'voucher_number' => $voucher_id,
				'voucher_expiration' => '', // should be a date time string to be used by strtotime().
				'data' => $msg_details, //product name MAKE THIS AVAILABLE IN THE HTML TEMPLATE FILE
				'voucher_image_id' => $images[0], //image selected by shopper
				'name' => '', // product name
				'item' => $item, // order item object
				'coupon_id' => $this->get_coupon_id( $voucher_code ),
				'expiry' => $expiry,
				'coupon_code' => $voucher_code,
				'coupon_amount' => $amount,
				'product_desc' => $desc,
				
			);
			
			$pdf = new IGN_Gift_Cert_Voucher( $voucher_id, $order_id, $item_data );

			$file = $pdf->generate_pdf( true ); // generate a file and save it, return the path / filename

			return $file;
			
		}


		// Maybe show user's gift certificates / store credit / coupon code and expiry date
		function show_customer_vouchers() {

			global $current_user;

			if ( !$current_user || !is_a( $current_user, 'WP_User' ) ) 
				$current_user = wp_get_current_user();

			$args = array(
					'post_type'		=> 'shop_coupon',
					'post_status'		=> 'publish',
					'posts_per_page' 	=> -1,
					'meta_query' 		=> array(
									array(
										'key'		=> 'customer_email',
										'value' 	=> $current_user->user_email,
										'compare'	=> 'LIKE'
									),
									array(
										'key'		=> 'coupon_amount',
										'value' 	=> '0',
										'compare'	=> '>=',
										'type'		=> 'NUMERIC'
									)
					)
				);
				
			$vouchers = get_posts( $args );

			if ( !$vouchers ) 
				return;

			?>

			<h2><?php _e('Store Vouchers', 'ignitewoo_gift_certs' ); ?></h2>

			<ul class="my_account_vouchers">

				<?php 

				foreach ( $vouchers as $code ) {

					$coupon = new WC_Coupon( $code->post_title );

					if ( 'ign_store_credit' == $coupon->discount_type )
						$value = __( 'Store credit of ', 'ignitewoo_gift_certs' ) . woocommerce_price( $coupon->amount );

					else if ( 'percent_product' == $coupon->discount_type )						
						$value = $coupon->amount . '%' . __( ' discount on a product.', 'ignitewoo_gift_certs' );
							
					else if ( 'percent' == $coupon->discount_type )
						$value = $coupon->amount . '%' . __( ' discount on your entire purchase.', 'ignitewoo_gift_certs' );

					else if ( 'fixed_cart' == $coupon->discount_type )
						$value = woocommerce_price( $coupon->amount ) . __( ' discount on your entire purchase.', 'ignitewoo_gift_certs' );

					else if ( 'fixed_product' == $coupon->discount_type )
						$value = woocommerce_price( $coupon->amount ) . __( ' discount on a product.', 'ignitewoo_gift_certs' );

					if ( '' != $coupon->expiry_date ) {

						$the_date = is_numeric( $coupon->expiry_date ) ? date( 'M j, Y', $coupon->expiry_date ) : $coupon->expiry_date;
					
						$expires = ' &mdash; <span class="my_account_voucher_expires">' . 
							    __( 'Voucher expires:', 'ignitewoo_gift_certs' ) .
							    ' ' . $the_date . 
							    '<span>';
					}

					echo '<li>
						<strong>Code: '. $coupon->code .'</strong> &mdash;'. $value  . $expires . '</li>';

				}

				?>

			</ul>

			<?php

		}
		
		
		function get_voucher_download_path( $voucher_code ) { 
		
			$vid = $this->get_coupon_id( $voucher_code );
			
			if ( !empty( $vid ) )
				return get_post_meta( $vid, 'voucher_file_path', true );
		
		}
		
		
		function download_voucher() {
			global $wpdb, $is_IE;

			if ( !isset( $_GET['voucher_file'] ) || !isset( $_GET['order'] ) )
				return;

			$coupon_code = urldecode( $_GET['voucher_file'] );
			
			$order_id = urldecode( $_GET['order'] ); // md5 of order ID number

			$sql = 'select ID from ' . $wpdb->posts . ' where post_type="shop_order" and SHA1( ID ) = "' . $order_id . '"';
			
			$res = $wpdb->get_var( $sql );
			
			if ( empty( $res ) )
				wp_die( __( 'Invalid order.', 'woocommerce' ) . ' <a href="' . home_url() . '">' . __( 'Go to homepage &rarr;', 'woocommerce' ) . '</a>' );
			
			$order = new WC_Order( $res );
							
			$file_path = $this->get_voucher_download_path( $coupon_code );

			if ( !file_exists( $file_path ) )
				wp_die( __( 'Invalid download request', 'woocommerce' ) . ' <a href="' . home_url() . '">' . __( 'Go to homepage &rarr;', 'woocommerce' ) . '</a>' );
			

			$file_download_method = apply_filters( 'woocommerce_file_download_method', get_option( 'woocommerce_file_download_method' ), $product_id );

			$file_extension  = strtolower( substr( strrchr( $file_path, "." ), 1 ) );
			
			$ctype           = "application/force-download";

			foreach ( get_allowed_mime_types() as $mime => $type ) {
			
				$mimes = explode( '|', $mime );
				
				if ( in_array( $file_extension, $mimes ) ) {
					$ctype = $type;
					break;
				}
			}

			if ( !@ini_get('safe_mode') )
				@set_time_limit(0);

			if ( function_exists( 'get_magic_quotes_runtime' ) && get_magic_quotes_runtime() )
				@set_magic_quotes_runtime(0);

			if ( function_exists( 'apache_setenv' ) )
				@apache_setenv( 'no-gzip', 1 );

			@session_write_close();
			
			@ini_set( 'zlib.output_compression', 'Off' );
			
			@ob_end_clean();

			if ( ob_get_level() )
				@ob_end_clean(); // Zip corruption fix

			if ( $is_IE && is_ssl() ) {
				// IE bug prevents download via SSL when Cache Control and Pragma no-cache headers set.
				header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
				
				header( 'Cache-Control: private' );
				
			} else {
			
				nocache_headers();
				
			}

			$file_name = basename( $file_path );

			if ( strstr( $file_name, '?' ) ) {
			
				$exploded_file_name = explode( '?', $file_name );
				
				$file_name = current( $exploded_file_name );
				
			}

			header( "Robots: none" );
			
			header( "Content-Type: " . $ctype );
			
			header( "Content-Description: File Transfer" );
			
			header( "Content-Disposition: attachment; filename=\"" . $file_name . "\";" );
			
			header( "Content-Transfer-Encoding: binary" );

			if ( $size = @filesize( $file_path ) )
				header( "Content-Length: " . $size );

			if ( $file_download_method == 'xsendfile' ) {

				// Path fix - kudos to Jason Judge
				if ( getcwd() )
					$file_path = trim( preg_replace( '`^' . getcwd() . '`' , '', $file_path ), '/' );

				header( "Content-Disposition: attachment; filename=\"" . $file_name . "\";" );

				if ( function_exists( 'apache_get_modules' ) && in_array( 'mod_xsendfile', apache_get_modules() ) ) {

					header( "X-Sendfile: $file_path" );
					exit;

				} elseif ( stristr( getenv( 'SERVER_SOFTWARE' ), 'lighttpd' ) ) {

					header( "X-Lighttpd-Sendfile: $file_path" );
					exit;

				} elseif ( stristr( getenv( 'SERVER_SOFTWARE' ), 'nginx' ) || stristr( getenv( 'SERVER_SOFTWARE' ), 'cherokee' ) ) {

					header( "X-Accel-Redirect: /$file_path" );
					exit;

				}
			}
	
			@woocommerce_readfile_chunked( $file_path ) or wp_die( __( '404 - File not found', 'woocommerce' ) . ' <a href="' . home_url() . '">' . __( 'Go to homepage &rarr;', 'woocommerce' ) . '</a>' );

			die;

		}
		
	
		// IGN One Page Checkout support -------------------------------------------
		function hide_qty( $identifier = '' ) { 
			?>

			<script>
				jQuery( document ).ready( function() { 
				
					<?php if ( !empty( $identifier ) ) { ?>
					
					jQuery( "<?php echo $identifier ?> .quantity" ).css( "display", "none" );
					
					<?php } else { ?>
					
					jQuery( ".quantity" ).css( "display", "none" );
					
					<?php } ?>
				});
			</script>

			<?php 
		
		}
		
		
		function adjust_template_hooks( $args ) { 

			if ( $args['minimum'] )
				add_action( 'woocommerce_single_product_summary', array( &$this->pricer, 'display_minimum_price'), 15 );

			add_action( 'woocommerce_before_add_to_cart_button', array( &$this->pricer, 'display_price_input' ) );
				
			add_filter( 'woocommerce_get_price_html', array( &$this->pricer, 'filter_suggested_price'), 10, 2 );	
		}
		
		
		function remove_template_hooks(  ) { 

			remove_action( 'woocommerce_single_product_summary', array( &$this->pricer, 'display_minimum_price'), 15 );
			
			remove_action( 'woocommerce_before_add_to_cart_button', array( &$this->pricer, 'display_price_input' ) );
			
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' , 10 );
		
		}
		
		
		function opc_content( $_product, $args ) { 
			global $product; 

			$product = $_product;

			unset( $_product );

			$product->minimum = isset( $args['minimum'] ) ? $args['minimum'] : '';
			
			$product->suggested = isset( $args['suggested'] ) ? $args['suggested'] : '';
			
			$product->label_text = isset( $args['label_text'] ) ? $args['label_text'] : '';

			//if ( !isset( $args['display_form'] ) || false !== $args['display_form'] )
			//	$this->pricer->display_price_input();

			if ( !empty( $product->minimum ) ) {

				$this->js_args['notmin'] = sprintf( __( "Please enter at least %s", "woocommerce" ) , woocommerce_price( $product->minimum ) );
				$this->js_args['min'] = $product->minimum;

			}

			$args = array();
			
			foreach( $this->js_args as $k => $v )
				$args[] = $k . ': "' . addslashes( $v ) . '"';
				
			$args = implode( ',', $args );
			
			?>
			<link rel="stylesheet" href="<?php echo $this->pricer->css_file ?>">
			<?php

		}
		
		// END OPC support -------------------------------------------------------------


	}
	

}

global $ignite_gift_certs;

$ignite_gift_certs = new Ignite_Gift_Certs();


/**
* Required functions
*/
if ( ! function_exists( 'ignitewoo_queue_update' ) )
	require_once( dirname( __FILE__ ) . '/ignitewoo_updater/ignitewoo_update_api.php' );

$this_plugin_base = plugin_basename( __FILE__ );

add_action( "after_plugin_row_" . $this_plugin_base, 'ignite_plugin_update_row', 1, 2 );


/**
* Plugin updates
*/
ignitewoo_queue_update( plugin_basename( __FILE__ ), '000034507680e9da027946f01cc71467', '228' );
