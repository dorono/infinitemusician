<?php
/**
 * Email Order Items
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$text_align = is_rtl() ? 'right' : 'left';

foreach ( $items as $item_id => $item ) :
	if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
		$product = $item->get_product();
		?>
		<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
			<td class="order_items_table_td_style order_items_table_td order_items_table_td_left order_items_table_td_product">
				
				<table class="order_items_table_product_details_inner" cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<?php
						// Show image.
						$show_image = ( 'yes' == get_option( 'ec_deluxe_all_order_item_table_thumbnail' ) );
						if ( $show_image && is_object( $product ) && $product->get_image_id() ) {
							?>
							<td class="order_items_table_product_details_inner_td order_items_table_product_details_inner_td_image">
								<?php echo apply_filters( 'woocommerce_order_item_thumbnail', '<span style="margin-bottom: 5px"><img src="' . ( $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'email-control' ) . '" width="' . esc_attr( $image_size[0] ) . '" style="vertical-align:middle; margin-' . ( is_rtl() ? 'left' : 'right' ) . ': 10px;" /></span>', $item ); ?>
							</td>
							<?php
						}
						?>
						<td class="order_items_table_product_details_inner_td order_items_table_product_details_inner_td_text" width="100%">
							
							<div class="order_items_table_product_details_inner_title">
								<?php
								// Product name
								echo apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false );
								
								// SKU
								if ( $show_sku && is_object( $product ) && $product->get_sku() ) {
									echo ' (#' . $product->get_sku() . ')';
								}
								?>
							</div>
							
							<?php
							// allow other plugins to add additional product information here
							do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );
							
							// Variation/Meta
							echo wc_display_item_meta( $item, array(
								'before'    => '<div class="wc-item-meta"><div>',
								'separator'	=> '</div><div>',
								'after'		=> '</div></div>',
								'echo'		=> false,
								'autop'		=> false,
							) );
				
							// File URLs
							if ( $show_download_links ) {
								wc_display_item_downloads( $item );
							}
							
							// allow other plugins to add additional product information here
							// plain_text check is required as was only passed as an arg to `order-items` since WC2.5.4
							do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );
							?>
							
						</td>
					</tr>
				</table>
				
			</td>
			<td class="order_items_table_td_style order_items_table_td order_items_table_td_product"><?php echo apply_filters( 'woocommerce_email_order_item_quantity', $item->get_quantity(), $item ); ?></td>
			<td class="order_items_table_td_style order_items_table_td order_items_table_td_right order_items_table_td_product" style="text-align:right"><?php echo $order->get_formatted_line_subtotal( $item ); ?></td>
		</tr>
		<?php
	}

	if ( $show_purchase_note && is_object( $product ) && ( $purchase_note = $product->get_purchase_note() ) ) : ?>
		<tr>
			<td colspan="3" class="order_items_table_td_style order_items_table_td order_items_table_td_both order_items_table_td_product"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></td>
		</tr>
	<?php endif; ?>

<?php endforeach; ?>
