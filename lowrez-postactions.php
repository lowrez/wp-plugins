<?php

/*
Plugin Name: LOW REZ Post Actions
Description: LOW REZ Post Actions - eNews, SMS
Author: LOW REZ
Version: 1.0
*/

/*function lowrez_expired_post_status() {
	$args = array(
		'label'                     => _x( 'expired', 'Status General Name', 'lowrez' ),
		'label_count'               => _n_noop( 'Expired (%s)',  'Expired (%s)', 'lowrez' ),
		'public'                    => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'exclude_from_search'       => false,
	);

	register_post_status( 'expired', $args );
}*/

// Hook into the 'init' action
//add_action( 'init', 'lowrez_expired_post_status', 0 );



add_action( 'admin_enqueue_scripts', 'lowrez_category_icons' );
function lowrez_category_icons( $hook_suffix ) {
	
	if ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) {
		wp_enqueue_style( 'lowrez-categories', plugins_url( 'postactions/postactions.css' , __FILE__ ) );
	}
	/*if ( 'profile.php' == $hook_suffix || 'user-edit.php' == $hook_suffix ) {
		wp_enqueue_script( 'lowrez-profile', plugins_url( 'users/profile.js' , __FILE__ ) );
		}*/
}

//add_action		( 'do_meta_boxes',	'lowrez_publish_metabox', 10, 2	);

function lowrez_publish_metabox( $page, $context ) {
	
	if (!current_user_can('send_enews') && !current_user_can('send_sms')) 
		return;
	
	//$yourls_options = get_option('yourls_options');
	
	/*if(	empty($yourls_options['api']) || empty($yourls_options['url']) )
	return;*/
	
	// check post status, only display on posts with URLs
	/*$status		= get_post_status();
	if ( $status == 'publish' ||
	$status == 'future' ||
	$status == 'pending'*/
	// )
	// {
		
		
		// now set array of post types
	//$customs	= isset($yourls_options['typ']) ? $yourls_options['typ'] : false;
		$types		= array('post' => 'post', 'page' => 'page');
		
		if ( in_array( $page,  $types ) && 'side' == $context )
			add_meta_box('lowrez-publish', __('Member Communication', 'lowrez'), 'lowrez_publish_metabox_display', $page, $context, 'high');
	//}
}

function lowrez_publish_metabox_display() {
	
	global $post;
	$cats = wp_get_post_categories($post->ID);
	$status = $post->post_status;
	
	if ($status == 'publish') {
	
	if (in_array(13, $cats)) { //eNews
		$method = 'eNews';
	}
	elseif (in_array(19, $cats)) { //SMS Alert
		$method = 'SMS';
	}
	else { return; }
	
	$sent	= get_post_meta($post->ID, $method.'_sent', true);
		
	if ($sent) {
		
		$sent_date = 'date';
		
		echo '<p class="howto">' . sprintf( 'Sent to members by %s on: <strong>%s</strong>.', $method, $sent_date );
	}
	else {
		echo '<p class="howto">'. sprintf('Not sent to members by %s.', $method) .'</p>';
	}
	
	
	echo '<hr />
<input name="send_members" type="button" class="button button-primary button-large" id="send_members" value="Send">
';
		
	}
	else {
	
		
		echo '<p class="howto">To send this post to members as eNews or SMS, you must select the appropriate Category and click Publish.</p>';
		
	}
		
}


function lowrez_publish_post($post_id) {
	$post = get_post($post_id);
	$cats = wp_get_post_categories($post_id);
	
	
	//wp_die('<pre>'.print_r($post, true).'</pre>');
	
	//if ($post->post_date == $post->post_modified) { // Not previously published
	
	if (in_array(13, $cats)) { //eNews
		if (current_user_can('send_enews')) {
			lowrez_send_enews($post);
		}
		else {
			// Update post
			$my_post = array();
			$my_post['ID'] = $post_id;
			$my_post['post_status'] = 'draft';
			wp_update_post( $my_post );
			//add_action('admin_notices', 'lowrez_error_enews');
			//
			//return new WP_Error('cannot-send-enews', 'You are not allowed to send eNews to members.');
		}
	}
	
	if (in_array(19, $cats)) { //SMS Alert
		if (current_user_can('send_sms')) {
			lowrez_send_sms($post);
		}
		else {
			$my_post = array();
			$my_post['ID'] = $post_id;
			$my_post['post_status'] = 'draft';
			wp_update_post( $my_post );
			//add_action('admin_notices', 'lowrez_error_enews');
			//echo "<div class='error'><p>You are not allowed to send SMS Alerts to members.</p></div>";
			//return new WP_Error('cannot-send-sms', 'You are not allowed to send SMS Alerts to members.');
		}
	}
	
	//}
	
	
}
////add_action('publish_post', 'lowrez_publish_post');


function lowrez_error_enews() {
	echo "<div class='error'><p>You are not allowed to send eNews to members.</p></div>";
}
function lowrez_error_sms() {
	echo "<div class='error'><p>You are not allowed to send SMS Alerts to members.</p></div>";
}

/*-----------------------------------------*/

function lowrez_send_sms($post) {
	global $mail_html;
	$mail_html = false;
	
	$message = strip_tags(apply_filters('the_content', $post->post_content));
	add_action('phpmailer_init', 'lowrez_email_sms');
	
	wp_mail('webmaster+sms@test.lowrez.com.au', "SMS: " .$post->post_title, $message); //FIXME
	
}

function lowrez_email_sms($phpmailer) {
	$phpmailer->From = 'webmaster@lowrez.com.au';
	$phpmailer->FromName = 'LOW REZ Webmaster';
}

/*-----------------------------------------*/

function lowrez_send_enews($post) {
	
	$message = apply_filters('the_content', $post->post_content);
	add_action('phpmailer_init', 'lowrez_email_enews');
	wp_mail('webmaster+enews@test.lowrez.com.au', "eNews: " .$post->post_title, $message); //FIXME
	
}

function lowrez_email_enews($phpmailer) {
	$phpmailer->From = 'communication@lowrez.com.au';
	$phpmailer->FromName = 'LOW REZ eNews';
}