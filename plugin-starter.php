<?php
/**
 * Plugin Name: My Plugin
 * Plugin URI:  https://example.com
 * Description: Starter plugin.
 * Version:     0.1.0
 * Author:      Your Name
 * Author URI:  https://example.com
 * License:     GPL2
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/src/PluginStarter.php';

add_action( 'plugins_loaded', [ 'PluginStarter', 'init' ] );

