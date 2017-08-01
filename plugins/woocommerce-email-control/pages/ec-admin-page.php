<?php
global $wp_scripts, $woocommerce, $woocommerce, $current_user;
?>

<div class="wp-core-ui ec ec-admin-page pe-in-admin-page">

	<!-- Left Column -->
	<div class="left-column">
		
		<?php
		// Load WC Emails
		// ----------------------------------------
		
		// Load mailer
		if ( class_exists('WC') ) {
			$mailer = WC()->mailer();
			$mails = $mailer->get_emails();
		}
		else{
			$mailer = $woocommerce->mailer();
			$mails = $mailer->get_emails();
		}
		
		
		$show_type = isset( $_REQUEST["ec_email_type"] ) ? $_REQUEST["ec_email_type"] : current( $mails )->id ;
		
		
		// Load WC Orders
		// ----------------------------------------
		
		$limit_orders = 800;
		
		$order_collection = new WP_Query(array(
			'post_type'			=> 'shop_order',
			'post_status'		=> array_keys( wc_get_order_statuses() ),
			'posts_per_page'	=> $limit_orders,
		));
		
		$order_collection = $order_collection->posts;
		$latest_order = ( count( $order_collection ) ) ? current( $order_collection )->ID : FALSE ;
		
		$show_order = isset( $_REQUEST["ec_email_order"] ) ? $_REQUEST["ec_email_order"] : $latest_order ;
		
		$src_url = "";
		$src_url .= 'admin.php?';
		$src_url .= 'page=woocommerce_email_control';
		$src_url .= '&ec_render_email=true';
		$src_url .= '&ec_email_type=' . $show_type;
		
		if ( $show_order ) {
			$src_url .= '&ec_email_order='.$show_order;
		}
		?>
		
		<div class="ec-admin-panel ec-admin-panel-controls">
			
			<div class="main-controls-top-button-row">
				<?php
				$backlink = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? $_SERVER['HTTP_REFERER'] : admin_url() ;
				if ( strrpos( $backlink, 'login' ) != -1 ) $backlink = admin_url();
				?>
				<a class="button-primary exit-button" href="<?php echo $backlink; ?>">
					<span class="dashicons dashicons-arrow-left ec-back-icon"></span>
					<?php _e( "Back", 'email-control' ); ?>
				</a>
			</div>
			
			<form class="main-controls-form" id="render_email" name="render_email" data-name="Render Email"  action="<?php echo wp_nonce_url( admin_url( $src_url ), 'email-control'); ?>" target="preview-email-iframe" method="post" >
				<div class="main-controls">
					
					<div class="main-controls-element main-controls-element-heading-block">
						<div class="heading-first">
							<h2><?php _e( "Email Customizer", 'email-control' ); ?></h2>
						</div>
					</div>
					
					<?php
					global $ec_email_themes;
					
					if ( isset( $ec_email_themes ) && is_array( $ec_email_themes ) ) :
						?>
						<div class="main-controls-element">
							<label class="controls-label" for="ec_email_theme" title="<?php _e( "Choose which email theme to preview. Then click 'Use' to use it for all your future WooCommerce emails.", 'email-control' ); ?>">
								<?php _e( "Email Theme to show", 'email-control' ); ?> 
								<span class="help-icon">&nbsp;&nbsp;&nbsp;&nbsp;</span>
							</label>
							<div class="controls-field">
								<div class="controls-inner-row">
									<select class="w-select control-dropdown" id="ec_email_theme" name="ec_email_theme">
										<option value="woocommerce_original">
											<?php _e( 'WooCommerce (original, not editable)', 'email-control' ); ?>
										</option>
										
										<?php
										$ec_theme_selected = get_option( 'ec_template' );
										foreach ( $ec_email_themes as $ec_email_theme_id => $ec_email_theme_args ) {
											
											$name = $ec_email_theme_args['name'];
											$woocommerce_version_pass = ec_check_theme_version( $ec_email_theme_id );
											?>
											<option class="theme-select" value="<?php echo $ec_email_theme_id; ?>" <?php echo ( $ec_email_theme_id == $ec_theme_selected ) ? "selected" : "" ; ?> >
												<?php echo $name; ?><?php if ( ! $woocommerce_version_pass ) echo ' ' . __( '(WooCommerce update required)', 'email-control' ) ?>
											</option>
											<?php
										}
										?>
									</select>
									<input type="hidden" name="ec_email_theme_active" id="ec_email_theme_active" class="ec_email_theme_active" value="<?php echo $ec_theme_selected; ?>" >
									<input type="hidden" name="ec_email_theme_preview" id="ec_email_theme_preview" class="ec_email_theme_preview">
								</div>
								<div class="controls-inner-row controls-inner-button-row ec_theme_button_row" id="theme-commit">
									<a class="button" id="ec_cancel_email_theme" href="#"><i class="cxectrl-icon-reply"></i>&nbsp; <?php _e( "Don't Use", 'email-control' ); ?></a>
									<a class="button-primary" id="ec_save_email_theme" href="#"><i class="cxectrl-icon-check"></i>&nbsp; <?php _e( "Use", 'email-control' ); ?></a>
								</div>
							</div>
						</div>
						<?php
					endif;
					?>
					
					<div class="main-controls-element">
						<label class="controls-label" for="ec_email_type" title="<?php _e( 'Choose which email type to preview.', 'email-control' ); ?>">
							<?php _e( "Email Type to show", 'email-control' ); ?> 
							<span class="help-icon">&nbsp;&nbsp;&nbsp;&nbsp;</span>
						</label>
						<div class="controls-field">
							<div class="controls-inner-row">
								<select class="w-select control-dropdown" id="ec_email_type" name="ec_email_type">
									<option value="">
										<?php _e( "Select one...", 'email-control' ); ?>
									</option>
									<?php
									//Customer_Invoice
									if ( ! empty( $mails ) ) {
										foreach ( $mails as $mail ) {
											?>
											<option value="<?php echo $mail->id ?>" <?php echo ( $show_type == $mail->id ) ? "selected" : "" ; ?> >
												<?php echo ucwords( $mail->title ); ?>
											</option>
											<?php
										}
									}
									?>
								</select>
							</div>
						</div>
					</div>
					
					<div class="main-controls-element" id="ec_edit_content_controls">
						<label class="controls-label" for="ec_edit_content" title="<?php _e( "Customize the email you're showing in the preview. Then click 'Save & Publish' to save your customizations.", 'email-control' ); ?>">
							<?php _e( "Customize", 'email-control' ); ?> 
							<span class="help-icon">&nbsp;&nbsp;&nbsp;&nbsp;</span>
						</label>
						<div class="controls-field">
							<div class="controls-inner-row">
								<button class="button" id="ec_edit_content" href="#">
									<i class="cxectrl-icon-pencil"></i> <?php _e( "Customize", 'email-control' ); ?>
								</button>
							</div>
						</div>
						<?php if ( false ) : ?>
							<div class="get_themes button"><?php _e( "Get Customized Themes", 'email-control' ); ?></div>
							<div class="get_themes_flyout">
								<!-- More Themes -->
							</div>
						<?php endif; ?>
					</div>
					
					<div class="main-controls-element">
						<label class="controls-label" for="ec_email_order" title="<?php _e( 'Choose which order to use to populate the email preview.', 'email-control' ); ?>">
							<?php _e( "Order to show", 'email-control' ); ?> 
							<span class="help-icon">&nbsp;&nbsp;&nbsp;&nbsp;</span>
						</label>
						<div class="controls-field">
							<div class="controls-inner-row">
								
								<select class="w-select control-dropdown" id="ec_email_order" name="ec_email_order">
									
									<?php
									if ( count( $order_collection ) ) {
										?>
										<option value="">
											<?php _e( "Select one...", 'email-control' ); ?>
										</option>
										<?php
									}
									else {
										?>
										<option value="">
											<?php _e( "There are no orders to preview...", 'email-control' ); ?>
										</option>
										<?php
									}
									
									// Show the orders.
									foreach ( $order_collection as $order_item ) {
										
										// Get order object.
										$order = new WC_Order( $order_item->ID );
										
										?>
										<option value="<?php echo ec_order_get_id( $order ); ?>" data-order-email="<?php echo ec_order_get_billing_email( $order ); ?>" <?php echo ( ec_order_get_id( $order ) == $show_order ) ? "selected" : "" ; ?> >
											<?php echo $order->get_order_number(); ?> - <?php echo ec_order_get_billing_first_name( $order ); ?> <?php echo ec_order_get_billing_last_name( $order ); ?> (<?php echo ec_order_get_billing_email( $order ); ?>)
										</option>
										<?php
									}
									// If more than the orders limit then let the user know.
									if ( $limit_orders <= count( $order_collection ) ) {
										?>
										<option><?php printf( __( '...Showing the most recent %u orders', 'email-control' ), $limit_orders ); ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
					</div>
					
					<div class="main-controls-element">
						<label class="controls-label" title="<?php _e( 'More advanced debugging options.', 'email-control' ); ?>">
							<?php _e( "Debugging", 'email-control' ) ; ?> 
							<span class="help-icon">&nbsp;&nbsp;&nbsp;&nbsp;</span>
						</label>
						<div class="controls-field">
							<div class="controls-inner-row">
								
								<label for="header_info_userspecifc">
									<?php
									$field_default = "off";
									$field_value = get_user_meta( $current_user->ID, "ec_header_info_userspecifc", true);
									$field_value = ( $field_value )? $field_value : $field_default;
									
									if ( $field_value == "on" ) $header = true;
									else $header = false;
									?>
									
									<input type="checkbox" id="header_info_userspecifc" class="header_info_userspecifc" <?php if ( $field_value == "on" ) echo "checked"; ?> name="header_info_userspecifc" value="on" /> 
									<?php _e( 'Show Header & Template Info', 'email-control' ) ?>
								</label>
								
								<label for="show_errors_userspecifc">
									<?php
									$field_default = "off";
									$field_value = get_user_meta( $current_user->ID, "ec_show_errors_userspecifc", true);
									$field_value = ( $field_value )? $field_value : $field_default;
									
									if ( $field_value == "on" ) $header = true;
									else $header = false;
									?>
									
									<input type="checkbox" id="show_errors_userspecifc" class="show_errors_userspecifc" <?php if ( $field_value == "on" ) echo "checked"; ?> name="show_errors_userspecifc" value="on" /> 
									<?php _e( 'Show Error Notices', 'email-control' ) ?>
								</label>
							
							</div>
						</div>
					</div>
					
					<div class="main-controls-element">
						<?php
						//Load order for Email Customizer Send
						$order = new WC_Order( $show_order );
						?>
						<label class="controls-label" for="ec_send_email" title="<?php _e( 'Send a test email to any address. Use a comma separated list to send to multiple addresses', 'email-control' ); ?>">
							<?php _e( "Send a Test", 'email-control' ) ; ?> 
							<span class="help-icon">&nbsp;&nbsp;&nbsp;&nbsp;</span>
						</label>
						<div class="controls-field">
							<div class="controls-inner-row">
								<input type="text" class="" id="ec_send_email" name="ec_send_email" value="<?php echo ec_order_get_billing_email( $order ); ?>" placeholder="somone@somewhere.com, ..." />
								<button id="send_test" class="button send_test" name="send_test" type="button"><i class="cxectrl-icon-mail-alt"></i></button>
							</div>
						</div>
					</div>
					
				</div>
				<input type="hidden" class="" id="ec_approve_preview" name="ec_approve_preview" />
			</form>
		</div>
		
		
		<?php
		global $ec_email_themes;
			
		if ( isset( $ec_email_themes ) && is_array( $ec_email_themes ) ) :
			?>
			<div class="ec-admin-panel ec-admin-panel-edit-content">
				
				<div class="edit-top-controls">
					<span class="edit-top-control close_settings" id="close_edit_settings" >
						<i class="cxectrl-icon-left-open"></i>
					</span>
					<span class="edit-top-control hide_settings">
						<i class="cxectrl-icon-eye"></i>
					</span>
				</div>
				
				
				<?php
				// Add so the 'all' fields are shown
				$ec_email_types_for_settings = $mails;
				$ec_email_types_for_settings[] = (object)array('id'=>'all');
				
				foreach ( $ec_email_themes as $ec_email_theme_id => $ec_email_theme_args ) {
					
					if ( ! empty( $ec_email_types_for_settings) && ec_get_settings( $ec_email_theme_id ) ) {
						
						$form_id = "ec_settings_form_{$ec_email_theme_id}";
						
						$form_class = '';
						$form_class	.= "ec_settings_form ";
						$form_class	.= "ec_settings_form_{$ec_email_theme_id} ";
						?>
						<form
							id="<?php echo esc_attr( $form_id ); ?>"
							class="<?php echo esc_attr( $form_class ); ?>"
							>
							
							<input type="button" id="save_edit_settings" class="button-primary save_edit_settings" value='Saved' disabled />
							
							<?php
							foreach ( $ec_email_types_for_settings as $mail ) {
								
								$ec_email_type = $mail->id;
								
								// Get the related settings.
								$ec_settings = ec_get_settings( $ec_email_theme_id, array(
									'email-type' => $ec_email_type,
								) );
								
								if ( $ec_settings ) {
									
									// Get Sections array.
									$sections = ec_get_sections( $ec_email_theme_id );
									
									foreach ( $sections as $section_value_array ) {
										
										$ec_settings = ec_get_settings( $ec_email_theme_id, array(
											'email-type' => $ec_email_type,
											'section'    => $section_value_array['id'],
										) );
									
										if ( $ec_settings ) {
											?>
											<div
												class="section"
												data-ec-email-theme="<?php echo esc_attr( $ec_email_theme_id ); ?>"
												data-ec-email-type="<?php echo esc_attr( $ec_email_type ); ?>"
												>
												<h3>
													<?php echo $section_value_array['name'] ?>
													<?php if ( FALSE ) { ?>
														(<?php echo $ec_email_theme_id ?>, <?php echo $ec_email_type ?>)
													<?php } ?>
												</h3>
												
												<div class="section-inner">
													
													<?php EC_Settings::output_fields( $ec_settings ); ?>
													
												</div>
											</div>
											<?php
										}
									}
								}
							}
							?>
							
							<!-- <input type="hidden" name="ec_email_type" value="<?php echo $ec_email_type ?>" > -->
							<input type="hidden" name="ec_email_id" value="<?php echo $ec_email_theme_id ?>" >
							<input type="hidden" name="ec_action" value="yes" >
							
							<div class="main-controls-element forminp-tags ec-allowed-tags">
								<label class="controls-label" title="<?php echo esc_attr( __( 'Copy & paste any of these [shortcodes] to use dynamic text in your text.', 'email-control' ) ) ?>">
									<?php _e( 'Allowed Shortcodes:', 'email-control' ); ?>
									<span class="help-icon">&nbsp;</span>
								</label>
								<div class="controls-field">
									<div class="controls-inner-row">
										
										[ec_order] 
										[ec_firstname] 
										[ec_lastname] 
										[ec_email] 
										[ec_pay_link] 
										[ec_customer_note] 
										[ec_user_login] 
										[ec_account_link] 
										[ec_user_password] 
										[ec_reset_password_link] 
										[ec_login_link] 
										[ec_site_name] 
										[ec_site_link] 
										[ec_delivery_note] 
										[ec_shipping_method] 
										[ec_payment_method] 
										[ec_custom_field] <!-- <span class="ec-new-shortcode-badge"><?php _e( 'New', 'email-control' ) ?></span>  -->
										
										<p class="ec-allowed-shortcode-docs-link"><?php echo sprintf( __( 'For more shortcode documentation <a href="%s" target="_blank">click here</a>', 'email-control' ), 'https://www.cxthemes.com/documentation/email-customizer/shortcodes-email-customizer/' ); ?></p>
									</div>
								</div>
							</div>
							
						</form>
						<?php
					}
				}
				?>
				
			</div>
		<?php endif; ?>
	</div>
	<!-- Left Column -->

	<!-- Main Content -->
	<iframe id="preview-email-iframe" name="preview-email-iframe" border="0" src="<?php echo wp_nonce_url( admin_url( $src_url ), 'email-control'); ?>"></iframe>

</div>
