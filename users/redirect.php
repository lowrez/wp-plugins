<?php

function lowrez_login_redirect( $redirect_to, $request, $user ){
	//is there a user to check?
	
	//wp_die( ($redirect_to === admin_url() ? 'exact' : 'nomatch') . ' ==&gt; '.  $redirect_to );
	
	if ($redirect_to == admin_url() || empty($redirect_to) ) {
		if( is_array( $user->roles ) ) {
			//check for admins
			if( in_array( "administrator", $user->roles ) ) {
				// redirect them to the default place
				return admin_url();
			} else {
				return home_url('members');
			}
		}
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
	$allowed_hosts[] = 'album.lowrez.com.au';
	$allowed_hosts[] = 'files.lowrez.com.au';
	$allowed_hosts[] = 'songvote.lowrez.com.au';
	
	return array_merge( $hosts, $allowed_hosts );
}

add_filter( 'allowed_redirect_hosts', 'add_allowed_redirect_hosts', 10, 1 );