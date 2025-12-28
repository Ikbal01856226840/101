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
                                                <h4>Sales Order</h4>
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
                                                    <h4 style="text-align: center;font-weight: bold; padding:0%;margin:0%">{{company()->company_name }}</h4>
                                                    <h4 style="text-align: center;font-weight: bold; padding:0%;margin:0%">Bill of : {{$tran_ledger->voucher_name }}</h4>
                                                </div>
                                                <hr>
                                                <div class="row col-md-10 row_style m-1">
                                                        <div style="display:flex;">
                                                            <span style="font-weight: bold;min-width:50px">Invoice No</span>
                                                            <span style="margin-left:1%">: {{$tran_ledger->invoice_no}}</span>
                                                        </div style="display:flex;">
                                                        <div style="display:flex;">
                                                            <span style="min-width:65px">Order Date</span>
                                                            <span style="margin-left:1%">: {{date('d M, Y', strtotime($tran_ledger->transaction_date))}}</span>
                                                        </div>
                                                        <div style="display:flex;">
                                                            <span style="min-width:65px">Party Name</span>
                                                            <span style="margin-left:1%">:{{$tran_ledger->ledger_name }}</span>
                                                        </div>
                                                        <div style="display:flex;">

                                                            <spans style="min-width:65px">Address </spans>
                                                            <span style="margin-left:1% ">:{{$tran_ledger->mailing_add}}</span>
                                                        </div>
                                                        <div style="display:flex;">
                                                            <span style="min-width:65px">Note </span>
                                                            <span style="margin-left:1%">:{{$tran_ledger->narration}}</span>
                                                        </div>
                                                </div>
                                                <div class="dt-responsive table-responsive cell-border sd ">
                                                    <table id="tableId" style=" border-collapse: collapse;" class="table  customers ">
                                                            {{-- stock--}}
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 3%;  border: 1px solid #ddd;">SL</th>
                                                                    <th style="width: 3%;  border: 1px solid #ddd;">Description Of Goods</th>
                                                                    <th style="width: 3%;  border: 1px solid #ddd;" class="text-right">Quantity</th>
                                                                    <th style="width: 3%;  border: 1px solid #ddd;" class="text-right">Rate</th>
                                                                    <th style="width: 3%;  border: 1px solid #ddd;" class="text-right">Amount</th>

                                                                </tr>
                                                            </thead>
                                                        <tbody class="qw" id="myTable">
                                                        @php  $pcs=0;$sheet=0;$Sft=0; $Cone=0; $Roll=0;$Mtr=0;$Yrd=0; $kg=0;$Pair=0;$Rim=0; $Bottle=0;$Ltr=0;$Pkt=0; $Set=0;$Pnd=0;$INC=0;$SMTR=0;$Dram=0;$Yds=0;$Bag=0;@endphp
                                                            @php $total_qty=0;$amount=0; $total_rate=0; $final_total_credit_product_wise=0; $final_total_debit_product_wise=0; @endphp
                                                            @foreach ($stock as $key=>$data)
                                                            @php $total_qty+=$data->qty;$amount+=$data->total; $total_rate+=$data->rate; @endphp
                                                            @php
                                                                switch ($data->unit_of_measure_id) {
                                                                case 1:
                                                                    $pcs += $data->qty;
                                                                    break;
                                                                case 4:
                                                                    $sheet += $data->qty;
                                                                    break;
                                                                case 5:
                                                                    $Sft += $data->qty;
                                                                    break;
                                                                case 6:
                                                                    $Cone += $data->qty;
                                                                    break;
                                                                case 7:
                                                                    $Roll += $data->qty;
                                                                    break;
                                                                case 8:
                                                                    $Mtr += $data->qty;
                                                                    break;
                                                                case 9:
                                                                    $Yrd += $data->qty;
                                                                    break;
                                                                case 10:
                                                                    $kg += $data->qty;
                                                                    break;
                                                                case 11:
                                                                    $Pair += $data->qty;
                                                                    break;
                                                                case 12:
                                                                    $Rim += $data->qty;
                                                                    break;
                                                                case 13:
                                                                    $Bottle += $data->qty;
                                                                    break;
                                                                case 14:
                                                                    $Ltr += $data->qty;
                                                                    break;
                                                                case 15:
                                                                    $Pkt += $data->qty;
                                                                    break;
                                                                case 16:
                                                                    $Set += $data->qty;
                                                                    break;
                                                                case 17:
                                                                    $Pnd += $data->qty;
                                                                    break;
                                                                case 18:
                                                                    $INC += $data->qty;
                                                                    break;
                                                                case 19:
                                                                    $SMTR += $data->qty;
                                                                    break;
                                                                case 20:
                                                                    $Dram += $data->qty;
                                                                    break;
                                                                case 21:
                                                                    $Yds += $data->qty;
                                                                    break;
                                                                case 22:
                                                                    $Bag += $data->qty;
                                                                    break;
                                                                default:
                                                                    // Handle unexpected unit_of_measure_id values if needed
                                                                    break;
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td class="sl" style="width: 3%;  border: 1px solid #ddd;">{{ $key+1 }}</td>
                                                                <td style="width: 3%;  border: 1px solid #ddd;" class="text-wrap">{{$data->product_name}}</td>
                                                                <td style="width: 3%;  border: 1px solid #ddd;text-align: right;">{{$data->qty." "}}{{$data->symbol }}</td>
                                                                <td style="width: 3%;  border: 1px solid #ddd;text-align: right;">{{number_format((float)$data->rate, company()->amount_decimals)}}</td>
                                                                <td style="width: 3%;  border: 1px solid #ddd;text-align: right;">{{number_format((float)$data->total, company()->amount_decimals)}}</td>
                                                            </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td class="sl" style="width: 3%;  border: 1px solid #ddd;"></td>
                                                                <td style="width: 3%;  border: 1px solid #ddd;font-weight: bold;text-align: right;">TOTAL :</td>
                                                                <td style="width: 3%;  border: 1px solid #ddd;font-weight: bold;;text-align: right;">@if(!empty($pcs)){{$pcs}}Pcs @endif
                                                                @if(!empty($sheet)){{$sheet}} Sheet @endif @if(!empty($Sft)){{$Sft}} Sft @endif @if(!empty($Cone)){{$Cone}}Cone @endif @if(!empty($Roll)){{$Roll}}Roll @endif
                                                                @if(!empty($Mtr)){{$Mtr}} Mtr @endif @if(!empty($Yrd)){{$Yrd}} Yrd  @endif @if(!empty($kg)){{$kg}} kg @endif @if(!empty($Pair)){{$Pair}} Pair  @endif @if(!empty($Rim)){{$Rim}} Rim  @endif
                                                                @if(!empty($Bottle)){{$Bottle}} Bottle  @endif @if(!empty($Ltr)){{$Ltr}} Ltr  @endif @if(!empty($Pkt)){{$Pkt}} Pkt @endif @if(!empty($Set)){{$Set}} Set @endif @if(!empty($Pnd)){{$Pnd}} Pnd  @endif
                                                                @if(!empty($INC)){{$INC}}INC @endif @if(!empty($SMTR)){{$SMTR}}SMTR @endif @if(!empty($Yds)){{$Yds}}Yds @endif @if(!empty($Bag)){{$Bag}}Bag @endif
                                                                </td>
                                                                <td style="width: 3%;  border: 1px solid #ddd;font-weight: bold;;text-align: right;"></td>
                                                                <td style="width: 3%;  border: 1px solid #ddd;font-weight: bold;;text-align: right;">{{number_format((float)$amount, company()->amount_decimals)}}</td>

                                                            </tr>


                                                        </tbody>

                                                    </table>
                                                    <div class="row mt-3">
                                                        <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                                            <h5 class="text-start">Received By : . . . . . . . . . . . . . . . . . . . . .</h5>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <h5 class="text-sm-end text-start">Received For : . . . . . . . . . . . . . . . . . . . . . .</h5>
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
            </div>
            <br>
            @push('js')

            <!-- table hover js -->
            <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
            <script>
                JsBarcode("#barcode", "{{$tran_ledger->invoice_no }}", {
                    width: 1,
                    height: 30,
                    displayValue: false
                });
            </script>
            @endpush
            @endsection
