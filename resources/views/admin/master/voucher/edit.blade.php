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
@endphp
<!-- voucher add model  -->
@component('components.create', [
    'title' => 'Accounts Voucher [edit]',
    'help_route'=>route('voucher.index'),
    'close_route'=>route('master-dashboard'),
    'veiw_route'=>route('voucher.index'),
    'form_id' => 'edit_voucher_form',
    'method'=> 'PUT',
])
    @slot('body')
      <div class="row">
        <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6" >
            <div class="card-block ">
                <fieldset class="border p-2">
                    <legend  class="float-none w-auto p-2">General Fields</legend>
                        <div class="form-group">
                            <label for="formGroupExampleInput">Voucher Name:</label>
                            <input type="text" name="voucher_name" class="form-control "  id="formGroupExampleInput" placeholder="Voucher Name"  value="{{$voucher->voucher_name}}">
                            <span id='edit_error_voucher_name'class=" text-danger"></span>
                            <input type="hidden" class="form-control id" name="id"   value="{{$voucher->voucher_id}}">
                        </div>
                        <div class="form-group ">
                            <label for="formGroupExampleInput2">Voucher Type</label>
                            <select name="voucher_type_id"  class="form-control  js-example-basic-single   left-data">
                                <option value="0">-- select one --</option>
                                @foreach ($voucher_types as $voucher_type)
                                <option {{$voucher->voucher_type_id==$voucher_type->voucher_type_id ? 'selected' : ''}} value="{{$voucher_type->voucher_type_id}}">{{$voucher_type->voucher_type}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group ">
                            <label for="formGroupExampleInput2">Category :</label>
                            <select name="category"  class="form-control left-data" >
                                <option {{$voucher->category=='normal' ? 'selected' : ''}} value="normal">Normal</option>
                                <option {{$voucher->category=='All' ? 'selected' : ''}} value="All">All</option>
                                <option {{$voucher->category=='Pos' ? 'selected' : ''}} value="Pos">POS</option>
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="border p-2">
                    <legend  class="float-none w-auto p-2">Number Settings</legend>
                            <div class="form-group ">
                            <label for="formGroupExampleInput2">Number Method :</label>
                            <select name="vouchernumbermethod"  class="form-control  voucher_type_id  left-data" >
                                <option  {{$voucher->vouchernumbermethod==1 ? 'selected' : ''}} value="1">Full Automatic [ Singular Number ]</option>
                                <option  {{$voucher->vouchernumbermethod==2 ? 'selected' : ''}} value="2">Semi Automatic [ Manual Text + Number ]</option>
                                <option  {{$voucher->vouchernumbermethod==3 ? 'selected' : ''}} value="3">Semi Automatic [ Manual Text + Timeframe+ Number ]</option>
                                <option  {{$voucher->vouchernumbermethod==4 ? 'selected' : ''}} value="4">Full Manual</option>
                            </select>
                            </div>
                            <div class="form-group">
                            <label for="formGroupExampleInput">Manual Text :</label>
                            <input type="text" name="manual_text" class="form-control manual"  id="formGroupExampleInput " placeholder="Manual Text" value="{{$voucher->manual_text}}" {{$voucher->manual_text?'':'disabled'}} >
                            </div>

                            <div class="form-group row  "  >
                            <label for="formGroupExampleInput2">Timeframe : </label>
                            <div class="col-md-4">
                                <select name="time_frame_year"  class="form-control  js-example-basic-single   left-data time_frame" id="year"   {{$voucher->time_frame_year?'':'disabled'}}>
                                <option  {{$voucher->time_frame_year==0 ? 'selected' : ''}} value="0">-- select one --</option>
                                <optgroup label="Year">Year</optgroup>
                                <option  {{$voucher->time_frame_year==1 ? 'selected' : ''}} value="1">Year/ [2 digits]</option>
                                <option  {{$voucher->time_frame_year==2 ? 'selected' : ''}} value="2">Year- [2 digits]</option>
                                <option  {{$voucher->time_frame_year==3 ? 'selected' : ''}} value="3">Year/ [4 digits]</option>
                                <option  {{$voucher->time_frame_year==4 ? 'selected' : ''}} value="4">Year- [4 digits]</option>
                                <optgroup label="Month">Month</optgroup>
                                <option  {{$voucher->time_frame_year==5 ? 'selected' : ''}} value="5">Month/ [ 01-12 ]</option>
                                <option  {{$voucher->time_frame_year==6 ? 'selected' : ''}} value="6">Month- [ 01-12] </option>
                                <option  {{$voucher->time_frame_year==7 ? 'selected' : ''}} value="7">Month/ [ Jan-Dec ]</option>
                                <option  {{$voucher->time_frame_year==8 ? 'selected' : ''}} value="8">Month/ [ Jan-Dec ]</option>
                                <optgroup label="Day">Day</optgroup>
                                <option  {{$voucher->time_frame_year==9 ? 'selected' : ''}} value="9">Day/ [01-31 ]</option>
                                <option {{$voucher->time_frame_year==10 ? 'selected' : ''}} value="10">Day/ [01-31 ]</option>
                                <optgroup label="Time">Time</optgroup>
                                <option {{$voucher->time_frame_year==11 ? 'selected' : ''}} value="11">Hour/ [00-23]</option>
                                <option {{$voucher->time_frame_year==12 ? 'selected' : ''}} value="12">Hour- [00-23]</option>
                                <option {{$voucher->time_frame_year==13 ? 'selected' : ''}} value="13">Minute/ [00-59]</option>
                                <option {{$voucher->time_frame_year==14 ? 'selected' : ''}} value="14">Minute/ [00-59]</option>
                                <option {{$voucher->time_frame_year==15 ? 'selected' : ''}}value="15">Second/ [00-59]</option>
                                <option {{$voucher->time_frame_year==16 ? 'selected' : ''}} value="16">Second/ [00-59]</option>
                            </select>
                            </div>
                            <div class="col-md-4">
                                    <select name="time_frame_month"   class="form-control  js-example-basic-single   left-data time_frame" id="month"   {{$voucher->time_frame_month ? '':'disabled'}}>
                                        <option  {{$voucher->time_frame_month==0 ? 'selected' : ''}} value="0">-- select one --</option>
                                        <optgroup  label="Year">Year</optgroup>
                                        <option {{$voucher->time_frame_month==1 ? 'selected' : ''}} value="1">Year/ [2 digits]</option>
                                        <option {{$voucher->time_frame_month==2 ? 'selected' : ''}} value="2">Year- [2 digits]</option>
                                        <option {{$voucher->time_frame_month==3 ? 'selected' : ''}} value="3">Year/ [4 digits]</option>
                                        <option {{$voucher->time_frame_month==4 ? 'selected' : ''}} value="4">Year- [4 digits]</option>
                                        <optgroup label="Month">Month</optgroup>
                                        <option {{$voucher->time_frame_month==5 ? 'selected' : ''}} value="5">Month/ [ 01-12 ]</option>
                                        <option {{$voucher->time_frame_month==6 ? 'selected' : ''}} value="6">Month- [ 01-12] </option>
                                        <option {{$voucher->time_frame_month==7 ? 'selected' : ''}} value="7">Month/ [ Jan-Dec ]</option>
                                        <option {{$voucher->time_frame_month==8 ? 'selected' : ''}} value="8">Month/ [ Jan-Dec ]</option>
                                        <optgroup label="Day">Day</optgroup>
                                        <option {{$voucher->time_frame_month==9 ? 'selected' : ''}} value="9">Day/ [01-31 ]</option>
                                        <option {{$voucher->time_frame_month==10 ? 'selected' : ''}} value="10">Day/ [01-31 ]</option>
                                        <optgroup label="Time">Time</optgroup>
                                        <option {{$voucher->time_frame_month==11 ? 'selected' : ''}} value="11">Hour/ [00-23]</option>
                                        <option {{$voucher->time_frame_month==12 ? 'selected' : ''}}value="12">Hour- [00-23]</option>
                                        <option {{$voucher->time_frame_month==13 ? 'selected' : ''}} value="13">Minute/ [00-59]</option>
                                        <option {{$voucher->time_frame_month==14 ? 'selected' : ''}} value="14">Minute/ [00-59]</option>
                                        <option {{$voucher->time_frame_month==15 ? 'selected' : ''}} value="15">Second/ [00-59]</option>
                                        <option {{$voucher->time_frame_month==16 ? 'selected' : ''}} value="16">Second/ [00-59]</option>
                                    </select>
                            </div>
                            <div class="col-md-4">
                                <select name="time_frame_day"  class="form-control  js-example-basic-single   left-data time_frame" id="time" {{$voucher->time_frame_day?'':'disabled'}} >
                                    <option {{$voucher->time_frame_day==0 ? 'selected' : ''}} value="0">-- select one --</option>
                                    <optgroup label="Year">Year</optgroup>
                                    <option {{$voucher->time_frame_day==1 ? 'selected' : ''}} value="1">Year/ [2 digits]</option>
                                    <option {{$voucher->time_frame_day==2 ? 'selected' : ''}} value="2">Year- [2 digits]</option>
                                    <option {{$voucher->time_frame_day==3 ? 'selected' : ''}} value="3">Year/ [4 digits]</option>
                                    <option {{$voucher->time_frame_day==4 ? 'selected' : ''}} value="4">Year- [4 digits]</option>
                                    <optgroup label="Month">Month</optgroup>
                                    <option {{$voucher->time_frame_day==5 ? 'selected' : ''}} value="5">Month/ [ 01-12 ]</option>
                                    <option {{$voucher->time_frame_day==6 ? 'selected' : ''}} value="6">Month- [ 01-12] </option>
                                    <option {{$voucher->time_frame_day==7 ? 'selected' : ''}} value="7">Month/ [ Jan-Dec ]</option>
                                    <option {{$voucher->time_frame_day==8 ? 'selected' : ''}} value="8">Month/ [ Jan-Dec ]</option>
                                    <optgroup label="Day">Day</optgroup>
                                    <option {{$voucher->time_frame_day==9  ? 'selected' : ''}}value="9">Day/ [01-31 ]</option>
                                    <option {{$voucher->time_frame_day==10 ? 'selected' : ''}} value="10">Day/ [01-31 ]</option>
                                    <optgroup label="Time">Time</optgroup>
                                    <option {{$voucher->time_frame_day==11 ? 'selected' : ''}} value="11">Hour/ [00-23]</option>
                                    <option {{$voucher->time_frame_day==12 ? 'selected' : ''}} value="12">Hour- [00-23]</option>
                                    <option {{$voucher->time_frame_day==13 ? 'selected' : ''}} value="13">Minute/ [00-59]</option>
                                    <option {{$voucher->time_frame_day==14 ? 'selected' : ''}} value="14">Minute/ [00-59]</option>
                                    <option {{$voucher->time_frame_day==15 ? 'selected' : ''}} value="15">Second/ [00-59]</option>
                                    <option {{$voucher->time_frame_day==16 ? 'selected' : ''}} value="16">Second/ [00-59]</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="text_year" class="text_year"  >
                        <input type="hidden" name="text_month" class="text_month"  >
                        <input type="hidden" name="text_time" class="text_time" >
                        <div class="form-group  row current_num">
                            <label class="col-md-3 col-form-label">Current Number :</label>
                            <div class="col-md-8">
                                <div class='basket-card' style="align-content:center;">
                                    <div class="row current_number">
                                        <div class="col-md-1">
                                            <button type="button" class="decrement"  id="decrement" >-</button>
                                        </div>
                                        <div class="col-md-3">
                                            <h3 style="width:100%;" class="rounded-button qty" id="qty">1</h3>
                                            <input type="hidden" name="current_no" class="current_no" value="1">
                                        </div>
                                        <div class="col-md-2">
                                          <button type="button" class="inc"  id="inc" >+</button>
                                        </div>
                                   </div>
                            </div>
                        </div>
                        <div class="form-group row mixcalculation">
                            <label class="col-md-3" for="formGroupExampleInput2 "> next Invoice Number : </label>
                            <h3 class="rounded-button qty col-md-8" id="invoice_number">{{$next_invoice}}</h3>
                            <input type="hidden" name="invoice" class="invoice" value="{{$next_invoice}}">
                        </div>
                    </div>
                    </fieldset>
                    <fieldset class="border p-2" style="background: #ddffff!important">
                        <legend  class="float-none w-auto p-2">Auto Reset Invoice</legend>
                        <label for="formGroupExampleInput">Auto Reset Period :</label>
                            <div class="form-group">
                                <input class="form-check-input" type="radio"  {{ $voucher->auto_reset_invoice ==1 ? 'checked' : ''}}  name="auto_reset_invoice" value="1" checked="checked">
                                <label class="form-check-label fs-6" for="flexRadioDefault1" >
                                    Never
                                </label><br>
                                <input class="form-check-input" type="radio"  {{ $voucher->auto_reset_invoice ==2 ? 'checked' : ''}}  name="auto_reset_invoice" value="2" >
                                <label class="form-check-label fs-6" for="flexRadioDefault1">
                                    Monthly : auto reset on every 1st day of month.
                                </label><br>
                                <input class="form-check-input" type="radio" {{ $voucher->auto_reset_invoice ==3 ? 'checked' : ''}}   name="auto_reset_invoice" value="3"  >
                                <label class="form-check-label fs-6" for="flexRadioDefault1">
                                    Yearly : auto reset on every year.
                                </label><br>
                            </div>
                            <div class="form-group ">
                                <label for="formGroupExampleInput2">Auto Reset Starting Invoice Number  :</label>
                                <input type="number" name="starting_number" class="form-control "  id="formGroupExampleInput" value="{{ $voucher->starting_number??0 }}" placeholder="Auto Reset Starting Invoice Number " >
                            </div>
                    </fieldset>
                    <fieldset class="border p-2" style="background: #ddffff!important">
                    <legend  class="float-none w-auto p-2">Auto Reset Settings</legend>
                       <label for="formGroupExampleInput">Auto Reset Period :</label>
                        <div class="form-group">
                            <input class="form-check-input" {{ $voucher->auto_reset_period ==1 ? 'checked' : ''}} type="radio"  name="auto_reset_period" value="1" >
                            <label class="form-check-label fs-6" for="flexRadioDefault1" >
                                Never
                            </label><br>
                            <input class="form-check-input" {{ $voucher->auto_reset_period ==2 ? 'checked' : ''}} type="radio"  name="auto_reset_period" value="2"  >
                            <label class="form-check-label fs-6" for="flexRadioDefault1">
                                Yearly : auto reset on every 1st January.
                            </label><br>
                            <input class="form-check-input" {{ $voucher->auto_reset_period ==3 ? 'checked' : ''}} type="radio"  name="auto_reset_period" value="3" >
                            <label class="form-check-label fs-6" for="flexRadioDefault1">
                                Yearly : auto reset on every financial start date. (here it is : 30th June)
                            </label><br>
                            <input class="form-check-input" {{ $voucher->auto_reset_period ==4 ? 'checked' : ''}} type="radio"  name="auto_reset_period" value="4" >
                            <label class="form-check-label fs-6" for="flexRadioDefault1">
                                Monthly : auto reset on every 1st day of month.
                            </label><br>
                        </div>
                        <!-- <div class="form-group ">
                            <label for="formGroupExampleInput2">Auto Reset Starting Number  :</label>
                            <input type="number" name="starting_number" class="form-control "  id="formGroupExampleInput" placeholder="Auto Reset Starting Number " >
                        </div> -->
                    </fieldset>
                    <fieldset class="border p-2" >
                    <legend  class="float-none w-auto p-2">Advance Settings</legend>
                        <div class="form-group advance_setting">
                         {{-- voucher voucher_type_id 1 Contra and 8 Payment and 14  Receipt  --}}
                        @if($voucher->voucher_type_id==14||$voucher->voucher_type_id==8||$voucher->voucher_type_id==1)
                             @if($voucher->voucher_type_id==1)
                                <input class="form-check-input"  {{ $voucher->dup_row ==1 ? 'checked' : ''}} type="checkbox"  name="dup_row"  value="1">
                                <label class="form-check-label fs-5" for="flexRadioDefault1" >
                                    Prevent Duplicate Accounts Ledger
                                </label><br>
                              @endif
                            <input class="form-check-input dc_amnt"  {{ $voucher->dc_amnt==1 ? 'checked' : ''}} type="checkbox"  name="dc_amnt"  value="1">
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                              Allow Total Dr/Cr Amount is 0
                            </label><br>
                            <input class="form-check-input"  {{ $voucher->amnt_typeable ==1 ? 'checked' : ''}} type="checkbox"  name="amnt_typeable"  value="1">
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                              Allow Larger Amount than a Ledger/Party current Balance
                            </label><br>
                            <input class="form-check-input" type="checkbox"  name="ch_4_dup_vou_no" {{ $voucher->ch_4_dup_vou_no==1 ? 'checked' : ''}} value="1" >
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                               Allow Duplicate " Voucher Number "
                            </label><br>
                            @if($voucher->voucher_type_id==14||$voucher->voucher_type_id==8)

                                <input class="form-check-input" type="checkbox"  name="voucher_with_stock_is" {{ $voucher->voucher_with_stock_is==1 ? 'checked' : ''}} value="1">
                                <label class="form-check-label fs-5" for="flexRadioDefault1">
                                    Allow Stock Item/s with this Voucher ?
                                </label><br>
                            @endif
                            @if ($voucher->voucher_type_id==1)
                                <input class="form-check-input" type="checkbox"  name="remark_is" {{ $voucher->remark_is==1 ? 'checked' : ''}} value="1">
                                <label class="form-check-label fs-5" for="flexRadioDefault1">
                                Show Remarks field  with each row
                                </label><br>
                            @endif
                        @endif
                        {{-- voucher voucher_type_id 10 Purchase and 6 Journal  and 19  sales  and 20 Stock Journal and 21 Sales Order and  23 Goods Transfer Note and 24 Goods Receive Note and 25 Sales Return and 29 purchase return
                        --}}
                        @if($voucher->voucher_type_id==10||$voucher->voucher_type_id==24||$voucher->voucher_type_id==19||$voucher->voucher_type_id==20||$voucher->voucher_type_id==23||$voucher->voucher_type_id==29||$voucher->voucher_type_id==22||$voucher->voucher_type_id==25||$voucher->voucher_type_id==21||$voucher->voucher_type_id==6||$voucher->voucher_type_id==14||$voucher->voucher_type_id==8)
                           @if($voucher->voucher_type_id==6||$voucher->voucher_type_id==14||$voucher->voucher_type_id==8)
                                <input class="form-check-input" type="checkbox"   {{ $voucher->dup_row ==1 ? 'checked' : ''}} type="checkbox"  name="dup_row"  value="1">
                                <label class="form-check-label fs-5" for="flexRadioDefault1" >
                                    Prevent Duplicate  Stock Item/Accounts Ledger
                                </label><br>
                            @else
                                <input class="form-check-input" type="checkbox"   {{ $voucher->dup_row ==1 ? 'checked' : ''}} type="checkbox"  name="dup_row"  value="1">
                                <label class="form-check-label fs-5" for="flexRadioDefault1" >
                                    Prevent Duplicate  Stock Item
                                </label><br>
                            @endif
                            
                            @if($voucher->voucher_type_id==21)
                                <input class="form-check-input" type="checkbox"   {{ $voucher->is_pary_ledger==1 ? 'checked' : ''}} type="checkbox"  name="is_pary_ledger"  value="1">
                                <label class="form-check-label fs-5" for="flexRadioDefault1" >
                                    Allow Party Ledger
                                </label><br>
                                <input class="form-check-input" type="checkbox"   {{ $voucher->bill_of_material_is==1 ? 'checked' : ''}} type="checkbox"  name="bill_of_material_is"  value="1">
                                <label class="form-check-label fs-5" for="flexRadioDefault1" >
                                    Bill of Material
                                </label><br>
                            @endif
                            @if($voucher->voucher_type_id==22 && unit_branch_first()==151)
                                @if($checking_st_aprove==0)
                                <input class="form-check-input" type="checkbox"   {{ $voucher->st_approval==1 ? 'checked' : ''}} type="checkbox"  name="st_approval"   value="1">
                                @else
                                    <input  type="checkbox"   {{ $voucher->st_approval==1 ? 'checked' : ''}} type="checkbox"  disabled  >
                                    <input type="hidden"    name="st_approval"   value="{{ $voucher->st_approval }}">
                                @endif
                                <label class="form-check-label fs-5" for="flexRadioDefault1" >
                                    Allow Stock Transfer Goods in Transits?
                                </label><br>
                            @endif
                            <input class="form-check-input row_wise_qty" type="checkbox" id="row_wise_qty"  {{ $voucher->row_wise_qty_is ==1 ? 'checked' : ''}}  name="row_wise_qty_is"  value="1">
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                               Allow Row Wise  Quantity is 0
                            </label><br>
                            <input class="form-check-input total_qty" type="checkbox" id="total_qty"  {{ $voucher->total_qty_is ==1 ? 'checked' : ''}}  name="total_qty_is"  value="1">
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                               Allow Total  Quantity is 0
                            </label><br>
                            @if($voucher->voucher_type_id==19||$voucher->voucher_type_id==23||$voucher->voucher_type_id==22||$voucher->voucher_type_id==21||$voucher->voucher_type_id==6||$voucher->voucher_type_id==29||$voucher->voucher_type_id==14||$voucher->voucher_type_id==8)
                            <input class="form-check-input"  {{ $voucher->amnt_typeable ==1 ? 'checked' : ''}} type="checkbox"  name="amnt_typeable"  value="1">
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                              Allow More Quantity Over Current Stock
                            </label><br>
                            @endif
                            <input class="form-check-input dc_amnt" type="checkbox" id="dc_amnt"  {{ $voucher->total_price_is==1 ? 'checked' : ''}}  name="total_price_is"  value="1">
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                              Allow Total Amount is 0
                            </label><br>
                              <input class="form-check-input" type="checkbox"  name="stock_item_price_typeabe"  {{ $voucher->stock_item_price_typeabe==1 ? 'checked' : ''}}  value="1" >
                             <label class="form-check-label fs-5" for="flexRadioDefault1">
                             Allow Custom Price of Stock Item.
                            </label><br>
                            <input class="form-check-input" type="checkbox"  name="amount_typeabe" {{ $voucher->amount_typeabe==1 ? 'checked' : ''}}   value="1" >
                             <label class="form-check-label fs-5" for="flexRadioDefault1">
                             Prevent Custom Amount of Stock Item.
                            </label><br>
                            <input class="form-check-input" type="checkbox"  name="ch_4_dup_vou_no" {{ $voucher->ch_4_dup_vou_no==1 ? 'checked' : ''}} value="1" >
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                              Allow Duplicate " Voucher Number "
                            </label><br>
                            <input class="form-check-input" type="checkbox"   name="remark_is" {{ $voucher->remark_is==1 ? 'checked' : ''}} value="1" >
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                             Show Remarks field  with each row
                            </label><br>
                            @if($voucher->voucher_type_id==19||$voucher->voucher_type_id==20)
                                <input class="form-check-input" type="checkbox"   name="commission_is" {{ $voucher->commission_is==1 ? 'checked' : ''}} value="1" >
                                <label class="form-check-label fs-5" for="flexRadioDefault1">
                                    Allow Commission  with each Product/Item
                                </label><br>
                            @endif
                          @endif
                          <input class="form-check-input" type="checkbox"   {{ $voucher->secret_narration_is==1 ? 'checked' : ''}} type="checkbox"  name="secret_narration_is"  value="1">
                          <label class="form-check-label fs-5" for="flexRadioDefault1" >
                              Allow Secret narration
                          </label><br>
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
                                    <label for="formGroupExampleInput2">Unit/Branch :</label>
                                    <select name="branch_id"  class="form-control  js-example-basic-single   left-data" required>
                                        <option value="0">-- select one --</option>
                                        @foreach ($branch as $data)
                                        <option {{$voucher->branch_id==$data->id ? 'selected' : ''}}  value="{{$data->id }}">{{ $data->branch_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class=" col-form-label ">Distribution Center / Shop Setup:</label>
                                    <select name="distribution_center_id"  class="form-control js-example-basic-single" >
                                        {{-- @dd($distributions); --}}
                                        <option value="0">-- select one --</option>
                                        @foreach ($distributions as  $distribution)
                                          <option {{$distribution->dis_cen_id==$voucher->distribution_center_id ? 'selected' : ''}} value="{{$distribution->dis_cen_id}}" >{{ $distribution->dis_cen_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($voucher->voucher_type_id==22)
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="formGroupExampleInput2">Godown: Destination </label>
                                        <select name="destination_godown_id"  class="form-control  js-example-basic-single   left-data" required>
                                            <option value="0">-- select one --</option>
                                            @foreach ($godown as $data)
                                            <option {{$voucher->destination_godown_id==$data->godown_id ? 'selected' : ''}} value="{{$data->godown_id }}">{{ $data->godown_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="formGroupExampleInput2"> Godown's Motive :</label>
                                        <select name="destination_godown_motive"  class="form-control left-data" required>
                                            <option {{$voucher->destination_godown_motive==1 ? 'selected' : ''}} value="1">Normal[available for each row]</option>
                                            <option {{$voucher->destination_godown_motive==2 ? 'selected' : ''}} value="2">Readonly</option>
                                            <option {{$voucher->destination_godown_motive==3 ? 'selected' : ''}} value="3">Hidden</option>
                                            <option {{$voucher->destination_godown_motive==4 ? 'selected' : ''}} value="4">Top</option>
                                        </select>
                                    </div>
                                </div>
                                @endif
                                <div class="form-group row ">
                                    <div class="col-md-6">
                                        <label for="formGroupExampleInput2">Godown : {{$voucher->voucher_type_id==21 ?'Source (Consumption)':($voucher->voucher_type_id==22 ?'Source':'')}}</label>
                                        <select name="godown_id"  class="form-control  js-example-basic-single   left-data" required>
                                            <option value="0">-- select one --</option>
                                            @foreach ($godown as $data)
                                            <option {{$voucher->godown_id==$data->godown_id ? 'selected' : ''}} value="{{$data->godown_id }}">{{ $data->godown_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="formGroupExampleInput2"> Godown's Motive :</label>
                                        <select name="godown_motive"  class="form-control left-data" required>
                                            <option {{$voucher->godown_motive==1 ? 'selected' : ''}} value="1">Normal[available for each row]</option>
                                            <option {{$voucher->godown_motive==2 ? 'selected' : ''}} value="2">Readonly</option>
                                            <option {{$voucher->godown_motive==3 ? 'selected' : ''}} value="3">Hidden</option>
                                            <option {{$voucher->godown_motive==4 ? 'selected' : ''}} value="4">Top</option>
                                        </select>
                                    </div>                                    
                                </div>
                                @if($voucher->voucher_type_id==21)
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="formGroupExampleInput2">Godown: Destination (Production)</label>
                                        <select name="destination_godown_id"  class="form-control  js-example-basic-single   left-data" required>
                                            <option value="0">-- select one --</option>
                                            @foreach ($godown as $data)
                                            <option {{$voucher->destination_godown_id==$data->godown_id ? 'selected' : ''}} value="{{$data->godown_id }}">{{ $data->godown_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="formGroupExampleInput2"> Godown's Motive :</label>
                                        <select name="destination_godown_motive"  class="form-control left-data" required>
                                            <option {{$voucher->destination_godown_motive==1 ? 'selected' : ''}} value="1">Normal[available for each row]</option>
                                            <option {{$voucher->destination_godown_motive==2 ? 'selected' : ''}} value="2">Readonly</option>
                                            <option {{$voucher->destination_godown_motive==3 ? 'selected' : ''}} value="3">Hidden</option>
                                            <option {{$voucher->destination_godown_motive==4 ? 'selected' : ''}} value="4">Top</option>
                                        </select>
                                    </div>
                                </div>
                                @endif
                                <div class="form-group ">
                                    <label for="formGroupExampleInput2">Select Date Time :</label>
                                    <select name="select_date"  class="form-control select_date" required>
                                        <option {{$voucher->select_date=="current_date" ? 'selected' : ''}} value="current_date">Current Date</option>
                                        <option {{$voucher->select_date=="last_insert_date" ? 'selected' : ''}} value="last_insert_date">Last Insert Date</option>
                                        <option {{$voucher->select_date=="fix_date" ? 'selected' : ''}} value="fix_date">Fixt Date</option>
                                    </select>
                                </div>

                                <div class="form-group date_show">
                                    @if($voucher->fix_date_create)
                                     <label for="formGroupExampleInput">Fixt Date:</label>'
                                    <input type="date" name="fix_date_create" class="form-control " id="formGroupExampleInput" value="{{$voucher->fix_date_create}}" placeholder="Voucher Name" >
                                    @endif
                                </div>
                                <div class="form-group ">
                                    <label for="formGroupExampleInput2">Default Debit Ledger :</label>
                                    <select name="debit"  class="form-control  js-example-basic-single  " required>
                                        <option value="0">-- select one --</option>
                                        @foreach ( $debitLedger as $debitLedgers)
                                          <option  {{ $voucher->debit==$debitLedgers->ledger_head_id ? 'selected' : ''}} value="{{$debitLedgers->ledger_head_id}}">{{$debitLedgers->ledger_name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-group " style="margin-left: 30px;">
                                        <label for="formGroupExampleInput2">Debit Ledger  Range 1:</label>
                                        <select name="debit_group_id_array[]"  class="form-control debit_group_id_1  js-example-basic-single   " required>
                                            <option value="0">-- select one --</option>
                                            {!!html_entity_decode($group_chart)!!}
                                        </select>
                                        <label for="formGroupExampleInput2">Debit Ledger  Range 2:</label>
                                        <select name="debit_group_id_array[]" class="form-control  js-example-basic-single debit_group_id_2  " required>
                                            <option value="0">-- select one --</option>
                                            {!!html_entity_decode($group_chart)!!}
                                        </select>
                                        <label for="formGroupExampleInput2">Debit Ledger  Range 3:</label>
                                        <select name="debit_group_id_array[]"  class="form-control  js-example-basic-single debit_group_id_3  " required>
                                            <option value="0">-- select one --</option>
                                            {!!html_entity_decode($group_chart)!!}
                                        </select>
                                        <label for="formGroupExampleInput2">Debit Ledger  Range 4:</label>
                                        <select name="debit_group_id_array[]"  class="form-control  js-example-basic-single debit_group_id_4  " required>
                                            <option value="0">-- select one --</option>
                                            {!!html_entity_decode($group_chart)!!}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="formGroupExampleInput2">Default Credit Ledger :</label>
                                    <select name="credit"  class="form-control  js-example-basic-single  " required>
                                        <option value="0">-- select one --</option>
                                        @foreach ( $creditLedger as $creditLedgers)
                                          <option {{$voucher->credit==$creditLedgers->ledger_head_id ? 'selected' : ''}} value="{{$creditLedgers->ledger_head_id}}">{{$creditLedgers->ledger_name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-group " style="margin-left: 30px;">
                                        <label for="formGroupExampleInput2">Credit Ledger  Range 1:</label>
                                        <select  name="credit_group_id_array[]"   class="form-control credit_group_id_1  js-example-basic-single   " required>
                                            <option value="0">-- select one --</option>
                                            {!!html_entity_decode($group_chart)!!}
                                        </select>
                                        <label for="formGroupExampleInput2">Credit Ledger  Range 2:</label>
                                        <select  name="credit_group_id_array[]"   class="form-control credit_group_id_2 js-example-basic-single   " required>
                                            <option value="0">-- select one --</option>
                                            {!!html_entity_decode($group_chart)!!}
                                        </select>
                                        <label for="formGroupExampleInput2">Credit Ledger  Range 3:</label>
                                        <select  name="credit_group_id_array[]"   class="form-control credit_group_id_3 js-example-basic-single  " required>
                                            <option value="0">-- select one --</option>
                                            {!!html_entity_decode($group_chart)!!}
                                        </select>
                                        <label for="formGroupExampleInput2">Credit Ledger  Range 4:</label>
                                        <select  name="credit_group_id_array[]"   class="form-control credit_group_id_4 js-example-basic-single  " required>
                                            <option value="0">-- select one --</option>
                                            {!!html_entity_decode($group_chart)!!}
                                        </select>
                                    </div>
                                    <div class="form-group ">
                                        <label for="formGroupExampleInput2">Pricing System : {{$voucher->voucher_type_id==21 ?'Source (Consumption)':'' }} </label>
                                            <select name="price_type_id" class="form-control price_type_id">
                                                <option {{$voucher->price_type_id==1 ? 'selected' : ''}} value="1">Selling Price</option>
                                                <option {{$voucher->price_type_id==2 ? 'selected' : ''}} value="2">Purchage/Standard Price </option>
                                                <option {{$voucher->price_type_id==3 ? 'selected' : ''}} value="3">Wholesale Price</option>
                                                <option {{$voucher->price_type_id==4 ? 'selected' : ''}} value="4">POS Price</option>
                                                <option {{$voucher->price_type_id==5 ? 'selected' : ''}} value="5">Last Insert Price</option>
                                                <option {{$voucher->price_type_id==6 ? 'selected' : ''}} value="6">Average Price</option>
                                                <option {{$voucher->price_type_id===7 ? 'selected' : ''}} value="7">FIFO Price</option>
                                            </select>
                                    </div>
                                    @if($voucher->voucher_type_id==21)
                                    <div class="form-group  destination">
                                        <label for="formGroupExampleInput2">Pricing System : Destination (Production)</label>
                                            <select name="destrination_price_type_id" class="form-control price_type_id">
                                                <option {{$voucher->destrination_price_type_id==1 ? 'selected' : ''}} value="1">Selling Price</option>
                                                <option {{$voucher->destrination_price_type_id==2 ? 'selected' : ''}} value="2">Purchage/Standard Price </option>
                                                <option {{$voucher->destrination_price_type_id==3 ? 'selected' : ''}} value="3">Wholesale Price</option>
                                                <option {{$voucher->destrination_price_type_id==4 ? 'selected' : ''}} value="4">POS Price</option>
                                                <option {{$voucher->destrination_price_type_id==5 ? 'selected' : ''}} value="5">Last Insert Price</option>
                                                <option {{$voucher->destrination_price_type_id==6 ? 'selected' : ''}} value="6">Average Price</option>
                                                <option {{$voucher->destrination_price_type_id==7 ? 'selected' : ''}} value="7">FIFO Price</option>
                                            </select>
                                    </div>
                                    @endif
                                    <div class="form-group ">
                                        <label for="formGroupExampleInput2">Commission  :</label>
                                            <select name="commission_type_id" class="form-control price_type_id">
                                                <option {{$voucher->commission_type_id==0 ? 'selected' : ''}} value="0">-- select one --</option>
                                                <option {{$voucher->commission_type_id==1 ? 'selected' : ''}} value="1">Stock Group</option>
                                                <option {{$voucher->commission_type_id==2 ? 'selected' : ''}} value="2">Stock_item</option>
                                            </select>
                                    </div>
                                    <div class="form-group ">
                                        <label for="formGroupExampleInput2">Default Commission Ledger :</label>
                                            <select name="commission_ledger_id" class="form-control js-example-basic-single commission_ledger_id" required >
                                                 <option value="0">-- select one --</option>
                                                    @foreach ( $debitLedger as $debitLedgers)
                                                    <option value="{{$debitLedgers->ledger_head_id}}">{{$debitLedgers->ledger_name}}</option>
                                                    @endforeach
                                            </select>
                                    </div>
                                </div>
                                <div class="form-group {{$voucher->voucher_type_id==19 ? '' : 'd-none'}}" id="sale_type_group">
                                    <label for="sale_type">Sales Type :</label>
                                    <select name="sale_type" class="form-control sale_type" id="sale_type" {{$voucher->voucher_type_id==19 ? '' : 'disabled'}}>
                                        <option @selected($voucher->sale_type==2) value="2">Sales Credit</option>
                                        <option @selected($voucher->sale_type==0) value="0">None</option>
                                        <option @selected($voucher->sale_type==1) value="1">Cash Sales</option>
                                        <option @selected($voucher->sale_type==3) value="3">Inter Company Sale</option>
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
        <div class="col-lg-4 ">
            <div class="form-group">
                <button type="submit"  id="add_voucher_btn" class="btn hor-grd btn-grd-primary btn-block submit" style="width:100%" >Update</button>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
            <a class=" btn hor-grd btn-grd-success btn-block " href="{{route('master-dashboard')}}" style="width:100%">Close</a>
            </div>
        </div>
        <div class="col-lg-4">
            @if(user_privileges_check('master','Voucher Type','delete_role'))
                <div class="form-group">
                    <button  type="button" class=" btn hor-grd btn-grd-danger deleteIcon "  data-dismiss="modal" style="width:100%">Delete</button>
                </div>
            @endif
        </div>
    @endslot
 @endcomponent
@push('js')
<!-- table hover js -->
<script>
 $('.commission_ledger_id').val("{{$voucher->commission_ledger_id??0 }}");
$('#qty').text('{{$voucher_count?$voucher_count:($voucher->current_no?$voucher->current_no:'')}}');
$(document).ready(function() {
    $('.voucher_type').on('change',function(){
        var voucher=$(this).val();
        if(voucher==19){
            $("#sale_type_group").removeClass("d-none");
            $("#sale_type").attr("disabled",false);
        }else{
            $("#sale_type_group").addClass("d-none");
            $("#sale_type").attr("disabled",true);
        }
        if(voucher==21){
            $('.destination').toggleClass("d-none");
        }else{
            $('.destination').addClass("d-none");
        }

        var data='';
        // voucher voucher_type_id 1 Contra and 8 Payment and 14  Receipt  
        if(voucher==14||voucher==8||voucher==1 ){
            data+='<input class="form-check-input" type="checkbox"  name="dup_row" checked="checked"  value="1">';
            data+='<label class="form-check-label fs-5" for="flexRadioDefault1" >';
            data+='Allow Duplicate Accounts Ledger ?';
            data+='</label><br>';
            data+='<input class="form-check-input dc_amnt" type="checkbox" id="dc_amnt"  name="dc_amnt" checked="checked" value="1">';
            data+=' <label class="form-check-label fs-5" for="flexRadioDefault1">'
            data+=' Allow Total Dr/Cr Amount is 0 ?'
            data+='</label><br>';
            data+='<input class="form-check-input" type="checkbox"  name="amnt_typeable" checked="checked"  value="1">';
            data+='<label class="form-check-label fs-5" for="flexRadioDefault1">';
            data+='Allow Larger Amount than a Ledger/Party current Balance ?';
            data+='</label><br>';
            data+='<input class="form-check-input" type="checkbox"  name="ch_4_dup_vou_no" checked="checked" value="1" >';
            data+='<label class="form-check-label fs-5" for="flexRadioDefault1">';
            data+='Check and Prevent duplicate " Voucher Number " ?';
            data+='</label><br>'
            data+='<input class="form-check-input" type="checkbox"  name="remark_is" checked="checked" value="1" >';
            data+='<label class="form-check-label fs-5" for="flexRadioDefault1">';
            data+='Show Remarks field  with each row ?';
            data+='</label><br>';
            $('.advance_setting').html(data);
        //    voucher voucher_type_id 10 Purchase and 6 Journal  and 19  sales  and 20 Stock Journal and 21 Sales Order and  23 Goods Transfer Note and 24 Goods Receive Note and 25 Sales Return and 29 purchase return  
        }else if(voucher==10||voucher==24|voucher==19||voucher==23||voucher==29||voucher==20||voucher==22||voucher==25||voucher==21||voucher==6){
            data+='<input class="form-check-input" type="checkbox"  name="dup_row" checked="checked"  value="1">';
            data+='<label class="form-check-label fs-5" for="flexRadioDefault1" >';
            data+='Allow Duplicate  Stock Item ?';
            data+='</label><br>';
            data+='<input class="form-check-input total_qty" type="checkbox" id="total_qty"  name="total_qty_is" checked="checked" value="1">';
            data+=' <label class="form-check-label fs-5" for="flexRadioDefault1">'
            data+=' Allow Total  Quantity is 0 ?'
            data+='</label><br>';
            if(voucher==19||voucher==23||voucher==29||voucher==22||voucher==21||voucher==20||voucher==6){
            data+='<input class="form-check-input" type="checkbox"  name="amnt_typeable" checked="checked"  value="1">';
            data+='<label class="form-check-label fs-5" for="flexRadioDefault1">';
            data+='Allow More Quantity Over Current Stock';
            data+='</label><br>';
            }
            data+='<input class="form-check-input dc_amnt" type="checkbox" id="dc_amnt"  name="total_price_is" checked="checked" value="1">';
            data+=' <label class="form-check-label fs-5" for="flexRadioDefault1">'
            data+=' Allow Total Amount is 0 ?'
            data+='</label><br>';
            data+='<input class="form-check-input" type="checkbox"  name="stock_item_price_typeabe" checked="checked" value="1" >';
            data+='<label class="form-check-label fs-5" for="flexRadioDefault1">';
            data+='Allow Custom Price of Stock Item';
            data+='</label><br>'
            data+='<input class="form-check-input" type="checkbox"  name="amount_typeabe" checked="checked" value="1" >';
            data+='<label class="form-check-label fs-5" for="flexRadioDefault1">';
            data+='Allow Custom Amount of Stock Item';
            data+='</label><br>';
            data+='<input class="form-check-input" type="checkbox"  name="ch_4_dup_vou_no" checked="checked" value="1" >';
            data+='<label class="form-check-label fs-5" for="flexRadioDefault1">';
            data+='Check and Prevent duplicate " Voucher Number " ?';
            data+='</label><br>';
            data+='<input class="form-check-input" type="checkbox"  name="remark_is" checked="checked" value="1" >';
            data+='<label class="form-check-label fs-5" for="flexRadioDefault1">';
            data+='Show Remarks field  with each row ?';
            data+='</label><br>'
            if(voucher==19){
            data+='<input class="form-check-input commission_is" type="checkbox"  name="commission_is" checked="checked" value="1" >';
            data+='<label class="form-check-label fs-5" for="flexRadioDefault1">';
            data+='Allow Commission  with each Product/Item ?';
            data+='</label><br>'
            }
            $('.advance_setting').html(data);
            let com_id=$('.commission_is').prop("checked") ? '' : 1 ;

            $('.commission_type_id').val(com_id);
        }
        // Allow Larger Quantity than a Product/Item Current Stock
    });
});
$(document).ready(function () {
        manual();
        let credit='{{$voucher->credit_group_id}}';
        let credit_arr=credit.split(",");
        $('.credit_group_id_1').val(credit_arr[0]).trigger('change');
        $('.credit_group_id_2').val(credit_arr[1]).trigger('change');
        $('.credit_group_id_3').val(credit_arr[2]).trigger('change');
        $('.credit_group_id_4').val(credit_arr[3]).trigger('change');
        $('.credit_group_id_5').val(credit_arr[4]).trigger('change');
     let debit='{{$voucher->debit_group_id}}';
        let debit_arr=debit.split(",");
        $('.debit_group_id_1').val(debit_arr[0]).trigger('change');
        $('.debit_group_id_2').val(debit_arr[1]).trigger('change');
        $('.debit_group_id_3').val(debit_arr[2]).trigger('change');
        $('.debit_group_id_4').val(debit_arr[3]).trigger('change');
        $('.debit_group_id_5').val(debit_arr[4]).trigger('change');
     })

$(document).ready(function() {
    // number_method();
    $('.select_date').on('change',function(){
       var fixt_date=$(this).val();
       var data='';
       if(fixt_date=='fix_date'){
        data+='<label for="formGroupExampleInput">Fixt Date:</label>'
        data+='<input type="date" name="fix_date_create" class="form-control " id="formGroupExampleInput" placeholder="Voucher Name" >';
        $('.date_show').html(data);
       }else{
        $('.date_show').empty();
       }
    });
    $('.voucher_type_id').on( 'change',function(e) {
        manual();
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
function number_method(){
    let mumber_method = $(this).val();
       if(mumber_method==2){
        $(".manual").attr("disabled", false);
       }
      else if(mumber_method==3){
        $(".manual").attr("disabled", false);
        $(".time_frame").attr("disabled", false);

       }else{
        $(".time_frame").attr("disabled", true);
        $(".manual").attr("disabled", true);
       }

}
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
function  manual(){
   var man=$('.voucher_type_id').val();

   if(man==4){
     $('.current_num').hide();
   }else{
    $('.current_num').show();
   }

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
     // update branch ajax request
     $("#edit_voucher_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id = $('.id').val();
        $("#edit_voucher_btn").text('Adding...');
        $.ajax({
            url: "{{ url('voucher') }}" + '/' + id ,
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                    claer_error()
                    swal_message(response.message, 'success', 'Successfully');
                    setTimeout(function () {  window.location.href='{{route("voucher.index")}}'; },100);
                    claer_error()
                    $("#edit_voucher_btn").text('Edit Voucher');

            },
            error : function(data,status,xhr){
                    if(data.status==400){
                        swal_message(data.responseJSON.message, 'error', 'Error');
                    } if(data.status==422){
                        claer_error();
                        $('#edit_error_voucher_name').text(data.responseJSON.data.voucher_name[0]);
                    }

                }
        });
    });

//data validation data clear
function claer_error(){
    $('#edit_error_voucher_name').text('');
}
// delete  ajax request
$(document).on('click', '.deleteIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id = $('.id').val();
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
            buttonsStyling: false,
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('voucher') }}" + '/' + id ,
                    type : "POST",
                    data : {'_method' : 'DELETE', '_token' : csrf_token},
                    success: function (data) {
                        swal_message(data.message, 'success', 'Successfully');
                        location.replace('{{ route("voucher.index") }}');
                    },
                    error: function () {
                        swal_message(data.responseJSON.message, 'error', 'Error');
                    }
                });
            } else if (
                // Read more about handling dismissals
                result.dismiss === swal.DismissReason.cancel
            ) {
                swal(
                    'Cancelled',
                    'Your data is safe :)',
                    'error'
                )
            }
        })
    });

});
function swal_message(data, message, title_mas) {
        swal({
            title: title_mas,
            text: data,
            type: message,
            timer: '1500'
        });
   }
//data validation data clear
function claer_error(){
    $('#edit_error_voucher_name').text('');
}
</script>
@endpush
@endsection

