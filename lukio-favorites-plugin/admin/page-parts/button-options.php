<?php

/**
 * markup of the plugin admin option tab for button options tab content
 */

defined('ABSPATH') || exit;

$text_inputs = array(
    'add_text' => __('Add text', 'lukio-favorites-plugin'),
    'remove_text' => __('Remove text', 'lukio-favorites-plugin')
);

$edit_button = 'custom_button';
$svg_option_name = 'svg_index';
$svg_color_option_name = 'button_color';
$button_prefix = 'button_';
$preview_class = 'lukio_favorites_button';
$off_option = 'custom_button_off';
$on_option = 'custom_button_on';

$image_buttons = array(
    'off' => array(
        'label' => __('Image when not in favorites', 'lukio-favorites-plugin'),
        'image_id' => $active_options[$off_option]
    ),
    'on' => array(
        'label' => __('Image when in favorites', 'lukio-favorites-plugin'),
        'image_id' => $active_options[$on_option]
    ),
);
?>

<div class="lukio_favorites_switch_wrapper">
    <span><?php echo __('Use text button', 'lukio-favorites-plugin'); ?></span>
    <label class="lukio_favorites_switch" for="text_button">
        <input class="lukio_favorites_switch_input" type="checkbox" name="text_button" id="text_button" <?php echo $active_options['text_button'] ? ' checked' : ''; ?> autocomplete="off">
        <span class="lukio_favorites_switch_slider"></span>
    </label>
</div>

<div class="lukio_favorites_text_button_wrapper<?php if (!$active_options['text_button']) {
                                                    echo ' hide_option';
                                                } ?>" data-toggle="text_button">
    <?php include __DIR__ . '/button-options/text-option.php'; ?>
</div>

<div class="lukio_favorites_image_button_wrapper<?php if ($active_options['text_button']) {
                                                    echo ' hide_option';
                                                } ?>" data-toggle="text_button">
    <div class="lukio_favorites_switch_wrapper">
        <span><?php echo __('Use custom image', 'lukio-favorites-plugin'); ?></span>
        <label class="lukio_favorites_switch" for="<?php echo $edit_button; ?>">
            <input class="lukio_favorites_switch_input" type="checkbox" name="<?php echo $edit_button; ?>" id="<?php echo $edit_button; ?>" <?php echo $active_options[$edit_button] ? ' checked' : ''; ?> autocomplete="off">
            <span class="lukio_favorites_switch_slider"></span>
        </label>
    </div>

    <div class="lukio_favorites_custom_button_wrapper<?php if ($active_options[$edit_button]) {
                                                            echo ' hide_option';
                                                        } ?>" data-toggle="<?php echo $edit_button; ?>">
        <?php include __DIR__ . '/button-options/svg-option.php'; ?>
    </div>

    <div class="lukio_favorites_custom_images_wrapper<?php if (!$active_options[$edit_button]) {
                                                            echo ' hide_option';
                                                        } ?>" data-toggle="<?php echo $edit_button; ?>">
        <?php include __DIR__ . '/button-options/images-option.php'; ?>
    </div>

    <?php
    include __DIR__ . '/button-options/size-option.php';

    include __DIR__ . '/button-options/preview-button.php';
    ?>
</div>