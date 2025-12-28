@extends('layouts.backend.app')
@section('title', 'Sales List')
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

        .td {
            border: 1px solid #ddd;
        }

        .font {
            font-size: 16px;
        }
    </style>
@endpush
@section('admin_content')<br>
    <!-- add component-->
    @component('components.report', [
        'title' => 'Sales List',
        'print_layout' => 'landscape',
        'print_header' => 'Sales List',
        'user_privilege_title' => 'POSSalesList',
    ])
        ;

        <!-- Page-header component -->
        @slot('header_body')
            <form id="add_sales_list_form" method="POST">
                @csrf
                {{ method_field('POST') }}
                <div class="row">
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
            <div class="dt-responsive table-responsive cell-border sd tableFixHead_report">
            </div>
        @endslot
    @endcomponent
    <br>
    @push('js')
        <!-- table hover js -->
        <script type="text/javascript" src="{{ asset('libraries/assets/table-hover.js') }}"></script>
        <script>
            $(document).ready(function() {
                if (localStorage.getItem("end_date")) {
                    $('.to_date').val(localStorage.getItem("end_date"));
                    localStorage.setItem("end_date", '');
                }
                if (localStorage.getItem("start_date")) {
                    $('.from_date').val(localStorage.getItem("start_date"));
                    localStorage.setItem("start_date", '');
                }

            });

            $(document).ready(function() {
                var amount_decimals = "{{ company()->amount_decimals }}";

                // sales list initial show
                function get_sales_list_initial_show() {
                    $.ajax({
                        url: "{{ route('report-sales-list-data') }}",
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            to_date: $('.to_date').val(),
                            from_date: $('.from_date').val(),
                        },
                        success: function(response) {
                            get_sales_list_val(response)
                        }
                    })
                }

                // sales list  show
                $("#add_sales_list_form").submit(function(e) {
                    e.preventDefault();
                    const fd = new FormData(this);
                    $.ajax({
                        url: '{{ route('report-sales-list-data') }}',
                        method: 'POST',
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                            get_sales_list_val(response)
                        },
                    });
                });

                get_sales_list_initial_show();

                function get_sales_list_val(response) {
                    let htmlFragments = [];
                    htmlFragments.push(`<table  id="tableId" style=" border-collapse: collapse; " class="table table-striped customers  " >
                    <thead>
                     <tr>
                        <th style="width: 1%;" class="td">SL.</th>
                        <th style="width: 3%;"class="td">Date</th>
                        <th style="width: 3%;"class="td">Particulars</th>
                        <th style="width: 2%;"class="td">Type</th>
                        <th style="width: 2%;"class="td">Voucher Type</th>
                        <th style="width: 3%;"class="td">Voucher No</th>`);
                    if ($("#narratiaon").is(':checked')) {
                        htmlFragments.push(`<th style="width: 3%;"class="td" >Narration</th>`);
                    }
                    if ($(".user_info").is(':checked')) {
                        htmlFragments.push(`<th style="width: 3%;"class="td" >User Name</th>`);
                    }
                    if ($(".last_update").is(':checked')) {
                        htmlFragments.push(`<th style="width: 3%;"class="td" >Last Update</th>`);
                    }
                    if ($(".debit_check").is(':checked')) {
                        htmlFragments.push(`<th style="width: 3%;"class="td" >Debit Amount/<br>Inwords Qty</th>`);
                    }
                    if ($(".credit_check").is(':checked')) {
                        htmlFragments.push(`<th style="width: 3%;"class="td" >Credit Amount/<br>Outwards Qty</th>`);
                    }
                    htmlFragments.push(`</tr>`);
                    htmlFragments.push(`</thead>`);
                    htmlFragments.push(`<tbody  id="myTable"  class="qw">`);
                    $.each(response.data, function(key, v) {
                        htmlFragments.push(`<tr id='${v.tran_id+","+v.voucher_type_id}' class="left left-data editIcon table-row">
                                        <td  style="width: 1%;"class="td">${(key+1)}</td>
                                        <td  style="width: 3%;"class="td">${join( new Date(v.transaction_date), options, ' ')}</td>
                                        <td  style="width: 3%;"class="td font">${(v.ledger_name||'')}</td>
                                        <td style="color:#0B55C4;" class="td">
                                         <button type="button" class="btn btn-sm btn-success bill" value="${v.tran_id}">Bill</button>
                                         <button type="button" class="btn btn-sm btn-success challan" value="${v.tran_id}">Challan</button>
                                        </td>
                                        <td  class="daybook_voucher" style="width: 2%;color:#0B55C4;">${v.voucher_name||''}</td>
                                        <td  style="width: 3%;"class="td font">${v.invoice_no}</td>`);
                        if ($("#narratiaon").is(':checked')) {
                            htmlFragments.push(
                                `<td  style="width: 3%;"class="td font">${(v.narration||"")}</td>`);
                        }
                        if ($(".user_info").is(':checked')) {
                            htmlFragments.push(
                                `<td  style="width: 3%;"class="td font">${(v.narration||"")}'</td>`);
                        }
                        if ($(".last_update").is(':checked')) {
                            htmlFragments.push(
                                `<td  style="width: 3%;   font-size: 10px;"class="td"><div><i>${JSON.parse(v.other_details||'')}</i></div></td>`
                            );
                        }
                        if ($(".debit_check").is(':checked')) {
                            htmlFragments.push(`<td  style="width: %;text-align: right;"class="td font"></td>`);
                        }
                        if ($(".credit_check").is(':checked')) {
                            htmlFragments.push(
                                `<td  style="width: 3%;  text-align: right;"class="td font">${(v.stock_out_qty||0).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>`
                            );
                        }

                        htmlFragments.push(`</tr>`);
                    });
                    htmlFragments.push(`</tbody>
                            <tfoot>
                             <tr>
                                <th style="width: 1%;"class="td">SL.</th>
                                <th style="width: 3%;"class="td">Date</th>
                                <th style="width: 3%;"class="td">Particulars</th>
                                 <th style="width: 2%;"class="td">Type</th>
                                <th style="width: 2%;"class="td">Voucher Type</th>
                                <th style="width: 3%;"class="td">Voucher No</th>`);
                    if ($("#narratiaon").is(':checked')) {
                        htmlFragments.push(`<th style="width: 3%;"class="td" >Narration</th>`);
                    }
                    if ($(".user_info").is(':checked')) {
                        htmlFragments.push(`<th style="width: 3%;"class="td">User Name</th>`);
                    }
                    if ($(".last_update").is(':checked')) {
                        htmlFragments.push(`<th style="width: 3%;"class="td" >Last Update</th>`);
                    }
                    if ($(".debit_check").is(':checked')) {
                        htmlFragments.push(`<th style="width: 3%;"class="td">Debit Amount/<br>Inwords Qty</th>`);
                    }
                    if ($(".credit_check").is(':checked')) {
                        htmlFragments.push(`<th style="width: 3%; "class="td" >Credit Amount/<br> Outwards Qty</th>`);
                    }
                    htmlFragments.push(`</tr>
                    </tfoot>
                </table>
                `);
                    $(".sd").html(htmlFragments.join(''));
                    set_scroll_table();
                    get_hover();
                }
            });
            //redirect route
            $(document).ready(function() {
                $('.sd').on('click', '.daybook_voucher', function(e) {
                    localStorage.setItem("end_date", $('.to_date').val());
                    localStorage.setItem("start_date", $('.from_date').val());
                    e.preventDefault();
                    let day_book_arr = $(this).closest('tr').attr('id').split(",");
                    if (day_book_arr[1] == 30) {
                        window.open(`{{ url('voucher-pos') }}/${day_book_arr[0]}/edit`, '_blank');
                    }
                })
            });
            //get  all data show
            $(document).ready(function() {
                $('.sd').on('click', '.challan', function(e) {
                    e.preventDefault();
                    let id = $(this).val();
                    console.log(id);
                    if (id) {
                        window.open(`{{ url('show-challan-approve') }}/${id}`, '_blank');
                    }
                })
            });
            //get  all data show
            $(document).ready(function() {
                $('.sd').on('click', '.bill', function(e) {
                    e.preventDefault();
                    let id = $(this).val();
                    console.log(id);
                    if (id) {
                        window.open(`{{ url('show-pos-bill') }}/${id}`, '_blank');
                    }
                })
            });
        </script>
    @endpush
@endsection
