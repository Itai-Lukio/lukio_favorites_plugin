<?php
/* 
 * Plugin Name: Lukio Favorites Plugin
 * Plugin URI: https://lukio.pro
 * Author: Itai Dotan @Lukio
 * Author URI: https://lukio.pro/
 * Description: Favorites plugin to simply allow users to mark as favorite any post type selected in the plugin options
 * Version: 1.0
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Text Domain: lukio-favorites-plugin
 * Domain Path: /languages/
 */

defined('ABSPATH') || exit;

// setup constants for the plugin dir and url to use across the plugin
define('LUKIO_FAVORITES_PLUGIN_URL', plugin_dir_url(__FILE__));
define('LUKIO_FAVORITES_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LUKIO_FAVORITES_PLUGIN_MAIN_FILE', basename(__DIR__) . '/' . basename(__FILE__));

// require the needed plugin php files
require_once __DIR__ . '/inc/lukio-favorites-class.php';
require_once __DIR__ . '/inc/setup.php';
require_once __DIR__ . '/inc/admin-page-functions.php';

/**
 * setup the needed plugin parts when activating the plugin
 * 
 * @author Itai Dotan
 */
function lukio_favorites_activation()
{
    $lukio_favorites = lukio_favorites();
    if (!get_option(Lukio_Favorites_Class::OPTIONS_META_KEY)) {
        // create the option when not set using the default options
        add_option(Lukio_Favorites_Class::OPTIONS_META_KEY, $lukio_favorites->get_default_options());
    }

    $lukio_favorites->create_pages();
}

register_activation_hook(__FILE__, 'lukio_favorites_activation');

/**
 * deactivation actions to take
 */
function lukio_favorites_deactivate()
{
    // move the favorites page to the trash
    wp_trash_post(lukio_favorites()->get_active_options()['favorites_page_id']);
}
register_deactivation_hook(__FILE__, 'lukio_favorites_deactivate');
