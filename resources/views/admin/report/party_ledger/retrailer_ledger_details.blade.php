@extends('layouts.backend.app')
@section('title','Retrailer Ledger Details')
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
@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
'id' =>'exampleModal',
'class' =>'modal fade',
'size'=>'modal-xl',
'page_title'=>'Retrailer Ledger Details',
'page_unique_id'=>12,
'ledger'=>'yes',
'accounts'=>"Yes",
'title'=>'Retrailer',
'daynamic_function'=>'get_retrailer_ledger_all_initial_show',
])
@endcomponent
<!-- add component-->
@component('components.report', [
'title' => 'Retrailer Ledger Details',
'print_layout'=>'portrait',
'print_header'=>'Retrailer Ledger Details',
'user_privilege_title'=>'RetrailerLedgerDetails',
'print_date'=>1,
'party_name'=>1,
'report_setting_model'=>'report_setting_model',
'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
<form id="retrailer_ledger_details_form" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row ">
        <div class="col-md-4">
            <label>Party Name : </label>
            <select name="ledger_id" class="form-control js-example-basic-single ledger_id" required>
                <option value="">--select--</option>
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
                </label><br>

                <label class="fs-6">Ledger type :</label>
                <input class="form-check-input ledger_type" type="radio" name="ledger_type" value="1" checked="checked">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Both
                </label>
                <input class="form-check-input ledger_type" type="radio" name="ledger_type" value="2">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Dealer
                </label>
                <input class="form-check-input ledger_type" type="radio" name="ledger_type" value="3">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Retrailer
                </label>
            </div>
        </div>
        <div class="col-md-2 ">
            <div class="form-group description_show my-0 py-0">
                <p class="closing_blance  my-0 py-0" style="font-size: 18px;"></p>
                <input class="form-check-input narratiaon" type="checkbox" name="last_update" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Narration
                </label>
                <input class="form-check-input user_info" type="checkbox" name="last_update" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    User Info
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
<script>
    // table header fixed
    let display_height = $(window).height();
    $('.tableFixHead_report_party_ledger').css('height', `${display_height-110}px`);
    let is_debit_credit = $('#show_debit_credit_is').is(':checked') ? 0 : 1;
    $('.is_debit_credit').val(is_debit_credit);

    $(document).ready(function() {
        description_check();
        $('.ledger_id').on('change', function() {
            description_check();
        });

        local_store_retrailer_ledger_details_get();
        get_retrailer_ledger_all_initial_show();

        $("#retrailer_ledger_details_form").submit(function(e) {
            local_store_retrailer_ledger_details_set_data();
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
                url: '{{ route("report-ledger-details-retrailer-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                     get_current_retrailer_ledger_val(response)
                },
                error: function(data, status, xhr) {

                }
            });
        });
    });


    function get_current_retrailer_ledger_val(response) {
        let dr_cr;
        let closing_blance_show, closing_blance_sign_show;

        let html = [];
        //Opening Balance
        if ($(".inline_closing_blance").is(':checked')) {
            let inline_opening_sign;
            if ((response.data.group_chart_nature.nature_group == 1) || (response.data.group_chart_nature.nature_group == 3)) {
                let debit_opening = (response.data.op_party_ledger[0] ? (parseFloat(response.data.op_party_ledger[0].op_total_debit1 ? response.data.op_party_ledger[0].op_total_debit1 : 0) - parseFloat(response.data.op_party_ledger[0].op_total_credit1 ? response.data.op_party_ledger[0].op_total_credit1 : 0)) : 0) + (response.data.group_chart_nature.opening_balance || 0);
                inline_opening_sign = debit_opening >= 0 ? 'Dr' : 'Cr';
                //openning blance show but current blance now
                closing_blance_show = debit_opening;
                closing_blance_sign_show = inline_opening_sign;
                html.push( `<tr class="left left-data editIcon table-row">
                            <td class="th" style="width: 1%;"></td>
                            <td class="th" style="width: 1%;"></td>
                            <td class="th" style="width: 1%;"></td>
                            <td class="th" style="width: 1%;"></td>
                            <td colspan="2"  class="th" style="width: 1%;font-size: 18px; text-align: right;">Opening Balance :</td>
                            <td class="th" style="width: 1%;"></td>
                            <td class="th" style="width: 1%;"></td>
                            <td style="width: 1%; font-size: 18px; text-align: right;font-family: Arial, sans-serif;" class="th  inline_op">${(debit_opening ? Math.abs(debit_opening) : 0).formatBangladeshCurrencyType("accounts",'',inline_opening_sign) }</td>
                         </tr>`);

            }

            if ((response.data.group_chart_nature.nature_group == 2) || (response.data.group_chart_nature.nature_group == 4)) {
                let credit_opning = (response.data.op_party_ledger[0] ? (parseFloat(response.data.op_party_ledger[0].op_total_credit2 ? response.data.op_party_ledger[0].op_total_credit2 : 0) - parseFloat(response.data.op_party_ledger[0].op_total_debit2 ? response.data.op_party_ledger[0].op_total_debit2 : 0)) : 0) + (response.data.group_chart_nature.opening_balance || 0);
                inline_opening_sign = credit_opning >= 0 ? 'Cr' : 'Dr';

                //openning blance show but current blance now
                closing_blance_show = credit_opning;
                closing_blance_sign_show = inline_opening_sign;
                html.push(`<tr class="left left-data editIcon table-row">
                            <td class="th" style="width: 1%; "></td>
                            <td class="th" style="width: 1%; "></td>
                            <td class="th" style="width: 1%; "></td>
                            <td class="th" style="width: 1%;"></td>
                            <td class="th" style="width: 1%;"></td>
                            <td text-aline  colspan="2" class="th" style="width: 1%; font-size: 18px; text-align: right;">Opening Balance :</td><td style="width: 1%;" class="th"></td>
                            <td style="width: 1%;" class="th"></td>
                            <td class="th inline_op" style="width: 1%; font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(credit_opning ? Math.abs(credit_opning) : 0).formatBangladeshCurrencyType("accounts",'',inline_opening_sign)}</td>
                       </tr>`);

            }
        }
        let closing_blance = 0,
            debit = 0,
            credit = 0,
            dealer_total_debit = 0,
            dealer_total_credit = 0,
            retrailer_total_debit = 0,
            retrailer_total_credit = 0,
            op_first_itarate = 0,
            inline_total_debit = 0,
            inline_total_credit = 0,
            closing_blance_sign,
            ledger_dr_cr;

        // ledger array
        $.each(response.data.party_ledger, function(key, v) {
            ledger_dr_cr = v.DrCr;
            debit += parseFloat(v.debit_sum ? v.debit_sum : 0);
            credit += parseFloat(v.credit_sum ? v.credit_sum : 0);
            if(v.ledger_type==1){
                dealer_total_debit += parseFloat(v.debit_sum ? v.debit_sum : 0);
                dealer_total_credit+= parseFloat(v.credit_sum ? v.credit_sum : 0);
            }else if(v.ledger_type==2){
                retrailer_total_debit += parseFloat(v.debit_sum ? v.debit_sum : 0);
                retrailer_total_credit += parseFloat(v.credit_sum ? v.credit_sum : 0);

            }
            html.push( `<tr id="${v.tran_id},${v.voucher_type_id}" class="left left-data editIcon table-row ${v.ledger_type==2?'table-info':'table-primary'}">
                        <td class="th"  style="width: 1%;">${(key + 1)}</td>
                        <td  class="th" style="width: 1%; font-size: 16px;">${join(new Date(v.transaction_date), options, ' ')}</td>
                        <td  class="th" style="width: 1%; font-size: 16px;">${v.ledger_type==2?'Retrailer':'Dealer'}</td>
                        `);

            if (($("input[type='radio'].description:checked").val() == 1)) {
                 html.push(`<td  class="th text-wrap" style="width: 1%; font-size: 16px;font-weight: bold; ">${(v.ledger_name ? v.ledger_name : '')}</td>`);
            } else if (($("input[type='radio'].description:checked").val() == 2)) {
                let party_name_drcr = v.party_name_debit_credit.split("__");
               html.push(`<td  class="th " style="width: 1%; font-size: 16px;" >
                            <div style="display: flex;justify-content: space-between;">
                                <p style="font-size: 16px;">${(party_name_drcr[2])} </p>
                                <p style="font-size: 16px;">
                                    ${parseFloat(party_name_drcr[0]==0 ? party_name_drcr[1] : party_name_drcr[0] )?.formatBangladeshCurrencyType("accounts",'',(party_name_drcr[3]))}
                                </p>
                            </div>
                          </td>`);
            } else if (($("input[type='radio'].description:checked").val() == 3)) {
                let party_name_drcr = v.party_name_debit_credit.split("__");
                html.push(`<td  class="th " style="width: 1%; font-size: 16px; ">
                                <p style="font-weight: bold" class="text-wrap">${ (v.ledger_name ? v.ledger_name : '')}</p>
                                <div style="display: flex;justify-content: space-between;">
                                    <p style="margin-bottom:0;padding-bottom:0;"  class="drcr">${(party_name_drcr[2] )}</p>
                                    <p style="margin-bottom:0;padding-bottom:0;font-size: 16px;"  class="drcr">
                                        ${parseFloat(party_name_drcr[0]==0 ? party_name_drcr[1] : party_name_drcr[0] )?.formatBangladeshCurrencyType("accounts",'',(party_name_drcr[3]))}
                                    </p>
                                </div>
                            </td>`);
            } else {
                 html.push(`<td class="th " style="width: 1%; font-size: 16px;font-weight: bold; ">
                <i  class="text-wrap">${(v.ledger_name ? v.ledger_name : '')}</i>`);
            }


            // ledger
            if (($("input[type='radio'].description:checked").val() == 4) || (($("input[type='radio'].description:checked").val() == 5))) {
                if (response?.data?.description_ledger.hasOwnProperty(v?.tran_id)) {
                   html.push(response.data.description_ledger[v?.tran_id].map(x => {
                        if ((x.debit != 0) || (x.credit != 0)) {
                            return `<div class="drcr_mp_b_0" style="display: flex;justify-content: space-between;">
                            <p style="margin-bottom:0;padding-bottom:0;font-size: 12px; " class="text-wrap drcr">${x?.ledger_name} </p>
                            <p style="margin-bottom:0;padding-bottom:0; font-size: 12px;" class="drcr">
                            ${(x?.dr_cr == "Dr" ?
                                ((x?.debit).formatBangladeshCurrencyType("accounts",'',' Dr ')) :
                                (x?.credit.formatBangladeshCurrencyType("accounts",'',' Cr ') ))
                            }
                            </p>
                        </div>`
                        }
                    }).join(""))
                }
            }

            // stock out
            if ($("input[type='radio'].description:checked").val() == 5) {
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
                                        </tr>`);

                    if (body.length) {
                        html.push( `<table width:100% style="width:100%" class="table table-bordered">
                           <tr >
                                <td style="text-align:center;font-size: 12px;" class="my-0 py-0">Item</td>
                                <td style="text-align:right;font-size: 12px;" class="my-0 py-0">Qty</td>
                                <td style="text-align:right;font-size: 12px;" class="my-0 py-0">Rate</td>
                                <td style="text-align:right;font-size: 12px;" class="my-0 py-0">Total</td>
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
                                    </tr>`);

                    if (body.length) {
                        html.push( `<table width:100% style="width:100%" class="table table-bordered">
                           <tr >
                                <td style="text-align:center;font-size: 12px;" class="my-0 py-0">Item</td>
                                <td style="text-align:right;font-size: 12px;" class="my-0 py-0">Qty</td>
                                <td style="text-align:right;font-size: 12px;" class="my-0 py-0">Rate</td>
                                <td style="text-align:right;font-size: 12px;" class="my-0 py-0">Total</td>
                            </tr>
                    ${body?.join('')}
                        </table>`)
                    }
                }
            }
            if ($(".user_info").is(':checked')) {
                 html.push(`<div >
                           <i style="font-size: 16px;" class="text-wrap">${JSON.parse(v.other_details)}</i>
                        </div>`);
            }
            if (($(".narratiaon").is(':checked'))) {
                html.push( `<div>
                     <i style="font-size: 16px;" class="text-wrap">${(v.narration ? v.narration : '')}</i>
                </div>`);
            }
             html.push(`</td>
                    <td class="th party_ledger_voucher" style="width: 1%; font-size: 16px;color:#0B55C4;">${v.voucher_name}</td>
                        <td class="th"  style="width: 1%; font-size: 16px;">${v.invoice_no}</td>
                        <td class="th" style="width: 2%;  text-align: right; font-size: 18px;font-family: Arial, sans-serif;">${(v.debit ? (v.debit_sum ? v.debit_sum.formatBangladeshCurrencyType("accounts") : '') : '') }</td>
                        <td  class="th" style="width: 2%; text-align: right; font-size: 18px;font-family: Arial, sans-serif;">${(v.credit ? (v.credit_sum ? v.credit_sum.formatBangladeshCurrencyType("accounts") : '') : '')}</td>`);

            let total_closing;
            if (response.data.group_chart_nature.nature_group == 1 || response.data.group_chart_nature.nature_group == 3) {

                if ($(".inline_closing_blance").is(':checked')) {

                    if (op_first_itarate == 0) {
                        // total_closing=((parseFloat(v.debit_sum||0) - parseFloat(v.credit_sum ||0)));
                        // closing_blance +=total_closing;
                        total_closing = (((parseFloat(response.data.op_party_ledger[0]?.op_total_debit1 || 0) - parseFloat(response.data.op_party_ledger[0]?.op_total_credit1 || 0)) + (parseFloat(v.debit_sum || 0) - parseFloat(v.credit_sum || 0)))) + (response.data.group_chart_nature.opening_balance || 0);
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
                   html.push(`<td class="th"  style="width: 2%; text-align: right; font-size: 18px;font-family: Arial, sans-serif;">${(Math.abs(closing_blance) || 0).formatBangladeshCurrencyType("accounts",'',closing_blance_sign)}</td>`);
                }
            }
            if (response.data.group_chart_nature.nature_group == 2 || response.data.group_chart_nature.nature_group == 4) {
                if (op_first_itarate == 0) {

                    total_closing = ((parseFloat(response.data.op_party_ledger[0]?.op_total_credit2 || 0) - parseFloat(response.data.op_party_ledger[0]?.op_total_debit2 || 0)) + (parseFloat(v.credit_sum || 0) - parseFloat(v.debit_sum || 0))) + (response.data.group_chart_nature.opening_balance || 0);
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
                if ($(".inline_closing_blance").is(':checked')) {
                   html.push(`<td class="th"  style="width: 1%;text-align: right; font-size: 18px;font-family: Arial, sans-serif;">${(Math.abs(closing_blance) || 0).formatBangladeshCurrencyType("accounts",'',closing_blance_sign)}</td>`);
                }
            }

           html.push(`</tr>`);
        });

        // Opening Balance
        if ($(".inline_closing_blance").is(':checked')) {
            if (closing_blance) {
                html.push(`<tr class="left left-data editIcon table-row">

                            <td colspan="6" style="width: 1%;  border: 1px solid #ddd;font-size: 18px;text-align: right;">Closing Balance  :</td>
                            <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(inline_total_debit) || 0).formatBangladeshCurrencyType("accounts")}</td>
                            <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(inline_total_credit) || 0).formatBangladeshCurrencyType("accounts")}</td>
                            <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(closing_blance) || 0).formatBangladeshCurrencyType("accounts",'',closing_blance_sign)}</td>
                        </tr>`);
                $('.closing_blance').text("Closing Blance : " + (Math.abs(closing_blance)).formatBangladeshCurrencyType("accounts",'',closing_blance_sign));
            } else {

                if(inline_total_debit||inline_total_credit){
                    html.push(`<tr class="left left-data editIcon table-row">
                        <td colspan="6" style="width: 1%;  border: 1px solid #ddd;font-size: 18px;text-align: right;">Closing Balance  :</td>
                        <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(inline_total_debit) || 0).formatBangladeshCurrencyType("accounts")}</td>
                        <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(inline_total_credit) || 0).formatBangladeshCurrencyType("accounts")}</td>
                        <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(closing_blance) || 0).formatBangladeshCurrencyType("accounts",'',closing_blance_sign)}</td>
                        </tr>`);
                        $('.closing_blance').text("Closing Blance : " + (Math.abs(closing_blance)).formatBangladeshCurrencyType("accounts",'',closing_blance_sign));
                }else{
                    html.push(`<tr class="left left-data editIcon table-row">
                    <td colspan="6" style="width: 1%; border: 1px solid #ddd;font-size: 18px;text-align: right;">Closing Balance  :</td>
                    <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(closing_blance_sign_show == 'Dr' ?Math.abs(inline_total_debit||closing_blance_show): 0).formatBangladeshCurrencyType("accounts")}</td>
                    <td style="width: 1%;  border: 1px solid #ddd;font-size: 16px; text-align: right;font-family: Arial, sans-serif;">${(closing_blance_sign_show == 'Cr' ? Math.abs(inline_total_credit||closing_blance_show):0).formatBangladeshCurrencyType("accounts")}</td>
                    <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(Math.abs(closing_blance_show) || 0).formatBangladeshCurrencyType("accounts",'',closing_blance_sign_show)}</td>
                    </tr>`);
                    $('.closing_blance').text("Closing Blance : " + (Math.abs(closing_blance_show)).formatBangladeshCurrencyType("accounts",'',closing_blance_sign_show));
                }
            }

        } else {
            let oppening_sign;
            let closing_sign;

            if ((dealer_total_debit != 0) || (dealer_total_credit !=0)) {
                html.push(`<tr class="left left-data editIcon table-row table-primary">
                <td colspan="6" style="width: 1%; border: 1px solid #ddd;font-size: 18px; text-align: right;">Dealer Current Total  :</td>
                <td style="width: 1%;  border: 1px solid #ddd; font-size: 18px; text-align: right;font-family: Arial, sans-serif; ">${(dealer_total_debit).formatBangladeshCurrencyType("accounts")}</td>
                <td style="width: 1%;  border: 1px solid #ddd; font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(dealer_total_credit).formatBangladeshCurrencyType("accounts")}</td>
                </tr>`);
            }
            if((retrailer_total_debit != 0)||(retrailer_total_credit != 0)){
                html.push(`<tr class="left left-data editIcon table-row table-info">
                <td colspan="6" style="width: 1%; border: 1px solid #ddd;font-size: 18px; text-align: right;">Retrailer Current Total  :</td>
                <td style="width: 1%;  border: 1px solid #ddd; font-size: 18px; text-align: right;font-family: Arial, sans-serif; ">${(retrailer_total_debit).formatBangladeshCurrencyType("accounts")}</td>
                <td style="width: 1%;  border: 1px solid #ddd; font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(retrailer_total_credit).formatBangladeshCurrencyType("accounts")}</td>
                </tr>`);
            }
          html.push(`<tr class="left left-data editIcon table-row">

                      <td colspan="6" style="width: 1%; border: 1px solid #ddd;font-size: 18px; text-align: right;">Current Total  :</td>
                      <td style="width: 1%;  border: 1px solid #ddd; font-size: 18px; text-align: right;font-family: Arial, sans-serif; ">${(debit).formatBangladeshCurrencyType("accounts")}</td>
                      <td style="width: 1%;  border: 1px solid #ddd; font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(credit).formatBangladeshCurrencyType("accounts")}</td>
                    </tr>`);

            if (response.data.group_chart_nature.nature_group == 1 || response.data.group_chart_nature.nature_group == 3) {
                //dealer
                let debit_opening_blance = (response.data.op_party_ledger[0] ? (parseFloat(response.data.op_party_ledger[0].op_total_debit1 || 0) - parseFloat(response.data.op_party_ledger[0].op_total_credit1 || 0)) : 0) + (response.data.group_chart_nature.opening_balance || 0);
                oppening_sign = debit_opening_blance >= 0 ? 'Dr' : 'Cr';

                html.push(`<tr class="left left-data editIcon table-row">

                          <td colspan="6" style="width: 1%;   border: 1px solid #ddd;font-size: 18px; text-align: right;">Opening Balance :</td>
                          <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(oppening_sign == 'Dr' ? (Math.abs(debit_opening_blance)).formatBangladeshCurrencyType("accounts") : '')}</td>
                          <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${ (oppening_sign == 'Cr' ? (Math.abs(debit_opening_blance)).formatBangladeshCurrencyType("accounts") : '') }</td>
                        </tr>`);

                let closing_bance_debit = ((debit_opening_blance + parseFloat(debit || 0)) - (credit || 0));
                closing_sign = closing_bance_debit >= 0 ? 'Dr' : 'Cr';

                 html.push(`<tr class="left left-data editIcon table-row">

                            <td colspan="6" style="width: 1%; border: 1px solid #ddd;font-size: 18px;text-align: right;">Closing Balance  :</td>
                            <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(closing_sign == 'Dr' ? (Math.abs(closing_bance_debit)).formatBangladeshCurrencyType("accounts") : '')}</td>
                            <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(closing_sign == 'Cr' ? (Math.abs(closing_bance_debit)).formatBangladeshCurrencyType("accounts") : '')}</td>
                        </tr>`);

                $('.closing_blance').text("Closing Blance : " + (Math.abs(closing_bance_debit)).formatBangladeshCurrencyType("accounts",'',closing_sign));

            }
            if (response.data.group_chart_nature.nature_group == 2 || response.data.group_chart_nature.nature_group == 4) {
                let credit_opning_blance = (response.data.op_party_ledger[0] ? (parseFloat(response.data.op_party_ledger[0].op_total_credit2 || 0) - parseFloat(response.data.op_party_ledger[0].op_total_debit2 || 0)) : 0) + (response.data.group_chart_nature.opening_balance || 0);
                oppening_sign = credit_opning_blance >= 0 ? 'Cr' : 'Dr';
                 html.push(`<tr class="left left-data editIcon table-row">

                          <td colspan="6"  style="width: 1%;   border: 1px solid #ddd;font-size: 18px;  text-align: right;">Opening Balance :</td><td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(oppening_sign == 'Dr' ? (Math.abs(credit_opning_blance)).formatBangladeshCurrencyType("accounts") : '')}</td>
                          <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(oppening_sign == 'Cr' ? (Math.abs(credit_opning_blance)).formatBangladeshCurrencyType("accounts") : '')}</td>
                        </tr>`);

                let closing_blance_credit = ((credit_opning_blance + (credit || 0)) - (debit || 0));
                closing_sign = closing_blance_credit >= 0 ? 'Cr' : 'Dr';
                 html.push(`<tr class="left left-data editIcon table-row">
                          <td colspan="6" style="width: 1%; border: 1px solid #ddd;font-size: 18px;  text-align: right;">Closing Balance  :</td>
                          <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(closing_sign == 'Dr' ? (Math.abs(closing_blance_credit)).formatBangladeshCurrencyType("accounts") : '')}</td>
                          <td style="width: 1%;  border: 1px solid #ddd;font-size: 18px; text-align: right;font-family: Arial, sans-serif;">${(closing_sign == 'Cr' ? (Math.abs(closing_blance_credit)).formatBangladeshCurrencyType("accounts") : '')}</td>
                        </tr>`);


                $('.closing_blance').text("Closing Blance : " + (Math.abs(closing_blance_credit)).formatBangladeshCurrencyType("accounts") + ' ' + closing_sign);

            }
        }
        $("#tableId").find(".qw").html(html.join(''));
        get_hover();
    }

    function get_retrailer_ledger_all_initial_show() {

        //$(".modal").show();
        let ledger_id = $(".ledger_id").val();
        append_header();
        $.ajax({
            url: '{{ route("report-ledger-details-retrailer-data") }}',
            method: 'GET',
            data: {
                to_date: $('.to_date').val(),
                from_date: $('.from_date').val(),
                ledger_id: $(".ledger_id").val(),
                is_debit_credit: $('#show_debit_credit_is').is(':checked') ? 0 : 1,
                sort_by: $(".sort_by:checked").val(),
                stort_type: $(".stort_type:checked").val(),
                description: $(".description:checked").val(),
                ledger_type:$(".ledger_type:checked").val(),
            },
            dataType: 'json',
            success: function(response) {
                $(".modal").hide();
                get_current_retrailer_ledger_val(response);
            },
            error: function(data, status, xhr) {}
        });

    }

    function local_store_retrailer_ledger_details_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("ledger_id", '.ledger_id');
        let sort_by = getStorage("sort_by");
        $(".sort_by[value='" + sort_by + "']").prop("checked", true);
        let stort_type = getStorage("stort_type");
        $(".stort_type[value='" + stort_type + "']").prop("checked", true);
        let description = getStorage("description");
        $(".description[value='" + description + "']").prop("checked", true);
        let ledger_type = getStorage("ledger_type");
        $(".ledger_type[value='" + ledger_type + "']").prop("checked", true);
    }

    function local_store_retrailer_ledger_details_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("ledger_id", $('.ledger_id').val());
        setStorage("sort_by", $(".sort_by:checked").val());
        setStorage("stort_type", $(".stort_type:checked").val());
        setStorage("description", $(".description:checked").val());
        setStorage("ledger_type", $(".ledger_type:checked").val());
    }

    function append_header() {

            $("#tableId").html(`<thead>
                            <tr>
                                <th class="th" style="width: 1%;">SL.</th>
                                <th class="th" style="width: 1%;">Date</th>
                                <th class="th" style="width: 1%;">Ledger Type</th>
                                <th class="th" style="width: 5%;">Particulars</th>
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

    function description_check() {
        if ($(".ledger_id").val() == 0) {
            $(".description_show").addClass("d-none");
            $('.sort_by_particular').removeClass("d-none");
        } else {
            $(".description_show").removeClass("d-none");
            $('.sort_by_particular').addClass("d-none");
        }
    }
    ///redirect route
    $(document).ready(function() {
        $('.sd').on('click', '.party_ledger_voucher', function(e) {

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
            } else if (day_book_arr[1] == 20) {
                window.open(`{{url('voucher-sales-order')}}/${day_book_arr[0]}/edit`, '_blank');
            }
        })
    });
</script>
@endpush
@endsection
