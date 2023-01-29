$(document).ready(function () {
  const owl = $(".owl-carousel");
  owl.owlCarousel({
    items: 1,
    autoplay: true,
    autoPlaySpeed: 500,
    autoPlayTimeout: 500,
    autoplayHoverPause: true,
    loop: true,
  });
});
// Copy Page Url
function fallbackCopyTextToClipboard() {
  var textArea = document.createElement("textarea");
  textArea.value = window.location.href;

  // Avoid scrolling to bottom
  textArea.style.top = "0";
  textArea.style.left = "0";
  textArea.style.position = "fixed";

  document.body.appendChild(textArea);
  textArea.focus();
  textArea.select();

  try {
    var successful = document.execCommand("copy");
    var msg = successful ? "successful" : "unsuccessful";
    console.log("Fallback: Copying text command was " + msg);
  } catch (err) {
    console.error("Fallback: Oops, unable to copy", err);
  }

  document.body.removeChild(textArea);
}
function copyPageLink() {
  let text = window.location.href;
  if (!navigator.clipboard) {
    fallbackCopyTextToClipboard(text);
    return;
  }
  navigator.clipboard.writeText(text).then(
    function () {
      console.log("Async: Copying to clipboard was successful!");
    },
    function (err) {
      console.error("Async: Could not copy text: ", err);
    }
  );
}

// Get the quantity input field and the increment and decrement buttons
const quantityInput = document.querySelector("#quantity");
const total = document.querySelector("#total");
const price = document.querySelector("#price");

// Function to decrement the quantity by 1
function decrementQuantity() {
  let quantityValue = parseInt(quantityInput.value);
  if (quantityValue > 1) {
    quantityValue--;
    quantityInput.value = quantityValue;
  }
  if (total) {
    total.innerHTML = "$" + quantityValue * parseInt(price);
  }
}

// Function to increment the quantity by 1
function incrementQuantity() {
  let quantityValue = parseInt(quantityInput.value);
  quantityValue++;
  quantityInput.value = quantityValue;
  if (total) {
    total.innerHTML = "$" + quantityValue * parseInt(price);
  }
}

$(".minus-btn").on("click", decrementQuantity);
$(".plus-btn").on("click", incrementQuantity);

$(".cart-plus-btn").on("click", function () {
  var input = $(this).siblings("input[name=quantity]");
  var currentValue = parseInt(input.val());
  input.val(currentValue + 1);
  updateSubtotal($(this));
});

$(".cart-minus-btn").on("click", function () {
  var input = $(this).siblings("input[name=quantity]");
  var currentValue = parseInt(input.val());
  if (currentValue > 1) {
    input.val(currentValue - 1);
    updateSubtotal($(this));
  }
});

// Update Subtotal
function updateSubtotal(btn) {
  let _total = $(btn).parent().parent("tr").find(".total");
  let _price = $(btn).parent().parent("tr").find(".price").val();
  let _quantity = $(btn).parent().parent("tr").find(".quantity").val();
  let _fulltotal = 0;
  _total.html("$" + _price * _quantity);

  $("table")
    .find(".total")
    .each(function () {
      _p = $(this).html();
      _fulltotal += Number(_p.replace(/[^0-9.-]+/g, ""));
    });
  $("#fulltotal").html("Total : $" + _fulltotal);
}
