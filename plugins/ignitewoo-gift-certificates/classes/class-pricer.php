<?php 
/* Copyright (c) 2013-2015 IgniteWoo.com - All Rights Reserved */
/* Copyright (c) 2012 Kathy Darling. */

if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );
	
if ( ! class_exists( 'ign_gc_pricer' ) ) :

	class ign_gc_pricer {

		var $plugin_path;

		public function __construct() {
			global $woocommerce;

			$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
			
			$this->css_file = plugins_url( '/../assets/css/gift-cert-price.css', __FILE__ );

			// Setup Product Data
			add_action( 'the_post', array( &$this, 'setup_product' ), 25 );

			// CSS
			add_action( 'wp_enqueue_scripts', array( &$this, 'style' ), 9999 );
			
			// Single Product Display
			add_action( 'wp_head', array( &$this, 'gcp_templates' ) );
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( &$this, 'add_to_cart_text' ), 20 );

			// WC 2.1.x 
			add_filter( 'woocommerce_loop_add_to_cart_link', array( &$this, 'loop_add_to_cart_link' ), 99 );
			
			// Loop Display
			add_filter( 'woocommerce_get_price_html', array( &$this, 'filter_suggested_price'), 10, 2 );
			add_filter( 'woocommerce_product_add_to_cart_text', array( &$this, 'add_to_cart_text' ), 20 );
			add_filter( 'woocommerce_add_to_cart_url', array( &$this, 'add_to_cart_url' ) );
			
			// Functions for cart actions - ensure they have a priority before addons (10)
			add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 5, 2 );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 5, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 5, 2 );
			add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 5, 1 );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 5, 3 );
			
			// Settings Link for Plugin page
			add_filter( 'plugin_action_links', array( &$this, 'add_action_link' ), 9, 2);

		}

		
		function add_error( $message, $type = 'error' ) { 
			global $woocommerce;
			
			if ( function_exists( 'wc_add_notice' ) )
				wc_add_notice( $message, $type );
			else 
				$woocommerce->add_error( $message );
				
			
		
		}
		
		function style(){
			global $product, $post;

			if ( ! is_product() || !get_post_meta( $post->ID, 'ignite_gift_enabled', true ) )
				return;

			if ( !get_post_meta( $post->ID, 'ignite_buyer_sets_price', true ) )
				return;
			
			wp_dequeue_style( 'name-your-price' );

			wp_enqueue_style( 'set-your-amount', $this->css_file );
			
		}
		
		/*-----------------------------------------------------------------------------------*/
		/* Add to the global $product object */
		/*-----------------------------------------------------------------------------------*/

		/**
		 * setup product
		 * @since 1.0
		 */
		function setup_product(){
			global $product;

			if ( ! $product || ! is_object( $product ) )
				return;

			$val = $this->is_gcp( $product->id );

			// add our data to the $product variable
			$product->gift_cert = ( $val ) ? $val['gcp'] : FALSE;
			$product->gcp = ( $val ) ? $val['gcp'] : FALSE;
			$product->suggested = ( $val ) ? $val['suggested'] : FALSE;
			$product->minimum = ( $val ) ? $val['minimum'] : FALSE;
			$product->label_text = ( $val ) ? __( $val['label_text'], 'ignite_gift_certs' ) : FALSE;
			$product->minimum_text = ( $val ) ? $val['minimum_text'] : FALSE;

		}


		/**
		 * Display the suggested and minimum prices on single products
		 * @since 1.0
		 */
		public function gcp_templates() {
			global $post;

			// if not a singular product quit right now
			if ( ! is_product() && ( !isset( $post->opc_gift_cert ) || false == $post->opc_gift_cert ) )
				return;

			// if not a gcp product quit right now
			if ( ! ( $attributes = $this->is_gcp( $post->ID ) ) ) 
				return;

			// Product does not exist yet so load values from is_gcp array
			extract( $attributes );

			// if min price set, display min price template
			if ( $minimum ) {
				add_action( 'woocommerce_single_product_summary', array( &$this, 'display_minimum_price'), 15 );
			}

			add_action( 'woocommerce_before_add_to_cart_button', array( &$this, 'display_price_input' ) );

		}



		/**
		 * Call the Minimum Price Template
		 * @since 1.0
		 */
		function display_minimum_price(){
		
			$template = locate_template( array( '/../templates/minimum-price.php' ), false, false );
			
			if ( '' != $template ) 
				require ( $template );
			else 
				require( dirname( __FILE__ ) . '/../templates/minimum-price.php' );

		}

		/**
		 * Call the Price Input Template
		 * @since 1.0
		 */
		function display_price_input(){
			$template = locate_template( array( '/../templates/price-input.php' ), false, false );

			if ( '' != $template ) 
				require ( $template );
			else 
				require( dirname( __FILE__ ) . '/../templates/price-input.php' );

		}

		/*
		 * Price Formatting Helper
		 * similar to woocommerce_price() but returns a text input instead with formatted number
		 * @since 1.0
		 */
		public function price_input_helper( $price ) {
		    global $woocommerce, $product;

			$num_decimals = ( int ) get_option( 'woocommerce_price_num_decimals' );
			$currency_pos = get_option( 'woocommerce_currency_pos' );
			$currency_symbol = get_woocommerce_currency_symbol();

			if ( '' != $price ) {

				$price = apply_filters( 'raw_woocommerce_price', ( double ) $price );

					$price = number_format( $price, $num_decimals, stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ), stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) );

				if ( 'yes' == get_option( 'woocommerce_price_trim_zeros' ) && $num_decimals > 0 )
					$price = woocommerce_trim_zeros( $price);
			}

			$input = sprintf( '<input id="gcp" name="gcp" value="%s" size="6" title="gcp" class="input-text amount gcp text" />', $price );

			$input .= '<input type="hidden" name="yith-wacp-is-excluded" />';
			
			return $input;
		}

		/*-----------------------------------------------------------------------------------*/
		/* Loop Display Functions */
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Filter the Suggested Price
		 * @since 1.0
		 */
		function filter_suggested_price( $price, $product ){

			if( ! $product->gcp )
				return $price;

			// Hide the Suggested Price on archive pages and on single products if not set :: @since 1.1.2
			if ( is_shop() || is_product_category() || is_product_tag() || ( is_product() && !$product->suggested ) ) {

				$price = FALSE;

			} elseif ( ( is_product() || ( isset( $product->opc_gift_cert ) && true === $product->opc_gift_cert ) ) && $product->suggested ) {

				$suggested = woocommerce_price( $product->suggested );

				$price = sprintf( _x( '%s: %s', 'In case you need to change the order of Suggested Price: $suggested', 'ignitewoo_gift_certs', 'ignitewoo_gift_certs' ), get_option( 'woocommerce_gcp_suggested_text', __('Suggested Amount', 'ignitewoo_gift_certs' ) ), $suggested );
			}

			return $price;
		}

		/*
		 * if NYP change the loop's add to cart button text
		 * @since 1.0
		 */
		public function add_to_cart_text( $text ) {
			global $product,  $ignite_gift_certs;

			if ( empty( $product->gcp ) ) return $text;

			if ( is_archive() ) {
				$product->product_type = 'gcp';
				$text = isset( $ignite_gift_certs->admin_settings['text_single'] ) ? __( $ignite_gift_certs->admin_settings['text_single'], 'ignitewoo_gift_certs' ) : '';
			} elseif ( is_product() ) {
				$text = isset( $ignite_gift_certs->admin_settings['text_single'] ) ? __( $ignite_gift_certs->admin_settings['text_single'], 'ignitewoo_gift_certs' ) : '';
			}

			return $text;
		}
		
		function loop_add_to_cart_link( $link = '' ) { 
			global $product;
			
			if ( empty( $link ) )
				return $link;
				
			if ( !$this->is_gcp( $product->id ) )
				return $link;
			
			if ( !get_post_meta( $product->id, 'ignite_gift_enabled', true ) )
				return $link;

			if ( !get_post_meta( $product->id, 'ignite_buyer_sets_price', true ) )
				return $link;
				
			return sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>',
			esc_url( get_permalink( $product->id ) ),
			esc_attr( $product->id ),
			esc_attr( $product->get_sku() ),
			$product->is_purchasable() ? 'view_product' : '',
			esc_attr( $product->product_type ),
			esc_html( get_option( 'woocommerce_gcp_button_text', __( 'Set Amount', 'ignitewoo_gift_certs' ) ) )
			);
		
		}

		/*
		 * if NYP change the loop's add to cart button URL
		 * disable ajax add to cart and redirect to product page
		 * @since 1.0
		 */
		public function add_to_cart_url( $url ) {
			global $product;

			if ( ! is_single( $product->id ) && $this->is_gcp( $product->id ) ) {
					$product->product_type = 'gcp';
					$url = get_permalink( $product->id );
			}

			return $url;
		}

		/*-----------------------------------------------------------------------------------*/
		/* Cart Filters */
		/*-----------------------------------------------------------------------------------*/

		/*
		 * override woo's is_purchasable in cases of gcp products
		 * @since 1.0
		 */
		public function is_purchasable( $purchasable , $product ) {
			if( $this->is_gcp( $product->id) ) {
				$purchasable = true;
			}
			return $purchasable;
		}

		/*
		 * add cart session data
		 * @since 1.0
		 */
		public function add_cart_item_data( $cart_item_meta, $product_id ) {
			global $woocommerce;
//echo 'item data'; var_dump( $_POST ); die;
			//no need to check is_gcp b/c this has already been validated by validate_add_cart_item()
			if( isset( $_POST['gcp'] ) ) {
				$num_decimals = get_option ( 'woocommerce_price_num_decimals' );
				$cart_item_meta['gcp'] = round( floatval( $_POST['gcp'] ), $num_decimals );
			}

			return $cart_item_meta;
		}

		/*
		 * adjust the product based on cart session data
		 * @since 1.0
		 */
		function get_cart_item_from_session( $cart_item, $values ) {
//echo 'sessions'; var_dump( $values ); die;
			//no need to check is_gcp b/c this has already been validated by validate_add_cart_item()
			if ( isset( $values['gcp'] ) ) {
				$num_decimals = get_option ( 'woocommerce_price_num_decimals' );
				$cart_item['gcp'] = round( floatval( $values['gcp'] ), $num_decimals );
				$cart_item = $this->add_cart_item( $cart_item );
			}
			return $cart_item;
		}

		/*
		 * change the price of the item in the cart
		 * @since 1.0
		 */
		public function add_cart_item( $cart_item ) {
//echo 'add item';die;
			// Adjust price in cart if gcp is set
			if ( $this->is_gcp( $cart_item['data']->id ) && isset( $cart_item['gcp'] ) ) {
				$cart_item['data']->price = $cart_item['gcp'];
				$cart_item['data']->sale_price =  $cart_item['gcp'];
				$cart_item['data']->regular_price = $cart_item['gcp'];
			}

			return $cart_item;
		}

		/*
		 * check this is a NYP product before adding to cart
		 * @since 1.0
		 */
		public function validate_add_cart_item( $passed, $product_id, $qty ) {
			global $woocommerce;

			// skip if not a gcp product (or no gcp is in the $_POST) - send original status back

			$val = $this->is_gcp( $product_id );

			if ( ! $val )
				return $passed;

			// set a null string to 0
			if ( empty( $_POST['gcp'] ) )
				$_POST['gcp'] = 0;

			$tsep = get_option( 'woocommerce_price_thousand_sep', '' );
			$dsep = get_option( 'woocommerce_price_decimal_sep', '' );
			
			// find the decimal sep and convert to dot
			$temp_price = str_replace( $dsep, '.', $_POST['gcp'] );
			// remove thousand sep 
			$temp_price = str_replace( $tsep, '', $temp_price );
			
			// check that it is a numeric value
			/*
			if ( ! is_numeric( $_POST['gcp'] ) ) {
				$passed = false;
				$this->add_error( __( 'Please enter a valid number.', 'ignitewoo_gift_certs' ) );
			// check that it is not negative
			} elseif ( floatval( $_POST['gcp'] ) < 0 ) {
				$passed = false;
				$this->add_error( sprintf(__( 'You cannot enter a negative value.', 'ignitewoo_gift_certs' ), woocommerce_price( $val['minimum'] ) ) );
			// check that it is greater than minimum price
			} elseif ( ! empty( $val[ 'minimum'] ) && floatval( $_POST['gcp'] ) < floatval( $val[ 'minimum'] ) ) {
				$passed = false;
				$this->add_error( sprintf(__( 'Please enter at least %s.', 'ignitewoo_gift_certs' ), woocommerce_price( $val['minimum'] ) ) );
			}
			*/
			if ( ! is_numeric( $temp_price ) ) {
				$passed = false;
				$this->add_error( __( 'Please enter a valid number.', 'ignitewoo_gift_certs' ) );
			// check that it is not negative
			} elseif ( floatval( $temp_price ) < 0 ) {
				$passed = false;
				$this->add_error( sprintf(__( 'You cannot enter a negative value.', 'ignitewoo_gift_certs' ), woocommerce_price( $val['minimum'] ) ) );
			// check that it is greater than minimum price
			} elseif ( ! empty( $val[ 'minimum'] ) && floatval( $temp_price ) < floatval( $val[ 'minimum'] ) ) {
				$passed = false;
				$this->add_error( sprintf(__( 'Please enter at least %s.', 'ignitewoo_gift_certs' ), woocommerce_price( $val['minimum'] ) ) );
			}
			return $passed;
		}


		/*
		 * 'Settings' link on plugin page
		 * @since 1.0
		 */

		public function add_action_link( $links, $file ) {

		    if ( $file == plugin_basename( __FILE__ ) ) {
		      $settings_link = '<a href="'.admin_url('admin.php?page=woocommerce&tab=gcp').'" title="'.__('Go to the settings page', 'ignitewoo_gift_certs').'">'.__('Settings', 'wc_shipworks').'</a>';
				      // make the 'Settings' link appear first
		      array_unshift( $links, $settings_link );
		    }

		    return $links;
		  }



	    /*-----------------------------------------------------------------------------------*/
		/* Helper Functions */
		/*-----------------------------------------------------------------------------------*/

		/*
		 * Verify this is a Name Your Price product
		 *
		 * right now only available on simple products and subscriptions
		 *
		 * @since 	1.0
		 * @access 	public
		 * @return 	return array() or FALSE
		 */

		public function is_gcp( $id ){

			if ( has_term( array( 'simple' ), 'product_type', $id ) && get_post_meta( $id , 'ignite_buyer_sets_price', true ) && get_post_meta( $id, 'ignite_gift_enabled', true ) ) {

				$num_decimals = ( int ) get_option( 'woocommerce_price_num_decimals' );
				
				$price = round( floatval( ( isset( $_POST['gcp'] ) ? $_POST['gcp'] : '' ) ), $num_decimals );

				$suggested = get_post_meta( $id , 'ignite_suggested_price', true ) ? round( floatval( get_post_meta( $id , 'ignite_suggested_price', true ) ), $num_decimals ) : FALSE;

				// filter the raw suggested price @since 1.2
				$suggested = apply_filters ( 'woocommerce_raw_suggested_price', $suggested, $id );

				$minimum = get_post_meta( $id , 'ignite_min_price', true ) ? round( floatval( get_post_meta( $id , 'ignite_min_price', true ) ), $num_decimals ) : FALSE;

				// filter the raw minimum price @since 1.2
				$minimum = apply_filters ( 'woocommerce_raw_minimum_price', $minimum, $id );

				$opts = get_option( 'woocommerce_woocommerce_gift_certificates_settings', array() );

				return array (
						'gcp' => TRUE,
						'suggested' => $suggested,
						'minimum' => $minimum,
						'label_text' => isset( $opts['label_text'] ) ? __( $opts['label_text'] , 'ignite_gift_certs' ) : '',
						'minimum_text' => isset( $opts['minimum_text'] ) ? __( $opts['minimum_text'], 'ignite_gift_certs' ) : '',
					) ;
					
			} else {
			
				return FALSE;
				
			}
		}

	} //end class: do not remove or there will be no more guacamole for you

endif; // end class_exists check


