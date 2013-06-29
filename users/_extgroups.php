<?php

define('CLOCKWORK_API_KEY', '40babc7884296d59cbec0991e3850a9e8f943f4e');

/*add_filter('pre_option_default_role', 'lowrez_get_to_role');
function lowrez_get_to_role() {
	
	if (isset($_GET['to_role'])) {
		if (in_array($_GET['to_role'], array(
			'fan',
			'member'
		))) {
			return $_GET['to_role'];
		} else {
			return false;
		}
	} else {
		return false;
	}
	}*/

/*function lowrez_reset_groups($user_id) {
	
	$u = new LowRez_User($user_id);
	$u->delete_all_groups();
	$u->should_add_groups('role', 'google_groups');
	$u->should_add_groups('role', 'wp_groups');
	$u->section_leader_group();
	$u->should_add_groups('voicepart', 'google_groups');
	$u->should_add_groups('voicepart', 'wp_groups');
	$mobile = format_mobile_clockwork(get_user_meta($user_id, 'mobile', true));
		if ($old_value != $meta_value) {
			$u = new LowRez_User($user_id, false);
			$u->should_replace_google_groups('mobile', $old_value, $new_value);
		}	
	
	}*/

add_action('set_user_role', 'lowrez_user_role_change', 10, 2);
function lowrez_user_role_change($user_id, $role) {
	$u = new LowRez_User($user_id);
	$u->new_role = $role;
	
	//if ($u->active_member()) {
		$u->should_add_groups('role', 'google_groups');
		$u->should_add_groups('role', 'wp_groups');
		$u->section_leader_group();		
	//}
	//else {
	//DO SOMETHING?
	//}
}

add_action('profile_update', 'lowrez_user_profile_change', 10, 2);
function lowrez_user_profile_change($user_id, $old_user_data = false) {
	$u = new LowRez_User($user_id, $old_user_data);
	$u->should_replace_google_groups();
	$u->should_add_groups('voicepart', 'google_groups');
	$u->should_add_groups('voicepart', 'wp_groups');
}

add_filter( 'update_user_metadata', 'lowrez_user_metadata_change', 10, 5 );
function lowrez_user_metadata_change( $meta_type = null, $user_id, $meta_key, $new_value, $prev_value = '' ) {
	
	if ($meta_key == 'mobile') {
		
		$old_value = format_mobile_clockwork(get_user_meta($user_id, $meta_key, true));
		$new_value = format_mobile_clockwork($new_value);
		
		//print_pre('OLD '. var_export($old_value) . ' NEW ' . var_export($new_value));
		
		if ($old_value != $new_value) {
			$u = new LowRez_User($user_id, false);
			$u->should_replace_google_groups('mobile', $old_value, $new_value);
		}		
	}
}

function lowrez_delete_user($user_id) {
	
	$u = new LowRez_User($user_id);
	$u->delete_all_groups();
	
}
add_action( 'delete_user', 'lowrez_delete_user');

//add_action('tml_new_user_activated', 'lowrez_new_to_temp_group');
//add_action('user_register', 'lowrez_new_to_temp_group');
/*function lowrez_new_to_temp_group($user_id) {
$group_id = 10;
// New Members
$expires = "+3 months";
$expires = date("Y-m-d", strtotime(date("Y-m-d") . $expires));

CTXPS_Queries::add_membership_with_expiration($user_id, $group_id, $expires);
}*/

class LowRez_User {
	
	private $wp_user;
	private $wp_usermeta;
	private $old_user_data;
	private $old_usermeta;
	
	public $new_role;
	
	private $_voicepart;
	private $_sectionleader = false;
	
	private $_google_groups;
	private $_wp_groups;
	
	private $group_defs = array(
		'voicepart' => array(
			'google_groups' => array(
				'add' => array(
					't1' => array('tenor1'),
					't2' => array('tenor2'),
					'bar' => array('baritone'),
					'b' => array('bass')
				),
				'remove' => array(
					'tenor1',
					'tenor2',
					'baritone',
					'bass'
				),
				//'ignore' => array()
			),
			'wp_groups' => array(
				'add' => array(
					't1' => array(11),
					't2' => array(12),
					'bar' => array(13),
					'b' => array(14)
				),
				'remove' => array(
					11,
					12,
					13,
					14
				),
				//'ignore' => array()
			)
		),
		'role' => array(
			/*'google_groups' => array(
				'add' => array(
					'hiatus' => array('hiatus'),
					'alumni' => array('alumni'),
				),
				'remove' => array(
					'sectionleaders',
					'alumni'
				),
				//'ignore' => array()
				),*/
			'wp_groups' => array(
				'add' => array(
					'administrator' => array(), // Everything
					'committee' => array(
						3, // Committee
						2, // Members
						5, // Fans
						15 // Arrangers
					),
					'section_leader' => array(
						2, // Members
						5, // Fans
						16 // Section Leaders
					),
					'member' => array(2), // Members
					'fan' => array(5), // Fans
					'arranger' => array(15), // Arrangers
					'alumni' => array(17), // Inactive Members
					'hiatus' => array(17), // Inactive Members
				),
				'remove' => array(
					3, // Committee
					2, // Members
					5, // Fans
					15, // Arrangers
					16, // Section Leaders
					17, // Inactive Members
				),
				/*'ignore' => array(
				1, // All Registered
				10, // New Members (time-based)
				11, // Tenor 1
				12, // Tenor 2
				13, // Baritone
				14, // Bass
				)*/
			))
	);
	
	function __construct($wp_id, $old_user_data = false) {
		
		define('LOWREZ_DOMAIN', 'test.lowrez.com.au');
		require_once 'googleapps/zend.php';
		
		init_zend_gapps();
		$this->id = $wp_id;
		$this->wp_user = get_userdata($wp_id);
		$this->wp_usermeta = get_user_meta($wp_id);
		$this->old_user_data = $old_user_data;
		//$this->old_usermeta = get_user_meta($old_user_data->ID);
	}
	
	function unget($property) {
		$this->{"_$property"} = false;
	}
	
	function __get($property) {
		if (!$this->$property) {
			switch ($property) {
				case 'email' :
				return $this->wp_user->user_email;
				//return str_replace('+', '%2B', $this->wp_user->user_email);
				break;
				
				case 'mobile' :
				$meta = $this->wp_usermeta['mobile'];
				return @array_shift($meta);
				break;
				
				case 'voicepart' :
				$meta = $this->wp_usermeta['voicepart'];
				$value = @array_shift($meta);
				//euf
				//FIXME do away with EUF
				break;
				
				case 'section_leader' :
				case 'sectionleader' :
				$value = in_array('section_leader', $this->wp_user->roles);
				//FIXME Check this array works
				break;
				
				case 'active' :
				$value = !in_array('alumni', $this->wp_user->roles) &&
						 !in_array('hiatus', $this->wp_user->roles) &&
						 !empty($this->wp_user->roles);
				break;
				
				case 'google_groups' :
				global $gdata;
				$value = array();
				
				try {
					/*$old_email = $this->old_user_data->user_email;
					if ($old_email != $this->email) {
					$groups = $gdata->retrieveGroups($old_email);
					}
					else {*/
					$groups = $gdata->retrieveGroups($this->email);
					//}
					foreach ($groups as $group) {
						$g = new GAppsObject($group);
						$value[] = str_replace('@' . LOWREZ_DOMAIN, '', $g->groupId);
					}
				} catch(Zend_Gdata_Gapps_ServiceException $ex) {
					//die($ex->getMessage());}
				}
				
				break;
				
				case 'wp_groups' :
				$value = array();
				$groups = CTXPS_Queries::get_groups($this->id);
				
				foreach ($groups as $group) {
					$value[] = $group->ID;
				}
				break;
				
				
				case 'google_groups_display' :
				//return false; //FIXME//FIXME
				global $gdata;
				$value = array();
				
				try {
					/*$old_email = $this->old_user_data->user_email;
					if ($old_email != $this->email) {
					$groups = $gdata->retrieveGroups($old_email);
					}
					else {*/
					$groups = $gdata->retrieveGroups($this->email);
					//}
					$added_members = false;
					
					foreach ($groups as $group) {
						$g = new GAppsObject($group);
						$value[] = $g;
						if (!$added_members && in_array(
							str_replace('@' . LOWREZ_DOMAIN, '', $g->groupId),
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
					$mobile = format_mobile_clockwork($this->mobile);
					if ($mobile) {
						$groups = $gdata->retrieveGroups($mobile);
						//}
						foreach ($groups as $group) {
							$g = new GAppsObject($group);
							$value[] = $g;
						}
					}
					
					
				} catch(Zend_Gdata_Gapps_ServiceException $ex) {
					//die($ex->getMessage());}
				}
				
				break;
				
				case 'wp_groups_display' :
				$value = CTXPS_Queries::get_groups($this->id);
				
				/*foreach ($groups as $group) {
				$value[] = $group;
				}*/
				break;
				
			}
			$this->{"_$property"} = $value;
		}
		return $this->{"_$property"};
	}
	
	function delete_all_groups() {
		
		global $gdata;			
		try {
			$groups = $gdata->retrieveGroups($this->email);
			foreach ($groups as $group) {
				$g = new GAppsObject($group);
				// Remove email from all existing groups
				try {
					$gdata->removeMemberFromGroup($this->email, $g->groupId);
				} catch(Zend_Gdata_Gapps_ServiceException $ex) {
					//die($ex->getMessage());}
				}
				
			}
		} catch(Zend_Gdata_Gapps_ServiceException $ex) {
			//die($ex->getMessage());}
		}
		if ($mobile = format_mobile_clockwork($this->mobile)) {
			
			try {
				$groups = $gdata->retrieveGroups($mobile);
				foreach ($groups as $group) {
					$g = new GAppsObject($group);
					// Remove email from all existing groups
					try {
						$gdata->removeMemberFromGroup($mobile, $g->groupId);
					} catch(Zend_Gdata_Gapps_ServiceException $ex) {
						//die($ex->getMessage());}
					}
					
				}
			} catch(Zend_Gdata_Gapps_ServiceException $ex) {
				//die($ex->getMessage());}
			}
			
		}
		
	}
	
	function should_replace_google_groups($type = 'email', $old_mobile = false, $new_mobile = false) {
		
		if ($type == 'email') {
			
			if ($this->old_user_data) {
				
				$old_email = $this->old_user_data->user_email;
				
				if ($old_email != $this->email) {
					
					global $gdata;			
					try {
						$groups = $gdata->retrieveGroups($old_email);
						//print_r($groups);
						//die();
						foreach ($groups as $group) {
							$g = new GAppsObject($group);
							// Add new email to all existing groups
							try {
								$gdata->addMemberToGroup($this->email, $g->groupId);
							} catch(Zend_Gdata_Gapps_ServiceException $ex) {
								//die($ex->getMessage());}
							}
							// Remove old email from all existing groups
							try {
								$gdata->removeMemberFromGroup($old_email, $g->groupId);
							} catch(Zend_Gdata_Gapps_ServiceException $ex) {
								//die($ex->getMessage());}
							}
							
						}
					} catch(Zend_Gdata_Gapps_ServiceException $ex) {
						//die($ex->getMessage());}
					}
					
				}
				
			}
		}
		elseif ($type == 'mobile') {
			
			if ($old_mobile != $new_mobile) {
				
				global $gdata;			
				if ($new_mobile) {
					// Add new mobile to all existing groups
					try {
						$gdata->addMemberToGroup($new_mobile, 'sms.members@' . LOWREZ_DOMAIN);
					} catch(Zend_Gdata_Gapps_ServiceException $ex) {
						//die($ex->getMessage());}
					}
				}
				if ($old_mobile) {
					// Remove old mobile from all existing groups
					try {
						$gdata->removeMemberFromGroup($old_mobile, 'sms.members@' . LOWREZ_DOMAIN);
						
					} catch(Zend_Gdata_Gapps_ServiceException $ex) {
						//die($ex->getMessage());}
					}
				}
				
			}
		}
		
	}
	
	function should_add_groups($case, $type) {
		// e.g. voicepart/role, google_groups/wp_groups
		$group_defs = $this->group_defs[$case][$type];
		
		switch ($case) {
			case 'voicepart' :
			$key = $this->voicepart;
			break;
			case 'role' :
			$key = $this->new_role;
			break;
		}
		
		$add_groups = $group_defs['add'][$key];
		
		if (is_array($add_groups)) {
			
			$remove_groups = $group_defs['remove'];
			//$ignore_groups = $group_defs['ignore'];
			$has_groups = $this->$type;
			
			$remove_groups = array_intersect(array_diff($remove_groups, $add_groups), $has_groups);
			$add_groups = array_diff($add_groups, $has_groups);
			
			call_user_func(array(
				$this,
				'add_' . $type
			), $add_groups);
			call_user_func(array(
				$this,
				'remove_' . $type
			), $remove_groups);
			$this->unget($type);
		}
		
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
	
	function add_google_groups($groups) {
		if (is_array($groups)) {
			global $gdata;
			foreach ($groups as $group) {
				try {
					$gdata->addMemberToGroup($this->email, $group . '@' . LOWREZ_DOMAIN);
				} catch(Zend_Gdata_Gapps_ServiceException $ex) {
					//die($ex->getMessage());}
				}
			}
		}
	}
	
	function remove_google_groups($groups) {
		if (is_array($groups)) {
			global $gdata;
			foreach ($groups as $group) {
				try {
					$gdata->removeMemberFromGroup($this->email, $group . '@' . LOWREZ_DOMAIN);
				} catch(Zend_Gdata_Gapps_ServiceException $ex) {
					//die($ex->getMessage());}
				}
			}
		}
	}
	
	function section_leader_group() {
		$groups = $this->google_groups;
		if (is_array($groups)) {
			global $gdata;
			if ($this->new_role == 'section_leader' && !in_array('sectionleaders', $groups)) {
				
				// Is a Section Leader but not in Section Leader GGroup
				try {
					$gdata->addMemberToGroup($this->email, 'sectionleaders@' . LOWREZ_DOMAIN);
				} catch(Zend_Gdata_Gapps_ServiceException $ex) {
					//die($ex->getMessage());}
				}
				// Add to Section Leader GGroup
			} elseif (in_array('sectionleaders', $groups)) {
				// Not a Section Leader but is in Section Leader GGroup
				try {
					$gdata->removeMemberFromGroup($this->email, 'sectionleaders@' . LOWREZ_DOMAIN);
				} catch(Zend_Gdata_Gapps_ServiceException $ex) {
					//die($ex->getMessage());}
				}
				// Remove from Section Leader GGroup
			}
		}
	}
	
	
	function active_member() {
		
		if ( in_array( $this->new_role, array('hiatus', 'alumni') ) ) {
			
			$this->delete_all_groups();
			
			global $gdata;
			try {
				$gdata->addMemberToGroup($this->email, $this->new_role. '@' . LOWREZ_DOMAIN);
			} catch(Zend_Gdata_Gapps_ServiceException $ex) {
				//die($ex->getMessage());}
			}
			
			return false;
		}
		else {
			return true;
		}
		
	}
	
	/*function sms_group() {
	$groups = $this->google_groups;
	if (is_array($groups)) {
	global $gdata;
	if ($this->new_role == 'section_leader' && !in_array('sms.members', $groups)) {
	
	// Is a Section Leader but not in Section Leader GGroup
	try {
	$gdata->addMemberToGroup(format_mobile_clockwork($this->mobile), 'sms.members@' . LOWREZ_DOMAIN);
	} catch(Zend_Gdata_Gapps_ServiceException $ex) {
	//die($ex->getMessage());}
	}
	// Add to Section Leader GGroup
	} elseif (in_array('sectionleaders', $groups)) {
	// Not a Section Leader but is in Section Leader GGroup
	try {
	$gdata->removeMemberFromGroup(format_mobile_clockwork($this->mobile), 'sms.members@' . LOWREZ_DOMAIN);
	} catch(Zend_Gdata_Gapps_ServiceException $ex) {
	//die($ex->getMessage());}
	}
	// Remove from Section Leader GGroup
	}
	}
	}*/
	
}


function format_mobile_clockwork($mobile) {
	$preedit = $mobile;
	
	$mobile = preg_replace('/[^0-9\r\n]+/m', '', $mobile); //Remove non-numerals
	$mobile = preg_replace('/^\+?(61)?0?/m', '', $mobile); //Remove any part of +610 prefix, replace with nothing
	
	if (substr('0'.$mobile, 0, 2) == '04' && strlen('0'.$mobile) == 10) {	// Is a mobile number (less 0)
		$mobile = '61'.$mobile.'@'.CLOCKWORK_API_KEY.'.clockworksms.com'; // Add 61 prefix and Clockwork API key and domain
	}
	else {
		//new WP_Error('invalid_mobile', __("The mobile number $preedit you have entered is not valid."));
		return false;
	}
	return $mobile;
	
}
