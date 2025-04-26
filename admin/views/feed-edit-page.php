<?php

/**
 * View for the admin page to add or edit a Custom Feed.
 *
 * @package WordPlug_Custom_Feeds
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

// Security check
if (! current_user_can('manage_options')) { // Assuming 'manage_options' capability
    wp_die(esc_html__('You do not have sufficient permissions to access this page.', WORDPLUG_CF_TEXT_DOMAIN));
}

// Determine if editing or adding new (basic example, needs refinement)
$feed_id = isset($_GET['feed_id']) ? intval($_GET['feed_id']) : 0;
$is_editing = $feed_id > 0;
$page_title = $is_editing ? __('Edit Custom Feed', WORDPLUG_CF_TEXT_DOMAIN) : __('Add New Custom Feed', WORDPLUG_CF_TEXT_DOMAIN);
$feed_title = ''; // Placeholder for existing title if editing

// TODO: Load existing feed data if $is_editing

?>

<div class="wrap">
    <h1><?php echo esc_html($page_title); ?></h1>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); // Point to admin-post.php for processing 
                                ?>">
        <?php wp_nonce_field('wordplug_save_feed_action', 'wordplug_save_feed_nonce'); ?>
        <input type="hidden" name="action" value="wordplug_save_feed">
        <input type="hidden" name="feed_id" value="<?php echo esc_attr($feed_id); ?>">

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="feed_title"><?php esc_html_e('Feed Title', WORDPLUG_CF_TEXT_DOMAIN); ?></label>
                    </th>
                    <td>
                        <!-- MDC Outlined Text Field for Title -->
                        <label class="mdc-text-field mdc-text-field--outlined" data-mdc-auto-init="MDCTextField" style="width: 100%;">
                            <span class="mdc-notched-outline">
                                <span class="mdc-notched-outline__leading"></span>
                                <span class="mdc-notched-outline__notch">
                                    <span class="mdc-floating-label" id="feed-title-label"><?php esc_html_e('Feed Title', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
                                </span>
                                <span class="mdc-notched-outline__trailing"></span>
                            </span>
                            <input type="text" class="mdc-text-field__input" id="feed_title" name="feed_title" aria-labelledby="feed-title-label" value="<?php echo esc_attr($feed_title); ?>" required>
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>

        <hr>

        <h2><?php esc_html_e('Feed Configuration', WORDPLUG_CF_TEXT_DOMAIN); ?></h2>
        <p><?php esc_html_e('Configure the fields and toggles for this feed.', WORDPLUG_CF_TEXT_DOMAIN); ?></p>

        <!-- Dynamic sections for Text Fields, Media Fields, Toggles using MDC -->
        <div id="feed-config-container">

            <!-- Text Fields Section -->
            <div class="feed-config-section">
                <h4><?php esc_html_e('Text Fields', WORDPLUG_CF_TEXT_DOMAIN); ?></h4>
                <div id="text-fields-list">
                    <!-- Dynamically added text fields will go here -->
                    <p><i><?php esc_html_e('No text fields added yet.', WORDPLUG_CF_TEXT_DOMAIN); ?></i></p>
                </div>
                <button type="button" class="mdc-button mdc-button--outlined" id="add-text-field-button">
                    <span class="mdc-button__ripple"></span>
                    <span class="mdc-button__label"><?php esc_html_e('Add Text Field', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
                </button>
            </div>

            <hr style="margin: 20px 0;">

            <!-- Media Fields Section -->
            <div class="feed-config-section">
                <h4><?php esc_html_e('Media Fields', WORDPLUG_CF_TEXT_DOMAIN); ?></h4>
                <div id="media-fields-list">
                    <!-- Dynamically added media fields will go here -->
                    <p><i><?php esc_html_e('No media fields added yet.', WORDPLUG_CF_TEXT_DOMAIN); ?></i></p>
                </div>
                <button type="button" class="mdc-button mdc-button--outlined" id="add-media-field-button">
                    <span class="mdc-button__ripple"></span>
                    <span class="mdc-button__label"><?php esc_html_e('Add Media Field', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
                </button>
            </div>

            <hr style="margin: 20px 0;">

            <!-- Toggle Switches Section -->
            <div class="feed-config-section">
                <h4><?php esc_html_e('Toggle Switches', WORDPLUG_CF_TEXT_DOMAIN); ?></h4>
                <div id="toggle-switches-list">
                    <?php if (! empty($feed_config['toggles'])) : ?>
                        <?php foreach ($feed_config['toggles'] as $index => $field) : ?>
                            <?php $is_on = isset($field['value']) && $field['value'] === 'on'; ?>
                            <div class="feed-config-item" data-index="<?php echo esc_attr($index); ?>" style="margin-bottom: 15px; padding: 10px; border: 1px solid #eee;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                    <label class="mdc-text-field mdc-text-field--outlined mdc-text-field--dense" data-mdc-auto-init="MDCTextField" style="flex-grow: 1;">
                                        <span class="mdc-notched-outline mdc-notched-outline--upgraded <?php echo ! empty($field['key']) ? 'mdc-notched-outline--notched' : ''; ?>">
                                            <span class="mdc-notched-outline__leading"></span>
                                            <span class="mdc-notched-outline__notch">
                                                <span class="mdc-floating-label <?php echo ! empty($field['key']) ? 'mdc-floating-label--float-above' : ''; ?>">Toggle Key</span>
                                            </span>
                                            <span class="mdc-notched-outline__trailing"></span>
                                        </span>
                                        <input type="text" class="mdc-text-field__input" name="feed_config[toggles][<?php echo esc_attr($index); ?>][key]" value="<?php echo esc_attr($field['key']); ?>" required>
                                    </label>
                                    <div class="mdc-form-field" style="flex-grow: 0;">
                                        <div class="mdc-switch <?php echo $is_on ? 'mdc-switch--selected' : 'mdc-switch--unselected'; ?>" data-mdc-auto-init="MDCSwitch">
                                            <div class="mdc-switch__track"></div>
                                            <div class="mdc-switch__handle-track">
                                                <div class="mdc-switch__handle">
                                                    <div class="mdc-switch__shadow">
                                                        <div class="mdc-elevation-overlay"></div>
                                                    </div>
                                                    <div class="mdc-switch__ripple"></div>
                                                    <div class="mdc-switch__icons">
                                                        <svg class="mdc-switch__icon mdc-switch__icon--on" viewBox="0 0 24 24">
                                                            <path d="M19.69,5.23L8.96,15.96l-4.23-4.23L2.96,13.5l6,6L21.46,7L19.69,5.23z" />
                                                        </svg>
                                                        <svg class="mdc-switch__icon mdc-switch__icon--off" viewBox="0 0 24 24">
                                                            <path d="M20 13H4v-2h16v2z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <label style="margin-left: 10px;">Default Off / On</label>
                                        <!-- Hidden input to store the actual value (on/off) -->
                                        <input type="hidden" class="toggle-value" name="feed_config[toggles][<?php echo esc_attr($index); ?>][value]" value="<?php echo $is_on ? 'on' : 'off'; ?>">
                                    </div>
                                </div>
                                <button type="button" class="mdc-button mdc-button--outlined mdc-button--dense remove-feed-item-button" style="--mdc-theme-primary: red;">
                                    <span class="mdc-button__ripple"></span>
                                    <span class="mdc-button__label">Remove</span>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="no-items-placeholder"><?php esc_html_e('No toggle switches added yet.', WORDPLUG_CF_TEXT_DOMAIN); ?></p>
                    <?php endif; ?>
                </div>
                <button type="button" class="mdc-button mdc-button--outlined" id="add-toggle-switch-button">
                    <span class="mdc-button__ripple"></span>
                    <span class="mdc-button__label"><?php esc_html_e('Add Toggle Switch', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
                </button>
            </div>

        </div>

        <?php submit_button($is_editing ? __('Update Feed', WORDPLUG_CF_TEXT_DOMAIN) : __('Create Feed', WORDPLUG_CF_TEXT_DOMAIN)); ?>
    </form>
</div>