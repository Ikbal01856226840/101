@extends('layouts.backend.app')
@section('title','Group Wise Party Ledger Credit Limit')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
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
        text-align:right;
        font-family: Arial, sans-serif;

    }

    .table-scroll thead tr:nth-child(2) th {
        top: 30px;
    }
    body{
        overflow: auto !important;
    }
</style>
@endpush
@section('admin_content')<br>
<!-- add component-->
@component('components.report', [
    'title' => 'Group Wise Party Ledger Credit Limit',
    'print_layout'=>'portrait',
    'print_header'=>'Group Wise Party Ledger Credit Limit',
    'user_privilege_title'=>'GroupWisePartyLedger',
    'print_date'=>1,
]);

<!-- Page-header component -->
@slot('header_body')
<form id="group_wise_party_form" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row ">
        <div class="col-md-3">
            <label>Accounts Group :</label>
            <select name="group_id" class="form-control  js-example-basic-single  group_id" required>
                <option value="">--Select--</option>
                {!!html_entity_decode($group_chart_data)!!}
            </select>
        </div>
        <div class="col-md-3">
            <label></label>
            <div class="form-group mb-0" style="position: relative">
                <input class="form-check-input separate_closing_blance" type="checkbox"  name="separate_closing_blance"   >
                <label class="form-check-label fs-6" for="flexRadioDefault1" >
                    Separate Closing Balance
                </label>
            </div>
            <div class="form-group m-0 p-0" style="position: relative">
                <input class="form-check-input opening_blance" type="checkbox" name="opening_blance" value="1" checked="checked">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Opening Balance
                </label>
                <input class="form-check-input  closing_blance" type="checkbox" name="closing_blance" value="1" checked="checked">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Closing Balance
                </label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row  m-0 p-0">
                <div class="col-md-6 m-0 p-0">
                    <label>Date From: </label>
                    <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{$form_date?? date('Y-m-d')}}">
                </div>
                <div class="col-md-6 m-0 p-0">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{$to_date?? date('Y-m-d') }}">
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <label></label><br>
            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;"><span class="m-1 m-t-1"></span><span>Search</span></button>
        </div>
    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_report">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <tr>
                <th rowspan="2" class="td" style="width: 1%; ">SL.</th>
                <th rowspan="2" class="td" style="width: 3%;">Particulars</th>
                <th rowspan="2" style="width: 2%;" class="td opening_checkbox">Opening Balance</th>
                <th colspan="3" style="width: 3%;" class="td text-center">Sales Amount</th>
                <th colspan="4" style="width: 2%;" class="td text-center">Credit Amount</th>
                <th  style="width: 3%; " class="td closing_checkbox closing_check">Closing Balance</th>
                <th rowspan="2" style="width: 3%;" class=" td alias_checkbox">Credit Limit</th>
                <th rowspan="2" style="width: 3%;" class=" td ">Balance</th>
            </tr>
            <tr>
                <th style="width: 3%;" class="td text-end">Journal</th>
                <th style="width: 3%;" class="td text-end">Sales</th>
                <th style="width: 3%;" class="td text-end">Total</th>
                <th style="width: 3%;" class="td text-end">Journal</th>
                <th style="width: 3%;" class="td text-end">Sales Return</th>
                <th style="width: 3%;" class="td text-end">Collection</th>
                <th style="width: 3%;" class="td text-end">Total</th>
                <th class="td closing_debit_check text-end">Debit</th>
                <th class="td closing_debit_check text-end">Credit</th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>

                <th style="width: 1%;;" class="td"></th>
                <th style="width: 3%;" class="td">Total :</th>
                <th style="width: 2%;  font-size: 18px;" class="td total_opening opening_checkbox text-end"></th>
                <th style="width: 3%;  font-size: 18px;" class="td total_journal_debit text-end"></th>
                <th style="width: 3%;  font-size: 18px;" class="td total_debit text-end"></th>
                <th style="width: 3%;  font-size: 18px;" class="td total_dedit_sum text-end"></th>
                <th style="width: 2%;  font-size: 18px;" class="td total_journal_credit text-end"></th>
                <th style="width: 3%;  font-size: 18px;" class="td total_sales_return_group_credit text-end"></th>
                <th style="width: 2%;  font-size: 18px;" class="td total_credit credit_checkbox text-end "></th>
                <th style="width: 3%;  font-size: 18px;" class="td total_credit_sum text-end"></th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;font-size: 18px;"  class=" closing_checkbox debit_closing_val closing_debit_check text-end"></th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;font-size: 18px;"  class="total_clasing closing_credit_check text-end"></th>
                <th style="width: 2%;  font-size: 18px;" class="td"></th>
                <th style="width: 3%;  font-size: 18px;" class="td"></th>


            </tr>
        </tfoot>
    </table>
</div>
@endslot
@endcomponent
<br>
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script type="text/javascript" src="{{asset('dist/jquery-simple-tree-table.js')}}"></script>
<script>
    var amount_decimals = "{{company()->amount_decimals}}";
    let total_opening = 0;
    total_debit = 0;
    total_credit = 0;
    total_journal_debit = 0;
    total_journal_credit = 0;
    total_sales_return_group_credit = 0;
    total_clasing = 0;
    i = 1;
    drcr_closing_sign='';
    // group chart  id check

    // group wise  party ledger quantity
    $(document).ready(function() {
        // get party ledger
        function get_group_party_ledger_initial_show() {
            print_date();
            $(".modal").show();
            $.ajax({
                url: '{{ route("group-wise-party-ledger-get-data") }}',
                method: 'GET',
                data: {
                    to_date: $('.to_date').val(),
                    from_date: $('.from_date').val(),
                    group_id: $(".group_id").val(),
                },
                dataType: 'json',
                success: function(response) {
                    $('.item_body').empty();
                    $(".modal").hide();
                    get_group_wise_party_ledger(response)
                },
                error: function(data, status, xhr) {}
            });
        }

        // group chart get id check
        $("#group_wise_party_form").submit(function(e) {
            print_date();
            $(".modal").show();
            total_opening = 0;
            total_debit = 0;
            total_credit = 0;
            total_journal_debit = 0;
            total_journal_credit = 0;
            total_sales_return_group_credit = 0;
            total_clasing = 0;
            i = 1;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("group-wise-party-ledger-credit-limit-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_group_wise_party_ledger(response)
                },
                error: function(data, status, xhr) {
                    Unauthorized(data.status);
                }
            });
        });
        checkbox_check();
        // group wise party ledger function
        function get_group_wise_party_ledger(response) {
            const children_sum = calculateSumOfChildren(response.data);
            var tree = getTreeView(response.data, children_sum);
            $('.item_body').html(tree);
            get_hover();
            $('.total_opening').text(Math.abs(total_opening)?.formatBangladeshCurrency());
            $('.total_journal_debit').text(Math.abs(total_journal_debit)?.formatBangladeshCurrency());
            $('.total_debit').text(Math.abs(total_debit)?.formatBangladeshCurrency());
            $('.total_dedit_sum').text(Math.abs(total_journal_debit + total_debit)?.formatBangladeshCurrency());
            $('.total_journal_credit').text(Math.abs(total_journal_credit)?.formatBangladeshCurrency());
            $('.total_sales_return_group_credit').text(Math.abs(total_sales_return_group_credit)?.formatBangladeshCurrency());
            $('.total_credit').text(Math.abs(total_credit)?.formatBangladeshCurrency());
            $('.total_credit_sum').text(Math.abs(total_journal_credit + total_credit + total_sales_return_group_credit)?.formatBangladeshCurrency());
            $('.total_clasing').text(Math.abs(total_clasing)?.formatBangladeshCurrency());

            //checking condition
            if ($(".separate_closing_blance").is(':checked')) {
                $('.debit_closing_val').text('');
                $('.closing_credit_check').text('');
                if (drcr_closing_sign == 'Dr') {
                    // Format number with commas for Dr
                    $('.debit_closing_val').text(total_clasing?.formatBangladeshCurrency());
                } else if (drcr_closing_sign == 'Cr') {
                    // Format number with commas for Cr
                    $('.closing_credit_check').text(total_clasing?.formatBangladeshCurrency());
                } else {
                    // Default formatting when no Dr or Cr sign
                    $('.total_clasing').text('');
                }
            } else {
                // If the checkbox is not checked, default to formatting with zero
                $('.total_clasing').text(total_clasing ?.formatBangladeshCurrency(drcr_closing_sign));
            }

            //checking condition
            checkbox_check();
        }
    });

    var result = [];

    // calcucation child summation
    function calculateSumOfChildren(arr) {
        const result = {};

        function sumProperties(obj, prop) {
            return obj.reduce((acc, val) => acc + (val[prop] || 0), 0);
        }

        function processNode(node) {
            if (!result[node.group_chart_id]) {
                result[node.group_chart_id] = {
                    group_chart_id: node.group_chart_id,
                    group_debit: 0,
                    group_credit: 0,
                    op_group_debit: 0,
                    op_group_credit: 0,
                    journal_group_debit: 0,
                    journal_group_credit: 0,
                    sales_return_group_credit: 0
                };
            }

            const currentNode = result[node.group_chart_id];
            currentNode.group_debit += node.group_debit || 0;
            currentNode.group_credit += node.group_credit || 0;
            currentNode.op_group_debit += node.op_group_debit || 0;
            currentNode.op_group_credit += node.op_group_credit || 0;
            currentNode.journal_group_debit += node.journal_group_debit || 0;
            currentNode.journal_group_credit += node.journal_group_credit || 0;
            currentNode.sales_return_group_credit += node.sales_return_group_credit || 0;
            if (node.children) {
                node.children.forEach(processNode);
            }
        }

        arr.forEach(processNode);

        return Object.values(result);
    }

    function getTreeView(arr, children_sum, depth = 0, chart_id = 0) {
        var eol = '<?php echo str_replace(array("\n", "\r"), array('\\n', '\\r'), PHP_EOL) ?>';
        let htmlFragments = [];
        arr.forEach(function(v) {
            a = '&nbsp;';
            h = a.repeat(depth);
            if (chart_id != v.group_chart_id) {
                htmlFragments.push(`<tr id="${v.group_chart_id + '-' + v.under}" class='left left-data group_chart_id table-row table-row_tree'>
                                <td class="td" style='color: #0B55C4'></td>
                                <td class="group_id" style='width: 3%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4'><p style="margin-left:${(h + a + a).length-12}px;font-family: Arial, sans-serif;" class="text-wrap mb-0 pb-0 ">${v.group_chart_name}</p></td>
                              `);
                let matchingChild = children_sum.find(c => v.group_chart_id == c.group_chart_id);
                if (matchingChild) {
                    let total_op_val;
                    let total_op_sign;
                    let total_closing_val;
                    let total_closing_sign;
                    let op_group_debit = matchingChild.op_group_debit || 0;
                    let op_group_credit = matchingChild.op_group_credit || 0;
                    let journal_group_debit = matchingChild.journal_group_debit || 0;
                    let group_credit = matchingChild.group_credit || 0;
                    let group_debit = matchingChild.group_debit || 0;
                    let journal_group_credit = matchingChild.journal_group_credit || 0;
                    let sales_return_group_credit = matchingChild.sales_return_group_credit || 0;
                    if (v.nature_group == 1 || v.nature_group == 3) {
                        total_op_val = (op_group_debit - op_group_credit);
                        total_op_sign = total_op_val >= 0 ? 'Dr' : 'Cr';
                    } else {
                        total_op_val = (op_group_credit - op_group_debit)
                        total_op_sign = total_op_val >= 0 ? 'Cr' : 'Dr';
                    }
                    const totalOpeningBalance = Math.abs(total_op_val)?.formatBangladeshCurrency(total_op_sign);
                    if (v.nature_group == 1 || v.nature_group == 3) {
                        total_closing_val = (op_group_debit - op_group_credit + journal_group_debit + group_debit) - (group_credit + journal_group_credit + sales_return_group_credit);
                        total_closing_sign = total_closing_val >= 0 ? 'Dr' : 'Cr';
                    } else {
                        total_closing_val = (op_group_credit - op_group_debit + group_credit + journal_group_credit + sales_return_group_credit) - (group_debit + journal_group_debit);
                        total_closing_sign = total_closing_val >= 0 ? 'Cr' : 'Dr';
                    }
                    if(total_closing_sign!=drcr_closing_sign){
                                    drcr_closing_sign =total_closing_sign ;
                    }
                    const totalCurrentBalance = Math.abs(total_closing_val)?.formatBangladeshCurrency(total_closing_sign);

                    htmlFragments.push(`<td class="td opening_checkbox text-end" style='font-weight: bold;'>  ${totalOpeningBalance}</td>
                                        <td class="td text-end" style='font-weight: bold;'> ${(journal_group_debit)?.formatBangladeshCurrency()} </td>
                                        <td class="td text-end" style='font-weight: bold;'> ${(group_debit)?.formatBangladeshCurrency()} </td>
                                        <td class="td text-end" style='font-weight: bold;'> ${(journal_group_debit+group_debit)?.formatBangladeshCurrency()} </td>
                                        <td class="td text-end" style='font-weight: bold;'> ${(journal_group_credit)?.formatBangladeshCurrency()} </td>
                                        <td class="td text-end" style='font-weight: bold;'> ${(sales_return_group_credit)?.formatBangladeshCurrency()} </td>
                                        <td class="td text-end" style='font-weight: bold;'> ${(group_credit)?.formatBangladeshCurrency()}</td>
                                        <td class="td text-end" style='font-weight: bold;'> ${(journal_group_credit+group_credit+sales_return_group_credit)?.formatBangladeshCurrency()}</td>
                                        ${$(".separate_closing_blance").is(':checked') ?`<td class="td closing_checkbox text-end" style='font-weight: bold;'> ${total_closing_sign=='Dr'?Math.abs(total_closing_val)?.formatBangladeshCurrency():''}</td>
                                         <td class="td closing_checkbox text-end" style='font-weight: bold;'>${total_closing_sign=='Cr'?Math.abs(total_closing_val)?.formatBangladeshCurrency():''} </td>` : ` <td class="td closing_checkbox text-end" style='font-weight: bold;'>${totalCurrentBalance} </td>`}
                                         <td class="td alias_checkbox" style='color: #0B55C4'></td>
                                         <td class="td alias_checkbox" style='color: #0B55C4'></td>
                                         `);
                                        

                }
                htmlFragments.push(`</tr>`);
                chart_id = v.group_chart_id;
            }
            if (v.ledger_head_id) {
                let opening_val;
                let op_sign;
                let clasing_val;
                let closing_sign;
                if (v.nature_group == 1 || v.nature_group == 3) {
                    opening_val = openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance) + (v.op_total_debit || 0) - (v.op_total_credit || 0);
                    op_sign = opening_val >= 0 ? 'Dr' : 'Cr';
                } else {
                    opening_val = openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance) + (v.op_total_credit || 0) - (v.op_total_debit || 0);
                    op_sign = opening_val >= 0 ? 'Cr' : 'Dr';
                }
                total_opening = total_opening + parseFloat(opening_val);
                const openingBalance = Math.abs(opening_val)?.formatBangladeshCurrency(op_sign);

                total_debit += (v.total_debit || 0);
                total_credit += (v.total_credit || 0);
                total_journal_debit += (v.journal_debit || 0);
                total_journal_credit += (v.journal_credit || 0);
                total_sales_return_group_credit = (v.sales_return_credit || 0);
                if (v.nature_group == 1 || v.nature_group == 3) {
                    clasing_val =openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance) + (((v.op_total_debit || 0) - (v.op_total_credit || 0) + (v.total_debit || 0) + (v.journal_debit || 0)) - (v.total_credit || 0) - (v.journal_credit) - (v.sales_return_credit || 0));
                    closing_sign = clasing_val >= 0 ? 'Dr' : 'Cr';
                } else {
                    clasing_val = openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance) + (((v.op_total_credit || 0) - (v.op_total_debit || 0) + (v.total_credit || 0) + (v.journal_credit) + (v.sales_return_credit || 0)) - (v.total_debit || 0) - (v.journal_debit || 0));
                    closing_sign = clasing_val >= 0 ? 'Cr' : 'Dr';
                }


                total_clasing = parseFloat(total_clasing) + parseFloat(clasing_val);
                const currentBalance = Math.abs(clasing_val)?.formatBangladeshCurrency(closing_sign);
                if (((opening_val || 0) == 0) && ((v.journal_debit || 0) == 0) && ((v.total_debit || 0) == 0) && ((v.journal_credit || 0) == 0) && ((v.sales_return_credit || 0) == 0) && ((v.total_credit || 0) == 0)) {} else {
                    htmlFragments.push(`<tr id="${v.ledger_head_id}" class="table-row table-row_id">
                                <td  style="width: 1%;  border: 1px solid #ddd;">${i++}</td>
                                <td  class="ledger_td" style="width: 5%;  border: 1px solid #ddd;font-size: 16px;"><p style="margin-left:${(h+a+a+a).length-12}px;font-family: Arial, sans-serif;" class="text-wrap mb-0 pb-0">${v.ledger_name}</p></td>
                                <td  class='opening_checkbox td text-end'>${(openingBalance)}</td>
                                <td  class='td text-end'>${(v.journal_debit||0)?.formatBangladeshCurrency()}</td>
                                <td  class='td text-end'>${(v.total_debit||0)?.formatBangladeshCurrency()}</td>
                                <td  class='td text-end'>${((v.total_debit||0)+(v.journal_debit||0))?.formatBangladeshCurrency()}</td>
                                <td  class='td text-end'>${(v.journal_credit||0)?.formatBangladeshCurrency()}</td>
                                <td  class='td text-end'>${(v.sales_return_credit||0)?.formatBangladeshCurrency()}</td>
                                <td  class='td text-end'>${(v.total_credit||0)?.formatBangladeshCurrency()}</td>
                                <td  class='td text-end'>${((v.sales_return_credit||0)+(v.total_credit||0)+(v.journal_credit||0))?.formatBangladeshCurrency()}</td>
                                ${$(".separate_closing_blance").is(':checked') ? `<td class='closing_checkbox text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;'> ${closing_sign=='Dr'?Math.abs(clasing_val)?.formatBangladeshCurrency():''}</td>
                                        <td class='closing_checkbox text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;'>${closing_sign=='Cr'? Math.abs(clasing_val)?.formatBangladeshCurrency():''} </td>` : `<td class='closing_checkbox text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;'>${(currentBalance)}</td>`}
                                <td  class=' td'>${(v.credit_limit||0).formatBangladeshCurrency()}</td>
                                <td  class='td'>${(closing_sign.trim()=="Dr"?((v.credit_limit||0)-clasing_val)?.formatBangladeshCurrency():Math.abs(clasing_val+(v.credit_limit||0))?.formatBangladeshCurrency())}</td>
                            </tr>`);
                }
            }
            if ('children' in v) {
                htmlFragments.push(getTreeView(v.children, children_sum, depth + 1, chart_id));
            }
        });
        return htmlFragments.join('');
    }
    //get  all data show
    $(document).ready(function() {
        //party ledger  wise summary route
        $('.sd').on('click', '.ledger_td', function(e) {
            e.preventDefault();
            let ledger_id = $(this).closest('tr').attr('id');
            let form_date = $('.from_date').val();
            let to_date = $('.to_date').val();
            url = "{{route('party-ledger-id-wise', ['ledger_id' =>':ledger_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
            url = url.replace(':ledger_id', ledger_id);
            url = url.replace(':form_date', form_date);
            url = url.replace(':to_date', to_date);
            window.location = url;
        });
        // group  party ledger wise summary route
        $('.sd').on('click', '.group_id', function(e) {
            e.preventDefault();
            let group_chart_id = $(this).closest('tr').attr('id');
            let form_date = $('.from_date').val();
            let to_date = $('.to_date').val();
            url = "{{route('group-wise-party-ledger-id-wise', ['group_chart_id' =>':group_chart_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
            url = url.replace(':group_chart_id', group_chart_id);
            url = url.replace(':form_date', form_date);
            url = url.replace(':to_date', to_date);
            window.location = url;
        })
    });
    function checkbox_check(){
            //checking condition
            // $(".alias_checkbox").css("display", $(".ledger_alias").is(':checked') == true ? '' : 'none');
            // $(".opening_checkbox").css("display", $(".opening_blance").is(':checked') == true ? '' : 'none');
            // $(".closing_checkbox").css("display", $(".closing_blance").is(':checked') == true ? '' : 'none');
            // $(".closing_credit_check").css("display", $(".closing_blance" ).is(':checked')==true?'':'none');
            // $(".closing_debit_check").css("display", $(".separate_closing_blance" ).is(':checked')==true?'':'none');

            // if($(".ledger_alias").is(':checked')==true){
            //     $(".alias_checkbox").removeClass("d-none");
            // }else{
            //     $(".alias_checkbox").addClass("d-none");
            // }

            if($(".opening_blance").is(':checked')==true){
                $(".opening_checkbox").removeClass("d-none");
            }else{
                $(".opening_checkbox").addClass("d-none");
            }

            if($(".closing_blance").is(':checked')==true){
                $(".closing_checkbox").removeClass("d-none");
                $(".closing_credit_check").removeClass("d-none");
            }else{
                $(".closing_checkbox").addClass("d-none");
                $(".closing_credit_check").addClass("d-none");
            }

            if($(".separate_closing_blance").is(':checked')==true){
                $(".closing_debit_check").removeClass("d-none");
            }else{
                $(".closing_debit_check").addClass("d-none");
            }

            if ($(".separate_closing_blance").is(':checked')) {
                // If the checkbox is checked, set rowspan to 2
                $('.closing_check').attr('colspan', 2);
                $('.closing_check').removeAttr('rowspan'); // Optionally, remove rowspan if it exists
            } else {
                $('.closing_check').attr('rowspan', 2);
                $('.closing_check').removeAttr('colspan');
                // If the checkbox is not checked, set colspan to 2

            }
    }
    $(document).ready(function(){
    // table header fixed
    let display_height=$(window).height();
    $('.tableFixHead_report').css('height',`${display_height-115}px`);
});
</script>
@endpush
@endsection