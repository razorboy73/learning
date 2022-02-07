<?php
get_header()
?>
   <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/ocean.jpg')?>);"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title">
            <?php
            if (is_category( )){
                 single_cat_title();
            ?>
            </h1>
            <div class="page-banner__intro">
            <p><?php echo category_description();?> </p>
            </div>

            <?php }

            elseif(is_author()){
            echo "Posts by ";   the_author();

            ?>
            </h1>
            <div class="page-banner__intro">
            <p><?php echo get_the_author_meta("description");?> </p>
            </div>

          <?php  }

          elseif(is_archive()){
              the_archive_title(); ?>
            </h1>
            <div class="page-banner__intro">
            <p><?php the_archive_description();?> </p>
            </div>

              
          <?php }

          else{
            the_title(); ?>
          </h1>
            <div class="page-banner__intro">
            <p>A Recap Of Past Events </p>
            </div>

              
          <?php }


            ?>

           
        </div>
        </div>
  
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
        $pastEvents->the_post()?>
        <div class="event-summary">
        <a class="event-summary__date t-center" href="<?php the_permalink()?>">
            <span class="event-summary__month"><?php 
            
            $eventDate = new DateTime(get_field('event_date'));
            
            echo $eventDate->format("M");
            
            
            
            
            ?></span>
              <span class="event-summary__day">
              <?php 
            
                $eventDate = new DateTime(get_field('event_date'));
            
                echo $eventDate->format("j");
            
            
            
            
            ?>
              </span>
            </a>
            <div class="event-summary__content">
            <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink()?>"><?php the_title(); ?></a></h5>
              <p><?php echo wp_trim_words(get_the_content(),18);?>  <a href="<?php the_permalink()?>" class="nu gray">Read more</a></p>
          </div>
          </div>

    
       
     <?php }// end of while loop
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