

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
    'opration'=>'Create',
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
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Ref No:</label>
                        <input type="text" name="reference_no" class="form-control m-1" value="{{$data->reference_no}}" style="border-radius: 15px;" />
                        <input type="hidden" name="delete_id" class="form-control delete_id" />

                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row" style="margin-top: 5px;">
                    <div class="col-sm-12" style="margin-left: 5px;!important;">
                        <label class="display" >Party's A/C Name:</label>
                        <label id="credit_amont" style="font-weight: bold; font-size: 18px !important; margin: 2px;">{{$cash_credit_sum_value??'0.000'}}</label>
                        <input
                            type="text"
                            name="debit_ledger_name"
                            id="debit_ledger_name"
                            class="form-control debit_ledger_name"
                            value="{{$ledger_name->ledger_name??''}}"
                            style="margin-bottom: 4px !important; margin-top: 2px;"
                            required
                        />
                        <span id="error_inventory_value_affected" class="text-danger"></span>
                        <input type="hidden" name="ledger_id" id="credit_ledger_id" class="form-control credit_ledger_id" value="{{$data->ledger_id??''}}" style="margin-bottom: 4px !important; border-radius: 15px; margin-top: 2px;" />
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
                <textarea style="margin: 15px;" name="narration" rows="2.5" cols="2.5" class="form-control">{{$data->narration}}</textarea>
            
            </div>
        </div>
    </div>
    <div align="center">
        <button type="submit" class="btn btn-info edit_transfer_btn" style="width: 120px; border-radius: 15px;">
            <span class="m-1 m-t-1" style="color: #404040;"><i class="fa fa-save" style="font-size: 18px;"></i></span><span>Update</span>
        </button>
        @if (user_privileges_check('Voucher',$voucher->voucher_id,'delete_role'))
        <button type="button" class="btn btn-danger deleteIcon" style="width: 120px; border-radius: 15px;">
            <span class="m-1 m-t-1" style="color: #404040;"><i class="fa fa-save" style="font-size: 18px;"></i></span><span>Delete</span>
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

$(document).ready(function(){
  var amount_decimals="{{company()->amount_decimals}}";
  var check_current_stock= "{{$voucher->amnt_typeable ?? ''}}";
  var remark_is="{{$voucher->remark_is ?? ''}}";
  var stock_item_price_typeabe="{{$voucher->stock_item_price_typeabe ?? ''}}";
  var total_qty_is="{{$voucher->total_qty_is ?? ''}}";
  var total_price_is="{{$voucher->total_price_is ?? ''}}";
  var amount_typeabe="{{$voucher->amount_typeabe ?? ''}}";
  var godown_motive="{{$voucher->godown_motive ?? ''}}";
  var dup_row="{{$voucher->dup_row ?? ''}}";
  var t_m_id="{{$data->id}}";
  let p;
        $.ajax({
            type: 'GET',
            url: "{{ url('voucher-order-requisition-show-data')}}",
            async: false,
            data: {
                tran_id:t_m_id
            },
            dataType: 'json',
            success: function (response) {
             $.each(response.data, function(i,val) {
                $('#orders').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${i}">
                    <input class="form-control  id m-0 p-0"  name="id[]" type="hidden" value="${val.id}" data-type="id" id="id_${i}"  for="${i}"/>
                    <input class="form-control  product_id m-0 p-0"  name="product_id[]" type="hidden" value="${val.stock_item_id}" data-type="product_id" id="product_id_${i}"  for="${i}"/>
                    <td  class="m-0 p-0"><button  type="button" name="remove" id="${i}" class="btn btn-danger btn_remove cicle" style="padding: 0px 19px;margin:1px 0px;">-</button></td>
                    <td  class="m-0 p-0">
                        <input class="form-control product_name  autocomplete_txt" name="product_name[]" data-field-name="product_name" value="${val.product_name}"  type="text" data-type="product_name" id="product_name_${i}"  autocomplete="off" for="${rowCount}"  />
                    </td>
                    <td  class="m-0 p-0">
                        <input class="form-control qty text-right " name="qty[]" value="${val.qty||0}"  data-field-name="qty" step="any"  type="number" id="qty_${i}"  for="${i}"  />
                    </td>
                    <td  class="m-0 p-0">
                        <input class="form-control rate text-right "  name="rate[]" value="${val.rate||0}" data-field-name="rate" type="number" step="any" data-type="rate" id="rate_${i}"  for="${i}"  />
                    </td>
                    <td  class="m-0 p-0">
                        <input class="form-control per "  name="per[]" data-field-name="per"  value="${val.symbol||''}" type="text" data-type="per" id="per_${i}"  for="${i}" readonly />
                            <input class="form-control measure_id "  name="measure_id[]" data-field-name="measure_id" type="hidden" data-type="measure_id" id="measure_id_${i}"  for="${i}" readonly />
                    </td>
                    <td  class="m-0 p-0">
                        <input class="form-control amount  text-right mx-0 px-0" type="number" step="any"  value="${val.total||0}"  name="amount[]" id="amount_${i}"   for="${i}"/>
                    </td>
                    <td  class="m-0 p-0">
                        <input class="form-control remark"  name="remark[]" type="text" data-type=" id="remark_${i}" value="${val.remark||''}"  autocomplete="off" for="${i}"/>
                    </td>
                    <td  class="m-0 p-0">
                        <input class="form-control brand"  name="brand[]" type="text" data-type=" id="brand_${i}" value="${val.brand||''}"  autocomplete="off" for="${i}"/>
                    </td>
                    <td  class="m-0 p-0">
                        <input class="form-control order_no"  name="order_no[]" type="text" data-type=" id="order_no_${i}" value="${val.order_no||''}" autocomplete="off" for="${i}"/>
                    </td>
                    <td  class="m-0 p-0">
                        <input class="form-control measurement"  name='measurement[]' type="text" data-type=" id="measurement_${i}" value="${val.measurement||''}"  autocomplete="off" for="${i}"/>
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
        arr_in.push($(this).closest('tr').find('.id').val());
        $('.delete_id').val(arr_in);

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
        qty=$(this).closest('tr').find('.qty').val();
       let rate=$(this).closest('tr').find('.rate').val();
       $(this).closest('tr').find('.amount').val(parseFloat(qty*rate));
       $(this).closest('tr').find('.display_amount').val(parseFloat(qty*rate).toFixed(amount_decimals));
      calculation_total();
  });

  $('#orders').on('keyup','.display_amount',function(){
    // setting checking is amount_typeabe
        let amount=$(this).val();
        $(this).closest('tr').find('.amount').val(amount)

      if(amount_typeabe==0){
        calculation_total();
        let qty=$(this).closest('tr').find('.qty').val();
        $(this).closest('tr').find('.rate').val(dividevalue(parseFloat(amount)/parseFloat(qty)));
      }
  });

  // on change godown amount calculation
  $('.godown').on('change',function(){
    $('#orders tr').find('.godown_name').each(function(){
        let qty=$(this).closest('tr').find('.qty').val();
        let rate=$(this).closest('tr').find('.rate').val();
        $(this).closest('tr').find('.amount').val(parseFloat(qty*rate));
        $(this).closest('tr').find('.display_amount').val(parseFloat(qty*rate).toFixed(amount_decimals));
    });
    calculation_total();
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
                                    godown_id:$(this).closest('tr').find('.godown_id').val(),
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
        var id="{{$data->id}}";
        $("#edit_transfer_btn").text('Update');
        $.ajax({

                url: "{{ url('voucher-order-requisition') }}" + '/' + id,
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data,status,xhr) {
                    swal_message(data.message,'success','Successfully');
                    // setTimeout(function () {  window.location.href='{{route("daybook-report.index")}}'; },100);
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
// delete sales ajax request
$(document).on('click', '.deleteIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id ="{{$data->id}}";
        swal(swal_data()).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('voucher-order-requisition') }}" + '/' + id ,
                    type : "POST",
                    data : {'_method' : 'DELETE', '_token' : csrf_token},
                    success: function (data) {
                        swal_message(data.message,'success','Successfully');
                        setTimeout(function () {  window.location.href='{{route("daybook-report.index")}}'; },100);

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


input_checking('godown');
input_checking('product');
</script>
@endpush
@endsection

