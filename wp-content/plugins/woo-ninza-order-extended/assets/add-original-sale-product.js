
jQuery(document).ready(function ($) {
  $(document).on("select2:select", ".wc-product-search", function (e) {
    let productId = e.params.data.id;
    let row = $(this).closest("tr");

    fetch(`/wp-json/my-api/v1/product-price/${productId}`)
      .then((res) => res.json())
      .then((data) => {
        let $orig = row.find(".wc-product-search-original");
        let $sale = row.find(".wc-product-search-sales");

        if (data.formatted_regular) {
          $orig.html(data.formatted_regular);
        } else {
          $orig.text("");
        }

        if (data.formatted_sale) {
          $sale.html(data.formatted_sale);
        } else {
          $sale.text("");
        }
      })
      .catch((err) => console.error(err));
  });
});
