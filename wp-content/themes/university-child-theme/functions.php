<?php

function child_enqueue_ficitional_university_stylesheet( ) {
	wp_enqueue_style(
		'fictional-university-theme',
		get_template_directory_uri() . '/build/style-index.css'
	);

    wp_enqueue_style(
        'fictional-university-theme',
        get_template_directory_uri() .'/build/index.css');
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_ficitional_university_stylesheet' );



function wpshout_filter_example( $title ) {
	return 'Hooked by Child! ' . $title;
}
add_filter( 'the_title', 'wpshout_filter_example' );


?>