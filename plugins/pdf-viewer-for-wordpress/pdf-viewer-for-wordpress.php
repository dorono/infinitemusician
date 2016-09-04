<?php
/*
 * Plugin Name: PDF viewer for WordPress
 * Plugin URI: https://themencode.com/plugins/pdf-viewer-for-wordpress/
 * Description: A Simple plugin to display Your site's pdf files with a nice viewer using pdfjs script from mozilla.
 * Version: 5.3
 * Author: ThemeNcode
 * Author URI: https://themencode.com
*/
define('PVFW_PLUGIN_NAME','PDF viewer for WordPress');
define('PVFW_PLUGIN_DIR', 'pdf-viewer-for-wordpress');
define('WEB_DIR', 'pdf-viewer-for-wordpress/web');
define('BUILD_DIR', 'pdf-viewer-for-wordpress/build');
define('RESOURCES_DIR', 'pdf-viewer-for-wordpress/tnc-resources');
include "admin/tnc-pdf-viewer-options.php";
//get_settings
$auto_add       	= get_option('auto_add_link');
$show_social   		= get_option('ss_opt_name');
function tnc_pdf_autolink(){
	$viewer_url = plugins_url()."/".WEB_DIR."/viewer.php?file=";
	$tnc_al_get_viewer_page_id = get_option( 'tnc_pdf_viewer_page_id', false );
 	$viewer_url = get_permalink( $tnc_al_get_viewer_page_id).'?file=';
?>
	<script type="text/javascript">
		jQuery(document).ready(function() {		
			var gethost = new RegExp(location.host);
			jQuery("a[href$='.pdf']").each(function() {
				if(gethost.test(jQuery(this).attr('href'))){
			   		var _href = jQuery(this).attr("href");
			   		jQuery(this).attr("href", '<?php echo $viewer_url; ?>'+ _href);
			   	} else {
			   		// Do Nothing
			   	}
			});
		});
	</script>
<?php }
function tnc_pdf_autoiframe(){
	$viewer_url = plugins_url()."/".WEB_DIR."/viewer.php?file=";
	$tnc_ai_get_viewer_page_id = get_option( 'tnc_pdf_viewer_page_id', false );
 	$viewer_url = get_permalink( $tnc_ai_get_viewer_page_id).'?file=';
	$auto_iframe_width	= get_option('auto_iframe_width');
	$auto_iframe_height	= get_option('auto_iframe_height');
?>
	<script type="text/javascript">
		jQuery(document).ready(function() {		
			var gethost = new RegExp(location.host);
			jQuery("a[href$='.pdf']").each(function() {
				if(gethost.test(jQuery(this).attr('href'))){
			   		var _href = jQuery(this).attr("href");
			   		jQuery(this).replaceWith("<iframe width='<?php echo $auto_iframe_width; ?>' height='<?php echo $auto_iframe_height; ?>' src='<?php echo $viewer_url ?>" + _href +"'></iframe>");
			   	} else {
			   		// do nothing
			   	}
			});
		});
	</script>
<?php }
if($auto_add == 'auto_iframe'){
	add_action('wp_footer', 'tnc_pdf_autoiframe');
} elseif($auto_add == 'auto_link') {
	add_action('wp_footer', 'tnc_pdf_autolink');
} else {
}
//Autolink Blank Target
function themencode_autolink_target(){
	$autolink_setting = get_option('tnc_link_target', '_blank');
    $output  	 = '<script type="text/javascript">';
    $output 	.=    "jQuery(function($) {";
    $output 	.=  "jQuery('a[href$=\".pdf\"]').attr('target', '".$autolink_setting."');";
    $output    	.= "});"; 
    $output 	.= "</script>";
    echo $output;
}
add_action('wp_footer', 'themencode_autolink_target');
// Iframe Responsive Fix ** Added in 3.0
function tnc_pdf_iframe_responsive_fix(){
	echo "<style type='text/css'>
		iframe{
			max-width: 100%;
		}
	</style>";
}
add_action( 'wp_head', 'tnc_pdf_iframe_responsive_fix' );
add_action( 'admin_enqueue_scripts', 'tnc_enqueue_color_picker' );
function tnc_enqueue_color_picker( $hook_suffix ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'tnc-cp-script-handle', plugins_url('tnc-pdf-scripts.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}
function tnc_pdf_admin_css(){
	echo '<link rel="stylesheet" href="'.plugins_url().'/'.PVFW_PLUGIN_DIR.'/tnc-resources/admin-css.css" />';
}
add_action('admin_head', 'tnc_pdf_admin_css');
include "includes/tnc_shortcodes.php";
include "includes/tinymce/button.php";
require('update-notifier.php');
class TncRegisterPT{
		/**
		* A reference to an instance of this class.
		*/
		private static $instance;
		/**
		* The array of templates that this plugin tracks.
		*/
		protected $templates;
		/**
		* Returns an instance of this class.
		*/
		public static function get_instance() {
			if( null == self::$instance ){
				self::$instance = new TncRegisterPT();
			}
			return self::$instance;
		}
		/**
		* Initializes the plugin by setting filters and administration functions.
		*/
		private function __construct() {
			$this->templates = array();
			// Add a filter to the attributes metabox to inject template into the cache.
			add_filter(
				'page_attributes_dropdown_pages_args',
				array( $this, 'register_tnc_pdf_templates' )
			);
				// Add a filter to the save post to inject out template into the page cache
			add_filter(
				'wp_insert_post_data',
				array( $this, 'register_tnc_pdf_templates' )
			);
			// Add a filter to the template include to determine if the page has our
			// template assigned and return it's path
			add_filter(
				'template_include',
				array( $this, 'view_tnc_pdf_template')
			);
			// Add your templates to this array.
			$this->templates = array(
				'tnc-pdf-viewer.php'				=> 'PDF Viewer Template',
				'tnc-pdf-viewer-shortcode.php'		=> 'PDF Viewer Shortcode Template',
				);
		}
		/**
		* Adds our template to the pages cache in order to trick WordPress
		* into thinking the template file exists where it doens't really exist.
		*
		*/
		public function register_tnc_pdf_templates( $atts ) {
			// Create the key used for the themes cache
			$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );
			// If it doesn't exist, or it's empty prepare an array
			$templates = wp_get_theme()->get_page_templates();
			if ( empty( $templates ) ) {
				$templates = array();
			}
			// New cache, therefore remove the old one
			wp_cache_delete( $cache_key , 'themes');
			// Now add our template to the list of templates by merging our templates
			// with the existing templates array from the cache.
			$templates = array_merge( $templates, $this->templates );
			// Add the modified cache to allow WordPress to pick it up for listing
			// available templates
			wp_cache_add( $cache_key, $templates, 'themes', 1800 );
			return $atts;
		}
		/**
		* Checks if the template is assigned to the page
		*/
		public function view_tnc_pdf_template( $template ) {
			global $post;
			if (!isset($this->templates[get_post_meta(
				$post->ID, '_wp_page_template', true
				)] ) ) {
				return $template;
		}
			$file = plugin_dir_path(__FILE__). get_post_meta(
				$post->ID, '_wp_page_template', true
			);
				// Just to be safe, we check if the file exist first
				if( file_exists( $file ) ) {
					return $file;
				}
				else { echo $file; }
				return $template;
			}
}
add_action( 'plugins_loaded', array( 'TncRegisterPT', 'get_instance' ) );
register_activation_hook( __FILE__, 'pvfw_activation' );
register_deactivation_hook( __FILE__, 'pvfw_deactivation' );
function pvfw_activation(){
	add_option( 'auto_add_link', '', 'yes' );
	add_option( 'hide_share', '', 'yes' );
	add_option( 'hide_print', '', 'yes' );
	add_option( 'hide_download', '', 'yes' );
	add_option( 'hide_open', '', 'yes' );
	add_option( 'hide_zoom', '', 'yes' );
	add_option( 'hide_fullscreen', '', 'yes' );
	add_option( 'logo_image', '', 'yes' );
	add_option( 'hide_logo', '', 'yes' );
	add_option( 'hide_find', '', 'yes' );
	add_option( 'hide_pagenav', '', 'yes' );
	add_option( 'tnc_link_target', '', 'yes' );
	add_option( 'pdf_viewer_custom_css', '', 'yes' );
	add_option( 'auto_iframe_width', '', 'yes' );
	add_option( 'auto_iframe_height', '', 'yes' );
	add_option( 'tnc_pdf_viewer_page_id', '', 'yes' );
	add_option( 'tnc_pdf_viewer_sc_page_id', '', 'yes' );

	function pvfw_get_page_by_name($pagename){
		$list_all_pages = get_pages();
		foreach ($list_all_pages as $page) if ($page->post_name == $pagename) return $page;
		return false;
	}
	$pdf_viewer_page = pvfw_get_page_by_name('themencode-pdf-viewer');
	if (!empty($pdf_viewer_page)) {
		// do nothing
	} else {
		$themencode_pdf_viewer_page = array(
			'post_name' 	=> 'themencode-pdf-viewer',
			'post_title' 	=> 'ThemeNcode PDF Viewer [Do not Delete]',
			'post_content' 	=> 'This page is used for Viewing PDF.',
			'post_status' 	=> 'publish',
			'post_type' 	=> 'page',
			'page_template' => 'tnc-pdf-viewer.php'
		);
		$themencode_pdf_viewer_page_post_id = wp_insert_post($themencode_pdf_viewer_page);
		$tpvp_opt_name                = "tnc_pdf_viewer_page_id";
		add_option( $tpvp_opt_name, '', 'yes' );
		update_option($tpvp_opt_name, $themencode_pdf_viewer_page_post_id);
		update_post_meta( $themencode_pdf_viewer_page_post_id, '_wp_page_template', 'tnc-pdf-viewer.php' );
	}
	$pdf_viewer_sc_page = pvfw_get_page_by_name('themencode-pdf-viewer-sc');
	if (!empty($pdf_viewer_sc_page)) {
	// page exists and is in $page
	} else {
		$themencode_pdf_viewer_sc_page = array(
			'post_name' 	=> 'themencode-pdf-viewer-sc',
			'post_title' 	=> 'ThemeNcode PDF Viewer SC [Do not Delete]',
			'post_content' 	=> 'This page is used for Viewing PDF.',
			'post_status' 	=> 'publish',
			'post_type' 	=> 'page',
		);
		$themencode_pdf_viewer_sc_page_post_id = wp_insert_post($themencode_pdf_viewer_sc_page);
		$tpvps_opt_name               = "tnc_pdf_viewer_sc_page_id";
		add_option( $tpvps_opt_name, '', 'yes' );
		update_option($tpvps_opt_name, $themencode_pdf_viewer_sc_page_post_id);
		update_post_meta( $themencode_pdf_viewer_sc_page_post_id, '_wp_page_template', 'tnc-pdf-viewer-shortcode.php' );
	}
}
function pvfw_deactivation(){
	// Do Nothing Right Now
}
?>