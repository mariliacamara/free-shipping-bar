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

// assets
add_action('wp_enqueue_scripts', function () {

    // só carrega no carrinho
    if (function_exists('is_cart') && !is_cart()) {
        return;
    }

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

    // garante que WooCommerce está pronto
    if (function_exists('WC') && WC()->cart) {

        $data = fsb_get_bar_data();

        ob_start();

        if ($data) {
            ?>
            <div class="fsb-button--wrapper">
                <a href="<?php echo esc_url($data['link']); ?>" class="fsb-button">
                    <?php echo esc_html($data['label']); ?>
                </a>
            </div>
            <?php
        }

        $button_html = ob_get_clean();

        wp_localize_script('fsb-script', 'fsbData', [
            'button_html' => $button_html,
            'remaining'   => $data['remaining'] ?? 0
        ]);
    }
});