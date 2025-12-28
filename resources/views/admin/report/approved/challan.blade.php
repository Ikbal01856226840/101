@extends('layouts.backend.app')
@section('title', 'Challan')
@push('css')
    <!-- model style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('libraries/assets/modal-style.css') }}">
    <style>
        .card-block {
            padding: 0.25rem;
        }

        .card {
            margin-bottom: -29px;
        }

        body {
            overflow: auto !important;
        }

        .th {
            width: 3%;
            border: 1px solid #ddd;
        }

        .td {
            width: 3%;
            border: 1px solid #ddd;
        }
    </style>
@endpush
@section('admin_content')
    @component('components.setting_modal', [
        'id' => 'exampleModal',
        'class' => 'modal fade',
        'page_title' => 'challan',
        'page_unique_id' => 15,
        'title' => 'Challan',
        'sort_by' => 'sort_by',
        'insert_settings' => 'insert_settings',
        'view_settings' => 'view_settings',
    ])
    @endcomponent
    <div class="coded-main-container navChild">
        <div class="pcoded-content">
            <div class="pcoded-inner-content"><br>
                <!-- Main-body start -->
                <div class="main-body side-component p-0">
                    <div class="page-wrapper m-t-0 p-0">
                        <div class="page-wrapper m-t-0 m-l-1 m-r-1 p-2">
                            <!-- Page-header start -->
                            <div class="page-header m-0 p-0">
                                <div class="row align-items-left">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="page-header-title">
                                                    <h4>Challan</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div style="float: right; margin-left: 5px;">
                                                    <a style=" float:right; text-decoration: none; "
                                                        href="{{ route('master-dashboard') }}"><span
                                                            class="fa fa-times-circle-o m-1"
                                                            style="font-size:27px; color:#ff6666;"></span><span
                                                            style="float:right;margin:2px; padding-top:5px; ">Close</span></a>
                                                </div>
                                                <div style="float: right; margin-left: 5px;">
                                                    <a style=" float:right ;text-decoration: none; cursor: pointer"
                                                        data-toggle="modal" data-target="#exampleModal"><span
                                                            class="fa fa-cog m-1"
                                                            style="font-size:27px;  color:Green;"></span><span
                                                            style="float:right;margin:2px; padding-top:5px; ">Setting</span></a>
                                                </div>
                                                @if (user_privileges_check('report', 'Challan', 'print_role'))
                                                    <div style="float: right;margin-left:9px">
                                                        <a style="float:right; text-decoration: none;cursor: pointer; "
                                                            onclick="print_html('landscape','Stock Item Price')"><span
                                                                class="fa fa-print m-1"
                                                                style="font-size:27px; color:teal;"></span><span
                                                                style="float:right;margin:2px; padding-top:5px;">Print</span></a>
                                                    </div>
                                                    <div style="float: right;margin-left:9px">
                                                        <a style="float:right; text-decoration: none;cursor: pointer;"
                                                            class="excel"
                                                            onclick="exportTableToExcel('Stock Item Price')"><span
                                                                class="fa fa-file-excel-o m-1"
                                                                style="font-size:25px; color:Gray;"></span><span
                                                                style="float:right;margin:2px; padding-top:5px;">Excel</span></a>
                                                    </div>
                                                    <div style="float: right;margin-left:9px">
                                                        <a style="float:right; text-decoration: none;cursor: pointer;"
                                                            class="pdf_download"
                                                            onclick="generateTable('Stock Item Opening')"><span
                                                                class="fa fa-file-pdf-o m-1"
                                                                style="font-size:25px; color:MediumSeaGree; "></span><span
                                                                style="float:right;margin:2px; padding-top:5px;">Pdf</span></a>
                                                    </div>
                                                @endif
                                                <div style="float: right; width:200px;">
                                                    <input type="text" id="myInput" style="border-radius: 5px"
                                                        class="form-control form-control pb-1" width="100%"
                                                        placeholder="searching">
                                                </div>
                                            </div>
                                            <hr style="margin-bottom: 0px;">
                                        </div>
                                    </div>
                                    <!-- Page-header end -->
                                    <!-- Page-body start -->
                                    <div class="page-body">
                                        <div class="row">
                                            <div class="page-header m-0">
                                                <form id="challan_form" method="POST">
                                                    @csrf
                                                    {{ method_field('POST') }}
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <label>Voucher Type : </label>
                                                            <select name="voucher_id"
                                                                class="form-control js-example-basic-single voucher_id">
                                                                {{-- @if (Auth()->user()->user_level == 1) --}}
                                                                <option value="0">--ALL--</option>
                                                                {{-- @endif --}}
                                                                @php $voucher_type_id= 0; @endphp
                                                                @foreach ($vouchers as $voucher)
                                                                    @if ($voucher_type_id != $voucher->voucher_type_id)
                                                                        @php $voucher_type_id=$voucher->voucher_type_id; @endphp
                                                                        @if (Auth()->user()->user_level == 1)
                                                                            <option style="color:red;"
                                                                                value="v{{ $voucher->voucher_type_id ?? '' }}">
                                                                                {{ $voucher->voucher_type ?? '' }}</option>
                                                                        @elseif($voucher->filtered_count == $voucher->total_count)
                                                                            <option style="color:red;"
                                                                                value="v{{ $voucher->voucher_type_id ?? '' }}">
                                                                                {{ $voucher->voucher_type ?? '' }}</option>
                                                                        @endif
                                                                    @endif
                                                                    <option value="{{ $voucher->voucher_id }}">
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $voucher->voucher_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label>Distributon Center:</label>
                                                            <select name="dis_cen_id"
                                                                class="form-control js-example-basic-single" required>
                                                                <option value="">--Select One--</option>
                                                                @foreach ($distributions as $distribution)
                                                                    <option value="{{ $distribution->dis_cen_id }}">
                                                                        {{ $distribution->dis_cen_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="row m-0 p-0">
                                                                <div class="col-md-6 start_date m-0 p-0">
                                                                    <label>Date From: </label>
                                                                    <input type="text" name="from_date"
                                                                        class="form-control setup_date fs-5 from_date"
                                                                        value="{{ financial_end_date(date('Y-m-d')) }}"
                                                                        name="narratiaon">
                                                                </div>
                                                                <div class="col-md-6 end_date m-0 p-0">
                                                                    <label>Date To : </label>
                                                                    <input type="text" name="to_date"
                                                                        class="form-control setup_date fs-5 to_date"
                                                                        value="{{ financial_end_date(date('Y-m-d')) }}"
                                                                        name="narratiaon">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>Delivery Status:</label>
                                                            <select name="delivery_status"
                                                                class="form-control js-example-basic-single delivery_status">
                                                                <option value="">ALL</option>
                                                                <option value="1">Delivery Pending</option>
                                                                <option value="2">Delivery Complete</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label></label>
                                                            <div class="form-group">
                                                                <button type="submit"
                                                                    class="btn hor-grd btn-grd-primary btn-block submit"
                                                                    style="width:100%">Search</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Page-body start -->
                                <div class="page-body left-data">
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <!-- Zero config.table start -->
                                            <div class="card">
                                                <div class="card-block table_content">
                                                    <div
                                                        class="dt-responsive table-responsive cell-border sd tableFixHead_challan_report">
                                                        <table id="tableId" style=" border-collapse: collapse;"
                                                            class="customers table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="th">SL</th>
                                                                    <th class="th">Date</th>
                                                                    <th class="th">Particulars</th>
                                                                    <th class="th">Voucher<br>Type</th>
                                                                    <th class="th">Invoice<br>No</th>
                                                                    <th class="th">Voucher Status</th>
                                                                    <th class="th">Action</th>

                                                                </tr>
                                                            </thead>
                                                            <tbody class="qw" id="myTable">
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th class="th">SL</th>
                                                                    <th class="th">Date</th>
                                                                    <th class="th">Particulars</th>
                                                                    <th class="th">Voucher<br>Type</th>
                                                                    <th class="th">Invoice<br>No</th>
                                                                    <th class="th">Voucher Status</th>
                                                                    <th class="th">Action</th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                @push('js')
                    <!-- table hover js -->
                    <script type="text/javascript" src="{{ asset('libraries/assets/table-hover.js') }}"></script>
                    <script type="text/javascript" src="{{ asset('pageWiseSetting/page_wise_setting.js') }}"></script>
                    <script>
                        let user_level = @json(Auth()->user()->user_level);
                        $(function() {
                            // add new challan ajax request
                            $("#challan_form").submit(function(e) {
                                e.preventDefault();
                                const fd = new FormData(this);
                                $.ajax({
                                    url: '{{ route('challan-data') }}',
                                    method: 'post',
                                    data: fd,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    dataType: 'json',
                                    success: (response) => {
                                        var fragment = document.createDocumentFragment();
                                        $.each(response.data, function(key, v) {
                                            var row = document.createElement('tr');
                                            row.className = 'left left-data table-row';
                                            row.setAttribute('data-toggle', 'modal');

                                            row.innerHTML = `
                                                    <td class="sl td">${key + 1}</td>
                                                    <td class="td">${join(new Date(v.transaction_date), options, ' ')}</td>
                                                    <td class="td text-wrap">${v.ledger_name||''}</td>
                                                    <td style="color:#0B55C4;" class="td text-wrap"><input type="hidden" class="voucher_name" value="${v.tran_id}" />${v.voucher_name|| ''}</td>
                                                    <td class="td">${v.invoice_no ||''}</td>
                                                    <td style="font-size: 16px;" class="delivery_complede td text-wrap">
                                                        ${v.delivery_status == 1 ? `<div style="color:black;"><i>Delivery Pending <br>${v.order_approver ? JSON.parse(v.order_approver) : ''}</i></div>` : v.delivery_status==2?'<i>Delivery Completed  <br>' + (v.order_approver ? JSON.parse(v.order_approver) : '') + '</i></div> ':''}
                                                    </td>
                                                    ${user_level == 5 ? `<td style="color:#0B55C4; font-size: 16px;" class="${v.delivery_status == 1 ? `deliveryIcon`:''} td">
                                                        ${v.delivery_status == 1 ? `<input type="hidden" class="delivery" value="${v.tran_id}" />Delivery Now` : ''}
                                                    </td>`:''}
                                                `;

                                            fragment.appendChild(row);
                                        });

                                        // Clear existing content before appending
                                        $('.qw').empty();
                                        $('.qw')[0].appendChild(fragment);

                                        page_wise_setting_checkbox();
                                        get_hover();
                                    },
                                    error: function(data, status, xhr) {
                                        if (data.status == 400) {
                                            swal({
                                                title: 'Oops...',
                                                text: data.message,
                                                type: 'error',
                                                timer: '1500'
                                            });
                                        }
                                    }
                                });
                            });
                        });
                        //get  all data show
                        $(document).ready(function() {
                            $('#tableId').on('click', 'td', function(e) {
                                e.preventDefault();
                                let id = $(this).find('.voucher_name').val();
                                if (id) {
                                    window.open(`{{ url('show-challan-approve') }}/${id}`, '_blank');
                                }
                            });
                            let display_height = $(window).height();
                            $('.tableFixHead_challan_report').css('height', `${display_height-130}px`);

                        });
                        // delete challan ajax request
                        $(document).on('click', '.deliveryIcon', function(e) {
                            var csrf_token = $('meta[name="csrf-token"]').attr('content');
                            var id = $(this).find('.delivery').val();
                            swal({
                                title: 'Are you sure?',
                                text: "You won't be able to revert this!",
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, approve it!',
                                cancelButtonText: 'No, cancel!',
                                confirmButtonClass: 'btn btn-success',
                                cancelButtonClass: 'btn btn-danger',
                                buttonsStyling: false,
                                reverseButtons: true
                            }).then((result) => {
                                if (result.value) {
                                    event.preventDefault();
                                    $.ajax({
                                        url: "{{ url('delivery-approved') }}" + '/' + id,
                                        type: "GET",
                                        success: (response) => {
                                            let html =
                                                '<div style="color:black;" ><i>Delivery Completed  <br>' + (
                                                    response.data.other_details ? JSON.parse(response.data
                                                        .other_details) : '') + '</i></div>';
                                            $(this).closest('tr').find('.deliveryIcon').text('');
                                            $(this).closest('tr').find('.delivery_complede').html(html);
                                            swal_message(response.message, 'success', 'Successfully');
                                        },
                                        error: function() {
                                            swal_message(response.message, 'error', 'Error');
                                        }
                                    });
                                } else if (
                                    // Read more about handling dismissals
                                    result.dismiss === swal.DismissReason.cancel
                                ) {
                                    swal(
                                        'Cancelled',
                                        'Your data is safe :)',
                                        'error'
                                    )
                                }
                            })
                        });
                        // alert message
                        function swal_message(data, message, m_title) {
                            swal({
                                title: m_title,
                                text: data,
                                type: message,
                                timer: '1500'
                            });

                        }
                    </script>
                @endpush
            @endsection
