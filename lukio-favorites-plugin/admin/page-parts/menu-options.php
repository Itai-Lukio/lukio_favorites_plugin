<?php

/**
 * markup of the plugin admin option tab for menu button
 */

defined('ABSPATH') || exit;

$text_inputs = array(
    'menu_button_text' => __('Menu button title', 'lukio-favorites-plugin')
);

$edit_button = 'custom_menu_button';
$svg_option_name = 'menu_svg_index';
$svg_color_option_name = 'menu_button_color';
$button_prefix = 'menu_button_';
$preview_class = 'lukio_favorites_menu_button';
$off_option = 'custom_menu_button_off';
$on_option = 'custom_menu_button_on';

$image_buttons = array(
    'off' => array(
        'label' => __('Image when favorites are empty', 'lukio-favorites-plugin'),
        'image_id' => $active_options[$off_option]
    ),
    'on' => array(
        'label' => __('Image when favorites are not empty', 'lukio-favorites-plugin'),
        'image_id' => $active_options[$on_option]
    ),
);

// add the menu integration only when the theme supports 'menus' 
if (current_theme_supports('menus')) {
?>

    <div class="lukio_favorites_switch_wrapper">
        <span><?php echo __('Add favorites button to menu', 'lukio-favorites-plugin'); ?></span>
        <label class="lukio_favorites_switch" for="add_to_menu">
            <input class="lukio_favorites_switch_input" type="checkbox" name="add_to_menu" id="add_to_menu" <?php echo $active_options['add_to_menu'] ? ' checked' : ''; ?> autocomplete="off">
            <span class="lukio_favorites_switch_slider"></span>
        </label>
    </div>

    <div class="lukio_favorites_nav_menu_options<?php if (!$active_options['add_to_menu']) {
                                                    echo ' hide_option';
                                                } ?>" data-toggle="add_to_menu">

        <label class="lukio_favorites_menu_select_label lukio_favorites_label" for="menu_add_slug">
            <span><?php echo __('Menu to add button to', 'lukio-favorites-plugin'); ?></span>
            <?php
            echo str_replace('<select', '<select autocomplete="off"', wp_dropdown_categories(array(
                'taxonomy' => 'nav_menu',
                'hide_if_empty' => false,
                'hide_empty' => false,
                'selected' => $active_options['menu_add_slug'],
                'name' => 'menu_add_slug',
                'class' => 'lukio_favorites_menu_select',
                'value_field' => 'slug',
                'show_option_none' => __('Select menu', 'lukio-favorites-plugin'),
                'echo' => false
            )));
            ?>
        </label>

        <div class="lukio_favorites_switch_wrapper">
            <span><?php echo __('Place favorites button at the start of the menu', 'lukio-favorites-plugin'); ?></span>
            <label class="lukio_favorites_switch" for="add_to_menu_start">
                <input class="lukio_favorites_switch_input" type="checkbox" name="add_to_menu_start" id="add_to_menu_start" <?php echo $active_options['add_to_menu_start'] ? ' checked' : ''; ?> autocomplete="off">
                <span class="lukio_favorites_switch_slider"></span>
            </label>
        </div>

    </div>

<?php
}
?>

<div class="lukio_favorites_switch_wrapper">
    <span><?php echo __('Use text button', 'lukio-favorites-plugin'); ?></span>
    <label class="lukio_favorites_switch" for="menu_button_use_text">
        <input class="lukio_favorites_switch_input" type="checkbox" name="menu_button_use_text" id="menu_button_use_text" <?php echo $active_options['menu_button_use_text'] ? ' checked' : ''; ?> autocomplete="off">
        <span class="lukio_favorites_switch_slider"></span>
    </label>
</div>

<div class="lukio_favorites_text_button_wrapper">
    <?php include __DIR__ . '/button-options/text-option.php'; ?>
</div>

<div class="lukio_favorites_image_button_wrapper<?php if ($active_options['menu_button_use_text']) {
                                                    echo ' hide_option';
                                                } ?>" data-toggle="menu_button_use_text">
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