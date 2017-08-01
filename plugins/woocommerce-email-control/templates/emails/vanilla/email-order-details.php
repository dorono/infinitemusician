<?php
/**
 * Order details table shown in emails.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<p>&nbsp;</p>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td class="order_items_table_holder">
		
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td class="order-table-heading">
					
						<p>
							<span class="highlight"><?php _e( 'Order Number:', 'email-control' ) ?></span> 
							<?php if ( ! $sent_to_admin ) : ?>
								<?php echo $order->get_order_number(); ?>
							<?php else : ?>
								<a class="link" href="<?php echo esc_url( admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ) ); ?>"><?php printf( __( 'Order #%s', 'woocommerce'), $order->get_order_number() ); ?></a>
							<?php endif; ?>
						</p>
						
						<p>
							<span class="highlight"><?php _e( 'Order Date:', 'email-control' ) ?></span> 
							<?php printf( '<time datetime="%s">%s</time>', $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ); ?>
						</p>
						
					</td>
				</tr>
			</table>

			<table cellspacing="0" cellpadding="0" class="order_items_table" border="0" width="100%" >
				<?php if ( FALSE ) { ?>
					<thead>
						<tr>
							<th scope="col" class="order_items_table_th_style order_items_table_td order_items_table_td_top" width="80%"><?php _e( 'Product', 'email-control' ); ?></th>
							<th scope="col" class="order_items_table_th_style order_items_table_td order_items_table_td_top"><?php _e( 'Quantity', 'email-control' ); ?></th>
							<th scope="col" class="order_items_table_th_style order_items_table_td order_items_table_td_top" style="text-align:right"><?php _e( 'Price', 'email-control' ); ?></th>
						</tr>
					</thead>
				<?php } ?>
				<tbody>
					<?php echo wc_get_email_order_items( $order, array(
						'show_sku'      => $sent_to_admin,
						'show_image'    => FALSE,
						'image_size'    => array( 70, 70 ),
						'plain_text'    => $plain_text,
						'sent_to_admin' => $sent_to_admin
					) ); ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3">
							
							<table cellspacing="0" cellpadding="0" class="order_items_table" border="0" width="100%">
								<thead>
									<tr>
										
										<?php
											if ( $totals = $order->get_order_item_totals() ) {
												$i = 0;
												foreach ( $totals as $total ) {
													$i++;
													?>
													<tr class="order_items_table_total_row order_items_table_total_row_<?php echo esc_attr( sanitize_title( $total['label'] ) ) ?>">
														<th scope="row" class="order_items_table_totals_style order_items_table_td">
															<?php echo $total['label']; ?>
														</th>
														<td class="order_items_table_totals_style order_items_table_td">
															<?php echo $total['value']; ?>
														</td>
													</tr>
													<?php
												}
											}
										?>
									
									</tr>
								</thead>
							</table>
							
						</td>
					</tr>
				</tfoot>
			</table>
		
		</td>
	</tr>
</table>

<p>&nbsp;</p>

<?php
ob_start();
do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email );
$check_content = ob_get_clean();
if ( '' !== $check_content ) { ?>
	
	<?php echo $check_content; ?>
	
	<p>&nbsp;</p>
	
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td class="divider-line" align="center" valign="top">&nbsp;
				<!-- Divider -->
			</td>
		</tr>
	</table>
	
	<p>&nbsp;</p>
	
<?php } ?>
