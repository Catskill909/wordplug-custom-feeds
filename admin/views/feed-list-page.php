<?php

/**
 * View for the main admin page listing the Custom Feeds.
 *
 * @package WordPlug_Custom_Feeds
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

// Security check (redundant as it's checked in the calling function, but good practice)
if (! current_user_can('manage_options')) { // Assuming 'manage_options' capability
    wp_die(esc_html__('You do not have sufficient permissions to access this page.', WORDPLUG_CF_TEXT_DOMAIN));
}

?>

<div class="wrap">
    <h1><?php echo esc_html__('Custom Feeds', WORDPLUG_CF_TEXT_DOMAIN); ?></h1>

    <p><?php echo esc_html__('This page will list all created custom feeds.', WORDPLUG_CF_TEXT_DOMAIN); ?></p>

    <!-- Add New Feed Button (Modern Material Design) -->
    <div class="add-feed-btn-row" style="display: flex; justify-content: center; align-items: center; margin-bottom: 32px;">
        <a href="<?php echo esc_url(admin_url('admin.php?page=' . $main_menu_slug . '-edit')); ?>"
           class="mdc-button mdc-button--raised mdc-button--primary add-feed-btn"
           style="font-size: 1.1rem; letter-spacing: 0.02em; padding: 0 28px; height: 44px;">
            <span class="mdc-button__ripple"></span>
            <span class="mdc-button__label">
                <?php echo esc_html__('Add New Feed', WORDPLUG_CF_TEXT_DOMAIN); ?>
            </span>
        </a>
    </div>

    <!-- MDC Data Table -->
    <div id="feed-list-table-container" class="mdc-data-table" style="margin-top: 20px;">
        <div class="mdc-data-table__table-container">
            <table class="mdc-data-table__table" aria-label="<?php esc_attr_e('Custom Feeds', WORDPLUG_CF_TEXT_DOMAIN); ?>">
                <thead>
                    <tr class="mdc-data-table__header-row">
                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col"><?php esc_html_e('Feed Title', WORDPLUG_CF_TEXT_DOMAIN); ?></th>
                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col"><?php esc_html_e('API Endpoint URL', WORDPLUG_CF_TEXT_DOMAIN); ?></th>
                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col"><?php esc_html_e('Actions', WORDPLUG_CF_TEXT_DOMAIN); ?></th>
                    </tr>
                </thead>
                <tbody class="mdc-data-table__content">
                    <?php if (! empty($feeds)) : ?>
                        <?php foreach ($feeds as $feed) : ?>
                            <?php
                            // Construct the API endpoint URL
                            $api_url = rest_url('wordplug/v1/feed/' . $feed->post_name);
                            // Construct the Edit URL
                            // We need a dedicated edit page handler or use the CPT edit screen.
                            // Link to the registered hidden edit page slug
                            $edit_url = add_query_arg(
                                array(
                                    'page'    => $this->main_menu_slug . '-edit', // Use the registered slug
                                    'feed_id' => $feed->ID,
                                ),
                                admin_url('admin.php')
                            );
                            // Construct Delete URL (needs nonce and handler)
                            $delete_url = wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action'  => 'wordplug_delete_feed', // We'll need an admin-post action for this
                                        'feed_id' => $feed->ID,
                                    ),
                                    admin_url('admin-post.php')
                                ),
                                'wordplug_delete_feed_action_' . $feed->ID, // Nonce action name
                                'wordplug_delete_feed_nonce' // Nonce name
                            );
                            ?>
                            <tr class="mdc-data-table__row" data-row-id="<?php echo esc_attr($feed->ID); ?>">
                                <td class="mdc-data-table__cell">
                                    <a href="<?php echo esc_url($edit_url); ?>"><?php echo esc_html($feed->post_title); ?></a>
                                </td>
                                <td class="mdc-data-table__cell">
                                    <a href="<?php echo esc_url($api_url); ?>" target="_blank" title="<?php esc_attr_e('Open API endpoint in new tab', WORDPLUG_CF_TEXT_DOMAIN); ?>"><?php echo esc_html($api_url); ?></a>
                                </td>
                                <td class="mdc-data-table__cell">
                                    <a href="<?php echo esc_url($edit_url); ?>" class="mdc-button mdc-button--outlined mdc-button--dense">
                                        <span class="mdc-button__ripple"></span>
                                        <span class="mdc-button__label"><?php esc_html_e('Edit', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
                                    </a>
                                    <a href="<?php echo esc_url($delete_url); ?>" class="mdc-button mdc-button--outlined mdc-button--dense mdc-button--danger" style="--mdc-theme-primary: red; margin-left: 8px;" onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this feed? This cannot be undone.', WORDPLUG_CF_TEXT_DOMAIN); ?>');">
                                        <span class="mdc-button__ripple"></span>
                                        <span class="mdc-button__label"><?php esc_html_e('Delete', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr class="mdc-data-table__row">
                            <td class="mdc-data-table__cell" colspan="3"><?php esc_html_e('No custom feeds found. Add one!', WORDPLUG_CF_TEXT_DOMAIN); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>