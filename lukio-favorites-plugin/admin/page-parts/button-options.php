<?php

/**
 * markup of the plugin admin option page for button options tab content
 */

defined('ABSPATH') || exit;

?>

<div class="lukio_favorirs_switch_wrapper">
    <label for="text_button">
        <span><?php echo __('Use text button', 'lukio-favorites-plugin'); ?></span>
        <label class="lukio_favorites_switch" for="text_button">
            <input class="lukio_favorites_switch_input" type="checkbox" name="text_button" id="text_button" <?php echo $active_options['text_button'] ? ' checked' : ''; ?> autocomplete="off">
            <span class="lukio_favorites_switch_slider"></span>
        </label>
    </label>
</div>

<div class="lukio_favorirs_text_button_wrapper<?php if (!$active_options['text_button']) {
                                                    echo ' hide_option';
                                                } ?>">
    <?php include __DIR__ . '/button-options/text-optioin.php'; ?>
</div>

<div class="lukio_favorirs_image_button_wrapper<?php if ($active_options['text_button']) {
                                                    echo ' hide_option';
                                                } ?>">
    <div class="lukio_favorirs_switch_wrapper">
        <label for="custom_button">
            <span><?php echo __('Use custom image', 'lukio-favorites-plugin'); ?></span>
            <label class="lukio_favorites_switch" for="custom_button">
                <input class="lukio_favorites_switch_input" type="checkbox" name="custom_button" id="custom_button" <?php echo $active_options['custom_button'] ? ' checked' : ''; ?> autocomplete="off">
                <span class="lukio_favorites_switch_slider"></span>
            </label>
        </label>
    </div>

    <div class="lukio_favorirs_custom_button_wrapper<?php if ($active_options['custom_button']) {
                                                        echo ' hide_option';
                                                    } ?>">
        <?php include __DIR__ . '/button-options/svg-optioin.php'; ?>
    </div>

    <div class="lukio_favorirs_custom_images_wrapper<?php if (!$active_options['custom_button']) {
                                                        echo ' hide_option';
                                                    } ?>">
        <?php include __DIR__ . '/button-options/images-optioin.php'; ?>
    </div>

    <?php
    include __DIR__ . '/button-options/size-optioin.php';

    include __DIR__ . '/button-options/preview-button.php';
    ?>
</div>