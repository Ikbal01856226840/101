
@extends('layouts.backend.app')
@section('title','Voucher Sales Return')
@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/jquery-ui.theme.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/jquery-ui.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('voucher_setup/voucher_setup.css')}}">
@endpush
@section('admin_content')
@component('components.voucher', [
    'title' => "$voucher->voucher_name",
    'background_color'=>'#e5e5cd!important',
    'opration'=>'Update',
]);
<!-- Page-header component -->
@slot('voucher_body')
<form id="edit_sales_return_id" method="POST">
    @csrf {{ method_field("PUT") }}
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
                        <input type="text" name="invoice_no" class="form-control m-1" value="{{$data->invoice_no}}" style="border-radius: 15px;" style="color: green;" required />
                        <span id="error_voucher_no" class="text-danger"></span>
                        <input type="hidden" name="ch_4_dup_vou_no" class="form-control" value="{{$voucher->ch_4_dup_vou_no ?? ''}}" />
                        <input type="hidden" name="invoice" class="form-control" value="{{$voucher->invoice ?? ''}}" />
                        <input type="hidden" name="voucher_id" class="form-control voucher_id" value="{{$voucher->voucher_id ?? ''}}" />
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Ref No:</label>
                        <input type="text" name="ref_no" class="form-control m-1" value="{{$data->ref_no}}" style="border-radius: 15px;" />
                        <input type="hidden" name="delete_stock_out_id" class="form-control delete_stock_out_id" />
                        <input type="hidden" name="delete_debit_credit_id" class="form-control delete_debit_credit_id" />
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row" style="margin-top: 5px;">
                    <div class="col-sm-4 dis" style="float: right;">
                        <label style="float: right; margin: 2px;">Ledger Name :</label><br />
                        <br />

                        <label style="float: right; margin: 2px; margin-top: 4px;" for="exampleInputEmail1">Party's A/C Name:</label><br />
                        <br />
                    </div>
                    <div class="col-sm-8 m-0 p-0" style="margin-left: 5px;!important;">
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Ledger Name :</label>
                        <input type="hidden" name="debit_id" value="{{$debit->debit_credit_id??0}}" class="form-control debit_id" />
                        <select style="border-radius: 15px;" name="debit_ledger_id" class="form-control m-1 js-example-basic-single js-example-basic debit_ledger_id" required>
                            <option value="">--Select--</option>
                            @if($voucher->debit!=0?$voucher->debit:'')
                            <option value="{{$voucher->debit}}">{{$ledger_name_debit_wise->ledger_name}}</option>
                            @endif {!!html_entity_decode($ledger_tree)!!}
                        </select>
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Party's A/C Name:</label>
                        <input type="hidden" name="credit_id" value="{{$credit->debit_credit_id}}" class="form-control debit_id" />
                        <label id="credit_amont" style="font-weight: bold; font-size: 18px !important; margin: 2px;">{{$credit_sum_value??'0.000'}}</label>
                        <input type="text" name="debit_ledger_name" id="debit_ledger_name" class="form-control debit_ledger_name" value="{{$credit->ledger_name??0}}" style="margin-bottom: 4px !important; margin-top: 2px;" required />
                        <span id="error_inventory_value_affected" class="text-danger"></span>
                        <input
                            type="hidden"
                            name="credit_ledger_id"
                            value="{{$credit->ledger_head_id??0}}"
                            id="credit_ledger_id"
                            class="form-control credit_ledger_id"
                            style="margin-bottom: 4px !important; border-radius: 15px; margin-top: 2px;"
                        />
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
                        <input type="text" name="invoice_date" class="form-control setup_date invoice_date" style="margin-bottom: 4px !important; border-radius: 15px;" value="{{$data->transaction_date}}" />
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Unit / Branch:</label>
                        <select style="margin-top: 2px; border-radius: 15px;" name="unit_or_branch" class="form-control m-1 js-example-basic-single js-example-basic unit_or_branch" required>
                            <option value="0">--Select--</option>
                            @foreach ($branch_setup as $unit_branchs)
                            <option value="{{$unit_branchs->id }}">{{$unit_branchs->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            @if (company()->customer_id!=0)
            <div class="form-group">
                <label for="exampleInputEmail1">Customer :</label>
                <select name="customer_id" class="form-control js-example-basic-single p_customer_id">
                    <option value="0">--Select--</option>
                    @foreach ($customers as $customer)
                    <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
        <div class="col-sm-4 {{$voucher->godown_motive==3?'d-none ':'' }}">
            <div class="form-group">
                <label for="exampleInputEmail1">Godowns :</label>
                <select name="godown" class="form-control js-example-basic-single js-example-basic godown left-data" required>
                    @if($voucher->godown_motive==3)
                    <option value="0"></option>
                    @else @foreach ($godowns as $godown)
                    <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                    @endforeach @endif
                </select>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="exampleInputEmail1">Distribution Center :</label>
                <select name="dis_cen_id" class="form-control js-example-basic-single dis_cen_id" required>
                    @if(empty($voucher->distribution_center_id))
                      <option value="0">--Select--</option>
                    @endif
                    @foreach ($distributionCenter as $distribution)
                    <option value="{{$distribution->dis_cen_id }}">{{$distribution->dis_cen_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table customers" style="border: none !important; margin-top: 5px;">
                <thead>
                    <tr>
                        <th class="col-0.5">#</th>
                        <th class="th {{$voucher->remark_is==1?'col-4':'col-5'}}">Product Name</th>
                        <th class="th col-2 {{$voucher->godown_motive==3?'d-none':'' }}{{$voucher->godown_motive==4?'d-none':'' }}">Godown</th>
                        <th class="th col-1">Quantity</th>
                        <th class="th col-1">Price</th>
                        <th class="th col-1">Per</th>
                        <th class="th col-2">Amount</th>
                        @if($voucher->godown_motive==3)
                        <th class="th {{$voucher->godown_motive==3?'col-2':'col-1'}}  {{$voucher->remark_is==0?'d-none':'' }}">Remarks</th>
                        @elseif($voucher->godown_motive==4)
                        <th class="th {{$voucher->godown_motive==4?'col-2':'col-1'}} {{$voucher->remark_is==0?'d-none':'' }}">Remarks</th>
                        @else
                        <th class="th col-1 {{$voucher->remark_is==0?'d-none':'' }}">Remarks</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="orders"></tbody>
                <tfoot>
                    <tr>
                        <td class="m-0 p-0"><button type="button" name="add_commission" id="add_commission" class="btn btn-success cicle m-0 py-1">+</button></td>
                        <td colspan="1" class="text-right">Final Total :</td>
                        @if($voucher->godown_motive==1)
                        <td></td>
                        @elseif($voucher->godown_motive==2)
                        <td></td>
                        @endif
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><input type="text " name="get_without_commission" style="border-radius: 15px; font-weight: bold;" class="form-control text-right get_commission_without" readonly /></td>
                        <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
                    </tr>
                </tfoot>
                <tfoot>
                    <tr>
                        <td class="m-0 p-0"><button type="button" id="add" class="btn btn-success cicle m-0 py-1">+</button></td>
                        <td colspan="1" class="text-right">Total:</td>
                        @if($voucher->godown_motive==1)
                        <td></td>
                        @elseif($voucher->godown_motive==2)
                        <td></td>
                        @endif
                        <td><input type="text " style="border-radius: 15px; font-weight: bold;" class="total_dedit form-control text-right" readonly /></td>

                        <td></td>
                        <td></td>
                        <td><input typecommision_ca="text " name="total_credit" style="border-radius: 15px; font-weight: bold;" class="total_credit form-control text-right" readonly /></td>
                        <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
                    </tr>
                </tfoot>
                <tfoot id="commission_append"></tfoot>
            </table>
            <div class="row" style="margin: 3px;">
                <label style="margin-left: 2px;">Narration:</label>
                <textarea style="margin: 15px;" name="narration" rows="2.5" cols="2.5" class="form-control narration">{{$data->narration??''}}</textarea>
            </div>
        </div>
        @if($voucher->secret_narration_is)
            <div class="col-sm-12 mb-1">
                <label style="margin-left: 2px;">Secret Narration:</label>
                <textarea style="margin-left: 2px; border-radius: 15px;" name="secret_narration" rows="2.5" cols="2.5" class="form-control">{{$data->secret_narration}}</textarea>
            </div>
        @endif
    </div>
    <div align="center">
        @if (user_privileges_check('Voucher',$voucher->voucher_id,'alter_role'))
            @if (voucher_modify_authorization($data->tran_id))
            <button type="submit" class="btn btn-info edit_received_btn" style="width: 120px; border-radius: 15px;">
                <span class="m-1 m-t-1" style="color: #404040;"><i class="fa fa-save" style="font-size: 18px;"></i></span><span>Update</span>
            </button>
          @endif
        @endif
        @if (user_privileges_check('Voucher',$voucher->voucher_id,'delete_role'))
        <button type="button" class="btn btn-danger deleteIcon" style="width: 120px; border-radius: 15px;">
            <span class="m-1 m-t-1" style="color: #404040;"><i class="fa fa-save" style="font-size: 18px;"></i></span><span>Delete</span>
        </button>
        <button type="button" class="btn btn-danger CancelIcon" style="width: 120px; border-radius: 15px;">
            <span class="m-1 m-t-1" style="color:#404040;!important"><i class="fa fa-times-circle" style="font-size: 20px;"></i></span><span>Cancel</span>
        </button>
        @endif

    </div>
</form>
@endslot
@endcomponent
@push('js')
<script type="text/javascript" src="{{asset('libraries/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{asset('voucher_setup/voucher_setup_purchase.js')}}"></script>
<script>

selected_val();
function selected_val(){
    $('.p_customer_id').val('{{$data->customer_id}}')
    $('.unit_or_branch').val('{{$data->unit_or_branch}}');
    $('.dis_cen_id').val('{{$data->dis_cen_id}}')
    $('.debit_ledger_id').val('{{$debit->ledger_head_id??0}}');
}
var debit_credit_value='{{$debit_credit_data}}';
$(document).ready(function () {

  $('.debit_ledger_name').autocomplete({
      // minChars: 1,
      source: function(request, response) {
      $.ajax({
          type: 'GET',
          dataType: 'json',
          url:'{{route("searching-ledger") }}',
          data: {
                  name: request.term,
                  voucher_id:"{{$voucher->voucher_id}}",
          },
          success: function(data) {
          response($.map( data, function( item ) {
              var object = new Object();
              object.label = item.ledger_name;
              object.value = item.ledger_name;
              object.ledger_head_id = item.ledger_head_id;
              object.inventory_value=item.inventory_value;
              return object
          }));

          }
      });
      },
      change: function (event, ui) {
               if (ui.item == null) {
                    $(this).val('');
                    $(this).focus();
                }
      },
      select: function (event, ui) {
        $.ajax({
                url: '{{route("balance-debit-credit") }}',
                method: 'GET',
                dataType: 'json',
                async: false,
                data: {
                    ledger_head_id:ui.item.ledger_head_id
                },
                success: function(response){
                    if(ui.item.inventory_value=='Yes'){
                    $('#credit_amont').text(response.data);
                    $("#debit_ledger_name").val(ui.item.value);
                    $("#credit_ledger_id").val(ui.item.ledger_head_id);
                    $('#error_inventory_value_affected').text('');
                    return true;
                    }else{
                    $("#debit_ledger_name").val('');
                    $('#credit_amont').text('');
                    $('#error_inventory_value_affected').text('Inventory Value Affected NO');
                    return false;
                    }
                }
            });
        return false;
      }
  });

});

$(document).ready(function(){

   // form reset
   resetForm("edit_sales_return_id");
   selected_val();
  var amount_decimals="{{company()->amount_decimals}}";
  var check_current_stock= "{{$voucher->amnt_typeable ?? ''}}";
  var remark_is="{{$voucher->remark_is ?? ''}}";
  var stock_item_price_typeabe="{{$voucher->stock_item_price_typeabe ?? ''}}";
  var total_qty_is="{{$voucher->total_qty_is ?? ''}}";
  var total_price_is="{{$voucher->total_price_is ?? ''}}";
  var amount_typeabe="{{$voucher->amount_typeabe ?? ''}}";
  var godown_motive="{{$voucher->godown_motive ?? ''}}";
  var t_m_id="{{$data->tran_id}}";
  let p;
        $.ajax({
            type: 'GET',
            url: "{{ url('edit/stock/out')}}",
            async: false,
            data: {
                tran_id:t_m_id
            },
            dataType: 'json',
            success: function (response) {
             $.each(response.data, function(i,val) {
                 $('#orders').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${i}">
                    <input class="form-control  stock_out_id m-0 p-0"  name="stock_out_id[]" type="hidden" data-type="stock_out_id" value="${val.stock_out_id}" id="stock_out_id_${i}"  for="${i}"/>
                    <input class="form-control  product_id m-0 p-0"  name="product_id[]" type="hidden" data-type="product_id" id="product_id_${i}" value="${val.stock_item_id}"  for="${i}"/>
                  <td  class="m-0 p-0"><button  type="button" name="remove" id="${i}" class="btn btn-danger btn_remove cicle m-0  py-1">-</button></td>
                  <td  class="m-0 p-0">
                      <input class="form-control product_name  autocomplete_txt" name="product_name[]" data-field-name="product_name"  type="text" data-type="product_name" value="${val.product_name}" id="product_name_${i}"  autocomplete="off" for="${i}"  />
                  </td>
                  <td  class="m-0 p-0 ${godown_motive==3?'d-none':''} ${godown_motive==4?'d-none':''}">
                      <input class="form-control godown_name autocomplete_txt " name="godown_name[]"   data-field-name="godown_name" type="text"  value="${val.godown_name}" id="godown_name_${i}"  for="${i}" ${godown_motive==2?'readonly':''}  autocomplete="off" />
                      <input class="form-control godown_id text-right " name="godown_id[]"  data-field-name="godown_id"  type="hidden"  value="${val.godown_id}" id="godown_id_${i}"  for="${i}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control qty text-right" name="qty[]"  data-field-name="qty" type="number"  step="any"
                       id="qty_${i}" value="${Math.abs(val.qty)}" for="${i}"  />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control rate text-right"  name="rate[]" data-field-name="rate" type="number"  step="any"
                      data-type="rate" id="rate_${i}" value="${Math.abs(val.rate).toFixed(amount_decimals)}"
                      for="${i}" ${stock_item_price_typeabe==0?'readonly':''} />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control per " value="${val.symbol}"  name="per[]" data-field-name="per" type="text" data-type="per" id="per_${rowCount}"  for="${rowCount}" readonly />
                      <input class="form-control measure_id "  name="measure_id[]" data-field-name="measure_id" type="hidden" data-type="measure_id" id="measure_id_${i}"  for="${i}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control amount  text-right" type="number"  step="any"  name="amount[]" id="amount_${i}"  value="${Math.abs(val.total).toFixed(amount_decimals)}" ${amount_typeabe==1?'readonly':''}  for="${i}"/>
                  </td>
                  <td  class="m-0 p-0 ${remark_is==0?'d-none':''}">
                      <input class="form-control remark"  name='remark[]'value="${val.remark==null?"":val.remark}" type="text" data-type=" id="remark_${i}"  autocomplete="off" for="${i}"/>
                  </td>
                 </tr>`);
                    p=i;
                });
            }
        });
  var rowCount=p;
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
    //remove row
    var arr = [];
    $(document).on('click', '.btn_remove', function() {
        var button_id = $(this).attr('id');
        $('#row'+button_id+'').remove();
      arr.push($(this).closest('tr').find('.stock_out_id').val());
        $('.delete_stock_out_id').val(arr);

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
                      <input class="form-control qty text-right " name="qty[]"  step="any" data-field-name="qty" type="number"
                      class="qty" id="qty_${rowCount}"  for="${rowCount}"  />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control rate text-right "  name="rate[]" data-field-name="rate" type="number" step="any" data-type="rate" id="rate_${rowCount}"  for="${rowCount}" ${stock_item_price_typeabe==0?'readonly':''} />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control per  "  name="per[]" data-field-name="per" type="text" data-type="per" id="per_${rowCount}"  for="${rowCount}" readonly />
                      <input class="form-control measure_id "  name="measure_id[]" data-field-name="measure_id" type="hidden" data-type="measure_id" id="measure_id_${rowCount}"  for="${rowCount}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control amount  text-right" type="number" step="any"  name="amount[]" id="amount_${rowCount}" ${amount_typeabe==1?'readonly':''}  for="${rowCount}"/>
                  </td>
                  <td  class="m-0 p-0 ${remark_is==0?'d-none':''}">
                      <input class="form-control remark"  name='remark[]' type="text" data-type=" id="remark_${rowCount}"  autocomplete="off" for="${rowCount}"/>
                  </td>
            </tr>`);
      }

  }
  calculation_total();
  function calculation_total(){
      let debit=0;
      let credit=0;
      $('#orders tr').each(function(i){
          if(parseFloat($(this).find('.qty').val())) debit+=parseFloat($(this).find('.qty').val());
          if(parseFloat($(this).find('.amount').val())) credit+=parseFloat($(this).find('.amount').val());
      })
      $('.total_dedit').val(parseFloat(debit).toFixed(amount_decimals));
      $('.total_credit').val(parseFloat(credit).toFixed(amount_decimals));
       let commision_cal= $('#commission_append tr').find('.commision_cal').val();
       var commission_amount=$('#commission_append tr').find('.commission_amount').val();
       let current_row=$('#commission_append tr').attr('id');
       commission_per_calcucation();
       commission_calcucation();
      // setting checking is total qty and price
      if(total_qty_is==0){
          if(debit==0){
            $(":submit").attr("disabled", true);
          }else{
            $(":submit").attr("disabled", false);
          }
      }
      if( total_price_is==0){
          if(credit==0){
            $(":submit").attr("disabled", true);
          }else{
            $(":submit").attr("disabled", false);
          }

      }

  }
  $('#orders').on('keyup change','.qty,.rate',function(){
       let qty=$(this).closest('tr').find('.qty').val();
       let rate=$(this).closest('tr').find('.rate').val();
       $(this).closest('tr').find('.amount').val(parseFloat(qty*rate).toFixed(amount_decimals));
       calculation_total();
  });
  $('#orders').on('keyup change','.amount',function(){
    // setting checking is amount_typeabe
      if(amount_typeabe==0){
        calculation_total();
        $(this).closest('tr').find('.rate').val(parseFloat($(this).closest('tr').find('.amount').val())/parseFloat($(this).closest('tr').find('.qty').val()));
      }
  });

  $('#commission_append').on('keyup change', '.commission_amount,.commision_cal', function() {
       let commision_cal=$(this).closest('tr').find('.commision_cal').val();
       let total_amount=$('.total_credit').val();
       var commission_amount=$(this).closest('tr').find('.commission_amount').val();
       let current_row=$(this).closest('tr').attr('id');
       if((commision_cal==1 || commision_cal==2) && (commission_amount>100)){
            commission_amount=100;
            $(this).closest('tr').find('.commission_amount').val(100)
        }
        if(commision_cal==1){
            $('#commission_append tr').find('#get_commission_'+current_row).val(parseFloat((commission_amount/100)*parseFloat(total_amount)).toFixed(amount_decimals));
        }
        else if(commision_cal==3){
            $('#commission_append tr').find('#get_commission_'+current_row).val(parseFloat((commission_amount)).toFixed(amount_decimals));
        }
        else if(commision_cal==2){
            $('#commission_append tr').find('#get_commission_'+current_row).val(parseFloat((commission_amount/100)*parseFloat(total_amount)).toFixed(amount_decimals));
        }
        else if(commision_cal==4){
            $('#commission_append tr').find('#get_commission_'+current_row).val(parseFloat(commission_amount).toFixed(amount_decimals));
        }
     commission_calcucation();

 });

//  function commission_per_calcucation(){
//     let total_amount=$('.total_credit').val();
//     $('#commission_append tr').each(function(i){
//         let commission_amount=$(this).find('.commission_amount').val();
//         let get_commission = $(this).find('.get_commission ').val();
//         let commision_cal=$(this).find('.commision_cal').val();
//         if(commision_cal==1 || commision_cal==2){
//             $(this).find('.get_commission').val(parseFloat((commission_amount/100)*parseFloat(total_amount)).toFixed(amount_decimals));
//         }

//     });
//  }
function commission_per_calcucation(){
        let total_amount=parseFloat($('.nettotal').val() || $('.total_amount').val());
        $('#commission_append tr').each(function(i){
                let commission_amount=$(this).find('.commission_amount').val();
                let get_commission = $(this).find('.get_commission ').val();
                let commision_cal=$(this).find('.commision_cal').val();
                if(commision_cal==1 || commision_cal==2){
                    let commissionValue=parseFloat((commission_amount/100)*parseFloat(total_amount));
                    $(this).find('.get_commission').val((commissionValue).toFixed(amount_decimals));
                    if(commision_cal==1){
                        total_amount=total_amount-commissionValue;
                    }else if(commision_cal==2){
                        total_amount=total_amount+commissionValue;
                    }
                }else{
                    if(commision_cal==3){
                        total_amount=total_amount-get_commission;
                    }else if(commision_cal==4){
                        total_amount=total_amount+get_commission;
                    }

                }
        });

    }

 // get commission cal
    function  commission_calcucation(){
        let total_val=$('.total_credit').val();
        $('#commission_append tr').each(function(i){
            let get_commission =$(this).find('.get_commission ').val();
            if($(this).find('.commision_cal').val()==1 || $(this).find('.commision_cal').val()==3){
                total_val=parseFloat(total_val)-parseFloat(get_commission);

            }else if($(this).find('.commision_cal').val()==2 || $(this).find('.commision_cal').val()==4){
                total_val=parseFloat(total_val)+parseFloat(get_commission);
            }

        });
        $('.get_commission_without').val(parseFloat(total_val).toFixed(amount_decimals))

}

  $(document).on('click', '.btn_remove,.com_btn_remove', function() {
    calculation_total();
 });

 let ledger_commission_tree=@json($ledger_commission_tree);
   //append commission table
   $(document).ready(function(){
  //edit value commission
    $.each(JSON.parse(debit_credit_value.replace(/&quot;/g,'"')), function(i,val) {
        if(val.commission_type){
            $('#commission_append').append(`<tr id="${i}">
                <input type="hidden" name="commission_debit_credit_id[]" value="${val.debit_credit_id}" class="form-control debit_id" />
                <td  class="m-0 p-0"><button  type="button" name="com_btn_remove" id="${i}" class="btn btn-danger com_btn_remove cicle m-0  py-1">-</button></td>
                <td class="text-right ">Commission: </td>

                <td  colspan="2" class="m-0 p-0" > <select name="commission_ledger_id[]" class="form-control select2  commission_ledger_id${val.debit_credit_id}"  id="select_${i}" required>
                     ${ledger_commission_tree}
                </select></td>

                <td class="m-0 p-0">
                <select name="commision_cal[]" class="form-control left-data commision_cal" id="commision_cal_${i}" style="margin:0%;" required>
                    <option ${val.commission_type==1?'selected':''}  value="1">(-) %</option>
                    <option ${val.commission_type==2?'selected':''}  value="2">(+) %</option>
                    <option ${val.commission_type==3?'selected':''}  value="3">(-)</option>
                    <option ${val.commission_type==4?'selected':''}  value="4">(+)</option>
                </select>
                </td>
                <td class="m-0 p-0" >
                    <input type="number"  step="any"  name="commission_amount[]"class="form-control commission_amount" value="${val.commission.toFixed(amount_decimals)}" id="commission_amount_${i}" />
                </td>
                <td class="col-1.5"><input type="text " name="get_commission[]" style="border-radius: 15px;" id="get_commission_${i}"  value="${val.debit?val.debit.toFixed(amount_decimals):val.credit.toFixed(amount_decimals)}" class="get_commission form-control text-right" readonly></td>
                <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
            </tr>`);
            $(`.commission_ledger_id${val.debit_credit_id}`).val(val.ledger_head_id);
            $('#select_'+i).select2({
            width:'100%'
        });
        }
       });
       commission_calcucation();
   var ComRowCount=$('#commission_append tr:last').attr('id')||1;
   $('#add_commission').click(function() {
      ComRowCount++;
        $('#commission_append').append(`<tr id="${ComRowCount}">
                <td  class="m-0 p-0"><button  type="button" name="com_btn_remove" id="${ComRowCount}" class="btn btn-danger com_btn_remove cicle m-0  py-1">-</button></td>
                <td class="text-right ">Commission: </td>
                <td  colspan="2" class="m-0 p-0" > <select name="commission_ledger_id[]" class="form-control select2" id="select_${ComRowCount}" required>
                <option value="0">--Select--</option>
                     ${ledger_commission_tree}
                </select></td>
                <td class="m-0 p-0">
                <select name="commision_cal[]" class="form-control left-data commision_cal" id="commision_cal_${ComRowCount}" style="margin:0%;" required>
                    <option value="1">(-) %</option>
                    <option value="2">(+) %</option>
                    <option value="3">(-)</option>
                    <option value="4">(+)</option>
                </select>
                </td>
                <td class="m-0 p-0" >
                    <input type="number"  step="any"  name="commission_amount[]"class="form-control commission_amount" id="commission_amount_${ComRowCount}" />
                </td>
                <td class="col-1.5"><input type="text " name="get_commission[]" style="border-radius: 15px;" id="get_commission_${ComRowCount}" class="get_commission form-control text-right" readonly></td>
                <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
            </tr>`);
            $('#select_'+ComRowCount).val("{{$voucher->commission_ledger_id??0}}");
            $('#select_'+ComRowCount).select2({
            width:'100%'
           });
    });
});
  let debit_credit_arr=[];
  $('#commission_append').on('click', '.com_btn_remove', function() {
      calculation_total();
      var button_id =$(this).closest('tr').remove();
      debit_credit_arr.push($(this).closest('tr').find('.debit_id').val());
     $('.delete_debit_credit_id').val(debit_credit_arr);
  });

  function getId(element){
      var id, idArr;
      id = element.attr('id');
      idArr = id.split("_");
      return idArr[idArr.length - 1];
    }

    var item_check =[];
    var row =[];
  // product searching
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
                    else if($(this).attr('name')==='godown_name[]')$(this).closest('tr').find('.godown_id').val('');
                    $(this).focus();
                }
           },
          select: function( event, selectedData ) {
            if(checkDuplicateItem(selectedData?.item?.data?.stock_item_id)){
                    currentEle.val('');
                }else if(selectedData && selectedData.item && selectedData.item.data){
                  var rowNo, data;
                  rowNo = getId(currentEle);
                  data = selectedData.item.data;
                $(this).css({backgroundColor: 'white'}) ;
                if(data.godown_id){
                        $('#godown_name_'+rowNo).val(data.godown_name);
                        $('#godown_id_'+rowNo).val(data.godown_id);
                    }
                    if(data.stock_item_id){
                        $('#product_id_'+rowNo).val(data.stock_item_id);
                        $('#per_'+rowNo).val(data.symbol);
                        $('#measure_id_'+rowNo).val(data.unit_of_measure_id);
                        // stock item get price
                        $.ajax({
                            url: '{{route("searching-stock-item-price") }}',
                            method: 'GET',
                            dataType: 'json',
                            async: false,
                            data: {
                                stock_item_id:data.stock_item_id,
                                voucher_id:"{{$voucher->voucher_id}}",
                                tran_date:$('.invoice_date').val()
                            },
                            success: function(response){
                                if(response){
                                    if(response.rate){
                                    $('#rate_'+rowNo).val(response.rate);
                                        selected_auto_value_change(currentEle,response.rate,amount_decimals)
                                        calculation_total();
                                    }else{
                                        $('#rate_'+rowNo).val(0);
                                    }
                                }else{
                                    selected_auto_value_change(currentEle,0,amount_decimals)
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
$("#edit_sales_return_id").submit(function(e) {
  e.preventDefault();
  const fd = new FormData(this);
  var id="{{$data->tran_id}}";
  $("#edit_sales_return_btn").text('Update');
  $.ajax({

          url: "{{ url('voucher-sales-return') }}" + '/' + id,
          method: 'post',
          data: fd,
          cache: false,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function(data,status,xhr) {
              swal_message(data.message,'success','Successfully');
              setTimeout(function () {  window.location.href='{{ url()->previous() }}'; },100);
              $('#error_voucher_no').text('');
          },
          error : function(data,status,xhr){
              if(data.status==404){
                  swal_message(data.responseJSON.message,'error','Error');
              } if(data.status==422){
                $('#error_voucher_no').text(data.responseJSON.data?.invoice_no && data.responseJSON.data?.invoice_no[0]);
                        let error=[];
                        for (const [key, value] of Object.entries(data.responseJSON?.data)) {
                            error.push(`<p style="margin: 0; padding: 4px 0; color: red">${value}</p>`);
                        }
                        swal_message(data.responseJSON.message,'error',`${error.join('')}`);

              }
          }
  });
});

// delete sales return ajax request
$(document).on('click', '.deleteIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id ="{{$data->tran_id}}";
        swal(swal_data()).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('voucher-sales-return') }}" + '/' + id ,
                    type : "POST",
                    data : {'_method' : 'DELETE', '_token' : csrf_token},
                    success: function (data) {
                        swal_message(data.message,'success','Successfully');
                        setTimeout(function () {  window.location.href='{{ url()->previous() }}'; },100);
                    },
                    error: function () {
                        swal_message(data.responseJSON.message,'error','Error');
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

// Cancelled sales return ajax request
$(document).on('click', '.CancelIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id ="{{$data->tran_id}}";
        var narration =$('.narration').val();
        swal(swal_data()).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('voucher-sales-return-cancel') }}" + '/' + id ,
                    type : "POST",
                    data : {'_method' : 'POST', '_token' : csrf_token ,'narration':narration},
                    success: function (data) {
                        swal_message(data.message,'success','Successfully');
                        setTimeout(function () {  window.location.href='{{ url()->previous() }}'; },100);
                    },
                    error: function (data) {

                        swal_message(data.responseJSON.message,'error','Error');
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
function swal_message(data,message,m_title){
        swal({
            title:m_title,
            text: data,
            type: message,
            timer: '3000'
        });
}
$('.godown').on('change',function(){
    let godown=checkGodownValidity($('.godown option:selected').text());
    let godown_val=$('.godown').val();
    $('#orders tr').find('.godown_name').each(function(){
        $('.godown_name').val(godown);
        $('.godown_id').val(godown_val);
    })
})
input_checking('godown');
input_checking('product');
function input_checking(class_name){
    $('#orders').on('keyup  selected blur',`.${class_name}_name`,function(){
        let id=$(this).closest('tr').find(`.${class_name}_id`).val();
        let name=$(this).closest('tr').find(`.${class_name}_name`).val();
        if((id.length!=0)||(name.length==0)){
             $(this).closest('tr').find(`.${class_name}_name`).css({backgroundColor: 'white'});
             $('#orders').on('click','input',function(){
                $(this).focus();
             })
        }else{
            $(this).css('backgroundColor','red');
            $(".cansale_btn").attr("disabled", true);
            $(this).focus();
        }
   });
}
</script>

@endpush
@endsection

