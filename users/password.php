<?php

add_filter( 'retrieve_password_title', 'lowrez_retrieve_password_title' );
function lowrez_retrieve_password_title( $title ) {
	return 'Password Reset on LOW REZ';
}
add_filter( 'retrieve_password_message', 'lowrez_retrieve_password_message' );
function lowrez_retrieve_password_message( $message, $key = false ) {
	
	if (preg_match('%<(https?://[^>]+)>%i', $message, $matches)) {
		//if (preg_match('/<(https?:\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$])>/i', $message, $regs)) {
		$link = $matches[1];
	} /*else {
		$link = '-cannot-'; //htmlspecialchars($message, true);
		}*/
	
	$new_message = sprintf('<p>A request has been made to change your password on the LOW REZ Members\' website.</p>
<p>If this was a mistake, you can ignore this message and nothing will change.</p>
<p>To reset your password, visit the following address: <a href="%s">%s</a></p>
<br>
<p><i>This is an automated message. Please do not reply to this message.</i></p>', $link, $link);//, htmlspecialchars($message));
	
	return $new_message;
}

if ( !function_exists('wp_generate_password') ) :
/**
* Generates a random password drawn from the defined set of characters.
*
* @since 2.5
*
* @param int $length The length of password to generate
* @param bool $special_chars Whether to include standard special characters. Default true.
* @param bool $extra_special_chars Whether to include other special characters. Used when
*   generating secret keys and salts. Default false.
* @return string The random password
**/
function wp_generate_password( $length = 8, $special_chars = false, $extra_special_chars = false ) {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	if ( $special_chars )
		$chars .= '0123456789!@#$%^&*()';
	if ( $extra_special_chars )
		$chars .= '-_ []{}<>~`+=,.;:/?|';
	
	$password = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$password .= substr($chars, wp_rand(0, strlen($chars) - 1), 1);
	}
	
	// random_password filter was previously in random_password function which was deprecated
	return apply_filters('random_password', $password);
}
endif;
