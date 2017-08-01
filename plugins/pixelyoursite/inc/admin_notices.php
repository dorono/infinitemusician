<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once "admin_notices_content.php";

if ( ! function_exists( 'pys_admin_notices_hook' ) ) {

	add_action( 'admin_notices', 'pys_admin_notices_hook' );
	function pys_admin_notices_hook() {

		if ( false == current_user_can( 'manage_options' ) ) {
			return;
		}

		$now             = time();
		$activation_time = (int) get_option( 'pys_free_activation_time', null );
		$version         = get_option( 'pys_free_notices_version', null );

		if ( empty( $activation_time ) || version_compare( $version, PYS_FREE_NOTICES_VERSION, '<' ) ) {

			$activation_time = $now;

			update_option( 'pys_free_activation_time', $activation_time );
			update_option( 'pys_free_notices_version', PYS_FREE_NOTICES_VERSION );

			## reset dismissed notices
			update_option( 'pys_free_dismissed_options', array() );

		}

		global $PYS_FREE_WOO_NOTICES, $PYS_FREE_EDD_NOTICES, $PYS_FREE_NO_WOO_NO_EDD_NOTICES;

		## switch suitable notices set
		if( pys_is_woocommerce_active() ) {
			
			$notices_set      = $PYS_FREE_WOO_NOTICES;
			$notices_set_name = 'PYS_FREE_WOO_NOTICES';
			
		} else if ( pys_is_edd_active() ) {
			
			$notices_set      = $PYS_FREE_EDD_NOTICES;
			$notices_set_name = 'PYS_FREE_EDD_NOTICES';
			
		} else {
			
			$notices_set      = $PYS_FREE_NO_WOO_NO_EDD_NOTICES;
			$notices_set_name = 'PYS_FREE_NO_WOO_NO_EDD_NOTICES';
			
		}
		
		$dismissed_notices = get_option( 'pys_free_dismissed_options', array() );
		
		## calc days passed since activation
		$days_passed = pys_calc_days_passed( $now, $activation_time );
		
		## calc expiration time
//		$expiration_days = 3;
//		$expiration_time = $activation_time + $expiration_days * DAY_IN_SECONDS;
		
		$content   = null;
		$notice_id = null;

		foreach ( $notices_set as $notice ) {

			if( empty( $dismissed_notices[ $notices_set_name ][ $notice['from'] ] ) ) {
				$is_dismissed = false;
			} else {
				$is_dismissed = true;
			}

			if( $is_dismissed || $notice['visible'] == false ) {
				break;
			}

			if ( $days_passed >= $notice['from'] && $days_passed <= $notice['to'] ) {

				$notice_id = $notice['from'];
				$content = $notice['content'];
				break;

			}

		}

		## nothing to show
		if( empty( $content ) ) {
			return;
		}

		?>

		<style type="text/css">
			.pys-notice p a {
				color: #F4524D;
			}
		</style>

		<div class="notice-warning notice is-dismissible pys-notice" data-pys-set-name="<?php esc_attr_e( $notices_set_name ); ?>" data-pys-notice-id="<?php esc_attr_e( $notice_id ); ?>">
			<p><?php echo $content; ?></p>
		</div>

		<script type='text/javascript'>
			jQuery(document).on('click', '.pys-notice .notice-dismiss', function () {

				var wrapper = jQuery(this).parent('div.pys-notice'),
					set_name = wrapper.data('pys-set-name'),
					notice_id = wrapper.data('pys-notice-id');

				jQuery.ajax({
					url: ajaxurl,
					data: {
						action: 'pys_dismiss_admin_notice',
						set: set_name,
						id: notice_id,
						_wpnonce: '<?php echo wp_create_nonce( 'pys_notice_dismiss' ); ?>'
					}
				})

			});
        </script>

		<?php
	}

}

if ( ! function_exists( 'pys_dismiss_admin_notice_ajax' ) ) {

	add_action( 'wp_ajax_pys_dismiss_admin_notice', 'pys_dismiss_admin_notice_ajax' );
	function pys_dismiss_admin_notice_ajax() {

		if ( false == current_user_can( 'manage_options' ) ) {
			exit();
		}

		if ( empty( $_GET['_wpnonce'] ) || false == wp_verify_nonce( $_GET['_wpnonce'], 'pys_notice_dismiss' ) ) {
			exit();
		}

		if ( isset( $_GET['id'] ) && isset( $_GET['set'] )) {

			$set_name  = $_GET['set'];
			$notice_id = $_GET['id'];

			$dismissed_notices = get_option( 'pys_free_dismissed_options', array() );
			$dismissed_notices[ $set_name ][ $notice_id ] = 1;

			update_option( 'pys_free_dismissed_options', $dismissed_notices );

		}

		exit();
	}

}

if( ! function_exists( 'pys_calc_days_passed' ) ) {

	function pys_calc_days_passed( $now, $time_to_compare ) {

		$time_passed = ( $now - $time_to_compare ) / DAY_IN_SECONDS;
		$time_passed = floor( $time_passed ) + 1;

		return $time_passed;

	}

}



