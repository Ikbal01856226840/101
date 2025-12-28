@extends('layouts.backend.app')
@section('title','Accounts Monthly Summary')
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
    border: 1px solid #ddd; font-size: 16px;
    font-family: Arial, sans-serif;
}

table {width:100%;grid-template-columns: auto auto;}

</style>
@endpush
@section('admin_content')<br>
<!-- add component-->
@component('components.report', [
    'title' => 'Accounts Group  Monthly Summary',
    'print_layout'=>'portrait',
    'print_header'=>'Account Group Monthly Summary',
    'user_privilege_title'=>'AccountMontlySummary',
    'print_date'=>1,
]);

<!-- Page-header component -->
@slot('header_body')
 <form  id="add_accounts_group_monthly_summary_form"  method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row">
        <div class="col-md-4">
            <label>Accounts Group :</label>
            <select name="group_id" class="form-control  js-example-basic-single  group_id" required>
                <option value="">--Select --</option>
                {!!html_entity_decode($group_chart_data)!!}
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
                <th  style="width: 1%;" class="th"></th>
                <th  style="width: 5%;" class="th text-end">Total :</th>
                <th  style="width: 2%;font-size: 18px;" class="th total_debit text-end"></th>
                <th  style="width: 2%;font-size: 18px;" class="th total_credit text-end"></th>
                <th  style="width: 5%;font-size: 18px;" class="th total_closing_blance text-end"></th>
        </tfoot>
    </table>
    <div class="col-sm-12 text-center hide-btn">
        <span><b>Copyright &copy; 2014-2022 <a href="http://www.hamko-ict.com/">Hamko-ICT.</a> All rights
                reserved.</b></span>
    </div>
</div>
@endslot
@endcomponent
<br>
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>

<script>
    let   totalDebit=0;totalCredit=0;closingBlance=0,dr_cr_text='' , currentBalance='';op_1=0;
    // add ledger monthly summary form
    $("#add_accounts_group_monthly_summary_form").submit(function(e) {
        print_date();
        op_1=0;
        $(".modal").show();
        e.preventDefault();
        const fd = new FormData(this);
        $.ajax({
            url: '{{ route("report-account-group-monthly-summary-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                  get_account_group_summary_val(response.data)
                },
                error : function(data,status,xhr){
                    Unauthorized(data.status);
                }
        });
    });



    // accounts monthly summary

    function  get_account_group_summary_val(response) {
        totalDebit = 0;
        totalCredit = 0;
        closingBlance = 0;
        dr_cr_text=response.ledger_opening_balance[0].nature_group == 1 ||  response.ledger_opening_balance[0].nature_group == 3?"Dr":'Cr';

        const opening = response.opening_balance[0] || { op_total_debit: 0,op_total_credit: 0 };
        let total_op_val;
        let total_op_sign;
            if(response.ledger_opening_balance[0].nature_group == 1 ||response.ledger_opening_balance[0].nature_group == 3){
                total_op_val=(openning_blance_cal(response.ledger_opening_balance[0].nature_group,response.ledger_opening_balance[0].DrCr,response.ledger_opening_balance[0].opening_balance) + ((opening.op_total_debit || 0) - (opening.op_total_credit || 0)));
                total_op_sign = total_op_val >= 0 ? 'Dr' : 'Cr';
            }else{
                total_op_val=(openning_blance_cal(response.ledger_opening_balance[0].nature_group,response.ledger_opening_balance[0].DrCr,response.ledger_opening_balance[0].opening_balance) + ((opening.op_total_credit || 0) - (opening.op_total_debit || 0)))
                total_op_sign = total_op_val >= 0 ? 'Cr' : 'Dr';
            }
            const openingBalance = Math.abs(total_op_val).formatBangladeshCurrencyType("accounts", ' ',  total_op_sign);

        let htmlFragments = [];

        // Opening Balance Row
        htmlFragments.push(`<tr>
                                <td style="width: 1%;  border: 1px solid #ddd;"></td>
                                <td colspan="3" style="width: 3%;"class="td">Opening Balance</td>
                                <td style="width: 3%;text-align: right;"class="td">${(openingBalance||0)}</td>
                            </tr>`);

        // Current Stock Rows
        if(response.monthWiseDebitCredit[0]){
            response.monthWiseDebitCredit.forEach((v, key) => {
                let closing_val;
                let total_closing_sign
                totalDebit += (v.debit_sum || 0);
                totalCredit += (v.credit_sum || 0);

                if(response.ledger_opening_balance[0].nature_group == 1 ||response.ledger_opening_balance[0].nature_group == 3){
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

                currentBalance =Math.abs(closingBlance).formatBangladeshCurrencyType("accounts",' ',total_closing_sign);
                htmlFragments.push(`<tr id="${v.transaction_date}" class="left left-data editIcon table-row">
                                        <td style="width: 1%;  border: 1px solid #ddd;">${(key + 1)}</td>
                                        <td class="td" style="width: 3%;color: #0B55C4" class="text-wrap">${new Date(v.transaction_date).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}</td>
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

</script>
@endpush
@endsection
