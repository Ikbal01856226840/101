@extends('layouts.backend.app')
@section('title','Day Wise Current Stock')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
<style>
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
<!-- setting component-->
@component('components.report_setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'size'=>'modal-xl',
    'page_title'=>'Day Wise Current Stock',
    'page_unique_id'=>46,
    'ledger'=>'yes',
    'title'=>'Day Wise Current Stock',
    'daynamic_function'=>'get_ledger_analysis_initial_show',
])
@endcomponent

<!-- add component-->
@component('components.report', [
'title' => 'Day Wise Current Stock',
'print_layout'=>'landscape',
'print_header'=>'Day Wise Current Stock',
'user_privilege_title'=>'DayWiseCurrentStock',
'print_date'=>1
]);

<!-- Page-header component -->
@slot('header_body')
<form id="current_stock" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row ">
        <div class="col-md-3">
            <label>Stock Group :</label>
            <select name="stock_group_id" class="form-control  js-example-basic-single  group_id" required>
                <option value="">--Select--</option>
                <option value="0">Primary</option>
                {!!html_entity_decode($stock_group)!!}
            </select>
            <input type="hidden" name="godown_id"  value="0">
        </div>
        {{-- <div class="col-md-3">
            <label>Godown Name :</label>
            <select name="godown_id" class="form-control  js-example-basic-single" required>
                <option value="0">All</option>
                @foreach($godowns as $godown)
                <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                @endforeach
            </select>
        </div> --}}
        <div class="col-md-3">
            <div class="row  m-0 p-0">
                <div class="col-md-6 m-0 p-0">
                    <label>Date From: </label>
                    <input type="text" name="from_date" class="form-control  fs-5 from_date" value="{{financial_end_date(date('Y-m-d'))}}" >
                </div>
                <div class="col-md-6 m-0 p-0">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control  fs-5 to_date" value="{{financial_end_date(date('Y-m-d'))}}">
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <label></label><br>
            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;"><span class="m-1 m-t-1"></span><span>Search</span></button>
        </div>
    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_report">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers ">
        <thead>
            <tr>
                <th style="width: 1%;" class="th">SL.</th>
                <th style="width: 2%;" class="th">Particulars</th>
                <th style="width: 1%;" class="th text-end">Clasing Quantity</th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;  "></th>

                <th style="width: 2%;  text-align: right">Total :</th>
                <th style="width: 1%; font-size: 18px;"  class="total_clasing th text-end"></th>
            </tr>
        </tfoot>
    </table>
</div>
@endslot
@endcomponent

@push('js')
<!-- table hover js -->
<script>
    var amount_decimals = "{{company()->amount_decimals}}";
    let  total_clasing = 0;
   
    i = 1;

    // warehose product quantity
    $(document).ready(function() {
        $("#current_stock").submit(function(e) {
            $(".modal").show();
            total_clasing = 0;
            i = 1;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("current-stock-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_warehouse_wise_product(response)
                },
                error: function(data, status, xhr) {
                    Unauthorized(data.status);
                }
            });
        });

        // warehose product quantity function
        function get_warehouse_wise_product(response) {
            const children_sum = calculateSumOfChildren(response.data);
            var tree = getTreeView(response.data, children_sum);
            $('.item_body').html(tree);
            get_hover();
            $('.total_clasing').text(total_clasing.toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
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
    i = 1;

    function getTreeView(arr, children_sum, depth = 0, chart_id = 0) {
        let htmlFragments = [];
        arr.forEach(function(v) {
            a = '&nbsp;';
            h = a.repeat(depth);

            if (chart_id != v.stock_group_id) {
                let matchingChild = children_sum.find(c => v.stock_group_id == c.stock_group_id);
                if (((matchingChild.stock_qty_in_opening|| 0) == 0) && ((matchingChild.stock_qty_out_opening || 0) == 0) && ((matchingChild.stock_qty_in || 0) == 0) && ((matchingChild.stock_qty_out || 0) == 0)) {} else {
                    htmlFragments.push(`<tr class='left left-data editIcon'>
                                    <td style='width: 1%; border: 1px solid #ddd;'></td>
                                    <td style='width: 3%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4'><p style="margin-left:${(h+a).length-12}px;cursor: default !important; font-size: 18px; font-family: Arial, sans-serif" class="text-wrap mb-0 pb-0 ">${v.stock_group_name}</p></td>`);
                if (matchingChild) {
                    htmlFragments.push(`<td style='width: 2%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4' class="text-end">${((((matchingChild.stock_qty_in_opening || 0) - (matchingChild.stock_qty_out_opening || 0) + (matchingChild.stock_qty_in || 0)) - (matchingChild.stock_qty_out || 0))).formatBangladeshCurrencyType("quantity")}</td>`);
                }
            }
                htmlFragments.push(`</tr>`);
                chart_id = v.stock_group_id;
            }


            if ((((v.stock_in_sum_qty_op || 0) - (v.stock_out_sum_qty_op || 0)) == 0)&& ((v.stock_in_sum_qty || 0) == 0) && ((v.stock_out_sum_qty || 0) == 0)) {} else {
               
                total_clasing += (((v.stock_in_sum_qty_op || 0) - (v.stock_out_sum_qty_op || 0) + (v.stock_in_sum_qty || 0)) - (v.stock_out_sum_qty || 0));

                htmlFragments.push(`<tr id="${v.product_name}" data-node-id="" data-node-pid="${v.stock_group_id}" class="left left-data editIcon table-row" data-toggle="modal" data-target="#EditLedgerModel">
                                    <td class="sl" style="width: 1%; border: 1px solid #ddd;">${i++}</td>
                                    <td style="width: 5%;" class="td"><p style="margin-left:${(h+a).length-12}px;cursor: default !important; font-size: 18px; font-family: Arial, sans-serif" class="text-wrap mb-0 pb-0 ">${v.product_name}</p></td>
                                    <td style='width: 3%;' class='clasing td text-end'>${((((v.stock_in_sum_qty_op || 0) - (v.stock_out_sum_qty_op || 0) + (v.stock_in_sum_qty || 0)) - (v.stock_out_sum_qty || 0))).formatBangladeshCurrencyType("quantity",v.symbol)}</td>
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
