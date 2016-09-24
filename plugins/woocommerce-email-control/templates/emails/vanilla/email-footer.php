<?php
/**
 * Email Footer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
							
							</td>
						</tr>
						
						
						<!-- Nav -->
						<?php if ( ec_nav_bar() ) { ?>
							
							<tr>
								<td class="divider-line" align="center" valign="top">&nbsp;
									<!-- Divider -->
								</td>
							</tr>
							
							<tr style="text-align: center;">
								<td align="center" valign="top" class="top_nav_holder">
									<?php echo ec_nav_bar(); ?>
								</td>
							</tr>
							
						<?php } ?>
						<!-- / Nav -->
						
						
						<!-- Footer -->
						<?php
						$footer_text = get_option("ec_vanilla_all_footer_text");
						$footer_image = get_option('ec_vanilla_all_footer_image');
						
						if ( $footer_text || $footer_image ) {
							?>
							
							<tr>
								<td class="divider-line" align="center" valign="top">&nbsp;
									<!-- Divider -->
								</td>
							</tr>
							
							<tr>
								<td width="100%" align="center">
									
									<!-- Footer Text -->
									<?php
									if ( $footer_text ) {
										?>
										<table class="footer-text-block" align="center" cellpadding="0" cellspacing="0" border="0" width="100%">
											<tr>
												<td class="footer-text-block-td">
													<?php echo $footer_text; ?>
												</td>
											</tr>
										</table>
										<?php
									}
									?>
									<!-- / Footer Text -->
									
									<!-- Footer Image -->
									<?php
									if ( $footer_image ) {
										?>
										<table class="footer-logo-block" align="left" cellpadding="0" cellspacing="0" border="0" width="100%">
											<tr>
												<td class="footer-logo-block-td">
													<img src="<?php echo $footer_image; ?>" />
												</td>
											</tr>
										</table>
										<?php
									}
									?>
									<!-- / Footer Image -->
									
								</td>
							</tr>
							<?php
						}
						?>
						<!-- / Footer -->
						
						
					</table>
					
				</td>
			</tr>
		</table>
		
	</body>
</html>
