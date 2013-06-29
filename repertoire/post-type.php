<?php

global $user_can_view;

add_action( 'init', 'user_can_view', 0 );
function user_can_view() {
	global $user_can_view;
	$user_can_view = protect_code(array(2, 3));
}


/*----------------------------------------------------*/
// Repertoire Media
/*----------------------------------------------------*/

function register_repertoire_media() {
  global $user_can_view;
  $labels = array(
    'name'                => _x( 'Repertoire Media', 'Post Type General Name', 'lowrez' ),
    'singular_name'       => _x( 'Repertoire Media', 'Post Type Singular Name', 'lowrez' ),
    'menu_name'           => __( 'Repertoire Media', 'lowrez' ),
    'parent_item_colon'   => __( 'Repertoire:', 'lowrez' ),
    'all_items'           => __( 'All Media', 'lowrez' ),
    'view_item'           => __( 'View Media', 'lowrez' ),
    'add_new_item'        => __( 'Add New Media', 'lowrez' ),
    'add_new'             => __( 'New Repertoire Media', 'lowrez' ),
    'edit_item'           => __( 'Edit Media', 'lowrez' ),
    'update_item'         => __( 'Update Media', 'lowrez' ),
    'search_items'        => __( 'Search media', 'lowrez' ),
    'not_found'           => __( 'No repertoire media found', 'lowrez' ),
    'not_found_in_trash'  => __( 'No repertoire media found in Trash', 'lowrez' ),
  );
  
  $capabilities = array(
    'edit_post'           => 'edit_posts',
    'read_post'           => 'read',
    'delete_post'         => 'delete_posts',
    'edit_posts'          => 'edit_posts',
    'edit_others_posts'   => 'edit_others_posts',
    'publish_posts'       => 'publish_posts',
    'read_private_posts'  => 'read_private_posts',
  );
  
	/*$rewrite = array(
    'slug'                => 'media',
    'with_front'          => true,
    'pages'               => false,
    'feeds'               => false,
	);*/
  
  $args = array(
    'label'               => __( 'repertoire-media', 'lowrez' ),
    'description'         => __( 'Repertoire Media Files', 'lowrez' ),
    'labels'              => $labels,
    'supports'            => false,//array( 'title', 'custom-fields', ),
    //'taxonomies'          => array( 'media-type', 'part', 'concert' ),
    'hierarchical'        => false,
	  'public'              => false,//$user_can_view,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 29,
    'menu_icon'           => '',
    'can_export'          => true,
	  'has_archive'         => false,//$user_can_view,
	  'exclude_from_search' => true,//!$user_can_view,
	  'publicly_queryable'  => false, //$user_can_view,
	  'rewrite'             => false,//$rewrite,
    'capabilities'        => $capabilities,
  );
  
  register_post_type( 'repertoire-media', $args );
}

add_action( 'init', 'register_repertoire_media', 0 );

/*----------------------------------------------------*/

function repertoire_updated_messages( $messages ) {
	global $post, $post_ID;
	
	$messages['repertoire-media'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Repertoire media updated.')),// <a href="%s">View book</a>', 'lowrez'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.', 'lowrez'),
		3 => __('Custom field deleted.', 'lowrez'),
		4 => __('Repertoire media updated.', 'lowrez'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Repertoire media restored to revision from %s', 'lowrez'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Repertoire media published.')),// <a href="%s">View book</a>', 'lowrez'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Repertoire media saved.', 'lowrez'),
		8 => sprintf( __('Repertoire media submitted.' //<a target="_blank" href="%s">Preview book</a>'
						 , 'lowrez'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Repertoire media scheduled for: <strong>%1$s</strong>.'),// <a target="_blank" href="%2$s">Preview book</a>', 'lowrez'),
					 // translators: Publish box date format, see http://php.net/date
					 date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Repertoire media draft updated.')),// <a target="_blank" href="%s">Preview book</a>', 'lowrez'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	$messages['repertoire'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Repertoire updated. <a href="%s">View repertoire</a>', 'lowrez'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.', 'lowrez'),
		3 => __('Custom field deleted.', 'lowrez'),
		4 => __('Repertoire updated.', 'lowrez'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Repertoire restored to revision from %s', 'lowrez'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Repertoire published. <a href="%s">View repertoire</a>', 'lowrez'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Repertoire saved.', 'lowrez'),
		8 => sprintf( __('Repertoire submitted. <a target="_blank" href="%s">Preview repertoire</a>', 'lowrez'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Repertoire scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview repertoire</a>', 'lowrez'),
					 // translators: Publish box date format, see http://php.net/date
					 date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Repertoire draft updated. <a target="_blank" href="%s">Preview repertoire</a>', 'lowrez'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	
	return $messages;
}
add_filter( 'post_updated_messages', 'repertoire_updated_messages' );

/*----------------------------------------------------*/

add_filter('name_save_pre', 'save_repertoire_media_slug');
add_filter('title_save_pre', 'save_repertoire_media_title');

function save_repertoire_media_slug($ignore) {
  
  if ('repertoire-media' == $_POST['post_type']) {
    $ignore = sanitize_title($_POST['post_title']);
  }
  
  return $ignore;
}

function no_the($str) {
  if (preg_match('/^(?:the[- ])?(.*)/im', $str, $matches)) {
    $str = $matches[1];
  }
  return $str;
}

function sanitize_title_no_the($title, $raw_title, $context) {
  return no_the($title);
}
add_filter('sanitize_title', 'sanitize_title_no_the', 10, 3);

function nest_terms($taxonomy, $include, $nestable = false, &$nested_terms = array()) {
  
  $terms = get_terms($taxonomy, array('fields' => 'all', 'hide_empty' => false, 'include' => $include));
  
  $ensemble = false;
  $parents = array();
  
  if ($nestable == '()') {
    
    foreach ($terms as $term) {
      
      if ($term->parent) {
        if (!isset($parents[$term->parent])) {
          $parents[$term->parent]['term'] = '((get))';
        }
        $parents[$term->parent]['children'][$term->term_id] = $term->name;
      }
      else {
        $parents[$term->term_id]['term'] = $term->name;
      }
      
    }
    
    foreach ($parents as $id => &$group) {
      if ($group['term'] == '((get))') {
        $term = get_term( $id, $taxonomy );
        $group['term'] = $term->name;
      }
    }
    
    foreach ($parents as &$group) {
      
      if ($group['children']) {
        
        $group['children'] = implode(', ', $group['children']);
        
        $group = $group['term'] . ' (' . $group['children'] . ')';
        $nested_terms[] = $group['term'];
        
      }
      else {
        $group = $group['term'];
      }
      
    }
    
  }
  else {
    
    if ($nestable == 'AB') {
      
      foreach ($terms as $term) {
		  if ($term->slug == 'ensemble' && $taxonomy == 'part') {
			  $ensemble = 'Ensemble';
		  }
		  else {
			  $parents[$term->term_id] = $term;
		  }
      }

      
      $terms = $parents;
      
      foreach ($terms as $term) {
        if (preg_match('/(?:[0-9]|-)[a-z]$/i', $term->slug) && $term->parent) {
          unset($parents[$term->parent]);
          
          $nested_term = get_term( $term->parent, $taxonomy );
          $nested_terms[] = $nested_term->name;
        }
      }
      
      $terms = $parents;
      
    }
    
    foreach ($terms as $term) {
      $parents[$term->term_id] = $term->name;
    }
    
  }
  
  $parents = implode(', ', $parents);
  
  if (!empty($nested_terms)) { 
    $nested_terms = implode(', ', $nested_terms);
  }
  else {
    $nested_terms = false;
  }
  
	if ($ensemble) {
		return "$ensemble ($parents)";
	}
	
	return $ensemble.$parents;
  
}


function save_repertoire_media_title($ignore) {
	
	if ('repertoire-media' == $_POST['post_type']) {
		
		$post_id = $_POST['post_ID'];
		
		if ($_POST['parent_id']) {
			$repertoire = get_post($_POST['parent_id']);
			//print_pre($repertoire);
			$repertoire = $repertoire->post_title;
			
			$media_type = nest_terms('media-type', $_POST['tax_input']['media-type'], false);
			
			$parts_nested = array();
			$part = nest_terms('part', $_POST['tax_input']['part'], 'AB', $parts_nested);
			
			$concert = nest_terms('concert', $_POST['tax_input']['concert'], '()');
			
			$post_title = implode(' - ', array_filter(compact('repertoire', 'media_type', 'part', 'concert')));
			
			$_POST['post_title'] = $post_title;
			
			//print_pre($_POST);
			
			if ($file = get_attached_file($post_id)) {
				
				require_once('mp3tagger/mp3tagger.php');
				
				$tagger = new mp3tagger();
				
				$title = $repertoire;
				$album_artist = 'LOW REZ';
				
				if ($concert) {
					$artist = 'LOW REZ';
					$album = $concert;
				}
				elseif (stripos($media_type, 'Original Recording')!==FALSE) {
					if ($_POST['primary_performer']) {
						$artist = get_term($_POST['primary_performer'], 'performer');
					}
						
					if (is_object($artist)) {
						$artist = $artist->name;
					}
					else {
						$artist = array_shift(wp_get_post_terms($_POST['parent_id'], 'performer', array('fields' => 'names')));//implode(', ', );
					}
					
					$album = 'Original Recording';
					$post_title = implode(' - ', array_filter(compact('repertoire', 'artist', 'media_type')));
					$concert = $artist;
					$original = true;
				}
				else {
					$artist = $part;
					$album = $media_type;
				}
				
				update_post_meta($post_id, 'podcast_title', $title);
				update_post_meta($post_id, 'podcast_artist', $artist);
				update_post_meta($post_id, 'podcast_album', $album);
				update_post_meta($post_id, 'podcast_album_artist', $album_artist);
				
				$rename = true;//FIXME
				
				$duration = false;
				
				define('LOCAL_ALBUM_COVER', true);
				include_once('/home/lowrez/_prod/podcast/albumcover.php');
								
				$media_type_get = array_filter($_POST['tax_input']['media-type']);
				$media_type_get = $media_type_get ? get_terms('media-type', array('include'=>$media_type_get)) : false;
				
				if (!$concert) {
					$part_get = array_filter($_POST['tax_input']['part']);
					$part_get = $part_get ? get_terms('part', array('include'=>$part_get)) : false;
				}
				
				$terms = array();
				
				if (is_array($media_type_get)) {
					foreach ($media_type_get as $mt) {
						$terms[0][] = $mt->slug;
					}
				} else {
					$terms[0] = false;
				}
				if (is_array($part_get)) {
					foreach ($part_get as $mt) {
						$terms[1][] = $mt->slug;
					}
				} else {
					$terms[1] = false;
				}
				
				//print_pre($terms);
				
				if (!$picture = get_album_cover($terms, $concert)) {
					$picture = plugin_dir_path(__FILE__) . 'mp3tagger/albumcover.png';
				}
				
				update_post_meta($post_id, 'podcast_album_cover', $picture);
				
				//echo $picture;
				
				$tagger->write($file, $title, $artist, $album, $album_artist, $picture, $duration);
				
				if ($duration) {
					update_post_meta($post_id, 'podcast_duration', $duration);
				}
				else {
					delete_post_meta($post_id, 'podcast_duration');
				}
				
				if ($rename) {
					
					$duration = false;
					
					$basepath = wp_upload_dir();
					$basepath = $basepath['path'];
					
					if ($concert) {
						if ($original) {
							$filename = $tagger->rename($file, $title, $artist, 'Original Recording', $basepath, $duration);
						}
						else {
							$filename = $tagger->rename($file, $title, $album, 'Concert Recording', $basepath, $duration);
						}
					}
					else {
						if ($parts_nested) { $artist = array($parts_nested, $artist); }
						$filename = $tagger->rename($file, $title, $artist, $album, $basepath, $duration);
					}
					
					if ($filename) {
						update_attached_file($post_id, $filename);          
					}
				}
				
			}
			
    return $post_title;
      
    }
    else {
      
      /*$my_post = array();
      $my_post['ID'] = $post_id;
      $my_post['post_status'] = 'draft';
      $_POST['post_status'] = 'draft';*/
      
      
		
      //remove_action('title_save_pre', 'save_repertoire_media_title');
      //remove_action('save_post', 'save_repertoire_media_meta');
      //wp_update_post( $my_post );
      //add_action('title_save_pre', 'save_repertoire_media_title');

      //print_pre($_POST);
      //die();
      
    }
    
  }
  
  return $ignore;
  
}

/*----------------------------------------------------*/

add_action( 'before_delete_post', 'cleanup_repertoire_media_delete' );
function cleanup_repertoire_media_delete( $post_id ){
  
  // We check if the global post type isn't ours and just return
  global $post_type;   
  if ( $post_type != 'repertoire-media' ) return;
  
  $file = get_attached_file( $post_id );
  if ( ! empty($file) ) {
    $success = @unlink($file);
  }
  
  // My custom stuff for deleting my custom post type here
}

/*----------------------------------------------------*/

add_action('admin_head', 'repertoire_column_widths');

function repertoire_column_widths() {
  //echo $GLOBALS['wp_query']->request;
  global $current_screen;
  if ($current_screen->id == 'edit-repertoire') {
?>
<style type="text/css">
  .column-song-year {
    width: 60px;
  }
  .column-arrangement-year {
    width: 90px;
  }
  .column-repertoire-media {
    width: 80px;
  }
  .tablenav .actions select {
    max-width: 100px;
  }
  .tablenav .actions select[name="m"] {
    display: none;
  }
  .view-switch {
    display:none;
  }
  .column-this-season {
    width: 40px;
  }
  .column-this-season .spinner {
    margin:0;
  }
  th.column-this-season {
    font-size: 0.9em;
    line-height:95%;
  }
  
</style>
<?php
  }
  elseif ($current_screen->id == 'edit-repertoire-media') {
?>
<style type="text/css">
  .tablenav .actions select {
    max-width: 100px;
  }
  .tablenav .actions select[name="m"] {
    display: none;
  }
  .view-switch {
    display:none;
  }
  .file_missing {
    background-color: #f00;
    font-weight: bold;
    color: #fff;
    display: inline-block;
    padding: 0;
    width: 1.22em;
    text-align: center;
    border-radius: 100%;
    cursor:default;
  }
	.column-this-season {
    width: 40px;
  }
  .column-this-season .spinner {
    margin:0;
  }
  th.column-this-season {
    font-size: 0.9em;
    line-height:95%;
  }
</style>
<?php
  }
  
}

function set_repertoire_column( $column, $post_id ) {
	switch ( $column ) {
		case 'song-year':
		case 'arrangement-year':
		$column = str_replace('-', '_', $column);
		$year = get_post_meta($post_id, $column, true);
		
		$link = add_query_arg($column, $year ? $year : -1);
		
		printf('<a href="%s">%s</a>', $link, $year ? $year : '&mdash;');
		break;
		case 'this-season':
		$this_season = get_post_meta($post_id, 'repertoire-this-season', true);
		
		printf('<input type="checkbox" class="ajax-this-season" name="repertoires-this-season[]" value="%s" %s title="This Season" /> ', $post_id, checked( $this_season, 'include', false ));
		
		$this_season_sup = get_post_meta($post_id, 'repertoire-this-season-sup', true);
		
		printf('(<input type="checkbox" class="ajax-this-season ajax-this-season-sup" name="repertoires-this-season-sup[]" value="%s" %s title="Supplementary"/>)', $post_id, checked( $this_season_sup, 'include', false ));
		
		echo '<span class="spinner"></span>';
		
		break;
		case 'repertoire-media':
		
		$args = array(
			'numberposts' => -1,
			'orderby' => 'name',
			'post_type' => 'repertoire-media',
			'post_parent' => $post_id
		);
		$medias = get_posts($args);
		
		if ($medias = count($medias)) {
			printf('<a href="%s">[ %s ]</a>', admin_url('edit.php?post_type=repertoire-media&post_parent='.$post_id), $medias);    
		}
		else {
			echo '&mdash;';
		}
		
		break;
	}
}

add_action( 'manage_repertoire_posts_custom_column' , 'set_repertoire_column', 10, 2 );

/*----------------------------------------------------*/

add_filter( 'request', 'year_column_orderby' );
function year_column_orderby( $vars ) {
  if ( isset( $vars['orderby'] ) && in_array($vars['orderby'], array('song_year', 'arrangement_year') )) {
    $vars = array_merge( $vars, array(
      'meta_key' => $vars['orderby'],
      'orderby' => 'meta_value'
    ) );
  }
  return $vars;
}

/*----------------------------------------------------*/

function repertoire_clauses( $clauses, $wp_query ) {
  global $wpdb;
  
  if (
    isset( $wp_query->query['orderby'] ) &&
    in_array($wp_query->query['orderby'], array('performer', 'composer', 'arranger', 'concert', 'media-type', 'part'))
     ) {
    
    /*$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;*/
    
    $clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT JOIN {$wpdb->term_taxonomy} ON ({$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
AND {$wpdb->term_taxonomy}.taxonomy = '{$wp_query->query['orderby']}')
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;
    
    //$clauses['where'] .= " AND (taxonomy = '{$wp_query->query['orderby']}' OR taxonomy IS NULL)";
    $clauses['groupby'] = "object_id";
    $clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.slug ORDER BY slug ASC) ";
    $clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
  }
  
  return $clauses;
}
add_filter( 'posts_clauses', 'repertoire_clauses', 10, 2 );

/*----------------------------------------------------*/

add_filter( 'parse_query', 'sort_filter_repertoire' );
function sort_filter_repertoire($query) {
	global $current_screen;
	if (is_admin() && in_array($current_screen->id,  array('edit-repertoire', 'edit-repertoire-media')) ) {
		
		if (!isset($_GET['orderby'])) {
			$query->query_vars['orderby'] = 'name';
			$query->query_vars['order'] = 'ASC';
		}
		
		if ( $year = $_GET['song_year'] )  {
			$query->query_vars['meta_query'][] = array(
				'key' => 'song_year',
				'value' => ($year == -1 ? '' : $year)
			);
		}
		if ( $year = $_GET['arrangement_year'] )  {
			$query->query_vars['meta_query'][] = array(
				'key' => 'arrangement_year',
				'value' => ($year == -1 ? '' : $year)
			);
		}
		if ( $this_season = $_GET['this-season'] )  {
			
			if ($this_season == 'sup') {
				$query->query_vars['meta_query'][] = array(
					'key' => 'repertoire-this-season-sup',
					'compare' => 'EXISTS'
				);
			}
			else {
				$exist = array('include'=>'EXISTS', 'exclude'=>'NOT EXISTS');
				$query->query_vars['meta_query'][] = array(
					'key' => 'repertoire-this-season',
					'compare' => $exist[$this_season]
				);
				if ($this_season == 'exclude') {
					$query->query_vars['meta_query'][] = array(
						'key' => 'repertoire-this-season-sup',
						'compare' => 'NOT EXISTS'
					);
				}
			}
			
		}
		if ( $has_media = $_GET['has-media'] )  {
			
			add_filter('posts_where', 'post_has_parent');
			remove_filter( 'parse_query', 'sort_filter_repertoire' );
			$args = array(
				'post_type' => 'repertoire-media',
				'numberposts' => '-1',
			);
			
			$media_posts = get_posts($args);
			remove_filter('posts_where', 'post_has_parent');
			add_filter( 'parse_query', 'sort_filter_repertoire' );
			
			$media_in = array();
			foreach ($media_posts as $media_post) {
				$media_in[] = $media_post->post_parent;
			}
			
			$exist = array('include'=>'post__in', 'exclude'=>'post__not_in');
			
			$query->query_vars[$exist[$has_media]] = $media_in;
			
		}
		if ( $parent = $_GET['post_parent'] )  {
			$query->query_vars['post_parent'] = $parent;
		}
		if ( $mime_type = $_GET['post_mime_type'] )  {
			$query->query_vars['post_mime_type'] = $mime_type;
		}
	}
}
function post_just_get_parent($fields, $and, $more) {
	echo 'just';
	print_pre($fields);
	print_pre($and);print_pre($more);
	return $fields;
}
function post_has_parent($where = '') {
	$where .= " AND post_parent > 0";
	return $where;
}

add_filter('mime_types', 'repertoire_mime_types');
function repertoire_mime_types ( $mimes=array() ) {
 
$mimes['sib'] = 'application/x-sibelius-score';

return $mimes;

}

/*----------------------------------------------------*/

add_action( 'restrict_manage_posts', 'my_restrict_manage_posts' );
function my_restrict_manage_posts() {
  
  // only display these taxonomy filters on desired custom post_type listings
  global $typenow;
  if ($typenow == 'repertoire') {
    
    $count_has = 0;
    $count_hasnot = 0;
    
    echo "<select name='this-season' id='this-season' class='postform'>";
    echo "<option value=''>Show all</option>";
    echo '<option value="include"', $_GET['this-season'] == 'include' ? ' selected="selected"' : '','>This Season</option>';// (' . $count_has .')
	echo '<option value="sup"', $_GET['this-season'] == 'sup' ? ' selected="selected"' : '','>Supplementary</option>';// (' . $count_has .')
    echo '<option value="exclude"', $_GET['this-season'] == 'exclude' ? ' selected="selected"' : '','>Not This Season</option>';// (' . $count_hasnot .')
    echo "</select>";
    
    $count_has = 0;
    $count_hasnot = 0;
    
    echo "<select name='has-media' id='has-media' class='postform'>";
    echo "<option value=''>Show all</option>";
    echo '<option value="include"', $_GET['has-media'] == 'include' ? ' selected="selected"' : '','>Has Media</option>';// (' . $count_has .')
    echo '<option value="exclude"', $_GET['has-media'] == 'exclude' ? ' selected="selected"' : '','>Has No Media</option>';// (' . $count_hasnot .')
    echo "</select>";
    
    // create an array of taxonomy slugs you want to filter by - if you want to retrieve all taxonomies, could use get_taxonomies() to build the list
    $filters = array('performer', 'composer', 'arranger', 'concert');
    
    foreach ($filters as $tax_slug) {
      // retrieve the taxonomy object
      $tax_obj = get_taxonomy($tax_slug);
      $tax_name = $tax_obj->labels->name;
      // retrieve array of term objects per taxonomy
				$args = array('orderby' => 'slug');
      $terms = get_terms($tax_slug, $args);
      
      // output html for taxonomy dropdown filter
      echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
      echo "<option value=''>Show all $tax_name</option>";
      foreach ($terms as $term) {
        // output each select option line, check against the last $_GET to show the current option selected
        echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
      }
      echo "</select>";
    }
    
    echo '</div>
<div class="alignleft actions">';
    
    $filters = array('song_year', 'arrangement_year');
    
    global $wpdb;
    
    foreach ($filters as $tax_slug) {
      // retrieve array of posts per meta value
      $query = $wpdb->prepare("
SELECT meta_value     AS value, 
       Count(post_id) AS count 
FROM   (SELECT DISTINCT post_id, 
                        meta_value 
        FROM   $wpdb->postmeta 
        WHERE  meta_key = '%s') AS tmp_table 
GROUP  BY meta_value 
ORDER  BY meta_value; ", $tax_slug );
      
      $terms = $wpdb->get_results($query);
      
      // output html for metadata dropdown filter
      echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
      echo "<option value=''>Show all Years</option>";
      foreach ($terms as $term) {
        if ( empty($term->value) ) {
          $term->value = '(Blank)';
          $term->id = -1;
        }
        else {
          $term->id = $term->value;
        }
        echo '<option value='. $term->id, $_GET[$tax_slug] == $term->id ? ' selected="selected"' : '','>' . $term->value .' (' . $term->count .')</option>';
      }
      echo "</select>";
    }
	  
	  printf('<a class="button" href="%s">Reset</a> ', admin_url('edit.php?post_type=repertoire'));
	  
  }
  elseif ($typenow == 'repertoire-media') {
    
    global $wpdb;
    
      // retrieve array of posts per meta value
      $terms = $wpdb->get_results("
SELECT post_parent AS id, 
       post_title  AS value, 
       Count(id)   AS count 
FROM   (SELECT DISTINCT children.post_parent, 
                        parents.post_title, 
                        parents.post_name, 
                        children.id 
        FROM   $wpdb->posts AS children 
               INNER JOIN $wpdb->posts AS parents 
                       ON children.post_parent = parents.id 
        WHERE  children.post_type = 'repertoire-media' 
               ) AS tmp_table 
GROUP  BY post_parent 
ORDER  BY post_name; " ); //AND children.post_status = 'publish'
      
      // output html for metadata dropdown filter
      $tax_slug = 'post_parent';
      echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
      echo "<option value=''>Show all Repertoire</option>";
      foreach ($terms as $term) {
        if ( empty($term->value) ) {
          $term->value = '(Blank)';
          $term->id = -1;
        }
        echo '<option value='. $term->id, $_GET[$tax_slug] == $term->id ? ' selected="selected"' : '','>' . $term->value .' (' . $term->count .')</option>';
      }
      echo "</select>";
	  
	  /*-------------------------------------------------*/
	  
      // retrieve array of posts per meta value
	  $terms = $wpdb->get_results("
SELECT post_mime_type AS id, count(*) AS count
FROM   $wpdb->posts
WHERE  post_type = 'repertoire-media' 
GROUP BY post_mime_type
" ); //AND post_status = 'publish'
      
      // output html for metadata dropdown filter
      $tax_slug = 'post_mime_type';
      echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
      echo "<option value=''>Show all File Types</option>";
      foreach ($terms as $term) {
		  if ( empty($term->id) ) {
			  $term->value = '(Blank)';
			  $term->id = '';
		  }
		  else {
			  $term->value = $term->id; //FIXME
		  }
        echo '<option value='. $term->id, $_GET[$tax_slug] == $term->id ? ' selected="selected"' : '','>' . $term->value .' (' . $term->count .')</option>';
      }
      echo "</select>";
	  
	  /*-------------------------------------------------*/
    
    // create an array of taxonomy slugs you want to filter by - if you want to retrieve all taxonomies, could use get_taxonomies() to build the list
    $filters = array('media-type', 'part', 'concert');
    
    foreach ($filters as $tax_slug) {
      // retrieve the taxonomy object
      $tax_obj = get_taxonomy($tax_slug);
      $tax_name = $tax_obj->labels->name;
      // retrieve array of term objects per taxonomy
				
      $terms = get_terms($tax_slug);
      
      // output html for taxonomy dropdown filter
      echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
      echo "<option value=''>Show all $tax_name</option>";
      foreach ($terms as $term) {
        // output each select option line, check against the last $_GET to show the current option selected
        if ($term->count) echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
      }
      echo "</select>";
    }  
	  
	  printf('<a class="button" href="%s">Reset</a> ', admin_url('edit.php?post_type=repertoire-media'));

  }
}

/*----------------------------------------------------*/

function change_repertoire_media_columns( $cols ) {
  
  unset($cols['protected']);
  unset($cols['date']);
  
  $cols['title'] = 'Title';
  
  $first = array_shift(array_chunk($cols, 2, true));
  $middle = array(
    'file_name' => 'File Name',
    'repertoire' => 'Repertoire',
	  'this-season' => 'This Season'
  );
  $last = array_splice($cols, 2);
  
  $cols = array_merge($first, $middle, $last);
  
  $cols['taxonomy-media-type'] = 'Media Type';
  $cols['taxonomy-part'] = 'Part';
  $cols['taxonomy-concert'] = 'Concert';
  
  return $cols;
}
add_filter( "manage_repertoire-media_posts_columns", 'change_repertoire_media_columns' );

function set_repertoire_media_column( $column, $post_id ) {
  switch ( $column ) {
    case 'file_name':
    
    $not_exists = repertoire_media_exists($post_id);
    $file = repertoire_get_attachment_url($post_id);
    
    if ($not_exists==false) {
      printf('<a href="%s" target="_blank">%s</a>', $file, basename($file));
    }
    else {
      printf('%s %s', basename($file), $not_exists);
    }
    
    
    break;
    case 'repertoire':
    $post = get_post($post_id);
    if ($parent = $post->post_parent) {
      echo edit_post_link(get_the_title($parent), '', '', $parent);
    }
    else {
      echo '&mdash;';
    }
    
    break;
	  case 'this-season':
	  $post = get_post($post_id);
	  if ($parent = $post->post_parent) {
		  
		  $this_season = get_post_meta($parent, 'repertoire-this-season', true);
		  
		  printf('<input type="checkbox" %s title="This Season" disabled readonly /> ', checked( $this_season, 'include', false ));
		  
		  $this_season_sup = get_post_meta($parent, 'repertoire-this-season-sup', true);
		  
		  printf('(<input type="checkbox" %s title="Supplementary" disabled readonly />)', checked( $this_season_sup, 'include', false ));
	  }
    break;
  }
}

add_action( 'manage_repertoire-media_posts_custom_column' , 'set_repertoire_media_column', 10, 2 );

/*----------------------------------------------------*/

add_action('quick_edit_custom_box',  'repertoire_media_quick_edit', 10, 2);

function repertoire_media_quick_edit($column_name, $post_type) {
  global $post;
  if ($post_type != 'repertoire-media') return;
  if ($column_name == 'repertoire') {
?>
<fieldset class="inline-edit-col-left">
  <div class="inline-edit-col">
    <span class="title">Repertoire</span>
    <?php //<input type="hidden" name="repertoire_media_noncename" id="repertoire_media_noncename" value="" /> ?>
    <?php wp_dropdown_repertoire('parent_id'); // $post->post_parent?>
  </div>
</fieldset>
<?php
                    }
}

/*----------------------------------------------------*/

// Add to our admin_init function
add_filter('post_row_actions', 'repertoire_media_quick_edit_link', 10, 2);

function repertoire_media_quick_edit_link($actions, $post) {
  global $current_screen;
  if (($current_screen->id != 'edit-repertoire-media')) return $actions;
  
  //$nonce = wp_create_nonce( 'shiba_widget_set'.$post->ID);
  //$widget_id = get_post_meta( $post->ID, 'post_widget', TRUE);
  $parent_id = $post->post_parent;
  $actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
  $actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '" ';
  $actions['inline hide-if-no-js'] .= " onclick=\"set_inline_parent_id('{$parent_id}')\">";
  $actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
  $actions['inline hide-if-no-js'] .= '</a>';
  return $actions;   
}

// Add to our admin_init function
add_action('admin_footer', 'repertoire_media_quick_edit_javascript');

function repertoire_media_quick_edit_javascript() {
  global $current_screen;
  if (($current_screen->id != 'edit-repertoire-media')) return;
  
?>
<script type="text/javascript">
  <!--
    function set_inline_parent_id(parentId) {
      console.log(parentId);
      // revert Quick Edit menu so that it refreshes properly
      inlineEditPost.revert();
      var widgetInput = document.getElementById('parent_id');
      // check option manually
      for (i = 0; i < widgetInput.options.length; i++) {
        if (widgetInput.options[i].value == parentId) {
          widgetInput.options[i].setAttribute("selected", "selected");
        } else { widgetInput.options[i].removeAttribute("selected"); }
      }
    }
  //-->
</script>
<?php
}

// Add to our admin_init function
add_action('admin_footer', 'repertoire_table_ajax');

function repertoire_table_ajax() {
  global $current_screen;
  if (($current_screen->id != 'edit-repertoire')) return;
  
?>
<script type="text/javascript">
  jQuery('.ajax-this-season').on('change', function () {
    
    var spinner = jQuery(this).parent().find('.spinner');
    
    spinner.show();
    
    send = {
      action: 'change_repertoire_this_season',
      repertoire_id: jQuery(this).val(),
		sup: jQuery(this).hasClass('ajax-this-season-sup'),
      checked: jQuery(this).is(':checked')
    };
    
    jQuery.ajax({
      type:"POST",
      url: "/wp-admin/admin-ajax.php",
      data: send,
      success: function(data) {
        if (data=='true') {
          jQuery(this).prop('checked', true);
        }
        else if (data=='false') {
          jQuery(this).prop('checked', false);
        }
        else {
          jQuery(this).prop('checked', false);
        }
        spinner.hide();
      }
    });
    
    return false;
  });

</script>
<?php
}

function change_repertoire_this_season() {
  
  if ($repertoire = $_POST['repertoire_id']) {
    
    if ($_POST['checked']) {
      
      $checked = $_POST['checked'] == 'true';
		$sup = $_POST['sup'] == 'true' ? '-sup' : false;
      
      if (current_user_can('edit_posts')) {
        
        if ($checked) {
          update_post_meta($repertoire, 'repertoire-this-season'.$sup, 'include');
        }
        else {
          delete_post_meta($repertoire, 'repertoire-this-season'.$sup);
        }
        
		uncache_podcast($repertoire);
		  
		global $wpdb;

		  $post_modified = current_time( 'mysql' );
		  $post_modified_gmt = current_time( 'mysql', 1 );
		  $wpdb->update($wpdb->posts,
						array(
							'post_modified' => $post_modified,
							'post_modified_gmt' => $post_modified_gmt
						),
						array('ID' => $repertoire)
					   );
		  $wpdb->update($wpdb->posts,
						array(
							'post_modified' => $post_modified,
							'post_modified_gmt' => $post_modified_gmt
						),
						array('post_parent' => $repertoire)
					   );
		  
        $checked = get_post_meta($repertoire, 'repertoire-this-season'.$sup, true) == 'include' ? 'true' : 'false';
        
        echo $checked;
        
      }
      else {
        echo $checked;
      }
    }
    else {
      echo 'false';
    }
  }
  else {
    echo 'false';
  }
  
  die();
}
add_action('wp_ajax_change_repertoire_this_season', 'change_repertoire_this_season');
//add_action('wp_ajax_nopriv_change_repertoire_this_season', 'change_repertoire_this_season'); // not really needed


/*----------------------------------------------------*/

function repertoire_media_sortable_columns() {
  return array(
    'title' => 'title',
    //'file_name' => 'file_name',
    //'repertoire' => 'parent_id',
    'taxonomy-media-type' => 'media-type',
    'taxonomy-part' => 'part',
    'taxonomy-concert' => 'concert',
  );
}

add_filter( "manage_edit-repertoire-media_sortable_columns", "repertoire_media_sortable_columns" );

/*----------------------------------------------------*/
// Repertoire
/*----------------------------------------------------*/

function register_repertoire() {
  global $user_can_view;
  $labels = array(
    'name'                => _x( 'Repertoire', 'Post Type General Name', 'lowrez' ),
    'singular_name'       => _x( 'Repertoire', 'Post Type Singular Name', 'lowrez' ),
    'menu_name'           => __( 'Repertoire', 'lowrez' ),
    'parent_item_colon'   => __( 'Parent Repertoire:', 'lowrez' ),
    'all_items'           => __( 'All Repertoire', 'lowrez' ),
    'view_item'           => __( 'View Repertoire', 'lowrez' ),
    'add_new_item'        => __( 'Add New Repertoire', 'lowrez' ),
    'add_new'             => __( 'New Repertoire', 'lowrez' ),
    'edit_item'           => __( 'Edit Repertoire', 'lowrez' ),
    'update_item'         => __( 'Update Repertoire', 'lowrez' ),
    'search_items'        => __( 'Search repertoire', 'lowrez' ),
    'not_found'           => __( 'No repertoire found', 'lowrez' ),
    'not_found_in_trash'  => __( 'No repertoire found in Trash', 'lowrez' ),
  );
  
  $capabilities = array(
    'edit_post'           => 'edit_posts',
    'read_post'           => 'read',
    'delete_post'         => 'delete_posts',
    'edit_posts'          => 'edit_posts',
    'edit_others_posts'   => 'edit_others_posts',
    'publish_posts'       => 'publish_posts',
    'read_private_posts'  => 'read_private_posts',
  );
  
  $args = array(
    'label'               => __( 'repertoire', 'lowrez' ),
    'description'         => __( 'Repertoire', 'lowrez' ),
    'labels'              => $labels,
    'supports'            => array( 'title', 'thumbnail' ),//, 'custom-fields',
    'taxonomies'          => array( 'performer', 'composer', 'arranger', 'concert' ),
    'hierarchical'        => false,
	  'public'              => true,//$user_can_view,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 28,
    'menu_icon'           => '',
    'can_export'          => true,
	  'has_archive'         => true,//$user_can_view,
    'exclude_from_search' => !$user_can_view,
	  'publicly_queryable'  => true,//$user_can_view,
    'capabilities'        => $capabilities,
  );
  
  register_post_type( 'repertoire', $args );
}

// Hook into the 'init' action
add_action( 'init', 'register_repertoire', 0 );

/*----------------------------------------------------*/

function change_repertoire_columns( $cols ) {
  
  unset($cols['protected']);
  unset($cols['date']);
  
  unset($cols['taxonomy-performer']);
  unset($cols['taxonomy-composer']);
  unset($cols['taxonomy-arranger']);
  unset($cols['taxonomy-concert']);
  
  $cols['taxonomy-performer'] = 'Performer';
  $cols['song-year'] = 'Year';
  
  $cols['taxonomy-composer'] = 'Composer';
  
  $cols['taxonomy-arranger'] = 'Arranger';
  $cols['arrangement-year'] = 'Arranged';
  
  $cols['this-season'] = 'This Season';
  
  $cols['taxonomy-concert'] = 'Concert';
  
  $cols['repertoire-media'] = 'Media';
  
  return $cols;
}
add_filter( "manage_edit-repertoire_columns", 'change_repertoire_columns' );

/*----------------------------------------------------*/
//http://test.lowrez.com.au/wp-admin/edit.php?post_type=repertoire-media&post_parent=2753
function repertoire_sortable_columns() {
  return array(
    'title' => 'title',
    'taxonomy-performer' => 'performer',
    'taxonomy-composer' => 'composer',
    'taxonomy-arranger' => 'arranger',
    'taxonomy-concert' => 'concert',
    //'repertoire-media' => 'repertoire-media',
    'song-year' => 'song_year',
    'arrangement-year' => 'arrangement_year',
    //'this-season' => 'repertoire-this-season',
  );
}

add_filter( "manage_edit-repertoire_sortable_columns", "repertoire_sortable_columns" );

/*----------------------------------------------------*/

add_action("save_post", "uncache_podcast");

function uncache_podcast($post_id) {
	if (in_array(get_post_type($post_id), array("repertoire-media", "repertoire"))) {
		update_option('podcast_cache', time());
	}
}