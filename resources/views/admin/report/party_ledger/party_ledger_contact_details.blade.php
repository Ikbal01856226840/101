
@extends('layouts.backend.app')
@section('title','Party Ledger Contact Details')
@push('css')
 <!-- model style -->
 <link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
 <style>
    .td{
        width: 3%;  border: 1px solid #ddd;
    }
    table {width:100%;grid-template-columns: auto auto;}
</style>
@endpush
@section('admin_content')<br>
<!-- add component-->
@component('components.report', [
    'title' => 'Party Contact Details',
    'print_layout'=>'No',
    'print_header'=>'Party Contact Details',
    'user_privilege_title'=>'PartyLedgeDetails',
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="party_ledger_contact_details_form"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row ">
            <div class="col-md-4">
                <select name="group_id" class="form-control js-example-basic-single group_id" required>
                    <option value="">--select--</option>
                    <option value="0">ALL</option>
                    {!!html_entity_decode($group_chart_id)!!}
                </select>
            </div>
            <div class="col-md-3">
                <button  type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style=" width:200px; margin-bottom:5px;" ><span class="m-1 m-t-1" ></span><span >Search</span></button>
            </div>
        </div>
    </form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_report">
    <table id="tableId" style=" border-collapse: collapse;" class="table table-striped customers ">
        <thead>
            <tr>
                <th style="width: 1%;  border: 1px solid #ddd;font-weight: bold;">SL.</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Particulars</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Under Group</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Alias</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Malling Address</th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;">Phone Number</th>
                
            </tr>
        </thead>
        <tbody id="myTable" class="ledger_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;  border: 1px solid #ddd;font-weight: bold;">SL.</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Particulars</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Under Group</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Alias</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Malling Address </th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;">Phone Number</th>
                
            </tr>
        </tfoot>
    </table>
</div>
@endslot
@endcomponent
<br>
@push('js')
<!-- table hover js -->
<script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script type="text/javascript" src="{{asset('dist/jquery-simple-tree-table.js')}}"></script>
<script>
var amount_decimals="{{company()->amount_decimals}}";
let i=1;
$("#party_ledger_contact_details_form").submit(function(e) {
        $(".modal").show();
        $('.ledger_body').empty();
        e.preventDefault();
        const fd = new FormData(this);
        $.ajax({
                url: '{{ route("party-ledger-contact-details-data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                     i=1;
                     if($('.group_id').val()==0){
                         const chunkSize =1; // Set your desired chunk size
                         const dataChunks = chunkArray(response.data, chunkSize);
                         let chunkIndex = 0;
                         function appendNextChunk() {
                            if (chunkIndex < dataChunks.length) {
                                $('.ledger_body').append(getTreeView(dataChunks[chunkIndex], depth = 0, 0));
                                chunkIndex++;
                                setTimeout(appendNextChunk, 100); // Delay between appending chunks
                            } 
                         }
                         appendNextChunk();
                      }else{
                        $('.ledger_body').html(getTreeView(response.data, depth = 0, 0));
                      }
                    
                },
                error : function(data,status,xhr){
              }
           });
});

function chunkArray(arr, chunkSize) {
    const chunks = [];
    for (let i = 0; i < arr.length; i += chunkSize) {
        chunks.push(arr.slice(i, i + chunkSize));
    }
    return chunks;
}

// get Tree view table row
function getTreeView(arr, depth = 0, chart_id = 0) {
    let html = [];
    arr.forEach(function(v) {
        a = '&nbsp;&nbsp;&nbsp;&nbsp;';
        h = a.repeat(depth);
        if (v.under != 0) {
            if (chart_id != v.group_chart_id) {
                html.push(`<tr style='pointer-events: none' class='left left-data editIcon table-row'>
                                <td class="td"></td>
                                <td class="td" style='color:#BBB;'>${h + a + v.group_chart_name}</td>
                                <td class="td"></td>
                                <td class="td"></td>
                                <td class="td"'></td>
                                <td class="td"></td>
                                
                                </tr>`);
                chart_id = v.group_chart_id;
            }
            if (v.ledger_name != null) {
                html.push(`<tr class="left left-data editIcon table-row">
                                <td class="td">${i++}</td>
                                <td class="td text-wrap"><p style="margin-left:${(h+a).length}px" class="text-wrap mb-0 pb-0">${v.ledger_name}</p></td>
                                <td class="td text-wrap"> ${v.group_chart_name||''}</td>
                                <td class="td text-wrap"> ${v.alias||''}</td>
                                <td class="td text-wrap">${v.mailing_add||''}</td>
                                <td class="td text-wrap">${v.mobile||''}</td>
                                
                            </tr>`);
            }
        }
        if ('children' in v) {
            html.push(getTreeView(v.children, depth + 1, chart_id));
        }
    });
    return html.join("");
}


</script>
@endpush
@endsection
