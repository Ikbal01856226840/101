@extends('layouts.backend.app')
@section('title','Stock Group Summary')
@push('css')
 <style>

    .th {
        border: 1px solid #ddd;
        font-weight: bold;
        text-align: center;
    }

    .td {
        border: 1px solid #ddd;
        
        text-align: right;
    }

    .table-scroll thead tr:nth-child(2) th {
        top: 30px;
    }

    body{
        overflow: auto !important;
    }
</style>
@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'size'=>'modal-xl',
    'page_title'=>'Godownwise Stock Analysis',
    'page_unique_id'=>22,
    'stockGroup'=>'yes',
    'title'=>'Godownwise Stock Analysis',
    'daynamic_function'=>'get_unused_stock_initial_show',
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Godownwise Stock Analysis',
    'print_layout'=>'portrait',
    'print_header'=>'Godownwise Stock Analysis',
    'user_privilege_title'=>'StockGroupSummary',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="grodown_wise_stock_analysis_form"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row ">
            <div class="col-md-3">
                <label>Stock Group :</label>
                <select name="stock_group_id" class="form-control  js-example-basic-single  stock_group_id" required>
                    <option value="">--Select--</option>
                    <option value="0">Primary</option>
                    {!!html_entity_decode($stock_group)!!}
                </select>

            </div>
            <div class="col-md-3">
                <div class="row px-2">
                    <div class="col-md-6">
                        <label>Date From: </label>
                        <input type="text" name="from_date" class="form-control setup_date from_date" value="{{financial_end_date(date('Y-m-d'))}}">
                    </div>
                    <div class="col-md-6">
                        <label>Date To : </label>
                        <input type="text" name="to_date" class="form-control setup_date to_date" value="{{financial_end_date(date('Y-m-d'))}}">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <label></label>
                <div class="form-group mb-0" style="position: relative">

                    <input class="form-check-input status_value" type="radio"  name="status_value"  value="1" >
                    <label class="form-check-label fs-6" for="flexRadioDefault1" >
                        Opening Balance
                    </label>

               </div>
               <div class="form-group m-0 p-0" style="position:relative">
                    <input class="form-check-input status_value" type="radio"  name="status_value"  value="2" checked="checked" >
                    <label class="form-check-label fs-6" for="flexRadioDefault1" >
                        Closing Balance
                    </label>

               </div>
            </div>
            <div class="col-md-2">
                <label></label><br>
                <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
            </div>
        </div>
    </form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_grodown_wise_stock_analysis">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <tr>
                <th rowspan="2" class="th align-middle" style="width: 1%;">SL.</th>
                <th rowspan="2" class="th align-middle" style="width: 5%;table-layout: fixed;" >Particulars</th>
                <th colspan="3" class="th opening opening_checkbox" style=" width: 5%; ">Opening Balance</th>
                <th colspan="3" class="th closing closing_checkbox" style=" width: 5%;">Closing Balance</th>

            </tr>
            <tr>
                <th style="display: none;"></th>
                <th style="display: none;"></th>
                <th class="th opening_checkbox text-end" >Quantity</th>
                <th class="th opening_checkbox text-end" >Rate</th>
                <th class="th opening_checkbox text-end" >Value</th>

                <th class="th closing_checkbox text-end " >Quantity</th>
                <th class="th closing_checkbox text-end">Rate</th>
                <th class="th closing_checkbox text-end">Value</th>

            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th  style="width: 1%;"class="td_th"></th>
                <th  style="width: 5%;"class="td_th">Total :</th>
                <th  style="width: 2%; " class="th total_opening_qty opening_checkbox text-end"></th>
                <th  style="width: 2%;" class="th total_opening_rate opening_checkbox text-end"></th>
                <th  style="width: 5%;" class="th total_opening_value opening_checkbox text-end"></th>

                <th  style="width: 3%;" class="th total_clasing_qty   closing_checkbox text-end"></th>
                <th style="width: 2%;"  class="th total_clasing_rate  closing_checkbox text-end"></th>
                <th  style="width: 5%;" class="th total_clasing_value closing_checkbox text-end"></th>
            </tr>
        </tfoot>
    </table>
</div>
@endslot
@endcomponent
<br>
@push('js')
<script>
let  total_opening_qty=0;total_opening_value=0;total_clasing_qty=0;total_clasing_value=0; i=1,j=1;
// grodown wise stock analysis
$(document).ready(function () {
    
    if(getStorage("stock_group_id", '.stock_group_id')){
        grodown_wise_stock_analysis_initial_show();
    }

    local_grodown_wise_stock_analysis_get();
    chcking_checkbok();

    $("#grodown_wise_stock_analysis_form").submit(function(e) {
        
        local_grodown_wise_stock_analysis_set_data();
        $(".modal").show();
            total_opening_qty=0;
            total_opening_value=0;
            total_inwards_qty=0;
            total_inwards_value=0;
            total_outwards_qty=0;
            total_outwards_value=0;
            total_clasing_qty=0;
            total_clasing_value=0;
            i=1,j=1;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("report-grodown-wise-stock-analysis-data") }}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                    $(".modal").hide();
                    get_grodown_wise_stock_analysis(response)
                    chcking_checkbok();
                    },
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
    });
});

// grodown wise stock analysis  function
function get_grodown_wise_stock_analysis(response){
    const children_sum= calculateSumOfChildren(response.data);
    var tree=getTreeView(response.data,children_sum);
       $('.item_body').html(tree);
        get_hover();
        $('.total_opening_qty').text((total_opening_qty||0).formatBangladeshCurrencyType("quantity"));
        $('.total_opening_rate').text((((Math.abs(total_opening_value)/Math.abs(total_opening_qty))||0)).formatBangladeshCurrencyType("rate"));
        $('.total_opening_value').text((total_opening_value||0).formatBangladeshCurrencyType("amount"));
        $('.total_clasing_qty').text(total_clasing_qty.formatBangladeshCurrencyType("quantity"));
        $('.total_clasing_rate').text(((Math.abs(total_clasing_value)/Math.abs(total_clasing_qty))||0).formatBangladeshCurrencyType("rate"));
        $('.total_clasing_value').text(total_clasing_value.formatBangladeshCurrencyType("amount"));
     }
// calcucation children summation
function calculateSumOfChildren(arr) {
    const result = {};

    function sumProperties(obj, prop) {
        return obj.reduce((acc, val) => acc + (val[prop] || 0), 0);
    }

    function processNode(node) {
        if (!result[node.stock_group_id]) {
            result[node.stock_group_id] = {
                stock_group_id: node.stock_group_id,
                stock_qty_in: 0,
                stock_qty_out: 0,
                total_op_qty: 0,
                sum_op_value: 0,
                sum_current_value: 0
            };
        }

        const currentNode = result[node.stock_group_id];

        currentNode.stock_qty_in += node.stock_qty_in || 0;
        currentNode.stock_qty_out += node.stock_qty_out || 0;
        currentNode.total_op_qty += node.total_op_qty || 0;
        currentNode.sum_op_value += node.sum_op_value || 0;
        currentNode.sum_current_value += node.sum_current_value || 0;

        if (node.children) {
            node.children.forEach(processNode);
        }
    }

    arr.forEach(processNode);

    return Object.values(result);
}


// recursive function tree
function getTreeView(arr, children_sum, depth = 0, chart_id = 0,stock_item_id=0) {
    let htmlFragments = [];
    arr.forEach((v) => {
        const a = '&nbsp;&nbsp;';
        const h = a.repeat(depth);

        if (chart_id !== v.stock_group_id) {
            let matchingChild = children_sum.find(c => v.stock_group_id == c.stock_group_id);
             if (((matchingChild.total_op_qty|| 0) == 0) && ((matchingChild.stock_qty_out_opening || 0) == 0) && ((matchingChild.stock_qty_in || 0) == 0) && ((matchingChild.stock_qty_out || 0) == 0)) {} else {
                htmlFragments.push(`<tr class='left left-data editIcon example_input'>
                        <td style='width: 1%;  border: 1px solid #ddd;'></td>
                        <td style='width: 3%; border: 1px solid #ddd;'>
                        <p style="margin-left:${(h + a).length-12}px;font-weight: bold;" class="text-wrap mb-0 pb-0 ">${v.stock_group_name}</p></td>`)

            
                if (matchingChild) {
                    let opening_rate_cal_group=dividevalue(matchingChild.sum_op_value,matchingChild.total_op_qty);
                    htmlFragments.push(`
                             ${$("input[type='radio'].status_value:checked").val() == 1?`<td class="td" style='width: 3%;font-weight: bold;'>${(matchingChild.total_op_qty||0).formatBangladeshCurrencyType("quantity")}</td>
                            <td class="td" style='width: 3%;font-weight: bold;'>${(opening_rate_cal_group||0).formatBangladeshCurrencyType("rate")}</td>
                            <td class="td" style='width: 5%;font-weight: bold;'>${(matchingChild.sum_op_value||0).formatBangladeshCurrencyType("amount")}</td>`:
                            `<td class="td" style='width: 3%;font-weight: bold;'>${(((matchingChild.total_op_qty || 0) +(matchingChild.stock_qty_in || 0)) - (matchingChild.stock_qty_out || 0)).formatBangladeshCurrencyType("quantity")}</td>
                            <td class="td" style='width: 3%;font-weight: bold;'>${((matchingChild.sum_current_value || 0) / Math.abs(((matchingChild.total_op_qty || 0)+ (matchingChild.stock_qty_in || 0)) - (matchingChild.stock_qty_out || 0)) || 0).formatBangladeshCurrencyType("rate")}</td>
                            <td class="td" style='width: 5%;font-weight: bold;'>${((matchingChild.sum_current_value || 0)).formatBangladeshCurrencyType("amount")}</td>`}
                        `)

                    }
             }

            chart_id = v.stock_group_id;
        }

        if ((v.op_qty == null)&& (v.stock_in_sum_qty == null) && (v.stock_out_sum_qty == null)) { }
        else {


                if(v.stock_item_id!=stock_item_id){
                    total_opening_qty +=(v.stock_op_qty||0);
                    total_opening_value +=(v.stock_op_qty*v.op_in_rate);
                    total_clasing_qty += (v.stock_op_qty||0)+(v.stock_in_out_qty||0);
                    total_clasing_value += ((v.stock_op_qty||0)+(v.stock_in_out_qty||0))*(v.current_rate || 0);
                    htmlFragments.push(`<tr id="${v.stock_item_id}" class="lleft left-data table-row example_input">
                    <td class="sl" style="width: 1%;  border: 1px solid #ddd;">${i++}</td>
                    <td style="width: 5%;  border: 1px solid #ddd"><p style="margin-left:${(h +a+a).length-12}px" class="text-wrap mb-0 pb-0">${v.product_name}</p></td>
                    ${$("input[type='radio'].status_value:checked").val() == 1?`<td class="td" style='width: 3%;'>${(v.stock_op_qty||0).formatBangladeshCurrencyType("quantity",v.symbol)}</td>
                    <td class="td" style='width: 3%;'>${(v.op_in_rate||0).formatBangladeshCurrencyType("rate")}</td>
                    <td class="td" style='width: 5%;'>${((v.stock_op_qty||0)*(v.op_in_rate||0)).formatBangladeshCurrencyType("amount")}</td>`:
                    `<td class="td" style='width: 3%;'>${((v.stock_op_qty||0)+(v.stock_in_out_qty||0)).formatBangladeshCurrencyType("quantity",v.symbol)}</td>
                    <td class="td" style='width: 3%;'>${(v.current_rate || 0).formatBangladeshCurrencyType("rate")}</td>
                    <td class="td" style='width: 5%;'>${(((v.stock_op_qty||0)+(v.stock_in_out_qty||0))*(v.current_rate || 0)).formatBangladeshCurrencyType("amount")}</td>`}

                ` )
                stock_item_id=v.stock_item_id;
                }
                htmlFragments.push(`<tr id="${v.stock_item_id}" class="lleft left-data table-row example_input">
                    <td class="sl" style="width: 1%;  border: 1px solid #ddd;">${j++}</td>
                    <td style="width: 5%;  border: 1px solid #ddd"><p style="margin-left:${(h+a+a+a).length-12}px" class="text-wrap mb-0 pb-0">${v.godown_name}</p></td>
                     ${$("input[type='radio'].status_value:checked").val() == 1?`<td class="td" style='width: 3%;'>${(v.op_qty||0).formatBangladeshCurrencyType("quantity",v.symbol)}</td>
                    <td class="td" style='width: 3%;'>${(v.godown_wise_op_rate||0).formatBangladeshCurrencyType("rate")}</td>
                    <td class="td" style='width: 5%;'>${((v.godown_wise_op_rate||0)*(v.op_qty||0)).formatBangladeshCurrencyType("amount")}</td>`:
                    `<td class="td" style='width: 3%;'>${(((v.op_qty||0) + (v.stock_in_sum_qty || 0)) - (v.stock_out_sum_qty || 0)).formatBangladeshCurrencyType("quantity",v.symbol)}</td>
                    <td class="td" style='width: 3%;'>${(v.godown_wise_current_rate || 0).formatBangladeshCurrencyType("rate")}</td>
                    <td class="td" style='width: 5%;'>${((((v.op_qty||0) + (v.stock_in_sum_qty || 0)) - (v.stock_out_sum_qty || 0))*((v.godown_wise_current_rate|| 0))).formatBangladeshCurrencyType("amount",v.symbol)}</td>`}
                ` )


        }
        if ('children' in v) {
            htmlFragments.push(getTreeView(v.children, children_sum, depth + 1, chart_id,stock_item_id));
        }
    });

    return htmlFragments.join('');
}

$(document).ready(function(){
    // table header fixed
    let display_height=$(window).height();
    $('.tableFixHead_grodown_wise_stock_analysis').css('height',`${display_height-115}px`);
});

$(window).on('resize', function() {
    font_size_auto_change();
});
$(window).trigger('resize');

function font_size_auto_change(){
    // Get the current window width
    var windowWidth = $(window).width();
    // Calculate a new font size based on the window width
    // For example, let's make the font size 2% of the window width
    var newFontSize = windowWidth * 0.008;
    if(windowWidth>768){
        // Apply the new font size to the target element(s)
        $(document).find('table').css({'font-size': `${newFontSize}px`});
    }

};
function grodown_wise_stock_analysis_initial_show(){
            print_date();
            chcking_checkbok();
            $(".modal").show();
            $.ajax({
                url: '{{ route("report-grodown-wise-stock-analysis-data") }}',
                method: 'GET',
                data: {
                    stock_group_id:$('.stock_group_id').val(),
                    from_date:$('.from_date').val(),
                    to_date :$('.to_date').val(),
                },
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_grodown_wise_stock_analysis(response)
                },
                error: function(data, status, xhr) {
                    Unauthorized(data.status);
                }
            });
}
   function chcking_checkbok(){
        if($("input[type='radio'].status_value:checked").val() == 1){
             $(".closing_checkbox").addClass("d-none");
             $(".opening_checkbox").removeClass("d-none");
        }else{
             $(".closing_checkbox").removeClass("d-none");
             $(".opening_checkbox").addClass("d-none");
        }
   }

    function local_grodown_wise_stock_analysis_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("stock_group_id", '.stock_group_id');
       let status_value= getStorage("status_value");
       $(".status_value[value='" + status_value + "']").prop("checked", true);
       
    }

    function local_grodown_wise_stock_analysis_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("stock_group_id", $('.stock_group_id').val());
        setStorage("status_value", $(".status_value:checked").val());
    }
</script>
@endpush
@endsection
