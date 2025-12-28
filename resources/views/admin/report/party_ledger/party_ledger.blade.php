
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
<!-- setting component-->
@component('components.report_setting_modal', [
'id' =>'exampleModal',
'class' =>'modal fade',
'size'=>'modal-xl',
'page_title'=>'Party Ledger ',
'page_unique_id'=>36,
'ledger'=>'yes',
'accounts'=>"Yes",
'title'=>'Party Ledger',
'daynamic_function'=>'get_party_ledger_all_initial_show',
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Party Ledger',
    'print_layout'=>'No',
    'print_header'=>'Party Ledger',
    'user_privilege_title'=>'PartyLedger',
    'print_date'=>1,
    'party_name'=>1,
    'report_setting_model'=>'report_setting_model',
     'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="group_wise_party_form"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row ">
            <div class="col-md-3">
                <label>Party Name :</label>
                {{-- js-example-basic-single  --}}
                <select 
                    name="ledger_id" 
                    id="ledger_id" 
                    class="w3-select  ledger_id" required>
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
            <div class="col-md-2">
                <label></label><br>
                <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
            </div>
             <div class="col-md-3">
                    <label></label><br>
                    <input class="form-check-input narratiaon" type="checkbox" id="narratiaon" value="1">
                   <label class="form-check-label fs-6">Narration</label>
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
$('#ledger_id').make_searchable();
var amount_decimals="{{company()->amount_decimals}}";
// ledger id check
if("{{$ledger_id??0}}"!=0){
     $('.ledger_id').val("{{$ledger_id??0}}");
}
// party ledger
$(document).ready(function () {
    if ("{{ $ledger_id ?? 0 }}" != 0) {
            $('.ledger_id').val('{{$ledger_id??0}}');
            $('.ledger_id').trigger('change');
        }else{
            local_store_party_ledger_get();
        }
        // get_party_ledger_initial_show();
    // get party ledger
    // function get_party_ledger_initial_show(){
    //     print_date();
    //     let ledger_name = $('.ledger_id').find('option:selected').text();
    //     localStorage.setItem('ledger_name', '');
    //     localStorage.setItem('ledger_name', ledger_name);
    //     $(".modal").show();
    //     $.ajax({
    //         url: '{{ route("party-ledger-get-data") }}',
    //             method: 'GET',
    //             data: {
    //                 to_date:$('.to_date').val(),
    //                 from_date:$('.from_date').val(),
    //                 ledger_id:$(".ledger_id").val(),
    //             },
    //             dataType: 'json',
    //             success: function(response) {
    //                 $(".modal").hide();
    //                 get_current_party_ledger_val(response)
    //             },
    //             error : function(data,status,xhr){
    //                 Unauthorized(data.status);
    //             }
    //     });
    // }
    // ledger get id check
    if("{{$ledger_id??0}}"!=0){
        // get_party_ledger_initial_show();
    }
    $("#group_wise_party_form").submit(function(e) {
           local_store_party_ledger_set_data();
            e.preventDefault();
            print_date();
            $(".modal").show();
            let ledger_id=$(".ledger_id").val();
            let ledger_name = $('.ledger_id').find('option:selected').text();
            localStorage.setItem('ledger_name', '');
            localStorage.setItem('ledger_name', ledger_name);
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("party-ledger-get-data") }}',
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
                        get_current_party_ledger_val(response)
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
                   var html='';
                    html +='<table   id="tableId"  style=" border-collapse: collapse;"   class="table  customers wrap" >';
                    html +='<thead >';
                        html+='<tr>';
                            html+= '<th style="width: 1%;  border: 1px solid #ddd;">SL.</th>';
                            html+= '<th style="width: 1%;  border: 1px solid #ddd;">Date</th>';
                            html+= '<th style="width: 6%;  border: 1px solid #ddd;">Particulars</th>';
                            html+= '<th style="width: 1%;  border: 1px solid #ddd;">Voucher Type</th>';
                            html+='<th style="width: 1%;  border: 1px solid #ddd;" >Voucher No</th>';
                              html+= '<th style="width: 5%;table-layout: fixed;" class="th narration_show ">Narration</th>';
                            html+='<th style="width: 2%;  border: 1px solid #ddd;text-align:right;" >Debit</th>';
                            html+='<th style="width: 2%;  border: 1px solid #ddd;text-align:right;" >Credit</th>';
                        html+='</tr>';
                    html+='</thead>';
                    html+='<tbody id="myTable" class="qw">';
                        let total_debit=0,total_credit=0,opening_balance=0;
                        $.each(response.data.party_ledger, function(key, v) {
                            total_debit+=(v.debit_sum||0);total_credit+=(v.credit_sum||0);
                            html+='<tr id='+v.tran_id+","+v.voucher_type_id+' class="left left-data editIcon table-row"> ';
                                html += '<td  style="width: 1%;  border: 1px solid #ddd;">'+(key+1)+'</td>' ;
                                html += '<td  style="width: 1%;  border: 1px solid #ddd; font-size: 16px;">'+join( new Date(v.transaction_date), options, ' ')+'</td>' ;
                                html += '<td  style="width: 1%;  border: 1px solid #ddd; font-size: 16px; "class="text-wrap"><i style="font-weight: bold">'+(v.ledger_name ? v.ledger_name:'')+'</i></td>';
                                html += `<td  style="width: 1%;  border: 1px solid #ddd; font-size: 16px;" class="text-wrap">${redirectVoucherIdWise(v.voucher_type_id, v.tran_id,v.voucher_name)}</td>` ;
                                html += '<td  style="width: 1%;  border: 1px solid #ddd; font-size: 16px;">'+v.invoice_no+'</td>' ;
                                 if (($(".narratiaon").is(':checked'))) {
                                     html += `<td style="width:3%;border: 1px solid #ddd; font-size: 16px;" class="td text-wrap">${(v.narration ||"")}</td> `;
                                }
                                html += '<td  style="width: 2%;  border: 1px solid #ddd; text-align: right; font-size: 20px;">'+((v.debit_sum||0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>' ;
                                html += '<td  style="width: 2%;  border: 1px solid #ddd;text-align: right; font-size: 20px;">'+((v.credit_sum||0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>' ;
                            html+="</tr> ";
                        });
                        html+=`<tr class="left left-data editIcon table-row">
                              <td style="width: 1%;  border: 1px solid #ddd;"></td>
                              <td style="width: 1%;  border: 1px solid #ddd;"></td><td style="width: 1%;  border: 1px solid #ddd;">
                              </td><td colspan="2" style=" border: 1px solid #ddd;font-size: 18px; text-align: right;">Current Balance  :</td>
                              <td style="width: 1%;  border: 1px solid #ddd; font-size: 20px;text-align: right; ">${(total_debit).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                              <td style="width: 1%;  border: 1px solid #ddd; font-size: 20px;text-align: right;">${(total_credit).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td></tr>`;
                              let  inline_opening_sign;
                              let  closing_blance_sign;
                              if((response.data.group_chart_nature.nature_group==1)||(response.data.group_chart_nature.nature_group==3)){
                           opening_balance=(response.data.op_party_ledger[0]?((response.data.op_party_ledger[0].op_total_debit)-(response.data.op_party_ledger[0].op_total_credit)):0)+openning_blance_cal(response.data.group_chart_nature.nature_group,response.data.group_chart_nature.DrCr,response.data.group_chart_nature.opening_balance);
                           inline_opening_sign = opening_balance >= 0 ? 'Dr' : 'Cr';
                           console.log(inline_opening_sign=='Cr');
                           html+=`<tr class="left left-data editIcon table-row">
                            <td style="width: 1%;  border: 1px solid #ddd;"></td>
                            <td style="width: 1%;  border: 1px solid #ddd;"></td>
                            <td style="width: 1%;  border: 1px solid #ddd;"></td>
                            <td colspan="2"  style="width: 1%;   border: 1px solid #ddd;font-size: 18px;  text-align: right;">Opening Balance :</td>
                           <td style="width: 1%; border: 1px solid #ddd; font-size: 20px; text-align: right;">
                               ${inline_opening_sign === 'Dr' ? (Math.abs(opening_balance).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,') + inline_opening_sign) : ""}
                          </td>
                          <td style="width: 1%; border: 1px solid #ddd; font-size: 20px; text-align: right;">
                              ${inline_opening_sign === 'Cr' ? (Math.abs(opening_balance).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,') + inline_opening_sign) : ""}
                          </td>
                            </tr>`;
                        }else if((response.data.group_chart_nature.nature_group==2)||(response.data.group_chart_nature.nature_group==4)){
                            opening_balance=(response.data.op_party_ledger[0]?((response.data.op_party_ledger[0].op_total_credit)-(response.data.op_party_ledger[0].op_total_debit)):0)+openning_blance_cal(response.data.group_chart_nature.nature_group,response.data.group_chart_nature.DrCr,response.data.group_chart_nature.opening_balance);
                            inline_opening_sign = opening_balance >= 0 ? 'Cr' : 'Dr';
                           html+=`<tr class="left left-data editIcon table-row">
                            <td style="width: 1%;  border: 1px solid #ddd;"></td>
                            <td style="width: 1%;  border: 1px solid #ddd;"></td>
                            <td style="width: 1%;  border: 1px solid #ddd;"></td>
                            <td colspan="2"  style="width: 1%;   border: 1px solid #ddd;font-size: 18px;  text-align: right;">Opening Balance :</td>
                            <td style="width: 1%; border: 1px solid #ddd; font-size: 20px; text-align: right;">
                              ${inline_opening_sign === 'Dr' ? (Math.abs(opening_balance).toFixed(amount_decimals).toLocaleString() + inline_opening_sign) : ""}
                           </td>
                           <td style="width: 1%; border: 1px solid #ddd; font-size: 20px; text-align: right;">
                            ${inline_opening_sign === 'Cr' ? (Math.abs(opening_balance).toFixed(amount_decimals).toLocaleString() + inline_opening_sign) : ""}
                           </td>
                            </tr>`;
                        }
                        let closing
                        if((response.data.group_chart_nature.nature_group==1)||(response.data.group_chart_nature.nature_group==3)){
                             closing=((opening_balance||0)+(total_debit||0))-(total_credit||0);
                             closing_blance_sign = closing >= 0 ? 'Dr' : 'Cr';
                            html+=`<tr class="left left-data editIcon table-row">
                                <td style="width: 1%;  border: 1px solid #ddd;"></td>
                                <td style="width: 1%;  border: 1px solid #ddd;"></td><td style="width: 1%;  border: 1px solid #ddd;"></td>
                                <td colspan="2" style="width: 1%; font-weight: bold; border: 1px solid #ddd;font-size: 18px;  text-align: right;">Closing Balance  :</td>
                                 <td style="width: 1%; border: 1px solid #ddd; font-size: 20px; text-align: right;">
                                   ${closing_blance_sign === "Dr" ? 
                                           (Math.abs(closing).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,') + closing_blance_sign) 
                                        : ""}
                                   </td>
                                    <td style="width: 1%; border: 1px solid #ddd; font-size: 20px; text-align: right;">
                                        ${closing_blance_sign === "Cr" ? 
                                            (Math.abs(closing).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,') + closing_blance_sign) 
                                            : ""}
                                    </td>
                                  </tr>`;
                        }else if((response.data.group_chart_nature.nature_group==2)||(response.data.group_chart_nature.nature_group==4)){
                            closing=((opening_balance||0)+(total_credit))-(total_debit);
                            closing_blance_sign = closing >= 0 ? 'Cr' : 'Dr';
                            html+=`<tr class="left left-data editIcon table-row">
                                <td style="width: 1%;  border: 1px solid #ddd;"></td>
                                <td style="width: 1%;  border: 1px solid #ddd;"></td>
                                <td style="width: 1%;  border: 1px solid #ddd;"></td>
                                <td colspan="2" style="width: 1%; font-weight: bold; border: 1px solid #ddd;font-size: 18px;  text-align: right;">Closing Balance  :</td>
                                <td style="width: 1%;  border: 1px solid #ddd; font-size: 20px; text-align: right;">
                                   ${closing_blance_sign == "Dr" ? Math.abs(closing).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,') + closing_blance_sign : ""}
                               </td>
                               <td style="width: 1%;  border: 1px solid #ddd; font-size: 20px; text-align: right;">
                                  ${closing_blance_sign == "Cr" ? Math.abs(closing).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,') + closing_blance_sign : ""}
                               </td>
                            </tr>`;
                        }

                    html+='</tbody>';
                    html+='</table>';
            $(".sd").html(html);
            get_hover();
            checkbox_check();
    }
});
    function local_store_party_ledger_get() {
        // getStorage("end_date", '.to_date');
        // getStorage("start_date", '.from_date');
        // getStorage("ledger_id", '.ledger_id');
    }

    function local_store_party_ledger_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("ledger_id", $('.ledger_id').val());
    }

    $(document).ready(function(){
     // table header fixed
      let display_height=$(window).height();
      $('.tableFixHead_ledger_group_wise_report').css('height',`${display_height-130}px`);
   });

   function checkbox_check(){
         // checking colspan table
        $(".narration_show").css("display", $(".narratiaon" ).is(':checked')==true?'':'none');
    }
</script>
</script>
@endpush
@endsection
