<?php

/*
Plugin Name: LOW REZ Dashboard
Description: LOW REZ Dashboard Customization
Author: LOW REZ
Version: 1.0
*/


/* ===================== Dashboard Customization ===================== */
//http://sumtips.com/2011/03/customize-wordpress-admin-bar.html
function add_lowrez_admin_bar_link() {
	global $wp_admin_bar;
	if ( !is_super_admin() || !is_admin_bar_showing() )
		return;
	$wp_admin_bar->add_menu( array(
		'id' => 'lowrez_menu',
		'title' => get_option('blogname'),
		'href' => '#',
	) );
	
	
	$wp_admin_bar->add_menu( array(
		'parent' => 'lowrez_menu',
		'id'     => 'lowrez_members',
		'title' => __( 'Members\' Home'),
		'href' => site_url('members'),
	));
	
	$wp_admin_bar->add_menu( array(
		'parent' => 'lowrez_menu',
		'id'     => 'lowrez_home',
		'title' => __( 'Public Site'),
		'href' => site_url(),
	));
	
}
add_action('admin_bar_menu', 'add_lowrez_admin_bar_link',25);

function lowrez_remove_admin_bar_links() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');
	$wp_admin_bar->remove_menu('site-name');
	$wp_admin_bar->remove_menu('updates');
	$wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'lowrez_remove_admin_bar_links' );


// Hook it in to the dashboard setup action
add_action('wp_dashboard_setup', 'lowrez_members_home');

function lowrez_members_home() {
	if(!current_user_can('promote_users')) {
		wp_redirect('/members/');
		exit;
	}
}

add_action( 'admin_menu', 'lowrez_remove_menu_pages' );
function lowrez_remove_menu_pages() {
	remove_menu_page('edit-comments.php');	
	if (!current_user_can('export')) {
		remove_menu_page('tools.php');	
	}
}

add_action('admin_menu', 'lowrez_files_menu');

function lowrez_files_menu(){
	add_media_page( 'LOW REZ Files', 'LOW REZ Files', 'upload_files', 'lowrez-files', 'render_lowrez_files_page');
}
function render_lowrez_files_page(){
	
	echo '<iframe style="width:100%;height:100%;border:none;" frameborder="0" seamless src="http://files.lowrez.com.au/">';
}

if (false) {
	
?>
<script type="text/javascript">
	function ajaxplorerPopupCallback(data) {
		console.log(data);
		if(typeof(data) === "string"){
			$('.imagepath').val(data);
		}
		else if(typeof(data) === "object"){
			//do something with the array of data
		}
	}
	
	window.onmessage = function (e) {
		ajaxplorerPopupCallback(e.data);
	};
	
	
	function chooseFile(){
		var fbWindow = window.open(
			"http://files.lowrez.com.au/?external_selector_type=popup&relative_path=/files/&filetypes=pdf+mp4+mp3+jpeg+jpg+gif+png&allow_multi=true",
			"ajaxplorer",
			width=500,
			height=500
		);
	}
	jQuery(function() {
		jQuery('#popupajax').click(function() {
			chooseFile();
		});
	});
</script>
<button id="popupajax">Popup</button>
<?php
	
}


