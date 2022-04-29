<?php
/**
 * Plugin Name:   MITlib Pull Hours
 * Plugin URI:    https://github.com/MITLibraries/mitlib-pull-hours
 * Description:   A WordPress plugin that populates a local JSON cache from a Google Spreadsheet.
 * Version:       0.4.0
 * Author:        MIT Libraries
 * Author URI:    https://github.com/MITLibraries
 * Licence:       GPL2
 *
 * @package MITlib Pull Hours
 * @author MIT Libraries
 * @link https://github.com/MITLibraries/mitlib-pull-hours
 */

namespace Mitlib\Pullhours;

// Don't call the file directly!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Use the Composer autoloader to get all needed classes.
require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Call the class' init method as part of dashboard setup.
add_action( 'admin_init', array( 'Mitlib\Pullhours\Settings', 'init' ) );
add_action( 'wp_dashboard_setup', array( 'Mitlib\Pullhours\AdminWidget', 'init' ) );
add_action( 'admin_menu', array( 'Mitlib\Pullhours\Dashboard', 'init' ) );
add_action( 'widgets_init', array( 'Mitlib\Pullhours\DisplayWidget', 'init' ) );
add_action( 'widgets_init', array( 'Mitlib\Pullhours\DisplayWidgetSlim', 'init' ) );

add_action( 'wp_dashboard_setup', array( 'Mitlib\Pullhours\TestAdminWidget', 'init' ) );
