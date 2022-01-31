<?php get_header("", array( 'name' => 'Josh Kerbel', 'age' => 48 )); ?>

<?php

while(have_posts()){
    the_post(); ?>
    <h2><a href ="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></h2>
    <?php the_content(); ?>
    <hr>
    
<?php
        }

?>


<?php get_footer(); ?>

