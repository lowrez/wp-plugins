<?php

require_once('/home/lowrez/_prod/getmime/getmime.php');
//echo __FILE__;

if (!defined('INSIDE_WP')) {
	define('LOWREZ_TEST', true);
	define('NOT_INSIDE_WP', true);
	require_once('/home/lowrez/_wpbridge/wpbridge.php');
}

require_once('class.UploadHandler.php');

//if (!class_exists('zzUploadHandlerExtended')) {

	class UploadHandlerExtended extends UploadHandler {
		
		public function __call($method, $args) {
			if(method_exists($this, $method)) {
				return call_user_func_array(array(&$this, $method), $args);
			}
		}
		
		protected function get_user_id() {
			if (defined('INSIDE_WP')) {
				global $current_user;
				return $current_user->user_nicename;
			}
			else {
				return WPBridge::user();
			}
		}
		protected function get_file_icon($filename = false) {
			
			$img = getMIME::icon($filename, true);
			return sprintf('<img src="%s" class="media-object fileicon"/>' , $img);
		}
		
		protected function get_file_list() { //) {$width = '4'
			$path = $this->get_upload_path();
			
			$files = array();
			
			if (is_dir($path)) {
				chdir($path);
				if ($handle = opendir('.')) {
					while (($item = readdir($handle)) !== false) {
						if(is_dir($item)){
							//array_push($directories, realpath($item)); //Not interested in directories
						}
						else
						{
							array_push($files, ($item));
						}
					}
					closedir($handle);
				}
				else {
					$files[] = 'Directory handle could not be obtained.';
				}
			}
			else {
				mkdir($path, 0755, true);
			}
			
			sort($files);
			
			return $files;
		}
		
		protected function notify_setup($files) {
			global $current_user;
			
			foreach ($files as $file) {
				if (!$file->error) add_user_meta($current_user->ID, 'lowrez_upload_files', $file->name);
			}
			
		}
		
		
		protected function unzip(&$file, &$unzipped_files) {
			
			if (in_array($file->type, array("application/zip", "application/x-zip", "application/x-zip-compressed"))) {
				
				$zip = new ZipArchive;
				$zip_path = $this->get_upload_path($file->name);
				
				$res = $zip->open($zip_path);
				if ($res === TRUE) {
					
					for($i = 0; $i < $zip->numFiles; $i++) {
						$filename = $zip->getNameIndex($i);
						
						if ( substr( $filename, -1 ) == DIRECTORY_SEPARATOR ) continue;
						
						$basename = basename($filename);
						
						if (!preg_match($this->options['accept_file_types'], $basename)) {
							$new_file = new stdClass();
							$new_file->name = $basename;
							$new_file->size = 0;//$this->get_file_size($upload_to);
							$new_file->type = $this->get_file_type($basename); 
							$new_file->error = $this->get_error_message('accept_file_types');
							$new_file->zipped = true;
							$unzipped_files[] = $new_file;
							
							return false;
						}
						
						if (is_file($this->get_upload_path($basename))) {
							$basename = $this->upcount_name($basename);
						}
						
						//$basename = lowercase_ext($basename);
						
						$upload_to = $this->get_upload_path($basename);
						copy("zip://".$zip_path."#".$filename, $upload_to);
						
						$new_file = new stdClass();
						$new_file->name = $basename;
						$new_file->size = $this->get_file_size($upload_to);
						$new_file->type = $this->get_file_type($upload_to); 
						$new_file->url = $this->get_download_url($basename);
						$new_file->zipped = true;
						$this->set_file_delete_properties($new_file);
						
						$unzipped_files[] = $new_file;
						
						$this->unzip($new_file, $unzipped_files);
					}
					$file->unzipped = true;
					$zip->close();
				} else {
					$file->error = 'Could not unzip file';
				}
				
			}
			
		}
		
		protected function lowercase_ext($file) {
			return pathinfo($file) . '.' . strtolower(pathinfo($file, PATHINFO_EXTENSION));
		}
		
		protected function get_download_url($file_name, $version = null) {
			if ($this->options['download_via_php']) {
				$url = $this->options['script_url']
					.$this->get_query_separator($this->options['script_url'])
					.'file='.rawurlencode($file_name);
				if ($version) {
					$url .= '&version='.rawurlencode($version);
				}
				return $url.'&download=1';
			}
			$version_path = empty($version) ? '' : rawurlencode($version).'/';
			
			$this->options['upload_url'] = sprintf($this->options['upload_url'], $this->get_user_id());		
			
			return $this->options['upload_url'].$this->get_user_path()
				.$version_path.rawurlencode($file_name);
		}
		
		protected function generate_response($content, $print_response = true) {
			$unzipped_files = array();
			foreach ($content['files'] as &$file) {
				$this->unzip($file, $unzipped_files);
			}
			
			$content['files'] = array_merge($content['files'], $unzipped_files);
			
			foreach ($content['files'] as &$file) {
				$file->icon = $this->get_file_icon($file->name);
			}
			
			if (defined('INSIDE_WP')) {		
				header("HTTP/1.1 200 OK");
			}
			else {
				WPBridge::ok();
			}
			
			$redirect = isset($_REQUEST['redirect']) ?
				stripslashes($_REQUEST['redirect']) : null;
			if ($redirect) {
				$errors = array(); 
				
				$i = 0;
				
				foreach ($content['files'] as $file) {
					$i++;
					if (
						$file->error &&
						($file->error != parent::get_error_message(4) || $i == 1
						)
					) {
						if ($file->error == parent::get_error_message(4)) {
							$errors['(nofile)'] = $file->error;
						}
						else{
							$errors[$file->name] = $file->error;
						}
					}
				}
				$this->set_flashdata($errors);
			}
			
			$this->notify_setup($content['files']);
			parent::generate_response($content, $print_response);
		}
		
		protected function get_file_type($file_path) {
			return getMIME::mime($file_path);	
		}
		
		public function delete() {
			//do nothing
		}
		
		
		function start_session() {
			if(!session_id()) {
				session_start();
			}
		}
		
		function end_session() {
			session_destroy();
		}
		
		function set_flashdata($value) {
			$this->start_session();
			$_SESSION['lowrez_upload_flashdata'] = $value;
		}
		
		function get_flashdata() {
			if(isset($_SESSION['lowrez_upload_flashdata'])) {
				return $_SESSION['lowrez_upload_flashdata'];
			}
		}
		
		function clear_flashdata() {
			unset($_SESSION['lowrez_upload_flashdata']);
		}
		
		function ouput_flashdata() {
			if ($flashdata = $this->get_flashdata()) {
				echo "<div id=\"message\" class=\"updated\"><p><strong>{$flashdata}</strong></p></div>";
			}
			
		}
		
	}
//}