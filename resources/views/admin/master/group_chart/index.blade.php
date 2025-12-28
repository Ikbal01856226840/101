@extends('layouts.backend.app')
@section('title','Group Chart')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
@endpush
@section('admin_content')
<br>
@php
  $page_wise_setting_data=page_wise_setting(Auth::user()->id,1);
@endphp
<!-- setting component-->
@component('components.setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=> 'group_chart',
    'page_unique_id'=>1,
    'title'=>'Group Chart',
    'alias_true'=>'alias_true',
    'tree_collapser'=>'tree_collapser',
    'last_inset_true'=>'last_inset_true',
])
@endcomponent
<!-- add component-->
@component('components.index', [
    'title' => 'Accounts Group',
    'close_route'=>route('master-dashboard'),
    'add_modal_data'=>'#AddGroupChartModal',
    'plan_view'=>'Plain View',
    'tree_view'=>'Tree View',
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'print_layout'=>'landscape',
    'print_header'=>'Stock Group',
    'setting_model'=>'setting_model',
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Group',
    'user_privilege_type'=>'create_role'
])
@slot('body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead">
</div>
@endslot
@endcomponent
<!-- add and edit form include -->
@include('admin.master.group_chart.form');
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script type="text/javascript" src="{{asset('dist/jquery-simple-tree-table.js')}}"></script>

<script>
    $(document).ready(function() {
        get_nature_group();
        $('.group_id').on(' change', function() {
            get_nature_group();
        })
        //show select2
        $(".js-example-basic-single ").select2({
            dropdownParent: $("#AddGroupChartModal")
        });
        $(".js-example-basic").select2({
            dropdownParent: $("#EditGroupChartModal")
        });
    });
    //get nature group
    function get_nature_group() {
        var group_chart_id = $('.group_id').val();
        $.ajax({
            url: "{{ url('get_nature_group')}}",
            type: "GET",
            data: {
                "group_chart_id": group_chart_id
            },
            dataType: "json",
            success: function(data) {
                $.each(data, function(key, value) {
                    $(".nature_group1").html('<option value="' + value.nature_group + '">' + value.o +
                        '</option>');
                });
            }
        });
    }

    $(function() {
        // add new group chart ajax request
        $("#add_group_chart_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            $("#add_group_chart_btn").text('Adding...');
            $.ajax({
                url: '{{ route("group-chart.store") }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data, status, xhr) {
                    claer_error();
                    swal_message(data.message, 'success', 'Successfully');
                    allDataTree();
                    $("#add_group_chart_btn").text('Add Group Chart');
                    $("#add_group_chart_form")[0].reset();
                    $("#AddGroupChartModal").modal('hide');
                    if({{$page_wise_setting_data?$page_wise_setting_data->last_insert_data_set:0}}==5){
                    }else{
                        $(".group_id").val(0).trigger('change');
                    }
                    $(".nature_group1").val(0).trigger('change');
                    $("#add_group_chart_form").get(0).reset();
                    setTimeout(function(){
                      location.reload();
                   },200);
                },
                error: function(data, status, xhr) {
                    claer_error();
                    if (data.status == 400) {
                        swal_message(data.message, 'error', 'Error');
                    }
                    if (data.status == 422) {
                        claer_error();
                        $('#error_group_chart_name').text(data.responseJSON.data.group_chart_name?data.responseJSON.data.group_chart_name[0]:'');
                        $('#error_alias').text(data.responseJSON.data.alias?data.responseJSON.data.alias[0]:'');
                    }

                }
            });
        });
        // edit group chart ajax request
        $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            edit_group_chart(id);
        });
        // update group chart ajax request
        $("#edit_group_chart_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            var id = $('.id').val();
            $("#edit_group_chart_btn").text('Adding...');
            $.ajax({
                url: "{{ url('group-chart') }}" + '/' + id,
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data, status, xhr) {
                    swal_message(data.message, 'success', 'Successfully');
                    allDataTree();
                    claer_error()
                    $("#edit_group_chart_btn").text('Update');
                    $("#edit_group_chart_form")[0].reset();
                    $("#EditGroupChartModal").modal('hide');
                    setTimeout(function(){
                      location.reload();
                    },200);
                },
                error: function(data, status, xhr) {

                    if (data.status == 400) {
                        swal_message(data.responseJSON.message, 'error', 'Error');
                    }
                    if (data.status == 422) {
                        claer_error();
                        $('#edit_error_group_chart_name').text(data.responseJSON.data.group_chart_name?data.responseJSON.data.group_chart_name[0]:'');
                        $('#edit_error_alias').text(data.responseJSON.data.alias?data.responseJSON.data.alias[0]:'');
                    }

                }
            });
        });
        // delete group chart ajax request
        $(document).on('click', '.deleteIcon', function(e) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var id = $('.id').val();
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
                        url: "{{url('group-chart') }}" + '/' + id,
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            '_token': csrf_token
                        },
                        success: function(data) {
                            allDataTree();
                            swal_message(data.message, 'success', 'Successfully');
                        },
                        error: function() {
                            swal_message(data.responseJSON.message, 'error', 'Error');
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
        //plain all data show
        $('.plain_id').click(function() {
            $(this).addClass('d-none');
            $('.tree_id').removeClass('d-none');
            $.ajax({
                url: "{{ url('group-chart_view/plain_view')}}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    var html = '';
                    html += '<table id="tableId"  style=" border-collapse: collapse; "   class="table table-striped customers table-responsive" >';
                        html += '<thead >';
                            html += '<tr>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;">Group Name</th>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;">Alias</th>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;" >Nature of Group</th>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;" class="alias d-none d-print-none">Alias</th>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                            html += '</tr>';
                        html += '</thead>';
                    html += '<tbody id="myTable" class="qw">';
                    var i=0;
                    $.each(response.data, function(key, v) {
                        if (v.under!= 0) {
                            i++
                            let data_targer = "{{user_privileges_check('master','Group','alter_role')}}" == 1 ? (v.group_chart_id > 99 ? "data-target='#EditGroupChartModal'" : "") : "";
                            html += '<tr id="' + v.group_chart_id + '" class="left left-data editIcon table-row"  data-toggle="modal" ' + data_targer + ' >';
                            html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;">' + (i) + '</td>';
                            html += '<td  style="width: 3%;  border: 1px solid #ddd;"><input type="hidden" class="form-control get_group_id" name="get_group_id" value="' + v.group_chart_id + '" ">' + v.group_chart_name + '</td>';
                            html += '<td  style="width: 3%;  border: 1px solid #ddd;">' + (v.alias ? v.alias : '') + '</td>';
                            html += '<td class="nature_val"  style="width: 3%;  border: 1px solid #ddd;">' + v.o + '</td>';
                            html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="alias d-none d-print-none">' + (v.alias ? v.alias : '') + '</td>';
                            html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">' + (v.user_name?v.user_name:'') + '</td>';
                            html += '<td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i>' + (v.other_details ? JSON.parse(v.other_details) : '') + '</i></div></td>';
                            html += "</tr> ";
                        }
                    });
                    html += '</tbody>';
                        html += '<tfoot>';
                            html += '<tr>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;">Group Name</th>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;">Alias</th>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;" >Nature of Group</th>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;" class="alias d-none d-print-none">Alias</th>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                                html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                            html += '</tr>';
                        html += '</tfoot>';
                    html += '</table>';
                    html += `<div class="col-sm-12 text-center" >
                            <span><b>Copyright &copy; 2014-2022 <a href="http://www.hamko-ict.com/">Hamko-ICT.</a> All rights reserved.</b></span>
                        </div>`
                    $(".sd").empty();
                    $(".sd").append(html);
                    page_wise_setting_checkbox();
                    if ($(".sort_by_asc").prop('checked') === true) {
                        page_wise_setting_table_row_sort_by($(".sort_by_asc").val());
                    } else if ($(".sort_by_desc").prop('checked') === true) {
                        page_wise_setting_table_row_sort_by($(".sort_by_desc").val());
                    }
                    get_hover();

                }
            })
        });
        $('.tree_id').click(function() {
            $(this).addClass('d-none');
            $('.plain_id').removeClass('d-none');
            allDataTree();
        });
    });
    //all data tree show
    function allDataTree() {
        $(".modal").show();
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ url('group-chart_view/tree_view')}}",
            success: function(response) {
                $(".modal").hide();
                var html = '';
                var tree = getTreeView(response, depth = 0);
                html += '<table  id="tableId"  style=" border-collapse: collapse; "   class="table table-striped customers table_content" >';
                    html += '<thead>';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Group Name</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Alias</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" >Nature of Group</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="alias d-none d-print-none">Alias</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                        html += '</tr>';
                    html += '</thead>';
                html += '<tbody id="myTable" class="qw">';
                html += tree;
                html += '</tbody>';
                    html += '<tfoot>';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Group Name</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Alias</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" >Nature of Group</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="alias d-none d-print-none">Alias</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                        html += '</tr>';
                    html += '</tfoot>';
                html += '</table>';
                html += `<div class="col-sm-12 text-center" >
                            <span><b>Copyright &copy; 2014-2022 <a href="http://www.hamko-ict.com/">Hamko-ICT.</a> All rights reserved.</b></span>
                        </div>`
                $('.sd').html(html);
                $('#tableId').simpleTreeTable({
                    expander: $('#expander'),
                    collapser: $('#collapser'),
                    sstore: 'session',
                    storeKey: 'simple-tree-table-basic',
                    iconPosition: 'td:eq(1) > span'
                });
                page_wise_setting_checkbox();
                get_hover();
                $('tbody').find('tr .sl').each(function(i) {
                    $(this).text(i + 1);
                });


            }
        })
    }
    // get Tree view table row
    function getTreeView(arr, depth = 0) {
        var eol = '<?php echo str_replace(array("\n", "\r"), array('\\n', '\\r'), PHP_EOL) ?>';
        html = '';
        arr.forEach(function(v) {
            a = '&nbsp;&nbsp;';
            h = a.repeat(depth);
            if (v.under != 0) {
                let data_targer = "{{user_privileges_check('master','Group','alter_role')}}" == 1 ? (v.group_chart_id > 99 ? "data-target='#EditGroupChartModal'" : "") : "";
                html += `<tr  data-node-id="${v.group_chart_id}" data-node-pid="${v.under}" id="${v.group_chart_id}"  class="left left-data editIcon table-row"  data-toggle="modal" ${data_targer}> `;
                html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;"></td>';
                html += '<td  style="width: 3%;  border: 1px solid #ddd;"><input type="hidden" class="form-control get_group_id" name="get_group_id" value="' + v.group_chart_id + '"><span>' + h + a + v.group_chart_name + '</span></td>' + eol;
                html += '<td  style="width: 3%;  border: 1px solid #ddd;">' + (v.alias ? v.alias : '') + '</td>';
                html += '<td class="nature_val"  style="width: 3%;  border: 1px solid #ddd;">' + v.o + '</td>';
                html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="alias d-none d-print-none">' + (v.alias ? v.alias : '') + '</td>';
                html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">' + (v.user_name?v.user_name:'' )+ '</td>';
                html += '<td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">' + (v.other_details ? JSON.parse(v.other_details) : '') + '</i></div></td>';
                html += "</tr> ";
            }
            if ('children' in v) {

                html += getTreeView(v.children, depth + 1);
            }
        });
        return html;
    }
    //group chart edit modal show
    function tableTrModal() {
        edit_group_chart($('.currentNav').closest('tr').attr('id'));
        $('.group_id').val($('.currentNav').closest('tr').attr('id'));
        if ($('.currentNav').closest('tr').attr('id') > 99) {
            $(this).find('form').trigger('reset');
            $('#EditGroupChartModal').modal('show');
        }
    }
    //group edit function
    function edit_group_chart(id) {
        $.ajax({
            url: "{{ url('group-chart') }}" + '/' + id,
            type: "GET",
            dataType: "JSON",
            success: function(response) {
                $(".id").val(response.data.group_chart_id);
                $(".group_chart_name").val(response.data.group_chart_name);
                $(".unit_or_branch").val(response.data.unit_or_branch).trigger('change');
                $(".group_id").val(response.data.under).trigger('change');
                $('#edit_alias').val(response.data.alias);
                $(".nature_group1").val(response.data.nature_group).trigger('change');
                get_nature_group();
            }
        });
    }
    //data validation data clear
    function claer_error() {
        $('#error_group_chart_name').text('');
        $('#edit_error_alias').text('');
    }
    $(document).ready(function() {
        allDataTree();
    });
    function swal_message(data, message, title_mas) {
        swal({
            title: title_mas,
            text: data,
            type: message,
            timer: '1500'
        });
   }
   $('.close').on('click',function(){
        $('.model_rest')[0].reset();
        $(".group_id").val(0).trigger('change');
        $('#error_group_chart_name').text('');
        $('#edit_error_alias').text('');

    })
    $('.model_rest_btn').on('click',function(){
        $(".group_id").val(0).trigger('change');
        $('.model_rest')[0].reset();
        $('#error_group_chart_name').text('');
        $('#edit_error_alias').text('');
    })
</script>
@endpush
@endsection
