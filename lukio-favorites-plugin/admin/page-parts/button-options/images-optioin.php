<?php

/**
 * markup of the plugin admin option page images related options
 */

defined('ABSPATH') || exit;

$image_buttons = array(
    'off' => array(
        'label' => __('Image when not in favorites', 'lukio-favorites-plugin'),
        'image_id' => $active_options['custom_button_off']
    ),
    'on' => array(
        'label' => __('Image when in favorites', 'lukio-favorites-plugin'),
        'image_id' => $active_options['custom_button_on']
    ),
);
foreach ($image_buttons as $button_type => $button_data) {
    $button_id = 'custom_button_' . $button_type
?>
    <label class="lukio_favorirs_custom_images_label" for="<?php echo $button_id; ?>_btn">
        <span><?php echo $button_data['label']; ?></span>
        <div class="lukio_favorirs_custom_image_selector_wrapper">
            <img class="lukio_favorirs_custom_image_preview" src="<?php echo wp_get_attachment_image_url($button_data['image_id'], 'medium', false); ?>" alt="">
            <input type="hidden" value="<?php echo $button_data['image_id']; ?>" class="lukio_favorirs_process_custom_images" id="<?php echo $button_id; ?>" name="<?php echo $button_id; ?>" autocomplete="off">
            <button id="<?php echo $button_id; ?>_btn" class="lukio_favorirs_set_custom_images button" data-popup_title="<?php echo $button_data['label']; ?>" type="button"><?php echo __('Pick image', 'lukio-favorites-plugin'); ?></button>
        </div>
    </label>
<?php
}
?>