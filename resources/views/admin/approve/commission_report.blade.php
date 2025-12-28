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
    .th {
        border: 1px solid #ddd;
        font-weight: bold;
    }

    .td1 {
        border: 1px solid #ddd;
        font-size: 18px;
        font-family: Arial, sans-serif;
    }

    .td2 {
        border: 1px solid #ddd;
        font-size: 16px;
        font-family: Arial, sans-serif;
    }


    .td-bold {
        font-weight: bold;
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
                                            <div class="card-block table_content">
                                                <div class="row ">
                                                    <h4 class="header_title_challan"  style="text-align: center;font-weight: bold; padding:0%;margin:0%">{{company()->company_name }}</h4>
                                                    <h4 class="header_title_challan_name" style="text-align: center;font-weight: bold; padding:0%;margin:0%">Bill of : {{$tran_ledger->voucher_name }}</h4>
                                                </div>
                                                <hr>
                                                <div class="row col-md-10 row_style m-1">
                                                    <div class="col-md-6 box">
                                                        <div style="display:flex;">
                                                            <span style="font-weight: bold;min-width:130px">Invoice No</span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span style="margin-left:1%"> {{$tran_ledger->invoice_no}}</span>
                                                        </div style="display:flex;">
                                                        <div style="display: flex;">
                                                            <span style="min-width:130px"></span>
                                                            {{-- <span style="margin-right: 3px ">: </span> --}}
                                                            <span style="margin-left:1%;margin-top:2%"><svg id="barcode"></svg></span>
                                                        </div>

                                                        <div style="display:flex;">
                                                            <span style="min-width:130px">Ref No </span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span style="margin-left:1%"> {{$tran_ledger->ref_no}}</span>
                                                        </div>

                                                        <div style="display:flex;">
                                                            <span style="min-width:130px">Date </span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span style="margin-left:1%"> {{date('d M, Y', strtotime($tran_ledger->transaction_date))}}</span>
                                                        </div>

                                                    </div>


                                                    <div class="col-md-6 box" style="border-left: 1px solid black;">
                                                        <div style="display:flex;">
                                                            <span style="min-width:160px">Party Code</span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span style="margin-left:1%"> {{$tran_ledger->alias }}</span>
                                                        </div>
                                                        <div style="display:flex;">
                                                            <span style="min-width:160px">Party Name</span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span style="margin-left:1%"> {{$tran_ledger->ledger_name }}</span>
                                                        </div>
                                                        <div style="display:flex;">

                                                            <spans style="min-width:160px">Address </spans>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span style="margin-left:1% "> {{$tran_ledger->mailing_add}}</span>
                                                        </div>
                                                        <div style="display:flex;">
                                                            <span style="min-width:160px">Contact</span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span style="margin-left:1%"> {{$tran_ledger->mobile}}</span>
                                                        </div>
                                                        <div style="display:flex;">
                                                            <span style="min-width:160px">NID</span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span style="margin-left:1%"> {{$tran_ledger->national_id}}</span>
                                                        </div>
                                                    </div>
                                                   
                                                </div>
                                                <div class="row col-md-10  m-1">
                                                    <div class="col-md-12 box">
                                                        <div style="display:flex;">
                                                            <span style="min-width:128px">Date between:</span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span >{{$tran_ledger->commission_from_date??''}} </span><span style="margin-left:1%;margin-right:1%;">to</span> <span>{{$tran_ledger->commission_to_date??''}} </span>
                                                            <input type="hidden" class="from_date" name="from_date"  value="{{$tran_ledger->commission_from_date??''}}" >
                                                            <input type="hidden" class="to_date" name="to_date"  value="{{$tran_ledger->commission_to_date??''}}">
                                                            <input type="hidden" class="ledger_head_id" name="ledger_id"  value="{{$tran_ledger->ledger_head_id??0}}">
                                                        </div>
                                                
                                                        <div style="display:flex;">
                                                            <span style="min-width:128px">Note </span>
                                                            <span style="margin-right: 3px ">: </span>
                                                            <span style="margin-left:1%">{{$tran_ledger->narration}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="dt-responsive table-responsive cell-border sd ">
                                                    <table  id="example"  style=" border-collapse: collapse; "   class="table table-striped customers " >
                                                        <thead >
                                                             <tr>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;">Serial No</th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;">Particulars</th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;" class="text-end td-bold">Sales<br>Quantity</th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;" class="text-end td-bold">Sales<br>Eff. Rate</th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;" class="text-end td-bold">Sales<br>Value</th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;" >Commission<br>[Per Quantity]</th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;" >Commission<br>[% of Sales Value]</th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;" >Total Commission</th>
                                                             </tr>
                                                         </thead>
                                                         <tbody id="orders">
                                                         </tbody>
                                                         <tfoot>
                                                             <tr>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;"></th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;"class=" td1 text-end td-bold">Total :</th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;" style="font-weight: bold;" class="sale_qty td1 text-end td-bold"></th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;" style="font-weight: bold;" class="sale_rate td1 text-end td-bold"></th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;" style="font-weight: bold;" class="sale_value td1 text-end td-bold"></th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;" style="font-weight: bold;" class="commission_per_qty"></th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;" style="font-weight: bold;"class="commission_per_value"></th>
                                                                 <th style="width: 3%;  border: 1px solid #ddd;" class="text-end td-bold total_commission td1" >
                                                                     
                                                                 </th>
                                                             </tr>
                                                         <tfoot>
                                                     </table>
                                                    <div class="row mt-5 received_by" style="display:flex;">
                                                        @if ($access_report->id_report==1)
                                                            <div class="col-sm-6" >
                                                                <h5  style="text-align:left ;min-width:500px;">Received By : . . . . . . . . . . . . . . . . </h5>
                                                            </div>
                                                            <div class="col-sm-6" >
                                                                <h5  style="text-align:left ;min-width:500px;">Received For :. . . . . . . . . . . . . . . .</h5>
                                                            </div>
                                                        @elseif ($access_report->id_report==51)
                                                            <div class="col-sm-3" >
                                                                <h5  style="text-align:left ;min-width:250px;">Prepared By : . . . . . . . . . . </h5>
                                                            </div>
                                                            <div class="col-sm-3" >
                                                                <h5  style="text-align:left ;min-width:250px;">Received For :. . . . . . . . . .</h5>
                                                            </div>
                                                            <div class="col-sm-3" >
                                                                <h5  style="text-align:left ;min-width:250px;">Checked BY : . . . . . . . . . . .</h5>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <h5  style="text-align:right;min-width:250px;">Authorized BY : . . . . . . . . . .</h5>
                                                            </div>
                                                        @elseif ($access_report->id_report==151)
                                                            <div class="col-sm-3" >
                                                                <h5  style="text-align:left ;min-width:240px;">Prepared By : . . . . . . .</h5>
                                                            </div>
                                                            <div class="col-sm-3" >
                                                                <h5  style="text-align:left ;min-width:240px;">Checked BY : . . . . . . . .</h5>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <h5  style="text-align:left;min-width:240px;">Authorized BY : . . . . . . .</h5>
                                                            </div>
                                                            <div class="col-sm-3" >
                                                                <h5  style="text-align:left ;min-width:240px;">Customer Signature:. . . . .</h5>
                                                            </div>
                                                        @else
                                                        <div class="col-sm-6" >
                                                            <h5  style="text-align:left ;min-width:500px;">Received By : . . . . . . . . . . . . . . . . </h5>
                                                        </div>
                                                        <div class="col-sm-6" >
                                                            <h5  style="text-align:left ;min-width:500px;">Received For :. . . . . . . . . . . . . . . .</h5>
                                                        </div>
                                                        @endif
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
                let  total_qty=0,total_value=0,i=1,com_tatal_val=0;
                const  amount_decimals="{{company()->amount_decimals}}";
               
                // day book initial show
                function get_stock_item_commission_show() {
                    print_date();
                    $(".modal").show();
                    let tran_id = '{{$tran_ledger->tran_id??0}}';
                    $.ajax({
                        url: "{{ url('stock-itam-commission-data') }}" + '/' + tran_id ,
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            to_date: $('.to_date').val(),
                            from_date: $('.from_date').val(),
                            ledger_head_id: $('.ledger_head_id').val()
                        },
                        
                        success: function(response) {
                            
                            $(".modal").hide();
                            get_commission_voucher(response.data)
                        },
                        error: function(data, status, xhr) {
                            Unauthorized(data.status);
                        }
                    })
                }
                get_stock_item_commission_show();
                function get_commission_voucher(response) {

                    var tree = getTreeView(response.commission_ledger_voucher,response.sum_of_children);
                    console.log(tree);
                    $('#orders').html(tree);
                    get_hover();
                    $('.sale_qty').text(MakeCurrency(total_qty));
                    $('.sale_rate').text(MakeCurrency(((total_value || 0) / (total_qty|| 0)) || 0));
                    $('.sale_value').text(MakeCurrency(total_value));
                    $('.total_commission').text(MakeCurrency(com_tatal_val));

                    }
                    function getTreeView(arr, children_sum, depth = 0, chart_id = 0) {
                        let html = [];
                        arr.forEach(function(v) {
                            a = '&nbsp;';
                            h = a.repeat(depth);

                            if (chart_id != v.stock_group_id) {
                                let matchingChild = children_sum.find(c => v.stock_group_id == c.stock_group_id);
                                if (((matchingChild.stock_qty || 0) == 0)) {} else {
                                    html.push(`<tr id="${v.stock_group_id+'-'+v.under}" class="left left-data table-row_tree">
                                            <td style='width: 1%;  border: 1px solid #ddd;'></td>
                                            <td style='width: 3%;' class="td1 td-bold"><p style="margin-left:${(h+a+a).length-12}px;cursor: default !important; font-size: 18px; font-family: Arial, sans-serif" class="text-wrap mb-0 pb-0 ">${v.stock_group_name}</p></td>`);


                                    if (matchingChild) {

                                        html.push(`<td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">
                                                    ${MakeCurrency(matchingChild.stock_qty)}
                                                </td>
                                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">
                                                ${MakeCurrency(dividevalue(matchingChild.stock_total,matchingChild.stock_qty))}
                                            </td>
                                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold">${MakeCurrency(matchingChild.stock_total)}</td>
                                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold"></td>
                                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold"></td>
                                            <td style='width: 3%;cursor: default !important;'class="td1 text-end td-bold"></td>

                                            `);
                                    }
                                    html.push(`</tr>`);
                                }
                                chart_id = v.stock_group_id;
                            }

                            if (v.qty != null&&v.com_rate!= null) {
                                total_qty += (v.qty || 0);
                                total_value += (v.total||0);
                                com_tatal_val+=(v.com_total||0);
                                let par_rate=Math.abs(dividevalue(v?.total,v?.qty))

                                html.push(`<tr id="${v.stock_item_id}" class="left left-data editIcon table-row">
                                        <td class="sl" style="width: 1%;  border: 1px solid #ddd;">${i++}</td>
                                        <td style="width: 5%;'" class="td2 item_name"><p style="margin-left :${(h+a+a+a).length-12}px; font-family: Arial, sans-serif" class="text-wrap mb-0 pb-0">${v.product_name}</p></td>
                                        <td style='width: 2%;'class="td2 text-end">
                                                ${MakeCurrency(v?.qty)}
                                               
                                            </td>
                                        <td style='width: 2%;'class="td2 text-end">
                                                ${MakeCurrency(par_rate)}
                                            </td>
                                        <td style='width: 3%;'class="td2 text-end">
                                                ${MakeCurrency(v.total)}
                                            </td>
                                           <td  style="width: 3%;  border: 1px solid #ddd;" class="td2 text-end">${MakeCurrency(v.com_rate)}</td>
                                            <td  class="td2 text-end"  style="width: 3%;  border: 1px solid #ddd;">
                                                ${MakeCurrency(v.com_percent)}
                                            </td>
                                            <td  class="td2 text-end"  style="width: 3%;  border: 1px solid #ddd;">
                                                ${MakeCurrency(v.com_total)}
                                            </td>

                                </tr>`);
                            }

                            if ('children' in v) {
                                html.push(getTreeView(v.children, children_sum, depth + 1, chart_id));
                            }
                        });

                        return html.join("");
                    }

            </script>
            @endpush
            @endsection

