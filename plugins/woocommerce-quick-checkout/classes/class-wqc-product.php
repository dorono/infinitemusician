<?php

/**
 *  Quick Checkout Product Class
 *
 * @description: Responsible for product pages
 * @since      : 1.0
 * @created    : 2/4/14
 */
class Quick_Checkout_Product {

	var $product_type = null;
	var $gravity_form = false;

	public function __construct() {

		//Get options from settings
		$this->product_option           = get_option( 'woocommerce_quick_checkout_product' );
		$this->product_action           = get_option( 'woocommerce_quick_checkout_product_action' );
		$this->product_button_display   = get_option( 'woocommerce_quick_checkout_product_button_display' );
		$this->product_button_text      = get_option( 'woocommerce_quick_checkout_product_button_text' );
		$this->product_checkout_display = get_option( 'woocommerce_quick_checkout_product_checkout_display_position' );
		$this->product_clear_cart       = get_option( 'woocommerce_quick_checkout_product_cart_action' );
		$this->product_image_button     = get_option( 'woocommerce_quick_checkout_product_image_button' );
		$this->enable_single_control    = get_option( 'woocommerce_quick_checkout_product_metabox' );


		add_action( 'wp', array( $this, 'quick_checkout_product_init' ) );

	}


	/**
	 * Product Init
	 *
	 * Executes on product page init
	 */
	function quick_checkout_product_init() {

		global $post;

		//bounce out if no post ID
		if ( ! isset( $post->ID ) ) {
			return;
		}

		$product = wc_get_product( $post->ID );

		$this->product_type = $product->product_type;

		//Sanity Check: bounce out if external Product (cannot be purchased on site via checkout)
		if ( $product->product_type == 'external' ) {
			return;
		}

		//Sanity Check: Does user want quick checkout enabled globally and not disabled on an individual post level
		if ( $this->is_quick_checkout_enabled_single() !== true ) {
			return;
		}

		//get single option for display from post meta
		$single_display_option_override = get_post_meta( $post->ID, 'qc_single_display_option', true );

		//determine which option is enabled on the product and global level and hook action
		//@TODO: There are some pretty nasty conditionals in this switch/case that may need optimization...
		switch ( true ) {

			//Display quick checkout button after
			case ( $this->product_button_display == 'after' && $single_display_option_override == '' || $this->product_button_display == 'after' && $single_display_option_override == 'default' || $single_display_option_override == 'after' ):

				//Determine action to hook depending on product type
				if ( $product->product_type == 'variable' ) {
					$action = 'woocommerce_after_single_variation';
				} else {
					$action = 'woocommerce_after_add_to_cart_button';
				}

				add_action( $action, array( $this, 'quick_checkout_product_button' ) );

				break;

			//Display before
			case ( $this->product_button_display == 'before' && $single_display_option_override == '' || $this->product_button_display == 'before' && $single_display_option_override == 'default' || $single_display_option_override == 'before' ):

				//Determine action to hook depending on product type
				if ( $product->product_type == 'variable' ) {
					$action = 'woocommerce_before_single_variation';
				} else {
					$action = 'woocommerce_before_add_to_cart_button';
				}

				add_action( $action, array( $this, 'quick_checkout_product_button' ) );

				break;

			//Replace button entirely
			case ( $this->product_button_display == 'replace' && $single_display_option_override == '' || $this->product_button_display == 'replace' && $single_display_option_override == 'default' || $single_display_option_override == 'replace' ):

				$this->product_button_display = 'replace';

				//add button before and hide normal button with CSS
				add_action( 'woocommerce_after_add_to_cart_button', array(
					$this,
					'quick_checkout_product_button'
				) );

				break;

		} //end switch case


		/**
		 * Product Image Hover Button
		 */
		$single_product_image_button = get_post_meta( $post->ID, 'qc_single_product_image_button', true );

		if ( $this->product_image_button == 'yes' && $single_product_image_button == '' || $single_product_image_button == 'yes' ) {

			add_action( 'woocommerce_product_thumbnails', array( $this, 'quick_checkout_product_image_button' ) );

		}


		$this->get_product_qc_wrap();


	}


	/**
	 * Get Product Quick Checkout Wrap
	 */
	function get_product_qc_wrap() {

		global $post;

		//get single option for display from post meta
		$single_product_action_override = get_post_meta( $post->ID, 'qc_single_product_action', true );

		/**
		 * Product Action - Get Checkout Action
		 *
		 * Depends on the option provided by the user globally and per product
		 */
		switch ( true ) {

			//Lightbox option
			case ( $this->product_action == 'lightbox' && $single_product_action_override == '' || $this->product_action == 'lightbox' && $single_product_action_override == 'default' || $single_product_action_override == 'lightbox' ):

				add_action( 'woocommerce_after_single_product', array(
					WC_Quick_Checkout()->quick_checkout_engine,
					'get_qc_wrap'
				) );
				break;

			//Display on page
			case ( $this->product_action == 'reveal' && $single_product_action_override == '' || $this->product_action == 'reveal' && $single_product_action_override == 'default' || $single_product_action_override == 'reveal' ):

				//Custom Single Position?
				$single_product_checkout_display = get_post_meta( $post->ID, 'qc_single_product_checkout_display_position', true );

				if ( ! empty( $single_product_checkout_display ) && $single_product_checkout_display !== 'default' ) {
					$this->product_checkout_display = $single_product_checkout_display;
				}

				add_action( $this->product_checkout_display, array(
					WC_Quick_Checkout()->quick_checkout_engine,
					'get_qc_wrap'
				) );

				break;

		}
	}

	/**
	 * Quick Checkout Product Button
	 */
	function quick_checkout_product_button() {

		global $post, $product;

		//Add classes for JS and CSS
		$classes                        = 'quick-checkout-product single_add_to_cart_button button alt';
		$single_display_option_override = get_post_meta( $post->ID, 'qc_single_display_option', true );
		$single_product_action_override = get_post_meta( $post->ID, 'qc_single_product_action', true );
		$single_product_cart_action     = get_post_meta( $post->ID, 'qc_single_cart_action', true );
		$single_product_button_text     = get_post_meta( $post->ID, 'qc_single_product_button_text', true );
		$gravity_form_enabled           = get_post_meta( $product->id, '_gravity_form_data', true );

		$replace_btn = false; //used to flag btn replace

		//Checkout Action
		$checkout_action = ( $single_product_action_override == 'default' || empty( $single_product_action_override ) ) ? $this->product_action : $single_product_action_override;

		//Add gform Class
		if ( $gravity_form_enabled ) {
			$classes .= ' quick-checkout-gform-button';
		}


		//Is there customized button text for this product?
		if ( ! empty( $single_product_button_text ) && $single_product_button_text !== 'Buy Now' ) {
			$this->product_button_text = $single_product_button_text;
		}

		//@TODO: Simplify if/else
		if ( $this->product_button_display == 'after' && $single_display_option_override == '' || $this->product_button_display == 'after' && $single_display_option_override == 'default' || $single_display_option_override == 'after' ) {
			//After class
			$classes .= ' quick-checkout-product-after';

		} elseif ( $this->product_button_display == 'before' && $single_display_option_override == '' || $this->product_button_display == 'before' && $single_display_option_override == 'default' || $single_display_option_override == 'before' ) {
			//Before class
			$classes .= ' quick-checkout-product-before';
		} elseif ( $this->product_button_display == 'replace' && $single_display_option_override == '' || $this->product_button_display == 'replace' && $single_display_option_override == 'default' || $single_display_option_override == 'replace' ) {
			//Replace
			$classes .= ' quick-checkout-product-replace';
			$replace_btn = true;
		}

		$classes = apply_filters( 'wqc_product_btn_classes', $classes );

		//Echo button
		echo '<button class="' . $classes . '" data-checkout_action="' . $checkout_action . '" data-product_id="' . esc_attr( $product->id ) . '" data-clear_cart="' . $single_product_cart_action . '">' . apply_filters( 'quick_checkout_product_button_text', $this->product_button_text ) . '</button>';

		//Hide button if replacing
		if ( $replace_btn === true ) {
			echo '<style>.product .single_add_to_cart_button { display:none !important; } .product button.single_add_to_cart_button.quick-checkout-product { display:inline-block !important; }</style>';
		}

	}


	/**
	 * Product Image Button
	 */
	public function quick_checkout_product_image_button() {

		global $product, $post;

		$single_product_action_override = get_post_meta( $post->ID, 'qc_single_product_action', true );
		$single_product_button_text     = get_post_meta( $post->ID, 'qc_single_product_button_text', true );
		$single_product_cart_action     = get_post_meta( $post->ID, 'qc_single_cart_action', true );

		//Checkout Action
		$checkout_action = ( $single_product_action_override == 'default' || empty( $single_product_action_override ) ) ? $this->product_action : $single_product_action_override;

		//Is there customized button text for this product?
		if ( ! empty( $single_product_button_text ) && $single_product_button_text !== 'Buy Now' ) {
			$this->product_button_text = $single_product_button_text;
		}


		if ( $product->product_type !== 'simple' ) {
			return false;
		}

		echo '<a href="#quick-checkout" class="quick-checkout-button quick-checkout-product quick-checkout-button-image-overlay quick-checkout-button-overlay-single" data-checkout_action="' . $checkout_action . '" data-product_single_cart_action="' . $single_product_cart_action . '" data-product_id="' . esc_attr( $product->id ) . '" data-product_sku="' . esc_attr( $product->get_sku() ) . '">' . apply_filters( 'quick_checkout_product_image_button_text', $this->product_button_text ) . '</a>';

		return true;

	}

	/**
	 * Check whether single product has Quick Checkout enabled or not
	 *
	 * @return bool
	 */
	function is_quick_checkout_enabled_single() {

		global $post;
		$enable_quick_checkout  = get_post_meta( $post->ID, 'qc_enable_checkbox', true );
		$disable_quick_checkout = get_post_meta( $post->ID, 'qc_disable_checkbox', true );


		$response = false;

		//if enabled checkbox is checked  = true
		if ( $enable_quick_checkout == 'on' ) {
			$response = true;
		}

		//if enable/disabled missing and global option turned on = true
		if ( empty( $enable_quick_checkout ) && empty( $disable_quick_checkout ) && $this->product_option == 'yes' ) {
			$response = true;
		}

		//if metabox is not enabled and global options not enabled
		if ( $this->product_option !== 'yes' && $this->enable_single_control !== 'yes' ) {
			$response = false;
		}

		return $response;

	}


}