<?php

defined('ABSPATH') || exit;

/**
 * Handle the admin side of the plugin
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
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'));

        add_action('plugin_action_links_' . LUKIO_FAVORITES_PLUGIN_MAIN_FILE, array($this, 'plugin_action_links'));

        add_action('wp_ajax_lukio_favorites_get_preview_img', array($this, 'get_preview_img'));

        // add a post display state for plugin pages.
        add_filter('display_post_states', array($this, 'add_display_post_states'), 10, 2);
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
    public function admin_enqueue()
    {
        wp_enqueue_style('lukio_favorites_admin_menu_stylesheet', LUKIO_FAVORITES_PLUGIN_URL . '/assets/css/Lukio-fav.min.css', [], filemtime(LUKIO_FAVORITES_PLUGIN_DIR . '/assets/css/Lukio-fav.min.css'));

        if (get_current_screen()->base == 'toplevel_page_lukio_favorites') {
            // enqueue needed to the admin page
            wp_enqueue_media();
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_style('lukio_favorites_stylesheet', LUKIO_FAVORITES_PLUGIN_URL . '/assets/css/lukio-favorites.min.css', [], filemtime(LUKIO_FAVORITES_PLUGIN_DIR . '/assets/css/lukio-favorites.min.css'));
            wp_enqueue_style('lukio_favorites_admin_stylesheet', LUKIO_FAVORITES_PLUGIN_URL . '/assets/css/lukio-favorites-admin.min.css', [], filemtime(LUKIO_FAVORITES_PLUGIN_DIR . '/assets/css/lukio-favorites-admin.min.css'));
            wp_enqueue_script('lukio_favorites_admin_scripts', LUKIO_FAVORITES_PLUGIN_URL . '/assets/js/lukio-favorites-admin.min.js', ['jquery', 'wp-color-picker'], filemtime(LUKIO_FAVORITES_PLUGIN_DIR . '/assets/js/lukio-favorites-admin.min.js'), true);
            wp_localize_script(
                'lukio_favorites_admin_scripts',
                'lukio_favorites_data',
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
            isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'lukio_fav_save_options')
        ) {
            $this->save_options();
        }
        // include the page markup file
        include LUKIO_FAVORITES_PLUGIN_DIR . 'admin/admin-page.php';
    }

    /**
     * save the new posted options
     * 
     * @author Itai Dotan
     */
    private function save_options()
    {
        $lukio_favorites = lukio_favorites();
        $options_schematics = $lukio_favorites->get_default_options_schematics();
        $options = $lukio_favorites->get_default_options();
        foreach ($options as $key => &$option) {
            if ($options_schematics[$key]['type'] == 'bool') {
                // set all bools to false because form dont post unchecked checkboxes
                $option = false;
            } else if ($options_schematics[$key]['type'] == 'array') {
                // set array options to an empty array, form dont post unchecked checkboxes
                $option = array();
            }

            if (!isset($_POST[$key])) {
                continue;
            }

            switch ($options_schematics[$key]['type']) {
                case 'bool':
                    $option =  true;
                    break;
                case 'hex':
                    $option = sanitize_hex_color($_POST[$key]);
                    break;
                case 'text':
                    $option = sanitize_text_field($_POST[$key]);
                    break;
                case 'textarea':
                    $option = sanitize_textarea_field($_POST[$key]);
                    break;
                case 'array':
                    $option = array_map(
                        function ($post_name) {
                            return sanitize_text_field($post_name);
                        },
                        (array)$_POST[$key]
                    );
                    break;
                case 'int':
                    $option = (int)$_POST[$key];
                    break;
            }
        }

        update_option(Lukio_Favorites_Class::OPTIONS_META_KEY, $options);
        $lukio_favorites->update_options();
        $lukio_favorites->set_empty_text_button();
    }

    /**
     * get the image src for the button preview
     * 
     * @author Itai Dotan
     */
    public function get_preview_img()
    {
        $image_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($image_id) {
            $image_src = wp_get_attachment_image_src($image_id, 'medium');
            $data = array(
                'success' => true,
                'image_src' => $image_src ? $image_src[0] : '',
            );
        } else {
            $data = array(
                'success' => false,
            );
        }
        echo json_encode($data);
        die;
    }

    /**
     * add link to the plugin option page in wp plugin page when the plugin is active
     * 
     * @param array $actions an array of plugin action links
     * 
     * @return array modified actions link when the plug in is active, un-modified when not active
     * 
     * @author Itai Dotan
     */
    public function plugin_action_links($actions)
    {
        if (isset($actions['deactivate'])) {
            $setting = array(
                'settings' => '<a href ="' . esc_url(add_query_arg('page', 'lukio_favorites', get_admin_url() . 'admin.php')) . '">' . __('Settings', 'lukio-favorites-plugin') . '</a>',
            );
            $actions = array_merge($setting, $actions);
        }
        return $actions;
    }

    /**
     * Add a post display state for the plugin pages
     *
     * @param array $post_states An array of post display states.
     * @param WP_Post $post The current post object.
     */
    public function add_display_post_states($post_states, $post)
    {
        if (lukio_favorites()->get_active_options()['favorites_page_id'] === $post->ID) {
            $post_states['lukio_favorites'] = __('Favorites page', 'lukio-favorites-plugin');
        }

        return $post_states;
    }
}
new lukio_favorites_admin_class();
