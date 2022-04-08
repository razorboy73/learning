<?php
/*
Plugin Name: Shortcode demo plugin
Description:  Learning how to use shortcodes via a plugin
Plugin URI: http://frshminds.com
Version: 1.0
Author: Josh Kerbel
License: GPL2

*/

// The shortcode name—the name users will enter into their posts to invoke the shortcode.
// The name of the function which will process the shortcode data and return it to 
// WordPress’s main PHP processing. We’ll be creating this function in the next section.

add_shortcode("wpshout_demo_shortcode","wpshout_color_demo_shortcode");



//Handler function 
//Name matches second parameter

function wpshout_color_demo_shortcode($attributes, $content=""){
    $attributes = shortcode_atts( array(
        "background" => "gold",
        "color" => "blue"
    ), $attributes, "wpshout_color_demo_shortcode"
);
return '<p style="background: '. $attributes['background'] . '; color: ' .$attributes['color'].';">' . $content .'</p>';
}