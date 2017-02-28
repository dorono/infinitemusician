<?php
/*
Plugin Name: Anti-Captcha
Plugin URI: http://blog.fili.nl/wordpress-anti-captcha-plugin/
Description: Anti-Captcha is a transparent captcha solution which does not require any end-user interaction
Version: 20141103
Author: Filidor Wiese
Author URI: http://www.fili.nl/
*/

define("ANTI_CAPTCHA_ERROR", __("<b>Error submitting form!</b><br />Please make sure both javascript and cookies are enabled in your browser.<br />Use the back button to try again...<br /><br /><i>(Note: if this still doesn't help, then refreshing your cache might)</i>", 'anti_captcha'));

add_action('login_head', 'anti_captcha_head');
//add_action('wp_authenticate', 'anti_captcha_process_login');
add_action('wp_head', 'anti_captcha_head');
add_action('register_post','anti_captcha_process_registration',10,3);
add_action('lostpassword_post','anti_captcha_process_lostpassword',10,3);
add_filter('preprocess_comment', 'anti_captcha_process_comment');

if (!function_exists('anti_captcha_head')) {
    function anti_captcha_head()
    {
        if (is_user_logged_in()) return;
        
        wp_enqueue_script('anti-captcha', '/wp-content/plugins/anti-captcha/anti-captcha-0.3.js.php', array(), md5(rand(1111,9999)));
        wp_print_scripts('anti-captcha');
    }
}

if (!function_exists('anti_captcha_verify_token')) {
    function anti_captcha_verify_token($token)
    {
        if (sha1($token) == $_COOKIE['anti-captcha-crc']) {
            setcookie('anti-captcha-crc', sha1(rand(1111,9999)), time() + 3600, '/');
            return true;
        }
        
        return false;
    }
}

if (!function_exists('anti_captcha_process_comment')) {
    function anti_captcha_process_comment($incoming_comment)
    {
        // Defaults to unknown
        $commentStatus = null;

        // Approve comment if user is logged in or provides a valid anti-capcha-token
        if (is_user_logged_in() || anti_captcha_verify_token($_POST['anti-captcha-token'])) {
            // If a mailaddress is provided, check it for format and MX-records
            // If this test fails, hold comment for moderation
            if (strlen($incoming_comment['comment_author_email'])) {
                if (!anti_captcha_validate_email($incoming_comment['comment_author_email'])) {
                    $commentStatus = '0';
                }
            }

            // Detect PhantomJs headless browsers by user-agent, spam comment if found
            if (stristr($_SERVER['HTTP_USER_AGENT'], 'phantomjs')) {
                $commentStatus = 'spam';
            }
        } else {
            // No valid anti-captcha-token
            $commentStatus = 'spam';
        }
        
        if ($commentStatus !== null) {
            add_filter('pre_comment_approved', create_function('$a', "return '" . $commentStatus . "';"));
        }

        return $incoming_comment;
    }
}

/*
// Uncomment this if you want Anti-Captcha to also work on the login form
if (!function_exists('anti_captcha_process_login')) {
    function anti_captcha_process_login()
    {
        if (count($_POST)) {
            if (!anti_captcha_verify_token($_POST['anti-captcha-token'])) {
                wp_die(ANTI_CAPTCHA_ERROR);
            }
        }
    }
}
*/

if (!function_exists('anti_captcha_process_registration')) {
    function anti_captcha_process_registration($login, $email, $errors)
    {
        if (!anti_captcha_verify_token($_POST['anti-captcha-token'])) {
            $errors->add('anti_captcha_error', ANTI_CAPTCHA_ERROR);
        }
    }
}

if (!function_exists('anti_captcha_process_lostpassword')) {
    function anti_captcha_process_lostpassword()
    {
        if (!anti_captcha_verify_token($_POST['anti-captcha-token'])) {
            wp_die(ANTI_CAPTCHA_ERROR);
        }
    }
}

if (!function_exists('anti_captcha_validate_email')) {
    function anti_captcha_validate_email($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Some OS's don't support this
        if (function_exists('dns_get_record')) {
            $emailParts = explode('@', $email);
            if (count(dns_get_record($emailParts[1], DNS_MX)) < 1) {
                return false;
            }
        }

        return true;
    }
}
