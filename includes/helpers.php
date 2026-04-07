<?php

if (!defined('ABSPATH')) exit;

function fsb_get_bar_data() {
    $options = get_option('fsb_settings');

    $threshold = floatval($options['free_shipping'] ?? 20);
    $min_trigger = floatval($options['min_trigger'] ?? 0);

    $cart_total = WC()->cart->get_displayed_subtotal();
    $remaining = $threshold - $cart_total;

    if ($cart_total < $min_trigger) {
        return null;
    }

    $link = '#';
    $label = 'Ver promoções';

    if (($options['button_type'] ?? '') === 'category') {
        if (!empty($options['category'])) {
            $term_link = get_term_link((int)$options['category'], 'product_cat');

            if (!is_wp_error($term_link)) {
                $link = $term_link;
                $label = 'Ver produtos';
            }
        }
    } else {
        $link = !empty($options['url']) ? $options['url'] : '#';
        $label = $options['label'] ?? 'Ver promoções';
    }

    return [
        'remaining' => $remaining,
        'link' => $link,
        'label' => $label,
    ];
}