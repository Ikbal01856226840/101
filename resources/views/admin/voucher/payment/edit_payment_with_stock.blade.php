
@extends('layouts.backend.app')
@section('title',' Voucher Journal')
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
<form id="edit_received_id" method="POST">
    @csrf {{ method_field("PUT") }}
    <div class="page-body">
        <div class="row  margin">
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-3 dis" style="float: right;">
                        <label style="float: right; margin: 2px;">Invoice No:</label><br />
                        <br />
                        <label style="float: right; margin: 2px; margin-right: 29px;">Ref No:</label>
                    </div>
                    <div class="col-sm-9 m-0 p-0" style="margin-left: 5px;!important;">
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
                        <input type="hidden" name="delete_debit_credit_id" class="form-control delete_debit_credit_id" />
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
                        <input type="text" name="invoice_date" class="form-control setup_date" style="margin-bottom: 4px !important; border-radius: 15px;" value="{{$data->transaction_date}}" />
                         <!-- resposive lebel change -->
                         <label class="display" style="display:none">Unit / Branch:</label>
                        <select style="margin-top: 2px; border-radius: 15px;" name="unit_or_branch" class="form-control m-1 js-example-basic-single js-example-basic unit_or_branch" required>
                            @foreach ($branch_setup as $unit_branchs)
                            <option value="{{ $unit_branchs->id }}">{{$unit_branchs->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <label style="margin-left: 2px;">Narration:</label>
                <textarea style="margin-left: 2px; border-radius: 15px;" name="narration narration" rows="2.5" cols="2.5" class="form-control">{{$data->narration}}</textarea>
            </div>
        </div>
    </div>
    <div class="col-sm-4 {{$voucher->godown_motive==3?'d-none ':'' }}">
        <div class="form-group">
            <label for="exampleInputEmail1">Godowns :</label>
            <select name="godown" class="form-control js-example-basic-single godown left-data" required>
                @if($voucher->godown_motive==3)
                <option value="0"></option>
                @else @foreach ($godowns as $godown)
                <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                @endforeach @endif
            </select>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table customers" style="border: none !important; margin-top: 5px;">
                <thead>
                    <tr>
                        <th class="col-0.5" style="width: 46px;">#</th>
                        <th class="col-0.5">Dr/Cr</th>
                        <th class="th col-1.5">Ledger Name</th>
                        <th class="th col-1.5">Blance</th>
                        @if($voucher->godown_motive==3||$voucher->godown_motive==4)
                         <th class="th {{$voucher->remark_is==1?'col-2':'col-2'}}">Product Name</th>
                        @else
                         <th class="th {{$voucher->remark_is==1?'col-1':'col-1'}}">Product Name</th>
                        @endif
                        <th class="th col-1 {{$voucher->godown_motive==3?'d-none':'' }}{{$voucher->godown_motive==4?'d-none':'' }}">Godown</th>
                        <th class="th col-1">Stock</th>
                        <th class="th col-1">Quantity</th>
                        <th class="th col-1">Price</th>
                        <th class="th col-1">Amount</th>
                        <th class="th col-1">Dedit</th>
                        <th class="th col-1">Credit</th>
                        @if( $voucher->remark_is==1)
                        <th class="th col-1">Remarks</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="orders"></tbody>
                <tfoot>
                    <tr>
                        <td class="m-0 p-0"><button type="button" name="add" id="add" class="btn btn-success cicle m-0 py-1">+</button></td>
                        <td colspan="2" class="text-right">Total:</td>
                        <td></td>
                        <td></td>
                        @if($voucher->godown_motive==3||$voucher->godown_motive==4)
                        @else
                        <td></td>
                        @endif
                        <td></td>
                        <td><input type="text" style="font-weight: bold;" class="total_qty form-control text-right" /></td>
                        <td></td>
                        <td><input type="text" style="font-weight: bold;" class="total_amount form-control text-right" readonly /></td>
                        <td><input type="text" style="font-weight: bold;" class="total_dedit form-control text-right" readonly /></td>
                        <td><input type="text" style="font-weight: bold;" class="total_credit form-control text-right" readonly /></td>
                        <td></td>
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
    <div align="center">
        <button type="submit" class="btn btn-info edit_journal" style="width: 120px; border-radius: 15px;">
            <span class="m-1 m-t-1" style="color: #404040;"><i class="fa fa-save" style="font-size: 18px;"></i></span><span>Update</span>
        </button>
        <button type="button" class="btn btn-danger deleteIcon" style="width: 120px; border-radius: 15px;">
            <span class="m-1 m-t-1" style="color: #404040;"><i class="fa fa-save" style="font-size: 18px;"></i></span><span>Delete</span>
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
    selected_val();
    function selected_val(){
      $('.unit_or_branch').val('{{$data->unit_or_branch}}');
    }
    $(document).ready(function(){

        // form reset
        resetForm("edit_received_id");
        selected_val();

        let amount_decimals="{{company()->amount_decimals}}";
        let debit_credit_amont= "{{$voucher->amnt_typeable ?? ''}}";
        let remark_is="{{$voucher->remark_is ?? ''}}";
        let dup_row="{{$voucher->dup_row ?? ''}}";
        let dc_amnt="{{$voucher->dc_amnt ?? ''}}";
        let stock_item_price_typeabe="{{$voucher->stock_item_price_typeabe ?? ''}}";
        let total_qty_is="{{$voucher->total_qty_is ?? ''}}";
        let total_price_is="{{$voucher->total_price_is ?? ''}}";
        let amount_typeabe="{{$voucher->amount_typeabe ?? ''}}";
        let godown_motive="{{$voucher->godown_motive ?? ''}}";
        let t_m_id="{{$data->tran_id}}";
        let p;
        $.ajax({
            type: 'GET',
            url: "{{ url('voucher-journal-edit')}}",
            async: false,
            data: {
                tran_id:t_m_id
            },
            dataType: 'json',
            success: function (response) {
             let debit_credit_check_in=0,debit_credit_check=0;
             $.each(response.data, function(i,val) {
                $('#orders').append(`<tr style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${i}">
                                        <input class="form-control  stock_in_id m-0 p-0" type="hidden" name="stock_in_id[]" value="${val.in_stock_in_id?val.in_stock_in_id:''}" data-type="stock_in_id" id="stock_in_id_${i}" >
                                        <input class="form-control  stock_out_id m-0 p-0" type="hidden" name="stock_out_id[]" value="${val.out_stock_in_id?val.out_stock_in_id:''}" data-type="stock_out_id" id="stock_out_id_${i}">
                                    <td  class="m-0 p-0"><button  type="button" name="remove" id="${i}" class="btn btn-danger btn_remove cicle m-0  py-1">-</button></td>
                                    <input class="form-control  ledger_in_id m-0 p-0" type="hidden" name="ledger_in_id[]" data-type="ledger_in_id" id="ledger_in_id_${i}">
                                            <input class="form-control  ledger_out_id m-0 p-0" type="hidden" name="ledger_out_id[]" data-type="ledger_out_id" id="ledger_out_id_${i}">
                                    ${(val.debit_credit_id!=debit_credit_check_in) ? `
                                        <td  class="m-0 p-0">
                                            <input class="form-control  debit_credit_id m-0 p-0"  name="debit_credit_id[]" type="hidden" value="${val.debit_credit_id}" data-type="debit_credit_id" id="debit_credit_id_${i}"  for="${i}"/>
                                            <input class="form-control  ledger_id m-0 p-0" type="hidden" name="ledger_id[]" value="${val.ledger_head_id?val.ledger_head_id:''}" data-type="ledger_id" id="ledger_id_${i}"  for="${i}" />
                                            <select  id="DrCr" name="DrCr[]" class="form-control  js-example-basic-single  DrCr " >
                                                <option   ${val.dr_cr=="Dr"?'selected':''} value="Dr">Dr</option>
                                                <option  ${val.dr_cr=="Cr" ?'selected':''} value="Cr">Cr</option>
                                            </select>
                                        </td>
                                        <td  class="m-0 p-0">
                                            <textarea data-adaptheight  style="resize: none;" class="form-control ledger_name  autocomplete_txt" name="ledger_name[]" data-field-name="ledger_name"  type="text" data-type="ledger_name" id="ledger_name_${i}"  autocomplete="off" for="${i}" rows="1" >${val.ledger_name?val.ledger_name:''}</textarea>

                                        </td>
                                        <td  class="m-0 p-0">
                                            <input class="form-control blance text-right "   data-field-name="blance"  name="blance[]" type="text" class="blance" id="blance_${i}"  for="${i}"  readonly/>
                                        </td>
                                        ${debit_credit_check_in=val.debit_credit_id}`:`<td  class="m-0 p-0">
                                            <input class="form-control  debit_credit_id m-0 p-0"  name="debit_credit_id[]" type="hidden"  data-type="debit_credit_id" id="debit_credit_id_${i}"  for="${i}"/>
                                            <input class="form-control  ledger_id m-0 p-0" type="hidden" name="ledger_id[]" value="" data-type="ledger_id" id="ledger_id_${i}"  for="${i}" />
                                            <select  id="DrCr" name="DrCr[]" class="form-control  js-example-basic-single  DrCr " >
                                                <option  value="Dr">Dr</option>
                                                <option  value="Cr">Cr</option>
                                            </select>
                                        </td>
                                        <td  class="m-0 p-0">
                                            <textarea style="resize: none;"  class="form-control ledger_name  autocomplete_txt" name="ledger_name[]" data-field-name="ledger_name"  type="text" data-type="ledger_name" id="ledger_name_${i}"  autocomplete="off" for="${i}" rows="1" ></textarea>

                                        </td>
                                        <td  class="m-0 p-0">
                                            <input class="form-control blance text-right "   data-field-name="blance"  name="blance[]" type="text" class="blance" id="blance_${i}"  for="${i}"  readonly/>
                                        </td>`
                                    }
                                    <td  class="m-0 p-0">
                                        <textarea  style="resize: none;" class="form-control product_name  autocomplete_txt" name="product_name[]" data-field-name="product_name"  type="text" data-type="product_name" id="product_name_${i}"  autocomplete="off" for="${i}" rows="1">${val.in_product_name?val.in_product_name:(val.out_product_name?val.out_product_name:'')}</textarea>
                                        <input class="form-control product_id"   data-field-name="product_id"  name="product_id[]" type="hidden" class="blance" id="product_id_${i}"  for="${i}" value="${val.in_item_id?val.in_item_id:(val.out_item_id?val.out_item_id:'')}"  readonly/>
                                    </td>
                                    <td  class="m-0 p-0 ${godown_motive==3?'d-none':''} ${godown_motive==4?'d-none':''}">
                                        <textarea class="form-control godown_name autocomplete_txt " name="godown_name[]"   data-field-name="godown_name" type="text"  id="godown_name_${i}"  for="${i}" ${godown_motive==2?'readonly':''}  autocomplete="off"  rows="1">${val.in_godown_name?val.in_godown_name:(val.out_godowns_name?val.out_godowns_name:'')}</textarea>
                                        <input class="form-control godown_id text-right " name="godown_id[]"  data-field-name="godown_id" value="${val.in_godown_id?val.in_godown_id:(val.out_godown_id?val.out_godown_id:'')}" type="hidden"  id="godown_id_${i}"  for="${i}" readonly />
                                    </td>
                                    <td  class="m-0 p-0">
                                        <input class="form-control stock_out text-right"   data-field-name="stock_out" type="number"  step="any"  id="stock_${i}"  for="${i}" readonly />
                                    </td>
                                    <td  class="m-0 p-0">
                                        <input class="form-control qty text-right " name="qty[]"  data-field-name="qty" type="number"  step="any"  value="${val.in_qty?val.in_qty:val.out_qty}" step="any" class="qty" id="qty_${i}"  for="${i}"  />

                                    </td>
                                    <td  class="m-0 p-0">
                                        <input class="form-control rate text-right "   name="rate[]" data-field-name="rate" type="number" 
                                        value="${val.in_rate?val.in_rate:val.out_rate}"  step="any" data-type="rate" id="rate_${i}"  
                                        for="${i}" ${stock_item_price_typeabe==0?'readonly':''} />
                                    </td>

                                    <td  class="m-0 p-0">
                                        <input class="form-control amount  text-right" type="number" step="any"  name="amount[]" value="${val.in_total?val.in_total:val.out_total}"  amount="${val.in_total?val.in_total:val.out_total}" step="any" id="amount_${i}" ${amount_typeabe==1?'readonly':''}   for="${i}"/>
                                    </td>

                                    ${(val.debit_credit_id!=debit_credit_check) ?`<td  class="m-0 p-0">
                                        <input class="form-control debit text-right " data-field-name="debit" name="debit[]" type="number" 
                                        data-type="debit" id="debit_${i}" step="any" 
                                        value="${val.debit.toFixed(amount_decimals)}" ${val.dr_cr=="Dr"?'':'readonly'}  for="${i}"/>
                                    </td>
                                    <td  class="m-0 p-0">
                                        <input class="form-control credit text-right" type="number" name="credit[]" id="credit_${i}"  step="any"  
                                        value="${val.credit.toFixed(amount_decimals)}" ${val.dr_cr=="Cr"?'':'readonly'} for="${i}" />
                                    </td>${debit_credit_check=val.debit_credit_id}`:`<td  class="m-0 p-0">
                                        <input class="form-control debit text-right " data-field-name="debit" name="debit[]" type="number" step="any" data-type="debit" id="debit_${i}" value=""  for="${i}"/>
                                    </td>
                                    <td  class="m-0 p-0">
                                        <input class="form-control credit text-right" type="number" name="credit[]" id="credit_${i}"  step="any"  value="" for="${i}" />
                                    </td>`}
                                ${remark_is==1 && `<td  class="m-0 p-0 "><input class="form-control remark"  name="remark[]" type="text" data-type=" id="remark_${i}"  autocomplete="off" for="${i}"/></td>`}
                            </tr>`);
                            let ledger_head_id =$('#ledger_id_'+i).val();
                                    $.ajax({
                                    url: '{{url("journal-data") }}',
                                    method: 'GET',
                                    dataType: 'json',
                                    async: false,
                                    data: {
                                        ledger_head_id:ledger_head_id,
                                        stock_item_id:val.in_item_id?val.in_item_id:(val.out_item_id?val.out_item_id:''),
                                        godown_id:val.in_godown_id?val.in_godown_id:(val.out_godown_id?val.out_godown_id:''),
                                    },
                                    success: function(response){

                                         $('#blance_'+i).val(response[0].balance==0?'':response[0].balance);
                                         $('#stock_'+i).val(response[0].stock==0?'':response[0].stock);
                                    }

                                });
                    p=i;
                });
            }
        });

       var rowCount=p;
        //row append function
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

           //row remove
          let  arr_out=[];
          let  arr_in=[];
          let  debit_credit=[];
          $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr('id');
                arr_out.push($(this).closest('tr').find('.stock_out_id').val());
                arr_in.push($(this).closest('tr').find('.stock_in_id').val());
                debit_credit.push($(this).closest('tr').find('.debit_credit_id').val());
                $('.delete_stock_out_id').val(arr_out);
                $('.delete_stock_in_id').val(arr_in);
                $('.delete_debit_credit_id').val(debit_credit);
                $('#row'+button_id+'').remove();
            });

           //row append table
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
                            let remark=``;
                            $('#orders').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${rowCount}">
                                  <input class="form-control  ledger_id m-0 p-0" type="hidden" name="ledger_id[]" data-type="ledger_id" id="ledger_id_${rowCount}"  for="${rowCount}" />
                                  <input class="form-control  ledger_in_id m-0 p-0" type="hidden" name="ledger_in_id[]" data-type="ledger_in_id" id="ledger_in_id_${rowCount}">
                                  <input class="form-control  ledger_out_id m-0 p-0" type="hidden" name="ledger_out_id[]" data-type="ledger_out_id" id="ledger_out_id_${rowCount}">
                                <td  class="m-0 p-0"><button  type="button" name="remove" id="${rowCount}" class="btn btn-danger btn_remove cicle m-0  py-1">-</button></td>
                                <td  class="m-0 p-0">
                                    <select  id="DrCr" name="DrCr[]" class="form-control  js-example-basic-single  DrCr " >
                                        <option value="Dr">Dr</option>
                                        <option value="Cr">Cr</option>
                                    </select>
                                </td>
                                <td  class="m-0 p-0">
                                    <textarea   class="form-control ledger_name  autocomplete_txt" name="ledger_name[]" data-field-name="ledger_name"  type="text" data-type="ledger_name" id="ledger_name_${rowCount}" " autocomplete="off" for="${rowCount}" rows="1" ></textarea>
                                </td>
                                <td  class="m-0 p-0">
                                    <input class="form-control blance text-right "   data-field-name="blance"  name="blance[]" type="text" class="blance" id="blance_${rowCount}"  for="${rowCount}"  readonly/>
                                </td>
                                <td  class="m-0 p-0">
                                    <textarea class="form-control product_name  autocomplete_txt" name="product_name[]" data-field-name="product_name"  type="text" data-type="product_name" id="product_name_${rowCount}"  autocomplete="off" for="${rowCount}" rows="1"></textarea>
                                    <input class="form-control product_id"   data-field-name="product_id"  name="product_id[]" type="hidden" class="blance" id="product_id_${rowCount}"  for="${rowCount}"  readonly/>
                                </td>
                                <td  class="m-0 p-0 ${godown_motive==3?'d-none':''} ${godown_motive==4?'d-none':''}">
                                    <textarea class="form-control godown_name autocomplete_txt " name="godown_name[]"   data-field-name="godown_name" type="text"  id="godown_name_${rowCount}"  for="${rowCount}" ${godown_motive==2?'readonly':''}  autocomplete="off"  rows="1">${godown_name}</textarea>
                                    <input class="form-control godown_id text-right " name="godown_id[]"  data-field-name="godown_id" value="${godown_id}" type="hidden"  id="godown_id_${rowCount}"  for="${rowCount}" readonly />
                                </td>
                                <td  class="m-0 p-0">
                                    <input class="form-control stock_out text-right"   data-field-name="stock_out" type="number"  step="any"  id="stock_${rowCount}"  for="${rowCount}" readonly />
                                </td>
                                <td  class="m-0 p-0">
                                    <input class="form-control qty text-right " name="qty[]" step="any" data-field-name="qty" type="number" class="qty" id="qty_${rowCount}"  for="${rowCount}"  />
                                </td>
                                <td  class="m-0 p-0">
                                    <input class="form-control rate text-right "   name="rate[]" data-field-name="rate" type="number"  step="any" data-type="rate" id="rate_${rowCount}"  for="${rowCount}" ${stock_item_price_typeabe==0?'readonly':''} />
                                </td>

                                <td  class="m-0 p-0">
                                    <input class="form-control amount  text-right" type="number" step="any"  name="amount[]" id="amount_${rowCount}" ${amount_typeabe==1?'readonly':''}  for="${rowCount}"/>
                                </td>
                                <td  class="m-0 p-0">
                                    <input class="form-control debit text-right " data-field-name="debit" name="debit[]" type="number" step="any" data-type="debit" id="debit_${rowCount}"  for="${rowCount}"/>
                                </td>
                                <td  class="m-0 p-0">
                                    <input class="form-control credit text-right" type="number" step="any"  name="credit[]" id="credit_${rowCount}"   for="${rowCount}" readonly/>
                                </td>
                                ${remark_is==1 && `<td  class="m-0 p-0 "><input class="form-control remark"  name="remark[]" type="text" data-type=" id="remark_${rowCount}"  autocomplete="off" for="${rowCount}"/></td>`}
                            </tr>`);
                        }
                        $('#orders').find('input,textarea,button,select').height($("textarea")[0].scrollHeight-10)

            }

        // select debit credit row
        $('#orders').on('change','.DrCr',function(){
            var DrCr=$(this).closest('tr').find('.DrCr').val();
            var credit=$(this).closest('tr').find('.credit').val();
            var debit= $(this).closest('tr').find('.debit').val();
            if( DrCr=='Cr'){
                if(debit){
                    $(this).closest('tr').find('.debit').val('');
                    let check_blance=$(this).closest('tr').find('.blance').val().search("Cr");
                    if(check_blance==-1){
                            if(debit_credit_amont==0){
                                if(parseFloat($(this).closest('tr').find('.blance').val())<parseFloat(debit)){
                                    $(this).closest('tr').find('.credit').val('');
                                    $(this).closest('tr').find('.credit').css({backgroundColor: 'red'});
                                }else{
                                    $(this).closest('tr').find('.credit').css({backgroundColor: 'white'});
                                    $(this).closest('tr').find('.credit').val(debit);
                                }
                          }else{
                            $(this).closest('tr').find('.credit').val(debit);
                            $(this).closest('tr').closest('tr').find('.debit').css({backgroundColor: ''});
                          }
                    }else{
                        $(this).closest('tr').find('.credit').val(debit);
                        $(this).closest('tr').find('.debit').css({backgroundColor: ''});
                    }
                    $(this).closest('tr').find('.credit').attr('readonly', false);
                    $(this).closest('tr').find('.debit').attr('readonly', true);
                }
                $(this).closest('tr').find('.credit').attr('readonly', false);
                $(this).closest('tr').find('.debit').attr('readonly', true);
            }else if(DrCr=='Dr'){
                if(credit){
                    $(this).closest('tr').find('.credit').val('');
                    let check_blance=$(this).closest('tr').find('.blance').val().search("Dr");
                    if(check_blance==-1){
                            if(debit_credit_amont==0){
                                if(parseFloat($(this).closest('tr').find('.blance').val())<parseFloat(credit)){
                                    $(this).closest('tr').find('.debit').val('');
                                    $(this).closest('tr').find('.debit').css({backgroundColor: 'red'});
                                }else{

                                    // $(this).closest('tr').find('.debit').css({backgroundColor: 'white'});
                                    $(this).closest('tr').find('.debit').val(credit);
                                }
                          }else{
                             $(this).closest('tr').find('.debit').val(credit);
                             $(this).closest('tr').find('.credit').css({backgroundColor: ''});
                          }
                    }else{
                        $(this).closest('tr').find('.debit').val(credit);
                        $(this).closest('tr').find('.credit').css({backgroundColor: ''});
                    }
                    $(this).closest('tr').find('.debit').attr('readonly', false);
                    $(this).closest('tr').find('.credit').attr('readonly', true);
                }
                $(this).closest('tr').find('.debit').attr('readonly', false);
                $(this).closest('tr').find('.credit').attr('readonly', true);

            }
        })

        $('#orders').on('click  keyup','.credit,.debit',function(event){
            if (event.type === 'click' || (event.type === 'keyup' && event.key === 'Tab')) {
                if($(this).val()<=0){
                    if( $(this).closest('tr').find('.amount').val()<=0){
                        if($(this).attr('class').search('credit')>=0 && $(this).closest('tr').find('.DrCr').val()=='Cr'){
                            let check_blance=$(this).closest('tr').find('.blance').val().search("Cr");
                            if(check_blance==-1){

                                if(debit_credit_amont==0){
                                    if(parseFloat($(this).closest('tr').find('.blance').val())<parseFloat(DrCrCalculation('credit'))){
                                        $(this).closest('tr').find('.credit').val('');
                                        $(this).closest('tr').find('.credit').css({backgroundColor: 'red'});
                                    }else{
                                        $(this).val(parseFloat(DrCrCalculation('credit')).toFixed(amount_decimals));
                                        $(this).closest('tr').find('.credit').css({backgroundColor: 'white'});
                                    }
                                }else{

                                    $(this).val(parseFloat(DrCrCalculation('credit')).toFixed(amount_decimals));
                                }
                            }else{

                                $(this).val(parseFloat(DrCrCalculation('credit')).toFixed(amount_decimals));
                            }
                        }else if($(this).attr('class').search('debit')>=0 && $(this).closest('tr').find('.DrCr').val()=='Dr'){
                            let check_blance=$(this).closest('tr').find('.blance').val().search("Dr");
                            if(check_blance==-1){
                                if(debit_credit_amont==0){
                                    if(parseFloat($(this).closest('tr').find('.blance').val())<parseFloat(DrCrCalculation('debit'))){
                                        $(this).closest('tr').find('.debit').val('');
                                        $(this).closest('tr').find('.debit').css({backgroundColor: 'red'});
                                    }else{
                                        $(this).val(parseFloat(DrCrCalculation('debit')).toFixed(amount_decimals));
                                        $(this).closest('tr').find('.debit').css({backgroundColor: 'white'});
                                    }
                                }else{
                                    $(this).val(parseFloat(DrCrCalculation('debit')).toFixed(amount_decimals));
                                }
                            }else{
                                    $(this).val(parseFloat(DrCrCalculation('debit')).toFixed(amount_decimals));
                            }

                        }
                    }
                }
                calculation_total();
            }
        })

        $('#orders').on('change','.credit, .debit ',function(){

            $(this).val(parseFloat($(this).val()).toFixed(amount_decimals));
        });

        $('#orders').on('keyup','.amount',function(){
            // setting checking is amount_typeabe
            if(amount_typeabe==0){
                calculation_total();
                $(this).closest('tr').find('.rate').val(parseFloat($(this).closest('tr').find('.amount').val()||0)/parseFloat($(this).closest('tr').find('.qty').val()||1));
            }
        });

        // debit credit calculation
        function calculation_total(){
            let debit=0;
            let credit=0;
            let qty=0;
            let amount=0;
            $('#orders tr').each(function(i){
                if(parseFloat($(this).find('.debit').val())) debit+=parseFloat($(this).find('.debit').val()).toFixed(amount_decimals);
                if(parseFloat($(this).find('.credit').val())) credit+=parseFloat($(this).find('.credit').val()).toFixed(amount_decimals);
                if(parseFloat($(this).find('.qty').val())) qty+=parseFloat($(this).find('.qty').val());
                if(parseFloat($(this).find('.amount').val())) amount+=parseFloat($(this).find('.amount').val());
            })
            $('.total_qty').val(parseFloat(qty).toFixed(amount_decimals));
            $('.total_amount').val(parseFloat(amount).toFixed(amount_decimals));
            $('.total_dedit').val(parseFloat(debit).toFixed(amount_decimals));
            $('.total_credit').val(parseFloat(credit).toFixed(amount_decimals));
            if(debit!=credit){
                $(":submit").attr("disabled", true);
            }else{
                $(":submit").attr("disabled", false);
            }
            if(dc_amnt==0){
                if(debit==0||debit==''){
                  $(":submit").attr("disabled", true);
                }else{
                    if(debit==credit){
                     $(":submit").attr("disabled", false);
                    }
                }
                if(credit==0||credit==''){
                $(":submit").attr("disabled", true);
                }else{
                    if(debit==credit){
                      $(":submit").attr("disabled", false);
                    }
                }
            }

        }

        $('#orders').on('change keyup', '.credit, .debit,.qty,.rate,.amount', function() {

            if($(this).hasClass('credit') || $(this).hasClass('debit')){
                $(this).closest('tr').find('.amount').removeAttr('amount')
            }
            let qty =parseFloat($(this).closest('tr').find('.qty').val()||0);
            let rate =parseFloat($(this).closest('tr').find('.rate').val() || 0);
            let amount=parseFloat($(this).closest('tr').find('.amount').val() || 0);
            let DrCr = parseFloat($(this).closest('tr').find('.DrCr').val());
            let product_id = parseFloat($(this).closest('tr').find('.product_id').val());

            if(!$(this).closest('tr').find('.amount').attr('amount') && (qty * rate)){
                $(this).closest('tr').find('.amount').attr('amount',parseFloat(qty * rate))
            }
            if($(this).hasClass('amount')){
                $(this).closest('tr').find('.rate').val(parseFloat(amount/(qty>0?qty:1)).toFixed(amount_decimals));

            }else{
                $(this).closest('tr').find('.amount').val(parseFloat(qty * rate).toFixed(amount_decimals));

            }

            if (parseFloat($(this).closest('tr').find('.product_id').val())) {
                calculation_total();
                let product_wise_debit_sum = 0
                let prevTr = '';
                $('#orders tr').each(function(i) {
                    if ($(this).find('.ledger_name').val() && $(this).closest('tr').find('.product_id').val()&& !!$(this).closest('tr').find('.amount').attr('amount')) {

                            if (prevTr) {
                                let DrCr = prevTr.find('.DrCr').val()
                                if (DrCr === 'Dr') prevTr.find('.debit').val(parseFloat(product_wise_debit_sum));
                                else if (DrCr === 'Cr') prevTr.find('.credit').val(parseFloat(product_wise_debit_sum));
                                product_wise_debit_sum = 0;
                            }
                            let qty =parseFloat($(this).closest('tr').find('.qty').val()||0);
                            let rate =parseFloat($(this).closest('tr').find('.rate').val() || 0);
                            product_wise_debit_sum = qty * rate;
                            let DrCr = $(this).find('.DrCr').val()

                            if (DrCr === 'Dr') $(this).find('.debit').val(parseFloat(product_wise_debit_sum));
                            else if (DrCr === 'Cr') $(this).find('.credit').val(parseFloat(product_wise_debit_sum));
                           prevTr = $(this);
                    }else if(!$(this).closest('tr').find('.product_id').val() && $(this).find('.ledger_name').val()){
                            if (prevTr) {
                                if(product_wise_debit_sum!=0){
                                    let DrCr = prevTr.find('.DrCr').val()
                                    if (DrCr === 'Dr') prevTr.find('.debit').val(parseFloat(product_wise_debit_sum));
                                    else if (DrCr === 'Cr') prevTr.find('.credit').val(parseFloat(product_wise_debit_sum));
                                    product_wise_debit_sum = 0;
                                }
                            }
                            let qty =parseFloat($(this).closest('tr').find('.qty').val()||0);
                            let rate =parseFloat($(this).closest('tr').find('.rate').val() || 0);
                            product_wise_debit_sum = qty * rate;

                            let DrCr = $(this).find('.DrCr').val()
                            if(product_wise_debit_sum!=0){
                                if (DrCr === 'Dr') $(this).find('.debit').val(parseFloat(product_wise_debit_sum));
                                else if (DrCr === 'Cr') $(this).find('.credit').val(parseFloat(product_wise_debit_sum));
                            }
                        this_product_id_row=$(this);
                        prevTr = $(this);
                    }else if($(this).closest('tr').find('.product_id').val() && $(this).find('.ledger_name').val() && !$(this).closest('tr').find('.amount').attr('amount')){

                        prevTr =null;
                    }
                    else if($(this).find('.ledger_name').val()){

                        prevTr = $(this);
                    }
                    else {

                        if(!prevTr?.find('.product_id').val() && prevTr?.find('.ledger_name').val()){

                        }else{
                            product_wise_debit_sum += parseFloat($(this).find('.qty').val() * ($(this).find('.rate').val()));
                        }
                    }
                });
                if (prevTr) {
                    if(product_wise_debit_sum!=0){
                        let DrCr = prevTr.find('.DrCr').val()
                        if (DrCr === 'Dr') prevTr.find('.debit').val(parseFloat(product_wise_debit_sum));
                        else if (DrCr === 'Cr') prevTr.find('.credit').val(parseFloat(product_wise_debit_sum));
                    }
                    product_wise_debit_sum = 0;
                }

            }
            calculation_total();
        });

        $('#orders').on('keyup change', '.product_name,.DrCr,.ledger_name', function() {
            input_stock_in_and_stock_out();
        });

        $('#orders').on('keyup ','.credit',function(){
            let check_blance=$(this).closest('tr').find('.blance').val().search("Cr");
            if(check_blance==-1){
                if(debit_credit_amont==0){
                    if(parseFloat($(this).closest('tr').find('.blance').val())<parseFloat($(this).closest('tr').find('.credit').val())){
                        $(this).closest('tr').find('.credit').val('');
                        $(this).closest('tr').find('.credit').css({backgroundColor: 'red'});
                    }else{
                        $(this).closest('tr').find('.credit').css({backgroundColor: 'white'});
                    }
               }
            }
         });

         $('#orders').on('keyup','.debit',function(){
            let check_blance=$(this).closest('tr').find('.blance').val().search("Dr");
            if(check_blance==-1){
                if(debit_credit_amont==0){
                    if(parseFloat($(this).closest('tr').find('.blance').val())<parseFloat($(this).closest('tr').find('.debit').val())){
                        $(this).closest('tr').find('.debit').val('');
                        $(this).closest('tr').find('.debit').css({backgroundColor: 'red'});
                    }else{
                        $(this).closest('tr').find('.debit').css({backgroundColor: 'white'});
                    }
               }
            }
         });

         calculation_total();
        $('#orders').on('change','tr','.DrCr,.credit, .debit ',function(){
            calculation_total();
        });

        $(document).on('click', '.btn_remove', function() {
           calculation_total();
         });

         // calculation debit credit
        function DrCrCalculation(type){
            let debit=0;
            let credit=0;
            let qty=0;
            let amount=0;
            $('#orders tr').each(function(i){
                if(parseFloat($(this).find('.debit').val())) debit+=parseFloat($(this).find('.debit').val());
                if(parseFloat($(this).find('.credit').val())) credit+=parseFloat($(this).find('.credit').val());
                if(parseFloat($(this).find('.qty').val())) qty+=parseFloat($(this).find('.qty').val());
                if(parseFloat($(this).find('.amount').val())) amount+=parseFloat($(this).find('.amount').val());
            })
            $('.total_qty').val(parseFloat(qty).toFixed(amount_decimals));
            $('.total_amount').val(parseFloat(amount).toFixed(amount_decimals));
            if(debit>credit && type == 'credit'){
                $('.total_credit').val(parseFloat(debit).toFixed(amount_decimals));
                return parseFloat(debit)-parseFloat(credit);
            }
            else if(debit<credit && type == 'debit'){
                $('.total_dedit').val(parseFloat(credit).toFixed(amount_decimals));
                return parseFloat(credit)-parseFloat(debit);
            }
        }

        // auto searching
        function getId(element){
            var id, idArr;
            id = element.attr('id');
            idArr = id.split("_");
            return idArr[idArr.length - 1];
          }

        var ledger_check =[];
        function handleAutocomplete() {
            var fieldName, currentEle,DrCr;
            currentEle = $(this);

            fieldName = currentEle.data('field-name');
            DrCr=$(this).closest('tr').find('.DrCr').val()

            if(typeof fieldName === 'undefined') {
                return false;
            }
            currentEle.autocomplete({
                delay: 500,
                source: function( data, cb ) {
                    $.ajax({
                        url: '{{route("searching-ledger-data") }}',
                        method: 'GET',
                        dataType: 'json',
                        timeout: 1000,
                        data: {
                            name:  data.term,
                            fieldName: fieldName,
                            voucher_id:"{{$voucher->voucher_id}}",
                            DrCr:DrCr
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
                change: function(event, ui) {
                    if (ui.item == null) {
                        if ($(this).attr('name') === 'product_name[]') $(this).closest('tr').find('.product_id').val('');
                        else if ($(this).attr('name') === 'godown_name[]') $(this).closest('tr').find('.godown_id').val('');
                        else if ($(this).attr('name') === 'ledger_name[]') $(this).closest('tr').find('.ledger_id').val('');
                        $(this).focus();

                    }
                },
                select: function( event, selectedData ) {
                    if(selectedData && selectedData.item && selectedData.item.data){
                        var rowNo, data;
                        rowNo = getId(currentEle);
                        data = selectedData.item.data;
                        input_disabled.call(rowNo,data.inventory_value);
                        if(data.ledger_head_id){
                            $('#ledger_id_'+rowNo).val(data.ledger_head_id);
                                $.ajax({
                                    url: '{{route("balance-debit-credit") }}',
                                    method: 'GET',
                                    dataType: 'json',
                                    async: false,
                                    data: {
                                        ledger_head_id:data.ledger_head_id
                                    },
                                    success: function(response){

                                        $('#blance_'+rowNo).val(response.data);
                                    }
                                });
                        }else if(data.godown_id){
                            $('#godown_name_'+rowNo).val(data.godown_name);
                            $('#godown_id_'+rowNo).val(data.godown_id);
                             //current stock check
                            current_stock(rowNo,$(this).closest('tr').find('.product_id').val(),data.godown_id,'{{url("current-stock") }}')
                        }
                        else{
                            $('#product_id_'+rowNo).val(data.stock_item_id);
                            $('#per_'+rowNo).val(data.symbol);
                            $('#measure_id_'+rowNo).val(data.unit_of_measure_id);
                            //current stock check
                            current_stock(rowNo,data.stock_item_id,$(this).closest('tr').find('.godown_id').val(),'{{url("current-stock") }}')
                                // stock item get price
                                $.ajax({
                                    url: '{{route("searching-stock-item-price") }}',
                                    method: 'GET',
                                    dataType: 'json',
                                    async: false,
                                    data: {
                                        stock_item_id:data.stock_item_id,
                                        voucher_id:"{{$voucher->voucher_id}}",
                                    },
                                    success: function(response){
                                        if(response){
                                            if(response.rate){
                                                $('#rate_'+rowNo).val(response.rate);
                                                selected_auto_value_change(1,currentEle,response.rate,amount_decimals);
                                                calculation_total();
                                            }else{
                                                $('#rate_'+rowNo).val(0);
                                                selected_auto_value_change(1,currentEle,0,amount_decimals);
                                                calculation_total();
                                            }

                                        }else{
                                            $('#rate_'+rowNo).val(0);
                                            selected_auto_value_change(1,currentEle,0,amount_decimals);
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
// insert journal
$(document).ready(function(){
   $("#edit_received_id").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id="{{$data->tran_id}}";
        $("#edit_received_btn").text('Add');
        let itemIdNotValid=false;
        $('#orders tr').each(function(i) {
            if(!$(this).closest('tr').find('.product_id').val()&&($(this).closest('tr').find('.amount').val()||0)>0){
                itemIdNotValid=true;
                $(this).closest('tr').find('.product_name').css({ backgroundColor: 'red' });

            }else if($(this).closest('tr').find('.product_id').val()&&!$(this).closest('tr').find('.godown_id').val()){
                itemIdNotValid=true;
                $(this).closest('tr').find('.godown_name').css({ backgroundColor: 'red' });
            }
        })

        if(itemIdNotValid){
                Swal.fire({
                    icon: "warning",
                    title: "Oops...",
                    text: "Stock item not found!",
                });

            }else{
                $.ajax({
                    url:  "{{ url('voucher-journal') }}" + '/' + id,
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
                            $('#error_voucher_no').text(data.responseJSON.data.invoice_no[0]);
                        }
                    }
               });
            }

    });
// delete purchase ajax request
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
                    url: "{{ url('voucher-journal') }}" + '/' + id ,
                    type : "POST",
                    data : {'_method' : 'DELETE', '_token' : csrf_token},
                    success: function (data) {
                        window.location = "{{url('daybook-report')}}";
                        swal_message(data.message,'success','Successfully');
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
// select option  change godown
$('.godown').on('change',function(){
    let godown=checkGodownValidity($('.godown option:selected').text());
    let godown_val=$('.godown').val();
    $('#orders tr').find('.godown_name').each(function(){
    $('.godown_name').val(godown);
    $('.godown_id').val(godown_val);
    })
});
function input_disabled(type) {
        if(type=="No"){
            $('#product_name_'+$(this)[0]).attr("readonly", "readonly");
            $('#godown_name_'+$(this)[0]).attr("readonly", "readonly");
            $('#qty_'+$(this)[0]).attr("readonly", "readonly");
            $('#rate_'+$(this)[0]).attr("readonly", "readonly");
            $('#amount_'+$(this)[0]).attr("readonly", "readonly");
        }else{
            $('#product_name_'+$(this)[0]).removeAttr("readonly", "readonly");
            $('#godown_name_'+$(this)[0]).removeAttr("readonly", "readonly");
            $('#qty_'+$(this)[0]).removeAttr("readonly", "readonly");
            $('#rate_'+$(this)[0]).removeAttr("readonly", "readonly");
            $('#amount_'+$(this)[0]).removeAttr("readonly", "readonly");
        }
    }
    function input_stock_in_and_stock_out() {
        let nature_group=null;
        let ledger_id =null;
        let DrCr = null;
        $('#orders tr').each(function(i) {
            if($(this).closest('tr').find('.ledger_name').val()?.trim()&&parseFloat($(this).closest('tr').find('.product_id').val())){
                nature_group=parseFloat($(this).closest('tr').find('.nature_group').val());
                ledger_id =parseFloat($(this).closest('tr').find('.ledger_id').val());
                DrCr = $(this).closest('tr').find('.DrCr').val();
            }
            if($(this).closest('tr').find('.product_id').val()&&ledger_id){
                ledger_debit=1;
                if (DrCr == 'Dr') {
                    if(nature_group==1||nature_group==3){
                        $(this).closest('tr').find('.ledger_out_id').val('');
                        $(this).closest('tr').find('.ledger_in_id').val(ledger_id);
                    }else if(nature_group==2||nature_group==4){
                        $(this).closest('tr').find('.ledger_in_id').val('');
                        console.log($(this).closest('tr').find('.ledger_id').val());
                        $(this).closest('tr').find('.ledger_in_id').val(ledger_id);
                    }
                } else if (DrCr == 'Cr') {
                    if(nature_group==2||nature_group==4){
                        $(this).closest('tr').find('.ledger_in_id').val('');
                        $(this).closest('tr').find('.ledger_out_id').val(ledger_id);
                    }else if(nature_group==1||nature_group==3){
                        $(this).closest('tr').find('.ledger_in_id').val('');
                        $(this).closest('tr').find('.ledger_out_id').val(ledger_id);
                    }
                }
            }else if($(this).closest('tr').find('.product_id').val()){
                ledger_debit=2;
            }
        })
    }
</script>
@endpush
@endsection

