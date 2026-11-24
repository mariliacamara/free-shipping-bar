<?php
/**
 * Destino e texto do botão (categoria, página ou URL) e sanitização das opções.
 * wp eval-file wp-content/plugins/free-shipping-bar/tests/test-button.php
 */

require __DIR__ . '/bootstrap.php';

// Qualquer notice ou warning durante o teste é uma falha.
set_error_handler(function ($no, $str, $file, $line) {
    if ($no & (E_NOTICE | E_WARNING | E_USER_NOTICE | E_USER_WARNING)) {
        fsb_assert(false, "sem avisos PHP: $str ($file:$line)");
    }
    return false;
});

$criados = array();
$cat = wp_insert_term('Teste FSB ' . wp_rand(1000, 9999), 'product_cat');
$cat_id = $cat['term_id'];
$cat_nome = get_term($cat_id, 'product_cat')->name;

$promo = get_term_by('name', 'Promoções', 'product_cat');
$promo_criada = false;
if (!$promo) {
    $r = wp_insert_term('Promoções', 'product_cat', array('slug' => 'promocoes-fsb-teste'));
    $promo = get_term($r['term_id'], 'product_cat');
    $promo_criada = true;
}

$page_id = wp_insert_post(array('post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Página Teste FSB'));

// Limpeza mesmo se o teste parar com erro fatal.
register_shutdown_function(function () use ($cat_id, $page_id, $promo, $promo_criada) {
    if (get_term($cat_id, 'product_cat')) wp_delete_term($cat_id, 'product_cat');
    if (get_post($page_id)) wp_delete_post($page_id, true);
    if ($promo_criada && get_term($promo->term_id, 'product_cat')) wp_delete_term($promo->term_id, 'product_cat');
});

{
    $b = fsb_get_button(array('button_type' => 'category', 'category' => $cat_id));
    fsb_assert($b && $b['link'] === get_term_link($cat_id, 'product_cat') && $b['label'] === "Ver $cat_nome", 'categoria: link da categoria e "Ver <categoria>"');

    $b = fsb_get_button(array('button_type' => 'category', 'category' => $promo->term_id));
    fsb_assert($b && $b['label'] === 'Ver produtos em promoção', 'categoria Promoções: "Ver produtos em promoção"');

    $b = fsb_get_button(array('button_type' => 'page', 'page' => $page_id, 'label' => ''));
    fsb_assert($b && $b['link'] === get_permalink($page_id) && $b['label'] === 'Ver Página Teste FSB', 'página sem texto: link da página e "Ver <título>"');

    $b = fsb_get_button(array('button_type' => 'page', 'page' => $page_id, 'label' => 'Continuar a comprar'));
    fsb_assert($b && $b['label'] === 'Continuar a comprar', 'página com texto: usa o texto');

    $b = fsb_get_button(array('button_type' => 'url', 'url' => 'https://exemplo.pt/x', 'label' => 'Ver x'));
    fsb_assert($b && $b['link'] === 'https://exemplo.pt/x' && $b['label'] === 'Ver x', 'URL: link e texto do campo');

    fsb_assert(fsb_get_button(array('button_type' => 'url', 'url' => '', 'label' => 'Ver x')) === null, 'URL vazia: sem botão');
    fsb_assert(fsb_get_button(array('button_type' => '')) === null, 'sem tipo: sem botão');
    fsb_assert(fsb_get_button(array()) === null, 'opções vazias: sem botão');

    wp_delete_term($cat_id, 'product_cat');
    fsb_assert(fsb_get_button(array('button_type' => 'category', 'category' => $cat_id)) === null, 'categoria apagada: sem botão');

    wp_delete_post($page_id, true);
    fsb_assert(fsb_get_button(array('button_type' => 'page', 'page' => $page_id)) === null, 'página apagada: sem botão');

    $o = fsb_get_options();
    fsb_assert(isset($o['color_remaining'], $o['color_success'], $o['color_button']), 'fsb_get_options traz as cores');

    $s = fsb_sanitize_settings(array('button_type' => 'page', 'page' => '12', 'color_button' => '#2A8936'));
    fsb_assert($s['button_type'] === 'page' && $s['page'] === 12 && strtolower($s['color_button']) === '#2a8936', 'sanitização aceita página e cor válida');

    $s = fsb_sanitize_settings(array('button_type' => 'xpto', 'color_remaining' => 'vermelho'));
    fsb_assert($s['button_type'] === '' && $s['color_remaining'] === '#e53935', 'sanitização: tipo inválido → sem botão, cor inválida → padrão');

    $s = fsb_sanitize_settings(array('button_type' => 'category', 'category' => '1678'));
    fsb_assert($s['button_type'] === 'category' && $s['category'] === 1678, 'sanitização mantém as opções da Zincomed');
}
restore_error_handler();

fsb_test_finish();
