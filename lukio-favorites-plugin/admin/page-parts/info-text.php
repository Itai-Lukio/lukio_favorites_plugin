<?php

/**
 * text for the info part of the plugin page
 */

defined('ABSPATH') || exit;

$the_loop_link = 'https://developer.wordpress.org/themes/basics/the-loop/';
$loop_link = '<a href="' . $the_loop_link . '" target="_blank">The Loop</a>';

$button_general_info = _x('The button settings are managed in the "%s" tab.', '$s is a placeholder to place the tab translated name later on', 'lukio-favorites-plugin');
$button_general_info = sprintf($button_general_info, __('Button options', 'lukio-favorites-plugin'));

$buton_base_shortcode = _x('The base shortcode for the favorites button to place the button freely and using its basic settings. Use the global post to be its target to be marked in favorites or the current post when used in side %s.', '%s is for placing link for wp "the loop" explanation', 'lukio-favorites-plugin');
$buton_base_shortcode = sprintf($buton_base_shortcode, $loop_link);

$button_post_id = _x('Can specify a post id to be the target to be maked in favorites, usefull to have full control of the button target with out being reliance on %s or the global post.', '%s is for placing link for wp "the loop" explanation', 'lukio-favorites-plugin');
$button_post_id = sprintf($button_post_id, $loop_link);

$button_class = __('Allow to add custom class string to be added to favorites buttons. when you need just a bit more control to have different style or scripts to be added to different buttons in the site.', 'lukio-favorites-plugin');
