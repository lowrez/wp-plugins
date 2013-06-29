<?php

function lowrez_admin_tabs( $current = 'membership' ) {
	$tabs = array(
		'membership' => 'Membership',
		//'who-we-are' => 'Who We Are',
		//'financial' => 'Financial',
		'site-admin' => 'Site Admin',
	);
	
	if (current_user_can('promote_users')) {
		if (defined('CF_FORMNAME')) {
			$tabs['application'] = 'Application';
		}
	}
	
	echo "<input type='hidden' name='tab' value='$current'>";
	
	echo '<br><h2 class="nav-tab-wrapper">';
	foreach( $tabs as $tab => $name ){
		$class = ( $tab == $current ) ? ' nav-tab-active' : '';
		$url = add_query_arg( array('tab'=>$tab) );
		echo "<a class='nav-tab$class' href='$url'>$name</a>";
		
	}
	echo '</h2><br>';
}


function profile_whoweare( $user ) {
	
	$me = $user->ID == get_current_user_id();
	$publish = get_user_meta($user->ID, 'who_we_are_show', true);
	
?>
<h3><?php _e( 'Who We Are','simple-local-avatars' ); ?></h3>
<div style="max-width:800px;">
	<p class="description">The public website contains a page called <a href="<?php echo home_url('about/who-we-are'); ?>">Who We Are</a>, designed to show members of the 
		public what makes up LOW REZ.</p>
	<p class="description">
		If you would like to be included in this page (first name, voice part, photo, your short message), you may enter a short message about yourself and why you sing with LOW REZ. It may be edited by 
		the committee before being published. </p>
	<p class="description">
		If you do not want to be included in this page, leave the text box below blank.
	</p>
</div>
<table class="form-table">
	<tr>
		<th><label for="simple-local-avatar"><?php _e('Profile Photo','simple-local-avatars'); ?></label></th>
		<td style="width: 50px;" valign="top">
			<?php echo get_avatar( $user->ID ); ?>
		</td>
		<td>
			<?php
	$options = get_option('simple_local_avatars_caps');
	
	if ( (empty($options['simple_local_avatars_caps']) || current_user_can('upload_files')) && ( !$publish || (!$me && current_user_can('promote_users')) ) ) {
		do_action( 'simple_local_avatar_notices' ); 
		wp_nonce_field( 'simple_local_avatar_nonce', '_simple_local_avatar_nonce', false ); 
			?>
			<input type="file" name="simple-local-avatar" id="simple-local-avatar" /><br />
			<?php
		if ( empty( $user->simple_local_avatar ) )
			echo '<span class="description">' . __('No profile photo is set. Use the upload field to add a profile photo.','simple-local-avatars') . '</span>';
		else 
			echo '
<input type="checkbox" name="simple-local-avatar-erase" value="1" /> ' . __('Delete profile photo','simple-local-avatars') . '<br />
<span class="description">' . __('Replace the profile photo by uploading a new photo, check the delete option to remove it.','simple-local-avatars') . '</span>
';		
	} else {
		if ( empty( $user->simple_local_avatar ) )
			echo '<span class="description">' . __('No profile photo is set.','simple-local-avatars') . '</span>';
		else 
			echo '<span class="description">' . __('Because you have been published on the Who We Are page, you cannot change your profile photo.<br>To change your profile photo, contact <a href="mailto:secretary@lowrez.com.au">secretary@lowrez.com.au</a>.','simple-local-avatars') . '</span>';
	}
			?>
		</td>
	</tr>
	
	<?php
	
	if (!$me && current_user_can('promote_users')): ?>
	
	<?php
	$unedited = get_user_meta($user->ID, 'who_we_are_bio_unedited', true);
	$edited = get_user_meta($user->ID, 'who_we_are_bio', true);
	$edited = $edited ? $edited : $unedited;
	?>
	<tr>
		<th><label for="who_we_are_bio_unedited">User-Submitted Biography</label></th>
		<td colspan="2"><textarea name="who_we_are_bio_unedited" id="who_we_are_bio_unedited" disabled="disabled" rows="5" cols="30"><?php echo $unedited; ?></textarea><br />
			<span class="description">This is what the user has submitted as their About You. It will not be published until you edit it below and check Publish.</span></td>
	</tr>
	<tr>
		<th><label for="who_we_are_bio">Edited Biography for Publishing</label></th>
		<td colspan="2"><textarea name="who_we_are_bio" id="who_we_are_bio" rows="5" cols="30"><?php echo $edited; ?></textarea>
		</td>
	</tr>
	<?php
	if (protect_code(array(2, 3), $user->ID)):
	?>
	<tr>
		<th>Publish?</th>
		<td colspan="2"><input type="checkbox" name="who_we_are_show" value="show" <?php checked( $publish, 'show' ); ?> />&nbsp;Publish this user on the Who We Are page. <span class="description">This will prevent them from changing their profile photo and biography.</span></td>
	</tr>
	<?php 
	else:
	?>
	
	<tr>
		<th>Published</th>
		<td colspan="2">As a non-active member, this biography is <strong>not</strong> shown on the public Who We Are page</td>
	</tr>
	
	<?php
	
	endif;
	?>
	<?php elseif($me):
	if (protect_code(array(2, 3), $user->ID)):
	?>
	<tr>
		<th><label for="who_we_are_bio_unedited">About You</label></th>
		<td colspan="2"><textarea name="who_we_are_bio_unedited" id="who_we_are_bio_unedited" rows="5" cols="30"><?php echo get_user_meta($user->ID, 'who_we_are_bio_unedited', true); ?></textarea><br />
			<span class="description">Share a short message about yourself and why you sing with LOW REZ.</span></td>
	</tr>
	<tr>
		<th><label for="who_we_are_bio">Edited for Publication</label></th>
		<td colspan="2"><textarea name="who_we_are_bio" id="who_we_are_bio" rows="5" cols="30" disabled="disabled"><?php echo get_user_meta($user->ID, 'who_we_are_bio', true); ?></textarea><br />
			<span class="description">If published, this is the edited version of your About You that will be displayed.<br>To have this edited biography changed, contact <a href="mailto:secretary@lowrez.com.au">secretary@lowrez.com.au</a>.</span></td>
	</tr>
	<tr>
		<th>Published</th>
		<td colspan="2"><?php echo 'You '. ($publish=='show' ? '<strong>are</strong>' : 'are <strong>not</strong>') . ' shown on the public Who We Are page.'; ?></td>
	</tr>
	<?php
	
	else:
	?>
	
	<tr>
		<th>Published</th>
		<td colspan="2"><?php echo 'As a non-active member, you are <strong>not</strong> shown on the public Who We Are page.'; ?></td>
	</tr>
	
	<?php
	endif;
	endif;
	
	?>
</table>
<script type="text/javascript">var form = document.getElementById('your-profile');form.encoding = 'multipart/form-data';form.setAttribute('enctype', 'multipart/form-data');</script>
<?php
	
}

add_action ( 'show_user_profile', 'lowrez_show_extra_profile_fields', 20 );
add_action ( 'edit_user_profile', 'lowrez_show_extra_profile_fields', 20 );

function lowrez_show_extra_profile_fields ( $user ) {
	
	$me = $user->ID == get_current_user_id();
	
	if (protect_code(array(2,3,17), $user->ID)) {
	
	$you_member = $me ? 'You' : 'Member';
	$your_members = $me ? 'Your' : 'Member\'s';
	
	$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 'membership';
	
	lowrez_admin_tabs($tab);
		
	}
	else {
		$tab = false;
	}
	
	
	switch ($tab) {
		case 'membership':
		
?>

<h3><?php _e('Membership Information'); ?></h3>
<?php if (!current_user_can('promote_users') || $me): ?>
<div style="background: #FFFFE0;border: 1px solid #E6DB55;border-radius:3px;padding: 0 0.6em;"><p style="margin: 0.5em 0;padding: 2px;">Any information below which you cannot modify must be updated by emailing <a href="mailto:secretary@lowrez.com.au">secretary@lowrez.com.au</a>.</p></div>
<?php endif; ?>
<table class="form-table">
	<?php if (!current_user_can('promote_users') || $me): ?>
	<tr>
		<th>Access Level</th>
		<td><?php
		global $wp_roles;
		$role = array_shift($user->roles);
		echo $wp_roles->role_names[$role];
			?>
		</td>
	</tr>
	
	<?php endif; ?>
	<?php if(false): ?>
	<tr>
		<th><label for="memberstatus"><?php _e('Membership Status'); ?></label></th>
		<td>
			<?php $value = get_user_meta($user->ID, 'memberstatus', true);
		if (!$value) $value = 'active';
			?>
			
			<select name="memberstatus" id="memberstatus">
				<option value="active" <?php selected($value, 'active'); ?>>Active</option>
				<option value="hiatus" <?php selected($value, 'hiatus'); ?>>Hiatus</option>
				<option value="left" <?php selected($value, 'left'); ?>>Left</option>
			</select>
			<?php
		$value = get_user_meta($user->ID, 'memberstatus_modified', true);
		if ($value) {
			echo ' since&nbsp;'.date('j\&\n\b\s\p\;F Y', $value);
			echo ' <span class="description">('.time_passed($value) . ')</span>';
		}
		else {
			//echo 'Never updated';
		}
			?>
		</td>
	</tr>
	<?php endif; ?>
	<tr>
		<?php $value = get_user_meta($user->ID, 'voicepart', true); ?>
		<?php if (current_user_can('promote_users')): ?>
		<th><label for="voicepart"><?php _e('Voice Part'); ?></label></th>
		<td>
			<select name="voicepart" id="voicepart">
				<option value="" <?php selected($value, ''); ?>>&mdash;</option>
				<option value="t1" <?php selected($value, 't1'); ?>>Tenor 1</option>
				<option value="t2" <?php selected($value, 't2'); ?>>Tenor 2</option>
				<option value="bar" <?php selected($value, 'bar'); ?>>Baritone</option>
				<option value="b" <?php selected($value, 'b'); ?>>Bass</option>
			</select>
		</td>
		<?php else: ?>
		<th>Voice Part</th>
		<td><?php echo format_voicepart($value, false); ?></td>
		<?php endif; ?>
	</tr>
	<tr>
		<th>Section Leader
			<?php if (current_user_can('promote_users')): ?>
			<br><span class="description">Set Access Level <a href="#role">above</a><br> to change this.</span></th>
		<?php endif; ?>
		<td>
			<?php $value = in_array('section_leader', $user->roles);
		echo $value ? 'Yes' : 'No';
			?>
			<?php //<label for="section_leader"><input id="section_leader" type="checkbox"
		// checked($value); disabled="disabled" /> Section Leader</label>
			?>
			
		</td>
	</tr>
	<tr>
		<th>Date Joined</th>
		<td>
			<?php
		$value = explode('/', get_user_meta($user->ID, 'date_joined', true));
		if (is_array($value)) {
			$d = (int) $value[2];
			$m = (int) $value[1];
			$y = (int) $value[0];
		} else {
			$d = $m = $y = '';
		}
			?>
			<select name="date_joined_d" id="date_joined_d">
				<option value="" <?php selected($d, ''); ?>>-</option>
				<?php
		for ($i = 1; $i <= 31; $i++) { echo '<option value="' . $i . '"' . selected($d, $i) . '>' . $i . '</option>';
									 }
				?>
			</select>
			<select name="date_joined_m" id="date_joined_m">
				<option value="" <?php selected($m, ''); ?>>-</option>
				<?php
		for ($i = 1; $i <= 12; $i++) {
			$month = date("F", mktime(0, 0, 0, $i, 10));
			echo '<option value="' . $i . '"' . selected($m, $i) . '>' . $month . '</option>';
		}
				?>
			</select>
			<select name="date_joined_y" id="date_joined_y">
				<option value="" <?php selected($y, ''); ?>>-</option>
				<?php $year = (int) date("Y"); ?>
				<?php
		for ($i = $year; $i >= 2008; $i--) { echo '<option value="' . $i . '"' . selected($y, $i) . '>' . $i . '</option>';
										   }
				?>
			</select>
			<?php
		if ($y) {
			echo ' <span class="description">('.time_passed(mktime(0, 0, 0, (int) $m, (int) $d, (int) $y)) . ')</span>';
		}
			?>
		</td>
	</tr>
	<tr>
		<th>Date of Birth</th>
		<td>
			<?php
		$y = get_user_meta($user->ID, 'date_of_birth_y', true);
		$value = explode('/', get_user_meta($user->ID, 'date_of_birth', true));
		if (is_array($value)) {
			
			$m = (int) $value[0];
			$d = (int) $value[1];
			
		} else {
			$d = $m = '';
		}
			?>
			<select name="date_of_birth_d" id="date_of_birth_d">
				<option value="" <?php selected($d, ''); ?>>-</option>
				<?php
		for ($i = 1; $i <= 31; $i++) { echo '<option value="' . $i . '"' . selected($d, $i) . '>' . $i . '</option>';
									 }
				?>
			</select>
			<select name="date_of_birth_m" id="date_of_birth_m">
				<option value="" <?php selected($m, ''); ?>>-</option>
				<?php
		for ($i = 1; $i <= 12; $i++) {
			$month = date("F", mktime(0, 0, 0, $i, 10));
			echo '<option value="' . $i . '"' . selected($m, $i) . '>' . $month . '</option>';
		}
				?>
			</select>
			<select name="date_of_birth_y" id="date_of_birth_y">
				<option value="" <?php selected($y, ''); ?>>-</option>
				<?php $year = (int) date("Y"); ?>
				<?php
		for ($i = $year - 16; $i >= $year - 80; $i--) { echo '<option value="' . $i . '"' . selected($y, $i) . '>' . $i . '</option>';
													  }
				?>
			</select> 
		</td>
	</tr>
	<tr>
		<th>Celebrate Birthday</th>
		<td><label>
			<?php if ($me): ?>
			<input type="checkbox" name="display_birthday" value="yes" <?php checked( get_user_meta($user->ID, 'display_birthday', true), 'yes' ); ?> />
			Display your birthday to other members <span class="description">(day and month only)</span>
			</label>
			<br>
			<span class="description">Your date of birth is always displayed to committee members.</span>
			<?php else: ?>
			<?php if (get_user_meta($user->ID, 'display_birthday', true) == 'yes') {
					echo 'Display member\'s birthday to other members';
				}
		else {
			echo 'Do <strong>not</strong> display member\'s birthday to other members';
		}
			?>
			
			
			<?php endif; ?>
		</td>
	</tr>
</table>

<h3><?php _e('Emergency Contact Details'); ?></h3>
<table class="form-table">
	<tr>
		<th><label for="emergency_name"><?php _e('Emergency Contact Name'); ?></label></th>
		<td>
			<?php $value = get_user_meta($user->ID, 'emergency_name', true); ?>
			<input id="emergency_name" name="emergency_name" type="text" class="regular-text" value="<?php echo $value; ?>" />
		</td>
	</tr>
	<tr>
		<th><label for="emergency_number"><?php _e('Mobile Number'); ?></label></th>
		<td>
			<?php $value = get_user_meta($user->ID, 'emergency_number', true); ?>
			<input id="emergency_number" name="emergency_number" type="text" value="<?php echo $value; ?>" />
		</td>
	</tr>
	<tr>
		<th><label for="emergency_relation"><?php _e('Relationship to ' . $you_member); ?></label></th>
		<td>
			<?php $value = get_user_meta($user->ID, 'emergency_relation', true); ?>
			<select name="emergency_relation" id="emergency_relation">
				<option value="" <?php selected($value, ''); ?>>&mdash;</option>
				<option value="partner" <?php selected($value, 'partner'); ?>>Partner</option>
				<option value="sibling" <?php selected($value, 'sibling'); ?>>Sibling</option>
				<option value="parent" <?php selected($value, 'parent'); ?>>Parent</option>
				<option value="child" <?php selected($value, 'child'); ?>>Child</option>
				<option value="relative" <?php selected($value, 'relative'); ?>>Relative</option>
				<option value="friend" <?php selected($value, 'friend'); ?>>Friend</option>
				<option value="colleague" <?php selected($value, 'colleague'); ?>>Colleague</option>
				<option value="other" <?php selected($value, 'other'); ?>>Other</option>
			</select>
			<!--<input id="emergency_relation" name="emergency_relation" type="text" class="regular-text" value="<?php //echo $value; ?>" />-->
		</td>
	</tr>
</table>

<?php break; ?>
<?php case 'who-we-are': break; //FIXME ?>

<?php
		profile_whoweare($user);		
?>

<?php break; ?>
<?php case 'site-admin': ?>

<?php if(current_user_can('list_users') || $me): ?>
<!--<div id="icon-admin" class="icon32"><br></div>
<h2>Site Administration</h2>-->
<h3><?php _e('Site Usage'); ?></h3>
<table class="form-table">
	<?php if (current_user_can('list_users')): ?>
	<tr>
		<th><?php _e('Most Recent Logins'); ?></th>
		<?php $values = get_user_meta($user->ID, 'wp-last-login', true); ?>
		<td>
			<?php
		if (is_array($values)) {
			$values = array_reverse($values);
			foreach ($values as $value) {
				echo date('j\&\n\b\s\p\;F Y, g:i\&\n\b\s\p\;a', $value);
				echo ' <span class="description">('.time_passed($value) . ')</span>';
				echo '<br>';
			}
		}
		else {
			echo 'Never logged in';
		}
			?>
		</td>
	</tr>
	<tr>
		<th><?php _e('Number of Logins'); ?></th>
		<td>
			<?php $value = get_user_meta($user->ID, 'wp-last-login-times', true); ?>
			<?php echo $value ? number_format($value) : 0; ?>
		</td>
	</tr>
	<tr>
		<th><?php _e('First Login'); ?></th>
		<td>
			<?php $value = get_user_meta($user->ID, 'wp-last-login-first', true);
		if ($value) {
			echo date('j\&\n\b\s\p\;F Y, g:i\&\n\b\s\p\;a', $value);
			echo ' <span class="description">('.time_passed($value) . ')</span>';
		}
		else {
			echo 'Never logged in';
		}
			?>
		</td>
	</tr>
	<?php else: ?>
	<tr>
		<th><?php _e('Last Login'); ?></th>
		<td>
			<?php $value = second_last_login(); ?>
			<?php
		if ($value) {
			echo date('j\&\n\b\s\p\;F Y, g:i\&\n\b\s\p\;a', $value);
			echo ' <span class="description">('.time_passed($value) . ')</span>';
		}
		else {
			echo 'This is your first login';
		}
			?>
		</td>
	</tr>
	<?php endif; ?>
	<tr>
		<th><?php _e('Profile Last Modified'); ?></th>
		<td>
			<?php $value = get_user_meta($user->ID, 'profile-last-updated-self', true);
		if ($value) {
			echo date('j\&\n\b\s\p\;F Y, g:i\&\n\b\s\p\;a', $value['time']);
			echo ' <span class="description">('.time_passed($value['time']) . ')</span> by '.strtolower($you_member);
		}
		else {
			echo 'Never modified by '.strtolower($you_member);
		}
			?>
			<br>
			<?php $value = get_user_meta($user->ID, 'profile-last-updated', true);
		if ($value) {
			echo date('j\&\n\b\s\p\;F Y, g:i\&\n\b\s\p\;a', $value['time']);
			echo ' <span class="description">('.time_passed($value['time']) . ')</span>';
			if ($value['user'] == get_current_user_id()) {
				echo ' by you';
			}
			else {
				$moduser = new WP_User($value['user']);
				if ($moduser) {
					echo ' by ' . $moduser->display_name;
				}
			}
		}
		else {
			echo 'Never modified by another user';
		}
			?>
		</td>
	</tr>
</table>
<?php endif; ?>
<?php if(current_user_can('edit_users')): ?>
<h3><?php _e('Group Membership'); ?></h3>
<?php
		$u = new LowRez_User($user->ID);										 
?>
<table class="form-table">
	<tr>
		<th><?php _e('Site Access Groups'); ?></th>
		<td>
			<?php $values = $u->wp_groups_display; ?>
			<?php
		if (!is_array($values) || empty($values)) {
			echo 'No groups';
		}
		else {
			foreach ($values as $value) {
				//int_pre($value);
				echo $value->group_title;
				//echo ' <span class="description">'. $value->group_description . '</span>';
				echo '<br>';
			}
		}
			?>
		</td>
	</tr>
	<tr>
		<th><?php _e('Google Mailing Lists'); ?></th>
		<td>
			<?php $values = $u->google_groups_display; ?>
			<?php
		if (!is_array($values) || empty($values)) {
			echo 'No groups';
		}
		else {
			foreach ($values as $value) {
				//print_pre($value);
				echo $value->groupName;
				echo ' <span class="description">'. $value->groupId. '</span>';
				//echo ' <span class="description">'. $value->description. '</span>';
				echo '<br>';
			}
		}
			?>
		</td>
	</tr>
</table>
<?php endif; ?>


<?php break; ?>
<?php case 'financial': break; //FIXME ?>

<h3><?php _e('Fees History'); ?></h3>
<?php if (!current_user_can('promote_users') || $me): ?>
<div style="background: #FFFFE0;border: 1px solid #E6DB55;border-radius:3px;padding: 0 0.6em;"><p style="margin: 0.5em 0;padding: 2px;">Any information below which you cannot modify must be updated by emailing <a href="mailto:treasurer@lowrez.com.au">treasurer@lowrez.com.au</a>.</p></div>
<?php endif; ?>
<table class="form-table">
	<tr>
		<?php $value = get_user_meta($user->ID, 'fee_type', true); ?>
		<?php if (current_user_can('promote_users') && !$me): ?>
		<th><label for="fee_type"><?php _e('Fee Concession'); ?></label></th>
		<td>
			<input type="checkbox" name="fee_type" value="c" <?php checked( get_user_meta($user->ID, 'fee_type', true), 'c' ); ?> />&nbsp;Concession rate
		</td>
		<?php else: ?>
		<th>Fee Concession</th>
		<td><?php echo $value=='c' ? 'Concession rate' : 'Standard rate'; ?></td>
		<?php endif; ?>
	</tr>
	<!--<tr>
<th><label for="s"><?php //_e('s'); ?></label></th>
<td>
</td>
</tr>-->
</table>

<?php break; ?>
<?php case 'signups': ?>


<?php break; ?>

<?php case 'application': 
		if (current_user_can('promote_users')) : ?>

<h3><?php _e('Original Application'); ?></h3>

<?php
		$exp = new CFDBFormIterator();
		$atts = array(
			'filter' => 'new_user_id='.$user->ID
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
		<td><?php echo $data['your-email']; ?></td>
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

<?php else: ?>

<p>No application on record.<p>

<?php endif; ?>
<?php endif; ?>
<?php break; ?>

<?php } ?>

<br>

<?php
}

/**
* Save data input from custom field on profile page
*/

add_action ( 'personal_options_update', 'lowrez_save_extra_profile_fields' );
add_action ( 'edit_user_profile_update', 'lowrez_save_extra_profile_fields' );

function lowrez_save_extra_profile_fields( $user_id ) {
	
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
	
	$_POST['mobile'] = format_mobile($_POST['mobile']);
	
	$me = $user_id == get_current_user_id();
	
	$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 'membership';
	
	switch ($tab) {
		
		case 'membership':
		
		//update_user_meta( $user_id, 'memberstatus', $_POST['memberstatus'] );
		if (!$me) update_user_meta( $user_id, 'voicepart', $_POST['voicepart'] );
		update_user_meta( $user_id, 'date_joined', $_POST['date_joined_y'].'/'.$_POST['date_joined_m'].'/'.$_POST['date_joined_d'] );
		
		// Date of Birth
		$y = $_POST['date_of_birth_y'];
		update_user_meta( $user_id, 'date_of_birth_y', $y );
		
		if (($m = $_POST['date_of_birth_m']) && ($d = $_POST['date_of_birth_d'])) {
			update_user_meta( $user_id, 'date_of_birth', $m.'/'.$d );
		}
		update_user_meta( $user_id, 'display_birthday', $_POST['display_birthday'] );
		
		// Emergency Contact Details
		update_user_meta( $user_id, 'emergency_name', $_POST['emergency_name'] );
		$_POST['emergency_number'] = format_mobile($_POST['emergency_number']);
		update_user_meta( $user_id, 'emergency_number', $_POST['emergency_number'] );
		update_user_meta( $user_id, 'emergency_relation', $_POST['emergency_relation'] );
		
		break;
		
		case 'who-we-are':
		
		if ($me) {
			update_user_meta( $user_id, 'who_we_are_bio_unedited', $_POST['who_we_are_bio_unedited'] );
		}
		else {
			$bio = $_POST['who_we_are_bio'];
			update_user_meta( $user_id, 'who_we_are_bio', $bio );
			$show = $_POST['who_we_are_show'];
			
			if (!empty($show) && !empty($bio)) {
				update_user_meta( $user_id, 'who_we_are_show', $show );
			}
			else {
				update_user_meta( $user_id, 'who_we_are_show', '' );
			}
		}
		
		break;
		
		case 'financial':
		
		update_user_meta( $user_id, 'fee_type', $_POST['fee_type'] );
		
		break;
		
		case 'site-admin':
		
		
		
		break;
		
	}
	
}

add_action ( 'personal_options_update', 'lowrez_save_last_modified_date' );
add_action ( 'edit_user_profile_update', 'lowrez_save_last_modified_date' );
add_action ( 'user_register', 'lowrez_save_last_modified_date' );

function lowrez_save_last_modified_date( $user_id ) {
	if ($user_id == get_current_user_id()) {
		update_user_meta($user_id, 'profile-last-updated-self', array('time'=>time()));
	}
	else {
		update_user_meta($user_id, 'profile-last-updated', array('time'=>time(), 'user'=>get_current_user_id()));
	}
}



function format_mobile($mobile, $format = 'clean') {
	
	$mobile = preg_replace('/[^0-9\r\n]+/m', '', $mobile); //Remove non-numerals
	$mobile = preg_replace('/^\+?610?/m', '0', $mobile); //Remove +61(0) prefix, replace with 0
	
	
	switch ($format) {
		case 'display':
		if (substr($mobile, 0, 2) == '04') {
			$mobile = preg_replace('/^([0-9]{4})([0-9]{3})([0-9]{3})(.*)$/m', '$1 $2 $3', $mobile); // 0000 000 000
		}
		else {
			$mobile = preg_replace('/^([0-9]{2})([0-9]{4})([0-9]{4})(.*)$/m', '$1 $2 $3', $mobile); // 00 0000 0000
		}
		break;
	}
	
	return $mobile;
	
}

function format_voicepart($voicepart, $roles = false, $format = 'long') {
	
	$parts =
		array(
		'long' =>
		array(
			't1' => 'Tenor 1',
			't2' => 'Tenor 2',
			'bar' => 'Baritone',
			'b' => 'Bass'
		),
		'short' =>
		array(
			't1' => 'T1',
			't2' => 'T2',
			'bar' => 'Bar.',
			'b' => 'B'
		),
		'slug' =>
		array(
			't1' => 'tenor-1',
			't2' => 'tenor-2',
			'bar' => 'baritone',
			'b' => 'bass'
		),
	);
	
	$leader =
		array(
		'long' =>
		array(
			true => 'Section Leader',
			false => 'Member'
		),
		'short' =>
		array(
			true => '*',
			false => ''
		),
	);
	
	$voicepart = $parts[$format][$voicepart];
	if (is_array($roles)) {
		$voicepart = $voicepart . ' ' . $leader[$format][in_array('section_leader', $roles)];
	}
	
	return $voicepart;
}

add_filter( 'update_user_metadata', 'lowrez_update_user_metadata', 10, 5 );
function lowrez_update_user_metadata( $meta_type = null, $user_id, $meta_key, $new_value, $prev_value = '' ) {
	
	if (in_array($meta_key, array( 'zzmemberstatus') )) {
		
		$old_value = get_user_meta($user_id, $meta_key, true);
		if ($old_value != $new_value) {
			if ($meta_key == 'zzmemberstatus') {
				//update_user_meta( $user_id, 'memberstatus_modified', time() );
			}
			/*elseif ($meta_key == 'date_of_birth') {
			//$m = explode('/', $new_value);
			//$m = $m[1];
			//if ($m == date('n')) {
			//	update_option('lowrez_birthdays', false);
			//echo 'updating birthdays because of '. $meta_key;
			//}
			}
			elseif ($meta_key == 'display_birthday') {
			//update_option('lowrez_birthdays', false);
			//echo 'updating birthdays because of ' . $meta_key;
			}*/
		}		
	}
}


function lowrez_default_gravatar( $avatar_defaults ) {
	$avatar_defaults = array();	
	
	$myavatar = plugin_dir_url(__FILE__). 'user.png';//get_bloginfo('template_directory')
	$avatar_defaults[$myavatar] = 'LOW REZ User';
	
	$myavatar = plugin_dir_url(__FILE__). 'lowrez.png';//get_bloginfo('template_directory')
	$avatar_defaults[$myavatar] = 'LOW REZ Icon';
	
	
	/*unset($avatar_defaults['blank']);
	unset($avatar_defaults['mystery']);
	unset($avatar_defaults['gravatar_default']);
	unset($avatar_defaults['identicon']);
	unset($avatar_defaults['wavatar']);
	unset($avatar_defaults['monsterid']);
	unset($avatar_defaults['retro']);*/
	
	return $avatar_defaults;
}

add_filter( 'avatar_defaults', 'lowrez_default_gravatar' );

/* ------------------------------------------------------- */

function get_user_by_meta_data( $meta_key, $meta_value ) {

	$users = get_users_by_meta_data($meta_key, $meta_value);

	return $users[0];

} // end get_user_by_meta_data

function get_users_by_meta_data( $meta_key, $meta_value ) {

	// Query for users based on the meta data
	$user_query = new WP_User_Query(
		array(
			'meta_key'	  =>	$meta_key,
			'meta_value'	=>	$meta_value
		)
	);
	
	// Get the results from the query, returning the first user
	$users = $user_query->get_results();

	return $users;

} // end get_users_by_meta_data
