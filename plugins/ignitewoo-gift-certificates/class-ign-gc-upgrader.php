<?php
/*
Copyright (c) 2012-2015 IgniteWoo.com - All Rights Reserved
*/

if ( !defined( 'ABSPATH' ) ) exit; 

class IGN_GC_Upgrader { 

	function __construct() { 
	
	}
	
	
	function do_upgrade() { 

		$current_version = get_option( 'ign_gc_version' );
		
		if ( empty( $current_version ) )
			$current_version = '0.1';

		$res = false;
		
		// Upgrading from less than 3.2 triggers these upgrades

		// Do 3.2 upgrade
		if ( version_compare( $current_version, '3.2' ) < 0 )
			$res = $this->do_3_2();
	
		return $res;
	
	}

	
	// Gift cert recipients moved to item meta as of v3.2
	function do_3_2() { 
		global $woocommerce, $wpdb;
	
		// get all order IDs 
		$sql = 'select ID from ' . $wpdb->posts . ' where post_type="shop_order"';
		
		$orders = $wpdb->get_results( $sql );
		
		if ( empty( $orders ) )
			return true;
		
		// read post meta in each order to find gift cert recipients, move to item meta
		foreach( $orders as $o ) { 
		
			$meta = get_post_meta( $o->ID );
			
			if ( empty( $meta['ign_receiver_email'][0] ) )
				continue;
				
			$order = new WC_Order( $o->ID );
			
			foreach( $order->get_items() as $item_key => $item ) { 

				if ( !get_post_meta( $item['product_id'], 'ignite_gift_enabled', true ) )
					continue;

				for ( $q = 0; $q < $item['qty']; $q++ ) {

					$email = $meta['ign_receiver_email'][0];
					
					$name = $meta['ign_receiver_name'][0];
					
					$msg = $meta['ign_receiver_message'][0];
				
					$amt = $item['line_total'] / $item['qty'];

					$amt = round( floatval( $amt ), 2 );
				
					$r = woocommerce_get_order_item_meta( $item_key, '_voucher_recievers', true );

					if ( empty( $r ) )
						$r = array();
						
					$r[] = array( 'email' => $email, 'name' => $name, 'msg' => $msg, 'amt' => $amt );

					woocommerce_update_order_item_meta( $item_key, '_voucher_recievers', $r );

				
				}
				
			}
			
			delete_post_meta( $o->ID, 'ign_receiver_email' );
				
			delete_post_meta( $o->ID, 'ign_receiver_name' );
			
			delete_post_meta( $o->ID, 'ign_receiver_message' );


		}
		
		return true;
		
	}

}