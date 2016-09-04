<?php

/**
 *  Quick Checkout Shop Class
 *
 * @description:
 * @since      :
 * @created    : 2/4/14
 */
class Quick_Checkout_Shop {

	public function __construct() {

		$this->shop_option             = get_option( 'woocommerce_quick_checkout_shop' );
		$this->shop_image_hover        = get_option( 'woocommerce_quick_checkout_shop_image_hover' );
		$this->shop_cart_reveal        = get_option( 'woocommerce_quick_checkout_shop_cart_reveal' );
		$this->shop_cart_reveal_text   = get_option( 'woocommerce_quick_checkout_shop_cart_link_text' );
		$this->shop_action             = get_option( 'woocommerce_quick_checkout_shop_action' );
		$this->shop_clear_cart         = get_option( 'woocommerce_quick_checkout_shop_cart_action' );
		$this->shop_button_text        = get_option( 'woocommerce_quick_checkout_shop_cart_button_text' );
		$this->shop_display_position   = get_option( 'woocommerce_quick_checkout_checkout_display_position' );
		$this->related_products_option = get_option( 'woocommerce_quick_checkout_related_products' );

		add_action( 'wp', array( $this, 'quick_checkout_shop_init' ), 10, 1 );

	}


	/**
	 * Shop Init
	 *
	 * @description: Hooked after wp so we can use Woo's conditionals
	 *
	 * @return bool
	 */
	function quick_checkout_shop_init() {

		//Sanity Check: Only run on shop page & single product pages if related product option enabled in settings
		if ( ! is_shop() || $this->related_products_option !== 'yes' && is_product() ) {
			return false;
		}

		//Sanity Check:Does user want shop quick cart enabled
		if ( $this->shop_option !== 'yes' ) {
			return false;
		} //endif


		//Display quick cart button
		if ( $this->shop_image_hover == 'yes' ) {
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'quick_checkout_shop_button' ) );
		}

		/**
		 * Shop Action
		 *
		 * Determines where checkout opens on the shop page
		 */
		switch ( $this->shop_action ) {

			//Lightbox option
			case 'lightbox':
				add_action( apply_filters( 'quick_checkout_shop_hook', 'woocommerce_after_shop_loop' ), array(
					WC_Quick_Checkout()->quick_checkout_engine,
					'get_qc_wrap'
				) );
				break;

			//Display on page
			case 'reveal':
				add_action( $this->shop_display_position, array(
					WC_Quick_Checkout()->quick_checkout_engine,
					'get_qc_wrap'
				) );
				break;
		}

	}

	/**
	 * Shop Button
	 *
	 * @description: Output for the shop Quick Checkout button
	 */
	public function quick_checkout_shop_button() {

		global $product;

		$single_disable_option = get_post_meta( $product->id, 'qc_disable_checkbox', true );
		$gravity_form_enabled  = get_post_meta( $product->id, '_gravity_form_data', true );

		//output on shop page if product meets reqs
		if ( isset( $product->product_type ) && $product->product_type == 'simple' && $single_disable_option !== 'on' && ! is_cart() && empty( $gravity_form_enabled ) && $product->stock_status === 'instock' ) {

			echo '<a href="#" class="quick-checkout quick-checkout-button quick-checkout-button-shop quick-checkout-button-image-overlay" data-product_id="' . esc_attr( $product->id ) . '" data-product_sku="' . esc_attr( $product->get_sku() ) . '" data-checkout_action="' . $this->shop_action . '">' . $this->shop_button_text . '</a>';

		} //endif

	}


}