<?php
/**
 * Copyright (c) 2012-2013, IgniteWoo.com
 * Copyright (c) 2012-2013, SkyVerge, Inc.
 *
 * GPL v3
 */

/**
 * Functions for displaying the voucher data meta box
 *
 * @since 3.3
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );


function ign_voucher_gen_meta_box() { 
	global $post, $wpdb;
	
	$sql = 'select ID, post_title as code from ' . $wpdb->posts . ' left join ' . $wpdb->postmeta . ' on ID = post_id 
		where post_type="shop_coupon" and meta_key="discount_type"  and meta_value="ign_store_credit" and post_status="publish"';
		
	$res = $wpdb->get_results( $sql );
	
	if ( empty( $res ) || is_wp_error( $res ) ) { 
	
		?>
		<div id="voucher_image_wrapper" style="position:relative;">
			<?php _e( 'No gift certificate coupons exist yet. You must create one before manually creating a printable voucher. Navigate to WooCommerce -> Coupons to do so.', 'ignite_gift_certs' ) ?>
		</div>
		
		<?php
		
		return;
	}
		
	?>
	<div id="voucher_image_wrapper">
		<p><?php _e( 'You can manually generate a gift certificate from existing gift certificate / store credit coupons.', 'ignitewoo_gift_certs' ) ?></p>
		<p><?php _e( 'To do so select a coupon code, fill in the fields as you see fit, and click to generate.', 'ignitewoo_gift_certs' ) ?></p>
		<select name="gen[code]" class="voucher_code_select" data-placeholder="<?php _e( 'Select a coupon code', 'ignitewoo_gift_certs' ) ?>" style="width:250px;height:28px">
			<option value=""></option>
			<?php foreach( $res as $r ) { ?>
			<option value="<?php echo $r->ID ?>"><?php _e( 'ID #', 'ignitewoo_gift_certs' ); echo $r->ID ?> - <?php echo $r->code ?></option>
			<?php } ?>
		</select>
		<p><label><?php _e( 'Recipient Name', 'ignitewoo_gift_certs' ) ?></label><br/><input type="text" name="gen[recipient_name]" value=""></p>
		<p><label><?php _e( 'Product Name', 'ignitewoo_gift_certs' ) ?></label><br/><input type="text" name="gen[product_name]" value=""></p>
		<p><label><?php _e( 'Short Description', 'ignitewoo_gift_certs' ) ?></label><br/><textarea name="gen[product_desc]" style="min-width:250px"></textarea></p>
		<p><label><?php _e( 'SKU', 'ignitewoo_gift_certs' ) ?></label><br/><input type="text" name="gen[product_sku]" value=""></p>
		<p><label><?php _e( 'Message', 'ignitewoo_gift_certs' ) ?></label><br/><textarea name="gen[message]" style="min-width:250px"></textarea></p>
		<p><input type="submit" value="<?php _e( 'Generate PDF', 'ignitewoo_gift_certs' )?>" class="button button-large" name="gen_pdf_voucher"></p>
		
		<style>
			.select2-container .select2-choice > .select2-chosen {
				height: 26px;
				margin-top: 5px;
			}
		</style>
	</div>
	<?php
}


add_action( 'admin_init', 'ign_maybe_gen_voucher_from_post_type', 99999 );

function ign_maybe_gen_voucher_from_post_type( ) {
	global $ignite_gift_certs, $post;

	if ( empty( $_POST['gen_pdf_voucher'] ) )
		return;
		
	if ( empty( $_POST['gen'] ) || !is_array( $_POST['gen'] ) )
		return;

	if ( empty( $_POST['post_ID'] ) )
		return;
		
	$post = get_post( $_POST['post_ID'] );
	
	if ( empty( $post ) || is_wp_error( $post ) || !is_object( $post ) )
		return;
		
	//$post_type = get_query_var( 'post_type' );

	//if ( 'ign_voucher' == $post_type && strpos( $locate, 'single.php' ) ) {
		$template = $ignite_gift_certs->plugin_path . '/templates/single-ign_voucher.php';
	//}
	
	require_once( $template );
	
//echo $locate; die;
	//return $locate;
}