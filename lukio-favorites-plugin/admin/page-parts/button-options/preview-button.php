<?php

/**
 * markup of the plugin admin option page preview button
 */

defined('ABSPATH') || exit;
?>

<div class="lukio_favorites_button_preview_wrapper">
    <h3 class="lukio_favorites_default_btn_span"><?php echo __('Preview of the button', 'lukio-favorites-plugin'); ?></h3>
    <div class="lukio_favorites_default_btn_wrapper">
        <?php
        $preview_options = array(
            array(
                'state' => 0,
                'option_index' => $off_option,
                'indicator_class' => 'not_added',
            ),
            array(
                'state' => 1,
                'option_index' => $on_option,
                'indicator_class' => 'added',
            )
        );
        foreach ($preview_options as $preview_data) {
            $empty = $preview_data['state'] ? '' : ' empty';
        ?>
            <div class="<?php echo $preview_class; ?> preview_button image_button preview<?php echo $empty; ?>" data-lukio-fav="<?php echo $preview_data['state']; ?>" data-target="<?php echo $edit_button; ?>">
                <div class="lukio_favorites_button_content<?php if ($active_options[$edit_button]) {
                                                                echo ' hide_option';
                                                            } ?>" data-toggle="<?php echo $edit_button; ?>">
                    <?php
                    echo $svgs_markup;
                    ?>
                </div>
                <div class="lukio_favorites_button_content<?php if (!$active_options[$edit_button]) {
                                                                echo ' hide_option';
                                                            } ?>" data-toggle="<?php echo $edit_button; ?>">
                    <?php
                    $img_class = $preview_class . '_image ' . $preview_data['indicator_class'] . $empty;
                    $image_markup = wp_get_attachment_image($active_options[$preview_data['option_index']], 'thumbnail', false, array('class' => $img_class));
                    echo $image_markup ? $image_markup : '<img class="' . $img_class . '" src="" alt="">';
                    ?>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>