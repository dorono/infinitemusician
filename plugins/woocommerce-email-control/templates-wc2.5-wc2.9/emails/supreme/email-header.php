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
$header_img_src = esc_url_raw( get_option( 'ec_supreme_all_header_logo' ) );
if ( ! isset( $header_img_src ) || '' == $header_img_src ) {
	$header_img_src = esc_url_raw( get_option( 'woocommerce_email_header_image' ) );
}

$header_logo_alignment = get_option( 'ec_supreme_all_logo_position' );
$top_nav_position = ( $header_logo_alignment == 'center' ) ? 'center' : 'right' ;

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
