<?php
/**
 * Homestore_Customizer Class
 * Makes adjustments to Storefront cores Customizer implementation.
 *
 * @author   WooThemes
 * @package  Homestore
 * @since    1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Homestore_Customizer' ) ) :

	/**
	 * Homestore Customizer Class
	 */
	class Homestore_Customizer {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts',  array( $this, 'add_customizer_css' ), 1000 );
			add_action( 'customize_register',  array( $this, 'edit_default_controls' ), 99 );
			add_filter( 'storefront_setting_default_values', array( $this, 'get_defaults' ) );

			/**
			 * The following can be removed when Storefront 2.1 lands
			 */
			add_action( 'customize_register',  array( $this, 'edit_default_customizer_settings' ), 99 );
			add_action( 'init',                array( $this, 'default_theme_mod_values' ) );
			add_action( 'init',                array( $this, 'default_theme_settings' ) );
		}

		/**
		 * Returns an array of the desired default Storefront options
		 *
		 * @return array
		 */
		public static function get_defaults() {
			return apply_filters( 'homestore_default_settings', array(
				'storefront_header_text_color'              => Homestore::color_white( null ),
				'storefront_header_link_color'              => Homestore::color_white( null ),
				'storefront_button_background_color'        => Homestore::color_brown( null ),
				'storefront_button_text_color'              => Homestore::color_white( null ),
				'storefront_button_alt_background_color'    => Homestore::color_black( null ),
				'storefront_button_alt_text_color'  		=> Homestore::color_white( null ),
				'storefront_text_color'                     => Homestore::color_black( null ),
				'storefront_heading_color'                  => Homestore::color_black( null ),
				'storefront_header_link_color'              => Homestore::color_white( null ),
				'storefront_accent_color'                   => Homestore::color_brown( null ),
				'storefront_layout'                         => self::default_layout( null ),
				'storefront_header_background_color'        => '#151515',
				'storefront_footer_background_color'        => '#1f1d1d',
				'storefront_footer_heading_color'           => '#e2e0de',
				'storefront_footer_text_color'              => '#8d8d8d',
				'storefront_footer_link_color'              => '#bebebe',
				'background_color'                          => '#fcfcfc',
			) );
		}

		/**
		 * Set default Customizer settings based on Homestore design.
		 *
		 * @param array $wp_customize The Customizer object.
		 * @uses get_defaults()
		 * @return void
		 */
		public function edit_default_customizer_settings( $wp_customize ) {
			foreach ( self::get_defaults() as $mod => $val ) {
				$setting = $wp_customize->get_setting( $mod );

				if ( is_object( $setting ) ) {
					$setting->default = $val;
				}
			}
		}

		/**
		 * Returns a default theme_mod value if there is none set.
		 *
		 * @uses get_defaults()
		 * @return void
		 */
		public function default_theme_mod_values() {
			foreach ( self::get_defaults() as $mod => $val ) {
				add_filter( 'theme_mod_' . $mod, function( $setting ) use ( $val ) {
					return $setting ? $setting : $val;
				}, 0 );
			}
		}

		/**
		 * Sets default theme color filters for storefront color values.
		 *
		 * @uses get_defaults()
		 * @return void
		 */
		public function default_theme_settings() {
			$prefix_regex = '/^storefront_/';
			foreach ( self::get_defaults() as $mod => $val ) {
				if ( preg_match( $prefix_regex, $mod ) ) {
					$filter = preg_replace( $prefix_regex, 'storefront_default_', $mod );
					add_filter( $filter, function( $setting ) use ( $val ) {
						return $val;
					}, 99 );
				}
			}
		}

		/**
		 * Modify the default controls
		 *
		 * @param array $wp_customize the Customizer object.
		 * @return void
		 */
		public function edit_default_controls( $wp_customize ) {
			$wp_customize->get_setting( 'storefront_header_text_color' )->transport = 'refresh';
			$wp_customize->get_setting( 'background_color' )->transport = 'refresh';
			$wp_customize->remove_control( 'storefront_footer_background_color' );
			$wp_customize->remove_control( 'storefront_header_link_color' );
		}

		/**
		 * Add CSS using settings obtained from the theme options.
		 *
		 * @uses get_defaults()
		 * @return void
		 */
		public function add_customizer_css() {
			$defaults = self::get_defaults();
			$styles = array();

			$header_background_color = get_theme_mod( 'storefront_header_background_color', $defaults['storefront_header_background_color'] );
			$header_text_color       = get_theme_mod( 'storefront_header_text_color', $defaults['storefront_header_text_color'] );
			$button_alt_bgcolor      = get_theme_mod( 'storefront_button_alt_background_color', $defaults['storefront_button_alt_background_color'] );
			$button_alt_color        = get_theme_mod( 'storefront_button_alt_text_color', $defaults['storefront_button_alt_text_color'] );
			$headings_color          = get_theme_mod( 'storefront_heading_color', $defaults['storefront_heading_color'] );
			$text_color              = get_theme_mod( 'storefront_text_color', $defaults['storefront_text_color'] );
			$accent_color            = get_theme_mod( 'storefront_accent_color', $defaults['storefront_accent_color'] );
			$background_color        = storefront_get_content_background_color();

			$styles[] = '.site-header,
			.main-navigation ul ul,
			.secondary-navigation ul ul,
			.main-navigation ul.menu > li.menu-item-has-children:after,
			.secondary-navigation ul.menu ul,
			.main-navigation ul.menu ul,
			.main-navigation ul.nav-menu ul {
				background-color: ' . $header_background_color . ';
			}';

			$styles[] = '.storefront-product-section .section-title:after, .single-product div.product .related.products > h2:first-child:after {
				border-color: transparent ' . $background_color . $background_color . $background_color . ';
			}';

			$styles[] = '#page #site-navigation {
				border-bottom-color: ' . $header_background_color .  ';
			}';

			$styles[] = '.main-navigation ul.menu > li.current-menu-item > a, .main-navigation ul.nav-menu > li.current_page_item > a, .main-navigation ul.nav-menu > li:hover > a {
				color: ' . $accent_color . ';
			}';

			$styles[] = '.site-title a,
			.site-description,
			.secondary-navigation .menu > li > a,
			.secondary-navigation .nav-menu > li > a,
			.secondary-navigation .nav-menu > li > a:before {
				color: ' . $header_text_color . ' !important;
			}

			#masthead p.site-description {
				border-left-color: ' . $header_text_color . ' !important;
			}

			.secondary-navigation .menu > li > a:before,
			.secondary-navigation .nav-menu > li > a:before {
				background-color: ' . $header_text_color . ' !important;
			}';

			$styles[] = '.sprh-hero a.button,
			.sph-hero a.button {
				background-color: ' . $button_alt_bgcolor . ';
				color: ' . $button_alt_color . ';
			}';

			if ( $background_color ) {
				$styles[] = '#page .site-header .hs-primary-navigation, .woocommerce-breadcrumb { background-color: ' . $background_color . '; }';
			}

			if ( class_exists( 'Storefront_Parallax_Hero' ) ) {
				$sph_text_color = get_theme_mod( 'sph_hero_text_color', apply_filters( 'storefront_default_text_color', Homestore::color_black( null ) ) );
				$sph_link_color = get_theme_mod( 'sph_hero_link_color', apply_filters( 'storefront_default_accent_color', Homestore::color_black( null ) ) );

				$styles[] = '.sph-hero-content { color: ' . $sph_text_color . '; }';
				$styles[] = '.sph-hero-content a:not(.button) { color: ' . $sph_link_color . ' !important; }';
			}

			wp_add_inline_style( 'storefront-child-style', implode( "\n", $styles ) );
		}

		/**
		 * Adjust the default Storefront layout
		 * Makes the sidebar appear on the left instead of the right.
		 *
		 * @param string $layout the current layout.
		 */
		public static function default_layout( $layout ) {
			$layout = is_rtl() ? 'right' : 'left';
			return $layout;
		}
	}

endif;

return new Homestore_Customizer();
