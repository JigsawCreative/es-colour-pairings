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
	 * Updates existing row if pairing_id exists, inserts otherwise.
	 *
	 * @param \WP_REST_Request $request The incoming REST request.
	 * @return array|\WP_Error Success array or WP_Error on failure.
	 */
	public function handle_submission( $request ) {

		// Retrieve JSON
		$data = $request->get_json_params();

		// Return error if no data
		if ( ! $data ) {
			return new \WP_Error( 'no_data', 'No JSON received', array( 'status' => 400 ) );
		}

		global $wpdb;

		// Set data/table values
		$table      = $wpdb->prefix . 'escp_pairs';
		$pairing_id = intval( $data['PairingID'] ?? 0 );
		$page_topic = sanitize_text_field( $data['Section']['PageTopic'] ?? '' );

		// Loop over data and add to database
		foreach ( $data['Section']['Grouping'] as $group ) {

			$heading = sanitize_text_field( $group['Heading'] ?? '' );

			if ( empty( $group['Options'] ) || ! is_array( $group['Options'] ) ) {
				continue;
			}

			foreach ( $group['Options'] as $option ) {
				$product1 = intval( $option['Product1ID'] ?? 0 );
				$product2 = intval( $option['Product2ID'] ?? 0 );
				$product3 = intval( $option['Product3ID'] ?? 0 );

				// Use ON DUPLICATE KEY UPDATE to preserve the original id
				$sql = "INSERT INTO {$table} (pairing_id, page_topic, heading, product1id, product2id, product3id)
                        VALUES (%d, %s, %s, %d, %d, %d)
                        ON DUPLICATE KEY UPDATE
                            page_topic = VALUES(page_topic),
                            heading    = VALUES(heading),
                            product1id = VALUES(product1id),
                            product2id = VALUES(product2id),
                            product3id = VALUES(product3id)";

				$wpdb->query(
					$wpdb->prepare(
						$sql,
						$pairing_id,
						$page_topic,
						$heading,
						$product1,
						$product2,
						$product3
					)
				);
			}
		}

		return array( 'success' => true );
	}
}
