<?php
/**
 * Additional Customer Details
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<h3><?php _e( "Customer Details", 'email-control'); ?></h3>

<?php foreach ( $fields as $field ) : ?>
	<p><strong><?php echo wp_kses_post( $field['label'] ); ?>:</strong> <span class="text"><?php echo wp_kses_post( $field['value'] ); ?></span></p>
<?php endforeach; ?>
