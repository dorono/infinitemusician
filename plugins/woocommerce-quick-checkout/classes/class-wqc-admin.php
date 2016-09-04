<?php

/**
 *  Quick Checkout Admin Class
 *
 * @description: Displays options in a custom WooCommerce tab
 * @since      :
 * @created    : 2/4/14
 */
class WC_Quick_Checkout_Admin {


	public function __construct() {

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ), 999 );
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'quick_checkout_admin_tab_label' ), 30 );
		add_action( 'woocommerce_settings_tabs_quick_checkout', array( $this, 'admin_panel' ) );
		add_action( 'woocommerce_update_options_quick_checkout', array( $this, 'process_admin_options' ) );

		// Settings Link for Plugin page
		add_filter( 'plugin_action_links', array( $this, 'add_action_link' ), 9, 2 );

		//Settings Page for License
		add_action( 'admin_menu', array( $this, 'register_quick_checkout_license_submenu_page' ) );


		//Enqueue Scripts/Styles
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

		//Include custom meta boxes and fields
		//https://github.com/WebDevStudios/Custom-Metaboxes-and-Fields-for-WordPress/wiki/Basic-Usage
		// Initialize the metabox class
		$this->single_product_enable_control = get_option( 'woocommerce_quick_checkout_product_metabox' );

		//Does user want control over individual products?
		if ( $this->single_product_enable_control == 'yes' ) {
			//Create metabox
			add_action( 'init', array( $this, 'quick_checkout_initialize_cmb_meta_boxes' ), 9999 );
			add_filter( 'cmb_meta_boxes', array( $this, 'quick_checkout_metaboxes' ) );

			//Enqueue Scripts/Styles
			add_action( 'admin_enqueue_scripts', array( $this, 'metabox_admin_enqueue' ) );

		}

	}


	/**
	 * Add License Subpage
	 */
	function register_quick_checkout_license_submenu_page() {
		add_submenu_page(
			'options-general.php', //The parent page of this menu
			__( 'Quick Checkout', 'wqc' ), //The Menu Title
			__( 'Quick Checkout', 'wqc' ), //The Page Title
			'manage_options', // The capability required for access to this item
			'quick-checkout-license', //the slug to use for the page in the URL
			array( $this, 'quick_checkout_license_submenu_page_options' ) //The function to call to render the page
		);
	}

	/**
	 * Add the License Metabox
	 */
	function quick_checkout_license_submenu_page_options() {
		?>

		<div class="wrap">
			<h2 class="qc-icon"><?php _e( 'Quick Checkout License', 'wqc' ); ?></h2>

			<p><?php echo sprintf( __( 'This settings page is where you manage your license for Quick Checkout. If you are looking for the plugin options page <a href="%s">click here</a> to be taken to that page.', 'wqc' ), 'admin.php?page=wc-settings&tab=quick_checkout' ); ?></p>

			<?php
			//Output Licensing Fields
			if ( class_exists( 'Quick_Checkout_Licence' ) ) {
				WC_Quick_Checkout()->licence->edd_wordimpress_license_page();
			} ?>

		</div>

		<?php
	}


	/**
	 * Initialize the metabox class
	 */
	function quick_checkout_initialize_cmb_meta_boxes() {
		if ( ! class_exists( 'cmb_Meta_Box' ) ) {
			require_once( WQC_PLUGIN_PATH . '/includes/metaboxes/init.php' );

		}
	}

	/**
	 * Create metaboxes
	 */
	function quick_checkout_metaboxes() {

		$global_option = get_option( 'woocommerce_quick_checkout_product' );
		$prefix        = 'qc_'; // Prefix for all fields
		$fields        = array();

		//Is global option enabled
		if ( $global_option == 'yes' ) {

			//Display Info to user
			$fields[] = $fields + array(
					'name' => esc_attr__( 'Global Options Enabled', 'wqc' ),
					'desc' => esc_attr__( 'Quick Checkout global settings have been enabled on the plugin settings page. All options set within this metabox will override global settings.', 'wqc' ),
					'type' => 'title',
					'id'   => $prefix . 'global_info'
				);


		} //Individual Options

		//Display disable option
		$fields[] = $fields + array(
				'name' => esc_attr__( 'Enable', 'wqc' ),
				'desc' => esc_attr__( 'Enable Quick Checkout for this product', 'wqc' ),
				'id'   => $prefix . 'enable_checkbox',
				'type' => 'checkbox',
			);

		//Display disable option
		$fields[] = $fields + array(
				'name' => esc_attr__( 'Disable', 'wqc' ),
				'desc' => 'Disable Quick Checkout for this product on all pages',
				'id'   => $prefix . 'disable_checkbox',
				'type' => 'checkbox',
			);

		$fields[] = $fields + array(
				'name'    => esc_attr__( 'Clear Cart ', 'wqc' ),
				'desc'    => esc_attr__( 'Clear user\'s cart before opening checkout', 'wqc' ),
				'id'      => $prefix . 'single_cart_action',
				'type'    => 'checkbox',
				'default' => get_option( 'woocommerce_quick_checkout_product_cart_action' )
			);
		$fields[] = $fields + array(
				'name'    => esc_attr__( 'Image Hover ', 'wqc' ),
				'desc'    => esc_attr__( 'Show Quick Checkout button on product image hover; only supports simple products', 'wqc' ),
				'id'      => $prefix . 'single_product_image_button',
				'type'    => 'radio_inline',
				'default' => get_option( 'woocommerce_quick_checkout_product_image_button' ),
				'options' => array(
					array( 'name' => 'Yes', 'value' => 'yes' ),
					array( 'name' => 'No', 'value' => 'no' ),
				)
			);

		$fields[] = $fields + array(
				'name'    => esc_attr__( 'Display Option', 'wqc' ),
				'desc'    => '',
				'id'      => $prefix . 'single_display_option',
				'type'    => 'select',
				'default' => get_option( 'woocommerce_quick_checkout_product_button_display' ),
				'options' => array(
					array( 'name' => esc_attr__( 'Select option to customize...', 'wqc' ), 'value' => 'default' ),
					array(
						'name'  => esc_attr__( 'Add Quick Checkout button AFTER the Add to Cart button', 'wqc' ),
						'value' => 'after'
					),
					array(
						'name'  => esc_attr__( 'Add Quick Checkout button BEFORE the Add to Cart button', 'wqc' ),
						'value' => 'before'
					),
					array(
						'name'  => esc_attr__( 'Replace the Add to Cart button with Quick Checkout button', 'wqc' ),
						'value' => 'replace'
					)
				)
			);

		$fields[] = $fields + array(
				'name'    => esc_attr__( 'Checkout Action', 'wqc' ),
				'desc'    => '',
				'id'      => $prefix . 'single_product_action',
				'type'    => 'select',
				'default' => get_option( 'woocommerce_quick_checkout_product_button_display' ),
				'options' => array(
					array( 'name' => esc_attr__( 'Select option to customize...', 'wqc' ), 'value' => 'default' ),
					array(
						'name'  => esc_attr__( 'Open checkout in a responsive lightbox', 'wqc' ),
						'value' => 'lightbox'
					),
					array(
						'name'  => esc_attr__( 'Reveal checkout within product post content', 'wqc' ),
						'value' => 'reveal'
					),
				)
			);

		$fields[] = $fields + array(
				'name'    => esc_attr__( 'Checkout position', 'wqc' ),
				'desc'    => esc_attr__( 'This controls where the checkout will be revealed once the user chooses to checkout using the quick cart button. This only applies to when the "Reveal checkout on product post" option is enabled.', 'wqc' ),
				'id'      => $prefix . 'single_product_checkout_display_position',
				'type'    => 'select',
				'default' => get_option( 'woocommerce_quick_checkout_product_checkout_display_position' ),
				'options' => array(
					array(
						'name'  => esc_attr__( 'After single product', 'wqc' ),
						'value' => 'woocommerce_after_single_product'
					),
					array(
						'name'  => esc_attr__( 'Before single product', 'wqc' ),
						'value' => 'woocommerce_before_single_product'
					),
					array(
						'name'  => esc_attr__( 'After single product summary', 'wqc' ),
						'value' => 'woocommerce_after_single_product_summary'
					),
					array(
						'name'  => esc_attr__( 'Before single product summary', 'wqc' ),
						'value' => 'woocommerce_before_single_product_summary'
					),
					array(
						'name'  => esc_attr__( 'After main content', 'wqc' ),
						'value' => 'woocommerce_after_main_content'
					),
					array(
						'name'  => esc_attr__( 'Before main content', 'wqc' ),
						'value' => 'woocommerce_before_main_content'
					),
					array(
						'name'  => esc_attr__( 'After add to cart form', 'wqc' ),
						'value' => 'woocommerce_after_add_to_cart_form'
					),
					array(
						'name'  => esc_attr__( 'Within product summary', 'wqc' ),
						'value' => 'woocommerce_single_product_summary'
					),
				)
			);

		$fields[] = $fields + array(
				'name'    => esc_attr__( 'Button Text', 'wqc' ),
				'desc'    => '',
				'std'     => esc_attr__( 'Buy Now', 'wqc' ),
				'id'      => $prefix . 'single_product_button_text',
				'default' => get_option( 'woocommerce_quick_checkout_product_button_text' ),
				'type'    => 'text_medium'
			);

		//Create Metabox
		$meta_boxes['qc_metabox'] = array(
			'id'         => 'qc_metabox',
			'title'      => esc_attr__( 'Quick Checkout Options', 'wqc' ),
			'pages'      => array( 'product' ), // post type
			'context'    => 'normal',
			'priority'   => 'default',
			'show_names' => true, // Show field names on the left
			'fields'     => $fields
		);

		return apply_filters( 'quick_checkout_meta_boxes', $meta_boxes );

	}


	/**
	 * Admin Single Product Metabox Scripts
	 */
	function metabox_admin_enqueue() {
		global $post_type;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		//Only load scripts on Product CPT as to not slow down WP admin
		if ( $post_type == 'product' ) {

			wp_enqueue_script( 'quick_checkout_admin_metabox_js', WQC_PLUGIN_URL . '/assets/js/quick-checkout-admin-product' . $suffix . '.js', array( 'jquery' ) );
			wp_enqueue_style( 'quick_checkout_admin_metabox_css', WQC_PLUGIN_URL . '/assets/css/quick-checkout-admin-product' . $suffix . '.css' );

		}

	}

	/**
	 * Admin WooCommerce Quick Checkout Options Screen Scripts
	 */
	function admin_enqueue( $hook ) {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( $hook == 'woocommerce_page_woocommerce_settings' || $hook == 'woocommerce_page_wc-settings' ) {

			wp_enqueue_script( 'quick_checkout_admin_js', WQC_PLUGIN_URL . '/assets/js/quick-checkout-admin' . $suffix . '.js' );
			wp_enqueue_style( 'quick_checkout_admin_css', WQC_PLUGIN_URL . '/assets/css/quick-checkout-admin' . $suffix . '.css' );

		}

	}


	/**
	 * Helper Link
	 *
	 * Adds link to plugin listing page to settings
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return mixed
	 */
	public function add_action_link( $links, $file ) {

		if ( $file == WQC_PLUGIN_BASE ) {

			$settings_link = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=quick_checkout' ) . '" title="' . __( 'Go to the settings page', 'wqc' ) . '">' . __( 'Settings', 'wqc' ) . '</a>';

			array_unshift( $links, $settings_link );
		}

		return $links;
	}


	/**
	 * Admin Notices
	 *
	 * @description: Display a notice if there's no checkout page set.
	 */
	public function admin_notices() {
		if ( wc_get_checkout_url() ) {
			return;
		}

		if ( 'settings_page_quick-checkout-license' != get_current_screen()->base && 'woocommerce' != get_current_screen()->parent_base ) {
			return;
		}

		?>
		<div class="notice notice-error is-dismissible">
			<p><?php echo sprintf( __( 'WooCommerce Quick Checkout requires that a checkout page be set. <a href="%s">Set one now</a> to use the plugin!', 'wqc' ), esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout#s2id_woocommerce_cart_page_id' ) ) ); ?></p>
		</div>
		<?php
	}


	/**
	 * Admin Footer
	 *
	 * @description: Hide's Woo extension advertising on the WC tab
	 *
	 */
	public function admin_footer() {
		if ( ! isset( $_GET['tab'] ) || 'quick_checkout' != $_GET['tab'] ) {
			return;
		}
		?>
		<style>
			#woocommerce_extensions {
				display: none !important;
			}
		</style>
		<?php

	}


	/**
	 * Admin Tab Label
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	public static function quick_checkout_admin_tab_label( $settings ) {
		$settings['quick_checkout'] = esc_attr__( 'Quick Checkout', 'wqc' );

		return $settings;
	}


	/**
	 * Add Settings Fields
	 */
	public static function add_settings_fields() {
		global $woocommerce_settings;

		$woocommerce_settings['quick_checkout'] = apply_filters( 'woocommerce_quick_checkout_settings', array(

			//SHOP OPTIONS SECTION
			array(
				'name' => esc_attr__( 'Shop Page Display Options', 'wqc' ),
				'type' => 'title',
				'desc' => esc_attr__( 'Manage how Quick Checkout displays on your website\'s shop page. The shop page displays the entire catalog of products.', 'wqc' ),
				'id'   => 'woocommerce_quick_checkout_display_options_shop'
			),
			array(
				'title'           => esc_attr__( 'Shop Page', 'wqc' ),
				'desc'            => esc_attr__( 'Enable Quick Checkout on the WooCommerce shop page', 'wqc' ),
				'id'              => 'woocommerce_quick_checkout_shop',
				'default'         => 'no',
				'type'            => 'checkbox',
				'show_if_checked' => 'option',
				'desc_tip'        => __( 'Check to enable the Quick Checkout option for WooCommerce shop pages.', 'wqc' ),
				'checkboxgroup'   => 'start',
			),
			array(
				'desc'            => esc_attr__( 'Clear user\'s cart before opening checkout', 'wqc' ),
				'id'              => 'woocommerce_quick_checkout_shop_cart_action',
				'default'         => 'no',
				'type'            => 'checkbox',
				'checkboxgroup'   => '',
				'show_if_checked' => 'yes',
			),
			array(
				'title'         => esc_attr__( 'Quick Checkout Display', 'wqc' ),
				'desc'          => esc_attr__( 'Display Quick Checkout button on product image hover', 'wqc' ),
				'desc_tip'      => esc_attr__( 'Shows a Quick Checkout button upon product image hover; only supports the simple product type', 'wqc' ),
				'id'            => 'woocommerce_quick_checkout_shop_image_hover',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start'
			),
			array(
				'desc'          => esc_attr__( 'Reveal Quick Checkout link after product added to cart', 'wqc' ),
				'id'            => 'woocommerce_quick_checkout_shop_cart_reveal',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'desc_tip'      => sprintf( esc_attr__( 'Enable AJAX add to cart buttons on archives option must be enabled in <a href="%s" target="_blank">WooCommerce Product Settings</a>.', 'wqc' ), 'admin.php?page=wc-settings&tab=quick_checkout' ),
				'checkboxgroup' => 'end'
			),
			array(
				'title'   => esc_attr__( 'Hover Button Text', 'wqc' ),
				'desc'    => '',
				'id'      => 'woocommerce_quick_checkout_shop_cart_button_text',
				'default' => esc_attr__( 'Buy Now', 'wqc' ),
				'type'    => 'text',
				'css'     => 'min-width:200px;',
			),
			array(
				'title'   => esc_attr__( 'Reveal Link Text', 'wqc' ),
				'desc'    => '',
				'id'      => 'woocommerce_quick_checkout_shop_cart_link_text',
				'default' => esc_attr__( 'Checkout Now', 'wqc' ),
				'type'    => 'text',
				'css'     => 'min-width:200px;',
			),
			array(
				'title'    => esc_attr__( 'Shop Action', 'wqc' ),
				'id'       => 'woocommerce_quick_checkout_shop_action',
				'class'    => 'qc_shop_action',
				'default'  => 'lightbox',
				'type'     => 'select',
				'desc_tip' => esc_attr__( 'This option is important as it will affect how Quick Checkout functions on the shop page.', 'wqc' ),
				'options'  => array(
					'lightbox' => esc_attr__( 'Open checkout in a responsive lightbox', 'wqc' ),
					'reveal'   => esc_attr__( 'Reveal checkout on shop page', 'wqc' )
				),
			),
			array(
				'title'    => esc_attr__( 'Checkout Position', 'wqc' ),
				'desc'     => esc_attr__( 'This controls where the checkout will be revealed once the user chooses to checkout using the Quick Checkout button. This only applies to when the "Reveal checkout on shop page" option is enabled.', 'wqc' ),
				'id'       => 'woocommerce_quick_checkout_checkout_display_position',
				'css'      => 'min-width:150px;',
				'default'  => 'woocommerce_before_shop_loop',
				'type'     => 'select',
				'options'  => array(
					'woocommerce_before_shop_loop' => esc_attr__( 'Above shop loop', 'wqc' ),
					'woocommerce_after_shop_loop'  => esc_attr__( 'Below shop loop', 'wqc' ),
				),
				'desc_tip' => true,
			),
			array( 'type' => 'sectionend', 'id' => 'woocommerce_quick_checkout_display_options_shop' ),
			//INDIVIDUAL PRODUCT POSTS OPTIONS SECTION
			array(
				'name' => esc_attr__( 'Individual Product Post Display Options', 'wqc' ),
				'type' => 'title',
				'desc' => esc_attr__( 'Provides full control over Quick Checkout on single product posts. Options set on the individual product post level override global settings.', 'wqc' ),
				'id'   => 'woocommerce_quick_checkout_display_options_product_single'
			),
			array(
				'title'           => esc_attr__( 'Single Products', 'wqc' ),
				'desc'            => esc_attr__( 'Enable Quick Checkout Metabox for the WooCommerce Product Type', 'wqc' ),
				'id'              => 'woocommerce_quick_checkout_product_metabox',
				'default'         => 'no',
				'type'            => 'checkbox',
				'show_if_checked' => 'option',
				'desc_tip'        => sprintf( esc_attr__( 'Check to enable the Quick Checkout metabox for WooCommerce <a href="%s">single product posts</a>.', 'wqc' ), 'edit.php?post_type=product' ),
				'checkboxgroup'   => 'start',
			),
			array( 'type' => 'sectionend', 'id' => 'woocommerce_quick_checkout_display_options_shop' ),
			//GLOBAL OPTIONS SECTION
			array(
				'name' => esc_attr__( 'Global Product Post Display Options', 'wqc' ),
				'type' => 'title',
				'desc' => esc_attr__( 'Manage how Quick Checkout displays globally for WooCommerce single product posts. Individual product Quick Checkout settings will override global settings.', 'wqc' ),
				'id'   => 'woocommerce_quick_checkout_display_options_product_global'
			),
			array(
				'title'           => esc_attr__( 'All Products', 'wqc' ),
				'desc'            => esc_attr__( 'Enable Quick Checkout globally for individual product posts', 'wqc' ),
				'id'              => 'woocommerce_quick_checkout_product',
				'default'         => 'no',
				'type'            => 'checkbox',
				'show_if_checked' => 'option',
				'desc_tip'        => esc_attr__( 'Check to enable Quick Checkout on all WooCommerce single product posts.', 'wqc' ),
				'checkboxgroup'   => 'start',
			),
			array(
				'desc'            => esc_attr__( 'Display Quick Checkout on Related Products', 'wqc' ),
				'id'              => 'woocommerce_quick_checkout_related_products',
				'default'         => 'no',
				'type'            => 'checkbox',
				'checkboxgroup'   => '',
				'show_if_checked' => 'yes',
			),
			array(
				'desc'            => esc_attr__( 'Clear user\'s cart before opening checkout', 'wqc' ),
				'id'              => 'woocommerce_quick_checkout_product_cart_action',
				'default'         => 'no',
				'type'            => 'checkbox',
				'checkboxgroup'   => '',
				'show_if_checked' => 'yes',
			),
			array(
				'title'    => esc_attr__( 'Quick Checkout Display', 'wqc' ),
				'id'       => 'woocommerce_quick_checkout_product_button_display',
				'class'    => 'qc_product_button_display',
				'default'  => 'after',
				'type'     => 'select',
				'desc_tip' => esc_attr__( 'This option is important as it will affect how the quick cart functions on the shop page.', 'wqc' ),
				'options'  => array(
					'after'   => esc_attr__( 'Add Quick Checkout button after the Add to Cart button ', 'wqc' ),
					'before'  => esc_attr__( 'Add Quick Checkout button before the Add to Cart button ', 'wqc' ),
					'replace' => esc_attr__( 'Replace the Add to Cart button with Quick Checkout button', 'wqc' ),
				),
			),
			array(
				'desc'    => esc_attr__( 'Display Quick Checkout button on product image hover', 'wqc' ),
				'id'      => 'woocommerce_quick_checkout_product_image_button',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'    => esc_attr__( 'Quick Checkout Action', 'wqc' ),
				'id'       => 'woocommerce_quick_checkout_product_action',
				'class'    => 'qc_product_action',
				'default'  => 'lightbox',
				'type'     => 'select',
				'desc_tip' => esc_attr__( 'This option is important as it will affect how the quick checkout functions when a user clicks it on the product post.', 'wqc' ),
				'options'  => array(
					'lightbox' => esc_attr__( 'Open checkout in a WooCommerce lightbox', 'wqc' ),
					'reveal'   => esc_attr__( 'Reveal checkout within product post content', 'wqc' )
				),
			),
			array(
				'title'    => esc_attr__( 'Checkout position', 'wqc' ),
				'desc'     => esc_attr__( 'This controls where the checkout will be revealed once the user chooses to checkout using the quick cart button. This only applies to when the "Reveal checkout on product post" option is enabled.', 'wqc' ),
				'id'       => 'woocommerce_quick_checkout_product_checkout_display_position',
				'css'      => 'min-width:150px;',
				'default'  => 'woocommerce_after_single_product_summary',
				'type'     => 'select',
				'options'  => array(
					'woocommerce_after_single_product'          => esc_attr__( 'After single product', 'wqc' ),
					'woocommerce_before_single_product'         => esc_attr__( 'Before single product', 'wqc' ),
					'woocommerce_after_single_product_summary'  => esc_attr__( 'After single product summary', 'wqc' ),
					'woocommerce_before_single_product_summary' => esc_attr__( 'Before single product summary', 'wqc' ),
					'woocommerce_after_main_content'            => esc_attr__( 'After main content', 'wqc' ),
					'woocommerce_before_main_content'           => esc_attr__( 'Before main content', 'wqc' ),
					'woocommerce_after_add_to_cart_form'        => esc_attr__( 'After add to cart form', 'wqc' ),
					'woocommerce_single_product_summary'        => esc_attr__( 'Within product summary', 'wqc' ),
				),
				'desc_tip' => true,
			),
			array(
				'title'   => esc_attr__( 'Product Button Text', 'wqc' ),
				'desc'    => '',
				'id'      => 'woocommerce_quick_checkout_product_button_text',
				'default' => esc_attr__( 'Buy Now', 'wqc' ),
				'type'    => 'text',
				'css'     => 'min-width:300px;',
			),
			array( 'type' => 'sectionend', 'id' => 'woocommerce_quick_checkout_display_options_product_global' ),
			//SHORTCODES SECTION
			array(
				'name' => esc_attr__( 'Shortcode Display Options', 'wqc' ),
				'type' => 'title',
				'desc' => esc_attr__( 'WooCommerce includes a number of shortcodes that many page builders use to output products. If you are using a popular theme such as Divi or X theme then be sure to enable support for shortcodes if you wish to use Quick Checkout. ', 'wqc' ) . sprintf( esc_attr__( 'The following shortcodes are supported by Quick Checkout. Read more about %1$sWooCommerce shortcodes%2$s. If enabled, the shortcodes will display a "Buy Now" button display on hover which opens Quick Checkout in a modal window upon click.', 'wqc' ), '<a href="http://docs.woothemes.com/document/woocommerce-shortcodes/" target="_blank" class="new-window">', '</a>' ),
				'id'   => 'woocommerce_quick_checkout_display_options_shortcodes',
			),
			array(
				'title'         => esc_attr__( 'Display for Shortcodes', 'wqc' ),
				'desc'          => esc_attr__( 'Recent Products - Example: <code>[recent_products per_page="12" columns="4"]</code>', 'wqc' ),
				'id'            => 'woocommerce_quick_checkout_support_shortcode_recent_products',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start'
			),
			array(
				'desc'          => esc_attr__( 'Featured Products - Example:', 'wqc' ) . ' <code>[featured_products per_page="12" orderby="menu_order"]</code>',
				'id'            => 'woocommerce_quick_checkout_support_shortcode_featured_products',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => ''
			),
			array(
				'desc'          => esc_attr__( 'Best Selling Products - Example:', 'wqc' ) . ' <code>[best_selling_products per_page="12" orderby="menu_order"]</code>',
				'id'            => 'woocommerce_quick_checkout_support_shortcode_best_selling_products',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => ''
			),
			array(
				'desc'          => esc_attr__( 'Top Rated Products - Example:', 'wqc' ) . ' <code>[top_rated_products per_page="12" orderby="menu_order"]</code>',
				'id'            => 'woocommerce_quick_checkout_support_shortcode_top_rated_products',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => ''
			),
			array(
				'desc'          => esc_attr__( 'Sale Products - Example: ', 'wqc' ) . '<code>[sale_products per_page="12" orderby="menu_order"]</code>',
				'id'            => 'woocommerce_quick_checkout_support_shortcode_sale_products',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end'
			),
			array( 'type' => 'sectionend', 'id' => 'woocommerce_quick_checkout_display_options_product_global' ),


		) );

	}


	/**
	 * Output admin panel
	 */
	public static function admin_panel() {

		if ( ! current_user_can( 'manage_options' ) ) {

			echo '<p>' . esc_attr__( 'You do not have sufficient permissions to access this page.', 'wqc' ) . '</p>';

		} else {

			//Admin Panel
			global $woocommerce_settings; ?>

			<div class="quick-checkout-wrap">
				<div class="quick-checkout-admin-intro">

					<div class="quick-checkout-logo"></div>

					<p><?php esc_attr_e( 'Welcome to Quick Checkout for WooCommerce! This plugin speeds up the WooCommerce checkout process by allowing users to purchase products on virtually any page with fewer clicks and no page reload. Insert checkout forms directly on single product posts, on the product listing page (also called the catalog/shop page), as well as any other page or post.', 'wqc' ); ?></p>

					<p><?php echo sprintf( esc_attr__( 'For more information and instructions please visit %1$sQuick Checkout Documentation%3$s. We appreciate your purchase and stand by supporting our product. If you require %2$ssupport%3$s please first consult the documentation and if your answer is not found then feel free to post a support topic.', 'wqc' ), '<a href="https://wordimpress.com/documentation/woocommerce-quick-checkout/" target="_blank" class="new-window">', '<a href="https://wordimpress.com/support/" target="_blank" class="new-window">', '</a>' ); ?></p>

				</div>

				<?php
				/**
				 * Woo Options
				 */
				self::add_settings_fields();

				woocommerce_admin_fields( $woocommerce_settings['quick_checkout'] );

				?>

			</div><!--/.quick-checkout-wrap -->
			<?php

		}
	}


	/**
	 * Process Admin Options
	 */
	public static function process_admin_options() {
		global $woocommerce_settings;
		self::add_settings_fields();
		woocommerce_update_options( $woocommerce_settings['quick_checkout'] );

	}
}

return new WC_Quick_Checkout_Admin();