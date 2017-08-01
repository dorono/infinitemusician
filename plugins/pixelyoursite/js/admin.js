jQuery(function($){

    /* Tabs Management */
    $('.pys-menu li').on('click', function(event) {

        event.preventDefault();
        var tab = $(this).attr('id').replace('pys-menu-', '');
        $('.pys-menu li').removeClass('nav-tab-active selected');
        $(this).addClass('nav-tab-active selected');

        $('.pys-panel').hide();
        $('#pys-panel-'+tab).show();

        // remember active tab
        $('input[name=active_tab]').val(tab);

    });

    /* Woo events toggle */
    // set main switcher state on page load
    if($('.woo-option').length == $('.woo-option:checked').length) {
        $('.woo-events-toggle').prop('checked', 'checked');
    }

    /* Woo events toggle */
    // add multiple select / deselect functionality
    $('.woo-events-toggle').click(function(){
        var options = $('.woo-option');
        options.prop('checked', this.checked);
    });

    /* Woo events toggle */
    // if all checkbox are selected, check the selectall checkbox and viceversa
    $('.woo-option').click(function(){

        if($('.woo-option').length == $('.woo-option:checked').length) {
            $('.woo-events-toggle').prop('checked', 'checked');
        } else {
            $('.woo-events-toggle').removeAttr('checked');
        }

    });

    /**
     * EDD events toggle
     * Set main switcher state on page load.
     */
    if ($('.edd-option').length == $('.edd-option:checked').length) {
        $('.edd-events-toggle').prop('checked', 'checked');
    }

    /**
     * EDD events toggle
     * Add multiple select / deselect functionality.
     */
    $('.edd-events-toggle').click(function () {
        var options = $('.edd-option');
        options.prop('checked', this.checked);
    });

    /**
     * EDD events toggle
     * If all checkboxes are selected, check the main checkbox and vice versa.
     */
    $('.edd-option').click(function () {

        if ($('.edd-option').length == $('.edd-option:checked').length) {
            $('.edd-events-toggle').prop('checked', 'checked');
        } else {
            $('.edd-events-toggle').removeAttr('checked');
        }

    });

}); /* Dom Loaded */