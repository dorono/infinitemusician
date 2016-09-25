   <section class="storefront-product-section storefront-homepage-extra-sections">
		
	<?php 
		$tripple_title = get_theme_mod( 'shes_tripple_title' );
		$tripple_tagline = get_theme_mod( 'shes_tripple_tagline' );
			
		if ( get_theme_mod('shes_tripple_title') ) {
	?>
        <div class="tripple-widget-header">
		    <h1><?php echo esc_attr( $tripple_title ); ?></h1>
		</div>
	<?php }
	    if ( get_theme_mod('shes_tripple_tagline') ) { ?>
		<div class="tripple-widget-tagline">
			<?php echo esc_attr( $tripple_tagline ); ?>
		</div>
	<?php } ?>
		
		<div class="woocommerce columns-3">
			
			<?php if ( is_active_sidebar( 'shesw-1' ) || is_active_sidebar( 'shesw-2' ) || is_active_sidebar( 'shesw-3' ) ) { ?>
				<?php if ( is_active_sidebar( 'shesw-1' ) ) { ?>
				    <div class="shes-widget first">
					    <?php dynamic_sidebar( 'shesw-1' ); ?>
	                </div><!-- .widget-area -->
                <?php } ?>
				
				<?php if ( is_active_sidebar( 'shesw-2' ) ) { ?>
				    <div class="shes-widget">
					    <?php dynamic_sidebar( 'shesw-2' ); ?>
	                </div><!-- .widget-area -->
                <?php } ?>
				
				<?php if ( is_active_sidebar( 'shesw-3' ) ) { ?>
				    <div class="shes-widget last">
					    <?php dynamic_sidebar( 'shesw-3' ); ?>
	                </div><!-- .widget-area -->
                <?php } 
			} ?>	
			
		</div>
		
	</section>