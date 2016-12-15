<?php
/*
Copyright (c) 2012-2015 IgniteWoo.com - All Rights Reserved
*/

/**
 * Welcome Page Class
 *
 * Shows a feature overview for the new version
 *
 * Adapted from code in EDD (Copyright (c) 2012, Pippin Williamson), WordPress, and WooCommerce
 *
 *
 * @since 3.3
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );


class IgniteWoo_GC_Welcome_Page {

	private $plugin;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menus') );
		
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		
		add_action( 'admin_init', array( $this, 'welcome' ), 1 );
	}

	/**
	 * Add admin menus/screens
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menus() {

		$welcome_page_title = __( 'Welcome to IgniteWoo', 'ignitewoo_gift_certs' );

		// About
		$about = add_dashboard_page( $welcome_page_title, $welcome_page_title, 'manage_options', 'ign-gc-about', array( $this, 'about_screen' ) );

		add_action( 'admin_print_styles-'. $about, array( $this, 'admin_css' ) );

	}

	/**
	 * admin_css function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_css() {
	

		
	}

	/**
	 * Into text/links shown on all about pages.
	 *
	 * @access private
	 * @return void
	 */
	private function intro() {
		global $woocommerce, $ignite_gift_certs;

		$major_version = $ignite_gift_certs->version;
		
		?>
		<h2 style="font-weight:bold"><?php _e( 'Welcome to IgniteWoo Gift Certificates Pro', 'ignitewoo_gift_certs' ); ?></h2>


		<div class="ign-badge" style="float:right">
			
		</div>

		<div class="about-text woocommerce-about-text">
			
			<?php

				$message = __( 'Thanks for installing!', 'ignitewoo_gift_certs' );

				printf( __( '%s Version %s is more powerful than ever before. We hope you enjoy it.', 'ignitewoo_gift_certs' ), $message, $major_version );
			?>
			
			
		</div>


		<p class="woocommerce-actions">
			<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<=' ) ) { ?>
				<a href="<?php echo admin_url('admin.php?page=woocommerce_settings&tab=gift_cert'); ?>" class="button button-primary"><?php _e( 'Settings', 'ignitewoo_gift_certs' ); ?></a>
			<?php } else { ?>
				<a href="<?php echo admin_url('admin.php?page=wc-settings&tab=gift_cert'); ?>" class="button button-primary"><?php _e( 'Settings', 'ignitewoo_gift_certs' ); ?></a>
			<?php } ?>
			
			<a class="docs button button-primary" href="http://ignitewoo.com/ignitewoo-software-documentation/"><?php _e( 'Docs', 'ignitewoo_gift_certs' ); ?></a>
			
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://ignitewoo.com/" data-text="Powerful #ecommerce extensions for #WooCommerce. Plus great support." data-via="IgniteWoo" data-size="large" data-hashtags="WooCommerce">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</p>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( $_GET['page'] == 'ign-gc-about' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wc-about' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'ignitewoo_gift_certs' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Output the about screen.
	 *
	 * @access public
	 * @return void
	 */
	public function about_screen() {
		global $woocommerce,$ignite_gift_certs
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<div class="changelog">

				<br/>
				
				<h2 style="font-style:italic; color:#cf0000">
			
					<?php _e( 'Make certain that you read this page carefully. There are important changes that you need to become aware of.', 'ignitewoo_gift_certs' ) ?>
			
				</h2>
				
			
				<h3><?php _e( 'New Features', 'ignitewoo_gift_certs' ); ?></h3>

				<div class="feature-section col three-col">

					<div>
						<img src="<?php echo $ignite_gift_certs->plugin_url . '/assets/images/voucher-templates.png'; ?>" alt="Voucher templates screenshot" style="width: auto; margin: 0 0 1em; max-height: 106px; border: 1px dotted #aaa" />
						<h4><?php _e( 'New PDF Vouchers', 'ignitewoo_gift_certs' ); ?></h4>
						<p><?php _e( 'We\'ve added the ability for you to create totally customizable PDF vouchers. You can optionally allow shoppers to select with voucher style they\'d like to receive.', 'ignitewoo_gift_certs' ); ?></p>
						
					</div>

					<div>
						<img src="<?php echo $ignite_gift_certs->plugin_url . '/assets/images/order-voucher-data.png'; ?>" alt="Order panel screenshot" style="width: 80%; margin: 0 0 1em; min-height: 106px; border: 1px dotted #aaa" />
						<h4><?php _e( 'Order Administration', 'ignitewoo_gift_certs' ); ?></h4>
						<p><?php _e( 'Now you can view voucher information directly within each order. And you update any voucher\'s remaining balance on the fly from directly within the associated order.', 'ignitewoo_gift_certs' ); ?></p>
					</div>


					<div class="last-feature">
						<img src="<?php echo $ignite_gift_certs->plugin_url . '/assets/images/order-voucher-data2.png'; ?>" alt="Order data screenshot" style="width: 80%; margin: 0 0 1em; max-height: 106px; border: 1px dotted #aaa" />
						<h4><?php _e( 'Order Administration', 'ignitewoo_gift_certs' ); ?></h4>
						<p><?php _e( 'Each voucher in an order now has meta data showing you the value and who it was sent to.', 'ignitewoo_gift_certs' ); ?></p>
					</div>
					
					
					
					<div>
						<img src="<?php echo $ignite_gift_certs->plugin_url . '/assets/images/barcode.png'; ?>" alt="Voucher templates screenshot" style="width: 80%; margin: 0 0 1em; min-height: 106px; border: 1px dotted #aaa" />
						<h4><?php _e( 'Bar Codes', 'ignitewoo_gift_certs' ); ?></h4>
						<p><?php _e( 'With the new PDF voucher feature you can also opt to insert a barcode for easy integration with your point of sale (POS) system.', 'ignitewoo_gift_certs' ); ?></p>
						
					</div>

					<div>
						<img src="<?php echo $ignite_gift_certs->plugin_url . '/assets/images/customcodes.png'; ?>" alt="Order panel screenshot" style="width: 80%; margin: 0 0 1em; min-height: 106px; border: 1px dotted #aaa" />
						<h4><?php _e( 'Use Your Own Codes', 'ignitewoo_gift_certs' ); ?></h4>
						<p><?php _e( 'Now you can upload you own list of custom voucher codes to be used when generating new vouchers in your store. Combine this feature with PDF vouchers and barcodes for for easy integration with your point of sale (POS) system.', 'ignitewoo_gift_certs' ); ?></p>
					</div>


					<div class="last-feature">
						<img src="<?php echo $ignite_gift_certs->plugin_url . '/assets/images/qr-code-setting.png'; ?>" alt="QR code setting screenshot" style="width: 80%; margin: 0 0 1em; min-height: 106px; border: 1px dotted #aaa" />
						<h4><?php _e( 'New QR Code Setting', 'ignitewoo_gift_certs' ); ?></h4>
						<p><?php _e( 'You now can choose whether the QR Code links to the associated order or the coupon itself.', 'ignitewoo_gift_certs' ); ?></p>
					</div>
				</div>
				
				<h3><?php _e( 'New Templates and Product Settings', 'ignitewoo_gift_certs' ); ?></h3>

				<p style="font-weight:bold;font-style:italic"><?php _e( 'Be certain to review the new templates carefully and make any adjustments to your custom versions of the template files', 'ignite_gift_certs' ) ?></p>
				
				<div class="feature-section col three-col">
				
					<div class="psettings">
						<h4><?php _e( 'Email Template', 'ignitewoo_gift_certs' ); ?></h4>
						<p><?php _e( 'The email template is revised to support PDF voucher attachments and we\'ve added new variables for more flexible customization. Be sure to compare the new template with any customizations that you made.', 'ignitewoo_gift_certs' ); ?></p>
					</div>

					<div class="psettings">
						<h4><?php _e( 'Checkout Template', 'ignitewoo_gift_certs' ); ?></h4>
						<p><?php _e( 'The checkout page voucher template is revised to support PDF vouchers. You may need to update yours if you\'ve customized it.', 'ignitewoo_gift_certs' ); ?></p>
					</div>

					<div class="last-feature psettings">
						<h4><?php _e( 'Product Settings', 'ignitewoo_gift_certs' ); ?></h4>
						<p><?php _e( 'We added a new setting to the product editor so that you can attachment any number of PDF voucher templates to the product. If you attach more than one then shoppers can select which style they want to receive!', 'ignitewoo_gift_certs' ); ?></p>
					</div>

				</div>


				<h3><?php _e( 'Documentation', 'ignitewoo_gift_certs' ); ?></h3>

				<div class="feature-section col">

					<div style="width:90%;min-height:auto">
						<p><?php _e( 'We\'ve updated the <a href="http://ignitewoo.com/ignitewoo-software-documentation/" target="_blank">documentation on our site</a> to explain all the new features and how to use them. Head over to our site and read it carefully. If you\'re already familiar with using Gift Certificates Pro review the section about PDF vouchers to get up to speed on what you need to do.', 'ignitewoo_gift_certs' ); ?></p>
						
					</div>


				</div>

			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'woocommerce_settings' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to WooCommerce Settings', 'ignitewoo_gift_certs' ); ?></a>
			</div>
		</div>
		
		<style>
		.psettings {
			float:left;
			margin-left: 10px;
			background-color: #fff;
			min-height: 205px !important;
		}
		.about-wrap .feature-section div {
			float:left;
			margin-left: 10px;
			min-height: 305px;
			width: 30%;
		}
		.about-wrap .feature-section .last-feature:after {
			clear:both;
		}
		</style>
		<?php
	}




	/**
	 * Sends user to the welcome page on first activation
	 */
	public function welcome() {

		// Bail if no activation redirect transient is set
		if ( !get_transient( '_ign_gc_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_ign_gc_activation_redirect' );

		// Bail if activating from network, or bulk, or within an iFrame
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) || defined( 'IFRAME_REQUEST' ) )
			return;

		if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' == $_GET['action'] ) && ( isset( $_GET['plugin'] ) && strstr( $_GET['plugin'], 'woocommerce-gift-certificates.php' ) ) )
			return;

		wp_safe_redirect( admin_url( 'index.php?page=ign-gc-about' ) );
		
		exit;
	}
	

	/**
	 * Add styles just for this page, and remove dashboard page links.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_head() {
		global $woocommerce;

		remove_submenu_page( 'index.php', 'ign-gc-about' );

		?>
		<style type="text/css">
			/*<![CDATA[*/
			.ign-badge {
				padding-top: 150px;
				height: 52px;
				width: 185px;
				color: #9c5d90;
				font-weight: bold;
				font-size: 14px;
				text-align: center;
				text-shadow: 0 1px 0 rgba(255, 255, 255, 0.6);
				margin: 0 -5px;
				background: url('http://ignitewoo.com/wp-content/themes/eleven40/images/woocommerce-plugins-custom-development.png') no-repeat center center;
			}

			@media
			(-webkit-min-device-pixel-ratio: 2),
			(min-resolution: 192dpi) {
				.ign-badge {
					background-image:url('http://ignitewoo.com/wp-content/themes/eleven40/images/woocommerce-plugins-custom-development.png');
					background-size: 173px 194px;
				}
			}

			.about-wrap .ign-badge {
				position: absolute;
				top: 0;
				right: 0;
			}
			.woocommerce-message {
				position: relative;
				z-index: 100;
				border: 1px solid #b76ca9!important;
				-moz-border-radius: 3px;
				-webkit-border-radius: 3px;
				border-radius: 3px;
				-webkit-box-shadow: inset 0 0 15px rgba(0,0,0,0.04);
				-moz-box-shadow: inset 0 0 15px rgba(0,0,0,0.04);
				box-shadow: inset 0 0 15px rgba(0,0,0,0.04);
				overflow: hidden;
				padding: 10px 0 10px!important;
				background: #fff no-repeat right bottom!important;
			}

			.woocommerce-message .squeezer {
				max-width: 960px;
				margin: 0;
				padding: 0 10px;
				text-align: left;
				overflow: hidden;
			}

			.woocommerce-message h4 {
				margin: 5px 10px 5px 0;
				font-size: 14px;
				line-height: 27px;
				font-family: "Helvetica Neue",Helvetica,Arial,"Lucida Grande",Verdana,"Bitstream Vera Sans",sans-serif;
				font-weight: normal;
				color: #555;
				text-shadow: none;
				-moz-border-radius: 5px;
				-webkit-border-radius: 5px;
				border-radius: 5px;
				float: left;
				vertical-align: middle;
			}

			.woocommerce-message p {
				margin: 5px 0!important;
				padding: 1px 2px!important;
				float: left!important;
				line-height: 27px;
				vertical-align: middle;
			}

			.woocommerce-message .twitter-share-button {
				vertical-align: middle;
				margin-left: 3px;
			}

			p.woocommerce-actions a.button-primary,.woocommerce-message a.button-primary {
				font-size: 14px!important;
				line-height: 16px!important;
				height: auto!important;
				-webkit-border-radius: 3px;
				border-radius: 3px;
				margin: 0 5px 0 0;
				padding: 5px 12px;
				vertical-align: middle;
				color: #fff;
				text-align: center;
				text-decoration: none;
				border: 1px solid #76456d;
				-webkit-transition: none;
				-moz-transition: none;
				cursor: pointer;
				outline: 0;
				font-family: "Helvetica Neue",Helvetica,Arial,"Lucida Grande",Verdana,"Bitstream Vera Sans",sans-serif;
				text-shadow: 0 1px 0 rgba(0,0,0,0.3);
				background-color: #a46497;
				background-image: -webkit-gradient(linear,left top,left bottom,from(#a46497),to(#864f7b));
				background-image: -webkit-linear-gradient(top,#a46497,#864f7b);
				background-image: -moz-linear-gradient(top,#a46497,#864f7b);
				background-image: -ms-linear-gradient(top,#a46497,#864f7b);
				background-image: -o-linear-gradient(top,#a46497,#864f7b);
				background-image: linear-gradient(to bottom,#a46497,#864f7b);
				-webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),inset 0 -1px 0 rgba(0,0,0,0.1);
				box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),inset 0 -1px 0 rgba(0,0,0,0.1);
			}

			p.woocommerce-actions a.button-primary:hover,.woocommerce-message a.button-primary:hover {
				text-shadow: 0 -1px 0 rgba(0,0,0,0.3);
				border: 1px solid #76456d;
				background-color: #ad74a2;
				background-image: -webkit-gradient(linear,left top,left bottom,from(#ad74a2),to(#864f7b));
				background-image: -webkit-linear-gradient(top,#ad74a2,#864f7b);
				background-image: -moz-linear-gradient(top,#ad74a2,#864f7b);
				background-image: -ms-linear-gradient(top,#ad74a2,#864f7b);
				background-image: -o-linear-gradient(top,#ad74a2,#864f7b);
				background-image: linear-gradient(to bottom,#ad74a2,#864f7b);
				-webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),inset 0 -1px 0 rgba(0,0,0,0.1);
				box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),inset 0 -1px 0 rgba(0,0,0,0.1);
			}

			p.woocommerce-actions a.button-primary:active,.woocommerce-message a.button-primary:active {
				border: 1px solid #76456d;
				text-shadow: 0 1px 0 rgba(0,0,0,0.3);
				background-color: #864f7b;
				background-image: -webkit-gradient(linear,left top,left bottom,from(#864f7b),to(#864f7b));
				background-image: -webkit-linear-gradient(top,#864f7b,#a46497);
				background-image: -moz-linear-gradient(top,#864f7b,#a46497);
				background-image: -ms-linear-gradient(top,#864f7b,#a46497);
				background-image: -o-linear-gradient(top,#864f7b,#a46497);
				background-image: linear-gradient(to bottom,#a46497,#a46497);
				-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.2);
				box-shadow: inset 0 1px 1px rgba(0,0,0,0.2);
			}

			p.woocommerce-actions a.skip,.woocommerce-message a.skip,p.woocommerce-actions a.docs,.woocommerce-message a.docs {
				opacity: .7;
			}

			p.woocommerce-actions .twitter-share-button,.woocommerce-message .twitter-share-button {
				vertical-align: middle;
				margin-left: 3px;
			}

			p.woocommerce-actions {
				margin-bottom: 2em;
			}

			.woocommerce-about-text {
				margin-bottom: 1em!important;
				font-size:18px;
			}
			.about-wrap h3 {
				padding-top: 0px;
			}
			.psettings { 
				background: none repeat scroll 0 0 #F3F3F3;
				padding: 12px 0 0 9px;
				width: 29% !important;
			}
			/*]]>*/
		</style>
		<?php
	}

}

$ign_gc_welcome = new IgniteWoo_GC_Welcome_Page();