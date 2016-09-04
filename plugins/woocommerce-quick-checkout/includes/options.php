<?php

/**
 * Get Google Maps Builder Themes
 *
 * @return array
 */
function wqc_get_widget_themes() {
	$options = array(
		__( 'Bare Bones', 'wqc' ),
		__( 'Minimal Light', 'wqc' ),
		__( 'Minimal Dark', 'wqc' ),
		__( 'Shadow Light', 'wqc' ),
		__( 'Shadow Dark', 'wqc' ),
		__( 'Inset Light', 'wqc' ),
		__( 'Inset Dark', 'wqc' )
	);

	return apply_filters( 'wqc_widget_themes', $options );
}

/**
 * Get Google Maps Builder Themes
 *
 * @return array
 */
function wqc_get_widget_cache_options() {
	$options = array(
		__( 'None', 'wqc' ),
		__( '1 Hour', 'wqc' ),
		__( '3 Hours', 'wqc' ),
		__( '6 Hours', 'wqc' ),
		__( '12 Hours', 'wqc' ),
		__( '1 Day', 'wqc' ),
		__( '2 Days', 'wqc' ),
		__( '1 Week', 'wqc' )
	);

	return apply_filters( 'wqc_widget_cache_options', $options );
}

/**
 *  Google Places Reviews Admin Tooltips
 *
 * @param $tip_name
 *
 * @return bool|string
 */
function wqc_admin_tooltip( $tip_name ) {

	$tip_text = '';

	//Ensure there's a tip requested
	if ( empty( $tip_name ) ) {
		return false;
	}

	switch ( $tip_name ) {
		case 'title':
			$tip_text = __( 'The title text appears at the very top of the widget above all other elements.', 'wqc' );
			break;
		case 'autocomplete':
			$tip_text = __( 'Enter the name of your Google Place in this field to retrieve it\'s Google Place ID. If no information is returned there you may have a conflict with another plugin or theme using Google Maps API.', 'wqc' );
			break;
		case 'place_type':
			$tip_text = __( 'Specify how you would like to lookup your Google Places. Address instructs the Place Autocomplete service to return only geocoding results with a precise address. Establishment instructs the Place Autocomplete service to return only business results. The Regions type collection instructs the Places service to return any result matching the following types: locality, sublocality, postal_code, country, administrative_area_level_1, administrative_area_level_2.', 'wqc' );
			break;
		case 'location':
			$tip_text = __( 'This is the name of the place returned by Google\'s Places API.', 'wqc' );
			break;
		case 'place_id':
			$tip_text = __( 'The Google Place ID is a textual identifier that uniquely identifies a place and can be used to retrieve information about the place. This option is set using the "Location Lookup" field above.', 'wqc' );
			break;
		case 'review_filter':
			$tip_text = __( 'Filter bad reviews to prevent them from displaying. Please note that the Google Places API only allows for up to 5 total reviews displayed per location. This option may limit the total number further.', 'wqc' );
			break;
		case 'review_limit':
			$tip_text = __( 'Limit the number of reviews displayed for this location to a set number.', 'wqc' );
			break;
		case 'reviewers_link':
			$tip_text = __( 'Toggle on or off the link on the reviews name to their Google+ page.', 'wqc' );
			break;
		case 'review_characters':
			$tip_text = __( 'Some reviews may be very long and cause the widget to have a very large height. This option uses JavaScript to expand and collapse the text.', 'wqc' );
			break;
		case 'review_char_limit':
			$tip_text = __( 'Set the character limit for this review widget. Values are in pixels.', 'wqc' );
			break;
		case 'widget_style':
			$tip_text = __( 'Choose from a set of predefined widget styles. Want to style your own? Set it to \'Bare Bones\' for easy CSS styling.', 'wqc' );
			break;
		case 'hide_header':
			$tip_text = __( 'Disable the main business information profile image, name, overall rating. Useful for displaying only ratings in the widget.', 'wqc' );
			break;
		case 'hide_out_of_rating':
			$tip_text = __( 'Hide the text the appears after the star image displaying \'x out of 5 stars\'. The text will still be output because it is important for SEO but it will be hidden with CSS.', 'wqc' );
			break;
		case 'google_image':
			$tip_text = __( 'Prevent the Google logo from displaying in the reviews widget.', 'wqc' );
			break;
		case 'cache':
			$tip_text = __( 'Caching data will save Google Place data to your database in order to speed up response times and conserve API requests. The suggested settings is 1 Day.', 'wqc' );
			break;
		case 'disable_title_output':
			$tip_text = __( 'The title output is content within the \'Widget Title\' field above. Disabling the title output may be useful for some themes.', 'wqc' );
			break;
		case 'target_blank':
			$tip_text = __( 'This option will add target=\'_blank\' to the widget\'s links. This is useful to keep users on your website.', 'wqc' );
			break;
		case 'no_follow':
			$tip_text = __( 'This option will add rel=\'nofollow\' to the widget\'s outgoing links. This option may be useful for SEO.', 'wqc' );
			break;
		case 'alignment':
			$tip_text = __( 'Choose whether to float the widget to the right or left, or not at all. This is helpful for integrating within post content so text wraps around the widget if wanted. Default value is \'none\'.', 'wqc' );
			break;
		case 'max_width':
			$tip_text = __( 'Define a max-width property for the widget. Dimension value can be in pixel or percentage. Default value is \'250px\'.', 'wqc' );
			break;
		case 'pre_content':
			$tip_text = __( 'Output content before the main widget content. Useful to provide introductory text.', 'wqc' );
			break;
		case 'post_content':
			$tip_text = __( 'Output content after the main widget content. Useful to provide a button or custom text inviting the user to perform an action or read a message.', 'wqc' );
			break;
		case 'custom_avatar':
			$tip_text = __( 'If you are not happy with the image pulled from the Google API then you may upload your own. The recommended size is 60x60',  'wqc' );
			break;
	}

	return '<img src="' . WQC_PLUGIN_URL . '/assets/img/help.png" title="' . $tip_text . '" class="tooltip-info" width="16" height="16" />';

}