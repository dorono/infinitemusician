<?php
/*
Copyright (c) 2012-2015 IgniteWoo.com - All Rights Reserved
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );

class Ignite_Gift_Certs_Product_Settings { 


	function __construct() { 

		add_action( 'save_post', array( &$this, 'process_product_meta' ) );		
		add_action( 'woocommerce_product_write_panel_tabs', array( &$this, 'panel_tabs' ), 99 );
		add_action( 'woocommerce_product_write_panels', array( &$this, 'write_panels' ), 99 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'custom_admin_pointers_header' ) );
	
	}


	function custom_admin_pointers_header() {
		if ( $this->custom_admin_pointers_check() ) {
		
			add_action( 'admin_print_footer_scripts', array( &$this, 'custom_admin_pointers_footer' ) );

			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_style( 'wp-pointer' );
		}
	}

	function custom_admin_pointers_check() {
		
		$admin_pointers = $this->custom_admin_pointers();

		foreach ( $admin_pointers as $pointer => $array ) {
			if ( $array['active'] )
				return true;
		}
	}

	function custom_admin_pointers_footer() {
		$admin_pointers = $this->custom_admin_pointers();
		?>
		<script type="text/javascript">
		/* <![CDATA[ */
		jQuery( document ).ready( function($) {
		<?php
		foreach ( $admin_pointers as $pointer => $array ) {
			if ( $array['active'] ) {
				?>
				$( '<?php echo $array['anchor_id']; ?>' ).pointer( {
					content: '<?php echo $array['content']; ?>',
					position: {
						edge: '<?php echo $array['edge']; ?>',
						align: '<?php echo $array['align']; ?>'
					},
					close: function() {
						$.post( ajaxurl, {
							pointer: '<?php echo $pointer; ?>',
							action: 'dismiss-wp-pointer'
						} );
					}
				} ).pointer( 'open' );
				<?php
			}
		}
		?>
		})
		/* ]]> */
		</script>
		<?php
	}

	function custom_admin_pointers() {
	
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		
		$version = '1_0'; // replace all periods in 1.0 with an underscore
		
		$prefix = 'custom_admin_pointers' . $version . '_';

		$new_pointer_content = '<h3>' . __( "IgniteWoo Gift Certficates Pro" ) . '</h3>';
		$new_pointer_content .= '<p>' . __( 'Gift certificate / coupon code settings have been moved here :)' ) . '</p>';

		return array(
			$prefix . 'new_items' => array(
				'content' => $new_pointer_content,
				'anchor_id' => '.gift_cert_settings',
				'edge' => 'bottom',
				'align' => 'left right',
				'active' => ( ! in_array( $prefix . 'new_items', $dismissed ) )
			),
		);
	}
	
	public function panel_tabs() {

		?>
		<li class="general_options hide_if_grouped hide_if_external gift_cert_settings">
			<a href="#gift_certificates">
				<?php _e( 'Gift Certificates', 'ignitewoo_deposits_pro' ); ?>
			</a>
		</li>
		<?php
	}
	
	function write_panels() { 
		global $post, $wpdb;

		$saved_post = $post;

		require_once( dirname( __FILE__ ) . '/class-ign-gc-metabox.php' );
		
		$gift_cert_enabled = get_post_meta( $post->ID, 'ignite_gift_enabled', true );
		
		$sold_as_voucher = get_post_meta( $post->ID, 'ignite_gc_sold_as_voucher', true );

		$delete_coupon_after_use = get_post_meta( $post->ID, 'ignite_delete_coupon', true );

		$delete_gift_cert_after_use = get_post_meta( $post->ID, 'ignite_delete_gift_cert', true );

		$restrict_to_buyer = get_post_meta( $post->ID, 'ignite_restrict_to_buyer', true );
		
		$set_your_price = get_post_meta( $post->ID, 'ignite_buyer_sets_price', true );
		
		$ign_include_tax = get_post_meta( $post->ID, 'ignite_include_tax', true );
		
		$min_price = get_post_meta( $post->ID, 'ignite_min_price', true );
		
		$sug_price = get_post_meta( $post->ID, 'ignite_suggested_price', true );
		
		$prefix = get_post_meta( $post->ID, '_coupon_prefix', true );
		
		$exp_days = get_post_meta( $post->ID, '_expiration_days', true );
		
		$display_included_vouchers = get_post_meta( $post->ID, 'display_included_vouchers', true);
		
		//$users_select_vouchers = get_post_meta( $post->ID, 'users_select_vouchers', 'no' );
		$voucher_styles = get_post_meta( $post->ID, 'voucher_styles', true );

		if ( '' != $prefix )
			$prefix = trim( $prefix );
		else
			$prefix = '';

		if ( $gift_cert_enabled ) 
			$checked = 'checked="checked"';
		else
			$checked = '';
		?>
		
		<style>
		#gift_certificates .help_tip { width: 18px; width:18px; }
		#gift_certificates .description { clear: none; display: inline-block; }
		#edit-slug-box { display:block !important; }
		#minor-publishing-actions { display: block !important; }
		</style>


		<div id="gift_certificates" class="panel woocommerce_options_panel">

			<p><?php _e( 'You can add a gift certificate or coupon that is automatically generated and given the buyer when this product is purchased. To do that edit the fields below', 'ignitewoo_gift_certs' ); ?></p>

			<p class="form-field">
				<label for="gc_enabled"><?php _e( 'Gift Certs / Coupons', 'ignitewoo_gift_certs' ) ?></label>
				<input type="checkbox" class="checkbox" name="ignite_enable_gift_cert" value="1" <?php echo $checked ?>>  
				<span class="description">
		 			<?php _e( 'Enable', 'ignitewoo_gift_certs' ) ?>
					<img class="help_tip" data-tip="<?php _e('Enables gift certificates / coupon codes to be given to buyers of this product. NOTE: Disabling this does not delete or disable codes that have already been issued!', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />

				</span>
			</p>
			
			<?php 
			/*
			This setting controls how the plugin determines the voucher/coupon amount. When disabled vouchers/coupons are issued at the amount of the product price or user provided price if Adjustable Amount is enabled. When enabled vouchers/coupons are issued at the amount of the Coupon amount.
			*/
							
			if ( $sold_as_voucher ) 
				$checked = 'checked="checked"';
			else
				$checked = '';
			?>
			<p class="form-field">
				<label for="gc_enabled"><?php _e( 'Ignore product price', 'ignitewoo_gift_certs' ) ?></label>
				<input type="checkbox" class="checkbox" name="ignite_gc_sold_as_voucher" value="1" <?php echo $checked ?>>  
				<span class="description">
		 			<?php _e( 'Enable', 'ignitewoo_gift_certs' ) ?>
					<img class="help_tip" data-tip='<?php _e('Enable this to issue the voucher/coupon in the amount set in your "Discount Amount" as defined below. Otherwise the voucher/coupon will have an amount equal to your product cost.<br/><br/>Enabling this settings allows you to sell a product at your specified price while issuing a voucher or discount coupon at a fixed amount as defined in the "Coupon amount" setting below.<br/><br/><em><strong>This setting only takes effect when you choose "Gift Certificate/Store Credit" as the discount type below. Also, this setting has no effect if you enable "Adjustable price" below.</em></strong>
					', 'ignitewoo_gift_certs' ) ?>' src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />

				</span>
			</p>
			
			<p class="form-field">
				<label for="gc_enabled"><?php _e( 'Prefix', 'ignitewoo_gift_certs' ) ?></label>
				<input type="text" name="_coupon_prefix" value="<?php echo $prefix ?>" style="width:45px">  
				<span class="description">
					<img class="help_tip" data-tip="<?php _e('Enter a brief prefix to use when generating new gift certificate / coupon codes to help you identify codes associated with this product when viewing the Coupons interface. Try to keep it short, 3 numbers and/or letters or less. Special characters are not allowed. The default is CC', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />

				</span>
			</p>
			
			<?php
			if ( $delete_coupon_after_use ) 
				$checked = 'checked="checked"';
			else
				$checked = '';
			?>

				<p class="form-field">
					<label for="gc_enabled"><?php _e( 'Delete after one use', 'ignitewoo_gift_certs' ) ?></label>
					<input type="checkbox" class="checkbox" name="woocommerce_delete_coupon_code_after_usage" value="1" <?php echo $checked ?>> 
					<span class="description">
						<?php _e( 'Enable', 'ignitewoo_gift_certs' ) ?> 
						<img class="help_tip" data-tip="<?php _e( 'Delete coupon code after one use. Otherwise codes can be resused until deleted manually or any defined expiration date is reached', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />
					</span>
				</p>

				<?php
				if ( $delete_gift_cert_after_use ) 
					$checked = 'checked="checked"';
				else
					$checked = '';
				?>
		
				<p class="form-field">
					<label for="gc_enabled"><?php _e( 'Delete when zero balance', 'ignitewoo_gift_certs' ) ?></label>
					<input type="checkbox" class="checkbox" name="woocommerce_delete_gift_credit_after_usage" value="1" <?php echo $checked ?>> <?php _e( 'Enable', 'ignitewoo_gift_certs' ) ?>  
					<span class="description">
						<img class="help_tip" data-tip="<?php _e('Note that if an order was purchased using a gift certificate / stored credit, and that order is refunded or cancelled then any gift certificates / store credit amout used for the purchase will credited back to the related voucher code and those codes will be marked as published again and available for use by the customer even if those gift certificate / store credit codes were set to draft status or moved to the trash!', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />
					</span>
				</p>
			
				<?php
				if ( $restrict_to_buyer ) 
					$checked = 'checked="checked"';
				else
					$checked = '';
				?>
				
				<p class="form-field">
					<label for="gc_enabled"><?php _e( 'Restrict use to purchaser', 'ignitewoo_gift_certs' ) ?></label>
					<input type="checkbox" class="checkbox" name="woocommerce_restrict_to_buyer" value="1" <?php echo $checked ?>> <?php _e( 'Enable', 'ignitewoo_gift_certs' ) ?> 
					<span class="description">
						
						<img class="help_tip" data-tip="<?php _e('Restrictions are based on the billing email address. When this feature is enabled, the user of the gift certificate must be logged in with the user account of the person who purchased it.', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />
					</span>
				</p>
		
				<p class="form-field">
					<label for="gc_expiry"><?php _e( 'Expire codes after', 'ignitewoo_gift_certs' ) ?></label>
					<input type="text" name="_expiration_days" value="<?php echo $exp_days ?>" style="width:50px"> &nbsp;<?php _e( 'days', 'ignitewoo_gift_certs' ) ?>
					<span class="description">
						<img class="help_tip" data-tip="<?php _e('The number of days after purchase that the gift cert / store credit or coupon expires. Leave this blank to set the date manually below, or to never expire. ', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />

					</span>
				</p>
			
				<?php
				if ( !empty( $display_included_vouchers ) && 'yes' == $display_included_vouchers )
					$checked = 'checked="checked"';
				else if ( empty( $display_included_vouchers ) )
					$checked = 'checked="checked"';
				else
					$checked = '';
				?>

				<p class="form-field">
					<label for="gc_enabled"><?php _e( 'Display in product page', 'ignitewoo_gift_certs' ) ?></label>
					<input type="checkbox" class="checkbox" name="woocommerce_display_included_vouchers" value="yes" <?php echo $checked ?>> <?php _e( 'Enable', 'ignitewoo_gift_certs' ) ?> 
					<span class="description">
						<img class="help_tip" data-tip="'<?php _e('You can display a note on the product page that informs the shopper what kind of voucher is included with the purchase of the product. Turn this off if this product is a standalone gift certificate / store credit and the product type is variable', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />
					</span>
				</p>

				<?php 
					$sql = 'select ID, post_title from ' . $wpdb->posts . ' where post_type="ign_voucher" and post_status="private"';
					$vouchers = $wpdb->get_results( $sql );
				?>
				
				<p class="form-field">
					<label for="gc_enabled"><?php _e( 'Voucher style', 'ignitewoo_gift_certs' ) ?></label>
			
					<?php 
					if ( empty( $vouchers ) ) {
					
						?> <strong><em> <?php _e( 'You have not created any voucher templates yet. Voucher will be sent as regular email', 'ignitewoo_gift_certs' ) ?></em></strong> <?php
						
					} else { 
						?>
						
						<select id="woocommerce_voucher_styles" name="woocommerce_voucher_styles[]" multiple="multiple" style="width:200px !important">
						
							<option value=""> </option>
							
							<?php
							foreach( $vouchers as $v ) { 
							
								$selected = in_array( $v->ID, (array)$voucher_styles );
								
								if ( $selected )
									$selected = 'selected="selected"';
								else 
									$selected = '';
							
								echo '<option value="'. $v->ID . '" ' . $selected . '> ' . esc_html( $v->post_title ) . ' </option>';
							}
							?>
							
						</select>

						<span class="description">
							<img class="help_tip" data-tip="<?php _e('Optionally select voucher styles to use for PDF vouchers when this product is purchased. If you leave this empty then vouchers will be sent as regular email. If you select more than one then shoppers will be allowed to choose which one they prefer', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />
						</span>
						
					<?php
					}
					?>
				</p>
				
				<?php
				if ( 'yes' == get_post_meta( $post->ID, 'use_custom_gc_codes', true ) )
					$checked = 'checked="checked"';
				else
					$checked = '';
				?>
				
		
				<p class="form-field">
					<label for="gc_enabled"><?php _e( 'Use your own codes', 'ignitewoo_gift_certs' ) ?></label>
					<input type="checkbox" class="checkbox" id="use_custom_gc_codes" name="woocommerce_use_custom_gc_codes" value="yes" <?php echo $checked ?>> <?php _e( 'Enable', 'ignitewoo_gift_certs' ) ?>
					<span class="description">
						<img class="help_tip" data-tip="<?php _e('If you enable this option then new vouchers will use your custom codes. When no codes are available a unique code will be automatically generated', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />
					</span>
				</p>
			
				<?php
				if ( 'yes' == get_post_meta( $post->ID, 'use_custom_gc_codes', true ) )
					$display = 'display:block';
				else
					$display = 'display:none';
				
				
				$codes = get_post_meta( $post->ID, 'custom_gc_codes', true );
				?>
			
				<p class="form-field" id="custom_gc_codes_wrap" style="<?php $display ?>">
					<label for="gc_enabled"><?php _e( 'Your codes', 'ignitewoo_gift_certs' ) ?></label>
					<textarea id="custom_gc_codes" name="woocommerce_custom_gc_codes" style="max-width:200px;min-height:80px"><?php echo $codes ?> </textarea>  
					<span class="description">
						<img class="help_tip" data-tip="<?php _e('Paste in a list of codes, or leave it blank to use the global list of codes', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />
					</span>
					
				</p>

				<?php
				if ( $set_your_price ) 
					$checked = 'checked="checked"';
				else
					$checked = '';
				?>

				<p class="form-field">
					<label for="gc_enabled"><?php _e( 'Adjustable price', 'ignitewoo_gift_certs' ) ?></label>
					<input type="checkbox" class="checkbox" id="enable_set_your_price" name="woocommerce_buyer_sets_price" value="1" <?php echo $checked ?>> <?php _e( 'Enable', 'ignitewoo_gift_certs' ) ?>
					<span class="description">
						<img class="help_tip" data-tip="<?php _e('Allow users to set their own gift certificate price. If you enable this option then the Coupon Amount below becomes irrelevant. The amount will be automatically set to the amount the shopper enters when adding the item to the cart. THIS FEATURE ONLY APPLIES WHEN YOU SET THE DISCOUNT TYPE TO GIFT CERTIFICATE / STORE CREDIT, AND ONLY WHEN THE PRODUCT TYPE IS SIMPLE ', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />
					</span>
				</p>

				<div class="set_your_price" style="display:none">
					<p class="form-field">
						<label for="gc_enabled" style="font-weight:bold">&nbsp;</label>
						<input type="text" placeholder="" value="<?php echo $sug_price ?>" id="_suggested_price" name="ignite_suggested_price" class="wc_input_price short">
						<span class="description">&nbsp;
							<?php echo __( 'Suggested Amount', 'ignitewoo_gift_certs') . ' (' . get_woocommerce_currency_symbol() . ')' ?>
							<img class="help_tip" data-tip="<?php _e('If you enable this option then the Coupon Amount below becomes irrelevant. The amount will be automatically set to the amount the shopper enters when adding the item to the cart. THIS FEATURE ONLY APPLIES WHEN YOU SET THE DISCOUNT TYPE TO GIFT CERTIFICATE / STORE CREDIT, AND ONLY WHEN THE PRODUCT TYPE IS SIMPLE ', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />
						</span>
					</p>

					<p class="form-field">
						<label for="gc_enabled" style="font-weight:bold">&nbsp;</label>
						<input type="text" placeholder="" value="<?php echo $min_price ?>" id="_min_price" name="ignite_min_price" class="wc_input_price short">
						<span class="description">&nbsp;
							<?php echo __( 'Minimum Amount', 'ignitewoo_gift_certs') . ' (' . get_woocommerce_currency_symbol(). ')' ?>
							<img class="help_tip" data-tip="<?php _e('If you enable this option then the Coupon Amount below becomes irrelevant. The amount will be automatically set to the amount the shopper enters when adding the item to the cart. THIS FEATURE ONLY APPLIES WHEN YOU SET THE DISCOUNT TYPE TO GIFT CERTIFICATE / STORE CREDIT, AND ONLY WHEN THE PRODUCT TYPE IS SIMPLE ', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />
						</span>
					</p>
					<p class="form-field">
				
						<label for="gc_enabled" style="font-weight:bold">&nbsp;</label>
						<input type="checkbox" placeholder="" value="yes" id="_min_price" name="ignite_include_tax" class="wc_input short" style="width:20px" <?php checked( 'yes', $ign_include_tax, true ) ?> >
						<span class="description">&nbsp;
							<?php echo __( 'Include Tax', 'ignitewoo_gift_certs') ?>
							<img class="help_tip" data-tip="<?php _e('When issuing the voucher where the shopper sets their own amount, include the tax paid when buying this item', 'ignitewoo_gift_certs' ) ?>" src="<?php echo WC()->plugin_url() . '/assets/images/help.png' ?>" />
						</span>
					</p>
					
					<script>
					jQuery( document ).ready( function($) { 
					
						$( "#enable_set_your_price" ).click( function() {
							if ( $( this ).is( ':checked' ) )
								$( '.set_your_price' ).show('fast');
							else 
								$( '.set_your_price' ).hide('fast');
						})

						if ( $( "#enable_set_your_price" ).is( ':checked' ) ) {
							$( '.set_your_price' ).show('fast');
						}
						
						$( "#use_custom_gc_codes" ).click( function() {
							if ( $( this ).is( ':checked' ) )
								$( '#custom_gc_codes_wrap' ).show('fast');
							else 
								$( '#custom_gc_codes_wrap' ).hide('fast');
						})
						
						$( '.coupon_description_field ' ).remove();
						
						<?php if ( $set_your_price ) { ?>
							if ( $( "#enable_set_your_price" ).is( ':checked' ) )
								$( '.set_your_price' ).show('fast');

						<?php } ?>

						<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '>=' ) ) { ?>
						
							$( '#woocommerce_voucher_styles' ).select2();
						
						<?php } else { ?>
						
							$( '#woocommerce_voucher_styles' ).chosen();
						
						<?php } ?>
						
						
					})

					</script>
				</div>
				
			<div style="background-color:#fafafa;">
			
				<p style="font-weight: bold; padding: 9px 9x 4px 9px; line-height:16px">
					<?php _e( 'Below you must set the default parameters for each gift certificate or coupon code that will be generated when this product is purchased', 'ignitewoo_gift_certs' ); ?>
					
					<span style="font-style:italic;"><?php _e( 'Changing the settings below will not change the settings of any gift certificates / coupon codes that have already been issued.', 'ignitewoo_gift_certs' ); ?></span>
					
					
					<span style="font-style:italic; font-weight:normal"><?php _e( 'For gift certificate be sure to set the Discount Type to Gift Certificate / Store Credit', 'ignitewoo_gift_certs' ); ?></span>
				</p>
			</div>
			
			<?php IGN_Meta_Box_Coupon_Data::output( $post ); ?>

		</div>
		
		<?php 

		$post = $saved_post;
	}
	

	// Save metabox data
	function process_product_meta( $post_id ) {
		global $post; 

		if ( ! $_POST || empty( $post_id ) || empty( $post ) || is_int( wp_is_post_revision( $post_id ) ) || is_int( wp_is_post_autosave( $post_id ) ) ) return $post_id;
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;
		
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
		
		if ( $post->post_type != 'product' ) return $post_id;
		
		if ( isset( $_POST['ignite_enable_gift_cert'] ) && 1 == $_POST['ignite_enable_gift_cert'] ) 
			update_post_meta( $post_id, 'ignite_gift_enabled', 1 );
		else
			delete_post_meta( $post_id, 'ignite_gift_enabled' );

		if ( isset( $_POST['ignite_gc_sold_as_voucher'] ) && 1 == $_POST['ignite_gc_sold_as_voucher'] ) 
			update_post_meta( $post_id, 'ignite_gc_sold_as_voucher', 1 );
		else
			delete_post_meta( $post_id, 'ignite_gc_sold_as_voucher' );

		if ( isset( $_POST['_coupon_prefix'] ) && '' != $_POST['_coupon_prefix'] )
			$prefix =preg_replace('/[^0-9a-zA-Z_]/', "", $_POST['_coupon_prefix'] );

		if ( $prefix && '' != $prefix )
			update_post_meta( $post_id, '_coupon_prefix', $prefix );
		else
			update_post_meta( $post_id, '_coupon_prefix', 'CC' );

		if ( isset( $_POST['_expiration_days'] ) && !empty( $_POST['_expiration_days'] ) ) 
			update_post_meta( $post_id, '_expiration_days',  absint( $_POST['_expiration_days'] ) );
		else
			delete_post_meta( $post_id, '_expiration_days' );

			
		if ( isset( $_POST['woocommerce_delete_coupon_code_after_usage'] ) && 1 == $_POST['woocommerce_delete_coupon_code_after_usage'] ) 
			update_post_meta( $post_id, 'ignite_delete_coupon', 1 );
		else
			delete_post_meta( $post_id, 'ignite_delete_coupon' );


		if ( isset( $_POST['woocommerce_delete_gift_credit_after_usage'] ) && 1 == $_POST['woocommerce_delete_gift_credit_after_usage'] ) 
			update_post_meta( $post_id, 'ignite_delete_gift_cert', 1 );
		else
			delete_post_meta( $post_id, 'ignite_delete_gift_cert' );

		if ( isset( $_POST['woocommerce_restrict_to_buyer'] ) && 1 == $_POST['woocommerce_restrict_to_buyer'] ) 
			update_post_meta( $post_id, 'ignite_restrict_to_buyer', 1 );
		else
			delete_post_meta( $post_id, 'ignite_restrict_to_buyer' );

		if ( 'variable' == $_POST['product-type'] ) 
			delete_post_meta( $post_id, 'ignite_buyer_sets_price' );
		else if ( isset( $_POST['woocommerce_buyer_sets_price'] ) && 1 == $_POST['woocommerce_buyer_sets_price'] ) 
			update_post_meta( $post_id, 'ignite_buyer_sets_price', 1 );
		else
			delete_post_meta( $post_id, 'ignite_buyer_sets_price' );
		
				
		if ( isset( $_POST['woocommerce_use_custom_gc_codes'] ) && 'yes' == $_POST['woocommerce_use_custom_gc_codes'] ) 
			update_post_meta( $post_id, 'use_custom_gc_codes', 'yes' );
		else
			update_post_meta( $post_id, 'use_custom_gc_codes', 'no' );
		
		if ( isset( $_POST['woocommerce_custom_gc_codes'] ) && !empty( $_POST['woocommerce_custom_gc_codes'] ) )
			update_post_meta( $post_id, 'custom_gc_codes', trim( $_POST['woocommerce_custom_gc_codes'] ) );
		else
			update_post_meta( $post_id, 'custom_gc_codes', '' );
		
			
		if ( isset( $_POST['woocommerce_display_included_vouchers'] ) && 'yes' == $_POST['woocommerce_display_included_vouchers'] ) 
			update_post_meta( $post_id, 'display_included_vouchers', 'yes' );
		else
			update_post_meta( $post_id, 'display_included_vouchers', 'no' );

		/*
		if ( isset( $_POST['woocommerce_users_select_vouchers'] ) && 'yes' == $_POST['woocommerce_users_select_vouchers'] ) 
			update_post_meta( $post_id, 'users_select_vouchers', 'yes' );
		else
			update_post_meta( $post_id, 'users_select_vouchers', 'no' );
		*/	

		if ( isset( $_POST['woocommerce_voucher_styles'] ) ) 
			update_post_meta( $post_id, 'voucher_styles', $_POST['woocommerce_voucher_styles'] );
		else
			update_post_meta( $post_id, 'voucher_styles', array() );
			
		$num_decimals = ( int ) get_option( 'woocommerce_price_num_decimals' );

		if ( !empty( $_POST['ignite_suggested_price'] ) ) {
		
			$suggested = round( abs ( floatval( $_POST['ignite_suggested_price'] ) ), $num_decimals ) ;
			
			update_post_meta( $post_id, 'ignite_suggested_price', $suggested );
			
		} else {
		
			delete_post_meta( $post_id, 'ignite_suggested_price' );

		}

		if ( !empty( $_POST['ignite_min_price'] ) ) {
		
			$min = round( abs ( floatval( $_POST['ignite_min_price'] ) ), $num_decimals ) ;

			$min = ( isset( $suggested ) && $min > $suggested ) ? '' : $min;

			update_post_meta( $post_id, 'ignite_min_price', $min );
			
		} else {
		
			delete_post_meta( $post_id, 'ignite_min_price' );
			
		}
			
		if ( !empty( $_POST['ignite_include_tax'] ) ) {

			update_post_meta( $post_id, 'ignite_include_tax', 'yes' );
			
		} else {
		
			delete_post_meta( $post_id, 'ignite_include_tax' );
			
		}
		
		require_once( dirname( __FILE__ ) . '/class-ign-gc-metabox.php' );
		
		IGN_Meta_Box_Coupon_Data::save( $post_id );
		
	}
		
}

$ign_gc_product_settings = new Ignite_Gift_Certs_Product_Settings();
