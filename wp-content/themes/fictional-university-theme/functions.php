<?php

function university_files(){
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', includes_url( '/js/jquery/jquery.js' ), false, NULL, true );
    wp_enqueue_script( 'jquery' );
    wp_enqueue_style("custom-google-fonts", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i");
    wp_enqueue_style("university_main_styles", get_template_directory_uri().'/build/style-index.css');
    wp_enqueue_style("university_extra_styles", get_template_directory_uri().'/build/index.css');
    wp_enqueue_style("font-awesome", get_template_directory_uri().'/fontawesome/css/all.min.css');
    wp_enqueue_script("font-awesome-js", get_template_directory_uri().'/fontawesome/js/all.min.js',array(), false, true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    
   
}



add_action("wp_enqueue_scripts", "university_files");



function university_features(){
    add_theme_support("title-tag");
}
add_action("after_setup_theme", 'university_features')


?>