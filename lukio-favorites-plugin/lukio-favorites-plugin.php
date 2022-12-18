<?php
/* 
 * Plugin Name: Lukio Favorites Plugin
 * Author: Itai Dotan By <a href="https://lukio.pro">Lukio</a>
 * Text Domain: lukio-favorites-plugin
 * Domain Path: /languages/
 */

require_once __DIR__ . '/inc/lukio_favorites_class.php';
require_once __DIR__ . '/inc/setup.php';
require_once __DIR__ . '/inc/admin_page_functions.php';

if (!function_exists('lukio_favorites_activation')) {
    function lukio_favorites_activation()
    {
        $lukio_favoritesorites = lukio_favorites();
        if (!get_option('lukio_favorites_plugin_options')) {
            add_option('lukio_favorites_plugin_options', $lukio_favoritesorites->get_default_options());
        }
        $lukio_favoritesorites->create_table();
    }
}
register_activation_hook(__FILE__, 'lukio_favorites_activation');

if (!function_exists('lukio_favorites_uninstall')) {
    function lukio_favorites_uninstall()
    {
        lukio_favorites()->drop_table();
        delete_option('lukio_favorites_plugin_options');
    }
}
register_uninstall_hook(__FILE__, 'lukio_favorites_uninstall');
