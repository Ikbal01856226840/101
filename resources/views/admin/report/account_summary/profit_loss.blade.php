
@extends('layouts.backend.app')
@section('title','Profit & Loss')
@push('css')
 <!-- model style -->
 <link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
 <style>
 table {width:100%;grid-template-columns: auto auto;}
 .td{
    width: 3%;  border: 1px solid #ddd; font-size: 16px;
    font-family: Arial, sans-serif;
    font-weight: bold;
 }
 .th{
    border: 1px solid #ddd;font-weight: bold;
    font-family: Arial, sans-serif;
 }

 .table-scroll thead tr:nth-child(2) th {
    top: 30px;
}

.tree-node {
      display: none;
}

.tree-node.show {
    display: table-row;
}
</style>
@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=>'LProfit & Loss',
    'size'=>'modal-xl',
    'page_unique_id'=>37,
    'ledger'=>'yes',
    'title'=>'Profit & Loss',
    'daynamic_function'=>'get_ledger_daily_summary_initial_show'
]);
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Profit & Loss',
    'print_layout'=>'portrait',
    'print_header'=>'Profit & Loss',
    'user_privilege_title'=>'ProfitLoss',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="profit_loss_form"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row ">
            <div class="col-md-6">
                <div class="row  m-0 p-0">
                    <div class="col-md-6 m-0 p-0">
                        <label>Date From: </label>
                        <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{$form_date?? company()->financial_year_start }}">
                    </div>
                    <div class="col-md-6 m-0 p-0">
                        <label>Date To : </label>
                        <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{$to_date?? financial_end_date(date('Y-m-d')) }}">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label></label><br>
                <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
            </div>
        </div>
    </form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_report">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers tree table-scroll">
        <thead>
            <tr>
                <th style="width: 3%;" class="th">Particulars</th>
                <th style="width: 2%;" class="th opening_checkbox"></th>
                <th style="width: 3%;" class="th debit_checkbox"></th>
                <th style="width: 3%;" class="th closing_checkbox"></th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body ">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 3%;" class="th"></th>
                <th style="width: 2%;font-size: 18px;"  class="th total_opening opening_checkbox"></th>
                <th style="width: 3%;font-size: 18px;"  class="th total_debit debit_checkbox"></th>
                <th style="width: 3%;font-size: 18px;"  class="th total_clasing closing_checkbox"></th>
            </tr>
        </tfoot>
    </table>
    <div class="col-sm-12 text-center hide-btn">
        <span><b>Copyright &copy; 2014-2022 <a href="http://www.hamko-ict.com/">Hamko-ICT.</a> All rights
                reserved.</b></span>
    </div>
</div>
@endslot
@endcomponent
<br>
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>
var amount_decimals="{{company()->amount_decimals}}";
let  total_sales=0;total_purchase=0; total_income=0; total_expance=0;i=1,total_clasing=0;

$(document).ready(function () {

    $("#profit_loss_form").submit(function(e) {
        print_date();
        $(".modal").show();
        e.preventDefault();
        const fd = new FormData(this);
        $.ajax({
            url: '{{ route("profit-loss-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                get_profit_loss(response)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
    });

 function get_profit_and_loss_initial_show(){
        print_date();
        $(".modal").show();
        $.ajax({
            url: '{{ route("profit-loss-data") }}',
                method: 'GET',
                data: {
                    to_date:$('.to_date').val(),
                    from_date:$('.from_date').val(),
                },
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_profit_loss(response)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
   }
 get_profit_and_loss_initial_show();

// profit and loss function
function get_profit_loss(response){
        total_sales=0;total_purchase=0; total_income=0; total_expance=0;total_clasing=0;
        const children_sum_income= calculateSumOfChildren(response.data.ledger_income);
        const children_sum_sales= calculateSumOfChildren(response.data.ledger_sales);
        const children_sum_purchase= calculateSumOfChildren(response.data.ledger_purchase);
        const children_sum_expenses= calculateSumOfChildren(response.data.ledger_expenses);
        let opening_stock=(response.data.oppening_stock[0].total_stock_total_out_opening||0);
        let closing_stock=(response.data.current_stock[0].total_val||0)
        let html = [];
        html.push(getTreeView(response.data.ledger_sales,children_sum_sales));
        html.push(`<tr left left-data editIcon>
                        <td class="td"></td>
                        <td  class="opening_val" style='width: 3%;  border: 1px solid #ddd; font-size: 18px; color: #0B55C4'>Opening Stock</td>
                        <td   style='width: 3%;  border: 1px solid #ddd; font-size: 18px;'>${(opening_stock||0).formatBangladeshCurrencyType("accounts")}</td>
                        <td class="td"></td>
                    </tr>`);
        html.push(getTreeViewPurchase(response.data.ledger_purchase,children_sum_purchase));
        let total_gross_margin=(total_sales||0)-(((total_purchase||0)-(closing_stock||0))+(opening_stock||0));
        html.push(`<tr left left-data editIcon>
                        <td class="td"></td>
                        <td class="closing_val" style='width: 3%;  border: 1px solid #ddd; font-size: 18px; color: #0B55C4'>Closing Stock</td>
                        <td  style='width: 3%;  border: 1px solid #ddd; font-size: 18px;'>${(closing_stock||0).formatBangladeshCurrencyType("accounts")}</td>
                        <td class="td"></td>
                   </tr>`);

        html.push(`<tr left left-data editIcon>
                        <td class="td"></td>
                        <td class="td" style="font-size: 18px; font-weight: bold;">Cost of Goods Sold (COGS)</td>
                        <td  style='width: 3%;  border: 1px solid #ddd; font-size: 18px; font-weight: bold;'>${(((total_purchase||0)-(closing_stock||0))+(opening_stock||0)).formatBangladeshCurrencyType("accounts")}</td>
                        <td class="td"></td>
                </tr>`);
        let all_total_gross_margin = total_gross_margin < 0
              ? `(${Math.abs(total_gross_margin).formatBangladeshCurrencyType("accounts")})`
              :total_gross_margin.formatBangladeshCurrencyType("accounts");
        html.push(`<tr>
                    <td style="font-size: 18px; font-weight: bold;">Gross Margin</td>
                    <td class="td"></td>
                    <td class="td"></td>
                    <td  style='width: 3%;  border: 1px solid #ddd; font-size: 18px; font-weight: bold;'>${all_total_gross_margin}</td>
                    </tr>`);
         html.push(getTreeView(response.data.ledger_income,children_sum_income));
         let net_income=(total_gross_margin+total_income);
         let net_all_income = net_income < 0
            ? `(${Math.abs(net_income).formatBangladeshCurrencyType("accounts")})`
            : net_income.formatBangladeshCurrencyType("accounts");
         html.push(`<tr left left-data editIcon>
                        <td class="td" style="font-size: 18px; font-weight: bold;">Net Indirect Income</td>
                        <td class="td"></td>
                        <td class="td"></td>
                        <td style='width: 3%;  border: 1px solid #ddd; font-size: 18px; font-weight: bold;'>${net_all_income}</td>
                    </tr>`);
        html.push(getTreeView(response.data.ledger_expenses,children_sum_expenses));

        let profit=(net_income-total_expance);
        html.push(`<tr>
                        <td style="font-size: 18px;font-weight: bold;">${profit < 0? 'Net Loss':'Net Income'}</td>
                        <td class="td"></td>
                        <td class="td"></td>
                        <td style='width: 3%;  border: 1px solid #ddd; font-size: 18px; font-weight: bold;'>${profit < 0?Math.abs(profit).formatBangladeshCurrencyType("accounts"):profit.formatBangladeshCurrencyType("accounts")}</td>
                    </tr>`);
        $('.item_body').html(html.join(''));
     }
});

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
                op_group_credit: 0
            };
        }
        const currentNode = result[node.group_chart_id];
        currentNode. group_debit += node.group_debit || 0;
        currentNode.group_credit += node.group_credit || 0;
        currentNode.op_group_debit += node.op_group_debit || 0;
        currentNode.op_group_credit += node.op_group_credit || 0;
        if (node.children) {
            node.children.forEach(processNode);
        }
    }

    arr.forEach(processNode);

    return Object.values(result);
}

function getTreeView(arr,children_sum,depth = 0, chart_id = 0,group=0,under_id='') {
    let htmlFragments = [];
    let under_unique=0;
    arr.forEach(function (v) {
        a = '&nbsp;&nbsp;&nbsp;&nbsp;';
        h = a.repeat(depth);
                if (chart_id != v.group_chart_id) {
                    if(group==v.under){
                        if(under_unique!=v.under){
                            under_id+=' '+v.under
                            under_unique=v.under;
                        }
                    }

                    htmlFragments.push(`<tr id="${v.group_chart_id + '-' + v.under}" class='${under_id} left left-data editIcon ${group==v.under?'tree-node':''}'data-id='${v.group_chart_id}' data-parent_id='${v.under}' ><td style='width: 3%;  border: 1px solid #ddd; font-size: 16px; color: #0B55C4'><span class="group_chart">${h + a + v.group_chart_name}</span>
                        <span>
                           ${v.ledger_head_id?'<i class="fa fa-angle-double-down" style="font-size: x-large"></i>':v.children?'<i class="fa fa-angle-double-down" style="font-size: x-large"></i>':''}
                            <i class="fa fa-angle-double-up"  aria-hidden="true" style="display: none;font-size: x-large"></i>
                        </span>
                        </td>`);
                    let matchingChild = children_sum.find(c =>v.group_chart_id == c.group_chart_id);
                    if (matchingChild) {
                            // calcucation
                           
                            let total_closing_sign;
                            let closing_val;
                            //=v.nature_group == 1 || v.nature_group == 3 ? (((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0) + (matchingChild.group_debit || 0)) - (matchingChild.group_credit || 0)) : (v.nature_group == 2 || v.nature_group == 4 ? ((matchingChild.op_group_credit || 0) - (matchingChild.op_group_debit || 0) + (matchingChild.group_credit || 0)) - (matchingChild.group_debit || 0) : 0)
                            if(v.nature_group == 1 || v.nature_group == 3){
                                closing_val=(((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0) + (matchingChild.group_debit || 0)) - (matchingChild.group_credit || 0));
                                total_closing_sign =  closing_val >= 0 ? 'Dr' : 'Cr';

                            }else if(v.nature_group == 2 || v.nature_group == 4){
                                closing_val=((matchingChild.op_group_credit || 0) - (matchingChild.op_group_debit || 0) + (matchingChild.group_credit || 0)) - (matchingChild.group_debit || 0);
                                total_closing_sign =  closing_val >= 0 ? 'Cr' : 'Dr';
                            }
                            if(v.group_chart_id==35){
                                total_sales=parseFloat(closing_val);
                            }else if(v.group_chart_id==30||v.group_chart_id==31){
                                total_expance=parseFloat(total_expance)+parseFloat(closing_val);
                            }else if(v.group_chart_id==33||v.group_chart_id==34){
                                total_income=parseFloat(total_income)+parseFloat(closing_val);
                            }

                            htmlFragments.push(`<td class="td" </td>
                                                <td class="td"></td>
                                                <td class="td">${(Math.abs(closing_val||0)).formatBangladeshCurrencyType("accounts",'',total_closing_sign)}</td>
                            </tr>`);
                        }
                    chart_id = v.group_chart_id;
                }
                if (v.ledger_head_id) {
                        let ledger_total_closing_sign
                        let clasing
                        //= (v.nature_group == 1 || v.nature_group == 3 ? (((v.op_total_debit || 0) - (v.op_total_credit || 0) + (v.total_debit || 0)) - (v.total_credit || 0)) : (v.nature_group == 2 || v.nature_group == 4 ? ((v.op_total_credit || 0) - (v.op_total_debit || 0) + (v.total_credit || 0)) - (v.total_debit || 0) : 0));
                           if(v.nature_group == 1 || v.nature_group == 3){
                                clasing=(((v.op_total_debit || 0) - (v.op_total_credit || 0) + (v.total_debit || 0)) - (v.total_credit || 0));
                                ledger_total_closing_sign =  clasing >= 0 ? 'Dr' : 'Cr';

                            }else if(v.nature_group == 2 || v.nature_group == 4){
                                clasing=((v.op_total_credit || 0) - (v.op_total_debit || 0) + (v.total_credit || 0)) - (v.total_debit || 0);
                                ledger_total_closing_sign =  clasing >= 0 ? 'Cr' : 'Dr';
                            }
                        total_clasing =parseFloat(total_clasing)+parseFloat(clasing)
                    if (((v.op_total_debit|| 0) == 0) && ((v.op_total_credit || 0) == 0)&& ((v.total_debit || 0) == 0)&& ((v.total_credit || 0) == 0) ) {} else {
                        htmlFragments.push(`<tr id="${v.ledger_head_id}" class="${under_id} left left-data  table-row tree-node ledger_id" data-id_data-parent="${v.under}" data-parent_id="${v.group_chart_id}">
                                                <td style='width: 3%;  border: 1px solid #ddd; font-size: 16px; color: #0B55C4'><p style="margin-left:${(h + a + a + a).length}px" class="text-wrap mb-0 pb-0 ">${v.ledger_name}</p></td>
                                                <td class='td'></td>
                                                <td class="td"></td>
                                                <td class='td'>${(Math.abs(clasing||0)).formatBangladeshCurrencyType("accounts",'',ledger_total_closing_sign)}</td>
                                        </tr>`);
                    }
                }

            if ('children' in v) {
                htmlFragments.push(getTreeView(v.children, children_sum, depth + 1, chart_id,v.group_chart_id,under_id));
            }

    });

    return htmlFragments.join('');
}

function getTreeViewPurchase(arr,children_sum,depth = 0, chart_id = 0,group=0,under_id='') {
    let htmlFragments = [];
    let under_unique=0;
    arr.forEach(function (v) {
        a = '&nbsp;&nbsp;&nbsp;&nbsp;';
        h = a.repeat(depth);
                if (chart_id != v.group_chart_id) {
                    if(group==v.under){
                        if(under_unique!=v.under){
                            under_id+=' '+v.under
                            under_unique=v.under;
                        }
                    }

                    htmlFragments.push(`<tr id="${v.group_chart_id +'-'+v.under}" class='${under_id} left left-data editIcon ${group==v.under?'tree-node':''}'data-id='${v.group_chart_id}' data-parent_id='${v.under}' ><td style="width: 3%;  border: 1px solid #ddd; font-size: 16px; color: #0B55C4"></td>`);
                    let matchingChild = children_sum.find(c =>v.group_chart_id == c.group_chart_id);

                    if (matchingChild) {
                            // calcucation
                            let total_closing_sign;
                            let closing_val;
                            //=v.nature_group == 1 || v.nature_group == 3 ? (((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0) + (matchingChild.group_debit || 0)) - (matchingChild.group_credit || 0)) : (v.nature_group == 2 || v.nature_group == 4 ? ((matchingChild.op_group_credit || 0) - (matchingChild.op_group_debit || 0) + (matchingChild.group_credit || 0)) - (matchingChild.group_debit || 0) : 0)
                            if(v.nature_group == 1 || v.nature_group == 3){
                                closing_val=(((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0) + (matchingChild.group_debit || 0)) - (matchingChild.group_credit || 0));
                                total_closing_sign =  closing_val >= 0 ? 'Dr' : 'Cr';

                            }else if(v.nature_group == 2 || v.nature_group == 4){
                                closing_val=((matchingChild.op_group_credit || 0) - (matchingChild.op_group_debit || 0) + (matchingChild.group_credit || 0)) - (matchingChild.group_debit || 0);
                                total_closing_sign =  closing_val >= 0 ? 'Cr' : 'Dr';
                            }

                            //let closing_val=v.nature_group == 1 || v.nature_group == 3 ? (((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0) + (matchingChild.group_debit || 0)) - (matchingChild.group_credit || 0)) : (v.nature_group == 2 || v.nature_group == 4 ? ((matchingChild.op_group_credit || 0) - (matchingChild.op_group_debit || 0) + (matchingChild.group_credit || 0)) - (matchingChild.group_debit || 0) : 0)
                            if(v.group_chart_id==32){
                                total_purchase=parseFloat(closing_val);
                            }

                            htmlFragments.push(`<td style='width: 3%;  border: 1px solid #ddd; font-size: 16px; color: #0B55C4'><span class="group_chart">${h + a + v.group_chart_name}</span>
                                                    <span>
                                                    ${v.ledger_head_id?'<i class="fa fa-angle-double-down" style="font-size: x-large"></i>':v.children?'<i class="fa fa-angle-double-down" style="font-size: x-large"></i>':''}
                                                        <i class="fa fa-angle-double-up"  aria-hidden="true" style="display: none;font-size: x-large"></i>
                                                    </span>
                                                 </td>
                                                <td class="td">${(Math.abs(closing_val||0)).formatBangladeshCurrencyType("accounts",'',total_closing_sign)}</td>
                                                <td class="td"></td>
                                           </tr>`);
                        }
                    chart_id = v.group_chart_id;
                }
                if (v.ledger_head_id) {
                    //let clasing= parseFloat(v.opening_balance||0) + (v.nature_group == 1 || v.nature_group == 3 ? (((v.op_total_debit || 0) - (v.op_total_credit || 0) + (v.total_debit || 0)) - (v.total_credit || 0)) : (v.nature_group == 2 || v.nature_group == 4 ? ((v.op_total_credit || 0) - (v.op_total_debit || 0) + (v.total_credit || 0)) - (v.total_debit || 0) : 0));
                         let ledger_total_closing_sign
                         let clasing
                        //= (v.nature_group == 1 || v.nature_group == 3 ? (((v.op_total_debit || 0) - (v.op_total_credit || 0) + (v.total_debit || 0)) - (v.total_credit || 0)) : (v.nature_group == 2 || v.nature_group == 4 ? ((v.op_total_credit || 0) - (v.op_total_debit || 0) + (v.total_credit || 0)) - (v.total_debit || 0) : 0));
                           if(v.nature_group == 1 || v.nature_group == 3){
                                clasing=(((v.op_total_debit || 0) - (v.op_total_credit || 0) + (v.total_debit || 0)) - (v.total_credit || 0));
                                ledger_total_closing_sign =  clasing >= 0 ? 'Dr' : 'Cr';

                            }else if(v.nature_group == 2 || v.nature_group == 4){
                                clasing=((v.op_total_credit || 0) - (v.op_total_debit || 0) + (v.total_credit || 0)) - (v.total_debit || 0);
                                ledger_total_closing_sign =  clasing >= 0 ? 'Cr' : 'Dr';
                            }
                        total_clasing =parseFloat(total_clasing)+parseFloat(clasing);
                    if (((v.op_total_debit|| 0) == 0) && ((v.op_total_credit || 0) == 0)&& ((v.total_debit || 0) == 0)&& ((v.total_credit || 0) == 0) ) {} else {
                        htmlFragments.push(`<tr id="${v.ledger_head_id}" class="${under_id} left left-data  table-row tree-node ledger_id" data-id_data-parent="${v.under}" data-parent_id="${v.group_chart_id}">
                                                <td class='td'></td>
                                                <td style='width: 3%;  border: 1px solid #ddd; font-size: 16px; color: #0B55C4'><p style="margin-left:${(h + a + a + a).length}px" class="text-wrap mb-0 pb-0 ">${v.ledger_name}</p></td>
                                                <td class='td'>${(Math.abs(clasing||0)).formatBangladeshCurrencyType("accounts",'',ledger_total_closing_sign)}</td>
                                                <td class="td"></td>
                                        </tr>`);
                    }
                }

            if ('children' in v) {
                htmlFragments.push(getTreeViewPurchase(v.children, children_sum, depth + 1, chart_id,v.group_chart_id,under_id));
            }

    });

    return htmlFragments.join('');
}
//get  all data show
$(document).ready(function () {
    // party ledger  wise summary route
    $('.sd').on('click','.ledger_id',function(e){
        e.preventDefault();
        let ledger_id=$(this).closest('tr').attr('id');
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        url = "{{route('account-ledger-voucher-id-wise', ['ledger_id' =>':ledger_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':ledger_id',ledger_id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        window.open(url, '_blank');
    });

   // ledger voucher route
    $('.sd').on('click','tbody tr .group_chart',function(e){
        e.preventDefault();
        let  group_chart_id=$(this).closest('tr').attr('id');
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        url = "{{route('account-group-summary-without-opening-blance-id-wise', ['group_chart_id' =>':group_chart_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':group_chart_id',group_chart_id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        window.open(url, '_blank');
    });
    // ledger voucher route
    $('.sd').on('click','.opening_val,.closing_val',function(e){
        e.preventDefault();
        let  type;
       if($(this).closest('tr').find('.opening_val').text()){
          type=1;
       }else if($(this).closest('tr').find('.closing_val').text()){
         type=2;
       }
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        url = "{{route('report-stock-group-summary-profit-value', ['type' =>':type', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':type',type);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        window.open(url, '_blank');

    })
});
</script>
<script>
    // expand and collapse
    document.addEventListener('DOMContentLoaded', function() {
        const dataTable = document.getElementById('myTable');
        dataTable.addEventListener('click', function(e) {
            const target = e.target;
            const tr = target.closest('tr');
            $(tr).find('i:eq(1)').toggle('show');
            $(tr).find('i:eq(0)').toggle('show');
            if (tr) {
                const nodeId = tr.dataset.id;
                const childNodes = document.querySelectorAll(`tr[data-parent_id="${nodeId}"]`);
                let key=0;
                childNodes.forEach(node => {
                    node.classList.toggle('show');

                });
                const childNodes1 = document.getElementsByClassName(`${nodeId}`);
                [...childNodes1]?.forEach(node => {
                    if(node.classList.contains('show')){
                        let parent=node.getAttribute("data-parent_id");
                        if(key!=parent && parent!=nodeId){
                            document.querySelector(`tr[data-id="${parent}"]`);
                            const childNodes1 = document.querySelector(`tr[data-id="${parent}"]`).click();
                            key=parent;
                        }
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection
