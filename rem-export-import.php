<?php
/**
 * Plugin Name: Export Import - Real Estate Manager Extension
 * Plugin URI: https://webcodingplace.com/real-estate-manager-wordpress-plugin/
 * Description: Easily Import and Export Real Estate Manager data including settings, fields, listings etc
 * Version: 2.1
 * Author: WebCodingPlace
 * Author URI: https://webcodingplace.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rem-expimp
 * Domain Path: /languages
 */

require_once('plugin.class.php');
require_once('inc/class-download-image.php');

/**
 * Iniliatizing main class object for setting up import/export
 */
if( class_exists('REM_Export_Import')){
    $rem_filterable_grid = new REM_Export_Import;
}

?>