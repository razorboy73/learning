<?php
get_header();
pageBanner(array(
  "title" => "All Programs...Even Your Momma",
  "subtitle" => "Something for everyone",
  "photo" =>"https://images.unsplash.com/photo-1644333192098-75573dacbb0c?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1171&q=80"

));
?>

  
    <div class="container container--narrow page-section">

    <ul class= "link-list min-list">
   <?php
    if(have_posts()){
      while(have_posts()){ 
        the_post()?>
      
      <li><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></li>

    
       
     <?php }// end of while loop
    echo paginate_links();
    }// end of if stateent
   ?>
  </div>
<?php
get_footer()
?>