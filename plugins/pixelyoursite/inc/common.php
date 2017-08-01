<?php
/**
 * Common functions for both versions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Check if WooCommerce plugin is installed and activated.
 */
if( !function_exists( 'pys_is_woocommerce_active' ) ) {

	function pys_is_woocommerce_active() {
		return class_exists( 'WooCommerce' ) == true ? true : false;
	}

}

/**
 * Check if Easy Digital Downloads plugin is installed and activated.
 */
if( !function_exists( 'pys_is_edd_active' ) ) {

	function pys_is_edd_active() {
		return class_exists( 'Easy_Digital_Downloads' ) == true ? true : false;
	}

}

if ( ! function_exists( 'pys_is_pcf_pro_active' ) ) {

	function pys_is_pcf_pro_active() {
		return class_exists( 'wpwoof_product_catalog' ) == true ? true : false;
	}

}

if( !function_exists( 'pys_get_option' ) ) {

	/**
	 * Return option value.
	 *
	 * @param string $section Options section, eg. 'general', 'woo', etc.
	 * @param string $option Option name
	 * @param mixed|string $default Default option value
	 *
	 * @return mixed Option or default option value
	 */
	function pys_get_option( $section, $option, $default = '' ) {

		$options = get_option( 'pixel_your_site' );

		return isset( $options[ $section ][ $option ] ) ? $options[ $section ][ $option ] : $default;

	}

}

if( !function_exists( 'pys_checkbox_state' ) ) {

    /**
     * @param string $section Option section name
     * @param string $option  Option name
     * @param bool   $echo    Echo or return value
     * @param int    $checked Value to state compare
     *
     * @return string
     */
	function pys_checkbox_state( $section, $option, $echo = true, $checked = 1 ) {

		$options = get_option( 'pixel_your_site' );

		if ( isset( $options[ $section ][ $option ] ) ) {
			$value = $options[ $section ][ $option ] == $checked ? 'checked' : '';
		} else {
			$value = '';
		}

		if( $echo ) {
			echo $value;
		}

		return $value;

	}

}

if ( ! function_exists( 'pys_checkbox' ) ) {
    
    /**
     * Echos checkbox input.
     *
     * @param string $section Input section name
     * @param string $option  Input option name
     * @param string $classes Class names (optional)
     * @param int    $value   Input value (optional)
     */
    function pys_checkbox( $section, $option, $classes = '', $value = 1 ) {
        
        $state = pys_checkbox_state( $section, $option, false, $value );
        $value = esc_attr( $value );
        
        echo "<input type='checkbox' value='$value' name='pys[$section][$option]' class='$classes' $state>";
        
    }
    
}

if ( ! function_exists( 'pys_text_field' ) ) {

	/**
	 * Echos text input.
	 *
	 * @param string $section Input section name
	 * @param string $option  Input option name
	 */
	function pys_text_field( $section, $option ) {

		$value = pys_get_option( $section, $option );
		$value = esc_attr( $value );

		echo "<input type='text' value='$value' name='pys[$section][$option]'>";

	}

}

if ( ! function_exists( 'pys_radio_box' ) ) {
	
	/**
	 * Echos radio box input.
	 *
	 * @param string $section Input section name
	 * @param string $option  Input option name
	 * @param string $value   Option value
	 */
	function pys_radio_box( $section, $option, $value ) {
		
		$state = pys_radio_state( $section, $option, $value );
		echo "<input type='radio' value='$value' name='pys[$section][$option]' $state>";
		
	}
	
}

/**
 * Return radio box state.
 */
if( !function_exists( 'pys_radio_state' ) ) {

	function pys_radio_state( $section, $option, $value ) {

		$options = get_option( 'pixel_your_site' );

		if ( isset( $options[ $section ][ $option ] ) ) {

			return $options[ $section ][ $option ] == $value ? 'checked' : '';

		} else {

			$defaults = pys_get_default_options();
			return $defaults[ $section ][ $option ] == $value ? 'checked' : '';

		}

	}

}

/**
 * Facebook Pixel Event types options html.
 */
if( !function_exists( 'pys_event_types_select_options' ) ) {

	function pys_event_types_select_options( $current = null, $full = true ) {
		?>

		<option <?php selected( null, $current ); ?> disabled>Select Type</option>
		<option <?php selected( 'ViewContent', $current ); ?> value="ViewContent">ViewContent</option>

		<?php if ( $full ) : ?>

		<option <?php selected( 'Search', $current ); ?> value="Search">Search</option>

		<?php endif; ?>

		<option <?php selected( 'AddToCart', $current ); ?> value="AddToCart">AddToCart</option>
		<option <?php selected( 'AddToWishlist', $current ); ?> value="AddToWishlist">AddToWishlist</option>
		<option <?php selected( 'InitiateCheckout', $current ); ?> value="InitiateCheckout">InitiateCheckout</option>
		<option <?php selected( 'AddPaymentInfo', $current ); ?> value="AddPaymentInfo">AddPaymentInfo</option>
		<option <?php selected( 'Purchase', $current ); ?> value="Purchase">Purchase</option>
		<option <?php selected( 'Lead', $current ); ?> value="Lead">Lead</option>
		<option <?php selected( 'CompleteRegistration', $current ); ?> value="CompleteRegistration">CompleteRegistration</option>

		<?php if ( $full ) : ?>

		<option disabled></option>
		<option <?php selected( 'CustomEvent', $current ); ?> value="CustomEvent">Custom event</option>
		<option <?php selected( 'CustomCode', $current); ?> value="CustomCode">Custom event code</option>

		<?php endif; ?>

		<?php
	}

}

/**
 * Current Page Full URL without trailing slash
 */
if( !function_exists( 'pys_get_current_url' ) ) {

	function pys_get_current_url() {

		$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$current_url = rtrim( $current_url, '/' );

		return $current_url;

	}

}

/**
 * Returns relative path without protocol, host, slashes.
 */
if( !function_exists( 'pys_get_relative_path' ) ) {

	function pys_get_relative_path( $url ) {

		$host = str_replace( array( 'http://', 'https://', 'http://www.', 'https://www.', 'www.' ), '', home_url() );

		$url = str_replace( array( 'http://', 'https://', 'http://www.', 'https://www.', 'www.' ), '', $url );
		$url = str_replace( $host, '', $url );

		$url = trim( $url );
		$url = ltrim( $url, '/' );
		$url = rtrim( $url, '/' );

		return $url;

	}

}

/**
 * Check if needle URL (full or relative) matches with current.
 */
if( !function_exists( 'pys_match_url' ) ) {

	function pys_match_url( $match_url, $current_url = '' ) {

		// use current url by default
		if ( ! isset( $current_url ) || empty( $current_url ) ) {
			$current_url = pys_get_current_url();
		}

		$current_url = pys_get_relative_path( $current_url );
		$match_url   = pys_get_relative_path( $match_url );

		if ( substr( $match_url, - 1 ) == '*' ) {
			// if match_url ends with wildcard

			$match_url = rtrim( $match_url, '*' );

			if ( pys_startsWith( $current_url, $match_url ) ) {
				return true;
			}

		} else {
			// exact url

			if ( $current_url == $match_url ) {
				return true;
			}

		}

		return false;

	}

}

if( !function_exists( 'pys_startsWith' ) ) {

	function pys_startsWith( $haystack, $needle ) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos( $haystack, $needle, - strlen( $haystack ) ) !== false;
	}

}

if( !function_exists( 'pys_endsWith' ) ) {

	function pys_endsWith( $haystack, $needle ) {
		// search forward starting from end minus needle length characters
		return $needle === "" || ( ( $temp = strlen( $haystack ) - strlen( $needle ) ) >= 0 && strpos( $haystack, $needle, $temp ) !== false );
	}

}

if( !function_exists( 'pys_currency_options' ) ) {

	function pys_currency_options( $current = 'USD' ) {

		$currencies = apply_filters( 'pys_currencies_list', array(
			'AUD' => 'Australian Dollar',
			'BRL' => 'Brazilian Real',
			'CAD' => 'Canadian Dollar',
			'CZK' => 'Czech Koruna',
			'DKK' => 'Danish Krone',
			'EUR' => 'Euro',
			'HKD' => 'Hong Kong Dollar',
			'HUF' => 'Hungarian Forint',
			'IDR' => 'Indonesian Rupiah',
			'ILS' => 'Israeli New Sheqel',
			'JPY' => 'Japanese Yen',
			'KRW' => 'Korean Won',
			'MYR' => 'Malaysian Ringgit',
			'MXN' => 'Mexican Peso',
			'NOK' => 'Norwegian Krone',
			'NZD' => 'New Zealand Dollar',
			'PHP' => 'Philippine Peso',
			'PLN' => 'Polish Zloty',
			'RON' => 'Romanian Leu',
			'GBP' => 'Pound Sterling',
			'SGD' => 'Singapore Dollar',
			'SEK' => 'Swedish Krona',
			'CHF' => 'Swiss Franc',
			'TWD' => 'Taiwan New Dollar',
			'THB' => 'Thai Baht',
			'TRY' => 'Turkish Lira',
			'USD' => 'U.S. Dollar',
			'ZAR' => 'South African Rands'
		) );

		foreach( $currencies as $symbol => $name ) {
			echo '<option ' . selected( $symbol, $current, false ) . ' value="' . esc_attr( $symbol ) . '">' . esc_html( $name ) . '</option>';
		}

	}

}

/**
 * Extract Object taxonomies list.
 */
if( !function_exists( 'pys_get_content_taxonomies' ) ) {

	function pys_get_content_taxonomies( $taxonomy = 'category', $post_id = null ) {

		$post_id = isset( $post_id ) ? $post_id : get_the_ID();
		$terms   = get_the_terms( $post_id, $taxonomy );

		if ( is_wp_error( $terms ) || empty ( $terms ) ) {
			return false;
		}

		$list = array();

		foreach( $terms as $term ) {
			$list[ $term->term_id ] = html_entity_decode( $term->name );
		}

		return implode( ', ', $list );

	}

}

if( !function_exists( 'pys_add_event' ) ) {
	
	/**
	 * Add event with params to global events list.
	 *
	 * @param string $event  Event name, eg. "PageView"
	 * @param array  $params Optional. Associated array of event parameters in 'param_name' => 'param_value' format.
	 * @param int    $delay  Optional. If set, event will be fired with desired delay in seconds.
	 */
	function pys_add_event( $event, $params = array(), $delay = 0 ) {
		global $pys_events;

		$params = apply_filters( 'pys_event_params', $params, $event );

		$sanitized = array();

		// sanitize param names and its values
		foreach ( $params as $name => $value ) {

			if ( empty( $value ) && $value !== "0" ) {
				continue;
			}
			
			$key               = esc_js( $name );
			$sanitized[ $key ] = $value;

		}

		$pys_events[] = array(
			'type'   => pys_is_standard_event( $event ) ? 'track' : 'trackCustom',
			'name'   => $event,
			'params' => $sanitized,
			'delay'  => $delay
		);

	}

}

if ( ! function_exists( 'pys_get_product_content_id' ) ) {

	/**
	 * Return product id or sku.
	 */
    function pys_get_product_content_id( $product_id ) {
        
        $content_id_format = pys_get_option( 'woo', 'content_id_format', 'default' );
        
        if ( pys_get_option( 'woo', 'content_id' ) == 'sku' ) {
            $content_id = get_post_meta( $product_id, '_sku', true );
        } else {
            $content_id = intval( $product_id );
        }
        
        return apply_filters( 'pys_fb_pixel_woo_product_content_id', array( $content_id ), $product_id,
            $content_id_format );
        
    }

}

if ( ! function_exists( 'pys_get_product_id' ) ) {

	/**
	 * Return main or variation product id.
	 */
	function pys_get_product_id( $product ) {

		if ( pys_get_option( 'woo', 'variation_id' ) != 'main' && isset( $product['variation_id'] ) && $product['variation_id'] != 0 ) {
			$id = $product['variation_id'];
		} else {
            $id = $product['product_id'];
        }

		return $id;
	}

}

if ( ! function_exists( 'pys_insert_attribute' ) ) {

	/**
	 * Add attribute with value to a HTML tag.
	 *
	 * @param string $attr_name  Attribute name, eg. "class"
	 * @param string $attr_value Attribute value
	 * @param string $content    HTML content where attribute should be inserted
	 * @param bool   $overwrite  Override existing value of attribute or append it
	 * @param string $tag        Selector name, eg. "button". Default "a"
	 *
	 * @return string Modified HTML content
	 */
	function pys_insert_attribute( $attr_name, $attr_value, $content, $overwrite = false, $tag = 'a' ) {

		## do not modify js attributes
		if( $attr_name == 'on' ) {
			return $content;
		}

		$attr_value = trim( $attr_value );

		try {

			$doc = new DOMDocument();

			/**
			 * Old libxml does not support options parameter.
			 *
			 * @since 3.2.0
			 */
			if ( defined( 'LIBXML_DOTTED_VERSION' ) && version_compare( LIBXML_DOTTED_VERSION, '2.6.0', '>=' ) &&
				version_compare( phpversion(), '5.4.0', '>=' )
			) {
				@$doc->loadHTML( '<?xml encoding="UTF-8">' . $content, LIBXML_NOEMPTYTAG );
			} else {
				@$doc->loadHTML( '<?xml encoding="UTF-8">' . $content );
			}

			/**
			 * Select top-level tag if it is not specified in args.
			 *
			 * @since: 5.0.6
			 */
			if ( $tag == 'any' ) {

				/** @var DOMNodeList $node */
				$node = $doc->getElementsByTagName( 'body' );

				if ( $node->length == 0 ) {
					throw new Exception( 'Empty or wrong tag passed to filter.' );
				}

				$node = $node->item( 0 )->childNodes->item( 0 );

			} else {
				$node = $doc->getElementsByTagName( $tag )->item( 0 );
			}

			if ( is_null( $node ) ) {
				return $content;
			}

			/** @noinspection PhpUndefinedMethodInspection */
			$attribute = $node->getAttribute( $attr_name );

			// add attribute or override old one
			if ( empty( $attribute ) || $overwrite ) {

				/** @noinspection PhpUndefinedMethodInspection */
				$node->setAttribute( $attr_name, $attr_value );

				return str_replace( array( '<?xml encoding="UTF-8">', '<html>', '</html>', '<body>', '</body>' ), null, $doc->saveHTML() );

			}

			// append value to exist attribute
			if ( $overwrite == false ) {

				$value = $attribute . ' ' . $attr_value;
				/** @noinspection PhpUndefinedMethodInspection */
				$node->setAttribute( $attr_name, $value );

				return str_replace( array( '<?xml encoding="UTF-8">', '<html>', '</html>', '<body>', '</body>' ), null, $doc->saveHTML() );

			}

		} catch ( Exception $e ) {

			error_log( $e );

		}

		return $content;

	}

}

if ( ! function_exists( 'pys_is_disabled_for_role' ) ) {

	function pys_is_disabled_for_role() {

		$options = get_option( 'pixel_your_site' );
		$disabled_roles = $options['general'];

		$user           = wp_get_current_user();
		foreach ( (array) $user->roles as $role ) {

			if ( array_key_exists( "disable_for_$role", $disabled_roles ) ) {

				add_action( 'wp_head', 'pys_head_message', 1 );
				return true;

			}

		}

		if( empty( $user->roles ) && isset( $disabled_roles['disable_for_guest'] ) ) {

			add_action( 'wp_head', 'pys_head_message', 1 );
			return true;

		}

		return false;

	}

}

if( ! function_exists( 'pys_head_message' ) ) {

	/**
	 * Output "disabled for role" and version number message to document head.
	 */
	function pys_head_message() {
		echo "<!-- Facebook Pixel Code disabled for current role ( " . pys_get_version() . " ) -->\r\n";
	}

}

if( ! function_exists( 'pys_get_version' ) ) {

	function pys_get_version() {

		if( defined('PYS_PRO_VERSION') ) {
			$version = "PixelYourSite PRO v".PYS_PRO_VERSION;
		} elseif( defined('PYS_FREE_VERSION') ) {
			$version = "PixelYourSite FREE v".PYS_FREE_VERSION;
		} else {
			$version = null;
		}

		return $version;

	}

}

if( ! function_exists( 'pys_page_view_event' ) ) {

	/*
	 * PageView event. Present on each page if pixel setup is enabled.
	 */
	function pys_page_view_event() {
		pys_add_event( 'PageView' );
	}

}

if( ! function_exists( 'pys_general_event' ) ) {

	function pys_general_event() {
		global $post;

		if ( pys_get_option( 'general', 'general_event_enabled' ) == false ) {
			return;
		}

		$params     = array();
		$event_name = pys_get_option( 'general', 'general_event_name' );
		$post_type  = get_post_type();
		$delay      = floatval( pys_get_option( 'general', 'general_event_delay', 0 ) );

		$on_posts_enabled      = pys_get_option( 'general', 'general_event_on_posts_enabled' );
		$on_pages_enables      = pys_get_option( 'general', 'general_event_on_pages_enabled' );
		$on_taxonomies_enabled = pys_get_option( 'general', 'general_event_on_tax_enabled' );
		$on_cpt_enabled        = pys_get_option( 'general', 'general_event_on_' . $post_type . '_enabled' );
		$on_woo_enabled        = pys_get_option( 'general', 'general_event_on_woo_enabled' );
		$on_edd_enabled        = pys_get_option( 'general', 'general_event_on_edd_enabled' );
		$track_tags_enabled    = pys_get_option( 'general', 'general_event_add_tags', false );

		// Posts
		if ( $on_posts_enabled && is_singular( 'post' ) ) {

			$params['post_type']    = 'post';
			$params['content_name'] = $post->post_title;
			$params['post_id']      = $post->ID;

			if ( $terms = pys_get_content_taxonomies() ) {
				$params['content_category'] = $terms;
			}

			if ( $track_tags_enabled && $tags = pys_get_post_tags( $post->ID ) ) {
				$params['tags'] = implode( ', ', $tags );
			}

			pys_add_event( $event_name, $params, $delay );

			return;

		}

		// Pages or Front Page
		if ( $on_pages_enables && ( is_singular( 'page' ) || is_home() ) ) {

			// exclude WooCommerce Cart page
			if ( pys_is_woocommerce_active() && is_cart() ) {
				return;
			}

			$params['post_type']    = 'page';
			$params['content_name'] = is_home() == true ? get_bloginfo( 'name' ) : $post->post_title;

			is_home() != true ? $params['post_id'] = $post->ID : null;

			pys_add_event( $event_name, $params, $delay );

			return;

		}

		// WooCommerce Shop page
		if ( $on_pages_enables && pys_is_woocommerce_active() && is_shop() ) {

			$page_id = wc_get_page_id( 'shop' );

			$params['post_type']    = 'page';
			$params['post_id']      = $page_id;
			$params['content_name'] = get_the_title( $page_id );;

			pys_add_event( $event_name, $params, $delay );

			return;

		}

		// Taxonomies (built-in and custom)
		if ( $on_taxonomies_enabled && ( is_category() || is_tax() || is_tag() ) ) {

			$term = null;
			$type = null;

			if ( is_category() ) {

				$cat  = get_query_var( 'cat' );
				$term = get_category( $cat );

				$params['post_type']    = 'category';
				$params['content_name'] = $term->name;
				$params['post_id']      = $cat;

			} elseif ( is_tag() ) {

				$slug = get_query_var( 'tag' );
				$term = get_term_by( 'slug', $slug, 'post_tag' );

				$params['post_type']    = 'tag';
				$params['content_name'] = $term->name;
				$params['post_id']      = $term->term_id;

			} else {

				$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

				$params['post_type']    = get_query_var( 'taxonomy' );
				$params['content_name'] = $term->name;
				$params['post_id']      = $term->term_id;

			}

			pys_add_event( $event_name, $params, $delay );

			return;

		}

		// Custom Post Type
		if ( $on_cpt_enabled && $post_type != 'post' && $post_type != 'page' ) {

			// skip products and downloads is plugins are activated
			if ( ( pys_is_woocommerce_active() && $post_type == 'product' ) || ( pys_is_edd_active() && $post_type == 'download' ) ) {
				return;
			}

			$params['post_type']    = $post_type;
			$params['content_name'] = $post->post_title;
			$params['post_id']      = $post->ID;

			$taxonomies = get_post_taxonomies( get_post() );
			$terms      = pys_get_content_taxonomies( $taxonomies[0] );
			if ( $terms ) {
				$params['content_category'] = $terms;
			}

			if ( $track_tags_enabled && $tags = pys_get_post_tags( $post->ID ) ) {
				$params['tags'] = implode( ', ', $tags );
			}

			pys_add_event( $event_name, $params, $delay );

			return;

		}

		## WooCommerce Products
		if ( $on_woo_enabled && pys_is_woocommerce_active() && $post_type == 'product' ) {

			pys_general_woo_event( $post, $track_tags_enabled, $delay, $event_name );
			return;

		}

		## Easy Digital Downloads
		if ( $on_edd_enabled && pys_is_edd_active() && $post_type == 'download' ) {

			pys_general_edd_event( $post, $track_tags_enabled, $delay, $event_name );
			return;

		}

	}

}

if( ! function_exists( 'pys_search_event' ) ) {

	function pys_search_event() {

		if ( pys_get_option( 'general', 'search_event_enabled' ) == false || is_search() == false || ! isset( $_REQUEST['s'] ) ) {
			return;
		}

		pys_add_event( 'Search', array(
			'search_string' => $_REQUEST['s']
		) );

	}

}

if( ! function_exists( 'pys_standard_events' ) ) {

	function pys_standard_events() {
		global $pys_custom_events;

		$std_events = get_option( 'pixel_your_site_std_events', array() );

		if ( pys_get_option( 'std', 'enabled' ) == false || empty( $std_events ) ) {
			return;
		}

		foreach ( $std_events as $event ) {

			// skip wrong events
			if( ! isset( $event['pageurl'] ) || ! isset( $event['eventtype'] ) ) {
				continue;
			}

			// skip if current page does not match URL filter
			if ( pys_match_url( $event['pageurl'] ) == false  ) {
				continue;
			}

			if ( $event['eventtype'] == 'CustomCode' ) {

				$custom_code = $event['code'];
				$custom_code = stripcslashes( $custom_code );
				$custom_code = trim( $custom_code );

				// add custom code to global custom events array
				$pys_custom_events[] = $custom_code;

			} else {

				$type = $event['eventtype'];
				$params = pys_clean_system_event_params( $event );

				pys_add_event( $type, $params );

			}

		}

	}

}

if( !function_exists( 'pys_pixel_init_event' ) ) {

	function pys_pixel_init_event() {
		global $pys_events;

		$pys_events[] = array(
			'type'   => 'init',
			'name'   => pys_get_option( 'general', 'pixel_id' ),
			'params' => pys_pixel_init_params()
		);

	}

}

if( ! function_exists( 'pys_output_noscript_code' ) ) {

	function pys_output_noscript_code() {
		global $pys_events;

		if( empty( $pys_events ) ) {
			return;
		}

		foreach( $pys_events as $event ) {

			$args = array();

			if( $event['type'] == 'init' ) {
				continue;
			}

			$args['id']       = pys_get_option( 'general', 'pixel_id' );
			$args['ev']       = $event['name'];
			$args['noscript'] = 1;

			foreach ( $event['params'] as $param => $value ) {
				@$args[ 'cd[' . $param . ']' ] = urlencode( $value );
			}

			$src_attr = add_query_arg( $args, 'https://www.facebook.com/tr' );

			// note: ALT tag used to pass ADA compliance
			// @see: issue #63 for details

			echo "<noscript><img height='1' width='1' style='display: none;' src='{$src_attr}' alt='facebook_pixel'></noscript>";

		}

	}

}

if( ! function_exists( 'pys_output_js_events_code' ) ) {

	function pys_output_js_events_code() {
		global $pys_events;

		// allow external plugins modify events
		$pys_events = apply_filters( 'pys_prepared_events', $pys_events );

		if( empty( $pys_events ) ) {
			return;
		}

		wp_localize_script( 'pys-public', 'pys_events', $pys_events );

	}

}

if( ! function_exists( 'pys_output_custom_events_code' ) ) {

	function pys_output_custom_events_code() {
		global $pys_custom_events;

		if( empty( $pys_custom_events ) ) {
			return;
		}

		wp_localize_script( 'pys-public', 'pys_customEvents', $pys_custom_events );

	}

}

if( !function_exists( 'pys_clean_system_event_params' ) ) {

	function pys_clean_system_event_params( $params ) {

		// remove unused params
		unset( $params['pageurl'] );
		unset( $params['eventtype'] );
		unset( $params['code'] );
		unset( $params['trigger_type'] );    // pro
		unset( $params['custom_currency'] ); // pro
		unset( $params['url'] );             // pro
		unset( $params['css'] );             // pro
		unset( $params['custom_name'] );     // custom events
		unset( $params['scroll_pos'] );      // pro
		unset( $params['url_filter'] );      // pro - dynamic events

		return $params;

	}

}

if( !function_exists( 'pys_get_woo_checkout_params' ) ) {

	function pys_get_woo_checkout_params( $additional_params_enabled ) {
		global $woocommerce;

		// set defaults params
		$params                 = array();
		$params['content_type'] = 'product';

		$ids        = array();     // cart items ids or sku
		$names      = '';        // cart items names
		$categories = '';   // cart items categories

		foreach ( $woocommerce->cart->cart_contents as $cart_item_key => $item ) {

            $product_id = pys_get_product_id( $item );
            $ids        = array_merge( $ids, pys_get_product_content_id( $product_id ) );

			// content_name, category_name for each cart item
			if ( $additional_params_enabled ) {

				$temp = array();
				pys_get_additional_woo_params( $product_id, $temp );

				$names .= isset( $temp['content_name'] ) ? $temp['content_name'] . ' ' : null;
				$categories .= isset( $temp['category_name'] ) ? $temp['category_name'] . ' ' : null;

			}

		}

		if ( $additional_params_enabled ) {
			$params['num_items'] = $woocommerce->cart->get_cart_contents_count();
		}

		$params['content_ids'] = json_encode( $ids );

		if ( ! empty( $names ) ) {
			$params['content_name'] = $names;
		}

		if ( ! empty( $categories ) ) {
			$params['category_name'] = $categories;
		}

		return $params;

	}

}

if( !function_exists( 'pys_get_default_options' ) ) {

	function pys_get_default_options() {

		$options = array();

		$options['general']['pixel_id'] = '';
		$options['general']['enabled']  = 0;
		$options['general']['add_traffic_source']  = 1;
		$options['general']['enable_advance_matching']  = 1;
		$options['general']['in_footer']  = 0;

		$options['general']['general_event_enabled']          = 1;
		$options['general']['general_event_name']             = 'GeneralEvent';
		$options['general']['general_event_on_posts_enabled'] = 1;
		$options['general']['general_event_on_pages_enabled'] = 1;
		$options['general']['general_event_on_tax_enabled']   = 1;
		$options['general']['general_event_on_woo_enabled']   = 0;
		$options['general']['general_event_on_edd_enabled']   = 0;
		$options['general']['general_event_add_tags']         = 0;

		$options['general']['timeonpage_enabled'] = 1;

		$options['general']['search_event_enabled'] = 1;

		$options['std']['enabled'] = 0;

		$options['dyn']['enabled']            = 0;
		$options['dyn']['enabled_on_content'] = 0;
		$options['dyn']['enabled_on_widget']  = 0;

		$options['woo']['enabled'] = pys_is_woocommerce_active() ? 1 : 0;

		$options['woo']['content_id']   = 'id';
		$options['woo']['variation_id'] = 'variation';

		$options['woo']['enable_additional_params'] = 1;
		$options['woo']['enable_tags']              = 1;
		$options['woo']['tax']                      = 'incl';

		$options['woo']['on_view_content']            = 1;
		$options['woo']['enable_view_content_value']  = 1;
		$options['woo']['view_content_value_option']  = 'price';
		$options['woo']['view_content_percent_value'] = '';
		$options['woo']['view_content_global_value']  = '';

		$options['woo']['on_add_to_cart_btn']        = 1;
		$options['woo']['on_cart_page']              = 1;
		$options['woo']['enable_add_to_cart_value']  = 1;
		$options['woo']['add_to_cart_value_option']  = 'price';
		$options['woo']['add_to_cart_percent_value'] = '';
		$options['woo']['add_to_cart_global_value']  = '';

		$options['woo']['on_checkout_page']       = 1;
		$options['woo']['enable_checkout_value']  = 1;
		$options['woo']['checkout_value_option']  = 'price';
		$options['woo']['checkout_percent_value'] = '';
		$options['woo']['checkout_global_value']  = '';

		$options['woo']['on_thank_you_page']      = 1;
		$options['woo']['enable_purchase_value']  = 1;
		$options['woo']['purchase_fire_once']     = 1;
		$options['woo']['purchase_transport']     = 'included';
		$options['woo']['purchase_value_option']  = 'total';
		$options['woo']['purchase_percent_value'] = '';
		$options['woo']['purchase_global_value']  = '';

		$options['woo']['purchase_add_address']         = 1;
		$options['woo']['purchase_add_payment_method']  = 1;
		$options['woo']['purchase_add_shipping_method'] = 1;
		$options['woo']['purchase_add_coupons']         = 1;

		$options['woo']['enable_aff_event']     = 0;
		$options['woo']['aff_event']            = 'predefined';
		$options['woo']['aff_predefined_value'] = 'Lead';
		$options['woo']['aff_custom_value']     = '';
		$options['woo']['aff_value_option']     = 'none';
		$options['woo']['aff_global_value']     = '';

		$options['woo']['enable_paypal_event'] = 0;
		$options['woo']['pp_event']            = 'predefined';
		$options['woo']['pp_predefined_value'] = 'InitiatePayment';
		$options['woo']['pp_custom_value']     = '';
		$options['woo']['pp_value_option']     = 'none';
		$options['woo']['pp_global_value']     = '';
		
		/**
		 * Easy Digital Downloads
		 */

		$options['edd']['enabled'] = pys_is_edd_active() ? 1 : 0;
		
		$options['edd']['content_id']               = 'id';
		$options['edd']['enable_additional_params'] = 1;
		$options['edd']['enable_tags']              = 1;
		$options['edd']['tax']                      = 'incl';

		$options['edd']['on_view_content']            = 1;
		$options['edd']['on_view_content_delay']      = null;
		$options['edd']['enable_view_content_value']  = 1;
		$options['edd']['view_content_value_option']  = 'price';
		$options['edd']['view_content_percent_value'] = null;
		$options['edd']['view_content_global_value']  = null;
		
		$options['edd']['on_add_to_cart_btn']        = 1;
		$options['edd']['enable_add_to_cart_value']  = 1;
		$options['edd']['add_to_cart_value_option']  = 'price';
		$options['edd']['add_to_cart_percent_value'] = null;
		$options['edd']['add_to_cart_global_value']  = null;

		$options['edd']['on_checkout_page']       = 1;
		$options['edd']['enable_checkout_value']  = 1;
		$options['edd']['checkout_value_option']  = 'price';
		$options['edd']['checkout_percent_value'] = null;
		$options['edd']['checkout_global_value']  = null;

		$options['edd']['on_success_page']        = 1;
		$options['edd']['enable_purchase_value']  = 1;
		$options['edd']['purchase_fire_once']     = 1;
		$options['edd']['purchase_value_option']  = 'total';
		$options['edd']['purchase_percent_value'] = null;
		$options['edd']['purchase_global_value']  = null;

		$options['edd']['purchase_add_address']        = true;
		$options['edd']['purchase_add_payment_method'] = true;
		$options['edd']['purchase_add_coupons']        = true;

		return apply_filters( 'pys_fb_pixel_setting_defaults', $options );

	}

}

if( ! function_exists( 'pys_get_custom_params' ) ) {

	/**
	 * Extract non-system params array
	 */
	function pys_get_custom_params( $event ) {

		if( !is_array( $event ) || empty( $event ) ) {
			return array();
		}

		$system_params = array(
			'trigger_type',
			'url_filter',
			'url',
			'css',
			'pageurl',
			'eventtype',
			'value',
			'currency',
			'custom_currency',
			'content_name',
			'content_ids',
			'content_type',
			'content_category',
			'num_items',
			'order_id',
			'search_string',
			'status',
			'code',
			'custom_name',
			'scroll_pos'
		);

		$custom_params = array();
		foreach( $event as $param => $value ) {

			// skip standard params
			if( in_array( $param, $system_params ) ) {
				continue;
			}

			$custom_params[ $param ] = $value;

		}


		return $custom_params;

	}

}

if( ! function_exists( 'pys_is_standard_event' ) ) {

	function pys_is_standard_event( $eventtype ) {

		$std_events = array(
			'PageView',
			'ViewContent',
			'Search',
			'AddToCart',
			'AddToWishlist',
			'InitiateCheckout',
			'AddPaymentInfo',
			'Purchase',
			'Lead'
		);

		return in_array( $eventtype, $std_events );

	}

}

if( ! function_exists( 'pys_pixel_init_params' ) ) {

	/**
	 * Add extra params to FB init call.
	 */
	function pys_pixel_init_params() {

		$params = array();
		$params = apply_filters( 'pys_pixel_init_params', $params );

		$sanitized = array();

		// sanitize params
		foreach ( $params as $name => $value ) {

			if ( empty( $value ) ) {
				continue;
			}

			$key   = esc_js( $name );
			$sanitized[ $key ] = $value;

		}

		return $sanitized;

	}

}

if( ! function_exists( 'pys_woocommerce_events' ) ) {

	function pys_woocommerce_events() {

		if ( pys_get_option( 'woo', 'enabled' ) == false || pys_is_woocommerce_active() == false ) {
			return;
		}

		pys_get_woo_code();

		// WooCommerce non-ajax AddToCart Event handler
		if ( isset( $_REQUEST['add-to-cart'] ) ) {
            
			if ( pys_get_option( 'woo', 'variation_id' ) != 'main' && isset( $_REQUEST['variation_id'] ) ) {
				$product_id = $_REQUEST['variation_id'];
			} else {
                $product_id = isset( $_REQUEST['add-to-cart'] ) ? $_REQUEST['add-to-cart'] : null;
            }

			$params = pys_get_woo_ajax_addtocart_params( $product_id );

			pys_add_event( 'AddToCart', $params );

		}

	}

}

if( ! function_exists( 'pys_output_woo_ajax_events_code' ) ) {

	function pys_output_woo_ajax_events_code() {
		global $pys_woo_ajax_events;

		if( empty( $pys_woo_ajax_events ) ) {
			return;
		}

		$events = array();

		foreach ( $pys_woo_ajax_events as $id => $event ) {

			$params = pys_clean_system_event_params( $event['params'] );

			// sanitize params
			$sanitized = array();
			foreach ( $params as $name => $value ) {

				if ( empty( $value ) ) {
					continue;
				}

				$key               = esc_js( $name );
				$sanitized[ $key ] = $value;

			}

			$name = $event['name'];

			$events[ $id ] = array(
				'type'   => pys_is_standard_event( $name ) ? 'track' : 'trackCustom',
				'name'   => $name,
				'params' => $sanitized
			);

		}

		?>

	<script type="text/javascript">
	/* <![CDATA[ */
	var pys_woo_ajax_events = <?php echo json_encode( $events ); ?>;
	/* ]]> */
	</script>

		<?php

	}

}

if( ! function_exists( 'pys_output_options' ) ) {

	function pys_output_options() {

		$options = array(
			'site_url'               => get_site_url(),
			'traffic_source_enabled' => pys_get_option( 'general', 'add_traffic_source' )
		);

		wp_localize_script( 'pys-public', 'pys_options', $options );

	}

}

if( ! function_exists( 'pys_head_comments' ) ) {

	function pys_head_comments() {

		if( defined( 'PYS_PRO_VERSION' ) ) {
			$version = "PRO v" . PYS_PRO_VERSION;
		} elseif( defined( 'PYS_FREE_VERSION' ) ) {
			$version = "FREE v" . PYS_FREE_VERSION;
		} else {
			$version = null;
		}

		?>

		<!-- Facebook Pixel code is added on this page by PixelYourSite <?php echo esc_attr( $version ); ?> plugin. You can test it with Pixel Helper Chrome Extension. -->

		<?php
	}

}

if( ! function_exists( 'pys_render_event_code' ) ) {

	/**
	 * Return full event code as a string value. Used to display event code preview on standard and dynamic event lists.
	 */
	function pys_render_event_code( $event_name, $params ) {

		$event_type = pys_is_standard_event( $event_name ) ? 'track' : 'trackCustom';

		$params = pys_clean_system_event_params( $params );

		$event_params = '';
		foreach( $params as $name => $value ) {

			if( empty( $value ) && $value !== "0" ) {
				continue;
			}

			$event_params .= " " . esc_js( $name ) . ": '" . $value . "',";

		}

		$event_params = ltrim( $event_params );
		$event_params = rtrim( $event_params, "," );

		return "fbq('{$event_type}', '{$event_name}', { {$event_params} } );";

	}

}

if( !function_exists( 'pys_add_domain_param' ) ) {

	function pys_add_domain_param( $params, $event ) {

		// get home URL without protocol
		$params['domain'] = substr( get_home_url( null, '', 'http' ), 7 );
		return $params;

	}

}

if ( ! function_exists( 'pys_get_additional_post_params' ) ) {
	
	function pys_get_additional_post_params( $post, &$params, $taxonomy ) {
		
		// get WP_Post object
		if ( is_numeric( $post ) ) {
			$id    = absint( $post );
			$_post = get_post( $id );
		} elseif ( $post instanceof WC_Product ) {
			$_post = $post->post;
		} elseif ( isset( $post->ID ) ) {
			$_post = $post;
		} else {
			return;
		}
		
		$params['content_name'] = $_post->post_title;
		
		$terms = pys_get_content_taxonomies( $taxonomy, $_post->ID );
		if ( $terms ) {
			$params['category_name'] = $terms;
		}
		
	}
	
}

if ( ! function_exists( 'pys_get_object_tags' ) ) {

	/**
	 * @param int|WP_Post $post     Post ID or object
	 * @param string      $taxonomy Taxonomy name
	 *
	 * @return array Array of terms titles on success or empty array
	 */
	function pys_get_object_tags( $post, $taxonomy ) {
		
		$tags  = array();
		$terms = get_the_terms( $post, $taxonomy );
		
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			
			foreach ( $terms as $term ) {
				$tags[] = html_entity_decode( $term->name );
			}
			
		}
		
		return $tags;
		
	}
	
}

if ( ! function_exists( 'pys_delete_events' ) ) {

	function pys_delete_events( $events_ids, $events_type ) {

		if ( $events_type == 'standard' ) {
			$option_name = 'pixel_your_site_std_events';
		} else {
			$option_name = 'pixel_your_site_dyn_events';
		}

		$events = (array) get_option( $option_name );

		if ( empty( $events ) ) {
			return;
		}

		foreach ( $events_ids as $id ) {
			unset( $events[ $id ] );
		}

		update_option( $option_name, $events );

	}

}

if( ! function_exists( 'pys_is_wc_version_gte' ) ) {
	
	function pys_is_wc_version_gte( $version ) {
		
		if ( defined( 'WC_VERSION' ) && WC_VERSION ) {
			return version_compare( WC_VERSION, $version, '>=' );
		} else if ( defined( 'WOOCOMMERCE_VERSION' ) && WOOCOMMERCE_VERSION ) {
			return version_compare( WOOCOMMERCE_VERSION, $version, '>=' );
		} else {
			return false;
		}
		
	}
	
}