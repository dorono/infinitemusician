/**
 * WooCommerce Quick Checkout
 *
 * @description: Main AJAX functionality for Quick Checkout
 *
 */
var quick_checkout;

(function ($) {

    /**
     * QC Cart iFrame
     */
    var cart_frame;

    /**
     * QC Button
     */
    var qc_button;

    /**
     * QC DOM obj
     */
    var qc_dom_obj;

    /**
     * QC Object
     */
    var qc_object;

    /**
     * QC Selectors
     *
     * @type {string}
     */
    var qc_selectors = '.quick-checkout, .quick-checkout-link, .quick-checkout-button-shortcode, .quick-checkout-product, .qc-trigger-autoload, .quick-checkout-now';

    /**
     * iFrame URL
     *
     * @type {string}
     */
    var iframe_url = '';

    /**
     * WC Login Form
     *
     * @type {jQuery}
     */
    var wc_login_form = $('.quick-checkout-frame form.login');

    /**
     * Woo QC
     */
    var Woo_QC = {

        /**
         * Initialize
         * Kick it off
         */
        init: function () {

            //Check to see if there's a checkout URL set; if not, we can't proceed, so exit.
            if (quick_checkout.checkout_url == 'checkout_not_set') {
                return false;
            }

            //Clicked / Touchend Checkouts
            $(document.body).on('click touchend', qc_selectors, Woo_QC.qc_clicked);

            //Onpage autoload
            $('.qc-trigger-autoload').trigger('click');

            //Hide all except checkout
            Woo_QC.hide_all_except_checkout();

            //On Window Loads
            $(window).load(Woo_QC.window_load);

            //Remove single product default submit button if necessary
            $('.quick-checkout-product-replace').prevAll('.single_add_to_cart_button').remove();

            //Add action to login form on submit
            wc_login_form.on('submit', Woo_QC.login_form_submitted);


        },


        /**
         * Quick Checkout Clicked
         *
         * @param e Click event
         */
        qc_clicked: function (e) {

            e.preventDefault();

            qc_button = $(this);

            //Make sure there's no variation needed still
            //@see: https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/82
            if(qc_button.hasClass('wc-variation-selection-needed')) {
                return false;
            }

            //Add loading classes
            qc_button.addClass('loading').prop('disabled', true);
            qc_button.closest('.product').addClass('quick-checkout-active-loading');

            //Set QC object
            qc_object = $.extend({}, {
                quantity: 1 // add more defaults if needed
            }, Woo_QC.qc_get_object(qc_button));

            //Gravity Form Support
            if (qc_button.hasClass('quick-checkout-gform-button')) {
                Woo_QC.gravity_form_support(qc_object);
                return false;
            }

            //a.quick-checkout-link support: need to create an object for qc_dom_obj if none exist already,
            //because these aren't output by WP.
            if (qc_button.hasClass('quick-checkout-link') && $('[id^=quick-checkout-]').length == 0) {
                qc_button.after('<div id="quick-checkout-0"></div>');
            }

            Woo_QC.qc_open_checkout(qc_object);

        },


        /**
         * Open Checkout
         *
         * @param qc_object
         */
        qc_open_checkout: function (qc_object) {

            qc_dom_obj = $('#quick-checkout-' + qc_object.product_id);

            //QC dom object for QC woo shortcodes
            if (!qc_dom_obj.length) {
                qc_dom_obj = qc_button.parents('.qc-woo-shortcode-wrap').find('[id^=quick-checkout-]');
            }

            //If still no qc_dom_obj find one from body
            if (!qc_dom_obj.length) {
                qc_dom_obj = $(document.body).find('[id^=quick-checkout-]');
            }

            //Handle Shortcodes
            switch (qc_object.checkout_action) {
                case 'onpage':
                    Woo_QC.onpage_checkout(qc_dom_obj, qc_object);
                    break;
                case 'reveal':
                    Woo_QC.reveal_checkout(qc_dom_obj, qc_object);
                    break;
                case 'lightbox':
                    Woo_QC.lightbox_checkout(qc_dom_obj, qc_object);
                    break;
            }

        },

        /**
         * On Page Checkout
         *
         * @param qc_dom_obj
         * @param qc_object
         */
        onpage_checkout: function (qc_dom_obj, qc_object) {
            Woo_QC.init_frame('onpage', qc_object);
            //Show checkout form
            qc_dom_obj.show();
        },

        /**
         * Reveal (Slide Down) Checkout
         *
         * @param qc_dom_obj
         * @param qc_object
         */
        reveal_checkout: function (qc_dom_obj, qc_object) {
            //Hide QC button if not on shop page or any woo page
            //(for when shortcode is in use)
            if (!$(document.body).hasClass('single-product') && !$(document.body).hasClass('woocommerce-page')) {
                qc_button.hide();
            }
            //Reveal form
            qc_dom_obj.show();
            qc_button.closest('.product').removeClass('quick-checkout-active-loading');
            //Proceed
            Woo_QC.init_frame('reveal', qc_object);
        },

        /**
         * Lightbox Checkout
         *
         * @param qc_dom_obj
         * @param qc_object
         */
        lightbox_checkout: function (qc_dom_obj, qc_object) {
            Woo_QC.qc_open_modal();
            Woo_QC.init_frame('lightbox', qc_object);
        },

        /**
         * Initialize Frame Loader
         *
         * @param type
         * @param qc_object
         */
        init_frame: function (type, qc_object) {

            //Remove any existing frames prior to placing another
            $(document.body).find('iframe.qc-frame').remove();

            //Check the type & insert the frame
            switch (type) {
                case 'lightbox':
                    cart_frame = $('<div id="quick-checkout-modal" class="qc-clearfix"><button class="mfp-close" type="button" title="Close (Esc)">×</button><iframe src="" scrolling="no" style="overflow:hidden;width:100%;height:50px;" name="qc_loader" class="qc-frame"  frameBorder="0"></iframe></div>');
                    qc_dom_obj.append(cart_frame);
                    break;
                case 'reveal':
                    cart_frame = $('<div class="qc-loading"></div><iframe src="" class="qc-frame" scrolling="no" style="overflow:hidden;width:100%;height:50px;" name="qc_loader" frameBorder="0"></iframe>');
                    qc_dom_obj.append(cart_frame);
                    break;
                case 'onpage':
                    cart_frame = $('<div class="qc-loading"></div><iframe src="" scrolling="no" style="overflow:hidden;width:100%;height:50px;" name="qc_loader" class="qc-frame" frameBorder="0"></iframe>');
                    qc_dom_obj.append(cart_frame);
                    cart_frame.show();
                    break;
                default:
                    return;
            }

            Woo_QC.load_iframe_checkout(qc_object);

        },

        /**
         * iFrame Source
         *
         * @returns string|bool
         */
        get_iframe_src: function () {

            iframe_url = quick_checkout.checkout_url + '?qc_loader=true';

            //Match iFrame origin
            if (window.location.protocol == 'http:') {
                iframe_url = iframe_url.replace('https:', 'http:');
            }

            //Don't allow the mess of qc_object.product_variations into URL
            if (qc_object.product_variations) {
                qc_object.product_variations = ''
            }

            //Use jQuery's param to build iframe src URL from object
            iframe_url += '&' + $.param(qc_object);

            //Return it
            return iframe_url;

        },

        /**
         * Load iframe Checkout
         *
         * @param qc_object
         */
        load_iframe_checkout: function (qc_object) {

            iframe_url = Woo_QC.get_iframe_src();
            var variation_form = qc_button.closest('.variations_form');

            //Debugging Info:
            if (quick_checkout.script_debug) {
                console.log('QC Object:');
                console.log(qc_object);
                console.log('QC DOM Object:');
                console.log(qc_dom_obj);
                console.log('QC iFrame URL:');
                console.log(iframe_url);
            }

            //Is this a Checkout Now trigger?
            if (qc_button.hasClass('quick-checkout-now') || qc_button.hasClass('quick-checkout-link')) {
                cart_frame.find('iframe').attr('src', iframe_url);
                Woo_QC.frame_loaded();
                return false;
            }

            //Single Product Variation -> add_single_product_variation();
            if (variation_form.length) {
                Woo_QC.add_single_product_variation(variation_form);
                return false;
            }

            //gForm?
            if (qc_object.is_gform) {
                iframe_url = Woo_QC.get_iframe_src();
                //Sanity check Check if cart
                if (cart_frame.is('iframe')) {
                    cart_frame.attr('src', iframe_url);
                } else {
                    cart_frame.find('iframe').attr('src', iframe_url);
                }

                Woo_QC.frame_loaded();
                return false;
            }

            // AJAX add to cart request
            var data = {
                action: 'wqc_add_to_cart',
                data: qc_object
            };

            //Do the AJAX
            Woo_QC.qc_ajax(data);

        },

        /**
         * QC AJAX
         *
         * @param data
         */
        qc_ajax: function (data) {

            iframe_url = Woo_QC.get_iframe_src();

            //Debugging Info:
            if (quick_checkout.script_debug) {
                console.log('QC AJAX Data:');
                console.log(data);
            }

            //AJAX Add to Cart
            $.post(quick_checkout.ajax_url, data, function (response) {

                //Debugging Info:
                if (quick_checkout.script_debug) {
                    console.log('QC AJAX Response:');
                    console.log(response);
                }

                //Check if there was a problem
                if (response == 'empty_cart') {
                    alert(quick_checkout.i18n.cart_error);
                    qc_button.removeClass('loading').prop('disabled', false);
                    qc_dom_obj.empty();

                    //Is magnific open?
                    if ($.magnificPopup.instance.isOpen) {
                        $.magnificPopup.close(); //close it
                    }

                    return false;
                }

                //Sanity check Check if cart
                if (cart_frame.is('iframe')) {
                    cart_frame.attr('src', iframe_url);
                } else {
                    cart_frame.find('iframe').attr('src', iframe_url);
                }

                Woo_QC.frame_loaded();
                return true;

            }).fail(function () {
                console.log(response);
                alert(quick_checkout.i18n.cart_error);
                qc_button.removeClass('loading').prop('disabled', false);
                qc_dom_obj.empty();
            });

        },

        /**
         * Frame Loaded
         */
        frame_loaded: function () {

            //Remove loading class from button & re-enable
            if (qc_object.checkout_action !== 'reveal') {
                qc_button.removeClass('loading').prop('disabled', false);
                $('.qc-loading').remove();
            }

            var log_option = false;
            if (quick_checkout.script_debug) {
                log_option = true;
            }

            //iFrame Resizing
            iFrameResize({
                inPageLinks: true,
                enablePublicMethods: true,
                log: log_option,
                messageCallback: function (messageData) {

                    //Sanity Check
                    if (typeof messageData.message.result == 'undefined') {
                        return false;
                    }

                    console.log(qc_object.checkout_action);

                    //ScrollTo Errors on AJAX Failure
                    if (messageData.message.result == 'failure') {
                        //ScrollTo form
                        $('html, body').animate({
                            scrollTop: $(qc_dom_obj).offset().top - 100
                        }, 500);
                        //Magnific popup goes to top
                        $('.mfp-wrap').animate({
                            scrollTop: 0
                        }, 500);
                    }


                    //Reveal form
                    if (qc_object.checkout_action == 'reveal') {
                        //Remove loading
                        qc_button.removeClass('loading').prop('disabled', false);
                        $('.qc-loading').hide().remove();
                        //Reveal checkout form
                        qc_dom_obj.slideDown();
                        //scrollTo Object
                        $('html, body').animate({
                            scrollTop: qc_dom_obj.offset().top - 35
                        }, 500);

                    }


                },
                resizedCallback: function (messageData) {
                    //Hide loader
                    $('.mfp-preloader').hide();
                }
            });

        },

        /**
         * QC Get Object
         *
         * @description: Helper function
         *
         * @param el
         * @returns {*}
         */
        qc_get_object: function (el) {

            qc_object = el.data();

            //Single Product variation support
            if (qc_button.parents('form').data('product_variations')) {
                qc_object.product_variations = qc_button.parents('form').data('product_variations');
            }

            //Standardize the id to product_id
            if (qc_object.id) {
                qc_object.product_id = qc_object.id;
                delete qc_object.id;
            }

            //Quantities
            if (qc_button.data('quantity') > 1) {
                qc_object.quantity = qc_button.data('quantity');
            } else if (qc_button.hasClass('single_add_to_cart_button')) {
                qc_object.quantity = qc_button.parents('form').find('input.qty').val();
            }

            //Checkout Now
            if (qc_button.hasClass('quick-checkout-link') || qc_button.hasClass('quick-checkout-now')) {
                qc_object.checkout_action = 'lightbox';
                qc_object.clear_cart = false;
                qc_object.checkout_now = true;
                qc_object.product_id = 0;
            }

            return qc_object;
        },


        /**
         * Open Modal
         */
        qc_open_modal: function () {

            // Open Magnific Popup directly via plugin API
            $.magnificPopup.open({
                items: {
                    src: qc_dom_obj, // can be a HTML string, jQuery object, or CSS selector
                    type: 'inline'

                },
                callbacks: {
                    open: function () {

                        //Display loader graphic
                        $('.mfp-preloader').show();

                        //Remove loading from clicked button parent
                        $('.quick-checkout-active-loading').removeClass('quick-checkout-active-loading');

                        // Scroll to top in Modal on error
                        $(document.body).on('checkout_error', function () {
                            $('.mfp-wrap').animate({
                                scrollTop: 0
                            }, 1000);
                        });

                    },
                    close: function () {
                        //Remove popup class
                        $('#quick-checkout-modal').remove();
                    }
                }
            });

        },


        /**
         * Add Variation Product
         *
         * @param variation_form
         * @returns {boolean}
         */
        add_single_product_variation: function (variation_form) {

            var var_id = variation_form.find('input[name=variation_id]').val();

            var product_id = variation_form.find('input[name=product_id]').val();
            var quantity = variation_form.find('input[name=quantity]').val();

            $('.ajaxerrors').remove();
            var item = {},
                check = true;

            var variations = variation_form.find('select[name^=attribute]');
            if (!variations.length) {
                variations = variation_form.find('[name^=attribute]:checked');
            }

            //Backup Code for getting input variable
            if (!variations.length) {
                variations = variation_form.find('input[name^=attribute]');
            }

            //Loop through variations
            variations.each(function () {

                var $this = $(this),
                    attributeName = $this.attr('name'),
                    attributevalue = $this.val(),
                    index,
                    attributeTaxName;

                $this.removeClass('error');

                if (attributevalue.length === 0) {
                    index = attributeName.lastIndexOf('_');
                    attributeTaxName = attributeName.substring(index + 1);

                    $this
                        .addClass('required error')
                        .before('<div class="ajaxerrors"><p>Please select ' + attributeTaxName + '</p></div>');

                    check = false;

                } else {
                    item[attributeName] = attributevalue;
                }


            });

            if (!check) {
                return false;
            }

            // AJAX add to cart request
            var data = {
                action: 'wqc_add_to_cart',
                product_id: product_id,
                quantity: quantity,
                variation_id: var_id,
                variation: item
            };

            // Trigger event
            $('body').trigger('adding_to_cart', [qc_button, data]);

            Woo_QC.qc_ajax(data);

            return false;


        },

        /**
         * Add a Checkout Now link after Add to Cart
         *
         * @description: Shop and Shortcode Reveals Quick Checkout after Product Added to Cart
         */
        shop_checkout_now: function () {

            //Sanity check: not on cart page
            if (quick_checkout.woocommerce_is_cart == '1') {
                return false;
            }

            //Sanity check: Only if enabled
            if (!Woo_QC.is_shop_checkout_now_enabled()) {
                return false;
            }

            var add_to_cart_btn = $('.products').find('.add_to_cart_button');

            add_to_cart_btn.on('click touchend', add_to_cart_btn, Woo_QC.checkout_now);

        },


        /**
         * Add a Checkout Now link after Add to Cart
         *
         * @description: Shop and Shortcode Reveals Quick Checkout after Product Added to Cart
         */
        checkout_now: function () {

            var this_clicked_button = $(this);
            var this_clicked_button_sku = $(this).data('product_sku');
            var this_clicked_button_id = $(this).data('product_id');
            var button_text = quick_checkout.shop_cart_reveal_text;

            if (quick_checkout.shortcode_shop_cart_reveal_text) {
                button_text = quick_checkout.shortcode_shop_cart_reveal_text;
            }

            $('body').on('added_to_cart', function (data) {

                //if this product doesn't already have a quick checkout link append one after
                if ($(this_clicked_button).closest('.product').find('.quick-checkout-now').length == 0) {

                    $(this_clicked_button).after(
                        '<a href="#" class="quick-checkout-now quick-checkout-button-shop wc-forward" data-product_id="' + this_clicked_button_id + '" data-product_sku="' + this_clicked_button_sku + '">' + button_text + '</a>'
                    );
                }

            });


        },

        /**
         * Is Checkout Now Enabled
         *
         * @returns {boolean}
         */
        is_shop_checkout_now_enabled: function () {

            //only if options are enabled;
            //shop option on, is on shop page, and shop cart reveal on
            //shortcode option yes, not on shop page
            if (quick_checkout.shop_cart_reveal == 'yes' && quick_checkout.woocommerce_is_shop == '1' && quick_checkout.shop_on == 'yes') {
                return true;
            } else if (quick_checkout.shortcode_shop_cart_reveal == 'yes' && quick_checkout.woocommerce_is_shop !== '1') {
                return true;
            } else {
                return false;
            }

        },

        /**
         * Gravity Forms Support
         */
        gravity_form_support: function () {

            var gform = $('form.cart');

            // AJAX add to cart request
            var data = gform.serializeArray();

            //AJAX Add to Cart
            $.post(quick_checkout.ajax_url, data, function (response) {

                //False response == Error, validation issue
                if (response == 0) {
                    //Submit form to handle validation
                    $('.single_add_to_cart_button').trigger('click');
                    return false;
                }

                //Add gform flag
                qc_object.is_gform = true;

                //Open QC as Expected
                Woo_QC.qc_open_checkout(qc_object);

            }).fail(function () {
                alert(quick_checkout.i18n.cart_error);
            });


        },

        /**
         * Hide All on Page Except Woo Checkout
         *
         * @description: This only runs in the checkout iFrame
         *
         */
        hide_all_except_checkout: function () {

            var body = $('body.quick-checkout-frame');
            var checkout = $(body).find('.woocommerce');

            //Let's move it
            checkout.prependTo(body);

            //CSS Show/Hide
            checkout.siblings().hide();
            checkout.parents().siblings().hide();
            body.show();
            checkout.show();


        },

        /**
         * Window Load Trigger
         */
        window_load: function () {

            //Center Hover shop buttons on hover
            $('.quick-checkout-button-image-overlay').each(function (index, element) {
                Woo_QC.center_div($(element), 0, 0);
            });

            //Shop: Checkout Now Link
            Woo_QC.shop_checkout_now();
            //Shop: Checkout Now Link

            $('.quick-checkout-product_quick_checkout').each(function () {
                var add_to_cart_btn = $(this).find('.add_to_cart_button');
                add_to_cart_btn.on('click touchend', add_to_cart_btn, Woo_QC.checkout_now);
            });

        },

        /**
         * Center element horizontal and vertically
         *
         * Helper function
         * http://stackoverflow.com/questions/4790475/jquery-programmatically-center-elements
         *
         * @param element
         * @param xPosFromCenter
         * @param yPosFromCenter
         */
        center_div: function (element, xPosFromCenter, yPosFromCenter) {

            //shop pages x/y
            var images_elem = element.closest('.product').find('.wp-post-image');

            //the theme has done something funny with the normal woo image class name so just find any image
            if (images_elem.length === 0) {
                images_elem = element.closest('.product').find('img');
            }

            var xPos = parseInt(images_elem.outerWidth()) / 2 - parseInt(element.outerWidth()) / 2 - xPosFromCenter;
            var yPos = parseInt(images_elem.outerHeight()) / 2 - parseInt(element.outerHeight()) / 2 - yPosFromCenter;

            //Product pages x/y vars centering
            //modified selector is only difference
            if (element.hasClass('quick-checkout-button-overlay-single')) {
                images_elem = element.closest('.images').find('.wp-post-image');
                var largest_img_elem;
                var largest_img_elem_width;
                var largest_img_elem_height;

                //Some themes use a slider so let's find the largest image element to calculate w/h
                images_elem.each(function () {
                    if (!largest_img_elem_width) {
                        largest_img_elem = $(this);
                        largest_img_elem_width = this.width;
                        largest_img_elem_height = this.height;
                    }
                    else if (this.width > largest_img_elem_width) {
                        largest_img_elem = $(this);
                        largest_img_elem_width = this.width;
                        largest_img_elem_height = this.height;
                    }
                });

                xPos = parseInt(largest_img_elem_width) / 2 - parseInt(element.outerWidth()) / 2 - xPosFromCenter;
                yPos = parseInt(largest_img_elem_height) / 2 - parseInt(element.outerHeight()) / 2 - yPosFromCenter;

            }

            element.css({top: yPos, left: xPos}).addClass('qc-buy-now-centered');

        },

        /**
         * Add action to login form on submit.
         *
         * This ensures that the user is redirected to the full checkout page, not to the URL of the iFrame.
         * The latter renders the iFrame's contents full width, which is less than ideal.
         *
         * @param event
         */
        login_form_submitted: function (event) {
            // Check to see if the form has an `action` set already; only proceed if not.
            if (!wc_login_form[0].hasAttribute('action') || '' == wc_login_form.attr('action')) {
                // Prevent the form from submitting.
                event.preventDefault();

                // Add the action attribute, using the WC checkout URL as the destination.
                wc_login_form.attr('action', quick_checkout.checkout_url);

                // Safely submit the form.
                wc_login_form.submit();
            }
        }

    };
    /**
     * Initializes once loaded
     */
    $(function () {
        Woo_QC.init();
    });

})(jQuery);