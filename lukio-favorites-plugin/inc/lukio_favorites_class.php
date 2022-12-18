<?php
class Lukio_Favorites_Class
{
    private static $instance = null;

    private $table_name;
    private $wpdb;

    private $default_color = '#4aa896';
    private $default_options = array(
        'add_to_title' => true,
        'custom_button' => false,
        'button_color' => '#4aa896',
        'custom_button_on' => 0,
        'custom_button_off' => 0,
    );
    private $default_options_bools = array();
    private $add_to_title;
    private $custom_button;
    private $button_color;
    private $custom_button_on;
    private $custom_button_off;

    private $default_svg = '<svg class="lukio_favorites_button_default_svg" xmlns="http://www.w3.org/2000/svg" width="18.777" height="17" viewBox="0 0 18.777 17">
    <path class="lukio_pre_fav" id="pre-like" d="M536,431.778a4,4,0,0,1,4,4c0,2.209-2.033,3.8-3.555,5.333-1.223,1.23-3.555,3.111-3.555,3.111s-2.332-1.881-3.555-3.111c-1.523-1.532-3.556-3.124-3.556-5.333a4,4,0,0,1,7.111-2.512A3.99,3.99,0,0,1,536,431.778M536,430a5.749,5.749,0,0,0-3.111.908,5.78,5.78,0,0,0-8.889,4.87c0,2.647,1.9,4.477,3.419,5.948.224.216.444.427.654.639,1.277,1.285,3.6,3.163,3.7,3.242a1.778,1.778,0,0,0,2.232,0c.1-.08,2.423-1.957,3.7-3.242.21-.211.43-.423.654-.639,1.523-1.471,3.419-3.3,3.419-5.948A5.784,5.784,0,0,0,536,430Z" transform="translate(-523.5 -429.5)" fill="transparent" stroke="rgba(0,0,0,0)" stroke-miterlimit="10" stroke-width="1" />
    <path class="lukio_fav" xmlns="http://www.w3.org/2000/svg" id="like" d="M536,430a5.749,5.749,0,0,0-3.111.908,5.78,5.78,0,0,0-8.889,4.87c0,2.647,1.9,4.477,3.419,5.948.224.216.444.427.654.639,1.277,1.285,3.6,3.163,3.7,3.242a1.778,1.778,0,0,0,2.232,0c.1-.08,2.423-1.957,3.7-3.242.21-.211.43-.423.654-.639,1.523-1.471,3.419-3.3,3.419-5.948A5.784,5.784,0,0,0,536,430Z" transform="translate(-523.5 -429.5)" fill="transparent" stroke="rgba(0,0,0,0)" stroke-miterlimit="10" stroke-width="1" />
    </svg>';
    private $color_printed = false;


    public static function get_instance()
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $this->wpdb->prefix . 'lukio_favorites_plugin';

        foreach ($this->default_options as $key => $value) {
            if ($value === true || $value === false) {
                $this->default_options_bools[] = $key;
            }
        }
        $this->update_options();
    }

    public function update_options()
    {
        // get the site option with the default of default_options
        $options = array_merge($this->default_options, get_option('lukio_favorites_plugin_options', array()));

        $this->add_to_title = $options['add_to_title'];
        $this->custom_button = $options['custom_button'];
        $this->button_color = $options['button_color'];
        $this->custom_button_on = $options['custom_button_on'];
        $this->custom_button_off = $options['custom_button_off'];
    }

    private function admin_statue()
    {
        return (count(array_intersect(['administrator'], wp_get_current_user()->roles)) > 0);
    }

    public function create_table()
    {
        if (!is_user_logged_in() || !$this->admin_statue()) {
            return;
        }

        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
          id bigint(20) NOT NULL AUTO_INCREMENT,
          user_id bigint(20) UNSIGNED NOT NULL,
          post_id bigint(20) UNSIGNED NOT NULL,
          PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function drop_table()
    {
        if (!is_user_logged_in() || !$this->admin_statue()) {
            return;
        }

        $this->wpdb->query(
            $this->wpdb->prepare('DROP TABLE %1$s;', $this->table_name)
        );
    }

    public function get_user_favorites($per_page = null, $page = 0)
    {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();

            $limit = '';
            if (!is_null($per_page)) {
                $offset = ($page + 1) * $per_page;
                $limit = "LIMIT $per_page OFFSET $offset";
            }

            $results = $this->wpdb->get_results(
                $this->wpdb->prepare('SELECT post_id FROM %1$s WHERE user_id=%2$d %3$s', $this->table_name, $user_id, $limit)
            );

            $favorites_array = array();
            foreach ($results as $favorites_obj) {
                $fav_post_id = (int)$favorites_obj->post_id;
                if (get_post($fav_post_id)) {
                    $favorites_array[] = $fav_post_id;
                } else {
                    $this->favorites_button_clicked($fav_post_id);
                }
            }
        } else {
            if (is_null($per_page)) {
                $favorites_array = $_SESSION['lukio_fav_session'];
            } else {
                $offset = ($page + 1) * $per_page;
                $favorites_array = array_slice($_SESSION['lukio_fav_session'], $offset, $per_page);
            }
        }

        return $favorites_array;
    }

    public function favorites_button_clicked($post_id)
    {
        $new_status = false;
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $results = $this->wpdb->get_results(
                $this->wpdb->prepare('SELECT post_id FROM %1$s WHERE user_id=%2$d AND post_id=%3$d', $this->table_name, $user_id, $post_id)
            );
            if (empty($results)) {
                $this->wpdb->insert($this->table_name, array(
                    'user_id' => $user_id,
                    'post_id' => $post_id
                ));
                $new_status = true;
            } else {
                $this->wpdb->delete($this->table_name, array(
                    'user_id' => $user_id,
                    'post_id' => $post_id
                ));
            }
        } else {
            $pos = array_search($post_id, $_SESSION['lukio_fav_session']);
            if ($pos === false) {
                $_SESSION['lukio_fav_session'][] = $post_id;
                $new_status = true;
            } else {
                unset($_SESSION['lukio_fav_session'][$pos]);
            }
        }

        return $new_status;
    }

    public function get_favorites_status($post_id = null)
    {
        if (is_null($post_id)) {
            $post_id = get_post($post_id)->ID;
        }
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();

            $results = $this->wpdb->get_results(
                $this->wpdb->prepare('SELECT post_id FROM %1$s WHERE user_id=%2$d AND post_id=%3$d', $this->table_name, $user_id, $post_id)
            );
            return !empty($results);
        } else {
            return in_array($post_id, $_SESSION['lukio_fav_session']);
        }
    }

    public function get_button_content()
    {
        if ($this->custom_button) {
            echo wp_get_attachment_image($this->custom_button_off, 'thumbnail', false, array('class' => 'lukio_favorites_button_not_added'));
            echo wp_get_attachment_image($this->custom_button_on, 'thumbnail', false, array('class' => 'lukio_favorites_button_added'));
        } else {
            echo $this->default_svg;
            if (!$this->color_printed) {
                $this->color_printed = true;
?>
                <style>
                    .lukio_favorites_button[data-lukio-fav="0"] .lukio_pre_fav {
                        fill: <?php echo $this->button_color; ?>;
                    }

                    .lukio_favorites_button[data-lukio-fav="1"] .lukio_fav {
                        fill: <?php echo $this->button_color; ?>;
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

    public function get_default_options_bools()
    {
        return $this->default_options_bools;
    }
}
