<?php

/**
 * markup of the plugin admin option page text button related options
 */

defined('ABSPATH') || exit;
?>

<span class="lukio_favorites_text_instruction"><?php echo __('Saving empty field will go back to the current language default, if supported', 'lukio-favorites-plugin'); ?></span>

<div class="lukio_favorirs_text_button__fields_wrapper">
    <label class="lukio_favorites_label" for="add_text">
        <span><?php echo __('Add text', 'lukio-favorites-plugin'); ?></span>
        <input name="add_text" id="add_text" type="text" value="<?php echo $active_options['add_text']; ?>">
    </label>

    <label class="lukio_favorites_label" for="remove_text">
        <span><?php echo __('Remove text', 'lukio-favorites-plugin'); ?></span>
        <input name="remove_text" id="remove_text" type="text" value="<?php echo $active_options['remove_text']; ?>">
    </label>
</div>