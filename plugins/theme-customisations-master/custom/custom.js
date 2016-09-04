jQuery(document).ready(function ($) {
    $(document).find('.woocommerce-checkout').on('click', function (evt) {
      console.log('evt', evt.target.className);
      var linkClass = evt.target.className;
      var anchorEl = $(this).find('.product-remove a').attr('class');

      if (linkClass === anchorEl) {
        console.log('this is it');
      }

    });
});
