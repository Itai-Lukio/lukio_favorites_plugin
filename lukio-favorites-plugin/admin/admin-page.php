<?php

/**
 * display the plugin admin option page
 */

defined('ABSPATH') || exit;

// setup needed vars
$lukio_favorites = lukio_favorites();
$active_options = lukio_favorites()->get_active_options();
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form action="" method="post">
        <input type="hidden" name="action" value="lukio_favorites_save_options">
        <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('lukio_fav_save_options'); ?>">

        <?php
        $tabs = array(
            array(
                'title' => __('General options', 'lukio-favorites-plugin'),
                'path' => LUKIO_FAVORITES_PLUGIN_DIR . 'admin/page-parts/general-options.php'
            ),
            array(
                'title' => __('Button options', 'lukio-favorites-plugin'),
                'path' => LUKIO_FAVORITES_PLUGIN_DIR . 'admin/page-parts/button-options.php'
            ),
            array(
                'title' => __('Extra css', 'lukio-favorites-plugin'),
                'path' => LUKIO_FAVORITES_PLUGIN_DIR . 'admin/page-parts/extra-css.php'
            ),
            array(
                'title' => __('Info', 'lukio-favorites-plugin'),
                'path' => LUKIO_FAVORITES_PLUGIN_DIR . 'admin/page-parts/info.php'
            ),
        );

        $tabs_content = '';
        ?>

        <ul class="lukio_favorirs_options_tabs_wrapper">
            <?php
            foreach ($tabs as $index => $tab) {
                $active_mark = $index == 0 ? ' active' : '';
            ?>
                <li class="lukio_favorirs_options_tab<?php echo $active_mark; ?>" data-tab="<?php echo $index ?>" tabindex="0"><?php echo $tab['title']; ?></li>
                <?php
                ob_start();
                ?>
                <div class="lukio_favorirs_options_tab_content<?php echo $active_mark; ?>" data-tab="<?php echo $index ?>">
                    <?php include $tab['path']; ?>
                </div>
            <?php
                $tabs_content .= ob_get_clean();
            }
            ?>
        </ul>

        <?php
        echo $tabs_content;
        ?>

        <button class="button button-primary button-large" type="submit"><?php echo __('Save Settings', 'lukio-favorites-plugin'); ?></button>
    </form>
</div>