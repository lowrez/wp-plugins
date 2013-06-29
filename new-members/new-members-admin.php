<?php

require_once(ABSPATH . 'wp-content/plugins/contact-form-7-to-database-extension/CF7DBPlugin.php');
require_once(ABSPATH . 'wp-content/plugins/contact-form-7-to-database-extension/CFDBFormIterator.php');
require_once(ABSPATH . 'wp-content/plugins/contact-form-7-to-database-extension/CFDBShortcodeCount.php');

/*************************** LOAD THE BASE CLASS *******************************
*******************************************************************************
* The WP_List_Table class isn't automatically available to plugins, so we need
* to check if it's available and load it if necessary.
*/
if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

define('CF_FORMNAME', 'New Member Application');


add_action('wp_dashboard_setup', 'lowrez_dashboard_newmem');
function lowrez_dashboard_newmem() {
	if(current_user_can('promote_users')) {
		
		$title = "New Member Applications";
		
		$count = count_member_apps('unprocessed');//true
		$title .= ' ('.$count.')';
		
		wp_add_dashboard_widget('lowrez_dashboard_newmembers', $title, 'lowrez_dashboard_newmem_render');
	}
}
function lowrez_dashboard_newmem_render() {
	
	$exp = new CFDBFormIterator();
	$atts = array(
		'filter' => 'processed!=approved&&processed!=ignored'
	);
	
	$exp->export(CF_FORMNAME, $atts);
	
	echo '<ul>';
	while ($data = $exp->nextRow()) {
		
		$time = strtotime($data['Submitted']);
		$time = ' <span class="description">('.time_passed($time) . ')</span>';
		
		echo '
<li>
<a href="' . admin_url('admin.php?page=new-member-form&new-member='.$data['submit_time']) .'">
<strong>'.$data['first-name'].' '.$data['last-name'].'</strong>
</a>' . $time . '
</li>';
		
	}
	echo '</ul>';
	
	
	
	echo stripslashes($content);
}



add_filter('views_users', 'kbs_users_views');
function kbs_users_views($views) {
	
	if ($pending = $views['pending']) {
		unset($views['pending']);
		//$views[] = '<br>';
		$views['pending'] = '<br>'.$pending;
	}
	
	return $views;
	
}

function unprocess_member($id, $silently = false) {
	if (!is_array($id)) {
		$id = array($id);
	}
	
	global $wpdb;
	$c = new CF7DBPlugin();
	$tableName = $c->getSubmitsTableName();
	
	foreach ($id as $idz) {
		
		$parametrizedQuery = "DELETE FROM `$tableName` WHERE `form_name` = %s AND (`field_name` = %s OR `field_name` = %s) AND `submit_time` = %s";
		
		$wpdb->query($wpdb->prepare($parametrizedQuery,
									CF_FORMNAME,
									'processed',
									'processed_date',
									$idz
								   ));
		
	}
	$count = count($id);
	$application = $count==1 ? '' : 's';
	
	if (!$silently) echo('<div id="message" class="updated"><p>Unprocessed '.$count.' new member application'.$application.'.</p></div>');
}

function delete_member($id) {
	if (!is_array($id)) {
		$id = array($id);
	}
	
	global $wpdb;
	$c = new CF7DBPlugin();
	$tableName = $c->getSubmitsTableName();
	
	foreach ($id as $idz) {
		
		$parametrizedQuery = "DELETE * FROM `$tableName` WHERE `form_name` = %s AND `submit_time` = %s";
		
		$wpdb->query($wpdb->prepare($parametrizedQuery,
									CF_FORMNAME,
									$idz
								   ));
		
	}
	$count = count($id);
	$application = $count==1 ? '' : 's';
	
	echo('<div id="message" class="updated"><p>Deleted '.$count.' new member application'.$application.' permanently.</p></div>');
}

function ignore_member($id) {
	if (!is_array($id)) {
		$id = array($id);
	}
	
	global $wpdb;
	$c = new CF7DBPlugin();
	$tableName = $c->getSubmitsTableName();
	
	foreach ($id as $idz) {
		
		$parametrizedQuery = "INSERT INTO `$tableName` (`submit_time`, `form_name`, `field_name`, `field_value`, `field_order`) VALUES (%s, %s, %s, %s, %s)";
		
		$wpdb->query($wpdb->prepare($parametrizedQuery,
									$idz,
									CF_FORMNAME,
									'processed',
									'ignored',
									'99'));
		
	}
	$count = count($id);
	$application = $count==1 ? '' : 's';
	
	echo('<div id="message" class="updated"><p>Ignored '.$count.' new member application'.$application.'.</p></div>');
}

function make_new_member($id) {
	global $wpdb;		
	$exp = new CFDBFormIterator();
	
	if (is_array($id)) {
		foreach ($id as $idz) {
			$atts['filter'][] = 'submit_time='.$idz;
		}
		$atts['filter'] = implode('||', $atts['filter']);
	}   
	else {
		$atts['filter'] = 'submit_time='.$id;
	}
	
	$exp->export(CF_FORMNAME, $atts);
	$data = array();
	while ($row = $exp->nextRow()) {
		$data[] = $row;
	}
	
	
	
	foreach($data as $member) {
		
		if ($u = get_user_by('email', $member['your-email'])) {
			echo('<div id="message" class="error"><p>A user named <a href="'.admin_url('user-edit.php?user_id='.$u->ID).'"><strong>'.$u->display_name . '</strong></a> already uses this email address.</p></div>');
		}
		else {	
			
			//$login = lowrez_make_username($member['first-name'], $member['last-name']);
			
			$user = array();
			$user['first_name'] = $member['first-name'];
			$user['last_name'] = $member['last-name'];
			$user['display_name'] = $member['first-name'] . ' ' . $member['last-name'];
			$user['user_email'] = $member['your-email'];
			//$user[''] = $member['mobile'];
			//$user[''] = $member['address'];
			//$user[''] = $member['suburb'];
			//$user[''] = $member['postcode'];
			//$user[''] = $member['dob-day'].$member['dob-mth'].$member['dob-year'];
			$user['role'] = 'member';
			$user['user_login'] = uniqid(); //$login;
			$user['user_pass'] = wp_generate_password( 8, false );
			//$user['user_pass'] = wp_hash_password($pwd);
			//$user[''] = $member[''];
			
			if ($u = wp_insert_user($user)) {
				
				wp_new_user_notification($u, $user['user_pass']);
				
				update_user_meta($u, 'mobile', format_mobile($member['mobile-phone'], 'clean'));
				update_user_meta($u, 'street', $member['address']);
				update_user_meta($u, 'suburb', $member['suburb']);
				update_user_meta($u, 'postcode', $member['postcode']);
				update_user_meta($u, 'date_joined', date('Y/m/d'));
				
				
				if (($m = $member['dob-mth']) && ($d = $member['dob-day'])) {
					
					$m = date('n', strtotime("1 $m 2000"));
					
					update_user_meta($u, 'date_of_birth', $m.'/'.$d);
				}
				update_user_meta($u, 'date_of_birth_y', $member['dob-year']);
				
				$u = new WP_User( $u );
				//$u->remove_role( 'subscriber' );
				//$u->set_role( 'contributor' );
				
				//kbs_new_to_temp_group($u->ID);
				
				$c = new CF7DBPlugin();
				$tableName = $c->getSubmitsTableName();
				
				$parametrizedQuery = "INSERT INTO `$tableName` (`submit_time`, `form_name`, `field_name`, `field_value`, `field_order`) VALUES (%s, %s, %s, %s, %s)";
				
				$wpdb->query($wpdb->prepare($parametrizedQuery,
											$id,
											CF_FORMNAME,
											'processed',
											'approved',
											'99'));
				
				$wpdb->query($wpdb->prepare($parametrizedQuery,
											$id,
											CF_FORMNAME,
											'new_user_id',
											$u->ID,
											'99'));
				
				$wpdb->query($wpdb->prepare($parametrizedQuery,
											$id,
											CF_FORMNAME,
											'processed_date',
											date('j M y'),
											'99'));
				
				//$url = add_query_arg(array('action'=>'approve', 'user'=>$u->ID, 'to_role'=>'member'), wp_nonce_url( "users.php", 'approve-user' ));
				
				echo '<div id="message" class="updated"><p>The new member application from <a href="'.admin_url('user-edit.php?user_id='.$u->ID).'"><strong>' . $u->display_name . '</strong></a> has been successfully processed and a user account created.</p></div>';
				
				
			}
			else {
				echo '<div id="message" class="error"><p>Cannot create user.</p></div>';
			}
			
		}
	}
}


function new_members_help( $contextual_help, $screen_id, $screen ) { 
	
	if ( in_array($screen->id, array('toplevel_page_new-members', 'admin_page_new-member-form') ) ) {
		
		$contextual_help = '<h2>New Member Applications</h2>
<p>Click on a name to view the full application.</p> 
<p>If you <strong>approve</strong> an application, a user account will be created and a welcome email will be sent to the applicant.</p>
<p>If you <strong>ignore</strong> an application, the application will be moved to the <strong>Ignored</strong> tab and the applicant will not be notified.</p>
';
		
	}
	return $contextual_help;
}
add_action( 'contextual_help', 'new_members_help', 10, 3 );


/************************** CREATE A PACKAGE CLASS *****************************
*******************************************************************************
* Create a new list table package that extends the core WP_List_Table class.
* WP_List_Table contains most of the framework for generating the table, but we
* need to define and override some methods so that our data can be displayed
* exactly the way we need it to be.
*
* To display this example on a page, you will first need to instantiate the class,
* then call $yourInstance->prepare_items() to handle any data manipulation, then
* finally call $yourInstance->display() to render the table to the page.
*
* Our theme for this list table is going to be movies.
*/
class New_Members_Table extends WP_List_Table {
	
	/** ************************************************************************
	* REQUIRED. Set up a constructor that references the parent constructor. We
	* use the parent reference to set some default configs.
	***************************************************************************/
	function __construct(){
		global $status, $page;
		
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'new-member',     //singular name of the listed records
			'plural'    => 'new-members',    //plural name of the listed records
			'ajax'      => false        //does this table support ajax?
		) );
		
	}
	
	
	/** ************************************************************************
	* Recommended. This method is called when the parent class can't find a method
	* specifically build for a given column. Generally, it's recommended to include
	* one method for each column you want to render, keeping your package class
	* neat and organized. For example, if the class needs to process a column
	* named 'title', it would first see if a method named $this->column_title()
	* exists - if it does, that method will be used. If it doesn't, this one will
	* be used. Generally, you should try to use custom column methods as much as
	* possible.
	*
	* Since we have defined a column_title() method later on, this method doesn't
	* need to concern itself with any column with a name of 'title'. Instead, it
	* needs to handle everything else.
	*
	* For more detailed insight into how columns are handled, take a look at
	* WP_List_Table::single_row_columns()
	*
	* @param array $item A singular item (one full row's worth of data)
	* @param array $column_name The name/slug of the column to be processed
	* @return string Text or HTML to be placed inside the column <td>
	**************************************************************************/
	function column_default($item, $column_name){
		switch($column_name){
			case 'date':
			//case 'first-name':
			//case 'last-name':
			case 'how-did-you-hear':
			case 'voice-part':
			return $item[$column_name];
			break;
			case 'musical':
			if (!$item['read-music']) {
				$item['read-music'] = 'Not answered';
			}
			return sprintf('Reads? %1$s<br>%2$s', $item['read-music'], $item['sing-experience']);
			break;
			case 'date-of-birth':
			$day = $item['dob-day'];
			$mth = $item['dob-mth'];
			$mth = $mth ? ' '.date('M',strtotime($mth)) : '';
			$year = $item['dob-year'];
			$year = $mth && $year ? ' '.substr($year,2) : '';
			
			return sprintf('%1$s%2$s%3$s', $day, $mth, $year);
			break;
			case 'comments':
			$comments = $item['comments'];
			if ($comments) {
				return '<div class="limitcomments">'.$comments.'</div>';
				//return '<input type="button" value="Show" class="button showcomments"><span class="hidecomments">'.$comments.'</span>';
			}
			else {
				return false;
			}
			break;
			case 'processed-date':
			return $item['processed_date'].'<br>'.ucfirst($item['processed']);
			break;
			//case 'outcome':
			//return ucfirst($item['processed']);
			//break;
			case 'contact-details':
			return $this->column_default($item, 'your-email').'<br>'.$this->column_default($item, 'mobile');
			break;
			case 'your-email':
			//$result = preg_replace('/(@|\.|\+)/', '\1<wbr>', $item['your-email']);/*im*/
			//$result = str_replace('@', '@<br>', $item['your-email']);/*im*/
			$result = $item['your-email'];/*im*/
			return sprintf('<a href="mailto:%1$s">%2$s</a>', $item['your-email'], $result);
			break;
			case 'mobile':
			if ($item['mobile-phone']) {
				return format_mobile($item['mobile-phone'], 'display');
			}
			break;
			default:
			return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
		
	}
	
	
	/** ************************************************************************
	* Recommended. This is a custom column method and is responsible for what
	* is rendered in any column with a name/slug of 'title'. Every time the class
	* needs to render a column, it first looks for a method named
	* column_{$column_title} - if it exists, that method is run. If it doesn't
	* exist, column_default() is called instead.
	*
	* This example also illustrates how to implement rollover actions. Actions
	* should be an associative array formatted as 'slug'=>'link html' - and you
	* will need to generate the URLs yourself. You could even ensure the links
	*
	*
	* @see WP_List_Table::::single_row_columns()
	* @param array $item A singular item (one full row's worth of data)
	* @return string Text to be placed inside the column <td> (movie title only)
	**************************************************************************/
	function column_name($item) {
		
		//Build row actions
		$actions = array(
			//'edit'      => sprintf('<a href="?page=%s&action=%s&new-member=%s">Edit</a>',$_REQUEST['page'],'edit',$item['submit_time']),
			'make-member'      => sprintf('<a href="?page=%s&action=%s&new-member=%s">Approve</a>',$_REQUEST['page'],'make-member',$item['submit_time']),
			'delete'      => sprintf('<a href="?page=%s&action=%s&new-member=%s">Ignore</a>',$_REQUEST['page'],'ignore-member',$item['submit_time'])//,
			//'delete'    => sprintf('<a href="?page=%s&action=%s&new-member=%s">Delete</a>',$_REQUEST['page'],'delete',$item['submit_time']),
		);
		
		if ($item['processed']=='ignored') {
			$actions['delete'] = sprintf('<a href="?page=%s&action=%s&new-member=%s">Delete</a>',$_REQUEST['page'],'delete-member',$item['submit_time']);
		}
		if ($item['processed']=='approved') {
			unset($actions['make-member']);
		}
		if ($item['processed']) {
			$actions['unprocess'] = sprintf('<a href="?page=%s&action=%s&new-member=%s">Unprocess</a>',$_REQUEST['page'],'unprocess-member',$item['submit_time']);
		}
		
		
		$name = trim($item['first-name'] .' '. $item['last-name']);
		
		if (($username = $item['new_user_id']) && (get_userdata($item['new_user_id'])) ) {
			$name = '<a href="' . admin_url('user-edit.php?user_id='.$username.'&tab=application') .'"><strong>'.$name.'</strong></a>';
		}
		else {
			$name = '<a href="' . admin_url('admin.php?page=new-member-form&new-member='.$item['submit_time']) .'"><strong>'.$name.'</strong></a>';
		}
		
		//Return the title contents
		return sprintf('%1$s %2$s',
					   $name,
					   $this->row_actions($actions)
					  );
	}
	
	function column_fulladdress($item) {
		$address = $item['address'] ? $item['address'].'<br>' : '';
		return trim(sprintf('%1$s %2$s %3$s',
							/*$1%s*/ $item['address'],
							/*$2%s*/ strtoupper($item['suburb']),
							/*$3%s*/ $item['postcode']
						   ));
	}
	
	/*function column_email($item) {
	if ($item['your-email']) {
	return sprintf('<a href="mailto:%1$s">%1$s</a>', $item['your-email']);
	}
	}*/
	
	/** ************************************************************************
	* REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	* is given special treatment when columns are processed. It ALWAYS needs to
	* have its own method.
	*
	* @see WP_List_Table::::single_row_columns()
	* @param array $item A singular item (one full row's worth of data)
	* @return string Text to be placed inside the column <td> (movie title only)
	**************************************************************************/
	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/ $item['submit_time']                //The value of the checkbox should be the record's id
		);
	}
	
	function column_date($item) {
		return date('j M y', strtotime($item['Submitted']));
	}
	
	
	/** ************************************************************************
	* REQUIRED! This method dictates the table's columns and titles. This should
	* return an array where the key is the column slug (and class) and the value
	* is the column's title text. If you need a checkbox for bulk actions, refer
	* to the $columns array below.
	*
	* The 'cb' column is treated differently than the rest. If including a checkbox
	* column in your table you must create a column_cb() method. If you don't need
	* bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	*
	* @see WP_List_Table::::single_row_columns()
	* @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	**************************************************************************/
	function get_columns(){
		$columns = array(
			'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
			'name' => 'Name',
			//'last-name' => 'Last Name',
			//'printout' => 'printout',
			'date' => 'Applied',
			'processed-date' => 'Processed',
			//'outcome' => 'Outcome',
			//'your-email' => 'Email',
			//'mobile' => 'Mobile',
			'contact-details' => 'Contact',
			//'fulladdress' => 'Address',
			//'date-of-birth' => 'DOB',
			'voice-part' => 'Voice Part',
			'musical' => 'Experience',
			//'how-did-you-hear' => 'Referral',
			//'comments' => 'Comments'
		);
		return $columns;
	}
	
	/** ************************************************************************
	* Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	* you will need to register it here. This should return an array where the
	* key is the column that needs to be sortable, and the value is db column to
	* sort by. Often, the key and value will be the same, but this is not always
	* the case (as the value is a column name from the database, not the list table).
	*
	* This method merely defines which columns should be sortable and makes them
	* clickable - it does not handle the actual sorting. You still need to detect
	* the ORDERBY and ORDER querystring variables within prepare_items() and sort
	* your data accordingly (usually by modifying your query).
	*
	* @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
	**************************************************************************/
	function get_sortable_columns() {
		$sortable_columns = array(
			'date'     => array('Submitted',true),     //true means its already sorted
			'name'  => array('last-name,first-name',false),
			'voice-part'    => array('voice-part',false)
		);
		return $sortable_columns;
	}
	
	
	/** ************************************************************************
	* Optional. If you need to include bulk actions in your list table, this is
	* the place to define them. Bulk actions are an associative array in the format
	* 'slug'=>'Visible Title'
	*
	* If this method returns an empty value, no bulk action will be rendered. If
	* you specify any bulk actions, the bulk actions box will be rendered with
	* the table automatically on display().
	*
	* Also note that list tables are not automatically wrapped in <form> elements,
	* so you will need to create those manually in order for bulk actions to function.
	*
	* @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
	**************************************************************************/
	function get_bulk_actions() {
		$actions = array(
			//'delete'    => 'Delete',
			'make-member'    => 'Approve Member',
			'ignore-member'    => 'Ignore Member'
		);
		return $actions;
	}
	
	
	/** ************************************************************************
	* Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	* For this example package, we will handle it in the class to keep things
	* clean and organized.
	*
	* @see $this->prepare_items()
	**************************************************************************/
	function process_bulk_action() {
		
		if ('make-member' === $this->current_action()) {
			
			if ($ms = $_GET['new-member']) {
				unprocess_member($ms, true);
				make_new_member($ms);
			} else {
				wp_die('No member to edit!');
				//FIXME
			}
			
		} elseif ('ignore-member' === $this->current_action()) {
			
			if ($ms = $_GET['new-member']) {
				unprocess_member($ms, true);
				ignore_member($ms);
			} else {
				wp_die('No member to edit!');
				//FIXME
			}
		} elseif ('delete-member' === $this->current_action()) {
			
			if ($ms = $_GET['new-member']) {
				delete_member($ms);
			} else {
				wp_die('No member to delete!');
				//FIXME
			}
		} elseif ('unprocess-member' === $this->current_action()) {
			
			if ($ms = $_GET['new-member']) {
				unprocess_member($ms);
			} else {
				wp_die('No member to delete!');
				//FIXME
			}
			
		}
		
	}
	/** ************************************************************************
	* REQUIRED! This is where you prepare your data for display. This method will
	* usually be used to query the database, sort and filter the data, and generally
	* get it ready to be displayed. At a minimum, we should set $this->items and
	* $this->set_pagination_args(), although the following properties and methods
	* are frequently interacted with here...
	*
	* @uses $this->_column_headers
	* @uses $this->items
	* @uses $this->get_columns()
	* @uses $this->get_sortable_columns()
	* @uses $this->get_pagenum()
	* @uses $this->set_pagination_args()
	**************************************************************************/
	function prepare_items() {
		global $processed;
		/**
		* First, lets decide how many records per page to show
		*/
		$per_page = 15;
		
		
		/**
		* REQUIRED. Now we need to define our column headers. This includes a complete
		* array of columns to be displayed (slugs & titles), a list of columns
		* to keep hidden, and a list of columns that are sortable. Each of these
		* can be defined in another method (as we've done here) before being
		* used to build the value for our _column_headers property.
		*/
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		
		$processed = isset( $_REQUEST['processed'] ) ? $_REQUEST['processed'] : 'unprocessed';
		
		/**
		* REQUIRED. Finally, we build an array to be used by the class for column
		* headers. The $this->_column_headers property takes an array which contains
		* 3 other arrays. One for all columns, one for hidden columns, and one
		* for sortable columns.
		*/
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		
		/**
		* Optional. You can handle your bulk actions however you see fit. In this
		* case, we'll handle them within our package just to keep things clean.
		*/
		$this->process_bulk_action();
		
		
		
		
		
		$exp = new CFDBFormIterator();
		
		$orderby = explode(',', (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'date,name');
		$order = explode(',', (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc,asc');
		
		$end = end($order);
		$order = array_pad($order, count($orderby), $end);
		$sortable = $this->get_sortable_columns();
		
		for ($i=0; $i < count($orderby); $i++) {
			$orderby[$i] = $sortable[$orderby[$i]][0].' '.$order[$i];
		}
		
		$orderby = implode(',', $orderby);
		
		//print $orderby;
		
		$atts = array('orderby' => $orderby);
		
		if ($_REQUEST['s']) {
			$atts['search'] = $_REQUEST['s'];
		}
		else {
			if ($processed=='all') {
				//do nothing
			}
			elseif ($processed == 'unprocessed') {
				$atts['filter'] = 'processed!=approved&&processed!=ignored';
			}
			else {
				$atts['filter'] = 'processed='.$processed;
			}
		}
		
		
		$exp->export(CF_FORMNAME, $atts);
		$data = array();
		while ($row = $exp->nextRow()) {
			$data[] = $row;
		}
		
		
		/**
		* This checks for sorting input and sorts the data in our array accordingly.
		*
		* In a real-world situation involving a database, you would probably want
		* to handle sorting by passing the 'orderby' and 'order' values directly
		* to a custom query. The returned data will be pre-sorted, and this array
		* sorting technique would be unnecessary.
		*/
		/*function usort_reorder($a,$b){
		$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'date'; //If no sort, default to date
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to desc
		
		$result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
		return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
		}
		usort($data, 'usort_reorder');*/
		
		
		/**
		* REQUIRED for pagination. Let's figure out what page the user is currently
		* looking at. We'll need this later, so you should always include it in
		* your own package classes.
		*/
		$current_page = $this->get_pagenum();
		
		/**
		* REQUIRED for pagination. Let's check how many items are in our data array.
		* In real-world use, this would be the total number of items in your database,
		* without filtering. We'll need this later, so you should always include it
		* in your own package classes.
		*/
		$total_items = count($data);
		
		
		/**
		* The WP_List_Table class does not handle pagination for us, so we need
		* to ensure that the data is trimmed to only the current page. We can use
		* array_slice() to
		*/
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
		
		
		
		/**
		* REQUIRED. Now we can add our *sorted* data to the items property, where
		* it can be used by the rest of the class.
		*/
		$this->items = $data;
		
		
		/**
		* REQUIRED. We also have to register our pagination options & calculations.
		*/
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
		) );
	}
	
	function get_views() {
		
		global $processed;
		
		$url = 'admin.php?page=new-members';
		$total_apps = count_member_apps(); 
		
		$processed_types = array('unprocessed' => 'Unprocessed',
								 'approved' => 'Approved',
								 'ignored' => 'Ignored');
		
		$processed_links = array();
		
		
		foreach ( $processed_types as $this_processed => $name ) {
			
			$class = '';
			
			if ( $this_processed == $processed ) {
				$class = ' class="current"';
			}
			
			$name = sprintf( __('%1$s <span class="count">(%2$s)</span>'), $name, number_format_i18n( count_member_apps($this_processed) ) );//$avail_roles[$this_role]
			$processed_links[$this_processed] = "<a href='" . esc_url( add_query_arg( 'processed', $this_processed, $url ) ) . "'$class>$name</a>";
		}
		
		$class = $processed=='all' ? ' class="current"' : '';
		$processed_links['all'] = "<a href='" . esc_url( add_query_arg( 'processed', 'all', $url ) ) . "'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_apps, 'new-members' ), number_format_i18n( $total_apps ) ) . '</a>';
		
		return $processed_links;
	}
	
	
	
	
}

function count_member_apps($processed = FALSE) {
	
	$countShortCode = new CFDBShortcodeCount();
	$attributes = array('form' => CF_FORMNAME);
	
	if ($processed) {
		if ($processed=='all') {
			//do nothing
		}
		elseif ($processed=='unprocessed') {
			$attributes['filter'] = 'processed!=approved&&processed!=ignored';
		}
		else {
			$attributes['filter'] = 'processed='.$processed;
		}
	}
	
	return ($countShortCode->handleShortCode($attributes));
	//global $wpdb;
	//$counted = $wpdb->get_row( "SELECT Count(`submit_time`) AS `count` FROM (SELECT DISTINCT `submit_time` FROM $wpdb->cf7dbplugin_submits) AS `submits`;" );
}


/** ************************ REGISTER THE TEST PAGE ****************************
*******************************************************************************
* Now we just need to define an admin page. For this example, we'll add a top-level
* menu item to the bottom of the admin menus.
*/

function newmem_add_menu_items(){
	if (current_user_can('promote_users')) {
		$count = count_member_apps('unprocessed');//true
		$counter = '<span class="update-plugins count-'.$count.'"><span class="plugin-count">'.$count.'</span></span>';
		//wp_die('yeppit');
		add_menu_page('New Member Applications', 'Applications'.$counter, 'promote_users', 'new-members', 'newmem_render', plugin_dir_url( __FILE__ ).'user-add32.png', '71.9');
		add_submenu_page(NULL, 'Application', 'Application', 'promote_users', 'new-member-form', 'newmem_form_render');
	}
}

add_action('admin_menu', 'newmem_add_menu_items');

/***************************** APP ********************************/

function newmem_form_render() {
?>
<div class="wrap">
	
	<div id="icon-edit" class="icon32 icon32-new-members">
		<br/>
	</div>
	<h2>New Member Applications <a href="?page=new-members" class="add-new-h2">Back to Applications</a></h2>
	<h3><?php _e('Application Form'); ?></h3>
	
	<?php
							   $app = $_GET['new-member'];
							   $exp = new CFDBFormIterator();
							   $atts = array(
								   'filter' => 'submit_time='.$app
							   );
							   
							   $exp->export(CF_FORMNAME, $atts);
							   if ($data = $exp->nextRow()) :
	?>
	
	<table class="form-table">
		<tr>
			<th>Application Date</th>
			<td><?php $time = strtotime($data['Submitted']);
							   echo date('j\&\n\b\s\p\;F Y, g:i\&\n\b\s\p\;a', $time);
							   echo ' <span class="description">('.time_passed($time) . ')</span>';
				?></td>
		</tr>
		<tr>
			<th>First Name</th>
			<td><?php echo $data['first-name']; ?></td>
		</tr>
		<tr>
			<th>Last Name</th>
			<td><?php echo $data['last-name']; ?></td>
		</tr>
		<tr>
			<th>Date of Birth</th>
			<td><?php 
							   
							   $day = $data['dob-day'];
							   $mth = $data['dob-mth'];
							   $mth = $mth ? ' '.date('F',strtotime($mth)) : '';
							   $year = $data['dob-year'];
							   $year = $mth && $year ? ' '.$year : '';
							   
							   echo sprintf('%1$s%2$s%3$s', $day, $mth, $year);
							   
				?></td>
		</tr>
		<tr>
			<th>Email</th>
			<td><?php echo sprintf('<a href="mailto:%1$s">%1$s</a>', trim($data['your-email'])); ?></td>
		</tr>
		<tr>
			<th>Mobile Phone</th>
			<td><?php echo format_mobile($data['mobile-phone'], 'display'); ?></td>
		</tr>
		<tr>
			<th>Address</th>
			<td><?php echo $data['address'] . '<br>' . $data['suburb'] . '&nbsp;' . $data['postcode']; ?></td>
		</tr>
		<tr>
			<th>Reads Music</th>
			<td><?php echo $data['read-music']; ?></td>
		</tr>
		<tr>
			<th>Singing Experience</th>
			<td><?php echo $data['sing-experience']; ?></td>
		</tr>
		<tr>
			<th>Voice Type</th>
			<td><?php echo $data['voice-part']; ?></td>
		</tr>
		<tr>
			<th>Referral</th>
			<td><?php echo $data['how-did-you-hear']; ?></td>
		</tr>
		<tr>
			<th>Comments</th>
			<td><?php echo $data['comments']; ?></td>
		</tr>
	</table>
	<p class="submit">
		<a href="?page=new-members&action=make-member&new-member=<?php echo $app; ?>" class="button button-primary">Approve Member</a>
		<a href="?page=new-members&action=ignore-member&new-member=<?php echo $app; ?>" class="button">Ignore Member</a>
	</p>
	
	<?php else: ?>
	
	<p>No application on record.<p>
	
	<?php endif; ?>
	</div>
<?
							  }

/***************************** LIST ********************************/
function newmem_render() {
	
	//Create an instance of our package class...
	$testListTable = new New_Members_Table();
	//Fetch, prepare, sort, and filter our data...
	$testListTable->prepare_items();
	
	
	
?>
<style type="text/css">
	.fixed .column-comments {
		width: 140px !important;
	}
	.fixed .column-date-of-birth,
	.fixed .column-processed-date,
	.fixed .column-date {
		width: 80px;
	}
	
	.limitcomments {
		max-height:100px;
		overflow-y:auto;
	}
</style>
<div class="wrap">
	
	<div id="icon-edit" class="icon32 icon32-new-members">
		<br/>
	</div>
	<h2>New Member Applications</h2>
	
	<!--<div style="background:#ECECEC;border:1px solid #CCC;padding:0
10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
<p>This page demonstrates the use of the <tt><a
href="http://codex.wordpress.org/Class_Reference/WP_List_Table" target="_blank"
style="text-decoration:none;">WP_List_Table</a></tt> class in plugins.</p>
<p>For a detailed explanation of using the <tt><a
href="http://codex.wordpress.org/Class_Reference/WP_List_Table" target="_blank"
style="text-decoration:none;">WP_List_Table</a></tt>
class in your own plugins, you can view this file <a
href="/wp-admin/plugin-editor.php?plugin=table-test/table-test.php"
style="text-decoration:none;">in the Plugin Editor</a> or simply open <tt
style="color:gray;"><?php echo __FILE__ ?></tt> in the PHP editor of your
choice.</p>
<p>Additional class details are available on the <a
href="http://codex.wordpress.org/Class_Reference/WP_List_Table" target="_blank"
style="text-decoration:none;">WordPress Codex</a>.</p>
</div>-->
	<?php $testListTable->views(); ?>
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to
use features like bulk actions -->
	<form method="post">
		<input type="hidden" name="page" value="new-members" />
		<?php $testListTable->search_box('Search', 'search_id'); ?>
	</form>
	<form id="new-members-filter" method="get">
		<!-- For plugins, we also need to ensure that the form posts back to our current
page -->
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<!-- Now we can render the completed list table -->
		<?php $testListTable->display()
		?>
	</form>
	
</div>
<?php
}
