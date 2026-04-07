jQuery(function ($) {
    $(document.body).on('updated_cart_totals', function () {
        location.reload();
    });
});