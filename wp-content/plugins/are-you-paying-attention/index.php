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
        add_action( "init", array(
            $this,"adminAssets"
        ));
        
    }
    
    function adminAssets(){
        wp_register_style("quizeditcss", plugin_dir_url(__FILE__)."build/index.css");



        wp_register_script("ournewblocktype", plugin_dir_url(__FILE__)."build/index.js", array(
            "wp-blocks",
            "wp-element",
            "wp-editor"
        ));
        register_block_type("ourplugin/are-you-paying-attention", array(
            "editor_script"=> "ournewblocktype",
            "render_callback" => array($this, "theHTML"),
            "editor_style" => "quizeditcss"
        ));

    }

    function theHTML($attributes){

        if(!is_admin()){
            wp_enqueue_script("attentionFrontend", plugin_dir_url(__FILE__ )."build/frontend.js", array(
                "wp-element"
            ));
            wp_enqueue_style("attentionFrontendStyles",plugin_dir_url(__FILE__)."build/frondend.css");
 
        }
       
        ob_start(); ?>
        <div class="paying-attention-update-me"></div>
        <?php  return ob_get_clean(); 
    }


}




$areYouPayingAttention = new AreYouPayingAttention();