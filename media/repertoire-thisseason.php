<?php



if (isset($_REQUEST['rescan'])) {
	$thisseason = new ThisSeason(false);
	$thisseason->scandir();
}
if (isset($_REQUEST['this-season-songs']) && isset($_REQUEST['save-songs'])) {
	$thisseason = new ThisSeason(false);
	$thisseason->savesongs();
}

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

function ignored_files($file) {
	
	$ignored = array('.','..');
	$allowed_ext = array(
		'pdf', 
		'mp3',
		'mp4'
	);
	
	if ( !in_array($file, $ignored) ) {
		//if ( in_array(pathinfo($file, PATHINFO_EXTENSION), $allowed_ext) ) {
		return true;
		//}
	}
	
}

class ThisSeason {
	
	private $_url = 'http://files.lowrez.com.au/media/';
	private $_path = '/home/lowrez/files/';
	private $_dir = 'Repertoire/This Season/';
	private $_files;
	private $_repertoires;
	
	function __construct($init = true) {
		if ($init) add_action('admin_menu', array(&$this, 'admin_menu'));
	}
	
	function __get($prop) {
		
		switch ($prop) {
			
			case 'url':
			
			global $current_user; get_currentuserinfo();			
			return $this->_url . $current_user->user_nicename . '/' . $this->_dir;
			
			break;
			/*---*/
			case 'path';
			
			return $this->_path . $this->_dir;
			
			break;	
			/*---*/
			case 'repertoires':
			
			if (!isset($this->_repertoires)) {
				
				$args = array(
					'post_type' => 'repertoire',
					'orderby'   => 'name',
					'order'     => 'ASC',
					'nopaging'     => true,
					'update_post_term_cache' => false
				);
				$query = new WP_Query( $args );
				
				$posts = array();
				
				foreach ($query->posts as $post) {
					$posts[$post->ID] = $post->post_title;
				}
				
				$this->_repertoires = $posts;
				
			}
			
			return $this->_repertoires;
			
			break;	
			/*---*/
			case 'match_repertoires':
			
			//return array_flip(array_map('sanitize_title', $this->repertoires));
			return array_map('sanitize_title', $this->repertoires);
			
			break;
			/*---*/
			
			case 'voiceparts':
			
			return array(
				'tenor-1' => 'Tenor 1',
				'tenor-2' => 'Tenor 2',
				'baritone' => 'Baritone',
				'bass' => 'Bass',
				'countertenor' => 'Countertenor',
				'solo' => 'Solo',
				'guitar' => 'Guitar',
				'bass-guitar' => 'Bass Guitar',
				'drums' => 'Drums',
				'other' => 'Other',
			);
			
			break;
			/*---*/
			case 'media_types':
			
			return array(
				'sheet' => 'Sheet Music',
				'singalong' => 'Singalong Track',
				'synth' => 'Synth Track',
				'backing' => 'Backing Track',
				'lead-sheet' => 'Lead Sheet',
				'song' => 'Original Track',
				'video' => 'Original Video'
			);
			
			break;
			/*---*/
			default:
			
			return $this->$prop;
			
		}
		
	}
	
	/*function get_all_files($file, &$found_files) {
	
	$found_files = scandir($path);
	$found_files = array_filter($found_files, 'ignored_files');
	
	foreach ($found_files as $file) {
	
	if (is_file($this->path.$file)) {
	
	$found_files[] = array(
	'name' => $file
	'path' => ''
	);
	
	}
	elseif (is_dir($this->path.$file)) {
	
	}
	
	}
	
	}*/
	
	function getFilesFromDir($dir) { 
		
		$files = array(); 
		if ($handle = opendir($dir)) { 
			while (false !== ($file = readdir($handle))) { 
				if ($file != "." && $file != "..") { 
					if(is_dir($dir.''.$file)) { 
						$dir2 = $dir.''.$file; 
						$files[] = $this->getFilesFromDir($dir2); 
					} 
					else { 
						$files[] = $dir.'{}'.$file; 
					} 
				} 
			} 
			closedir($handle); 
		} 
		
		return $this->array_flat($files); 
	} 
	
	function array_flat($array) { 
		
		foreach($array as $a) { 
			if(is_array($a)) { 
				$tmp = array_merge($tmp, $this->array_flat($a)); 
			} 
			else { 
				$tmp[] = $a; 
			} 
		} 
		
		return $tmp; 
	} 
	
	
	
	
	function scandir() {
		
		// Usage 
		$foo = $this->getFilesFromDir($this->path); 
		
		print_pre($foo); 
		die();
		
		
		
		$new_files = scandir($this->path);
		$found_files = array();
		
		foreach ($new_files as $file) {
			
			if (is_file($this->path.$file)) {
				
				$found_files[] = array(
					'name' => $file,
					'path' => ''
				);
				
			}
			elseif (is_dir($this->path.$file)) {
				
			}
			
		}
		
		$new_files = array_filter($found_files, 'ignored_files');
		
		
		global $wpdb;
		$query = "SELECT id, file_path, file_name FROM {$wpdb->prefix}thisseason";
		$query = $wpdb->get_results($query);
		
		$saved_files = array();
		
		foreach ($query as $file) {
			$saved_files[$file->id] = $file->file_name;
		}
		
		$new_files = array_diff($new_files, $saved_files);
		
		foreach ($new_files as $file) {
			
			$wpdb->insert( 
				$wpdb->prefix.'thisseason', 
				array(
					'file_name' => $file['name'],
					'file_name' => $file['path']
				)
			);
			
		}
		
		//if ( true || ! headers_sent() ) {
		$this->reload();
		//}
		
	}
	
	function reload() {
		$base = remove_query_arg('rescan');
		
		$base = 'http://'.$_SERVER['HTTP_HOST'].$base;
		header('Location: '. $base);
		exit;
	}
	
	function savesongs() {
		
		$songs = $_REQUEST['this-season-songs'];
		global $wpdb;
		
		$i = 0;
		
		foreach ($songs as $id => $song) {
			
			
			if (!isset($song['hidden'])) {
				$song['hidden'] = null;
			}
			/*if (!isset($song['hidden'])) {
			$song['hidden'] = null;
			}*/
			
			$i += $wpdb->update( 
				$wpdb->prefix.'thisseason', 
				$song,
				array('id' => $id)
			);
			
		}
		
		//wp_die($i.' rows affected.');
		
		$this->reload();
		
	}
	
	
	function admin_menu() {
		add_menu_page('This Season', 'This Season', 'publish_posts', 'this-season', array(&$this, 'admin_submenu_thisseason'), plugin_dir_url(__FILE__) . 'music16.png', 30);
		add_submenu_page('this-season', 'This Season', 'This Season', 'publish_posts', 'this-season', array(&$this, 'admin_submenu_thisseason'));
		//edit.php?post_type=repertoire
	}
	
	function admin_submenu_thisseason() {
?>
<div class="wrap">
	<div id="icon-edit" class="icon32 icon32-this-season">
		<br/>
	</div>
	<h2>This Season <a href="?page=this-season&rescan=1" class="add-new-h2">Rescan Directory</a></h2>
	<?php
		
		$thisseason_table = new ThisSeason_Table($this);
		$thisseason_table->prepare_items();
		$thisseason_table->css();
		echo '<form id="thisseason-form" method="post"><input type="hidden" name="page" value="' . $_REQUEST['page'] . '" />';
		$thisseason_table->display();
		
		echo '</form>';
		
		echo sprintf('<p>Browsing server directory: <code>%s</code>.</p>', $this->path);
		echo sprintf('<p>Files are downloadable from: <code>%s</code>.</p>', $this->url);
		
		print_pre($_REQUEST);
		
	?>
</div>
<?php
	}
	
}

$thisseason = new ThisSeason();

//Our class extends the WP_List_Table class, so we need to make sure that it's there


class ThisSeason_Table extends WP_List_Table {
	
	public $season;
	
	/**
	* Constructor, we override the parent to pass our own arguments
	* We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	*/
	function __construct(&$season) {
		$this->season = $season;
		
		parent::__construct( array(
			'singular'=> 'this-season-song', //Singular label
			'plural' => 'this-season-songs', //plural label, also this well be one of the table css class
			'ajax'	=> false //We won't support Ajax for this table
		) );
	}
	
	function get_bulk_actions() {
		return array();
		$actions = array(
			'include'    => 'Include',
			'exclude'    => 'Exclude'
		);
		return $actions;
	}
	
	
	/**
	* Add extra markup in the toolbars before or after the list
	* @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
	*/
	function extra_tablenav( $which ) {
		
		//$this->pseudo_bulk_actions($which);
		
		echo '<div class="alignleft actions">';
		
		submit_button('Save Changes', 'primary', 'save-songs['.$which.']', false);
		
		echo '</div>';
		
		if ( $which == "top" ){
			//The code that goes before the table is here
		}
		if ( $which == "bottom" ){
			//The code that goes after the table is there
			
			//echo '<div class="alignleft actions"><input type="submit" class="button button-primary" value="Save Changes" /></div>';//<p class="submit"></p>
			
			
			
		}
	}
	
	function pseudo_bulk_actions($two) {
		
		//$two = $which == 'top' ? '' : '2';
		
		$actions = array('repertoire' => array('title'=>'Assign repertoire...', 'actions'=>$this->season->repertoires),
						 'media_types' => array('title'=>'Assign type...', 'actions'=>$this->season->media_types),
						 'voicepart' => array('title'=>'Assign part...', 'actions'=>$this->season->voiceparts),
						);
		
		foreach ($actions as $key => $action) {
			
			echo '<div class="alignleft actions">';
			echo "<select name='assign_{$key}[{$two}]'>\n";
			echo "<option value='-1' selected='selected'>" . $action['title'] . "</option>\n";
			foreach ( $action['actions'] as $name => $title )
				echo "\t<option value='$name'>$title</option>\n";
			echo "</select>\n";
			
			submit_button( __( 'Assign' ), 'button-secondary action', false, false, array( 'id' => "doassign_{$key}_{$two}" ) );
			echo "\n";
			echo '</div>';
			
		}
		
	}
	
	/**
	* Define the columns that are going to be used in the table
	* @return array $columns, the array of columns to use with the table
	*/
	function get_columns() {
		return $columns= array(
			'cb'        => '<input type="checkbox" />',
			//'col_link_id'=>__('ID'),
			//'col_link_name'=>__('Path'),
			'col_file_name'=>__('File'),
			'col_repertoire'=>__('Repertoire'),
			'col_media_type'=>__('Type'),
			'col_voicepart'=>__('Part'),
			//'col_link_descr'=>__('Description'),
			//'col_file_date_mod'=>__('Modified'),
			'col_hidden'=>__('Exclude')
		);
	}
	
	function column_cb( $item ){
		return sprintf(
			'<input type="checkbox" name="selected_ids[]" value="%2$s" />',
			$this->_args['singular'],
			$item->id
		);
	}
	
	/**
	* Decide which columns to activate the sorting functionality on
	* @return array $sortable, the array of columns that can be sorted by the user
	*/
	public function get_sortable_columns() {
		return $sortable = array(
			'col_file_name'=>array('file_name'),
			//'col_repertoire'=>array('repertoire_title'),
			'col_media_type'=>array('media_type'),
			'col_voicepart'=>array('voicepart'),
			'col_hidden'=>array('hidden'),
		);
	}
	
	/**
	* Prepare the table with different parameters, pagination, columns and table elements
	*/
	function prepare_items() {
		
		
		
		global $wpdb, $_wp_column_headers;
		$screen = get_current_screen();
		
		/* -- Preparing your query -- */
		$query = "SELECT * FROM {$wpdb->prefix}thisseason";
		
		/* -- Ordering parameters -- */
		//Parameters that are going to be used to order the result
		$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
		$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
		if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
		
		/* -- Pagination parameters -- */
		//Number of elements in your table?
		$totalitems = $wpdb->query($query); //return the total number of affected rows
		//How many to display per page?
		$perpage = 25;
		//Which page is this?
		$paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
		//How many pages do we have in total?
		$totalpages = ceil($totalitems/$perpage);
		//adjust the query to take pagination into account
		if(!empty($paged) && !empty($perpage)){
			$offset=($paged-1)*$perpage;
			$query.=' LIMIT '.(int)$offset.','.(int)$perpage;
		}
		
		/* -- Register the pagination -- */
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $perpage,
		) );
		//The pagination links are automatically built according to those parameters
		
		/* — Register the Columns — */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		/* -- Fetch the items -- */
		$this->items = $wpdb->get_results($query);
		
		$repertoires = $this->season->repertoires;
		
		foreach ($this->items as &$item) {
			$item->repertoire_title = $repertoires[$item->repertoire];
		}
		
		//print_pre($query);
		//print_pre($this->items);
		//die();
		//$this->items = $new_files;
	}
	
	function compare_title($item) {
		return str_replace('-', ' ', sanitize_title(pathinfo($item->file_name, PATHINFO_FILENAME)));
	}
	
	
	function column_col_file_name($item) {
		return sprintf('<a href="%s" target="_blank">%s</a>', $this->season->url.$item->file_name, $item->file_name);
	}
	
	function column_col_repertoire($item) {
		
		$repertoires = $this->season->repertoires;
		$matches = $this->season->match_repertoires;
		
		if (empty($item->repertoire)) {
			$compare_title = $this->compare_title($item);
			
			foreach ($matches as $repertoire => $match) {
				
				if (preg_match('/\b'.str_replace('-', ' ', $match).'\b/i', $compare_title)) {
					$item->repertoire = $repertoire;
					$guessed = true;
					break;
				}
				
			}
			
		}
		
		return $this->_select($repertoires, $item, 'repertoire', $guessed);
	}
	
	function column_col_media_type($item) {
		
		if (empty($item->media_type)) {
			
			$compare_title = $this->compare_title($item);
			
			switch (pathinfo($item->file_name, PATHINFO_EXTENSION)) {
				case 'pdf':
				$item->media_type = 'sheet';
				$guessed = true;
				break;
				case 'mp3':
				
				$matches = array(
					'singalong' => 'singalong',
					'synth' => 'syn(th)?'
				);
				
				foreach ($matches as $media_type => $match) {
					
					if (preg_match('/\b'.$match.'\b/i', $compare_title )) {
						$item->media_type = $media_type;
						$guessed = true;
						break;
					}
					
				}
				break;
				case 'mp4':
				case 'webm':
				
				$matches = array(
					'concert' => 'concert',
					'visuals' => 'visuals?'
				);
				
				foreach ($matches as $media_type => $match) {
					
					if (preg_match('/\b'.$match.'\b/i', $compare_title )) {
						$item->media_type = $media_type;
						$guessed = true;
						break;
					}
					
				}
				break;
			}
		}
		
		return $this->_select($this->season->media_types, $item, 'media_type', $guessed);
		
	}
	
	function column_col_voicepart($item) {
		
		if (empty($item->voicepart)) {
			
			$compare_title = $this->compare_title($item);
			
			$matches = array(
				'tenor-1' => 't(en(or)?)?(\s*|-|_)1',
				'tenor-2' => 't(en(or)?)?(\s*|-|_)2',
				'baritone' => 'bar(i|itone)?',
				'bass' => 'b(ass)?',
				'countertenor' => '(counterten(or)?|ct)',
				'solo' => 'solo?'
				/*'bass guitar' => '',
				'guitar' => '',
				'drums' => '',*/
			);
			
			foreach ($matches as $voicepart => $match) {
				
				if (preg_match('/\b'.$match.'\b/i', $compare_title )) {
					$item->voicepart = $voicepart;
					$guessed = true;
					break;
				}
				
			}
		}
		
		return $this->_select($this->season->voiceparts, $item, 'voicepart', $guessed);
		
	}
	
	function column_col_hidden( $item ){
		return sprintf(
			'<input type="checkbox" name="%s[%s][hidden]" value="1" '.checked($item->hidden, '1', false).'/>',
			$this->_args['plural'],
			$item->id
		);
	}
	
	function _select($possibles, $item, $field, $guessed = false) {
		$guessed = $guessed ? ' class="guessed"' : '';
		$options = '<option value="" '.selected($item->$field, '', false).'></option>'.PHP_EOL;	
		
		foreach ($possibles as $id => $title) {
			$options .= sprintf('<option value="%s" '.selected($item->$field, $id, false).'>%s</option>'.PHP_EOL, $id, $title);
		}
		
		return sprintf('<select name="%s[%s][%s]"%s>%s</select>', $this->_args['plural'], $item->id, $field, $guessed, $options);
		
	}
	
	function column_default($item) {
	}
	
	/**
	* Display the rows of records in the table
	* @return string, echo the markup of the rows
	*/
	function zdisplay_rows() {
		//Get the records registered in the prepare_items method
		$records = $this->items;
		
		//Get the columns registered in the get_columns and get_sortable_columns methods
		list( $columns, $hidden ) = $this->get_column_info();
		
		//Loop for each record
		if(!empty($records)){foreach($records as $rec){
			
			//Open the line
			echo '<tr id="record_'.$rec->link_id.'">';
			foreach ( $columns as $column_name => $column_display_name ) {
				
				//Style attributes for each col
				$class = "class='$column_name column-$column_name'";
				$style = "";
				if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
				$attributes = $class . $style;
				
				//edit link
				$editlink  = '/wp-admin/link.php?action=edit&link_id='.(int)$rec->link_id;
				
				//Display the cell
				switch ( $column_name ) {
					case "col_file_name":	echo '<td '.$attributes.'>'.stripslashes($rec->link_id).'</td>';	break;
					case "col_file_repertoire": echo '<td '.$attributes.'><strong><a href="'.$editlink.'" title="Edit">'.stripslashes($rec->link_name).'</a></strong></td>'; break;
					case "col_file_media_type": echo '<td '.$attributes.'>'.stripslashes($rec->link_url).'</td>'; break;
					case "col_file_voicepart": echo '<td '.$attributes.'>'.$rec->link_description.'</td>'; break;
					case "col_file_date_mod": echo '<td '.$attributes.'>'.$rec->link_visible.'</td>'; break;
					case "col_link_visible": echo '<td '.$attributes.'>'.$rec->link_visible.'</td>'; break;
				}
			}
			
			//Close the line
			echo'</tr>';
		}}
	}
	
	function css() {
		echo '
<style type="text/css">

#col_repertoire {
width: 230px !important;
}
.col_repertoire select {
width: 225px !important;
}

#col_media_type,
#col_voicepart {
width: 140px !important;
}
.col_media_type select,
.col_voicepart select {
width: 120px !important;
}

#col_hidden {
/*width: 80px !important;*/
}

.actions .button {
margin-top:1px;
}

select.guessed {
background:lightblue;
}

</style>
<script type="text/javascript">
jQuery(document).ready(function($) {

jQuery(\'.guessed\').change( function() {
jQuery(this).removeClass(\'guessed\');
});

});
</script>
';
		
		/*
		.limitcomments {
		max-height: 100px;
		overflow-y: auto;
		}
		*/
		
	}
	
}






?>