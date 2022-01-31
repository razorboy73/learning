<?php
get_header("", array( 'name' => 'Josh Kerbel', 'age' => 48 ));
while(have_posts()){
    the_post(); ?>
    <h2><?php echo the_title(); ?></h2>
    <?php the_content(); ?>
   
    
<?php
        }


get_footer()

?>


