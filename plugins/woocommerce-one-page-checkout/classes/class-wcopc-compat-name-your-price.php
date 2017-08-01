<?php
/**
 * @package		WooCommerce One Page Checkout
 * @subpackage	Name Your Price Extension Compatibility
 * @category	Compatibility Class
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class to hold Name Your Price compat functionality
 */
class WCOPC_Compat_Name_Your_Price {

	const PREFIX = '-opc-';

	public function __construct() {

		if ( class_exists( 'WC_Name_Your_Price' ) ) {
			add_action( 'wcopc_before_add_to_cart_button', array( __CLASS__, 'opc_nyp_price_input' ) );
			add_filter( 'nyp_field_prefix', array( __CLASS__, 'nyp_cart_prefix' ), 10, 2 );

			add_action( 'woocommerce_before_single_product', array( __CLASS__, 'maybe_swap_nyp_price_input' ) );

			if ( isset( WC_Name_Your_Price()->display ) ) {
				// Load the NYP scripts with OPC scripts.
				add_action( 'wcopc_enqueue_scripts', array( WC_Name_Your_Price()->display, 'nyp_scripts' ) );
				add_action( 'wcopc_enqueue_scripts', array( WC_Name_Your_Price()->display, 'nyp_style' ) );
			}
		}
	}

	/*
	 * Maybe swap default price input with OPC function that adds prefix.
	 * @param	obj $product
	 * @return	void
	 * @access	public
	 * @since	2.4.3
	 */
	public static function maybe_swap_nyp_price_input(){
		if ( PP_One_Page_Checkout::is_any_form_of_opc_page() ) {
			remove_action( 'woocommerce_before_add_to_cart_button', array( WC_Name_Your_Price()->display, 'display_price_input' ), 9 );
			add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'opc_nyp_price_input' ), 9 );
		}
	}

	/*
	 * Display Price Input in OPC templates.
	 * @param	obj $product
	 * @return	void
	 * @access	public
	 * @since	2.4.3
	 */
	public static function opc_nyp_price_input( $product = false ){
		if ( ! is_a( $product, 'WC_Product' ) ) {
			global $product;
		}

		if ( ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		$product_id = WC_Name_Your_Price_Core_Compatibility::get_id( $product );
		$prefix = PP_One_Page_Checkout::is_any_form_of_opc_page() ? self::PREFIX . $product_id : false; 
		WC_Name_Your_Price()->display->display_price_input( $product_id, $prefix );
	}

	/**
	 * Sets a unique prefix for unique NYP products in OPC templates. 
	 * The prefix is set and re-set globally before validating and adding to cart.
	 *
	 * @param  string  $prefix
	 * @param  int     $product_id
	 * @return string
	 */
	public static function nyp_cart_prefix( $prefix, $product_id ) {

		if ( PP_One_Page_Checkout::is_any_form_of_opc_page() ) {
			$prefix = self::PREFIX . $product_id;
		}

		return $prefix;
	}
}
new WCOPC_Compat_Name_Your_Price();