
@extends('layouts.backend.app')
@section('title','Ledger Cash Flow')
@push('css')
 <style>
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
'page_title'=>'Ledger Cash Flow',
'size'=>'modal-xl',
'page_unique_id'=>28,
'ledger'=>'yes',
'title'=>'Ledger Cash Flow',
'daynamic_function'=>'get_ledger_cash_flow_initial_show'
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Ledger Cash Flow',
    'print_layout'=>'landscape',
    'print_header'=>'Ledger Cash Flow',
    'user_privilege_title'=>'LedgerCashFlow',
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
 <form  id="add_ledger_form"  method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row">
        <div class="col-md-4">
            <label>Party Name : </label>
            <select name="ledger_id" id="ledger_id" class="form-control  js-example-basic-single  ledger_id" required>
                <option value="">--Select--</option>
                {!!html_entity_decode($ledger_data)!!}
            </select>
        </div>
        <div class="col-md-4">
            <div class="row  m-0 p-0">
                <div class="col-md-6 m-0 p-0">
                    <label>Date From: </label>
                    <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{$form_date??financial_end_date(date('Y-m-d')) }}" >
                </div>
                <div class="col-md-6 m-0 p-0">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{$to_date?? financial_end_date(date('Y-m-d')) }}">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <br>
            <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
        </div>
    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
 <div class="dt-responsive table-responsive cell-border sd ledger_cash_flow_summarytableFixHead " >
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers ">
        <thead>
            <tr>
                <th style="width: 1%;" class="th">SL.</th>
                <th style="width: 3%;"class="th">Particulars</th>
                <th style="width: 3%;  border: 1px solid #ddd;">Date</th>
                <th style="width: 2%;  border: 1px solid #ddd;">Voucher Type</th>
                <th style="width: 3%;  border: 1px solid #ddd;" >Voucher No</th>
                <th style="width: 2%;"class="th text-end">Inflow</th>
                <th style="width: 3%;"class="th text-end">Outflow</th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;"class="th"></th>
                <th style="width: 1%;"class="th"></th>
                <th style="width: 1%;"class="th"></th>
                <th style="width: 1%;"class="th"></th>
                <th style="width: 3%;"class="th text-end ">Total</th>
                <th style="width: 2%; font-size: 18px"class="th total_debit text-end"></th>
                <th style="width: 3%;font-size: 18px"class="th total_credit text-end"></th>
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
if("{{$ledger_id??0}}"!=0){
     $('.ledger_id').val("{{$ledger_id??0}}");
}
$(document).ready(function () {
   
    if("{{$ledger_id??0}}"!=0){
        local_store_ledger_cash_flow_summary_set_data()
    }else{
        local_store_ledger_cash_flow_summary_get();
    }
    get_ledger_cash_flow_initial_show();
 
    $("#add_ledger_form").submit(function(e) {
        local_store_ledger_cash_flow_summary_set_data();
        e.preventDefault();
        const fd = new FormData(this);
        $.ajax({
            url: '{{ route("ledger-cash-flow-get-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    get_ledger_val(response)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
    });
});
    function get_ledger_val(response){
                htmlFragments=[];
                let total_debit=0,total_credit=0;
                $.each(response.data, function(key, v) {
                    total_debit+=v.sum_debit;
                    total_credit+=v.sum_credit;
                    htmlFragments.push(`<tr id='${v.tran_id},${v.voucher_type_id}' class="left left-data editIcon table-row">
                        <td  style="width: 1%;  border: 1px solid #ddd;">${(key+1)}</td>
                        <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;">${join( new Date(v.transaction_date), options, ' ')}</td>
                        <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;">${(v.ledger_name ? v.ledger_name:'')}</td>
                        <td class="voucher_name" style="width: 2%;  border: 1px solid #ddd; font-size: 16px;">${redirectVoucherIdWise(v.voucher_type_id,v.tran_id,v.voucher_name)}</td>
                        <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" >${v.invoice_no}</td>
                        <td  style="width: 2%;  border: 1px solid #ddd; font-size: 18px;" class="text-end">${(v.sum_debit?v.sum_debit.formatBangladeshCurrencyType("accounts"):"")}</td>
                        <td  style="width: 3%;  border: 1px solid #ddd; font-size: 18px;" class="text-end">${(v.sum_credit?v.sum_credit.formatBangladeshCurrencyType("accounts"):"")}</td>
                    </tr> `);
                });

        $(".item_body").html(htmlFragments.join(""));
        $('.total_debit').text(((total_debit||0)||0).formatBangladeshCurrencyType("accounts"));
        $('.total_credit').text((total_credit||0).formatBangladeshCurrencyType("accounts"));
        get_hover();
    }
   function  get_ledger_cash_flow_initial_show(){
        updategetAndRemoveStorage();
        $.ajax({
                url: "{{ url('ledger-cash-flow-get-data')}}",
                type: 'GET',
                dataType: 'json',
                data:{
                    to_date:$('.to_date').val(),
                    from_date:$('.from_date').val(),
                    ledger_id:$('.ledger_id').val()
                },
                success: function(response) {
                    get_ledger_val(response)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
    }
function local_store_ledger_cash_flow_summary_get() {
    getStorage("end_date", '.to_date');
    getStorage("start_date", '.from_date');
    getStorage("ledger_id", '.ledger_id');
}

function local_store_ledger_cash_flow_summary_set_data() {
    setStorage("end_date", $('.to_date').val());
    setStorage("start_date", $('.from_date').val());
    setStorage("ledger_id", $('.ledger_id').val());
}
    //redirect route
    $(document).ready(function() {
        $(document).on('click', '.voucher_name', function(e) {
            setStorage("end_date_update", $('.to_date').val());
            setStorage("start_date_update", $('.from_date').val());
            setStorage("ledger_id_update", $('.ledger_id').val());
        })
        let display_height=$(window).height();
       $('.ledger_cash_flow_summarytableFixHead').css('height',`${display_height-120}px`);
    });
    function updategetAndRemoveStorage() {
        getStorage("end_date_update", '.to_date');
        getStorage("start_date_update", '.from_date');
        getStorage("ledger_id_update", '.ledger_id');
        getRemoveItem("end_date_update", '.to_date');
        getRemoveItem("start_date_update", '.from_date');
        getRemoveItem("ledger_id_update", '.ledger_id');
    }
</script>
@endpush
@endsection
