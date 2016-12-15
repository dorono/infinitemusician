<?php
/**
 * Copyright (c) 2012-2013, IgniteWoo.com
 * Copyright (c) 2012-2013, SkyVerge, Inc.
 *
 * GPL v3
 */

/**
 * Functions for displaying the voucher primary image meta box
 *
 * @since 3.3
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );

/**
 * Display the voucher image meta box
 * Fluid image reference: http://unstoppablerobotninja.com/entry/fluid-images
 *
 * @since 3.3
 */
function ign_voucher_image_meta_box() {
	global $post, $woocommerce;

	$image_src = '';
	$image_id  = '';

	$image_ids = get_post_meta( $post->ID, '_image_ids', true );

	if ( is_array( $image_ids ) && count( $image_ids ) > 0 ) {
		$image_id = $image_ids[0];
		$image_src = wp_get_attachment_url( $image_id );
	}

	$attachment = wp_get_attachment_metadata( $image_id );
	

	?>
	<div id="voucher_image_wrapper" style="position:relative;">
		<img id="voucher_image_0" src="<?php echo $image_src ?>" style="max-width:100%;" />
	</div>
	<input type="hidden" name="upload_image_id[0]" id="upload_image_id_0" value="<?php echo $image_id; ?>" />
	<p>
		<a title="<?php esc_attr_e( 'Set voucher image', 'ignitewoo_gift_certs' ) ?>" href="#" id="set-voucher-image"><?php _e( 'Set voucher image', 'ignitewoo_gift_certs' ) ?></a>
		<a title="<?php esc_attr_e( 'Remove voucher image', 'ignitewoo_gift_certs' ) ?>" href="#" id="remove-voucher-image" style="<?php echo ( ! $image_id ? 'display:none;' : '' ); ?>"><?php _e( 'Remove voucher image', 'ignitewoo_gift_certs' ) ?></a>
	</p>
	<?php
}
