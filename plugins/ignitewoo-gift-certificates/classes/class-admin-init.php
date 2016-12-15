<?php
/*
Copyright (c) 2012-2015 IgniteWoo.com - All Rights Reserved
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );
	
global $post, $woocommerce, $ignite_gift_certs;

//add_action( 'woocommerce_after_order_itemmeta', 'ign_voucher_maybe_add_order_item_print', 1, 3 );

function ign_voucher_maybe_add_order_item_print( $item_id = null , $item = null, $_product = null ) { 

	if ( empty( $item_id ) || empty( $_product ) )
		return;

	$meta = woocommerce_get_order_item_meta( $item_id, '_voucher_recievers', true );
	
	var_dump( $code, $meta ); return;
	
	if ( empty( $meta ) )
		return;
	
	foreach( $meta as $k => $v ) {	
	
		$code = $meta['voucher_code'];

		if ( empty( $code ) )
			continue;
			
		
	
		
	?>
	<div class="gen_voucher">
	
		<div class="gen_voucher_parms" style="display:none">
			<input name="gen[code]" type="hidden" value="<?php echo $code ?>">
		
			<input type="text" name="gen[recipient_name]" value="<?php echo $meta['voucher_to_name'] ?>">
			<input type="text" name="gen[product_name]" value="<?php echo $_product->post_title ?>">
			<textarea name="gen[product_desc]" style="min-width:250px"><?php echo $_product->post_excerpt ?></textarea>
			<input type="text" name="gen[product_sku]" value="<?php echo $_product->sku ?>">
			<textarea name="gen[message]" style="min-width:250px"><?php echo $meta['voucher_message'] ?></textarea>
		</div>
		
		<input type="submit" value="<?php _e( 'Generate PDF', 'ignitewoo_gift_certs' )?>" class="button button-large" name="gen_pdf_voucher"></p>
	
	</div>
	
	<?php 
	
	}
}

$screen = get_current_screen();

if ( 'ign_voucher' == $screen->id ) {

	// get the primary image dimensions (if any) which are needed for the page script
	$attachment = null;

	$image_ids = get_post_meta( $post->ID, '_image_ids', true );

	if ( is_array( $image_ids ) && isset( $image_ids[0] ) && $image_ids[0] ) {
		$attachment = wp_get_attachment_metadata( $image_ids[0] );
	}

	// default js params
	$vouchers_js_params = array( 'primary_image_width' => '', 'primary_image_height' => '' );

	$vouchers_js_params = array(
		'done_label'           => __( 'Done', 'ignitewoo_gift_certs' ),
		'set_position_label'   => __( 'Set Position', 'ignitewoo_gift_certs' ),
		'post_id'              => $post->ID,
		'primary_image_width'  => isset( $attachment['width']  ) && $attachment['width']  ? $attachment['width']  : '0',
		'primary_image_height' => isset( $attachment['height'] ) && $attachment['height'] ? $attachment['height'] : '0',
	);

	wp_enqueue_script( 'ign_vouchers_admin', $this->plugin_url . '/assets/js/voucher.js', array( 'jquery' ) );

	wp_localize_script( 'ign_vouchers_admin', 'voucher_js_params', $vouchers_js_params );

	wp_enqueue_script( 'wp-color-picker' );

	wp_enqueue_style( 'wp-color-picker' );

	wp_enqueue_media();

	wp_enqueue_script( 'imgareaselect' );

	wp_enqueue_style( 'imgareaselect' );

	// Need the WC admin styles loads
	wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );

	wp_enqueue_style( 'woocommerce_vouchers_admin_styles', $ignite_gift_certs->plugin_url . '/assets/css/voucher.css' );

	//wp_enqueue_script( 'woocommerce_writepanel' );
}



