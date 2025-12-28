
@extends('layouts.backend.app')
@section('title','Current Stock')
@push('css')
 <!-- model style -->
 <link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">
 <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
 <style>
  input[type=radio] {
    width: 20px;
    height: 20px;
 }
 input[type=checkbox] {
    width: 20px;
    height: 20px;
 }
</style>
@endpush
@section('admin_content')<br>
<!-- add component-->
@component('components.report', [
    'title' => 'Current Stock',
    'print_layout'=>'landscape',
    'print_header'=>'Current Stock',
]);

<!-- Page-header component -->
@slot('header_body')
    <form id="warehouse_form"  method="POST">
        @csrf
        {{ method_field('POST') }}
        <div class="row ">
            <div class="col-md-3">
                <label>Stock Group :</label>
                <select name="stock_group_id" class="form-control  js-example-basic-single  group_id" required>
                    <option value="">--Select--</option>
                    <option value="0">Primary</option>
                    {{-- {!!html_entity_decode($stock_group)!!} --}}
                </select>
            </div>
            <div class="col-md-3">
                <label>Godown Name :</label>
                <select name="godown_id" class="form-control  js-example-basic-single" required>
                    <option value="0">All</option>
                    {{-- @foreach($godowns as $godown)
                      <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="col-md-3">
                <div class="row  m-0 p-0">
                    <div class="col-md-6 m-0 p-0">
                        <label>Date From: </label>
                        <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{company()->financial_year_start }}">
                    </div>
                    <div class="col-md-6 m-0 p-0">
                        <label>Date To : </label>
                        <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{ date('Y-m-d') }}">
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
    <table id="tableId" style=" border-collapse: collapse; " class="table table-striped customers ">
        <thead>
            <tr>
                <th style="width: 1%;  border: 1px solid #ddd;font-weight: bold;">SL.</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Particulars</th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;">Current  Quantity</th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;">Current Stock Rate</th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Current Stock value</th>

            </tr>
        </thead>
        <tbody id="myTable" class="item_body">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;  border: 1px solid #ddd;font-weight: bold;"></th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;">Total :</th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;"></th>
                <th style="width: 2%;  border: 1px solid #ddd;font-weight: bold;font-size: 18px;"></th>
                <th style="width: 3%;  border: 1px solid #ddd;font-weight: bold;font-size: 18px;"></th>

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
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
var amount_decimals="{{company()->amount_decimals}}";
$(document).ready(function () {
    $("#warehouse_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            $.ajax({
                    url:'{{route("report-current-stock-data")}}',
                    method: 'POST',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        let  html='';
                        $.each(response.data, function(key, v) {
                            html +='<tr class="left left-data editIcon table-row">';
                                html += '<td style="width: 3%; border: 1px solid #ddd;">'+(key + 1) +'</td>';;
                                html += '<td style="width: 3%; border: 1px solid #ddd;">'+v.product_name+'</td>';
                                html += '<td style="width: 3%; border: 1px solid #ddd;">'+v.inwards_qty+'</td>';
                                html += '<td style="width: 3%; border: 1px solid #ddd;">'+v.inwards_rate+'</td>';
                                html += '<td style="width: 3%; border: 1px solid #ddd;">'+(v.inwards_qty*v.inwards_rate)+'</td>';
                            html += "</tr> ";
                        });
                        $('.item_body').html(html);
                        get_hover();
                        $('tbody').find('tr .sl').each(function(i){
                          $(this).text(i+1);
                        });
                    },
                    error : function(data,status,xhr){

                    }
            });
    });
});

</script>
@endpush
@endsection



