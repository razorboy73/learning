<?php


function university_post_types(){

    //event post type
    register_post_type("event", array(
        //for the archive, it uses the register post type name parameter, not the label
        //or url rewrite
        "has_archive" => true,
     
        "show_in_rest" => true,
        "description" => "What is going on around campus",
        "public" => true,
        "labels" => array(
            "name" => "Events",
            "add_new_item" => "Add New Event",
            "edit_item" => "Edit Your Silly Event",
            "all_items" => "All Events",
            "singular_name" => "Event",
        ),
        "menu_icon" => "dashicons-calendar",
        'rewrite'     => array( 'slug' => 'amazing-events' ),
        "supports" => array(
            "title",
            "editor",
            "excerpt",
            

        )
        ),
        

    );

    register_post_type("program", array(
        //for the archive, it uses the register post type name parameter, not the label
        //or url rewrite
        "has_archive" => true,
     
        "show_in_rest" => true,
        "description" => "What is going on around campus",
        "public" => true,
        "labels" => array(
            "name" => "Programs",
            "add_new_item" => "Add New Crappy Program",
            "edit_item" => "Edit Your Boring Program",
            "all_items" => "All Lame Ass Programs",
            "singular_name" => "Program",
        ),
        "menu_icon" => "dashicons-awards",
        'rewrite'     => array( 'slug' => 'programs' ),
        "supports" => array(
            "title",
            "editor",
            "thumbnail"
           
            

        )
        ),
        

    );

    register_post_type("professor", array(
        //Creates professor post type
        //for the archive, it uses the register post type name parameter, not the label
        //or url rewrite
        
        "supports"=> array("title", "editor,"),
        "show_in_rest" => true,
        "description" => "What is going on around campus",
        "public" => true,
        "labels" => array(
            "name" => "Professors",
            "add_new_item" => "Add New Crappy professor",
            "edit_item" => "Edit Your Boring professor",
            "all_items" => "All Old Professors",
            "singular_name" => "professor",
        ),
        "menu_icon" => "dashicons-welcome-learn-more",
        
        "supports" => array(
            "title",
            "editor",
            "excerpt",
            "thumbnail"
            

        )
        ),
        

    );

    //Campus post type
    register_post_type("campus", array(
        //for the archive, it uses the register post type name parameter, not the label
        //or url rewrite
        "has_archive" => true,
     
        "show_in_rest" => true,
        "description" => "What is going on around campus",
        "public" => true,
        "labels" => array(
            "name" => "Campuses",
            "add_new_item" => "Add Hot New Campus",
            "edit_item" => "Edit Your Hot Campus",
            "all_items" => "All Hot Ass Campuses",
            "singular_name" => "Campus",
        ),
        "menu_icon" => "dashicons-location-alt",
        'rewrite'     => array( 'slug' => 'campuses' ),
        "supports" => array(
            "title",
            "editor",
            "thumbnail"
           
            

        )
        ),
        

    );

}

add_action("init","university_post_types");


?>
