<?php

/**
 *  Quick_Checkout_Engine Class
 *
 * @description: The core checkout functionality of the plugin resides within
 *
 */
class Quick_Checkout_Engine {


	/**
	 * Quick_Checkout_Engine constructor.
	 */
	public function __construct() {

		//QC AJAX
		add_action( 'wp_ajax_wqc_add_to_cart', array(
			$this,
			'quick_checkout_ajax'
		) );
		add_action( 'wp_ajax_nopriv_wqc_add_to_cart', array(
			$this,
			'quick_checkout_ajax'
		) );

		//Handle Redirects
		add_action( 'template_redirect', array( $this, 'template_redirect' ), 1 );

		//Load WC payment gateways
		add_action( 'template_redirect', array( $this, 'load_payment_gateways' ), 1 );

		//QC Checkout Template
		add_filter( 'template_include', array( $this, 'checkout_template_include' ), 1, 1 );

		//Dequeue Scripts in Frame
		add_action( 'get_header', array( $this, 'remove_admin_login_header' ) );

		//Modify Amazon Gateway banner
		add_action( 'woocommerce_before_checkout_form', array( $this, 'modify_amazon_gateway_text' ), 1 );
		add_action( 'before_woocommerce_pay', array( $this, 'modify_amazon_gateway_text' ), 1 );

		//Enqueue Scripts/Styles
		add_action( 'wp_enqueue_scripts', array( $this, 'quick_checkout_enqueue' ), 15 );

	}

	/**
	 * Get the Quick Checkout
	 *
	 * @description: Adds QC tags to the page which load the iframe
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_qc_wrap( $id = 0 ) {

		global $post;

		//Set ID properly for single product CPT
		if ( $post->post_type == 'product' && $id == 0 ) {
			$id = $post->ID;
		}

		if ( is_shop() ) {
			$id = 'shop';
		}

		//Main wrap w/ filter to allow for user modification
		$open_tag = apply_filters( 'quick_checkout_opening_tag', '<div id="quick-checkout-' . $id . '">' );

		//Closing tag w/ filter to allow for user modification
		$closing_tag = apply_filters( 'quick_checkout_closing_tag', '</div>' );

		$tags = $open_tag . $closing_tag;

		if ( $post->post_type == 'product' ) {
			echo $tags;
		}

		return $tags;

	}

	/**
	 * Checkout Template Include
	 *
	 * @param $template
	 *
	 * @return string
	 */
	public function checkout_template_include( $template ) {

		if ( isset( $_GET['qc_loader'] ) ) {

			$this->process_quick_checkout();

			return apply_filters( 'qc_checkout_template_path', $template );

		}

		return $template;

	}

	/**
	 * Process Quick Checkout
	 *
	 * @since 1.9
	 */
	public function process_quick_checkout() {

		//No admin bar
		add_filter( 'show_admin_bar', '__return_false' );

		//Add CSS Class
		add_filter( 'body_class', array( $this, 'qc_body_class' ) );
		add_action( 'wp_head', array( $this, 'qc_frame_head' ) );

	}

	/**
	 * Quick Checkout AJAX
	 *
	 * @since 1.9
	 */
	public function quick_checkout_ajax() {

		//Setup datac
		$data = isset( $_POST['data'] ) ? $_POST['data'] : array();

		//Check for other data
		if ( empty( $data ) ) {
			$data = isset( $_POST ) ? $_POST : array();
		}

		//Sanity check
		if ( empty( $data ) ) {
			return false;
		}

		//Setup sessions for new customers
		$session_data = WC()->session->get_session_data();
		if ( empty( $session_data ) ) {
			WC()->session->set_customer_session_cookie( true );
		}

		$clear_cart   = isset( $data['clear_cart'] ) ? filter_var( $data['clear_cart'], FILTER_VALIDATE_BOOLEAN ) : false;
		$checkout_now = isset( $data['checkout_now'] ) ? filter_var( $data['checkout_now'], FILTER_VALIDATE_BOOLEAN ) : false;
		$product_id   = isset( $data['product_id'] ) ? intval( $data['product_id'] ) : false;
		$quantity     = isset( $data['quantity'] ) ? intval( $data['quantity'] ) : 1;

		//Variation ID?
		$variation_id = isset( $data['variation_id'] ) ? intval( $data['variation_id'] ) : 0;

		//Variations?
		$variations = isset( $data['variation'] ) ? $data['variation'] : array();

		//Shortcode 'variation_attr_*' Support
		if ( empty( $variations ) ) {

			$counter = 1;
			//Reverse array order so variations appear in proper order
			$data = array_reverse( $data );
			//Loop through data array
			foreach ( $data as $key => $value ) {
				//Sanity check for $atts (skip if no match)
				if ( strpos( $key, 'variation_attr_' ) !== 0 ) {
					continue;
				}
				//Handle `attribute_pa` vs `attribute_`
				if ( $counter == 1 ) {
					$new_key = str_replace( 'variation_attr_', 'attribute_pa_', $key );
				} else {
					$new_key = str_replace( 'variation_attr_', 'attribute_', $key );
				}
				//Set value
				$variations[ $new_key ] = $value;
				$counter ++;
			}

		}

		//Checkout now: Only open the checkout, bail
		if ( ! empty( $checkout_now ) ) {
			return false;
		}

		//Sanity check: product_id & quantity
		if ( empty( $product_id ) && empty( $quantity ) ) {
			return false;
		}

		//Clear cart option
		if ( $clear_cart == true ) {
			WC()->cart->empty_cart();
		}

		//Add SIMPLE products to cart (NO variations)
		if ( empty( $variation_id ) && WC()->cart->add_to_cart( $product_id, $quantity ) ) {
			do_action( 'woocommerce_ajax_added_to_cart', $product_id );
			// Return fragments
			WC_AJAX::get_refreshed_fragments();

		} //Add Variation Products (non-simple)
		elseif ( ! empty( $variation_id ) &&
		         ! empty( $variations ) &&
		         WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations )
		) {
			do_action( 'woocommerce_ajax_added_to_cart', $product_id );
			// Return fragments
			WC_AJAX::get_refreshed_fragments();
		}

		//Cart should NOT be empty at this point
		if ( WC()->cart->is_empty() ) {
			echo 'empty_cart';
			wp_die();
		} else {
			//All good proceed
			return true;
		}

	}

	/**
	 * Set URL Data Attributes
	 *
	 * @description: Helper Function
	 * @since      1.9
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	function set_url_data_attributes( $atts ) {
		$data_atts = '';
		foreach ( $atts as $key => $value ) {
			//Sanity check for $atts
			if ( empty( $value ) || $key == 'checkout_text' ) {
				continue;
			}
			//Setup data="value" for qc anchor tag below
			$data_atts .= 'data-' . $key . '="' . urlencode( $value ) . '" ';
		}

		return $data_atts;

	}

	/**
	 * Body Class
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	function qc_body_class( $classes ) {
		$classes[] = 'quick-checkout-frame';

		return apply_filters( 'qc_frame_body_class_output', $classes );
	}

	/**
	 * iFrame Header
	 *
	 * @description: Adds relevant tags to open links in the parent window
	 *
	 * @return string
	 */
	function qc_frame_head() {

		$output = '<base target="_parent" />';
		echo $output;
	}

	/**
	 * Remove Admin Login header
	 */
	function remove_admin_login_header() {
		//Sanity Check
		if ( isset( $_GET['qc_loader'] ) ) {
			remove_action( 'wp_head', '_admin_bar_bump_cb' );
		}
	}

	/**
	 * Quick Checkout Error Message
	 *
	 * @desc: Handles outputting error messages on the frontend in a Woo style way
	 *
	 * @param $message
	 * @param $type
	 *
	 * @return string
	 */
	public function quick_checkout_error( $message, $type ) {

		switch ( $type ) {
			case 'error':
				$message = '<div class="woocommerce-error"><strong>' . esc_html__( 'Quick Checkout Error', 'wqc' ) . '</strong>: ' . $message . '</div>';
				break;
			default :
				$message = '<div class="woocommerce-info">' . $message . '</div>';
		}

		return apply_filters( 'quick_checkout_error_message', $message, $type );

	}

	/**
	 * Template Redirect
	 *
	 * @description: Prevents Woo from redirecting back to cart when in the qc iFrame
	 *
	 * @see: Quick_Checkout_Engine->load_payment_gateways()
	 */
	public function template_redirect() {
		if ( isset( $_GET['qc_loader'] ) ) {
			remove_action( 'template_redirect', 'wc_template_redirect' );
		}
	}

	/**
	 * Load Payment Gateways
	 *
	 * @description: Ensures Woo payment gateways are loaded early enough.
	 *
	 * By disabling the `wc_template_redirect` hook, we prevent Woo from setting up its
	 * payment gateways early enough; this prevents some gateways, like Stripe, from loading
	 * their JS, as the `wp_enqueue_scripts` hook has already fired by the time they're
	 * finally loaded.
	 */
	public function load_payment_gateways() {
		if ( isset( $_GET['qc_loader'] ) ) {
			WC()->payment_gateways();
		}
	}

	/**
	 * Modify Pay with Amazon banner
	 *
	 * @description: If within the QC iframe, remove the default Pay with Amazon banner, replacing it with our own.
	 */
	public function modify_amazon_gateway_text() {
		if ( isset( $_GET['qc_loader'], $GLOBALS['wc_amazon_payments_advanced'] ) ) {
			$amazon_instance = $GLOBALS['wc_amazon_payments_advanced'];

			// Remove the normal Amazon Gateway message
			remove_action( 'woocommerce_before_checkout_form', array( $amazon_instance, 'checkout_message' ), 5 );
			remove_action( 'before_woocommerce_pay', array( $amazon_instance, 'checkout_message' ), 5 );

			// Add our own Amazon gateway message
			add_action( 'woocommerce_before_checkout_form', array( $this, 'pay_with_amazon_banner' ), 5 );
			add_action( 'before_woocommerce_pay', array( $this, 'pay_with_amazon_banner' ), 5 );
		}
	}

	/**
	 * Pay with Amazon Banner
	 *
	 * @description: Provides a link to the normal checkout page, for users wanting to check out with Amazon.
	 */
	public function pay_with_amazon_banner() {
		?>
		<div class="wc-qc-amazon-checkout-message">
			<div class="woocommerce-info info">
				<?php echo apply_filters( 'qc_amazon_modal_checkout_message', sprintf( __( 'Have an Amazon account? <a href="%s" target="_parent">Go to the full checkout page to pay with Amazon.</a>', 'wqc' ), wc_get_page_permalink( 'checkout' ) ) ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue Scripts / Styles
	 *
	 * @description: Used to enqueue scripts in on the frontend
	 *
	 * @return mixed;
	 */
	function quick_checkout_enqueue() {

		global $woocommerce;

		//first, let's define which pages to not enqueue checkout scripts on
		if ( is_cart() || is_account_page() ) {
			return false;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		//if debugging enqueue non-min assets
		if ( SCRIPT_DEBUG == true ) {

			//Magnific Popup JS
			wp_register_script( 'quick_checkout_magnific_popup_js', WQC_PLUGIN_URL . '/assets/js/jquery.magnific.popup' . $suffix . '.js', array(
				'jquery',
				'woocommerce'
			), uniqid(), true );
			wp_enqueue_script( 'quick_checkout_magnific_popup_js' );

			//Magnific Popup CSS
			wp_register_style( 'quick_checkout_magnific_popup_css', WQC_PLUGIN_URL . '/assets/css/magnific-popup.css', array(), uniqid() );
			wp_enqueue_style( 'quick_checkout_magnific_popup_css' );

			//Quick Checkout Scripts
			wp_register_script( 'quick_checkout_scripts', WQC_PLUGIN_URL . '/assets/js/quick-checkout-ajax.js', array(
				'jquery',
				'woocommerce'
			), uniqid(), true );
			wp_enqueue_script( 'quick_checkout_scripts' );

		} else {

			//One JS file to rule them all
			wp_register_script( 'quick_checkout_scripts', WQC_PLUGIN_URL . '/assets/js/quick-checkout.min.js', array(
				'jquery',
				'woocommerce'
			), WQC_PLUGIN_VERSION, true );
			wp_enqueue_script( 'quick_checkout_scripts' );

		}

		//Enqueue iFrame scripts
		$this->qc_iframe_scripts();

		//Quick Checkout CSS
		wp_register_style( 'quick_checkout_css', WQC_PLUGIN_URL . '/assets/css/quick-checkout' . $suffix . '.css', array(), WQC_PLUGIN_VERSION );
		wp_enqueue_style( 'quick_checkout_css' );

		//Quick Checkout AJAX params
		// in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
		wp_localize_script(
			'quick_checkout_scripts', 'quick_checkout',
			array(
				'ajax_url'                => WC()->ajax_url(),
				'wp_debug'                => ( defined( 'WP_DEBUG' ) && WP_DEBUG == true ),
				'script_debug'            => ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true ),
				'checkout_url'            => $woocommerce->cart->get_checkout_url() ? $woocommerce->cart->get_checkout_url() : 'checkout_not_set',
				'shop_on'                 => WC_Quick_Checkout()->quick_checkout_shop->shop_option,
				'shop_checkout_action'    => WC_Quick_Checkout()->quick_checkout_shop->shop_action,
				'shop_clear_cart'         => WC_Quick_Checkout()->quick_checkout_shop->shop_clear_cart,
				'shop_cart_reveal'        => WC_Quick_Checkout()->quick_checkout_shop->shop_cart_reveal,
				'shop_cart_reveal_text'   => WC_Quick_Checkout()->quick_checkout_shop->shop_cart_reveal_text,
				'product_on'              => WC_Quick_Checkout()->quick_checkout_product->product_option,
				'product_checkout_action' => WC_Quick_Checkout()->quick_checkout_product->product_action,
				'product_button_display'  => WC_Quick_Checkout()->quick_checkout_product->product_button_display,
				'product_clear_cart'      => WC_Quick_Checkout()->quick_checkout_product->product_clear_cart,
				'product_type'            => WC_Quick_Checkout()->quick_checkout_product->product_type,
				'woocommerce_is_cart'     => is_cart(),
				'woocommerce_is_checkout' => is_checkout(),
				'woocommerce_is_shop'     => is_shop(),
				'i18n'                    => array(
					'cart_error'       => esc_attr__( 'There was an error adding this product to the cart.', 'wqc' ),
					'checkout_not_set' => esc_attr__( 'No checkout page has been set. Please contact the site administrator.', 'wqc' ),
				)
			)
		);

	}


	/**
	 * QC Iframe Scripts
	 *
	 * @description: Responsible for outputting the appropriate iFrameResizer scripts on the checkout page and the non-checkout pages
	 */
	public function qc_iframe_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		//QC Iframe Scripts
		if ( is_checkout() && isset( $_GET['qc_loader'] ) ) {

			add_action( 'wp_footer', array( $this, 'print_iframe_script' ) );

			//Checkout Iframe Resizer Content
			wp_register_script( 'quick_checkout_iframe_checkout_resizer', WQC_PLUGIN_URL . '/assets/js/iframeResizer.contentWindow' . $suffix . '.js', array(
				'jquery',
				'woocommerce',
				'quick_checkout_scripts'
			), false, true );
			wp_enqueue_script( 'quick_checkout_iframe_checkout_resizer' );


		} else {

			//iFrame Parent
			wp_register_script( 'quick_checkout_iframe_resizer', WQC_PLUGIN_URL . '/assets/js/iframeResizer' . $suffix . '.js', array(
				'jquery',
				'woocommerce'
			), false, true );
			wp_enqueue_script( 'quick_checkout_iframe_resizer' );

		}
	}

	/**
	 * Checkout iFrame Scripts
	 *
	 * @description: Prints inline JS on only the QC checkout page in the footer
	 *
	 * @since      1.9
	 */
	public function print_iframe_script() { ?>

		<script>

			(function ($) {

				/**
				 * When AJAX is successful in QC checkout
				 */
				jQuery(document).ajaxSuccess(function (event, xhr, settings, response) {
					wqc_checkout_iframe(response);
				});

				/**
				 * Sends response info to parent page of QC iframe
				 */
				function wqc_checkout_iframe(response) {

					var counter = 0;

					if (!('parentIFrame' in window)) {
						console.log('No parentIFrame object yet...');
						if(counter > 100) {
							console.log('Failed to reach parent iFrame');
							return false;
						}
						setTimeout(function () {
							counter++;
							wqc_checkout_iframe(response);
						}, 50);

					} else {
						//Send response to iFrame Parent
						window.parentIFrame.sendMessage(response);
						//Redirect parent when successful checkout
						if (response.result === 'success' && response.redirect) {
							if (-1 === response.redirect.indexOf('https://') || -1 === response.redirect.indexOf('http://')) {
								parent.location = response.redirect;
							} else {
								parent.location = decodeURI(response.redirect);
							}
						}
					}


				}


			})(jQuery);


		</script>


	<?php }


}