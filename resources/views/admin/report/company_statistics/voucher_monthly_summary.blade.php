@extends('layouts.backend.app')
@section('title','Voucher Monthly Summary')
@push('css')
 <style>

    .table-scroll thead tr:nth-child(2) th {
        top: 30px;
    }
    .th{
        border: 1px solid #ddd;font-weight: bold;
    }
    .td{
        border: 1px solid #ddd; font-size: 16px;
    }
</style>
@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
'id' =>'exampleModal',
'class' =>'modal fade',
'page_title'=>'Voucher Monthly Summary',
'size'=>'modal-xl',
'page_unique_id'=>24,
'title'=>'Voucher Monthly Summary',
'daynamic_function'=>'get_voucher_monthly_summary_initial_show'
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Voucher Monthly Summary',
    'print_layout'=>'portrait',
    'print_header'=>'Voucher Monthly Summary',
    'user_privilege_title'=>'VoucherMonthlySummary',
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
 <form  id="add_voucher_monthly_summary_form"  method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row">
        <div class="col-md-4">
            <label>Voucher Type : </label>
            <select name="voucher_id" class="form-control js-example-basic-single voucher_id" required>
                <option value="">--select--</option>
                @php $voucher_type_id= 0; @endphp
                @foreach ($vouchers as $voucher)
                @if($voucher_type_id!=$voucher->voucher_type_id)
                @php $voucher_type_id=$voucher->voucher_type_id; @endphp
                <option style="color:red;" value="v{{$voucher->voucher_type_id??''}}">{{$voucher->voucher_type??''}}</option>
                @endif
                <option value="{{$voucher->voucher_id}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$voucher->voucher_name}}</option>

                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <div class="row  m-0 p-0 ">
                <div class="col-md-6 m-0 p-0 start_date">
                    <label>Date From: </label>
                        <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{$form_date??financial_end_date(date('Y-m-d'))}}" >
                </div>
                <div class="col-md-6 m-0 p-0 end_date">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{$to_date??financial_end_date(date('Y-m-d'))}}" >
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
<div class="dt-responsive table-responsive cell-border sd tableFixHead_item_register">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <tr>
                <th  rowspan="2" style="width: 1%;" class="align-middle">SL.</th>
                <th rowspan="2"style="width: 3%;  border: 1px solid #ddd;" class="align-middle">Particulars</th>
                <th class="th" colspan="3" style=" width: 5%;text-align:center;"class="inwards">Transactions</th>
            </tr>
            <tr>
                <th class="th text-end" style="width: 3%;">Total Vouchers</th>
                <th style="width: 3%;" class="inwards_rate th text-end">(Cancelled)</th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th  style="width: 1%;" class="th"></th>
                <th  style="width: 5%;" class="th text-end">Total :</th>
                <th  style="width: 2%;font-size: 18px;" class="th totol_voucher_calculation text-end"></th>
                <th  style="width: 2%;font-size: 18px;" class="th inwards_rate total_voucher_calcelled_calculation text-end"></th>
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
<!-- table hover js -->
<script type="text/javascript" src="{{asset('ledger&item_select_option.js')}}"></script>
<script>
  let  totol_voucher=0; total_voucher_cal=0;

// Create an array of month names
// stock item get id check
    if("{{$from_date??0}}"!=0){
        $('.from_date').val('{{$from_date??0}}');
    }
    if("{{$to_date??0}}"!=0){
        $('.to_date').val('{{$to_date??0}}');
    }

    if("{{$voucher_id??0}}"!=0){
        $('.voucher_id').val('{{$voucher_id??0}}');
    }

$(document).ready(function () {

    // stock item get id check
    if("{{$voucher_id??0}}"!=0){
        local_store_voucher_monthly_summary_set_data();
    }else{
        local_store_voucher_monthly_summary_get();
    }

    if($('.voucher_id').val()!=0){
        get_voucher_monthly_summary_initial_show();
    }

    // voucher monthly summary form
    $("#add_voucher_monthly_summary_form").submit(function(e) {
        local_store_voucher_monthly_summary_set_data();
        $(".modal").show();
        print_date();
        e.preventDefault();
        const fd = new FormData(this);
        $.ajax({
            url: '{{ route("report-voucher-monthly-summary-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_voucher_monthly_summary_val(response.data)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
    });
});

// voucher monthly summary
function get_voucher_monthly_summary_val(response) {
        totol_voucher= 0;
        total_voucher_cal = 0;
        htmlFragments=[];

        // date wise voucher
        dateToMonthConvert($('.from_date').val(),$('.to_date').val()).forEach((v, key) => {
            let voucher_count=0;
            let voucher_count_cal=0;

            //month wise voucher
            response.month_wise_voucher?.forEach((mw, key) => {
                if((new Date(mw.transaction_date).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })==v)){
                    voucher_count=mw.tran_id_count;
                    totol_voucher+=mw.tran_id_count;
                }
            });

            //month wise voucher calcelled
            response.month_wise_voucher_cal?.forEach((mwc, key) => {
                if((new Date(mwc.transaction_date).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })==v)){
                    voucher_count=(voucher_count-mwc.tran_id_can_count);
                    voucher_count_cal=mwc.tran_id_can_count;
                    total_voucher_cal+=mwc.tran_id_can_count;
                    totol_voucher-=mwc.tran_id_can_count;
                }
            })
            // let date_1 = new Date(v);
            // date_1 .setDate(date_1 .getDate() + 1);
            // let year = date_1.getFullYear();
            // let month = String(date_1.getMonth() + 1).padStart(2, '0');
            // let day = String(date_1.getDate()).padStart(2, '0');
            // let formattedDate = `${year}-${month}-${day}`;
            htmlFragments.push(`<tr id="${v}" class="left left-data editIcon table-row" data-toggle="modal" data-target="#EditLedgerModel">
                                    <td class="sl" style="width: 1%; border: 1px solid #ddd;">${key+1}</td>
                                    <td style="width: 5%;color: #0B55C4;" class="td"><span><span>${voucherMonthIdWise(v,v)}</td>`);
            htmlFragments.push(`<td style='width: 3%;' class='td text-end'>${(voucher_count??0).formatBangladeshCurrencyType("amount")}</td>`);
            htmlFragments.push(`<td style='width: 3%;' class='td text-end'>${(voucher_count_cal??0).formatBangladeshCurrencyType("amount")}</td>`);
            htmlFragments.push(`</tr>`);
        });

        $(".item_body").html(htmlFragments.join(''));
        $('.totol_voucher_calculation').text(totol_voucher.formatBangladeshCurrencyType("amount"));
        $('.total_voucher_calcelled_calculation').text(total_voucher_cal.formatBangladeshCurrencyType("amount"));
        set_scroll_table();
        get_hover();
    }

    function get_voucher_monthly_summary_initial_show(){
        $(".modal").show();
        print_date();
        $.ajax({
            url: '{{ route("report-voucher-monthly-summary-data") }}',
                method: 'GET',
                data: {
                    to_date:$('.to_date').val(),
                    from_date:$('.from_date').val(),
                    voucher_id:$('.voucher_id').val()
                },
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_voucher_monthly_summary_val(response.data)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
    }

// table header fixed
$(document).ready(function(){
    // table header fixed
    let display_height=$(window).height();
    $('.tableFixHead_item_register').css('height',`${display_height-280}px`);
});

// local store in bowser
function local_store_voucher_monthly_summary_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("voucher_id", '.voucher_id');
    }

function local_store_voucher_monthly_summary_set_data() {
    setStorage("end_date", $('.to_date').val());
    setStorage("start_date", $('.from_date').val());
    setStorage("voucher_id", $('.voucher_id').val());
}


// stock item  wise  voucher  route
// $('.sd').on('click','.table-row',function(e){
//     e.preventDefault();
//     let date=$(this).closest('tr').attr('id');
//     let from_date=$('.from_date').val();
//     let to_date=$('.to_date').val();
//     let voucher_id=$('.voucher_id').val();
//     url = "{{route('voucher-month-id-wise', ['voucher_id'=>':voucher_id','date' =>':date','from_date' =>':from_date','to_date' =>':to_date'])}}";
//     url = url.replace(':from_date',from_date);
//     url = url.replace(':to_date',to_date);
//     url = url.replace(':date',date);
//     url = url.replace(':voucher_id',voucher_id);
//     window.open(url, '_blank');
// });

function voucherMonthIdWise(id,name){
    let form_date=$('.from_date').val();
    let to_date=$('.to_date').val();
    let voucher_id=$('.voucher_id').val();
    let url = "{{route('voucher-month-id-wise', ['voucher_id'=>':voucher_id','date' =>':date','from_date' =>':from_date','to_date' =>':to_date'])}}";
    url = url.replace(':from_date',form_date);
    url = url.replace(':to_date',to_date);
    url = url.replace(':date',id);
    url = url.replace(':voucher_id',voucher_id);

    return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                href="${url}">${name || ''}</a>
                <span class="display-none">${name || ''}</span>`;
}
</script>
@endpush
@endsection
