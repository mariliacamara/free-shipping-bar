jQuery(function ($) {
    $(document.body).on('updated_cart_totals', function () {
        $.get('/wp-admin/admin-ajax.php?action=fsb_refresh', function (html) {
            $('#fsb-bar').replaceWith(html);
        });
    });
});