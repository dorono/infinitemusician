<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'pys_edit_std_event' ) ) {

	add_action( 'wp_ajax_pys_edit_std_event', 'pys_edit_std_event' );
	function pys_edit_std_event() {

		if ( false == current_user_can( 'manage_options' ) ) {
			exit();
		}

		if ( empty( $_GET['_wpnonce'] ) || false == wp_verify_nonce( $_GET['_wpnonce'], 'pys_show_event_modal' ) ) {
			exit();
		}

		$id     = isset( $_REQUEST['id'] ) == true ? sanitize_text_field( $_REQUEST['id'] ) : uniqid();
		$events = (array) get_option( 'pixel_your_site_std_events' );

		if ( array_key_exists( $id, $events ) ) {

			$event = $events[ $id ];

		} else {

			$event = array(
				'eventtype'        => null,
				'pageurl'          => null,
				'value'            => null,
				'currency'         => null,
				'content_name'     => null,
				'content_ids'      => null,
				'content_type'     => null,
				'content_category' => null,
				'num_items'        => null,
				'order_id'         => null,
				'search_string'    => null,
				'status'           => null,
				'code'             => null,
				'custom_name'      => null
			);

		}

		// change `eventtype`
		if ( ! empty( $event['custom_name'] ) ) {
			$event['eventtype'] = 'CustomEvent';
		}

		// collect custom params
		$custom_params = pys_get_custom_params( $event );

		$table_class = sanitize_text_field( $event['eventtype'] );

		$is_custom_currency = isset( $event['custom_currency'] )
			? sanitize_text_field( $event['custom_currency'] )
			: false;

		?>

		<form action="" method="post" id="std-event-form">
			
			<?php wp_nonce_field( 'pys_update_std_event' ); ?>
			
			<input type="hidden" name="action" value="pys_update_std_event">
			<input type="hidden" name="std_event[id]" value="<?php esc_attr_e( $id ); ?>">

			<table class="layout <?php esc_attr_e( $table_class ); ?>">
				
				<tr>
					<td class="legend"><p class="label">URL:</p></td>
					<td>
						<input type="text" name="std_event[pageurl]" value="<?php esc_attr_e( $event['pageurl'] ); ?>"
						       id="std-url">
						<span
							class="help"><?php _e( 'Event will trigger when this URL is visited.<br>If you add * at the end of the URL string, it will match all URLs starting with the this string.', 'pys' ); ?></span>
					</td>
				</tr>

				<tr>
					<td class="legend"><p class="label"><?php _e( 'Event type:', 'pys' ); ?></p></td>
					<td>
						<select name="std_event[eventtype]" id="std-event-type">
							<?php pys_event_types_select_options( $event['eventtype'] ); ?>
						</select>
					</td>
				</tr>

				<tr class="ViewContent-visible Search-visible AddToCart-visible AddToWishlist-visible InitiateCheckout-visible AddPaymentInfo-visible Purchase-visible Lead-visible CompleteRegistration-visible">
					<td class="legend"><p class="label"><?php _e( 'Value:', 'pys' ); ?></p></td>
					<td>
						<input type="text" name="std_event[value]" value="<?php esc_attr_e( $event['value'] ); ?>">
						<span class="help"><?php _e( 'Mandatory for purchase event only.', 'pys' ); ?></span>
					</td>
				</tr>

				<tr class="ViewContent-visible Search-visible AddToCart-visible AddToWishlist-visible InitiateCheckout-visible AddPaymentInfo-visible Purchase-visible Lead-visible CompleteRegistration-visible">
					<td class="legend"><p class="label"><?php _e( 'Currency:', 'pys' ); ?></p></td>
					<td>
						<select name="std_event[currency]" id="currency"
						        class="<?php echo $is_custom_currency ? 'custom-currency' : ''; ?>">
							<option
								disabled <?php selected( false, $is_custom_currency && $event['currency'] ); ?> ><?php _e( 'Select Currency', 'pys' ); ?></option>
							<?php pys_currency_options( $event['currency'] ); ?>
							<option disabled></option>
							<option
								value="pys_custom_currency" <?php selected( true, $is_custom_currency ); ?> ><?php _e( 'Custom Currency', 'pys' ); ?></option>
						</select>
						<input type="text" name="std_event[custom_currency]"
						       value="<?php echo $is_custom_currency ? $event['currency'] : ''; ?>"
						       placeholder="Enter custom currency code" class="custom-currency-visible">
						<span class="help"><?php _e( 'Mandatory for purchase event only.', 'pys' ); ?></span>
					</td>
				</tr>

				<tr class="ViewContent-visible AddToCart-visible AddToWishlist-visible InitiateCheckout-visible Purchase-visible Lead-visible CompleteRegistration-visible">
					<td class="legend"><p class="label">content_name:</p></td>
					<td>
						<input type="text" name="std_event[content_name]"
						       value="<?php esc_attr_e( $event['content_name'] ); ?>">
						<span
							class="help"><?php _e( 'Name of the page/product i.e "Really Fast Running Shoes".', 'pys' ); ?></span>
					</td>
				</tr>

				<tr class="ViewContent-visible Search-visible AddToCart-visible AddToWishlist-visible InitiateCheckout-visible AddPaymentInfo-visible Purchase-visible">
					<td class="legend"><p class="label">content_ids:</p></td>
					<td>
						<input type="text" name="std_event[content_ids]"
						       value="<?php esc_attr_e( $event['content_ids'] ); ?>">
						<span class="help"><?php _e( 'Product ids/SKUs associated with the event.', 'pys' ); ?></span>
					</td>
				</tr>

				<tr class="ViewContent-visible AddToCart-visible InitiateCheckout-visible Purchase-visible">
					<td class="legend"><p class="label">content_type:</p></td>
					<td>
						<input type="text" name="std_event[content_type]"
						       value="<?php esc_attr_e( $event['content_type'] ); ?>">
						<span
							class="help"><?php _e( 'The type of content. i.e product or product_group.', 'pys' ); ?></span>
					</td>
				</tr>

				<tr class="Search-visible AddToWishlist-visible InitiateCheckout-visible AddPaymentInfo-visible Lead-visible">
					<td class="legend"><p class="label">content_category:</p></td>
					<td>
						<input type="text" name="std_event[content_category]"
						       value="<?php esc_attr_e( $event['content_category'] ); ?>">
						<span class="help"><?php _e( 'Category of the page/product.', 'pys' ); ?></span>
					</td>
				</tr>

				<tr class="InitiateCheckout-visible Purchase-visible">
					<td class="legend"><p class="label">num_items:</p></td>
					<td>
						<input type="text" name="std_event[num_items]"
						       value="<?php esc_attr_e( $event['num_items'] ); ?>">
						<span class="help"><?php _e( 'The number of items in the cart. i.e 3.', 'pys' ); ?></span>
					</td>
				</tr>

				<tr class="Purchase-visible">
					<td class="legend"><p class="label">order_id:</p></td>
					<td>
						<input type="text" name="std_event[order_id]"
						       value="<?php esc_attr_e( $event['order_id'] ); ?>">
						<span
							class="help"><?php _e( 'The unique order id of the successful purchase. i.e 19.', 'pys' ); ?></span>
					</td>
				</tr>

				<tr class="Search-visible">
					<td class="legend"><p class="label">search_string:</p></td>
					<td>
						<input type="text" name="std_event[search_string]"
						       value="<?php esc_attr_e( $event['search_string'] ); ?>">
						<span
							class="help"><?php _e( 'The string entered by the user for the search. i.e "Shoes".', 'pys' ); ?></span>
					</td>
				</tr>

				<tr class="CompleteRegistration-visible">
					<td class="legend"><p class="label">status:</p></td>
					<td>
						<input type="text" name="std_event[status]" value="<?php esc_attr_e( $event['status'] ); ?>">
						<span
							class="help"><?php _e( 'The status of the registration. i.e completed.', 'pys' ); ?></span>
					</td>
				</tr>

				<tr class="CustomCode-visible">
					<td class="legend">
						<p class="label" style="line-height: inherit;"><?php _e( 'Custom event code (advanced users only):', 'pys' ); ?></p></td>
					<td>
						<textarea name="std_event[code]" rows="5"
						          style="width:100%;"><?php echo stripslashes( $event['code'] ); ?></textarea>
						<span
							class="help"><?php _e( 'The code inserted in the field MUST be complete, including <code>fbq(\'track\', \'AddToCart\', { … });</code>', 'pys' ); ?></span>
					</td>
				</tr>

				<tr class="CustomEvent-visible tall">
					<td class="legend"><p class="label"><?php _e( 'Event name:', 'pys' ); ?></p></td>
					<td>
						<input type="text" name="std_event[custom_name]" value="<?php echo $event['custom_name']; ?>">
					</td>
				</tr>

				<?php foreach ( $custom_params as $param => $value ) : ?>

				<?php $param_id = uniqid() . $param; ?>

				<tr class="class-<?php esc_attr_e( $param_id ); ?> ViewContent-visible Search-visible AddToCart-visible AddToWishlist-visible InitiateCheckout-visible AddPaymentInfo-visible Purchase-visible Lead-visible CompleteRegistration-visible CustomEvent-visible">
					<td class="legend"><p class="label"><?php _e( 'Param name:', 'pys' ); ?></p></td>
					<td>
						<input type="text" name="std_event[custom_names][<?php esc_attr_e( $param ); ?>]" 
						       value="<?php	esc_attr_e( $param ); ?>">
					</td>
				</tr>

					<tr class="class-<?php esc_attr_e( $param_id ); ?> ViewContent-visible Search-visible AddToCart-visible AddToWishlist-visible InitiateCheckout-visible AddPaymentInfo-visible Purchase-visible Lead-visible CompleteRegistration-visible CustomEvent-visible">
					<td class="legend"><p class="label"><?php _e( 'Param value:', 'pys' ); ?></p></td>
					<td>
						<input type="text" name="std_event[custom_values][<?php esc_attr_e( $param ); ?>]"
						       value="<?php esc_attr_e( $value ); ?>">
					</td>
				</tr>

					<tr class="class-<?php esc_attr_e( $param_id ); ?> tall ViewContent-visible Search-visible AddToCart-visible AddToWishlist-visible InitiateCheckout-visible AddPaymentInfo-visible Purchase-visible Lead-visible CompleteRegistration-visible CustomEvent-visible">
					<td></td>
					<td><a href="#" class="remove-param"
					       data-id="<?php esc_attr_e( $param_id ); ?>">Remove param</a></td>
				</tr>

				<?php endforeach; ?>

				<tr id="marker"></tr>

				<tr class="ViewContent-visible Search-visible AddToCart-visible AddToWishlist-visible InitiateCheckout-visible AddPaymentInfo-visible Purchase-visible Lead-visible CompleteRegistration-visible CustomEvent-visible">
					<td class="legend"></td>
					<td>
						<a href="#"
						   class="button button-add-row button-primary action"><?php _e( 'Add Param', 'pys' ); ?></a>
					</td>
				</tr>

			</table>

			<div class="actions-row">
				<a href="#" class="button button-close action"><?php _e( 'Cancel', 'pys' ); ?></a>
				<a href="#"
				   class="button button-save button-primary action disabled"><?php echo isset( $_REQUEST['id'] ) == true ? __( 'Save', 'pys' ) : __( 'Add', 'pys' ); ?></a>
			</div>

		</form>

		<script>
			jQuery(function ($) {

				function makeid() {
					var id = "";
					var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

					for (var i = 0; i < 10; i++)
						id += possible.charAt(Math.floor(Math.random() * possible.length));

					return id;
				}

				validate();

				/* Standard event fields show/hide on event type change. */
				$('#std-event-type').on('change', function () {
					var wrapper = $(this).closest('table');

					wrapper.removeClass();	// clear all classes
					wrapper.addClass('layout');
					wrapper.addClass(this.value);

					validate();

				});

				// Show/hide custom currency field
				$('select#currency').on('change', function () {

					if ($(this).val() == 'pys_custom_currency') {
						$(this).addClass('custom-currency');
					} else {
						$(this).removeClass('custom-currency');
					}

				});

				/* Close modal window */
				$('.button-close').on('click', function (e) {
					e.preventDefault();
					tb_remove();
				});

				/* Save / Add event */
				$('.button-save').on('click', function (e) {
					e.preventDefault();

					if (validate() == false) {
						return;
					}

					$('#std-event-form').addClass('disabled');
					$(this).text('Saving...');

					var data = $('#std-event-form').serialize();
					data = data + '&action=pys_update_std_event';

					$.ajax({
						url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						type: 'post',
						dataType: 'json',
						data: data
					})
						.done(function (data) {

							$("input[name='active_tab']").val('posts-events');
							$('.pys-content > form').submit();
							//location.reload(true);

						});

				});

				var l10n_param_name = '<?php _e( 'Param name:', 'pys' ); ?>';
				var l10n_param_value = '<?php _e( 'Param value:', 'pys' ); ?>';
				var l10n_param_remove = '<?php _e( 'Remove param', 'pys' ); ?>';

				/* Add new param/value group */
				var marker = $('#marker');
				$('.button-add-row').on('click', function (e) {
					e.preventDefault();

					var id = makeid();

					var html = '<tr class="class-' + id + ' ViewContent-visible Search-visible AddToCart-visible AddToWishlist-visible InitiateCheckout-visible AddPaymentInfo-visible Purchase-visible Lead-visible CompleteRegistration-visible CustomEvent-visible">';
					html += '<td class="legend"><p class="label">' + l10n_param_name + '</p></td>';
					html += '<td><input type="text" name="std_event[custom_names][' + id + ']" value=""></td>';
					html += '</tr>';

					html += '<tr class="class-' + id + ' ViewContent-visible Search-visible AddToCart-visible AddToWishlist-visible InitiateCheckout-visible AddPaymentInfo-visible Purchase-visible Lead-visible CompleteRegistration-visible CustomEvent-visible">';
					html += '<td class="legend"><p class="label">' + l10n_param_value + '</p></td>';
					html += '<td><input type="text" name="std_event[custom_values][' + id + ']" value=""></td>';
					html += '</tr>';

					html += '<tr class="class-' + id + ' tall ViewContent-visible Search-visible AddToCart-visible AddToWishlist-visible InitiateCheckout-visible AddPaymentInfo-visible Purchase-visible Lead-visible CompleteRegistration-visible CustomEvent-visible">';
					html += '<td></td>';
					html += '<td><a href="#" class="remove-param" data-id="' + id + '">' + l10n_param_remove + '</a></td>';
					html += '</tr>';


					$(html).insertBefore(marker);

				});

				// Remove custom params row
				$(document).on('click', '.remove-param', function (e) {
					e.preventDefault();

					var id = $(this).data('id');
					$('tr.class-' + id).remove();

				});

				// Form validation
				$('form').submit(function (e) {

					if (validate() == false) {
						e.preventDefault();
					}

				});

				$('#std-url').on('change, keyup', function (e) {
					validate();
				});

				function validate() {

					var pageURL = $('#std-url').val(),
						eventType = $('#std-event-type').val(),
						btnSave = $('.button-save'),
						isValid = true;

					if (eventType == null || pageURL.length == 0) {
						isValid = false;
					}

					if (isValid) {
						btnSave.removeClass('disabled');
					} else {
						btnSave.addClass('disabled');
					}

					return isValid;

				}

			});
		</script>

		<?php
		exit();
	}

}

if ( ! function_exists( 'pys_update_std_event' ) ) {

	add_action( 'wp_ajax_pys_update_std_event', 'pys_update_std_event' );
	function pys_update_std_event() {

		if ( false == current_user_can( 'manage_options' ) ) {
			return -1;
		}

		if ( ! isset( $_POST['action'] ) || $_POST['action'] != 'pys_update_std_event'
			|| ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'pys_update_std_event' )
		) {

			return -1;

		}

		$events = (array) get_option( 'pixel_your_site_std_events' );
		$data   = $_REQUEST['std_event'];

		$id = $data['id'];
		unset( $data['action'] );
		unset( $data['id'] );

		if ( isset( $data['custom_currency'] ) && ! empty( $data['custom_currency'] ) ) {

			$data['currency']        = strtoupper( $data['custom_currency'] );
			$data['custom_currency'] = true;

		}

		if ( $data['eventtype'] == 'CustomEvent' ) {

			$custom_name = $data['custom_name'];
			$custom_name = preg_replace( '/[^A-Za-z0-9\_]/u', '', $custom_name );
			$custom_name = trim( $custom_name );
			$custom_name = empty( $custom_name ) ? 'Untitled' : $custom_name;

			$data['eventtype'] = trim( $custom_name );
			$data['custom_name'] = $custom_name;

		}

		if ( isset( $data['custom_names'] ) && is_array( $data['custom_names'] ) ) {

			foreach ( $data['custom_names'] as $key => $value ) {

				$custom_param_name = $value;
				$custom_param_name = preg_replace( '/[^A-Za-z0-9\_]/u', '', $custom_param_name );

				## skip untitled params
				if ( empty( $custom_param_name ) ) {
					continue;
				}

				$custom_param_value = $data['custom_values'][ $key ];

				## skip custom params without values
				if ( empty( $custom_param_value ) && $custom_param_value != "0" ) {
					continue;
				}

				$data[ $custom_param_name ] = $custom_param_value;

			}

		}

		unset( $data['custom_names'] );
		unset( $data['custom_values'] );

		$events[ $id ] = $data;
		update_option( 'pixel_your_site_std_events', $events );

		echo json_encode( 'success' );

		exit();
	}

}

if ( ! function_exists( 'pys_bulk_delete_std_events' ) ) {

	add_action( 'wp_ajax_pys_bulk_delete_std_events', 'pys_bulk_delete_std_events' );
	function pys_bulk_delete_std_events() {

		if ( false == current_user_can( 'manage_options' ) ) {
			return -1;
		}

		if ( ! isset( $_POST['action'] ) || $_POST['action'] != 'pys_bulk_delete_std_events'
			|| ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'pys_bulk_delete_std_events' )
		) {

			return -1;

		}

		if ( empty( $_POST['events_ids'] ) ) {
			return -1;
		}

		pys_delete_events( $_POST['events_ids'], 'standard' );

		echo json_encode( 'success' );
		exit();

	}

}