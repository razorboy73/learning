<?php

require get_theme_file_path("/includes/search-route.php"); 

function university_custom_rest(){
  register_rest_field("post", "authorName", array(
    "get_callback" => function (){return get_the_author();}
  ));
  register_rest_field("note", "userNoteCount", array(
    "get_callback" => function (){return count_user_posts(get_current_user_id(),"note");}
  ));
}

add_action("rest_api_init", "university_custom_rest");


function pageBanner($args = NULL) {
    if (isset($args['title'])) {
      $args['title'] = $args['title'];
    } else {
      $args['title'] = get_the_title();
    }
    if (isset($args['subtitle'])) {
      $args['subtitle'] = $args['subtitle'];
    } else {
      $args['subtitle'] = get_field('page_banner_subtitle');
    }
    if (isset($args['photo'])) {
      $args['photo'] = $args['photo'];
    } else {
      if (get_field('page_banner_background_image')) {
        $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
      } else {
        $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
      }
    }
  ?>
    <div class="page-banner">
      <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>);"></div>
      <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
        <div class="page-banner__intro">
          <p><?php echo $args['subtitle']; ?></p>
        </div>
      </div>  
    </div>
  <?php }






function university_files(){
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', includes_url( '/js/jquery/jquery.js' ), false, NULL, true );
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script('googleMap', "//maps.googleapis.com/maps/api/js?key=AIzaSyCanSBlOFWxNbdr3dtMpFHMHwDHRxr_wYk", NULL, '1.0', true);
    wp_enqueue_style("custom-google-fonts", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i");
    wp_enqueue_style("university_main_styles", get_stylesheet_directory_uri().'/build/style-index.css');
    wp_enqueue_style("university_extra_styles", get_stylesheet_directory_uri().'/build/index.css');
    wp_enqueue_style("font-awesome", get_template_directory_uri().'/fontawesome/css/all.min.css');
    wp_enqueue_script("font-awesome-js", get_template_directory_uri().'/fontawesome/js/all.min.js',array(), false, true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_localize_script( 'main-university-js', "universityData", array(
      "root_url"=>get_site_url(),
      "nonce" => wp_create_nonce("wp_rest")


    ) );
   
}



add_action("wp_enqueue_scripts", "university_files");



function university_features(){
    add_theme_support("title-tag");
    add_theme_support("post-thumbnails");
    add_image_size("professorLandscape", 400, 260, true);
    add_image_size("professorPortrait", 480, 650, true);
    add_image_size("pageBanner",1500, 350, true);
    //added a menu code name and user friendly name
    register_nav_menus(array("headerMenuLocation" => __("Silly Header Menu"), 
                            'footerLocation1' => __( 'Footer 1 menu' ),
                            'footerLocation2' => __( 'Footer 2 menu' ),
                            "main-nav" => __("Main Nav Tutorial")
                        ));
}
add_action("after_setup_theme", 'university_features');


function university_adjust_queries($query){

    if(!is_admin() AND is_post_type_archive( "campus" ) AND $query->is_main_query()){
       
        $query->set("posts_per_page", -1);
       
        

    }

    if(!is_admin() AND is_post_type_archive( "program" ) AND $query->is_main_query()){
      $query->set("orderby", "title");
      $query->set("order", "ASC");
      $query->set("posts_per_page", -1);
     
      

  }

    if(!is_admin() AND is_post_type_archive( "event" ) AND $query->is_main_query()){
    $today = date("Y-m-d");
    $query->set("meta_key", "event_date");
    $query->set("orderby", "meta_value_num");
    $query->set("order", "ASC");
    $query->set("meta_query", array(
        array(
          "key"=>"event_date",
          "compare" => ">=",
          "value"=> $today,
          "type" => 'DATE'
        )
        ));
    }
}

add_action("pre_get_posts", "university_adjust_queries");

function universityMapKey($api){
    $api['key'] = 'AIzaSyCanSBlOFWxNbdr3dtMpFHMHwDHRxr_wYk';
    return $api;

}


add_filter("acf/fields/google_map/api", "universityMapKey");



//redirect subscriber to home pageBanner




add_action("admin_init","redirectSubsToFrontend");

function redirectSubsToFrontend(){

  $ourCurrentUser = wp_get_current_user();

  if(count($ourCurrentUser->roles)==1 AND $ourCurrentUser->roles[0]=="subscriber"){

    wp_redirect(site_url("/"));
    exit;
  }
}

add_action("wp_loaded","noSubsAdminBar");

function noSubsAdminBar(){

  $ourCurrentUser = wp_get_current_user();

  if(count($ourCurrentUser->roles)==1 AND $ourCurrentUser->roles[0]=="subscriber"){
   show_admin_bar(false);
  }
}


//Customize Login Screen

add_filter("login_headerurl", "ourHeaderUrl");


function ourHeaderURL(){
  
  return esc_url(site_url("/"));
}

add_action("login_enqueue_scripts", "ourLoginCSS");


function ourLoginCSS(){

  wp_enqueue_style("custom-google-fonts", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i");
  wp_enqueue_style("university_main_styles", get_template_directory_uri().'/build/style-index.css');
  wp_enqueue_style("university_extra_styles", get_template_directory_uri().'/build/index.css');
  wp_enqueue_style("font-awesome", get_template_directory_uri().'/fontawesome/css/all.min.css');
 
}


add_filter("login_headertext","ourLoginTitle");

function ourLoginTitle(){
  return get_bloginfo("name");
}

//force note posts to be private

add_filter("wp_insert_post_data","makeNotePrivate", 20, 2);

function makeNotePrivate($data, $postarr){

  if($data['post_type']=="note"){
    $data['post_content'] = sanitize_textarea_field($data['post_content']);

    if(count_user_posts(get_current_user_id(), "note")>4 AND !$postarr["ID"]){
      die("You have hit your note limit");
    }
    $data['post_title'] = sanitize_text_field($data['post_title']);
  }

  if($data['post_type']== "note" AND $data["post_status"] != "trash"){
      $data["post_status"] = "private";
      
    }
  return $data;
}


// Only run if wpshout_filter_example() does not already exist
if ( ! function_exists( 'wpshout_filter_example' ) ) {
	function wpshout_filter_example( $title ) {
		return 'Hooked: '.$title;
	}
	add_filter( 'the_title', 'wpshout_filter_example' );	
}



/*let's put a widget in the footer */

function footer_widget(){

  register_sidebar(array(
    'name' => 'Footer Widget',
    'id' => 'footer_widget',
    'description' => "Our Widgetized Footer",
    'class' => '',
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
    'after_widget'  => "</li>\n",
    'before_title'  => '<h2 class="widgettitle">',
    'after_title'   => "</h2>",

  ));


  
}
add_action("widget_init","footer_widget")


?>
