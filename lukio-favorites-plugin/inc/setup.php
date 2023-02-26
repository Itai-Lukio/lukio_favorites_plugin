<?php

/**
 * setup class of the plugin
 */

defined('ABSPATH') || exit;

class Lukio_Favorites_Setup
{
    /**
     * add the needed actions and shortcode for the class
     * 
     * @author Itai Dotan
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue'));

        add_action('plugin_action_links_' . LUKIO_FAVORITES_PLUGIN_MAIN_FILE, array($this, 'plugin_action_links'));

        if (lukio_favorites()->get_add_to_tilte_setting()) {
            // add the filter only when the option to add to title is true
            add_filter('the_title', array($this, 'add_button_to_titles'), 10, 2);
        }

        add_action('wp_ajax_lukio_favorites_button_click', array($this, 'ajax_favorite_click'));
        add_action('wp_ajax_nopriv_lukio_favorites_button_click', array($this, 'ajax_favorite_click'));

        add_shortcode('lukio_favorites_button', array($this, 'button_markup'));
        add_shortcode('lukio_favorites_page', array($this, 'favorites_page_content'));
    }

    /**
     * init action to set up the plugin
     * 
     * @author Itai Dotan
     */
    public function init()
    {
        load_plugin_textdomain('lukio-favorites-plugin', false, 'lukio-favorites-plugin/languages');
    }

    /**
     * enqueue and localize the plugin styles and scripts
     * 
     * @author Itai Dotan
     */
    public function enqueue()
    {
        wp_enqueue_style('lukio_favorites_stylesheets', LUKIO_FAVORITES_PLUGIN_URL . '/assets/css/lukio-favorites.min.css', [], filemtime(LUKIO_FAVORITES_PLUGIN_DIR . '/assets/css/lukio-favorites.min.css'));
        wp_add_inline_style('lukio_favorites_stylesheets', lukio_favorites()->button_dynamic_css());

        wp_enqueue_script('lukio_favorites_script', LUKIO_FAVORITES_PLUGIN_URL . '/assets/js/lukio-favorites.min.js', ['jquery'], filemtime(LUKIO_FAVORITES_PLUGIN_DIR . '/assets/js/lukio-favorites.min.js'), true);
        wp_localize_script(
            'lukio_favorites_script',
            'lukio_favorites_ajax',
            array('ajax_url' => admin_url('admin-ajax.php'))
        );

        // enqueu favorites page style and script
        if (is_favorites()) {
            var_dump('chj');
        }
    }

    /**
     * ajax function to run when a favorite button has been clicked
     * 
     * @author Itai Dotan
     */
    public function ajax_favorite_click()
    {
        // make sure all the required variables are set
        if (!isset($_POST['post_id']) || !isset($_POST['post_type']) || !isset($_POST['nonce'])) {
            die;
        }

        $post_id = (int)$_POST['post_id'];
        $post_type = sanitize_text_field($_POST['post_type']);
        $nonce = sanitize_text_field($_POST['nonce']);
        if (wp_verify_nonce($nonce, $post_type . '_' . $post_id) === false) {
            die;
        }

        $new_status = lukio_favorites()->favorites_button_clicked($post_id, $post_type) ? 1 : 0;
        echo json_encode(array(
            'favorite' => $new_status,
            'aria_label' => $this->button_aria_label($new_status, $post_id),
        ));
        die;
    }

    /**
     * function of the shortcode 'lukio_favorites_button', output the button markup
     * 
     * @param array $atts user defined attributes in shortcode tag, default `[]`
     * 
     * @author Itai Dotan
     */
    public function button_markup($atts = [])
    {
        $atts = shortcode_atts(
            array(
                'post_id' => null,
                'class' => null,
            ),
            $atts
        );

        $post = get_post($atts['post_id']);
        $post_id = $post->ID;
        $post_type = $post->post_type;
        $post_id = is_null($atts['post_id']) ? get_post()->ID : (int)$atts['post_id'];
        $atts['class'] = is_null($atts['class']) ? '' : ' ' . trim($atts['class']);

        $lukio_favorites = lukio_favorites();
        $favorites_status = $lukio_favorites->get_favorites_status($post_id);
        ob_start();
?>
        <button class="lukio_favorites_button<?php echo $lukio_favorites->is_text_button() ? ' text_button' : ' image_button';
                                                echo esc_attr($atts['class']); ?>" type="button" data-lukio-fav="<?php echo $favorites_status ? 1 : 0; ?>" data-post-id="<?php echo esc_attr($post_id); ?>" data-post-type="<?php echo esc_attr($post_type); ?>" data-nonce="<?php echo wp_create_nonce($post_type . '_' . $post_id); ?>" aria-label="<?php echo esc_attr($this->button_aria_label($favorites_status, $post_id)); ?>">
            <?php
            do_action('lukio_favorites_before_button_content');
            $lukio_favorites->get_button_content();
            do_action('lukio_favorites_after_button_content');
            ?>
        </button>

<?php
        return ob_get_clean();
    }

    /**
     * aria-label attribute for the favorites button
     * 
     * the first param $favorites_status is for the current/new status when returning new label from ajax click
     * 
     * @param bool $favorites_status true when in favorites
     * @param int $post_id id of the post the button is for
     * 
     * @return string aria-label for the favorites button
     * 
     * @author Itai Dotan
     */
    private function button_aria_label($favorites_status, $post_id)
    {
        $post = get_post($post_id);
        $post_title = isset($post->post_title) ? $post->post_title : '';
        $label = $favorites_status ? __('Remove "%s" from favorites', 'lukio-favorites-plugin') : __('Add "%s" to favorites', 'lukio-favorites-plugin');
        return sprintf($label, $post_title);
    }

    /**
     * add link to the plug in option page in wp plugin page when the plugin is active
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
            $settings = array(
                'settings' => '<a href ="' . esc_url(add_query_arg('page', 'lukio_favorites', get_admin_url() . 'admin.php')) . '">' . __('Settings', 'lukio-favorites-plugin') . '</a>',
            );
            $actions = array_merge($settings, $actions);
        }
        return $actions;
    }

    /**
     * hook in to 'the_title' filter and append the favorite button to it when set to and the post type is selected
     * 
     * @param string $post_title the title coming from the hook
     * @param int $post_id the post id the title is for
     * 
     * @return string title withe favorites button appended to it
     * 
     * @author Itai Dotan
     */
    public function add_button_to_titles($post_title, $post_id)
    {
        // add the button only on the front page
        if (is_admin() && !wp_doing_ajax()) {
            return $post_title;
        }

        $options = lukio_favorites()->get_active_options();

        $post_type = get_post_type($post_id);
        if (!$post_type || !in_array($post_type, $options['post_types'])) {
            return $post_title;
        }
        return $post_title . $this->button_markup(array('post_id' => $post_id));
    }

    /**
     * get the template path.
     * 
     * get template from the active theme when the template was overridden, plugin template file when not.
     * 
     * @param string $template_name name of the file to get the path for
     * @return string full path to the template file
     * 
     * @author Itai Dotan
     */
    private function get_template_path($template_name)
    {
        $theme_dir = get_stylesheet_directory();
        if (file_exists("$theme_dir/lukio-favorites/$template_name.php")) {
            return "$theme_dir/lukio-favorites/$template_name.php";
        }
        return LUKIO_FAVORITES_PLUGIN_DIR . "templates/$template_name.php";
    }

    /**
     * get the content of favorites-content.php for the shortcode
     * 
     * @return string favorites page markup
     * 
     * @author Itai Dotan
     */
    public function favorites_page_content()
    {
        // set for easy access in the template
        $user_favorites = lukio_favorites()->get_user_favorites();

        ob_start();
        include $this->get_template_path('favorites-content');
        return ob_get_clean();
    }
}

new Lukio_Favorites_Setup();
