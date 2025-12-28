@extends('layouts.backend.app')
@section('title','Stock Item')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
<style>
    .td {
        width: 3%;
        border: 1px solid #ddd;
    }
</style>
@endpush
@section('admin_content')
<br>
<!-- setting component-->
@component('components.setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=> 'stock_item',
    'page_unique_id'=>13,
    'title'=>'Stock Item',
    'alias_true'=>'alias_true',
    'last_inset_true'=>'last_inset_true',
    'bangla_true'=>'bangla_true',
    'sort_by_group_true'=>'sort_by_group_true',
    'redirect_page_true'=>'redirect_page_true'
])
@endcomponent
<!-- add component-->
@component('components.index', [
    'title' => 'Stock Item',
    'close' => 'Close',
    'plan_view'=>'Plain View',
    'tree_view'=>'Tree View',
    'add_route'=>route('stock-item.create'),
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'print_layout'=>'landscape',
    'print_header'=>'Stock Item',
    'setting_model'=>'setting_model',
    'close_route'=>route('master-dashboard'),
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Stock Item',
    'user_privilege_type'=>'create_role'
])
@slot('body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers ">
        <thead>
            <tr>
                <th class="td">SL</th>
                <th class="td">Stock Item Name</th>
                <th class="bangla_name d-none d-print-none td">Bangla Stock Item Name</th>
                <th class="td">Unit</th>
                <th class="td">Under Stock Group</th>
                <th class="alias d-none d-print-none td">Alias</th>
                <th class="created_user d-none d-print-none td">Created By</th>
                <th class="last_update d-none d-print-none td">History</th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th class="td">SL</th>
                <th class="td">Bangla Stock Item</th>
                <th class="bangla_name d-none d-print-none td">Bangla Stock Item Name</th>
                <th class="td">Godown Type</th>
                <th class="td">Under Stock Group</th>
                <th class="alias d-none d-print-none td">Alias</th>
                <th class="created_user d-none d-print-none td">Created By</th>
                <th class="last_update d-none d-print-none td">History</th>
            </tr>
        </tfoot>
    </table>
    <div class="col-sm-12 text-center hide-btn">
        <span><b>Copyright &copy; 2014-2022 <a href="http://www.hamko-ict.com/">Hamko-ICT.</a> All rights
                reserved.</b></span>
    </div>
</div>
<br>
@endslot
@endcomponent
<br>
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>
    let i = 1;
    $(function() {
        //plain all data show
        $('.plain_id').click(function() {
            $(this).addClass('d-none');
            $('.tree_id').removeClass('d-none');
            $.ajax({
                url: "{{ url('stock-item_view/plain-view')}}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let html = [];
                    $.each(response.data, function(key, v) {
                        html.push(`<tr id="${v.stock_item_id}" class="left left-data editIcon table-row"  data-toggle="modal" data-target="#EditGodownModal" >
                                        <td  class="sl" style="width: 3%;  border: 1px solid #ddd;">${ (key + 1)}</td>
                                        <td  class="td">${v.product_name}</td>
                                        <td  class="td bangla_name d-none d-print-none">${(v.bangla_product_name||'')}</td>
                                        <td  class="td nature_val" >${v.symbol}</td>
                                        <td  class="td">${v.stock_group_name}</td>
                                        <td  class="td alias d-none d-print-none">${(v.alias||'')}</td>
                                        <td  class="td created_user d-none d-print-none">${v.user_name||''}</td>
                                        <td  class="td last_update d-none d-print-none"><div><i>${(v.other_details ? JSON.parse(v.other_details) : '')}</i></div></td>
                                   </tr> `);
                    });
                    $(".item_body").html(html.join(""));
                    set_scroll_table();
                    page_wise_setting_checkbox();
                    get_hover();
                },
            })
        });
        $('.tree_id').click(function() {
            $(this).addClass('d-none');
            $('.plain_id').removeClass('d-none');
            i = 1
            allDataTree();
        });
    });

    //all data tree show
    function allDataTree() {
        $(".modal").show();
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ url('stock-item_view/tree-view')}}",
            success: function(response) {
                $(".modal").hide();
                i = 1
                $('.item_body').html(getTreeView(response.data, depth = 0, 0));
                get_hover();
                set_scroll_table();
                page_wise_setting_checkbox();

            }
        })
    }

    // get Tree view table row
    function getTreeView(arr, depth = 0, chart_id = 0) {
        let html = [];
        arr.forEach(function(v) {
            a = '&nbsp;&nbsp;&nbsp;&nbsp;';
            h = a.repeat(depth);
            if (chart_id != v.stock_group_id) {
                html.push(`<tr style='pointer-events: none' class='left left-data editIcon table-row'>
                               <td  class="td"></td>
                               <td style='width: 3%;  border: 1px solid #ddd; color:#BBB;'>${h + a +v.stock_group_name}</td>
                               <td class='bangla_name d-none d-print-none td'></td>
                               <td class="td"></td>
                               <td class="td"></td>
                               <td  class='alias d-none d-print-none td'></td><td  class='created_user d-none d-print-none td'></td><td class='last_update d-none d-print-none td'></td>
                            </tr>`);
                chart_id = v.stock_group_id;

            }
            if (v.product_name != null) {
                html.push(`<tr id="${v.stock_item_id}" class="left left-data editIcon table-row"  data-toggle="modal" data-target="#EditLedgerModel">
                                <td class="sl td" >${i++}</td>
                                <td  class="td" >${h+a+v.product_name}</td>
                                <td  class="bangla_name d-none d-print-none td">${(v.bangla_product_name||'')}</td>
                                <td class="td">${v.symbol}</td>
                                <td  class="td">
                                    <input type="hidden" class="form-control get_group_id" name="get_group_id" value="'+v.group_chart_id+'" ">${h+a+v.stock_group_name}
                                </td>
                                <td  class="alias d-none d-print-none td">${(v.alias||'')}</td>
                                <td  class="created_user d-none d-print-none td">${v.user_name||''}</td>
                                <td class="last_update d-none d-print-none td"><div><i  class="history_font_size">${(v.other_details ? JSON.parse(v.other_details) : '')}</i></div></td>
                            </tr>`);
            }

            if ('children' in v) {
                html.push(getTreeView(v.children, depth + 1, chart_id));
            }
        });
        return html.join("");
    }
    $(document).ready(function() {
        allDataTree();
    });
    $(document).ready(function() {
        $('.sd').on('click', '.customers tbody tr', function() {
            @if(user_privileges_check('master', 'Stock Item', 'alter_role'))
            window.location = "{{ url('stock-item') }}" + '/' + $(this).attr('id');
            @endif
        })
    });
</script>
@endpush
@endsection
