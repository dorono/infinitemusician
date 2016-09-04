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
 * Authorize.Net CIM Payment Gateway
 *
 * Handles all credit card purchases
 *
 * This is a direct credit card gateway that supports card types, charge,
 * and authorization
 *
 * @since 2.0.0
 */
class WC_Gateway_Authorize_Net_CIM_Credit_Card extends WC_Gateway_Authorize_Net_CIM {


	/**
	 * Initialize the gateway
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		parent::__construct(
			WC_Authorize_Net_CIM::CREDIT_CARD_GATEWAY_ID,
			wc_authorize_net_cim(),
			array(
				'method_title'       => __( 'Authorize.Net CIM', 'woocommerce-gateway-authorize-net-cim' ),
				'method_description' => __( 'Allow customers to securely pay using their credit cards with Authorize.Net CIM.', 'woocommerce-gateway-authorize-net-cim' ),
				'supports'           => array(
					self::FEATURE_PRODUCTS,
					self::FEATURE_CARD_TYPES,
					self::FEATURE_PAYMENT_FORM,
					self::FEATURE_TOKENIZATION,
					self::FEATURE_TOKEN_EDITOR,
					self::FEATURE_CREDIT_CARD_CHARGE,
					self::FEATURE_CREDIT_CARD_AUTHORIZATION,
					self::FEATURE_CREDIT_CARD_CAPTURE,
					self::FEATURE_DETAILED_CUSTOMER_DECLINE_MESSAGES,
					self::FEATURE_REFUNDS,
					self::FEATURE_VOIDS,
					self::FEATURE_CUSTOMER_ID,
					self::FEATURE_ADD_PAYMENT_METHOD,
				 ),
				'payment_type'       => self::PAYMENT_TYPE_CREDIT_CARD,
				'environments'       => array( 'production' => __( 'Production', 'woocommerce-gateway-authorize-net-cim' ), 'test' => __( 'Test', 'woocommerce-gateway-authorize-net-cim' ) ),
				'shared_settings'    => $this->shared_settings_names,
			)
		);
	}


	/**
	 * Add original transaction ID for capturing a prior authorization
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway_Direct::get_order_for_capture()
	 * @param WC_Order $order order object
	 * @return WC_Order object with payment and transaction information attached
	 */
	protected function get_order_for_capture( $order ) {

		$order = parent::get_order_for_capture( $order );

		$order->authorize_net_cim_capture_trans_id = $this->get_order_meta( $order->id, 'trans_id' );

		return $order;
	}


	/**
	 * Add Authorize.Net specific data to the order for performing a refund/void,
	 * all transactions require transaction ID and amount.
	 *
	 * Profile transactions require the customer profile ID and payment profile ID
	 *
	 * Non-Profile transactions require the last 4 digits and expiration date of
	 * the card used for the original transaction
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway::get_order_for_refund()
	 * @param int $order_id order ID
	 * @param float $amount refund amount
	 * @param string $reason refund reason text
	 * @return WC_Order|WP_Error order object on success, or WP_Error if missing required data
	 */
	protected function get_order_for_refund( $order_id, $amount, $reason ) {

		// set defaults
		$order = parent::get_order_for_refund( $order_id, $amount, $reason );

		if ( $this->get_order_meta( $order->id, 'payment_token' ) ) {

			// profile refund/void
			$order->refund->customer_profile_id = $this->get_order_meta( $order->id, 'customer_id' );
			$order->refund->customer_payment_profile_id = $this->get_order_meta( $order->id, 'payment_token' );

			if ( empty( $order->refund->customer_profile_id ) ) {
				$error_message = __( 'Order is missing customer profile ID.', 'woocommerce-gateway-authorize-net-cim' );
			}

		} else {

			// non-profile refund/void
			$order->refund->last_four = $this->get_order_meta( $order->id, 'account_four' );
			$order->refund->expiry_date = date( 'm-Y', strtotime( '20' . $this->get_order_meta( $order->id, 'card_expiry_date' ) ) );

			if ( empty( $order->refund->last_four ) || empty( $order->refund->expiry_date ) ) {

				$error_message = __( 'Order is missing the last four digits or expiration date of the credit card used.', 'woocommerce-gateway-authorize-net-cim' );
			}
		}

		if ( ! empty( $error_message ) ) {
			return new WP_Error( 'wc_' . $this->get_id() . '_refund_error', __( '%s Refund error - %s', 'woocommerce-gateway-authorize-net-cim' ), $this->get_method_title(), $error_message );
		}

		return $order;
	}


	/**
	 * Authorize.Net allows for an authorized & captured transaction that has not
	 * yet settled to be voided. This overrides the refund method when a refund
	 * request encounters the "Code 54 - The referenced transaction does not meet
	 * the criteria for issuing a credit." error and attempts a void instead.
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway::maybe_void_instead_of_refund()
	 * @param \WC_Order $order order
	 * @param \SV_WC_Payment_Gateway_API_Response $response refund response
	 * @return boolean true if
	 */
	protected function maybe_void_instead_of_refund( $order, $response ) {

		return ! $response->transaction_approved() && '3' == $response->get_transaction_response_code() && '54' == $response->get_transaction_response_reason_code();
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

			$defaults['account-number'] = '4007000000027';
			$defaults['expiry'] = '01/' . ( date( 'y' ) + 1 ); // TODO: remove when FW is 4.1.x+ @MR 2015-08-05
		}

		return $defaults;
	}


}
