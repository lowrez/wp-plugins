<?php

function lowrez_admin_head() {
	echo '<link rel="stylesheet" type="text/css" href="' .plugins_url('wp-admin.css', __FILE__). '">';
}

add_action('admin_head', 'lowrez_admin_head');

function bank_ref( $atts = false ){
global $current_user;
$q = floor((date('n')- 1) / 3) + 1;

 return 'Q'.$q.' '.strtoupper($current_user->user_login);
}
add_shortcode( 'bank_ref', 'bank_ref' );

/*---------------------------------------------*/
/*User Data*/
/*---------------------------------------------*/
function lowrez_user_contactmethods($contactmethods) {
	//Add fields
	$contactmethods['mobile'] = 'Mobile Phone';
	$contactmethods['street'] = 'Address';
	$contactmethods['suburb'] = 'Suburb';
	$contactmethods['postcode'] = 'Postcode';
	
	//Remove fields
	unset($contactmethods['url']); // FIXME
	unset($contactmethods['aim']);
	unset($contactmethods['jabber']);
	unset($contactmethods['yim']);
	
	return $contactmethods;
}

add_filter('user_contactmethods', 'lowrez_user_contactmethods', 10, 1);
/*---------------------------------------------*/

remove_action("admin_color_scheme_picker", "admin_color_scheme_picker");

//$all_groups = array();
/*---------------------------------------------*/
function lowrez_user_table($column) {
	//global $all_groups;
	
	
	//$all_groups = CTXPS_Queries::get_groups();
	
	$column['voicepart'] = 'Part';
	$column['mobile'] = 'Mobile';
	$column['suburb'] = 'Suburb';
	//$column['who-we-are'] = 'Who We Are';
	
	unset($column['posts']);
	unset($column['role']);
	
	$column['group'] = 'Access Level';
	
	return $column;
}

/*---------------------------------------------*/
function lowrez_user_views($views){
	
	unset($views['administrator']);
	
	$edit_url = admin_url('users.php');
	
	$views['voicepart-newmembers'] = '<a href="'.$edit_url.'?newmembers=true">New Members</a>';
	
	$views['voicepart-tenor1'] = '<a href="'.$edit_url.'?voicepart=t1">Tenor 1</a>';
	$views['voicepart-tenor2'] = '<a href="'.$edit_url.'?voicepart=t1">Tenor 2</a>';
	$views['voicepart-baritone'] = '<a href="'.$edit_url.'?voicepart=bar">Baritone</a>';
	$views['voicepart-bass'] = '<a href="'.$edit_url.'?voicepart=b">Bass</a>';
	
	$views['voicepart-none'] = '<a href="'.$edit_url.'?voicepart=n">No Voice Part</a>';
	
	return $views;
}
//add_filter('views_users', 'lowrez_user_views', 10, 1);

//add_filter('pre_user_query', 'lowrez_user_items', 10, 1);
function lowrez_user_items(&$user_query) {
	
	if ( isset( $_REQUEST['voicepart'] ) )  {
		
		$qv['meta_key'] = 'voicepart';
		
		$voicepart = $_REQUEST['voicepart'];
		if ($voicepart == 'n') {
			$qv['meta_compare'] = 'NOT EXISTS';
		}
		else {
			$qv['meta_value'] = $voicepart;
			$qv['meta_compare'] = '=';
		}
		
		global $wpdb;
		
		$meta_query = new WP_Meta_Query();
		$meta_query->parse_query_vars( $qv );
				
		if ( !empty( $meta_query->queries ) ) {
			$clauses = $meta_query->get_sql( 'user', $wpdb->users, 'ID', $user_query );
			$user_query->query_from .= $clauses['join'];
			$user_query->query_where .= $clauses['where'];
		}
		
		//$user_query->prepare_query();
		
		//print_pre($user_query);
		
	}
	
	//return $user_query;
}


/*---------------------------------------------*/

add_filter('manage_users_columns', 'lowrez_user_table');

function lowrez_user_table_row($val, $column_name, $user_id) {
	$user = get_userdata($user_id);
	//global $all_groups;
	//echo $column_name;
	switch ($column_name) {
		
		case 'who-we-are' :
		$show = get_user_meta($user_id, 'who_we_are_show', true);
		
		if ($show) {
			return 'Yes';
		}
		
		break;
		
		case 'mobile' :
		return format_mobile(get_user_meta($user_id, $column_name, true), 'display');
		break;
		case 'suburb' :
		return get_user_meta($user_id, $column_name, true);
		break;
		case 'group' :
		$g = array();
		//$g[] = $user->role;
		
		global $wp_roles;
		$role = array_shift($user->roles);
		$rl = $wp_roles->role_names[$role];
		//$g[] = '<strong>' . $rl . '</strong><br>';
		return $rl; //'<strong>' . $rl . '</strong><br>';
		
		$groups = CTXPS_Queries::get_groups($user->ID);
		/*$gr_sort = array(4,3,16,2,10,11,12,13,14,15,5,17);
		$grs = array();
		foreach ($gr_sort as $gr) {
		$grs[] = $group->ID;
		}*/
		
		foreach ($groups as $group) {
			if ($group->group_title == 'Tenor 1' || $group->group_title == 'Tenor 2') {
				$gr = 'T' . substr($group->group_title, -1);
			} else {
				$gr = substr($group->group_title, 0, 3);
			}
			$g[] = '<abbr class="noabbr" title="' . $group->group_title . '">' . $gr . '</abbr>';
			//$g[] = '<input type="checkbox" title="'.$group->group_title.'" />';
		}
		/*foreach ($all_groups as $group) {
		$checked = in_array($group->ID, $grs) ? ' checked="checked"' : '';
		$g[] = '<input type="checkbox" title="'.$group->group_title.'"'.$checked.'
		disabled="disabled" />';
		}*/
		return implode('&middot;', $g);
		break;
		
		case 'voicepart' :
		$val = get_user_meta($user_id, 'voicepart', true);
		
		if ($val) {
			$val = format_voicepart($val, $user->roles, 'short');
		}
		
		return $val;
		break;
		
		default :
		return $val;
	}
	
	return $return;
}

add_filter('manage_users_custom_column', 'lowrez_user_table_row', 30, 3);