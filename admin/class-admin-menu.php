<?php

/**
 * Handles the Admin Menu and Pages for the plugin.
 *
 * @package WordPlug_Custom_Feeds
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class WordPlug_CF_Admin_Menu
 *
 * Creates the admin menu item and renders the corresponding pages.
 */
class WordPlug_CF_Admin_Menu
{

    /**
     * Capability required to access the plugin admin pages.
     *
     * @var string
     */
    private $capability = 'manage_options'; // Default capability

    /**
     * The slug for the main menu page.
     *
     * @var string
     */
    private $main_menu_slug = 'wordplug-custom-feeds';

    /**
     * Constructor. Hooks into WordPress actions.
     */
    public function __construct()
    {
        error_log('[WordPlug] Admin menu class instantiated');
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('admin_post_wordplug_save_feed', array($this, 'handle_save_feed'));
        add_action('admin_post_wordplug_delete_feed', array($this, 'handle_delete_feed'));
        add_action('admin_notices', array($this, 'display_admin_notices'));
    }

    /**
     * Adds the main menu page.
     */
    public function add_admin_menu()
    {
        add_menu_page(
            __('Custom Feeds', WORDPLUG_CF_TEXT_DOMAIN), // Page Title
            __('Custom Feeds', WORDPLUG_CF_TEXT_DOMAIN), // Menu Title
            $this->capability,                             // Capability
            $this->main_menu_slug,                         // Menu Slug
            array($this, 'render_list_page'),            // Callback function
            'dashicons-rss',                               // Icon URL
            25                                             // Position
        );

        // Add hidden submenu page for Adding/Editing feeds
        // This allows us to use a consistent slug for both actions
        add_submenu_page(
            null, // Parent slug - null hides it from menu
            __('Add/Edit Feed', WORDPLUG_CF_TEXT_DOMAIN), // Page Title (doesn't show in menu)
            __('Add/Edit Feed', WORDPLUG_CF_TEXT_DOMAIN), // Menu Title (doesn't show)
            $this->capability,
            $this->main_menu_slug . '-edit', // Submenu slug (e.g., wordplug-custom-feeds-edit)
            array($this, 'render_edit_page') // Callback function
        );
    }

    /**
     * Renders the main list page view.
     */
    public function render_list_page()
    {
        // Security check
        if (! current_user_can($this->capability)) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', WORDPLUG_CF_TEXT_DOMAIN));
        }

        // Query the custom feeds
        $feeds_query = new WP_Query(array(
            'post_type'      => 'custom_feed',
            'posts_per_page' => -1, // Get all feeds
            'post_status'    => 'publish', // Only published feeds
            'orderby'        => 'title',
            'order'          => 'ASC',
        ));
        $feeds = $feeds_query->posts; // Get the array of post objects

        // Include the view file, passing the feeds data and menu slug
        $list_view_path = WORDPLUG_CF_PATH . 'admin/views/feed-list-page.php';
        if (file_exists($list_view_path)) {
            // Make $feeds and $main_menu_slug available within the included file's scope
            $main_menu_slug = $this->main_menu_slug; // Assign to local variable
            include $list_view_path;
        } else {
            echo '<div class="wrap"><h2>' . esc_html__('Error', WORDPLUG_CF_TEXT_DOMAIN) . '</h2><p>' . esc_html__('List page view file not found.', WORDPLUG_CF_TEXT_DOMAIN) . '</p></div>';
        }
    }

    /**
     * Renders the edit/add new page view.
     * (This will be called by the submenu pages if we add them, or handled via query vars)
     */
    public function render_edit_page()
    {
        // Security check
        if (! current_user_can($this->capability)) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', WORDPLUG_CF_TEXT_DOMAIN));
        }

        // Determine if editing or adding new
        $feed_id    = isset($_GET['feed_id']) ? intval($_GET['feed_id']) : 0;
        $is_editing = $feed_id > 0;
        $feed_title = '';
        $feed_config = array( // Default empty config
            'text_fields'  => array(),
            'media_fields' => array(),
            'toggles'      => array(),
        );

        // If editing, load existing data
        if ($is_editing) {
            $feed_post = get_post($feed_id);
            if ($feed_post && $feed_post->post_type === 'custom_feed') {
                // Security check: Ensure user can edit this specific post
                if (! current_user_can('edit_post', $feed_id)) {
                    wp_die(esc_html__('You do not have permission to edit this feed.', WORDPLUG_CF_TEXT_DOMAIN));
                }
                $feed_title = $feed_post->post_title;
                $feed_config_raw = get_post_meta($feed_id, '_feed_config', true);
                // Ensure config is an array and has expected keys, sanitize if necessary (though should be sanitized on save)
                if (is_array($feed_config_raw)) {
                    $feed_config['text_fields'] = isset($feed_config_raw['text_fields']) && is_array($feed_config_raw['text_fields']) ? $feed_config_raw['text_fields'] : array();
                    $feed_config['media_fields'] = isset($feed_config_raw['media_fields']) && is_array($feed_config_raw['media_fields']) ? $feed_config_raw['media_fields'] : array();
                    $feed_config['toggles'] = isset($feed_config_raw['toggles']) && is_array($feed_config_raw['toggles']) ? $feed_config_raw['toggles'] : array();
                }
            } else {
                // Invalid feed_id provided, treat as Add New or show error
                $is_editing = false;
                $feed_id = 0;
                // Optionally add an admin notice here if an invalid ID was passed
            }
        }

        // Include the view file, passing the data
        $edit_view_path = WORDPLUG_CF_PATH . 'admin/views/feed-edit-page.php';
        if (file_exists($edit_view_path)) {
            // Make variables available to the view
            include $edit_view_path;
        } else {
            echo '<div class="wrap"><h2>' . esc_html__('Error', WORDPLUG_CF_TEXT_DOMAIN) . '</h2><p>' . esc_html__('Edit page view file not found.', WORDPLUG_CF_TEXT_DOMAIN) . '</p></div>';
        }
    }

    /**
     * Enqueues the necessary scripts and styles for the admin pages.
     */
    public function enqueue_assets($hook = '')
    {
        // Only load scripts on our plugin's main and edit pages
        $main_slug = $this->main_menu_slug;
        $edit_slug = $main_slug . '-edit';
        if (
            (strpos($hook, $main_slug) === false) &&
            (strpos($hook, $edit_slug) === false)
        ) {
            return;
        }

        $js_path = dirname(__DIR__, 1) . '/admin/js/admin-scripts.js';
        if (!file_exists($js_path)) {
            error_log('[WordPlug] admin-scripts.js NOT FOUND at: ' . $js_path);
            add_action('admin_notices', function() use ($js_path) {
                echo '<div class="notice notice-error"><p>WordPlug: admin-scripts.js NOT FOUND at: ' . esc_html($js_path) . '</p></div>';
            });
            return;
        }

        wp_enqueue_script(
            'wordplug-admin-scripts',
            plugins_url('admin/js/admin-scripts.js', WORDPLUG_CF_PLUGIN_FILE),
            array(), // Add 'jquery' here if needed
            '1.0.0',
            true // Load in footer
        );
    }

    /**
     * Handles the submission of the feed edit/add form.
     */
    public function handle_save_feed()
    {
        // 1. Verify Nonce
        if (! isset($_POST['wordplug_save_feed_nonce']) || ! wp_verify_nonce(sanitize_key($_POST['wordplug_save_feed_nonce']), 'wordplug_save_feed_action')) {
            wp_die(esc_html__('Nonce verification failed.', WORDPLUG_CF_TEXT_DOMAIN), 'Error', array('response' => 403));
        }

        // 2. Check Capability
        if (! current_user_can($this->capability)) {
            wp_die(esc_html__('You do not have sufficient permissions to save this feed.', WORDPLUG_CF_TEXT_DOMAIN), 'Error', array('response' => 403));
        }

        // 3. Sanitize and Retrieve Data
        $feed_id    = isset($_POST['feed_id']) ? intval($_POST['feed_id']) : 0;
        $feed_title = isset($_POST['feed_title']) ? sanitize_text_field(wp_unslash($_POST['feed_title'])) : '';
        $feed_config_raw = isset($_POST['feed_config']) ? wp_unslash($_POST['feed_config']) : array(); // Expecting an array: ['text_fields'=>[], 'media_fields'=>[], 'toggles'=>[]]

        // Basic validation
        if (empty($feed_title)) {
            // Redirect back with error (or handle more gracefully)
            wp_safe_redirect(add_query_arg(array('page' => $this->main_menu_slug, 'error' => 'missing_title'), admin_url('admin.php')));
            exit;
        }

        // 4. Prepare Post Data
        $post_data = array(
            'post_title'  => $feed_title,
            'post_status' => 'publish', // Or 'draft' if needed
            'post_type'   => 'custom_feed',
        );

        if ($feed_id > 0) {
            $post_data['ID'] = $feed_id;
            $result = wp_update_post($post_data, true); // Pass true to return WP_Error on failure
        } else {
            $result = wp_insert_post($post_data, true); // Pass true to return WP_Error on failure
            if (! is_wp_error($result)) {
                $feed_id = $result; // Get the new post ID
            }
        }

        // Check for errors saving post
        if (is_wp_error($result)) {
            // Redirect back with error
            wp_safe_redirect(add_query_arg(array('page' => $this->main_menu_slug, 'error' => 'save_post_failed', 'message' => urlencode($result->get_error_message())), admin_url('admin.php')));
            exit;
        }

        // 5. Sanitize and Save Feed Configuration Meta (Example: saving the raw structure)
        // You might want more specific sanitization based on expected keys/values
        $sanitized_config = $this->sanitize_feed_config($feed_config_raw);
        update_post_meta($feed_id, '_feed_config', $sanitized_config);

        // 6. Redirect on Success
        wp_safe_redirect(add_query_arg(array('page' => $this->main_menu_slug, 'message' => 'feed_saved'), admin_url('admin.php')));
        exit;
    }

    /**
     * Sanitizes the feed configuration array.
     *
     * @param array $config Raw config array from $_POST.
     * @return array Sanitized config array.
     */
    private function sanitize_feed_config($config)
    {
        $sanitized = array(
            'text_fields'  => array(),
            'media_fields' => array(),
            'toggles'      => array(),
        );

        if (isset($config['text_fields']) && is_array($config['text_fields'])) {
            foreach ($config['text_fields'] as $index => $field) {
                if (isset($field['key']) && ! empty($field['key'])) {
                    $sanitized['text_fields'][sanitize_key($index)] = array(
                        'key'   => sanitize_text_field($field['key']),
                        'value' => isset($field['value']) ? sanitize_textarea_field($field['value']) : '',
                    );
                }
            }
        } // End text_fields loop

        if (isset($config['media_fields']) && is_array($config['media_fields'])) {
            foreach ($config['media_fields'] as $index => $field) {
                if (isset($field['key']) && ! empty($field['key'])) {
                    $sanitized['media_fields'][sanitize_key($index)] = array(
                        'key'   => sanitize_text_field($field['key']),
                        'value' => isset($field['value']) ? esc_url_raw($field['value']) : '', // Sanitize as URL
                    );
                }
            }
        }

        if (isset($config['toggles']) && is_array($config['toggles'])) {
            foreach ($config['toggles'] as $index => $field) {
                if (isset($field['key']) && ! empty($field['key'])) {
                    $sanitized['toggles'][sanitize_key($index)] = array(
                        'key'   => sanitize_text_field($field['key']),
                        'value' => (isset($field['value']) && $field['value'] === 'on') ? 'on' : 'off', // Ensure only 'on' or 'off'
                    );
                }
            }
        }

        return $sanitized;
    }

    /**
     * Handles the deletion of a feed via admin-post.php.
     */
    public function handle_delete_feed()
    {
        // 1. Get Feed ID and Verify Nonce
        $feed_id = isset($_GET['feed_id']) ? intval($_GET['feed_id']) : 0;
        $nonce   = isset($_GET['wordplug_delete_feed_nonce']) ? sanitize_key($_GET['wordplug_delete_feed_nonce']) : '';

        if (! $feed_id || ! wp_verify_nonce($nonce, 'wordplug_delete_feed_action_' . $feed_id)) {
            wp_die(esc_html__('Invalid request or nonce verification failed.', WORDPLUG_CF_TEXT_DOMAIN), 'Error', array('response' => 403));
        }

        // 2. Check Capability
        if (! current_user_can($this->capability)) { // Or a more specific capability like 'delete_post', $feed_id
            wp_die(esc_html__('You do not have sufficient permissions to delete this feed.', WORDPLUG_CF_TEXT_DOMAIN), 'Error', array('response' => 403));
        }

        // 3. Check Post Type
        if (get_post_type($feed_id) !== 'custom_feed') {
            wp_die(esc_html__('Invalid feed ID.', WORDPLUG_CF_TEXT_DOMAIN), 'Error', array('response' => 400));
        }

        // 4. Delete Post (Move to Trash by default)
        $result = wp_delete_post($feed_id, false); // false = move to trash, true = force delete

        // 5. Redirect on Success/Failure
        if ($result) {
            // Success
            wp_safe_redirect(add_query_arg(array('page' => $this->main_menu_slug, 'message' => 'feed_deleted'), admin_url('admin.php')));
        } else {
            // Failure
            wp_safe_redirect(add_query_arg(array('page' => $this->main_menu_slug, 'error' => 'delete_failed'), admin_url('admin.php')));
        }
        exit;
    }

    /**
     * Displays admin notices based on query parameters.
     */
    public function display_admin_notices()
    {
        // Check if we are on our plugin's main page
        $screen = get_current_screen();
        if (! $screen || $screen->id !== 'toplevel_page_' . $this->main_menu_slug) {
            return;
        }

        $message = isset($_GET['message']) ? sanitize_key($_GET['message']) : '';
        $error   = isset($_GET['error']) ? sanitize_key($_GET['error']) : '';

        if ($message === 'feed_saved') {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Custom feed saved successfully.', WORDPLUG_CF_TEXT_DOMAIN) . '</p></div>';
        } elseif ($message === 'feed_deleted') {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Custom feed moved to Trash.', WORDPLUG_CF_TEXT_DOMAIN) . '</p></div>';
        }

        if ($error === 'missing_title') {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Error: Feed title cannot be empty.', WORDPLUG_CF_TEXT_DOMAIN) . '</p></div>';
        } elseif ($error === 'save_post_failed') {
            $error_message = isset($_GET['message']) ? esc_html(urldecode(sanitize_text_field($_GET['message']))) : '';
            echo '<div class="notice notice-error is-dismissible"><p>' . sprintf(esc_html__('Error saving feed: %s', WORDPLUG_CF_TEXT_DOMAIN), $error_message) . '</p></div>';
        } elseif ($error === 'delete_failed') {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Error deleting feed.', WORDPLUG_CF_TEXT_DOMAIN) . '</p></div>';
        }
    }
} // End class WordPlug_CF_Admin_Menu
// Instantiate only if in admin area
// if ( is_admin() ) {
//     new WordPlug_CF_Admin_Menu();
// }
// We will instantiate this from the main plugin file later.
