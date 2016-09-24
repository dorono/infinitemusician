<?php
/**
 * Email Header
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * Get Settings.
 */
$header_img_src = esc_url_raw( get_option( 'ec_supreme_all_header_logo' ) );
if ( ! isset( $header_img_src ) || '' == $header_img_src )
	$header_img_src = esc_url_raw( get_option( 'woocommerce_email_header_image' ) );

$header_logo_alignment = get_option( "ec_supreme_all_logo_position" );
$top_nav_position = ( $header_logo_alignment == 'center' ) ? 'center' : 'right' ;

/**
 * EC Special Title
 */
if ( ! function_exists( 'ec_special_title' ) ) :
	function ec_special_title ($pass_heading_text, $args) {
		
		$defaults = array (
			'text_position'		=> 'left',	// text_position = center, left, right
			'border_position'	=> 'right',	// border_position = center, bottom, none
			'space_before'		=> '',		// 6px or 6 (px will get added automatically)
			'space_after'		=> '',		// 6px or (px will get added automatically)
		);
		
		// Parse incoming $args into an array and merge it with $defaults
		$args = wp_parse_args( $args, $defaults );
		
		// Auto add 'px' to the end of spacing values
		if ( $args['space_before'] !== '' && FALSE === strpos( $args['space_before'], 'px' ) ) {
			$args['space_before'] = $args['space_before'] . 'px';
		}
		if ( $args['space_after'] !== '' && FALSE === strpos( $args['space_after'], 'px' ) ) {
			$args['space_after'] = $args['space_after'] . 'px';
		}
		
		$pass_heading_text = str_replace( ' ', '&nbsp;', $pass_heading_text );
		
		ob_start();
		?>
		<table class="special-title-holder" width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class="header_content_h2_space_before" style="<?php if ( $args['space_before'] ) echo 'height: ' . $args['space_before'] . ';' ; ?> font-size:0px; "></td>
						</tr>
					</table>
					
					<?php
					if ( $args['border_position'] == "center" && $args['text_position'] == "center" ) {
						?>
						<!-- Heading with lines on either side -->
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="50%">
									<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
										<tr height="50%" style="height:50%;" >
											<td>&nbsp;</td>
										</tr>
										<tr height="50%" style="height:50%;" >
											<td class="header_content_h2_border"></td>
										</tr>
									</table>
								</td>
								<td width="1%" style="padding-right:6px; padding-left:6px; " class="header_content_h2" >
									<?php echo $pass_heading_text; ?>
								</td>
								<td width="50%">
									<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
										<tr height="50%" style="height:50%;" >
											<td>&nbsp;</td>
										</tr>
										<tr height="50%" style="height:50%;" >
											<td class="header_content_h2_border"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<?php
					}
					if ( $args['border_position'] == "center" && $args['text_position'] == "left" ) {
						?>
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="1%" style="padding-right:6px;" class="header_content_h2" >
									<?php echo $pass_heading_text; ?>
								</td>
								<td width="99%">
									<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
										<tr height="50%" style="height:50%;" >
											<td>&nbsp;</td>
										</tr>
										<tr height="50%" style="height:50%;" >
											<td class="header_content_h2_border"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<?php
					}
					if ( $args['border_position'] == "center" && $args['text_position'] == "right" ) {
						?>
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="99%">
									<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
										<tr height="50%" style="height:50%;" >
											<td>&nbsp;</td>
										</tr>
										<tr height="50%" style="height:50%;" >
											<td class="header_content_h2_border"></td>
										</tr>
									</table>
								</td>
								<td width="1%" style="padding-left:6px;" class="header_content_h2" >
									<?php echo $pass_heading_text; ?>
								</td>
							</tr>
						</table>
						<?php
					}
					if ( $args['border_position'] == "bottom" || $args['border_position'] == "border-none" ) {
						?>
						
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="100%" style="text-align: <?php echo $args['text_position']; ?>;" class="header_content_h2" >
									<?php echo $pass_heading_text; ?>
								</td>
							</tr>
						</table>
						
						<?php if ($args['border_position'] == "bottom") { ?>
							<table width="100%" border="0" cellpadding="0" cellspacing="0" style="padding-top:6px; padding-bottom:6px;">
								<tr>
									<td width="100%" class="header_content_h2_border" >
									</td>
								</tr>
							</table>
						<?php } ?>
						
						<?php
					}
					?>
					
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class="header_content_h2_space_after" <?php if ( $args['space_after'] ) echo 'style="height: ' . $args['space_after'] . ';"' ; ?> ></td>
						</tr>
					</table>
					
				</td>
			</tr>
		</table>
		<?php
		
		$string = ob_get_clean();
		
		return $string;
	}
endif;

/**
 * EC Nav Bar
 */
if ( ! function_exists( 'ec_nav_bar' ) ) :
	function ec_nav_bar () {
		
		$return = false;
		
		$link_text_1	= get_option('ec_supreme_all_link_1_text');
		$link_image_1	= get_option('ec_supreme_all_link_1_image');
		$link_url_1		= get_option('ec_supreme_all_link_1_url');
		
		$link_text_2	= get_option('ec_supreme_all_link_2_text');
		$link_image_2	= get_option('ec_supreme_all_link_2_image');
		$link_url_2		= get_option('ec_supreme_all_link_2_url');
		
		$link_text_3	= get_option('ec_supreme_all_link_3_text');
		$link_image_3	= get_option('ec_supreme_all_link_3_image');
		$link_url_3		= get_option('ec_supreme_all_link_3_url');
		
		$link_text_4	= get_option('ec_supreme_all_link_4_text');
		$link_image_4	= get_option('ec_supreme_all_link_4_image');
		$link_url_4		= get_option('ec_supreme_all_link_4_url');
		
		$link_text_5	= get_option('ec_supreme_all_link_5_text');
		$link_image_5	= get_option('ec_supreme_all_link_5_image');
		$link_url_5		= get_option('ec_supreme_all_link_5_url');
		
		$link_text_6	= get_option('ec_supreme_all_link_6_text');
		$link_image_6	= get_option('ec_supreme_all_link_6_image');
		$link_url_6		= get_option('ec_supreme_all_link_6_url');
		
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
									<img src="<?php echo get_option('ec_supreme_all_link_1_image') ?>" />
								<?php if ( $link_url_1 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_1 ) { ?>
							<td class="nav-text-block <?php if ($link_image_1) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_1 ) { ?><a href="<?php echo esc_url_raw($link_url_1);?>"><?php } ?>
									<?php echo get_option('ec_supreme_all_link_1_text') ?>
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
									<img src="<?php echo get_option('ec_supreme_all_link_2_image') ?>" />
								<?php if ( $link_url_2 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_2 ) { ?>
							<td class="nav-text-block <?php if ($link_image_2) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_2 ) { ?><a href="<?php echo esc_url_raw($link_url_2);?>"><?php } ?>
									<?php echo get_option('ec_supreme_all_link_2_text') ?>
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
									<img src="<?php echo get_option('ec_supreme_all_link_3_image') ?>" />
								<?php if ( $link_url_3 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_3 ) { ?>
							<td class="nav-text-block <?php if ($link_image_3) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_3 ) { ?><a href="<?php echo esc_url_raw($link_url_3);?>"><?php } ?>
									<?php echo get_option('ec_supreme_all_link_3_text') ?>
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
									<img src="<?php echo get_option('ec_supreme_all_link_4_image') ?>" />
								<?php if ( $link_url_4 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_4 ) { ?>
							<td class="nav-text-block <?php if ($link_image_4) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_4 ) { ?><a href="<?php echo esc_url_raw($link_url_4);?>"><?php } ?>
									<?php echo get_option('ec_supreme_all_link_4_text') ?>
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
									<img src="<?php echo get_option('ec_supreme_all_link_5_image') ?>" />
								<?php if ( $link_url_5 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_5 ) { ?>
							<td class="nav-text-block <?php if ($link_image_5) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_5 ) { ?><a href="<?php echo esc_url_raw($link_url_5);?>"><?php } ?>
									<?php echo get_option('ec_supreme_all_link_5_text') ?>
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
									<img src="<?php echo get_option('ec_supreme_all_link_6_image') ?>" />
								<?php if ( $link_url_6 ) { ?></a><?php } ?>
							</td>
						<?php } ?>
						<?php if ( $link_text_6 ) { ?>
							<td class="nav-text-block <?php if ($link_image_6) { ?>nav-text-block-with-image<?php } ?>">
								<?php if ( $link_url_6 ) { ?><a href="<?php echo esc_url_raw($link_url_6);?>"><?php } ?>
									<?php echo get_option('ec_supreme_all_link_6_text') ?>
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

ob_start();
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
						<tr>
							<td align="center" valign="top">
								
								<!-- Header -->
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td class="template_header" >
											<a href="<?php echo get_site_url(); ?>" border="0">
												<?php
												if ( $header_img_src ) {
													?>
													<img src="<?php echo $header_img_src ?>" />
													<?php
												}
												else{
													?>
													<br>
													<br>
													<br>
													<?php
												}
												?>
											</a>
											
										</td>
									</tr>
								</table>
								<!-- End Header -->
								
							</td>
						</tr>
						
						
						<?php if ( ec_nav_bar() ) { ?>
							<tr>
								<td align="<?php echo $top_nav_position; ?>" valign="top" class="top_nav_holder">
									
									<?php echo ec_nav_bar(); ?>
								
								</td>
							</tr>
						<?php } ?>
						
						
						<tr>
							<td align="left" valign="top">
								
								
								<!-- Body -->
								<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body">
									<tr>
										<td valign="top" class="body_content">
											
											
											<!-- Content -->
											<table border="0" cellspacing="0" width="100%">
												<tr>
													<td valign="top" class="body_content_inner">
