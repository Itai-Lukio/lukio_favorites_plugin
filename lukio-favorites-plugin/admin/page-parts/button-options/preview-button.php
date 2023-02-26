<?php

/**
 * markup of the plugin admin option page preview button
 */

defined('ABSPATH') || exit;
?>

<div class="lukio_favorirs_button_preview_wrapper">
    <h3 class="lukio_favorirs_default_btn_span"><?php echo __('Preview of the button', 'lukio-favorites-plugin'); ?></h3>
    <div class="lukio_favorirs_default_btn_wrapper">
        <?php
        $preview_options = array(
            array(
                'state' => 0,
                'option_index' => 'custom_button_off',
                'indicator_class' => 'not_added',
            ),
            array(
                'state' => 1,
                'option_index' => 'custom_button_on',
                'indicator_class' => 'added',
            )
        );
        foreach ($preview_options as $preview_data) {
        ?>
            <div class="lukio_favorites_button image_button preview" data-lukio-fav="<?php echo $preview_data['state']; ?>">
                <div class="lukio_favorirs_button_content<?php if ($active_options['custom_button']) {
                                                                echo ' hide_option';
                                                            } ?>">
                    <?php
                    echo $svgs_markup;
                    ?>
                </div>
                <div class="lukio_favorirs_button_content<?php if (!$active_options['custom_button']) {
                                                                echo ' hide_option';
                                                            } ?>">
                    <?php
                    $image_markup = wp_get_attachment_image($active_options[$preview_data['option_index']], 'thumbnail', false, array('class' => 'lukio_favorites_button_image ' . $preview_data['indicator_class']));
                    echo $image_markup ? $image_markup : '<img class="lukio_favorites_button_image ' . $preview_data['indicator_class'] . '" src="" alt="">';
                    ?>
                </div>
            </div>
        <?php
        }
        ?>
    </div>

    <style>
        <?php
        echo $lukio_favorites->button_dynamic_css();
        ?>
    </style>
</div>