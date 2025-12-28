
@extends('layouts.backend.app')
@section('title','Dashboard')
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
<div class="pcoded-main-container ">
    <p class='component-name d-none'>master</p>
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
                                                <div class="col-md-6 side-component">
                                                    <div class="card">
                                                        <div class="text-left" style="background-color:#CCCCCC">
                                                            <h4 class="m-2">Accounting Setup</h4>
                                                        </div>
                                                        @if(user_privileges_check('master','Group','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('group-chart.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left  mb-2 " >
                                                                    <a class="text-decoration-none "  href="{{ route('group-chart.index') }}"  data-turbolinks="false" style="font-size: 18px!important;" >Accounts Group</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','Ledger','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('ledger.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-2  " >
                                                                    <a class="text-decoration-none "  href="{{ route('ledger.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Accounts Ledger</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','Voucher Type','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('voucher.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-2  " >
                                                                    <a class="text-decoration-none "  href="{{ route('voucher.index') }}"  style="font-size: 18px!important;">Accounts Voucher</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','Godown','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('godown.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-2  " >
                                                                    <a class="text-decoration-none "  href="{{ route('godown.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Setup Godown</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','Distribution Center','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('distribution.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-2  " >
                                                                    <a class="text-decoration-none "  href="{{ route('distribution.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Setup Shop/Distribution Center</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','POS Discount','display_role'))
                                                            <div class="row m-1 left-data "><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-2">
                                                                    <a class="text-decoration-none "  href="{{ route('discount-offer-pos.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Discount Offer-POS</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','Components','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('components.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-2  " >
                                                                    <a class="text-decoration-none "  href="{{ route('components.index') }}"  data-turbolinks="false" style="font-size: 18px!important;">Components</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','Customer','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('customer.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-2  " >
                                                                    <a class="text-decoration-none "  href="{{ route('customer.index') }}"  data-turbolinks="false"  style="font-size: 18px!important;">Customer</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','Supplier','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('supplier.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-2  " >
                                                                    <a class="text-decoration-none "  href="{{ route('supplier.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Supplier</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','Material','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('bill-of-material.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-2  " >
                                                                    <a class="text-decoration-none "  href="{{ route('bill-of-material.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Bill of Material</a>
                                                                </div>
                                                            </div><hr class="m-0 p-0">
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6 side-component">
                                                    <div class="card p-0 m-0">
                                                        <div class="text-left " style="background-color:#CCCCCC">
                                                            <h4 class="m-2">Inventory Setup</h4>
                                                        </div>
                                                      @if(user_privileges_check('master','Stock Group','display_role'))
                                                            <div class="row  m-1  right-data {{Route::is('stock-group.index') ? 'active' : ''}}">
                                                                <hr class="m-0 p-0">
                                                                <div class="col-md-6 text-left mb-2">
                                                                    <a class="text-decoration-none "  href="{{ route('stock-group.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Stock Group</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','Group Price','display_role'))
                                                            <div class="row  m-1  right-data {{Route::is('stock-group-price.index') ? 'active' : ''}}">
                                                                <hr class="m-0 p-0">
                                                                <div class="col-md-6 text-left mb-2">
                                                                    <a class="text-decoration-none "  href="{{ route('stock-group-price.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Stock Group >> Price</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                       @if(user_privileges_check('master','stock_group__commission','display_role'))
                                                            <div class="row  m-1 right-data {{Route::is('stock-commission.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-6 text-left mb-2">
                                                                     <a class="text-decoration-none "  href="{{ route('stock-commission.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Stock Group >> Commission</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','Stock Item','display_role'))
                                                            <div class="row  m-1  right-data {{Route::is('stock-item.index') ? 'active' : ''}}">
                                                                <hr class="m-0 p-0">
                                                                <div class="col-md-6 text-left mb-2">
                                                                    <a class="text-decoration-none "  href="{{ route('stock-item.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Stock Item</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','stock_item__opening_balance','display_role'))
                                                            <div class="row   m-1 right-data  {{Route::is('stock-item-opening.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-6 text-left mb-2">
                                                                    <a class="text-decoration-none "  href="{{ route('stock-item-opening.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Stock Item >> Opening Balance</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','Selling Price','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('stock-item-price.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-2  " >
                                                                    <a class="text-decoration-none "  href="{{ route('stock-item-price.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Stock Item >> Price</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','stock_item__commission','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('stock-item-commision.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-2  " >
                                                                    <a class="text-decoration-none "  href="{{ route('stock-item-commission.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Stock Item >> Commission</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','Unit of Size','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('size.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-2  " >
                                                                    <a class="text-decoration-none "  href="{{ route('size.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Stock Item >> Unit of Size</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(user_privileges_check('master','Units of Measure','display_role'))
                                                            <div class="row m-1 left-data {{Route::is('measure.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-2  " >
                                                                    <a class="text-decoration-none "  href="{{ route('measure.index') }}" data-turbolinks="false"  style="font-size: 18px!important;">Stock Item >> Unit of Measure</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <hr class="m-0 p-0">
                                                        </div>
                                                    </div>
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
 <!-- Chart js -->
 <script type="text/javascript" src="{{asset('libraries\bower_components\Chart.js\js\Chart.js')}}"></script>
 <!-- amchart js -->
 <script type="text/javascript" src="{{asset('libraries\assets\pages\dashboard\amchart\js\amcharts.js')}}"></script>
 <script type="text/javascript" src="{{asset('libraries\assets\pages\dashboard\amchart\js\serial.js')}}"></script>
 <script type="text/javascript" src="{{asset('libraries\assets\pages\dashboard\amchart\js\light.js')}}"></script>
@endpush
@endsection
