<?php

  function lowrez_signups_install() {

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

    global $wpdb;
    global $lowrez_signups_version;

    // ---- Signups
    $lowrez_signups = $wpdb->prefix . "lowrez_signups";

	$sql = "CREATE TABLE $lowrez_signups (
		id int(10) unsigned NOT NULL AUTO_INCREMENT,
		event_id int(10) unsigned NOT NULL,
		user_id int(10) unsigned NOT NULL,
		attend varchar(1) DEFAULT NULL,
		signup_meta text,
		timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (id)
	);";

    dbDelta($sql);

/*$sql = "UPDATE $lowrez_signups
SET attend = yes_no 
WHERE attend IS NULL
AND yes_no IS NOT NULL";

$sql = "ALTER TABLE $lowrez_signups
DROP yes_no";

$sql = "ALTER TABLE  $lowrez_signups
CHANGE  yes_no
attend VARCHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";*/

	update_option("lowrez_signups_version", $lowrez_signups_version);

  }


  function lowrez_signups_update_db_check() {
    global $lowrez_signups_version;
    if ( get_site_option('lowrez_signups_version') != $lowrez_signups_version ) {
      lowrez_signups_install();
    }
  }

  add_action('plugins_loaded', 'lowrez_signups_update_db_check');