<?php

require_once('class.UploadHandlerExtended.php');

$upload_handler = new UploadHandlerExtended(array(
	'accept_file_types' => '/\.(gif|jpe?g|png|tif?|wav|mp3|m4a|pdf|sib|mus|mid|docx?|rtf|txt|zip)$/i',
	'user_dirs' => true,
	'upload_url' => 'http://files.lowrez.com.au/media/%s/arrangers/',
	'upload_dir' => '/home/lowrez/_files/arrangers/',
), !defined('BROWSE_UPLOAD'));
