<?php

function deny_code($only_groups = '1') {
	if (!protect_code($only_groups)) {
		CTXPS_Security::deny_access();
	}
}

function protect_code($only_groups = '1', $user_id = false) {
	
	if (!$user_id) {
		global $current_user;
		$user_id = $current_user->ID;
		
		if (!$user_id) return false;
	}
	$user_groups = CTXPS_Queries::get_user_groups($user_id);
	
	if (!is_array($only_groups)) {
		$only_groups = preg_replace('/[^0-9,]/im', '', $only_groups);
		$only_groups = explode(',', $only_groups);
	}
	
	if (count(array_intersect($only_groups, array_keys($user_groups))) == 0) {
		//We return false as the user does not have access
		return false;
	}
	
	return true;
	
	/*foreach ( $user_groups as $group ) {
	if ( in_array( $group->id, $user_groups ) ) {
	return true;
	}
	}
	
	return false;*/
	
}

function protect_taxon($term_id, $taxonomy) {
	global $current_user;
	$user_groups = CTXPS_Queries::get_user_groups($current_user->ID);
	$only_groups = array_keys(CTXPS_Queries::get_groups_by_term($term_id, $taxonomy, true));
	
	if (!is_array($only_groups)) {
		$only_groups = preg_replace('/[^0-9,]/im', '', $only_groups);
		$only_groups = explode(',', $only_groups);
	}
	
	if (count($only_groups) && count(array_intersect($only_groups, array_keys($user_groups))) == 0) {
		//We return false as the user does not have access
		return false;
	}
	
	return true;
}

add_shortcode('protect_block', 'protect_code_func');
function protect_code_func($atts, $content = null) {
	extract(shortcode_atts(array(
		'only' => '1',
		//'except' => '1',
		'instead' => '' //<i>Protected content.</i>
	), $atts));
	
	if (preg_match('%\s*(?P<content>.*?)\s*(?![instead])(?:\[instead\]\s*(?P<instead>.+?).s*\[/instead\])%s', $content, $matches)) {
		$instead = $matches['instead'];
		$content = $matches['content'];
	}
	
	if (protect_code($only)) {
		return wptexturize(do_shortcode(trim_br($content)));
	} else {
		return wptexturize(do_shortcode(trim_br($instead)));
	}
}