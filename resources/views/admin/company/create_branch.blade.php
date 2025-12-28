
@extends('layouts.backend.app')
@section('title','Branch')
@section('admin_content')
<br>
<!-- voucher add model  -->
@component('components.create', [
    'title'    => 'Add Branch [New]',
    'form_id' => 'add_branch_form',
    'help_route'=>route('showCompany'),
    'close_route'=>route('showCompany'),
    'veiw_route'=>route('branch-show'),
    'method'=> 'POST',

])
    @slot('body')
    <div class="row">
        <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6" >
            <div class="card-block ">
                <div class="form-group">
                    <label for="formGroupExampleInput">Brnach Name:</label>
                    <input type="text" name="branch_name" class="form-control "  id="formGroupExampleInput" placeholder="Branch Name" >
                    <span id='error_branch_name' class=" text-danger"></span>
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput">Remark:</label>
                    <input type="text" name="alias" class="form-control "  id="formGroupExampleInput" placeholder="Remark " >

                </div>
            </div>
        </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6 "  >
            <div class="card-block  " >
                <div class=" m-t-0 " style="margin-left:-36px;">
                    <div style="margin-left: 36px;">
                        <div class="form-group">
                            <label for="formGroupExampleInput">Alias:</label>
                            <input type="text" name="remark" class="form-control "  id="formGroupExampleInput" placeholder=" Alias" >
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
    <div class="col-lg-6 ">
        <div class="form-group">
            <button type="submit"  id="add_voucher_btn" class="btn hor-grd btn-grd-primary btn-block submit" style="width:100%" ><u>S</u>ave</button>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
        <a class=" btn hor-grd btn-grd-success btn-block " href="<?php echo route('showCompany') ?>" style="width:100%"><u>B</u>ack</a>
        </div>
    </div>
    @endslot
 @endcomponent
@push('js')
<!-- table hover js -->
<script>
$(function() {
    // add new branch ajax request
    $("#add_branch_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#add_branch_btn").text('Adding...');
        $.ajax({
                url: '{{ route("branch.store") }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data,status,xhr) {
                    claer_error();
                        swal({
                            title: 'Success!',
                            text: data.message,
                            type: 'success',
                            timer: '1500'
                        })
                        location.replace('{{ route("branch-show") }}')
                    $("#add_branch_btn").text('Add Branch');

                },
                error : function(data,status,xhr){
                    claer_error();
                    if(data.status==400){
                        swal({
                        title: 'Oops...',
                        text: data.message,
                        type: 'error',
                        timer: '1500'
                    });
                    } if(data.status==422){
                        claer_error();
                      $('#error_branch_name').text(data.responseJSON.data.branch_name[0]);
                    }

                }
        });
    });

});
//data validation data clear
function claer_error(){
    $('#error_branch_name').text('');
}
</script>
@endpush
@endsection



