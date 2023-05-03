<?php

/**
 * The template for displaying the content of the favorites page
 *
 * This template can be overridden by copying it to yourtheme/lukio-favorites/favorites-content.php.
 */

defined('ABSPATH') || exit;

?>
<div class="lukio_favorites_page_wrapper">
    <?php
    if ($empty_favorites) {
    ?>
        <span><?php echo __('Nothing was saved to favorites yet', 'lukio-favorites-plugin'); ?></span>
        <?php
    } else {
        $woocommerce_active = is_plugin_active('woocommerce/woocommerce.php');

        foreach ($user_favorites as $type => $posts) {
            $is_wc_product = $woocommerce_active && $type === 'product';
            foreach ($posts as $post_id) {
                $post = get_post($post_id);

                // display ony existing and published posts
                if (is_null($post) || $post->post_status !== 'publish') {
                    continue;
                }
        ?>
                <div class="lukio_favorites_post lukio_favorites_page_post <?php $type; ?>">
                    <?php
                    $post_thumbnail = get_post_thumbnail_id($post);

                    if ($post_thumbnail) {
                        echo wp_get_attachment_image($post_thumbnail, 'thumbnail', false, array('class' => 'lukio_favorites_page_post_thumbnail'));
                    } else {
                        echo '<img class="lukio_favorites_page_post_thumbnail" src="" alt="">';
                    }

                    ?>
                    <h4 class="lukio_favorites_page_post_title"><?php echo get_the_title($post); ?></h4>

                    <?php
                    // show the price and woocommerce loop button when a wc_product
                    if ($is_wc_product) {
                        global $product;
                        $product = wc_get_product($post_id);
                    ?>
                        <p class="lukio_favorites_page_p"><?php echo $product->get_price_html(); ?></p>
                    <?php
                        woocommerce_template_loop_add_to_cart();
                    } else {
                    ?>
                        <p class="lukio_favorites_page_p"><?php echo get_the_excerpt($post); ?></p>
                        <a class="button" href="<?php echo get_post_permalink($post); ?>"><?php echo __('Read More', 'lukio-favorites-plugin'); ?></a>
                    <?php
                    }
                    ?>
                </div>
    <?php
            }
        }
    }
    ?>
</div>