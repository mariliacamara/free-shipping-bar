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
    $options = fsb_get_options();
    $type    = $options['button_type'];
    ?>

    <div class="wrap">
        <h1>Free Shipping Bar</h1>

        <p>O valor dos portes grátis vem das regras de portes da WooCommerce
            (método «Envio grátis» de cada zona), conforme a morada do cliente.</p>

        <form method="post" action="options.php">
            <?php settings_fields('fsb_settings_group'); ?>

            <table class="form-table">

                <tr>
                    <th><label for="fsb_min_trigger">Mostrar barra a partir de (€)</label></th>
                    <td>
                        <input type="number" step="0.01" min="0" id="fsb_min_trigger" name="fsb_settings[min_trigger]"
                            value="<?php echo esc_attr($options['min_trigger']); ?>">
                        <p class="description">0 para mostrar sempre.</p>
                    </td>
                </tr>

                <tr>
                    <th><label for="fsb_button_type">Botão</label></th>
                    <td>
                        <select name="fsb_settings[button_type]" id="fsb_button_type">
                            <option value="" <?php selected($type, ''); ?>>Sem botão</option>
                            <option value="category" <?php selected($type, 'category'); ?>>Categoria de produto</option>
                            <option value="page" <?php selected($type, 'page'); ?>>Página</option>
                            <option value="url" <?php selected($type, 'url'); ?>>URL personalizada</option>
                        </select>
                    </td>
                </tr>

                <tr id="fsb_category_row">
                    <th><label for="fsb_category">Categoria</label></th>
                    <td>
                        <?php
                        wp_dropdown_categories([
                            'taxonomy'        => 'product_cat',
                            'name'            => 'fsb_settings[category]',
                            'id'              => 'fsb_category',
                            'selected'        => $options['category'],
                            'show_option_all' => 'Selecione',
                            'hide_empty'      => false,
                            'hierarchical'    => true,
                        ]);
                        ?>
                        <p class="description">O texto do botão é «Ver &lt;categoria&gt;» («Ver produtos em promoção» para Promoções).</p>
                    </td>
                </tr>

                <tr id="fsb_page_row">
                    <th><label for="fsb_page">Página</label></th>
                    <td>
                        <?php
                        wp_dropdown_pages([
                            'name'             => 'fsb_settings[page]',
                            'id'               => 'fsb_page',
                            'selected'         => $options['page'],
                            'show_option_none' => 'Selecione',
                            'option_none_value' => 0,
                        ]);
                        ?>
                    </td>
                </tr>

                <tr id="fsb_url_row">
                    <th><label for="fsb_url">URL</label></th>
                    <td>
                        <input type="url" class="regular-text" id="fsb_url" name="fsb_settings[url]"
                            value="<?php echo esc_attr($options['url']); ?>">
                    </td>
                </tr>

                <tr id="fsb_label_row">
                    <th><label for="fsb_label">Texto do botão</label></th>
                    <td>
                        <input type="text" class="regular-text" id="fsb_label" name="fsb_settings[label]"
                            value="<?php echo esc_attr($options['label']); ?>">
                        <p class="description">Página: em branco usa «Ver &lt;título da página&gt;».</p>
                    </td>
                </tr>

                <tr>
                    <th>Cores</th>
                    <td>
                        <p><label><input type="color" name="fsb_settings[color_remaining]" value="<?php echo esc_attr($options['color_remaining']); ?>"> Barra «faltam X»</label></p>
                        <p><label><input type="color" name="fsb_settings[color_success]" value="<?php echo esc_attr($options['color_success']); ?>"> Barra «ganhou envio grátis»</label></p>
                        <p><label><input type="color" name="fsb_settings[color_button]" value="<?php echo esc_attr($options['color_button']); ?>"> Botão</label></p>
                    </td>
                </tr>

            </table>

            <?php submit_button(); ?>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var typeSelect = document.getElementById('fsb_button_type');
        var rows = {
            category: document.getElementById('fsb_category_row'),
            page: document.getElementById('fsb_page_row'),
            url: document.getElementById('fsb_url_row'),
            label: document.getElementById('fsb_label_row')
        };

        function toggleFields() {
            var type = typeSelect.value;
            rows.category.style.display = type === 'category' ? '' : 'none';
            rows.page.style.display = type === 'page' ? '' : 'none';
            rows.url.style.display = type === 'url' ? '' : 'none';
            rows.label.style.display = (type === 'page' || type === 'url') ? '' : 'none';
        }

        typeSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
    </script>

    <?php
}
