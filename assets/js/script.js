jQuery(function ($) {

    if (!$('body').hasClass('woocommerce-cart')) return;

    function replaceUpdateButton() {

        const btn = $('button[name="update_cart"]');

        if (!btn.length) return;

        // 🔥 remove o botão original
        btn.remove();

        const container = $('.woocommerce-cart-form .actions');

        container.find('.fsb-button--wrapper').remove();

        $.ajax({
            url: fsbData.ajax_url,
            method: 'GET',
            data: {
                action: 'fsb_get_button'
            },
            success: function (html) {

                if (!html) return;

                container.append(html);
            }
        });
    }

    // inicial
    replaceUpdateButton();

    // quando Woo atualiza
    $(document.body).on('updated_cart_totals', function () {
        replaceUpdateButton();
    });

});