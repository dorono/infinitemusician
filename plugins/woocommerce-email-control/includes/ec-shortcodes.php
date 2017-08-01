<?php

$GLOBALS['EC_Shortcodes'] = new EC_Shortcodes();

class EC_Shortcodes {
	
	/**
	 * Construct and initialize the main plugin class
	 */
	public function __construct() {
		
		/*
		 * Init Shortcodes
		 */

		add_shortcode( 'ec_firstname', array( $this, 'ec_firstname' ) );
		add_shortcode( 'ec_lastname', array( $this, 'ec_lastname' ) );

		add_shortcode( 'ec_email', array( $this, 'ec_email' ) );

		add_shortcode( 'ec_order', array( $this, 'ec_order' ) );
		add_shortcode( 'ec_order_link', array( $this, 'ec_order' ) );
		add_shortcode( 'ec_user_order_link', array( $this, 'ec_order' ) );
		add_shortcode( 'ec_pay_link', array( $this, 'ec_pay_link' ) );

		add_shortcode( 'ec_customer_note', array( $this, 'ec_customer_note' ) );
		add_shortcode( 'ec_delivery_note', array( $this, 'ec_delivery_note' ) );

		add_shortcode( 'ec_coupon_code', array( $this, 'ec_coupon_code' ) );

		add_shortcode( 'ec_shipping_method', array( $this, 'ec_shipping_method' ) );
		add_shortcode( 'ec_payment_method', array( $this, 'ec_payment_method' ) );

		add_shortcode( 'ec_user_login', array( $this, 'ec_user_login' ) );
		add_shortcode( 'ec_account_link', array( $this, 'ec_account_link' ) );
		add_shortcode( 'ec_user_password', array( $this, 'ec_user_password' ) );
		add_shortcode( 'ec_reset_password_link', array( $this, 'ec_reset_password_link' ) );
		add_shortcode( 'ec_login_link', array( $this, 'ec_login_link' ) );
		add_shortcode( 'ec_site_link', array( $this, 'ec_site_link' ) );
		add_shortcode( 'ec_site_name', array( $this, 'ec_site_name' ) );

		add_shortcode( 'ec_custom_field', array( $this, 'ec_custom_field' ) );
	}
	

	/*
	 * Define Shortcodes
	 */

	public static function ec_firstname( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		// Check if necessary email args exits
		if ( ! isset( $ec_email_args['first_name'] ) ) return;
		
		$content = $ec_email_args['first_name'];
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_firstname">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}

	public static function ec_lastname( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		// Check if necessary email args exits
		if ( ! isset( $ec_email_args['last_name'] ) ) return;
		
		$content = $ec_email_args['last_name'];
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_lastname">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}

	public static function ec_email( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		// Check if necessary email args exits
		if ( ! isset( $ec_email_args['email'] ) ) return;
		
		$content = $ec_email_args['email'];
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_email">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}

	public static function ec_order( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => '#, number, date, link, container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		// Check if this shortcode can be used here
		if ( ! isset( $ec_email_args['order'] ) ) {
			if ( isset( $_REQUEST["ec_render_email"] ) ) return '<span class="shortcode-error">' . __( 'This shortcode cannot be used in this email', 'email-control' ) . '</span>';
			else return;
		}
		
		// Check if necessary email args exits
		if ( ! isset( $ec_email_args['order'] ) ) {
			if ( isset( $_REQUEST["ec_render_email"] ) ) return '<span class="shortcode-error">[' . __( 'Order shortcodes cannot be used in this email', 'email-control' ) . ']</span>';
			else return;
		}
		
		if ( isset( $ec_email_args['sent_to_admin'] ) && $ec_email_args['sent_to_admin'] ) {
			
			//Admin Order URL
			$order_url = admin_url( 'post.php?post=' . ec_order_get_id( $ec_email_args['order'] ) . '&action=edit' );
		}
		else {
			
			//Front End Order URL
			$order_url = $ec_email_args['order']->get_view_order_url();
		}
		
		// s( $shortcode_args_modified );
		// self::check_display( $shortcode_args_modified, '#' );
		// s( $shortcode_args_modified );
		
		//start the return output.
		$content = '';
		
		if ( self::check_display( $shortcode_args_modified, '#' ) || self::check_display( $shortcode_args_modified, 'number' ) ) {
			
			if ( self::check_display( $shortcode_args_modified, 'link' ) ) {
				$content .= '<a href="' . $order_url . '">';
			}
			
			if ( self::check_display( $shortcode_args_modified, '#' ) ) {
				$content .= '#';
			}
			
			if ( self::check_display( $shortcode_args_modified, 'number' ) ) {
				$content .= ltrim( $ec_email_args['order']->get_order_number(), '#' );
			}
			
			if ( self::check_display( $shortcode_args_modified, 'link' ) ) {
				$content .= '</a>';
			}
			
			// Add space.
			$content .= " ";
		}
		
		if ( self::check_display( $shortcode_args_modified, 'date' ) ) {
			
			$temp_date_created = ec_order_get_date_created( $ec_email_args['order'] );
			
			// `->format()` only since WC3.0.
			if ( method_exists( $temp_date_created, 'format' ) ) {
				$content .= '<span class="ec_datetime">(' . sprintf( '<time datetime="%s">%s</time>', $temp_date_created->format( 'c' ), wc_format_datetime( $temp_date_created ) ) . ')</span>';
			}
			else {
				$content .= '<span class="ec_datetime">(' . sprintf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $temp_date_created ) ), date_i18n( wc_date_format(), strtotime( $temp_date_created ) ) ) . ')</span>';
			}
			
			// Add space.
			$content .= " ";
		}
		
		// Trim spaces beginning and end.
		$content = trim( $content );
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_order">' . $content . '</span>';
		}
		
		return $content;
	}

	public static function ec_customer_note( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		// Check if this shortcode can be used here
		if ( ! isset( $ec_email_args['order'] ) ) {
			if ( isset( $_REQUEST["ec_render_email"] ) ) return '<span class="shortcode-error">' . __( 'This shortcode cannot be used in this email', 'email-control' ) . '</span>';
			else return;
		}
		if ( isset( $_REQUEST["ec_email_type"] ) && $_REQUEST["ec_email_type"] !== 'customer_note' ) {
			if ( isset( $_REQUEST["ec_render_email"] ) ) return '<span class="shortcode-error">' . __( 'This shortcode cannot be used in this email', 'email-control' ) . '</span>';
			else return;
		}
			
		$content = '';
		
		// Check if necessary email args exits
		if ( isset( $ec_email_args['customer_note'] ) ) {
			$content = $ec_email_args['customer_note'];
		}
		elseif ( isset( $_REQUEST["ec_email_theme"] ) ) {
			$content = '*** Note will be inserted here ***';
		}
		
		if ( '' == $content ) {
			return;
		}
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_customer_note">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}

	public static function ec_delivery_note( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		// Check if necessary email args exits
		if ( ! isset( $ec_email_args['delivery_note'] ) ) return;
		
		$content = $ec_email_args['delivery_note'];
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_delivery_note">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}

	public static function ec_coupon_code( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		// Check if necessary email args exits
		if ( ! isset( $ec_email_args['coupon_code'] ) ) return;
		
		$content = $ec_email_args['coupon_code'];
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_coupon_code">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}

	public static function ec_shipping_method( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		// Check if necessary email args exits
		if ( ! isset( $ec_email_args['shipping_method'] ) ) return;
		
		$content = $ec_email_args['shipping_method'];
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_shipping_method">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}

	public static function ec_payment_method( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		// Check if necessary email args exits
		if ( ! isset( $ec_email_args['payment_method'] ) ) return;
		
		$content = $ec_email_args['payment_method'];
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_payment_method">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}

	public static function ec_account_link( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		$link = get_permalink( wc_get_page_id( 'myaccount' ) );
		
		ob_start();
		?><a href="<?php echo $link; ?>"><?php echo $link; ?></a><?php
		$content = ob_get_clean();
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_account_link">' . $content . '</span>';
		}
		
		return $content;
	}

	public static function ec_reset_password_link( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		$query_args = array();
		
		if ( isset( $ec_email_args['reset_key'] ) && isset( $ec_email_args['user_login'] ) ) {
			$query_args = array(
				'key' => $ec_email_args['reset_key'],
				'login' => rawurlencode( $ec_email_args['user_login'] )
			);
		}
		
		$link = esc_url_raw( add_query_arg(
			$query_args,
			wc_get_endpoint_url( 'lost-password', '', get_permalink( wc_get_page_id( 'myaccount' ) ) )
		) );
		
		// Allow custom text as shortcode args.
		$text = __( 'Click here to reset your password', 'email-control' );
		if ( isset( $shortcode_args_modified['text'] ) && trim( $shortcode_args_modified['text'] ) )
			$text = __( $shortcode_args_modified['text'], 'email-control' );
		
		ob_start();
		?><a href="<?php echo $link; ?>"><?php echo $text; ?></a><?php
		$content = ob_get_clean();
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_reset_password_link">' . $content . '</span>';
		}
		
		return $content;
	}

	public static function ec_user_password( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		// Check if this shortcode can be used here
		if ( isset( $ec_email_args['order'] ) ) {
			if ( isset( $_REQUEST["ec_render_email"] ) ) return '<span class="shortcode-error">' . __( 'This shortcode cannot be used in this email', 'email-control' ) . '</span>';
			else return;
		}
		
		$content = '';
		
		// Check if necessary email args exits
		if ( isset( $ec_email_args['user_pass'] ) ) {
			$content = esc_html( $ec_email_args['user_pass'] );
		}
		elseif ( isset( $_REQUEST["ec_email_theme"] ) ) {
			$content = '#%ZiZi$%#kL#';
		}
		
		if ( '' == $content ) {
			return;
		}
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_user_password">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}

	public static function ec_pay_link( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		// Check if this shortcode can be used here
		if ( ! isset( $ec_email_args['order'] ) ) {
			if ( isset( $_REQUEST["ec_render_email"] ) ) return '<span class="shortcode-error">' . __( 'This shortcode cannot be used in this email', 'email-control' ) . '</span>';
			else return;
		}
		
		// Check if necessary email args exits
		$link = esc_url_raw( $ec_email_args['order']->get_checkout_payment_url() );
		if ( ! $link ) return;
		
		// Allow custom text as shortcode args.
		$text = __( 'Pay now', 'email-control' );
		if ( isset( $shortcode_args_modified['text'] ) && trim( $shortcode_args_modified['text'] ) )
			$text = __( $shortcode_args_modified['text'], 'email-control' );
		
		ob_start();
		?><a href="<?php echo $link; ?>"><?php echo $text; ?></a><?php
		$content = ob_get_clean();
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_pay_link">' . $content . '</span>';
		}
		
		return $content;
	}

	public static function ec_login_link( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		$link = get_permalink( wc_get_page_id( 'myaccount' ) );
		
		ob_start();
		?><a href="<?php echo $link; ?>"><?php echo $link; ?></a><?php
		$content = ob_get_clean();
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_login_link">' . $content . '</span>';
		}
		
		return $content;
	}

	public static function ec_site_link( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		ob_start();
		?><a href="<?php echo esc_url_raw( get_site_url() ) ?>"><?php echo get_bloginfo( 'name' ); ?></a><?php
		$content = ob_get_clean();
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_site_link">' . $content . '</span>';
		}
		
		return $content;
	}

	public static function ec_site_name( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		$content = get_bloginfo( 'name' );
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_site_name">' . $content . '</span>';
		}
		
		return $content;
	}

	public static function ec_custom_field( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		// s( $shortcode_args_modified['key'] );
		// s( get_post_meta( ec_order_get_id( $ec_email_args['order'] ), $shortcode_args_modified['key'], TRUE ) );
		// s( get_post_meta( ec_order_get_id( $ec_email_args['order'] ) ) );
		// exit();
		
		// Check if necessary email args exits
		if ( ! isset( $shortcode_args_modified['key'] ) && ! isset( $ec_email_args['order'] ) ) return;
		
		$content = get_post_meta(
			ec_order_get_id( $ec_email_args['order'] ),
			$shortcode_args_modified['key'],
			TRUE
		);
		
		// Bail if there's no corresponding data to fetch.
		if ( ! $content ) return;
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_custom_field ec_' . trim( $shortcode_args_modified['key'] ) . '">' . trim( $content ) . '</span>';
		}
		
		return $content;
	}

	public static function ec_user_login( $shortcode_args ) {
		
		// Shortcode args
		// ---------------------------
		
		// Set shortcode args defaults
		$shortcode_args_defaults = array(
			'show' => 'container',
			'hide' => '',
		);
		
		// Merge shortcode args with defaults
		$shortcode_args_modified = wp_parse_args( $shortcode_args, $shortcode_args_defaults );
		
		// Compile content
		// ----------------------------
		global $ec_email_args;
		self::ec_normalize_email_args();
		
		$content = esc_html( $ec_email_args['user_login'] );
		
		// Add Container (optional).
		if ( self::check_display( $shortcode_args_modified, 'container' ) ) {
			$content = '<span class="ec_shortcode ec_user_login">' . $content . '</span>';
		}
		
		return $content;
	}


	/**
	 * Helper Functions.
	 */

	/**
	 * Helper to check whether to show shortcode elements.
	 */
	public static function check_display( $shortcode_args, $check ) {
		
		// Get the values.
		$show = $shortcode_args['show'];
		$hide = $shortcode_args['hide'];
		
		// Convert to arrays
		$show = array_map( 'trim', explode( ',', $show ) );
		$hide = array_map( 'trim', explode( ',', $hide ) );
		
		//Remove any show's that have been set as hide's
		foreach ( $hide as $key => $value ) {
			if ( in_array( $value, $show ) ) {
				unset( $show[array_search( $value, $show )] );
			}
		}
		
		$return = in_array( $check, $show );
		
		return $return;
	}

	public static function ec_normalize_email_args() {
		
		global $ec_email_args;
		
		$defaults = array(
			'user_id'         => '' ,
			'user_login'      => ( isset( $_REQUEST["ec_render_email"] ) ) ? '**username**' : '' ,
			'user_nicename'   => ( isset( $_REQUEST["ec_render_email"] ) ) ? '**nicename**' : '' ,
			'email'           => ( isset( $_REQUEST["ec_render_email"] ) ) ? '**email**' : '' ,
			'first_name'      => ( isset( $_REQUEST["ec_render_email"] ) ) ? '**firstname**' : '' ,
			'last_name'       => ( isset( $_REQUEST["ec_render_email"] ) ) ? '**lastname**' : '' ,
			'delivery_note'   => ( isset( $_REQUEST["ec_delivery_note"] ) ) ? '**delivery_note**' : '' ,
			'shipping_method' => ( isset( $_REQUEST["ec_shipping_method"] ) ) ? '**shipping_method**' : '' ,
			'payment_method'  => ( isset( $_REQUEST["ec_payment_method"] ) ) ? '**payment_method**' : '' ,
			'coupon_code'     => ( isset( $_REQUEST["ec_coupon_code"] ) ) ? '**coupon_code**' : '' ,
		);
		
		$ec_email_args = wp_parse_args( $ec_email_args, $defaults );
		
		if ( isset( $ec_email_args['order'] ) ) {
			
			$order = $ec_email_args['order'];
			$order_id = ec_order_get_id( $order );
			
			// Get Delivery Note.
			$ec_email_args['delivery_note'] = $order->customer_note; // "The blue house at the end of the street".
			
			// Get Shipping Method.
			if ( 'yes' == get_option( 'woocommerce_calc_shipping' ) )
				$ec_email_args['shipping_method'] = $order->get_shipping_method(); // "Free Shipping".
			
			// Get Payment Method.
			$ec_email_args['payment_method'] = $order->payment_method_title; // "Paypal".
			
			// Get Coupon Names.
			if ( $order->get_used_coupons() ) {
				$coupons_list = array();
				foreach ( $order->get_used_coupons() as $coupon ) {
					$coupons_list[] = $coupon;
				}
				$ec_email_args['coupon_code'] = implode( ', ', $coupons_list );
			}
			
			if ( ( $user_id = get_post_meta( $order_id, '_customer_user', true ) ) ) {
				
				$user = get_user_by( 'id', $user_id );
				
				// All emails with Orders in them - with an existing user account
				
				if ( isset( $user->user_login ) ) $ec_email_args['user_login'] = $user->user_login ; // brentvr
				if ( isset( $user->user_nicename ) ) $ec_email_args['user_nicename'] = $user->user_nicename ; // BrentVR
				if ( $billing_email = get_post_meta( $order_id, '_billing_email', true ) ) $ec_email_args['email'] = $billing_email ; // brentvr@gmail.com
				if ( $billing_first_name = get_post_meta( $order_id, '_billing_first_name', true ) ) $ec_email_args['first_name'] = $billing_first_name ; // Brent
				if ( $billing_last_name = get_post_meta( $order_id, '_billing_last_name', true ) ) $ec_email_args['last_name'] = $billing_last_name ; // VanRensburg
			}
			else{
				
				// All emails with Orders in them - without a user account
				
				$ec_email_args['user_login'] = ''; //$user->user_login; // brentvr
				$ec_email_args['user_nicename'] = ''; //$user->user_nicename; // BrentVR
				if ( $billing_email = get_post_meta( $order_id, '_billing_email', true ) ) $ec_email_args['email'] = get_post_meta( $order_id, '_billing_email', true ); // brentvr@gmail.com
				if ( $billing_first_name = get_post_meta( $order_id, '_billing_first_name', true ) ) $ec_email_args['first_name'] = get_post_meta( $order_id, '_billing_first_name', true ); // Brent
				if ( $billing_last_name = get_post_meta( $order_id, '_billing_last_name', true ) ) $ec_email_args['last_name'] = get_post_meta( $order_id, '_billing_last_name', true ); // VanRensburg
			}
		}
		elseif ( isset( $ec_email_args['user_login'] ) ) {
			
			// New account email.
			
			$user = get_user_by( 'login', $ec_email_args['user_login'] );
			
			if ( isset( $user->ID ) ) $ec_email_args['user_id'] = $user->ID; // 2
			if ( isset( $user->user_login ) ) $ec_email_args['user_login'] = $user->user_login; // brentvr@gmail.com
			if ( isset( $user->user_nicename ) ) $ec_email_args['user_nicename'] = $user->user_nicename; // brentvr@gmail.com
			if ( isset( $user->user_email ) ) $ec_email_args['email'] = $user->user_email; // brentvr@gmail.com
			
			if ( ( $first_name = get_user_meta( $ec_email_args['user_id'], 'first_name', true ) ) )
				$ec_email_args['first_name'] = $first_name; // Brent
			elseif ( isset( $_POST['billing_first_name'] ) )
				$ec_email_args['first_name'] = $_POST['billing_first_name']; // Brent
			
			if ( ( $last_name = get_user_meta( $ec_email_args['user_id'], 'last_name', true ) ) )
				$ec_email_args['last_name'] = $last_name; // Van Rensburg
			elseif ( isset( $_POST['billing_last_name'] ) )
				$ec_email_args['last_name'] = $_POST['billing_last_name']; // Van Rensburg
		}
	}
	
}
