<?php
/**
 * Global functions
 *
 * Used when required. But in case user's PHP is 5.2,
 * lambda functions will never included.
 * Prefix is irt_, internal functions are prefixed with _irt
 *
 * @package irt
 * @author Takahashi Fumiki
 */

/**
 * Initialize plugin
 *
 * Called in plugins_loaded hook.
 *
 * @since 1.0
 * @ignore
 */
function _irt_init(){

    // Register text domain for i18n
    load_plugin_textdomain(IRT_DOMAIN, false, 'ime-ready-taxonomy/languages');

    // Detect if this WordPress is PHP 5.3
    if( version_compare(phpversion(), '5.3.0', '>=') ){
        // O.K. let's initialize plugin
        spl_autoload_register('_irt_autoload');
        // Initialize
        call_user_func(array('IRT\Main', 'get_instance'));
    }else{
        // N.G.
        add_action('admin_notices', '_irt_notice');
    }
}

/**
 * Auto loader
 *
 * Auto loader function for IRT
 *
 * @ignore
 * @param string $class_name
 */
function _irt_autoload($class_name){
    $class_name = ltrim($class_name, '\\');
    if( 0 === strpos($class_name, 'IRT\\') ){
        $path = dirname(__FILE__).DIRECTORY_SEPARATOR.strtolower(str_replace('\\', '/', $class_name)).'.php';
        if( file_exists($path) ){
            require $path;
        }
    }
}

/**
 * Show notice about PHP requirements
 *
 * Displayed only when PHP is too old.
 *
 * @since 1.0
 * @ignore
 */
function _irt_notice(){
    printf('<div class="error"><p>%s</p></div>', sprintf(__('Your PHP version is %s, but this plugin requires 5.3. Please contact your server administrator.', 'ime_ready_taxonomy'), phpversion()));
}
