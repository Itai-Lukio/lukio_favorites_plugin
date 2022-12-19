<?php
/* 
 * Plugin Name: Lukio Favorites Plugin
 * Author: Itai Dotan By <a href="https://lukio.pro">Lukio</a>
 * Text Domain: lukio-favorites-plugin
 * Domain Path: /languages/
 */

// setup constants for the plugin dir and url to use across the plugin
define('LUKIO_FAVORITES_PLUGIN_URL', plugin_dir_url(__FILE__));
define('LUKIO_FAVORITES_PLUGIN_DIR', plugin_dir_path(__FILE__));

// require the needed plugin php files
require_once __DIR__ . '/inc/lukio_favorites_class.php';
require_once __DIR__ . '/inc/setup.php';
require_once __DIR__ . '/inc/admin_page_functions.php';

if (!function_exists('lukio_favorites_activation')) {
    /**
     * setup the needed plugin parts when activating the plugin
     * 
     * @author Itai Dotan
     */
    function lukio_favorites_activation()
    {
        $lukio_favoritesorites = lukio_favorites();
        if (!get_option('lukio_favorites_plugin_options')) {
            add_option('lukio_favorites_plugin_options', $lukio_favoritesorites->get_default_options());
        }
    }
}
register_activation_hook(__FILE__, 'lukio_favorites_activation');

if (!function_exists('lukio_favorites_uninstall')) {
    /**
     * run on uninstall and clean the plugin added meta and options
     * 
     * @author Itai Dotan
     */
    function lukio_favorites_uninstall()
    {
        // delete plugin options
        delete_option('lukio_favorites_plugin_options');

        // loop to clean saved post favorites count meta
        $posts_query = new WP_Query(array(
            'post_type' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => array(
                array(
                    'key' => 'lukio_favorites_count',
                    'compare' => 'EXISTS',
                ),
            ),
        ));
        foreach ($posts_query->posts as $post_id) {
            delete_post_meta($post_id, 'lukio_favorites_count');
        }

        // loop to clean saved user favorites meta
        $users_query = new WP_User_Query(array(
            'fields' => 'ID',
            'meta_query' => array(
                array(
                    'key' => 'lukio_favorites_user_favorites',
                    'compare' => 'EXISTS',
                ),
            )
        ));
        foreach ($users_query->get_results() as $user_id) {
            delete_user_meta($user_id, 'lukio_favorites_user_favorites');
        }
    }
}
register_uninstall_hook(__FILE__, 'lukio_favorites_uninstall');
