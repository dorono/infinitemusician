jQuery(document).ready(function($) {
  $( 'body' ).on( 'updated_checkout', function () {
    if ($(this).hasClass('just-removed')) {
      console.log('it is happening');
      $(this).removeClass('just-removed');

      $(this).unbind('updated_checkout');
      setTimeout(function () {
        $(this).bind('updated_checkout');
      }, 600);
    }
  });



    var $woocommerceCheckout = $(document).find('.woocommerce-checkout');
    var $productInfo = $('.postid-11.single-product div.product').find('.images, .entry-summary');

    if ($woocommerceCheckout.find('.product-remove a').length < 1) {
      $productInfo.show();
    }

    $woocommerceCheckout.on('click', function(evt) {
        var linkClass = evt.target.className;
        var anchorEl = $(this).find('.product-remove a').attr('class');

        if (linkClass === anchorEl) {
          $productInfo.show();
          window.scrollTo(0, 0);
          $('body').addClass('just-removed');
        }

    });
});
