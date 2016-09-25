<?php 
  
    /*
    Plugin Name: WP Persistent Login
    Plugin URI: 
    Description: Keep users logged into your website forever, unless they explicitly log out.
    Author: B9 Media Ltd
    Version: 1.0.2
    Author URI: http://b9media.co.uk
    */
    
    
    /*  
    Copyright 2014 B9 Media Ltd  (email : info@b9media.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/
    
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    
    	
	    // install new features
	    global $pl_db_version;
		$pl_db_version = '1.0';
		
		function pl_install() {
			
			// fixes bug with phpFastCGI
			ini_set('zlib.output_handler', '');
			
			global $wpdb;

			// if updating, cretae new table & migrate data
			if( $wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "pl_logins'") != $wpdb->prefix . 'pl_logins' ) :
		
				
			// create new table
				global $pl_db_version;
			
				$table_name = $wpdb->prefix . 'pl_logins';
				
				/*
				 * We'll set the default character set and collation for this table.
				 * If we don't do this, some characters could end up being converted 
				 * to just ?'s when saved in our table.
				 */
				$charset_collate = '';
			
				if ( ! empty( $wpdb->charset ) ) {
				  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
				}
			
				if ( ! empty( $wpdb->collate ) ) {
				  $charset_collate .= " COLLATE {$wpdb->collate}";
				}
			
				$sql = "CREATE TABLE $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					user INT NOT NULL,
					login_key CHAR(40) NOT NULL,
					UNIQUE KEY id (id)
				) $charset_collate;";
			
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
			
				add_option( 'pl_db_version', $pl_db_version );

			
			

			// transfer data to new table	
				global $wpdb;
		
				$user_meta_table = $wpdb->prefix .'usermeta';
				$current_logins = $wpdb->get_results("SELECT * FROM $user_meta_table WHERE meta_key = 'login_key' OR meta_key = '_login_key'");
				
				if( $current_logins == true ) :
				
					foreach( $current_logins as $current_login ) :
						
						if( $current_login->meta_key == 'login_key' ) :
						
							$row = $current_login->umeta_id;
							$user = $current_login->user_id;
							$key = $current_login->meta_value;
							
							$wpdb->insert( $wpdb->prefix .'pl_logins', array( 'user' => $user, 'login_key' => $key ) );
							$wpdb->delete( $user_meta_table, array( 'umeta_id' => $row ), $where_format = null );
						
						elseif( $current_login->meta_key == '_login_key' ) :
						
							$row = $current_login->umeta_id;
							$wpdb->delete( $user_meta_table, array( 'umeta_id' => $row ), $where_format = null );
							
						endif;
					
					endforeach;
					
				endif;
			
			
			endif;
			
						
		}
		register_activation_hook( __FILE__, 'pl_install' );

		
		 
		
		// if the user isn't logged in, check for a valid cookie
	    function pl_login_check() {
	    	
	    	// fixes bug with phpFastCGI
	    	ini_set('zlib.output_handler', '');
	    
		    if( !is_user_logged_in() ) :
					
				// check if user has cookies
				if ( isset($_COOKIE['pl_i']) && isset($_COOKIE['pl_k']) ) :
					
					// store cookie info
					$id = $_COOKIE['pl_i'];
					$old_key = $_COOKIE['pl_k'];
					
					// check if user is in db
					global $wpdb;
					$table = $wpdb->prefix .'pl_logins';
					$user_check = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE user = %d AND login_key = %s", $id, $old_key));
					
					// if valid user is in db
					if( $user_check == true ) :
						
						// log the user in
						$user_login = get_user_by( 'id', $id );
						wp_set_current_user( $id, $user_login->user_login );
						wp_set_auth_cookie( $id );						
						do_action( 'wp_login', $user_login->user_login );
					
					endif; // end if user check
					
					
				endif; // end if cookies
							
			endif; // end if is logged in
		}
		add_action('wp', 'pl_login_check');
		
		
	
		
		// when a user is logged in, set/reset their cookie
		function pl_set_user_cookie($user_login) {
			
			// fixes bug with phpFastCGI
			ini_set('zlib.output_handler', '');
			
			// get user info
			$user_id = get_user_by( 'login', $user_login );
			$id = $user_id->ID;
			
			// generate new key for user
			$salt = wp_generate_password(20); // 20 character "random" string
			$key = sha1($salt . uniqid(time(), true));
						
			// set new cookies
			setcookie("pl_i", $id, time()+31536000);  /* expire in 1 year */
			setcookie("pl_k", $key, time()+31536000);  /* expire in 1 year */
			
			// check if user is in db
			global $wpdb;
			$table = $wpdb->prefix .'pl_logins';
			$user_check = $wpdb->get_results("SELECT login_key FROM $table WHERE user = $id");
		
			// if user is already in db
			if( $user_check == true ) :
		
					// update the db
					$wpdb->update( 
						$wpdb->prefix .'pl_logins', 
						array( 'login_key' => $key ), 
						array( 'user' => $id ), $format = null, 
						$where_format = null 
					);
			
			else :	

				// add new row to db
				$wpdb->insert( $wpdb->prefix .'pl_logins', array( 'user' => $id, 'login_key' => $key ) );
	
			endif; // end if user check
							
		}
		add_action('wp_login', 'pl_set_user_cookie', 10, 1);
		
		
		
		
		// remove the users cookie when they click logout
		function pl_remove_user_cookie() {
	
			unset($_COOKIE['pl_i']);
	        unset($_COOKIE['pl_k']);
	        setcookie('pl_i', '', time() - 3600);
	        setcookie('pl_k', '', time() - 3600);
		
		}
		add_action('wp_logout', 'pl_remove_user_cookie');

	     
?>