<?php

namespace ESColourPairings\Assets;

/**
 * Handles enqueueing of plugin CSS and JS assets.
 */
class ESCP_Enqueue {

	/**
	 * Initialize hooks.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue front-end styles and scripts.
	 *
	 * Loads unminified assets in WP_DEBUG mode and minified assets from dist in production.
	 */
	public static function enqueue_assets() {
		$dev_mode = defined( 'WP_DEBUG' ) && WP_DEBUG === true;

		wp_enqueue_style( 'colour-pairings-css', ESCP_URL . ( $dev_mode ? 'assets/css/colour-pairings.css' : 'dist/css/style.min.css' ), array(), ESCP_VERSION );
		wp_enqueue_script( 'colour-pairings-js', ESCP_URL . ( $dev_mode ? 'assets/js/script.js' : 'dist/js/script.min.js' ), array( 'jquery' ), ESCP_VERSION, true );
	}
}
