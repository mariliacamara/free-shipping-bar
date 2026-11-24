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

    return array(
        'remaining' => round($threshold - $total, wc_get_price_decimals()),
        'threshold' => $threshold,
        'button'    => fsb_get_button($options),
    );
}
