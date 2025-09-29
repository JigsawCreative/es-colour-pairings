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

use ESColourPairings\API\ESCP_Cognito;
use ESColourPairings\Database\ESCP_Create;
use ESColourPairings\Database\ESCP_Update;

// Initialize database
$escp_db_create = new ESCP_Create();

// Register activation hook
register_activation_hook( __FILE__, [ $escp_db_create, 'create_table' ] );

// Bootstrap main classes
add_action( 'plugins_loaded', function() {

    new ESCP_Cognito();
    ESCP_Update::init();

});