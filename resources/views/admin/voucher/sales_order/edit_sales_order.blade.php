
@extends('layouts.backend.app')
@section('title','Voucher Sales Order')
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
<form id="edit_sales_order_id" method="POST">
    @csrf {{ method_field("PUT") }}
    <div class="page-body">
        <div class="row margin">
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-4 dis" style="float: left;">
                        <label style="float: left; margin: 2px;">Invoice No:</label><br />
                        <br />
                        <label style="float: left; margin: 2px; margin-right: 29px;">Delivery Date :</label>
                    </div>
                    <div class="col-sm-8 m-0 p-0" style="margin-left: 5px;">
                        <!-- resposive lebel change -->
                        <label class="display" style="display: none;">Invoice No:</label>
                        <input type="text" name="invoice_no" class="form-control m-1" value="{{$data->invoice_no}}" style="border-radius: 15px;" style="color: green;" required />
                        <span id="error_voucher_no" class="text-danger"></span>
                        <input type="hidden" name="ch_4_dup_vou_no" class="form-control" value="{{$voucher->ch_4_dup_vou_no ?? ''}}" />
                        <input type="hidden" name="invoice" class="form-control" value="{{$voucher->invoice ?? ''}}" />
                        <input type="hidden" name="voucher_id" class="form-control voucher_id" value="{{$voucher->voucher_id ?? ''}}" />
                        <!-- resposive lebel change -->
                        <label class="display" style="display: none;">Delivery Date :</label>
                        <input type="text" name="invoice_date" class="form-control setup_date invoice_date" style="margin-bottom: 4px !important; border-radius: 15px;" value="{{$data->transaction_date}}" />
                        <input type="hidden" name="delete_sales_order_id" class="form-control delete_sales_order_id" />
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="row" style="margin-top: 5px;">
                    <div class="col-sm-4 dis" style="float: right;">
                        <label style="float: right; margin: 2px; margin-top: 4px;" for="exampleInputEmail1">Party's A/C Name:</label><br />
                        <br />
                    </div>
                    <div class="col-sm-8" style="float: right;">
                        <!-- resposive lebel change -->
                        <label class="display" style="display: none;">Party's A/C Name:</label>
                        <label id="credit_amont" style="font-weight: bold; font-size: 18px !important; margin: 2px;">{{$credit_sum_value??'0.000'}}</label>
                        <input type="text" name="debit_ledger_name" id="debit_ledger_name" class="form-control debit_ledger_name" value="{{$data->ledger_name??''}}" style="margin-bottom: 4px !important; margin-top: 2px;" required />
                        <span id="error_inventory_value_affected" class="text-danger"></span>
                        <input
                            type="hidden"
                            name="credit_ledger_id"
                            id="credit_ledger_id"
                            class="form-control credit_ledger_id"
                            value="{{$data->ledger_head_id}}"
                            style="margin-bottom: 4px !important; border-radius: 15px; margin-top: 2px;"
                        />
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="row" style="margin-top: 5px;">
                    <div class="col-sm-4 dis" style="float: right;">
                        <label style="float: right; margin: 2px; margin-right: 26px;" for="exampleInputEmail1">Unit / Branch:</label>
                    </div>
                    <div class="col-sm-8 m-0 p-0" style="margin-left: 5px;!important;">
                        <!-- resposive lebel change -->
                        <label class="display" style="display: none;">Unit / Branch:</label>
                        <select style="margin-top: 2px; border-radius: 15px;" name="unit_or_branch" class="form-control m-1 js-example-basic-single js-example-basic" required>
                            @foreach ($branch_setup as $unit_branchs)
                            <option value="{{ $unit_branchs->id }}">{{$unit_branchs->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
             <div class="col-sm-3 {{$voucher->godown_motive==3?'d-none ':'' }}">
                <div class="row" style="margin-top: 5px;">
                    <div class="col-sm-4 dis" style="float: right;">
                        <label style="float: right; margin: 2px; margin-right: 26px;" for="exampleInputEmail1">Godowns :</label>
                    </div>
                    <div class="col-sm-8 m-0 p-0" style="margin-left: 5px;!important;">
                        <!-- resposive lebel change -->
                        <label class="display" style="display: none;">Godowns :</label>
                        <select name="godown" style="margin-top: 2px; border-radius: 15px;" class="form-control m-1 js-example-basic-single js-example-basic godown" >
                            <option value="">--Select One--</option>
                            @if($voucher->godown_motive==3)
                                @foreach ($godowns as $godown)
                                    <option value="{{$godown->godown_id}}" {{$voucher->godown_id==$godown->godown_id ? 'selected' : ''}}>{{$godown->godown_name}}</option>
                                @endforeach
                            @else
                                @foreach ($godowns as $godown)
                                    <option value="{{$godown->godown_id}}" {{$voucher->godown_id==$godown->godown_id ? 'selected' : ''}}>{{$godown->godown_name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table customers" style="border: none !important; margin-top: 5px;">
                <thead>
                    <tr>
                        <th class="col-0.5">#</th>
                        <th class="th col-4">Product Name</th>
                        <th class="th {{$voucher->commission_is==1?'col-1':'col-1'}} {{$voucher->godown_motive==3?'d-none':'' }}{{$voucher->godown_motive==4?'d-none':'' }}">Godown</th>
                        <th class="th col-1">stock</th>
                        <th class="th col-1">Quantity</th>
                        <th class="th col-1">Per</th>
                        <th class="th col-1">Amount</th>
                        <th class="th col-1">Commission</th>
                        <th class="th col-2">
                            Comm <br />
                            Amount
                        </th>
                    </tr>
                </thead>
                <tbody id="orders"></tbody>
                <tfoot>
                    <tr>
                        <td class="m-0 p-0"><button type="button" id="add" class="btn btn-success cicle m-0 py-1">+</button></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">Total :</td>
                        <td class="text-right"><input type="text " class="total_qty form-control text-right" style="font-weight: bold;" readonly /></td>
                        <td></td>
                        <td><input type="text " class="total_amount form-control text-right" style="font-weight: bold;" readonly /></td>
                        <td></td>
                        <td><input type="text " class="total_commission form-control text-right" style="font-weight: bold;" readonly /></td>
                    </tr>
                </tfoot>
            </table>
            <div class="row" style="margin: 3px;">
                <label style="margin-left: 2px;">Narration:</label>
                <textarea style="margin: 15px;" name="narration" rows="2.5" cols="2.5" class="form-control"></textarea>
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
                        <button type="submit" class="btn btn-info edit_sales_btn" style="width: 120px; border-radius: 15px;">
                            <span class="m-1 m-t-1" style="color: #404040;"><i class="fa fa-save" style="font-size: 18px;"></i></span><span>Update</span>
                        </button>
                @endif
            @endif
            <button type="button" class="btn btn-danger deleteIcon m-2" style="width: 120px; border-radius: 15px;">
                <span class="m-2 m-t-1" style="color: #404040;"><i class="fa fa-save" style="font-size: 18px;"></i></span><span>Delete</span>
            </button>
            <a class="btn btn-danger m-2" style="border-radius: 15px;" href="{{route('voucher-dashboard')}}">
                <span class="m-2 m-t-1" style="color:#404040;!important"><i class="fa fa-times-circle" style="font-size: 20px;"></i></span><span>Cancel</span>
            </a>
        </div>
    </div>
</form>
@endslot
@endcomponent
@push('js')
<script type="text/javascript" src="{{asset('libraries/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{asset('voucher_setup/vocher_setup_sales.js')}}"></script>
<script>
let godown_motive="{{$voucher->godown_motive ?? ''}}";
$('.unit_or_branch').val('{{$data->unit_or_branch}}');
let check_current_stock= "{{$voucher->amnt_typeable ?? ''}}";
// debit ledger searching
$(document).ready(function () {
  $('.debit_ledger_name').autocomplete({
      source: function(request, response) {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url:'{{route("searching-ledger-debit") }}',
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
                    $("#debit_ledger_namee").val('');
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
  // voucher setup and setting variable
  var amount_decimals="{{company()->amount_decimals}}";
  var t_m_id="{{$data->tran_id}}";
  let p;
  // get item
    $.ajax({
            type: 'GET',
            url: "{{ url('sales-order-data')}}",
            async: false,
            data: {
                tran_id:t_m_id,
            },
            dataType: 'json',
            success: function (response) {
                $.each(response.data, function(i,val) {
                    $('#orders').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${i}">
                        <input class="form-control  product_id m-0 p-0"  name="product_id[]" type="hidden" data-type="product_id" id="product_id_${i}" value="${val.stock_item_id}"  for="${i}"/>
                        <input class="form-control  sales_order_id m-0 p-0"  name="sales_order_id[]" type="hidden" data-type="sales_order_id" id="sales_order_id_${i}" value="${val.id}"  for="${i}"/>
                        <td  class="m-0 p-0"><button  type="button" name="remove" id="${i}" class="btn btn-danger btn_remove cicle m-0  py-1">-</button></td>
                        <td  class="m-0 p-0">
                            <input class="form-control product_name  autocomplete_txt" name="product_name[]" data-field-name="product_name"  type="text" data-type="product_name" value="${val.product_name }" id="product_name_${i}"  autocomplete="off" for="${i}" />
                        </td>
                        <td  class="m-0 p-0 ${godown_motive==3?'d-none':''} ${godown_motive==4?'d-none':''}">
                            <input class="form-control godown_name autocomplete_txt " name="godown_name[]"   data-field-name="godown_name" type="text"  value="${val.godown_name}" id="godown_name_${i}"  for="${i}" ${godown_motive==2?'readonly':''}  autocomplete="off" />
                            <input class="form-control godown_id text-right " name="godown_id[]"  data-field-name="godown_id"  type="hidden"  value="${val.godown_id}" id="godown_id_${i}"  for="${i}" readonly />
                        </td>
                       <td  class="m-0 p-0">
                        <input
                            class="form-control stock text-right"
                            data-field-name="stock"
                            type="number"
                            step="any"
                            name="stock[]"
                            value="${((val.stock_in_sum)-(val.stock_out_sum)).toFixed(amount_decimals)}" id="stock_${i}"
                            for="${i}"
                            readonly
                        />

                    </td>
                        <td  class="m-0 p-0">
                            <input class="form-control qty text-right " name="qty[]"  data-field-name="qty" type="number"  value="${val.qty}" 
                             step="any"  id="qty_${i}"  for="${i}"  />
                        </td>
                        
                        <td  class="m-0 p-0">
                             <input class="form-control rate text-right "  name="rate[]" data-field-name="rate" type="hidden" step="any" data-type="rate" value="${val.rate}" id="rate_${i}"  for="${i}" readonly/>
                            <input class="form-control per "  name="per[]" data-field-name="per" type="text" data-type="per" id="per_${i}" value="${val.symbol}"  for="${i}"  />
                            <input class="form-control measure_id "  name="measure_id[]" data-field-name="measure_id" type="hidden" data-type="measure_id" id="measure_id_${i}" value="${val.unit_of_measure_id}"  for="${i}" readonly/>
                        </td>
                        <td  class="m-0 p-0">
                            <input class="form-control amount  text-right" type="number" step="any"  name="amount[]" id="amount_${i}"  value="${val.total}" for="${i}" readonly/>

                        </td>

                    <td  class="m-0 p-0">
                        <input type="hidden" name="debit_ledger_id[]"  class="form-control debit_ledger_id_commission ledger_debit" id="ledger_debit_id_${i}"  for="${i}" value="${val.debit_ledger_id}" readonly/>
                        <div style="display: flex;flex-direction: row;">
                                <select name="product_wise_commission_cal[]" class="form-control  left-data product_wise_commision_cal m-0 p-0" id="product_wise_commision_cal_${i}" style="margin:0%;pointer-events: none;" readonly>
                                    <option ${val.comission_type==1?'selected':''} value="1">(-) %</option>
                                    <option ${val.comission_type==2?'selected':''} value="2">(+) %</option>
                                    <option ${val.comission_type==3?'selected':''} value="3">(-)</option>
                                    <option ${val.comission_type==4?'selected':''} value="4">(+)</option>
                                </select>
                                <input type="number" step="any" name="product_wise_commission_amount[]" id="product_wise_commission_amount_${i}"  value="${val.commission}" class="product_wise_commission_amount form-control text-right mx-0 px-0" style="min-width:60px;" readonly>
                        </div>
                    </td>

                    <td class="m-0 p-0"><input type="number" step="any"
                        name="product_wise_get_commission[]"  id="product_wise_get_commission_${i}"
                        class="product_wise_get_commission form-control text-right" value="${val.commission_amount}" readonly></td>
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


   //remove row
   var arr = [];
    $(document).on('click', '.btn_remove', function() {
        var button_id = $(this).attr('id');
        $('#row'+button_id+'').remove();
      arr.push($(this).closest('tr').find('.sales_order_id').val());
        $('.delete_sales_order_id').val(arr);

    });

    // append table
    function addrow (rowCount)
    {
        if(rowCount==null){
            rowCount=1;
        }else{
            rowCount=rowCount;
        }
        let godown_id=$('.godown').val();
        let godown_name=godown_id?$('.godown option:selected').text():'';
        for(var row=1; row<6;row++) {
            rowCount++;
                $('#orders').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${rowCount}">
                    <input class="form-control  product_id m-0 p-0"  name="product_id[]" type="hidden" data-type="product_id" id="product_id_${rowCount}"  for="${rowCount}"/>
                    <td  class="m-0 p-0"><button  type="button" name="remove" id="${rowCount}" class="btn btn-danger btn_remove cicle" style="padding: 0px 19px;margin:1px 0px;">-</button></td>
                    <td  class="m-0 p-0">
                        <input class="form-control product_name  autocomplete_txt" name="product_name[]" data-field-name="product_name"  type="text" data-type="product_name" id="product_name_${rowCount}"  autocomplete="off" for="${rowCount}" />
                    </td>
                    <td  class="m-0 p-0 ${godown_motive==3?'d-none':''} ${godown_motive==4?'d-none':''}">
                        <input class="form-control godown_name autocomplete_txt " name="godown_name[]" value="${godown_name}"  data-field-name="godown_name" type="text"  id="godown_name_${rowCount}"  for="${rowCount}" ${godown_motive==2?'readonly':''}  autocomplete="off"  required/>
                        <input class="form-control godown_id text-right " name="godown_id[]"  data-field-name="godown_id" value="${godown_id}" type="hidden"  id="godown_id_${rowCount}"  for="${rowCount}" readonly />
                    </td>
                    <td  class="m-0 p-0">
                        <input
                            class="form-control stock text-right"
                            data-field-name="stock"
                            type="number"
                            step="any"
                            name="stock[]"
                            id="stock_${rowCount}"
                            for="${rowCount}"
                            readonly
                        />
                    </td>
                    <td  class="m-0 p-0">
                        <input class="form-control qty text-right " name="qty[]"  data-field-name="qty" type="number"  step="any" 
                         id="qty_${rowCount}"  for="${rowCount}"  />
                    </td>
      
                    <td  class="m-0 p-0">
                        <input class="form-control rate text-right "  name="rate[]" data-field-name="rate" type="hidden" step="any" data-type="rate" id="rate_${rowCount}"  for="${rowCount}" readonly/>
                        <input class="form-control per "  name="per[]" data-field-name="per" type="text" data-type="per" id="per_${rowCount}"  for="${rowCount}"  />
                        <input class="form-control measure_id "  name="measure_id[]" data-field-name="measure_id" type="hidden" data-type="measure_id" id="measure_id_${rowCount}"  for="${rowCount}" readonly/>
                    </td>
                    <td  class="m-0 p-0">
                        <input class="form-control amount  text-right" type="number" step="any"  name="amount[]" id="amount_${rowCount}"  for="${rowCount}" readonly/>
                    </td>
                    
                    <td  class="m-0 p-0">
                        <input type="hidden" name="debit_ledger_id[]"  class="form-control debit_ledger_id_commission ledger_debit" id="ledger_debit_id_${rowCount}"  for="${rowCount}" readonly/>
                        <div style="display: flex;flex-direction: row;">
                                <select name="product_wise_commission_cal[]" class="form-control  left-data product_wise_commision_cal m-0 p-0" id="product_wise_commision_cal_${rowCount}" style="margin:0%;pointer-events: none;" readonly>
                                    <option value="1">(-) %</option>
                                    <option value="2">(+) %</option>
                                    <option value="3">(-)</option>
                                    <option value="4">(+)</option>
                                </select>
                                <input type="number" step="any" name="product_wise_commission_amount[]" id="product_wise_commission_amount_${rowCount}"  class="product_wise_commission_amount form-control text-right mx-0 px-0" style="min-width:60px;" readonly>
                        </div>
                    </td>

                    <td class="m-0 p-0"><input type="number" step="any"
                        name="product_wise_get_commission[]"  id="product_wise_get_commission_${rowCount}"
                        class="product_wise_get_commission form-control text-right" readonly></td>
                </tr>`);
        }
    }

 // calculation total
  function calculation_total(){
      let qty=0;
      let amount=0;
      let commission_total_amont=0;
      $('#orders tr').each(function(i){
          if(parseFloat($(this).find('.qty').val())) qty+=parseFloat($(this).find('.qty').val());
          if(parseFloat($(this).find('.amount').val())) amount+=parseFloat($(this).find('.amount').val());
          if(parseFloat($(this).find('.product_wise_get_commission').val())) commission_total_amont+=parseFloat($(this).find('.product_wise_get_commission').val());
      })
      $('.total_qty').val(parseFloat(qty).toFixed(amount_decimals));
      $('.total_amount').val(parseFloat(amount).toFixed(amount_decimals));
      $('.total_commission').val(parseFloat(commission_total_amont).toFixed(amount_decimals));
      product_wise_commission_per_calcucation();
  }

  $('#orders').on('keyup change','.qty,.rate',function(){
        let qty;
        if(check_current_stock==0){
            if(parseInt($(this).closest('tr').find('.stock').val())>=($(this).closest('tr').find('.qty').val())){
                qty=$(this).closest('tr').find('.qty').val();
            }else if(parseInt($(this).closest('tr').find('.stock').val())>=0){
                $(this).closest('tr').find('.qty').val(parseInt($(this).closest('tr').find('.stock').val()));
                qty=parseInt($(this).closest('tr').find('.stock').val());
            }else{
                $(this).closest('tr').find('.qty').val(0);
                qty=parseInt(0);
            }
        }else{
            qty=$(this).closest('tr').find('.qty').val();
        }
        let rate=$(this).closest('tr').find('.rate').val();
        $(this).closest('tr').find('.amount').val(parseFloat(qty*rate).toFixed(amount_decimals));
        calculation_total();
        product_wise_commission_per_calcucation();
    });

  $('#orders').on('keyup','.amount',function(){
        calculation_total();
        $(this).closest('tr').find('.rate').val(parseFloat($(this).closest('tr').find('.amount').val())/parseFloat($(this).closest('tr').find('.qty').val()));
  });

  $('#orders').on('keyup  change','.product_wise_commission_amount',function(){
      product_wise_commission_per_calcucation();
      calculation_total();
  });



  $(document).on('click', '.btn_remove,.com_btn_remove', function() {
    calculation_total();
 });
 //get product wise commission calcucation Percentage
     function  product_wise_commission_per_calcucation(){
        $('#orders tr').each(function(i){
            let commission_amount=$(this).find('.product_wise_commission_amount').val()||0;
            let get_commission = $(this).find('.product_wise_get_commission').val()||0;
            let commision_cal=$(this).find('.product_wise_commision_cal').val()||0;
           
            let total_amount =$(this).find('.amount').val()||0;
            let qty =$(this).find('.qty').val()||0;
            if(commision_cal==1 || commision_cal==2){
                $(this).find('.product_wise_get_commission').val(parseFloat((commission_amount/100)*parseFloat(total_amount)).toFixed(amount_decimals));
            }else if(commision_cal==3 || commision_cal==4){
                console.log(commission_amount);
                $(this).find('.product_wise_get_commission').val((parseFloat(commission_amount)*parseFloat(qty)).toFixed(amount_decimals));
            }
        });
    }
 calculation_total();
// auto searching
  function getId(element){
      var id, idArr;
      id = element.attr('id');
      idArr = id.split("_");
      return idArr[idArr.length - 1];
    }

var item_check =[];
 // insert sales
 function handleAutocomplete() {
      var fieldName, currentEle
      currentEle = $(this);
      fieldName = currentEle.data('field-name');
      if(typeof fieldName === 'undefined') {
          return false;
      }
      currentEle.autocomplete({
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
                    if(data.ledger_head_id){
                        $('#product_wise_commission_ledger_'+rowNo).val(data.ledger_head_id);
                    }
                     if(data.godown_id){
                                $(this).closest('tr').find('.godown_name').css({backgroundColor: 'white'})
                                $('#godown_name_'+rowNo).val(data.godown_name);
                                $('#godown_id_'+rowNo).val(data.godown_id);
                                current_stock(rowNo,$(this).closest('tr').find('.product_id').val(),data.godown_id,'{{url("current-stock") }}',check_current_stock)
                    }
                    if(data.stock_item_id){
                          current_stock(rowNo,data.stock_item_id,$(this).closest('tr').find('.godown_id').val(),'{{url("current-stock") }}',check_current_stock)
                        $(this).closest('tr').find('.product_name').css({backgroundColor: 'white'});
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
                                        calculation_total();
                                    }else{
                                        $('#rate_'+rowNo).val(0);
                                        calculation_total();
                                    }
                                    if(response.commission){
                                           $('#product_wise_commission_amount_'+rowNo).val(response.commission);
                                           if(response.commission_type){
                                                    $('#product_wise_commision_cal_'+rowNo).val(response.commission_type);
                                            }else{
                                                $('#product_wise_commision_cal_'+rowNo).val(1);
                                            }
                                            product_wise_commission_per_calcucation();
                                            calculation_total();
                                    }else{

                                           $('#product_wise_commission_amount_'+rowNo).val(0);
                                            product_wise_commission_per_calcucation();
                                            calculation_total();
                                    }
                                    if(response.sales_ledger){
                                            $('#ledger_debit_id_'+rowNo).val(response.sales_ledger);
                                        }
                                }else{
                                    $('#rate_'+rowNo).val(0);
                                    product_wise_commission_per_calcucation();
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

 // update sales order
$(document).ready(function(){
    $("#edit_sales_order_id").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id="{{$data->tran_id}}";
        $("#edit_sales_btn").text('Add');
        $.ajax({
                url: "{{url('voucher-sales-order') }}" + '/' + id,
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
input_checking('product');

// delete sales ajax request
$(document).on('click', '.deleteIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id ="{{$data->tran_id}}";
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
                    url: "{{ url('voucher-sales-order') }}" + '/' + id ,
                    type : "POST",
                    data : {'_method' : 'DELETE', '_token' : csrf_token},
                    success: function (data) {
                        swal_message(data.message,'success','Successfully');
                        setTimeout(function () { location.reload() },100);
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

</script>
@endpush
@endsection

