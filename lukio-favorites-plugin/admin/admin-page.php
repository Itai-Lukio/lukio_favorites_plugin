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
                'name' => __('General options', 'lukio-favorites-plugin'),
                'file_path' => LUKIO_FAVORITES_PLUGIN_DIR . 'admin/page-parts/general-options.php'
            ),
            array(
                'name' => __('Button options', 'lukio-favorites-plugin'),
                'file_path' => LUKIO_FAVORITES_PLUGIN_DIR . 'admin/page-parts/button-options.php'
            ),
            array(
                'name' => __('Menu options', 'lukio-favorites-plugin'),
                'file_path' => LUKIO_FAVORITES_PLUGIN_DIR . 'admin/page-parts/menu-options.php'
            ),
            array(
                'name' => __('Extra css', 'lukio-favorites-plugin'),
                'file_path' => LUKIO_FAVORITES_PLUGIN_DIR . 'admin/page-parts/extra-css.php'
            ),
            array(
                'name' => __('Info', 'lukio-favorites-plugin'),
                'file_path' => LUKIO_FAVORITES_PLUGIN_DIR . 'admin/page-parts/info.php'
            ),
        );

        $tabs_content_markup = '';

        // check if there is a selected tab to show
        $active_tab_index = isset($_REQUEST['tab']) ? (int)$_REQUEST['tab'] : 0;
        $active_tab_index = $active_tab_index != 0 && $active_tab_index < count($tabs) ? $active_tab_index : 0;
        ?>
        <ul class="lukio_favorites_options_tabs_wrapper">
            <?php
            // loop over the tabs to create their li and content markup
            foreach ($tabs as $index => $tab_data) {
                $active = $index == $active_tab_index ? ' active' : '';
            ?>
                <li class="lukio_favorites_options_tab<?php echo $active; ?>" data-tab="<?php echo $index; ?>"><?php echo $tab_data['name']; ?></li>

                <?php
                ob_start();
                ?>
                <div class="lukio_favorites_options_tab_content<?php echo $active; ?>" data-tab="<?php echo $index; ?>">
                    <?php include $tab_data['file_path']; ?>
                </div>
            <?php
                // add the tab content markup to the overall tabs content
                $tabs_content_markup .= ob_get_clean();
            }
            ?>
        </ul>

        <?php
        // print all the tabs content
        echo $tabs_content_markup;
        ?>

        <button class="button button-primary button-large" type="submit"><?php echo __('Save Settings', 'lukio-favorites-plugin'); ?></button>
    </form>

    <style>
        <?php
        echo $lukio_favorites->dynamic_css();
        ?>
    </style>
</div>