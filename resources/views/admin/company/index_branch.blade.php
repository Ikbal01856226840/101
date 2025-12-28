
@extends('layouts.backend.app')
@section('title','Branch')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
@endpush
@section('admin_content')
<br>
 <!-- add component-->
 @component('components.index', [
    'title'    => 'Branch [View]',
    'print' => 'Print',
    'excel'=>'excel',
    'pdf'=>'pdf',
    'print_layout'=>'landscape',
    'print_header'=>'Branch',
    'close_route'=>route('showCompany'),
    'add_route'=>route('branch.create'),
    'user_privilege_status_type'=>'Global Setup',
    'user_privilege_title'=>'unit_or_branch',
    'user_privilege_type'=>'create_role'

])
    @slot('body')
    <div class="dt-responsive table-responsive cell-border sd tableFixHead">
    </div>
    @endslot
@endcomponent
<!-- add and edit form include -->
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>
       $(document).ready(function () {
        $('.sd').on('click','.customers tbody tr',function(){
            $(this).attr('id');
            })
       });
   // edit branch ajax request
    $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            edit_group_chart(id);
    });
     // update branch ajax request
    $("#edit_stock_group_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id = $('.id').val();
        $("#edit_group_chart_btn").text('Adding...');
        $.ajax({
            url: "{{ url('stock-group') }}" + '/' + id ,
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(xhr,response,response1) {
                        claer_error()
                    swal({
                            title: 'Success!',
                            text: response.message,
                            type: 'success',
                            timer: '1500'
                        })
                    allDataTree();
                    $("edit_stock_group_bt").text('Add Stock Group');
                    $("#edit_stock_group_form")[0].reset();
                    $("#EditStockGroupModal").modal('hide');
            },
            error : function(data,status,xhr){

                    if(data.status==400){
                        swal({
                        title: 'Oops...',
                        text: data.message,
                        type: 'error',
                        timer: '1500'
                    });
                    } if(data.status==422){
                        claer_error();
                        $('.error_stock_group_name').text(data.responseJSON.data.stock_group_name[0]);
                    }

                }
        });
    });

     //get  all data show
   function getBranch(){
    $.ajax({
            url: "{{ url('branch')}}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                let data_targer=" ";
                html +='<table id="tableId"  style=" border-collapse: collapse; "   class="table table-striped customers " >';
                    html +='<thead >';
                        html+='<tr>';
                            html+= '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html+= '<th style="width: 3%;  border: 1px solid #ddd;">Branch Name</th>';
                            html+= '<th style="width: 3%;  border: 1px solid #ddd;">Alias</th>';
                            html+='<th style="width: 3%;  border: 1px solid #ddd;" >Remark</th>';
                        html+='</tr>';
                    html+='</thead>';
                    html+='<tbody id="myTable" class="qw">';
                        $.each(response.data, function(key, v) {
                        html+=" <tr id='"+v.id +"' class='left left-data table-row' >";
                            html += '<td class="sl" style="width: 3%;  border: 1px solid #ddd;">'+(key+1)+'</td>' ;
                            html += '<td  style="width: 3%;  border: 1px solid #ddd;">'+(v.branch_name?v.branch_name: '')+'</td>';
                            html += '<td  style="width: 3%;  border: 1px solid #ddd;">'+(v.alias?v.alias:'')+'</td>';
                            html += '<td  style="width: 3%;  border: 1px solid #ddd;">'+(v.remark?v.remark: '')+'</td>';
                        html+="</tr> ";
                        });
                    html+='</tbody>';
                    html+='<tfoot>';
                        html+='<tr>';
                            html+= '<th style="width: 3%;  border: 1px solid #ddd;">SL</th>';
                            html+= '<th style="width: 3%;  border: 1px solid #ddd;">Branch Name</th>';
                            html+= '<th style="width: 3%;  border: 1px solid #ddd;">Alias</th>';
                            html+='<th style="width: 3%;  border: 1px solid #ddd;" >Remark</th>';
                        html+='</tr>';
                    html+='</tfoot>';
                html+='</table>';
                $(".sd").html(html);
                get_hover();
            }
        })

   }
   getBranch();
//branch edit modal show
function tableTrModal(){
    edit_group_chart($('.currentNav').closest('tr').attr('id'));
    $('.group_id').val($('.currentNav').closest('tr').attr('id'));
    if($('.currentNav').closest('tr').attr('id')>99){
        $(this).find('form').trigger('reset');
        $('#EditGroupChartModal').modal('show');
    }
}
//branch edit function
function edit_group_chart(id){
    $.ajax({
            url: "{{ url('stock-group') }}" + '/' + id ,
            type: "GET",
            dataType: "JSON",
        success: function(response) {
            $(".id").val(response.data.stock_group_id);
            $(".stock_group_name").val(response.data.stock_group_name);
            $(".under").val(response.data.under).trigger('change');
            $(".item_add").val(response.data.item_add).trigger('change');
            $('.alias').val(response.data.alias);
            $(".group_category").val(response.data.group_category).trigger('change');

        }});
}
//data validation data clear
function claer_error(){
    $('#error_group_chart_name').text('');
}
$(document).ready(function () {
    $('.sd').on('click','.customers tbody tr',function(){
        @if (user_privileges_check('Global Setup','unit_or_branch','alter_role'))
          window.location = "{{ url('branch') }}" + '/' + $(this).attr('id') ;
        @endif
    })
});


</script>
@endpush
@endsection


