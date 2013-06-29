<?php

/*function lowrez_validate_registration($errors, $login, $email) {
$errors->add('demo_error',__('This is a demo error, and will halt registration'));
return $errors;
}
add_filter('registration_errors', 'lowrez_validate_registration', 10, 3);*/


add_filter('show_password_fields', 'lowrez_newuser_fields');
function lowrez_newuser_fields() {
	
	$screen = get_current_screen();
	if ( $screen->action == 'add' && $screen->base == 'user' ) {
?>
<tr class="form-field">
	<th scope="row"><label for="voicepart"><?php _e('Voice Part') ?> <span class="description">(if applicable)</span></label></th>
	<td><?php $value = @$_POST['voicepart']; ?>
		<select name="voicepart" id="voicepart">
			<option value="" <?php selected($value, ''); ?>>&mdash;</option>
			<option value="t1" <?php selected($value, 't1'); ?>>Tenor 1</option>
			<option value="t2" <?php selected($value, 't2'); ?>>Tenor 2</option>
			<option value="bar" <?php selected($value, 'bar'); ?>>Baritone</option>
			<option value="b" <?php selected($value, 'b'); ?>>Bass</option>
		</select>
	</td>
</tr>
<?php
															   }
	return true;
}

add_action('user_register', 'lowrez_new_username', 10);
function lowrez_new_username($user_id) {
	
	update_user_meta( $user_id, 'voicepart', @$_POST['voicepart'] );
	
	$usermeta = get_user_meta( $user_id );
	if ($username = lowrez_make_username($usermeta['first_name'][0], $usermeta['last_name'][0])) {
		
		global $wpdb;
		$wpdb->update( $wpdb->users, array('user_login' => $username, 'user_nicename' => sanitize_title($username)), array('ID' => $user_id) );
		
		//wp_cache_delete($user[ 'user_email' ], 'useremail');
		clean_user_cache($user_id);
	}
	
	lowrez_user_profile_change($user_id, false); // Add groups
	
}

function lowrez_make_username($first, $last) {
	
	//$first = str_replace(array(' ', '-', '\''), '', $first);
	//$last = str_replace(array(' ', '\''), '', $last);
	
	$first = trim($first);
	$last = trim($last);
	
	$first = str_replace(array('-', '\''), '', $first);
	
	if ($first || $last) {
		
		$firsts = explode(' ', $first);
		
		$first = array_shift($firsts);
		
		foreach ($firsts as $name) {
			$first .= substr($name, 0, 1);
		}
		
		$lasts = explode('-', $last);
		
		if (count($lasts) == 2) {
			$u = sanitize_user(strtolower($first . substr($lasts[0], 0, 1) . substr($lasts[1], 0, 1)));
			//echo $u.'; ';
			if (!username_exists($u)) {
				return $u;
			}
		}
		else {
			$last = str_replace('-', '', $last);
		}
		
		$iters = strlen($last);
		
		for ($i = 1; $i <= $iters; $i++) {
			$u = sanitize_user(strtolower($first . substr($last, 0, $i)));
			//echo $u.'; ';
			if (!username_exists($u)) {
				return $u;
			}
		}
		
		$iters = strlen($first);
		
		for ($i = 1; $i <= $iters; $i++) {
			$u = sanitize_user(strtolower(substr($first, 0, $i) . $last));
			//echo $u.'; ';
			if (!username_exists($u)) {
				return $u;
			}
		}
		
		for ($i = 1; $i <= 10; $i++) {
			$u = sanitize_user(strtolower($first . $last . $i));
			//echo $u.'; ';
			if (!username_exists($u)) {
				return $u;
			}
		}
		
	}
	else {
		return false;
	}
	
	
}



if ( !function_exists('wp_new_user_notification') ) :
/**
* Notify the blog admin of a new user, normally via email.
*
* @since 2.0
*
* @param int $user_id User ID
* @param string $plaintext_pass Optional. The user's plaintext password
*/
function wp_new_user_notification($user_id, $plaintext_pass = '', $extra_message = false) {
	$user = get_userdata( $user_id );
	
	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);
	$user_name = stripslashes($user->display_name);
	
	$message = sprintf('<p>A new member has been registered on the LOW REZ Members\' website.</p>
<table>
<tr><th>Name</th><td>%s</td></tr>
<tr><th>Username</th><td>%s</td></tr>
<tr><th>Email</th><td>%s</td></tr>
</table>', $user_name, $user_login, $user_email);
	
	//@wp_mail(get_option('admin_email'), 'New Member Registration', $message);
	
	if ( empty($plaintext_pass) )
		return;
	
	$message = sprintf('<p>Welcome to the LOW REZ Members\' website.</p>
<p>You may log in at <a href="%s">%s</a> with the following details:</p>
<table>
<tr><th>Username</th><td>%s</td></tr>
<tr><th>Password</th><td>%s</td></tr>
</table>
%s', wp_login_url(), home_url(), $user_login, $plaintext_pass, $extra_message);
	
	wp_mail($user_email, 'Welcome to LOW REZ Members', $message, 'From: system@lowrez.com.au');
	//wp_mail(get_option('admin_email'), 'New registration to LOW REZ Members', $message);
	
}
endif;