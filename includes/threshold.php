<?php
/**
 * Valor dos portes grátis, lido das regras de portes da WooCommerce de cada loja.
 */

if (!defined('ABSPATH')) exit;

use Automattic\WooCommerce\Utilities\NumberUtil;

/**
 * Método "Envio grátis" por valor mínimo da zona da morada do cliente.
 *
 * Usa o primeiro pacote do carrinho. Entre os métodos ativos que dependem do valor
 * mínimo ("min_amount" ou "either"), devolve o de menor valor.
 *
 * @return WC_Shipping_Free_Shipping|null
 */
function fsb_get_free_shipping_method() {
    if (!function_exists('WC') || !WC()->cart) {
        return null;
    }

    $packages = WC()->cart->get_shipping_packages();
    if (empty($packages)) {
        return null;
    }

    $zone = WC_Shipping_Zones::get_zone_matching_package(reset($packages));
    $best = null;

    foreach ($zone->get_shipping_methods(true) as $method) {
        if (!$method instanceof WC_Shipping_Free_Shipping) {
            continue;
        }
        if (!in_array($method->requires, array('min_amount', 'either'), true)) {
            continue;
        }
        if ($best === null || (float) $method->min_amount < (float) $best->min_amount) {
            $best = $method;
        }
    }

    return $best;
}

/**
 * Total que a WooCommerce compara com o valor mínimo do método
 * (mesmo cálculo de WC_Shipping_Free_Shipping::is_available).
 *
 * @param WC_Shipping_Free_Shipping $method
 * @return float
 */
function fsb_get_free_shipping_total($method) {
    $cart  = WC()->cart;
    $total = $cart->get_displayed_subtotal();

    if ('no' === $method->ignore_discounts) {
        $total = $total - $cart->get_discount_total();
        if ($cart->display_prices_including_tax()) {
            $total = $total - $cart->get_discount_tax();
        }
    }

    return (float) NumberUtil::round($total, wc_get_price_decimals());
}
