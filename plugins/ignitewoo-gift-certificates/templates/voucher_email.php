<?php if ( !defined('ABSPATH' ) ) die; ?>

<?php do_action('woocommerce_email_header', $heading ); ?>

<p><?php _e( 'This message is from', 'ignitewoo_gift_certs' )?><?php echo ' ' . $sitename; ?>!</p>

<p> <?php _e( 'Hi there', 'ignitewoo_gift_certs' ); ?> <?php echo $recipient_name ?>, </p>

<?php echo $message; ?>

<p><?php _e( 'To redeem your voucher use the following coupon code during checkout:', 'ignitewoo_gift_certs' ); ?></p>

<strong style="margin: 12px 0; font-size: 1.2em; font-weight: bold; display: block; text-align: center;">
	<?php echo $voucher_code; ?>
</strong>

<?php
/** 
For developers: 

$pid - contains the product ID

	So for example to load the product title and print it in the email do this:

	$title = get_the_title( $pid );

	echo $title;

$order - contains the entire order object

$order_id - contains the order ID number

$item - contains the order item

	So, if you want to load the product related to the item:
	
	$data = $order->get_product_from_item( $item );
	
IgniteWoo does not offer support for customizations. But we'll answer basic customization questions if we can. 

*/


	// New for v3.3 - detect if the message is going to have a PDF voucher attached and if so inform the recipient.
	if ( $has_pdf ) { 
		?>
		
		<p><strong>
			
			<?php
				_e( 'A printable PDF version of your voucher is attached to this message.', 'ignitewoo_gift_certs' );

			?>

		</strong></p>
		
		<?php 
	
	}

	
	if ( !empty( $voucher_expires ) ) {
		?>
		
		<p><em>
			
			<?php
				_e( 'Note that this voucher expires on ', 'ignitewoo_gift_certs' );
			
				echo $voucher_expires;
			?>

		</em></p>
		
		<?php 
	}
?>

<div style="clear:both;"></div>

<?php // this prints the QR code image when that option is enabled. ?>
<?php do_action( 'ignite_gc_qrcode', $order_id, $preview, $voucher_code )  ?>

<?php do_action('woocommerce_email_footer'); ?>