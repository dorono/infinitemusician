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

/**
 * Voucher data meta box
 *
 * Displays the meta box
 *
 * @since 3.3
 */
function ign_voucher_data_meta_box( $post ) {
	global $woocommerce;

	wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );

	$voucher = new IGN_Gift_Cert_Voucher( $post->ID );

	$default_fonts = array(
		'Helvetica' 	=> 'Helvetica',
		'Courier'   	=> 'Courier',
		'Times'     	=> 'Times',
		'DejaVu'	=> 'DejaVu Sans (UTF-8)',
		'Angsa'		=> 'Angsa (Thai only)'
	);
	$available_fonts = array_merge( array( '' => '' ), $default_fonts );

	// since this little snippet of css applies only to the voucher post page, it's easier to have inline here
	?>
	<style type="text/css">
		#misc-publishing-actions { display:none; }
		#edit-slug-box { display:none }
		.imgareaselect-outer { cursor: crosshair; }
		.woocommerce_options_panel .options_group {
			background: none repeat scroll 0 0 #EEEEEE;
			border-bottom: 1px solid #DFDFDF;
			margin: 10px 0 15px 0;
			padding: 0px;
		}
		#voucher_options .voucher_options {
			display: none;
			margin-top: -10px !important;
		}
		.voucher_nav {
			cursor: pointer;
			padding: 10px 10px;
			margin-bottom: 10px;
			background: none repeat scroll 0 0 #EEEEEE;
			font-weight: bold;
			//url("../images/arrows.png") no-repeat scroll 6px 7px rgba(0, 0, 0, 0)
		}
		.voucher_arrow_down { 
			float:right;
			color: #aaa;
			display:none;
		}
		.voucher_nav:hover > .voucher_arrow_down { display: block; }
		
		
		.woocommerce_options_panel .short {
			width: 75%;
		}
		.woocommerce_options_panel input, .woocommerce_options_panel label, .woocommerce_options_panel legend, .woocommerce_options_panel select  {
			float: none;
		}
		<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) { ?>
		.woocommerce_options_panel fieldset.form-field, .woocommerce_options_panel p.form-field {
			padding: 5px 20px !important;
		}
		.woocommerce_options_panel label, .woocommerce_options_panel legend {
			margin: 0 !important;
		}
		#_product_name_font_size {
			margin-left: 28px !important;
		}
		.woocommerce_options_panel input[type="email"], .woocommerce_options_panel input[type="number"], .woocommerce_options_panel input[type="text"] {
			float: none !important;
		}
		.font_wrapper {
			margin-left: 15px;
		}
		.woocommerce_options_panel .description {
			margin: 0;
			line-height: 1em;
		}
		<?php } ?>
	</style>
	
	<script>
	jQuery( document ).ready( function( $ ) { 
		$( '.voucher_nav' ).click( function() { 
			//$( '.voucher_options' ).hide();
			if ( $( '.' + $( this ).data( 'div' ) ).is( ':visible' ) )
				$( '.' + $( this ).data( 'div' ) ).hide();
			else 
				$( '.' + $( this ).data( 'div' ) ).show();
		})
	})
	</script>
	
	<div id="voucher_options" class="panel woocommerce_options_panel">
		<div class="options_groups">
			<?php

				
				// Text color
				echo '<div class="voucher_nav" data-div="voucher_defaults">' . __( 'Defaults', 'ignitewoo_gift_certs' ) . '<div class="voucher_arrow_down">&#9660;</div></div>';
				
				echo '<div class="options_group voucher_options voucher_defaults">';
					ign_vouchers_wp_font_select( array(
						'id'                => '_voucher',
						'label'             => __( 'Default Font', 'ignitewoo_gift_certs' ),
						'options'           => $default_fonts,
						'font_size_default' => 48,
					) );
					woocommerce_wp_text_input( array(
						'id'          => '_voucher_font_color',
						'label'       => __( 'Default Font color', 'ignitewoo_gift_certs' ) . ' ',
						'default'     => '#000000',
						'description' => __( 'The default text color for the voucher.', 'ignitewoo_gift_certs' ),
						'class'       => 'colorpick',
					) );

				echo '</div>';

				// Product name position
				echo '<div class="voucher_nav" data-div="product_name">' . __( 'Product Name Position', 'ignitewoo_gift_certs' ) . '<div class="voucher_arrow_down">&#9660;</div></div>';
				
				echo '<div class="options_group  voucher_options product_name">';
					ign_vouchers_wp_position_picker( array(
						'id'          => 'product_name_pos',
						'label'       => '', //__( '<strong>Product Name Position</strong><br/>',
						'labelname'       => __( '<strong>Product Name Position</strong><br/>','ignitewoo_gift_certs' ),
						'value'       => implode( ',', $voucher->get_field_position( 'product_name' ) ),
						'description' => '<br>' . __( 'Optional position of the product name', 'ignitewoo_gift_certs' ),
						
					) );
					woocommerce_wp_hidden_input( array(
						'id'    => '_product_name_pos',
						'class' => 'field_pos',
						'value' => implode( ',', $voucher->get_field_position( 'product_name' ) ),
					) );
					ign_vouchers_wp_font_select( array(
						'id'      => '_product_name',
						'label'   => __( 'Font', 'ignitewoo_gift_certs' ),
						'options' => $available_fonts,
						'align' => $voucher->get_text_align( 'product_name' ),
					) );
					woocommerce_wp_text_input( array(
						'id'    => '_product_name_font_color',
						'label' => __( 'Font color', 'ignitewoo_gift_certs' ) . ' ',
						'value' => isset( $voucher->voucher_fields['product_name']['font']['color'] ) ? $voucher->voucher_fields['product_name']['font']['color'] : '',
						'class' => 'colorpick',
					) );
				echo '</div>';
				
				// Product desc position
				echo '<div class="voucher_nav" data-div="product_desc">' . __( 'Short Desc Position', 'ignitewoo_gift_certs' ) . '<div class="voucher_arrow_down">&#9660;</div></div>';
				
				echo '<div class="options_group voucher_options product_desc">';
					ign_vouchers_wp_position_picker( array(
						'id'          => 'product_desc_pos',
						'label'       => '', //__( '<strong>Product Name Position</strong><br/>',
						'labelname'       => __( '<strong>Short Desc Position</strong><br/>','ignitewoo_gift_certs' ),
						'value'       => implode( ',', $voucher->get_field_position( 'product_desc' ) ),
						'description' => '<br>' . __( 'Optional position of the short product description', 'ignitewoo_gift_certs' ),
						
					) );
					woocommerce_wp_hidden_input( array(
						'id'    => '_product_desc_pos',
						'class' => 'field_pos',
						'value' => implode( ',', $voucher->get_field_position( 'product_desc' ) ),
					) );
					ign_vouchers_wp_font_select( array(
						'id'      => '_product_desc',
						'label'   => __( 'Font', 'ignitewoo_gift_certs' ),
						'options' => $available_fonts,
					) );
					woocommerce_wp_text_input( array(
						'id'    => '_product_desc_font_color',
						'label' => __( 'Font color', 'ignitewoo_gift_certs' ) . ' ',
						'value' => isset( $voucher->voucher_fields['product_desc']['font']['color'] ) ? $voucher->voucher_fields['product_desc']['font']['color'] : '',
						'class' => 'colorpick',
					) );
				echo '</div>';

				echo '<div class="voucher_nav" data-div="voucher_sku">' . __( 'SKU Position', 'ignitewoo_gift_certs' ) . '<div class="voucher_arrow_down">&#9660;</div></div>';
				
				// SKU position
				echo '<div class="options_group voucher_options voucher_sku">';
					ign_vouchers_wp_position_picker( array(
						'id'          => 'product_sku_pos',
						'labelname'       => __( '<strong>SKU Position</strong>', 'ignitewoo_gift_certs' ),
						'value'       => implode( ',', $voucher->get_field_position( 'product_sku' ) ),
						'description' => __( 'Optional position of the product SKU', 'ignitewoo_gift_certs' ),
					) );
					woocommerce_wp_hidden_input( array(
						'id'    => '_product_sku_pos',
						'class' => 'field_pos',
						'value' => implode( ',', $voucher->get_field_position( 'product_sku' ) )
					) );
					ign_vouchers_wp_font_select( array(
						'id'      => '_product_sku',
						'label'   => __( 'Font', 'ignitewoo_gift_certs' ),
						'options' => $available_fonts,
					) );
					woocommerce_wp_text_input( array(
						'id'    => '_product_sku_font_color',
						'label' => __( 'Font color', 'ignitewoo_gift_certs' ) . ' ',
						'value' => isset( $voucher->voucher_fields['product_sku']['font']['color'] ) ? $voucher->voucher_fields['product_sku']['font']['color'] : '',
						'class' => 'colorpick',
					) );
				echo '</div>';

				// Voucher number position
				echo '<div class="voucher_nav" data-div="voucher_number">' . __( 'Voucher Number Position', 'ignitewoo_gift_certs' ) . '<div class="voucher_arrow_down">&#9660;</div></div>';
				
				echo '<div class="options_group voucher_options voucher_number">';
					ign_vouchers_wp_position_picker( array(
						'id'          => 'voucher_number_pos',
						'labelname'       => __( '<strong>Voucher Number Position</strong>', 'ignitewoo_gift_certs' ),
						'value'       => implode( ',', $voucher->get_field_position( 'voucher_number' ) ),
						'description' => __( 'Optional position of the voucher number', 'ignitewoo_gift_certs' ),
					) );
					woocommerce_wp_hidden_input( array(
						'id'    => '_voucher_number_pos',
						'class' => 'field_pos',
						'value' => implode( ',', $voucher->get_field_position( 'voucher_number' ) ),
					) );
					ign_vouchers_wp_font_select( array(
						'id'      => '_voucher_number',
						'label'   => __( 'Font', 'ignitewoo_gift_certs' ),
						'options' => $available_fonts,
					) );
					woocommerce_wp_text_input( array(
						'id'    => '_voucher_number_font_color',
						'label' => __( 'Font color', 'ignitewoo_gift_certs' ) . ' ',
						'value' => isset( $voucher->voucher_fields['voucher_number']['font']['color'] ) ? $voucher->voucher_fields['voucher_number']['font']['color'] : '',
						'class' => 'colorpick',
					) );
				echo '</div>';

				
				// Voucher value position
				echo '<div class="voucher_nav" data-div="voucher_value">' . __( 'Voucher Value Position', 'ignitewoo_gift_certs' ) . '<div class="voucher_arrow_down">&#9660;</div></div>';
				
				echo '<div class="options_group voucher_options voucher_value">';
					ign_vouchers_wp_position_picker( array(
						'id'          => 'voucher_value_pos',
						'labelname'       => __( '<strong>Voucher Value Position</strong>', 'ignitewoo_gift_certs' ),
						'value'       => implode( ',', $voucher->get_field_position( 'voucher_value' ) ),
						'description' => __( 'Optional position of the voucher value', 'ignitewoo_gift_certs' ),
					) );
					woocommerce_wp_hidden_input( array(
						'id'    => '_voucher_value_pos',
						'class' => 'field_pos',
						'value' => implode( ',', $voucher->get_field_position( 'voucher_value' ) ),
					) );
					ign_vouchers_wp_font_select( array(
						'id'      => '_voucher_value',
						'label'   => __( 'Font', 'ignitewoo_gift_certs' ),
						'options' => $available_fonts,
					) );
					woocommerce_wp_text_input( array(
						'id'    => '_voucher_value_font_color',
						'label' => __( 'Font color', 'ignitewoo_gift_certs' ) . ' ',
						'value' => isset( $voucher->voucher_fields['voucher_value']['font']['color'] ) ? $voucher->voucher_fields['voucher_value']['font']['color'] : '',
						'class' => 'colorpick',
					) );
				echo '</div>';
				
				
				// Days to expiration
				echo '<div class="voucher_nav" data-div="voucher_date">' . __( 'Expiration Date Position', 'ignitewoo_gift_certs' ) . '<div class="voucher_arrow_down">&#9660;</div></div>';
				
				echo '<div class="options_group voucher_options voucher_date">';
					ign_vouchers_wp_position_picker( array(
						'id'          => 'expiration_date_pos',
						'labelname'       => __( '<strong>Expiration Date Position</strong>', 'ignitewoo_gift_certs' ),
						'value'       => implode( ',', $voucher->get_field_position( 'expiration_date' ) ),
						'description' => __( 'Optional position of the voucher expiration date', 'ignitewoo_gift_certs' ),
					) );
					woocommerce_wp_hidden_input( array(
						'id' => '_expiration_date_pos',
						'class' => 'field_pos',
						'value' => implode( ',', $voucher->get_field_position( 'expiration_date' ) ),
					) );
					ign_vouchers_wp_font_select( array(
						'id'      => '_expiration_date',
						'label'   => __( 'Font', 'ignitewoo_gift_certs' ),
						'options' => $available_fonts,
					) );
					woocommerce_wp_text_input( array(
						'id'    => '_expiration_date_font_color',
						'label' => __( 'Font color', 'ignitewoo_gift_certs' ) . ' ',
						'value' => isset( $voucher->voucher_fields['expiration_date']['font']['color'] ) ? $voucher->voucher_fields['expiration_date']['font']['color'] : '',
						'class' => 'colorpick',
					) );
					woocommerce_wp_text_input( array(
						'id'          => '_days_to_expiry',
						'label'       => __( 'Days to Expiration', 'ignitewoo_gift_certs' ),
						'description' => __( 'Optional number of days after purchase until the voucher expires. For preview purposes only.', 'ignitewoo_gift_certs' ),
						'placeholder' => __( 'days', 'ignitewoo_gift_certs' ),
						'value'       => $voucher->get_expiry(),
					) );
				echo '</div>';

				// Voucher recipient position
				echo '<div class="voucher_nav" data-div="voucher_recipient">' . __( 'Voucher Recipient Position', 'ignitewoo_gift_certs' ) . '<div class="voucher_arrow_down">&#9660;</div></div>';
				
				echo '<div class="options_group voucher_options voucher_recipient">';
					ign_vouchers_wp_position_picker( array(
						'id'          => 'recipient_name_pos',
						'labelname'       => __( '<strong>Voucher Recipient Position</strong>', 'ignitewoo_gift_certs' ),
						'value'       => implode( ',', $voucher->get_field_position( 'recipient_name' ) ),
						'description' => __( 'Optional position of the name of the receiving party.', 'ignitewoo_gift_certs' ),
					) );
					woocommerce_wp_hidden_input( array(
						'id'    => '_recipient_name_pos',
						'class' => 'field_pos',
						'value' => implode( ',', $voucher->get_field_position( 'recipient_name' ) ),
					) );
					ign_vouchers_wp_font_select( array(
						'id'      => '_recipient_name',
						'label'   => __( 'Font', 'ignitewoo_gift_certs' ),
						'options' => $available_fonts,
					) );
					woocommerce_wp_text_input( array(
						'id'    => '_recipient_name_font_color',
						'label' => __( 'Font color', 'ignitewoo_gift_certs' ) . ' ',
						'value' => isset( $voucher->voucher_fields['recipient_name']['font']['color'] ) ? $voucher->voucher_fields['recipient_name']['font']['color'] : '',
						'class' => 'colorpick',
					) );
					woocommerce_wp_text_input( array(
						'id'          => '_recipient_name_max_length',
						'label'       => __( 'Max Length', 'ignitewoo_gift_certs' ),
						'description' => __( 'The maximum length of the recipient name field', 'ignitewoo_gift_certs' ),
						'placeholder' => __( 'No Limit', 'ignitewoo_gift_certs' ),
						'value'       => $voucher->get_user_input_field_max_length( 'recipient_name' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'          => '_recipient_name_is_required',
						'label'       => __( 'Required', 'ignitewoo_gift_certs' ),
						'description' => __( 'Make this field required in order to add a voucher product to the cart', 'ignitewoo_gift_certs' ),
						'value'       => $voucher->user_input_field_is_required( 'recipient_name' ) ? 'yes' : 'no',
					) );
				echo '</div>';

				// Voucher message position
				echo '<div class="voucher_nav" data-div="voucher_message">' . __( 'Message Position', 'ignitewoo_gift_certs' ) . '<div class="voucher_arrow_down">&#9660;</div></div>';
				
				echo '<div class="options_group voucher_options voucher_message">';
					ign_vouchers_wp_position_picker( array(
						'id'          => 'message_pos',
						'labelname'       => __( '<strong>Message Position</strong>', 'ignitewoo_gift_certs' ),
						'value'       => implode( ',', $voucher->get_field_position( 'message' ) ),
						'description' => __( 'Optional position of the user-supplied message', 'ignitewoo_gift_certs' ),
					) );
					woocommerce_wp_hidden_input( array(
						'id'    => '_message_pos',
						'class' => 'field_pos',
						'value' => implode( ',', $voucher->get_field_position( 'message' ) ),
					) );
					ign_vouchers_wp_font_select( array(
						'id'      => '_message',
						'label'   => __( 'Font', 'ignitewoo_gift_certs' ),
						'options' => $available_fonts,
					) );
					woocommerce_wp_text_input( array(
						'id'    => '_message_font_color',
						'label' => __( 'Font color', 'ignitewoo_gift_certs' ) . ' ',
						'value' => isset( $voucher->voucher_fields['message']['font']['color'] ) ? $voucher->voucher_fields['message']['font']['color'] : '',
						'class' => 'colorpick',
					) );
					woocommerce_wp_text_input( array(
						'id'          => '_message_max_length',
						'label'       => __( 'Max Length', 'ignitewoo_gift_certs' ),
						'description' => __( 'The maximum length of the message field', 'ignitewoo_gift_certs' ),
						'placeholder' => __( 'No Limit', 'ignitewoo_gift_certs' ),
						'value'       => $voucher->get_user_input_field_max_length( 'message' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'          => '_message_is_required',
						'label'       => __( 'Required', 'ignitewoo_gift_certs' ),
						'description' => __( 'Make this field required in order to add a voucher product to the cart', 'ignitewoo_gift_certs' ),
						'value'       => $voucher->user_input_field_is_required( 'message' ) ? 'yes' : 'no',
					) );
				echo '</div>';
				
								
				// Voucher text position
				echo '<div class="voucher_nav" data-div="voucher_text">' . __( 'Arbitrary Text Position', 'ignitewoo_gift_certs' ) . '<div class="voucher_arrow_down">&#9660;</div></div>';
				
				echo '<div class="options_group voucher_options voucher_text">';
					ign_vouchers_wp_position_picker( array(
						'id'          => 'random_text_pos',
						'labelname'       => __( '<strong>Arbitrary Text Position</strong>', 'ignitewoo_gift_certs' ),
						'value'       => implode( ',', $voucher->get_field_position( 'random_text' ) ),
						'description' => __( 'Optional position of the any text you want to insert into this voucher', 'ignitewoo_gift_certs' ),
					) );
					woocommerce_wp_hidden_input( array(
						'id'    => '_random_text_pos',
						'class' => 'field_pos',
						'value' => implode( ',', $voucher->get_field_position( 'random_text' ) ),
					) );
					ign_vouchers_wp_font_select( array(
						'id'      => '_random_text',
						'label'   => __( 'Font', 'ignitewoo_gift_certs' ),
						'options' => $available_fonts,
					) );
					woocommerce_wp_text_input( array(
						'id'    => '_random_text_font_color',
						'label' => __( 'Font color', 'ignitewoo_gift_certs' ) . ' ',
						'value' => isset( $voucher->voucher_fields['random_text']['font']['color'] ) ? $voucher->voucher_fields['random_text']['font']['color'] : '',
						'class' => 'colorpick',
					) );
					woocommerce_wp_textarea_input( array(
						'id'    => '_random_text_data',
						'label' => __( 'Your text:', 'ignitewoo_gift_certs' ) . ' ',
						'value' => isset( $voucher->voucher_fields['random_text']['random_text_data'] ) ? $voucher->voucher_fields['random_text']['random_text_data'] : '',
						'class' => '',
						'style' => 'min-height: 100px',
						'description' => __( 'Enter the text you want to insert into the voucher', 'ignitewoo_gift_certs' ),
					) );

				echo '</div>';
				
				// QR Code  position
				echo '<div class="voucher_nav" data-div="voucher_qr">' . __( 'QR Code Position', 'ignitewoo_gift_certs' ) . '<div class="voucher_arrow_down">&#9660;</div></div>';
				
				echo '<div class="options_group voucher_options voucher_qr">';
					ign_vouchers_wp_position_picker( array(
						'id'          => 'qr_pos',
						'labelname'       => __( '<strong>QR Code Position</strong>', 'ignitewoo_gift_certs' ),
						'value'       => implode( ',', $voucher->get_field_position( 'qr' ) ),
						'description' => __( 'Optional position of the QR code. Note that the QR code image is always square regardless of the box shape you draw. The width of the box is used to determine the size of the square', 'ignitewoo_gift_certs' ),
					) );
					woocommerce_wp_hidden_input( array(
						'id'    => '_qr_pos',
						'class' => 'field_pos',
						'value' => implode( ',', $voucher->get_field_position( 'qr' ) ),
					) );
					woocommerce_wp_select( array(
						'id'          => 'qr_links_to',
						'label' => '',
						'description' => __( 'QR code links to this area', 'ignitewoo_gift_certs' ),
						'default'	=> __( 'coupon', 'ignitewoo_gift_certs' ),
						'options'	=> array(
								'coupon' => __( 'Coupon', 'ignitewoo_gift_certs' ),
								'order' => __( 'Order', 'ignitewoo_gift_certs' ),
									),
						'value'	=> get_post_meta( $post->ID, '_qr_links_to', 'coupon' )
					) );
				echo '</div>';
				
				// Barcode  position
				echo '<div class="voucher_nav" data-div="voucher_barcode">' . __( 'Barcode Position', 'ignitewoo_gift_certs' ) . '<div class="voucher_arrow_down">&#9660;</div></div>';
				
				echo '<div class="options_group voucher_options voucher_barcode">';
					ign_vouchers_wp_position_picker( array(
						'id'          => 'barcode_pos',
						'labelname'       => __( '<strong>Barcode Position</strong>', 'ignitewoo_gift_certs' ),
						'value'       => implode( ',', $voucher->get_field_position( 'barcode' ) ),
						'description' => __( 'Optional position of the barcode. Note that the barcode image is always rectangular regardless of the box shape you draw. The width of the box is used to determine the size of the rectangle', 'ignitewoo_gift_certs' ),
					) );
					woocommerce_wp_hidden_input( array(
						'id'    => '_barcode_pos',
						'class' => 'field_pos',
						'value' => implode( ',', $voucher->get_field_position( 'barcode' ) ),
					) );

				echo '</div>';


			?>
		</div>
	</div>
	<?php

	ign_vouchers_wp_color_picker_js();
}


add_action( 'woocommerce_process_ign_voucher_meta', 'ign_vouchers_process_voucher_meta', 10, 2 );

/**
 * Voucher Data Save
 *
 * Function for processing and storing all voucher data.
 *
 * @since 3.3
 * @param int $post_id the voucher id
 * @param object $post the voucher post object
 */
function ign_vouchers_process_voucher_meta( $post_id, $post ) {

	// voucher font defaults
	update_post_meta( $post_id, '_voucher_font_color',  $_POST['_voucher_font_color'] ? $_POST['_voucher_font_color'] : '#000000' );  // provide a default
	
	update_post_meta( $post_id, '_voucher_font_size',   $_POST['_voucher_font_size'] ? $_POST['_voucher_font_size'] : 11 );
	
	// provide a default
	update_post_meta( $post_id, '_voucher_font_family', $_POST['_voucher_font_family']  );
	
	update_post_meta( $post_id, '_voucher_font_style',  ( isset( $_POST['_voucher_font_style_b'] ) && 'yes' == $_POST['_voucher_font_style_b'] ? 'B' : '' ) . ( isset( $_POST['_voucher_font_style_i'] ) && 'yes' == $_POST['_voucher_font_style_i'] ? 'I' : '' ) );
		
	update_post_meta( $post_id, '_qr_links_to', $_POST['qr_links_to'] );

	// original sizes: default 11, product name 16, sku 8
	// create the voucher fields data structure
	$fields = array();
	foreach ( array( '_product_name', '_product_desc', '_product_sku', '_voucher_number', '_voucher_value', '_expiration_date', '_recipient_name', '_message', '_random_text', '_qr', '_barcode' ) as $i => $field_name ) {
		// set the field defaults
		$field = array(
			'type'      => 'property',
			'font'     => array( 'family' => '', 'size' => '', 'style' => '', 'color' => '' ),
			'position' => array(),
			'order'    => $i,
			'align' => 'left',
		);

		// get the field position (if set)
		if ( $_POST[ $field_name . '_pos' ] ) {
			$position = explode( ',', $_POST[ $field_name . '_pos' ] );
			$field['position'] = array( 'x1' => $position[0], 'y1' => $position[1], 'width' => $position[2], 'height' => $position[3] );
		}

		// get the field font settings (if any)
		if ( $_POST[ $field_name . '_font_family' ] )  $field['font']['family'] = $_POST[ $field_name . '_font_family' ];
		
		if ( $_POST[ $field_name . '_font_size' ] )    $field['font']['size']   = $_POST[ $field_name . '_font_size' ];
		
		if ( isset( $_POST[ $field_name . '_font_style_b' ] ) && $_POST[ $field_name . '_font_style_b' ] ) $field['font']['style']  = 'B';
		
		if ( isset( $_POST[ $field_name . '_font_style_i' ] ) && $_POST[ $field_name . '_font_style_i' ] ) $field['font']['style'] .= 'I';
		
		if ( $_POST[ $field_name . '_font_color' ] )   $field['font']['color']  = $_POST[ $field_name . '_font_color' ];
		
		if ( $_POST[ $field_name . '_text_align' ] ) 
			$field['align'] = $_POST[ $field_name . '_text_align' ];
		else 
			$field['align'] = 'none'; // for field where alignment cannot be set

		// expiration date special case
		if ( '_expiration_date' == $field_name ) {
			$field['days_to_expiry'] = $_POST['_days_to_expiry'] ? absint( $_POST['_days_to_expiry'] ) : '';
		} elseif ( '_recipient_name' == $field_name ) {
			$field['display_name'] = 'Voucher Recipient';  // this is translated upon display
			$field['type']         = 'user_input';
			$field['input_type']   = 'text';
			$field['max_length']   = $_POST['_recipient_name_max_length'] ? absint( $_POST['_recipient_name_max_length'] ) : '';
			$field['is_required']  = isset( $_POST['_recipient_name_is_required'] ) && 'yes' == $_POST['_recipient_name_is_required'] ? 'yes' : 'no';
		} elseif ( '_message' == $field_name ) {
			$field['display_name'] = 'Voucher Message'; // this is translated upon display
			$field['type']         = 'user_input';
			$field['input_type']   = 'textarea';
			$field['max_length']   = $_POST['_message_max_length'] ? absint( $_POST['_message_max_length'] ) : '';
			$field['is_required']  = isset( $_POST['_message_is_required'] ) && 'yes' == $_POST['_message_is_required'] ? 'yes' : 'no';
		} elseif ( '_random_text' == $field_name ) {
			$field['random_text_data']  = isset( $_POST['_random_text_data'] ) ? trim( $_POST['_random_text_data'] ) : '';
		}

		// cut off the leading '_' to create the field name
		$fields[ ltrim( $field_name, '_' ) ] = $field;
	}

	update_post_meta( $post_id, '_voucher_fields', $fields );
}
