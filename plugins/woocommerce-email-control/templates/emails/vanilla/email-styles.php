<?php

/**
 * Load our settings.
 */

// Background Styling
$back_bg_color = get_option( "ec_vanilla_all_background_color" ); //"#f7f7f5";

// Email Sizing
$email_width = get_option( "ec_vanilla_all_email_width" ); //700px

// Main Body Styling
$body_color				= get_option( "ec_vanilla_all_text_color" );
$body_accent_color		= get_option( "ec_vanilla_all_text_accent_color" ); //#988255
$body_text_color		= get_option( "ec_vanilla_all_text_color" ); // "#3d3d3d";
$body_text_size 		= 14; //px
$body_letter_spacing	= 0.1; //em

$heading_color 			= get_option( "ec_vanilla_all_heading_color" );

$heading_1_size			= get_option( "ec_vanilla_all_heading_1_size" ); //px

$body_a_color 			= $body_accent_color;
$body_a_decoration 		= "underline";
$body_a_style			= "none";

$body_important_a_color 	 = $body_accent_color;
$body_important_a_decoratio  = "underline";
$body_important_a_style		 = "none";
$body_important_a_size		 = "17";
$body_important_a_weight	 = "bold";

$body_highlight_color		= $body_accent_color;
$body_highlight_decoration	= "none";
$body_highlight_style		= "none";

// Footer Styling
$footer_a_color				= "#3C3C3C";
$footer_a_decoration		= "none";
$footer_a_style				= "none";


/**
 * Generate CSS.
 */

?>

/* GENERAL STYLES */
body { margin: 0; padding: 0; }
body, table, td, tr { color: <?php echo $body_text_color ?>; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.5em; }
p { margin: 10px 0; padding: 0; }
ul { display: block; margin: 0; padding: 0; }
li { margin: 10px 0; padding: 0; }
h1, h2, h3, h4, h5, h6 { font-family: Arial, sans-serif; letter-spacing: -.5px; font-weight: bold; color: <?php echo $heading_color; ?>; text-align: center; margin: 0; padding: 0; }
h1 { font-size: 24px; line-height: 24px; margin: 24px 0; }
h2 { font-size: 24px; line-height: 24px; margin: 20px 0; }
h3 { font-size: 20px; line-height: 20px; margin: 18px 0; }
h4 { font-size: 18px; line-height: 18px; margin: 16px 0; }
h5 { font-size: 16px; line-height: 16px; margin: 14px 0; }
h6 { font-size: 14px; line-height: 14px; margin: 12px 0; }
img { border: 0; }
a { color: <?php echo $body_text_color ?>; font-style: <?php echo $body_a_style ?>; text-decoration: <?php echo $body_a_decoration ?>; }
	
/* BODY CONTENT */
.body_content { font-family: Arial, sans-serif; text-align: center; color: <?php echo $body_color ?>; margin: 0; padding: 40px 0; }
.body_content p { font-family: Arial, sans-serif; text-align: center; color: <?php echo $body_color ?>; margin: 10px 0; padding: 0; }

	/* GENERAL HEADING COLORS */
	.heading-color,
	.heading-color p,
	.heading-color p a,
	.heading-color p a.link { color: <?php echo $body_color; ?>; }

	/* MAIN/TOP HEADING */
	.top_heading { color: <?php echo $heading_color; ?>; font-family: Arial, sans-serif; font-size: <?php echo $heading_1_size ?>px; letter-spacing: -1px; line-height: <?php echo $heading_1_size; ?>px; font-weight: bold; margin: 0; padding: 1px 0; }
	.top_heading p { color: <?php echo $heading_color; ?>; }

.wrapper { font-family: Arial, sans-serif; font-size: <?php echo $body_text_size ?>px; color: <?php echo $body_text_color ?>; background-color: <?php echo esc_attr( $back_bg_color ) ?>; width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 0 0 50px 0; }
.wrapper-td { padding: 0 20px 20px; }

.main-body { font-family: Arial, sans-serif; text-align: center; overflow: hidden; width: <?php echo $email_width ?>px; }

.divider-line { background: <?php echo wc_hex_darker( $back_bg_color, 8 ); ?>; font-size: 0; height: 1px; }

.template_header { font-family: Arial, sans-serif; font-family:Arial; font-weight:bold; vertical-align:middle; padding: 20px 0; padding: 3% 0; }
.template_header a { font-weight: normal; text-decoration: none; font-size: 13px; margin: 0 0 0 12px; }

/* GENERAL HEADING, TEXT, LINKS */
.a_tag { color: <?php echo $body_text_color ?>; font-style: <?php echo $body_a_style ?>; text-decoration: <?php echo $body_a_decoration ?>; } /* a tags that are body colour with underline set in the global styles */
.a_tag_clean { color: <?php echo $body_text_color ?>; font-style: <?php echo $body_a_style ?>; text-decoration: none; } /* a tags that are body colour with no underline forced on */
.a_tag_color { color: <?php echo $body_a_color ?>; text-decoration: <?php echo $body_a_decoration ?>; font-style: <?php echo $body_a_style ?>; } /* a tags that are specific colour with underline set in the global styles */
.a_tag_color_clean { color: <?php echo $body_a_color ?>; font-style: <?php echo $body_a_style ?>; text-decoration: none; } /* a tags that are colour with no underline forced on */
.highlight { color: <?php echo $body_highlight_color ?>; text-decoration: <?php echo $body_highlight_decoration ?>; font-style: <?php echo $body_highlight_style ?>; }

/* ORDER TABLE */
.order-table-heading { color: #757575; padding: 12px; font-weight: bold; }
.order-table-heading p { color: #757575; }
.order-table-heading .highlight { color: <?php echo $body_highlight_color ?>; text-decoration: <?php echo $body_highlight_decoration ?>; font-style: <?php echo $body_highlight_style ?>; }
.order-table-heading a { color: #757575; font-style: <?php echo $body_a_style ?>; text-decoration: none; }

/* PAYMENT GATEWAY OPTIONS */
.pay_link { font-size: <?php echo $body_important_a_size ?>px; font-weight: <?php echo $body_important_a_weight ?>; font-style: <?php echo $body_important_a_style ?>; color: <?php echo $body_important_a_color ?>; text-decoration: <?php echo $body_important_a_decoration ?>; }

/* ORDER ITEMS TABLE */
.order_items_table_holder { background: #fdfdfd /*#fcfcfc*/; border-radius: 4px; border: 2px solid <?php echo wc_hex_darker( $back_bg_color, 10 ); ?> /*#e0e0e0*/; }
.order_items_table_holder p { margin: 2px 0; }
.order_items_table { margin: 0; overflow: hidden; width: 100%; background: white; }
	
	/* TD GENERAL */
	.order_items_table_td { color: #757575; padding: 15px 30px; border-top: 1px solid #f7f7f7; font-family: Arial, sans-serif; text-align:left; vertical-align: top; font-size: 14px; }
	
	/* TH GENERAL */
	.order_items_table_th_style { font-family: Arial, sans-serif; text-align: left; text-transform: uppercase; font-size: 10px; font-weight: normal; padding-top: 12px; padding-bottom: 12px; margin:0; line-height: .8em; }
	
	/* TABLE TOP */
	.order_items_table_td_top { border-top: 1px solid #f7f7f7; }
	
	/* PRODUCT */
	.order_items_table_td_product { padding-top: 25px; padding-bottom: 25px; }
	.order_items_table_td_product,
	.order_items_table_td_product td { color: <?php echo wc_hex_lighter( '#757575', 30 ); ?>; }
	
		/* DETAILS */
		.order_items_table_td_product_details { text-align: left; padding-top: 20px; padding-bottom: 25px; font-weight: normal; line-height: 20px; }

			/* DETAILS INNER */
			.order_items_table_product_details_inner {  }
			.order_items_table_product_details_inner_td { vertical-align: top; }
			.order_items_table_product_details_inner_td.order_items_table_product_details_inner_td_image { padding-right: 18px; }
			.order_items_table_product_details_inner_td.order_items_table_product_details_inner_td_text {  }
			.order_items_table_product_details_inner img { border-radius: 3px; padding-top: 4px; margin: 0; }
			.order_items_table_product_details_inner .order_items_table_product_details_inner_title { color: #757575; font-size: 16px; font-weight: bold; }
			.order_items_table_product_details_inner small {  }
		
		/* QUANTITY */
		.order_items_table_td.order_items_table_td_product_quantity { color: #757575; font-weight: bold; padding-left: 0; padding-right: 0; }
		
		/* TOTAL */
		.order_items_table_td.order_items_table_td_product_total { font-weight: normal; }
		.order_items_table_td.order_items_table_td_product_total .amount { color: #757575; font-weight: bold; }
		.order_items_table_td.order_items_table_td_product_total small { white-space: nowrap; word-wrap: normal; }

	/* TOTALS */
	.order_items_table_totals_style { background: #fcfcfc; font-family: Arial, sans-serif; text-align: left; font-size: 14px; line-height: 1em; }
	th.order_items_table_totals_style { width: 50%; text-align: right; padding-right: 12px; }
	td.order_items_table_totals_style { width: 50%; text-align: left; padding-left: 12px; }

/* ADDRESSES */
.addresses { width: 80%; }

/* NAVIGATION BAR */
.top_nav_holder {}
.top_nav { }
.top_nav tr td { height: 38px; font-size: 14px; }
.top_nav tr td.nav-text-block { padding: 11px 12px;  }
.top_nav tr td.nav-text-block-with-image { padding-left: 0px; }
.top_nav tr td.nav-image-block { padding: 8px 6px; }
.top_nav tr td.nav-spacer-block { padding: 8px 6px; }
.top_nav a { font-weight: bold; color: <?php echo wc_hex_darker( $body_text_color, 20 ); ?>; text-decoration: none; }

/* FOOTER */
.footer-text-block { font-family: Arial,sans-serif; font-size: 12px; text-align: center; }
.footer-text-block-td { font-family: Arial,sans-serif; font-size: 12px; padding: 15px 0 0; }
.footer-logo-block { font-family: Arial,sans-serif; font-size: 12px; text-align: center; }
.footer-logo-block-td { font-family: Arial,sans-serif; font-size: 12px; padding: 9px 0 0; }
.footer_a_tag { color: <?php echo $footer_a_color ?>; text-decoration: <?php echo $footer_a_decoration ?>; }

/* CUSTOM CSS */
<?php echo wp_strip_all_tags( get_option( "ec_vanilla_all_custom_css" ) ); ?>

/* RESPONSIVE */
@media screen and ( max-width: <?php echo $email_width + 60 ?>px ) {
	
	.main-body { width: 100% !important; }
	.addresses { width: 100% !important; }
	.addresses-td { display: block; width: 100%; }
	.nav-text-block { padding-left: 6px !important; padding-right: 6px !important; }
}
@media screen and ( max-width: 500px ) {
	
	.order_items_table_product_details_inner_td_image { display: none !important; }
}

/* ADMIN STYLES */
.testing-block { padding:8px 10px; color: rgb(59, 59, 59); box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.07) inset; font-family: sans-serif; font-size:11px; margin: 0 auto 4px; text-shadow: 0 0px 3px rgba(255, 255, 255, 0.54); display: inline-block; }
.state-guide { font-size: 10px; color: #AEAEAE; margin: 0; padding: 6px 0; text-transform: uppercase; }
.shortcode-error { color: #FFF; font-size: 12px; background-color: #545454; border-radius: 3px; padding: 2px 6px 1px; }
