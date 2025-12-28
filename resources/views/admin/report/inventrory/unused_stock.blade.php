
@extends('layouts.backend.app')
@section('title','Unused Stock')
@push('css')
 <style>

    .th{
        border: 1px solid #ddd;font-weight: bold !important;
    }
    .td{
        border: 1px solid #ddd; font-size: 16px  !important;
        font-family: Arial, sans-serif;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
                                line-height: 18px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
                                line-height: 18px !important;
    }
    .radio-custom {
    width: 25px !important; /* Adjust width */
    height: 25px !important; /* Adjust height */

   }

    .form-check-label {
        font-size: 14px; /* Adjust label size */
    }
    .in_min_width{
        min-width: 240px
    }
    @media (max-width: 575.98px) {
        .fns-9 {
            font-size: 11px;
        }
        .col-4{
            margin: 0px;
            padding: 0px;
        }
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
    'size'=>'modal-xl',
    'page_title'=>'Unused Stock',
    'page_unique_id'=>21,
    'godown'=>'yes',
    'stockGroup'=>'yes',
    'title'=>'Unused Stock',
    'daynamic_function'=>'get_unused_stock_initial_show',
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Unused Stock',
    'print_layout'=>'portrait',
    'print_header'=>'Unused Stock',
    'user_privilege_title'=>'StockItemUnusedStock',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="unused_stock_form"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-4">
                        <label>Stock Group :</label>
                        <select name="stock_group_id" class="form-control  js-example-basic-single  stock_group_id" required>
                            <option value="0">Primary</option>
                            {!!html_entity_decode($stock_group)!!}
                        </select>
                        <div class="row px-2">
                            <div class="col-md-6">
                                <label>Date From: </label>
                                <input type="text" name="from_date" class="form-control setup_date from_date" value="{{financial_end_date(date('Y-m-d'))}}">
                            </div>
                            <div class="col-md-6">
                                <label>Date To : </label>
                                <input type="text" name="to_date" class="form-control setup_date to_date" value="{{financial_end_date(date('Y-m-d'))}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Godown Name:</label>
                        <select name="godown_id[]" class="form-control js-example-basic-multiple godown_id" multiple="multiple" required>
                            <option value="0" selected>All</option>
                            @foreach($godowns as $godown)
                            <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label></label>
                        <div class="form-group mb-0" style="position: relative">
                            <label class="fs-5 in_min_width">show if Inward Balance :</label>
                            <input class="form-check-input in_is radio-custom " type="radio" name="in_is" value="1"  checked >
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                                is any
                            </label>
                            <input class="form-check-input in_is radio-custom fs-5" type="radio" name="in_is" value="2"  >
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                                is 0
                            </label>
                            <input class="form-check-input in_is radio-custom fs-5" type="radio" name="in_is" value="3">
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                                is not 0.
                            </label>
                        </div>
                        <div class="form-group m-0 p-0" style="position: relative">
                            <label class="fs-5 ">show if Outward Balance :</label>
                            <input class="form-check-input out_is radio-custom" type="radio" name="out_is" value="1" checked >
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                                is any
                            </label>
                            <input class="form-check-input out_is radio-custom" type="radio" name="out_is" value="2" >
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                                is 0
                            </label>
                            <input class="form-check-input out_is radio-custom" type="radio" name="out_is"  value="3">
                            <label class="form-check-label fs-5" for="flexRadioDefault1">
                                is not 0.
                            </label>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-2 col-sm-12 mt-4 d-flex align-items-start justify-content-center">
                <button type="submit" class="btn btn-primary hor-grd btn-grd-primary w-100 submit" style="max-width: 200px; margin-bottom: 5px;">Search</button>
            </div>
        </div>
    </form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_unused_stock">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <thead>
                <tr>
                    <th  style="width: 1%; text-align:center;" class="th align-middle">SL.</th>
                    <th  style="width: 5%; text-align:center;table-layout: fixed;"class="th align-middle" >Particulars</th>
                    <th  style=" width: 5%;" class="th inwards_text  text-end in_wards">Opening Balance</th>
                    <th  style=" width: 5%;" class="th outwards_text  text-end out_wards">Inward Balance</th>
                    <th  style=" width: 5%;" class="th outwards_text  text-end out_wards">Outward Balance</th>
                </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;" class="th"></th>
                <th style="width: 3%;" class="th">Total :</th>
                <th style="width: 2%; font-size: 18px;"  class="th total_opening text-end in_wards"></th>
                <th style="width: 3%; font-size: 18px;"  class="th total_inwards text-end out_wards"></th>
                <th style="width: 3%; font-size: 18px;"  class="th total_outwards text-end out_wards"></th>
            </tr>
        </tfoot>
    </table>
</div>
@endslot
@endcomponent
<br>
@push('js')
<script>

    let total_opening = 0;
    total_inwards = 0;
    total_outwards = 0;
    total_clasing = 0;
    i=1;
    // warehose product quantity
    $(document).ready(function() {
        local_store_unused_stock_get();
        get_unused_stock_initial_show();
        $("#unused_stock_form").submit(function(e) {
            print_date();
            $(".modal").show();
            total_opening = 0;
            total_inwards = 0;
            total_outwards = 0;
            total_clasing = 0;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("report-stock-item-unnsed-stock-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_unused_stock(response)
                },
                error: function(data, status, xhr) {
                    Unauthorized(data.status);
                }
            });
        });
    });

   // warehose product quantity function
   function get_unused_stock(response) {
            const children_sum = calculateSumOfChildren(response.data);
            var tree = getTreeView(response.data, children_sum);
            $('.item_body').html(tree);
            get_hover();
            $('.total_opening').text(total_opening.formatBangladeshCurrencyType("quantity"));
            $('.total_inwards').text(total_inwards.formatBangladeshCurrencyType("quantity"));
            $('.total_outwards').text(total_outwards.formatBangladeshCurrencyType("quantity"));

    }

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
   i=1;
    function getTreeView(arr, children_sum, depth = 0, chart_id = 0) {
        let htmlFragments = [];
        arr.forEach(function(v) {
            a = '&nbsp;';
            h = a.repeat(depth);

            if (chart_id != v.stock_group_id) {
                if(($("input[type='radio'].in_is:checked").val()==1)&&($("input[type='radio'].out_is:checked").val()==1)){

                    unused_stock_group(htmlFragments,v,i,a,children_sum);
                }

                chart_id = v.stock_group_id;
            }
            if(($("input[type='radio'].in_is:checked").val()==1)&&($("input[type='radio'].out_is:checked").val()==1)){
             unused_stock_item(htmlFragments,v,i,a);
            }else if(($("input[type='radio'].in_is:checked").val()==2)&&($("input[type='radio'].out_is:checked").val()==2)){
                if (((v.stock_in_sum_qty || 0) == 0) && ((v.stock_out_sum_qty || 0) == 0)){

                    unused_stock_item(htmlFragments,v,i,a);
                }

            }else if(($("input[type='radio'].in_is:checked").val()==3)&&($("input[type='radio'].out_is:checked").val()==3)){
                if (((v.stock_in_sum_qty || 0) == 0) && ((v.stock_out_sum_qty || 0) == 0)) {} else {
                    unused_stock_item(htmlFragments,v,i,a);
                }
            }else if(($("input[type='radio'].in_is:checked").val()==2)){
                if (((v.stock_in_sum_qty || 0) == 0)) {
                    unused_stock_item(htmlFragments,v,i,a);
                }
            }else if(($("input[type='radio'].out_is:checked").val()==2)){
                if (((v.stock_out_sum_qty || 0) == 0)) {
                    unused_stock_item(htmlFragments,v,i,a);
                }
            }else if(($("input[type='radio'].out_is:checked").val()==3)){
                if (((v.stock_out_sum_qty || 0) == 0)) {} else {
                    unused_stock_item(htmlFragments,v,i,a);
                }
            }else if(($("input[type='radio'].in_is:checked").val()==3)){
                if (((v.stock_in_sum_qty || 0) == 0)) {} else {
                    unused_stock_item(htmlFragments,v,i,a);
                }
            }

            if ('children' in v) {
                htmlFragments.push(getTreeView(v.children, children_sum, depth + 1, chart_id));
            }
        });

        return htmlFragments.join('');
    }


    function unused_stock_item(htmlFragments,v,i,a){
        if (v.stock_in_sum_qty_op != null || v.stock_out_sum_qty_op != null || v.stock_in_sum_qty != null || v.stock_out_sum_qty != null) {
                total_opening += ((v.stock_in_sum_qty_op || 0) - (v.stock_out_sum_qty_op || 0));
                total_inwards += (v.stock_in_sum_qty || 0);
                total_outwards += (v.stock_out_sum_qty || 0);
                total_clasing += (((v.stock_in_sum_qty_op || 0) - (v.stock_out_sum_qty_op || 0) + (v.stock_in_sum_qty || 0)) - (v.stock_out_sum_qty || 0));

                htmlFragments.push(`<tr id="${v.product_name}" data-node-id="" data-node-pid="${v.stock_group_id}" class="left left-data editIcon table-row" data-toggle="modal" data-target="#EditLedgerModel">
                                    <td class="sl" style="width: 1%; border: 1px solid #ddd;">${i++}</td>
                                    <td style="width: 5%;" class="td"><span>${h + h + a + v.product_name}<span></td>
                                    <td style='width: 3%;' class='opening td text-end'>${(((v.stock_in_sum_qty_op || 0) - (v.stock_out_sum_qty_op || 0)).formatBangladeshCurrencyType("quantity",v?.symbol))}</td>
                                    <td style='width: 3%;' class='inwards td text-end'>${(((v.stock_in_sum_qty || 0)).formatBangladeshCurrencyType("quantity",v?.symbol))}</td>
                                    <td style='width: 3%;' class='outwards td text-end'>${(((v.stock_out_sum_qty || 0)).formatBangladeshCurrencyType("quantity",v?.symbol))}</td>

                                </tr>`);
            }
    }
    function unused_stock_group(htmlFragments,v,i,a,children_sum){
        htmlFragments.push(`<tr class='left left-data editIcon'>
                                    <td style='width: 1%; border: 1px solid #ddd;'></td>
                                    <td style='width: 3%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4'><span><span class='table-row_tree'>${h + a + v.stock_group_name}</span></span></td>`);

                                    let matchingChild = children_sum.find(c => v.stock_group_id == c.stock_group_id);
                if (matchingChild) {
                        htmlFragments.push(`<td class="text-end" style='width: 3%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4'>${(((matchingChild.stock_qty_in_opening || 0) - (matchingChild.stock_qty_out_opening || 0)).formatBangladeshCurrencyType("quantity"))}</td>
                                            <td  class="text-end" style='width: 3%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4'>${(((matchingChild.stock_qty_in || 0)).formatBangladeshCurrencyType("quantity"))}</td>
                                            <td  class="text-end" style='width: 3%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4'>${(((matchingChild.stock_qty_out || 0)).formatBangladeshCurrencyType("quantity"))}</td>`);

                    }

                htmlFragments.push(`</tr>`);
    }
// stock group analysis function
function get_unused_stock_initial_show(){
    local_store_unused_stock_set_data();
    $(".modal").show();
    print_date();
    i=1
    $.ajax({
        url: '{{ route("report-stock-item-unnsed-stock-data") }}',
            method: 'GET',
            data: {
                to_date:$('.to_date').val(),
                from_date:$('.from_date').val(),
                stock_group_id:$(".stock_group_id").val(),
                godown_id:$(".godown_id").val(),
            },
            dataType: 'json',
            success: function(response) {
                $(".modal").hide();
                get_unused_stock(response)
            },
            error : function(data,status,xhr){
                Unauthorized(data.status);
            }
    });
}

function local_store_unused_stock_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("stock_group_id", '.stock_group_id');
        let godown = getStorage("godown");
        if (godown) {
            $('.godown_id').val(godown.split(",")).trigger('change');
        }


    }

    function local_store_unused_stock_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("stock_group_id", $('.stock_group_id').val());
        setStorage("godown", $('.godown_id').val());

    }
$(document).ready(function () {
   // group item analysis route
    $('.sd').on('click','.table-row',function(e){
        e.preventDefault();
        let  stock_item_id=$(this).closest('tr').attr('id');
        let godown_id=$('.godown_id').val();
        let voucher_id=$('.voucher_id').val();
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();

        url = "{{route('report-stock-item-voucher-register', ['stock_item_id' =>':stock_item_id','godown_id'=>':godown_id','form_date' =>':form_date','to_date' =>':to_date','voucher_id'=>':voucher_id'])}}";

        url = url.replace(':stock_item_id',stock_item_id);
        url = url.replace(':godown_id',godown_id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        url=url.replace(':voucher_id',voucher_id);
        window.open(url,'_blank');
    });
    // table header fixed
    let display_height=$(window).height();
    $('.tableFixHead_unused_stock').css('height',`${display_height-120}px`);

});

</script>
@endpush
@endsection
