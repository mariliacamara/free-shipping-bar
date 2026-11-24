<?php

if (!defined('ABSPATH')) exit;

add_action('admin_init', function () {
  register_setting('fsb_settings_group', 'fsb_settings', [
    'sanitize_callback' => 'fsb_sanitize_settings'
  ]);
});

function fsb_sanitize_settings($input) {
  $input    = is_array($input) ? $input : [];
  $defaults = fsb_default_options();
  $output   = [];

  $output['min_trigger'] = isset($input['min_trigger']) ? max(0, floatval($input['min_trigger'])) : 0;

  $output['button_type'] = in_array($input['button_type'] ?? '', ['category', 'page', 'url'], true)
      ? $input['button_type']
      : '';

  $output['category'] = isset($input['category']) ? intval($input['category']) : 0;
  $output['page']     = isset($input['page']) ? intval($input['page']) : 0;
  $output['url']      = isset($input['url']) ? esc_url_raw($input['url']) : '';
  $output['label']    = isset($input['label']) ? sanitize_text_field($input['label']) : '';

  foreach (['color_remaining', 'color_success', 'color_button'] as $key) {
    $color        = isset($input[$key]) ? sanitize_hex_color($input[$key]) : '';
    $output[$key] = $color ? $color : $defaults[$key];
  }

  return $output;
}
