<?php
error_log('[WordPlug] wordplug.php loaded');

/**
 * Plugin Name:       WordPlug Custom Feeds
 * Plugin URI:        #
 * Description:       Creates custom REST API feeds with a Material Design admin interface.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            Your Name or Company
 * Author URI:        #
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wordplug-custom-feeds
 * Domain Path:       /languages
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

// Define constants
if (!defined('WORDPLUG_CF_PLUGIN_FILE')) {
    define('WORDPLUG_CF_PLUGIN_FILE', __FILE__);
}
define('WORDPLUG_CF_VERSION', '1.0.0');
define('WORDPLUG_CF_PATH', plugin_dir_path(__FILE__));
define('WORDPLUG_CF_URL', plugin_dir_url(__FILE__));
define('WORDPLUG_CF_BASENAME', plugin_basename(__FILE__));
define('WORDPLUG_CF_TEXT_DOMAIN', 'wordplug-custom-feeds');


// Include core files
require_once WORDPLUG_CF_PATH . 'includes/class-custom-post-type.php';
require_once WORDPLUG_CF_PATH . 'includes/class-rest-api.php';
require_once WORDPLUG_CF_PATH . 'includes/helpers.php';
require_once WORDPLUG_CF_PATH . 'admin/class-admin-menu.php';


/**
 * Load plugin textdomain.
 */
function wordplug_cf_load_textdomain()
{
    load_plugin_textdomain(
        WORDPLUG_CF_TEXT_DOMAIN,
        false,
        dirname(WORDPLUG_CF_BASENAME) . '/languages'
    );
}
add_action('plugins_loaded', 'wordplug_cf_load_textdomain');


/**
 * Activation hook.
 */
function wordplug_cf_activate()
{
    // Placeholder for activation tasks (e.g., flush rewrite rules if CPT is registered here)
    // error_log('WordPlug Custom Feeds Activated'); // Example logging
}
register_activation_hook(__FILE__, 'wordplug_cf_activate');

/**
 * Deactivation hook.
 */
function wordplug_cf_deactivate()
{
    // Delete all custom_feed posts and their meta
    $custom_feeds = get_posts([
        'post_type' => 'custom_feed',
        'post_status' => 'any',
        'numberposts' => -1,
        'fields' => 'ids',
    ]);
    if ($custom_feeds) {
        foreach ($custom_feeds as $feed_id) {
            // Delete post meta
            delete_post_meta($feed_id, '_feed_config');
            delete_post_meta($feed_id, '_feed_values');
            // Delete the post itself
            wp_delete_post($feed_id, true); // Force delete
        }
    }

    // Remove any plugin-specific options if used
    // delete_option('wordplug_cf_some_option');
    // delete_option('wordplug_cf_another_option');

    // Optionally, flush rewrite rules if necessary
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'wordplug_cf_deactivate');


// Initialize plugin components
function wordplug_cf_init()
{
    // CPT class hooks itself in its constructor via the require_once above.

    // Instantiate Admin class only in the admin area
    if (is_admin()) {
        new WordPlug_CF_Admin_Menu();
    }

    // Instantiate REST API class
    new WordPlug_CF_REST_API();

    // Shortcode class not implemented. If needed, add require_once and class implementation.
    // new WordPlug_CF_Shortcode();
}
add_action('plugins_loaded', 'wordplug_cf_init');
