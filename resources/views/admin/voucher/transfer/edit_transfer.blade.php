

@extends('layouts.backend.app')
@section('title','Voucher Transfer')
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
<form id="edit_transfer_id" method="POST">
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
                        <input type="text" name="invoice_no" class="form-control m-1" value="{{$data->invoice_no}}" style="border-radius: 15px;" {{$data->invoice_no?'readonly':''}} style="color: green" required/>
                        <span id="error_voucher_no" class="text-danger"></span>
                        <input type="hidden" name="voucher_id" class="form-control voucher_id" value="{{$voucher->voucher_id ?? ''}}" />
                        <input type="hidden" name="ch_4_dup_vou_no" class="form-control" value="{{$voucher->ch_4_dup_vou_no ?? ''}}" />
                        <input type="hidden" name="invoice" class="form-control" value="{{$voucher->invoice ?? ''}}" />
                        <input type="hidden" name="check_current_stock" class="form-control" value="{{$voucher->amnt_typeable ?? ''}}" />
                        <input type="hidden" name="row_wise_qty_is" class="form-control" value="{{$voucher->row_wise_qty_is ?? ''}}" />
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Ref No:</label>
                        <input type="text" name="ref_no" class="form-control m-1" value="{{$data->ref_no}}" style="border-radius: 15px;" />
                        <input type="hidden" name="delete_stock_out_id" class="form-control delete_stock_out_id" />
                        <input type="hidden" name="delete_stock_in_id" class="form-control delete_stock_in_id" />
                        <input type="hidden" id="allowAllStock" value="1"  />
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row" style="margin-top: 5px;">
                <div class="col-sm-12" style="margin-left: 5px;!important;">
                        <label for="exampleInputEmail1">Destination Godowns :</label>
                        <select name="godown_id_in" class="form-control js-example-basic-single godown_id_in left-data" required {{$voucher->destination_godown_motive==3?'d-none ':'' }}>
                            @if($voucher->destination_godown_motive==3)
                                @foreach ($destination_godowns as $godown)
                                <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                                @endforeach
                            @else @foreach ($destination_godowns as $godown)
                            <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                            @endforeach @endif
                        </select>
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
    <div class="row">
        <div class="col-sm-4">
            @if (company()->customer_id!=0)
            <div class="form-group">
                <label for="exampleInputEmail1">Customer :</label>
                <select name="customer_id" class="form-control js-example-basic-single customer_id left-data" required>
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
                <label for="exampleInputEmail1">Source Godowns :</label>
                <select name="godown" class="form-control js-example-basic-single godown left-data">
                   @if($voucher->godown_id==0)
                         <option value="">--Select--</option>
                    @endif
                    @if($voucher->godown_motive==3)
                    @foreach ($godowns as $godown)
                     <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                    @endforeach
                    @else @foreach ($godowns as $godown)
                    <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                    @endforeach @endif
                </select>
            </div>
        </div>
        <div class="col-sm-4">
            <label>Distribution Center :</label>
            <select style="border-radius: 15px;" name="dis_cen_id" class="form-control m-1 js-example-basic-single js-example-basic dis_cen_id" required>
                <option value="">--Select--</option>
                @foreach ($distributionCenter as $distribution)
                <option value="{{$distribution->dis_cen_id }}">{{$distribution->dis_cen_name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table customers" style="border: none !important; margin-top: 5px;">
                <thead>
                    <tr>
                        <th class="col-0.5">#</th>
                        <th class="th {{$voucher->remark_is==1?'col-3':'col-4'}}">Product Name</th>
                        <th class="th col-2 {{$voucher->godown_motive==3?'d-none':'' }}{{$voucher->godown_motive==4?'d-none':'' }}">Godown</th>
                        <th class="th col-1">stock</th>
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
                        <td class="m-0 p-0"><button type="button" id="add" class="btn btn-success cicle m-0 py-1">+</button></td>
                        <td colspan="1" class="text-right">Total:</td>
                        <td></td>
                        @if($voucher->godown_motive==1)
                        <td></td>
                        @elseif($voucher->godown_motive==2)
                        <td></td>
                        @endif
                        <td><input type="text " style="border-radius: 15px; font-weight: bold;" class="total_dedit form-control text-right" readonly /></td>
                        <td></td>
                        <td></td>
                        <td><input type="text " name="total_credit" style="border-radius: 15px; font-weight: bold;" class="total_credit form-control text-right" readonly /></td>
                        <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
                    </tr>
                </tfoot>
            </table>
            <div class="row" style="margin: 3px;">
                <label style="margin-left: 2px;">Narration:</label>
                <textarea style="margin: 15px;" name="narration" rows="2.5" cols="2.5" class="form-control narration">{{$data->narration??''}}</textarea>
            </div>
            @if($voucher->secret_narration_is)
                <div class="col-sm-12 mb-1">
                    <label style="margin-left: 2px;">Secret Narration:</label>
                    <textarea style="margin-left: 2px; border-radius: 15px;" name="secret_narration" rows="2.5" cols="2.5" class="form-control">{{$data->secret_narration}}</textarea>
                </div>
           @endif
        </div>
    </div>
    <div align="center">
        @if (user_privileges_check('Voucher',$voucher->voucher_id,'alter_role'))
            @if (voucher_modify_authorization($data->tran_id))
            <button type="submit" class="btn btn-info edit_transfer_btn" style="width: 120px; border-radius: 15px;">
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
<script type="text/javascript" src="{{asset('voucher_setup/vocher_setup_sales.js')}}"></script>

<script>
$('.customer_id').val('{{$data->customer_id??0}}')
$('.unit_or_branch').val('{{$data->unit_or_branch??0}}');
$('.dis_cen_id').val('{{$data->dis_cen_id??0}}')

$('.godown_id_in').val('{{$godown_id_in->godown_id??0}}');

$(document).ready(function(){
  var amount_decimals="{{company()->amount_decimals}}";
  var check_current_stock= "{{$voucher->amnt_typeable ?? ''}}";
  var remark_is="{{$voucher->remark_is ?? ''}}";
  var stock_item_price_typeabe="{{$voucher->stock_item_price_typeabe ?? ''}}";
  var total_qty_is="{{$voucher->total_qty_is ?? ''}}";
  var total_price_is="{{$voucher->total_price_is ?? ''}}";
  var amount_typeabe="{{$voucher->amount_typeabe ?? ''}}";
  var godown_motive="{{$voucher->godown_motive ?? ''}}";
  var t_m_id="{{$data->tran_id}}";
  let p=0;
        $.ajax({
            type: 'GET',
            url: "{{ url('voucher-stock-in-out')}}",
            async: false,
            data: {
                tran_id:t_m_id
            },
            dataType: 'json',
            success: function (response) {
             $.each(response.data, function(i,val) {
                 $('#orders').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${i}">
                    <input class="form-control  stock_out_id m-0 p-0"  name="stock_out_id[]" type="hidden" data-type="stock_out_id" value="${val?.stock_out_id||''}" id="stock_out_id_${i}"  for="${i}"/>
                    <input class="form-control  stock_in_id m-0 p-0"  name="stock_in_id[]" type="hidden" data-type="stock_in_id" value="${val?.stock_in_id||''}" id="stock_in_id_${i}"  for="${i}"/>
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
                      <input class="form-control stock text-right"   data-field-name="stock" 
                      type="number" step="any"
                      value="${((val.stock_in_sum)-(val.stock_out_sum)).toFixed(amount_decimals)}" 
                      id="stock_${i}"  for="${i}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control qty text-right" name="qty[]"  data-field-name="qty"  mainQty="${val.qty}"
                      step="any" type="number" 
                      id="qty_${i}" value="${val.qty}" for="${i}"  />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control rate text-right"  name="rate[]" data-field-name="rate" 
                      type="number" step="any" data-type="rate" id="rate_${i}" 
                      value="${val.rate.toFixed(amount_decimals)}" for="${i}" ${stock_item_price_typeabe==0?'readonly':''} />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control per " value="${val.symbol}"  name="per[]" data-field-name="per" type="text" data-type="per" id="per_${rowCount}"  for="${rowCount}" readonly />
                      <input class="form-control measure_id "  name="measure_id[]" data-field-name="measure_id" type="hidden" data-type="measure_id" id="measure_id_${i}"  for="${i}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control amount  text-right" type="number" step="any"  name="amount[]" id="amount_${i}"  value="${val.total.toFixed(amount_decimals)}" ${amount_typeabe==1?'readonly':''}  for="${i}"/>
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
    var arr_in = [];
    var arr_out = [];
    $(document).on('click', '.btn_remove', function() {
        var button_id = $(this).attr('id');
        $('#row'+button_id+'').remove();
        arr_in.push($(this).closest('tr').find('.stock_in_id').val());
        $('.delete_stock_in_id').val(arr_in);
        arr_out.push($(this).closest('tr').find('.stock_out_id').val());
        $('.delete_stock_out_id').val(arr_out);

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
        // <input class="form-control godown_name autocomplete_txt " name="godown_name[]" value="${godown_name}"  data-field-name="godown_name" type="text"  id="godown_name_${rowCount}"  for="${rowCount}" ${godown_motive==2?'readonly':''}  autocomplete="off" />
        // <input class="form-control godown_id text-right " name="godown_id[]"  data-field-name="godown_id" value="${godown_id}" type="hidden"  id="godown_id_${rowCount}"  for="${rowCount}" readonly />
          rowCount++;
              $('#orders').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${rowCount}">
                  <input class="form-control  product_id m-0 p-0"  name="product_id[]" type="hidden" data-type="product_id" id="product_id_${rowCount}"  for="${rowCount}"/>
                  <td  class="m-0 p-0"><button  type="button" name="remove" id="${rowCount}" class="btn btn-danger btn_remove cicle m-0  py-1">-</button></td>
                  <td  class="m-0 p-0">
                      <input class="form-control product_name  autocomplete_txt" name="product_name[]" data-field-name="product_name"  type="text" data-type="product_name" id="product_name_${rowCount}"  autocomplete="off" for="${rowCount}"  />
                  </td>
                  <td  class="m-0 p-0 ${godown_motive==3?'d-none':''} ${godown_motive==4?'d-none':''}">
                      <input 
                        class="form-control godown_name autocomplete_txt " 
                        name="godown_name[]" 
                        data-field-name="godown_name" 
                        type="text"  
                        id="godown_name_${rowCount}"  
                        for="${rowCount}" 
                        ${godown_motive==2?'readonly':''}  autocomplete="off" />
                      <input 
                        class="form-control godown_id text-right " 
                        name="godown_id[]"  
                        data-field-name="godown_id"  
                        type="hidden"  
                        id="godown_id_${rowCount}"  
                        for="${rowCount}" 
                        readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control stock text-right"   data-field-name="stock" 
                      type="number" step="any" id="stock_${rowCount}"  for="${rowCount}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control qty text-right " name="qty[]"  data-field-name="qty" 
                      type="number" step="any" id="qty_${rowCount}"  for="${rowCount}"  />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control rate text-right "  name="rate[]" data-field-name="rate" 
                      type="number" step="any" data-type="rate" id="rate_${rowCount}"  for="${rowCount}" 
                      ${stock_item_price_typeabe==0?'readonly':''} />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control per  "  name="per[]" data-field-name="per" type="text" data-type="per" id="per_${rowCount}"  for="${rowCount}" readonly />
                      <input class="form-control measure_id "  name="measure_id[]" data-field-name="measure_id" type="hidden" data-type="measure_id" id="measure_id_${rowCount}"  for="${rowCount}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control amount  text-right" 
                      type="number" step="any"  name="amount[]" id="amount_${rowCount}" 
                      ${amount_typeabe==1?'readonly':''}  for="${rowCount}"/>
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
      //is checking qty
       let qty;
       if(check_current_stock==0){
        let mainQty=$(this).closest('tr').find('.qty').attr('mainQty') || 0;
        if((parseFloat($(this).closest('tr').find('.stock').val())+parseFloat(mainQty))>=($(this).closest('tr').find('.qty').val())){
             qty=$(this).closest('tr').find('.qty').val();
        }else if((parseFloat($(this).closest('tr').find('.stock').val())+parseFloat(mainQty))>=0){
            qty=parseFloat($(this).closest('tr').find('.stock').val())+parseFloat(mainQty);
            $(this).closest('tr').find('.qty').val(qty);

        }else{
            qty=parseFloat(0);
            $(this).closest('tr').find('.qty').val(qty);
        }
       }else{
          qty=$(this).closest('tr').find('.qty').val();
       }
       let rate=$(this).closest('tr').find('.rate').val();
       $(this).closest('tr').find('.amount').val(parseFloat(qty*rate).toFixed(amount_decimals));
      calculation_total();
  });

  $('#orders').on('keyup','.amount',function(){
    // setting checking is amount_typeabe
      if(amount_typeabe==0){
        calculation_total();
        $(this).closest('tr').find('.rate').val(parseFloat($(this).closest('tr').find('.amount').val())/parseFloat($(this).closest('tr').find('.qty').val()));
      }
  });

  // on change godown amount calculation
  $('.godown').on('change',function(){
    $('#orders tr').find('.godown_name').each(function(){
        $(this).closest('tr').find('.amount').val(parseFloat($(this).closest('tr').find('.qty').val()*$(this).closest('tr').find('.rate').val()).toFixed(amount_decimals));
    });
    calculation_total();
  });

  $(document).on('click', '.btn_remove', function() {
    calculation_total();
 });
 $("#orders").on('keydown', '.product_name', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
            let stock_item_id= $(this).closest('tr').find('.product_id').val();
            tab_price.call($(this),stock_item_id,"{{$voucher->voucher_id}}",'{{route("searching-stock-item-price") }}',check_current_stock,amount_decimals);

            calculation_total();
        }
    });
  function getId(element){
      var id, idArr;
      id = element.attr('id');
      idArr = id.split("_");
      return idArr[idArr.length - 1];
    }

    var item_check =[];
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
                    check_item_null({{$voucher->total_qty_is ?? ''}},0);
                }
            },
          select: function( event, selectedData ) {
              if(selectedData && selectedData.item && selectedData.item.data){
                  var rowNo, data;
                  rowNo = getId(currentEle);
                  data = selectedData.item.data;
                    currentEle.css({backgroundColor: 'white'});
                    if(data.godown_id){
                            $('#godown_name_'+rowNo).val(data.godown_name);
                            $('#godown_id_'+rowNo).val(data.godown_id);
                            //current stock check
                            current_stock(rowNo,$(this).closest('tr').find('.product_id').val(),data.godown_id,'{{url("current-stock") }}',check_current_stock);
                        }
                        if(data.stock_item_id){
                            $('#product_id_'+rowNo).val(data.stock_item_id);
                            $('#per_'+rowNo).val(data.symbol);
                            $('#measure_id_'+rowNo).val(data.unit_of_measure_id);
                            //current stock check
                            current_stock(rowNo,data.stock_item_id,$(this).closest('tr').find('.godown_id').val(),'{{url("current-stock") }}',check_current_stock)
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
                                        selected_auto_value_change(check_current_stock,currentEle,response.rate,amount_decimals);
                                        calculation_total();
                                        }else{
                                            $('#rate_'+rowNo).val(0);
                                            selected_auto_value_change(check_current_stock,currentEle,0,amount_decimals);
                                            calculation_total();
                                        }
                                    }else{
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
    $("#edit_transfer_id").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id="{{$data->tran_id}}";
        $("#edit_transfer_btn").text('Update');
        $.ajax({

                url: "{{ url('voucher-transfer') }}" + '/' + id,
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
// delete sales ajax request
$(document).on('click', '.deleteIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id ="{{$data->tran_id}}";
        swal(swal_data()).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('voucher-transfer') }}" + '/' + id ,
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

// Cancelled stock transfer ajax request
$(document).on('click', '.CancelIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id ="{{$data->tran_id}}";
        var narration =$('.narration').val();
        swal(swal_data()).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('voucher-transfer-cancel') }}" + '/' + id ,
                    type : "POST",
                    data : {'_method' : 'POST', '_token' : csrf_token ,'narration':narration},
                    success: function (data) {
                        swal_message(data.message,'success','Successfully');
                        setTimeout(function () {  window.location.href='{{route("daybook-report.index")}}'; },100);
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
input_checking('godown');
input_checking('product');
</script>
@endpush
@endsection

