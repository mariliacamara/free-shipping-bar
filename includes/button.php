<?php
/**
 * Opções do plugin e destino do botão (categoria de produto, página ou URL).
 */

if (!defined('ABSPATH')) exit;

/**
 * Valores padrão das opções. As cores são as da Zincomed antes da versão 1.1.0.
 *
 * @return array
 */
function fsb_default_options() {
    return array(
        'min_trigger'     => 0,
        'button_type'     => '',
        'category'        => 0,
        'page'            => 0,
        'url'             => '',
        'label'           => '',
        'color_remaining' => '#e53935',
        'color_success'   => '#2e7d32',
        'color_button'    => '#29b0c2',
    );
}

/**
 * Opções guardadas, completadas com os padrões.
 *
 * @return array
 */
function fsb_get_options() {
    $saved = get_option('fsb_settings');
    return array_merge(fsb_default_options(), is_array($saved) ? $saved : array());
}

/**
 * Destino e texto do botão, ou null se não houver destino válido.
 *
 * @param array $options Opções do plugin.
 * @return array|null ['link' => string, 'label' => string]
 */
function fsb_get_button($options) {
    $type  = isset($options['button_type']) ? $options['button_type'] : '';
    $label = isset($options['label']) ? trim((string) $options['label']) : '';

    if ($type === 'category') {
        $term_id = (int) ($options['category'] ?? 0);
        if ($term_id <= 0) {
            return null;
        }
        $term = get_term($term_id, 'product_cat');
        if (!$term || is_wp_error($term)) {
            return null;
        }
        $link = get_term_link($term);
        if (is_wp_error($link)) {
            return null;
        }
        $name = mb_strtolower($term->name);
        $text = ($name === 'promoções' || $name === 'promocoes') ? 'Ver produtos em promoção' : 'Ver ' . $term->name;
        return array('link' => $link, 'label' => $text);
    }

    if ($type === 'page') {
        // get_post(0) devolveria o post global (a página atual): sem página escolhida, sem botão.
        $page_id = (int) ($options['page'] ?? 0);
        if ($page_id <= 0) {
            return null;
        }
        $page = get_post($page_id);
        if (!$page || $page->post_type !== 'page' || $page->post_status !== 'publish') {
            return null;
        }
        return array(
            'link'  => get_permalink($page),
            'label' => $label !== '' ? $label : 'Ver ' . get_the_title($page),
        );
    }

    if ($type === 'url') {
        $url = isset($options['url']) ? trim((string) $options['url']) : '';
        if ($url === '') {
            return null;
        }
        return array('link' => $url, 'label' => $label !== '' ? $label : 'Ver promoções');
    }

    return null;
}
