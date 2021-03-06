

<footer class="site-footer">
      <div class="site-footer__inner container container--narrow">
        <div class="group">
          <div class="site-footer__col-one">
          
              <?php if ( is_active_sidebar( 'footer_widget' ) ) : ?>
                  <div id="secondary" class="widget-area" role="complementary">
                      <?php dynamic_sidebar( 'footer_widget' ); ?>
                  </div><!-- #secondary -->
              <?php endif; ?>
            <!-- <h1 class="school-logo-text school-logo-text--alt-color">
              <a href="<?php echo site_url()?>"><strong>Fictional</strong> University</a>
            </h1>
            <p><a class="site-footer__link" href="#">555.555.5555</a></p> -->
          </div>

          <div class="site-footer__col-two-three-group">
            <div class="site-footer__col-two">
              <h3 class="headline headline--small">Explore</h3>
              <nav class="nav-list">
              <?php
              wp_nav_menu( array( 
              //adding menu to theme - theme location is a defined name
            //
            'theme_location' => 'footerLocation1'));
            ?>
         
              </nav>
            </div>

            <div class="site-footer__col-three">
              <h3 class="headline headline--small">Learn</h3>
              <nav class="nav-list">
              <?php
              wp_nav_menu( array( 
              //adding menu to theme - theme location is a defined name
            //
            'theme_location' => 'footerLocation2'));
            ?>
              </nav>
            </div>
          </div>

          <div class="site-footer__col-four">
            <h3 class="headline headline--small">Connect With Us</h3>
            <nav>
              <ul class="min-list social-icons-list group">
                <li>
                  <a href="#" class="social-color-facebook"><i class="fab fa-facebook" aria-hidden="true"></i></a>
                </li>
                <li>
                  <a href="#" class="social-color-twitter"><i class="fab fa-twitter" aria-hidden="true"></i></a>
                </li>
                <li>
                  <a href="#" class="social-color-youtube"><i class="fab fa-youtube" aria-hidden="true"></i></a>
                </li>
                <li>
                  <a href="#" class="social-color-linkedin"><i class="fab fa-linkedin" aria-hidden="true"></i></a>
                </li>
                <li>
                  <a href="#" class="social-color-instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </footer>
    
<?php wp_footer(); ?>

</body>
</html>
