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
                    <a class="metabox__blog-home-link" href="<?php echo site_url("/blog"); ?>">
                    <i class="fa fa-home" aria-hidden="true"></i> Blog Home:</a> 
                    <span class="metabox__main">
                    Posted by <?php the_author_posts_link(); ?> on <?php the_time('F jS, Y'); ?> in <?php the_category( ', ' ); ?>
      
                    </span>
                    </p>
                </div>
             
    
    <div class="generic-content"><?php the_content(); ?></div>
    </div> 
    <!-- Environment: We're in a theme template file -->

    <div class="post-taxonomies">
    <!-- thumbs is the name of the taxonomy" -->
	//We gave this a <?php the_terms( get_the_ID(), 'thumbs' ); ?>

    
    </div>

    <?php

/* Environment: This could be any PHP file */

// Define a class
class Chair {
    public $color;
    private $origin_factory;

    public function __construct($color = "orange", $origin_factory = "Cleveland"){
        $this->color = $color;
        $this-> origin_factory = $origin_factory;

    }
    
}

// Instantiate an object of the class
$default_orange_chair = new Chair;
echo $default_orange_chair->color;
?>
    
   
    
<?php
        }


get_footer()

?>


