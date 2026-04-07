<?php

if (!defined('ABSPATH')) exit;

add_action('admin_init', function () {

    register_setting('fsb_settings_group', 'fsb_settings');

});