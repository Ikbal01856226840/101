
@extends('layouts.backend.app')
@section('title','Balance Sheet')
@push('css')
 <!-- model style -->
 <link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
 <style>

 table {width:100%;grid-template-columns: auto auto;}
 .td{
    width: 3%;  border: 1px solid #ddd; font-size: 18px;
    font-family: Arial, sans-serif;
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
body{
    overflow: auto !important;
 }
</style>
@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=>'Balance Sheet',
    'size'=>'modal-xl',
    'page_unique_id'=>35,
    'ledger'=>'yes',
    'title'=>'Balance Sheet',
    'daynamic_function'=>'get_balance_sheet_initial_show'
]);
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Balance Sheet',
    'print_layout'=>'No',
    'print_header'=>'Balance Sheet',
    'user_privilege_title'=>'BalanceSheet',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="balance_sheet_form"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row">
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
<div class="dt-responsive table-responsive cell-border sd balance_sheet_tableFixHead">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers tree table-scroll">
        <thead>
            <tr>
                <th style="width: 3%;" class="th">Particulars</th>
                @if(Auth::user()->id==1)
                    <th style="width: 2%;" class="th opening_checkbox text-end">Opening Balance</th>
                    <th style="width: 3%;" class="th debit_checkbox text-end">Debit Amount</th>
                    <th style="width: 2%;" class="th credit_checkbox text-end">Credit Amount</th>
                @endif
                <th style="width: 3%;" class="th closing_checkbox text-end">Closing Balance</th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body ">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 3%;" class="th">:</th>
                @if(Auth::user()->id==1)
                    <th style="width: 2%;font-size: 18px;"  class="th total_opening opening_checkbox text-end"></th>
                    <th style="width: 3%;font-size: 18px;"  class="th total_debit debit_checkbox text-end"></th>
                    <th style="width: 2%;font-size: 18px;"  class="th total_credit credit_checkbox text-end"></th>
                @endif
                <th style="width: 3%;font-size: 18px;"  class="th total_clasing closing_checkbox text-end"></th>
            </tr>
        </tfoot>
    </table>
    <div class="col-sm-12 text-center footer_class">
    </div>
</div>
@endslot
@endcomponent
<br>
@push('js')
<script>
   
let  total_opening=0; total_debit=0; total_credit=0;total_clasing=0;i=1;total_sales=0;total_purchase=0; total_income=0; total_expance=0;total_clasing=0;profit=0;
const userId="{{Auth::user()->id}}";
$(document).ready(function () {
    
    // get  balance sheet
    function get_balance_sheet_initial_show(){
          total_opening=0; total_debit=0; total_credit=0;total_clasing=0;i=1;total_sales=0;total_purchase=0; total_income=0; total_expance=0;total_clasing=0;profit=0;
            print_date();
            $(".modal").show();
            $.ajax({
                url: '{{ route("balance-sheet-data") }}',
                    method: 'GET',
                    data: {
                        to_date:$('.to_date').val(),
                        from_date:$('.from_date').val(),
                    },
                    dataType: 'json',
                    success: function(response) {
                        $(".modal").hide();
                        get_profit_loss(response);
                        get_balance_sheet(response)
                    },
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
        }
        get_balance_sheet_initial_show();

    $("#balance_sheet_form").submit(function(e) {
          total_opening=0; total_debit=0; total_credit=0;total_clasing=0;i=1;total_sales=0;total_purchase=0; total_income=0; total_expance=0;total_clasing=0;profit=0;
             print_date();
            $(".modal").show();
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("balance-sheet-data") }}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                    $(".modal").hide();
                    get_profit_loss(response);
                    get_balance_sheet(response)
                    },
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
 });

// profit and loss function
function get_profit_loss(response){
        total_sales=0;total_purchase=0; total_income=0; total_expance=0;total_clasing=0;profit=0;
        const children_sum_income= calculateSumOfChildren(response.data.ledger_income);
        const children_sum_sales= calculateSumOfChildren(response.data.ledger_sales);
        const children_sum_purchase= calculateSumOfChildren(response.data.ledger_purchase);
        const children_sum_expenses= calculateSumOfChildren(response.data.ledger_expenses);
        let opening_stock=(response.data.oppening_stock[0].total_stock_total_out_opening||0);
        let closing_stock=(response.data.current_stock_1[0].total_val||0)
        let html = [];
       getTreeView1(response.data.ledger_sales,children_sum_sales);
       
        getTreeViewPurchase1(response.data.ledger_purchase,children_sum_purchase);
        let total_gross_margin=(total_sales||0)-(((total_purchase||0)-(closing_stock||0))+(opening_stock||0));
        

       
        let all_total_gross_margin = total_gross_margin < 0
              ? `(${Math.abs(total_gross_margin).formatBangladeshCurrencyType("accounts")})`
              :total_gross_margin.formatBangladeshCurrencyType("accounts");
         getTreeView1(response.data.ledger_income,children_sum_income);

         let net_income=(total_gross_margin+total_income);
         let net_all_income = net_income < 0
            ? `(${Math.abs(net_income).formatBangladeshCurrencyType("accounts")})`
            : net_income.formatBangladeshCurrencyType("accounts");
        
       getTreeView1(response.data.ledger_expenses,children_sum_expenses);

        profit=(net_income-total_expance);
      
       
     }

// balance sheet function
function get_balance_sheet(response){
   
        const children_sum= calculateSumOfChildren(response.data.assets);
        total_opening=0; total_debit=0; total_credit=0;total_clasing=0;
        const children_sum_liabilities= calculateSumOfChildren(response.data.liabilities);
        let closing_stock=(response.data.current_stock[0]?.total_val||0);
        let html = [];
        html.push(`<tr left left-data editIcon> <td   colspan="1" style='width: 3%;  border: 1px solid #ddd; font-size: 25px; color: #0B55C4'>Assets</td></tr>`);
        html.push(getTreeView(response.data.assets,children_sum,closing_stock));

        html.push(`<tr left left-data editIcon>
                    <td style='width: 3%;  border: 1px solid #ddd; font-size: 20px;'>Total Assets</td>
                    ${userId=='1'?`
                        <td style="font-weight: bold" class="td text-end">${(Math.abs(total_opening||0)).formatBangladeshCurrencyType("accounts",'',(total_opening >= 0 ? 'Dr' : 'Cr'))}</td>
                        <td style="font-weight: bold" class="td text-end">${((total_debit||0)||0).formatBangladeshCurrencyType("accounts")}</td>
                        <td style="font-weight: bold" class="td text-end">${(total_credit||0).formatBangladeshCurrencyType("accounts")}</td>
                    `:``}
                    <td style="font-weight: bold" class="td text-end">${(Math.abs(total_clasing||0)).formatBangladeshCurrencyType("accounts",'',(total_clasing >= 0 ? 'Dr' : 'Cr'))}</td>
                    </tr>`);
                    html.push(`<tr left left-data editIcon> <td   colspan="1" style='width: 3%;  border: 1px solid #ddd; font-size: 25px; color: #0B55C4'>Liabilities</td></tr>`);
                    total_opening=0; total_debit=0; total_credit=0;total_clasing=0;
                    html.push(getTreeView(response.data.liabilities,children_sum_liabilities,closing_stock));
        // console.log('total_clasing',total_clasing);
        html.push(`<tr left left-data editIcon>
                    <td class="td">Total Liabilities</td>
                    ${userId=='1'?`
                        <td style="font-weight: bold" class="td text-end">${(total_opening||0).formatBangladeshCurrencyType("accounts",'',(total_opening >= 0 ? 'Cr' : 'Dr'))}</td>
                        <td style="font-weight: bold" class="td text-end">${((total_debit||0)||0).formatBangladeshCurrencyType("accounts")}</td>
                        <td style="font-weight: bold"  class="td text-end">${(total_credit||0).formatBangladeshCurrencyType("accounts")}</td>
                    `:``}
                    <td style="font-weight: bold" class="td text-end">${(total_clasing||0).formatBangladeshCurrencyType("accounts",'',(total_clasing >= 0 ? 'Cr' : 'Dr'))}</td>
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

function getTreeView(arr, children_sum,closing_stock, depth = 0, chart_id = 0,group=0,under_id='') {
    let htmlFragments = [];
    let under_unique=0;
    arr.forEach(function (v) {
        a = '&nbsp;&nbsp;';
        h = a.repeat(depth);

                if (chart_id != v.group_chart_id) {
                    if(group==v.under){
                        if(under_unique!=v.under){
                            under_id+=' '+v.under
                            under_unique=v.under;

                        }
                    }
                    htmlFragments.push(`<tr id="${v.group_chart_id + '-' + v.under}" class='${under_id} left left-data editIcon ${group==v.under?'tree-node':''}'   data-id='${v.group_chart_id}' data-parent='${v.under}' > <td   style='width: 3%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4'><span class="group_chart">${h + a + v.group_chart_name}</span>
                        <span>
                           ${v.ledger_head_id?'<i class="fa fa-angle-double-down" style="font-size: x-large"></i>':v.children?'<i class="fa fa-angle-double-down" style="font-size: x-large"></i>':''}
                            <i class="fa fa-angle-double-up"  aria-hidden="true" style="display: none;font-size: x-large"></i>
                        </span>
                        </td>`);

                    let matchingChild = children_sum.find(c =>v.group_chart_id == c.group_chart_id);
                    if (matchingChild) {
                                let total_op_val;
                                let total_op_sign;
                                let total_closing_val;
                                let total_closing_sign;
                                if(v.nature_group == 1 || v.nature_group == 3){
                                    total_op_val=((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0));
                                    total_op_sign = total_op_val >= 0 ? 'Dr' : 'Cr';
                                }else{
                                    total_op_val=((matchingChild.op_group_credit || 0) -(matchingChild.op_group_debit || 0))
                                    total_op_sign = total_op_val >= 0 ? 'Cr' : 'Dr';
                                }
                                const totalOpeningBalance = Math.abs(total_op_val||0).formatBangladeshCurrencyType("accounts",'',total_op_sign);
                                if(v.group_chart_id==7){
                                    if(v.nature_group == 1 || v.nature_group == 3){
                                        total_closing_val=(((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0) + ((matchingChild.group_debit || 0)+parseFloat(closing_stock||0))) - ((matchingChild.group_credit|| 0)));
                                        total_closing_sign = total_closing_val >= 0 ? 'Dr' : 'Cr';
                                    }
                                }else if(v.group_chart_id==12){
                                        total_closing_val=parseFloat(closing_stock||0);
                                        total_closing_sign = total_closing_val >= 0 ? 'Dr' : 'Cr';
                                }else if(v.group_chart_id==36){
                                    total_closing_val=(((matchingChild.op_group_credit || 0) - (matchingChild.op_group_debit || 0) + ((matchingChild.group_credit || 0)+parseFloat(profit||0))) - ((matchingChild.group_debit|| 0)));;
                                    total_closing_sign = total_closing_val >= 0 ? 'Cr' : 'Dr';
                                    
                                    
                                }
                                else{
                                    if(v.nature_group == 1 || v.nature_group == 3){
                                    total_closing_val=(((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0) + (matchingChild.group_debit)) - ((matchingChild.group_credit|| 0)));
                                    total_closing_sign = total_closing_val >= 0 ? 'Dr' : 'Cr';
                                    }else{
                                        total_closing_val=((matchingChild.op_group_credit || 0) - (matchingChild.op_group_debit || 0) + (matchingChild.group_credit)) - ((matchingChild.group_debit || 0)+(matchingChild.sales_return_debit || 0));
                                        total_closing_sign = total_closing_val >= 0 ? 'Cr' : 'Dr';
                                    }
                                }


                         let totalCurrentBalance = Math.abs(total_closing_val).formatBangladeshCurrencyType("accounts",'',total_closing_sign);

                         if(userId=='1'){
                            htmlFragments.push(`<td class="td text-end" style="font-weight: bold">  ${totalOpeningBalance}</td>
                                            <td class="td text-end" style="font-weight: bold"> ${((matchingChild.group_debit || 0)).formatBangladeshCurrencyType("accounts")} </td>
                                            <td class="td text-end" style="font-weight: bold"> ${((matchingChild.group_credit || 0)).formatBangladeshCurrencyType("accounts")}</td>
                                            `);
                         }
                         htmlFragments.push(`<td class="td text-end" style="font-weight: bold"> ${totalCurrentBalance}</td>
                                            `);



                        }

                    htmlFragments.push(`</tr>`);
                    chart_id = v.group_chart_id;
                }

                if (v.ledger_head_id||v.group_chart_id==12) {
                        let op_sign;
                        let  op_val;
                        let clasing_val;
                        let closing_sign;

                        if(v.nature_group == 1 || v.nature_group == 3){
                            op_val=openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance)+((v.op_total_debit || 0) - (v.op_total_credit || 0));
                            op_sign = op_val >= 0 ? 'Dr' : 'Cr';
                        }else{
                            op_val=openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance)+((v.op_total_credit || 0) - (v.op_total_debit || 0));
                            op_sign = op_val >= 0 ? 'Cr' : 'Dr';
                        }
                        let opening_blance = Math.abs(op_val||0).formatBangladeshCurrencyType("accounts",'',op_sign);

                        total_opening+=(op_val||0);
                        total_debit += (v?.total_debit || 0);
                        total_credit += (v?.total_credit || 0);


                        if(v.group_chart_id==12){
                            clasing_val=parseFloat(closing_stock||0); 
                        }else if(v.ledger_head_id==5338&&v.ledger_head_id!=6){
                            clasing_val=parseFloat(profit||0);
                            closing_sign= profit >= 0 ? 'Cr' : 'Dr';
                            htmlFragments.push(`<tr id="${v.ledger_head_id}" id="" class="${under_id} left left-data  table-row tree-node ledger_id" data-id_data-parent="${v.under}" data-parent="${v.group_chart_id}" >
                                                <td style='width: 3%;  border: 1px solid #ddd; font-size: 16px;color: #0B55C4'><p style="margin-left:${(h + a + a + a).length}px" class="text-wrap mb-0 pb-0 ">${v.ledger_name}</p></td>
                                                ${userId=='1'?`<td  class='td opening text-end'> </td>
                                                <td class='td text-end' </td>
                                                <td class='td text-end'></td>`:``}
                                                <td class='td text-end'> ${profit.formatBangladeshCurrencyType("accounts",'',closing_sign)} </td>
                                        </tr>`);
                                        
                        }else{
                            if(v.nature_group == 1 || v.nature_group == 3){
                                clasing_val=openning_blance_cal(v.nature_group,v.DrCr,(v?.opening_balance||0))+(((v.op_total_debit || 0) - (v.op_total_credit || 0) + (v.total_debit || 0)) - (v.total_credit || 0));
                                closing_sign=clasing_val >= 0 ? 'Dr' : 'Cr';
                            }else{
                                clasing_val=openning_blance_cal(v.nature_group,v.DrCr,(v?.opening_balance||0))+(((v.op_total_credit || 0) - (v.op_total_debit || 0) + (v.total_credit || 0)) - (v.total_debit || 0));
                                closing_sign=clasing_val >= 0 ? 'Cr' : 'Dr';
                            }

                        }
                        
                        let closing_blance = Math.abs(clasing_val||0).formatBangladeshCurrencyType("accounts",'',closing_sign);
                        // console.log(total_clasing,parseFloat(total_clasing),parseFloat(clasing_val),total_clasing,clasing_val);
                        total_clasing=(parseFloat(total_clasing)+parseFloat(clasing_val));
                        if (((op_val|| 0) == 0) && ((v.total_debit || 0) == 0)&& ((v.total_credit || 0) == 0) ) {} else {
                           htmlFragments.push(`<tr id="${v.ledger_head_id}" class="${under_id} left left-data  table-row tree-node ledger_id" data-id_data-parent="${v.under}" data-parent="${v.group_chart_id}" >
                                                <td style='width: 3%;  border: 1px solid #ddd; font-size: 16px;color: #0B55C4'><p style="margin-left:${(h + a + a + a).length}px" class="text-wrap mb-0 pb-0 ">${v.ledger_name}</p></td>
                                                ${userId=='1'?`<td  class='td opening text-end'>${(opening_blance)} </td>
                                                <td class='td text-end'>${((v.total_debit || 0)).formatBangladeshCurrencyType("accounts")} </td>
                                                <td class='td text-end'>${((v.total_credit || 0)).formatBangladeshCurrencyType("accounts")} </td>
                                                `:``}<td class='td text-end'> ${closing_blance}</td>
                                        </tr>`);
                        }
                }

            if ('children' in v) {
                ch=v.children;
                htmlFragments.push(getTreeView(v.children, children_sum,closing_stock, depth + 1, chart_id,v.group_chart_id,under_id));

            }

    });

    return htmlFragments.join('');
}
// calcucation child summation
function calculateSumOfChildren1(arr) {
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

function getTreeView1(arr,children_sum,depth = 0, chart_id = 0,group=0,under_id='') {
    
    let under_unique=0;
    arr.forEach(function (v) {
       
       
                if (chart_id != v.group_chart_id) {
                    if(group==v.under){
                        if(under_unique!=v.under){
                            under_id+=' '+v.under
                            under_unique=v.under;
                        }
                    }
                    let matchingChild = children_sum.find(c =>v.group_chart_id == c.group_chart_id);
                    if (matchingChild) {
                            // calcucation
                            let closing_val=v.nature_group == 1 || v.nature_group == 3 ? (((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0) + (matchingChild.group_debit || 0)) - (matchingChild.group_credit || 0)) : (v.nature_group == 2 || v.nature_group == 4 ? ((matchingChild.op_group_credit || 0) - (matchingChild.op_group_debit || 0) + (matchingChild.group_credit || 0)) - (matchingChild.group_debit || 0) : 0)
                            if(v.group_chart_id==35){
                                total_sales=parseFloat(closing_val);
                            }else if(v.group_chart_id==30||v.group_chart_id==31){
                                total_expance=parseFloat(total_expance)+parseFloat(closing_val);
                            }else if(v.group_chart_id==33||v.group_chart_id==34){
                                total_income=parseFloat(total_income)+parseFloat(closing_val);
                            }
                          
                        }
                    chart_id = v.group_chart_id;
                }
                if (v.ledger_head_id) {
                        let clasing= parseFloat(v.opening_balance||0) + (v.nature_group == 1 || v.nature_group == 3 ? (((v.op_total_debit || 0) - (v.op_total_credit || 0) + (v.total_debit || 0)) - (v.total_credit || 0)) : (v.nature_group == 2 || v.nature_group == 4 ? ((v.op_total_credit || 0) - (v.op_total_debit || 0) + (v.total_credit || 0)) - (v.total_debit || 0) : 0));
                        total_clasing =parseFloat(total_clasing)+parseFloat(clasing)
                    if (((v.op_total_debit|| 0) == 0) && ((v.op_total_credit || 0) == 0)&& ((v.total_debit || 0) == 0)&& ((v.total_credit || 0) == 0) ) {} else {
                        
                    }
                }

            if ('children' in v) {
                getTreeView(v.children, children_sum, depth + 1, chart_id,v.group_chart_id,under_id);
            }

    });

}

function getTreeViewPurchase1(arr,children_sum,depth = 0, chart_id = 0,group=0,under_id='') {
  
    let under_unique=0;
    arr.forEach(function (v) {
       
                if (chart_id != v.group_chart_id) {
                    if(group==v.under){
                        if(under_unique!=v.under){
                            under_id+=' '+v.under
                            under_unique=v.under;
                        }
                    }
                    let matchingChild = children_sum.find(c =>v.group_chart_id == c.group_chart_id);
                    if (matchingChild) {
                            // calcucation
                            let closing_val=v.nature_group == 1 || v.nature_group == 3 ? (((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0) + (matchingChild.group_debit || 0)) - (matchingChild.group_credit || 0)) : (v.nature_group == 2 || v.nature_group == 4 ? ((matchingChild.op_group_credit || 0) - (matchingChild.op_group_debit || 0) + (matchingChild.group_credit || 0)) - (matchingChild.group_debit || 0) : 0)
                            if(v.group_chart_id==32){
                                total_purchase=parseFloat(closing_val);
                            }
                        }
                    chart_id = v.group_chart_id;
                }
                if (v.ledger_head_id) {
                    let clasing= parseFloat(v.opening_balance||0) + (v.nature_group == 1 || v.nature_group == 3 ? (((v.op_total_debit || 0) - (v.op_total_credit || 0) + (v.total_debit || 0)) - (v.total_credit || 0)) : (v.nature_group == 2 || v.nature_group == 4 ? ((v.op_total_credit || 0) - (v.op_total_debit || 0) + (v.total_credit || 0)) - (v.total_debit || 0) : 0));
                        total_clasing =parseFloat(total_clasing)+parseFloat(clasing);
                    if (((v.op_total_debit|| 0) == 0) && ((v.op_total_credit || 0) == 0)&& ((v.total_debit || 0) == 0)&& ((v.total_credit || 0) == 0) ) {} else {
                        
                    }
                }
            if ('children' in v) {
                getTreeViewPurchase(v.children, children_sum, depth + 1, chart_id,v.group_chart_id,under_id);
            }

    });
}
//get  all data show
$(document).ready(function () {
    // party ledger  wise summary route
    $('.sd').on('click','.ledger_id',function(e){
        e.preventDefault();
        let ledger_id=$(this).closest('tr').attr('id');
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        url = "{{route('account-ledger-monthly-summary-id-wise', ['ledger_id' =>':ledger_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':ledger_id',ledger_id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        window.open(url, '_blank');
    });

   // ledger voucher route
    $('.sd').on('click','tbody tr .group_chart',function(e){
        e.preventDefault();
        let  group_chart_id=$(this).closest('tr').attr('id');
       
      if(group_chart_id=='12-7'){
        let  type=2;
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        url = "{{route('report-stock-group-summary-profit-value', ['type' =>':type', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':type',type);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        window.open(url, '_blank');
      }else{
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        url = "{{route('account-group-summary-id-wise', ['group_chart_id' =>':group_chart_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':group_chart_id',group_chart_id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        window.open(url, '_blank');
      }

    });

     // table header fixed
     let display_height=$(window).height();
    $('.balance_sheet_tableFixHead').css('height',`${display_height-120}px`);
});
</script>

<script>
    // expand and collapse
    document.addEventListener('DOMContentLoaded', function() {
      const dataTable = document.getElementById('tableId');

      dataTable.addEventListener('click', function(e) {
        const target = e.target;

        const tr = target.closest('tr');

        $(tr).find('i:eq(1)').toggle('show');
        $(tr).find('i:eq(0)').toggle('show');

        if (tr) {
            const nodeId = tr.dataset.id;
            const childNodes = document.querySelectorAll(`tr[data-parent="${nodeId}"]`);
            let key=0;
            childNodes.forEach(node => {
                node.classList.toggle('show');
            });
            const childNodes1 = document.getElementsByClassName(`${nodeId}`);
            [...childNodes1]?.forEach(node => {
                if(node.classList.contains('show')){
                    let parent=node.getAttribute("data-parent");
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
