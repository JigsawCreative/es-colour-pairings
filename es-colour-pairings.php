<?php
/**
 * Plugin Name: ES Colour Pairings
 * Description: ES Colour Pairings plugin.
 * Version:     0.1.0
 * Author:      Emporio Surfaces
 * Author URI:  https://emporiosurfaces.com
 * License:     GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Constants
define( 'ESCP_PATH', plugin_dir_path( __FILE__ ) );
define( 'ESCP_URL',  plugin_dir_url( __FILE__ ) );
define( 'ESCP_VERSION', '1.0' );

// Composer autoload
if ( file_exists( ESCP_PATH . 'vendor/autoload.php' ) ) {
    require_once ESCP_PATH . 'vendor/autoload.php';
}

use ESColourPairings\ESCP_Database;

// Bootstrap main class
add_action( 'plugins_loaded', function() {

    // Initialize database
    $escp_db = new ESCP_Database();

});