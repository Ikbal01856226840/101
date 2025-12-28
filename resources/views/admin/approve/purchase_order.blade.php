@extends('layouts.backend.app')
@section('title','Challan')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('custom_responsive/custom_master_responsive.css')}}">
<style>
    body {
        overflow: auto;
    }
</style>
@endpush
@section('admin_content')
<br>
@component('components.setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=>'approve',
    'page_unique_id'=>15,
    'title'=>'Challan',
    'sort_by'=>'sort_by',
    'insert_settings'=>'insert_settings',
    'view_settings'=>'view_settings'
])
@endcomponent
<div class="coded-main-container navChild ">
    <div class="pcoded-content">
        <div class="pcoded-inner-content"><br>
            <!-- Main-body start -->
            <div class="main-body p-0  side-component">
                <div class="page-wrapper m-t-0 p-0">
                    <div class="page-wrapper m-t-0 m-l-1 m-r-1 p-2">
                        <!-- Page-header start -->
                        <div class="page-header m-0 p-0  ">
                            <div class="row align-items-left">
                                <div class="col-lg-12">
                                    <div class="row ">
                                        <div class="col-md-3">
                                            <div class="page-header-title">
                                                <h4>Challan</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div style="float: right; margin-left: 5px;">
                                                <a style=" float:right; text-decoration: none; " href="{{route('master-dashboard')}}"><span class="fa fa-times-circle-o m-1" style="font-size:27px; color:#ff6666;"></span><span style="float:right;margin:2px; padding-top:5px; ">Close</span></a>
                                            </div>
                                            <div style="float: right; margin-left: 5px;">
                                                <a style=" float:right ;text-decoration: none; cursor: pointer" data-toggle="modal" data-target="#exampleModal"><span class="fa fa-cog m-1" style="font-size:27px;  color:Green;"></span><span style="float:right;margin:2px; padding-top:5px; ">Setting</span></a>
                                            </div>
                                            <div style="float: right;margin-left:9px">
                                                <a style="float:right; text-decoration: none;cursor: pointer; " onclick="print_html('protrait','')"><span class="fa fa-print m-1" style="font-size:27px; color:teal;"></span><span style="float:right;margin:2px; padding-top:5px;">Print</span></a>
                                            </div>
                                            <div style="float: right;margin-left:9px">
                                                <a style="float:right; text-decoration: none;cursor: pointer;" class="excel" onclick="exportTableToExcel('Order Approve')"><span class="fa fa-file-excel-o m-1 " style="font-size:25px; color:Gray;"></span><span style="float:right;margin:2px; padding-top:5px;">Excel</span></a>
                                            </div>
                                            <div style="float: right;margin-left:9px">
                                                <a style="float:right; text-decoration: none;cursor: pointer;" class="pdf_download" onclick="generateTable('Order Approve')"><span class="fa fa-file-pdf-o m-1 " style="font-size:25px; color:MediumSeaGree; "></span><span style="float:right;margin:2px; padding-top:5px;">Pdf</span></a>
                                            </div>
                                            <div style="float: right; width:200px;">
                                                <input type="text" id="myInput" style="border-radius: 5px" class="form-control form-control pb-1" width="100%" placeholder="searching">
                                            </div>
                                        </div>
                                        <hr style="margin-bottom: 0px;">
                                    </div>
                                </div>
                                <!-- Page-header end -->
                                <!-- Page-body start -->
                                <div class="page-body">
                                    <div class="row">
                                        <div class="page-header m-0  ">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Page-body start -->
                            <div class="page-body left-data">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <!-- Zero config.table start -->
                                        <div class="card ">
                                            <div class="card-block table_content">
                                                <div class="row ">

                                                    <h4 class="header_title_challan" style="text-align: center;font-weight: bold; padding:0%;margin:0%">{{company()->company_name }}</h4>
                                                    <h4 class="header_title_challan_name" style="text-align: center;font-weight: bold; padding:0%;margin:0%">{{$purchase_order_transaction->voucher_name??'Purchase Order' }}</h4>

                                                </div>
                                                <hr class="hr">
                                                <div class="row col-md-10 row_style m-1">
                                                    <div class="col-md-6 box">
                                                        <div style="display:flex;">
                                                            <span style="font-weight: bold;min-width:100px">Invoice No</span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span style="margin-left:1%">{{$purchase_order_transaction->invoice_no??''}}</span>
                                                        </div style="display:flex;">


                                                        <div style="display:flex;">
                                                            <span style="min-width:65px; min-width:100px">Order Date</span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span style="margin-left:1%">{{date('d M, Y', strtotime($purchase_order_transaction->date??''))}}</span>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6 box" style="border-left: 1px solid black;">

                                                        <div style="display:flex;">
                                                            <span style="min-width:160px">Supplier Name</span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span > {{$purchase_order_transaction->ledger_name??'' }}</span>
                                                        </div>
                                                        <div style="display:flex;">
                                                            <spans style="min-width:160px">Supplier Address</spans>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span> {{$purchase_order_transaction->mailing_add??''}}</span>
                                                        </div>
                                                        <div style="display:flex;">
                                                            <span style="min-width:160px">Order Quantity</span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span></span>
                                                        </div>
                                                        <div style="display:flex;">
                                                            <span style="min-width:160px">Country of Origin</span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-10  m-1">
                                                    <div class="col-md-12 box">
                                                        <div style="display:flex;">
                                                            <span style="min-width:65px; min-width:100px">Note </span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span style="margin-left:1%">{{$purchase_order_transaction->narration??''}}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="dt-responsive table-responsive cell-border sd ">
                                                    <table id="tableId" style=" border-collapse: collapse;" class="table  customers ">
                                                            {{-- stock--}}
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 1%;  border: 1px solid #ddd;">Serial No</th>
                                                                    <th style="width: 60%;  border: 1px solid #ddd;text-align:left">Description Of Goods</th>
                                                                    <th style="width: 20%;  border: 1px solid #ddd;text-align: right">Quantity</th>
                                                                    <th style="width: 19%;  border: 1px solid #ddd;text-align:center" class="text-left">Remarks</th>

                                                                </tr>
                                                            </thead>
                                                        <tbody class="qw" id="myTable">
                                                        @php
                                                        $pcs=0;$kg=0;;$Ltr=0;$Bottle=0;$pail=0;$Tin=0;$Feet=0;$Roll=0;$sheet=0;
                                                        $Pair=0;$Packet=0;$Sets=0;$Mtr=0;$Rim=0;$Coil=0;$Box=0;$Cn1=0;
                                                        $gm=0;$yard=0;$Drum=0;$inch=0;$Bag=0;$Can=0;$CBM=0;$Cft=0;$Cyl=0;
                                                        $Dozen=0;$Gallon=0;$Jar=0;$Lbs=0;$metter=0;$ounce=0;$Rft=0;
                                                        $Sqm=0;$Than=0;$Deffult=0;
                                                        @endphp
                                                            @php $total_qty=0;$amount=0; $total_rate=0; $final_total_credit_product_wise=0; $final_total_debit_product_wise=0; @endphp
                                                            @foreach ($stock as $key=>$data)
                                                            @php $total_qty+=$data->qty;$amount+=$data->total; $total_rate+=$data->rate; @endphp
                                                            @php
                                                                switch ($data->unit_of_measure_id) {
                                                                case 1:
                                                                    $pcs += $data->qty;
                                                                    break;
                                                                case 2:
                                                                    $kg += $data->qty;
                                                                    break;
                                                                case 3:
                                                                    $Ltr += $data->qty;
                                                                    break;
                                                                case 4:
                                                                    $Bottle += $data->qty;
                                                                    break;
                                                                case 5:
                                                                    $pail += $data->qty;
                                                                    break;
                                                                case 6:
                                                                    $Tin += $data->qty;
                                                                    break;
                                                                case 7:
                                                                    $Feet += $data->qty;
                                                                    break;
                                                                case 8:
                                                                    $Roll += $data->qty;
                                                                    break;
                                                                case 9:
                                                                    $Pair += $data->qty;
                                                                    break;
                                                                case 10:
                                                                    $Packet += $data->qty;
                                                                    break;
                                                                case 11:
                                                                    $Sets += $data->qty;
                                                                    break;
                                                                case 12:
                                                                    $Mtr += $data->qty;
                                                                    break;
                                                                case 13:
                                                                    $Rim += $data->qty;
                                                                    break;
                                                                case 14:
                                                                    $Coil += $data->qty;
                                                                    break;
                                                                case 15:
                                                                    $Box += $data->qty;
                                                                    break;
                                                                case 16:
                                                                    $Cn1 += $data->qty;
                                                                    break;
                                                                case 17:
                                                                    $gm += $data->qty;
                                                                    break;
                                                                case 18:
                                                                    $yard += $data->qty;
                                                                    break;
                                                                case 19:
                                                                    $Drum += $data->qty;
                                                                    break;
                                                                case 20:
                                                                    $inch += $data->qty;
                                                                    break;
                                                                case 21:
                                                                    $Bag += $data->qty;
                                                                    break;
                                                                case 22:
                                                                    $Can += $data->qty;
                                                                    break;
                                                                case 23:
                                                                    $CBM += $data->qty;
                                                                    break;
                                                                case 24:
                                                                    $Cft += $data->qty;
                                                                    break;
                                                                case 25:
                                                                    $Cyl += $data->qty;
                                                                    break;
                                                                case 26:
                                                                    $Dozen += $data->qty;
                                                                    break;
                                                                case 27:
                                                                    $Gallon += $data->qty;
                                                                    break;
                                                                case 28:
                                                                    $Jar += $data->qty;
                                                                    break;
                                                                case 29:
                                                                    $Lbs += $data->qty;
                                                                    break;
                                                                case 30:
                                                                    $metter += $data->qty;
                                                                    break;
                                                                case 31:
                                                                    $ounce += $data->qty;
                                                                    break;
                                                                case 32:
                                                                    $pack += $data->qty;
                                                                    break;
                                                                case 33:
                                                                    $Rft += $data->qty;
                                                                    break;
                                                                case 34:
                                                                    $Sqm += $data->qty;
                                                                    break;
                                                                case 35:
                                                                    $Than += $data->qty;
                                                                    break;
                                                                default:
                                                                    $Deffult += $data->qty;
                                                                    // Handle unexpected unit_of_measure_id values if needed
                                                                    break;
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td class="sl" style="width: 1%;  border: 1px solid #ddd; ">{{ $key+1 }}</td>
                                                                <td  style="width: 9%;  border: 1px solid #ddd;">{{$data->product_name}}</td>
                                                                <td style="width: 2%;  border: 1px solid #ddd;text-align: right;">{{$data->qty." "}}{{$data->symbol }}</td>
                                                                <td  style="width: 9%;  border: 1px solid #ddd;">{{$data->remark??''}}</td>
                                                            </tr>
                                                            @endforeach
                                                            <tr>
                                                                {{-- <td class="sl" style="width: 1%;  border: 1px solid #ddd;"></td> --}}
                                                                <td style="border: 1px solid #ddd;font-weight: bold;text-align: right;">Total:</td>
                                                                <td colspan="2" style="width: 2%;  border: 1px solid #ddd;font-weight: bold;;text-align: right;">

                                                                    @if(!empty($pcs)){{$pcs}} Pcs @endif
                                                                    @if(!empty($kg)){{$kg}} Kg @endif
                                                                    @if(!empty($Ltr)){{$Ltr}} Ltr @endif
                                                                    @if(!empty($Bottle)){{$Bottle}} Bottle @endif
                                                                    @if(!empty($pail)){{$pail}} Pail @endif
                                                                    @if(!empty($Tin)){{$Tin}} Tin @endif
                                                                    @if(!empty($Feet)){{$Feet}} Feet @endif
                                                                    @if(!empty($Roll)){{$Roll}} Roll @endif
                                                                    @if(!empty($sheet)){{$sheet}} Sheet @endif
                                                                    @if(!empty($Pair)){{$Pair}} Pair @endif
                                                                    @if(!empty($Packet)){{$Packet}} Packet @endif
                                                                    @if(!empty($Sets)){{$Sets}} Sets @endif
                                                                    @if(!empty($Mtr)){{$Mtr}} Mtr @endif
                                                                    @if(!empty($Rim)){{$Rim}} Rim @endif
                                                                    @if(!empty($Coil)){{$Coil}} Coil @endif
                                                                    @if(!empty($Box)){{$Box}} Box @endif
                                                                    @if(!empty($Cn1)){{$Cn1}} Cn1 @endif
                                                                    @if(!empty($gm)){{$gm}} Gm @endif
                                                                    @if(!empty($yard)){{$yard}} Yard @endif
                                                                    @if(!empty($Drum)){{$Drum}} Drum @endif
                                                                    @if(!empty($inch)){{$inch}} Inch @endif
                                                                    @if(!empty($Bag)){{$Bag}} Bag @endif
                                                                    @if(!empty($Can)){{$Can}} Can @endif
                                                                    @if(!empty($CBM)){{$CBM}} CBM @endif
                                                                    @if(!empty($Cft)){{$Cft}} Cft @endif
                                                                    @if(!empty($Cyl)){{$Cyl}} Cyl @endif
                                                                    @if(!empty($Dozen)){{$Dozen}} Dozen @endif
                                                                    @if(!empty($Gallon)){{$Gallon}} Gallon @endif
                                                                    @if(!empty($Jar)){{$Jar}} Jar @endif
                                                                    @if(!empty($Lbs)){{$Lbs}} Lbs @endif
                                                                    @if(!empty($metter)){{$metter}} Metter @endif
                                                                    @if(!empty($ounce)){{$ounce}} Ounce @endif
                                                                    @if(!empty($Rft)){{$Rft}} Rft @endif
                                                                    @if(!empty($Sqm)){{$Sqm}} Sqm @endif
                                                                    @if(!empty($Than)){{$Than}} Than @endif
                                                                    @if(!empty($Deffult)){{$Deffult}} @endif
                                                                </td>
                                                                 <td style="width: 2%;  border: 1px solid #ddd;text-align: right"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div class="row mt-5 received_by" style="display:flex;">
                                                        <div class="col-sm-3" >
                                                            <h5  style="text-align:left ;min-width:250px;">Prepared By : . . . . . . . . . . </h5>
                                                        </div>
                                                        <div class="col-sm-3" >
                                                            <h5  style="text-align:left ;min-width:250px;">Checked BY : . . . . . . . . . . .</h5>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <h5  style="text-align:right;min-width:250px;">Authorized BY : . . . . . . . . . .</h5>
                                                        </div>
                                                        <div class="col-sm-3" >
                                                            <h5  style="text-align:left ;min-width:250px;">Received For :. . . . . . . . . .</h5>
                                                        </div>
                                                        {{-- <div class="col-sm-3" >
                                                            <h5  style="text-align:left ;min-width:250px;">Prepared By : . . . . . . . . . . </h5>
                                                        </div>
                                                        <div class="col-sm-3" >
                                                            <h5  style="text-align:left ;min-width:250px;">Checked BY : . . . . . . . . . . .</h5>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <h5  style="text-align:right;min-width:250px;">Authorized BY : . . . . . . . . . .</h5>
                                                        </div>
                                                        <div class="col-sm-3" >
                                                            <h5  style="text-align:left ;min-width:250px;">Customer Signature:. . . . . . . . . .</h5>
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            @push('js')
            @endpush
            @endsection
