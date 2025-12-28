@extends('layouts.backend.app')
@section('title','Stock Item Opening')
@push('css')

<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
<style>
</style>
@endpush
@section('admin_content')
<br>
@component('components.setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=>'stock_item_opening',
    'page_unique_id'=>14,
    'title'=>'Stock Item Opening',
    'sort_by'=>'sort_by',
    'insert_settings'=>'insert_settings',
    'view_settings'=>'view_settings'
])
@endcomponent

<div class="coded-main-container navChild ">
    <div class="pcoded-content">
        <div class="pcoded-inner-content"><br>
            <!-- Main-body start -->
            <div class="main-body p-0  side-component">
                <div class="page-wrapper m-t-0 p-0">
                    <div class="page-wrapper m-t-0 m-l-1 m-r-1 p-2">
                        <!-- Page-header start -->
                        <div class="page-header m-0 p-0  ">
                            <div class="row align-items-left">
                                <div class="col-lg-12">
                                    <div class="row ">
                                        <div class="col-md-3">
                                            <div class="page-header-title">
                                                <h4>Stock Item Opening</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div style="float: right; margin-left: 5px;">
                                                <a style=" float:right; text-decoration: none;" href="{{route('master-dashboard'),}}"><span class="fa fa-times-circle-o m-1" style="font-size:27px; color:#ff6666;"></span><span style="float:right;margin:2px; padding-top:5px; ">Close</span></a>
                                            </div>
                                            <div style="float: right; margin-left: 5px;">
                                                <a style=" float:right ;text-decoration: none; cursor: pointer" data-toggle="modal" data-target="#exampleModal"><span class="fa fa-cog m-1" style="font-size:27px;  color:Green;"></span><span style="float:right;margin:2px; padding-top:5px; ">Setting</span></a>
                                            </div>
                                            <div style="float: right;margin-left:9px">
                                                <a style="float:right; text-decoration: none;cursor: pointer;" class="print" onclick="print_html('landscape','Stock Item Opening')"><span class="fa fa-print m-1" style="font-size:27px; color:teal;"></span><span style="float:right;margin:2px; padding-top:5px;">Print</span></a>
                                            </div>
                                            <div style="float: right;margin-left:9px">
                                                <a style="float:right; text-decoration: none;cursor: pointer;  " class="excel" onclick="exportTableToExcel('Stock Item Opening')"><span class="fa fa-file-excel-o m-1 " style="font-size:25px; color:Gray;"></span><span style="float:right;margin:2px; padding-top:5px;">Excel</span></a>
                                            </div>
                                            <div style="float: right;margin-left:9px">
                                                <a style="float:right; text-decoration: none;cursor: pointer;" class="pdf_download pdf" onclick="generateTable('Stock Item Opening')"><span class="fa fa-file-pdf-o m-1 " style="font-size:25px; color:MediumSeaGree; "></span><span style="float:right;margin:2px; padding-top:5px;">Pdf</span></a>
                                            </div>
                                            <div style="float: right; width:200px;">

                                                <input type="text" id="myInput" style="border-radius: 5px" class="form-control form-control pb-1" width="100%" placeholder="searching">
                                            </div>
                                        </div>
                                        <hr style="margin-bottom: 0px;">
                                    </div>
                                </div>
                                <!-- Page-header end -->
                                <!-- Page-body start -->
                                <div class="page-body">
                                    <div class="row">
                                        <div class="page-header m-0  ">
                                            <form id="add_stock_item_prce_form" method="POST">
                                                @csrf
                                                {{ method_field('POST') }}
                                                <div class="row ">
                                                    <div class="col-md-3">
                                                        <label>Select Group : </label>
                                                        <select name="stock_group_id" class="form-control js-example-basic-single stock_group_id">
                                                            <option value="0">Primary</option>
                                                            {!!html_entity_decode($select_option_tree)!!}
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Godown : </label>
                                                        <select name="godown_id" class="form-control js-example-basic-single godown_id">
                                                            @foreach ($godowns as $godown)
                                                            <option value="{{$godown->godown_id}}">
                                                                {{$godown->godown_name}}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        @if(company()->opening_stock_item_customer_is==1)
                                                        <label>Customer : </label>
                                                        <select name="customer_id" class="form-control js-example-basic-single customer_id" required>
                                                            <option selected disabled>--Select--</option>
                                                            @foreach ($customers as $customer)
                                                            <option value="{{$customer->customer_id}}">
                                                                {{$customer->customer_name}}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label style="display: none">submit</label><br>
                                                        <div class="form-group">
                                                            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:100%">Search</button>
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
                                <form id="add_stock_item_opening_form" method="POST">
                                    @csrf
                                    {{ method_field('POST') }}
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <!-- Zero config.table start -->
                                            <div class="card ">
                                                <div class="card-block table_content">
                                                    <div class="dt-responsive table-responsive cell-border sd tableFixHead_double_header">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 "><br><br>
                                                    @if (user_privileges_check('master','stock_item__opening_balance','create_role'))
                                                    <div class="form-group">
                                                        <button type="submit" id="btn_stock_item_opening" class="btn hor-grd btn-grd-primary btn-block submit " style="width:100%"><u>S</u>ave</button>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="col-lg-6"><br><br>
                                                    <div class="form-group">
                                                        <a class=" btn hor-grd btn-grd-success btn-block " href="{{route('master-dashboard')}}" style="width:100%">Close</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script type="text/javascript" src="{{asset('pageWiseSetting/page_wise_setting.js')}}"></script>
<script>
    $(document).on('change click', '.stock_group_id,.godown_id,.customer_id ,.submit', function(e) {
        $('.hidden_stock_group_id').val($('.stock_group_id').val());
        $('.hidden_godown_id').val($('.godown_id').val());
        $('.hidden_customer_id').val($('.customer_id').val());
    });
    $(function() {
        // add new stock item opening ajax request
        $("#add_stock_item_prce_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            let last_updae_value = $('.last_update').is(':checked');
            $("#add_group_chart_btn").text('Adding...');
            $.ajax({
                url: '{{ url("stock-item-opening/tree-view") }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data, status, xhr) {
                    let html = [];

                    html.push(`<table  id="tableId"  style=" border-collapse: collapse;"   class="table opening customers " >
                        <thead >
                           <tr>
                                <th style="width: 2px;  border: 1px solid #ddd;">SL</th>
                                <th style="width: 3%;  border: 1px solid #ddd;">Stock Item Name</th>
                                <th style="width: 3%;  border: 1px solid #ddd;">Unit</th>
                                <th style="width: 3%;  border: 1px solid #ddd;min-width: 150px;">Quantity</th>
                                <th style="width: 3%;  border: 1px solid #ddd;min-width: 150px;">Price</th>
                                <th style="width: 3%;  border: 1px solid #ddd;min-width: 150px;">Amount</th>
                                <th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>
                                <th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>
                           </tr>
                        </thead>
                     <tbody class="qw" id="myTable">`);
                    html.push(getTreeView(data.data, depth = 0));
                    html.push(`</tbody>;
                        <tfoot>
                            <tr>
                                <th style="width: 2px;  border: 1px solid #ddd;">SL</th>
                                <th style="width: 3%;  border: 1px solid #ddd;">Stock Item Name</th>
                                <th style="width: 3%;  border: 1px solid #ddd;">Unit</th>
                                <th style="width: 3%;  border: 1px solid #ddd;min-width: 150px;">Quantity</th>
                                <th style="width: 3%;  border: 1px solid #ddd;min-width: 150px;">Price</th>
                                <th style="width: 3%;  border: 1px solid #ddd;min-width: 150px;">Amount</th>
                                <th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>
                                <th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>
                            </tr>
                       </tfoot>
                    </table>`)
                    $('.sd').html(html.join(""));
                    page_wise_setting_checkbox();
                    get_hover();
                    $('#tableId').on('keyup', '.qty,.rate', function() {
                        let qty = $(this).closest('tr').find('.qty').val();
                        let rate = $(this).closest('tr').find('.rate').val();
                        $(this).closest('tr').find('.amount').val(
                            parseFloat(qty * rate).toFixed(
                                "{{company()->amount_decimals}}"));

                    });
                    $('#tableId').on('keyup', '.qty,.amount', function() {
                        let qty = $(this).closest('tr').find('.qty').val();
                        let total = $(this).closest('tr').find('.amount').val();
                        $(this).closest('tr').find('.rate').val(
                            parseFloat(total / qty).toFixed(
                                "{{company()->amount_decimals}}"));

                    });
                    $('tbody').find('tr .sl').each(function(i) {
                        let prd_name = $(this).text();
                        let sl = i + 1;
                        let trim_prd_name = prd_name.trim();
                        prd_name = prd_name.replace(trim_prd_name,
                            `${sl}.${trim_prd_name}`);
                        $(this).text(prd_name);
                    });
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
                    if (data.status == 422) {

                        $('#error_group_chart_name').text(data.responseJSON.data
                            .group_chart_name[0]);
                    }

                }
            });
        });
    });

    // get Tree view table row
    function getTreeView(arr, depth = 0, chart_id = 0, stock_item = 0) {

        let html = [];
        let data_targer = " ";
        var sl = 1;
        arr.forEach(function(v, index) {
            a = '&nbsp;&nbsp;&nbsp;';
            h = a.repeat(depth);

            h = a.repeat(depth);
            if (chart_id != v.stock_group_id) {
                html.push(`<tr class='left left-data editIcon table-row'>
                  <td  style='width: 3%;  border: 1px solid #ddd;'></td>
                  <td style='width: 3%;  border: 1px solid #ddd; font-weight: bold;'>${h+a+v.stock_group_name}</td>
                  <td style='width: 3%;  border: 1px solid #ddd;'></>
                  <td style='width: 3%;  border: 1px solid #ddd;'></td>
                  <td style='width: 3%;  border: 1px solid #ddd;'></td>
                  <td style='width: 3%;  border: 1px solid #ddd;'></td>
                  <td  style='width: 3%;  border: 1px solid #ddd;' class='created_user d-none d-print-none'></td>
                  <td class='last_update d-none d-print-none'  style='width: 3%;  border: 1px solid #ddd;'></td></tr>`);
                chart_id = v.stock_group_id;
            }
            html.push(v.stock_item_id ? `<tr id="${v.stock_item_id}" class="left left-data editIcon table-row"  data-toggle="modal" data-target="#EditLedgerModel">
                    <td class="sl" style="width: 2px;  border: 1px solid #ddd;"></td>
                    <td  style="width: 3%;  border: 1px solid #ddd;">${h+a+h+a+v.product_name}</td>
                    <td  style="width: 3%;  border: 1px solid #ddd;">
                        <input type="hidden" class="hidden_stock_group_id" name="stock_group_id">
                        <input type="hidden" class="hidden_godown_id" name="godown_id" >
                        <input type="hidden" class="hidden_customer_id" name="customer_id">
                        <input type="hidden"  name="tran_id[]" value="${v.tran_id||''}">
                        <input type="hidden" class=" stock_item_id" name="stock_item_id[]" value="${v.stock_item_id||''}">
                        <input type="hidden"  name="stock_in_id[]" value="${v.stock_in_id||''}">
                        ${v.symbol||''}
                    </td>
                    <td  style="width: 3%;  border: 1px solid #ddd;">
                        <input type="number" class="qty" step="any" name="qty[]" value="${v.qty?v.qty.toFixed("{{company()->amount_decimals}}"):''}">
                    </td>
                    <td class="nature_val"  style="width: 3%;  border: 1px solid #ddd;">
                        <input type="number" id="rate" class="rate" step="any" name="rate[]" value="${v.rate?v.rate.toFixed("{{company()->amount_decimals}}"):''}">
                    </td>
                    <td  style="width: 3%;  border: 1px solid #ddd;">
                        <input type="number" class="amount" name="total[]" step="any" value="${v.total?v.total.toFixed("{{company()->amount_decimals}}"):''}">
                    </td>
                     <td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">${(v.user_name?v.user_name:'')}</td>
                     <td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">${(v.other_details ? JSON.parse(v.other_details) : '')}</i></div></td>
                </tr>` : '');
            if ('children' in v) {
                html.push(getTreeView(v.children, depth + 1, chart_id));
            }

        });
        return html.join("");
    }
</script>

<script>
    // add new stock item opening  ajax request
    $("#add_stock_item_opening_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        console.log(fd);
        $("#btn_stock_item_opening").text('Save');
        $.ajax({
            url: '{{route("stock-item-opening.store") }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                swal_message(data.message, 'success');
                location.reload();
            },
            error: function(data, status, xhr) {
                if (data.status == 400) {
                    swal_message(data.message, 'error');
                }

            }
        });
    });

    function swal_message(data, message) {
        swal({
            title: 'Successfullly',
            text: data,
            type: message,
            timer: '2500'
        });
    }
    page_wise_setting();

    function page_wise_setting() {
        $('input[type="checkbox"]').on('change', function(e) {
            if (e.target.click) {
                page_wise_setting_checkbox();
            }
        });
        $('input[type="radio"]').on('change', function(e) {
            if (e.target.click) {
                if ($(".sort_by_asc").prop('checked') === true) {
                    page_wise_setting_table_row_sort_by($(this).val())
                } else if ($(".sort_by_desc").prop('checked') === true) {
                    page_wise_setting_table_row_sort_by($(this).val())
                }
            }

        });
    }
</script>
@endpush
@endsection