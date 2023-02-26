<?php

/**
 * markup of the plugin admin option page svg related options
 */

defined('ABSPATH') || exit;
?>

<label class="lukio_favorirs_svg_select_label lukio_favorites_label" for="svg_index">
    <span><?php echo __('Select button icon', 'lukio-favorites-plugin'); ?></span>
    <select class="lukio_favorirs_svg_picker" name="svg_index" id="svg_index" autocomplete="off">

        <?php
        $svgs_markup = '';

        foreach ($lukio_favorites->get('svg_array') as $svg_index => $svg_data) {
            $selected_svg = $svg_index == $active_options['svg_index'];
        ?>
            <option value="<?php echo $svg_index; ?>" <?php echo $selected_svg ? ' selected' : ''; ?>><?php echo $svg_data['name']; ?></option>

            <?php
            ob_start();
            ?>
            <div class="lukio_favorirs_button_content_svg<?php echo $selected_svg ? '' : ' hide_option'; ?>" data-index="<?php echo $svg_index; ?>">
                <?php echo $svg_data['svg']; ?>
            </div>
        <?php
            $svgs_markup .= ob_get_clean();
        }
        ?>

    </select>
</label>

<input type="text" class="lukio_favorirs_color_picker" name="button_color" id="button_color" value="<?php echo esc_attr($active_options['button_color']); ?>" autocomplete="off">
<input type="hidden" id="lukio_default_color" name="lukio_default_color" value="<?php echo esc_attr($lukio_favorites->get('default_color')); ?>" autocomplete="off">