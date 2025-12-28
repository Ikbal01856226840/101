
@extends('layouts.backend.app')
@section('title','Party Ledger')
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
<link rel="stylesheet" type="text/css" href="{{asset('common_css/selectSearchable.css')}}">
@endpush
@section('admin_content')<br>
<!-- add component-->
@component('components.report', [
    'title' => 'Party Ledger Closing Balance',
    'print_layout'=>'landscape',
    'print_header'=>'Party Ledger Closing Balance',
    'user_privilege_title'=>'PartyLedgerClosingBalance',
    'print_date'=>1,
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="group_wise_party_form"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row ">
            <div class="col-md-3">
                <label>Party Name :</label>
                {{-- form-control  js-example-basic-single --}}
                <select
                    name="ledger_id"
                    id="ledger_id"
                    class="w3-select ledger_id"
                    required>
                  @if($all==1)
                    <option value="0">--All--</option>
                  @endif
                    {!!html_entity_decode($ledgers)!!}
                </select>
            </div>
            <div class="col-md-3">
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
                <label></label><br>
                <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
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
                <th class="th" style="width: 2%;text-align:right;">Opening Balance</th>
                <th class="th"style="width: 3%;text-align:right;">Debit</th>
                <th class="th"style="width: 2%;text-align:right;">Credit</th>
                <th class="th"class="closing_checkbox" style="width: 2%;text-align:right;">Current Balance</th>
            </tr>
        </thead>
        <tbody id="myTable" class="ledger_body">
        </tbody>
        <tfoot>
            <tr>
                <th class="th" style="width: 1%;">SL.</th>
                <th class="th" style="width: 3%;">Particulars</th>
                <th class="th" style="width: 2%;text-align:right;">Opening Balance</th>
                <th class="th" style="width: 3%;text-align:right;">Debit</th>
                <th class="th" style="width: 2%;text-align:right;">Credit</th>
              <th style="width: 3%;text-align:right;" class="th closing_checkbox">Current Balance</th>
            </tr>
        </tfoot>
    </table>
</div>
@endslot
@endcomponent
<br>
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script type="text/javascript" src="{{asset('dist/jquery-simple-tree-table.js')}}"></script>
<script type="text/javascript" src="{{asset('common_js/select_searchable.js')}}"></script>

<script>
 let   op_1=0;currentBalance=0;
$('#ledger_id').make_searchable();
var amount_decimals="{{company()->amount_decimals}}";
// ledger id check
if("{{$ledger_id??0}}"!=0){
     $('.ledger_id').val("{{$ledger_id??0}}");
}
// party ledger
$(document).ready(function () {
    // get party ledger
    function get_party_ledger_initial_show(){
        print_date();
        let ledger_name = $('.ledger_id').find('option:selected').text();
        localStorage.setItem('ledger_name', '');
        localStorage.setItem('ledger_name', ledger_name);
        $(".modal").show();
        $.ajax({
            url: '{{ route("party-ledger-get-data") }}',
                method: 'GET',
                data: {
                    to_date:$('.to_date').val(),
                    from_date:$('.from_date').val(),
                    ledger_id:$(".ledger_id").val(),
                },
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_current_party_ledger_val(response)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
    }
    // ledger get id check
    if("{{$ledger_id??0}}"!=0){
        get_party_ledger_initial_show();
    }
    $("#group_wise_party_form").submit(function(e) {
            e.preventDefault();
            print_date();
            op_1=0;
            $(".modal").show();
            let ledger_id=$(".ledger_id").val();
            let ledger_name = $('.ledger_id').find('option:selected').text();
            localStorage.setItem('ledger_name', '');
            localStorage.setItem('ledger_name', ledger_name);
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("report-party-ledger-closed-balanc-data") }}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                      $(".modal").hide();
                      if(ledger_id==0){
                        get_party_ledger_val(response)
                      }else{
                        get_current_party_ledger_val(response.data)
                      }
                    },
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
    });

    function get_party_ledger_val(response) {
        $(".ledger_body").empty();
        const chunkSize = 500; // Adjust chunk size as needed
        const totalRows = response.data.length;
        let currentRow = 0;

        function appendChunk() {
            let htmlFragments = [];
            // Calculate the end index for the chunk
            const endIndex = Math.min(currentRow + chunkSize, totalRows);

            for (let key = currentRow; key < endIndex; key++) {
                const v = response.data[key];
                let balance;
                let sign;
                let closingBalance;
                let closingsign;

                if (v.nature_group == 1 || v.nature_group == 3) {
                    balance = openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance) + (parseFloat(v.op_group_debit || 0) - parseFloat(v.op_group_credit || 0));
                    sign = balance >= 0 ? 'Dr' : 'Cr';
                } else {
                    balance = openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance) + (parseFloat(v.op_group_credit || 0) - parseFloat(v.op_group_debit || 0));
                    sign = balance >= 0 ? 'Cr' : 'Dr';
                }

                const openingBalance = Math.abs(balance).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ' ' + sign;
                const totalDebit = (v.total_debit || 0).toFixed(amount_decimals);
                const totalCredit = (v.total_credit || 0).toFixed(amount_decimals);

                if (v.nature_group == 1 || v.nature_group == 3) {
                    closingBalance = ((((parseFloat(v.op_group_debit || 0) - parseFloat(v.op_group_credit || 0)) + (parseFloat(v.total_debit || 0)) - parseFloat(v.total_credit || 0)) +  openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance)));

                    closingsign = closingBalance >= 0 ? 'Dr' : 'Cr';
                } else {
                    closingBalance = ((((parseFloat(v.op_group_credit || 0) - parseFloat(v.op_group_debit || 0)) +
                        (parseFloat(v.total_credit || 0)) - parseFloat(v.total_debit || 0)) + openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance)));
                    closingsign = closingBalance >= 0 ? 'Cr' : 'Dr';
                }

                const currentBalance = Math.abs(closingBalance).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ' ' + closingsign;

                htmlFragments.push(`<tr class="left left-data editIcon table-row">
                    <td style="width: 1%; border: 1px solid #ddd;">${key + 1}</td>
                    <td style="width: 3%; border: 1px solid #ddd; font-size: 16px;">${v.ledger_name || ''}</td>
                    <td style="width: 3%; border: 1px solid #ddd; font-size: 18px;text-align:right;">${openingBalance}</td>
                    <td style="width: 2%; border: 1px solid #ddd; font-size: 18px;text-align:right;">${totalDebit.replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                    <td style="width: 2%; border: 1px solid #ddd; font-size: 18px;text-align:right;">${totalCredit.replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                    <td style="width: 3%; border: 1px solid #ddd; font-size: 18px;text-align:right;">${currentBalance}</td>
                </tr>`);


            }

            $(".ledger_body").append(htmlFragments.join(''));
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

    function get_current_party_ledger_val(response){
              let html = [];
                   html.push( `<table   id="tableId"  style=" border-collapse: collapse;"   class="table  customers wrap" >
                         <thead >
                          <tr>
                            <th style="width: 1%;  border: 1px solid #ddd;">SL.</th>
                            <th style="width: 1%;  border: 1px solid #ddd;">Date</th>
                            <th style="width: 3%;  border: 1px solid #ddd;">Particulars</th>
                            <th style="width: 1%;  border: 1px solid #ddd;">Voucher Type</th>
                            <th style="width: 1%;  border: 1px solid #ddd;" >Voucher No</th>
                            <th style="width: 3%;  border: 1px solid #ddd;" >Narration</th>
                            <th style="width: 2%;  border: 1px solid #ddd;text-align:right;" >Debit</th>
                            <th style="width: 2%;  border: 1px solid #ddd;text-align:right;" >Credit</th>
                            <th style="width: 2%;  border: 1px solid #ddd;text-align:right;" >Balance</th>
                        </tr>
                    </thead>
                    <tbody id="myTable" class="qw">`);
                        let total_debit=0,total_credit=0,opening_balance=0;
                        totalDebit = 0;
                totalCredit = 0;
                closingBlance = 0;
       

        const opening = response.op_party_ledger[0] || { op_total_debit: 0,op_total_credit: 0 };
        let total_op_val;
        let total_op_sign;
            if(response.group_chart_nature.nature_group == 1 ||response.group_chart_nature.nature_group == 3){
                total_op_val=(openning_blance_cal(response.group_chart_nature.nature_group,response.group_chart_nature.DrCr,response.group_chart_nature.opening_balance) + ((opening.op_total_debit || 0) - (opening.op_total_credit || 0)));
                total_op_sign = total_op_val >= 0 ? 'Dr' : 'Cr';
            }else{
                total_op_val=(openning_blance_cal(response.group_chart_nature.nature_group,response.group_chart_nature.DrCr,response.group_chart_nature.opening_balance) + ((opening.op_total_credit || 0) - (opening.op_total_debit || 0)))
                total_op_sign = total_op_val >= 0 ? 'Cr' : 'Dr';
            }
            const openingBalance = Math.abs(total_op_val).formatBangladeshCurrencyType("accounts",'',total_op_sign);
            html.push( `<tr class="left left-data editIcon table-row">
                            <td colspan="8"  class="th" style="width: 1%;font-size: 18px; text-align: right;">Opening Balance :</td>
                            <td style="width: 1%; font-size: 18px; text-align: right;font-family: Arial, sans-serif;" class="th  inline_op">
                                ${openingBalance}
                            </td>
                        </tr>`)
                        $.each(response.party_ledger, function(key, v) {
                            let closing_val;
                            let total_closing_sign
                            if(response.group_chart_nature.nature_group == 1 ||response.group_chart_nature.nature_group == 3){
                                closing_val= (parseFloat(v.debit_sum|| 0) - parseFloat(v.credit_sum || 0));
                                if(op_1==0){
                                    closingBlance+=parseFloat(closing_val)+parseFloat(total_op_val);
                                    total_closing_sign= closingBlance >= 0 ? 'Dr' : 'Cr';
                                    op_1=1;
                                }else{
                                    closingBlance+=parseFloat(closing_val);
                                    total_closing_sign= closingBlance >= 0 ? 'Dr' : 'Cr';
                                }
                            }else{
                                closing_val= (parseFloat(v.credit_sum || 0) - parseFloat( v.debit_sum|| 0));
                                if(op_1==0){
                                    closingBlance+=parseFloat(closing_val)+parseFloat(total_op_val);
                                    total_closing_sign = closingBlance >= 0 ? 'Cr' : 'Dr';
                                    op_1=1;
                                }else{
                                    closingBlance+=parseFloat(closing_val);
                                    total_closing_sign = closingBlance >= 0 ? 'Cr' : 'Dr';
                                }
                            }
                         currentBalance =Math.abs(closingBlance||0).formatBangladeshCurrencyType("accounts",'',total_closing_sign);
                           html.push( `<tr id='${v.tran_id},${v.voucher_type_id}' class="left left-data editIcon table-row"> 
                                   <td  style="width: 1%;  border: 1px solid #ddd;">${(key+1)}</td>
                                   <td  style="width: 1%;  border: 1px solid #ddd; font-size: 16px;">${join( new Date(v.transaction_date), options, ' ')}</td>
                                   <td  style="width: 1%;  border: 1px solid #ddd; font-size: 16px; "class="text-wrap"><i>${(v.ledger_name||'')}</i></td>
                                   <td  style="width: 1%;  border: 1px solid #ddd; font-size: 16px;" class="text-wrap"><a class="party_ledger_voucher" style=" text-decoration: none; font-size: 16px;color:#0B55C4;" href="#">${v.voucher_name||''}</a></td>
                                   <td  style="width: 1%;  border: 1px solid #ddd; font-size: 16px;">${v.invoice_no||''}</td>
                                    <td class="text-wrap" style="width: 1%;  border: 1px solid #ddd; font-size: 16px;">${v.narration||''}</td>
                                   <td  style="width: 2%;  border: 1px solid #ddd; text-align: right; font-size: 16px;">${((v.debit_sum||0)).formatBangladeshCurrencyType("accounts")}</td>
                                  <td  style="width: 2%;  border: 1px solid #ddd;text-align: right; font-size: 16px;">${((v.credit_sum||0)).formatBangladeshCurrencyType("accounts")}</td>
                                  <td  style="width: 2%;  border: 1px solid #ddd;text-align: right; font-size: 16px;">${(currentBalance||0)}</td>
                           </tr> `);
                        });

                        html.push( `<tr class="left left-data editIcon table-row">
                            
                            <td colspan="8"  class="th" style="width: 1%;font-size: 18px; text-align: right;font-weight: bold">Closing Balance :</td>
                           
                            <td style="width: 1%; font-size: 18px; text-align: right;font-family: Arial, sans-serif;font-weight: bold" class="th  inline_op">
                                ${currentBalance||(openingBalance||0)}
                            </td>
                        </tr>`);

                    html.push( `</tbody></table>`)
                   
            $(".sd").html(html.join(''));
            get_hover();
    }
});
    //redirect route
    $(document).ready(function() {
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
    });
    $(document).ready(function(){
    // table header fixed
    let display_height=$(window).height();
    $('.tableFixHead_ledger_group_wise_report').css('height',`${display_height-130}px`);
});
</script>
</script>
@endpush
@endsection
