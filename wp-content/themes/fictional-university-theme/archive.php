<?php
get_header();
pageBanner(array(
  "title" => get_the_archive_title(),
  "subtitle" => get_the_archive_description(),
  "photo" =>"https://images.unsplash.com/photo-1644333192098-75573dacbb0c?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1171&q=80"

));
?>

  
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
    echo paginate_links();
    }// end of if stateent
   ?>
    </div>
      
<?php
get_footer()
?>