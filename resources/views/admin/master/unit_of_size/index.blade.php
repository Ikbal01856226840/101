@extends('layouts.backend.app')
@section('title','Size')
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
    'page_title'=>'stock_size',
    'page_unique_id'=>17,
    'title'=>'Stock Size',
    'insert_settings'=>'insert_settings',
    'view_settings'=>'view_settings'
])
@endcomponent
<!-- add component-->
@component('components.index', [
    'title' => 'Unit Of Size',
    'close' => 'Close',
    'print' => 'Print',
    'add_modal_data'=>'#AddSizeModal',
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'setting_model'=>'setting_model',
    'print_layout'=>'landscape',
    'print_header'=>'Stock Size',
    'close_route'=>route('master-dashboard'),
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Unit of Size',
    'user_privilege_type'=>'create_role'
])
@slot('body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead">
</div>
@endslot
@endcomponent
<!-- add and edit form include -->
@include('admin.master.unit_of_size.form');
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>
$(function() {
    // add new Size ajax request
    $("#add_size_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#add_size_btn").text('Save');
        $.ajax({
            url: '{{ route("size.store") }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                claer_error();
                swal_message(data.message, 'success');
                getSize()
                $("#add_size_btn").text('Add Size');
                $("#add_size_form")[0].reset();
                $("#AddSizeModal").modal('hide');
            },
            error: function(data, status, xhr) {
                claer_error();
                if (data.status == 404) {
                    swal_message(data.message, 'error');
                }
                if (data.status == 422) {
                    claer_error();
                    $('#error_symbol_name').text(data.responseJSON.data.symbol[0]);
                }

            }
        });
    });
    // edit size ajax request
    $(document).on('click', '.editIcon', function(e) {
        e.preventDefault();
        let id = $(this).attr('id');
        edit_size(id);
    });
    // update size ajax request
    $("#edit_size_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id = $('.id').val();
        $("#edit_size_btn").text('Update');
        $.ajax({
            url: "{{ url('size') }}" + '/' + id,
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                claer_error()
                swal_message(data.message, 'success');
                getSize();
                claer_error()
                $("#edit_size_btn").text('Edit Size');
                $("#edit_size_form")[0].reset();
                $("#EditSizeModal").modal('hide');
            },
            error: function(data, status, xhr) {

                if (data.status == 404) {
                    swal_message(data.message, 'error');
                }
                if (data.status == 422) {
                    claer_error();
                    $('#error_update_symbol_name').text(data.responseJSON.data.symbol[0]);
                }

            }
        });
    });
    // delete size ajax request
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
                    url: "{{ url('size') }}" + '/' + id,
                    type: "POST",
                    data: {
                        '_method': 'DELETE',
                        '_token': csrf_token
                    },
                    success: function(data) {
                        getSize();
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
    function getSize() {
        let data_targer = "{{user_privileges_check('master','Unit of Size','alter_role')}}" == 1 ?
            "data-target='#EditSizeModal'" : "";
        $.ajax({
            url: "{{ url('size_view')}}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                html += '<table  id="tableId"  style=" border-collapse: collapse; "   class="table table-striped customers " >';
                    html += '<thead >';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Symbol</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Formal Name</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                        html += '</tr>';
                    html += '</thead>';
                html += '<tbody class="qw" id="myTable">';
                $.each(response.data, function(key, v) {
                    html += '<tr id="' +v.unit_of_size_id+'" class="left left-data editIcon table-row"  data-toggle="modal" '+ data_targer +' >';
                        html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;">'+(key + 1)+'</td>';
                        html +='<td  style="width: 3%;  border: 1px solid #ddd;"><input type="hidden" class="form-control unit_of_size_id" name="unit_of_size_id" value="' +v.unit_of_size_id + '" ">' + v.symbol + '</td>';
                        html +='<td class="nature_val"  style="width: 3%;  border: 1px solid #ddd;">' + (v.formal_name?v.formal_name:'') + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">' + (v.user_name?v.user_name:'') + '</td>';
                        html += '<td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">' + (v.other_details ? JSON.parse(v.other_details) : '') + '</i></div></td>';
                    html += "</tr> ";
                });
                html += '</tbody>';
                    html += '<tfoot>';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Symbol</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Formal Name</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                        html += '</tr>';
                    html += '</tfoot>';
                html += '</table>';
                html += `<div class="col-sm-12 text-center d-print-none" >
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
    getSize();
});

//size edit function
function edit_size(id) {
    $.ajax({
        url: "{{ url('size') }}" + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(response) {
            $(".id").val(response.data.unit_of_size_id);
            $(".symbol").val(response.data.symbol);
            $('.formal_name').val(response.data.formal_name);

        }
    });
}
//data validation data clear
function claer_error() {
    $('#error_update_symbol_name').text('');
    $('#error_update_formal_name').text('');
    $('#error_symbol_name').text('');
    $('#error_formal_name').text('');
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
