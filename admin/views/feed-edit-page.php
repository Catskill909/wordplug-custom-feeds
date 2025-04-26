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

// Variables $feed_id, $is_editing, $page_title, $feed_title, and $feed_config are set in render_edit_page().

?>

<div class="wrap">
<style>
/* Container and Card Padding */
.feed-edit-card, .mdc-card__content {
    padding: 2rem 2.5rem 2.5rem 2.5rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    margin-bottom: 2rem;
}
.feed-edit-section-header, .feed-edit-section-desc, .feed-config-section, .no-items-placeholder {
    padding-left: 0.25rem;
    padding-right: 0.25rem;
}

/* Modern Button Styles */
.mdc-button, .button, .add-field-btn, .add-toggle-btn, .add-media-btn {
    font-size: 1rem;
    font-weight: 500;
    border-radius: 6px;
    padding: 0.7em 1.7em;
    border: none;
    outline: none;
    cursor: pointer;
    transition: box-shadow 0.2s, background 0.2s, color 0.2s, border 0.2s;
    box-shadow: 0 1px 2px rgba(60,60,60,0.07);
    margin-right: 0.5em;
    margin-bottom: 0.5em;
}
.mdc-button--primary, .button-primary, .create-feed-btn {
    background: #6200ee;
    color: #fff;
    box-shadow: 0 2px 8px rgba(98,0,238,0.09);
    border: none;
}
.mdc-button--primary:hover, .button-primary:hover, .create-feed-btn:hover {
    background: #3700b3;
    box-shadow: 0 4px 16px rgba(98,0,238,0.13);
}
.mdc-button--outlined, .button-secondary, .cancel-btn {
    background: #fff;
    color: #6200ee;
    border: 2px solid #bdbdbd;
}
.mdc-button--outlined:hover, .button-secondary:hover, .cancel-btn:hover {
    background: #f3f0fa;
    border-color: #6200ee;
    color: #3700b3;
}
.add-field-btn, .add-toggle-btn, .add-media-btn, #add-text-field-button, #add-media-field-button, #add-toggle-switch-button {
    background: #fff;
    color: #6200ee;
    border: 2px solid #6200ee;
    font-weight: 600;
}
.add-field-btn:hover, .add-toggle-btn:hover, .add-media-btn:hover, #add-text-field-button:hover, #add-media-field-button:hover, #add-toggle-switch-button:hover {
    background: #f3f0fa;
    color: #3700b3;
    border-color: #3700b3;
}
.mdc-button:focus, .button:focus, .add-field-btn:focus, .add-toggle-btn:focus, .add-media-btn:focus, .create-feed-btn:focus, .cancel-btn:focus, #add-text-field-button:focus, #add-media-field-button:focus, #add-toggle-switch-button:focus {
    box-shadow: 0 0 0 3px #bdbdbd, 0 2px 8px rgba(98,0,238,0.09);
    outline: none;
}

/* Responsive tweaks */
@media (max-width: 600px) {
    .feed-edit-card, .mdc-card__content {
        padding: 1rem 0.5rem;
    }
}
.remove-feed-item-button {
    background: #b00020;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    font-size: 1rem;
    padding: 0.6em 1.3em;
    margin: 0.5em 0;
    box-shadow: 0 1px 4px rgba(176,0,32,0.08);
    cursor: pointer;
    transition: background 0.2s, box-shadow 0.2s, color 0.2s;
    display: inline-block;
    letter-spacing: 0.02em;
}
.remove-feed-item-button:hover, .remove-feed-item-button:focus {
    background: #d32f2f;
    color: #fff;
    box-shadow: 0 2px 8px rgba(176,0,32,0.18);
    outline: 2px solid #b00020;
}
</style>

    <h1><?php echo esc_html($page_title); ?></h1>

    <!-- Snackbar for feedback messages -->
    <div id="form-snackbar" class="mdc-snackbar" aria-live="polite">
      <div class="mdc-snackbar__surface">
        <div class="mdc-snackbar__label" role="status" aria-live="polite"></div>
        <div class="mdc-snackbar__actions">
          <button type="button" class="mdc-icon-button mdc-snackbar__dismiss material-icons" title="Dismiss">close</button>
        </div>
      </div>
    </div>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php
        // DEBUG: Show config arrays for admin troubleshooting (remove after debugging)
        if (current_user_can('manage_options') && !empty($is_editing)) {
            echo '<div style="background: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 10px; margin-bottom: 16px; font-size: 13px;">';


            echo '</div>';
        }
        ?>
        <?php wp_nonce_field('wordplug_save_feed_action', 'wordplug_save_feed_nonce'); ?>
        <input type="hidden" name="action" value="wordplug_save_feed">
        <input type="hidden" name="feed_id" value="<?php echo esc_attr($feed_id); ?>">

        <!-- Card for Feed Title -->
        <div class="mdc-card mdc-card--outlined feed-edit-card" style="margin-bottom: 32px;">
            <div class="mdc-card__content">
                <label class="mdc-text-field mdc-text-field--outlined feed-title-field" data-mdc-auto-init="MDCTextField">
                    <span class="mdc-notched-outline">
                        <span class="mdc-notched-outline__leading"></span>
                        <span class="mdc-notched-outline__notch">
                            <span class="mdc-floating-label" id="feed-title-label"><?php esc_html_e('Feed Title', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
                        </span>
                        <span class="mdc-notched-outline__trailing"></span>
                    </span>
                    <input type="text" class="mdc-text-field__input" id="feed_title" name="feed_title" aria-labelledby="feed-title-label" value="<?php echo esc_attr($feed_title); ?>" required autocomplete="off" onfocus="this.parentElement.classList.add('mdc-text-field--label-floating'); document.getElementById('feed-title-label').classList.add('mdc-floating-label--float-above');" onblur="if(!this.value){this.parentElement.classList.remove('mdc-text-field--label-floating'); document.getElementById('feed-title-label').classList.remove('mdc-floating-label--float-above');}">
                </label>
                <script>
                // Float label if field is pre-filled
                document.addEventListener('DOMContentLoaded', function() {
                    var feedTitle = document.getElementById('feed_title');
                    if (feedTitle && feedTitle.value) {
                        var label = document.getElementById('feed-title-label');
                        if (label) label.classList.add('mdc-floating-label--float-above');
                        feedTitle.parentElement.classList.add('mdc-text-field--label-floating');
                    }
                });
                </script>
            </div>
        </div>

        <!-- Back to Feed List Button -->
        <div style="margin-bottom: 20px;">
            <a href="<?php echo esc_url(admin_url('admin.php?page=wordplug-custom-feeds')); ?>" class="mdc-button mdc-button--outlined" aria-label="<?php esc_attr_e('Back to Feed List', WORDPLUG_CF_TEXT_DOMAIN); ?>">
                <span class="mdc-button__ripple"></span>
                <span class="mdc-button__label"><?php esc_html_e('Back to Feed List', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
            </a>
        </div>

        <!-- Card for Feed Configuration -->
        <div class="mdc-card mdc-card--outlined feed-edit-card">
            <div class="mdc-card__content">
                <div class="feed-edit-section-header"><?php esc_html_e('Feed Configuration', WORDPLUG_CF_TEXT_DOMAIN); ?></div>
                <div class="feed-edit-section-desc"><?php esc_html_e('Configure the fields and toggles for this feed.', WORDPLUG_CF_TEXT_DOMAIN); ?></div>
                <div id="feed-config-container">

                    <!-- Text Fields Section -->
                    <div class="feed-config-section">
                        <h4 class="feed-edit-section-header"><?php esc_html_e('Text Fields', WORDPLUG_CF_TEXT_DOMAIN); ?></h4>
                        <div id="text-fields-list" aria-label="<?php esc_attr_e('Text Fields List', WORDPLUG_CF_TEXT_DOMAIN); ?>">
                            <?php if (!empty($feed_config['text_fields'])): ?>
                                <?php foreach ($feed_config['text_fields'] as $index => $field): ?>
                                    <div class="feed-config-item" data-index="<?php echo esc_attr($index); ?>">
                                        <label class="mdc-text-field mdc-text-field--outlined mdc-text-field--dense" data-mdc-auto-init="MDCTextField">
                                            <span class="mdc-notched-outline mdc-notched-outline--upgraded <?php echo !empty($field['key']) ? 'mdc-notched-outline--notched' : ''; ?>">
                                                <span class="mdc-notched-outline__leading"></span>
                                                <span class="mdc-notched-outline__notch">
                                                    <span class="mdc-floating-label mdc-floating-label--float-above">Text Key</span>
                                                </span>
                                                <span class="mdc-notched-outline__trailing"></span>
                                            </span>
                                            <input type="text" class="mdc-text-field__input" name="feed_config[text_fields][<?php echo esc_attr($index); ?>][key]" value="<?php echo esc_attr($field['key']); ?>" required aria-label="<?php esc_attr_e('Text Key', WORDPLUG_CF_TEXT_DOMAIN); ?>">
                                        </label>
                                        <button type="button" class="remove-feed-item-button">
                                            <span><?php esc_html_e('Remove', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="no-items-placeholder"><i><?php esc_html_e('No text fields added yet.', WORDPLUG_CF_TEXT_DOMAIN); ?></i></p>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="mdc-button mdc-button--outlined" id="add-text-field-button" aria-label="<?php esc_attr_e('Add Text Field', WORDPLUG_CF_TEXT_DOMAIN); ?>">
                            <span class="mdc-button__ripple"></span>
                            <span class="mdc-button__label"><?php esc_html_e('Add Text Field', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
                        </button>
                    </div>

                    <!-- Media Fields Section -->
                    <div class="feed-config-section">
                        <h4 class="feed-edit-section-header"><?php esc_html_e('Media Fields', WORDPLUG_CF_TEXT_DOMAIN); ?></h4>
                        <div id="media-fields-list" aria-label="<?php esc_attr_e('Media Fields List', WORDPLUG_CF_TEXT_DOMAIN); ?>">
                            <?php if (!empty($feed_config['media_fields'])): ?>
                                <?php foreach ($feed_config['media_fields'] as $index => $field): ?>
                                    <div class="feed-config-item" data-index="<?php echo esc_attr($index); ?>">
                                        <label class="mdc-text-field mdc-text-field--outlined mdc-text-field--dense" data-mdc-auto-init="MDCTextField">
                                            <span class="mdc-notched-outline mdc-notched-outline--upgraded <?php echo !empty($field['key']) ? 'mdc-notched-outline--notched' : ''; ?>">
                                                <span class="mdc-notched-outline__leading"></span>
                                                <span class="mdc-notched-outline__notch">
                                                    <span class="mdc-floating-label mdc-floating-label--float-above">Media Key</span>
                                                </span>
                                                <span class="mdc-notched-outline__trailing"></span>
                                            </span>
                                            <input type="text" class="mdc-text-field__input" name="feed_config[media_fields][<?php echo esc_attr($index); ?>][key]" value="<?php echo esc_attr($field['key']); ?>" required aria-label="<?php esc_attr_e('Media Key', WORDPLUG_CF_TEXT_DOMAIN); ?>">
                                        </label>
                                        <button type="button" class="remove-feed-item-button">
                                            <span><?php esc_html_e('Remove', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="no-items-placeholder"><i><?php esc_html_e('No media fields added yet.', WORDPLUG_CF_TEXT_DOMAIN); ?></i></p>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="mdc-button mdc-button--outlined" id="add-media-field-button" aria-label="<?php esc_attr_e('Add Media Field', WORDPLUG_CF_TEXT_DOMAIN); ?>">
                            <span class="mdc-button__ripple"></span>
                            <span class="mdc-button__label"><?php esc_html_e('Add Media Field', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
                        </button>
                    </div>

                    <!-- Toggle Switches Section -->
                    <div class="feed-config-section">
                        <h4 class="feed-edit-section-header"><?php esc_html_e('Toggle Switches', WORDPLUG_CF_TEXT_DOMAIN); ?></h4>
                        <div id="toggle-switches-list" aria-label="<?php esc_attr_e('Toggle Switches List', WORDPLUG_CF_TEXT_DOMAIN); ?>">
                            <?php if (! empty($feed_config['toggles'])) : ?>
                                <?php foreach ($feed_config['toggles'] as $index => $field) : ?>
                                    <?php $is_on = isset($field['value']) && $field['value'] === 'on'; ?>
                                    <div class="feed-config-item" data-index="<?php echo esc_attr($index); ?>">
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                            <label class="mdc-text-field mdc-text-field--outlined mdc-text-field--dense" data-mdc-auto-init="MDCTextField" style="flex-grow: 1;">
                                                <span class="mdc-notched-outline mdc-notched-outline--upgraded <?php echo ! empty($field['key']) ? 'mdc-notched-outline--notched' : ''; ?>">
                                                    <span class="mdc-notched-outline__leading"></span>
                                                    <span class="mdc-notched-outline__notch">
                                                        <span class="mdc-floating-label <?php echo ! empty($field['key']) ? 'mdc-floating-label--float-above' : ''; ?>">Toggle Key</span>
                                                    </span>
                                                    <span class="mdc-notched-outline__trailing"></span>
                                                </span>
                                                <input type="text" class="mdc-text-field__input" name="feed_config[toggles][<?php echo esc_attr($index); ?>][key]" value="<?php echo esc_attr($field['key']); ?>" required aria-label="<?php esc_attr_e('Toggle Key', WORDPLUG_CF_TEXT_DOMAIN); ?>">
                                            </label>
                                            <div class="mdc-form-field" style="flex-grow: 0;">
                                                <div class="mdc-switch <?php echo $is_on ? 'mdc-switch--selected' : 'mdc-switch--unselected'; ?>" data-mdc-auto-init="MDCSwitch" aria-label="<?php esc_attr_e('Toggle Switch', WORDPLUG_CF_TEXT_DOMAIN); ?>">
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
<!-- Accessible label for switch -->
                                            </div>
                                        </div>
                                        <button type="button" class="remove-feed-item-button">
                                            <span><?php esc_html_e('Remove', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p class="no-items-placeholder"><?php esc_html_e('No toggle switches added yet.', WORDPLUG_CF_TEXT_DOMAIN); ?></p>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="mdc-button mdc-button--outlined" id="add-toggle-switch-button" aria-label="<?php esc_attr_e('Add Toggle Switch', WORDPLUG_CF_TEXT_DOMAIN); ?>">
                            <span class="mdc-button__ripple"></span>
                            <span class="mdc-button__label"><?php esc_html_e('Add Toggle Switch', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
                        </button>
                    </div>

                </div> <!-- #feed-config-container -->
            </div> <!-- .mdc-card__content -->
        </div> <!-- .mdc-card -->

        <!-- Save/Cancel Buttons -->
        <div style="display: flex; gap: 20px; justify-content: flex-end; margin-top: 30px;">
            <button type="submit" class="mdc-button mdc-button--raised" aria-label="<?php echo esc_attr($is_editing ? __('Update Feed', WORDPLUG_CF_TEXT_DOMAIN) : __('Create Feed', WORDPLUG_CF_TEXT_DOMAIN)); ?>">
                <span class="mdc-button__ripple"></span>
                <span class="mdc-button__label"><?php echo esc_html($is_editing ? __('Update Feed', WORDPLUG_CF_TEXT_DOMAIN) : __('Create Feed', WORDPLUG_CF_TEXT_DOMAIN)); ?></span>
            </button>
            <a href="<?php echo esc_url(admin_url('admin.php?page=wordplug-custom-feeds')); ?>" class="mdc-button mdc-button--outlined" aria-label="<?php esc_attr_e('Cancel', WORDPLUG_CF_TEXT_DOMAIN); ?>">
                <span class="mdc-button__ripple"></span>
                <span class="mdc-button__label"><?php esc_html_e('Cancel', WORDPLUG_CF_TEXT_DOMAIN); ?></span>
            </a>
        </div>

        <?php submit_button($is_editing ? __('Update Feed', WORDPLUG_CF_TEXT_DOMAIN) : __('Create Feed', WORDPLUG_CF_TEXT_DOMAIN)); ?>
    </form>

<!-- JS: Improve label float, toggle accessibility, and prevent duplicate interface -->
<script>
// Float label for Feed Title if pre-filled (already handled above)
// Toggle switch accessibility and state sync
function updateHiddenToggleValue(switchEl, hiddenInput) {
    if (switchEl.classList.contains('mdc-switch--selected')) {
        hiddenInput.value = 'on';
        switchEl.setAttribute('aria-checked', 'true');
    } else {
        hiddenInput.value = 'off';
        switchEl.setAttribute('aria-checked', 'false');
    }
}
document.addEventListener('DOMContentLoaded', function() {
    // Toggle switch click handler
    document.querySelectorAll('.mdc-switch').forEach(function(switchEl) {
        var hiddenInput = switchEl.closest('.feed-config-item')?.querySelector('.toggle-value');
        if (!hiddenInput) return;
        switchEl.addEventListener('click', function(e) {
            switchEl.classList.toggle('mdc-switch--selected');
            switchEl.classList.toggle('mdc-switch--unselected');
            updateHiddenToggleValue(switchEl, hiddenInput);
        });
        // Keyboard accessibility
        switchEl.addEventListener('keydown', function(e) {
            if (e.key === ' ' || e.key === 'Enter') {
                e.preventDefault();
                switchEl.click();
            }
        });
    });
    // Remove button color accessibility
    document.querySelectorAll('.remove-feed-item-button').forEach(function(btn) {
        btn.addEventListener('focus', function() { btn.style.outline = '2px solid #b00020'; });
        btn.addEventListener('blur', function() { btn.style.outline = ''; });
    });

});
</script>