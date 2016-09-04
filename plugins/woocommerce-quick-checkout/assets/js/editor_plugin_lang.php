<?php

$strings = 'tinyMCE.addI18n({' . _WP_Editors::$mce_locale . ':{
	wqc:{
		shortcode_generator_title: "' . esc_js( __( 'Quick Checkout', 'wqc' ) ) . '",
		shortcode_tag: "' . esc_js( apply_filters( 'wqc_shortcode_tag', 'woocommerce-quick-checkout' ) ) . '"
	}
}})';