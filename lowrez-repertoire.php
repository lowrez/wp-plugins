<?php
/*
Plugin Name: LOW REZ Repertoire Library
Description: LOW REZ Repertoire Library
Author: LOW REZ
Version: 1.0
*/

//include_once 'repertoire/this-season.php';

include_once 'repertoire/admin.php';
include_once 'repertoire/post-type.php';
include_once 'repertoire/post-meta.php';
include_once 'repertoire/taxonomy.php';
include_once 'repertoire/import.php';
include_once 'repertoire/podcast-content.php';

/*--------------------------------*/

define ('LOWREZ_FILES', 'http://files.lowrez.com.au/media/');

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

function lowrez_download_shortcode($atts) {
	if (isset($atts['src'])) {
		$download = isset($atts['download']) ? '/download' : '';
		
		$f = LOWREZ_FILES.lowrez_media_hash($atts['src']).'/'.trim($atts['src'], '/') . $download;
		
		if (isset($atts['link'])) {
			$f = sprintf('<a href="%s"><i class="icon-download-alt">&nbsp;</i>%s</a>', $f, $atts['link']);
		}
		else {
			$f = sprintf('<a href="%s"><i class="icon-download-alt">&nbsp;</i>%s</a>', $f, basename($atts['src']));
		}
		
		return $f;
	}
}

add_shortcode('f', 'lowrez_download_shortcode');

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


/*================================*/