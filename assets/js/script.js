jQuery(function ($) {

    function killUpdateButton() {
        const btn = $('button[name="update_cart"]');

        if (btn.length) {
            btn.remove(); // remove MESMO
        }
    }

    // roda várias vezes no início (pra pegar re-render do tema)
    const interval = setInterval(() => {
        killUpdateButton();
    }, 300);

    // para depois de 5 segundos (performance)
    setTimeout(() => {
        clearInterval(interval);
    }, 5000);

    // quando Woo atualiza carrinho
    $(document.body).on('updated_cart_totals updated_wc_div', function () {
        killUpdateButton();
    });
});