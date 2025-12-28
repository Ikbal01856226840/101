
@extends('layouts.backend.app')
@section('title','Retrailer Analysis')
@push('css')
 <style>
    .th{
        border: 1px solid #ddd;font-weight: bold;
    }
    .td{
        border: 1px solid #ddd; font-size: 18px;
        font-family: Arial, sans-serif
    }
    body{
        overflow: auto !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
                                line-height: 18px !important;
    }
    @media (max-width: 575.98px) {
        .fns-9 {
            font-size: 11px;
        }
        .col-4{
            margin: 0px;
            padding: 0px;
        }
    }
</style>
@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'size'=>'modal-xl',
    'page_title'=>'Retrailer Analysis',
    'page_unique_id'=>3,
    'ledger'=>'yes',
    'title'=>'Retrailer Analysis',
    'daynamic_function'=>'get_retrailer_analysis_initial_show',
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Retrailer Analysis ',
    'print_layout'=>'portrait',
    'print_header'=>'Retrailer Analysis',
    'user_privilege_title'=>'RetrailerAnalysis',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="retrailer_analysis"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row px-2">
                            <label>Ledger :</label>
                            <select name="ledger_id" class="form-control js-example-basic-single ledger_id" required>
                                @if($all==1)
                                    {{-- <option value="0">--All--</option> --}}
                                @endif
                                {!!html_entity_decode($ledgers)!!}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row px-2">
                            <div class="col-md-6">
                                <label>Date From: </label>
                                <input type="text" name="from_date" class="form-control setup_date from_date" value="{{financial_end_date(date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label>Date To : </label>
                                <input type="text" name="to_date" class="form-control setup_date to_date" value="{{financial_end_date(date('Y-m-d'))}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="row ps-4">
                        <div class="col-md-2 col-sm-4 col-4">
                            <label class="fns-9">Eff. Rate :</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input op_qty in_ward_column_rate" type="checkbox" name="rate_in" value="1" checked="checked">
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Inward Column</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input op_rate out_ward_column_rate" type="checkbox" name="rate_out" value="1" checked="checked">
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Outward Column</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4"></div>
                        <div class="col-md-2 col-sm-4 col-4"></div>
                    </div>
                    <div class="row ps-4">
                        <div class="col-md-2 col-sm-4 col-4">
                            <label class="fns-9">Inward Column :</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input purchase_in in_qty" type="checkbox" name="in_qty[]" {{ isset($purchase_in)?($purchase_in==10 ? 'checked' : ''):'' }} value="10" {{$purchase_in ?? "checked"}}>
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Purchase</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input grn_in in_qty" type="checkbox" {{ isset($grn_in)?($grn_in==24 ? 'checked' : ''):'' }} name="in_qty[]" value="24" {{$grn_in ?? "checked"}}>
                            <label class="form-check-label fns-9" for="flexRadioDefault1">GRN</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input sales_return_out " type="checkbox" {{ isset($sales_return_out)?($sales_return_out==25 ? 'checked' : ''):'' }} name="sales_return_out" value="25" {{$sales_return_out ?? "checked"}}>
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Sales Return</label>
                        </div>

                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input journal_in in_qty" type="checkbox" name="in_qty[]" {{ isset($journal_in)?($journal_in==6 ? 'checked' : ''):'' }} value="6" {{$journal_in ?? "checked"}}>
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Journal</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input stock_journal_in in_qty" type="checkbox" name="in_qty[]" {{ isset($stock_journal_in)?($stock_journal_in==21 ? 'checked' : ''):'' }} value="21" {{$stock_journal_in ?? "checked"}}>
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Stock Journal</label>
                        </div>
                    </div>
                    <div class="row ps-4">
                        <div class="col-md-2 col-sm-4 col-4">
                            <label class="fns-9">Outward Column :</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input sales_out out_qty" type="checkbox" name="out_qty[]" {{ isset($sales_out)?($sales_out==19 ? 'checked' : ''):'' }} value="19" {{$sales_out ?? "checked"}}>
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Sales</label>
                        </div>

                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input gtn_out out_qty" type="checkbox" name="out_qty[]" {{ isset($gtn_out)?($gtn_out==23 ? 'checked' : ''):'' }} value="23" {{$gtn_out ?? "checked"}}>
                            <label class="form-check-label fns-9" for="flexRadioDefault1">GTN</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input purchase_return_in " type="checkbox" name="purchase_return_in" {{ isset($purchase_return_in)?($purchase_return_in==29 ? 'checked' : ''):'' }} value="29" {{$purchase_return_in ?? "checked"}}>
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Purchase Return</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input journal_out out_qty" type="checkbox" name="out_qty[]" {{ isset($journal_out)?($journal_out==6 ? 'checked' : ''):'' }} value="6" {{$journal_out ?? "checked"}}>
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Journal</label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input stock_journal_out out_qty" type="checkbox" name="out_qty[]" {{ isset($stock_journal_out)?($stock_journal_out==12 ? 'checked' : ''):'' }} value="21" {{$tock_journal_out ?? "checked"}}>
                            <label class="form-check-label fns-9" for="flexRadioDefault1">Stock Journal</label>
                        </div>
                    </div>
                    <div class="row ps-4">
                        <div class="col-md-2 col-sm-4 col-4">
                            <label class="fs-6">Ledger type :</label>
                            <input class="form-check-input ledger_type" type="radio" name="ledger_type" value="1" checked="checked">
                            <label class="form-check-label fs-6" for="flexRadioDefault1">
                                Both
                            </label>
                       </div>
                       <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input ledger_type" type="radio" name="ledger_type" value="2">
                            <label class="form-check-label fs-6" for="flexRadioDefault1">
                                Dealer
                            </label>
                        </div>
                        <div class="col-md-2 col-sm-4 col-4">
                            <input class="form-check-input ledger_type" type="radio" name="ledger_type" value="3">
                            <label class="form-check-label fs-6" for="flexRadioDefault1">
                                Retrailer
                            </label>
                       </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-12 mt-4 d-flex align-items-start justify-content-center">
                <button type="submit" class="btn btn-primary hor-grd btn-grd-primary w-100 submit" style="max-width: 200px; margin-bottom: 5px;">Search</button>
            </div>
        </div>
    </form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_stock_group_analysis">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <thead>
                <tr>
                    <th rowspan="2" style="width: 1%;" class="th align-middle">SL.</th>
                    <th rowspan="2" style="width: 5%;table-layout: fixed;" class="th align-middle" >Particulars</th>
                    <th colspan="3" style=" width: 5%; text-align:center;" class="th inwards_text in_wards"  id="inwards_text">Inward : Purchase, GRN, Purchase Return,Journal , Stock Journal	</th>
                    <th colspan="3" style=" width: 5%;text-align:center;" class="th outwards_text  out_wards" id="outwards_text">Outward : Sales, GTN, Sales Return, Journal , Stock Journal</th>
                </tr>
                <tr>
                    <th style="width: 2%;overflow: hidden;" class="th in_wards text-end">Quantity</th>
                    <th style="width: 2%;overflow: hidden;" class="th in_wards in_wards_header_rate text-end">Rate</th>
                    <th style="width: 2%;overflow: hidden;" class="th in_wards text-end">Value</th>
                    <th style="width: 5%;overflow: hidden;" class="th out_wards text-end">Quantity</th>
                    <th style="width: 2%;overflow: hidden;" class="th out_wards out_wards_header_rate text-end">Rate</th>
                    <th style="width: 3%;overflow: hidden;" class="th out_wards text-end">Value</th>
                </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;" class="th"></th>
                <th style="width: 3%;" class="th">Total :</th>
                <th style="width: 2%; font-size: 18px;font-weight: bold;"  class="th total_inwards_qty in_wards text-end"></th>
                <th style="width: 3%; font-size: 18px;font-weight: bold;"  class="th total_inwards_rate in_wards_header_rate in_wards text-end"></th>
                <th style="width: 2%; font-size: 18px;font-weight: bold;"  class="th total_inwards_value in_wards text-end"></th>
                <th style="width: 3%; font-size: 18px;font-weight: bold;"  class="th total_outwards_qty out_wards text-end"></th>
                <th style="width: 2%; font-size: 18px;font-weight: bold;"  class="th total_outwards_rate out_wards_header_rate out_wards text-end"></th>
                <th style="width: 3%; font-size: 18px;font-weight: bold;"  class="th total_outwards_value out_wards text-end"></th>
            </tr>
        </tfoot>
    </table>
</div>
@endslot
@endcomponent
<br>
@push('js')
<script>
let  total_inwards_qty=0;total_inwards_value=0; total_outwards_qty=0;total_outwards_value=0;i=1;
// stock group analysis
$(document).ready(function () {

    // ledger  get id check
    if($('ledger_id').val()!=0){
        get_retrailer_analysis_initial_show();
    }
    checkbox_check('','');

    $("#retrailer_analysis").submit(function(e) {
        local_store_retrailer_analysis_set_data();
        // $(".modal").show();
        checkbox_check('','')
        print_date();
        total_inwards_qty=0;total_inwards_value=0; total_outwards_qty=0;total_outwards_value=0;i=1;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("report-retrailer-analysis-data") }}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        $(".modal").hide();
                       get_retrailer_analysis(response)
                    },
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
    });

});

// retrailer analysis function
function get_retrailer_analysis(response){
    const children_sum= calculateSumOfChildren(response.data);
    var tree=getTreeView(response.data,children_sum);
       $('.item_body').html(tree);
        get_hover();
        $('.total_inwards_qty').text(total_inwards_qty.formatBangladeshCurrencyType("quantity"));
        $('.total_inwards_rate').text((((total_inwards_value||0)/(total_inwards_qty||0))||0).formatBangladeshCurrencyType("rate"));
        $('.total_inwards_value').text((total_inwards_value||0).formatBangladeshCurrencyType("amount"));
        $('.total_outwards_qty').text(total_outwards_qty.formatBangladeshCurrencyType("quantity"));
        $('.total_outwards_rate').text((((total_outwards_value||0)/(total_outwards_qty||0))||0).formatBangladeshCurrencyType("rate"));
        $('.total_outwards_value').text(total_outwards_value.formatBangladeshCurrencyType("amount"));
        checkbox_check('','');
 }

// calcucation child summation
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
                stock_total_in: 0,
                stock_total_out: 0
            };
        }

        const currentNode = result[node.stock_group_id];
        currentNode.stock_qty_in += node.stock_qty_in || 0;
        currentNode.stock_qty_out += node.stock_qty_out || 0;
        currentNode.stock_total_in += node.stock_total_in || 0;
        currentNode.stock_total_out+= node.stock_total_out || 0;
        if (node.children) {
            node.children.forEach(processNode);
        }
    }

    arr.forEach(processNode);

    return Object.values(result);
}

 // stock group analysis function
 function get_retrailer_analysis_initial_show(){
        local_store_retrailer_analysis_get();
       total_inwards_qty=0;total_inwards_value=0; total_outwards_qty=0;total_outwards_value=0;i=1;
        // $(".modal").show();
        print_date();
            // stock in array check
        let in_qty=[];
        $('.in_qty').each(function(){
        in_qty.push($(this).is(':checked')?$(this).val():0);
        });
        // stock out array check
        let out_qty=[];
        $('.out_qty').each(function(){
        out_qty.push($(this).is(':checked')?$(this).val():0);
        })

        $.ajax({
            url: '{{ route("report-retrailer-analysis-data") }}',
                method: 'GET',
                data: {
                    to_date:$('.to_date').val(),
                    from_date:$('.from_date').val(),
                    ledger_id:$('.ledger_id').val(),
                    ledger_type:$(".ledger_type:checked").val(),
                    purchase_return_in:$('.purchase_return_in').is(':checked')?$('.purchase_return_in').val():'',
                    sales_return_out:$('.sales_return_out').is(':checked')?$('.sales_return_out').val():'',
                    in_qty:in_qty,
                    out_qty:out_qty
                },
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_retrailer_analysis(response)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
}

// recursive function tree
function getTreeView(arr, children_sum, depth = 0, chart_id = 0) {
    let html = [];
    let in_ward_column_rate = $('.in_ward_column_rate').is(':checked');
    let out_ward_column_rate = $('.out_ward_column_rate').is(':checked');
    if(in_ward_column_rate){
        $('.in_wards_header_rate').removeClass('d-none')
        $('#inwards_text').attr('colspan',3)
    }else{
        $('.in_wards_header_rate').addClass('d-none')
        $('#inwards_text').attr('colspan',2)
    }
    if(out_ward_column_rate){
        $('.out_wards_header_rate').removeClass('d-none')
        $('#outwards_text').attr('colspan',3)
    }else{
        $('.out_wards_header_rate').addClass('d-none')
        $('#outwards_text').attr('colspan',2)
    }
    arr.forEach(function (v) {
        a = '&nbsp;';
        h = a.repeat(depth);

        if (chart_id != v.stock_group_id) {
            let matchingChild = children_sum.find(c => v.stock_group_id == c.stock_group_id);
            if (((matchingChild.stock_qty_in|| 0) == 0) && ((matchingChild.stock_qty_out || 0) == 0) ) {} else {
                html.push(`<tr id="${v.stock_group_id+'-'+v.under}" class="left left-data table-row_tree">`);
                html.push(`<td style='width: 1%;  border: 1px solid #ddd;'></td>`);
                html.push(`<td style='width: 3%;border: 1px solid #ddd;'><p style="margin-left:${(h+a+a).length-12}px;font-weight: bold;" class="text-wrap mb-0 pb-0 ">${v.stock_group_name}</p></td>`);


                if (matchingChild) {
                    html.push(`<td style='width: 3%;font-weight: bold; 'class="td in_wards text-end ">${(matchingChild.stock_qty_in || 0).formatBangladeshCurrencyType("quantity")}</td>`);
                    if(in_ward_column_rate){
                        html.push(`<td style='width: 3%;font-weight: bold; 'class="td in_wards text-end">
                            ${((((matchingChild.stock_total_in||0)/(matchingChild.stock_qty_in||0))||0).formatBangladeshCurrencyType("rate"))}
                            </td>`);
                    }
                    html.push(`<td style='width: 3%;font-weight: bold; 'class="td in_wards text-end">${((matchingChild.stock_total_in||0)).formatBangladeshCurrencyType("amount")}</td>`);
                    html.push(`<td style='width: 3%;font-weight: bold; 'class="td out_wards text-end">${((matchingChild.stock_qty_out||0)).formatBangladeshCurrencyType("quantity")}</td>`);
                    if(out_ward_column_rate){
                        html.push(`<td style='width: 3%;font-weight: bold; 'class="td out_wards text-end">
                            ${(((matchingChild.stock_total_out||0)/(matchingChild.stock_qty_out||0))||0).formatBangladeshCurrencyType("rate")}
                            </td>`);
                    }
                    html.push(`<td style='width: 3%;font-weight: bold;'class="td  out_wards text-end">${((matchingChild.stock_total_out||0)).formatBangladeshCurrencyType("amount")}</td>`);
                }

                html.push(`</tr>`);
           }
            chart_id = v.stock_group_id;
        }

        if (((v.stock_qty_total_in|| 0) == 0) && ((v.stock_qty_total_out || 0) == 0) ) {} else {
            total_inwards_qty += (v.stock_qty_total_in || 0);
            total_inwards_value += (v.stock_total_value_in || 0);
            total_outwards_qty += (v.stock_qty_total_out || 0);
            total_outwards_value += (v.stock_total_value_out || 0);

            html.push(`<tr id="${v.stock_item_id}" class="left left-data editIcon table-row">`);
            html.push(`<td class="sl" style="width: 1%;  border: 1px solid #ddd;">${i++}</td>`);
            html.push(`<td style="width: 5%; color: #0B55C4;border: 1px solid #ddd;" class="item_name"><p style="margin-left:${(h+a+a+a).length-12}px" class="text-wrap mb-0 pb-0">${v.product_name}</p></td>`);
            html.push(`<td style='width: 3%;'class='td opening in_wards text-end'>${(v.stock_qty_total_in || 0).formatBangladeshCurrencyType("quantity",v.symbol)}</td>`);

            if(in_ward_column_rate){
                html.push(`<td style='width: 3%;'class='td inwards in_wards text-end'>${(((v.stock_total_value_in||0)/(v.stock_qty_total_in||0))||0).formatBangladeshCurrencyType("rate")}</td>`);
            }
            html.push(`<td style='width: 3%;'class='td outwards in_wards text-end'>${((v.stock_total_value_in||0)).formatBangladeshCurrencyType("amount")}</td>`);
            html.push(`<td style='width: 3%;'class='td clasing out_wards text-end'>${((v.stock_qty_total_out||0)).formatBangladeshCurrencyType("quantity",v.symbol)}</td>`);

            if(out_ward_column_rate){
                html.push(`<td style='width: 3%;'class='td outwards out_wards text-end'>${(((v.stock_total_value_out||0)/(v.stock_qty_total_out||0))||0).formatBangladeshCurrencyType("rate")}</td>`);
            }
            html.push(`<td style='width: 3%;'class='td clasing out_wards text-end'>${((v.stock_total_value_out||0)).formatBangladeshCurrencyType("amount")}</td>`);
            html.push(`</tr>`);
        }

        if ('children' in v) {
            html.push(getTreeView(v.children, children_sum, depth + 1, chart_id));
        }
    });

    return html.join("");
}

$(document).ready(function(){
    // table header fixed
    let display_height=$(window).height();
    $('.tableFixHead_stock_group_analysis').css('height',`${display_height-110}px`);
});

$(document).ready(function () {

   // item wise analysis route
   $('.sd').on('click', '.item_name',function(e){
        e.preventDefault();
        let  stock_item_id=$(this).closest('tr').attr('id');
        let ledger_id=$('.ledger_id').val();
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        let purchase_in=$('.purchase_in').is(':checked')?$('.purchase_in').val():0;
        let grn_in=$('.grn_in').is(':checked')?$('.grn_in').val():0;
        let purchase_return_in=$('.purchase_return_in').is(':checked')?$('.purchase_return_in').val():0;
        let journal_in=$('.journal_in').is(':checked')?$('.journal_in').val():0;
        let stock_journal_in=$('.stock_journal_in').is(':checked')?$('.stock_journal_in').val():0;
        let sales_return_out=$('.sales_return_out').is(':checked')?$('.sales_return_out').val():0;
        let gtn_out=$('.gtn_out').is(':checked')?$('.gtn_out').val():0;
        let sales_out=$('.sales_out').is(':checked')?$('.sales_out').val():0;
        let journal_out=$('.journal_out').is(':checked')?$('.journal_out').val():0;
        let stock_journal_out=$('.stock_journal_out').is(':checked')?$('.stock_journal_out').val():0;

        url = "{{route('ledger-analysis-details', ['stock_item_id' =>':stock_item_id','ledger_id'=>':ledger_id','form_date' =>':form_date','to_date' =>':to_date','purchase_in'=>':purchase_in','grn_in'=>':grn_in','purchase_return_in'=>':purchase_return_in','journal_in'=>':journal_in','stock_journal_in'=>':stock_journal_in','sales_return_out'=>':sales_return_out','gtn_out'=>':gtn_out','sales_out'=>':sales_out','journal_out'=>':journal_out','stock_journal_out'=>':stock_journal_out'])}}";

        url = url.replace(':stock_item_id',stock_item_id);
        url = url.replace(':ledger_id',ledger_id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        url=url.replace(':purchase_in',purchase_in);
        url = url.replace(':grn_in',grn_in);
        url = url.replace(':purchase_return_in',purchase_return_in);
        url = url.replace(':journal_in',journal_in);
        url = url.replace(':stock_journal_in',stock_journal_in);
        url=url.replace(':sales_return_out',sales_return_out);
        url=url.replace(':gtn_out',gtn_out);
        url = url.replace(':sales_out',sales_out);
        url = url.replace(':journal_out',journal_out);
        url=url.replace(':stock_journal_out',stock_journal_out);
        window.open(url,'_blank');
    });
});
function local_store_retrailer_analysis_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("ledger_id", '.ledger_id');
        getStorage("purchase", '.purchase_in', 'checkbox');
        getStorage("grn", '.grn_in', 'checkbox');
        getStorage("purchase_return", '.purchase_return_in', 'checkbox');
        getStorage("journal_in", '.journal_in', 'checkbox');
        getStorage("stock_journal_in", '.stock_journal_in', 'checkbox');
        getStorage("gtn", '.gtn_out', 'checkbox');
        getStorage("sales", '.sales_out', 'checkbox');
        getStorage("sales_return", '.sales_return_out', 'checkbox');
        getStorage("journal_out", '.journal_out', 'checkbox');
        getStorage("stock_journal_out", '.stock_journal_out', 'checkbox');
        getStorage("in_ward_column", '.in_ward_column','checkbox');
        getStorage("out_ward_column", '.out_ward_column','checkbox');

    }

    function local_store_retrailer_analysis_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("ledger_id", $('.ledger_id').val());
        setStorage("purchase", $('.purchase_in').is(':checked'));
        setStorage("grn", $('.grn_in').is(':checked'));
        setStorage("purchase_return", $('.purchase_return_in').is(':checked'));
        setStorage("journal_in", $('.journal_in').is(':checked'));
        setStorage("stock_journal_in", $('.stock_journal_in').is(':checked'));
        setStorage("gtn", $('.gtn_out').is(':checked'));
        setStorage("sales", $('.sales_out').is(':checked'));
        setStorage("journal_out", $('.journal_out').is(':checked'));
        setStorage("stock_journal_out", $('.stock_journal_out').is(':checked'));
        setStorage("sales_return", $('.sales_return_out').is(':checked'));
        setStorage("in_ward_column", $('.in_ward_column').is(':checked'));
        setStorage("out_ward_column", $('.out_ward_column').is(':checked'));
    }
function checkbox_check(in_wards_text,out_wards_text) {

    if($(".purchase_in" ).is(':checked')==true){

        in_wards_text+='Purchase,';

    }
    if($(".grn_in" ).is(':checked')==true){
        in_wards_text+='Grn,';
    }
    if($(".sales_return_out" ).is(':checked')==true){
        in_wards_text+='Sales Return,';

    }

    if ($(".journal_in").is(':checked') == true) {
        in_wards_text+='Journal ,';
    }

    if ($(".stock_journal_in").is(':checked') == true) {
        in_wards_text+='Stock Journal';
    }

    if($(".sales_out" ).is(':checked')==true){
        out_wards_text+='Sales,';
    }
    if($(".gtn_out" ).is(':checked')==true){
        out_wards_text+='Gtn,';
    }
    if($(".purchase_return_in" ).is(':checked')==true){
        out_wards_text+='Purchase  Return,';
    }

    if ($(".journal_out").is(':checked') == true) {
        out_wards_text+='Journal ,';
    }

    if ($(".stock_journal_out").is(':checked') == true) {
        out_wards_text+='Stock Journal';
    }

    $('.inwards_text').text("Inward : "+in_wards_text.replace(/,$/, ''));
    $('.outwards_text').text(" Outward : "+out_wards_text.replace(/,$/, ''));
}
</script>
@endpush
@endsection
