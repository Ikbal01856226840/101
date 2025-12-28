
<!DOCTYPE html>
<html lang="en">

<head>
<title>{{company()->company_name}}</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="#">
<meta name="keywords" content="Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
<meta name="author" content="#">
<!-- Favicon icon -->
<!-- Google font-->
<!-- Required Fremwork -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/bower_components/bootstrap/css/bootstrap5.css')}}">
    <!-- feather Awesome -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries/assets\icon\feather\css\feather.css')}}">
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\assets\icon\themify-icons\themify-icons.css')}}">
<!-- themify-icons line icon -->
<!-- Style.css -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries\assets\css\style.css')}}">
</head>
<style>
    .hd {
  display: none;
}

</style>
<body class="fix-menu">
    <!-- Pre-loader start -->
    <!-- Pre-loader end -->
    <section class="login-block">
        <!-- Container-fluid starts -->
        <div class="container">
            <div class="row">
            <div class="page-body">
            <div class="col-sm-12">
                    <!-- Authentication card start -->
                        <form class="md-float-material form-material" action="{{ route('login') }}" method="POST" >
                            @csrf
                            <div class="text-center">
                                <!-- <img src="<?php echo asset('libraries\assets\images\logo.png')?>" alt="logo.png"> -->
                                <h4>{{company()->company_name}}</h4>
                            </div>
                            <div class="auth-box card">
                                <div class="card-block">
                                    <div class="row m-b-20">
                                        <div class="col-md-12">
                                            <h3 class="text-center">Sign In</h3>
                                        </div>
                                    </div>
                                    @if ($message = Session::get('error'))
                                    <div class="alert alert-info background-danger">
                                        {{-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <i class="icofont icofont-close-line-circled text-white"></i>
                                        </button> --}}
                                        <strong>{{ $message }}</strong>
                                    </div>

                                    @endif
                                    <div class="form-group form-primary">
                                        <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('User Name') }}</label>

                                        <input id="user_name" type="text" class="form-control @error('user_name') is-invalid @enderror" name="user_name" value="{{Session::get('user_name') ?? ''}}"  autocomplete="user_name"  required autofocus>
                                        @error('user_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

                                    </div>
                                    <div class="form-group form-primary">
                                        <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{Session::get('password') ?? ''}}" autocomplete="current-password"  required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

                                    </div>
                                    @php
                                    // init variables
                                    $min_number = 1;
                                    $max_number = 9;
                                    // generating random numbers
                                    $random_number1 = mt_rand($min_number, $max_number);
                                    $random_number2 = mt_rand($min_number, $max_number);
                                @endphp
                                <div class="form-group row mt-2">
                                    <span for="LeaveType" class="col-sm-6 control-span"><span style="font-weight:bold;">CAPTCHA &nbsp;&nbsp;:</span>&nbsp;&nbsp;{{  $random_number1 . ' + ' . $random_number2;}}</span>
                                    <div class="col-sm-6">
                                        <input type="text" name="captchar" class="form-control form-control-sm" placeholder="Enter Captcha" {{Session::get('captchar') ? 'autofocus' : ''}}  value="{{Session::get('captchar') ? '':''}}"  required>
                                        <input name="firstNumber" type="hidden" value="{{ $random_number1; }} " />
                                        <input name="secondNumber" type="hidden" value="{{ $random_number2 }}" />
                                    </div>
                                </div>
                                    <div class="row m-t-30">
                                        <div class="col-md-12 text-center">
                                            <button type="submit" class="btn btn-primary btn-md btn-block waves-effect waves-light text-center m-b-20 login-btn">Sign in</button>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-10">
                                            <p class="text-inverse text-left m-b-0">Thank you.</p>
                                            <p class="text-inverse text-left"><a href="index-1.htm"><b class="f-w-600"></b></a></p>
                                        </div>
                                        <div class="col-md-2">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- end of form -->
                </div>
                <!-- end of col-sm-12 -->
            </div>
            <!-- end of row -->
        </div>
        <!-- end of container-fluid -->
    </section>

    <!-- Warning Section Ends -->
    <!-- Required Jquery -->
    <script type="text/javascript" src="<?php echo asset('libraries\bower_components\jquery\js\jquery.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo asset('libraries\bower_components\jquery-ui\js\jquery-ui.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo asset('libraries\bower_components\popper.js\js\popper.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo asset('libraries\bower_components\bootstrap\js\bootstrap5.min.js')?>"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="<?php echo asset('libraries\bower_components\jquery-slimscroll\js\jquery.slimscroll.js')?>"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="<?php echo asset('libraries\bower_components\modernizr\js\modernizr.js')?>"></script>
    <script type="text/javascript" src="<?php echo asset('libraries\bower_components\modernizr\js\css-scrollbars.js')?>"></script>
    <script>
        localStorage.clear();
        $('.login-btn').click(function(){
            localStorage.setItem('scrollValue', 0);
            localStorage.setItem('scrollValueId', 0)
        });
    </script>

</body>

</html>
