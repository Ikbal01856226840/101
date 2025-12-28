@extends('layouts.backend.app')
@section('title','Accounts Group Analysis With Sales Type')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">

<style>
    
    .th {
        border: 1px solid #ddd;
        font-weight: bold;
    }

    .td1 {
        border: 1px solid #ddd;
        /* font-size: 18px; */
        font-family: Arial, sans-serif;
        font-weight: bold;
    }
    
    .td2 {
        border: 1px solid #ddd;
        /* font-size: 16px; */
        font-family: Arial, sans-serif;
        font-weight: bold;

    }

    .credit_sales_style {
        min-width: 153px;
    }

    .sales_return_style {
        min-width: 153px;
        ;
    }

    .rate_style {
        min-width: 180px;
    }

    .cash_sales_style {
        min-width: 153px;
    }


    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 18px !important;
    }

    ;

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        padding: 0px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        padding: 0px !important;

    }

    body {
        overflow: auto !important;
    }
</style>
@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
'id' =>'exampleModal',
'class' =>'modal fade',
'size'=>'modal-xl',
'page_title'=>'Accounts Group Analysis With Sales Type',
'page_unique_id'=>10,
'godown'=>'yes',
'groupChart'=>'yes',
'stockGroup'=>'yes',
'title'=>'Accounts  Analysis',
'daynamic_function'=>'account_group_analysis_with_sales_type_intial_show',
])
@endcomponent
<!-- add component-->
@component('components.report', [
'title' => 'Accounts Group Analysis With Sales Type',
'print_layout'=>'landscape',
'print_header'=>'Accounts Group Analysis With Sales Type',
'user_privilege_title'=>'AccountsGroupAnalysis',
'print_date'=>1,
'report_setting_model'=>'report_setting_model',
'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
<form id="add_account_group_analysis_with_sales_type" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row ">
        <div class="col-md-4">
            <label>Stock Group :</label>
            <select name="stock_group_id" class="form-control  js-example-basic-single  stock_group_id" required>
                <option value="">--Select--</option>
                <option value="0">Primary</option>
                {!!html_entity_decode($stock_group)!!}
            </select>
            <label>Accounts Group:</label>
            <select name="group_id" class="form-control  js-example-basic-single  group_id" required>
                <option value="">--Select--</option>
                {!!html_entity_decode($group_chart_data)!!}
            </select>
        </div>
        <div class="col-md-3">
            <label>Godowns :</label>
            <select name="godown_id[]" class="form-control js-example-basic-multiple godown_id" multiple="multiple">
                <option value="0" selected>All</option>
                @foreach($godowns as $godown)
                <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                @endforeach
            </select>
            <div class="row px-2">
                <div class="col-md-6">
                    <label>Date From: </label>
                    <input type="text" name="from_date" class="form-control setup_date from_date" value="{{financial_end_date(date('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control setup_date to_date" value="{{financial_end_date(date('Y-m-d'))}}">
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <label></label>
            <div class="form-group mb-0" style="position: relative">
            <label class="fs-6 rate_style">Sales Type: </label>
            <label class="fs-6 ">Eff. Rate: </label>
            </div>
            <div class="form-group mb-0" style="position: relative">
               
                <input class="form-check-input  salse_credit" type="checkbox" name="salse_credit" value="1" checked="checked">
                <label class="fs-6 credit_sales_style">Credit Sales </label>
                <input class="form-check-input salse_credit_rate" type="checkbox" name="salse_credit_rate" value="1" checked="checked">
                <label class="fs-6 credit_credit_style">Eff. Rate</label>
            </div>
            <div class="form-group mb-0" style="position: relative">
                <input class="form-check-input salse_cash" type="checkbox" name="salse_cash" value="1" checked="checked">
                <label class="fs-6 cash_sales_style">Cash Sales </label>
                <input class="form-check-input salse_cash_rate" type="checkbox" name="salse_cash_rate" value="1" checked="checked">
                <label class="fs-6">Eff. Rate</label>
            </div>

            <div class="form-group mb-0" style="position: relative">
                <input class="form-check-input salse_internal" type="checkbox" name="salse_internal" value="1" checked="checked">
                <label class="fs-6 ">Inter Company Sales </label>
                <input class="form-check-input salse_internal_rate" type="checkbox" name="salse_internal_rate" value="1" checked="checked">
                <label class="fs-6">Eff. Rate</label>

            </div>
            <div class="form-group mb-0" style="position: relative">
                <input class="form-check-input salse_return" type="checkbox" name="salse_return" value="1" checked="checked">
                <label class="fs-6 sales_return_style">Sales Return </label>
                <input class="form-check-input salse_return_rate" type="checkbox" name="salse_return_rate" value="1" checked="checked">
                <label class="fs-6 ">Eff. Rate</label>
            </div>
        </div>
        <div class="col-md-1">
           <br class="br">
           <br class="br">
           <br class="br">
           <br class="br">
            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit p-1 " style=" width:200px; margin-bottom:5px;"><span class="m-1 m-t-1"></span><span>Search</span></button>
        </div>
     </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_report">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <thead>
                <tr>
                    <th rowspan="2" style="width: 1%;" class="text-center th align-middle ">SL.</th>
                    <th rowspan="2" style="width: 5%; table-layout: fixed;" class="text-center th align-middle ">Particulars</th>
                    <th colspan="3" style=" width: 5%; text-align:center;" class="th salse_credit_row credit_col">Credit Sales</th>
                    <th colspan="3" style=" width: 5%; text-align:center;" class="th salse_cash_row cash_col">Cash Sales</th>
                    <th colspan="3" style=" width: 5%; text-align:center;" class="th salse_internal_row internal_col">Inter Company Sales</th>
                    <th colspan="3" style=" width: 5%; text-align:center;" class="th salse_return_row return_col">Sales Return</th>
                    <th colspan="3" style=" width: 5%; text-align:center;" class="th">Net Sales</th>
                </tr>
                <tr>
                    <th style="width: 2%;  overflow: hidden;" class="th salse_credit_row">Quantity</th>
                    <th style="width: 2%;  overflow: hidden;" class="th salse_credit_row salse_credit_rate_row">Rate</th>
                    <th style="width: 2%;  overflow: hidden;" class="th salse_credit_row">Value</th>
                    <th style="width: 2%;  overflow: hidden;" class="th salse_cash_row">Quantity</th>
                    <th style="width: 2%;  overflow: hidden;" class="th salse_cash_row salse_cash_rate_row">Rate</th>
                    <th style="width: 2%;  overflow: hidden;" class="th salse_cash_row">Value</th>
                    <th style="width: 2%;  overflow: hidden;" class="th salse_internal_row">Quantity</th>
                    <th style="width: 2%;  overflow: hidden;" class="th salse_internal_row salse_internal_rate_row">Rate</th>
                    <th style="width: 2%;  overflow: hidden;" class="th salse_internal_row">Value</th>
                    <th style="width: 2%;  overflow: hidden;" class="th salse_return_row">Quantity</th>
                    <th style="width: 2%;  overflow: hidden;" class="th salse_return_row salse_return_rate_row">Rate</th>
                    <th style="width: 2%;  overflow: hidden;" class="th salse_return_row">Value</th>
                    <th style="width: 2%;  overflow: hidden;" class="th">Quantity</th>
                    <th style="width: 2%;  overflow: hidden;" class="th">Rate</th>
                    <th style="width: 2%;  overflow: hidden;" class="th">Value</th>
                </tr>
            </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;" class="th"></th>
                <th style="width: 3%;" class="th">Total :</th>
                <th style="width: 2%; " class="total_stock_qty_sales_credit th salse_credit_row"></th>
                <th style="width: 3%; " class="total_stock_qty_sales_credit_rate th salse_credit_row salse_credit_rate_row"></th>
                <th style="width: 2%; " class="total_sales_credit_value th salse_credit_row"></th>
                <th style="width: 3%; " class="total_stock_qty_sales_cash th salse_cash_row"></th>
                <th style="width: 2%; " class="total_sales_cash_rate th salse_cash_row salse_cash_rate_row"></th>
                <th style="width: 3%; " class="total_sales_cash_value th salse_cash_row"></th>
                <th style="width: 2%; " class="total_stock_qty_inter_company_sales th salse_internal_row"></th>
                <th style="width: 3%; " class="total_stock_qty_inter_company_sales_rate th salse_internal_row salse_internal_rate_row"></th>
                <th style="width: 2%; " class="total_inter_company_sales_value th salse_internal_row"></th>
                <th style="width: 2%; " class="total_qty_sales_return_total th salse_return_row"></th>
                <th style="width: 3%; " class="total_qty_sales_return_total_rate th salse_return_row salse_return_rate_row"></th>
                <th style="width: 2%; " class="total_sales_return_value th salse_return_row"></th>
                <th style="width: 2%; " class="total_net_sales_qty th "></th>
                <th style="width: 3%; " class="total_net_sales_rate th"></th>
                <th style="width: 2%; " class="total_net_sales_value th"></th>

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
<script>
    let i = 1;
    let total_stock_qty_sales_cash = 0;
    total_sales_cash_value = 0;
    total_stock_qty_sales_credit = 0;
    total_sales_credit_value = 0;
    total_stock_qty_inter_company_sales = 0, total_inter_company_sales_value = 0, total_qty_sales_return_total = 0, total_sales_return_value = 0;


    // group  analysis
    $(document).ready(function() {
        
            local_store_sales_type_get();
           
            if($('.stock_group_id').val()){
                account_group_analysis_with_sales_type_intial_show();
           }
        
        $("#add_account_group_analysis_with_sales_type").submit(function(e) {
            local_store_sales_type_set_data();
            $(".modal").show();
            print_date();
            total_stock_qty_sales_cash = 0;
            total_sales_cash_value = 0;
            total_stock_qty_sales_credit = 0;
            total_sales_credit_value = 0, i = 1, total_stock_qty_inter_company_sales = 0, total_inter_company_sales_value = 0, total_qty_sales_return_total = 0, total_sales_return_value = 0;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{route("report-account-group-analysis-with-sales-type-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    account_group_analysis_with_sales_type(response);
                },
                error: function(data, status, xhr) {
                    Unauthorized(data.status);
                }
            });
        });

        
    });

     // stock group analysis function
     function account_group_analysis_with_sales_type(response) {
            const children_sum = calculateSumOfChildren(response.data);

            var tree = getTreeView(response.data, children_sum);
            $('.item_body').html(tree);
            get_hover();
            let total_net_sales_qty = (total_stock_qty_sales_cash + total_stock_qty_sales_credit + total_stock_qty_inter_company_sales + total_qty_sales_return_total);
            let total_net_sales_amount = (total_sales_cash_value + total_sales_credit_value + total_inter_company_sales_value + total_sales_return_value);
            $('.total_stock_qty_sales_cash').text(total_stock_qty_sales_cash.formatBangladeshCurrencyType("quantity"));
            $('.total_sales_cash_rate').text((((total_sales_cash_value || 0) / (total_stock_qty_sales_cash || 0)) || 0).formatBangladeshCurrencyType("rate"));
            $('.total_sales_cash_value').text(total_sales_cash_value.formatBangladeshCurrencyType("amount"));
            $('.total_stock_qty_sales_credit').text(total_stock_qty_sales_credit.formatBangladeshCurrencyType("quantity"));
            $('.total_stock_qty_sales_credit_rate').text((((total_sales_credit_value || 0) / (total_stock_qty_sales_credit || 0)) || 0).formatBangladeshCurrencyType("rate"));
            $('.total_sales_credit_value').text(total_sales_credit_value.formatBangladeshCurrencyType("amount"));
            $('.total_stock_qty_inter_company_sales').text(total_stock_qty_inter_company_sales.formatBangladeshCurrencyType("quantity"));
            $('.total_stock_qty_inter_company_sales_rate').text((((total_inter_company_sales_value || 0) / (total_stock_qty_inter_company_sales || 0)) || 0).formatBangladeshCurrencyType("rate"));
            $('.total_inter_company_sales_value').text(total_inter_company_sales_value.formatBangladeshCurrencyType("amount"));
            $('.total_qty_sales_return_total').text(Math.abs(total_qty_sales_return_total).formatBangladeshCurrencyType("quantity"));
            $('.total_qty_sales_return_total_rate').text((((total_sales_return_value || 0) / (total_qty_sales_return_total || 0)) || 0).formatBangladeshCurrencyType("rate"));
            $('.total_sales_return_value').text(Math.abs(total_sales_return_value).formatBangladeshCurrencyType("amount"));
            $('.total_net_sales_qty').text(total_net_sales_qty.formatBangladeshCurrencyType("quantity"));
            $('.total_net_sales_rate').text((((total_net_sales_amount || 0) / (total_net_sales_qty || 0)) || 0).formatBangladeshCurrencyType("rate"));
            $('.total_net_sales_value').text(total_net_sales_amount.formatBangladeshCurrencyType("amount"));

        }

    // calcucation child summation
    function calculateSumOfChildren(arr) {
        const result = {};

        function sumProperties(obj, prop) {
            return obj.reduce((acc, val) => acc + (val[prop] || 0), 0);
        }

        function processNode(node) {
            if (!result[node.stock_group_id]) {
                result[node.stock_group_id] = {
                    stock_group_id: node.stock_group_id,
                    stock_qty_sales_cash: 0,
                    stock_qty_sales_credit: 0,
                    stock_qty_inter_company_sales: 0,
                    stock_qty_sales_return: 0,
                    stock_total_sales_cash: 0,
                    stock_total_sales_credit: 0,
                    stock_total_inter_company_sales: 0,
                    stock_total_sales_return: 0

                };
            }
            const currentNode = result[node.stock_group_id];
            currentNode.stock_qty_sales_cash += node.stock_qty_sales_cash || 0;
            currentNode.stock_qty_sales_credit += node.stock_qty_sales_credit || 0;
            currentNode.stock_qty_inter_company_sales += node.stock_qty_inter_company_sales || 0;
            currentNode.stock_qty_sales_return += node.stock_qty_sales_return || 0;
            currentNode.stock_total_sales_cash += node.stock_total_sales_cash || 0;
            currentNode.stock_total_sales_credit += node.stock_total_sales_credit || 0;
            currentNode.stock_total_inter_company_sales += node.stock_total_inter_company_sales || 0;
            currentNode.stock_total_sales_return += node.stock_total_sales_return || 0;
            if (node.children) {
                node.children.forEach(processNode);
            }
        }

        arr.forEach(processNode);

        return Object.values(result);
    }

    function getTreeView(arr, children_sum, depth = 0, chart_id = 0) {
        let html = [];
        let salse_credit=$('.salse_credit').is(':checked'); 
        let salse_credit_rate=$('.salse_credit_rate').is(':checked');       
        if (salse_credit) {
            $(".salse_credit_row").removeClass("d-none");
            if (salse_credit_rate) {
                $('.credit_col').attr('colspan',3);
                $(document).find(".salse_credit_rate_row").removeClass("d-none");
            } else {
                $(document).find(".salse_credit_rate_row").addClass("d-none");
                $('.credit_col').attr('colspan',2);
            }
        } else {
            $(".salse_credit_row").addClass("d-none");
        }

        let salse_cash=$('.salse_cash').is(':checked');
        let salse_cash_rate=$('.salse_cash_rate').is(':checked');        
        if (salse_cash) {
            $(".salse_cash_row").removeClass("d-none");
            if (salse_cash_rate) {
                $('.cash_col').attr('colspan',3);
                $(document).find(".salse_cash_rate_row").removeClass("d-none");
            } else {
                $(document).find(".salse_cash_rate_row").addClass("d-none");
                $('.cash_col').attr('colspan',2);
            }
        } else {
            $(".salse_cash_row").addClass("d-none");
        }

        let salse_internal=$('.salse_internal').is(':checked');
        let salse_internal_rate=$('.salse_internal_rate').is(':checked');        
        if (salse_internal) {
            $(".salse_internal_row").removeClass("d-none");
            if (salse_internal_rate) {
                $('.internal_col').attr('colspan',3);
                $(document).find(".salse_internal_rate_row").removeClass("d-none");
            } else {
                $(document).find(".salse_internal_rate_row").addClass("d-none");
                $('.internal_col').attr('colspan',2);
            }
        } else {
            $(".salse_internal_row").addClass("d-none");
        }


        let salse_return=$('.salse_return').is(':checked');
        let salse_return_rate=$('.salse_return_rate').is(':checked');        
        if (salse_return) {
            $(".salse_return_row").removeClass("d-none");
            if (salse_return_rate) {
                $('.return_col').attr('colspan',3);
                $(document).find(".salse_return_rate_row").removeClass("d-none");
            } else {
                $(document).find(".salse_return_rate_row").addClass("d-none");
                $('.return_col').attr('colspan',2);
            }
        } else {
            $(".salse_return_row").addClass("d-none");
        }


        arr.forEach(function(v) {
            a = '&nbsp;';
            h = a.repeat(depth);

            if (chart_id != v.stock_group_id) {
                let matchingChild = children_sum.find(c => v.stock_group_id == c.stock_group_id);
                if (((matchingChild.stock_qty_sales_cash || 0) == 0) && ((matchingChild.stock_qty_sales_credit || 0) == 0) && ((matchingChild.stock_qty_inter_company_sales || 0) == 0) && ((matchingChild.stock_qty_sales_return || 0) == 0)) {} else {
                    html.push(`<tr id="${v.stock_group_id+'-'+v.under}" class="left left-data table-row_tree">
                       <td style='width: 1%;  border: 1px solid #ddd;'></td>
                      <td style='width: 3%;color: #0B55C4' class="td1"><p style="margin-left:${(h+a+a).length-12}px;" class="text-wrap mb-0 pb-0 ">${v.stock_group_name}</p></td>`);


                    if (matchingChild) {
                        let net_qty_group = ((matchingChild.stock_qty_sales_cash || 0) + (matchingChild.stock_qty_sales_credit || 0) + (matchingChild.stock_qty_inter_company_sales || 0) + (matchingChild.stock_qty_sales_return || 0)) || 0;
                        let net_value_group = ((matchingChild.stock_total_sales_cash || 0) + (matchingChild.stock_total_sales_credit || 0) + (matchingChild.stock_total_inter_company_sales || 0) + (matchingChild.stock_total_sales_return || 0)) || 0;
                        html.push(`
                            ${salse_credit?`
                                <td style='width: 3%;'class="td1">
                                    ${(matchingChild.stock_qty_sales_credit||0).formatBangladeshCurrencyType("quantity")}
                                </td>
                                ${salse_credit_rate?
                                    `<td style='width: 3%;'class="td1">
                                        ${(Math.abs((matchingChild.stock_total_sales_credit||0)/(matchingChild.stock_qty_sales_credit||0))||0).formatBangladeshCurrencyType("rate")}
                                    </td>`:''
                                }
                                <td style='width: 3%'class="td1">
                                    ${(matchingChild.stock_total_sales_credit||0).formatBangladeshCurrencyType("amount")}
                                </td>
                            `:''}

                            ${salse_cash?`
                                <td style='width: 3%;'class="td1">
                                    ${((matchingChild.stock_qty_sales_cash||0)).formatBangladeshCurrencyType("quantity")}
                                </td>
                                ${salse_cash_rate?`
                                    <td style='width: 3%;'class="td1">
                                        ${((Math.abs(matchingChild.stock_total_sales_cash||0)/Math.abs(matchingChild.stock_qty_sales_cash||0))||0).formatBangladeshCurrencyType("rate")}
                                    </td>
                                `:''}
                                <td style='width: 3%;'class="td1">
                                    ${(((matchingChild.stock_total_sales_cash||0)).formatBangladeshCurrencyType("amount"))}
                                </td>
                            `:''}

                            ${salse_internal?`
                                <td style='width: 3%'class="td1">
                                    ${(matchingChild.stock_qty_inter_company_sales||0).formatBangladeshCurrencyType("quantity")}
                                </td>
                                ${salse_internal_rate?`
                                    <td style='width: 3%;'class="td1">
                                        ${(Math.abs((matchingChild.stock_total_inter_company_sales||0)/(matchingChild.stock_qty_inter_company_sales||0))||0).formatBangladeshCurrencyType("rate")}
                                    </td>
                                `:''}
                                <td style='width: 3%;'class="td1">
                                    ${(matchingChild.stock_total_inter_company_sales||0).formatBangladeshCurrencyType("amount")}
                                </td>
                            `:''}
                            ${salse_return?` 
                                <td style='width: 3%;'class="td1">
                                    ${(Math.abs(matchingChild.stock_qty_sales_return||0)).formatBangladeshCurrencyType("quantity")}
                                </td>
                                ${salse_return_rate?`
                                    <td style='width: 3%;'class="td1">
                                        ${(Math.abs((matchingChild.stock_total_sales_return||0)/(matchingChild.stock_qty_sales_return||0))||0).formatBangladeshCurrencyType("rate")}
                                    </td>
                                `:''}
                                <td style='width: 3%;'class="td1">
                                    ${(Math.abs(matchingChild.stock_total_sales_return||0)).formatBangladeshCurrencyType("amount")}
                                </td>
                            `:''}
                            <td style='width: 3%;'class="td1">${(net_qty_group||0).formatBangladeshCurrencyType("quantity")}</td>
                            <td style='width: 3%;'class="td1">${(Math.abs(net_value_group/net_qty_group)||0).formatBangladeshCurrencyType("rate")}</td>
                            <td style='width: 3%;'class="td1">${(net_value_group).formatBangladeshCurrencyType("amount")}</td>
                            </tr>`);

                    }
                }
                chart_id = v.stock_group_id;
            }


            total_stock_qty_sales_cash += (v.stock_qty_sales_cash_total || 0);
            total_sales_cash_value += (v.stock_total_sales_cash_value || 0);

            total_stock_qty_sales_credit += (v.stock_qty_sales_credit_total || 0);
            total_sales_credit_value += (v.stock_total_sales_credit_value || 0);

            total_stock_qty_inter_company_sales += (v.stock_qty_inter_company_sales_total || 0);
            total_inter_company_sales_value += (v.stock_total_inter_company_sales_value || 0);


            total_qty_sales_return_total += (v.stock_qty_sales_return_total || 0);

            total_sales_return_value += (v.stock_total_sales_return_value || 0);


            let net_qty = ((v.stock_qty_sales_cash_total || 0) + (v.stock_qty_sales_credit_total || 0) + (v.stock_qty_inter_company_sales_total || 0) + (v.stock_qty_sales_return_total || 0));
            let net_vale = ((v.stock_total_sales_cash_value || 0) + (v.stock_total_sales_credit_value || 0) + (v.stock_total_inter_company_sales_value || 0) + (v.stock_total_sales_return_value || 0));

            if (((v.stock_qty_sales_cash_total || 0) == 0) && ((v.stock_qty_sales_credit_total || 0) == 0) && ((v.stock_qty_inter_company_sales_total || 0) == 0) && ((v.stock_total_sales_return_value || 0) == 0)) {} else {
                html.push(`<tr id="${v.stock_item_id}" class="left left-data editIcon table-row">
                        <td class="sl" style="width: 1%;  border: 1px solid #ddd;">${i++}</td>
                        <td style="width: 5%;" class="td2"><p style="margin-left:${(h+a+a).length-12}px" class="text-wrap mb-0 pb-0">${v.product_name}</p></td>
                        ${salse_credit?`
                            <td style='width: 3%;'class="td2">
                                ${(((v.stock_qty_sales_credit_total||0)).formatBangladeshCurrencyType("quantity",v.symbol))}
                            </td>
                            ${salse_credit_rate?
                                `<td style='width: 3%;'class="td2">
                                    ${(Math.abs((v.stock_total_sales_credit_value||0)/(v.stock_qty_sales_credit_total||0))||0).formatBangladeshCurrencyType("rate")}
                                </td>`:''
                            }
                            <td style='width: 3%;'class="td2">
                                ${(((v.stock_total_sales_credit_value||0)).formatBangladeshCurrencyType("amount"))}
                            </td>
                        `:''}

                        ${salse_cash?`
                            <td style='width: 3%;'class="td2">
                                ${((v.stock_qty_sales_cash_total||0)).formatBangladeshCurrencyType("quantity",v.symbol)}
                            </td>
                            ${salse_cash_rate?`
                                <td style='width: 3%;'class="td2">
                                    ${((Math.abs(v.stock_total_sales_cash_value||0)/Math.abs(v.stock_qty_sales_cash_total||0))||0).formatBangladeshCurrencyType("rate")}
                                </td>
                            `:''}
                            <td style='width: 3%;'class="td2">
                                ${(((v.stock_total_sales_cash_value||0)).formatBangladeshCurrencyType("amount"))}
                            </td>
                        `:''}
                        ${salse_internal?`
                            <td style='width: 3%;'class="td2">
                                ${(((v.stock_qty_inter_company_sales_total||0)).formatBangladeshCurrencyType("quantity",v.symbol))}
                            </td>
                            ${salse_internal_rate?`
                                <td style='width: 3%;'class="td2">
                                    ${(Math.abs((v.stock_total_inter_company_sales_value||0)/(v.stock_qty_inter_company_sales_total||0))||0).formatBangladeshCurrencyType("rate")}
                                </td>
                            `:''}
                            <td style='width: 3%;'class="td2">
                                ${(((v.stock_total_inter_company_sales_value||0)).formatBangladeshCurrencyType("amount"))}
                            </td>
                        `:''}
                        ${salse_return?`  
                            <td style='width: 3%;'class="td2">
                                ${((Math.abs(v.stock_qty_sales_return_total||0)).formatBangladeshCurrencyType("quantity",v.symbol))}
                            </td>
                            ${salse_return_rate?`
                                <td style='width: 3%;'class="td2">
                                    ${(Math.abs((v.stock_total_sales_return_value||0)/(v.stock_qty_sales_return_total||0))||0).formatBangladeshCurrencyType("rate")}
                                </td>
                            `:''}
                            <td style='width: 3%;'class="td2">
                                ${((Math.abs(v.stock_total_sales_return_value||0)).formatBangladeshCurrencyType("amount"))}
                            </td>
                        `:''}
                        <td style='width: 3%;'class="td2">${(((net_qty||0)).formatBangladeshCurrencyType("quantity",v.symbol))}</td>
                        <td style='width: 3%;'class="td2">${(Math.abs((net_vale||0)/(net_qty||0))||0).formatBangladeshCurrencyType("rate")}</td>
                        <td style='width: 3%;'class="td2">${(((net_vale||0)).formatBangladeshCurrencyType("amount"))}</td>
                   </tr>`);
            }

            if ('children' in v) {
                html.push(getTreeView(v.children, children_sum, depth + 1, chart_id));
            }
        });
        return html.join("");
    }
    
    $(document).ready(function() {
        // table header fixed
        let display_height = $(window).height();
        $('.tableFixHead_report').css('height', `${display_height-115}px`);
    });

    $(window).on('resize', function() {
        font_size_auto_change();
    });

    $(window).trigger('resize');

    function font_size_auto_change() {
        // Get the current window width
        var windowWidth = $(window).width();
        // For example, let's make the font size 2% of the window width
        var newFontSize = windowWidth * 0.008;
        if (windowWidth > 768) {
            // Apply the new font size to the target element(s)
            $(document).find('table').css({
                'font-size': `${Math.round(newFontSize)}px`
            });
        }
    };

    function account_group_analysis_with_sales_type_intial_show(){
        total_stock_qty_sales_cash = 0;
        total_sales_cash_value = 0;
        total_stock_qty_sales_credit = 0;
        total_sales_credit_value = 0, i = 1, total_stock_qty_inter_company_sales = 0, total_inter_company_sales_value = 0, total_qty_sales_return_total = 0, total_sales_return_value = 0;
        $(".modal").show();
        print_date();
        $.ajax({
                url: '{{route("report-account-group-analysis-with-sales-type-data") }}',
                method: 'GET',
                data: {
                    to_date:$('.to_date').val(),
                    from_date:$('.from_date').val(),
                    stock_group_id:$('.stock_group_id').val(),
                    group_id:$('.group_id').val(),
                    godown_id: $('.godown_id').val(),
                },
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    account_group_analysis_with_sales_type(response);
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
    }

    function local_store_sales_type_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("stock_group_id", '.stock_group_id');
        getStorage("group_id", '.group_id');
        let godown = getStorage("godown");
        if (godown) {
            $('.godown_id').val(godown.split(",")).trigger('change');
        }
    }

    function local_store_sales_type_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("stock_group_id", $('.stock_group_id').val());
        setStorage("group_id", $('.group_id').val());
        setStorage("godown", $('.godown_id').val());
        
    }

    function checkbox_check() {

    }
</script>
@endpush
@endsection