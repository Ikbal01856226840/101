
@extends('layouts.backend.app')
@section('title','Dashboard User')
@push('css')
<!-- model style -->
<style>
    @media only screen and (max-width: 991px) {
        .main-body .page-wrapper .align-items-end .page-header-title,
        .main-body .page-wrapper .align-items-end .page-header-breadcrumb {
            margin-top: 0px !important;
        }
    }
</style>
@endpush
@section('admin_content')
<div class="pcoded-main-container navChild">
    <div class="pcoded-content  ">
        <div class="pcoded-inner-content  " >
                <!-- Main-body start -->
            <div class="main-body  side-component">
                <div class="page-wrapper m-t-0 p-0">
                <!-- Page-header start -->
                    <div class="page-header m-0 p-0">
                        <div class="row align-items-end">
                            <div class="col-lg-8">
                                <div class="page-header-title">
                                    <div class="d-inline ">
                                        <h4 class="text-center mx-auto"></h4>
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
                    <div class="page-body left-data">
                            <!-- Basic Form Inputs card start -->
                            <div class="card">
                                <div class="row">
                                    <div class="col-md-6 " >
                                        @if (Auth::user()->user_level==1)
                                            <h5 style="background-color:#CCCCCC" class="text-center ">Create user</h5>
                                            <li  class="m-1 voucher_type ata"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('user.create')}}">Add User</a></li>
                                          </ul>
                                            <li  class="m-1 voucher_type {{Route::is('user-list-show') ? 'activedata' : ''}}" data-turbolinks="false"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('user-list-show')}}">Vew User</a></li>
                                            </ul>
                                        @endif
                                    </div>
                                    <div class="col-md-6  ">
                                      <h5 style="background-color:#CCCCCC" class="text-center">Change Password</h5>
                                        <li  class="m-1 voucher_type ata"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('user_change_password')}}">Change Password</a></li>
                                    </ul>
                                    </div>
                                    {{-- <div class="col-md-4  ">
                                        <h5 style="background-color:#CCCCCC" class="text-center"></h5>
                                    </div> --}}
                                <div>
                            </div>
                    </div>
                </div>
           </div>
       </div>
   </div>
</div>
@push('js')
<script>

</script>
@endpush
@endsection

