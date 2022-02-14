<?php
get_header();
pageBanner(array(
  "title" => "Search Results",
  "subtitle" => "You Searched For &ldquo; ".esc_html(get_search_query( )). " &rdquo;",
  "photo" =>"https://images.unsplash.com/photo-1644333192098-75573dacbb0c?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1171&q=80"

));
?>
   <!-- <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/ocean.jpg')?>);"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php wp_title(NULL) ?></h1>
            <div class="page-banner__intro">
            <p>Keep Up With The News</p>
            </div>
        </div>
    </div> -->
  
    <div class="container container--narrow page-section">
   <?php
   
        if(have_posts()){
            while(have_posts()){ 
                the_post();
                get_template_part("template-parts/content", get_post_type());
               
            }// end of while loop
            echo paginate_links();
            }else{
                echo "<h2 class ='headline headline--small-plus'> No results match that query </h2>";
            }

        get_search_form()
    ?>
    </div>
      
<?php
get_footer()
?>