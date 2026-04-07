<?php

if (!defined('ABSPATH')) exit;

add_action('admin_init', function () {
  register_setting('fsb_settings_group', 'fsb_settings', [
    'sanitize_callback' => 'fsb_sanitize_settings'
  ]);
});

function fsb_sanitize_settings($input) {
  $output = [];

  $output['min_trigger']   = isset($input['min_trigger']) ? floatval($input['min_trigger']) : 0;
  $output['free_shipping'] = isset($input['free_shipping']) ? floatval($input['free_shipping']) : 0;

  $output['button_type'] = in_array($input['button_type'] ?? '', ['category', 'url'])
      ? $input['button_type']
      : 'category';

  $output['category'] = isset($input['category']) ? intval($input['category']) : 0;

  $output['url']   = isset($input['url']) ? esc_url_raw($input['url']) : '';
  $output['label'] = isset($input['label']) ? sanitize_text_field($input['label']) : '';

  return $output;
}