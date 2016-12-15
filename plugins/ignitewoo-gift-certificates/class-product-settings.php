<?php

class Ignite_Gift_Certs_Product_Settings { 


	function __construct() { 

		// Display meta box on product edit page and handle storing input data
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ), 12 );

		add_action( 'save_post', array( &$this, 'process_product_meta' ) );
		
	}
	
	// Queue the addition of a metabox to the product editor
	function add_meta_box() { 

		add_meta_box( 'woocommerce-coupon-data', __('Gift Certificates / Store Credits / Coupon Codes', 'ignitewoo_gift_certs' ), array( &$this, 'product_options' ), 'product', 'normal', 'high' );

	}


	// Metabox display 
	function product_options() {
		global $post, $woocommerce;
		
		$saved_post = $post;

		require_once( dirname( __FILE__ ) . '/class-ign-gc-metabox.php' );
		
		$gift_cert_enabled = get_post_meta( $post->ID, 'ignite_gift_enabled', true );

		$delete_coupon_after_use = get_post_meta( $post->ID, 'ignite_delete_coupon', true );

		$delete_gift_cert_after_use = get_post_meta( $post->ID, 'ignite_delete_gift_cert', true );

		$restrict_to_buyer = get_post_meta( $post->ID, 'ignite_restrict_to_buyer', true );
		
		$set_your_price = get_post_meta( $post->ID, 'ignite_buyer_sets_price', true );
		
		$min_price = get_post_meta( $post->ID, 'ignite_min_price', true );
		
		$sug_price = get_post_meta( $post->ID, 'ignite_suggested_price', true );
		
		$prefix = get_post_meta( $post->ID, '_coupon_prefix', true );
		
		$exp_days = get_post_meta( $post->ID, '_expiration_days', true );
		
		$display_included_vouchers = get_post_meta( $post->ID, 'display_included_vouchers', 'yes' );

		if ( '' != $prefix )
			$prefix = trim( $prefix );
		else
			$prefix = '';

		if ( $gift_cert_enabled ) 
			$checked = 'checked="checked"';
		else
			$checked = '';

		// WooCom global
		global $thepostid;

		$thepostid = $post->ID;

		echo '<p style="margin-left:10px;">';

		_e( 'You can add a gift certificate or coupon that is automatically generated and given the buyer when this product is purchased. To do that edit the fields below', 'ignitewoo_gift_certs' );

		echo '</p>';

		echo '
			<div id="coupon_options" class="panel woocommerce_options_panel">
			<div class="options_group">
				<p class="form-field">
					<label for="gc_enabled">Settings</label>
					<input type="checkbox" class="checkbox" name="ignite_enable_gift_cert" value="1" ' . $checked . '>  
					<span class="description">
			' . 			__( 'Enable gift certificates / store credits / coupon codes with this product.', 'ignitewoo_gift_certs' ) . '
						<img class="help_tip" data-tip="'. __('Enables gift certificates / coupon codes to be given to buyers of this product. NOTE: Disabling this does not delete or disable codes that have already been issued!', 'ignitewoo_gift_certs' ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/tip.png" />

					</span>
				</p>
				<p class="form-field">
					<label for="gc_enabled" style="font-weight:bold">&nbsp;</label>
					<input type="text" name="_coupon_prefix" value="' . $prefix . '" style="width:45px">  
					<span class="description">
			' . 			__( 'Enter a gift certificate / coupon code prefix.', 'ignitewoo_gift_certs' ) . '
						<img class="help_tip" data-tip="'. __('Enter a brief prefix to use when generating new gift certificate / coupon codes to help you identify codes associated with this product when viewing the Coupons interface. Try to keep it short, 3 numbers and/or letters or less. Special characters are not allowed. The default is CC', 'ignitewoo_gift_certs' ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/tip.png" />

					</span>
				</p>
			';

			if ( $delete_coupon_after_use ) 
				$checked = 'checked="checked"';
			else
				$checked = '';

			echo '
				<p class="form-field">
					<label for="gc_enabled" style="font-weight:bold">&nbsp;</label>
					<input type="checkbox" class="checkbox" name="woocommerce_delete_coupon_code_after_usage" value="1" ' . $checked . '>  
					<span class="description">
			' . 			__( 'Delete coupon code after one use. Otherwise codes can be resused until deleted manually or any defined expiration date is reached', 'ignitewoo_gift_certs' ) . '
					</span>
				</p>
			';

			if ( $delete_gift_cert_after_use ) 
				$checked = 'checked="checked"';
			else
				$checked = '';

			echo '
				<p class="form-field">
					<label for="gc_enabled" style="font-weight:bold">&nbsp;</label>
					<input type="checkbox" class="checkbox" name="woocommerce_delete_gift_credit_after_usage" value="1" ' . $checked . '>  
					<span class="description">
			' . 			__( 'Delete gift certificate / store credit code when the balance reaches zero', 'ignitewoo_gift_certs' ) . '
						<img class="help_tip" data-tip="'. __('Note that if an order was purchased using a gift certificate / stored credit, and that order is refunded or cancelled then any gift certificates / store credit amout used for the purchase will credited back to the related voucher code and those codes will be marked as published again and available for use by the customer even if those gift certificate / store credit codes were set to draft status or moved to the trash!', 'ignitewoo_gift_certs' ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/tip.png" />
					</span>
				</p>
			';

			if ( $restrict_to_buyer ) 
				$checked = 'checked="checked"';
			else
				$checked = '';

			echo '
				<p class="form-field">
					<label for="gc_enabled" style="font-weight:bold">&nbsp;</label>
					<input type="checkbox" class="checkbox" name="woocommerce_restrict_to_buyer" value="1" ' . $checked . '>  
					<span class="description">
			' . 			__( 'Restrict use of this gift certificate to the user who purchases it', 'ignitewoo_gift_certs' ) . '
						<img class="help_tip" data-tip="'. __('Restrictions are based on the billing email address. When this feature is enabled, the user of the gift certificate must be logged in with the user account of the person who purchased it.', 'ignitewoo_gift_certs' ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/tip.png" />
					</span>
				</p>
			';

			echo '
			<p class="form-field">
					<label for="gc_expiry" style="font-weight:bold">&nbsp;</label>
					<input type="text" name="_expiration_days" value="' . $exp_days . '" style="width:50px">  
					<span class="description">
			' . 			__( 'Expire this many days after purchase.', 'ignitewoo_gift_certs' ) . '
						<img class="help_tip" data-tip="'. __('The number of days after purchase that the gift cert / store credit or coupon expires. Leave this blank to set the date manually below, or to never expire. ', 'ignitewoo_gift_certs' ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/tip.png" />

					</span>
				</p>
			';
				
			if ( !empty( $display_included_vouchers ) && 'yes' == $display_included_vouchers )
				$checked = 'checked="checked"';
			else if ( empty( $display_included_vouchers ) )
				$checked = 'checked="checked"';
			else
				$checked = '';

			echo '
				<p class="form-field">
					<label for="gc_enabled" style="font-weight:bold">&nbsp;</label>
					<input type="checkbox" class="checkbox" name="woocommerce_display_included_vouchers" value="yes" ' . $checked . '>  
					<span class="description">
			' . 			__( 'Display included vouchers in the product page', 'ignitewoo_gift_certs' ) . '
						<img class="help_tip" data-tip="'. __('You can display a note on the product page that informs the shopper what kind of voucher is included with the purchase of the product. Turn this off if this product is a standalone gift certificate / store credit and the product type is variable', 'ignitewoo_gift_certs' ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/tip.png" />
					</span>
				</p>
			';
			
			
			if ( $set_your_price ) 
				$checked = 'checked="checked"';
			else
				$checked = '';

			echo '
				<p class="form-field">
					<label for="gc_enabled" style="font-weight:bold">&nbsp;</label>
					<input type="checkbox" class="checkbox" id="enable_set_your_price" name="woocommerce_buyer_sets_price" value="1" ' . $checked . '>  
					<span class="description">
			' . 			__( 'Allow users to set their own gift certificate price', 'ignitewoo_gift_certs' ) . '
						<img class="help_tip" data-tip="'. __('If you enable this option then the Coupon Amount below becomes irrelevant. The amount will be automatically set to the amount the shopper enters when adding the item to the cart. THIS FEATURE ONLY APPLIES WHEN YOU SET THE DISCOUNT TYPE TO GIFT CERTIFICATE / STORE CREDIT, AND ONLY WHEN THE PRODUCT TYPE IS SIMPLE ', 'ignitewoo_gift_certs' ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/tip.png" />
					</span>
				</p>
			';

			echo '<div class="set_your_price" style="display:none">';

			echo '
				<p class="form-field">
					<label for="gc_enabled" style="font-weight:bold">&nbsp;</label>
					<input type="text" placeholder="" value="'. $sug_price . '" id="_suggested_price" name="ignite_suggested_price" class="wc_input_price short">
					<span class="description">
				' . __( 'Suggested Amount', 'ignitewoo_gift_certs') . ' (' . get_woocommerce_currency_symbol() . ')' . '
						<img class="help_tip" data-tip="'. __('If you enable this option then the Coupon Amount below becomes irrelevant. The amount will be automatically set to the amount the shopper enters when adding the item to the cart. THIS FEATURE ONLY APPLIES WHEN YOU SET THE DISCOUNT TYPE TO GIFT CERTIFICATE / STORE CREDIT, AND ONLY WHEN THE PRODUCT TYPE IS SIMPLE ', 'ignitewoo_gift_certs' ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/tip.png" />
					</span>
				</p>
			';

			echo '
				<p class="form-field">
					<label for="gc_enabled" style="font-weight:bold">&nbsp;</label>
					<input type="text" placeholder="" value="' . $min_price .'" id="_min_price" name="ignite_min_price" class="wc_input_price short">
					<span class="description">
				' . __( 'Minimum Amount', 'ignitewoo_gift_certs') . ' (' . get_woocommerce_currency_symbol(). ')' . '
						<img class="help_tip" data-tip="'. __('If you enable this option then the Coupon Amount below becomes irrelevant. The amount will be automatically set to the amount the shopper enters when adding the item to the cart. THIS FEATURE ONLY APPLIES WHEN YOU SET THE DISCOUNT TYPE TO GIFT CERTIFICATE / STORE CREDIT, AND ONLY WHEN THE PRODUCT TYPE IS SIMPLE ', 'ignitewoo_gift_certs' ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/tip.png" />
					</span>
				</p>
			';

			?>
				
			<script>
			jQuery( document ).ready( function() { 
				jQuery( "#enable_set_your_price" ).click( function() {
					if ( jQuery( this ).is( ':checked' ) )
						jQuery( '.set_your_price' ).show('fast');
					else 
						jQuery( '.set_your_price' ).hide('fast');
				})
				
				jQuery( '.coupon_description_field ' ).remove();
				
				<?php if ( $set_your_price ) { ?>
				if ( jQuery( "#enable_set_your_price" ).is( ':checked' ) )
					jQuery( '.set_your_price' ).show('fast');

				<?php } ?>
			})

			</script>
			
			<?php 
			
			echo '</div>';
			
			
		echo '</div></div>';

		echo '<p style="margin-left:10px">';

		_e( 'Set the default parameters for each gift certificate or coupon code that will be generated when this product is purchased', 'ignitewoo_gift_certs' );

		echo '</p>';

		echo '<p style="margin-left:10px; font-style:italic">';

		_e( 'NOTE: Changing the settings below will not change the settings of associated gift certificates / coupon codes that have already been issued!', 'ignitewoo_gift_certs' );

		echo '</p>';

		IGN_Meta_Box_Coupon_Data::output( $post );
		
		?>
		
		<style>
			#edit-slug-box { display:block; }
			#minor-publishing-actions { display: block; }
		</style>

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


		if ( isset( $_POST['_coupon_prefix'] ) && '' != $_POST['_coupon_prefix'] )
			$prefix = ereg_replace("[^A-Za-z0-9]", "", $_POST['_coupon_prefix'] );

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
		
		if ( isset( $_POST['woocommerce_display_included_vouchers'] ) && 'yes' == $_POST['woocommerce_display_included_vouchers'] ) 
			update_post_meta( $post_id, 'display_included_vouchers', 'yes' );
		else
			update_post_meta( $post_id, 'display_included_vouchers', 'no' );

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
			
		require_once( dirname( __FILE__ ) . '/class-ign-gc-metabox.php' );
		
		IGN_Meta_Box_Coupon_Data::save( $post_id );
		
	}
		
}

$ign_gc_product_settings = new Ignite_Gift_Certs_Product_Settings();
