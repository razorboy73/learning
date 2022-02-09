<?php
get_header();
pageBanner(array(
  "title" => "Campus Locations",
  "subtitle" => "No matter where you live, there we are",
  "photo" =>"https://images.unsplash.com/photo-1644333192098-75573dacbb0c?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1171&q=80"

));
?>

  
    <div class="container container--narrow page-section">

        <div class="acf-map">
            <?php
            if(have_posts()){
                while(have_posts()){ 
                the_post();
                $mapLocation = get_field("map_location");
                ?>
                <div class="marker" data-lat="<?php echo $mapLocation["lat"]; ?>" data-long="<?php echo $mapLocation["lng"] ?>"></div>
        

        
        
                <?php }// end of while loop
                echo paginate_links();
            }// end of if stateent
            ?>
        </div>
  </div>
<?php
get_footer()
?>