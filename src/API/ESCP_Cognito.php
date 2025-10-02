<?php

namespace ESColourPairings\API;

/**
 * Handles Cognito form submissions for ES Colour Pairings.
 */
class ESCP_Cognito {

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_endpoint']);
    }

    public function register_endpoint() {
        register_rest_route(
            'escp/v1',
            '/cognito-submit',
            [
                'methods'             => 'POST',
                'callback'            => [$this, 'handle_submission'],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function handle_submission($request) {
        $data = $request->get_json_params();
        if (!$data) {
            return new \WP_Error('no_data', 'No JSON received', ['status' => 400]);
        }

        $pairing_id = (int) ($data['PairingID'] ?? 0);
        $page_topic = sanitize_text_field($data['Section']['PageTopic'] ?? '');

        if (empty($data['Section']['Grouping']) || !is_array($data['Section']['Grouping'])) {
            return new \WP_Error('no_groupings', 'No groupings found', ['status' => 400]);
        }

        global $wpdb;

        // Remove all existing rows for this pairing_id
        $wpdb->delete(
            "{$wpdb->prefix}escp_pairs",
            ['pairing_id' => $pairing_id],
            ['%d']
        );

        // Insert each grouping anew
        foreach ($data['Section']['Grouping'] as $group) {
            $heading     = sanitize_text_field($group['Heading'] ?? '');
            $more_link   = sanitize_text_field($group['MoreLink'] ?? '');
            $products_json = $this->build_products_json($group['Options'] ?? []);

            $wpdb->insert(
                "{$wpdb->prefix}escp_pairs",
                [
                    'pairing_id' => $pairing_id,
                    'page_topic' => strtolower($page_topic),
                    'heading'    => strtolower($heading),
                    'products'   => $products_json,
                    'more_link'  => $more_link,
                ],
                ['%d', '%s', '%s', '%s', '%s']
            );
        }

        return ['success' => true];
    }

    private function build_products_json(array $options): string {
        $products_json = [];

        foreach ($options as $option) {
            $cognito_option_id = sanitize_text_field($option['Id'] ?? '');
            $products = array_filter([
                isset($option['Product1ID']) ? (int) $option['Product1ID'] : null,
                isset($option['Product2ID']) ? (int) $option['Product2ID'] : null,
                isset($option['Product3ID']) ? (int) $option['Product3ID'] : null,
            ]);

            if ($products) {
                $products_json[$cognito_option_id] = array_values($products);
            }
        }

        return wp_json_encode($products_json);
    }
}
