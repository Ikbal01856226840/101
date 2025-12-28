
@extends('layouts.backend.app')
@section('title',' Group Cash Flow')
@push('css')

 <style>
  .tree-node {
      display: none;
}
.tree-node.show {
    display: table-row;
}
.th{
    border: 1px solid #ddd;font-weight: bold;
}
.td{
    border: 1px solid #ddd; font-size: 18px;
}
.td-bold {
        font-weight: bold;
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
'page_title'=>'GroupCashFlow',
'size'=>'modal-xl',
'page_unique_id'=>27,
'groupChart'=>'yes',
'title'=>'GroupCashFlow',
'daynamic_function'=>'get_group_cash_flow_initial_show'
])
@endcomponent
<!-- add component-->
@component('components.report', [
    'title' => 'Group Cash Flow',
    'print_layout'=>'portrait',
    'print_header'=>'Group Cash Flow',
    'user_privilege_title'=>'GroupCashFlow',
    'report_setting_model'=>'report_setting_model',
    'report_setting_mail'=>'report_setting_mail'
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="group_cash_flow_form"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row ">
            <div class="col-md-4 ">
                <label></label>
                <select name="group_id" id="group_id" class="form-control  js-example-basic-single  group_id left-data group_id_add" required>
                    <option value="">--Select--</option>
                    {!!html_entity_decode($group_chart_data)!!}
                </select>
            </div>
            <div class="col-md-4 ">
                <div class="row  m-0 p-0">
                    <div class="col-md-6 m-0 p-0">
                        <label>Date From: </label>
                        <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{$form_date??financial_end_date(date('Y-m-d')) }}" >
                    </div>
                    <div class="col-md-6 m-0 p-0">
                        <label>Date To : </label>
                        <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{$to_date?? financial_end_date(date('Y-m-d')) }}">
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <label></label><br>
                <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
            </div>
        </div>
    </form>
@endslot

<!-- Main body component -->
@slot('main_body')
 <div class="dt-responsive table-responsive cell-border sd grow_cash_flow_summarytableFixHead " >
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers ">
        <thead>
            <tr>
                <th style="width: 1%;" class="th">SL.</th>
                <th style="width: 3%;"class="th">Particulars</th>
                <th style="width: 2%;"class="th text-end">Inflow</th>
                <th style="width: 3%;"class="th text-end">Outflow</th>
            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;"class="th"></th>
                <th style="width: 3%;"class="th text-end ">Total</th>
                <th style="width: 2%; font-size: 18px"class="th total_debit text-end"></th>
                <th style="width: 3%;font-size: 18px"class="th total_credit text-end"></th>
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
if("{{$group_id??0}}"!=0){
     $('.group_id').val("{{$group_id??0}}");
}

let total_debit=0,total_credit=0,i=1;
$(document).ready(function () {

    if("{{$group_id??0}}"!=0){
        local_store_group_cash_flow_summary_set_data();
    }else{
        local_store_group_cash_flow_summary_get();
    }

     get_group_cash_flow_initial_show();

    $("#group_cash_flow_form").submit(function(e) {

        total_debit=0,total_credit=0,i=1;

        local_store_group_cash_flow_summary_set_data();
        e.preventDefault();
        print_date();
        const fd = new FormData(this);
        $.ajax({
            url: '{{ route("group-cash-flow-get-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                   get_cash_flow(response)
                },
                error : function(data,status,xhr){
                  Unauthorized(data.status);
                }
        });
    });

 let display_height=$(window).height();
    $('.grow_cash_flow_summarytableFixHead').css('height',`${display_height-120}px`);

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
            };
        }

        const currentNode = result[node.group_chart_id];
        currentNode.group_debit += node.group_debit || 0;
        currentNode.group_credit += node.group_credit || 0;
        if (node.children) {
            node.children.forEach(processNode);
        }
    }
    arr.forEach(processNode);
    return Object.values(result);
}

    function getTreeView(arr, children_sum, depth = 0, chart_id = 0,group=1,under_id='') {
        let html = [];
        let under_unique=0;

        arr.forEach(function (v) {
            a = '&nbsp;&nbsp;';
            h = a.repeat(depth);
            if(v.under!=0){
                if (chart_id != v.group_chart_id) {
                            if(group==v.under){
                                if(under_unique!=v.under){
                                    under_id+=' '+v.under
                                    under_unique=v.under;
                                }
                            }
                        let matchingChild = children_sum.find(c =>v.group_chart_id == c.group_chart_id);
                        if (((matchingChild.group_debit|| 0) == 0) && ((matchingChild.group_credit || 0) == 0)) {} else {
                            html.push(`<tr id="${v.group_chart_id}" class='${under_id} table-row  ${group==v.under?'tree-node':''} left left-data'   data-id='${v.group_chart_id}' data-parent='${v.under}'>
                                        <td style='width: 1%;  border: 1px solid #ddd;'></td>
                                        <td style='width: 3%;  border: 1px solid #ddd; font-size: 16px; color: #0B55C4' class="td-bold">
                                            <span class="group_chart">
                                                ${groupCashFlowIdWise(v?.group_chart_id,h+a+v.group_chart_name)}
                                            </span>
                                            <span>
                                                ${v.ledger_head_id?'<i class="fa fa-plus fa-lg"  ></i>':v.children?'<i class="fa fa-plus fa-lg" aria-hidden="true"></i>':''}
                                                <i class="fa fa-minus fa-lg"  aria-hidden="true" style="display: none;"></i>
                                            </span>
                                    </td>`);


                                if (matchingChild) {
                                    html.push(`<td style='width: 3%;' class="td text-end td-bold">${(matchingChild.group_credit || 0).formatBangladeshCurrencyType("accounts")}</td>
                                            <td style='width: 3%;' class="td text-end td-bold">${((((matchingChild.group_debit||0))||0).formatBangladeshCurrencyType("accounts"))}</td>
                                        </tr>`);
                                }
                        }
                        chart_id = v.group_chart_id;
                }

                if (((v.op_total_debit|| 0) == 0) && ((v.op_total_credit || 0) == 0)) {} else {
                            total_debit += (v.op_total_credit|| 0);
                            total_credit += (v.op_total_debit || 0);

                    html.push(`<tr id="${v.ledger_head_id}" class="${under_id} ledger_id table-row tree-node " data-id_data-parent="${v.under}" data-parent="${v.group_chart_id}">
                                <td class="sl" style="width: 1%;  border: 1px solid #ddd;">${i++}</td>
                                <td style="width: 5%;color: #0B55C4; " class="td">
                                        ${ledgerCashFlowIdWise(v?.ledger_head_id,h+a+a+v.ledger_name)}
                                </td>
                                <td style='width: 3%;'class='td opening text-end'>${(v.op_total_credit || 0).formatBangladeshCurrencyType("accounts")}</td>
                                <td style='width: 3%; 'class='td inwards text-end'>${(v.op_total_debit||0).formatBangladeshCurrencyType("accounts")}</td>
                            </tr>`);
                }
            }
                if ('children' in v) {
                    let arr_gruop_id=[1,2,3,4,5,6];
                    let group_id=(arr_gruop_id.includes(v.group_chart_id)?0:v.group_chart_id)
                    html.push(getTreeView(v.children, children_sum, depth + 1, chart_id,group_id,under_id));
                }
            });

        return html.join("");
    }
//get group cash flow initial show
function  get_group_cash_flow_initial_show(){
       total_debit=0,total_credit=0,i=1;
       print_date();
        $.ajax({
            url: "{{ url('group-cash-flow-get-data')}}",
            type: 'GET',
            dataType: 'json',
            data:{
                to_date:$('.to_date').val(),
                from_date:$('.from_date').val(),
                group_id:$('.group_id').val()
            },
            success: function(response) {

            get_cash_flow(response)
            }
        })
    }
function get_cash_flow(response){
       total_debit=0,total_credit=0;
      const children_sum= calculateSumOfChildren(response.data);
       $('.item_body').html(getTreeView(response.data,children_sum));
       $('.total_debit').text(((total_debit||0)||0).formatBangladeshCurrencyType("accounts"));
       $('.total_credit').text((total_credit||0).formatBangladeshCurrencyType("accounts"));

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
     //get  all data show
$(document).ready(function () {
    // $('.sd').on('click','.group_chart',function(e){
    //     e.preventDefault();
    //     let   id=$(this).closest('tr').attr('id');
    //     let form_date=$('.from_date').val();
    //     let to_date=$('.to_date').val();
    //     url = "{{route('group-cash-flow-id-wise', ['id' =>':id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
    //     url = url.replace(':id',id);
    //     url = url.replace(':form_date',form_date);
    //     url = url.replace(':to_date',to_date);
    //     window.location=url;
    // });
    //  $('.sd').on('click','.ledger_id',function(e){
    //     e.preventDefault();
    //     let   id=$(this).closest('tr').attr('id');
    //     let form_date=$('.from_date').val();
    //     let to_date=$('.to_date').val();
    //     url = "{{route('ledger-cash-flow-id-wise', ['id' =>':id', 'form_date' =>':form_date','to_date' =>':to_date'])}}";
    //     url = url.replace(':id',id);
    //     url = url.replace(':form_date',form_date);
    //     url = url.replace(':to_date',to_date);
    //     window.location=url;
    //  });
    });

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
