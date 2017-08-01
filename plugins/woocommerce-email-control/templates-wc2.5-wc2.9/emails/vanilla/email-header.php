<?php
/**
 * Email Header
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Settings.
 */
$header_img_src = esc_url_raw( get_option( 'ec_vanilla_all_header_logo' ) );
if ( ! isset( $header_img_src ) || '' == $header_img_src ) {
	$header_img_src = esc_url_raw( get_option( 'woocommerce_email_header_image' ) );
}

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
