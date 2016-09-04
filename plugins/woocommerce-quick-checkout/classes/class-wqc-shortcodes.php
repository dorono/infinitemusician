<?php

/**
 *  Quick Checkout Shortcodes
 *
 * @description: Add the shortcode capabilities of the plugin
 */
class Quick_Checkout_Shortcodes {

	protected $atts = array(
		'id'                    => '',
		'checkout_text'         => 'Buy Now',
		'checkout_action'       => 'lightbox',
		'shop_cart_reveal'      => 'yes',
		'shop_cart_reveal_text' => 'Checkout Now',
		'clear_cart'            => 'false',
		'image_overlay'         => true,
		'variation_id'          => '',
		'quantity'              => '1'
	);

	/**
	 * Quick_Checkout_Shortcodes constructor.
	 */
	public function __construct() {

		//The One and Only
		add_shortcode( 'quick_checkout', array( $this, 'shortcode' ) );
		add_shortcode( 'product_quick_checkout', array( $this, 'shortcode_product' ) );

		//SOON TO BE DEPRECATED: Single Product Quick Checkout Shortcode
		add_shortcode( 'open_checkout', array( $this, 'shortcode_open_checkout' ) );
		add_shortcode( 'reveal_quick_checkout', array( $this, 'shortcode_reveal_quick_checkout' ) );
		add_shortcode( 'show_quick_checkout', array( $this, 'shortcode_show_quick_checkout' ) );

		//Support for standard WooCommerce shortcodes; Only enable if the option has been enabled
		add_action( 'wp_loaded', array( $this, 'override_woocommerce_shortcodes' ) );

	}

	/**
	 * Quick Checkout Shortcode [wqc_checkout]
	 *
	 * @TODO: Enable single shortcode:
	 *
	 * @param $atts
	 *
	 * @return mixed
	 */
	function shortcode( $atts ) {

		$shortcode_atts = $this->atts;

		//get the args for this shortcode
		$atts = shortcode_atts( $shortcode_atts, $atts, 'quick_checkout' );


		if ( empty( $atts['id'] ) ) {
			return $this->shortcode_open_checkout( $atts );
		}


		if ( 'yes' != $atts['shop_cart_reveal'] ) {
			$atts['autoload'] = false;

			return $this->shortcode_show_quick_checkout( $atts );
		}

		return $this->shortcode_reveal_quick_checkout( $atts );

	}


	/**
	 * Quick Checkout Single Product Shortcode [product_quick_checkout id=""]
	 *
	 * @param $atts
	 *
	 * @return mixed
	 */
	function shortcode_product( $atts ) {

		//get the args for this shortcode
		$atts = shortcode_atts(
			array(
				'id'                    => '',
				'checkout_text'         => __( 'Buy Now', 'wqc' ),
				'checkout_action'       => 'lightbox',
				'shop_cart_reveal'      => 'yes',
				'shop_cart_reveal_text' => __( 'Checkout Now', 'wqc' ),
				'clear_cart'            => 'false',
				'image_overlay'         => true
			), $atts, 'product_quick_checkout'
		);

		//get product output
		$output = '<div class="quick-checkout-product_quick_checkout">';
		$output .= WC_Shortcodes::product( $atts );

		//get image hover button
		$button = $this->shortcode_button( $atts );

		//string replace with button inserted (filter provided for users with modified templates)
		$string_to_replace = apply_filters( 'shortcode_product_str_replace', '</li>' );
		$output            = str_replace( $string_to_replace, $button . $string_to_replace, $output );


		$output .= WC_Quick_Checkout()->quick_checkout_engine->get_qc_wrap( $atts['id'] );

		$output .= '</div>'; //Finish wrap

		//Return shortcode output
		return apply_filters( 'product_quick_checkout_output', $output );

	}


	/**
	 * Quick Checkout Single Product Shortcode [reveal_quick_checkout id=""]
	 *
	 * @param $atts array
	 *
	 * @return mixed
	 */
	function shortcode_reveal_quick_checkout( $atts ) {

		//Support `variation_attr_*`
		$defaults = $this->supported_variation_attributes( array(
			'id'              => '',
			'checkout_text'   => __( 'Buy Now', 'wqc' ),
			'checkout_action' => 'lightbox',
			'variation_id'    => '',
			'quantity'        => '1',
			'clear_cart'      => 'true'
		), $atts );

		//Use shortcode_atts to properly filter defaults
		$atts = shortcode_atts( $defaults, $atts, 'reveal_quick_checkout' );

		//Get checkout
		ob_start();

		//Validation
		if ( $this->validate_shortcode_options( $atts ) ) {
			echo $this->shortcode_button( $atts );
			echo WC_Quick_Checkout()->quick_checkout_engine->get_qc_wrap( $atts['id'] );
		}

		$output = ob_get_clean();

		return apply_filters( 'shortcode_reveal_quick_checkout_output', $output );


	}

	/**
	 * Quick Checkout Single Product Shortcode [show_quick_checkout id=""]
	 *
	 * @param $atts
	 *
	 * @return mixed
	 */
	function shortcode_show_quick_checkout( $atts ) {

		//Support `variation_attr_*`
		$defaults = $this->supported_variation_attributes( array(
			'id'              => '',
			'quantity'        => '1',
			'variation_id'    => '',
			'checkout_action' => 'onpage',
			'clear_cart'      => 'true',
			'autoload'        => 'true',
			'checkout_text'   => null,
		), $atts );

		//Use shortcode_atts to properly filter defaults
		$atts = shortcode_atts( $defaults, $atts, 'show_quick_checkout' );

		ob_start();

		echo $this->shortcode_button( $atts );

		//Validation 1: Needs a product ID to continue
		if ( $this->validate_shortcode_options( $atts ) ) {

			//Passed validation
			echo WC_Quick_Checkout()->quick_checkout_engine->get_qc_wrap( $atts['id'] );

		}

		$output = ob_get_clean();

		//Return shortcode output
		return apply_filters( 'shortcode_show_quick_checkout_output', $output );

	}

	/**
	 * Open Checkout Link
	 *
	 * Displays a link to open checkout from any page. Commonly placed in headers and sidebars for easier checkout.
	 *
	 * Shortcode [open_checkout]
	 *
	 * @param $atts array
	 *
	 * @return string $output
	 */
	function shortcode_open_checkout( $atts ) {

		//get the args for this shortcode
		$atts = shortcode_atts(
			array(
				'checkout_text' => __( 'Checkout Now', 'wqc' ), // match other shortcode buttons
			), $atts, 'open_checkout'
		);

		$output = '<a href="#open-checkout" rel="nofollow" class="button quick-checkout-link">' . $atts['checkout_text'] . '</a>';

		ob_start();
		echo WC_Quick_Checkout()->quick_checkout_engine->get_qc_wrap();
		$output .= ob_get_clean();

		//Return shortcode output
		return $output;

	}


	/**
	 * Shortcode Image Hover Button
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function shortcode_button( $atts ) {

		//Setup Dynamic data attributes
		$data_atts = WC_Quick_Checkout()->quick_checkout_engine->set_url_data_attributes( $atts );

		//Shortcode Classes
		$shortcode_classes = '';

		if ( isset( $atts['image_overlay'] ) && $atts['image_overlay'] == true ) {
			$shortcode_classes .= 'quick-checkout-button-image-overlay ';
		} else {
			$shortcode_classes .= 'button ';
		}

		//Autoload?
		if ( ! empty( $atts['autoload'] ) ) {
			$shortcode_classes .= 'qc-trigger-autoload ';
		}

		$shortcode_classes .= apply_filters( 'wqc_shortcode_classes', 'quick-checkout-button quick-checkout-button-shortcode', $shortcode_classes );

		//Return QC anchor tag
		return apply_filters( 'qc_shortcode_button_anchor_tag', '<a href="#quick-checkout" class="' . $shortcode_classes . '" ' . $data_atts . '>' . $atts['checkout_text'] . '</a>' );

	}

	/**
	 * Override WooCommerce Shortcodes
	 *
	 * @description: Support for standard WooCommerce shortcodes; Only enable if the option has been enabled. We have to do this because WooCommerce doesn't have appropriate hooks to output the checkout on demand
	 *
	 * @since      1.8
	 */
	function override_woocommerce_shortcodes() {

		//[recent_products]
		$option_support_recent_products = get_option( 'woocommerce_quick_checkout_support_shortcode_recent_products' );
		if ( $option_support_recent_products === 'yes' ) {
			remove_shortcode( 'recent_products' );
			add_shortcode( 'recent_products', array( $this, 'override_shortcode_woocommerce_shortcodes' ) );
		}

		//[featured_products]
		$options_support_featured_products = get_option( 'woocommerce_quick_checkout_support_shortcode_featured_products' );
		if ( $options_support_featured_products === 'yes' ) {
			remove_shortcode( 'featured_products' );
			add_shortcode( 'featured_products', array( $this, 'override_shortcode_woocommerce_shortcodes' ) );
		}

		//[sale_products]
		$options_support_sale_products = get_option( 'woocommerce_quick_checkout_support_shortcode_sale_products' );
		if ( $options_support_sale_products === 'yes' ) {
			remove_shortcode( 'sale_products' );
			add_shortcode( 'sale_products', array( $this, 'override_shortcode_woocommerce_shortcodes' ) );
		}

		//[best_selling_products]
		$options_support_best_selling_products = get_option( 'woocommerce_quick_checkout_support_shortcode_best_selling_products' );
		if ( $options_support_best_selling_products === 'yes' ) {
			remove_shortcode( 'best_selling_products' );
			add_shortcode( 'best_selling_products', array( $this, 'override_shortcode_woocommerce_shortcodes' ) );
		}

		//[top_rated_products]
		$options_support_top_rated_products = get_option( 'woocommerce_quick_checkout_support_shortcode_top_rated_products' );

		if ( $options_support_top_rated_products === 'yes' ) {
			remove_shortcode( 'top_rated_products' );
			add_shortcode( 'top_rated_products', array( $this, 'override_shortcode_woocommerce_shortcodes' ) );
		}


	}

	/**
	 * Override WooCommerce Shortcodes
	 *
	 * @description: This function adds support for quick checkout to WooCommerce's included shortcodes
	 *
	 * @param $atts
	 * @param $content
	 * @param $tag
	 *
	 * @return string
	 */
	function override_shortcode_woocommerce_shortcodes( $atts, $content, $tag ) {

		$woo_shortcodes = new WC_Shortcodes();
		$wqc_shop       = new Quick_Checkout_Shop();

		ob_start();
		//ensure Buy Now button is output properly
		add_action( 'woocommerce_after_shop_loop_item', array(
			$wqc_shop,
			'quick_checkout_shop_button'
		) );

		$output = '<div class="qc-woo-shortcode-wrap">';
		$output .= WC_Quick_Checkout()->quick_checkout_engine->get_qc_wrap( $tag );

		//Determine which shortcode we are using
		switch ( $tag ) {
			case 'recent_products':
				$output .= $woo_shortcodes->recent_products( $atts );
				break;
			case 'featured_products':
				$output .= $woo_shortcodes->featured_products( $atts );
				break;
			case 'best_selling_products':
				$output .= $woo_shortcodes->best_selling_products( $atts );
				break;
			case 'sale_products':
				$output .= $woo_shortcodes->sale_products( $atts );
				break;
			case 'top_rated_products':
				$output .= $woo_shortcodes->top_rated_products( $atts );
				break;
		}

		$output .= '</div>'; //Close it

		//Get buffer
		$output .= ob_get_clean();

		//remove action to ensure any subsequent shortcodes don't inherit this action
		remove_action( 'woocommerce_after_shop_loop_item', array(
			$wqc_shop,
			'quick_checkout_shop_button'
		) );

		return $output;

	}

	/**
	 * Supported Variation Attributes
	 *
	 * @param $defaults
	 * @param $atts
	 *
	 * @return mixed
	 */
	function supported_variation_attributes( $defaults, $atts ) {

		foreach ( $atts as $key => $value ) {
			//Sanity check for $atts
			if ( strpos( $key, 'variation_attr_' ) !== 0 ) {
				continue;
			}
			$defaults[ $key ] = $value;
		}

		return $defaults;

	}


	/**
	 * Validate Shortcode Options
	 *
	 * @description: Function Validate Required Shortcode Fields
	 *
	 * @param      : $attributes
	 *
	 * @param $attributes
	 *
	 * @return bool
	 */
	public function validate_shortcode_options( $attributes ) {


		$message_no_id      = esc_attr__( 'It looks like you forgot to add the product ID option to this shortcode. Please make sure you have entered a valid WooCommerce Product ID.', 'wqc' );
		$missing_attributes = esc_attr__( 'Please set the options for this widget. Refer to the Quick Checkout documentation for more information.', 'wqc' );

		//Run through validation conditionals
		if ( empty( $attributes ) ) {
			echo WC_Quick_Checkout()->quick_checkout_engine->quick_checkout_error( $missing_attributes, 'error' );

			return false;
		}

		//Product ID check
		if ( empty( $attributes['id'] ) ) {
			echo WC_Quick_Checkout()->quick_checkout_engine->quick_checkout_error( $message_no_id, 'error' );

			return false;
		}

		//Check to ensure that the product is purchasable
		$_product = wc_get_product( $attributes['variation_id'] ? $attributes['variation_id'] : $attributes['id'] );

		if ( empty( $_product ) || ! $_product->is_purchasable() ) {
			echo WC_Quick_Checkout()->quick_checkout_engine->quick_checkout_error( esc_attr__( 'Sorry this product is not currently available to be purchased.', 'wqc' ), 'error' );

			return false;
		}

		//Stock check
		if ( ! $_product->is_in_stock() ) {
			echo WC_Quick_Checkout()->quick_checkout_engine->quick_checkout_error( esc_attr__( 'Sorry this product is out of stock.', 'wqc' ), 'error' );

			return false;
		}

		//passed validation!
		return true;


	}

}