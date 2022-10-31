<?php
/**
 * Plugin Name:       42Web - Donor Honor Roll
 * Plugin URI:        https://github.com/BODA82/fourtwo-donor-honor-roll
 * Description:       WordPress plugin for displaying a donor honor roll.
 * Version:           1.0.0
 * Requires at least: 4.9
 * Author:            Christopher Spires
 * Author URI:        https://cspir.es
 */
 
 // Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// Plugin definitions
define('FOURTWO_DONOR_VER', '1.0.0');

// Autoload Composer dependencies
require_once __DIR__ . '/vendor/autoload.php';

// Plugin classes
require_once 'classes/class-plugin.php';

new FourTwo_Donors();