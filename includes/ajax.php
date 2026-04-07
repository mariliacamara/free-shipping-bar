<?php

if (!defined('ABSPATH')) exit;

add_action('wp_ajax_fsb_refresh', 'fsb_refresh');
add_action('wp_ajax_nopriv_fsb_refresh', 'fsb_refresh');

function fsb_refresh() {
    $data = fsb_get_bar_data();

    ob_start();
    include FSB_PATH . 'templates/bar.php';
    echo ob_get_clean();

    wp_die();
}