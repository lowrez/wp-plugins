<?php

/* ---------------------------------------------------------------- */

function lowrez_signups_scripts($hook) {
	global $current_screen;
	if( 'signup' != $current_screen->id ) return;
	wp_enqueue_script( 'jquery.tablegroup', plugins_url('/jquery.tablegroup.js', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'lowrez_signups_scripts' );


// Register Custom Post Type
function register_signup() {
	$labels = array(
		'name'                => _x( 'Signups', 'Post Type General Name', 'lowrez' ),
		'singular_name'       => _x( 'Signup', 'Post Type Singular Name', 'lowrez' ),
		'menu_name'           => __( 'Signups', 'lowrez' ),
		'parent_item_colon'   => __( 'Parent Signup:', 'lowrez' ),
		'all_items'           => __( 'All Signups', 'lowrez' ),
		'view_item'           => __( 'View Signup', 'lowrez' ),
		'add_new_item'        => __( 'Add New Signup', 'lowrez' ),
		'add_new'             => __( 'New Signup', 'lowrez' ),
		'edit_item'           => __( 'Edit Signup', 'lowrez' ),
		'update_item'         => __( 'Update Signup', 'lowrez' ),
		'search_items'        => __( 'Search signups', 'lowrez' ),
		'not_found'           => __( 'No signups found', 'lowrez' ),
		'not_found_in_trash'  => __( 'No signups found in Trash', 'lowrez' ),
	);
	
	$args = array(
		'label'               => __( 'signup', 'lowrez' ),
		'description'         => __( 'Signup', 'lowrez' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),//, 'excerpt'),//'editor', 
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 40                   ,
		'menu_icon'           => '',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	
	register_post_type( 'signup', $args );
}

// Hook into the 'init' action
add_action( 'init', 'register_signup', 0 );

/* ---------------------------------------------------------------- */

function register_signup_metaboxes() {
	
	remove_meta_box( 'slugdiv' , 'signup' , 'side' );
	remove_meta_box( 'ctxps-grouplist-box' , 'signup' , 'side' );  
	
	add_meta_box("signup_meta", "Signup Details", "set_signup_meta", "signup", "normal", "high");
	
	global $post;
	if ($post->post_status != 'auto-draft' && $post->post_status != 'draft') {
		
		add_meta_box("signups_summary_meta", "Signups Summary", "set_signups_summary_meta", "signup", "side", "high");
		
		if (get_post_meta($post->ID, 'signup_bus', true)) {
			add_meta_box("signups_bus_summary_meta", "Bus Summary", "set_signups_bus_summary_meta", "signup", "side", "default");
			add_meta_box("signups_bus_list_meta", "Bus List", "set_signups_bus_list_meta", "signup", "normal", "default");
		}
		
		//if (get_post_meta($post->ID, 'signup_meal', true))
		//add_meta_box("signups_meal_summary_meta", "Meal Summary", "set_meals_summary_meta", "signup", "side", "high");
		
		add_meta_box("signups_list_meta", "Signups Recorded", "set_signups_list_meta", "signup", "normal", "default");
	}
}
add_action( 'add_meta_boxes' , 'register_signup_metaboxes' );

/* ---------------------------------------------------------------- */

function set_signups_summary_meta() {
	global $post, $wpdb;
	//attend, meta_value as voicepart, 
	$query = $wpdb->prepare("SELECT CONCAT(meta_value, '-', attend) as id, 
count(*) AS signups FROM {$wpdb->prefix}lowrez_signups s
INNER JOIN {$wpdb->usermeta} m
ON s.user_id = m.user_id AND m.meta_key = 'voicepart'
WHERE event_id = %d
GROUP BY attend, meta_value", $post->ID);
	
	$results = $wpdb->get_results($query, OBJECT_K);

	output_signup_summary($results);
}

/* ---------------------------------------------------------------- */

function set_signups_bus_summary_meta() {
	global $post, $wpdb;
	
	$query = $wpdb->prepare("
SELECT
  CASE
    WHEN signup_meta LIKE '%%\"bus\";s:1:\"y\"%%' THEN 'y'
    WHEN signup_meta LIKE '%%\"bus\";s:1:\"n\"%%' THEN 'n'
    WHEN signup_meta LIKE '%%\"bus\";s:1:\"m\"%%' THEN 'm'
    ELSE '-'
  END AS signup_id,
count(*) AS signups
FROM {$wpdb->prefix}lowrez_signups s
WHERE event_id = %d
GROUP BY signup_id", $post->ID);
	
	$results = $wpdb->get_results($query, OBJECT_K);
	
	
	$query = $wpdb->prepare("
SELECT
user_id,
  CASE
    WHEN signup_meta LIKE '%%\"bus\";s:1:\"y\"%%' THEN 'y'
    WHEN signup_meta LIKE '%%\"bus\";s:1:\"m\"%%' THEN 'm'
    ELSE '-'
  END AS signup_id,
signup_meta AS signups
FROM {$wpdb->prefix}lowrez_signups s
WHERE event_id = %d
AND signup_meta LIKE '%%\"bus_guest\"%%'
GROUP BY user_id", $post->ID);
	
	$guests_results = $wpdb->get_results($query, OBJECT_K);
	
	$guests = array();
	
	foreach ($guests_results as $guest) {
		$guest->signups = unserialize($guest->signups);
		$guests[$guest->signup_id] += (int) $guest->signups['bus_guest'];
	}

	
	output_signup_summary($results, true, $guests);
}

/* ---------------------------------------------------------------- */

function output_signup_summary($results, $members = false, $guest = false) {
?>
<table class="signup-custom signup-custom-meta">
	<thead>
		<tr>
			<th style="padding:0;"><h3>Part</h3></th>
			<th style="padding:0;text-align:right;"><h3>Y</h3></th>
			<th style="padding:0;text-align:right;"><h3>M</h3></th>
			<th class="total" style="padding:0;text-align:right;"><h3>Likely</h3></th>
			<th style="padding:0;text-align:right;"><h3>N</h3></th>
			<th class="total" style="padding:0;text-align:right;"><h3>All</h3></th>
		</tr>
	</thead>
	<tbody>
		<?php if(!$members) { ?>
		<tr>
			<th>Tenor&nbsp;1</th>
			<td><?php echo (int) @$results['t1-y']->signups; ?></td>
			<td><?php echo (int) @$results['t1-m']->signups; ?></td>
			<td class="total"><?php echo (int) @$results['t1-y']->signups + (int) @$results['t1-m']->signups; ?></td>
			
			<td><?php echo (int) @$results['t1-n']->signups; ?></td>
			<td class="total"><?php echo @$results['t1-y']->signups + @$results['t1-m']->signups + @$results['t1-n']; ?></td>
		</tr>
		<tr>
			<th>Tenor&nbsp;2</th>
			<td><?php echo (int) @$results['t2-y']->signups; ?></td>
			<td><?php echo (int) @$results['t2-m']->signups; ?></td>
			<td class="total"><?php echo (int) @$results['t2-y']->signups + (int) @$results['t2-m']->signups; ?></td>
			
			<td><?php echo (int) @$results['t2-n']->signups; ?></td>
			<td class="total"><?php echo @$results['t2-y']->signups + @$results['t2-m']->signups + @$results['t2-n']; ?></td>
		</tr>
		<tr>
			<th>Baritone</th>
			<td><?php echo (int) @$results['bar-y']->signups; ?></td>
			<td><?php echo (int) @$results['bar-m']->signups; ?></td>
			<td class="total"><?php echo (int) @$results['bar-y']->signups + (int) @$results['bar-m']->signups; ?></td>
			
			<td><?php echo (int) @$results['bar-n']->signups; ?></td>
			<td class="total"><?php echo @$results['bar-y']->signups + @$results['bar-m']->signups + @$results['bar-n']; ?></td>
		</tr>
		<tr>
			<th>Bass</th>
			<td><?php echo (int) @$results['b-y']->signups; ?></td>		
			<td><?php echo (int) @$results['b-m']->signups; ?></td>
			<td class="total"><?php echo (int) @$results['b-y']->signups + (int) @$results['b-m']->signups; ?></td>
			
			<td><?php echo (int) @$results['b-n']->signups; ?></td>
			<td class="total"><?php echo @$results['b-y']->signups + @$results['b-m']->signups + @$results['b-n']; ?></td>
		</tr>
		<tr>
			<th>Other</th>
			<td><?php echo (int) @$results['-y']->signups; ?></td>
			<td><?php echo (int) @$results['-m']->signups; ?></td>
			<td class="total"><?php echo (int) @$results['-y']->signups + (int) @$results['-m']->signups; ?></td>
			
			<td><?php echo (int) @$results['-n']->signups; ?></td>
			<td class="total"><?php echo @$results['-y']->signups + @$results['-m']->signups + @$results['-n']; ?></td>
		</tr>
		<?php }
																			else { ?>
		<tr>
			<th>Members</th>
			<td><?php echo (int) @$results['y']->signups; ?></td>
			<td><?php echo (int) @$results['m']->signups; ?></td>
			<td class="total"><?php echo (int) @$results['y']->signups + (int) @$results['m']->signups; ?></td>
			
			<td><?php echo (int) @$results['n']->signups; ?></td>
			<td class="total"><?php echo @$results['y']->signups + @$results['m']->signups + @$results['n']; ?></td>
		</tr>
		<?php } ?>
		<?php
										  if ($guest) {
		?>
		<tr>
			<th>Guests</th>
			<td><?php echo (int) @$guest['y']; ?></td>
			<td><?php echo (int) @$guest['m']; ?></td>
			<td class="total"><?php 
											  $guest_ym = (int) @$guest['y'] + (int) @$guest['m'];
											  echo $guest_ym;
				?></td>
			
			<td>&mdash;</td>
			<td class="total"><?php echo $guest_ym; ?></td>
		</tr>
		
		<?php
										  }
		?>
		
		
	</tbody>
	<tfoot>
		<tr>
			<th style="padding:0;border-top:1px solid #DFDFDF;"><h3>Total</h3></th>
			<th style="padding:0;border-top:1px solid #DFDFDF;text-align:right;"><h3><?php
										  $y_total = @$results['y']->signups + @$results['t1-y']->signups + @$results['t2-y']->signups + @$results['bar-y']->signups + @$results['b-y']->signups + @$results['-y']->signups;
										  echo $y_total + @$guest['y'];
				?></h3></th>
			<th style="padding:0;border-top:1px solid #DFDFDF;text-align:right;"><h3><?php
										  $m_total =  @$results['m']->signups + @$results['t1-m']->signups + @$results['t2-m']->signups + @$results['bar-m']->signups + @$results['b-m']->signups + @$results['-m']->signups;
										  echo $m_total + @$guest['m'];
				?></h3></th>
			
			<th style="padding:0;border-top:1px solid #DFDFDF;text-align:right;"><h3><strong><?php
										  echo $y_total + $m_total + $guest_ym;
				?></strong></h3></th>
			
			
			<th style="padding:0;border-top:1px solid #DFDFDF;text-align:right;"><h3><?php
										  $n_total = @$results['n']->signups + @$results['t1-n']->signups + @$results['t2-n']->signups + @$results['bar-n']->signups + @$results['b-n']->signups + @$results['-n']->signups;
										  echo $n_total;
				?></h3></th>
			<th style="padding:0;border-top:1px solid #DFDFDF;text-align:right;"><h3><strong><?php
										  echo $y_total + $m_total + $n_total + $guest_ym;
				?></strong></h3></th>
		</tr>
	</tfoot>
</table>
<?php
}

/* ---------------------------------------------------------------- */

function signup_responses($field, $response) {
	if ($response) {
		switch ($field) {
			case 'dietary':
			$signup_types = array(
				'vegetarian' => "Vegetarian",
				'vegan' => "Vegan",
				'dairy' => "Dairy",
				'wheat' => "Wheat",
				'nut' => "Nuts",
				'seafood' => "Seafood",
			);
			break;
			case 'bus_guest':
			case 'meal_guest':
			return $response;
			default:
			$signup_types = array('y'=>'Yes','n'=>'No','m'=>'Maybe');
		}
		return $signup_types[$response] ? $signup_types[$response] : '<i>'.ucfirst($response).'</i>';
	}
	else {
		return '&mdash;';
	}
}

/* ---------------------------------------------------------------- */

function iter_users($part, $users, $signups=false, $heads=false) {
	$count = 0;
	
	$part_name = $part == '-' ? 'Other' : format_voicepart($part);
	
	$user_signups = array();
	
	if ($users = $users[$part]) {
		if ( !empty( $users->results ) ) {
			foreach ($users->results as $user) {
				if (array_intersect($user->roles, array('member', 'section_leader', 'committee'))) {
					$attend = signup_responses('attend', @$signups[$user->ID]->attend);
					
					$signup_meta = unserialize(@$signups[$user->ID]->signup_meta);
					
					$allheads = '';
					if (is_array($heads)) {
						unset($heads['attend']);
						foreach ($heads as $field=>$title) {
							$meta_all = $signup_meta[$field];
							
							if (is_array($meta_all)) {
								foreach ($meta_all as &$meta_value) {
									$meta_value = signup_responses($field, $meta_value);
								}
								$meta = implode(', ', $meta_all);
							}
							else {							
								
								$meta = signup_responses($field, $meta_all);
							}
							
							$allheads.= "<td>{$meta}</td>";
						}
					}
					
					$us = array();
					$us['name'] = $user->display_name;
					$us['attend'] = $attend;
					$us['allheads'] = $allheads;
					
					$user_signups[] = $us;
					
					//$count++;
				}
			}
		}
	}
	
	foreach ($user_signups as $key => $row) {
		$user_names[$key]  = $row['name'];
		$user_attends[$key] = $row['attend'];
	}
	
	array_multisort($user_attends, SORT_DESC, $user_names, SORT_ASC, $user_signups);
	
	foreach ($user_signups as $us) {
		$return .= "<tr><td><strong>{$part_name}</strong></td><td>{$us['attend']}</td><td>{$us['name']}</td>{$us['allheads']}</tr>";
	}
	
	$count = count($user_signups);
	
	if ($count==0) {
		$cols = count($heads)+1;
		return "<tr><td><strong>{$part_name}</strong></td><td colspan=\"{$cols}\">None found.</td></tr>";
	}
	else {
		return $return;
	}
	//echo '</ul>';
}

/* ---------------------------------------------------------------- */

function iter_users_bus($signups, $heads=false) {
	$count = 0;
	
	$user_signups = array();
	
	if ( !empty( $signups ) ) {
		foreach ($signups as $user) {
			
			$signup_meta = unserialize($user->signup_meta);
			//print_pre($signup_meta);
			
			if ($signup_meta['bus'] != 'n') {
				
				$allheads = '';
				if (is_array($heads)) {
					unset($heads['attend']);
					foreach ($heads as $field=>$title) {
						$meta_all = $signup_meta[$field];
						
						if (is_array($meta_all)) {
							foreach ($meta_all as &$meta_value) {
								$meta_value = signup_responses($field, $meta_value);
							}
							$meta = implode(', ', $meta_all);
						}
						else {							
							$meta = signup_responses($field, $meta_all);
						}
						
						$allheads.= "<td>{$meta}</td>";
					}
				}
				
				$us = array();
				$us['name'] = $user->display_name;
				$us['allheads'] = $allheads;
				$us['attend'] = signup_responses('attend', $user->attend);
				
				//print_pre($us);
				
				$user_signups[] = $us;
				
			}
			
			//$count++;
		}
	}
	
	
	/*foreach ($user_signups as $key => $row) {
	$user_names[$key]  = $row['name'];
	$user_attends[$key] = $row['bus'];
	}
	
	array_multisort($user_attends, SORT_DESC, $user_names, SORT_ASC, $user_signups);*/
	
	foreach ($user_signups as $us) {
		$return .= "<tr><td>{$us['name']}</td>{$us['allheads']}<td>{$us['attend']}</td></tr>";
	}
	
	$count = count($user_signups);
	
	if ($count==0) {
		$cols = count($heads)+1;
		return "<tr><td colspan=\"{$cols}\">None found.</td></tr>";
	}
	else {
		return $return;
	}
	//echo '</ul>';
}

/* ---------------------------------------------------------------- */

function signup_list_head($field, $width=20) {
	return "<th style=\"padding:0;width:{$width}%\"><h3>{$field}</h3></th>";
}

/* ---------------------------------------------------------------- */

function signup_all_heads($heads=false) {
	
	if (!$heads) $heads = signup_get_heads();
	$listheads = '';
	
	if (count($heads)) {
	$width = 40/count($heads);
	
	foreach ($heads as $field=>$title) {
		$listheads.=signup_list_head($title, $width);
	}
	}
	
	return $listheads;
}

function signup_get_heads() {
	global $post;
	$custom = get_post_custom($post->ID);
	//	$heads = array('attend'=>'Signup');
	
	if ($custom['signup_bus'][0]) $heads['bus'] = 'Bus';
	if ($custom['signup_bus_guest'][0]) $heads['bus_guest'] = 'Guests';
	if ($custom['signup_meal'][0]) $heads['meal'] = 'Meal';
	if ($custom['signup_meal_guest'][0]) $heads['meal_guest'] = 'Guests';
	if ($custom['signup_dietary'][0]) $heads['dietary'] = 'Dietary';  //ETC
	
	return $heads;
}

/* ---------------------------------------------------------------- */

function set_signups_list_meta() {
	global $post, $wpdb;
	
	$query = $wpdb->prepare("SELECT user_id, attend, signup_meta FROM {$wpdb->prefix}lowrez_signups
WHERE event_id = %d", $post->ID);
	
	$signups = $wpdb->get_results($query, OBJECT_K);
	
	$parts = array('t1','t2','bar','b');
	$users = array();
	foreach ($parts as $part) {
		$users[$part] = new WP_User_Query(
			array(
				'meta_key' => 'voicepart',
				'meta_value' => $part
			)
		);
	}
	$users['-'] = new WP_User_Query(
		array('meta_query' => array(
			array(
				'key' => 'voicepart',
				'value' => $parts,
				'compare' => 'NOT IN'
			)
		) )
	);
	
?>
<script type="text/javascript">
	jQuery(function () {
		jQuery("#signups_list").tablegroup(0, 2, true);
		jQuery('#signup_datelist .newdate a').data('addnew', jQuery('#signup_datelist .newdateblank').clone()).click( function(e) {
			e.preventDefault();
			jQuery('#signup_datelist .newdate').before(jQuery(this).data('addnew').clone());
		});
		jQuery('#signup_datelist').on('click', 'a.delbutton', function(e) {
			e.preventDefault();
			jQuery(this).closest('li').remove();
		}); 
	});
</script>
<table id="signups_list" class="signup-custom">
	<?php
	$heads = signup_get_heads();
	$allheads = signup_all_heads($heads);
	?>
	<?php $groupheads = '<tr>'. signup_list_head('Part').signup_list_head('Signup').signup_list_head('Member').$allheads . '</tr>'; ?>
	<?php
	$parts[] = '-';
	foreach ($parts as $part) {
		echo $groupheads;
		echo iter_users($part, $users, $signups, $heads);
	} ?>
</table>
<?php
}

/* ---------------------------------------------------------------- */

function set_signups_bus_list_meta() {
	global $post, $wpdb;
	
	$query = $wpdb->prepare("
SELECT s.user_id, attend, signup_meta, display_name
FROM {$wpdb->prefix}lowrez_signups s
INNER JOIN {$wpdb->users} u
ON s.user_id = u.ID
WHERE event_id = %d
ORDER BY display_name", $post->ID);
	
	$signups = $wpdb->get_results($query, OBJECT_K);
	
?>
<table id="signups_bus_list" class="signup-custom">
	<?php
$heads = array();	
$heads['bus'] = 'Bus'; $heads['bus_guest'] = 'Guests';
	?>
	<?php $groupheads = '<tr>'. signup_list_head('Member').signup_list_head('Bus').signup_list_head('Guests').signup_list_head('Singing') . '</tr>';
	echo $groupheads;?>
	<?php
		echo iter_users_bus($signups, $heads);
	?>
</table>
<?php
}


/* ---------------------------------------------------------------- */

function set_signup_meta() {
	global $post;
	$custom = get_post_custom($post->ID);
?>
<table class="signup-custom signup-custom-meta">
	<tr>
		<th colspan="2" style="padding:0;"><h3>Information</h3></th>
		<th style="padding:0;"><h3>Status</h3></th>
	</tr>
	<tr>
		<td colspan="2">
			<textarea rows="1" cols="40" name="excerpt" id="excerpt" style="max-width:570px;"><?php echo get_the_excerpt($post->ID); ?></textarea>
		</td>
		<td><select id="signup_open" name="signup_open">
			<option value="open" <?php selected('open', $custom['signup_open'][0]); ?>>Open</option>
			<option value="close" <?php selected('close', $custom['signup_open'][0]); ?>>Closed</option>
			</select>
		</td>
	</tr>
	<tr>
		<th style="padding:0;"><h3>Date(s)</h3></th>
		<th style="padding:0;"><h3>Standard Questions</h3></th>
		<th style="padding:0;"><h3>Additional Questions</h3></th>
	</tr>
	<tr>
		<td>
			<ul id="signup_datelist" class="tagchecklist">
				<?php
	$dates = unserialize($custom['signup_dates'][0]);
	if (is_array($dates)) {
		foreach($dates as $date) { ?>
				<li><input type="text" name="signup_dates[]" value="<?php echo $date; ?>" />&nbsp;<span><a class="delbutton">x</a></span></li>
				<?php }
	} ?>
				<li class="newdateblank"><input type="text" name="signup_dates[]" value="" placeholder="New" />&nbsp;<span><a class="delbutton">x</a></span></li>
				<li class="newdate"><a href="#" class="button button-small">Add Date</a></li>
			</ul>
		</td>
		<td>
			<ul>
				<li><label><input type="checkbox" name="signup_bus" <?php checked($custom['signup_bus'][0]); ?> value="1" /> Bus</label>
				</li>
				<li><label><input type="checkbox" name="signup_bus_guest" <?php checked($custom['signup_bus_guest'][0]); ?> value="1" /> Guests allowed?</label>
					<label for="signup_bus_guest_max">Max</label>
					<select id="signup_bus_guest_max" name="signup_bus_guest_max" style="width:50px;min-width:50px;">
						<?php for ($i = 1; $i <= 10; $i++) {
		echo '<option value="'.$i.'"'. selected($i, $custom['signup_bus_guest_max'][0]) .'>'.$i.'</option>'.PHP_EOL;
	} ?>
					</select>
				</li>
				
				<li><label for="signup_bus_cost">Bus Cost</label> <input type="text" id="signup_bus_cost" name="signup_bus_cost" value="<?php echo $custom['signup_bus_cost'][0]; ?>" /></li>
				<li><hr></li>			
				<li><label><input type="checkbox" name="signup_meal" <?php checked($custom['signup_meal'][0]); ?> value="1" /> Meal</label></li>
					<li>
					<label><input type="checkbox" name="signup_meal_guest" <?php checked($custom['signup_meal_guest'][0]); ?> value="1" /> Guests allowed?</label>
										<label for="signup_meal_guest_max">Max</label>
						<select id="signup_meal_guest_max" name="signup_meal_guest_max" style="width:50px;min-width:50px;">
						<?php for ($i = 1; $i <= 10; $i++) {
		echo '<option value="'.$i.'"'. selected($i, $custom['signup_meal_guest_max'][0]) .'>'.$i.'</option>'.PHP_EOL;
	} ?>
					</select>
				</li>
				<li><label for="signup_meal_cost">Meal Cost</label> <input type="text" id="signup_meal_cost" name="signup_meal_cost" value="<?php echo $custom['signup_meal_cost'][0]; ?>" /></li>
				<li><label><input type="checkbox" name="signup_dietary" <?php checked($custom['signup_dietary'][0]); ?> value="1" /> Ask for dietary requirements?</label></li>
			</ul>
		</td>
		<td>
			Under construction
		</td>
	</tr>
</table>
<?php
	echo '<input type="hidden" name="signup_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}

/* ---------------------------------------------------------------- */

function save_signup_meta($post_id) {
	
	global $post;
	
	// make sure data came from our meta box
	if (!wp_verify_nonce($_POST['signup_meta_noncename'],__FILE__)) return $post_id;
	
	if (@$_POST['post_type'] == "signup") {
		
		update_post_meta($post_id, 'signup_open', @$_POST['signup_open']);
		
		update_post_meta($post_id, 'signup_dates', array_filter($_POST['signup_dates']));
		
		update_post_meta($post_id, 'signup_bus', @$_POST['signup_bus']);
		update_post_meta($post_id, 'signup_bus_cost', @$_POST['signup_bus_cost']);
		update_post_meta($post_id, 'signup_bus_guest', @$_POST['signup_bus_guest']);
		update_post_meta($post_id, 'signup_bus_guest_max', @$_POST['signup_bus_guest_max']);
		
		update_post_meta($post_id, 'signup_meal', @$_POST['signup_meal']);
		update_post_meta($post_id, 'signup_meal_cost', @$_POST['signup_meal_cost']);
		update_post_meta($post_id, 'signup_meal_guest', @$_POST['signup_meal_guest']);
		update_post_meta($post_id, 'signup_meal_guest_max', @$_POST['signup_meal_guest_max']);
		
		update_post_meta($post_id, 'signup_dietary', @$_POST['signup_dietary']);
		
		
	}
}
add_action("save_post", "save_signup_meta");

/* ---------------------------------------------------------------- */

add_action( 'admin_print_styles-post.php',     'signup_css');
add_action( 'admin_print_styles-post-new.php', 'signup_css');

function signup_css() {
	global $post_type;
	if( !in_array($post_type, array('signup') ) ) return;
?>
<style type="text/css">
	
	#poststuff #signup_meta .inside,
	#poststuff #signups_summary_meta .inside,
	#poststuff #signups_bus_summary_meta .inside,
	#poststuff #signups_meal_summary_meta .inside,
	#poststuff #signups_list_meta .inside,
	#poststuff #signups_bus_list_meta .inside
	{
		/*margin-left:0;
		margin-right:0;
		padding-left:0;
		padding-right:0;*/
		margin:0;
		padding:0;
	}
	
	#poststuff #signups_summary_meta td,
	#poststuff #signups_bus_summary_meta td,
	#poststuff #signups_meal_summary_meta td {
		text-align:right;
	}
	
	#signup_media_meta .tabs-panel {
		height: 200px;
	}
	
	#postimagediv img {
		max-width:220px;
		max-height:220px;
	}
	
	table.signup-custom select {
		max-width:300px;
		min-width: 180px;
	}
	
	table.signup-custom.signup-custom-meta th,
	table.signup-custom.signup-custom-meta td {
		/*width:25%;*/
	}
	
	table.signup-custom.signup-media-custom-meta th,
	table.signup-custom.signup-media-custom-meta td {
		/*width:33%;*/
	}
	
	table.signup-custom {
		border-collapse: separate;
		border-spacing: 0px;
		max-width:100%;
		border-top: none;/*1px solid #dfdfdf;*/
		border-bottom: none; /*1px solid #fff;*/
	}
	
	table.signup-custom-meta {
		width:100%;
	}
	
	table.signup-custom th {
		/*width:150px;*/
	}
	
	table.signup-custom th,
	table.signup-custom td {
		/*height: 24px;*/
		text-align:left;
		vertical-align:top;
		padding: 6px;
		border-top: 1px solid #fff;
		border-bottom: 1px solid #dfdfdf;
	}
	table.signup-custom tr:first-child th,
	table.signup-custom tr:first-child td {
		border-top:none;
	}
	table.signup-custom tr:last-child th,
	table.signup-custom tr:last-child td {
		border-bottom:none;
	}
	
	#signup_media_title .postbox .inside {
		padding:0;
	}
	
	#signup_media_title {
		background: none repeat scroll 0 0 transparent;
		border: medium none;
	}
	#signup_media_title .hndle, #signup_media_title .handlediv {
		display: none;
	}
	
	.signup-custom td,
	.signup-custom th {
		border-left: 1px solid #dfdfdf;
	}
	
	.signup-custom tr td:first-child,
	.signup-custom tr th:first-child {
		border-left: none;
	}
	
	.signup-custom th h3 {
			font-family:sans-serif !important;
			font-size: 12px !important;
			cursor:default !important;
			padding: 6px !important;
		}
	
	.signup-custom td.total {
		background: rgb(236, 236, 236) !important;
		font-weight:bold !important;
	}
	
	.signup-custom th.total h3 {
		font-weight:bold !important;
	}
	
</style>
<?php
}

/* ---------------------------------------------------------------- */

add_action('admin_head', 'signup_column_widths');

function signup_column_widths() {
	//echo $GLOBALS['wp_query']->request;
	global $current_screen;
	if ($current_screen->id == 'edit-signup') {
?>
<style type="text/css">
	.column-signup_responded {
		border-left: 1px solid #dfdfdf !important;
		border-right: 1px solid #dfdfdf !important;
	}
	
	.column-title {
			width: 200px;
	}
	
	.column-signup_open,
	.column-signup_dates
	{
		width:100px;
	}
	
	.column-signup_bus,
	.column-signup_meal,
	.column-signup_responded,
	.column-signup_y,
	.column-signup_m,
	.column-signup_n
	{
		width: 60px;
		text-align:center !important;
	}
	
</style>
<?php 
	}
}

/* ---------------------------------------------------------------- */

function change_signup_columns( $cols ) {
	
	unset($cols['protected']);
	unset($cols['date']);
	
	$cols['title'] = 'Event';
	
	$cols['signup_open'] = 'Status';
	$cols['signup_dates'] = 'Date(s)';
	
	$cols['signup_bus'] = 'Bus';
	$cols['signup_meal'] = 'Meal';
	
	$cols['signup_responded'] = 'Total';
	
	$cols['signup_y'] = 'Yes';
	$cols['signup_m'] = 'Maybe';
	$cols['signup_n'] = 'No';
	
	return $cols;
}
add_filter( "manage_signup_posts_columns", 'change_signup_columns' );

/* ---------------------------------------------------------------- */

function set_signup_column( $column, $post_id ) {
	switch ( $column ) {
		case 'signup_open':
		$status = get_post_meta($post_id, 'signup_open', true);
		$statuses = array('open' => 'Open', 'close' => 'Closed');
		$status = @$statuses[$status];
		echo $status ? $status : '&mdash;';
		
		break;
		case 'signup_dates':
		$dates = get_post_meta($post_id, 'signup_dates', true);
		if (is_array($dates)) {
			foreach ($dates as $date) {
				echo date('j M Y', strtotime($date)).'<br>';
			}
		}
		break;
		
		case 'signup_bus':
		case 'signup_dietary':
		case 'signup_meal':
		echo get_post_meta($post_id, $column, true) ? 'Y' : '&mdash;';
		break;
		
		case 'signup_responded':
		
		global $wpdb;
		$query = $wpdb->prepare("SELECT Count(*) AS signups FROM {$wpdb->prefix}lowrez_signups WHERE event_id=%d", $post_id);
		echo $wpdb->get_var($query);
		
		break;
		
		case 'signup_y':
		case 'signup_m':
		case 'signup_n':
		
		global $wpdb;
		$status = trim(strstr($column, '_'), '_');
		$query = $wpdb->prepare("SELECT Count(*) AS signups FROM {$wpdb->prefix}lowrez_signups WHERE event_id=%d AND attend=%s;", $post_id, $status);
		echo $wpdb->get_var($query);
		break;
		
	}
}

add_action( 'manage_signup_posts_custom_column' , 'set_signup_column', 10, 2 );

/* ---------------------------------------------------------------- */	

function signup_updated_messages( $messages ) {
  global $post, $post_ID;

$messages['signup'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Signup updated. <a href="%s">View signup</a>', 'lowrez'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.', 'lowrez'),
    3 => __('Custom field deleted.', 'lowrez'),
    4 => __('Signup updated.', 'lowrez'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Signup restored to revision from %s', 'lowrez'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Signup published. <a href="%s">View signup</a>', 'lowrez'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Signup saved.', 'lowrez'),
    8 => sprintf( __('Signup submitted. <a target="_blank" href="%s">Preview signup</a>', 'lowrez'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Signup scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview signup</a>', 'lowrez'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Signup draft updated. <a target="_blank" href="%s">Preview signup</a>', 'lowrez'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}
add_filter( 'post_updated_messages', 'signup_updated_messages' );

/* ---------------------------------------------------------------- */	