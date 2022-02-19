<?php

/*
Plugin Name: Are you paying attention
Description: block something or other
Version: 1.0
Author: Josh
Author URI: https://frshminds.com


*/



if(!defined("ABSPATH")) exit; //exit if access directly




class AreYouPayingAttention{


    function __construct()
    {
        add_action( "enqueue_block_editor_assets", array(
            $this,"adminAssets"
        ));
        
    }
    
    function adminAssets(){

        wp_enqueue_script("ournewblocktype", plugin_dir_url(__FILE__)."test.js", array(
            "wp-blocks",
            "wp-element"
        ));

    }


}




$areYouPayingAttention = new AreYouPayingAttention();