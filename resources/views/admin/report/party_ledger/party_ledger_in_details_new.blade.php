@extends('layouts.backend.app')
@section('title','Party Ledger Details')
@push('css')
<style>
    .th {
        border: 1px solid #ddd;
    }

    body {
        overflow: auto !important;
    }

    .drcr {
        font-family: Arial, sans-serif;
        font-weight: 500;
    }

    .card .card-block p {
        line-height: 18px !important;
    }



    .ledger-list {
        list-style: none;
        padding: 0;
        margin: 10px;
        border: 1px solid #ddd;
        font-size: 11px;
        border-collapse: collapse;
    }

    .ledger-list li {
        display: flex;
        border-bottom: 1px solid #ccc;
        align-items: stretch; /* This is key for column height */
        padding: 0;
    }

    .ledger-header {
        font-weight: bold;
        background-color: #f9f9f9;
    }

    /* Common column styles */
    .col-particular,
    .col-remarks {
        display: flex;
        align-items: center;        /* Vertical alignment */
        box-sizing: border-box;
        /* padding: 4px 6px; */
        white-space: pre-wrap;      /* Wrap text with preserved line breaks */
        word-break: break-word;     /* Allow breaking inside long words */
        min-height: 100%;           /* Ensure full height for border */
    }
    .col-amount{
        display: flex;
        align-items: center;        /* Vertical alignment */
        box-sizing: border-box;
        min-height: 100%;           /* Ensure full height for border */
    }

    /* Column-specific styling */
    .col-particular {
        flex: 2;
        border-right: 1px solid #ccc;
        padding:1px 2px;
    }

    .col-amount {
        flex: 1;
        justify-content: flex-end;
        text-align: right;
        border-right: 1px solid #ccc;
        padding:1px 2px;
    }

    .col-remarks {
        flex: 2;
        justify-content: flex-end;
        text-align: right;
        padding:1px 2px;
    }
</style>
{{-- <link rel="stylesheet" type="text/css" href="{{asset('common_css/selectSearchable.css')}}"> --}}

@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
'id' =>'exampleModal',
'class' =>'modal fade',
'size'=>'modal-xl',
'page_title'=>'Party Ledger Details',
'page_unique_id'=>11,
'ledger'=>'yes',
'accounts'=>"Yes",
'title'=>'Party Ledger Details',
'daynamic_function'=>'get_party_ledger_all_initial_show',
])
@endcomponent
<!-- add component-->
@component('components.report', [
'title' => 'Party Ledger Details',
'print_layout'=>'portrait',
'print_header'=>'Party Ledger Details',
'user_privilege_title'=>'PartyLedgeDetails',
'print_date'=>1,
'party_name'=>1,
'report_setting_model'=>'report_setting_model',
'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
<form id="party_ledger_details_form" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row ">
        <div class="col-md-4">
            <label>Party Name : </label>
            @component('components.party_ledger_auto_completed', [
                'party_ledger'=>'',
                'ledger_id'=>''
            ])
            @endcomponent
            
            <div class="row  m-0 p-1">
                <div class="col-md-6 m-0 p-0">
                    <label>Date From: </label>
                    <input type="text" name="from_date" class="form-control from_date setup_date fs-5" value="{{financial_end_date(date('Y-m-d'))}}">
                </div>
                <div class="col-md-6 m-0 p-0">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control to_date setup_date fs-5" value="{{financial_end_date(date('Y-m-d'))}}">
                </div>
            </div>
        </div>

        <div class="col-md-6 ">
            <label></label>
            <div class="form-group ">
                <div class="description_show">
                    <label class="fs-6">Description : </label>
                    <input class="form-check-input description" type="radio" name="description" value="1">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        None
                    </label>
                    <input class="form-check-input description" type="radio" name="description" value="2">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Summary
                    </label>
                    <input class="form-check-input description" type="radio" name="description" value="3">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Dr Cr
                    </label>
                    <input class="form-check-input description description_Ledger" type="radio" name="description" value="4" checked>
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Ledger
                    </label>
                    <input class="form-check-input description" type="radio" name="description" value="5">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Stock Item Summary
                    </label>

                </div>

                <label class="fs-6" style="min-width: 95px;">SORT by :</label>
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
                <input class="form-check-input last_update sort_by" type="radio" name="sort_by" value="4">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Dr Cr Combine
                </label><br>
                <label class="fs-6" style="min-width: 95px;">SORT type :</label>
                <input class="form-check-input sort_type" type="radio" name="sort_type" value="1" checked="checked">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    A to Z
                </label>
                <input class="form-check-input sort_type" type="radio" name="sort_type" value="2">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Z to A
                </label>
                <input type="hidden" name="is_debit_credit" class="is_debit_credit">
            </div>
            <div class="form-group ">
                <input class="form-check-input ref_number_check" id="ref_number_check" type="checkbox" name="ref_number_check" value="4">
                <label class="form-check-label fs-6" for="ref_number_check">
                   Ref Number
                </label>                
                <input class="form-check-input narratiaon" type="checkbox" name="last_update" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Narration
                </label>
                <input class="form-check-input remarks_check" type="checkbox" name="remarks_check" value="3">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                   Remarks
                </label>
            </div>
        </div>
        <div class="col-md-2 ">
            <div class="form-group description_show my-0 py-0">
                <p class="closing_blance  my-0 py-0" style="font-size: 18px;"></p>
                <input class="form-check-input inline_closing_blance" type="checkbox" name="last_update" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    In-line Closing Balance
                </label>
                 @if (Auth()->user()->user_level==1)
                    <input class="form-check-input user_info" type="checkbox" name="last_update" value="1">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        User Info
                    </label>
                @endif
                
                
                
            </div>

            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit m-2" style=" width:200px; margin-bottom:5px;"><span class="m-1 m-t-1"></span><span>Search</span></button>
        </div>
    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_report_party_ledger">
    <table id="tableId" style=" border-collapse: collapse;" class="table table-striped customers">
    </table>
</div>
@endslot
@endcomponent
<br>
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>

<script>
    $(document).ready(function() {
        // $('#ledger_id').make_searchable();
        let is_debit_credit = $('#show_debit_credit_is').is(':checked') ? 0 : 1;
        $('.is_debit_credit').val(is_debit_credit);
        if ("{{ $from_date ?? 0 }}" != 0) {
            $('.from_date').val('{{$from_date??0}}');
        }
        if ("{{ $to_date ?? 0 }}" != 0) {
            $('.to_date').val('{{$to_date??0}}');
        }

        if ("{{ $ledger_id ?? 0 }}"  >= '0') {
            $('.ledger_id').val('{{$ledger_id??""}}').trigger('change');
            $(".sort_by[value='" + {{ $sort_by??1}}+ "']").prop("checked", true);
            $(".sort_type[value='" + {{ $stort_type??1 }}+ "']").prop("checked", true);
            $(".description[value='" +{{ $description??1 }}+ "']").prop("checked", true);
            $('.narratiaon').prop('checked', {{ $narratiaon??false }});
            $('.remarks_check').prop('checked', {{$remarks??false }});
             $('.user_info').prop('checked', {{$user_info??false }});
             $('.inline_closing_blance').prop('checked', {{$inline_closing_blance??false }});
             $('.ref_number_check').prop('checked', {{$ref_number??false }});
            $('.ledger_id').trigger('change');
        }else{
            // local_store_party_ledger_details_get();
        }
        description_check();
        $('.party_ledger').on('keyup', function() {

            description_check();
            $('.description_Ledger').prop('checked', true)
       });

        $('.ledger_id').on('change', function() {

            description_check();
            $('.description_Ledger').prop('checked', true)
        });

        get_party_ledger_all_initial_show();

        $("#tableId").on('click','.hide-data',function(){
            $(this).addClass('d-none');
            $(this).closest('td').find('.show-data').removeClass('d-none');
            $(this).closest('tr').addClass('d-print-none');
            $(this).closest('tr').find('td').not($(this).parent('td')).hide();
            totalCalculate();
        });
        $("#tableId").on('click','.show-data',function(){
            $(this).addClass('d-none');
            $(this).closest('td').find('.hide-data').removeClass('d-none');
            $(this).closest('tr').removeClass('d-print-none');
            $(this).closest('tr').find('td').show();
            totalCalculate();
        });
        $("#tableId").on('click','.show-all-data',function(){
            $("#tableId").find(".qw .d-print-none").each(function(){
                $(this).removeClass('d-print-none');
                $(this).find('td').show();
                $(this).find('.show-data').addClass('d-none');
                $(this).find('.hide-data').removeClass('d-none');
            });
            totalCalculate();
        });

        $("#party_ledger_details_form").submit(function(e) {
            reset_scroll_height();
            local_store_party_ledger_details_set_data();
            e.preventDefault();
            print_date();
            let ledger_id = $(".ledger_id").val()||0;
            let ledger_name = $('.ledger_id').find('option:selected').text();
            localStorage.setItem('ledger_name', '');
            localStorage.setItem('ledger_name', ledger_name);
            let form_date=$('.from_date').val();
            let to_date=$('.to_date').val();
            let sort_by=$(".sort_by:checked").val()||1;
            let sort_type = $(".sort_type:checked").val()||1
            let description= $(".description:checked").val()||1;
            let narratiaon=$('.narratiaon').is(':checked')||false;
            let remarks=$('.remarks_check').is(':checked')||false;
            let user_info=$('.user_info').is(':checked')||false;
            let inline_closing_blance=$('.inline_closing_blance').is(':checked')||false;
            url = "{{route('party-ledger-id-wise-details-search', ['ledger_id' =>':ledger_id', 'form_date' =>':form_date','to_date' =>':to_date','sort_by' =>':sort_by','sort_type' =>':sort_type','description' =>':description','narratiaon' =>':narratiaon','remarks' =>':remarks','user_info' =>':user_info','inline_closing_blance' =>':inline_closing_blance'])}}";
            url = url.replace(':ledger_id',ledger_id);
            url = url.replace(':form_date',form_date);
            url = url.replace(':to_date',to_date);
            url = url.replace(':sort_by',sort_by);
            url = url.replace(':sort_type',sort_type);
            url = url.replace(':description',description);
            url = url.replace(':narratiaon',narratiaon);
            url = url.replace(':remarks',remarks);
            url = url.replace(':user_info',user_info);
            url = url.replace(':inline_closing_blance',inline_closing_blance);

            window.location.href=url;
        });

    });

    function totalCalculate(){
        let debit = 0;
        let credit = 0;
        let accountsDebit=0;
        let accountsCredit=0;
        
        $("#tableId").find(".qw tr").each(function(){
            if(!$(this).hasClass('d-print-none')) {
                debit += parseFloat($(this).find('.debit').val()) || 0;
                credit += parseFloat($(this).find('.credit').val()) || 0;
                accountsDebit += parseFloat($(this).find('.accountsDebit').val()) || 0;
                accountsCredit += parseFloat($(this).find('.accountsCredit').val()) || 0;
            }
        });
        $('#total_debit').text(debit?.formatBangladeshCurrencyType("accounts"));
        $('#total_credit').text(credit?.formatBangladeshCurrencyType("accounts"));

        let debit_opening=parseFloat($('#debit_opening').val()||0);
        let credit_opening=parseFloat($('#credit_opening').val()||0);

        let total_debit=parseFloat(debit)+parseFloat(debit_opening);
        let total_credit=parseFloat(credit)+parseFloat(credit_opening);
        if($(".inline_closing_blance").is(':checked')){
            $("#closing_debit").text(total_debit.formatBangladeshCurrencyType("accounts"));
            $("#closing_credit").text(total_credit.formatBangladeshCurrencyType("accounts"))
            if(total_debit>total_credit){
                let closing_debit=(total_debit-total_credit).formatBangladeshCurrencyType("accounts");
                $(".closing_blance").text(`Closing Blance : ${closing_debit}Dr`);
                $("#closing_amount").text(closing_debit+"Dr")
            }else{
                let closing_credit=(total_credit-total_debit).formatBangladeshCurrencyType("accounts");
                $(".closing_blance").text(`Closing Blance : ${closing_credit}Cr`);
                $("#closing_amount").text(closing_credit+"Cr")
            }
        }else{
            if(total_debit>total_credit){
                let closing_debit=(total_debit-total_credit).formatBangladeshCurrencyType("accounts");
                $("#closing_debit").text(closing_debit);
                $("#closing_credit").text('')
                $(".closing_blance").text(`Closing Blance : ${closing_debit}Dr`);
                // $("#closing_amount").text(closing_debit+"Dr")
            }else{
                let closing_credit=(total_credit-total_debit).formatBangladeshCurrencyType("accounts");
                $("#closing_credit").text(closing_credit);
                $("#closing_debit").text('');
                $(".closing_blance").text(`Closing Blance : ${closing_credit}Cr`);
                // $("#closing_amount").text(closing_credit+"Cr")
            }
        }

            
        
    }


    

    function get_party_ledger_val(response) {
        $("#tableId").find(".ledger_body").empty();
        print_date();
        const chunkSize = 500; // Adjust chunk size as needed
        const totalRows = response.data.length;
        let currentRow = 0;
        let row_incre = 1;

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
                    balance = parseFloat(v.opening_debit_credit || 0) +  openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance);
                    sign = balance >= 0 ? 'Dr' : 'Cr';
                } else {
                    balance = parseFloat(v.opening_debit_credit) +  openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance);
                    sign = balance >= 0 ? 'Cr' : 'Dr';
                }

                const openingBalance = Math.abs(balance).formatBangladeshCurrencyType("accounts",'',sign);
                const totalDebit = (v.total_debit || 0);
                const totalCredit = (v.total_credit || 0);

                if (v.nature_group == 1 || v.nature_group == 3) {
                    closingBalance = ((parseFloat(v.opening_debit_credit || 0) + parseFloat(v.total_debit || 0)) - parseFloat(v.total_credit || 0)) +  openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance);

                    closingsign = closingBalance >= 0 ? 'Dr' : 'Cr';
                } else {
                    closingBalance = ((parseFloat(v.opening_debit_credit || 0) + parseFloat(v.total_credit || 0)) - parseFloat(v.total_debit || 0)) +  openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance);

                    closingsign = closingBalance >= 0 ? 'Cr' : 'Dr';
                }

                const currentBalance = Math.abs(closingBalance).formatBangladeshCurrencyType("accounts",'',closingsign);

                if ($('#show_closing_is').is(':checked')) {
                    if (closingBalance == 0) {} else {
                        htmlFragments.push(`<tr class="left left-data editIcon table-row">
                            <td style="width: 1%; border: 1px solid #ddd;">${row_incre++}</td>
                            <td style="width: 3%; border: 1px solid #ddd; font-size: 16px;">${v.ledger_name || ''}</td>
                            <td style="width: 3%; border: 1px solid #ddd; font-size: 18px;text-align:right;">${openingBalance}</td>
                            <td style="width: 2%; border: 1px solid #ddd; font-size: 18px;text-align:right;">${totalDebit.formatBangladeshCurrencyType("accounts")}</td>
                            <td style="width: 2%; border: 1px solid #ddd; font-size: 18px;text-align:right;">${totalCredit.formatBangladeshCurrencyType("accounts")}</td>
                            <td style="width: 3%; border: 1px solid #ddd; font-size: 18px;text-align:right;">${currentBalance}</td>
                          </tr>`);
                    }

                } else {
                    htmlFragments.push(`<tr class="left left-data editIcon table-row">
                            <td style="width: 1%; border: 1px solid #ddd;">${key + 1}</td>
                            <td style="width: 3%; border: 1px solid #ddd; font-size: 16px;">${v.ledger_name || ''}</td>
                            <td style="width: 3%; border: 1px solid #ddd; font-size: 18px;text-align:right;">${openingBalance}</td>
                            <td style="width: 2%; border: 1px solid #ddd; font-size: 18px;text-align:right;">${totalDebit.formatBangladeshCurrencyType("accounts")}</td>
                            <td style="width: 2%; border: 1px solid #ddd; font-size: 18px;text-align:right;">${totalCredit.formatBangladeshCurrencyType("accounts")}</td>
                            <td style="width: 3%; border: 1px solid #ddd; font-size: 18px;text-align:right;">${currentBalance}</td>
                        </tr>`);
                }



            }

            $("#tableId").find(".ledger_body").append(htmlFragments.join(''));
            get_hover();

            // If there are more rows, append the next chunk after a delay
            currentRow += chunkSize;
            if (currentRow < totalRows) {
                setTimeout(appendChunk, 0); // Use setTimeout to allow UI updates
            }else{
                scroll_table_to_prev();
            }
        }

        // Start appending chunks
        appendChunk();
    }



    function get_current_party_ledger_val(response) {
        let dr_cr;


        let inline_closing_blance_is_checked = $(".inline_closing_blance").is(':checked');
        let user_info = $(".user_info").is(':checked');
        let narratiaon = $(".narratiaon").is(':checked');
        let nature_group = response?.data?.group_chart_nature?.nature_group;
        let closing_blance = 0,
            debit = 0,
            credit = 0,
            op_first_itarate = 0,
            inline_total_debit = 0,
            inline_total_credit = 0,
            closing_blance_sign,
            ledger_dr_cr;
        let remarks_check = $("input[type='checkbox'].remarks_check:checked").val();
        let description = $("input[type='radio'].description:checked").val();
        let htmlOpening = [];
        let op_party_ledger = response?.data?.op_party_ledger[0];
        let DrCr = response?.data?.group_chart_nature?.DrCr;
        let opening_balance = response?.data?.group_chart_nature?.opening_balance;
        if (inline_closing_blance_is_checked) {
            let inline_closing_blance=0;
            let inline_closing_blance_sign='';
            let openning_blance = openning_blance_cal(nature_group, DrCr, opening_balance);
            if (nature_group == 1 || nature_group == 3) {
                let op_total_debit1 = parseFloat(op_party_ledger?.op_total_debit1 || 0);
                let op_total_credit1 = parseFloat(op_party_ledger?.op_total_credit1 || 0);
                inline_closing_blance = (op_total_debit1 - op_total_credit1) + openning_blance;
                inline_closing_blance_sign = inline_closing_blance >= 0 ? 'Dr' : 'Cr';
            }
            if (nature_group == 2 || nature_group == 4) {
                let op_total_credit2 = parseFloat(op_party_ledger?.op_total_credit2 || 0);
                let op_total_debit2 = parseFloat(op_party_ledger?.op_total_debit2 || 0);
                inline_closing_blance = (op_party_ledger ? (op_total_credit2 - op_total_debit2) : 0) + openning_blance;
                inline_closing_blance_sign =  inline_closing_blance >= 0 ? 'Cr' : 'Dr';
            }
            htmlOpening.push(`
                    <tr class="left left-data editIcon table-row">
                        <td class="th" style="width: 1%;"></td>
                        <td class="th" style="width: 1%;"></td>
                        <td class="th" style="width: 1%;"></td>
                        <td colspan="2" class="th" style="width: 1%;font-size: 18px; text-align: right;">Opening Balance :</td>
                        <td class="th" style="width: 1%;"></td>
                        <td class="th" style="width: 1%;"></td>
                        <td class="th inline_op" style="width: 1%; font-size: 18px; text-align: right;font-family: Arial, sans-serif;">
                            ${(Math.abs(inline_closing_blance||0)).formatBangladeshCurrencyType("accounts", '', inline_closing_blance_sign)}
                        </td>
                    </tr>`);
        }

        let $tbody = $("#tableId").find(".qw");
        $tbody.empty();
        $tbody.append(htmlOpening.join(''));
        let ledger_data = response.data.party_ledger || [];
        let chunkSize = 500;
        let chunkIndex = 0;

        function renderChunk() {
            let fragment = document.createDocumentFragment();
            for (let i = 0; i < chunkSize && chunkIndex < ledger_data.length; i++, chunkIndex++) {
                let v = ledger_data[chunkIndex];
                ledger_dr_cr = v.DrCr;
                let debit_sum = parseFloat(v?.debit_sum || 0);
                let credit_sum = parseFloat(v?.credit_sum || 0);
                debit += debit_sum;
                credit += credit_sum;
                let descHTML = '';
                let ledger_name=` <i  class="text-wrap" style="ont-size: 16px;font-weight: bold;">${ (v.ledger_name ? v.ledger_name : '')}</i>`
                let user_info_narratiaon=`
                        ${user_info ? `<div class="text-wrap" style="font-size:12px;">${JSON.parse(v?.other_details)}</div>` : ''}
                        ${narratiaon ? `<div class="text-wrap" style="font-size:12px;">${v?.narration || ''}</div>` : ''}`;
                if (description == 1) {
                    descHTML = ledger_name+user_info_narratiaon;
                } else if (description == 2) {
                    let parts = v.party_name_debit_credit.split("__");
                    descHTML =`${ledger_name}
                        <div style="display:flex;justify-content:space-between;">
                            <p>${parts[2]}</p>
                            <p>${parseFloat(parts[0] == 0 ? parts[1] : parts[0]).formatBangladeshCurrencyType("accounts", '', parts[3])}</p>
                        </div>
                        ${user_info_narratiaon}
                    `;
                } else if (description == 3) {
                    let parts = v.party_name_debit_credit?.split("__");
                    descHTML = `
                        ${ledger_name}
                            <div style="display:flex;justify-content:space-between;">
                                <p class="drcr">${parts[2]}</p>
                                <p class="drcr">${parseFloat(parts[0] == 0 ? parts[1] : parts[0]).formatBangladeshCurrencyType("accounts", '', parts[3])}</p>
                            </div>
                            ${user_info_narratiaon}
                        `;
                }
                else if (description == 4 || description == 5) {
                    let rows = '';
                    if (response?.data?.description_ledger?.[v.tran_id]) {
                        const entries = response.data.description_ledger[v.tran_id];
                        // rows = entries.map(x => `
                        //     <tr>
                        //         <td class="text-wrap drcr my-0 py-0" style="font-size: 12px;">${x?.ledger_name || ''}</td>
                        //         <td style="text-align:right; font-size: 12px;" class="drcr my-0 py-0">${
                        //             x?.dr_cr === 'Dr'
                        //             ? (x?.debit)?.formatBangladeshCurrencyType("accounts", '', ' Dr')
                        //             : (x?.credit)?.formatBangladeshCurrencyType("accounts", '', ' Cr')
                        //         }</td>
                        //         ${
                        //             (remarks_check == 3 && description == 4)
                        //             ? `<td style="text-align:right; font-size: 12px;" class="drcr my-0 py-0">${x?.remark || ''}</td>`
                        //             : ''
                        //         }
                        //     </tr>
                        // `).join('');
                        // if (rows) {
                        //     descHTML = ` ${ledger_name}
                        //         <table class="table table-bordered" style="width:100%">
                        //             <tr>
                        //                 <td class="my-0 py-0" style="text-align:center; font-size:12px;">Particular</td>
                        //                 <td class="my-0 py-0" style="text-align:right; font-size:12px;">Amount</td>
                        //                 ${(remarks_check == 3 && description == 4) ? `<td class="my-0 py-0" style="text-align:right; font-size:12px;">Remarks</td>` : ''}
                        //             </tr>
                        //             ${rows}
                        //         </table>
                        //     ${user_info_narratiaon} `;
                        // }
                        rows = entries.map(x => {
                            const ledgerName = x?.ledger_name || '';
                            const amount = x?.dr_cr === 'Dr'
                                ? (x?.debit)?.formatBangladeshCurrencyType("accounts", '', ' Dr')
                                : (x?.credit)?.formatBangladeshCurrencyType("accounts", '', ' Cr');
                            const remarkColumn = (remarks_check == 3 && description == 4)
                                ? `<span class="col-remarks">${x?.remark || ''}</span>`
                                : '';

                            return `
                                <li class="ledger-row">
                                    <span class="col-particular">${ledgerName}</span>
                                    <span class="col-amount">${amount}</span>
                                    ${remarkColumn}
                                </li>
                            `;
                        }).join('');

                        if (rows) {
                            const remarksHeader = (remarks_check == 3 && description == 4)
                                ? '<span class="col-remarks">Remarks</span>'
                                : '';

                            descHTML = `
                                ${ledger_name}
                                <ul class="ledger-list" >
                                    <li class="ledger-header">
                                        <span class="col-particular">Particular</span>
                                        <span class="col-amount">Amount</span>
                                        ${remarksHeader}
                                    </li>
                                    ${rows}
                                </ul>
                                ${user_info_narratiaon}
                            `;
                        }
                    }

                    if (description == 5) {
                        let stockOut = response?.data?.description_stock_out?.[v.tran_id];
                        let stockIn = response?.data?.description_stock_in?.[v.tran_id];
                        if (stockOut) {
                            let rows = stockOut.map(x => `
                                <tr>
                                    <td style="font-size:12px;">${x?.product_name}</td>
                                    <td style="text-align:right; font-size:12px;">${(x?.qty).formatBangladeshCurrencyType("quantity", x?.symbol)}</td>
                                    <td style="text-align:right; font-size:12px;">${(x?.rate).formatBangladeshCurrencyType("rate")}</td>
                                    <td style="text-align:right; font-size:12px;">${(x?.total).formatBangladeshCurrencyType("amount")}</td>
                                    ${remarks_check == 3 ? `<td style="text-align:right; font-size:12px;">${x?.remark || ''}</td>` : ''}
                                </tr>
                            `).join('');
                            descHTML += ` ${ledger_name}
                                <table class="table table-bordered" style="width:100%">
                                    <tr>
                                        <td style="text-align:center;font-size:12px;">Item</td>
                                        <td style="text-align:right;font-size:12px;">Qty</td>
                                        <td style="text-align:right;font-size:12px;">Rate</td>
                                        <td style="text-align:right;font-size:12px;">Total</td>
                                        ${remarks_check == 3 ? `<td style="text-align:right;font-size:12px;">Remarks</td>` : ''}
                                    </tr>
                                    ${rows}
                                </table>
                                ${user_info_narratiaon}
                            `;
                        }



                        if (stockIn) {

                            let rows = stockIn.map(x => `

                                <tr>

                                    <td style="font-size:12px;">${x?.product_name}</td>

                                    <td style="text-align:right; font-size:12px;">${(x?.qty).formatBangladeshCurrencyType("quantity", x?.symbol)}</td>

                                    <td style="text-align:right; font-size:12px;">${(x?.rate).formatBangladeshCurrencyType("rate")}</td>

                                    <td style="text-align:right; font-size:12px;">${(x?.total).formatBangladeshCurrencyType("amount")}</td>

                                    ${remarks_check == 3 ? `<td style="text-align:right; font-size:12px;">${x?.remark || ''}</td>` : ''}

                                </tr>

                            `).join('');



                            descHTML += ` ${ledger_name}

                                <table class="table table-bordered" style="width:100%">

                                    <tr>

                                        <td style="text-align:center;font-size:12px;">Item</td>

                                        <td style="text-align:right;font-size:12px;">Qty</td>

                                        <td style="text-align:right;font-size:12px;">Rate</td>

                                        <td style="text-align:right;font-size:12px;">Total</td>

                                        ${remarks_check == 3 ? `<td style="text-align:right;font-size:12px;">Remarks</td>` : ''}

                                    </tr>

                                    ${rows}

                                </table>

                            ${user_info_narratiaon}`;

                        }

                    }

                }
                else{
                    descHTML = `${ledger_name}`;
                }

                let row = document.createElement("tr");
                row.className = "left left-data editIcon table-row";
                row.id = `${v?.tran_id},${v?.voucher_type_id}`;
                row.innerHTML = `
                    <td class="th">${chunkIndex + 1}</td>
                    <td class="th">${join(new Date(v?.transaction_date), options, ' ')}</td>
                    <td class="th">${descHTML}</td>
                    <td class="th party_ledger_voucher" style="color:#0B55C4;">${redirectVoucherIdWise(v.voucher_type_id, v.tran_id,v.voucher_name)}</td>
                    <td class="th">${v.invoice_no}</td>
                    <td class="th" style="width: 2%;  text-align: right; font-size: 18px;">${debit_sum ? debit_sum.formatBangladeshCurrencyType("accounts") : ''}</td>
                    <td class="th" style="width: 2%;  text-align: right; font-size: 18px;">${credit_sum ? credit_sum.formatBangladeshCurrencyType("accounts") : ''}</td>
                `;
                fragment.appendChild(row);

            }
            $tbody[0].appendChild(fragment);
            if (chunkIndex < ledger_data.length) {
                setTimeout(renderChunk, 0);
            } else {
                appendClosingBalance();
                get_hover();
            }

        }

        renderChunk();

        function appendClosingBalance() {
            let fragment = document.createDocumentFragment()
            let closingRow = document.createElement("tr");
            closingRow.className = "left left-data editIcon table-row";
            if (inline_closing_blance_is_checked) {
                const finalRow = document.createElement('tr');
                finalRow.className = 'left left-data editIcon table-row';
                finalRow.innerHTML = `
                    <td colspan="5" style="width: 1%; border: 1px solid #ddd; font-size: 18px; text-align: right;">Closing Balance :</td>
                    <td style="width: 1%; border: 1px solid #ddd; font-size: 18px; text-align: right; font-family: Arial, sans-serif;">
                        ${(Math.abs(inline_total_debit) || 0).formatBangladeshCurrencyType("accounts")}
                    </td>
                    <td style="width: 1%; border: 1px solid #ddd; font-size: 18px; text-align: right; font-family: Arial, sans-serif;">
                        ${(Math.abs(inline_total_credit) || 0).formatBangladeshCurrencyType("accounts")}
                    </td>
                    <td style="width: 1%; border: 1px solid #ddd; font-size: 18px; text-align: right; font-family: Arial, sans-serif;">
                        ${(Math.abs(closing_blance) || 0).formatBangladeshCurrencyType("accounts", '', closing_blance_sign)}
                    </td>`;
                fragment.appendChild(finalRow);
                $('.closing_blance').text("Closing Balance : " + (Math.abs(closing_blance)).formatBangladeshCurrencyType("accounts", '', closing_blance_sign));
            } else {
                const closingRow = document.createElement('tr');
                closingRow.className = 'left left-data editIcon table-row';
                closingRow.innerHTML = `
                    <td colspan="5" style="text-align:right; font-size:18px;">Current Total :</td>
                    <td style="text-align:right; font-family: Arial, sans-serif;font-size:18px;">${debit.formatBangladeshCurrencyType("accounts")}</td>
                    <td style="text-align:right; font-family: Arial, sans-serif;font-size:18px;">${credit.formatBangladeshCurrencyType("accounts")}</td>
                `;
                fragment.appendChild(closingRow);
                const openingRow = document.createElement('tr');
                openingRow.className = 'left left-data editIcon table-row';
                let openingBalance = 0;
                let closingBalance = 0;
                let sign = '';
                if (nature_group == 1 || nature_group == 3) {
                    const op_balance = (parseFloat(op_party_ledger?.op_total_debit1 || 0) - parseFloat(op_party_ledger?.op_total_credit1 || 0)) + openning_blance_cal(nature_group, DrCr, opening_balance);
                    openingBalance = op_balance;
                    sign = op_balance >= 0 ? 'Dr' : 'Cr';
                    openingRow.innerHTML = `
                        <td colspan="5" style="text-align:right; font-size:18px;">Opening Balance :</td>
                        <td style="text-align:right;font-size:18px; font-family: Arial, sans-serif;">${sign === 'Dr' ? Math.abs(op_balance).formatBangladeshCurrencyType("accounts") : ''}</td>
                        <td style="text-align:right;font-size:18px; font-family: Arial, sans-serif;">${sign === 'Cr' ? Math.abs(op_balance).formatBangladeshCurrencyType("accounts") : ''}</td>
                    `;
                    fragment.appendChild(openingRow);
                    closingBalance = (op_balance + debit - credit);
                }
                if (nature_group == 2 || nature_group == 4) {
                    const op_balance = (parseFloat(op_party_ledger?.op_total_credit2 || 0) - parseFloat(op_party_ledger?.op_total_debit2 || 0)) + openning_blance_cal(nature_group, DrCr, opening_balance);
                    openingBalance = op_balance;
                    sign = op_balance >= 0 ? 'Cr' : 'Dr';
                    openingRow.innerHTML = `
                        <td colspan="5" style="text-align:right; font-size:18px;">Opening Balance :</td>
                        <td style="text-align:right;font-size:18px; font-family: Arial, sans-serif;">${sign === 'Dr' ? Math.abs(op_balance).formatBangladeshCurrencyType("accounts") : ''}</td>
                        <td style="text-align:right;font-size:18px; font-family: Arial, sans-serif;">${sign === 'Cr' ? Math.abs(op_balance).formatBangladeshCurrencyType("accounts") : ''}</td>
                    `;
                    fragment.appendChild(openingRow);
                    closingBalance = (op_balance + credit - debit);
                }
                const finalRow = document.createElement('tr');
                finalRow.className = 'left left-data editIcon table-row';
                const closingSign = closingBalance >= 0 ? (nature_group == 1 || nature_group == 3 ? 'Dr' : 'Cr') : (nature_group == 1 || nature_group == 3 ? 'Cr' : 'Dr');
                finalRow.innerHTML = `
                    <td colspan="5" style="text-align:right; font-size:18px;">Closing Balance :</td>
                    <td style="text-align:right;font-size:18px; font-family: Arial, sans-serif;">${closingSign === 'Dr' ? Math.abs(closingBalance).formatBangladeshCurrencyType("accounts") : ''}</td>
                    <td style="text-align:right;font-size:18px; font-family: Arial, sans-serif;">${closingSign === 'Cr' ? Math.abs(closingBalance).formatBangladeshCurrencyType("accounts") : ''}</td>
                `;
                fragment.appendChild(finalRow);
                $('.closing_blance').text("Closing Balance : " + Math.abs(closingBalance).formatBangladeshCurrencyType("accounts", '', closingSign));
            }
            fragment.appendChild(closingRow);
            $tbody[0].appendChild(fragment);
        }

    }
    function get_party_ledger_all_initial_show() {
        if($(".ledger_id").val()=='')return ;
        $(".modal").show();
        let ledger_id = $(".ledger_id").val();
        append_header();
        print_date();
        $.ajax({
            url: '{{ route("party-ledger-details-get-data") }}',
            method: 'GET',
            data: {
                to_date: $('.to_date').val(),
                from_date: $('.from_date').val(),
                ledger_id: $(".ledger_id").val(),
                is_debit_credit: $('#show_debit_credit_is').is(':checked') ? 0 : 1,
                sort_by: $(".sort_by:checked").val(),
                stort_type: $(".stort_type:checked").val(),
                description: $(".description:checked").val(),
            },
            dataType: 'json',
            success: function(response) {
                $(".modal").hide();
                if ($('.ledger_id').val() == 0) {
                    get_party_ledger_val(response)
                } else {
                    get_current_party_ledger_val(response)
                }
            },
            error: function(data, status, xhr) {
                Unauthorized(data.status);
            }
        });

    }

    function local_store_party_ledger_details_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("ledger_id", '.ledger_id');
        let sort_by = getStorage("sort_by");
        $(".sort_by[value='" + sort_by + "']").prop("checked", true);
        let stort_type = getStorage("stort_type");
        $(".stort_type[value='" + stort_type + "']").prop("checked", true);
        let description = getStorage("description");
        $(".description[value='" + description + "']").prop("checked", true);
        getStorage("narratiaon", '.narratiaon', 'checkbox');
        getStorage("ref_number_check", '.ref_number_check', 'checkbox');
        getStorage("remarks_check", '.remarks_check', 'checkbox');
    }

    function local_store_party_ledger_details_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("ledger_id", $('.ledger_id').val());
        setStorage("ledger_name", $('.party_ledger').val());
        setStorage("sort_by", $(".sort_by:checked").val());
        setStorage("stort_type", $(".stort_type:checked").val());
        setStorage("description", $(".description:checked").val());
        setStorage("narratiaon", $('.narratiaon').is(':checked'));
        setStorage("ref_number_check", $('.ref_number_check').is(':checked'));
        setStorage("remarks_check", $('.remarks_check').is(':checked'));
    }

    function append_header() {
        if ($(".ledger_id").val() == 0) {
            $("#tableId").html(`<thead>
                        <tr>
                            <th class="th" style="width: 1%;;">SL.</th>
                            <th class="th" style="width: 3%;">
                                Particulars 
                            </th>
                            <th class="th" style="width: 2%;text-align:right;">Opening Balance</th>
                            <th class="th" style="width: 3%;text-align:right;">Debit</th>
                            <th class="th" style="width: 2%;text-align:right;">Credit</th>
                            <th class="th" style="width: 3%;text-align:right;" class="closing_checkbox">Current Balance</th>
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
                            <th class="th" style="width: 3%;text-align:right;" class="closing_checkbox">Current Balance</th>
                        </tr>
                    </tfoot>`);
        } else {
            $("#tableId").html(`<thead>
                            <tr>
                                <th class="th" style="width: 1%;">SL.</th>
                                <th class="th" style="width: 1%;">Date</th>
                                <th class="th" style="width: 5%;">
                                    Particulars <button class="show-all-data m-0 p-0 px-1 rounded bg-info fw-bold text-black opacity-25 d-print-none">Show All</button>
                                </th>
                                <th class="th" style="width: 1%;">Voucher Type</th>
                                <th class="th" style="width: 1%;" >Voucher No</th>
                                <th class="th" style="width: 2%;" >Debit</th>
                                <th class="th" style="width: 2%;" >Credit</th>
                                ${$(".inline_closing_blance").is(':checked')?'<th style="width: 2%;" >Blance</th>':""}
                            </tr>
                        </thead>
                        <tbody id="myTable" class="qw">
                        </tbody>`);
        }
    }

    function description_check() {
        if ($(".ledger_id").val() == 0) {
            $(".description_show").addClass("d-none");
            $('.sort_by_particular').removeClass("d-none");
        } else {
            $(".description_show").removeClass("d-none");
            $('.sort_by_particular').addClass("d-none");
        }
    }

    // table header fixed
    let display_height = $(window).height();
    $('.tableFixHead_report_party_ledger').css('height', `${display_height-110}px`);
    ///redirect route
    $(document).ready(function() {
        $('.sd').on('click', '.party_ledger_voucher', function(e) {

            localStorage.setItem("end_date", $('.to_date').val());
            localStorage.setItem("start_date", $('.from_date').val());
            localStorage.setItem("voucher_id", $('.voucher_id').val());
            // e.preventDefault();
            // redirectVoucher($(this).closest('tr').attr('id').split(","));
        })
    });
</script>
@endpush
@endsection
