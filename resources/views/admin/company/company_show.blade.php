
@extends('layouts.backend.app')
@section('title','Company')
@push('css')
<style>
    @media only screen and (max-width: 991px) {
        .main-body .page-wrapper .align-items-end .page-header-title,
        .main-body .page-wrapper .align-items-end .page-header-breadcrumb {
            margin-top: 0px !important;
        }
        .main-body .page-wrapper .page-header {
            margin-bottom: 0px;
        }
    }
</style>
@endpush
@section('admin_content')
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <!-- Main-body start -->
                <div class="main-body p-0">
                    <div class="page-wrapper p-0">
                        <!-- Page-header start -->
                        <div class="page-header p-0">
                            <div class="row align-items-end">
                                <div class="page-header-title">
                                    <div class="d-inline">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Page-header end -->
                        <!-- Page-body start -->
                        <div class="page-body ">
                            <div class="row">
                                <!-- <div class="col-md-1"></div> -->
                                <div class="col-sm-12">
                                    <!-- Zero config.table start -->
                                    <div id="print-demo2" class="card m-1 p-1">
                                        <div class="card-header p-1">
                                        </div>
                                        <div class="card-block">
                                        <div class="text-center">
                                            <div class="row">
                                                <div class="col-md-6 ">
                                                    <div class="card">
                                                        <div class="text-left" style="background-color:#CCCCCC">
                                                            <h4 class="m-2">Global Setup</h4>
                                                        </div>
                                                        @if(user_privileges_check('Global Setup','unit_or_branch','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('branch-show') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left  mb-3 ">
                                                                    <a class="text-decoration-none "  href="{{ route('branch-show') }}" >Unit/Branch</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('menu','tab_company','create_role'))
                                                            <div class="row m-1 left-data {{Route::is('company.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-3  " >
                                                                    <a class="text-decoration-none "  href="{{ route('company.index') }}">Company</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="row m-1 left-data"><hr class="m-0 p-0">
                                                            <div class="col-md-12 text-left mb-3  " >
                                                                <a class="text-decoration-none "  href="{{asset('sales_order_app\SalesOrder.apk')}}">App Download</a>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $allowedUsers = ['masud', 'DIDERUL ALAM (IT)', 'Rehanit'];
                                                        @endphp
                                                        {{-- @if(user_privileges_check('menu','tab_company','create_role')) --}}
                                                            @if(in_array(Auth::user()->user_name, $allowedUsers))
                                                                <div class="row m-1 left-data"><hr class="m-0 p-0">
                                                                    <div class="col-md-12 text-left mb-3 " >
                                                                        <a class="text-decoration-none"href="{{ url('set-alias') }}">Set Alias</a>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        {{-- @endif --}}
                                                        @if(user_privileges_check('menu','tab_company','create_role'))
                                                           @if(Auth::user()->user_name=='masud')
                                                            <div class="row m-1 left-data"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-3 " >
                                                                    <a class="text-decoration-none"href="{{ url('logs') }}">Log Viewer</a>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        @endif
                                                        @if(user_privileges_check('menu','tab_company','create_role'))
                                                           @if(Auth::user()->user_name=='masud')
                                                            <div class="row m-1 left-data"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-3 " >
                                                                    <a class="text-decoration-none"href="{{ url('logs') }}">Report Settong</a>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        @endif                                                        
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-md-6 ">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1"></div>
                            </div>
                        </div>
                        <!-- Page-body end -->
                    </div>
                    <!-- Main-body end -->
                </div>
            </div>
        </div>
    </div>
@push('js')
@endpush
@endsection
