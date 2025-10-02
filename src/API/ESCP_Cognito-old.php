<?php

namespace ESColourPairings\API;

/**
 * Handles Cognito form submissions for ES Colour Pairings.
 */
class ESCP_Cognito {

	/**
	 * Constructor.
	 *
	 * Hooks REST endpoint when plugin is loaded.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_endpoint' ) );
	}

	/**
	 * Registers the REST API endpoint.
	 */
	public function register_endpoint() {
		register_rest_route(
			'escp/v1',
			'/cognito-submit',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_submission' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Handles incoming Cognito form submissions.
	 *
	 * @param \WP_REST_Request $request
	 * @return array|\WP_Error
	 */
	public function handle_submission( $request ) {
		$data = $request->get_json_params();
		if ( ! $data ) {
			return new \WP_Error( 'no_data', 'No JSON received', array( 'status' => 400 ) );
		}

		$pairing_id = $this->sanitize_pairing_id( $data['PairingID'] ?? 0 );
		$page_topic = sanitize_text_field( $data['Section']['PageTopic'] ?? '' );

		if ( empty( $data['Section']['Grouping'] ) || ! is_array( $data['Section']['Grouping'] ) ) {
			return new \WP_Error( 'no_groupings', 'No groupings found', array( 'status' => 400 ) );
		}

		foreach ( $data['Section']['Grouping'] as $group ) {
			$this->process_group( $pairing_id, $page_topic, $group );
		}

		return array( 'success' => true );
	}

	/**
	 * Sanitize pairing ID.
	 *
	 * @param mixed $pairing_id
	 * @return int
	 */
	private function sanitize_pairing_id( $pairing_id ): int {
		return (int) $pairing_id;
	}

	/**
	 * Processes a single grouping from the Cognito submission.
	 *
	 * @param int   $pairing_id
	 * @param string $page_topic
	 * @param array  $group
	 */
	private function process_group( int $pairing_id, string $page_topic, array $group ) {
		global $wpdb;

		$heading   = sanitize_text_field( $group['Heading'] ?? '' );
		$more_link = sanitize_text_field( $group['MoreLink'] ?? '' );

		if ( empty( $group['Options'] ) || ! is_array( $group['Options'] ) ) {
			return;
		}

		$products_json = $this->build_products_json( $group['Options'] );

		// Check if row exists
		$existing_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}escp_pairs
                 WHERE pairing_id = %d AND heading = %s",
				$pairing_id,
				strtolower( $heading )
			)
		);

		if ( $existing_id ) {
			$this->update_row( $existing_id, $pairing_id, $page_topic, $heading, $products_json, $more_link );
		} else {
			$this->insert_row( $pairing_id, $page_topic, $heading, $products_json, $more_link );
		}
	}

	/**
	 * Build JSON keyed by Cognito Option ID.
	 *
	 * @param array $options
	 * @return string
	 */
	private function build_products_json( array $options ): string {
		$products_json = array();

		foreach ( $options as $option ) {
			$cognito_option_id = sanitize_text_field( $option['Id'] ?? '' );

			$products = array_filter(
				array(
					isset( $option['Product1ID'] ) ? (int) $option['Product1ID'] : null,
					isset( $option['Product2ID'] ) ? (int) $option['Product2ID'] : null,
					isset( $option['Product3ID'] ) ? (int) $option['Product3ID'] : null,
				)
			);

			if ( $products ) {
				$products_json[ $cognito_option_id ] = array_values( $products );
			}
		}

		return wp_json_encode( $products_json );
	}

	/**
	 * Update existing row.
	 *
	 * @param int    $id
	 * @param int    $pairing_id
	 * @param string $page_topic
	 * @param string $heading
	 * @param string $products_json
	 * @param string $more_link
	 */
	private function update_row( int $id, int $pairing_id, string $page_topic, string $heading, string $products_json, string $more_link ) {
		global $wpdb;

		$wpdb->update(
			"{$wpdb->prefix}escp_pairs",
			array(
				'products'   => $products_json,
				'more_link'  => $more_link,
				'page_topic' => strtolower( $page_topic ),
			),
			array( 'id' => $id ),
			array( '%s', '%s', '%s' ),
			array( '%d' )
		);
	}

	/**
	 * Insert new row.
	 *
	 * @param int    $pairing_id
	 * @param string $page_topic
	 * @param string $heading
	 * @param string $products_json
	 * @param string $more_link
	 */
	private function insert_row( int $pairing_id, string $page_topic, string $heading, string $products_json, string $more_link ) {
		global $wpdb;

		$wpdb->insert(
			"{$wpdb->prefix}escp_pairs",
			array(
				'pairing_id' => $pairing_id,
				'page_topic' => strtolower( $page_topic ),
				'heading'    => strtolower( $heading ),
				'products'   => $products_json,
				'more_link'  => $more_link,
			),
			array( '%d', '%s', '%s', '%s', '%s' )
		);
	}
}
