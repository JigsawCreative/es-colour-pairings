<?php

namespace ESColourPairings\Frontend;

/**
 * Fetches and renders colour pairings for a given page pairing_id.
 */
class ESCP_DisplayPairings {

	/**
	 * Query all rows from the escp_pairs table for a supplied pairing_id.
	 * Decodes the JSON `products` column and returns structured array per heading.
	 *
	 * @param string $pairing_id Page pairing_id to match.
	 * @return array Grouped array keyed by heading.
	 */
	public static function get_pairings( string $pairing_id ): array {

		global $wpdb;

		// Convert ACF string to int for database fetch
		$pairing_id = (int) $pairing_id;

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM wp_escp_pairs WHERE pairing_id = %d ORDER BY id ASC',
				$pairing_id
			),
			ARRAY_A
		);

		if ( ! $rows ) {
			return array();
		}

		foreach ( $rows as &$row ) {

			// Decode JSON products column (keyed by cognito_option_id)
			$row['products'] = json_decode( $row['products'], true );

		}

		return $rows;
	}
}
