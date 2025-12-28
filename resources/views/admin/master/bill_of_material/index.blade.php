@extends('layouts.backend.app')
@section('title','Bill Of Material')
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
    'page_title'=> 'Bill_of_material',
    'page_unique_id'=>9,
    'title'=>'Bill of Material',
    'insert_settings'=>'insert_settings',
    'view_settings'=>'view_settings'
])
@endcomponent
<!-- add component-->
@component('components.index', [
    'title' => 'Bill of Material [View]',
    'print' => 'Print',
    'add_route'=>route('bill-of-material.create'),
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'setting_model'=>'setting_model',
    'print_layout'=>'landscape',
    'print_header'=>'Bill Of Material',
    'close_route'=>route('master-dashboard'),
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Material',
    'user_privilege_type'=>'create_role'
])
@slot('body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead">
</div>
@endslot
@endcomponent
<br>
<!-- add and edit form include -->

@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>
$(function() {
    //get  all data show
    function getBillOfMaterial() {

        $.ajax({
            url: "{{ url('bill-of-material/get/data')}}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                html +='<table id="tableId"  style=" border-collapse: collapse; "   class="table table-striped customers " >';
                    html += '<thead >';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Bill of Material</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">Created By</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;" class="last_update d-none d-print-none">History</th>';
                        html += '</tr>';
                    html += '</thead>';
                html += '<tbody id="myTable" class="qw">';
                $.each(response.data, function(key, v) {
                    html += " <tr id='" + v.bom_id + "' class='left left-data table-row' >";
                        html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;">'+ (key + 1) + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;">' + v.bom_name + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;" class="created_user d-none d-print-none">' + (v.user_name?v.user_name:'') + '</td>';
                        html += '<td class=" last_update d-none d-print-none"  style="width: 3%;  border: 1px solid #ddd;"><div><i  class="history_font_size">' + (v.other_details ? JSON.parse(v.other_details) : '') + '</i></div></td>';
                    html += "</tr> ";
                });
                html += '</tbody>';
                    html += '<tfoot>';
                        html += '<tr>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html += '<th style="width: 3%;  border: 1px solid #ddd;">Bill of Material</th>';
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
    getBillOfMaterial();
});


$(document).ready(function() {
    $('.sd').on('click', '.customers tbody tr', function() {
        @if(user_privileges_check('master', 'Material', 'alter_role'))
        window.location = "{{ url('bill-of-material') }}" + '/' + $(this).attr('id');
        @endif

    })
});
</script>
@endpush
@endsection
