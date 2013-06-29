<?php
if (!defined('NOT_INSIDE_WP')) {
	define('BROWSE_UPLOAD', true);
	define('INSIDE_WP', true);
	
	require_once('do-upload.php');

//http://scribu.net/wordpress/optimal-script-loading.html
class LOWREZ_Upload {
	static $add_script;
	
	static function init() {
		add_shortcode('lowrez_upload', array(__CLASS__, 'handle_shortcode'));
		
		add_action('init', array(__CLASS__, 'register_script'));
		add_action('wp_footer', array(__CLASS__, 'print_script'));
	}
		
	static function handle_shortcode($atts) {
		
		//lowrez_upload_notify();
		
		self::$add_script = true;
		global $upload_handler;
		
		extract( shortcode_atts( array(
			'dir' => 'arrangers',
			'userdirs' => 'true',
		), $atts ) );
		
		$style = sprintf('<link rel="stylesheet" type="text/css" href="%s" />', plugins_url('upload.css', __FILE__));
		
		$filelist = '';
		
		$width = 4;
		$files = $upload_handler->get_file_list($width);
		$listed = 0;
		
		$script = __FILE__;
		$action = plugins_url('do-upload.php', __FILE__);
		$self = $_SERVER['SCRIPT_URL'];
		
		foreach( $files as $file ){
			if(preg_match("/^\./", $file) || $file == $script): continue; endif;
			
			$listed++;	
			
			$url = $upload_handler->get_download_url($file);
			
			$icon = $upload_handler->get_file_icon($file);
			
			$file = sprintf('<a href="%s/download">%s</a>', $url, $file);
			
			$filelist .= "<li class=\"span{$width}\"><div class=\"media\"><a class=\"pull-left\" href=\"#\">{$icon}</a><div class=\"media-body\">{$file}</div></div></li>";
		}
		
		$errors = $upload_handler->get_flashdata();
		
		if ($errors) {
			foreach($errors as $errorwhat => $errorwhy) {
				if ($errorwhat == '(nofile)') {
					$errorlist.= "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>
<strong>Error:</strong> <span class='errorwhy'>{$errorwhy}</span>.</div>";
				}
				else {
				$errorlist.= "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>
<strong>Error:</strong> <span class='errorwhy'>{$errorwhy}</span>: <span class='errorwhat'>{$errorwhat}</span>.</div>";
				}
			}
		}
		
		$upload_handler->clear_flashdata();
		
		if ($listed==0) {
			$filelist .= "<li class=\"span{$width} nofiles\">No files were found.</li>";
		}
		
		$icon_load = $upload_handler->get_file_icon('load');
		$icon_error = $upload_handler->get_file_icon('error');
		
$html = <<<HTML
<div class="row-fluid">
	<div class="span4">
		<ul id="uploadmethods" class="nav nav-tabs">
			<!--[if !(IE 6) & !(IE 7) & !(IE 8)  ]><!-->			
			<li><a href="#improved" data-toggle="tab">Advanced Uploader</a></li>
			<!--<![endif]-->			
			<li class="active"><a href="#basic" data-toggle="tab">Basic Uploader</a></li>
		</ul>
		<div class="tab-content">
			<!--[if !(IE 6) & !(IE 7) & !(IE 8)  ]><!-->
			<div id="improved" class="tab-pane">
				<div id="dropzone" class="fade well" style="text-align:center;">
					<p id="drophere"><strong>Drop Files Here</strong></p>
					<p>or</p>
					<p>
						<span class="btn fileinput-button">
							<span>Select Files</span>
<input id="fileupload" type="file" name="files[]" data-url="{$action}" multiple>
						</span>
					</p>
				</div>
				<p>
					If you are experiencing problems uploading files, try the <a id="gobasic" href="#basic">Basic uploader</a>. 
Alternatively, you may email your files to <a href="mailto:arranger.upload@files.lowrez.com.au" class="nobreak"><i class="icon-envelope">&nbsp;</i>arranger.upload@files.lowrez.com.au</a>.
				</p>
			</div>
			<!--<![endif]-->
			<div id="basic" class="tab-pane active">
				<form method="post" action="{$action}" enctype="multipart/form-data">
					<input type="hidden" name="noscript" value="true">
					<input type="hidden" name="redirect" value="{$self}#basic_uploader">
					<ul class="unstyled">
						<li><input type="file" name="files[]"></li>
						<li><input type="file" name="files[]"></li>
						<li><input type="file" name="files[]"></li>
						<li><input type="file" name="files[]"></li>
						<li><input type="file" name="files[]"></li>
					</ul>
					<input type="submit" value="Upload" class="btn" id="basicsubmit"><span id="loader" style="display:none;"></span>
				</form>
				<p>
					If you are experiencing problems uploading files, you may email your files to <a href="mailto:arranger.upload@files.lowrez.com.au" class="nobreak"><i class="icon-envelope">&nbsp;</i>arranger.upload@files.lowrez.com.au</a>.
				</p>
			</div>
		</div>
<p>
A notification email will be sent to the Music Coordinator when your documents have been successfully uploaded.
</p>
	<div id="uploaderrors">
	{$errorlist}						
	</div>
	</div>
	<div class="span8">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#basic">Uploaded Files</a></li>
	</ul>
		<span id="nofile">{$icon_load}</span><span id="errorfile">{$icon_error}</span>
		<ul id="uploadfileslist" class="uploadfileslist thumbnails">
		{$filelist}
		</ul>
	</div>
</div>
HTML;
		/*<!--<h3 class="filedivider">Public Uploads</h3>
		<ul class="uploadfileslist thumbnails">
		{$publicfilelist}
		</ul>-->*/
		
		return $style.$html;
	}
	
	static function register_script() {
		wp_register_script('jquery.ui.widget', plugins_url('js/jquery.ui.widget.js', __FILE__), array('bootstrap'), '1.0', true);
		wp_register_script('jquery.iframe-transport', plugins_url('js/jquery.iframe-transport.js', __FILE__), array('bootstrap'), '1.0', true);
		wp_register_script('jquery.fileupload', plugins_url('js/jquery.fileupload.js', __FILE__), array('bootstrap'), '1.0', true);
		wp_register_script('jquery.filestyle', plugins_url('js/jquery.filestyle.min.js', __FILE__), array('bootstrap'), '1.0', true);
		
		wp_register_script('lowrez-upload', plugins_url('js/lowrez-upload.js', __FILE__), array('bootstrap'), '1.0', true);
	}
	
	static function print_script() {
		if ( ! self::$add_script )
			return;
		
		wp_print_scripts('jquery.ui.widget');
		wp_print_scripts('jquery.iframe-transport');
		wp_print_scripts('jquery.fileupload');
		wp_print_scripts('jquery.filestyle');
		
		wp_print_scripts('lowrez-upload');
	}
}

LOWREZ_Upload::init();
	
}