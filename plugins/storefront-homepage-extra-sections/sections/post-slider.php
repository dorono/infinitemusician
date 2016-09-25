    <div class="flexslider">
		
	    <ul class="slides"> 
	        <?php

			$meta_key = get_theme_mod( 'shes_slider_area' );
			//if(get_theme_mod( 'shes_slider_num_show' )) {
				$num_prod = get_theme_mod( 'shes_slider_num_show', 5 );
			//} else {
				//$num_prod = "5";
			//}

			$args = array(
			    'post_type'      => 'post',
				'post__in'       =>get_option('sticky_posts'),
			    'posts_per_page' => $num_prod,
		    );
			$slider = new WP_Query( $args );
			while ( $slider->have_posts() ) : $slider->the_post(); global $post;	 ?>
            <section class="storefront-product-section storefront-homepage-extra-sections">
				<li class="featured-slider"> 
					<div class="banner-featured-image">
						<?php if ( has_post_thumbnail( $slider->post->ID ) ) echo get_the_post_thumbnail($slider->post->ID, 'full'); ?>
					</div>
					<div class="banner-featured-details">   
						<h3><?php the_title(); ?></h3>						
						<p><?php the_excerpt(); ?></p>
						<p>
						<a href="<?php echo get_permalink( $slider->post->ID ) ?>" class="button" title="<?php echo esc_attr($slider->post->post_title ? $slider->post->post_title : $slider->post->ID); ?>"><?php _e('Continue Reading','storefront-homepage-extra-sections'); ?></a>
						</p>
					</div>
					<div class="clearfix"></div>
				</li>
			</section>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</ul>
	</div>