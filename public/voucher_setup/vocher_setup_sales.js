//current stock qty and check
function current_stock(i, stock_item, godown_id, url, check_current_stock = 0) {
  let allowAllStock = $("#allowAllStock").val() || 0;
  $.ajax({
    url: url,
    method: "GET",
    dataType: "json",
    async: false,
    data: {
      godown_id: godown_id,
      stock_item_id: stock_item,
      allowAllStock: allowAllStock,
    },
    success: function (response) {
      if (response) {
        $("#stock_" + i).val(response?.data?.toFixed(3));
        if (check_current_stock == 0) {
          let mainQty = parseFloat($("#qty_" + i).attr("mainqty"));
          let stock = parseFloat($("#stock_" + i).val());
          let total = mainQty + stock;
          let qty = parseFloat($("#qty_" + i).val());
          if (total < qty) {
            $("#qty_" + i)
              .val(total)
              .trigger("keyup");
          }
        }
      } else {
        $("#stock_" + i).val(0);
      }
    },
  });
}

// select option change godown
$(".godown").on("change", function () {
  let godown_val = $(".godown").val();
  let godown = godown_val ? $(".godown option:selected").text() : "";

  let i = 2;
  $("#orders tr")
    .find(".godown_name")
    .each(function () {
      $(".godown_name").val(godown);
      $(".godown_id").val(godown_val);
      const url_get = $("#homeRoute").attr("href");
      let product_id = $(this).closest("tr").find(".product_id").val();
      let row_id = $(this).closest("tr").attr("id");
      let td_id = row_id.replace("row", "");
      if (product_id.length != 0) {
        current_stock(
          td_id,
          $(this).closest("tr").find(".product_id").val(),
          $(this).closest("tr").find(".godown_id").val(),
          `${url_get}/current-stock`,
          0
        );
      }
      i++;
    });
});

// select option change ledger
$(".ledger").on("change", function () {
  let ledger_id = $(".ledger").val();
  $("#orders tr")
    .find(".ledger_debit")
    .each(function (i) {
      $(".ledger_debit").val(ledger_id);
    });
});

// alert message
function swal_message(data, message, m_title) {
  swal({
    title: m_title,
    text: data,
    type: message,
    timer: "3000",
  });
}

//select auto change current qty and amount
function selected_auto_value_change(
  check_current_stock,
  currentEle,
  response,
  amount_decimals
) {
  let qty = 0;

  if (check_current_stock == 0) {
    let mainQty = currentEle.find(".qty").attr("mainQty") || 0;

    if (
      parseFloat(currentEle.closest("tr").find(".stock").val()) +
        parseFloat(mainQty) >=
      currentEle.closest("tr").find(".qty").val()
    ) {
      qty = currentEle.closest("tr").find(".qty").val();
    } else if (currentEle.closest("tr").find(".stock").val() >= 0) {
      currentEle
        .closest("tr")
        .find(".qty")
        .val(currentEle.closest("tr").find(".stock").val());
      qty = currentEle.closest("tr").find(".stock").val();
    } else {
      currentEle.closest("tr").find(".qty").val(0);
      qty = 0;
    }
  } else {
    qty = currentEle.closest("tr").find(".qty").val();
  }
  currentEle
    .closest("tr")
    .find(".amount")
    .val(parseFloat(qty * response).toFixed(amount_decimals));
}

//check all item is null check and submit button disabled
function check_item_null(total_qty_is, product_id) {
  if (total_qty_is == 0 && $(".total_qty").val() > 0) {
    $(":submit").attr("disabled", false);
  } else if (total_qty_is != 0) {
    let product = "";
    $(document)
      .find(".product_name")
      .each(function () {
        if ($(this).val()) product = $(this).val();
      });
    if (product == "" || product == null) $(":submit").attr("disabled", true);
    else $(":submit").attr("disabled", false);
  } else {
    $(":submit").attr("disabled", true);
  }
}

// input checking
function input_checking(class_name) {
  $("#orders,#orders_in,#orders_out").on(
    "keyup  selected blur",
    `.${class_name}_name`,
    function () {
      let id = $(this).closest("tr").find(`.${class_name}_id`).val();
      let name = $(this).closest("tr").find(`.${class_name}_name`).val();
      if (id.length != 0 || name.length == 0) {
        $(this)
          .closest("tr")
          .find(".debit_ledger_id_commission")
          .attr("required", "false");
        $(this)
          .closest("tr")
          .find(`.${class_name}_name`)
          .css({ backgroundColor: "white" });
        $("#orders").on("click", "input", function () {
          $(this).focus();
        });
      } else {
        $(this).css("backgroundColor", "red");
        $(".cansale_btn").attr("disabled", true);
        $(this).focus();
      }
    }
  );
}

//select auto change current qty and amount in and out
function selected_auto_value_change_in_out(
  check_current_stock,
  stock_class,
  qty_class,
  amount_class,
  currentEle,
  response,
  amount_decimals
) {
  let qty = 0;
  if (check_current_stock == 0) {
    if (
      currentEle.closest("tr").find(`.${stock_class}`).val() >=
      currentEle.closest("tr").find(`.${qty_class}`).val()
    ) {
      qty = currentEle.closest("tr").find(`.${qty_class}`).val();
    } else {
      currentEle
        .closest("tr")
        .find(`.${qty_class}`)
        .val(currentEle.closest("tr").find(`.${stock_class}`).val());
      qty = currentEle.closest("tr").find(`.${stock_class}`).val();
    }
  } else {
    qty = currentEle.closest("tr").find(`.${qty_class}`).val();
  }
  currentEle
    .closest("tr")
    .find(`.${amount_class}`)
    .val(parseFloat(qty * response).toFixed(amount_decimals));
}

// item checking in or out
function check_item_in_out(total_qty_is, product_id, total_qty, product_name) {
  if (total_qty_is == 0 && $(`.${total_qty}`).val() > 0) {
    $(":submit").attr("disabled", false);
  } else if (total_qty_is != 0) {
    let product = "";
    $(document)
      .find(`.${product_name}`)
      .each(function () {
        if ($(this).val()) product = $(this).val();
      });
    if (product == "" || product == null || product_id == 0)
      $(":submit").attr("disabled", true);
    else $(":submit").attr("disabled", false);
  } else {
    $(":submit").attr("disabled", true);
  }
}

// tab current  price
function tab_price(
  stock_item_id,
  voucher_id,
  url,
  check_current_stock,
  currentEle,
  amount_decimals
) {
  $.ajax({
    url: url,
    method: "GET",
    dataType: "json",
    async: false,
    data: {
      stock_item_id: stock_item_id,
      voucher_id: voucher_id,
      tran_date: $(".invoice_date").val(),
    },
    success: (response) => {
      if (response) {
        if (response.rate) {
          $(this).closest("tr").find(".rate").val(response.rate);
          select_auto_value_change_tab.call(
            $(this),
            check_current_stock,
            response.rate,
            amount_decimals
          );
        } else {
          $(this).closest("tr").find(".rate").val(0);
          select_auto_value_change_tab.call(
            $(this),
            check_current_stock,
            0,
            amount_decimals
          );
        }
        if (response.commission) {
          //$(this).closest('tr').find('.product_wise_commission_amount').val(response.commission)
        } else {
          //$(this).closest('tr').find('.product_wise_commission_amount').val(0)
        }
      } else {
        $(this).closest("tr").find(".rate").val(0);
        select_auto_value_change_tab.call(
          $(this),
          check_current_stock,
          0,
          amount_decimals
        );
      }
    },
  });
}

//auto tab price change
function select_auto_value_change_tab(
  check_current_stock,
  response,
  amount_decimals
) {
  let qty = 0;
  if (check_current_stock == 0) {
    let mainQty = $(this).closest("tr").find(".qty").attr("mainQty") || 0;
    if (
      parseFloat($(this).closest("tr").find(".stock").val()) +
        parseFloat(mainQty) >=
      $(this).closest("tr").find(".qty").val()
    ) {
      qty = $(this).closest("tr").find(".qty").val();
    } else if ($(this).closest("tr").find(".stock").val() >= 0) {
      $(this)
        .closest("tr")
        .find(".qty")
        .val($(this).closest("tr").find(".stock").val());
      qty = $(this).closest("tr").find(".stock").val();
    } else {
      $(this).closest("tr").find(".qty").val(0);
      qty = 0;
    }
  } else {
    qty = $(this).closest("tr").find(".qty").val();
  }
  $(this)
    .closest("tr")
    .find(".amount")
    .val(parseFloat(qty * response).toFixed(amount_decimals));
}

// party ledger autocomplete daynamic

function party_ledger_autocomplete(
  voucher_id,
  amount,
  ledger_name,
  ledger_name_id
) {
  let homeRoute = $("#homeRoute").attr("href");

  $("." + ledger_name + "").autocomplete({
    source: function (request, response) {
      $.ajax({
        type: "GET",
        dataType: "json",
        url: homeRoute + "/searching-ledger-debit",
        data: {
          name: request.term,
          voucher_id: voucher_id,
        },
        success: function (data) {
          console.log(data);
          response(
            $.map(data, function (item) {
              var object = new Object();
              object.label = item.ledger_name;
              object.value = item.ledger_name;
              object.ledger_head_id = item.ledger_head_id;
              object.inventory_value = item.inventory_value;
              return object;
            })
          );
        },
      });
    },
    change: function (event, ui) {
      if (ui.item == null) {
        $(this).val("");
        $(this).focus();
      }
    },
    select: function (event, ui) {
      $.ajax({
        url: url_get + "/get/balance/debit-credit",
        method: "GET",
        dataType: "json",
        async: false,
        data: {
          ledger_head_id: ui.item.ledger_head_id,
        },
        success: function (response) {
          if (ui.item.inventory_value == "Yes") {
            $(`.${amount}`).text(response.data);
            $(`.${ledger_name}`).val(ui.item.value);
            $(`.${ledger_name_id}`).val(ui.item.ledger_head_id);
            $(`.${error_inventory_value_affected}`).text("");
            return true;
          } else {
            $(`.${amount}`).text(response.data);
            $(`.${ledger_name}`).val(ui.item.value);
            $(`.${error_inventory_value_affected}`).text(
              "Inventory Value Affected NO"
            );
            return false;
          }
        },
      });
      return false;
    },
  });
}

function stockCalculation(
  tbody = "#orders",
  productId = ".product_id",
  quantity = ".qty",
  stockClass = ".stock",
  godownId = ".godown_id",
  actualQty = ".actual_qty"
) {
  let stock = {};
  $(`${tbody} tr`).each(function () {
    let product_id = $(this).find(productId).val();
    let qty = $(this).find(quantity).val();
    let godown_id = $(this).find(godownId).val();
    let actual_qty = $(this).find(actualQty).val() || 0;
    if (godown_id && product_id) {
      if (stock.hasOwnProperty(godown_id)) {
        if (stock[godown_id].hasOwnProperty(product_id)) {
          console.log(check_current_stock);
          if (check_current_stock == 1) {
            $(this).find(stockClass).val(stock[godown_id][product_id]);
            stock[godown_id][product_id] =
              parseFloat(stock[godown_id][product_id]) +
              parseFloat(actual_qty) -
              qty;
          } else {
            if (stock[godown_id][product_id] >= 0) {
              $(this).find(stockClass).val(stock[godown_id][product_id]);
              stock[godown_id][product_id] =
                parseFloat(stock[godown_id][product_id]) +
                parseFloat(actual_qty) -
                qty;
            } else {
              $(this).find(stockClass).val(0);
              $(this).find(quantity).val(0);
            }
          }
        } else {
          let stocks = $(this).find(stockClass).val();
          stock[godown_id][product_id] =
            parseFloat(stocks) + parseFloat(actual_qty) - qty;
        }
      } else {
        let stocks = $(this).find(stockClass).val();
        stock[godown_id] = {};
        stock[godown_id][product_id] =
          parseFloat(stocks) + parseFloat(actual_qty) - qty;
      }
    }
  });
}
