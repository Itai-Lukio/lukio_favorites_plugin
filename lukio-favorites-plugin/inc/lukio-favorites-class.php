<?php

defined('ABSPATH') || exit;

/**
 * Handle the main plugin actions
 */
class Lukio_Favorites_Class
{
    /**
     * plugin option meta key
     * @var string meta key
     */
    const OPTIONS_META_KEY = 'lukio_favorites_plugin_options';

    /**
     * plugin user favorites meta key
     * @var string meta key
     */
    const USER_FAVORITES = 'lukio_favorites_user_favorites';

    /**
     * plugin post's favorites count meta key
     * @var string meta key
     */
    const POST_FAVORITES_COUNT = 'lukio_favorites_count';

    /**
     * indicator class for button in a fragment
     * @var string indicator class
     */
    const FRAGMENT_INDICATOR = 'in_fragment';

    /**
     * cookie index of the favorites
     * @var array user favorites
     */
    const FAVORITES_COOKIE = 'lukio_favorites_cookie';

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
     * @var array $default_options_schematics schematics for the default options the plugin start with, indexed by option name
     */
    private $default_options_schematics;

    /**
     * default plugin options
     * 
     * @var array $default_options default options the plugin start with, indexed by option name
     */
    private $default_options = array();

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
     * holds the empty status of the user favorites
     * 
     * @var null|bool null at start, bool of the empty status affter populated 
     */
    private $favorites_empty = null;

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
            self::$instance = new self();
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
        // setup FAVORITES_COOKIE
        if (!isset($_COOKIE[Lukio_Favorites_Class::FAVORITES_COOKIE])) {
            // when not set start with empty array
            $_COOKIE[Lukio_Favorites_Class::FAVORITES_COOKIE] = array();
        } else {
            // when set decode the json data
            $_COOKIE[Lukio_Favorites_Class::FAVORITES_COOKIE] = json_decode(stripslashes($_COOKIE[Lukio_Favorites_Class::FAVORITES_COOKIE]), true);
        }

        $this->set_default_options_schematics();

        $this->set_default_options();

        $this->update_options();

        $this->user_id = get_current_user_id();
        $this->set_saved_favorites();

        $this->svg_array = include LUKIO_FAVORITES_PLUGIN_DIR . 'assets/icons-array.php';

        add_action('wp_login', array($this, 'merge_cookie_in_to_user'), 10, 2);

        add_action('lukio_favorites_before_fragment', array($this, 'before_fragment'));
        add_action('lukio_favorites_after_fragment', array($this, 'after_fragment'));
    }

    /**
     * set default options schematics
     * 
     * @author Itai Dotan
     */
    private function set_default_options_schematics()
    {
        $this->default_options_schematics = array(
            // general tab options
            'favorites_page_id' => array(
                'type' => 'int',
                'default' => 0
            ),
            'add_to_title' => array(
                'type' => 'bool',
                'default' => true
            ),
            'post_types' => array(
                'type' => 'array',
                'default' => ['post']
            ),
            // button tab options
            'text_button' => array(
                'type' => 'bool',
                'default' => false
            ),
            'add_text' => array(
                'type' => 'text',
                'default' => __('Add to favorites', 'lukio-favorites-plugin')
            ),
            'remove_text' => array(
                'type' => 'text',
                'default' => __('Remove from favorites', 'lukio-favorites-plugin')
            ),
            'custom_button' => array(
                'type' => 'bool',
                'default' => false
            ),
            'svg_index' => array(
                'type' => 'int',
                'default' => 0
            ),
            'button_color' => array(
                'type' => 'hex',
                'default' => '#4aa896'
            ),
            'custom_button_off' => array(
                'type' => 'int',
                'default' => 0
            ),
            'custom_button_on' => array(
                'type' => 'int',
                'default' => 0
            ),
            'button_width' => array(
                'type' => 'int',
                'default' => 20
            ),
            'button_height' => array(
                'type' => 'int',
                'default' => 20
            ),
            // menu tab options
            'add_to_menu' => array(
                'type' => 'bool',
                'default' => false
            ),
            'menu_add_slug' => array(
                'type' => 'text',
                'default' => ''
            ),
            'add_to_menu_start' => array(
                'type' => 'bool',
                'default' => false
            ),
            'menu_button_use_text' => array(
                'type' => 'bool',
                'default' => false
            ),
            'menu_button_text' => array(
                'type' => 'text',
                'default' => __('Favorites', 'lukio-favorites-plugin')
            ),
            'custom_menu_button' => array(
                'type' => 'bool',
                'default' => false
            ),
            'menu_svg_index' => array(
                'type' => 'int',
                'default' => 0
            ),
            'menu_button_color' => array(
                'type' => 'hex',
                'default' => '#4aa896'
            ),
            'custom_menu_button_off' => array(
                'type' => 'int',
                'default' => 0
            ),
            'custom_menu_button_on' => array(
                'type' => 'int',
                'default' => 0
            ),
            'menu_button_width' => array(
                'type' => 'int',
                'default' => 20
            ),
            'menu_button_height' => array(
                'type' => 'int',
                'default' => 20
            ),
            // extar css tab
            'extra_css' => array(
                'type' => 'textarea',
                'default' => ''
            ),
        );
    }

    /**
     * populate the default_options from the schematics
     * 
     * @author Itai Dotan
     */
    private function set_default_options()
    {
        foreach ($this->default_options_schematics as $option_index => $option_data) {
            $this->default_options[$option_index] = $option_data['default'];
        }
    }

    /**
     * update active_options with the saved options form OPTIONS_META_KEY
     * 
     * @author Itai Dotan
     */
    public function update_options()
    {
        $this->active_options = array_merge($this->default_options, get_option(Lukio_Favorites_Class::OPTIONS_META_KEY, array()));
    }

    /**
     * set the saved favorites class variable
     * 
     * @author Itai Dotan
     */
    private function set_saved_favorites()
    {
        if ($this->user_id) {
            $user_meta = get_user_meta($this->user_id, Lukio_Favorites_Class::USER_FAVORITES, true);
            $favorites = is_array($user_meta) ? $user_meta : array();
        } else {
            $favorites = $_COOKIE[Lukio_Favorites_Class::FAVORITES_COOKIE];
        }

        $this->update_favorites($favorites);
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
            update_user_meta($this->user_id, Lukio_Favorites_Class::USER_FAVORITES, $new_favorites);
        } else {
            $_COOKIE[Lukio_Favorites_Class::FAVORITES_COOKIE] = $new_favorites;
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
        // count only for logged in users
        if ($this->user_id == 0) {
            return;
        }

        $old_count = get_post_meta($post_id, Lukio_Favorites_Class::POST_FAVORITES_COUNT, true);
        $old_count = $old_count != '' ? (int)$old_count : 0;
        update_post_meta($post_id, Lukio_Favorites_Class::POST_FAVORITES_COUNT, ($add ? $old_count + 1 : $old_count - 1));
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
        $shortcode = str_replace(array('<!-- wp:shortcode -->', '<!-- /wp:shortcode -->'), '', $page_content);
        $valid_page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$shortcode}%"));

        if ($valid_page_found) {
            $this->active_options[$option_index] = (int)$valid_page_found;
            update_option(Lukio_Favorites_Class::OPTIONS_META_KEY, $this->active_options);

            return $valid_page_found;
        }

        // Search for a matching valid trashed page.
        $trashed_page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$shortcode}%"));

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
        update_option(Lukio_Favorites_Class::OPTIONS_META_KEY, $this->active_options);

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
     * handle the clicking on a favorites button
     * 
     * @param int $post_id the post id
     * @param string $post_type the post type
     * @param bool $skip_empty_update true to skip the empty status update, used to loop 'click' favorites and not update every iteration. default `false`
     * 
     * @return bool true when added to favorites, false otherwise
     * 
     * @author Itai Dotan
     */
    public function favorites_button_clicked($post_id, $post_type, $skip_empty_update = false)
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

                // remove the post type sub array when empty
                if (count($favorites_array[$post_type]) == 0) {
                    unset($favorites_array[$post_type]);
                }
            }
        } else {
            // add the new post type and post id
            $favorites_array[$post_type][] = $post_id;
            $new_status = true;
        }
        $this->update_favorites($favorites_array);
        $this->update_post_favorites_count($post_id, $new_status);

        if (!$skip_empty_update) {
            $this->update_favorites_empty_status();
        }

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
     * print the inner content for the favorites button
     * 
     * @author Itai Dotan
     */
    public function get_button_content()
    {
        if ($this->active_options['text_button']) {
?>
            <span class="lukio_favorites_button_text not_added"><?php echo apply_filters('lukio_favorites_button_add_text', $this->active_options['add_text']); ?></span>
            <span class="lukio_favorites_button_text added"><?php echo apply_filters('lukio_favorites_button_remove_text', $this->active_options['remove_text']); ?></span>
        <?php
            return;
        }

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
     * @return mix|null the property value or null when no property with the given name
     * 
     * @author Itai Dotan
     */
    public function get($property)
    {
        if (isset($this->$property)) {
            return $this->$property;
        }
        return null;
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
    public function get_default_options_schematics()
    {
        return $this->default_options_schematics;
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
     * return css string with favorites dynamic css and the user extra css
     * 
     * @return string css string
     * 
     * @author Itai Dotan
     */
    public function dynamic_css()
    {
        $opener = "/*\nCreated by Lukio Favorites Plugin\nDynamic css created base on the option set in the plugin option page\n*/\n";
        return $opener . '.lukio_favorites_button.image_button{width:' . $this->active_options['button_width'] . 'px;height:' . $this->active_options['button_height'] . 'px;}' .
            '.lukio_favorites_button[data-lukio-fav="0"] .lukio_favorites_unmarked{fill:' . $this->active_options['button_color'] . ';}' .
            '.lukio_favorites_button[data-lukio-fav="1"] .lukio_favorites_marked{fill:' . $this->active_options['button_color'] . ';}' .
            '.lukio_favorites_menu_button.image_button{width:' . $this->active_options['menu_button_width'] . 'px;height:' . $this->active_options['menu_button_height'] . 'px;}' .
            '.lukio_favorites_menu_button.empty .lukio_favorites_unmarked{fill:' . $this->active_options['menu_button_color'] . ';}' .
            '.lukio_favorites_menu_button:not(.empty) .lukio_favorites_marked{fill:' . $this->active_options['menu_button_color'] . ';}' . "\n" .
            //trim and remove multi whitespaces
            trim(preg_replace('/(?:\s\s+)/', '', $this->active_options['extra_css']));
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
     * return if the button is in text mode
     * 
     * @return bool true when the button is in text mode
     * 
     * @author Itai Dotan
     */
    public function is_text_button()
    {
        return $this->active_options['text_button'];
    }

    /**
     * return menu slug to add the button to
     * 
     * @return string|false menu slug when the option is active, false when the option is disabled
     * 
     * @author Itai Dotan
     */
    public function get_menu_add_slug()
    {
        return $this->active_options['add_to_menu'] ? $this->active_options['menu_add_slug'] : false;
    }

    /**
     * return if to add the menu button to the start
     * 
     * @return bool true when the button should be added at the start of the menu
     * 
     * @author Itai Dotan
     */
    public function get_add_to_menu_start()
    {
        return $this->active_options['add_to_menu_start'];
    }

    /**
     * clear non-existing posts from the favorites
     * 
     * @author Itai Dotan
     */
    public function clear_deleted_posts()
    {
        foreach ($this->saved_favorites as $type => $posts) {
            foreach ($posts as $post_index => $post_id) {
                $post = get_post($post_id);

                if (is_null($post)) {
                    // remove non-existing posts
                    unset($this->saved_favorites[$type][$post_index]);
                }
            }

            // remove the type index when empty
            if (empty($this->saved_favorites[$type])) {
                unset($this->saved_favorites[$type]);
            }
        }

        $this->update_favorites($this->saved_favorites);
    }

    /**
     * on user login get the favorites from cookie and add them to the user saved favorites when not added before
     * 
     * @param string $user_login username
     * @param WP_User $user object of the logged-in user
     * 
     * @author Itai Dotan
     */
    public function merge_cookie_in_to_user($user_login, $user)
    {
        // update the class variables
        $this->user_id = $user->ID;
        $this->set_saved_favorites();

        // go over the cookie favorites and add to the user when not added yet
        foreach ($_COOKIE[Lukio_Favorites_Class::FAVORITES_COOKIE] as $post_type => $post_type_array) {
            foreach ($post_type_array as $post_id) {
                if (!$this->get_favorites_status($post_id)) {
                    $this->favorites_button_clicked($post_id, $post_type, true);
                }
            }
        }

        $this->clear_deleted_posts();
        $this->update_favorites_empty_status();

        // reset the cookie
        $_COOKIE[Lukio_Favorites_Class::FAVORITES_COOKIE] = array();
        setcookie(Lukio_Favorites_Class::FAVORITES_COOKIE, json_encode(array()), 0, '/');
    }

    /**
     * update the empty status by checking there is at least one published post saved
     * 
     * @author Itai Dotan
     */
    private function update_favorites_empty_status()
    {
        $empty = true;
        foreach ($this->saved_favorites as $type => $posts) {
            foreach ($posts as $post_id) {
                $post = get_post($post_id);

                if (is_null($post)) {
                    continue;
                }

                if ($post->post_status === 'publish') {
                    //once a valid post found the favorites is not empty, braek out of the 2 foreach
                    $empty = false;
                    break 2;
                }
            }
        }
        $this->favorites_empty = $empty;
    }

    /**
     * return the empty state of the user favorites
     * 
     * @return bool true when the favorites is empty
     * 
     * @author Itai Dotan
     */
    public function is_favorites_empty()
    {
        if (is_null($this->favorites_empty)) {
            $this->update_favorites_empty_status();
        }

        return $this->favorites_empty;
    }

    /**
     * return the url of the favorites page
     * 
     * @return string favorites page url
     * 
     * @author Itai Dotan
     */
    public function get_favorites_page_url()
    {
        return get_page_link($this->active_options['favorites_page_id']);
    }

    /**
     * return true when menu button have text content
     * 
     * @return bool true when text button, false otherwise
     */
    public function is_menu_button_text()
    {
        return $this->active_options['menu_button_use_text'];
    }

    /**
     * print the content of the menu button
     * 
     * @author Itai Dotan
     */
    public function get_menu_button_content()
    {
        $is_text_button = $this->is_menu_button_text();
        $button_title = apply_filters('lukio_favorites_menu_button_text', $this->active_options['menu_button_text']);
        $title_attr = $is_text_button ? '' : ' title="' . esc_attr($button_title) . '"';

        if ($is_text_button) {
            $link_content = $button_title;
        } else {
            if ($this->active_options['custom_menu_button']) {
                $link_content = wp_get_attachment_image($this->active_options['custom_menu_button_off'], 'thumbnail', false, array('class' => 'lukio_favorites_menu_button_image empty')) .
                    wp_get_attachment_image($this->active_options['custom_menu_button_on'], 'thumbnail', false, array('class' => 'lukio_favorites_menu_button_image'));
            } else {
                $link_content = $this->svg_array[$this->active_options['menu_svg_index']]['svg'];
            }
        }
        ?>
        <a class="lukio_favorites_menu_button_link" href="<?php echo esc_attr($this->get_favorites_page_url()) ?>" <?php echo $title_attr; ?>><?php echo $link_content; ?></a>
<?php
    }

    /**
     * add indicator class to indicate the button is in an updating fragment
     * 
     * @param string $class class string to append indicator to
     * 
     * @return string class string with indicator
     * 
     * @author Itai Dotan
     */
    public function add_fragment_indicator($class)
    {
        return $class . ' ' . Lukio_Favorites_Class::FRAGMENT_INDICATOR;
    }

    /**
     * dynamically add/remove fragments filters
     * 
     * @param bool $add true to add filters, false to remove filters
     * 
     * @author Itai Dotan
     */
    private function handle_fragment_filters($add)
    {
        $action = $add ? 'add_filter' : 'remove_filter';
        $action('lukio_favorites_button_markup_class', [$this, 'add_fragment_indicator'], 10);
    }

    /**
     * add filters needed before a fragment
     * 
     * @author Itai Dotan
     */
    public function before_fragment()
    {
        $this->handle_fragment_filters(true);
    }

    /**
     * remove filters added before a fragment
     * 
     * @author Itai Dotan
     */
    public function after_fragment()
    {
        $this->handle_fragment_filters(false);
    }

    /**
     * get favorites cookie data
     * 
     * @return false|string false when user is logged in, json string otherwise
     * 
     * @author Itai Dotan
     */
    public function get_cookie_data()
    {
        if ($this->user_id) {
            return false;
        } else {
            return json_encode($this->saved_favorites);
        }
    }
}

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


/**
 * check if the current page is the favorites page
 * 
 * @return bool true when he current page is the favorites page
 * 
 * @author Itai Dotan
 */
function is_lukio_favorites()
{
    $favorites_page_id = lukio_favorites()->get_active_options()['favorites_page_id'];
    return $favorites_page_id && is_page($favorites_page_id);
}
