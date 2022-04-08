<?php
/*
Template Name: Author Link Page
Template Post Type: post, page, product
 */

get_header(); 
pageBanner(array(
    //   "title" => get_the_title(),
    //   "subtitle" => get_the_archive_description(),
    //   "photo" =>"https://images.unsplash.com/photo-1644333192098-75573dacbb0c?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1171&q=80"
    
    ));?>

<?php while(have_posts()){
    the_post(); ?>
    

			
			// END CUSTOM BIT!
    <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
                    <p>
                    <a class="metabox__blog-home-link" href="<?php echo site_url("/blog"); ?>">
                    <i class="fa fa-home" aria-hidden="true"></i> Blog Home:</a> 
                    <span class="metabox__main">
                    Posted by <?php the_author_posts_link(); ?> on <?php the_time('F jS, Y'); ?> in <?php the_category( ', ' ); ?>
      
                    </span>
                    </p>
                </div>
             
    
    <div class="generic-content"><?php the_content(); ?></div>
    <div>
            <!-- custom bit -->
			<div class="author-more-link">
                <?php echo get_the_author_meta( 'ID' ); ?>
                <?php  echo get_author_posts_url(1);?>
				<a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>">
					More by our author, <?php the_author(); ?>
				</a>
			</div>
        </div>
    </div> 


    
   
    
<?php
        }


get_footer()

?>


