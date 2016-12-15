
</div>
</div>

<?php /** You can optionally move this CSS to your theme and delete it here if you're customizing this template */ ?>

<style>
.gift_cert_field_wrapper {
	background-color: #FFFFE0;
	border: 1px dotted #ccc;
	padding: 10px;
	margin-bottom: 10px;
}
/** New CSS */
.voucher_image_option {
	float: left;
	margin: 5px;
	padding: 2px;
	text-align: center;
}
.voucher_image_option img {
	display:block;
	margin-bottom: 0.5em;
}
.voucher_image_option input {
	clear:both;
}
</style>


<div class="gift-certificate">

	<div class="receiver-form">

	<h3><?php _e( "Gift Certificate / Store Credit / Coupon receiver's details", 'ignitewoo_gift_certs' ) ?></h3>
	
	<p><?php _e( 'Leave the recipient name and email address blank to receive the voucher yourself. Or, enter details below to send the voucher(s) to someone else.', 'ignitewoo_gift_certs' ) ?></p>

	<?php $x = 0; ?>
	
	<?php foreach ( $woocommerce->cart->cart_contents as $prod ) { ?>

		<?php // New as of v3.4 ?>
		<?php $voucher_styles = get_post_meta( $prod['product_id'], 'voucher_styles', true ) ?>
		
		<?php if ( !get_post_meta( $prod['product_id'], 'ignite_gift_enabled', true ) ) continue; ?>
		
		<?php
		// New as of v3.5.19
		// Is the setting on to override the product price? If so then then the voucher will be 
		// issued in an amount equal to the "Discount amount" set in the product, otherwise the 
		// the voucher will be issued in an amount equal to the product price. 
		// This setting has no effect when the product allows the shopper to set their own amount.
		$override_product_price = get_post_meta( $prod['product_id'], 'ignite_gc_sold_as_voucher', true );
		?>
		
		<?php for ( $i = 0; $i < $prod['quantity']; $i++ ) { ?>
		
			<?php 
			// New as of v3.3.6
			// This is set when customer sets their own price
			if ( !empty( $prod['gcp'] ) ) 
				$gc_price = $prod['gcp'];
			else if ( !empty( $prod['variation_id'] ) && $override_product_price )
				$gc_price = get_post_meta( $prod['product_id'], 'coupon_amount', true );
			else if ( !empty( $prod['variation_id'] ) && !$override_product_price )
				$gc_price = get_post_meta( $prod['variation_id'], '_regular_price', true );
			else if ( $override_product_price )
				$gc_price = get_post_meta( $prod['product_id'], 'coupon_amount', true );
			else 
				$gc_price = get_post_meta( $prod['product_id'], '_regular_price', true );

			?>
		
			<div class="gift_cert_field_wrapper">
		
				<?php /** DO NOT REMOVE ANY OF THESE FIELDS OR THE PLUGIN WILL NOT EMAIL CERTIFICATES CORRECTLY ! */ ?>
				
				<?php // MODIFIED IN v3.3.6 ?>
				<p id="order_comments_field" class="form-row notes">
					<?php echo sprintf( __( 'Recipient %s Amount', 'ignitewoo_gift_certs' ), ( $x+1 ) ) . ': ' . woocommerce_price( $gc_price ) ?>	
				</p>
				
				<p id="order_comments_field" class="form-row notes">
					<label class="" for="ign_receiver_name"><?php _e( 'Recipient Name', 'ignitewoo_gift_certs' ) ?></label>
					<input id="ign_receiver_name" type="text" name="ign_receiver_name[<?php echo $x ?>]" value="" />
				</p>

				<p id="order_comments_field" class="form-row notes">
					<label class="" for="ign_receiver_email"><?php _e( 'Recipient Email Address', 'ignitewoo_gift_certs' ) ?></label>
					<input id="ign_receiver_email" type="text" name="ign_receiver_email[<?php echo $x ?>]" value="" />
				</p>

				<p id="order_comments_field" class="form-row notes">
					<label class="" for="ign_receiver_message"><?php _e( 'Message to Recipient', 'ignitewoo_gift_certs' ) ?></label>
					<textarea rows="2" cols="5" id="ign_receiver_message" class="input-text" name="ign_receiver_message[<?php echo $x ?>]"></textarea>
				</p>
				
				<p>
				
				</p>
				
				<?php 

				// New as of v3.4 
				if ( !empty( $voucher_styles ) ) {
					
					// Only one voucher defined for this product? Set a hidden checkbox
					if ( count( (array)$voucher_styles ) == 1 ) { 
					
						echo '<input type="checkbox" name="ign_receiver_style[' . $x . ']" value="' . $voucher_styles[0] . '" checked="checked" style="display:none"> ';
						
					// More than one so list them and let the shopper pick one
					} else {
						
						?>
						
						<p id="order_comments_field" class="form-row notes">
						
						<label class="" for="ign_receiver_message"><?php _e( 'Select a voucher style &nbsp; (<em>required</em>)', 'ignitewoo_gift_certs' ) ?></label>
						
						<ul class="voucher_styles">
						
						<?php
						
						foreach( (array)$voucher_styles as $vs ) { 
						
							echo '<li style="list-style-type:none; display:inline-block; margin-right: 10px;"><div class="voucher_image_option">';
							
							$img = get_the_post_thumbnail( $vs, 'ignitewoo_voucher_thumb_size'  );
							
							// get voucher data
							$img_id = get_post_meta( $vs, '_image_ids', true );
							
							$full_size = wp_get_attachment_image_src( $img_id[0], 'large' ); 

							echo '<a title="' . __( 'Click to see a larger view', 'ignitewoo_gift_certs' ) .'" href="' . $full_size[0] .'" class="zoom" rel="prettyPhoto">' . $img . '</a>';
							
							echo '<input type="radio" name="ign_receiver_style[' . $x . ']" value="' . $vs . '"> ';
							
							echo '</div></li>';
						
						}
						
						?>
						
						</ul>
						</p>
						<script type="text/javascript" charset="utf-8">
							jQuery(document).ready(function($){
								$("a[rel^='prettyPhoto']").prettyPhoto();
							});
						</script>
					<?php 
					
					}
					
				}
				
				// Modified as of v3.5.19
				if ( $override_product_price ) { 
					?>
					<input type="hidden" name="ign_receiver_index[<?php echo $x ?>]" value="<?php echo $gc_price ?>">
					<?php
				} else { 
					?>
					<input type="hidden" name="ign_receiver_index[<?php echo $x ?>]" value="<?php echo ( $prod['line_total'] / $prod['quantity'] ) ?>">
					<?php 
				} 
				?>
				
			</div>
			
			<?php $x++; ?>
	
		<?php } ?>
	
	<?php } ?>
	
	<?php /** DO NOT REMOVE THIS FIELD OR THE PLUGIN WILL NOT EMAIL CERTIFICATES CORRECTLY!!!! */ ?>
	<input type="hidden" name="total_gift_cert_count" value="<?php echo $x ?>">


	
	