<?php

/**
 * Quick_Checkout_Shortcode_Generator class.
 */
class Quick_Checkout_Shortcode_Generator {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_head', array( $this, 'add_shortcode_button' ), 20 );
		add_filter( 'tiny_mce_version', array( $this, 'refresh_mce' ), 20 );
		add_filter( 'mce_external_languages', array( $this, 'add_tinymce_lang' ), 20, 1 );

		// Tiny MCE button icon
		add_action( 'admin_head', array( __CLASS__, 'set_tinymce_button_icon' ) );

		add_action( 'wp_ajax_wqc_shortcode_iframe', array( $this, 'wqc_shortcode_iframe' ), 9 );
	}

	/**
	 * Add a button for the GPR shortcode to the WP editor.
	 */
	public function add_shortcode_button() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// check if WYSIWYG is enabled
		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_tinymce_plugin' ), 10 );
			add_filter( 'mce_buttons', array( $this, 'register_shortcode_button' ), 10 );
		}
	}

	/**
	 * Add TinyMCE language function.
	 *
	 * @param array $arr
	 *
	 * @return array
	 */
	public function add_tinymce_lang( $arr ) {
		$arr['wqc_shortcode_button'] = WQC_PLUGIN_URL . '/assets/js/editor_plugin_lang.php';

		return $arr;
	}

	/**
	 * Register the shortcode button.
	 *
	 * @param array $buttons
	 *
	 * @return array
	 */
	public function register_shortcode_button( $buttons ) {

		array_push( $buttons, '|', 'wqc_shortcode_button' );

		return $buttons;
	}

	/**
	 * Add the shortcode button to TinyMCE
	 *
	 * @param array $plugin_array
	 *
	 * @return array
	 */
	public function add_shortcode_tinymce_plugin( $plugin_array ) {

		$plugin_array['wqc_shortcode_button'] = WQC_PLUGIN_URL . '/assets/js/editor_plugin.js';

		return $plugin_array;
	}

	/**
	 * Force TinyMCE to refresh.
	 *
	 * @param int $ver
	 *
	 * @return int
	 */
	public function refresh_mce( $ver ) {
		$ver += 3;

		return $ver;
	}

	/**
	 * Adds admin styles for setting the tinymce button icon
	 */
	public static function set_tinymce_button_icon() {
		?>
		<style>
			i.mce-i-gpr {
				font: 400 20px/1 dashicons;
				padding: 0;
				vertical-align: top;
				speak: none;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				margin-left: -2px;
				padding-right: 2px
			}

			#wqc_shortcode_dialog-body {
				background: #F1F1F1;
			}

			.gpr-shortcode-submit {
				margin: 0 -15px;
				position: fixed;
				bottom: 0;
				background: #FFF;
				width: 100%;
				padding: 15px;
				border-top: 1px solid #DDD;
			}

			div.place-id-set {
				clear: both;
				float: left;
				width: 100%;
			}

		</style>
	<?php
	}

	/**
	 * Display the contents of the iframe used when the GPR Shortcode Generator is clicked
	 * TinyMCE button is clicked.
	 *
	 * @param int $ver
	 *
	 * @return int
	 */
	public static function wqc_shortcode_iframe() {

		global $wp_scripts;
		set_current_screen( 'wqc' );
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		//Tipsy tooltips
		wp_enqueue_script( 'wqc_shortcode_admin_tipsy', WQC_PLUGIN_URL . '/assets/js/wqc-tipsy' . $suffix . '.js', array( 'jquery' ) );
		//Shortcode Generator Specific JS
		wp_enqueue_script( 'wqc_shortcode_admin_generator', WQC_PLUGIN_URL . '/assets/js/shortcode-iframe' . $suffix . '.js', array( 'jquery', 'wc-enhanced-select' ) );
		//Styles
		$wc_assets_path          = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
		wp_enqueue_style( 'select2', $wc_assets_path . 'css/select2.css' );
		wp_enqueue_style( 'wqc_widget_admin_tipsy', WQC_PLUGIN_URL . '/assets/css/wqc-tipsy' . $suffix . '.css', array('select2') );

		iframe_header();
		include WQC_PLUGIN_PATH . '/includes/options.php';
		include WQC_PLUGIN_PATH . '/views/iframe.php';


		iframe_footer();
		exit();
	}
}

new Quick_Checkout_Shortcode_Generator();
