<?php

/**
 * Registers page templates with WordPress.
 *
 * @package ESColourPairings\Templates
 */

namespace ESColourPairings\Templates;

class ESCP_RegisterTemplates {

	public function __construct() {

		add_filter( 'theme_page_templates', array( $this, 'add_template' ) );
		add_filter( 'template_include', array( $this, 'load_template' ) );
	}

	public function add_template( $templates ) {

		$templates['pairings-loop.php'] = 'Pairings Loop';

		return $templates;
	}

	public function load_template( $template ) {

		if ( is_page() && get_page_template_slug() === 'pairings-loop.php' ) {
			return plugin_dir_path( dirname( __DIR__, 1 ) ) . 'templates/pairings-loop.php';
		}

		return $template;
	}
}
