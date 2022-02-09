<?php
get_header();

pageBanner(array(
  "title" => "All Events",
  "subtitle" => "Wild And Crazy Stuff In Our World",
  "photo" =>"https://images.unsplash.com/photo-1644333192098-75573dacbb0c?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1171&q=80"

));



?>

  
    <div class="container container--narrow page-section">
   <?php
    if(have_posts()){
      while(have_posts()){ 
        the_post()?>
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
    echo paginate_links();
    }// end of if stateent
   ?>
   <hr class="section-break">
   <p>Looking for past events?  <a href="<?php echo site_url(('/past-events'))?>">Check out past events archive page</a>.</p>
    </div>
      
<?php
get_footer()
?>