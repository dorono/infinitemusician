<?php
/**
 * Email Header
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get Settings.
 */
$header_img_src = esc_url_raw( get_option( 'ec_vanilla_all_header_logo' ) );
if ( ! isset( $header_img_src ) || '' == $header_img_src )
	$header_img_src = esc_url_raw( get_option( 'woocommerce_email_header_image' ) );

/**
 * EC Nav Bar
 */
if ( ! function_exists( 'ec_nav_bar' ) ) :
	function ec_nav_bar () {
		
		$return = false;
		
		$link_text_1	= get_option('ec_vanilla_all_link_1_text');
		$link_image_1	= get_option('ec_vanilla_all_link_1_image');
		$link_url_1		= get_option('ec_vanilla_all_link_1_url');
		
		$link_text_2	= get_option('ec_vanilla_all_link_2_text');
		$link_image_2	= get_option('ec_vanilla_all_link_2_image');
		$link_url_2		= get_option('ec_vanilla_all_link_2_url');
		
		$link_text_3	= get_option('ec_vanilla_all_link_3_text');
		$link_image_3	= get_option('ec_vanilla_all_link_3_image');
		$link_url_3		= get_option('ec_vanilla_all_link_3_url');
		
		$link_text_4	= get_option('ec_vanilla_all_link_4_text');
		$link_image_4	= get_option('ec_vanilla_all_link_4_image');
		$link_url_4		= get_option('ec_vanilla_all_link_4_url');
		
		$link_text_5	= get_option('ec_vanilla_all_link_5_text');
		$link_image_5	= get_option('ec_vanilla_all_link_5_image');
		$link_url_5		= get_option('ec_vanilla_all_link_5_url');
		
		$link_text_6	= get_option('ec_vanilla_all_link_6_text');
		$link_image_6	= get_option('ec_vanilla_all_link_6_image');
		$link_url_6		= get_option('ec_vanilla_all_link_6_url');
		
		if 	( $link_text_1 || $link_image_1 || $link_text_2 || $link_image_2 || $link_text_3 || $link_image_3 || $link_text_4 || $link_image_4 || $link_text_5 || $link_image_5 || $link_text_6 || $link_image_6 ) {
		
			ob_start();
			?>
			<table border="0" cellpadding="0" cellspacing="0" width="auto" class="top_nav">
				<tr>
					<td class="nav-spacer-block">&nbsp;
						
					</td>
					
					<?php
					if ( $link_text_1 || $link_image_1 ) {
						?>
						<?php if ( $link_image_1 ) { ?>
							<td class="nav-image-block">
								<?php if ( $link_url_1 ) { ?><a href="<?php echo esc_url_raw($link_url_1);?>"><?php } ?>
									<img src="<?php echo get_option('ec_vanilla_all_link_1_image') ?>" />
								<?php if ( $link_url_1 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_1 ) { ?>
							<td class="nav-text-block <?php if ($link_image_1) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_1 ) { ?><a href="<?php echo esc_url_raw($link_url_1);?>"><?php } ?>
									<?php echo get_option('ec_vanilla_all_link_1_text') ?>
								<?php if ( $link_url_1 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php
					}
					?>
					
					<?php
					if ( $link_text_2 || $link_image_2 ) {
						?>
						<?php if ( $link_image_2 ) { ?>
							<td class="nav-image-block">
								<?php if ( $link_url_2 ) { ?><a href="<?php echo esc_url_raw($link_url_2);?>"><?php } ?>
									<img src="<?php echo get_option('ec_vanilla_all_link_2_image') ?>" />
								<?php if ( $link_url_2 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_2 ) { ?>
							<td class="nav-text-block <?php if ($link_image_2) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_2 ) { ?><a href="<?php echo esc_url_raw($link_url_2);?>"><?php } ?>
									<?php echo get_option('ec_vanilla_all_link_2_text') ?>
								<?php if ( $link_url_2 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php
					}
					?>
					
					<?php
					if ( $link_text_3 || $link_image_3 ) {
						?>
						<?php if ( $link_image_3 ) { ?>
							<td class="nav-image-block">
								<?php if ( $link_url_3 ) { ?><a href="<?php echo esc_url_raw($link_url_3);?>"><?php } ?>
									<img src="<?php echo get_option('ec_vanilla_all_link_3_image') ?>" />
								<?php if ( $link_url_3 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_3 ) { ?>
							<td class="nav-text-block <?php if ($link_image_3) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_3 ) { ?><a href="<?php echo esc_url_raw($link_url_3);?>"><?php } ?>
									<?php echo get_option('ec_vanilla_all_link_3_text') ?>
								<?php if ( $link_url_3 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php
					}
					?>
					
					<?php
					if ( $link_text_4 || $link_image_4 ) {
						?>
						<?php if ( $link_image_4 ) { ?>
							<td class="nav-image-block">
								<?php if ( $link_url_4 ) { ?><a href="<?php echo esc_url_raw($link_url_4);?>"><?php } ?>
									<img src="<?php echo get_option('ec_vanilla_all_link_4_image') ?>" />
								<?php if ( $link_url_4 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_4 ) { ?>
							<td class="nav-text-block <?php if ($link_image_4) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_4 ) { ?><a href="<?php echo esc_url_raw($link_url_4);?>"><?php } ?>
									<?php echo get_option('ec_vanilla_all_link_4_text') ?>
								<?php if ( $link_url_4 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php
					}
					?>
					
					<?php
					if ( $link_text_5 || $link_image_5 ) {
						?>
						<?php if ( $link_image_5 ) { ?>
							<td class="nav-image-block">
								<?php if ( $link_url_5 ) { ?><a href="<?php echo esc_url_raw($link_url_5);?>"><?php } ?>
									<img src="<?php echo get_option('ec_vanilla_all_link_5_image') ?>" />
								<?php if ( $link_url_5 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_5 ) { ?>
							<td class="nav-text-block <?php if ($link_image_5) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_5 ) { ?><a href="<?php echo esc_url_raw($link_url_5);?>"><?php } ?>
									<?php echo get_option('ec_vanilla_all_link_5_text') ?>
								<?php if ( $link_url_5 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php
					}
					?>
					
					<?php
					if ( $link_text_6 || $link_image_6 ) {
						?>
						<?php if ( $link_image_6 ) { ?>
							<td class="nav-image-block">
								<?php if ( $link_url_6 ) { ?><a href="<?php echo esc_url_raw($link_url_6);?>"><?php } ?>
									<img src="<?php echo get_option('ec_vanilla_all_link_6_image') ?>" />
								<?php if ( $link_url_6 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_6 ) { ?>
							<td class="nav-text-block <?php if ($link_image_6) { ?>nav-text-block-with-image<?php } ?>" >
								<?php if ( $link_url_6 ) { ?><a href="<?php echo esc_url_raw($link_url_6);?>"><?php } ?>
									<?php echo get_option('ec_vanilla_all_link_6_text') ?>
								<?php if ( $link_url_6 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php
					}
					?>
					
					<td class="nav-spacer-block">&nbsp;
						
					</td>
				</tr>
			</table>
			<?php
			$return = ob_get_clean();
		
		}
		
		return $return;
	}
endif;

?>
<!DOCTYPE html>
<html dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
	</head>
	<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
		
		<table class="wrapper" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
			<tr>
				<td class="wrapper-td" align="center" valign="top">
					
					<table class="main-body" border="0" cellpadding="0" cellspacing="0">
						
						<!-- Nav -->
						<?php if ( ec_nav_bar() ) { ?>
							
							<tr>
								<td align="center" valign="top" class="top_nav_holder">
									<?php echo ec_nav_bar(); ?>
								</td>
							</tr>
							
							<tr>
								<td class="divider-line" align="center" valign="top">&nbsp;
									<!-- Divider -->
								</td>
							</tr>
							
						<?php } ?>
						<!-- / Nav -->
						
						
						<!-- Header -->
						<?php if ( $header_img_src ) { ?>
							
							<tr>
								<td class="template_header" >
									<a href="<?php echo get_site_url(); ?>" border="0">
										<img src="<?php echo $header_img_src ?>" />
									</a>
								</td>
							</tr>
							
							<tr>
								<td class="divider-line" align="center" valign="top">&nbsp;
									<!-- Divider -->
								</td>
							</tr>
							
						<?php } ?>
						<!-- / Header -->
						
						
						<!-- Body Content -->
						<tr>
							<td class="body_content" align="center" valign="top">
