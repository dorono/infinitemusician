<?php
/**
 * WARNING:
 *
 * Do not edit or add to this file if you wish to upgrade the plugin!
 * Otherwise you will lose all changes when you upgrade.
 *
 *
 * Copyright (c) 2013 -  2015, IgniteWoo - ALL RIGHTS RESERVED
 *
 * Portions also Copyright (c) 2012-2013, SkyVerge, Inc.
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( '404 - NOT FOUND' );

/**
 *
 * This class gets voucher data from storage.  This class
 * represents two different concepts:  a "voucher template" and a "product voucher".
 * The voucher template can be thought of as the blueprint for a voucher, it
 * contains everything needed to create a voucher (one or more images, the
 * coordinates for a number of fields, expiry days, etc).  The "product voucher"
 * is an instantiation of a voucher template, it also contains the voucher data.
 *
 * @since 3.3
 */
class IGN_Gift_Cert_Voucher {

	/**
	 * @var int voucher post_id
	 */
	public $id;

	private $voucher_custom_fields;
	/**
	 * @var string voucher name (post title)
	 */
	private $name;
	/**
	 * @var string Default voucher font color
	 */
	private $voucher_font_color;
	/**
	 * @var int Default voucher font size
	 */
	private $voucher_font_size;
	/**
	 * @var string Default voucher font style (one of 'B', 'I' or 'BI')
	 */
	private $voucher_font_style;
	/**
	 * @var string Default voucher font family
	 */
	private $voucher_font_family;
	/**
	 * @var array Voucher fields (text which is written on top of the voucher image to create the final pdf)
	 */
	public $voucher_fields;
	/**
	 * @var array of voucher image ids (attachment ids)
	 */
	private $image_ids;
	/**
	 * @var int Voucher main image id (attachment id)
	 */
	public $image_id;
	/**
	 * @var array Optional voucher 'reverse' or second page image
	 */
	private $additional_image_ids;

	/** Product Voucher Fields ******************************************************/

	/**
	 * @var int Order id when this is a product voucher
	 */
	private $order_id;
	/**
	 * @var WC_Order Order object when this is a product voucher
	 */
	private $order;
	/**
	 * @var array Array of item data when this is a product voucher
	 */
	private $item;
	/**
	 * @var string Product voucher number
	 */
	public $voucher_number;
	/**
	 * @var int Expiration date of this product voucher, mesured in number of seconds since the Unix Epoch
	 */
	public $expiration_date;

	/**
	 * @var string The product name for this voucher
	 */
	public $product_name;

	/**
	 * @var string The product sku for this voucher
	 */
	public $product_sku;

	/**
	 * @var string The recipient name for this voucher
	 */
	public $recipient_name;

	/**
	 * @var string The recipient message for this voucher
	 */
	public $message;


	/**
	 * Construct voucher with $id
	 *
	 * @since 3.3
	 * @param int $id Voucher id
	 * @param int $order_id optional order id when this is a product voucher
	 * @param array $item optional item data when this is a product voucher
	 */
	function __construct( $id = '', $order_id = null, $item = array() ) {
		global $wpdb;

		$this->id       = (int) $id;
		$this->order_id = $order_id;
		$this->item     = isset( $item['item'] ) ? $item['item'] : '';
		$this->data	=  isset( $item['data'] ) ? $item['data'] : '';
		$this->coupon_id =  isset( $item['coupon_id'] ) ? $item['coupon_id'] : '';
		$this->coupon_code =  isset( $item['coupon_code'] ) ? $item['coupon_code'] : '';
		$this->coupon_amount =  isset( $item['coupon_amount'] ) ? $item['coupon_amount'] : '';
		$this->expiration_date = isset( $item['expiry'] ) ? strtotime( $item['expiry'] ) : '';

		// load data from the item if this is a product voucher
		if ( $this->item ) {
			$this->voucher_number = $item['voucher_number'];
		}

		$this->voucher_custom_fields = get_post_custom( $this->id );

		// Define the data we're going to load: Key => Default value
		$load_data = array(
			'image_ids'            => array(),
			'additional_image_ids' => array(),
			'voucher_font_color'   => '',
			'voucher_font_size'    => '',
			'voucher_font_style'   => '',
			'voucher_font_family'  => '',
			'voucher_fields'       => array(),
		);

		// Load the data from the custom fields
		foreach ( $load_data as $key => $default ) {
		
			// set value from db (unserialized if needed) or use default
			$this->$key = ( isset( $this->voucher_custom_fields[ '_' . $key ][0] ) && '' !== $this->voucher_custom_fields[ '_' . $key ][0] ) ? ( is_array( $default ) ? maybe_unserialize( $this->voucher_custom_fields[ '_' . $key ][0] ) : $this->voucher_custom_fields[ '_' . $key ][0] ) : $default;
			
		}

		// set the voucher main template image, if any
		if ( count( $this->image_ids ) > 0 ) {
			$this->image_id = $this->image_ids[0];
		}
		
		$this->product_desc = isset( $item['product_desc'] ) ? $item['product_desc'] : '';

		return false;
	}


	/** Getter/Setter methods ******************************************************/


	/**
	 * Returns true if this voucher is completely redeemed
	 *
	 * @since 3.3
	 * @return boolean true if the voucher is completely redeemd, false otherwise
	 */
	public function is_redeemed() {
		if ( $this->item && isset( $this->item['voucher_redeem'] ) ) {

			$voucher_redeem = maybe_unserialize( $this->item['voucher_redeem'] );

			foreach ( $voucher_redeem as $date ) {
				if ( ! $date ) return false;
			}
		}

		return true;
	}


	/**
	 * Returns the formatted product voucher number, which consists of the
	 * order number - voucher number
	 *
	 * @since 3.3
	 * @return string if a voucher number has been created, or null otherwise
	 */
	public function get_voucher_number() {

		return $this->coupon_code;
		
	}

	/**
	 * Returns the formatted product voucher number, which consists of the
	 * order number - voucher number
	 *
	 * @since 3.3
	 * @return string if a voucher number has been created, or null otherwise
	 */
	public function get_voucher_value() {

		if ( empty( $this->coupon_amount ) )
			return '';
			
		$value = html_entity_decode( strip_tags( woocommerce_price( $this->coupon_amount ) ) );

		return $value;
		
	}
	

	/**
	 * Get the number of days this voucher is valid for
	 *
	 * @since 3.3
	 * @return int expiry days
	 */
	public function get_expiry() {
	
		return $this->expiration_date;

	}


	/**
	 * Set the expiration date for this product voucher
	 *
	 * @since 3.3
	 * @param int $expiration_date expiration date of this product voucher,
	 *        mesured in number of seconds since the Unix Epoch
	 */
	public function set_expiration_date( $expiration_date ) {
		$this->expiration_date = $expiration_date;
	}


	/**
	 * Get the expiration date (if any) in the user-defined WordPress format,
	 * or the empty string.  Product voucher method.
	 *
	 * @since 3.3
	 * @return string formatted expiration date, if any, otherwise the empty string
	 */
	public function get_formatted_expiration_date() {

		if ( empty( $this->expiration_date ) && !empty( $_POST['gen']['code'] ) ) {
			$this->expiration_date = get_post_meta( absint( $_POST['gen']['code'] ), 'expiry_date', true );
			if ( !empty( $this->expiration_date ) )
				$this->expiration_date = strtotime( $this->expiration_date );
		}
		
		if ( $this->expiration_date ) {
		
			if ( is_int( $this->expiration_date ) ) 
				return date_i18n( get_option( 'date_format' ), $this->expiration_date );
			
			else 
				return $this->expiration_date;
				
		}
		
		return '';
	}


	/**
	 * Get the recipient name if any for this product voucher
	 *
	 * @since 3.3
	 * @return string voucher recipient name or empty string
	 */
	public function get_recipient_name() {
		if ( ! isset( $this->recipient_name ) ) {
		
			
			$this->recipient_name = isset( $this->data['voucher_to_name'] ) ? $this->data['voucher_to_name'] : '';
		}

		return $this->recipient_name;
	}


	/**
	 * Get the voucher message if any for this product voucher
	 *
	 * @since 3.3
	 * @return string voucher message or empty string
	 */
	public function get_message() {
	
		if ( !isset( $this->message ) ) {
		
			$this->message = isset( $this->data['voucher_message'] ) ? $this->data['voucher_message'] : '';
		
		}

		return $this->message;
	}


	/**
	 * Get the product name, if available
	 *
	 * @since 3.3
	 * @return string product name if this is a product voucher, or the empty string
	 */
	public function get_product_name() {
		if ( ! isset( $this->product_name ) ) {
		
			$this->product_name = isset( $this->item['name'] ) ? $this->item['name'] : '';

		}

		return $this->product_name;
	}


	/**
	 * Get the product sku, if available
	 *
	 * @since 3.3
	 * @return string product sku if this is a product voucher, or the empty string
	 */
	public function get_product_sku() {
	
		if ( ! isset( $this->product_sku ) ) {
		
			if ( $this->order_id && $this->item ) {
			
				// get product (this works for simple and variable products)
				$order = $this->get_order();
				
				$product = $order->get_product_from_item( $this->item );

				$this->product_sku = $product->get_sku();
				
			} else {
			
				$this->product_sku = '';
				
			}
		}

		return $this->product_sku;
	}


	/**
	 * Gets the main voucher image, or a placeholder
	 *
	 * @since 3.3
	 * @return string voucher primary img tag
	 */
	public function get_image( $size = 'ignitewoo_voucher_thumb_size' ) {
		global $woocommerce;

		$image = '';

		if ( version_compare( $woocommerce->version, '2.1' ,'<' ) ) {
		
			$width = $woocommerce->get_image_size( 'shop_thumbnail_image_width' );
			$height = $woocommerce->get_image_size( 'shop_thumbnail_image_height' );
			$placeholder = woocommerce_placeholder_img_src();
		
		} else { 
		
			$this_size = wc_get_image_size( 'shop_thumbnail' );
			$width = $this_size['width'];
			$height = $this_size['height'];
			$placeholder = wc_placeholder_img_src();
		
		}

		if ( has_post_thumbnail( $this->id ) ) {
			$image = get_the_post_thumbnail( $this->id, $size );
		} else {
			$image = '<img src="' . $placeholder . '" alt="Placeholder" width="' . $width . '" height="' . $height . '" />';
		}

		return $image;
	}


	/**
	 * Gets the voucher image id: the selected image id if this is a voucher product
	 * otherwise the voucher template primary image id
	 *
	 * @since 3.3
	 * @return int voucher image id
	 */
	public function get_image_id() {

		// if this is a voucher product, return the selected image id
		if ( isset( $this->item['voucher_image_id'] ) ) return $this->item['voucher_image_id'];

		// otherwise return the template primary image id
		return $this->image_id;
	}


	/**
	 * Get the all available images for this voucher
	 *
	 * @since 3.3
	 * @return array of img tags
	 */
	public function get_image_urls( $size = 'ignitewoo_voucher_thumb_size' ) {
		global $woocommerce;

		$images = array();

		foreach ( $this->image_ids as $image_id ) {
			$image_src = wp_get_attachment_url( $image_id );
			$thumb_src = wp_get_attachment_image_src( $image_id, $size );

			if ( $image_src ) {
				$images[ $image_id ]['image'] = $image_src;
				$images[ $image_id ]['thumb'] = $thumb_src[0];
			}
		}

		return $images;
	}


	/**
	 * Returns any user-supplied voucher field data in an associative array of
	 * data display name to value.
	 *
	 * @since 3.3
	 * @param int $cut_textarea the number of characters to limit a returned
	 *        textarea value to.  0 indicates to return the entire value regardless
	 *        of length
	 *
	 * @return array associative array of input field name to value
	 */
	public function get_user_input_data( $limit_textarea = 25 ) {
		$data = array();

		// get any meta data

		foreach ( $this->voucher_fields as $field ) {
			if ( 'user_input' == $field['type'] ) {
				foreach ( $this->item as $meta_name => $meta_value ) {
					if ( __( $field['display_name'], 'ignitewoo_gift_certs' ) == $meta_name ) {

						// limit the textarea value?
						if ( 'textarea' == $field['input_type'] && $limit_textarea && strlen( $meta_value ) > $limit_textarea ) {
							list( $value ) = explode( "\n", wordwrap( $meta_value, $limit_textarea, "\n" ) );
							$meta_value = $value . '...';
						}

						$data[ $field['display_name'] ] = $meta_value;
						break;
					}
				}
			}
		}

		return $data;
	}


	/**
	 * Return an array of user-input voucher fields
	 *
	 * @since 3.3
	 * @return array of user-input voucher fields
	 */
	public function get_user_input_voucher_fields() {
		$fields = array();
		foreach ( $this->voucher_fields as $name => $voucher_field ) {

			if ( 'user_input' == $voucher_field['type'] && ! empty( $voucher_field['position'] ) ) {
				$voucher_field['name'] = $name;
				$fields[ (int) $voucher_field['order'] ] = $voucher_field;
			}
		}
		// make sure they're ordered properly (ie for the frontend)
		ksort( $fields );

		return $fields;
	}


	/**
	 * Get the maximum length for the user input field named $name.  This is
	 * enforced on the frontend so that the voucher text doesn't overrun the
	 * field area
	 *
	 * @since 3.3
	 * @param string $name the field name
	 * @return int the max length of the field, or empty string if there is no
	 *         limit
	 */
	public function get_user_input_field_max_length( $name ) {
		if ( isset( $this->voucher_fields[ $name ]['max_length'] ) ) return $this->voucher_fields[ $name ]['max_length'];
		return '';
	}


	/**
	 * Returns true if the user input field named $name is required, false otherwise
	 *
	 * @since 1.1
	 * @param string $name the field name
	 * @return boolean true if $name is required, false otherwise
	 */
	public function user_input_field_is_required( $name ) {
		if ( isset( $this->voucher_fields[ $name ]['is_required'] ) ) return 'yes' == $this->voucher_fields[ $name ]['is_required'];
		return '';
	}


	/**
	 * Returns true if this voucher has any user input fields that are required
	 *
	 * @since 1.1
	 * @return boolean true if there is a required field
	 */
	public function has_required_input_fields() {
		foreach ( $this->voucher_fields as $field ) {
			if ( isset( $field['is_required'] ) && 'yes' == $field['is_required'] ) return true;
		}

		return false;
	}


	/**
	 * Returns the font definition for the field $field_name, using the voucher
	 * font defaults if not provided
	 *
	 * @since 3.3
	 * @param string $field_name name of the field
	 *
	 * @return array with optional members 'family', 'size', 'style', 'color'
	 */
	public function get_field_font( $field_name ) {
		$default_font = array( 'family' => $this->voucher_font_family, 'size' => $this->voucher_font_size, 'color' => $this->voucher_font_color );

		// only use the default font style if there is no specific font family set
		if ( ! isset( $this->voucher_fields[ $field_name ]['font']['family'] ) || ! $this->voucher_fields[ $field_name ]['font']['family'] ) {
			$default_font['style'] = $this->voucher_font_style;
		}

		// get rid of any empty fields so the defaults can take precedence
		foreach ( $this->voucher_fields[ $field_name ]['font'] as $key => $value ) {
			if ( ! $value ) unset( $this->voucher_fields[ $field_name ]['font'][ $key ] );
		}

		$merged = array_merge( $default_font, $this->voucher_fields[ $field_name ]['font'] );

		// handle style specially
		if ( ! isset( $merged['style'] ) ) $merged['style'] = '';

		return $merged;
	}


	/**
	 * Returns the field position for the field $field_name
	 *
	 * @since 3.3
	 * @return array associative array with position members 'x1', 'y1', 'width'
	 *         and 'height'
	 */
	public function get_field_position( $field_name ) {
		return isset( $this->voucher_fields[ $field_name ]['position'] ) ? $this->voucher_fields[ $field_name ]['position'] : array();
	}

	public function get_text_align( $field_name ) {
		return isset( $this->voucher_fields[ $field_name ] ) ? $this->voucher_fields[ $field_name ]['align'] : 'left';
	}
	/**
	 * Returns the file name for this product voucher
	 *
	 * @since 3.3
	 * @return string voucher pdf file name
	 */
	public function get_voucher_filename() {
		return 'voucher-' . $this->get_voucher_number() . '.pdf';
	}


	/**
	 * Returns the relative voucher pdf file path for this product voucher
	 *
	 * @since 3.3
	 * @return string voucher pdf file path
	 */
	public function get_voucher_path() {
		
		$year = date( 'Y', current_time( 'timestamp' ) );
		
		$month = date( 'm', current_time( 'timestamp' ) );
		
		$upload_dir = wp_upload_dir();
		
		//looks like this: wp-content/uploads/woocommerce_uploads/ignitewoo_vouchers/2012/01

		$dir = $upload_dir['basedir'] . '/woocommerce_uploads/ignitewoo_vouchers/' . $year . '/' . $month;
		
		if ( !file_exists( $dir ) )
			mkdir( $dir, 0755, true );
		
		return $dir;
	}


	/**
	 * Get the order that this voucher is attached to, when it is a product voucher.
	 *
	 * @since 3.3
	 * @return WC_Order the order, or null
	 */
	public function get_order() {
		if ( $this->order ) return $this->order;

		if ( $this->order_id ) {
			$this->order = new WC_Order( $this->order_id );
			return $this->order;
		}

		return null;
	}


	/**
	 * Returns the item associated with this voucher
	 *
	 * @since 1.1.1
	 * @return array order item
	 */
	public function get_item() {
		if ( $this->item ) return $this->item;

		return null;
	}


	/** PDF Generation methods ******************************************************/


	/**
	 * Generate and save or stream a PDF file for this product voucher
	 *
	 * @since 3.3
	 * @param string $path optional absolute path to the voucher directory, if
	 *        not supplied the PDF will be streamed as a downloadable file (used
	 *        for admin previewing of the PDF)
	 *
	 * @return mixed nothing if a $path is supplied, otherwise a PDF download
	 */
	public function generate_pdf( $path = '' ) {
		global $post, $ignite_gift_certs;
//echo urlencode( $this->get_voucher_value() ); die;
//echo iconv( 'UTF-8', 'windows-1252', $this->get_voucher_value() ); die;

		// include the pdf library
		$root_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
		
		require_once( $root_dir . '/../lib/fpdf/tfpdf.php' );
		//require_once( $root_dir . '/../lib/MPDF57/mpdf.php' );

		$image = wp_get_attachment_metadata( $this->get_image_id() );

		// determine orientation: landscape or portrait
		if ( $image['width'] > $image['height'] ) {
			$orientation = 'L';
		} else {
			$orientation = "P";
		}

		// Create the pdf
		// TODO: we're assuming a standard DPI here of where 1 point = 1/72 inch = 1 pixel
		// When writing text to a Cell, the text is vertically-aligned in the middle
		$fpdf = new tFPDF( $orientation, 'pt', array( $image['width'], $image['height'] ) );
		//$fpdf = new mPDF();
		$fpdf->AddPage();
		$fpdf->SetAutoPageBreak( false );

		// set the voucher image
		$upload_dir = wp_upload_dir();
		
		// DPI : http://stackoverflow.com/questions/10040309/how-to-maintain-image-quality-with-fpdf-and-php
		$fpdf->Image( $upload_dir['basedir'] . '/' . $image['file'], 0, 0, $image['width'], $image['height'] );

		// this is useful for displaying the text cell borders when debugging the PDF layout,
		//  though keep in mind that we translate the box position to align the text to bottom
		//  edge of what the user selected, so if you want to see the originally selected box,
		//  display that prior to the translation
		$show_border = 0;

		// voucher message text, this is multi-line, so it's handled specially
		$this->textarea_field( $fpdf, 'message', $this->get_message(), $show_border );

		// product name
		$this->text_field( $fpdf, 'product_name', $this->get_product_name(), $show_border );

		// product desc
		$this->textarea_field( $fpdf, 'product_desc', $this->product_desc, $show_border );
		
		// product sku
		$this->text_field( $fpdf, 'product_sku', $this->get_product_sku(), $show_border );

		// recepient name
		$this->text_field( $fpdf, 'recipient_name', $this->get_recipient_name(), $show_border );

		// expiry date
		$this->text_field( $fpdf, 'expiration_date', $this->get_formatted_expiration_date(), $show_border );

		// voucher number
		$this->text_field( $fpdf, 'voucher_number', $this->get_voucher_number(), $show_border );

		// voucher value
		$this->text_field( $fpdf, 'voucher_value', $this->get_voucher_value(), $show_border );

		// voucher value
		if ( isset( $this->voucher_fields['random_text']['random_text_data'] ) && !empty( $this->voucher_fields['random_text']['random_text_data'] ) )
			$this->textarea_field( $fpdf, 'random_text', $this->voucher_fields['random_text']['random_text_data'], $show_border );
		
		$links_to = $ignite_gift_certs->admin_settings['qr_links_to'];

		// This kicks in when someone is manually generating a PDF from the voucher template page
		if ( empty( $this->coupon_id ) && !empty( $_POST['gen']['code'] ) ) {
			$this->coupon_id = $_POST['gen']['code'];
			$links_to = 'coupon';
		}
			
		if ( empty( $links_to ) || 'order' == $links_to )
			$link = urlencode( admin_url( '/post.php?post=' . $this->order_id . '&action=edit' ) );
		else if ( 'coupon' == $links_to )
			$link = urlencode( admin_url( '/post.php?post=' . $this->coupon_id . '&action=edit' ) );

		// QR code
		$this->qr_code( $fpdf, 'qr', $link , $show_border );
		
		$this->barcode( $fpdf, 'barcode' , $show_border );
		
		// has additional pages?
		foreach ( $this->additional_image_ids as $additional_image_id ) {
			$fpdf->AddPage();
			$additional_image = wp_get_attachment_metadata( $additional_image_id );
			$fpdf->Image( $upload_dir['basedir'] . '/' . $additional_image['file'],
			              0,
			              0,
			              $additional_image['width']  < $image['width']  ? $additional_image['width']  : $image['width'],
			              $additional_image['height'] < $image['height'] ? $additional_image['height'] : $image['height'] );
		}

		if ( $path ) {
			// save the pdf as a file
			
			//$filepath = $path . '/' . $this->get_voucher_path() . '/' . $this->get_voucher_filename();
			$filepath = $this->get_voucher_path() . '/' . $this->get_voucher_filename();

			$fpdf->Output( $filepath, 'F' );

			return $filepath;
			
			
		} else {
			// download file
			$fpdf->Output( 'ignitewoo-voucher-preview-' . $this->id . '.pdf', 'D' );
		}
	}


	/**
	 * Render a multi-line text field to the PDF
	 *
	 * @since 3.3
	 * @param FPDF $fpdf fpdf library object
	 * @param string $field_name the field name
	 * @param mixed $value string or int value to display
	 * @param int $show_border a debugging/helper option to display a border
	 *        around the position for this field
	 */
	private function textarea_field( $fpdf, $field_name, $value, $show_border ) {

		if ( $this->get_field_position( $field_name ) && $value ) {

			$max_length = $this->get_user_input_field_max_length( $field_name );

			if ( !empty( $max_length ) )
				$value = substr( $value, 0, $max_length );
			
			$font = $this->get_field_font( $field_name );

			// get the field position
			list( $x, $y, $w, $h ) = array_values( $this->get_field_position( $field_name ) );

			// font color
			$font['color'] = $this->hex2rgb( $font['color'] );
			$fpdf->SetTextColor( $font['color'][0], $font['color'][1], $font['color'][2] );

			if ( false !== strpos( $font['family'], 'Deja' ) )
				$fpdf->AddFont( 'DejaVu', $font['style'], 'DejaVuSansCondensed.ttf', true );
				
			// set the field text styling
			$fpdf->SetFont( $font['family'], $font['style'], $font['size'] );

			$fpdf->setXY( $x, $y );
			
			$align = isset( $this->voucher_fields[$field_name]['align'] ) ? $this->voucher_fields[$field_name]['align'] : '';

			// and write out the value
			$fpdf->Multicell( $w, $font['size'], utf8_decode( $value ), $show_border, $align );
		}
	}


	/**
	 * Render a single-line text field to the PDF
	 *
	 * @since 3.3
	 * @param FPDF $fpdf fpdf library object
	 * @param string $field_name the field name
	 * @param mixed $value string or int value to display
	 * @param int $show_border a debugging/helper option to display a border
	 *        around the position for this field
	 */
	private function text_field( $fpdf, $field_name, $value, $show_border ) {

		if ( $this->get_field_position( $field_name ) && $value ) {

			$max_length = $this->get_user_input_field_max_length( $field_name );

			if ( !empty( $max_length ) )
				$value = substr( $value, 0, $max_length );
			
			$font = $this->get_field_font( $field_name );

			// get the field position
			list( $x, $y, $w, $h ) = array_values( $this->get_field_position( $field_name ) );

			// font color
			$font['color'] = $this->hex2rgb( $font['color'] );
			$fpdf->SetTextColor( $font['color'][0], $font['color'][1], $font['color'][2] );

			if ( false !== strpos( $font['family'], 'Deja' ) )
				$fpdf->AddFont( 'DejaVu', $font['style'], 'DejaVuSansCondensed.ttf', true );
			else if ( false !== strpos( $font['family'], 'Angsa' ) )
				$fpdf->AddFont( 'angsa', $font['style'], 'angsa.php', false );
			
			// set the field text styling
			$fpdf->SetFont( $font['family'], $font['style'], $font['size'] );

			// show a border for debugging purposes
			if ( $show_border ) {
				$fpdf->setXY( $x, $y );
				$fpdf->Cell( $w, $h, '', 1 );
			}

			// align the text to the bottom edge of the cell by translating as needed
			$y = $font['size'] > $h ? $y - ( $font['size'] - $h ) / 2 : $y + ( $h - $font['size'] ) / 2;
			$fpdf->setXY( $x, $y );

			$value = str_replace( '&euro;', '€', $value );
			
			/*
			if ( '€' == substr( $value, 0, 3 ) ) 
				$value = iconv( 'UTF-8', 'windows-1252', $value );
			else 
				$value = utf8_decode( $value );
			*/
			
			$align = isset( $this->voucher_fields[$field_name]['align'] ) ? $this->voucher_fields[$field_name]['align'] : '';

			if ( false !== strpos( $font['family'], 'Angsa' ) )
				$fpdf->Cell( $w, $h, iconv( 'UTF-8', 'TIS-620', $value ), 0, 0, $align  );
			// and write out the value
			else 
				$fpdf->Cell( $w, $h, $value, 0, 0, $align );  // can try iconv('UTF-8', 'windows-1252', $content); if this doesn't work correctly for accents
		}
	}


	private function qr_code( $fpdf, $field_name, $link = '', $show_border ) { 
	
		if ( !$this->get_field_position( $field_name ) ) 
			return;

		// get the field position
		list( $x, $y, $w, $h ) = array_values( $this->get_field_position( $field_name ) );

		$size = $w . 'x' . $w;
	
		// Set url
		//$url = 'https://chart.googleapis.com/chart?chs=' . $size . '&cht=qr&chl=' . $link . '&choe=UTF-8';
		$url = 'https://chart.googleapis.com/chart?chs=' . $size . '&cht=qr&chl=' . $link . '&choe=UTF-8';
	
	
		$fpdf->setXY( $x, $y );
		
		// Insert a logo in the top-left corner at 300 dpi
		//$pdf->Image('logo.png',10,10,-300);
		// Insert a dynamic image from a URL
		//             $url, X,  Y,  width, height, type - optional
		//$pdf->Image( $url, 60, 30, 90,    0,    'PNG' );

		$fpdf->Cell( $w, $h, $fpdf->Image( $url, $x, $y, $w, 0, 'PNG' ), 0, 0, 'L', false );
	
	}
	
	
	private function barcode( $fpdf, $field_name, $show_border ) { 
	
		if ( empty( $this->coupon_code ) )
			return;
			
		if ( !$this->get_field_position( $field_name ) ) 
			return;

		// get the field position
		list( $x, $y, $w, $h ) = array_values( $this->get_field_position( $field_name ) );

		$size = $w . 'x' . $w;
		
		$fpdf->setXY( $x, $y );
		
		require_once( dirname( __FILE__ ) .  '/../lib/barcode/class/BCGFontFile.php' );
		
		require_once( dirname( __FILE__ ) .  '/../lib/barcode/class/BCGColor.php' );
		
		require_once( dirname( __FILE__ ) .  '/../lib/barcode/class/BCGDrawing.php' );

		// Including the barcode technology
		require_once( dirname( __FILE__ ) .  '/../lib/barcode/class/BCGcode39.barcode.php' );

		// Loading Font
		$font = new BCGFontFile( dirname( __FILE__ ) .  '/../lib/barcode/font/Arial.ttf', 18 );

		// The arguments are R, G, B for color.
		$color_black = new BCGColor( 0, 0, 0 );
		
		$color_white = new BCGColor( 255, 255, 255 );

		$drawException = null;
		
		try {
			$code = new BCGcode39();
			
			$code->setScale( 2 ); // Resolution
			
			$code->setThickness( 25 ); // Thickness
			
			$code->setForegroundColor( $color_black ); // Color of bars
			
			$code->setBackgroundColor( $color_white ); // Color of spaces
			
			$code->setFont( $font ); // Font (or 0)
			
			$code->parse( $this->coupon_code ); // Text
			
		} catch(Exception $exception) {
		
			$drawException = $exception;
			
		}

		/* Here is the list of the arguments
		1 - Filename (empty : display on screen)
		2 - Background color */
		$drawing = new BCGDrawing( '', $color_white );
		
		$filename = sys_get_temp_dir() . '/' . $this->coupon_code . '.png';
		
		$drawing->setFilename( $filename ); 
		
		if ( $drawException ) {
		
			$drawing->drawException($drawException);
			
		} else {
		
			$drawing->setBarcode($code);
			
			$drawing->draw();
		}

		// Header that says it is an image (remove it if you save the barcode to a file)
		// header('Content-Type: image/png');
		
		// header('Content-Disposition: inline; filename="barcode.png"');

		// Draw (or save) the image into PNG format.
		$drawing->finish( BCGDrawing::IMG_FORMAT_PNG );
		
		$fpdf->Cell( $w, $h, $fpdf->Image( $filename, $x, $y, $w, 0, 'PNG' ), 0, 0, 'L', false );
		
	}
	
	
	/**
	 * Takes a hex color code and returns the RGB components in an array
	 *
	 * @since 3.3
	 * @param string $hex hex color code, ie #EEEEEE
	 *
	 * @return array rgb components, ie array( 'EE', 'EE', 'EE' )
	 */
	private function hex2rgb( $hex ) {

		if ( ! $hex ) return '';

		$hex = str_replace( "#", "", $hex );

		if ( 3 == strlen( $hex ) ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}

		return array( $r, $g, $b );
	}


	/** Helper methods ******************************************************/


	/**
	 * Returns the value for $meta_name, or empty string
	 *
	 * @since 3.3
	 * @param string $meta_name untranslated meta name
	 *
	 * @return string value for $meta_name or empty string
	 */
	private function get_item_meta_value( $meta_name ) {

		// no item set
		if ( ! $this->item ) return '';

		foreach ( $this->item as $name => $value ) {
			if ( __( $meta_name, 'ignitewoo_gift_certs' ) == $name ) {
				return $value;
			}
		}

		// not found
		return '';
	}
}
