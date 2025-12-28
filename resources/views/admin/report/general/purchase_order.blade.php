@extends('layouts.backend.app')
@section('title','Purchase Order')
@push('css')
<!-- model style -->
<link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
<style>
    .td {
        border: 1px solid #ddd;
    }
    .font {
        font-size: 16px;
    }
    body {
        overflow: auto !important;
    }
</style>

@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
'id' =>'exampleModal',
'class' =>'modal fade',
'page_title'=>'Purchase Order',
'size'=>'modal-xl',
'page_unique_id'=>35,
'godown'=>'yes',
'title'=>'Purchase Order Reports',
'daynamic_function'=>'get_invoice_summary_initial_show'
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Purchase Order',
    'print_layout'=>'portrait',
    'print_header'=>'Purchase Order',
    'user_privilege_title'=>'PurchaseOrder',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
<form id="add_day_book_form" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row">
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
        <div class="col-md-2">
            <br>
            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:200px; margin-bottom:5px;"><span class="m-1 m-t-1"></span><span>Search</span></button>
        </div>
        <div class="col-md-2">
            <br>
            <button  class="btn hor-grd btn-grd-primary btn-block purchase_order_register" style="width:250px; margin-bottom:5px;" >Purchase Order Register</button>
        </div>

    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_invoice_summary_report ">
</div>
@endslot
@endcomponent
<br>
@push('js')
<script>
    let total_qty=0;total_amount=0;
    $(document).ready(function() {
        // day book initial show
        function get_invoice_summary_initial_show() {
            print_date();
            $(".modal").show();
            $.ajax({
                url: "{{ route('report-purchase-order-data')}}",
                type: 'GET',
                dataType: 'json',
                data: {
                    to_date: $('.to_date').val(),
                    from_date: $('.from_date').val(),
                },
                success: function(response) {
                    $(".modal").hide();
                    get_invoice_summary_val(response)
                }
            })
        }

        // day book  show
        $("#add_day_book_form").submit(function(e) {
            total_qty=0;total_amount=0;
            e.preventDefault();
            print_date();
            $(".modal").show();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("report-purchase-order-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    $(".modal").hide();
                    get_invoice_summary_val(response)
                },
                error: function(data) {
                    Unauthorized(data.status);
                }
            });
        });

        get_invoice_summary_initial_show();

        function get_invoice_summary_val(response) {

            const rowsPerPage = 500; // Number of rows to display at a time
            let currentPage = 0; // Track current page
            const totalRows = response.data.length; // Total number of rows

            // Function to render the table header and footer
            function renderTableHeaderFooter() {
                let htmlFragments = [];
                htmlFragments.push(`
                    <table id="tableId" style="border-collapse: collapse;" class="table table-striped customers table-scroll">
                        <thead>
                            <tr>
                                <th style="width: 1%;" class="td">SL.</th>
                                <th style="width: 3%;" class="td">Date</th>
                                <th style="width: 3%;" class="td">Particulars</th>
                                <th style="width: 2%;" class="td">Voucher Type</th>
                                <th style="width: 3%;" class="td">Voucher No</th>
                                <th style="width: 3%;" class="td">Ref No</th>
                                <th style="width: 3%;" class="td">Narration</th>
                                <th style="width: 3%;" class="td">Action</th>
                `);

                // Add optional headers based on checkboxes

                htmlFragments.push(`</tr></thead><tbody id="myTable" class="qw">`);

                $(".sd").html(htmlFragments.join('')); // Render header once
            }

            // Function to render a chunk of data
            function renderTableChunk(startIndex) {
                let htmlFragments = [];
                for (let i = startIndex; i < Math.min(startIndex + rowsPerPage, totalRows); i++) {
                    const v = response.data[i];

                    htmlFragments.push(`
                        <tr id='${v.id}' class="left left-data editIcon table-row">
                            <td style="width: 1%;" class="td">${(i + 1)}</td>
                            <td style="width: 3%;" class="td">${join(new Date(v.date), options, ' ')}</td>
                            <td style="width: 3%;" class="td font text-wrap">${(v.ledger_name || '')}</td>
                            <td style="width: 2%;color:#0B55C4;" class="td" >
                                <input type="hidden" class="voucher_name" value="${v.id}" />${v.voucher_name || ''}
                            </td>
                            <td style="width: 3%;" class="td font ext-wrap">${v.invoice_no|| ''}</td>
                            <td style="width: 3%;" class="td font ext-wrap">${v.reference_no|| ''}</td>

                            <td style="width: 3%;" class="td font text-wrap">${(v.narration || "")}</td>
                            <td style="width: 3%;" class="td">
                                <button class="edit" id='${v.id}'><i class="fa fa-edit" style="font-size:28px;color:#4d9900;"></i></button>
                            </td>
                        </tr>

                    `);



                    htmlFragments.push(`</tr>`);
                }

                $("#myTable").append(htmlFragments.join('')); // Append chunk to the table body

                // Load next chunk if there are more rows
                if (startIndex + rowsPerPage < totalRows) {
                    setTimeout(() => renderTableChunk(startIndex + rowsPerPage), 0); // Use timeout for UI responsiveness
                } else {
                    renderTableFooter(); // Render footer once all rows are loaded
                }
            }

            // Function to render the footer
            function renderTableFooter() {
                let footerFragments = [];
                footerFragments.push(`<tfoot><tr>`);
                footerFragments.push(`
                    <th style="width: 1%;" class="td">SL.</th>
                    <th style="width: 3%;" class="td">Date</th>
                    <th style="width: 3%;" class="td">Particulars</th>
                    <th style="width: 2%;" class="td">Voucher Type</th>
                    <th style="width: 3%;" class="td">Voucher No</th>
                    <th style="width: 3%;" class="td">Ref No</th>
                    <th style="width: 3%;" class="td">Narration</th>
                    <th style="width: 3%;" class="td">Action</th>

                `);




                footerFragments.push(`</tr></tfoot>`);
                $("#tableId").append(footerFragments.join('')); // Append footer to the table
            }

            // Initial rendering
            renderTableHeaderFooter();
            renderTableChunk(currentPage * rowsPerPage);
       }

    });

    //get  all data show
    $(document).ready(function () {
        $('.sd').on('click','td',function(e){
            e.preventDefault();
            let   id=$(this).find('.voucher_name').val();
            if(id){
                window.open(`{{url('show-purchase-order')}}/${id}`, '_blank');
            }


        })

    });

    //redirect route
    $(document).ready(function() {
        $('.sd').on('click', 'td', function(e) {
            e.preventDefault();
            if($(this).find('.edit').attr('id')){
                let day_book_arr = $(this).closest('tr').attr('id').split(",");
                window.open(`{{url('voucher-order-requisition')}}/${day_book_arr[0]}/edit`, '_blank');
            }
        });
        let display_height=$(window).height();
       $('.tableFixHead_invoice_summary_report').css('height',`${display_height-120}px`);
    });
    
     //stock  voucher register
   $(document).on('click', '.purchase_order_register', function(e) {
        e.preventDefault();
        let form_date = $('.from_date').val();
        let to_date = $('.to_date').val();
        let url = "{{ route('report-purchase-order-register', ['form_date' => ':form_date', 'to_date' => ':to_date']) }}";
        url = url.replace(':form_date', form_date);
        url = url.replace(':to_date', to_date);
        window.open(url, '_blank');
   });
</script>
@endpush
@endsection
