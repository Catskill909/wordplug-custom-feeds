<?php
/*
Plugin Name: WP Admin JS Test
Description: Minimal test plugin to verify admin JS enqueuing.
Version: 1.0
Author: Codeium Cascade
*/

add_action('admin_menu', function() {
    add_menu_page('JS Test', 'JS Test', 'manage_options', 'js-test', function() {
        echo '<div class="wrap"><h1>JS Test Page</h1><p>If you see an alert, JS is working!</p></div>';
    });
});

add_action('admin_enqueue_scripts', function($hook) {
    // Only load JS on our test page
    if (isset($_GET['page']) && $_GET['page'] === 'js-test') {
        wp_enqueue_script('admin-test-js', plugin_dir_url(__FILE__) . 'admin-test.js', array(), null, true);
    }
});
