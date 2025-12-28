@extends('layouts.backend.app')
@section('title','Ledger')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
<style>
    .td {
        width: 3%;
        border: 1px solid #ddd;
    }
    .display_alias{
        display: none;
    }
</style>
@endpush
@section('admin_content')
<!-- setting component-->
@component('components.setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=>'ledger_head',
    'page_unique_id'=>2,
    'title'=>'Accounts Ledger',
    'alias_true'=>'alias_true',
    'last_inset_true'=>'last_inset_true',
    'bangla_true'=>'bangla_true',
    'redirect_page_true'=>'redirect_page_true'
])
@endcomponent
<!-- add component-->
@component('components.index', [
    'title' => 'Accounts Ledger Alias Set',
    'print_header'=>'Accounts Ledger Alias Set',
    'print_layout'=>'landscape',
    'print' => 'Print',
    'excel'=>'excel',
    'close_route'=>route('master-dashboard'),
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Ledger',
    'user_privilege_type'=>'alter_role'
])
@slot('body')
<div class="ledger-form-body">
    <div class="row m-0 p-0">
        <div class="page-header m-0 p-0 d-print-none">
            <form id="add_ledger_form" method="POST">
                @csrf
                {{ method_field('POST') }}
                <div class="row m-0 p-0">
                    <div class="col-md-4">
                        <select name="group_id" class="form-control js-example-basic-single" id="group_id">
                            <option value="">--select--</option>
                            {!!html_entity_decode($group_chart_id)!!}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="alias_type" id="alias_type_All" value="2" checked>
                            <label class="form-check-label" for="alias_type_auto">All</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="alias_type" id="alias_type_auto" value="0">
                            <label class="form-check-label" for="alias_type_auto">Auto</label>
                        </div>
                    
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="alias_type" id="alias_type_manual" value="1">
                            <label class="form-check-label" for="alias_type_manual">Manual</label>
                        </div>
                    </div>
                    <div class="col-md-3 ">
                        <div class="form-group  m-0 p-1">
                            <button type="button" class="btn hor-grd btn-grd-primary btn-block submit" id="search_ledger" style="width:100%">Search</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-3 ">
                    <label>Start Alias</label>
                    <input type="number" class="form-control" id="start_alias"  value="{{$nextAlias??100001}}" readonly required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn hor-grd btn-grd-primary btn-block" id="Set_Alias" style="width:100%">Set Alias</button>
                </div>                

            </div>
        </div>
    </div>
</div>
<form id="Store_Alias_form" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="dt-responsive table-responsive cell-border sd tableFixHead">
        <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers ">
            <thead>
                <tr>
                    <th class="td">SL</th>
                    <th class="td">Ledger Name</th>
                    <th class="td">Nature of Group</th>
                    <th class="td">Alias Type</th>
                    <th class="td">Alias</th>
                </tr>
            </thead>
            <tbody id="myTable" class="ledger_body">
            </tbody>
            <tfoot>
                <tr>
                    <th class="td">SL</th>
                    <th class="td">Ledger Name</th>
                    <th class="td">Nature of Group</th>
                    <th class="td">Alias Type</th>
                    <th class="td">Alias</th>
                </tr>
            </tfoot>
        </table>
        <div class="row">
            <div class="col-md-5"></div>
            <div class="col-md-2">
                <button type="button" class="btn hor-grd btn-grd-primary btn-block" id="Store_Alias" style="width:100%">Save</button>
            </div>
        </div>
        
            
        <div class="col-sm-12 text-center hide-btn">
            <span><b>Copyright &copy; 2014-2022 <a href="http://www.hamko-ict.com/">Hamko-ICT.</a> All rights
                    reserved.</b></span>
        </div>
    </div>

</form>

@endslot
@endcomponent
<br>
@push('js')
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
@if ($id??'')
    <script>
        let id="{{$id??''}}";
        $("#group_id").val(id).trigger('change');
        let alias_type="{{$alias_type??''}}";
        if(alias_type==0){
            $("#alias_type_auto").prop('checked', true);
        }else if(alias_type==1){
            $("#alias_type_manual").prop('checked', true);
        }
        $(document).ready(function() {
            setTimeout(() => {
                $("#add_ledger_form").submit();
            }, 100);
        })
    </script>
@else
<script>
    let id="";
    let alias_type="";
</script>
    
@endif
<script>
    let i = 1;
    let alias_type_All=0;
    let alias_type_auto=0;
    let alias_type_manual=0;

    $("#search_ledger").click(function() {
        alias_type_All=$('#alias_type_All').is(':checked');
        alias_type_auto=$('#alias_type_auto').is(':checked');
        alias_type_manual=$('#alias_type_manual').is(':checked');
        let alias_type = alias_type_manual ? 1 : (alias_type_auto ? 0 : 2);
        let id = $('#group_id').val();
        if(id){
            url = "{{route('get-set-alias-data', ['id' =>':id', 'alias_type' =>':alias_type'])}}";
            url = url.replace(':id',id);
            url = url.replace(':alias_type',alias_type);
            window.location.href=url;
        } 
    });

    $(function() {

        //all data tree show
        $('.plain_id').click(function() {
            i = 1;
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ url('ledger_view/tree_view')}}",
                success: function(response) {
                    $('.ledger_body').html(getTreeView(response, depth = 0, 0));
                    page_wise_setting_checkbox();
                    set_scroll_table();
                    get_hover();
                }
            })
            $(this).addClass('d-none');
            $('.ledger-form-body').removeClass('d-none');
            $('.ledger_type_form').addClass('d-none');

        });

        //group chart searching
        $("#add_ledger_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            alias_type_All=$('#alias_type_All').is(':checked');
            alias_type_auto=$('#alias_type_auto').is(':checked');
            alias_type_manual=$('#alias_type_manual').is(':checked');
            i = 1;
            $(".modal").show();
            $.ajax({
                url: '{{route("ledger-searching-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $('.ledger_body').html(getTreeView(response, depth = 0, 0));
                    page_wise_setting_checkbox();
                    set_scroll_table();
                    get_hover();
                    $(".modal").hide();
                }
            })
            $('.ledger-form-body').removeClass('d-none');
        });
    });
    
    // get Tree view table row
    function getTreeView(arr, depth = 0, chart_id = 0) {
        let html = [];
        let data_targer = " ";
        arr.forEach(function(v) {
            a = '&nbsp;&nbsp;&nbsp;&nbsp;';
            h = a.repeat(depth);
            if (v.under != 0) {
                if (chart_id != v.group_chart_id) {
                    html.push(`<tr style='pointer-events: none' class='left left-data editIcon table-row'>
                                <td class="td"></td>
                                <td class="td td-bold" style='color:#BBB;'>${h + a + v.group_chart_name}</td>
                                <td class="td"'>${natureGroupName(v?.nature_group,v?.o)}</td>
                                <td class="td"></td>
                                <td class='td'></td>`);
                    chart_id = v.group_chart_id;
                }
                if ((v.ledger_name != null) && alias_type_All || (alias_type_auto && v?.alias_type === 0) || (alias_type_manual && v?.alias_type === 1)) {
                    // <td class="nature_val td" >${natureGroupName(v?.nature_group,v?.o)}</td>
                    html.push(`<tr id="${v.ledger_head_id }" class="left left-data editIcon table-row ledger_head">
                                <td class="td">${i++}</td>
                                <td class="td">${h+h+a+v.ledger_name}</td>
                                
                                <td class="nature_val td" >${natureGroupName(v?.nature_group,v?.o)}</td>
                                <td class="td ">${aliasType(v?.alias_type)}</td>
                                <td  class="td">
                                    <input type="number" style="display:none" name="ledger_head_id[]" value="${v.ledger_head_id}">
                                    <input type="number" class="alias_type" style="display:none" name="alias_type[]" value="${v?.alias_type||''}">
                                    <input type="text" class="form-control aliasValue" name="alias[]" value="${v?.alias||''}" readonly>
                                </td>
                            </tr>`);
                }
            }
            if ('children' in v) {
                html.push(getTreeView(v.children, depth + 1, chart_id));
            }
        });
        return html.join("");
    }

    function natureGroupName(nature_group = '',nature_group_name='') {
        const groupMap = {
            1: 'Assets',
            2: 'Liabilities',
            3: 'Expenses',
            4: 'Income',
            5: 'Equity',
            6: 'A/L',
        };
        return groupMap[nature_group] || nature_group_name|| '';
    }

    function ledgerType(ledger_type = '') {
        const typeMap = {
            1: 'Dealer',
            2: 'Retrailer',
        };
        return typeMap[ledger_type] || 'None';
    }    
    function aliasType(alias_type = '') {
        const typeMap = {
            0: 'Auto',
            1: 'Manual',
        };
        return typeMap[alias_type] || '';
    }

    function swal_message(data, message) {
        swal({
            title: 'Successfully',
            text: data,
            type: message,
            timer: '1500'
        });
    }
    $(document).ready(function() {
        $('.sd').on('click', '.customers tbody tr', function() {
            @if(user_privileges_check('master', 'Ledger', 'alter_role'))
            window.location = "{{ url('ledger') }}" + '/' + $(this).attr('id');
            @endif
        });
    });

    $(document).ready(function() {
        // On dropdown selection change
        $('#ledger_type').on('change', function() {
            var selectedValue = $(this).val().toLowerCase();  // Get the selected dropdown value
           console.log(selectedValue);
            // Filter table rows based on the selected dropdown value
            $('#tableId tbody tr').filter(function() {
                $(this).toggle($(this).find(".ledger_type_name").text().toLowerCase().indexOf(selectedValue) > -1 || selectedValue === ""); // Show all if "All" is selected
            });
        });
    });

    $('#Set_Alias').click(function(){
        let start=$("#start_alias").val();
        if(start){
            $('.ledger_body').find('.aliasValue').each(function(){
                let alias_type=$(this).closest('td').find('.alias_type').val()
                if((!alias_type_manual) && alias_type != 1){
                    $(this).val(start);
                    // $(this).closest('td').text(start);
                    start++;
                    $(this).closest('td').find('.alias_type').val(0)
                }
                
            })
        }
    });

    $("#Store_Alias").click(function(){
        $("#Store_Alias_form").submit();
    })
    $("#Store_Alias_form").submit(function(e) {
        e.preventDefault();
        $(".modal").show();
        const fd = new FormData(this);
        $.ajax({
                url: '{{route("alias-update") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if(response?.success){
                        swal_message(response?.message,'success','Successfully');
                        $(".modal").hide();
                    }
                }
            })
    })
</script>
@endpush
@endsection