<?php
/*
Plugin Name: Title Amendment
Description:  Adds "University" infront of titles
Plugin URI: http://frshminds.com
Version: 1.0
Author: Josh Kerbel
License: GPL2

*/



function modify_title($title){
    return "University: " . $title;
}


add_filter("the_title","modify_title");


function action_example(){
    echo "Action Example";
}


add_action("wp_footer","action_example");