<?php

function lowrez_happy_birthday($user_id = false) {
	
	if (!$user_id) $user_id = get_current_user_id();
	
	$celebrate	=	get_user_meta( $user_id, 'display_birthday', true );
	
	if ( $celebrate ) {
		
		
		
	}
	
}

function lowrez_get_birthdays() {
	
	if ( $bdays = get_option( 'lowrez_birthdays' ) ) {
		// Winning
	}
	else {
		
		global $wpdb;
		$bdays = $wpdb->get_results(
			"SELECT dob.user_id, 
dob.meta_value AS birthday 
FROM   $wpdb->usermeta dob 
INNER JOIN $wpdb->usermeta dsp 
ON ( dob.user_id = dsp.user_id 
AND dsp.meta_key = 'display_birthday' 
AND dob.`meta_key` = 'date_of_birth' ) 
WHERE  dsp.meta_value = 'yes'
ORDER BY  dob.meta_value;
");
		
		$newbdays = array();
		
		foreach ($bdays as $bday) {
			
			$date = explode('/', $bday->birthday);
			$y = (date('n') == 1 && $date[0] == 12) ? date('Y', strtotime('-1 year')) : date('Y');
			$date = mktime(0, 0, 0, $date[0], $date[1], $y); 	
			
			$u = get_userdata($bday->user_id);
			
			$newbdays['u'.$bday->user_id]['date'] = $date;
			$newbdays['u'.$bday->user_id]['name'] = $u->display_name;
			
		}
		
		usort($newbdays, function($a, $b) {
			return $a['date'] > $b['date'];
		});
		$bdays = $newbdays;
		
		//update_option( 'lowrez_birthdays', $bdays );
	}
	
	
	$bdays = array_filter($bdays, function($item) { //FIXME
		//echo date('j M Y', $item).'<br>';
		//return true;
		return true;//$item['date'] >= strtotime('-7 days') &&
		//$item['date'] <= strtotime('+7 days');
	});
	
	return $bdays;
	
}