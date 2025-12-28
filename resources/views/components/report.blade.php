
<link rel="stylesheet" type="text/css" href="{{asset('custom_responsive/custom_report_responsive.css')}}">
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

    .table-scroll thead tr:nth-child(2) th {
        top: 20px !important;
    }
    .display-none{
        display: none;
    }
     .select2-container--default .select2-results>.select2-results__options{
       max-height: 650px !important;
       overflow-y: auto !important;
    }
</style>
<div class="coded-main-container navChild ">
    <div class="pcoded-content">
        <div class="pcoded-inner-content" >
            <!-- Main-body start -->
            <div class="main-body p-0  side-component">
                <div class="page-wrapper m-t-0 p-0">
                    <!-- Page-header start -->
                    <div class="page-header ms-2 me-2 mb-2 p-0">
                        <div class="col-lg-12 row">
                            <div class="col-lg-5">
                                <div class="page-header-title">
                                    <h4 id="report_header_title">{{ $title ?? '' }}</h4>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div style="float: right;margin-left:9px">
                                    <a style=" float:right;text-decoration: none; " href="{{ url()->previous() }}"><span class="fa fa-times-circle-o m-1" style="font-size:27px; color:#ff6666;"></span><span style="float:right;margin:2px; padding-top:5px; ">Close</span></a>
                                </div>
                                <div style="float: right; margin-left: 5px;"> <a style=" float:right; text-decoration: none; cursor: pointer" data-toggle="modal" data-target="#exampleModal" class="{{$report_setting_model ?? 'd-none' }}"><span class="fa fa-cog m-1" style="font-size:27px;  color:Green;"></span><span style="float:right;margin:2px; padding-top:5px; ">Setting</span></a></div>
                                <div style="float: right; margin-left: 5px;"> <a style=" float:right; text-decoration: none; cursor: pointer" class="{{$report_setting_mail ?? 'd-none' }}"><span class="fa fa-envelope m-1" style="font-size:27px;  color:black;"></span><span style="float:right;margin:2px; padding-top:5px; ">Mail</span></a></div>
                                @if(user_privileges_check('report',$user_privilege_title,'print_role'))
                                    <div style="float: right;margin-left:9px">
                                        <a style="float:right;text-decoration: none;cursor: pointer; " class="print" onclick="print_html('{{$print_layout}}','{{$print_header}}','{{$print_date??null}}','{{$party_name??null}}')"><span class="fa fa-print m-1" style="font-size:27px; color:teal;"></span><span style="float:right;margin:2px; padding-top:5px; ">Print</span></a>
                                    </div>
                                    <div style="float: right;margin-left:9px">
                                        <a style="float:right;text-decoration: none;cursor: pointer; " class="excel" onclick="exportTableToExcel('{{$print_header}}')"><span class="fa fa-file-excel-o m-1" style="font-size:25px; color:Gray;"></span><span style="float:right;margin:2px; padding-top:5px; ">Excel</span></a>
                                    </div>
                                    <div style="float: right;margin-left:9px">
                                        <a style="float:right;text-decoration: none;cursor: pointer;" class="pdf" onclick="generateTable('{{$print_header}}')"><span class="fa fa-file-pdf-o m-1" style="font-size:25px; color:MediumSeaGree;"></span><span style="float:right;margin:2px; padding-top:5px; ">Pdf</span></a>
                                    </div>
                                @endif
                                <div style="float: right; width:200px;">
                                    <input type="text" id="myInput" style="border-radius: 5px" class="form-control form-control pb-1" width="100%" placeholder="searching">
                                </div>
                            </div>
                            <hr style="margin-bottom: 0px;">
                        </div>
                        {{$header_body}}
                    </div>
                </div>
            </div>
            <!-- Page-body start -->
            <div class="page-body left-data">
                <div class="row">
                    <div class="col-xl-12">
                        <!-- Zero config.table start -->
                        <div class="card">
                            <div class="card-block table-responsive table_content">
                                {{$main_body}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal">
    <div class="center">
        <img alt="" src="{{asset('loding-image/loading_icon.gif')}}" />
    </div>
</div>
<script>
    let scrollValue = 0;
    let bodyScrollValue = 0;
    function set_scroll_table() {
    }
    function scroll_table() {
    }
    function scroll_table_to_prev() {
        let reportName=$('#report_header_title')?.text()?.replaceAll(' ','_');
        let scrollTop = localStorage.getItem(reportName);
        let bodyScrollTop = localStorage.getItem(`${reportName}_body`);
        if (scrollTop > 0) {
            $(document).find('.table-responsive').scrollTop(scrollTop);
        }
        if(bodyScrollTop>0){
            $(document).find('body').scrollTop(bodyScrollTop);
        }
    }
    function reset_scroll_height(){
        let reportName=$('#report_header_title')?.text()?.replaceAll(' ','_');
        localStorage.setItem(reportName, null);
        localStorage.setItem(`${reportName}_body`, null);
        $(document).find('.table-responsive').scrollTop(0);
        $(document).find('body').scrollTop(0);
    }
    $(document).ready(function(){
        scroll_table_to_prev();
        $('.table-responsive').on('scroll', function () {
            scrollValue = $(this).scrollTop();
        });
        $('body').on('scroll', function () {
            bodyScrollValue = $(this).scrollTop();
        });
    })

    $(document).on('click', 'tr', function() {
        let reportName=$('#report_header_title')?.text()?.replaceAll(' ','_');
        localStorage.setItem(reportName, scrollValue);
        localStorage.setItem(`${reportName}_body`, bodyScrollValue);
    })

    let select2SelectedDat = {};
    $(document).ready(function() {
        initializeSelect2Godown();
    });

    function initializeSelect2Godown(godown_in = null, all = 0) {
        const selectElement = $('.js-example-basic-multiple');
        // Initialize Select2
        selectElement.select2({
            placeholder: "--Select--",
            allowClear: true
        });
        if (selectElement.val()[0] == 0) {
            select2SelectedDat[0] = 1;
        }
        // Listen for changes
        selectElement.unbind('change').on('change', function() {
            let selectedValues = $(this).val();
            if (selectedValues.length > 1 && selectedValues[0] == 0) {
                if (select2SelectedDat.hasOwnProperty(0)) {
                    selectElement.val(selectedValues.filter(x => x != 0)).trigger('change');
                    delete select2SelectedDat[0];
                } else {
                    selectElement.val([0]).trigger('change');
                    selectedValues[0] = 1;
                }
            } else {
                if (selectedValues.includes('0')) {
                    select2SelectedDat[0] = 1;
                } else if (select2SelectedDat.hasOwnProperty(0)) {
                    delete select2SelectedDat[0];
                }
            }
        });

    }

    $('.stock_item_auto_completed').each(function() {
        // initializeAutocompleteItem(this);
    });
    $(document).on('keyup focus', '.stock_item_auto_completed', function() {
        initializeAutocompleteItem.call($(this));
    });
    function initializeAutocompleteItem() {
        let homeRoute=$("#homeRoute").attr("href");
        $(this).autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: `${homeRoute}/searching-stock-item`,
                    data: {
                        name: request.term,
                    },
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.product_name,
                                value: item.product_name,
                                stock_item_id: item.stock_item_id
                            };
                        }));
                    }
                });
            },
            change: function(event, ui) {
                if (!ui.item) {
                    $(this).val('');
                    $(this).focus();
                }
            },
            select: function(event, ui) {
                $(this).val(ui.item.value); // Set the ledger name
                $('#stock_item_id').val(ui.item.stock_item_id);
            }
        });
    }



    $('#stock_item_id').change(function(){
        let homeRoute=$("#homeRoute").attr("href");
        if(!$('.stock_item_auto_completed').val().trim()){
            let id=$(this).val();
            $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: `${homeRoute}/searching-stock-item-name`,
                    data: {
                        id:id
                    },
                    success: function(data) {
                        $('.stock_item_auto_completed').val(data?.product_name) 
                    }
                });
        }
    });
    function redirectVoucherIdWise(voucher_type_id, tran_id,voucher_name) {
        let url_get_1 = $("#homeRoute").attr("href");
        let path = '';
        
        switch(voucher_type_id) {
            case 14: path = '/voucher-receipt/edit/' + tran_id; break;
            case 8: path = '/voucher-payment/' + tran_id + '/edit'; break;
            case 1: path = '/voucher-contra/' + tran_id + '/edit'; break;
            case 10: path = '/voucher-purchase/' + tran_id + '/edit'; break;
            case 24: path = '/voucher-grn/' + tran_id + '/edit'; break;
            case 19: path = '/voucher-sales/' + tran_id + '/edit'; break;
            case 23: path = '/voucher-gtn/' + tran_id + '/edit'; break;
            case 29: path = '/voucher-purchase-return/' + tran_id + '/edit'; break;
            case 22: path = '/voucher-transfer/' + tran_id + '/edit'; break;
            case 25: path = '/voucher-sales-return/' + tran_id + '/edit'; break;
            case 21: path = '/voucher-stock-journal/' + tran_id + '/edit'; break;
            case 6: path = '/voucher-journal/' + tran_id + '/edit'; break;
            case 28: path = '/voucher-commission/' + tran_id + '/edit'; break;
            case 20: path = '/voucher-sales-order/' + tran_id + '/edit'; break;
            default: path = ''; break;
        }
        
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" href="${url_get_1 + path}">${voucher_name || ''}</a><spna class="display-none">${voucher_name || ''}</spna>`;
    }
    

    function reportCompanyStatisticsMonthlyData(id,name){
        let from_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        let url = "{{route('report-company-statistics-monthly-data', ['voucher_id' =>':voucher_id','form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':voucher_id',id);
        url = url.replace(':form_date',from_date);
        url = url.replace(':to_date',to_date);
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                    href="${url}">${name || ''}</a>
                    <span class="display-none">${name || ''}</span>`;

    }

    function voucherMonthIdWise(id,name){
        let from_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        let voucher_id=$('.voucher_id').val();
        let url = "{{route('voucher-month-id-wise', ['voucher_id'=>':voucher_id','date' =>':date','from_date' =>':from_date','to_date' =>':to_date'])}}";
        url = url.replace(':from_date',from_date);
        url = url.replace(':to_date',to_date);
        url = url.replace(':date',id);
        url = url.replace(':voucher_id',voucher_id);

        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                    href="${url}">${name || ''}</a>
                    <span class="display-none">${name || ''}</span>`;
    }

    

    function accountLedgerMonthlySummaryIdWise(id,name){
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        let voucher_id=$('.voucher_id').val();
        let url = "{{route('account-ledger-monthly-summary-id-wise', ['ledger_id' =>':ledger_id', 'form_date' =>':form_date','to_date' =>':to_date','voucher_id' =>':voucher_id'])}}";
        url = url.replace(':ledger_id',id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        url = url.replace(':voucher_id',voucher_id); 
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                    href="${url}">${name || ''}</a>
                    <span class="display-none">${name || ''}</span>`;

    }

    function accountLedgerVoucherMonthIdWise(date,name){
        let ledger_id=$('.ledger_id').val();
        let from_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        let voucher_id=$('.voucher_id').val();
        let url = "{{route('account-ledger-voucher-month-id-wise', ['ledger_id'=>':ledger_id','date' =>':date','from_date' =>':from_date','to_date' =>':to_date','voucher_id' =>':voucher_id','narration' =>':narration'])}}";
        url = url.replace(':date',date);
        url = url.replace(':ledger_id',ledger_id);
        url = url.replace(':from_date',from_date);
        url = url.replace(':to_date',to_date);
        url = url.replace(':voucher_id',voucher_id);
        url = url.replace(':narration',false);
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                        href="${url}">${name || ''}</a>
                        <span class="display-none">${name || ''}</span>`;
    }

    function accountLedgerVoucherIdWise(id,name){
        let form_date = $('.from_date').val();
        let to_date = $('.to_date').val();
        let url = "{{route('account-ledger-voucher-id-wise', ['ledger_id' =>':ledger_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':ledger_id', id);
        url = url.replace(':form_date', form_date);
        url = url.replace(':to_date', to_date);
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                        href="${url}">${name || ''}</a>
                        <span class="display-none">${name || ''}</span>`;
    }    
    function accountGroupSummaryIdWise(id,name){
        let form_date = $('.from_date').val();
        let to_date = $('.to_date').val();
        let url = "{{route('account-group-summary-id-wise', ['group_chart_id' =>':group_chart_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':group_chart_id', id);
        url = url.replace(':form_date', form_date);
        url = url.replace(':to_date', to_date);
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                        href="${url}">${name || ''}</a>
                        <span class="display-none">${name || ''}</span>`;
    }

    function redirectAccountLedgerMonthlySummaryIdWise(ledger_id,ledger_name=''){
        let url_get_1 = $("#homeRoute").attr("href");
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        let path=url_get_1+`/report/account-ledger-monthly-summary/${ledger_id}/${form_date}/${to_date}`;

        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" href="${path}">${ledger_name || ''}</a><span class="display-none">${ledger_name || ''}</span>`;     
    }    
   
    function redirectAccountGroupSummaryIdWise(group_chart_id,group_chart_name=''){
        let url_get_1 = $("#homeRoute").attr("href");
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        let path=url_get_1+`/report/account-group-summary/${group_chart_id}/${form_date}/${to_date}`;

        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" href="${path}">${group_chart_name || ''}</a><span class="display-none">${group_chart_name || ''}</span>`;     
    }


    function partyLedgerIdWise(id,name){
        let form_date = $('.from_date').val();
        let to_date = $('.to_date').val();
        let url = "{{route('party-ledger-id-wise', ['ledger_id' =>':ledger_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':ledger_id',id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                        href="${url}">${name || ''}</a>
                        <span class="display-none">${name || ''}</span>`;
    }
    
    function groupWisePartyLedgerIdWise(id,name){
        let form_date = $('.from_date').val();
        let to_date = $('.to_date').val();
        let url = "{{route('group-wise-party-ledger-id-wise', ['group_chart_id' =>':group_chart_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':group_chart_id',id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                        href="${url}">${name || ''}</a>
                        <span class="display-none">${name || ''}</span>`;
    }

    function stockItemRegisterMonthlyIdWise(id,name){
        let from_date = $('.from_date').val();
        let to_date = $('.to_date').val();
        let godown_id=$('.godown_id').val();
        let stock_item_id=$('.stock_item_id').val();
        let url = "{{route('stock-item-register-monthly-id-wise', ['date' =>':date', 'stock_item_id'=>':stock_item_id','godown_id'=>':godown_id','from_date'=>':from_date','to_date'=>':to_date'])}}";
        url = url.replace(':date',id);
        url = url.replace(':stock_item_id',stock_item_id);
        url = url.replace(':godown_id',godown_id);
        url = url.replace(':from_date',from_date);
        url = url.replace(':to_date',to_date);
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                        href="${url}">${name || ''}</a>
                        <span class="display-none">${name || ''}</span>`;
    }

    function stockItemRegisterStoreMonthlyIdWise(id,name){
        let godown_id=$('.godown_id').val();
        let stock_item_id=$('.stock_item_id').val();
        let url = "{{route('stock-item-register-store-monthly-id-wise', ['date' =>':date', 'stock_item_id'=>':stock_item_id','godown_id'=>':godown_id'])}}";
        url = url.replace(':date',id);
        url = url.replace(':stock_item_id',stock_item_id);
        url = url.replace(':godown_id',godown_id);
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                        href="${url}">${name || ''}</a>
                        <span class="display-none">${name || ''}</span>`;        
    }


    function stockItemAnalysisDetailsIdWise(id, name, voucherType = 0) {
        let stock_item_id = $(".stock_item_id").val();
        let godown_id = $('.godown_id').val();
        let form_date = $('.from_date').val();
        let to_date = $('.to_date').val();
        let purchase_in = voucherType == 10 ? $('.purchase').val() : 0;
        let grn_in = voucherType == 24 ? $('.grn').val() : 0;
        let purchase_return_in = voucherType == 29 ? $('.purchase_return').val() : 0;
        let journal_in = voucherType == 6 ? $('.journal').val() : 0;
        let stock_journal_in = voucherType == 21 ? $('.stock_journal_in').val() : 0;
        let sales_return_out = voucherType == 25 ? $('.sales_return').val() : 0;
        let gtn_out = voucherType == 23 ? $('.gtn').val() : 0;
        let sales_out = voucherType == 19 ? $('.sales').val() : 0;
        let journal_out = voucherType == 6 ? $('.journal').val() : 0;
        let stock_journal_out = voucherType == 21 ? $('.stock_journal_out').val() : 0;
        let sort_by = $(".sort_by:checked").val();
        let stort_type = $(".stort_type:checked").val();
        let url =
            "{{ route('stock-item-analysis-details-id-wise', ['ledger_head_id' => ':ledger_head_id', 'stock_item_id' => ':stock_item_id', 'godown_id' => ':godown_id', 'form_date' => ':form_date', 'to_date' => ':to_date', 'purchase_in' => ':purchase_in', 'grn_in' => ':grn_in', 'purchase_return_in' => ':purchase_return_in', 'journal_in' => ':journal_in', 'stock_journal_in' => ':stock_journal_in', 'sales_return_out' => ':sales_return_out', 'gtn_out' => ':gtn_out', 'sales_out' => ':sales_out', 'journal_out' => ':journal_out', 'stock_journal_out' => ':stock_journal_out', 'sort_by' => ':sort_by', 'stort_type' => ':stort_type']) }}";

        url = url.replace(':ledger_head_id', id || 0);
        url = url.replace(':stock_item_id', stock_item_id);
        url = url.replace(':godown_id', godown_id);
        url = url.replace(':form_date', form_date);
        url = url.replace(':to_date', to_date);
        url = url.replace(':purchase_in', purchase_in);
        url = url.replace(':grn_in', grn_in);
        url = url.replace(':purchase_return_in', purchase_return_in);
        url = url.replace(':journal_in', journal_in);
        url = url.replace(':stock_journal_in', stock_journal_in);
        url = url.replace(':sales_return_out', sales_return_out);
        url = url.replace(':gtn_out', gtn_out);
        url = url.replace(':sales_out', sales_out);
        url = url.replace(':journal_out', journal_out);
        url = url.replace(':stock_journal_out', stock_journal_out);
        url = url.replace(':sort_by', sort_by);
        url = url.replace(':stort_type', stort_type);
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                        href="${url}">${name || ''}</a>
                        <span class="display-none">${name || ''}</span>`;
    }
    
    function groupCashFlowIdWise(id,name){
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        let url = "{{route('group-cash-flow-id-wise', ['id' =>':id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':id',id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                        href="${url}">${name || ''}</a>
                        <span class="display-none">${name || ''}</span>`;        
    }    
    function ledgerCashFlowIdWise(id,name){
        let form_date = $('.from_date').val();
        let to_date = $('.to_date').val();
        url =
            "{{ route('ledger-cash-flow-id-wise', ['id' => ':id', 'form_date' => ':form_date', 'to_date' => ':to_date']) }}";
        url = url.replace(':id', id);
        url = url.replace(':form_date', form_date);
        url = url.replace(':to_date', to_date);
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                        href="${url}">${name || ''}</a>
                        <span class="display-none">${name || ''}</span>`;        
    }
</script>