<?php if ( class_exists( 'WooCommerce' ) ) { ?>
	<div class="flexslider">
		
	    <ul class="slides"> 
	        <?php

			$meta_key = get_theme_mod( 'shes_slider_area' );
			if(get_theme_mod( 'shes_slider_num_show' )) {
				$num_prod = get_theme_mod( 'shes_slider_num_show', 5 );
			} else {
				$num_prod = "5";
			}

			if( $meta_key == "top_rated" ) {
				add_filter( 'posts_clauses', array( WC()->query, 'order_by_rating_post_clauses' ) );
				$args = array( 'posts_per_page' => $num_prod, 'no_found_rows' => 1, 'post_status' => 'publish', 'post_type' => 'product' );
				$args['meta_query'] = WC()->query->get_meta_query();
			} elseif( $meta_key == "featured" ) {
				$args = array( 'post_type' => 'product', 'posts_per_page' => $num_prod ,'meta_key' => '_featured', 'meta_value' => 'yes' );
			} elseif( $meta_key == "sale" ) {
				$args = array( 'post_type' => 'product', 'posts_per_page' => $num_prod,
				    'meta_query' => array(
				        'relation' => 'OR',
				        array( 
				        'key'           => '_sale_price',
				        'value'         => 0,
				        'compare'       => '>',
				        'type'          => 'numeric'
				        ),
				        array( // Variable products type
				        'key'           => '_min_variation_sale_price',
				        'value'         => 0,
				        'compare'       => '>',
				        'type'          => 'numeric'
				        )
				    ) 
				);	
			} elseif( $meta_key == "total_sales" ) {
				$args = array(
					'post_type' => 'product',
					'meta_key' => 'total_sales',
					'orderby' => 'meta_value_num',
					'posts_per_page' => $num_prod
				);
			} elseif( $meta_key == "recent" ) {
				$args = array( 'post_type' => 'product', 'stock' => 1, 'posts_per_page' => $num_prod, 'orderby' =>'date','order' => 'DESC' );
			} else {
				$args = array( 'post_type' => 'product', 'stock' => 1, 'posts_per_page' => $num_prod, 'orderby' =>'date','order' => 'DESC' );
			}
			$loop = new WP_Query( $args );
			while ( $loop->have_posts() ) : $loop->the_post(); global $product, $post;	 ?>
            
				<li class="product-slider"> 
					<div class="banner-product-image">
						<?php if ( has_post_thumbnail( $loop->post->ID ) ) echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" width="300px" height="300px" />'; ?>
					</div>
					<div class="banner-product-details">   
						<h3><?php the_title(); ?></h3>						
						<p><?php echo $loop->post->post_excerpt; ?></p>
						<p class="price"><?php echo $product->get_price_html(); ?>
						<a href="<?php echo get_permalink( $loop->post->ID ) ?>" class="button" title="<?php echo esc_attr($loop->post->post_title ? $loop->post->post_title : $loop->post->ID); ?>"><?php _e('View Product','storefront-homepage-extra-sections'); ?></a>
						</p>
					</div>
					<div class="clearfix"></div>
				</li>
			
			<?php endwhile; ?>
			<?php 
				if( $meta_key == "top_rated" ) {
					remove_filter( 'posts_clauses', array( WC()->query, 'order_by_rating_post_clauses' ) ); 
				}
			?>
			<?php wp_reset_postdata(); ?>
		</ul>
	</div>

<?php }