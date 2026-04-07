<?php

if (!defined('ABSPATH')) exit;

function fsb_get_bar_data() {
    $options = get_option('fsb_settings');

    $threshold = floatval($options['free_shipping'] ?? 20);
    $min_trigger = floatval($options['min_trigger'] ?? 0);

    $cart_total = WC()->cart->get_displayed_subtotal();
    $remaining = $threshold - $cart_total;

    // não mostrar antes do trigger
    if ($cart_total < $min_trigger) {
        return null;
    }

    // link
    if (($options['button_type'] ?? '') === 'category') {
        $link = get_term_link($options['category'], 'product_cat');
        $label = 'Ver produtos';
    } else {
        $link = $options['url'] ?? '#';
        $label = $options['label'] ?? 'Ver promoções';
    }

    return [
        'remaining' => $remaining,
        'threshold' => $threshold,
        'cart_total' => $cart_total,
        'link' => $link,
        'label' => $label,
    ];
}