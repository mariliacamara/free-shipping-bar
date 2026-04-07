<?php

if (!defined('ABSPATH')) exit;

add_action('woocommerce_cart_totals_after_order_total', 'fsb_render_bar');

function fsb_render_bar() {
    $data = fsb_get_bar_data();

    include FSB_PATH . 'templates/bar.php';
}