<?php

function signup_head() {
	$post_id = get_the_ID();
	
	$dates = get_post_meta($post_id, 'signup_dates', true);
	if (is_array($dates)) {
		foreach ($dates as &$date) {
			$date = date('j F Y', strtotime($date));
		}
		$dates = implode(', ', $dates);
	}
	return $dates;
}

function do_guest_dropdown($max) {
	$types = array();
	for ($i = 0; $i <= $max; $i++) {
		$types[$i] = $i;
	}
	return $types;
}

function signup_body() {
	$post_id = get_the_ID();
	
	global $wpdb, $current_user;
	$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}lowrez_signups WHERE event_id=%d AND user_id=%d;", $post_id, $current_user->ID);
	$user_signup = $wpdb->get_row($query, ARRAY_A);
	
	$open = get_post_meta($post_id, 'signup_open', true) == 'open';
	
	if ($_POST && $open) {
		
		$signup_meta = array(
			'bus' => $_POST['bus'],
			'bus_guest' => $_POST['bus_guest'],
			'meal' => $_POST['meal'],
			'meal_guest' => $_POST['meal_guest'],
			'dietary' => $_POST['dietary'],
			'dietary_other' => $_POST['dietary_other'],
		);
		
		$signup_meta = serialize($signup_meta);
		
		if ($user_signup) {
			$user_signup['signup_meta'] = $signup_meta;
			$user_signup['attend'] = $_POST['attend'];
			$user_signup['signup_meta'] = $signup_meta;
			//unset($user_signup['id']);
			unset($user_signup['timestamp']);
			$wpdb->update($wpdb->prefix.'lowrez_signups', $user_signup, array('id' => $user_signup['id']));
		}
		else {
			$user_signup = array();
			$user_signup['event_id'] = $_POST['event_id'];
			$user_signup['user_id'] = $current_user->ID;
			$user_signup['attend'] = $_POST['attend'];
			$user_signup['signup_meta'] = $signup_meta;
			$wpdb->insert($wpdb->prefix.'lowrez_signups', $user_signup);
		}
	}
	
	$signup_meta = unserialize($user_signup['signup_meta']);
	unset($user_signup['signup_meta']);

	$signup_modify = $user_signup ? 'Modify Signup' : 'Sign Up';
	$user_signup = @array_merge($user_signup, $signup_meta);
	


	$user_signup['signup_open'] = $open;
	
	$signup_submit = $open == 'open' ?
		"<input value=\"{$signup_modify}\" class=\"btn btn-small btn-primary\" type=\"submit\">" :
	'<span class="btn btn-small btn-primary disabled">Signup Closed</span>';
	
	$attend_types = array(
		'y' => "Yes",
		'm' => "Unsure",
		'n' => "No",
	);
	$attend_types = iter_responses('attend', $attend_types, $user_signup);
	
	$dietary_types = array(
		'vegetarian' => "Vegetarian",
		'vegan' => "Vegan",
		'dairy' => "No dairy/ lactose",
		'wheat' => "No wheat/ gluten",
		'nut' => "No nuts",
		'seafood' => "No seafood",
	);
	$dietary_types = iter_responses('dietary', $dietary_types, $user_signup, true);
	
	$bus_types = array(
		'y' => "Yes",
		'm' => "Unsure",
		'n' => "No",
	);
	$bus_types = iter_responses('bus', $bus_types, $user_signup);
	
	$attend = "<fieldset>
<h5 class=\"\">Will you be singing in the concert?</h5>
<p>{$attend_types}</p>
</fieldset>";
	
	$dietary = get_post_meta($post_id, 'signup_dietary', true) ? "<fieldset>
<h5 class=\"\">Dietary requirements</h5>
{$dietary_types}
<label for=\"dietary_other\">Other</label> <input type=\"text\" name=\"dietary_other\" id=\"dietary_other\" value=\"{$user_signup['dietary_other']}\">
</fieldset>" : false;
	
	$bus = false;
	
	if (get_post_meta($post_id, 'signup_bus', true)) {
		
		$bus_cost = get_post_meta($post_id, 'signup_bus_cost', true);
		if (empty($bus_cost)) { $bus_cost = 'to be advised'; }
		
		if (get_post_meta($post_id, 'signup_bus_guest', true)) {
			
			$bus_guest_max = get_post_meta($post_id, 'signup_bus_guest_max', true);
			if (!$bus_guest_max) $bus_guest_max = 1;
			
			$guest_dropdown = iter_responses_dropdown('bus_guest', do_guest_dropdown($bus_guest_max), $user_signup);
			
			$bus_guest = "<h5>Will you bring a partner/ friend(s) on the bus? <span class=\"label label-warning\">New!</span></h5>
<p>{$guest_dropdown} people (excluding yourself).</p>";
			
		}
		
		$bus = "<fieldset>
<!--<legend>Bus</legend>-->
<h5 class=\"\">Will you be taking the bus?</h5>
<p>Cost is {$bus_cost}.</p>
<p>{$bus_types}</p>
{$bus_guest}
</fieldset>";
		
	}
	
	$meal_cost = get_post_meta($post_id, 'signup_meal_cost', true);
	if (empty($meal_cost)) { $meal_cost = 'to be advised'; }
	
	$meal_guest_max = get_post_meta($post_id, 'signup_meal_guest_max', true);
	if (!$meal_guest_max) $meal_guest_max = 1;
	
	$meal_guest = get_post_meta($post_id, 'signup_meal_guest', true) ? "<p>I will bring my partner/ friend(s):<br>{$guest_dropdown} people (excluding me).</p>" : false;
	
	$meal = get_post_meta($post_id, 'signup_meal', true) ? "<fieldset>
<!--<legend>meal</legend>-->
<h5 class=\"\">Will you be eating dinner/ lunch?</h5>
<p>Cost is {$meal_cost}.</p>
<p>{$meal_types}</p>
{$meal_guest}
</fieldset>" : false;
	
	$script = $_SERVER['REQUEST_URI'];
	
	$excerpt = get_the_excerpt($post_id);
	
	$form = "
<p>{$excerpt}</p>
<form action=\"{$script}\" method=\"post\">
<input type=\"hidden\" name=\"event_id\" value=\"{$post_id}\">
{$attend}
{$dietary}
{$bus}
{$meal}
<div style=\"border-top: 1px solid rgb(238, 238, 238);padding-top: 10px;margin-top: 10px;\">
{$signup_submit}
</div>
</form>";
	
	$open = $open ? '' : '<span class="label">Signups have closed for this event.</span>';
	
	return $open.$form;
}


function iter_responses($field, $responses, $user_signup, $checkbox = false) {
	$multiple = $checkbox ? '[]' : false;
	$checkbox = $checkbox ? 'checkbox' : 'radio';
	$disabled = disabled($user_signup['signup_open'] != 'open', true, false);
	
	foreach ($responses as $type => &$response) {
		if ($multiple) {
			//print_pre($user_signup);
			if (is_array($user_signup[$field])) {
				$checked = in_array($type, $user_signup[$field]) ? ' checked="checked"' : false;
			}
			else {
				$checked = false;
			}
		}
		else {
			$checked = checked($type, $user_signup[$field], false);
		}
		$response = "<label class=\"{$checkbox} inline\"><input type=\"{$checkbox}\" id=\"{$field}_{$type}\" name=\"{$field}{$multiple}\" value=\"{$type}\" {$checked}{$disabled}/> {$response}</label>";
	}
	return implode(PHP_EOL, $responses);
}

function iter_responses_dropdown($field, $responses, $user_signup) {
	
	foreach ($responses as $type => &$response) {
		if ($multiple) {
			if (is_array($user_signup[$field])) {
				$checked = in_array($type, $user_signup[$field]) ? ' selected="selected"' : false;
			}
			else {
				$checked = false;
			}
		}
		else {
			$checked = selected($type, $user_signup[$field], false);
		}
		$response = "<option value=\"{$type}\" {$checked}/>{$response}</option>";
	}
	return "<select id=\"{$field}\" name=\"{$field}\" style=\"width:auto;\">".implode(PHP_EOL, $responses)."</select>";
}