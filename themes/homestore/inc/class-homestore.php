<?php
/**
 * Homestore Class
 *
 * @author   WooThemes
 * @since    1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Homestore' ) ) :

class Homestore {
	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'after_setup_theme',   array( $this, 'setup_menus' ) );
		add_action( 'after_setup_theme',   array( $this, 'register_default_header_images' ) );
		add_action( 'wp_enqueue_scripts',  array( $this, 'enqueue_styles' ), 99 );
		add_action( 'wp_enqueue_scripts',  array( $this, 'enqueue_child_styles' ), 99 );
		add_action( 'wp_print_scripts',    array( $this, 'javascript_class' ), 0 );

		add_filter( 'body_class',                     array( $this, 'body_class' ) );
	}

	/**
	 * Registers the menu locations
	 */
	public function setup_menus() {
		register_nav_menus( array(
			'homepage' => __( 'Homepage Menu', 'homestore' ),
		) );
	}

	/**
	 * Enqueue Storefront Styles
	 * @return void
	 */
	public function enqueue_styles() {
		global $storefront_version;

		wp_enqueue_style( 'storefront-style', get_template_directory_uri() . '/style.css', $storefront_version );
		wp_style_add_data( 'storefront-child-style', 'rtl', 'replace' );
	}

	/**
	 * Enqueue Homestore Styles
	 * @return void
	 */
	public function enqueue_child_styles() {
		global $homestore_version;

		wp_enqueue_style( 'work-sans', '//fonts.googleapis.com/css?family=Work+Sans:400,300,500,600,700', array( 'storefront-child-style' ) );
		wp_enqueue_style( 'montserrat-alternates', '//fonts.googleapis.com/css?family=Montserrat+Alternates:400,700', array( 'storefront-child-style' ) );

		wp_enqueue_script( 'homestore', get_stylesheet_directory_uri() . '/assets/js/homestore.min.js', array( 'jquery' ), $homestore_version, true );
	}

	/**
	 * Registers the default header image.
	 */
	public function register_default_header_images() {
		register_default_headers( array(
			'default' => array(
				'url'           => get_stylesheet_directory_uri() . '/assets/images/header.jpg',
				'thumbnail_url' => get_stylesheet_directory_uri() . '/assets/images/header-thumbnail.jpg',
				/* translators: header image description */
				'description'   => __( 'Default header image', 'homestore' ),
			),
		) );
	}

	/**
	 * Adds the .js class to the body
	 */
	public function javascript_class() {
		if ( ! is_admin() ) {
			echo '<script type="text/javascript">( function( html ) { html.setAttribute( "class", "js" + ( html.getAttribute( "class" ) || "" ) ); } ).call(null, document.documentElement);</script>';
		}
	}

	/**
	 * Adds custom body classes
	 */
	public function body_class( $classes ) {
		global $storefront_version;
		if ( class_exists( 'Storefront_Designer' ) ) {
			$typographical_scheme = get_theme_mod( 'sd_typography', 'helvetica' );
			if ( 'helvetica' != $typographical_scheme ) {
				$classes[] = 'sd-custom-font-scheme';
			}
		}
		if ( version_compare( $storefront_version, '2.0.0', '>=' ) ) {
			$classes[] = 'storefront-2x';
		}
		return $classes;
	}

	/**
	 * Get a menus name based on location
	 * @param  string $location the menu location slug
	 * @return array the menu detailed assigned to an array
	 */
	public static function get_menu_name( $location ) {
		if ( empty( $location ) ) {
			return false;
		}

		$locations = get_nav_menu_locations();

		if ( ! isset( $locations[$location] ) ) {
			return false;
		}

		$menu_obj = get_term( $locations[$location], 'nav_menu' );

		return $menu_obj;
	}

	/**
	 * Black
	 * @return  string color
	 */
	public static function color_black( $color ) {
		$color = '#150604';
		return $color;
	}

	/**
	 * White
	 * @return  string color
	 */
	public static function color_white( $color ) {
		$color = '#fcfcfc';
		return $color;
	}

	/**
	 * Brown
	 * @return  string color
	 */
	public static function color_brown( $color ) {
		$color = '#c7804d';
		return $color;
	}
}

endif;

return new Homestore();