@extends('layouts.backend.app')
@section('title','Search, Analysis and Filter Vouchers')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
<style>
    input[type=radio] {
        width: 20px;
        height: 20px;
    }

    input[type=checkbox] {
        width: 20px;
        height: 20px;
    }

    table {
        width: 100%;
        grid-template-columns: auto auto;
    }

    .td {
        border: 1px solid #ddd;
    }
  
    body{
        overflow: auto !important;
    }
</style>
@endpush
@section('admin_content')<br>
<!-- add component-->
@component('components.report', [
    'title' => 'Search, Analysis and Filter Vouchers',
    'print_layout'=>'landscape',
    'print_header'=>'Search, Analysis and Filter Vouchers',
    'user_privilege_title'=>'Bill',
]);

<!-- Page-header component -->
@slot('header_body')
<form id="add_voucher_filter_form" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row">
        <div class="col-md-2">
            <label>Voucher Type : </label>
            <select name="voucher_id" class="form-control js-example-basic-single voucher_id">
                <option value="0">--ALL--</option>
                @php $voucher_type_id= 0; @endphp
                @foreach ($vouchers as $voucher)
                @if($voucher_type_id!=$voucher->voucher_type_id)
                @php $voucher_type_id=$voucher->voucher_type_id; @endphp
                <option style="color:red;" value="v{{$voucher->voucher_type_id??''}}">{{$voucher->voucher_type??''}}</option>
                @endif
                <option value="{{$voucher->voucher_id}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$voucher->voucher_name}}</option>

                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <div class="row  m-0 p-0 ">
                <div class="col-md-6 m-0 p-0 start_date">
                    <label>Date From: </label>
                    <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{financial_end_date(date('Y-m-d'))}}" name="narratiaon">
                </div>
                <div class="col-md-6 m-0 p-0 end_date">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{financial_end_date(date('Y-m-d'))}}" name="narratiaon">
                </div>
            </div>
        </div>
        <div class="col-md-2 mt-3">

            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:200px; margin-bottom:5px;"><span class="m-1 m-t-1"></span><span>Search</span></button>
        </div>

    </div>
</form>
<form id="unique_voucher_filter_form" method="POST">
    <input type="hidden" name="from_date" class="form-control setup_date fs-5 from_date_new" >
    <input type="hidden" name="to_date" class="form-control setup_date fs-5 to_date_new">
    <input type="hidden" name="voucher_id" class="form-control setup_date fs-5 voucher_id_new">
    <div style="border: 2px black;display: none;" class="show">
        @csrf
        <div class="col-md-12" style="display:flex;">
            <label> Date :</label>
            <div class="date">
            </div>
        </div>
        <div class="col-md-12" style="display:flex;">
            <label> Rate :</label>
            <div class="rate">
            </div>
        </div>
        <div class="col-md-12" style="display:flex;">
            <label> Ledger [Dr] :</label>
            <div class="ledger_debit">
            </div>

        </div>
        <div class="col-md-12" style="display:flex;">
            <label> Ledger [Cr] : </label>
            <div class="ledger_credit">
            </div>
        </div>
    </div>
    <div class="col-md-2 mt-1">

        <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:200px; margin-bottom:5px;"><span class="m-1 m-t-1"></span><span>Search</span></button>
    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_voucher_filter">
    <table id="tableId" style=" border-collapse: collapse;" class="table table-striped customers ">
        <thead>
            <tr>
                <th style="width: 1%;  border: 1px solid #ddd;font-weight: bold;">SL.</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Date</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Party Name [Dr]</th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;">Party Name [Cr]</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Voucher Type</th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;">Voucher No</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Product Name</th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;">Qty [in]</th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;">Qty [out]</th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;">Unit</th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;">Rate </th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;">Amount</th>

            </tr>
        </thead>
        <tbody id="myTable" class="voucher_filter_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;"></th>
                <th style="width: 3%;"></th>
                <th style="width: 3%;"></th>
                <th style="width: 2%;"></th>
                <th style="width: 3%;"></th>
                <th style="width: 2%;"></th>
                <th style="width: 3%;"></th>
                <th style="width: 2%; font-size: 18px;" class="in_qty"></th>
                <th style="width: 2%; font-size:18px;" class="out_qty"></th>
                <th style="width: 2%;"></th>
                <th style="width: 2%;"></th>
                <th style="width: 2%; font-size: 18px;" class="total_amount"></th>

            </tr>
        </tfoot>
    </table>
</div>
@endslot
@endcomponent
<br>
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>
    $(document).ready(function(){
    // table header fixed
   // let display_height=$(window).height();
    //$('.tableFixHead_voucher_filter').css('height',`${display_height-400}px`);
});
    var amount_decimals = "{{company()->amount_decimals}}";
    let qty_in = 0;
    qty_out = 0;
    total_amount = 0;
    unique_date = '', unique_rate = 0, unique_debit = 0, unique_credit = 0; i=1;
    let  unique_rateo=[];
    let unique_date_arr=[];
    let unique_debit_arr=[];
    let unique_credit_arr=[];
    $(document).ready(function() {
        // getvoucher_filter_analysis
        function get_voucher_filter_analysis_initial_show() {
                unique_rateo=[];
                unique_date_arr=[];
                unique_debit_arr=[];
                unique_credit_arr=[];
            $.ajax({
                url: '{{route("report-voucher-filter-data") }}',
                method: 'GET',
                dataType: 'json',
                data: {
                    to_date: $('.to_date').val(),
                    from_date: $('.from_date').val(),
                    voucher_id: $('.voucher_id').val()
                },
                success: function(response) {

                    get_voucher_filter_analysis_val(response)
                },
                error: function(data, status, xhr) {}
            });
        }

        get_voucher_filter_analysis_initial_show();

        // voucher_filter
        $("#add_voucher_filter_form").submit(function(e) {
            unique_rateo=[];
            unique_date_arr=[];
            unique_debit_arr=[];
            unique_credit_arr=[];
            qty_in = 0;
            qty_out = 0;
            total_amount = 0;
            unique_date = '', unique_rate = 0, unique_debit = 0, unique_credit = 0;  i=1;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("report-voucher-filter-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    get_voucher_filter_analysis_val(response)
                },
                error: function(data, status, xhr) {}
            });
        });

        $("#unique_voucher_filter_form").submit(function(e) {
            unique_rateo=[];
            unique_date_arr=[];
            unique_debit_arr=[];
            unique_credit_arr=[];
            qty_in = 0;
            qty_out = 0;
            total_amount = 0;
            unique_date = '', unique_rate = 0, unique_debit = 0, unique_credit = 0;
            $('.voucher_id_new').val($('.voucher_id').val());
            $('.to_date_new').val($('.to_date').val());
            $('.from_date_new').val($('.from_date').val());
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("report-voucher-filter-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    get_voucher_filter_analysis_val(response)
                },
                error: function(data, status, xhr) {}
            });
        });
        
        function get_voucher_filter_analysis_val(response) {
            
                let htmlFragments = [];
                htmldate = [];
                htmlrate = [];
                htmldebit = [];
                htmlcredit = [];
                
                response.data.data.forEach((v,key) => {
                  
                    qty_in += v.stock_in_qty || 0;
                    qty_out += v.stock_out_qty || 0;
                    amount = ((v.stock_in_rate || (v.stock_out_rate || 0)) * (v.stock_in_qty || (v.stock_out_qty || 0)));
                    total_amount += ((v.stock_in_rate || (v.stock_out_rate || 0)) * (v.stock_in_qty || (v.stock_out_qty || 0)));
                   
                        // date filter
                        let date_check
                        
                        if (v.transaction_date != unique_date) {
                            $(".show").css("display", "block");
                            unique_date_arr.push((v.transaction_date||0));
                            unique_date = v.transaction_date;
                            if (response.data.request_date.length != 0) {
                                date_check = response.data.request_date.find((element) => element = v.transaction_date);
                            }
                            if(getOccurrence(unique_date_arr,v.transaction_date)==1){
                            htmldate.push(`<input class="form-check-input myCheckbox" type="checkbox"  name="date[]" ${(date_check?"checked":'')}  value="${v.transaction_date}" >
                                            <label class="form-check-label fs-6" for="flexRadioDefault1" >
                                                ${join( new Date(v.transaction_date), options, ' ')}
                                            </label>`);
                            }

                        }

                        // rate filter
                        let rate_check;
                        if(((v.stock_out_rate||0))!=unique_rate){

                            if(response.data.request_rate.length!=0){
                            rate_check= response.data.request_rate.find((element) => element=(v.stock_in_rate||(v.stock_out_rate||0)));
                            }
                           
                               unique_rateo.push((v.stock_out_rate||0));
                              
                            if(getOccurrence(unique_rateo, v.stock_out_rate)==1){
                                
                                    htmlrate.push(`<input class="form-check-input" type="checkbox"  name="rate[]"  ${(rate_check?"checked":'')} value="${(v.stock_in_rate||(v.stock_out_rate||0))}" >
                                                    <label class="form-check-label fs-6" for="flexRadioDefault1" >
                                                        ${(v.stock_in_rate||(v.stock_out_rate||0))}
                                                    </label>`);
                                unique_rate=(v.stock_out_rate||0);
                                 
                            }

                        }

                        // debit filter
                        let debit_check;
                        if (v.debit_ledger_name != unique_debit) {
                            unique_debit_arr.push((v.debit_ledger_name||0));
                            unique_debit = v.debit_ledger_name;
                            if (response.data.request_debit.length != 0) {

                                debit_check = response.data.request_debit.find((element) => element = (v.stock_in_rate || (v.stock_out_rate || 0)));
                            }
                            if(getOccurrence(unique_debit_arr,v.debit_ledger_name)==1){
                            htmldebit.push(`<input class="form-check-input" type="checkbox"  name="debit[]" ${(debit_check?"checked":'')} value="${v.debit_ledger_name||''}" >
                                            <label class="form-check-label fs-6" for="flexRadioDefault1" >
                                                ${v.debit_ledger_name||''}
                                            </label>`);
                            }
                        }

                        // credit filter
                        let credit_check;
                        if (v.credit_ledger_name != unique_credit) {
                            unique_credit = v.credit_ledger_name;
                            unique_credit_arr.push((v.credit_ledger_name||0));
                            if (response.data.request_credit.length != 0) {

                                credit_check = response.data.request_credit.find((element) => element = (v.stock_in_rate || (v.stock_out_rate || 0)));
                            }
                            if(getOccurrence(unique_credit_arr,v.credit_ledger_name)==1){
                            htmlcredit.push(`<input class="form-check-input" type="checkbox"  name="credit[]"  ${(credit_check?"checked":'')} value="${v.credit_ledger_name}" >
                                            <label class="form-check-label fs-6" for="flexRadioDefault1" >
                                                ${v.credit_ledger_name||''}
                                            </label>`);
                            }

                        }

                        htmlFragments.push(`<tr class="left left-data editIcon table-row">
                            <td style="width: 1%;">${key+1}</td>
                            <td style="width: 3%;" class="td font text-wrap">${join( new Date(v.transaction_date), options, ' ')}</td>
                            <td style="width: 3%;"class="td font text-wrap" >${v.debit_ledger_name||''}</td>
                            <td style="width: 3%;"class="td font text-wrap" >${v.credit_ledger_name||''}</td>
                            <td style="width: 3%;"class="td font text-wrap">${v.voucher_name||''}</td>
                            <td style="width: 3%;"class="td font text-wrap">${v.invoice_no||''}</td>
                            <td style="width: 3%;"class="td font text-wrap">${v.in_product_name||(v.out_product_name||0)}</td>
                            <td style="width: 3%;"class="td">${(v.stock_in_qty||0).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                            <td style="width: 3%;"class="td" >${(v.stock_out_qty||0).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                            <td style="width: 3%;"class="td" >${v.symbol_in||(v.symbol_out||0)}</td>
                            <td style="width: 3%;"class="td" >${(v.stock_in_rate||(v.stock_out_rate||0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                            <td style="width: 3%;"class="td">${amount.toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>

                        </tr>`);

                });

                $(".voucher_filter_body").html(htmlFragments.join(''));
                $(".date").html(htmldate.join(''));
                $(".rate").html(htmlrate.join(''));
                $(".ledger_debit").html(htmldebit.join(''));
                $(".ledger_credit").html(htmlcredit.join(''));
                $('.in_qty').text((qty_in || 0).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                $('.out_qty').text((qty_out || 0).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                $('.total_amount').text((total_amount || 0).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                get_hover();

    
        }


    });
    //get  all data show
    $(document).ready(function() {
        $('.sd').on('click', '.customers tbody tr ', function(e) {
            localStorage.setItem("end_date", $('.to_date').val());
            localStorage.setItem("start_date", $('.from_date').val());
            localStorage.setItem("voucher_id", $('.voucher_id').val());
            e.preventDefault();
            let day_book_arr = $(this).closest('tr').attr('id').split(",");
            if (day_book_arr[1] == 14) {
                window.open(`{{url('voucher-receipt/edit')}}/${day_book_arr[0]}`, '_blank');
            } else if (day_book_arr[1] == 8) {
                window.open(`{{url('voucher-payment')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 1) {
                window.open(`{{url('voucher-contra')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 10) {
                window.open(`{{url('voucher-purchase')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 24) {
                window.open(`{{url('voucher-grn')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 19) {
                window.open(`{{url('voucher-sales')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 23) {
                window.open(`{{url('voucher-gtn')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 29) {
                window.open(`{{url('voucher-purchase-return')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 22) {
                window.open(`{{url('voucher-transfer')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 25) {
                window.open(`{{url('voucher-sales-return')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 21) {
                window.open(`{{url('voucher-stock-journal')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 6) {
                window.open(`{{url('voucher-journal')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 28) {
                window.open(`{{url('voucher-commission')}}/${day_book_arr[0]}/edit`, '_blank');
            } else if (day_book_arr[1] == 20) {
                window.open(`{{url('voucher-sales-order')}}/${day_book_arr[0]}/edit`, '_blank');
            }
        })
    });
    function getOccurrence(array, value) {
        return array.filter((v) => (v === value)).length;
  }
</script>
@endpush
@endsection