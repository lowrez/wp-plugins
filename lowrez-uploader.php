<?php
/*
Plugin Name: LOW REZ Uploader
Description: LOW REZ Uploader
Author: LOW REZ
Version: 1.0
*/

register_activation_hook(__FILE__, 'lowrez_upload_activation');
add_action('lowrez_upload_notify', 'lowrez_upload_notify_event');
register_deactivation_hook(__FILE__, 'lowrez_upload_deactivation'); 

function lowrez_upload_activation() {
	if ( ! wp_next_scheduled( 'lowrez_upload_notify' ) ) {
		wp_schedule_event(time(), 'hourly', 'lowrez_upload_notify');
	}
}

function lowrez_upload_deactivation() {
	wp_clear_scheduled_hook('lowrez_upload_notify');
}

function lowrez_upload_notify_event() {
	
	global $wpdb;
	
	$key = 'lowrez_upload_files';
	
	$query = $wpdb->prepare(
		"
SELECT  user_id , GROUP_CONCAT(  meta_value 
SEPARATOR  '|' )  AS files
FROM  $wpdb->usermeta
WHERE meta_key = %s
GROUP BY user_id
",  $key );
	
	if ($users = $wpdb->get_results($query)) {
		print_pre($users);
		foreach ($users as $user) {
			
			$wp_user = get_user_by('id', $user->user_id);
			
			$uploader = $wp_user->display_name;
			$subject = 'Files uploaded by '. $uploader;
			
			$filelist = '<li>'.str_replace('|','</li><li>', $user->files).'</li>';
			
			$message = <<<HTML
The following files were successfully uploaded to the LOW REZ website by {$uploader}:
<ul>
{$filelist}
</ul>
HTML;
			
			$to = 'music@lowrez.com.au';
			$cc = $wp_user->user_email;
			
			$headers = array("CC: $cc");
			
			wp_mail( $to, $subject, $message, $headers );
			
		}
		
		
		$wpdb->query( 
			$wpdb->prepare( 
				"
DELETE FROM $wpdb->usermeta
WHERE meta_key = %s
",  $key 
			)
		);
		
	}
	
}

require('upload/index.php');