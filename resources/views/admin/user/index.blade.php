@extends('layouts.backend.app')
@section('title','User View')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
@endpush
@section('admin_content')
<br>
<!-- add component-->
@component('components.index', [
'title' => 'List of User',
'add_route'=>route('user.create'),
'print' => 'Print',
'excel'=>'excel',
'pdf'=>'pdf',
'print_layout'=>'landscape',
'print_header'=>'List of User',
'close_route'=>route('user-dashboard'),
'user_privilege_status_type'=>'master',
'user_privilege_title'=>'User',
'user_privilege_type'=>'create_role'
])
@slot('body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead">
</div>
@endslot
@endcomponent

@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>
$(function() {

    function getUser() {
        $.ajax({
            url: "{{ url('user')}}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                let data_targer = " ";
                html +=
                    '<table  id="tableId"  style=" border-collapse: collapse; "   class="table table-striped customers " >';
                html += '<thead >';
                html += '<tr>';
                html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                html += '<th style="width: 3%;  border: 1px solid #ddd;">User Id</th>';
                html += '<th style="width: 3%;  border: 1px solid #ddd;">User Name</th>';
                html += '<th style="width: 3%;  border: 1px solid #ddd;">Level</th>';
                html += '<th style="width: 3%;  border: 1px solid #ddd;" >Activity</th>';
                html += '<th style="width: 3%;  border: 1px solid #ddd;" >Operations</th>';
                html +=
                    '<th style="width: 3%;  border: 1px solid #ddd;" >Last Updated User Privilege</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody id="myTable" class="qw">';
                $.each(response.data, function(key, v) {
                    let edit_url = "{{ url('user') }}" + '/' + v.id;
                    let edit_user_privilege_url = "{{ url('user-privilege') }}" + '/' + v
                    .id;
                    html += '<tr id="' + v.id +
                        '" class="left left-data editIcon table-row"  data-toggle="modal" data-target="#EditGodownModal" >';
                    html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;">' +
                        (key + 1) + '</td>';
                        html += '<td  style="width: 3%;  border: 1px solid #ddd;">' + v
                        .log_in_id + '</td>';
                    html += '<td  style="width: 3%;  border: 1px solid #ddd;">' + v
                        .user_name + '</td>';
                    html += '<td  style="width: 3%;  border: 1px solid #ddd;">' + v
                        .user_level + '</td>';
                    if (v.activity == 1) {

                        html +=
                            '<td  style="width: 3%;  border: 1px solid #ddd;">Active</td>';
                    } else {
                        html +=
                            '<td  style="width: 3%;  border: 1px solid #ddd;"><span style="color:red">Inactive</span> </td>';
                    }
                    @if(user_privileges_check('master', 'User', 'alter_role'))
                    html +=
                        '<td class="upadte_user" style="width: 3%;  border: 1px solid #ddd;"><a href="' +
                        edit_url +
                        '"><i class="fa fa-edit" style="font-size:28px;color:#4d9900;"></i></a></td>';
                    @endif
                    @if(Auth()->user()->user_level==1)
                    html +=
                        '<td  class="upadte_user_privilege" class="" style="width: 3%;  border: 1px solid #ddd;"><a href="' +
                        edit_user_privilege_url +
                        '"><i class="fa fa-edit" style="font-size:28px;color:#4d9900;"></i></a></td>';
                    @endif

                    html += "</tr> ";
                });
                html += '</tbody>';
                html += '<tfoot>';
                html += '<tr>';
                html += '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                html += '<th style="width: 3%;  border: 1px solid #ddd;">User Id</th>';
                html += '<th style="width: 3%;  border: 1px solid #ddd;">User Name</th>';
                html += '<th style="width: 3%;  border: 1px solid #ddd;">Level</th>';
                html += '<th style="width: 3%;  border: 1px solid #ddd;" >Activity</th>';
                html += '<th style="width: 3%;  border: 1px solid #ddd;" >Operations</th>';
                html +=
                    '<th style="width: 3%;  border: 1px solid #ddd;" >Last Updated User Privilege</th>';
                html += '</tr>';
                html += '</tfoot>';
                html += '</table>';
                $(".sd").html(html);
                get_hover();
            }
        })

    }
    getUser();
});


function swal_message(data, message) {
    swal({
        title: 'Oops...',
        text: data,
        type: message,
        timer: '1500'
    });
}
</script>
@endpush
@endsection
