<?php
get_header()
?>
   <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/ocean.jpg')?>);"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php wp_title(NULL) ?></h1>
            <div class="page-banner__intro">
            <p>Keep Up With The News</p>
            </div>
        </div>
        </div>
  
    <div class="container container--narrow page-section">
   <?php
    if(have_posts()){
      while(have_posts()){ 
        the_post()?>

      <div class="post-item">
        <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2>
        <div class="metabox">
          <p>Posted by <?php the_author_posts_link(); ?> on <?php the_time('F jS, Y'); ?> in <?php the_category( ', ' ); ?></p>
        </div>
        <div class="generic-content">
          <?php the_excerpt(); ?>
          <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">Read More</a></p>
        </div>
      
      
      
      
      </div>
       
     <?php }// end of while loop

    }// end of if stateent
   ?>
    </div>
      
<?php
get_footer()
?>