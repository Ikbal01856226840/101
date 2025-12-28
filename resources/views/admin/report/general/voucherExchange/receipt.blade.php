@extends('layouts.backend.app')
@section('title','Voucher Receive')
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
    @csrf {{ method_field("POST") }}
    @component('components.accounts.accounts', [
        'voucher' => $voucher,
        'branch_setup'=>$branch_setup,
        'voucher_invoice'=>$voucher_invoice,
        'createOrEdit'=>'exchange',
        'data' => $data
    ]);
    @endcomponent
</form>
@endslot
@endcomponent
@push('js')
<script>
</script>
<script type="text/javascript" src="{{asset('libraries/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{asset('voucher_setup/voucher_setup_receive.js')}}"></script>
<script type="text/javascript" src="{{asset('voucher_setup/voucherValidation.js')}}"></script>
<script type="text/javascript" src="{{asset('voucher_setup/accounts/accounts.js')}}"></script>

<script>
$(document).ready(function() {

    $.ajax({
        type: 'GET',
        url: "{{ url('edit/debit-credit') }}",
        async: false,
        data: {
            tran_id: t_m_id
        },
        dataType: 'json',
        success: function(response) {
            $.each(response.data, function(i, val) {
                let debit_credit_blance,debit_credit_sign;
                if(val.nature_group == 1 || val.nature_group == 3){
                    debit_credit_blance= ((val.debit_sum_1-val.credit_sum_1) +openning_blance_cal(val.nature_group,val.DrCr,val.opening_balance)) ;
                    debit_credit_sign = debit_credit_blance >= 0 ? 'Dr' : 'Cr';
                }else{
                    debit_credit_blance= ((val.credit_sum_2-val.debit_sum_2) +openning_blance_cal(val.nature_group,val.DrCr,val.opening_balance)) ;
                    debit_credit_sign = debit_credit_blance >= 0 ? 'Cr' : 'Dr';
                }
                $('#orders').append(`<tr   style="margin:0px;padding:0px;" class="p-0 m-0"  id="row${i}">
                    <input class="form-control  ledger_id m-0 p-0"  name="ledger_id[]" type="hidden" value="${val.ledger_head_id}" data-type="ledger_id" id="ledger_id_${i}"  for="${i}"/>
                    <input class="form-control  debit_credit_id m-0 p-0"  name="debit_credit_id[]" type="hidden" value="${val.debit_credit_id}" data-type="debit_credit_id" id="debit_credit_id_${i}"  for="${i}"/>
                    <td  class="m-0 p-0"><button  type="button" name="remove" id="${i}" class="btn btn-danger btn_remove cicle" style="padding: 0px 19px;margin:1px 0px;">-</button></td>
                    <td  class="m-0 p-0">
                        <select  name="DrCr[]" id="DrCr" data-field-name="DrCr"  class="form-control  js-example-basic-single  DrCr "  for="${i}">
                            <option   ${val.dr_cr=="Dr"?'selected':''} value="Dr">Dr</option>
                            <option  ${val.dr_cr=="Cr" ?'selected':''} value="Cr">Cr</option>
                        </select>
                    </td>

                    <td  class="m-0 p-0">
                        <input class="form-control ledger_name  autocomplete_txt" name="ledger_name[]" data-field-name="ledger_name"  type="text" data-type="ledger_name" value="${val.ledger_name}" id="ledger_name_${i}" " autocomplete="off" for="${i}"  />
                    </td>
                    <td  class="m-0 p-0">
                        <input
                            class="form-control  blance  text-right "
                            name="blance[]"
                            data-field-name="blance"
                            type="text"
                            id="blance_${i}"
                            value="${MakeCurrency(Math.abs(debit_credit_blance),true,amount_decimals) + debit_credit_sign}"
                            for="${i}"
                            readonly
                        />
                    </td>
                    <td  class="m-0 p-0">
                        <input
                            class="form-control display_debit text-right "
                            name="display_debit[]"
                            data-field-name="display_debit"
                            type="text"
                            data-type="display_debit"
                            value="${MakeCurrency(val.debit,true,amount_decimals)}"
                            ${val.dr_cr=="Dr"?'':'readonly'}
                            id="display_debit_${i}"
                            for="${i}"
                        />
                        <input
                            class="form-control debit text-right "
                            name="debit[]"
                            data-field-name="debit"
                            type="hidden"
                            data-type="debit"
                            value="${parseFloat(val.debit)}"
                            id="debit_${i}"
                            for="${i}"
                        />
                    </td>
                    <td  class="m-0 p-0">
                        <input
                            class="form-control display_credit text-right"
                            name="display_credit[]"
                            data-field-name="display_credit"
                            type="text"
                            step="any"
                            data-type="display_debit"
                            id="display_credit_${i}"
                            value="${MakeCurrency(val.credit,true,amount_decimals)}"
                            for="${i}"
                            ${val.dr_cr=="Cr" ?'':'readonly'}
                        />
                        <input
                            class="form-control credit text-right"
                            name="credit[]"
                            data-field-name="credit"
                            type="hidden"
                            data-type="debit"
                            id="credit_${i}"
                            for="${i}"
                            value="${val.credit}"
                        />
                    </td>
                    ${remark_is==1 && `<td  class="m-0 p-0 "><input class="form-control remark"  name="remark[]" type="text" data-type=" id="remark_${i}" value="${val.remark?val.remark:''}"  autocomplete="off" for="${i}"/></td>`}
                    </tr>`);
                rowCount = i;
            });
            button_debit_or_credit_total();
        }
    });


    $(document).on('click', '.btn_remove', function() {
        var button_id = $(this).attr('id');
        $('#row' + button_id + '').remove();
        arr.push($(this).closest('tr').find('.debit_credit_id').val());
        $('.delete_debit_credit_id').val(arr);
        button_debit_or_credit_total();
    });



    $("#edit_received_id").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#add_received_btn").text('Add');
        $.ajax({
                url: '{{ route("voucher-receipt.store") }}',
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
    // delete recived ajax request
    $(document).on('click', '.deleteIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id = $('.tran_id').val();
        swal(swal_data()).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('voucher-receipt') }}" + '/' + id,
                    type: "POST",
                    data: {
                        '_method': 'DELETE',
                        '_token': csrf_token
                    },
                    success: function(data) {
                        swal_message(data.message, 'success', 'Successfully');
                        setTimeout(function () { location.reload() },100);
                    },
                    error: function() {
                        swal_message(data.responseJSON.message, 'error', 'Error');
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



        // Cancelled receipt ajax request
    $(document).on('click', '.CancelIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id ="{{$data->tran_id}}";
        var narration =$('.narration').val();
        swal(swal_data()).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('voucher-receipt-cancel') }}" + '/' + id ,
                    type : "POST",
                    data : {'_method' : 'POST', '_token' : csrf_token ,'narration':narration},
                    success: function (data) {
                        swal_message(data.message,'success','Successfully');
                        setTimeout(function () { location.reload() },100);
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


</script>
<script type="text/javascript" src="{{asset('voucher_setup/accounts/accounts.js')}}"></script>

@endpush
@endsection
