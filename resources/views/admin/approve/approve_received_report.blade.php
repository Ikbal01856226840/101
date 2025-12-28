@extends('layouts.backend.app')
@section('title','Approve')
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
'title'=>'Approve',
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
                                                <h4>Approve Order</h4>
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
                            <div class="page-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <!-- Zero config.table start -->
                                        <div class="card " >
                                            <div class="card-block table_content  ">
                                                <div class="row col-md-10  m-1">
                                                    <div class="col-md-6 font_size_print">
                                                        <div style="display:flex;">
                                                            <span style="font-weight: bold;min-width:245px;font-size: 20px">Invoice No</span>
                                                            <span style="margin-left:1%;font-size: 20px;font-weight: bold;">: {{$tran_ledger->invoice_no}}</span>
                                                        </div style="display:flex;">
                                                        <div style="display:flex;">
                                                            <span style="min-width:245px;font-size: 20px">Ref No </span>
                                                            <span style="margin-left:1%;font-size: 20px">: {{$tran_ledger->ref_no}}</span>
                                                        </div>

                                                        <div style="display:flex;">
                                                            <span style="min-width:245px;font-size: 20px">Date </span>
                                                            <span style="margin-left:1%;font-size: 20px">: {{date('d M, Y', strtotime($tran_ledger->transaction_date))}}</span>
                                                        </div>
                                                        @if($tran_ledger->voucher_type_id==14)
                                                            <div style="display:flex;">
                                                                <span style="min-width:245px;font-size: 20px">Received with thanks from</span>
                                                                <span style="margin-left:1%;font-size: 20px">: {{$debit_credit->ledger_name}}</span>
                                                            </div>
                                                            <div style="display:flex;">
                                                                <span style="min-width:245px;font-size: 20px"></span>
                                                                <span style="margin-left:1%;font-size: 20px"> {{$debit_credit->mailing_add}}</span>
                                                            </div>
                                                       
                                                                <div style="display:flex;">
                                                                    <span style="min-width:245px;font-size: 20px">The Sum of </span>
                                                                    <span style="margin-left:1%;font-size: 20px">:= {{number_format((float)$debit_credit->credit, company()->amount_decimals)}}/=</span>
                                                                    
                                                                </div>
                                                                <div style="display:flex;">
                                                                    <span  style="min-width:245px;font-size: 20px">Sum in Word </span>
                                                                    <span style="margin-left:1%;font-size: 20px">: Taka {{ numberTowords($debit_credit->credit) }} Only</span>
                                                                </div>
                                                        @endif
                                                        <div style="display:flex;">
                                                            <span style="min-width:245px;font-size: 20px">Note </span>
                                                            <span style="margin-left:1%;font-size: 20px">: {{$tran_ledger->narration}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3" style="display:flex;">
                                                        <div class="col-sm-6">
                                                            <h5 style="text-align:left ;min-width:500px;;font-size: 20px">Received By : . . . . . . . . . . . . . . . . . . . . .</h5>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <h5 style="text-align:right;min-width:500px;;font-size: 20px">Received For : . . . . . . . . . . . . . . . . . . . . . .</h5>
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
            </div>
            <br>
            @endsection
