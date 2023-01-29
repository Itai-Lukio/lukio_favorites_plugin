<?php

/**
 * markup of the plugin admin option page for placing options tab content
 */
?>
<div class="lukio_pages_wrapper">
    <label class="lukio_page_label lukio_label" for="favorites_page_id">
        <span><?php echo __('Favorites page', 'lukio-favorites-plugin'); ?></span>
        <?php
        echo str_replace('<select', '<select autocomplete="off"', wp_dropdown_pages(array(
            'selected' => $active_options['favorites_page_id'],
            'name' => 'lukio_favorites[favorites_page_id]',
            'id' => 'favorites_page_id',
            'echo' => false
        )));
        ?>
    </label>
</div>

<div class="lukio_favorirs_switch_wrapper">
    <span><?php echo __('Add Button to product title', 'lukio-favorites-plugin'); ?></span>
    <label class="lukio_favorites_switch" for="add_to_title">
        <input class="lukio_favorites_switch_input" type="checkbox" name="lukio_favorites[add_to_title]" id="add_to_title" <?php echo $active_options['add_to_title'] ? ' checked' : ''; ?> autocomplete="off">
        <span class="lukio_favorites_switch_slider"></span>
    </label>
</div>

<ul class="lukio_favorirs_posts_list">
    <?php
    $public_posts = array_merge(
        array('post' => 'post'),
        get_post_types(
            array(
                'public'   => true,
                '_builtin' => false
            )
        )
    );
    foreach ($public_posts as $index => $post_type) {
    ?>
        <li>
            <label class="lukio_favorirs_posts_li" for="posts_types_<?php echo $post_type; ?>">
                <input class="lukio_favorirs_posts_li_input" id="posts_types_<?php echo $post_type; ?>" name="post_types[]" type="checkbox" value="<?php echo esc_attr($post_type); ?>" <?php echo in_array($post_type, $active_options['post_types']) ? ' checked' : ''; ?> autocomplete="off">
                <span class="lukio_favorirs_posts_li_text"><?php echo ucfirst($post_type); ?></span>
            </label>
        </li>
    <?php
    }
    ?>
</ul>