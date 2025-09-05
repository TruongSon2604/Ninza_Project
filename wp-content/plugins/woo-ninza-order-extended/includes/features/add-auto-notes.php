<?php
/**
 * Plugin Name: WooCommerce Auto Note (Simple)
 * Description: Add Auto Note box in page WooCommerce Order Edit.
 * Version: 1.1
 * Author: Son
 */

if (!defined('ABSPATH'))
    exit;

// Hook cho meta box
add_action('add_meta_boxes', 'autonote_add_meta_box');
function autonote_add_meta_box()
{

    add_meta_box(
        'order-autonote',
        __('Auto Note', 'mydomain'),
        'render_order_autonote_box',
        'shop_order',
        'side',
        'default'
    );

    add_meta_box(
        'order-autonote',
        __('Auto Note', 'mydomain'),
        'render_order_autonote_box',
        'woocommerce_page_wc-orders',
        'side',
        'default'
    );
}

// Render box
function render_order_autonote_box($post_or_order)
{
    if ($post_or_order instanceof WC_Order) {
        $order_id = $post_or_order->get_id();
    } else {
        $order_id = is_object($post_or_order) && isset($post_or_order->ID) ? $post_or_order->ID : get_the_ID();
    }

    error_log("[AutoNote] ✅ render_order_autonote_box chạy cho ID {$order_id}");
    ?>
    <div id="order-autonote-box">
        <input type="text" id="order_autonote_input_<?php echo $order_id; ?>" style="width:65%;" />
        <button type="button" class="button order_autonote_add" data-order="<?php echo $order_id; ?>">Add</button>
        <ul id="order_autonote_list_<?php echo $order_id; ?>"></ul>
    </div>

    <script type="text/javascript">
        jQuery(function ($) {

            $('.order_autonote_add').on('click', function () {
                const orderId = $(this).data('order');
                const note = $('#order_autonote_input_' + orderId).val();

                if (!note) {
                    alert("⚠️ Vui lòng nhập note");
                    return;
                }

                $.post(ajaxurl, {
                    action: 'add_order_autonote',
                    order_id: orderId,
                    note: note,
                    _ajax_nonce: '<?php echo wp_create_nonce("autonote_nonce"); ?>'
                }, function (response) {
                    if (response.success) {
                        $('#order_autonote_list_' + orderId).append('<li>' + response.data.note + '</li>');
                        $('#order_autonote_input_' + orderId).val('');
                        console.log("✅ Note added:", response.data.note);
                    } else {
                        alert("❌ Lỗi: " + response.data);
                    }
                });
            });
        });
    </script>
    <?php
}

// AJAX handler
add_action('wp_ajax_add_order_autonote', 'handle_add_order_autonote');
function handle_add_order_autonote()
{
    check_ajax_referer('autonote_nonce');

    $order_id = intval($_POST['order_id']);
    $note = sanitize_text_field($_POST['note']);

    if (!$order_id || empty($note)) {
        wp_send_json_error("Thiếu dữ liệu");
    }

    $order = wc_get_order($order_id);
    if (!$order) {
        wp_send_json_error("Không tìm thấy order");
    }

    $order->add_order_note($note, false);

    wp_send_json_success([
        'order_id' => $order_id,
        'note' => $note
    ]);
}
