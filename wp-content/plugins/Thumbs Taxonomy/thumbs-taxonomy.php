<?php
/*
Plugin Name: Thumbs Taxonomy Plugin
Description:  Is it good or bad
Plugin URI: http://frshminds.com
Version: 1.0
Author: Josh Kerbel
License: GPL2

*/




function register_thumbs_taxonomy(){

    register_taxonomy("thumbs", array("post","event","program","restaurant_review"), array(
        "hierarchical" => true,
        "show_in_rest" => true,
        "label" => "Thumbs up/down"
    ));
}

add_action("init", "register_thumbs_taxonomy");



function register_thumbs_terms(){

    wp_insert_term("Thumbs Up!", "thumbs", array(
        "description" => "This thing gets a Thumbs Up!!!!"
        )
    );

    wp_insert_term("Thumbs Down!", "thumbs", array(
        "description" => "Donkey Balls.  This thing gets a Thumbs Down!!!!"
        )
    );

}

