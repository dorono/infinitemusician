<?php
/**
 * Copyright (c) 2012-2013, IgniteWoo.com
 * Copyright (c) 2012-2013, SkyVerge, Inc.
 *
 * GPL v3
 */

/**
 * Functions for displaying the voucher alternative images meta box
 *
 * @since 3.3
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );

/**
 * Render the voucher alternative images meta box.  This box allows alternative
 * images, with the same dimensions/layout as the main voucher image, to be added,
 * removed, and displayed in a little gallery
 *
 * @since 3.3
 */
function ign_voucher_alternative_images_meta_box() {
	global $post, $woocommerce;

	$image_ids = get_post_meta( $post->ID, '_image_ids', true );

	?>
	<?php

	echo '<ul id="voucher_alternative_images">';
	if ( is_array( $image_ids ) ) {
		for ( $i = 1, $ix = count( $image_ids ); $i < $ix; $i++ ) {

			$image_src = wp_get_attachment_url( $image_ids[ $i ] );
			?>
			<li class="alternative_image"><a href="#" class="remove-alternative-voucher-image"><img style="max-width:100px;max-height:100px;" src="<?php echo $image_src ?>" /><input type="hidden" name="upload_image_id[<?php echo $i ?>]" class="upload_image_id" value="<?php echo $image_ids[ $i ] ?>" /><span class="overlay"></span></a></li>
			<?php
		}
	}
	echo '</ul>';

	?>
	<p style="clear:left;">
		<a title="<?php esc_attr_e( 'Add an alternative voucher image', 'ignitewoo_gift_certs' ) ?>" href="#" id="add-alternative-voucher-image"><?php _e( 'Add an alternative voucher image', 'ignitewoo_gift_certs' ) ?></a>
	</p>
	<?php
}


add_action( 'woocommerce_process_ign_voucher_meta', 'ign_vouchers_process_voucher_alternative_images_meta', 10, 2 );

/**
 * Voucher Alternative Images Data Save
 *
 * Function for processing and storing voucher alternative images
 *
 * @since 3.3
 * @param int $post_id the voucher id
 * @param object $post the voucher post object
 */
function ign_vouchers_process_voucher_alternative_images_meta( $post_id, $post ) {

	// handle the special image_ids meta, which will always have at least an index 0 for the main template image, even if the value is empty
	$image_ids = array();
	foreach ( $_POST['upload_image_id'] as $i => $image_id ) {
		if ( 0 == $i || $image_id ) {
			$image_ids[] = $image_id;
		}
	}
	update_post_meta( $post_id, '_image_ids', $image_ids );

	if ( $image_ids[0] )
		set_post_thumbnail( $post_id, $image_ids[0] );
	else
		delete_post_thumbnail( $post_id );
}
