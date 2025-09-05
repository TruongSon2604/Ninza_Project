<?php
if (!defined('ABSPATH'))
    exit;

add_action('woocommerce_admin_order_item_headers', function ($order) {
    echo '<th class="item-regular-price sortable" width="1%" style="text-align:center;" data-sort="float">' . __('Original Price', 'woocommerce') . '</th>';
}, 20);

add_action('woocommerce_admin_order_item_values', function ($product, $item, $item_id) {
    if (is_a($product, 'WC_Product')) {
        $regular_price = $product->get_regular_price();
        if ($regular_price) {
            echo '<td class="item-regular-price" style="text-align:center;">' . wc_price($regular_price) . '</td>';
        } else {
            echo '<td class="item-regular-price" style="text-align:center;"></td>';
        }
    }
}, 20, 3);



add_action('admin_footer', function () {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'woocommerce_page_wc-orders'): ?>
        <script>
            jQuery(function ($) {
                function renameColumn() {
                    let th = $('table.woocommerce_order_items th.item_cost');
                    let License = $('table.woocommerce_order_items th.quantity');
                    if (th.length) {
                        th.text('Sales Price');
                        License.text('License');
                    }
                }
                function colorizeTotal() {
                    $('table.woocommerce_order_items td.line_cost .woocommerce-Price-amount')
                        .css({
                            'color': 'blue',
                            'font-weight': 'bold'
                        });
                }
                renameColumn();
                colorizeTotal()
                $(document.body).on('updated_order_items', function () {
                    renameColumn();
                    colorizeTotal()
                });
            });
        </script>
    <?php endif;
});

add_action('admin_footer', function () {
    $screen = get_current_screen();
    if ($screen && in_array($screen->id, ['shop_order', 'woocommerce_page_wc-orders'])): ?>
        <style>
            table.wc-order-totals .woocommerce-Price-amount {
                color: blue;
                font-weight: bold;
            }
        </style>
    <?php endif;
});


add_action('admin_footer', function () {
    $screen = get_current_screen();
    if ($screen && in_array($screen->id, ['shop_order', 'woocommerce_page_wc-orders'])): ?>
        <script>
            jQuery(function ($) {
                // Coupon
                let couponRow = $("table.wc-order-totals td.label:contains('Coupon')").closest("tr");
                let couponTotalTd = couponRow.find("td.total");
                couponTotalTd.css({
                    "color": "red",
                    "font-weight": "bold"
                });

                couponTotalTd.find('.woocommerce-Price-amount').css({
                    "color": "red",
                    "font-weight": "bold"
                });

                // Discount (Sale)
                let discountRow = $("table.wc-order-totals td.label:contains('Discount')").closest("tr");
                let discountTotalTd = discountRow.find("td.total");
                discountTotalTd.css({
                    "color": "red",
                    "font-weight": "bold"
                });
                discountTotalTd.find('.woocommerce-Price-amount').css({
                    "color": "red",
                    "font-weight": "bold"
                });
            });
        </script>
    <?php endif;
});


add_action('woocommerce_admin_order_totals_after_discount', function ($order_id) {
    $order = wc_get_order($order_id);
    if (!$order)
        return;

    $discount_total = 0;

    // Tính discount dựa trên product regular - sale
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if ($product) {
            $regular_price = (float) $product->get_regular_price();
            $sale_price = (float) $product->get_sale_price();
            $qty = $item->get_quantity();

            if ($regular_price > 0 && $sale_price > 0 && $regular_price > $sale_price) {
                $discount_total += ($regular_price - $sale_price) * $qty;
            }
        }
    }

    if ($discount_total > 0): ?>
        <tr class="sale-discount-row">
            <td class="label">Discount (Sale):</td>
            <td width="1%"></td>
            <td class="total">
                <span style="color:red; font-weight:bold;">
                    -<?php echo wc_price($discount_total); ?>
                </span>
            </td>
        </tr>
        <script>
            window.__saleDiscountTotal = <?php echo (float) $discount_total; ?>;
        </script>
    <?php endif;
});

add_action('admin_footer', function () {
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->id, ['shop_order', 'woocommerce_page_wc-orders'], true)) {
        return;
    }
    ?>
    <script>
        jQuery(function ($) {
            function parseMoney(txt) {
                if (!txt) return 0;
                txt = txt.toString().replace(/[^\d,.\-]/g, '');
                const onlyDigits = txt.replace(/[^\d\-]/g, '');
                return parseFloat(onlyDigits) || 0;
            }

            function computeAndRender() {
                const table = $('table.wc-order-totals');
                if (!table.length) return;

                const subRow = table.find("td.label:contains('Items Subtotal'), td.label:contains('Subtotal'), td.label:contains('Tổng mặt hàng')").closest('tr');
                const totalRow = table.find("td.label:contains('Order total'), td.label:contains('Order Total'), td.label:contains('Tổng đơn hàng')").closest('tr');

                if (!subRow.length || !totalRow.length) return;

                const subtotalText = subRow.find('td.total').text().trim();
                const totalText = totalRow.find('td.total').text().trim();

                const subtotalVal = parseMoney(subtotalText);
                const totalVal = parseMoney(totalText);

                const saleDiscount = window.__saleDiscountTotal || 0;

                let percent = (subtotalVal > 0 ? Math.round(((subtotalVal - totalVal + saleDiscount) / subtotalVal) * 100) : 0);

                const amountSpan = totalRow.find('td.total .woocommerce-Price-amount');
                if (percent > 0 && totalRow.find('.order-percent-off').length === 0) {
                    amountSpan.append('<span class="order-percent-off" style="color:#000; font-size:12px;"> (' + percent + '% off)</span>');
                }
            }


            computeAndRender();

            const observer = new MutationObserver(() => {
                if ($('table.wc-order-totals').length && $('.order-percent-off').length === 0) {
                    computeAndRender();
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        });
    </script>
    <?php
});