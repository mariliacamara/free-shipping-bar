<?php

if (!defined('ABSPATH')) exit;

// Carrinho: dentro dos totais, que a WooCommerce atualiza por AJAX.
add_action('woocommerce_cart_totals_after_order_total', 'fsb_render_bar');

function fsb_render_bar() {
    $data = fsb_get_bar_data();

    include FSB_PATH . 'templates/bar.php';
}

// Checkout: por cima do resumo da encomenda.
add_action('woocommerce_checkout_before_order_review', 'fsb_render_bar_checkout_top', 5);

function fsb_render_bar_checkout_top() {
    fsb_render_bar();
}

add_action('woocommerce_proceed_to_checkout', 'fsb_add_button_below_checkout', 999);
add_action('woocommerce_review_order_after_submit', 'fsb_add_button_below_checkout', 20);

function fsb_add_button_below_checkout() {
    $data = fsb_get_bar_data();

    if (!$data || !$data['button']) return;

    echo '<div class="fsb-checkout-button-wrapper">
        <a href="' . esc_url($data['button']['link']) . '" class="button fsb-button">'
            . esc_html($data['button']['label']) .
        '</a>
    </div>';
}

// Checkout: a barra fica fora da parte que a WooCommerce redesenha quando a morada muda,
// por isso vai também como fragmento (ex.: cliente escreve um código postal das ilhas).
add_filter('woocommerce_update_order_review_fragments', 'fsb_checkout_fragment');

function fsb_checkout_fragment($fragments) {
    ob_start();
    fsb_render_bar();
    $fragments['#fsb-bar'] = trim(ob_get_clean());

    return $fragments;
}
