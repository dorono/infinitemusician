<?php
/**
 * Copyright (c) 2012-2013, IgniteWoo.com
 * Copyright (c) 2012-2013, SkyVerge, Inc.
 *
 * GPL v3
 */

/**
 * Sets up the write panels used by vouchers (custom post types)
 *
 * @since 3.3
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );

require_once( 'writepanel-order_vouchers.php' );
require_once( 'writepanel-voucher_additional_images.php' );
require_once( 'writepanel-voucher_alternative_images.php' );
require_once( 'writepanel-voucher_image.php' );
require_once( 'writepanel-voucher_data.php' );
require_once( 'writepanel-voucher_gen.php' );

add_action( 'add_meta_boxes', 'ign_vouchers_meta_boxes' );

add_action( 'admin_enqueue_scripts', 'ign_voucher_scripts' );

function ign_voucher_scripts() { 
	global $ignite_gift_certs;
	
	wp_enqueue_script( 'ign_voucher_select2_js', $ignite_gift_certs->plugin_url . '/assets/js/select2/select2.min.js' );
	
	wp_register_style( 'ign_voucher_select2_css', $ignite_gift_certs->plugin_url . '/assets/js/select2/select2.css', false, current_time( 'timestamp' ) );
	
        wp_enqueue_style( 'ign_voucher_select2_css' );

}


/**
 * Add and remove meta boxes from the Voucher edit page and Order edit page
 *
 * @since 3.3

 */
function ign_vouchers_meta_boxes() {

	// Voucher Primary Image box
	add_meta_box(
		'woocommerce-voucher-image',
		__( 'Primary Voucher Image <small>&ndash; Used to lay out the voucher fields found in the Voucher Data box.</small>', 'ignitewoo_gift_certs' ),
		'ign_voucher_image_meta_box',
		'ign_voucher',
		'normal',
		'high'
	);

	// Voucher Data box
	add_meta_box(
		'woocommerce-voucher-data',
		__( 'Voucher Data', 'ignitewoo_gift_certs' ),
		'ign_voucher_data_meta_box',
		'ign_voucher',
		'side',
		'default'
	);
	
	// Voucher Generator box
	add_meta_box(
		'woocommerce-voucher-gen',
		__( 'Create printable voucher', 'ignitewoo_gift_certs' ),
		'ign_voucher_gen_meta_box',
		'ign_voucher',
		'normal',
		'default'
	);

	/* NOT CURRENTLY IMPLEMENTED
	// Voucher alternative images box
	add_meta_box(
		'woocommerce-voucher-alternative-images',
		__( 'Alternative Images <small>&ndash; Optional alternative images with the same layout and dimensions as the primary image, which your customers may choose from.</small>', 'ignitewoo_gift_certs' ),
		'ign_voucher_alternative_images_meta_box',
		'ign_voucher',
		'normal',
		'high'
	);
	*/
	
	// Voucher additional image box
	add_meta_box(
		'woocommerce-voucher-additional-images',
		__( 'Additional Image <small>&ndash; Optional image with the same dimensions as the primary voucher image, that will be added as a second page to the voucher.</small>', 'ignitewoo_gift_certs' ),
		'ign_voucher_additional_images_meta_box',
		'ign_voucher',
		'normal',
		'high'
	);

	// Admin Edit Order Voucher Meta Box
	add_meta_box(
		'woocommerce-order-vouchers',
		__( 'Vouchers', 'ignitewoo_gift_certs' ),
		'ign_order_vouchers_meta_box',
		'shop_order',
		'normal',
		'default' );

	// remove unnecessary meta boxes
	remove_meta_box( 'woothemes-settings', 'ign_voucher', 'normal' );
	remove_meta_box( 'commentstatusdiv',   'ign_voucher', 'normal' );
	remove_meta_box( 'slugdiv',            'ign_voucher', 'normal' );
}

add_filter( 'enter_title_here', 'ign_vouchers_enter_title_here', 1, 2 );

/**
 * Set a more appropriate placeholder text for the New Voucher title field
 *
 * @since 3.3
 * @param string $text "Enter Title Here" string
 * @param object $post post object
 *
 * @return string "Voucher Name" when the post type is ign_voucher
 */
function ign_vouchers_enter_title_here( $text, $post ) {
	if ( 'ign_voucher' == $post->post_type ) return __( 'Voucher Name', 'ignitewoo_gift_certs' );
	return $text;
}


add_action( 'save_post', 'ign_vouchers_meta_boxes_save', 1, 2 );

/**
 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
 *
 * @since 3.3
 * @param int $post_id post identifier
 * @param object $post post object
 */
function ign_vouchers_meta_boxes_save( $post_id, $post ) {
	global $woocommerce;
	
	if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( is_int( wp_is_post_revision( $post ) ) ) return;
	if ( is_int( wp_is_post_autosave( $post ) ) ) return;
	if ( empty($_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;
	if ( 'ign_voucher' != $post->post_type ) return;

	do_action( 'woocommerce_process_ign_voucher_meta', $post_id, $post );

	//if ( version_compare( $woocommerce->version, '2.1' ,'<' ) ) 
	//	woocommerce_meta_boxes_save_errors();
	//else
	//	wc_meta_boxes_save_errors();
	
}



add_action( 'publish_ign_voucher', 'ign_voucher_private', 10, 2 );


/**
 * Automatically make the voucher posts private when they are published.
 * That way we can have them be publicly_queryable for the purposes of
 * generating a preview pdf for the admin user, while having them always
 * hidden on the frontend (draft posts are not visible by definition)
 *
 * @since 3.3
 * @param int $post_id the voucher identifier
 * @param object $post the voucher object
 */
function ign_voucher_private( $post_id, $post ) {
	global $wpdb;

	$wpdb->update( $wpdb->posts, array( 'post_status' => 'private' ), array( 'ID' => $post_id ) );
}


/**
 * Rendres a custom admin input field to select a font which includes font
 * family, size and style (bold/italic)
 *
 * @since 3.3
 */
function ign_vouchers_wp_font_select( $field ) {
	global $thepostid, $post, $woocommerce;

	if ( ! $thepostid ) $thepostid = $post->ID;

	// values
	$font_family_value = $font_size_value = $font_style_value = '';

	if ( '_voucher' == $field['id'] ) {
		// voucher defaults
		$font_family_value = get_post_meta( $thepostid, $field['id'] . '_font_family', true );
		
		$font_size_value   = get_post_meta( $thepostid, $field['id'] . '_font_size',   true );
		
		$font_style_value  = get_post_meta( $thepostid, $field['id'] . '_font_style',  true );
		
	} else {
		// field-specific overrides
		$voucher_fields = get_post_meta( $thepostid, '_voucher_fields', true );

		$field_name = ltrim( $field['id'], '_' );

		if ( is_array( $voucher_fields ) ) {
		
			if ( isset( $voucher_fields[ $field_name ]['font']['family'] ) ) 
				$font_family_value = $voucher_fields[ $field_name ]['font']['family'];
			else 
				$font_family_value = 'Helvetica';
			
			if ( isset( $voucher_fields[ $field_name ]['font']['size'] ) )   
				$font_size_value   = $voucher_fields[ $field_name ]['font']['size'];
			else 
				$font_size_value = '48';

			if ( isset( $voucher_fields[ $field_name ]['font']['style'] ) )  
				$font_style_value  = $voucher_fields[ $field_name ]['font']['style'];
		}
	}

	// defaults
	if ( empty( $font_size_value ) && isset( $field['font_size_default'] ) ) 
		$font_size_value = $field['font_size_default'];


	if ( empty( $font_family_value ) )
		$font_family_value = 'Helvetica';


	echo '<p class="form-field ' . $field['id'] . '_font_family_field"><label for="' . $field['id'] . '_font_family">' . $field['label'] . '</label> <select id="' . $field['id'] . '_font_family" name="' . $field['id'] . '_font_family" class="select short">';

	foreach ( $field['options'] as $key => $value ) {
		echo '<option value="' . $key . '" ';
		selected( $font_family_value, $key );
		echo '>' . $value . '</option>';
	}

	echo '</select> ';

	echo '<br/><div class="font_wrapper">' . __( 'Size', 'ignitewoo_gift_certs' ) . '<input type="text" style="width:auto;margin-left:10px;" size="2" name="' . $field['id'] . '_font_size" id="' . $field['id'] . '_font_size" value="' . esc_attr( $font_size_value ) . '" placeholder="' . __( 'Size', 'ignitewoo_gift_certs' ) . '" /> pt';

	echo '<br/><label for="' . $field['id'] . '_font_style_b" style="width:auto;margin:0 5px 0 0px;">' . __( 'Bold', 'ignitewoo_gift_certs' ) . '</label> <input type="checkbox" class="checkbox" style="margin-top:4px;" name="' . $field['id'] . '_font_style_b" id="' . $field['id'] . '_font_style_b" value="yes" ';
	checked( false !== strpos( $font_style_value, 'B' ), true );
	echo ' /> ';

	echo '<br/><label for="' . $field['id'] . '_font_style_i" style="width:auto;margin:0 5px 0 0px;">' . __( 'Italic', 'ignitewoo_gift_certs' ) . '</label> <input type="checkbox" class="checkbox" style="margin-top:4px;" name="' . $field['id'] . '_font_style_i" id="' . $field['id'] . '_font_style_i" value="yes" ';
	checked( false !== strpos( $font_style_value, 'I' ), true );
	echo ' />';
	
	
	$align = isset( $field['align'] ) ? $field['align'] : 'left';

	?>
	
	<br>
	<label><?php _e( 'Align', 'ignitewoo_gift_certs' ) ?></label>
	<select name="<?php echo $field['id'] ?>_text_align" id="<?php echo $field['id'] ?>_text_align">
		<option value="L" <?php selected( $align, 'L', true ) ?>><?php _e( 'Left', 'ignitewoo_gift_certs' ) ?></option>
		<option value="C" <?php selected( $align, 'C', true ) ?>><?php _e( 'Center', 'ignitewoo_gift_certs' ) ?></option>
		<option value="R" <?php selected( $align, 'R', true ) ?>><?php _e( 'Right', 'ignitewoo_gift_certs' ) ?></option>
	</select>
	
	<?php

	echo '</div> ';

	echo '</p>';
}


/**
 * Add inline javascript to activate the farbtastic color picker element.
 * Must be called in order to use the ign_vouchers_wp_color_picker() method
 *
 * @since 3.3
 */
function ign_vouchers_wp_color_picker_js() {
	global $woocommerce;

	ob_start();
	?>
	
	$(".colorpick").wpColorPicker();

	$(document).mousedown(function(e) {
		if ($(e.target).hasParent(".wp-picker-holder"))
			return;
		if ($( e.target ).hasParent("mark"))
			return;
		$(".wp-picker-holder").each(function() {
			$(this).fadeOut();
		});
	});
	<?php
	$javascript = ob_get_clean();
	
	if ( function_exists( 'wc_enqueue_js' ) )
		wc_enqueue_js( $javascript );
	else 
		$woocommerce->add_inline_js( $javascript );
}


/**
 * Renders a custom admin control used on the voucher edit page to Set/Remove
 * the position via two buttons
 *
 * @since 3.3
 */
function ign_vouchers_wp_position_picker( $field ) {
	global $woocommerce;

	if ( ! isset( $field['value'] ) ) $field['value'] = '';

	//echo '<p class="form-field"><label>' . $field['label'] . '</label><labelname style="display:none">' . $field['labelname'] . '</labelname><input type="button" id="' . $field['id'] . '" class="set_position button" value="' . esc_attr__( 'Set', 'ignitewoo_gift_certs' ) . '" style="width:auto;" /> <input type="button" id="remove_' . $field['id'] . '" class="remove_position button" value="' . esc_attr__( 'Remove', 'ignitewoo_gift_certs' ) . '" style="width:auto;' . ( $field['value'] ? '' : 'display:none' ) . ';margin-left:7px;" />';

	echo '<p class="form-field"><labelname style="display:none">' . $field['labelname'] . '</labelname><input type="button" id="' . $field['id'] . '" class="set_position button" value="' . esc_attr__( 'Set Position', 'ignitewoo_gift_certs' ) . '" style="width:auto;" /> <input type="button" id="remove_' . $field['id'] . '" class="remove_position button" value="' . esc_attr__( 'Remove', 'ignitewoo_gift_certs' ) . '" style="width:auto;' . ( $field['value'] ? '' : 'display:none' ) . ';margin-left:7px;" />';
	
	if ( isset( $field['description'] ) && $field['description'] ) {

		if ( isset( $field['desc_tip'] ) ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" />';
		} else {
			echo '<br/><span class="description">' . $field['description'] . '</span>';
		}
	}
	echo '</p>';
}
