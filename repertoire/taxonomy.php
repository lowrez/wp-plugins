<?php
//http://www.codehooligans.com/2010/07/07/custom-meta-for-new-taxonomies-in-wordpress-3-0/
/*----------------------------------------------------*/
// Media Types
/*----------------------------------------------------*/
function register_media_type()  {
	$labels = array(
		'name'                       => _x( 'Media Types', 'Taxonomy General Name', 'lowrez' ),
		'singular_name'              => _x( 'Media Type', 'Taxonomy Singular Name', 'lowrez' ),
		'menu_name'                  => __( 'Media Types', 'lowrez' ),
		'all_items'                  => __( 'All Media Types', 'lowrez' ),
		'parent_item'                => __( 'Parent Media Type', 'lowrez' ),
		'parent_item_colon'          => __( 'Parent Media Type:', 'lowrez' ),
		'new_item_name'              => __( 'New Media Type', 'lowrez' ),
		'add_new_item'               => __( 'Add New Media Type', 'lowrez' ),
		'edit_item'                  => __( 'Edit Media Type', 'lowrez' ),
		'update_item'                => __( 'Update Media Type', 'lowrez' ),
		'separate_items_with_commas' => __( 'Separate media types with commas', 'lowrez' ),
		'search_items'               => __( 'Search media types', 'lowrez' ),
		'add_or_remove_items'        => __( 'Add or remove media types', 'lowrez' ),
		'choose_from_most_used'      => __( 'Choose from the most used media types', 'lowrez' ),
	);
	
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => $user_can_view,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);
	
	register_taxonomy( 'media-type', 'repertoire-media', $args );
}

// Hook into the 'init' action
add_action( 'init', 'register_media_type', 0 );

/*----------------------------------------------------*/
// Parts
/*----------------------------------------------------*/
function register_part()  {
	$labels = array(
		'name'                       => _x( 'Parts', 'Taxonomy General Name', 'lowrez' ),
		'singular_name'              => _x( 'Part', 'Taxonomy Singular Name', 'lowrez' ),
		'menu_name'                  => __( 'Parts', 'lowrez' ),
		'all_items'                  => __( 'All Parts', 'lowrez' ),
		'parent_item'                => __( 'Parent Part', 'lowrez' ),
		'parent_item_colon'          => __( 'Parent Part:', 'lowrez' ),
		'new_item_name'              => __( 'New Part', 'lowrez' ),
		'add_new_item'               => __( 'Add New Part', 'lowrez' ),
		'edit_item'                  => __( 'Edit Part', 'lowrez' ),
		'update_item'                => __( 'Update Part', 'lowrez' ),
		'separate_items_with_commas' => __( 'Separate parts with commas', 'lowrez' ),
		'search_items'               => __( 'Search parts', 'lowrez' ),
		'add_or_remove_items'        => __( 'Add or remove parts', 'lowrez' ),
		'choose_from_most_used'      => __( 'Choose from the most used parts', 'lowrez' ),
	);
	
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => $user_can_view,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);
	
	register_taxonomy( 'part', 'repertoire-media', $args );
}

// Hook into the 'init' action
add_action( 'init', 'register_part', 0 );

/*----------------------------------------------------*/
// Performers
/*----------------------------------------------------*/

function register_performer()  {
	$labels = array(
		'name'                       => _x( 'Performers', 'Taxonomy General Name', 'lowrez' ),
		'singular_name'              => _x( 'Performer', 'Taxonomy Singular Name', 'lowrez' ),
		'menu_name'                  => __( 'Performers', 'lowrez' ),
		'all_items'                  => __( 'All Performers', 'lowrez' ),
		'parent_item'                => __( 'Parent Performer', 'lowrez' ),
		'parent_item_colon'          => __( 'Parent Performer:', 'lowrez' ),
		'new_item_name'              => __( 'New Performer', 'lowrez' ),
		'add_new_item'               => __( 'Add New Performer', 'lowrez' ),
		'edit_item'                  => __( 'Edit Performer', 'lowrez' ),
		'update_item'                => __( 'Update Performer', 'lowrez' ),
		'separate_items_with_commas' => __( 'Separate performers with commas', 'lowrez' ),
		'search_items'               => __( 'Search performers', 'lowrez' ),
		'add_or_remove_items'        => __( 'Add or remove performers', 'lowrez' ),
		'choose_from_most_used'      => __( 'Choose from the most used performers', 'lowrez' ),
	);
	
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => $user_can_view,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);
	
	register_taxonomy( 'performer', 'repertoire', $args );
}

// Hook into the 'init' action
add_action( 'init', 'register_performer', 0 );

/*----------------------------------------------------*/
// Composers
/*----------------------------------------------------*/

function register_composer()  {
	$labels = array(
		'name'                       => _x( 'Composers', 'Taxonomy General Name', 'lowrez' ),
		'singular_name'              => _x( 'Composer', 'Taxonomy Singular Name', 'lowrez' ),
		'menu_name'                  => __( 'Composers', 'lowrez' ),
		'all_items'                  => __( 'All Composers', 'lowrez' ),
		'parent_item'                => __( 'Parent Composer', 'lowrez' ),
		'parent_item_colon'          => __( 'Parent Composer:', 'lowrez' ),
		'new_item_name'              => __( 'New Composer', 'lowrez' ),
		'add_new_item'               => __( 'Add New Composer', 'lowrez' ),
		'edit_item'                  => __( 'Edit Composer', 'lowrez' ),
		'update_item'                => __( 'Update Composer', 'lowrez' ),
		'separate_items_with_commas' => __( 'Separate composers with commas', 'lowrez' ),
		'search_items'               => __( 'Search composers', 'lowrez' ),
		'add_or_remove_items'        => __( 'Add or remove composers', 'lowrez' ),
		'choose_from_most_used'      => __( 'Choose from the most used composers', 'lowrez' ),
	);
	
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => $user_can_view,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);
	
	register_taxonomy( 'composer', 'repertoire', $args );
}

// Hook into the 'init' action
add_action( 'init', 'register_composer', 0 );

/*----------------------------------------------------*/
// Arrangers
/*----------------------------------------------------*/

function register_arranger()  {
	$labels = array(
		'name'                       => _x( 'Arrangers', 'Taxonomy General Name', 'lowrez' ),
		'singular_name'              => _x( 'Arranger', 'Taxonomy Singular Name', 'lowrez' ),
		'menu_name'                  => __( 'Arrangers', 'lowrez' ),
		'all_items'                  => __( 'All Arrangers', 'lowrez' ),
		'parent_item'                => __( 'Parent Arranger', 'lowrez' ),
		'parent_item_colon'          => __( 'Parent Arranger:', 'lowrez' ),
		'new_item_name'              => __( 'New Arranger', 'lowrez' ),
		'add_new_item'               => __( 'Add New Arranger', 'lowrez' ),
		'edit_item'                  => __( 'Edit Arranger', 'lowrez' ),
		'update_item'                => __( 'Update Arranger', 'lowrez' ),
		'separate_items_with_commas' => __( 'Separate arrangers with commas', 'lowrez' ),
		'search_items'               => __( 'Search arrangers', 'lowrez' ),
		'add_or_remove_items'        => __( 'Add or remove arrangers', 'lowrez' ),
		'choose_from_most_used'      => __( 'Choose from the most used arrangers', 'lowrez' ),
	);
	
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => $user_can_view,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);
	
	register_taxonomy( 'arranger', 'repertoire', $args );
}

// Hook into the 'init' action
add_action( 'init', 'register_arranger', 0 );

/*----------------------------------------------------*/
// Concerts
/*----------------------------------------------------*/
function register_concert()  {
	$labels = array(
		'name'                       => _x( 'Concerts', 'Taxonomy General Name', 'lowrez' ),
		'singular_name'              => _x( 'Concert', 'Taxonomy Singular Name', 'lowrez' ),
		'menu_name'                  => __( 'Concerts', 'lowrez' ),
		'all_items'                  => __( 'All Concerts', 'lowrez' ),
		'parent_item'                => __( 'Parent Concert', 'lowrez' ),
		'parent_item_colon'          => __( 'Parent Concert:', 'lowrez' ),
		'new_item_name'              => __( 'New Concert', 'lowrez' ),
		'add_new_item'               => __( 'Add New Concert', 'lowrez' ),
		'edit_item'                  => __( 'Edit Concert', 'lowrez' ),
		'update_item'                => __( 'Update Concert', 'lowrez' ),
		'separate_items_with_commas' => __( 'Separate concerts with commas', 'lowrez' ),
		'search_items'               => __( 'Search concerts', 'lowrez' ),
		'add_or_remove_items'        => __( 'Add or remove concerts', 'lowrez' ),
		'choose_from_most_used'      => __( 'Choose from the most used concerts', 'lowrez' ),
	);
	
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => $user_can_view,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);
	
	register_taxonomy( 'concert', array('repertoire', 'repertoire-media'), $args );
}

// Hook into the 'init' action
add_action( 'init', 'register_concert', 0 );

/*----------------------------------------------------*/
add_filter("manage_edit-part_columns", 'repertoire_regex_columns', 90); 
add_filter("manage_edit-media-type_columns", 'repertoire_regex_columns', 90); 
 
function repertoire_regex_columns($columns) {
	$columns['description'] = 'Filename Regex';
	unset($columns['protected']);
	unset($columns['posts']);
    return $columns;
}

add_action('admin_footer', 'repertoire_part_labels');

function repertoire_part_labels() {
  global $current_screen;
	//print_pre($current_screen);
  if (!in_array($current_screen->id, array('edit-part', 'edit-media-type'))) return;
  
?>
<script type="text/javascript">
  <!--
	jQuery('label[for="tag-description"], label[for="description"]').text('Filename Regex');
	jQuery('#tag-description').next('p').add(jQuery('#description').siblings('span.description')).text('The regular expression used to identify the contents of repertoire media files.');
  -->
</script>
<?php
}

/*----------------------------------------------------*/
add_filter("manage_edit-concert_columns", 'repertoire_remove_description_column', 90); 
add_filter("manage_edit-performer_columns", 'repertoire_remove_description_column', 90); 
add_filter("manage_edit-arranger_columns", 'repertoire_remove_description_column', 90); 
add_filter("manage_edit-composer_columns", 'repertoire_remove_description_column', 90); 
 
function repertoire_remove_description_column($columns) {
	unset($columns['description']);
	unset($columns['protected']);
	unset($columns['posts']);
    return $columns;
}

add_action('admin_footer', 'repertoire_remove_description');

function repertoire_remove_description() {
  global $current_screen;
  if (!in_array($current_screen->id, array('edit-concert', 'edit-performer', 'edit-arranger', 'edit-composer'))) return;
  
?>
<script type="text/javascript">
  <!--
	jQuery('#tag-description, #description').closest('.form-field').remove();
  -->
</script>
<?php
}

add_action('admin_footer', 'repertoire_remove_protection');

function repertoire_remove_protection() {
  global $current_screen;
  if (!in_array($current_screen->id, array('edit-concert', 'edit-performer', 'edit-arranger', 'edit-composer', 'edit-part', 'edit-media-type'))) return;
  
?>
<script type="text/javascript">
  <!--
	jQuery('#ctxps-cb-protect').closest('table.form-table').add(jQuery('#term-restrict')).remove();
  -->
</script>
<?php
}


/*// Add to admin_init function   
add_filter("manage_shiba_theme_custom_column", 'manage_theme_columns', 10, 3);
 
function manage_theme_columns($out, $column_name, $theme_id) {
    $theme = get_term($theme_id, 'shiba_theme');
    switch ($column_name) {
        case 'header_icon': 
            // get header image url
            $data = maybe_unserialize($theme->description);
            $out .= "<img src=\"{$data['HEADER_image']}\" width=\"250\" height=\"83\"/>"; 
            break;
 
        default:
            break;
    }
    return $out;    
}*/