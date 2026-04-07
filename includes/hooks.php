<?php

if (!defined('ABSPATH')) exit;

add_action('woocommerce_cart_totals_after_order_total', 'fsb_render_bar');

function fsb_render_bar() {
    $data = fsb_get_bar_data();

    include FSB_PATH . 'templates/bar.php';
}

add_action('woocommerce_cart_actions', 'fsb_add_cart_button', 20);

function fsb_add_cart_button() {
    $data = fsb_get_bar_data();

    if (!$data) return;

    echo '<a href="' . esc_url($data['link']) . '" class="button fsb-cart-button">
        ' . esc_html($data['label']) . '
    </a>';
}