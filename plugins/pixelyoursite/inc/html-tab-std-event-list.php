<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="pys-box">
	<div class="pys-col pys-col-full">
		<h2 class="section-title"><?php _e( 'Active Events', 'pys' ); ?></h2>

		<?php

		$show_modal_url = add_query_arg( array(
			'action' => 'pys_edit_std_event', '_wpnonce' => wp_create_nonce( 'pys_show_event_modal' )
		), admin_url( 'admin-ajax.php' ) );

		?>

		<div class="tablenav top">
			<a href="<?php echo esc_url( $show_modal_url ); ?>" class="button button-primary action thickbox"><?php _e( 'Add new event', 'pys' ); ?></a>
			<a href="#" class="button action" id="pys-bulk-delete-std-events"><?php _e( 'Delete selected', 'pys' ); ?></a>
		</div>

		<table class="widefat fixed pys-list pys-std-events-list">
			<thead>
			<tr>
				<td class="check-column"><input type="checkbox"></td>
				<th scope="col" class="column-type"><?php _e( 'Type', 'pys' ); ?></th>
				<th scope="col" class="column-url">URL</th>
				<th scope="col" class="column-code"><?php _e( 'Code', 'pys' ); ?></th>
				<th scope="col" class="column-actions"><?php _e( 'Actions', 'pys' ); ?></th>
			</tr>
			</thead>
			<tbody>

			<?php if ( $std_events = get_option( 'pixel_your_site_std_events' ) ) : ?>

				<?php foreach ( $std_events as $key => $params ) : ?>

					<?php

					// skip wrong events
					if ( ! isset( $params['eventtype'] ) || ! isset( $params['pageurl'] ) ) {
						continue;
					}

					?>

					<tr>
						<th scope="row" class="check-column">
							<input type="checkbox" class="std-event-check" data-id="<?php esc_attr_e( $key ); ?>">
						</th>

						<td><?php esc_attr_e( $params['eventtype'] ); ?></td>
						<td>
							<pre><?php echo $params['pageurl']; ?></pre>
						</td>
						<td>

							<?php

							$code = '';
							if ( $params['eventtype'] == 'CustomCode' ) {

								$code = $params['code'];

							} else {

								$code = pys_render_event_code( $params['eventtype'], $params );

							}

							$code = stripcslashes( $code );
							$code = trim( $code );
							echo '<pre>' . $code . '</pre>';

							?>

						</td>
						<td>

							<?php
							
							$edit_event_url = add_query_arg( array(
								'action' => 'pys_edit_std_event', '_wpnonce' => wp_create_nonce( 'pys_show_event_modal' ), 'id' => $key,
							), admin_url( 'admin-ajax.php' ) );
							
							$delete_event_url = add_query_arg( array(
								'action' => 'pys_delete_events', '_wpnonce' => wp_create_nonce( 'pys_delete_events' ), 'events_ids' => array( $key ), 'events_type' => 'standard'
							), admin_url( 'admin.php?page=pixel-your-site' ) );

							?>

							<a href="<?php echo esc_url( $edit_event_url ); ?>" class="button action thickbox">Edit</a>
							<a href="<?php echo esc_url( $delete_event_url ); ?>" class="button btn-delete-std-event action">Delete</a>

						</td>
					</tr>

				<?php endforeach; ?>

			<?php endif; ?>

			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
	jQuery(function ($) {

		/**
		 * Bulk delete Standard events
		 */
		$('#pys-bulk-delete-std-events').on('click', function (e) {

			e.preventDefault();
			$(this).addClass('disabled');

			// collect all selected rows to ids array
			var ids = [];
			$.each($('.std-event-check'), function (index, check) {
				if ($(check).prop('checked') == true) {
					ids.push($(check).data('id'));
				}
			});

			$.ajax({
				url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				type: 'post',
				dataType: 'json',
				data: {
					action: 'pys_bulk_delete_std_events',
					_wpnonce: '<?php echo wp_create_nonce( "pys_bulk_delete_std_events" ); ?>',
					events_ids: ids
				}
			})
				.done(function (data) {
					location.href = '<?php echo admin_url( "admin.php?page=pixel-your-site&active_tab=posts-events" ); ?>';
				});

		});

	});
</script>