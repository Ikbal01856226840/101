@extends('layouts.backend.app')
@section('title','Error Console')
@push('css')
<style>
    input[type=radio] {
        width: 20px;
        height: 20px;
    }

    input[type=checkbox] {
        width: 20px;
        height: 20px;
    }
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
<!-- add component-->
@component('components.report', [
    'title' => 'Error Console',
    'print_layout'=>'landscape',
    'print_header'=>'Error Console',
    'user_privilege_title'=>'Error Console',
    'print_date'=>1,
]);

<!-- Page-header component -->
@slot('header_body')
<form id="add_day_book_form" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row">
        
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
                <input class="form-check-input narratiaon" type="checkbox" id="narratiaon" name="narratiaon" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Narration
                </label>
                @if (Auth()->user()->user_level==1)
                <input class="form-check-input user_info" type="checkbox" name="last_update" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    User Info
                </label>
                
              @endif
            </div>
        </div>
    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_ErrorConsole_report ">
</div>
@endslot
@endcomponent
<br>
@push('js')

<script>
    $(document).ready(function() {
        var amount_decimals = "{{company()->amount_decimals}}";

        // day book initial show
        function get_ErrorConsole_initial_show() {
            updategetAndRemoveStorage();
            print_date();
            //$(".modal").show();
            $.ajax({
                url: "{{ url('report/error-console-data')}}",
                type: 'GET',
                dataType: 'json',
                data: {
                    to_date: $('.to_date').val(),
                    from_date: $('.from_date').val(),
                },
                success: function(response) {
                    $(".modal").hide();
                    get_ErrorConsole_val(response);
                    set_scroll_table();
                },
                error: function(data, status, xhr) {
                    Unauthorized(data.status);
                }
            })
        }

        // day book  show
        $("#add_day_book_form").submit(function(e) {
            e.preventDefault();
            print_date();
            //$(".modal").show();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("error-console-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    $(".modal").hide();
                    get_ErrorConsole_val(response)
                },
                error: function(data, status, xhr) {
                    Unauthorized(data.status);
                }
            });
        });

        get_ErrorConsole_initial_show();

        function get_ErrorConsole_val(response) {

            const rowsPerPage = 500; // Number of rows to display at a time
            let currentPage = 0; // Track current page
            const totalRows = response.data.length; // Total number of rows

            // Function to render the table header and footer
            function renderTableHeaderFooter() {
                let htmlFragments = [];
                htmlFragments.push(`
                    <table id="tableId" style="border-collapse: collapse;" class="table table-striped customers">
                        <thead>
                            <tr>
                                <th style="width: 1%;" class="td">SL.</th>
                                <th style="width: 3%;" class="td">Date</th>
                                <th style="width: 2%;" class="td">Voucher Type</th>
                                <th style="width: 3%;" class="td">Voucher No</th>
                `);
                // Add optional headers based on checkboxes
                if ($("#narratiaon").is(':checked')) {
                    htmlFragments.push(`<th style="width: 3%;" class="td">Narration</th>`);
                }
                    htmlFragments.push(`<th style="width: 3%;" class="td">Debit Amount/<br>Inwords Qty</th>`);
                    htmlFragments.push(`<th style="width: 3%;" class="td">Credit Amount/<br>Outwards Qty</th>`);
                
                htmlFragments.push(`</tr></thead><tbody id="myTable" class="qw">`);

                $(".sd").html(htmlFragments.join('')); // Render header once

            }

            // Function to render a chunk of data
            function renderTableChunk(startIndex) {
                let htmlFragments = [];
                for (let i = startIndex; i < Math.min(startIndex + rowsPerPage, totalRows); i++) {
                    const v = response.data[i];
                    htmlFragments.push(`
                        <tr id='${v.tran_id},${v.voucher_type_id}' class="left left-data editIcon table-row ">
                            <td style="width: 1%;" class="td">${(i + 1)}</td>
                            <td style="width: 3%;" class="td">${join(new Date(v.transaction_date), options, ' ')}</td>
                            <td style="width: 2%;" class="td font text-wrap voucher_name">
                                 ${redirectVoucherIdWise(v.voucher_type_id,v.tran_id,v.voucher_name)}

                            </td>
                            <td style="width: 3%;" class="td font">${v.invoice_no}</td>
                    `);

                    // Optional columns
                    if ($("#narratiaon").is(':checked')) {
                        htmlFragments.push(`<td style="width: 3%;" class="td font text-wrap">${(v.narration || "")}</td>`);
                    }
                    
                    if(v.adjusted_debit){
                        htmlFragments.push(`<td style="width: 3%; text-align: right; " class="td font">${(v.adjusted_debit||'' )}</td>`);
                        htmlFragments.push(`<td style="width: 3%; text-align: left; color: red" class="td font">Commission Amount Not Equal</td>`);
                    }else if(v.balance && v.type==2){
                        htmlFragments.push(`<td style="width: 3%; text-align: right; " class="td font">${(v.balance||'' )}</td>`);
                        htmlFragments.push(`<td style="width: 3%; text-align: left; color: red" class="td font">Debit Credit Amount Not Equal</td>`);
                    }else if(v.min_ledger_id==0 && v.type==2){
                        htmlFragments.push(`<td style="width: 3%; text-align: right; " class="td font"></td>`);
                        htmlFragments.push(`<td style="width: 3%; text-align: left; color: red" class="td font">Account Ledger Empty</td>`);
                    }else if((v.total_debit-v.total_credit) && v.type==3){
                        htmlFragments.push(`<td style="width: 3%; text-align: right; " class="td font">${((v.total_debit-v.total_credit)||'' )}</td>`);
                        htmlFragments.push(`<td style="width: 3%; text-align: left; color: red" class="td font">Quantity*Rete Not Equal Tatal Amount </td>`);
                    }else if(v.min_ledger_id==0 && v.type==3){
                        htmlFragments.push(`<td style="width: 3%; text-align: right; " class="td font"></td>`);
                        htmlFragments.push(`<td style="width: 3%; text-align: left; color: red" class="td font">Stock Item  Empty</td>`);
                    }else if(v.balance==0 && v.type==3){
                        htmlFragments.push(`<td style="width: 3%; text-align: right; " class="td font"></td>`);
                        htmlFragments.push(`<td style="width: 3%; text-align: left; color: red" class="td font">Godown name  Empty</td>`);
                    }else if((v.total_debit-v.total_credit) && v.type==4){
                        htmlFragments.push(`<td style="width: 3%; text-align: right; " class="td font">${((v.total_debit-v.total_credit)||'' )}</td>`);
                        htmlFragments.push(`<td style="width: 3%; text-align: left; color: red" class="td font">Quantity*Rete Not Equal Tatal Amount </td>`);
                    }else if(v.min_ledger_id==0 && v.type==4){
                        htmlFragments.push(`<td style="width: 3%; text-align: right; " class="td font"></td>`);
                        htmlFragments.push(`<td style="width: 3%; text-align: left; color: red" class="td font">Stock Item  Empty</td>`);
                    }else if(v.balance==0 && v.type==4){
                        htmlFragments.push(`<td style="width: 3%; text-align: right; " class="td font"></td>`);
                        htmlFragments.push(`<td style="width: 3%; text-align: left; color: red" class="td font">Godown name  Empty</td>`);
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

                    get_hover();
            }

            // Function to render the footer
            function renderTableFooter() {
                let footerFragments = [];
                footerFragments.push(`<tfoot><tr>`);
                footerFragments.push(`
                    <th style="width: 1%;" class="td">SL.</th>
                    <th style="width: 3%;" class="td">Date</th>
                    <th style="width: 2%;" class="td">Voucher Type</th>
                    <th style="width: 3%;" class="td">Voucher No</th>
                `);

                if ($("#narratiaon").is(':checked')) {
                    footerFragments.push(`<th style="width: 3%;" class="td">Narration</th>`);
                }
              
                footerFragments.push(`<th style="width: 3%;" class="td">Debit Amount/<br>Inwords Qty</th>`);
              
                footerFragments.push(`<th style="width: 3%;" class="td">Credit Amount/<br>Outwards Qty</th>`);
                

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
        $(document).on('click', '.voucher_name', function(e) {
            setStorage("end_date_update", $('.to_date').val());
            setStorage("start_date_update", $('.from_date').val());
            setStorage("voucher_id_update", $('.voucher_id').val());
        })
        let display_height=$(window).height();
        $('.tableFixHead_ErrorConsole_report').css('height',`${display_height-120}px`);
    });
    function updategetAndRemoveStorage() {
        getStorage("end_date_update", '.to_date');
        getStorage("start_date_update", '.from_date');
        getStorage("voucher_id_updat", '.voucher_id');
        getRemoveItem("end_date_update", '.to_date');
        getRemoveItem("start_date_update", '.from_date');
        getRemoveItem("voucher_id_update", '.voucher_id');

    }
    // function updateRemoveStorage() {
    //     getRemoveItem("end_date_update", '.to_date');
    //     getRemoveItem("start_date_update", '.from_date');
    //     getRemoveItem("voucher_id_updat", '.voucher_id');
    // }
</script>
@endpush
@endsection
