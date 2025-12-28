
@extends('layouts.backend.app')
@section('title','Stock Group Analysis')
@push('css')
 <style>
    .sales_column {
        min-width: 60px
    }
    .sort_column_none {
        min-width: 70px
    }
    .sort_column_a_z {
        min-width: 55px
    }
    .sales_return_colunm {
        min-width: 110px;
    }

    .inword_cloumn {
        min-width: 135px;
    }

    .eff_rate_colunm {
        min-width: 135px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 18px !important;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        padding: 0px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        padding: 0px !important;
    }
</style>
@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'size'=>'modal-xl',
    'page_title'=>'Stock Item Analysis',
    'page_unique_id'=>8,
    'godown'=>'yes',
    'stock_item'=>'yes',
    'title'=>'Stock Item Analysis',
    'daynamic_function'=>'get_stock_item_analysis_initial_show',
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Stock Item Analysis ',
    'print_layout'=>'portrait',
    'print_header'=>'Stock Item Analysis',
    'user_privilege_title'=>'StockItemAnalysis',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="stock_item_analysis"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row ">
            <div class="col-md-3">
                <label>Stock Item :</label>
                <select name="stock_item_id" class="form-control  js-example-basic-single stock_item stock_item_id" required>
                    <option value="">--Select--</option>

                </select>
                <label>Godown Name :</label>
                <select name="godown_id[]" class="form-control js-example-basic-multiple godown_id" multiple="multiple" required >
                    <option value="0" selected>All</option>
                    @foreach($godowns as $godown)
                    <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
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
                <div class="col-md-2">
                    <label></label><br>
                    <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-0" style="position: relative">
                    <label class="fs-6 eff_rate_colunm">Eff. Rate :</label>
                    <input class="form-check-input op_qty in_ward_column" type="checkbox" name="rate_in" value="1" checked="checked">
                    <label class="form-check-label fs-7 " for="flexRadioDefault1">
                        Inward Column
                    </label>
                    <input class="form-check-input op_rate out_ward_column" type="checkbox" name="rate_out" value="1" checked="checked">
                    <label class="form-check-label fs-7" for="flexRadioDefault1">
                        Outward Column
                    </label>
                </div>
                <div class="form-group m-0 p-0" style="position: relative">
                    <label class="fs-6 inword_cloumn">Inward Column :</label>
                    <input class="form-check-input purchase" type="checkbox" name="purchase" {{ isset($purchase_in)?($purchase_in==10 ? " checked" : ''):''  }} value="10" {{$purchase_in?? "checked"}}>
                    <label class="form-check-label fs-7" for="flexRadioDefault1">
                        Purchase
                    </label>
                    <input class="form-check-input grn" type="checkbox" {{ isset($grn_in)?($grn_in==24 ? 'checked' : ''):''  }} name="grn" value="24" {{$grn_in??"checked"}}>
                    <label class="form-check-label fs-7" for="flexRadioDefault1">
                        GRN
                    </label>
                    <input class="form-check-input sales_return" type="checkbox" name="sales_return" {{ isset($sales_return_out)?($sales_return_out==25 ? ' checked' : ''):'checked'  }} value="25" {{$sales_return_out?? "checked"}}>
                    <label class="form-check-label fs-7 sales_return_colunm" for="flexRadioDefault1">
                        Sales Return
                    </label>

                    <input class="form-check-input journal_in" type="checkbox" name="journal_in" {{ isset($journal_in)?($journal_in==6 ? 'checked' : ''):'checked'  }} value="6">
                    <label class="form-check-label fs-7" for="flexRadioDefault1">
                        Journal
                    </label>
                    <input class="form-check-input stock_journal_in" type="checkbox" name="stock_journal_in" {{ isset($stock_journal_in)?($stock_journal_in==21 ? 'checked' : ''):'checked'  }} value="21">
                    <label class="form-check-label fs-7" for="flexRadioDefault1">
                        Stock Journal
                    </label>
                </div>
                <div class="form-group m-0 p-0" style="position: relative">
                    <label class="fs-6">Outward Column :</label>
                    <input class="form-check-input sales" type="checkbox" name="sales" {{ isset($sales_out)?($sales_out==19 ? ' checked' : ''):''  }} value="19" {{$sales_out?? "checked"}}>
                    <label class="form-check-label fs-7 sales_column" for="flexRadioDefault1">
                        Sales
                    </label>
                    <input class="form-check-input gtn" type="checkbox" name="gtn" {{ isset($gtn_out)?($gtn_out==23 ? ' checked' : ''):''  }} value="23" {{$gtn_out?? "checked"}}>
                    <label class="form-check-label fs-7" for="flexRadioDefault1">
                        GTN
                    </label>
                    <input class="form-check-input purchase_return" type="checkbox" name="purchase_return" {{ isset($purchase_return_in)?($purchase_return_in==29 ? ' checked' : ''):''  }} value="29" {{$purchase_return_in??"checked"}}>
                    <label class="form-check-label fs-7" for="flexRadioDefault1">
                        Purchase Return
                    </label>
                    <input class="form-check-input journal_out" type="checkbox" name="journal_out" {{ isset($journal_out)?($journal_out==6 ? 'checked' : ''):'checked'  }} value="6">
                    <label class="form-check-label fs-7" for="flexRadioDefault1">
                        Journal
                    </label>
                    <input class="form-check-input stock_journal_out" type="checkbox" name="stock_journal_out" {{ isset($stock_journal_out)?($stock_journal_out==21 ? 'checked' : ''):'checked'  }} value="21">
                    <label class="form-check-label fs-7" for="flexRadioDefault1">
                        Stock Journal
                    </label>
                </div>
                <div class="form-group m-0 p-0" style="position: relative">
                    <label class="fs-6 eff_rate_colunm">Sort by :</label>
                    <input class="form-check-input sort_by " type="radio" name="sort_by" value="1" checked>
                    <label class="form-check-label fs-7 sort_column" for="flexRadioDefault1">
                      Particulars
                    </label>
                    <input class="form-check-input  sort_by" type="radio" name="sort_by"  value="2">
                    <label class="form-check-label fs-7" for="flexRadioDefault1">
                       Quantity
                    </label>
                    <input class="form-check-input  sort_by" type="radio" name="sort_by"  value="3">
                    <label class="form-check-label fs-7" for="flexRadioDefault1">
                      Amount
                    </label>
                </div>
                <div class="form-group m-0 p-0" style="position: relative">
                    <label class="fs-6 eff_rate_colunm">Sort type:</label>
                    <input class="form-check-input stort_type" type="radio" name="stort_type"  value="3" checked>
                    <label class="form-check-label fs-7 sort_column_none" for="flexRadioDefault1">
                       None
                    </label>
                    <input class="form-check-input stort_type" type="radio" name="stort_type"  value="1">
                    <label class="form-check-label fs-7 sort_column_a_z" for="flexRadioDefault1">
                        A to Z
                    </label>
                    <input class="form-check-input stort_type" type="radio" name="stort_type" value="2">
                    <label class="form-check-label fs-7 " for="flexRadioDefault1">
                          Z to A
                    </label>
                    
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
                    <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold; overflow: hidden;">Serial</th>
                    <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold; overflow: hidden;" >Particulars/Voucher Type</th>
                    <th style="width: 5%;  border: 1px solid #ddd;font-weight: bold; overflow: hidden;" class="text-end">Quantity</th>
                    <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold; overflow: hidden;" class="text-end">Rate</th>
                    <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;overflow: hidden;"  class="text-end">Value</th>
                </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th ></th>
                <th></th>
                <th></th>
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
<script type="text/javascript" src="{{asset('ledger&item_select_option.js')}}"></script>
<script>
//item tree function
get_item_recursive('{{route("stock-item-select-option-tree") }}');

let  total_inwards_qty=0; total_inwards_value=0;total_outwards_qty=0;total_outwards_value=0;

if("{{$stock_item_id??0}}"!=0){
     $('.stock_item_id').val('{{$stock_item_id??0}}');
}
if("{{$godown_id??0}}"!=0){
        let godown_string="{{$godown_id??0}}";
        $('.godown_id').val((godown_string).split(",")).trigger('change');
}
if ("{{$from_date ?? 0 }}" != 0) {
        $('.from_date').val('{{$from_date??0}}');
    }
if ("{{ $to_date ?? 0 }}" != 0) {
    $('.to_date').val('{{$to_date??0}}');
}

// stock group analysis
$(document).ready(function () {
    
    if ("{{$from_date ?? 0 }}" != 0) {
        local_store_stock_item_analysis_set_data();
    }
    local_store_stock_item_analysis_get();
    // stock  group get id check
    if($(".stock_item_id").val()){
        get_stock_item_analysis_initial_show();
    }

    $("#stock_item_analysis").submit(function(e) {
        local_store_stock_item_analysis_set_data();
        print_date();
        total_inwards_qty=0; total_inwards_value=0;total_outwards_qty=0;total_outwards_value=0;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("stock-item-analysis-data") }}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                    get_stock_item_analysis(response.data)
                    },
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
    });

   
});

function get_stock_item_analysis(response){
              total_inwards_qty=0;total_inwards_value=0; total_outwards_qty=0; total_outwards_value=0;
                let  html='';
                let in_ward_column=$('.in_ward_column').is(':checked');
                let out_ward_column=$('.out_ward_column').is(':checked');
                //stock in
                html+=` <tr><td style="font-weight: bolder;font-size: 18px;" colspan="5">Movement Inward</td></tr> `;
                if(response.purchase){
                    response.purchase[0]?html+=`<tr><td colspan="5" style="font-weight: bolder;font-size: 16px;">Purchase</td></tr>`:'';
                    $.each(response.purchase, function(key, v) {
                           total_inwards_qty+=(v.stock_in_qty||0);total_inwards_value+=(v.stock_in_total||0);
                            html+=`<tr id="${10}" ledger_id="${v.ledger_head_id}" class="left left-data editIcon table-row">
                             <td  style="width: 1%;  border: 1px solid #ddd;">${(key+1)}</td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;color: #0B55C4"class="text-wrap" ledger_id="${v.ledger_head_id}">
                                ${stockItemAnalysisDetailsIdWise(v?.ledger_head_id,v?.ledger_name||v?.voucher_name||'',10)}
                             </td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class=" text-end">${(v.stock_in_qty||0).formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class=" text-end">
                                ${in_ward_column ?
                                    dividevalue((v?.stock_in_total||0),(v?.stock_in_qty||0)).formatBangladeshCurrencyType("rate")
                                :''}
                            </td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class=" text-end">${(v.stock_in_total||0).formatBangladeshCurrencyType("amount")}</td>
                           </tr> `;
                   });
                }
                if(response.grn){
                    response.grn[0]?html+=`<tr><td colspan="5" style="font-weight: bolder;font-size: 16px;">GRN</td></tr> `:'';
                    $.each(response.grn, function(key, v) {
                            total_inwards_qty+=(v.stock_in_qty||0);total_inwards_value+=(v.stock_in_total||0);
                            html+=` <tr id="${24}"  ledger_id="${v.ledger_head_id}" class="left left-data editIcon table-row">
                             <td  style="width: 1%;  border: 1px solid #ddd;">${(key+1)}</td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;color: #0B55C4"class="text-wrap" ledger_id="${v.ledger_head_id}">
                                ${stockItemAnalysisDetailsIdWise(v?.ledger_head_id,v?.ledger_name||v?.voucher_name||'',24)}
                            </td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_in_qty||0).formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">
                                ${in_ward_column ? 
                                    dividevalue((v?.stock_in_total||0),(v?.stock_in_qty||0)).formatBangladeshCurrencyType("rate")
                                :''}
                            </td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_in_total||0).formatBangladeshCurrencyType("amount")}</td>
                           </tr> `;
                   });
                }
                if(response.sales_return){
                    response.sales_return[0]? html+=`<tr ><td colspan="5" style="font-weight: bolder;font-size: 16px;">Sales Return </td></tr> `:'';
                    $.each(response.sales_return, function(key, v) {
                        total_inwards_qty+="{{company()->sales_return}}"==2?Math.abs((v.stock_out_qty||0)):Math.abs((v.stock_in_qty||0));
                        total_inwards_value+="{{company()->sales_return}}"==2?Math.abs((v.stock_out_total||0)):Math.abs((v.stock_in_total||0));
                        html+=`<tr id="${25}"  ledger_id="${v?.ledger_head_id}" class="left left-data editIcon table-row">
                                <td  style="width: 1%;  border: 1px solid #ddd;">${(key+1)}</td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;color: #0B55C4"class="text-wrap" ledger_id="${v.ledger_head_id}">
                                    ${stockItemAnalysisDetailsIdWise(v?.ledger_head_id,v?.ledger_name||v?.voucher_name||'',25)}
                                </td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${("{{company()->sales_return}}"==2?Math.abs(v.stock_out_qty||0):Math.abs(v.stock_in_qty||0)).formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">
                                    ${in_ward_column ? 
                                            dividevalue(
                                                Math.abs({{ company()->sales_return == 2 ? 'v.stock_out_total || 0' : 'v.stock_in_total || 0' }}),
                                                Math.abs({{ company()->sales_return == 2 ? 'v.stock_out_qty || 1' : 'v.stock_in_qty || 1' }})
                                            ).formatBangladeshCurrencyType("rate")
                                    :''}
                                </td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${("{{company()->sales_return}}"==2?Math.abs(v.stock_out_total||0):Math.abs(v.stock_in_total||0)).formatBangladeshCurrencyType("amount")}</td>
                        </tr> `;
                    });
               }
                
                if(response.journal_in){
                    response.journal_in[0]?html+=`<tr><td colspan="5" style="font-weight: bolder;font-size: 16px;">Journal</td></tr>`:'';
                    $.each(response.journal_in, function(key, v) {
                            total_inwards_qty+=(v.stock_in_qty||0);total_inwards_value+=(v.stock_in_total||0);
                            html+=` <tr id="${6}"  ledger_id="${v.ledger_head_id}" class="left left-data editIcon table-row">
                             <td  style="width: 1%;  border: 1px solid #ddd;">${(key+1)}</td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;color: #0B55C4"class="text-wrap" ledger_id="${v.ledger_head_id}">
                                ${stockItemAnalysisDetailsIdWise(v?.ledger_head_id,v?.ledger_name||v?.voucher_name||'',6)}
                             </td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_in_qty||0).formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">
                                ${in_ward_column ? 
                                    dividevalue((v.stock_in_total||0),(v.stock_in_qty||0)).formatBangladeshCurrencyType("rate")
                                :''}
                            </td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_in_total||0).formatBangladeshCurrencyType("amount")}</td>
                           </tr> `;
                   });
                }
                if(response.stock_journal_in){
                    response.stock_journal_in[0]? html+=`<tr ><td colspan="5" style="font-weight: bolder;font-size: 16px;">Stock Journal</td></tr> `:'';
                    $.each(response.stock_journal_in, function(key, v) {
                            total_inwards_qty+=(v.stock_in_qty||0);total_inwards_value+=(v.stock_in_total||0);
                            html+=` <tr id="${21}"  ledger_id="${v.ledger_head_id}" class="left left-data editIcon table-row">
                             <td  style="width: 1%;  border: 1px solid #ddd;">${(key+1)}</td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;color: #0B55C4"class="text-wrap" ledger_id="${v.ledger_head_id}">
                                ${stockItemAnalysisDetailsIdWise(v?.ledger_head_id,v?.ledger_name||v?.voucher_name||'',21)}
                             </td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_in_qty||0).formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">
                                ${in_ward_column ? 
                                    dividevalue((v.stock_in_total||0),(v.stock_in_qty||0)).formatBangladeshCurrencyType("rate")
                                :''}
                            </td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_in_total||0).formatBangladeshCurrencyType("amount")}</td>
                           </tr> `;
                   });
                }
                html+=` <tr>
                        <td style="font-weight: bolder;font-size: 18px;" colspan="2">Total :</td>
                        <td style="font-size: 16px;font-weight: bolder;"  class="text-end">${total_inwards_qty.formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
                        <td style=";font-size: 16px;font-weight: bolder;"  class="text-end">
                            ${in_ward_column ? 
                                Math.abs(dividevalue(total_inwards_value,total_inwards_qty)).formatBangladeshCurrencyType("rate")
                            :''}
                        </td>
                        <td style=";font-size: 16px;font-weight: bolder;"  class="text-end">${total_inwards_value.formatBangladeshCurrencyType("amount")}</td>
                    </tr> `;

                //stock out
                html+=` <tr><td style="font-weight: bolder;font-size: 18px;" colspan="5">Movement Outward</td></tr> `;

                if(response.sales){
                    response.sales[0]? html+=`<tr ><td colspan="5" style="font-weight: bolder;font-size: 16px;">Sales</td></tr> `:'';
                    $.each(response.sales, function(key, v) {
                        total_outwards_qty+=(v.stock_out_qty||0);total_outwards_value+=(v.stock_out_total||0);
                        html+=`<tr id="${19}"  ledger_id="${v.ledger_head_id}" class="left left-data editIcon table-row">
                                <td  style="width: 1%;  border: 1px solid #ddd;">${(key+1)}</td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;color: #0B55C4"class="text-wrap" ledger_id="${v.ledger_head_id}">
                                    ${stockItemAnalysisDetailsIdWise(v?.ledger_head_id,v?.ledger_name||v?.voucher_name||'',19)}
                                </td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_out_qty||0).formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">
                                    ${out_ward_column ? 
                                        dividevalue((v.stock_out_total||0),(v.stock_out_qty||0)).formatBangladeshCurrencyType("rate")
                                    :''}
                                </td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_out_total||0).formatBangladeshCurrencyType("amount")}</td>
                        </tr> `;
                    });
               }
               if(response.gtn){
                    response.gtn[0]? html+=`<tr ><td colspan="5" style="font-weight: bolder;font-size: 16px;">GTN</td></tr> `:'';
                    $.each(response.gtn, function(key, v) {
                        total_outwards_qty+=(v.stock_out_qty||0);total_outwards_value+=(v.stock_out_total||0);
                        html+=`<tr id="${23}"  ledger_id="${v.ledger_head_id}" class="left left-data editIcon table-row">
                                <td  style="width: 1%;  border: 1px solid #ddd;">${(key+1)}</td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;color: #0B55C4"class="text-wrap" ledger_id="${v.ledger_head_id}">
                                    ${stockItemAnalysisDetailsIdWise(v?.ledger_head_id,v?.ledger_name||v?.voucher_name||'',23)}
                                </td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_out_qty||0).formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">
                                    ${out_ward_column ? 
                                        dividevalue((v.stock_out_total||0),(v.stock_out_qty||0)).formatBangladeshCurrencyType("rate")
                                    :''}
                                </td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_out_total||0).formatBangladeshCurrencyType("amount")}</td>
                        </tr> `;
                    });
               }
               if(response.purchase_return){
                    response.purchase_return[0]?html+=`<tr><td colspan="5" style="font-weight: bolder;font-size: 16px;">Purchase Return</></tr> `:'';
                    $.each(response.purchase_return, function(key, v) {
                           total_outwards_qty+="{{company()->sales_return}}"==2?Math.abs((v.stock_in_qty||0)):Math.abs((v.stock_out_qty||0));
                           total_outwards_value+="{{company()->sales_return}}"==2?Math.abs((v.stock_in_total||0)):Math.abs((v.stock_out_total||0));
                            html+=` <tr id="${29}"  ledger_id="${v.ledger_head_id}" class="left left-data editIcon table-row">
                             <td  style="width: 1%;  border: 1px solid #ddd;">${(key+1)}</td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;color: #0B55C4"class="text-wrap" ledger_id="${v.ledger_head_id}">
                                ${stockItemAnalysisDetailsIdWise(v?.ledger_head_id,v?.ledger_name||v?.voucher_name||'',29)}
                             </td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${("{{company()->sales_return}}"==2?Math.abs(v.stock_in_qty||0):Math.abs(v.stock_out_qty||0)).formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">
                                ${out_ward_column ? 
                                            dividevalue(
                                                Math.abs({{ company()->sales_return == 2 ? 'v.stock_in_total || 0' : 'v.stock_out_total || 0' }}),
                                                Math.abs({{ company()->sales_return == 2 ? 'v.stock_in_qty || 1' : 'v.stock_out_qty || 1' }})
                                            ).formatBangladeshCurrencyType("rate")
                                :''}                            </td>
                             <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${("{{company()->sales_return}}"==2?Math.abs(v.stock_in_total||0):Math.abs(v.stock_out_total||0)).formatBangladeshCurrencyType("amount")}</td>
                           </tr> `;
                   });
                }
               
               if(response.journal_out){
                   response.journal_out[0]? html+=`<tr ><td colspan="5" style="font-weight: bolder;font-size: 16px;">Journal</td></tr> `:'';
                    $.each(response.journal_out, function(key, v) {
                        total_outwards_qty+=(v.stock_out_qty||0);total_outwards_value+=(v.stock_out_total||0);
                        html+=`<tr id="${6}"  ledger_id="${v.ledger_head_id}" class="left left-data editIcon table-row">
                                <td  style="width: 1%;  border: 1px solid #ddd;">${(key+1)}</td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;color: #0B55C4"class="text-wrap" ledger_id="${v.ledger_head_id}">
                                    ${stockItemAnalysisDetailsIdWise(v?.ledger_head_id,v?.ledger_name||v?.voucher_name||'',6)}
                                </td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_out_qty||0).formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">
                                    ${out_ward_column ? 
                                        dividevalue((v.stock_out_total||0),(v.stock_out_qty||0)).formatBangladeshCurrencyType("rate")
                                    :''}
                                </td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_out_total||0).formatBangladeshCurrencyType("amount")}</td>
                        </tr> `;
                    });
               }
               if(response.stock_journal_out){
                   response.stock_journal_out[0]? html+=`<tr ><td colspan="5" style="font-weight: bolder;font-size: 16px;"> Stock Journal</td></tr> `:'';
                    $.each(response.stock_journal_out, function(key, v) {
                        total_outwards_qty+=(v.stock_out_qty||0);total_outwards_value+=(v.stock_out_total||0);
                        html+=`<tr id="${21}"  ledger_id="${v.ledger_head_id}" class="left left-data editIcon table-row">
                                <td  style="width: 1%;  border: 1px solid #ddd;">${(key+1)}</td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;color: #0B55C4"class="text-wrap">
                                    ${stockItemAnalysisDetailsIdWise(v?.ledger_head_id,v?.ledger_name||v?.voucher_name||'',21)}
                                </td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_out_qty||0).formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">
                                    ${out_ward_column ? 
                                        dividevalue((v.stock_out_total||0),(v.stock_out_qty||0)).formatBangladeshCurrencyType("rate")
                                    :''}
                                </td>
                                <td  style="width: 3%;  border: 1px solid #ddd; font-size: 16px;" class="text-end">${(v.stock_out_total||0).formatBangladeshCurrencyType("amount")}</td>
                        </tr> `;
                    });
               }
               html+=` <tr>
                        <td style="font-weight: bolder;font-size: 18px;" colspan="2">Total :</td>
                        <td class="text-end" style="font-size: 16px;font-weight: bolder;">${total_outwards_qty.formatBangladeshCurrencyType("quantity", response.unit_of_measure.symbol)}</td>
                        <td class="text-end" style=";font-size: 16px;font-weight: bolder;">
                            ${out_ward_column ? 
                            Math.abs(dividevalue(total_outwards_value,total_outwards_qty)).formatBangladeshCurrencyType("rate")
                        :''}
                        </td>
                        <td class="text-end" style=";font-size: 16px;font-weight: bolder;">${total_outwards_value.formatBangladeshCurrencyType("amount")}</td>
                    </tr> `;
        $(".item_body").html(html);
        get_hover();
    }

// stock item analysis function
function get_stock_item_analysis_initial_show(){
        print_date();
        $.ajax({
            url: '{{ route("stock-item-analysis-data") }}',
                method: 'GET',
                data: {
                    to_date:$('.to_date').val(),
                    from_date:$('.from_date').val(),
                    stock_item_id:$(".stock_item_id").val(),
                    godown_id:$(".godown_id").val(),
                    purchase:$(".purchase").is(':checked')?$(".purchase").val():'',
                    grn:$(".grn").is(':checked')?$(".grn").val():'',
                    purchase_return:$(".purchase_return").is(':checked')?$(".purchase_return").val():'',
                    journal_in:$(".journal_in").is(':checked')?$(".journal_in").val():'',
                    stock_journal_in:$(".stock_journal_in").is(':checked')?$(".stock_journal_in").val():'',
                    sales_return:$(".sales_return").is(':checked')?$(".sales_return").val():'',
                    gtn:$(".gtn").is(':checked')?$(".gtn").val():'',
                    sales:$(".sales").is(':checked')?$(".sales").val():'',
                    journal_out:$(".journal_out").is(':checked')?$(".journal_out").val():'',
                    stock_journal_out:$(".stock_journal_out").is(':checked')?$(".stock_journal_out").val():'',
                    sort_by:$(".sort_by:checked").val(),
                    stort_type:$(".stort_type:checked").val(),
                },
                dataType: 'json',
                success: function(response) {
                    get_stock_item_analysis(response.data)
                },
                error : function(data,status,xhr){
                     Unauthorized(data.status);
                }
        });
}

function local_store_stock_item_analysis_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("stock_item_id", '.stock_item_id');
        let godown = getStorage("godown");
        if (godown) {
            $('.godown_id').val(godown.split(",")).trigger('change');
        }
       let sort_by= getStorage("sort_by");
       $(".sort_by[value='" + sort_by + "']").prop("checked", true);
       let stort_type=getStorage("stort_type");
       $(".stort_type[value='" + stort_type + "']").prop("checked", true);
        getStorage("purchase", '.purchase', 'checkbox');
        getStorage("grn", '.grn', 'checkbox');
        getStorage("purchase_return", '.purchase_return', 'checkbox');
        getStorage("journal_in", '.journal_in', 'checkbox');
        getStorage("stock_journal_in", '.stock_journal_in', 'checkbox');
        getStorage("gtn", '.gtn', 'checkbox');
        getStorage("sales", '.sales', 'checkbox');
        getStorage("sales_return", '.sales_return', 'checkbox');
        getStorage("journal_out", '.journal_out', 'checkbox');
        getStorage("stock_journal_out", '.stock_journal_out', 'checkbox');
        getStorage("in_ward_column", '.in_ward_column','checkbox');
        getStorage("out_ward_column", '.out_ward_column','checkbox');
       
    }

    function local_store_stock_item_analysis_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("stock_item_id", $('.stock_item_id').val());
        setStorage("godown", $('.godown_id').val());
        setStorage("purchase", $('.purchase').is(':checked'));
        setStorage("sort_by", $(".sort_by:checked").val());
        setStorage("stort_type", $(".stort_type:checked").val());
        setStorage("grn", $('.grn').is(':checked'));
        setStorage("purchase_return", $('.purchase_return').is(':checked'));
        setStorage("journal_in", $('.journal_in').is(':checked'));
        setStorage("stock_journal_in", $('.stock_journal_in').is(':checked'));
        setStorage("gtn", $('.gtn').is(':checked'));
        setStorage("sales", $('.sales').is(':checked'));
        setStorage("journal_out", $('.journal_out').is(':checked'));
        setStorage("stock_journal_out", $('.stock_journal_out').is(':checked'));
        setStorage("sales_return", $('.sales_return').is(':checked'));
        setStorage("in_ward_column", $('.in_ward_column').is(':checked'));
        setStorage("out_ward_column", $('.out_ward_column').is(':checked'));
    }
    
$(document).ready(function(){
    // table header fixed
    let display_height=$(window).height();
    $('.tableFixHead_stock_group_analysis').css('height',`${display_height-300}px`);
});

// group item analysis route
// $('.sd').on('click','.table-row',function(e){
//         e.preventDefault();
//         let  ledger_head_id=$(this).closest('tr').attr('ledger_id');
//         let stock_item_id=$(".stock_item_id").val();
//         let godown_id=$('.godown_id').val();
//         let form_date=$('.from_date').val();
//         let to_date=$('.to_date').val();
//         let purchase_in=$(this).closest('tr').attr('id')==10?$('.purchase').val():0;
//         let grn_in=$(this).closest('tr').attr('id')==24?$('.grn').val():0;
//         let purchase_return_in=$(this).closest('tr').attr('id')==29?$('.purchase_return').val():0;
//         let journal_in=$(this).closest('tr').attr('id')==6?$('.journal').val():0;
//         let stock_journal_in=$(this).closest('tr').attr('id')==21?$('.stock_journal_in').val():0;
//         let sales_return_out=$(this).closest('tr').attr('id')==25?$('.sales_return').val():0;
//         let gtn_out=$(this).closest('tr').attr('id')==23?$('.gtn').val():0;
//         let sales_out=$(this).closest('tr').attr('id')==19?$('.sales').val():0;
//         let journal_out=$(this).closest('tr').attr('id')==6?$('.journal').val():0;
//         let stock_journal_out=$(this).closest('tr').attr('id')==21?$('.stock_journal_out').val():0;
//         let sort_by=$(".sort_by:checked").val();
//         let stort_type=$(".stort_type:checked").val();
//         url = "{{route('stock-item-analysis-details-id-wise', ['ledger_head_id'=>':ledger_head_id','stock_item_id' =>':stock_item_id','godown_id'=>':godown_id','form_date' =>':form_date','to_date' =>':to_date','purchase_in'=>':purchase_in','grn_in'=>':grn_in','purchase_return_in'=>':purchase_return_in','journal_in'=>':journal_in','stock_journal_in'=>':stock_journal_in','sales_return_out'=>':sales_return_out','gtn_out'=>':gtn_out','sales_out'=>':sales_out','journal_out'=>':journal_out','stock_journal_out'=>':stock_journal_out','sort_by'=>':sort_by','stort_type'=>':stort_type'])}}";

//         url = url.replace(':ledger_head_id',ledger_head_id);
//         url = url.replace(':stock_item_id',stock_item_id);
//         url = url.replace(':godown_id',godown_id);
//         url = url.replace(':form_date',form_date);
//         url = url.replace(':to_date',to_date);
//         url=url.replace(':purchase_in',purchase_in);
//         url = url.replace(':grn_in',grn_in);
//         url = url.replace(':purchase_return_in',purchase_return_in);
//         url = url.replace(':journal_in',journal_in);
//         url = url.replace(':stock_journal_in',stock_journal_in);
//         url=url.replace(':sales_return_out',sales_return_out);
//         url=url.replace(':gtn_out',gtn_out);
//         url = url.replace(':sales_out',sales_out);
//         url = url.replace(':journal_out',journal_out);
//         url=url.replace(':stock_journal_out',stock_journal_out);
//         url=url.replace(':sort_by',sort_by);
//         url=url.replace(':stort_type',stort_type);
//         window.open(url,'_blank');
//     });
</script>
@endpush
@endsection
