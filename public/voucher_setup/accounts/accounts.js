$(document).ready(function () {
    $("#add").click(function () {
        rowCount += 5;
        addrow(rowCount);
    });

    $("#orders").on(
        "change",
        "tr",
        ".DrCr,.display_credit, .display_debit",
        function () {
            button_debit_or_credit_total();
        }
    );

    $("#orders").on("keyup", ".display_credit", function () {
        let blance = $(this).closest("tr").find(".blance").val();
        let check_blance = blance.search("Cr");
        if (check_blance == -1) {
            if (debit_credit_amont == 0) {
                let credit = $(this).closest("tr").find(".credit").val();
                if (parseFloat(blance) < parseFloat(credit)) {
                    StoreCreditValue.call($(this).closest("tr"), "");
                    $(this)
                        .closest("tr")
                        .find(".display_credit")
                        .css({ backgroundColor: "red" });
                } else {
                    $(this)
                        .closest("tr")
                        .find(".display_credit")
                        .css({ backgroundColor: "white" });
                }
            }
        }
        button_debit_or_credit_total();
    });

    $("#orders").on("keyup", ".display_debit", function () {
        let blance = $(this).closest("tr").find(".blance").val();
        let check_blance = blance.search("Dr");
        if (check_blance == -1) {
            if (debit_credit_amont == 0) {
                let debit = $(this).closest("tr").find(".debit").val();
                if (parseFloat(blance) < parseFloat(debit)) {
                    StoreDebitValue.call($(this).closest("tr"), "");
                    $(this).closest("tr").find(".display_debit").css({
                        backgroundColor: "red",
                    });
                } else {
                    $(this)
                        .closest("tr")
                        .find(".display_debit")
                        .css({ backgroundColor: "white" });
                }
            }
        }
        button_debit_or_credit_total();
    });

    // $("#orders").on(
    //     "keyup",
    //     ".display_credit, .display_debit",
    //     function (event) {
    //         // Allow only numbers, backspace, and a single decimal point
    //         if (isValidNumberInput(event)) {
    //             if ($(this).attr("class").search("display_credit") >= 0) {
    //                 $(this)
    //                     .closest("tr")
    //                     .find(".credit")
    //                     .val(MakeCurrency($(this).val(), false));
    //             } else if ($(this).attr("class").search("display_debit") >= 0) {
    //                 $(this)
    //                     .closest("tr")
    //                     .find(".debit")
    //                     .val(MakeCurrency($(this).val(), false));
    //             }
    //         }
    //         button_debit_or_credit_total();
    //     }
    // );

    $("#orders").on("input", ".display_credit", function () {
        $(this)
            .closest("td")
            .find(".credit")
            .val(MakeCurrency($(this).val(), false));
        button_debit_or_credit_total();
    });
    $("#orders").on("input", ".display_debit", function () {
        $(this)
            .closest("td")
            .find(".debit")
            .val(MakeCurrency($(this).val(), false));
        button_debit_or_credit_total();
    });

    $("#orders").on(
        "change blur",
        ".display_credit, .display_debit",
        function () {
            if ($(this).val()) {
                $(this).val(MakeCurrency($(this).val(), true, amount_decimals));
            }
        }
    );

    $("#orders").on(
        "click  keyup",
        ".display_credit, .display_debit",
        function (event) {
            if (
                event.type === "click" ||
                (event.type === "keyup" && event.key === "Tab")
            ) {
                if ($(this).val() <= 0) {
                    let blance = $(this).closest("tr").find(".blance").val();
                    let DrCr = $(this).closest("tr").find(".DrCr").val();
                    let className = $(this).attr("class");
                    if (
                        className.search("display_credit") >= 0 &&
                        DrCr == "Cr"
                    ) {
                        let check_blance = blance.search("Cr");
                        let credit = parseFloat(DrCrCalculation("credit")) || 0;
                        if (credit) {
                            if (check_blance == -1) {
                                if (debit_credit_amont == 0) {
                                    if (parseFloat(blance) < credit) {
                                        StoreCreditValue.call(
                                            $(this).closest("tr"),
                                            ""
                                        );
                                        $(this)
                                            .closest("tr")
                                            .find(".display_credit")
                                            .css({ backgroundColor: "red" });
                                    } else {
                                        StoreCreditValue.call(
                                            $(this).closest("tr"),
                                            credit
                                        );
                                        $(this).css({
                                            backgroundColor: "white",
                                        });
                                    }
                                } else {
                                    StoreCreditValue.call(
                                        $(this).closest("tr"),
                                        credit
                                    );
                                }
                            } else {
                                StoreCreditValue.call(
                                    $(this).closest("tr"),
                                    credit
                                );
                            }
                        }
                    } else if (
                        className.search("display_debit") >= 0 &&
                        DrCr == "Dr"
                    ) {
                        let check_blance = blance.search("Dr");
                        let debit = parseFloat(DrCrCalculation("debit"));
                        if (debit) {
                            if (check_blance == -1) {
                                if (debit_credit_amont == 0) {
                                    if (blance < debit) {
                                        StoreDebitValue.call(
                                            $(this).closest("tr"),
                                            ""
                                        );
                                        $(this)
                                            .closest("tr")
                                            .find(".display_debit")
                                            .css({ backgroundColor: "red" });
                                    } else {
                                        StoreDebitValue.call(
                                            $(this).closest("tr"),
                                            debit
                                        );
                                        $(this)
                                            .closest("tr")
                                            .find(".display_debit")
                                            .css({ backgroundColor: "white" });
                                    }
                                } else {
                                    StoreDebitValue.call(
                                        $(this).closest("tr"),
                                        debit
                                    );
                                }
                            } else {
                                StoreDebitValue.call(
                                    $(this).closest("tr"),
                                    debit
                                );
                            }
                        }
                    }
                } else if (parseFloat(MakeCurrency($(this).val(), false)) > 0) {
                    $(this).val(MakeCurrency($(this).val(), false));
                }
                button_debit_or_credit_total();
            }
        }
    );

    registerEvents();
    input_checking("ledger");
});

function getId(element) {
    let id = element.attr("id");
    let idArr = id.split("_");
    return idArr[idArr.length - 1];
}

function button_debit_or_credit_total() {
    let debit = 0;
    let credit = 0;
    $("#orders tr").each(function (i) {
        if (parseFloat($(this).find(".debit").val()))
            debit += parseFloat($(this).find(".debit").val());
        if (parseFloat($(this).find(".credit").val()))
            credit += parseFloat($(this).find(".credit").val());
    });
    $(".total_dedit").val(MakeCurrency(debit, true, amount_decimals));
    $(".total_credit").val(MakeCurrency(credit, true, amount_decimals));
    if (!areApproxEqual(debit, credit)) {
        $(":submit").attr("disabled", true);
    } else {
        if (debit != 0) {
            $(":submit").attr("disabled", false);
        }
    }
    if (dc_amnt == 0) {
        if (debit == 0 || debit == "") {
            $(":submit").attr("disabled", true);
        } else {
            if (areApproxEqual(debit, credit)) {
                $(":submit").attr("disabled", false);
            }
        }
        if (credit == 0 || credit == "") {
            $(":submit").attr("disabled", true);
        } else {
            if (areApproxEqual(debit, credit)) {
                $(":submit").attr("disabled", false);
            }
        }
    }
}

function DrCrCalculation(type) {
    let debit = 0;
    let credit = 0;
    $("#orders tr").each(function (i) {
        if (parseFloat($(this).find(".debit").val())) {
            debit += parseFloat($(this).find(".debit").val());
        }
        if (parseFloat($(this).find(".credit").val())) {
            credit += parseFloat($(this).find(".credit").val());
        }
    });

    if (debit > credit && type == "credit") {
        $(".total_credit").val(MakeCurrency(debit, true, amount_decimals));
        return parseFloat(debit) - parseFloat(credit);
    } else if (debit < credit && type == "debit") {
        $(".total_dedit").val(MakeCurrency(credit, true, amount_decimals));
        return parseFloat(credit) - parseFloat(debit);
    }
}

function DrCrCalculation(type) {
    let debit = 0;
    let credit = 0;
    $("#orders tr").each(function (i) {
        if (parseFloat($(this).find(".debit").val()))
            debit += parseFloat($(this).find(".debit").val());
        if (parseFloat($(this).find(".credit").val()))
            credit += parseFloat($(this).find(".credit").val());
    });

    if (debit > credit && type == "credit") {
        $(".total_credit").val(MakeCurrency(debit, true, amount_decimals));
        return parseFloat(debit) - parseFloat(credit);
    } else if (debit < credit && type == "debit") {
        $(".total_dedit").val(MakeCurrency(credit, true, amount_decimals));
        return parseFloat(credit) - parseFloat(debit);
    }
}

function registerEvents() {
    $(document).on("focus", ".autocomplete_txt", handleAutocomplete);
}

function calculateCurrentTr() {
    let DrCr = $(this).find(".DrCr").val();
    let credit = $(this).find(".credit").val();
    let debit = parseFloat($(this).find(".debit").val());
    let blance = parseFloat($(this).find(".blance").val());
    if (DrCr == "Cr") {
        if (debit) {
            StoreDebitValue.call($(this), "");
            let check_blance = $(this).find(".blance").val().search("Cr");
            if (check_blance == -1 && debit_credit_amont == 0) {
                if (blance < debit) {
                    StoreCreditValue.call($(this), "");
                    $(this)
                        .find(".display_credit")
                        .css({ backgroundColor: "red" });
                } else {
                    $(this)
                        .find(".display_credit")
                        .css({ backgroundColor: "white" });
                    StoreCreditValue.call($(this), debit);
                }
            } else {
                StoreCreditValue.call($(this), debit);
                $(this).find(".display_debit").css({ backgroundColor: "" });
            }
        }
        $(this).find(".display_credit").attr("readonly", false);
        $(this).find(".display_debit").attr("readonly", true);
    } else if (DrCr == "Dr") {
        if (credit) {
            StoreCreditValue.call($(this), "");
            let check_blance = $(this).find(".blance").val().search("Dr");
            if (check_blance == -1 && debit_credit_amont == 0) {
                if (blance < parseFloat(credit)) {
                    StoreDebitValue.call($(this), "");
                    $(this)
                        .find(".display_debit")
                        .css({ backgroundColor: "red" });
                } else {
                    // $(this).find('.debit').css({backgroundColor: 'white'});
                    StoreDebitValue.call($(this), credit);
                }
            } else {
                StoreDebitValue.call($(this), credit);
                $(this).find(".display_credit").css({ backgroundColor: "" });
            }
        }
        $(this).find(".display_debit").attr("readonly", false);
        $(this).find(".display_credit").attr("readonly", true);
    }
}

function addrowCreate(rowCount) {
    if (rowCount == null) {
        rowCount = 1;
    }

    for (var row = 1; row < 6; row++) {
        rowCount++;
        let ledgerDebitId = "";
        let ledger_name = "";
        let display_blance = "";
        let blance = "";
        let appended = false;
        if (ledger_debit.length) {
            if (ledger_debit.length && rowCount == 2) {
                ledgerDebitId = ledger_debit_id;
                ledger_name = ledger_debit;
                display_blance = debit_balance_cal;
                blance = debit_balance_cal;
                appended = true;
            } else if (ledger_debit.length && rowCount == 3) {
                ledgerDebitId = ledger_debit_id;
                ledger_name = credit_ledger;
                display_blance = credit_balance_cal;
                blance = credit_balance_cal;
                appended = true;
            } else if (rowCount > 3) {
                appended = true;
            }
        } else {
            appended = true;
        }
        if (appended) {
            $("#orders").append(`
            <tr style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${rowCount}">
                <input
                    class="form-control  ledger_id m-0 p-0"
                    type="hidden"
                    name="ledger_id[]"
                    data-type="ledger_id"
                    id="ledger_id_${rowCount}"
                    for="${rowCount}"
                    value="${ledgerDebitId}"
                />
                <td  class="m-0 p-0">
                    <button  type="button" name="remove" id="${rowCount}" class="btn btn-danger btn_remove cicle" style="padding: 0px 19px;margin:1px 0px;">-</button>
                </td>
                <td  class="m-0 p-0">
                    <select  id="DrCr" name="DrCr[]" class="form-control DrCr " data-field-name="DrCr"  >
                        <option value="Dr">Dr</option>
                        <option value="Cr">Cr</option>
                    </select>
                </td>
                <td  class="m-0 p-0">
                    <input
                        class="form-control ledger_name  autocomplete_txt"
                        name="ledger_name[]"
                        data-field-name="ledger_name"
                        type="text"
                        data-type="ledger_name"
                        id="ledger_name_${rowCount}"
                        autocomplete="off"
                        for="${rowCount}"
                        value="${ledger_name}"
                    />
                </td>
                <td  class="m-0 p-0">
                    <input
                        class="form-control display_blance text-right"
                        name="display_blance[]"
                        data-field-name="display_blance"
                        type="text"
                        id="display_blance_${rowCount}"
                        for="${rowCount}"
                        readonly
                        value="${display_blance}"
                    />
                    <input
                        class="blance d-none"
                        name="blance[]"
                        data-field-name="blance"
                        type="number"
                        step="any"
                        id="blance_${rowCount}"
                        for="${rowCount}"
                        readonly
                        value="${blance}"
                    />
                </td>
                <td  class="m-0 p-0">
                    <input
                        class="form-control display_debit text-right "
                        data-field-name="display_debit"
                        name="display_debit[]"
                        type="text"
                        data-type="display_debit"
                        id="display_debit_${rowCount}"
                        for="${rowCount}"
                    />
                    <input
                        class="debit d-none"
                        data-field-name="debit"
                        name="debit[]"
                        type="number"
                        step="any"
                        data-type="debit"
                        id="debit_${rowCount}"
                        for="${rowCount}"
                    />
                </td>
                <td  class="m-0 p-0">
                    <input
                        class="form-control display_credit text-right"
                        type="text"
                        name="display_credit[]"
                        id="display_credit_${rowCount}"
                        for="${rowCount}"
                        readonly
                    />
                    <input
                        class="credit d-none"
                        type="number"
                        step="any"
                        name="credit[]"
                        id="credit_${rowCount}"
                        for="${rowCount}"
                        readonly
                    />
                </td>
                ${
                    remark_is == 1 &&
                    `<td  class="m-0 p-0 "><input class="form-control remark"  name="remark[]" type="text" data-type=" id="remark_${rowCount}"  autocomplete="off" for="${rowCount}"/></td>`
                }
            </tr>`);
        }
    }
}

function addRowEdit(rowCount) {
    if (rowCount == null) {
        rowCount = 1;
    } else {
        rowCount = rowCount;
    }
    for (var row = 1; row < 6; row++) {
        rowCount++;
        $("#orders")
            .append(`<tr style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${rowCount}">
            <input class="form-control  ledger_id m-0 p-0"  name="ledger_id[]" type="hidden" data-type="ledger_id" id="ledger_id_${rowCount}"  for="${rowCount}"/>
            <td  class="m-0 p-0"><button  type="button" name="remove" id="${rowCount}" class="btn btn-danger btn_remove cicle" style="padding: 0px 19px;margin:1px 0px;">-</button></td>
            <td  class="m-0 p-0">
                <select  name="DrCr[]" id="DrCr" data-field-name="DrCr"  class="form-control  DrCr " >
                    <option value="Dr">Dr</option>
                    <option value="Cr">Cr</option>
                </select>
            </td>
            <td  class="m-0 p-0">
                <input class="form-control ledger_name  autocomplete_txt" name="ledger_name[]" data-field-name="ledger_name"  type="text" data-type="ledger_name" id="ledger_name_${rowCount}" " autocomplete="off" for="${rowCount}"  />
            </td>
            <td  class="m-0 p-0">
                <input
                    class="form-control display_blance text-right "
                    name="display_blance[]"
                    data-field-name="display_blance"
                    type="text"
                    class="display_blance"
                    id="display_blance_${rowCount}"
                    for="${rowCount}"
                />
                <input
                    class="form-control blance text-right "
                    name="blance[]"
                    data-field-name="blance"
                    type="hidden"
                    class="blance"
                    id="blance_${rowCount}"
                    for="${rowCount}"
                />

            </td>
            <td  class="m-0 p-0">
                <input
                    class="form-control display_debit  text-right "
                    name="display_debit[]"
                    data-field-name="display_debit"
                    type="text"
                    data-type="display_debit"
                    id="display_debit_${rowCount}"
                    for="${rowCount}"
                />
                <input
                    class="form-control debit  text-right "
                    name="debit[]"
                    data-field-name="debit"
                    type="hidden"
                    data-type="debit"
                    id="debit_${rowCount}"
                    for="${rowCount}"
                />
            </td>
            <td  class="m-0 p-0">
                <input
                    class="form-control display_credit  text-right"
                    type="text"
                    name="display_credit[]"
                    id="display_credit_${rowCount}"
                    for="${rowCount}"
                    readonly
                />
                <input
                    class="form-control credit  text-right"
                    type="hidden"
                    name="credit[]"
                    id="credit_${rowCount}"
                    for="${rowCount}"
                    readonly
                />
            </td>
            ${
                remark_is == 1 &&
                `<td  class="m-0 p-0 "><input class="form-control remark"  name="remark[]" type="text" data-type=" id="remark_${rowCount}"  autocomplete="off" for="${rowCount}"/></td>`
            }
            </tr>`);
    }
}

function handleAutocomplete() {
    var fieldName, currentEle, DrCr;
    currentEle = $(this);

    fieldName = currentEle.data("field-name");
    DrCr = $(this).closest("tr").find(".DrCr").val();

    if (typeof fieldName === "undefined") {
        return false;
    }

    currentEle.autocomplete({
        delay: 500,
        source: function (data, cb) {
            $.ajax({
                url: searchingLedgerDataRoute,
                method: "GET",
                dataType: "json",
                data: {
                    name: data.term,
                    fieldName: fieldName,
                    voucher_id: voucher_id,
                    DrCr: DrCr,
                },
                success: function (res) {
                    var result;
                    result = [
                        {
                            label:
                                "There is no matching record found for " +
                                data.term,
                            value: "",
                        },
                    ];

                    if (res.length) {
                        result = $.map(res, function (obj) {
                            return {
                                label: obj[fieldName],
                                value: obj[fieldName],
                                data: obj,
                            };
                        });
                    }

                    cb(result);
                },
            });
        },
        autoFocus: true,
        minLength: 1,
        change: function (event, ui) {
            if (ui.item == null) {
                if ($(this).attr("name") === "ledger_name[]")
                    $(this).closest("tr").find(".ledger_id").val("");
                $(this).focus();
                check_item_null(dc_amnt, 0);
            }
        },
        select: function (event, selectedData) {
            if (
                checkDuplicateItem(
                    selectedData?.item?.data?.ledger_head_id,
                    ".ledger_id"
                )
            ) {
                currentEle.val("");
            } else if (
                selectedData &&
                selectedData.item &&
                selectedData.item.data
            ) {
                var rowNo, data;
                rowNo = getId(currentEle);
                data = selectedData.item.data;
                currentEle.css({ backgroundColor: "white" });
                check_item_null(dc_amnt, 1);
                $("#ledger_id_" + rowNo).val(data.ledger_head_id);
                $.ajax({
                    url: '{{route("balance-debit-credit") }}',
                    method: "GET",
                    dataType: "json",
                    async: false,
                    data: {
                        ledger_head_id: data.ledger_head_id,
                    },
                    success: function (response) {
                        $("#blance_" + rowNo).val(response.data);
                        if (show_party_balance_is) {
                            $("#display_blance_" + rowNo).val(
                                response?.data || ""
                            );
                        } else {
                            $("#display_blance_" + rowNo).val("");
                        }
                    },
                });
            }
        },
    });
}
