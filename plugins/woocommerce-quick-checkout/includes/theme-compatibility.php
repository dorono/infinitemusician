<?php
/**
 *  Theme Compatibility
 *
 * @description: Misc. functions to handle theme compatibility
 * @copyright  : http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      : 1.8.2
 */

//Current theme information
$wqc_current_theme = wp_get_theme();

//Sanity Check:
if ( ! isset( $wqc_current_theme->template ) ) {
	return false;
}

//Avada Theme
switch ( strtolower( $wqc_current_theme->template ) ) {
	case 'avada':
		add_filter( 'wqc_shortcode_classes', 'avada_shortcode_button_classes' );
		break;
	case 'divi':
		add_filter( 'wqc_shortcode_classes', 'divi_shortcode_button_classes' );
		break;
}

/**
 * Avada Shortcode Buttons
 *
 * @param $shortcode_classes
 *
 * @since 1.9
 * @return string
 */
function avada_shortcode_button_classes( $shortcode_classes ) {

	//Append Avada classes
	$shortcode_classes .= ' fusion-button fusion-button-default';

	return $shortcode_classes;

}

/**
 * Divi Shortcode Buttons
 *
 * @param $shortcode_classes
 *
 * @since 1.9
 * @return string
 */
function divi_shortcode_button_classes( $shortcode_classes ) {

	//Append Divi button classes
	$shortcode_classes .= ' et_pb_button et_pb_bg_layout_light';

	return $shortcode_classes;

}