<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package storefront
 */

get_header(); ?>

	<div id="primary" class="content-area">

		<main id="main" class="site-main" role="main">

			<div class="error-404 not-found">

				<div class="page-content">

					<header class="page-header">
						<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'storefront' ); ?></h1>
					</header><!-- .page-header -->
					<h3 style="background: #ccc; padding: 10px;">If you just made a purchase and were redirected to this page, our apologies, as we just launched this site and are running into a few growing pains. Please be rest-assured that your purchase did indeed go through, and you should have received one email with your password and another email with a download link. If for some reason you haven't received a confirmation email, please <a href="https://www.infinitemusician.com/contact/">contact us here and we'll sort it out right away.</a></h3>
					<p><?php esc_html_e( 'Nothing was found at this location. Try searching, or check out the links below.', 'storefront' ); ?></p>

					<?php
					echo '<section aria-label="Search">';

					if ( storefront_is_woocommerce_activated() ) {
						the_widget( 'WC_Widget_Product_Search' );
					} else {
						get_search_form();
					}

					echo '</section>';

					if ( storefront_is_woocommerce_activated() ) {

						echo '<div class="fourohfour-columns-2">';

							echo '<section class="col-1" aria-label="Promoted Products">';

								storefront_promoted_products();

							echo '</section>';

							echo '<nav class="col-2" aria-label="Product Categories">';

							echo '<h2>' . esc_html__( 'Product Categories', 'storefront' ) . '</h2>';

							the_widget( 'WC_Widget_Product_Categories', array(
																			'count'		=> 1,
							) );
							echo '</nav>';

							echo '</div>';

							echo '<section aria-label="Popular Products" >';

							echo '<h2>' . esc_html__( 'Popular Products', 'storefront' ) . '</h2>';

							echo storefront_do_shortcode( 'best_selling_products', array(
								'per_page'  => 4,
								'columns'   => 4,
							) );

							echo '</section>';
					}
					?>

				</div><!-- .page-content -->
			</div><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer();
