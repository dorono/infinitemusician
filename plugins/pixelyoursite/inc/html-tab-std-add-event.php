<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="pys-box">
	<div class="pys-col pys-col-full">

		<div style="text-align: center; margin-top: 13px;">

			<?php

			$show_modal_url = add_query_arg( array(
				'action' => 'pys_edit_std_event', '_wpnonce' => wp_create_nonce( 'pys_show_event_modal' )
			), admin_url( 'admin-ajax.php' ) );

			?>

			<a href="<?php echo esc_url( $show_modal_url ); ?>" class="pys-btn pys-btn-big pys-btn-blue thickbox"><?php _e( 'Add New Event', 'pys' ); ?></a>
			<p><?php _e( 'Add standard or custom events that will trigger when a URL is visited.', 'pys' ); ?></p>
		</div>

	</div>
</div>