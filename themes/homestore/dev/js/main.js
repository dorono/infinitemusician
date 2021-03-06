jQuery(document).ready(function($) {
    var $woocommerceCheckout = $(document).find('.woocommerce-checkout');
    var $productInfo = $('.single-product div.product').find('.images, .entry-summary');

    if ($woocommerceCheckout.find('.product-remove a').length < 1) {
      $productInfo.show();
    }

    $woocommerceCheckout.on('click', function(evt) {
        var linkClass = evt.target.className;
        var anchorEl = $(this).find('.product-remove a').attr('class');

        if (linkClass === anchorEl) {
          $productInfo.show();
          $('body').addClass('just-removed');
        }
    });
});
