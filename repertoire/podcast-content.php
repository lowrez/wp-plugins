<?php

include_once('repertoire-looper.php');

function podcast_updated($podcast, $voicepart) {
	$last_cached = get_option('podcast_cache_'.$podcast);
}

function put_lowrez_podcasts() {

global $current_user;
$voicepart = get_user_meta($current_user->ID, 'voicepart', true);
$voicepart_name = format_voicepart($voicepart);

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
	/*$the_slug = 'this-season';
$args=array(
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
	
						?>

<ul class="nav nav-tabs" id="repertoire-tabs">
	<li class="active"><a href="#repertoire-podcast" data-toggle="tab"><i class="iconnew-podcast" style="line-height:80%;">&nbsp;</i>Podcast</a></li>
	<li><a href="#repertoire-download" data-toggle="tab"><i class="icon-download-alt">&nbsp;</i>Download</a></li>
</ul>

<div class="tab-content">
	<div class="tab-pane active" id="repertoire-podcast">
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
		echo "<div class='span{$width}'><table class='table table-bordered table-hover'>
<tbody>
<tr>
<th rowspan='2' style='background:url({$pbg}{$p['bglink']}.png) no-repeat center;background-size:cover;padding:0;'><div style='background:url({$pbg}repbg.png);padding:8px;'>{$p['title']}</div></th>
<td style='width:60px;'><a href='http://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}' target='_blank'><i class='icon-rss'>&nbsp;</i>RSS</a></td>
</tr><tr>
<td style='width:60px;'><a href='itpc://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}'>iTunes</a></td>
</tr>
</tbody>
</table>
</div>";
	}
	foreach ($podcasts['voicepart'] as $p) {
		echo "<div class='span{$width}'><table class='table table-bordered table-hover'>
<tbody>
<tr>
<th rowspan='2' style='background:url({$pbg}{$p['bglink']}.png) no-repeat center;background-size:cover;padding:0;'><div style='background:url({$pbg}repbg.png);padding:8px;'>{$voicepart_name} {$p['title']}</div></th>
<td style='width:60px;'><a href='http://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}/$voicepart_slug' target='_blank'><i class='icon-rss'>&nbsp;</i>RSS</a></td>
</tr><tr>
<td style='width:60px;'><a href='itpc://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}/$voicepart_slug'>iTunes</a></td>
</tr>
</tbody>
</table>
</div>";
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
	echo "<tr>
<th>{$p['title']}</th>
<td style='width:60px;'><a href='http://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}' target='_blank'><i class='icon-rss'>&nbsp;</i>RSS</a></td>
<td style='width:60px;'><a href='itpc://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}'>iTunes</a></td>
</tr>";
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
	//$voicepart_slug = format_voicepart($vpart, false, 'slug');
	echo "<tr>
<th>$vpart</th>";
	
	foreach ($podcasts['voicepart'] as $p) {
		echo "
<td style='width:60px;'><a href='http://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}/$v' target='_blank'><i class='icon-rss'>&nbsp;</i>RSS</a></td>
<td style='width:60px;'><a href='itpc://{$lowrez_podcast}/{$current_user->user_nicename}/{$p['link']}/$v'>iTunes</a></td>";
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
	
	$m = IS_MOBILE;
	
	?>
	<div class="tab-pane" id="repertoire-download">
		<div id="repertoire-accordion">
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#repertoire-accordion" href="#repertoire-mine">
						<strong>My Repertoire</strong>
					</a>
				</div>
				<div id="repertoire-mine" class="accordion-body collapse in">
					<div class="accordion-inner">
						<div class="row-fluid">
						<?php
	if (!$m) {
		$all_reps = array_chunk($all_reps, ceil(count($all_reps)/3));
	}
	else {
		$all_reps = array($all_reps);
	}
	foreach ($all_reps as $reps) {
						?>
						<div class="span4">
						<table class="table repertoire-table table-bordered table-hover">
							<thead>
								<th>Song</th>
								<th>Downloads</th>
							</thead>
							<tbody>
								<?php
	$voicepart_slug = format_voicepart($voicepart, false, 'slug');
	
	foreach ($reps as $rep) {
		echo '<tr>';
		rcell_repertoire($rep, 3);
		rcell_media($rep, 'sheet-music');
		echo '</tr><tr>';
		rcell_media($rep, 'practice-track', $voicepart_slug);
		echo '</tr><tr>';
		rcell_media($rep, 'singalong',  $voicepart_slug);
		echo '</tr>';
	}
	
								?>
							</tbody>
						</table>
						</div>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#repertoire-accordion" href="#repertoire-all">
						<strong>All Repertoire</strong>
					</a>
				</div>
				<div id="repertoire-all" class="accordion-body collapse">
					<div class="accordion-inner">
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
			rcell_media($rep, 'sheet-music');
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
						<?php //echo repertoire_media_shortcode(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	
}

function rcell_repertoire($post, $rowspan=false) {
	
	global $post_type;
	$post_type = 'attachment';

	$image = wp_get_attachment_image_src(get_post_meta( $post->ID, '_thumbnail_id', true ));
	$pbg = plugin_dir_url(__FILE__).'repbg.png';
	
	?>
<td <?php echo $rowspan ? " rowspan='$rowspan'" : ''; ?> style="padding:0;width:120px;height:120px;background:url(<?php echo $image[0]; ?>) no-repeat center top;background-size:120px;">
	<div style="background:url(<?php echo $pbg; ?>);padding:8px;">
		<a href="<?php echo get_permalink($post); ?>">
			<strong><em><?php echo $post->post_title; ?></em></strong></a>
		<br><?php echo implode(',<br>', wp_get_post_terms($post->ID, 'performer', array("fields" => "names"))); ?>
	</div>
</td>
<?php
}

function rcell_media($post, $media_type=false, $part=false, $all=false) {
		
	global $post_type;
	$post_type = 'repertoire-media';
	
	$args = array( 
		'post_type' => 'repertoire-media',  
		'post_status' => 'publish',
		'nopaging' => true,
		'order' => 'ASC',
		'orderby' => 'title',
		'post_parent' => $post->ID
	);
	
	if ($media_type) {
		$args['tax_query'][] = array(
			'taxonomy' => 'media-type',
			'field' => 'slug',
			'terms' => $media_type,
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
<td>
	<?php if (count($medias)) { ?>
	<ul class="unstyled" style="margin-bottom:0;">
	<?php foreach ($medias as $media) {
		$file = repertoire_get_attachment_url($media->ID);
		$title = $part || $all ? get_post_meta($media->ID, 'podcast_artist', true) : false; ?>
		<li><a href="<?php echo $file; ?>"><?php echo $title . ' ' . $media_type->name; ?></a><?php echo is_unread($media->ID); ?></li>
	<?php } ?>
	</ul>
	<?php } else { ?>
	<?php /*if (!$all) {*/ ?><span class="muted"><?php echo $media_type->name; ?></span> <?php /*}*/ ?>
	<span class="label" title="Not yet available">N/A</span>
	<?php } ?>
</td>
<?php
}

