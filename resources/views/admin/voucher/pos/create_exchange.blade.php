@extends('layouts.backend.app')
@section('title','Exchange')
@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/jquery-ui.theme.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/jquery-ui.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('voucher_setup/voucher_setup.css')}}">
</style>
@endpush
@section('admin_content')
@component('components.voucher', [
    'title' => "$voucher->voucher_name",
    'background_color'=>'#e5e5cd!important',
    'opration'=>'Create',
]);
<!-- Page-header component -->
@slot('voucher_body')
<form id="add_pos" method="POST">
    @csrf
    <div class="page-body">
        <div class="row margin">
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-4 dis" style="float: left;">
                        <label style="float: left; margin: 2px;">Invoice No:</label><br />
                        <br />
                        <label style="float: left; margin: 2px; margin-right: 29px;">Ref No:</label>
                    </div>
                    <div class="col-sm-8 m-0 p-0" style="margin-left: 5px;!important;">
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Invoice No:</label>
                        <input type="text" name="invoice_no" class="form-control m-1" value="{{$voucher_invoice}}" style="border-radius: 15px;" {{$voucher_invoice?'readonly':'autofocus'}} style="color: green" required/>
                        <span id="error_voucher_no" class="text-danger"></span>
                        <input type="hidden" name="voucher_id" class="form-control voucher_id" value="{{$voucher->voucher_id ?? ''}}" />
                        <input type="hidden" name="ch_4_dup_vou_no" class="form-control" value="{{$voucher->ch_4_dup_vou_no ?? ''}}" />
                        <input type="hidden" name="invoice" class="form-control" value="{{$voucher->invoice ?? ''}}" />
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Ref No:</label>
                        <input type="text" name="ref_no" class="form-control m-1" style="border-radius: 15px;" autofocus />
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row" style="margin-top: 5px;">
                    <div class="col-sm-4 dis" style="float: right;">
                        <label style="float: right; margin: 2px;">Payment Type :</label><br />
                        <br />
                        <label style="float: right; margin: 2px; margin-top: 4px;" for="exampleInputEmail1">Card No :</label><br />
                        <br />
                    </div>
                    <div class="col-sm-8 m-0 p-0" style="margin-left: 5px;!important;">
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Payment Type:</label>
                        <select style="border-radius: 15px;" name="payment_type" class="form-control m-1 js-example-basic-single payment_type" required>
                            <option value="">--Select--</option>
                            <option value="1">Cach</option>
                            <option value="2">Visa</option>
                            <option value="3">Master</option>
                            <option value="4">MX</option>
                            <option value="5">Dedit</option>
                            <option value="6">Cash and Back</option>
                        </select>
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Card No :</label>
                        <input type="text" name="card_no" class="form-control card" style="margin-bottom: 4px !important; margin-top: 2px;" disabled required />
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row" style="margin-top: 5px;">
                    <div class="col-sm-4 dis" style="float: right;">
                        <label style="float: right; margin: 2px; margin-right: 82px;">Date:</label><br />
                        <br />
                        <label style="float: right; margin: 2px; margin-right: 26px;" for="exampleInputEmail1">Unit / Branch:</label>
                    </div>
                    <div class="col-sm-8 m-0 p-0" style="margin-left: 5px;!important;">
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Date:</label>
                        <input type="text" name="invoice_date" class="form-control setup_date" style="margin-bottom: 4px !important; border-radius: 15px;" value="{{financial_end_date($voucher_date)}}" />
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Unit / Branch:</label>
                        <select style="margin-top: 2px; border-radius: 15px;" name="unit_or_branch" class="form-control m-1 js-example-basic-single js-example-basic" required>
                            @foreach ($branch_setup as $unit_branchs)
                            <option value="{{ $unit_branchs->id }}">{{$unit_branchs->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row margin">
        <div class="col-sm-4">
            <div class="row" style="margin-top: 5px;">
                <div class="col-sm-4 dis" style="float: right;">
                    <label style="float: left; margin: 2px;">Sales Ledger :</label><br />
                    <br />
                    <label style="float: left; margin: 2px; margin-top: 4px;" for="exampleInputEmail1">Party's A/C Name:</label><br />
                    <br />
                </div>
                <div class="col-sm-8 m-0 p-0" style="margin-left: 5px;!important;">
                    <!-- resposive lebel change -->
                    <label class="display" style="display:none">Sales Ledger:</label>
                    <select style="border-radius: 15px;" name="credit_ledger_id" class="form-control m-1 js-example-basic-single js-example-basic credit_ledger_id" required>
                        <option value="">--Select--</option>
                        @if($voucher->credit!=0?$voucher->credit:'')
                        <option value="{{$voucher->credit}}">{{$ledger_name_credit_wise->ledger_name}}</option>
                        @endif
                    </select>
                    <!-- resposive lebel change -->
                    <label class="display" style="display:none">Party's A/C Name:</label>
                    <label id="debit_amont" style="font-weight: bold; font-size: 18px !important; margin: 2px;">{{$debit_sum_value??'0.000'}}</label>
                    <input
                        type="text"
                        name="debit_ledger_name"
                        id="debit_ledger_name"
                        class="form-control debit_ledger_name"
                        value="{{$ledger_name_debit_wise->ledger_name??''}}"
                        style="margin-bottom: 4px !important; margin-top: 2px;"
                        required
                    />
                    <span id="error_inventory_value_affected" class="text-danger"></span>
                    <input type="hidden" name="debit_ledger_id" id="debit_ledger_id" class="form-control debit_ledger_id" value="{{$voucher->debit??''}}" style="margin-bottom: 4px !important; border-radius: 15px; margin-top: 2px;" />
                </div>
            </div>
        </div>
        <div class="col-sm-4 {{$voucher->godown_motive==3?'d-none ':'' }}">
            <div class="row" style="margin-top: 5px;">
                <div class="col-sm-4 dis" style="float: right;">
                    <label style="float: right; margin: 2px;">Payment by Card :</label><br />
                    <br />
                    <label style="float: right; margin: 2px; margin-top: 4px;" for="exampleInputEmail1">Godowns :</label><br />
                    <br />
                </div>
                <div class="col-sm-8 m-0 p-0" style="margin-left: 5px;!important;">
                    <!-- resposive lebel change -->
                    <label class="display" style="display:none">Payment by Card :</label>
                    <input type="text" name="paymrnt_card" id="paymrnt_card" class="form-control paymrnt_card" style="margin-bottom: 4px !important; margin-top: 2px;" disabled />
                    <!-- resposive lebel change -->
                    <label class="display" style="display:none">Godowns :</label>
                    <select name="godown" class="form-control js-example-basic-single godown left-data" required>
                        @if($voucher->godown_motive==3)
                        <option value="0"></option>
                        @else @foreach ($godowns as $godown)
                        <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                        @endforeach @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="exampleInputEmail1"> APPR Code :</label>
                <input type="text" name="appr_code" id="appr_code" class="form-control appr_code" style="margin-bottom: 4px !important; margin-top: 2px;" disabled />
                <label for="exampleInputEmail1"> Shop Name :</label>
                <select name="dis_cen_id" class="form-control js-example-basic-single dis_cen_id" required>
                    @foreach ($distributionCenter as $distribution)
                    <option value="{{$distribution->dis_cen_id }}">{{$distribution->dis_cen_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <fieldset style="border: 1px rgb(32, 30, 30) solid; margin: 3px;">
                <legend>Return Product/s</legend>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="row">
                            <div class="col-sm-2">
                                <label style="float: left; margin-left: 2px; font-weight: bold;">Invoice No </label>
                            </div>
                            <div class="col-sm-4 m-0 p-0">
                                <input type="text" name="invoice_no" class="form-control m-1 exchange_invoice_no" style="color: green;" required />
                            </div>
                            <div class="col-sm-6">
                                <div style="display: flex; flex-direction: row;">
                                    <button type="button" class="btn btn-info exchange-invoice" style="width: 116px; border-radius: 15px;">Submit</button>
                                    <p class="invoice_not_found m-2 d-none" style="color: #ff6666;">Invoice Not Found</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table customers" style="border: none !important; margin-top: 5px;">
                    <thead>
                        <tr>
                            <th class="col-0.5">#</th>
                            <th class="th col-4">Product Name</th>
                            <th class="th col-2">Godown</th>
                            <th class="th col-1">Quantity</th>
                            <th class="th col-1">Price</th>
                            <th class="th col-1">Per</th>
                            <th class="th col-3">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="exchange"></tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td colspan="1" class="text-right">Total:</td>
                            <td></td>
                            <td><input type="text " style="border-radius: 15px; font-weight: bold;" class="total_invoice_qty form-control text-right" readonly /></td>
                            <td></td>
                            <td></td>
                            <td><input type="number" name="invoice_total_amount" step="any" style="border-radius: 15px; font-weight: bold;" class="total_invoice_amount form-control text-right" readonly /></td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
            <fieldset style="border: 1px rgb(32, 30, 30) solid; margin: 3px;">
                <legend>Sales Product/s</legend>
                <table class="table customers" style="border: none !important; margin-top: 5px;">
                    <thead>
                        <tr>
                            <th class="col-0.5">#</th>
                            <th class="th col-4">Product Name</th>
                            <th class="th col-2 {{$voucher->godown_motive==3?'d-none':'' }}{{$voucher->godown_motive==4?'d-none':'' }}">Godown</th>
                            <th class="th col-1">stock</th>
                            <th class="th col-1">Quantity</th>
                            <th class="th col-1">Price</th>
                            <th class="th col-1 text-right">Discount</th>
                            <th class="th col-2">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="orders"></tbody>
                    <tfoot>
                        <tr>
                            <td class="m-0 p-0"></td>
                            <td colspan="1" class="text-right">Change Amount :</td>
                            <td></td>
                            @if($voucher->godown_motive==1)
                            <td></td>
                            @elseif($voucher->godown_motive==2)
                            <td></td>
                            @endif
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><input type="number" name="total_amount" step="any" style="border-radius: 15px; font-weight: bold;" class="form-control text-right change_amount" /></td>
                        </tr>
                    </tfoot>
                    <tfoot>
                        <tr>
                            <td class="m-0 p-0"><button type="button" id="add" class="btn btn-success cicle m-0 py-1">+</button></td>
                            <td colspan="1" class="text-right">Total:</td>
                            <td></td>
                            <td></td>
                            <td><input type="text " style="border-radius: 15px; font-weight: bold;" class="total_qty form-control text-right" readonly /></td>
                            <td></td>
                            <td></td>
                            <td><input type="number" name="total_amount" step="any" style="border-radius: 15px; font-weight: bold;" class="total_amount form-control text-right" readonly /></td>
                        </tr>
                    </tfoot>
                    <tfoot>
                        <tr>
                            <td class="m-0 p-0"></td>
                            <td colspan="1" class="text-right">Net Payable :</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><input type="number" name="total_amount" step="any" style="border-radius: 15px; font-weight: bold;" class="total_amount form-control text-right" readonly /></td>
                        </tr>
                    </tfoot>
                    <tfoot>
                        <tr>
                            <td class="m-0 p-0"></td>
                            <td colspan="1" class="text-right">Received Amount :</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><input type="number" name="amount_received" step="any" style="border-radius: 15px; font-weight: bold;" class="amount_received form-control text-right" /></td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
            <div class="row" style="margin: 3px;">
                <label style="margin-left: 2px;">Narration:</label>
                <textarea style="margin: 15px;" name="narration" rows="2.5" cols="2.5" class="form-control"></textarea>
            </div>
        </div>
        @if($voucher->secret_narration_is)
            <div class="col-sm-12 mb-1">
                <label style="margin-left: 2px;">Secret Narration:</label>
                <textarea style="margin-left: 2px; border-radius: 15px;" name="secret_narration" rows="2.5" cols="2.5" class="form-control"></textarea>
            </div>
        @endif
    </div>
    <div align="center">
        <button type="submit" class="btn btn-info add_pos" style="width: 116px; border-radius: 15px;">
            <span class="m-1 m-t-1" style="color: #404040;"><i class="fa fa-save" style="font-size: 18px;"></i></span><span>Save</span>
        </button>
        <a class="btn btn-danger" style="border-radius: 15px;" href="{{route('voucher-dashboard')}}">
            <span class="m-1 m-t-1" style="color:#404040;!important"><i class="fa fa-times-circle" style="font-size: 20px;"></i></span><span>Cancel</span>
        </a>
    </div>
</form>
@endslot
@endcomponent
@push('js')
<script type="text/javascript" src="{{asset('libraries/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{asset('voucher_setup/vocher_setup_sales.js')}}"></script>
<script>
let lilineComRowCount;
@if($voucher->credit!=0?$voucher->credit:'')
    $('.credit_ledger_id').val('{{$voucher->credit}}');
@endif
  let amount_decimals="{{company()->amount_decimals}}";
  let check_current_stock= "{{$voucher->amnt_typeable ?? ''}}";
  let stock_item_price_typeabe="{{$voucher->stock_item_price_typeabe ?? ''}}";
  let total_qty_is="{{$voucher->total_qty_is ?? ''}}";
  let total_price_is="{{$voucher->total_price_is ?? ''}}";
  let amount_typeabe="{{$voucher->amount_typeabe ?? ''}}";
  let godown_motive="{{$voucher->godown_motive ?? ''}}";
  let dup_row="{{$voucher->dup_row ?? ''}}";

// exchange invoice data
$('.exchange-invoice').on('click',function(){
   $.ajax({
            url: '{{ route("voucher-exchange-invoice") }}',
                method: 'GET',
                data: {
                    invoice_no:$('.exchange_invoice_no').val()
                },
                dataType: 'json',
                success: function(response) {
                    if(response.data.length>0){
                        $.each(response.data, function(i,val) {
                            $('#exchange').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${i}">
                                        <input class="form-control  invoice_stock_out_id m-0 p-0"  name="invoice_stock_out_id[]" type="hidden" data-type="stock_out_id" value="${val.stock_out_id}" id="stock_in_id_${i}"  for="${i}"/>
                                        <input class="form-control  invoice_product_id m-0 p-0"  name="invoice_product_id[]" type="hidden" data-type="product_id"  value="${val.stock_item_id}" id="product_id_${i}"  for="${i}"/>
                                        <td  class="m-0 p-0"><button  type="button" name="remove" id="${i}" class="btn btn-danger btn_remove cicle m-0  py-1">-</button></td>
                                        <td  class="m-0 p-0">
                                            <input class="form-control product_name  autocomplete_txt"  data-field-name="product_name"  type="text" data-type="product_name" value="${val.product_name}" id="product_name_${i}"  autocomplete="off" for="${i}"  />
                                        </td>
                                        <td  class="m-0 p-0">
                                            <input class="form-control godown_name autocomplete_txt "   value="${val.godown_name}"   data-field-name="godown_name" type="text"  id="godown_name_${i}"  for="${i}"   autocomplete="off" />
                                            <input class="form-control godown_id text-right " name="invoice_godown_id[]"  data-field-name="godown_id" value="${val.godown_id}" type="hidden"  id="godown_id_${i}"  for="${i}" readonly />
                                        </td>
                                        <td  class="m-0 p-0">
                                            <input class="form-control invoice_qty text-right" name="invoice_qty[]"  data-field-name="qty" 
                                            type="number" value="${val.qty}"  step="any" id="qty_${i}"  for="${i}"  />
                                        </td>
                                        <td  class="m-0 p-0">
                                            <input class="form-control rate text-right"  name="invoice_rate[]" step="any" data-field-name="rate" 
                                            type="number" value="${val.rate}" data-type="rate" id="rate_${i}"  for="${i}"  />
                                        </td>
                                        <td  class="m-0 p-0" style="display: flex;flex-direction: row;">
                                            <input class="form-control per "  data-field-name="per" type="text" data-type="per" value="${val.symbol}"  id="per_${i}"  for="${i}" readonly />
                                            <input class="form-control discont "  name="invoice_disc[]" data-field-name="discont" type="text" data-type="discont" value="${val.disc??0}" id="discont_${i}"  for="${i}"  />
                                            <input class="form-control measure_id "  name="invoice_measure_id[]" data-field-name="measure_id" type="hidden" data-type="measure_id"  value="${val.symbol}" id="measure_id_${i}"  for="${i}" readonly />
                                        </td>
                                        <td  class="m-0 p-0">
                                            <input class="form-control invoice_amount  text-right" type="number" step="any"  name="invoice_amount[]" id="amount_${i}" value="${val.total}" for="${i}"/>
                                        </td>
                            </tr>`);
                            $('.invoice_not_found').addClass("d-none");
                       });
                    }else{
                     $('.invoice_not_found').removeClass("d-none");
                    }
                    let invoice_qty=0;
                    let invoice_amount=0
                    $('#exchange tr').each(function(i){
                    if(parseFloat($(this).find('.invoice_qty').val())) invoice_qty+=parseFloat($(this).find('.invoice_qty').val());
                    if(parseFloat($(this).find('.invoice_amount').val())) invoice_amount+=parseFloat($(this).find('.invoice_amount').val());
                })
                $('.total_invoice_qty').val(parseFloat(invoice_qty).toFixed(amount_decimals));
                $('.total_invoice_amount').val(parseFloat(invoice_amount).toFixed(amount_decimals));
                },
                error : function(data,status,xhr){
                    $('.invoice_not_found').removeClass("d-none");
                }
        });
})
$(document).ready(function(){

  let rowCount=1;
    addrow ();
    $('#add').click(function() {
      rowCount+=5;
      addrow (rowCount);
    });

    function getId(element){
      var id, idArr;
      id = element.attr('id');
      idArr = id.split("_");
      return idArr[idArr.length - 1];
    }
    var arr = [];
    $(document).on('click', '.btn_remove', function() {
      var button_id = $(this).attr('id');
      $('#row'+button_id+'').remove();
    });

function addrow (rowCount)
 {
      if(rowCount==null){
          rowCount=1;
      }else{
          rowCount=rowCount;
      }
      let godown_id=$('.godown').val();
      let godown_name=checkGodownValidity($('.godown option:selected').text())
      for(var row=1; row<6;row++) {
          rowCount++;
              $('#orders').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${rowCount}">
                  <input class="form-control  product_id m-0 p-0"  name="product_id[]" type="hidden" data-type="product_id" id="product_id_${rowCount}"  for="${rowCount}"/>
                  <td  class="m-0 p-0"><button  type="button" name="remove" id="${rowCount}" class="btn btn-danger btn_remove cicle" style="padding: 0px 19px;margin:1px 0px;">-</button></td>
                  <td  class="m-0 p-0">
                      <input class="form-control product_name  autocomplete_txt" name="product_name[]" data-field-name="product_name"  type="text" data-type="product_name" id="product_name_${rowCount}"  autocomplete="off" for="${rowCount}"  />
                  </td>
                  <td  class="m-0 p-0 ${godown_motive==3?'d-none':''} ${godown_motive==4?'d-none':''}">
                      <input class="form-control godown_name autocomplete_txt " name="godown_name[]" value="${godown_name}"  data-field-name="godown_name" type="text"  id="godown_name_${rowCount}"  for="${rowCount}" ${godown_motive==2?'readonly':''}  autocomplete="off" />
                      <input class="form-control godown_id text-right " name="godown_id[]"  data-field-name="godown_id" value="${godown_id}" type="hidden"  id="godown_id_${rowCount}"  for="${rowCount}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control stock text-right"   data-field-name="stock" type="number"
                       step="any"  id="stock_${rowCount}"  for="${rowCount}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control qty text-right " name="qty[]" step="any" data-field-name="qty" type="number" 
                       id="qty_${rowCount}"  for="${rowCount}"  />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control rate text-right "  name="rate[]" step="any" data-field-name="rate" type="number" data-type="rate" id="rate_${rowCount}"  for="${rowCount}"  />
                  </td>
                  <td  class="m-0 p-0" style="display: flex;flex-direction: row;">
                      <input class="form-control per "  name="per[]" data-field-name="per" type="text" data-type="per" id="per_${rowCount}"  for="${rowCount}" readonly />
                      <input class="form-control discont "  name="disc[]" data-field-name="discont" type="text" data-type="discont" id="discont_${rowCount}"  for="${rowCount}"  />
                      <input class="form-control measure_id "  name="measure_id[]" data-field-name="measure_id" type="hidden" data-type="measure_id" id="measure_id_${rowCount}"  for="${rowCount}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control amount  text-right" type="number" step="any"  name="amount[]" id="amount_${rowCount}" ${amount_typeabe==1?'readonly':''}  for="${rowCount}"/>
                  </td>
            </tr>`);
      }

  }

  function calculation_total(){
      let qty=0;
      let amount=0;
      $('#orders tr').each(function(i){
          if(parseFloat($(this).find('.qty').val())) qty+=parseFloat($(this).find('.qty').val());
          if(parseFloat($(this).find('.amount').val())) amount+=parseFloat($(this).find('.amount').val());
      })
      $('.total_qty').val(parseFloat(qty).toFixed(amount_decimals));
      $('.total_amount').val(parseFloat(amount).toFixed(amount_decimals));
      received_amount();
      // setting checking is total qty and price
      if(total_qty_is==0){
          if(qty==0){
            $(":submit").attr("disabled", true);
          }else{
            $(":submit").attr("disabled", false);
          }
      }
      if( total_price_is==0){
          if(amount==0){
            $(":submit").attr("disabled", true);
          }else{
            $(":submit").attr("disabled", false);
          }
      }
  }

  $('#orders').on('keyup change','.qty,.rate,.discont',function(){
       let qty;
       if(check_current_stock==0){
        if(parseFloat($(this).closest('tr').find('.stock').val())>=($(this).closest('tr').find('.qty').val())){
             qty=$(this).closest('tr').find('.qty').val();
        }else{
            $(this).closest('tr').find('.qty').val('');
             qty=0;
        }

       }else{
          qty=$(this).closest('tr').find('.qty').val();
       }
       let rate=$(this).closest('tr').find('.rate').val();
       let discount=$(this).closest('tr').find('.discont').val();

       $(this).closest('tr').find('.amount').val(((parseFloat(qty*rate)))-((parseFloat((qty*rate)*(discount||0)))/100));
      calculation_total();
  });

  $('#orders').on('keyup change','.amount',function(){
    // setting checking is amount_typeabe
      if(amount_typeabe==0){
        calculation_total();
        $(this).closest('tr').find('.rate').val(parseFloat($(this).closest('tr').find('.amount').val())/parseFloat($(this).closest('tr').find('.qty').val()));
      }
  });

  $(document).on('click', '.btn_remove', function() {
    calculation_total();
 });



  function getId(element){
      var id, idArr;
      id = element.attr('id');
      idArr = id.split("_");
      return idArr[idArr.length - 1];
    }

    var item_check =[];

  function handleAutocomplete() {

      var fieldName, currentEle
      currentEle = $(this);
      fieldName = currentEle.data('field-name');
      if(typeof fieldName === 'undefined') {
          return false;
      }

      currentEle.autocomplete({
          delay: 500,
          source: function( data, cb ) {
              $.ajax({
                  url: '{{route("searching-item-data") }}',
                  method: 'GET',
                  dataType: 'json',
                  data: {
                      name:  data.term,
                      fieldName: fieldName,
                      voucher_id:"{{$voucher->voucher_id}}",
                  },
                  success: function(res){
                      var result;
                      result = [
                          {
                              label: 'There is no matching record found for '+data.term,
                              value: ''
                          }
                      ];
                      if (res.length) {
                          result = $.map(res, function(obj){

                              return {
                                  label: obj[fieldName],
                                  value: obj[fieldName],
                                  data : obj
                              };
                          });
                      }
                      cb(result);
                  }
              });
          },
          autoFocus: true,
          minLength: 1,
          change: function (event, ui) {
               if (ui.item == null) {
                    if($(this).attr('name')==='product_name[]')$(this).closest('tr').find('.product_id').val('');
                    $(this).focus();
                    check_item_null({{$voucher->total_qty_is ?? ''}},0);
                }
           },
          select: function( event, selectedData ) {
            if(checkDuplicateItem(selectedData?.item?.data?.stock_item_id)){
                    currentEle.val('');
                }else if(selectedData && selectedData.item && selectedData.item.data){
                  var rowNo, data;
                  rowNo = getId(currentEle);
                  data = selectedData.item.data;
                  check_item_null({{$voucher->total_qty_is ?? ''}},1);
                  currentEle.css({backgroundColor: 'white'});
                    if(data.godown_id){
                            $('#godown_name_'+rowNo).val(data.godown_name);
                            $('#godown_id_'+rowNo).val(data.godown_id);
                            current_stock(rowNo,$(this).closest('tr').find('.product_id').val(),data.godown_id,'{{url("current-stock") }}')
                        }
                        if(data.stock_item_id){
                            $('#product_id_'+rowNo).val(data.stock_item_id);
                            $('#per_'+rowNo).val(data.symbol);
                            $('#measure_id_'+rowNo).val(data.unit_of_measure_id);
                            current_stock(rowNo,data.stock_item_id,$(this).closest('tr').find('.godown_id').val(),'{{url("current-stock") }}')
                            // stock item get price
                            $.ajax({
                                url: '{{route("pos-stock-item-price") }}',
                                method: 'GET',
                                dataType: 'json',
                                async: false,
                                data: {
                                    stock_item_id:data.stock_item_id,
                                    voucher_id:"{{$voucher->voucher_id}}",
                                    shop_id:$('.dis_cen_id').val(),
                                },
                                success: function(response){
                                    if(response){
                                        if(response.rate){
                                            $('#rate_'+rowNo).val(response.rate);
                                            selected_auto_value_change(check_current_stock,currentEle,response.rate,amount_decimals);
                                            calculation_total();
                                        }else{
                                            $('#rate_'+rowNo).val(0);
                                            selected_auto_value_change(check_current_stock,currentEle,0,amount_decimals);
                                            calculation_total();
                                        }
                                        if(response.discount){
                                            $('#discont_'+rowNo).val(response.discount);

                                                calculation_total();
                                        }else{

                                            $('discont_'+rowNo).val(0);
                                                calculation_total();
                                        }
                                    }else{
                                        $('#rate_'+rowNo).val(0);
                                        selected_auto_value_change(check_current_stock,currentEle,0,amount_decimals);
                                        calculation_total();

                                    }
                                }
                           });
                        }
                    }
          }
      });
  }
function registerEvents() {
$(document).on('focus','.autocomplete_txt', handleAutocomplete);
  }
registerEvents();
});
</script>
<script>
$(document).ready(function(){
        $("#add_pos").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $(".add_pos").text('Add');
        $.ajax({
                url: '{{ route("voucher-pos-exchange.store") }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data,status,xhr) {
                    swal_message(data.message,'success','Successfully');
                    setTimeout(function () { location.reload() },100);
                    $('#error_voucher_no').text('');
                },
                error : function(data,status,xhr){
                    if(data.status==404){
                        swal_message(data.responseJSON.message,'error','Error');
                    } if(data.status==422){
                        $('#error_voucher_no').text(data.responseJSON.data.invoice_no[0]);

                    }
                }
        });
    });

});
input_checking('godown');
input_checking('product');

// payment type chacking
$('.payment_type').on('change',function(){
   let payment_type=$('.payment_type').val();
   if(payment_type==2||payment_type==3||payment_type==4||payment_type==5){
      $('.card').prop('disabled', false);
      $('.appr_code').prop('disabled', false);
      $('.paymrnt_card').prop('disabled',true);
   }else if(payment_type==6){
      $('.card').prop('disabled', false);
      $('.appr_code').prop('disabled', false);
      $('.paymrnt_card').prop('disabled', false);
   }
});

$('.amount_received').on('keyup',function(){
    received_amount();
})

function received_amount(){
   let change_amount=($('.amount_received').val())-($('.total_amount').val());
   $('.change_amount').val(change_amount);
}

</script>
@endpush
@endsection
