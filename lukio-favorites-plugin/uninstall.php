<?php

/**
 * uninstall action to take when uninstalling the plugin
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// include to get the class constants of the meta keys
require_once __DIR__ . '/inc/lukio-favorites-class.php';

// delete plugin options
delete_option(Lukio_Favorites_Class::OPTIONS_META_KEY);

// delete posts favorites count
delete_metadata('post', 0, Lukio_Favorites_Class::POST_FAVORITES_COUNT, '', true);

// delete users favorites
delete_metadata('user', 0, Lukio_Favorites_Class::USER_FAVORITES, '', true);
