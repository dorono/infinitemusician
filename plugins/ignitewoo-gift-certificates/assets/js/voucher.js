jQuery( function($){

	"use strict";

	// on the order edit page, hook up the 'redeem all' vouchers button
	$('button.redeem_all_vouchers').live('click', function(){
		$('.voucher_redeem').each(function(index,el) {
			el = $(el);

			var date = new Date();
			var month = String(date.getMonth() + 1);
			var day = String(date.getDate());
			if (1 == month.length ) month = "0" + month;
			if (1 == day.length   ) day   = "0" + day;
			date = date.getFullYear() + "-" + month + "-" + day;

			if (!el.val()) {
				el.val(date);
			}
		});
	});


	/** Handler code for the voucher primary image voucher_image_meta_box() **/

	// save the default WP media browser callback
	window.send_to_editor_default = window.send_to_editor;

	// Uploading files
	var file_frame;
	var el;

	// original image dimensions
	var imageWidth  = voucher_js_params.primary_image_width;
	var imageHeight = voucher_js_params.primary_image_height;

	$('#set-voucher-image, #set-additional-image, #add-alternative-voucher-image').live('click', function(event){

		event.preventDefault();

		// save the element that was clicked on so we can set the image
		el = $(this);

		// If the media frame already exists, reopen it.
		if (file_frame) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: "Select an Image",
			button: {
				text: "Set Image",
			},
			multiple: false,
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			var attachment = file_frame.state().get('selection').first().toJSON();

			// grab the original image height/width for the image area select
			imageWidth  = attachment.width;
			imageHeight = attachment.height;

			if ('set-voucher-image' == el.attr('id')) {
				// primary (first page) voucher image
				$('#upload_image_id_0').val(attachment.id);
				$('#remove-voucher-image').show();
				$('img#voucher_image_0').attr('src', attachment.url);
			} else if ('set-additional-image' == el.attr('id')) {
				// additional (second page) voucher image
				$('#upload_additional_image_id_0').val(attachment.id);
				$('#set-additional-image').hide();
				$('#remove-additional-image').show();
				$('img#voucher_additional_image').attr('src', attachment.url);
			} else if ('add-alternative-voucher-image' == el.attr('id')) {
				var imgindex = $('#voucher_alternative_images li').size() + 1;

				$('#voucher_alternative_images').append(
					'<li class="alternative_image">' +
						'<a href="#" class="remove-alternative-voucher-image">' +
							'<img style="max-width:100px;max-height:100px;" src="' + attachment.url + '" />' +
							'<input type="hidden" name="upload_image_id[' + imgindex + ']" class="upload_image_id" value="' + attachment.id + '" />' +
							'<span class="overlay"></span>' +
						'</a>' +
					'</li>');
				set_remove_alternative_voucher_image_handler();
			}
		});

		// Finally, open the modal
		file_frame.open();
	});

	// remove the primary voucher image
	$('#remove-voucher-image').click(function() {

		$('#upload_image_id_0').val('');
		$('img#voucher_image_0').attr('src', '');
		$(this).hide();

		return false;
	});

	// redraw the positioned voucher fields on the primary image as the browser is scaled
	$(window).resize(function() {
		redraw_voucher_field_placeholders();
	});

	// draw any positioned voucher fields on the primary image
	function redraw_voucher_field_placeholders() {

		$('.field_pos').each(function(index,el) {
			
			el = $(el);
			var field = $('#field'+el.attr('id'));
			var image = $('#voucher_image_0');

			// if the image is removed, hide all fields
			if ('' == image.attr('src')) {
				if (field) field.hide();
				return;
			}

			// is the image resized due to the browser being shrunk?
			var scale = 1;
			if (imageWidth != image.width()) {
				scale = image.width() / imageWidth;
			}

			// get the scaled field position
			var position = el.val() ? el.val().split(',').map(function(n) { return parseInt(n) * scale }) : null;

			// create the field element if needed
			if (0 == field.length) {
				var name = el.prev().find('labelname').html();

				name = name.substr( 0, name.length - 9);
				$('#voucher_image_wrapper').append('<span id="field'+el.attr('id')+'" class="voucher_field" style="display:none;">'+name+'</span>');

				// clicking on the fields allows them to be edited
				$('#field'+el.attr('id')).click( function(el) {
					voucher_field_area_select(el.target.id.substr(6));  // remove the leading 'field_' to create the field name
				});

				field = $('#field'+el.attr('id'));
			}

			if (position) {
				field.css({left:position[0]+'px', top:position[1]+'px', width:position[2]+'px', height:position[3]+'px'});
				field.show();
			} else {
				field.hide();
			}

		});
	}

	// initial setup of the field placeholders
	redraw_voucher_field_placeholders();


	/** Handler code for the voucher data fields voucher_data_meta_box() **/

	// Note on the image area select:  I have to be very brute force
	// with this thing unfortunately and create/remove it with every
	// selection start, because otherwise I can't get the thing to
	// update the selection position, or to resize properly if the
	// browser window is resized.
	// And it still doesn't resize the selection box as the image is
	// resized due to the browser window shrinking/growing, but oh well
	// can't have it all

	var ias;

	// a coordinate field gained focus, enable the image area select overlay on the voucher main image and scroll it into the viewport if needed
	$('input.set_position').click(function() {
		voucher_field_area_select(this.id);
	});

	// display the imgAreaSelect tool on top of the primary voucher image so that the field_name position can be defined
	// field_name: ie 'product_name_post'
	function voucher_field_area_select(field_name) {
		// no voucher image
		if (!$("img#voucher_image_0").attr('src')) 
			return;

		// always clear the image select area, if any
		remove_img_area_select();

		// clicked 'done', return the button to normal and remove the area select overlay
		if ($('#'+field_name).val() == voucher_js_params.done_label) {
			$('#'+field_name).val(voucher_js_params.set_position_label);
			return;
		}

		// make sure the voucher field placeholder for this field is hidden
		$('#field_'+field_name).hide();

		var coords = $('#_' + field_name).val() ? $('#_' + field_name).val().split(',').map(function(n) { return parseInt(n) }) : [null,null,null,null];

		// reset all position set buttons and set the current
		$('input.set_position').val(voucher_js_params.set_position_label);
		$('#'+field_name).val(voucher_js_params.done_label);

		// show the associated options box
		$('#'+field_name).closest( '.voucher_options' ).show();

		// create the image area select element
		ias = $('img#voucher_image_0').imgAreaSelect({
			show: true,
			handles: true,
			instance: true,
			imageWidth: imageWidth,
			imageHeight: imageHeight,
			x1: coords[0],
			y1: coords[1],
			x2: coords[0] + coords[2],
			y2: coords[1] + coords[3],
			onSelectEnd: function(img, selection) { area_select(selection, field_name); }
		});

		// scroll into viewport if needed
		if ($(document).scrollTop() > $("img#voucher_image_0").offset().top + $("img#voucher_image_0").height() * (2/3)) {
			$('html, body').animate({
				scrollTop: $("#woocommerce-voucher-image").offset().top
			}, 500);
		}
	}

	// disable the img area select overlay
	function remove_img_area_select() {
		$('img#voucher_image_0').imgAreaSelect({remove:true});
		redraw_voucher_field_placeholders();
	}

	// voucher image selection made, save it to the coordinate field and show the 'remove' button
	function area_select(selection, field_name) {
		$('#_' + field_name).val(selection.x1 + ',' + selection.y1 + ',' + selection.width + ',' + selection.height);
		$('#remove_' + field_name).show();
	}

	// position remove button clicked
	$('input.remove_position').click(function() {
		$(this).hide();
		$('#_' + this.id.substr(7)).val('');  // remove the coordinates
		$('#' + this.id.substr(7)).val(voucher_js_params.set_position_label);
		remove_img_area_select();  // make sure the overlay is gone
		return;
	});

	// remove an alternative voucher image
	function set_remove_alternative_voucher_image_handler() {
		$('.remove-alternative-voucher-image').click(function() {

			var parent = $(this).parent();
			var current_field_wrapper = parent;

			$('input', current_field_wrapper).val('');
			$('img', current_field_wrapper).attr('src', '');
			parent.hide();

			return false;
		});
	}
	set_remove_alternative_voucher_image_handler();

	$('#remove-additional-image').click(function() {

		$('#upload_additional_image_id_0').val('');
		$('img#voucher_additional_image').attr('src', '');
		$(this).hide();
		$('#set-additional-image').show();

		return false;
	});
	
});

jQuery( document ).ready( function($) { 
	$('.voucher_code_select').select2({ placeholder: "Select a state" });
})