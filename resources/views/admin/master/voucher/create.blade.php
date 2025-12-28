@extends('layouts.backend.app')
@section('title','Voucher')
@push('css')
<style>
  input[type=radio] {
    width: 20px;
    height: 20px;
}
input[type=checkbox] {
    width: 20px;
    height: 20px;
}

</style>
@endpush
@section('admin_content')
<br>
@php
 $page_wise_setting_data=page_wise_setting(Auth::user()->id,3);
 if($page_wise_setting_data){
    $redirect=$page_wise_setting_data->redirect_page;

 }else{
    $redirect=3;
 }
@endphp
<!-- voucher add model  -->
@component('components.create', [
    'title'    => 'Accounts Voucher [New]',
    'help_route'=>route('voucher.index'),
    'close_route'=>route('master-dashboard'),
    'veiw_route'=>route('voucher.index'),
    'form_id' => 'add_voucher_form',
    'method'=> 'POST',
])
    @slot('body')
        <div class="row">
            <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6" >
                <div class="card-block ">
                    <fieldset class="border p-2">
                        <legend  class="float-none w-auto p-2">General Fields</legend>
                        <div class="form-group">
                            <label for="voucher_name">Voucher Name:</label>
                            <input type="text" name="voucher_name" class="form-control "  id="voucher_name" placeholder="Voucher Name" >
                            <span id='error_voucher_name' class=" text-danger"></span>
                        </div>
                        <div class="form-group ">
                            <label for="voucher_type_id">Voucher Type</label>
                            <select name="voucher_type_id"  class="form-control  js-example-basic-single  voucher_type " id="voucher_type_id" required>
                                <option value="" >-- select one --</option>
                                @foreach ($voucher_types as $voucher_type)
                                <option value="{{$voucher_type->voucher_type_id}}">{{$voucher_type->voucher_type}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group ">
                            <label for="category">Category :</label>
                            <select name="category"  class="form-control left-data" id="category" required>
                                <option value="normal">Normal</option>
                                <option value="All">All</option>
                                <option value="Pos">POS</option>
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="border p-2">
                        <legend  class="float-none w-auto p-2">Number Settings</legend>
                        <div class="form-group ">
                            <label for="vouchernumbermethod">Number Method :</label>
                            <select name="vouchernumbermethod"  class="form-control voucher_type_id  left-data" id="vouchernumbermethod" required>
                                <option value="1">Full Automatic [ Singular Number ]</option>
                                <option value="2">Semi Automatic [ Manual Text + Number ]</option>
                                <option value="3">Semi Automatic [ Manual Text + Timeframe+ Number ]</option>
                                <option value="4">Full Manual</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="manual_text">Manual Text :</label>
                            <input type="text" name="manual_text" class="form-control manual" disabled id="manual_text" placeholder="Manual Text" >
                        </div>
                        <div class="form-group row  "  >
                            <label for="formGroupExampleInput2">Timeframe : </label>
                            <div class="col-md-4">
                                <select name="time_frame_year"  class="form-control  js-example-basic-single   left-data time_frame" id="year" required disabled>
                                    <option value="0">-- select one --</option>
                                    <optgroup label="Year">Year</optgroup>
                                    <option value="1">Year/ [2 digits]</option>
                                    <option value="2">Year- [2 digits]</option>
                                    <option value="3">Year/ [4 digits]</option>
                                    <option value="4">Year- [4 digits]</option>
                                    <optgroup label="Month">Month</optgroup>
                                    <option value="5">Month/ [ 01-12 ]</option>
                                    <option value="6">Month- [ 01-12] </option>
                                    <option value="7">Month/ [ Jan-Dec ]</option>
                                    <option value="8">Month/ [ Jan-Dec ]</option>
                                    <optgroup label="Day">Day</optgroup>
                                    <option value="9">Day/ [01-31 ]</option>
                                    <option value="10">Day/ [01-31 ]</option>
                                    <optgroup label="Time">Time</optgroup>
                                    <option value="11">Hour/ [00-23]</option>
                                    <option value="12">Hour- [00-23]</option>
                                    <option value="13">Minute/ [00-59]</option>
                                    <option value="14">Minute/ [00-59]</option>
                                    <option value="15">Second/ [00-59]</option>
                                    <option value="16">Second/ [00-59]</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="time_frame_month"  class="form-control  js-example-basic-single   left-data time_frame" id="month" required disabled>
                                    <option value="0">-- select one --</option>
                                    <optgroup label="Year">Year</optgroup>
                                    <option value="1">Year/ [2 digits]</option>
                                    <option value="2">Year- [2 digits]</option>
                                    <option value="3">Year/ [4 digits]</option>
                                    <option value="4">Year- [4 digits]</option>
                                    <optgroup label="Month">Month</optgroup>
                                    <option value="5">Month/ [ 01-12 ]</option>
                                    <option value="6">Month- [ 01-12] </option>
                                    <option value="7">Month/ [ Jan-Dec ]</option>
                                    <option value="8">Month/ [ Jan-Dec ]</option>
                                    <optgroup label="Day">Day</optgroup>
                                    <option value="9">Day/ [01-31 ]</option>
                                    <option value="10">Day/ [01-31 ]</option>
                                    <optgroup label="Time">Time</optgroup>
                                    <option value="11">Hour/ [00-23]</option>
                                    <option value="12">Hour- [00-23]</option>
                                    <option value="13">Minute/ [00-59]</option>
                                    <option value="14">Minute/ [00-59]</option>
                                    <option value="15">Second/ [00-59]</option>
                                    <option value="16">Second/ [00-59]</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="time_frame_day"  class="form-control  js-example-basic-single   left-data time_frame" id="time" required disabled>
                                    <option value="0">-- select one --</option>
                                    <optgroup label="Year">Year</optgroup>
                                    <option value="1">Year/ [2 digits]</option>
                                    <option value="2">Year- [2 digits]</option>
                                    <option value="3">Year/ [4 digits]</option>
                                    <option value="4">Year- [4 digits]</option>
                                    <optgroup label="Month">Month</optgroup>
                                    <option value="5">Month/ [ 01-12 ]</option>
                                    <option value="6">Month- [ 01-12] </option>
                                    <option value="7">Month/ [ Jan-Dec ]</option>
                                    <option value="8">Month/ [ Jan-Dec ]</option>
                                    <optgroup label="Day">Day</optgroup>
                                    <option value="9">Day/ [01-31 ]</option>
                                    <option value="10">Day/ [01-31 ]</option>
                                    <optgroup label="Time">Time</optgroup>
                                    <option value="11">Hour/ [00-23]</option>
                                    <option value="12">Hour- [00-23]</option>
                                    <option value="13">Minute/ [00-59]</option>
                                    <option value="14">Minute/ [00-59]</option>
                                    <option value="15">Second/ [00-59]</option>
                                    <option value="16">Second/ [00-59]</option>
                                </select>
                            </div>
                            <input type="hidden" name="text_year" class="text_year"  >
                            <input type="hidden" name="text_month" class="text_month"  >
                            <input type="hidden" name="text_time" class="text_time" >
                        </div>
                        <div class="form-group  row ">
                            <label class="col-md-3 col-form-label ">Current Number :</label>
                            <div class="col-md-8">
                                <div class='basket-card' style="align-content:center;">
                                    <div class="row current_number">
                                        <div class="col-md-1">
                                            <button type="button" class="decrement"  id="decrement" >-</button>
                                        </div>
                                        <div class="col-md-2">
                                            <h3 class="rounded-button qty" id="qty">1</h3>
                                            <input type="hidden" name="current_no" class="current_no" value="1">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="inc"  id="inc" >+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mixcalculation">
                                <label class="col-md-3" for="invoice"> next Invoice Number : </label>
                                <h3 class="rounded-button qty col-md-8" id="invoice_number">1</h3>
                                <input type="hidden" name="invoice" class="invoice" id="invoice" value="1">
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="border p-2" style="background: #ddffff!important">
                        <legend  class="float-none w-auto p-2">Auto Reset Invoice</legend>
                        <label for="formGroupExampleInput">Auto Reset Period :</label>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio"  name="auto_reset_invoice" id="auto_reset_invoice_never" value="1" checked="checked">
                                <label class="form-check-label fs-6" for="auto_reset_invoice_never" >
                                    Never
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio"  name="auto_reset_invoice" id="auto_reset_invoice_monthly" value="3" >
                                <label class="form-check-label fs-6" for="auto_reset_invoice_monthly">
                                    Monthly : auto reset on every 1st day of month.
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio"  name="auto_reset_invoice" id="auto_reset_invoice_yearly" value="2"  >
                                <label class="form-check-label fs-6" for="auto_reset_invoice_yearly">
                                    Yearly : auto reset on every year.
                                </label>
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="starting_number">Auto Reset Starting Invoice Number  :</label>
                            <input type="number" name="starting_number" class="form-control "  id="starting_number" placeholder="Auto Reset Starting Number " >
                        </div>
                    </fieldset>
                    <fieldset class="border p-2" style="background: #ddffff!important">
                        <legend  class="float-none w-auto p-2">Auto Reset Settings</legend>
                        <label for="formGroupExampleInput">Auto Reset Period :</label>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio"  name="auto_reset_period" value="1" id="auto_reset_period_naver" checked="checked">
                                <label class="form-check-label fs-6" for="auto_reset_period_naver" >
                                    Never
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio"  name="auto_reset_period" id="auto_reset_period_yearly_1" value="2"  >
                                <label class="form-check-label fs-6" for="auto_reset_period_yearly_1">
                                    Yearly : auto reset on every 1st January.
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio"  name="auto_reset_period" id="auto_reset_period_yearly_30" value="3" >
                                <label class="form-check-label fs-6" for="auto_reset_period_yearly_30">
                                    Yearly : auto reset on every financial start date. (here it is : 30th June)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio"  name="auto_reset_period" id="auto_reset_period_monthly" value="4" >
                                <label class="form-check-label fs-6" for="auto_reset_period_monthly">
                                    Monthly : auto reset on every 1st day of month.
                                </label>
                            </div>
                        </div>
                        <div class="form-group ">
                            <label for="starting_number1">Auto Reset Starting Number  :</label>
                            <input type="number" name="starting_number" class="form-control "  id="starting_number1" placeholder="Auto Reset Starting Number " >
                        </div>
                    </fieldset>
                    <fieldset class="border p-2" >
                        <legend  class="float-none w-auto p-2">Advance Settings</legend>
                        <div class="form-group advance_setting">
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6 "  >
                <div class="card-block  " style="padding-left:0px;">
                    <div class=" m-t-0 " style="">
                        <div style="margin-left: 0px;">
                            <fieldset class="border p-2">
                                <legend  class="float-none w-auto p-2">Optional Settings</legend>
                                <div class="form-group ">
                                    <label for="branch_id">Unit/Branch :</label>
                                    <select name="branch_id"  class="form-control  js-example-basic-single   left-data" id="branch_id" required>
                                        <option value="0">-- select one --</option>
                                        @foreach ($branch as $data)
                                        <option value="{{$data->id }}">{{ $data->branch_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class=" col-form-label" for="distribution_center_id">Distribution Center / Shop Setup:</label>
                                    <select name="distribution_center_id" id="distribution_center_id" class="form-control status js-example-basic-single" >
                                        @foreach ($distributions as  $distribution)
                                        <option value="{{ $distribution->dis_cen_id}}">{{ $distribution->dis_cen_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group row stock_tranfer_destination">
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="godown_id" class="godown_1">Godown :</label>
                                        <select name="godown_id" id="godown_id"  class="form-control  js-example-basic-single   left-data" required>
                                            <option value="0">-- select one --</option>
                                            @foreach ($godown as $data)
                                            <option value="{{$data->godown_id }}">{{ $data->godown_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="godown_motive"> Godown's Motive :</label>
                                        <select name="godown_motive" id="godown_motive"  class="form-control left-data" required>
                                            <option value="1">Normal[available for each row]</option>
                                            <option value="2">Readonly</option>
                                            <option value="3">Hidden</option>
                                            <option value="4">Top</option>
                                        </select>
                                    </div>                                    
                                </div>
                                <div class="form-group row  stock_jounal_destination">
                                </div>

                                <div class="form-group ">
                                    <label for="select_date">Select Date Time :</label>
                                    <select name="select_date" id="select_date"  class="form-control    select_date" required>
                                        <option value="current_date">Current Date</option>
                                        <option value="last_insert_date">Last Insert Date</option>
                                        <option value="fix_date">Fixt Date</option>
                                    </select>
                                </div>
                                <div class="form-group date_show">
                                </div>
                                <div>
                                    <div class="form-group ">
                                        <label for="debit">Default Debit Ledger :</label>
                                        <select name="debit" id="debit"  class="form-control  js-example-basic-single  " required>
                                            <option value="0">-- select one --</option>
                                            @foreach ( $debitLedger as $debitLedgers)
                                            <option value="{{$debitLedgers->ledger_head_id}}">{{$debitLedgers->ledger_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="margin-left: 30px;">
                                        <div class="form-group">
                                            <label for="debit_group_id_array_1">Group  Range 1:</label>
                                            <select  name="debit_group_id_array[]" id="debit_group_id_array_1" class="form-control  js-example-basic-single   " required>
                                                <option value="0">-- select one --</option>
                                                {!!html_entity_decode($group_chart)!!}
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="debit_group_id_array_2">Group  Range 2:</label>
                                            <select  name="debit_group_id_array[]" id="debit_group_id_array_2" class="form-control  js-example-basic-single  " required>
                                                <option value="0">-- select one --</option>
                                                {!!html_entity_decode($group_chart)!!}
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="debit_group_id_array_3">Group  Range 3:</label>
                                            <select  name="debit_group_id_array[]" id="debit_group_id_array_3"  class="form-control  js-example-basic-single  " required>
                                                <option value="0">-- select one --</option>
                                                {!!html_entity_decode($group_chart)!!}
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="debit_group_id_array_4">Group  Range 4:</label>
                                            <select   name="debit_group_id_array[]" id="debit_group_id_array_4"  class="form-control  js-example-basic-single  " required>
                                                <option value="0">-- select one --</option>
                                                {!!html_entity_decode($group_chart)!!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="form-group ">
                                        <label for="credit">Default Credit Ledger :</label>
                                        <select name="credit" id="credit"  class="form-control  js-example-basic-single   " required>
                                            <option value="0">-- select one --</option>
                                            @foreach ( $creditLedger as $creditLedgers)
                                            <option value="{{$creditLedgers->ledger_head_id}}">{{$creditLedgers->ledger_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="margin-left: 30px;">
                                        <div class="form-group ">
                                            <label for="credit_group_id_array_1">Group  Range 1:</label>
                                            <select  name="credit_group_id_array[]" id="credit_group_id_array_1"  class="form-control  js-example-basic-single" required>
                                                <option value="0">-- select one --</option>
                                                {!!html_entity_decode($group_chart)!!}
                                            </select>
                                        </div>
                                        <div class="form-group ">
                                            <label for="credit_group_id_array_2">Group  Range 2:</label>
                                            <select  name="credit_group_id_array[]" id="credit_group_id_array_2"  class="form-control  js-example-basic-single" required>
                                                <option value="0">-- select one --</option>
                                                {!!html_entity_decode($group_chart)!!}
                                            </select>
                                        </div>
                                        <div class="form-group ">
                                            <label for="credit_group_id_array_3">Group Ledger  Range 3:</label>
                                            <select  name="credit_group_id_array[]" id="credit_group_id_array_3" class="form-control  js-example-basic-single" required>
                                                <option value="0">-- select one --</option>
                                                {!!html_entity_decode($group_chart)!!}
                                            </select>
                                        </div>
                                        <div class="form-group ">
                                            <label for="credit_group_id_array_4">Group  Range 4:</label>
                                            <select  name="credit_group_id_array[]" id="credit_group_id_array_4"  class="form-control  js-example-basic-single" required>
                                                <option value="0">-- select one --</option>
                                                {!!html_entity_decode($group_chart)!!}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label for="price_type_id" class="price_system">Pricing System  :</label>
                                        <select name="price_type_id" id="price_type_id" class="form-control price_type_id">
                                            <option value="1">Selling Price</option>
                                            <option value="2">Purchage/Standard Price </option>
                                            <option value="3">Wholesale Price</option>
                                            <option value="4">POS Price</option>
                                            <option value="5">Last Insert Price</option>
                                            <option value="6">Average Price</option>
                                            <option value="7">FIFO Price</option>
                                        </select>
                                    </div>
                                    <div class="form-group d-none destination">
                                        <label for="destrination_price_type_id">Pricing System : Destination (Production)</label>
                                        <select name="destrination_price_type_id" id="destrination_price_type_id" class="form-control price_type_id">
                                            <option value="1">Selling Price</option>
                                            <option value="2">Purchage/Standard Price </option>
                                            <option value="3">Wholesale Price</option>
                                            <option value="4">POS Price</option>
                                            <option value="5">Last Insert Price</option>
                                            <option value="7">FIFO Price</option>
                                        </select>
                                    </div>
                                    <div class="form-group ">
                                        <label for="commission_type_id">Commission  :</label>
                                        <select name="commission_type_id" id="commission_type_id" class="form-control commission_type_id" required disabled>
                                            <option value=''>-- select one --</option>
                                            <option value="1">Stock Group</option>
                                            <option value="2">Stock_item</option>
                                        </select>
                                    </div>
                                    <div class="form-group ">
                                        <label for="commission_ledger_id">Default Commission Ledger :</label>
                                        <select name="commission_ledger_id" id="commission_ledger_id" class="form-control js-example-basic-single commission_ledger_id" required disabled>
                                            <option value="0">-- select one --</option>
                                            @foreach ( $debitLedger as $debitLedgers)
                                            <option value="{{$debitLedgers->ledger_head_id}}">{{$debitLedgers->ledger_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group d-none" id="sale_type_group">
                                    <label for="sale_type">Sales Type :</label>
                                    <select name="sale_type" class="form-control sale_type" id="sale_type">
                                        <option value="2">Sales Credit</option>
                                        <option value="0">None</option>
                                        <option value="1">Cash Sales</option>
                                        <option value="3">Inter Company Sale</option>
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endslot
    @slot('footer')
        <div class="col-lg-6 ">
            <div class="form-group">
                <button type="submit"  id="add_voucher_btn" class="btn hor-grd btn-grd-primary btn-block submit" style="width:100%" >Add</button>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group">
            <a class=" btn hor-grd btn-grd-success btn-block " href="{{route('master-dashboard')}}" style="width:100%">Close</a>
            </div>
        </div>
    @endslot
 @endcomponent
@push('js')
<!-- table hover js -->
<script>
let company_id="{{unit_branch_first()}}";
$(document).ready(function() {
    $('.voucher_type').on('change',function(){
    let voucher=$(this).val();
    let destination=`<div class="col-md-6">
                        <label for="destination_godown_id">Godown : ${voucher==21?' Destination (Production)':'Destination'}</label>
                        <select name="destination_godown_id" id="destination_godown_id"  class="form-control  js-example-basic-single   left-data" required>
                            <option value="0">-- select one --</option>
                            @foreach ($godown as $data)
                            <option value="{{$data->godown_id }}">{{ $data->godown_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="destination_godown_motive"> Godown's Motive :</label>
                        <select name="destination_godown_motive" id="destination_godown_motive"  class="form-control left-data" required>
                            <option value="1">Normal[available for each row]</option>
                            <option value="2">Readonly</option>
                            <option value="3">Hidden</option>
                            <option value="4">Top</option>
                        </select>
                    </div>`;

        $('.price_system').text('Pricing System  :');
        $('.godown_1').text('Godown :');
    //    voucher voucher_type_id 19 sales  and 20 Stock Journal and  and 22 stock tranfer
    if(voucher==19){
        $('.commission_type_id').prop("disabled",false); 
        $("#sale_type_group").removeClass("d-none");
        $("#sale_type").attr("disabled",false);
    }else{
        $('.commission_type_id').prop("disabled",true);
        $("#sale_type_group").addClass("d-none");
        $("#sale_type").attr("disabled",true);
    }

        if(voucher==10||voucher==19||voucher==23||voucher==24||voucher==25||voucher==29){
            $('.commission_ledger_id').prop("disabled",false);
        }else{
            $('.commission_ledger_id').prop("disabled",true); 
        }

        $('.stock_jounal_destination').html('');
        $('.stock_tranfer_destination').html('');

        if(voucher==21||voucher==22){
            if(voucher==21){
                $('.stock_jounal_destination').html(destination);
                $('.godown_1').text('Godown: Source (Consumption)');
                $('.destination').toggleClass("d-none");
                $('.price_system').text('Pricing System : Source (Consumption)');
               
            }else if(voucher==22){
                $('.stock_tranfer_destination').html(destination)
                $('.godown_1').text('Godown: Source'); 
                $('.destination').toggleClass("d-none");
                $('.destination').addClass("d-none");
            }

        }else{
            $('.destination').addClass("d-none");
        }
        
         // voucher voucher_type_id 1 Contra and 8 Payment and 14  Receipt  
        if(voucher==14||voucher==8||voucher==1 ){
            let data=`${voucher==1?`
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox"  name="dup_row" id="dup_row" checked="checked"  value="1">
                            <label class="form-check-label fs-5" for="dup_row" >
                                Prevent Duplicate Accounts Ledger 
                            </label>
                        </div>`:``}
                    <div class="form-check">
                        <input class="form-check-input dc_amnt" type="checkbox" id="dc_amnt"  name="dc_amnt" checked="checked" value="1">
                        <label class="form-check-label fs-5" for="dc_amnt">
                            Allow Total Dr/Cr Amount is 0
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"  name="amnt_typeable" id="amnt_typeable" checked="checked"  value="1">
                        <label class="form-check-label fs-5" for="amnt_typeable">
                            Allow Larger Amount than a Ledger/Party current Balance
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"  name="ch_4_dup_vou_no"  id="ch_4_dup_vou_no"  value="1" >
                        <label class="form-check-label fs-5" for="ch_4_dup_vou_no">
                            Allow Duplicate " Voucher Number "
                        </label>
                    </div>
                    ${(voucher==14||voucher==8)?`
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"  name="voucher_with_stock_is" id="voucher_with_stock_is"  value="1" >
                            <label class="form-check-label fs-5" for="voucher_with_stock_is">
                                Allow Stock Item/s with this Voucher ?
                            </label>
                        </div>`:``}
                    ${(voucher!=14||voucher!=8||voucher!=6)?`
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"  name="remark_is" checked="checked" value="1" >
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                                Show Remarks field  with each row
                            </label>
                        </div>`:``}`;
            
            $('.advance_setting').html(data);
        }
        let data='';
         //  voucher voucher_type_id 10 Purchase and 6 Journal  and 19  sales  and 20 Stock Journal and 21  Sales Order and 22 stock tranfer  and  23 Goods Transfer Note and 24 Goods Receive Note and 25 Sales Return and 29 purchase return  
        if(voucher==10||voucher==24|voucher==19||voucher==23||voucher==29||voucher==22||voucher==25||voucher==20||voucher==21||voucher==6||voucher==14||voucher==8){
            data+=`${(voucher==6||voucher==14||voucher==8)?`
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"  name="dup_row" id="dup_row" checked="checked"  value="1">
                            <label class="form-check-label fs-5" for="dup_row" >
                                Prevent Duplicate  Accounts Ledger/Stock Item 
                            </label>
                        </div>`:`
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"  name="dup_row" id="dup_row" checked="checked"  value="1">
                            <label class="form-check-label fs-5" for="dup_row" >
                                Prevent Duplicate  Stock Item 
                            </label>
                        </div>`}
                    ${(voucher==21)?`
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"  name="is_pary_ledger" id="is_pary_ledger" checked="checked"  value="1">
                            <label class="form-check-label fs-5" for="is_pary_ledger" >
                                Allow Party Ledger ?
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"  name="bill_of_material_is" id="bill_of_material_is" >
                            <label class="form-check-label fs-5" for="bill_of_material_is" >
                                Bill of Material  ?
                            </label>
                        </div>`:''}
                    ${(voucher==22&&company_id==151)?`
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"   name="st_approval" id="st_approval"   value="1">
                            <label class="form-check-label fs-5" for="st_approval" >
                                Allow Stock Transfer Goods in Transits?
                            </label>
                        </div>`:''}
                    <div class="form-check">
                        <input class="form-check-input total_qty" type="checkbox" id="total_qty"  name="row_wise_qty_is" id="row_wise_qty_is"  value="1">
                        <label class="form-check-label fs-5" for="row_wise_qty_is">
                            Allow  Row Wise Quantity is 0 
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input total_qty" type="checkbox" id="total_qty"  name="total_qty_is" id="total_qty_is" checked="checked" value="1">
                        <label class="form-check-label fs-5" for="total_qty_is">
                            Allow Total  Quantity is 0 
                        </label>
                    </div>
                    ${(voucher==19||voucher==23||voucher==29||voucher==22||voucher==21||voucher==6||voucher==14||voucher==8)?`
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"  name="amnt_typeable" id="amnt_typeable"   value="1">
                            <label class="form-check-label fs-5" for="amnt_typeable">
                                Allow More Quantity Over Current Stock
                            </label>
                        </div>`:``}  
                    <div class="form-check">
                        <input class="form-check-input dc_amnt" type="checkbox" id="dc_amnt"  name="total_price_is" checked="checked" value="1">
                        <label class="form-check-label fs-5" for="dc_amnt">
                            Allow Total Amount is 0 
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"  name="stock_item_price_typeabe" id="stock_item_price_typeabe" checked="checked" value="1" >
                        <label class="form-check-label fs-5" for="stock_item_price_typeabe">
                            Allow Custom Price of Stock Item
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"  name="amount_typeabe" id="amount_typeabe" checked="checked" value="1" >
                        <label class="form-check-label fs-5" for="amount_typeabe">
                            Allow Custom Amount of Stock Item
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"  name="ch_4_dup_vou_no" id="ch_4_dup_vou_no"  value="1" >
                        <label class="form-check-label fs-5" for="ch_4_dup_vou_no">
                            Allow Duplicate " Voucher Number " 
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"  name="remark_is" id="remark_is"  value="1" >
                        <label class="form-check-label fs-5" for="remark_is">
                            Show Remarks field  with each row
                        </label>
                    </div>
                    ${(voucher==19||voucher==20)?`
                        <div class="form-check">
                            <input class="form-check-input commission_is" type="checkbox"  name="commission_is" id="commission_is" checked="checked" value="1" >
                            <label class="form-check-label fs-5" for="commission_is">
                                Allow Commission  with each Product/Item
                            </label>
                        </div>`:``}
                    <div class="form-check">  
                        <input class="form-check-input" type="checkbox"  name="secret_narration_is"   value="1">
                        <label class="form-check-label fs-5" for="flexRadioDefault1" >
                            Allow Secret narration
                        </label>
                    </div>`;
            $('.advance_setting').html(data);
            let com_id=$('.commission_is').prop("checked") ? '' : 1 ;

            $('.commission_type_id').val(com_id);
        }
        // Allow Larger Quantity than a Product/Item Current Stock
    });
    $('.advance_setting').on('click',function(){
        let com_id=$('.commission_is').prop("checked") ? '' : 1 ;
        $('.commission_type_id').val(com_id);
    })
    $('.select_date').on('change',function(){
       var fixt_date=$(this).val();
       if(fixt_date=='fix_date'){
        let data=`<label for="formGroupExampleInput">Fixt Date:</label>
                <input type="date" name="fix_date_create" class="form-control " id="formGroupExampleInput" placeholder="Voucher Name" >`;
        $('.date_show').html(data);
       }else{
        $('.date_show').empty();
       }
    });
    $('.voucher_type_id').on( 'change',function(e) {
        let mumber_method = $(this).val();
       if(mumber_method==2){
        $('.current_number').show();
        $('.mixcalculation').show();
        $(".manual").attr("disabled", false);
       }
      else if(mumber_method==3){
        $('.current_number').show();
        $('.mixcalculation').show();
        $(".manual").attr("disabled", false);
        $(".time_frame").attr("disabled", false);

       }else if(mumber_method==4){
          $('.mixcalculation').hide();
          $('.current_number').hide();
          $('.invoice').val('');
       }else{
        $('.current_number').show();
        $('.mixcalculation').show();
        $(".time_frame").attr("disabled", true);
        $(".manual").attr("disabled", true);
       }

    });

    $('.manual').empty().on( 'keyup',function(e) {
       let mumber_method = $(this).empty().val();
       let g= $('#qty').text();
       mumber_method=mumber_method+g;
       $('#qty1').empty().text(mumber_method);
    });

    $('#year').on('change',function(e){
        var m_names = ['January', 'February', 'March',
               'April', 'May', 'June', 'July',
               'August', 'September', 'October', 'November', 'December'];

        const d = new Date();
       let value=$(this).val();
       $('.text_year').val(timeframe(value,d,m_names));

    });
    $('#month').on('change',function(e){
        var m_names = ['January', 'February', 'March',
               'April', 'May', 'June', 'July',
               'August', 'September', 'October', 'November', 'December'];

        const d = new Date();
       let value=$(this).val();
       $('.text_month').val(timeframe(value,d,m_names));

    });
    $('#time').on('change',function(e){
        var m_names = ['January', 'February', 'March',
               'April', 'May', 'June', 'July',
               'August', 'September', 'October', 'November', 'December'];

        const d = new Date();
       let value=$(this).val();
       $('.text_time').val(timeframe(value,d,m_names));

    });


    $('.inc').click(function(e){
        e.preventDefault();
        let qty=$('#qty').text();
        qty++;
        $('.qty').text(qty);
        $('.current_no').val(qty);
        invoice_number();
    });
    $('.decrement').click(function(e){
        e.preventDefault();
        let qty=$('#qty').text();
        qty--;
        if(qty>=0)$('.qty').text(qty);
        $('.current_no').text(qty);
        invoice_number();
    });

    $('.manual, #year, #month, #time').on( 'keyup change',function(e) {
        invoice_number();
    })
});
function invoice_number(){
    let manual=$('.manual').val();
    let year=$('.text_year').val();
    let month=$('.text_month').val();
    let time=$('.text_time').val();
    let qty=$('#qty').text();
    let text=manual+year+month+time+qty+'';
    $('#invoice_number').empty().text(text);
    $('.invoice').empty().val(text);


}
function timeframe(value,d,m_names){
    let data='';
    if(value==1){
        data=d.getFullYear().toString().substr(-2)+"/";
    }else if(value==2){
    data=d.getFullYear().toString().substr(-2)+"-";

    }
    else if(value==3){
    data=d.getFullYear()+"/";

    }
    else if(value==4){
    data=d.getFullYear()+"-";

    }
    else if(value==5){
    data=("0" + (d.getMonth() + 1)).slice(-2)+"/";

    }
    else if(value==6){
    data=("0" + (d.getMonth() + 1)).slice(-2)+"-";

    }
    else if(value==7){
    data = m_names[d.getMonth()]+"/";

    }
    else if(value==8){
    data =m_names[d.getMonth()]+"-";

    }
    else if(value==9){
    data =d.getDate()+"/";

    }
    else if(value==10){
    data=d.getDate() +"-";

    }
    else if(value==11){
    data =String( d.getHours()).padStart(2, '0')+"/";

    }
    else if(value==12){
    data =String( d.getHours()).padStart(2, '0')+"-";

    }
    else if(value==13){
    data =String(d.getMinutes()).padStart(2, '0')+"/";

    }
    else if(value==14){
    data  =String(d.getMinutes()).padStart(2, '0')+ "-";

    }
    else if(value==15){
    data =String(d.getSeconds()).padStart(2, '0')+"/";

    }
    else if(value==16){
    data  =String(d.getSeconds()).padStart(2, '0')+ "-";

    }
    return data;
}


$(document).ready(function() {
        $('.js-example-basic-single').select2();
    });

$(function() {
    // add new voucher ajax request
    $("#add_voucher_form").submit('turbolinks:request-end',function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#add_voucher_btn").text('Adding...');
        $.ajax({
                url: '{{ route("voucher.store") }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data) {
                    claer_error();
                    swal_message(data.message, 'success', 'Successfully');
                    $("#add_voucher_btn").text('Add Voucher');
                    if({{$redirect}}==0){
                        setTimeout(function () {  window.location.href='{{route("voucher.create")}}'; },100);
                    }else{
                        setTimeout(function () {  window.location.href='{{route("voucher.index")}}'; },100);
                    }

                },
                error : function(data,status,xhr){
                    claer_error();
                    if(data.status==400){
                      swal_message(data.message, 'error', 'Error');
                    } if(data.status==422){
                        claer_error();
                      $('#error_voucher_name').text(data.responseJSON.data.voucher_name[0]);
                    }

                }
        });
    });

});
//data validation data clear
function claer_error(){
    $('#error_voucher_name').text('');
}
function swal_message(data, message, title_mas) {
        swal({
            title: title_mas,
            text: data,
            type: message,
            timer: '1500'
        });
   }
</script>
@endpush
@endsection

