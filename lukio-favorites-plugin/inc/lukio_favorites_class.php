<?php
class Lukio_Favorites_Class
{
    private static $instance = null;

    /**
     * default plugin svg color
     * 
     * @var string $default_color
     */
    private $default_color = '#4aa896';

    /**
     * default plugin options.
     * 
     * @var array $default_options default options the plugin start with 
     */
    private $default_options = array(
        'add_to_title' => true,
        'custom_button' => false,
        'button_color' => '#4aa896',
        'custom_button_off' => 0,
        'custom_button_on' => 0,
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
     * store the default svg of the plugin
     * 
     * @var string $default_svg html of the default svg
     */
    private $default_svg = '<svg class="lukio_favorites_button_default_svg" xmlns="http://www.w3.org/2000/svg" width="18.777" height="17" viewBox="0 0 18.777 17">
    <path class="lukio_pre_fav" id="pre-like" d="M536,431.778a4,4,0,0,1,4,4c0,2.209-2.033,3.8-3.555,5.333-1.223,1.23-3.555,3.111-3.555,3.111s-2.332-1.881-3.555-3.111c-1.523-1.532-3.556-3.124-3.556-5.333a4,4,0,0,1,7.111-2.512A3.99,3.99,0,0,1,536,431.778M536,430a5.749,5.749,0,0,0-3.111.908,5.78,5.78,0,0,0-8.889,4.87c0,2.647,1.9,4.477,3.419,5.948.224.216.444.427.654.639,1.277,1.285,3.6,3.163,3.7,3.242a1.778,1.778,0,0,0,2.232,0c.1-.08,2.423-1.957,3.7-3.242.21-.211.43-.423.654-.639,1.523-1.471,3.419-3.3,3.419-5.948A5.784,5.784,0,0,0,536,430Z" transform="translate(-523.5 -429.5)" fill="transparent" stroke="rgba(0,0,0,0)" stroke-miterlimit="10" stroke-width="1" />
    <path class="lukio_fav" xmlns="http://www.w3.org/2000/svg" id="like" d="M536,430a5.749,5.749,0,0,0-3.111.908,5.78,5.78,0,0,0-8.889,4.87c0,2.647,1.9,4.477,3.419,5.948.224.216.444.427.654.639,1.277,1.285,3.6,3.163,3.7,3.242a1.778,1.778,0,0,0,2.232,0c.1-.08,2.423-1.957,3.7-3.242.21-.211.43-.423.654-.639,1.523-1.471,3.419-3.3,3.419-5.948A5.784,5.784,0,0,0,536,430Z" transform="translate(-523.5 -429.5)" fill="transparent" stroke="rgba(0,0,0,0)" stroke-miterlimit="10" stroke-width="1" />
    </svg>';

    /**
     * track if the button css color has been printed
     * 
     * @var bool $color_printed true when the style tag been printed to the page
     */
    private $color_printed = false;

    /**
     * get an instance of the class, create new on first call
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

    private function __construct()
    {
        $this->update_options();
    }

    public function update_options()
    {
        $this->active_options = array_merge($this->default_options, get_option('lukio_favorites_plugin_options', array()));
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
    private function get_user_saved_favorites()
    {
        $user_id = get_current_user_id();
        if ($user_id) {
            $user_meta = get_user_meta($user_id, 'lukio_favorites_user_favorites', true);
            $favorites_array = is_array($user_meta) ? $user_meta : array();
        } else {
            $favorites_array = $_SESSION['lukio_fav_session'];
        }

        return $favorites_array;
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
        $user_id = get_current_user_id();
        if ($user_id) {
            update_user_meta($user_id, 'lukio_favorites_user_favorites', $new_favorites);
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

        $favorites_array = $this->get_user_saved_favorites();
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

    public function get_favorites_status($post_id = null)
    {
        if (is_null($post_id)) {
            $post_id = get_post($post_id)->ID;
        }

        foreach ($this->get_user_saved_favorites() as $post_type_array) {
            if (in_array($post_id, $post_type_array)) {
                return true;
            }
        }
        return false;
    }

    public function get_button_content()
    {
        if ($this->active_options['custom_button']) {
            echo wp_get_attachment_image($this->active_options['custom_button_off'], 'thumbnail', false, array('class' => 'lukio_favorites_button_image not_added'));
            echo wp_get_attachment_image($this->active_options['custom_button_on'], 'thumbnail', false, array('class' => 'lukio_favorites_button_image added'));
        } else {
            echo $this->default_svg;
            if (!$this->color_printed) {
                $this->color_printed = true;
?>
                <style>
                    .lukio_favorites_button[data-lukio-fav="0"] .lukio_pre_fav {
                        fill: <?php echo $this->active_options['button_color']; ?>;
                    }

                    .lukio_favorites_button[data-lukio-fav="1"] .lukio_fav {
                        fill: <?php echo $this->active_options['button_color']; ?>;
                    }
                </style>
<?php
            }
        }
    }

    public function get($property)
    {
        if (isset($this->$property)) {
            return $this->$property;
        }
    }

    public function get_default_options()
    {
        return $this->default_options;
    }

    public function get_active_options()
    {
        return $this->active_options;
    }
}
