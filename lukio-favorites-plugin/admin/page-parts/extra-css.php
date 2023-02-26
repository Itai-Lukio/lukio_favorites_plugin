<?php

/**
 * markup of the plugin admin option page for extar css tab content
 */

defined('ABSPATH') || exit;

?>

<textarea class="lukio_favorirs_textarea" name="extra_css" id="extra_css" cols="30" rows="10" placeholder=".lukio_favorites_button {&#10;&#x09;position: absolute;&#10;}"><?php echo $active_options['extra_css']; ?></textarea>