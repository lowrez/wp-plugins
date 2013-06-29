<?php

if (isset($_GET['cleanup'])) {
	$import_repertoire_media = new ImportRepertoireMedia(false);
	$import_repertoire_media->cleanup_empty();
}
/*if (isset($_REQUEST['rescan'])) {
$import_repertoire_media = new ImportRepertoireMedia(false);
$import_repertoire_media->scandir();
}
elseif (isset($_REQUEST['import-songs']) && isset($_REQUEST['save-songs'])) {
$import_repertoire_media = new ImportRepertoireMedia(false);
$import_repertoire_media->savesongs();
}
else {*/
$import_repertoire_media = new ImportRepertoireMedia();
//}

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


function ignored_files($file) {
	
	$ignored = array('.','..');
	$allowed_ext = array(
		'pdf', 
		'mp3',
		'mp4'
	);
	
	if ( !in_array($file, $ignored) ) {
		//if ( in_array(pathinfo($file, PATHINFO_EXTENSION), $allowed_ext) ) {
		return true;
		//}
	}
	
}

class ImportRepertoireMedia {
	
	private $_url = 'http://files.lowrez.com.au/media/%s/repertoire/%s';
	private $_path = '/home/lowrez/_files/repertoire/';
	private $_dir = '_test/';//this-season/testing';
	private $_uploading = '_uploading';
	private $_processing = '_processing';
	private $_files;
	private $_repertoires;
	
	private $_media_types;
	private $_parts;
	private $_concerts;
	
	private $_repertoire_medias;
	
	public $media_types_matches;
	public $parts_matches;
	public $concerts_matches;
	
	function __construct($init = true) {
		if ($init) {
			
			add_action('add_attachment', array(&$this, 'catch_attachment_to_repertoire'));
			add_action('edit_attachment', array(&$this, 'catch_attachment_to_repertoire'));
			
			add_filter('upload_dir', array(&$this, 'repertoire_upload_dir'));
			
			add_action('admin_menu', array(&$this, 'admin_menu'));
			add_filter('media_upload_tabs', array(&$this, 'remove_media_library_tab'));
			add_action('admin_enqueue_scripts', array(&$this, 'admin_scripts_upload_repertoire_media'));
			
			add_filter( 'plupload_init', array(&$this, 'plupload_init') );
		}
	}
	
	function __get($prop) {
		
		switch ($prop) {
			
			case 'url':
			
			global $current_user; get_currentuserinfo();			
			return untrailingslashit(sprintf($this->_url, 'podcast', $this->_dir));//$current_user->user_nicename
			
			break;
			/*---*/
			case 'path';
			
			return untrailingslashit($this->_path . $this->_dir);
			
			break;	
			/*---*/
			case 'uploading';
			
			return untrailingslashit($this->_path . $this->_dir . $this->_uploading);
			
			break;	
			/*---*/
			case 'processing';
			
			return untrailingslashit($this->_path . $this->_dir . $this->_processing);
			
			break;	
			/*---*/
			case 'repertoires':
			
			if (!isset($this->_repertoires)) {
				$get_posts = get_posts(
					array(
						'post_type'=> 'repertoire',
						'post_status'=> 'publish',
						'suppress_filters' => false,
						'posts_per_page'=>-1,
						'orderby'=>'title',
						'order'=>'ASC',
					)
				);
				$posts = array();
				
				foreach ($get_posts as $post) {
					$posts[$post->ID] = $post->post_title;
				}
				$this->_repertoires = $posts;				
			}
			
			return $this->_repertoires;
			
			break;	
			/*---*/
			case 'match_repertoires':
			
			//return array_flip(array_map('sanitize_title', $this->repertoires));
			return array_map('sanitize_title', $this->repertoires);
			
			break;
			/*---*/
			
			case 'parts':
			
			if (!isset($this->_parts)) {
				$terms = get_terms('part',
								   array(
									   'orderby' => 'name',
									   'order' => 'ASC',
									   'hide_empty' => false,
									   'fields' => 'all',
									   'hierarchical' => 1,
								   )
								  );
				
				foreach ($terms as $term) {
					$this->_parts[$term->term_id] = $term->name;				
					$this->parts_matches[$term->term_id] = $this->prepare_term_regex($term);
				}
			}
			
			return $this->_parts;
			
			break;
			/*---*/
			case 'media_types':
			
			if (!isset($this->_media_types)) {
				$terms = get_terms('media-type',
								   array(
									   'orderby' => 'name',
									   'order' => 'ASC',
									   'hide_empty' => false,
									   'fields' => 'all',
									   'hierarchical' => 1,
								   )
								  );
				
				foreach ($terms as $term) {
					$this->_media_types[$term->term_id] = $term->name;
					$this->media_types_matches[$term->term_id] = $this->prepare_term_regex($term);
				}
			}
			
			return $this->_media_types;
			
			break;
			/*---*/
			case 'concerts':
			
			if (!isset($this->_concerts)) {
				$terms = get_terms('concert',
								   array(
									   'orderby' => 'name',
									   'order' => 'ASC',
									   'hide_empty' => false,
									   'fields' => 'all',
									   'hierarchical' => 1,
								   )
								  );
				
				foreach ($terms as $term) {
					$this->_concerts[$term->term_id] = $term->name;
					$this->concerts_matches[$term->term_id] = $this->prepare_term_regex($term);
				}
			}
			
			return $this->_concerts;
			
			break;
			/*---*/
			default:
			
			return $this->$prop;
			
		}
		
	}
	
	
	function admin_message() {
		if ($msg = $_GET['admin_msg']) {
			
			$msgs = array(
				'cleanup_success' => 'Tidied up empty subdirectories.',
				'cleanup_none' => 'No empty subdirectories to tidy up.',
				'cleanup_fail' => 'Could not tidy up empty subdirectories.',
			);
			
			// class="error" ?
			
			if (!$msg = $msgs[$msg]) $msg = 'An unknown error occurred.';
			
			echo "<div id=\"message\" class=\"updated\"><p>{$msg}</p></div>";
		}
	}
	
	function prepare_term_regex($term) {
		
		$regex = array();
		$regex[] = $term->description;
		$regex[] = str_replace('-',' ',sanitize_title($term->name));
		
		//$regex[] = initialism($term->name);
		
		$regex = array_filter($regex);
		
		return '('.implode('|',$regex).')';
	}
	
	function plupload_init($init) {
		if (!is_admin()) return $init;
		$screen = get_current_screen();
		if ($screen->base == 'repertoire-media_page_upload-repertoire') {
			$init['multipart_params']['save_type'] = 'repertoire-media';
		}
		return $init;
	}
	
	function post_upload_ui() {
		/*	
		<script type="text/javascript">
		wpUploaderInit.multipart_params.save_type = 'repertoire-media';
		</script>
		*/
	}
	
	function reload($msg = false) {
		$query = remove_query_arg('rescan');
		$query = remove_query_arg('cleanup', $query);
		if ($msg) $query = add_query_arg('admin_msg', $msg, $query);
		
		header('Location: '. 'http://'.$_SERVER['HTTP_HOST'].$query);
		exit;
	}
	
	function admin_menu() {
		add_submenu_page('edit.php?post_type=repertoire-media', 'Upload Repertoire Media', 'Upload Media', 'publish_posts', 'upload-repertoire', array(&$this, 'admin_submenu_upload_repertoire_media') );
	}
	
	
	function admin_submenu_upload_repertoire_media() {
		wp_enqueue_script('plupload-handlers');
		
		$post_id = 0;
		if ( isset( $_REQUEST['post_id'] ) ) {
			$post_id = absint( $_REQUEST['post_id'] );
			if ( ! get_post( $post_id ) || ! current_user_can( 'edit_post', $post_id ) )
				$post_id = 0;
		}
		
		if ( $_POST ) {
			if ($_POST['import-type'] == 'upload') {
				$location = 'edit.php?post_type=repertoire-media';
				if ( isset($_POST['html-upload']) && !empty($_FILES) ) {
					check_admin_referer('media-form');
					// Upload File button was clicked
					$id = media_handle_upload( 'async-upload', $post_id );
					if ( is_wp_error( $id ) )
						$location .= '?message=3';
				}
				wp_redirect( admin_url( $location ) );
				exit;
			}
		}
		
		$form_class = 'media-upload-form type-form validate';
		
		if ( get_user_setting('uploader') || isset( $_GET['browser-uploader'] ) )
			$form_class .= ' html-uploader';
?>
<div class="wrap">
	<div id="icon-edit" class="icon32 icon32-this-season">
		<br/>
	</div>
	<h2>Upload Repertoire Media</h2>
	<?php
		
		$this->admin_message();
		
		/*$cleanup = sprintf('<a href="%s" class="button">Clean Up</a>', admin_url('edit.php?post_type=repertoire-media&page=upload-repertoire&cleanup=1'));
		
		echo sprintf('<p>Server Directory: <code>%s</code>. %s</p>', $this->path, $cleanup);*/
		echo sprintf('<p>Download URL: <code>%s</code>.</p>', $this->url);
		
		//printf( "<iframe frameborder='0' src=' %s ' style='width: 100%%; height: 400px;'> </iframe>", get_upload_iframe_src('media') );
		$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 'upload';
		$this->admin_tabs($tab);
		
		switch ($tab) {
			case 'upload':
			
	?>
	<form enctype="multipart/form-data" method="post" action="<?php echo admin_url('edit.php?post_type=repertoire-media&page=upload-repertoire&tab=upload'); ?>" class="<?php echo $form_class; ?>" id="file-form">
		<input type="hidden" name="import-type" id="import-type" value="upload" />
		<h3>Upload Files from Computer</h3>
		<table style="width:100%;">
			<tr>
				<td style="vertical-align:top;padding-right:20px;">
					<?php media_upload_form(); ?>
					
					<script type="text/javascript">
						var post_id = <?php echo $post_id; ?>, shortform = 3;
					</script>
					<input type="hidden" name="post_id" id="post_id" value="<?php echo $post_id; ?>" />
					<input type="hidden" name="save_type" id="save_type" value="repertoire-media" />
					
					<?php wp_nonce_field('media-form'); ?>
				</td>
				<td style="width:625px;vertical-align:top;">
					<div id="media-items" class="hide-if-no-js"></div>
				</td>
			</tr>
		</table>
	</form>
	
</div>
<?php
			
			break;
			case 'server':
?>
<h3>Import Files from Server</h3>
<table style="width:100%;">
	<tr>
		<td style="vertical-align:top;padding-right:20px;">
			<form method="post" action="<?php echo admin_url('edit.php?post_type=repertoire-media&page=upload-repertoire&tab=server'); ?>">
				<input type="hidden" name="save_type" id="save_type" value="repertoire-media" />
				<input type="hidden" name="import-type" id="import-type" value="server" />
				<?php
			if ($files = $_POST['selected_files']) { array_walk($files, 'unslashit'); }
			$this->glob_files($this->uploading, 'server', $files);
				?>
			</form>
		</td>
		<td style="width:625px;vertical-align:top;">
			<div id="media-items">
				<?php
			if ($files) {
				
				foreach ($files as $file) {
					echo '<div class="media-item">
<div class="progress">
<div class="percent">Imported</div>
<div class="bar" style="width: 200px;"></div>
</div>';
					$this->import_server_file($file);
					echo '    </div>';
				}
				
			}
				?>
			</div>
		</td>
	</tr>
</table>
<?php
			break;
			case 'remote':
?>
<h3>Download Files from URL</h3>
<table style="width:100%;">
	<tr>
		<td style="vertical-align:top;padding-right:20px;">
			<form method="post" action="<?php echo admin_url('edit.php?post_type=repertoire-media&page=upload-repertoire&tab=remote'); ?>">
				<input type="hidden" name="save_type" id="save_type" value="repertoire-media" />
				<input type="hidden" name="import-type" id="import-type" value="remote" />
				
				<?php
			$list_table = new Server_Folder_List_Table('remote');
			$list_table->prepare_items();
			$list_table->display();
			
				?>
			</form>
		</td>
		<td style="width:625px;vertical-align:top;">
			<div id="media-items">
				<?php
			if ($files = $_POST['selected_files']) {
				//"(https?://(?:www\.)?dropbox\.com/sh/[^"]+)"
				
				foreach ($files as $file) {
					$tmpfile = $this->get_remote_file($file);
					$filename = $this->get_remote_filename($file);
					
					if (false || !$this->is_url($file)) {
						echo '<div class="media-item error"><p>';
						echo urldecode($file) . ' is not a valid URL.';
						echo '</p></div>';
					}
					else {
						
						if (!$this->url_exists($file)) {
							echo '<div class="media-item error"><p>';
							echo urldecode($file) . ' could not be reached.';
							echo '</p></div>';
						}
						else {
							
							echo '<div class="media-item">
<div class="progress">
<div class="percent">Imported</div>
<div class="bar" style="width: 200px;"></div>
</div>';
							$this->import_server_file($tmpfile, 0, 'file', $filename);
							echo '    </div>';
							
						}
						
					}
					
					
				}
				
			}
				?>
			</div>
		</td>
	</tr>
</table>
<?php
			
			break;
			case 'dropbox':
?>
<h3>Choose Files from Dropbox</h3>
<script type="text/javascript" src="https://www.dropbox.com/static/api/1/dropbox.js" id="dropboxjs" data-app-key="ldfdgxzha1bf81t"></script>
<table style="width:100%;">
	<tr>
		<td style="vertical-align:top;padding-right:20px;">
			<form method="post" action="<?php echo admin_url('edit.php?post_type=repertoire-media&page=upload-repertoire&tab=dropbox'); ?>">
				<input type="hidden" name="save_type" id="save_type" value="repertoire-media" />
				<input type="hidden" name="import-type" id="import-type" value="dropbox" />
				
				<?php
			$list_table = new Server_Folder_List_Table('dropbox');
			$list_table->prepare_items();
			$list_table->display();
			
				?>
			</form>
		</td>
		<td style="width:625px;vertical-align:top;">
			<div id="media-items">
				<?php
			if ($files = $_POST['selected_files']) {
				
				foreach ($files as $file) {
					$tmpfile = $this->get_remote_file($file);
					
					echo '<div class="media-item">
<div class="progress">
<div class="percent">Imported</div>
<div class="bar" style="width: 200px;"></div>
</div>';
					$this->import_server_file($tmpfile, 0, 'file', urldecode($file));
					echo '    </div>';
				}
				
			}
				?>
			</div>
		</td>
	</tr>
</table>
<?php
			break;
			case 'browse':
?>
<h3>Browse Folder</h3>
<?php
			$this->glob_files($this->path);
			break;
		}
		
	}
	
	function admin_tabs( $current = 'upload' ) {
		$tabs = array(
			'upload' => 'Upload',
			'server' => 'Server',
			'remote' => 'URL',
			'dropbox' => 'Dropbox',
			'browse' => 'Browse',
		);
		
		echo "<input type='hidden' name='tab' value='$current'>";
		
		echo '<br><h2 class="nav-tab-wrapper">';
		foreach( $tabs as $tab => $name ){
			$class = ( $tab == $current ) ? ' nav-tab-active' : '';
			$url = add_query_arg( array('tab'=>$tab) );
			echo "<a class='nav-tab$class' href='$url'>$name</a>";
			
		}
		echo '</h2>';
	}
	
	function get_remote_file($url) {
		$path = tempnam(sys_get_temp_dir(), basename($file));
		
		$fp = fopen($path, 'w');
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		
		$data = curl_exec($ch);
		
		curl_close($ch);
		fclose($fp);
		
		return $path;
	}
	
	function url_exists($file) {
		
		if ($headers = @get_headers($file)) {
			if($headers[0] == 'HTTP/1.1 404 Not Found') {
				return false;
			}
			else {
				return true;
			}
		}
		else {
			return false;
		}
	}
	
	function get_remote_filename($file) {
		
		$headers = @get_headers($file);
		
		if ($headers) {
			
			foreach ($headers as $header) {
				if (preg_match('/^content-disposition:\s*attachment;\s*filename="([^"]+)"$/im', $header, $regs)) {
					$filename = $regs[1];
					continue;
				}
			}
			
			if (!$filename) {
				foreach ($headers as $header) {
					if (preg_match('/^content-type:\s+([^;]+)/im', $header, $regs)) {
						$filemime = $regs[1];
						continue;
					}
					
				}

				if ($filemime) {
					require_once('/home/lowrez/_prod/getmime/getmime.php');
					if ( $filename = trim(basename(parse_url($file, PHP_URL_PATH)), DIRECTORY_SEPARATOR) ) {
						$filename = sanitize_file_name($filename);
					}
					else {
						$filename = str_replace('.','_',sanitize_file_name(parse_url($file, PHP_URL_HOST)));
					}
					
					$fileext = getMIME::mime_to_ext($filemime);
					$filename = $filename.'.'.$fileext;
				}
			}
			
		}
		else {
			$filename = $file;
		}
		
		return $filename;
		
	}
	
	function is_url($url) {
		return filter_var($url, FILTER_VALIDATE_URL);
	}
	
	function glob_files($path = false, $cb = false, $exclude = false) {
		$path = untrailingslashit($path);
		$files = $this->glob_recursive($path);
		
		$list_table = new Server_Folder_List_Table($cb, $path);
		$list_table->prepare_items($files, $path, $exclude);
		$list_table->display();
	}
	
	function glob_recursive($dir) {
		$items = glob(rtrim($dir, DIRECTORY_SEPARATOR) . '/*', GLOB_MARK);
		
		for ($i = 0; $i < count($items); $i++) {
			if (is_dir($items[$i])) {
				$add = glob(rtrim($items[$i], DIRECTORY_SEPARATOR) . '/*', GLOB_MARK);
				$items = array_merge($items, $add);
			}
		}
		
		sort($items);
		
		return $items;
	}
	
	function import_server_file($file, $post_id = 0, $import_date = 'file', $filename = false) {
		set_time_limit(120);
		
		if (!is_file($file)) return false;
		
		$filename = basename(!$filename ? $file : $filename);
		
		$new_file = wp_unique_filename($this->processing, $filename);
		
		if (rename(stripslashes($file), $new_file)) $file = $new_file;
		
		// Initially, Base it on the -current- time.
		$time = current_time('mysql', 1);
		// Next, If it's post to base the upload off:
		if ( 'post' == $import_date && $post_id > 0 ) {
			$post = get_post($post_id);
			if ( $post && substr( $post->post_date_gmt, 0, 4 ) > 0 )
				$time = $post->post_date_gmt;
		} elseif ( 'file' == $import_date ) {
			$time = gmdate( 'Y-m-d H:i:s', @filemtime($file) );
		}
		
		// A writable uploads dir will pass this test. Again, there's no point overriding this one.
		if ( ! ( ( $uploads = wp_upload_dir($time) ) && false === $uploads['error'] ) )
			return new WP_Error( 'upload_error', $uploads['error']);
		
		$wp_filetype = wp_check_filetype( $file, null );
		
		extract( $wp_filetype );
		
		//if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) )
		//	return new WP_Error('wrong_file_type', __( 'Sorry, this file type is not permitted for security reasons.' ) ); //A WP-core string..
		
		//Is the file allready in the uploads folder?
		if ( preg_match('|^' . preg_quote(str_replace('\\', '/', $uploads['basedir'])) . '(.*)$|i', $file, $mat) ) {
			
			$filename = basename($file);
			$new_file = $file;
			
			$url = $uploads['baseurl'] . $mat[1];
			
			$attachment = get_posts(array( 'post_type' => 'repertoire-media', 'meta_key' => '_wp_attached_file', 'meta_value' => ltrim($mat[1], '/') ));
			if ( !empty($attachment) )
				return new WP_Error('file_exists', __( 'That file already exists in the Repertoire Media library.' ) );
			
			//Ok, Its in the uploads folder, But NOT in WordPress's media library.
			if ( 'file' == $import_date ) {
				$time = @filemtime($file);
				if ( preg_match("|(\d+)/(\d+)|", $mat[1], $datemat) ) { //So lets set the date of the import to the date folder its in, IF its in a date folder.
					$hour = $min = $sec = 0;
					$day = 1;
					$year = $datemat[1];
					$month = $datemat[2];
					
					// If the files datetime is set, and it's in the same region of upload directory, set the minute details to that too, else, override it.
					if ( $time && date('Y-m', $time) == "$year-$month" )
						list($hour, $min, $sec, $day) = explode(';', date('H;i;s;j', $time) );
					
					$time = mktime($hour, $min, $sec, $month, $day, $year);
				}
				$time = gmdate( 'Y-m-d H:i:s', $time);
				
				// A new time has been found! Get the new uploads folder:
				// A writable uploads dir will pass this test. Again, there's no point overriding this one.
				if ( ! ( ( $uploads = wp_upload_dir($time) ) && false === $uploads['error'] ) )
					return new WP_Error( 'upload_error', $uploads['error']);
				$url = $uploads['baseurl'] . $mat[1];
			}
		} else {
			$filename = wp_unique_filename( $uploads['path'], basename($file));
			
			// copy the file to the uploads dir
			$new_file = $uploads['path'] . '/' . $filename;
			if ( false === @copy( $file, $new_file ) )
				return new WP_Error('upload_error', sprintf( __('The selected file could not be copied to %s.', 'add-from-server'), $uploads['path']) );
			
			// Set correct file permissions
			$stat = stat( dirname( $new_file ));
			$perms = $stat['mode'] & 0000666;
			@ chmod( $new_file, $perms );
			// Compute the URL
			$url = $uploads['url'] . '/' . $filename;
			
			if ( 'file' == $import_date )
				$time = gmdate( 'Y-m-d H:i:s', @filemtime($file));
		}
		
		//Apply upload filters
		$return = apply_filters( 'wp_handle_upload', array( 'file' => $new_file, 'url' => $url, 'type' => $type ) );
		$new_file = $return['file'];
		$url = $return['url'];
		$type = $return['type'];
		
		$title = preg_replace('!\.[^.]+$!', '', basename($file));
		$content = '';
		
		// use image exif/iptc data for title and caption defaults if possible
		/*if ( $image_meta = @wp_read_image_metadata($new_file) ) {
		if ( '' != trim($image_meta['title']) )
		$title = trim($image_meta['title']);
		if ( '' != trim($image_meta['caption']) )
		$content = trim($image_meta['caption']);
		}*/
		
		if ( $time ) {
			$post_date_gmt = $time;
			$post_date = $time;
		} else {
			$post_date = current_time('mysql');
			$post_date_gmt = current_time('mysql', 1);
		}
		
		// Construct the attachment array
		$attachment = array(
			'post_mime_type' => $type,
			'guid' => $url,
			'post_parent' => $post_id,
			'post_title' => $title,
			'post_name' => $title,
			'post_content' => $content,
			'post_date' => $post_date,
			'post_date_gmt' => $post_date_gmt
		);
		
		//$attachment = apply_filters('afs-import_details', $attachment, $file, $post_id, $import_date);
		
		//Win32 fix:
		//$new_file = str_replace( strtolower(str_replace('\\', '/', $uploads['basedir'])), $uploads['basedir'], $new_file);
		
		// Save the data
		$id = wp_insert_attachment($attachment, $new_file, $post_id);
		if ( !is_wp_error($id) ) {
			$data = wp_generate_attachment_metadata( $id, $new_file );
			wp_update_attachment_metadata( $id, $data );
		}
		//update_post_meta( $id, '_wp_attached_file', $uploads['subdir'] . '/' . $filename );
		
		//catch_attachment_to_repertoire($id, 'repertoire-media');
		
		return $id;
	}
	
	
	function cleanup_empty() {
		
		if ($this->RemoveEmptySubFolders($this->path)) {
			$msg = 'cleanup_success';
		}
		else {
			$msg = 'cleanup_none';
		}
		
		$this->reload($msg);
		
	}
	
	//http://stackoverflow.com/questions/1833518/remove-empty-subfolders-with-php
	function RemoveEmptySubFolders($path)
	{
		if (is_dir($path)) {
			$empty = true;
			foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file)
			{
				$empty &= is_dir($file) && $this->RemoveEmptySubFolders($file);
			}
			return $empty && rmdir($path);
		}
	}
	
	function filename_to_guess_title($attachment_id, &$ext) {
		$file = wp_get_attachment_url($attachment_id);
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		$file = str_replace('-', ' ', sanitize_title(pathinfo($file, PATHINFO_FILENAME)));
		return $file;
	}
	
	function guess_repertoire($compare_title) {
		foreach ($this->match_repertoires as $repertoire => $match) {
			if (preg_match('/\b'.str_replace('-', ' ', $match).'\b/i', $compare_title)) {
				return $repertoire;
			}
		}
		
	}
	function guess_media_type($compare_title, $ext) {	
		$this->media_types;
		foreach ($this->media_types_matches as $media_type => $match) {
			if (preg_match('/\b'.$match.'\b/i', $compare_title )) {
				return $media_type;
			}
		}
		
		switch ($ext) {
			case 'sib':
			return 'sibelius';
			break;
			case 'pdf':
			return 'sheet-music';
			break;
			case 'mp3':
			case 'm4a':
			return 'audio';
			break;
			case 'mp4':
			case 'mpeg':
			case 'webm':
			case 'avi':
			return 'video';
			break;
		}
	}
	function guess_part($compare_title) {
		$this->parts;
		foreach ($this->parts_matches as $part => $match) {
			if (preg_match('/\b'.$match.'\b/i', $compare_title )) {
				return $part;
			}
		}
	}
	function guess_concert($compare_title, $media_type) {
		if (preg_match('/\bconcert\b/i', $compare_title . ' ' . $media_type )) {
			$this->concerts;
			foreach ($this->concerts_matches as $concert => $match) {
				if (preg_match('/\b'.$match.'\b/i', $compare_title )) {
					return $concert;
				}
			}
		}
	}
	
	function catch_attachment_to_repertoire($attachment_id, $save_type = false) {
		//return $attachment_id;
		//print_pre($_SERVER);
		//print_pre($_REQUEST);
		//die();
		//print_pre(get_current_screen();)
		//if ($_SERVER['HTTP_REFERER'] != admin_url('edit.php?post_type=repertoire-media&page=upload-repertoire') ) return $attachment_id;
		
		if ($_POST['save_type'] != 'repertoire-media' && $save_type != 'repertoire-media' ) return $attachment_id;
		
		$repertoire_media = array();
		$repertoire_media['ID'] = $attachment_id;
		$repertoire_media['post_type'] = 'repertoire-media';
		$repertoire_media['post_status'] = 'draft';
		
		$ext = false;
		$guess_title = $this->filename_to_guess_title($attachment_id, $ext);
		
		$repertoire_media['post_parent'] = $this->guess_repertoire($guess_title);
		
		if ($media_type = $this->guess_media_type($guess_title, $ext)) {
			wp_set_post_terms( $attachment_id, $media_type, 'media-type', false );
		}
		if ($part = $this->guess_part($guess_title)) {
			wp_set_post_terms( $attachment_id, $part, 'part', false );
		}
		if ($concert = $this->guess_concert($guess_title, $media_type)) {
			wp_set_post_terms( $attachment_id, $concert, 'concert', false );
		}
		
		// Update the post into the database
		wp_update_post( $repertoire_media );
		$post = get_post( $attachment_id );
		
		//if ( $thumb_url = wp_get_attachment_image_src( $post->ID, 'thumbnail', true ) )
		
		//echo '<img class="pinkynail" src="' . esc_url( $thumb_url[0] ) . '" alt="" />';
		$edit_link = '<a class="edit-attachment" href="' . esc_url( get_edit_post_link( $post->ID ) ) . '" target="_blank">' . _x( 'Edit', 'media item' ) . '</a>';
		$title = $post->post_title ? $post->post_title : wp_basename( $post->guid ); // title shouldn't ever be empty, but use filename just in cas.e
		echo '<div class="filename new"><span class="title">' . esc_html( wp_html_excerpt( $post->post_title, 60 ) ) . '</span>'.$edit_link.'</div>';
		
		return $attachment_id;
		//if ( ! $attachment = repertoire_prepare_attachment_for_js( $attachment_id ) )
		//die();
		
		/*echo json_encode( array(
		'success' => true,
		'data'    => $attachment,
		) );*/
		
	}
	
	function scour_post_type() {
		global $post, $post_id;
		$post_id = (!empty($post_id) ? $post_id : (!empty($_REQUEST['post_id']) ? $_REQUEST['post_id'] : ''));
		if (empty($post) || (!empty($post) && is_numeric($post_id) && $post_id != $post->ID)) {
			$post = get_post($post_id);
		}
		
		if (isset($post->post_type)) {
			$post_type = $post->post_type;
		} else {
			$post_type = $_POST['save_type'];
			//$post_type = wp_parse_args(trim(strstr($_SERVER['HTTP_REFERER'],'?'),'?'), array('post_type'=>'post'));
			//$post_type = $post_type['post_type'];
		}
		
		//print_pre(wp_parse_args(trim(strstr($_SERVER['HTTP_REFERER'],'?'),'?'), array('post_type'=>'post')));
		return $post_type;
		
	}
	
	function repertoire_upload_dir($upload) {
		//return $upload;
		global $post_type;
		//if ($_POST['save_type'] == 'repertoire-media' || $_POST['post_type'] == 'repertoire-media' ) {
		if ( $this->scour_post_type() == 'repertoire-media' || $post_type == 'repertoire-media' ) {
			
			$upload['subdir']	= '';
			$upload['path'] = $upload['basedir'] = untrailingslashit($this->path);
			$upload['url'] = $upload['baseurl'] = untrailingslashit($this->url);
			
		}
		
		return $upload;
		
		//print_pre($upload);
	}
	
	
	function admin_scripts_upload_repertoire_media($hook) {
		if( 'repertoire-media_page_upload-repertoire' != $hook ) return;
		wp_enqueue_media();
		wp_enqueue_script( 'simple-uploader', plugins_url('/simple-uploader.js', __FILE__) );
	}
	
	function remove_media_library_tab($tabs) {
		$screen = get_current_screen();
		if ($screen->id <> 'repertoire-media_page_upload-repertoire') return;
		
		unset($tabs['library']);
		return $tabs;
	}
	
	
}

class Server_Folder_List_Table extends WP_List_Table {
	
	private $cb=false;
	private $path=false;
	
	function __construct($cb=false, $path=false) {
		$this->cb = $cb;
		$this->path = $path;
		
		parent::__construct( array(
			'singular'=> 'server-file', //Singular label
			'plural' => 'server-files', //plural label, also this well be one of the table css class
			'ajax'	=> false //We won't support Ajax for this table
		) );
	}
	
	function get_columns() {
		
		$columns = array();
		
		if ($this->cb) $columns['cb'] = '<input type="checkbox" checked="checked" />';
		
		if ($this->cb == 'dropbox') {
			$columns['file'] = 'File';
		}
		elseif ($this->cb == 'remote') {
			$columns['file'] = 'URL';
		}
		else {
			
			$columns['file'] = 'File';
			
			if (!$this->cb)
				$columns['path'] = 'Folder';
			
		}
		
		return $columns;
	}
	
	function extra_tablenav( $which ) {
		
		echo '<div class="alignleft actions">';
		
		if ($this->cb == 'dropbox') {
			
			printf('<a href="%s" class="button" id="db-chooser-%s">Choose from Dropbox</a>', '#', $which);
			if ($which=='top') {
				echo ' ';
				submit_button('Import', 'primary', 'import-media', false);
			}
			
			if ($which=='bottom') {
?>
<script type="text/javascript">
	jQuery(document).ready(function(e){
		jQuery('#db-chooser-top, #db-chooser-bottom').on('click', function(e) {
			e.preventDefault();
			
			options = {
				linkType: "direct",
				multiselect: true,
				success: function(files) {
					
					var tbl = jQuery('#the-list');
					tbl.find('tr.no-items').remove();
					
					jQuery.each(files, function(i, file) {
						tbl.append(jQuery('<tr><th scope="row" class="check-column"><input type="checkbox" name="selected_files[]" value="'+file.link+'" checked="checked" /></th><td class="file column-file">'+file.name+'</td></tr>'));
					});
					
				},
				cancel:  function() {
					
				}
			};
			
			Dropbox.choose(options);
		});
	});
</script>
<?php
			}
		}
elseif ($this->cb == 'remote') {
			
			printf('<a href="%s" class="button" id="url-chooser-%s">Add Another</a>', '#', $which);
			if ($which=='top') {
				echo ' ';
				submit_button('Import', 'primary', 'import-media', false);
			}
			
			if ($which=='bottom') {
?>
<script type="text/javascript">
	jQuery(document).ready(function(e){
		jQuery('#url-chooser-top, #url-chooser-bottom').on('click', function(e) {
			e.preventDefault();
			
			var tbl = jQuery('#the-list');
			tbl.find('tr.no-items').remove();
			tbl.append(jQuery('<tr><th scope="row" class="check-column"><input type="checkbox" name="selected_files[]" checked="checked" /></th><td class="file column-file"><input type="text" style="width:100%;" /></td></tr>'));
		});
		jQuery('#the-list').on('change', '.column-file > input[type=text]', function() {
			jQuery(this).closest('tr').find('input[type=checkbox]').val(jQuery(this).val());
		});
	});
</script>
<?php
			}
		}
		else {
			
			$refresh = sprintf('<a href="%s" class="button">Refresh</a> ', add_query_arg());
			
			if ($which=='top') {
				
				$import = $this->cb ? get_submit_button('Import', 'primary', 'import-media', false) : false;
				$cleanup = !$this->cb ? sprintf('<a href="%s" class="button">Tidy Up</a>', admin_url('edit.php?post_type=repertoire-media&page=upload-repertoire&cleanup=1')) : false;
				
				echo sprintf('<p>Server Directory: <code>%s</code>. %s %s %s</p>', $this->path, $refresh, $import, $cleanup);
				
			}
			else {
				
				echo $refresh;
				
			}
			
		}
		
		echo '</div>';
		
	}
	
	function prepare_items($items=array(), $root=false, $exclude = false) {
		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		$this->items = array();
		
		if ($this->cb == 'remote') {
			
			$f = new stdClass();
			$f->file = '';
			$this->items[] = $f;
			
		} else {
			
			if (!is_array($exclude)) $exclude = array();

			if ($root) {
				foreach ($items as $item) {
					if (substr($item, -1) == DIRECTORY_SEPARATOR) {
						//Nothing
					}
					else {
						
						$f = new stdClass();
						
						$f->id = $item;
						$f->file = basename($item);
						
						$neat_path = dirname(substr($item, strlen($root)));				
						$f->path = ltrim($neat_path, DIRECTORY_SEPARATOR);
						
						$f->no_select = in_array($item, $exclude);
						
						$this->items[] = $f;
					}
				}
			}
			
		}
	}
	
	function column_default($item, $column) {
		if ($this->cb == 'remote') {
			return '<input type="text" style="width:100%;" />';
		} else {
			return $item->$column;
		}
	}
	
	function column_cb( $item ){
		if ($this->cb == 'remote') {
			return '<input type="checkbox" name="selected_files[]" checked="checked" />';
		} else {
			if ($item->no_select) return '<input type="checkbox" disabled="disabled" />';
			
			return sprintf(
				'<input type="checkbox" name="selected_files[]" value="%2$s" checked="checked" />',
				$this->_args['singular'],
				$item->id
			);
		}
	}
	
	function css() {
		/*
		.widefat tbody th.check-column {
		padding: 4px 0 2px;
		}
		*/
	}
	
}

//Our class extends the WP_List_Table class, so we need to make sure that it's there


class ImportRepertoireMedia_Table extends WP_List_Table {
	
	public $import;
	
	/**
	* Constructor, we override the parent to pass our own arguments
	* We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	*/
	function __construct(&$import) {
		$this->import = $import;
		
		parent::__construct( array(
			'singular'=> 'import-song', //Singular label
			'plural' => 'import-songs', //plural label, also this well be one of the table css class
			'ajax'	=> false //We won't support Ajax for this table
		) );
	}
	
	/*function get_bulk_actions() {
	return array();
	$actions = array(
	'include'    => 'Include',
	'exclude'    => 'Exclude'
	);
	return $actions;
	}*/
	
	
	/**
	* Add extra markup in the toolbars before or after the list
	* @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
	*/
	function extra_tablenav( $which ) {
		
		//$this->pseudo_bulk_actions($which);
		
		echo '<div class="alignleft actions">';
		
		echo sprintf('<a href="%s" class="button">Rescan Directory</a> ', add_query_arg(array('rescan'=>'1')));
		
		submit_button('Import', 'primary', 'save-songs['.$which.']', false);
		
		
		
		echo '</div>';
		
		if ( $which == "top" ){
			//The code that goes before the table is here
		}
		if ( $which == "bottom" ){
			//The code that goes after the table is there
			
			//echo '<div class="alignleft actions"><input type="submit" class="button button-primary" value="Save Changes" /></div>';//<p class="submit"></p>
			
			
			
		}
	}
	
	function pseudo_bulk_actions($two) {
		
		//$two = $which == 'top' ? '' : '2';
		
		$actions = array('repertoire' => array('title'=>'Assign repertoire...', 'actions'=>$this->import->repertoires),
						 'media_types' => array('title'=>'Assign type...', 'actions'=>$this->import->media_types),
						 'part' => array('title'=>'Assign part...', 'actions'=>$this->import->parts),
						);
		
		foreach ($actions as $key => $action) {
			
			echo '<div class="alignleft actions">';
			echo "<select name='assign_{$key}[{$two}]'>\n";
			echo "<option value='-1' selected='selected'>" . $action['title'] . "</option>\n";
			foreach ( $action['actions'] as $name => $title )
				echo "\t<option value='$name'>$title</option>\n";
			echo "</select>\n";
			
			submit_button( __( 'Assign' ), 'button-secondary action', false, false, array( 'id' => "doassign_{$key}_{$two}" ) );
			echo "\n";
			echo '</div>';
			
		}
		
	}
	
	/**
	* Define the columns that are going to be used in the table
	* @return array $columns, the array of columns to use with the table
	*/
	function get_columns() {
		return $columns= array(
			//'cb'        => '<input type="checkbox" />',
			//'col_link_id'=>__('ID'),
			//'col_link_name'=>__('Path'),
			'col_file_name'=>__('File'),
			'col_repertoire'=>__('Repertoire'),
			'col_media_type'=>__('Type'),
			'col_part'=>__('Part'),
			'col_concert'=>__('Concert'),
			//'col_link_descr'=>__('Description'),
			//'col_file_date_mod'=>__('Modified'),
			'col_hidden'=>__('Skip'),
			'col_imported_to'=>__('Imported')
		);
	}
	
	function column_cb( $item ){
		return sprintf(
			'<input type="checkbox" name="selected_ids[]" value="%2$s" />',
			$this->_args['singular'],
			$item->id
		);
	}
	
	/**
	* Decide which columns to activate the sorting functionality on
	* @return array $sortable, the array of columns that can be sorted by the user
	*/
	public function get_sortable_columns() {
		return $sortable = array(
			'col_file_name'=>array('file_name'),
			//'col_repertoire'=>array('repertoire_title'),
			'col_media_type'=>array('media_type'),
			'col_part'=>array('part'),
			'col_concert'=>array('concert'),
			'col_hidden'=>array('hidden'),
			'col_imported_to'=>array('imported_to'),
		);
	}
	
	/**
	* Prepare the table with different parameters, pagination, columns and table elements
	*/
	function prepare_items() {
		
		global $wpdb, $_wp_column_headers;
		$screen = get_current_screen();
		
		/* -- Preparing your query -- */
		$query = "SELECT * FROM {$wpdb->prefix}import_media";
		
		/* -- Ordering parameters -- */
		//Parameters that are going to be used to order the result
		$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
		$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
		if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
		
		/* -- Pagination parameters -- */
		//Number of elements in your table?
		$totalitems = $wpdb->query($query); //return the total number of affected rows
		//How many to display per page?
		$perpage = 25;
		//Which page is this?
		$paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
		//How many pages do we have in total?
		$totalpages = ceil($totalitems/$perpage);
		//adjust the query to take pagination into account
		if(!empty($paged) && !empty($perpage)){
			$offset=($paged-1)*$perpage;
			$query.=' LIMIT '.(int)$offset.','.(int)$perpage;
		}
		
		/* -- Register the pagination -- */
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $perpage,
		) );
		//The pagination links are automatically built according to those parameters
		
		/* — Register the Columns — */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		/* -- Fetch the items -- */
		$this->items = $wpdb->get_results($query);
		
		$repertoires = $this->import->repertoires;
		
		foreach ($this->items as &$item) {
			$item->repertoire_title = $repertoires[$item->repertoire];
		}
		
	}
	
	function compare_title($item) {
		return str_replace('-', ' ', sanitize_title(pathinfo($item->file_name, PATHINFO_FILENAME)));
	}
	
	
	function column_col_file_name($item) {
		$not_found = empty($item->not_found) ? '' : '&nbsp;<b style="color:red;">!</b>';
		$return = sprintf('<a href="%s" target="_blank">%s</a>%s', $this->import->url.$item->file_name, $item->file_name, $not_found);
		$return .= sprintf('<input type="hidden" name="%s[%s][file_name]" value="%s" />', $this->_args['plural'], $item->id, basename($item->file_name) );
		
		return $return;
	}
	
	function column_col_repertoire($item) {
		
		$repertoires = $this->import->repertoires;
		$matches = $this->import->match_repertoires;
		
		if (empty($item->repertoire)) {
			$compare_title = $this->compare_title($item);
			
			foreach ($matches as $repertoire => $match) {
				
				if (preg_match('/\b'.str_replace('-', ' ', $match).'\b/i', $compare_title)) {
					$item->repertoire = $repertoire;
					$guessed = true;
					break;
				}
				
			}
			
		}
		
		return $this->_select($repertoires, $item, 'repertoire', $guessed);
	}
	
	function column_col_media_type($item) {
		
		$media_types = $this->import->media_types;
		
		if (empty($item->media_type)) {
			
			$compare_title = $this->compare_title($item);
			
			switch (pathinfo($item->file_name, PATHINFO_EXTENSION)) {
				case 'pdf':
				$term = get_term_by('slug', 'sheet-music', 'media-type');
				$item->media_type = $term->term_id;
				$guessed = true;
				break;
				case 'mp3':
				
				$matches = $this->import->media_types_matches;
				
				foreach ($matches as $media_type => $match) {
					
					if (preg_match('/\b'.$match.'\b/i', $compare_title )) {
						$item->media_type = $media_type;
						$guessed = true;
						break;
					}
					
				}
				break;
				case 'mp4':
				case 'webm':
				
				$matches = array(
					'concert' => 'concert',
					'visuals' => 'visuals?'
				);
				
				foreach ($matches as $media_type => $match) {
					
					if (preg_match('/\b'.$match.'\b/i', $compare_title )) {
						$item->media_type = $media_type;
						$guessed = true;
						break;
					}
					
				}
				break;
			}
		}
		
		return $this->_select($media_types, $item, 'media_type', $guessed);
		
	}
	
	function column_col_part($item) {
		
		$parts = $this->import->parts;
		
		if (empty($item->part)) {
			
			$compare_title = $this->compare_title($item);
			
			$matches = $this->import->parts_matches;
			foreach ($matches as $part => $match) {
				
				if (preg_match('/\b'.$match.'\b/i', $compare_title )) {
					$item->part = $part;
					$guessed = true;
					break;
				}
				
			}
		}
		
		return $this->_select($parts, $item, 'part', $guessed);
		
	}
	
	function column_col_concert($item) {
		
		/*if (empty($item->concert)) {
		
		$compare_title = $this->compare_title($item);
		
		$matches = array(
		'tenor-1' => 't(en(or)?)?(\s*|-|_)1',
		'tenor-2' => 't(en(or)?)?(\s*|-|_)2',
		'baritone' => 'bar(i|itone)?',
		'bass' => 'b(ass)?',
		//'countertenor' => '(counterten(or)?|ct)',
		'solo' => 'solo?'
		/*'bass guitar' => '',
		'guitar' => '',
		'drums' => '',
		);
		
		foreach ($matches as $part => $match) {
		
		if (preg_match('/\b'.$match.'\b/i', $compare_title )) {
		$item->part = $part;
		$guessed = true;
		break;
		}
		
		}
		}*/
		
		return $this->_select($this->import->concerts, $item, 'concert', $guessed);
		
	}
	
	function column_col_hidden( $item ){
		return sprintf(
			'<input type="checkbox" name="%s[%s][hidden]" value="1" '.checked($item->hidden, '1', false).'/>',
			$this->_args['plural'],
			$item->id
		);
	}
	
	function column_col_imported_to( $item ){
		if ($item->imported_to) {
			return sprintf(
				'<a href="http://test.lowrez.com.au/wp-admin/post.php?post=%s&action=edit" class="button">Edit</a>',
				$item->imported_to
			);
		}
		else {
			return '&ndash;';
		}
	}
	
	function _select($possibles, $item, $field, $guessed = false) {
		$guessed = $guessed ? ' class="guessed"' : '';
		$options = '<option value="" '.selected($item->$field, '', false).'></option>'.PHP_EOL;	
		
		if (is_array($possibles)) {
			foreach ($possibles as $id => $title) {
				$options .= sprintf('<option value="%s" '.selected($item->$field, $id, false).'>%s</option>'.PHP_EOL, $id, $title);
			}
		}
		
		return sprintf('<select name="%s[%s][%s]"%s>%s</select>', $this->_args['plural'], $item->id, $field, $guessed, $options);
		
	}
	
	function column_default($item) {
	}
	
	/**
	* Display the rows of records in the table
	* @return string, echo the markup of the rows
	*/
	function ____display_rows() {
		//Get the records registered in the prepare_items method
		$records = $this->items;
		
		//Get the columns registered in the get_columns and get_sortable_columns methods
		list( $columns, $hidden ) = $this->get_column_info();
		
		//Loop for each record
		if(!empty($records)){foreach($records as $rec){
			
			//Open the line
			echo '<tr id="record_'.$rec->link_id.'">';
			foreach ( $columns as $column_name => $column_display_name ) {
				
				//Style attributes for each col
				$class = "class='$column_name column-$column_name'";
				$style = "";
				if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
				$attributes = $class . $style;
				
				//edit link
				$editlink  = '/wp-admin/link.php?action=edit&link_id='.(int)$rec->link_id;
				
				//Display the cell
				switch ( $column_name ) {
					case "col_file_name":	echo '<td '.$attributes.'>'.stripslashes($rec->link_id).'</td>';	break;
					case "col_file_repertoire": echo '<td '.$attributes.'><strong><a href="'.$editlink.'" title="Edit">'.stripslashes($rec->link_name).'</a></strong></td>'; break;
					case "col_file_media_type": echo '<td '.$attributes.'>'.stripslashes($rec->link_url).'</td>'; break;
					case "col_file_part": echo '<td '.$attributes.'>'.$rec->link_description.'</td>'; break;
					case "col_file_date_mod": echo '<td '.$attributes.'>'.$rec->link_visible.'</td>'; break;
					case "col_link_visible": echo '<td '.$attributes.'>'.$rec->link_visible.'</td>'; break;
				}
			}
			
			//Close the line
			echo'</tr>';
		}}
	}
	
	function css() {
		echo '
<style type="text/css">

/*#col_repertoire {
width: 230px !important;
}
.col_repertoire select {
width: 225px !important;
}*/

#col_repertoire,
#col_media_type,
#col_part,
#col_concert {
width: 140px !important;
}
.col_repertoire select,
.col_media_type select,
.col_part select,
.col_concert select {
width: 120px !important;
}

#col_hidden {
width: 60px !important;
}
.col_hidden {
text-align:center;
}
.col_hidden input {
margin-right:14px;
}

.actions .button {
margin-top:1px;
}

select.guessed {
background:lightblue;
}

</style>
<script type="text/javascript">
jQuery(document).ready(function($) {

jQuery(\'.guessed\').change( function() {
jQuery(this).removeClass(\'guessed\');
});

});
</script>
';
		
		/*
		.limitcomments {
		max-height: 100px;
		overflow-y: auto;
		}
		*/
		
	}
	
	
}

function repertoire_prepare_attachment_for_js( $attachment ) {
	if ( ! $attachment = get_post( $attachment ) )
		return;
	
	if ( 'repertoire-media' != $attachment->post_type )
		return;
	
	$meta = wp_get_attachment_metadata( $attachment->ID );
	if ( false !== strpos( $attachment->post_mime_type, '/' ) )
		list( $type, $subtype ) = explode( '/', $attachment->post_mime_type );
	else
		list( $type, $subtype ) = array( $attachment->post_mime_type, '' );
	
	$attachment_url = repertoire_get_attachment_url( $attachment->ID );
	
	$response = array(
		'id'          => $attachment->ID,
		'title'       => $attachment->post_title,
		'filename'    => basename( $attachment->guid ),
		'url'         => $attachment_url,
		'link'        => get_attachment_link( $attachment->ID ),
		'alt'         => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
		'author'      => $attachment->post_author,
		'description' => $attachment->post_content,
		'caption'     => $attachment->post_excerpt,
		'name'        => $attachment->post_name,
		'status'      => $attachment->post_status,
		'uploadedTo'  => $attachment->post_parent,
		'date'        => strtotime( $attachment->post_date_gmt ) * 1000,
		'modified'    => strtotime( $attachment->post_modified_gmt ) * 1000,
		'menuOrder'   => $attachment->menu_order,
		'mime'        => $attachment->post_mime_type,
		'type'        => $type,
		'subtype'     => $subtype,
		'icon'        => wp_mime_type_icon( $attachment->ID ),
		'dateFormatted' => mysql2date( get_option('date_format'), $attachment->post_date ),
		'nonces'      => array(
			'update' => false,
			'delete' => false,
		),
		'editLink'   => false,
	);
	
	if ( current_user_can( 'edit_post', $attachment->ID ) ) {
		$response['nonces']['update'] = wp_create_nonce( 'update-post_' . $attachment->ID );
		$response['editLink'] = get_edit_post_link( $attachment->ID, 'raw' );
	}
	
	if ( current_user_can( 'delete_post', $attachment->ID ) )
		$response['nonces']['delete'] = wp_create_nonce( 'delete-post_' . $attachment->ID );
	
	if ( $meta && 'image' === $type ) {
		$sizes = array();
		$possible_sizes = apply_filters( 'image_size_names_choose', array(
			'thumbnail' => __('Thumbnail'),
			'medium'    => __('Medium'),
			'large'     => __('Large'),
			'full'      => __('Full Size'),
		) );
		unset( $possible_sizes['full'] );
		
		// Loop through all potential sizes that may be chosen. Try to do this with some efficiency.
		// First: run the image_downsize filter. If it returns something, we can use its data.
		// If the filter does not return something, then image_downsize() is just an expensive
		// way to check the image metadata, which we do second.
		foreach ( $possible_sizes as $size => $label ) {
			if ( $downsize = apply_filters( 'image_downsize', false, $attachment->ID, $size ) ) {
				if ( ! $downsize[3] )
					continue;
				$sizes[ $size ] = array(
					'height'      => $downsize[2],
					'width'       => $downsize[1],
					'url'         => $downsize[0],
					'orientation' => $downsize[2] > $downsize[1] ? 'portrait' : 'landscape',
				);
			} elseif ( isset( $meta['sizes'][ $size ] ) ) {
				if ( ! isset( $base_url ) )
					$base_url = str_replace( wp_basename( $attachment_url ), '', $attachment_url );
				
				// Nothing from the filter, so consult image metadata if we have it.
				$size_meta = $meta['sizes'][ $size ];
				
				// We have the actual image size, but might need to further constrain it if content_width is narrower.
				// Thumbnail, medium, and full sizes are also checked against the site's height/width options.
				list( $width, $height ) = image_constrain_size_for_editor( $size_meta['width'], $size_meta['height'], $size, 'edit' );
				
				$sizes[ $size ] = array(
					'height'      => $height,
					'width'       => $width,
					'url'         => $base_url . $size_meta['file'],
					'orientation' => $height > $width ? 'portrait' : 'landscape',
				);
			}
		}
		
		$sizes['full'] = array(
			'height'      => $meta['height'],
			'width'       => $meta['width'],
			'url'         => $attachment_url,
			'orientation' => $meta['height'] > $meta['width'] ? 'portrait' : 'landscape',
		);
		
		$response = array_merge( $response, array( 'sizes' => $sizes ), $sizes['full'] );
	}
	
	if ( function_exists('get_compat_media_markup') )
		$response['compat'] = get_compat_media_markup( $attachment->ID, array( 'in_modal' => true ) );
	
	return apply_filters( 'repertoire_prepare_attachment_for_js', $response, $attachment, $meta );
}

function repertoire_get_attachment_url( $post_id = 0 ) {
	$post_id = (int) $post_id;
	if ( !$post = get_post( $post_id ) )
		return false;
	
	if ( 'repertoire-media' != $post->post_type )
		return false;
	
	$url = '';
	if ( $file = get_post_meta( $post->ID, '_wp_attached_file', true) ) { //Get attached file
		if ( ($uploads = wp_upload_dir()) && false === $uploads['error'] ) { //Get upload directory
			if ( 0 === strpos($file, $uploads['basedir']) ) //Check that the upload base exists in the file location
				$url = str_replace($uploads['basedir'], $uploads['baseurl'], $file); //replace file location with url location
			elseif ( false !== strpos($file, 'wp-content/uploads') )
				$url = $uploads['baseurl'] . substr( $file, strpos($file, 'wp-content/uploads') + 18 );
			else
				$url = $uploads['baseurl'] . "/$file"; //Its a newly uploaded file, therefor $file is relative to the basedir.
		}
	}
	
	if ( empty($url) ) //If any of the above options failed, Fallback on the GUID as used pre-2.7, not recommended to rely upon this.
		$url = get_the_guid( $post->ID );
	
	$url = apply_filters( 'repertoire_get_attachment_url', $url, $post->ID );
	
	if ( empty( $url ) )
		return false;
	
	return $url;
}


function unslashit(&$item, $key) {
	return $item = stripslashes($item);
}

?>