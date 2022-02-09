<?php
get_header();
pageBanner(array(
  "title" => get_the_title(),
  "subtitle" => get_the_archive_description(),
  "photo" =>"https://images.unsplash.com/photo-1644333192098-75573dacbb0c?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1171&q=80"

));
?>

  
    <div class="container container--narrow page-section">
   <?php
        $today = date("Y-m-d");
            //has to match return value in ACF
        $pastEvents = new WP_Query(array(
            "paged" => get_query_var("paged", 1),
            "post_type" => 'event',
            "meta_key"=> "event_date",
            //https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters
            "orderby" => "meta_value_num",
            'order' => "ASC",
            //https://developer.wordpress.org/reference/classes/wp_meta_query/#user-contributed-notes
            "meta_query" => array(
              array(
                "key"=>"event_date",
                "compare" => "<",
                "value"=> $today,
                "type" => 'DATE'
              )
            )
          ));

    
      while($pastEvents->have_posts()){ 
        $pastEvents->the_post();
        get_template_part("/template-parts/content", get_post_type());
      
      }// end of while loop
    echo paginate_links(array(
        "total" => $pastEvents->max_num_pages
    )

    );
    // end of if stateent
   ?>
    </div>
      
<?php
get_footer()
?>