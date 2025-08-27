jQuery(function ($) {
  // Click vào Apply coupon
  $(document).off("click", ".wc-order-add-coupon"); // Gỡ sự kiện mặc định WooCommerce
  $(document).on("click", ".wc-order-add-coupon", function (e) {
    alert("Vui lòng sử dụng nút 'Chọn coupon' để thêm mã giảm giá.");
    e.preventDefault();
    e.stopPropagation();

    $.post(
      MyCouponData.ajax_url,
      { action: "my_get_coupons", nonce: MyCouponData.nonce },
      function (res) {
        if (res.success) {
          let list = res.data
            .map(
              (c) => `
                    <tr class="coupon-row" data-code="${c.code}">
                        <td>${c.code}</td>
                        <td>${c.amount} (${c.type})</td>
                        <td>${c.expiry ?? "Không giới hạn"}</td>
                        <td>${c.desc ?? ""}</td>
                    </tr>
                `
            )
            .join("");

          $("#myCouponModal tbody").html(list);
          $("#myCouponModal").fadeIn();
        }
      }
    );
  });

  // Chọn coupon
  $(document).on("click", ".coupon-row", function () {
    let code = $(this).data("code");
    $("input#coupon_code").val(code);
    $(".apply_coupon").trigger("click"); // WooCommerce apply
    $("#myCouponModal").fadeOut();
  });

  // Đóng modal
  $(document).on("click", ".my-modal-close", function () {
    $("#myCouponModal").fadeOut();
  });
});
