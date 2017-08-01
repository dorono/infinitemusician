<?php
/**
 * Homestore_Integrations Class
 * Provides integrations with Storefront extensions by removing/changing incompatible controls/settings. Also adjusts default values
 * if they need to differ from the original setting.
 *
 * @author   WooThemes
 * @package  Homestore
 * @since    2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Homestore_Integrations' ) ) :

	/**
	 * Homestore integrations class
	 */
	class Homestore_Integrations {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'after_switch_theme',        array( $this, 'edit_theme_mods' ) );
			add_action( 'customize_register',        array( $this, 'edit_controls' ), 99 );
			add_action( 'customize_register',        array( $this, 'set_extension_default_settings' ), 99 );
			add_action( 'init',                      array( $this, 'default_theme_mod_values' ) );
			add_action( 'wp',                        array( $this, 'storefront_woocommerce_customiser' ) );
			add_filter( 'sp_designer_selectors_map', array( $this, 'new_powerpack_selectors' ) );

			/**
			 * Storefront WooCommerce Customiser
			 */
			if ( class_exists( 'Storefront_WooCommerce_Customiser' ) ) {
				add_filter( 'swc_setting_defaults', array( $this, 'homestore_swc_setting_defaults' ), 99 );
			}
		}

		/**
		 * Adds a new selector to Powerpack
		 * @return array Powerpack css selectors
		 */
		function new_powerpack_selectors( $map ) {
			$map[ 'homepage-product-section-title' ] = array(
				'selector' => '.site-main .storefront-product-section h2.section-title',
				'name'     => __( 'Homepage product section title', 'homestore' ),
			);

			return $map;
		}

		/**
		 * Filters the Storefront WooCommerce Customiser default settings to set them appropriately for this theme.
		 *
		 * @param array $args the SWC default settings.
		 * @return array
		 */
		public function homestore_swc_setting_defaults( $args ) {
			$args['swc_homepage_category_columns']           = '3';
			$args['swc_homepage_category_limit']             = '3';
			$args['swc_homepage_recent_products_columns']    = '3';
			$args['swc_homepage_recent_products_limit']      = '3';
			$args['swc_homepage_featured_products_columns']  = '3';
			$args['swc_homepage_featured_products_limit']    = '3';
			$args['swc_homepage_top_rated_products_columns'] = '3';
			$args['swc_homepage_top_rated_products_limit']   = '3';
			$args['swc_homepage_on_sale_products_columns']   = '3';
			$args['swc_homepage_on_sale_products_limit']     = '3';

			return $args;
		}

		/**
		 * Returns an array of the desired Storefront extension settings
		 *
		 * @return array
		 */
		public function get_extension_defaults() {
			return apply_filters( 'homestore_default_extension_settings', $args = array(
				/**
				 * Parallax Hero
				 */
				'sph_hero_link_color'  => Homestore::color_black( null ),
				'sph_hero_text_color'  => Homestore::color_black( null ),
				'sph_heading_color'    => Homestore::color_black( null ),

				/**
				 * Product Hero
				 */
				'sprh_layout'            => 'fixed',
				'sprh_heading_color'     => Homestore::color_black( null ),
				'sprh_background_color'  => Homestore::color_white( null ),
				'sprh_hero_text_color'   => Homestore::color_black( null ),
				'sprh_overlay_opacity'   => '0',

				/**
				 * Footer Bar
				 */
				'sfb_background_color' => '#1f1d1d',
			) );
		}

		/**
		 * Set default settings for Storefront extensions to provide compatibility with Homestore.
		 *
		 * @uses get_extension_defaults()
		 * @param array $wp_customize the Customizer object.
		 * @return void
		 */
		public function set_extension_default_settings( $wp_customize ) {
			foreach ( self::get_extension_defaults() as $mod => $val ) {
				$setting = $wp_customize->get_setting( $mod );

				if ( is_object( $setting ) ) {
					$setting->default = $val;
				}
			}
		}

		/**
		 * Returns a default theme_mod value if there is none set.
		 *
		 * @uses get_extension_defaults()
		 * @return void
		 */
		public function default_theme_mod_values() {
			foreach ( self::get_extension_defaults() as $mod => $val ) {
				add_filter( 'theme_mod_' . $mod, function( $setting ) use ( $val ) {
					return $setting ? $setting : $val;
				});
			}
		}

	    /**
		 * Remove unused/incompatible controls from the Customizer to avoid confusion
		 *
		 * @param array $wp_customize the Custsomizer object.
		 * @return void
		 */
		public function edit_controls( $wp_customize ) {
			/**
			 * Storefront Designer
			 */
			$wp_customize->remove_control( 'sd_header_layout' );
			$wp_customize->remove_control( 'sd_button_flat' );
			$wp_customize->remove_control( 'sd_button_shadows' );
			$wp_customize->remove_control( 'sd_button_background_style' );
			$wp_customize->remove_control( 'sd_button_rounded' );
			$wp_customize->remove_control( 'sd_button_size' );
			$wp_customize->remove_control( 'sd_header_layout_divider_after' );
			$wp_customize->remove_control( 'sd_button_divider_1' );
			$wp_customize->remove_control( 'sd_button_divider_2' );
		}

		/**
		 * Remove any pre-existing theme mods for settings that are incompatible with Homestore.
		 *
		 * @return void
		 */
		public function edit_theme_mods() {
			/**
			 * Storefront Designer
			 */
			remove_theme_mod( 'sd_header_layout' );
			remove_theme_mod( 'sd_button_flat' );
			remove_theme_mod( 'sd_button_shadows' );
			remove_theme_mod( 'sd_button_background_style' );
			remove_theme_mod( 'sd_button_rounded' );
			remove_theme_mod( 'sd_button_size' );
			remove_theme_mod( 'sd_content_background_color' );
		}

		/**
		 * Storefront WooCommerce Customiser compatibility tweaks
		 */
		public function storefront_woocommerce_customiser() {
			remove_action( 'storefront_header', 'storefront_product_search', 40 );
			remove_action( 'storefront_header', 'storefront_header_cart', 60 );

			$cart_link = true;
			$search    = true;

			if ( class_exists( 'Storefront_WooCommerce_Customiser' ) ) {
				$cart_link 	= get_theme_mod( 'swc_header_cart', true );
				$search 	= get_theme_mod( 'swc_header_search', true );
			}

			if ( true == $cart_link ) {
				add_action( 'storefront_header', 'storefront_header_cart', 30 );
			}

			if ( true == $search ) {
				add_action( 'storefront_header', 'storefront_product_search', 10 );
			}
		}
	}

endif;

return new Homestore_Integrations();
