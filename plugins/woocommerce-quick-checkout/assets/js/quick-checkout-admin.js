/**
 *  Quick Checkout Admin JS
 *
 *  @description: Adds JS for the admin settings page
 */

jQuery(function () {

	//Shop Page Display Options Hide and Reveal for top level enable
	jQuery('input#woocommerce_quick_checkout_shop').change(function () {
		if (jQuery(this).is(':checked')) {
			jQuery('.qc_shop_action').closest('tr').show();
			jQuery('#woocommerce_quick_checkout_shop_image_hover').closest('tr').show();

			if(jQuery('#woocommerce_quick_checkout_shop_image_hover').is(':checked')) {
				jQuery('#woocommerce_quick_checkout_shop_cart_button_text').closest('tr').show();
			}
			if(jQuery('#woocommerce_quick_checkout_shop_cart_reveal').is(':checked')) {
				jQuery('#woocommerce_quick_checkout_shop_cart_link_text').closest('tr').show();
			}

		} else {
			jQuery('.qc_shop_action').closest('tr').hide();
			jQuery('#woocommerce_quick_checkout_shop_cart_button_text').closest('tr').hide();
			jQuery('#woocommerce_quick_checkout_checkout_display_position').closest('tr').hide();
			jQuery('#woocommerce_quick_checkout_shop_image_hover').closest('tr').hide();
			jQuery('#woocommerce_quick_checkout_shop_cart_link_text').closest('tr').hide();
		}
	}).change();


	//Product Post Display Options Hide and Reveal for top level enable
	jQuery('input#woocommerce_quick_checkout_product').change(function () {
		if (jQuery(this).is(':checked')) {
			jQuery('.qc_product_button_display').closest('tr').show();
			jQuery('.qc_product_action').closest('tr').show();
			jQuery('#woocommerce_quick_checkout_product_image_button').closest('tr').show();
			jQuery('#woocommerce_quick_checkout_product_button_text').closest('tr').show();

		} else {
			jQuery('.qc_product_button_display').closest('tr').hide();
			jQuery('.qc_product_action').closest('tr').hide();
			jQuery('#woocommerce_quick_checkout_product_image_button').closest('tr').hide();
			jQuery('#woocommerce_quick_checkout_product_checkout_display_position').closest('tr').hide();
			jQuery('#woocommerce_quick_checkout_product_button_text').closest('tr').hide();

		}
	}).change();


	/**
	 * Shop Action Show/Hide
	 */
	jQuery('select.qc_shop_action').change(function () {
		if (jQuery(this).val() == 'reveal') {
			jQuery('#woocommerce_quick_checkout_checkout_display_position').closest('tr').show();
		} else {
			jQuery('#woocommerce_quick_checkout_checkout_display_position').closest('tr').hide();
		}
	}).change();

	/**
	 * Shop Button Text Show/Hide
	 */
	jQuery('#woocommerce_quick_checkout_shop_image_hover').change(function () {
		if (jQuery(this).is(':checked')) {
			jQuery('#woocommerce_quick_checkout_shop_cart_button_text').closest('tr').show();
		} else {
			jQuery('#woocommerce_quick_checkout_shop_cart_button_text').closest('tr').hide();
		}
	}).change();

	/**
	 * Shop Link Text Show/Hide
	 */
	jQuery('#woocommerce_quick_checkout_shop_cart_reveal').change(function () {
		if (jQuery(this).is(':checked')) {
			jQuery('#woocommerce_quick_checkout_shop_cart_link_text').closest('tr').show();
		} else {
			jQuery('#woocommerce_quick_checkout_shop_cart_link_text').closest('tr').hide();
		}
	}).change();


	/**
	 * Product Post Action Hide
	 */
	jQuery('select.qc_product_action').change(function () {
		if (jQuery(this).val() == 'reveal') {
			jQuery('#woocommerce_quick_checkout_product_checkout_display_position').closest('tr').show();
		} else {
			jQuery('#woocommerce_quick_checkout_product_checkout_display_position').closest('tr').hide();
		}
	}).change();


});

