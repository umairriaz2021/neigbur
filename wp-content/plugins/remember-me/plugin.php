<?php
/*
Plugin Name: Remember Me
Plugin URI: 
Version: 1.0
Description: Remember me 
Author: Libin Anto
Author URI: snapd.com
*/

// Hook stuff in
function wp_remember_me_init() {
	add_action( 'wp_footer', 'wp_remember_me_js' );
	add_filter( 'auth_cookie_expiration', 'wp_remember_me_cookie', 10, 3 );
}
add_action( 'init', 'wp_remember_me_init' );

// JS that checks the checkbox
function wp_remember_me_js() {
	echo "
	<script>
	document.getElementById('rememberme').checked = true;
	document.getElementById('user_login').focus();
	</script>";
}

function wp_remember_me_cookie() {
	return 31536000; // one year
}