<?php
if ( ! current_user_can( 'manage_woocommerce' ) ) {
	wp_die( __( 'Cheatin&#8217; uh?', 'email-control' ) );
}

global $wp_scripts, $wpdb, $current_user, $order, $cxec_email_control;
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="<?php echo 'Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'); ?>" />
	<title>
		Prevew Email Theme
	</title>

	<?php
	do_action( 'ec_render_preview_head_scripts' );
	
	print_head_scripts(); //This is the main one
	print_admin_styles();
	?>
</head>
<body id="ec-theme" class="ec-theme" >
	
	<?php
	$mails = WC()->mailer()->get_emails();
	
	// Ensure gateways are loaded in case they need to insert data into the emails
	WC()->payment_gateways();
	WC()->shipping();
	
	/* Get Email to Show */
	if ( isset( $_REQUEST['ec_email_type'] ) )
		$email_type = $_REQUEST['ec_email_type'];
	else
		$email_type = current( $mails )->id;
	
	/* Get Email Theme to Show */
	if ( isset( $_REQUEST['ec_email_theme'] ) )
		$email_theme = $_REQUEST['ec_email_theme'];
	else
		$email_theme = false;

	/* Get Order to Show */
	if ( isset( $_REQUEST['ec_email_order'] ) ) {
		$order_id_to_show = $_REQUEST['ec_email_order'];
	}
	else{
		//Get the most recent order.
		$order_collection = new WP_Query(array(
			'post_type'			=> 'shop_order',
			'post_status'		=> array_keys( wc_get_order_statuses() ),
			'posts_per_page'	=> 1,
		));
		$order_collection = $order_collection->posts;
		$latest_order = current( $order_collection );
		$order_id_to_show = ec_order_get_id( $latest_order );
	}
	
	if ( ! get_post( $order_id_to_show ) ) :
		
		/**
		 * Display an error message if there isn't an order yet.
		 */
		
		?>
		<div class="email-preview pe-in-admin-page">
			<div class="main-content">
				
				<!-- ---------- No Order Warning ---------- -->
				
				<div class="compatability-warning-text">
					<span class="dashicons dashicons-welcome-comments"></span>
					<!-- <h6><?php _e( "You'll need at least one order to use Email Customizer properly", 'email-control' ) ?></h6> -->
					<h6><?php _e( "You'll need at least one order to preview all the email types correctly", 'email-control' ) ?></h6>
					<p>
						<?php _e( "Simply follow your store's checkout process to create at least one order, then return here to preview all the possible email types.", 'email-control' ) ?>
					</p>
				</div>
				
				<!-- ---------- / No Order Warning ---------- -->
			
				<?php
				
				/**
				 * Copied from class-wc-admin.php
				 */
				
				// load the mailer class
				$mailer = WC()->mailer();

				// get the preview email subject
				$email_heading = __( 'HTML Email Template', 'woocommerce' );

				// get the preview email content
				ob_start();
				include( WC()->plugin_path() . '/includes/admin/views/html-email-template-preview.php' );
				$message = ob_get_clean();
				
				// create a new email
				$email = new WC_Email();
				
				// wrap the content with the email template and then add styles
				$message = $email->style_inline( $mailer->wrap_message( $email_heading, $message ) );

				// print the preview email
				echo $message;
				?>
			
			</div>
		</div>
		<?php
		
	else :
		
		/**
		 * Display the chosen email.
		 */
		
		// prep the order.
		$order = new WC_Order( $order_id_to_show );
		
		if ( ! empty( $mails ) ) {
			foreach ( $mails as $mail ) {
				if ( $mail->id == $email_type ) {
					
					// Important Step: populates the $mail object with the necessary properties for it to Preview (or Send a test).
					// It also returns a BOOLEAN for whether we have checked this email types preview with our plugin.
					$compat_warning = $cxec_email_control->populate_mail_object( $order, $mail );
					
					// Info Meta Swicth on/off
					$header = ( get_user_meta( $current_user->ID, 'ec_header_info_userspecifc', true) ) ? get_user_meta( $current_user->ID, 'ec_header_info_userspecifc', true ) : 'off' ;
					?>
					
					<div class="email-preview pe-in-admin-page">
						<div class="main-content">
						
							<?php if ( ! ec_check_theme_version( $email_theme ) ) : ?>
								
								<!-- ---------- WooCommerce Version Warning ---------- -->
								
								<div class="compatability-warning">
									<div class="compatability-warning-text">
										<span class="dashicons dashicons-welcome-comments"></span>
										<h6><?php _e( "You need to update WooCommerce in order to use this theme", 'email-control' ) ?></h6>
										<p>
											<?php _e( "The theme you selected requires the latest version of WooCommerce - please first update, then return here and select it.", 'email-control' ) ?>
										</p>
									</div>
								</div>
								
								<!-- ---------- / WooCommerce Version Warning ---------- -->
								
							<?php elseif ( $compat_warning && ( $mail->id !== $_REQUEST['ec_approve_preview'] ) ) : ?>
								
								<!-- ---------- Compatability Warning ---------- -->
								
								<div class="compatability-warning">
									<div class="compatability-warning-text">
										<span class="dashicons dashicons-welcome-comments"></span>
										<h6><?php _e( "We've not seen this email type from this third party plugin before", 'email-control' ) ?></h6>
										<p>
											<?php _e( "Don't worry, the email will send fine, you just can't preview it. Customizing it - here are your options. Option 1: choose to show one of the other known emails and customize it (colors, sizes, etc) - the styling will be inherited if they have included the header and footer in the normal way. Option 2: choose to dismiss this message and see if it just works. If you see a blank screen then use option 1.", 'email-control' ) ?>
											<a href="#" id="ec_approve_preview_button" data-approve-preview="<?php echo $mail->id ?>" ><?php _e( 'Dismiss', 'email-control' ); ?></a>
										</p>
									</div>
								</div>
								
								<!-- ---------- / Compatability Warning ---------- -->
								
							<?php else: ?>
								
								<!-- ---------- Header Info ---------- -->
								
								<div class="header-info-holder" style="<?php if ( 'on' == $header ) echo 'display: block;'; ?>">
									
									<div class="header-info-meta-blocks">
									
										<div class="header-info-meta-block header-info-meta-block-title">
											<div class="header-info-meta-heading">
												<?php _e( "Header Info", 'email-control' ) ; ?> 
												<span class="help-icon" title="<?php _e( 'The header infomration that will be sent with the current email. This can be changed in WooCommerce Settings > Emails, or simply click Edit next to the relevant field to be taken there.', 'email-control' ); ?>" >&nbsp;&nbsp;&nbsp;&nbsp;</span>
											</div>
										</div>
										
										<div class="header-info-meta-block header-info-meta-block-subject">
											<div class="header-info-meta-heading">
												<?php _e( "Subject", 'email-control' ) ; ?>
											</div>
											<div class="header-info-meta">
												<span class="meta-value"><?php echo $mail->get_subject() ?></span> 
												<span class="meta-divider">|</span> 
												<a class="edit-meta" target="wc-settings" href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=email&section=' . "wc_email_{$mail->id}" ); ?>"><?php _e( "Edit", 'email-control' ) ; ?></a>
											</div>
										</div>
										
										<div class="header-info-meta-block header-info-meta-block-from-email">
											<div class="header-info-meta-heading">
												<?php _e( "From Email", 'email-control' ) ; ?>
											</div>
											<div class="header-info-meta">
												<span class="meta-value"><?php echo $mail->get_from_address() ?></span> 
												<span class="meta-divider">|</span> 
												<a class="edit-meta" target="wc-settings" href="<?php echo admin_url('admin.php?page=wc-settings&tab=email') ?>"><?php _e( "Edit", 'email-control' ) ; ?></a>
											</div>
										</div>
										
										<div class="header-info-meta-block header-info-meta-block-frome-name">
											<div class="header-info-meta-heading">
												<?php _e( "From Name", 'email-control' ) ; ?>
											</div>
											<div class="header-info-meta">
												<span class="meta-value"><?php echo $mail->get_from_name() ?></span> 
												<span class="meta-divider">|</span> 
												<a class="edit-meta" target="wc-settings" href="<?php echo admin_url('admin.php?page=wc-settings&tab=email') ?>"><?php _e( "Edit", 'email-control' ) ; ?></a>
											</div>
										</div>
										
										<div class="header-info-meta-block header-info-meta-block-to-email">
											<div class="header-info-meta-heading">
												<?php _e( "To Email", 'email-control' ) ; ?>
											</div>
											<div class="header-info-meta">
												<span class="meta-value"><?php echo ec_order_get_billing_email( $order ); ?></span>
											</div>
										</div>
										
										<div class="header-info-meta-block header-info-meta-block-to-email">
											<div class="header-info-meta-heading">
												<?php _e( "Templates In This Email", 'email-control' ) ; ?>
											</div>
											<div class="header-info-meta">
												<div class="template-info-holder">
													
													<div class="template-files">
														<?php
														// Get the email content here so we can generate a report which templates are used.
														@ $email_message = $mail->get_content();
														$email_message   = $mail->style_inline( $email_message );
														$email_message   = apply_filters( 'woocommerce_mail_content', $email_message );
														
														global $collect_email_template;
														foreach ( $collect_email_template as $template => $status ) {
															
															$full_path = wp_normalize_path( $template );
															$short_path = str_replace( wp_normalize_path( WP_CONTENT_DIR ), '&hellip;', $full_path );
															?>
															<div class="template-file">
																<pre title="<?php echo esc_attr( $full_path ); ?>" class="<?php echo esc_attr( $status ); ?>"><?php echo $short_path; ?></pre>
															</div>
															<?php
														}
														?>
													</div>
													
													<div class="template-legend">
														<span class="legend-text">
															<span class="legend-color legend-color-default"></span> 
															<?php _e( 'Email Customizer Default Templates', 'email-control' ) ; ?>
														</span>
														<span class="legend-text">
															<span class="legend-color legend-color-third-party"></span> 
															<?php _e( 'Non Email Customizer Templates', 'email-control' ) ; ?>
														</span>
														<span class="legend-text">
															<span class="legend-color legend-color-override"></span> 
															<?php _e( 'Overridden Templates', 'email-control' ) ; ?>
														</span>
													</div>
													
													<div class="template-explanation">
<?php _e( '<strong>Email Customizer Default Templates</strong> are those found inside our plugin folder. We update these templates when to incorporate any changes that WooCommerce, or the other plugins we support e.g Subscription and Bookings, make to theirs. If you want to customize these templates then please don\'t edit them here (in our plugin folder) as your changes will be lost next time you update. Rather use the suggested Template Overriding method explained below which will maintain your changes after updating.<br />
<strong>Non Email Customizer Templates</strong> are templates being pulled in from 3rd party plugins that we have chosen not to, or don\'t yet, support. Our emails simply include the code from these templates at the time the email is sent. Usually our header and footer are used and their template is used for the main content or body. These templates can be customized in the same way using the template override method explained below.<br/>
<strong>Overridden Templates</strong> are templates that you have customized and overridden using the method described in our <a href="https://www.cxthemes.com/documentation/email-customizer/customize-emails-by-overriding-templates-via-your-theme/" target="_blank">Customize Emails by Overriding Templates via your Theme documentation</a>. You will be responsible for copying any future code changes we make to our templates over to these custom overridden templates.', 'email-control' ); ?>
													</div>
													
												</div>
											</div>
										</div>
										
									</div>
									
									<a class="hide-icon hide-up" <?php if ( $header ) { ?> style="display:block" <?php } ?> ></a>
											
								</div>
								
								<a class="hide-icon hide-down" <?php if ( $header ) { ?> style="display:none" <?php } ?> ></a>
								
								<!-- ---------- / Header Info ---------- -->
								
								
								<!-- ----------  Email Content ---------- -->
								
								<?php
								// Info Meta Swicth on/off
								$show_errors = ( get_user_meta( $current_user->ID, 'ec_show_errors_userspecifc', true) ) ? get_user_meta( $current_user->ID, 'ec_show_errors_userspecifc', true ) : 'off' ;
								
								// Get the email contents. using @ to block ugly error messages showing in the preview.
								// The following mimics what WooCommerce does in it's `->send` method of WC_Email.
								if ( 'on' == $show_errors ) {
									$email_message = $mail->get_content(); // Show error notices.
								}
								else {
									@ $email_message = $mail->get_content(); // Hide error notices.
								}
								$email_message = $mail->style_inline( $email_message );
								$email_message = apply_filters( 'woocommerce_mail_content', $email_message );
								
								// Convert line breaks to <br>'s if the mail is type 'plain'.
								if ( 'plain' === $mail->email_type )
									$email_message = '<div style="padding: 35px 40px; background-color: white;">' . str_replace( "\n", '<br/>', $email_message ) . '</div>';
								
								// Display the email.
								echo $email_message;
								?>
								
								<!-- ----------  / Email Content ---------- -->
								
							<?php endif; ?>
							
						</div>
					</div>
					
					<?php
				}
			}
		}
	
	endif;
	?>
	
</body>
</html>

<?php exit; ?>
