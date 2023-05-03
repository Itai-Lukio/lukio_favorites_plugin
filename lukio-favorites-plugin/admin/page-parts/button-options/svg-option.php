<?php

/**
 * markup of the plugin admin option page svg related options
 */

defined('ABSPATH') || exit;
?>

<label class="lukio_favorites_svg_select_label lukio_favorites_label" for="<?php echo $svg_option_name; ?>">
    <span><?php echo __('Select button icon', 'lukio-favorites-plugin'); ?></span>
    <select class="lukio_favorites_svg_picker" name="<?php echo $svg_option_name; ?>" id="<?php echo $svg_option_name; ?>" autocomplete="off">

        <?php
        $svgs_markup = '';

        foreach ($lukio_favorites->get('svg_array') as $svg_index => $svg_data) {
            $selected_svg = $svg_index == $active_options[$svg_option_name];
        ?>
            <option value="<?php echo $svg_index; ?>" <?php echo $selected_svg ? ' selected' : ''; ?>><?php echo $svg_data['name']; ?></option>

            <?php
            ob_start();
            ?>
            <div class="lukio_favorites_button_content_svg<?php echo $selected_svg ? '' : ' hide_option'; ?>" data-index="<?php echo $svg_index; ?>">
                <?php echo $svg_data['svg']; ?>
            </div>
        <?php
            $svgs_markup .= ob_get_clean();
        }
        ?>

    </select>
</label>

<input type="text" class="lukio_favorites_color_picker" name="<?php echo $svg_color_option_name; ?>" id="<?php echo $svg_color_option_name; ?>" value="<?php echo esc_attr($active_options[$svg_color_option_name]); ?>" autocomplete="off">
<input type="hidden" id="lukio_default_color" name="lukio_default_color" value="<?php echo esc_attr($lukio_favorites->get('default_color')); ?>" autocomplete="off">