<?php
/**
 * Plugin Name: WooCommerce Quick Checkout
 * Plugin URI: http://wordimpress.com/
 * Description: Single page checkout process for WooCommerce to expedite the checkout process and increase conversion rates
 * Version: 1.9.5
 * Author: WordImpress
 * Author URI: http://wordimpress.com/
 * License: GPLv2
 * Text Domain: wqc
 * GitHub Plugin URI: https://github.com/WordImpress/WooCommerce-Quick-Checkout
 * GitHub Branch:     master
 */

//!!! Important to update this version number prior to release !!!
if ( ! defined( 'WQC_PLUGIN_VERSION' ) ) {
	define( 'WQC_PLUGIN_VERSION', '1.9.5' );
}

// Define Constants
if ( ! defined( 'WQC_PLUGIN_PATH' ) ) {
	define( 'WQC_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( ! defined( 'WQC_PLUGIN_URL' ) ) {
	define( 'WQC_PLUGIN_URL', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );
}
if ( ! defined( 'WQC_PLUGIN_BASE' ) ) {
	define( 'WQC_PLUGIN_BASE', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

global $woocommerce;

if ( ! class_exists( 'WC_Quick_Checkout' ) ) {

	class WC_Quick_Checkout {

		/**
		 * @var WooCommerce The single instance of the class
		 * @since 1.0
		 */
		protected static $_instance = null;

		/**
		 * Min Woo Version
		 * @var string
		 */
		public $min_woocommerce_version = '2.1';

		/**
		 * Plugin Path
		 *
		 * @var null|string
		 */
		public $plugin_path = null;

		/**
		 * Cart instance.
		 *
		 * @var Quick_Checkout_Engine
		 */
		public $quick_checkout_engine = null;

		/**
		 * Cart instance.
		 *
		 * @var Quick_Checkout_Shop
		 */
		public $quick_checkout_shop = null;

		/**
		 * Shortcode Instance
		 *
		 * @var Quick_Checkout_Shortcodes
		 */
		public $quick_checkout_shortcodes = null;


		/**
		 * Quick Checkout Product
		 *
		 * @var Quick_Checkout_Product
		 */
		public $quick_checkout_product = null;

		/**
		 * License
		 *
		 * @var null
		 */
		public $licence = null;

		/**
		 * License key
		 *
		 * @var null
		 */
		public $licence_key = null;

		/**
		 * Checkout Page ID
		 *
		 * @var null
		 */
		public $checkout_page_id = null;

		/**
		 * Class Constructor
		 */
		public function __construct() {

			$this->file        = __FILE__;
			$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
			$this->plugin_url  = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) );
			$this->store_url   = 'https://wordimpress.com';
			$this->item_name   = __( 'WooCommerce Quick Checkout', 'wqc' );

			//check version prior to initialize
			add_action( 'plugins_loaded', array( $this, 'version_check' ), 10 );

			//i18n
			add_action( 'plugins_loaded', array( $this, 'load_qc_textdomain' ) );

			// Loaded action
			do_action( 'woocommerce_quick_checkout_loaded' );

		}


		/**
		 * Main WC Quick Checkout Instance
		 *
		 * Ensures only one instance of WC Quick Checkout is loaded or can be loaded.
		 *
		 * @since 1.0
		 * @static
		 * @see   WC()
		 * @return WooCommerce - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}


		/**
		 * Check WooCommerce Version
		 *
		 * Require at least WooCommerce version 2.1+
		 */
		public function version_check() {

			global $woocommerce;

			if ( is_object( $woocommerce ) && version_compare( WC()->version, $this->min_woocommerce_version ) >= 0 ) {
				$this->init();
				update_option( 'wqc_wc_version_flag', WC()->version );

			} else {
				add_action( 'admin_notices', array( &$this, 'admin_notice' ) );
			}

		}

		/**
		 * Display Update Notice
		 */
		public function admin_notice() {
			echo '<div class="error">
     		    <p>' . sprintf( __( 'The <strong>WooCommerce Quick Checkout</strong> extension requires at least WooCommerce %s in order to function properly. Please install or upgrade the WooCommerce plugin in order to use the WooCommerce Quick Checkout plugin.', 'wqc' ), $this->min_woocommerce_version ) . '</p>
     		</div>';
			//Deactivate this beast
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}


		/**
		 * Plugin Initialization
		 */
		public function init() {

			//Admin only
			if ( is_admin() ) {

				// Require admin class to handle all backend functions
				require_once( dirname( __FILE__ ) . '/classes/class-wqc-admin.php' );

				//Licensing
				require_once( dirname( __FILE__ ) . '/includes/license/licence.php' );

				//Licence Args
				$licence_args = array(
					'plugin_basename'     => WQC_PLUGIN_BASE,
					'settings_page'       => 'settings_page_quick-checkout-license', //Used to determine CSS enqueues
					'store_url'           => $this->store_url,
					'item_name'           => $this->item_name,
					'licence_key_setting' => 'wqc_licence_setting',
					'licence_key_option'  => 'edd_quick_checkout_license_key',
					'licence_key_status'  => 'edd_quick_checkout_license_status',
				);

				$current_options   = get_option( $licence_args['licence_key_option'] );
				$this->licence_key = ! empty( $current_options ) ? trim( $current_options['license_key'] ) : '';
				$this->licence     = new Quick_Checkout_Licence( $licence_args );
				add_action( 'admin_init', array( $this, 'edd_sl_wordimpress_updater' ) );

				//get TinyMCE shortcode generator for admin (coming soon)
				//				require_once( dirname( __FILE__ ) . '/classes/class-woocommerce-quick-checkout-tinymce.php' );
				//				$this->quick_checkout_tinyMCE = new Quick_Checkout_TinyMCE();

				//require_once dirname( __FILE__ ) . '/classes/class-woocommerce-quick-checkout-shortcode-generator.php';

			}

			//Class instances
			require_once( dirname( __FILE__ ) . '/classes/class-wqc-engine.php' );
			require_once( dirname( __FILE__ ) . '/classes/class-wqc-shop.php' );
			require_once( dirname( __FILE__ ) . '/classes/class-wqc-shortcodes.php' );
			require_once( dirname( __FILE__ ) . '/classes/class-wqc-product.php' );
			require_once( dirname( __FILE__ ) . '/includes/theme-compatibility.php' );

			$this->quick_checkout_engine     = new Quick_Checkout_Engine();
			$this->quick_checkout_shortcodes = new Quick_Checkout_Shortcodes();
			$this->quick_checkout_shop       = new Quick_Checkout_Shop();
			$this->quick_checkout_product    = new Quick_Checkout_Product();

		}


		/**
		 * Load Transation Files
		 *
		 * @description: Loads appropriate text domain
		 * @reference  : http://www.gsy-design.com/how-to-generate-a-pot-file-using-poedit/
		 */
		public function load_qc_textdomain() {
			// Initializing translations. Translation files in the WP_LANG_DIR folder have a higher priority.
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wqc' );
			load_textdomain( 'wqc', WP_LANG_DIR . '/woocommerce-quick-checkout/wqc-' . $locale . '.mo' );
			load_plugin_textdomain( 'wqc', false, 'woocommerce-quick-checkout/languages' );

		}


		/**
		 * Plugin Updates
		 *
		 * Provides native plugin updates for users with valid, activated license keys
		 */
		function edd_sl_wordimpress_updater() {

			$meta = get_plugin_data( $this->file, false );

			// Include Licensing
			if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
				// load our custom updater
				include_once( dirname( __FILE__ ) . '/includes/license/classes/EDD_SL_Plugin_Updater.php' );
			}

			// setup the updater
			$edd_updater = new EDD_SL_Plugin_Updater(
				$this->store_url, WQC_PLUGIN_BASE, array(
					'version'   => $meta['Version'], // current version number
					'license'   => $this->licence_key, // license key (used get_option above to retrieve from DB)
					'item_name' => $this->item_name, // name of this plugin
					'author'    => 'WordImpress' // author of this plugin
				)
			);

		}


		


	} //end class


} //end if class exists

/**
 * Returns the main instance of Quick Checkout to prevent the need to use globals.
 *
 * @since  1.0
 * @return WC_Quick_Checkout
 */
function WC_Quick_Checkout() {
	return WC_Quick_Checkout::instance();
}

WC_Quick_Checkout();


/**
 * Is WooCommerce version 2.3 plus Conditional
 *
 * @description: Used to check if WC version flag is set and it GREATER than 2.3; we use an option because the WC() function isn't available yet here
 *
 * @return bool
 */
function is_woocommerce_v23_plus() {

	$version = get_option( 'wqc_wc_version_flag' );

	if ( ! empty( $version ) && version_compare( $version, '2.3' ) >= 0 ) {
		return true;
	} else {
		return false;
	}

}