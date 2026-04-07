<?php

if (!defined('ABSPATH')) exit;

function fsb_get_bar_data() {
    $options = get_option('fsb_settings');

    if (!$options) return null;

    $threshold   = floatval($options['free_shipping'] ?? 20);
    $min_trigger = floatval($options['min_trigger'] ?? 0);

    $cart_total = WC()->cart->get_displayed_subtotal();
    $remaining  = $threshold - $cart_total;

    if ($cart_total < $min_trigger) {
        return null;
    }

    $link  = '#';
    $label = 'Ver promoções';

    // 🔥 CATEGORY
    if (($options['button_type'] ?? '') === 'category') {

        $category_id = intval($options['category'] ?? 0);

        if ($category_id > 0) {

            $term = get_term($category_id, 'product_cat');

            if ($term && !is_wp_error($term)) {

                $term_link = get_term_link($term);

                if (!is_wp_error($term_link)) {
                    $link  = $term_link;
                    $label = 'Ver ' . $term->name;
                }
            }
        }
    }

    // 🔥 URL
    else {

        if (!empty($options['url'])) {
            $link = esc_url($options['url']);
        }

        if (!empty($options['label'])) {
            $label = $options['label'];
        }
    }

    return [
        'remaining' => $remaining,
        'link'      => $link,
        'label'     => $label,
    ];
}