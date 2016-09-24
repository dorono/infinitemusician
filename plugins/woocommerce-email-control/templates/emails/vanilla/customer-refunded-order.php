<?php
/**
 * Customer refunded order email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

		
<?php if ( $partial_refund || isset( $_REQUEST['ec_render_email'] ) ) { ?>

	<div class="top_heading">
		<?php echo get_option( "ec_vanilla_customer_refunded_order_heading_partial" ); ?>
	</div>
	
	<?php echo get_option( "ec_vanilla_customer_refunded_order_main_text_partial" ); ?>
	
	<?php if ( isset( $_REQUEST['ec_render_email'] ) ) { ?>
		<p class="state-guide">
			▲ <?php _e( "Partial Refund", 'email-control' ) ?>
		<p>
	<?php } ?>
	
<?php } ?>

<?php if ( !$partial_refund || isset( $_REQUEST['ec_render_email'] ) ) { ?>
	
	<div class="top_heading">
		<?php echo get_option( "ec_vanilla_customer_refunded_order_heading_full" ); ?>
	</div>
	
	<?php echo get_option( "ec_vanilla_customer_refunded_order_main_text_full" ); ?>
	
	<?php if ( isset( $_REQUEST['ec_render_email'] ) ) { ?>
		<p class="state-guide">
			▲ <?php _e( "Refund", 'email-control' ) ?>
		<p>
	<?php } ?>

<?php } ?>


<?php

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
