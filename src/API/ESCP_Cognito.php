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
	 * Products are stored as JSON keyed by Cognito Option ID.
	 * Notes like `more_link` are retained.
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

			$heading   = sanitize_text_field( $group['Heading'] ?? '' );
			$more_link = sanitize_text_field( $group['MoreLink'] ?? '' );

			if ( empty( $group['Options'] ) || ! is_array( $group['Options'] ) ) {
				continue;
			}

			// Build JSON of products keyed by Cognito Option ID
			$products_json = array();
			foreach ( $group['Options'] as $option ) {
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

			// Insert row per heading with JSON products and notes
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}escp_pairs
                        (pairing_id, page_topic, heading, products, more_link)
                    VALUES
                        (%d, %s, %s, %s, %s)",
					$pairing_id,
					strtolower( $page_topic ),
					strtolower( $heading ),
					wp_json_encode( $products_json ),
					$more_link
				)
			);
		}

		return array( 'success' => true );
	}
}
