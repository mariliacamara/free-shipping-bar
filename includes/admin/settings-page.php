<?php

if (!defined('ABSPATH')) exit;

add_action('admin_menu', function () {
    $hook = add_menu_page(
        'Free Shipping Bar',
        'Free Shipping',
        'manage_options',
        'fsb-settings',
        'fsb_render_settings_page',
        'dashicons-cart'
    );

    add_action('admin_enqueue_scripts', function ($current) use ($hook) {
        if ($current !== $hook) {
            return;
        }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('fsb-admin', FSB_URL . 'assets/admin/admin.css', [], FSB_VERSION);
        wp_enqueue_script('fsb-admin', FSB_URL . 'assets/admin/admin.js', ['jquery', 'wp-color-picker'], FSB_VERSION, true);
    });
});

/**
 * Zonas com envio grátis por valor mínimo, para mostrar na página.
 *
 * @return array Lista de ['zone' => string, 'amount' => float, 'url' => string].
 */
function fsb_admin_free_shipping_zones() {
    if (!class_exists('WC_Shipping_Zones')) {
        return [];
    }

    $zones   = WC_Shipping_Zones::get_zones();
    $zones[] = ['zone_id' => 0, 'zone_name' => 'Resto do mundo', 'shipping_methods' => (new WC_Shipping_Zone(0))->get_shipping_methods()];

    $out = [];
    foreach ($zones as $zone) {
        foreach ($zone['shipping_methods'] as $method) {
            if ($method->id !== 'free_shipping' || $method->enabled !== 'yes') {
                continue;
            }
            if (!in_array($method->requires, ['min_amount', 'either', 'both'], true) || (float) $method->min_amount <= 0) {
                continue;
            }
            $out[] = [
                'zone'   => $zone['zone_name'],
                'amount' => (float) $method->min_amount,
                'url'    => admin_url('admin.php?page=wc-settings&tab=shipping&zone_id=' . (int) $zone['zone_id']),
            ];
        }
    }
    return $out;
}

function fsb_render_settings_page() {
    $options = fsb_get_options();
    $type    = $options['button_type'];
    $zones   = fsb_admin_free_shipping_zones();
    $types   = [
        ''         => ['Sem botão', 'dashicons-hidden'],
        'category' => ['Categoria', 'dashicons-category'],
        'page'     => ['Página', 'dashicons-admin-page'],
        'url'      => ['URL', 'dashicons-admin-links'],
    ];
    ?>

    <div class="wrap fsb-admin">
        <h1 class="fsb-title"><span class="dashicons dashicons-cart"></span> Free Shipping Bar</h1>
        <p class="fsb-lead">A barra aparece no carrinho e no checkout e diz ao cliente quanto falta para os portes grátis.</p>

        <?php settings_errors(); ?>

        <form method="post" action="options.php" class="fsb-layout">
            <?php settings_fields('fsb_settings_group'); ?>

            <div class="fsb-main">

                <section class="fsb-card">
                    <h2>Valor dos portes grátis</h2>
                    <p class="fsb-muted">Vem do método «Envio grátis» de cada zona da WooCommerce, conforme a morada do cliente. Para mudar, edite a zona.</p>
                    <?php if ($zones): ?>
                        <ul class="fsb-zones">
                            <?php foreach ($zones as $z): ?>
                                <li>
                                    <a href="<?php echo esc_url($z['url']); ?>"><?php echo esc_html($z['zone']); ?></a>
                                    <strong><?php echo wp_kses_post(wc_price($z['amount'])); ?></strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="fsb-alert">Nenhuma zona tem envio grátis por valor mínimo: a barra não aparece.
                            <a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=shipping')); ?>">Configurar zonas</a></div>
                    <?php endif; ?>
                </section>

                <section class="fsb-card">
                    <h2>Quando mostrar</h2>
                    <label class="fsb-field" for="fsb_min_trigger">
                        <span class="fsb-label">Mostrar a partir de</span>
                        <span class="fsb-input-suffix">
                            <input type="number" step="0.01" min="0" id="fsb_min_trigger" name="fsb_settings[min_trigger]"
                                value="<?php echo esc_attr($options['min_trigger']); ?>">
                            <span>€</span>
                        </span>
                        <span class="fsb-help">Total do carrinho a partir do qual a barra aparece. 0 mostra sempre.</span>
                    </label>
                </section>

                <section class="fsb-card">
                    <h2>Botão</h2>
                    <p class="fsb-muted">Aparece por baixo da barra enquanto faltar valor, para levar o cliente a mais produtos.</p>

                    <div class="fsb-segmented" role="radiogroup" aria-label="Destino do botão">
                        <?php foreach ($types as $value => [$label, $icon]): ?>
                            <label class="fsb-segment">
                                <input type="radio" name="fsb_settings[button_type]" value="<?php echo esc_attr($value); ?>" <?php checked($type, $value); ?>>
                                <span><span class="dashicons <?php echo esc_attr($icon); ?>"></span><?php echo esc_html($label); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="fsb-field" data-fsb-show="category">
                        <label class="fsb-label" for="fsb_category">Categoria</label>
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
                        <span class="fsb-help">O texto do botão é «Ver &lt;categoria&gt;» («Ver produtos em promoção» para Promoções).</span>
                    </div>

                    <div class="fsb-field" data-fsb-show="page">
                        <label class="fsb-label" for="fsb_page">Página</label>
                        <?php
                        wp_dropdown_pages([
                            'name'              => 'fsb_settings[page]',
                            'id'                => 'fsb_page',
                            'selected'          => $options['page'],
                            'show_option_none'  => 'Selecione',
                            'option_none_value' => 0,
                        ]);
                        ?>
                    </div>

                    <div class="fsb-field" data-fsb-show="url">
                        <label class="fsb-label" for="fsb_url">URL</label>
                        <input type="url" class="regular-text" id="fsb_url" name="fsb_settings[url]" placeholder="https://"
                            value="<?php echo esc_attr($options['url']); ?>">
                    </div>

                    <div class="fsb-field" data-fsb-show="page url">
                        <label class="fsb-label" for="fsb_label">Texto do botão</label>
                        <input type="text" class="regular-text" id="fsb_label" name="fsb_settings[label]"
                            value="<?php echo esc_attr($options['label']); ?>">
                        <span class="fsb-help">Em branco: «Ver &lt;título da página&gt;» ou «Ver promoções».</span>
                    </div>
                </section>

                <section class="fsb-card">
                    <h2>Cores</h2>
                    <div class="fsb-colors">
                        <?php
                        $colors = [
                            'color_remaining' => 'Barra «faltam X»',
                            'color_success'   => 'Barra «ganhou»',
                            'color_button'    => 'Botão',
                        ];
                        $defaults = fsb_default_options();
                        foreach ($colors as $key => $label): ?>
                            <div class="fsb-color">
                                <span class="fsb-label"><?php echo esc_html($label); ?></span>
                                <input type="text" class="fsb-color-input" data-fsb-color="<?php echo esc_attr($key); ?>"
                                    data-default-color="<?php echo esc_attr($defaults[$key]); ?>"
                                    name="fsb_settings[<?php echo esc_attr($key); ?>]"
                                    value="<?php echo esc_attr($options[$key]); ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <?php submit_button('Guardar alterações', 'primary large'); ?>
            </div>

            <aside class="fsb-side">
                <div class="fsb-card fsb-preview">
                    <h2>Pré-visualização</h2>

                    <span class="fsb-preview-caption">Ainda falta valor</span>
                    <div class="fsb-preview-bar" data-fsb-bg="color_remaining">FALTAM <strong>12,50 €</strong> PARA PORTES GRÁTIS!</div>
                    <a class="fsb-preview-button" data-fsb-bg="color_button" href="#" tabindex="-1"></a>

                    <span class="fsb-preview-caption">Valor atingido</span>
                    <div class="fsb-preview-bar" data-fsb-bg="color_success">🎉 GANHOU ENVIO GRÁTIS!</div>

                    <p class="fsb-help">Valor de exemplo. Na loja, a barra usa o tipo de letra do tema.</p>
                </div>
            </aside>
        </form>

        <footer class="fsb-footer">
            Developed by <strong>Camorim Tech</strong> &middot;
            <a href="https://camorim.dev.br" target="_blank" rel="noopener">camorim.dev.br</a>
        </footer>
    </div>

    <?php
}
