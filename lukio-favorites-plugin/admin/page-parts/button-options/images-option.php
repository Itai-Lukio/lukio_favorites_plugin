<?php

/**
 * markup of the plugin admin option page images related options
 */

defined('ABSPATH') || exit;

foreach ($image_buttons as $button_type => $button_data) {
    $button_id = 'custom_' . $button_prefix . $button_type;
?>
    <label class="lukio_favorites_custom_images_label" for="<?php echo $button_id; ?>_btn">
        <span><?php echo $button_data['label']; ?></span>
        <div class="lukio_favorites_custom_image_selector_wrapper">
            <img class="lukio_favorites_custom_image_preview" src="<?php echo wp_get_attachment_image_url($button_data['image_id'], 'medium', false); ?>" alt="">
            <input type="hidden" value="<?php echo $button_data['image_id']; ?>" class="lukio_favorites_process_custom_images" id="<?php echo $button_id; ?>" name="<?php echo $button_id; ?>" autocomplete="off">
            <button id="<?php echo $button_id; ?>_btn" class="lukio_favorites_set_custom_images button" data-popup-title="<?php echo $button_data['label']; ?>" type="button"><?php echo __('Pick image', 'lukio-favorites-plugin'); ?></button>
        </div>
    </label>
<?php
}
?>