<?php
/**
 * Dados da barra, template e botão.
 * wp eval-file .../tests/test-bar.php <produto> <limite_continente> <limite_ilhas> <cp_continente> <distrito> <cp_ilhas> <distrito_ilhas>
 */

require __DIR__ . '/bootstrap.php';

list($pid, $cont, , $cp_cont, $st_cont) = $args;
$pid = (int) $pid; $cont = (float) $cont;

// Opções da loja guardadas e repostas no fim, mesmo com erro fatal.
$original = get_option('fsb_settings', null);
register_shutdown_function(function () use ($original) {
    if ($original === null) delete_option('fsb_settings'); else update_option('fsb_settings', $original);
    if (WC()->cart) WC()->cart->empty_cart();
});

function fsb_render($data) {
    ob_start();
    include FSB_PATH . 'templates/bar.php';
    return trim(ob_get_clean());
}

update_option('fsb_settings', array('min_trigger' => 0, 'button_type' => ''));
fsb_test_cart($pid, 1, $cp_cont, $st_cont);
$total = fsb_get_free_shipping_total(fsb_get_free_shipping_method());
$d = fsb_get_bar_data();
fsb_assert(is_array($d) && abs($d['remaining'] - ($cont - $total)) < 0.005 && (float) $d['threshold'] === $cont, "continente: falta limite − total ($cont − $total)");
fsb_assert(is_array($d) && array_key_exists('button', $d) && $d['button'] === null, 'sem tipo de botão: button null, barra presente');

$html = fsb_render($d);
fsb_assert(strpos($html, 'FALTAM') !== false && strpos($html, 'id="fsb-bar"') !== false, 'template com dados: mostra FALTAM no contentor #fsb-bar');

ob_start(); fsb_add_button_below_checkout(); $btn = trim(ob_get_clean());
fsb_assert($btn === '', 'sem destino: o botão não é impresso');

update_option('fsb_settings', array('min_trigger' => 0, 'button_type' => 'url', 'url' => 'https://exemplo.pt/x', 'label' => 'Ver x'));
ob_start(); fsb_add_button_below_checkout(); $btn = ob_get_clean();
fsb_assert(strpos($btn, 'href="https://exemplo.pt/x"') !== false && strpos($btn, 'Ver x') !== false && strpos($btn, 'style=') === false, 'com destino: botão com link e texto, sem style inline');

fsb_assert(fsb_render(null) === '<div id="fsb-bar" class="fsb-bar-wrapper"></div>', 'sem dados: só o contentor vazio');

// Quando a WooCommerce dá o envio grátis (ex.: cupão de portes grátis com «cupão ou valor mínimo»,
// ou outro plugin a ligar o método), a barra tem de dizer que ganhou, mesmo abaixo do valor.
add_filter('woocommerce_shipping_free_shipping_is_available', '__return_true', 99);
fsb_test_cart($pid, 1, $cp_cont, $st_cont);
$d = fsb_get_bar_data();
fsb_assert(is_array($d) && $d['remaining'] <= 0, 'Envio grátis disponível abaixo do valor: a barra diz que ganhou');
remove_filter('woocommerce_shipping_free_shipping_is_available', '__return_true', 99);

update_option('fsb_settings', array('min_trigger' => 100000, 'button_type' => ''));
fsb_assert(fsb_get_bar_data() === null, 'total abaixo de "mostrar a partir de": sem barra');

update_option('fsb_settings', array('min_trigger' => 0));
fsb_test_cart($pid, 1, '28001', 'M', '', 'ES');
if (fsb_get_free_shipping_method() === null) {
    fsb_assert(fsb_get_bar_data() === null, 'zona sem envio grátis: sem barra');
}

fsb_test_finish();
