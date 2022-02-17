<?php

/*
Plugin Name: Our Filter Plugin
Description: Replaces words
Version: 1.0
Author: Josh
Author URI: https://frshminds.com


*/


if(!defined('ABSPATH')) exit; //Exit if access directly


class OurWordFilterPlugin {

    function __construct(){
        add_action("admin_menu", array($this, "ourMenu"));
    }

    function ourMenu(){
        add_menu_page("Words To Filter","Word Filter","manage_options","ourwordfilter", array(
            $this,
            "wordFilterPage"
             ),
             "dashicons-smiley",100);
        add_submenu_page( "ourwordfilter", "Word Filter Options", "Options", "manage_options", "word-filter-options", array(
                $this,
                "optionsSubPage"
            ));
        
        add_submenu_page( "ourwordfilter", "Word Filter Options", "Options", "manage_options", "word-filter-options", array(
            $this,
            "optionsSubPage"
        ));
    }
    function wordFilterPage(){?>

hi
    <?php }

    function optionsSubPage(){?>

        hi again
            <?php }

}

$ourWordFilterPlugin= new OurWordFilterPlugin();