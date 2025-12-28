@extends('layouts.backend.app')
@section('title','Stock Item Daily Summary')
@push('css')
 <style>
 .table-scroll thead tr:nth-child(2) th {
    top: 30px;
}
.th{
    border: 1px solid #ddd;font-weight: bold;
}
.td{
    border: 1px solid #ddd; font-size: 16px;
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
'page_title'=>'Stock Item Daily Summary',
'size'=>'modal-xl',
'page_unique_id'=>15,
'godown'=>'yes',
'stock_item'=>'yes',
'title'=>'Stock Item Daily Summary',
'daynamic_function'=>'get_stock_item_daily_summary_initial_show'
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Stock Item Daily Summary',
    'print_layout'=>'portrait',
    'print_header'=>'Stock Item Daily Summary',
    'user_privilege_title'=>'StockItemDaily',
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
 <form  id="add_stock_item_daily_summary_form"  method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row">
        <div class="col-md-3">
            <label>Stock Item : </label>
            <select name="stock_item_id" class="form-control js-example-basic-single stock_item stock_item_id">
                <option value="0">--ALL--</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>Godown Name :</label>
            <select name="godown_id" class="form-control  js-example-basic-single godown_id" required>
                <option value="0">All</option>
                @foreach($godowns as $godown)
                  <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <div class="row  m-0 p-0 ">
                <div class="col-md-6 m-0 p-0 start_date">
                    <label>Date From: </label>
                        <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{$from_date??financial_end_date(date('Y-m-d'))}}" >
                </div>
                <div class="col-md-6 m-0 p-0 end_date">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{$to_date?? financial_end_date(date('Y-m-d')) }}" >
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <br>
            <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
        </div>
    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_item_register">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <tr>
                <th rowspan="2" style="width: 1%;" class="align-middle">SL.</th>
                <th rowspan="2"style="width: 3%;  border: 1px solid #ddd;" class="align-middle">Date</th>
                <th class="th" colspan="3" style=" width: 5%;text-align:center;"class="inwards">Inward Balance</th>
                <th class="th" colspan="3" style=" width: 5%;text-align:center; "class="outwards">Outward Balance</th>
                <th class="th" colspan="3" style=" width: 5%;text-align:center;"class="clasing">Closing Balance</th>

            </tr>
            <tr>
                <th class="th text-end" style="width: 3%;">Quantity</th>
                <th style="width: 3%;" class="inwards_rate th text-end">Rate</th>
                <th style="width: 5%;" class="inwards_value th text-end">Value</th>
                <th style="width: 2%;" class="th text-end">Quantity</th>
                <th style="width: 2%;" class="outwards_rate th text-end">Rate</th>

                <th style="width: 5%;" class="outwards_value th text-end">Value</th>
                <th style="width: 3%;" class="th text-end">Quantity</th>
                <th style="width: 3%;" class="clasing_rate th text-end">Rate</th>
                <th style="width: 5%;" class="clasing_value th text-end">Value</th>

            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th  style="width: 1%;" class="th"></th>
                <th  style="width: 5%;" class="th">Total :</th>
                <th  style="width: 2%;font-size: 18px;" class="th total_inwards_qty text-end"></th>
                <th  style="width: 2%;font-size: 18px;" class="th inwards_rate total_inwards_rate text-end"></th>
                <th  style="width: 5%;font-size: 18px;" class="th inwards_value total_inwards_value text-end"></th>
                <th  style="width: 2%;font-size: 18px;" class="th total_outwards_qty text-end"></th>
                <th  style="width: 3%;font-size: 18px;" class="th outwards_rate total_outwards_rate text-end"></th>
                <th  style="width: 5%;font-size: 18px;" class="th outwards_value total_outwards_value text-end"></th>
                <th  style="width: 3%;font-size: 18px;" class="th total_clasing_qty text-end"></th>
                <th style="width: 2%;font-size: 18px;"  class="th clasing_rate total_clasing_rate text-end"></th>
                <th  style="width: 5%;font-size: 18px;" class="th clasing_value total_clasing_value text-end"></th>
            </tr>
        </tfoot>
    </table>
</div>
@endslot
@endcomponent
<br>
@push('js')
<script type="text/javascript" src="{{asset('ledger&item_select_option.js')}}"></script>
<script>
//item tree function
get_item_recursive('{{route("stock-item-select-option-tree") }}');
 let  total_inwards_qty=0;total_inwards_value=0; total_outwards_qty=0; total_outwards_value=0;total_clasing_qty=0;total_clasing_rate=0;

// stock item get id check
if("{{$stock_item_id??0}}"!=0){
     $('.stock_item').val("{{$stock_item_id??0}}");
}

// stock item get id check
if("{{$godown_id??0}}"!=0){
     $('.godown_id').val("{{$godown_id??0}}");
}

$(document).ready(function () {

    // stock item get id check
    if("{{$stock_item_id??0}}"!=0){
        local_store_stock_group_daily_summary_set_data();
    }else{
        local_store_stock_group_daily_summary_get();
    }

    get_stock_item_daily_summary_initial_show();


    // add stock item daily summary form
    $("#add_stock_item_daily_summary_form").submit(function(e) {
        local_store_stock_group_daily_summary_set_data();
        print_date();
        e.preventDefault();
        const fd = new FormData(this);
        $.ajax({
            url: '{{ route("stock-item-daily-summary-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    get_stock_item_daily_summary_val(response.data)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
    });
});

 // stock item daily summary
 function get_stock_item_daily_summary_val(response) {
         total_inwards_qty = 0;
         total_inwards_value = 0;
         total_outwards_qty = 0;
         total_outwards_value = 0;
         total_clasing_qty = 0;
         total_clasing_rate= 0;

        const openingStock = response.oppening_stock[0] || { total_stock_total_opening_qty: 0, total_stock_total_out_opening: 0 };
        const openingQty =  openingStock.total_stock_total_opening_qty;
        const openingTotal = openingStock.total_stock_total_out_opening;

        // Create a document fragment
        const fragment = document.createDocumentFragment();

        // Opening Balance Row
        const openingBalanceRow = document.createElement('tr');
        openingBalanceRow.innerHTML = `
            <td style="width: 1%;  border: 1px solid #ddd;"></td>
            <td colspan="2" style="width: 3%;"class="td">Opening Balance</td>
            <td style="width: 3%;"class="td"></td>
            <td style="width: 2%;"class="td"></td>
            <td style="width: 3%;"class="td"></td>
            <td style="width: 3%; "class="td"></td>
            <td style="width: 2%;"class="td"></td>
            <td style="width: 3%;"class="td text-end">${((openingQty||0).formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol))}</td>
            <td style="width: 3%;"class="td text-end">${(dividevalue((openingTotal||0),(openingQty || 0)).formatBangladeshCurrencyType("rate"))}</td>
            <td style="width: 2%;"class="td text-end">${((openingTotal||0).formatBangladeshCurrencyType("amount"))}</td>

        `;
        fragment.appendChild(openingBalanceRow);
        // checking oppening balance
        let op_1=0;
        // Current Stock Rows
        response.current_stock.forEach((v, key) => {
            const inwardsQty = v.inwards_qty || 0;
            const inwardsValue = v.inwards_value || 0;
            const outwardsQty = v.outwards_qty || 0;
            const outwardsValue = v.outwards_value || 0;
            const currentStockRate = parseFloat(v.current_stock_rate || 0);

            total_inwards_qty += inwardsQty;
            total_inwards_value += inwardsValue;
            total_outwards_qty += outwardsQty;
            total_outwards_value += outwardsValue;
            if(op_1==0){
                console.log(openingQty);
                total_clasing_qty +=openingQty+parseFloat((inwardsQty - outwardsQty));
                total_clasing_rate=currentStockRate;
                op_1=1;
            }else{
                total_clasing_qty += parseFloat((inwardsQty - outwardsQty));
                total_clasing_rate=currentStockRate;
            }

            const stockRow = document.createElement('tr');
            stockRow.id = v.transaction_date;
            stockRow.className = 'left left-data editIcon table-row';
            stockRow.innerHTML = `
            <td style="width: 1%;  border: 1px solid #ddd;">${(key + 1)}</td>
            <td class="td" style="width: 3%;color: #0B55C4;" class="text-wrap date">${new Date(v.transaction_date).toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' })}</td>
            <td class="td text-end" style="width: 3%;">${inwardsQty.formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
            <td class="td text-end" style="width: 3%;">${(((inwardsValue/inwardsQty)) || 0).formatBangladeshCurrencyType("rate")}</td>
            <td class="td text-end" style="width: 3%;">${inwardsValue.formatBangladeshCurrencyType("amount")}</td>
            <td class="td text-end" style="width: 3%;">${outwardsQty.formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
            <td class="td text-end" style="width: 2%;">${(((outwardsValue/outwardsQty)) || 0).formatBangladeshCurrencyType("rate")}</td>
            <td class="td text-end" style="width: 3%;">${(outwardsValue.formatBangladeshCurrencyType("amount"))}</td>
            <td class="td text-end" style="width: 3%;">${((total_clasing_qty)).formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
            <td class="td text-end" style="width: 2%;">${(currentStockRate.formatBangladeshCurrencyType("rate"))}</td>
            <td class="td text-end" style="width: 3%; ">${((total_clasing_qty*currentStockRate).formatBangladeshCurrencyType("amount"))}</td>
            `;
            fragment.appendChild(stockRow);
        });
        // Append the fragment to the DOM once
        $(".item_body").empty().append(fragment);
        // Update total values
        $('.total_inwards_qty').text(total_inwards_qty.formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol));
        $('.total_inwards_rate').text(((Math.abs(total_inwards_value) / Math.abs(total_inwards_qty)) || 0).formatBangladeshCurrencyType("rate"));
        $('.total_inwards_value').text(total_inwards_value.formatBangladeshCurrencyType("amount"));
        $('.total_outwards_qty').text(total_outwards_qty.formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol));
        $('.total_outwards_rate').text(((Math.abs(total_outwards_value) / Math.abs(total_outwards_qty)) || 0).formatBangladeshCurrencyType("rate"));
        $('.total_outwards_value').text(total_outwards_value.formatBangladeshCurrencyType("amount"));
        $('.total_clasing_qty').text((parseFloat(total_clasing_qty)).formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol));
        $('.total_clasing_rate').text((total_clasing_rate).formatBangladeshCurrencyType("rate"));
        $('.total_clasing_value').text(((parseFloat(total_clasing_rate*total_clasing_qty))).formatBangladeshCurrencyType("amount"));

        set_scroll_table();
        get_hover();
    }
    function get_stock_item_daily_summary_initial_show(){
        print_date();
        $.ajax({
            url: '{{ route("stock-item-daily-summary-data") }}',
                method: 'GET',
                data: {
                    to_date:$('.to_date').val(),
                    from_date:$('.from_date').val(),
                    stock_item_id:$('.stock_item').val(),
                    godown_id:$('.godown_id').val()
                },
                dataType: 'json',
                success: function(response) {
                    get_stock_item_daily_summary_val(response.data)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
    }
    // local store in bowser
    function local_store_stock_group_daily_summary_get() {
            getStorage("end_date", '.to_date');
            getStorage("start_date", '.from_date');
            getStorage("stock_item", '.stock_item');
            getStorage("godown_id", '.godown_id');
        }

    function local_store_stock_group_daily_summary_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("stock_item", $('.stock_item').val());
        setStorage("godown_id", $('.godown_id').val());
    }

// table header fixed
$(document).ready(function(){
    // table header fixed
    let display_height=$(window).height();
    $('.tableFixHead_item_register').css('height',`${display_height-110}px`);

});

// stock item  wise  voucher  route
$('.sd').on('click','.table-row',function(e){
    e.preventDefault();
    let date=$(this).closest('tr').attr('id');
    let godown_id=$('.godown_id').val();
    let stock_item_id=$('.stock_item').val();
    url = "{{route('stock-item-register-daily-id-wise', ['date' =>':date', 'stock_item_id'=>':stock_item_id','godown_id'=>':godown_id'])}}";
    url = url.replace(':date',date);
    url = url.replace(':stock_item_id',stock_item_id);
    url = url.replace(':godown_id',godown_id);
    window.open(url, '_blank');
});
</script>
@endpush
@endsection
