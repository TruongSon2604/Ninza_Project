<?php
if (!defined('ABSPATH'))
    exit;

add_action('admin_enqueue_scripts', function () {
    $screen = get_current_screen();
    echo "📦 admin_enqueue_scripts for screen: " . ($screen ? $screen->id : 'unknown') . "\n";
    if (
        $screen &&
        $screen->id === 'woocommerce_page_wc-orders'
    ) {
        wp_enqueue_script(
            'woo-ninza-admin-add-original-sale-product',
            plugin_dir_url(__DIR__) . '../assets/add-original-sale-product.js',
            [],
            time(),
            true
        );
    }
});

add_action('rest_api_init', function () {
    register_rest_route('my-api/v1', '/product-price/(?P<id>\d+)', [
        'methods'  => 'GET',
        'callback' => function ($request) {
            $product_id = absint($request['id']);
            $product    = wc_get_product($product_id);
            if (!$product) {
                return new WP_Error('not_found', 'Product not found', ['status' => 404]);
            }
            return [
                'id'            => $product->get_id(),
                'name'          => $product->get_name(),
                'regular_price' => wc_get_price_to_display($product, ['price' => $product->get_regular_price()]),
                'sale_price'    => $product->get_sale_price() ? wc_get_price_to_display($product, ['price' => $product->get_sale_price()]) : null,
                'formatted_regular' => wc_price($product->get_regular_price()),
                'formatted_sale'    => $product->get_sale_price() ? wc_price($product->get_sale_price()) : null,
            ];
        },
        'permission_callback' => '__return_true',
    ]);
});
