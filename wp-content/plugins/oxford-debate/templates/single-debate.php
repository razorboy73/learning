<?php 
global $url;
$url = home_url();
global $comments;
global $votes;
global $posture;
global $plugin_purchased;
global $votea;
global $voteb;
global $disabled;
global $registered_only;
global $colorA;
global $colorB;
$disabled = false;
$votea = false;
$voteb = false;
$plugin_purchased = get_option('purchased');
$registered_only = get_option('oxd_votes');
$fluid = get_option('oxd_fluid');
$debateId = get_the_ID();
$colorA = get_posture_colour($debateId,'A');
$colorB = get_posture_colour($debateId,'B');

// SOCIAL NETWORKS
$social = get_oxd_social();
$social_networks = $social['social_networks'];
$network_array  = $social['network_array'];

// CHECK IF ALREADY VOTED

$cookie = ""; 
//isset()
$cookie = isset($_COOKIE['oxd-voted']) ? $_COOKIE['oxd-voted'] : '';
//empty()
$cookie = !empty($_COOKIE['oxd-voted']) ? $_COOKIE['oxd-voted'] : '';

$session_cookie = ""; 
//isset()
$session_cookie = isset($_COOKIE['oxd-session-' . $debateId]) ? $_COOKIE['oxd-session' . $debateId] : '';
//empty()
$session_cookie = !empty($_COOKIE['oxd-session' . $debateId]) ? $_COOKIE['oxd-session' . $debateId] : '';

if ($session_cookie == $debateId) {
    // ALREADY VISITED THE PAGE
    $new_visit = FALSE;
} else {
    // NEW VISIT TO THE PAGE
    $new_visit = TRUE;
    // ADD VISIT 
    add_visit( ip_info("Visitor", "Country"), ip_info("Visitor", "Country Code"), $debateId );
    setcookie('oxd-session' . $debateId,$debateId,time() + 86400,'/');
}

if ($cookie == $debateId . 'a') {
    $votea = true;
} else if ($cookie == $debateId . 'b') {
    $voteb = true;
} else if (isset($_REQUEST['vote'])){
    if ($_REQUEST['vote']=='a'){
        $vote=get_post_meta( $debateId, 'votea', true )+1;
        update_post_meta( $debateId, "votea", $vote);
        setcookie('oxd-voted',$debateId . $_REQUEST['vote'],time() + 86400,'/');
        $votea = true;
    }
    else if ($_REQUEST['vote']=='b'){
        $vote=get_post_meta( $debateId, 'voteb', true )+1;
        update_post_meta( $debateId, "voteb", $vote);
        setcookie('oxd-voted',$debateId . $_REQUEST['vote'],time() + 86400,'/');
        $voteb = true;
    }
}
?>
<?php
/**
* Template Name: Debate Page
*
* 
*/
get_header();

while ( have_posts() ) : the_post(); 
    $post = get_post(get_the_ID());
    $debate_id = $post->ID;
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
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
    $current_posturea = html_entity_decode(get_post_meta( get_the_ID(), 'posturea', true ));
    $current_postureb = html_entity_decode(get_post_meta( get_the_ID(), 'postureb', true ));

    if( $my_query->have_posts() ) {
        while ($my_query->have_posts()) : $my_query->the_post();
            $title = html_entity_decode(get_the_title());
            if ( html_entity_decode($current_posturea) == html_entity_decode($title) ) {
                $posturea = get_the_content(__('More','oxd'));
                $authora = get_the_author_meta('ID');
            }
            if ( html_entity_decode($current_postureb) == html_entity_decode($title) ) {
                $postureb = get_the_content(__('More','oxd'));
                $authorb = get_the_author_meta('ID');
            }
endwhile;
}
wp_reset_query();
$usera = get_userdata($authora);
$userb = get_userdata($authorb);
?>
<input type="hidden" name="debate-id" id="debate-id" value="<?php echo $debate_id; ?>"/>
<input type="hidden" name="user-id" id="user-id" value="<?php echo $user_id; ?>"/>
<input type="hidden" name="vote-process-url" id="vote-process-url" value="<?php echo plugins_url( '../vote-process.php' , __FILE__ ); ?>"/>
<input type="hidden" name="vote-update-url" id="vote-update-url" value="<?php echo plugins_url( '../vote-update.php' , __FILE__ ); ?>"/>

  <div id="debate-header" class="container<?php if ($fluid == 'yes') { echo '-fluid'; } ?> debates-container">
      <div class="row">
    
<?php 
          
$debate_duration = get_debate_duration($debateId);
$debateColour = get_oxd_colour();

          
if (!$debate_duration['disabled']) { ?>
    <div id="duration-container">
      <p id="duration-text">
        <span>
          <?php _e('Duration:','oxd'); ?>
        </span>&nbsp;
        <?php echo $debate_duration['current_day']; ?> / 
        <?php echo $debate_duration['days']; ?>&nbsp;
        <?php _e('days','oxd'); ?>
      </p>
      <div id="current-percent-container">
        <div id="current-percent" style="width:<?php echo $debate_duration['current_percent']; ?>%; background-color: <?php echo $debateColour; ?>">  
        </div>
      </div>
    </div>
    <?php } else { ?>
      <div id="duration-container">

          <?php if ($debate_duration['time_to_close'] >= 0){  ?>

      <p id="duration-text">
          <span>
           <?php _e('Days to begin: ','oxd');echo $debate_duration['days_to_begin']; ?>
          </span>&nbsp; 
      </p>
          <?php } else { ?>
      <p class="circle-text" id="duration-text">
          <span>
        <?php _e('Closed Debate','oxd'); ?>
          </span>
      </p>
           <?php } ?>
    </div>

      <?php } ?>
    </div>
    </div>
  <div id="debate-section" class="container<?php if ($fluid == 'yes') { echo '-fluid'; } ?> debates-container">
      <div class="row">
        <h1>
          <?php echo $post->post_title; ?>
        </h1>
<div id="under-title">
 <!-- Debates Social Sharing -->

<div id="social-share">
    
<?php 

    $debate_share = array (
    
        'twitter'   => 'http://twitter.com/share?url=' . get_permalink( $post->ID ) . '&text=' . $post->post_title,
        'facebook'   => 'http://www.facebook.com/sharer/sharer.php?u=' . get_permalink( $post->ID ),
        'email'   => 'mailto:?subject=' . $post->post_title . ' &body=' . __('You may be interested in the debate: ','oxd') . $post->post_title . __(' at ','oxd') . get_permalink( $post->ID ),
        'linkedin'   => 'http://www.linkedin.com/shareArticle?url=' . get_permalink( $post->ID ) . '&title=' . $post->post_title,
        'telegram'   => 'https://telegram.me/share/url?url=' . get_permalink( $post->ID ) . '&text=' . $post->post_title,
        'whatsapp'   => 'whatsapp://send?text=' . $post->post_title . ' ' . get_permalink( $post->ID ) . '" data-action="share/whatsapp/share"'
    );

    if (sizeof($network_array) > 0) {
        ?>
        <p id="share-title"><?php _e('Share debate on ','oxd'); ?></p>
        <?php
        if (sizeof($network_array) <= 2) {
            //First two social networks
            ?>
            <ul class="primary-social-list">
            <?php
            for ($y = 0; $y <= sizeof($network_array) - 1; $y++) { 
                ?>
                
                <a href="<?php echo $debate_share[(string)$network_array[$y]]; ?>" target="_blank">
                    <li id="li-<?php echo (string)$network_array[$y]; ?>"></li>
                </a>
           <?php } ?>
            </ul>
   
            <?php
            } else {
            ?>
            <ul class="primary-social-list">
            <?php
            //If there is more than 2 networks
            for ($y = 0; $y <= 1; $y++) { 
            ?>
                <a href="<?php echo $debate_share[(string)$network_array[$y]]; ?>" target="_blank">
                    <li id="li-<?php echo (string)$network_array[$y]; ?>"></li>
                </a>
            <?php } ?>
                <li id="show-social-share">
                <!-- More button -->
                </li>
            </ul>
        </div>
        <div id="social-share-hidden">
            <ul class="secondary-social-list">
                <?php
            
            for ($y = 2; $y <= sizeof($network_array) - 1; $y++) { 
            ?> 
                <a href="<?php echo $debate_share[(string)$network_array[$y]]; ?>" target="_blank">
                    <li id="li-<?php echo (string)$network_array[$y]; ?>"></li>
                </a>
        <?php } ?>
            </ul>
        </div>
        <?php
            }
        }
        //end Debates Social Sharing
        ?>
        <p id="debate-tags"># 
          <?php the_tags( ' ', ', ', '<br />' ); ?>
        </p>
    </div>
    </div>
        <div class="row">
        <?php if (($plugin_purchased) and ($debate_duration['time_to_close'] >= 0)) { ?>
        <div id="response-container">
        </div>
        <?php } ?>
        <p id="author_name">
          <?php the_author_meta( 'first_name' ); ?> 
          <?php the_author_meta( 'last_name' ); ?>
        </p>
        <p id="author_description">
          <?php the_author_meta( 'user_description' ); ?>
        </p>
        <hr class="debates-hr">
        <p>
          <?php echo $post->post_content;?>
        </p>
       </div> 
     
  </div>
  <div id="postures-section"  class="container<?php if ($fluid == 'yes') { echo '-fluid'; } ?> debates-container">
    <!-- content -->
      <div class="row">
    
      
        <div class="postures-container col col-sm-6" id="postures-container-a">
          <div id="postures-title-container-a">
            <div id="postures-title-a" style="background-color: <?php echo $colorA; ?>">
              <p>
                <span>
                  <?php _e('Proposal A','oxd'); ?>: 
                </span>
                <?php echo get_post_meta( get_the_ID(), 'posturea', true );?>
              </p>
            </div>
          </div>
          <div id="postures-content-a">
            <div class="speaker-info-div">
              <?php 
              echo get_avatar( $authora, 160 );
                ?>
              <p class="speaker-name">
                <?php echo $usera->first_name . ' ' . $usera->last_name; ?>
              </p>
              <p class="speaker-description">
                <?php echo $usera->description; ?>
              </p> 
            </div>
            <div>
              <p>
                <?php echo $posturea; ?>
              </p>
            </div>  
          </div>
          <!-- vote a -->
          <div class="postures-vote" id="postures-vote-a">
            
              <?php if (!$plugin_purchased) { echo get_post_meta( get_the_ID(), 'votea', true).'&nbsp;'._e('Votes ','oxd');} ?>&nbsp;
              <?php 
              if (!$disabled) {
                    if (($registered_only == 'yes') and (!is_user_logged_in())) { 
                        ?>
                        <button class="oxd-tooltip" id="vote-button-a" disabled>
                        <?php  _e('Vote A','oxd') ?><span class="oxd-tooltiptext"><?php  _e('You must be registered to participate in the debate.','oxd') ?></span>
                        </button>
                        <?php } else { 
                        if ($plugin_purchased) { ?>
                        <button id="vote-button-a" <?php if ($votea) { ?> disabled> <?php _e('Voted','oxd'); } else if ($voteb) { ?> disabled> <?php _e('Vote A','oxd'); ?> 
                        <?php } else {  ?>
                        > <!-- Close <button> opening tag -->
                        <?php _e('Vote A','oxd'); ?>
                        </button>
                        <?php 
                        } } else { ?>
                        <a href="?p=<?php the_ID(); ?>&vote=a">
                            <button id="vote-a" <?php if ($votea) { ?> disabled> </a> <?php _e('Voted','oxd'); } else if ($voteb) { ?> disabled> </a> <?php _e('Vote A','oxd'); ?>                      
                        <?php } else {  ?>   
                        > <!-- Close <button> opening tag -->
                        <?php _e('Vote A','oxd'); ?>
                        </button>
                        </a>
                        <?php } 
                        } 
                        $votes_a = get_post_meta( get_the_ID(), 'votea', true );
                        } }
?>
              <input type="hidden" name="a-votes" id="a-votes" value="<?php echo $votes_a; ?>"/>
            
          </div>
        </div>
        <div class="postures-container col col-sm-6" id="postures-container-b">
          <div id="postures-title-container-b">
            <div id="postures-title-b" style="background-color: <?php echo $colorB; ?>">
              <p>
                <span>
                  <?php _e('Proposal B','oxd'); ?>: 
                </span>
                <?php echo get_post_meta( get_the_ID(), 'postureb', true );?>
              </p>
            </div>
          </div>
          <div id="postures-content-b">
            <div class="speaker-info-div">
                <?php 
                echo get_avatar( $authorb, 160 );
                ?>
              <p class="speaker-name">
                <?php echo $userb->first_name . ' ' . $userb->last_name; ?>
              </p>
              <p class="speaker-description">
                <?php echo $userb->description; ?>
              </p> 
            </div>
            <div>
              <p>
                <?php echo $postureb; ?>
              </p>
            </div>  
          </div>
          <!-- vote b -->
          <div class="postures-vote" id="postures-vote-b">
            
              <?php if (!$plugin_purchased) { echo get_post_meta( get_the_ID(), 'voteb', true).'&nbsp;'._e('Votes ','oxd');} ?>&nbsp;
              
              <?php 
                if (!$disabled) {
                    if (($registered_only == 'yes') and (!is_user_logged_in())) { 
                        ?>
                        <button class="oxd-tooltip" id="vote-button-b" disabled>
                        <?php  _e('Vote B','oxd') ?><span class="oxd-tooltiptext"><?php  _e('You must be registered to participate in the debate.','oxd') ?></span>
                        </button>
                        <?php } else { 
                        if ($plugin_purchased) { ?>
                        <button id="vote-button-b" <?php if ($voteb) { ?> disabled> <?php _e('Voted','oxd'); } else if ($votea) { ?> disabled> <?php _e('Vote B','oxd'); ?> 
                        <?php } else {  ?>
                        > <!-- Close <button> opening tag -->
                        <?php _e('Vote B','oxd'); ?>
                        </button>
                        <?php 
                        } } else { ?>
                        <a href="?p=<?php the_ID(); ?>&vote=b">
                            <button id="vote-b" <?php if ($voteb) { ?> disabled> </a> <?php _e('Voted','oxd'); } else if ($votea) { ?> disabled> </a> <?php _e('Vote B','oxd'); ?>                      
                        <?php } else {  ?>   
                        > <!-- Close <button> opening tag -->
                        <?php _e('Vote B','oxd'); ?>
     <div id="postures-contents">                   </button>
                        </a>
                        <?php } 
                        } 
                        $votes_b = get_post_meta( get_the_ID(), 'voteb', true );
                        } }
?>
              <input type="hidden" name="b-votes" id="b-votes" value="<?php echo $votes_b; ?>"/>
            
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- duration -->
  <?php comments_template( $file = plugin_path() . '/comments-debate.php', $separate_comments = false ); ?>

<?php endwhile; // end of the loop. ?>
<?php //get_sidebar(); ?>
<?php get_footer(); ?>