@extends('layouts.backend.app')
@section('title','Stock Commission')
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
<style>
    input[type=radio] {
        width: 20px;
        height: 20px;
    }

    input[type=checkbox] {
        width: 20px;
        height: 20px;
    }

    .card-block {
        padding: 0.25rem;
    }

    .card {
        margin-bottom: -29px;
    }

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
'page_title'=> 'stock_commission',
'page_unique_id'=>12,
'title'=>'Stock Commission',
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
                                                <h4>Stock Commission</h4>
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
                                                <a style="float:right; text-decoration: none;cursor: pointer;" class="print" onclick="print_html('landscape','Stock Group Price')"><span class="fa fa-print m-1" style="font-size:27px; color:teal;"></span><span style="float:right;margin:2px; padding-top:5px;">Print</span></a>
                                            </div>
                                            <div style="float: right;margin-left:9px">
                                                <a style="float:right; text-decoration: none;cursor: pointer;  " class="excel" onclick="exportTableToExcel('Stock Group Price')"><span class="fa fa-file-excel-o m-1 " style="font-size:25px; color:Gray;"></span><span style="float:right;margin:2px; padding-top:5px;">Excel</span></a>
                                            </div>
                                            <div style="float: right;margin-left:9px">
                                                <a style="float:right; text-decoration: none;cursor: pointer;" class="pdf_download pdf" onclick="generateTable('Stock Group Price')"><span class="fa fa-file-pdf-o m-1 " style="font-size:25px; color:MediumSeaGree; "></span><span style="float:right;margin:2px; padding-top:5px;">Pdf</span></a>
                                            </div>
                                            <div style="float: right; width:200px;">

                                                <input type="text" id="myInput" style="border-radius: 5px" class="form-control form-control pb-1" width="100%" placeholder="searching">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Page-header end -->
                                <!-- Page-body start -->
                                <div class="page-body">
                                    <div class="row">
                                        <div class="page-header m-0  ">
                                            <form id="add_stock_commission_form" method="POST">
                                                @csrf
                                                {{ method_field('POST') }}
                                                <div class="row ">
                                                    <div class="col-md-6">
                                                        <label>Select Group : </label>
                                                        <select name="stock_group_id" class="form-control js-example-basic-single stock_group_id">
                                                            <option value="0">Primary</option>
                                                            {!!html_entity_decode($select_option_tree)!!}
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label></label>
                                                        <div class="form-group">
                                                            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:100%">Search</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 ">
                                                        <label></label>
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
        </div>
    </div>
</div>
<br>
@push('js') <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js">
</script>
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script type="text/javascript" src="{{asset('pageWiseSetting/page_wise_setting.js')}}"></script>
<script>
    $(function() {
        // add new stock commission ajax request
        $("#add_stock_commission_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            $("#add_stock_commission_btn").text('Adding...');
            $.ajax({
                url: '{{ url("stock-commission/tree-view") }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response, status, xhr) {
                    var html = '';
                    var tree = getTreeView(response.data, depth = 0);
                    html += '<table  id="tableId"  style=" border-collapse: collapse;"   class="table  customers " >';
                    html += '<thead >';
                    html += '<tr>';
                    html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                    html += '<th style="width: 3%;  border: 1px solid #ddd;">Stock Group Name</th>';
                    html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                    html += '<th style="width: 3%;  border: 1px solid #ddd;">Commission</th>';
                    html += '<th style="width: 3%;  border: 1px solid #ddd;" >Setup Date</th>';
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
                    html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                    html += '<th style="width: 3%;  border: 1px solid #ddd;">Stock Group Name</th>';
                    html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                    html += '<th style="width: 3%;  border: 1px solid #ddd;">Commission</th>';
                    html += '<th style="width: 3%;  border: 1px solid #ddd;" >Setup Date</th>';
                    html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                    html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                    html += '<th style="width: 3%;  border: 1px solid #ddd;" >Operations</th>';
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
                        $(this).text(i + 1);
                    });
                },
                error: function(data, status, xhr) {
                    claer_error();
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
                        $('#error_group_chart_name').text(data.responseJSON.data
                            .group_chart_name[0]);
                    }

                }
            });
        });
    });

    // get Tree view table row
    function getTreeView(arr, depth = 0, chart_id = 0) {
        $(document).ready(function() {
            $(".setup_date").datepicker({
                dateFormat: "yy-mm-dd",
                minDate: "{{company()->financial_year_start}}",
                maxDate: "{{company()->financial_year_end}}",
                changeMonth: true,
                changeYear: true
            });
        })
        var eol = '<?php echo str_replace(array("\n", "\r"), array('\\n', '\\r'), PHP_EOL) ?>';
        html = '';
        let data_targer = " ";
        var sl = 1;
        arr.forEach(function(v, index) {
            a = '&nbsp;&nbsp;&nbsp;&nbsp;';
            h = a.repeat(depth);
            if (v.under != 0) {

                html += '<tr id="' + v.stock_group_id + '" class="left left-data editIcon table-row""  data-toggle="modal" data-target="#EditStockGroupModal"> ';
                if (chart_id != v.stock_group_id) {
                    html += '<tr id="' + v.stock_group_id + '" class="left left-data  table-row ' + v.stock_group_id + '"  data-toggle="modal" ' + data_targer + '>';
                    html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;"></td>';
                    html += '<td  style="width: 3%;  border: 1px solid #ddd;"><input type="hidden" class="form-control get_group_id" name="get_group_id" value="' + v.stock_group_id + '" ">' + h + a + v.stock_group_name + '</td>' + eol;
                    html += '<td  style="width: 3%;  border: 1px solid #ddd;"></td>';
                    html += `<td  style="width: 3%;  border: 1px solid #ddd;">
                        <div style="display: flex;flex-direction: row;">
                               <span class="commission_type_id"></span>
                                <select name="commission_type" class="form-control left-data d-none commission_type m-0 p-0"  style="margin:0%; max-width: 20%;" >
                                    <option value="1">(-) %</option>
                                    <option value="2">(+) %</option>
                                    <option value="3">(-)</option>
                                    <option value="4">(+)</option>
                                </select>

                        <span class="commission_id"></span>
                        <input type="number" name="commission" class="form-control d-none commission"  >

                        </div> </td>`;
                    // html += '<td  style="width: 3%;  border: 1px solid #ddd;">' +
                    //     '<span class="commission_id"></span>' +
                    //     '<input type="number" name="commission" class="form-control d-none commission"  >' +
                    //     '</td>';
                    html += '<td   style="width: 3%;  border: 1px solid #ddd;">' +
                        '<span class="setup_date_id"></span>' +
                        '<input type="text" name="setup_date" class="form-control d-none setup_date"  placeholder="">' +
                        '</td>'
                    html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none"></td>';
                    html += '<td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i></i></div></td>';
                    html += '<td style="width: 2%;  border: 1px solid #ddd;">' +
                        @if(user_privileges_check('master', 'stock_group__commission', 'create_role'))
                    '<button class="btn btn-sm btn-primary add_new " >Add New</button>' +
                    '<button class="btn btn-sm btn-primary  back d-none" >Back</button>' +
                    '<button class="btn btn-sm btn-success  save d-none m-1" >Save</button>' +
                    @endif
                        '</td>';
                    html += "</tr>";
                    chart_id = v.stock_group_id;
                    if (v?.commission || v?.commission==0) {
                        sl = 1
                        html += '<tr id="' + v.stock_group_id + '" class="left left-data  table-row ' + v.stock_group_id + '"  data-toggle="modal" ' + data_targer + '> <td  style="width: 3%; border-left: 1px solid #ddd;"></td>';

                        html += '<td  style="width: 3%;  border: 1px solid #ddd;"><input type="hidden" class="form-control get_group_id" name="get_group_id"  value="' + v.stock_group_id + '" ></td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;">' + (sl) + '</td>';
                        html += `<td  style="width: 3%;  border: 1px solid #ddd;">
                        <div style="display: flex;flex-direction: row;">
                               <span class="commission_type_id fw-bold pe-1">
                                    ${v.commission_type==1 ? '(-) % ' :
                                            v.commission_type==2 ? '(+) % ' :
                                                v.commission_type==3 ? '(-) ' :
                                                    v.commission_type==4 ? '(+) ' : ''}
                                </span>
                                <select
                                    name="commission_type"
                                    class="form-control left-data d-none commission_type m-0 p-0"
                                    style="margin:0%; max-width: 20%;"
                                    >
                                    <option ${v.commission_type==1 ? 'selected' : ''} value="1">(-) %</option>
                                    <option ${v.commission_type==2 ? 'selected' : ''} value="2">(+) %</option>
                                    <option ${v.commission_type==3 ? 'selected' : ''} value="3">(-)</option>
                                    <option ${v.commission_type==4 ? 'selected' : ''} value="4">(+)</option>
                                </select>

                            <span class="commission_id">${v.commission}</span>
                            <input type="hidden" class="form-control get_group_id" name="get_group_id" value="${v.stock_group_id}">
                            <input type="number" name="commission" class="form-control d-none commission" value="${v.commission}" >
                            <input type="hidden" class="form-control group_commission_id" name="group_commission_id" value="${v.group_commission_id}">

                        </div> </td>`;
                        // html += '<td  style="width: 3%;  border: 1px solid #ddd;">' +
                        //     '<span class="commission_id">' + v.commission + '</span>' +
                        //     '<input type="hidden" class="form-control get_group_id" name="get_group_id" value="' + v.stock_group_id + '" ">' +
                        //     '<input type="number" name="commission" class="form-control d-none commission" value="' + v.commission + '" >' +
                        //     '<input type="hidden" class="form-control group_commission_id" name="group_commission_id" value="' + v.group_commission_id + '">' +
                        //     '</td>';
                        html += '<td   style="width: 3%;  border: 1px solid #ddd;">' +
                            '<span class="setup_date_id">' + v.setup_date + '</span>' +
                            '<input type="text" name="setup_date" class="form-control d-none setup_date"  value="' + v.setup_date + '"  placeholder="">' +
                            '</td>'
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">' + (v.user_name ? v.user_name : '') + '</td>';
                        html += '<td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">' + (v.other_details ? JSON.parse(v.other_details) : '') + '</i></div></td>';
                        html += '<td style="width: 3%;  border: 1px solid #ddd;">' +
                            @if(user_privileges_check('master', 'stock_group__commission', 'alter_role'))
                        '<button class="btn btn-sm btn-primary edit" >Edit</button>' +
                        @endif
                        @if(user_privileges_check('master', 'stock_group__commission', 'delete_role'))
                        '<button class="btn btn-sm btn-danger deleteIcon" >Delete</button>' +
                        @endif
                            '<button class="btn btn-sm btn-success  save_edit d-none m-1" >Update</button>' +
                            '<button class="btn btn-sm btn-primary back d-none" >Back</button>' +
                            '</td>';
                        html += "</tr>";
                    }

                } else {
                    if (v?.commission || v?.commission==0) {
                        sl++;
                        html += '<tr id="' + v.stock_group_id + '" class="left left-data  table-row ' + v.stock_group_id + '"  data-toggle="modal" ' + data_targer + '> <td  style="width: 3%; border-top: 1px solid #ddd;border-right: 1px solid #ddd;border-left: 1px solid #ddd;"></td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;"><input type="hidden" class="form-control get_group_id"  value="' + v.stock_group_id + '"  name="get_group_id" ></td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;">' + (sl) + '</td>';
                        // html += '<td  style="width: 3%;  border: 1px solid #ddd;">' +
                        //     '<span class="commission_id">' + v.commission + '</span>' +
                        //     '<input type="number" name="commission" class="form-control d-none commission" value="' + v.commission + '" >' +
                        //     '<input type="hidden" class="form-control group_commission_id" name="group_commission_id" value="' + v.group_commission_id + '">' +
                        //     '</td>';
                        html += `<td  style="width: 3%;  border: 1px solid #ddd;">
                        <div style="display: flex;flex-direction: row;">
                               <span class="commission_type_id fw-bold pe-1">
                                    ${v.commission_type==1 ? '(-) % ' :
                                            v.commission_type==2 ? '(+) % ' :
                                                v.commission_type==3 ? '(-) ' :
                                                    v.commission_type==4 ? '(+) ' : ''}
                                </span>
                                <select
                                    name="commission_type"
                                    class="form-control left-data d-none commission_type m-0 p-0"
                                    style="margin:0%; max-width: 20%;"
                                    >
                                    <option ${v.commission_type==1 ? 'selected' : ''} value="1">(-) %</option>
                                    <option ${v.commission_type==2 ? 'selected' : ''} value="2">(+) %</option>
                                    <option ${v.commission_type==3 ? 'selected' : ''} value="3">(-)</option>
                                    <option ${v.commission_type==4 ? 'selected' : ''} value="4">(+)</option>
                                </select>

                            <span class="commission_id">${v.commission}</span>
                            <input type="hidden" class="form-control get_group_id" name="get_group_id" value="${v.stock_group_id}">
                            <input type="number" name="commission" class="form-control d-none commission" value="${v.commission}" >
                            <input type="hidden" class="form-control group_commission_id" name="group_commission_id" value="${v.group_commission_id}">

                        </div> </td>`;
                        html += '<td   style="width: 3%;  border: 1px solid #ddd;">' +
                            '<span class="setup_date_id">' + v.setup_date + '</span>' +
                            '<input type="text" name="setup_date" class="form-control d-none setup_date"  value="' + v.setup_date + '"  placeholder="">' +
                            '</td>'
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">' + (v.user_name ? v.user_name : '') + '</td>';
                        html += '<td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i>' + (v.other_details ? JSON.parse(v.other_details) : '') + '</i></div></td>';
                        html += '<td style="width: 3%;  border: 1px solid #ddd;">' +
                            @if(user_privileges_check('master', 'stock_group__commission', 'alter_role'))
                        '<button class="btn btn-sm btn-primary edit" >Edit</button>' +
                        @endif
                        @if(user_privileges_check('master', 'stock_group__commission', 'delete_role'))
                        '<button class="btn btn-sm btn-danger deleteIcon" >Delete</button>' +
                        @endif
                            '<button class="btn btn-sm btn-success  save_edit d-none m-1" >Update</button>' +
                            '<button class="btn btn-sm btn-primary back d-none" >Back</button>' +
                            '</td>';
                        html += "</tr>";
                    }
                }

                html += "</tr> ";
            }
            if ('children' in v) {

                html += getTreeView(v.children, depth + 1, chart_id);
            }
        });
        return html;
    }
</script>

<script>
    //new add stock commission
    $(document).ready(function() {
        $(document).on('click', '.add_new', function() {
            $(this).closest('tr').find('.commission_id, .commission_type_id, .setup_date_id,.add_new').addClass('d-none');
            $(this).closest('tr').find('.commission,.commission_type, .setup_date, .back, .save').removeClass('d-none');

        })
        $(document).on('click', '.back', function() {
            $(this).closest('tr').find('.commission_id,.commission_type_id,.setup_date_id,.add_new').removeClass('d-none');
            $(this).closest('tr').find('.commission,.commission_type, .setup_date, .back, .save').addClass('d-none');

        })
        $(document).on('click', '.save', function(e) {
            e.preventDefault();
            let last_history_value = $('#last_update').is(':checked');
            let user_name_value = $('#user_name').is(':checked');
            let commission_type = $(this).closest('tr').find('.commission_type').val();
            let commission = $(this).closest('tr').find('.commission').val();
            let get_group_id = $(this).closest('tr').find('.get_group_id').val();
            let setup_date = $(this).closest('tr').find('.setup_date').val();
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{ route("stock-commission.store") }}',
                dataType: "JSON",
                method: "POST",
                data: {
                    'stock_group_id': get_group_id,
                    'setup_date': setup_date,
                    'commission': commission,
                    'commission_type':commission_type,
                    '_token': csrf_token
                },
                success: (data) => {
                    let tr_id = $(this).closest('tr').attr('id');
                    let sl_class = '.' + tr_id;
                    let sl = $(sl_class).length;
                    let commission = data.data.commission;
                    let commission_type = data.data.commission_type;
                   console.log(commission_type)
                    let user_name = data.data.user_name;
                    let tr = `<tr class="left left-data  table-row  ${tr_id}">
                <td  style="width: 3%;  border: 1px solid #ddd;"></td>
                <td  style="width: 3%;  border: 1px solid #ddd;"></td>
                <td  style="width: 3%;  border: 1px solid #ddd;">
                    ${sl}
                </td>
                <td>
                <div style="display: flex;flex-direction: row;">
                               <span class="commission_type_id fw-bold pe-1">
                                    ${commission_type==1 ? '(-) % ' :
                                            commission_type==2 ? '(+) % ' :
                                                commission_type==3 ? '(-) ' :
                                                    commission_type==4 ? '(+) ' : ''}
                                </span>
                                <select
                                    name="commission_type"
                                    class="form-control left-data d-none commission_type m-0 p-0"
                                    style="margin:0%; max-width: 20%;"
                                    >
                                    <option ${commission_type==1 ? 'selected' : ''} value="1">(-) %</option>
                                    <option ${commission_type==2 ? 'selected' : ''} value="2">(+) %</option>
                                    <option ${commission_type==3 ? 'selected' : ''} value="3">(-)</option>
                                    <option ${commission_type==4 ? 'selected' : ''} value="4">(+)</option>
                                </select>

                             <span class="commission_id">${commission}</span>
                            <input type="number" name="rate" class="form-control d-none commission" value="${commission}" >
                            <input type="hidden" class="form-control group_commission_id" name="group_commission_id" value="${data.data.group_commission_id}">
                            <input type="hidden" class="form-control get_group_id"  value="${data.data.stock_group_id }"  name="get_group_id" >

                  </div>
                </td>

                <td  style="width: 3%;  border: 1px solid #ddd;">
                    <span class="setup_date_id">${data.data.setup_date}</span>
                    <input type="date" name="setup_date" class="form-control d-none setup_date" value="${data.data.setup_date}"  placeholder="">
                </td>
                ${user_name_value?`<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user  d-print-none">${user_name}</td>`:''}
                ${last_history_value?`<td class="last_update  d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i>'${( data.data.other_details ? JSON.parse(data.data.other_details) : '')}</i></div></td>`:''}
                <td style="width: 3%;  border: 1px solid #ddd;">
                    <button class="btn btn-sm btn-primary edit" >Edit</button>
                    <button class="btn btn-sm btn-danger deleteIcon" >Delete</button>
                    <button class="btn btn-sm btn-success  save_edit d-none m-1" >Update</button>
                    <button class="btn btn-sm btn-primary back d-none" >Back</button>
                </td>
            </tr>`;

                    $(sl_class).last().after(tr);
                    $(this).closest('tr').find('.commission').addClass('d-none').val('');
                    $(this).closest('tr').find('.commission_type').addClass('d-none').val(0);
                    $(this).closest('tr').find('.setup_date').addClass('d-none').val('');
                    $(this).addClass('d-none');
                    $(this).closest('tr').find('.back').addClass('d-none');
                    $(this).closest('tr').find('.add_new').removeClass('d-none');
                    swal({
                        title: 'Success!',
                        text: data.message,
                        type: 'success',
                        timer: '1500'
                    })
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
        // new edit stock commission
        $(document).on('click', '.edit', function() {
            $(this).closest('tr').find('.commission_id,.commission_type_id,.setup_date_id,.edit,.deleteIcon').addClass('d-none');
            $(this).closest('tr').find('.commission,.commission_type, .setup_date, .back,.save_edit').removeClass('d-none');
        })
        $(document).on('click', '.back', function() {
            $(this).closest('tr').find('.commission_id,.commission_type_id,.setup_date_id,.edit,.deleteIcon').removeClass('d-none');
            $(this).closest('tr').find('.commission,.commission_type, .setup_date, .back, .save_edit').addClass('d-none');
        })
        $(document).on('click', '.save_edit', function(e) {
            e.preventDefault();
            let id = $(this).closest('tr').find('.group_commission_id').val();
            let commission = $(this).closest('tr').find('.commission').val();
            let commission_type = $(this).closest('tr').find('.commission_type').val();
            let get_group_id = $(this).closest('tr').find('.get_group_id').val();
            let setup_date = $(this).closest('tr').find('.setup_date').val();
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ url('stock-commission') }}" + '/' + id,
                dataType: "JSON",
                method: "POST",
                data: {
                    'stock_group_id': get_group_id,
                    'setup_date': setup_date,
                    'commission': commission,
                    'commission_type':commission_type,
                    '_token': csrf_token,
                    '_method': 'PUT',
                },
                success: (data) => {

                    swal({
                        title: 'Success!',
                        text: data.message,
                        type: 'success',
                        timer: '1500'
                    });
                    $(this).closest('tr').find('.commission_id').text(data.data.commission);
                    $(this).closest('tr').find('.commission_type_id').text(data.data.commission_type);
                    $(this).closest('tr').find('.setup_date_id').text(data.data.setup_date);
                    $(this).closest('tr').find('.commission_id,.setup_date_id,.edit,.deleteIcon').removeClass('d-none');
                    $(this).closest('tr').find('.commission, .setup_date, .back, .save_edit').addClass('d-none');
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

        })
    });

    // delete stock commission ajax request
    $(document).on('click', '.deleteIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id = $(this).closest('tr').find('.group_commission_id').val();
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
                    url: "{{ url('stock-commission') }}" + '/' + id,
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
                            timer: '1500'
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
