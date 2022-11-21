<?php
global $lukio_favorites_plugin_class;
$lukio_favorites_plugin_class = include __DIR__ . '/lukio_favorites_class.php';

if (!function_exists('lukio_favorites_init')) {
    function lukio_favorites_init()
    {
        if (!session_id()) {
            session_start();
            if (!isset($_SESSION['lukio_fav_session'])) {
                $_SESSION['lukio_fav_session'] = array();
            }
        }
        load_plugin_textdomain('lukio-favorites-plugin', false, 'lukio-favorites-plugin/languages');
    }
}
add_action('init', 'lukio_favorites_init');

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('lukio_favorites_stylesheets', WP_PLUGIN_URL . '/lukio-favorites-plugin/assets/css/lukio_favorites.min.css', [], filemtime(WP_PLUGIN_DIR . '/lukio-favorites-plugin/assets/css/lukio_favorites.min.css'));
    wp_enqueue_script('lukio_favorites_script', WP_PLUGIN_URL . '/lukio-favorites-plugin/assets/js/lukio_favorites.min.js', ['jquery'], filemtime(WP_PLUGIN_DIR . '/lukio-favorites-plugin/assets/js/lukio_favorites.min.js'));
    wp_localize_script(
        'lukio_favorites_script',
        'lukio_favorites_ajax',
        array('ajax_url' => admin_url('admin-ajax.php'))
    );
});

if (!function_exists('lukio_favorites')) {
    function lukio_favorites()
    {
        global $lukio_favorites_plugin_class;
        return $lukio_favorites_plugin_class;
    }
}

if (!function_exists('lukio_favorites_button_click')) {
    function lukio_favorites_button_click()
    {
        $new_status = lukio_favorites()->favorites_button_clicked((int)$_POST['post_id']) ? 1 : 0;
        echo json_encode(array(
            'favorite' => $new_status,
        ));
        wp_die();
    }
}
add_action('wp_ajax_lukio_favorites_button_click', 'lukio_favorites_button_click');
add_action('wp_ajax_nopriv_lukio_favorites_button_click', 'lukio_favorites_button_click');

function lukio_favorites_button_markup($atts)
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

    $lukio_fav = lukio_favorites();
    ob_start();
?>
    <button class="lukio_favorites_button<?php echo $atts['class']; ?>" type="button" data-lukio-fav="<?php echo $lukio_fav->get_favorites_status($post_id) ? 1 : 0; ?>" data-post-id="<?php echo $post_id; ?>">
        <?php $lukio_fav->get_button_content(); ?>
    </button>

<?php
    return ob_get_clean();
}
add_shortcode('lukio_favorites_button', 'lukio_favorites_button_markup');
