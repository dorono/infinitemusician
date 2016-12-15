<?php
/**
 * Single Product Suggested Gift Cert Price, including microdata for SEO
 */

global $product;

// go through a few options to find the $price we should display in the input (typically will be the suggested price)
if ( isset( $_POST['nyp'] ) &&  floatval( $_POST['nyp'] ) >= 0 ) {

	$num_decimals = ( int ) get_option( 'woocommerce_price_num_decimals' );
	
	$price = round( floatval( $_POST['nyp'] ), $num_decimals );
	
} elseif ( $product->suggested && floatval( $product->suggested ) > 0 ) {

	$price = $product->suggested;
	
} elseif ( $product->minimum && floatval( $product->minimum ) > 0 ) {

	$price =  $product->minimum;
	
} else {

	$price = '';
	
}

?>

<div class="gcp">

	<label for="gcp">
		<?php printf( _x( '%s ( %s )', 'In case you need to change the order of the currency symbol ( $currency_symbol )', 'wc_name_your_price' ), $product->label_text, get_woocommerce_currency_symbol() ); ?>
	</label>

	<?php echo ign_gc_pricer::price_input_helper( esc_attr( $price ), array( 'name' => 'nyp' ) ); ?>

</div>