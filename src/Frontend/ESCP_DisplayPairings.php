<?php

namespace ESColourPairings\Frontend;

/**
 * Fetches and renders colour pairings for a given page pairing_id.
 */

class ESCP_DisplayPairings {

	/**
	 * Query all rows from the escp_pairs table for a supplied pairing_id.
	 * Groups results by pairing_id and adds a products[] array per row.
	 *
	 * @param string $pairing_id Page pairing_id to match.
	 * @return array  Grouped array keyed by pairing_id.
	 */
	public static function get_pairings( string $pairing_id ): array {

		global $wpdb;

		// Convert ACF string to int for databsae fetch
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

		// Array to hold items grouped by heading
		$grouped = array();

		foreach ( $rows as &$row ) {

			// Collect IDs without auto-casting nulls/empties to 0
			$product_ids = array(
				$row['product1id'],
				$row['product2id'],
				$row['product3id'],
			);

			// Keep only numeric and > 0
			$row['products'] = array_values(
				array_filter(
					$product_ids,
					function ( $id ) {
						return ! is_null( $id ) && (int) $id > 0;
					}
				)
			);

			// Group by heading
			$heading               = $row['heading'];
			$grouped[ $heading ][] = $row;
		}

		return $grouped;
	}
}
