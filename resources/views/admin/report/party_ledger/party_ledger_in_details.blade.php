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
</style>
<link rel="stylesheet" type="text/css" href="{{asset('common_css/selectSearchable.css')}}">

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
            {{-- form-control js-example-basic-single w3-select--}}
            <select
                name="ledger_id"
                id="ledger_id"
                class="form-control js-example-basic-single ledger_id">
                @if($all==1)
                <option value="0">--All--</option>
                @endif
                {!!html_entity_decode($ledgers)!!}
            </select>
            <div class="row  m-0 p-1">
                <div class="col-md-6 m-0 p-0">
                    <label>Date From: </label>
                    <input type="text" name="from_date" class="form-control from_date setup_date fs-5" value="{{financial_end_date(date('Y-m-d'))}}" name="narratiaon">
                </div>
                <div class="col-md-6 m-0 p-0">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control to_date setup_date fs-5" value="{{financial_end_date(date('Y-m-d'))}}" name="narratiaon">
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
                    <input class="form-check-input description" type="radio" name="description" value="4" checked>
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
        </div>
        <div class="col-md-2 ">
            <div class="form-group description_show my-0 py-0">
                <p class="closing_blance  my-0 py-0" style="font-size: 18px;"></p>
                <input class="form-check-input narratiaon" type="checkbox" name="last_update" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Narration
                </label>

                 @if (Auth()->user()->user_level==1)
                    <input class="form-check-input user_info" type="checkbox" name="last_update" value="1">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        User Info
                    </label>
                @endif
                
                <input class="form-check-input remarks_check" type="checkbox" name="remarks_check" value="3">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                   Remarks
                </label>
                <input class="form-check-input inline_closing_blance" type="checkbox" name="last_update" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    In-line Closing Balance
                </label>
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
<script type="text/javascript" src="{{asset('common_js/select_searchable.js')}}"></script>

<script>
//$('#ledger_id').make_searchable();


    let is_debit_credit = $('#show_debit_credit_is').is(':checked') ? 0 : 1;
    $('.is_debit_credit').val(is_debit_credit);
    if ("{{ $from_date ?? 0 }}" != 0) {
        $('.from_date').val('{{$from_date??0}}');
    }
    if ("{{ $to_date ?? 0 }}" != 0) {
        $('.to_date').val('{{$to_date??0}}');
    }
    if ("{{ $ledger_id ?? 0 }}" != 0) {
            $('.ledger_id').val('{{$ledger_id??0}}');
            $('.ledger_id').trigger('change');
    }else{
        local_store_party_ledger_details_get();
    }

    description_check();
    $(document).ready(function() {
        $('.ledger_id').on('keyup', function() {

            description_check();
       });

        $('.ledger_id').on('change', function() {

            description_check();
        });

        // local_store_party_ledger_details_get();
        get_party_ledger_all_initial_show();

        $("#party_ledger_details_form").submit(function(e) {
            local_store_party_ledger_details_set_data();
            e.preventDefault();
            print_date();
            const fd = new FormData(this);
            let ledger_id = $(".ledger_id").val();
            let ledger_name = $('.ledger_id').find('option:selected').text();
            localStorage.setItem('ledger_name', '');
            localStorage.setItem('ledger_name', ledger_name);
            append_header();
            $(".modal").show();
            $.ajax({
                url: '{{ route("party-ledger-details-get-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    if (ledger_id == 0) {
                        get_party_ledger_val(response)
                    } else {
                        get_current_party_ledger_val(response)
                    }
                },
                error: function(data, status, xhr) {
                    Unauthorized(data.status);
                }
            });
        });
    });

    function get_party_ledger_val(response) {
        $("#tableId").find(".ledger_body").empty();
        const chunkSize = 500; // Adjust chunk size as needed
        const totalRows = response.data.length;
        let currentRow = 0;
        let row_incre = 1;

        function appendChunk() {
            let htmlFragments = [];
            // Calculate the end index for the chunk
            const endIndex = Math.min(currentRow + chunkSize, totalRows);
            let show_closing_is=$('#show_closing_is').is(':checked');
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

                if (show_closing_is) {
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
        let closing_blance_show, closing_blance_sign_show;
        let inline_closing_blance=$(".inline_closing_blance").is(':checked');
        let user_info= $(".user_info").is(':checked');
        let narratiaon=$(".narratiaon").is(':checked');
        let html = [];
        let nature_group=response?.data?.group_chart_nature?.nature_group;

        let closing_blance = 0,
            debit = 0,
            credit = 0,
            op_first_itarate = 0,
            inline_total_debit = 0,
            inline_total_credit = 0,
            closing_blance_sign,
            ledger_dr_cr;
        let remarks_check=$("input[type='checkbox'].remarks_check:checked").val();
        let description=$("input[type='radio'].description:checked").val();
        //Opening Balance
        let op_party_ledger=response?.data?.op_party_ledger[0];
        if (inline_closing_blance) {
            let inline_opening_sign;
            let DrCr=response?.data?.group_chart_nature?.DrCr;
            let opening_balance=response?.data?.group_chart_nature?.opening_balance;
            let openning_blance=openning_blance_cal(nature_group,DrCr,opening_balance);
            html.push( `<tr class="left left-data editIcon table-row">
                            <td class="th" style="width: 1%;"></td>
                            <td class="th" style="width: 1%;"></td>
                            <td class="th" style="width: 1%;"></td>
                            <td colspan="2"  class="th" style="width: 1%;font-size: 18px; text-align: right;">Opening Balance :</td>
                            <td class="th" style="width: 1%;"></td>
                            <td class="th" style="width: 1%;"></td>
                            <td class="th inline_op" style="width: 1%; font-size: 18px; text-align: right;font-family: Arial, sans-serif;">`);
            if (nature_group== 1 || nature_group == 3) {
                let op_total_debit1=parseFloat(op_party_ledger?.op_total_debit1 || 0);
                let op_total_credit1=parseFloat(op_party_ledger?.op_total_credit1 || 0);
                let debit_opening = (op_total_debit1 - op_total_credit1) + openning_blance;
                inline_opening_sign = debit_opening >= 0 ? 'Dr' : 'Cr';
                //openning blance show but current blance now
                closing_blance_show = debit_opening;
                closing_blance_sign_show = inline_opening_sign;
                html.push( `${(debit_opening ? Math.abs(debit_opening) : 0).formatBangladeshCurrencyType("accounts",'', inline_opening_sign) }</td>
                         </tr>`);
            }

            if (nature_group == 2 || nature_group == 4) {
                let op_total_credit2=parseFloat(op_party_ledger?.op_total_credit2 || 0);
                let op_total_debit2=parseFloat(op_party_ledger?.op_total_debit2 || 0);
                let credit_opning = (op_party_ledger ? (op_total_credit2 - op_total_debit2) : 0)  + openning_blance;
                inline_opening_sign = credit_opning >= 0 ? 'Cr' : 'Dr';

                //openning blance show but current blance now
                closing_blance_show = credit_opning;
                closing_blance_sign_show = inline_opening_sign;
                html.push(`${(credit_opning ? Math.abs(credit_opning) : 0).formatBangladeshCurrencyType("accounts",'',inline_opening_sign) }</td>
                       </tr>`);

            }
        }


        // ledger array
        $.each(response.data.party_ledger, function(key, v) {
            ledger_dr_cr = v.DrCr;
            debit += parseFloat(v?.debit_sum || 0);
            credit += parseFloat(v?.credit_sum || 0);
            html.push( `<tr id="${v?.tran_id},${v?.voucher_type_id}" class="left left-data editIcon table-row">
                        <td class="th"  style="width: 1%;">${(key + 1)}</td>
                        <td  class="th" style="width: 1%; font-size: 16px;">${join(new Date(v?.transaction_date), options, ' ')}</td>`);

            if (description == 1) {
                 html.push(`<td  class="th text-wrap" style="width: 1%; font-size: 16px;font-weight: bold; ">
                                ${(v?.party_name || '')}
                                ${user_info?`<div style="font-size: 12px;" class="text-wrap">${JSON.parse(v?.other_details)}</div>`:``}
                                ${narratiaon?`<div style="font-size: 12px;font-weight: normal;" class="text-wrap">${(v?.narration || '')}</div> `:``}
                            </td>
                        `);
            } else if ((description == 2)) {
                let party_name_drcr = v.party_name_debit_credit.split("__");
               html.push(`<td  class="th " style="width: 1%; font-size: 16px;" >
                            <div style="display: flex;justify-content: space-between;">
                                <p style="font-size: 16px;">${(party_name_drcr[2])} </p>
                                <p style="font-size: 16px;">
                                    ${parseFloat(party_name_drcr[0]==0 ? party_name_drcr[1] : party_name_drcr[0] )?.formatBangladeshCurrencyType("accounts",'',(party_name_drcr[3]))}
                                </p>
                            </div>
                          </td>`);
            } else if ((description == 3)) {
                let party_name_drcr = v?.party_name_debit_credit?.split("__");
                html.push(`<td  class="th " style="width: 1%; font-size: 16px; ">
                                <p style="font-weight: bold" class="text-wrap">${(v?.ledger_name ? v?.ledger_name : '')}</p>
                                <div style="display: flex;justify-content: space-between;">
                                    <p style="margin-bottom:0;padding-bottom:0;"  class="drcr">${(party_name_drcr[2] )}</p>
                                    <p style="margin-bottom:0;padding-bottom:0;font-size: 16px;"  class="drcr">
                                        ${parseFloat(party_name_drcr[0]==0 ? party_name_drcr[1] : party_name_drcr[0] )?.formatBangladeshCurrencyType("accounts",'',(party_name_drcr[3]))}
                                    </p>
                                </div>
                            </td>`);
            } else {
                 html.push(`<td class="th " style="width: 1%; font-size: 16px;font-weight: bold; ">
                <i  class="text-wrap">${ (v?.ledger_name || '')}</i>`);
            }


            // ledger
            if ((description == 4) || ((description == 5))) {
                if (response?.data?.description_ledger.hasOwnProperty(v?.tran_id)) {
                    let body = response?.data?.description_ledger[v?.tran_id]?.map(x => `
                                <tr>
                                    <td class="text-wrap drcr my-0 py-0" style="font-size: 12px;">
                                        ${x?.ledger_name||''}
                                    </td>
                                    <td style="text-align:right; font-size: 12px;"  class="drcr  my-0 py-0">
                                         ${(x?.dr_cr == "Dr" ?
                                           ((x?.debit)?.formatBangladeshCurrencyType("accounts",'', ' Dr ')  ) :
                                           (x?.credit?.formatBangladeshCurrencyType("accounts",'', ' Cr')  ))
                                       }
                                    </td>
                                   ${(remarks_check == 3 && description == 4) ? `<td style="text-align:right;font-size: 12px;"  class="drcr  my-0 py-0">
                                      ${x?.remark||''}
                                    </td>`:''}
                            </tr>`
                        );

                        if (body.length) {
                            html.push( `<table width:100% style="width:100%" class="table table-bordered">
                                            <tr >
                                                <td style="text-align:center;font-size: 12px;" class="my-0 py-0">Paticular</td>
                                                <td style="text-align:right;font-size: 12px;" class="my-0 py-0">Amount</td>
                                                ${remarks_check == 3&&description == 4?`<td style="text-align:right;font-size: 12px;" class="my-0 py-0">Remarks</td>`:''}
                                            </tr>
                                            ${body?.join('')}
                                        </table>`)
                        }
                }
            }
            // stock out
            if (description == 5) {
                if (response.data.description_stock_out.hasOwnProperty(v?.tran_id)) {
                    let body = response.data.description_stock_out[v?.tran_id]?.map(x => `
                                        <tr>
                                            <td class="text-wrap drcr my-0 py-0" style="font-size: 12px;">
                                                ${x?.product_name}
                                            </td>
                                            <td style="text-align:right; font-size: 12px;"  class="drcr  my-0 py-0">
                                                ${(x?.qty).formatBangladeshCurrencyType("quantity",x?.symbol)}
                                            </td>
                                            <td style="text-align:right; font-size: 12px;"  class="drcr  my-0 py-0">
                                                ${(x?.rate).formatBangladeshCurrencyType("rate") }
                                            </td>
                                            <td style="text-align:right;font-size: 12px;"  class="drcr  my-0 py-0">
                                                ${(x?.total).formatBangladeshCurrencyType("amount") }
                                            </td>
                                            ${remarks_check == 3?`<td style="text-align:right;font-size: 12px;"  class="drcr  my-0 py-0">
                                              ${x?.remark||''}
                                            </td>`:''}
                                        </tr>`);

                    if (body.length) {
                        html.push( `<table width:100% style="width:100%" class="table table-bordered">
                           <tr >
                                <td style="text-align:center;font-size: 12px;" class="my-0 py-0">Item</td>
                                <td style="text-align:right;font-size: 12px;" class="my-0 py-0">Qty</td>
                                <td style="text-align:right;font-size: 12px;" class="my-0 py-0">Rate</td>
                                <td style="text-align:right;font-size: 12px;" class="my-0 py-0">Total</td>
                                 ${remarks_check == 3?`<td style="text-align:right;font-size: 12px;" class="my-0 py-0">Remarks</td>`:''}
                            </tr>
                    ${body?.join('')}
                        </table>`)
                    }
                }

                // stock in
                if (response.data.description_stock_in?.hasOwnProperty(v?.tran_id)) {
                    let body = response.data.description_stock_in[v?.tran_id]?.map(x => `<tr>
                                        <td class="text-wrap drcr  my-0 py-0" style="font-size: 12px;">
                                            ${ x?.product_name}
                                        </td>
                                        <td  style="text-align:right;font-size: 12px;"  class="drcr  my-0 py-0">
                                            ${(x?.qty).formatBangladeshCurrencyType("quantity",x?.symbol)}
                                        </td>
                                        <td  style="text-align:right;font-size: 12px;" class="drcr  my-0 py-0">
                                            ${(x?.rate).formatBangladeshCurrencyType("rate") }
                                        </td>

                                        <td  style="text-align:right;font-size: 12px;"  class="drcr  my-0 py-0">
                                            ${(x?.total).formatBangladeshCurrencyType("amount")}
                                        </td>
                                         ${remarks_check == 3?`<td style="text-align:right;font-size: 12px;"  class="drcr  my-0 py-0">
                                              ${x?.remark||''}
                                            </td>`:''}
                                    </tr>`);

                    if (body.length) {
                        html.push( `<table width:100% style="width:100%" class="table table-bordered">
                           <tr >
                                <td style="text-align:center;font-size: 12px;" class="my-0 py-0">Item</td>
                                <td style="text-align:right;font-size: 12px;" class="my-0 py-0">Qty</td>
                                <td style="text-align:right;font-size: 12px;" class="my-0 py-0">Rate</td>
                                <td style="text-align:right;font-size: 12px;" class="my-0 py-0">Total</td>
                                ${remarks_check == 3?`<td style="text-align:right;font-size: 12px;" class="my-0 py-0">Remarks</td>`:''}
                            </tr>
                    ${body?.join('')}
                        </table>`)
                    }
                }
            }
            if (user_info) {
                 html.push(`<div >
                           <i style="font-size: 16px;" class="text-wrap">${JSON.parse(v.other_details)}</i>
                        </div>`);
            }
            if (narratiaon) {
                html.push( `<div>
                     <i style="font-size: 16px;" class="text-wrap">${(v?.narration || '')}</i>
                </div>`);
            }
             html.push(`</td>
                    <td class="th party_ledger_voucher" style="width: 1%; font-size: 16px;color:#0B55C4;">${redirectVoucherIdWise(v.voucher_type_id, v.tran_id,v.voucher_name)}</td>
                        <td class="th"  style="width: 1%; font-size: 16px;">${v.invoice_no}</td>
                        <td class="th" style="width: 2%;  text-align: right; font-size: 18px;font-family: Arial, sans-serif;">${(v.debit ? (v.debit_sum ? v.debit_sum.formatBangladeshCurrencyType("accounts") : '') : '') }</td>
                        <td  class="th" style="width: 2%; text-align: right; font-size: 18px;font-family: Arial, sans-serif;">${(v.credit ? (v.credit_sum ? v.credit_sum.formatBangladeshCurrencyType("accounts") : '') : '')}</td>`);

            let total_closing;
            if (nature_group == 1 || nature_group == 3) {

                if (inline_closing_blance) {

                    if (op_first_itarate == 0) {
                        // total_closing=((parseFloat(v.debit_sum||0) - parseFloat(v.credit_sum ||0)));
                        // closing_blance +=total_closing;
                        total_closing = (((parseFloat(op_party_ledger?.op_total_debit1 || 0) - parseFloat(op_party_ledger?.op_total_credit1 || 0)) + (parseFloat(v.debit_sum || 0) - parseFloat(v.credit_sum || 0)))) + +openning_blance_cal(nature_group,response.data.group_chart_nature.DrCr,response.data.group_chart_nature.opening_balance);
                        closing_blance += total_closing;
                        inline_total_credit += parseFloat(v.credit_sum || 0);
                        inline_total_debit += parseFloat(v.debit_sum || 0);
                        op_first_itarate = 1;
                    } else {
                        total_closing = ((parseFloat(v.debit_sum ? v.debit_sum : 0) - parseFloat(v.credit_sum ? v.credit_sum : 0)));
                        closing_blance += total_closing;
                        inline_total_credit += parseFloat(v.credit_sum || 0);
                        inline_total_debit += parseFloat(v.debit_sum || 0);

                    }

                    closing_blance_sign = closing_blance >= 0 ? 'Dr' : 'Cr';
                   html.push(`<td class="th"  style="width: 2%; text-align: right; font-size: 18px;font-family: Arial, sans-serif;">${(Math.abs(closing_blance) || 0).formatBangladeshCurrencyType("accounts",'',closing_blance_sign)  }</td>`);
                }
            }
            if (nature_group == 2 || nature_group == 4) {
                if (op_first_itarate == 0) {

                    total_closing = ((parseFloat(op_party_ledger?.op_total_credit2 || 0) - parseFloat(op_party_ledger?.op_total_debit2 || 0)) + (parseFloat(v.credit_sum || 0) - parseFloat(v.debit_sum || 0))) +  +openning_blance_cal(nature_group,response.data.group_chart_nature.DrCr,response.data.group_chart_nature.opening_balance);
                    closing_blance += total_closing
                    inline_total_credit += parseFloat(v.credit_sum || 0);
                    inline_total_debit += parseFloat(v.debit_sum || 0);
                    op_first_itarate = 1;
                } else {
                    total_closing = ((parseFloat(v.credit_sum || 0) - parseFloat(v.debit_sum || 0)));
                    closing_blance += total_closing;
                    inline_total_credit += parseFloat(v.credit_sum || 0);
                    inline_total_debit += parseFloat(v.debit_sum || 0);

                }
                closing_blance_sign = closing_blance >= 0 ? 'Cr' : 'Dr';
                if (inline_closing_blance) {
                   html.push(`<td class="th"  style="width: 1%;text-align: right; font-size: 18px;font-family: Arial, sans-serif;">${(Math.abs(closing_blance) || 0).formatBangladeshCurrencyType("accounts",'', closing_blance_sign) }</td>`);
                }
            }

           html.push(`</tr>`);
        });

        // Opening Balance
        if (inline_closing_blance) {
            if (closing_blance) {
                html.push(`<tr class="left left-data editIcon table-row">

                            <td colspan="5" style="width: 1%;  border: 1px solid #ddd;font-size: 18px;text-align: right;">Closing Balance  :</td>
                            <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(inline_total_debit) || 0).formatBangladeshCurrencyType("accounts")}</td>
                            <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(inline_total_credit) || 0).formatBangladeshCurrencyType("accounts")}</td>
                            <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(closing_blance) || 0).formatBangladeshCurrencyType("accounts",'', closing_blance_sign) }</td>
                        </tr>`);
                $('.closing_blance').text("Closing Blance : " + (Math.abs(closing_blance)).formatBangladeshCurrencyType("accounts",'',closing_blance_sign));
            } else {

            if(inline_total_debit||inline_total_credit){
                html.push(`<tr class="left left-data editIcon table-row">
                    <td colspan="5" style="width: 1%;  border: 1px solid #ddd;font-size: 18px;text-align: right;">Closing Balance  :</td>
                    <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(inline_total_debit) || 0).formatBangladeshCurrencyType("accounts")}</td>
                    <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(inline_total_credit) || 0).formatBangladeshCurrencyType("accounts")}</td>
                    <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(closing_blance) || 0).formatBangladeshCurrencyType("accounts",'', closing_blance_sign)}</td>
                    </tr>`);
                    $('.closing_blance').text("Closing Blance : " + (Math.abs(closing_blance)).formatBangladeshCurrencyType("accounts",'',closing_blance_sign) );
               }else{
                html.push(`<tr class="left left-data editIcon table-row">
                <td colspan="5" style="width: 1%; border: 1px solid #ddd;font-size: 18px;text-align: right;">Closing Balance  :</td>
                <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(closing_blance_sign_show == 'Dr' ?Math.abs(inline_total_debit||closing_blance_show): 0).formatBangladeshCurrencyType("accounts")}</td>
                <td style="width: 1%;  border: 1px solid #ddd;font-size: 16px; text-align: right;font-family: Arial, sans-serif;">${(closing_blance_sign_show == 'Cr' ? Math.abs(inline_total_credit||closing_blance_show):0).formatBangladeshCurrencyType("accounts")}</td>
                <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(closing_blance_show) || 0).formatBangladeshCurrencyType("accounts",'', closing_blance_sign_show) }</td>
                </tr>`);
                $('.closing_blance').text("Closing Blance : " + (Math.abs(closing_blance_show)).formatBangladeshCurrencyType("accounts",'', closing_blance_sign_show) );
               }
            }

        } else {
            let oppening_sign;
            let closing_sign;
          html.push(`<tr class="left left-data editIcon table-row">

                      <td colspan="5" style="width: 1%; border: 1px solid #ddd;font-size: 18px; text-align: right;">Current Total  :</td>
                      <td style="width: 1%;  border: 1px solid #ddd; font-size: 18px; text-align: right;font-family: Arial, sans-serif; ">${(debit).formatBangladeshCurrencyType("accounts")}</td>
                      <td style="width: 1%;  border: 1px solid #ddd; font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(credit).formatBangladeshCurrencyType("accounts")}</td>
                    </tr>`);

            if (nature_group == 1 || nature_group == 3) {
                let debit_opening_blance = (op_party_ledger ? (parseFloat(op_party_ledger.op_total_debit1 || 0) - parseFloat(op_party_ledger.op_total_credit1 || 0)) : 0) + openning_blance_cal(nature_group,response.data.group_chart_nature.DrCr,response.data.group_chart_nature.opening_balance);
                oppening_sign = debit_opening_blance >= 0 ? 'Dr' : 'Cr';

                html.push(`<tr class="left left-data editIcon table-row">

                          <td colspan="5" style="width: 1%;   border: 1px solid #ddd;font-size: 18px; text-align: right;">Opening Balance :</td>
                          <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(oppening_sign == 'Dr' ? (Math.abs(debit_opening_blance)).formatBangladeshCurrencyType("accounts") : '')}</td>
                          <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${ (oppening_sign == 'Cr' ? (Math.abs(debit_opening_blance)).formatBangladeshCurrencyType("accounts") : '') }</td>
                        </tr>`);

                let closing_bance_debit = ((debit_opening_blance + parseFloat(debit || 0)) - (credit || 0));
                closing_sign = closing_bance_debit >= 0 ? 'Dr' : 'Cr';

                 html.push(`<tr class="left left-data editIcon table-row">

                            <td colspan="5" style="width: 1%; border: 1px solid #ddd;font-size: 18px;text-align: right;">Closing Balance  :</td>
                            <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(closing_sign == 'Dr' ? (Math.abs(closing_bance_debit)).formatBangladeshCurrencyType("accounts") : '')}</td>
                            <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(closing_sign == 'Cr' ? (Math.abs(closing_bance_debit)).formatBangladeshCurrencyType("accounts") : '')}</td>
                        </tr>`);

                $('.closing_blance').text("Closing Blance : " + (Math.abs(closing_bance_debit)).formatBangladeshCurrencyType("accounts",'',closing_sign));

            }
            if (nature_group == 2 || nature_group == 4) {
                let credit_opning_blance = (op_party_ledger ? (parseFloat(op_party_ledger.op_total_credit2 || 0) - parseFloat(op_party_ledger.op_total_debit2 || 0)) : 0) + openning_blance_cal(nature_group,response.data.group_chart_nature.DrCr,response.data.group_chart_nature.opening_balance);
                oppening_sign = credit_opning_blance >= 0 ? 'Cr' : 'Dr';
                 html.push(`<tr class="left left-data editIcon table-row">

                          <td colspan="5"  style="width: 1%;   border: 1px solid #ddd;font-size: 18px;  text-align: right;">Opening Balance :</td><td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(oppening_sign == 'Dr' ? (Math.abs(credit_opning_blance)).formatBangladeshCurrencyType("accounts") : '')}</td>
                          <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(oppening_sign == 'Cr' ? (Math.abs(credit_opning_blance)).formatBangladeshCurrencyType("accounts") : '')}</td>
                        </tr>`);

                let closing_blance_credit = ((credit_opning_blance + (credit || 0)) - (debit || 0));
                closing_sign = closing_blance_credit >= 0 ? 'Cr' : 'Dr';
                 html.push(`<tr class="left left-data editIcon table-row">
                          <td colspan="5" style="width: 1%; border: 1px solid #ddd;font-size: 18px;  text-align: right;">Closing Balance  :</td>
                          <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(closing_sign == 'Dr' ? (Math.abs(closing_blance_credit)).formatBangladeshCurrencyType("accounts") : '')}</td>
                          <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(closing_sign == 'Cr' ? (Math.abs(closing_blance_credit)).formatBangladeshCurrencyType("accounts") : '')}</td>
                        </tr>`);


                $('.closing_blance').text("Closing Blance : " + (Math.abs(closing_blance_credit)).formatBangladeshCurrencyType("accounts",'',closing_sign) );

            }
        }
        $("#tableId").find(".qw").html(html.join(''));
        get_hover();
    }

    function get_party_ledger_all_initial_show() {

        $(".modal").show();
        let ledger_id = $(".ledger_id").val();
        let ledger_name = $('.ledger_id').find('option:selected').text();
            localStorage.setItem('ledger_name', '');
            localStorage.setItem('ledger_name', ledger_name);
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
        getStorage("remarks_check", '.remarks_check', 'checkbox');
    }

    function local_store_party_ledger_details_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("ledger_id", $('.ledger_id').val());
        setStorage("sort_by", $(".sort_by:checked").val());
        setStorage("stort_type", $(".stort_type:checked").val());
        setStorage("description", $(".description:checked").val());
        setStorage("narratiaon", $('.narratiaon').is(':checked'));
        setStorage("remarks_check", $('.remarks_check').is(':checked'));
    }

    function append_header() {
        if ($(".ledger_id").val() == 0) {
            $("#tableId").html(`<thead>
                        <tr>
                            <th class="th" style="width: 1%;;">SL.</th>
                            <th class="th" style="width: 3%;">Particulars</th>
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
            let inline_closing_blance=$(".inline_closing_blance").is(':checked');
            $("#tableId").html(`<thead>
                            <tr>
                                <th class="th" style="width: 1%;">SL.</th>
                                <th class="th" style="width: 1%;">Date</th>
                                <th class="th" style="width: 5%;">Particulars</th>
                                <th class="th" style="width: 1%;">Voucher Type</th>
                                <th class="th" style="width: 1%;" >Voucher No</th>
                                <th class="th" style="width: 2%;" >Debit</th>
                                <th class="th" style="width: 2%;" >Credit</th>
                                ${inline_closing_blance?'<th style="width: 2%;" >Blance</th>':""}
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
</script>
@endpush
@endsection
