<?php

/**
 * markup of the plugin admin option page for button options tab content
 */
?>
<div class="lukio_favorirs_switch_wrapper">
    <span><?php echo __('Use custom image', 'lukio-favorites-plugin'); ?></span>
    <label class="lukio_favorites_switch" for="custom_button">
        <input class="lukio_favorites_switch_input" type="checkbox" name="lukio_favorites[custom_button]" id="custom_button" <?php echo $custom_button ? ' checked' : ''; ?> autocomplete="off">
        <span class="lukio_favorites_switch_slider"></span>
    </label>
</div>

<div class="lukio_custom_button_wrapper<?php if ($custom_button) {
                                            echo ' hide_option';
                                        } ?>">

    <label class="lukio_svg_select_label lukio_label" for="svg_index">
        <span><?php echo __('Select button icon', 'lukio-favorites-plugin'); ?></span>
        <select class="lukio_svg_picker" name="lukio_favorites[svg_index]" id="svg_index" autocomplete="off">
            <?php
            $svgs_markup = '';
            foreach ($lukio_favorites->get('svg_array') as $svg_index => $svg_data) {
                $selected_svg = $svg_index == $active_options['svg_index'];
            ?>
                <option value="<?php echo $svg_index; ?>" <?php echo $selected_svg ? ' selected' : ''; ?>><?php echo $svg_data['name']; ?></option>
                <?php
                ob_start();
                ?>
                <div class="button_content_svg<?php echo $selected_svg ? '' : ' hide_option'; ?>" data-index="<?php echo $svg_index; ?>">
                    <?php echo $svg_data['svg']; ?>
                </div>
            <?php
                $svgs_markup .= ob_get_clean();
            }
            ?>
        </select>
    </label>

    <input type="text" class="lukio_color_picker" name="lukio_favorites[button_color]" id="button_color" value="<?php echo esc_attr($button_color); ?>" autocomplete="off">
    <input type="hidden" id="lukio_default_color" name="lukio_default_color" value="<?php echo esc_attr($lukio_favorites->get('default_color')); ?>">
</div>

<div class="lukio_custom_images_wrapper<?php if (!$custom_button) {
                                            echo ' hide_option';
                                        } ?>">
    <label class="lukio_custom_images_label" for="custom_button_off_btn">
        <span class="custom_image_span"><?php echo __('Image when not in favorites', 'lukio-favorites-plugin'); ?></span>
        <div class="lukio_custom_image_selector_wrapper">
            <img class="preview_image" src="<?php echo wp_get_attachment_image_url($custom_button_off, 'medium', false); ?>" alt="">
            <input type="hidden" value="<?php echo $custom_button_off; ?>" class="lukio_process_custom_images" id="custom_button_off" name="lukio_favorites[custom_button_off]" autocomplete="off">
            <button id="custom_button_off_btn" class="lukio_set_custom_images button"><?php echo __('Pick image', 'lukio-favorites-plugin'); ?></button>
        </div>
    </label>

    <label class="lukio_custom_images_label" for="custom_button_on_btn">
        <span class="custom_image_span"><?php echo __('Image when in favorites', 'lukio-favorites-plugin'); ?></span>
        <div class="lukio_custom_image_selector_wrapper">
            <img class="preview_image" src="<?php echo wp_get_attachment_image_url($custom_button_on, 'medium', false); ?>" alt="">
            <input type="hidden" value="<?php echo $custom_button_on; ?>" class="lukio_process_custom_images" id="custom_button_on" name="lukio_favorites[custom_button_on]" autocomplete="off">
            <button id="custom_button_on_btn" class="lukio_set_custom_images button"><?php echo __('Pick image', 'lukio-favorites-plugin'); ?></button>
        </div>
    </label>
</div>

<div class="lukio_button_size_wrapper">
    <label class="lukio_label" for="button_width">
        <span><?php echo __('Button width', 'lukio-favorites-plugin') ?></span>
        <input class="lukio_edit_button_size" type="number" name="lukio_favorites[button_width]" id="button_width" value="<?php echo esc_attr($active_options['button_width']); ?>" min="0" autocomplete="off">
    </label>
    <label class="lukio_label" for="button_height">
        <span><?php echo __('Button height', 'lukio-favorites-plugin') ?></span>
        <input class="lukio_edit_button_size" type="number" name="lukio_favorites[button_height]" id="button_height" value="<?php echo esc_attr($active_options['button_height']); ?>" min="0" autocomplete=" off">
    </label>
</div>

<div class="lukio_button_preview_wrapper">
    <h3 class="lukio_default_btn_span"><?php echo __('Preview of the button', 'lukio-favorites-plugin'); ?></h3>
    <div class="lukio_default_btn_wrapper">
        <div class="lukio_favorites_button" data-lukio-fav="0">
            <div class="button_content<?php if ($custom_button) {
                                            echo ' hide_option';
                                        } ?>">
                <?php
                echo $svgs_markup;
                ?>
            </div>
            <div class="button_content<?php if (!$custom_button) {
                                            echo ' hide_option';
                                        } ?>">
                <?php
                $off_image = wp_get_attachment_image($active_options['custom_button_off'], 'thumbnail', false, array('class' => 'lukio_favorites_button_image not_added'));
                echo $off_image ? $off_image : '<img class="lukio_favorites_button_image not_added" src="" alt="">';
                ?>
            </div>
        </div>
        <div class="lukio_favorites_button" data-lukio-fav="1">
            <div class="button_content<?php if ($custom_button) {
                                            echo ' hide_option';
                                        } ?>">
                <?php
                echo $svgs_markup;
                ?>
            </div>
            <div class="button_content<?php if (!$custom_button) {
                                            echo ' hide_option';
                                        } ?>">
                <?php
                $on_image = wp_get_attachment_image($active_options['custom_button_on'], 'thumbnail', false, array('class' => 'lukio_favorites_button_image added'));
                echo $on_image ? $on_image : '<img class="lukio_favorites_button_image added" src="" alt="">';
                ?>
            </div>
        </div>
    </div>

    <style>
        <?php
        echo $lukio_favorites->button_dynamic_css();
        ?>
    </style>
</div>