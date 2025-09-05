<?php
/**
 * Plugin Name: WooCommerce Order Tags
 * Description:
 * Author: Son
 * Version: 1.1
 */

add_action('init', function () {
    register_taxonomy(
        'shop_order_tag',
        'shop_order',
        array(
            'label' => __('Order Tags', 'order-tags'),
            'public' => false,
            'show_ui' => false,
            'hierarchical' => false,
            'show_in_quick_edit' => true,
            'capabilities' => array(
                'manage_terms' => 'manage_woocommerce',
                'edit_terms' => 'manage_woocommerce',
                'delete_terms' => 'manage_woocommerce',
                'assign_terms' => 'edit_shop_orders',
            ),
        )
    );
    $existing_terms = get_terms([
        'taxonomy' => 'shop_order_tag',
        'hide_empty' => false,
    ]);

    $default_tags = ['Exchange', 'Upgrade', 'Locked', 'Archived', 'Manual', 'Auto', 'Test'];

    foreach ($existing_terms as $term) {
        if (!in_array($term->name, $default_tags, true)) {
            wp_delete_term($term->term_id, 'shop_order_tag');
        }
    }

    foreach ($default_tags as $tag) {
        if (!term_exists($tag, 'shop_order_tag')) {
            wp_insert_term($tag, 'shop_order_tag');
        }
    }
});

add_filter('pre_insert_term', function ($term, $taxonomy) {
    if ($taxonomy === 'shop_order_tag' && !is_super_admin()) {
        return new WP_Error('permission_denied', __('Only super admin can create new order tags.', 'order-tags'));
    }
    return $term;
}, 10, 2);

add_action('woocommerce_process_shop_order_meta', 'track_order_tags_changes_debug', 5, 2);
function track_order_tags_changes_debug($order_id, $post)
{
    $order = wc_get_order($order_id);
    if (!$order) {
        error_log("[AutoNote] Not a valid order for ID {$order_id}");
        return;
    }

    $old_tags = wp_get_object_terms($order_id, 'shop_order_tag', array('fields' => 'ids'));
    error_log("[AutoNote] 🔹 Old tag IDs: " . json_encode($old_tags));

    $new_tags = isset($_POST['shop_order_tags']) ? array_map('intval', (array) $_POST['shop_order_tags']) : array();
    error_log("[AutoNote] 🔹 New tag IDs (POST): " . json_encode($new_tags));

    $removed = array_diff($old_tags, $new_tags);
    $added = array_diff($new_tags, $old_tags);

    error_log("[AutoNote]  Added tag IDs: " . json_encode($added));
    error_log("[AutoNote]  Removed tag IDs: " . json_encode($removed));

    $admin_user = wp_get_current_user()->display_name;

    if (!empty($added)) {
        foreach ($added as $term_id) {
            $term = get_term($term_id);
            if ($term && !is_wp_error($term)) {
                $note = "🔖New Auto Note: Admin {$admin_user} added order tag '{$term->name}'.";
                $order->add_order_note($note, false, true);
                error_log("[AutoNote] ✅ $note");
            }
        }
    }

    if (!empty($removed)) {
        foreach ($removed as $term_id) {
            $term = get_term($term_id);
            if ($term && !is_wp_error($term)) {
                $note = "🔖Remove Auto Note: Admin {$admin_user} removed order tag '{$term->name}'.";
                $order->add_order_note($note, false, true);
                error_log("[AutoNote] ✅ $note");
            }
        }
    }

    if (empty($added) && empty($removed)) {
        error_log("[AutoNote] no changes in tags for order {$order_id}");
    }
}


add_action('woocommerce_admin_order_data_after_order_details', function ($order) {
    $terms = get_terms(array(
        'taxonomy' => 'shop_order_tag',
        'hide_empty' => false,
    ));
    $options = [];
    foreach ($terms as $term) {
        $options[$term->term_id] = $term->name;
    }

    $selected = wp_get_post_terms($order->get_id(), 'shop_order_tag', array('fields' => 'ids'));

    woocommerce_wp_select(array(
        'id' => 'shop_order_tags[]',
        'label' => __('Order Tags', 'order-tags'),
        'options' => $options,
        'value' => $selected,
        'class' => 'wc-enhanced-select',
        'custom_attributes' => array(
            'multiple' => 'multiple',
            'style' => 'width:100%;'
        ),
    ));
});

add_action('woocommerce_process_shop_order_meta', function ($order_id) {
    if (!empty($_POST['shop_order_tags'])) {
        $tags = array_map('intval', (array) $_POST['shop_order_tags']);
        wp_set_post_terms($order_id, $tags, 'shop_order_tag');
    } else {
        wp_set_post_terms($order_id, [], 'shop_order_tag');
    }
});




add_filter('woocommerce_get_checkout_payment_url', function ($pay_url, $order) {
    // Tạo link rút gọn theo ý muốn
    $short_url = add_query_arg([
        'order' => $order->get_id(),
    ], wc_get_checkout_url());
    return $short_url;
}, 10, 2);

