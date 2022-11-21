<?php
/* 
 * Plugin Name: Lukio Favorites Plugin
 * Author: Itai Dotan
 * Text Domain: lukio-favorites-plugin
 * Domain Path: /languages/
 */

require_once __DIR__ . '/inc/setup.php';
require_once __DIR__ . '/inc/admin_page_functions.php';

if (!function_exists('lukio_favorites_activation')) {
    function lukio_favorites_activation()
    {
        $lukio_favorites = lukio_favorites();
        if (!get_option('lukio_favorites')) {
            add_option('lukio_favorites', $lukio_favorites->get_default_options());
        }
        $lukio_favorites->create_table();
    }
}
register_activation_hook(__FILE__, 'lukio_favorites_activation');

if (!function_exists('lukio_favorites_uninstall')) {
    function lukio_favorites_uninstall()
    {
        lukio_favorites()->drop_table();
        delete_option('lukio_favorites');
    }
}
register_uninstall_hook(__FILE__, 'lukio_favorites_uninstall');
