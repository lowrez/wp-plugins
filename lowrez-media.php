<?php
/*
Plugin Name: LOW REZ Media Management
Description: LOW REZ Media Management
Author: LOW REZ
Version: 1.0
*/

//require_once('media/repertoire-admin.php');

define ('LOWREZ_FILES', 'http://files.lowrez.com.au/media/');

/*--------------------------------*/

add_shortcode('v', 'lowrez_video');
add_shortcode('a', 'lowrez_audio');

function lowrez_video($atts) {
	return lowrez_media_shortcode($atts);
}

function lowrez_audio($atts) {
	return lowrez_media_shortcode($atts, true);
}

/*--------------------------------*/

function lowrez_media_shortcode($atts, $audio = false) {
	
	$media_atts = array('src', 'mp4', 'webm', 'mp3', 'ogg', 'poster');
	
	foreach ($media_atts as $ma) {
		if (isset($atts[$ma])) {
			$atts[$ma] = LOWREZ_FILES.lowrez_media_hash($atts[$ma]).'/'.trim($atts[$ma], '/');
		}
	}
	
	//$atts['src'] = LOWREZ_FILES.lowrez_media_hash($atts['src']).'/'.trim($atts['src'], '/');
	
	//if ($audio) { $atts['config'] = 'lowrez_audio'; } else { $atts['config'] = 'lowrez_video'; } //unset( $atts['config']); }
	$audio = $audio ? 'audio' : 'video';
	
	return do_shortcode('['.$audio.' '. lowrez_join_atts($atts) . ']').PHP_EOL;
	
}

/*--------------------------------*/

function lowrez_join_atts($atts) {
	if (is_array($atts)) {
		$att_str = array();
		foreach ($atts as $key => $val) {
			$att_str[] = $key . '="' . $val . '"';
		}	
		return implode(' ', $att_str);
	}	
}

function lowrez_media_hash($file = '') {
	//return 'sss';
	
	global $current_user;
	return $current_user->user_nicename;
	///return time();
}