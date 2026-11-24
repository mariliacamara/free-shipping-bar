<?php
/**
 * Valor dos portes grátis pela zona da morada, e total igual ao da WooCommerce.
 *
 * wp eval-file wp-content/plugins/free-shipping-bar/tests/test-threshold.php \
 *   <produto> <limite_continente> <limite_ilhas> <cp_continente> <distrito_continente> <cp_ilhas> <distrito_ilhas>
 * Nova Porto dev: 11379 49 300 4000-001 PR 9000-001 MD
 * Zincomed dev:   29265 64 350 4000-001 PR 9000-001 MD
 */

require __DIR__ . '/bootstrap.php';

list($pid, $cont, $ilhas, $cp_cont, $st_cont, $cp_ilhas, $st_ilhas) = $args;
$pid = (int) $pid; $cont = (float) $cont; $ilhas = (float) $ilhas;

// Cupão de 10% sem portes grátis, criado só para o teste.
$code = 'fsb-teste-' . wp_rand(1000, 9999);
$c = new WC_Coupon();
$c->set_code($code);
$c->set_discount_type('percent');
$c->set_amount(10);
$c->save();

// Limpeza mesmo se o teste parar com erro fatal.
register_shutdown_function(function () use ($c) {
    if (WC()->cart) { WC()->cart->empty_cart(); WC()->cart->remove_coupons(); }
    $c->delete(true);
});

{
    fsb_test_cart($pid, 1, $cp_cont, $st_cont);
    $m = fsb_get_free_shipping_method();
    fsb_assert($m && (float) $m->get_option('min_amount') === $cont, "continente usa o limite do continente ($cont)");

    fsb_test_cart($pid, 1, $cp_ilhas, $st_ilhas);
    $m = fsb_get_free_shipping_method();
    fsb_assert($m && (float) $m->get_option('min_amount') === $ilhas, "ilhas usam o limite das ilhas ($ilhas)");

    $price = (float) wc_get_product($pid)->get_price();
    $max_qty = (int) ceil(($ilhas * 1.25) / max($price, 0.01)) + 1;
    foreach (array(array($cp_cont, $st_cont, ''), array($cp_ilhas, $st_ilhas, ''), array($cp_cont, $st_cont, $code)) as $caso) {
        list($cp, $st, $cupao) = $caso;
        $desacordos = array();
        for ($q = 1; $q <= $max_qty; $q++) {
            $pkgs = fsb_test_cart($pid, $q, $cp, $st, $cupao);
            $m = fsb_get_free_shipping_method();
            if (!$m) { $desacordos[] = "qtd $q sem método"; continue; }
            $barra_diz_ganhou = ((float) $m->get_option('min_amount') - fsb_get_free_shipping_total($m)) <= 0;
            if ($barra_diz_ganhou !== $m->is_available($pkgs[0])) {
                $desacordos[] = "qtd $q";
            }
        }
        fsb_assert(!$desacordos, "barra e Envio grátis concordam de 1 a $max_qty unidades (cp $cp" . ($cupao ? ', com cupão 10%' : '') . ')' . ($desacordos ? ': ' . implode(', ', array_slice($desacordos, 0, 5)) : ''));
    }

    fsb_test_cart($pid, 1, '28001', 'M', '', 'ES');
    fsb_assert(fsb_get_free_shipping_method() === null || WC_Shipping_Zones::get_zone_matching_package(WC()->cart->get_shipping_packages()[0])->get_id() !== 0, 'Espanha: sem zona com envio grátis → nenhum método (ou a loja tem zona para Espanha)');
}

fsb_test_finish();
