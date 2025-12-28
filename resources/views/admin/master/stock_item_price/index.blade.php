@extends('layouts.backend.app')
@section('title','Stock Item Price')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('custom_responsive/custom_master_responsive.css')}}">
<style>
    body {
        overflow: hidden;
        /* Hide scrollbars */
    }
</style>
@endpush
@section('admin_content')
<br>
@component('components.setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=>'stock_item_price',
    'page_unique_id'=>15,
    'title'=>'Stock Item Price',
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
                                                <h4>Stock Item price</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div style="float: right; margin-left: 5px;">
                                                <a style=" float:right; text-decoration: none; " href="{{route('master-dashboard')}}"><span class="fa fa-times-circle-o m-1" style="font-size:27px; color:#ff6666;"></span><span style="float:right;margin:2px; padding-top:5px; ">Close</span></a>
                                            </div>
                                            <div style="float: right; margin-left: 5px;">
                                                <a style=" float:right ;text-decoration: none; cursor: pointer" data-toggle="modal" data-target="#exampleModal"><span class="fa fa-cog m-1" style="font-size:27px;  color:Green;"></span><span style="float:right;margin:2px; padding-top:5px; ">Setting</span></a>
                                            </div>
                                            <div style="float: right;margin-left:9px">
                                                <a style="float:right; text-decoration: none;cursor: pointer; " onclick="print_html('landscape','Stock Item Price')"><span class="fa fa-print m-1" style="font-size:27px; color:teal;"></span><span style="float:right;margin:2px; padding-top:5px;">Print</span></a>
                                            </div>
                                            <div style="float: right;margin-left:9px">
                                                <a style="float:right; text-decoration: none;cursor: pointer;" class="excel" onclick="exportTableToExcel('Stock Item Price')"><span class="fa fa-file-excel-o m-1 " style="font-size:25px; color:Gray;"></span><span style="float:right;margin:2px; padding-top:5px;">Excel</span></a>
                                            </div>
                                            <div style="float: right;margin-left:9px">
                                                <a style="float:right; text-decoration: none;cursor: pointer;" class="pdf_download" onclick="generateTable('Stock Item Opening')"><span class="fa fa-file-pdf-o m-1 " style="font-size:25px; color:MediumSeaGree; "></span><span style="float:right;margin:2px; padding-top:5px;">Pdf</span></a>
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
                                                    <div class="col-md-4">
                                                        <label>Select Group : </label>
                                                        <select name="stock_group_id" class="form-control js-example-basic-single stock_group_id">
                                                            <option value="0">Primary</option>
                                                            {!!html_entity_decode($select_option_tree)!!}
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label>Select Price Type:</label>
                                                        <select name="price_type_id" class="form-control js-example-basic-single price_type_id">
                                                            <option value="1">Selling Price</option>
                                                            <option value="2">Purchage/Standard Price </option>
                                                            <option value="3">Wholesale Price</option>
                                                            <option value="4">POS Price</option>
                                                            <option value="7">Dealer Price</option>
                                                            <option value="8">Retrailer Price</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label></label>
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
                                <div class="row">
                                    <div class="col-xl-12">
                                        <!-- Zero config.table start -->
                                        <div class="card ">
                                            <div class="card-block table_content">
                                                <div class="dt-responsive table-responsive cell-border sd tableFixHead_double_header">
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
            <script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
            <script type="text/javascript" src="{{asset('pageWiseSetting/page_wise_setting.js')}}"></script>
            <script>
                $(function() {
                    // add new stock item price ajax request
                    $("#add_stock_item_prce_form").submit(function(e) {
                        e.preventDefault();
                        const fd = new FormData(this);
                        let last_updae_value = $('.last_update').is(':checked');
                        let user_name = $('.user_name').is(':checked');
                        $("#add_group_chart_btn").text('Adding...');
                        $.ajax({
                            url: '{{ url("stock-item-price/tree-view") }}',
                            method: 'post',
                            data: fd,
                            cache: false,
                            contentType: false,
                            processData: false,
                            dataType: 'json',
                            success: function(data, status, xhr) {
                                var html = '';
                                var tree = getTreeView(data.data, depth = 0);
                                html += '<table  id="tableId"  style=" border-collapse: collapse;"   class="table  customers " >';
                                    html += '<thead >';
                                        html += '<tr>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Stock Item</th>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;min-width: 150px;">Price</th>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;min-width: 150px;" >Setup Date</th>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;" >Operations</th>';
                                        html += '</tr>';
                                    html += '</thead>';
                                html += '<tbody class="qw" id="myTable">';
                                html += tree;
                                html += '</tbody>';
                                    html += '<tfoot>';
                                        html += '<tr>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Stock Item</th>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;min-width: 150px;">Price</th>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;min-width: 150px;">Setup Date</th>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Operations</th>';
                                        html += '</tr>';
                                    html += '</tfoot>';
                                html += '</table>';
                                html += `<div class="col-sm-12 text-center" >
                                        <span><b>Copyright &copy; 2014-2022 <a href="http://www.hamko-ict.com/">Hamko-ICT.</a> All rights reserved.</b></span>
                                        </div>`
                                $('.sd').html(html);
                                page_wise_setting_checkbox();
                                get_hover();
                                $('tbody').find('tr .sl').each(function(i) {
                                    let prd_name = $(this).text();
                                    let sl = i + 1;
                                    let trim_prd_name = prd_name.trim();
                                    prd_name = prd_name.replace(trim_prd_name, `${sl}.${trim_prd_name}`);
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
                                    claer_error();
                                    $('#error_group_chart_name').text(data.responseJSON.data.group_chart_name[0]);
                                }

                            }
                        });
                    });
                });

                // get Tree view table row
                function getTreeView(arr, depth = 0, chart_id = 0, stock_item = 0) {
                   // jquery date formate
                    $(document).ready(function () {
                        $( ".setup_date" ).datepicker({
                                dateFormat:"yy-mm-dd",
                                minDate:"{{company()->financial_year_start}}",
                                maxDate:"{{company()->financial_year_end}}",
                                changeMonth: true,
                                changeYear: true
                        });
                    })
                    let last_updae_value = $('.last_update').is(':checked');
                    let user_name = $('.user_name').is(':checked');
                    var eol = '<?php echo str_replace(array("\n", "\r"), array('\\n', '\\r'), PHP_EOL) ?>';
                    html = '';
                    let data_targer = " ";
                    var sl = 1;
                    arr.forEach(function(v, index) {
                        a = '&nbsp;&nbsp;&nbsp;';
                        h = a.repeat(depth);
                        if (chart_id != v.stock_group_id) {
                            html += "<tr class='left left-data  table-row'><td  style='width: 3%;  border: 1px solid #ddd; font-weight: bold;'>" + h + a + v.stock_group_name + "</td><td style='width: 3%;  border: 1px solid #ddd; color:#BBB;'></td><td style='width: 3%;  border: 1px solid #ddd;'></td><td style='width: 3%;  border: 1px solid #ddd;'></td><td style='width: 3%;  border: 1px solid #ddd;'></td><td  style='width: 3%;  border: 1px solid #ddd;' class='created_user d-none d-print-none'></td><td class='last_update d-none d-print-none'  style='width: 3%;  border: 1px solid #ddd;'></td>";
                            html += "</tr>";
                            chart_id = v.stock_group_id;
                        }
                        html += '<tr id="' + v.stock_item_id + '" class="left left-data  table-row ' + v.stock_item_id + '"  data-toggle="modal" ' + data_targer + '> ';
                        if ((stock_item != v.stock_item_id) && (v.stock_item_id !== null)) {
                            sl = 1
                            html += '<td class="sl" style="width: 3%; border-top: 1px solid #ddd;border-right: 1px solid #ddd;border-left: 1px solid #ddd;">' + h + a + h + a + v.product_name + '</td>' + eol;
                            html += '<td  style="width: 3%;  border: 1px solid #ddd;"></td>';
                            html += `<td  style="width: 3%;  border: 1px solid #ddd;">
                                        <span class="rate_id"></span>
                                        <input type="number" name="rate" step="any" class="form-control d-none rate"  placeholder="">
                                        <input type="hidden" class="form-control stock_item_id" name="stock_item_id" value="${v.stock_item_id}"">
                                    </td>`;
                            html += `<td style="width: 3%;  border: 1px solid #ddd;">
                                        <span class="setup_date_id"></span>
                                        <input   type="text" name="setup_date" class="form-control d-none setup_date"   placeholder="">
                                    </td>
                                    <td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none"></td>
                                    <td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"></td>`
                            html += '<td style="width: 2%;  border: 1px solid #ddd;">' +
                            @if(user_privileges_check('master', 'Selling Price', 'create_role'))
                                '<button class="btn btn-sm btn-primary add_new " >Add New</button>' +
                                '<button class="btn btn-sm btn-primary  back d-none" >Back</button>' +
                                '<button class="btn btn-sm btn-success  save d-none m-1" >Save</button>' +
                            @endif
                                '</td>';
                            stock_item = v.stock_item_id;
                            if (v.rate) {
                                html += '</tr><tr id="' + v.stock_item_id + '" class="left left-data  table-row ' + v.stock_item_id + '"  data-toggle="modal" ' + data_targer + '> <td  style="width: 3%;   border-left: 1px solid #ddd;"  "></td>' + eol;
                                html += '<td  style="width: 3%;  border: 1px solid #ddd;">' + (sl) + '</td>';
                                html += '<td  style="width: 3%;  border: 1px solid #ddd;">' +
                                    '<span class="rate_id">' + v.rate.toFixed("{{company()->amount_decimals}}") + '</span>' +
                                    '<input type="number" name="rate" class="form-control d-none rate" step="any" value="' + v.rate + '" >' +
                                    '<input type="hidden" class="form-control stock_item_id" name="stock_item_id" value="' + v.stock_item_id + '" >' +
                                    '</td>';
                                html += '<td   style="width: 3%;  border: 1px solid #ddd;">' +
                                    '<span class="setup_date_id">' + join(new Date(v.setup_date), options, ' ') + '</span>' +
                                    '<input type="text" name="setup_date" class="form-control d-none setup_date"  value="' + v.setup_date + '"  placeholder="">' +
                                    '<input type="hidden" class="form-control selling_price_id" name="selling_price_id" value="' + v.price_id + '">' +
                                    '</td>'
                                html += `<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">${(v.user_name?v.user_name:'')}</td>
                                     <td class=" last_update d-none d-print-none updated_history"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">${(v.updated_history ? JSON.parse(v.updated_history) : '')}</i></div></td>`
                                html += '<td style="width: 3%;  border: 1px solid #ddd;">' +
                                @if(user_privileges_check('master', 'Selling Price', 'alter_role'))
                                     '<button class="btn btn-sm btn-primary edit" >Edit</button>' +
                                @endif
                                @if(user_privileges_check('master', 'Selling Price', 'delete_role'))
                                    '<button class="btn btn-sm btn-danger  deleteIcon" >Delete</button>' +
                                @endif
                                    '<button class="btn btn-sm btn-success  save_edit d-none m-1" >Update</button>' +
                                    '<button class="btn btn-sm btn-primary back d-none" >Back</button>' +
                                    '</td>';
                                stock_item = v.stock_item_id;
                            }
                        } else {
                            if (v.stock_item_id) {
                                sl++;
                                html += '<td  style="width: 3%; border-left: 1px solid #ddd;"></td>';
                                html += '<td  style="width: 3%;  border: 1px solid #ddd;">' + (sl) + '</td>';
                                html += '<td  style="width: 3%;  border: 1px solid #ddd;">' +
                                    '<span class="rate_id">' + v.rate.toFixed("{{company()->amount_decimals}}") + '</span>' +
                                    '<input type="number" name="rate" step="any" class="form-control d-none rate" value="' + v.rate + '" >' +
                                    '<input type="hidden" class="form-control stock_item_id" name="stock_item_id" value="' + v.stock_item_id + '" >' +
                                    '</td>';
                                html += '<td  style="width: 4%;  border: 1px solid #ddd;">' +
                                    '<span class="setup_date_id">' + join(new Date(v.setup_date), options, ' ') + '</span>' +
                                    '<input type="text" name="setup_date" class="form-control d-none setup_date"  value="' + v.setup_date + '"  placeholder="">' +
                                    '<input type="hidden" class="form-control selling_price_id" name="selling_price_id" value="' + v.price_id + '">' +
                                    '</td>'
                                html += `<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">${(v.user_name?v.user_name:'')}</td>
                                     <td class="last_update d-none d-print-none updated_history"  style="width: 3%;  border: 1px solid #ddd;"><div><i class="history_font_size">${(v.updated_history ? JSON.parse(v.updated_history) : '')}</i></div></td>`
                                html += '<td style="width: 3%;  border: 1px solid #ddd;">' +
                                @if(user_privileges_check('master', 'Selling Price', 'alter_role'))
                                  '<button class="btn btn-sm btn-primary edit" >Edit</button>' +
                                @endif
                                @if(user_privileges_check('master', 'Selling Price', 'delete_role'))
                                    '<button class="btn btn-sm btn-danger deleteIcon" >Delete</button>' +
                                @endif
                                    '<button class="btn btn-sm btn-success  save_edit d-none m-1" >Update</button>' +
                                    '<button class="btn btn-sm btn-primary back d-none" >Back</button>' +
                                    '</td>';
                            }

                        }

                        html += "</tr> ";

                        if ('children' in v) {

                            html += getTreeView(v.children, depth + 1, chart_id, stock_item);
                        }
                    });
                    return html;
                }
            </script>

            <script>
                //new add stock item price
                $(document).ready(function() {
                    $(document).on('click', '.add_new', function() {

                        $(this).closest('tr').find('.rate_id,.setup_date_id,.add_new').addClass('d-none');
                        $(this).closest('tr').find('.rate, .setup_date, .back, .save').removeClass('d-none');
                    })
                    $(document).on('click', '.back', function() {
                        $(this).closest('tr').find('.rate_id,.setup_date_id,.add_new').removeClass('d-none');
                        $(this).closest('tr').find('.rate, .setup_date, .back, .save').addClass('d-none');
                    })

                    $(document).on('input', '.setup_date', function(e) {
                        let date = new Date($(this).val().trim());

                        if (!isNaN(date.getTime())) { // Check if date is valid
                            let year = date.getFullYear();
                            let month = String(date.getMonth() + 1).padStart(2, '0'); // Month is 0-indexed
                            let day = String(date.getDate()).padStart(2, '0');

                            let formattedDate = `${year}-${month}-${day}`;
                            $(this).val(formattedDate);
                        } else {
                            $(this).val('');
                            swal({
                                title: 'Oops...',
                                text: "Invalid date",
                                type: 'error',
                                timer: '2500'
                            })
                        }
                    });

                    $(document).on('click', '.save', function(e) {

                        e.preventDefault();
                        let stock_item_id = $(this).closest('tr').find('.stock_item_id').val();
                        let price_type = $('.price_type_id').val();
                        let rate = $(this).closest('tr').find('.rate').val();
                        let setup_date = $(this).closest('tr').find('.setup_date').val();
                        var csrf_token = $('meta[name="csrf-token"]').attr('content');
                        let last_update_value = $('#last_update').is(':checked');
                        let user_name = $('#user_name').is(':checked');
                        $.ajax({
                            url: '{{ route("stock-item-price.store") }}',
                            dataType: "JSON",
                            method: "POST",
                            data: {
                                'rate': rate,
                                'setup_date': setup_date,
                                'stock_item_id': stock_item_id,
                                'price_type': price_type,
                                '_token': csrf_token
                            },
                            success: (data) => {
                                swal.fire({
                                    title: 'Success!',
                                    text: data.message,
                                    type: 'success',
                                    timer: '2500'
                                })
                                let tr_id = $(this).closest('tr').attr('id');
                                let stock_item_id = $(this).closest('tr').find('.stock_item_id').val();
                                let sl_class = '.' + tr_id;
                                let sl = $(sl_class).length;
                                let rate = data.data.rate;
                                let updated_history = JSON.parse(data.data.updated_history);
                                let tr = `<tr class="left left-data  table-row ${tr_id}">
                                <td></td>

                                <td   style="width: 3%;  border: 1px solid #ddd;">
                                    ${sl}
                                </td>
                                <td  style="width: 3%;  border: 1px solid #ddd;">
                                    <span class="rate_id">${rate}</span>
                                    <input type="number" name="rate" class="form-control d-none rate" value="${rate}">
                                    <input type="hidden" class="form-control stock_item_id" name="stock_item_id" value="${data.data.stock_item_id}">
                                    <input type="hidden" class="form-control selling_price_id" name="selling_price_id" value="${data.data.price_id}">
                                </td>
                                <td  style="width: 3%;  border: 1px solid #ddd;">
                                    <span class="setup_date_id">${join( new Date(data.data.setup_date), options, ' ')}</span>
                                    <input type="date" name="setup_date" class="form-control d-none setup_date" value="${data.data.setup_date}">
                                    <input type="hidden" class="form-control selling_price_id" name="selling_price_id" value="${data.data.price_id}">
                                </td>
                                ${user_name?`<td  style="width: 3%;  border: 1px solid #ddd;" class="user_name">${(data.data.user_name?data.data.user_name:'')}</td>`:''}
                                ${last_update_value?`<td  style="width: 3%;  border: 1px solid #ddd;" class="updated_history" ><i class="history_font_size">${updated_history}</i></td>`:''}
                                <td style="width: 3%;  border: 1px solid #ddd;">
                                    <button class="btn btn-sm btn-primary edit" >Edit</button>
                                    <button class="btn btn-sm btn-danger deleteIcon" >Delete</button>
                                    <button class="btn btn-sm btn-success  save_edit d-none m-1" >Update</button>
                                    <button class="btn btn-sm btn-primary back d-none" >Back</button>
                                </td>
                            </tr>`;
                                $(sl_class).last().after(tr);
                                $(this).closest('tr').find('.rate').addClass('d-none').val('');
                                $(this).closest('tr').find('.setup_date').addClass('d-none').val('');
                                $(this).addClass('d-none');
                                $(this).closest('tr').find('.back').addClass('d-none');
                                $(this).closest('tr').find('.add_new').removeClass('d-none');

                            },
                            error: function() {
                                swal({
                                    title: 'Oops...',
                                    text: data.message,
                                    type: 'error',
                                    timer: '1500'
                                })
                            }
                        })

                    });
                    // new edit stock item price
                    $(document).on('click', '.edit', function() {
                        $(this).closest('tr').find('.rate_id,.setup_date_id,.edit,.deleteIcon').addClass('d-none');
                        $(this).closest('tr').find('.rate, .setup_date, .back, .save_edit').removeClass('d-none');
                        $(".stock_group_id").attr('disabled', 'disabled');
                        $(".price_type_id").attr('disabled', 'disabled');
                    })
                    $(document).on('click', '.back', function() {
                        $(this).closest('tr').find('.rate_id,.setup_date_id,.edit,.deleteIcon').removeClass('d-none');
                        $(this).closest('tr').find('.rate, .setup_date, .back, .save_edit').addClass('d-none');
                        $(".stock_group_id").removeAttr('disabled');
                        $(".price_type_id").removeAttr('disabled');
                    })
                    $(document).on('click', '.save_edit', function(e) {
                        e.preventDefault();
                        let price_type = $('.price_type_id').val();
                        let last_update_value = $('#last_update').is(':checked');
                        let user_name = $('#user_name').is(':checked');
                        let stock_item_id = $(this).closest('tr').find('.stock_item_id').val();
                        let id = $(this).closest('tr').find('.selling_price_id').val();
                        let price_type_id = $('.price_type_id').val();
                        let rate = $(this).closest('tr').find('.rate').val();
                        let setup_date = $(this).closest('tr').find('.setup_date').val();
                        var csrf_token = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            url: "{{ url('stock-item-price') }}" + '/' + id,
                            dataType: "JSON",
                            method: "POST",
                            data: {
                                'rate': rate,
                                'setup_date': setup_date,
                                'stock_item_id': stock_item_id,
                                'price_type': price_type,
                                '_token': csrf_token,
                                '_method': 'PUT',
                            },
                            success: (data) => {
                                swal({
                                    title: 'Success!',
                                    text: data.message,
                                    type: 'success',
                                    timer: '2500'
                                });
                                $(this).closest('tr').find('.rate_id').text(data.data.rate);
                                $(this).closest('tr').find('.setup_date_id').text(join(new Date(data.data.setup_date), options, ' '));
                                if (last_update_value == true) {
                                    $(this).closest('tr').find('.updated_history').html(JSON.parse(data.data.updated_history));
                                }
                                if (user_name == true) {
                                    $(this).closest('tr').find('.user_name').text(data.data.user_name ? data.data.user_name : '');
                                }
                                $(this).closest('tr').find('.rate_id,.setup_date_id,.edit,.deleteIcon').removeClass('d-none');
                                $(this).closest('tr').find('.rate, .setup_date, .back, .save_edit').addClass('d-none');
                                $(".stock_group_id").removeAttr('disabled');
                                $(".price_type_id").removeAttr('disabled');
                            },
                            error: function() {
                                swal({
                                    title: 'Oops...',
                                    text: data.message,
                                    type: 'error',
                                    timer: '2500'
                                })
                            }
                        })

                    })
                });
                // delete stock item price ajax request
                $(document).on('click', '.deleteIcon', function(e) {
                    var csrf_token = $('meta[name="csrf-token"]').attr('content');
                    var id = $(this).closest('tr').find('.selling_price_id').val();
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
                                url: "{{ url('stock-item-price') }}" + '/' + id,
                                type: "POST",
                                data: {
                                    '_method': 'DELETE',
                                    '_token': csrf_token
                                },
                                success: (data) => {
                                    swal({
                                        title: 'Success!',
                                        text: data.message,
                                        type: 'success',
                                        timer: '2500'
                                    })
                                    $(this).closest('tr').remove();

                                },
                                error: function() {
                                    swal({
                                        title: 'Oops...',
                                        text: data.message,
                                        type: 'error',
                                        timer: '1500'
                                    })
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
                page_wise_setting();
                function page_wise_setting() {
                    $('input[type="checkbox"]').on('change', function(e) {
                        if (e.target.click) {
                            page_wise_setting_checkbox();
                        }
                    });
                }
            </script>
            @endpush
            @endsection
