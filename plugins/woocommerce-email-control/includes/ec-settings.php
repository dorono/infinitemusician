<?php

$GLOBALS['EC_Settings'] = new EC_Settings();

/*
*  EC_settings
*
* Settings for eg email thmeme settings editing
* @since: 3.6
* @created: 25/01/13
*/

class EC_Settings {
	
	/**
	 * Construct and initialize the main plugin class
	 */
	public function __construct() {
		
	}
	
	/**
	 * Output admin fields.
	 *
	 * Loops though the options array and outputs each field.
	 *
	 * @access public
	 * @param array $options Options array to output
	 */
	public static function output_fields( $options ) {
		
		foreach ( $options as $value ) {
			
			// Apply defaults.
			$value = wp_parse_args( $value, array(
				'type'        => 'text',
				'id'          => '',
				'title'       => isset( $value['name'] ) ? $value['name'] : '',
				'class'       => '',
				'field_class' => '',
				'css'         => '',
				'default'     => '',
				'desc'        => '',
				'tip'         => false,
				'size'        => 'full',
				'email-type'  => 'all',
			) );
			
			// Custom attribute handling
			$custom_attributes = array();

			if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) )
				foreach ( $value['custom_attributes'] as $attribute => $attribute_value )
					$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';

			// Description handling
			if ( $value['tip'] === true ) {
				$description = '';
				$tip = $value['desc'];
			}
			elseif ( ! empty( $value['tip'] ) ) {
				$description = $value['desc'];
				$tip = $value['tip'];
			}
			elseif ( ! empty( $value['desc'] ) ) {
				$description = $value['desc'];
				$tip = '';
			}
			else {
				$description = $tip = '';
			}

			if ( $description && in_array( $value['type'], array( 'textarea', 'radio' ) ) ) {
				$description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
			}
			elseif ( $description && in_array( $value['type'], array( 'checkbox' ) ) ) {
				$description =  wp_kses_post( $description );
			}
			elseif ( $description ) {
				$description = '<span class="description">' . wp_kses_post( $description ) . '</span>';
			}

			if ( $tip && in_array( $value['type'], array( 'checkbox' ) ) ) {
				$tip = '<span class="help-icon" title="' . esc_attr( $tip ) . '" >&nbsp;</span>';
			}
			elseif ( $tip ) {
				$tip = '<span class="help-icon" title="' . esc_attr( $tip ) . '" >&nbsp;</span>';
			}
			

			// Switch based on type
			switch( $value['type'] ) {
				
				// CX
				
				case 'image_upload':
					$type 			= $value['type'];
					$class 			= '';
					$option_value 	= self::get_option( $value['id'], $value['default'] );
					?>
					
					<div class="main-controls-element forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo esc_attr( $value['class'] ); ?>">
						<label class="controls-label">
							<?php echo esc_html( $value['title'] ); ?>
							<?php echo $tip; ?>
						</label>
						<?php // echo $tip; ?>
						
						<div class="controls-field">
							<div class="controls-inner-row">
								
								<?php if ( $value['default'] ) : ?>
									<span class="reset-to-default" title="<?php echo esc_attr( __( "Reset to:", 'email-control' ) . "\n" . $value['default'] ); ?>" data-default="<?php echo esc_attr( $value['default'] ); ?>">
										<?php _e( 'Reset to default', 'email-control' ) ?> <i class="cxectrl-icon-arrows-cw"></i>
									</span>
								<?php endif ?>
								
								<input
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									type="text"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									value="<?php echo esc_attr( $option_value ); ?>"
									placeholder="http://"
									
									class="upload_image <?php echo esc_attr( $value['field_class'] ); ?>"
									autocomplete="off"
									<?php echo implode( ' ', $custom_attributes ); ?>
								/>
								<input class="upload_image_button button" type="button" value="Upload" />
								
								<?php // echo $description; ?>
							</div>
						</div>
					</div>
					
					<?php
				
				break;
				
				case 'text':
				case 'email':
				case 'number':
				case 'color' :
				case 'password' :
					
					$type 			= $value['type'];
					$class 			= '';
					$option_value 	= self::get_option( $value['id'], $value['default'] );

					if ( 'color' == $type ) {
						$type = 'text';
						$value['field_class'] .= 'ec-colorpick';
						$description .= '<div id="colorPickerDiv_' . esc_attr( $value['id'] ) . '" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>';
					}
					?>
					
					<div class="main-controls-element forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo esc_attr( $value['class'] ); ?>">
						<label class="controls-label">
							<?php echo esc_html( $value['title'] ); ?>
							<?php echo $tip; ?>
						</label>
						
						<div class="controls-field">
							<div class="controls-inner-row">
								
								<?php if ( $value['default'] ) : ?>
									<span class="reset-to-default" title="<?php echo esc_attr( __( "Reset to:", 'email-control' ) . "\n" . $value['default'] ); ?>" data-default="<?php echo esc_attr( $value['default'] ); ?>">
										<?php _e( 'Reset to default', 'email-control' ) ?> <i class="cxectrl-icon-arrows-cw"></i>
									</span>
								<?php endif ?>
								
								<input
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									type="<?php echo esc_attr( $type ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									value="<?php echo esc_attr( $option_value ); ?>"
									class="<?php echo esc_attr( $value['field_class'] ); ?>"
									autocomplete="off"
									<?php echo implode( ' ', $custom_attributes ); ?>
								/>
								<?php // echo $description; ?>
							</div>
						</div>
					</div>
					
					<?php
				break;
				
				case 'heading':
					
					$type 			= $value['type'];
					$class 			= '';
					?>
					
					<div class="main-controls-heading forminp-<?php echo sanitize_title( $value['type'] ) ?>  <?php echo esc_attr( $value['class'] ); ?>">
						<?php echo esc_html( $value['title'] ); ?>
						<?php echo $tip; ?>
					</div>
					
					<?php
				break;

				// Textarea
				case 'textarea':

					$option_value = self::get_option( $value['id'], $value['default'] );
					?>
					
					<div class="main-controls-element forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo esc_attr( $value['class'] ); ?>">
						<label class="controls-label">
							<?php echo esc_html( $value['title'] ); ?>
							<?php echo $tip; ?>
						</label>
						<?php // echo $tip; ?>
						<?php // echo $description; ?>
						
						<div class="controls-field">
							<div class="controls-inner-row">
								
								<?php if ( $value['default'] ): ?>
									<span class="reset-to-default" title="<?php echo esc_attr( __( "Reset to:", 'email-control' ) . "\n" . $value['default'] ); ?>" data-default="<?php echo esc_attr( $value['default'] ); ?>">
										<?php _e( 'Reset to default', 'email-control' ) ?> <i class="cxectrl-icon-arrows-cw"></i>
									</span>
								<?php endif ?>
								
								<textarea
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									class="<?php echo esc_attr( $value['field_class'] ); ?>"
									<?php echo implode( ' ', $custom_attributes ); ?>
								><?php echo esc_textarea( $option_value ); ?></textarea>
								
							</div>
						</div>
					</div>
					
					<?php
					
				break;
				
				
				// /CX

				// Section Titles
				case 'title':
					if ( ! empty( $value['title'] ) ) {
						echo '<h3>' . esc_html( $value['title'] ) . '</h3>';
					}
					if ( ! empty( $value['desc'] ) ) {
						echo wpautop( wptexturize( wp_kses_post( $value['desc'] ) ) );
					}
					echo '<table class="form-table">'. "\n\n";
					if ( ! empty( $value['id'] ) ) {
						do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) );
					}
				break;

				// Section Ends
				case 'sectionend':
					if ( ! empty( $value['id'] ) ) {
						do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) . '_end' );
					}
					echo '</table>';
					if ( ! empty( $value['id'] ) ) {
						do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) . '_after' );
					}
				break;
				
				// Select boxes
				case 'select' :
				case 'multiselect' :

					$option_value = self::get_option( $value['id'], $value['default'] );

					?>
					<div class="main-controls-element forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo esc_attr( $value['class'] ); ?>">
						<label class="controls-label">
							<?php echo esc_html( $value['title'] ); ?>
							<?php echo $tip; ?>
						</label>
						<?php // echo $tip; ?>
						<?php // echo $description; ?>
						
						<div class="controls-field">
							<div class="controls-inner-row">
								
								<?php if ( $value['default'] ) : ?>
									<span class="reset-to-default" title="<?php echo esc_attr( __( "Reset to:", 'email-control' ) . "\n" . $value['default'] ); ?>" data-default="<?php echo esc_attr( $value['default'] ); ?>">
										<?php _e( 'Reset to default', 'email-control' ) ?> <i class="cxectrl-icon-arrows-cw"></i>
									</span>
								<?php endif ?>
								
								<select
									name="<?php echo esc_attr( $value['id'] ); ?><?php if ( $value['type'] == 'multiselect' ) echo '[]'; ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									class="<?php echo esc_attr( $value['field_class'] ); ?>"
									<?php echo implode( ' ', $custom_attributes ); ?>
									<?php if ( $value['type'] == 'multiselect' ) echo 'multiple="multiple"'; ?>
									>
									<?php
										foreach ( $value['options'] as $key => $val ) {
											?>
											<option value="<?php echo esc_attr( $key ); ?>" <?php

												if ( is_array( $option_value ) )
													selected( in_array( $key, $option_value ), true );
												else
													selected( $option_value, $key );

											?>><?php echo $val ?></option>
											<?php
										}
									?>
							   </select>
							   
							</div>
						</div>
					</div>
					<?php
				break;

				// Radio inputs
				case 'radio' :

					$option_value = self::get_option( $value['id'], $value['default'] );

					?><tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							<?php echo $tip; ?>
						</th>
						<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
							<fieldset>
								<?php echo $description; ?>
								<ul>
								<?php
									foreach ( $value['options'] as $key => $val ) {
										?>
										<li>
											<label><input
												name="<?php echo esc_attr( $value['id'] ); ?>"
												value="<?php echo $key; ?>"
												type="radio"
												style="<?php echo esc_attr( $value['css'] ); ?>"
												class="<?php echo esc_attr( $value['field_class'] ); ?>"
												<?php echo implode( ' ', $custom_attributes ); ?>
												<?php checked( $key, $option_value ); ?>
												/> <?php echo $val ?></label>
										</li>
										<?php
									}
								?>
								</ul>
							</fieldset>
						</td>
					</tr><?php
				break;

				// Checkbox input
				case 'checkbox' :
				
					$option_value = self::get_option( $value['id'], $value['default'] );
					?>
					<div class="main-controls-element forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo esc_attr( $value['class'] ); ?>">
						<label class="controls-label">
							<?php echo esc_html( $value['title'] ); ?>
							<?php echo $tip; ?>
						</label>
						
						<div class="controls-field">
							<div class="controls-inner-row">
								
								<?php if ( $value['default'] ) : ?>
									<span class="reset-to-default" title="<?php echo esc_attr( __( "Reset to:", 'email-control' ) . "\n" . $value['default'] ); ?>" data-default="<?php echo esc_attr( $value['default'] ); ?>">
										<?php _e( 'Reset to default', 'email-control' ) ?> <i class="cxectrl-icon-arrows-cw"></i>
									</span>
								<?php endif ?>
								
								<input
									name="<?php echo esc_attr( $value['id'] ); ?>"
									type="hidden"
									value="no"
								/>
								<input
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									type="checkbox"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									value="yes"
									<?php echo checked( 'yes', $option_value ); ?>
									class="<?php echo esc_attr( $value['field_class'] ); ?>"
									<?php echo implode( ' ', $custom_attributes ); ?>
								/>
								
								<?php // echo $description; ?>
							</div>
						</div>
					</div>
					<?php
				break;

				// Image width settings
				case 'image_width' :

					$width 	= self::get_option( $value['id'] . '[width]', $value['default']['width'] );
					$height = self::get_option( $value['id'] . '[height]', $value['default']['height'] );
					$crop 	= checked( 1, self::get_option( $value['id'] . '[crop]', $value['default']['crop'] ), false );

					?><tr valign="top">
						<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?> <?php echo $tip; ?></th>
						<td class="forminp image_width_settings">

							<input name="<?php echo esc_attr( $value['id'] ); ?>[width]" id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3" value="<?php echo $width; ?>" autocomplete="off" /> &times; <input name="<?php echo esc_attr( $value['id'] ); ?>[height]" id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3" value="<?php echo $height; ?>" autocomplete="off" />px

							<label><input name="<?php echo esc_attr( $value['id'] ); ?>[crop]" id="<?php echo esc_attr( $value['id'] ); ?>-crop" type="checkbox" <?php echo $crop; ?> /> <?php _e( 'Hard Crop?', 'email-control' ); ?></label>

							</td>
					</tr><?php
				break;

				// Single page selects
				case 'single_select_page' :

					$args = array( 'name'				=> $value['id'],
								   'id'					=> $value['id'],
								   'sort_column' 		=> 'menu_order',
								   'sort_order'			=> 'ASC',
								   'show_option_none' 	=> ' ',
								   'field_class'		=> $value['field_class'],
								   'echo' 				=> false,
								   'selected'			=> absint( self::get_option( $value['id'] ) )
								   );

					if ( isset( $value['args'] ) )
						$args = wp_parse_args( $value['args'], $args );

					?><tr valign="top" class="single_select_page">
						<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?> <?php echo $tip; ?></th>
						<td class="forminp">
							<?php echo str_replace(' id=', " data-placeholder='" . __( 'Select a page&hellip;', 'email-control' ) .  "' style='" . $value['css'] . "' class='" . $value['field_class'] . "' id=", wp_dropdown_pages( $args ) ); ?> <?php echo $description; ?>
						</td>
					</tr><?php
				break;

				// Single country selects
				case 'single_select_country' :
					$country_setting = (string) self::get_option( $value['id'] );
					
					if ( class_exists('WC') )
						$countries       = WC()->countries->countries;
					else
						$countries       = $woocommerce->countries->countries;

					if ( strstr( $country_setting, ':' ) ) {
						$country_setting = explode( ':', $country_setting );
						$country         = current( $country_setting );
						$state           = end( $country_setting );
					}
					else {
						$country = $country_setting;
						$state   = '*';
					}
					?><tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php echo esc_html( $value['title'] ); ?></label>
							<?php echo $tip; ?>
						</th>
						<td class="forminp"><select name="<?php echo esc_attr( $value['id'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>" data-placeholder="<?php _e( 'Choose a country&hellip;', 'email-control' ); ?>" title="Country" class="chosen_select">
							<?php
							if ( class_exists('WC') )
								WC()->countries->country_dropdown_options( $country, $state );
							else
								$woocommerce->countries->country_dropdown_options( $country, $state );
							?>
						</select> <?php echo $description; ?>
						</td>
					</tr><?php
				break;

				// Country multiselects
				case 'multi_select_countries' :

					$selections = (array) self::get_option( $value['id'] );

					if ( ! empty( $value['options'] ) )
						$countries = $value['options'];
					else
						$countries = WC()->countries->countries;

					asort( $countries );
					?><tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php echo esc_html( $value['title'] ); ?></label>
							<?php echo $tip; ?>
						</th>
						<td class="forminp">
							<select multiple="multiple" name="<?php echo esc_attr( $value['id'] ); ?>[]" style="width:350px" data-placeholder="<?php _e( 'Choose countries&hellip;', 'email-control' ); ?>" title="Country" class="chosen_select">
								<?php
									if ( $countries )
										foreach ( $countries as $key => $val )
											echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $selections ), true, false ).'>' . $val . '</option>';
								?>
							</select> <?php if ( $description ) echo $description; ?> </br><a class="select_all button" href="#"><?php _e( 'Select all', 'email-control' ); ?></a> <a class="select_none button" href="#"><?php _e( 'Select none', 'email-control' ); ?></a>
						</td>
					</tr><?php
				break;

				// Default: run an action
				default:
					
					do_action( 'woocommerce_admin_field_' . $value['type'], $value );
				break;
			}
		}
	}
	
	/**
	 * Get a setting from the settings API.
	 *
	 * @param	mixed $option
	 * @return	string
	 */
	public static function get_option( $option_name, $default = '' ) {
		
		if ( strstr( $option_name, '[' ) ) {
			
			/**
			 * Array value
			 */
			
			parse_str( $option_name, $option_array );

			// Option name is first key
			$option_name = current( array_keys( $option_array ) );

			// Get value
			$option_values = get_option( $option_name, '' );

			$key = key( $option_array[ $option_name ] );

			if ( isset( $option_values[ $key ] ) ) {
				$option_value = $option_values[ $key ];
			}
			else {
				$option_value = null;
			}
		}
		else {
			
			/**
			 * Single value
			 */
			
			$option_value = get_option( $option_name, null );
		}

		if ( is_array( $option_value ) ) {
			
			$option_value = array_map( 'stripslashes', $option_value );
		}
		elseif ( ! is_null( $option_value ) ) {
			
			$option_value = stripslashes( $option_value );
		}

		return $option_value === null ? $default : $option_value;
	}
	
	/**
	 * Save admin fields.
	 *
	 * Loops though the options array and save each field.
	 *
	 * @access	public
	 * @param 	array $options Opens array to output
	 * @return	bool
	 */
	public static function save_fields( $options ) {
		
		// Bail if empty.
		// if ( empty( $_POST ) ) return false;

		// Options to update will be stored here
		$update_options = array();

		// Loop options and get values to save
		foreach ( $options as $value ) {

			if ( ! isset( $value['id'] ) )
				continue;

			$type = isset( $value['type'] ) ? sanitize_title( $value['type'] ) : '';

			// Get the option name
			$option_value = null;

			switch ( $type ) {
				
				// CX
				
				case 'checkbox' :

					if ( isset( $_POST[ $value['id'] ] ) && 'yes' == $_POST[ $value['id'] ] )
						$option_value = 'yes';
					else
						$option_value = 'no';

				break;
				
				case 'textarea' :

					if ( isset( $_POST[$value['id']] ) ) {
						
						$option_value = $_POST[ $value['id'] ];
						$option_value = stripslashes( $option_value );
						$option_value = trim( $option_value );
						$option_value = wp_kses_post( $option_value );
					}
					else {
						
						$option_value = '';
					}
					
				break;

				case 'text' :
				case 'email':
				case 'number':
				case 'select' :
				case 'color' :
				case 'password' :
				case 'single_select_page' :
				case 'single_select_country' :
				case 'radio' :
				case 'image_upload' :
				
					if ( isset( $_POST[$value['id']] ) ) {
						
						$option_value = $_POST[ $value['id'] ];
						$option_value = stripslashes( $option_value );
						$option_value = trim( $option_value );
						$option_value = wp_kses_post( $option_value );
					}
					else {
						
						$option_value = '';
					}
					
				break;
				
				// / CX

				// Special types
				case 'multiselect' :
				case 'multi_select_countries' :

					// Get countries array
					if ( isset( $_POST[ $value['id'] ] ) )
						$selected_countries = array_map( 'wc_clean', array_map( 'stripslashes', (array) $_POST[ $value['id'] ] ) );
					else
						$selected_countries = array();

					$option_value = $selected_countries;

				break;

				case 'image_width' :

					if ( isset( $_POST[$value['id'] ]['width'] ) ) {

						$update_options[ $value['id'] ]['width']  = wc_clean( stripslashes( $_POST[ $value['id'] ]['width'] ) );
						$update_options[ $value['id'] ]['height'] = wc_clean( stripslashes( $_POST[ $value['id'] ]['height'] ) );

						if ( isset( $_POST[ $value['id'] ]['crop'] ) )
							$update_options[ $value['id'] ]['crop'] = 1;
						else
							$update_options[ $value['id'] ]['crop'] = 0;

					}
					else {
						$update_options[ $value['id'] ]['width'] 	= $value['default']['width'];
						$update_options[ $value['id'] ]['height'] 	= $value['default']['height'];
						$update_options[ $value['id'] ]['crop'] 	= $value['default']['crop'];
					}

				break;

				// Custom handling
				default :

					do_action( 'woocommerce_update_option_' . $type, $value );

				break;

			}

			if ( ! is_null( $option_value ) ) {
				if ( strstr( $value['id'], '[' ) ) {
					
					/**
					 * Option is an Array.
					 */

					parse_str( $value['id'], $option_array );

					// Option name is first key
					$option_name = current( array_keys( $option_array ) );

					// Get old option value
					if ( ! isset( $update_options[ $option_name ] ) )
						 $update_options[ $option_name ] = get_option( $option_name, array() );

					if ( ! is_array( $update_options[ $option_name ] ) )
						$update_options[ $option_name ] = array();

					// Set keys and value
					$key = key( $option_array[ $option_name ] );

					$update_options[ $option_name ][ $key ] = $option_value;
				}
				else {
					
					/**
					 * Option is an Single.
					 */
					
					$update_options[ $value['id'] ] = $option_value;
				}
			}

			// Custom handling
			do_action( 'woocommerce_update_option', $value );
		}

		// Now save the options
		foreach( $update_options as $name => $value ) {
			
			if (
					str_replace( PHP_EOL, "\n", $value )
					!=
					str_replace( PHP_EOL, "\n", self::get_option_array( $name, 'default' ) )
				) {
				
				update_option( $name, $value );
				// update_option( $name, self::get_option_array( $name, 'default' ) );
			}
			else{
				
				delete_option( $name );
			}
		}

		return true;
	}
	
	/**
	 * Save Defaults
	 *
	 * @deprecated 2.0 Never used - rather use sane defaults
	 *
	 */
	function save_defaults( $id, $settings ) {
		
		foreach ( $settings as $setting_key => $setting_args ) {
			
			$field_id = implode( '_', array(
				'ec',
				$ec_required_email_themes_key,
				$setting_args["email-type" ],
				$setting_args["id"],
			) );
			
			if ( '' == get_option( $field_id ) && isset( $setting_args['default'] ) ) {
				update_option( $field_id, $setting_args['default'] );
			}
		}
	}
	
	/**
	 * Get a setting from the settings API.
	 *
	 * @param 	mixed $option
	 * @return	string Value of the option.
	 */
	public static function get_option_array( $option_name, $option_key = false ) {
		
		global $ec_cache_options;
		
		$return_value = false;
		
		if ( ! isset( $ec_cache_options ) ) {
			$ec_cache_options = ec_get_settings();
		}
		
		if ( isset( $ec_cache_options[$option_name] ) ) {
			$return_value = $ec_cache_options[$option_name];
		}
		
		// No option array found return false
		if ( ! $return_value ) {
			return false;
		}
		
		$defaults = array(
			'type'		=> '',
			'default'	=> '',
		);
		
		$return_value = wp_parse_args( $return_value, $defaults );
		
		if ( $option_key ) {
			if ( isset( $return_value[ $option_key ] ) )
				$return_value = $return_value[ $option_key ];
			else
				$return_value = false;
		}
		
		return $return_value;
	}
	
	/**
	 * Render Option
	 *
	 * @param mixed $option
	 * @return string
	 */
	
	public static function ec_default_option( $default ) {
		
		$option_name  = str_replace( 'default_option_', '', current_filter() );
		$option_value = self::get_option_array( $option_name, 'default' );
		$option_value = EC_Settings::ec_render_option( $option_name, $option_value );
		
		return $option_value;
	}
	
	/**
	 * Render Option
	 *
	 * @param 	mixed $option
	 * @return	string
	 */
	public static function ec_render_option( $option_name, $option_value = '' ) {
		
		// If no option value then return the default.
		if ( ! $option_value ) {
			$option_value = self::get_option_array( $option_name, 'default' );
		}
		
		// Use the posted value if we're in the customizer.
		if ( isset( $_REQUEST[$option_name] ) ) {
			$option_value = stripslashes( $_REQUEST[$option_name] );
		}
		
		// Translate certain content types, eg type, textarea.
		if ( in_array( self::get_option_array( $option_name, 'type' ), array( 'text', 'textarea' ) ) ) {
			
			$option_value = __( $option_value, 'email-control' );
		}
		
		// Do the shortcodes e.g. [ec_order] converted to #1434
		$option_value = do_shortcode( $option_value );
		
		// Texturize.
		$option_value = wptexturize( $option_value );
		
		// Auto paragraph certain content types, eg textarea
		if ( in_array( self::get_option_array( $option_name, 'type' ), array( 'textarea' ) ) ) {
			
			$option_value = wpautop( $option_value );
		}
		
		// REMOVED - was creating unwanted string bloar for translation plugins.
		// Translation (again) - pretty pointless tho after the dynamic shortcodes, and textures happen.
		// $option_value = __( $option_value, 'email-control' );
		
		return $option_value;
	}
}

/**
 * Get Sections.
 */
if ( ! function_exists( 'ec_get_sections' ) ) {
	function ec_get_sections( $theme_id = false ) {
		
		global $ec_email_themes;
		
		if ( ! isset( $ec_email_themes[$theme_id]['sections'] ) ) return false;
		
		return $ec_email_themes[$theme_id]['sections'];
	}
}

/**
 * Gets modified array of uniqe-ified keys for optionally specific selected settings.
 *
 * @param  boolean|string $theme_id Optional. Theme id of settings to get.
 * @param  array          $filter_args Args to further filter the config array e.g. array( 'email-type' => 'new_order' ).
 * @return array                       Settings array with the id names modified to be unique the way we need them.
 */
if ( ! function_exists( 'ec_get_settings' ) ) {
	function ec_get_settings( $theme_id = false, $filter_args = array() ) {
		
		global $ec_email_themes;
		
		// Collect the themes that the settings are required from.
		$ec_required_email_themes = $ec_email_themes;
		
		// If settings are only required from a specific theme (e.g. deluxe, supreme, etc).
		if ( $theme_id ) {
			
			// Bail if there are no settings for this theme.
			if ( empty( $ec_email_themes[$theme_id]['settings'] ) ) return FALSE;
			
			// Note the theme that is required.
			$ec_required_email_themes = array( $theme_id => $ec_email_themes[$theme_id] );
		}
		
		// Collect the newly constructed settings array.
		$settings_all = array();
		
		// Loop each `required_email_theme` and gather it's settings with uniquely modified id's.
		foreach ( $ec_required_email_themes as $ec_required_email_themes_key => $ec_required_email_themes_value ) {
			
			// Skip if there are not settings for this theme.
			if ( empty( $ec_required_email_themes_value["settings"] ) ) continue;
			
			// Get the settings.
			$settings = $ec_required_email_themes_value["settings"];
			
			// Check the settings config just before we process it.
			foreach ( $settings as $setting_key => $setting_args ) {
				
				// Make sure that `email-type` is set. If it was ommited by mistake then set it to `all` so that it's available to all the emails.
				if ( ! isset( $setting_args['email-type'] ) ) {
					$settings[$setting_key]['email-type'] = 'all';
				}
			}
			
			// Filter out anything specified in the filter args array e.g. array( 'email-type' => 'new_order', 'section' => 'text-section' ).
			foreach ( $filter_args as $filter_key => $filter_value ) {
				
				$filtered_settings = array();
				
				foreach ( $settings as $setting_key => $setting_args ) {
					if ( isset( $setting_args[$filter_key] ) && $setting_args[$filter_key] == $filter_value ) {
						$filtered_settings[] = $setting_args;
					}
				}
				
				$settings = $filtered_settings;
			}
			
			// Rename ID's to make them more unique. eg `heading` becomes `ec_deluxe_new_order_heading`.
			$renamed_id_settings = array();
			foreach ( $settings as $setting_key => $setting_args ) {
				
				$new_id = implode( '_', array(
					'ec',
					$ec_required_email_themes_key,
					$setting_args["email-type" ],
					$setting_args["id"],
				) );
				
				$setting_args["id"] = $new_id;
				$renamed_id_settings[$new_id] = $setting_args;
			}
			
			$settings_all = array_merge( $settings_all, $renamed_id_settings );
		}
		
		if ( empty( $settings_all ) ) $settings_all = FALSE;
		
		return $settings_all;
	}
}
