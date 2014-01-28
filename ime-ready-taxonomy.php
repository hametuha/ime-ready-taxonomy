<?php
/*
Plugin Name: IME Ready Taxonomy
Plugin URI: http://wordpress.org/plugins/ime-ready-taxonomy
Description: Enhanced WordPress UI for taxonomy. Don't hesitate to press enter key!
Author: Takahashi Fumiki
Version: 1.0
Author URI: http://takahashifumiki.com
*/

// Do not load directly
defined('ABSPATH') or die();


/**
 * Plugin version
 *
 * @const strint
 */
define('IRT_VERSION', '1.0');

/**
 * Plugin's i18n domain
 */
define('IRT_DOMAIN', 'ime_ready_taxonomy');

// Load functions
require dirname(__FILE__).DIRECTORY_SEPARATOR.'functions.php';

/**
 * Initialize plugin
 */
add_action("plugins_loaded", '_irt_init');
