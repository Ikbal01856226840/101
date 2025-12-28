@extends('layouts.backend.app')
@section('title','Party Wise Actual Sales')
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
'page_title'=>'Party Wise Actual Sales',
'size'=>'modal-xl',
'page_unique_id'=>46,
'godown'=>'yes',
'title'=>'Party Wise Actual Sales Reports',
'daynamic_function'=>'get_retailer_actual_sales_initial_show'
])
@endcomponent

<!-- add component-->
@component('components.report', [
'title' => 'Party Wise Actual Sales',
'print_layout'=>'portrait',
'print_header'=>'Party Wise Actual Sales',
'user_privilege_title'=>'RetrailerActualSales',
'print_date'=>1,
'report_setting_model'=>'report_setting_model',
'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
<form id="add_retailer_actual_sales" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row ">
        <div class="col-md-6">
            <label>Ledger :</label>
            <select name="ledger_id" class="form-control js-example-basic-single ledger_id" required>
                @if($all==1)
                    <option value="0">--All--</option>
                @endif
                {!!html_entity_decode($ledgers)!!}
            </select>
            {{-- <div class="form-group ">
                
                <label class="fs-6">Ledger type :</label>
                <input class="form-check-input ledger_type" type="radio" name="ledger_type" value="1" checked="checked">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Both
                </label>
                <input class="form-check-input ledger_type" type="radio" name="ledger_type" value="2">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Dealer
                </label>
                <input class="form-check-input ledger_type" type="radio" name="ledger_type" value="3">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Retrailer
                </label>
            </div> --}}
        </div>
        <div class="col-md-4">
            <div class="row  m-0 p-0">
                <div class="col-md-6 m-0 p-0">
                    <label>Date From: </label>
                    <input type="text" name="from_date" class="form-control  fs-5 from_date" value="{{financial_end_date(date('Y-m-d'))}}" readonly>
                </div>
                <div class="col-md-6 m-0 p-0">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control  fs-5 to_date" value="{{financial_end_date(date('Y-m-d'))}}" readonly>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <label></label><br>
            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;"><span class="m-1 m-t-1"></span><span>Search</span></button>
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
                <th rowspan="2" class="text-left th" style="width: 5%; table-layout: fixed;">Particulars</th>
               <th style="width: 2%;  overflow: hidden;" class="th text-end">Sales<br> Quantity</th>
                <th style="width: 2%;  overflow: hidden;" class="th text-end">Sales <br>Return <br> Quantity</th>
                <th style="width: 2%;  overflow: hidden;" class="th text-end">Actual <br>Sales<br> Quantity</th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;" class="th"></th>
                <th style="width: 3%;" class="th text-end td-bold">Total :</th>
                <th style="width: 2%; font-size: 18px;" class="total_sales_qty th text-end td-bold"></th>
                <th style="width: 2%; font-size: 18px;" class="total_sales_return_qty th text-end td-bold"></th>
                <th style="width: 2%; font-size: 18px;" class="total_actual_sales_qty th text-end td-bold"></th>
                
            </tr>
        </tfoot>
    </table>
    <div class="col-sm-12 text-center footer_class">
    </div>
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
        get_retailer_actual_sales_initial_show();
        $("#add_retailer_actual_sales").submit(function(e) {
            // $(".modal").show();
            let urlKey = encodeURIComponent(window.location.href);
            localStorage.setItem(`${urlKey}_end_date`, $('.to_date').val());
            localStorage.setItem(`${urlKey}_start_date`, $('.from_date').val());
            localStorage.setItem(`${urlKey}_ledger`, $('.ledger_id').val());
             localStorage.setItem(`${urlKey}_ledger_type`,$(".ledger_type:checked").val());
            let ledger_name = $('.ledger_id').find('option:selected').text();
            localStorage.setItem('ledger_name', '');
            localStorage.setItem('ledger_name', ledger_name);
            print_date();
            total_sales_qty = 0;
            total_sales_value = 0;
            total_sales_return_qty = 0;
            total_sales_return_value = 0, i = 1;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{route("retrailer-actual-sales-data-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_retailer_actual_sales(response)
                },
                error: function(data, status, xhr) {
                    Unauthorized(data.status);
                }
            });
        });

    });

    // actual sales function
    function get_retailer_actual_sales(response) {
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
                    stock_qty_sales_return: 0
                };
            }

            const currentNode = result[node.stock_group_id];
            currentNode.stock_qty_sales += node.stock_qty_sales || 0;
            currentNode.stock_qty_sales_return += node.stock_qty_sales_return || 0;
            
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
                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">${Math.abs(matchingChild.stock_qty_sales_return||0).formatBangladeshCurrencyType("quantity")}</td>
                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">${(((matchingChild.stock_qty_sales||0)+(matchingChild.stock_qty_sales_return||0))).formatBangladeshCurrencyType("quantity")}</td>
                            `);
                    }

                    html.push(`</tr>`);
                }
                chart_id = v.stock_group_id;
            }

            if (v.stock_qty_sales_total != null || v.tock_qty_sales_return_total != null || v.stock_qty_sales_return_total != null || v.stock_total_sales_return_value != null) {
                total_sales_qty += (v.stock_qty_sales_total || 0);
                total_sales_return_qty += (v.stock_qty_sales_return_total || 0);

                html.push(`<tr id="${v.stock_item_id}" class="left left-data editIcon table-row">
                           <td class="sl" style="width: 1%;  border: 1px solid #ddd;">${i++}</td>
                           <td style="width: 5%;'" class="td2 item_name"><p style="margin-left :${(h+a+a+a).length-12}px;color: #0B55C4; font-family: Arial, sans-serif" class="text-wrap mb-0 pb-0">${v.product_name}</p></td>
                           <td style='width: 2%;'class="td2 text-end">${(v.stock_qty_sales_total||0).formatBangladeshCurrencyType("quantity",v.symbol)}</td>
                           <td style='width: 2%;'class="td2 text-end">${Math.abs(v.stock_qty_sales_return_total||0).formatBangladeshCurrencyType("quantity",v.symbol)}</td>
                           <td style='width: 2%;'class="td2 text-end">${(((v.stock_qty_sales_total||0)+(v.stock_qty_sales_return_total||0))).formatBangladeshCurrencyType("quantity",v.symbol)}</td>
                           
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
        if (localStorage.getItem(`${urlKey}_ledger`)) {
            $('.ledger_id').val(localStorage.getItem(`${urlKey}_ledger`)).trigger('change');
        }
         if (localStorage.getItem(`${urlKey}_ledger_type`)) {
              let ledger_type =localStorage.getItem(`${urlKey}_ledger_type`);
             $(".ledger_type[value='" + ledger_type + "']").prop("checked", true);

        }
   }

    // sales list initial show
    function get_retailer_actual_sales_initial_show() {
        total_sales_qty = 0;
        total_sales_value = 0;
        total_sales_return_qty = 0;
        total_sales_return_value = 0;
        i = 1;

        // $(".modal").show();
        print_date();
        local_store();
        $.ajax({
            url: "{{ route('retrailer-actual-sales-data-data')}}",
            type: 'GET',
            dataType: 'json',
            data: {
                to_date: $('.to_date').val(),
                from_date: $('.from_date').val(),
                ledger_id: $('.ledger_id').val()
               

            },
            success: function(response) {
                $(".modal").hide();
                get_retailer_actual_sales(response);
            },
            error: function(data, status, xhr) {
                Unauthorized(data.status);
            }
        })
    }
</script>
@endpush
@endsection
