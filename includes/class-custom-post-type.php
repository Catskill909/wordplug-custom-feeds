<?php

/**
 * Registers the Custom Post Type for Custom Feeds.
 *
 * @package WordPlug_Custom_Feeds
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class WordPlug_CF_Custom_Post_Type
 *
 * Handles the registration and configuration of the 'custom_feed' CPT.
 */
class WordPlug_CF_Custom_Post_Type
{

    /**
     * The slug for the custom post type.
     *
     * @var string
     */
    private $post_type = 'custom_feed';

    /**
     * Constructor. Hooks into WordPress actions.
     */
    public function __construct()
    {
        add_action('init', array($this, 'register_post_type'));
        // Add activation hook for flushing rewrite rules
        register_activation_hook(WORDPLUG_CF_BASENAME, array($this, 'activation_flush_rewrite_rules'));
        // Add deactivation hook if needed, though flushing on activation is usually sufficient
        // register_deactivation_hook( WORDPLUG_CF_BASENAME, array( $this, 'deactivation_flush_rewrite_rules' ) );
    }

    /**
     * Registers the custom post type.
     */
    public function register_post_type()
    {
        $labels = array(
            'name'                  => _x('Custom Feeds', 'Post type general name', WORDPLUG_CF_TEXT_DOMAIN),
            'singular_name'         => _x('Custom Feed', 'Post type singular name', WORDPLUG_CF_TEXT_DOMAIN),
            'menu_name'             => _x('Custom Feeds', 'Admin Menu text', WORDPLUG_CF_TEXT_DOMAIN),
            'name_admin_bar'        => _x('Custom Feed', 'Add New on Toolbar', WORDPLUG_CF_TEXT_DOMAIN),
            'add_new'               => __('Add New', WORDPLUG_CF_TEXT_DOMAIN),
            'add_new_item'          => __('Add New Custom Feed', WORDPLUG_CF_TEXT_DOMAIN),
            'new_item'              => __('New Custom Feed', WORDPLUG_CF_TEXT_DOMAIN),
            'edit_item'             => __('Edit Custom Feed', WORDPLUG_CF_TEXT_DOMAIN),
            'view_item'             => __('View Custom Feed', WORDPLUG_CF_TEXT_DOMAIN),
            'all_items'             => __('All Custom Feeds', WORDPLUG_CF_TEXT_DOMAIN),
            'search_items'          => __('Search Custom Feeds', WORDPLUG_CF_TEXT_DOMAIN),
            'parent_item_colon'     => __('Parent Custom Feeds:', WORDPLUG_CF_TEXT_DOMAIN),
            'not_found'             => __('No custom feeds found.', WORDPLUG_CF_TEXT_DOMAIN),
            'not_found_in_trash'    => __('No custom feeds found in Trash.', WORDPLUG_CF_TEXT_DOMAIN),
            'featured_image'        => _x('Custom Feed Cover Image', 'Overrides the “Featured Image” phrase for this post type.', WORDPLUG_CF_TEXT_DOMAIN),
            'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type.', WORDPLUG_CF_TEXT_DOMAIN),
            'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type.', WORDPLUG_CF_TEXT_DOMAIN),
            'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type.', WORDPLUG_CF_TEXT_DOMAIN),
            'archives'              => _x('Custom Feed archives', 'The post type archive label used in nav menus.', WORDPLUG_CF_TEXT_DOMAIN),
            'insert_into_item'      => _x('Insert into custom feed', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post).', WORDPLUG_CF_TEXT_DOMAIN),
            'uploaded_to_this_item' => _x('Uploaded to this custom feed', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post).', WORDPLUG_CF_TEXT_DOMAIN),
            'filter_items_list'     => _x('Filter custom feeds list', 'Screen reader text for the filter links heading on the post type listing screen.', WORDPLUG_CF_TEXT_DOMAIN),
            'items_list_navigation' => _x('Custom Feeds list navigation', 'Screen reader text for the pagination heading on the post type listing screen.', WORDPLUG_CF_TEXT_DOMAIN),
            'items_list'            => _x('Custom Feeds list', 'Screen reader text for the items list heading on the post type listing screen.', WORDPLUG_CF_TEXT_DOMAIN),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false, // Not publicly queryable via standard WP queries
            'publicly_queryable' => false, // Not queryable via ?custom_feed=...
            'show_ui'            => true,  // Show in admin UI
            'show_in_menu'       => false, // We will add our own menu page
            'query_var'          => false, // No query var needed
            'rewrite'            => false, // No frontend rewrite rules needed
            'capability_type'    => 'post', // Use standard post capabilities
            'has_archive'        => false, // No archive page needed
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title'), // Only support title initially
            'show_in_rest'       => false, // We will handle REST API exposure separately
            'menu_icon'          => 'dashicons-rss', // Example icon
        );

        register_post_type($this->post_type, $args);
    }

    /**
     * Flush rewrite rules on plugin activation.
     */
    public function activation_flush_rewrite_rules()
    {
        $this->register_post_type(); // Ensure CPT is registered before flushing
        flush_rewrite_rules();
    }

    /**
     * Flush rewrite rules on plugin deactivation (optional).
     */
    // public function deactivation_flush_rewrite_rules() {
    //  flush_rewrite_rules();
    // }
}

// Instantiate the class
new WordPlug_CF_Custom_Post_Type();
