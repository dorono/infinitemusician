<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @package   WC-Memberships/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2017, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Integration class for bbPress plugin
 *
 * @since 1.8.5
 */
class WC_Memberships_Integration_Bbpress {


	/**
	 * Load bbPress integration.
	 *
	 * @since 1.8.5
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'bbpress_init' ), 40 );
	}


	/**
	 * When hidden or private forums exists, no posts shows in the members area content.
	 *
	 * This is due to a bug in bbPress where the meta_query it adds via pre_get_posts excludes Hidden or Private forums for all queries that include the forum post type.
	 * Memberships is affected as it checks for posts of all post types when querying for a plan's restricted content.
	 * Our workaround merely consists of suppressing bbPress pre_get_posts filtering.
	 *
	 * TODO version 2.6 of bbPress might fix this, making this workaround useful only in bbPress versions 2.5.x and earlier {FN 2017-05-19}
	 *
	 * @internal
	 *
	 * @since 1.8.5
	 */
	public function bbpress_init() {

		$bbpress = bbpress();

		if (      isset( $bbpress->version )
		     &&   version_compare( $bbpress->version, '2.6', '<' )
		     &&   $this->is_members_area()
		     && ! is_bbpress() ) {

			remove_action( 'pre_get_posts', 'bbp_pre_get_posts_normalize_forum_visibility', 4 );
		}
	}


	/**
	 * Check if the current page is the members area.
	 *
	 * Note: this is not the best way to determine if we are on the members area, but bbPress itself with pre_get_posts filtering may prevent determining it via query vars.
	 * @see \WC_Memberships_Integration_Bbpress::bbpress_init()
	 * @see \WC_Memberships_Members_Area::is_members_area()
	 *
	 * @since 1.8.7
	 *
	 * @return bool
	 */
	private function is_members_area() {

		$is_endpoint_url = false;

		if ( isset( $_SERVER['REQUEST_URI'] ) && get_option( 'permalink_structure' ) ) {

			$members_area_sections = wc_memberships_get_members_area_sections();

			if ( ! empty( $members_area_sections ) ) {
				foreach ( array_keys( $members_area_sections ) as $members_area_section ) {
					if ( (bool) strpos( $_SERVER['REQUEST_URI'], $members_area_section ) ) {
						$is_endpoint_url = true;
						break;
					}
				}
			}

		} else {

			$is_endpoint_url = ! empty( $_GET['members_area'] ) && is_numeric( $_GET['members_area'] );
		}

		return $is_endpoint_url;
	}


}
