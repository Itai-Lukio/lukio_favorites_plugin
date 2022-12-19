<?php
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
    }

    /**
     * init action to set up the plugin
     * 
     * @author Itai Dotan
     */
    public function init()
    {
        if (!session_id()) {
            session_start();
            if (!isset($_SESSION['lukio_fav_session'])) {
                $_SESSION['lukio_fav_session'] = array();
            }
        }
        load_plugin_textdomain('lukio-favorites-plugin', false, 'lukio-favorites-plugin/languages');
    }

    /**
     * enqueue and localize the plugin style and script
     * 
     * @author Itai Dotan
     */
    public function enqueue()
    {
        wp_enqueue_style('lukio_favorites_stylesheets', LUKIO_FAVORITES_PLUGIN_URL . '/assets/css/lukio_favorites.min.css', [], filemtime(LUKIO_FAVORITES_PLUGIN_DIR . '/assets/css/lukio_favorites.min.css'));
        wp_enqueue_script('lukio_favorites_script', LUKIO_FAVORITES_PLUGIN_URL . '/assets/js/lukio_favorites.min.js', ['jquery'], filemtime(LUKIO_FAVORITES_PLUGIN_DIR . '/assets/js/lukio_favorites.min.js'));
        wp_localize_script(
            'lukio_favorites_script',
            'lukio_favorites_ajax',
            array('ajax_url' => admin_url('admin-ajax.php'))
        );
    }

    /**
     * ajax function to run when a favorite button has been clicked
     * 
     * @author Itai Dotan
     */
    public function ajax_favorite_click()
    {
        $new_status = lukio_favorites()->favorites_button_clicked((int)$_POST['post_id'], $_POST['post_type']) ? 1 : 0;
        echo json_encode(array(
            'favorite' => $new_status,
        ));
        die;
    }

    /**
     * function of the shortcode 'lukio_favorites_button', output the button markup
     * 
     * @author Itai Dotan
     */
    public function button_markup($atts)
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
        ob_start();
?>
        <button class="lukio_favorites_button<?php echo $atts['class']; ?>" type="button" data-lukio-fav="<?php echo $lukio_favorites->get_favorites_status($post_id) ? 1 : 0; ?>" data-post-id="<?php echo $post_id; ?>" data-post-type="<?php echo $post_type; ?>">
            <?php $lukio_favorites->get_button_content(); ?>
        </button>

<?php
        return ob_get_clean();
    }
}
new Lukio_Favorites_Setup();

if (!function_exists('lukio_favorites')) {
    /**
     * get an instance of Lukio_Favorites_Class
     * 
     * @author Itai Dotan
     */
    function lukio_favorites()
    {
        return Lukio_Favorites_Class::get_instance();
    }
}
