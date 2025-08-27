<?php
if (!defined('ABSPATH'))
    exit;

add_action('admin_enqueue_scripts', function () {
    $screen = get_current_screen();

    // echo "📦 admin_enqueue_scripts for screen:custom-input-quantity ----- " . ($screen ? $screen->id : 'unknown') . "\n";
    error_log("📦 admin_enqueue_scripts");
    if (
        $screen &&
        $screen->id === 'woocommerce_page_wc-orders'
    ) {

        wp_enqueue_script(
            'woo-ninza-admin-input-quantity',
            plugin_dir_url(__DIR__) . '../assets/custom-input-quantity.js',
            [],
            time(),
            true
        );
    }
});