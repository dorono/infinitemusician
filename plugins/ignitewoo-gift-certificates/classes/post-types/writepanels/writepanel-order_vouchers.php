<?php
/**
 * Copyright (c) 2012-2013, IgniteWoo.com
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );

function ign_order_vouchers_meta_box( $post ) {
	global $woocommerce, $wpdb;

	$sql = 'select ID, post_title from ' . $wpdb->posts . ' 
		left join ' . $wpdb->postmeta . ' m1 on ID = m1.post_id 
		left join ' . $wpdb->postmeta . ' m2 on ID = m2.post_id
		where
		post_type="shop_coupon"
		and
		post_status="publish"
		and
		( m1.meta_key = "associated_order_id" and m1.meta_value = "' . $post->ID . '" )
		and 
		( m2.meta_key = "discount_type" and m2.meta_value="ign_store_credit" )
		order by ID asc
		';

	$vouchers = $wpdb->get_results( $sql );
	
	?>

	<div class="inside">
	
		<?php
		if ( empty( $vouchers ) ) { 
			
			echo '<p style="margin-left: 1.5em">' . __( 'No vouchers in this order', 'ignitewoo_gift_certs' ) . '</p>';
			
			return;
			
		} 
		?>
		
		<table class="widefat">
			<tr>
				<th width="2%"><?php _e( 'Template', 'ignitewoo_gift_certs' ); ?></th>
				<th><?php _e( 'Voucher code', 'ignitewoo_gift_certs' ); ?></th>
				<th><?php _e( 'Balance', 'ignitewoo_gift_certs' ); ?></th>
				<th><?php _e( 'Expires', 'ignitewoo_gift_certs' ); ?></th>
			<tr>
			
		<?php
		foreach( $vouchers as $v ) { 
			?>
			<tr>
				<td>
					<?php 
					
					$vtid = get_post_meta( $v->ID, 'voucher_template_id', true );
					
						if ( !empty( $vtid ) && has_post_thumbnail( $vtid ) ) {
							$image = get_the_post_thumbnail( $vtid, array( 75, 0 ) );
						} else {
							$image = '<img src="' . woocommerce_placeholder_img_src() . '" alt="Placeholder" />';
						}
					?>
					
					<a target="_blank" href="<?php echo esc_url( admin_url( 'post.php?post='. $v->ID . '&action=edit' ) ); ?>" class="tips" data-tip="<?php echo '<strong>' . __( 'Edit Voucher ID', 'ignitewoo_gift_certs' ).'</strong> ' . $v->ID ?>">
						<?php echo $image ?>
					</a>
				</td>
				<td><?php echo $v->post_title ?></td>
				<td>
					<input type="text" class="voucher_balance" name="voucher[<?php echo $v->ID ?>]" value="<?php echo get_post_meta( $v->ID, 'coupon_amount', true ) ?>" > 
					<button class="button vbutton" name="vbutton" data-voucherid="<?php echo $v->ID ?>"><?php _e( 'Update', 'ignitewoo_gift_certs' )?></button>
				</td>
				<td>
					<?php 
						$exp = get_post_meta( $v->ID, 'expiry_date', true );
						
						if ( empty( $exp ) )
							_e( 'Never', 'ignitewoo_gift_certs' );
						else
							echo $exp;
					?>
				</td>
			</tr>
			<?php
			
		}
		?>
		</table>
		
		<?php
		
			$nonce = wp_create_nonce( 'ign_update_voucher_balance' );

		?>
		
		<script src="<?php echo $woocommerce->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI.min.js' ?>"></script>
		
		<script>
		jQuery( document ).ready( function( $ ) {
		
			$( '.vbutton' ).click( function( e ) { 
			
				e.preventDefault();
				
				var btn = $( this );
				
				var ajax_loader_url = '<?php echo $woocommerce->plugin_url() . '/assets/images/ajax-loader@2x.gif' ?>';
				
				btn.block({message: null, overlayCSS: {background: '#fff url(' + ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});

				var vid = $( this ).data( 'voucherid' );
				
				if ( null == vid || NaN == vid )
					return false;
					
				var balance = $( this ).parent().find( '.voucher_balance' ).val();
				
				if ( null == balance || NaN == balance )
					return false;
					
				$.post( ajaxurl, { action: 'update_voucher_balance', vid: vid, balance: balance, n: '<?php echo $nonce ?>' }, function() { 
					btn.unblock();
				});
			});
			
			return false;
		})
		</script>
	</div>

	<?php

}
