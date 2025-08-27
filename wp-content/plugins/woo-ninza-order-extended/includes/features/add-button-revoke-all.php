<?php
if (!defined('ABSPATH'))
    exit;

add_action('admin_enqueue_scripts', function () {
    $screen = get_current_screen();

    if (
        $screen &&
        $screen->id === 'woocommerce_page_wc-orders'
    ) {
        wp_enqueue_script(
            'woo-ninza-admin-license',
            plugin_dir_url(__DIR__) . '../assets/admin-add-button-revoke-all.js',
            [],
            time(),
            true
        );

        wp_localize_script('woo-ninza-admin-license', 'my_admin_order', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('regenerate-downloads'),
        ]);
    }
});


add_action('wp_ajax_my_regenerate_all_downloadables', 'myshop_regenerate_all_downloadables');

function myshop_regenerate_all_downloadables()
{
    check_ajax_referer('regenerate-downloads', 'security');

    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    if (!$order_id) {
        wp_send_json_error("Invalid order_id");
    }

    $order = wc_get_order($order_id);
    if (!$order) {
        wp_send_json_error("Order not found");
    }

    global $wpdb;
    $results = [];

    foreach ($order->get_items('line_item') as $item_id => $item) {
        /** @var WC_Order_Item_Product $item */
        $product = $item->get_product();

        if ($product && $product->is_downloadable()) {
            $downloads = $item->get_item_downloads();

            foreach ($downloads as $download_id => $file) {
                $update_data = [
                    'access_expires' => date('Y-m-d H:i:s', strtotime('+1 year')), // ✅ cột đúng
                ];
                $where = [
                    'order_id' => $order_id,
                    'product_id' => $product->get_id(),
                    'download_id' => $download_id,
                ];

                $result = $wpdb->update(
                    "{$wpdb->prefix}woocommerce_downloadable_product_permissions",
                    $update_data,
                    $where
                );

                error_log("🔄 Update result: " . var_export($result, true));
                error_log("📝 Last query: " . $wpdb->last_query);
                error_log("⚠️ Last error: " . $wpdb->last_error);

                $results[] = [
                    'product_id' => $product->get_id(),
                    'download_id' => $download_id,
                    'result' => $result,
                ];
            }
        }
    }

    wp_send_json_success([
        'order_id' => $order->get_id(),
        'status' => $order->get_status(),
        'total' => $order->get_total(),
        'updates' => $results,
    ]);
}


// Thêm meta box vào trang edit order
add_action('add_meta_boxes', function () {
    add_meta_box(
        'my_custom_order_notes',             // ID
        __('Custom Order Notes', 'myshop'), // Title
        'render_my_order_notes_box',        // Callback
        'shop_order',                       // Screen
        'side',                             // Context (side, normal, advanced)
        'default'                           // Priority
    );
});

function render_my_order_notes_box($post)
{
    $notes = wc_get_order_notes(array(
        'order_id' => $post->ID,
        'order_by' => 'date_created',
        'order' => 'DESC',
    ));

    ?>
    <ul class="order_notes">
        <?php if ($notes): ?>
            <?php foreach ($notes as $note): ?>
                <li rel="<?php echo absint($note->id); ?>" class="note">
                    <div class="note_content">
                        <?php echo wpautop(wp_kses_post($note->content)); ?>
                    </div>
                    <p class="meta">
                        <abbr title="<?php echo esc_attr($note->date_created->date('Y-m-d H:i:s')); ?>">
                            <?php echo esc_html($note->date_created->date_i18n(wc_date_format() . ' ' . wc_time_format())); ?>
                        </abbr>
                        <?php if ($note->added_by): ?>
                            <?php echo esc_html(sprintf(__('by %s', 'woocommerce'), $note->added_by)); ?>
                        <?php endif; ?>
                    </p>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="no-items"><?php esc_html_e('There are no notes yet.', 'woocommerce'); ?></li>
        <?php endif; ?>
    </ul>
    <?php
}