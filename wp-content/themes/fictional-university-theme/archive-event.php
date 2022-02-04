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

          else{
              the_archive_title(); ?>

            <div class="page-banner__intro">
            <p><?php the_archive_description();?> </p>
            </div>

              
          <?php }


            ?>


           
        </div>
        </div>
  
    <div class="container container--narrow page-section">
   <?php
    if(have_posts()){
      while(have_posts()){ 
        the_post()?>
        <div class="event-summary">
            <a class="event-summary__date t-center" href="<?php the_permalink()?>">
            <span class="event-summary__month"><?php the_date("M");?></span>
              <span class="event-summary__day"><?php the_time("d");?></span>
            </a>
            <div class="event-summary__content">
            <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink()?>"><?php the_title(); ?></a></h5>
              <p><?php echo wp_trim_words(get_the_content(),18);?>  <a href="<?php the_permalink()?>" class="nu gray">Read more</a></p>
          </div>
          </div>

      <!-- <div class="post-item"> -->
         <!-- <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2> -->
        <!-- <div class="metabox"> -->
          <!-- <p>Posted by <?php the_author_posts_link(); ?> on <?php the_time('F jS, Y'); ?> in <?php the_category( ', ' ); ?></p> -->
        <!-- </div> -->
        <!-- <div class="generic-content"> -->
          <!-- <?php the_excerpt(); ?> -->
          <!-- <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">Read More</a></p> -->
        <!-- </div> -->
      <!-- </div> -->
       
     <?php }// end of while loop
    echo paginate_links();
    }// end of if stateent
   ?>
    </div>
      
<?php
get_footer()
?>