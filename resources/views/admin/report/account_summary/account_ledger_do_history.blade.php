@extends('layouts.backend.app')
@section('title','Ledger DO History')
@push('css')
 <style>
 .table-scroll thead tr:nth-child(2) th {
    top: 30px;
}
.th{
    border: 1px solid #ddd;font-weight: bold;
    font-family: Arial, sans-serif;
}
.td{
    border: 1px solid #ddd; font-size: 16px;
    font-family: Arial, sans-serif;
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
    'page_title'=>'Ledger DO History',
    'size'=>'modal-xl',
    'page_unique_id'=>34,
    'ledger'=>'yes',
    'title'=>'Ledger DO History',
    'daynamic_function'=>'get_ledger_daily_summary_initial_show'
]);
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Ledger DO History',
    'print_layout'=>'portrait',
    'print_header'=>'Ledger DO History',
    'user_privilege_title'=>'LedgerDOHistory',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
 <form  id="add_ledger_do_history"  method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row">
        <div class="col-md-3">
            <label>Accounts Ledger : </label>
            <select name="ledger_head_id" class="form-control  js-example-basic-single  ledger_id" required>
                <option value="">--Select--</option>
                @if($all==1)
                    <option value="0">--All--</option>
                  @endif
                {!!html_entity_decode($ledgers)!!}
            </select>
        </div>
        <div class="col-md-3">
            <div class="row  m-0 p-0 ">
                <div class="col-md-6 m-0 p-0 start_date">
                    <label>Date From: </label>
                        <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{$form_date?? financial_end_date(date('Y-m-d')) }}" >
                </div>
                <div class="col-md-6 m-0 p-0 end_date">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{$to_date?? financial_end_date(date('Y-m-d')) }}" >
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <br>
            <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
        </div>
    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd ledger_Do_history_tableFixHead">
    <h5 style="text-align:center;font-weight: bold;">Stock In History</h5>
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <tr>
                <th style="width: 1%;">SL.</th>
                <th style="width: 3%;  border: 1px solid #ddd;">Date</th>
                <th class="th"  style=" width: 5%;">Particulars</th>
                <th class="th" style=" width: 5%; ">Voucher Type</th>
                <th class="th"  style=" width: 5%;">Voucher No</th>
                <th class="th"  style=" width: 5%;">Note</th>
                <th class="th text-end"  style=" width: 5%;">Qty</th>
                <th class="th text-end" style=" width: 5%;">Amount</th>

            </tr>
        </thead>
        <tbody id="myTable" class="item_body_in">
        </tbody>
        <tfoot>
            <tr>
                <th  style="width: 1%;" class="th"></th>
                <th  style="width: 5%;" class="th "></th>
                <th  style="width: 2%;" class="th"></th>
                <th  style="width: 2%;" class="th"></th>
                <th  style="width: 2%;" class="th"></th>
                <th  style="width: 2%;" class="th text-end"> Total :</th>
                <th  style="width: 2%;font-size: 18px;" class="th total_qty_in text-end"></th>
                <th  style="width: 2%;font-size: 18px;" class="th total_amount_in text-end"></th>
            </tr>
        </tfoot>
    </table>

    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <h5 style="text-align:center;font-weight: bold;">Stock Out History</h5>
        <thead>
            <tr>
                <th style="width: 1%;">SL.</th>
                <th style="width: 3%;  border: 1px solid #ddd;">Date</th>
                <th class="th"  style=" width: 5%;">Particulars</th>
                <th class="th" style=" width: 5%; ">Voucher Type</th>
                <th class="th"  style=" width: 5%;">Voucher No</th>
                <th class="th"  style=" width: 5%;">(DO) Ref No</th>
                <th class="th"  style=" width: 5%;">Gate Pass Ref</th>
                <th class="th"  style=" width: 5%;">Note</th>
                <th class="th text-end"  style=" width: 5%;">Qty</th>
                <th class="th text-end" style=" width: 5%;">Amount</th>

            </tr>
        </thead>
        <tbody id="myTable" class="item_body_out">
        </tbody>
        <tfoot>
            <tr>
                <th  style="width: 1%;" class="th"></th>
                <th  style="width: 5%;" class="th"></th>
                <th  style="width: 2%;font-size: 18px;" class="th"></th>
                <th  style="width: 2%;font-size: 18px;" class="th"></th>
                <th  style="width: 2%;font-size: 18px;" class="th"></th>
                <th  style="width: 2%;font-size: 18px;" class="th"></th>
                <th  style="width: 2%;font-size: 18px;" class="th"></th>
                <th  style="width: 2%;font-size: 18px;" class="th text-end">Total :</th>
                <th  style="width: 2%;font-size: 18px;" class="th total_qty_out text-end"></th>
                <th  style="width: 2%;font-size: 18px;" class="th total_amount_out text-end"></th>
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
let   qty_in_tptal=0;qty_out_tptal=0;amount_in_tptal=0;amount_out_tptal=0;
    // add ledger voucher form
    $(document).ready(function () {
        local_store_ledger_do_history_get();
        get_ledger_do_history_initial_show();

        $("#add_ledger_do_history").submit(function(e) {

            local_store_ledger_do_history_set_data();
            print_date();
            $(".modal").show();
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("account-ledger-do-history-data") }}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {

                    $(".modal").hide();
                    get_ledger_do_history_in_val(response.data.stock_in)
                    get_ledger_do_history_out_val(response.data.stock_out)
                    },
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
        });
});

    // ledger voucher
    function  get_ledger_do_history_in_val(response) {
        qty_in_tptal = 0;
        amount_in_tptal = 0;
        let htmlFragments = [];
        response.forEach((v, key) => {
           qty_in_tptal += (v.stock_in_qty || 0);
            amount_in_tptal += (v.stock_in_total || 0);
            htmlFragments.push(`<tr id="${v.tran_id+","+v?.voucher_type_id}" class="left left-data editIcon table-row">
                                    <td style="width: 1%;  border: 1px solid #ddd;">${(key + 1)}</td>
                                    <td class="td" style="width: 3%;" class="text-wrap">${join(new Date(v?.transaction_date), options, ' ')}</td>
                                    <td class="td" style="width: 3%;">${(v?.ledger_name||'')}</td>
                                    <td class="td" style="width: 3%;color: #0B55C4">${redirectVoucherIdWise(v?.voucher_type_id, v?.tran_id,v?.voucher_name)}</td>
                                    <td class="td" style="width: 3%;">${(v?.invoice_no ||'')}</td>
                                    <td class="td text-wrap" style="width: 3%;">${(v?.narration ||'')}</td>
                                    <td class="td" style="width: 3%; text-align:right">${(v?.stock_in_qty || 0).formatBangladeshCurrencyType("accounts")}</td>
                                    <td class="td" style="width: 3%;text-align:right">${(v?.stock_in_total|| 0).formatBangladeshCurrencyType("accounts")}</td>
                            </tr>`);
        });
        // Append the fragment to the DOM once
        $(".item_body_in").html(htmlFragments.join(''));

        // Update total values
        $('.total_qty_in').text((qty_in_tptal||0).formatBangladeshCurrencyType("accounts"));
        $('.total_amount_in').text((amount_in_tptal|| 0).formatBangladeshCurrencyType("accounts"));
        get_hover();
    }
     // ledger voucher
     function  get_ledger_do_history_out_val(response) {
        qty_out_tptal = 0;
        amount_out_tptal = 0;
        let htmlFragments = [];
        response.forEach((v, key) => {
           qty_out_tptal += (v.stock_out_qty || 0);
            amount_out_tptal += (v.stock_out_total || 0);


            htmlFragments.push(`<tr id="${v.tran_id+","+v.voucher_type_id}" class="left left-data editIcon table-row">
                                    <td style="width: 1%;  border: 1px solid #ddd;">${(key + 1)}</td>
                                    <td class="td" style="width: 3%;" class="text-wrap">${join(new Date(v.transaction_date), options, ' ')}</td>
                                    <td class="td" style="width: 3%;">${(v.ledger_name||'')}</td>
                                    <td class="td" style="width: 3%;color: #0B55C4">${(v.voucher_name ||'')}</td>
                                    <td class="td" style="width: 3%;">${(v.invoice_no ||'')}</td>
                                    <td class="td text-wrap" style="width: 3%;">${(v.narration ||'')}</td>
                                    <td class="td text-wrap" style="width: 3%;"></td>
                                    <td class="td text-wrap" style="width: 3%;"></td>
                                    <td class="td" style="width: 3%;text-align:right">${(v.stock_out_qty || 0).formatBangladeshCurrencyType("accounts")}</td>
                                    <td class="td" style="width: 3%;text-align:right">${(v.stock_out_total || 0).formatBangladeshCurrencyType("accounts")}</td>
                            </tr>`);
        });
        // Append the fragment to the DOM once
        $(".item_body_out").html(htmlFragments.join(''));

        // Update total values
        $('.total_qty_out').text((qty_out_tptal||0).formatBangladeshCurrencyType("accounts"));
        $('.total_amount_out').text((amount_out_tptal|| 0).formatBangladeshCurrencyType("accounts"));
        get_hover();
    }
    function  get_ledger_do_history_initial_show(){
        print_date();
        $(".modal").show();
        $.ajax({
                url: "{{ route('account-ledger-do-history-data')}}",
                type: 'GET',
                dataType: 'json',
                data:{
                    to_date:$('.to_date').val(),
                    from_date:$('.from_date').val(),
                    ledger_id:$('.ledger_id').val()
                },
                success: function(response) {
                    $(".modal").hide();
                    get_ledger_do_history_in_val(response.data.stock_in)
                    get_ledger_do_history_out_val(response.data.stock_out)
                },
                error : function(data,status,xhr){
                        Unauthorized(data.status);
                }
        });
    }
    function local_store_ledger_do_history_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("ledger_id", '.ledger_id');
    }

    function local_store_ledger_do_history_set_data() {
            setStorage("end_date", $('.to_date').val());
            setStorage("start_date", $('.from_date').val());
            setStorage("ledger_id", $('.ledger_id').val());
    }
    //redirect route
    $(document).ready(function() {        
        let display_height=$(window).height();
        $('.ledger_Do_history_tableFixHead').css('height',`${display_height-120}px`);
    });

</script>
@endpush
@endsection
