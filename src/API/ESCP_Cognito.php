<?php

namespace ESColourPairings\API;

class ESCP_Cognito {

    public function __construct() {

        // Hook REST endpoint when plugin is loaded
        add_action('rest_api_init', [$this, 'register_endpoint']);

    }

    public function register_endpoint() {

        register_rest_route('escp/v1', '/cognito-submit', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_submission'],
            'permission_callback' => '__return_true',
        ]);
        
    }

    public function handle_submission($request) {

        $data = $request->get_json_params();

        if (!$data) {
            return new \WP_Error('no_data', 'No JSON received', ['status' => 400]);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'escp_pairs';

        // Base values
        $pairing_id = $data['PairingID'] ?? 0;
        $page_topic = $data['Section']['PageTopic'] ?? '';

        // Loop through each grouping
        if (!empty($data['Section']['Grouping']) && is_array($data['Section']['Grouping'])) {

            foreach ($data['Section']['Grouping'] as $group) {

                $heading = $group['Heading'] ?? '';

                // Loop through each option in the grouping
                if (!empty($group['Options']) && is_array($group['Options'])) {

                    foreach ($group['Options'] as $option) {

                        // Extract product IDs
                        $product1 = $option['Product1ID'] ?? 0;
                        $product2 = $option['Product2ID'] ?? 0;
                        $product3 = $option['Product3ID'] ?? 0;

                        // Insert one row per option
                        $wpdb->insert($table, [
                            'pairing_id' => $pairing_id,
                            'page_topic' => $page_topic,
                            'heading'    => $heading,
                            'product1id' => $product1,
                            'product2id' => $product2,
                            'product3id' => $product3,
                        ]);
                    }
                }
            }
        }

        return ['success' => true];
    }
}
