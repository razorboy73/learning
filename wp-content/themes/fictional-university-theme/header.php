<!DOCTYPE html>
<html <?php language_attributes()?>>
    <head>
      <meta charset='<?php bloginfo("charset");?>'>
      <meta name = "viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class();?> >
    <header class="site-header">
      <div class="container">
        <h1 class="school-logo-text float-left">
          <a href="<?php echo site_url()?>"><strong>Fictional</strong> University</a>
        </h1>
        <a href="<?php echo esc_url(site_url("/search"))?>"class="js-search-trigger site-header__search-trigger"><i class="fa fa-search" aria-hidden="true"></i></a>
        <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>
        <div class="site-header__menu group">
          <nav class="main-navigation">

    	  <?php
          wp_nav_menu( array( 
            //adding menu to theme - theme location is a defined name
            //
            'theme_location' => 'headerMenuLocation'));
        ?>
        <?php

        wp_nav_menu( array(
          'theme_location'  => 'main-nav',
          //slug from menu area
          'menu'            => '',
          'container'       => 'div',
          'container_class' => '',
          'container_id'    => '',
          'menu_class'      => 'menu',
          'menu_id'         => '',
          'echo'            => true,
          'fallback_cb'     => 'wp_page_menu',
          'before'          => '<h1>',
          'after'           => '</h2>',
          'link_before'     => '',
          'link_after'      => '',
          'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>', 
          'depth' => 0, 
          'walker' => ''

        ))

        ?>
         
          </nav>
          <div class="site-header__util">

          <?php if(is_user_logged_in()){ ?> 
            <a href="<?php echo esc_url(site_url("/my-notes"))?>" class="btn btn--small btn--orange float-left push-right">My Notes</a>
                <a href="<?php echo wp_logout_url(); ?>" class="btn btn--small btn--orange float-left push-right btn--with-photo">
                <span class="site-header__avatar"><?php echo get_avatar(get_current_user_id(), 60); ?></span>
                <span class="btn__text">Logout</span>
                </a>

         <?php  }else{ ?>
            <a href="<?php echo wp_login_url()?>" class="btn btn--small btn--orange float-left push-right">Login</a>
            <a href="<?php echo wp_registration_url()?>" class="btn btn--small btn--dark-orange float-left">Sign Up</a>

          <?php } ?>
            
            <a href="<?php echo esc_url(site_url("/search"))?>"class="search-trigger js-search-trigger"><i class="fa fa-search" aria-hidden="true"></i></a>
          </div>
        </div>
      </div>
    </header>
