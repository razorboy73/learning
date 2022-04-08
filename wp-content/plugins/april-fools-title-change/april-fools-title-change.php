<?php
/*
Plugin Name: April Fools Title Change
Description:  Is it good or bad
Plugin URI: http://frshminds.com
Version: 1.0
Author: Josh Kerbel
License: GPL2

*/


function april_fools_title(){
    $joke_title = "Snot Nosed Kid";
    $site_title = get_option("blogname");

    //Save "normal" title
    if($site_title !== $joke_title){
        update_option("site_normal_title", $site_title);
    }

    
    

    // If normal_title exists and the site title's the joke title, change it back
    $normal_title =  get_option("site_normal_title");
    if($site_title == $joke_title && $normal_title){
        update_option("blogname", $normal_title);
    }
}

add_action("init", "april_fools_title");