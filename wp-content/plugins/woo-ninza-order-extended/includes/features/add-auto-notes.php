<?php
if (!defined('ABSPATH'))
    exit;

// add_action('admin_enqueue_scripts', function () {
//     $screen = get_current_screen();

//     if (
//         $screen &&
//         $screen->id === 'woocommerce_page_wc-orders'
//     ) {
//         wp_enqueue_script(
//             'woo-ninza-admin-license',
//             plugin_dir_url(__DIR__) . '../assets/add-auto-notes.js',
//             [],
//             time(),
//             true
//         );

//     }
// });
add_action('admin_enqueue_scripts', function () {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'woocommerce_page_wc-orders') {
        wp_enqueue_script(
            'my-order-notes-panel',
            get_stylesheet_directory_uri() . '/js/my-order-notes-panel.js',
            [ 'wc-admin-app' ], // rất quan trọng
            '1.0',
            true
        );
    }
});