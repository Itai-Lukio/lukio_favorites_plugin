<?php

/**
 * markup displaying plugin info and tips
 */

defined('ABSPATH') || exit;

$post_id_explanation = __('Can specify ', 'lukio-favorites-plugin');
?>

<div class="lukio_favorirs_info_wrapper">
    <code>[lukio_favorites_button post_id=null class=null]</code>
    <p><span class="lukio_favorites_param_name">post_id</span> <?php echo  $post_id_explanation; ?></p>
</div>

<div class="lukio_favorirs_info_wrapper">
    <code>[lukio_favorites_page]</code>
</div>