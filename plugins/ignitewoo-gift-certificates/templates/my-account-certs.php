<h2><?php _e( 'My Vouchers', 'ignitewoo_gift_certs' ) ?></h2>

<table class="shop_table my_account_vouchers">

	<thead>
		<tr>
			<th class="voucher-number"><span class="nobr"><?php _e( 'Code', 'ignitewoo_gift_certs' ); ?></span></th>
			<th class="voucher-date"><span class="nobr"><?php _e( 'Type Info', 'ignitewoo_gift_certs' ); ?></span></th>
			<th class="voucher-status"><span class="nobr"><?php _e( 'Current Value', 'ignitewoo_gift_certs' ); ?></span></th>
		</tr>
	</thead>

	<tbody><?php
		if ( empty( $customer_vouchers ) ) { 
		
			_e( 'You have do not have any gift certificates / store credits', 'ignitewoo_gift_certs' );
		
		} else 
		foreach ( $customer_vouchers as $voucher ) {
			
			$meta = get_post_meta( $voucher->ID );
			
			$type = $meta['discount_type'][0];
			
			$amount = $meta['coupon_amount'][0];

			if ( 'ign_store_credit' == $type )
				$type_title = __( 'Gift Certificate / Store Credit', 'ignitewoo_gift_certs' );

			else if ( 'percent_product' == $type )
				$type_title = __( 'Product Percent Discount', 'ignitewoo_gift_certs' );

			else if ( 'fixed_product' == $type )
				$type_title = __( 'Product Discount', 'ignitewoo_gift_certs' );
				
			else if ( 'percent' == $type )
				$type_title = __( 'Cart Discount Percent', 'ignitewoo_gift_certs' );

			else if ( 'fixed_cart' == $type )
				$type_title = __( 'Cart Discount', 'ignitewoo_gift_certs' );

			else
				$type_title = __( 'Unknown', 'ignitewoo_gift_certs' );
			
			$attrs = array();
			
			/** 
				This next set of data gathers the coupon settings and displays them to the user. 
				
				This code only support basic WooCommerce coupon functionality.
				
				If you have plugins that add additional functionality or features for coupons and you 
				want that data displayed you have to develop that code yourself. IgniteWoo does not 
				offer free support and assistance for customizations. Contact us if you'd like a price 
				quote for customizations. 
			*/
			
			// Product inclusions
			$pt = array();
			if ( !empty( $meta['product_ids'][0] ) ) { 
			
				$ids = explode( ',' , $meta['product_ids'][0] );
	
				foreach( $ids as $id ) { 
			
					$p = get_post( $id );

					if ( empty( $p->post_parent ) )
						$t = get_the_title( $id );
					// Variation? Get the main product title
					else 
						$t = get_the_title( $p->post_parent );
					
					if ( !is_wp_error( $t ) )
						$pt[] = $t;
				}
				
				if ( !empty( $pt ) )
					$attrs[ __( 'Products' , 'ignitewoo_gift_certs' ) ] = implode( ', ', $pt );
			
			}
			
			// Product category inclusions
			$pt = array();
			if ( !empty( $meta['product_categories'][0] ) ) { 
			
				$ids = maybe_unserialize( $meta['product_categories'][0] );
				
				foreach( $ids as $id ) { 
				
					$term = get_term( $id, 'product_cat' );
					
					if ( !is_wp_error( $term ) )
						$pt[] = $term->name;
					
				}
				
				if ( !empty( $pt ) )
					$attrs[ __( 'Products Categories' , 'ignitewoo_gift_certs' ) ] = implode( ', ', $pt );
			
			}
			
			// Product exclusions
			$pt = array();
			if ( !empty( $meta['exclude_product_ids'][0] ) ) { 
			
				$ids = explode( ',' , $meta['exclude_product_ids'][0] );
	
				foreach( $ids as $id ) { 
			
					$p = get_post( $id );

					if ( empty( $p->post_parent ) )
						$t = get_the_title( $id );
					// Variation? Get the main product title
					else 
						$t = get_the_title( $p->post_parent );
					
					if ( !is_wp_error( $t ) )
						$pt[] = $t;
				}
				
				if ( !empty( $pt ) )
					$attrs[ __( 'Products Excluded' , 'ignitewoo_gift_certs' ) ] = implode( ', ', $pt );
			
			}
			
			// Product category exclusions
			$pt = array();
			if ( !empty( $meta['exclude_product_categories'][0] ) ) { 
			
				$ids = maybe_unserialize( $meta['exclude_product_categories'][0] );
				
				foreach( $ids as $id ) { 
				
					$term = get_term( $id, 'product_cat' );
					
					if ( !is_wp_error( $term ) )
						$pt[] = $term->name;
					
				}
				
				if ( !empty( $pt ) )
					$attrs[ __( 'Products Categories Excluded' , 'ignitewoo_gift_certs' ) ] = implode( ', ', $pt );
			
			}
			
			// Email restriction? 
			if ( !empty( $meta['customer_email'][0] ) ) { 
			
				$emails = maybe_unserialize( $meta['customer_email'][0] );
				
				if ( !empty( $emails ) )
					$attrs[ __( 'Restricted to email address' , 'ignitewoo_gift_certs' ) ] = implode( ', ', $emails );
			
			
			}
			
			// Expiration date?
			if ( !empty( $meta['expiry_date'][0] ) ) { 

				$expires = date( 'M j, Y', $meta['expiry_date'][0] );
				
				$attrs[ __( 'Expires' , 'ignitewoo_gift_certs' ) ] = $meta['expiry_date'][0];
			
			
			}
			
			// Usage limit? 
			if ( !empty( $meta['usage_limit'][0] ) ) { 

				$attrs[ __( 'Use limit' , 'ignitewoo_gift_certs' ) ] = $meta['usage_limit'][0]; 
			
			
			}
			
			// Used previously?
			if ( !empty( $meta['usage_count'][0] ) ) { 

				$attrs[ __( 'Number of uses' , 'ignitewoo_gift_certs' ) ] = $meta['usage_count'][0]; 
			
			
			}
			
			// Applies before tax? ( Vouchers are valid for tax too )
			if ( 'ign_store_credit' != $type && 'yes' == $meta['apply_before_tax'][0] ) { 

				$attrs[ __( 'Applies before tax' , 'ignitewoo_gift_certs' ) ] = '';
			
			}
			
			// Free Shipping? ( Vouchers are valid for shipping too )
			if ( 'ign_store_credit' != $type && 'yes' == $meta['free_shipping'][0] ) { 

				$attrs[ __( 'Free Shipping' , 'ignitewoo_gift_certs' ) ] = __( 'Yes', 'ignitewoo_gift_certs' );
			
			} else if ( 'ign_store_credit' != $type ) { 
			
				$attrs[ __( 'Free Shipping' , 'ignitewoo_gift_certs' ) ] = __( 'No', 'ignitewoo_gift_certs' );
			
			}
			
			// Excluding Sale Items? 
			if ( empty( $meta['exclude_sale_items'][0] ) || 'no' == $meta['exclude_sale_items'][0] ) { 

				$attrs[ __( 'Valid for Sale Items' , 'ignitewoo_gift_certs' ) ] = __( 'Yes', 'ignitewoo_gift_certs' );
			
			} else { 
			
				$attrs[ __( 'Valid for Sale Items' , 'ignitewoo_gift_certs' ) ] = __( 'No', 'ignitewoo_gift_certs' );
			
			}
			
			?><tr class="voucher">
				<td class="voucher-number" style="vertical-align:top">
					<p><?php echo $voucher->post_title ?></p>
					
					<?php // NEW FOR V3.4 ?>
					<?php if ( !empty( $meta['voucher_file_path'][0] ) ) { ?>
						
						<?php 
							$url = get_bloginfo( 'url' );
							
							$url = add_query_arg( 
									array( 
										'voucher_file' => $voucher->post_title,
										'order' => sha1( $meta['associated_order_id'][0] ),
									) 
								);
						?>
						
						<a href="<?php echo $url ?>"><?php _e( 'Download Voucher', 'ignitewoo_gift_certs' )?></a>
					
					<?php } ?>
				</td>
				<td class="voucher-date">
					<p><?php echo $type_title ?></p>
					<?php 
						if ( !empty( $attrs ) )
						foreach( $attrs as $name => $data ) 
							echo '<p>' . $name . ': ' . $data . '</p>';
					
					?>
				</td>
				<td class="voucher-status" style="text-align:right; white-space:nowrap;vertical-align:top">
					<?php 
						if ( 'percent' == $type || 'percent_product' == $type ) 
							echo $amount . '%';
						else
							echo woocommerce_price( $amount );
					?>
				</td>
			</tr><?php
		}
	?></tbody>

</table>