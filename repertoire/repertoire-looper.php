<?php
function repertoire_media_shortcode($atts=array()) {
	
	$musicians = isset($atts['musicians']);
	
	$songs = array();
	
	$songs['Angels']['Music'] = 'Angels.pdf';
	//$songs['Angels']['new'] = true;
	$songs['Angels']['performer'] = 'Robbie Williams';
	$songs['Angels']['Practice']['Tenor 1'] = 'Angels - Practice - Tenor 1.mp3';
	$songs['Angels']['Practice']['Tenor 2'] = 'Angels - Practice - Tenor 2.mp3';
	$songs['Angels']['Practice']['Baritone'] = 'Angels - Practice - Baritone.mp3';
	$songs['Angels']['Practice']['Bass'] = 'Angels - Practice - Bass.mp3';
	$songs['Angels']['Singalong']['Tenor 1'] = 'Angels - Singalong - Tenor 1.mp3';
	$songs['Angels']['Singalong']['Tenor 2'] = 'Angels - Singalong - Tenor 2.mp3';
	$songs['Angels']['Singalong']['Baritone'] = 'Angels - Singalong - Baritone.mp3';
	$songs['Angels']['Singalong']['Bass'] = 'Angels - Singalong - Bass.mp3';
	$songs['Angels']['slug'] = 'angels';
	$songs['Angels']['thumb'] = '2012/10/AngelsRobbiecover.jpg';
	
	/* ----------------------------------------------- */
	
	$songs['Beautiful']['Music'] = 'Beautiful.pdf';
	//$songs['Beautiful']['new'] = true;
	$songs['Beautiful']['performer'] = 'Christina Aguilera';
	$songs['Beautiful']['Singalong']['Tenor 1'] = 'Beautiful - Singalong - Tenor 1.mp3';
	$songs['Beautiful']['Singalong']['Tenor 2'] = 'Beautiful - Singalong - Tenor 2.mp3';
	$songs['Beautiful']['Singalong']['Baritone'] = 'Beautiful - Singalong - Baritone.mp3';
	$songs['Beautiful']['Singalong']['Bass'] = 'Beautiful - Singalong - Bass.mp3';
	$songs['Beautiful']['Practice']['Tenor 1'] = 'Beautiful - Practice - Tenor 1.mp3';
	$songs['Beautiful']['Practice']['Tenor 2'] = 'Beautiful - Practice - Tenor 2.mp3';
	$songs['Beautiful']['Practice']['Baritone'] = 'Beautiful - Practice - Baritone.mp3';
	$songs['Beautiful']['Practice']['Bass'] = 'Beautiful - Practice - Bass.mp3';
	$songs['Beautiful']['slug'] = 'beautiful';
	$songs['Beautiful']['thumb'] = '2012/10/220px-02_-_Beautiful.jpg';
	
	/* ----------------------------------------------- */
	
	$songs['Chasing Cars']['Music'] = 'Chasing Cars.pdf';
	//$songs['Chasing Cars']['new'] = true;
	$songs['Chasing Cars']['performer'] = 'Snow Patrol';
	$songs['Chasing Cars']['Practice']['Tenor 1'] = 'Chasing Cars - Practice - Tenor1.mp3';
	$songs['Chasing Cars']['Practice']['Tenor 2'] = 'Chasing Cars - Practice - Tenor 2.mp3';
	$songs['Chasing Cars']['Practice']['Baritone'] = 'Chasing Cars - Practice - Baritone.mp3';
	$songs['Chasing Cars']['Practice']['Bass'] = 'Chasing Cars - Practice - Bass.mp3';
	$songs['Chasing Cars']['Singalong']['Tenor 1']['A'] = 'Chasing Cars - Singalong - Tenor 1A.mp3';
	$songs['Chasing Cars']['Singalong']['Tenor 1']['B'] = 'Chasing Cars - Singalong - Tenor 1B.mp3';
	$songs['Chasing Cars']['Singalong']['Tenor 2'] = 'Chasing Cars - Singalong - Tenor 2.mp3';
	$songs['Chasing Cars']['Singalong']['Baritone']['A'] = 'Chasing Cars - Singalong - Baritone A.mp3';
	$songs['Chasing Cars']['Singalong']['Baritone']['B'] = 'Chasing Cars - Singalong - Baritone B.mp3';
	$songs['Chasing Cars']['Singalong']['Bass'] = 'Chasing Cars - Singalong - Bass.mp3';
	$songs['Chasing Cars']['slug'] = 'chasing-cars';
	$songs['Chasing Cars']['thumb'] = '2012/10/Chasingcars.jpg';
	
	/* ----------------------------------------------- */
	
	$songs['Colour My World']['Music'] = 'Colour My World.pdf';
	$songs['Colour My World']['performer'] = 'Petula Clark';
	$songs['Colour My World']['Practice']['All Parts'] = 'Colour My World - Practice.mp3';
	$songs['Colour My World']['Practice']['Tenor 1'] = 'Colour My World - Practice - Tenor 1.mp3';
	$songs['Colour My World']['Practice']['Tenor 2'] = 'Colour My World - Practice - Tenor 2.mp3';
	$songs['Colour My World']['Practice']['Baritone'] = 'Colour My World - Practice - Baritone.mp3';
	$songs['Colour My World']['Practice']['Bass'] = 'Colour My World - Practice - Bass.mp3';
	$songs['Colour My World']['Singalong']['Tenor 1'] = 'Colour My World - Singalong - Tenor 1.mp3';
	$songs['Colour My World']['Singalong']['Tenor 2'] = 'Colour My World - Singalong - Tenor 2.mp3';
	$songs['Colour My World']['Singalong']['Baritone'] = 'Colour My World - Singalong - Baritone.mp3';
	$songs['Colour My World']['Singalong']['Bass'] = 'Colour My World - Singalong - Bass.mp3';
	$songs['Colour My World']['slug'] = 'colour-my-world';
	$songs['Colour My World']['thumb'] = '2012/07/220px-Colormyworld.jpg';
	
	/* ----------------------------------------------- */
	
	$songs['Fix You']['Music'] = 'Fix You.pdf';
	//$songs['Fix You']['new'] = true;
	$songs['Fix You']['performer'] = 'Coldplay';
	$songs['Fix You']['Practice']['Tenor 1'] = 'Fix You - Practice - Tenor 1.mp3';
	$songs['Fix You']['Practice']['Tenor 2'] = 'Fix You - Practice - Tenor 2.mp3';
	$songs['Fix You']['Practice']['Baritone'] = 'Fix You - Practice - Baritone.mp3';
	$songs['Fix You']['Practice']['Bass'] = 'Fix You - Practice - Bass.mp3';
	$songs['Fix You']['Singalong']['Tenor 1'] = 'Fix You - Singalong - Tenor 1.mp3';
	$songs['Fix You']['Singalong']['Tenor 2'] = 'Fix You - Singalong - Tenor 2.mp3';
	$songs['Fix You']['Singalong']['Baritone'] = 'Fix You - Singalong - Baritone.mp3';
	$songs['Fix You']['Singalong']['Bass'] = 'Fix You - Singalong - Bass.mp3';
	$songs['Fix You']['slug'] = 'fix-you';
	$songs['Fix You']['thumb'] = '2012/10/220px-Coldplay_Fix_You.svg_.png';
	
	/* ----------------------------------------------- */
	
	$songs['Greenfields']['Music'] = 'Greenfields.pdf';
	//$songs['Greenfields']['new'] = true;
	$songs['Greenfields']['performer'] = 'The Brothers Four';
	$songs['Greenfields']['Practice']['All Parts'] = 'Greenfields - Practice.mp3';
	$songs['Greenfields']['Singalong']['Tenor'] = 'Greenfields - Singalong - Tenor.mp3';
	$songs['Greenfields']['Singalong']['Bass'] = 'Greenfields - Singalong - Bass.mp3';
	$songs['Greenfields']['slug'] = 'greenfields';
	$songs['Greenfields']['thumb'] = 'the-brothers-four-greenfields-cbs-2.jpg';
	
	/* ----------------------------------------------- */
	
	$songs['Happy Ending']['Music'] = 'Happy Ending.pdf';
	//$songs['Happy Ending']['new'] = true;
	$songs['Happy Ending']['performer'] = 'Mika';
	$songs['Happy Ending']['Singalong']['Tenor 1'] = 'Happy Ending - Singalong - Tenor 1.mp3';
	$songs['Happy Ending']['Singalong']['Tenor 2'] = 'Happy Ending - Singalong - Tenor 2.mp3';
	$songs['Happy Ending']['Singalong']['Baritone'] = 'Happy Ending - Singalong - Baritone.mp3';
	$songs['Happy Ending']['Singalong']['Bass'] = 'Happy Ending - Singalong - Bass.mp3';
	$songs['Happy Ending']['Practice']['Tenor 1'] = 'Happy Ending - Practice - Tenor 1.mp3';
	$songs['Happy Ending']['Practice']['Tenor 2'] = 'Happy Ending - Practice - Tenor 2.mp3';
	$songs['Happy Ending']['Practice']['Baritone'] = 'Happy Ending - Practice - Baritone.mp3';
	$songs['Happy Ending']['Practice']['Bass'] = 'Happy Ending - Practice - Bass.mp3';
	$songs['Happy Ending']['slug'] = 'happy-ending';
	$songs['Happy Ending']['thumb'] = '2012/10/220px-HappyEndingMika.jpg';
	
	/* ----------------------------------------------- */
	
	$songs['Proud']['Music'] = 'Proud.pdf';
	$songs['Proud']['performer'] = 'Heather Small';
	$songs['Proud']['Practice']['All Parts'] = 'Proud - Practice.mp3';
	$songs['Proud']['Practice']['Tenor 1']['A'] = 'Proud - Practice - Tenor 1A.mp3';
	$songs['Proud']['Practice']['Tenor 1']['B'] = 'Proud - Practice - Tenor 1B.mp3';
	$songs['Proud']['Practice']['Tenor 2']['A'] = 'Proud - Practice - Tenor 2A.mp3';
	$songs['Proud']['Practice']['Tenor 2']['B'] = 'Proud - Practice - Tenor 2B.mp3';
	$songs['Proud']['Practice']['Baritone']['A'] = 'Proud - Practice - Baritone A.mp3';
	$songs['Proud']['Practice']['Baritone']['B'] = 'Proud - Practice - Baritone B.mp3';
	$songs['Proud']['Practice']['Bass']['A'] = 'Proud - Practice - Bass A.mp3';
	$songs['Proud']['Practice']['Bass']['B'] = 'Proud - Practice - Bass B.mp3';
	$songs['Proud']['Singalong']['Tenor 1']['A'] = 'Proud - Singalong - Tenor 1A.mp3';
	$songs['Proud']['Singalong']['Tenor 1']['B'] = 'Proud - Singalong - Tenor 1B.mp3';
	$songs['Proud']['Singalong']['Tenor 2']['A'] = 'Proud - Singalong - Tenor 2A.mp3';
	$songs['Proud']['Singalong']['Tenor 2']['B'] = 'Proud - Singalong - Tenor 2B.mp3';
	$songs['Proud']['Singalong']['Baritone']['A'] = 'Proud - Singalong - Baritone A.mp3';
	$songs['Proud']['Singalong']['Baritone']['B'] = 'Proud - Singalong - Baritone B.mp3';
	$songs['Proud']['Singalong']['Bass']['A'] = 'Proud - Singalong - Bass A.mp3';
	$songs['Proud']['Singalong']['Bass']['B'] = 'Proud - Singalong - Bass B.mp3';
	$songs['Proud']['slug'] = 'proud';
	$songs['Proud']['thumb'] = '2012/08/Proud-220px.jpg';
	
	/* ----------------------------------------------- */
	
	$songs['Real Men']['Music'] = 'Real Men.pdf';
	//$songs['Real Men']['new'] = true;
	$songs['Real Men']['performer'] = 'Joe Jackson';
	$songs['Real Men']['Practice']['All Parts'] = 'Real Men - Practice.mp3';
	$songs['Real Men']['Practice']['Tenor 1'] = 'Real Men - Practice - Tenor 1.mp3';
	$songs['Real Men']['Practice']['Tenor 2'] = 'Real Men - Practice - Tenor 2.mp3';
	$songs['Real Men']['Practice']['Baritone'] = 'Real Men - Practice - Baritone.mp3';
	$songs['Real Men']['Practice']['Bass'] = 'Real Men - Practice - Bass.mp3';
	$songs['Real Men']['Singalong']['Tenor 1'] = 'Real Men - Singalong - Tenor 1.mp3';
	$songs['Real Men']['Singalong']['Tenor 2'] = 'Real Men - Singalong - Tenor 2.mp3';
	$songs['Real Men']['Singalong']['Baritone'] = 'Real Men - Singalong - Baritone.mp3';
	$songs['Real Men']['Singalong']['Bass'] = 'Real Men - Singalong - Bass.mp3';
	$songs['Real Men']['slug'] = 'real-men';
	$songs['Real Men']['thumb'] = '220px-Night_and_day_JJ.jpg';
	
	/* ----------------------------------------------- */
	
	$songs['Rolling in the Deep']['Music'] = 'Rolling in the Deep.pdf';
	$songs['Rolling in the Deep']['performer'] = 'Adele';
	$songs['Rolling in the Deep']['Practice']['All Parts'] = 'Rolling in the Deep - Practice.mp3';
	$songs['Rolling in the Deep']['Practice']['Tenor 1'] = 'Rolling in the Deep - Practice - Tenor 1.mp3';
	$songs['Rolling in the Deep']['Practice']['Tenor 2'] = 'Rolling in the Deep - Practice - Tenor 2.mp3';
	$songs['Rolling in the Deep']['Practice']['Bass']['A'] = 'Rolling in the Deep - Practice - Bass A.mp3';
	$songs['Rolling in the Deep']['Practice']['Bass']['B'] = 'Rolling in the Deep - Practice - Bass B.mp3';
	$songs['Rolling in the Deep']['Singalong']['Tenor 1'] = 'Rolling in the Deep - Singalong - Tenor 1.mp3';
	$songs['Rolling in the Deep']['Singalong']['Tenor 2']['A'] = 'Rolling in the Deep - Singalong - Tenor 2A.mp3';
	$songs['Rolling in the Deep']['Singalong']['Tenor 2']['B'] = 'Rolling in the Deep - Singalong - Tenor 2B.mp3';
	$songs['Rolling in the Deep']['Singalong']['Baritone'] = 'Rolling in the Deep - Singalong - Baritone.mp3';
	$songs['Rolling in the Deep']['Singalong']['Bass']['A'] = 'Rolling in the Deep - Singalong - Bass A.mp3';
	$songs['Rolling in the Deep']['Singalong']['Bass']['B'] = 'Rolling in the Deep - Singalong - Bass B.mp3';
	$songs['Rolling in the Deep']['slug'] = 'rolling-in-the-deep';
	$songs['Rolling in the Deep']['thumb'] = '2012/08/220px-Adele-Rolling_In_The_Deep.jpg';
	
	/* ----------------------------------------------- */
	
	$songs['Scarborough Fair']['Music'] = 'Scarborough Fair.pdf';
	//$songs['Scarborough Fair']['new'] = true;
	$songs['Scarborough Fair']['performer'] = 'Simon and Garfunkel';
	$songs['Scarborough Fair']['Practice']['All Parts'] = 'Scarborough Fair - Practice.mp3';
	$songs['Scarborough Fair']['Practice']['Tenor 1'] = 'Scarborough Fair - Practice - Tenor 1.mp3';
	$songs['Scarborough Fair']['Practice']['Tenor 2'] = 'Scarborough Fair - Practice - Tenor 2.mp3';
	$songs['Scarborough Fair']['Practice']['Baritone'] = 'Scarborough Fair - Practice - Baritone.mp3';
	$songs['Scarborough Fair']['Practice']['Bass'] = 'Scarborough Fair - Practice - Bass.mp3';
	$songs['Scarborough Fair']['Singalong']['Tenor 1'] = 'Scarborough Fair - Singalong - Tenor 1.mp3';
	$songs['Scarborough Fair']['Singalong']['Tenor 2'] = 'Scarborough Fair - Singalong - Baritone Tenor 2.mp3';
	$songs['Scarborough Fair']['Singalong']['Baritone'] = 'Scarborough Fair - Singalong - Baritone Tenor 2.mp3';
	$songs['Scarborough Fair']['Singalong']['Bass'] = 'Scarborough Fair - Singalong - Bass.mp3';
	$songs['Scarborough Fair']['slug'] = 'scarborough-fair';
	$songs['Scarborough Fair']['thumb'] = '2012/10/220px-ParsleySage.jpg';
	
	/* ----------------------------------------------- */
	
	$songs['Somebody To Love']['Music'] = 'Somebody To Love.pdf';
	//$songs['Somebody To Love']['new'] = true;
	$songs['Somebody To Love']['performer'] = 'Queen';
	$songs['Somebody To Love']['Practice']['All Parts'] = 'Somebody To Love - Practice.mp3';
	$songs['Somebody To Love']['Practice']['Tenor 1'] = 'Somebody To Love - Practice - Tenor 1.mp3';
	$songs['Somebody To Love']['Practice']['Tenor 2'] = 'Somebody To Love - Practice - Tenor 2.mp3';
	$songs['Somebody To Love']['Practice']['Baritone'] = 'Somebody To Love - Practice - Baritone.mp3';
	$songs['Somebody To Love']['Practice']['Bass'] = 'Somebody To Love - Practice - Bass.mp3';
	$songs['Somebody To Love']['Singalong']['Tenor 1'] = 'Somebody To Love - Singalong - Tenor 1.mp3';
	$songs['Somebody To Love']['Singalong']['Tenor 2'] = 'Somebody To Love - Singalong - Tenor 2.mp3';
	$songs['Somebody To Love']['Singalong']['Baritone'] = 'Somebody To Love - Singalong - Baritone.mp3';
	$songs['Somebody To Love']['Singalong']['Bass'] = 'Somebody To Love - Singalong - Bass.mp3';
	$songs['Somebody To Love']['slug'] = 'somebody-to-love';
	$songs['Somebody To Love']['thumb'] = 'Stlove.jpg';
	
	/* ----------------------------------------------- */
	
	$songs['Space Oddity']['Music'] = 'Space Oddity.pdf';
	//$songs['Space Oddity']['new'] = true;
	$songs['Space Oddity']['performer'] = 'David Bowie';
	$songs['Space Oddity']['Practice']['Tenor 1'] = 'Space Oddity - Practice - Tenor 1.mp3';
	$songs['Space Oddity']['Practice']['Tenor 2'] = 'Space Oddity - Practice - Tenor 2.mp3';
	$songs['Space Oddity']['Practice']['Baritone'] = 'Space Oddity - Practice - Baritone.mp3';
	$songs['Space Oddity']['Practice']['Bass'] = 'Space Oddity - Practice - Bass.mp3';
	$songs['Space Oddity']['Singalong']['Tenor 1'] = 'Space Oddity - Singalong - Tenor 1.mp3';
	$songs['Space Oddity']['Singalong']['Tenor 2'] = 'Space Oddity - Singalong - Tenor 2.mp3';
	$songs['Space Oddity']['Singalong']['Baritone'] = 'Space Oddity - Singalong - Baritone.mp3';
	$songs['Space Oddity']['Singalong']['Bass'] = 'Space Oddity - Singalong - Bass.mp3';
	$songs['Space Oddity']['slug'] = 'space-oddity';
	$songs['Space Oddity']['thumb'] = 'Bowie_SpaceOdditySingle.jpg';
	
	/* ----------------------------------------------- */
	
	$songs['Throw Your Arms Around Me']['Music'] = 'Throw Your Arms Around Me.pdf';
	//$songs['Throw Your Arms Around Me']['new'] = true;
	$songs['Throw Your Arms Around Me']['performer'] = 'Hunters and Collectors';
	$songs['Throw Your Arms Around Me']['Practice']['All Parts'] = 'Throw Your Arms Around Me - Practice - All Parts.mp3';
	$songs['Throw Your Arms Around Me']['Practice']['Solo'] = 'Throw Your Arms Around Me - Practice - Solo.mp3';
	$songs['Throw Your Arms Around Me']['Practice']['Tenor 1'] = 'Throw Your Arms Around Me - Practice - Tenor 1.mp3';
	$songs['Throw Your Arms Around Me']['Practice']['Tenor 2'] = 'Throw Your Arms Around Me - Practice - Tenor 2.mp3';
	$songs['Throw Your Arms Around Me']['Practice']['Baritone'] = 'Throw Your Arms Around Me - Practice - Baritone.mp3';
	$songs['Throw Your Arms Around Me']['Practice']['Bass'] = 'Throw Your Arms Around Me - Practice - Bass.mp3';
	$songs['Throw Your Arms Around Me']['Singalong']['Solo'] = 'Throw Your Arms Around Me - Singalong - Solo.mp3';
	$songs['Throw Your Arms Around Me']['Singalong']['Tenor 1'] = 'Throw Your Arms Around Me - Singalong - Tenor 1.mp3';
	$songs['Throw Your Arms Around Me']['Singalong']['Tenor 2'] = 'Throw Your Arms Around Me - Singalong - Tenor 2.mp3';
	$songs['Throw Your Arms Around Me']['Singalong']['Baritone'] = 'Throw Your Arms Around Me - Singalong - Baritone.mp3';
	$songs['Throw Your Arms Around Me']['Singalong']['Bass'] = 'Throw Your Arms Around Me - Singalong - Bass.mp3';
	$songs['Throw Your Arms Around Me']['slug'] = 'throw-your-arms-around-me';
	$songs['Throw Your Arms Around Me']['thumb'] = '2012/08/220px-ThrowYourArmsAroundMe.jpg';
	
	ksort($songs);
	
	//print_pre($songs);
	global $current_user;
	$base_url = 'http://files.lowrez.com.au/media/'.$current_user->user_nicename.'/repertoire/this-season/';
	
	
	if ($musicians) {
		
		$content = '<table class="table repertoire-table table-bordered table-hover">';
		$content .= '<thead>
<th></th>
<th>Song</th>
<th>Sheet&nbsp;Music</th>
<th>Recording</th>
<th>Lead&nbsp;Sheet</th>
</thead>';
		
		foreach ($songs as $title => $details) {
			
			$content .= '<tr>';
			$content .= sprintf('<td class="textcenter"><img class="list-img img-polaroid" alt="" src="http://www.lowrez.com.au/wp-content/uploads/%s" /></td>', $details['thumb']);
			
			$content .= sprintf('<td><strong><em>%s</em></strong>%s<br>%s</td>', $title, $new, $details['performer']);
			
			$content .= sprintf('<td><a href="%s%s/download">Sheet&nbsp;Music</a></td>', $base_url, $details['Music']);
			
			if ($details['Practice']['All Parts']) {
				$content .= sprintf('<td><a href="%s%s/download">Recording</a></td>', $base_url, $details['Practice']['All Parts']);
			}
			else {
				$content .= '<td><span class="label" title="Not yet available">N/A</span></td>';
			}
			
			$content .= '<td>';
			
			if (is_array(@$details['Lead Sheet'])) {
				$content .= '<ul class="unstyled">';
				foreach ($details['Lead Sheet'] as $part => $file) {
					if ($file == false) {
						$content .= sprintf('<li>%s <span class="label" title="Not yet available">N/A</span></li>', str_replace(' ', '&nbsp;', $part));
					}
					else {
						$content .= sprintf('<li><a href="%s%s/download">%s</a></li>', $base_url, $file, str_replace(' ', '&nbsp;', $part));
					}
				}
				$content .= '</ul>';
			}
			else {
				$content .= '<span class="label" title="Not yet available">N/A</span>';
			}
			
			$content .= '</td>';
			
			$content .= '</tr>';
		}
		
		$content .= '</table>';
		
	}
	else {
		
		$voicepart = format_voicepart(get_user_meta($current_user->ID, 'voicepart', true));
		
		$content = '<p>N.B. If a part is split (divisi), part A is the higher part and part B is the lower part.</p>';
		//<p>Singalongs will be provided as they are available. Thanks to Nick for all his time in preparing these!</p>';
		
		$pbg = plugin_dir_url(__FILE__).'repbg.png';
		
		if (!IS_MOBILE) {  
			
			$content .= '<table class="table repertoire-table table-bordered table-hover">';
			$content .= '<thead>
<th>Song</th>
<th>Sheet&nbsp;Music</th>
<th>Practice</th>
<th>Singalong</th>
</thead>';
			
			foreach ($songs as $title => $details) {
				
				$new = isset($details['new']) ? ' <span class="label label-warning" title="Posted since your last login">New</span>' : '';
				
				$content .= '<tr>';
				/*$content .= sprintf('<td class="textcenter"><a href="http://www.lowrez.com.au/repertoire/%s/">
<img class="list-img img-polaroid" alt="" src="http://www.lowrez.com.au/wp-content/uploads/%s" />
</a></td>', $details['slug'], $details['thumb']);*/
	
				
				/*$content .= sprintf('<td style="min-width:200px;background:url(http://www.lowrez.com.au/wp-content/uploads/%s) no-repeat center top;"><div class="media">
<a class="pull-left" style="margin-right:8px;" href="http://www.lowrez.com.au/repertoire/%s/">
<img class="media-object list-img img-polaroid" src="http://www.lowrez.com.au/wp-content/uploads/%s">
</a>
<div class="media-body">
<a href="http://www.lowrez.com.au/repertoire/%s/">
<strong class="media-heading"><em>%s</em></strong></a>%s<br>
%s
</div>
</div></td>', $details['thumb'], $details['slug'], $details['thumb'], $details['slug'], $title, $new, $details['performer'] );*/
				//rgba(255,255,255,0.6)
				$content .= sprintf('<td style="padding:0;width:130px;height:130px;background:url(http://www.lowrez.com.au/wp-content/uploads/%s) no-repeat center top;background-size:cover;">
<div style="background:url(%s);padding:8px;"><a href="http://www.lowrez.com.au/repertoire/%s/">
<strong><em>%s</em></strong></a>%s<br>
%s</div>
</td>', $details['thumb'], $pbg, $details['slug'], $title, $new, $details['performer'] );
				
				
				/*$content .= sprintf('<td><a href="http://www.lowrez.com.au/repertoire/%s/">
<strong><em>%s</em></strong></a>%s<br>
%s</td>', $details['slug'], $title, $new, $details['performer']);*/
				
				$content .= sprintf('<td><a href="%s%s/download"><strong>Sheet&nbsp;Music</strong></a></td>', $base_url, $details['Music']);
				
				$content .= '<td>';
				
				if (is_array(@$details['Practice'])) {
					$content .= '<ul class="unstyled">';
					foreach ($details['Practice'] as $part => $file) {
						if (is_array($file)) {
							if ($part == $voicepart) { $part = "<strong>$part</strong>"; }
							$content .= sprintf('<li>%s <ul class="unstyled" style="margin-left:1em;">', $part);
							foreach ($file as $subpart => $file) {
								$content .= sprintf('<li><a href="%s%s/download">%s</a></li>', $base_url, $file, str_replace(' ', '&nbsp;', 'Part ' . $subpart));
							}
							$content .= '</ul>';
						}
						else {
							if ($file == false) {
								$content .= sprintf('<li>%s <span class="label" title="Not yet available">N/A</span></li>', str_replace(' ', '&nbsp;', $part));
							}
							else {
								if ($part == $voicepart) { $part = "<strong>$part</strong>"; }
								$content .= sprintf('<li><a href="%s%s/download">%s</a></li>', $base_url, $file, str_replace(' ', '&nbsp;', $part));
							}
						}
					}
					$content .= '</ul>';
				}
				else {
					$content .= '<span class="label" title="Not yet available">N/A</span>';
				}
				
				$content .= '</td>';
				$content .= '<td>';
				
				if (is_array(@$details['Singalong'])) {
					$content .= '<ul class="unstyled">';
					foreach ($details['Singalong'] as $part => $file) {
						if (is_array($file)) {
							if ($part == $voicepart) { $part = "<strong>$part</strong>"; }
							$content .= sprintf('<li>%s (', $part);
							$subparts = array();
							foreach ($file as $subpart => $file) {
								$subparts[] = sprintf('<a href="%s%s/download">%s</a>', $base_url, $file, str_replace(' ', '&nbsp;', 'Part ' . $subpart));
							}
							$content .= implode(', ', $subparts);
							$content .= ')';
						}
						else {
							if ($file == false) {
								$content .= sprintf('<li>%s <span class="label" title="Not yet available">N/A</span></li>', str_replace(' ', '&nbsp;', $part));
							}
							else {
								if ($part == $voicepart) { $part = "<strong>$part</strong>"; }
								$content .= sprintf('<li><a href="%s%s/download">%s</a></li>', $base_url, $file, str_replace(' ', '&nbsp;', $part));
							}
						}
					}
					$content .= '</ul>';
				}
				else {
					$content .= '<span class="label" title="Not yet available">N/A</span>';
				}
				
				$content .= '<td>';
				$content .= '</tr>';
			}
			
			$content .= '</table>';
			
		}
		else {
			
			$content .= '';
			
			foreach ($songs as $title => $details) {
				
				$new = isset($details['new']) ? ' <span class="label label-warning" title="Posted since your last login">New</span>' : '';
				
				$content .= sprintf('<h4><a href="http://www.lowrez.com.au/repertoire/%s/"><em>%s</em> - %s</a> %s</h4>', $details['slug'], $title, $details['performer'], $new);
				
				$content .= '<ul class="unstyled" style="margin-left:2em;">';
				
				$content .= sprintf('<li><a href="%s%s/download"><h5>Sheet Music</h5></a></li>', $base_url, $details['Music']);
				
				$content .= '<li>';
				
				$content .= '<h5>Practice Tracks</h5>';
				if (is_array(@$details['Practice'])) {
					$content .= '<ul class="unstyled" style="margin-left:2em;">';
					foreach ($details['Practice'] as $part => $file) {
						
						if (is_array($file)) {
							if ($part == $voicepart) { $part = "<strong>$part</strong>"; }
							$content .= sprintf('<li>%s <ul class="unstyled" style="margin-left:1em;">', $part);
							foreach ($file as $subpart => $file) {
								$content .= sprintf('<li><a href="%s%s/download">%s</a></li>', $base_url, $file, str_replace(' ', '&nbsp;', 'Part ' . $subpart));
							}
							$content .= '</ul>';
						}
						else {
							if ($file == false) {
								$content .= sprintf('<li>%s <span class="label" title="Not yet available">N/A</span></li>', str_replace(' ', '&nbsp;', $part));
							}
							else {
								if ($part == $voicepart) { $part = "<strong>$part</strong>"; }
								$content .= sprintf('<li><a href="%s%s/download">%s</a></li>', $base_url, $file, str_replace(' ', '&nbsp;', $part));
							}
						}
						
					}
					$content .= '</ul>';
				}
				else {
					$content .= '<span class="label" title="Not yet available">N/A</span>';
				}
				
				$content .= '</li>';
				$content .= '<li>';
				
				$content .= '<h5>Singalong Tracks</h5>';
				if (is_array(@$details['Singalong'])) {
					$content .= '<ul class="unstyled" style="margin-left:2em;">';
					foreach ($details['Singalong'] as $part => $file) {
						if (is_array($file)) {
							if ($part == $voicepart) { $part = "<strong>$part</strong>"; }
							$content .= sprintf('<li>%s <ul class="unstyled" style="margin-left:1em;">', $part);
							foreach ($file as $subpart => $file) {
								$content .= sprintf('<li><a href="%s%s/download">%s</a></li>', $base_url, $file, str_replace(' ', '&nbsp;', 'Part ' . $subpart));
							}
							$content .= '</ul>';
						}
						else {
							if ($file == false) {
								$content .= sprintf('<li>%s <span class="label" title="Not yet available">N/A</span></li>', str_replace(' ', '&nbsp;', $part));
							}
							else {
								if ($part == $voicepart) { $part = "<strong>$part</strong>"; }
								$content .= sprintf('<li><a href="%s%s/download">%s</a></li>', $base_url, $file, str_replace(' ', '&nbsp;', $part));
							}
						}
					}
					$content .= '</ul>';
				}
				else {
					$content .= '<span class="label" title="Not yet available">N/A</span>';
				}
				
				$content .= '<li>';
				$content .= '</ul>';
			}
			
		}
		
	}
	
	
	return $content;
	
}