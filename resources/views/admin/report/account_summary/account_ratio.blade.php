
@extends('layouts.backend.app')
@section('title','Trial Balance')
@push('css')
 <!-- model style -->
 <link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
 <style>
 table {width:100%;grid-template-columns: auto auto;}

 th{
    border: 1px solid #ddd;font-weight: bold;
    font-family: Arial, sans-serif;
    font-size: 20px;
 }



</style>
@endpush
@section('admin_content')<br>

<!-- setting component-->
@component('components.report_setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=>'Account Ratio',
    'size'=>'modal-xl',
    'page_unique_id'=>43,
    'ledger'=>'yes',
    'title'=>'Account Ratio',
    'daynamic_function'=>'get_ledger_daily_summary_initial_show'
]);
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Account Ratio',
    'print_layout'=>'landscape',
    'print_header'=>'Account Ratio',
    'user_privilege_title'=>'AccountRatio',
    'print_date'=>1,
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="trial_balance_form"  method="POST">
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
<div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
        <div class="table-responsive">
            <table id="tableId" style=" border-collapse: collapse; " class="table d-none">
                <tbody>
                    <tr>
                        <th >Gross Profit Margin: </th>
                        <th id="gpm"></th>
                    </tr>
                    <tr>
                        <th >Net Profit Margin: </th>
                        <th id="npm"></th>
                    </tr>
                    <tr>
                        <th >Working Capital: </th>
                        <th id="workingCapital"></th>
                    </tr>
                    <tr>
                        <th>Current Ratio: </th>
                        <th id="currentRatio"></th>
                    </tr>
                    <tr>
                        <th>Quick Ratio: </th>
                        <th id="quickRatio"></th>
                    </tr>
                    <tr>
                        <th>Leverage Ratio: </th>
                        <th id="lequageRatio"></th>
                    </tr>
                    <tr>
                        <th>Debt Equity Ratio: </th>
                        <th id="der"></th>
                    </tr>
                    <tr>
                        <th>Inventory Turnover Ratio: </th>
                        <th id="itr"></th>
                    </tr>
                </tbody>
            </table>
        </div>
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
let  total_opening=0; total_debit=0; total_credit=0;total_clasing=0;i=1;

$(document).ready(function () {
    // get trial balance
    function get_trial_balance_initial_show(){
        print_date();
        $(".modal").show();
        $.ajax({
            url: '{{ route("account-ratio-data") }}',
                method: 'GET',
                data: {
                    to_date:$('.to_date').val(),
                    from_date:$('.from_date').val(),
                    group_id:$(".group_id").val(),
                },
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_trial_balance(response)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
    }

    // get_trial_balance_initial_show();

    $("#trial_balance_form").submit(function(e) {
            print_date();
            $(".modal").show();
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("account-ratio-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                        console.log(response)
                        getAccountRatio(response?.data)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                },
                complete:()=>{
                    $(".modal").hide();
                }
            });
   });

    // trial balance function
    function getAccountRatio(data){
        $('#gpm').text(data?.gpm?.formatBangladeshCurrencyType("accounts"));
        $('#npm').text(data?.npm?.formatBangladeshCurrencyType("accounts"));
        $('#workingCapital').text(data?.workingCapital?.formatBangladeshCurrencyType("accounts"));
        $('#currentRatio').text(data?.currentRatio?.formatBangladeshCurrencyType("accounts"));
        $('#quickRatio').text(data?.quickRatio?.formatBangladeshCurrencyType("accounts"));
        $('#lequageRatio').text(data?.lequageRatio?.formatBangladeshCurrencyType("accounts"));
        $('#der').text(data?.der?.formatBangladeshCurrencyType("accounts"));
        $('#itr').text(data?.itr?.formatBangladeshCurrencyType("accounts"));
        //  $(".modal").hide();
        $("#tableId").removeClass('d-none');
     }
});


</script>


@endpush
@endsection
