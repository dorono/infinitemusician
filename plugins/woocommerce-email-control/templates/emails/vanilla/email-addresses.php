<?php
/**
 * Email Addresses
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<table class="addresses" cellpadding="0" cellspacing="0" border="0" align="center">
	<tr>
		<td class="addresses-td" width="50%" valign="top" class="addresses-td">
			<h3><?php _e( "Billing Address", 'email-control') ?></h3>
			<p><?php echo $order->get_formatted_billing_address(); ?></p>
		</td>
		<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && ( $shipping = $order->get_formatted_shipping_address() ) ) : ?>
			<td class="addresses-td" width="50%" valign="top" class="addresses-td">
				<h3><?php _e( "Shipping Address", 'email-control') ?></h3>
				<p><?php echo $shipping; ?></p>
			</td>
		<?php endif; ?>
	</tr>
</table>
