<?php
/**
 * Plugin Name: Free Shipping Bar
 */

if (!defined('ABSPATH')) exit;

define('FSB_PATH', plugin_dir_path(__FILE__));
define('FSB_URL', plugin_dir_url(__FILE__));

// includes
require_once FSB_PATH . 'includes/hooks.php';
require_once FSB_PATH . 'includes/helpers.php';
require_once FSB_PATH . 'includes/admin/settings-page.php';
require_once FSB_PATH . 'includes/admin/settings-register.php';
require_once FSB_PATH . 'includes/ajax.php';

// assets
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'fsb-style',
        FSB_URL . 'assets/css/style.css'
    );

    wp_enqueue_script(
        'fsb-script',
        FSB_URL . 'assets/js/script.js',
        ['jquery'],
        null,
        true
    );

    wp_localize_script('fsb-script', 'fsbData', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
});

add_filter('woocommerce_locate_template', function ($template, $template_name, $template_path) {

    $plugin_path = FSB_PATH . 'templates/woocommerce/';

    $custom_template = $plugin_path . $template_name;

    if (file_exists($custom_template)) {
        return $custom_template;
    }

    return $template;

}, 10, 3);