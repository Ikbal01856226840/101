@extends('layouts.backend.app')
@section('title', 'Bill')
@push('css')
    <!-- model style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('libraries/assets/modal-style.css') }}">
    <style>
        input[type=radio] {
            width: 20px;
            height: 20px;
        }

        input[type=checkbox] {
            width: 20px;
            height: 20px;
        }

        .th {
            border: 1px solid #ddd;
        }

        body {
            overflow: auto !important;
        }
    </style>
@endpush
@section('admin_content')
    <!-- add component-->
    @component('components.report', [
        'title' => 'Bill',
        'print_layout' => 'landscape',
        'print_header' => 'Bill',
        'user_privilege_title' => 'Bill',
    ]);

        <!-- Page-header component -->
        @slot('header_body')
            <form id="bill_form" method="POST">
                @csrf
                {{ method_field('POST') }}
                <div class="row">
                    <div class="col-md-2">
                        <label>Voucher Type : </label>
                        <select name="voucher_id" class="form-control js-example-basic-single voucher_id">
                            @if (Auth()->user()->user_level == 1)
                                <option value="0">--ALL--</option>
                            @endif
                            @php $voucher_type_id= 0; @endphp
                            @foreach ($vouchers as $voucher)
                                @if ($voucher_type_id != $voucher->voucher_type_id)
                                    @php $voucher_type_id=$voucher->voucher_type_id; @endphp
                                    @if (Auth()->user()->user_level == 1)
                                        <option style="color:red;" value="v{{ $voucher->voucher_type_id ?? '' }}">
                                            {{ $voucher->voucher_type ?? '' }}</option>
                                    @elseif($voucher->filtered_count == $voucher->total_count)
                                        <option style="color:red;" value="v{{ $voucher->voucher_type_id ?? '' }}">
                                            {{ $voucher->voucher_type ?? '' }}</option>
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
                                    value="{{ financial_end_date(date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6 end_date m-0 p-0">
                                <label>Date To : </label>
                                <input type="text" name="to_date" class="form-control setup_date fs-5 to_date"
                                    value="{{ financial_end_date(date('Y-m-d')) }}">
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
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        @endslot

        <!-- Main body component -->
        @slot('main_body')
            <div class="dt-responsive table-responsive cell-border sd tableFixHead_bill_report">
                <table id="tableId" style=" border-collapse: collapse; " class="table-striped customers table">
                    <thead>
                        <tr>
                            <th class="th" style="width: 1%;">SL.</th>
                            <th class="th" style="width: 3%;">Date</th>
                            <th class="th" style="width: 3%;">Particulars</th>
                            <th class="th" style="width: 2%;">Voucher Type</th>
                            <th class="th" style="width: 3%;">Voucher No</th>
                            <th style="width: 3%;" class="th narration d-none colunm_none">Narration</th>
                            <th style="width: 3%; " class="th user_name d-none colunm_none">User Name</th>
                            <th class="th" style="width: 3%;">Debit Amount/<br>Inwords Qty</th>
                            <th class="th" style="width: 3%;">Credit Amount/<br>Outwards Qty</th>

                        </tr>
                    </thead>
                    <tbody id="myTable" class="bill_body">
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="th" style="width: 1%;">SL.</th>
                            <th class="th" style="width: 3%;">Date</th>
                            <th class="th" style="width: 3%;">Particulars</th>
                            <th class="th" style="width: 2%;">Voucher Type</th>
                            <th class="th" style="width: 3%;">Voucher No</th>
                            <th style="width: 3%;" class="th narration d-none colunm_none">Narration</th>
                            <th style="width: 3%;" class="th user_name d-none colunm_none">User Name</th>
                            <th class="th" style="width: 3%;">Debit Amount/<br>Inwords Qty</th>
                            <th class="th" style="width: 3%;">Credit Amount/<br>Outwards Qty</th>

                        </tr>
                    </tfoot>
                </table>
            </div>
        @endslot
    @endcomponent
    <br>
    @push('js')
        <!-- table hover js -->
        <script type="text/javascript" src="{{ asset('libraries/assets/table-hover.js') }}"></script>
        <script>
            $(document).ready(function() {
                var amount_decimals = "{{ company()->amount_decimals }}";

                function get_bill_initial_show() {
                    $.ajax({
                        url: "{{ url('bill-data') }}",
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            to_date: $('.to_date').val(),
                            from_date: $('.from_date').val(),
                            voucher_id: $('.voucher_id').val()
                        },
                        success: function(response) {
                            get_bill_val(response)
                        },
                        error: function(data, status, xhr) {
                            Unauthorized(data.status);
                        }
                    })
                }

                $("#bill_form").submit(function(e) {
                    e.preventDefault();
                    const fd = new FormData(this);
                    $.ajax({
                        url: '{{ route('bill-data') }}',
                        method: 'POST',
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                            get_bill_val(response)
                        },
                        error: function(data, status, xhr) {
                            Unauthorized(data.status);
                        }
                    });
                });
                get_bill_initial_show();

                function get_bill_val(response) {
                    if ($(".user_info").is(':checked')) {
                        $('.user_name').removeClass("d-none");
                        $('.user_name').removeClass("colunm_none");
                    } else {
                        $('.user_name').addClass("d-none");
                        $('.user_name').addClass("colunm_none");
                    }
                    if ($("#narratiaon").is(':checked')) {
                        $('.narration').removeClass("d-none");
                        $('.narration').removeClass("colunm_none");
                    } else {
                        $('.narration').addClass("d-none");
                        $('.narration').addClass("colunm_none");
                    }

                    let htmlFragments = [];
                    $.each(response.data, function(key, v) {
                        htmlFragments.push(`<tr id='${v.tran_id+","+v.voucher_type_id}' class="left left-data editIcon table-row">
                                 <td  class="th" style="width: 1%;">${(key+1)}</td>
                                 <td  class="th text-wrap" style="width: 3%;  font-size: 16px;">${join( new Date(v.transaction_date), options, ' ')}</td>
                                 <td  class="th text-wrap" style="width: 3%; font-size: 16px;">${(v.ledger_name ? v.ledger_name:'')}</td>
                                 <td  class="th text-wrap" style="width: 2%; font-size: 16px;color:#0B55C4;"><input type="hidden" class="voucher_name" value="${v.tran_id}">${(v.voucher_name)}</td>
                                 <td  class="th" style="width: 3%; font-size: 16px;">${v.invoice_no}</td>`);
                        if ($("#narratiaon").is(':checked')) {
                            htmlFragments.push(
                                `<td class="th text-wrap" style="width: 3%;  font-size: 16px;">${(v.narration||"")}</td>`
                            );
                        }
                        if ($(".user_info").is(':checked')) {
                            htmlFragments.push(
                                `<td  class="th text-wrap" style="width: 3%; font-size: 16px;">${JSON.parse(v.other_details || '')}</td>`
                            );
                        }
                        if (v.voucher_type_id == 1 || v.voucher_type_id == 6 || v.voucher_type_id == 8 || v
                            .voucher_type_id == 14 || v.voucher_type_id == 28) {
                            htmlFragments.push(
                                `<td class="th"  style="width: 3%; text-align: right; font-size: 16px;">${(v.debit?(v.debit_sum?v.debit_sum.toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')+ 'TK':''):'')}</td>`
                            );
                            htmlFragments.push(
                                `<td  class="th" style="width: 3%;  text-align: right; font-size: 16px;">${(v.credit?(v.credit_sum?v.credit_sum.toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')+ 'TK':''):'')}</td>`
                            );
                        } else {
                            htmlFragments.push(
                                `<td  class="th" style="width: 3%; text-align: right; font-size: 16px;">${(v.stock_in_sum?Math.abs(parseFloat(v.stock_in_sum)).toFixed(amount_decimals):'')}</td>`
                            );
                            htmlFragments.push(
                                `<td  class="th" style="width: 3%; text-align: right; font-size: 16px;">${(v.stock_out_sum?Math.abs(parseFloat(v.stock_out_sum)).toFixed(amount_decimals):'')}</td>`
                            );
                        }
                        htmlFragments.push(`</tr>`);
                    });
                    $(".bill_body").html(htmlFragments.join(''));
                    set_scroll_table();
                    get_hover();
                }
            });
            //get  all data show
            $(document).ready(function() {
                $('#tableId').on('click', 'td', function(e) {
                    e.preventDefault();
                    let id = $(this).find('.voucher_name').val();
                    if (id) {
                        window.open(`{{ url('approve') }}/${id}`, '_blank');
                    }
                })

            });
            let display_height = $(window).height();
            $('.tableFixHead_bill_report').css('height', `${display_height-120}px`);
        </script>
    @endpush
@endsection
