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

// assets
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('fsb-style', FSB_URL . 'assets/css/style.css');
    wp_enqueue_script('fsb-script', FSB_URL . 'assets/js/script.js', ['jquery'], null, true);
});