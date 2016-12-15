<?php
/**
 * Copyright (c) 2012-2013, IgniteWoo.com
 * Copyright (c) 2012-2013, SkyVerge, Inc.
 *
 * GPL v3
 */


if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );

add_filter( 'bulk_actions-edit-ign_voucher', 'ign_vouchers_edit_voucher_bulk_actions' );

/**
 * Remove the bulk edit action for vouchers, it really isn't useful
 *
 * @since 3.3
 * @param array $actions associative array of action identifier to name
 *
 * @return array associative array of action identifier to name
 */
function ign_vouchers_edit_voucher_bulk_actions( $actions ) {

	unset( $actions['edit'] );

	return $actions;
}


add_filter( 'views_edit-ign_voucher', 'ign_vouchers_edit_voucher_views' );

/**
 * Modify the 'views' links, ie All (3) | Publish (1) | Draft (1) | Private (2) | Trash (3)
 * shown above the vouchers list table, to hide the publish/private states,
 * which are not important and confusing for voucher objects.
 *
 * @since 3.3
 * @param array $views associative-array of view state name to link
 *
 * @return array associative array of view state name to link
 */
function ign_vouchers_edit_voucher_views( $views ) {

	// publish and private are not important distinctions for vouchers
	unset( $views['publish'], $views['private'] );

	return $views;
}


add_filter( 'manage_edit-ign_voucher_columns', 'ign_vouchers_edit_voucher_columns' );

/**
 * Columns for Vouchers page
 *
 * @since 3.3
 * @param array $columns associative-array of column identifier to header names
 *
 * @return array associative-array of column identifier to header names for the vouchers page
 */
function ign_vouchers_edit_voucher_columns( $columns ){

	$new_columns = array();

	$new_columns['cb'] = $columns['cb'];
	
	unset( $columns['cb'] );
	
	$new_columns['thumb'] = __( 'Image', 'ignitewoo_gift_certs' );

	$columns = array_merge( $new_columns, $columns );
	
	return $columns;
}


add_action( 'manage_ign_voucher_posts_custom_column', 'ign_vouchers_custom_voucher_columns', 2 );


/**
 * Custom Column values for Vouchers page
 *
 * @since 3.3
 * @param string $column column identifier
 */
function ign_vouchers_custom_voucher_columns( $column ) {
	global $post, $woocommerce;

	$voucher = new IGN_Gift_Cert_Voucher( $post->ID );

	switch ( $column ) {
		case 'thumb':
			?>
			<style>
			th#thumb { width: 150px }
			</style>
			<?php 
			$edit_link = get_edit_post_link( $post->ID );
			echo '<a href="' . $edit_link . '">' . $voucher->get_image() . '</a>';
		break;
		
		/*
		case 'name':
			$edit_link = get_edit_post_link( $post->ID );
			$title = _draft_or_post_title();

			$post_type_object = get_post_type_object( $post->post_type );
			$can_edit_post = current_user_can( $post_type_object->cap->edit_post, $post->ID );

			echo '<strong><a class="row-title" href="' . $edit_link . '">' . $title . '</a>';

			// display post states a little more selectively than _post_states( $post );
			if ( 'draft' == $post->post_status ) {
				echo " - <span class='post-state'>" . __( 'Draft', 'ignitewoo_gift_certs' ) . '</span>';
			}

			echo '</strong>';

			// Get actions
			$actions = array();

			$actions['id'] = 'ID: ' . $post->ID;

			
			if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
				if ( 'trash' == $post->post_status )
					$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash', 'ignitewoo_gift_certs' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-' . $post->post_type . '_' . $post->ID ) . "'>" . __( 'Restore', 'ignitewoo_gift_certs' ) . "</a>";
				elseif ( EMPTY_TRASH_DAYS )
					$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash', 'ignitewoo_gift_certs' ) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash', 'ignitewoo_gift_certs' ) . "</a>";
				if ( 'trash' == $post->post_status || ! EMPTY_TRASH_DAYS )
					$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently', 'ignitewoo_gift_certs' ) ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently', 'ignitewoo_gift_certs' ) . "</a>";
			}

			// TODO: maybe add a duplicate voucher action

			$actions = apply_filters( 'post_row_actions', $actions, $post );

			echo '<div class="row-actions">';

			$i = 0;
			$action_count = count( $actions );

			foreach ( $actions as $action => $link ) {
				( $action_count - 1 == $i ) ? $sep = '' : $sep = ' | ';
				echo '<span class="' . $action . '">' . $link . $sep . '</span>';
				$i++;
			}
			echo '</div>';
		break;
		*/

	}
}
