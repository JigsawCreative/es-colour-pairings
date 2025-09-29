<?php

namespace ESColourPairings\Assets;

class ESCP_Enqueue {

	public static function init() {

		// Enqueue front-end scripts
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	public static function enqueue_scripts() {

		// Styles for the filter UI
		wp_enqueue_style( 'colour-pairings-css', plugin_dir_url( __FILE__ ) . '/../../../assets/css/colour-pairings.css', array(), ESCP_VERSION );
	}
}
