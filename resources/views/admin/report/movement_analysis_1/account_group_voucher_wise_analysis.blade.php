
@extends('layouts.backend.app')
@section('title','Sales & Collection Report')
@push('css')

 <style>
.th{
    border: 1px solid #ddd;font-weight: bold;
}
.td{
        width: 3%;  border: 1px solid #ddd; font-size: 18px;font-weight: bold;text-align:right;
        font-family: Arial, sans-serif;
    }
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 18px !important;
}

@media (max-width: 575.98px) {
    .fns-9 {
        font-size: 11px;
    }
    .col-4{
        margin: 0px;
        padding: 0px;
    }
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
    'size'=>'modal-xl',
    'page_title'=>'Sales & Collection ',
    'page_unique_id'=>134,
    'groupChart'=>'yes',
    'title'=>'Sales & Collection',
    'daynamic_function'=>'get_accounts_group_analysis_initial_show',
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Sales & Collection',
    'print_layout'=>'portrait',
    'print_header'=>'Sales & Collection',
    'user_privilege_title'=>'AccountsGroupVoucherWiseAnalysis',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="add_group_analysis"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-8">
                        <label> Accounts Group :</label>
                        <select name="group_id" class="form-control  js-example-basic-single  group_id" required>
                            <option value="">--Select--</option>
                            {!!html_entity_decode($group_chart_data)!!}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="row px-2">
                            <div class="col-md-6">
                                <label>Date From: </label>
                                <input type="text" name="from_date" class="form-control setup_date from_date" value="{{financial_end_date(date('Y-m-d'))}}">
                            </div>
                            <div class="col-md-6">
                                <label>Date To : </label>
                                <input type="text" name="to_date" class="form-control setup_date to_date" value="{{financial_end_date(date('Y-m-d'))}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="row ps-4">
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input op_qty in_ward_column_rate" type="checkbox" name="rate_in" value="1" checked="checked">
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Debit Column</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input op_rate out_ward_column_rate" type="checkbox" name="rate_out" value="1" checked="checked">
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Credit Column</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4"></div>
                        <div class="col-md-2 col-sm-4 col-4"></div>
                    </div>
                    <div class="row ps-4">
                        <div class="col-md-2 col-sm-4 col-4">
                            <label class="fns-9">Debit Column :</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input sales_in in_qty" type="checkbox" name="out_qty[]" {{ isset($sales_out)?($sales_out==19 ? 'checked' : ''):'' }} value="19"   checked>
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Sales</label>
                        </div>

                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input gtn_in in_qty" type="checkbox" name="out_qty[]" {{ isset($gtn_out)?($gtn_out==23 ? 'checked' : ''):'' }} value="23" >
                            <label class="form-check-label fns-9" for="flexRadioDefault1">GTN</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            @if (company()->sales_return==2)
                              <input class="form-check-input purchase_return_in " type="checkbox" name="purchase_return_in" {{ isset($purchase_return_in)?($purchase_return_in==29 ? 'checked' : ''):'' }} value="29">
                            @else
                              <input class="form-check-input purchase_return_in out_qty" type="checkbox" name="out_qty[]" {{ isset($purchase_return_in)?($purchase_return_in==29 ? 'checked' : ''):'' }} value="29">
                            @endif
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Purchase Return</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input journal_in in_qty" type="checkbox" name="out_qty[]" {{ isset($journal_out)?($journal_out==6 ? 'checked' : ''):'' }} value="6"  >
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Journal</label>
                        </div>
                        <div class="col-md-1 col-sm-2 col-2">
                            <input class="form-check-input payment_in in_qty" type="checkbox" name="out_qty[]" {{ isset($payment)?($payment==8 ? 'checked' : ''):'' }} value="8" >
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Payment</label>
                        </div>
                        <div class="col-md-1 col-sm-2 col-2">
                            <input class="form-check-input commission_in in_qty" type="checkbox" name="out_qty[]" {{ isset($commission)?($commission==8 ? 'checked' : ''):'' }} value="28" >
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Commission</label>
                        </div>
                    </div>
                    <div class="row ps-4">
                        <div class="col-md-2 col-sm-4 col-4">
                            <label class="fns-9">Credit Column :</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input purchase_out out_qty" type="checkbox" name="out_qty[]" {{ isset($purchase_in)?($purchase_in==10 ? 'checked' : ''):'' }} value="10">
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Purchase</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input grn_out out_qty" type="checkbox" {{ isset($grn_in)?($grn_in==24 ? 'checked' : ''):'' }} name="out_qty[]" value="24" >
                            <label class="form-check-label fns-9" for="flexRadioDefault1">GRN</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            @if (company()->sales_return==2)
                              <input class="form-check-input sales_return_out  " type="checkbox" {{ isset($sales_return_out)?($sales_return_out==25 ? 'checked' : ''):'' }} name="sales_return_out" value="25" >
                            @else
                             <input class="form-check-input sales_return_out   out_qty" type="checkbox" {{ isset($sales_return_out)?($sales_return_out==25 ? 'checked' : ''):'' }} name="out_qty[]" value="25">
                            @endif
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Sales Return</label>
                        </div>

                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input journal_out out_qty" type="checkbox" name="out_qty[]" {{ isset($journal_in)?($journal_in==6 ? 'checked' : ''):'' }} value="6"  >
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Journal</label>
                        </div>
                        <div class="col-md-1 col-sm-2 col-2">
                            <input class="form-check-input receipt_out out_qty" type="checkbox" name="out_qty[]" {{ isset($receipt)?($receipt==21 ? 'checked' : ''):'' }} value="14"  {{$receipt ?? "checked"}}>
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Receipt</label>
                        </div>
                         <div  class="col-md-1 col-sm-2 col-2">
                            <input class="form-check-input cash_out out_qty" type="checkbox" name="cash_out" {{ isset($cash_out)?($cash_out==32 ? 'checked' : ''):'' }} value="32"  {{$cash_out ?? "checked"}}>
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Cash Sales</label>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-2 col-sm-12 mt-4 d-flex align-items-start justify-content-center">
                <button type="submit" class="btn btn-primary hor-grd btn-grd-primary w-100 submit" style="max-width: 200px; margin-bottom: 5px;">Search</button>
            </div>
        </div>
    </form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_account_group_voucher_analysis">
    <table id="tableId" style="border-collapse: collapse;" class="table table-striped customers table-scroll">
            <thead>
                <tr>
                <th rowspan="2" style="width: 1%; text-align:center;" class="th align-middle">SL.</th>
                <th rowspan="2" style="width: 5%;" class="th align-middle">Particulars</th>


                <!-- Inward Group -->
                <th colspan="1" style="width: 5%; text-align:center;"
                    class="th inwards_text in_wards" id="inwards_text">
                      Inward : Sales,GTN, Purchase Return, Journal, Payment, Commission
                </th>
               <!-- Outward Group -->
                <th colspan="1" style="width: 5%; text-align:center;"
                    class="th outwards_text out_wards" id="outwards_text">
                    Outward : Purchase,GRN , Sales Return, Journal, Receipt,Cash Sales
                </th>


                <!-- Closing Balance -->
                <th rowspan="2" style="width: 5%; text-align:right;"
                    class="th closing_text">Closing Balance</th>
                </tr>

                <tr>
                <!-- Child columns -->
                <th style="width: 2%; text-align:right;" class="th text-end">Sales</th>
                <th style="width: 2%; text-align:right;" class="th text-end">Collection</th>
                </tr>
            </thead>

            <tbody id="myTable" class="item_body">
                <!-- rows will go here -->
            </tbody>

            <tfoot>
                    <tr>
                    <th style="width: 1%;" class="th"></th>
                    <th style="width: 3%;" class="th text-end">Total :</th>
                    <th style="width: 3%; font-size: 18px; font-weight: bold;"
                        class="total_debit th in_wards text-end"></th>
                    <th style="width: 2%; font-size: 18px; font-weight: bold;"
                        class="total_credit th out_wards text-end"></th>
                    <th style="width: 2%; font-size: 18px; font-weight: bold;"
                        class="total_clasing th closing_text text-end"></th>
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

let  total_debit=0; total_credit=0;i=1;;total_clasing=0;drcr_closing_sign='';

// group  analysis
checkbox_check('','');
$(document).ready(function () {
    local_store_account_group_analysis_get();
     $('.group_id').val("13-7");

    $("#add_group_analysis").submit(function(e) {
        local_store_account_group_analysis_set_data();
        $(".modal").show();
        checkbox_check('','');
        print_date();
       total_debit=0; total_credit=0;i=1;;total_clasing=0;drcr_closing_sign='';
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{route("report-account-group-voucher-wise-analysis-data") }}',
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
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
    });
});



function  getTreeView(arr,children_sum,depth = 0 ,chart_id=0, htmlFragments){
       
        arr.forEach(function (v) {
                a='&nbsp;';
                h=  a.repeat(depth);
                if(v.under!=0){
                    if(chart_id!=v.group_chart_id){
                        let matchingChild = children_sum.find(c =>v.group_chart_id == c.group_chart_id);
                        if ( ((matchingChild.group_debit || 0) == 0) && ((matchingChild.group_credit || 0) == 0)) {} else {
                            htmlFragments.push(`<tr id="${v.group_chart_id + '-' + v.under}" class='left left-data group_chart_id table-row table-row_tree'>
                                <td class="td"></td>
                                <td  class="group_td" style='width: 3%; color: #0B55C4; border: 1px solid #ddd;font-weight: bold;'>
                                <p style="margin-left:${(h + a + a).length-12}px;" class="text-wrap mb-0 pb-0 ">${v.group_chart_name}</p></td>
                              `);

                            if (matchingChild) {
                                let total_closing_val;
                                let total_closing_sign;
                                if(v.nature_group == 1 || v.nature_group == 3){
                                    total_closing_val=(matchingChild.group_debit||0)-(matchingChild.group_credit || 0);
                                    total_closing_sign = total_closing_val >= 0 ? 'Dr' : 'Cr';
                                }else{
                                    total_closing_val=(matchingChild.group_credit)-(matchingChild.group_debit || 0);
                                    total_closing_sign = total_closing_val >= 0 ? 'Cr' : 'Dr';
                                }
                                if(total_closing_sign!=drcr_closing_sign){
                                    drcr_closing_sign =total_closing_sign ;
                                }
                                const totalCurrentBalance = Math.abs(total_closing_val).formatBangladeshCurrencyType("accounts") + ' ' + total_closing_sign;

                                htmlFragments.push(`
                                        <td class="td debit_checkbox text-end" > ${((matchingChild.group_debit||0)).formatBangladeshCurrencyType("accounts")}</td>
                                        <td class="td credit_checkbox text-end" > ${((matchingChild.group_credit || 0)).formatBangladeshCurrencyType("accounts")}</td>
                                        <td class="td closing_checkbox text-end" > ${totalCurrentBalance} </td>
                                    `);

                            }
                            htmlFragments.push(`</tr>`);
                        }
                           chart_id = v.group_chart_id;
                    }
                   if(v.ledger_head_id){

                    let clasing_val;
                    let closing_sign;

                    total_debit += (v.total_debit|| 0);
                    total_credit += (v.total_credit || 0);

                    if(v.nature_group == 1 || v.nature_group == 3){
                        clasing_val=((v.total_debit||0) -(v.total_credit||0));
                        closing_sign=clasing_val >= 0 ? 'Dr' : 'Cr';
                    }else{
                        clasing_val=((v.total_credit || 0))-((v.total_debit || 0));
                        closing_sign=clasing_val >= 0 ? 'Cr' : 'Dr';
                    }
                    total_clasing = parseFloat(total_clasing)+parseFloat(clasing_val);
                    const currentBalance = Math.abs(clasing_val).formatBangladeshCurrencyType("accounts") + ' ' +closing_sign;
                     console.log(currentBalance);
                    if (((v.total_debit || 0) == 0) && ((v.total_credit || 0) == 0) ) {} else {
                        if ($('#show_closing_is').is(':checked')) {
                            if (clasing_val== 0) {} else {
                                party_ledger_data_show(htmlFragments,v,clasing_val,currentBalance)
                            }

                        } else {
                            party_ledger_data_show(htmlFragments,v,clasing_val,currentBalance)
                        }
                    }
                }
            }
                if ('children' in v){
                    htmlFragments.push(getTreeView(v.children,children_sum,depth + 1,chart_id,htmlFragments));
                }
        });
        return htmlFragments;
}


// group wise party ledger function
function get_group_wise_party_ledger(response){
    const children_sum= response.data.sum_of_children;
     // Initialize fragments array
    let htmlFragments = [];
    // Opening Balance row
       htmlFragments.push(`
            <tr >

                <td colspan="2" style="width: 3%;" class="td"></td>
                <td style="width: 3%;" class="td text-end total_sales_amount"></td>
                <td style="width: 3%;" class="td text-end total_collection_amount"></td>
                <td style="width: 3%;" class="td text-end total_closing_amount"></td>
            </tr> `);

       // Cash Transaction row
    $(".cash_out").is(':checked') ?  htmlFragments.push(`
            <tr>

                <td colspan="2" style="width: 3%;" class="td">Cash Transaction</td>
                <td style="width: 3%;" class="td text-end">${(response.data?.cash_sales[0]?.total_debit || 0).formatBangladeshCurrencyType("accounts")}</td>
                <td style="width: 3%;" class="td text-end">${(response.data?.cash_sales[0]?.total_debit || 0).formatBangladeshCurrencyType("accounts")}</td>
                <td style="width: 3%;" class="td text-end">${((response.data?.cash_sales[0]?.total_debit || 0)).formatBangladeshCurrencyType("accounts")}</td>
            </tr>
    `):"";

    // Build tree
    getTreeView(response.data.group_wise_ledger, children_sum, 0, 0, htmlFragments);

    // Inject into DOM
    document.querySelector('#myTable').innerHTML = htmlFragments.join('');
    //    $('.item_body').html(tree);
        get_hover();

            $('.total_debit').text((total_debit+response.data?.cash_sales[0]?.total_debit || 0).formatBangladeshCurrencyType("accounts"));
            $('.total_credit').text(((total_credit+response.data?.cash_sales[0]?.total_debit || 0)).formatBangladeshCurrencyType("accounts"));

            // If the checkbox is not checked, default to formatting with zero
            $('.total_clasing').text(Math.abs((total_clasing|| 0)).formatBangladeshCurrencyType("accounts")+' ' +drcr_closing_sign);

            $('.total_sales_amount').text((total_debit+(response.data?.cash_sales[0]?.total_debit || 0)).formatBangladeshCurrencyType("accounts"));
            $('.total_collection_amount').text((total_credit+(response.data?.cash_sales[0]?.total_debit || 0)).formatBangladeshCurrencyType("accounts"));
            $('.total_closing_amount').text(Math.abs((total_clasing || 0)).formatBangladeshCurrencyType("accounts"));
            $('.closing_credit_checking').text('');

}
function local_store_account_group_analysis_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("group_id", '.group_id');
        getStorage("purchase", '.purchase_in', 'checkbox');
        getStorage("grn", '.grn_in', 'checkbox');
        getStorage("purchase_return", '.purchase_return_in', 'checkbox');
        getStorage("journal_in", '.journal_in', 'checkbox');
        getStorage("payment_in", '.payment_in', 'checkbox');
        getStorage("commission_in", '.commission_in', 'checkbox');
        getStorage("gtn", '.gtn_out', 'checkbox');
        getStorage("sales", '.sales_out', 'checkbox');
        getStorage("sales_return", '.sales_return_out', 'checkbox');
        getStorage("journal_out", '.journal_out', 'checkbox');
        getStorage("receipt_out", '.receipt_out', 'checkbox');
        getStorage("cash_out", '.cash_out', 'checkbox');
        getStorage("in_ward_column", '.in_ward_column','checkbox');
        getStorage("out_ward_column", '.out_ward_column','checkbox');

    }

    function local_store_account_group_analysis_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("group_id", $('.group_id').val());
        setStorage("purchase", $('.purchase_in').is(':checked'));
        setStorage("grn", $('.grn_in').is(':checked'));
        setStorage("purchase_return", $('.purchase_return_in').is(':checked'));
        setStorage("journal_in", $('.journal_in').is(':checked'));
        setStorage("payment_in", $('.payment_in').is(':checked'));
        setStorage("commission_in", $('.commission_in').is(':checked'));
        setStorage("gtn", $('.gtn_out').is(':checked'));
        setStorage("sales", $('.sales_out').is(':checked'));
        setStorage("journal_out", $('.journal_out').is(':checked'));
        setStorage("receipt_out", $('.receipt_out').is(':checked'));
         getStorage("cash_out", $('.cash_out').is(':checked'));
        setStorage("sales_return", $('.sales_return_out').is(':checked'));
        setStorage("in_ward_column", $('.in_ward_column').is(':checked'));
        setStorage("out_ward_column", $('.out_ward_column').is(':checked'));
    }
$(document).ready(function(){
    // table header fixed
    let display_height=$(window).height();
    $('.tableFixHead_account_group_voucher_analysis').css('height',`${display_height-120}px`);
});

function party_ledger_data_show(htmlFragments,v,closing_sign,currentBalance){
            htmlFragments.push(`<tr id="${v.ledger_head_id}" class="table-row table-row_id">
                        <td style="width: 1%;  border: 1px solid #ddd;">${i++}</td>
                        <td  class='ledger_td' style="width: 5%;  border: 1px solid #ddd;color: #0B55C4;"><p style="margin-left:${(h+a+a+a).length-12}px;font-size: 16px;" class="text-wrap mb-0 pb-0">${v.ledger_name}</p></td>
                        <td class=' text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;t'>${v.total_debit?(((v.total_debit||0)).formatBangladeshCurrencyType("accounts")):''}</td>
                        <td class=' text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;'>${v.total_credit?(((v.total_credit||0)).formatBangladeshCurrencyType("accounts")):""}</td>
                        <td class='closing_checkbox text-end' style='width: 3%;  border: 1px solid #ddd; font-size: 16px;'>${(currentBalance)}</td>
            </tr>`);

}
function checkbox_check(in_wards_text,out_wards_text) {

    if($(".purchase_out" ).is(':checked')==true){
      in_wards_text+='Purchase,';
    }
    if($(".grn_out" ).is(':checked')==true){
    in_wards_text+='Grn,';
    }
    if($(".sales_return_out" ).is(':checked')==true){
       in_wards_text+='Sales Return,';
    }

    if ($(".journal_out").is(':checked') == true) {
      in_wards_text+='Journal ,';
    }
    if ($(".cash_out").is(':checked') == true) {
        in_wards_text+='Cash Sales ';
    }else

    if($(".sales_in" ).is(':checked')==true){
      out_wards_text+='Sales,';
    }
    if($(".gtn_in" ).is(':checked')==true){
      out_wards_text+='Gtn,';
    }
    if($(".purchase_return_in" ).is(':checked')==true){
      out_wards_text+='Purchase  Return,';
    }


    if ($(".journal_in").is(':checked') == true) {
       out_wards_text+='Journal,';
    }
    if ($(".payment_in").is(':checked') == true) {
       out_wards_text+='Payment,';
    }

    if ($(".commission_in").is(':checked') == true) {
        out_wards_text+=' Commission';
    }
    if ($(".receipt_out").is(':checked') == true) {
      in_wards_text+='Receipt';
    }

    $('.inwards_text').text("Inward : "+out_wards_text.replace(/,$/, ''));
    $('.outwards_text').text(" Outward : "+in_wards_text.replace(/,$/, ''));

}


</script>
@endpush
@endsection
