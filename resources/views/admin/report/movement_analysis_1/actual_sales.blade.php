@extends('layouts.backend.app')
@section('title','Actual Sales')
@push('css')

<style>
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

    body {
        overflow: auto !important;
    }

    .td-bold {
        font-weight: bold;
    }
</style>
@endpush
@section('admin_content')<br>

<!-- setting component-->
@component('components.report_setting_modal', [
'id' =>'exampleModal',
'class' =>'modal fade',
'page_title'=>'Actual Sales',
'size'=>'modal-xl',
'page_unique_id'=>1,
'godown'=>'yes',
'title'=>'Actual Sales Reports',
'daynamic_function'=>'get_actual_sales_initial_show'
])
@endcomponent

<!-- add component-->
@component('components.report', [
'title' => 'Actual Sales',
'print_layout'=>'portrait',
'print_header'=>'Actual Sales',
'user_privilege_title'=>'ActualSales',
'print_date'=>1,
'report_setting_model'=>'report_setting_model',
'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
<form id="add_actual_sales" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row ">
        <div class="col-md-6">
            <label>Godowns : </label>
            <select name="godown_id[]" class="form-control js-example-basic-multiple godown_id" multiple="multiple" required>
                <option value="0" selected>All</option>
                @foreach($godowns as $godown)
                <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <div class="row  m-0 p-0">
                <div class="col-md-6 m-0 p-0">
                    <label>Date From: </label>
                    <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{financial_end_date(date('Y-m-d'))}}">
                </div>
                <div class="col-md-6 m-0 p-0">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{financial_end_date(date('Y-m-d'))}}">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <label></label><br>
            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;"><span class="m-1 m-t-1"></span><span>Search</span></button>
        </div>
    </div>
    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_report">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <tr>
                <th rowspan="2" class="text-center th align-middle" style="width: 1%; ">SL.</th>
                <th rowspan="2" class="text-center th align-middle" style="width: 5%; table-layout: fixed;">Particulars</th>
                <th colspan="3" class="text-center th " style=" width: 5%;">Sales</th>
                <th colspan="3" class="text-center th " style=" width: 5%; ">Sales Return</th>
                <th colspan="3" class="text-center th" tyle=" width: 5%;">Actual Sales</th>
            </tr>
            <tr>
                <th style="width: 2%;  overflow: hidden;" class="th text-end">Quantity</th>
                <th style="width: 2%;  overflow: hidden;" class="th text-end">Rate</th>
                <th style="width: 3%;  overflow: hidden;" class="th text-end">Value</th>
                <th style="width: 2%;  overflow: hidden;" class="th text-end">Quantity</th>
                <th style="width: 2%;  overflow: hidden;" class="th text-end">Rate</th>
                <th style="width: 3%;  overflow: hidden;" class="th text-end">Value</th>
                <th style="width: 2%;  overflow: hidden;" class="th text-end">Quantity</th>
                <th style="width: 2%;  overflow: hidden;" class="th text-end">Rate</th>
                <th style="width: 3%;  overflow: hidden;" class="th text-end">Value</th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;" class="th"></th>
                <th style="width: 3%;" class="th text-end td-bold">Total :</th>
                <th style="width: 2%; font-size: 18px;" class="total_sales_qty th text-end td-bold"></th>
                <th style="width: 2%; font-size: 18px;" class="total_sales_rate th text-end td-bold"></th>
                <th style="width: 3%; font-size: 18px;" class="total_sales_value th text-end td-bold"></th>
                <th style="width: 2%; font-size: 18px;" class="total_sales_return_qty th text-end td-bold"></th>
                <th style="width: 2%; font-size: 18px;" class="total_sales_return_rate th text-end td-bold"></th>
                <th style="width: 3%; font-size: 18px;" class="total_sales_return_value th text-end td-bold"></th>
                <th style="width: 2%; font-size: 18px;" class="total_actual_sales_qty th text-end td-bold"></th>
                <th style="width: 2%; font-size: 18px;" class="total_actual_sales_rate th text-end td-bold"></th>
                <th style="width: 3%; font-size: 18px;" class="total_actual_sales_value th text-end td-bold"></th>
            </tr>
        </tfoot>
    </table>
</div>
@endslot
@endcomponent
<br>
@push('js')

<script>
    let i = 1;
    let total_sales_qty = 0;
    total_sales_value = 0;
    total_sales_return_qty = 0;
    total_sales_return_value = 0;

    // actual sales
    $(document).ready(function() {
        get_actual_sales_initial_show();
        $("#add_actual_sales").submit(function(e) {
            $(".modal").show();
            let urlKey = encodeURIComponent(window.location.href);
            localStorage.setItem(`${urlKey}_end_date`, $('.to_date').val());
            localStorage.setItem(`${urlKey}_start_date`, $('.from_date').val());
            localStorage.setItem(`${urlKey}_godown`, $('.godown_id').val());
            print_date();
            total_sales_qty = 0;
            total_sales_value = 0;
            total_sales_return_qty = 0;
            total_sales_return_value = 0, i = 1;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{route("actual_sales-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_actual_sales(response)
                },
                error: function(data, status, xhr) {
                    Unauthorized(data.status);
                }
            });
        });

    });

    // actual sales function
    function get_actual_sales(response) {
        const children_sum = calculateSumOfChildren(response.data);
        var tree = getTreeView(response.data, children_sum);
        $('.item_body').html(tree);
        get_hover();
        $('.total_sales_qty').text(total_sales_qty.formatBangladeshCurrencyType("quantity"));
        $('.total_sales_rate').text((((total_sales_value || 0) / (total_sales_qty || 0)) || 0).formatBangladeshCurrencyType("rate"));
        $('.total_sales_value').text(total_sales_value.formatBangladeshCurrencyType("amount"));
        $('.total_sales_return_qty').text(total_sales_return_qty.formatBangladeshCurrencyType("quantity"));
        $('.total_sales_return_rate').text((((total_sales_return_value || 0) / (total_sales_return_qty || 0)) || 0).formatBangladeshCurrencyType("rate"));
        $('.total_sales_return_value').text(total_sales_return_value.formatBangladeshCurrencyType("amount"));
        $('.total_actual_sales_qty').text(((total_sales_qty || 0) + (total_sales_return_qty || 0)).formatBangladeshCurrencyType("quantity"));
        $('.total_actual_sales_rate').text(((Math.abs((total_sales_value || 0) + (total_sales_return_value || 0)) / Math.abs((total_sales_qty || 0) + (total_sales_return_qty || 0))) || 0).formatBangladeshCurrencyType("rate"));
        $('.total_actual_sales_value').text(((total_sales_value || 0) + (total_sales_return_value || 0)).formatBangladeshCurrencyType("amount"));
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
                    stock_qty_sales: 0,
                    stock_qty_sales_return: 0,
                    stock_total_sales: 0,
                    stock_total_sales_return: 0
                };
            }

            const currentNode = result[node.stock_group_id];
            currentNode.stock_qty_sales += node.stock_qty_sales || 0;
            currentNode.stock_qty_sales_return += node.stock_qty_sales_return || 0;
            currentNode.stock_total_sales += node.stock_total_sales || 0;
            currentNode.stock_total_sales_return += node.stock_total_sales_return || 0;
            if (node.children) {
                node.children.forEach(processNode);
            }
        }

        arr.forEach(processNode);

        return Object.values(result);
    }

    function getTreeView(arr, children_sum, depth = 0, chart_id = 0) {
        let html = [];
        arr.forEach(function(v) {
            a = '&nbsp;';
            h = a.repeat(depth);

            if (chart_id != v.stock_group_id) {
                let matchingChild = children_sum.find(c => v.stock_group_id == c.stock_group_id);
                if (((matchingChild.stock_qty_sales || 0) == 0) && ((matchingChild.stock_qty_sales_return || 0) == 0)) {} else {
                    html.push(`<tr id="${v.stock_group_id+'-'+v.under}" class="left left-data table-row_tree">
                            <td style='width: 1%;  border: 1px solid #ddd;'></td>
                            <td style='width: 3%;' class="td1 td-bold"><p style="margin-left:${(h+a+a).length-12}px;cursor: default !important; font-size: 18px; font-family: Arial, sans-serif" class="text-wrap mb-0 pb-0 ">${v.stock_group_name}</p></td>`);


                    if (matchingChild) {
                        
                        html.push(`<td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">
                                    ${(matchingChild.stock_qty_sales||0).formatBangladeshCurrencyType("quantity")}
                                </td>
                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">
                                ${(((matchingChild.stock_total_sales||0)/(matchingChild.stock_qty_sales||0))||0).formatBangladeshCurrencyType("rate")}
                            </td>
                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">${(matchingChild.stock_total_sales||0).formatBangladeshCurrencyType("amount")}</td>
                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">${Math.abs(matchingChild.stock_qty_sales_return||0).formatBangladeshCurrencyType("quantity")}</td>
                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">${((Math.abs(matchingChild.stock_total_sales_return||0)/Math.abs(matchingChild.stock_qty_sales_return||0))||0).formatBangladeshCurrencyType("rate")}</td>
                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">${Math.abs(matchingChild.stock_total_sales_return||0).formatBangladeshCurrencyType("amount")}</td>
                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">${(((matchingChild.stock_qty_sales||0)+(matchingChild.stock_qty_sales_return||0))).formatBangladeshCurrencyType("quantity")}</td>
                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">${((((matchingChild.stock_total_sales||0)+(matchingChild.stock_total_sales_return))/(((matchingChild.stock_qty_sales||0)+(matchingChild.stock_qty_sales_return||0))))||0).formatBangladeshCurrencyType("rate")}</td>
                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">${((matchingChild.stock_total_sales||0)+(matchingChild.stock_total_sales_return||0)).formatBangladeshCurrencyType("amount")}</td>`);
                    }

                    html.push(`</tr>`);
                }
                chart_id = v.stock_group_id;
            }

            if (v.stock_qty_sales_total != null || v.tock_qty_sales_return_total != null || v.stock_qty_sales_return_total != null || v.stock_total_sales_return_value != null) {
                total_sales_qty += (v.stock_qty_sales_total || 0);
                total_sales_value += (v.stock_total_sales_value || 0);
                total_sales_return_qty += (v.stock_qty_sales_return_total || 0);
                total_sales_return_value += (v.stock_total_sales_return_value || 0);

                html.push(`<tr id="${v.stock_item_id}" class="left left-data editIcon table-row">
                           <td class="sl" style="width: 1%;  border: 1px solid #ddd;">${i++}</td>
                           <td style="width: 5%;'" class="td2 item_name"><p style="margin-left :${(h+a+a+a).length-12}px;color: #0B55C4; font-family: Arial, sans-serif" class="text-wrap mb-0 pb-0">${v.product_name}</p></td>
                           <td style='width: 2%;'class="td2 text-end">${(v.stock_qty_sales_total||0).formatBangladeshCurrencyType("quantity",v.symbol)}</td>
                           <td style='width: 2%;'class="td2 text-end">${(Math.abs((v.stock_total_sales_value||0)/(v.stock_qty_sales_total||0))||0).formatBangladeshCurrencyType("rate")}</td>
                           <td style='width: 3%;'class="td2 text-end">${((v.stock_total_sales_value||0)).formatBangladeshCurrencyType("amount")}</td>
                           <td style='width: 2%;'class="td2 text-end">${Math.abs(v.stock_qty_sales_return_total||0).formatBangladeshCurrencyType("quantity",v.symbol)}</td>
                           <td style='width: 2%;'class="td2 text-end">${((Math.abs(v.stock_total_sales_return_value||0)/Math.abs(v.stock_qty_sales_return_total||0))||0).formatBangladeshCurrencyType("rate")}</td>
                           <td style='width: 3%;'class="td2 text-end">${(Math.abs(v.stock_total_sales_return_value||0)).formatBangladeshCurrencyType("amount")}</td>;
                           <td style='width: 2%;'class="td2 text-end">${(((v.stock_qty_sales_total||0)+(v.stock_qty_sales_return_total||0))).formatBangladeshCurrencyType("quantity",v.symbol)}</td>
                           <td style='width: 2%;'class="td2 text-end">${((Math.abs((v.stock_total_sales_value||0)+(v.stock_total_sales_return_value||0))/Math.abs((v.stock_qty_sales_total||0)+(v.stock_qty_sales_return_total||0)))||0).formatBangladeshCurrencyType("rate")}</td>
                           <td style='width: 3%;'class="td2 text-end">${((v.stock_total_sales_value||0)+(v.stock_total_sales_return_value||0)).formatBangladeshCurrencyType("amount")}</td>
                   </tr>`);
            }

            if ('children' in v) {
                html.push(getTreeView(v.children, children_sum, depth + 1, chart_id));
            }
        });

        return html.join("");
    }
    $(document).ready(function() {
        // table header fixed
        let display_height = $(window).height();
        $('.tableFixHead_report').css('height', `${display_height-115}px`);
    });

    //get  all data show
    $(document).ready(function() {

        // stock item month wise summary route
        $('.sd').on('click', '.item_name', function(e) {
            e.preventDefault();
            let stock_item_id = $(this).closest('tr').attr('id');
            let godown_id = $('.godown_id').val();
            let form_date = $('.from_date').val();
            let to_date = $('.to_date').val();
            url = "{{route('actual-sales-details', ['godown_id'=>':godown_id','stock_item_id' =>':stock_item_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
            url = url.replace(':stock_item_id', stock_item_id);
            url = url.replace(':form_date', form_date);
            url = url.replace(':to_date', to_date);
            url = url.replace(':godown_id', godown_id);

            window.open(url, '_blank');
        });
    });


    $(document).ready(function() {
        local_store();
    });

    function local_store() {
        let urlKey = encodeURIComponent(window.location.href); // Encode the URL
        if (localStorage.getItem(`${urlKey}_end_date`)) {
            $('.to_date').val(localStorage.getItem(`${urlKey}_end_date`));
        }
        if (localStorage.getItem(`${urlKey}_start_date`)) {
            $('.from_date').val(localStorage.getItem(`${urlKey}_start_date`));
        }
        if (localStorage.getItem(`${urlKey}_godown`)) {
            $('.godown_id').val(localStorage.getItem(`${urlKey}_godown`).split(",")).trigger('change');
        }
   }

    // sales list initial show
    function get_actual_sales_initial_show() {
        total_sales_qty = 0;
        total_sales_value = 0;
        total_sales_return_qty = 0;
        total_sales_return_value = 0;
        i = 1;

        $(".modal").show();
        print_date();
        local_store();
        $.ajax({
            url: "{{ route('actual_sales-data')}}",
            type: 'GET',
            dataType: 'json',
            data: {
                to_date: $('.to_date').val(),
                from_date: $('.from_date').val(),
                godown_id: $('.godown_id').val(),

            },
            success: function(response) {
                $(".modal").hide();
                get_actual_sales(response);
            },
            error: function(data, status, xhr) {
                Unauthorized(data.status);
            }
        })
    }
</script>
@endpush
@endsection
