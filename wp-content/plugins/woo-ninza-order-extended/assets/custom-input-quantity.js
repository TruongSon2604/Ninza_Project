function addQtyButtons() {
  // console.log("Adding quantity buttons to inputs");
  // alert("Adding quantity buttons to inputs");
  const inputs = document.querySelectorAll("#order_line_items input.quantity");
  document.querySelectorAll("input.quantity").forEach(function (input) {
    if (input.parentNode.classList.contains("qty-wrapper")) return;

    const wrapper = document.createElement("div");
    wrapper.classList.add("qty-wrapper");
    wrapper.style.display = "flex";
    wrapper.style.alignItems = "center";
    wrapper.style.gap = "4px";
    wrapper.style.justifyContent = "center";

    const btnMinus = document.createElement("button");
    btnMinus.type = "button";
    btnMinus.textContent = "−";
    btnMinus.style.padding = "2px 6px";
    btnMinus.style.cursor = "pointer";
    btnMinus.addEventListener("click", function () {
      let val = parseInt(input.value) || 0;
      if (val > 1) {
        input.value = val - 1;
        input.dispatchEvent(new Event("change", { bubbles: true }));
      }
    });

    const btnPlus = document.createElement("button");
    btnPlus.type = "button";
    btnPlus.textContent = "+";
    btnPlus.style.padding = "2px 6px";
    btnPlus.style.cursor = "pointer";
    btnPlus.addEventListener("click", function () {
      let val = parseInt(input.value) || 0;
      input.value = val + 1;
      input.dispatchEvent(new Event("change", { bubbles: true }));
    });

    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(btnMinus);
    wrapper.appendChild(input);
    wrapper.appendChild(btnPlus);

    input.style.width = "40px";
    input.style.padding = "2px 4px";
    input.style.fontSize = "13px";
    input.style.textAlign = "center";
  });
}

// jQuery(document).ready(function ($) {
//   setInterval(addQtyButtons, 100);
// });
jQuery(document).ready(function ($) {
  // alert("Custom input quantity script loaded!");
  var intervalId = setInterval(addQtyButtons, 100);
  $(window).on("beforeunload unload", function () {
    clearInterval(intervalId);
  });
});
