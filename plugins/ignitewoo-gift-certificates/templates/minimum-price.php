<?php
/**
 * Single Product Minimum Price
 */

global $product;

$minimum = woocommerce_price( $product->minimum );

$html = sprintf( _x( '%s: %s', 'In case you need to change the order of Minimum Amount: $minimum', 'ignitewoo_gift_certs', 'ignitewoo_gift_certs' ), $product->minimum_text, $minimum );

?> 

<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
	
	<p itemprop="price" class="minimum-price"><?php echo $html; ?></p>
	
	<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />
	
</div>
