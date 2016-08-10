<?php
/**
 * Homestore_Template Class
 *
 * @author   WooThemes
 * @since    1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Homestore_Template' ) ) :

class Homestore_Template {

	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'storefront_header',  array( $this, 'header_bar_wrapper' ), 5 );
		add_action( 'storefront_header',  array( $this, 'header_bar_wrapper_close' ), 15 );
		add_action( 'storefront_header',  array( $this, 'primary_navigation_wrapper' ), 75 );
		add_action( 'storefront_header',  array( $this, 'primary_navigation_wrapper_close' ), 85 );

		add_action( 'storefront_page',  array( $this, 'homepage_navigation' ), 5 );
		add_action( 'storefront_page',  array( $this, 'homepage_content_wrapper' ), 6 );
		add_action( 'storefront_page',  array( $this, 'homepage_content_wrapper_close' ), 30 );

		add_action( 'storefront_before_footer',  array( $this, 'footer_bar_wrapper' ), 10 );
		add_action( 'storefront_before_footer',  array( $this, 'footer_bar_wrapper_close' ), 11 );

		add_action( 'woocommerce_before_single_product_summary',  array( $this, 'single_product_title' ), 10 );

		add_action( 'init',      array( $this, 'custom_storefront_markup' ) );
		add_action( 'homepage',  array( $this, 'section_tabs' ) );
	}

	/**
	 * Custom markup tweaks
	 * @return void
	 */
	public function custom_storefront_markup() {
		remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );

		remove_action( 'storefront_header', 'storefront_primary_navigation', 50 );
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
		add_action( 'storefront_header', 'storefront_secondary_navigation', 10 );

		add_action( 'storefront_header', 'storefront_primary_navigation', 80 );
		add_action( 'woocommerce_product_thumbnails',  'woocommerce_show_product_sale_flash', 10 );

		remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper',       42 );
		remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper_close', 68 );
	}

	/**
	 * Displays the homepage menu before the homepage content (if a menu is assigned)
	 */
	public function homepage_navigation() {
		if ( is_page_template( 'template-homepage.php' ) && has_nav_menu( 'homepage' ) ) {
			$menu_object = Homestore::get_menu_name( 'homepage' );
			$menu_name   = $menu_object->name;

			echo '<div class="homestore-homepage-menu">';

			echo '<h3 class="title">' . esc_attr( $menu_name ) . '</h3>';

			wp_nav_menu(
				array(
					'theme_location'    => 'homepage',
					'container_class'   => 'homepage-navigation',
					'fallback_cb'       => '',
				)
			);

			echo '</div>';
		}
	}

	/**
	 * Header bar wrapper
	 * @return void
	 */
	public function header_bar_wrapper() {
		echo '<section class="hs-header-bar">';
	}

	/**
	 * Header bar wrapper (closing tag)
	 * @return void
	 */
	public function header_bar_wrapper_close() {
		echo '</section>';
	}

	/**
	 * Primary navigation wrapper
	 * @return void
	 */
	public function primary_navigation_wrapper() {
		echo '<section class="hs-primary-navigation">';
	}

	/**
	 * Primary navigation wrapper (closing tag)
	 * @return void
	 */
	public function primary_navigation_wrapper_close() {
		echo '</section>';
	}

	/**
	 * Homepage content wrapper
	 * @return void
	 */
	public function homepage_content_wrapper() {
		if ( is_page_template( 'template-homepage.php' ) ) {
			echo '<section class="homestore-homepage-content">';
		}
	}

	/**
	 * Homepage content wrapper (closing tag)
	 * @return void
	 */
	public function homepage_content_wrapper_close() {
		if ( is_page_template( 'template-homepage.php' ) ) {
			echo '</section>';
		}
	}

	/**
	 * Footer bar wrapper
	 * @return void
	 */
	public function footer_bar_wrapper() {
		if ( class_exists( 'Storefront_Footer_Bar' ) && is_active_sidebar( 'footer-bar-1' ) ) {
			echo '<div class="homestore-footer-bar-wrapper col-full">';
		}
	}

	/**
	 * Footer bar wrapper (closing tag)
	 * @return void
	 */
	public function footer_bar_wrapper_close() {
		if ( class_exists( 'Storefront_Footer_Bar' ) && is_active_sidebar( 'footer-bar-1' ) ) {
			echo '</div><!-- .homestore-footer-bar-wrapper -->';
		}
	}

	/**
	 * Display single product title
	 * @return void
	 */
	public function single_product_title() { ?>
		<div id="hero">
			<img src="<?php echo get_post_meta( get_the_ID(), 'header_img_url', true ); ?>" class="product-page-hero">
		</div>
		<!-- <h1 itemprop="name" class="product_title entry-title"><?php the_title(); ?></h1>--><?php
	}

	/**
	 * Renders the tabbed sections
	 * @return void
	 */
	public static function section_tabs() {
		?>
			<ul class="homestore-section-tabs"></ul>
			<div id="homestore-section-tabs-ref" class="clear"></div>
		<?php
	}

}

endif;

return new Homestore_Template();
