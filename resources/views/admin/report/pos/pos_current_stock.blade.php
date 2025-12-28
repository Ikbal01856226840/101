@extends('layouts.backend.app')
@section('title', 'POS Current Stock')
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
            font-weight: bold;
        }

        .td {
            border: 1px solid #ddd;
            font-size: 16px;
        }
    </style>
@endpush
@section('admin_content')<br>
    <!-- add component-->
    @component('components.report', [
        'title' => 'POS Current Stock',
        'print_layout' => 'landscape',
        'print_header' => 'POS Current Stock',
        'user_privilege_title' => 'POSCurrentStock',
    ])
        ;

        <!-- Page-header component -->
        @slot('header_body')
            <form id="pos_current_form" method="POST">
                @csrf
                {{ method_field('POST') }}
                <div class="row">
                    <div class="col-md-4">
                        <label>Stock Group :</label>
                        <select name="stock_group_id" class="form-control js-example-basic-single group_id" required>
                            <option value="">--Select--</option>
                            <option value="0">Primary</option>
                            {!! html_entity_decode($stock_group) !!}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="row m-0 p-0">
                            <div class="col-md-6 m-0 p-0">
                                <label>Date From: </label>
                                <input type="text" name="from_date" class="form-control setup_date fs-5 from_date"
                                    value="{{ company()->financial_year_start }}">
                            </div>
                            <div class="col-md-6 m-0 p-0">
                                <label>Date To : </label>
                                <input type="text" name="to_date" class="form-control setup_date fs-5 to_date"
                                    value="{{ financial_end_date(date('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label></label><br>
                        <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit"
                            style=" width:200px; margin-bottom:5px;"><span class="m-t-1 m-1"></span><span>Search</span></button>
                    </div>
                </div>
            </form>
        @endslot

        <!-- Main body component -->
        @slot('main_body')
            <div class="dt-responsive table-responsive cell-border sd tableFixHead_report">
                <table id="tableId" style=" border-collapse: collapse; " class="table-striped customers table">
                    <thead>
                        <tr>
                            <th style="width: 1%;"class="th">SL.</th>
                            <th style="width: 3%;"class="th">Particulars</th>
                            <th style="width: 2%;"class="th">Opening Quantity</th>
                            <th style="width: 3%;"class="th">Inwards Quantity</th>
                            <th style="width: 2%;"class="th">Outwards Quantity</th>
                            <th style="width: 3%;"class="th">Clasing Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="myTable" class="item_body">
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="width: 1%;  "></th>
                            <th style="width: 3%;  ">Total :</th>
                            <th style="width: 2%; font-size: 18px;" class="total_opening th"></th>
                            <th style="width: 3%; font-size: 18px;" class="total_inwards th"></th>
                            <th style="width: 2%; font-size: 18px;" class="total_outwards th"></th>
                            <th style="width: 3%; font-size: 18px;" class="total_clasing th"></th>
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
        <script type="text/javascript" src="{{ asset('dist/jquery-simple-tree-table.js') }}"></script>
        <script>
            var amount_decimals = "{{ company()->amount_decimals }}";
            let total_opening = 0;
            total_inwards = 0;
            total_outwards = 0;
            total_clasing = 0;

            // pos current product quantity
            $(document).ready(function() {
                $("#pos_current_form").submit(function(e) {
                    total_opening = 0;
                    total_inwards = 0;
                    total_outwards = 0;
                    total_clasing = 0;
                    e.preventDefault();
                    const fd = new FormData(this);
                    $.ajax({
                        url: '{{ route('report-warehousewise-stock-data') }}',
                        method: 'POST',
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                            result = [];
                            get_pos_current_stock_product(response)
                        },
                        error: function(data, status, xhr) {

                        }
                    });
                });

                // warehose product quantity function
                function get_pos_current_stock_product(response) {
                    const children_sum = calculateSumOfChildren(response.data);
                    var tree = getTreeView(response.data, children_sum);
                    $('.item_body').html(tree);
                    get_hover();
                    $('.total_opening').text(total_opening.toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g,
                    '$&,'));
                    $('.total_inwards').text(total_inwards.toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g,
                    '$&,'));
                    $('.total_outwards').text(total_outwards.toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g,
                        '$&,'));
                    $('.total_clasing').text(total_clasing.toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g,
                    '$&,'));
                }
            });

            var result = [];

            // // calcucation child summation
            function calculateSumOfChildren(arr) {
                const result = {};

                function sumProperties(obj, prop) {
                    return obj.reduce((acc, val) => acc + (val[prop] || 0), 0);
                }

                function processNode(node) {
                    if (!result[node.stock_group_id]) {
                        result[node.stock_group_id] = {
                            stock_group_id: node.stock_group_id,
                            stock_qty_in: 0,
                            stock_qty_out: 0,
                            stock_qty_in_opening: 0,
                            stock_qty_out_opening: 0,
                        };
                    }

                    const currentNode = result[node.stock_group_id];

                    currentNode.stock_qty_in += node.stock_qty_in || 0;
                    currentNode.stock_qty_out += node.stock_qty_out || 0;
                    currentNode.stock_qty_in_opening += node.stock_qty_in_opening || 0;
                    currentNode.stock_qty_out_opening += node.stock_qty_out_opening || 0;
                    if (node.children) {
                        node.children.forEach(processNode);
                    }
                }

                arr.forEach(processNode);

                return Object.values(result);
            }
            let i = 1;

            function getTreeView(arr, children_sum, depth = 0, chart_id = 0) {
                let htmlFragments = [];
                arr.forEach(function(v) {
                    a = '&nbsp;&nbsp;';
                    h = a.repeat(depth);

                    if (chart_id != v.stock_group_id) {
                        htmlFragments.push(
                            `<tr class='left left-data editIcon'>
                                    <td style='width: 1%; border: 1px solid #ddd;'></td>
                                    <td style='width: 3%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4'><span><span class='table-row_tree'>${h + a + v.stock_group_name}</span></span></td>`
                            );

                        let matchingChild = children_sum.find(c => v.stock_group_id == c.stock_group_id);
                        if (matchingChild) {
                            htmlFragments.push(
                                `<td style='width: 3%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4'>${(((matchingChild.stock_qty_in_opening || 0) - (matchingChild.stock_qty_out_opening || 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'))}</td>
                                            <td style='width: 3%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4'>${(((matchingChild.stock_qty_in || 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'))}</td>
                                            <td style='width: 3%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4'>${(((matchingChild.stock_qty_out || 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'))}</td>
                                            <td style='width: 3%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4'>${((((matchingChild.stock_qty_in_opening || 0) - (matchingChild.stock_qty_out_opening || 0) + (matchingChild.stock_qty_in || 0)) - (matchingChild.stock_qty_out || 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'))}</td>`
                                );
                        }

                        htmlFragments.push(`</tr>`);
                        chart_id = v.stock_group_id;
                    }

                    if (v.stock_in_sum_qty_op != null || v.stock_out_sum_qty_op != null || v.stock_in_sum_qty != null ||
                        v.stock_out_sum_qty != null) {
                        total_opening += ((v.stock_in_sum_qty_op || 0) - (v.stock_out_sum_qty_op || 0));
                        total_inwards += (v.stock_in_sum_qty || 0);
                        total_outwards += (v.stock_out_sum_qty || 0);
                        total_clasing += (((v.stock_in_sum_qty_op || 0) - (v.stock_out_sum_qty_op || 0) + (v
                            .stock_in_sum_qty || 0)) - (v.stock_out_sum_qty || 0));

                        htmlFragments.push(`<tr id="${v.product_name}" data-node-id="" data-node-pid="${v.stock_group_id}" class="left left-data editIcon table-row" data-toggle="modal" data-target="#EditLedgerModel">
                                    <td class="sl" style="width: 1%; border: 1px solid #ddd;">${i++}</td>
                                    <td style="width: 5%;" class="td"><span>${h + h + a + v.product_name}<span></td>
                                    <td style='width: 3%;' class='opening td'>${(((v.stock_in_sum_qty_op || 0) - (v.stock_out_sum_qty_op || 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'))}</td>
                                    <td style='width: 3%;' class='inwards td'>${(((v.stock_in_sum_qty || 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'))}</td>
                                    <td style='width: 3%;' class='outwards td'>${(((v.stock_out_sum_qty || 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'))}</td>
                                    <td style='width: 3%;' class='clasing td'>${((((v.stock_in_sum_qty_op || 0) - (v.stock_out_sum_qty_op || 0) + (v.stock_in_sum_qty || 0)) - (v.stock_out_sum_qty || 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'))}</td>
                                </tr>`);
                    }

                    if ('children' in v) {
                        htmlFragments.push(getTreeView(v.children, children_sum, depth + 1, chart_id));
                    }
                });

                return htmlFragments.join('');
            }
        </script>
    @endpush
@endsection
