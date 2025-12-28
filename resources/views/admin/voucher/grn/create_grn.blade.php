
@extends('layouts.backend.app')
@section('title','Voucher grn')
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
<form id="add_grn_id" method="POST">
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
                        <input type="text" name="invoice_no" class="form-control m-1 invoice_no" value="{{$voucher_invoice}}" style="border-radius: 15px;" {{$voucher_invoice?'readonly':'autofocus'}} style="color: green" required/>
                        <span id="error_voucher_no" class="text-danger"></span>
                        <input type="hidden" name="voucher_id" class="form-control voucher_id" value="{{$voucher->voucher_id ?? ''}}" />
                        <input type="hidden" name="ch_4_dup_vou_no" class="form-control" value="{{$voucher->ch_4_dup_vou_no ?? ''}}" />
                        <input type="hidden" name="invoice" class="form-control invoice" value="{{$voucher->invoice ?? ''}}" />
                        <input type="hidden" name="row_wise_qty_is" class="form-control" value="{{$voucher->row_wise_qty_is ?? ''}}" />
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Ref No:</label>
                        <input type="text" name="ref_no" class="form-control m-1" style="border-radius: 15px;" autofocus />
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
                        <select style="border-radius: 15px;" name="debit_ledger_id" class="form-control m-1 js-example-basic-single js-example-basic debit_ledger_id" required>
                            <option value="">--Select--</option>
                            @if($voucher->debit!=0?$voucher->debit:'')
                            <option value="{{$voucher->debit}}">{{$ledger_name_debit_wise->ledger_name}}</option>
                            @endif {!!html_entity_decode($ledger_tree)!!}
                        </select>
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Party's A/C Name:</label>
                        <label id="credit_amont" style="font-weight: bold; font-size: 18px !important; margin: 2px;"> {{$credit_sum_value??'0.000'}}</label>
                        <input
                            type="text"
                            name="credit_ledger_name"
                            id="credit_ledger_name"
                            class="form-control credit_ledger_name"
                            value="{{$ledger_name_credit_wise->ledger_name??''}}"
                            style="margin-bottom: 4px !important; margin-top: 2px;"
                            required
                        />
                        <span id="error_inventory_value_affected" class="text-danger"></span>
                        <input
                            type="hidden"
                            name="credit_ledger_id"
                            id="credit_ledger_id"
                            class="form-control credit_ledger_id"
                            value="{{$voucher->credit??''}}"
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
                <label for="exampleInputEmail1">Godowns :</label>
                <select name="godown" class="form-control js-example-basic-single godown left-data" required>
                    @if(empty($voucher->godown_id))
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
        <div class="col-sm-4"></div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table customers" style="border: none !important; margin-top: 5px;">
                <thead>
                    <tr>
                        <th class="col-0.5">#</th>
                        <th class="th {{$voucher->remark_is==1?'col-3':'col-4'}}">Product Name</th>
                        <th class="th col-2 {{$voucher->godown_motive==3?'d-none':'' }}{{$voucher->godown_motive==4?'d-none':'' }}">Godown</th>
                        <th class="th col-1.5">Quantity</th>
                        <th class="th col-1.5">Price</th>
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
                        <td><input type="text " style="border-radius: 15px; font-weight: bold;" class="total_qty form-control text-right" readonly /></td>
                        <td></td>
                        <td></td>
                        <td><input type="text " name="total_credit" style="border-radius: 15px; font-weight: bold;" class="total_credit form-control text-right" readonly /></td>
                        <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
                    </tr>
                </tfoot>
                <tfoot id="commission_append"></tfoot>
            </table>
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
         @if (user_privileges_check('Voucher',$voucher->voucher_id,'create_role'))
            <button type="submit" class="btn btn-info add_grn_btn cansale_btn" style="width: 116px; border-radius: 15px;">
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
<script type="text/javascript" src="{{asset('voucher_setup/voucher_setup_purchase.js')}}"></script>
<script>
let lilineComRowCount;
@if($voucher->debit!=0?$voucher->debit:'')
    $('.debit_ledger_id').val('{{$voucher->debit}}');
@endif
//checking item is null
$(":submit").attr("disabled", true);

// credit ledger searching
$(document).ready(function () {
    //duplicate voucher check
    duplicate_invoice_check();

  $('.credit_ledger_name').autocomplete({
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
                        $("#credit_ledger_name").val(ui.item.value);
                        $("#credit_ledger_id").val(ui.item.ledger_head_id);
                        $('#error_inventory_value_affected').text('');
                    return true;
                    }else{
                        $("#credit_ledger_name").val('');
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
      resetForm("add_grn_id");

    // voucher setup and setting variable
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
     // remove row
    $(document).on('click', '.btn_remove', function() {
      var button_id = $(this).attr('id');
      $('#row'+button_id+'').remove();
    });
    // append table
    function addrow (rowCount){
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
                        <input class="form-control qty text-right " name="qty[]"  data-field-name="qty" step="any" type="number" class="qty" id="qty_${rowCount}"  for="${rowCount}"  />
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
   // calculation total
    function calculation_total(){
        let qty=0;
        let amount=0;
        $('#orders tr').each(function(i){
            if(parseFloat($(this).find('.qty').val())) qty+=parseFloat($(this).find('.qty').val());
            if(parseFloat($(this).find('.amount').val())) amount+=parseFloat($(this).find('.amount').val());
        })
        $('.total_qty').val(parseFloat(qty).toFixed(amount_decimals));
        $('.total_credit').val(parseFloat(amount).toFixed(amount_decimals));
        let commision_cal= $('#commission_append tr').find('.commision_cal').val();
        var commission_amount=$('#commission_append tr').find('.commission_amount').val();
        let current_row=$('#commission_append tr').attr('id');
        commission_per_calcucation();
        commission_calcucation();
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

  $('#orders').on('keyup change select','.product_name,.qty,.rate',function(){
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
       let total_credit=$('.total_credit').val();
       var commission_amount=$(this).closest('tr').find('.commission_amount').val();
       let current_row=$(this).closest('tr').attr('id');
       if((commision_cal==1 || commision_cal==2) && (commission_amount>100)){
            commission_amount=100;
            $(this).closest('tr').find('.commission_amount').val(100)
        }
        if(commision_cal==1){
            $('#commission_append tr').find('#get_commission_'+current_row).val(parseFloat((commission_amount/100)*parseFloat(total_credit)).toFixed(amount_decimals));
        }
        else if(commision_cal==3){
            $('#commission_append tr').find('#get_commission_'+current_row).val(parseFloat((commission_amount)).toFixed(amount_decimals));
        }
        else if(commision_cal==2){
            $('#commission_append tr').find('#get_commission_'+current_row).val(parseFloat((commission_amount/100)*parseFloat(total_credit)).toFixed(amount_decimals));
        }
        else if(commision_cal==4){
            $('#commission_append tr').find('#get_commission_'+current_row).val(parseFloat(commission_amount).toFixed(amount_decimals));
        }
     commission_calcucation();

 });
//  get commission calcucation Percentage
//  function commission_per_calcucation(){
//     let total_credit=$('.total_credit').val();
//     $('#commission_append tr').each(function(i){
//         let commission_amount=$(this).find('.commission_amount').val();
//         let get_commission = $(this).find('.get_commission ').val();
//         let commision_cal=$(this).find('.commision_cal').val();
//         if(commision_cal==1 || commision_cal==2){
//             $(this).find('.get_commission').val(parseFloat((commission_amount/100)*parseFloat(total_credit)).toFixed(amount_decimals));
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
                    console.log(commissionValue,total_amount,commision_cal);
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
    check_item_null({{$voucher->total_qty_is ?? ''}},0);
 });

   let ledger_commission_tree=@json($ledger_commission_tree);
   //append commission table
   $(document).ready(function(){
    $('.js-example-basic-single').select2();
    var ComRowCount=1;
    $('#add_commission').click(function() {
      ComRowCount++;
      $('#commission_append').append(`<tr id="${ComRowCount}">
            <td  class="m-0 p-0"><button  type="button" name="com_btn_remove" id="${ComRowCount}" class="btn btn-danger com_btn_remove cicle m-0  py-1">-</button></td>
            <td class="text-right ">Commission: </td>
            <td  colspan="2" class="m-0 p-0" > <select name="commission_ledger_id[]" class="form-control select2 commission_select2_${ComRowCount}" id="select_${ComRowCount}" >
            <option value="0">--Select--</option>
                ${ledger_commission_tree}
            </select></td>
            <td class="m-0 p-0">
            <select name="commision_cal[]" class="form-control left-data commision_cal" id="commision_cal_${ComRowCount}" style="margin:0%;" >
                <option value="1">(-) %</option>
                <option value="2">(+) %</option>
                <option value="3">(-)</option>
                <option value="4">(+)</option>
            </select>
            </td>
            <td class="m-0 p-0" >
                <input type="number" step="any" name="commission_amount[]"class="form-control commission_amount" id="commission_amount_${ComRowCount}" />
            </td>
            <td class="col-1.5"><input type="text " name="get_commission[]" style="border-radius: 15px;font-weight: bold;" id="get_commission_${ComRowCount}" class="get_commission form-control text-right" readonly></td>
            <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
        </tr>`);
        lilineComRowCount=ComRowCount;
        $('#select_'+ComRowCount).val("{{$voucher->commission_ledger_id??0}}");
        $('#select_'+ComRowCount).select2({
            width:'100%'
        });

    });
});
  $('#commission_append').on('click', '.com_btn_remove', function() {
      var button_id =$(this).closest('tr').remove();

});

  function getId(element){
      var id, idArr;
      id = element.attr('id');
      idArr = id.split("_");
      return idArr[idArr.length - 1];
    }

    var item_check =[];
    var row =[];
// auto searching
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
            if(checkDuplicateItem(selectedData?.item?.data?.stock_item_id)){
                    currentEle.val('');
                }else if(selectedData && selectedData.item && selectedData.item.data){
                  var rowNo, data;
                  rowNo = getId(currentEle);
                  data = selectedData.item.data;
                       check_item_null({{$voucher->total_qty_is ?? ''}},1);
                       if(data.godown_id){
                            $('#godown_name_'+rowNo).val(data.godown_name);
                            $('#godown_id_'+rowNo).val(data.godown_id);
                            $(this).closest('tr').find('.godown_name').css({backgroundColor: 'white'})
                            $(".cansale_btn").attr("disabled", false);
                        }
                        if(data.stock_item_id){
                        $('#product_id_'+rowNo).val(data.stock_item_id);
                        $('#per_'+rowNo).val(data.symbol);
                        $('#measure_id_'+rowNo).val(data.unit_of_measure_id);
                        $(this).closest('tr').find('.product_name').css({backgroundColor: 'white'}) ;
                        $(".cansale_btn").attr("disabled", false);
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
                                        $('#rate_'+rowNo).val(0);
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
    $("#add_grn_id").submit(function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    $("#add_grn_btn").text('Add');
    $(":submit").attr("disabled", true);
        $.ajax({
                url: '{{ route("voucher-grn.store") }}',
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
});
input_checking('godown');
input_checking('product');

</script>
@endpush
@endsection

