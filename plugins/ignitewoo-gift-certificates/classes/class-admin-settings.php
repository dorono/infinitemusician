<?php
/*
Copyright (c) 2012-2015 IgniteWoo.com - All Rights Reserved
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );
	
if ( !class_exists( 'WC_Settings_API' ) )
	return;
	
class IgniteWoo_GC_Admin extends WC_Settings_API {

	var $plugin_url;

	function __construct() {

		$this->plugin_url = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '' , plugin_basename( __FILE__ ) );

		$this->id = 'woocommerce_gift_certificates';

		$this->method_title = __( 'Gift Certs', 'ignitewoo_gift_certs' );

		$this->init_form_fields(); // Do this twice for backward compat with older versions of WooCommerce

		$this->init_settings();

		$this->title 			= 'Gift Certificates';

		$this->email_subject		= $this->settings['email_subject'];
		$this->use_qr			= $this->settings['use_qr'];
		$this->email_admin		= $this->settings['email_admin'];
		
		$this->heading_store_credit	= $this->settings['heading_store_credit'];
		$this->heading_percent		= $this->settings['heading_percent'];
		$this->heading_percent_product	= $this->settings['heading_percent_product'];
		$this->heading_fixed_cart	= $this->settings['heading_fixed_cart'];
		$this->heading_fixed_product	= $this->settings['heading_fixed_product'];


		$this->init_form_fields(); // Do this twice for backward compat with older versions of WooCommerce

		add_filter ( 'woocommerce_settings_tabs_array', array( &$this, 'add_tab' ), 1, 1 );

		add_action( 'woocommerce_settings_tabs_gift_cert', array( &$this, 'gift_cert_settings' ) );

		// call action in the parent class - saves option settings
		add_action( 'woocommerce_update_options_gift_cert', array( &$this, 'process_gift_cert_options' ) );

		// save the selected authentication user
		add_action( 'woocommerce_update_gift_cert_extra_options', array( &$this, 'process_gift_cert_settings' ) );

		
	}
	
	function add_tab( $tabs = '' ) {

		$tabs['gift_cert'] = __( 'Gift Certs', 'woothemes' );

		return $tabs;

	}


	function process_gift_cert_options() {

		$this->process_admin_options();

		do_action( 'woocommerce_update_gift_cert_extra_options' );

	}


	function gift_cert_settings() {

		$this->admin_options();

	}

	// Init form fields - modeled on WooCommerce core code
	function init_form_fields() {
		global $woocommerce;


		$this->form_fields = array(
			'title1' => array(
						'title' 		=> __( 'General Settings', 'ignitewoo_gift_certs' ),
						'type' 			=> 'title',
						'description' 		=> __( '', 'ignitewoo_gift_certs' ),
						),
			'email_vouchers' => array(
						'title' 		=> __( 'Email Vouchers', 'ignitewoo_gift_certs' ),
						'type' 			=> 'checkbox',
						'description' 		=> __( 'Enable this option to have vouchers emailed when an order is paid. If you turn this off then no vouchers are emailed and no recipient forms will appear on the checkout page', 'ignitewoo_gift_certs' ),
						'default'		=>'yes',
						),
			'email_subject' => array(
						'title' 		=> __( 'Email Subject', 'ignitewoo_gift_certs' ),
						'type' 			=> 'text',
						'class'			=> 'ignwide',
						'description' 		=> __( '<br> Email subject for the gift certificate email sent to buyers or recipients.<br> Insert %s where you want the buyer\'s name inserted, or omit it if you do not want the buyer name to appear in the email subject', 'ignitewoo_gift_certs' ),
						'default'		=> '[' . wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) . '] ' . __( 'You received a gift certificate from %s', 'ignitewoo_gift_certs' ),
						),
			'email_admin' => array(
						'title' 		=> __( 'CC Admin', 'ignitewoo_gift_certs' ),
						'type' 			=> 'checkbox',
						'description' 		=> __( ' Enable this option to send the store admin an email when a gift certificate is issued.<br> The email will be the same email that is sent to the customer.<br> Note that the email address used is the same as the "New Order Notifications" email address seen at WooCommerce -> Settings -> Email', 'ignitewoo_gift_certs' ),
						'default'		=> 'yes',
						),
			'order_status_trigger' => array(
						'title' 		=> __( 'Order Status Trigger', 'ignitewoo_gift_certs' ),
						'type' 			=> 'select',
						'default' 		=> 'completed',
						'options'		=> array( 'completed' => __( 'Completed', 'ignitewoo_gift_certs' ), 'processing' => __( 'Processing or Completed', 'ignitewoo_gift_certs' ) ),
						'description' 		=> __( ' Select which order status triggers the issuance of vouchers to the buyer. Recommended settings is Completed.', 'ignitewoo_gift_certs' ),
					),
			'titleg' => array( 'title' => __( 'Selectable Amount Setup', 'ignitewoo_gift_certs' ), 'type' => 'title', 'desc' =>  __( 'Modify the text strings used by the plugin.', 'ignitewoo_gift_certs' ), 'id' => 'woocommerce_nyp_options' ),

			'suggested_text' => array(
							'title' => __( 'Suggested Price Text', 'ignitewoo_gift_certs' ),
							'desc' 		=> __( 'This is the text to display before the suggested price.', 'ignitewoo_gift_certs' ),
							'id' 		=> 'woocommerce_nyp_suggested_text',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default' 		=> __( 'Suggested Amount', 'ignitewoo_gift_certs' ),
							'desc_tip'	=>  true,
						),

			'minimum_text' => array(
							'title' => __( 'Minimum Price Text', 'ignitewoo_gift_certs' ),
							'desc' 		=> __( 'This is the text to display before the minimum accepted price.', 'ignitewoo_gift_certs' ),
							'id' 		=> 'woocommerce_nyp_minimum_text',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default' 		=> __( 'Minimum Amount', 'ignitewoo_gift_certs' ),
							'desc_tip'	=>  true,
						),

			'label_text' =>	array(
							'title' => __( 'Name Your Price Text', 'ignitewoo_gift_certs' ),
							'desc' 		=> __( 'This is the text that appears above the Name Your Price input field.', 'ignitewoo_gift_certs' ),
							'id' 		=> 'woocommerce_nyp_label_text',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default' 		=> __( 'Set Your Amount', 'ignitewoo_gift_certs' ),
							'desc_tip'	=>  true,
						),

			'button_text' => array(
							'title' => __( 'Add to Cart Button Text for Shop Page', 'ignitewoo_gift_certs' ),
							'desc' 		=> __( 'This is the text that appears on the Add to Cart buttons on the Shop Pages.', 'ignitewoo_gift_certs' ),
							'id' 		=> 'woocommerce_nyp_button_text',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default' 		=> __( 'Select Amount', 'ignitewoo_gift_certs' ),
							'desc_tip'	=>  true,
						),

			'text_single' => array(
							'title' => __( 'Add to Cart Button Text for Single Product Page', 'ignitewoo_gift_certs' ),
							'desc' 		=> __( 'This is the text that appears on the Add to Cart buttons on the Single Product Pages.', 'ignitewoo_gift_certs' ),
							'id' 		=> 'woocommerce_nyp_button_text_single',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default' 	=> __( 'Add to Cart', 'ignitewoo_gift_certs' ),
							'desc_tip'	=>  true,
						),

					
			'title2' => array(
						'title' 		=> __( 'Heading Settings', 'ignitewoo_gift_certs' ),
						'type' 			=> 'title',
						'description' 		=> __( 'The settings affect the header shown inside gift certificate email messages sent to buyers or recipients.<br>Pay special attention to the %s and %s%% indicators - these will be replaced with the actual value of the gift certificate / store credit / coupon.<br>When all else fails, use the default strings indicated for each setting below.<br><em>These fields cannot be blank.</em><br> <em>Be sure to test these by making a test purchase!</em>', 'ignitewoo_gift_certs' ),
						),
			'heading_store_credit' => array(
						'title' 		=> __( 'Heading - Store Credit', 'ignitewoo_gift_certs' ),
						'type' 			=> 'text',
						'class'			=> 'ignwide',
						'description' 		=> __( '<br> Header title shown inside the gift certificate email message sent to buyers or recipients when the gift certificate is a store credit.<br>Default: You received store credit worth %s', 'ignitewoo_gift_certs' ),
						'default'		=> __( 'You received store credit worth %s', 'ignitewoo_gift_certs' ),
						),
			'heading_percent_product' => array(
						'title' 		=> __( 'Heading - Percent Off Product', 'ignitewoo_gift_certs' ),
						'type' 			=> 'text',
						'class'			=> 'ignwide',
						'description' 		=> __( '<br> Header title shown inside the gift certificate email message sent to buyers or recipients when the gift certificate is for a percentage off a specific product.<br>Default: You received a coupon worth %s%% off a product.', 'ignitewoo_gift_certs' ),
						'default'		=> __( 'You received a coupon worth %s%% off a product.', 'ignitewoo_gift_certs' ),
						),
			'heading_percent' => array(
						'title' 		=> __( 'Heading - Entire Purchase', 'ignitewoo_gift_certs' ),
						'type' 			=> 'text',
						'class'			=> 'ignwide',
						'description' 		=> __( '<br> Header title shown inside the gift certificate email message sent to buyers or recipients when the gift certificate is for a percentage off an entire purchase.<br>Default: You received a coupon worth %s%% off your entire purchase.', 'ignitewoo_gift_certs' ),
						'default'		=> __( 'You received a coupon worth %s%% off your entire purchase.', 'ignitewoo_gift_certs' ),
						),
			'heading_fixed_cart' => array(
						'title' 		=> __( 'Heading - Cart Amount', 'ignitewoo_gift_certs' ),
						'type' 			=> 'text',
						'class'			=> 'ignwide',
						'description' 		=> __( '<br> Header title shown inside the gift certificate email message sent to buyers or recipients when the gift certificate is for X dollars off an entire purchase.<br>Default: You received a coupon worth %s off your entire purchase.', 'ignitewoo_gift_certs' ),
						'default'		=> __( 'You received a coupon worth %s off your entire purchase.', 'ignitewoo_gift_certs' ),
						),
			'heading_fixed_product' => array(
						'title' 		=> __( 'Heading - Product Amount', 'ignitewoo_gift_certs' ),
						'type' 			=> 'text',
						'class'			=> 'ignwide',
						'description' 		=> __( '<br> Header title shown inside the gift certificate email message sent to buyers or recipients when the gift certificate is for a fixed amount off a product<br>Default: You received a coupon worth %s off a product.', 'ignitewoo_gift_certs' ),
						'default'		=> __( 'You received a coupon worth %s off a product.', 'ignitewoo_gift_certs' ),
						),
			'title3' => array(
						'title' 		=> __( 'Other Message Content Settings', 'ignitewoo_gift_certs' ),
						'type' 			=> 'title',
						'description' 		=> __( '', 'ignitewoo_gift_certs' ),
						),
			'custom_codes' => array(
						'title' 		=> __( 'Paste in a list of codes', 'ignitewoo_gift_certs' ),
						'type' 			=> 'textarea',
						'description' 		=> __( 'You can paste in a list of codes and optionally enable any gift certificate / store credit / voucher product to use codes from this list when generating a new voucher.', 'ignitewoo_gift_certs' ),
						'default'		=> '',
						),
			'use_qr' => array(
						'title' 		=> __( 'Insert QR Code', 'ignitewoo_gift_certs' ),
						'type' 			=> 'checkbox',
						'description' 		=> __( ' Enable this option to insert a QR Code at the bottom  of the notification email sent to buyers or recipients.<br>This is very useful if customers print the certificates / coupons to bring into a physical store front.<br> Note that the QR Code will link to the associated order in the WordPress admin panel.<br><em> Customers will not be able to access the order in the admin area since they will not be logged in as an administrator.</em>', 'ignitewoo_gift_certs' ),
						'default'		=>'yes',
						),
			'qr_links_to' => array(
						'title' 		=> __( 'QR Code Links To', 'ignitewoo_gift_certs' ),
						'type' 			=> 'select',
						'description' 		=> __( ' Where the QR code links to', 'ignitewoo_gift_certs' ),
						'default'		=> 'order',
						'options'		=> array( 
								'order' => __( 'Order', 'ignitewoo_gift_certs' ), 
								'coupon' => __( 'Coupon', 'ignitewoo_gift_certs' ) 
								),
						
						),
			'buyer_template_prefix' => array(
						'title' 		=> __( "Buyer's Message Prefix", 'ignitewoo_gift_certs' ),
						'type' 			=> 'text',
						'class'			=> 'ignwide',
						'description' 		=> __( '<br>During checkout buyers can optionally choose to send the certificate to someone other than themselves. They can also optionally enter a custom message to send to the recipient, which is appended to the email message containing the certificate code. Enter a preamble or text prefix that will preceed the actual custom message body. Insert %s where you want the buyer name to appear.', 'ignitewoo_gift_certs' ),
						'default'		=> "Here's a message from %s",
						),
			'buyer_template' => array(
						'title' 		=> __( "Buyer's Message Body", 'ignitewoo_gift_certs' ),
						'type' 			=> 'textarea',
						'class'			=> 'ignwide',
						'description' 		=> __( "Use %s to indicate where you want the buyer's custom message to appear", 'ignitewoo_gift_certs' ),
						'default'		=> "----------------------------------------\n%s\n----------------------------------------\n",
						),


		);

	}

	
	function admin_options() {
		global $wpdb;
	?>
		<style>
			#woocommerce_extensions { display:none !important; }
			#woocommerce_woocommerce_gift_certificates_email_subject { width: 400px; }
			.ignwide { width: 400px; }
		</style>

		<script>
			jQuery( document ).ready( function() {
			
				jQuery( "#ign_gc_preview" ).click( function() {

					go = confirm( '<?php _e( 'Save your settings if you have not done so yet. Ready to preview?', 'ignitewoo_gift_certs' ) ?>' );

					if ( !go )
						return false;
						
					var n = '<?php echo wp_create_nonce( 'ign_gc_preview' ) ?>';
					
					jQuery.post( ajaxurl, { action: 'ign_gc_preview', n: n }, function( data ) {
					
						if ( null == data || data.length <= 0 )
							data = 'Error loading or parsing your email template.';

						jQuery("#ign_gc_preview_box").attr( 'style', 'width:650px; height:400px; overflow: auto; border: 1px solid #ccc' );
						var $frame = jQuery( "#ign_gc_preview_box" );
						var doc = $frame[0].contentWindow.document;
						var $body = jQuery( 'body', doc );
						$body.html( '' );
						$body.html( data );
								
					
					});
					
					return false;
				});
			});
		</script>
		
		<h3><?php _e('Gift Certificates', 'ignitewoo_gift_certs'); ?></h3>

		<p><?php echo sprintf( __('Adjust the settings to suit your needs. Be sure to review the <a href="%s" target="_blank">documentation</a>.', 'ignitewoo_gift_certs'), 'http://ignitewoo.com/ignitewoo-software-documentation/' ); ?></p>

		<p><?php //_e( "To customize other aspects not found in these settings, copy the file ' templates/voucher_email.php' ( located in the plugin directory ) to your theme's directory and edit the file as you like.", 'ignitewoo_gift_certs' ) ?></p>

		<p><?php //_e( 'For help customizing aspect of email templates ( or other templates related to WooCommerce ) review the <a href="http://wcdocs.woothemes.com/codex/template-structure/" target="_blank">WooCommerce template documentation</a>.', 'ignitewoo_gift_certs' ) ?>
		
		<table class="form-table">

		    <tr>
			<td>

			    <table>

				<?php $this->generate_settings_html(); ?>

				<?php $style = 'border: 2px solid #009f00; padding-top: 8px;'; ?>

				<?php if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) $style = 'border: 2px solid #009f00; padding-top: 0'; ?>

				<tr>
					<td style="vertical-align:top"><button id="ign_gc_preview" class="button" style="<?php echo $style ?>"><?php _e( 'Preview Email Template', 'ignitewoo_gift_certs' ) ?></button>
					</td>
					<td style="vertical-align:top">
						<iframe id="ign_gc_preview_box" style=""></iframe>
					</td>
				</tr>
				
			    </table>

			</td>

			<td valign="top" style="vertical-align:top">
				    <div style="float:right;  width:250px; border: 3px solid #333; background-color: #f7f7f7; padding: 12px 0; font-weight:bold; font-style:italic; margin-top: 15px; text-align:center; border-radius:7px;-webkit-border-radius:7px">
					    <a title=" More Extensions + Custom WooCommerce Site Development " href="http://ignitewoo.com" target="_blank" style="color:#0000cc; text-decoration:none">
						<img style="height:50px" src="data:image/png;base64,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%3D%3D">
					    </a>
					    <br>
					    <?php _e('Get more custom plugins <br/> + Custom development', 'ignitewoo_gift_certs'); ?>
					    <br><br>
					    <a title=" <?php _e('More Extensions + Custom WooCommerce Site Development', 'ignitewoo_gift_certs'); ?> " href="http://ignitewoo.com" target="_blank" style="color:#0000cc; text-decoration:none"><?php _e('Contact us', 'ignitewoo_gift_certs'); ?></a>
				    </div>
			</td>
		    </tr>
		</table>

	<?php
	}


	// process rate type settings
	function process_gift_cert_settings() {

		if ( isset( $_POST['gift_cert_authorized_user'] ) )
			update_option( 'gift_cert_authorized_user', $_POST['gift_cert_authorized_user'] );

		if ( isset( $_POST['gift_cert_send_tracking_number'] ) )
			update_option( 'gift_cert_send_tracking_number', 1 );
		else
			update_option( 'gift_cert_send_tracking_number', 0 );

		if ( isset( $_POST['gift_cert_debug_on'] ) )
			update_option( 'gift_cert_debug_on', 1 );
		else
			update_option( 'gift_cert_debug_on', 0 );

	}
}