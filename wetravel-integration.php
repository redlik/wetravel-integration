<?php

/**
 * Plugin Name:       WeTravel Integration
 * Plugin URI:        http://collage.ie
 * Description:       WeTravel Integration plugin.
 * Version:           1.0.0
 * Author:            Rafo
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wetravel-integration
 * Domain Path:       /languages
 */

require_once(plugin_dir_path(__FILE__) . '/vendor/autoload.php');

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
