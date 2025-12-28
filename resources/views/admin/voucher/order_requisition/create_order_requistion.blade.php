
@extends('layouts.backend.app')
@section('title','Voucher Order Requisition')
@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/jquery-ui.theme.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/jquery-ui.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('voucher_setup/voucher_setup.css')}}">
@endpush
@section('admin_content')
@component('components.voucher', [
    'title' => "$voucher->voucher_name",
    'background_color'=>'#e5e5cd!important',
    'opration'=>'Create',
]);
<!-- Page-header component -->
@slot('voucher_body')
<form id="add_order_requisition_id" method="POST">
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
                        <input type="text" name="reference_no" class="form-control m-1" style="border-radius: 15px;" autofocus />
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row" style="margin-top: 5px;">
                    <div class="col-sm-12" style="margin-left: 5px;!important;">
                        <label class="display" >Party's A/C Name:</label>
                        <label id="credit_amont" style="font-weight: bold; font-size: 18px !important; margin: 2px;">{{$debit_sum_value??'0.000'}}</label>
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
                        <input type="hidden" name="ledger_id" id="credit_ledger_id" class="form-control credit_ledger_id" value="{{$voucher->debit??''}}" style="margin-bottom: 4px !important; border-radius: 15px; margin-top: 2px;" />
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row" style="margin-top: 5px;">
                    <div class="col-sm-4 dis" style="float: right;">
                        <label style="float: right; margin: 2px; margin-right: 82px;">Date:</label><br />
                        <br />
                    </div>
                    <div class="col-sm-8 m-0 p-0" style="margin-left: 5px;!important;">
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Date:</label>
                        <input type="text" name="date" class="form-control setup_date" style="margin-bottom: 4px !important; border-radius: 15px;" value="{{financial_end_date($voucher_date)}}" />

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
                        <th class="th col-1">Quantity</th>
                        <th class="th col-1">Price</th>
                        <th class="th col-1">Per</th>
                        <th class="th col-1">Amount</th>
                        <th class="th col-1">Remarks</th>
                        <th class="th col-1">Brand</th>
                        <th class="th col-1">Order No </th>
                        <th class="th col-1"> Measurement</th>
                    </tr>
                </thead>
                <tbody id="orders"></tbody>
                <tfoot>
                    <tr>
                        <td class="m-0 p-0"><button type="button" id="add" class="btn btn-success cicle m-0 py-1">+</button></td>
                        <td colspan="1" class="text-right">Total:</td>
                        <td><input type="text " style="border-radius: 15px; font-weight: bold;" class="total_qty form-control text-right" readonly /></td>
                        <td></td>
                        <td></td>
                        <td><input type="text " name="total_credit" style="border-radius: 15px; font-weight: bold;" class="total_amount form-control text-right" readonly /></td>
                        <td></td>
                        <td></td>

                        <td class="col-1"></td>
                    </tr>
                </tfoot>
            </table>
            <div class="row" style="margin: 3px;">
                <label style="margin-left: 2px;">Narration:</label>
                <textarea style="margin: 15px;" name="narration" rows="2.5" cols="2.5" class="form-control"></textarea>
            </div>
        </div>
    </div>
    <div align="center">
        <button type="submit" class="btn btn-info add_received_btn" style="width: 116px; border-radius: 15px;">
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
//checking item is null
$(":submit").attr("disabled", true);
$(document).ready(function () {
     // form reset
    resetForm("add_order_requisition_id");
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
                console.log(data);
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
  var amount_decimals="{{company()->amount_decimals}}";
  var check_current_stock= "{{$voucher->amnt_typeable ?? ''}}";
  var remark_is="{{$voucher->remark_is ?? ''}}";
  var stock_item_price_typeabe="{{$voucher->stock_item_price_typeabe ?? ''}}";
  var total_qty_is="{{$voucher->total_qty_is ?? ''}}";
  var total_price_is="{{$voucher->total_price_is ?? ''}}";
  var amount_typeabe="{{$voucher->amount_typeabe ?? ''}}";
  var godown_motive="{{$voucher->godown_motive ?? ''}}";
  var dup_row="{{$voucher->dup_row ?? ''}}";
  var rowCount=1;
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
      for(var row=1; row<6;row++) {
          rowCount++;
              $('#orders').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${rowCount}">
                  <input class="form-control  product_id m-0 p-0"  name="product_id[]" type="hidden" data-type="product_id" id="product_id_${rowCount}"  for="${rowCount}"/>
                  <td  class="m-0 p-0"><button  type="button" name="remove" id="${rowCount}" class="btn btn-danger btn_remove cicle" style="padding: 0px 19px;margin:1px 0px;">-</button></td>
                  <td  class="m-0 p-0">
                      <input class="form-control product_name  autocomplete_txt" name="product_name[]" data-field-name="product_name"  type="text" data-type="product_name" id="product_name_${rowCount}"  autocomplete="off" for="${rowCount}"  />
                  </td>
                  <td  class="m-0 p-0">
                     <input class="form-control qty text-right " name="qty[]"  data-field-name="qty" step="any"  type="number" id="qty_${rowCount}"  for="${rowCount}"  />
                  </td>
                  <td  class="m-0 p-0">
                       <input class="form-control rate text-right "  name="rate[]" data-field-name="rate" type="number" step="any" data-type="rate" id="rate_${rowCount}"  for="${rowCount}"  />
                  </td>
                  <td  class="m-0 p-0">
                       <input class="form-control per "  name="per[]" data-field-name="per" type="text" data-type="per" id="per_${rowCount}"  for="${rowCount}" readonly />
                        <input class="form-control measure_id "  name="measure_id[]" data-field-name="measure_id" type="hidden" data-type="measure_id" id="measure_id_${rowCount}"  for="${rowCount}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control amount  text-right mx-0 px-0" type="number" step="any"  name="amount[]" id="amount_${rowCount}"   for="${rowCount}"/>
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control remark"  name="remark[]" type="text" data-type=" id="remark_${rowCount}"  autocomplete="off" for="${rowCount}"/>
                  </td>
                   <td  class="m-0 p-0">
                      <input class="form-control brand"  name="brand[]" type="text" data-type=" id="brand_${rowCount}"  autocomplete="off" for="${rowCount}"/>
                  </td>
                   <td  class="m-0 p-0">
                      <input class="form-control order_no"  name="order_no[]" type="text" data-type=" id="order_no_${rowCount}"  autocomplete="off" for="${rowCount}"/>
                  </td>

                  <td  class="m-0 p-0">
                      <input class="form-control measurement"  name='measurement[]' type="text" data-type=" id="measurement_${rowCount}"  autocomplete="off" for="${rowCount}"/>
                  </td>
            </tr>`);
      }

  }

  function calculation_total(){
      let total_qty=0;
      let total_amount=0;
      $('#orders tr').each(function(i){
          if(parseFloat($(this).find('.qty').val())) total_qty+=parseFloat($(this).find('.qty').val());
          if(parseFloat($(this).find('.amount').val())) total_amount+=parseFloat($(this).find('.amount').val());
      })
      $('.total_qty').val(parseFloat(total_qty).toFixed(amount_decimals));
      $('.total_amount').val(parseFloat(total_amount).toFixed(amount_decimals));

      // setting checking is total qty and price
      if(total_qty_is==0){
          if(total_qty==0){
            $(":submit").attr("disabled", true);
          }else{
            $(":submit").attr("disabled", false);
          }
      }
      if( total_price_is==0){
          if(total_amount==0){
            $(":submit").attr("disabled", true);
          }else{
            $(":submit").attr("disabled", false);
          }

      }

  }
  $('#orders').on('keyup change','.qty,.rate',function(){
       let qty;

        qty=$(this).closest('tr').find('.qty').val();

       let rate=$(this).closest('tr').find('.rate').val();
       $(this).closest('tr').find('.display_amount').val(parseFloat(qty*rate).toFixed(amount_decimals));
       $(this).closest('tr').find('.amount').val(parseFloat(qty*rate));
      calculation_total();
  });

  $('#orders').on('keyup','.display_amount',function(){
        // setting checking is amount_typeabe
        let amount=$(this).val();
        $(this).closest('tr').find('.amount').val(amount)
        if(amount_typeabe==0){
            calculation_total();
            let qty=$(this).closest('tr').find('.qty').val();
            $(this).closest('tr').find('.rate').val(dividevalue(parseFloat(amount),parseFloat(qty)));
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
                    check_item_null("{{$voucher->total_qty_is ?? ''}}",0);
                }
            },
          select: function( event, selectedData ) {
              if(selectedData && selectedData.item && selectedData.item.data){
                  var rowNo, data;
                  rowNo = getId(currentEle);
                  data = selectedData.item.data;
                  check_item_null({{$voucher->total_qty_is ?? ''}},1);
                  currentEle.css({backgroundColor: 'white'});

                    if(data.stock_item_id){
                        $('#product_id_'+rowNo).val(data.stock_item_id);
                        $('#per_'+rowNo).val(data.symbol);
                        $('#measure_id_'+rowNo).val(data.unit_of_measure_id);
                        //current stock check
                        // stock item get price
                        $.ajax({
                            url: '{{route("searching-stock-item-price") }}',
                            method: 'GET',
                            dataType: 'json',
                            async: false,
                            data: {
                                stock_item_id:data.stock_item_id,
                                voucher_id:"{{$voucher->voucher_id}}",
                                godown_id:$(this).closest('tr').find('.godown_id').val(),
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
                                }else{
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
    $("#add_order_requisition_id").submit(function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    $("#add_transfer_btn").text('Add');
        $.ajax({
                url: '{{ route("voucher-order-requisition.store") }}',
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
});
input_checking('godown');
input_checking('product');
</script>
@endpush
@endsection

