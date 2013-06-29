<?php
/*
Plugin Name: LOW REZ Signups
Description: LOW REZ Member signups
Version: 2.0.2
Author: LOW REZ
*/

global $lowrez_signups_version;
$lowrez_signups_version = "2.0.2";

require_once('signups/install.php');
register_activation_hook(__FILE__, 'lowrez_signups_install');

require_once('signups/admin.php');
require_once('signups/frontend.php');