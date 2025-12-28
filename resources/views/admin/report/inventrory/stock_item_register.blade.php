@extends('layouts.backend.app')
@section('title','Stock Item Register')
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
'page_title'=>'Stock Item Register',
'size'=>'modal-xl',
'page_unique_id'=>16,
'godown'=>'yes',
'stock_item'=>'yes',
'title'=>'Stock Item Register',
'daynamic_function'=>'get_stock_item_register_initial_show'
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Stock Item Register',
    'print_layout'=>'portrait',
    'print_header'=>'Stock Item Register',
    'user_privilege_title'=>'StockItemRegister',
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
 <form  id="add_item_register_form"  method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row">
        <div class="col-md-3">
            <label>Stock Item : </label>
            <input
                type="text"
                class="stock_item_auto_completed form-control stock_item"
            />
            <input
                type="hidden"
                name="stock_item_id"
                id="stock_item_id"
                class="stock_item_auto_completed_id form-control stock_item_id "
            />
        </div>
        <div class="col-md-2">
            <label>Voucher Type : </label>
            <select name="voucher_id" class="form-control js-example-basic-single voucher_id">
                <option value="0">--ALL--</option>
                @php  $voucher_type_id= 0;  @endphp
                @foreach ($vouchers as $voucher)
                    @if($voucher_type_id!=$voucher->voucher_type_id)
                    @php  $voucher_type_id=$voucher->voucher_type_id;  @endphp
                        <option style="color:red;"  value="v{{$voucher->voucher_type_id??''}}">{{$voucher->voucher_type??''}}</option>
                    @endif
                    <option value="{{$voucher->voucher_id}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$voucher->voucher_name}}</option>

                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label>Godown Name:</label>
            <select name="godown_id[]" class="form-control js-example-basic-multiple godown_id" multiple="multiple" required>
                <option value="0" selected>All</option>
                @foreach($godowns as $godown)
                <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <div class="row  m-0 p-0 ">
                <div class="col-md-6 m-0 p-0 start_date">
                    <label>Date From: </label>
                        <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{$from_date?? financial_end_date(date('Y-m-d')) }}"   name="narratiaon"  >
                </div>
                <div class="col-md-6 m-0 p-0 end_date">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{$to_date?? financial_end_date(date('Y-m-d')) }}"  name="narratiaon"  >
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <br>
            <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
        </div>
        <div class="col-md-12">
            <label></label>
            <div>
                <input class="form-check-input inwords_rate_checkbox" type="checkbox"  name="inwords_rate"   checked>
                <label class="form-check-label fs-6" for="flexRadioDefault1" >
                    Inwards Rate
                </label>
                <input class="form-check-input inwords_value_checkbox" type="checkbox"  name="inwords_value"    checked>
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Inwards Value
                </label>
                <input class="form-check-input outwords_rate_checkbox" type="checkbox"  name="outwords_rate"   checked>
                <label class="form-check-label fs-6" for="flexRadioDefault1" >
                    Outwards Rate
                </label>
                <input class="form-check-input outwords_value_checkbox" type="checkbox"  name="outwords_value"    checked>
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Outwards Value
                </label>
                <input class="form-check-input  closing_rate_checkbox" type="checkbox"  name="closing_rate"   checked>
                <label class="form-check-label fs-6" for="flexRadioDefault1" >
                    Closing Rate
                </label>
                <input class="form-check-input closing_value_checkbox" type="checkbox"  name="closing_value"   checked>
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Closing  Value
                </label>
                <input class="form-check-input narratiaon_checkbox" type="checkbox" id="narratiaon" name="narratiaon"  >
                <label class="form-check-label fs-6" for="flexRadioDefault1" >
                    Narration
                </label>
            </div>
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
                <th rowspan="2" style="width: 1%;" class="th align-middle">SL.</th>
                <th rowspan="2"style="width: 3%" class="th align-middle" >Date</th>
                <th rowspan="2" style="width: 3%;" class="th align-middle">Voucher No</th>
                <th rowspan="2" style="width: 2%;" class="th align-middle">Voucher Type</th>
                <th rowspan="2" style="width: 5%;text-align:center;table-layout: fixed;" class="th narration align-middle">Narration</th>
                <th rowspan="2" style="width: 5%;text-align:center;table-layout: fixed;" class="th align-middle">Particulars</th>
                <th colspan="3" style=" width: 5%; text-align:center;"class="th inwards">Inward Balance</th>
                <th colspan="3" style=" width: 5%; text-align:center;"class="th outwards">Outward Balance</th>
                <th colspan="3" style=" width: 5%; text-align:center;"class="th closing">Closing Balance</th>

            </tr>
            <tr>
                <th style="width: 3%;" class="th text-end">Quantity</th>
                <th style="width: 3%;" class="th inwards_rate text-end">Rate</th>
                <th style="width: 5%;" class="th inwards_value text-end">Value</th>
                <th style="width: 2%;" class="th text-end">Quantity</th>
                <th style="width: 2%;" class="th outwards_rate text-end">Rate</th>
                <th style="width: 5%;" class="outwards_value th text-end">Value</th>
                <th style="width: 3%;" class="th text-end">Quantity</th>
                <th style="width: 3%;" class="thc losing_rate text-end">Rate</th>
                <th style="width: 5%"  class="th closing_value text-end">Value</th>

            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th  style="width: 1%;" class="th"></th>
                <th  style="width: 5%;" class="th">Total :</th>
                <th  style="width: 1%;" class="th"></th>
                <th  style="width: 1%;" class="th"></th>
                <th  style="width: 1%;" class="th"></th>
                <th  style="width: 1%;" class="th narration"></th>
                <th  style="width: 2%;font-size: 18px;" class="th total_inwards_qty text-end"></th>
                <th  style="width: 2%;font-size: 18px;" class="th inwards_rate total_inwards_rate text-end"></th>
                <th  style="width: 5%;font-size: 18px;" class="th inwards_value total_inwards_value text-end"></th>
                <th  style="width: 2%;font-size: 18px;" class="th total_outwards_qty text-end"></th>
                <th  style="width: 3%;font-size: 18px;" class="th outwards_rate total_outwards_rate text-end"></th>
                <th  style="width: 5%;font-size: 18px;" class="th outwards_value total_outwards_value text-end"></th>
                <th  style="width: 3%;font-size: 18px;" class="th total_clasing_qty text-end"></th>
                <th style="width: 2%;font-size: 18px;"  class="th closing_rate total_clasing_rate text-end"></th>
                <th  style="width: 5%;font-size: 18px;" class="th closing_value total_clasing_value text-end"></th>
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
// get_item_recursive('{{route("stock-item-select-option-tree") }}');

// stock item get id check
if("{{$stock_item_id??0}}"!=0){
    $('.stock_item_id').val("{{$stock_item_id??0}}").trigger('change');
}

// godown id check
if("{{$godown_id??0}}"!=0){
    let godown_string="{{$godown_id??0}}";
    $('.godown_id').val((godown_string).split(",")).trigger('change');
}
if("{{$voucher_id??0}}"!=0){
     $('.voucher_id').val("{{$voucher_id??0}}");
}

let  total_inwards_qty=0;total_inwards_value=0; total_outwards_qty=0; total_outwards_value=0;total_clasing_qty=0;total_clasing_rate=0;
$(document).ready(function () {

    // stock item get id check
    if("{{$stock_item_id??0}}"!=0){
        local_store_stock_item_register_set_data()
    }else{
        local_store_stock_item_register_get();
    }

    get_stock_item_register_initial_show();

    $("#add_item_register_form").submit(function(e) {
        local_store_stock_item_register_set_data();
             checkbox_check();
             print_date();
            e.preventDefault();
            const fd = new FormData(this);
            $(".modal").show();
            $.ajax({
                url: '{{ route("stock-item-register-data") }}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                      $(".modal").hide();
                    get_item_register_val(response.data)
                    },
                     error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
    });


});
function get_item_register_val(response) {
         total_inwards_qty = 0;
         total_inwards_value = 0;
         total_outwards_qty = 0;
         total_outwards_value = 0;
         total_clasing_qty = 0;
         total_clasing_rate = 0;

        let html = '';
        let htmlFragment = document.createDocumentFragment();
        let row = document.createElement('tr');

        row.innerHTML=`<td style="width: 1%; border: 1px solid #ddd;"></td>
                        <td colspan="2" style="width: 3%;" class="td">Opening Blance</td>
                        <td style="width:3%;"class="td"></td>
                        <td style="width:2%" class="td"></td>
                        <td style="width:3%" class="td"></td>
                        ${$(".narratiaon_checkbox").is(':checked') ? `<td style="width: 2%;" class="td"></td>` : ''}
                        ${$(".inwords_rate_checkbox").is(':checked') ? `<td style="width: 3%;" class="td"></td>` : `<td style='display:none'></td>`}
                        ${$(".inwords_value_checkbox").is(':checked') ? `<td style="width: 2%;" class="td"></td>` : ''}
                        <td style="width: 3%;" class="td"></td>
                        ${$(".outwords_rate_checkbox").is(':checked') ? `<td style="width: 3%;" class="td"></td>` : ''}
                        ${$(".outwords_value_checkbox").is(':checked') ? `<td style="width: 2%;" class="td"></td>` : ''}
                        <td style="width: 3%;" class="td text-end">${((response.oppening_stock[0]?.total_stock_total_opening_qty || 0).formatBangladeshCurrencyType("quantity", response?.unit_of_measure?.symbol))}</td>
                        ${$(".closing_rate_checkbox").is(':checked') ? `<td style="width: 3%" class="td text-end">${dividevalue((response.oppening_stock[0]?.total_stock_total_out_opening || 0),(response.oppening_stock[0]?.total_stock_total_opening_qty || 0)).formatBangladeshCurrencyType("rate")}</td>` : ''}
                        ${$(".closing_value_checkbox").is(':checked') ? `<td style="width: 2%;" class="td text-end">${(((response.oppening_stock[0]?.total_stock_total_out_opening || 0)).formatBangladeshCurrencyType("amount"))}</td>` : ''}
                  `;
        htmlFragment.appendChild(row);

          // checking oppening balance
          let op_1=0;
        $.each(response.current_stock, function (key, v) {

                let inwards_qty_in=0;
                let inwards_value_in=0;
                let outwards_qty_o=0;
                let outwards_value_o=0;
                    if(op_1==0){
                         total_clasing_qty += (parseFloat(response.oppening_stock[0]?.total_stock_total_opening_qty || 0)+parseFloat(v.inwards_qty || 0))-parseFloat( v.outwards_qty);
                         total_clasing_rate = parseFloat(v.current_rate || 0);
                        op_1=1;
                    }else{
                        total_clasing_qty += parseFloat(v.inwards_qty || 0)-parseFloat(v.outwards_qty);
                        total_clasing_rate= parseFloat(v.current_rate || 0);
                    }

                        inwards_qty_in=(v.inwards_qty || 0)
                        total_inwards_qty +=inwards_qty_in ;
                        inwards_value_in=(v.inwards_value || 0);
                        total_inwards_value +=inwards_value_in;
                        outwards_qty_o=(v.outwards_qty || 0)
                        total_outwards_qty +=outwards_qty_o;
                        outwards_value_o=(v.outwards_value || 0);
                        total_outwards_value +=outwards_value_o;

                let row = document.createElement('tr');
                row.id = `${v.tran_id},${v.voucher_type_id}`;
                row.className = "left left-data editIcon table-row";
                row.innerHTML = `
                    <td style="width: 1%; border: 1px solid #ddd;">${key + 1}</td>
                    <td style="width: 3%;" class="td text-wrap">${join(new Date(v.transaction_date), options, ' ')}</td>
                    <td style="width: 3%;" class="td text-wrap">${v.invoice_no}</td>
                    <td style="width: 2%;" class="td voucher_name">${redirectVoucherIdWise(v.voucher_type_id, v.tran_id,v.voucher_name)}</td>
                    ${$(".narratiaon_checkbox").is(':checked') ? `<td style="width:3%;" class="td text-wrap">${(v.narration ||"")}</td>` : ""}
                    <td style="width: 3%;" class="td text-wrap">${(v.ledger_name ? v.ledger_name : '')}</td>
                    <td style="width: 3%;" class="td text-end" >${(inwards_qty_in || 0).formatBangladeshCurrencyType("quantity", response?.unit_of_measure?.symbol)}</td>
                    ${$(".inwords_rate_checkbox").is(':checked') ? `<td style="width:3%;" class="td text-end">${((dividevalue((inwards_value_in ||0), (inwards_qty_in ||0 )) || 0).formatBangladeshCurrencyType("rate"))}</td>` : ""}
                    ${$(".inwords_value_checkbox").is(':checked') ? `<td style="width: 3%;" class="td text-end">${(( inwards_value_in || 0).formatBangladeshCurrencyType("amount"))}</td>` : ''}
                    <td style="width: 3%;" class="td text-end">${(outwards_qty_o||0).formatBangladeshCurrencyType("quantity", response?.unit_of_measure?.symbol)}</td>
                    ${$(".outwords_rate_checkbox").is(':checked') ? `<td style="width: 2%;" class="td text-end">${(( outwards_value_o ||0) / (outwards_qty_o||1)).formatBangladeshCurrencyType("rate")}</td>` : ""}
                    ${$(".outwords_value_checkbox").is(':checked') ? `<td style="width: 3%" class="td text-end">${(( outwards_value_o ||0).formatBangladeshCurrencyType("amount"))}</td>` : ''}
                    <td style="width: 3%;" class="td text-end">${((total_clasing_qty)).formatBangladeshCurrencyType("quantity",response?.unit_of_measure?.symbol)}</td>
                    ${$(".closing_rate_checkbox").is(':checked') ? `<td style="width: 2%;" class="td text-end">${((v.current_rate || 0).formatBangladeshCurrencyType("rate"))}</td>` : ''}
                    ${$(".closing_value_checkbox").is(':checked') ? `<td style="width: 3%;" class="td text-end">${((total_clasing_qty*v.current_rate)).formatBangladeshCurrencyType("amount")}</td>` : ''}
                `;

                htmlFragment.appendChild(row);
            });

            $(".item_body").html(htmlFragment);
            $('.total_inwards_qty').text(total_inwards_qty.formatBangladeshCurrencyType("quantity", response?.unit_of_measure?.symbol));
            $('.total_inwards_rate').text(((Math.abs(total_inwards_value) / Math.abs(total_inwards_qty||1)) || 0).formatBangladeshCurrencyType("rate"));
            $('.total_inwards_value').text(total_inwards_value.formatBangladeshCurrencyType("amount"));
            $('.total_outwards_qty').text(total_outwards_qty.formatBangladeshCurrencyType("quantity", response?.unit_of_measure?.symbol));
            $('.total_outwards_rate').text(((Math.abs(total_outwards_value) / Math.abs(total_outwards_qty||1)) || 0).formatBangladeshCurrencyType("rate"));
            $('.total_outwards_value').text(total_outwards_value.formatBangladeshCurrencyType("amount"));
            $('.total_clasing_qty').text((total_clasing_qty ).formatBangladeshCurrencyType("quantity", response?.unit_of_measure?.symbol));
            $('.total_clasing_rate').text(((Math.abs((total_clasing_rate*total_clasing_qty))/Math.abs(total_clasing_qty||1))).formatBangladeshCurrencyType("rate"));
            $('.total_clasing_value').text(((total_clasing_rate*total_clasing_qty)).formatBangladeshCurrencyType("amount"));
            get_hover();
            scroll_table_to_prev();
   }
    function get_stock_item_register_initial_show(){
        checkbox_check();
        updategetAndRemoveStorage();
        print_date();
        $(".modal").show();
        $.ajax({
            url: '{{ route("stock-item-register-data") }}',
                method: 'GET',
                data: {
                    to_date:$('.to_date').val(),
                    from_date:$('.from_date').val(),
                    stock_item_id:$('.stock_item_id').val(),
                    godown_id:$('.godown_id').val(),
                    voucher_id:$('.voucher_id').val(),
                },
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_item_register_val(response.data);
                    console.log(response.data);
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
    }

    function checkbox_check(){
         // checking colspan table
         $('.inwards').attr('colspan',1+($(".inwords_rate_checkbox" ).is(':checked')?1:0)+($(".inwords_value_checkbox" ).is(':checked')?1:0));
        $('.outwards').attr('colspan',1+($(".outwords_rate_checkbox" ).is(':checked')?1:0)+($(".outwords_value_checkbox" ).is(':checked')?1:0));
         $('.closing').attr('colspan',1+($(".closing_rate_checkbox" ).is(':checked')?1:0)+($(".closing_value_checkbox" ).is(':checked')?1:0));

        //checking condition
        $(".inwards_rate").css("display", $(".inwords_rate_checkbox" ).is(':checked')==true?'':'none');
        $(".inwards_value").css("display", $(".inwords_value_checkbox" ).is(':checked')==true?'':'none');
        $(".outwards_rate").css("display", $(".outwords_rate_checkbox" ).is(':checked')==true?'':'none');
        $(".outwards_value").css("display", $(".outwords_value_checkbox" ).is(':checked')==true?'':'none');
        $(".closing_rate").css("display", $(".closing_rate_checkbox" ).is(':checked')==true?'':'none');
        $(".closing_value").css("display", $(".closing_value_checkbox" ).is(':checked')==true?'':'none');
        $(".narration").css("display", $(".narratiaon_checkbox" ).is(':checked')==true?'':'none');
    }
    // local store in bowser
    function local_store_stock_item_register_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("stock_item", '.stock_item');
        getStorage("stock_item_id", '.stock_item_id');
        let godown = getStorage("godown");
        if (godown) {
            $('.godown_id').val(godown.split(",")).trigger('change');
        }
        getStorage("voucher_id", '.voucher_id');
        getStorage("inwords_rate_checkbox", '.inwords_rate_checkbox', 'checkbox');
        getStorage("inwords_value_checkbox", '.inwords_value_checkbox', 'checkbox');
        getStorage("outwords_rate_checkbox", '.outwords_rate_checkbox', 'checkbox');
        getStorage("outwords_value_checkbox", '.outwords_value_checkbox', 'checkbox');
        getStorage("closing_rate_checkbox", '.closing_rate_checkbox', 'checkbox');
        getStorage("closing_value_checkbox", '.closing_value_checkbox', 'checkbox');
        getStorage("narratiaon_checkbox", '.narratiaon_checkbox', 'checkbox');
    }

    function local_store_stock_item_register_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("stock_item", $('.stock_item').val());
        setStorage("stock_item_id", $('.stock_item_id').val());
        setStorage("godown", $('.godown_id').val());
        setStorage("voucher_id", $('.voucher_id').val());
        setStorage("inwords_rate_checkbox", $(".inwords_rate_checkbox").is(':checked'));
        setStorage("inwords_value_checkbox",$(".inwords_value_checkbox").is(':checked'));
        setStorage("outwords_rate_checkbox", $(".outwords_rate_checkbox").is(':checked'));
        setStorage("outwords_value_checkbox", $(".outwords_value_checkbox").is(':checked'));
        setStorage("closing_rate_checkbox", $(".closing_rate_checkbox").is(':checked'));
        setStorage("closing_value_checkbox", $(".closing_value_checkbox").is(':checked'));
        setStorage("narratiaon_checkbox",$(".narratiaon_checkbox").is(':checked'));
    }

// table header fixed
$(document).ready(function(){
    $(document).on('click', '.voucher_name', function(e) {
            setStorage("end_date_update", $('.to_date').val());
            setStorage("start_date_update", $('.from_date').val());
            setStorage("voucher_id_update", $('.voucher_id').val());
            setStorage("godown_id_update", $('.godown_id').val());
            setStorage("stock_item_id_update", $('.stock_item_id').val());
    })
    // table header fixed
    let display_height=$(window).height();
    $('.tableFixHead_item_register').css('height',`${display_height-110}px`);

});
function updategetAndRemoveStorage() {
        getStorage("end_date_update", '.to_date');
        getStorage("start_date_update", '.from_date');
        getStorage("voucher_id_updat", '.voucher_id');
        getStorage("godown_id_update", '.godown_id');
        getStorage("stock_item_id_update", '.stock_item_id');
        getRemoveItem("end_date_update", '.to_date');
        getRemoveItem("start_date_update", '.from_date');
        getRemoveItem("voucher_id_updat", '.voucher_id');
        getRemoveItem("godown_id_update", '.godown_id');
        getRemoveItem("stock_item_id_update", '.stock_item_id');
    }
</script>
@endpush
@endsection
