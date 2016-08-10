
/* homestore.js */

(function( $ ) {

homestore_product_list_separators();
homestore_mega_menu_enhancements();
homestore_section_tabs();

$( document ).ready( function() {
	homestore_setup_sticky_navigation();
} );

/**
 * Creates the tabbed interface for product sections in homepage template
 * @return void
 */
function homestore_section_tabs() {
	var ref        = $( '#homestore-section-tabs-ref' );
	var sections   = $( '.storefront-recent-products, .storefront-featured-products, .storefront-popular-products, .storefront-on-sale-products' );
	var tabs       = $( '.homestore-section-tabs' );
	var classRegex = /storefront-(recent|featured|popular|on-sale)-products/;

	if ( tabs.length > 0 ) {
	    sections.detach();
	  	sections.find( '> .section-title' ).each( function( i, node ) {
	  		var title = $( node ), section = title.parent();
	  		var context, matches = section.attr( 'class' ).match( classRegex );
	  		if ( matches ) {
	  			section.attr( 'id', matches[0] );
	  			context = matches[1];
	  			tabs.append( '<li data-tab-icon="'+ context +'"><a href="#storefront-'+ context +'-products">'+ title.text() +'</a></li>' );
	  			title.remove();
	  		}
	  	} );
	}


	tabs.children().first().addClass( 'active' );

	var busy = false;

	tabs.find( 'a' ).click( function( e ) {
		if ( ! busy &&  ! $( this ).parent().hasClass( 'active' ) ) {
			var t = 100;
			var id = $( this ).attr( 'href' );
			var target = sections.filter( id );
			var current = tabs.next( '.storefront-product-section' );

			busy = true;

			$( this ).parent().addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );

			current.fadeOut( t, function() {
				current.detach();
				target.hide().insertBefore( ref );
				target.fadeIn( t, function() {
					busy = false;
				});
			} );
		}
		e.preventDefault();
	} );

	sections.first().insertBefore(ref).show();
}

/**
 * Adds a separator to the product lists
 * @return void
 */
function homestore_product_list_separators( ) {
	var colsRegex  = /^columns-(\d+)$/;
	$( '.storefront-product-section ul.products' ).each( function() {
		var cols = $( this ).parent().attr( 'class' ).split( /\s+/ ).reduce( function( match, c ) {
			if ( null === match && colsRegex.test( c ) ) {
				match = parseInt( c.match( colsRegex )[1], 10 );
			}
			return match;
		}, null );
		if ( null !== cols ) {
			$( this ).children( 'li.product:not(:first-child):nth-of-type('+ cols +'n+1)' ).before( '<li class="col-sep"></li>' );
		}
	} );
}

/**
 * Improves column display on Storefront Mega Menus
 * @return void
 */
function homestore_mega_menu_enhancements() {
	var idCounter = Math.floor( Math.random() * 1e6 );
	var style, css = [];

	$( '.smm-mega-menu .smm-row:only-of-type' ).each( function() {
		var cols = $( this ).children( 'div' ), len = cols.length;
		var margin = len > 3 ? '2em' : '3.5em';
		var id = 'smm-row-' + idCounter;
		var calcWidth = 'calc(100%/'+ len  + ' - ' + (len-1) + '*' + margin +'/' + len + ')';
		$( this ).attr( 'id', id );
		css.push( '#' + id + ' > div { float: left; margin-right: 0; width: -webkit-'+ calcWidth +'; width: '+ calcWidth +' }' );
		css.push( '#' + id + ' > div:nth-child(n+2) { margin-left: '+ margin +'; }' );
		css.push( '#' + id + ' > div:first-child { margin-left: 0; }' );
		idCounter++;
	} );

	if ( css.length > 0 ) {
		style = document.createElement( 'style' );
		style.setAttribute( 'id', 'homestore-mega-menu-css' );
		style.setAttribute( 'type', 'text/css' );
		style.setAttribute( 'media', 'screen' );
		style.innerHTML = css.join( '\n' );
		document.head.appendChild( style );
	}
}

/**
 * Properly configures sticky navigation
 * @return void
 */
function homestore_setup_sticky_navigation() {
	var wrapper = $( '#masthead .sticky-wrapper' );
	if ( wrapper.length > 0 ) {
		wrapper.siblings( '.hs-primary-navigation' ).appendTo( wrapper.find( '.sd-sticky-navigation' ) );
	}
}

})( jQuery );
