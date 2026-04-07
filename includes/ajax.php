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

add_action('wp_ajax_fsb_get_button', 'fsb_get_button');
add_action('wp_ajax_nopriv_fsb_get_button', 'fsb_get_button');

function fsb_get_button() {

    $data = fsb_get_bar_data();

    if (!$data) {
        wp_die();
    }

    ?>
    <div class="fsb-button--wrapper">
        <a href="<?php echo esc_url($data['link']); ?>" class="fsb-button">
            <?php echo esc_html($data['label']); ?>
        </a>
    </div>
    <?php

    wp_die();
}