
@extends('layouts.backend.app')
@section('title','Branch')
@section('admin_content')
<br>
<!-- branch add model  -->
@component('components.create', [
    'title'    => 'Edit Branch [Edit]',
    'form_id' => 'edit_branch_form',
    'help_route'=>route('showCompany'),
    'close_route'=>route('showCompany'),
    'veiw_route'=>route('branch-show'),
    'method'=> 'PUT',

])
    @slot('body')
    <div class="row">
        <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6" >
            <div class="card-block ">
                <div class="form-group">
                    <input type="hidden" name="id" class="form-control id"  id="id" value="{{$data->id}}" >
                    <label for="formGroupExampleInput">Brnach Name:</label>
                    <input type="text" name="branch_name" class="form-control "  id="formGroupExampleInput" value="{{$data->branch_name}}" >
                    <span id='error_branch_name' class=" text-danger"></span>
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput">Remark:</label>
                    <input type="text" name="alias" class="form-control "  id="formGroupExampleInput"  value="{{$data->remark}}">

                </div>
            </div>
        </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6 "  >
            <div class="card-block  " >
                <div class=" m-t-0 " style="margin-left:-36px;">
                    <div style="margin-left: 36px;">
                        <div class="form-group">
                            <label for="formGroupExampleInput">Alias:</label>
                            <input type="text" name="remark" class="form-control "  id="formGroupExampleInput"  value="{{$data->alias}}" >
                            <span id='error_alias_name' class=" text-danger"></span>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endslot
    @slot('footer')
    <div class="col-lg-4 ">
        <div class="form-group">
            <button type="submit"  id="add_voucher_btn" class="btn hor-grd btn-grd-primary btn-block submit" style="width:100%" ><u>S</u>ave</button>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
        <a class=" btn hor-grd btn-grd-success btn-block " href="{{route('showCompany')}}" style="width:100%"><u>B</u>ack</a>
        </div>
    </div>
    <div class="col-lg-4">
        @if(user_privileges_check('master','Branch','delete_role'))
            <div class="form-group">
                <button  type="button" class=" btn hor-grd btn-grd-danger deleteIcon "  data-dismiss="modal" style="width:100%">Delete</button>
            </div>
        @endif
    </div>
    @endslot
 @endcomponent
@push('js')
<!-- table hover js -->
<script>
     // update branch ajax request
     $("#edit_branch_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id = $('.id').val();
        $("#edit_branch_btn").text('Adding...');
        $.ajax({
            url: "{{ url('branch') }}" + '/' + id ,
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(xhr,response,response1) {
                    claer_error()
                    swal_message(response,'success');
                    location.replace('{{ route("branch-show") }}');
                    claer_error()
                    $("#edit_branch_btn").text('Edit Branch');

            },
            error : function(data,status,xhr){
                    if(data.status==400){
                        swal_message(data.message,'error');
                    } if(data.status==422){
                        claer_error();
                        $('#error_branch_name').text(data.responseJSON.data.branch_name[0]);
                    }

                }
        });
    });

//data validation data clear
function claer_error(){
    $('#error_branch_name').text('');
}
// delete  ajax request
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
                    url: "{{ url('branch') }}" + '/' + id ,
                    type : "POST",
                    data : {'_method' : 'DELETE', '_token' : csrf_token},
                    success: function (data) {
                        swal_message(data.message,'success');
                        location.replace('{{ route("branch-show") }}');
                    },
                    error: function () {
                        swal_message(data.message,'error');
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
    function swal_message(data,message){
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



