<?php



// coupon modal 

add_action('admin_notices', function () {
    $screen = get_current_screen();
    if (!$screen)
        return;

    echo '<div class="notice notice-info is-dismissible">';
    echo '<pre>';
    echo 'Screen ID: ' . esc_html($screen->id) . "\n";
    echo 'Post Type: ' . esc_html($screen->post_type);
    echo '</pre>';
    echo '</div>';
});

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], null, true);
});


add_action('rest_api_init', function () {
    register_rest_route('my-api/v1', '/coupons', [
        'methods' => 'GET',
        'callback' => function () {
            $args = [
                'posts_per_page' => -1,
                'post_type' => 'shop_coupon',
                'post_status' => 'publish',
            ];

            $query = new WP_Query($args);
            $list = [];

            foreach ($query->posts as $post) {
                $coupon = new WC_Coupon($post->post_name);
                $list[] = [
                    'id' => $coupon->get_id(),
                    'code' => $coupon->get_code(),
                    'amount' => wc_price($coupon->get_amount()),
                    'amount_raw' => $coupon->get_amount(),
                    'discount_type' => $coupon->get_discount_type(),
                    'description' => get_post_field('post_excerpt', $post),
                    'date_expires' => $coupon->get_date_expires() ? $coupon->get_date_expires()->date('Y-m-d H:i:s') : null,
                    'usage_limit' => $coupon->get_usage_limit(),
                    'usage_count' => $coupon->get_usage_count(),
                    'limit_usage_to_x_items' => $coupon->get_limit_usage_to_x_items(),
                    'free_shipping' => $coupon->get_free_shipping(),
                    'product_ids' => $coupon->get_product_ids(),
                    'exclude_product_ids' => $coupon->get_excluded_product_ids(),
                    'product_categories' => $coupon->get_product_categories(),
                    'exclude_categories' => $coupon->get_excluded_product_categories(),
                    'minimum_amount' => $coupon->get_minimum_amount(),
                    'maximum_amount' => $coupon->get_maximum_amount(),
                ];
            }

            return $list;
        },
        'permission_callback' => '__return_true'
    ]);
});



add_action('admin_enqueue_scripts', function ($hook) {
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'shop_order') {
        // wp_enqueue_script(
        // 	'my-custom-admin-order',
        // 	get_template_directory_uri() . '/js/custom-admin-order.js',
        // 	['jquery'],
        // 	'1.0',
        // 	true
        // );
        wp_enqueue_script(
            'my-custom-admin-order',
            get_template_directory_uri() . '/js/custom-admin-order.js',
            ['jquery', 'wc-admin-order-meta-boxes'], // load sau WooCommerce
            '1.0',
            true
        );
    }
});