@extends('layouts.backend.app')
@section('title', 'Group Wise Party Ledger')
@push('css')
    <!-- model style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('libraries/assets/modal-style.css') }}">
    <style>
        input[type=radio] {
            width: 20px;
            height: 20px;
        }

        input[type=checkbox] {
            width: 20px;
            height: 20px;
        }

        table {
            width: 100%;
            grid-template-columns: auto auto;
        }

        .td {
            width: 3%;
            border: 1px solid #ddd;
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            font-family: Arial, sans-serif;
        }

        .table-scroll thead tr:nth-child(2) th {
            top: 30px;
        }

        body {
            overflow: auto !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 18px !important;
        }
    </style>
@endpush
@section('admin_content')<br>
    <!-- setting component-->
    @component('components.report_setting_modal', [
        'id' => 'exampleModal',
        'class' => 'modal fade',
        'size' => 'modal-xl',
        'page_title' => 'Group Wise  Party Ledger',
        'page_unique_id' => 12,
        'groupChart'=>'yes',
        'accounts' => 'Yes',
        'title' => 'Group Wise  Party Ledger',
        'daynamic_function' => 'get_group_party_ledger_initial_show',
    ])
    @endcomponent
    <!-- add component-->
    @component('components.report', [
        'title' => 'Group Wise  Party Ledger',
        'print_layout' => 'portrait',
        'print_header' => 'Group Wise  Party Ledger',
        'user_privilege_title' => 'GroupWisePartyLedger',
        'print_date' => 1,
        'report_setting_model' => 'report_setting_model',
        'report_setting_mail' => 'report_setting_mail',
    ]);

        <!-- Page-header component -->
        @slot('header_body')
            <form id="group_wise_party_form" method="POST">
                @csrf
                {{ method_field('POST') }}
                <div class="row">
                    <div class="col-md-4">
                        <label>Accounts Group :</label>
                        <select name="group_id" class="form-control js-example-basic-single group_id" required>
                            @if ($all == 1)
                                <option value="0">--All--</option>
                            @endif
                            {!! html_entity_decode($group_chart_data) !!}
                        </select>
                        <div class="row px-2">
                            <div class="col-md-6">
                                <label>Date From: </label>
                                <input type="text" name="from_date" class="form-control setup_date from_date"
                                    value="{{ financial_end_date(date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label>Date To : </label>
                                <input type="text" name="to_date" class="form-control setup_date to_date"
                                    value="{{ financial_end_date(date('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label></label>
                        <div class="form-group mb-0" style="position: relative">
                            <input class="form-check-input ledger_address" type="checkbox" name="ledger_address" value="1">
                            <label class="form-check-label fs-6" for="flexRadioDefault1">
                                Ledger Address
                            </label>
                            <input class="form-check-input ledger_alias" type="checkbox" name="ledger_alias" value="1">
                            <label class="form-check-label fs-6" for="flexRadioDefault1">
                                Ledger Alias
                            </label>
                        </div>
                        <div class="form-group m-0 p-0" style="position: relative">
                            <input class="form-check-input opening_blance" type="checkbox" name="opening_blance" value="1"
                                checked="checked">
                            <label class="form-check-label fs-6" for="flexRadioDefault1">
                                Opening Balance
                            </label>
                        </div>
                        <div class="form-group m-0 p-0" style="position: relative">
                            <input class="form-check-input debit_amount" type="checkbox" name="debit_amount" value="1"
                                checked="checked">
                            <label class="form-check-label fs-6" for="flexRadioDefault1">
                                Debit Amount
                            </label>
                            <input class="form-check-input credit_amount" type="checkbox" name="credit_amount" value="1"
                                checked="checked">
                            <label class="form-check-label fs-6" for="flexRadioDefault1">
                                Credit Amount
                            </label>
                        </div>
                        <div class="form-group m-0 p-0" style="position: relative">
                            <input class="form-check-input closing_blance" type="checkbox" name="closing_blance" value="1"
                                checked="checked">
                            <label class="form-check-label fs-6" for="flexRadioDefault1">
                                Closing Balance
                            </label>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <input class="form-check-input separate_salse_return_blance" type="checkbox" value="1"
                            name="separate_salse_return_blance">
                        <label class="form-check-label fs-6" for="flexRadioDefault1">
                            Separate Credit Amount (Collection, Sales Return)
                        </label><br>
                        <input class="form-check-input separate_closing_blance" type="checkbox" value="1"
                            name="separate_closing_blance">
                        <label class="form-check-label fs-6" for="flexRadioDefault1">
                            Separate Closing Balance
                        </label><br>
                        <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit"
                            style=" width:200px; margin-bottom:5px;"><span class="m-t-1 m-1"></span><span>Search</span></button>
                    </div>
                </div>
            </form>
        @endslot

        <!-- Main body component -->
        @slot('main_body')
            <div class="dt-responsive table-responsive cell-border sd tableFixHead_ledger_group_wise">
                <table id="tableId" style=" border-collapse: collapse; " class="table-striped customers table-scroll table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="align-middle" style="width: 1%;  border: 1px solid #ddd;font-weight: bold;">
                                SL.</th>
                            <th rowspan="2" class="align-middle" style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">
                                Particulars</th>
                            <th rowspan="2" style="width: 1%;  border: 1px solid #ddd;font-weight: bold;"
                                class="alias_checkbox align-middle">Alias</th>
                            <th rowspan="2" style="width: 2%;  border: 1px solid #ddd;font-weight: bold;"
                                class="opening_checkbox text-end align-middle">Opening Balance</th>
                            <th rowspan="2" style="width: 3%;  border: 1px solid #ddd;font-weight: bold;"
                                class="debit_checkbox text-end align-middle">Debit Amount</th>
                            <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;"
                                class="credit_checkbox_balance credit_checkbox text-center">Credit Amount</th>
                            <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;"
                                class="closing_checkbox closing_checkbox_balance closing_checkbox text-center">Closing Balance</th>
                        </tr>
                        <tr>
                            <th style="width: 3%;" class="credit_checkbox credit_checkbox_separete td text-end">Collection</th>
                            <th style="width: 3%;" class="credit_checkbox credit_checkbox_separete td text-end">Sales Return</th>
                            <th class="th closing_debit_check closing_checkbox closing_checkbox_separete text-end"
                                style="width: 2%;  overflow: hidden; border: 1px solid #ddd;">Debit</th>
                            <th class="th closing_debit_check closing_checkbox closing_checkbox_separete text-end"
                                style="width: 2%;  overflow: hidden; border: 1px solid #ddd;">Credit</th>
                        </tr>
                    </thead>
                    <tbody id="myTable" class="item_body">
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="width: 1%;;" class="td"></th>
                            <th style="width: 1%;" class="td alias_checkbox"></th>
                            <th style="width: 3%;" class="td">Total :</th>
                            <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;font-size: 18px;"
                                class="total_opening opening_checkbox text-end"></th>
                            <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;font-size: 18px;"
                                class="total_debit debit_checkbox text-end"></th>
                            <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;font-size: 18px;"
                                class="credit_checkbox total_credit text-end"></th>
                            <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;font-size: 18px;"
                                class="credit_checkbox_separete credit_checkbox total_sales_return_credit separate_salse_return_blance_check text-end">
                            </th>
                            <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;font-size: 18px;"
                                class="total_clasing closing_checkbox closing_dedit_checking text-end"></th>
                            <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;font-size: 18px;"
                                class="closing_checkbox_separete closing_debit_check closing_credit_checking closing_checkbox text-end">
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endslot
    @endcomponent
    <br>
    @push('js')
        <!-- table hover js -->
        <script type="text/javascript" src="{{ asset('libraries/assets/table-hover.js') }}"></script>
        <script type="text/javascript" src="{{ asset('dist/jquery-simple-tree-table.js') }}"></script>
        <script>
            let total_opening = 0;
            total_debit = 0;
            total_credit = 0;
            total_clasing = 0;
            total_sales_return_credit = 0;
            i = 1;
            drcr_closing_sign = '';
            // group chart  id check
            console.log("{{ $group_id ?? 0 }}");
            if ("{{ $group_id ?? 0 }}" != 0) {

                $('.group_id').val('{{ $group_id ?? 0 }}');
            }
            if ("{{ $form_date ?? 0 }}" != 0) {
                $('.from_date').val('{{ $form_date ?? 0 }}');
            }
            if ("{{ $to_date ?? 0 }}" != 0) {
                $('.to_date').val('{{ $to_date ?? 0 }}');
            }

            // group wise  party ledger quantity
            $(document).ready(function() {

                // set local store data
                if ("{{ $to_date ?? 0 }}" != 0) {
                    local_store_group_wise_party_ledger_details_set_data();
                }

                // get local store data
                //  local_store_group_wise_party_ledger_details_get()
                // if (getStorage("group_id", '.group_id')) {
                    // group chart get id check
                    // get_group_party_ledger_initial_show();
                // }

                $("#group_wise_party_form").submit(function(e) {

                    // local store data
                    local_store_group_wise_party_ledger_details_set_data()
                    print_date();
                    total_opening = 0;
                    total_debit = 0;
                    total_credit = 0;
                    total_clasing = 0;
                    total_sales_return_credit = 0;
                    i = 1;;
                    e.preventDefault();
                    $(".modal").show();
                    const fd = new FormData(this);
                    $.ajax({
                        url: '{{ route('group-wise-party-ledger-get-data') }}',
                        method: 'POST',
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                            $('.item_body').empty();
                            $(".modal").hide();
                            get_group_wise_party_ledger(response)
                        },
                        error: function(data, status, xhr) {
                            Unauthorized(data.status);
                        }
                    });
                });

                chcking_checkbok();
            });

            function getTreeView(arr, children_sum, depth = 0, chart_id = 0) {
                let htmlFragments = [];
                arr.forEach(function(v) {
                    a = '&nbsp;';
                    h = a.repeat(depth);
                    if (v.under != 0) {
                        if (chart_id != v.group_chart_id) {
                            let matchingChild = children_sum.find(c => v.group_chart_id == c.group_chart_id);
                            if (((matchingChild.op_group_debit || 0) == 0) && ((matchingChild.op_group_credit || 0) ==
                                    0) && ((matchingChild.group_credit || 0) == 0) && ((matchingChild.group_credit ||
                                    0) == 0) && ((matchingChild.sales_return_credit || 0) == 0) && ((matchingChild
                                    .sales_return_debit || 0) == 0)) {} else {
                                htmlFragments.push(`<tr id="${v.group_chart_id + '-' + v.under}" class='left left-data group_chart_id table-row table-row_tree'>
                                <td class="td"></td>
                                <td  class="group_td" style='wwidth: 3%; color: #0B55C4; border: 1px solid #ddd;font-weight: bold;'>
                                    <p style="margin-left:${(h + a + a).length-12}px;" class="text-wrap mb-0 pb-0 ">
                                        ${groupWisePartyLedgerIdWise((v.group_chart_id + '-' + v.under),v.group_chart_name)}
                                    </p>
                                </td>
                                <td class="td alias_checkbox"></td>
                              `);

                                if (matchingChild) {
                                    let total_op_val;
                                    let total_op_sign;
                                    let total_closing_val;
                                    let total_closing_sign;
                                    if (v.nature_group == 1 || v.nature_group == 3) {
                                        total_op_val = ((matchingChild.op_group_debit || 0) - (matchingChild
                                            .op_group_credit || 0));
                                        total_op_sign = total_op_val >= 0 ? 'Dr' : 'Cr';
                                    } else {
                                        total_op_val = ((matchingChild.op_group_credit || 0) - (matchingChild
                                            .op_group_debit || 0))
                                        total_op_sign = total_op_val >= 0 ? 'Cr' : 'Dr';
                                    }
                                    const totalOpeningBalance = Math.abs(total_op_val).formatBangladeshCurrencyType(
                                        "accounts", '', total_op_sign);
                                    if (v.nature_group == 1 || v.nature_group == 3) {
                                        total_closing_val = (((matchingChild.op_group_debit || 0) - (matchingChild
                                            .op_group_credit || 0) + (matchingChild.group_debit +
                                            matchingChild.sales_return_debit || 0)) - ((matchingChild
                                            .group_credit || 0) + (matchingChild.sales_return_credit || 0)));
                                        total_closing_sign = total_closing_val >= 0 ? 'Dr' : 'Cr';
                                    } else {
                                        total_closing_val = ((matchingChild.op_group_credit || 0) - (matchingChild
                                            .op_group_debit || 0) + (matchingChild.group_credit + matchingChild
                                            .sales_return_credit || 0)) - ((matchingChild.group_debit || 0) + (
                                            matchingChild.sales_return_debit || 0));
                                        total_closing_sign = total_closing_val >= 0 ? 'Cr' : 'Dr';
                                    }

                                    if (total_closing_sign != drcr_closing_sign) {
                                        drcr_closing_sign = total_closing_sign;
                                    }
                                    const totalCurrentBalance = Math.abs(total_closing_val)
                                        .formatBangladeshCurrencyType("accounts", '', total_closing_sign);

                                    htmlFragments.push(`<td class="td opening_checkbox text-end" >  ${totalOpeningBalance}</td>
                                        <td class="td debit_checkbox text-end" > ${((matchingChild.group_debit+matchingChild.sales_return_debit || 0)).formatBangladeshCurrencyType("accounts")} </td>
                                        ${$(".separate_salse_return_blance").is(':checked') ?
                                        `<td class="td credit_checkbox text-end" > ${((matchingChild.group_credit || 0)).formatBangladeshCurrencyType("accounts")}</td>
                                                         <td class="td credit_checkbox text-end" > ${((matchingChild.sales_return_credit || 0)).formatBangladeshCurrencyType("accounts")}</td>`:
                                         `<td class="td text-end" > ${((matchingChild.sales_return_credit+matchingChild.group_credit||0)).formatBangladeshCurrencyType("accounts")}</td>`}

                                        ${$(".separate_closing_blance").is(':checked') ? `<td class="td closing_checkbox" > ${total_closing_sign=='Dr'?Math.abs(total_closing_val).formatBangladeshCurrencyType("accounts"):''}</td>
                                                        <td class="td closing_checkbox text-end" >${total_closing_sign=='Cr'? Math.abs(total_closing_val).formatBangladeshCurrencyType("accounts"):''} </td>` : `<td class="td closing_checkbox text-end" > ${totalCurrentBalance} </td>`}
                                    `);

                                }
                                htmlFragments.push(`</tr>`);
                            }
                            chart_id = v.group_chart_id;
                        }
                        if (v.ledger_head_id) {
                            let opening_val;
                            let op_sign;
                            let clasing_val;
                            let closing_sign;
                            if (v.nature_group == 1 || v.nature_group == 3) {
                                opening_val = openning_blance_cal(v.nature_group, v.DrCr, v.opening_balance) + (v
                                    .op_total_debit || 0) - (v.op_total_credit || 0);
                                op_sign = opening_val >= 0 ? 'Dr' : 'Cr';
                            } else {
                                opening_val = openning_blance_cal(v.nature_group, v.DrCr, v.opening_balance) + (v
                                    .op_total_credit || 0) - (v.op_total_debit || 0);
                                op_sign = opening_val >= 0 ? 'Cr' : 'Dr';
                            }
                            total_opening = total_opening + parseFloat(opening_val);
                            const openingBalance = Math.abs(opening_val).formatBangladeshCurrencyType("accounts", '',
                                op_sign);

                            total_debit += (v.total_debit + v.sales_return_debit_sum || 0);
                            total_credit += (v.total_credit || 0);
                            total_sales_return_credit += (v.sales_return_credit_sum || 0);

                            if (v.nature_group == 1 || v.nature_group == 3) {
                                clasing_val = openning_blance_cal(v.nature_group, v.DrCr, v.opening_balance) + (((v
                                    .op_total_debit || 0) - (v.op_total_credit || 0) + ((v.total_debit ||
                                    0) + (v.sales_return_debit_sum || 0))) - ((v.total_credit || 0) + (v
                                    .sales_return_credit_sum || 0)));
                                closing_sign = clasing_val >= 0 ? 'Dr' : 'Cr';
                            } else {
                                clasing_val = openning_blance_cal(v.nature_group, v.DrCr, v.opening_balance) + (((v
                                    .op_total_credit || 0) - (v.op_total_debit || 0) + ((v.total_credit ||
                                    0) + (v.sales_return_credit_sum || 0))) - ((v.total_debit || 0) + (v
                                    .sales_return_debit_sum || 0)));
                                closing_sign = clasing_val >= 0 ? 'Cr' : 'Dr';
                            }


                            total_clasing = parseFloat(total_clasing) + parseFloat(clasing_val);
                            const currentBalance = Math.abs(clasing_val).formatBangladeshCurrencyType("accounts", '',
                                closing_sign);
                            if (((opening_val || 0) == 0) && ((v.total_debit || 0) == 0) && ((v
                                    .sales_return_debit_sum || 0) == 0) && ((v.total_credit || 0) == 0) && ((v
                                    .sales_return_credit_sum || 0) == 0)) {} else {
                                if ($('#show_closing_is').is(':checked')) {
                                    if (clasing_val == 0) {} else {
                                        party_ledger_data_show(htmlFragments, v, openingBalance, closing_sign,
                                            clasing_val, currentBalance)
                                    }

                                } else {
                                    party_ledger_data_show(htmlFragments, v, openingBalance, closing_sign, clasing_val,
                                        currentBalance)
                                }
                            }
                        }
                    }
                    if ('children' in v) {
                        htmlFragments.push(getTreeView(v.children, children_sum, depth + 1, chart_id));
                    }
                });
                return htmlFragments.join('');
            }

            // group wise party ledger function
            function get_group_wise_party_ledger(response) {
                const children_sum = response.data.sum_of_children;
                let tree = getTreeView(response.data.group_wise_ledger, children_sum);
                document.querySelector('#myTable').innerHTML = tree
                //    $('.item_body').html(tree);
                get_hover();
                $('.total_opening').text(total_opening.formatBangladeshCurrencyType("accounts"));
                $('.total_debit').text(total_debit.formatBangladeshCurrencyType("accounts"));

                if ($(".separate_salse_return_blance").is(':checked')) {
                    $('.total_credit').text(total_credit.formatBangladeshCurrencyType("accounts"));
                    $('.total_sales_return_credit').text(total_sales_return_credit.formatBangladeshCurrencyType("accounts"));
                } else {
                    $('.total_credit').text((total_credit + total_sales_return_credit).formatBangladeshCurrencyType(
                        "accounts"));
                }

                if ($(".separate_closing_blance").is(':checked')) {
                    if (drcr_closing_sign == 'Dr') {
                        // Format number with commas for Dr
                        $('.closing_dedit_checking').text(total_clasing.formatBangladeshCurrencyType("accounts"));
                        $('.closing_credit_checking').text('');
                    } else if (drcr_closing_sign == 'Cr') {
                        // Format number with commas for Cr
                        $('.closing_credit_checking').text(total_clasing.formatBangladeshCurrencyType("accounts"));
                        $('.closing_dedit_checking').text('');
                    } else {
                        // Default formatting when no Dr or Cr sign
                        $('.total_clasing').text('');
                    }
                } else {
                    // If the checkbox is not checked, default to formatting with zero
                    $('.total_clasing').text(total_clasing.formatBangladeshCurrencyType("accounts", '', drcr_closing_sign));
                    $('.closing_credit_checking').text('');
                }

                chcking_checkbok();
            }


            // get group wise  party ledger
            //    function get_group_party_ledger_initial_show(){
            //            local_store_group_wise_party_ledger_details_get();
            //            print_date();
            //            total_opening=0; total_debit=0; total_credit=0;total_clasing=0; total_sales_return_credit=0; i=1;
            //             $(".modal").show();
            //             $.ajax({
            //                 url: '{{ route('group-wise-party-ledger-get-data') }}',
            //                     method: 'GET',
            //                     data: {
            //                         to_date:$('.to_date').val(),
            //                         from_date:$('.from_date').val(),
            //                         group_id:$(".group_id").val(),
            //                     },
            //                     dataType: 'json',
            //                     success: function(response) {
            //                         $('.item_body').empty();
            //                         $(".modal").hide();
            //                         get_group_wise_party_ledger(response)
            //                     },
            //                     error : function(data,status,xhr){
            //                         Unauthorized(data.status);
            //                     }
            //             });
            //    }

            function party_ledger_data_show(htmlFragments, v, openingBalance, closing_sign, clasing_val, currentBalance) {
                htmlFragments.push(`<tr id="${v.ledger_head_id}" class="table-row table-row_id">
                        <td style="width: 1%;  border: 1px solid #ddd;">${i++}</td>
                        <td  class='ledger_td' style="width: 5%;  border: 1px solid #ddd;color: #0B55C4;">
                            <p style="margin-left:${(h+a+a+a).length-12}px;font-size: 16px;" class="text-wrap mb-0 pb-0">
                                ${partyLedgerIdWise(v.ledger_head_id,v.ledger_name)}
                            </p>
                            <p style="margin-left:${(h+a+a+a).length-12}px;font-size: 12px;" class="text-wrap mb-0 pb-0 address_checkbox">
                                ${v.mailing_add||''}
                            </p>
                        </td>
                        <td class='alias_checkbox text-wrap' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;'>${(v.alias||'')}</td>
                        <td class='opening_checkbox text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;'>${(openingBalance)}</td>
                        <td class='debit_checkbox text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;t'>${(((v.total_debit||0)).formatBangladeshCurrencyType("accounts"))}</td>
                        ${$(".separate_salse_return_blance").is(':checked') ?
                            `<td class='credit_checkbox text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;'>${(((v.total_credit||0)).formatBangladeshCurrencyType("accounts"))}</td>
                                            <td class='credit_checkbox text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;'>${(((v.sales_return_credit_sum||0)).formatBangladeshCurrencyType("accounts"))}</td>`
                        :`<td class='text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;'>${(((v.sales_return_credit_sum+v.total_credit||0)).formatBangladeshCurrencyType("accounts"))}</td>`}
                        ${$(".separate_closing_blance").is(':checked') ? `<td class='closing_checkbox text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;'> ${closing_sign=='Dr'?Math.abs(clasing_val).formatBangladeshCurrencyType("accounts"):''}</td>
                                                <td class='closing_checkbox text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;'>${closing_sign=='Cr'?Math.abs(clasing_val).formatBangladeshCurrencyType("accounts"):''} </td>` : `<td class='closing_checkbox text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;'>${(currentBalance)}</td>`}
            </tr>`);

            }

            //get  all data show
            $(document).ready(function() {
                // party ledger  wise summary route
                // $('.sd').on('click','.ledger_td',function(e){
                //     e.preventDefault();
                //     let ledger_id=$(this).closest('tr').attr('id');
                //     let form_date=$('.from_date').val();
                //     let to_date=$('.to_date').val();
                //     url = "{{ route('party-ledger-id-wise', ['ledger_id' => ':ledger_id', 'form_date' => ':form_date', 'to_date' => ':to_date']) }}";
                //     url = url.replace(':ledger_id',ledger_id);
                //     url = url.replace(':form_date',form_date);
                //     url = url.replace(':to_date',to_date);
                //     window.open(url, '_blank');
                // });


                // group  party ledger wise summary route
                // $('.sd').on('click','.group_td',function(e){
                //     e.preventDefault();
                //         let  group_chart_id=$(this).closest('tr').attr('id');
                //         let form_date=$('.from_date').val();
                //         let to_date=$('.to_date').val();
                //         url = "{{ route('group-wise-party-ledger-id-wise', ['group_chart_id' => ':group_chart_id', 'form_date' => ':form_date', 'to_date' => ':to_date']) }}";
                //         url = url.replace(':group_chart_id',group_chart_id);
                //         url = url.replace(':form_date',form_date);
                //         url = url.replace(':to_date',to_date);
                //         window.open(url, '_blank');
                // })
            });



            function local_store_group_wise_party_ledger_details_get() {
                // getStorage("end_date", '.to_date');
                // getStorage("start_date", '.from_date');
                // getStorage("group_id", '.group_id');
                // getStorage("ledger_address", '.ledger_address', 'checkbox');
                // getStorage("ledger_alias", '.ledger_alias', 'checkbox');
                // getStorage("opening_blance", '.opening_blance', 'checkbox');
                // getStorage("credit_amount", '.credit_amount', 'checkbox');
                // getStorage("closing_blance", '.closing_blance', 'checkbox');
                // getStorage("separate_salse_return_blance", '.separate_salse_return_blance', 'checkbox');
                // getStorage("separate_closing_blance", '.separate_closing_blance', 'checkbox');

            }

            function local_store_group_wise_party_ledger_details_set_data() {
                setStorage("end_date", $('.to_date').val());
                setStorage("start_date", $('.from_date').val());
                setStorage("group_id", $('.group_id').val());
                setStorage("ledger_address", $(".ledger_address").is(':checked'));
                setStorage("ledger_alias", $(".ledger_alias").is(':checked'));
                setStorage("opening_blance", $(".opening_blance").is(':checked'));
                setStorage("credit_amount", $(".credit_amount").is(':checked'));
                setStorage("closing_blance", $(".closing_blance").is(':checked'));
                setStorage("separate_salse_return_blance", $(".separate_salse_return_blance").is(':checked'));
                setStorage("separate_closing_blance", $(".separate_closing_blance").is(':checked'));
            }

            function chcking_checkbok() {
                if ($(".ledger_alias").is(':checked') == true) {
                    $(".alias_checkbox").removeClass("d-none");
                } else {
                    $(".alias_checkbox").addClass("d-none");
                }
                if ($(".ledger_address").is(':checked') == true) {
                    $(".address_checkbox").removeClass("d-none");
                } else {
                    $(".address_checkbox").addClass("d-none");
                }

                if ($(".opening_blance").is(':checked') == true) {
                    $(".opening_checkbox").removeClass("d-none");
                } else {
                    $(".opening_checkbox").addClass("d-none");
                }

                if ($(".debit_amount").is(':checked') == true) {
                    $(".debit_checkbox").removeClass("d-none");
                } else {
                    $(".debit_checkbox").addClass("d-none");
                }
                if ($(".credit_amount").is(':checked') == true) {
                    $(".credit_checkbox").removeClass("d-none");
                } else {
                    $(".credit_checkbox").addClass("d-none");
                }

                if ($(".separate_closing_blance").is(':checked') == true) {
                    $(".closing_debit_check").removeClass("d-none");
                } else {
                    $(".closing_debit_check").addClass("d-none");
                }
                if ($(".closing_blance").is(':checked') == true) {
                    $(".closing_checkbox").removeClass("d-none");
                } else {
                    $(".closing_checkbox").addClass("d-none");
                }
                if ($(".separate_salse_return_blance").is(':checked') == true) {
                    $('.credit_checkbox_balance').attr('colspan', 2);
                    $('.credit_checkbox_balance').removeAttr('rowspan');
                    $(".credit_checkbox_separete").removeClass("d-none");
                } else {
                    $('.credit_checkbox_balance').attr('rowspan', (2));
                    $('.credit_checkbox_balance').removeAttr('colspan', (2));
                    $(".credit_checkbox_separete").addClass("d-none");
                }
                if ($(".separate_closing_blance").is(':checked') == true) {
                    $('.closing_checkbox_balance').attr('colspan', 2);
                    $('.closing_checkbox_balance').removeAttr('rowspan');
                    $(".closing_checkbox_separete").removeClass("d-none");

                } else {
                    $('.closing_checkbox_balance').attr('rowspan', (2));
                    $('.closing_checkbox_balance').removeAttr('colspan', (2));
                    $(".closing_checkbox_separete").addClass("d-none");
                }
            }

            $(document).ready(function() {
                // table header fixed
                let display_height = $(window).height();
                $('.tableFixHead_ledger_group_wise').css('height', `${display_height-115}px`);
            });
        </script>
    @endpush
@endsection
