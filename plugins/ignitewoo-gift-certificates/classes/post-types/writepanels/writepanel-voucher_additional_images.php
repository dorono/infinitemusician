<?php
/**
 * Copyright (c) 2012-2013, IgniteWoo.com
 * Copyright (c) 2012-2013, SkyVerge, Inc.
 *
 * GPL v3
 */

/**
 * Functions for displaying the voucher additional images meta box
 *
 * @since 3.3
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );

/**
 * Render the voucher additional (second page) image meta box.  This box
 * allows a second page, image to be added or removed.
 *
 * @since 3.3
 */
function ign_voucher_additional_images_meta_box() {
	global $post, $woocommerce;

	$image_src = '';
	$image_id = '';
	$image_ids = get_post_meta( $post->ID, '_additional_image_ids', true );

	if ( is_array( $image_ids ) && count( $image_ids ) > 0 ) {
		$image_id = $image_ids[0];
		$image_src = wp_get_attachment_url( $image_id );
	}

	?>
	<div>
		<img id="voucher_additional_image" src="<?php echo $image_src ?>" style="max-width:100px;max-height:100px;" />
	</div>
	<input type="hidden" name="upload_additional_image_id[0]" id="upload_additional_image_id_0" value="<?php echo $image_id; ?>" />
	<p>
		<a title="<?php esc_attr_e( 'Set additional image', 'ignitewoo_gift_certs' ) ?>" href="#" id="set-additional-image" style="<?php echo ( $image_id ? 'display:none;' : '' ); ?>"><?php _e( 'Set additional image', 'ignitewoo_gift_certs' ) ?></a>
		<a title="<?php esc_attr_e( 'Remove additional image', 'ignitewoo_gift_certs' ) ?>" href="#" id="remove-additional-image" style="<?php echo ( ! $image_id ? 'display:none;' : '' ); ?>"><?php _e( 'Remove additional image', 'ignitewoo_gift_certs' ) ?></a>
	</p>
	<?php
}


add_action( 'woocommerce_process_ign_voucher_meta', 'ign_vouchers_process_voucher_additional_images_meta', 10, 2 );

/**
 * Voucher Additional Images Data Save
 *
 * Function for processing and storing voucher additional images
 *
 * @since 3.3
 * @param int $post_id the voucher id
 * @param object $post the voucher post object
 */
function ign_vouchers_process_voucher_additional_images_meta( $post_id, $post ) {
	$additional_image_ids = $_POST['upload_additional_image_id'][0] ? $_POST['upload_additional_image_id'] : array();
	update_post_meta( $post_id, '_additional_image_ids', $additional_image_ids );
}
