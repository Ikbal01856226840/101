@extends('layouts.backend.app')
@section('title','Voucher Lists/Register')
@push('css')
 <style>
  input[type=radio] {
    width: 20px;
    height: 20px;
 }
 input[type=checkbox] {
    width: 20px;
    height: 20px;
 }
 .td {
        border: 1px solid #ddd;
  }
    .font {
        font-size: 16px;
    }
body {
        overflow: auto !important;
    }
    .print-sl{
        display:none;
    }
</style>
@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
'id' =>'exampleModal',
'class' =>'modal fade',
'page_title'=>'Voucher Lists/Register',
'size'=>'modal-xl',
'page_unique_id'=>25,
'title'=>'Voucher Lists/Register',
'daynamic_function'=>'get_voucher_register_initial_show'
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Voucher Lists/Register',
    'print_layout'=>'portrait',
    'print_header'=>'Voucher Lists/Register',
    'user_privilege_title'=>'LedgerCashFlow',
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
 <form  id="add_voucher_register_form"  method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row">
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
        <div class="col-md-3">
            <div class="row  m-0 p-0 ">
                <div class="col-md-6 m-0 p-0 start_date">
                    <label>Date From: </label>
                        <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{ date('Y-m-d') }}"   name="narratiaon"  >
                </div>
                <div class="col-md-6 m-0 p-0 end_date">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{financial_end_date(date('Y-m-d'))}}"  name="narratiaon"  >
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <br>
            <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
        </div>
        <div class="col-md-5">
            <label></label>
            <div class="col-md-12">
                <div>
                    <input class="form-check-input debit_check" type="checkbox" name="narratiaon" value="1" checked>
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Debit Amount
                    </label>
                    <input class="form-check-input credit_check" type="checkbox" name="last_update" value="1" checked>
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Credit Amount
                    </label>
                    <input class="form-check-input narratiaon" type="checkbox" id="narratiaon" name="narratiaon" value="1">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Narration
                    </label>
                </div>
                <label class="fs-6" style="min-width: 1px;">SORT by :</label>
                <input class="form-check-input sort_by" type="radio" name="sort_by" value="1" checked="checked">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    None
                </label>
                <input class="form-check-input sort_by_particular sort_by" type="radio" name="sort_by" value="5">
                <label class="form-check-label fs-6 sort_by_particular" for="flexRadioDefault1">
                    Particular
                </label>
                <input class="form-check-input sort_by" type="radio" name="sort_by" value="2">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Debit
                </label>
                <input class="form-check-input last_update sort_by" type="radio" name="sort_by" value="3">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Credit
                </label>
                <input class="form-check-input last_update sort_by" type="radio" name="sort_by" value="4">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Voucher No
                </label>
            </div>
        </div>
    </div>
</form>
<div class="col-md-5">
    <button  class="btn hor-grd btn-grd-primary btn-block ledger_voucher_register" style="width:250px; margin-bottom:5px;">Ledger Voucher Register</button>
</div>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_voucher_register ">
</div>
@endslot
@endcomponent
<br>
@push('js')
<script>
    if("{{$from_date??0}}"!=0){
        $('.from_date').val('{{$from_date??0}}');
    }
    if("{{$to_date??0}}"!=0){
        $('.to_date').val('{{$to_date??0}}');
        $(".sort_by[value='" + {{$sort_by??1}}+ "']").prop("checked", true);
        $(".debit_check[value='" +{{ $debit??1 }}+ "']").prop("checked", true);
        $('.narratiaon').prop('checked', {{ $narration??false }});
        $('.credit_check').prop('checked', {{$credit??false }});
    }

    if("{{$voucher_id??0}}"!=0){
        $('.voucher_id').val('{{$voucher_id??0}}');
    }
    $(document).ready(function () {
        if("{{$from_date??0}}"!=0){
            
            local_store_voucher_register_set_data();
        }else{
          
          local_store_voucher_register_get();
        }

        get_voucher_register_initial_show();
        $("#add_voucher_register_form").submit(function(e) {
            reset_scroll_height();
            local_store_voucher_register_set_data();
                print_date();
                e.preventDefault();
                let date= $('.from_date').val();;
                let from_date=$('.from_date').val();
                let to_date=$('.to_date').val();
                let voucher_id=$('.voucher_id').val();
                let sort_by=$(".sort_by:checked").val()||1;
                let debit=$('.debit_check').is(':checked');
                let credit=$('.credit_check').is(':checked');
                let narratiaon=$('.narratiaon').is(':checked');
                url = "{{route('voucher-month-id-wise-details', ['voucher_id'=>':voucher_id','date' =>':date','from_date' =>':from_date','to_date' =>':to_date','sort_by' =>':sort_by','debit' =>':debit','credit' =>':credit','narratiaon' =>':narratiaon'])}}";
                url = url.replace(':from_date',from_date);
                url = url.replace(':to_date',to_date);
                url = url.replace(':date',date);
                url = url.replace(':voucher_id',voucher_id);
                url = url.replace(':sort_by',sort_by);
                url = url.replace(':debit',debit);
                url = url.replace(':credit',credit);
                url = url.replace(':narratiaon',narratiaon);
                window.location=url;
                // const fd = new FormData(this);
                // $.ajax({
                //     url: '{{ route("voucher-register-data") }}',
                //         method: 'POST',
                //         data: fd,
                //         cache: false,
                //         contentType: false,
                //         processData: false,
                //         dataType: 'json',
                //         success: function(response) {
                //             $(".modal").hide();
                //             get_voucher_register_val(response)
                //         },
                //         error : function(data,status,xhr){
                //             Unauthorized(data.status);
                //         }
                // });
            });
    });

    function get_voucher_register_val(response) {
        const rowsPerPage = 500;
        let currentPage = 0;
        const totalRows = response.data.length;
        let totalDebit=0;
        let totalCredit=0;
        // Function to render the table header and footer
        function renderTableHeaderFooter() {
            let htmlFragments = [];
            htmlFragments.push(`
                <table id="tableId" style="border-collapse: collapse;" class="table table-striped customers ">
                    <thead>
                        <tr>
                            <th style="width: 1%;" class="td">SL.</th>
                            <th style="width: 3%;" class="td">Date</th>
                            <th style="width: 3%;" class="td">
                                Particulars <button class="show-all-data m-0 p-0 px-1 rounded bg-info fw-bold text-black opacity-25 d-print-none">Show All</button>
                            </th>
                            <th style="width: 2%;" class="td">Voucher Type</th>
                            <th style="width: 3%;" class="td">Voucher No</th>
            `);
            // Add optional headers based on checkboxes
            if ($("#narratiaon").is(':checked')) {
                htmlFragments.push(`<th style="width: 3%;" class="td">Narration</th>`);
            }
            if ($(".debit_check").is(':checked')) {
                htmlFragments.push(`<th style="width: 3%;" class="td">Debit Amount/<br>Inwards Qty</th>`);
            }
            if ($(".credit_check").is(':checked')) {
                htmlFragments.push(`<th style="width: 3%;" class="td">Credit Amount/<br>Outwards Qty</th>`);
            }

            htmlFragments.push(`</tr></thead><tbody id="myTable" class="qw">`);
            $(".sd").html(htmlFragments.join('')); // Render header once
        }
        // Function to render a chunk of data
        function renderTableChunk(startIndex) {
            let htmlFragments = [];
            for (let i = startIndex; i < Math.min(startIndex + rowsPerPage, totalRows); i++) {
                const v = response.data[i];
                htmlFragments.push(`
                    <tr id='${v.tran_id},${v.voucher_type_id}' class="left left-data editIcon table-row">
                        <td style="width: 1%;" class="td"> 
                            <button
                                class="hide-data m-0 p-0 px-1 fw-bold rounded bg-Secondary text-black opacity-25 small  d-print-none"
                                >Hide</button>
                            <button
                                class="show-data m-0 p-0 px-1 fw-bold rounded bg-Secondary text-black opacity-25 small d-print-none d-none  "
                                >Show</button>
                                <span class="d-print-none"> ${(i + 1)}</span>
                                <span class="print-sl"> ${(i + 1)}</span>
                               
                        </td>
                        <td style="width: 3%;" class="td">${join(new Date(v.transaction_date), options, ' ')}</td>
                        <td style="width: 3%;" class="td font text-wrap">${(v.ledger_name || '')}</td>
                        <td style="width: 2%;" class="td font voucher_name">
                            ${redirectVoucherIdWise(v.voucher_type_id, v.tran_id,v.voucher_name)}
                        </td>
                        <td style="width: 3%;" class="td font">${v.invoice_no}</td>
                `);

                // Optional columns
                if ($("#narratiaon").is(':checked')) {
                    htmlFragments.push(`<td style="width: 3%;" class="td font text-wrap">${(v.narration || "")}</td>`);
                }

                if ([1, 6, 8, 14].includes(v.voucher_type_id)) {
                    voucher_register_stock_to_ledger();
                    if ($(".debit_check").is(':checked')) {
                        htmlFragments.push(`<td style="width: 3%; text-align: right;" class="td font debit" debit="${v?.debit||0}">${(v.debit ? (v.debit ? v.debit.formatBangladeshCurrencyType("accounts") + ' TK' : '') : '')}</td>`);
                        totalDebit+=(v?.debit||0);
                    }
                    if ($(".credit_check").is(':checked')) {
                        htmlFragments.push(`<td style="width: 3%; text-align: right;" class="td font credit" credit="${v?.credit||0}">${(v.credit ? (v.credit ? v.credit.formatBangladeshCurrencyType("accounts") + ' TK' : '') : '')}</td>`);
                        totalCredit+=(v?.credit||0);
                    }
                } else if([21,22].includes(v.voucher_type_id)) {
                    voucher_register_ledger_to_stock();
                    if ($(".debit_check").is(':checked')) {
                        htmlFragments.push(`<td style="width: 3%; text-align: right;" class="td font">${(v.stock_in_sum ? Math.abs(parseFloat(v.stock_in_sum)).formatBangladeshCurrencyType("quantity")  : '')}</td>`);
                    }
                    if ($(".credit_check").is(':checked')) {
                        htmlFragments.push(`<td style="width: 3%; text-align: right;" class="td font">${(v.stock_out_sum ? Math.abs(parseFloat(v.stock_out_sum)).formatBangladeshCurrencyType("quantity")  : '')}</td>`);
                    }
                }else{
                    voucher_register_stock_to_ledger();
                    if ($(".debit_check").is(':checked')) {
                        htmlFragments.push(`<td style="width: 3%; text-align: right;" class="td font credit" credit="${v?.credit||0}" >${(v.credit ? v.credit.formatBangladeshCurrencyType("accounts",'',' TK'): '')}</td>`);
                        totalCredit+=(v?.credit||0);
                    }
                    if ($(".credit_check").is(':checked')) {
                        htmlFragments.push(`<td style="width: 3%; text-align: right;" class="td font debit" debit="${v?.debit||0}">${(v.debit ? v.debit.formatBangladeshCurrencyType("accounts",'',' TK'): '')}</td>`);
                        totalDebit+=(v?.debit||0);
                    }
                }
                htmlFragments.push(`</tr>`);
            }
            $("#myTable").append(htmlFragments.join('')); // Append chunk to the table body
            // Load next chunk if there are more rows
            if (startIndex + rowsPerPage < totalRows) {
                setTimeout(() => renderTableChunk(startIndex + rowsPerPage), 0); // Use timeout for UI responsiveness
            } else {
                renderTableFooter(); // Render footer once all rows are loaded
                scroll_table_to_prev();
            }
        }
        // Function to render the footer
        function renderTableFooter() {
            let footerFragments = [];
            footerFragments.push(`<tfoot><tr>`);
            footerFragments.push(`
                <td style="width: 1%;" class="td"></td>
                <td style="width: 3%;" class="td"></td>
                <td style="width: 3%;" class="td">Total:</td>
                <td style="width: 2%;" class="td"></td>
                <td style="width: 3%;" class="td"></td>
            `);
           

            if ($("#narratiaon").is(':checked')) {
                footerFragments.push(`<td style="width: 3%;" class="td">Narration</td>`);
            }
            if ($(".debit_check").is(':checked')) {
                footerFragments.push(`<td style="width: 3%; text-align: right; font-weight:bold;" class="td font totalDebit">${totalCredit>0?('TK'+totalCredit?.formatBangladeshCurrencyType("accounts",'')):''}</td>`);
            }
            if ($(".credit_check").is(':checked')) {
               footerFragments.push(`<td style="width: 3%; text-align: right; font-weight:bold;" class="td font totalCredit">${totalDebit>0?('TK'+totalDebit?.formatBangladeshCurrencyType("accounts",'')):""}</td>`); 
            }
            totalDebit=0;
            totalCredit=0;

            footerFragments.push(`</tr></tfoot>`);
            $("#tableId").append(footerFragments.join('')); // Append footer to the table
        }

        // Initial rendering
        renderTableHeaderFooter();
        renderTableChunk(currentPage * rowsPerPage);
    }

    // voucher register initial show
    function get_voucher_register_initial_show() {
            print_date();
            $(".modal").show();
            $.ajax({
                url: "{{ url('voucher-register-data')}}",
                type: 'GET',
                dataType: 'json',
                data: {
                    to_date: $('.to_date').val(),
                    from_date: $('.from_date').val(),
                    voucher_id: $('.voucher_id').val(),
                    sort_by:$(".sort_by:checked").val(),
                },
                success: function(response) {
                    $(".modal").hide();
                    get_voucher_register_val(response)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
            })
        }
    function local_store_voucher_register_get() {
            getStorage("end_date", '.to_date');
            getStorage("start_date", '.from_date');
            getStorage("voucher_id", '.voucher_id');
            getStorage("debit_check", '.debit_check', 'checkbox');
            getStorage("debit_check", '.debit_check', 'checkbox');
            getStorage("narratiaon", '.narratiaon', 'checkbox');
        let sort_by= getStorage("sort_by");
        $(".sort_by[value='" + sort_by + "']").prop("checked", true);
    }

    function local_store_voucher_register_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("voucher_id", $('.voucher_id').val());
        setStorage("sort_by", $(".sort_by:checked").val());
        setStorage("debit_check", $('.debit_check').is(':checked'));
        setStorage("credit_check", $('.credit_check').is(':checked'));
        setStorage("narratiaon", $('.narratiaon').is(':checked'));
    }

    function voucher_register_ledger_to_stock() {
        $('.ledger_voucher_register').addClass('stock_voucher_register');
        $('.ledger_voucher_register').text('Stock Voucher Register');
        $('.ledger_voucher_register').removeClass('ledger_voucher_register');
    }
    function voucher_register_stock_to_ledger() {
        $('.stock_voucher_register').addClass('ledger_voucher_register');
        $('.stock_voucher_register').text('Ledger Voucher Register');
        $('.stock_voucher_register').removeClass('stock_voucher_register');
    }

    //redirect route
    $(document).ready(function() {
        let display_height=$(window).height();
       $('.tableFixHead_voucher_register').css('height',`${display_height-120}px`);
    });

    // function updategetAndRemoveStorage() {
    //     getStorage("end_date_update", '.to_date');
    //     getStorage("start_date_update", '.from_date');
    //     getStorage("voucher_id_updat", '.voucher_id');
    //     getRemoveItem("end_date_update", '.to_date');
    //     getRemoveItem("start_date_update", '.from_date');
    //     getRemoveItem("voucher_id_updat", '.voucher_id');

    // }
    //ledger  voucher register
     $(document).on('click', '.ledger_voucher_register', function(e) {
        e.preventDefault();
        let voucher_id = $('.voucher_id').val();
        let form_date = $('.from_date').val();
        let to_date = $('.to_date').val();
        let url = "{{ route('report-stock-item-voucher-wise-register', ['from_date' => ':form_date', 'to_date' => ':to_date', 'voucher_id' => ':voucher_id']) }}";
        url = url.replace(':form_date', form_date);
        url = url.replace(':to_date', to_date);
        url = url.replace(':voucher_id', voucher_id);

        window.open(url, '_blank');
     });

    //stock  voucher register
    $(document).on('click', '.stock_voucher_register', function(e) {
        e.preventDefault();
        let voucher_id = $('.voucher_id').val();
        let form_date = $('.from_date').val();
        let to_date = $('.to_date').val();
        let url = "{{ route('voucher-list-to-stock-voucher-register', ['form_date' => ':form_date', 'to_date' => ':to_date', 'voucher_id' => ':voucher_id']) }}";
        url = url.replace(':form_date', form_date);
        url = url.replace(':to_date', to_date);
        url = url.replace(':voucher_id', voucher_id);

        window.open(url, '_blank');
    });
    function voucherListToStockVoucherRegister(){

    }
    $(document).ready(function(){
        $(document).on('click','.hide-data',function(){
            $(this).addClass('d-none');
            $(this).closest('td').find('.show-data').removeClass('d-none');
            $(this).closest('tr').addClass('d-print-none');
            $(this).closest('tr').find('td').not($(this).parent('td')).hide();
            totalCalculate();
        });
        $(document).on('click','.show-data',function(){
            $(this).addClass('d-none');
            $(this).closest('td').find('.hide-data').removeClass('d-none');
            $(this).closest('tr').removeClass('d-print-none');
            $(this).closest('tr').find('td').show();
            totalCalculate();
        });
        $(document).on('click','.show-all-data',function(){
            $(document).find("#tableId .qw .d-print-none").each(function(){
                $(this).removeClass('d-print-none');
                $(this).find('td').show();
                $(this).find('.show-data').addClass('d-none');
                $(this).find('.hide-data').removeClass('d-none');
            });
            totalCalculate();
        });
   })
   function totalCalculate(){
        let debit = 0;
        let credit = 0;
        let ls=0;
        $(document).find(".qw tr").each(function(){
            if(!$(this).hasClass('d-print-none')) {
                debit += parseFloat($(this).find('.debit').attr('debit')) || 0;
                credit += parseFloat($(this).find('.credit').attr('credit')) || 0;
                ls++;
                $(this).find('.print-sl').text(ls);
                console.log(ls)
            }else{
               $(this).find('.print-sl').text(''); 
            }
        });
        $('.totalDebit').text(credit>0?('TK'+credit?.formatBangladeshCurrencyType("accounts",'')):"");
        $('.totalCredit').text(debit>0?('TK'+debit?.formatBangladeshCurrencyType("accounts",'')):"");
   }
</script>
@endpush
@endsection
