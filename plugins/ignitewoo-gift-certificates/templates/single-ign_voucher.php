<?php
/**
 * Gift Cert / Store Credit / Vouchers Templates
 *
 *
 * USED ONLY FOR GENERATING A PDF PREVIEW - DO NOT MODIFY THIS FILE
 * UNLESS YOU KNOW EXACTLY WHAT YOU'RE DOING AND WHY YOU'RE DOING IT.
 *
 *
 * Copyright (c) 2013, IgniteWoo.com
 * Copyright (c) 2012-2013, SkyVerge, Inc.
 */

/**
 * The template for displaying voucher previews.  This isn't a page template in
 * the regular sense, instead it streams the voucher PDF to the client for preview
 * purposes. 
 *
 * The preview voucher is created with placeholder field data.  
 * The voucher primary image at least must be set.
 *
 * @since 3.3
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );

global $post;

$voucher = new IGN_Gift_Cert_Voucher( $post->ID );

if ( !$voucher->get_image_id() ) {
	wp_die( __( 'You must set a voucher primary image before you can preview', 'ignitewoo_gift_certs' ) );
}


if ( !empty( $_POST['gen'] ) ) { 

	if ( empty( $_POST['gen']['code'] ) && !absint( $_POST['gen']['code'] ) )
		return;
	
	$voucher->coupon_code = get_the_title( $_POST['gen']['code'] );
	$voucher->recipient_name = isset( $_POST['gen']['recipient_name'] ) ? $_POST['gen']['recipient_name'] : '';
	$voucher->message = isset( $_POST['gen']['message'] ) ? $_POST['gen']['message'] : '';
	$voucher->product_desc = isset( $_POST['gen']['product_desc'] ) ? $_POST['gen']['product_desc'] : '';
	$voucher->product_name = isset( $_POST['gen']['product_name'] ) ? $_POST['gen']['product_name'] : '';
	$voucher->product_sku = isset( $_POST['gen']['product_sku'] ) ? $_POST['gen']['product_sku'] : '';
	$voucher->coupon_amount = get_post_meta( $_POST['gen']['code'], 'coupon_amount', true );
	
	$voucher->generate_pdf();
	
	die();

} else { 

	// Dummy text used for previews only
	$lorem_ipsum = "Lorem ipsum donec mattis, elit eget tincidunt pellentesque, enim nibh gravida diam, eu tristique ante sem ut justo. Praesent feugiat lorem dui, eu rutrum nibh tempus sit amet. Pellentesque sit amet tincidunt dolor, eu congue nisl. Phasellus justo urna, rhoncus blandit porta non, viverra nec nulla. Pellentesque nec molestie neque. Duis auctor libero at mi porta tempus. Fusce varius, turpis et ornare malesuada, augue risus mollis metus, sed gravida velit tortor vitae justo. Duis iaculis nunc sit amet erat vehicula, pharetra cursus dui ullamcorper.";

	$desc = 'Dummy test that should appear where the product short description field has been entered. 

	In this case the text has a line break';


	// if there is at least a voucher image set, set default values for all positioned fields
	// ie, the field 'product_name' will have the value 'Product Name' set
	foreach ( $voucher->voucher_fields as $field_name => $field ) {

		if ( isset( $field['position'] ) && $field['position'] ) {

			$value_set = false;
			
			$value = ucwords( str_replace( '_', ' ', $field_name ) );

			/*
			if ( isset( $field['max_length'] ) && $field['max_length'] ) {
			
				while ( strlen( $value ) < $field['max_length'] ) {
				
					$value .= " " . substr( $lorem_ipsum, 0, $field['max_length'] - strlen( $value ) + 1 );
				}
				
			} else
				$field['max_length'] = 250;
			*/
			
			if ( 'message' == $field_name ) {
				$value = substr( $lorem_ipsum, 0, $field['max_length'] - strlen( $value ) + 1 );
			}

			if ( 'recipient_name' == $field_name ) 
				$value = 'Johnny,';
				
			if ( 'product_name' == $field_name ) 
				$value = 'Dinner at Zolos';
				
			if ( 'product_desc' == $field_name ) 
				$value = $desc;
				
			if ( 'voucher_value' == $field_name ) 
				$voucher->coupon_amount = '50';
				
			if ( isset( $field['days_to_expiry'] ) && $field['days_to_expiry'] ) {
			
				// if there's an expiration date set then provide a dummy date for preview purposes
				$voucher->set_expiration_date( strtotime( "+{$field['days_to_expiry']} day" ) );
				
				$value_set = true;
			}

			if ( ! $value_set )
				$voucher->$field_name = $value;
		}
	}

	$voucher->coupon_code = uniqid(); // generate a random code

}
	
// stream the voucher pdf to the browser
$voucher->generate_pdf();

exit;
