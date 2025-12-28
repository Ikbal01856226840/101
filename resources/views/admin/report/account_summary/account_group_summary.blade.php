
@extends('layouts.backend.app')
@section('title','Account Group Summary')
@push('css')
 <style>
    .td{
        width: 3%;  border: 1px solid #ddd; font-size: 18px;text-align: right;
        font-family: Arial, sans-serif;
    }
    .th{
        border: 1px solid #ddd;font-weight: bold;
        font-family: Arial, sans-serif;
    }

    .tree-node {
        display: none;
    }
    .td-bold {
            font-weight: bold;
    }
    .tree-node.show {
        display: table-row;
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
    'page_title'=>'Account Group Summary',
    'size'=>'modal-xl',
    'page_unique_id'=>30,
    'groupChart'=>'yes',
    'title'=>'Account Group Summary',
    'daynamic_function'=>'ledger_voucher_register_initial_show'
]);
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Account Group Summary',
    'print_layout'=>'portrait',
    'print_header'=>'Account Group Summary',
    'user_privilege_title'=>'AccountGroupSummary',
    'print_date'=>1,
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="account_group_summary_form"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row ">
            <div class="col-md-4">
                <label>Accounts Group :</label>
                <select name="group_id" class="form-control  js-example-basic-single  group_id" required>
                    <option value="0">--All--</option>
                    {!!html_entity_decode($group_chart_data)!!}
                </select>
            </div>
            <div class="col-md-4">
                <div class="row  m-0 p-0">
                    <div class="col-md-6 m-0 p-0">
                        <label>Date From: </label>
                        <input type="hidden" name="without_op_bla" class="form-control setup_date fs-5 without_op_bla" value="{{$without_opening_blance??0}}">
                        <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{$form_date?? financial_end_date(date('Y-m-d'))}}">
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
            <div class="col-md-12">
                <div>
                    <input class="form-check-input opening_balance_checkbox" type="checkbox"  name="opening_balance"  checked>
                    <label class="form-check-label fs-6" for="flexRadioDefault1" >
                        Opening Balance
                    </label>
                    <input class="form-check-input transactions_checkbox" type="checkbox"  name="transactions"   checked>
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Transactions
                    </label>
                    {{-- <label class="form-check-label fs-6" for="flexRadioDefault1" >
                        &nbsp; &nbsp;&nbsp;&nbsp; Closing Balance having :
                    </label>
                    <input class="form-check-input closing_blance" type="radio" name="closing_blance" value="1">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Dr
                    </label>
                    <input class="form-check-input closing_blance" type="radio" name="closing_blance" value="2">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        Cr
                    </label>
                    <input class="form-check-input closing_blance" type="radio" name="closing_blance" value="3"  checked="checked">
                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                        all
                    </label> --}}
                </div>
            </div>
        </div>
    </form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd accounts_summarytableFixHead">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers tree table-scroll">
        <thead>
            <tr>
                <th rowspan="2" style="width: 1%;" class="th">SL</th>
                <th rowspan="2" style="width: 3%;"class="th">Particulars</th>
                <th rowspan="2" style="width: 3%;text-align:right;" class="opening_checkbox th">Opening Balance</th>
                <th colspan="2" style=" width: 5%; text-align:center;" class="th transactions">Transactions</th>
                <th rowspan="2" style="width: 3%;  text-align:right;" class="closing_checkbox th">Closing Balance</th>
            </tr>
            <tr>
                <th style="width: 3%;" class="transactions th text-end">Debit Amount</th>
                <th style="width: 2%;" class="transactions th text-end">Credit Amount</th>
            </tr>

        </thead>
        <tbody id="myTable" class="item_body ">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;"class="th"></th>
                <th style="width: 3%;" class="th text-end">Total :</th>
                <th style="width: 3%;font-size: 18px;"  class="th total_opening opening_checkbox text-end"></th>
                <th style="width: 3%;font-size: 18px;"  class="th total_debit transactions text-end"></th>
                <th style="width: 2%;font-size: 18px;"  class="th total_credit transactions text-end"></th>
                <th style="width: 3%;font-size: 18px;"  class="th total_clasing closing_checkbox text-end"></th>


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

let  total_opening=0; total_debit=0; total_credit=0;total_clasing=0;i=1; group_id_sum='';nature_group=0;
// group chart  id check
if("{{$group_id??0}}"!=0){
     $('.group_id').val('{{$group_id??0}}');
}

// group wise  party ledger quantity
$(document).ready(function () {

    // group chart get id check
    if("{{$group_id??0}}"!=0){
        local_store_group_cash_flow_summary_set_data();
    }else{
        local_store_group_cash_flow_summary_get();
    }
    get_account_group_summary_initial_show();

    $("#account_group_summary_form").submit(function(e) {
            local_store_group_cash_flow_summary_set_data();
            $(".modal").show();
            print_date();
            total_opening=0; total_debit=0; total_credit=0;total_clasing=0;i=1;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("account-group-summary-data") }}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                    $('.item_body').empty();
                     $(".modal").hide();
                    chcking_checkbok();
                    get_account_group_summary(response)
                    },
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
      });
});

// group wise party ledger function
function  get_account_group_summary(response){
       const children_sum= calculateSumOfChildren(response.data);
        if($('.group_id').val()==0){
            get_account_main_group_summation(response)
        }

       $('.item_body').html(getTreeView(response.data,children_sum));

       if($('.group_id').val()!=0){
            let op_sign ='';cla_sign='';

            if(nature_group== 1 || nature_group== 3){
                op_sign=total_opening >= 0 ? 'Dr' : 'Cr'
                cla_sign=total_clasing >= 0 ? 'Dr' : 'Cr'
            }else{
                op_sign=total_opening >= 0 ? ' Cr' : 'Dr'
                cla_sign=total_clasing >= 0 ? 'Cr' : 'Dr'
            }
        $('.total_opening').text((Math.abs(total_opening)).formatBangladeshCurrencyType("accounts",'',(op_sign)));
        $('.total_clasing').text((Math.abs(total_clasing)).formatBangladeshCurrencyType("accounts",'',(cla_sign)));
       }
       $('.total_credit').text((total_credit||0).formatBangladeshCurrencyType("accounts"));
       $('.total_debit').text(((total_debit||0)||0).formatBangladeshCurrencyType("accounts"));


}

// alcucation child summation
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
                op_group_debit: 0,
                op_group_credit: 0
            };
        }

        const currentNode = result[node.group_chart_id];
        currentNode. group_debit += node.group_debit || 0;
        currentNode.group_credit += node.group_credit || 0;
        currentNode.op_group_debit += node.op_group_debit || 0;
        currentNode.op_group_credit += node.op_group_credit || 0;
        if (node.children) {
            node.children.forEach(processNode);
        }
    }

    arr.forEach(processNode);

    return Object.values(result);
}

function getTreeView(arr, children_sum, depth = 0, chart_id = 0,group=1,under_id='') {
    let   htmlFragments = [];
    let under_unique=0;
    let select_group_id=$('.group_id').val().split("-")[0]??0;
    console.log(select_group_id);
    arr.forEach(function (v) {
        a = '&nbsp;&nbsp;';
        h = a.repeat(depth);
        if(v.under!=0){
            if (chart_id != v.group_chart_id) {
                        if(group==v.under){
                            if(under_unique!=v.under){
                                under_id+=''+v.under
                                under_unique=v.under;
                            }
                        }
                    let matchingChild = children_sum.find(c =>v.group_chart_id == c.group_chart_id);
                      nature_group=v.nature_group;
                     if (((matchingChild.group_debit|| 0) == 0) && ((matchingChild.group_credit || 0) == 0)&& ((matchingChild.op_group_debit || 0)== 0)&& ((matchingChild.op_group_credit || 0) == 0)) {} else {




                            if (matchingChild) {
                                let total_op_val;
                                let total_op_sign;
                                let total_closing_val;
                                let total_closing_sign;
                                if(v.nature_group == 1 || v.nature_group == 3){
                                    total_op_val=((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0));
                                    total_op_sign = total_op_val >= 0 ? 'Dr' : 'Cr';
                                }else{
                                    total_op_val=((matchingChild.op_group_credit || 0) -(matchingChild.op_group_debit || 0))
                                    total_op_sign = total_op_val >= 0 ? 'Cr' : 'Dr';
                                }
                                const totalOpeningBalance = Math.abs(total_op_val).formatBangladeshCurrencyType("accounts", '',  total_op_sign);
                                if(v.nature_group == 1 || v.nature_group == 3){
                                    total_closing_val=(((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0) + (matchingChild.group_debit || 0)) - (matchingChild.group_credit || 0));
                                    total_closing_sign = total_closing_val >= 0 ? 'Dr' : 'Cr';
                                }else{
                                    total_closing_val=((matchingChild.op_group_credit || 0) - (matchingChild.op_group_debit || 0) + (matchingChild.group_credit || 0)) - (matchingChild.group_debit || 0);
                                    total_closing_sign = total_closing_val >= 0 ? 'Cr' : 'Dr';
                                }
                                const totalCurrentBalance = Math.abs(total_closing_val).formatBangladeshCurrencyType("accounts", '', total_closing_sign);
                                if(v.group_chart_id)
                                htmlFragments.push(`<tr id="${v.group_chart_id + '-' + v.under}" class='${under_id} table-row  ${group==v.under?'tree-node':''} left left-data'   data-id='${v.group_chart_id}' data-parent='${v?.under}'>
                                    <td style='width: 1%;  border: 1px solid #ddd;'></td>
                                    <td style='width: 3%;  border: 1px solid #ddd; font-size: 16px; color: #0B55C4' class="td-bold"><span class="group_chart">${accountGroupSummaryIdWise((v.group_chart_id + '-' + v?.under),h+a+v.group_chart_name)}</span>
                                                <span>
                                                ${v.ledger_head_id?'<i class="fa fa-plus fa-lg"  ></i>':v.children?'<i class="fa fa-plus fa-lg" aria-hidden="true"></i>':''}
                                                    <i class="fa fa-minus fa-lg"  aria-hidden="true" style="display: none;"></i>
                                                </span>
                                </td>`);

                                htmlFragments.push(`${$(".opening_balance_checkbox").is(':checked') ?`<td class="td td-bold">  ${totalOpeningBalance}</td>`:""}
                                        ${$(".transactions_checkbox").is(':checked') ?`<td class="td td-bold"> ${((matchingChild.group_debit || 0)).formatBangladeshCurrencyType("accounts")} </td>
                                        <td class="td td-bold"> ${((matchingChild.group_credit || 0)).formatBangladeshCurrencyType("accounts")}</td>`:""}
                                        <td class="td td-bold"> ${totalCurrentBalance} </td>`);
                            }
                     }
                    chart_id = v.group_chart_id;
            }

            if (v.ledger_head_id) {
                    let opening_val;
                    let op_sign;
                    let clasing_val;
                    let closing_sign;
                    if(v.nature_group == 1 || v.nature_group == 3){
                        opening_val=openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance)+(v.op_total_debit || 0) - (v.op_total_credit || 0);
                        op_sign = opening_val >= 0 ? 'Dr' : 'Cr';
                    }else{
                        opening_val=openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance)+(v.op_total_credit || 0) - (v.op_total_debit || 0);
                        op_sign = opening_val >= 0 ? 'Cr' : 'Dr';
                    }
                    total_opening =total_opening+ parseFloat(opening_val);
                    const openingBalance = Math.abs(opening_val).formatBangladeshCurrencyType("accounts",'',op_sign);

                    total_debit += (v.total_debit || 0);
                    total_credit += (v.total_credit || 0);
                    if(v.nature_group == 1 || v.nature_group == 3){
                        clasing_val=openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance)+(((v.op_total_debit||0) - (v.op_total_credit||0) + (v.total_debit||0)) - (v.total_credit||0));
                        closing_sign=clasing_val >= 0 ? 'Dr' : 'Cr';
                    }else{
                        clasing_val=openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance)+(((v.op_total_credit || 0) - (v.op_total_debit || 0) + (v.total_credit || 0)) - (v.total_debit || 0));
                        closing_sign=clasing_val >= 0 ? 'Cr' : 'Dr';
                    }


                    total_clasing +=parseFloat(clasing_val);


                    const currentBalance = Math.abs(clasing_val).formatBangladeshCurrencyType("accounts",'',closing_sign);
                    if (((opening_val || 0) == 0) && ((v.total_debit || 0) == 0) && ((v.total_credit || 0) == 0)) {} else {
                        htmlFragments.push(`<tr id="${v.ledger_head_id}" class="${under_id} ledger_id table-row ${select_group_id==v.group_chart_id?'':'tree-node'} "  data-id_data-parent="${v.under}" data-parent="${v.group_chart_id}">
                               <td class="sl" style="width: 1%;  border: 1px solid #ddd;">${i++}</td>
                               <td style="width: 5%;width: 1%;  border: 1px solid #ddd; color: #0B55C4">${accountLedgerMonthlySummaryIdWise(v.ledger_head_id,h+a+a+v.ledger_name)}</td>
                               ${$(".opening_balance_checkbox").is(':checked') ?`<td  class='td opening'>${(openingBalance)} </td>`:""}
                               ${$(".transactions_checkbox").is(':checked') ?`<td class='td'>${((v.total_debit || 0)).formatBangladeshCurrencyType("accounts")} </td>
                                                <td class='td'>${((v.total_credit || 0)).formatBangladeshCurrencyType("accounts")} </td>`:""}
                                                <td class='td'> ${(currentBalance)}</td>
                                        </tr>`);
                    }
            }
        }
            if ('children' in v) {
                let arr_gruop_id=[1,2,3,4,5,6];
                let group_id=(arr_gruop_id.includes(v.group_chart_id)?0:v.group_chart_id)

                htmlFragments.push(getTreeView(v.children, children_sum, depth + 1, chart_id,group_id,under_id));
            }
        });

    return   htmlFragments.join("");
}


// get party ledger
function get_account_group_summary_initial_show(){
           print_date();
           $(".modal").show();
            $.ajax({
                url: '{{ route("account-group-summary-data") }}',
                    method: 'GET',
                    data: {
                        to_date:$('.to_date').val(),
                        from_date:$('.from_date').val(),
                        group_id:$(".group_id").val(),
                        without_op_bla:"{{$without_opening_blance??0}}",
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('.item_body').empty();
                        $(".modal").hide();
                        get_account_group_summary(response)
                    },
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
    }

    function local_store_group_cash_flow_summary_get() {
        getStorage("end_date", '.to_date');
        getStorage("start_date", '.from_date');
        getStorage("group_id", '.group_id');
    }

    function local_store_group_cash_flow_summary_set_data() {
        setStorage("end_date", $('.to_date').val());
        setStorage("start_date", $('.from_date').val());
        setStorage("group_id", $('.group_id').val());
    }
    function get_account_main_group_summation(response){

        let total_opening = response.data
        ?.map((v) => {
            let matchingChild = v;
            let total_op_val;
            total_op_val = (matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0);
            return total_op_val;
        })
        .reduce((a, c) => a + c, 0);

        $('.total_opening').text((Math.abs(total_opening)).formatBangladeshCurrencyType("accounts",'',(total_opening>0?'Dr':'Cr')));
        let total_clasing = response.data
        ?.map((v) => {
            let matchingChild = v;
            let total_clasing;
            total_clasing = (((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0) + (matchingChild.group_debit || 0)) - (matchingChild.group_credit || 0));
            return total_clasing;
        })
        .reduce((a, c) => a + c, 0);

        $('.total_clasing').text((Math.abs(total_clasing)).formatBangladeshCurrencyType("accounts",'',(total_clasing>0?'Dr':'Cr')));
    }

//get  all data show
$(document).ready(function () {
    // month   wise ledger route
    // $('.sd').on('click','.ledger_id',function(e){
    //     e.preventDefault();
    //     let ledger_id=$(this).closest('tr').attr('id');
    //     let form_date=$('.from_date').val();
    //     let to_date=$('.to_date').val();
    //     url = "{{route('account-ledger-monthly-summary-id-wise', ['ledger_id' =>':ledger_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
    //     url = url.replace(':ledger_id',ledger_id);
    //     url = url.replace(':form_date',form_date);
    //     url = url.replace(':to_date',to_date);
    //     window.location=url;
    // });



   // group   ledger wise summary route
//    $('.sd').on('click','.group_chart',function(e){
//         e.preventDefault();
//         let  group_chart_id=$(this).closest('tr').attr('id');
//         let form_date=$('.from_date').val();
//         let to_date=$('.to_date').val();
//         url = "{{route('account-group-summary-id-wise', ['group_chart_id' =>':group_chart_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
//         url = url.replace(':group_chart_id',group_chart_id);
//         url = url.replace(':form_date',form_date);
//         url = url.replace(':to_date',to_date);
//         window.open(url, '_blank');
//     });


     // table header fixed
     let display_height=$(window).height();
    $('.accounts_summarytableFixHead').css('height',`${display_height-120}px`);

});
function chcking_checkbok(){
        if($(".opening_balance_checkbox").is(':checked')==true){
            $(".opening_checkbox").removeClass("d-none");
        }else{
            $(".opening_checkbox").addClass("d-none");
        }
        if($(".transactions_checkbox").is(':checked')==true){
            $(".transactions").removeClass("d-none");
        }else{
            $(".transactions").addClass("d-none");
        }


   }

    function accountLedgerMonthlySummaryIdWise(id,name){
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        let url = "{{route('account-ledger-monthly-summary-id-wise', ['ledger_id' =>':ledger_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':ledger_id',id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
                return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                    href="${url}">
                    ${name || ''}
                </a>
                <spna class="display-none">${name || ''}</spna>`;
    }
    function accountGroupSummaryIdWise(id,name){

        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        let url = "{{route('account-group-summary-id-wise', ['group_chart_id' =>':group_chart_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':group_chart_id',id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        return `<a class="d-print-none" style="text-decoration: none; font-size: 15px; color: #0B55C4;" 
                    href="${url}">
                    ${name || ''}
                </a>
                <spna class="display-none">${name || ''}</spna>`;
    }
</script>
<script>
    // table expand collapse rows
     document.addEventListener('DOMContentLoaded', function() {
       const dataTable = document.getElementById('tableId');

       dataTable.addEventListener('click', function(e) {
         const target = e.target;

         const tr = target.closest('tr');

         $(tr).find('i:eq(1)').toggle('show');
         $(tr).find('i:eq(0)').toggle('show');

         if (tr) {
             const nodeId = tr.dataset.id;
             const childNodes = document.querySelectorAll(`tr[data-parent="${nodeId}"]`);
             let key=0;
             childNodes.forEach(node => {
                 node.classList.toggle('show');
             });
             const childNodes1 = document.getElementsByClassName(`${nodeId}`);
             [...childNodes1]?.forEach(node => {
                 if(node.classList.contains('show')){
                     let parent=node.getAttribute("data-parent");
                     if(key!=parent && parent!=nodeId){
                         const childNodes1 = document.querySelector(`tr[data-id="${parent}"]`).click();
                         key=parent;
                     }
                 }
             });
         }
       });
     });
   </script>
@endpush
@endsection
