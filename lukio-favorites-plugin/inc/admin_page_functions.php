<?php
if (!function_exists('lukio_favorites_admin_menu')) {
    function lukio_favorites_admin_menu()
    {
        wp_enqueue_style('lukio_favorites_admin_menu_stylesheet', WP_PLUGIN_URL . '/lukio-favorites-plugin/assets/css/Lukio_fav.min.css', [], filemtime(WP_PLUGIN_DIR . '/lukio-favorites-plugin/assets/css/Lukio_fav.min.css'));
        add_menu_page(
            __('Lukio favorites', 'lukio-favorites-plugin'),
            __('Favorites', 'lukio-favorites-plugin'),
            'manage_options',
            'lukio_favorites',
            'lukio_favorites_admin_page_html',
            'dashicons-lukio_fav-H_lu',
            30
        );

        if (
            isset($_POST['action']) && $_POST['action'] == 'lukio_favorites_save_options' &&
            isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'lukio_fav_save_options')
        ) {
            lukio_favorites_save_options();
        }
    }
}
add_action('admin_menu', 'lukio_favorites_admin_menu');

if (!function_exists('lukio_favorites_admin_page_style_script_enqueue')) {
    function lukio_favorites_admin_page_style_script_enqueue()
    {
        if (get_current_screen()->base == 'toplevel_page_lukio_favorites') {
            wp_enqueue_media();
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_style('lukio_favorites_stylesheet', WP_PLUGIN_URL . '/lukio-favorites-plugin/assets/css/lukio_favorites.min.css', [], filemtime(WP_PLUGIN_DIR . '/lukio-favorites-plugin/assets/css/lukio_favorites.min.css'));
            wp_enqueue_style('lukio_favorites_admin_stylesheet', WP_PLUGIN_URL . '/lukio-favorites-plugin/assets/css/lukio_favorites_admin.min.css', [], filemtime(WP_PLUGIN_DIR . '/lukio-favorites-plugin/assets/css/lukio_favorites_admin.min.css'));
            wp_enqueue_script('lukio_favorites_admin_scripts', WP_PLUGIN_URL . '/lukio-favorites-plugin/assets/js/lukio_favorites_admin.min.js', ['jquery', 'wp-color-picker'], filemtime(WP_PLUGIN_DIR . '/lukio-favorites-plugin/assets/js/lukio_favorites_admin.min.js'));
        };
    }
}
add_action('admin_enqueue_scripts', 'lukio_favorites_admin_page_style_script_enqueue');

function lukio_favorites_admin_page_html()
{
    $options = get_option('lukio_favorites');
    $custom_button = $options['custom_button'];
    $lukio_fav = lukio_favorites();
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form action="" method="post">
            <input type="hidden" name="action" value="lukio_favorites_save_options">
            <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('lukio_fav_save_options'); ?>">
            <label for="custom_button">
                <input type="checkbox" name="lukio_favorites[custom_button]" id="custom_button" <?php echo $custom_button ? ' checked' : (''); ?> autocomplete="off">
            </label>

            <div class="lukio_custom_button_wrapper<?php if ($custom_button) {
                                                        echo ' hide_option';
                                                    } ?>">
                <input type="text" class="lukio_color_picker" name="lukio_favorites[button_color]" id="button_color" value="<?php echo $options['button_color']; ?>" autocomplete="off">
                <input type="hidden" id="lukio_default_color" name="lukio_default_color" value="<?php echo $lukio_fav->get_default_color(); ?>">

                <div class="lukio_default_btn_wrapper">
                    <div class="lukio_favorites_button" data-lukio-fav="0">
                        <?php $lukio_fav->get_default_svg(); ?>
                    </div>
                    <div class="lukio_favorites_button" data-lukio-fav="1">
                        <?php $lukio_fav->get_default_svg(); ?>
                    </div>
                    <style>
                        .lukio_favorites_button[data-lukio-fav="0"] .lukio_pre_fav {
                            fill: <?php echo $options['button_color']; ?>;
                        }

                        .lukio_favorites_button[data-lukio-fav="1"] .lukio_fav {
                            fill: <?php echo $options['button_color']; ?>;
                        }
                    </style>
                </div>
            </div>

            <div class="lukio_custom_images_wrapper<?php if (!$custom_button) {
                                                        echo ' hide_option';
                                                    } ?>">
                <label class="lukio_custom_images_label" for="custom_button_off_btn">
                    <span class="custom_image_span"><?php _e('Image when not in favorites', 'lukio-favorites-plugin'); ?></span>
                    <img class="preview_image" src="<?php echo wp_get_attachment_image_url($options['custom_button_off'], 'medium', false); ?>" alt="">
                    <input type="hidden" value="<?php echo $options['custom_button_off']; ?>" class="regular-text process_custom_images" id="custom_button_off" name="lukio_favorites[custom_button_off]">
                    <button id="custom_button_off_btn" class="set_custom_images button"><?php _e('Pick image', 'lukio-favorites-plugin'); ?></button>
                </label>

                <label class="lukio_custom_images_label" for="custom_button_on_btn">
                    <span class="custom_image_span"><?php _e('Image when in favorites', 'lukio-favorites-plugin'); ?></span>
                    <img class="preview_image" src="<?php echo wp_get_attachment_image_url($options['custom_button_on'], 'medium', false); ?>" alt="">
                    <input type="hidden" value="<?php echo $options['custom_button_on']; ?>" class="regular-text process_custom_images" id="custom_button_on" name="lukio_favorites[custom_button_on]">
                    <button id="custom_button_on_btn" class="set_custom_images button"><?php _e('Pick image', 'lukio-favorites-plugin'); ?></button>
                </label>
            </div>

            <button class="button button-primary button-large" type="submit"><?php echo __('Save Settings', 'lukio-favorites-plugin') ?></button>
        </form>
    </div>
<?php
}

function lukio_favorites_save_options()
{
    $options =  lukio_favorites()->get_default_options();
    foreach ($_POST['lukio_favorites'] as $key => $value) {
        if ($key == 'custom_button') {
            $options[$key] = $value == 'on' ? true : 0;
        } else if ($key == 'button_color') {
            $options[$key] = $value;
        } else {
            $options[$key] = (int)$value;
        }
    }
    update_option('lukio_favorites', $options);
}

function lukio_favorites_get_preview_img()
{
    if (isset($_GET['id'])) {
        $image = wp_get_attachment_image(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT), 'medium', false, array('class' => 'preview_image'));
        $data = array(
            'image'    => $image,
        );
        wp_send_json_success($data);
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_lukio_favorites_get_preview_img', 'lukio_favorites_get_preview_img');
