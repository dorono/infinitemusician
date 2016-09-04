jQuery(document).ready(function ($) {
    $(document).on('click', function (evt) {
      console.log('evt', evt.target.className);
      var anchorEl = $(this).find('.product-remove a');
      console.log('this is it yo', anchorEl.attr('class'));
    });
});
