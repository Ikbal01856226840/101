@extends('layouts.backend.app')
@section('title', 'Sales Order')
@push('css')
    <!-- model style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('libraries/assets/modal-style.css') }}">
    <style>
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
        'title' => 'Sales Order',
        'print_layout' => 'landscape',
        'print_header' => 'Sales Order',
        'user_privilege_title' => 'SalesOrder',
    ])
        ;

        <!-- Page-header component -->
        @slot('header_body')
            <form id="add_sales_order_form" method="POST">
                @csrf
                {{ method_field('POST') }}
                <div class="row">
                    <div class="col-md-4">
                        <label>Party Name :</label>
                        <select name="ledger_id" class="form-control js-example-basic-single ledger_id" required>
                            <option value="0">--All--</option>
                            {!! html_entity_decode($ledgers) !!}
                        </select>
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <br>
                        <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit"
                            style="width:200px; margin-bottom:5px;"><span class="m-t-1 m-1"></span><span>Search</span></button>
                    </div>

                </div>
            </form>
        @endslot
        <!-- Main body component -->
        @slot('main_body')
            <div class="dt-responsive table-responsive cell-border sd tableFixHead_report">
                <table id="tableId" style=" border-collapse: collapse;" class="table-striped customers table">
                    <thead>
                        <tr>
                            <th style="width: 1%;" class="td">SL.</th>
                            <th style="width: 3%;" class="td">Date</th>
                            <th style="width: 3%;" class="td">Particulars</th>
                            <th style="width: 2%;" class="td">Voucher Type</th>
                            <th style="width: 3%;" class="td">Voucher No</th>
                            <th style="width: 3%;" class="td">Voucher Status</th>
                            <th style="width: 3%;" class="td">Delivery Date</th>
                            <th style="width: 3%;" class="td">Narration</th>
                            <th style="width: 3%;" class="td">Action</th>
                        </tr>
                    </thead>
                    <tbody id="myTable" class="sales_order_body">
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="width: 1%;" class="td">SL.</th>
                            <th style="width: 3%;" class="td">Date</th>
                            <th style="width: 3%;" class="td">Particulars</th>
                            <th style="width: 2%;" class="td">Voucher Type</th>
                            <th style="width: 3%;" class="td">Voucher No</th>
                            <th style="width: 3%;" class="td">Voucher Status</th>
                            <th style="width: 3%;" class="td">Delivery Date</th>
                            <th style="width: 3%;" class="td">Narration</th>
                            <th style="width: 3%;" class="td">Action</th>
                        </tr>
                    </tfoot>
                </table>
            @endslot
        @endcomponent
        <br>
        @push('js')
            <!-- table hover js -->
            <script type="text/javascript" src="{{ asset('libraries/assets/table-hover.js') }}"></script>
            <script>
                $(document).ready(function() {
                    var amount_decimals = "{{ company()->amount_decimals }}";
                    // sales order initial show
                    function get_sales_order_initial_show() {
                        $.ajax({
                            url: "{{ route('report-sales-order-data') }}",
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                to_date: $('.to_date').val(),
                                from_date: $('.from_date').val(),
                                ledger_id: $(".ledger_id").val(),
                            },
                            success: function(response) {
                                get_sales_order_val(response)
                            }
                        })
                    }

                    // day book  show
                    $("#add_sales_order_form").submit(function(e) {
                        e.preventDefault();
                        const fd = new FormData(this);
                        $.ajax({
                            url: "{{ route('report-sales-order-data') }}",
                            method: 'POST',
                            data: fd,
                            cache: false,
                            contentType: false,
                            processData: false,
                            dataType: 'json',
                            success: function(response) {
                                get_sales_order_val(response)
                            },
                            error: function(data, status, xhr) {}
                        });
                    });

                    get_sales_order_initial_show();

                    function get_sales_order_val(response) {
                        let htmlFragments = [];
                        $.each(response.data, function(key, v) {
                            htmlFragments.push(`<tr id='${v.tran_id+","+v.voucher_type_id}'class="left left-data table-row">
                                        <td  style="width: 1%;"class="td">${(key+1)}</td>
                                        <td  style="width: 3%;"class="td">${join( new Date(v.transaction_date), options, ' ')}</td>
                                        <td  style="width: 3%;"class="td font">${(v.ledger_name||'')}</td>
                                        <td  style="width: 2%;color:#0B55C4;"class="td font sales_order">${v.voucher_name||''}</td>
                                        <td  style="width: 3%;"class="td font">${v.invoice_no}</td>
                                        ${v.status==0 ?`<td style="width: 3%;"class="td font text-warning">Bill Not Created</td>`
                                                      :`<td  style="width: 3%;"class="td font text-info">Bill Created</td>`}
                                        <td  style="width: 3%;"class="td">${join( new Date(v.transaction_date), options, ' ')}</td>
                                        <td  style="width: 3%;"class="td font">${(v.narration||"")}</td>
                                        ${v.status==0 ?`<td  style="width: 3%; color:#0B55C4;"class="td font deliver "><input type="hidden" class="tran_id" value="${v.tran_id}">Create Bill</td>`
                                                      :`<td  style="width: 3%;"class="td font"></td>`}

                                    </tr>`);
                        });
                        $(".sales_order_body").html(htmlFragments.join(''));
                        set_scroll_table();
                        get_hover();
                    }
                });
                //redirect route
                $(document).ready(function() {
                    $('#tableId').on('click', '.deliver', function(e) {
                        e.preventDefault();
                        let id = $(this).find('.tran_id').val();
                        if (id) {
                            window.open(`{{ url('report/sales-order-vouchere') }}/${id}`, '_blank');
                        }
                    })
                });
                $(document).ready(function() {
                    $('#tableId').on('click', '.sales_order', function(e) {
                        e.preventDefault();
                        let id = $(this).closest('tr').attr('id').split(",");
                        if (id) {
                            window.open(`{{ url('show-salse-bill-order') }}/${id[0]}`, '_blank');
                        }

                    })
                });
            </script>
        @endpush
    @endsection
