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
                    <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link("event"); ?>">
                    <i class="fa fa-home" aria-hidden="true"></i> Events Home:</a> 
                    <span class="metabox__main">
                     <?php the_title(); ?>
      
                    </span>
                    </p>
                </div>
             
    
    <div class="generic-content"><?php the_content(); ?></div>
 
   <?php

   $relatedPrograms = get_field(("related_programs"));
    if($relatedPrograms){
        echo "<hr class='section-break'>";
            echo "<h2 class='headline headline--medium'>Related Programs(s)</h2>";
            //print_r($relatedPrograms);
            echo "<ul class ='link-list min-list'>";
            foreach($relatedPrograms as $program){ ?>
            <li> <a href="<?php  echo get_the_permalink($program); ?>"><?php echo get_the_title($program); ?></a></li>
            <?php }
            echo "</ul>";
        }       

   ?>
  
    </div> 


    
   
    
<?php
        }


get_footer()

?>


