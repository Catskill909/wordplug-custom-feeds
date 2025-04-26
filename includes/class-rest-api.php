<?php

/**
 * Handles the Custom REST API endpoints for the plugin.
 *
 * @package WordPlug_Custom_Feeds
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class WordPlug_CF_REST_API
 *
 * Registers and handles the custom REST API routes.
 */
class WordPlug_CF_REST_API
{

    /**
     * The namespace for the custom REST API routes.
     *
     * @var string
     */
    private $namespace = 'wordplug/v1';

    /**
     * Constructor. Hooks into WordPress actions.
     */
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * Registers the custom REST API routes.
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/feed/(?P<slug>[a-zA-Z0-9-]+)', // Route using the post slug
            array(
                'methods'             => WP_REST_Server::READABLE, // GET requests
                'callback'            => array($this, 'get_feed_data'),
                'permission_callback' => '__return_true', // Publicly accessible
                'args'                => array(
                    'slug' => array(
                        'validate_callback' => function ($param, $request, $key) {
                            return is_string($param); // Basic validation
                        },
                        'sanitize_callback' => 'sanitize_key',
                        'required'          => true,
                        'description'       => __('The slug of the custom feed.', WORDPLUG_CF_TEXT_DOMAIN),
                    ),
                ),
            )
        );
    }

    /**
     * Callback function to get the feed data for the REST API endpoint.
     *
     * @param WP_REST_Request $request The request object.
     * @return WP_REST_Response|WP_Error Response object on success, WP_Error object on failure.
     */
    public function get_feed_data(WP_REST_Request $request)
    {
        $slug = $request['slug']; // Already sanitized by 'sanitize_callback' in route registration

        // Query for the post by slug (post_name) using WP_Query for consistency
        $args = array(
            'name'           => $slug,
            'post_type'      => 'custom_feed',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'no_found_rows'  => true, // Optimization: We only need one post.
            'fields'         => 'ids', // Optimization: Only fetch the ID initially
        );
        $query = new WP_Query($args);

        if (! $query->have_posts()) {
            return new WP_Error(
                'rest_feed_not_found',
                __('Custom feed not found.', WORDPLUG_CF_TEXT_DOMAIN),
                array('status' => 404)
            );
        }

        // Get the post ID
        $feed_id = $query->posts[0];

        // Get the feed configuration from post meta
        $feed_config = get_post_meta($feed_id, '_feed_config', true);

        // Basic validation: Ensure it's an array (it should be if saved correctly)
        if (! is_array($feed_config)) {
            // Log error for debugging, but return a generic error to the user
            error_log("WordPlug Custom Feeds: Invalid feed config format for feed ID {$feed_id} (Slug: {$slug}). Expected array, got " . gettype($feed_config));
            return new WP_Error(
                'rest_feed_config_error',
                __('Error retrieving feed configuration.', WORDPLUG_CF_TEXT_DOMAIN),
                array('status' => 500) // Internal Server Error
            );
        }

        // Prepare the response data - structure it cleanly as key => value pairs
        $response_data = array();

        // Process text fields
        if (isset($feed_config['text_fields']) && is_array($feed_config['text_fields'])) {
            foreach ($feed_config['text_fields'] as $field) {
                // Ensure key exists and is not empty before adding
                if (isset($field['key']) && trim($field['key']) !== '') {
                    // Use the sanitized key as the key in the response
                    $response_data[sanitize_key($field['key'])] = isset($field['value']) ? $field['value'] : '';
                }
            }
        }

        // Process media fields
        if (isset($feed_config['media_fields']) && is_array($feed_config['media_fields'])) {
            foreach ($feed_config['media_fields'] as $field) {
                // Ensure key exists and is not empty before adding
                if (isset($field['key']) && trim($field['key']) !== '') {
                    // Use the sanitized key as the key in the response
                    $response_data[sanitize_key($field['key'])] = isset($field['value']) ? $field['value'] : ''; // Value is already a URL
                }
            }
        }

        // Process toggles
        if (isset($feed_config['toggles']) && is_array($feed_config['toggles'])) {
            foreach ($feed_config['toggles'] as $field) {
                // Ensure key exists and is not empty before adding
                if (isset($field['key']) && trim($field['key']) !== '') {
                    // Use the sanitized key as the key, value should be boolean
                    $response_data[sanitize_key($field['key'])] = (isset($field['value']) && $field['value'] === 'on');
                }
            }
        }

        // Return a response object
        $response = new WP_REST_Response($response_data, 200);

        // Optional: Add caching headers if desired
        // $response->header( 'Cache-Control', 'max-age=' . ( 15 * MINUTE_IN_SECONDS ) );

        return $response;

        // Prepare the JSON output structure
        $output_data = array(
            'title'   => $feed_post->post_title,
            'fields'  => array(),
            'toggles' => array(),
        );

        // Populate fields
        if (isset($config['text_fields']) && is_array($config['text_fields'])) {
            foreach ($config['text_fields'] as $field) {
                if (isset($field['key']) && ! empty($field['key'])) {
                    $output_data['fields'][$field['key']] = isset($field['value']) ? $field['value'] : '';
                }
            }
        }
        if (isset($config['media_fields']) && is_array($config['media_fields'])) {
            foreach ($config['media_fields'] as $field) {
                if (isset($field['key']) && ! empty($field['key'])) {
                    $output_data['fields'][$field['key']] = isset($field['value']) ? $field['value'] : '';
                }
            }
        }

        // Populate toggles
        if (isset($config['toggles']) && is_array($config['toggles'])) {
            foreach ($config['toggles'] as $field) {
                if (isset($field['key']) && ! empty($field['key'])) {
                    $output_data['toggles'][$field['key']] = (isset($field['value']) && $field['value'] === 'on') ? 'on' : 'off';
                }
            }
        }

        // Create the response
        $response = new WP_REST_Response($output_data, 200);

        // Optional: Add headers like cache control if needed
        // $response->header( 'Cache-Control', 'max-age=' . ( HOUR_IN_SECONDS ) );

        return $response;
    }
}

// Instantiate the class (will be done in main plugin file)
// new WordPlug_CF_REST_API();
