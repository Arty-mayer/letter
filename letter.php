<?php

/*
Plugin Name: Letter
Description: A brief description of the Plugin.
Version: 1.0
Author: Artem
License: A "Slug" license name e.g. GPL2
*/

if (!function_exists('add_action')) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

const LETTERS_VERSION = '1.0';
define('LETTER_PLUGIN_DIR', rtrim(plugin_dir_path(__FILE__), "\/\\"));
define('LETTER_PLUGIN_URL', plugins_url('/', __FILE__));

spl_autoload_register(function ($class) {
	$prefix = 'letter';
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		return;
	}
	$relative_class = substr($class, $len);
	$file = LETTER_PLUGIN_DIR . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';
	if (file_exists($file)) {
		require_once $file;
	}
});

add_shortcode('letter_pl', 'view_letter');

/**
 *
 * @return string;
 *
 *
 */
function view_letter() : string
{
	return "Hallo, i'm letter-plugin";
}

use letter\Admin\Admin;

if (is_admin()) {
	$Admin = new Admin();
}
