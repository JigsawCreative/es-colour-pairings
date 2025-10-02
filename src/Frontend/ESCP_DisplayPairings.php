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
			$decoded         = json_decode( $row['products'], true );
			$row['products'] = is_array( $decoded ) ? array_values( $decoded ) : array();

		}

		return $rows;
	}

	/**
	 * Get content for the current post.
	 *
	 * @param int $post_id Current post ID.
	 * @return array{
	 *   url: string,
	 *   escaped_url: string,
	 *   parent_slug: string,
	 *   parent_name: string
	 * }
	 */
	public static function get_post_content( int $post_id ): array {

		$url         = get_permalink( $post_id );
		$escaped_url = esc_url( $url );

		$post_data   = get_post( wp_get_post_parent_id( $post_id ) );
		$parent_slug = $post_data ? $post_data->post_name : '';
		$parent_name = $parent_slug ? str_replace( '-', ' ', $parent_slug ) : '';

		return array(
			'url'         => $url,
			'escaped_url' => $escaped_url,
			'parent_slug' => $parent_slug,
			'parent_name' => ucwords( $parent_name ),
		);
	}
}
