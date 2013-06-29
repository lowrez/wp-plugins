<?php

require_once('last-login/wp-last-login.php');

function is_unread($post_id, $float=false, $fakeit=false) {
	
	$post = get_post($post_id);
	$the_date = strtotime($post->post_modified);
	$the_user_date = second_last_login();
	
	if ($the_user_date < $the_date || $fakeit) {
		$float = $float ? ' style="float:right;margin-top:1px;margin-left:1px;"' : false;
		return ' <span class="label label-warning"'.$float.' title="Posted since your last login">New</span>';
	}
	
}

function second_last_login($user_id = false) {
	
	if (!$user_id) $user_id = get_current_user_id();
	
	$last_login	=	get_user_meta( $user_id, 'wp-last-login', true );
	
	if ( $last_login ) {
		if (is_array($last_login)) {
			array_pop($last_login);
			$last_login = array_pop($last_login);
		}
		
		$last_login = empty($last_login) ? false : $last_login;
	}
	
	return $last_login;
}