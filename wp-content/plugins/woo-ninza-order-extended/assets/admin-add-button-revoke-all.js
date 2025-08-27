jQuery(document).ready(function ($) {
  $("button.grant_access").each(function () {
    if (!$(this).next(".revoke_all_access").length) {
      const revokeBtn = $(
        '<button type="button" class="revoke_all_access button">Revoke All</button>'
      );
      const regenerateBtn = $(
        '<button type="button" class="regenerate_all_downloadables button">Regenerate all Downloadables</button>'
      );
      revokeBtn.css({
        marginLeft: "8px",
      });
      regenerateBtn.css({
        marginLeft: "8px",
      });
      $(this).after(revokeBtn);
      $("button.revoke_all_access").after(regenerateBtn);

      regenerateBtn.on("click", function (e) {
        e.preventDefault();

        if (
          !window.confirm(
            "Are you sure you want to extend all downloads by 1 year?"
          )
        ) {
          return;
        }

        var data = {
          action: "my_regenerate_all_downloadables",
          order_id: woocommerce_admin_meta_boxes.post_id,
          security: my_admin_order.nonce,
        };
        console.log("Data to send:", data);

        $.post(my_admin_order.ajax_url, data, function (response) {
          console.log(" Response from server aa:", response);
          if (response.success) {
            // alert(response.data);
            alert("All downloads have been extended by 1 year.");
            // location.reload();
          } else {
            alert("Error: " + response.data);
          }
        });
      });
    }
  });
});
