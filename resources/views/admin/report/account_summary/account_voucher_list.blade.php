@extends('layouts.backend.app')
@section('title','Ledger Voucher List / Register')
@push('css')
 <style>
 .table-scroll thead tr:nth-child(2) th {
    top: 30px;
}
.th{
    border: 1px solid #ddd;font-weight: bold;
    font-family: Arial, sans-serif;
}
.td{
    border: 1px solid #ddd; font-size: 18px;
    font-family: Arial, sans-serif;
}
.td-bold {
        font-weight: bold;
}
body{
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
    'page_title'=>'Ledger Voucher List / Register',
    'size'=>'modal-xl',
    'page_unique_id'=>32,
    'ledger'=>'yes',
    'title'=>'Ledger Voucher List / Register',
    'daynamic_function'=>'get_ledger_voucher_initial_show'
]);
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Ledger Voucher List / Register',
    'print_layout'=>'portrait',
    'print_header'=>'Ledger Voucher List / Register',
    'user_privilege_title'=>'LedgerVoucherList',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
 <form  id="add_ledger_voucher_form"  method="POST">
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
        <div class="col-md-2">
            <label>Accounts Ledger : </label>
            <select name="ledger_id" class="form-control  js-example-basic-single  ledger_id" required>
                <option value="">--Select--</option>
            </select>
        </div>
        <div class="col-md-3">
            <div class="row  m-0 p-0 ">
                <div class="col-md-6 m-0 p-0 start_date">
                    <label>Date From: </label>
                        <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{$form_date?? financial_end_date(date('Y-m-d')) }}" >
                </div>
                <div class="col-md-6 m-0 p-0 end_date">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{$to_date?? financial_end_date(date('Y-m-d')) }}" >
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <br>
           <input class="form-check-input" id="narration" type="checkbox" name="last_update" value="1" >
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Narration
                </label>
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
<div class="dt-responsive table-responsive cell-border sd  voucher_list_summarytableFixHead">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <tr>
                <th style="width: 1%;">SL.</th>
                <th style="width: 3%;  border: 1px solid #ddd;">Date</th>
                <th class="th"  style=" width: 5%">
                    Particulars<button class="show-all-data m-0 p-0 px-1 rounded bg-info fw-bold text-black opacity-25 d-print-none">Show All</button>
                </th>
                <th class="th" style=" width: 5%;">Voucher Type</th>
                <th class="th"  style=" width: 5%;">Voucher No</th>
                <th class="th narration"  style=" width: 5%;">Narration</th>
                <th class="th text-end"  style=" width: 5%;">Debit</th>
                <th class="th text-end" style=" width: 5%; ">Credit</th>
                <th class="th text-end" style=" width: 5%; ">Closing</th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th  style="width: 1%;" class="th"></th>
                <th  style="width: 5%;" class="th"></th>
                <th  style="width: 2%;font-size: 18px;" class="th"></th>
                <th  style="width: 2%;font-size: 18px;" class="th"></th>
                <th  style="width: 2%;" class="th narration"></th>
                <th  style="width: 2%;font-size: 18px;" class="th text-end">Total :</th>
                <th  style="width: 2%;font-size: 18px; " class="th total_debit totalDebit text-end"></th>
                <th  style="width: 2%;font-size: 18px;" class="th total_credit totalCredit text-end"></th>
                <th  style="width: 2%;font-size: 18px;" class="th total_closing totalClosing text-end"></th>
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
    let narration="{{ $narration ?? 0 }}" || false;
    let op_1=0;
    if (narration==true||narration=='true') {
        $("#narration").prop('checked', true);
    } else {
        $("#narration").prop('checked', false);
    }
    //ledger select option
    get_ledger_recursive('{{route("stock-ledger-select-option-tree")}}');

    console.log("{{$ledger_id??0}}");
    if("{{$ledger_id??0}}"!=0){
        $('.ledger_id').val("{{$ledger_id??0}}");
    }

    if("{{$voucher_id??0}}"!=0){
          $('.voucher_id').val("{{$voucher_id??0}}");
    }

    $(document).ready(function () {

        if("{{$ledger_id??0}}"!=0){
            local_store_voucher_list_set_data();
        }else{
            // local_store_voucher_list_get();
        }
        get_ledger_voucher_initial_show();

        // add ledger voucher form
        $("#add_ledger_voucher_form").submit(function(e) {
            print_date();
            // local_store_voucher_list_set_data();
            // $(".modal").show();
            e.preventDefault();
            // const fd = new FormData(this);
            // $.ajax({
            //     url: '{{ route("account-ledger-voucher-data") }}',
            //         method: 'POST',
            //         data: fd,
            //         cache: false,
            //         contentType: false,
            //         processData: false,
            //         dataType: 'json',
            //         success: function(response) {
            //         $(".modal").hide();
            //         get_ledger_voucher_val(response.data)
            //         },
            //         error : function(data,status,xhr){
            //             Unauthorized(data.status);
            //         }
            // });
            let ledger_id=$('.ledger_id').val();
            let from_date=$('.from_date').val();
            let to_date=$('.to_date').val();
            let voucher_id=$('.voucher_id').val();
            let narration=$('#narration').is(':checked');
            url = "{{route('account-ledger-voucher-details-id-wise', ['ledger_id'=>':ledger_id','from_date' =>':from_date','to_date' =>':to_date','voucher_id' =>':voucher_id','narration' =>':narration'])}}";
            url = url.replace(':ledger_id',ledger_id);
            url = url.replace(':from_date',from_date);
            url = url.replace(':to_date',to_date);
            url = url.replace(':voucher_id',voucher_id);
            url = url.replace(':narration',narration);
            window.location=url;
        });
    });

    let   totalDebit=0;totalCredit=0;

    // ledger voucher
    function  get_ledger_voucher_val(response) {
        totalDebit = 0;
        totalCredit = 0;
        let htmlFragments = [];
        let narration=$("#narration").is(':checked');
        closingBlance = 0;
        dr_cr_text=response.group_chart_nature.nature_group == 1 ||  response.group_chart_nature.nature_group == 3?"Dr":'Cr';
        const opening = response.op_party_ledger[0] || { op_total_debit: 0,op_total_credit: 0 };
        let total_op_val;
        let total_op_sign;
        if(response.group_chart_nature.nature_group == 1 ||response.group_chart_nature.nature_group == 3){
            total_op_val=(openning_blance_cal(response.group_chart_nature.nature_group,response.group_chart_nature.DrCr,response.group_chart_nature.opening_balance) + ((opening.op_total_debit || 0) - (opening.op_total_credit || 0)));
            total_op_sign = total_op_val >= 0 ? 'Dr' : 'Cr';
        }else{
            total_op_val=(openning_blance_cal(response.group_chart_nature.nature_group,response.group_chart_nature.DrCr,response.group_chart_nature.opening_balance) + ((opening.op_total_credit || 0) - (opening.op_total_debit || 0)))
            total_op_sign = total_op_val >= 0 ? 'Cr' : 'Dr';
        }
        const openingBalance = Math.abs(total_op_val).formatBangladeshCurrencyType("accounts",'',total_op_sign);

        // Opening Balance Row
        htmlFragments.push(`<tr>
                                <td style="width: 1%;  border: 1px solid #ddd;"></td>                                <td style="width: 1%;  border: 1px solid #ddd;"></td>
                                <td colspan="5" style="width: 3%;"class="td">Opening Balance</td>
                                <td style="width: 3%;text-align: right;"class="td">${(openingBalance||0)}</td>
                            </tr>`);

        response.data.forEach((v, key) => {
            totalDebit += (v.debit_sum || 0);
            totalCredit += (v.credit_sum || 0);

                if(response.group_chart_nature.nature_group == 1 ||response.group_chart_nature.nature_group == 3){
                    closing_val= (parseFloat(v.debit_sum|| 0) - parseFloat(v.credit_sum || 0));
                    if(op_1==0){
                        closingBlance+=parseFloat(closing_val)+parseFloat(total_op_val);
                        total_closing_sign= closingBlance >= 0 ? 'Dr' : 'Cr';
                        op_1=1;
                    }else{
                        closingBlance+=parseFloat(closing_val);
                        total_closing_sign= closingBlance >= 0 ? 'Dr' : 'Cr';
                    }
                }else{
                    closing_val= (parseFloat(v.credit_sum || 0) - parseFloat( v.debit_sum|| 0));
                    if(op_1==0){
                        closingBlance+=parseFloat(closing_val)+parseFloat(total_op_val);
                        total_closing_sign = closingBlance >= 0 ? 'Cr' : 'Dr';
                        op_1=1;
                    }else{
                        closingBlance+=parseFloat(closing_val);
                        total_closing_sign = closingBlance >= 0 ? 'Cr' : 'Dr';
                    }
                }

            htmlFragments.push(`<tr id="${v.tran_id+","+v.voucher_type_id}" class="left left-data editIcon table-row">
                                    <td style="width: 1%;  border: 1px solid #ddd;">
                                        <button
                                            class="hide-data m-0 p-0 px-1 fw-bold rounded bg-Secondary text-black opacity-25 small  d-print-none"
                                            >Hide</button>
                                        <button
                                            class="show-data m-0 p-0 px-1 fw-bold rounded bg-Secondary text-black opacity-25 small d-print-none d-none  "
                                            >Show</button>
                                            <span class="d-print-none"> ${(key + 1)}</span>
                                            <span class="print-sl"> ${(key + 1)}</span>
                                    </td>
                                    <td class="td" style="width: 3%;" class="text-wrap">${join(new Date(v.transaction_date), options, ' ')}</td>
                                    <td class="td" style="width: 3%;">${(v.ledger_name||'')}</td>
                                    <td class="td voucher_name" style="width: 3%;color: #0B55C4">${redirectVoucherIdWise(v.voucher_type_id, v.tran_id,v.voucher_name)}</td>
                                    <td class="td" style="width: 3%;">${(v.invoice_no ||'')}</td>
                                    <td class="td narration text-wrap ${narration?'':'d-none d-print-none'}" style="">${v?.narration}</td>
                                    <td class="td debit" debit="${(v.debit_sum || 0)}" style="width: 3%;text-align:right;">${(v.debit_sum || 0).formatBangladeshCurrencyType("accounts")}</td>
                                    <td class="td credit" credit="${(v.credit_sum || 0)}" style="width: 3%;text-align:right;">${(v.credit_sum || 0).formatBangladeshCurrencyType("accounts")}</td>
                                    <td class="td" style="width: 3%;text-align:right;">${Math.abs(closingBlance || 0).formatBangladeshCurrencyType("accounts")}${total_closing_sign}</td>
                                    </tr>`);
        });

        // Append the fragment to the DOM once
        $(".item_body").html(htmlFragments.join(''));
        // Update total values
        $('.total_debit').text((totalDebit||0).formatBangladeshCurrencyType("accounts"));
        $('.total_credit').text((totalCredit|| 0).formatBangladeshCurrencyType("accounts"));
        get_hover();
    }

    function  get_ledger_voucher_initial_show(){
        print_date();
        op_1 = 0;
        // updategetAndRemoveStorage();
        $(".modal").show();
        $.ajax({
                url: "{{ route('account-ledger-voucher-data')}}",
                type: 'GET',
                dataType: 'json',
                data:{
                    to_date:$('.to_date').val(),
                    from_date:$('.from_date').val(),
                    ledger_id:$('.ledger_id').val(),
                    voucher_id:$('.voucher_id').val()
                },
                success: function(response) {
                    
                    $(".modal").hide();
                    get_ledger_voucher_val(response.data)
                    if($("#narration").is(':checked')){
                        $(document).find('.narration').removeClass('d-none');
                        $(document).find('.narration').removeClass('d-print-none');
                    }else{
                        $(document).find('.narration').addClass('d-none');
                        $(document).find('.narration').addClass('d-print-none');
                    }
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
    }
    function local_store_voucher_list_get() {
        // getStorage("end_date", '.to_date');
        // getStorage("start_date", '.from_date');
        // getStorage("ledger_id", '.ledger_id');
        // getStorage("voucher_id", '.voucher_id');
    }

    function local_store_voucher_list_set_data() {
            setStorage("end_date", $('.to_date').val());
            setStorage("start_date", $('.from_date').val());
            setStorage("ledger_id", $('.ledger_id').val());
            setStorage("voucher_id", $('.voucher_id').val());
    }
    //redirect route
    $(document).ready(function() {
        $(document).on('click', '.voucher_name', function(e) {
            setStorage("end_date_update", $('.to_date').val());
            setStorage("start_date_update", $('.from_date').val());
            setStorage("voucher_id_update", $('.voucher_id').val());
            setStorage("ledger_id_update", $('.ledger_id').val());
        })
        let display_height=$(window).height();
       $('.voucher_list_summarytableFixHead').css('height',`${display_height-120}px`);
    });

    function updategetAndRemoveStorage() {
        getStorage("end_date_update", '.to_date');
        getStorage("start_date_update", '.from_date');
        getStorage("voucher_id_updat", '.voucher_id');
        getStorage("ledger_id_update", '.ledger_id');
        getRemoveItem("end_date_update", '.to_date');
        getRemoveItem("start_date_update", '.from_date');
        getRemoveItem("voucher_id_updat", '.voucher_id');
        getRemoveItem("ledger_id_update", '.ledger_id');
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
            $(document).find("#tableId #myTable .d-print-none").each(function(){
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
        $(document).find("#myTable tr").each(function(){
            console.log($(this).hasClass('d-print-none'));
            if(!$(this).hasClass('d-print-none')) {
                debit += parseFloat($(this).find('.debit').attr('debit')) || 0;
                credit += parseFloat($(this).find('.credit').attr('credit')) || 0;
                ls++;
                $(this).find('.print-sl').text(ls);
            }else{
               $(this).find('.print-sl').text(''); 
            }
        });
        $('.totalDebit').text(debit?.formatBangladeshCurrencyType("accounts",'','TK'));
        $('.totalCredit').text(credit?.formatBangladeshCurrencyType("accounts",'','TK'));
   }
</script>
@endpush
@endsection
