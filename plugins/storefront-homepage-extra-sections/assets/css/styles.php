<?php    

	$bg_color			= apply_filters( 'storefront_homepage_extra_sections_bg', storefront_get_content_background_color() );
	$accent_color		= get_theme_mod( 'storefront_accent_color', apply_filters( 'storefront_default_accent_color', '#FFA107' ) );
	$overlay_opacity	= apply_filters( 'storefront_homepage_extra_sections_overlay', .8 );

	// Get RGB color of overlay from HEX
	if ( Storefront_Homepage_Extra_Sections::sanitize_hex_color( $bg_color ) ) {
		list( $r, $g, $b ) = sscanf( $bg_color, "#%02x%02x%02x" );
	} else {
		$r = $g = $b = 255;
	}

	$shes_style = '
	    .storefront-homepage-extra-sections .shes-overlay {
			background-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $overlay_opacity .');
		}

		.storefront-homepage-extra-sections .shes-contact-details ul li:before {
			color: ' . $accent_color . ';
		}';

	wp_add_inline_style( 'shes-styles', $shes_style );