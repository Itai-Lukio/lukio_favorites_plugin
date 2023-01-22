<?php

/**
 * display the plugin admin option page
 */

// setup needed vars
$lukio_favorites = lukio_favorites();
$active_options = lukio_favorites()->get_active_options();
$custom_button = $active_options['custom_button'];
$button_color = $active_options['button_color'];
$custom_button_off = $active_options['custom_button_off'];
$custom_button_on = $active_options['custom_button_on'];
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form action="" method="post">
        <input type="hidden" name="action" value="lukio_favorites_save_options">
        <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('lukio_fav_save_options'); ?>">

        <ul class="lukio_favorirs_options_tabs_wrapper">
            <li class="lukio_favorirs_options_tab active" data-tab="0"><?php echo __('General options', 'lukio-favorites-plugin'); ?></li>
            <li class="lukio_favorirs_options_tab" data-tab="1"><?php echo __('Button options', 'lukio-favorites-plugin'); ?></li>
        </ul>

        <div class="lukio_favorirs_options_tab_content active" data-tab="0">
            <?php include LUKIO_FAVORITES_PLUGIN_DIR . 'admin/page-parts/general-options.php' ?>
        </div>

        <div class="lukio_favorirs_options_tab_content" data-tab="1">
            <?php include LUKIO_FAVORITES_PLUGIN_DIR . 'admin/page-parts/button-options.php' ?>
        </div>

        <button class="button button-primary button-large" type="submit"><?php echo __('Save Settings', 'lukio-favorites-plugin') ?></button>
    </form>
</div>