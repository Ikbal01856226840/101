@extends('layouts.backend.app')
@section('title','Measure')
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
    'page_title'=>'stock_measure',
    'page_unique_id'=>18,
    'title'=>'Stock Measure',
    'insert_settings'=>'insert_settings',
    'view_settings'=>'view_settings'
])
@endcomponent
<!-- add component-->
@component('components.index', [
    'title' => 'Unit Of Measure',
    'close' => 'Close',
    'print' => 'Print',
    'add_modal_data'=>'#AddMeasureModal',
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'setting_model'=>'setting_model',
    'print_layout'=>'landscape',
    'print_header'=>'Stock Measure',
    'close_route'=>route('master-dashboard'),
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Units of Measure',
    'user_privilege_type'=>'create_role'
])
@slot('body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead">
</div>
@endslot
@endcomponent
<!-- add and edit form include -->
@include('admin.master.unit_of_measure.form');
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>

<script>
$(function() {
    // add new measure ajax request
    $("#add_measure_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#add_measure_btn").text('Add');
        $.ajax({
            url: '{{ route("measure.store") }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                claer_error();
                swal_message(data.message, 'success');
                getMeasure()
                $("#add_measure_btn").text('Add');
                $("#add_measure_form")[0].reset();
                $("#AddMeasureModal").modal('hide');
            },
            error: function(data, status, xhr) {
                claer_error();
                if (data.status == 404) {
                    swal_message(data.message, 'error');
                }
                if (data.status == 422) {
                    claer_error();
                    $('#error_symbol_name').text(data.responseJSON.data.symbol?data.responseJSON.data.symbol[0]:'');
                }

            }
        });
    });
    // edit measure ajax request
    $(document).on('click', '.editIcon', function(e) {
        e.preventDefault();
        let id = $(this).attr('id');
        edit_size(id);
    });
    // update measure ajax request
    $("#edit_measure_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id = $('.id').val();
        $("#edit_measure_btn").text('Update');
        $.ajax({
            url: "{{ url('measure') }}" + '/' + id,
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                claer_error()
                swal_message(data.message, 'success');
                getMeasure();
                claer_error()
                $("#edit_measure_btn").text('Update');
                $("#edit_measure_form")[0].reset();
                $("#EditMeasureModal").modal('hide');
            },
            error: function(data, status, xhr) {

                if (data.status == 404) {
                    swal_message(data.responseJSON.message, 'error');
                }
                if (data.status == 422) {
                    claer_error();
                    $('#error_update_symbol_name').text(data.responseJSON.data.symbol?data.responseJSON.data.symbol[0]:'');
                }

            }
        });
    });
    // delete measure ajax request
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
                    url: "{{ url('measure') }}" + '/' + id,
                    type: "POST",
                    data: {
                        '_method': 'DELETE',
                        '_token': csrf_token
                    },
                    success: function(data) {
                        getMeasure();
                        swal_message(data.message, 'success');
                    },
                    error: function() {
                        swal_message(data.responseJSON.message, 'error');
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
    function getMeasure() {
        let data_targer = "{{user_privileges_check('master','Units of Measure','alter_role')}}" == 1 ?
            "data-target='#EditMeasureModal'" : "";
        $.ajax({
            url: "{{ url('measure_view')}}",
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
                html += '<tbody id="myTable" class="qw">';
                $.each(response.data, function(key, v) {
                    html += '<tr id="' + v.unit_of_measure_id +'" class="left left-data editIcon table-row"  data-toggle="modal"  ' + data_targer + '>';
                        html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;">' +(key + 1) +'</td>';
                        html +='<td  style="width: 3%;  border: 1px solid #ddd;"><input type="hidden" class="form-control unit_of_size_id" name="unit_of_measure_id" value="' + v.unit_of_measure_id + '" ">' + v.symbol + '</td>';
                        html += '<td class="nature_val"  style="width: 3%;  border: 1px solid #ddd;">' + (v.formal_name?v.formal_name:'') + '</td>';
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
    getMeasure();
});

//measure edit function
function edit_size(id) {
    $.ajax({
        url: "{{ url('measure') }}" + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(response) {
            $(".id").val(response.data.unit_of_measure_id);
            $(".symbol").val(response.data.symbol);
            $('.formal_name').val(response.data.formal_name);

        }
    });
}
//data validation data clear
function claer_error() {
    $('#error_update_symbol_name').text('');
    $('#error_symbol_name').text('');
    $('#error_formal_name').text('');
}
$('.close').on('click',function(){
        $('.model_rest')[0].reset();
        $('#error_update_symbol_name').text('');
        $('#error_symbol_name').text('');

    })
$('.model_rest_btn').on('click',function(){
    $('.model_rest')[0].reset();
    $('#error_update_symbol_name').text('');
    $('#error_symbol_name').text('');
})
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
