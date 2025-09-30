<?php
/**
 * ES Colour Pairings database creation class.
 *
 * Handles creation of plugin tables.
 *
 * @package ES Colour Pairings
 */

namespace ESColourPairings\Database;

class ESCP_Create {

	/**
	 * Table name.
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'escp_pairs';
	}

	/**
	 * Create the database table.
	 *
	 * Products are stored as JSON keyed by Cognito Option ID.
	 * Notes like `more_link` are retained.
	 */
	public function create_table() {

		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$this->table_name} (
            id INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            pairing_id INT(8) NOT NULL,
            page_topic VARCHAR(50) NOT NULL,
            heading VARCHAR(50) NOT NULL,
            products JSON NOT NULL, -- JSON of products keyed by cognito_option_id
            more_link VARCHAR(256) NOT NULL, -- retained note
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

		dbDelta( $sql );
	}
}
