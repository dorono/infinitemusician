/**
 * TinyMCE plugin
 *
 * @see: http://generatewp.com/take-shortcodes-ultimate-level/ (heavily referenced)
 */
(function () {

	tinymce.PluginManager.add( 'wqc_shortcode_button', function ( editor, url ) {

		var ed = tinymce.activeEditor;
		var sh_tag = 'woocommerce-quick-checkout';


		/**
		 * Open Shortcode Generator Modal
		 *
		 * @param ui
		 * @param v
		 */
		function wqc_open_modal( ui, v ) {

			editor.windowManager.open( {
				title     : 'Quick Checkout',
				id        : 'wqc_shortcode_dialog',
				width     : 600,
				height    : 450,
				resizable : true,
				scrollbars: true,
				url       : ajaxurl + '?action=wqc_shortcode_iframe'
			}, {
				shortcode       : ed.getLang( 'wqc.shortcode_tag' ),
				shortcode_params: window.decodeURIComponent( v )
			} );
		}

		//add popup
		editor.addCommand( 'wqc_shortcode_popup', wqc_open_modal );


		editor.addButton( 'wqc_shortcode_button', {
			title  : 'Quick Checkout',
			icon   : 'gpr dashicons-cart',
			onclick: wqc_open_modal
		} );


		//replace from shortcode to an image placeholder
		editor.on( 'BeforeSetcontent', function ( event ) {
			event.content = wqc_replace_shortcode( event.content );
		} );

		//replace from image placeholder to shortcode
		editor.on( 'GetContent', function ( event ) {
			event.content = wqc_restore_shortcode( event.content );
		} );


		//open popup on placeholder double click
		editor.on( 'DblClick', function ( e ) {
			var cls = e.target.className.indexOf( 'wp-woocommerce-quick-checkout' );
			var attributes = e.target.attributes['data-wqc-attr'].value;

			if ( e.target.nodeName == 'IMG' && cls > -1 ) {
				editor.execCommand( 'wqc_shortcode_popup', false, attributes );
			}
		} );

		/**
		 * Helper functions
		 */
		function getAttr( s, n ) {
			n = new RegExp( n + '=\"([^\"]+)\"', 'g' ).exec( s );
			return n ? window.decodeURIComponent( n[1] ) : '';
		}


		/**
		 * Google Places Replace Shortcode
		 *
		 * @param content
		 * @returns {XML|*|string|void}
		 */
		function wqc_replace_shortcode( content ) {
			return content.replace( /\[woocommerce-quick-checkout([^\]]*)\]/g, function ( all, attr, con ) {
				return wqc_shortcode_html( 'wp-woocommerce-quick-checkout', attr, con );
			} );
		}

		/**
		 * Restore Shortcodes
		 */
		function wqc_restore_shortcode( content ) {
			return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(<\/p>)*/g, function ( match, image ) {
				var data = getAttr( image, 'data-wqc-attr' );

				if ( data ) {
					return '<p>[' + sh_tag + data + ']</p>';
				}
				return match;
			} );
		}

		/**
		 * HTML
		 */
		function wqc_shortcode_html( cls, data, con ) {

			var placeholder = url + '/shortcode-placeholder.jpg';
			data = window.encodeURIComponent( data );

			return '<img src="' + placeholder + '" class="mceItem ' + cls + '" ' + 'data-wqc-attr="' + data + '" data-mce-resize="false" data-mce-placeholder="1" />';
		}

	} );


})();