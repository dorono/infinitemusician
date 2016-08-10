<?php
/**
 * Homestore engine room
 *
 * @package homestore
 */

/**
 * Set the theme version number as a global variable
 */
 $theme              = wp_get_theme( 'homestore' );
 $homestore_version  = $theme['Version'];

/**
 * Load the individual classes required by this theme
 */
require_once( 'inc/class-homestore.php' );
require_once( 'inc/class-homestore-customizer.php' );
require_once( 'inc/class-homestore-template.php' );
require_once( 'inc/class-homestore-integrations.php' );
require_once( 'inc/plugged.php' );

/**
 * Do not add custom code / snippets here.
 * While Child Themes are generally recommended for customisations, in this case it is not
 * wise. Modifying this file means that your changes will be lost when an automatic update
 * of this theme is performed. Instead, add your customisations to a plugin such as
 * https://github.com/woothemes/theme-customisations
 */