<?php
/**
 * WooCommerce Authorize.Net CIM Gateway
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net CIM Gateway to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net CIM Gateway for your
 * needs please refer to http://docs.woothemes.com/document/authorize-net-cim/
 *
 * @package   WC-Gateway-Authorize-Net-CIM/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Authorize.Net CIM eCheck Payment Gateway
 *
 * Handles all purchases with eChecks
 *
 * This is a direct check gateway
 *
 * @since 2.0.0
 */
class WC_Gateway_Authorize_Net_CIM_eCheck extends WC_Gateway_Authorize_Net_CIM {


	/**
	 * Initialize the gateway
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		parent::__construct(
			WC_Authorize_Net_CIM::ECHECK_GATEWAY_ID,
			wc_authorize_net_cim(),
			array(
				'method_title'       => __( 'Authorize.Net CIM eCheck', 'woocommerce-gateway-authorize-net-cim' ),
				'method_description' => __( 'Allow customers to securely pay using their checking/savings accounts with Authorize.Net CIM.', 'woocommerce-gateway-authorize-net-cim' ),
				'supports'           => array(
					self::FEATURE_PRODUCTS,
					self::FEATURE_PAYMENT_FORM,
					self::FEATURE_TOKENIZATION,
					self::FEATURE_TOKEN_EDITOR,
					self::FEATURE_DETAILED_CUSTOMER_DECLINE_MESSAGES,
					self::FEATURE_CUSTOMER_ID,
					self::FEATURE_ADD_PAYMENT_METHOD,
				 ),
				'payment_type'       => self::PAYMENT_TYPE_ECHECK,
				'environments'       => array( 'production' => __( 'Production', 'woocommerce-gateway-authorize-net-cim' ), 'test' => __( 'Test', 'woocommerce-gateway-authorize-net-cim' ) ),
				'shared_settings'    => $this->shared_settings_names,
			)
		);
	}


	/**
	 * Return the default values for this payment method, used to pre-fill
	 * an authorize.net valid test account number when in testing mode
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway::get_payment_method_defaults()
	 * @return array
	 */
	public function get_payment_method_defaults() {

		$defaults = parent::get_payment_method_defaults();

		if ( $this->is_test_environment() ) {

			$defaults['routing-number'] = '121000248';
			$defaults['account-number'] = '8675309';
		}

		return $defaults;
	}


}
