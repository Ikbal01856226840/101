@extends('layouts.backend.app')
@section('title','Retailer Sales Analysis')
@push('css')
<style>
    .th {
        border: 1px solid #ddd;
    }

    body {
        overflow: auto !important;
    }

    .drcr {
        font-family: Arial, sans-serif;
        font-weight: 500;
    }

    .card .card-block p {
        line-height: 18px !important;
    }
</style>
@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
'id' =>'exampleModal',
'class' =>'modal fade',
'size'=>'modal-xl',
'page_title'=>'Retailer Sales Analysis',
'page_unique_id'=>45,
'ledger'=>'yes',
'accounts'=>"Yes",
'title'=>'Retrailer',
'daynamic_function'=>'get_retrailer_ledger_all_initial_show',
])
@endcomponent
<!-- add component-->
@component('components.report', [
'title' => 'Retailer Sales Analysis',
'print_layout'=>'portrait',
'print_header'=>'Retailer Sales Analysis',
'user_privilege_title'=>'DealerAndRetrailerSalesQuantity',
'print_date'=>1,
'party_name'=>1,
'report_setting_model'=>'report_setting_model',
'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
<form id="retrailer_ledger_details_form" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row ">
        <div class="col-md-6">
            <label>Party Name : </label>
            <select name="ledger_id" class="form-control js-example-basic-single ledger_id" required>
                <option value="">--select--</option>
                {!!html_entity_decode($ledgers)!!}
            </select>
            <div class="form-group ">
                <label class="fs-6">Ledger type :</label>
                <input class="form-check-input ledger_type" type="radio" name="ledger_type" value="1" checked="checked">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Both
                </label>
                <input class="form-check-input ledger_type" type="radio" name="ledger_type" value="2">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Dealer
                </label>
                <input class="form-check-input ledger_type" type="radio" name="ledger_type" value="3">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Retrailer
                </label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="row  m-0 p-1">
                <div class="col-md-6 m-0 p-0">
                    <label>Date From: </label>
                    <input type="text" name="from_date" class="form-control from_date setup_date fs-5" value="{{financial_end_date(date('Y-m-d'))}}"   >
                </div>
                <div class="col-md-6 m-0 p-0">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control to_date setup_date fs-5" value="{{financial_end_date(date('Y-m-d'))}}" >
                </div>
            </div>
            
        </div>
        <div class="col-md-2 ">
             <label></label><br>
            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit m-2" style=" width:200px; margin-bottom:5px;"><span class="m-1 m-t-1"></span><span>Search</span></button>
        </div>
    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_report_party_ledger">
    <div class="dt-responsive table-responsive cell-border sd tableFixHead_report">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <tr>
                <th style="width: 1%;  overflow: hidden;" class="th ">SL.</th>
                <th style="width: 2%;  overflow: hidden;" class="th ">Ledger Type</th>
                <th style="width: 2%;  overflow: hidden;" class="th ">Particulars</th>
                <th style="width: 2%;  overflow: hidden;" class="th text-end">Sales<br> Quantity</th>
                <th style="width: 2%;  overflow: hidden;" class="th text-end">Sales <br>Return<br> Quantity</th>
                <th style="width: 2%;  overflow: hidden;" class="th text-end">Actua <br>Sales<br> Quantity</th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;" class="th"></th>
                <th style="width: 3%;" class="th text-end td-bold"></th>
                <th style="width: 2%; font-size: 18px;" class=" th text-end td-bold">Total :</th>
                <th style="width: 2%; font-size: 18px;" class="total_salese th text-end td-bold"></th>
                 <th style="width: 2%; font-size: 18px;" class="total_sales_return th text-end td-bold"></th>
                  <th style="width: 2%; font-size: 18px;" class="total_actual_sales th text-end td-bold"></th>
            </tr>
        </tfoot>
    </table>
    <div class="col-sm-12 text-center footer_class">
    </div>
</div>
@endslot
@endcomponent
<br>
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>
    
    let total_salese = 0,total_salese_return = 0,total_actual_sales = 0,total_actual_sales_retrailer=0;
    $(document).ready(function() {
        
        local_store_retrailer_ledger_details_get();
        get_retrailer_ledger_all_initial_show();

        $("#retrailer_ledger_details_form").submit(function(e) {
            local_store_retrailer_ledger_details_set_data();
            e.preventDefault();
            print_date();
            let ledger_name = $('.ledger_id').find('option:selected').text();
            localStorage.setItem('ledger_name', '');
            localStorage.setItem('ledger_name', ledger_name);
            const fd = new FormData(this);
            total_salese = 0,total_salese_return = 0,total_actual_sales = 0, total_actual_sales_retrailer=0;
            $(".modal").show();
            $.ajax({
                url: '{{ route("dealer-retrailer-sales-quantity-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    $(".modal").hide();
                     get_current_retrailer_ledger_val(response)
                },
                error: function(data, status, xhr) {

                }
            });
        });
    });


    function get_current_retrailer_ledger_val(response) {
        let html = [];
      
        // ledger array
        $.each(response.data, function(key, v) {
                total_salese +=(v.sales_qty ||0);
                total_salese_return +=(Math.abs(v.sales_return_qty) ||0);
                if(v.ledger_type==2){
                  total_actual_sales += (Math.abs(v.sales_qty - v.sales_return_qty) ||0);
                }else if(v.ledger_type==1){
                  total_actual_sales_retrailer += (Math.abs(v.sales_qty - v.sales_return_qty) ||0);
                }
               
            html.push( `<tr  class="left left-data editIcon table-row ${v.ledger_type==2?'table-info':'table-primary'}">
                        <td class="th"  style="width: 1%;">${(key + 1)}</td>
                        <td  class="th" style="width: 1%; font-size: 16px;text-wrap: wrap;">${v.ledger_type==2?'Retrailer':'Dealer'}</td>
                        <td  class="th" style="width: 1%; font-size: 16px;text-wrap: wrap;">${v.ledger_type==1?(v.ledger_name||''):(v.alias||'')}</td>
                        <td  class="th text-end" style="width: 1%; font-size: 16px;" >${(v.sales_qty ||0).formatBangladeshCurrencyType("quantity")}</td>
                        <td  class="th text-end" style="width: 1%; font-size: 16px;">${(Math.abs(v.sales_return_qty) ||0).formatBangladeshCurrencyType("quantity")}</td>
                        <td  class="th text-end" style="width: 1%; font-size: 16px;">${(Math.abs(v.sales_qty - v.sales_return_qty) ||0).formatBangladeshCurrencyType("quantity")}</td>
                        <
                    `);
        });
        $(".item_body").html(html.join(''));
        $('.total_salese').text(total_salese.formatBangladeshCurrencyType("quantity"));
        $('.total_sales_return').text(total_salese_return.formatBangladeshCurrencyType("quantity"));
       $('.total_actual_sales').html(
           'Dealer: ' + total_actual_sales.formatBangladeshCurrencyType("quantity") +
            (total_actual_sales_retrailer > 0 
                ? '<br>Retailer: ' + total_actual_sales_retrailer.formatBangladeshCurrencyType("quantity") 
                : ''
            )
       );

        get_hover();
    }

    function get_retrailer_ledger_all_initial_show() {
         total_salese = 0,total_salese_return = 0,total_actual_sales = 0, total_actual_sales_retrailer=0;
        // $(".modal").show();
        $.ajax({
            url: '{{ route("dealer-retrailer-sales-quantity-data") }}',
            method: 'GET',
            data: {
                to_date: $('.to_date').val(),
                from_date: $('.from_date').val(),
                ledger_id: $(".ledger_id").val(),
                ledger_type:$(".ledger_type:checked").val(),
            },
            dataType: 'json',
            success: function(response) {
                $(".modal").hide();
                get_current_retrailer_ledger_val(response);
            },
            error: function(data, status, xhr) {}
        });

    }

    function local_store_retrailer_ledger_details_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("ledger_id", '.ledger_id');
        let ledger_type = getStorage("ledger_type");
        $(".ledger_type[value='" + ledger_type + "']").prop("checked", true);
    }

    function local_store_retrailer_ledger_details_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("ledger_id", $('.ledger_id').val());
        setStorage("ledger_type", $(".ledger_type:checked").val());
    }

</script>
@endpush
@endsection
