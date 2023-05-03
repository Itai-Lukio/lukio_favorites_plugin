<?php

/**
 * markup of the plugin admin option page size related options
 */

defined('ABSPATH') || exit;
?>

<div class="lukio_favorites_button_size_wrapper">
    <?php
    $sizes = array(
        'width' => __('Button width', 'lukio-favorites-plugin'),
        'height' => __('Button height', 'lukio-favorites-plugin'),
    );
    foreach ($sizes as $size => $label) {
        $button_identifier  = $button_prefix . $size;
    ?>
        <label class="lukio_favorites_label" for="<?php echo $button_identifier; ?>">
            <span><?php echo $label; ?></span>
            <input class="lukio_favorites_edit_button_size" type="number" name="<?php echo $button_identifier; ?>" id="<?php echo $button_identifier; ?>" value="<?php echo esc_attr($active_options[$button_identifier]); ?>" min="0" autocomplete="off" data-target="<?php echo $edit_button; ?>">
        </label>
    <?php
    }
    ?>
</div>