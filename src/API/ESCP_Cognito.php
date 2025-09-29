<?php
/**
 * Handles Cognito form submissions for ES Colour Pairings.
 *
 * @package ESColourPairings\API
 */

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

		$data = $request->get_json_params();
		if ( ! $data ) {
			return new \WP_Error( 'no_data', 'No JSON received', array( 'status' => 400 ) );
		}

		global $wpdb;

		$table      = $wpdb->prefix . 'escp_pairs';
		$pairing_id = (int) ( $data['PairingID'] ?? 0 );
		$page_topic = sanitize_text_field( $data['Section']['PageTopic'] ?? '' );

		foreach ( $data['Section']['Grouping'] as $group ) {

			$heading = sanitize_text_field( $group['Heading'] ?? '' );

			if ( empty( $group['Options'] ) || ! is_array( $group['Options'] ) ) {
				continue;
			}

			foreach ( $group['Options'] as $option ) {
				$product1 = (int) ( $option['Product1ID'] ?? 0 );
				$product2 = (int) ( $option['Product2ID'] ?? 0 );
				$product3 = (int) ( $option['Product3ID'] ?? 0 );

                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->query(
					$wpdb->prepare(
						"INSERT INTO {$wpdb->prefix}escp_pairs (pairing_id, page_topic, heading, product1id, product2id, product3id)
                        VALUES (%d, %s, %s, %d, %d, %d)
                        ON DUPLICATE KEY UPDATE
                            page_topic = VALUES(page_topic),
                            heading    = VALUES(heading),
                            product1id = VALUES(product1id),
                            product2id = VALUES(product2id),
                            product3id = VALUES(product3id)",
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
