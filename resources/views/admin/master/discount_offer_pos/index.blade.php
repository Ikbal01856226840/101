@extends('layouts.backend.app')
@section('title','Discount Offer POS')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
<style>
    .td{
        width: 3%;  border: 1px solid #ddd;
    }
</style>
@endpush
@section('admin_content')
<br>
<!-- setting component-->
@component('components.setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=> 'discount_offer_POS',
    'page_unique_id'=>5,
    'title'=>'Discount Offer POS',
    'alias_true'=>'alias_true',
    'insert_settings'=>'insert_settings',
    'view_settings'=>'view_settings'
])
@endcomponent
<!-- Discount Offer POS -->
@component('components.index', [
    'title' => 'Discount Offer POS',
    'close' => 'Close',
    'print' => 'Print',
    'add_modal_data'=>'#AddDiscountOfferModel',
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'print_layout'=>'landscape',
    'print_header'=>'Discount Offer POS',
    'setting_model'=>'setting_model',
    'close_route'=>route('master-dashboard'),
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Distribution Center',
    'user_privilege_type'=>'create_role'

])
@slot('body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers ">
        <thead>
            <tr>
                <th class="td">SL</th>
                <th class="td">Group Name</th>
                <th class="td">Actual Price</th>
                <th class="td">Offer Price</th>
                <th class="td">Discount %</th>
                <th class="td">Offer Start</th>
                <th class="td">Offer End</th>
                <th class="td">Shop Name</th>
            </tr>
        </thead>
        <tbody id="myTable" class="discount_body">
        </tbody>
        <tfoot>
            <tr>
                <th class="td">SL</th>
                <th class="td">Group Name</th>
                <th class="td">Actual Price</th>
                <th class="td">Offer Price</th>
                <th class="td">Discount %</th>
                <th class="td">Offer Start</th>
                <th class="td">Offer End</th>
                <th class="td">Shop Name</th>
            </tr>
        </tfoot>
    </table>
    <div class="col-sm-12 text-center hide-btn">
        <span><b>Copyright &copy; 2014-2022 <a href="http://www.hamko-ict.com/">Hamko-ICT.</a> All rights
                reserved.</b></span>
    </div>
</div>
@endslot
@endcomponent
<!-- add and edit form include -->
@include('admin.master.discount_offer_pos.form');
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>

$(document).ready(function() {
    //show select2
    $(".js-example-basic-single").select2({
        dropdownParent: $("#AddDiscountOfferModel")
    });
    $(".js-example-basic").select2({
        dropdownParent: $("#EditDiscountOfferModel")
    });
});

$(function() {
    // add new discount offer pos ajax request
    $("#add_discount_offer_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#dd_discount_offer_btn").text('Add');
        $.ajax({
            url: '{{ route("discount-offer-pos.store") }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                swal_message(data.message, 'success');
                getdDscountOfferPOS();
                $("#dd_discount_offer_btn").text('Add ');
                $("#add_discount_offer_form")[0].reset();
                $("#add_discount_offer_form").get(0).reset();
                $("#AddDiscountOfferModel").modal('hide');
                setTimeout(function(){
                      location.reload();
                    },200);

            },
            error: function(data, status, xhr) {
                if (data.status == 400) {
                    swal_message(data.message, 'error');
                }
            }
        });
    });
    // edit discount offer pos ajax request
    $(document).on('click', '.editIcon', function(e) {
        e.preventDefault();
        let id = $(this).attr('id');
        edit_discount_offer_pos(id);
    });
    // update discount offer pos ajax request
    $("#edit_discount_offer_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id = $('.id').val();
        $("#dit_discount_offer_btn").text('Update');
        $.ajax({
            url: "{{ url('discount-offer-pos') }}" + '/' + id,
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                swal_message(data.message, 'success');
                getdDscountOfferPOS();
                $("#edit_discount_offer_form").text('Update');
                $("#edit_discount_offer_form")[0].reset();
                $("#EditDiscountOfferModel").modal('hide');
                setTimeout(function(){
                      location.reload();
                    },200);
            },
            error: function(data, status, xhr) {
                if (data.status == 400) {
                    swal_message(data.message, 'error');
                }

            }
        });
    });
    // delete discount offer pos ajax request
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
                    url: "{{ url('discount-offer-pos') }}" + '/' + id,
                    type: "POST",
                    data: {
                        '_method': 'DELETE',
                        '_token': csrf_token
                    },
                    success: function(data) {
                       getdDscountOfferPOS();
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
    function getdDscountOfferPOS() {
        let data_targer = "{{user_privileges_check('master','Distribution Center','alter_role')}}" == 1 ?
            "data-target='#EditDiscountOfferModel'" : "";
        $.ajax({
            url: "{{ url('discount-offer-pos-view')}}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                    let html = [];
                    $.each(response.data, function(key, v) {
                            html.push( `<tr id=${v.offer_id} class="left left-data  table-row editIcon "  data-toggle="modal" ${data_targer}>
                                            <td class="sl td">${(key +  1)}</td>
                                            <td  class="td">${v.stock_group_name}</td>
                                            <td  class="td" >${v.price}</td>
                                            <td class="td">${v.price}</td>
                                            <td class="n td">${(Math.abs(v.discount)||'')}</td>
                                            <td class="td">${(v.date_from|| '')}</td>
                                            <td class="td">${(v.date_to||'')}</td>
                                            <td class="td">${(v.dis_cen_name||'')}</td>
                                </tr>`);
                    });

                    $('.discount_body').html(html.join(""));
                    set_scroll_table();
                    page_wise_setting_checkbox();
                    get_hover();
                }
         });

    }
   getdDscountOfferPOS();
});

//distribution edit function
function edit_discount_offer_pos(id) {
    $.ajax({
        url: "{{ url('discount-offer-pos') }}" + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(response) {
             console.log(response.data);
            $(".id").val(response.data.offer_id);
            $(".dis_cen_id").val(response.data.dis_cen_id).trigger('change');
            $(".stock_group_id").val(response.data.stock_group_id).trigger('change');
            $(".unit_or_branch").val(response.data.unit_or_branch).trigger('change');
            $(".new_price").val(response.data.price);
            $(".discount").val(response.data.discount);
            $('.date_from').val(response.data.date_from);
            $(".date_to").val(response.data.date_to);
            $(".approved_by").val(response.data.approved_by);
            $(".remarks").val(response.data.remarks);
        }
    });
}

// calculate discount offer
$(document).on('click keyup', '.current_price,.discount', function(e) {
    let current_price=$(this).closest('.row').find('.current_price').val()||0;
    let discount=$(this).closest('.row').find('.discount').val()||0;
  $('.new_price').val(current_price-(parseFloat(current_price*discount)/100));
});
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
