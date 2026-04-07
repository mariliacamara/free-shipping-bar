<?php

if (!defined('ABSPATH')) exit;

add_action('admin_menu', function () {
    add_menu_page(
        'Free Shipping Bar',
        'Free Shipping',
        'manage_options',
        'fsb-settings',
        'fsb_render_settings_page',
        'dashicons-cart'
    );
});

function fsb_render_settings_page() {
    $options = get_option('fsb_settings');
    ?>

    <div class="wrap">
        <h1>Free Shipping Bar</h1>

        <form method="post" action="options.php">
            <?php settings_fields('fsb_settings_group'); ?>

            <table class="form-table">

                <!-- VALOR GATILHO -->
                <tr>
                    <th>Mostrar barra a partir de (€)</th>
                    <td>
                        <input type="number" step="0.01" name="fsb_settings[min_trigger]" 
                            value="<?php echo esc_attr($options['min_trigger'] ?? 0); ?>">
                    </td>
                </tr>

                <!-- VALOR FRETE GRATIS -->
                <tr>
                    <th>Valor para frete grátis (€)</th>
                    <td>
                        <input type="number" step="0.01" name="fsb_settings[free_shipping]" 
                            value="<?php echo esc_attr($options['free_shipping'] ?? 20); ?>">
                    </td>
                </tr>

                <!-- TIPO DE LINK -->
                <tr>
                    <th>Tipo do botão</th>
                    <td>
                        <select name="fsb_settings[button_type]" id="fsb_button_type">
                            <option value="category" <?php selected($options['button_type'] ?? '', 'category'); ?>>
                                Categoria
                            </option>
                            <option value="url" <?php selected($options['button_type'] ?? '', 'url'); ?>>
                                URL personalizada
                            </option>
                        </select>
                    </td>
                </tr>

                <!-- CATEGORIA -->
                <tr id="fsb_category_row">
                    <th>Categoria</th>
                    <td>
                        <?php
                        wp_dropdown_categories([
                            'taxonomy' => 'product_cat',
                            'name' => 'fsb_settings[category]',
                            'selected' => $options['category'] ?? '',
                            'show_option_all' => 'Selecione'
                        ]);
                        ?>
                    </td>
                </tr>

                <!-- URL -->
                <tr id="fsb_url_row">
                    <th>URL</th>
                    <td>
                        <input type="text" name="fsb_settings[url]" 
                            value="<?php echo esc_attr($options['url'] ?? ''); ?>">
                    </td>
                </tr>

                <!-- LABEL -->
                <tr id="fsb_label_row">
                    <th>Label do botão</th>
                    <td>
                        <input type="text" name="fsb_settings[label]" 
                            value="<?php echo esc_attr($options['label'] ?? 'Ver promoções'); ?>">
                    </td>
                </tr>

            </table>

            <?php submit_button(); ?>
        </form>
    </div>

    <script>
        const toggleFields = () => {
            const type = document.getElementById('fsb_button_type').value;

            document.getElementById('fsb_category_row').style.display = type === 'category' ? '' : 'none';
            document.getElementById('fsb_url_row').style.display = type === 'url' ? '' : 'none';
            document.getElementById('fsb_label_row').style.display = type === 'url' ? '' : 'none';
        };

        document.getElementById('fsb_button_type').addEventListener('change', toggleFields);
        toggleFields();
    </script>

    <?php
}