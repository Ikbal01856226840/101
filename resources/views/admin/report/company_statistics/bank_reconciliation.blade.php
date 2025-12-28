
@extends('layouts.backend.app')
@section('title','Bank Reconciliation')
@push('css')
 <!-- model style -->
 <link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
 <style>
    input[type=radio] {
        width: 20px;
        height: 20px;
    }
    input[type=checkbox] {
        width: 20px;
        height: 20px;
    }
    table {width:100%;grid-template-columns: auto auto;}
    .th{
        border: 1px solid #ddd;;
    }
    body{
        overflow: auto !important;
    }
</style>
<!-- <link rel="stylesheet" type="text/css" href="{{asset('common_css/selectSearchable.css')}}"> -->
@endpush
@section('admin_content')<br>
<!-- add component-->
@component('components.report', [
    'title' => 'Bank Reconciliation',
    'print_layout'=>'landscape',
    'print_header'=>'Bank Reconciliation',
    'user_privilege_title'=>'BankReconciliation',
    'print_date'=>1,
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="bank_reconciliation_form"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row ">
            <div class="col-md-2">
                <label>Bank Name :</label>
                {{-- js-example-basic-single --}}
                <select name="ledger_id" id="ledger_id" class="party_ledger_auto_completed_id   ledger_id" required>
                    <option value="">--Select one--</option>
                    {!!html_entity_decode($ledgers)!!}
                    {{-- @foreach ($ledgers as $row)
                        <option value="{{$row->group_chart_id}}">{{$row->group_chart_name}}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="col-md-2">
                <div class="row  m-0 p-0">
                    <div class="col-md-6 m-0 p-0">
                        <label>Date From: </label>
                        <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{$form_date??financial_end_date(date('Y-m-d'))}}">
                    </div>
                    <div class="col-md-6 m-0 p-0">
                        <label>Date To : </label>
                        <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{$to_date??financial_end_date(date('Y-m-d'))}}">
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group description_show my-0 py-0">
                    <p class="closing_blance  my-0 py-0" style="font-size: 18px;"></p>
                    <input class="form-check-input " type="checkbox" id="narration" value="0">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Narration
                    </label>
                </div>
                <div class="form-group description_show my-0 py-0">
                    <input class="form-check-input "  type="checkbox" id="dont_show_bank_date" value="0">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Don't show idle 'Bank Date'
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <label></label><br>
                <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
            </div>
            <div class="col-md-5">
                <label></label>
                <div class="col-md-12">
                    <label class="fs-6" style="min-width: 1px;">SORT by :</label>
                    <input class="form-check-input sort_by" type="radio" name="sort_by" value="1" checked="checked">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        None
                    </label>
                    <input class="form-check-input sort_by_particular sort_by" type="radio" name="sort_by" value="5">
                    <label class="form-check-label fs-6 sort_by_particular" for="flexRadioDefault1">
                        Particular
                    </label>
                    <input class="form-check-input sort_by" type="radio" name="sort_by" value="2">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Debit
                    </label>
                    <input class="form-check-input last_update sort_by" type="radio" name="sort_by" value="3">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Credit
                    </label>
                </div>
            </div>
        </div>
    </form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_ledger_group_wise_report">
    <table id="tableId" style=" border-collapse: collapse;" class="table table-striped customers ">
        <thead>
            <tr>
                <th class="th" style="width: 1%;">SL.</th>
                <th class="th" style="width: 3%;">Particulars</th>
                <th class="th" style="width: 2%;text-align:right;">Voucher Type</th>
                <th class="th" style="width: 2%;text-align:right;">Voucher No</th>
                <th class="th narration" style="width: 2%;">Narration</th>

                <th class="th" style="width: 2%;text-align:right;">Instrument Date</th>
                <th class="th" style="width: 2%;text-align:right;">Bank Date</th>
                <th class="th"style="width: 3%;text-align:right;">Debit</th>
                <th class="th"style="width: 2%;text-align:right;">Credit</th>
            </tr>
        </thead>
        <tbody id="myTable" class="bank_reconciliation_body">
        </tbody>
        <tr>
            <th class="th footer_colspan" style="width: 2%;text-align:right; color: white;white;font-size: 15px" colspan="7">Balance as per Company books :</th>
            <th class="th total_debit_credit_debit "style="width: 3%;text-align:right;color: white;font-size: 20px"></th>
            <th class="th total_debit_credit_credit "style="width: 3%;text-align:right;color: white;font-size: 20px"></th>
        </tr>
        <tr>
            <th class="th footer_colspan" style="width: 2%;text-align:right;color: white;font-size: 15px" colspan="7">Amounts not reflected in bank :</th>
            <th class="th date_wise_total_debit_credit_debit" style="width: 3%;text-align:right;color: white;font-size: 20px"></th>
            <th class="th date_wise_total_debit_credit_credit" style="width: 3%;text-align:right;color: white;font-size: 20px"></th>
        </tr>
       <tr>
            <th class="th footer_colspan" style="width: 2%;text-align:right; color: white;font-size: 15px" colspan="7">Balance as per Bank :</th>
            <th class="th acual_total_debit_credit_debit "style="width: 3%;text-align:right;color: white;font-size: 20px"></th>
            <th class="th acual_total_debit_credit_credit "style="width: 3%;text-align:right;color: white;font-size: 20px"></th>
      </tr>
    </table>
    <div class="col-sm-12 text-center hide-btn">
        <span><b>Copyright &copy; 2014-2022 <a href="http://www.hamko-ict.com/">Hamko-ICT.</a> All rights
                reserved.</b></span>
    </div>
</div>
@endslot
@endcomponent
<br>
@push('js')

<!-- <script type="text/javascript" src="{{asset('common_js/select_searchable.js')}}"></script> -->

<script>
    const amount_decimals="{{company()->amount_decimals}}";
    let debit_total=0;
    let credit_total=0;
    let date_wise_debit=0;
    let date_wise_credit=0;
    // party ledger
    $(document).ready(function () {
        if($('#narration').is('checked')){ 
            $('.narration').removeClass("d-none");
            $('.footer_colspan').attr('colspan','7');
        }else{ 
            $('.narration').addClass("d-none");
            $('.footer_colspan').attr('colspan','6');
        }
        if($("#dont_show_bank_date").is('checked')){
            $('#myTable').addClass("d-none");
        }else{
            $('#myTable').removeClass("d-none");
        }
        // $('#ledger_id').make_searchable();

        $("#bank_reconciliation_form").submit(function(e) {
                debit_total=0;
                credit_total=0;
                e.preventDefault();
                print_date();
                $(".modal").show();
                let ledger_id=$(".ledger_id").val();
                const fd = new FormData(this);
                $.ajax({
                    url: '{{ route("report-bank-reconciliation-data") }}',
                        method: 'POST',
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                        $(".modal").hide();
                        if($('#narration').is(':checked')){ 
                            $('.narration').removeClass("d-none");
                            $('.footer_colspan').attr('colspan','7');
                        }else{ 
                            $('.narration').addClass("d-none");
                            $('.footer_colspan').attr('colspan','6');
                        }
                        if($("#dont_show_bank_date").is(':checked')){
                            $('#myTable').addClass("d-none");
                        }else{
                            $('#myTable').removeClass("d-none");
                        }
                        get_bank_reconciliation_val(response);

                        $(".setup_date").datepicker({ dateFormat: "yy-mm-dd",});
                        $('tbody').find('tr .sl').each(function(i) {
                            $(this).text(i + 1);
                        });
                        recalculate_date_wise_debit_credit();
                        },
                        error : function(data,status,xhr){
                            Unauthorized(data.status);
                        }
                });
        });


        //redirect route
        $('.sd').on('click', '.customers tbody tr ', function(e) {
            localStorage.setItem("end_date", $('.to_date').val());
            localStorage.setItem("start_date", $('.from_date').val());
            localStorage.setItem("voucher_id", $('.voucher_id').val());
            e.preventDefault();
            let day_book_arr = $(this).closest('tr').attr('id').split(",");
            if (day_book_arr[1] == 14) {
                window.open(`{{url('voucher-receipt/edit')}}/${day_book_arr[0]}`, '_blank');
            } else if (day_book_arr[1] == 8) {
                window.open(`{{url('voucher-payment')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 1) {
                window.open(`{{url('voucher-contra')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 10) {
                window.open(`{{url('voucher-purchase')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 24) {
                window.open(`{{url('voucher-grn')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 19) {
                window.open(`{{url('voucher-sales')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 23) {
                window.open(`{{url('voucher-gtn')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 29) {
                window.open(`{{url('voucher-purchase-return')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 22) {
                window.open(`{{url('voucher-transfer')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 25) {
                window.open(`{{url('voucher-sales-return')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 21) {
                window.open(`{{url('voucher-stock-journal')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 6) {
                window.open(`{{url('voucher-journal')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 28) {
                window.open(`{{url('voucher-commission')}}/${day_book_arr[0]}/edit`, '_blank');
            }
            else if (day_book_arr[1] == 20) {
                window.open(`{{url('voucher-sales-order')}}/${day_book_arr[0]}/edit`, '_blank');
            }
        })

        // table header fixed
        let display_height=$(window).height();
        $('.tableFixHead_ledger_group_wise_report').css('height',`${display_height-130}px`);
    });


    function get_bank_reconciliation_val(response) {
        $(".bank_reconciliation_body").empty();
        const chunkSize = 500; // Adjust chunk size as needed
        const totalRows = response.data.length;
        let currentRow = 0;

        function appendChunk() {
            let htmlFragments = [];
            // Calculate the end index for the chunk
            const endIndex = Math.min(currentRow + chunkSize, totalRows);

            for (let key = currentRow; key < endIndex; key++) {
                const v = response.data[key];
                debit_total+=(v.debit||0);

                credit_total+=(v.credit||0);

                htmlFragments.push(`<tr class="left left-data editIcon table-row"  id="${v.tran_id},${v.voucher_type_id}'">
                    <td style="width: 1%; border: 1px solid #ddd;">${key + 1}</td>
                    <td style="width: 3%; border: 1px solid #ddd; font-size: 16px;">${v?.ledger_name || ''}</td>
                    <td style="width: 3%; border: 1px solid #ddd; font-size: 16px;" class="voucher_name">${v?.voucher_name || ''}</td>
                    <td style="width: 3%; border: 1px solid #ddd; font-size: 16px;">${v?.invoice_no || ''}</td>
                    <td style="width: 3%; border: 1px solid #ddd; font-size: 16px;" 
                    class="narration text-wrap ${$('#narration').is(':checked') ? '' : 'd-none'}">${v?.narration || ''}</td>
                    <td style="width: 3%; border: 1px solid #ddd; font-size: 16px;">${v?.transaction_date  || ''}</td>
                    <td style="width: 3%; border: 1px solid #ddd; font-size: 18px;">
                        <input
                            type="text"
                            name="bank_date"
                            value="${v?.bank_date || ''}"
                            class="form-control setup_date bank_date fs-5 "

                        >
                    </td>
                    <td style="width: 2%; border: 1px solid #ddd; font-size: 18px;text-align:right;" class="debit">${v?.debit?.toFixed(amount_decimals)}</td>
                    <td style="width: 2%; border: 1px solid #ddd; font-size: 18px;text-align:right;">${v?.credit.toFixed(amount_decimals)}</td>
                </tr>`);
            }


            $(".bank_reconciliation_body").append(htmlFragments.join(''));
            recalculate_date_wise_debit_credit();
            get_hover();

            // If there are more rows, append the next chunk after a delay
            currentRow += chunkSize;
            if (currentRow < totalRows) {
                setTimeout(appendChunk, 0); // Use setTimeout to allow UI updates
            }
        }

        // Start appending chunks
        appendChunk();
    }

    $('.bank_reconciliation_body').on('change','.bank_date',function(){

            let csrf_token = $('meta[name="csrf-token"]').attr('content');
            let tran_id =$(this).closest('tr').attr('id').split(",")[0];
            let bank_date =$(this).closest('tr').find('.bank_date').val();
            swal(swal_data()).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    $.ajax({
                        url: "{{ route('report-bank-reconciliation-date-store') }}",
                        type: "POST",
                        data : { '_token' : csrf_token ,'bank_date':bank_date,'tran_id':tran_id},
                        success: function (data) {
                            swal_message(data.message,'success','Successfully');
                            recalculate_date_wise_debit_credit();
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
            });
    });
   // alert message
   function swal_message(data,message,m_title){
        swal({
            title:m_title,
            text: data,
            type: message,
            timer: '3000'
        });
   }
   function recalculate_date_wise_debit_credit(){

     if($('.bank_reconciliation_body').find('tr').length>0){
        date_wise_debit=0;
        date_wise_credit=0;
        $('.bank_reconciliation_body').find('tr').each(function(i){

            if($('.to_date').val()<$(this).find('.bank_date').val()){
                date_wise_debit+=Number($(this).find('.debit').text());
                date_wise_credit+=Number($(this).find('.credit').text());

            }

        });
        let total_debit_credit=(debit_total-credit_total);
        let date_wise_total_debit_credit=(date_wise_debit-date_wise_credit);
        $('.date_wise_total_debit_credit_debit').text("0.00");
        $('.date_wise_total_debit_credit_credit').text("0.00");
        $('.total_debit_credit_debit').text("0.00");
        $('.total_debit_credit_credit').text("0.00");
        $('.acual_total_debit_credit_debit').text("0.00");
        $('.acual_total_debit_credit_credit').text("0.00");
        if(total_debit_credit>0){
            $('.total_debit_credit_debit').text(Math.abs(total_debit_credit).toFixed(2));
        }else{
            $('.total_debit_credit_credit').text(Math.abs(debit_total-credit_total).toFixed(2));
        }
        if( date_wise_total_debit_credit>0){
            $('.date_wise_total_debit_credit_debit').text(Math.abs(date_wise_total_debit_credit).toFixed(2));
        }else{
            $('.date_wise_total_debit_credit_credit').text(Math.abs(date_wise_total_debit_credit).toFixed(2));
        }
        if((total_debit_credit-date_wise_total_debit_credit)>0){
            $('.acual_total_debit_credit_debit').text(Math.abs(total_debit_credit-date_wise_total_debit_credit).toFixed(2));
        }else{
            $('.acual_total_debit_credit_credit').text(Math.abs(total_debit_credit-date_wise_total_debit_credit).toFixed(2));
        }


     }
   }
</script>
@endpush
@endsection
