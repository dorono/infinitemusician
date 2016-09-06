jQuery(document).ready(function($) {
  $( 'body' ).on( 'updated_checkout', function () {
    console.log('this one is working!');
  });



    var $woocommerceCheckout = $(document).find('.woocommerce-checkout');
    var $productInfo = $('.postid-11.single-product div.product').find('.images, .entry-summary');

    console.log("$woocommerceCheckout.find('.product-remove a')", $woocommerceCheckout.find('.product-remove a'));

    if ($woocommerceCheckout.find('.product-remove a').length < 1) {
      console.log('we should show it');
      $productInfo.show();
    }

    $woocommerceCheckout.on('click', function(evt) {
        var linkClass = evt.target.className;
        var anchorEl = $(this).find('.product-remove a').attr('class');

        if (linkClass === anchorEl) {
          $productInfo.show();
          window.scrollTo(0, 0);
        }

    });
});
