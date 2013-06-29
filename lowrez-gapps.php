<?php
/*
Plugin Name: LOW REZ Google Apps Interface
Description: LOW REZ Google Apps Interface
Author: LOW REZ
Version: 1.1.1
*/

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

add_action('admin_menu', 'lowrez_gapps_menu');

function lowrez_gapps_menu(){
	add_menu_page( 'Google Apps', 'Google Apps', 'promote_users', 'google-apps', 'render_lowrez_gapps_menu');
	add_submenu_page( 'google-apps', 'Groups', 'Groups', 'promote_users', 'google-apps-group', 'render_lowrez_gapps_menu_groups');
	add_submenu_page( 'google-apps', 'Users', 'Users', 'promote_users', 'google-apps-user', 'render_lowrez_gapps_menu_groups');
	remove_submenu_page( 'google-apps', 'google-apps');
}
function render_lowrez_gapps_menu(){
}

function render_lowrez_gapps_menu_groups(){
	
	$screen = get_current_screen();
	
	switch ($screen->base) {
		case 'google-apps_page_google-apps-user':
		$type = 'user';
		$id = $_GET['memberId'];
		break;
		case 'google-apps_page_google-apps-group':
		$type = 'group';
		$id = $_GET['groupId'];
		break;
		default:
		wp_die('Invalid page.');
	}
	
	//if ($type == 'user' && !$id) wp_die('Invalid page');
	
	$table = new GApps_List_Table($type, $id);
	$table->prepare_items();
	
?>
<div class="wrap">
	<div id="icon-edit" class="icon32 icon32-gapps">
		<br/>
	</div>
	<h2>Google Apps</h2>
	<h3><?php echo $table->table_title(); ?></h3>
	<?php $table->display(); ?>
</div>
<?php
}

class GApps_List_Table extends WP_List_Table {
	
	public $type;
	public $id;
	public $sms;
	public $user;
	
	// ------------------------------------------------------ //
	function __construct($type='group', $id=false) {
		
		parent::__construct( array(
			'singular'=> 'google-apps',
			'plural' => 'google-apps',
			'ajax'	=> false
		) );
		
		require_once 'users/googleapps/zend.php';
		init_zend_gapps();
		
		$this->type = $type;
		$this->id = $id;
		$this->sms = uptoat($this->id) == 'sms.members';
		if ($this->type == 'user' && $id) {
			$this->user = get_user_by('email', $this->id);
		}
		
	}
	
	// ------------------------------------------------------ //
	
	function table_title() {
		switch ($this->type) {
			case 'group':
			if ($this->id) { return 'Group: ' . $this->id; }
			return 'All Groups';
			break;
			case 'user':
			if ($this->id) {
				$user = $this->user ? $this->user->display_name : 'Unknown';
				return 'User: ' . $this->id . ' ('. $user . ')';
			}
			return 'All Users';
			break;
		}
	}
	
	// ------------------------------------------------------ //
	function get_columns() {
		
		$columns = array(
			'group' => array(
				'memberId'=>__('Email'),
				'memberUser'=>__('Member'),
				'memberType'=>__('Type'),
				//'description'=>__('Description'),
				
			),
			'groups' => array(
				'groupId'=>__('Email'),
				'groupName'=>__('Group'),
				'emailPermission'=>__('Type'),
				//'description'=>__('Description'),
			),
			'user' => array(
			),
			'users' => array(
				'memberId'=>__('Email'),
				'memberUser'=>__('Member'),
				'userRole'=>__('Type'),
			)
		);
		
		switch ($this->type) {
			case 'group':
			if ($this->id) {
				if ($this->sms) { $columns['group']['memberId'] = 'Mobile'; }
				return $columns['group'];
			}
			else {
				return $columns['groups'];
				
			}
			break;
			case 'user':
			if ($this->id) {
				return $columns['groups'];
			}
			else {
				return $columns['users'];
			}
			break;
		}
		
	}
	
	// ------------------------------------------------------ //
	function prepare_items() {
		
		global $wpdb, $_wp_column_headers;
		
		/* — Register the Columns — */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		global $gdata;
		
		try {
			
			switch ($this->type) {
				case 'group':
				if ($id = $this->id) {
					$items = $gdata->retrieveAllMembers($id);
				}
				else {
					$items = $gdata->retrieveAllGroups();
				}
				break;
				case 'user':
				if ($id = $this->id) {
					$items = $gdata->retrieveGroups($id);
				}
				else {
					$items = 'users';
				}
				break;
				default:
				
			}
			
			if ($items != 'users') {
				foreach ($items as $item) {
					$new = new GAppsObject($item);
					
					if ($this->sms) {
						$new->mobile = format_mobile(uptoat($new->memberId));
						if ($new->mobile && $user = get_user_by_meta_data('mobile', $new->mobile)) {
							$new->userId = $user->ID;
							$new->userName = $user->display_name;
						}
						else {
							$new->userId = false;
							$new->userName = false;
						}
					}
					else {
						if ($user = get_user_by('email', $new->memberId)) {
							$new->userId = $user->ID;
							$new->userName = $user->display_name;
						}
						else {
							$new->userId = false;
							$new->userName = false;
						}
					}
					
					$this->items[] = $new;
					
				}
				
				if ($this->type == 'group' && $this->id) {
					usort($this->items, array(&$this, 'sortUserName'));
				}
				
			}
			elseif ($items == 'users') {
				
				$args = array( 'orderby' => 'display_name' );
				
				global $wp_roles;
				$user_query = new WP_User_Query( $args );
				
				if ( !empty( $user_query->results ) ) {
					foreach ( $user_query->results as $user ) {
						
						$member = new stdClass();
						$member->memberType = 'WP_User';
						$member->memberId = $user->user_email;
						$member->userId = $user->ID;
						$member->userName = $user->display_name;
						$member->userRole = $wp_roles->role_names[array_shift($user->roles)];
						
						$this->items[] = $member;
					}
				} else {
					$this->items = array();
				}
			}
			else {
				$this->items = array();
			}
			
			//print_pre($items);
			
		} catch(Zend_Gdata_Gapps_ServiceException $ex) {
			//ERROR
		}
		
	}
	
	// ------------------------------------------------------ //
	function column_groupId($item) {
		return sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=google-apps-group&groupId='.$item->groupId), $item->groupId);
	}
	
	function column_emailPermission($item) {
		switch ($item->emailPermission) {
			case 'Domain':
			return 'Discussion';
			break;
			case 'Owner':
			return 'Announcement';
			break;
			default:
			return $item->emailPermission;
		}
	}
	function column_memberType($item) {
		switch ($item->memberType) {
			case 'User':
			return $item->userId ? 'Recipient' : 'Unknown Recipient';
			break;
			case 'Owner':
			return 'Sender';
			break;
			default:
			return $item->memberType;
		}
	}
	
	function column_memberId($item) {
		if ($item->memberType == 'Group') {
			return sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=google-apps-group&groupId='.$item->memberId), $item->memberId);
		}
		else {
			if ($this->sms) {
				return format_mobile($item->mobile, 'display');
			}
			else {
				return sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=google-apps-user&memberId='.$item->memberId), $item->memberId);
			}
		}
	}
	
	function column_memberUser($item) {
		if ($this->sms) {
			if ( $item->userId ) {
				return sprintf('<a href="%s">%s</a>', admin_url('user-edit.php?user_id='.$item->userId), $item->userName);
			}
			else {
				return '&mdash;';
			}
		}
		else {
			if ($item->memberType == 'WP_User') {
				return sprintf('<a href="%s">%s</a>', admin_url('user-edit.php?user_id='.$item->userId), $item->userName);
			}
			/*elseif ($item->memberType == 'Group') {
			return $item->memberName;
			}*/
			elseif ($item->userId) {
				return sprintf('<a href="%s">%s</a>', admin_url('user-edit.php?user_id='.$item->userId), $item->userName);
			}
			else {
				return '&mdash;';
			}
		}
	}
	
	function column_default($item, $column) {
		return $item->$column;
	}
	// ------------------------------------------------------ //
	
	function display_tablenav($which) {
		return false;
	}
	
	function no_items() {
		switch ($this->type) {
			case 'user':
			if ($this->id) {
				_e('The user ' . $this->id . ' is not a member of any groups.');
			}
			else {
				_e('No users found.');
			}
			break;
			case 'group':
			if ($this->id) {
				_e('The group ' . $this->id . ' has no members.');
			}
			else {
				_e('No groups found.');
			}
			break;
		}
		
	}
	
	function sortUserName($a, $b) {
		if ($a->memberType == 'Group') { return -1; }
		if ($b->memberType == 'Group') { return 1; }
		if ($a->userName === false) { return 1; }
		if ($b->userName === false) { return -1; }
		$al = strtolower($a->userName);
		$bl = strtolower($b->userName);
		if ($al == $bl) {
			return 0;
		}
		return ($al > $bl) ? +1 : -1;
	}
}