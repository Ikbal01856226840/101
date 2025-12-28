
@extends('layouts.backend.app')
@section('title',' Voucher Payment')
@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/jquery-ui.theme.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/jquery-ui.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('voucher_setup/voucher_setup.css')}}">
@endpush
@section('admin_content')
@component('components.voucher', [
    'title' => "$voucher->voucher_name",
    'background_color'=>'#ccffff!important',
    'opration'=>'Update',
]);
<!-- Page-header component -->
@slot('voucher_body')
<form id="edit_received_id" method="POST">
    @csrf {{ method_field("PUT") }}
    <div class="page-body">
        <div class="row margin">
            <div class="col-sm-4 ">
                <div class="row">
                    <div class="col-sm-3 dis" style="float: right;">
                        <label style="float: right; margin: 2px;">Invoice No:</label><br />
                        <br />
                        <label style="float: right; margin: 2px; margin-right: 29px;">Ref No:</label>
                    </div>
                    <div class="col-sm-9 m-0 p-0" style="margin-left: 5px;!important;">
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Invoice No:</label>
                        <input type="text" name="invoice_no" class="form-control m-1" value="{{$data->invoice_no}}" style="border-radius: 15px;" style="color: green;" />
                        <span id="error_voucher_no" class="text-danger"></span>
                        <input type="hidden" class="form-control amount_decimals" value="{{company()->amount_decimals}}" />
                        <input type="hidden" class="form-control tran_id" value="{{$data->tran_id}}" />
                        <input type="hidden" name="delete_debit_credit_id" class="form-control delete_debit_credit_id" />
                        <input type="hidden" name="ch_4_dup_vou_no" class="form-control" value="{{$voucher->ch_4_dup_vou_no ?? ''}}" />
                        <input type="hidden" name="invoice" class="form-control" value="{{$voucher->invoice ?? ''}}" />
                        <input type="hidden" name="voucher_id" class="form-control voucher_id" value="{{$voucher->voucher_id ?? ''}}" />
                        <!-- resposive lebel change -->
                        <label class="display" style="display:none">Ref No:</label>
                        <input type="text" name="ref_no" class="form-control m-1" value="{{$data->ref_no}}" style="border-radius: 15px;" />
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
                        <select style="margin-top: 2px; border-radius: 15px;" name="unit_or_branch" class="form-control m-1 js-example-basic-single js-example-basic godown" required>
                            @foreach ($branch_setup as $unit_branchs)
                            <option {{ $unit_branchs->id==$data->unit_or_branch?'selected':''}} value="{{ $unit_branchs->id }}">{{$unit_branchs->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <label style="margin-left: 2px;">Narration:</label>
                <textarea style="margin-left: 2px; border-radius: 15px;" name="narration" rows="2.5" cols="2.5" class="form-control narration">{{$data->narration}}</textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table customers" style="border: none !important; margin-top: 5px;">
                <thead>
                    <tr>
                        <th class="col-0.5" style="width: 46px;">#</th>
                        <th class="col-0.5">Dr/Cr</th>
                        <th class="th col-5">Ledger Name</th>
                        <th class="th col-1.5">Balance</th>
                        <th class="th col-1.5">Debit</th>
                        <th class="th col-1.5">Credit</th>
                        @if( $voucher->remark_is==1)
                        <th class="th col-1">Remarks</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="orders"></tbody>
                <tfoot>
                    <tr>
                        <td class="m-0 p-0"><button type="button" name="add" id="add" class="btn btn-success cicle m-0 py-1">+</button></td>
                        <td></td>
                        <td colspan="2" class="text-right">Total:</td>
                        <td><input type="text" style="border-radius: 15px; font-weight: bold;" class="total_dedit form-control text-right" readonly /></td>
                        <td><input type="text" style="border-radius: 15px; font-weight: bold;" class="total_credit form-control text-right" readonly /></td>
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
        @if (user_privileges_check('Voucher',$voucher->voucher_id,'alter_role'))
            @if (voucher_modify_authorization($data->tran_id))
            <button type="submit" class="btn btn-info add_payment_btn" style="width: 120px; border-radius: 15px;">
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
  <script type="text/javascript" src="{{asset('voucher_setup/voucher_setup_receive.js')}}"></script>
  <script>
    $(document).ready(function(){
        var t_m_id=$('.tran_id').val();
        var amount_decimals=$('.amount_decimals').val();
        // var debit_credit_amont= "{{$voucher->amnt_typeable ?? ''}}";
        var debit_credit_amont = 1;
        var remark_is="{{$voucher->remark_is ?? ''}}";
        var dc_amnt="{{$voucher->dc_amnt ?? ''}}";
        let p;
        $.ajax({
            type: 'GET',
            url: "{{ url('edit/debit-credit') }}",
            async: false,
            data: {
                tran_id:t_m_id
            },
            dataType: 'json',
            success: function (response) {
                    $.each(response.data, function(i, val) {
                        let debit_credit_blance,debit_credit_sign;
                        if(val.nature_group == 1 || val.nature_group == 3){
                            debit_credit_blance= ((val.debit_sum_1-val.credit_sum_1) + (val.opening_balance)) ;
                            debit_credit_sign = debit_credit_blance >= 0 ? ' Dr' : ' Cr';
                        }else{
                            debit_credit_blance= ((val.credit_sum_2-val.debit_sum_2) + (val.opening_balance)) ;
                            debit_credit_sign = debit_credit_blance >= 0 ? ' Cr' : ' Dr';
                        }

                        $('#orders').append(`<tr   style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${i}">
                            <input class="form-control  ledger_id m-0 p-0"  name="ledger_id[]" type="hidden" value="${val.ledger_head_id}" data-type="ledger_id" id="ledger_id_${i}"  for="${i}"/>
                            <input class="form-control  debit_credit_id m-0 p-0"  name="debit_credit_id[]" type="hidden" value="${val.debit_credit_id}" data-type="debit_credit_id" id="debit_credit_id_${i}"  for="${i}"/>
                            <td  class="m-0 p-0"><button  type="button" name="remove" id="${i}" class="btn btn-danger btn_remove cicle" style="padding: 0px 19px;margin:1px 0px;">-</button></td>
                            <td  class="m-0 p-0">
                                <select  name="DrCr[]" id="DrCr" data-field-name="DrCr"  class="form-control  js-example-basic-single  DrCr "  for="${i}">
                                    <option  ${val.dr_cr=="Dr"?'selected':''} value="Dr">Dr</option>
                                    <option  ${val.dr_cr=="Cr" ?'selected':''} value="Cr">Cr</option>
                                </select>
                            </td>
                            <td  class="m-0 p-0">
                                <input class="form-control ledger_name  autocomplete_txt" name="ledger_name[]" data-field-name="ledger_name"  type="text" data-type="ledger_name" value="${val.ledger_name}" id="ledger_name_${i}" " autocomplete="off" for="${i}"  />
                            </td>
                            <td  class="m-0 p-0">
                                <input class="form-control  blance  text-right " name="blance[]"  data-field-name="blance" type="text" class="blance" id="blance_${i}"
                                value="${(Math.abs(debit_credit_blance)).toFixed(amount_decimals) +debit_credit_sign}" for="${i}" readonly/>
                            </td>
                            <td  class="m-0 p-0">
                                <input
                                    class="form-control display_debit text-right"
                                    name="display_debit[]"
                                    data-field-name="display_debit"
                                    type="text"
                                    data-type="display_debit"
                                    value="${ MakeCurrency(val.debit,true,amount_decimals)}"
                                    ${val.dr_cr=="Dr"?'':'readonly'}
                                    id="display_debit_${i}"
                                    for="${i}"
                                />
                                <input
                                    class="form-control debit text-right"
                                    name="debit[]"
                                    data-field-name="debit"
                                    type="hidden"
                                    data-type="debit"
                                    value="${ parseFloat(val.debit)}"
                                    ${val.dr_cr=="Dr"?'':'readonly'}
                                    id="debit_${i}"
                                    for="${i}"
                                />
                            </td>
                            <td  class="m-0 p-0">
                                <input
                                    class="form-control display_credit text-right"
                                    type="text"
                                    name="display_credit[]"
                                    id="display_credit_${i}"
                                    value="${MakeCurrency(val.credit,true,amount_decimals)}"
                                    for="${i}"
                                    ${val.dr_cr=="Cr" ?'':'readonly'}
                                />
                                <input
                                    class="form-control credit text-right"
                                    type="hidden"
                                    name="credit[]"
                                    id="credit_${i}"
                                    value="${val.credit}"
                                    for="${i}"
                                    ${val.dr_cr=="Cr" ?'':'readonly'}
                                />
                            </td>
                            <td  class="m-0 p-0  ${remark_is==0?'d-none':''}">
                                <input class="form-control remark " name="remark[]"  type="text"  id="remark_${i}"  value="${val.remark?val.remark:''}" autocomplete="off" for="${i}"/>
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

          $("#orders").on("input", ".display_credit", function () {
            $(this)
                .closest("td")
                .find(".credit")
                .val(MakeCurrency($(this).val(), false));
            button_debit_or_credit_total();
        });
        $("#orders").on("input", ".display_debit", function () {
            $(this)
                .closest("td")
                .find(".debit")
                .val(MakeCurrency($(this).val(), false));
            button_debit_or_credit_total();
        });
          var arr = [];
          $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr('id');
              $('#row'+button_id+'').remove();
              arr.push($(this).closest('tr').find('.debit_credit_id').val());
              $('.delete_debit_credit_id').val(arr);
            });
        $('#orders').on('keyup','.display_credit, .display_debit',function(event){
            let keypress = event.which;
            let inputVal = $(this).val();

            // Allow only numbers, backspace, and a single decimal point
            if (
                (keypress >= 48 && keypress <= 57) || // Numbers (0-9)
                (keypress >= 96 && keypress <= 105) ||
                keypress === 46 ||                  // Decimal point (.)
                keypress === 8                      // Backspace
            ) {
                if($(this).attr('class').search('display_credit')>=0){
                    $(this).closest('tr').find('.credit').val(MakeCurrency($(this).val(),false));
                }else if($(this).attr('class').search('display_debit')>=0){
                    $(this).closest('tr').find('.debit').val(MakeCurrency($(this).val(),false));
                }
            }
            button_debit_or_credit_total();
        })
        function addrow (rowCount)
        {
            if(rowCount==null){
                rowCount=1;
            }else{
                rowCount=rowCount;
            }
            for(var row=1; row<6;row++) {
                rowCount++;
                    $('#orders').append(`<tr   style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${rowCount}">
                        <input class="form-control  ledger_id m-0 p-0"  name="ledger_id[]" type="hidden" data-type="ledger_id" id="ledger_id_${rowCount}"  for="${rowCount}"/>
                        <td  class="m-0 p-0"><button  type="button" name="remove" id="${rowCount}" class="btn btn-danger btn_remove cicle" style="padding: 0px 19px;margin:1px 0px;">-</button></td>
                        <td  class="m-0 p-0">
                            <select  name="DrCr[]" id="DrCr" data-field-name="DrCr"  class="form-control  js-example-basic-single  DrCr " >
                                <option value="Dr">Dr</option>
                                <option value="Cr">Cr</option>
                            </select>
                        </td>
                        <td  class="m-0 p-0">
                            <input class="form-control ledger_name  autocomplete_txt" name="ledger_name[]" data-field-name="ledger_name"  type="text" data-type="ledger_name" id="ledger_name_${rowCount}" " autocomplete="off" for="${rowCount}"  />
                        </td>
                        <td  class="m-0 p-0">
                            <input class="form-control blance text-right " name="blance[]"  data-field-name="blance" type="text" class="blance" id="blance_${rowCount}"  for="${rowCount}"/>
                        </td>
                        <td  class="m-0 p-0">
                            <input
                                class="form-control display_debit  text-right "
                                name="display_debit[]"
                                data-field-name="display_debit"
                                type="text"
                                data-type="display_debit"
                                id="display_debit_${rowCount}"
                                for="${rowCount}"
                            />
                            <input
                                class="form-control debit  text-right "
                                name="debit[]"
                                data-field-name="debit"
                                type="hidden"
                                data-type="debit"
                                id="debit_${rowCount}"
                                for="${rowCount}"
                            />
                        </td>
                        <td  class="m-0 p-0">
                            <input
                                class="form-control display_credit  text-right"
                                type="text"
                                name="display_credit[]"
                                id="display_credit_${rowCount}"
                                for="${rowCount}"
                                readonly
                            />
                            <input
                                class="form-control credit  text-right"
                                type="hidden"
                                name="credit[]"
                                id="credit_${rowCount}"
                                for="${rowCount}"
                                readonly
                            />
                        </td>
                        ${remark_is==1 && `<td  class="m-0 p-0 "><input class="form-control remark"  name="remark[]" type="text" data-type=" id="remark_${rowCount}"  autocomplete="off" for="${rowCount}"/></td>`}
                        </tr>`);
                }
        }
        button_debit_or_credit_total();
        $('#orders').on('change','.DrCr',function(){
            var DrCr=$(this).closest('tr').find('.DrCr').val();
            var credit=$(this).closest('tr').find('.credit').val();
            var debit= $(this).closest('tr').find('.debit').val();
            if( DrCr=='Cr'){
                if(debit){
                    $(this).closest('tr').find('.debit').val('');
                    $(this).closest('tr').find('.display_debit').val('');
                    let check_blance=$(this).closest('tr').find('.blance').val().search("Cr");
                    if(check_blance==-1){
                            if(debit_credit_amont==0){
                                if(parseFloat($(this).closest('tr').find('.blance').val())<parseFloat(debit)){
                                    $(this).closest('tr').find('.credit').val('');
                                    $(this).closest('tr').find('.display_credit').val('');
                                    $(this).closest('tr').find('.display_credit').css({backgroundColor: 'red'});
                                }else{
                                    $(this).closest('tr').find('.display_credit').css({backgroundColor: 'white'});
                                    $(this).closest('tr').find('.credit').val(debit);
                                    $(this).closest('tr').find('.display_credit').val(debit);
                                }
                          }else{
                            $(this).closest('tr').find('.credit').val(debit);
                            $(this).closest('tr').find('.display_credit').val(debit);
                            $(this).closest('tr').closest('tr').find('.display_debit').css({backgroundColor: ''});
                          }
                    }else{
                        $(this).closest('tr').find('.credit').val(debit);
                        $(this).closest('tr').find('.display_credit').val(debit);
                        $(this).closest('tr').find('.display_debit').css({backgroundColor: ''});
                    }
                    $(this).closest('tr').find('.display_credit').attr('readonly', false);
                    $(this).closest('tr').find('.display_debit').attr('readonly', true);
                }
                $(this).closest('tr').find('.display_credit').attr('readonly', false);
                $(this).closest('tr').find('.display_debit').attr('readonly', true);
            }else if(DrCr=='Dr'){
                if(credit){
                    $(this).closest('tr').find('.credit').val('');
                    $(this).closest('tr').find('.display_credit').val('');
                    let check_blance=$(this).closest('tr').find('.blance').val().search("Dr");
                    if(check_blance==-1){
                            if(debit_credit_amont==0){
                                if(parseFloat($(this).closest('tr').find('.blance').val())<parseFloat(credit)){
                                    $(this).closest('tr').find('.debit').val('');
                                    $(this).closest('tr').find('.display_debit').val('');
                                    $(this).closest('tr').find('.display_debit').css({backgroundColor: 'red'});
                                }else{

                                    // $(this).closest('tr').find('.debit').css({backgroundColor: 'white'});
                                    $(this).closest('tr').find('.debit').val(credit);
                                    $(this).closest('tr').find('.display_debit').val(credit);
                                }
                          }else{
                             $(this).closest('tr').find('.debit').val(credit);
                             $(this).closest('tr').find('.display_debit').val(credit);
                             $(this).closest('tr').find('.display_credit').css({backgroundColor: ''});
                          }
                    }else{
                        $(this).closest('tr').find('.debit').val(credit);
                        $(this).closest('tr').find('.display_debit').val(credit);
                        $(this).closest('tr').find('.display_credit').css({backgroundColor: ''});
                    }
                    $(this).closest('tr').find('.display_debit').attr('readonly', false);
                    $(this).closest('tr').find('.display_credit').attr('readonly', true);
                }
                $(this).closest('tr').find('.display_debit').attr('readonly', false);
                $(this).closest('tr').find('.display_credit').attr('readonly', true);

            }
        })


        $('#orders').on('change blur','.display_credit, .display_debit',function(){
            if($(this).val()){
                $(this).val(MakeCurrency($(this).val(),true,amount_decimals));
            }
        })
        $('#orders').on('click  keyup','.display_credit, .display_debit ',function(event){
            if (event.type === 'click' || (event.type === 'keyup' && event.key === 'Tab')) {
                if($(this).val()<=0){
                    if($(this).attr('class').search('display_credit')>=0 && $(this).closest('tr').find('.DrCr').val()=='Cr'){
                        let check_blance=$(this).closest('tr').find('.blance').val().search("Cr");
                        let credit=parseFloat(DrCrCalculation('credit'));
                        if(credit){
                            if(check_blance==-1){
                                if(debit_credit_amont==0){
                                    if(parseFloat($(this).closest('tr').find('.blance').val())<parseFloat(DrCrCalculation('credit'))){
                                        $(this).closest('tr').find('.credit').val('');
                                        $(this).closest('tr').find('.display_credit').val('');
                                        $(this).closest('tr').find('.display_credit').css({backgroundColor: 'red'});
                                    }else{
                                        $(this).val(credit);
                                        $(this).closest('tr').find('.credit').val(credit);
                                        $(this).closest('tr').find('.display_credit').css({backgroundColor: 'white'});
                                    }
                                }else{
                                    $(this).closest('tr').find('.credit').val(credit);
                                    $(this).val(MakeCurrency(credit,false,amount_decimals));                       }
                            }else{
                                $(this).closest('tr').find('.credit').val(credit);
                                $(this).val(MakeCurrency(credit,false,amount_decimals));
                            }
                        }

                    }else if($(this).attr('class').search('display_debit')>=0 && $(this).closest('tr').find('.DrCr').val()=='Dr'){
                        let check_blance=$(this).closest('tr').find('.blance').val().search("Dr");
                        let debit=parseFloat(DrCrCalculation('debit'));
                        if(debit){
                            if(check_blance==-1){
                                if(debit_credit_amont==0){
                                    if(parseFloat($(this).closest('tr').find('.blance').val())<parseFloat(DrCrCalculation('debit'))){
                                        $(this).closest('tr').find('.debit').val('');
                                        $(this).closest('tr').find('.display_debit').val('');
                                        $(this).closest('tr').find('.display_debit').css({backgroundColor: 'red'});
                                    }else{
                                        $(this).val(debit);
                                        $(this).closest('tr').find('.debit').val(debit);
                                        $(this).closest('tr').find('.display_debit').css({backgroundColor: 'white'});
                                    }
                                }else{
                                    $(this).closest('tr').find('.debit').val(debit);
                                    $(this).val(MakeCurrency(debit,false,amount_decimals));
                                }
                            }else{
                                $(this).closest('tr').find('.debit').val(debit);
                                $(this).val(MakeCurrency(debit,false,amount_decimals));
                            }
                        }


                    }

                }else if(parseFloat(MakeCurrency($(this).val(),false,amount_decimals))>0){
                    $(this).val(MakeCurrency($(this).val(),false,amount_decimals));
                }
                button_debit_or_credit_total();
            }
        })
        function button_debit_or_credit_total(){
            let debit=0;
            let credit=0;
            $('#orders tr').each(function(i){
                if(parseFloat($(this).find('.debit').val())) debit+=parseFloat($(this).find('.debit').val());
                if(parseFloat($(this).find('.credit').val())) credit+=parseFloat($(this).find('.credit').val());
            })
            $('.total_dedit').val(parseFloat(debit).toFixed(amount_decimals));
            $('.total_credit').val(parseFloat(credit).toFixed(amount_decimals));
            if(!areApproxEqual(debit,credit)){
                $(":submit").attr("disabled", true);
            }else{
                $(":submit").attr("disabled", false);
            }
            if(dc_amnt==0){
                if(debit==0||debit==''){
                  $(":submit").attr("disabled", true);
                }else{
                    if(areApproxEqual(debit,credit)){
                    $(":submit").attr("disabled", false);
                    }
                }
                if(credit==0||credit==''){
                   $(":submit").attr("disabled", true);
                }else{
                    if(areApproxEqual(debit,credit)){
                     $(":submit").attr("disabled", false);
                    }
                }
            }
        }
        $('#orders').on('keyup','.total_debit_val, .total_credit_val',function(){
            button_debit_or_credit_total();

        })
        $('#orders').on('keyup ','.display_credit',function(){
            let check_blance=$(this).closest('tr').find('.blance').val().search("Cr");
            if(check_blance==-1){
                if(debit_credit_amont==0){

                    if(parseFloat($(this).closest('tr').find('.blance').val())<parseFloat($(this).closest('tr').find('.credit').val())){
                        $(this).closest('tr').find('.credit').val('');
                        $(this).closest('tr').find('.display_credit').val('');
                        $(this).closest('tr').find('.display_credit').css({backgroundColor: 'red'});
                    }else{
                        $(this).closest('tr').find('.display_credit').css({backgroundColor: 'white'});
                    }
               }
            }
         })
         $('#orders').on('keyup','.display_debit',function(){
            let check_blance=$(this).closest('tr').find('.blance').val().search("Dr");
            if(check_blance==-1){
                if(debit_credit_amont==0){
                    if(parseFloat($(this).closest('tr').find('.blance').val())<parseFloat($(this).closest('tr').find('.debit').val())){
                        $(this).closest('tr').find('.debit').val('');
                        $(this).closest('tr').find('.display_debit').val('');
                        $(this).closest('tr').find('.display_debit').css({backgroundColor: 'red'});
                    }else{
                        $(this).closest('tr').find('.display_debit').css({backgroundColor: 'white'});
                    }
               }
            }
         })

        $('#orders').on('change','tr','.DrCr,.display_credit, .display_debit ',function(){
            button_debit_or_credit_total();
        })
        $(document).on('click', '.btn_remove', function() {
           button_debit_or_credit_total();
         });
        function DrCrCalculation(type){
            let debit=0;
            let credit=0;
            $('#orders tr').each(function(i){
                if(parseFloat($(this).find('.debit').val())) debit+=parseFloat($(this).find('.debit').val());
                if(parseFloat($(this).find('.credit').val())) credit+=parseFloat($(this).find('.credit').val());
            })

            if(debit>credit && type == 'credit'){
                $('.total_credit').val(MakeCurrency(debit,true,amount_decimals));
                return parseFloat(debit)-parseFloat(credit);
            }
            else if(debit<credit && type == 'debit'){
                $('.total_dedit').val(MakeCurrency(credit,true,amount_decimals));
                return parseFloat(credit)-parseFloat(debit);
            }


        }

        function getId(element){
            var id, idArr;
            id = element.attr('id');
            idArr = id.split("_");
            return idArr[idArr.length - 1];
          }


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
                change: function (event, ui) {
                    if (ui.item == null) {
                        if($(this).attr('name')==='ledger_name[]')$(this).closest('tr').find('.ledger_id').val('');
                        $(this).focus();
                    }
               },
                select: function( event, selectedData ) {
                    if(checkDuplicateItem(selectedData?.item?.data?.ledger_head_id,'.ledger_id')){
                        currentEle.val('');
                    }else if(selectedData && selectedData.item && selectedData.item.data){
                        var rowNo, data;
                        rowNo = getId(currentEle);
                        data = selectedData.item.data;
                        currentEle.css({backgroundColor: 'white'});
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
   $("#edit_received_id").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id=$('.tran_id').val();
        $("#edit_received_btn").text('Upadte');
        $.ajax({
            url: "{{ url('voucher-payment') }}" + '/' + id,
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data,status,xhr) {
                    swal_message(data.message,'success','Successfully');
                    $('#error_voucher_no').text('');
                    setTimeout(function () {  window.location.href='{{ url()->previous() }}'; },100);
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

    // delete payment ajax request
$(document).on('click', '.deleteIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id =$('.tran_id').val();
        swal(swal_data()).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('voucher-payment') }}" + '/' + id ,
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
});

// Cancelled payment ajax request
$(document).on('click', '.CancelIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id ="{{$data->tran_id}}";
        var narration =$('.narration').val();
        swal(swal_data()).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('voucher-payment-cancel') }}" + '/' + id ,
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
input_checking('ledger');
</script>
@endpush
@endsection

