
@extends('layouts.backend.app')
@section('title','Stock Voucher Register')
@push('css')
 <style>
    .th{
        border: 1px solid #ddd;font-weight: bold !important;
    }
    .td{
        border: 1px solid #ddd; font-size: 16px  !important;
        font-family: Arial, sans-serif;
        text-align: right;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
                                line-height: 18px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
                                line-height: 18px !important;
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
    'page_title'=>'StockVoucherRegister',
    'page_unique_id'=>20,
    'godown'=>'yes',
    'stockGroup'=>'yes',
    'title'=>'StockVoucherRegister',
    'daynamic_function'=>'get_stock_voucher_register_initial_show',
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Stock Voucher Register',
    'print_layout'=>'portrait',
    'print_header'=>'Stock Voucher Register',
    'user_privilege_title'=>'StockVoucherRegister',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="stock_voucher_register"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-4">
                        <label>Voucher Type : </label>
                        <select name="voucher_id" class="form-control js-example-basic-single voucher_id">
                            <option value="0">--ALL--</option>
                            @php $voucher_type_id= 0; @endphp
                            @foreach ($vouchers as $voucher)
                            @if($voucher_type_id!=$voucher->voucher_type_id)
                            @php $voucher_type_id=$voucher->voucher_type_id; @endphp
                            <option style="color:red;" value="v{{$voucher->voucher_type_id??''}}">{{$voucher->voucher_type??''}}</option>
                            @endif
                            <option value="{{$voucher->voucher_id}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$voucher->voucher_name}}</option>
                            @endforeach
                        </select>
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
<div class="dt-responsive table-responsive cell-border sd tableFixHead_stock_voucher_register">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <thead>
                <tr>
                    <th rowspan="2" style="width: 1%; text-align:center;" class="th align-middle">SL.</th>
                    <th rowspan="2" style="width: 5%; text-align:center;table-layout: fixed;"class="th align-middle" >Particulars</th>
                    <th colspan="3"  style=" width: 5%;" class="th inwards_text text-center in_wards">Inward </th>
                    <th colspan="3" style=" width: 5%;" class="th outwards_text text-center out_wards">Outward</th>
                </tr>
                <tr>
                    <th  style="width: 2%; overflow: hidden;" class="th text-end in_wards">Quantity</th>
                    <th  style="width: 2%; overflow: hidden;" class="th text-end in_wards">Rate</th>
                    <th  style="width: 3%; overflow: hidden;" class="th text-end in_wards">Value</th>
                    <th  style="width: 2%; overflow: hidden;" class="th text-end out_wards" >Quantity</th>
                    <th  style="width: 2%; overflow: hidden;" class="th text-end out_wards">Rate</th>
                    <th  style="width: 3%; overflow: hidden;" class="th text-end out_wards">Value</th>
                </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;" class="th"></th>
                <th style="width: 3%;" class="th">Total :</th>
                <th style="width: 2%; font-size: 18px;"  class="th total_inwards_qty text-end in_wards"></th>
                <th style="width: 3%; font-size: 18px;"  class="th total_inwards_rate text-end in_wards"></th>
                <th style="width: 2%; font-size: 18px;"  class="th total_inwards_value text-end in_wards"></th>
                <th style="width: 3%; font-size: 18px;"  class="th total_outwards_qty text-end out_wards"></th>
                <th style="width: 2%; font-size: 18px;"  class="th total_outwards_rate text-end out_wards"></th>
                <th style="width: 3%; font-size: 18px;"  class="th total_outwards_value text-end out_wards"></th>
            </tr>
        </tfoot>
    </table>
</div>
@endslot
@endcomponent
<br>
@push('js')
<script>

let  total_inwards_qty=0;total_inwards_value=0; total_outwards_qty=0;total_outwards_value=0; i=1;

// stock  voucher register
$(document).ready(function () {
    // stock  group get id check
    local_store_stock_voucher_register_get()
    get_stock_voucher_register_initial_show();

    $("#stock_voucher_register").submit(function(e) {
        local_store_stock_voucher_register_set_data();
        print_date();
        total_inwards_qty=0;total_inwards_value=0; total_outwards_qty=0;total_outwards_value=0; i=1;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("report-stock-voucher-register-data") }}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                       get_stock_voucher_register(response)
                    },
                    error : function(data,status,xhr){

                    }
            });
    });
});

// stock group analysis function
function get_stock_voucher_register(response){
    const children_sum= calculateSumOfChildren(response.data);
    var tree=getTreeView(response.data,children_sum);
       $('.item_body').html(tree);
        get_hover();
        $('.total_inwards_qty').text(total_inwards_qty.formatBangladeshCurrencyType("quantity"));
        $('.total_inwards_rate').text((((total_inwards_value||0)/(total_inwards_qty||0))||0).formatBangladeshCurrencyType("rate"));
        $('.total_inwards_value').text(total_inwards_value.formatBangladeshCurrencyType("amount"));
        $('.total_outwards_qty').text(total_outwards_qty.formatBangladeshCurrencyType("quantity"));
        $('.total_outwards_rate').text((((total_outwards_value||0)/(total_outwards_qty||0))||0).formatBangladeshCurrencyType("rate"));
        $('.total_outwards_value').text(total_outwards_value.formatBangladeshCurrencyType("amount"));

}

// calcucation child summation
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
                stock_total_in: 0,
                stock_total_out: 0
            };
        }

        const currentNode = result[node.stock_group_id];
        currentNode.stock_qty_in += node.stock_qty_in || 0;
        currentNode.stock_qty_out += node.stock_qty_out || 0;
        currentNode.stock_total_in += node.stock_total_in || 0;
        currentNode.stock_total_out+= node.stock_total_out || 0;
        if (node.children) {
            node.children.forEach(processNode);
        }
    }

    arr.forEach(processNode);

    return Object.values(result);
}

function getTreeView(arr, children_sum, depth = 0, chart_id = 0) {
    let html = [];
    arr.forEach(function (v) {
        a = '&nbsp;';
        h = a.repeat(depth);

        if (chart_id != v.stock_group_id) {
            let matchingChild = children_sum.find(c => v.stock_group_id == c.stock_group_id);
            if (((matchingChild.stock_qty_in|| 0) == 0) && ((matchingChild.stock_qty_out || 0) == 0) ) {} else {
                html.push(`<tr id="${v.stock_group_id+'-'+v.under}" class="left left-data table-row_tree">`);
                html.push(`<td style='width: 1%; border: 1px solid #ddd;'></td>`);
                html.push(`<td style='width: 3%; border: 1px solid #ddd;font-weight: bold;' ><p style="margin-left:${(h+a+a).length-12}px; font-family: Arial, sans-serif;" class="text-wrap mb-0 pb-0 ">${v.stock_group_name}</p></td>`);


                if (matchingChild) {
                    html.push(`<td style='width: 3%;font-weight: bold;' class="td  text-end in_wards">${(matchingChild.stock_qty_in || 0).formatBangladeshCurrencyType("quantity")}</td>`);
                    html.push(`<td style='width: 3%;font-weight: bold;' class="td  text-end in_wards">${((((matchingChild.stock_total_in||0)/(matchingChild.stock_qty_in||0))||0).formatBangladeshCurrencyType("rate"))}</td>`);
                    html.push(`<td style='width: 3%;font-weight: bold;' class="td  text-end in_wards">${(((matchingChild.stock_total_in||0)).formatBangladeshCurrencyType("amount"))}</td>`);
                    html.push(`<td style='width: 3%;font-weight: bold;' class="td  text-end out_wards">${(((matchingChild.stock_qty_out||0)).formatBangladeshCurrencyType("quantity"))}</td>`);
                    html.push(`<td style='width: 3%;font-weight: bold;' class="td  text-end out_wards">${((((matchingChild.stock_total_out||0)/(matchingChild.stock_qty_out||0))||0).formatBangladeshCurrencyType("rate"))}</td>`);
                    html.push(`<td style='width: 3%;font-weight: bold;' class="td  text-end out_wards">${(((matchingChild.stock_total_out||0)).formatBangladeshCurrencyType("amount"))}</td>`);
                }

                html.push(`</tr>`);
            }
            chart_id = v.stock_group_id;
        }


        if (((v.stock_qty_total_in|| 0) == 0) && ((v.stock_qty_total_out || 0) == 0) ) {} else {
            total_inwards_qty += (v.stock_qty_total_in || 0);
            total_inwards_value += (v.stock_total_value_in || 0);
            total_outwards_qty += (v.stock_qty_total_out || 0);
            total_outwards_value += (v.stock_total_value_out || 0);

            html.push(`<tr id="${v.stock_item_id}" class="left left-data editIcon table-row">`);
            html.push(`<td class="sl" style="width: 1%; border: 1px solid #ddd;">${i++}</td>`);
            html.push(`<td style="width: 5%; border: 1px solid #ddd;color: #0B55C4;"><p style="margin-left:${(h+a+a+a).length-12}px; font-family: Arial, sans-serif" class="text-wrap mb-0 pb-0">${v.product_name}</p></td>`);
            html.push(`<td style='width: 3%;' class='td opening  text-end in_wards'>${(v.stock_qty_total_in || 0).formatBangladeshCurrencyType("quantity",v?.symbol)}</td>`);
            html.push(`<td style='width: 3%; 'class='td inwards  text-end in_wards'>${(((v.stock_total_value_in||0)/(v.stock_qty_total_in||0))||0).formatBangladeshCurrencyType("rate")}</td>`);
            html.push(`<td style='width: 3%; 'class='td outwards text-end in_wards'>${(((v.stock_total_value_in||0)).formatBangladeshCurrencyType("amount"))}</td>`);
            html.push(`<td style='width: 3%; 'class='td clasing  text-end out_wards'>${(((v.stock_qty_total_out||0)).formatBangladeshCurrencyType("quantity",v?.symbol))}</td>`);
            html.push(`<td style='width: 3%; 'class='td outwards text-end out_wards'>${(((v.stock_total_value_out||0)/(v.stock_qty_total_out||0))||0).formatBangladeshCurrencyType("rate")}</td>`);
            html.push(`<td style='width: 3%; 'class='td clasing text-end out_wards'>${(((v.stock_total_value_out||0)).formatBangladeshCurrencyType("amount"))}</td>`);
            html.push(`</tr>`);
        }

        if ('children' in v) {
            html.push(getTreeView(v.children, children_sum, depth + 1, chart_id));
        }
    });

    return html.join("");
}

// stock group analysis function
function get_stock_voucher_register_initial_show(){
    local_store_stock_voucher_register_set_data();
    $(".modal").show();
    print_date();
    i=1
    $.ajax({
        url: '{{ route("report-stock-voucher-register-data") }}',
            method: 'GET',
            data: {
                to_date:$('.to_date').val(),
                from_date:$('.from_date').val(),
                voucher_id:$(".voucher_id").val(),
                godown_id:$(".godown_id").val(),
            },
            dataType: 'json',
            success: function(response) {
                $(".modal").hide();
                get_stock_voucher_register(response)
            },
            error : function(data,status,xhr){
            }
    });
}

function local_store_stock_voucher_register_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("voucher_id", '.voucher_id');
        let godown = getStorage("godown");
        if (godown) {
            $('.godown_id').val(godown.split(",")).trigger('change');
        }
    }
function local_store_stock_voucher_register_set_data() {
    setStorage("end_date", $('.to_date').val());
    setStorage("start_date", $('.from_date').val());
    setStorage("voucher_id", $('.voucher_id').val());
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
    $('.tableFixHead_stock_voucher_register').css('height',`${display_height-120}px`);
});

</script>
@endpush
@endsection
