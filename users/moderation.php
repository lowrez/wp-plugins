<?php

//http://plugins.svn.wordpress.org/theme-my-login/trunk/modules/user-moderation/admin/user-moderation-admin.php

//add_filter('user_row_actions', 'lowrez_user_row_actions', 20, 2);
function lowrez_user_row_actions($actions, $user_object) {
	
	$current_user = wp_get_current_user();
	
	if ($current_user->ID != $user_object->ID) {
		if (in_array('pending', (array)$user_object->roles)) {
			//switch ( $this->get_option( 'type' ) ) {
			/*case 'email' :
			// Add "Resend Activation" link
			$actions['resend-activation'] = sprintf( '<a href="%1$s">%2$s</a>',
			add_query_arg( 'wp_http_referer',
			urlencode( esc_url( stripslashes( $_SERVER['REQUEST_URI'] ) ) ),
			wp_nonce_url( "users.php?action=resendactivation&amp;user=$user_object->ID",
			'resend-activation' )
			),
			__( 'Resend Activation', 'theme-my-login' )
			);
			break;*/
			//case 'admin' :
			// Add "Approve" link
			if (isset($actions['approve-user'])) {
				$actions['approve-user'] = sprintf('<a href="%1$s">%2$s</a>', add_query_arg('wp_http_referer', urlencode(esc_url(stripslashes($_SERVER['REQUEST_URI']))), wp_nonce_url("users.php?action=approve&amp;user=$user_object->ID&amp;to_role=subscriber", 'approve-user')), __('Make Fan', 'theme-my-login'));
				
				$tmp_edit = @$actions['edit'];
				$tmp_delete = @$actions['delete'];
				
				unset($actions['edit']);
				unset($actions['delete']);
				
				$actions['approve-user-member'] = sprintf('<a href="%1$s">%2$s</a>', add_query_arg('wp_http_referer', urlencode(esc_url(stripslashes($_SERVER['REQUEST_URI']))), wp_nonce_url("users.php?action=approve&amp;user=$user_object->ID&amp;to_role=contributor", 'approve-user')), __('Make Member', 'theme-my-login'));
				
				$actions['edit'] = $tmp_edit;
				$actions['delete'] = $tmp_delete;
				
			}
			//  break;
			//}
		}
	}
	return $actions;
}

/*------------------------------------------------------------*/
