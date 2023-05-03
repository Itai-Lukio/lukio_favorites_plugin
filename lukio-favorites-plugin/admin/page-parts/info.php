<?php

/**
 * markup of the plugin admin option tab for info and tips
 * 
 */

defined('ABSPATH') || exit;

include __DIR__ . '/info-text.php';
?>

<div class="lukio_favorites_shortcode_wrapper">
    <h2 class="lukio_favorites_shortcode_name"><?php echo __('Favorites button', 'lukio-favorites-plugin'); ?></h2>
    <p class="lukio_favorites_shortcode_general"><?php echo $button_general_info; ?></p>
    <div class="lukio_favorites_info_wrapper">
        <div class="lukio_favorites_sub_title">
            <span class="lukio_favorites_sub_title_span"><?php echo __('Base shortcode', 'lukio-favorites-plugin'); ?></span>
            <code class="lukio_favorites_sub_title_code">[lukio_favorites_button]</code>
        </div>
        <p class="lukio_favorites_info_description"><?php echo $buton_base_shortcode; ?></p>
    </div>

    <div class="lukio_favorites_info_wrapper">
        <div class="lukio_favorites_sub_title">
            <span class="lukio_favorites_sub_title_span">post_id</span>
            <code class="lukio_favorites_sub_title_code">[lukio_favorites_button post_id=my_post_id]</code>
        </div>
        <p class="lukio_favorites_info_description"><?php echo $button_post_id; ?></p>
    </div>

    <div class="lukio_favorites_info_wrapper">
        <div class="lukio_favorites_sub_title">
            <span class="lukio_favorites_sub_title_span">class</span>
            <code class="lukio_favorites_sub_title_code">[lukio_favorites_button class=my_class_name]</code>
        </div>
        <p class="lukio_favorites_info_description"><?php echo $button_class; ?></p>
    </div>
</div>

<div class="lukio_favorites_shortcode_wrapper">
    <h2 class="lukio_favorites_shortcode_name"><?php echo __('Favorites page', 'lukio-favorites-plugin'); ?></h2>
    <div class="lukio_favorites_info_wrapper">
        <div class="lukio_favorites_sub_title">

            <code class="lukio_favorites_sub_title_code">[lukio_favorites_page]</code>
        </div>
    </div>
</div>