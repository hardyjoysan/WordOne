<?php get_header(); ?>

      <div class="container">

        <div class="row">
          
          <?php while(have_posts()) : the_post(); ?>

            <div class="col-md-12">

              <h2 class="mt-5"><?php the_title(); ?></h2>

              <p><?php the_content(); ?></p>
              
            </div>

          <?php endwhile; ?>

        </div>

      </div><!-- /.container -->


<?php get_footer(); ?>