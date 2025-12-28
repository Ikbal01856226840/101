@extends('layouts.backend.app')
@section('title','Distribution')
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
    'page_title'=> 'distribution_center',
    'page_unique_id'=>5,
    'title'=>'Distribution Center',
    'alias_true'=>'alias_true',
    'insert_settings'=>'insert_settings',
    'view_settings'=>'view_settings'
])
@endcomponent
<!-- Distribution Center -->
@component('components.index', [
    'title' => 'Distribution Center',
    'close' => 'Close',
    'print' => 'Print',
    'add_modal_data'=>'#AddDistributionModel',
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'print_layout'=>'landscape',
    'print_header'=>'Distribution Center',
    'setting_model'=>'setting_model',
    'close_route'=>route('master-dashboard'),
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Distribution Center',
    'user_privilege_type'=>'create_role'

])
@slot('body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead">
</div>
@endslot
@endcomponent
<!-- add and edit form include -->
@include('admin.master.distribution_center.form');
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>
$(document).ready(function() {
    //show select2
    $(".js-example-basic-single").select2({
        dropdownParent: $("#AddDistributionModel")
    });
    $(".js-example-basic").select2({
        dropdownParent: $("#EditDistributionModel")
    });
});

$(function() {
    // add new distribution ajax request
    $("#add_distribution_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#add_distribution_btn").text('Add');
        $.ajax({
            url: '{{ route("distribution.store") }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                claer_error();
                swal_message(data.message, 'success');
                getDistributionCenter();
                $("#add_distribution_btn").text('Add ');
                $("#add_distribution_form")[0].reset();
                $("#add_distribution_form").get(0).reset();
                $("#AddDistributionModel").modal('hide');

            },
            error: function(data, status, xhr) {
                claer_error();
                if (data.status == 400) {
                    swal_message(data.message, 'error');
                }
                if (data.status == 422) {
                    claer_error();
                    $('#error_distribution_name').text(data.responseJSON.data.dis_cen_name[
                        0]);
                }

            }
        });
    });
    // edit distribution ajax request
    $(document).on('click', '.editIcon', function(e) {
        e.preventDefault();
        let id = $(this).attr('id');
        edit_distribution(id);
    });
    // update distribution ajax request
    $("#edit_distribution_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id = $('.id').val();
        $("#edit_distribution_btn").text('Adding...');
        $.ajax({
            url: "{{ url('distribution') }}" + '/' + id,
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                claer_error()
                swal_message(data.message, 'success');
                getDistributionCenter();
                claer_error()
                $("#edit_distribution_btn").text('Update');
                $("#edit_distribution_form")[0].reset();
                $("#EditDistributionModel").modal('hide');
            },
            error: function(data, status, xhr) {
                if (data.status == 400) {
                    swal_message(data.message, 'error');
                }
                if (data.status == 422) {
                    claer_error();
                    $('#error_distribution_name').text(data.responseJSON.data.dis_cen_name[0]);

                }

            }
        });
    });
    // delete distribution ajax request
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
                    url: "{{ url('distribution') }}" + '/' + id,
                    type: "POST",
                    data: {
                        '_method': 'DELETE',
                        '_token': csrf_token
                    },
                    success: function(data) {
                        getDistributionCenter();
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
    function getDistributionCenter() {
        let data_targer = "{{user_privileges_check('master','Distribution Center','alter_role')}}" == 1 ?
            "data-target='#EditDistributionModel'" : "";
        $.ajax({
            url: "{{ url('distribution_view')}}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                html += '<table  id="tableId"  style=" border-collapse: collapse;"   class="table table-striped customers " >';
                    html += '<thead >';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Distribution Center</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" >Discount</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="alias d-none d-print-none">Alias</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                        html += '</tr>';
                    html += '</thead>';
                html += '<tbody class="qw" id="myTable">';
                $.each(response.data, function(key, v) {
                    html += '<tr id="' + v.dis_cen_id + '" class="left left-data editIcon table-row"  data-toggle="modal" ' +data_targer + ' >';
                        html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;">' +(key + 1) + '</td>';
                        html +='<td  style="width: 3%;  border: 1px solid #ddd;"><input type="hidden" class="form-control get_group_id" name="get_group_id" value="' + v.dis_cen_id + '" ">' + v.dis_cen_name + '</td>';
                        html += '<td class="nature_val"  style="width: 3%;  border: 1px solid #ddd;">'+ (v.discount||'') + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="alias d-none d-print-none">' + (v.alias ? v.alias : '') + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">' + (v.user_name?v.user_name:'') + '</td>';
                        html += '<td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">' + (v.other_details ? JSON.parse(v.other_details) : '') + '</i></div></td>';

                    html += "</tr> ";
                });
                html += '</tbody>';
                    html += '<tfoot>';
                        html += '<tr>';
                                    html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                                    html += '<th style="width: 3%;  border: 1px solid #ddd;">Distribution Center</th>';
                                    html += '<th style="width: 3%;  border: 1px solid #ddd;" >Discount</th>';
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
    getDistributionCenter();
});

//distribution edit function
function edit_distribution(id) {
    $.ajax({
        url: "{{ url('distribution') }}" + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(response) {
            $(".id").val(response.data.dis_cen_id);
            $(".dis_cen_name").val(response.data.dis_cen_name);
            if (response.data.dis_cen_under == 1) {
                $(".dis_cen_under").val(0).trigger('change');
            } else {
                $(".dis_cen_under").val(response.data.dis_cen_under).trigger('change');
            }
            $(".unit_or_branch").val(response.data.unit_or_branch).trigger('change');

            $(".discount").val(response.data.discount);
            $('.alias_edit').val(response.data.alias);
            $(".address").val(response.data.address);
            $('.date_start').val(response.data.date_start);
            $(".date_end").val(response.data.date_end);
        }
    });
}
//data validation data clear
function claer_error() {
    $('#error_distribution_name').text('');
}

function swal_message(data, message) {
    swal({
        title: 'Succussfully',
        text: data,
        type: message,
        timer: '1500'
    });
}
</script>
@endpush
@endsection
