@extends('layouts.backend.app')
@section('title','Supplier')
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
    'page_title'=>'Supplier',
    'page_unique_id'=>8,
    'title'=>'Supplier',
    'insert_settings'=>'insert_settings',
    'view_settings'=>'view_settings'
])
@endcomponent
<!-- add component-->
@component('components.index', [
    'title' => 'Supplier',
    'close' => 'Close',
    'print' => 'Print',
    'add_modal_data'=>'#AddSupplierModal',
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'setting_model'=>'setting_model',
    'print_layout'=>'landscape',
    'print_header'=>'Stock Group',
    'close_route'=>route('master-dashboard'),
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Supplier',
    'user_privilege_type'=>'create_role'
])
@slot('body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead">
</div>
@endslot
@endcomponent
<!-- add and edit form include -->
@include('admin.master.supplier.form');
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>

<script>
//show select2
$(document).ready(function() {
    $(".js-example-basic-single").select2({
        dropdownParent: $("#AddSupplierModal")
    });
    $(".js-example-basic").select2({
        dropdownParent: $("#EditSupplierModal")
    });
});
$(function() {
    // add new supplier ajax request
    $("#add_supplier_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#add_supplier_btn").text('Save');
        $.ajax({
            url: '{{ route("supplier.store") }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                claer_error();
                swal_message(data.message, 'success', 'Successfullly');
                getSupplier()
                $("#add_supplier_btn").text('Save Supplier');
                $("#add_supplier_form")[0].reset();
                $("#AddSupplierModal").modal('hide');
                $(".ledger_id").val(0).trigger('change');
            },
            error: function(data, status, xhr) {
                claer_error();
                if (data.status == 404) {
                    swal_message(data.responseJSON.message, 'error', 'Error');
                    $("#AddSupplierModal").modal('hide');

                }
                if (data.status == 422) {
                    claer_error();
                    $('#error_supplier_name').text(data.responseJSON.data.supplier_name[0]);

                }

            }
        });
    });
    // edit supplier ajax request
    $(document).on('click', '.editIcon', function(e) {
        e.preventDefault();
        let id = $(this).attr('id');
        edit_supplier(id);
    });
    // update supplier ajax request
    $("#edit_supplier_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id = $('.id').val();
        $("#edit_supplier_btn").text('Update');
        $.ajax({
            url: "{{ url('supplier') }}" + '/' + id,
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                swal_message(data.message, 'success', 'Successfullly');
                getSupplier();
                claer_error()
                $("#edit_supplier_btn").text('Edit Supplier');
                $("#edit_supplier_form")[0].reset();
                $("#EditSupplierModal").modal('hide');
                $(".ledger_id").val(0).trigger('change');
            },
            error: function(data, status, xhr) {
                if (data.status == 404) {
                    swal_message(data.responseJSON.message, 'error', 'Error');
                    $("#EditSupplierModal").modal('hide');
                    // swal_message(data.message,'error','Error');
                }
                if (data.status == 422) {
                    claer_error();
                    $('#update_error_supplier_name').text(data.responseJSON.data
                        .supplier_name[0]);

                }

            }
        });
    });
    // delete supplier ajax request
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
                    url: "{{ url('supplier') }}" + '/' + id,
                    type: "POST",
                    data: {
                        '_method': 'DELETE',
                        '_token': csrf_token
                    },
                    success: function(data) {
                        getSupplier();
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
    function getSupplier() {
        let data_targer = "{{user_privileges_check('master','Supplier','alter_role')}}" == 1 ?
            "data-target='#EditSupplierModal'" : "";
        $.ajax({
            url: "{{ url('supplier_view')}}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                html += '<table  id="tableId"  style=" border-collapse: collapse; "   class="table table-striped customers " >';
                    html += '<thead >';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Supplier Name</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Phone</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">District</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Proprietor</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                        html += '</tr>';
                    html += '</thead>';
                html += '<tbody id="myTable" class="qw">';
                $.each(response.data, function(key, v) {
                    html += '<tr id="' + v.id +'" class="left left-data editIcon table-row"  data-toggle="modal" '+data_targer+' >';
                        html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;">' +(key + 1) + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;">' + v .supplier_name + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;">' +(v.phone1?v.phone1:'') +'</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;">' + (v.district?v.district:'') + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;">' + (v.proprietor?v.proprietor:'') + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">' + (v.user_name?v.user_name:'') + '</td>';
                        html += '<td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">' + (v.other_details ? JSON.parse(v.other_details) : '') + '</i></div></td>';
                    html += "</tr> ";
                });
                html += '</tbody>';
                    html += '<tfoot>';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Supplier Name</th>';
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
    getSupplier();
});

//supplier edit modal show
function tableTrModal() {
    edit_supplier($('.currentNav').closest('tr').attr('id'));
    $('#EditSupplierModal').modal('show');

}
//supplier edit function
function edit_supplier(id) {
    $.ajax({
        url: "{{ url('supplier') }}" + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(response) {

            $(".id").val(response.data.id);
            $('.ledger_id').val(response.data.ledger_id).trigger('change');
            $(".supplier_name").val(response.data.supplier_name);
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
