(function($) {
	$( document ).ready( function() {
		
		/**
		 * PREVIEW EMAIL - GENERAL STUFF.
		 */

		/**
		 * Preview Email Main Admin Page.
		 */
		
		
		

		/**
		 * Show/Hide the Customizer Editor, and update the Customizer Sections inside of the editor.
		 */
		
		// Ping a `show_settings_composer` when `Email to show` or `Template to show` is changed.
		jQuery('#ec_email_type, #ec_email_order').change(function() {
			reload_preview();
			hide_settings_composer();
			show_settings_composer();
		});
		reload_preview();

		function show_settings_composer() {
			
			// Get values.
			var val_email_type             = jQuery("#ec_email_type").val();
			var val_email_template         = jQuery("#ec_email_template").val();
			var val_email_order            = jQuery("#ec_email_order").val();
			var val_billing_email          = jQuery('#ec_email_order option:selected').attr('data-order-email');
			var val_email_template_preview = jQuery("#ec_email_template_preview").val();
			var val_approve_preview        = jQuery("#ec_approve_preview").val();
			
			// First hide everything.
			jQuery(".ec_settings_form").hide();
			jQuery(".ec_settings_form").find('.section').hide();
			
			// Show the chosen email template's form (e.g. Deluxe, Supreme, etc).
			$ec_settings_form_show = jQuery( "#ec_settings_form_" + val_email_template );
			jQuery( $ec_settings_form_show ).show();
			
			// Show the chosen email type's panels (e.g. New Order, Cancelled Order, etc).
			$ec_settings_form_show.find('.section').filter('[data-ec-template-kind="' + val_email_type + '"]').show();
			$ec_settings_form_show.find('.section').filter('[data-ec-template-kind="all"]').show();

			// Hide the main editing block on change of template
			jQuery(".ec-admin-panel-edit-content").removeClass('ec_active');

			// Show the edit button if there are fields showing to edit
			if ( $ec_settings_form_show.find('.section').filter(':visible') ) {
				jQuery("#ec_edit_content_controls").removeClass('disabled');
			}
			else{
				jQuery("#ec_edit_content_controls").addClass('disabled');
			}
		}
		
		function hide_settings_composer() {
			
			jQuery("#ec_edit_content_controls").addClass('disabled');
			jQuery(".ec-admin-panel-edit-content").removeClass('ec_active');
		}
		
		function toggle_settings_composer() {
			
			if ( jQuery("#ec_edit_content_controls").hasClass('disabled') )
				show_settings_composer();
			else
				hide_settings_composer();
		}
		
		// Ping a `show_settings_composer` so that the correct feilds are shown on startup.
		show_settings_composer();


		/**
		 * Show/Hide the Customizer - Edit Panel.
		 */
		
		// Show.
		function show_edit_panel() {
			
			jQuery(".ec-admin-panel-edit-content").addClass('ec_active');
			
			window.location.hash = 'customize';
		}
		
		// Hide.
		function hide_edit_panel() {
			
			if ( edited ) {
				var confirm_result = confirm("Are you sure you want to close without saving");
				
				if ( ! confirm_result ) {
					return;
				}
			}
			
			jQuery('.ec-admin-panel-edit-content').removeClass('ec_active');

			window.location.hash = '';
		}
		
		// Toggle.
		function toggle_edit_panel() {
			
			if( ! jQuery('.ec-admin-panel-edit-content').hasClass('ec_active') ) {
				
				show_edit_panel();
			}
			else {
				
				hide_edit_panel();
			}
		}

		// Peek (Breifly Hide)
		jQuery(".hide_settings").hover(
			function (event) {
				//jQuery(".ec-admin-panel-edit-content").animate({opacity:0}, 100);
				jQuery(".ec-admin-panel-edit-content").addClass('ec_force_hide');
			},
			function (event) {
				//jQuery(".ec-admin-panel-edit-content").animate({opacity:1}, 100);
				jQuery(".ec-admin-panel-edit-content").removeClass('ec_force_hide');
			}
		);
		
		jQuery("#close_edit_settings").click( function(event) {
			
			toggle_edit_panel();

			return false;
		});
		

		/**
		 * Show/Hide the Customizer Accordions.
		 */
		
		// Accordions open/close
		jQuery(document).on( 'click', '.section h3', function() {

			// Get elements.
			$section            = jQuery(this).parent('.section');
			$section_inner      = jQuery(this).parent('.section').find('.section-inner');
			$section_holder     = jQuery(this).parent('.section').parent('.ec_settings_form_sub');
			$edit_content_panel = jQuery('.ec-admin-panel-edit-content');
			$visible_panels     = $edit_content_panel.find('.section:visible');
			
			
			/**
			 * Set the height of the sections panel to make sure it uses the remaining vertical height of the window.
			 */
			
			$i = 0;
			$visible_panels.each(function(){
				// Get the current elements position so we know how many elements above.
				$i++;
				if ( $(this).is( $section ) ) return false;
			});
			
			// Get the heights of the element above the current one.
			$elements_above_height = ( ( $i + 1 ) * $section.find('h3').outerHeight() ) + $edit_content_panel.find('.edit-top-controls').outerHeight();
			$remaining_height_for_panel = jQuery(window).height() - $elements_above_height;
			
			// If the resulting height is too small then limit it.
			$remaining_height_for_panel =  ( $remaining_height_for_panel > 120 ) ? $remaining_height_for_panel : 120 ;
			
			// Set the max height.
			$section_inner.css( 'max-height', $remaining_height_for_panel );
			
			
			/**
			 * Close the other elements.
			 */
			
			jQuery('.section-inner').not($section_inner).slideUp();
			jQuery('.section').not($section).removeClass('ec-active');
			// document.location.hash = 'customize';
			
			
			/**
			 * Open the chosen element.
			 */
			
			if ( $section.hasClass('ec-active') ) {
				
				$section.removeClass('ec-active');
				$section_inner.slideUp();
				// document.location.hash = 'customize';
			}
			else{
				
				$section.addClass('ec-active');
				$section_inner.slideDown();
				// document.location.hash = 'customize/' + $section_holder.attr('id');
			}
		});

		// Close all the panels to start
		jQuery('.section-inner').slideUp();


		
		// Ajax saving of fields
		jQuery("#send_test").on("click", function () {
			
			// Get values.
			var val_email_type      = jQuery("#ec_email_type").val();
			var val_email_type_name = jQuery( "#ec_email_type :selected" ).text().replace(/\w\S*/g, function(txt) {return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase(); } ).replace(/(\r\n|\n|\r)/gm,"");
			var val_email_template  = jQuery("#ec_email_template").val();
			var val_email_order     = jQuery("#ec_email_order").val();
			var val_billing_email   = jQuery('#ec_email_order:selected').attr('data-order-email');
			var val_testing_email   = jQuery("#ec_send_email").val();
			

			form_data = "";

			form_data	+= '';
			form_data	+= 'action=ec_send_email';

			form_data	+= '&';
			form_data	+= 'ec_email_type=' + val_email_type;

			form_data	+= '&';
			form_data	+= 'ec_email_template=' + val_email_template;

			form_data	+= '&';
			form_data	+= 'ec_email_order=' + val_email_order;

			form_data	+= '&';
			form_data	+= 'ec_email_addresses=' + val_testing_email;

			form_data	+= '&';
			form_data	+= jQuery(".ec_settings_form:visible").serialize();
			
			ec_loading({text: "Sending Email"});

			jQuery.ajax({
				type:		"post",
				dataType:	"json",
				url:		woocommerce_email_control.ajaxurl,
				data:		form_data,
				success: function( data ) {
					ec_loading_end();
					ec_notify("Email Sent!", {id: "second-thing", size: "medium"});
				},
				error: function(xhr, status, error) {
					ec_loading_end();
					ec_notify("Email sending failed!", {id: "second-thing", size: "medium"});
				}
			});

			return false;
		});
		
		// Returns a function, that, as long as it continues to be invoked, will not
		// be triggered. The function will be called after it stops being called for
		// N milliseconds. If `immediate` is passed, trigger the function on the
		// leading edge, instead of the trailing.
		function debounce(func, wait, immediate) {
			var timeout;
			return function() {
				var context = this, args = arguments;
				var later = function() {
					timeout = null;
					if (!immediate) func.apply(context, args);
				};
				var callNow = immediate && !timeout;
				clearTimeout(timeout);
				timeout = setTimeout(later, wait);
				if (callNow) func.apply(context, args);
			};
		}

		
		var update_preview = debounce(function() {

			iframe_src = jQuery('#preview-email-template-iframe').attr("src");
			submit_form = jQuery(this).closest("form");

			//console.log( submit_form.attr('class') );

			submit_form.attr("action", iframe_src);
			submit_form.attr("target", "my-iframe");
			submit_form.attr("method", "post");

			submit_form.submit();

			set_edited(this);

			return false;
			
		}, 300);
		
		// OLD.
		// jQuery('.ec_settings_form .main-controls-element input, .ec_settings_form .main-controls-element textarea').keyup( update_preview );
		// jQuery('.ec_settings_form .main-controls-element select').change( update_preview );
		
		// NEW.
		jQuery('.ec_settings_form .main-controls-element')
			.find(' select,  input,  textarea')
			.on( 'keyup change', update_preview );
		
		var edited = false;
		function set_edited(element) {
			
			jQuery(element).closest("form").find("#save_edit_settings")
			.attr('disabled',false)
			.attr('value', 'Save & Publish');

			edited = true;
		}
		function clear_edited(element) {
			
			jQuery(element).closest("form").find("#save_edit_settings")
			.attr('disabled',true)
			.attr('value', 'Saved');
			
			edited = false;
		}
		
		// Ajax Save Edit Settings
		jQuery(".save_edit_settings").click( function (event) {

			if (edited) {
				var confirm_result = confirm("Are you sure you want save these changes");
				if (!confirm_result)
					return;
				
				element		= this;
				form		= jQuery(this).closest("form");
				form_data	= form.serialize();
				form_data	= "action=save_edit_email&" + form_data;

				jQuery.ajax({
					type:		"post",
					url:		woocommerce_email_control.ajaxurl,
					data:		form_data,
					success:	function( data ) {
						
						clear_edited(element);
						//reload_preview();

					},
					error:		function(xhr, status, error) {
						console.log(xhr, status, error);
					}
				});
				
			}
			
			return false;
		});
		
		
		// Show Send button only when somone types in the field
		jQuery('#ec_send_email').keyup(function() {
			
			if ( !jQuery.trim( jQuery("#ec_send_email").val() ) ) {
				//close
				jQuery('#send_test').fadeOut()
				.parent(".main-controls-element").removeClass("element-open");
			}
			else{
				//open
				jQuery('#send_test').fadeIn()
				.parent(".main-controls-element").addClass("element-open");
				
				jQuery("#preview-email-template-iframe")[0].contentWindow.ec_set_to_email( jQuery("#ec_send_email").val() );
			}
			
		});
		
		if ( jQuery('#ec_send_email').val() != "" ) {
			
			jQuery('#send_test').fadeIn();
			
		}
		
		// Ajax saving of fields
		jQuery(".header_info_userspecifc").on("change", function () {
			
			//jQuery("#preview-email-template-iframe")[0].contentWindow.ec_toggle_header_info();
			
			ec_toggle_header_info();
			
			var field_name	= jQuery(this).attr("name");
			var field_value	= jQuery(this).val();
			
			if ( jQuery(this).attr('type') == "checkbox" ) {
				if ( jQuery( "input[name='" + jQuery(this).attr('name') + "']" ).length == 1 ) {
					
					if ( jQuery(this).is(":checked") ) field_value = "on";
					else field_value = "off";
										
				}
			}

			jQuery.ajax({
				type:		"post",
				dataType:	"json",
				url:		woocommerce_email_control.ajaxurl,
				data: {
					action:			"save_meta",
					field_name:		field_name,
					field_value:	field_value
					//nonce:		nonce
				},
				success: function( data ) {
					
				},
				error: function(xhr, status, error) {
					
				}
			});
		});
		
		// Ajax saving of options
		function save_option ( option_name, option_value, complete ) {
			
			jQuery.ajax({
				type:		"post",
				url:		woocommerce_email_control.ajaxurl,
				data: {
					action:			"save_option",
					field_name:		option_name,
					field_value:	option_value
					//nonce:		nonce
				},
				success: function( data ) {
					
					if (typeof complete !== 'undefined') complete();
					
				},
				error: function(xhr, status, error) {
					console.log(xhr, status, error);
				}
			});

		}
		
		jQuery("#ec_edit_email").on('click', function(event) {
			
			return false;
			
		});
		
		// Preview Email Template Selector
		// ----------------------------------------
		
		jQuery('#ec_email_template').on("change", function () {
			
			jQuery('#template-commit').css({display:"block"});
			jQuery('#ec_email_template_preview').val( jQuery(this).val() );
			
			reload_preview();
			
			hide_settings_composer();
			
			return false;
		});
		
		
		jQuery('#ec_save_email_template').on("click", function () {
			
			var confirm_result = confirm("Are you sure you want to use this template for all future emails sent from your site");
			if (confirm_result) {
				jQuery('#template-commit').css({display:"none"});

				save_option ( "ec_template", jQuery('#ec_email_template_preview').val() /*, function() { reload_preview(); }*/ );

				jQuery('#ec_email_template_active').val( jQuery('#ec_email_template').val() );
				jQuery('#ec_email_template_preview').val("");
				
				show_settings_composer();
			}
			else{
				jQuery('#ec_cancel_email_template').click();
			}

			return false;
		});
		
		jQuery('#ec_cancel_email_template').on("click", function () {
			
			jQuery('#template-commit').css({display:"none"});
			
			jQuery('#ec_email_template').val( jQuery('#ec_email_template_active').val() );
			jQuery('#ec_email_template_preview').val("");
			
			reload_preview();
			
			show_settings_composer();
			
			return false;
		});
		
		// Handle Default re-populating
		jQuery('.reset-to-default').on( 'click', function () {
			
			// Get elements.
			$input = jQuery(this).closest('.main-controls-element').find('input, textarea');

			if ( $input.is(':checkbox') ) {
				$input = $input.filter(':checkbox');
				$input
					.prop( 'checked', 'checked' )
					.keyup();
			}
			else {
				$input
					.val( jQuery(this).attr("data-default") )
					.keyup();
			}

			return false;
		});
		
		// Initialise Color Pickers
		jQuery('.ec-colorpick').iris({
			change: function(a, b) {
				jQuery(this).css({ backgroundColor: b.color.toString() }).keyup();
			},
			hide: !0,
			border: !0,
		})
		.each(function() {
			jQuery(this).css({ backgroundColor: jQuery(this).val() });
		})
		.click(function() {
			jQuery('.iris-picker').hide();
			jQuery(this).parents('.main-controls-element').find('.iris-picker').show();
		});

		jQuery('body').click(function() {
			jQuery('.iris-picker').hide();
		});

		jQuery('.ec-colorpick').click(function(e) {
			
			// Get elements.
			var $parent_element = $(this).parents('.main-controls-element.forminp-color.ec-half');
			
			// If color-picker-panel is far to the right, then mov eit to the left, so it fit's inside the scroll panel.
			if ( $parent_element.length ) {
				var offset = $parent_element.position();
				if ( 100 < offset.left ) {
					$parent_element.addClass('ec-half-right');
				}
			}
			
			// This is important - stops the color picker opening, then closing.
			e.stopPropagation();
		});
		
		// Preview Email Upload Image
		// ----------------------------------------
		var custom_uploader;
		jQuery('.upload_image_button').click(function(event) {
			
			this_button	= jQuery(this);
			this_field	= this_button.parent().find('.upload_image');

			event.preventDefault();

			// If the uploader object has already been created, reopen the dialog
			if (custom_uploader) {
				custom_uploader.open();
				return;
			}

			// Extend the wp.media object
			custom_uploader = wp.media.frames.file_frame = wp.media({
				title: 'Choose Image',
				button: {
					text: 'Choose Image'
				},
				multiple: false
			});

			// When a file is selected, grab the URL and set it as the text field's value
			custom_uploader.on('select', function() {
				attachment = custom_uploader.state().get('selection').first().toJSON();
				console.log( attachment.url, this_field );
				this_field.val( attachment.url );
				
				this_field.keyup();
				set_edited(this);
				
			});

			// Open the uploader dialog
			custom_uploader.open();

			return false;

		});
		
		// Preview Email Edit Content
		// ----------------------------------------
		jQuery('#ec_edit_content').on("click", function () {
			
			toggle_edit_panel();
			
			return false;
		});
		
		// Open all links in the preview in a new tab.
		jQuery('#ec-template .main-content a:not("#ec_approve_preview_button")')
			.attr( 'target', 'wc_email_customizer_window' );
		
		// Dismiss compatability warning in email template.
		jQuery("#ec_approve_preview_button").on('click', function(event) {
			
			var email_id = jQuery(this).data('approve-preview');
			jQuery( parent.document ).find( "#ec_approve_preview" ).val(email_id);
			parent.reload_preview();
			return false;
		});
		
		// Preview Email Preview Template
		// ----------------------------------------
		
		// Open-Close the header info
		jQuery(".hide-icon").on('click', function(event) {

			// jQuery(".header_info_userspecifc").click();
			jQuery( parent.document ).find( ".header_info_userspecifc" ).click();
			
			return false;
		});
		
		function ec_notify(content, options) {
			
			// Set up default options
			var defaults = {
				id:				false,
				display_time:	5000,
				size:			"small"
			};
			options = jQuery.extend({}, defaults, options);
			
			
			if ( !jQuery("#cxectrl-notification-holder").length )
				jQuery("body").append( '<div id="cxectrl-notification-holder"></div>' );
			
			var current_element = jQuery(".cxectrl-notification-" + options.id );
			
			if ( current_element.length ) {
				current_element.animate({ "margin-top": - current_element.outerHeight(true) +"px", "top": (current_element.outerHeight(true) / 1.5 ) +"px", opacity: 0 }, { duration:300, complete: function() {
					current_element.remove();
				}});
			}
			
			var new_element = jQuery('<div/>', {
				style: 'display:none;',
				class: "cxectrl-notification cxectrl-notification-" + options.id,
				text: content
			});
			
			jQuery("#cxectrl-notification-holder").append(new_element);
			
			new_element.addClass('cxectrl-notification-' + options.size );
			
			new_element.css({ "top": (new_element.outerHeight(true) / 1.5 ) + "px", opacity:0, marginLeft: - (new_element.outerWidth(true) /2 ) });
			new_element.animate({"top": "0px", opacity:1, display:"block" }, 300);
						
			
			var element_timeout = setTimeout(function() {
				
				new_element.animate({ "margin-top": - new_element.outerHeight(true) +"px", "top": (new_element.outerHeight(true) / 1.5 ) +"px", opacity: 0 }, { duration:300, complete: function() {
					new_element.remove();
				}});
				
			}, options.display_time);
			
		}
		
		// Loading Testing
		if (false) {
			
			time_interval = 3000;
			setTimeout(function() { /* ec_loading(); */ }, 0 * time_interval);
			setTimeout(function() { /* ec_loading( { text: "Loadski!..." } ); */ }, 1 * time_interval);
			setTimeout(function() { /* ec_loading_end(); */ }, 2 * time_interval);
			
			time_interval = 300;
			setTimeout(function() { /* ec_notify("First thing done!", {id: "first-thing"}); */ }, 0 * time_interval);
			setTimeout(function() { /* ec_notify("Second thing done!", {id: "second-thing", size: "large"}); */ }, 1 * time_interval);
			setTimeout(function() { /* ec_notify("First thing done again!", {id: "first-thing"}); */ }, 2 * time_interval);
			setTimeout(function() { /* ec_notify("Third thing done!", {id: "third-thing"}); */ }, 3 * time_interval);
			setTimeout(function() { /* ec_notify("Fourth thing done!", {id: "fourth-thing", display_time:10000}); */ }, 4 * time_interval);
			setTimeout(function() { /* ec_notify("Fifth thing done!", {id: "fifth-thing", size: "medium"} ); */ }, 5 * time_interval);
			setTimeout(function() { /* ec_notify("Third thing done again!", {id: "third-thing"} ); */ }, 6 * time_interval);
			setTimeout(function() { /* ec_notify("Sixth thing done!", {id: "sixth-thing"} ); */ }, 7 * time_interval);
		}
			
		
		// Deep link to the cusomizer panel on startup.
		if ( document.location.hash ) {

			location_array = document.location.hash.split('/');

			if ( location_array[0] == '#customize' ) {
				jQuery('#ec_edit_content').click();
			}
		}
	});
	
})( jQuery );


function reload_preview() {
	
	// Get values.
	var val_email_type             = jQuery("#ec_email_type").val();
	var val_email_template         = jQuery("#ec_email_template").val();
	var val_email_order            = jQuery("#ec_email_order").val();
	var val_billing_email          = jQuery('#ec_email_order option:selected').attr('data-order-email');
	var val_email_template_preview = jQuery("#ec_email_template_preview").val();
	var val_approve_preview        = jQuery("#ec_approve_preview").val();

	// Reload the Preview src
	var new_src = "";
	new_src += woocommerce_email_control.admin_url;
	new_src += "admin.php?";
	new_src += "page=woocommerce_email_control";
	new_src += "&";
	new_src += "ec_render_email=true";
	new_src += "&";
	new_src += "ec_email_template=" + val_email_template;
	new_src += "&";
	new_src += "ec_email_type=" + val_email_type;
	new_src += "&";
	new_src += "ec_email_order=" + val_email_order;

	new_src += "&";
	new_src += "ec_approve_preview=" + val_approve_preview;

	if ( val_email_template_preview ) {
		new_src += "&";
		new_src += "ec_email_template_preview=" + val_email_template_preview;
	}

	jQuery('#preview-email-template-iframe').attr("src", new_src );



	// Set the Send test Input to Order Email
	// ----------------------------------------
	jQuery("#ec_send_email").val( val_billing_email );


}


function ec_loading(options) {
			
	// set up default options
	var defaults = {
		id:      false,
		text: "Loading...",
		backgroundColor: "rgba(0,0,0,.3)"
		
	};
	options = jQuery.extend({}, defaults, options);
	
	if ( !jQuery(".cxectrl-loading-holder").length ) {
		jQuery("body").append('<div class="cxectrl-loading-holder" style="display: none; background-color:' + options.backgroundColor + '; "><div class="cxectrl-loading-inner-holder"><div class="cxectrl-loading-graphic"></div><div class="cxectrl-loading-text"></div></div></div>' );
	}
	
	jQuery(".cxectrl-loading-text").append( options.content );
	jQuery(".cxectrl-loading-holder").fadeIn(300);
}


function ec_loading_end() {
	
	jQuery(".cxectrl-loading-holder").fadeOut(300, function() {
		jQuery(this).remove();
	});
}


function ec_toggle_header_info() {
	
	if ( jQuery("#preview-email-template-iframe").contents().find(".header-info").is(":visible") ) {
		jQuery("#preview-email-template-iframe").contents().find(".header-info").slideUp({ duration: 300 });
		jQuery("#preview-email-template-iframe").contents().find(".hide-icon.hide-up").fadeOut(50);
		jQuery("#preview-email-template-iframe").contents().find(".hide-icon.hide-down").fadeIn(50);
	}
	else {
		jQuery("#preview-email-template-iframe").contents().find(".header-info").slideDown({ duration: 300 });
		jQuery("#preview-email-template-iframe").contents().find(".hide-icon.hide-up").fadeIn(50);
		jQuery("#preview-email-template-iframe").contents().find(".hide-icon.hide-down").fadeOut(50);
	}
}


function ec_set_to_email(address) {
	
	jQuery(".header-info-meta-block-to-email .meta-value").html( address );
}


jQuery.extend(jQuery.easing,{
	peEaseInOutExpo: function (x, t, b, c, d) {
		if (t==0) return b;
		if (t==d) return b+c;
		if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
		return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	}
});


