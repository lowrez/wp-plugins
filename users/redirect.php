<?php

function lowrez_login_redirect( $redirect_to, $request, $user ){
	//is there a user to check?
	
	//wp_die('=='.$redirect_to.';');
	
	//wp_die( ($redirect_to === admin_url() ? 'exact' : 'nomatch') . ' ==&gt; '.  $redirect_to );
	
	if ($redirect_to == admin_url() || empty($redirect_to) ) {
		
		//print_pre($user);
		
		if( isset( $user->roles ) ) {
			//check for admins
			if( in_array( "administrator", $user->roles ) ) {
				return admin_url();
			}
			elseif( array_intersect( array('member', 'committee', 'section_leader', 'hiatus'), $user->roles ) ) {
				//elseif (protect_code(array(2,3))) {
				return home_url('members');
			}
			elseif( in_array( "alumni", $user->roles ) ) {
				return home_url('inactive-members');
			}
			elseif( in_array( "musician", $user->roles ) ) {
				return home_url('musicians');
			}
			elseif( in_array( "arranger", $user->roles ) ) {
				return home_url('arrangers');
			}
		}
		
		//return home_url();
	}
	else {
		return $redirect_to;
	}
}
add_filter("login_redirect", "lowrez_login_redirect", 10, 3);

function add_allowed_redirect_hosts( $hosts )
{
	$allowed_hosts = array();
	
	// Add lines similar to the one below, one for each host
	$allowed_hosts[] = 'www.lowrez.com.au';
	$allowed_hosts[] = 'lowrez.com.au';
	$allowed_hosts[] = 'dev.lowrez.com.au';
	$allowed_hosts[] = 'podcast.lowrez.com.au';
	$allowed_hosts[] = 'files.lowrez.com.au';
	$allowed_hosts[] = 'songvote.lowrez.com.au';
	$allowed_hosts[] = 'album.lowrez.com.au';
	$allowed_hosts[] = 'photos.lowrez.com.au';
	$allowed_hosts[] = 'videos.lowrez.com.au';
	
	return array_merge( $hosts, $allowed_hosts );
}

add_filter( 'allowed_redirect_hosts', 'add_allowed_redirect_hosts', 10, 1 );