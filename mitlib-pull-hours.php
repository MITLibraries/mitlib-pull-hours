<?php
/**
 * Plugin Name:   MITlib Pull Hours
 * Plugin URI:    https://github.com/MITLibraries/mitlib-pull-hours
 * Description:   A WordPress plugin that populates a local JSON cache from a Google Spreadsheet.
 * Version:       0.2.0-beta3
 * Author:        MIT Libraries
 * Author URI:    https://github.com/MITLibraries
 * Licence:       GPL2
 *
 * @package MITlib Pull Hours
 * @author MIT Libraries
 * @link https://github.com/MITLibraries/mitlib-pull-hours
 */

namespace mitlib;

// Don't call the file directly!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the necesary classes.
include_once( 'class-pull-hours-dashboard.php' );
include_once( 'class-pull-hours-display-widget.php' );
include_once( 'class-pull-hours-harvester.php' );
include_once( 'class-pull-hours-settings.php' );
include_once( 'class-pull-hours-widget.php' );

// Call the class' init method as part of dashboard setup.
add_action( 'admin_init', array( 'mitlib\Pull_Hours_Settings', 'init' ) );
add_action( 'wp_dashboard_setup', array( 'mitlib\Pull_Hours_Widget', 'init' ) );
add_action( 'admin_menu', array( 'mitlib\Pull_Hours_Dashboard', 'init' ) );
add_action( 'widgets_init', array( 'mitlib\Pull_Hours_Display_Widget', 'init' ) );
