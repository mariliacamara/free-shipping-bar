jQuery(function ($) {

    if (!$('body').hasClass('woocommerce-cart')) return;

    function replaceUpdateButton() {
        const btn = $('button[name="update_cart"]');

        if (!btn.length) return;

        const container = btn.closest('.actions');

        // remove botão antigo
        container.find('.fsb-button--wrapper').remove();

        if (typeof fsbData !== 'undefined' && fsbData.button_html) {
            container.append(fsbData.button_html);
        }
    }

    replaceUpdateButton();

    $(document.body).on('updated_cart_totals', function () {
        replaceUpdateButton();
    });

});