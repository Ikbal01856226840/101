
@extends('layouts.backend.app')
@section('title','Voucher Sales')
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
<form id="add_sales_id" method="POST">
    @csrf
    <div class="page-body">
        <div class="row margin">
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-4 dis" style="float: left;">
                        <label style="float: left; margin: 2px;">Invoice No:</label><br />
                        <br />
                        <label style="float: left; margin: 2px; margin-right: 29px;">Ref No:</label>
                        @if (company()->gate_pass_ref!=0)
                          <label style="float: left; margin: 2px; margin-right: 29px;">Gate Pass Ref:</label>
                        @endif
                    </div>
                    <div class="col-sm-8 m-0 p-0" style="margin-left: 5px;">
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Invoice No:</label>
                        <input type="text" name="invoice_no" class="form-control m-1" value="{{$voucher_invoice}}" style="border-radius: 15px;" {{$voucher_invoice?'readonly ':'autofocus'}} style="color: green" required />
                        <span id="error_voucher_no" class="text-danger"></span>
                        <input type="hidden" name="voucher_id" class="form-control voucher_id" value="{{$voucher->voucher_id ?? ''}}" />
                        <input type="hidden" name="ch_4_dup_vou_no" class="form-control" value="{{$voucher->ch_4_dup_vou_no ?? ''}}" />
                        <input type="hidden" name="invoice" class="form-control" value="{{$voucher->invoice ?? ''}}" />
                        <input type="hidden" name="check_current_stock" class="form-control" value="{{$voucher->amnt_typeable ?? ''}}" />
                        <input type="hidden" name="row_wise_qty_is" class="form-control" value="{{$voucher->row_wise_qty_is ?? ''}}" />
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Ref No:</label>
                        <input type="text" name="ref_no" class="form-control m-1" style="border-radius: 15px;" autofocus />
                        <!-- resposive lebel change -->
                        @if (company()->gate_pass_ref!=0)
                            <label class="display" style="display:none">Gate Pass Ref:</label>
                            <input type="text" name="gprf" class="form-control m-1" style="border-radius: 15px;" autofocus />
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row" style="margin-top: 5px;">
                    <div class="col-sm-4 dis" style="float: right;">
                        @if($voucher->commission_is!=1)
                        <label style="float: right; margin: 2px; margin-top: 4px;" for="exampleInputEmail1">Ledger Name :</label><br />
                        <br />
                        @else
                        <label style="float: right; margin: 2px; margin-top: 4px;" for="exampleInputEmail1">Distribution Center :</label><br />
                        <br />
                        @endif
                        <label style="float: right; margin: 2px; margin-top: 4px;" for="exampleInputEmail1">Party's A/C Name:</label><br />
                        <br />
                    </div>
                    @if($voucher->commission_is!=1)
                    <div class="col-sm-8 m-0 p-0" style="margin-left: 5px;">
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Ledger Name :</label>
                        <select style="border-radius: 15px;" name="debit_ledger_id" class="form-control m-1 js-example-basic-single js-example-basic debit_ledger_id" required>
                            <option value="">--Select--</option>
                            @if($voucher->credit!=0?$voucher->credit:'')
                            <option value="{{$voucher->credit}}">{{$ledger_name_credit_wise->ledger_name}}</option>
                            @endif {!!html_entity_decode($ledger_tree)!!}
                        </select>
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Party's A/C Name:</label>
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
                        <input type="hidden" name="credit_ledger_id" id="credit_ledger_id" class="form-control credit_ledger_id" value="{{$voucher->debit??''}}" style="margin-bottom: 4px !important; border-radius: 15px; margin-top: 2px;" />
                    </div>
                    @else
                    <div class="col-sm-8 m-0 p-0" style="margin-left: 5px;!important;">
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Distribution Center :</label>
                        <div class="form-group">
                            <select style="border-radius: 15px;" name="dis_cen_id" class="form-control m-1 js-example-basic-single js-example-basic" required>
                                @if(empty($voucher->distribution_center_id))
                                    <option value="">--Select--</option>
                                @endif
                                @foreach ($distributionCenter as $distribution)
                                <option value="{{$distribution->dis_cen_id }}">{{$distribution->dis_cen_name}}</option>
                                @endforeach
                            </select>
                            <!-- resposive lebel change -->
                            <label class="display" style="display:none">Party's A/C Name:</label>
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
                            <input
                                type="hidden"
                                name="credit_ledger_id"
                                id="credit_ledger_id"
                                class="form-control credit_ledger_id"
                                value="{{$voucher->debit??''}}"
                                style="margin-bottom: 4px !important; border-radius: 15px; margin-top: 2px;"
                            />
                        </div>
                    </div>
                    @endif
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
    <div class="col-sm-3">
            <div class="form-group">
                <label for="exampleInputEmail1">Sales Type :</label>
                <select name="sale_type" class="form-control js-example-basic-single sale_type" required>
                    <option @selected($voucher->sale_type==2) value="2">Sales Credit</option>
                    <option @selected($voucher->sale_type==0) value="0">None</option>
                    <option @selected($voucher->sale_type==1) value="1">Cash Sales</option>
                    <option @selected($voucher->sale_type==3) value="3">Inter Company Sale</option>
                </select>
            </div>
        </div>
        <div class="col-sm-3">
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
        <div class="col-sm-3 {{$voucher->godown_motive==3?'d-none ':'' }}">
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
                    @else
                        @foreach ($godowns as $godown)
                            <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-sm-3">
            @if($voucher->commission_is!=1)
            <div class="form-group">
                <label for="exampleInputEmail1">Distribution Center :</label>
                <select name="dis_cen_id" class="form-control js-example-basic-single dis_cen_id" required>
                    @if(empty($voucher->distribution_center_id))
                     <option value="">--Select--</option>
                    @endif
                    @foreach ($distributionCenter as $distribution)
                        <option value="{{$distribution->dis_cen_id }}">{{$distribution->dis_cen_name}}</option>
                    @endforeach
                </select>
            </div>
            @else
            <!-- <div class="form-group"  >
                <label  for="exampleInputEmail1">Ledger Name  :</label>
                <select class="form-control js-example-basic-single ledger">
                    <option value="">--Select--</option>
                    @if($voucher->credit!=0?$voucher->credit:'')
                        <option value="{{$voucher->credit}}">{{$ledger_name_credit_wise->ledger_name}}</option>
                    @endif
                    {!!html_entity_decode($ledger_tree)!!}
                </select>
            </div> -->
            @endif
        </div>
    </div>
    <div class="row cash_ledger mt-0 pt-0 {{$voucher->sale_type==1?'':'d-none'}}" >
        <div class="col-sm-3 mt-0 pt-0"  >
            <label class="salse_cash_ledger_amount" style="font-weight: bold; font-size: 18px !important; margin: 0px;">0.000</label>
            <div class="form-group" >
                <label for="exampleInputEmail1">Cash Party's A/C Name:</label>
                <input
                    type="text"
                    name="salse_cash_ledger_name"
                    class="form-control salse_cash_ledger_name"
                    style="margin-bottom: 4px !important; margin-top: 2px;"                    
                    disabled="{{$voucher->sale_type==1}}"
               />
               <input  type="hidden"  name="salse_cash_ledger_id" class="form-control salse_cash_ledger_id" disabled="{{$voucher->sale_type==1}}"/>
               <span  class="text-danger salse_cash_value_affected"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table customers" style="border: none !important; margin-top: 5px;">
                <thead>
                    <tr>
                        <th class="col-0.5">#</th>
                        <th class="th {{$voucher->commission_is==1?'col-2':($voucher->remark_is==1?'col-3':'col-4')}}">Product Name</th>
                        <th class="th {{$voucher->commission_is==1?'col-1':'col-2'}} {{$voucher->godown_motive==3?'d-none':'' }}{{$voucher->godown_motive==4?'d-none':'' }}">Godown</th>
                        <th class="th col-1">stock</th>
                        <th class="th col-1">Quantity</th>
                        <th class="th col-1">Price</th>
                        <th class="th col-0.5">Per</th>
                        <th class="th {{$voucher->commission_is==1?'col-1.5':'col-2'}}">Amount</th>
                        @if($voucher->commission_is==1)
                        <th class="th col-1">Sales Ledger</th>
                        <th class="th col-1">Commission</th>
                        <th class="th col-1">Type- %</th>
                        <th class="th col-1.5">
                            Comm <br />
                            Amount
                        </th>
                        @endif @if($voucher->godown_motive==3)
                        <th class="th {{$voucher->godown_motive==3?'col-1':'col-1'}}  {{$voucher->remark_is==0?'d-none':'' }}">Remarks</th>
                        @elseif($voucher->godown_motive==4)
                        <th class="th {{$voucher->godown_motive==4?'col-1':'col-1'}} {{$voucher->remark_is==0?'d-none':'' }}">Remarks</th>
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
                        <td></td>
                        <td><input type="number" name="get_without_commission"  step="any"
                            style="font-weight: bold;"
                            class="form-control text-right get_commission_without mx-0 px-0" readonly /></td>
                        <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
                    </tr>
                </tfoot>
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
                        <td><input type="text " class="total_qty form-control text-right" style="font-weight: bold;" readonly /></td>
                        <td></td>
                        <td></td>
                        <td><input type="text " name="total_amount" style="font-weight: bold;" class="total_amount form-control text-right mx-0 px-0" readonly /></td>
                        @if($voucher->commission_is==1)
                        <td></td>
                        <td></td>
                        <td colspan="2"><input type="number" name="product_wise_get_commission_without"  step="any"
                            style="font-weight: bold;"
                            class="product_wise_get_commission_without form-control text-right mx-0 px-0" readonly /></td>
                        @endif
                        <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
                    </tr>
                </tfoot>
                @if($voucher->commission_is==1)
                <tfoot>
                    <tr>
                        <td class="m-0 p-0"></td>
                        <td colspan="1" class="text-right">Commission Total :</td>
                        @if($voucher->godown_motive==1)
                        <td></td>
                        @elseif($voucher->godown_motive==2)
                        <td></td>
                        @endif
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><input type="number" name="product_wise_get_commission_without"  step="any"
                            style="font-weight: bold;"
                            class="form-control text-right product_wise_get_commission_without mx-0 px-0"
                            readonly /></td>
                        <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
                    </tr>
                </tfoot>
                <tfoot>
                    <tr>
                        <td class="m-0 p-0"></td>
                        <td colspan="1" class="text-right">Net Total :</td>
                        @if($voucher->godown_motive==1)
                        <td></td>
                        @elseif($voucher->godown_motive==2)
                        <td></td>
                        @endif
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><input type="number" name="nettotal" style="font-weight: bold;"  step="any"
                            class="form-control text-right nettotal mx-0 px-0" readonly /></td>
                        <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
                    </tr>
                </tfoot>
                @endif
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

<script>
      // voucher setup and setting variable
    var amount_decimals="{{company()->amount_decimals}}";
    var check_current_stock= "{{$voucher->amnt_typeable ?? ''}}";
    var remark_is="{{$voucher->remark_is ?? ''}}";
    var stock_item_price_typeabe="{{$voucher->stock_item_price_typeabe ?? ''}}";
    let total_qty_is="{{$voucher->total_qty_is ?? ''}}";
    var total_price_is="{{$voucher->total_price_is ?? ''}}";
    var amount_typeabe="{{$voucher->amount_typeabe ?? ''}}";
    var godown_motive="{{$voucher->godown_motive ?? ''}}";
    var commission_is="{{$voucher->commission_is}}";
    let ledger_tree=@json($ledger_tree);

</script>

<script type="text/javascript" src="{{asset('libraries/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{asset('voucher_setup/vocher_setup_sales.js')}}"></script>
@if ($voucher->sale_type==1)
    <script>
        party_ledger_autocomplete("{{$voucher->voucher_id}}",'salse_cash_ledger_amount','salse_cash_ledger_name','salse_cash_ledger_id','salse_cash_value_affected');
    </script>
@endif
<script>
    // sales type
    $('.sale_type').on('change',function(){
       if($('.sale_type').val()==1){
            party_ledger_autocomplete("{{$voucher->voucher_id}}",'salse_cash_ledger_amount','salse_cash_ledger_name','salse_cash_ledger_id','salse_cash_value_affected');
            $(".salse_cash_ledger_id").attr("disabled", false);
            $(".salse_cash_ledger_name").attr("disabled", false);
            $(".cash_ledger").removeClass("d-none");
       }else{
            $(".salse_cash_ledger_id").attr("disabled", true);
            $(".salse_cash_ledger_name").attr("disabled", true);
            $(".cash_ledger").addClass("d-none");
       }
    });
    @if($voucher->credit!=0?$voucher->credit:'')
        $('.debit_ledger_id').val('{{$voucher->credit}}');
    @endif
    // checking item null;
    $(":submit").attr("disabled", true);
    // debit ledger searching
$(document).ready(function () {
     // form reset
     resetForm("add_sales_id");

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

    $(document).on('change click keyup ','.product_name',function(){

        // check_item_null({{$voucher->total_qty_is ?? ''}},0);
    });
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
                    <td  class="m-0 p-0"><button  type="button" name="remove" id="${rowCount}" class="btn btn-danger btn_remove cicle " style="padding: 0px 19px;margin:1px 0px;">-</button></td>
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
                        <input class="form-control qty text-right " name="qty[]"  data-field-name="qty" step="any"  type="number" class="qty" id="qty_${rowCount}"  for="${rowCount}"  />
                    </td>
                    <td  class="m-0 p-0">
                        <input class="form-control rate text-right "  name="rate[]" data-field-name="rate" type="number" step="any" data-type="rate" id="rate_${rowCount}"  for="${rowCount}" ${stock_item_price_typeabe==0?'readonly':''} />
                    </td>
                    <td  class="m-0 p-0">
                        <input class="form-control per "  name="per[]" data-field-name="per" type="text" data-type="per" id="per_${rowCount}"  for="${rowCount}" readonly />
                        <input class="form-control measure_id "  name="measure_id[]" data-field-name="measure_id" type="hidden" data-type="measure_id" id="measure_id_${rowCount}"  for="${rowCount}" readonly />
                    </td>
                    <td  class="m-0 p-0">
                        <input class="form-control amount  text-right mx-0 px-0" type="number" step="any"  name="amount[]" id="amount_${rowCount}" ${amount_typeabe==1?'readonly':''}  for="${rowCount}"/>
                    </td>
                    ${(commission_is==1)?
                    `<td  class="m-0 p-0">
                        <select  name="debit_ledger_id[]"  class="form-control debit_ledger_id_commission ledger_debit" id="ledger_debit_id_${rowCount}">
                            <option value="">--Select--</option>
                            @if($voucher->credit!=0?$voucher->credit:'')
                            <option value="{{$voucher->credit}}">{{$ledger_name_credit_wise->ledger_name}}</option>
                            @endif
                            ${ledger_tree}
                        </select>
                    </td>
                    <td  class="m-0 p-0">
                        <input type="hidden" name="commission_is" value="${commission_is}">
                        <input class="form-control ledger_name  autocomplete_txt"  name="ledger_name[]" data-field-name="ledger_name"  type="text" data-type="ledger_name" id="ledger_name_${rowCount}" value="{{$ledger_id_wise?$ledger_id_wise->ledger_name:''}}" autocomplete="off" for="${rowCount}"  />
                        <input class="form-control product_wise_commission_ledger " name="product_wise_commission_ledger[]" data-field-name="product_wise_commission_ledger"  type="hidden" data-type="product_wise_commission_ledger" id="product_wise_commission_ledger_${rowCount}" value="{{$ledger_id_wise?$ledger_id_wise->ledger_head_id:''}}" autocomplete="off" for="${rowCount}"/>
                    </td>
                    <td  class="m-0 p-0">
                        <div style="display: flex;flex-direction: row;">
                                <select name="product_wise_commission_cal[]" id="product_wise_commision_cal_${rowCount}" class="form-control left-data product_wise_commision_cal m-0 p-0"  style="margin:0%;" >
                                    <option value="1">(-) %</option>
                                    <option value="2">(+) %</option>
                                    <option value="3">(-)</option>
                                    <option value="4">(+)</option>
                                </select>
                                <input type="number" step="any" name="product_wise_commission_amount[]" id="product_wise_commission_amount_${rowCount}"  class="product_wise_commission_amount form-control text-right mx-0 px-0" style="min-width:60px;">
                        </div>
                    </td>
                    <td class="m-0 p-0"><input type="number" step="any" name="product_wise_get_commission[]"  id="product_wise_get_commission_${rowCount}" class="product_wise_get_commission form-control text-right mx-0 px-0" readonly ></td>
                    `:`""`}

                    <td  class="m-0 p-0 ${remark_is==0?'d-none':''}">
                        <input class="form-control remark"  name='remark[]' type="text" data-type=" id="remark_${rowCount}"  autocomplete="off" for="${rowCount}"/>
                    </td>
                </tr>`);
                adjustFontSize($(`#godown_name_${rowCount}`)[0],godown_name);
                // select debit ledger
                // $('.ledger_debit').val($('.ledger').val());
                @if($voucher->credit!=0?$voucher->credit:'')
                  $('.debit_ledger_id_commission').val('{{$voucher->credit}}');
                @endif
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
        $('.total_amount').val(parseFloat(amount).toFixed(amount_decimals));
        let commision_cal= $('#commission_append tr').find('.commision_cal').val();
        var commission_amount=$('#commission_append tr').find('.commission_amount').val();
        let current_row=$('#commission_append tr').attr('id');
        product_wise_commission_per_calcucation();
        product_wise_commission_calcucation();
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
                qty=parseFloat(0);
            }
        }else{
            qty=$(this).closest('tr').find('.qty').val();
        }
        let rate=$(this).closest('tr').find('.rate').val();
        $(this).closest('tr').find('.amount').val(parseFloat(qty*rate).toFixed(amount_decimals));
        calculation_total();
        stockCalculation();
    });


    $('#orders').on('keyup','.amount',function(){
        // setting checking is amount_typeabe
        if(amount_typeabe==0){
            calculation_total();
            $(this).closest('tr').find('.rate').val(parseFloat($(this).closest('tr').find('.amount').val())/parseFloat($(this).closest('tr').find('.qty').val()));
        }
    });

    // onchange godown amount calculation
    $('.godown').on('change',function(){
        $('#orders tr').find('.godown_name').each(function(){
            $(this).closest('tr').find('.amount').val(parseFloat($(this).closest('tr').find('.qty').val()*$(this).closest('tr').find('.rate').val()).toFixed(amount_decimals));
        });
        calculation_total();
    });
    // commission keyup or on change
    $('#commission_append').on('keyup change', '.commission_amount,.commision_cal', function() {
        let commision_cal=$(this).closest('tr').find('.commision_cal').val();
        let total_amount=$('.nettotal').val() || $('.total_amount').val();
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
            commission_per_calcucation();
            commission_calcucation();
    });

    //product wise commission keyup or on change
    $('#orders').on('keyup change', '.product_wise_commission_amount,.product_wise_commision_cal', function() {
            let commision_cal=$(this).closest('tr').find('.product_wise_commision_cal').val();
            let total_amount=$(this).closest('tr').find('.amount').val();
            let qty =$(this).closest('tr').find('.qty').val()||0;
            let commission_amount=$(this).closest('tr').find('.product_wise_commission_amount').val();
            if((commision_cal==1 || commision_cal==2) && (commission_amount>100)){
                commission_amount=100;
                $(this).closest('tr').find('.product_wise_commission_amount').val(100)
            }
            let current_row=$(this).closest('tr').attr('id');
                if(commision_cal==1){
                    $(this).closest('tr').find('.product_wise_get_commission').val(parseFloat((commission_amount/100)*parseFloat(total_amount)).toFixed(amount_decimals));
                }
                else if(commision_cal==3){
                    $(this).closest('tr').find('.product_wise_get_commission').val(parseFloat((commission_amount)*parseFloat(qty)).toFixed(amount_decimals));
                }
                else if(commision_cal==2){
                    $(this).closest('tr').find('.product_wise_get_commission').val(parseFloat((commission_amount/100)*parseFloat(total_amount)).toFixed(amount_decimals));
                }
                else if(commision_cal==4){
                    $(this).closest('tr').find('.product_wise_get_commission').val(parseFloat((commission_amount)*parseFloat(qty)).toFixed(amount_decimals));
                }
            product_wise_commission_calcucation();
            commission_per_calcucation();
            commission_calcucation();

    });

    //  get commission calcucation Percentage
    // function commission_per_calcucation(){
    //           let total_amount=$('.nettotal').val() || $('.total_amount').val();
    //         $('#commission_append tr').each(function(i){
    //             let commission_amount=$(this).find('.commission_amount').val();
    //             let get_commission = $(this).find('.get_commission ').val();
    //             let commision_cal=$(this).find('.commision_cal').val();
    //             if(commision_cal==1 || commision_cal==2){
    //                 $(this).find('.get_commission').val(parseFloat((commission_amount/100)*parseFloat(total_amount)).toFixed(amount_decimals));
    //             }
    //     });

    // }
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

      //get product wise commission calcucation Percentage
    function  product_wise_commission_per_calcucation(){
        $('#orders tr').each(function(i){
            let commission_amount=$(this).find('.product_wise_commission_amount').val()||0;
            let get_commission = $(this).find('.product_wise_get_commission ').val()||0;
            let commision_cal=$(this).find('.product_wise_commision_cal').val()||0;
            let total_amount =$(this).find('.amount').val()||0;
            let qty =$(this).find('.qty').val()||0;
            if(commision_cal==1 || commision_cal==2){
                $(this).find('.product_wise_get_commission').val(parseFloat((commission_amount/100)*parseFloat(total_amount)).toFixed(amount_decimals));
            }else if(commision_cal==3 || commision_cal==4){
                $(this).find('.product_wise_get_commission').val((parseFloat(commission_amount)*parseFloat(qty)).toFixed(amount_decimals));
            }
        });
    }

    // get commission cal
    function  commission_calcucation(){
        let total_val=$('.nettotal').val() || $('.total_amount').val();
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

    // get  product wise commission cal
    function  product_wise_commission_calcucation(){
        let total_val=$('.total_amount').val();
        let product_wise_total_com=0;
        $('#orders tr').each(function(i){
            let get_commission =$(this).find('.product_wise_get_commission').val();
           if(get_commission){
                if($(this).find('.product_wise_commision_cal').val()==1 || $(this).find('.product_wise_commision_cal').val()==3){
                    total_val=parseFloat(total_val)-parseFloat(get_commission);
                    product_wise_total_com-=parseFloat(get_commission);
                }else if($(this).find('.product_wise_commision_cal').val()==2 || $(this).find('.product_wise_commision_cal').val()==4){
                    total_val=parseFloat(total_val)+parseFloat(get_commission);
                    product_wise_total_com+=parseFloat(get_commission);
                }
           }
        });
        let commission=$('#orders tr').find('.product_wise_get_commission').val();
        if(commission!=0){
            $('.product_wise_get_commission_without').val(parseFloat(Math.abs(product_wise_total_com)).toFixed(amount_decimals))
        }else{
            $('.product_wise_get_commission_without').val(parseFloat(Math.abs(product_wise_total_com)).toFixed(amount_decimals))
        }
        $('.nettotal').val(parseFloat(Math.abs(total_val)).toFixed(amount_decimals)) ;
    }

    $(document).on('click', '.btn_remove,.com_btn_remove', function() {
        calculation_total();
        check_item_null({{$voucher->total_qty_is ?? ''}},'');
        stockCalculation();
    });

    // key down current price
    $("#orders").on('keydown', '.product_name', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
            let stock_item_id= $(this).closest('tr').find('.product_id').val();
            tab_price.call($(this),stock_item_id,"{{$voucher->voucher_id}}",'{{route("searching-stock-item-price") }}',check_current_stock,amount_decimals);

            calculation_total();
            }
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
                    <td  colspan="${godown_motive==3 || godown_motive==4?2:3}" class="m-0 p-0" > <select name="commission_ledger_id[]" class="form-control select2 commission_select2" id="select_${ComRowCount}" >
                    <option value="0">--Select--</option>
                        ${ledger_commission_tree}
                    </select></td>
                    <td class="m-0 p-0">
                    <select name="commision_cal[]" class="form-control left-data commision_cal" id="commision_cal_${ComRowCount}" style="margin:0%;" >
                        <option  value="1">(-) %</option>
                        <option  value="2">(+) %</option>
                        <option  value="3">(-)</option>
                        <option  value="4">(+)</option>
                    </select>
                    </td>
                    <td class="m-0 p-0" >
                        <input type="number" step="any" name="commission_amount[]"class="form-control commission_amount" id="commission_amount_${ComRowCount}" />
                    </td>
                    <td class="col-1.5"><input type="number" step="any" name="get_commission[]"  style="border-radius: 15px;font-weight: bold;" id="get_commission_${ComRowCount}" class="get_commission form-control text-right" readonly></td>

                    <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
                </tr>`);
                $('#select_'+ComRowCount).val("{{$voucher->commission_ledger_id??0}}");
                $('#select_'+ComRowCount).select2({
                    allowClear: true,
                    width:'100%'

                });
            });
        });

    $('#commission_append').on('click', '.com_btn_remove', function() {
        var button_id =$(this).closest('tr').remove();
    });

    // auto searching
    function getId(element){
      var id, idArr;
      id = element.attr('id');
      idArr = id.split("_");
      return idArr[idArr.length - 1];
    }

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
                    if($(this).attr('name')==='product_name[]'){
                        $(this).closest('tr').find('.product_id').val('') ;
                        // $(this).closest('tr').find('.debit_ledger_id_commission').attr("required", "false");
                    }
                    else if($(this).attr('name')==='godown_name[]')$(this).closest('tr').find('.godown_id').val('');
                        $(this).focus();
                        check_item_null({{$voucher->total_qty_is ?? ''}},0);
                    }else{
                        // if($(this).attr('name')==='product_name[]')$(this).closest('tr').find('.debit_ledger_id_commission').attr("required", "false");
                       // if($(this).attr('name')==='product_name[]')$(this).closest('tr').find('.debit_ledger_id_commission').attr("required", "true");
                    }

            },
            select: function( event, selectedData ) {
                    if(checkDuplicateItem(selectedData?.item?.data?.stock_item_id)){
                        currentEle.val('');
                    }else if(selectedData && selectedData.item && selectedData.item.data){
                        var rowNo, data;
                        rowNo = getId(currentEle);
                        data = selectedData.item.data;
                        check_item_null({{$voucher->total_qty_is ?? ''}},1)
                        if(data.ledger_head_id){
                            $('#product_wise_commission_ledger_'+rowNo).val(data.ledger_head_id);
                        }
                        if(data.godown_id){
                            $(this).closest('tr').find('.godown_name').css({backgroundColor: 'white'})
                            $('#godown_name_'+rowNo).val(data.godown_name);
                            $('#godown_id_'+rowNo).val(data.godown_id);
                            adjustFontSize($('#godown_name_'+rowNo)[0],data.godown_name);
                            //current stock check
                            current_stock(rowNo,$(this).closest('tr').find('.product_id').val(),data.godown_id,'{{url("current-stock") }}',check_current_stock)
                        }
                        if(data.stock_item_id){
                            $(this).closest('tr').find('.product_name').css({backgroundColor: 'white'});
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
                                        selected_auto_value_change(check_current_stock,currentEle,0,amount_decimals);
                                        calculation_total();

                                    }
                                }
                            });
                        }
                        // Adjust font size for input
                        adjustFontSize(currentEle[0],data?.product_name);
                        stockCalculation();
                    }
                }
        }).bindAutocompleteTabKeyHandler();
    }
    function registerEvents() {
        $(document).on('focus','.autocomplete_txt', handleAutocomplete);
    }
    registerEvents();
});
</script>
<script>

 // insert sales
$(document).ready(function(){
    $("#add_sales_id").submit(function(e) {
        e.preventDefault();
        if (e.key === "Enter") {
            return ;
        }else{
            const fd = new FormData(this);
            $("#add_sales_btn").text('Add');
            $(":submit").attr("disabled", true);
            $.ajax({
                    url: '{{ route("voucher-sales.store") }}',
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
        }
    });
});
input_checking('godown');
input_checking('product');
</script>
@endpush
@endsection

