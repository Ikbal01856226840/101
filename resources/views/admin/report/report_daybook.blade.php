@extends('layouts.backend.app')
@section('title', 'DayBook')
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
        'title' => 'Day Book',
        'print_layout' => 'landscape',
        'print_header' => 'Day Book',
        'user_privilege_title' => 'Daybook',
        'print_date' => 1,
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
                            <option value="0">--ALL--</option>
                            @php $voucher_type_id= 0; @endphp
                            @foreach ($vouchers as $voucher)
                                @if ($voucher_type_id != $voucher->voucher_type_id)
                                    @php $voucher_type_id=$voucher->voucher_type_id; @endphp
                                    @if (Auth()->user()->user_level == 1)
                                        <option style="color:red;" value="v{{ $voucher->voucher_type_id ?? '' }}">
                                            {{ $voucher->voucher_type ?? '' }}</option>
                                    @else
                                        @if ($voucher->filtered_count == $voucher->total_count)
                                            <option style="color:red;" value="v{{ $voucher->voucher_type_id ?? '' }}">
                                                {{ $voucher->voucher_type ?? '' }}</option>
                                        @endif
                                    @endif
                                @endif
                                <option value="{{ $voucher->voucher_id }}">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $voucher->voucher_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="row m-0 p-0">
                            <div class="col-md-6 start_date m-0 p-0">
                                <label>Date From: </label>
                                <input type="text" name="from_date" class="form-control setup_date fs-5 from_date"
                                    value="{{ financial_end_date(date('Y-m-d')) }}" name="narratiaon">
                            </div>
                            <div class="col-md-6 end_date m-0 p-0">
                                <label>Date To : </label>
                                <input type="text" name="to_date" class="form-control setup_date fs-5 to_date"
                                    value="{{ financial_end_date(date('Y-m-d')) }}" name="narratiaon">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <br>
                        <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit"
                            style="width:200px; margin-bottom:5px;"><span class="m-t-1 m-1"></span><span>Search</span></button>
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
                            <input class="form-check-input narratiaon" type="checkbox" id="narratiaon" name="narratiaon"
                                value="1">
                            <label class="form-check-label fs-6" for="flexRadioDefault1">
                                Narration
                            </label>
                            @if (Auth()->user()->user_level == 1)
                                <input class="form-check-input user_info" type="checkbox" name="last_update" value="1">
                                <label class="form-check-label fs-6" for="flexRadioDefault1">
                                    User Info
                                </label>
                                <input class="form-check-input last_update" type="checkbox" name="last_update" value="1">
                                <label class="form-check-label fs-6" for="flexRadioDefault1">
                                    Last Update
                                </label>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        @endslot

        <!-- Main body component -->
        @slot('main_body')
            <div class="dt-responsive table-responsive cell-border sd tableFixHead_daybook_report">
            </div>
        @endslot
    @endcomponent
    @push('js')
        <script>
            $(document).ready(function() {
                var amount_decimals = "{{ company()->amount_decimals }}";

                // day book initial show
                function get_daybook_initial_show() {
                    updategetAndRemoveStorage();
                    print_date();
                    $(".modal").show();
                    $.ajax({
                        url: "{{ url('get-daybook') }}",
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            to_date: $('.to_date').val(),
                            from_date: $('.from_date').val(),
                            voucher_id: $('.voucher_id').val()
                        },
                        success: function(response) {
                            $(".modal").hide();
                            get_daybook_val(response);
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
                    reset_scroll_height();
                    print_date();
                    $(".modal").show();
                    const fd = new FormData(this);
                    $.ajax({
                        url: '{{ route('daybook-report.store') }}',
                        method: 'POST',
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            $(".modal").hide();
                            get_daybook_val(response)
                        },
                        error: function(data, status, xhr) {
                            Unauthorized(data.status);
                        }
                    });
                });

                get_daybook_initial_show();

                function get_daybook_val(response) {

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
                                    <th style="width: 2%;" class="td">Date</th>
                                    <th style="width: 4%;" class="td">Particulars</th>
                                    <th style="width: 2%;" class="td">Voucher Type</th>
                                    <th style="width: 3%;" class="td">Voucher No</th>
                    `);
                        // Add optional headers based on checkboxes
                        if ($("#narratiaon").is(':checked')) {
                            htmlFragments.push(`<th style="width: 3%;" class="td">Narration</th>`);
                        }
                        if ($(".last_update").is(':checked')) {
                            htmlFragments.push(`<th style="width: 3%;" class="td">Last Update</th>`);
                        }
                        if ($(".debit_check").is(':checked')) {
                            htmlFragments.push(`<th style="width: 3%;" class="td">Debit Amount/<br>Inwords Qty</th>`);
                        }
                        if ($(".credit_check").is(':checked')) {
                            htmlFragments.push(`<th style="width: 3%;" class="td">Credit Amount/<br>Outwards Qty</th>`);
                        }

                        htmlFragments.push(`</tr></thead><tbody id="myTable" class="qw">`);

                        $(".sd").html(htmlFragments.join('')); // Render header once
                    }

                    // Function to render a chunk of data
                    function renderTableChunk(startIndex) {
                        let htmlFragments = [];
                        for (let i = startIndex; i < Math.min(startIndex + rowsPerPage, totalRows); i++) {
                            const v = response.data[i];
                            htmlFragments.push(`
                            <tr id='${v.tran_id},${v.voucher_type_id}' class="left left-data editIcon table-row">
                                <td style="width: 1%;" class="td">${(i + 1)}</td>
                                <td style="width: 2%;" class="td">${join(new Date(v.transaction_date), options, ' ')}</td>
                                <td style="width: 4%;" class="td font text-wrap">${(v.ledger_name || '')}</td>
                                <td style="width: 2%;" class="td font text-wrap voucher_name">
                                    ${redirectVoucherIdWise(v.voucher_type_id,v.tran_id,v.voucher_name)}
                                </td>
                                <td style="width: 3%;" class="td font">${v.invoice_no}</td>
                        `);

                            // Optional columns
                            if ($("#narratiaon").is(':checked')) {
                                htmlFragments.push(
                                    `<td style="width: 3%;" class="td font text-wrap">${(v.narration || "")}</td>`);
                            }
                            if ($(".last_update").is(':checked')) {
                                htmlFragments.push(
                                    `<td style="width: 3%;" class="td"><div><i>${JSON.parse(v.other_details || '')}</i></div></td>`
                                );
                            }

                            if ([1, 6, 8, 14, 28].includes(v.voucher_type_id)) {
                                if ($(".debit_check").is(':checked')) {
                                    htmlFragments.push(
                                        `<td style="width: 3%; text-align: right;" class="td font">${(v.debit ? (v.debit_sum ? v.debit_sum.toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ' TK' : '') : '')}</td>`
                                    );
                                }
                                if ($(".credit_check").is(':checked')) {
                                    htmlFragments.push(
                                        `<td style="width: 3%; text-align: right;" class="td font">${(v.credit ? (v.credit_sum ? v.credit_sum.toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ' TK' : '') : '')}</td>`
                                    );
                                }
                            } else {
                                if ($(".debit_check").is(':checked')) {
                                    htmlFragments.push(
                                        `<td style="width: 3%; text-align: right;" class="td font">${(v.stock_in_sum ? Math.abs(parseFloat(v.stock_in_sum)).toFixed(amount_decimals) : '')}</td>`
                                    );
                                }
                                if ($(".credit_check").is(':checked')) {
                                    htmlFragments.push(
                                        `<td style="width: 3%; text-align: right;" class="td font">${(v.stock_out_sum ? Math.abs(parseFloat(v.stock_out_sum)).toFixed(amount_decimals) : '')}</td>`
                                    );
                                }
                            }
                            htmlFragments.push(`</tr>`);
                        }

                        $("#myTable").append(htmlFragments.join('')); // Append chunk to the table body

                        // Load next chunk if there are more rows
                        if (startIndex + rowsPerPage < totalRows) {
                            setTimeout(() => renderTableChunk(startIndex + rowsPerPage),
                                0); // Use timeout for UI responsiveness
                        } else {
                            renderTableFooter(); // Render footer once all rows are loaded
                            scroll_table_to_prev();
                        }
                        get_hover();
                    }

                    // Function to render the footer
                    function renderTableFooter() {
                        let footerFragments = [];
                        footerFragments.push(`<tfoot><tr>`);
                        footerFragments.push(`
                        <th style="width: 1%;" class="td">SL.</th>
                        <th style="width: 2%;" class="td">Date</th>
                        <th style="width: 4%;" class="td">Particulars</th>
                        <th style="width: 2%;" class="td">Voucher Type</th>
                        <th style="width: 3%;" class="td">Voucher No</th>
                    `);

                        if ($("#narratiaon").is(':checked')) {
                            footerFragments.push(`<th style="width: 3%;" class="td">Narration</th>`);
                        }
                        if ($(".last_update").is(':checked')) {
                            footerFragments.push(`<th style="width: 3%;" class="td">Last Update</th>`);
                        }
                        if ($(".debit_check").is(':checked')) {
                            footerFragments.push(`<th style="width: 3%;" class="td">Debit Amount/<br>Inwords Qty</th>`);
                        }
                        if ($(".credit_check").is(':checked')) {
                            footerFragments.push(
                                `<th style="width: 3%;" class="td">Credit Amount/<br>Outwards Qty</th>`);
                        }

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
                let display_height = $(window).height();
                $('.tableFixHead_daybook_report').css('height', `${display_height-120}px`);
            });

            function updategetAndRemoveStorage() {
                getStorage("end_date_update", '.to_date');
                getStorage("start_date_update", '.from_date');
                getStorage("voucher_id_update", '.voucher_id');
                getRemoveItem("end_date_update", '.to_date');
                getRemoveItem("start_date_update", '.from_date');
                getRemoveItem("voucher_id_update", '.voucher_id');

            }
        </script>
    @endpush
@endsection
