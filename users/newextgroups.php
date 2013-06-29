<?php

define('LOWREZ_DOMAIN', 'GETOPTION'); //FIXME
define('CLOCKWORK_API_KEY', 'GETOPTION'); //FIXME

/*----------------------------------------------------*/

add_filter( 'update_user_metadata', 'lowrez_user_metadata_change', 5, 5 );
function lowrez_user_metadata_change( $meta_type = null, $user_id, $meta_key, $new_value, $prev_value = '' ) {
	
	if ($meta_key == 'mobile') {
		
		$old_value = format_mobile_clockwork(get_user_meta($user_id, $meta_key, true), true);
		$new_value = format_mobile_clockwork($new_value, true);
		
		if ($old_value != $new_value) {
			$u = new LowRez_User($user_id, false, $new_value, $old_value);
			$u->do_groups();
		}		
	}
}

/*----------------------------------------------------*/

add_action('profile_update', 'lowrez_user_profile_change', 10, 2);
function lowrez_user_profile_change($user_id, $old_user_data = false) {
	$u = new LowRez_User($user_id, $old_user_data->user_email);
	$u->do_groups();
}

/*----------------------------------------------------*/

add_action('set_user_role', 'lowrez_user_role_change', 10, 2);
function lowrez_user_role_change($user_id, $role) {
	$u = new LowRez_User($user_id);	
	$u->do_groups();
}

/*----------------------------------------------------*/

function lowrez_delete_user($user_id) {
	$u = new LowRez_User($user_id);
	$u->delete_all_google_groups();
}
add_action( 'delete_user', 'lowrez_delete_user');

/*----------------------------------------------------*/

class LowRez_User {
	
	private $wp_user;
	private $wp_usermeta;
	private $old_email = false;
	private $old_mobile = false;
	
	private $_email;
	private $_mobile;
	private $_voicepart;
	private $_google_groups_display;
	private $_wp_groups_display;
	
	function __construct($wp_id, $old_email = false, $new_mobile = false, $old_mobile = false) {
		
		require_once 'googleapps/zend.php';
		
		init_zend_gapps();
		$this->id = $wp_id;
		$this->wp_user = get_userdata($wp_id);
		$this->wp_usermeta = get_user_meta($wp_id);
		
		if ($old_email) {
			if ($old_email != $this->email) {
				$this->old_email = $old_email;
			}
		}
		if ($new_mobile || $old_mobile) {
			$this->old_mobile = format_mobile_clockwork($old_mobile);
			$this->wp_usermeta['mobile'] = array($new_mobile);
			
			//wp_die(print_pre($this->wp_usermeta));
		}
	}
	
	function do_groups() {
		//return; //FIXME
		if ($this->old_email || $this->old_mobile) {
			$this->delete_all_google_groups(true);
		}
		
		$this->should_add_google_groups();
		$this->should_add_sms_google_group();
		
		$this->should_add_wp_groups();
		
	}
	
	/*----------------------------------------------------*/
	
	function __get($property) {
		
		if (!$this->{"_$property"}) {
			switch ($property) {
				
				/*case 'newmember' :
				$meta = $this->wp_usermeta['newmember'];
				$value = @array_shift($meta);
				break;*/
				
				case 'email' :
				$value = $this->wp_user->user_email;
				break;
				
				case 'mobile' :
				$meta = $this->wp_usermeta['mobile'];
				$value = format_mobile_clockwork(@array_shift($meta));
				//wp_die(print_pre($value));
				break;
				
				case 'voicepart' :
				$meta = $this->wp_usermeta['voicepart'];
				$value = @array_shift($meta);
				break;
				
				case 'google_groups_display' :
				//return false; //FIXME
				
				global $gdata;
				
				$value = array();
				
				if ($email = $this->email) {
					try {
						$groups = $gdata->retrieveGroups($email);
						
						$added_members = false;
						
						foreach ($groups as $group) {
							$g = new GAppsObject($group);
							$value[] = $g;
							if (!$added_members && in_array(
								uptoat($g->groupId),
								array('bass', 'baritone', 'tenor1', 'tenor2', 'hiatus', 'others'))
							   ) {
								$g = new stdClass();
								$g->groupId = 'members@' .LOWREZ_DOMAIN;
								$g->groupName = 'Members';
								$g->description = 'All members';
								$value[] = $g;
								$added_members = true;
							}
						}
						
					} catch(Zend_Gdata_Gapps_ServiceException $ex) {
						//ERROR
					}
				}
				
				if ($mobile = $this->mobile) {
					try {
						$groups = $gdata->retrieveGroups($mobile);
						//}
						foreach ($groups as $group) {
							$g = new GAppsObject($group);
							$value[] = $g;
						}
					} catch(Zend_Gdata_Gapps_ServiceException $ex) {
						//ERROR
					}
				}
				
				
				break;
				
				case 'wp_groups_display' :
				$value = CTXPS_Queries::get_groups($this->id);
				break;
				
			}
			$this->{"_$property"} = $value;
		}
		return $this->{"_$property"};
	}
	
	/*----------------------------------------------------*/
	
	function has_roles( $role ) {
		if (!is_array($role)) { $role = array($role); }
		return (bool) count(array_intersect($role, $this->wp_user->roles));
	}
	function no_roles() {
		return empty($this->wp_user->roles);
	}
	
	/*----------------------------------------------------*/
	
	function should_add_google_groups() {
		
		$does_have = $this->get_google_groups();
		$should_have = array();
		
		if ( $this->no_roles() ) {
			// Add no groups, remove from all groups
		}
		elseif ($this->has_roles('musician')) {
			//No google groups
		}
		elseif ($this->has_roles('arranger')) {
			$should_have[] = 'arrangers';
		}
		elseif ($this->has_roles('alumni')) {
			$should_have[] = 'alumni';
		}
		elseif ($this->has_roles('hiatus')) {
			$should_have[] = 'hiatus';
		}
		/*elseif ($this->has_roles('arranger')) {
		$should_have[] = 'arrangers'; //fans
		}*/
		else { //member, committee, section_leader
			
			if ($voicepart = $this->voicepart) {
				
				$voiceparts = array(
					't1' => 'tenor1',
					't2' => 'tenor2',
					'bar' => 'baritone',
					'b' => 'bass'
				);
				
				$should_have[] = $voiceparts[$voicepart];
				
			}
			else {
				$should_have[] = 'others'; //maybe?
			}
			
			/*if ($newmember = $this->newmember) {
				$should_have[] = 'newmembers';
				}*/
			
			//committee not needed
			
			if ($this->has_roles('section_leader')) {
				$should_have[] = 'sectionleaders';
			}
			
		}
		
		
		
		$diff = $this->add_or_remove($does_have, $should_have);
		
		$this->add_google_groups($diff['add'], $this->email);
		$this->remove_google_groups($diff['remove'], $this->email);
		
	}
	
	function should_add_sms_google_group() {
		
		$does_have = $this->get_google_groups(true);//mobile
		
		if ($mobile = $this->mobile) {
			
			if ($this->has_roles(array('member', 'committee', 'section_leader'))) {
				if (!in_array('sms.members', $does_have)) {
					//wp_die($this->mobile);
					$this->add_google_groups(array('sms.members'), $mobile);
				}
			}
			else { // Not active
				if (in_array('sms.members', $does_have)) {
					$this->remove_google_groups(array('sms.members'), $mobile);	
				}	
			}	
		}
	}
	
	/*----------------------------------------------------*/
	
	function should_add_wp_groups() {
		
		$does_have = $this->get_wp_groups();
		$should_have = array();
		
		if ( $this->no_roles() ) {
			// Add no groups, remove from all groups
		}
		elseif ($this->has_roles('alumni')) { //Inactive
			$should_have[] = 17;
		}
		elseif ($this->has_roles('hiatus')) { //Hiatus==members
			$should_have[] = 2;
		}
		elseif ($this->has_roles('musician')) {
			$should_have[] = 18;
		}
		elseif ($this->has_roles('arranger')) {
			$should_have[] = 15;
		}
		elseif ($this->has_roles('fan')) {
			$should_have[] = 5;
		}
		else { //member, committee, section_leader
			
			if ($voicepart = $this->voicepart) {
				
				$voiceparts = array(
					't1' => 11,
					't2' => 12,
					'bar' => 13,
					'b' => 14
				);
				
				$should_have[] = $voiceparts[$voicepart];
				
			}
			$should_have[] = 2;
			
			if ($this->has_roles('committee')) {
				$should_have[] = 3;
				$should_have[] = 16;
			}
			elseif ($this->has_roles('section_leader')) {
				$should_have[] = 16;
			}
			
		}
		
		$diff = $this->add_or_remove($does_have, $should_have);
		
		$this->add_wp_groups($diff['add']);
		$this->remove_wp_groups($diff['remove']);
		
	}
	
	/*----------------------------------------------------*/
	
	function add_or_remove($does_have, $should_have) {
		
		$needs = array_diff($should_have, $does_have);
		$doesnt_need = array_diff($does_have, $should_have);
		//$keep = array_intersect($should_have, $does_have);//Redundant
		
		$diff = array(
			'add' => $needs,
			'remove' => $doesnt_need
		);
		
		return $diff;
		
	}
	
	/*----------------------------------------------------*/
	
	function get_wp_groups() {
		
		$value = array();
		$groups = CTXPS_Queries::get_groups($this->id);
		
		foreach ($groups as $group) {
			$value[] = $group->ID;
		}
		
		return $value;
		
	}
	
	
	function add_wp_groups($groups) {
		if (is_array($groups)) {
			foreach ($groups as $group) {
				CTXPS_Queries::add_membership($this->id, $group);
			}
		}
	}
	
	function remove_wp_groups($groups) {
		if (is_array($groups)) {
			foreach ($groups as $group) {
				CTXPS_Queries::delete_membership($this->id, $group);
			}
		}
	}
	
	/*----------------------------------------------------*/
	
	function get_google_groups($mobile = false) {
		
		global $gdata;
		$value = array();
		
		if (!$mobile) {
			try {
				$groups = $gdata->retrieveGroups($this->email);
				foreach ($groups as $group) {
					$g = new GAppsObject($group);
					$value[] = uptoat($g->groupId);
				}
			} catch(Zend_Gdata_Gapps_ServiceException $ex) {
				//ERROR
			}
		}
		else {
			if ($mobile = $this->mobile) {
				try {
					$groups = $gdata->retrieveGroups($mobile);
					foreach ($groups as $group) {
						$g = new GAppsObject($group);
						$value[] = uptoat($g->groupId);
					}
				} catch(Zend_Gdata_Gapps_ServiceException $ex) {
					//ERROR
				}
			}
		}
		
		return $value;
		
	}
	
	
	function add_google_groups($groups, $id) {
		
		/*if ($id!='jgtest@test.lowrez.com.au') {
		wp_die(print_pre($groups).print_pre($id));
		}*/
		if ($id) {
			if (is_array($groups)) {
				global $gdata;
				foreach ($groups as $group) {
					try {
						$gdata->addMemberToGroup($id, domain($group));
					} catch(Zend_Gdata_Gapps_ServiceException $ex) {
						//ERROR
					}
					
				}
			}
		}
	}
	
	function remove_google_groups($groups, $id) {
		if ($id) {
			if (is_array($groups)) {
				global $gdata;
				foreach ($groups as $group) {
					try {
						$gdata->removeMemberFromGroup($id, domain($group));
					} catch(Zend_Gdata_Gapps_ServiceException $ex) {
						//ERROR
					}
					
				}
			}
		}
	}
	
	/*----------------------------------------------------*/
	
	function delete_all_google_groups($old = false) {
		
		if ($old) {
			$email = $this->old_email;
			$mobile = $this->old_mobile;
		}
		else {
			$email = $this->email;
			$mobile = $this->mobile;
		}
		
		//wp_die(
		/*print_pre(
		array(
		'old' => $old,
		'email' => $this->email,
		'mobile' => $this->mobile,
		'old_email' => $this->old_email,
		'old_mobile' => $this->old_mobile
		)
		);*/
		//);
		
		global $gdata;			
		if ($email) {
			try {
				$groups = $gdata->retrieveGroups($email);
				foreach ($groups as $group) {
					$g = new GAppsObject($group);
					// Remove email from all existing groups
					try {
						$gdata->removeMemberFromGroup($email, $g->groupId);
					} catch(Zend_Gdata_Gapps_ServiceException $ex) {
						//ERROR
					}
					
				}
			} catch(Zend_Gdata_Gapps_ServiceException $ex) {
				//ERROR
			}
		}
		if ($mobile) {
			try {
				$groups = $gdata->retrieveGroups($mobile);
				foreach ($groups as $group) {
					$g = new GAppsObject($group);
					// Remove mobile from all existing groups
					try {
						$gdata->removeMemberFromGroup($mobile, $g->groupId);
					} catch(Zend_Gdata_Gapps_ServiceException $ex) {
						//ERROR
					}
					
				}
			} catch(Zend_Gdata_Gapps_ServiceException $ex) {
				//ERROR
			}
		}
		
	}
	
	
}

/*----------------------------------------------------*/

function format_mobile_clockwork($mobile, $nodomain = false) {
	$preedit = $mobile;
	
	$mobile = uptoat($mobile); //Remove any existing SMS email domain
	$mobile = preg_replace('/[^0-9\r\n]+/m', '', $mobile); //Remove non-numerals
	$mobile = preg_replace('/^\+?(61)?0?/m', '', $mobile); //Remove any part of +610 prefix, replace with nothing
	
	if ($nodomain) return $mobile;
	
	if (substr('0'.$mobile, 0, 2) == '04' && strlen('0'.$mobile) == 10) {	// Is a mobile number (less 0)
		$mobile = '61'.$mobile.'@'.CLOCKWORK_API_KEY.'.clockworksms.com'; // Add 61 prefix and Clockwork API key and domain
	}
	else {
		//new WP_Error('invalid_mobile', __("The mobile number $preedit you have entered is not valid."));
		return false;
	}
	return $mobile;
	
}

/*----------------------------------------------------*/

function uptoat($str) {
	return strstr($str.'@', '@', true);
}

function domain($str) {
	return uptoat($str).'@'.LOWREZ_DOMAIN;
}

/*----------------------------------------------------*/