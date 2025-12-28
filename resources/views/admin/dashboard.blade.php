
@extends('layouts.backend.app')
@section('title','Dashboard')
@push('css')
@endpush
@section('admin_content')
<div class="pcoded-main-container master-component">
    <p class='component-name d-none'>master</p>
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <!-- Main-body start -->
                <div class="main-body p-0">
                    <div class="page-wrapper p-0">
                        <!-- Page-header start -->
                        <div class="page-header mt-4 p-0">
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
                                                        @php
                                                            $acc_group=user_privileges_role(Auth::user()->id,'master','Group');
                                                        @endphp
                                                        @if ($acc_group?$acc_group->display_role==1:'')
                                                            <div class="row m-1 left-data {{Route::is('group-chart.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left  mb-3 ">
                                                                    <a class="text-decoration-none "  href="{{ route('group-chart.index') }}"  data-turbolinks="false"  >Accounts Group</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @php
                                                           $acc_ledger=user_privileges_role(Auth::user()->id,'master','Ledger');
                                                        @endphp
                                                        @if ($acc_ledger? $acc_ledger->display_role==1:'')
                                                            <div class="row m-1 left-data {{Route::is('ledger.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-3  " >
                                                                    <a class="text-decoration-none "  href="{{ route('ledger.index') }}" data-turbolinks="false">Accounts Ledger</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @php
                                                          $voucher_type=user_privileges_role(Auth::user()->id,'master','Voucher Type');
                                                        @endphp
                                                        @if ($voucher_type? $voucher_type->display_role==1:'')
                                                            <div class="row m-1 left-data {{Route::is('voucher.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-3  " >
                                                                    <a class="text-decoration-none "  href="{{ route('voucher.index') }}">Accounts Voucher</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @php
                                                          $godown=user_privileges_role(Auth::user()->id,'master','Godown');
                                                        @endphp
                                                        @if ($godown? $godown->display_role==1:'')
                                                            <div class="row m-1 left-data {{Route::is('godown.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-3  " >
                                                                    <a class="text-decoration-none "  href="{{ route('godown.index') }}" data-turbolinks="false">Setup Godown</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @php
                                                          $distribution_center=user_privileges_role(Auth::user()->id,'master','Distribution Center');
                                                        @endphp
                                                        @if ( $distribution_center?  $distribution_center->display_role==1:'')
                                                            <div class="row m-1 left-data {{Route::is('distribution.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-3  " >
                                                                    <a class="text-decoration-none "  href="{{ route('distribution.index') }}" data-turbolinks="false">Setup Shop/Distribution Center</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="row m-1 left-data "><hr class="m-0 p-0">
                                                            <div class="col-md-12 text-left mb-3">
                                                                <td>Discount Offer-POS</td>
                                                            </div>

                                                        </div>
                                                        @php
                                                          $components=user_privileges_role(Auth::user()->id,'master','Components');
                                                        @endphp
                                                        @if ($components?  $components->display_role==1:'')
                                                        <div class="row m-1 left-data {{Route::is('components.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                            <div class="col-md-12 text-left mb-3  " >
                                                                <a class="text-decoration-none "  href="{{ route('components.index') }}">Components</a>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        <div class="row m-1 left-data"><hr class="m-0 p-0">
                                                            <div class="col-md-12 text-left mb-3 ">
                                                                <td>Bill of Material</td>
                                                            </div>

                                                        </div><hr class="m-0 p-0">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 side-component">
                                                    <div class="card p-0 m-0">
                                                        <div class="text-left " style="background-color:#CCCCCC">
                                                            <h4 class="m-2">Inventory Setup</h4>
                                                        </div>
                                                        @php
                                                          $stock_group=user_privileges_role(Auth::user()->id,'master','Stock Group');
                                                        @endphp
                                                        @if ($stock_group?$stock_group->display_role==1:'')
                                                            <div class="row  m-1  right-data {{Route::is('stock-group.index') ? 'active' : ''}}">
                                                                <hr class="m-0 p-0">
                                                                <div class="col-md-6 text-left mb-3">
                                                                    <a class="text-decoration-none "  href="{{ route('stock-group.index') }}" data-turbolinks="false" >Stock Group</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @php
                                                             $selling_price=user_privileges_role(Auth::user()->id,'master','Group Selling Price');
                                                        @endphp
                                                        @if ($selling_price?  $selling_price->display_role==1:'')
                                                            <div class="row  m-1 right-data"><hr class="m-0 p-0">
                                                                <div class="col-md-6 text-left mb-3">
                                                                <td>Stock Group >> Price</td>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @php
                                                            $stock_group_commision=user_privileges_role(Auth::user()->id,'master','stock_group__commission');
                                                         @endphp
                                                        @if ($stock_group_commision? $stock_group_commision->display_role==1:'')
                                                            <div class="row  m-1 right-data"><hr class="m-0 p-0">
                                                                <div class="col-md-6 text-left mb-3">
                                                                <td>Stock Group >> Commission</td>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @php
                                                          $stock_item=user_privileges_role(Auth::user()->id,'master','Stock Item');
                                                         @endphp
                                                        @if ( $stock_item?  $stock_item->display_role==1:'')
                                                            <div class="row  m-1  right-data {{Route::is('stock-item.index') ? 'active' : ''}}">
                                                                <hr class="m-0 p-0">
                                                                <div class="col-md-6 text-left mb-3">
                                                                    <a class="text-decoration-none "  href="{{ route('stock-item.index') }}" >Stock Item</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @php
                                                         $opening_balance=user_privileges_role(Auth::user()->id,'master','stock_item__opening_balance');
                                                        @endphp
                                                        @if ( $opening_balance?  $opening_balance->display_role==1:'')
                                                            <div class="row   m-1 right-data"><hr class="m-0 p-0">
                                                                <div class="col-md-6 text-left mb-3">
                                                                <td>Stock Item >> Opening Balance</td>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @php
                                                          $stock_item_selling_price=user_privileges_role(Auth::user()->id,'master','Selling Price');
                                                        @endphp
                                                        @if ( $stock_item_selling_price?  $stock_item_selling_price->display_role==1:'')
                                                            <div class="row m-1 left-data {{Route::is('stock-item-price.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-3  " >
                                                                    <a class="text-decoration-none "  href="{{ route('stock-item-price.index') }}">Stock Item >> Price</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @php
                                                         $stock_item__commission=user_privileges_role(Auth::user()->id,'master','stock_item__commission');
                                                        @endphp
                                                        @if ( $stock_item__commission?   $stock_item__commission->display_role==1:'')
                                                        <div class="row  m-1 right-data"><hr class="m-0 p-0">
                                                            <div class="col-md-6 text-left mb-3">
                                                            <td>Stock Item >> Commission</td>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @php
                                                          $unit_of_size=user_privileges_role(Auth::user()->id,'master','Unit of Size');
                                                        @endphp
                                                        @if ( $unit_of_size? $unit_of_size->display_role==1:'')
                                                            <div class="row m-1 left-data {{Route::is('size.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-3  " >
                                                                    <a class="text-decoration-none "  href="{{ route('size.index') }}">Stock Item >> Unit of Size</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @php
                                                            $unit_of_measure=user_privileges_role(Auth::user()->id,'master','Units of Measure');
                                                        @endphp
                                                        @if ( $unit_of_measure? $unit_of_measure->display_role==1:'')
                                                            <div class="row m-1 left-data {{Route::is('measure.index') ? 'active' : ''}}"><hr class="m-0 p-0">
                                                                <div class="col-md-12 text-left mb-3  " >
                                                                    <a class="text-decoration-none "  href="{{ route('measure.index') }}">Stock Item >> Unit of Measure</a>
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
<script type="text/javascript" src="{{asset('libraries\assets\pages\dashboard\custom-dashboard.min.js')}}"></script>
 <!-- Chart js -->
 <script type="text/javascript" src="{{asset('libraries\bower_components\Chart.js\js\Chart.js')}}"></script>
 <!-- amchart js -->
 <script type="text/javascript" src="{{asset('libraries\assets\pages\dashboard\amchart\js\amcharts.js')}}"></script>
 <script type="text/javascript" src="{{asset('libraries\assets\pages\dashboard\amchart\js\serial.js')}}"></script>
 <script type="text/javascript" src="{{asset('libraries\assets\pages\dashboard\amchart\js\light.js')}}"></script>
@endpush
@endsection
