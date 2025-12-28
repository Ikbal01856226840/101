@extends('layouts.backend.app')
@section('title','Change Password')
@push('css')
<style>
    #password-strength-status {
        padding: 5px 10px;
        color: #FFFFFF;
        border-radius: 4px;
        margin-top: 5px;
    }

    .medium-password {
        background-color: #b7d60a;
        border: #BBB418 1px solid;
    }

    .weak-password {
        background-color: #ce1d14;
        border: #AA4502 1px solid;
    }

    .strong-password {
        background-color: #12CC1A;
        border: #0FA015 1px solid;
    }
</style>
<!-- model style -->
@endpush
@section('admin_content')
div class="pcoded-main-container">
    <div class="pcoded-content  ">
        <div class="pcoded-inner-content  " >
               <br>
                <!-- Main-body start -->
            <div class="main-body ">
                <div style="padding-top:-6.9rem!important;" class="page-wrapper " >
                <!-- Page-header start -->
                    <div class="page-header m-0 p-0">
                        <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title">
                            <div class="d-inline ">
                                <h4 class="text-center mx-auto">Change Password</h4>
                            </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="page-header-breadcrumb">
                            </div>
                        </div>
                        </div>
                    </div>
                    <!-- Page-header end -->
                    <!-- Page body start -->
                    <div class="page-body">
                            <!-- Basic Form Inputs card start -->
                        <form id="change_password_form"  method="POST">
                            @csrf
                            <div class="card">
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6" >
                                        <div class="card-block ">
                                            <h6 class="m-t-0">Change Password</h6>
                                            <hr>
                                            <div class="form-group my-2 row">
                                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">User Name</label>
                                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-10">
                                                   <input  type="text" name="user_name" class="form-control " value="{{$user_name->user_name}}" />
                                                </div>
                                            </div>
                                            <div class="form-group my-2 row">
                                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">  Old  Password</label>
                                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-10">
                                                <input  type="password" name="old_password" class="form-control" placeholder=" Old Password input"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6 "  >
                                        <div class="card-block  " >
                                            <h6 class="">Basic Inputs</h6>
                                            <hr>
                                            <div class=" b-l-default m-t-0 " style="margin-left:-36px;">
                                                <div style="margin-left: 36px;">
                                                    <div class="form-group my-2 row">
                                                        <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">New  Password</label>
                                                        <div class="col-xl-10 col-md-10 col-sm-10">
                                                          <input  type="password" name="new_password" id="new_password" class="form-control"placeholder=" New Password input" />
                                                          <div id="password-strength-status"></div>
                                                        </div>

                                                    </div>
                                                    <div class="form-group my-2 row">
                                                        <label class="col-xl-2 col-md-3 col-sm-2 col-form-label">Re-type New Password</label>
                                                        <div class="col-xl-10 col-md-9 col-sm-10">
                                                           <input type="password" name="confirm_password"class="form-control confirm_password"placeholder=" Re-type New Password input" />
                                                           <span id="user_name_span" class="" style="font-size: small;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-lg-6 ">
                                    <div class="form-group">
                                        <button type="submit" id="change_password_btn" class="btn hor-grd btn-grd-primary btn-block submit " style="width:100%" ><u>S</u>ave</button>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                    <a class=" btn hor-grd btn-grd-success btn-block " href="<?php echo route('dashboard') ?>" style="width:100%"><u>B</u>ack</a>
                                    </div>
                                </div>
                            </div>
                                <!-- Input Alignment card end -->
                        </form>
                    </div>
                </div>
           </div>
       </div>
   </div>
</div>
@push('js')
<script>
$(function() {
    // add new voucher ajax request
    $("#change_password_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#change_password_btn").text('Adding...');
        $.ajax({
                url: '{{route("change_password")}}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data) {
                        swal({
                            title: 'Success!',
                            text: data.message,
                            type: 'success',
                            timer: '1500'
                        })
                        location.replace('{{ route("user_change_password") }}')
                    $("#change_password_btn").text('Add User');

                },
                error : function(data,status,xhr){
                    if(data.status==404){
                        swal({
                        title: 'Oops...',
                        text: data.message,
                        type: 'error',
                        timer: '1500'
                    });
                    }

                }
        });
    });

});
$(document).ready(function () {

        $('.confirm_password').on('keyup',function(){

           let new_password=$('#new_password').val();
           let confirm=$(this).val();
           if(new_password==confirm){
            $("#user_name_span").removeClass("badge badge-danger");
            $("#user_name_span").text("Confirm password  match").addClass("badge badge-success");
            $(":submit").removeAttr("disabled");
           }else{
            $("#user_name_span").removeClass("badge badge-success");
            $("#user_name_span").text("Confirm password not match").addClass("badge badge-danger");
            $(":submit").attr("disabled", true);
           }

        })
});


</script>
<script>
    $(document).ready(function () {
      $("#new_password").on('keyup', function(){
        var number = /([0-9])/;
        var alphabets = /([a-zA-Z])/;
        var special_characters = /([~,!,@,#,$,%,^,&,*,-,_,+,=,?,>,<])/;

        if($('#new_password').val().length>0){
            if ($('#new_password').val().length <8) {
            $('#password-strength-status').removeClass();
            $('#password-strength-status').addClass('weak-password');
            $('#password-strength-status').html("Weak (should be atleast 8 characters.)");
            $(":submit").attr("disabled", true);
            } else {
                if ($('#new_password').val().match(number) && $('#new_password').val().match(alphabets) && $('#new_password').val().match(special_characters)) {
                    $('#password-strength-status').removeClass();
                    $('#password-strength-status').addClass('strong-password');
                    $('#password-strength-status').html("Strong");
                    $(":submit").removeAttr("disabled");
                } else {
                    $('#password-strength-status').removeClass();
                    $('#password-strength-status').addClass('medium-password');
                    $('#password-strength-status').html("Medium (should include alphabets, numbers and special characters or some combination.)");
                    $(":submit").attr("disabled", true);
                }
            }
        }
        else{
            $('#password-strength-status').removeClass();
        }

      });
    });
    </script>
@endpush
@endsection



