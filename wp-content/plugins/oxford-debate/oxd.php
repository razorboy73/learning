<?php
/**
 *
 * Plugin Name: Oxford Debates Wordpress
 * Plugin URI: http://cws-tech.com
 * Description: The online version of the Oxford-style debates adapte the physical model and makes it possible to expand 
 * the capabilities of both speakers and audience. The speakers may argue using web connectivity and multimedia, 
 * and the audience can also comment fixing its position on the proposals of the speakers or raising their own alternatives.
 * Version: 2.1.3
 * Author: Rafa Fernandez
 * Author URI: http://cws-tech.com
 * Text Domain: oxd
 * Domain Path: /languages/
 *
 **/

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

define( 'OXD_VERSION', '2.1.3' );
define( 'OXD_DIR', plugin_dir_path( __FILE__ ) );
require_once( 'services/odServices.php' );

if (!class_exists("Oxd")) :

class Oxd {
	var $settings, $options_page;
	
	function __construct() {	
		if (is_admin()) {
			// Load example settings page
			if (!class_exists("Oxd_Settings"))
				require(OXD_DIR . 'oxd-settings.php');
			$this->settings = new OxD_Settings();	
		}

		add_action('init', array($this,'init') );
		add_action('admin_init', array($this,'admin_init') );
        
		add_action( 'admin_init', array($this, 'register_oxd_settings') );
        add_action('init', array($this, 'create_debatepost_type') );
        add_action('init', array($this, 'my_taxonomies_debate') );
        add_action('init', array($this, 'create_posturepost_type') );
        add_action('init', array($this, 'my_taxonomies_posture') );
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_styles') );
        add_action('comment_post', array($this, 'save_comment_meta_data') );
        add_action('admin_menu', array($this,'admin_menu') );
        add_action('update_option_key', array($this,'valid_key') );
        add_action('save_post', array($this, 'set_debate') );
        add_action('delete_post', array($this, 'delete_debate') );
        add_action( 'admin_notices', 'oxd_activation_notice' );
        add_filter('get_comment_author_link', array($this, 'attach_posture_to_author') );        
        add_filter('template_include', array($this, 'template_loader') );
        add_filter('comments_template', array($this, 'comments_template_loader') );
        
		register_activation_hook( __FILE__, array($this,'activate') );
		register_deactivation_hook( __FILE__, array($this,'deactivate') );
	}


	function activate($networkwide) {
        set_transient( 'oxd_activation_notice_transient', true, 5 );
	}

	function deactivate($networkwide) {

	}

	/*
		Enter our plugin activation code here.
	*/
	function _activate() {
        add_option('purchased',false);
    }

	/*
		Enter our plugin deactivation code here.
	*/
	function _deactivate() {}

	function init() {
		load_plugin_textdomain( 'oxd', false, 
							   basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	function admin_init() {
	}
    
	function admin_menu() {
	add_options_page( 'Oxford-Style Debate Settings', 'OxD Settings', 'administrator', 'oxd-admin-page', array($this, 'oxd_admin_page'), 'dashicons-admin-generic', 6  );
    }
    
    function register_oxd_settings() {
	//register our settings
    register_setting( 'oxd-registration', 'key' );
    register_setting( 'oxd-settings-group', 'oxd_votes' );
    register_setting( 'oxd-social', 'twitter-option' );
    register_setting( 'oxd-social', 'facebook-option' );
    register_setting( 'oxd-social', 'email-option' );
    register_setting( 'oxd-social', 'linkedin-option' );
    register_setting( 'oxd-social', 'telegram-option' );
    register_setting( 'oxd-social', 'whatsapp-option' );
    register_setting( 'oxd-styles', 'global_posture_colour_a' );
    register_setting( 'oxd-styles', 'global_posture_colour_b' );
    register_setting( 'oxd-styles', 'oxd_colour' );
    register_setting( 'oxd-layout', 'oxd_bootstrap' );
    register_setting( 'oxd-layout', 'oxd_fluid');
   

    }
    
    function valid_key () {
        $service = 'validate_portal';
        $key = get_option('key');
        $data = array(
        "product_key"  => $key
        );

        //Json Encode
        $json_data = json_encode($data);  
        $result_json = service_call($json_data, $service);
        
        $obj = json_decode($result_json);
        
        $check = $obj->check;
        $error = $obj->msg;
        
        if ($check == 'OK') {
            update_option('purchased',true);
            $purchased = get_option('purchased'); 
            $message = __('Your product has been registered. Happy Debating! ','oxd');
			$type = 'updated';
        }
        
        else {
            update_option('purchased',false);
            update_option('key',null);
            $purchased = get_option('purchased');
            $message = __('Something is going wrong. Your product couldn’t be registered. Try again!','oxd');
            $type = 'error';
		
	}
	
        // add_settings_error( $setting, $code, $message, $type )
        add_settings_error('valid_key_notice', 'valid_key_notice', $message, $type);
          
    } 
    
    function set_debate( $ID, $post ) {

        if (get_option('purchased') == true) {

            $service = 'set_debate';
            $post_type = get_post_type($post);

            //die();
            if ($post_type == 'debate') {

            $post_id = $ID;
            $author = get_post_field( 'post_author', $post_id );
            $debate_title = get_post_field( 'post_title', $post_id );
            $description = get_the_excerpt($post_id);
            //$permalink = get_permalink( $ID );
            $posturea = get_post_meta($post_id, 'posturea', true );
            $postureb = get_post_meta($post_id, 'postureb', true );
            $init_date = get_post_meta( $post_id, 'initduration-text', true );
            $end_date = get_post_meta( $post_id, 'endduration-text', true );
            $vote_a = get_post_meta( $post_id, 'votea', true );
            $vote_b = get_post_meta( $post_id, 'voteb', true );
            $key = get_option('key');
            $colorA = get_posture_colour($post_id,'A');
            $colorB = get_posture_colour($post_id,'B');

            // loop trough each posture
            $type = 'posture';
            $args=array(
              'post_type' => $type,
              'post_status' => 'publish',
              'posts_per_page' => -1,
              'ignore_sticky_posts'=> 1
                );
            $my_query = null;
            $my_query = new WP_Query($args);

            if( $my_query->have_posts() ) {
              while ($my_query->have_posts()) : $my_query->the_post();
                $title = get_the_title();

                if ( $posturea == $title ) { 

                    $posturea_user = (string)get_the_author_meta($ID);
                    $posture = get_the_ID();
                    $posturea_title = $posturea;
                    

                }
                if ( $postureb == $title ) { 

                    $postureb_user = (string)get_the_author_meta($ID);
                    $posture = get_the_ID();
                    $postureb_title = $postureb;
                    
                }
                endwhile;
            }

                if (get_post_status( $ID ) == 'publish') {    

                    wp_reset_query();

                    $data = array(
                    "debate_id"  => $ID,
                    "moderator_id"  => $author,
                    "posture_a_user_id"  => $posturea_user,
                    "posture_b_user_id"  => $postureb_user,
                    "posture_a_title"  => $posturea_title,
                    "posture_b_title"  => $postureb_title,
                    "init_date"  => $init_date,
                    "end_date"  => $end_date,
                    "product_key" => $key,
                    "vote_a" => $vote_a,
                    "vote_b" => $vote_b,
                    "color_a" => $colorA,
                    "color_b" => $colorB,
                    "title" => $debate_title,
                    "description" => $description
                    );
                    //Json Encode
                    $json_data = json_encode($data);  
                    $result_json = service_call($json_data, $service);

                    $obj = json_decode($result_json);

                    //print $obj->{'check'}; 
                    //print $obj->{'msg'}; 

                    $check = $obj->{'check'};
                    $error = $obj->{'msg'};

                    if ($check == 'OK') {
                        // Inserted Debate  
                    }

                    else {   
                        return FALSE;
                       

                    }      

                }
            }
        }
       
    }
    
    function delete_debate( $ID, $post ) {
        
        $service = 'delete_debate';
        $post_type = get_post_type($post);
        $key = get_option('key');
          
        //die();
        
        if ($post_type == 'debate') {

                $data = array(
                "product_key" => $key,
                "debate_id"  => $ID
                );

                //Json Encode
                $json_data = json_encode($data);  
                $result_json = service_call($json_data, $service);

                $obj = json_decode($result_json);

                //print $obj->{'check'}; 
                //print $obj->{'msg'}; 

                $check = $obj->{'check'};
                $error = $obj->{'msg'};

                if ($check == 'ok') {

                    // Inserted Debate
                }

                else {

                    //wp_delete_post($ID);
                }             
        }
    }
    
    
    function oxd_admin_page(){
        
        if (isset($_POST['oxd_unregister']) && check_admin_referer('oxd_unregister_clicked')) {
    // Unregister has been pressed
        unregister();
        }
	?>
	

		<div class="wrap">
            <div class="row oxd-admin-row">
                <h2></h2>
                <img class="oxd-admin-logo" src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/oxd-logo.png'; ?>"/>
                <h1><?php _e('Oxford-Style Debate Settings','oxd'); ?></h1>
                <p><?php _e('Oxford-Style Debate is an easy and simple plugin to create debates on your website in four steps:','oxd'); ?></p>
                <ol>
                    <li><?php _e('Create a debate and give a starting date and closing date.','oxd'); ?></li>
                    <li><?php _e('Create two proposals and give your personal touch with media resources or colors.','oxd'); ?></li>
                    <li><?php _e('Go to Debate, link the proposals to the debate and publish it.','oxd'); ?></li>
                    <li><?php _e('Happy debating!','oxd'); ?></li>
                </ol>
                <p><br>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/6cayrkWMzbw" frameborder="0" allowfullscreen></iframe>
                    </p>
                
            </div>
            <div class="row oxd-admin-row">
                <h2><?php _e('Insight Oxford-Style Debate','oxd'); ?></h2>
                <p><?php _e('Now you can enrich the Oxford-Style Debate with an Insight Oxford-Style Debate beta version. This feature will allow you to get more information and display the voting progress on the debate. If you want to try it, email us at ','oxd'); ?><a href="mailto: debate@cyberpractices.org">debate@cyberpractices.org</a></p>
            <form method="post" action="options.php">
                <?php settings_fields( 'oxd-registration' ); ?>
                <?php do_settings_sections( 'oxd-registration' ); ?>
            
                <table class="form-table">
                    <tr valign="top">
                    <td scope="row">
                    <h3><?php _e('Register','oxd'); ?></h3>
                    <p><?php _e('If you have already registered an Insight Oxford-style Debate account, please enter the product key.','oxd'); ?></p>
                    
                        <p><?php _e('Portal key code ','oxd'); ?>
                        <input type="text" name="key" value="<?php if (get_option('purchased') == true) { echo esc_attr( get_option('key') ); } ?>" <?php if (get_option('purchased') == true) {echo 'disabled';} ?> /> </p>
                        
                    </td>
                    </tr>
                </table>
            
            <?php if (get_option('purchased') == false) { 
                    submit_button('Save Changes'); 
                    } ?>
            </form>
            <?php if (get_option('purchased') == true) { ?>
            <form method="post" action="options-general.php?page=oxd-admin-page">
    
                <?php wp_nonce_field('oxd_unregister_clicked');
                ?>
            
            <table class="form-table">
                <tr valign="top">
                    <td scope="row">
                <h3><?php _e('Unregister','oxd'); ?></h3>
                <p><?php _e('If you wish to unregister Insight Oxford-style Debate by removing the product key, press the following button.
','oxd'); ?></p>
                        <input type="hidden" value="true" name="oxd_unregister" />
                    </td>
                </tr>
            </table>
    <?php submit_button(__('Unregister','oxd')); ?>
            </form>
             <?php } ?>
                
        </div>
        <div class="row oxd-admin-row">

		<h3><?php _e('Users votes','oxd'); ?></h3>
        <form method="post" action="options.php">
    <?php settings_fields( 'oxd-settings-group' ); ?>
    <?php do_settings_sections( 'oxd-settings-group' ); ?>
            <table class="form-table">
                
                <tr valign="top">
                    <td scope="row"><?php _e('Only users with an account on the portal can vote:','oxd'); ?></td>
                </tr>
                <tr valign="top">
                <td>
                    <select name="oxd_votes" id="oxd_votes">

                    <?php if (get_option('oxd_votes') == yes) { ?>
                      <option value="yes" selected><?php _e('Yes','oxd'); ?></option>
                      <option value="no"><?php _e('No','oxd'); ?></option>
                    <?php } else { ?>
                      <option value="yes"><?php _e('Yes','oxd'); ?></option>
                      <option value="no" selected><?php _e('No','oxd'); ?></option>

                     <?php } ?>

                    </select>
                </td>

                </tr>
            </table>
    
    <?php submit_button(); ?>

        </form>
        </div>
        <div class="row oxd-admin-row">
            
            <!-- SOCIAL MEDIA -->
            
            <h3><?php _e('Social Media','oxd'); ?></h3>
        <form method="post" action="options.php">
    <?php settings_fields( 'oxd-social' ); ?>
    <?php do_settings_sections( 'oxd-social' ); ?>
            <table class="form-table">
                
                <tr valign="top">
                    <td scope="row"><?php _e('Select where to share debates:','oxd'); ?></td>
                </tr>
                <tr valign="top">
                     <td><input type="checkbox" name="twitter-option"  value="1" <?php checked( get_option('twitter-option'), 1 ); ?>/>Twitter</td>
                </tr>
                <tr valign="top">
                    <td><input type="checkbox" name="facebook-option"  value="1" <?php checked( get_option('facebook-option'), 1 ); ?>/>Facebook</td>
                </tr>
                <tr valign="top">
                    <td><input type="checkbox" name="email-option"  value="1" <?php checked( get_option('email-option'), 1 ); ?>/>E-mail</td>
                </tr>
                <tr valign="top">
                    <td><input type="checkbox" name="linkedin-option"  value="1" <?php checked( get_option('linkedin-option'), 1 ); ?>/>Linkedin</td>
                </tr>
                <tr valign="top">
                    <td><input type="checkbox" name="telegram-option"  value="1" <?php checked( get_option('telegram-option'), 1 ); ?>/>Telegram</td>
                </tr>
                <tr valign="top">
                    <td><input type="checkbox" name="whatsapp-option"  value="1" <?php checked( get_option('whatsapp-option'), 1 ); ?>/>Whatsapp</td>
                </tr>

            </table>
    
    <?php submit_button(); ?>

        </form>
            
	<!-- SOCIAL MEDIA END -->
        </div>
            <div class="row oxd-admin-row">
                <h2><?php _e('Oxford-Style Debate Styles','oxd'); ?></h2>
                <p><?php _e('Customize debates with your own style.','oxd'); ?></p>
            <form method="post" action="options.php">
    <?php settings_fields( 'oxd-styles' ); ?>
    <?php do_settings_sections( 'oxd-styles' ); ?>
            
                <table class="form-table">
                    <tr>
                        <td scope="row">
                            <h3><?php _e('Plugin Colours','oxd'); ?></h3>
                            <p><?php _e('Select the colour for the plugin. It will be displayed at buttons and progress bar.','oxd'); ?></p>
                            <p><input class="color-field" type="text" name="oxd_colour" id="oxd_colour" value="<?php echo get_option('oxd_colour'); ?>" /> </p>
                        
                    </td>
                    </tr>
                    <tr valign="top">
                    <td scope="row">
                    <h3><?php _e('Debate Colours','oxd'); ?></h3>
                    <p><?php _e('Select the colours for each proposal. These colours will affect to all the debates. If you want to change the colour of a specific debate, you can select it from the debate page.','oxd'); ?></p>
                    
                        <p><?php _e('Proposal A Colour:','oxd'); ?></p>
                        <p><input class="color-field" type="text" name="global_posture_colour_a" id="global_posture_colour_a" value="<?php echo get_option('global_posture_colour_a'); ?>" /> </p>
                        <p><?php _e('Proposal B Colour:','oxd'); ?></p>
                        <p><input class="color-field" type="text" name="global_posture_colour_b" id="global_posture_colour_b" value="<?php echo get_option('global_posture_colour_b'); ?>" /> </p>
                        
                    </td>
                    </tr>
                </table>
            
            <?php  
                submit_button('Save Changes'); 
             ?>
            </form>
            
                
        </div>
    
        <div class="row oxd-admin-row">
                <h2><?php _e('Oxford-Style Debate Layout','oxd'); ?></h2>
                
            <form method="post" action="options.php">
                        <?php settings_fields( 'oxd-layout' ); ?>
                        <?php do_settings_sections( 'oxd-layout' ); ?>
                                
                <table class="form-table">
                    
                    
                    <tr valign="top">
                        <td scope="row">
                        <h3><?php _e('Bootstrap grid','oxd'); ?></h3>
                        
                            <p><?php _e('Check option YES, if your theme do not use Bootstrap.','oxd'); ?></p>
                            
 
                        <p><select name="oxd_bootstrap" id="oxd_bootstrap"></p>

                        <?php if (get_option('oxd_bootstrap') == yes) { ?>
                          <option value="yes" selected><?php _e('Yes','oxd'); ?></option>
                          <option value="no"><?php _e('No','oxd'); ?></option>
                        <?php } else { ?>
                          <option value="yes"><?php _e('Yes','oxd'); ?></option>
                          <option value="no" selected><?php _e('No','oxd'); ?></option>

                         <?php } ?>

                        </select>
                        
                    </td>
                </tr>
                
                <tr valign="top">
                        <td scope="row">
                        <h3><?php _e('Container fluid','oxd'); ?></h3>
                        
                            <p><?php _e('If you check option YES, the plugin width will be adapted to your theme’s container.','oxd'); ?></p>

                        <p><select name="oxd_fluid" id="oxd_fluid"></p>

                        <?php if (get_option('oxd_fluid') == yes) { ?>
                          <option value="yes" selected><?php _e('Yes','oxd'); ?></option>
                          <option value="no"><?php _e('No','oxd'); ?></option>
                        <?php } else { ?>
                          <option value="yes"><?php _e('Yes','oxd'); ?></option>
                          <option value="no" selected><?php _e('No','oxd'); ?></option>

                         <?php } ?>

                        </select>
                        
                    </td>
                </tr>
                    
                </table>
            
            <?php  
                submit_button('Save Changes'); 
             ?>
            </form>               
        </div>

        <div class="row oxd-admin-row">
            <h3><?php _e('Shortcode options','oxd'); ?></h3>
            <table class="form-table">
                <tr valign="top">
                    <td scope="row">
                        <p><strong><?php _e('Basic shortcode','oxd'); ?></strong></p>
                        <blockquote>[debates_q]</blockquote>
                    </td>
                </tr>
                <tr valign="top">
                    <td scope="row">
                        <p><strong><?php _e('Open debates shortcode','oxd'); ?></strong></p>
                        <p><?php _e('It lists only the open debates on your page.','oxd'); ?></p>
                        <blockquote>[debates_q type="open"]</blockquote>
                    </td>
                </tr>
                <tr valign="top">
                    <td scope="row">
                        <p><strong><?php _e('Closed debates shortcode','oxd'); ?></strong></p>
                        <p><?php _e('It lists only the closed debates on your page.','oxd'); ?></p>
                        <blockquote>[debates_q type="closed"]</blockquote>
                    </td>
                </tr>
                <tr valign="top">
                    <td scope="row">
                        <p><strong><?php _e('Coming soon debates shortcode','oxd'); ?></strong></p>
                        <p><?php _e('It lists only the debates that are coming soon.','oxd'); ?></p>
                        <blockquote>[debates_q type="soon"]</blockquote>
                    </td>
                </tr>
                <tr valign="top">
                    <td scope="row">
                        <p><strong><?php _e('Number of listed debates','oxd'); ?></strong></p>
                        <blockquote>[debates_q type="x"]</blockquote>
                    </td>
                </tr>
            </table>
            </div>
    </div>
	<?php
        
    }

    // CREATE POSTURE CUSTOM POST TYPE
    
    function create_posturepost_type() {

            $labels = array(
                'name'                => __('Proposals','oxd'),
                'singular_name'       => __('Proposal','oxd'),
                'menu_name'           => __('Proposals','oxd'),
                'all_items'           => __('All Proposals','oxd'),
                'view_item'           => __('View Proposal','oxd'),
                'add_new'             => __('Add Proposal','oxd'),
                'parent_item_colon'   => '',
            );
            $args = array(
                'labels'              => $labels,
                'supports'            => array('title','editor', 'author', 'thumbnail', 'excerpt'),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'rewrite'             => array( 'slug' => 'posture'),
                'menu_position'       => 7,
                'menu_icon'           => plugins_url( '/img/oxd_icon.png', __FILE__ ),
                'taxonomies'          => array( 'post_tag' ),
                'has_archive'         => true,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
                'map_meta_cap'        => true,
                'capabilities' => array(
                            'edit_post' => 'edit_posture',
                            'edit_posts' => 'edit_postures',
                            'edit_others_posts' => 'edit_others_postures',
                            'publish_posts' => 'publish_postures',
                            'read_post' => 'read_debate',
                            'read_private_posts' => 'read_private_postures',
                            'delete_post' => 'delete_posture'
    ),
            );

            register_post_type( 'posture', $args );
            flush_rewrite_rules();
            
    }


    function my_taxonomies_posture() {
        $labels = array(
        'name'              => _x( 'Proposals Categories', 'posture' ),
        'singular_name'     => _x( 'Proposal Category', 'posture' ),
        'search_items'      => __( 'Search Proposal Categories' ),
        'all_items'         => __( 'All Proposal Categories' ),
        'parent_item'       => __( 'Parent Proposal Category' ),
        'parent_item_colon' => __( 'Parent Proposal Category:' ),
        'edit_item'         => __( 'Edit Proposal Category' ), 
        'update_item'       => __( 'Update Proposal Category' ),
        'add_new_item'      => __( 'Add New Proposal Category' ),
        'new_item_name'     => __( 'New Proposal Category' ),
        'menu_name'         => __( 'Proposal Categories' ),
        );
        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'rewrite' => array('slug' => 'posture'),
        );
        register_taxonomy( 'posture_category', 'posture', $args );
    }
    
// CREATE DEBATE CUSTOM POST TYPE
    function create_debatepost_type() {

            $labels = array(
                'name'                => __('Debates','oxd'),
                'singular_name'       => __('Debate','oxd'),
                'menu_name'           => __('Debates','oxd'),
                'all_items'           => __('All Debates','oxd'),
                'view_item'           => __('View Debate','oxd'),
                'add_new'             => __('Add Debate','oxd'),
                'parent_item_colon'   => '',
            );
            $args = array(
                'labels'              => $labels,
                'supports'            => array('title','editor', 'author', 'thumbnail', 'excerpt', 'comments'),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'rewrite'             => array( 'slug' => 'debate'),
                'menu_icon'           => plugins_url( '/img/oxd_icon.png', __FILE__ ),
                'menu_position'       => 7,
                'taxonomies'          => array( 'post_tag' ),
                'has_archive'         => true,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
                'map_meta_cap'        => true,
                'capabilities' => array(
                            'edit_post' => 'edit_debate',
                            'edit_posts' => 'edit_debates',
                            'edit_others_posts' => 'edit_others_debates',
                            'publish_posts' => 'publish_debates',
                            'read_post' => 'read_debate',
                            'read_private_posts' => 'read_private_debates',
                            'delete_post' => 'delete_debate'
    ),
            );

            register_post_type( 'debate', $args );
            flush_rewrite_rules();
            
    }


    function my_taxonomies_debate() {
        $labels = array(
        'name'              => _x( 'Debates Categories', 'debate' ),
        'singular_name'     => _x( 'Debate Category', 'debate' ),
        'search_items'      => __( 'Search Debate Categories' ),
        'all_items'         => __( 'All Debate Categories' ),
        'parent_item'       => __( 'Parent Debate Category' ),
        'parent_item_colon' => __( 'Parent Debate Category:' ),
        'edit_item'         => __( 'Edit Debate Category' ), 
        'update_item'       => __( 'Update Debate Category' ),
        'add_new_item'      => __( 'Add New Debate Category' ),
        'new_item_name'     => __( 'New Debate Category' ),
        'menu_name'         => __( 'Debate Categories' ),
        );
        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'rewrite' => array('slug' => 'debate'),
        );
        register_taxonomy( 'debate_category', 'debate', $args );
    }

    

    function save_comment_meta_data( $comment_id, $ID ) {
        
        $comment = get_comment( $comment_id ); 
        $comment_post_id = $comment->comment_post_ID;
        $post_type = get_post_type($comment_post_id);
        
        if ($post_type == 'debate') {

            if ( isset( $_POST['posture'] ) ) {

                $args=array(
                    'post_type' => 'posture',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'ignore_sticky_posts'=> 1
                );
                $my_query = new WP_Query($args);

                if( $my_query->have_posts() ) {
                        while ($my_query->have_posts()) : $my_query->the_post();
                            $title = html_entity_decode(get_the_title());
                            if ( html_entity_decode($_POST['posture']) == html_entity_decode($title) ) {

                                $posture = get_the_ID();
                                $color = get_post_meta( $posture, 'posture_colour', true );

                            }

                endwhile;
                }

                $args=array(
                    'post_type' => 'debate',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'ignore_sticky_posts'=> 1
                );
                $my_query = new WP_Query($args);

                if( $my_query->have_posts() ) {
                        while ($my_query->have_posts()) : $my_query->the_post();
                            $title = html_entity_decode(get_the_title());
                            if ( html_entity_decode($_POST['posture']) == html_entity_decode(get_post_meta(get_the_ID(),'posturea',true)) ) {

                                $posture_type = 'A';

                            } else if ( html_entity_decode($_POST['posture']) == html_entity_decode(get_post_meta(get_the_ID(),'postureb',true)) ) {

                                $posture_type = 'B';

                            } else {

                                $posture_type = 'OTHER';
                            }

                endwhile;
                }


                add_comment_meta( $comment_id, 'posture', $_POST[ 'posture' ] );
                add_comment_meta( $comment_id, 'color', $color );
                add_comment_meta( $comment_id, 'posture_type', $posture_type );
                
                // Call add_comment service
                add_comment_service($comment_post_id, $comment_id, $posture_type);

            }
        }
    }
 

    function attach_posture_to_author( $author ) {
        
        
        $posture = get_comment_meta( get_comment_ID(), 'posture', true );
        $posture_type = get_comment_meta( get_comment_ID(), 'posture_type', true );
        $color = get_posture_colour (get_comment_ID(),$posture_type);
        
        
        
        if ( $posture ) {
            
            if (($posture_type == 'A') or ($posture_type == 'B')) {
            $author .= '<p class="posture-circle-container" style="color: ' . $color . '!important"><span class="circle-text" ></span></p><p>' . __('For Proposal ','oxd') . $posture_type . '</p>';
            } else {
            $author .= '<p class="posture-circle-container" style="color: ' . $color . '!important"><span class="circle-text" ></span></p><p>' . __('For Alternative Proposal','oxd') . '</p>';
                
            }
        }
            
        return $author;
    }


    function template_loader( $template ) {
    $file = '';
    if ( is_single() && get_post_type() == 'debate' ) {
                            $file   = 'single-debate.php';
                            $find[] = $file;
                            //$find[] = WC()->template_path() . $file;
    }

    if ( is_single() && get_post_type() == 'debate' ) {
                            $file   = 'single-debate.php';
                            $find[] = $file;
                            //$find[] = WC()->template_path() . $file;
    }

    if ( $file ) {
        $template       = locate_template( array_unique( $find ) );
        if ( ! $template  ) {
                   $template = plugin_path() . '/templates/' . $file;
        } 
    }

    return $template;

}

    function comments_template_loader( $template ) {

    if ( get_post_type() !== 'debate' ) {
            return $template;
    }
      
    $check_dirs = array(
            trailingslashit( get_stylesheet_directory() ) . plugin_path(),
            trailingslashit( get_template_directory() ) . plugin_path(),
            trailingslashit( get_stylesheet_directory() ),
            trailingslashit( get_template_directory() ),
            trailingslashit( plugin_path() ) . 'templates/'
    );

                
    foreach ( $check_dirs as $dir ) {
        if ( file_exists( trailingslashit( $dir ) . 'comments-debate.php' ) ) {
                return trailingslashit( $dir ) . 'comments-debate.php';
        }
    }
    }

    
    
    function enqueue_scripts() {
        
        wp_enqueue_script( 'scripts', plugin_dir_url( __FILE__ ) . 'js/scripts.js' , array ( 'jquery' ) );
    }
    
    function enqueue_styles() {  
    
        
	
        wp_register_style( 'register-style', plugin_dir_url( __FILE__ ) . 'css/style.css' );
        wp_register_style( 'register-grid', plugin_dir_url( __FILE__ ) . 'css/debatesGrid.css' );
        
        wp_enqueue_style( 'register-style' );
        wp_enqueue_style( 'wp-color-picker' ); 
        
        if (get_option('oxd_bootstrap') == 'yes') {
            wp_enqueue_style( 'register-grid' );
        } 
       
    }

    

} // end class

endif;


// Initialize our plugin object.
global $oxd;
if (class_exists("Oxd") && !$oxd) {
    $oxd = new Oxd();	
}	


add_shortcode( 'debates_q', 'display_custom_post_type' );

function display_custom_post_type($atts){

    extract( shortcode_atts( array(
        'type' => '',
        'number' => ''

    ), $atts ) );
    
    $debate_colour = get_oxd_colour();

    $args = array(
        'post_type' => 'debate',
        'post_status' => 'publish'
    );
    
    
    
    
    switch( $type ){

                case 'open': 
            
                    $counter = 0;
                    $string = '';
                    $query = new WP_Query( $args );
            
                    if( $query->have_posts() ){
                        $string .= '<ul class="debates-list">';
                        while( $query->have_posts() ){ 
                            
                            if ($number == $counter) {
                                break;
                            }
                            
                            $counter ++;
   
                            $query->the_post();

                            $posturea_title = get_post_meta( get_the_ID(), 'posturea', true );
                            $posturea = get_page_by_title( $posturea_title, OBJECT, 'posture' );
                            $posturea_author = $posturea->post_author; 
                            $authora_obj = get_user_by('id', $posturea_author);
                            $authora_name = $authora_obj->display_name;

                            $postureb_title = get_post_meta( get_the_ID(), 'postureb', true );
                            $postureb = get_page_by_title( $postureb_title, OBJECT, 'posture' );
                            $postureb_author = $postureb->post_author; 
                            $authorb_obj = get_user_by('id', $postureb_author);
                            $authorb_name = $authorb_obj->display_name;
                            
                            $debateId = get_the_ID();
                            $debate_duration = get_debate_duration($debateId);


                            if (!$debate_duration['disabled']) {
                            $string .= '<li class="even_debate_li">
                                              <div class="debate-list-container debates-container">
                                                <p class="shortcode-debate-title">' . get_the_title() . '</p>
                                                <p class="shortcode-debate-excerpt">' . get_the_excerpt() . '</p>


                                                    <div class="col col-sm-6">
                                                    <div class="shortcode-speakera-div">
                                                    <p class="shortcode-speaker-title">Speaker A</p>
                                                    <p>' . $authora_name . '</p>
                                                    </div>
                                                    <div class="shortcode-speakerb-div">
                                                    <p class="shortcode-speaker-title">Speaker B</p>
                                                    <p>' . $authorb_name . '</p>
                                                    </div>
                                                    </div>
                                                    <div class="col col-sm-6 text-right duration-col">
                                                    <div class="shortcode-duration-container">
                                                    <p id="duration-text">&nbsp;' . $debate_duration['current_day'] . ' / ' . $debate_duration['days'] . '&nbsp;' . (__('days','oxd')) . '</p>
                                                    <div id="shortcode-current-percent-container">
                                                    <div id="current-percent" style="width:' . $debate_duration['current_percent'] . '%;background-color:' . $debate_colour . '"></div>
                                                    </div>
                                                    </div>

                                                    <a href="' . get_permalink() . '">
                                                    <button class="oxd-button go-debate-button" style="background-color:' . $debate_colour . '">Go ></button>
                                                    </a>
                                                    </div>

                                              </div>
                                            </li>';
                            } 


                        }
                        $string .= '</ul>';
                    }
                    break;

                case 'closed': 
            
                    $counter = 0;
                    $string = '';
                    $query = new WP_Query( $args );
            
                    if( $query->have_posts() ){
                        $string .= '<ul class="debates-list">';
                        while( $query->have_posts() ){ 
                            
                            if ($number == $counter) {
                                break;
                            }
                            
                            $counter ++;

                            $query->the_post();

                            $posturea_title = get_post_meta( get_the_ID(), 'posturea', true );
                            $posturea = get_page_by_title( $posturea_title, OBJECT, 'posture' );
                            $posturea_author = $posturea->post_author; 
                            $authora_obj = get_user_by('id', $posturea_author);
                            $authora_name = $authora_obj->display_name;

                            $postureb_title = get_post_meta( get_the_ID(), 'postureb', true );
                            $postureb = get_page_by_title( $postureb_title, OBJECT, 'posture' );
                            $postureb_author = $postureb->post_author; 
                            $authorb_obj = get_user_by('id', $postureb_author);
                            $authorb_name = $authorb_obj->display_name;

                            $debateId = get_the_ID();
                            $debate_duration = get_debate_duration($debateId);

                            if ($debate_duration['disabled']) {

                                if ($debate_duration['time_to_close'] < 0){ 
                                    
                                $closed_msg = __('Closed Debate','oxd'); $container_open = '<p class="circle-text" id="duration-text">&nbsp;';
                                $container_close = '&nbsp;</p>';
                                $string .= '<li class="even_debate_li">
                                              <div class="debate-list-container debates-container">
                                                <p class="shortcode-debate-title">' . get_the_title() . '</p>
                                                <p class="shortcode-debate-excerpt">' . get_the_excerpt() . '</p>


                                                    <div class="col col-sm-6">
                                                    <div class="shortcode-speakera-div">
                                                    <p class="shortcode-speaker-title">Speaker A</p>
                                                    <p>' . $authora_name . '</p>
                                                    </div>
                                                    <div class="shortcode-speakerb-div">
                                                    <p class="shortcode-speaker-title">Speaker B</p>
                                                    <p>' . $authorb_name . '</p>
                                                    </div>
                                                    </div>
                                                    <div class="col col-sm-6 text-right duration-col">
                                                    <div class="shortcode-duration-container">'
                                                    . $container_open . $closed_msg . $container_close .                                    
                                                    '</div>

                                                    <a href="' . get_permalink() . '">
                                                    <button class="oxd-button go-debate-button" style="background-color:' . $debate_colour . '">Go ></button>
                                                    </a>
                                                    </div>

                                              </div>
                                            </li>';
                                }

                            }


                        }
                        $string .= '</ul>';
                    }
                    break;
            
                case 'soon': 
            
                    $counter = 0;
                    $string = '';
                    $query = new WP_Query( $args );
            
                    if( $query->have_posts() ){
                        $string .= '<ul class="debates-list">';
                        while( $query->have_posts() ){ 
                            
                            if ($number == $counter) {
                                break;
                            }
                            
                            $counter ++;

                            $query->the_post();

                            $posturea_title = get_post_meta( get_the_ID(), 'posturea', true );
                            $posturea = get_page_by_title( $posturea_title, OBJECT, 'posture' );
                            $posturea_author = $posturea->post_author; 
                            $authora_obj = get_user_by('id', $posturea_author);
                            $authora_name = $authora_obj->display_name;

                            $postureb_title = get_post_meta( get_the_ID(), 'postureb', true );
                            $postureb = get_page_by_title( $postureb_title, OBJECT, 'posture' );
                            $postureb_author = $postureb->post_author; 
                            $authorb_obj = get_user_by('id', $postureb_author);
                            $authorb_name = $authorb_obj->display_name;

                            $debateId = get_the_ID();
                            $debate_duration = get_debate_duration($debateId);


                            if ($debate_duration['disabled']) {

                                if ($debate_duration['time_to_close'] >= 0){ $closed_msg = __('Days to begin: ','oxd') . $debate_duration['days_to_begin']; $container_open = '<p id="duration-text">&nbsp;';
                                                        
                                $container_close = '&nbsp;</p>';
                                $string .= '<li class="even_debate_li">
                                              <div class="debate-list-container debates-container">
                                                <p class="shortcode-debate-title">' . get_the_title() . '</p>
                                                <p class="shortcode-debate-excerpt">' . get_the_excerpt() . '</p>


                                                    <div class="col col-sm-6">
                                                    <div class="shortcode-speakera-div">
                                                    <p class="shortcode-speaker-title">Speaker A</p>
                                                    <p>' . $authora_name . '</p>
                                                    </div>
                                                    <div class="shortcode-speakerb-div">
                                                    <p class="shortcode-speaker-title">Speaker B</p>
                                                    <p>' . $authorb_name . '</p>
                                                    </div>
                                                    </div>
                                                    <div class="col col-sm-6 text-right duration-col">
                                                    <div class="shortcode-duration-container">'
                                                    . $container_open . $closed_msg . $container_close .                                    
                                                    '</div>

                                                    <a href="' . get_permalink() . '">
                                                    <button class="oxd-button go-debate-button" style="background-color:' . $debate_colour . '">Go ></button>
                                                    </a>
                                                    </div>

                                              </div>
                                            </li>';     
                                } 
                                
                                
                                
                                
                                

                            }


                        }
                        $string .= '</ul>';
                    }
                    break;
                    
                default:
            
                    $counter = 0;
                    $string = '';
                    $query = new WP_Query( $args );
            
                    if( $query->have_posts() ){
                        $string .= '<ul class="debates-list">';
                        while( $query->have_posts() ){ 
                            
                            if ($number == $counter) {
                                break;
                            }
                            
                            $counter ++;

                            $query->the_post();

                            $posturea_title = get_post_meta( get_the_ID(), 'posturea', true );
                            $posturea = get_page_by_title( $posturea_title, OBJECT, 'posture' );
                            $posturea_author = $posturea->post_author; 
                            $authora_obj = get_user_by('id', $posturea_author);
                            $authora_name = $authora_obj->display_name;

                            $postureb_title = get_post_meta( get_the_ID(), 'postureb', true );
                            $postureb = get_page_by_title( $postureb_title, OBJECT, 'posture' );
                            $postureb_author = $postureb->post_author; 
                            $authorb_obj = get_user_by('id', $postureb_author);
                            $authorb_name = $authorb_obj->display_name;

                            $debateId = get_the_ID();
                            $debate_duration = get_debate_duration($debateId);

                            if (!$debate_duration['disabled']) {
                            $string .= '<li class="even_debate_li">
                                              <div class="debate-list-container debates-container">
                                                <p class="shortcode-debate-title">' . get_the_title() . '</p>
                                                <p class="shortcode-debate-excerpt">' . get_the_excerpt() . '</p>


                                                    <div class="col col-sm-6">
                                                    <div class="shortcode-speakera-div">
                                                    <p class="shortcode-speaker-title">Speaker A</p>
                                                    <p>' . $authora_name . '</p>
                                                    </div>
                                                    <div class="shortcode-speakerb-div">
                                                    <p class="shortcode-speaker-title">Speaker B</p>
                                                    <p>' . $authorb_name . '</p>
                                                    </div>
                                                    </div>
                                                    <div class="col col-sm-6 text-right duration-col">
                                                    <div class="shortcode-duration-container">
                                                    <p id="duration-text">&nbsp;' . $debate_duration['current_day'] . ' / ' . $debate_duration['days'] . '&nbsp;' . (__('days','oxd')) . '</p>
                                                    <div id="shortcode-current-percent-container">
                                                    <div id="current-percent" style="width:' . $debate_duration['current_percent'] . '%;background-color:' . $debate_colour . '"></div>
                                                    </div>
                                                    </div>

                                                    <a href="' . get_permalink() . '">
                                                    <button class="oxd-button go-debate-button" style="background-color:' . $debate_colour . '">Go ></button>
                                                    </a>
                                                    </div>

                                              </div>
                                            </li>';
                            } else {

                                if ($debate_duration['time_to_close'] >= 0){ $closed_msg = __('Days to begin: ','oxd') . $debate_duration['days_to_begin']; $container_open = '<p id="duration-text">&nbsp;';} else { $closed_msg = __('Closed Debate','oxd'); $container_open = '<p class="circle-text" id="duration-text">&nbsp;';}
                                $container_close = '&nbsp;</p>';
                                $string .= '<li class="even_debate_li">
                                              <div class="debate-list-container debates-container">
                                                <p class="shortcode-debate-title">' . get_the_title() . '</p>
                                                <p class="shortcode-debate-excerpt">' . get_the_excerpt() . '</p>


                                                    <div class="col col-sm-6">
                                                    <div class="shortcode-speakera-div">
                                                    <p class="shortcode-speaker-title">Speaker A</p>
                                                    <p>' . $authora_name . '</p>
                                                    </div>
                                                    <div class="shortcode-speakerb-div">
                                                    <p class="shortcode-speaker-title">Speaker B</p>
                                                    <p>' . $authorb_name . '</p>
                                                    </div>
                                                    </div>
                                                    <div class="col col-sm-6 text-right duration-col">
                                                    <div class="shortcode-duration-container">'
                                                    . $container_open . $closed_msg . $container_close .                                    
                                                    '</div>

                                                    <a href="' . get_permalink() . '">
                                                    <button class="oxd-button go-debate-button" style="background-color:' . $debate_colour. '">Go ></button>
                                                    </a>
                                                    </div>

                                              </div>
                                            </li>';

                            }


                        }
                        $string .= '</ul>';
                    }
                    break;
                }
    wp_reset_query();
    return $string;
}

function oxd_threaded_comments(){
if (!is_admin()) {
     if (is_singular() && comments_open() && (get_option('thread_comments') == 1))
          wp_enqueue_script('comment-reply');
     }
}

add_action('get_header', 'oxd_threaded_comments');


/**
    * Get the plugin url.
    * @return string
*/
function plugin_url() {
    return untrailingslashit( plugins_url( '/', __FILE__ ) );
}
/**
    * Get the plugin path.
    * @return string
*/
function plugin_path() {
    return untrailingslashit( plugin_dir_path( __FILE__ ) );
}

// Remove Featured Image Metabox from Custom Post Type Edit Screens
function remove_image_box() {
   remove_meta_box('postimagediv','debate','side');
   remove_meta_box('postimagediv','posture','side');

}
add_action('add_meta_boxes', 'remove_image_box');

    function add_theme_caps() {
    // administrator
    $administrator = get_role( 'administrator' );
        
    $administrator->add_cap( 'edit_debates' );
    $administrator->add_cap( 'edit_debate' );
    $administrator->add_cap( 'publish_debates' ); 
    $administrator->add_cap( 'delete_debate' );
    $administrator->add_cap( 'read_debate' );
    $administrator->add_cap( 'edit_others_debates' ); 
    $administrator->add_cap( 'read_private_debates' );
        
    $administrator->add_cap( 'edit_postures' );
    $administrator->add_cap( 'edit_posture' );
    $administrator->add_cap( 'publish_postures' ); 
    $administrator->add_cap( 'delete_posture' );
    $administrator->add_cap( 'read_posture' );
    $administrator->add_cap( 'edit_others_postures' );
    $administrator->add_cap( 'read_private_postures' );
// editors
    $editor = get_role( 'editor' );
        
    $editor->add_cap( 'edit_debates' );
    $editor->add_cap( 'edit_debate' );
    $editor->add_cap( 'publish_debates' ); 
    $editor->add_cap( 'delete_debate' );
    $editor->add_cap( 'read_debate' );
    $editor->add_cap( 'edit_others_debates' ); 
    $editor->add_cap( 'read_private_debates' );
        
    $editor->add_cap( 'edit_postures' );
    $editor->add_cap( 'edit_posture' );
    $editor->add_cap( 'publish_postures' ); 
    $editor->add_cap( 'delete_posture' );
    $editor->add_cap( 'read_posture' );
    $editor->add_cap( 'edit_others_postures' );
    $editor->add_cap( 'read_private_postures' );

    // authors
    $authors = get_role( 'author' );
        
    $authors->add_cap( 'edit_debates' );
    $authors->add_cap( 'edit_debate' );
    $authors->add_cap( 'publish_debates' ); 
    $authors->remove_cap( 'delete_debate' );
    $authors->remove_cap( 'read_debate' );
    $authors->remove_cap( 'edit_others_debates' ); 
    $authors->remove_cap( 'read_private_debates' );
        
    $authors->add_cap( 'edit_postures' );
    $authors->add_cap( 'edit_posture' );
    $authors->add_cap( 'publish_postures' ); 
    $authors->remove_cap( 'delete_posture' );
    $authors->remove_cap( 'read_posture' );
    $authors->remove_cap( 'edit_others_postures' );
    $authors->remove_cap( 'read_private_postures' );
         
    }
add_action( 'admin_init', 'add_theme_caps' );

//Facebook OG parameters

function custom_post_header() {
    global $post;
    if ((get_post_type($post) == 'debate') or (get_post_type($post) == 'posture')) {
    
    if  (is_single() ) {
?>
  <meta property="og:url" content="<?php echo get_permalink( $post->ID ) ?>" />
  <meta property="og:title" content="<?php echo $post->post_title; ?>" />
  <meta property="og:description" content="<?php echo get_post_field('post_excerpt', $post->ID); ?>" />
  <meta property="og:image" content="<?php echo plugins_url( '/img/square-logo.png', __FILE__ ); ?>" />
<?php
    }
        
    }
}
add_action('wp_head', 'custom_post_header');

//Add debates and postures tags

function add_oxd_custom_post_types( $query ) {
    if( is_tag() && $query->is_main_query() ) {

        // gets all post types:
        $post_types = get_post_types();

        

        $query->set( 'post_type', $post_types );
    }
}
add_filter( 'pre_get_posts', 'add_oxd_custom_post_types' );

//Unregister 

function unregister() {

        $service = 'unregister_portal';
        $key = get_option('key');
        $data = array(
        "product_key"  => $key
        );

        //Json Encode
        $json_data = json_encode($data);  
        $result_json = service_call($json_data, $service);
        
        $obj = json_decode($result_json);
        
        $check = $obj->check;
        $error = $obj->msg;
        
        if ($check == 'OK') {
            update_option('purchased',false);
            update_option('key','');  
        }
        
        else {
            print('Unable to unregister the portal, please try again later.');
            return FALSE;
        }  
}

//Activation message

function oxd_activation_notice(){
 
    /* Check transient, if available display notice */
    if( get_transient( 'oxd_activation_notice_transient' ) ){
        ?>
        <div class="updated notice is-dismissible">
            <p><?php echo __('Oxford-Style Debate is a plugin about debating with quite a lot enriched features. Do you want to have a quick tour?','oxd');?> <a href="https://www.youtube.com/watch?v=6cayrkWMzbw" target="_blank"><?php echo __('Yes','oxd');?></a> | <a href="options-general.php?page=oxd-admin-page"><?php echo __('No thanks, I will set myself','oxd'); ?></a></p>
        </div>
        <?php
        /* Delete transient, only display this notice once. */
        delete_transient( 'oxd_activation_notice_transient' );
    }
}

function get_posture_colour ($debateId,$posture) {
    
    if ($posture == 'A') {
        if (get_post_meta( $debateId, 'posture_colour_a', true ) != '' ) {
            $color = get_post_meta( $debateId, 'posture_colour_a', true );
        } else {
            $color = get_option('global_posture_colour_a');
        }
        
        if ($color == '') {
            $color = '#ffd300';
        }
    }
    if ($posture == 'B') {
        if (get_post_meta( $debateId, 'posture_colour_b', true ) != '' ) {
            $color = get_post_meta( $debateId, 'posture_colour_b', true );
        } else {
            $color = get_option('global_posture_colour_b');
        }
        
        if ($color == '') {
            $color = '#79eef3';
        }
    }
    
    
    return $color;
    
}

function get_oxd_colour() {
    
    $debateColour = get_option('oxd_colour');
    
    if ($debateColour == '') {
        $debateColour = '#ffd300';
    }
    
    return $debateColour;
    
}


function get_debate_duration($debateId) {
    
    $ts1 = (strtotime(substr(get_post_meta( get_the_ID(), 'initduration-text', true ), 0 , -6))); 
    $ts2 = (strtotime(substr(get_post_meta( get_the_ID(), 'endduration-text', true ), 0, -6)));
    $diff = $ts2 - $ts1;
    $time_end = (strtotime(get_post_meta( get_the_ID(), 'endduration-text', true )));
    $time_to_close = $time_end - strtotime(date("Y-m-d H:i"));
    $disabled = false;

    // $days = Debate duration
    $days = floor($diff / (60 * 60 * 24)) + 1;

    // $current_day = Current day of the debate
    $date = date('Y-m-d');
    $current_time = strtotime($date) - (strtotime(substr(get_post_meta( get_the_ID(), 'initduration-text', true ), 0, -6)));
    $current_day = floor($current_time / (60 * 60 * 24)) + 1;

    // $days_to_begin = Days until the debate begins
    $time_to_begin = (strtotime(substr(get_post_meta( get_the_ID(), 'initduration-text', true ), 0, -6))) - strtotime($date);
    $days_to_begin = floor($time_to_begin / (60 * 60 * 24));

    // $current_percent = Current completed percent of total days in the debate
    $current_percent = (($current_day) * 100) / ($days);

    // Debate is disabled until the right hour
    $times1 = (strtotime(get_post_meta( get_the_ID(), 'initduration-text', true )));
    $today = strtotime("now");

    if ((($today - $times1) < 0) or ($current_day > $days) or ($time_to_close <= 0)) {
        $disabled = true;  
    }     

    $duration_array = array(
            "time_to_close" => $time_to_close,//
            "days" => $days,//
            "current_day" => $current_day,//
            "days_to_begin" => $days_to_begin,//
            "current_percent" => $current_percent,//
            "disabled" => $disabled
        );

    return $duration_array;
    
}

function get_oxd_social() {
    
    $social_networks = array (
    
        array (
            'network'   => 'twitter',
            'active'    => get_option('twitter-option'),
            ),
        array (
            'network'   => 'facebook',
            'active'    => get_option('facebook-option')
            ),
        array (
            'network'   => 'email',
            'active'    => get_option('email-option')
            ),
        array (
            'network'   => 'linkedin',
            'active'    => get_option('linkedin-option')
            ),
        array (
            'network'   => 'telegram',
            'active'    => get_option('telegram-option')
            ),
        array (
            'network'   => 'whatsapp',
            'active'    => get_option('whatsapp-option')
            )
    
    );

    $network_array = array();
    for ($x = 0; $x <= 5; $x++) {
        //If the social network is marked
        if ($social_networks[$x]['active'] == 1) {
            array_splice($network_array, $x, 0, $social_networks[$x]['network']);
         }   
    }

    
    $social_result = array(
            "social_networks" => $social_networks,
            "network_array" => $network_array
        );
    
    return $social_result;
    
}

function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}

function add_visit( $country, $country_code, $ID ) {
        
        if (get_option('purchased') == true) {
            $service = 'add_visit';
            //die();
            $post_id = $ID;
            $key = get_option('key');

            if (get_post_status( $ID ) == 'publish') {    

                $data = array(
                "debate_id"  => $ID,
                "product_key" => $key,
                "country" => $country,
                "country_code" => $country_code,

                );
                //Json Encode
                $json_data = json_encode($data);  
                $result_json = service_call($json_data, $service);

                $obj = json_decode($result_json);

                //print $obj->{'check'}; 
                //print $obj->{'msg'}; 

                $check = $obj->{'check'};
                $error = $obj->{'msg'};

                if ($check == 'OK') {
                    // Inserted Debate
                }

                else { 
                    return FALSE;

                }      

            }

        }
    }

function add_comment_service($comment_post_id, $comment_id, $posture_type) {
    
    if (get_option('purchased') == true) {
        
        $service = 'add_comment';
        //die();

        $key = get_option('key');    

        $data = array(
        "debate_id"  => $comment_post_id,
        "product_key" => $key,
        "comment_id" => $comment_id,
        "posture" => $posture_type,

        );
        //Json Encode
        $json_data = json_encode($data);  
        $result_json = service_call($json_data, $service);

        $obj = json_decode($result_json);

        //print $obj->{'check'}; 
        //print $obj->{'msg'}; 

        $check = $obj->{'check'};
        $error = $obj->{'msg'};

        if ($check == 'OK') {
            // Inserted Debate
        }

        else {
            return FALSE;

        }      
    }  
}

?>