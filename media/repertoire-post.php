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
		'parent_item_colon'   => __( 'Parent Repertoire Media:', 'lowrez' ),
		'all_items'           => __( 'All Repertoire Media', 'lowrez' ),
		'view_item'           => __( 'View Repertoire Media', 'lowrez' ),
		'add_new_item'        => __( 'Add New Repertoire Media', 'lowrez' ),
		'add_new'             => __( 'New Media', 'lowrez' ),
		'edit_item'           => __( 'Edit Repertoire Media', 'lowrez' ),
		'update_item'         => __( 'Update Repertoire Media', 'lowrez' ),
		'search_items'        => __( 'Search repertoire media', 'lowrez' ),
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
	
	$args = array(
		'label'               => __( 'repertoire-media', 'lowrez' ),
		'description'         => __( 'Repertoire Media Files', 'lowrez' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'custom-fields', ),
		'taxonomies'          => array( 'media-type', 'part', 'concert' ),
		'hierarchical'        => false,
		'public'              => $user_can_view,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 29,
		'menu_icon'           => '',
		'can_export'          => true,
		'has_archive'         => $user_can_view,
		'exclude_from_search' => !$user_can_view,
		'publicly_queryable'  => $user_can_view,
		'capabilities'        => $capabilities,
	);
	
	register_post_type( 'repertoire-media', $args );
}

add_action( 'init', 'register_repertoire_media', 0 );

/*----------------------------------------------------*/

function change_repertoire_media_columns( $cols ) {
	
	unset($cols['protected']);
	unset($cols['date']);
	
	$cols['title'] = 'File Name';
	
	$first = array_shift(array_chunk($cols, 2, true));
	$middle = array(
		'file_path' => 'File Path',
		'repertoire' => 'Repertoire',
	);
	$last = array_splice($cols, 2);
	
	$cols = array_merge($first, $middle, $last);
	
	$cols['taxonomy-media-type'] = 'Media Type';
	$cols['taxonomy-part'] = 'Part';
	$cols['taxonomy-concert'] = 'Concert';
	
  return $cols;
}
add_filter( "manage_edit-repertoire-media_columns", 'change_repertoire_media_columns' );

/*----------------------------------------------------*/

function repertoire_media_sortable_columns() {
	return array(
		'title' => 'title',
		'file_path' => 'file_path',
		'repertoire' => 'repertoire',
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
		'supports'            => array( 'title', 'thumbnail', 'custom-fields', ),
		'taxonomies'          => array( 'performer', 'composer', 'arranger', 'concert' ),
		'hierarchical'        => false,
		'public'              => $user_can_view,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 28,
		'menu_icon'           => '',
		'can_export'          => true,
		'has_archive'         => $user_can_view,
		'exclude_from_search' => !$user_can_view,
		'publicly_queryable'  => $user_can_view,
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
	
	$cols['repertoire-media'] = 'Media';
	
  return $cols;
}
add_filter( "manage_edit-repertoire_columns", 'change_repertoire_columns' );

/*----------------------------------------------------*/

function repertoire_sortable_columns() {
	return array(
		'title' => 'title',
		'taxonomy-performer' => 'performer',
		'taxonomy-composer' => 'composer',
		'taxonomy-arranger' => 'arranger',
		'taxonomy-concert' => 'concert',
		'repertoire-media' => 'repertoire-media',
	);
}

add_filter( "manage_edit-repertoire_sortable_columns", "repertoire_sortable_columns" );