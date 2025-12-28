
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
<form id="add_transfer_id" method="POST">
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
                        <input type="hidden" name="check_current_stock" class="form-control" value="{{$voucher->amnt_typeable ?? ''}}" />
                        <input type="hidden" name="row_wise_qty_is" class="form-control" value="{{$voucher->row_wise_qty_is ?? ''}}" />
                        <input type="hidden" id="allowAllStock" value="1"  />
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Ref No:</label>
                        <input type="text" name="ref_no" class="form-control m-1" style="border-radius: 15px;" autofocus />
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row" style="margin-top: 5px;">
                    <div class="col-sm-12" style="margin-left: 5px;!important;">
                        <label for="exampleInputEmail1">Destination Godowns :</label>
                        <select name="godown_id_in" class="form-control js-example-basic-single godown_in left-data {{$voucher->destination_godown_motive==3?'d-none ':'' }}" required>
                          <option value="">--select--</option>
                           @if($voucher->destination_godown_motive==3)
                           @foreach ($godowns as $godown)
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
                        <input type="text" name="invoice_date" class="form-control setup_date invoice_date" style="margin-bottom: 4px !important; border-radius: 15px;" value="{{financial_end_date($voucher_date)}}" />
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
                <select name="customer_id" class="form-control js-example-basic-single left-data" required>
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
                <select name="godown" class="form-control js-example-basic-single godown left-data" required>
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
            <select style="border-radius: 15px;" name="dis_cen_id" class="DistributionCenter form-control m-1 js-example-basic-single js-example-basic" required>
                @if(empty($voucher->distribution_center_id))
                      <option value="">--Select--</option>
                @endif
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
                        @if($voucher->godown_motive==3||$voucher->godown_motive==4)
                         <th class="th {{$voucher->remark_is==1?'col-4':'col-6'}}">Product Name</th>
                        @else
                         <th class="th {{$voucher->remark_is==1?'col-3':'col-4'}}">Product Name</th>
                        @endif
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
                        <td><input type="text " style="border-radius: 15px; font-weight: bold;" class="total_qty form-control text-right" readonly /></td>
                        <td></td>
                        <td></td>
                        <td><input type="text " name="total_credit" style="border-radius: 15px; font-weight: bold;" class="total_amount form-control text-right" readonly /></td>
                        <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
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
                    <textarea style="margin-left: 2px; border-radius: 15px;" name="secret_narration" rows="2.5" cols="2.5" class="form-control"></textarea>
                </div>
            @endif
        </div>
    </div>
    <div align="center">
         @if (user_privileges_check('Voucher',$voucher->voucher_id,'create_role'))
            <button type="submit" class="btn btn-info add_received_btn" style="width: 116px; border-radius: 15px;">
                <span class="m-1 m-t-1" style="color: #404040;"><i class="fa fa-save" style="font-size: 18px;"></i></span><span>Save</span>
            </button>
         @endif
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
$(document).ready(function(){
    // form reset
    resetForm("add_transfer_id");
  var amount_decimals="{{company()->amount_decimals}}";
  var check_current_stock= "{{$voucher->amnt_typeable ?? ''}}";
  var remark_is="{{$voucher->remark_is ?? ''}}";
  var stock_item_price_typeabe="{{$voucher->stock_item_price_typeabe ?? ''}}";
  var total_qty_is="{{$voucher->total_qty_is ?? ''}}";
  var total_price_is="{{$voucher->total_price_is ?? ''}}";
  var amount_typeabe="{{$voucher->amount_typeabe ?? ''}}";
  var godown_motive="{{$voucher->godown_motive ?? ''}}";
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
                      <input class="form-control godown_name autocomplete_txt " name="godown_name[]" value="${godown_id?godown_name:''}"  data-field-name="godown_name" type="text"  id="godown_name_${rowCount}"  for="${rowCount}" ${godown_motive==2?'readonly':''}  autocomplete="off" />
                      <input class="form-control godown_id text-right " name="godown_id[]"  data-field-name="godown_id" value="${godown_id}" type="hidden"  id="godown_id_${rowCount}"  for="${rowCount}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control stock text-right"   data-field-name="stock" type="number"  step="any"
                       id="stock_${rowCount}"  for="${rowCount}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control qty text-right " name="qty[]"  data-field-name="qty" type="number" step="any"
                       id="qty_${rowCount}"  for="${rowCount}"  />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control rate text-right "  name="rate[]" data-field-name="rate" type="number" step="any"
                      data-type="rate" id="rate_${rowCount}"  for="${rowCount}" ${stock_item_price_typeabe==0?'readonly':''} />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control per  "  name="per[]" data-field-name="per" type="text" data-type="per" id="per_${rowCount}"  for="${rowCount}" readonly />
                      <input class="form-control measure_id "  name="measure_id[]" data-field-name="measure_id" type="hidden" data-type="measure_id" id="measure_id_${rowCount}"  for="${rowCount}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input
                        class="form-control display_amount  text-right"
                        name="display_amount[]"
                        id="display_amount_${rowCount}"
                        type="number"
                        step="any"
                        ${amount_typeabe==1?'readonly':''}
                        for="${rowCount}"
                        />
                      <input
                            class="form-control amount  text-right"
                            type="hidden"
                            step="any"
                            name="amount[]"
                            id="amount_${rowCount}"
                            ${amount_typeabe==1?'readonly':''}
                            for="${rowCount}"
                        />
                  </td>
                  <td  class="m-0 p-0 ${remark_is==0?'d-none':''}">
                      <input class="form-control remark"  name='remark[]' type="text" data-type=" id="remark_${rowCount}"  autocomplete="off" for="${rowCount}"/>
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
       if(check_current_stock==0){
            if(parseFloat($(this).closest('tr').find('.stock').val())>=($(this).closest('tr').find('.qty').val())){
                qty=$(this).closest('tr').find('.qty').val();
            }else if(parseFloat($(this).closest('tr').find('.stock').val())>=0){
                $(this).closest('tr').find('.qty').val(parseFloat($(this).closest('tr').find('.stock').val()));
                qty=parseFloat($(this).closest('tr').find('.stock').val());
            }else{
                $(this).closest('tr').find('.qty').val(0);
                qty=0;
            }
       }else{
          qty=$(this).closest('tr').find('.qty').val();
       }
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
                    else if($(this).attr('name')==='godown_name[]')$(this).closest('tr').find('.godown_id').val('');
                    $(this).focus();
                    check_item_null("{{$voucher->total_qty_is ?? ''}}",0);
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
                        //current stock check
                        current_stock(rowNo,$(this).closest('tr').find('.product_id').val(),data.godown_id,'{{url("current-stock") }}',check_current_stock)
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
    $("#add_transfer_id").submit(function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    $("#add_transfer_btn").text('Add');
    $(":submit").attr("disabled", true);
        $.ajax({
                url: '{{ route("voucher-transfer.store") }}',
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
                    $(":submit").attr("disabled", false);
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
    $('.godown').change(function () {        
        let godown_name = $(this).find('option:selected').text().trim() ;
        $('.DistributionCenter option').each(function () {
            if ($(this).text().trim() === godown_name) {
                 $('.DistributionCenter').val($(this).val()).trigger('change');
            } else {
            }
        });
    });
});
input_checking('godown');
input_checking('product');
</script>
@endpush
@endsection

