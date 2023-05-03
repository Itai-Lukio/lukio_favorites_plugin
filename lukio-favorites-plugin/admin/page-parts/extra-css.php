<?php

/**
 * markup of the plugin admin option tab for extra css tab content
 */

defined('ABSPATH') || exit;

?>

<textarea class="lukio_favorites_textarea" name="extra_css" id="extra_css" cols="30" rows="10" placeholder=".lukio_favorites_button {&#10;&#x09;position: absolute;&#10;}"><?php echo $active_options['extra_css']; ?></textarea>