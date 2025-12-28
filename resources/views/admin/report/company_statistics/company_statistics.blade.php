
@extends('layouts.backend.app')
@section('title','Company Statistics')
@push('css')

 <style>
.th{
    border: 1px solid #ddd;font-weight: bold;
}
.td1{
    border: 1px solid #ddd; font-size: 18px;
    font-family: Arial, sans-serif
}
.td2{
    border: 1px solid #ddd;font-size: 18px;
    font-family: Arial, sans-serif
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 18px !important;
}
.font {
    font-size: 16px;
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
    'page_title'=>'Company Statistics ',
    'page_unique_id'=>23,
    'groupChart'=>'yes',
    'title'=>'Company Statistics',
    'daynamic_function'=>'get_accounts_company_statistics_initial_show',
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Company Statistics',
    'print_layout'=>'portrait',
    'print_header'=>'Company Statistics',
    'user_privilege_title'=>'CompanyStatistics',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="add_company_statistics"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-6">
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
                    <div class="col-md-2">
                          <label></label><br>
                            <button type="submit" class="btn btn-primary hor-grd btn-grd-primary w-100 submit" style="max-width: 200px; margin-bottom: 5px;">Search</button>

                    </div>
                </div>
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
                    <th style="width: 1%;  font-size: 18px"class="th ">SL.</th>
                    <th  style="width: 5%; font-size: 18px "class="th ">Types of Vouchers</th>
                    <th  style="width: 5%; font-size: 18px"class="th from_to_date text-end"></th>
                    <th style="width: 1%; font-size: 18px "class="th">SL.</th>
                    <th  style="width: 5%;  font-size: 18px"class="th">Types of Accounts</th>
                    <th  style="width: 5%;  font-size: 18px"class="th from_to_date text-end"></th>
                </tr>

        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;" class="th"></th>
                <th style="width: 3%;"class="th text-end">Total :</th>
                <th style="width: 3%; font-size: 18px;font-weight: bold;" class="th text-end  total_voucher"></th>
                <th style="width: 2%; font-size: 18px;font-weight: bold;" class="th"></th>
                <th style="width: 3%; font-size: 18px;font-weight: bold;" class="th"></th>
                <th style="width: 2%; font-size: 18px;font-weight: bold;" class="th"></th>

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

let  total_voucher=0;i=1;

// group  analysis

$(document).ready(function () {
    from_to_date();
    local_store_company_statistics_get();
    get_accounts_company_statistics_initial_show();

    $("#add_company_statistics").submit(function(e) {
        local_store_company_statistics_set_data();
        $(".modal").show();
        from_to_date();
        print_date();
        total_voucher=0;i=1;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{route("report-company-statistics-data") }}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {

                    $(".modal").hide();
                    account_company_statistics(response.data)
                    },
                    error : function(data,status,xhr){
                       Unauthorized(data.status);
                    }
            });
    });

    // item wise analysis route
    // $('.sd').on('click', '.item_name',function(e){
    //     e.preventDefault();
    //     let  voucher_id=$(this).closest('tr').attr('id');
    //     let form_date=$('.from_date').val();
    //     let to_date=$('.to_date').val();
    //     url = "{{route('report-company-statistics-monthly-data', ['voucher_id' =>':voucher_id','form_date' =>':form_date','to_date' =>':to_date'])}}";
    //     url = url.replace(':voucher_id',voucher_id);
    //     url = url.replace(':form_date',form_date);
    //     url = url.replace(':to_date',to_date);
    //     window.open(url,'_blank');
    // });
    // table header fixed
    let display_height=$(window).height();
    $('.tableFixHead_report').css('height',`${display_height-120}px`);
    
});

// stock group analysis function
function get_accounts_company_statistics_initial_show(){
      total_voucher=0;i=1;
        $(".modal").show();
        from_to_date();
        print_date();
            // stock in array check

        $.ajax({
            url: '{{ route("report-company-statistics-data") }}',
                method: 'GET',
                data: {
                    to_date:$('.to_date').val(),
                    from_date:$('.from_date').val(),
                },
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();

                    account_company_statistics(response.data)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
}
function local_store_company_statistics_get() {
    getStorage("end_date", '.to_date');
    getStorage("start_date", '.from_date');
}

function local_store_company_statistics_set_data() {
    setStorage("end_date", $('.to_date').val());
    setStorage("start_date", $('.from_date').val());
}




function from_to_date(){
    let date = join(new Date($('.from_date').val()), options, ' ') +
    '<br>' +
    'to' +
    '<br>' +
    join(new Date($('.to_date').val()), options, ' ');
   $('.from_to_date').html(date);
}


// stock group analysis function
function account_company_statistics(response){

    htmlFragments=[];
    let ac_group=0;
    let  ac_ledger=0;
    let  voucher_type=0;
    let  stock_group=0;
    let  stock_item=0;
    let   godown=0;
    let  unitsof_measure=0;
    $.each(response.voucher, function (key, v) {

        total_voucher+=(v.tran_id_count||0);
        htmlFragments.push(`
                    <tr id="${v.voucher_id}" class="lleft left-data table-row">
                        <td class="sl" style="width: 1%; border: 1px solid #ddd;">${i++}</td>
                        <td  style="width: 5%; border: 1px solid #ddd; color: #0B55C4;" class="font">
                        ${reportCompanyStatisticsMonthlyData(v.voucher_id,v.voucher_name)}
                        </td>
                        <td class="td text-end" style="width: 3%;border: 1px solid #ddd; font-size: 18px">${(v.tran_id_count || 0).formatBangladeshCurrencyType("quantity")}</td>` );

        if(ac_group!=response.accounts_group){
            htmlFragments.push(`<td class="sl" style="width: 1%; border: 1px solid #ddd;">${1}</td>
            <td class="sl" style="width: 1%; border: 1px solid #ddd;">Accounts Group</td>
            <td class="sl text-end" style="width: 1%; border: 1px solid #ddd; font-size: 18px">${response.accounts_group[0].group_id_count.formatBangladeshCurrencyType("quantity")}</td></tr>`);
        ac_group=response.accounts_group
        }
        if(ac_ledger!=response.accounts_ledger&&key==1){
            htmlFragments.push(`<td class="sl" style="width: 1%; border: 1px solid #ddd;">${2}</td>
            <td class="sl" style="width: 1%; border: 1px solid #ddd;">Accounts Ledger</td>
            <td class="sl text-end" style="width: 1%; border: 1px solid #ddd; font-size: 18px">${response.accounts_ledger[0].ledger_head_id_count.formatBangladeshCurrencyType("quantity")}</td></tr>`);
            ac_ledger=response.accounts_ledger
        }
        if(voucher_type!=response.voucher_type&&key==2){
            htmlFragments.push(`<td class="sl" style="width: 1%; border: 1px solid #ddd;">${3}</td>
            <td class="sl" style="width: 1%; border: 1px solid #ddd;">Accounts Voucher</td>
            <td class="sl text-end" style="width: 1%; border: 1px solid #ddd; font-size: 18px">${response.voucher_type[0].voucher_type_id_count.formatBangladeshCurrencyType("quantity")}</td></tr>`);
            voucher_type=response.voucher_type;
        }
        if(stock_group!=response.stock_group&&key==3){
            htmlFragments.push(`<td class="sl" style="width: 1%; border: 1px solid #ddd;">${4}</td>
            <td class="sl" style="width: 1%; border: 1px solid #ddd;">Stock Groups</td>
            <td class="sl text-end" style="width: 1%; border: 1px solid #ddd; font-size: 18px">${response.stock_group[0].stock_group_id_count.formatBangladeshCurrencyType("quantity")}</td></tr>`);
            stock_group=response.stock_group;
        }
        if(stock_item!=response.stock_item&&key==4){
            htmlFragments.push(`<td class="sl" style="width: 1%; border: 1px solid #ddd;">${4}</td>
            <td class="sl" style="width: 1%; border: 1px solid #ddd;">Stock Items</td>
            <td class="sl text-end" style="width: 1%; border: 1px solid #ddd; font-size: 18px">${response.stock_item[0].stock_item_id_count.formatBangladeshCurrencyType("quantity")}</td></tr>`);
            stock_item=response.stock_item;
        }
        if( godown!=response.stock_godowns&&key==5){
            htmlFragments.push(`<td class="sl" style="width: 1%; border: 1px solid #ddd;">${5}</td>
            <td class="sl" style="width: 1%; border: 1px solid #ddd;">Godowns</td>
            <td class="sl text-end" style="width: 1%; border: 1px solid #ddd; font-size: 18px">${response.stock_godowns[0].godown_id_count.formatBangladeshCurrencyType("amount")}</td></tr>`);
            godown=response.stock_godowns;
        }
        if(unitsof_measure!=response.unitsof_measure&&key==6){
            htmlFragments.push(`<td class="sl" style="width: 1%; border: 1px solid #ddd;">${6}</td>
            <td class="sl" style="width: 1%; border: 1px solid #ddd;">Unit of Measure</td>
            <td class="sl text-end" style="width: 1%; border: 1px solid #ddd; font-size: 18px">${response.unitsof_measure[0].unit_of_measure_id_count.formatBangladeshCurrencyType("quantity")}</td></tr>`);
            unitsof_measure=response.unitsof_measure;
        }
        if(key>6){
            htmlFragments.push(`<td class="sl" style="width: 1%; border: 1px solid #ddd;"></td>
            <td class="sl" style="width: 1%; border: 1px solid #ddd;"></td>
            <td class="sl text-end" style="width: 1%; border: 1px solid #ddd;"></td></tr>`)
        };


    });
    $('.item_body').html(htmlFragments.join(""))
    get_hover();
    $('.total_voucher').text(total_voucher.formatBangladeshCurrencyType("quantity"));


}
</script>
@endpush
@endsection
