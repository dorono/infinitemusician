<?php

/**
 *  Quick Checkout TinyMCE
 *
 * @description: Add a button the the TinyMCE editor to allow Quick Checkout shortcode generation
 * @since      : 1.0
 * @created    : 2/7/14
 */
class Quick_Checkout_TinyMCE {

	public function __construct() {

		add_action( 'admin_head', array( $this, 'ubl_add_tinymce' ) );
	}

	function ubl_add_tinymce() {

		global $typenow;

		// check user permissions
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'add_tinymce_button' ) );
		}


	}

	// inlcude the js for tinymce
	function add_tinymce_plugin( $plugin_array ) {

		$plugin_array['quick_checkout_shortcodes'] = WQC_PLUGIN_URL . '/assets/js/quick-checkout-admin-shortcode.js';

		return $plugin_array;

	}

	// Add the button key for address via JS
	function add_tinymce_button( $buttons ) {

		array_push( $buttons, 'quick_checkout_shortcodes' );

		return $buttons;

	}


}

