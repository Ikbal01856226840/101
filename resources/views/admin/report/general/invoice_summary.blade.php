@extends('layouts.backend.app')
@section('title','invoice Summary')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
<style>
    .td {
        border: 1px solid #ddd;
    }
    .font {
        font-size: 16px;
    }
    body {
        overflow: auto !important;
    }
</style>

@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
'id' =>'exampleModal',
'class' =>'modal fade',
'page_title'=>'invoice Summary',
'size'=>'modal-xl',
'page_unique_id'=>35,
'godown'=>'yes',
'title'=>'invoice Summary Reports',
'daynamic_function'=>'get_invoice_summary_initial_show'
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'invoice Summary',
    'print_layout'=>'portrait',
    'print_header'=>'invoice Summary',
    'user_privilege_title'=>'invoiceSummary',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
<form id="add_day_book_form" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row">
        <div class="col-md-2">
            <label>Voucher Type : </label>
            <select name="voucher_id" class="form-control js-example-basic-single voucher_id">
                @if (Auth()->user()->user_level==1)
                    <option value="0">--ALL--</option>
                    @endif
                @php $voucher_type_id= 0; @endphp
                @foreach ($vouchers as $voucher)
                @if($voucher_type_id!=$voucher->voucher_type_id)
                @php $voucher_type_id=$voucher->voucher_type_id; @endphp
                <option style="color:red;" value="v{{$voucher->voucher_type_id??''}}">{{$voucher->voucher_type??''}}</option>
                @endif
                <option value="{{$voucher->voucher_id}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$voucher->voucher_name}}</option>

                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <div class="row  m-0 p-0 ">
                <div class="col-md-6 m-0 p-0 start_date">
                    <label>Date From: </label>
                    <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{financial_end_date(date('Y-m-d'))}}" name="narratiaon">
                </div>
                <div class="col-md-6 m-0 p-0 end_date">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{financial_end_date(date('Y-m-d'))}}" name="narratiaon">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <br>
            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:200px; margin-bottom:5px;"><span class="m-1 m-t-1"></span><span>Search</span></button>
        </div>
        <div class="col-md-5">
            <label></label>
            <div>
                <input class="form-check-input debit_check" type="checkbox" name="narratiaon" value="1" checked>
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Debit Amount/Inwords Qty
                </label>
                <input class="form-check-input credit_check" type="checkbox" name="last_update" value="1" checked>
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Credit Amount/Outwards Qty
                </label><br>
                <input class="form-check-input narratiaon" type="checkbox" id="narratiaon" name="narratiaon" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Narration
                </label>
                <input class="form-check-input user_info" type="checkbox" name="last_update" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    User Info
                </label>
                <input class="form-check-input last_update" type="checkbox" name="last_update" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Last Update
                </label>
            </div>
        </div>
    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_invoice_summary_report ">
</div>
@endslot
@endcomponent
<br>
@push('js')
<script>
    let total_qty=0;total_amount=0;
    $(document).ready(function() {
        // day book initial show
        function get_invoice_summary_initial_show() {
            print_date();
            $(".modal").show();
            $.ajax({
                url: "{{ url('report/invoice-summary-data')}}",
                type: 'GET',
                dataType: 'json',
                data: {
                    to_date: $('.to_date').val(),
                    from_date: $('.from_date').val(),
                    voucher_id: $('.voucher_id').val()
                },
                success: function(response) {
                    $(".modal").hide();
                    get_invoice_summary_val(response)
                }
            })
        }

        // day book  show
        $("#add_day_book_form").submit(function(e) {
            total_qty=0;total_amount=0;
            e.preventDefault();
            print_date();
            $(".modal").show();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("report-invoice-summary-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    $(".modal").hide();
                    get_invoice_summary_val(response)
                },
                error: function(data) {
                    Unauthorized(data.status);
                }
            });
        });

        get_invoice_summary_initial_show();

        function get_invoice_summary_val(response) {

            const rowsPerPage = 500; // Number of rows to display at a time
            let currentPage = 0; // Track current page
            const totalRows = response.data.length; // Total number of rows

            // Function to render the table header and footer
            function renderTableHeaderFooter() {
                let htmlFragments = [];
                htmlFragments.push(`
                    <table id="tableId" style="border-collapse: collapse;" class="table table-striped customers table-scroll">
                        <thead>
                            <tr>
                                <th style="width: 1%;" class="td">SL.</th>
                                <th style="width: 3%;" class="td">Date</th>
                                <th style="width: 3%;" class="td">Particulars</th>
                                <th style="width: 2%;" class="td">Voucher Type</th>
                                <th style="width: 3%;" class="td">Voucher No</th>
                                <th style="width: 3%;" class="td">Ref No</th>
                                <th style="width: 3%;" class="td">Gate Pase Ref</th>
                `);
                // Add optional headers based on checkboxes
                if ($("#narratiaon").is(':checked')) {
                    htmlFragments.push(`<th style="width: 3%;" class="td">Narration</th>`);
                }
                if ($(".last_update").is(':checked')) {
                    htmlFragments.push(`<th style="width: 3%;" class="td">Last Update</th>`);
                }
                htmlFragments.push(`<th style="width: 3%;text-align: right;" class="td">Total Qty</th>`);
                htmlFragments.push(`<th style="width: 3%; text-align: right;" class="td ">Total Amount</th>`);
                htmlFragments.push(`</tr></thead><tbody id="myTable" class="qw">`);

                $(".sd").html(htmlFragments.join('')); // Render header once
            }

            // Function to render a chunk of data
            function renderTableChunk(startIndex) {
                let htmlFragments = [];
                for (let i = startIndex; i < Math.min(startIndex + rowsPerPage, totalRows); i++) {
                    const v = response.data[i];
                    if(v?.voucher_type_id==21 || v?.voucher_type_id==22){
                        total_qty=total_qty+Math.abs(parseFloat(v?.stock_in_sum||v?.stock_out_sum||0));
                        total_amount=total_amount+Math.abs(parseFloat(v?.stock_in_total_sum || v?.stock_out_total_sum ||0));
                    }else{
                        total_qty=total_qty+Math.abs(parseFloat(v.stock_in_sum||0)-parseFloat(v.stock_out_sum||0));
                        total_amount=total_amount+(v.debit?v.debit:(v.credit||0));
                    }
                    htmlFragments.push(`
                        <tr id='${v.tran_id},${v.voucher_type_id}' class="left left-data editIcon table-row">
                            <td style="width: 1%;" class="td">${(i + 1)}</td>
                            <td style="width: 3%;" class="td">${join(new Date(v.transaction_date), options, ' ')}</td>
                            <td style="width: 3%;" class="td font text-wrap">${(v.ledger_name || '')}</td>
                            <td style="width: 2%;" class="td font">
                                ${redirectVoucherIdWise(v.voucher_type_id, v.tran_id,v.voucher_name)}
                            </td>
                            <td style="width: 3%;" class="td font ext-wrap">${v.invoice_no|| ''}</td>
                            <td style="width: 3%;" class="td font ext-wrap">${v.ref_no|| ''}</td>
                            <td style="width: 3%;" class="td font ext-wrap">${v.gprf|| ''}</td>
                    `);

                    // Optional columns
                    if ($("#narratiaon").is(':checked')) {
                        htmlFragments.push(`<td style="width: 3%;" class="td font text-wrap">${(v.narration || "")}</td>`);
                    }
                    if ($(".last_update").is(':checked')) {
                        htmlFragments.push(`<td style="width: 3%;" class="td"><div><i>${JSON.parse(v.other_details || '')}</i></div></td>`);
                    }
                    if(v?.voucher_type_id==21 || v?.voucher_type_id==22){
                        htmlFragments.push(`<td style="width: 3%; text-align: right;" class="td font">${Math.abs((parseFloat(v?.stock_in_sum||v?.stock_out_sum||0))).formatBangladeshCurrencyType("amount")}</td>`);
                        const amount = Number(v?.stock_in_total_sum ?? v?.stock_out_total_sum ?? 0);
                       htmlFragments.push(`<td style="width: 3%; text-align: right;" class="td font">${amount.formatBangladeshCurrencyType("amount")}</td>`);


                    }else{
                        htmlFragments.push(`<td style="width: 3%; text-align: right;" class="td font">${Math.abs((parseFloat(v.stock_in_sum||0)-parseFloat(v.stock_out_sum||0))).formatBangladeshCurrencyType("amount")}</td>`);
                        htmlFragments.push(`<td style="width: 3%; text-align: right;" class="td font">${(v.debit?v.debit:(v.credit||0))}</td>`);
                    }
                        htmlFragments.push(`</tr>`);
                }

                $("#myTable").append(htmlFragments.join('')); // Append chunk to the table body

                // Load next chunk if there are more rows
                if (startIndex + rowsPerPage < totalRows) {
                    setTimeout(() => renderTableChunk(startIndex + rowsPerPage), 0); // Use timeout for UI responsiveness
                } else {
                    renderTableFooter(); // Render footer once all rows are loaded
                }
            }

            // Function to render the footer
            function renderTableFooter() {
                let footerFragments = [];
                footerFragments.push(`<tfoot><tr>`);
                footerFragments.push(`
                    <th style="width: 1%;" class="td">SL.</th>
                    <th style="width: 3%;" class="td">Date</th>
                    <th style="width: 3%;" class="td">Particulars</th>
                    <th style="width: 2%;" class="td">Voucher Type</th>
                    <th style="width: 3%;" class="td">Voucher No</th>
                    <th style="width: 3%;" class="td">Ref No</th>
                    <th style="width: 3%;" class="td">Gate Pase Ref</th>
                `);

                if ($("#narratiaon").is(':checked')) {
                    footerFragments.push(`<th style="width: 3%;" class="td">Narration</th>`);
                }
                if ($(".last_update").is(':checked')) {
                    footerFragments.push(`<th style="width: 3%;" class="td">Last Update</th>`);
                }
console.log(total_amount);
                footerFragments.push(`<th style="width: 3%; font-size: 20px;text-align: right;">${(total_qty||0)?.formatBangladeshCurrencyType("amount")}</th>`);
                footerFragments.push(`<th style="width: 3%;font-size: 20px;text-align: right;">${(total_amount||0)?.formatBangladeshCurrencyType("amount")}</th>`);

                footerFragments.push(`</tr></tfoot>`);
                $("#tableId").append(footerFragments.join('')); // Append footer to the table
            }

            // Initial rendering
            renderTableHeaderFooter();
            renderTableChunk(currentPage * rowsPerPage);
       }

    });

    //redirect route
    $(document).ready(function() {
        let display_height=$(window).height();
       $('.tableFixHead_invoice_summary_report').css('height',`${display_height-120}px`);
    });
</script>
@endpush
@endsection
