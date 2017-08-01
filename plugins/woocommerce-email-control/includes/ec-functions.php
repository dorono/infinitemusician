<?php
/**
 * Email Customizer - Helper functions
 *
 * Used globally as tools across the plugin.
 *
 * @since 2.01
 */

/**
 * Register Email Themes.
 *
 * A function for creating or modifying a email themes based on the
 * parameters given. The function will accept an array (second optional
 * parameter), along with a string for the post type name.
 *
 * @since	2.0
 * @date	20-08-2014
 *
 * @global 	array      			$ec_email_themes	List of email themes.
 *
 * @param 	string				$theme_id	Email theme id, must not exceed 20 characters.
 * @param	array|string		$args {
 *     Array or string of arguments for registering email theme.
 * }
 * @return	object|WP_Error		The registered post type object, or an error object.
 */
if ( ! function_exists( 'ec_register_email_theme' ) ) {
	function ec_register_email_theme( $theme_id, $args ) {
		
		global $ec_email_themes;
		
		if ( ! is_array( $ec_email_themes ) )
			$ec_email_themes = array();
		
		$defaults = array(
			'name'                	=> $theme_id,
			'description'           => '',
			'settings'           	=> false,
		);
		$args = wp_parse_args( $args, $defaults );
		
		if ( strlen( $theme_id ) > 40 ) {
			_doing_it_wrong( __FUNCTION__, __( 'Theme IDs cannot exceed 20 characters in length', 'email-control' ) );
			return new WP_Error( 'theme_id_too_long', __( 'Theme IDs cannot exceed 20 characters in length', 'email-control' ) );
		}

		$ec_email_themes[ $theme_id ] = $args;
		
		return $args;
	}
}

/**
 * Apply CSS to content inline. (Legacy - no longer using this. replaced by WC emogrifier)
 *
 * @param string|null $content
 * @param string|null $css
 * @return string
 */
function ec_apply_inline_styles( $content = '', $css = '' ) {
	
	// load EmogrifierCXEC.
	require_once( WC_EMAIL_CONTROL_DIR . '/includes/emogrifier/Emogrifier.php' );
	
	$emogrifier = new EmogrifierCXEC();
	
	// Apply Emogrifier to inline the CSS.
	try {
		
		$emogrifier->setHtml( $content );
		$emogrifier->setCss( strip_tags( $css ) );
		$content = $emogrifier->emogrify();
	}
	catch ( Exception $e ) {

		$logger = new WC_Logger();
		$logger->add( 'emogrifier', $e->getMessage() );
	}
	
	return $content;
}

/**
 * Backup mb_convert_encoding function. (Legacy - no longer using this. replaced by WC emogrifier)
 *
 * backup if php module php_mbstring is not active on server.
 * Simply a backup to avoid errors. User should get module activated.
 *
 * @author cxThemes
 */
if ( ! function_exists( 'mb_convert_encoding' ) ) {
	function mb_convert_encoding ( $string, $type = 'HTML-ENTITIES', $encoding = 'utf-8' ) {
		
		// $string = htmlentities( $string, ENT_COMPAT, $encoding, false);
		// return html_entity_decode( $string );
		return $string;
	}
	
	// $string = 'Test:!"$%&/()=ÖÄÜöäü<<';
	// echo mb_convert_encoding( $string, 'HTML-ENTITIES', 'utf-8' );
	// echo htmlspecialchars_decode( utf8_decode( htmlentities( $string, ENT_COMPAT, 'utf-8', false) ) );
}

/**
 * Helper function to check if a theme will work with the current WooCommerce version.
 *
 * @param    string    $theme_id   Theme id eg `supreme` to check.
 * @return   boolean
 */
function ec_check_theme_version( $theme_id ) {
	global $ec_email_themes;
	
	if ( ! isset( $ec_email_themes[$theme_id] ) ) return TRUE;
	
	$woocommerce_required_version = ( isset( $ec_email_themes[$theme_id]['woocoomerce_required_version'] ) ) ? $ec_email_themes[$theme_id]['woocoomerce_required_version'] : WC_EMAIL_CONTROL_REQUIRED_WOOCOMMERCE_VERSION ;
	return version_compare( get_option( 'woocommerce_version' ), $woocommerce_required_version, '>' );
}

/**
 * Order helper function for methods that were only introduced in WC 3.0.
 */
function ec_order_get_id( $order ) {
	return method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id ;
}
function ec_order_get_date_created( $order ) {
	return method_exists( $order, 'get_date_created' ) ? $order->get_date_created() : $order->date;
}
function ec_order_get_billing_first_name( $order ) {
	return method_exists( $order, 'get_billing_first_name' ) ? $order->get_billing_first_name() : $order->billing_first_name;
}
function ec_order_get_billing_last_name( $order ) {
	return method_exists( $order, 'get_billing_last_name' ) ? $order->get_billing_last_name() : $order->billing_last_name;
}
function ec_order_get_billing_email( $order ) {
	return method_exists( $order, 'get_billing_email' ) ? $order->get_billing_email() : $order->billing_email;
}
function ec_order_get_customer_note( $order ) {
	return method_exists( $order, 'get_customer_note' ) ? $order->get_customer_note() : $order->customer_note;
}
function ec_order_get_payment_method_title( $order ) {
	return method_exists( $order, 'get_payment_method_title' ) ? $order->get_payment_method_title() : $order->payment_method_title;
}
