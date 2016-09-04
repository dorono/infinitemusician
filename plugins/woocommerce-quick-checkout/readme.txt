=== WooCommerce Quick Checkout ===
Contributors: wordimpress
Donate link: http://wordimpress.com/
Tags: woocommerce, checkout, woocommerce checkout, woocommerce single page checkout
Requires at least: 4.0
Tested up to: 4.5.2
Stable tag: 1.9.5

Streamline the WooCommerce checkout process with single page checkout screens and more.

== Description ==

Easily decrease the time it takes to complete a purchase in WooCommerce by providing faster options to checkout. Reduce abandoned carts and increase sales with Quick Checkout.

= Disclaimer =

Be sure to test your checkout forms thoroughly. While we've done our best to code this plugin for use in a variety of website environments, we provide this code "as-is" and make no warranties, representations, covenants or guarantees with regard to the checkout enhancements, tools, and functionalities and will not be liable for any direct, indirect, incidental or consequential damages or for loss of profit, revenue, data, business or use arising out of your use of the this plugin.

The developer of this plugin is in no way affiliated with WooCommerce, WooThemes, WordPress, the company or its affiliates.

== Installation ==

== Frequently Asked Questions ==

= Why should I use this plugin =

The default checkout process in WooCommerce can be slow and tedious. The number of clicks and redirects can lead to higher than expected abandoned cart rates. With Quick Checkout your users can checkout directly on page without any unnecessary redirect or clicks.

= Does this plugin work with my theme? =

If your theme is WooCommerce compatible this plugin should have no issues integrating. The plugin uses standard WooCommerce style classes to display buttons and can be easily adapted to suit your design needs.

== Changelog ==

= 1.9.5 =
* Fix: Bug when a customer would try to add a product without selecting appropriate required variations Quick Checkout would try to load anyways
* Fix: Quick Checkout can now be loaded on non-https pages for support for user accepting payments via offsite gateways such as PayPal Standard & Express
* Fix: Issue where callback to iframe parent would not pass reliably leaving elements like the ajax loader on page

= 1.9.4 =
* Fix: Removed an errant string after a semi-colon which potentially could cause issues

= 1.9.3 =
* Update: Updated iframeResizer.js & iframeResizer.contentWindow.js scripts to the latest version; tested for compatibility issues
* Fix: Uncaught ReferenceError: parentIFrame is not defined - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/78
* Fix: Bug reported with the "clear cart" option not working as expected - the option was incorrectly defaulting to true when not set, it should have defaulted to false (or leave cart contents intact)
* Fix: "quick-checkout-link" not working as expected when the link was on a page that does not have a checkout widget on it, or any other product at all... basically a non-woocommerce page - the checkout wouldn't load at all - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/75
* Fix: Formatting bug with the "Shortcode Display Options" settings description missing an anchor end tag

= 1.9.2 =
* Fix: Updated for compatibility with WooCommerce's latest Stripe Gateway
* Fix: Updated for compatibility with WooCommerce's latest Amazon Gateway
* Fix: Fixed show_quick_checkout variation support
* Enhancement: QuickCheckout can't work if no checkout page is configured. We throw an Admin alert in the WooCommerce settings now to alert the user if that's the case.
* Fix: Logging into the site within the QuickCheckout modal window would blow up the modal to fill the page. It now fails gracefully.

= 1.9.1 =
* Fix: Quantities not be respected from single product pages and shortcode options - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/61

= 1.9 =
* New: Revamped the way quick checkout engine works; we not use an iframe ajax method which increases plugin compatibility with themes and Woo extensions
* New: Close modal by clicking in the black area - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/22
* New: Better variation attribute support with Quick Checkout shortcodes - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/28
* Tweak: Additional validation in place to check whether cart is empty at end of AJAX. If it is then displays an error; additionally a lot more error checks in place for shortcode & Woo usage - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/46
* Fixed: Site header is stripped from thank you page - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/23
* Fixed: Deleted outdated and inaccurate German translation - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/21
* Fixed: Duplicate ID's when multiple products on the same page enhancement - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/20
* Fixed: Gravity Forms Add-on compatibility issues - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/15
* Fixed: Stripe compatibility issues - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/14
* Fixed: There should only ever be one Woo checkout markup output ever bug - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/10
* Fixed:  Session expired error - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/8
* Fixed:  Refactor JavaScript Powering Plugin enhancement help wanted - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/6
* Fixed:  Reliance and manipulation of Woo's is_checkout() conditional bug - https://github.com/WordImpress/WooCommerce-Quick-Checkout/issues/7

= 1.8.4 =
* Fix: parse args attribute. Previously was parsing a single index. Fixes issue with shortcode attribute.

= 1.8.3 =
* Fix: IE11 bug preventing modal and reveal functionality
* Fix: Password field is properly displayed for 'variable-subscription' products now

= 1.8.2 =
* New: Translatable .POT file added to the new languages directory; any translators who submit a valid translation file receive a free 1 year license for 1 site. Read about that at https://wordimpress.com/documentation/languages.
* New: When a quick checkout button is clicked then a loading symbol now appears. The issue before was many themes don't correctly style the .loading class with WooCommerce, so when the Quick Checkout button was clicked nothing would display indicating the user has to wait for the checkout to load. This fixes that by providing the user a clear indication.
* New: theme-compatibility.php added to house functions for specific themes
* New: theme compatibility with the "Lotus Flower" theme
* New: iDEAL payment gateway compatibility
* Improved: Removed anonymous functions from add_filter to prevent some hosts from erroring out on it
* Improved: Gravity Forms method for inserting submissions improved for version 1.9+
* Improved: Removed use of extract() function within shortcode
* Improved: Replaced use of get_product() with wc_get_product()
* Improved: Subscription products now require account on first page load
* Improved: is_checkout() override now checks for WC version using option flag to proceed. This is so that users of WC 2.3+ can take full advantage of the improved is_checkout filter.
* Fix: Correct improper use of the double colon operator in shortcodes
* Fix: Prevent other themes from adding a psuedo :after on quick checkout buttons, which would result in two loading animations on one button
* Fix: Bug with multiple uses of [product_page] shortcode on one page @see: https://wordimpress.com/support/topic/quick-checkout-not-loading-for-single-product/
* Updated: jQuery Magnific popup script to latest version 1.0.0 which in turn fixed a conflict with the "Flatsome" theme

= 1.8.1 =
* WooCommerce 2.3+ Compatible

= 1.8.0.4 =
* New: Updated is_checkout() handling to use new Woo filter
* Update: Removed SCRIPT_DEBUG constant in favor of WordPress' SCRIPT_DEBUG

= 1.8.0.3 =
* Fix: Change address field works now even if "Enable enhanced country select boxes" option is not selected

= 1.8.0.2 =
* Fix: Subscription products now require account properly for reveal & on page checkout implementations
* Fix: Respect if product global options are disabled in the product specific JavaScript function
* Update: CSS z-index that to ensure Stripe Modal displays over Quick Checkout modal

= 1.8.0.1 =
* Hotfix: Return of the is_checkout() trick so that payment gateways that restrict their output of required scripts properly enqueue the scripts for Quick Checkout; stripe.js for instance would not load without is_checkout()

= 1.8 =
* New: Support for various WooCommerce shortcodes [recent_products], [featured_products], [sales_products], and [best_selling_products] that's used by many page builders such as the Divi theme
* Fix: Protect window.console method calls, e.g. console is not defined on IE
* Fix: Ensure country select field changes conditional fields accordingly in checkout
* Update: Replaced all instances of deprecated the_product() function with wc_get_product()
* Update: Added is_shop() conditional check to class-woocommerce-quick-checkout-shop.php to ensure that it only runs on the shop page
* Update: Increase the z-index value for the Modal that loads QC to a ridiculously high number to prevent certain theme elements from displaying over it. Divi, for example, has a sticky header with a z-index of 9999 which was displaying over the modal
* Update: Added z-index CSS property to .quick-checkout-button-image-overlay class so that it appears over some overlays.
* Update: Added .single_add_to_cart_button to single product button class to improve theme compatibility
* Update: Scroll to top of modal if there is a validation error so user is aware
* Cleanup: Separated core Quick Checkout plugin logic into new separate class file class-woocommerce-quick-checkout-engine.php
* Refactor: Quick Checkout no longer attempts to "trick" is_checkout() conditional to enqueue necessary scripts; now we simply enqueue them ourselves
* Avada Theme: Fixed display issue with shop page hover "Buy now" button
* X Theme: Tested for compatibility; added .woocommerce-checkout to body tag classes to fix styling issue
* Total Theme: Fixed issue with modal popup needing a clearfix; emailed AJ from WP Explorer to inform him of missing hook within products loop
* OptimizePress: Evaluated this plugin and theme and determined it's not compatible with WooCommerce and Quick Checkout
* WooCommerce 2.3: Tested against beta v2 in anticipation for future release

= 1.7.2 =
* Update: Fixed issue with WP Engine caching and occasional issues with cached license activation responses
* Update: EDD_SL_Plugin_Updater.php to v1.5

= 1.7.1 =
* Fix: Resolved redirect issues with SSL/HTTPS - Quick Checkout now uses Woo's own SSL options to secure WooCommerce pages. If you are using a landing page then we suggest using the WP HTTPS plugin to target those pages specifically OR HTTPS secure your entire site (recommended).

= 1.7.0 =
* Update: Gravity forms add-on support updated for WC 2.2+
* Fix: "Session Expired" error now resolved by ensuring proper cart session cookies are set when adding product to cart
* Fix: SSL functionality to ensure no conflicts with WC 2.2 class
* Fix: Updated the way the "Replace the Add to Cart button with Quick Checkout" works so it hides the actual button with a tiny bit of inline CSS rather than the JS method.
* Fix: Quick Checkout improperly outputting button on Affiliate/External product types
* Testing: Extensive testing with WC 2.2+
* General code cleanup, inline docs and organization improvement

= 1.6.2 =
* Optimized: Reworked the way the plugin loads checkout scripts to be more efficient by using WooCommerce filter woocommerce_get_checkout_page_id. This works by setting the is_checkout conditional to true only when the wp_enqueue_scripts action is run in WordPress. This ensures conditional scripts, such as the stripe payment gateway, are loaded on every page of WordPress rather than just the checkout page.


= 1.6.1 =
* Optimized: Removed a $.getScript request from the AJAX
* Improved: Handling of login form's slide toggle improperly displaying before clicked upon
* Fix: Removed woocommerce_checkout_fields action from woocommerce-quick-checkout.php in plugin root because it was causing complications resulting in "Session Expired" errors. After testing, it seems this action was being called twice - unnecessarily by Quick Checkout.
* Removed SCRIPT_DEBUG constant from plugin as to not cause any "unexpected output" upon plugin activation
* General maintenance, code clean up and testing

= 1.6 =
* Fix: Reworked how checkout is loaded via AJAX so that the shipping field is loaded properly according to the products in cart.
* Fix: Issue with login form hiding on my-account page - note that this issue should have been fixed in version 1.5 after reviewing the changelog below but it has somehow cropped up again.

= 1.5.7 =
* Fix: Gravity Forms add-on bug with setting the current user to an admin preventing the forms from submitting and resulting in a "Session Expired" error to appear.

= 1.5.6 =
* Update: For product variations display variation name, not slug, in checkout screen
* Fix: [reveal_quick_checkout] shortcode support for variations, updated plugin docs
* Fix: If variation of a product is not in stock then hide the Quick Checkout button on single product posts
* Fix: If simple product is out of stock do not display Quick Checkout image overlay button on shop page

= 1.5.5 =
* New: [reveal_quick_checkout] now supports quantities with the new "quantity" option
* New: 'quick_checkout_shop_hook' allows for modification of Checkout output for shop pages that may help compatibility with some themes; particularly, ones that use AJAX to load the shop page and don't account for Woo's before/after shop hook
* Fix: Variable type products now validate on the single product posts as expected
* Improved: More reliable mouse hover display for the buy now button that appears over the product image
* Fix: Resolved several PHP warnings for Strict Standards

= 1.5.4 =
* Fix: added conditional to check for is_checkout function to prevent fatal error on plugin activation

= 1.5.3 =
* Fix: reworked how QC works with WooCommerce to output necessary checkout scripts
* Fix: Ensure normal WC checkout page is always reachable
* Fix: Single product post gateways now properly output required scripts
* Improved: Image hover button code output for [product_quick_checkout]

= 1.5.2 =
* New: [reveal_quick_checkout] shortcode now supports variations!
* Improved: AJAX loading of [show_quick_checkout] shortcode that outputs a checkout on page
* Fixed: Subscription checkout didn't properly require account
* Fixed: Issue with [reveal_quick_checkout] displaying "Session expired..." error for non-variation products
* Fixed: Issue with Payment Gateways loading of scripts for non-checkout pages

= 1.5.1 =
* Fix: Conditional check for subsciption type products now actually in place

= 1.5 =
* Improvement: JS handling of centering "Buy Now" button on product images
* New: Support for Subscription Products! Now you can use Quick Checkout with your subscription products
* New: Added addition conditional check for subscription type products for [reveal_quick_checkout] button output
* Fix: Lightbox modal window not opening for Gravity Forms products for non-users (anyone not logged in with proper permissions)
* Fix: Issue where the login form was incorrectly set with display none
* Code cleanup: Removed unnecessary order_item_meta function

= 1.4 =
* Fixed issue with multiple shortcodes on a single page
* SSL: Improved handling of AJAX when SSL is enabled.
* Improved: Body class outputs 'woocommerce' now sitewide, not just when shortcode is detected in the post content. This improves reliability and the checkout styles.

= 1.3 =
* New: Gravity Forms WooCommerce extension compatiblity is here... Hooray!
* New: Added new output location for checkout located after the cart form on single product posts (uses wc's woocommerce_after_add_to_cart_form action to hook)
* Improved: Better and more reliable handling of variation products
* Fix: Bug with Clear Cart before product is added when the Replace Add to Cart button is enabled
* Fix: Prevent Shop image overlay button from displaying on Gravity Form extension enabled products

= 1.2.1 =
* New: [show_quick_checkout] now supports variation by id
* Improved: Various inline documentation cleanup

= 1.2 =
* Updated: Text for various options and settings to add more description and clarity
* Updated: Added license activations remaining stat to license and improved various license styles
* Updated: Added license expired text for licenses that have gone over their activation date
* Improved: Reworked how the "Buy Now" button gets placed over the image so it's more reliable

= 1.1 =
* New: Shortcode to add checkout directly onto page with a product already in the cart ready for immediate checkout
* Updated: Readme.txt added more content

= 1.0.1 =
* Fix: Plugin does not activate unless WooCommerce version 2.1+ installed and active
* Fix: Minor PHP warning when trying to deactivate plugin with WooCommerce still active
* Fix: Minor PHP Warning when plugin activated illegal string offset

= 1.0 =
* Plugin release