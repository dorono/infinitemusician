<?php

if ( !defined( 'ABSPATH' ) ) 
	die;

class IgniteWoo_Event_Reports { 

	var $plugin_url;

	function __construct() {

		$this->plugin_url =  WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '' , plugin_basename( __FILE__ ) );

		add_filter( 'woocommerce_reports_charts', array( &$this, 'reports' ) );

		add_action( 'admin_head', array( &$this, 'admin_head' ) ) ;

	}


	function admin_head() { 
	
		if ( empty( $_GET['tab'] ) || 'gift_certs' != $_GET['tab'] )
			return;
			
		wp_register_script( 'ig_tablesorter', $this->plugin_url . '/assets/js/datatables/jquery.dataTables.min.js', array( 'jquery' ), '2.0' );

		wp_enqueue_script( 'ig_tablesorter' );

		wp_register_script( 'ig_tablesorter_clip', $this->plugin_url . '/assets/js/datatables/media/js/ZeroClipboard.js', array( 'jquery' ), '2.0' );

		wp_enqueue_script( 'ig_tablesorter_clip' );

		wp_register_script( 'ig_tablesorter_tools', $this->plugin_url . '/assets/js/datatables/media/js/TableTools.js', array( 'jquery' ), '2.0' );

		wp_enqueue_script( 'ig_tablesorter_tools' );

		wp_register_style( 'ig_tablesorter_css', $this->plugin_url . '/assets/js/datatables/media/css/jquery.dataTables.css' );

		wp_enqueue_style( 'ig_tablesorter_css' );

		wp_register_style( 'ig_tablesorter_tools_css', $this->plugin_url . '/assets/js/datatables/media/css/TableTools.css' );

		wp_enqueue_style( 'ig_tablesorter_tools_css' );

		?>

		<script type="text/javascript" charset="utf-8">

			jQuery( document ).ready( function () {

			    <?php if ( !empty( $_GET['page'] ) && 'woocommerce_reports' == $_GET['page'] ) { ?>

				var oTable = jQuery( '.tablesorter' ).dataTable();

				var oTableTools = new TableTools( oTable, {
					"sSwfPath": "<?php echo $this->plugin_url ?>/assets/js/datatables/media/swf/copy_csv_xls_pdf.swf",
					"buttons": [
						"copy",
						"csv",
						"xls",
						"pdf",
						{ "type": "print", "buttonText": "Print me!" }
					]
				} );
				
				jQuery( '.tablesorter' ).before( oTableTools.dom.container );
				
			    <?php } ?>
			} );
		</script>

		<style>
		    .woocommerce-reports table tr td{ padding-right: 10px; }
		    /* .tablesorter tr th { min-width: 150px; } */
		</style>

		<?php 
	}


	function reports( $reports ) { 
	
		$reports['gift_certs'] = array( 
			'title' 	=>  __( 'Gift Certs / Store Credit', 'ignitewoo_gift_certs' ),
			'charts' 	=> array(
						array(
							'title' => __('Gift Certificate / Store Credit Reporting', 'ignitewoo_gift_certs'),
							'description' => '',
							'hide_title' => true,
							'function' => 'ignitewoo_gc_details'
						),
					)
			);
		return $reports;

	}

}


function ignitewoo_gc_details() {
	global $wpdb, $woocommerce;

	/*
	$sql = ' 
	SELECT ID, post_title 
	FROM `' . $wpdb->posts . '` 
	left join `' . $wpdb->postmeta . '` m1 on ID = m1.post_id 
	WHERE 
	m1.meta_key = "_ignitewoo_event" and m1.meta_value = "yes" 
	ORDER BY post_title ASC
	';

	$posts = $wpdb->get_results( $sql );

	if ( !isset( $posts ) || '' == $posts ) { 
		_e( 'No certificates / vouchers have been created yet.', 'ignitewoo_gift_certs' );
		return;
	}


	$current_event = absint( $_POST['ignitewoo_gc_select'] );

	$event = array();

	$options = '';

	foreach( $posts as $p ) { 

		$options .= '<option ' . selected( $current_event, $p->ID ) . ' value="' . $p->ID . '">' . $p->post_title . '</option>';

	}
	*/

	// Set defaults
	if ( empty( $_POST ) ) { 

		$_POST['ignitewoo_gc_report_fields']['firstname'] = 1;
		$_POST['ignitewoo_gc_report_fields']['lastname'] = 1;
		$_POST['ignitewoo_gc_report_fields']['quantity'] = 1;
		$_POST['ignitewoo_gc_report_fields']['codes'] = 1;
		$_POST['ignitewoo_gc_report_fields']['data'] = 1;
		$_POST['ignitewoo_gc_report_fields']['cost'] = 1;
		$_POST['ignitewoo_gc_report_fields']['items'] = 1;
		$_POST['ignitewoo_gc_report_fields']['address'] = 1;
		$_POST['ignitewoo_gc_report_fields']['city'] = 1;
		$_POST['ignitewoo_gc_report_fields']['state'] = 1;
		$_POST['ignitewoo_gc_report_fields']['postalcode'] = 1;
		$_POST['ignitewoo_gc_report_fields']['country'] = 1;
		$_POST['ignitewoo_gc_report_fields']['phone'] = 1;
		$_POST['ignitewoo_gc_report_fields']['email'] = 1;
		$_POST['ignitewoo_gc_report_fields'][''] = 1;

		$_POST['ignitewoo_gc_report_fields']['status'] = array( 'completed', 'processing'  );

		if ( empty( $_POST['ignitewoo_gc_select'] ) )
			$key = '';
		else
			$key = $_POST['ignitewoo_gc_select'];
		
	}

	$statuses = apply_filters( 'woocommerce_reports_order_statuses', array( 'completed', 'processing'  ) );

	?>

	<div id="poststuff" class="ignitewoo-reports-wrap">
		<div class="woocommerce-reports">
			<h2><?php // _e( 'Generate reports of gift certificates / store credit issuance', 'ignitewoo_gift_certs' ) ?></h2>
			<div class="ignitewoo_postbox">
				<?php /* <h3><span><?php _e('Enter a code, email address, name, or leave blank for a full list', 'ignitewoo_gift_certs'); ?></span></h3> */ ?>
				<div class="inside">
					<form action="" method="post">
					<p class="stat">
					<?php /*
						<input style="width:300px" id="ignitewoo_gc_select" name="ignitewoo_gc_select" type="text">
					*/ ?>
						<input style="margin-left: 10px; position:relative; top: -8px" class="button-primary" type="submit" value=" <?php _e( 'Generate Report', 'ignitewoo_gift_certs' ) ?> ">
					</p>
<?php /*
					<p>
						<?php _e( 'Select report fields', 'ignitewoo_gift_certs' )?> <img class="help_tip" data-tip="<?php _e( 'Note that Cost reflects the total for items in the report. If you include Order Items in your report then Cost reflects the order total.', 'ignitewoo_gift_certs' )?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" />
					</p>

					<table><tr>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['firstname'], 1 )?> type="checkbox" name="ignitewoo_gc_report_fields[firstname]" value="1"> <?php _e( 'First name', 'ignitewoo_gift_certs' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['lastname'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[lastname]" value="1"> <?php _e( 'Last name', 'ignitewoo_gift_certs' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['quantity'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[quantity]" value="1"> <?php _e( 'Quantity', 'ignitewoo_gift_certs' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['codes'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[codes]" value="1"> <?php _e( 'Codes', 'ignitewoo_gift_certs' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['data'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[data]" value="1"> <?php _e( 'Extra Data', 'ignitewoo_gift_certs' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['cost'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[cost]" value="1"> <?php _e( 'Cost', 'ignitewoo_gift_certs' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['items'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[items]" value="1"> <?php _e( 'Order Items', 'ignitewoo_gift_certs' )?></label></td>
					</tr><tr>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['address'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[address]" value="1"> <?php _e( 'Address', 'ignitewoo_gift_certs' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['city'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[city]" value="1"> <?php _e( 'City', 'ignitewoo_gift_certs' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['state'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[state]" value="1"> <?php _e( 'State', 'ignitewoo_gift_certs' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['postalcode'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[postalcode]" value="1"> <?php _e( 'Postal code', 'ignitewoo_gift_certs' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['country'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[country]" value="1"> <?php _e( 'Country', 'ignitewoo_gift_certs' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['phone'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[phone]" value="1"> <?php _e( 'Phone', 'ignitewoo_gift_certs' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['email'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[email]" value="1"> <?php _e( 'Email', 'ignitewoo_gift_certs' )?></label></td>
					</tr><tr>
						<td colspan="7"><label><input class="report_field" <?php checked( $_POST['ignitewoo_gc_report_fields']['combine_address'], 1 )?>  type="checkbox" name="ignitewoo_gc_report_fields[combine_address]" value="1"> <?php _e( 'Combine all selected contact info into the Address field', 'ignitewoo_gift_certs' )?></label></td>
					</tr></table>

					<br/>

					<p><?php _e( 'Select the orders whose status matches the settings below:', 'ignitewoo_gift_certs' ) ?></p>

					<table></tr>
						<?php foreach( $statuses as $status ) { ?>
							<td><label><input class="report_field" <?php if ( in_array( $status, $_POST['ignitewoo_gc_report_fields']['status'] ) ) echo 'checked="checked"' ?>  type="checkbox" name="ignitewoo_gc_report_fields[status][]" value="<?php echo $status?>"> <?php _e( ucfirst( $status ), 'ignitewoo_gift_certs' )?></label></td>
						<?php } ?>
					</tr></table>
*/ ?>

					</form>
				</div>
			</div>
		</div>

		<?php //if ( !empty( $key ) && $attendees = ignitewoo_cd_data( $key ) ) { ?>
		<?php if ( $attendees = ignitewoo_get_gc_codes( $key ) ) { ?>
		<div class="woocommerce-reports-main">
			<div class="postbox">
				<h3><span><?php _e('Gift Cert Purchases', 'ignitewoo_gift_certs'); ?></span></h3>
				<p><?php _e( 'This report includes purchases that have a status of completed and processing', 'ignitewoo_gift_certs' ) ?></p>
				<div class="inside chart">
					<div id="placeholder" style="width:100%; overflow:hidden; height:568px; position:relative;"></div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	<?php
}

function ignitewoo_get_gc_codes( $key ) {
	global $wpdb;

	/*
	$max_sales = $max_totals = 0;

	$product_sales = $product_totals = $buyers = array();
	
	//$children_ids = array();

	//$children = (array) get_posts( 'post_parent=' . $event_id . '&fields=ids&post_status=any&numberposts=-1' );

	//$children_ids = $children_ids + $children;


	if ( !isset( $_POST['ignitewoo_gc_report_fields']['status'] ) || count( $_POST['ignitewoo_gc_report_fields']['status'] ) < 1 )
		$statuses = array( 'complete' );
	else 
		$statuses = $_POST['ignitewoo_gc_report_fields']['status'];

	foreach( $statuses as $s ) 
		$ss[] = "'" . $s . "'";

	$statuses = implode ( ',' , $ss );

	// Get codes
	$sql = "
		SELECT ID, post_date, meta.meta_value AS items, posts.post_date FROM {$wpdb->posts} AS posts
		
		LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
		LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
		LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
		LEFT JOIN {$wpdb->terms} AS term USING( term_id )

		WHERE 	meta.meta_key 		= '_order_items'
		AND 	posts.post_type 	= 'shop_order'
		AND 	posts.post_status 	= 'publish'
		AND 	tax.taxonomy		= 'shop_order_status'
		AND	term.slug IN (" . $statuses . ")
		AND	posts.post_date	> date_sub( NOW(), INTERVAL 1 YEAR )
		ORDER BY posts.post_date ASC
	";
	*/


	$sql = 'select ID, post_title, m1.meta_value as order_id, m2.meta_value as dtype, m3.meta_value as amount from ' . $wpdb->posts .
		' left join ' . $wpdb->postmeta . ' m1 on ID = m1.post_id
		 left join ' . $wpdb->postmeta . ' m2 on ID = m2.post_id
		 left join ' . $wpdb->postmeta . ' m3 on ID = m3.post_id
		where post_type = "shop_coupon"
		and post_status = "publish" 
		and m1.meta_key = "associated_order_id" and m1.meta_value != ""
		and m2.meta_key = "discount_type"
		and m3.meta_key = "coupon_amount" ';
	
	$order_items = $wpdb->get_results( $sql );


	if ( $order_items ) {

	?>
	<table class="tablesorter">
		<thead>
			<tr>
				<th><?php _e( 'Code', 'ignitewoo_gift_certs'); ?></th>
				<th><?php _e( 'Type', 'ignitewoo_gift_certs' ); ?></th>
				<th><?php _e( 'Current Amount', 'ignitewoo_gift_certs' ); ?></th>
				<th><?php _e( 'Order ID', 'ignitewoo_gift_certs'); ?></th>
				<th><?php _e( 'Buyer', 'ignitewoo_gift_certs'); ?></th>
				<th><?php _e( 'Buyer Email', 'ignitewoo_gift_certs'); ?></th>
				<?php /*<th><?php _e( 'Recipient', 'ignitewoo_gift_certs'); ?></th> */ ?>
			</tr>
		</thead>
		<tbody>
		<?php foreach( $order_items as $o ) { ?>

			<?php

			$order = new WC_Order( $o->order_id );

			$meta = get_post_meta( $o->order_id );

			$order_items = ( array )$order->get_items();

			/*
			if ( !isset( $meta['ign_receiver_message'][0] ) )
				$msg = '';
			else
				$msg = trim( $meta['ign_receiver_message'][0] );

			$msg_details = array(
					'voucher_from_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
					'voucher_from_email' => $order->billing_email,
					'voucher_to_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
					'voucher_to_email' => $order->billing_email,
					'voucher_message' => $msg
			);

			if ( isset( $meta['ign_receiver_email'][0] ) && is_email( $meta['ign_receiver_email'][0] ) ) {

				$msg_details['voucher_to_name'] = $meta['ign_receiver_name'][0];

				$msg_details['voucher_to_email'] = $meta['ign_receiver_email'][0];

			}
			*/
			
			$amount = $o->amount;
			
			switch( $o->dtype ) {
					case 'ign_store_credit': $type = __('Gift Cert / Store Credit', 'ignitewoo_gift_certs'); break;
					case 'percent_product': $type = __('Percent off product', 'ignitewoo_gift_certs'); $amount .= $o->amount . '%'; break;
					case 'percent': $type = __('Percent off cart', 'ignitewoo_gift_certs'); $amount .= $o->amount . '%'; break;
					case 'fixed_cart': $type = __('Amount off cart', 'ignitewoo_gift_certs'); break;
					case 'fixed_product': $type = __('Amount off product', 'ignitewoo_gift_certs'); break;
					default: $type = 'Unknown'; break;
			}

			if ( $order ) { 
				$order_date = $order->order_date;
				$order_date = '<br>(' . date( 'Y-m-d', strtotime( $order_date ) ) . ')';
			} else
				$order_date = '';
				
			?>
			<tr>
				<td><?php echo $o->post_title ?></td>
				<td><?php echo $type ?></td>
				<td><?php echo $o->amount ?></td>
				<td><a href="<?php echo admin_url( 'post.php?post=' . $o->order_id. '&action=edit' ) ?>"><?php echo $o->order_id ?></a> <?php echo $order_date ?></td>
				<td><?php echo $order->billing_first_name . ' ' . $order->billing_last_name ?></td>
				<td><?php echo $order->billing_email ?></td>
				<?php /* <td><?php echo $msg_details['voucher_to_name'] . ', ' . $msg_details['voucher_to_email'] ?></td> */ ?>
			</tr>
			
		<?php } ?>
		</tbody>
	</table>

	<?php

	}

	
	/*
	if ( $order_items ) {
	
		foreach ( $order_items as $order_item ) {

			$date 	= date( 'Ym', strtotime( $order_item->post_date ) );

			$items 	= maybe_unserialize( $order_item->items );

			foreach ( $items as $item ) {

				if ( 'yes' != get_post_meta( $item['id'], '_ignitewoo_event', true ) )
					continue;

				if ( $item['id'] != $event_id  & ! in_array( $item['id'], $children_ids ) ) 
					continue;

				if ( 1 == $_POST['ignitewoo_gc_report_fields']['items'] ) {
					$all_items = $items;
				}

				$info = get_post_custom( $order_item->ID, false );

				if ( isset( $item['line_total'] ) ) 
					$row_cost = $item['line_total'];
				else 
					$row_cost = $item['cost'] * $item['qty'];
				
				// if ( ! $row_cost ) continue;
					
				// $product_sales[ $date ] = isset( $product_sales[ $date ] ) ? $product_sales[$date] + $item['qty'] : $item['qty'];
				// $product_totals[ $date ] = isset( $product_totals[ $date ] ) ? $product_totals[ $date ] + $row_cost : $row_cost;
				
				// if ( $product_sales[ $date ] > $max_sales ) $max_sales = $product_sales[ $date ];
				// if ( $product_totals[ $date ] > $max_totals ) $max_totals = $product_totals[ $date ];

				$buyers[] = array( 
					    'name' => $info['_billing_last_name'][0] . ' ' . $info['_billing_first_name'][0],
					    'first_name' =>  $info['_billing_first_name'][0],
					    'last_name' => $info['_billing_last_name'][0],
					    'billing_address' => $info['_billing_address_1'][0] . $info['_billing_address_2'][0],
					    'billing_city' => $info['_billing_city'][0],
					    'billing_state' => $info['_billing_state'][0],
					    'billing_country' => $info['_billing_country'][0],
					    'billing_postalcode' => $info['_billing_postcode'][0],
					    'email' => $info['_billing_email'][0],
					    'phone' => $info['_billing_phone'][0],
					    'qty' => $item['qty'],
					    'total' => $row_cost,
					    'item_meta' => $item['item_meta'],
					    'prefix' => $order_item->ID . '-' . $item['id'] . '-',  // ticket numbers prefix
					    'items' => $all_items,
					    'order_total' => get_post_meta( $order_item->ID, '_order_total', true )
					    );
				
			}

		}
		
	}

	if ( !$buyers ) { 

		_e( 'No buyers for this event', 'ignitewoo_gift_certs' );

		return false;

	}

	?>

	<h2><?php echo get_the_title( $event_id ) ?></h2>

	<p>
		<?php 
			$ss = array();
			_e( 'Statuses:', 'ignitewoo_gift_certs' );
			$statuses = explode( ',' , $statuses );
			echo ' '; 
			foreach( $statuses as $s ) 
				$ss[] = ucfirst( str_replace( "'", "", $s ) );
			$ss = implode( ', ' , $ss );
			echo $ss;
		?>
	</p>

	<table class="tablesorter">
		<thead>
			<tr>
				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['firstname'] ) { ?>
					<th><?php _e( 'First Name', 'ignitewoo_gift_certs'); ?></th>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['lastname'] ) { ?>
					<th><?php _e( 'Last Name', 'ignitewoo_gift_certs'); ?></th>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['address'] ) { ?>
					<th><?php _e( 'Address', 'ignitewoo_gift_certs'); ?></th>
				<?php } ?>


				<?php if ( 1 != $_POST['ignitewoo_gc_report_fields']['combine_address'] ) { ?>

					<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['city'] ) { ?>
						<th><?php _e( 'City', 'ignitewoo_gift_certs'); ?></th>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['state'] ) { ?>
						<th><?php _e( 'State', 'ignitewoo_gift_certs'); ?></th>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['country'] ) { ?>
						<th><?php _e( 'Country', 'ignitewoo_gift_certs'); ?></th>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['postalcode'] ) { ?>
						<th><?php _e( 'Postal Code', 'ignitewoo_gift_certs'); ?></th>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['email'] ) { ?>
						<th><?php _e( 'Email', 'ignitewoo_gift_certs'); ?></th>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['phone'] ) { ?>
						<th><?php _e( 'Phone', 'ignitewoo_gift_certs'); ?></th>
					<?php } ?>

				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['quantity'] ) { ?>
					<th><?php _e( 'Quanity', 'ignitewoo_gift_certs'); ?></th>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['Codes'] ) { ?>
					<th><?php _e( 'Code Numbers', 'ignitewoo_gift_certs'); ?></th>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['data'] ) { ?>
					<th><?php _e( 'Extra Data', 'ignitewoo_gift_certs'); ?></th>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['items'] ) { ?>
					<th><?php _e( 'Order Items', 'ignitewoo_gift_certs'); ?></th>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['cost'] ) { ?>
					<th><?php _e( 'Cost', 'ignitewoo_gift_certs'); ?></th>
				<?php } ?>



			</tr>
		</thead>
		<tbody>
			<?php foreach( $buyers as $b ) { ?>
			<tr>
				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['firstname'] ) { ?>
					<td>
						<?php 
							echo $b['first_name']; 
							//if ( $b['billing'] != $b['shipping'] )
							//	echo  '<br/><br/><strong>' . __( 'Shipped to:', 'ignitewoo_gift_certs' ) . '</strong><br/>' . $b['shipping']
						?>
					</td>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['lastname'] ) { ?>
					<td>
						<?php 
							echo $b['last_name']; 
							//if ( $b['billing'] != $b['shipping'] )
							//	echo  '<br/><br/><strong>' . __( 'Shipped to:', 'ignitewoo_gift_certs' ) . '</strong><br/>' . $b['shipping']
						?>
					</td>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['combine_address'] ) { //1 == $_POST['ignitewoo_gc_report_fields']['address'] ) { ?>
					<td>

							<?php //if ( 1 == $_POST['ignitewoo_donation_report_fields']['combine_address'] ) { ?>

								<?php echo $b['billing_address']; ?>

								<?php echo '<br/>'; ?>

								<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['city'] ) { ?>
									<?php echo $b['billing_city']; ?> 
								<?php } ?>

								<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['state'] ) { ?>
									<?php echo $b['billing_state']; ?> 
								<?php } ?>

								<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['country'] ) { ?>
									<?php echo $b['billing_country']; ?> 
								<?php } ?>

								<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['postalcode'] ) { ?>
									<?php echo $b['billing_postalcode']; ?>
								<?php } ?>

								<?php echo '<br/>'; ?>

								<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['phone'] ) { ?>
									<?php echo htmlentities( $b['phone'] ); ?><br/>
								<?php } ?>

								<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['email'] ) { ?>
									<?php echo htmlentities( $b['email'] ); ?>
								<?php } ?>
							<?php //} ?>

					</td>
				<?php } ?>

				<?php if ( 1 != $_POST['ignitewoo_gc_report_fields']['combine_address'] ) { ?>

					<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['city'] ) { ?>
						<td>
							<?php echo $b['billing_city']; ?>
						</td>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['state'] ) { ?>
						<td>
							<?php echo $b['billing_state']; ?>
						</td>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['country'] ) { ?>
						<td>
							<?php echo $b['billing_country']; ?>
						</td>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['postalcode'] ) { ?>
						<td>
							<?php echo $b['billing_postalcode']; ?>
						</td>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['email'] ) { ?>
						<td>
							<?php echo htmlentities( $b['email'] ); ?>
						</td>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['phone'] ) { ?>
						<td>
							<?php echo htmlentities( $b['phone'] ); ?>
						</td>
					<?php } ?>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['quantity'] ) { ?>
					<td>
						<?php 
							echo $b['qty'];
						?>
					</td>

				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['codes'] ) { ?>
					<td>
						<?php 
							for( $i = 1; $i <= $b['qty']; $i++ ) 
								echo $b['prefix'] . $i . '<br/><br/>';
						?>
					</td>
				<?php } ?>


				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['data'] ) { ?>
					<td>
						<?php 
						    foreach( $b['item_meta'] as $key => $vals ) { 
							    // Do not print customer-supplied meta data that contains a URL - could expose sensitive info 
							    // depending on what the customer uploaded via the Event forms
							    if ( false === strpos( $vals['meta_value'], 'http://' ) && false === strpos( $vals['meta_value'], 'https://' ) )
								    echo '<p>' . $vals['meta_name'] . ' &ndash; ' . $vals['meta_value'] . '</p>';
						    }
						?>
					</td>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['items'] ) { ?>
					<td>
					    <table class="tablesorter_mini" style="width: 100%">
					    <thead><tr>
						    <th style="width:25%;text-align:left;background-color:transparent; border-bottom: 1px dotted #333"><?php _e( 'SKU', 'ignitewoo_gift_certs' )?></th>
						    <th style="text-align:left;background-color:transparent; border-bottom: 1px dotted #333"><?php _e( 'Name', 'ignitewoo_gift_certs' ) ?></th>
					    </tr></thead>
					    <tbody>
					    <?php 
						foreach( $b['items'] as $item ) { 

							if ( isset( $item['variation_id'] ) && $item['variation_id'] > 0 ) 
								$_product = new WC_Product_Variation( $item['variation_id'] );
							else
								$_product = new WC_Product( $item['id'] );

							echo '<tr><td>';

							if ( $_product->sku ) echo $_product->sku; else echo '-';
							
							echo '</td><td>' . $item['name'];

							if (isset($_product->variation_data)) echo '<br/>' . woocommerce_get_formatted_variation( $_product->variation_data, true );

							echo '</td></tr>';

							$b['total'] = $b['order_total'];
						}
					    ?>
					    </tbody>
					    </table>
					</td>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_gc_report_fields']['cost'] ) { ?>
					<td><?php echo get_woocommerce_currency_symbol() . $b['total'] ?></td>
				<?php } ?>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	*/ ?>
<?php
}

