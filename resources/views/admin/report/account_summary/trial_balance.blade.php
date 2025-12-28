
@extends('layouts.backend.app')
@section('title','Trial Balance')
@push('css')
 <!-- model style -->
 <link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
 <style>
 table {width:100%;grid-template-columns: auto auto;}
 .td{
    width: 3%;  border: 1px solid #ddd; font-size: 16px; color: #0B55C4;
    font-family: Arial, sans-serif;
 }
 .th{
    border: 1px solid #ddd;font-weight: bold;
    font-family: Arial, sans-serif;
 }
 .table-scroll thead tr:nth-child(2) th {
    top: 30px;
}
.tree-node {
      display: none;
}
.tree-node.show {
    display: table-row;
}
</style>
@endpush
@section('admin_content')<br>
<!-- add component-->
@component('components.report', [
    'title' => 'Trial Balance',
    'print_layout'=>'landscape',
    'print_header'=>'Trial Balance',
    'user_privilege_title'=>'TrialBalance',
    'print_date'=>1,
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="trial_balance_form"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row ">
            <div class="col-md-6">
                <div class="row  m-0 p-0">
                    <div class="col-md-6 m-0 p-0">
                        <label>Date From: </label>
                        <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{$form_date?? company()->financial_year_start }}">
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
<div class="dt-responsive table-responsive cell-border sd tableFixHead_report">
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers tree table-scroll">
        <thead>
            <tr>
                <th style="width: 3%;" class="th">Particulars</th>
                <th style="width: 2%;" class="th opening_checkbox">Opening Balance</th>
                <th style="width: 3%;" class="th debit_checkbox">Debit Amount</th>
                <th style="width: 2%;" class="th credit_checkbox">Credit Amount</th>
                <th style="width: 3%;" class="th closing_checkbox">Closing Balance</th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body ">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 3%;" class="th">Total :</th>
                <th style="width: 2%;font-size: 18px;"  class="th total_opening opening_checkbox"></th>
                <th style="width: 3%;font-size: 18px;"  class="th total_debit debit_checkbox"></th>
                <th style="width: 2%;font-size: 18px;"  class="th total_credit credit_checkbox"></th>
                <th style="width: 3%;font-size: 18px;"  class="th total_clasing closing_checkbox"></th>
            </tr>
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
var amount_decimals="{{company()->amount_decimals}}";
let  total_opening=0; total_debit=0; total_credit=0;total_clasing=0;i=1;

$(document).ready(function () {
    // get trial balance
    function get_trial_balance_initial_show(){
             print_date();
            $(".modal").show();
            $.ajax({
                url: '{{ route("trial-balance-data") }}',
                    method: 'GET',
                    data: {
                        to_date:$('.to_date').val(),
                        from_date:$('.from_date').val(),
                        group_id:$(".group_id").val(),
                    },
                    dataType: 'json',
                    success: function(response) {
                        $(".modal").hide();
                       get_trial_balance(response)
                    },
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
        }

    get_trial_balance_initial_show();

    $("#trial_balance_form").submit(function(e) {
            print_date();
            $(".modal").show();
            total_opening=0; total_debit=0; total_credit=0;total_clasing=0;i=1;
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("trial-balance-data") }}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                    $(".modal").hide();
                   get_trial_balance(response)
                    },
                    error : function(data,status,xhr){
                        Unauthorized(data.status);
                    }
            });
   });

// trial balance function
function get_trial_balance(response){
        const children_sum= calculateSumOfChildren(response.data);
        $('.item_body').html(getTreeView(response.data,children_sum));
        $('.total_opening').text((total_opening||0).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
        $('.total_debit').text(((total_debit||0)||0).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
        $('.total_credit').text((total_credit||0).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
        $('.total_clasing').text((total_clasing||0).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
     }
});
// calcucation child summation
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

i=1;
function getTreeView(arr, children_sum, depth = 0, chart_id = 0,group=1,under_id='') {
    let htmlFragments = [];
    let under_unique=0;
    arr.forEach(function (v) {
        a = '&nbsp;&nbsp;&nbsp;&nbsp;';
        h = a.repeat(depth);
            if(v.under!=0){
                if (chart_id != v.group_chart_id) {
                    if(group==v.under){
                        if(under_unique!=v.under){
                            under_id+=' '+v.under
                            under_unique=v.under;
                        }
                    }
                    htmlFragments.push(`<tr id="${v.group_chart_id + '-' + v.under}" class='${under_id} table-row  ${group==v.under?'tree-node':''}'   data-id='${v.group_chart_id}' data-parent='${v.under}' > <td style='width: 3%; border: 1px solid #ddd; font-size: 16px; color: #0B55C4'><span class="group_chart">${h + a + v.group_chart_name}</span>
                        <span>
                           ${v.ledger_head_id?'<i class="fa fa-angle-double-down" style="font-size: x-large" ></i>':v.children?'<i class="fa fa-angle-double-down" style="font-size: x-large" aria-hidden="true"></i>':''}
                            <i class="fa fa-angle-double-up"  aria-hidden="true" style="display: none;font-size: x-large"></i>
                        </span>
                        </td>`);

                    let matchingChild = children_sum.find(c =>v.group_chart_id == c.group_chart_id);
                    if (matchingChild) {
                        htmlFragments.push(`<td class="td">  ${(v.nature_group == 1 || v.nature_group == 3 ? ((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0)) : (v.nature_group == 2 ||v.nature_group == 4 ? ((matchingChild.op_group_credit || 0) - (matchingChild.op_group_debit || 0)) : 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                                            <td class="td"> ${((matchingChild.group_debit || 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')} </td>
                                            <td class="td"> ${((matchingChild.group_credit || 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                                            <td class="td"> ${(v.nature_group == 1 || v.nature_group == 3 ? (((matchingChild.op_group_debit || 0) - (matchingChild.op_group_credit || 0) + (matchingChild.group_debit || 0)) - (matchingChild.group_credit || 0)) : (v.nature_group == 2 || v.nature_group == 4 ? ((matchingChild.op_group_credit || 0) - (matchingChild.op_group_debit || 0) + (matchingChild.group_credit || 0)) - (matchingChild.group_debit || 0) : 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')} </td>`);
                        }

                    htmlFragments.push(`</tr>`);
                    chart_id = v.group_chart_id;
                }

                if (v.ledger_head_id) {
                        let opening_blance=openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance) + (v.nature_group == 1 || v.nature_group == 3 ? (v.op_total_debit || 0) - (v.op_total_credit || 0) : (v.nature_group == 2 || v.nature_group == 4 ? (v.op_total_credit || 0) - (v.op_total_debit || 0) : 0));
                        total_opening =(total_opening+opening_blance);
                        total_debit += (v.total_debit || 0);
                        total_credit += (v.total_credit || 0);
                        let clasing_blance=openning_blance_cal(v.nature_group,v.DrCr,v.opening_balance) + (v.nature_group == 1 || v.nature_group == 3 ? (((v.op_total_debit || 0) - (v.op_total_credit || 0) + (v.total_debit || 0)) - (v.total_credit || 0)) : (v.nature_group == 2 || v.nature_group == 4 ? ((v.op_total_credit || 0) - (v.op_total_debit || 0) + (v.total_credit || 0)) - (v.total_debit || 0) : 0));
                        total_clasing=(total_clasing+clasing_blance);
                        htmlFragments.push(`<tr id="${v.ledger_head_id}" class="${under_id} ledger_id table-row tree-node " data-id_data-parent="${v.under}" data-parent="${v.group_chart_id}"  >
                                                <td style='width: 3%;  border: 1px solid #ddd; font-size: 16px; color: #0B55C4'><p style="margin-left:${(h + a + a + a).length}px" class="text-wrap mb-0 pb-0 ">${v.ledger_name}</p></td>
                                                <td  class='td opening'>${(opening_blance).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')} </td>
                                                <td class='td'>${((v.total_debit || 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')} </td>
                                                <td class='td'>${((v.total_credit || 0)).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')} </td>
                                                <td class='td'> ${(clasing_blance).toFixed(amount_decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                                        </tr>`);
                }
            }
            if ('children' in v) {
                let arr_gruop_id=[1,2,3,4,5,6];
                let group_id=(arr_gruop_id.includes(v.group_chart_id)?0:v.group_chart_id)
                htmlFragments.push(getTreeView(v.children, children_sum, depth + 1, chart_id,group_id,under_id));

            }
    });
    return htmlFragments.join('');
}

//get  all data show
$(document).ready(function () {
    // party ledger  wise summary route
    $('.sd').on('click','.ledger_id',function(e){
        e.preventDefault();
        let ledger_id=$(this).closest('tr').attr('id');
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        url = "{{route('account-ledger-voucher-id-wise', ['ledger_id' =>':ledger_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':ledger_id',ledger_id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        window.location=url;
    });

   //  ledger voucher route
    $('.sd').on('click','tbody tr .group_chart',function(e){
        e.preventDefault();
        let  group_chart_id=$(this).closest('tr').attr('id');
        let form_date=$('.from_date').val();
        let to_date=$('.to_date').val();
        url = "{{route('account-group-summary-id-wise', ['group_chart_id' =>':group_chart_id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
        url = url.replace(':group_chart_id',group_chart_id);
        url = url.replace(':form_date',form_date);
        url = url.replace(':to_date',to_date);
        window.location=url;
    })
});
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
