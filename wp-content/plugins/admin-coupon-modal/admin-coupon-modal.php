<?php
/**
 * Plugin Name: Admin Coupon Modal for WooCommerce
 * Description: Hiển thị modal chọn coupon khi click Apply Coupon trong trang admin order.
 * Version: 1.0
 * Author: Bạn
 */

if (!defined('ABSPATH')) exit;

// Load JS/CSS
add_action('admin_enqueue_scripts', function($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'])) return;
    if (get_post_type() !== 'shop_order') return;

    wp_enqueue_script('my-admin-coupon-modal', plugin_dir_url(__FILE__) . 'admin-coupon-modal.js', ['jquery'], '1.0', true);
    wp_localize_script('my-admin-coupon-modal', 'MyCouponData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('my_get_coupons')
    ]);

    wp_enqueue_style('my-admin-coupon-modal-css', plugin_dir_url(__FILE__) . 'admin-coupon-modal.css');
});

// AJAX lấy coupon
add_action('wp_ajax_my_get_coupons', function() {
    check_ajax_referer('my_get_coupons', 'nonce');

    $coupons = get_posts([
        'post_type'   => 'shop_coupon',
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);

    $data = [];
    foreach ($coupons as $coupon) {
        $obj = new WC_Coupon($coupon->ID);
        $data[] = [
            'code'   => $obj->get_code(),
            'amount' => $obj->get_amount(),
            'type'   => $obj->get_discount_type(),
            'expiry' => $obj->get_date_expires() ? $obj->get_date_expires()->date('Y-m-d') : null,
            'desc'   => get_post_meta($coupon->ID, 'description', true),
        ];
    }

    wp_send_json_success($data);
});

// HTML modal
add_action('admin_footer', function() {
    $screen = get_current_screen();
    if ($screen->post_type !== 'shop_order') return;
    ?>
    <div id="myCouponModal" class="my-modal" style="display:none;">
        <div class="my-modal-content">
            <span class="my-modal-close" style="cursor:pointer;">&times;</span>
            <h2>Chọn Coupon</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Giảm</th>
                        <th>Hết hạn</th>
                        <th>Mô tả</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <?php
});
