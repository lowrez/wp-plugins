<?php

add_action( 'admin_menu', 'remove_repertoire_add_new' );
function remove_repertoire_add_new() {
	global $pagenow;
	if($pagenow == 'post-new.php' && $_GET['post_type'] == 'repertoire-media'){
		wp_die('You cannot add repertoire media from this page. Please <a href="'.admin_url('edit.php?post_type=repertoire-media').'">upload files</a> instead.');
	}
	remove_submenu_page('edit.php?post_type=repertoire-media', 'post-new.php?post_type=repertoire-media');
}

add_action('admin_head','repertoire_remove_add_buttons');
function repertoire_remove_add_buttons() {
	global $pagenow;
	if(is_admin()){
		if($pagenow == 'edit.php' && $_GET['post_type'] == 'repertoire-media'){
			echo '<style type="text/css">.add-new-h2{display: none;}</style>';
		}
	}
}

//Add the meta box callback function
function register_repertoire_media_meta(){
	
	//print_pre($screen);
	global $post;
	if ($post->post_status != 'auto-draft') {
		add_meta_box("repertoire_media_file", "File Attributes", "set_repertoire_media_file", "repertoire-media", "side", "high");
		add_meta_box("repertoire_media_metadata", "File Metadata", "set_repertoire_media_metadata", "repertoire-media", "side", "high");
		add_meta_box("repertoire_media_title", "Title", "set_repertoire_media_title", "repertoire-media", "normal", "high");
	}
	//add_meta_box("repertoire_media_preview", "Preview", "set_repertoire_media_preview", "repertoire-media", "normal", "normal");
	
	add_meta_box("repertoire_this_season", "Include in This Season", "set_repertoire_this_season", "repertoire", "side", "high");
	
	add_meta_box("repertoire_media_meta", "Repertoire", "set_repertoire_media_meta", "repertoire-media", "normal", "high");
	
	add_meta_box("repertoire_media_list", "Related Media", "set_repertoire_media_list", "repertoire", "side", "high");
	
	add_meta_box("repertoire_meta", "Attribution", "set_repertoire_meta", "repertoire", "normal", "high");
}
add_action("add_meta_boxes", "register_repertoire_media_meta");

function remove_repertoire_media_metaboxes() {
	remove_meta_box( 'media-typediv' , 'repertoire-media' , 'side' ); 
	remove_meta_box( 'partdiv' , 'repertoire-media' , 'side' ); 
	remove_meta_box( 'concertdiv' , 'repertoire-media' , 'side' );
	remove_meta_box( 'slugdiv' , 'repertoire-media' , 'side' );
	remove_meta_box( 'ctxps-grouplist-box' , 'repertoire-media' , 'side' );  
	remove_meta_box( 'ctxps-grouplist-box' , 'repertoire' , 'side' ); 
	
	remove_meta_box( 'composerdiv' , 'repertoire' , 'side' ); 
	remove_meta_box( 'arrangerdiv' , 'repertoire' , 'side' ); 
	remove_meta_box( 'performerdiv' , 'repertoire' , 'side' );
	remove_meta_box( 'concertdiv' , 'repertoire' , 'side' );
	remove_meta_box( 'slugdiv' , 'repertoire' , 'side' );
	
	remove_meta_box( 'postimagediv', 'repertoire', 'side' );
	add_meta_box('postimagediv', __('Album Cover'), 'post_thumbnail_meta_box', 'repertoire', 'side', 'high');
	
	
	global $post;
	if ($post->post_status == 'auto-draft') {
		remove_meta_box( 'submitdiv', 'repertoire-media', 'side' );
	}
	
}
add_action( 'add_meta_boxes' , 'remove_repertoire_media_metaboxes' );

function set_repertoire_meta() {
	global $post;
	$custom = get_post_custom($post->ID);
?>
<table class="repertoire-custom repertoire-custom-meta">
	<tr>
		<td colspan="2"><span class="labellike">Performer: <strong><?php echo implode(', ', wp_get_post_terms($post->ID, 'performer', array('fields' => 'names'))); ?></strong></span></td>
		<td><label>Year of Release: </label><input type="text" name="song_year" value="<?php echo $custom['song_year'][0]; ?>" style="width: 50px;" /></td>
		<td><label>Year of Arrangement: </label><input type="text" name="arrangement_year" value="<?php echo $custom['arrangement_year'][0]; ?>" style="width: 50px;" /></td>
	</tr>
	<tr>
		<td colspan="2"><span class="labellike">Composer: <strong><?php echo implode(', ', wp_get_post_terms($post->ID, 'composer', array('fields' => 'names'))); ?></strong></span></td>
		<td colspan="2" rowspan="2"><label>Copyright Notice: </label><br>
			<textarea name="copyright_notice" style="width:100%;"><?php echo $custom['copyright_notice'][0]; ?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2"><span class="labellike">Arranger: <strong><?php echo implode(', ', wp_get_post_terms($post->ID, 'arranger', array('fields' => 'names'))); ?></strong></span></td>
	</tr>
	<tr>
		<th style="padding:0;"><h3>Performer</h3></th>
		<th style="padding:0;"><h3>Composer</h3></th>
		<th style="padding:0;"><h3>Arranger</h3></th>
		<th style="padding:0;"><h3>Concerts</h3></th>
	</tr>
	<tr>
		<td><?php $box = array('args' => array('taxonomy' => 'performer')); post_taxonomy_meta_box($post, $box); ?></td>
		<td><?php $box = array('args' => array('taxonomy' => 'composer')); post_taxonomy_meta_box($post, $box); ?></td>
		<td><?php $box = array('args' => array('taxonomy' => 'arranger')); post_taxonomy_meta_box($post, $box); ?></td>
		<td><?php $box = array('args' => array('taxonomy' => 'concert')); post_taxonomy_meta_box($post, $box); ?></td>
	</tr>
</table>
<?php
	echo '<input type="hidden" name="repertoire_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}

/* -------------------------------------------------------- */

function set_repertoire_this_season() {
	global $post;
	$this_season = get_post_meta($post->ID, 'repertoire-this-season', true);
	$this_season_sup = get_post_meta($post->ID, 'repertoire-this-season-sup', true);
?>

<p><label><input type="checkbox" name="repertoire-this-season" value="include" <?php checked( $this_season, 'include' ); ?> />&nbsp; Include in this season's repertoire? </label></p>
<p><label><input type="checkbox" name="repertoire-this-season-sup" value="include" <?php checked( $this_season_sup, 'include' ); ?> />&nbsp; Include in <i>supplementary</i> repertoire? </label></p>
<p class="description">All repertoire media will be shown on the This Season page and included in the podcast.</p>

<?php
}

/* -------------------------------------------------------- */

function repertoire_media_exists($post_id) {
	//echo get_attached_file( $post_id );
	if (!is_file(get_attached_file( $post_id ))) {
		return ' <span class="file_missing" title="File missing">!</span>';
	}
}
/*function get_repertoire_attached_file( $attachment_id, $unfiltered = false ) {
	$file = get_post_meta( $attachment_id, '_wp_attached_file', true );
	$imp = new ImportRepertoireMedia(false);
	
	// If the file is relative, prepend upload dir
	if ( $file && 0 !== strpos($file, '/') && !preg_match('|^.:\\\|', $file) && ( ($uploads = $imp->force_upload_dir()) && false === $uploads['error'] ) )
		$file = $uploads['basedir'] . "/$file";
	if ( $unfiltered )
		return $file;
	return apply_filters( 'get_attached_file', $file, $attachment_id );
	}*/

function set_repertoire_media_list() {
	global $post;
	$args = array(
		'numberposts' => -1,
		'orderby' => 'name',
		'post_type' => 'repertoire-media',
		'post_parent' => $post->ID
	);
	$medias = get_posts($args);
	
	echo '<div id="related-media" class="like-categorydiv">
<div id="related-media-all" class="like-tabs-panel">';
	
	echo '<table class="media-list">';
	/*echo '<tr>
		<th>Media</th>
		<!--<th>Type</th>
		<th>Part</th>
		<th>Concert</th>-->
		</tr>';*/
	
	foreach ($medias as $media) {
		echo '<tr>';
		
		$title = $media->post_title;
		
		echo '<td>';
		edit_post_link($title, '', '', $media->ID);
		//echo repertoire_media_exists($media->ID);
		echo '</td>';
		
		//echo '<td>' . implode(', ', wp_get_post_terms($media->ID, 'media-type', array("fields" => "names"))) . '</td>';
		//echo '<td>' . implode(', ', wp_get_post_terms($media->ID, 'part', array("fields" => "names"))) . '</td>';
		//echo '<td>' . implode(', ', wp_get_post_terms($media->ID, 'concert', array("fields" => "names"))) . '</td>';
		
		echo '</tr>';
	}
	echo '</table>';
	
	echo '</div></div>';
	/*<div id="related-media-adder">
<h4><a id="media-add-toggle" href="#media-add">+ Add New media</a></h4>
</div>
</div>';*/
}

function set_repertoire_media_preview() {
	global $post;
	$meta = get_post_meta($post->ID);
	print_pre($post);
	print_pre($meta);
	if ($post->post_status == 'auto-draft') {
		echo '<p>You must save this media before it can be previewed.</p>';
	}
?>

<?php
}

function set_repertoire_media_title() {
	global $post;
	$not_exists = repertoire_media_exists($post->ID);
	echo '<div id="titlediv" style="padding-left:0;">
<div id="titlewrap"><span id="title" style="border:none;background:0;padding-left:0;">'.$post->post_title.$not_exists.'</span></div>';
	if (false && $post->post_status == 'publish') {
		echo '<div id="edit-slug-box" style="padding-left:0;">
<strong>Permalink:</strong>
<span id="sample-permalink">'.get_permalink($post->ID).'</span>&lrm;
</div>';
	}
	echo '</div>';
}

function set_repertoire_media_file() {
	global $post;
	$custom = get_post_custom($post->ID);
	$file = repertoire_get_attachment_url($post->ID);
	if ($post->post_status == 'auto-draft') {
		echo '<p>You must save this media before it can be previewed.</p>';
	}
	else {
?>
<div id="minor-publishing-rep">
	<?php $filepath = get_attached_file( $post->ID ); ?>
	<div id="misc-publishing-actions-rep">
		<?php require_once('/home/lowrez/_prod/getmime/getmime.php'); ?>
		<div class="misc-pub-section">
			<label for="attachment_path">File path:</label>
			<textarea type="text" rows="4" name="attachment_path" readonly="readonly" class="widefat urlfield"><?php echo $filepath; ?></textarea>
		</div>
		<div class="misc-pub-section">
			<label for="attachment_url">File URL:</label>
			<textarea type="text" rows="4" name="attachment_url" readonly="readonly" class="widefat urlfield"><?php echo $file; ?></textarea>
		</div>
		<div class="misc-pub-section">
			<label for="attachment_filename">File name:</label>
			<textarea type="text" rows="2" name="attachment_filename" readonly="readonly" class="widefat urlfield"><?php echo basename($file); ?></textarea>
		</div>
		<div class="misc-pub-section">
			File type: <strong><?php echo get_post_mime_type($post->ID); ?></strong>
		</div>
		<div class="misc-pub-section">
			File size: <strong><?php echo getMIME::size_readable(filesize($filepath)); ?></strong>
		</div>
		<div class="misc-pub-section">
			Last modified: <strong><?php echo date('M j, Y @ H:i', filemtime($filepath)); ?></strong>
		</div>
		
	</div>
</div>
<div id="major-publishing-actions-rep">
	<!--<div id="delete-action">
<a href="#" class="submitdelete deletion">Delete Permanently</a></div>-->
	
	<div id="publishing-action-rep">
		<span class="spinner"></span>
		<a class="button" href="<?php echo $file; ?>" target="_blank">Download</a>
	</div>
	<div class="clear"></div>
</div>
<?php
		 }
}

function set_repertoire_media_metadata() {
	global $post;
	$custom = get_post_custom($post->ID);
	
	if ($post->post_status == 'auto-draft') {
		echo '<p class="description">You must save this media before it can be previewed.</p>';
	}
	else {
?>
<div id="minor-publishing-rep">
	<div id="misc-publishing-actions-rep">
		 
		<?php
		  
		  switch (get_post_mime_type($post->ID)) {
			  case 'audio/mpeg':
			  case 'audio/mp3':
			  $fields = array(
				  'podcast_title' => 'Title',
				  //'original_artist' => 'Original Artist',
				  'podcast_album' => 'Album',
				  'podcast_artist' => 'Artist',
				  'podcast_album_artist' => 'Album Artist',
				  'podcast_duration' => 'Duration',
				  );
			  break;
			  case 'application/pdf':
			  break;
			  default:
		  }
		  
		if ($fields) {
			$album_cover = $custom['podcast_album_cover'][0];
			
			/*$performers = wp_get_object_terms($post->post_parent, 'performer');
			$performer_dropdown = false;
			
			if (count($performers)>1) { 
				$performer_dropdown = "<select name='primary_performer' id='primary_performer'>";
				foreach ($performers as $term) {
					$performer_dropdown .= sprintf('<option value="%s"%s>%s</option>', $term->term_id, selected($custom['primary_performer'][0], $term->term_id, false), $term->name);
				}
				$performer_dropdown .= "</select>";
			}*/
			
			foreach ($fields as $meta_key => $label) {
				$i++;
				$last = !empty($albumcover) && $i == count($fields) ? ' style="border-bottom:none;"' : false;
				
				/*if ($meta_key == 'original_artist' && $performer_dropdown) {
					$field = $performer_dropdown;
				}
				else {*/
					$field = $custom[$meta_key][0];
				/*}*/
				
				printf('<div class="misc-pub-section"%s>%s: <strong>%s</strong></div>', $last, $label, $field);
			}
			
			if ($album_cover) {
				$album_cover = str_replace('/home/lowrez/_prod/podcast/albumcover/', 'http://podcast.lowrez.com.au/album/', $album_cover);
				printf('<div class="misc-pub-section" style="text-align:center;border-bottom:none;"><img src="%s" style="max-width:100%%;" /></div>', $album_cover);
			}
			
		}
		else {
			echo '<div class="misc-pub-section" style="border-bottom:none;"><p class="description">No metadata can be read for this file.</p></div>';
		}
		
		  ?>
		
	</div>
</div>
<?php
		 }
}

function set_repertoire_media_meta() {
	global $post;
	$custom = get_post_custom($post->ID);
	$parent_id = $custom['parent_id'][0];
?>
<table class="repertoire-custom repertoire-media-custom-meta">
	<tr>
		<td colspan="3">
			<label for="parent_id">Repertoire:</label>
			<?php wp_dropdown_repertoire('parent_id', $post->post_parent); ?> <a href="<?php echo admin_url('post-new.php?post_type=repertoire') ?>" class="button" target="_blank">Add New</a>
			<?php if ($post->post_parent) { ?>
			<a id="view_parent_repertoire" href="<?php echo admin_url('post.php?action=edit&post='.$post->post_parent ) ?>" class="button">Edit Repertoire</a>
			<?php
		$performers = wp_get_object_terms($post->post_parent, 'performer');
										   
										   if (count($performers)>1) { 
											   $performer_dropdown = "<select name='primary_performer' id='primary_performer'>";
											   foreach ($performers as $term) {
												   $performer_dropdown .= sprintf('<option value="%s"%s>%s</option>', $term->term_id, selected($custom['primary_performer'][0], $term->term_id, false), $term->name);
											   }
											   $performer_dropdown .= "</select>";
											   
											   echo "</td></tr><tr id='primary_performer_tr'><td colspan='3'><label for='primary_performer'>Original Performer:</label> $performer_dropdown";
										   } ?>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<th style="padding:0;"><h3>Media Type</h3></th>
		<th style="padding:0;"><h3>Part</h3></th>
		<th style="padding:0;"><h3>Concert</h3></th>
	</tr>
	<tr>
		<td><?php $box = array('args' => array('taxonomy' => 'media-type')); post_taxonomy_meta_box($post, $box); ?>
		</td>
		<td><?php $box = array('args' => array('taxonomy' => 'part')); post_taxonomy_meta_box($post, $box); ?></td>
		<td><?php $box = array('args' => array('taxonomy' => 'concert')); post_taxonomy_meta_box($post, $box); ?></td>
	</tr>
</table>
<?php
	echo '<input type="hidden" name="repertoire_media_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}

// Save the meta data
function save_repertoire_media_meta($post_id) {
	
	global $post;
	
	// make sure data came from our meta box

	
	if (@$_POST['post_type'] == "repertoire-media") {
		if (!wp_verify_nonce($_POST['repertoire_media_noncename'],__FILE__)) return $post_id;
		
		if ($taxons = @$_POST['tax_input']['part']) {
			foreach ($taxons as $term_id) {
				$term = get_term( $term_id, 'part' );
				
				if ($term->parent &&
					preg_match('/(?:[0-9]|-)[a-z]$/i', $term->slug) &&
					!in_array($term->parent, $taxons)) {
					
					wp_set_object_terms( $post_id, (int) $term->parent, 'part', true );
				}
			}
		}
		if ($taxons = @$_POST['tax_input']['concert']) {
			foreach ($taxons as $term_id) {
				$term = get_term( $term_id, 'concert' );
				
				if ($term->parent && !in_array($term->parent, $taxons)) {
					wp_set_object_terms( $post_id, (int) $term->parent, 'concert', true );
				}
			}
		}
		
		$meta_keys = array('parent_id', 'primary_performer');
		
		foreach ($meta_keys as $meta_key) {
			if ($_POST[$meta_key]) {
				update_post_meta($post_id, $meta_key, $_POST[$meta_key]);
			}
			else {
				delete_post_meta($post_id, $meta_key);
			}
		}
		
	}
	elseif (@$_POST['post_type'] == "repertoire") {
		if (!wp_verify_nonce($_POST['repertoire_noncename'],__FILE__)) return $post_id;
		
		$taxons = $_POST['tax_input'];
		
		//print_pre($_POST);
		//wp_die();
		
		$meta_keys = array('copyright_notice', 'song_year', 'arrangement_year', 'repertoire-this-season', 'repertoire-this-season-sup');
		
		foreach ($meta_keys as $meta_key) {
			if ($_POST[$meta_key]) {
				update_post_meta($post_id, $meta_key, $_POST[$meta_key]);
			}
			else {
				delete_post_meta($post_id, $meta_key);
			}
		}
	
		
		foreach ($taxons as $taxon => &$terms) {
			foreach ($terms as &$term) {
				$term = get_term($term, $taxon);
				$term = $term->name;
			}
		}
		
		$_POST['tax_input'] = $taxons;
		
		global $wpdb;
		
		$post_modified = current_time( 'mysql' );
		$post_modified_gmt = current_time( 'mysql', 1 );
		$wpdb->update($wpdb->posts,
					  array(
						  'post_modified' => $post_modified,
						  'post_modified_gmt' => $post_modified_gmt
					  ),
					  array('post_parent' => $post_id)
					 );
		
	}
}
add_action("save_post", "save_repertoire_media_meta");


/*----------------------------------------------------*/

add_action('admin_footer', 'repertoire_media_select_url');

function repertoire_media_select_url() {
  global $current_screen;
  if (($current_screen->id != 'repertoire-media')) return;
  
?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.urlfield').on('focus', function() {
			jQuery(this).select();
		}).on('mouseup', function(e) {
			e.preventDefault();
		});

		jQuery('#primary_performer').detach().appendTo(jQuery('li#media-type-298'));
		if (!jQuery(this).is(':checked')) {
			jQuery('#primary_performer').hide();
		} 
		
		jQuery('#primary_performer_tr').remove();
		
		jQuery('#in-media-type-298').on('change', function() {
			if (jQuery(this).is(':checked')) {
				jQuery('#primary_performer').fadeIn();
			} else {
				jQuery('#primary_performer').fadeOut();
			}
		});
	});
</script>
<?php
}

add_action( 'admin_print_styles-post.php',     'repertoire_css');
add_action( 'admin_print_styles-post-new.php', 'repertoire_css');

function repertoire_css() {
	global $post_type;
	if( !in_array($post_type, array('repertoire-media', 'repertoire') ) ) return;
?>
<style type="text/css">
	
	<?php if ($post_type == 'repertoire-media') echo '#post-body-content { display:none; }'; ?>
	
	#poststuff #repertoire_meta .inside,
	#poststuff #repertoire_media_meta .inside,
	#poststuff #repertoire_media_metadata .inside,
	/*#poststuff #repertoire_media_list .inside,*/
	#poststuff #repertoire_media_file .inside  {
		/*margin-left:0;
		margin-right:0;
		padding-left:0;
		padding-right:0;*/
		margin:0;
		padding:0;
	}
	
	.like-tabs-panel {
		padding:0;
		overflow-y:auto;
		overflow-x:hidden;
		min-height: 42px;
		max-height: 200px;
		border-style: solid;
		border-width: 1px;
		border-color: #dfdfdf;
		background-color: #fff;
	}
	
	#repertoire_media_title {
		-webkit-box-shadow: none !important;
		-moz-box-shadow: none !important;
		box-shadow: none !important;
	}
	
	textarea[readonly] {
		background-color: #eee !important;
	}
	
	textarea {
		resize: vertical;
	}
	
	.labellike {
		margin: 1px 0 0;
		padding: 3px 0 0;
		display: block;
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
	
	#repertoire_media_meta .tabs-panel {
		height: 200px;
	}
	
	#postimagediv img {
		max-width:220px;
		max-height:220px;
	}
	
	table.repertoire-custom select {
		max-width:300px;
		min-width: 180px;
	}
	
	table.repertoire-custom.repertoire-custom-meta th,
	table.repertoire-custom.repertoire-custom-meta td {
		width:25%;
	}
	
	table.repertoire-custom.repertoire-media-custom-meta th,
	table.repertoire-custom.repertoire-media-custom-meta td {
		width:33%;
	}
	
	table.repertoire-custom {
		border-collapse: separate;
		border-spacing: 0px;
		width:100%;
		border-top: none;/*1px solid #dfdfdf;*/
		border-bottom: none; /*1px solid #fff;*/
	}
	
	table.repertoire-custom th {
		/*width:150px;*/
	}
	
	table.media-list th, 
	table.media-list td {
		text-align: left;
		vertical-align: top;
		padding: 6px 10px;
		border-bottom: 1px solid #dfdfdf;
	}
	
	table.media-list tr:last-child td {
		border-bottom: none;
	}
	
	table.repertoire-custom th,
	table.repertoire-custom td {
		height: 24px;
		text-align:left;
		vertical-align:top;
		padding: 6px 10px 8px;
		border-top: 1px solid #fff;
		border-bottom: 1px solid #dfdfdf;
	}
	table.repertoire-custom tr:first-child th,
	table.repertoire-custom tr:first-child td {
		border-top:none;
	}
	table.repertoire-custom tr:last-child th,
	table.repertoire-custom tr:last-child td {
		border-bottom:none;
	}
	
	#repertoire_media_title .postbox .inside {
		padding:0;
	}
	
	#repertoire_media_title {
		background: none repeat scroll 0 0 transparent;
		border: medium none;
	}
	#repertoire_media_title .hndle, #repertoire_media_title .handlediv {
		display: none;
	}
	
	#publishing-action-rep {
		float: right;
		line-height: 23px;
		text-align: right;
	}
	
	#major-publishing-actions-rep {
		border-top: 1px solid #F5F5F5;
		clear: both;
		margin-top: -2px;
		padding: 10px 10px 8px;
	}
	#minor-publishing-actions-rep {
		padding: 10px 10px 2px 8px;
		text-align: right;
	}
	#misc-publishing-actions-rep label {
		vertical-align: baseline;
	}
	#misc-publishing-actions-rep {
		padding: 6px 0 0;
	}
	#minor-publishing-rep {
		border-bottom-color: #DFDFDF;
		border-bottom-style: solid;
		border-bottom-width: 1px;
		box-shadow: 0 1px 0 #FFFFFF;
	}
	
	#minor-publishing-actions-rep input, #major-publishing-actions-rep input, #minor-publishing-actions-rep .preview {
		text-align: center;
	}
	
	#set-post-thumbnail, #postimagediv img {
		display: block;
		margin: 0 auto;
	}
	
</style>
<?php
	
}

/*----------------------------------------------------*/

function wp_dropdown_posts($select_id, $post_type, $selected = 0) {
	$post_type_object = get_post_type_object($post_type);
	$label = $post_type_object->label;
	$posts = get_posts(array('post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1, 'orderby'=>'title', 'order'=>'ASC'));
	echo '<select name="'. $select_id .'" id="'.$select_id.'">';
	//echo '<option value = "" >All '.$label.' </option>';
	echo '<option value = "" ></option>';
	foreach ($posts as $post) {
		echo '<option value="', $post->ID, '"', $selected == $post->ID ? ' selected="selected"' : '', '>', $post->post_title, '</option>';
	}
	echo '</select>';
}

function wp_dropdown_repertoire($select_id, $selected = 0) {
	$post_type = 'repertoire';
	$post_type_object = get_post_type_object($post_type);
	$label = $post_type_object->label;
	$posts = get_posts(array('post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1, 'orderby'=>'title', 'order'=>'ASC'));
	echo '<select name="'. $select_id .'" id="'.$select_id.'">';
	//echo '<option value = "" >All '.$label.' </option>';
	echo '<option value = "" ></option>';
	foreach ($posts as $post) {
		$performer = implode(', ', wp_get_post_terms($post->ID, 'performer', array('fields' => 'names')));
		echo '<option value="', $post->ID, '"', $selected == $post->ID ? ' selected="selected"' : '', '>', ($post->post_title . ' - ' . $performer), '</option>';
	}
	echo '</select>';
}

function wp_dropdown_media_types($selected=false) {
	
	$args = array('selected' => $selected);
	$args['taxonomy'] = 'media-type';
	$args['name'] = 'taxonomy-media-type';
	$args['id'] = 'taxonomy-media-type';
	$args['hide_empty'] = 0;
	$args['hierarchical'] = 1;
	$args['show_option_none'] = ' ';
	$args['orderby'] = 'name';
	$args['order'] = 'ASC';
	
	wp_dropdown_categories($args);
	
}

function wp_dropdown_parts($selected=false) {
	
	$args = array('selected' => $selected);
	$args['taxonomy'] = 'part';
	$args['name'] = 'taxonomy-part';
	$args['id'] = 'taxonomy-part';
	$args['hide_empty'] = 0;
	$args['hierarchical'] = 1;
	$args['show_option_none'] = ' ';
	
	wp_dropdown_categories($args);
}

function wp_dropdown_concerts($selected=false) {
	
	$args = array('selected' => $selected);
	$args['taxonomy'] = 'concert';
	$args['name'] = 'taxonomy-concert';
	$args['id'] = 'taxonomy-concert';
	$args['hide_empty'] = 0;
	$args['hierarchical'] = 1;
	$args['show_option_none'] = ' ';
	$args['orderby'] = 'name';
	$args['order'] = 'ASC';
	
	wp_dropdown_categories($args);
}




function post_taxonomy_meta_box( $post, $box ) {
	$defaults = array('taxonomy' => 'category');
	if ( !isset($box['args']) || !is_array($box['args']) )
		$args = array();
	else
		$args = $box['args'];
	extract( wp_parse_args($args, $defaults), EXTR_SKIP );
	$tax = get_taxonomy($taxonomy);
	
?>
<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
	
	<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
		<?php
	$name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
	echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
		?>
		<ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists="list:<?php echo $taxonomy?>" class="categorychecklist form-no-clear">
			<?php wp_terms_checklist($post->ID, array( 'taxonomy' => $taxonomy, 'popular_cats' => $popular_ids ) ) ?>
		</ul>
	</div>
	<?php if ( current_user_can($tax->cap->edit_terms) ) : ?>
	<div id="<?php echo $taxonomy; ?>-adder" class="wp-hidden-children">
		<h4>
			<a id="<?php echo $taxonomy; ?>-add-toggle" href="#<?php echo $taxonomy; ?>-add" class="hide-if-no-js">
				<?php
	/* translators: %s: add new taxonomy label */
	printf( __( '+ %s' ), $tax->labels->add_new_item );
				?>
			</a>
		</h4>
		<p id="<?php echo $taxonomy; ?>-add" class="category-add wp-hidden-child">
			<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
			<input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $tax->labels->new_item_name ); ?>" aria-required="true"/>
			<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>_parent">
				<?php echo $tax->labels->parent_item_colon; ?>
			</label>
			<?php wp_dropdown_categories( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => 'new'.$taxonomy.'_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => '&mdash; ' . $tax->labels->parent_item . ' &mdash;' ) ); ?>
			<input type="button" id="<?php echo $taxonomy; ?>-add-submit" data-wp-lists="add:<?php echo $taxonomy ?>checklist:<?php echo $taxonomy ?>-add" class="button category-add-submit" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" />
			<?php wp_nonce_field( 'add-'.$taxonomy, '_ajax_nonce-add-'.$taxonomy, false ); ?>
			<span id="<?php echo $taxonomy; ?>-ajax-response"></span>
		</p>
	</div>
	<?php endif; ?>
</div>
<?php
}
