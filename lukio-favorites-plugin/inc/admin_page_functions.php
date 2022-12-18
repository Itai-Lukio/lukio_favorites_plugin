<?php

/**
 * lukio favorites admin menu and page
 */
class lukio_favorites_admin_class
{
    /**
     * add actions of the class
     * 
     * @author Itai Dotan
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_page_enqueue'));

        add_action('wp_ajax_lukio_favorites_get_preview_img', array($this, 'get_preview_img'));
    }

    /**
     * add the favorites admin menu
     * 
     * @author Itai Dotan
     */
    public function admin_menu()
    {
        add_menu_page(
            __('Lukio favorites', 'lukio-favorites-plugin'),
            __('Lukio favorites', 'lukio-favorites-plugin'),
            'manage_options',
            'lukio_favorites',
            array($this, 'admin_page_markup'),
            'dashicons-lukio_fav-H_lu',
            30
        );
    }

    /**
     * enqueue the needed styles and scripts for the favorites page
     * 
     * @author Itai Dotan
     */
    public function admin_page_enqueue()
    {
        wp_enqueue_style('lukio_favorites_admin_menu_stylesheet', WP_PLUGIN_URL . '/lukio-favorites-plugin/assets/css/Lukio_fav.min.css', [], filemtime(WP_PLUGIN_DIR . '/lukio-favorites-plugin/assets/css/Lukio_fav.min.css'));

        if (get_current_screen()->base == 'toplevel_page_lukio_favorites') {
            wp_enqueue_media();
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_style('lukio_favorites_stylesheet', WP_PLUGIN_URL . '/lukio-favorites-plugin/assets/css/lukio_favorites.min.css', [], filemtime(WP_PLUGIN_DIR . '/lukio-favorites-plugin/assets/css/lukio_favorites.min.css'));
            wp_enqueue_style('lukio_favorites_admin_stylesheet', WP_PLUGIN_URL . '/lukio-favorites-plugin/assets/css/lukio_favorites_admin.min.css', [], filemtime(WP_PLUGIN_DIR . '/lukio-favorites-plugin/assets/css/lukio_favorites_admin.min.css'));
            wp_enqueue_script('lukio_favorites_admin_scripts', WP_PLUGIN_URL . '/lukio-favorites-plugin/assets/js/lukio_favorites_admin.min.js', ['jquery', 'wp-color-picker'], filemtime(WP_PLUGIN_DIR . '/lukio-favorites-plugin/assets/js/lukio_favorites_admin.min.js'));
            wp_localize_script(
                'lukio_favorites_admin_scripts',
                'lukio_favorites_ajax',
                array('ajax_url' => admin_url('admin-ajax.php'))
            );
        };
    }

    /**
     * markup for the favorites admin page
     * 
     * @author Itai Dotan
     */
    public function admin_page_markup()
    {
        // call the save function when posted and the checks are valid
        if (
            isset($_POST['action']) && $_POST['action'] == 'lukio_favorites_save_options' &&
            isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'lukio_fav_save_options')
        ) {
            $this->save_options();
        }

        $lukio_favorites = lukio_favorites();
        $custom_button = $lukio_favorites->get('custom_button');
        $button_color = $lukio_favorites->get('button_color');
        $custom_button_off = $lukio_favorites->get('custom_button_off');
        $custom_button_on = $lukio_favorites->get('custom_button_on');
?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <form action="" method="post">
                <input type="hidden" name="action" value="lukio_favorites_save_options">
                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('lukio_fav_save_options'); ?>">

                <?php
                if (function_exists('is_plugin_active')) {
                    if (is_plugin_active('woocommerce/woocommerce.php')) {
                ?>
                        <div class="lukio_favorirs_switch_add_to_title_wrapper">
                            <span><?php echo __('Add Button to product title', 'lukio-favorites-plugin'); ?></span>
                            <label class="lukio_favorites_switch" for="add_to_title">
                                <input class="lukio_favorites_switch_input" type="checkbox" name="lukio_favorites[add_to_title]" id="add_to_title" <?php echo $lukio_favorites->get('add_to_title') ? ' checked' : ''; ?> autocomplete="off">
                                <span class="lukio_favorites_switch_slider"></span>
                            </label>
                        </div>
                <?php
                    }
                }
                ?>

                <div class="lukio_favorites_switch_image_wrapper">
                    <span><?php echo __('Use custom image', 'lukio-favorites-plugin'); ?></span>
                    <label class="lukio_favorites_switch" for="custom_button">
                        <input class="lukio_favorites_switch_input" type="checkbox" name="lukio_favorites[custom_button]" id="custom_button" <?php echo $custom_button ? ' checked' : ''; ?> autocomplete="off">
                        <span class="lukio_favorites_switch_slider"></span>
                    </label>
                </div>

                <div class="lukio_custom_button_wrapper<?php if ($custom_button) {
                                                            echo ' hide_option';
                                                        } ?>">
                    <input type="text" class="lukio_color_picker" name="lukio_favorites[button_color]" id="button_color" value="<?php echo $button_color; ?>" autocomplete="off">
                    <input type="hidden" id="lukio_default_color" name="lukio_default_color" value="<?php echo $lukio_favorites->get('default_color'); ?>">

                    <div class="lukio_default_btn_wrapper">
                        <div class="lukio_favorites_button" data-lukio-fav="0">
                            <?php $lukio_favorites->get('default_svg'); ?>
                        </div>
                        <div class="lukio_favorites_button" data-lukio-fav="1">
                            <?php $lukio_favorites->get('default_svg'); ?>
                        </div>
                        <style>
                            .lukio_favorites_button[data-lukio-fav="0"] .lukio_pre_fav {
                                fill: <?php echo $button_color; ?>;
                            }

                            .lukio_favorites_button[data-lukio-fav="1"] .lukio_fav {
                                fill: <?php echo $button_color; ?>;
                            }
                        </style>
                    </div>
                </div>

                <div class="lukio_custom_images_wrapper<?php if (!$custom_button) {
                                                            echo ' hide_option';
                                                        } ?>">
                    <label class="lukio_custom_images_label" for="custom_button_off_btn">
                        <span class="custom_image_span"><?php _e('Image when not in favorites', 'lukio-favorites-plugin'); ?></span>
                        <img class="preview_image" src="<?php echo wp_get_attachment_image_url($custom_button_off, 'medium', false); ?>" alt="">
                        <input type="hidden" value="<?php echo $custom_button_off; ?>" class="regular-text process_custom_images" id="custom_button_off" name="lukio_favorites[custom_button_off]">
                        <button id="custom_button_off_btn" class="set_custom_images button"><?php _e('Pick image', 'lukio-favorites-plugin'); ?></button>
                    </label>

                    <label class="lukio_custom_images_label" for="custom_button_on_btn">
                        <span class="custom_image_span"><?php _e('Image when in favorites', 'lukio-favorites-plugin'); ?></span>
                        <img class="preview_image" src="<?php echo wp_get_attachment_image_url($custom_button_on, 'medium', false); ?>" alt="">
                        <input type="hidden" value="<?php echo $custom_button_on; ?>" class="regular-text process_custom_images" id="custom_button_on" name="lukio_favorites[custom_button_on]">
                        <button id="custom_button_on_btn" class="set_custom_images button"><?php _e('Pick image', 'lukio-favorites-plugin'); ?></button>
                    </label>
                </div>

                <button class="button button-primary button-large" type="submit"><?php echo __('Save Settings', 'lukio-favorites-plugin') ?></button>
            </form>
        </div>
<?php
    }

    /**
     * save the new posted options
     * 
     * @author Itai Dotan
     */
    private function save_options()
    {
        $lukio_favorites = lukio_favorites();
        $options =  $lukio_favorites->get_default_options();
        // set all bools to false because form dont post unchecked checkboxes
        foreach ($lukio_favorites->get_default_options_bools() as $bool) {
            $options[$bool] = false;
        }
        foreach ($_POST['lukio_favorites'] as $key => $value) {
            if ($key == 'custom_button' || $key == 'add_to_title') {
                $options[$key] =  true;
            } else if ($key == 'button_color') {
                $options[$key] = $value;
            } else {
                $options[$key] = (int)$value;
            }
        }
        update_option('lukio_favorites_plugin_options', $options);
        $lukio_favorites->update_options();
    }

    public function get_preview_img()
    {
        if (isset($_GET['id'])) {
            $image = wp_get_attachment_image(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT), 'medium', false, array('class' => 'preview_image'));
            $data = array(
                'success' => true,
                'image' => $image,
            );
        } else {
            $data = array(
                'success' => false,
            );
        }
        echo json_encode($data);
        die;
    }
}
new lukio_favorites_admin_class();
