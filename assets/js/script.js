jQuery(function ($) {

    if (!$('body').hasClass('woocommerce-cart')) return;

    function replaceUpdateButton() {

        const btn = $('button[name="update_cart"]');

        if (!btn.length) return;

        const container = btn.closest('.actions');

        // remove antigo
        container.find('.fsb-button--wrapper').remove();

        // 🔥 pega botão atualizado do backend
        $.get('/wp-admin/admin-ajax.php?action=fsb_get_button', function (html) {

            if (!html) return;

            container.append(html);

        });
    }

    // inicial
    replaceUpdateButton();

    // quando Woo atualiza
    $(document.body).on('updated_cart_totals', function () {
        replaceUpdateButton();
    });

});