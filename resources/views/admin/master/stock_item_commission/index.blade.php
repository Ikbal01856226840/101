@extends('layouts.backend.app')
@section('title','Stock Item Commission')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
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
    'page_title'=>'stock_item_commission',
    'page_unique_id'=>16,
    'title'=>'Stock Item Commission',
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
                                                <h4>Stock Item Commission</h4>
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
                                                <a style="float:right; text-decoration: none;cursor: pointer; " onclick="print_html('landscape','Stock Item Commission')"><span class="fa fa-print m-1" style="font-size:27px; color:teal;"></span><span style="float:right;margin:2px; padding-top:5px;">Print</span></a>
                                            </div>
                                            <div style="float: right;margin-left:9px">
                                                <a style="float:right; text-decoration: none;cursor: pointer; " class="excel" onclick="exportTableToExcel('Stock Item Commission')"><span class="fa fa-file-excel-o m-1 " style="font-size:25px; color:Gray;"></span><span style="float:right;margin:2px; padding-top:5px;">Excel</span></a>
                                            </div>
                                            <div style="float: right;margin-left:9px">
                                                <a style="float:right; text-decoration: none;cursor: pointer; " class="pdf_download" onclick="generateTable('Stock Item Commission')"><span class="fa fa-file-pdf-o m-1 " style="font-size:25px; color:MediumSeaGree; "></span><span style="float:right;margin:2px; padding-top:5px;">Pdf</span></a>
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
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Select Group : </label>
                                                        <select name="stock_group_id" class="form-control js-example-basic-single stock_group_id">
                                                            <option value="0">Primary</option>
                                                            {!!html_entity_decode($select_option_tree)!!}
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
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
                                                <div class="dt-responsive table-responsive cell-border sd  tableFixHead_double_header">
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
    @push('js')
    <!-- table hover js -->
    <script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
    <script type="text/javascript" src="{{asset('pageWiseSetting/page_wise_setting.js')}}"></script>
    <script>
        var amount_decimals = "{{company()->amount_decimals}}";
        $(function() {
            // add new stock item commission ajax request
            $("#add_stock_item_prce_form").submit(function(e) {
                e.preventDefault();
                const fd = new FormData(this);
                let last_update_value = $('.last_update').is(':checked');
                let user_name = $('.user_name').is(':checked');
                $("#add_group_chart_btn").text('Add');
                $.ajax({
                    url: '{{ url("stock-item-commission/tree-view") }}',
                    method: 'post',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(data, status, xhr) {
                        let html_header = [];
                        html_header.push(`<table  id="tableId"  style=" border-collapse: collapse;"   class="table  customers " >
                       <thead >
                           <tr>
                               <th style="width: 3%;  border: 1px solid #ddd;">Stock Item</th>
                               <th style="width: 3%;  border: 1px solid #ddd;">SL</th>
                                <th style="width: 3%;  border: 1px solid #ddd;">Commission</th>
                                <th style="width: 3%;  border: 1px solid #ddd;" >Setup Date</th>
                                <th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>
                                <th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>
                              <th style="width: 3%;  border: 1px solid #ddd;" >Operations</th>
                           </tr>
                        </thead>
                   <tbody class="qw" id="myTable">`);
                        html_header.push(getTreeView(data.data, depth = 0));
                        html_header.push(`</tbody>
                       <tfoot>
                            <tr>
                               <th style="width: 3%;  border: 1px solid #ddd;">Stock Item</th>
                               <th style="width: 3%;  border: 1px solid #ddd;">SL</th>
                                <th style="width: 3%;  border: 1px solid #ddd;">Commission</th>
                                <th style="width: 3%;  border: 1px solid #ddd;" >Setup Date</th>
                                <th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>
                               <th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>
                                <th style="width: 3%;  border: 1px solid #ddd;" >Operations</th>
                            </tr>
                        </tfoot>
                         </table>
                       <div class="col-sm-12 text-center" >
                                <span><b>Copyright &copy; 2014-2022 <a href="http://www.hamko-ict.com/">Hamko-ICT.</a> All rights reserved.</b></span>
                            </div>`);

                        $('.sd').html(html_header.join(""));
                        page_wise_setting_checkbox();
                        get_hover();
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
            $(document).ready(function () {
                $( ".setup_date" ).datepicker({
                        dateFormat:"yy-mm-dd",
                        minDate:"{{company()->financial_year_start}}",
                        maxDate:"{{company()->financial_year_end}}",
                        changeMonth: true,
                        changeYear: true
                });
            })
            let last_update_value = $('.last_update').is(':checked');
            let user_name = $('.user_name').is(':checked');
            let html = [];
            let data_targer = "";
            var sl = 1;
            arr.forEach(function(v, index) {
                a = '&nbsp;&nbsp;&nbsp;';
                h = a.repeat(depth);
                if (chart_id != v.stock_group_id) {
                    html.push(`<tr class='left left-data  table-row'>
                   <td  style='width: 3%;  border: 1px solid #ddd; font-weight: bold;'>${h + a + v.stock_group_name}</td>
                   <td style='width: 3%;  border: 1px solid #ddd; color:#BBB;'></td>
                   <td style='width: 3%;  border: 1px solid #ddd;'></td>
                   <td style='width: 3%;  border: 1px solid #ddd;'></td>
                   <td style='width: 3%;  border: 1px solid #ddd;'></td>
                   <td  style='width: 3%;  border: 1px solid #ddd;' class='created_user d-none d-print-none'></td>
                   <td class='last_update d-none d-print-none'  style='width: 3%;  border: 1px solid #ddd;'></td></tr>`);

                    chart_id = v.stock_group_id;
                }
                html.push(`<tr id="${v.stock_item_id}" class="left left-data  table-row ${v.stock_item_id} "data-toggle="modal" ${data_targer}>`);
                if ((stock_item != v.stock_item_id) && (v.stock_item_id !== null)) {
                    sl = 1
                    html.push(`<td class="sl" style="width: 3%; border-top: 1px solid #ddd;border-right: 1px solid #ddd;border-left: 1px solid #ddd;">${h+a+h+a+v.product_name}</td>
                    <td  style="width: 3%;  border: 1px solid #ddd;"></td>
                    <td  style="width: 3%;  border: 1px solid #ddd;">
                                <span class="commission_id"></span>
                                <input type="number" name="commission" class="form-control d-none commission">
                                <input type="hidden" class="form-control stock_item_id" name="stock_item_id" value="${v.stock_item_id}">
                            </td>
                     <td style="width: 3%;  border: 1px solid #ddd;">
                                <span class="setup_date_id"></span>
                                <input type="text" name="setup_date" class="form-control d-none setup_date">
                            </td>
                            <td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none"></td>
                            <td class="last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"></td>
               <td style="width: 2%;  border: 1px solid #ddd;">`)
                    @if(user_privileges_check('master', 'stock_item__commission', 'create_role'))
                    html.push(`<button class="btn btn-sm btn-primary add_new ">Add New</button>
                 <button class="btn btn-sm btn-primary  back d-none">Back</button>
                 <button class="btn btn-sm btn-success  save d-none m-1" >Save</button>`);
                    @endif
                    html.push(`</td>`);
                    stock_item = v.stock_item_id;
                    if (v.commission) {
                        html.push(`</tr><tr id="${v.stock_item_id}" class="left left-data  table-row ${v .stock_item_id}"  data-toggle="modal" ${data_targer}> <td  style="width: 3%;   border-left: 1px solid #ddd;"></td>
                        <td  style="width: 3%;  border: 1px solid #ddd;">${ (sl)}</td>
                        <td  style="width: 3%;  border: 1px solid #ddd;">
                        <span class="commission_id">${ v.commission.toFixed(amount_decimals)}</span>
                        <input type="number" name="commission" class="form-control d-none commission" value="${v.commission}" >
                        <input type="hidden" class="form-control stock_item_id" name="stock_item_id" value="${v.stock_item_id}" >
                        </td>
                       <td   style="width: 3%;  border: 1px solid #ddd;">
                        <span class="setup_date_id">${join(new Date(v.setup_date), options, ' ')}</span>
                        <input type="text" name="setup_date" class="form-control d-none setup_date"  value="${v.setup_date}">
                        <input type="hidden" class="form-control item_commission_id" name="item_commission_id" value="${v.item_commission_id}">
                        </td>
                       <td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">${(v.user_name?v.user_name:'')}</td>
                                     <td class=" last_update d-none d-print-none updated_history"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">${(v.updated_history ? JSON.parse(v.updated_history) : '')}</i></div></td>
                      <td style="width: 3%;  border: 1px solid #ddd;">`);
                        @if(user_privileges_check('master', 'stock_item__commission', 'alter_role'))
                        html.push(`<button class="btn btn-sm btn-primary edit" >Edit</button>`)
                        @endif
                        @if(user_privileges_check('master', 'stock_item__commission', 'delete_role'))
                        html.push(`<button class="btn btn-sm btn-danger  deleteIcon" >Delete</button>`)
                        @endif
                        html.push(`<button class="btn btn-sm btn-success  save_edit d-none m-1" >Update</button>
                        <button class="btn btn-sm btn-primary back d-none" >Back</button>
                        </td>`)


                        stock_item = v.stock_item_id;
                    }
                } else {
                    if (v.stock_item_id) {
                        sl++;
                        html.push(`<td  style="width: 3%; border-left: 1px solid #ddd;"></td>
                            <td  style="width: 3%;  border: 1px solid #ddd;">${ (sl)}</td>
                            <td  style="width: 3%;  border: 1px solid #ddd;">
                            <span class="commission_id">${v.commission.toFixed(amount_decimals)}</span>
                            <input type="number" name="commission" class="form-control d-none commission" value="${v.commission}" >
                            <input type="hidden" class="form-control stock_item_id" name="stock_item_id" value="${v.stock_item_id}" >
                            </td>
                        <td  style="width: 4%;  border: 1px solid #ddd;">
                        <span class="setup_date_id">${join(new Date(v.setup_date), options, ' ')}</span>
                        <input type="text" name="setup_date" class="form-control d-none setup_date"  value="${v.setup_date}">
                        <input type="hidden" class="form-control item_commission_id" name="item_commission_id" value="${v.item_commission_id}">
                        </td>
                       <td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">${(v.user_name?v.user_name:'')}</td>
                            <td class=" last_update d-none d-print-none updated_history"  style="width: 3%;  border: 1px solid #ddd;"><div><i class="history_font_size"><${(v.updated_history ? JSON.parse(v.updated_history) : '')}</i></div></td>
                       <td style="width: 3%;  border: 1px solid #ddd;">`);
                        @if(user_privileges_check('master', 'stock_item__commission', 'alter_role'))
                        html.push(`<button class="btn btn-sm btn-primary edit" >Edit</button>`);
                        @endif
                        @if(user_privileges_check('master', 'stock_item__commission', 'delete_role'))
                        html.push(`<button class="btn btn-sm btn-danger deleteIcon" >Delete</button>`)
                        @endif
                        html.push(`<button class="btn btn-sm btn-success  save_edit d-none m-1">Update</button>
                        <button class="btn btn-sm btn-primary back d-none" >Back</button>
                        </td>`);
                    }

                }

                html.push(`</tr>`);

                if ('children' in v) {

                    html.push(getTreeView(v.children, depth + 1, chart_id, stock_item));
                }
            });
            return html.join("");
        }
    </script>

    <script>
        //new add stock item commission
        $(document).ready(function() {
            $(document).on('click', '.add_new', function() {
                $(this).closest('tr').find('.commission_id,.setup_date_id,.add_new').addClass('d-none');
                $(this).closest('tr').find('.commission, .setup_date, .back, .save').removeClass('d-none');

            })
            $(document).on('click', '.back', function() {
                $(this).closest('tr').find('.commission_id,.setup_date_id,.add_new').removeClass('d-none');
                $(this).closest('tr').find('.commission, .setup_date, .back, .save').addClass('d-none');
            })
            $(document).on('click', '.save', function(e) {
                e.preventDefault();
                let stock_item_id = $(this).closest('tr').find('.stock_item_id').val();
                let commission = $(this).closest('tr').find('.commission').val();
                let setup_date = $(this).closest('tr').find('.setup_date').val();
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                let last_update_value = $('#last_update').is(':checked');
                let user_name = $('#user_name').is(':checked');
                $.ajax({
                    url: '{{ route("stock-item-commission.store") }}',
                    dataType: "JSON",
                    method: "POST",
                    data: {
                        'commission': commission,
                        'setup_date': setup_date,
                        'stock_item_id': stock_item_id,
                        '_token': csrf_token
                    },
                    success: (data) => {

                        let tr_id = $(this).closest('tr').attr('id');
                        let stock_item_id = $(this).closest('tr').find('.stock_item_id')
                            .val();
                        let sl_class = '.' + tr_id;
                        let sl = $(sl_class).length;
                        let commission = data.data.commission;
                        let updated_history = JSON.parse(data.data.updated_history);
                        let tr = `<tr class="left left-data  table-row  ${tr_id}">
                        <td></td>
                        <td   style="width: 3%;  border: 1px solid #ddd;">
                            ${sl}
                        </td>
                        <td  style="width: 3%;  border: 1px solid #ddd;">
                            <span class="commission_id">${commission}</span>
                            <input type="number" name="commission" class="form-control d-none commission" value="${commission}" >
                            <input type="hidden" class="form-control stock_item_id" name="stock_item_id" value="${data.data.stock_item_id}" >
                        </td>
                        <td  style="width: 3%;  border: 1px solid #ddd;">
                            <span class="setup_date_id">${join(new Date(data.data.setup_date), options, ' ')}</span>
                            <input type="date" name="setup_date" class="form-control d-none setup_date" value="${data.data.setup_date}"  placeholder="">
                            <input type="hidden" class="form-control item_commission_id" name="item_commission_id" value="${data.data.item_commission_id}">
                        </td>
                        <td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">${(data.data.user_name?data.data.user_name:'')}</td>
                        <td class=" last_update d-none d-print-none updated_history"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">${updated_history}</i></div></td>
                        <td style="width: 3%;  border: 1px solid #ddd;">
                            <button class="btn btn-sm btn-primary edit" >Edit</button>
                            <button class="btn btn-sm btn-danger deleteIcon" >Delete</button>
                            <button class="btn btn-sm btn-success  save_edit d-none m-1" >Update</button>
                            <button class="btn btn-sm btn-primary back d-none" >Back</button>
                        </td>
                    </tr>`;
                        $(sl_class).last().after(tr);
                        $(this).closest('tr').find('.commission').addClass('d-none').val('');
                        $(this).closest('tr').find('.setup_date').addClass('d-none').val('');
                        $(this).addClass('d-none');
                        $(this).closest('tr').find('.back').addClass('d-none');
                        $(this).closest('tr').find('.add_new').removeClass('d-none');
                        swal({
                            title: 'Success!',
                            text: data.message,
                            type: 'success',
                            timer: '2500'
                        })
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

            });
            // new edit stock item commission
            $(document).on('click', '.edit', function() {
                $(this).closest('tr').find('.commission_id,.setup_date_id,.edit,.deleteIcon').addClass('d-none');
                $(this).closest('tr').find('.commission, .setup_date, .back, .save_edit').removeClass('d-none');
                $(".stock_group_id").attr('disabled', 'disabled');

            })
            $(document).on('click', '.back', function() {
                $(this).closest('tr').find('.commission_id,.setup_date_id,.edit,.deleteIcon')
                    .removeClass('d-none');
                $(this).closest('tr').find('.commission, .setup_date, .back, .save_edit').addClass(
                    'd-none');
                $(".stock_group_id").removeAttr('disabled');

            })
            $(document).on('click', '.save_edit', function(e) {
                e.preventDefault();
                let last_update_value = $('#last_update').is(':checked');
                let user_name = $('#user_name').is(':checked');
                let stock_item_id = $(this).closest('tr').find('.stock_item_id').val();
                let id = $(this).closest('tr').find('.item_commission_id').val();

                let commission = $(this).closest('tr').find('.commission').val();
                let setup_date = $(this).closest('tr').find('.setup_date').val();
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: "{{ url('stock-item-commission') }}" + '/' + id,
                    dataType: "JSON",
                    method: "POST",
                    data: {
                        'commission': commission,
                        'setup_date': setup_date,
                        'stock_item_id': stock_item_id,
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
                        $(this).closest('tr').find('.commission_id').text(data.data.commission);
                        $(this).closest('tr').find('.setup_date_id').text(join(new Date(data.data.setup_date), options, ' '));
                        $(this).closest('tr').find('.updated_history').html(JSON.parse(data.data.updated_history));
                        $(this).closest('tr').find('.user_name').text(data.data.user_name ? data.data.user_name : '');
                        $(this).closest('tr').find('.commission_id,.setup_date_id,.edit,.deleteIcon').removeClass('d-none');
                        $(this).closest('tr').find('.commission, .setup_date, .back, .save_edit').addClass('d-none');

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
        // delete stock item commission ajax request
        $(document).on('click', '.deleteIcon', function(e) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var id = $(this).closest('tr').find('.item_commission_id').val();
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
                        url: "{{ url('stock-item-commission') }}" + '/' + id,
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
        // page wise setting
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
