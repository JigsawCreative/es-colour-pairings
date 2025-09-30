<?php

namespace ESColourPairings\Frontend;

/**
 * Fetches and renders colour pairings for a given page topic.
 */

class ESCP_DisplayPairings {

	/**
	 * Query all rows from the escp_pairs table for a supplied topic.
	 * Groups results by pairing_id and adds a products[] array per row.
	 *
	 * @param string $topic Page topic to match.
	 * @return array  Grouped array keyed by pairing_id.
	 */
	public static function get_pairings_by_topic( string $topic ): array {

		global $wpdb;

		$topic = sanitize_text_field( $topic );

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT id, pairing_id, page_topic, heading, 
                        product1id, product2id, product3id
                FROM ' . $wpdb->prefix . 'escp_pairs
                WHERE page_topic = %s
                ORDER BY id ASC',
				$topic
			),
			ARRAY_A
		);

		if ( ! $rows ) {
			return array();
		}

		foreach ( $rows as &$row ) {
			// Build a simple products array for easy looping.
			$row['products'] = array_filter(
				array(
					(int) $row['product1id'],
					(int) $row['product2id'],
					(int) $row['product3id'],
				)
			);
		}

		return $rows;
	}
}
