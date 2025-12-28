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
                            <div class="page-body left-data">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <!-- Zero config.table start -->
                                        <div class="card ">
                                            <div class="card-block payment_font_size_print table_content">
                                                <div class="row ">
                                                    <h4 style="text-align: center;font-weight: bold; padding:0%;margin:0%">{{company()->company_name }}</h4>
                                                    <div style="display: flex;">
                                                        <h4 style=" flex: 1; text-align: center;font-weight: bold; padding:0%;margin:0%">Bill of : {{$tran_ledger->voucher_name }}</h4>
                                                        <p style="position: absolute; right: 0;padding:0%;margin:0%">Print Date :{{date('d M, Y')}}</p>
                                                    </div>                                                    
                                                </div>
                                                <hr style="border-bottom: 2px solid #000; ">
                                                <div class="row col-md-10 m-1">
                                                    <div class="col-md-6 ">
                                                        <div style="display:flex;">
                                                            <span style="font-weight: bold;min-width:50px">Invoice No</span>
                                                            <span style="margin-left:1%">: {{$tran_ledger->invoice_no}}</span>
                                                        </div>
                                                        <div style="display:flex;">
                                                            <span style="min-width:65px">Ref No </span>
                                                            <span style="margin-left:1%">: {{$tran_ledger->ref_no}}</span>
                                                        </div>

                                                        <div style="display:flex;">
                                                            <span style="min-width:65px">Date </span>
                                                            <span style="margin-left:1%">: {{date('d M, Y', strtotime($tran_ledger->transaction_date))}}</span>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                <div class="dt-responsive table-responsive cell-border sd ">
                                                    <table id="tableId" style=" border-collapse: collapse;" class="table  customers ">
                                                        
                                                        <thead>
                                                            <tr>
                                                                
                                                                <th style="width: 6%;  border: 1px solid #ddd;font-weight: bold;">Particular</th>
                                                                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;text-align: right;">Amount</th>
                                                                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;text-align: right;">Amount</th>
                                                                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Remarks</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="qw" id="myTable">
                                                            @php $total_debit=0;$total_credit=0; $debit_credit_symbol='';@endphp
                                                            @foreach ($debit_credit as $key=>$data)
                                                                @php $total_debit+=$data->debit; $total_credit+=$data->credit;@endphp
                                                                @if($debit_credit_symbol!=$data->dr_cr)
                                                                    @php $debit_credit_symbol=$data->dr_cr; @endphp
                                                                    <tr>
                                                                    
                                                                        <td style="width: 1%;  border: 1px solid #ddd;font-weight: bold;">&ensp;{{$data->dr_cr=="Dr"?"Accounts":"Through"}}</td>
                                                                        <td style="width: 6%;  border: 1px solid #ddd;"></td>
                                                                        <td style="width: 3%;  border: 1px solid #ddd;text-align: right;"></td>
                                                                        <td style="width: 3%;  border: 1px solid #ddd;text-align: right;"></td>
                                                                    </tr>
                                                                @endif
                                                                <tr>
                                                                
                                                                    <td style="width: 1%;  border: 1px solid #ddd;">&emsp;&emsp;{{$data->ledger_name}}</td>
                                                                
                                                                    <td style="width: 3%;  border: 1px solid #ddd;text-align: right;">{{(float)$data->debit==0?" ":number_format((float)$data->debit, company()->amount_decimals)}}</td>
                                                                    <td style="width: 3%;  border: 1px solid #ddd;text-align: right;">{{(float)$data->credit==0?"":number_format((float)$data->credit, company()->amount_decimals)}}</td>
                                                                    <td style="width: 6%;  border: 1px solid #ddd;"></td>
                                                                </tr>
                                                            @endforeach
                                                            <tr>
                                                                
                                                                <td style="width: 6%;  border: 1px solid #ddd;font-weight: bold; text-align: right;">TOTAL :</td>
                                                                <td style="width: 3%;  border: 1px solid #ddd;font-weight: bold;text-align: right;">{{number_format((float)$total_debit, company()->amount_decimals)}}</td>
                                                                <td style="width: 3%;  border: 1px solid #ddd;font-weight: bold;text-align: right;">{{number_format((float)$total_credit, company()->amount_decimals)}}</td>
                                                                <td style="width: 3%;  border: 1px solid #ddd;font-weight: bold;text-align: right;"></td>
                                                            </tr>
                                                        </tbody>
                                 
                                                    </table>
                                                    <br/>
                                                    <div class="row mt-6">
                                                        <div style="display:flex;">
                                                            <span style="width:190px">Note </span>
                                                            <span style="text-align: left">:</span>
                                                            <span style="margin-left:1%"> {{$tran_ledger->narration}}</span>
                                                        </div>
                                                   </div>
                                                   <div class="row mt-6">
                                                        <div style="display:flex;">
                                                            <span style="width:190px">Amount (in word) </span>
                                                            <span style="text-align: left">:</span>
                                                            <span style="margin-left:1%">Taka {{ numberTowords($total_debit) }} Only </span>
                                                        </div>
                                                </div>
                                                    <div class="row mt-3" style="display:flex;">
                                                        <div class="col-sm-6">
                                                            <h5 style="text-align:left ;min-width:500px;">Received By : . . . . . . . . . . . . . . . . . . . . .</h5>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <h5 style="text-align:right;min-width:500px;">Received For : . . . . . . . . . . . . . . . . . . . . . .</h5>
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
