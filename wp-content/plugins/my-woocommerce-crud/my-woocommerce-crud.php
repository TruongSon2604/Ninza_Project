<?php
/**
 * Plugin Name: My WooCommerce CRUD
 * Description: Tạo/xem/sửa/xoá sản phẩm WooCommerce .
 * Version: 1.1
 * Author: Son
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('plugins_loaded', function () {
    if (!class_exists('WooCommerce'))
        return;

    $product_name = 'Áo thun code thủ new cua toi v3';
    $product_sku = 'DEV003';


    add_action('init', function () use ($product_name, $product_sku) {
        if (!wc_get_product_id_by_sku($product_sku)) {
            $product = new WC_Product_Simple();
            $product->set_name($product_name);
            $product->set_regular_price(199000);
            $product->set_description('Áo thun đậm chất lập trình viên');
            $product->set_sku($product_sku);
            $product->set_manage_stock(true);
            $product->set_stock_quantity(20);
            $product->set_status('publish');
            $product->save();
        }
    });

  
    add_shortcode('my_products_list', function () {
        $args = ['post_type' => 'product', 'posts_per_page' => 5];
        $loop = new WP_Query($args);
        $html = '<ul>';
        while ($loop->have_posts()) {
            $loop->the_post();
            global $product;
            $html .= '<li>' . get_the_title() . ' - ' . $product->get_price_html() . '</li>';
        }
        wp_reset_postdata();
        $html .= '</ul>';
        return $html;
    });

  
    add_action('init', function () use ($product_sku) {
        $product_id = wc_get_product_id_by_sku($product_sku);
        if ($product_id) {
            $product = wc_get_product($product_id);
            if ($product && $product->get_regular_price() !== '149000') {
                $product->set_regular_price(149000);
                $product->save();
            }
        }
    });

    add_action('init', function () {
        if (isset($_GET['delete_product_by_sku'])) {
            $sku = sanitize_text_field($_GET['delete_product_by_sku']);
            $product_id = wc_get_product_id_by_sku($sku);
            if ($product_id) {
                wp_delete_post($product_id, true);
                wp_die("Đã xoá sản phẩm có SKU: $sku (ID: $product_id)");
            } else {
                wp_die("Không tìm thấy sản phẩm với SKU: $sku");
            }
        }
    });


});
