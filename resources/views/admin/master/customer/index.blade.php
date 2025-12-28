@extends('layouts.backend.app')
@section('title','Customer')
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
    'page_title'=> 'Customer',
    'page_unique_id'=>7,
    'title'=>'Customer',
    'insert_settings'=>'insert_settings',
    'view_settings'=>'view_settings'
])
@endcomponent
<!-- add component-->
@component('components.index', [
    'close_route'=>route('master-dashboard'),
    'title' => 'Customer',
    'print_layout'=>'landscape',
    'print_header'=>'Customer',
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'setting_model'=>'setting_model',
    'add_modal_data'=>'#AddCustomerModal',
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Customer',
    'user_privilege_type'=>'create_role'
])
@slot('body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead">
</div>
@endslot
@endcomponent
<!-- add and edit form include -->
@include('admin.master.customer.form');
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>
//show select2
$(document).ready(function() {
    $(".js-example-basic-single").select2({
        dropdownParent: $("#AddCustomerModal")
    });
    $(".js-example-basic").select2({
        dropdownParent: $("#EditCustomerModal")
    });
});
$(function() {
    // add new customer ajax request
    $("#add_customer_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#add_measure_btn").text('Save');
        $.ajax({
            url: '{{ route("customer.store") }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                claer_error();
                swal_message(data.message, 'success', 'Successfullly');
                getCustomer()
                $("#add_customer_btn").text('Save Customer');
                $("#add_customer_form")[0].reset();
                $("#AddCustomerModal").modal('hide');
                $(".ledger_id").val(0).trigger('change');
            },
            error: function(data, status, xhr) {
                claer_error();
                if (data.status == 404) {
                    swal_message(data.responseJSON.message, 'error', 'Error');
                    $("#AddCustomerModal").modal('hide');

                }
                if (data.status == 422) {
                    claer_error();
                    $('#error_customer_name').text(data.responseJSON.data.customer_name[0]);

                }

            }
        });
    });
    // edit customer ajax request
    $(document).on('click', '.editIcon', function(e) {
        e.preventDefault();
        let id = $(this).attr('id');
        edit_customer(id);
    });
    // update customer ajax request
    $("#edit_customer_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id = $('.id').val();
        $("#edit_customer_btn").text('Update');
        $.ajax({
            url: "{{ url('customer') }}" + '/' + id,
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                claer_error()
                swal_message(data.message, 'success', 'Successfullly');
                getCustomer();
                claer_error()
                $("#edit_customer_btn").text('Edit Customer');
                $("#edit_customer_form")[0].reset();
                $("#EditCustomerModal").modal('hide');
                $(".ledger_id").val(0).trigger('change');
            },
            error: function(data, status, xhr) {

                if (data.status == 404) {
                    swal_message(data.responseJSON.message, 'error', 'Error');
                    $("#EditCustomerModal").modal('hide');
                    // swal_message(data.message,'error','Error');
                }
                if (data.status == 422) {
                    claer_error();
                    $('#update_error_customer_name').text(data.responseJSON.data
                        .customer_name[0]);

                }

            }
        });
    });
    // delete customer ajax request
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
                    url: "{{ url('customer') }}" + '/' + id,
                    type: "POST",
                    data: {
                        '_method': 'DELETE',
                        '_token': csrf_token
                    },
                    success: function(data) {
                        getCustomer();
                        swal_message(data.message, 'success', 'Successfully');
                    },
                    error: function() {
                        swal_message(data.message, 'error', 'Error');
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
    function getCustomer() {
        let data_targer = "{{user_privileges_check('master','Customer','alter_role')}}" == 1 ?
            "data-target='#EditCustomerModal'" : "";
        $.ajax({
            url: "{{ url('customer_view')}}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                html += '<table  id="tableId"  style=" border-collapse: collapse; "   class="table table-striped customers " >';
                    html += '<thead >';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Customer Name</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Phone</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">District</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Proprietor</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                        html += '</tr>';
                    html += '</thead>';
                html += '<tbody id="myTable" class="qw">';
                $.each(response.data, function(key, v) {
                    html += '<tr id="' + v.customer_id + '" class="left left-data editIcon table-row"  data-toggle="modal" ' +data_targer + ' >';
                        html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;">' +(key + 1) + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;">'+v.customer_name + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;">'+(v.phone1?v.phone1:'')+'</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;">'+(v.district?v.district:'') +'</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;">' +(v .proprietor?v .proprietor:'') + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">' + (v.user_name?v.user_name:'') + '</td>';
                        html += '<td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">' + (v.other_details ? JSON.parse(v.other_details) : '') + '</i></div></td>';
                    html += "</tr> ";
                });
                html += '</tbody>';
                    html += '<tfoot>';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Customer Name</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Phone</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">District</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Proprietor</th>';
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
    getCustomer();
});

//godown edit modal show
function tableTrModal() {
    edit_measure($('.currentNav').closest('tr').attr('id'));
    $('#EditCustomerModal').modal('show');

}
//group edit function
function edit_customer(id) {
    $.ajax({
        url: "{{ url('customer') }}" + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(response) {
            $(".id").val(response.data.customer_id);
            $('.ledger_id').val(response.data.ledger_id).trigger('change');
            $(".customer_name").val(response.data.customer_name);
            $('.phone1').val(response.data.phone1);
            $(".district").val(response.data.district);
            $('.proprietor').val(response.data.proprietor);
        }
    });
}
//data validation data clear
function claer_error() {
    $('#update_error_customer_name').text('');
    $('#error_customer_name').text('');

}

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
