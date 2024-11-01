<?php
/**
 * Plugin Name:       Wisy Page Builder
 * Plugin URI:        http://saturtheme.com/plugins/wisy
 * Description:       Wisy Builder is a simple and usefull page builder. Building pages without any coding knowledge.
 * Version:           0.2.1
 * Requires at least: 4.6
 * Requires PHP:      5.4
 * Author:            SaturTheme
 * Author URI:        http://saturtheme.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wisy
 * Domain Path:       /languages
 */

// Exit if accessed directly
if ( ! defined('ABSPATH') ) { exit; }

// Define plugin path
define( 'WISY_PATH', plugin_dir_path(__FILE__) );

// Define plugin url
define( 'WISY_URL', plugin_dir_url(__FILE__) );

// Define plugin version
define( 'WISY_VERSION', '0.2.1' );

// Wisy plugin exists
define( 'WISY_BUILDER_EXISTS', true );

// Require functions file
require_once ( WISY_PATH . 'includes/wisy-functions.php' );

// Require main class
require_once ( WISY_PATH . 'includes/class-wisy-builder.php' );

// Action hooks
require_once ( WISY_PATH . 'includes/action-hooks.php' );

// Filter hooks
require_once ( WISY_PATH . 'includes/filter-hooks.php' );