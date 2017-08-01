<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'pys_get_edd_content_id' ) ) {

	function pys_get_edd_content_id( $download_id ) {

		if ( pys_get_option( 'edd', 'content_id' ) == 'sku' ) {

			$sku = get_post_meta( $download_id, 'edd_sku', true );

			return empty( $sku ) ? $download_id : '"' . $sku . '"';

		} else {

			return $download_id;

		}

	}

}

if ( ! function_exists( 'pys_output_edd_ajax_events_code' ) ) {

	function pys_output_edd_ajax_events_code() {
		global $pys_edd_ajax_events;

		if ( empty( $pys_edd_ajax_events ) ) {
			$pys_edd_ajax_events = array();
		}

		$events = array();

		foreach ( $pys_edd_ajax_events as $id => $event ) {

			$params = pys_clean_system_event_params( $event['params'] );

			// sanitize params
			$sanitized = array();
			foreach ( $params as $name => $value ) {

				if ( empty( $value ) ) {
					continue;
				}

				$key               = esc_js( $name );
				$sanitized[ $key ] = $value;

			}

			$name = $event['name'];

			$events[ $id ] = array(
				'type'   => pys_is_standard_event( $name ) ? 'track' : 'trackCustom',
				'name'   => $name,
				'params' => $sanitized
			);

		}

		?>

		<script type="text/javascript">
		/* <![CDATA[ */
		var pys_edd_ajax_events = <?php echo json_encode( $events ); ?>;
		/* ]]> */
		</script>

		<?php

	}

}
