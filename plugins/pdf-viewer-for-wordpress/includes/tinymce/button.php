<?php
// Hooks your functions into the correct filters
function tnc_pdf_add_mce_button() {
	// check user permissions
	if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
		return;
	}
	// check if WYSIWYG is enabled
	if ( 'true' == get_user_option( 'rich_editing' ) ) {
		add_filter( 'mce_external_plugins', 'tnc_pdf_add_tinymce_plugin' );
		add_filter( 'mce_buttons', 'tnc_pdf_register_mce_button' );
	}
}
add_action('admin_head', 'tnc_pdf_add_mce_button');

// Declare script for new button
function tnc_pdf_add_tinymce_plugin( $plugin_array ) {
	$plugin_array['tnc_pdf_mce_button'] = plugins_url(). '/' .plugin_basename( dirname(__FILE__) ).'/js/tnc-pdf-button.js';
	return $plugin_array;
}

// Register new button in the editor
function tnc_pdf_register_mce_button( $buttons ) {
	array_push( $buttons, 'tnc_pdf_mce_button' );
	return $buttons;
}
?>