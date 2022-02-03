<?php
get_header();

while(have_posts()){
    the_post(); ?>
    
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/ocean.jpg')?>);"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php the_title() ?></h1>
            <div class="page-banner__intro">
            <p>Change This Stuff.</p>
            </div>
        </div>
    </div>

        <div class="container container--narrow page-section">
            <?php 
                $theParentPost = wp_get_post_parent_id(get_the_ID());
                //Returns 0 if we are on the parent post
            if ($theParentPost){ ?>

                <div class="metabox metabox--position-up metabox--with-home-link">
                    <p>
                    <a class="metabox__blog-home-link" href="<?php echo get_permalink($theParentPost); ?>">
                    <i class="fa fa-home" aria-hidden="true"></i>Back To: <?php echo get_the_title($theParentPost);?></a> 
                    <span class="metabox__main"><?php the_title(); ?></span>
                    </p>
                </div>
             
          <?php  }
            
            
            ?>

       
        <?php 
        //Test to see if a page has childeren - indicates it is a parrent
        $isAParent = get_pages(array(
            "child_of" => get_the_ID()
        ));
        
        
        //Want the menu to display if a page is a childpage or parent page
        if($theParentPost or $isAParent){ ?>
            <div class="page-links">
                <h2 class="page-links__title"><a href="<?php echo get_permalink($theParentPost); ?>
                "><?php echo get_the_title($theParentPost);?></a></h2>
                <ul class="min-list">
            <?php
            //Figure out if we are on a parent or child post
            if($theParentPost){
                //if a child page, the above returns true
                $findChildrenOf = $theParentPost;
            }else{
                $findChildrenOf = get_the_ID();
            }




                wp_list_pages(array(
                    "title_li" => NULL,
                    "child_of" => $findChildrenOf,
                    "sort_column" => "menu_order"
                ));
            ?>
                </ul>
            </div> 
            <?php } ?>


        <div class="generic-content">
            <?php the_content()?>
        </div>
    </div>
   
    
<?php
        }
        get_footer()
?>


