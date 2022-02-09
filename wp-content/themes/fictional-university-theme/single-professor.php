<?php
get_header(); ?>

<?php while(have_posts()){
    the_post(); 
    pageBanner(array(
        "title" => "hello, this is the title",
        "subtitle" => "this is the sub title",
        "photo" =>"https://images.unsplash.com/photo-1644333192098-75573dacbb0c?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1171&q=80"

    ));?>
    </div>
    <div class="container container--narrow page-section">
 
    
    <div class="generic-content">
        <div class="row group">
            <div class="one-third">
                <?php the_post_thumbnail("professorPortrait"); ?>
            </div>
            <div class="two-third">
                <?php the_content(); ?>
            </div>
        </div>

 
   <?php

   $relatedPrograms = get_field(("related_programs"));
    if($relatedPrograms){
        echo "<hr class='section-break'>";
            echo "<h2 class='headline headline--medium'>Subject(s) Taught</h2>";
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


