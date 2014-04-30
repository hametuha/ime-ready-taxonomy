<?php
/*
Plugin Name: IME Ready Taxonomy
Plugin URI: http://wordpress.org/plugins/ime-ready-taxonomy
Description: Enhanced WordPress UI for taxonomy. Don't hesitate to press enter key!
Author: Takahashi Fumiki
Version: 1.1
Author URI: http://takahashifumiki.com
Author Email: takahashi.fumiki@hametuha.co.jp
Text Domain: ime-ready-taxonomy
Domain Path: /languages
*/

// Do not load directly
defined('ABSPATH') or die();


/**
 * Plugin version
 *
 * @const string
 */
define('IRT_VERSION', '1.1');

/**
 * Plugin's i18n domain
 *
 * @const string
 */
define('IRT_DOMAIN', 'ime-ready-taxonomy');

// Load functions
require dirname(__FILE__).DIRECTORY_SEPARATOR.'functions.php';

/**
 * Initialize plugin
 */
add_action("plugins_loaded", '_irt_init');

// For gettext scraping
if( false ){
    __('Enhanced WordPress UI for taxonomy. Don\'t hesitate to press enter key!', IRT_DOMAIN);
}
