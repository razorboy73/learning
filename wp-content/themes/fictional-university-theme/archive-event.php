<?php
get_header();

pageBanner(array(
  "title" => "All Events",
  "subtitle" => "Wild And Crazy Stuff In Our World",
  "photo" =>"https://images.unsplash.com/photo-1644333192098-75573dacbb0c?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1171&q=80"

));



?>

  
    <div class="container container--narrow page-section">
   <?php
    if(have_posts()){
      while(have_posts()){ 
        the_post();
        get_template_part("/template-parts/content", get_post_type());
      }// end of while loop
    echo paginate_links();
    }// end of if stateent
   ?>
   <hr class="section-break">
   <p>Looking for past events?  <a href="<?php echo site_url(('/past-events'))?>">Check out past events archive page</a>.</p>
    </div>
      
<?php
get_footer()
?>