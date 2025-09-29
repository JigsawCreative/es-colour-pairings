<?php
/**
 * ES Colour Pairings database class.
 *
 * Handles creation of plugin tables.
 *
 * @package ES Colour Pairings
 */

namespace ESColourPairings;

defined( 'ABSPATH' ) || exit;

class ESCP_Database {

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

        // Hook into plugin activation to create table
        register_activation_hook( __FILE__, [ $this, 'create_table' ] );
    }

    /**
     * Create the database table.
     */
    public function create_table() {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            colour_from VARCHAR(50) NOT NULL,
            colour_to VARCHAR(50) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta( $sql );
    }
}
