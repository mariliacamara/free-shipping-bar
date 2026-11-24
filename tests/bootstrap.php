<?php
/**
 * Utilitários dos testes. Correm no servidor (dev) com `wp eval-file`, contra um carrinho real em memória.
 * Nada aqui é carregado pelo plugin.
 */

if (!defined('ABSPATH')) exit;

$GLOBALS['fsb_test_failures'] = 0;

function fsb_assert($cond, $msg) {
    if ($cond) {
        WP_CLI::log("OK      $msg");
    } else {
        $GLOBALS['fsb_test_failures']++;
        WP_CLI::log("FALHOU  $msg");
    }
}

function fsb_test_finish() {
    $n = $GLOBALS['fsb_test_failures'];
    if ($n > 0) {
        WP_CLI::error("$n teste(s) falharam");
    }
    WP_CLI::success('todos os testes passaram');
}

/**
 * Prepara o carrinho: vazio, morada de envio em Portugal, produto, cupão opcional.
 *
 * @return array Pacotes de envio do carrinho.
 */
function fsb_test_cart($product_id, $qty, $postcode, $state, $coupon = '', $country = 'PT') {
    if (!did_action('woocommerce_load_cart_from_session') && function_exists('wc_load_cart')) {
        wc_load_cart();
    }

    WC()->cart->empty_cart();
    WC()->cart->remove_coupons();

    WC()->customer->set_shipping_country($country);
    WC()->customer->set_shipping_state($state);
    WC()->customer->set_shipping_postcode($postcode);
    WC()->customer->set_billing_country($country);
    WC()->customer->set_billing_state($state);
    WC()->customer->set_billing_postcode($postcode);

    WC()->cart->add_to_cart($product_id, $qty);
    if ($coupon !== '') {
        WC()->cart->apply_coupon($coupon);
    }
    WC()->cart->calculate_totals();

    return WC()->cart->get_shipping_packages();
}
