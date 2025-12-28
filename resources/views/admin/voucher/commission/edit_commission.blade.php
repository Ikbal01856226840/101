@extends('layouts.backend.app')
@section('title', 'Edit Voucher Commission')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('libraries/css/jquery-ui.theme.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('libraries/css/jquery-ui.min.css') }}">
    <!-- model style -->
    <style>
        .form-control {
            font-size: 15px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }

        textarea.form-control {
            min-height: calc(1.5em + 0.75rem + 42px);
        }

        .customers th {
            background-color: #1AB0C3;
            color: white;
        }

        .table> :not(:first-child) {
            border-top: 0px solid currentColor;
        }

        .th {
            border: 1px solid #ddd;
            font-weight: bold;
        }

        .td1 {
            border: 1px solid #ddd;
            font-size: 18px;
            font-family: Arial, sans-serif;
        }

        .td2 {
            border: 1px solid #ddd;
            font-size: 16px;
            font-family: Arial, sans-serif;
        }


        .td-bold {
            font-weight: bold;
        }
    </style>
@endpush
@section('admin_content')
    <div class="pcoded-content" style="background-color: #e5e5cd!important">
        <div class="pcoded-inner-content">
            <br>
            <!-- Main-body start -->
            <div class="main-body">
                <div class="page-wrapper m-t-0 m-l-1 p-10">
                    <!-- Page-header start -->
                    <div class="page-header m-2 p-0">
                        <div class="row align-items-end" style="margin-bottom: 0%px !important;">
                            <div class="col-lg-8">
                                <div class="page-header-title m p-0" style="margin-bottom:7px !important;">
                                    <div class="d-inline">
                                        <h4 style="color: green;font-weight: bold;">{{ $voucher->voucher_name }} [Update]
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div style="float: right; margin-left: 5px;">
                                    <a style=" float:right;text-decoration: none; "
                                        href="{{ route('voucher-purchase.create') }}"><span class="fa fa-info-circle m-1"
                                            style="font-size:27px; color:#00b8e6;"></span><span
                                            style="float:right;margin:2px; padding-top:5px; color: color: white;#">Help</span></a>
                                </div>
                                <div style="float: right;margin-left:9px">
                                    <a style=" float:right;text-decoration: none; "
                                        href="{{ route('voucher-dashboard') }}"><span class="fa fa-times-circle-o m-1"
                                            style="font-size:27px; color:#ff6666;"></span><span
                                            style="float:right;margin:2px; padding-top:5px; ">Close</span></a>
                                </div>
                                <div style="float: right; margin-left:9px">
                                    <a style=" float: right;text-decoration: none; "
                                        href="{{ route('daybook-report.index') }}"><span class="fa fa-eye m-1"
                                            style="font-size:27px; color:#00b8e6;"></span><span
                                            style="float:right;margin:2px; padding-top:5px; ">View</span></a>
                                </div>

                            </div>
                            <hr style="margin-bottom: 0px;">
                        </div>

                    </div>
                    <div class="page-body">
                        <form id="show_commission_id" method="POST">
                            @csrf
                            <div class="row" style="border: 1px solid red;margin-left:3px;">
                                <div class="col-sm-4">
                                    <label>Party's A/C Name:</label>
                                    <select style="border-radius: 15px;" name="party_ledger_id"
                                        class="form-control js-example-basic-single party_ledger_id m-1" required>
                                        <option value="">--Select--</option>
                                        {!! html_entity_decode($ledger_commission_tree) !!}
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label>Commission Ledger : </label>
                                    <select style="border-radius: 15px;" name="commission_ledger_id"
                                        class="form-control js-example-basic-single commission_ledger_id m-1" required>
                                        <option value="">--Select--</option>
                                        {!! html_entity_decode($ledger_commission_tree) !!}
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label>Date From :</label>
                                            <input type="text" name="from_date" class="form-control from_date setup_date"
                                                value="{{ $data->commission_from_date }}" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Date To :</label>
                                            <input type="text" name="to_date" class="form-control to_date setup_date"
                                                value="{{ $data->commission_to_date }}" />
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="show_commission_id_submit_button" class="btn btn-info m-2"
                                    style="width: 200px;"><span class="m-t-1 m-1" style="color:#404040"><i
                                            class="fa fa-save"
                                            style="font-size:18px;"></i></span><span>Search</span></button>
                        </form>
                    </div>
                    <form id="edit_commission" method="POST">
                        @csrf
                        {{ method_field('PUT') }}

                        <div class="row">
                            <div class="col-sm-4">
                                <label style="float: left; margin:2px;">Invoice No:</label>
                                <input type="text" name="invoice_no" class="form-control m-1"
                                    value="{{ $data->invoice_no }}" style="color: green" required readonly />
                                <span id='error_voucher_no' class="text-danger"></span>
                                <input type="hidden" name="voucher_id" class="form-control voucher_id"
                                    value="{{ $voucher->voucher_id ?? '' }}" />
                                <input type="hidden" name="ch_4_dup_vou_no" class="form-control"
                                    value="{{ $voucher->ch_4_dup_vou_no ?? '' }}">
                                <input type="hidden" name="invoice" class="form-control"
                                    value="{{ $voucher->invoice ?? '' }}" />
                                <input type="hidden" name="party_ledger_id" class="form-control party_ledger" />
                                <input type="hidden" name="commission_ledger_id"
                                    class="form-control commission_ledger" />
                                <input type="hidden" name="commission_from_date"
                                    class="form-control commission_from_date"
                                    value="{{ $data->commission_from_date }}" />
                                <input type="hidden" name="commission_to_date" class="form-control commission_to_date"
                                    value="{{ $data->commission_to_date }}" />
                                <input type="hidden" name="credit_id" class="form-control"
                                    value="{{ $debit_credit_data[0]->debit_credit_id }}">
                                @if (isset($debit_credit_data[1]))
                                    <input type="hidden" name="debit_id" class="form-control"
                                        value="{{ $debit_credit_data[1]->debit_credit_id }}">
                                @else
                                    <input type="hidden" name="debit_id" class="form-control" value="">
                                @endif
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label style="float: left;  margin:2px; margin-right:29px;">Ref No:</label>
                                        <input type="text" name="ref_no" class="form-control m-1"
                                            value="{{ $data->ref_no }}" />
                                    </div>
                                    <div class="col-sm-6">
                                        <label style="float: left;  margin:2px; margin-right:29px;"> Date :</label>
                                        <input type="text"name="invoice_date"
                                            value="{{ $data->transaction_date }}"class="form-control setup_date" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <label style="margin-left:2px; ">Narration:</label>
                                <textarea style="margin:15px;" name="narration" rows="2.5" cols="2.5" class="form-control narration">{{ $data->narration ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="dt-responsive table-responsive cell-border sd">
                                <table id="example" style=" border-collapse: collapse; "
                                    class="table-striped customers table">
                                    <thead>
                                        <tr>
                                            <th style="width: 3%;  border: 1px solid #ddd;">SL</th>
                                            <th style="width: 3%;  border: 1px solid #ddd;">Particulars</th>
                                            <th style="width: 3%;  border: 1px solid #ddd;">Sales<br>Quantity</th>
                                            <th style="width: 3%;  border: 1px solid #ddd;">Sales<br>Eff. Rate</th>
                                            <th style="width: 3%;  border: 1px solid #ddd;">Sales<br>Value</th>
                                            <th style="width: 3%;  border: 1px solid #ddd;">Commission<br>[Per Quantity]
                                            </th>
                                            <th style="width: 3%;  border: 1px solid #ddd;">Commission<br>[% of Sales
                                                Value]</th>
                                            <th style="width: 3%;  border: 1px solid #ddd;">Total Commission</th>
                                        </tr>
                                    </thead>
                                    <tbody id="orders">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th style="width: 3%;  border: 1px solid #ddd;">SL</th>
                                            <th style="width: 3%;  border: 1px solid #ddd;"class=" td1 text-end td-bold">
                                                Total :</th>
                                            <th style="width: 3%;  border: 1px solid #ddd;" style="font-weight: bold;"
                                                class="sale_qty td1 td-bold text-end"></th>
                                            <th style="width: 3%;  border: 1px solid #ddd;" style="font-weight: bold;"
                                                class="sale_rate td1 td-bold text-end"></th>
                                            <th style="width: 3%;  border: 1px solid #ddd;" style="font-weight: bold;"
                                                class="sale_value td1 td-bold text-end"></th>
                                            <th style="width: 3%;  border: 1px solid #ddd;" style="font-weight: bold;"
                                                class="commission_per_qty td-bold text-end"></th>
                                            <th style="width: 3%;  border: 1px solid #ddd;"
                                                style="font-weight: bold;"class="commission_per_value text-end td-bold">
                                            </th>
                                            <th style="width: 3%;  border: 1px solid #ddd;">
                                                <input type="number" step="any" style="font-weight: bold;"
                                                    name="total_commission_per"
                                                    class="form-control total_commission_per td-bold text-end" readonly />
                                            </th>
                                        </tr>
                                        <tfoot>
                                </table>
                            </div>
                            @if ($voucher->secret_narration_is)
                                <div class="col-sm-12 mb-1">
                                    <label style="margin-left: 2px;">Secret Narration:</label>
                                    <textarea style="margin-left: 2px; border-radius: 15px;" name="secret_narration" rows="2.5" cols="2.5"
                                        class="form-control">{{ $data->secret_narration }}</textarea>
                                </div>
                            @endif
                        </div>
                        <div align="center">
                            @if ($data->voucher_status != 1)
                                @if (user_privileges_check('Voucher', $voucher->voucher_id, 'alter_role'))
                                    @if (voucher_modify_authorization($data->tran_id))
                                        <button type="submit" class="btn btn-info edit_commission_btn"
                                            style="width:120px;border-radius: 15px;">
                                            <span class="m-t-1 m-1" style="color:#404040"><i class="fa fa-save"
                                                    style="font-size:18px;"></i></span><span>Update</span>
                                        </button>
                                    @endif
                                @endif
                                @if (user_privileges_check('Voucher', $voucher->voucher_id, 'delete_role'))
                                    <button type="button" class="btn btn-danger deleteIcon"
                                        style="width:120px;border-radius: 15px;">
                                        <span class="m-t-1 m-1" style="color:#404040"><i class="fa fa-save"
                                                style="font-size:18px;"></i></span><span>Delete</span>
                                    </button>
                                @endif
                                <button type="button" class="btn btn-danger CancelIcon"
                                    style="width: 120px; border-radius: 15px;">
                                    <span class="m-t-1 m-1" style="color:#404040!important;"><i
                                            class="fa fa-times-circle"
                                            style="font-size: 20px;"></i></span><span>Cancel</span>
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    @push('js')
        <script type="text/javascript" src="{{ asset('libraries/js/jquery-ui.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('voucher_setup/voucherValidation.js') }}"></script>
        @if (isset($debit_credit_data[1]))
            <script>
                $('.commission_ledger_id').val('{{ $debit_credit_data[1]->ledger_head_id }}');
            </script>
        @endif

        <script>
            // ledger edit show
            $('.party_ledger_id').val('{{ $debit_credit_data[0]->ledger_head_id }}');
            let total_qty = 0,
                total_value = 0,
                i = 1;
            let stock_item_commission = @json($stock_item_commission);
            const amount_decimals = "{{ company()->amount_decimals }}";
            // party ledger
            $('.party_ledger').val($('.party_ledger_id').val());
            $('.party_ledger_id').on('change', function() {
                $('.party_ledger').val($('.party_ledger_id').val());
            });

            // commission ledger
            $('.commission_ledger').val($('.commission_ledger_id').val());
            $('.commission_ledger_id').on('change', function() {
                $('.commission_ledger').val($('.commission_ledger_id').val());
            });

            // from date
            $('.commission_from_date').val($('.from_date').val());
            $('.from_date').on('click change', function() {
                $('.commission_from_date').val($('.from_date').val());
            });

            // to date
            $('.commission_to_date').val($('.to_date').val());
            $('.to_date').on('click change', function() {
                $('.commission_to_date').val($('.to_date').val());
            });

            $(document).ready(function() {
                $('.to_date').change(function() {
                    $('#to_date').val($(this).val())
                })
                $('.from_date').change(function() {
                    $('#from_date').val($(this).val())
                })
                get_commission_voucher(stock_item_commission)
                // voucher setup and setting variable
                calculation_total();
                $('#orders').on('keyup change', '.commission_parqty', function(event) {

                    if (event.type === 'change' || (event.type === 'keyup' && isValidNumberInput(event))) {
                        let commission_parqty = $(this).closest('tr').find('.commission_parqty').val();
                        if (commission_parqty) {
                            let parqty = $(this).closest('tr').find('.parqty').val();
                            let par_total = $(this).closest('tr').find('.par_total').val();
                            let total_comm = parseFloat(((parqty) / (par_total)) * (100));
                            let commission_sale_value = parseFloat((total_comm) * (commission_parqty));
                            let commission_amount = parseFloat((parqty) * (commission_parqty));
                            $(this).closest('tr').find('.commission_sale_value').val(commission_sale_value
                                .toFixed(amount_decimals));
                            $(this).closest('tr').find('.commission_amount').val(commission_amount.toFixed(
                                amount_decimals));
                        } else {
                            $(this).closest('tr').find('.commission_sale_value').val(0);
                            $(this).closest('tr').find('.commission_amount').val(0);
                        }
                        calculation_total();
                    }


                });
                $('#orders').on('keyup change', '.commission_sale_value', function(event) {
                    if (event.type === 'change' || (event.type === 'keyup' && isValidNumberInput(event))) {
                        let commission_par_value = $(this).closest('tr').find('.commission_sale_value').val();
                        if (commission_par_value) {
                            let parqty = $(this).closest('tr').find('.parqty').val();
                            let par_total = $(this).closest('tr').find('.par_total').val();
                            let total_comm = parseFloat(((par_total) / (parqty)) / (100));
                            let commission_parqty = parseFloat((total_comm) * (commission_par_value));
                            let commission_amount = parseFloat(commission_parqty * parqty);
                            $(this).closest('tr').find('.commission_parqty').val(commission_parqty.toFixed(
                                amount_decimals));
                            $(this).closest('tr').find('.commission_amount').val(commission_amount.toFixed(
                                amount_decimals));
                        } else {
                            $(this).closest('tr').find('.commission_parqty').val(0);
                            $(this).closest('tr').find('.commission_amount').val(0);
                        }
                        calculation_total();
                    }
                });

            });

            // insert purchase
            $(document).ready(function() {
                $("#show_commission_id").submit(function(e) {
                    $("#show_commission_id_submit_button").prop('disabled', true);
                    e.preventDefault();
                    const fd = new FormData();
                    fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    fd.append('id', "{{ $data->tran_id }}");
                    fd.append('ledger_head_id', "{{ $debit_credit_data[0]?->ledger_head_id ?? 0 }}");
                    fd.append('commission_to_date', $('.to_date').val());
                    fd.append('commission_from_date', $('.from_date').val());

                    $.ajax({
                        url: '{{ url('show-commission-edit') }}',
                        method: 'post',
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(data) {
                            get_commission_voucher(data);
                        },
                        error: function(data) {
                            if (data.status == 404) {
                                swal_message(data.message, 'error', 'Error');
                            }
                            if (data.status == 422) {
                                $('#error_voucher_no').text(data.responseJSON.data.invoice_no[0]);
                            }
                        },
                        complete: function(xhr, status) {
                            $("#show_commission_id_submit_button").prop('disabled', false)
                        }
                    });
                });

                // delete commission ajax request
                $(document).on('click', '.deleteIcon', function(e) {
                    var csrf_token = $('meta[name="csrf-token"]').attr('content');
                    var id = "{{ $data->tran_id }}";
                    swal({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'No, cancel!',
                        confirmButtonClass: 'btn btn-success',
                        cancelButtonClass: 'btn btn-danger',
                        buttonsStyling: false,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.value) {
                            event.preventDefault();
                            $.ajax({
                                url: "{{ url('voucher-commission') }}" + '/' + id,
                                type: "POST",
                                data: {
                                    '_method': 'DELETE',
                                    '_token': csrf_token
                                },
                                success: function(data) {
                                    setTimeout(function() {
                                        window.location.href =
                                            '{{ url()->previous() }}';
                                    }, 100);
                                    swal_message(data.message, 'success', 'Successfully');
                                },
                                error: function() {
                                    swal_message(data.responseJSON.message, 'error',
                                        'Error');
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

            });

            function get_commission_voucher(response) {
                total_qty = 0, total_value = 0, i = 1;
                var tree = getTreeView(response.commission_ledger_voucher, response.sum_of_children);
                $('#orders').html(tree);
                get_hover();
                $('.sale_qty').text(total_qty.toFixed(amount_decimals));
                $('.sale_rate').text((((total_value || 0) / (total_qty || 0)) || 0).toFixed(amount_decimals));
                $('.sale_value').text(total_value.toFixed(amount_decimals));
                calculation_total();

            }

            function getTreeView(arr, children_sum, depth = 0, chart_id = 0) {
                let html = [];
                arr.forEach(function(v) {
                    a = '&nbsp;';
                    h = a.repeat(depth);

                    if (chart_id != v.stock_group_id) {
                        let matchingChild = children_sum.find(c => v.stock_group_id == c.stock_group_id);
                        if (((matchingChild.stock_qty || 0) == 0)) {} else {
                            html.push(
                                `<tr id="${v.stock_group_id+'-'+v.under}" class="left left-data table-row_tree">
                        <td style='width: 1%;  border: 1px solid #ddd;'></td>
                        <td style='width: 3%;' class="td1 td-bold"><p style="margin-left:${(h+a+a).length-12}px;cursor: default !important; font-size: 18px; font-family: Arial, sans-serif" class="text-wrap mb-0 pb-0 ">${v.stock_group_name}</p></td>`
                                );


                            if (matchingChild) {

                                html.push(`<td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">
                                ${(matchingChild.stock_qty||0).toFixed(amount_decimals)}
                            </td>
                        <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">
                            ${dividevalue(matchingChild.stock_total,matchingChild.stock_qty).toFixed(amount_decimals)}
                        </td>
                        <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">${(matchingChild.stock_total||0).toFixed(amount_decimals)}</td>
                        <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold"></td>
                        <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold"></td>
                        <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold"></td>

                        `);
                            }
                            html.push(`</tr>`);
                        }
                        chart_id = v.stock_group_id;
                    }

                    if (v.qty != null) {
                        total_qty += (v.qty || 0);
                        total_value += (v.total || 0);
                        let par_rate = Math.abs(dividevalue(v?.total, v?.qty))

                        html.push(`<tr id="${v.stock_item_id}" class="left left-data editIcon table-row">
                       <td class="sl" style="width: 1%;  border: 1px solid #ddd;">${i++}</td>
                       <td style="width: 5%;'" class="td2 item_name"><p style="margin-left :${(h+a+a+a).length-12}px; font-family: Arial, sans-serif" class="text-wrap mb-0 pb-0">${v.product_name}</p></td>
                       <td style='width: 2%;'class="td2 text-end">
                            ${(v?.qty||0).toFixed(amount_decimals)}
                            <input
                                type="hidden"
                                name="parqty[]"
                                value="${v?.qty || 0}"
                                class="parqty"
                            />
                        </td>
                       <td style='width: 2%;'class="td2 text-end">
                            ${par_rate.toFixed(amount_decimals)}
                            <input
                                type="hidden"
                                name="par_rate[]"
                                value="${par_rate}"
                                class="par_rate"
                            />
                            <input
                                type="hidden"
                                name="stock_item_id[]"
                                value="${v.stock_item_id}"
                            />
                            <input
                                type="hidden"
                                name="stock_comm_id[]"
                                value="${v.stock_comm_id}"
                            />

                        </td>
                       <td style='width: 3%;'class="td2 text-end">
                            ${((v.total||0)).toFixed(amount_decimals)}
                            <input
                                type="hidden"
                                name="par_total"
                                value="${v.total}"
                                class="par_total"
                            />

                        </td>
                       <td  style="width: 3%;  border: 1px solid #ddd;" class="td2 text-end">
                                <input type="number" step="any" name="commission_parqty[]" value="${v.com_rate||0}"  class="form-control commission_parqty td2 text-end" />
                        </td>
                        <td class="nature_val td2 text-end"  style="width: 3%;  border: 1px solid #ddd;">
                            <input type="number" step="any" name="commission_sale_value[]" value="${v.com_percent||0}" class="form-control commission_sale_value td2 text-end" />
                        </td>
                        <td class="nature_val td2 text-end"  style="width: 3%;  border: 1px solid #ddd;">
                            <input readonly type="number" step="any" name="commission_amount[]" value="${v.com_total||0}"class="form-control commission_amount td2 text-end" />
                        </td>

               </tr>`);
                    }

                    if ('children' in v) {
                        html.push(getTreeView(v.children, children_sum, depth + 1, chart_id));
                    }
                });

                return html.join("");
            }

            $(document).ready(function() {
                $("#edit_commission").submit(function(e) {
                    e.preventDefault();
                    var id = "{{ $data->tran_id }}";
                    const fd = new FormData(this);
                    $(".edit_commission_btn").text('Loading...');
                    $(".edit_commission_btn").prop('disabled', true);
                    $.ajax({
                        url: "{{ url('voucher-commission') }}" + '/' + id,
                        method: 'post',
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(data, status, xhr) {
                            swal_message(data.message, 'success');
                            $('#error_voucher_no').text('');
                            setTimeout(function() {
                                window.location.href = '{{ url()->previous() }}';
                            }, 100);
                        },
                        error: function(data, status, xhr) {
                            if(data.status==404){
                                    swal_message(data.responseJSON.message,'error','Error');
                            } if(data.status==422){
                                $('#error_voucher_no').text(data.responseJSON.data?.invoice_no && data.responseJSON.data?.invoice_no[0]);
                                let error=[];
                                for (const [key, value] of Object.entries(data.responseJSON?.data)) {
                                    error.push(`<p style="margin: 0; padding: 4px 0; color: red">${value}</p>`);
                                }
                                swal_message(data.responseJSON.message,'error',`${error.join('')}`);
                            }
                            $(".edit_commission_btn").text('Update');
                            $(".edit_commission_btn").prop('disabled', false);
                        },
                        complete: function(xhr, status) {
                            // This runs after success or error
                            
                        }
                    });
                });
            });

            // Cancelled payment ajax request
            $(document).on('click', '.CancelIcon', function(e) {
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                var id = "{{ $data->tran_id }}";
                var narration = $('.narration').val();
                swal(swal_data()).then((result) => {
                    if (result.value) {
                        event.preventDefault();
                        $.ajax({
                            url: "{{ url('voucher-commission-cancel') }}" + '/' + id,
                            type: "POST",
                            data: {
                                '_method': 'POST',
                                '_token': csrf_token,
                                'narration': narration
                            },
                            success: function(data) {
                                swal_message(data.message, 'success', 'Successfully');
                                setTimeout(function() {
                                    window.location.href = '{{ url()->previous() }}';
                                }, 100);
                            },
                            error: function(data) {
                                swal_message(data.responseJSON.message, 'error', 'Error');
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

            // calculation total
            function calculation_total() {

                let commission_amount = 0;
                $('#orders tr').each(function(i) {
                    if (parseFloat($(this).find('.commission_amount').val())) commission_amount += parseFloat($(this)
                        .find('.commission_amount').val());
                })
                $('.total_commission_per').val(parseFloat(commission_amount).toFixed(amount_decimals));

            }
        </script>

    @endpush
@endsection
