<?php
/**
 * Plugin Name:       Free Shipping Bar
 * Plugin URI:        https://github.com/mariliacamara/free-shipping-bar
 * Description:       Mostra no carrinho e no checkout quanto falta para os portes grátis, com um botão para uma categoria ou página de promoções.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * Author:            Marília Câmara
 * Author URI:        https://marilia.dev.br
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('ABSPATH')) exit;

define('FSB_PATH', plugin_dir_path(__FILE__));
define('FSB_URL', plugin_dir_url(__FILE__));

// includes
require_once FSB_PATH . 'includes/ajax.php';
require_once FSB_PATH . 'includes/hooks.php';
require_once FSB_PATH . 'includes/helpers.php';
require_once FSB_PATH . 'includes/admin/settings-page.php';
require_once FSB_PATH . 'includes/admin/settings-register.php';

// assets
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'fsb-style',
        FSB_URL . 'assets/css/style.css'
    );

    wp_localize_script('fsb-script', 'fsbData', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
});