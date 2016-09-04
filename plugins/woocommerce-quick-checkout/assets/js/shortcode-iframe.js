/**
 *  Google Places Reviews JS: WP Admin Shortcode Generator
 *
 *  @description: JavaScripts for the shortcode generator iframe
 *  @since: 1.3
 */

(function ( $ ) {
	"use strict";

	var custom_params = '';
	var existing_shortcode = false;

	$( document ).ready( function () {

		$(".wc-product-search").on("change", function (e) { });

		//Cancel button (closes iframe modal)
		$( '#wqc_cancel' ).on( 'click', function ( e ) {
			top.tinymce.activeEditor.windowManager.close();
			e.preventDefault();
		} );

		custom_params = top.tinyMCE.activeEditor.windowManager.getParams();

		//Are there custom params?
		if ( custom_params.shortcode_params !== 'undefined' ) {
			existing_shortcode = true;
		}

		//Get things going for various functions
		init();

	} );

	// Init
	// @public
	function init() {

		wqc_tipsy();
		wqc_generator_submit();

		//iframe sizing
		setTimeout( function () {
			$( 'body.iframe' ).css( {height: 'auto'} );
		}, 200 );

		//Toggle fields
		$( '.wqc-toggle-shortcode-fields' ).on( 'click', function () {
			$( '.wqc-shortcode-hidden-fields-wrap' ).slideToggle();
		} );

		//New or Existing Shortcode?
		if ( existing_shortcode === true ) {
			$( '.wqc-edit-shortcode' ).show(); //show edit options
			$( '.wqc-shortcode-hidden-fields-wrap' ).show(); //show table of options
			$( '#wqc_location_lookup_fields' ).hide(); //hide lookup fields (already set)
			$( '#wqc_submit' ).val( 'Edit Shortcode' ); //Change submit button text
			wqc_set_existing_params( custom_params ); //Set default options
		}

	}


	/**
	 * Set Existing Options
	 *
	 * @description Sets the generator options according to the user's already preset shortcode configuration
	 * @param custom_params obj
	 */
	function wqc_set_existing_params( custom_params ) {

		//Set variables from passed custom_params
		var id = wqc_get_attr( custom_params.shortcode_params, 'id' ),
			title = wqc_get_attr( custom_params.shortcode_params, 'title' ),
			theme = wqc_get_attr( custom_params.shortcode_params, 'widget_style' ),
			alignment = wqc_get_attr( custom_params.shortcode_params, 'align' ),
			max_width = wqc_get_attr( custom_params.shortcode_params, 'max_width' ),
			review_limit = wqc_get_attr( custom_params.shortcode_params, 'review_limit' ),
			cache = wqc_get_attr( custom_params.shortcode_params, 'cache' ),
			rating_filter = wqc_get_attr( custom_params.shortcode_params, 'review_filter' ),
			review_char_limit = wqc_get_attr( custom_params.shortcode_params, 'review_char_limit' ),
			pre_content = wqc_get_attr( custom_params.shortcode_params, 'pre_content' ),
			post_content = wqc_get_attr( custom_params.shortcode_params, 'post_content' ),
			hide_header = wqc_get_attr( custom_params.shortcode_params, 'hide_header' ),
			hide_google_image = wqc_get_attr( custom_params.shortcode_params, 'hide_google_image' ),
			hide_out_of_rating = wqc_get_attr( custom_params.shortcode_params, 'hide_out_of_rating' ),
			target_blank = wqc_get_attr( custom_params.shortcode_params, 'target_blank' ),
			no_follow = wqc_get_attr( custom_params.shortcode_params, 'no_follow' );

		//Set Place ID (very important)
		if ( id ) {
			$( '#wqc_widget_place_id' ).val( id );
		} else {
			alert( 'There was no Place ID found for this shortcode. Please create a new one.' );
			return false;
		}

		//Change default settings to customized ones using the values of the variables set above
		if ( title ) {
			$( '#wqc_widget_title' ).val( title );
		}
		if ( theme ) {
			$( '#wqc_widget_theme' ).val( theme );
		}
		if ( alignment ) {
			$( '#wqc_widget_alignment' ).val( alignment );
		}
		if ( max_width ) {
			$( '#wqc_widget_maxwidth' ).val( max_width );
		}
		if ( rating_filter ) {
			$( '#wqc_widget_review_filter' ).val( rating_filter );
		}
		if ( review_char_limit ) {
			$( '#wqc_widget_review_char_limit' ).val( review_char_limit );
		}
		if ( pre_content ) {
			$( '#wqc_widget_pre_content' ).val( pre_content );
		}
		if ( post_content ) {
			$( '#wqc_widget_post_content' ).val( post_content );
		}
		if ( cache ) {
			$( '#wqc_widget_cache' ).val( cache );
		}
		if ( hide_header == 'true' ) {
			$( '#wqc_widget_hide_header' ).prop( 'checked', true );
		}
		if ( hide_google_image == 'true' ) {
			$( '#wqc_widget_hide_google_image' ).prop( 'checked', true );
		}
		if ( hide_out_of_rating == 'true' ) {
			$( '#wqc_widget_hide_out_of_rating' ).prop( 'checked', true );
		}
		if ( no_follow == 'false' ) {
			$( '#wqc_widget_no_follow' ).prop( 'checked', false );
		}
		if ( target_blank == 'false' ) {
			$( '#wqc_widget_target_blank' ).prop( 'checked', false );
		}

	}

	/**
	 * Tooltips
	 */
	function wqc_tipsy() {
		//Tooltips for admins
		$( '.tooltip-info' ).tipsy( {
			fade    : true,
			html    : true,
			gravity : 'n',
			delayOut: 1000,
			delayIn : 500
		} );
	}

	/**
	 * Shortcode Generator On Submit
	 *
	 * @description: Outputs the shortcode in TinyMCE and does minor validation
	 */
	function wqc_generator_submit() {

		$( '#wqc_settings' ).on( 'submit', function ( e ) {
			e.preventDefault();

			var frm = $(this);

			var frmArray = frm.serializeArray();

			var data = {};

			$.each( frmArray, function () {
				if( '' != this.value )
					data[this.name] = this.value || '';
			});

			var joinedAttrs = $.map(data, function(v,k) {
				return k+'="'+v+'"';
			}).join(' ');

			//Set our variables
			var args = top.tinymce.activeEditor.windowManager.getParams(),
				shortcode;

			//Form the shortcode
			shortcode = '[quick_checkout ';

			shortcode += joinedAttrs;

			shortcode += ']';

			top.tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, shortcode );
			top.tinymce.activeEditor.windowManager.close();

		} );


	}

	/**
	 * Get Attribute
	 *
	 * @description: Helper function that plucks options from passed string
	 */
	function wqc_get_attr( s, n ) {
		n = new RegExp( n + '=\"([^\"]+)\"', 'g' ).exec( s );
		return n ? window.decodeURIComponent( n[1] ) : '';
	}


})( jQuery );