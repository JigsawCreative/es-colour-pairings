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

		$pairing_id = isset( $data['PairingID'] ) ? (int) $data['PairingID'] : 0;
		$page_topic = sanitize_text_field( $data['Section']['PageTopic'] ?? '' );

		// Loop each Grouping
		foreach ( $data['Section']['Grouping'] as $group ) {

			$heading = sanitize_text_field( $group['Heading'] ?? '' );
			$more_link = sanitize_text_field( $group['MoreLink'] ?? '' );

			if ( empty( $group['Options'] ) || ! is_array( $group['Options'] ) ) {
				continue;
			}

			// Loop each Options row = one DB row per option.
			foreach ( $group['Options'] as $option ) {
				$cognito_option_id = sanitize_text_field( $option['Id'] ?? '' );
				$product1 = (int) ( $option['Product1ID'] ?? 0 );
				$product2 = (int) ( $option['Product2ID'] ?? 0 );
				$product3 = (int) ( $option['Product3ID'] ?? 0 );

				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->query(
					$wpdb->prepare(
						'INSERT INTO wp_escp_pairs
							(pairing_id, cognito_option_id, page_topic, heading, product1id, product2id, product3id, more_link)
						VALUES
							(%d, %s, %s, %s, %d, %d, %d, %s)
						',
						$pairing_id,
						$cognito_option_id,
						strtolower( $page_topic ),
						strtolower( $heading ),
						$product1,
						$product2,
						$product3,
						$more_link
					)
				);
			}
		}

		return array( 'success' => true );
	}
}
