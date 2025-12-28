
@extends('layouts.backend.app')
@section('title','Ledger Voucher Register')
@push('css')
<style>
body{
  overflow: auto !important;
}
.th{
    border: 1px solid #ddd;font-weight: bold;
    font-family: Arial, sans-serif;
}
.td{
    border: 1px solid #ddd; font-size: 20px;
    font-family: Arial, sans-serif;
}
.td-bold {
        font-weight: bold;
}
</style>
@endpush
@section('admin_content')<br>
<!-- setting component-->
@component('components.report_setting_modal', [
    'id' =>'exampleModal',
    'class' =>'modal fade',
    'page_title'=>'Ledger Voucher Register',
    'size'=>'modal-xl',
    'page_unique_id'=>29,
    'ledger'=>'yes',
    'title'=>'Ledger Voucher Register',
    'daynamic_function'=>'ledger_voucher_register_initial_show'
]);
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Ledger Voucher Register',
    'print_layout'=>'portrait',
    'print_header'=>'Ledger Voucher Register',
    'user_privilege_title'=>'LedgerVoucherRegister',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="ledger_voucher_register_form"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row ">
            <div class="col-md-4">
                <label>Voucher Type : </label>
                <select name="voucher_id" class="form-control js-example-basic-single voucher_id">
                    <option value="0">--ALL--</option>
                    @php $voucher_type_id= 0; @endphp
                    @foreach ($vouchers as $voucher)
                    @if($voucher_type_id!=$voucher->voucher_type_id)
                    @php $voucher_type_id=$voucher->voucher_type_id; @endphp
                    <option style="color:red;" value="v{{$voucher->voucher_type_id??''}}">{{$voucher->voucher_type??''}}</option>
                    @endif
                    <option value="{{$voucher->voucher_id}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$voucher->voucher_name}}</option>

                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <div class="row  m-0 p-0">
                    <div class="col-md-6 m-0 p-0">
                        <label>Date From: </label>
                        <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{$form_date?? financial_end_date(date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6 m-0 p-0">
                        <label>Date To : </label>
                        <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{$to_date?? financial_end_date(date('Y-m-d')) }}">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label></label><br>
                <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
            </div>
        </div>
    </form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd ledger_summarytableFixHead">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers tree table-scroll">
        <thead>
            <tr>
                <th style="width:1%;" class="th">SL</th>
                <th style="width:8%;"class="th">Particulars</th>
                <th style="width: 4%;" class="debit_checkbox th text-end">Debit Amount</th>
                <th style="width: 4%;" class="credit_checkbox th text-end">Credit Amount</th>

            </tr>

        </thead>
        <tbody id="myTable" class="item_body ">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;"class="th"></th>
                <th style="width: 8%;" class="th text-end">Total :</th>
                <th style="width: 4%;font-size: 18px;"  class="th total_debit debit_checkbox text-end"></th>
                <th style="width: 4%;font-size: 18px;"  class="th total_credit credit_checkbox text-end"></th>
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
<script>

let  total_debit=0; total_credit=0;i=1;

// group chart  id check
if("{{$voucher_id??0}}"!=0){
    $('.voucher_id').val('{{$voucher_id??0}}');
}

// group wise  party ledger quantity
$(document).ready(function () {

    if("{{$voucher_id??0}}"!=0){
       local_store_ledger_voucher_register_set_data();
    }else{
        local_store_ledger_voucher_register_get();
    }
    ledger_voucher_register_initial_show();

    $("#ledger_voucher_register_form").submit(function(e) {
          local_store_ledger_voucher_register_set_data()
            print_date();
            $(".modal").show();
             total_debit=0; total_credit=0;i=1;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("report-account-ledger-voucher-register-data") }}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                    $(".modal").hide();
                    ledger_voucher_register(response)
                    },
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
   });


});
    // group wise party ledger function
    function  ledger_voucher_register(response){
        const children_sum= calculateSumOfChildren(response.data);
        $('.item_body').html(getTreeView(response.data,children_sum));
        $('.total_debit').text(((total_debit||0)||0).formatBangladeshCurrencyType("accounts"));
        $('.total_credit').text((total_credit||0).formatBangladeshCurrencyType("accounts"));
    }

// calculate child summation
function calculateSumOfChildren(arr) {
    const result = {};

    function sumProperties(obj, prop) {
        return obj.reduce((acc, val) => acc + (val[prop] || 0), 0);
    }

    function processNode(node) {
        if (!result[node.group_chart_id]) {
            result[node.group_chart_id] = {
                group_chart_id: node.group_chart_id,
                group_debit: 0,
                group_credit: 0,
            };
        }

        const currentNode = result[node.group_chart_id];
        currentNode. group_debit += node.group_debit || 0;
        currentNode.group_credit += node.group_credit || 0;
        if (node.children) {
            node.children.forEach(processNode);
        }
    }

    arr.forEach(processNode);

    return Object.values(result);
}

i=1;
function getTreeView(arr, children_sum, depth = 0, chart_id = 0) {
    let htmlFragments = [];
    arr.forEach(function (v) {
        a = '&nbsp;';
        h = a.repeat(depth);
      if (v.under != 0) {
            if (chart_id != v.group_chart_id) {
                let matchingChild = children_sum.find(c =>v.group_chart_id == c.group_chart_id);
                if (((matchingChild.group_debit|| 0) == 0) && ((matchingChild.group_credit || 0) == 0)) {} else {
                htmlFragments.push(`<tr id="${v.group_chart_id + '-' + v.under}" class='left left-data group_chart_id table-row'><td class="td"></td> <td style='width: 3%; border: 1px solid #ddd; font-size: 16px;' class="td-bold"><p style="margin-left:${(h+a).length-12}px;" class="text-wrap mb-0 pb-0 ">${v.group_chart_name}</p></td>`);


                if (matchingChild) {

                    htmlFragments.push(`<td class="td text-end td-bold"> ${((matchingChild.group_debit || 0)).formatBangladeshCurrencyType("accounts")} </td>
                                        <td class="td text-end td-bold"> ${((matchingChild.group_credit || 0)).formatBangladeshCurrencyType("accounts")}</td>`);

                    }

                htmlFragments.push(`</tr>`);
                }
                chart_id = v.group_chart_id;
            }

            if (v.ledger_head_id) {
                    total_debit+=v.total_debit||0;
                    total_credit+=v.total_credit||0;
                    htmlFragments.push(`<tr id="${v.ledger_head_id}" class="left left-data table-row ledger_id" >
                                            <td class="sl" style="width: 1%;  border: 1px solid #ddd;">${i++}</td>
                                            <td style='width: 3%;  border: 1px solid #ddd; font-size: 16px;color: #0B55C4' ><p style="margin-left:${(h+ a+a).length-12}px" class="text-wrap mb-0 pb-0">${accountLedgerMonthlySummaryIdWise(v.ledger_head_id,v.ledger_name)}</p></td>
                                            <td class='td text-end'>${(v.total_debit || 0).formatBangladeshCurrencyType("accounts")} </td>
                                            <td class='td text-end'>${(v.total_credit || 0).formatBangladeshCurrencyType("accounts")} </td>
                                    </tr>`);
            }
      }
            if ('children' in v) {
                htmlFragments.push(getTreeView(v.children, children_sum, depth + 1, chart_id));
            }

    });

    return htmlFragments.join('');
}

// Ledger Voucher Register
function ledger_voucher_register_initial_show(){
    print_date();
    $(".modal").show();
    $.ajax({
        url: '{{ route("report-account-ledger-voucher-register-data") }}',
            method: 'GET',
            data: {
                to_date:$('.to_date').val(),
                from_date:$('.from_date').val(),
                voucher_id:$(".voucher_id").val(),
            },
            dataType: 'json',
            success: function(response) {
                $(".modal").hide();
                ledger_voucher_register(response)
            },
            error : function(data,status,xhr){
                Unauthorized(data.status);
            }
    });
}

function local_store_ledger_voucher_register_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("voucher_id", '.voucher_id');
    }

function local_store_ledger_voucher_register_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("voucher_id", $('.voucher_id').val());
}

//get  all data show
$(document).ready(function () {
    // month   wise ledger route
    // $('.sd').on('click','.ledger_id',function(e){
    //     e.preventDefault();
    //     let ledger_id=$(this).closest('tr').attr('id');
    //     let form_date=$('.from_date').val();
    //     let to_date=$('.to_date').val();
    //     let voucher_id=$('.voucher_id').val();
    //     url = "{{route('account-ledger-monthly-summary-id-wise', ['ledger_id' =>':ledger_id', 'form_date' =>':form_date','to_date' =>':to_date','voucher_id' =>':voucher_id'])}}";
    //     url = url.replace(':ledger_id',ledger_id);
    //     url = url.replace(':form_date',form_date);
    //     url = url.replace(':to_date',to_date);
    //     url = url.replace(':voucher_id',voucher_id);
    //     window.open(url, '_blank');
    // });
      // table header fixed
      let display_height=$(window).height();
    $('.ledger_summarytableFixHead').css('height',`${display_height-120}px`);
});


</script>
@endpush
@endsection
