/**
 *  Admin Project Metabox JS
 *
 *  @description: JS for the Quick Checkout admin metabox on single product posts
 */

jQuery.noConflict();
(function ($) {
	'use strict';

	$(document).ready(function () {


		//On/Off checkbox toggles
		$('#qc_enable_checkbox').on('change', function () {
			//Is enabled checked?
			if ($(this).prop('checked')) {
				$('#qc_disable_checkbox').prop('checked', false);
				//show customize rows
				show_customize_rows();
			} else {
				$('#qc_disable_checkbox').prop('checked', true);
				hide_customize_rows();
			}
		});

		//Disable Option
		$('#qc_disable_checkbox').on('change', function () {
			//Is Disable option checked?
			if ($(this).prop('checked')) {

				$('#qc_enable_checkbox').prop('checked', false);
				//hide elements
				hide_customize_rows();

			} else {
				//not checked, check enabled and show elements
				$('#qc_enable_checkbox').prop('checked', true);
				//show customize rows
				show_customize_rows();
			}
		});


		//If disabled is checked on doc ready hide elements
		//enabled checkbox not checked
		if ($('#qc_enable_checkbox').prop('checked') == false) {
			hide_customize_rows();
		}
		//disabled checkbox not checked
		else if ($('#qc_disable_checkbox').prop('checked') == false) {
			show_customize_rows();
		}

		if (!$('#qc_disable_checkbox').prop('checked') && !$('#qc_enable_checkbox').prop('checked') && $('.cmb_id_qc_global_info').length > 0 ) {
			show_customize_rows();
		}


		//Hide and Show Enable/Disable option depending on Global Setting
		if (jQuery('#qc_metabox').has('.cmb_id_qc_global_info').length > 0) {
			jQuery('.cmb_id_qc_enable_checkbox').css('display', 'none');
		} else {
			jQuery('.cmb_id_qc_disable_checkbox').css('display', 'none');
		}


		//Hide and Show Display Position
		$('#qc_single_product_action').on('change', function () {
			if ($('#qc_single_product_action').val() == 'reveal') {
				$('.cmb_id_qc_single_product_checkout_display_position').css('display', 'table-row');
			} else {
				$('.cmb_id_qc_single_product_checkout_display_position').css('display', 'none');
			}
		});


	}); //END DOC READY


	function show_customize_rows() {
		//show elements
		$('.cmb_id_qc_single_display_option').css('display', 'table-row');
		$('.cmb_id_qc_single_product_action').css('display', 'table-row');
		$('.cmb_id_qc_single_product_button_text').css('display', 'table-row');
		$('.cmb_id_qc_single_cart_action').css('display', 'table-row');
		$('.cmb_id_qc_single_product_image_button ').css('display', 'table-row');

		if (jQuery('#qc_single_product_action').val() == 'reveal') {
			jQuery('.cmb_id_qc_single_product_checkout_display_position').css('display', 'table-row');
		}

	}

	function hide_customize_rows() {
		//show elements
		$('.cmb_id_qc_single_display_option').css('display', 'none');
		$('.cmb_id_qc_single_product_action').css('display', 'none');
		$('.cmb_id_qc_single_product_checkout_display_position').css('display', 'none');
		$('.cmb_id_qc_single_product_button_text').css('display', 'none');
		$('.cmb_id_qc_single_cart_action').css('display', 'none');
		$('.cmb_id_qc_single_product_image_button ').css('display', 'none');
	}


})(jQuery);