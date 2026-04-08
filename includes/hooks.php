<?php

if (!defined('ABSPATH')) exit;

add_action('woocommerce_cart_totals_after_order_total', 'fsb_render_bar');

function fsb_render_bar() {
    $data = fsb_get_bar_data();

    include FSB_PATH . 'templates/bar.php';
}

add_action('woocommerce_proceed_to_checkout', 'fsb_add_button_below_checkout', 999);

function fsb_add_button_below_checkout() {
    $data = fsb_get_bar_data();

    if (!$data) return;

    echo '<div class="fsb-checkout-button-wrapper" style="margin-top:10px;">
        <a href="' . esc_url($data['link']) . '" class="button fsb-button" style="width:100%; text-align:center;">
            ' . esc_html($data['label']) . '
        </a>
    </div>';
}