@extends('layouts.backend.app')
@section('title','Stock Group Summary Store')
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
    .op_min_width {
        min-width: 135px
    }
    .in_min_width {
        min-width: 135px
    }
    .clo_min_width {
        min-width: 135px
    }
    body {
        overflow: auto !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
                                line-height: 18px !important;
    }
</style>
@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
'id' =>'exampleModal',
'class' =>'modal fade',
'page_title'=>'Stock Group Summary Store',
'size'=>'modal-xl',
'page_unique_id'=>17,
'godown'=>'yes',
'stockGroup'=>'yes',
'title'=>'Stock Group Summary Store Reports ',
'daynamic_function'=>'get_stock_group_summary_initial_show'
])
@endcomponent
<!-- add component-->
@component('components.report', [
'title' => 'Stock Group Summary Store',
'print_layout'=>'portrait',
'print_header'=>'Stock Group Summary Store ',
'user_privilege_title'=>'StockGroupSummaryStore',
'print_date'=>1,
'report_setting_model'=>'report_setting_model',
'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
<form id="stock_group_summary_form" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row ">
        <div class="col-md-3">
            <label>Stock Group :</label>
            <select name="stock_group_id" class="form-control  js-example-basic-single  stock_group_id" required>
                {{-- <option value="">--Select--</option> --}}
                <option value="0">Primary</option>
                {!!html_entity_decode($stock_group)!!}
            </select>
            <label>Godown Name :</label>
            <select name="godown_id" class="form-control  js-example-basic-single godown_id" required>
                <option value="0">All</option>
                @foreach($godowns as $godown)
                <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                @endforeach
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
                <div class="form-group mb-0" style="position: relative">
                    <input class="form-check-input stock_with_out_item_group_check" type="checkbox" name="op_qty" value="1" checked >
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Show Stock Item/s with Group
                    </label><br>
                </div>
            </div>
        </div>
        <div class="col-md-4 check_box_cal">
            <label></label>
            <div class="form-group mb-0" style="position: relative">
                <label class="fs-6 op_min_width">Opening Blance :</label>
                <input class="form-check-input op_qty_check" type="checkbox" name="op_qty" value="1" {{ isset($type)?($type==1 ? ' checked' : ''):''  }} >
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Quantity
                </label>
            </div>
            <div class="form-group m-0 p-0" style="position: relative">
                <label class="fs-6 in_min_width">Inwards Blance :</label>
                <input class="form-check-input in_qty_check" type="checkbox" name="in_qty" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Quantity
                </label>
            </div>
            <div class="form-group m-0 p-0" style="position: relative">
                <label class="fs-6 ">Outwards Blance :</label>
                <input class="form-check-input out_qty_check" type="checkbox" name="out_qty" value="1" >
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Quantity
                </label>
            </div>
            <div class="form-group m-0 p-0" style="position:relative">
                <label class="fs-6 clo_min_width">Closing Blance :</label>
                <input class="form-check-input closing_qty_check" type="checkbox" name="clo_qty" value="1"  {{ isset($type)?($type==2 ? ' checked' : ''):''  }} {{$type?? "checked"}}>
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Quantity
                </label>
            </div>
        </div>
        <div class="col-md-2">
            <label></label><br>
            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;"><span class="m-1 m-t-1"></span><span>Search</span></button>
        </div>
    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_stock_group_summary">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <tr>
                <th  class="th align-middle" style="width: 1%;">SL</th>
                <th  class="th align-middle " style="width: 5%;table-layout: fixed;">Particulars</th>
                <th  class="th opening" style=" width: 5%;">Opening Balance</th>
                <th class="th inwards" style=" width: 5%;">Inward Balance</th>
                <th  class="th outwards" style=" width: 5%; ">Outward Balance</th>
                <th  class="th closing" style=" width: 5%;">Closing Balance</th>

            </tr>

        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;" class="td_th"></th>
                <th style="width: 5%;" class="td_th text-end">Total :</th>
                <th style="width: 2%; " class="th total_opening_qty opening_qty_check text-end"></th>
                <th style="width: 2%;" class="th total_inwards_qty inwards_qty_check text-end"></th>
                <th style="width: 2%;" class="th total_outwards_qty outwards_qty_check text-end"></th>
                <th style="width: 3%;" class="th total_clasing_qty closings_qty_check text-end"></th>

            </tr>
        </tfoot>
    </table>
</div>
@endslot
@endcomponent
<br>
@push('js')
<!-- table hover js -->
<script>
    let total_opening_qty = 0;
    total_inwards_qty = 0;
    total_clasing_qty = 0;
    initial=1;
    i = 1;
    // stock item get id check
    if("{{$from_date??0}}"!=0){
        $('.from_date').val('{{$from_date??0}}');
    }
    if("{{$to_date??0}}"!=0){

        $('.to_date').val('{{$to_date??0}}');
        if("{{$stock_group_id??0}}"!=0){}else{ $('.stock_group_id').val(0);}
    }else{
        // initial data show condition
        if(getStorage("start_date", '.from_date')){ initial=0}else{ initial=1}
    }
    if("{{$grodown_id??0}}"!=0){
        $('.grodown_id').val('{{$grodown_id??0}}');
    }
    if("{{$stock_group_id??0}}"!=0){

        $('.stock_group_id').val('{{$stock_group_id??0}}').trigger('change');
    }

    // stock group summary
    $(document).ready(function() {

          //local store check
            if("{{$type??0}}"!=0){
                local_store_stock_group_summary_set_data();
                initial=0;
            }
            if("{{$stock_group_id??0}}"!=0){
                local_store_stock_group_summary_set_data();
                initial=0;

            }else{
                local_store_stock_group_summary_get();
            }
            get_stock_group_summary_initial_show();

        $("#stock_group_summary_form").submit(function(e) {
            local_store_stock_group_summary_set_data();
            print_date();

            $(".modal").show();
            total_opening_qty = 0;
            total_inwards_qty = 0;
            total_outwards_qty = 0;
            total_clasing_qty = 0;
            i = 1;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("report-stock-group-summary-store-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_stock_group_summary(response);

                },
                error: function(data, status, xhr) {
                    Unauthorized(data.status);
                }
            });
        });


    });

     // stock group summary  function
      function get_stock_group_summary(response) {
            const worker = new Worker("{{asset('report_workerjs/stockGroupSummaryWorker.js')}}");
            worker.onmessage = function (e) {
                const { html, totals } = e.data;
                document.getElementById('myTable').innerHTML =html;
                 total_calcution_data(totals);
                 checkbox_check();
                 font_size_auto_change();
                 get_hover();
            };

            const checkboxStates = {
                op_qty_check: $(".op_qty_check").is(':checked'),
                op_rate_check: $(".op_rate_check").is(':checked'),
                op_value_check: $(".op_value_check").is(':checked'),
                in_qty_check: $(".in_qty_check").is(':checked'),
                in_rate_check: $(".in_rate_check").is(':checked'),
                in_value_check: $(".in_value_check").is(':checked'),
                out_qty_check: $(".out_qty_check").is(':checked'),
                out_rate_check: $(".out_rate_check").is(':checked'),
                out_value_check: $(".out_value_check").is(':checked'),
                closing_qty_check: $(".closing_qty_check").is(':checked'),
                closing_rate_check: $(".closing_rate_check").is(':checked'),
                closing_value_check: $(".closing_value_check").is(':checked'),
                stock_with_out_item_group_check: $(".stock_with_out_item_group_check").is(':checked'),
                show_closing_is: $("#show_closing_is").is(':checked'),
                quantity_decimals:$('.quantity_decimals:checked').val(),
                show_quantity_comma_is:$('.show_quantity_comma').is(':checked'),
                show_units_of_measure_is:$('.show_units_of_measure').is(':checked'),
                amount_decimals:$('.amount_decimals:checked').val(),
                rate_decimals:$('.rate_decimals:checked').val()
            };
            const children_sum =response.data.sum_of_children;
            worker.postMessage({ arr:response.data.stock_group_summary, children_sum: children_sum, checkboxStates });



        }

    $(document).ready(function() {
        // table header fixed
        let display_height = $(window).height();
        $('.tableFixHead_stock_group_summary').css('height', `${display_height-115}px`);
    });

    function get_stock_group_summary_initial_show(){
           local_store_stock_group_summary_get();
            print_date();
            checkbox_check();
            $(".modal").show();
            $.ajax({
                url: '{{ route("report-stock-group-summary-store-data") }}',
                method: 'GET',
                data: {
                    stock_group_id:$('.stock_group_id').val(),
                    godown_id:$('.godown_id').val(),
                    from_date:$('.from_date').val(),
                    to_date :$('.to_date').val(),
                    initial :initial,
                },
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_stock_group_summary(response)
                },
                error: function(data, status, xhr) {
                    Unauthorized(data.status);
                }
            });
    }
    function total_calcution_data(totals){
            $('.total_opening_qty').text(totals.total_opening_qty.formatBangladeshCurrencyType("quantity"));
            $('.total_opening_rate').text((((Math.abs(totals.total_opening_value) / Math.abs(totals.total_opening_qty)) || 0)).formatBangladeshCurrencyType("rate"));
            $('.total_opening_value').text((totals.total_opening_value || 0).formatBangladeshCurrencyType("amount"));
            $('.total_inwards_qty').text(totals.total_inwards_qty.formatBangladeshCurrencyType("quantity"));
            $('.total_inwards_rate').text(((Math.abs(totals.total_inwards_value) / Math.abs(totals.total_inwards_qty)) || 0).formatBangladeshCurrencyType("rate"));
            $('.total_inwards_value').text(totals.total_inwards_value.formatBangladeshCurrencyType("amount"));
            $('.total_outwards_qty').text(totals.total_outwards_qty.formatBangladeshCurrencyType("quantity"));
            $('.total_outwards_rate').text(((Math.abs(totals.total_outwards_value) / Math.abs(totals.total_outwards_qty)) || 0).formatBangladeshCurrencyType("rate"));
            $('.total_outwards_value').text(totals.total_outwards_value.formatBangladeshCurrencyType("amount"));
            $('.total_clasing_qty').text(totals.total_clasing_qty.formatBangladeshCurrencyType("quantity"));
            $('.total_clasing_rate').text(((Math.abs(totals.total_clasing_value) / Math.abs(totals.total_clasing_qty)) || 0).formatBangladeshCurrencyType("rate"));
            $('.total_clasing_value').text(totals.total_clasing_value.formatBangladeshCurrencyType("amount"));
    }

    //redirect page
    $(document).ready(function() {
        font_size_auto_change();

        // stock item month wise summary route
        $('.sd').on('click', '.item_name', function(e) {
            e.preventDefault();
            let id = $(this).closest('tr').attr('id');
            let form_date = $('.from_date').val();
            let to_date = $('.to_date').val();
            let godown_id = $('.godown_id').val();
            url = "{{route('stock-item-monthly-summary-store-id-wise', ['id' =>':id', 'form_date' =>':form_date','to_date' =>':to_date','godown_id'=>':godown_id'])}}";
            url = url.replace(':id', id);
            url = url.replace(':form_date', form_date);
            url = url.replace(':to_date', to_date);
            url = url.replace(':godown_id', godown_id);
            window.open(url, '_blank');
        });
       // stock item stock group wise summary route
        $('.sd').on('click', '.table-row_tree', function(e) {
            e.preventDefault();
            let id = $(this).closest('tr').attr('id');
            let form_date = $('.from_date').val();
            let to_date = $('.to_date').val();
            let godown_id = $('.godown_id').val();
            url = "{{route('report-stock-group-summary-store-id-wise', ['id' =>':id', 'form_date' =>':form_date','to_date' =>':to_date','godown_id'=>':godown_id'])}}";
            url = url.replace(':id', id);
            url = url.replace(':form_date', form_date);
            url = url.replace(':to_date', to_date);
            url = url.replace(':godown_id', godown_id);
            window.open(url, '_blank');
        })
    });

    $(window).on('resize', function() {
        font_size_auto_change();
    });
    $(window).trigger('resize');

    //automatic font size
    function font_size_auto_change() {
        // Get the current window width
        var windowWidth = $(window).width();
        // For example, let's make the font size 2% of the window width
        var newFontSize = windowWidth * 0.008;
        if (windowWidth > 768) {
            // Apply the new font size to the target element(s)
            let coloum = $('.check_box_cal').find('input[type=checkbox]:checked').length;
            let addedSize = 5.1 - (.425 * coloum);
            $(document).find('table').css({
                'font-size': `${Math.round(newFontSize+addedSize)}px`
            });
            $(document).find('table tbody tr td p').css({
                'font-size': `${Math.round(newFontSize+addedSize)}px`
            });
        }
    };

    // local store in bowser
    function local_store_stock_group_summary_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("stock_group_id", '.stock_group_id');
        getStorage("godown_id", '.godown_id');
        getStorage("op_qty_check", '.op_qty_check', 'checkbox');
        getStorage("op_rate_check", '.op_rate_check', 'checkbox');
        getStorage("op_value_check", '.op_value_check', 'checkbox');
        getStorage("in_qty_check", '.in_qty_check', 'checkbox');
        getStorage("in_rate_check", '.in_rate_check', 'checkbox');
        getStorage("in_value_check", '.in_value_check', 'checkbox');
        getStorage("out_qty_check", '.out_qty_check', 'checkbox');
        getStorage("out_rate_check", '.out_rate_check', 'checkbox');
        getStorage("out_value_check", '.out_value_check', 'checkbox');
        getStorage("closing_qty_check", '.closing_qty_check', 'checkbox');
        getStorage("closing_rate_check", '.closing_rate_check','checkbox');
        getStorage("closing_value_check", '.closing_value_check','checkbox');
        getStorage("stock_with_out_item_group_check", '.stock_with_out_item_group_check','checkbox');

    }

    function local_store_stock_group_summary_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("stock_group_id", $('.stock_group_id').val());
        setStorage("godown_id", $('.godown_id').val());
        setStorage("op_qty_check", $(".op_qty_check").is(':checked'));
        setStorage("op_rate_check",$(".op_rate_check").is(':checked'));
        setStorage("op_value_check", $(".op_value_check").is(':checked'));
        setStorage("in_qty_check", $(".in_qty_check").is(':checked'));
        setStorage("in_rate_check", $(".in_rate_check").is(':checked'));
        setStorage("in_value_check", $(".in_value_check").is(':checked'));
        setStorage("out_qty_check",$(".out_qty_check").is(':checked'));
        setStorage("out_rate_check", $(".out_rate_check").is(':checked'));
        setStorage("out_value_check", $(".out_value_check").is(':checked'));
        setStorage("closing_qty_check", $(".closing_qty_check").is(':checked'));
        setStorage("closing_rate_check", $('.closing_rate_check').is(':checked'));
        setStorage("closing_value_check", $('.closing_value_check').is(':checked'));
        setStorage("stock_with_out_item_group_check", $('.stock_with_out_item_group_check').is(':checked'));
    }

    // check box checking
    function checkbox_check() {

        // checking colspan table
        $('.opening').attr('colspan', ($(".op_qty_check").is(':checked') ? 1 : 0) + ($(".op_rate_check").is(':checked') ? 1 : 0) + ($(".op_value_check").is(':checked') ? 1 : 0));
        $('.opening').attr('colspan') == 0 ? $('.opening').addClass("d-none") : $('.opening').removeClass("d-none");
        $('.inwards').attr('colspan', ($(".in_qty_check").is(':checked') ? 1 : 0) + ($(".in_rate_check").is(':checked') ? 1 : 0) + ($(".in_value_check").is(':checked') ? 1 : 0));
        $('.inwards').attr('colspan') == 0 ? $('.inwards').addClass("d-none") : $('.inwards').removeClass("d-none");
        $('.outwards').attr('colspan', ($(".out_qty_check").is(':checked') ? 1 : 0) + ($(".out_rate_check").is(':checked') ? 1 : 0) + ($(".out_value_check").is(':checked') ? 1 : 0));
        $('.outwards').attr('colspan') == 0 ? $('.outwards').addClass("d-none") : $('.outwards').removeClass("d-none");
        $('.closing').attr('colspan', ($(".closing_qty_check").is(':checked') ? 1 : 0) + ($(".closing_rate_check").is(':checked') ? 1 : 0) + ($(".closing_value_check").is(':checked') ? 1 : 0));
        $('.closing').attr('colspan') == 0 ? $('.closing').addClass("d-none") : $('.closing').removeClass("d-none");


        //checking condition
         if($(".op_qty_check" ).is(':checked')==true){
            $(".opening_qty_check").removeClass("d-none");
        }else{
            $(".opening_qty_check").addClass("d-none");
        }

        if($(".op_rate_check" ).is(':checked')==true){
            $(".opening_rate_check").removeClass("d-none");
        }else{
            $(".opening_rate_check").addClass("d-none");
        }

        if($(".op_value_check" ).is(':checked')==true){
            $(".opening_value_check").removeClass("d-none");
        }else{
            $(".opening_value_check").addClass("d-none");
        }

        if ($(".in_qty_check").is(':checked') == true) {
             $(".inwards_qty_check").removeClass("d-none");
        } else {
            $(".inwards_qty_check").addClass("d-none");
        }

        if ($(".in_rate_check").is(':checked') == true) {
            $(".inwards_rate_check").removeClass("d-none");
        } else {
            $(".inwards_rate_check").addClass("d-none");
        }

        if ($(".in_value_check").is(':checked') == true) {
            $(".inwards_value_check").removeClass("d-none");
        } else {
            $(".inwards_value_check").addClass("d-none");
        }

        if ($(".out_qty_check").is(':checked') == true) {
            $(".outwards_qty_check").removeClass("d-none");
        } else {
            $(".outwards_qty_check").addClass("d-none");
        }

        if ($(".out_rate_check").is(':checked') == true) {
            $(".outwards_rate_check").removeClass("d-none");
        } else {
            $(".outwards_rate_check").addClass("d-none");
        }

        if ($(".out_value_check").is(':checked') == true) {
            $(".outwards_value_check").removeClass("d-none");
        } else {
            $(".outwards_value_check").addClass("d-none");
        }

        if ($(".closing_qty_check").is(':checked') == true) {
            $(".closings_qty_check").removeClass("d-none");
        } else {
            $(".closings_qty_check").addClass("d-none");
        }

        if ($(".closing_rate_check").is(':checked') == true) {
            $(".closings_rate_check").removeClass("d-none");
        } else {
            $(".closings_rate_check").addClass("d-none");
        }

        if ($(".closing_value_check").is(':checked') == true) {
            $(".closings_value_check").removeClass("d-none");
        } else {
            $(".closings_value_check").addClass("d-none");
        }
    }
</script>
@endpush
@endsection
