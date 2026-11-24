<?php
/**
 * Página no menu Camorim Tech.
 * wp eval-file .../tests/test-menu.php <loja>
 */

require __DIR__ . '/bootstrap.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

$admins = get_users(array('role' => 'administrator', 'number' => 1, 'fields' => 'ID'));
wp_set_current_user((int) $admins[0]);

fsb_assert(function_exists('camorim_tech_menu_register') && function_exists('fsb_register_admin_page'), 'menu Camorim Tech e registo da página existem');
fsb_assert(md5_file(FSB_PATH . 'includes/camorim-tech-menu.php') === 'd222aea92327ac4d2437f3b6c0785b91', 'ficheiro do menu igual ao do Buy Again');

$GLOBALS['menu'] = $GLOBALS['submenu'] = $GLOBALS['_wp_submenu_nopriv'] = $GLOBALS['_registered_pages'] = $GLOBALS['admin_page_hooks'] = array();
camorim_tech_menu_register();
fsb_register_admin_page();
camorim_tech_menu_finalize();
$sub = isset($GLOBALS['submenu']['camorim-tech']) ? array_column($GLOBALS['submenu']['camorim-tech'], 2) : array();
$topos = array_column($GLOBALS['menu'], 2);
fsb_assert(in_array('fsb-settings', $sub, true), 'Free Shipping Bar dentro de Camorim Tech');
fsb_assert(!in_array('fsb-settings', $topos, true), 'sem menu próprio «Free Shipping»');
fsb_assert(FSB_VERSION === '1.2.0', 'versão 1.2.0 (' . FSB_VERSION . ')');

fsb_test_finish();
