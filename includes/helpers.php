<?php

if (!defined('ABSPATH')) exit;

/**
 * Dados da barra para o carrinho atual.
 *
 * @return array|null ['remaining' => float, 'threshold' => float, 'button' => array|null],
 *                    ou null se a zona não tem envio grátis por valor mínimo
 *                    ou o total está abaixo de «mostrar a partir de».
 */
function fsb_get_bar_data() {
    $method = fsb_get_free_shipping_method();
    if (!$method) {
        return null;
    }

    $options   = fsb_get_options();
    $threshold = (float) $method->min_amount;
    $total     = fsb_get_free_shipping_total($method);

    if ($total < (float) $options['min_trigger']) {
        return null;
    }

    $remaining = round($threshold - $total, wc_get_price_decimals());

    // Se a WooCommerce já dá o envio grátis (cupão de portes grátis com «cupão ou valor mínimo»,
    // ou outro plugin), a barra diz que ganhou, como no checkout.
    $packages = WC()->cart->get_shipping_packages();
    if ($remaining > 0 && $packages && $method->is_available(reset($packages))) {
        $remaining = 0;
    }

    return array(
        'remaining' => $remaining,
        'threshold' => $threshold,
        'button'    => fsb_get_button($options),
    );
}
