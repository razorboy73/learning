<?php


function university_post_types(){

    //event post type
    register_post_type("event", array(
        //for the archive, it uses the register post type name parameter, not the label
        //or url rewrite
        "has_archive" => true,
        "supports"=> array("title", "editor,", "excerpt"),
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
        "supports"=> array("title", "editor,"),
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
            "excerpt",
            

        )
        ),
        

    );

}

add_action("init","university_post_types");


?>
