@extends('layouts.backend.app')
@section('title','Account Ledger Monthly Summary')
@push('css')
 <!-- model style -->
 <link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
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

table {width:100%;grid-template-columns: auto auto;}

</style>
@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=>'Account Ledger Monthly Summary',
    'size'=>'modal-xl',
    'page_unique_id'=>31,
    'ledger'=>'yes',
    'title'=>'Account Ledger Monthly Summary',
    'daynamic_function'=>'get_ledger_monthly_summary_initial_show'
]);
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Account Ledger Monthly Summary',
    'print_layout'=>'portrait',
    'print_header'=>'Account Ledger Monthly Summary',
    'user_privilege_title'=>'LedgerMonthly',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
 <form  id="add_ledger_monthly_summary_form"  method="POST">
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
            <label>Accounts Ledger : </label>
            <select name="ledger_id" class="form-control  js-example-basic-single  ledger_id" required>
                <option value="">--Select--</option>
            </select>
        </div>
        <div class="col-md-4">
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
        <div class="col-md-1">
            <br>
            <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
        </div>
    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd  tableFixHead_report">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers table-scroll">
        <thead>
            <tr>
                <th style="width: 1%;">SL.</th>
                <th style="width: 3%;  border: 1px solid #ddd;">Date</th>
                <th class="th  text-end"  style=" width: 5%;">Debit</th>
                <th class="th  text-end" style=" width: 5%;">Credit</th>
                <th class="th  text-end"  style=" width: 5%;">Closing Balance</th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th  style="width: 1%;" class="th"></th>
                <th  style="width: 5%;" class="th text-end">Total :</th>
                <th  style="width: 2%;font-size: 18px;" class="th total_debit text-end"></th>
                <th  style="width: 2%;font-size: 18px;" class="th total_credit text-end"></th>
                <th  style="width: 5%;font-size: 18px;" class="th total_closing_blance text-end"></th>
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
    let   totalDebit=0;totalCredit=0;closingBlance=0,dr_cr_text='' , currentBalance='';op_1=0;
    // ledger select option
    get_ledger_recursive('{{route("stock-ledger-select-option-tree")}}');
    if("{{$ledger_id??0}}"!=0){
        $('.ledger_id').val("{{$ledger_id??0}}");
    }
    if("{{$voucher_id??0}}"!=0){
        $('.voucher_id').val("{{$voucher_id??0}}");
    }
    $(document).ready(function () {

        // stock item get id check
        if("{{$ledger_id??0}}"!=0){
            local_store_ledger_monthly_summary_set_data();
        }else{
            local_store_ledger_monthly_summary_get();
        }
        get_ledger_monthly_summary_initial_show();

            // add ledger monthly summary form
        $("#add_ledger_monthly_summary_form").submit(function(e) {
            local_store_ledger_monthly_summary_set_data();
            op_1=0;
            print_date();
            $(".modal").show();
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("account-ledger-monthly-summary-data") }}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        $(".modal").hide();
                    get_ledger_monthly_summary_val(response.data)
                    },
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
        });
    });



// stock item  wise  voucher  route
// $('.sd').on('click','.table-row',function(e){
//     e.preventDefault();
//     let date=$(this).closest('tr').attr('id');
//     let ledger_id=$('.ledger_id').val();
//     let from_date=$('.from_date').val();
//     let to_date=$('.to_date').val();
//     let voucher_id=$('.voucher_id').val();
//     url = "{{route('account-ledger-voucher-month-id-wise', ['ledger_id'=>':ledger_id','date' =>':date','from_date' =>':from_date','to_date' =>':to_date','voucher_id' =>':voucher_id','narration' =>':narration'])}}";
//     url = url.replace(':date',date);
//     url = url.replace(':ledger_id',ledger_id);
//     url = url.replace(':from_date',from_date);
//     url = url.replace(':to_date',to_date);
//     url = url.replace(':voucher_id',voucher_id);
//     url = url.replace(':narration',false);
//     window.location=url;
// });



function  get_ledger_monthly_summary_val(response) {
        totalDebit = 0;
        totalCredit = 0;
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

        let htmlFragments = [];

        // Opening Balance Row
        htmlFragments.push(`<tr>
                                <td style="width: 1%;  border: 1px solid #ddd;"></td>
                                <td colspan="3" style="width: 3%;"class="td">Opening Balance</td>
                                <td style="width: 3%;text-align: right;"class="td">${(openingBalance||0)}</td>
                            </tr>`);

        // Current Stock Rows
        if(response.party_ledger[0]){
            response.party_ledger.forEach((v, key) => {
                let closing_val;
                let total_closing_sign
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
    
                currentBalance =Math.abs(closingBlance).formatBangladeshCurrencyType("accounts",'',total_closing_sign);
                let date=new Date(v.transaction_date).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                htmlFragments.push(`<tr id="${v.transaction_date}" class="left left-data editIcon table-row">
                                        <td style="width: 1%;  border: 1px solid #ddd;">${(key + 1)}</td>
                                        <td class="td" style="width: 3%;color: #0B55C4" class="text-wrap">${accountLedgerVoucherMonthIdWise(v.transaction_date,date)}</td>
                                        <td class="td" style="width: 3%;text-align: right;">${(v.debit_sum || 0).formatBangladeshCurrencyType("accounts")}</td>
                                        <td class="td" style="width: 3%;text-align: right;">${(v.credit_sum || 0).formatBangladeshCurrencyType("accounts")}</td>
                                        <td class="td" style="width: 3%;text-align: right;"> ${(currentBalance)}</td>

                                </tr>`);
            });
        }

        $(".item_body").html(htmlFragments.join(''));
        $('.total_debit').text((totalDebit||0).formatBangladeshCurrencyType("accounts"));
        $('.total_credit').text((totalCredit|| 0).formatBangladeshCurrencyType("accounts"));
        $('.total_closing_blance').text((currentBalance));
        get_hover();
}

function  get_ledger_monthly_summary_initial_show(){
        print_date();
        $(".modal").show();
        $.ajax({
                url: "{{ route('account-ledger-monthly-summary-data')}}",
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
                    get_ledger_monthly_summary_val(response.data);
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
}
function local_store_ledger_monthly_summary_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("ledger_id", '.ledger_id');
        getStorage("voucher_id", '.voucher_id');
    }

function local_store_ledger_monthly_summary_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("ledger_id", $('.ledger_id').val());
        setStorage("voucher_id", $('.voucher_id').val());
}
</script>
@endpush
@endsection
