@extends('layouts.backend.app')
@section('title','Group Chart')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
@endpush
@section('admin_content')
<br>
@php
  $page_wise_setting_data=page_wise_setting(Auth::user()->id,4);
@endphp
<!-- setting component-->
@component('components.setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=> 'Godown',
    'page_unique_id'=>4,
    'title'=>'Accounts Godown',
    'alias_true'=>'alias_true',
    'last_inset_true'=>'last_inset_true'
])
@endcomponent
<!--  Godown -->
@component('components.index', [
    'title' => 'Accounts Godown',
    'close' => 'Close',
    'print' => 'Print',
    'add_modal_data'=>'#AddGodownModal',
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'print_layout'=>'landscape',
    'print_header'=>'Accounts Godown',
    'setting_model'=>'setting_model',
    'close_route'=>route('master-dashboard'),
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Godown',
    'user_privilege_type'=>'create_role'
])
@slot('body')
<div class="dt-responsive table-responsive cell-border sd table_content tableFixHead">
</div>
@endslot
@endcomponent
<!-- add and edit form include -->
@include('admin.master.godown.form');
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>
$(document).ready(function() {
    //show select2
    $(".js-example-basic-single").select2({
        dropdownParent: $("#AddGodownModal")
    });
    $(".js-example-basic").select2({
        dropdownParent: $("#EditGodownModal")
    });
});

$(function() {
    // add new Godown ajax request
    $("#add_godown_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#add_godown_btn").text('Add');
        $.ajax({
            url: '{{ route("godown.store") }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                claer_error();
                swal_message(data.message, 'success', 'Successfully');
                getGodown();
                $("#add_godown_btn").text('Add');
                $("#add_godown_form")[0].reset();
                if({{$page_wise_setting_data?$page_wise_setting_data->last_insert_data_set:0}}==5){
                }else{
                    $("#godown_type").val(0).trigger('change');
                }
                $("#add_godown_form").get(0).reset();
                $("#AddGodownModal").modal('hide');
                location.reload();
            },
            error: function(data, status, xhr) {
                claer_error();
                if (data.status == 400) {
                    swal_message(data.responseJSON.message, 'error', 'Error');
                }
                if (data.status == 422) {
                    claer_error();
                    $('#error_godown_name').text(data.responseJSON.data.godown_name?data.responseJSON.data.godown_name[0]:'');
                    $('#error_alias').text(data.responseJSON.data.alias?data.responseJSON.data.alias[0]:'');
                }

            }
        });
    });
    // edit godown ajax request
    $(document).on('click', '.editIcon', function(e) {
        e.preventDefault();
        let id = $(this).attr('id');
        edit_godown(id);
    });
    // update godown ajax request
    $("#edit_godown_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id = $('.id').val();
        $("#edit_godown_btn").text('Adding...');
        $.ajax({
            url: "{{ url('godown') }}" + '/' + id,
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                claer_error()
                swal_message(data.message, 'success', 'Successfully');
                getGodown();
                claer_error()
                $("#edit_godown_btn").text('Update');
                $("#edit_godown_form")[0].reset();
                $("#godown_type").val(0).trigger('change');
                $("#EditGodownModal").modal('hide');


            },
            error: function(data, status, xhr) {

                if (data.status == 400) {
                    swal_message(data.message, 'error', 'Error');
                }
                if (data.status == 422) {
                    claer_error();
                    $('#edit_error_godown_name').text(data.responseJSON.data.godown_name?data.responseJSON.data.godown_name[0]:'');
                    $('#edit_error_alias').text(data.responseJSON.data.alias?data.responseJSON.data.alias[0]:'');
                }

            }
        });
    });
    // delete godown ajax request
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
                    url: "{{ url('godown') }}" + '/' + id,
                    type: "POST",
                    data: {
                        '_method': 'DELETE',
                        '_token': csrf_token
                    },
                    success: function(data) {
                        getGodown();
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
    //get  all data show
    function getGodown() {
        let data_targer = "{{user_privileges_check('master','Godown','alter_role')}}" == 1 ?
            "data-target='#EditGodownModal'" : "";
        $.ajax({
            url: "{{ url('godown_view')}}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                html += '<table  id="tableId"  style=" border-collapse: collapse; "   class="table table-striped customers " >';
                    html += '<thead >';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Godown Name</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Godown Type</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="alias d-none d-print-none">Alias</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                        html += '</tr>';
                    html += '</thead>';
                html += '<tbody id="myTable" class="qw">';
                $.each(response.data, function(key, v) {
                    html += '<tr id="' + v.godown_id + '" class="left left-data editIcon table-row"  data-toggle="modal" ' +  data_targer + ' >';
                        html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;">'+(key + 1) + '</td>';;
                        html +=  '<td  style="width: 3%;  border: 1px solid #ddd;"><input type="hidden" class="form-control get_group_id" name="get_group_id" value="' + v.godown_id + '" ">' + v.godown_name + '</td>';
                        html += '<td class="nature_val"  style="width: 3%;  border: 1px solid #ddd;">' + v.godown_type + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="alias d-none d-print-none">' + (v.alias ? v.alias : '') + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">' + (v.user_name?v.user_name:'') + '</td>';
                        html += '<td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">' + (v.other_details ? JSON.parse(v.other_details) : '') + '</i></div></td>';
                    html += "</tr> ";
                });
                html += '</tbody>';
                    html += '<tfoot>';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Godown Name</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Godown Type</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="alias d-none d-print-none">Alias</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                        html += '</tr>';
                    html += '</tfoot>';
                html += '</table>';
                html += `<div class="col-sm-12 text-center" >
                            <span><b>Copyright &copy; 2014-2022 <a href="http://www.hamko-ict.com/">Hamko-ICT.</a> All rights reserved.</b></span>
                        </div>`
                $(".sd").html(html);
                page_wise_setting_checkbox();
                if ($(".sort_by_asc").prop('checked') === true) {
                    page_wise_setting_table_row_sort_by($(".sort_by_asc").val());
                } else if ($(".sort_by_desc").prop('checked') === true) {
                    page_wise_setting_table_row_sort_by($(".sort_by_desc").val());
                }
                get_hover();
            }
        })

    }
    getGodown();
});

//group edit function
function edit_godown(id) {
    $.ajax({
        url: "{{ url('godown') }}" + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(response) {
            $(".id").val(response.data.godown_id);
            $(".godown_name").val(response.data.godown_name);
            $(".godown_type").val(response.data.godown_type).trigger('change');
            $(".unit_or_branch").val(response.data.unit_or_branch).trigger('change');
            $('.edit_alias').val(response.data.alias);
            $(".address").val(response.data.address).trigger('change');
        }
    });
}
//data validation data clear
function claer_error() {
    $('#error_godown_name').text('');
    $('#edit_error_godown_name').text('');
    $('#edit_error_alias').text('');
    $('#error_alias').text('');
}
$('.close').on('click',function(){
        $('.model_rest')[0].reset();
        $(".godown_type").val(0).trigger('change');
        $('#error_godown_name').text('');
        $('#edit_error_godown_name').text('');
        $('#edit_error_alias').text('');
        $('#error_alias').text('');

    })
    $('.model_rest_btn').on('click',function(){
        $(".godown_type").val(0).trigger('change');
        $('.model_rest')[0].reset();
        $('#error_godown_name').text('');
        $('#edit_error_godown_name').text('');
        $('#edit_error_alias').text('');
        $('#error_alias').text('');
    })
function swal_message(data, message, title_mas) {
        swal({
            title: title_mas,
            text: data,
            type: message,
            timer: '1500'
        });
}
</script>
@endpush
@endsection
