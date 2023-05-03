<?php

/**
 * markup of the plugin admin option page text button related options
 */

defined('ABSPATH') || exit;
?>

<div class="lukio_favorites_text_button_fields_wrapper">
    <?php
    foreach ($text_inputs as $input_name => $input_label) {
    ?>
        <label class="lukio_favorites_label" for="<?php echo $input_name; ?>">
            <span><?php echo $input_label; ?></span>
            <input class="lukio_favorites_text_input" name="<?php echo $input_name; ?>" id="<?php echo $input_name; ?>" type="text" value="<?php echo $active_options[$input_name]; ?>">
        </label>
    <?php
    }
    ?>
</div>

<span class="lukio_favorites_text_instruction"><?php echo __('Saving empty field will go back to the current language default, if supported', 'lukio-favorites-plugin'); ?></span>