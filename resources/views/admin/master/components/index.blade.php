@extends('layouts.backend.app')
@section('title','Components')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
@endpush
@section('admin_content')
<br>
<!-- setting component-->
@component('components.setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=> 'Components',
    'page_unique_id'=>6,
    'title'=>'Components',
    'alias_true'=>'alias_true',
    'insert_settings'=>'insert_settings',
    'view_settings'=>'view_settings'
])
@endcomponent
<!--  Godown -->
<!-- add component-->
@component('components.index', [
    'title' => 'Components',
    'close' => 'Close',
    'print' => 'Print',
    'add_modal_data'=>'#AddComponentsModal',
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'print_layout'=>'landscape',
    'print_header'=>'Components',
    'setting_model'=>'setting_model',
    'close_route'=>route('master-dashboard'),
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Components',
    'user_privilege_type'=>'create_role'
])
@slot('body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead">
</div>
@endslot
@endcomponent
<!-- add and edit form include -->
@include('admin.master.components.form');
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>
$(document).ready(function() {
    //show select2
    $(".js-example-basic-single").select2({
        dropdownParent: $("#AddComponentsModal")
    });
    $(".js-example-basic").select2({
        dropdownParent: $("#EditComponentsModal")
    });
});
$(function() {
    // add new Components ajax request
    $("#add_components_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#add_components_btn").text('Add');
        $.ajax({
            url: '{{ url("components") }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                claer_error();
                swal_message(data.message, 'success');
                getComponts();
                $("#add_components_btn").text('Add ');
                $("#add_components_form")[0].reset();
                $("#AddComponentsModal").modal('hide');
            },
            error: function(data, status, xhr) {
                claer_error();
                if (data.status == 400) {
                    swal_message(data.message, 'error');
                }
                if (data.status == 422) {
                    claer_error();
                    $('#error_components_name').text(data.responseJSON.data.comp_name[0]);
                }

            }
        });
    });
    // edit components ajax request
    $(document).on('click', '.editIcon', function(e) {
        e.preventDefault();
        let id = $(this).attr('id');
        edit_components(id);
    });

    // update components ajax request
    $("#edit_components_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id = $('.id').val();
        $("#edit_components_btn").text('Add');
        $.ajax({
            url: "{{ url('components') }}" + '/' + id,
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                swal_message(data.message, 'success');
                getComponts();
                claer_error()
                $("#edit_components_btn").text('Update');
                $("#edit_components_form")[0].reset();
                $("#EditComponentsModal").modal('hide');
            },
            error: function(data, status, xhr) {
                if (data.status == 400) {
                    swal_message(data.message, 'error');
                }
                if (data.status == 422) {
                    claer_error();
                    $('#error_components_name').text(data.responseJSON.data.comp_name[0]);
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
                    url: "{{ url('components') }}" + '/' + id,
                    type: "POST",
                    data: {
                        '_method': 'DELETE',
                        '_token': csrf_token
                    },
                    success: function(data) {
                        getComponts();
                        swal_message(data.message, 'success');
                    },
                    error: function() {
                        swal_message(data.message, 'error');
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
    function getComponts() {
        let data_targer = "{{user_privileges_check('master','Components','alter_role')}}" == 1 ? (
            "data-target='#EditComponentsModal'") : "";

        $.ajax({
            url: "{{ url('components_view')}}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                html +='<table  id="tableId"  style=" border-collapse: collapse; "   class="table table-striped customers " >';
                    html += '<thead >';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Article Name</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" >Notes</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="alias d-none d-print-none">Alias</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                        html += '</tr>';
                    html += '</thead>';
                html += '<tbody id="myTable" class="qw">';
                $.each(response.data, function(key, v) {
                    html += '<tr id="' + v.comp_id +'" class="left left-data editIcon table-row"  data-toggle="modal" ' + data_targer + '>';
                        html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;">' + (key + 1) + '</td>';
                        html +='<td  style="width: 3%;  border: 1px solid #ddd;"><input type="hidden" class="form-control get_group_id" name="get_group_id" value="' +   v.comp_id + '" ">' + v.comp_name + '</td>';
                        html +='<td class="nature_val"  style="width: 3%;  border: 1px solid #ddd;">' + v.notes + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="alias d-none d-print-none">' + (v.comp_alias ? v.comp_alias : '') + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">' + (v.user_name?v.user_name:'') + '</td>';
                        html += '<td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">' + (v.other_details ? JSON.parse(v.other_details) : '') + '</i></div></td>';
                    html += "</tr> ";
                });
                html += '</tbody>';
                    html += '<tfoot>';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Article Name</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" >Notes</th>';
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
                get_hover();
            }
        })

    }
    getComponts();
});

//godown edit modal show
function tableTrModal() {
    edit_components($('.currentNav').closest('tr').attr('id'));
    $('#EditComponentsModal').modal('show');

}
//group edit function
function edit_components(id) {
    $.ajax({
        url: "{{ url('components') }}" + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(response) {
            $(".id").val(response.data.comp_id);
            $(".comp_name").val(response.data.comp_name);
            $(".unit_or_branch").val(response.data.unit_or_branch).trigger('change');;
            $('.comp_alias').val(response.data.comp_alias);
            $(".notes").val(response.data.notes);
        }
    });
}
//data validation data clear
function claer_error() {
    $('#error_components_name').text('');
}

function swal_message(data, message) {
    swal({
        title: 'Successfully',
        text: data,
        type: message,
        timer: '1500'
    });
}
</script>
@endpush
@endsection
