<?php

/*
Plugin Name: LOW REZ Dashboard
Description: LOW REZ Dashboard Customization
Author: LOW REZ
Version: 1.0
*/

//include_once 'media/repertoire-thisseason.php';

/* ===================== Dashboard Customization ===================== */
//http://sumtips.com/2011/03/customize-wordpress-admin-bar.html
function add_lowrez_admin_bar_link() {
	global $wp_admin_bar;
	if ( !is_admin_bar_showing() ) return; //!is_super_admin() || 
	
	$wp_admin_bar->add_menu( array(
		'id' => 'lowrez_menu',
		'title' => __( 'LOW REZ'),
		'href' => '#',
		) );
	
	
	
	if (protect_code(array(2,3))) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'lowrez_menu',
			'id'     => 'lowrez_members',
			'title' => __( 'Members\' Home'),
			'href' => site_url('members'),
		));
	}
	elseif (protect_code(array(18))) { // Musicians
		$wp_admin_bar->add_menu( array(
			'parent' => 'lowrez_menu',
			'id'     => 'lowrez_members',
			'title' => __( 'Musicians\' Home'),
			'href' => site_url('musicians'),
		));
	}
	elseif (protect_code(array(15))) { // Arrangers
		$wp_admin_bar->add_menu( array(
			'parent' => 'lowrez_menu',
			'id'     => 'lowrez_members',
			'title' => __( 'Arrangers\' Home'),
			'href' => site_url('arrangers'),
		));
	}
	elseif (protect_code(array(17))) { // Inactive
		$wp_admin_bar->add_menu( array(
			'parent' => 'lowrez_menu',
			'id'     => 'lowrez_members',
			'title' => __( 'Inactive Members'),
			'href' => site_url('inactive-members'),
		));
	}
	
	
	
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

add_action( 'admin_bar_menu', 'wp_admin_bar_my_custom_account_menu', 11 );

function wp_admin_bar_my_custom_account_menu( $wp_admin_bar ) {
	$user_id = get_current_user_id();
	$current_user = wp_get_current_user();
	$profile_url = get_edit_profile_url( $user_id );
	
	if ( 0 != $user_id ) {
		/* Add the "My Account" menu */
		$avatar = get_avatar( $user_id, 28 );
		$howdy = sprintf( __('Welcome, %1$s'), $current_user->display_name );
		$class = empty( $avatar ) ? '' : 'with-avatar';
		
		$wp_admin_bar->add_menu( array(
			'id' => 'my-account',
			'parent' => 'top-secondary',
			'title' => $howdy . $avatar,
			'href' => $profile_url,
			'meta' => array(
				'class' => $class,
			),
		) );
		
	}
	
	//print_pre($wp_admin_bar);
}

//add_action('admin_head', 'iframe_breakout');

function iframe_breakout() {
?>
<script type="text/javascript">
	/*http://css-tricks.com/snippets/javascript/break-out-of-iframe/*/
	this.top.location !== this.location && (this.top.location = this.location);
</script>
<?php
}

// Hook it in to the dashboard setup action
add_action('wp_dashboard_setup', 'lowrez_members_home');

function lowrez_members_home() {
	
	if(!current_user_can('promote_users')) {
		
		if (protect_code(array(2,3))) {
			wp_redirect('/members/');
		}
		elseif (protect_code(array(18))) { // Musicians
			wp_redirect('/musicians/');
		}
		elseif (protect_code(array(15))) { // Arrangers
			wp_redirect('/arrangers/');
		}
		elseif (protect_code(array(17))) { // Inactive/ Alumni
			wp_redirect('/inactive-members/');
		}
		else {
			wp_redirect(home_url());
		}
		
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
	//add_media_page( 'LOW REZ Files', 'LOW REZ Files', 'upload_files', 'lowrez-files', 'render_lowrez_files_page');
}
function render_lowrez_files_page(){
	
	//echo '<iframe style="width:100%;height:100%;border:none;" frameborder="0" seamless src="http://files.lowrez.com.au/">';
	
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


