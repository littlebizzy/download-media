<?php
/*
Plugin Name: Download Media
Plugin URI: https://www.littlebizzy.com
Description: Quickly and easily download a raw file of any media currently uploaded on your WordPress website without requiring SFTP info or fancy dependencies.
Version: 1.0
Author: LittleBizzy
Author URI: https://www.littlebizzy.com
License: GPL3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Avoid script calls via plugin URL
if (!function_exists('add_action'))
	die;

// This plugin constants
define('DWNMDA_FILE', __FILE__);
define('DWNMDA_PATH', dirname(DWNMDA_FILE));
define('DWNMDA_VERSION', '1.0.0');

// Restricted to the admin area but not in AJAX mode
if (!is_admin())
	return;

// Load main class
require_once DWNMDA_PATH.'/admin/admin.php';
DWNMDA_Admin::instance();