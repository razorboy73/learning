<?php
/**
 *
 * Oxford Debates Wordpress
 * File: Settings
 *
 **/

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Oxd_Settings")) :

	class Oxd_Settings {

		function __construct() {
			
			add_action( 'add_meta_boxes', array($this,'oxd_meta_box') );
			add_action( 'admin_print_styles', array($this,'register_admin_styles') );
			add_action( 'admin_enqueue_scripts', array($this,'register_admin_scripts') );
			add_action( 'save_post', array($this,'oxd_meta_save'), 10, 2 );
            add_action( 'save_post', array($this,'oxd_posture_meta_save') );
            add_action('admin_notices', array($this,'invalid_date_notice'),0);
            add_action('admin_notices', array($this,'invalid_postures'),0);
            
			
		}
        function invalid_date_notice(){
            //print the message
            global $post;
            $notice = get_option('invalid_date');
            if (empty($notice)) return '';
            foreach($notice as $pid => $m){
                if ($post->ID == $pid ){
                    echo '<div id="message" class="error"><p>'.$m.'</p></div>';

                    unset($notice[$pid]);
                    update_option('invalid_date',$notice);
                    break;
                }
            }
        }
        
        function invalid_postures(){
            //print the message
            global $post;
            $notice = get_option('invalid_postures');
            if (empty($notice)) return '';
            foreach($notice as $pid => $m){
                if ($post->ID == $pid ){
                    echo '<div id="message" class="error"><p>'.$m.'</p></div>';

                    unset($notice[$pid]);
                    update_option('invalid_postures',$notice);
                    break;
                }
            }
        }
	

		function register_admin_styles() {
		
			wp_enqueue_style( 'jquery-ui-datepicker', plugin_dir_url( __FILE__ ) . 'assets/datetimepicker-master/jquery.datetimepicker.css' );
            wp_enqueue_style( 'admin-styles', plugin_dir_url( __FILE__ ) . 'css/admin-styles.css' );
            wp_enqueue_style( 'grid', plugin_dir_url( __FILE__ ) . 'css/debatesGrid.css' );

		}

		function register_admin_scripts() {
		    wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'admin-js', plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'wp-color-picker' ), false, true );
			wp_enqueue_script( 'wp-jquery-date-picker', plugin_dir_url( __FILE__ ) . 'assets/datetimepicker-master/build/jquery.datetimepicker.full.min.js' );
			
		} 
			
		function oxd_meta_box()
		{
		    $args = array('test', array('some data', 'in here'), 3);
            
		    add_meta_box(
		        'moderator_box',
		        __('Debate Details', 'debate'),
		        array($this,'oxd_display_meta_box'),
		        'debate',
		        'advanced',
		        'default'
		    );
            
		}
        
        
		function oxd_display_meta_box($post) {
			wp_nonce_field( basename( __FILE__ ), 'oxd_nonce' );
		    $prfx_stored_meta = get_post_meta( $post->ID );
		    ?>

		    <table class="form-table oxd-admin-table">
			<tbody>
				<tr>
				</tr>
				
				<tr>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="initduration-text"><?php _e( 'Start Date:', 'oxd' )?></label>
					</th>
					<td>
						<input type="text" id="initduration-text" name="initduration-text" value="<?php if ( isset ( $prfx_stored_meta['initduration-text'] ) ) echo $prfx_stored_meta['initduration-text'][0]; ?>" />
						<p class="description"></p>
					</td>
				</tr>
				<tr>
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="endduration-text"><?php _e( 'End Date:', 'oxd' )?></label>
					</th>
					<td>
						<input type="text" id="endduration-text" name="endduration-text" value="<?php if ( isset ( $prfx_stored_meta['endduration-text'] ) ) echo $prfx_stored_meta['endduration-text'][0]; ?>" />
						<p class="description"></p>
					</td>
				</tr>
				<tr>
				</tr>

		    	<?php
		    	global $post;
				$custom = get_post_custom($post->ID);

				// prepare arguments
				$user_args  = array(
				// search only for Authors role
				//'role' => 'Author',
				// order results by display_name
				'orderby' => 'display_name'
				);
				// Create the WP_User_Query object
				$wp_user_query = new WP_User_Query($user_args);
				// Get the results
				$authors = $wp_user_query->get_results();
				// Check for results

				if (!empty($authors))
				{

				?>

				<tr valign="top">
					<th scope="row">
						<label for="posturea"><?php _e( 'Proposal A:', 'oxd' )?></label>
					</th>
					<td>
						
                        <!-- choose posture -->
                        <select name="posturea">
                            <option value="no-posture-selected"><?php _e('Select a Proposal','oxd') ?></option>
						<?php
						// loop trough each posture
                        $type = 'posture';
                        $args=array(
                          'post_type' => $type,
                          'post_status' => 'publish',
                          'posts_per_page' => -1,
                          'caller_get_posts'=> 1
                            );
                        $my_query = null;
                        $my_query = new WP_Query($args);
                        if( $my_query->have_posts() ) {
                          while ($my_query->have_posts()) : $my_query->the_post();
                            $title = get_the_title();
                            ?>
                            
                            
                            <?php if (( isset ( $prfx_stored_meta['posturea'] ) ) and ( $prfx_stored_meta['posturea'][0] == $title )) { ?>
                            
                            
                            <option value="<?php the_title(); ?>"  selected="selected"><?php the_title(); ?></option>
                            <?php
                            }
                            else 
                            { ?>
                                
                                <option value="<?php the_title(); ?>"><?php the_title(); ?></option>
                            <?php
                            }
                            endwhile;
                        }
                        wp_reset_query();
			    	    echo "</select>";
                        ?>
                        <!-- choose posture end -->
                            
                        <p class="description"><?php _e( 'Select Proposal A', 'oxd' )?></p>
					</td>
                    <td>
                        <input class="color-field" type="text" name="posture_colour_a" id="posture_colour_a" value="<?php if ( isset ( $prfx_stored_meta['posture_colour_a'] ) ) echo $prfx_stored_meta['posture_colour_a'][0]; ?>" />
                        <p class="description"><?php _e( 'Select a different colour for this proposal', 'oxd' )?></p>
					</td>
				</tr>
                
				<tr>
				</tr>
                
				<tr valign="top">
                    
					<th scope="row">
						<label for="postureb"><?php _e( 'Proposal B:', 'oxd' )?></label>
					</th>
					<td>
                        
						<!-- choose posture -->
                        <select name="postureb">
                            <option value="no-posture-selected"><?php _e('Select a Proposal','oxd') ?></option>
						<?php
						// loop trough each posture
                        
                        $type = 'posture';
                        $args=array(
                          'post_type' => $type,
                          'post_status' => 'publish',
                          'posts_per_page' => -1,
                          'caller_get_posts'=> 1
                            );
                        $my_query = null;
                        $my_query = new WP_Query($args);
                        if( $my_query->have_posts() ) {
                          while ($my_query->have_posts()) : $my_query->the_post();
                            $title = get_the_title();
                            ?>
                            
                            
                            <?php if (( isset ( $prfx_stored_meta['postureb'] ) ) and ( $prfx_stored_meta['postureb'][0] == $title )) { ?>
                            
                            
                            <option value="<?php the_title(); ?>" selected="selected"><?php the_title(); ?></option>
                            <?php
                            }
                            else 
                            { ?>
                                
                                <option value="<?php the_title(); ?>"><?php the_title(); ?></option>
                            <?php
                            }
                            endwhile;
                        }
                        wp_reset_query();
			    	    echo "</select>";
                        ?>
                        <!-- choose posture end -->
                        <p class="description"><?php _e( 'Select Proposal B', 'oxd' )?></p>
                        <td>
                            <input class="color-field" type="text" name="posture_colour_b" id="posture_colour_b" value="<?php if ( isset ( $prfx_stored_meta['posture_colour_b'] ) ) echo $prfx_stored_meta['posture_colour_b'][0]; ?>" />
                            <p class="description"><?php _e( 'Select a different colour for this proposal', 'oxd' )?></p>
                        </td>   
						<p class="description"></p>
					</td>
				</tr>
				<tr>
				</tr>

			    <?php
				} else {
			    	echo _e( 'No authors found', 'oxd' );
				}
				?>
				
			</tbody>
			</table>
			<?php
		}


		function oxd_meta_save( $post_id ) {
            
            $init_duration_text = isset($_POST[ 'initduration-text' ]) ? $_POST[ 'initduration-text' ] : '';
            $end_duration_text = isset($_POST[ 'endduration-text' ]) ? $_POST[ 'endduration-text' ] : '';
            $posturea = isset($_POST[ 'posturea' ]) ? $_POST[ 'posturea' ] : '';
            $postureb = isset($_POST[ 'postureb' ]) ? $_POST[ 'postureb' ] : '';
            $posture_colour_a = isset($_POST[ "posture_colour_a" ]) ? $_POST[ "posture_colour_a" ] : '';
            $posture_colour_b = isset($_POST[ "posture_colour_b" ]) ? $_POST[ "posture_colour_b" ] : '';
            $error = false;
		    // Checks save status
		    $is_autosave = wp_is_post_autosave( $post_id );
		    $is_revision = wp_is_post_revision( $post_id );
		    $is_valid_nonce = ( isset( $_POST[ 'oxd_nonce' ] ) && wp_verify_nonce( $_POST[ 'oxd_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
		 
		    // Exits script depending on save status
		    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
		        return;
		    }
		 
		    // Checks for input and sanitizes/saves if needed
		  
            if ((sanitize_text_field( $init_duration_text )) > (sanitize_text_field( $end_duration_text ))) { 
                // INVALID DATE
                $notice = get_option('invalid_date');
                $notice[$post_id] = __('Start Date cannot be later than End Date.','oxd');
                update_option('invalid_date',$notice);
                return;
            } 
            if ((sanitize_text_field( $postureb ) == 'no-posture-selected') or (sanitize_text_field( $posturea ) == 'no-posture-selected')) { 
                // INVALID DATE
                $notice = get_option('invalid_postures');
                $notice[$post_id] = __('You must assign both proposals before publishing. Now, your debate status is "Draft".','oxd');
                if (get_post_status($post_id) == 'publish') {
                    
                    $post = array( 'ID' => $post_id, 'post_status' => 'draft' );
                    wp_update_post($post);
                    
                }
                update_option('invalid_postures',$notice);
                
                return;
            } 
            
            $initduration = sanitize_text_field( $init_duration_text );
            $endduration = sanitize_text_field( $end_duration_text );
            
            if (!empty($initduration)) {
            update_post_meta( $post_id, 'initduration-text', sanitize_text_field( $_POST[ 'initduration-text' ] ) );
            } 
            else {
                update_post_meta( $post_id, 'initduration-text', sanitize_text_field( date("Y-m-d H:i") ) );
            }
            if (!empty($endduration)) {
            update_post_meta( $post_id, 'endduration-text', sanitize_text_field( $_POST[ 'endduration-text' ] ) );
            }
            else {
                update_post_meta( $post_id, 'endduration-text', sanitize_text_field( date("Y-m-d H:i") ) );
            }

            
            // Checks for input and sanitizes/saves if needed
            update_post_meta( $post_id, "posturea", $posturea);
            update_post_meta( $post_id, "postureb", $postureb);
            update_post_meta( $post_id, "posture_colour_a", sanitize_text_field( $posture_colour_a ) );
            update_post_meta( $post_id, "posture_colour_b", sanitize_text_field( $posture_colour_b ) );
        
		}
         
        function oxd_posture_meta_save( $post_id ) {

		    // Checks save status
		    $is_autosave = wp_is_post_autosave( $post_id );
		    $is_revision = wp_is_post_revision( $post_id );
		    $is_valid_nonce = ( isset( $_POST[ 'oxd_nonce' ] ) && wp_verify_nonce( $_POST[ 'oxd_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
		 
		    // Exits script depending on save status
		    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
		        return;
		    }

		}
		
	}

endif;
?>