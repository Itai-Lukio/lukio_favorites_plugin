<?php
class Lukio_Favorites_Setup
{
    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue'));

        add_action('wp_ajax_lukio_favorites_button_click', array($this, 'ajax_click'));
        add_action('wp_ajax_nopriv_lukio_favorites_button_click', array($this, 'ajax_click'));

        add_shortcode('lukio_favorites_button', array($this, 'button_markup'));
    }

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

    public function enqueue()
    {
        wp_enqueue_style('lukio_favorites_stylesheets', WP_PLUGIN_URL . '/lukio-favorites-plugin/assets/css/lukio_favorites.min.css', [], filemtime(WP_PLUGIN_DIR . '/lukio-favorites-plugin/assets/css/lukio_favorites.min.css'));
        wp_enqueue_script('lukio_favorites_script', WP_PLUGIN_URL . '/lukio-favorites-plugin/assets/js/lukio_favorites.min.js', ['jquery'], filemtime(WP_PLUGIN_DIR . '/lukio-favorites-plugin/assets/js/lukio_favorites.min.js'));
        wp_localize_script(
            'lukio_favorites_script',
            'lukio_favorites_ajax',
            array('ajax_url' => admin_url('admin-ajax.php'))
        );
    }

    public function ajax_click()
    {
        $new_status = lukio_favorites()->favorites_button_clicked((int)$_POST['post_id']) ? 1 : 0;
        echo json_encode(array(
            'favorite' => $new_status,
        ));
        die;
    }

    public function button_markup($atts)
    {
        $atts = shortcode_atts(
            array(
                'post_id' => null,
                'class' => null,
            ),
            $atts
        );

        $post_id = is_null($atts['post_id']) ? get_post()->ID : (int)$atts['post_id'];
        $atts['class'] = is_null($atts['class']) ? '' : ' ' . trim($atts['class']);

        $lukio_favorites = lukio_favorites();
        ob_start();
?>
        <button class="lukio_favorites_button<?php echo $atts['class']; ?>" type="button" data-lukio-fav="<?php echo $lukio_favorites->get_favorites_status($post_id) ? 1 : 0; ?>" data-post-id="<?php echo $post_id; ?>">
            <?php $lukio_favorites->get_button_content(); ?>
        </button>

<?php
        return ob_get_clean();
    }
}
new Lukio_Favorites_Setup();

if (!function_exists('lukio_favorites')) {
    function lukio_favorites()
    {
        return Lukio_Favorites_Class::get_instance();
    }
}
