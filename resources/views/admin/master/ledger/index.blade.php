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
</style>
@endpush
@section('admin_content')
<br>
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
    'title' => 'Accounts Ledger',
    'print' => 'Print',
    'add_route'=>route('ledger.create'),
    'plan_view'=>'Tree View',
    'tree_view'=>'Plain View',
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'print_layout'=>'landscape',
    'print_header'=>'Accounts Ledger',
    'setting_model'=>'setting_model',
    'close_route'=>route('master-dashboard'),
    'user_privilege_status_type'=>'master',
    'user_privilege_title'=>'Ledger',
    'user_privilege_type'=>'create_role'
])
@slot('body')
<div class="col-md-4 p-1 m-0 ledger_type_form">
    <label  for="exampleInputEmail1">Ledger Type </label>
    <select name="ledger_type" id="ledger_type" class="ledger_type " >
        <option value="">select</option>
        <option value="None">None</option>
        <option value="Dealer">Dealer</option>
        <option value="Retrailer">Retrailer</option>

    </select>
</div>
<div class="ledger-form-body d-none">
    <div class="row m-0 p-0">
        <div class="page-header m-0 p-0">
            <form id="add_ledger_form" method="POST">
                @csrf
                {{ method_field('POST') }}
                <div class="row m-0 p-0">
                    <div class="col-md-4">
                        <select name="group_id" class="form-control js-example-basic-single">
                            <option value="">--select--</option>
                            {!!html_entity_decode($group_chart_id)!!}
                        </select>
                    </div>
                    <div class="col-md-3 ">
                        <div class="form-group  m-0 p-1">
                            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:100%">Search</button>
                        </div>
                    </div>
                    <div class="col-md-3 ">
                        <label></label>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="dt-responsive table-responsive cell-border sd tableFixHead">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers ">
        <thead>
            <tr>
                <th class="td">SL</th>
                <th class="td">Ledger Name</th>
                <th class="td bangla_name d-none d-print-none">Bangla Ledger Name</th>
                <th class="td">Group Name</th>
                <th class="td">Nature of Group</th>
                <th class="td">Ledger Type</th>
                <th class="td">Starting Balance</th>
                <th class="td alias d-none d-print-none">Alias</th>
                <th class="td created_user d-none d-print-none">Created By</th>
                <th class="td last_update d-none d-print-none">History</th>
            </tr>
        </thead>
        <tbody id="myTable" class="ledger_body">
        </tbody>
        <tfoot>
            <tr>
                <th class="td">SL</th>
                <th class="td">Ledger Name</th>
                <th class="td bangla_name d-none d-print-none">Bangla Ledger Name</th>
                <th class="td">Group Name</th>
                <th class="td">Nature of Group</th>
                <th class="td">Ledger Type</th>
                <th class="td">Starting Balance</th>
                <th class="alias d-none d-print-none td">Alias</th>
                <th class="created_user d-none d-print-none td">Created By</th>
                <th class="last_update d-none d-print-none td ">History</th>
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
<br>
@push('js')
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>

<script>
    let i = 1;
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
            $('.tree_id').removeClass('d-none');
            $('.ledger-form-body').removeClass('d-none');
            $('.ledger_type_form').addClass('d-none');

        });

        //group chart searching
        $("#add_ledger_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            i = 1;
            $.ajax({
                url: '{{route("ledger-searching-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    console.log(response)
                    $('.ledger_body').html(getTreeView(response, depth = 0, 0));
                    page_wise_setting_checkbox();
                    set_scroll_table();
                    get_hover();
                }
            })
            $('.tree_id').removeClass('d-none');
            $('.ledger-form-body').removeClass('d-none');
        });

        $('.tree_id').click(function() {
            $(this).addClass('d-none');
            $('.plain_id').removeClass('d-none');
            $('.ledger-form-body').addClass('d-none');
            $('.ledger_type_form').removeClass('d-none');
            allDataShow();
        });
    });
    //plain all data show
    function allDataShow() {
        $('.ledger_body').empty();
        $(".modal").show();
        $.ajax({
            url: "{{ url('ledger_view/plain_view')}}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {

                let chunkSize = 500; // Adjust chunk size as needed
                let data = response.data;
                let totalChunks = Math.ceil(data.length / chunkSize);
                
                $(".modal").hide();
                // Function to append a chunk of data to the table
                function appendChunk(startIndex) {
                    let endIndex = Math.min(startIndex + chunkSize, data.length);
                    let html = [];

                    for (let i = startIndex; i < endIndex; i++) {
                        let v = data[i];
                        if (v.under != 0) {
                            html.push(`<tr id=${v.ledger_head_id} class="left left-data table-row" data-toggle="modal">
                            <td class="sl td">${i + 1}</td>
                            <td class="td">${v.ledger_name}</td>
                            <td class="td bangla_name d-none d-print-none">${v.bangla_ledger_name || ''}</td>
                            <td class="td">${v.group_chart_name}</td>
                            <td class="nature_val td">${v?.o}</td>
                            <td class="ledger_type_name td ">${v.ledger_type==1 ? 'Dealer' : v.ledger_type==2?'Retrailer':'None'}</td>
                            <td class="nature_val td">${Math.abs(v.opening_balance) || ''}</td>
                            <td class="alias d-none d-print-none td">${v.alias || ''}</td>
                            <td class="created_user d-none d-print-none td">${v.user_name || ''}</td>
                            <td class="last_update d-none d-print-none td">
                                <div><i class="history_font_size">${v.other_details ? JSON.parse(v.other_details) : ''}</i></div>
                            </td>
                        </tr>`);
                        }
                    }

                    $('.ledger_body').append(html.join(""));

                    // If there are more chunks, append the next chunk after a delay
                    if (startIndex + chunkSize < data.length) {
                        setTimeout(function() {
                            appendChunk(startIndex + chunkSize);
                        }, 0); // Use setTimeout to allow UI updates
                    } else {
                        // Additional functions after all data is appended
                        set_scroll_table();
                        page_wise_setting_checkbox();
                        get_hover();
                    }
                }
                // Start appending chunks
                appendChunk(0);
            }
        })

    }
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
                                <td class='bangla_name d-none d-print-none td'></td>
                                <td class="td"></td>
                                <td class="td"'>${natureGroupName(v?.nature_group,v?.o)}</td>
                                <td class="td"></td>
                                <td class="td"></td>
                                <td class='alias d-none d-print-none td'></td>
                                <td  class='created_user d-none d-print-none td'></td>
                                <td class='last_update d-none d-print-none td'></td></tr>`);
                    chart_id = v.group_chart_id;
                }
                if (v.ledger_name != null) {
                    html.push(`<tr id="${v.ledger_head_id }" class="left left-data editIcon table-row"  data-toggle="modal" data-target="#EditLedgerModel">
                                <td class="td">${i++}</td>
                                <td class="td">${h+h+a+v.ledger_name}</td>
                                <td class="bangla_name d-none d-print-none td">${(v.bangla_ledger_name||'')}</td>
                                <td class="td">
                                    <input type="hidden" class="form-control get_group_id" name="get_group_id" value="${v.group_chart_id}">${h+a+v.group_chart_name}
                                </td>
                                <td class="nature_val td" >${natureGroupName(v?.nature_group,v?.o)}</td>
                                <td class="ledger_type_name td ">${ledgerType(v?.ledger_type)}</td>
                                <td class="nature_val td"  >${Math.abs(v.opening_balance)||''}</td>
                                <td  class="alias d-none d-print-none td">${(v.alias||'')}</td>
                                <td  class="created_user d-none d-print-none td">${(v.user_name||'')}</td>
                                <td class=" last_update d-none d-print-none td"><div><i  class="history_font_size">${(v.other_details ? JSON.parse(v.other_details) : '')}</i></div></td>
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


    $(document).ready(function() {
        allDataShow();
    });

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
</script>
@endpush
@endsection