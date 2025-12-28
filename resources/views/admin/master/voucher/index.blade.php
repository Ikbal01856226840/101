@extends('layouts.backend.app')
@section('title','Voucher')
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
    'page_title'=> 'voucher',
    'page_unique_id'=>3,
    'title'=>'Accounts Voucher',
    'redirect_page_true'=>'redirect_page_true',
    'sort_by'=>'sort_by',
])
@endcomponent
<!-- add component-->
@component('components.index', [
    'title' => 'Accounts Voucher [View]',
    'add_route'=>route('voucher.create'),
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'print_layout'=>'landscape',
    'print_header'=>'Accounts Voucher',
    'setting_model'=>'setting_model',
    'close_route'=>route('master-dashboard'),
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Voucher Type',
    'user_privilege_type'=>'create_role'
])
@slot('body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead">
</div>
@endslot
@endcomponent
<br>
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>

<script>
$(function() {

    //get  all data show
    function getVoucher() {
        $.ajax({
            url: "{{ url('voucher_view')}}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                let data_targer = " ";
                html +=
                    '<table id="tableId"  style=" border-collapse: collapse; "   class="table table-striped customers " >';
                    html += '<thead >';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Voucher Name</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Voucher Type</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                        html += '</tr>';
                    html += '</thead>';
                html += '<tbody class="qw" id="myTable">';
                $.each(response.data, function(key, v) {
                    html += " <tr id='" + v.voucher_id + "' class='left left-data table-row' >";
                        html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;">' +(key+1) +'</td>';
                        html +='<td  style="width: 3%;  border: 1px solid #ddd;"><input type="hidden" class="form-control get_group_id" name="get_group_id" value="' +v.voucher_type_id + '" ">' + v.voucher_name + '</td>';
                        if (v.voucher_type == 0 || v.voucher_type == null) {
                            html +='<td class="nature_val"  style="width: 3%;  border: 1px solid #ddd;"></td>';

                        } else {
                            html +='<td class="nature_val"  style="width: 3%;  border: 1px solid #ddd;">'+ v.voucher_type.voucher_type + '</td>';
                        }
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">' + (v.user_name?v.user_name:'') + '</td>';
                        html += '<td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">' + (v.other_details ? JSON.parse(v.other_details) : '') + '</i></div></td>';
                    html += "</tr> ";
                });
                html += '</tbody>';
                    html += '<tfoot>';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Voucher Name</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Voucher Type</th>';
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
                 set_scroll_table();
                // if ($(".sort_by_asc").prop('checked') === true) {
                //     page_wise_setting_table_row_sort_by($(".sort_by_asc").val());
                // } else if ($(".sort_by_desc").prop('checked') === true) {
                //     page_wise_setting_table_row_sort_by($(".sort_by_desc").val());
                // }
                get_hover();
            }
        })

    }
    getVoucher();
});

$(document).ready(function() {
    $('.sd').on('click', '.customers tbody tr', function() {
        @if(user_privileges_check('master', 'Voucher Type', 'alter_role'))
        window.location = "{{ url('voucher') }}" + '/' + $(this).attr('id');
        @endif
    })
});
</script>
@endpush
@endsection
