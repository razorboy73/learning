<?php
get_header(); 
pageBanner(array(
    //   "title" => get_the_title(),
    //   "subtitle" => get_the_archive_description(),
    //   "photo" =>"https://images.unsplash.com/photo-1644333192098-75573dacbb0c?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1171&q=80"
    
    ));?>

<?php while(have_posts()){
    the_post(); ?>
   
    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p>
                    <!--use get_post_type_archive_link rather than site_url("/amazing-events") -->
                <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link("campus"); ?>">
                <i class="fa fa-home" aria-hidden="true"></i> All Campuses</a> 
                <span class="metabox__main">
                <?php the_title(); ?>
      
                    </span>
                    </p>
        </div>
             
    
            <div class="generic-content"><?php the_content(); ?></div>
            <?php  $mapLocation = get_field("map_location");
           ?>
            <div class="acf-map">
             
                <div class="marker" data-lat="<?php echo $mapLocation["lat"]; ?>" data-lng="<?php echo $mapLocation["lng"] ?>">
                    <h3><<?php the_title(); ?></h3>
                    <?php echo $mapLocation['address'] ?>
                </div>
        </div>
        <?php

    
            //has to match return value in ACF
            $relatedPrograms = new WP_Query(array(
            "posts_per_page" => -11,
            "post_type" => 'program',
            //https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters
            "orderby" => "title",
            'order' => "ASC",
            //https://developer.wordpress.org/reference/classes/wp_meta_query/#user-contributed-notes
            "meta_query" => array(
            
            array(
                "key" => 'related_campus',
                "compare"=> "LIKE",
                "value" => '"'.get_the_ID().'"'
            )
            )
            ));

            if($relatedPrograms->have_posts()){
            echo "<hr class='section-break'>";
            echo "<h2 class='headline headline--medium'> Programs At This Campus</h2>";
        
            echo "<ul class='min-list link-list'>";
            
                while($relatedPrograms->have_posts()){
                        $relatedPrograms->the_post(); ?>
                        <li>
                            <a  href="<?php the_permalink() ?>">
                                <?php the_title()?>
                            </a>
                        </li>
                        
                
            
            <?php }
            echo "</ul>";
            }
            wp_reset_postdata();
            
            $today = date("Y-m-d");
            //has to match return value in ACF
            $homePageEvents = new WP_Query(array(
            "posts_per_page" => 2,
            "post_type" => 'event',
            "meta_key"=> "event_date",
            //https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters
            "orderby" => "meta_value_num",
            'order' => "ASC",
            //https://developer.wordpress.org/reference/classes/wp_meta_query/#user-contributed-notes
            "meta_query" => array(
              array(
                "key"=>"event_date",
                "compare" => ">=",
                "value"=> $today,
                "type" => 'DATE'
              ),
              array(
                  "key" => 'related_programs',
                  "compare"=> "LIKE",
                  "value" => '"'.get_the_ID().'"'
              )
            )
          ));

          if($homePageEvents->have_posts()){
            echo "<hr class='section-break'>";
            echo "<h2 class='headline headline--medium'> Upcoming ". get_the_title()." Events</h2>";
                while($homePageEvents->have_posts()){
                    $homePageEvents->the_post(); 
                    get_template_part("/template-parts/content", get_post_type());
                }
                ?>
            <?php
             }?>
   </div> 
    
<?php
        }


get_footer()

?>


