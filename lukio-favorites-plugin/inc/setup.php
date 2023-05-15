<?php


defined('ABSPATH') || exit;

/**
 * Setup class of the plugin
 */
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

        add_action('wp_ajax_lukio_favorites_button_click', array($this, 'ajax_favorite_click'));
        add_action('wp_ajax_nopriv_lukio_favorites_button_click', array($this, 'ajax_favorite_click'));

        add_shortcode('lukio_favorites_button', array($this, 'button_markup'));
        add_shortcode('lukio_favorites_page', array($this, 'favorites_page_content'));
        add_shortcode('lukio_favorites_menu_button', array($this, 'menu_button_markup'));
    }

    /**
     * init action to set up the plugin
     * 
     * @author Itai Dotan
     */
    public function init()
    {
        load_plugin_textdomain('lukio-favorites-plugin', false, 'lukio-favorites-plugin/languages');

        $lukio_favorites = lukio_favorites();
        if ($lukio_favorites->get_add_to_tilte_setting()) {
            // add the filter only when the option to add to title is true
            add_filter('the_title', array($this, 'add_button_to_titles'), 10, 2);
        }

        $add_to_menu = $lukio_favorites->get_menu_add_slug();
        if ($add_to_menu !== false) {
            // add the filter only when the option to add to menu is true 
            add_filter("wp_nav_menu_{$add_to_menu}_items", array($this, 'add_button_to_menu'));
        }
    }

    /**
     * enqueue and localize the plugin styles and scripts
     * 
     * @author Itai Dotan
     */
    public function enqueue()
    {
        $lukio_favorites =  lukio_favorites();
        wp_enqueue_style('lukio_favorites_stylesheets', LUKIO_FAVORITES_PLUGIN_URL . 'assets/css/lukio-favorites.min.css', [], filemtime(LUKIO_FAVORITES_PLUGIN_DIR . 'assets/css/lukio-favorites.min.css'));
        wp_add_inline_style('lukio_favorites_stylesheets', $lukio_favorites->dynamic_css());

        wp_enqueue_script('lukio_favorites_script', LUKIO_FAVORITES_PLUGIN_URL . 'assets/js/lukio-favorites.min.js', ['jquery'], filemtime(LUKIO_FAVORITES_PLUGIN_DIR . 'assets/js/lukio-favorites.min.js'), true);
        wp_localize_script(
            'lukio_favorites_script',
            'lukio_favorites_data',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'fragment_indicator' => Lukio_Favorites_Class::FRAGMENT_INDICATOR,
            )
        );
    }

    /**
     * get the plugin refresh fragments
     * 
     * @return array refresh fragments
     * 
     * @author Itai Dotan
     */
    public function get_fragments()
    {
        return apply_filters('lukio_favorites_fragments', array(
            'div.lukio_favorites_page_wrapper' => $this->favorites_page_content(),
        ));
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

        $lukio_favorites = lukio_favorites();
        $added = $lukio_favorites->favorites_button_clicked($post_id, $post_type);
        $posts = [];

        foreach ($lukio_favorites->get_user_favorites() as $type => $posts_ids) {
            $posts = array_merge($posts, $posts_ids);
        }
        echo json_encode(
            array(
                'favorite' => $added ? 1 : 0,
                'title' => $this->button_title($added, $post_id),
                'empty' => $lukio_favorites->is_favorites_empty(),
                'fragments' => $this->get_fragments(),
                'posts' => $posts,
            )
        );
        die;
    }

    /**
     * function of the shortcode 'lukio_favorites_button', output the button markup
     * 
     * @param array $atts user defined attributes in shortcode tag, default `[]`
     * 
     * @return string button markup
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
        $atts['class'] = apply_filters('lukio_favorites_button_markup_class', is_null($atts['class']) ? '' : ' ' . trim($atts['class']));

        $lukio_favorites = lukio_favorites();
        $favorites_status = $lukio_favorites->get_favorites_status($post_id);
        ob_start();
?>
        <button class="lukio_favorites_button<?php echo $lukio_favorites->is_text_button() ? ' text_button' : ' image_button';
                                                echo esc_attr($atts['class']); ?>" type="button" data-lukio-fav="<?php echo $favorites_status ? 1 : 0; ?>" data-post-id="<?php echo esc_attr($post_id); ?>" data-post-type="<?php echo esc_attr($post_type); ?>" data-nonce="<?php echo wp_create_nonce($post_type . '_' . $post_id); ?>" title="<?php echo esc_attr($this->button_title($favorites_status, $post_id)); ?>">
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
    private function button_title($favorites_status, $post_id)
    {
        $post = get_post($post_id);
        $post_title = isset($post->post_title) ? $post->post_title : '';
        $label = $favorites_status ? __('Remove "%s" from favorites', 'lukio-favorites-plugin') : __('Add "%s" to favorites', 'lukio-favorites-plugin');
        return apply_filters('lukio_favorites_button_title', sprintf($label, $post_title), $post_title, $post_id, $favorites_status);
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
        // allow 3rd partys to check for their custom admin ajax, or to not add the button in any admin page and admin quick edit ajax
        if (
            apply_filters('lukio_favorites_skip_button_title', false) ||
            is_admin() && (!wp_doing_ajax() || isset($_REQUEST['screen']) && isset($_REQUEST['action']) && $_REQUEST['action'] === 'inline-save')
        ) {
            return $post_title;
        }

        $options = lukio_favorites()->get_active_options();

        // not adding the button when not in the selected post types
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
        // filter to add extra class to the wrapper
        $class = apply_filters('lukio_favorites_page_wrapper_class', '');
        $class = $class == '' ? '' : ' ' . trim($class);

        $lukio_favorites = lukio_favorites();

        global $empty_favorites, $user_favorites;
        $empty_favorites = $lukio_favorites->is_favorites_empty();
        $user_favorites = $empty_favorites ? array() : $lukio_favorites->get_user_favorites();

        do_action('lukio_favorites_before_fragment');

        ob_start();
        include $this->get_template_path('favorites-content');
        $content = ob_get_clean();

        do_action('lukio_favorites_after_fragment');

        return $content;
    }

    /**
     *  add li with the menu button to the menu
     * 
     * @param string $items The HTML list content for the menu items
     * @return string the HTML list content with the added menu button
     * 
     * @author Itai Dotan
     */
    public function add_button_to_menu($items)
    {
        $menu_button = '<li class="lukio_favorites_menu_li">' . $this->menu_button_markup() . '</li>';
        if (lukio_favorites()->get_add_to_menu_start()) {
            $items = $menu_button . $items;
        } else {
            $items .= $menu_button;
        }
        return $items;
    }

    /**
     * function of the shortcode 'lukio_favorites_menu_button', output the button markup
     * 
     * @param array $atts user defined attributes in shortcode tag, default `[]`
     */
    public function menu_button_markup($atts = [])
    {
        $atts = shortcode_atts(
            array(
                'class' => null,
            ),
            $atts
        );

        $lukio_favorites = lukio_favorites();
        $empty = $lukio_favorites->is_favorites_empty() ? ' empty' : '';
        $type_indicator = $lukio_favorites->is_menu_button_text() ? ' text_button' : ' image_button';
        $atts['class'] = apply_filters('lukio_favorites_menu_button_class', is_null($atts['class']) ? '' : ' ' . trim($atts['class']));

        ob_start();
    ?>
        <div class="lukio_favorites_menu_button<?php echo esc_attr($atts['class']) . $type_indicator . $empty; ?>">
            <?php
            do_action('lukio_favorites_before_menu_button_content');
            $lukio_favorites->get_menu_button_content();
            do_action('lukio_favorites_after_menu_button_content');
            ?>
        </div>
<?php
        return ob_get_clean();
    }
}

new Lukio_Favorites_Setup();
