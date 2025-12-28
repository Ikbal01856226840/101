
@extends('layouts.backend.app')
@section('title','Voucher Stock Journal')
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
<form id="edit_stock_journal_id" method="POST">
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
                        <input type="hidden" name="delete_stock_in_id" class="form-control delete_stock_in_id" />
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
                        <select style="margin-top: 2px; border-radius: 15px;" name="unit_or_branch" class="form-control m-1 js-example-basic-single js-example-basic unit_or_branch_id" required>
                            @foreach ($branch_setup as $unit_branchs)
                            <option value="{{ $unit_branchs->id }}">{{$unit_branchs->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row" style="margin-top: 5px;">
                    <label style="margin-left: 2px;">Narration:</label>
                    <textarea name="narration" rows="2.7" cols="2.7" class="form-control m-1 narration">{{$data->narration}}</textarea>
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
        <div class="col-sm-4">
            <label>Distribution Center :</label>
            <select style="border-radius: 15px;" name="dis_cen_id" class="form-control m-1 js-example-basic-single dis_cen_id js-example-basic" required>
                <option value="">--Select--</option>
                @foreach ($distributionCenter as $distribution)
                <option value="{{$distribution->dis_cen_id }}">{{$distribution->dis_cen_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-4">
            @if($voucher->is_pary_ledger==1)
            <label for="exampleInputEmail1">Party's A/C Name:</label>
            <input type="text" name="credit_ledger_name" value="{{$ledger->ledger_name}}" id="credit_ledger_name" class="form-control credit_ledger_name" style="margin-bottom: 4px !important; margin-top: 2px;" required />
            <input type="hidden" name="ledger_id_optional" value="{{$ledger->ledger_head_id}}" id="credit_ledger_id" class="form-control credit_ledger_id" style="margin-bottom: 4px !important; border-radius: 15px; margin-top: 2px;" />
            @endif
        </div>
    </div>
    <fieldset style="border: 1px rgb(32, 30, 30) solid;">
        <legend style="width: 19%;">Source (Consumption)</legend>
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-3 {{$voucher->godown_motive==3?'d-none ':'' }}">
                <select name="godown_out" class="form-control js-example-basic-single godown_out left-data" required>
                    @if($voucher->godown_motive==3)
                     @foreach ($godowns as $godown)
                       <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                     @endforeach
                    @else @foreach ($godowns as $godown)
                    <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                    @endforeach @endif
                </select>
            </div>
            <div class="col-sm-3"></div>
            <div class="col-sm-3"></div>
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
                    <tbody id="orders_out"></tbody>
                    <tfoot>
                        <tr>
                            <td class="m-0 p-0"><button type="button" id="add_out" class="btn btn-success cicle m-0 py-1">+</button></td>
                            <td colspan="1" class="text-right">Total:</td>
                            <td></td>
                            @if($voucher->godown_motive==1)
                            <td></td>
                            @elseif($voucher->godown_motive==2)
                            <td></td>
                            @endif
                            <td><input type="text " style="border-radius: 15px; font-weight: bold;" class="total_qty_out form-control text-right" readonly /></td>
                            <td></td>
                            <td></td>
                            <td><input type="text " name="total_amont_out" style="border-radius: 15px; font-weight: bold;" class="total_amont_out form-control text-right" readonly /></td>
                            <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </fieldset>
    <fieldset style="border: 1px rgb(32, 30, 30) solid;">
        <legend style="width: 21%;">Destination (Production)</legend>
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-3 {{$voucher->destination_godown_motive==3?'d-none ':'' }}">
                <select name="godown_in" class="form-control js-example-basic-single godown_in left-data" required>
                    @if($voucher->destination_godown_motive==3)
                        @foreach ($destination_godowns as $godown)
                        <option {{$voucher->destination_godown_id==$godown->godown_id ? 'selected' : ''}} value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                        @endforeach
                    @else @foreach ($destination_godowns as $godown)
                    <option {{$voucher->destination_godown_id==$godown->godown_id ? 'selected' : ''}} value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                    @endforeach @endif
                </select>
            </div>
            <div class="col-sm-3"></div>
            <div class="col-sm-3"></div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table customers" style="border: none !important; margin-top: 5px;">
                    <thead>
                        <tr>
                            <th class="col-0.5">#</th>
                            @if($voucher->destination_godown_motive==3||$voucher->destination_godown_motive==4)
                             <th class="th {{$voucher->remark_is==1?'col-4':'col-6'}}">Product Name</th>
                            @else
                              <th class="th {{$voucher->remark_is==1?'col-3':'col-4'}}">Product Name</th>
                            @endif
                            <th class="th col-2 {{$voucher->destination_godown_motive==3?'d-none':'' }}{{$voucher->destination_godown_motive==4?'d-none':'' }}">Godown</th>
                            <th class="th col-1">stock</th>
                            <th class="th col-1">Quantity</th>
                            <th class="th col-1">Price</th>
                            <th class="th col-1">Per</th>
                            <th class="th col-2">Amount</th>
                            @if($voucher->destination_godown_motive==3)
                            <th class="th {{$voucher->destination_godown_motive==3?'col-2':'col-1'}}  {{$voucher->remark_is==0?'d-none':'' }}">Remarks</th>
                            @elseif($voucher->destination_godown_motive==4)
                            <th class="th {{$voucher->destination_godown_motive==4?'col-2':'col-1'}} {{$voucher->remark_is==0?'d-none':'' }}">Remarks</th>
                            @else
                            <th class="th col-1 {{$voucher->remark_is==0?'d-none':'' }}">Remarks</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="orders_in"></tbody>
                    <tfoot>
                        <tr>
                            <td class="m-0 p-0"><button type="button" id="add" class="btn btn-success cicle m-0 py-1">+</button></td>
                            <td colspan="1" class="text-right">Total:</td>
                            <td></td>
                            @if($voucher->destination_godown_motive==1)
                            <td></td>
                            @elseif($voucher->destination_godown_motive==2)
                            <td></td>
                            @endif
                            <td><input type="text " style="border-radius: 15px; font-weight: bold;" class="total_qty_in form-control text-right" readonly /></td>
                            <td></td>
                            <td></td>
                            <td><input type="text " name="total_amont_in" style="border-radius: 15px; font-weight: bold;" class="total_amont_in form-control text-right" readonly /></td>
                            <td class="col-1 {{$voucher->remark_is==0?'d-none':'' }}"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if($voucher->secret_narration_is)
                <div class="col-sm-12 mb-1">
                    <label style="margin-left: 2px;">Secret Narration:</label>
                    <textarea style="margin-left: 2px; border-radius: 15px;" name="secret_narration" rows="2.5" cols="2.5" class="form-control">{{$data->secret_narration}}</textarea>
                </div>
            @endif
        </div>
    </fieldset>
    <div align="center" class="m-1">
         @if (user_privileges_check('Voucher',$voucher->voucher_id,'alter_role'))
            @if (voucher_modify_authorization($data->tran_id))
                <button type="submit" class="btn btn-info edit_stock_journal_btn" style="width: 120px; border-radius: 15px;">
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
selected_val();
function selected_val(){
    $('.customer_id').val('{{$data->customer_id}}');
    $('.unit_or_branch_id').val('{{$data->unit_or_branch}}');
    $('.dis_cen_id').val('{{$data->dis_cen_id}}');
}
// credit ledger searching
$(document).ready(function () {
    // form reset
  resetForm("edit_stock_journal_id");
  selected_val();

  // ledger searching
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
        if(ui.item.inventory_value=='Yes'){
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



});
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
  let p_in,p_out;
  // source
        $.ajax({
            type: 'GET',
            url: "{{ url('edit/stock/in/with/current_stock')}}",
            async: false,
            data: {
                tran_id:t_m_id
            },
            dataType: 'json',
            success: function (response) {
             $.each(response.data, function(i,val) {
                $('#orders_in').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row_in${i}">
                  <input class="form-control  stock_in_id m-0 p-0"  name="stock_in_id[]" type="hidden" data-type="stock_in_id" value="${val.stock_in_id}" id="stock_in_id_${i}"  for="${i}"/>
                  <input class="form-control  product_in_id m-0 p-0"  name="product_in_id[]" type="hidden" data-type="product_in_id" value="${val.stock_item_id}" id="product_in_id_${i}"  for="${i}"/>

                  <td  class="m-0 p-0"><button  type="button" name="remove" id="${i}" class="btn btn-danger btn_remove_in cicle m-0  py-1">-</button></td>
                  <td  class="m-0 p-0">
                    <input class="form-control product_in_name  autocomplete_txt" name="product_in_name[]" data-field-name="product_name"  type="text" data-type="product_in_name"  value="${val.product_name}" id="product_in_name_${i}"  autocomplete="off" for="${i}"  />

                  </td>
                  <td  class="m-0 p-0 ${godown_motive==3?'d-none':''} ${godown_motive==4?'d-none':''}">
                      <input class="form-control godown_in_name autocomplete_txt " name="godown_in_name[]" value="${val.godown_name}"  data-field-name="godown_name" type="text"   data-type="godown_in_name" id="godown_in_name_${i}"  for="${i}" ${godown_motive==2?'readonly':''}  autocomplete="off" />
                      <input class="form-control godown_in_id text-right " name="godown_in_id[]"  data-field-name="godown_in_id" value="${val.godown_id}" type="hidden"  id="godown_in_id_${i}"  for="${i}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control stock_in text-right"   data-field-name="stock_in" type="number" class="stock" value="${((val.stock_in_sum)-(val.stock_out_sum)).toFixed(amount_decimals)}" id="stock_in_${i}"  for="${i}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                        <input
                            class="form-control display_qty_in text-right "
                            name="display_qty_in[]"
                            data-field-name="display_qty_in"
                            type="number"
                            step="any"
                            value="${val.qty?.toFixed(amount_decimals)}"
                            id="display_qty_in_${i}"
                            for="${i}"
                        />
                      <input
                            class="form-control qty_in text-right "
                            name="qty_in[]"
                            data-field-name="qty_in"
                            type="hidden"
                            value="${val.qty}"
                            id="qty_in_${i}"
                            for="${i}"  />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control rate_in text-right "  name="rate_in[]" data-field-name="rate_in" type="number" step="any" data-type="rate_in" value="${val.rate.toFixed(amount_decimals)}" id="rate_in_${i}"  for="${i}" ${stock_item_price_typeabe==0?'readonly':''} />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control per_in  "  name="per_in[]" data-field-name="per_in" type="text" data-type="per_in" value="${val.symbol}" id="per_in_${i}"  for="${i}" readonly />
                      <input class="form-control measure_in_id "  name="measure_in_id[]" data-field-name="measure_in_id" type="hidden" data-type="measure_in_id" id="measure_in_id_${i}"  for="${i}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input
                            class="form-control display_amount_in  text-right"
                            type="number" step="any"
                            name="display_amount_in[]"
                            value="${val.total.toFixed(amount_decimals)}"
                            id="display_amount_in_${i}"
                            ${amount_typeabe==1?'readonly':''}
                            for="${i}"
                        />
                      <input class="form-control amount_in  text-right" type="hidden" step="any"
                            name="amount_in[]" value="${val.total}" id="amount_in_${i}" ${amount_typeabe==1?'readonly':''}
                            for="${i}"
                        />
                  </td>
                  <td  class="m-0 p-0 ${remark_is==0?'d-none':''}">
                      <input class="form-control remark_in"  name='remark_in[]' type="text" data-type="" value="${val.remark?val.remark:''}" id="remark_in_${i}"  autocomplete="off" for="${i}"/>
                  </td>
               </tr>`);
                    p_in=i;
                });
            }
        });
        // destination
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
                $('#orders_out').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row_out${i}">
                <input class="form-control  stock_out_id m-0 p-0"  name="stock_out_id[]" type="hidden" data-type="stock_out_id" value="${val.stock_out_id}" id="stock_in_id_${i}"  for="${i}"/>
                <input class="form-control  product_out_id m-0 p-0"  name="product_out_id[]" type="hidden" data-type="product_out_id" value="${val.stock_item_id}" id="product_out_id_${i}"  for="${i}"/>
                <td  class="m-0 p-0"><button  type="button" name="btn_remove_out" id="${i}" class="btn btn-danger btn_remove_out cicle m-0  py-1">-</button></td>
                <td  class="m-0 p-0">
                <input class="form-control  autocomplete_txt" name="product_out_name[]" data-field-name="product_name"  type="text" data-type="product_out_name" value="${val.product_name}" id="product_out_name_${i}"  autocomplete="off" for="${i}"  />
                </td>
                <td  class="m-0 p-0 ${godown_motive==3?'d-none':''} ${godown_motive==4?'d-none':''}">
                    <input class="form-control godown_out_name autocomplete_txt " name="godown_out_name[]"  value="${val.godown_name}"  data-field-name="godown_name" type="text"  data-type="godown_out_name" id="godown_out_name_${i}"  for="${i}" ${godown_motive==2?'readonly':''}  autocomplete="off" />
                    <input class="form-control godown_out_id text-right " name="godown_out_id[]"  data-field-name="godown_out_id" value="${val.godown_id}" type="hidden"  id="godown_out_id_${i}"  for="${i}" readonly />
                </td>
                <td  class="m-0 p-0">
                    <input class="form-control stock_out text-right"   data-field-name="stock_out" type="number" step="any" value="${((val.stock_in_sum)-(val.stock_out_sum)).toFixed(amount_decimals)}" id="stock_out_${i}"  for="${i}" readonly />
                </td>
                <td  class="m-0 p-0">
                    <input
                        class="form-control display_qty_out text-right "
                        name="display_qty_out[]"
                        data-field-name="display_qty_out"
                        id="display_qty_out_${i}"
                        step="any"
                        type="number"
                        value="${val.qty?.toFixed(amount_decimals)}"
                        for="${i}"  />
                    <input class="form-control qty_out text-right " name="qty_out[]"  data-field-name="qty_out" step="any" type="hidden" class="qty_out" value="${val.qty}" id="qty_out_${i}"  for="${i}"  />
                </td>
                <td  class="m-0 p-0">
                    <input class="form-control rate_out text-right "  name="rate_out[]" data-field-name="rate_out" type="number" step="any" data-type="rate_out"  value="${val.rate?.toFixed(amount_decimals)}" id="rate_out_${i}"  for="${i}" ${stock_item_price_typeabe==0?'readonly':''} />
                </td>
                <td  class="m-0 p-0">
                    <input class="form-control per_out  "  name="per_out[]" data-field-name="per_out" type="text" data-type="per_out" value="${val.symbol}" id="per_out_${i}"  for="${i}" readonly />
                    <input class="form-control measure_out_id "  name="measure_out_id[]" data-field-name="measure_out_id" type="hidden" data-type="measure_out_id" id="measure_out_id_${i}"  for="${i}" readonly />
                </td>
                <td  class="m-0 p-0">
                    <input
                        class="form-control display_amount_out  text-right"
                        type="number"
                        step="any"
                        name="display_amount_out[]"
                        id="display_amount_out_${i}"
                        value="${val.total.toFixed(amount_decimals)}"
                        ${amount_typeabe==1?'readonly':''}
                        for="${i}"/>
                    <input class="form-control amount_out  text-right" type="hidden" step="any"  name="amount_out[]" value="${val.total}" id="amount_out_${i}" ${amount_typeabe==1?'readonly':''}  for="${i}"/>
                </td>
                <td  class="m-0 p-0 ${remark_is==0?'d-none':''}">
                    <input class="form-control remark_out"  name="remark_out[]" type="text" data-type="" value="${val.remark?val.remark:''}" id="remark_out_${i}"  autocomplete="off" for="${i}"/>
                </td>
            </tr>`);
                    p_out=i;
                });
            }
        });

  var rowCount_in=p_in,rowCount_out= p_out;
    //source
    $('#add').click(function() {
      rowCount_in+=5;
      addrow_in (rowCount_in);
    });
    //destination
    $('#add_out').click(function() {
      rowCount_out+=5;
      addrow_out (rowCount_out);
    });

    function getId(element){
      var id, idArr;
      id = element.attr('id');
      idArr = id.split("_");
      return idArr[idArr.length - 1];
    }
    var arr_in = [];
    var arr_out = [];
   //source  remove
    $(document).on('click', '.btn_remove_in', function() {
      var button_id = $(this).attr('id');
      arr_in.push($(this).closest('tr').find('.stock_in_id').val());
        $('.delete_stock_in_id').val(arr_in);
      $('#row_in'+button_id+'').remove();
    });

    //destination remove
    $(document).on('click', '.btn_remove_out', function() {
      var button_id = $(this).attr('id');
      arr_out.push($(this).closest('tr').find('.stock_out_id').val());
    $('.delete_stock_out_id').val(arr_out);
      $('#row_out'+button_id+'').remove();

    });

    // add row source
    function addrow_in (rowCount){
      if(rowCount==null){
          rowCount=1;
      }else{
          rowCount=rowCount;
      }
      let godown_id=$('.godown_in').val();
      let godown_name=$('.godown_in option:selected').text()
      for(var row=1; row<6;row++) {
          rowCount++;
              $('#orders_in').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row_in${rowCount}">
                  <input class="form-control  product_in_id m-0 p-0"  name="product_in_id[]" type="hidden" data-type="product_in_id" id="product_in_id_${rowCount}"  for="${rowCount}"/>
                  <td  class="m-0 p-0"><button  type="button" name="remove" id="${rowCount}" class="btn btn-danger btn_remove_in cicle m-0  py-1">-</button></td>
                  <td  class="m-0 p-0">
                    <input class="form-control product_in_name autocomplete_txt" name="product_in_name[]" data-field-name="product_name"  type="text" data-type="product_in_name" id="product_in_name_${rowCount}"  autocomplete="off" for="${rowCount}"  />
                  </td>
                  <td  class="m-0 p-0 ${godown_motive==3?'d-none':''} ${godown_motive==4?'d-none':''}">
                      <input class="form-control godown_in_name autocomplete_txt " name="godown_in_name[]" value="${godown_name}"  data-field-name="godown_name" type="text" data-type="godown_in_name" id="godown_in_name_${rowCount}"  for="${rowCount}" ${godown_motive==2?'readonly':''}  autocomplete="off" />
                      <input class="form-control godown_in_id text-right " name="godown_in_id[]"  data-field-name="godown_in_id" value="${godown_id}" type="hidden"  id="godown_in_id_${rowCount}"  for="${rowCount}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control stock_in text-right"   data-field-name="stock_in" type="number" class="stock" id="stock_in_${rowCount}"  for="${rowCount}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                      <input
                            class="form-control display_qty_in text-right "
                            name="display_qty_in[]"
                            data-field-name="display_qty_in"
                            type="number"
                            step="any"
                            id="display_qty_in_${rowCount}"
                            for="${rowCount}"
                        />
                        <input
                            class="form-control qty_in text-right "
                            name="qty_in[]"
                            data-field-name="qty_in"
                            type="hidden"
                            step="any"
                            id="qty_in_${rowCount}"
                            for="${rowCount}"
                        />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control rate_in text-right "  name="rate_in[]" data-field-name="rate_in" step="any" type="number" data-type="rate_in" id="rate_in_${rowCount}"  for="${rowCount}" ${stock_item_price_typeabe==0?'readonly':''} />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control per_in  "  name="per_in[]" data-field-name="per_in" type="text" data-type="per_in" id="per_in_${rowCount}"  for="${rowCount}" readonly />
                      <input class="form-control measure_in_id "  name="measure_in_id[]" data-field-name="measure_in_id" type="hidden" data-type="measure_in_id" id="measure_in_id_${rowCount}"  for="${rowCount}" readonly />
                  </td>
                  <td  class="m-0 p-0">
                        <input
                            class="form-control display_amount_in  text-right"
                            type="number"
                            name="display_amount_in[]"
                            id="display_amount_in_${rowCount}"
                            ${amount_typeabe==1?'readonly':''}
                            for="${rowCount}"
                        />
                        <input
                                class="form-control amount_in  text-right"
                                type="hidden"
                                name="amount_in[]"
                                id="amount_in_${rowCount}"
                                ${amount_typeabe==1?'readonly':''}
                                for="${rowCount}"
                        />
                  </td>
                  <td  class="m-0 p-0 ${remark_is==0?'d-none':''}">
                      <input class="form-control remark_in"  name='remark_in[]' type="text" data-type=" id="remark_in_${rowCount}"  autocomplete="off" for="${rowCount}"/>
                  </td>
            </tr>`);
      }
  }
  //add row destination
  function addrow_out (rowCount){

      if(rowCount==null){
          rowCount=1;
      }else{
          rowCount=rowCount;
      }
      let godown_id=$('.godown_out').val();
      let godown_name=$('.godown_out option:selected').text()
      for(var row=1; row<6;row++) {
          rowCount++;
          $('#orders_out').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row_out${rowCount}">
                <input class="form-control  product_out_id m-0 p-0"  name="product_out_id[]" type="hidden" data-type="product_out_id" id="product_out_id_${rowCount}"  for="${rowCount}"/>
                <td  class="m-0 p-0"><button  type="button" name="btn_remove_out" id="${rowCount}" class="btn btn-danger btn_remove_out cicle m-0  py-1">-</button></td>
                <td  class="m-0 p-0">
                <input class="form-control product_out_name autocomplete_txt" name="product_out_name[]" data-field-name="product_name"  type="text" data-type="product_out_name" id="product_out_name_${rowCount}"  autocomplete="off" for="${rowCount}"  />
                </td>
                <td  class="m-0 p-0 ${godown_motive==3?'d-none':''} ${godown_motive==4?'d-none':''}">
                    <input class="form-control godown_out_name autocomplete_txt " name="godown_out_name[]" value="${godown_name}"  data-field-name="godown_name" type="text"  data-type="godown_out_name" id="godown_out_name_${rowCount}"  for="${rowCount}" ${godown_motive==2?'readonly':''}  autocomplete="off" />
                    <input class="form-control godown_out_id text-right " name="godown_out_id[]"  data-field-name="godown_out_id" value="${godown_id}" type="hidden"  id="godown_out_id_${rowCount}"  for="${rowCount}" readonly />
                </td>
                <td  class="m-0 p-0">
                    <input class="form-control stock_out text-right"   data-field-name="stock_out" type="number" step="any"
                     id="stock_out_${rowCount}"  for="${rowCount}" readonly />
                </td>
                <td  class="m-0 p-0">
                    <input
                        class="form-control display_qty_out text-right "
                        name="display_qty_out[]"
                        data-field-name="display_qty_out"
                        type="number"
                        step="any"
                        id="display_qty_out_${rowCount}"
                        for="${rowCount}"
                    />
                    <input
                        class="form-control qty_out text-right "
                        name="qty_out[]"
                        data-field-name="qty_out"
                        type="hidden"
                        step="any"
                        id="qty_out_${rowCount}"
                        for="${rowCount}"
                    />
                </td>
                <td  class="m-0 p-0">
                    <input class="form-control rate_out text-right "  name="rate_out[]" data-field-name="rate_out"
                    type="number"
                    step="any"
                    data-type="rate_out" id="rate_out_${rowCount}"  for="${rowCount}" ${stock_item_price_typeabe==0?'readonly':''} />
                </td>
                <td  class="m-0 p-0">
                    <input class="form-control per_out  "  name="per_out[]" data-field-name="per_out" type="text" data-type="per_out" id="per_out_${rowCount}"  for="${rowCount}" readonly />
                    <input class="form-control measure_out_id "  name="measure_out_id[]" data-field-name="measure_out_id" type="hidden" data-type="measure_out_id" id="measure_out_id_${rowCount}"  for="${rowCount}" readonly />
                </td>
                <td  class="m-0 p-0">
                    <input
                        class="form-control display_amount_out  text-right"
                        type="number"
                        step="any"
                        name="display_amount_out[]"
                        id="display_amount_out_${rowCount}"
                        ${amount_typeabe==1?'readonly':''}
                        for="${rowCount}"/>
                    <input
                        class="form-control amount_out  text-right"
                        type="hidden"
                        step="any"
                        name="amount_out[]"
                        id="amount_out_${rowCount}"
                        ${amount_typeabe==1?'readonly':''}
                        for="${rowCount}"
                    />
                </td>
                <td  class="m-0 p-0 ${remark_is==0?'d-none':''}">
                    <input class="form-control remark_out"  name='remark_out[]' type="text" data-type=" id="remark_out_${rowCount}"  autocomplete="off" for="${rowCount}"/>
                </td>
            </tr>`);
      }
  }
  button_total_cal_in();
// source calculation
  function button_total_cal_in(){
      let qty_in=0;
      let amount_in=0;
      $('#orders_in tr').each(function(i){
          if(parseFloat($(this).find('.qty_in').val())) qty_in+=parseFloat($(this).find('.qty_in').val());
          if(parseFloat($(this).find('.amount_in').val())) amount_in+=parseFloat($(this).find('.amount_in').val());
      })
      $('.total_qty_in').val(parseFloat(qty_in).toFixed(amount_decimals));
      $('.total_amont_in').val(parseFloat(amount_in).toFixed(amount_decimals));
      // setting checking is total qty and price
      if(total_qty_is==0){
          if(qty_in==0){
            $(":submit").attr("disabled", true);
          }else{
            $(":submit").attr("disabled", false);
          }
      }
      if( total_price_is==0){
          if(amount_in==0){
            $(":submit").attr("disabled", true);
          }else{
            $(":submit").attr("disabled", false);
          }

      }

  }

  $('#orders_in').on('keyup','.display_qty_in',function(){
       $(this).closest('tr').find('.qty_in').val($(this).val());
  });

  $('#orders_in').on('keyup click change','.display_qty_in,.rate_in',function(){
       let qty;
    //    if(check_current_stock==0){

        // if(parseFloat($(this).closest('tr').find('.stock_in').val())>=($(this).closest('tr').find('.qty_in').val())){
            //  qty=$(this).closest('tr').find('.qty_in').val();
        // }else{
        //     $(this).closest('tr').find('.qty_in').val('');
        //     $(this).closest('tr').find('.display_qty_in').val('');
        //      qty=0;
        // }
    //    }else{
          qty=$(this).closest('tr').find('.qty_in').val();
    //    }
       let rate=$(this).closest('tr').find('.rate_in').val();
       $(this).closest('tr').find('.amount_in').val(parseFloat(qty*rate));
       $(this).closest('tr').find('.display_amount_in').val(parseFloat(qty*rate).toFixed(amount_decimals));
       button_total_cal_in();
  });
  $('#orders_in').on('keyup change','.display_amount_in',function(){
    // setting checking is amount_typeabe
        let amount=$(this).val();
        $(this).closest('tr').find('.amount_in').val(amount);
      if(amount_typeabe==0){
        button_total_cal_in();
        let qty_in=$(this).closest('tr').find('.qty_in').val();
        $(this).closest('tr').find('.rate_in').val(dividevalue(parseFloat(amount),parseFloat(qty_in)));
      }
  });
  button_total_cal_out();
  // destiontion calculation
  function button_total_cal_out(){
      let qty_out=0;
      let amount_out=0;
      $('#orders_out tr').each(function(i){
          if(parseFloat($(this).find('.qty_out').val())) qty_out+=parseFloat($(this).find('.qty_out').val());
          if(parseFloat($(this).find('.amount_out').val())) amount_out+=parseFloat($(this).find('.amount_out').val());
      })
      $('.total_qty_out').val(parseFloat(qty_out).toFixed(amount_decimals));
      $('.total_amont_out').val(parseFloat(amount_out).toFixed(amount_decimals));
      // setting checking is total qty and price
      if(total_qty_is==0){
          if(qty_out==0){
            $(":submit").attr("disabled", true);
          }else{
            $(":submit").attr("disabled", false);
          }
      }
      if( total_price_is==0){
          if(amount_out==0){
            $(":submit").attr("disabled", true);
          }else{
            $(":submit").attr("disabled", false);
          }

      }

  }
  $('#orders_out').on('keyup click change','.display_qty_out,.rate_out',function(){
       let qty;
       let qty_out=0;
       if($(this).attr('data-field-name')=='display_qty_out'){
            qty_out=$(this).closest('tr').find('.display_qty_out').val();
            $(this).closest('tr').find('.qty_out').val(qty_out)
       }else{
            qty_out=$(this).closest('tr').find('.qty_out').val();
       }

       if(check_current_stock==0){
        if(parseFloat($(this).closest('tr').find('.stock_out').val())>=(qty_out)){
             qty=qty_out;
        }else{
            $(this).closest('tr').find('.qty_out').val('');
            $(this).closest('tr').find('.display_qty_out').val('');
             qty=0;
        }
       }else{
          qty=qty_out;
       }
       let rate=$(this).closest('tr').find('.rate_out').val();
       $(this).closest('tr').find('.amount_out').val(parseFloat(qty*rate));
       $(this).closest('tr').find('.display_amount_out').val(parseFloat(qty*rate).toFixed(amount_decimals));
       button_total_cal_out();
  });
  $('#orders_out').on('keyup change','.display_amount_out',function(){
        // setting checking is amount_typeabe
        let amount=$(this).val();
        $(this).closest('tr').find('.amount_out').val(amount);
      if(amount_typeabe==0){
        button_total_cal_out();
        let qty_out=$(this).closest('tr').find('.qty_out').val();
        $(this).closest('tr').find('.rate_out').val(dividevalue(parseFloat(amount),parseFloat(qty_out)));
      }
  });
  $(document).on('click', '.btn_remove_in,.btn_remove_out', function() {
    button_total_cal_out();
    button_total_cal_in();
 });

  function getId(element){
      var id, idArr;
      id = element.attr('id');
      idArr = id.split("_");
      return idArr[idArr.length - 1];
    }

    var item_check =[];
    var item_check_out =[];
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
                          result=$.map(res, function(obj){

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
                    if($(this).attr('name')==='product_out_name[]')$(this).closest('tr').find('.product_out_id').val('');
                    else if($(this).attr('name')==='godown_out_name[]')$(this).closest('tr').find('.godown_out_id').val('');
                    if($(this).attr('name')==='product_in_name[]')$(this).closest('tr').find('.product_in_id').val('');
                    else if($(this).attr('name')==='godown_in_name[]')$(this).closest('tr').find('.godown_in_id').val('');
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
                  currentEle.css({backgroundColor: 'white'});

                  if(currentEle.data('type')=='product_in_name'||currentEle.data('type')=='godown_in_name'){
                            if(data.godown_id){
                                $('#godown_in_name_'+rowNo).val(data.godown_name);
                                $('#godown_in_id_'+rowNo).val(data.godown_id);
                            }
                            if(data.stock_item_id){
                                $('#product_in_id_'+rowNo).val(data.stock_item_id);
                                $('#per_in_'+rowNo).val(data.symbol);
                                $('#measure_in_id_'+rowNo).val(data.unit_of_measure_id);
                                //current stock check
                                $.ajax({
                                        url: '{{url("current-stock") }}',
                                        method: 'GET',
                                        dataType: 'json',
                                        async: false,
                                        data: {
                                            stock_item_id:data.stock_item_id,
                                            godown_id:$(this).closest('tr').find('.godown_in_id').val(),
                                        },
                                        success: function(response){
                                            if(response){
                                                $('#stock_in_'+rowNo).val((response.data));
                                            }else{
                                                $('#stock_in_'+rowNo).val('');
                                            }

                                        }
                                    });
                                // stock item get price
                                $.ajax({
                                    url: '{{route("destination_searching-stock-item-price") }}',
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
                                                $('#rate_in_'+rowNo).val(response.rate);
                                                selected_auto_value_change_in_out(check_current_stock,'stock_in','qty_in','amount_in',currentEle,response.rate,amount_decimals);
                                                button_total_cal_in();

                                            }else{
                                                $('#rate_in_'+rowNo).val(0);
                                                selected_auto_value_change_in_out(check_current_stock,'stock_in','qty_in','amount_in',currentEle,0,amount_decimals);
                                                button_total_cal_in();
                                            }
                                        }else{
                                            $('#rate_in_'+rowNo).val(0);
                                            selected_auto_value_change_in_out(check_current_stock,'stock_in','qty_in','amount_in',currentEle,0,amount_decimals);
                                            button_total_cal_in();
                                        }
                                    }
                                });
                            }
                       }
                      if(currentEle.data('type')=='product_out_name'||currentEle.data('type')=='godown_out_name'){
                        if(data.godown_id){
                            $('#godown_out_name_'+rowNo).val(data.godown_name);
                            $('#godown_out_id_'+rowNo).val(data.godown_id);
                        }
                        if(data.stock_item_id){
                            $('#product_out_id_'+rowNo).val(data.stock_item_id);
                            $('#per_out_'+rowNo).val(data.symbol);
                            $('#measure_out_id_'+rowNo).val(data.unit_of_measure_id);
                            //current stock check
                            $.ajax({
                                    url: '{{url("current-stock") }}',
                                    method: 'GET',
                                    dataType: 'json',
                                    async: false,
                                    data: {
                                        stock_item_id:data.stock_item_id,
                                        godown_id:$(this).closest('tr').find('.godown_out_id').val(),
                                    },
                                    success: function(response){
                                        if(response){
                                            $('#stock_out_'+rowNo).val((response.data));
                                        }else{
                                            $('#stock_out_'+rowNo).val('');
                                        }

                                    }
                            });
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
                                            $('#rate_out_'+rowNo).val(response.rate);
                                            selected_auto_value_change_in_out(check_current_stock,'stock_out','qty_out','amount_out',currentEle,response.rate,amount_decimals);
                                            button_total_cal_out();
                                        }else{
                                            $('#rate_out_'+rowNo).val(0);
                                            selected_auto_value_change_in_out(check_current_stock,'stock_out','qty_out','amount_out',currentEle,0,amount_decimals);
                                            button_total_cal_out();
                                        }
                                    }else{
                                        $('#rate_out_'+rowNo).val(0);
                                        selected_auto_value_change_in_out(check_current_stock,'stock_out','qty_out','amount_out',currentEle,0,amount_decimals);
                                        button_total_cal_out();
                                    }
                                }
                        });
                    }
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
$("#edit_stock_journal_id").submit(function(e) {
  e.preventDefault();
  const fd = new FormData(this);
  $("#edit_stock_journal_btn").text('Upadte');
  var id="{{$data->tran_id}}";
  $.ajax({
          url: "{{ url('voucher-stock-journal') }}" + '/' + id,
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
                $('#error_voucher_no').text(data.responseJSON.data.invoice_no[0]);

              }
          }
  });
});
// delete stock journal ajax request
$(document).on('click', '.deleteIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id ="{{$data->tran_id}}";
        swal(swal_data()).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('voucher-stock-journal') }}" + '/' + id ,
                    type : "POST",
                    data : {'_method' : 'DELETE', '_token' : csrf_token},
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
            timer: '1500'
        });
   }
});
$('.godown_in').on('change',function(){
    let godown=$('.godown_in option:selected').text();
    let godown_val=$('.godown_in').val();
    $('#orders_in tr').find('.godown_in_name').each(function(){
    $('.godown_in_name').val(godown);
    $('.godown_in_id').val(godown_val);
    })
})
$('.godown_out').on('change',function(){
    let godown=$('.godown_out option:selected').text();
    let godown_val=$('.godown_out').val();

    $('#orders_out tr').find('.godown_out_name').each(function(){
    $('.godown_out_name').val(godown);
    $('.godown_out_id').val(godown_val);
    })
});

// Cancelled stock journal ajax request
$(document).on('click', '.CancelIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id ="{{$data->tran_id}}";
        var narration =$('.narration').val();
        swal(swal_data()).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('voucher-stock-journal-cancel') }}" + '/' + id ,
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

input_checking('godown_out');
input_checking('product_out');
input_checking('godown_in');
input_checking('product_in');

</script>
@endpush
@endsection

