<?php
class Lukio_Favorites_Class
{
    /**
     * instance of the plugin
     * 
     * @var Lukio_Favorites_Class|null class instance when running, null before class was first called
     */
    private static $instance = null;

    /**
     * default plugin svg color
     * 
     * @var string $default_color
     */
    private $default_color = '#4aa896';

    /**
     * default plugin options
     * 
     * @var array $default_options default options the plugin start with, indexed by option name
     */
    private $default_options = array(
        'add_to_title' => true,
        'post_types' => ['post'],
        'custom_button' => false,
        'svg_index' => 0,
        'button_color' => '#4aa896',
        'custom_button_off' => 0,
        'custom_button_on' => 0,
        'button_width' => 20,
        'button_height' => 20,
        'favorites_page_id' => 0,
    );

    /**
     * holds the plugin options
     * 
     * @var array $active_options array indexed by option name
     * 
     * @see $default_options
     */
    private $active_options;

    /**
     * holds the array of icons for the plugin
     * 
     * @var array $svg_array array(
     *                          array(
     *                               'name' => the display name of the icon,
     *                               'svg' => svg mark up,
     *                               )
     *                          )
     */
    private $svg_array;

    /**
     * ID of the current user
     * 
     * @var int $user_id user ID
     */
    private $user_id;

    /**
     * holds the saved favorites array of the current user
     * 
     * @var array $saved_favorites array(
     *                                  post_type => array((int)$id, (int)$id...)
     *                                  )
     */
    private $saved_favorites;

    /**
     * track if the button css color has been printed
     * 
     * @var bool $style_printed true when the style tag been printed to the page
     */
    private $style_printed = false;

    /**
     * get an instance of the class, create new on first call
     * 
     * @return Lukio_Favorites_Class class instance
     * 
     * @author Itai Dotan
     */
    public static function get_instance()
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * construct action to run when creating a new instance
     * 
     * @author Itai Dotan
     */
    private function __construct()
    {
        // start a session when needed
        if (!session_id()) {
            session_start();
        }

        // set lukio_fav_session in the session when not set yet
        if (!isset($_SESSION['lukio_fav_session'])) {
            $_SESSION['lukio_fav_session'] = array();
        }

        $this->update_options();

        // useing priority 20 to be triggerd after the setup.php init action
        add_action('init', array($this, 'init'), 20);

        add_action('wp_login', array($this, 'merge_session_in_to_user'), 10, 2);

        // add a post display state for plugin pages.
        add_filter('display_post_states', array($this, 'add_display_post_states'), 10, 2);
    }

    /**
     * init action to set up the class
     * 
     * @author Itai Dotan
     */
    public function init()
    {
        // set the user_id and saved_favorites at init to be able to use get_current_user_id(), before init get_current_user_id() return 0
        $this->user_id = get_current_user_id();
        $this->set_saved_favorites();

        // set svg_array in init to have the textdomain loaded
        $this->svg_array = include LUKIO_FAVORITES_PLUGIN_DIR . 'assets/icons-array.php';
    }

    /**
     * update active_options with the saved options form 'lukio_favorites_plugin_options'
     * 
     * @author Itai Dotan
     */
    public function update_options()
    {
        $this->active_options = array_merge($this->default_options, get_option('lukio_favorites_plugin_options', array()));
    }

    /**
     * set the saved favorites class variable
     * 
     * @author Itai Dotan
     */
    private function set_saved_favorites()
    {
        if ($this->user_id) {
            $user_meta = get_user_meta($this->user_id, 'lukio_favorites_user_favorites', true);
            $this->saved_favorites = is_array($user_meta) ? $user_meta : array();
        } else {
            $this->saved_favorites = $_SESSION['lukio_fav_session'];
        }
    }

    /**
     * update the saved user favorites with the new array
     * 
     * @param array $new_favorites updated array to save to the user
     * 
     * @author Itai Dotan
     */
    private function update_favorites($new_favorites)
    {
        $this->saved_favorites = $new_favorites;
        if ($this->user_id) {
            update_user_meta($this->user_id, 'lukio_favorites_user_favorites', $new_favorites);
        } else {
            $_SESSION['lukio_fav_session'] = $new_favorites;
        }
    }
    /**
     * update the post favorites count
     * 
     * @param int $post_id the post id to update
     * @param bool $add true to add 1 to the counter, false to subtract 1
     * 
     * @author Itai Dotan
     */
    private function update_post_favorites_count($post_id, $add)
    {
        $old_count = get_post_meta($post_id, 'lukio_favorites_count', true);
        $old_count = $old_count != '' ? (int)$old_count : 0;
        update_post_meta($post_id, 'lukio_favorites_count', ($add ? $old_count + 1 : $old_count - 1));
    }

    /**
     * create or republish a page of the plugin and update the page id in options
     * 
     * @param string $slug page slug
     * @param string $option_index index the page id is save at in active_options
     * @param string $page_title page title
     * @param string $page_content page shortcode content
     * 
     * @return int the page id created or republished
     * 
     * @author Itai Dotan
     */
    private function create_page($slug, $option_index, $page_title, $page_content)
    {
        global $wpdb;

        $option_value = $this->active_options[$option_index];

        if ($option_value > 0) {
            $page_object = get_post($option_value);

            if ($page_object && 'page' === $page_object->post_type && !in_array($page_object->post_status, array('pending', 'trash', 'future', 'auto-draft'), true)) {
                // Valid page is already in place.
                return $page_object->ID;
            }
        }

        // Search for an existing page with the specified page content.
        $shortcode        = str_replace(array('<!-- wp:shortcode -->', '<!-- /wp:shortcode -->'), '', $page_content);
        $valid_page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$shortcode}%"));

        if ($valid_page_found) {
            $this->active_options[$option_index] = $valid_page_found;
            update_option('lukio_favorites_plugin_options', $this->active_options);

            return $valid_page_found;
        }

        // Search for a matching valid trashed page.
        $trashed_page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%"));

        if ($trashed_page_found) {
            $page_data = array(
                'ID'          => $trashed_page_found,
                'post_status' => 'publish',
            );
            $page_id = wp_update_post($page_data);
        } else {
            $page_data = array(
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_author'    => 1,
                'post_name'      => $slug,
                'post_title'     => $page_title,
                'post_content'   => $page_content,
                'post_parent'    => 0,
                'comment_status' => 'closed',
            );
            $page_id = wp_insert_post($page_data);
        }

        $this->active_options[$option_index] = $page_id;
        update_option('lukio_favorites_plugin_options', $this->active_options);

        return $page_id;
    }

    /**
     * create the plugin pages
     * 
     * @author Itai Dotan
     */
    public function create_pages()
    {
        $pages = array(
            'favorites' => array(
                'option_index' => 'favorites_page_id',
                'title' => __('Favorites', 'lukio-favorites-plugin'),
                'content' => '<!-- wp:shortcode -->[lukio_favorites_page]<!-- /wp:shortcode -->',
            ),
        );

        foreach ($pages as $slug => $page) {
            $this->create_page($slug, $page['option_index'], $page['title'], $page['content']);
        }
    }

    /**
     * Add a post display state for the plugin pages
     *
     * @param array $post_states An array of post display states.
     * @param WP_Post $post The current post object.
     */
    public function add_display_post_states($post_states, $post)
    {
        if ($this->active_options['favorites_page_id'] === $post->ID) {
            $post_states['lukio_favorites'] = __('Favorites page', 'lukio-favorites-plugin');
        }

        return $post_states;
    }

    /**
     * handle the clicking on a favorites button
     * 
     * @param int $post_id the post id
     * @param string $post_type the post type
     * 
     * @return bool true when added to favorites, false otherwise
     * 
     * @author Itai Dotan
     */
    public function favorites_button_clicked($post_id, $post_type)
    {
        $new_status = false;

        $favorites_array = $this->saved_favorites;
        if (isset($favorites_array[$post_type])) {
            // when the post type have the sub array
            $pos = array_search($post_id, $favorites_array[$post_type]);
            if ($pos === false) {
                // add the new post
                $favorites_array[$post_type][] = $post_id;
                $new_status = true;
            } else {
                // remove the post
                unset($favorites_array[$post_type][$pos]);
            }
        } else {
            // add the new post type and post id
            $favorites_array[$post_type][] = $post_id;
            $new_status = true;
        }
        $this->update_favorites($favorites_array);
        $this->update_post_favorites_count($post_id, $new_status);

        return $new_status;
    }

    /**
     * get if the current user added a post to their favorites
     * 
     * @param int $post_id post id to check if in the user favorites, when null check the global post. default `null`
     * 
     * @author Itai Dotan
     */
    public function get_favorites_status($post_id = null)
    {
        if (is_null($post_id)) {
            $post_id = get_post($post_id)->ID;
        }

        foreach ($this->saved_favorites as $post_type_array) {
            if (in_array($post_id, $post_type_array)) {
                return true;
            }
        }
        return false;
    }

    /**
     * print the inner svg\images for the favorites button
     * 
     * @author Itai Dotan
     */
    public function get_button_content()
    {
        if ($this->active_options['custom_button']) {
            echo wp_get_attachment_image($this->active_options['custom_button_off'], 'thumbnail', false, array('class' => 'lukio_favorites_button_image not_added'));
            echo wp_get_attachment_image($this->active_options['custom_button_on'], 'thumbnail', false, array('class' => 'lukio_favorites_button_image added'));
        } else {
            echo $this->svg_array[$this->active_options['svg_index']]['svg'];
        }
    }

    /**
     * get class property if exists
     * 
     * @param string $property property name to get
     * 
     * @return mix|false the property value or false when no property with the given name
     * 
     * @author Itai Dotan
     */
    public function get($property)
    {
        if (isset($this->$property)) {
            return $this->$property;
        }
        return false;
    }

    /**
     * get the current user saved favorites
     * 
     * the function return array of array indexed by post types when not empty. each sub array holds post id's of the same post type
     * 
     * @return array user favorites
     * 
     * @author Itai Dotan
     */
    public function get_user_favorites()
    {
        return $this->saved_favorites;
    }

    /**
     * get default plugin options
     * 
     * @return array default plugin options
     * 
     * @author Itai Dotan
     */
    public function get_default_options()
    {
        return $this->default_options;
    }

    /**
     * get active plugin options
     * 
     * @return array active plugin options
     * 
     * @author Itai Dotan
     */
    public function get_active_options()
    {
        return $this->active_options;
    }

    /**
     * return css string with favorites button dynamic css
     * 
     * @return string css string
     * 
     * @author Itai Dotan
     */
    public function button_dynamic_css()
    {
        return '.lukio_favorites_button{width:' . $this->active_options['button_width'] . 'px;height:' . $this->active_options['button_height'] . 'px;}' .
            '.lukio_favorites_button[data-lukio-fav="0"] .lukio_pre_fav{fill:' . $this->active_options['button_color'] . ';}' .
            '.lukio_favorites_button[data-lukio-fav="1"] .lukio_fav{fill:' . $this->active_options['button_color'] . '; }';
    }

    /**
     * return if to add the button to the title
     * 
     * @return bool true when to add the button to the title, false otherwise
     * 
     * @author Itai Dotan
     */
    public function get_add_to_tilte_setting()
    {
        return $this->active_options['add_to_title'];
    }


    /**
     * on user login get the favorites from session and add them to the user saved favorites when not added before
     * 
     * @param string $user_login username
     * @param WP_User $user object of the logged-in user
     * 
     * @author Itai Dotan
     */
    public function merge_session_in_to_user($user_login, $user)
    {
        // update the class variables
        $this->user_id = $user->ID;
        $this->set_saved_favorites();

        // go over the session liked and add to the user when not liked yet
        foreach ($_SESSION['lukio_fav_session'] as $post_type => $post_type_array) {
            foreach ($post_type_array as $post_id) {
                if (!$this->get_favorites_status($post_id)) {
                    $this->favorites_button_clicked($post_id, $post_type);
                }
            }
        }

        // reset the session
        $_SESSION['lukio_fav_session'] = array();
    }
}

if (!function_exists('lukio_favorites')) {
    /**
     * get an instance of Lukio_Favorites_Class
     * 
     * @return Lukio_Favorites_Class class instance
     * 
     * @author Itai Dotan
     */
    function lukio_favorites()
    {
        return Lukio_Favorites_Class::get_instance();
    }
}

if (!function_exists('is_favorites')) {
    /**
     * check if the current page is the favorites page
     * 
     * @return bool true when he current page is the favorites page
     * 
     * @author Itai Dotan
     */
    function is_favorites()
    {
        $favorites_page_id = lukio_favorites()->get_active_options()['favorites_page_id'];
        return $favorites_page_id && is_page($favorites_page_id);
    }
}