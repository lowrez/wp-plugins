<?php

include_once('repertoire-looper.php');

function podcast_updated($podcast, $voicepart) {
	$last_cached = get_option('podcast_cache_'.$podcast);
}

function put_lowrez_podcasts() {
	
	global $current_user;
	
	//if (!in_array($current_user->id, array(1, 4))) return false; //FIXME
	
	
	$voicepart = get_user_meta($current_user->ID, 'voicepart', true);
	if ($current_user->id == 1) $voicepart = 't2';
	$voicepart_name = format_voicepart($voicepart);
	
	$user_pref_podcast = get_user_meta($current_user->ID, 'user_pref_podcast', true);
	
	$voiceparts = array('tenor-1' => 'Tenor 1','tenor-2' => 'Tenor 2','baritone'=>'Baritone','bass'=>'Bass','solo'=>'Solo (various)');
	
	$pbg = plugin_dir_url(__FILE__);
	
	$lowrez_podcast = 'podcast.lowrez.com.au';
	
	$podcasts = array(
		'shared' => array(
			'sheet-music' => array(
				'title' => 'Sheet Music',
				'link' => 'sheet-music',
				'bglink' => 'pbg-sheet'
			),
			'original-recording' => array(
				'title' => 'Original Recordings',
				'link' => 'original-recording',
				'bglink' => 'pbg-recording'
			),
		),
		'voicepart' => array(
			'practice' => array(
				'title' => 'Practice Tracks',
				'link' => 'practice',
				'bglink' => 'pbg-practice'
			),
			'singalong' => array(
				'title' => 'Singalongs',
				'link' => 'singalong',
				'bglink' => 'pbg-singalong'
			),
		)
	);
	
?>

<?php
	//$the_slug = 'this-season';
	/*$args=array(
	'name' => $the_slug,
	'post_type' => 'page',
	'post_status' => 'publish',
	'numberposts' => 1
	);
	$my_posts = get_posts($args);
	if( $my_posts ) {
	
	$content = $my_posts[0]->post_content;
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	
	echo $content;
	}*/
	
	//$rep_media = new ImportRepertoireMedia();
	
	$fakenew = false;
	$fakenewbox = '<span class="label label-warning" style="float:right;margin-top:1px;margin-left:1px;" title="Posted since your last login">New</span>';
	
?>

<ul class="nav nav-tabs" id="repertoire-tabs">
	<li<?php if ($user_pref_podcast!='download') echo ' class="active"'; ?>><a href="#repertoire-podcast" data-toggle="tab"><i class="iconnew-podcast" style="line-height:80%;">&nbsp;</i>Podcast</a></li>
	<li<?php if ($user_pref_podcast=='download') echo ' class="active"'; ?>><a href="#repertoire-download" data-toggle="tab"><i class="icon-download-alt">&nbsp;</i>Download</a></li>
</ul>

<div class="tab-content">
	<div class="tab-pane<?php if ($user_pref_podcast!='download') echo ' active'; ?>" id="repertoire-podcast">
		<p>The podcasts will automatically send new music to your computer and iPad if you have set it up in iTunes. If you would rather download the music manually, go to the <strong>Download</strong> tab.</p>
		<p>To subscribe to the podcasts, add the links to your podcast subscriptions in iTunes or another podcast or RSS feed reader. You can subscribe to the sheet music, singalongs, practice tracks (no singing) or any combination you prefer.</p>
		<div id="podcasts-accordion">
			<?php if ($voicepart) { ?>
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#podcasts-accordion" href="#podcasts-mine">
						<strong>My Podcasts</strong>
					</a>
				</div>
				<div id="podcasts-mine" class="accordion-body collapse in">
					<div class="accordion-inner">
						<p>These are the podcasts most relevant to your voice part. If you need to subscribe to other voice parts, click <strong>All Podcasts</strong> below.</p>
						
						
						<div class="row-fluid">
							<?php
		
		
		
		$width = min(4, round(12 / (count($podcasts['shared']) + count($podcasts['voicepart'])), 0));
		
		$voicepart_slug = format_voicepart($voicepart, false, 'slug');
		
		foreach ($podcasts['shared'] as $p) {
			
			//$cacheterm = $p['link'];
			//$last_cached = get_option('podcast_cache_'.$cacheterm);
			
			//$fakenew = '';
			
			if (IS_MOBILE) {
				
				echo "<div class='span{$width}'><table class='table table-bordered table-hover'>
<tbody>
<tr>
<th style='background:url({$pbg}{$p['bglink']}.png) no-repeat center;background-size:cover;padding:0;height:75px;'>
<div style='background:url({$pbg}repbg.png) rgba(255, 255, 255, 0.3);padding:8px;height:51px;border: 4px solid rgb(255, 255, 255);'>
$fakenew{$p['title']}
</div></th>
<td style='width:60px;'><a href='http://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}' target='_blank'>Subscribe</a></td>
</tr>
</tbody>
</table>
</div>";
				
			}
			else
			{
				echo "<div class='span{$width}'><table class='table table-bordered table-hover'>
<tbody>
<tr>
<th rowspan='2' style='background:url({$pbg}{$p['bglink']}.png) no-repeat center;background-size:cover;padding:0;height:75px;'>
<div style='background:url({$pbg}repbg.png) rgba(255, 255, 255, 0.3);padding:8px;height:51px;border: 4px solid rgb(255, 255, 255);'>
$fakenew{$p['title']}
</div></th>
<td style='width:60px;'><a href='http://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}' target='_blank'><i class='icon-rss'>&nbsp;</i>RSS</a></td>
</tr><tr>
<td style='width:60px;'><a href='itpc://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}'>iTunes</a></td>
</tr>
</tbody>
</table>
</div>";
			}
		}
		foreach ($podcasts['voicepart'] as $p) {
			if (IS_MOBILE) {
				echo "<div class='span{$width}'><table class='table table-bordered table-hover'>
<tbody>
<tr>
<th style='background:url({$pbg}{$p['bglink']}.png) no-repeat center;background-size:cover;padding:0;height:75px;'>
<div style='background:url({$pbg}repbg.png) rgba(255, 255, 255, 0.3);padding:8px;height:51px;border: 4px solid rgb(255, 255, 255);'>
$fakenew{$voicepart_name} {$p['title']}
</div></th>
<td style='width:60px;'><a href='http://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}/$voicepart_slug' target='_blank'>Subscribe</a></td>
</tr>
</tbody>
</table>
</div>";
			}
			else 
			{
				echo "<div class='span{$width}'><table class='table table-bordered table-hover'>
<tbody>
<tr>
<th rowspan='2' style='background:url({$pbg}{$p['bglink']}.png) no-repeat center;background-size:cover;padding:0;height:75px;'>
<div style='background:url({$pbg}repbg.png) rgba(255, 255, 255, 0.3);padding:8px;height:51px;border: 4px solid rgb(255, 255, 255);'>
$fakenew{$voicepart_name} {$p['title']}
</div></th>
<td style='width:60px;'><a href='http://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}/$voicepart_slug' target='_blank'><i class='icon-rss'>&nbsp;</i>RSS</a></td>
</tr><tr>
<td style='width:60px;'><a href='itpc://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}/$voicepart_slug'>iTunes</a></td>
</tr>
</tbody>
</table>
</div>";
			}
		}
		
							?>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#podcasts-accordion" href="#podcasts-all">
						<strong>All Podcasts</strong>
					</a>
				</div>
				<div id="podcasts-all" class="accordion-body collapse<?php echo !$voicepart ? ' in' : false; ?>">
					<div class="accordion-inner">
						<p>These are the podcasts for all voice parts.</p>
						<div class="row-fluid">
							<div class="span6">
								<table class="table table-bordered table-hover">
									<thead>
										<tr>
											<th colspan="3" style="width:150px;">Podcast</th>
										</tr>
									</thead>
									<tbody>
										<?php
	foreach ($podcasts['shared'] as $p) {
		if (IS_MOBILE) {
			echo "<tr>
<th>$fakenew{$p['title']}</th>
<td colspan='2' style='width:60px;'><a href='http://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}' target='_blank'>Subscribe</a></td>
</tr>";
		}
		else
		{
			echo "<tr>
<th>$fakenew{$p['title']}</th>
<td style='width:60px;'><a href='http://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}' target='_blank'><i class='icon-rss'>&nbsp;</i>RSS</a></td>
<td style='width:60px;'><a href='itpc://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}'>iTunes</a></td>
</tr>";
		}
	}
										?>
									</tbody>
								</table>
							</div>
							<div class="span6">
								<table class="table table-bordered table-hover">
									<thead>
										<tr>
											<th style="width:150px;">Voice Part</th>
											<?php
	foreach ($podcasts['voicepart'] as $p) {
		echo "<th colspan='2'>{$p['title']}</th>";
	}
											?>
										</tr>
									</thead>
									<tbody>
										<?php
	foreach ($voiceparts as $v => $vpart) {
		echo "<tr>
<th>$fakenew$vpart</th>";
		
		foreach ($podcasts['voicepart'] as $p) {
			if (IS_MOBILE) {
				echo "
<td colspan='2' style='width:60px;'><a href='http://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}/$v' target='_blank'>Subscribe</a></td>";
			}
			else
			{
				echo "
<td style='width:60px;'><a href='http://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}/$v' target='_blank'><i class='icon-rss'>&nbsp;</i>RSS</a></td>
<td style='width:60px;'><a href='itpc://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}/$v'>iTunes</a></td>";
			}
		}
		
		echo "</tr>";
	}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	
	$args = array( 
		'post_type' => 'repertoire',  
		'post_status' => 'publish',
		'nopaging' => true,
		'order' => 'ASC',
		'orderby' => 'title',
		'meta_query' => array(
			array(
				'key' => 'repertoire-this-season',
				'value' => 'include',
			)
		)
	);
	
	$all_reps = get_posts($args);
	
	$m = false;//IS_MOBILE;
	
	if (!$m) {
		$all_reps = array_chunk($all_reps, ceil(count($all_reps)/3));
	}
	else {
		$all_reps = array($all_reps);
	}
	
	?>
	<div class="tab-pane<?php if ($user_pref_podcast=='download') echo ' active'; ?>" id="repertoire-download">
		
		<p>If you cannot subscribe to a podcast or RSS feed, you can download the music individually. If you would rather subscribe to download the music automatically, go to the <strong>Podcast</strong> tab.</p>
		<div id="repertoire-accordion">
			<?php if ($voicepart) { ?>
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#repertoire-accordion" href="#repertoire-mine">
						<strong>My Repertoire</strong>
					</a>
				</div>
				<div id="repertoire-mine" class="accordion-body collapse<?php echo $voicepart ? ' in' : false; ?>">
					<div class="accordion-inner">
						<p>Repertoire will continue to be added as it becomes available.</p>
						<div class="row-fluid">
							<?php
								   $nohead = false;
								   foreach ($all_reps as $reps) {
							?>
							<div class="span4">
								<table class="table repertoire-table table-bordered table-hover">
									<thead class="<?php if ($nohead) echo 'no-head-mobile'; ?>">
										<th>Song</th>
										<th>Downloads</th>
									</thead>
									<tbody>
										<?php
									   $voicepart_slug = format_voicepart($voicepart, false, 'slug');
									   
									   foreach ($reps as $rep) {
										   echo '<tr>';
										   rcell_repertoire($rep);
										   echo '<td><ul class="unstyled">';
										   rcell_media($rep, 'sheet-music', false, false, true);
										   rcell_media($rep, 'notes', false, false, true, true, true, false);
										   if ($rep->ID == 2898) echo '<li><span style="float:right;margin-top:1px;margin-left:1px;" title="Posted since your last login" class="label label-warning">New</span>
										   <a href="http://www.lowrez.com.au/members/midnight-train-voice-part-groups/">Guide to Voice Part Groups</a></li>';
										   //echo '</tr><tr>';
										   rcell_media($rep, 'practice-track', $voicepart_slug, false, true);
										   //echo '</tr><tr>';
										   rcell_media($rep, 'singalong',  $voicepart_slug, false, true);
										   echo '</ul></td>';
										   echo '</tr>';
									   }
									   
										?>
									</tbody>
								</table>
							</div>
							<?php $nohead = true;
								   } ?>
						</div>
						
						
						<?php
								   
								   $args = array( 
									   'post_type' => 'repertoire',  
									   'post_status' => 'publish',
									   'nopaging' => true,
									   'order' => 'ASC',
									   'orderby' => 'title',
									   'meta_query' => array(
										   array(
											   'key' => 'repertoire-this-season-sup',
											   'value' => 'include',
										   )
									   )
								   );
								   
								   $all_reps_sup = get_posts($args);
								   
								   if (!$m) {
									   $all_reps_sup = array_chunk($all_reps_sup, ceil(count($all_reps_sup)/3));
								   }
								   else {
									   $all_reps_sup = array($all_reps_sup);
								   }
								   
								   
								   if (count($all_reps_sup)) {
						?>
						
						<h4>Supplementary Repertoire</h4>
						<p>These are songs from past seasons which we are keeping in the current mix.</p>
						<div class="row-fluid">
							<?php
									   $nohead = false;
									   foreach ($all_reps_sup as $reps) {
							?>
							<div class="span4">
								<table class="table repertoire-table table-bordered table-hover">
									<thead class="<?php if ($nohead) echo 'no-head-mobile'; ?>">
										<th>Song</th>
										<th>Downloads</th>
									</thead>
									<tbody>
										<?php
										   $voicepart_slug = format_voicepart($voicepart, false, 'slug');
										   
										   foreach ($reps as $rep) {
											   echo '<tr>';
											   rcell_repertoire($rep);
											   echo '<td><ul class="unstyled">';
											   rcell_media($rep, 'sheet-music', false, false, true);
											   rcell_media($rep, 'notes', false, false, true, true, true, false);
											   //echo '</tr><tr>';
											   rcell_media($rep, 'practice-track', $voicepart_slug, false, true);
											   //echo '</tr><tr>';
											   rcell_media($rep, 'singalong',  $voicepart_slug, false, true);
											   echo '</ul></td>';
											   echo '</tr>';
										   }
										   
										?>
									</tbody>
								</table>
							</div>
							<?php $nohead = true;
									   } 
								   } ?>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#repertoire-accordion" href="#repertoire-all">
						<strong>All Repertoire</strong>
					</a>
				</div>
				<div id="repertoire-all" class="accordion-body collapse<?php echo !$voicepart ? ' in' : false; ?>">
					<div class="accordion-inner">
						<p>Repertoire will continue to be added as it becomes available.</p>
						<table class="table repertoire-table table-bordered table-hover">
							<thead>
								<th>Song</th>
								
								<?php
	
	if (!$m) { 
								?>
								<th>Sheet&nbsp;Music</th>
								<th>Practice&nbsp;Track</th>
								<th>Singalong</th>
								<?php
	}
	else {
								?>
								<th>Downloads</th>
								<?php
	}
								?>
							</thead>
							<tbody>
								<?php
	foreach ($all_reps as $reps) {
		foreach ($reps as $rep) {
			echo '<tr>';
			rcell_repertoire($rep, $m ? 3 : 1);
			echo '<td><ul class="unstyled">';
			rcell_media($rep, 'sheet-music', false, false, true, true, true);
			rcell_media($rep, 'notes', false, false, true, true, true, false);
			if ($rep->ID == 2898) echo '<li><span style="float:right;margin-top:1px;margin-left:1px;" title="Posted since your last login" class="label label-warning">New</span>
										   <a href="http://www.lowrez.com.au/members/midnight-train-voice-part-groups/">Guide to Voice Part Groups</a></li>';
			echo '</ul></td>';
			if ($m) { echo '</tr><tr>';	}
			rcell_media($rep, 'practice-track', false, true);
			if ($m) { echo '</tr><tr>';	}
			rcell_media($rep, 'singalong', false, true);
			echo '</tr>';
		}
	}
	
								?>
							</tbody>
						</table>
						<?php if (count($all_reps_sup)) { ?>
						<h4>Supplementary Repertoire</h4>
						<p>These are songs from past seasons which we are keeping in the current mix.</p>
						<table class="table repertoire-table table-bordered table-hover">
							<thead>
								<th>Song</th>
								
								<?php
									
									if (!$m) { 
								?>
								<th>Sheet&nbsp;Music</th>
								<th>Practice&nbsp;Track</th>
								<th>Singalong</th>
								<?php
									}
									else {
								?>
								<th>Downloads</th>
								<?php
									}
								?>
							</thead>
							<tbody>
								<?php
									foreach ($all_reps_sup as $reps) {
										foreach ($reps as $rep) {
											echo '<tr>';
											rcell_repertoire($rep, $m ? 3 : 1);
											rcell_media($rep, 'sheet-music');
											rcell_media($rep, 'notes', false, false, true, true, true, false);
											if ($m) { echo '</tr><tr>';	}
											rcell_media($rep, 'practice-track', false, true);
											if ($m) { echo '</tr><tr>';	}
											rcell_media($rep, 'singalong', false, true);
											echo '</tr>';
										}
									}
									
								?>
							</tbody>
						</table>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready( function() {
		jQuery('#repertoire-tabs a[data-toggle="tab"]').on('shown', function (e) {
			
			/*console.log(jQuery(e.target).attr('href'));*/
			
			send = {
				action: 'change_user_pref_podcast',
				user_pref_podcast: jQuery(e.target).attr('href')
			};
			
			jQuery.ajax({
				type:"POST",
				url: "/wp-admin/admin-ajax.php",
				data: send,
				success: function(data) {
					/*console.log(data);*/
				}
			});
			
		});
	});
</script>

<?php
	
}

function change_user_pref_podcast() {
	
	if ($user_pref_podcast = $_POST['user_pref_podcast']) {
		
		global $current_user;
		
		$user_pref_podcast = str_replace('#repertoire-', '', $user_pref_podcast);
		update_user_meta($current_user->id, 'user_pref_podcast', $user_pref_podcast);
		//echo get_user_meta($current_user->id, 'user_pref_podcast', true);
	}
	else {
		//echo 'false';
	}
	
	die();
}
add_action('wp_ajax_change_user_pref_podcast', 'change_user_pref_podcast');

function rcell_repertoire($post, $rowspan=false) {
	
	global $post_type;
	$post_type = 'attachment';
	
	$image = wp_get_attachment_image_src(get_post_meta( $post->ID, '_thumbnail_id', true ));
	$pbg = plugin_dir_url(__FILE__).'repbg.png';
	
	$bgsize = '120px';//$rowspan ? '120px' : 'cover' ;
	
?>
<td <?php echo $rowspan ? " rowspan='$rowspan'" : ''; ?> style="line-height:110%;padding:0;width:120px;height:120px;background:url(<?php echo $image[0]; ?>) no-repeat center top;background-size:<?php echo $bgsize; ?>;"><!--120px-->
	<div style="background:url(<?php echo $pbg; ?>) rgba(255, 255, 255, 0.3);padding:8px;height:96px;border: 4px solid rgb(255, 255, 255);">
		<a name="<?php echo the_slug($post); ?>" href="<?php echo get_permalink($post); ?>" style="display:block;margin-bottom:0.25em;">
			<strong><em><?php echo $post->post_title; ?></em></strong></a>
		<?php echo implode(',<br>', wp_get_post_terms($post->ID, 'performer', array("fields" => "names"))); ?>
	</div>
</td>
<?php
}

function rcell_media($post, $media_type=false, $part=false, $all=false, $ul=false, $unread=true, $children=false, $na=true) {
	
	global $post_type;
	$post_type = 'repertoire-media';
	
	if (is_object($post)) $post = $post->ID;
	
	
	$args = array( 
		'post_type' => 'repertoire-media',  
		'post_status' => 'publish',
		'nopaging' => true,
		'order' => 'ASC',
		'orderby' => 'title',
		'post_parent' => $post//->ID
	);
	
	if ($media_type) {
		$args['tax_query'][] = array(
			'taxonomy' => 'media-type',
			'field' => 'slug',
			'terms' => $media_type,
			'include_children' => $children
		);
		$media_type = get_term_by('slug', $media_type, 'media-type');
	}
	
	if ($part) {
		$args['tax_query'][] = array(
			'taxonomy' => 'part',
			'field' => 'slug',
			'terms' => $part,
		);
		//$part = get_term_by('slug', $part, 'part');
	}
	
	
	$medias = get_posts( $args );
	
	// . ' '. $media_type->name;
	
?>
<?php if(!$ul) { ?><td><?php } ?>
<?php if (count($medias)) { ?>
<?php if(!$ul) { ?><ul class="unstyled" style="margin-bottom:0;"><?php }
						   
						   $limit = 4;
						   
						   $i = 0;
						   $limited = $limit && count($medias) > $limit + 2;
						   
						   $output = array();
						   $resort = array(
							   'All Parts'=>'s0110',
							   'Ensemble (All Parts)'=>'s0120',
							   'Solo'=>'s1110',
							   'Harmony'=>'s1120',
							   'Ensemble (Solo)'=>'s2010',
							   'Ensemble (Harmony)'=>'s2020',
							   'Ensemble (Tenor 1)'=>'s2110',
							   'Ensemble (Tenor 1, Tenor 2)'=>'s2120',
							   'Ensemble (Tenor 2)'=>'s2210',
							   'Ensemble (Baritone)'=>'s2310',
							   'Ensemble (Baritone, Bass)'=>'s2320',
							   'Ensemble (Bass)'=>'s2410',
							   'Tenor 1'=>'s4110',
							   'Tenor 1A'=>'s4120',
							   'Tenor 1B'=>'s4130',
							   'Tenor 1, Tenor 2'=>'s4200',
							   'Tenor 2'=>'s4210',
							   'Tenor 2A'=>'s4220',
							   'Tenor 2B'=>'s4230',
							   'Baritone'=>'s4310',
							   'Baritone A'=>'s4320',
							   'Baritone B'=>'s4330',
							   'Baritone, Bass'=>'s4400',
							   'Bass'=>'s4410',
							   'Bass A'=>'s4420',
							   'Bass B'=>'s4430',
							   
						   );
						   
?>
<?php foreach ($medias as $media) {
							   $file = repertoire_get_attachment_url($media->ID);
							   $titleend = '';
							   
							   if ($media_type->slug == 'sheet-music') {
								   $title = 'Sheet Music';
							   }
							   elseif ($media_type->slug == 'concert-recording') {
								   $title = get_post_meta($media->ID, 'podcast_album', true);
							   }
							   elseif ($media_type->slug == 'singalong' || $media_type->slug == 'practice-track') {
								   $title = get_post_meta($media->ID, 'podcast_artist', true);
								   if ($part) {
									   $titleend = ' '. $media_type->name;
								   }
							   }
							   elseif ($media_type->slug == 'notes') {
								   $theterms = wp_get_object_terms($media->ID, 'media-type', array('fields' => 'names'));
								   $title = implode(', ', $theterms);
							   }
							   else {
								   $title = $media_type->name;
							   }
							   
							   //							   $over = $limited && $i > $limit + 1 ? ' overlimit' : false;
							   $key = @$resort[$title].$title.$titleend.$i;
							   
							   $markunread = $unread ? is_unread($media->ID, true) : false;
							   
							   $tooltip = esc_attr($media->post_title);//basename($file);
							   
							   $output[$key] = sprintf('<li class="clearfix">%s<a href="%s" title="%s">%s</a></li>', $markunread, $file, $tooltip, $title.$titleend);//$over, 
							   
?>

<?php }
						   
						   ksort($output);
						   
						   foreach ($output as &$line) {
							   $i++;
							   if ($limited && $i > $limit + 1) {
								   $line = str_replace('<li class="clearfix">', '<li class="clearfix overlimit">', $line);
							   }
							   
						   }
						   
						   
						   
						   echo implode(PHP_EOL, $output);
						   
						   if ($limited) { ?>
<li class="showmore"><a href="#"><i class="icon-double-angle-down"></i></a></li>
<?php }
						   
?>
<?php if(!$ul) { ?></ul><?php } ?>
<?php } else { ?>
<?php if(!$ul) { ?><ul class="unstyled" style="margin-bottom:0;"><?php } ?>
<?php if($na) { ?><li><span class="label" title="Not yet available" style="opacity:0.4;float:right;margin-top:1px;margin-left:1px;">N/A</span><span class="muted" style="opacity:0.6;"><?php echo $media_type->name; ?></span></li><?php } ?>
<?php if(!$ul) { ?></ul><?php } ?>
<?php } ?>
<?php if(!$ul) { ?></td><?php } ?>
<?php
}

