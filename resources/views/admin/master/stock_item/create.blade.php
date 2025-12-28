@extends('layouts.backend.app')
@section('title','Stock Item')
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
</style>
@endpush
@section('admin_content')
<br>
<!-- stock item add model  -->
@component('components.create', [
    'title' => 'Stock Item [New]',
    'help_route'=>route('stock-item.index'),
    'close_route'=>route('master-dashboard'),
    'veiw_route'=>route('stock-item.index'),
    'form_id' => 'add_stock_item_form',
    'method'=> 'POST',

])
@php
 $page_wise_setting_data=page_wise_setting(Auth::user()->id,13);
 if($page_wise_setting_data){
    $redirect=$page_wise_setting_data->redirect_page;
    $last_insert=$page_wise_setting_data->last_insert_data_set;
 }else{
    $redirect=3;
    $last_insert=0;
 }
@endphp
@slot('body')
<div class="row">
    <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
        <div class="card-block ">
            <fieldset class="border p-2">
                <legend class="float-none w-auto p-2">Stock Item</legend>
                <div class="form-group ">
                    <label for="exampleInputEmail1">Under Group :</label>
                    <select name="stock_group_id" class="form-control js-example-basic-single  js-example-basic stock_group_id left-data" required>
                        <option value="">--Select--</option>
                        <option value="1">Primary</option>
                        {!!html_entity_decode($select_option_tree)!!}
                    </select>
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput">Name :</label>
                    <input type="text" name="product_name" class="form-control " id="formGroupExampleInput" placeholder=" Name" required>
                    <span id='error_product_name' class=" text-danger"></span>
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput"> Bangla Name Optional :</label>
                    <input type="text" name="bangla_product_name" class="form-control " id="formGroupExampleInput" placeholder="Bangla Name" >
                </div>
                <div class="form-group ">
                    <label for="formGroupExampleInput2">Unit of Measure : </label>
                    <select name="unit_of_measure_id" class="form-control  js-example-basic-single   left-data" required>
                        <option value="">--Select--</option>
                        @foreach ($sizes as $size)
                        <option value="{{$size->unit_of_measure_id}}">{{$size->symbol}}</option>
                        @endforeach

                    </select>
                </div>
                <div class="form-group ">
                    <label for="exampleInputEmail1">Unit/Branch :</label>
                    <select name="unit_or_branch" class="form-control  js-example-basic-single unit_or_branch" required>
                        <option value="0">--select--</option>
                        @foreach (unit_branch() as $branch)
                        <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                        @endforeach
                    </select>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6 ">
        <div class="card-block  ">
            <div class=" m-t-0 " style="margin-left:-36px;">
                <div style="margin-left: 36px;">
                    <fieldset class="border p-2">
                        <legend class="float-none w-auto p-2">Optional Settings</legend>
                        <div class="form-group">
                            <label for="formGroupExampleInput">Alias :</label>
                            <input type="text" name="alias" class="form-control" id="formGroupExampleInput" placeholder="Alias Name">
                            <span id='error_alias' class=" text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput">Rate of Duty (optional) :</label>
                            <input type="number" name="rateofduty" class="form-control" id="formGroupExampleInput" placeholder="Rate of Duty ">
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="card-block ">
        <fieldset class="border p-2">
            <legend class="float-none w-auto p-2">Opening Balance</legend>
            <div class="form-group col-sm-3">
                <label for="exampleInputEmail1">Godowns :</label>
                <select name="godown" class="form-control js-example-basic-single  js-example-basic godown left-data">
                    @foreach ($godowns as $godown)
                    <option value="{{ $godown->godown_id }}">{{$godown->godown_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table " id="orders">
                        <tr>
                            <th class="col-0.5">#</th>
                            <th class="col-4">Godown</th>
                            <th class="col-3">Quantity</th>
                            <th class="col-3">Price</th>
                            <th class="col-2">Amount</th>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 m-0">
                    <td class="py-1"><button type="button" name="add" id="add" class="btn btn-success circle py-1 ">+</button></td>
                </div>
            </div>
        </fieldset>
    </div>
</div>
@endslot
@slot('footer')
<div class="col-lg-6 ">
    <div class="form-group">
        <button type="submit" id="add_stock_item_btn" class="btn hor-grd btn-grd-primary btn-block submit" style="width:100%">Add</button>
    </div>
</div>
<div class="col-lg-6">
    <div class="form-group">
        <a class=" btn hor-grd btn-grd-success btn-block " href="{{route('master-dashboard')}}" style="width:100%">Close</a>
    </div>
</div>
@endslot
@endcomponent
@push('js')
<!-- table hover js -->
<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
    $('.stock_group_id').on('change', function() {
        localStorage.setItem("stock_group_id", $('.stock_group_id').val());

    });
    if({{$last_insert}}==5){
      $('.stock_group_id').val(localStorage.getItem("stock_group_id"))
    }
    $(function() {
        // add new stock item ajax request
        $("#add_stock_item_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            $("#add_stock_item_btn").text('Add');
            $.ajax({
                url: '{{ route("stock-item.store") }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data, status, xhr) {
                    claer_error();
                    swal({
                        title: 'Success!',
                        text: data.message,
                        type: 'success',
                        timer: '1500'
                    })
                    if({{$redirect}}==0){
                        location.replace('{{ route("stock-item.create") }}')
                    }else{
                        location.replace('{{ route("stock-item.index") }}')
                    }
                    $("#add_stock_item_btn").text('Add');

                },
                error: function(data, status, xhr) {
                    claer_error();
                    if (data.status == 400) {
                        swal({
                            title: 'Oops...',
                            text: data.message,
                            type: 'error',
                            timer: '1500'
                        });
                    }
                    if (data.status == 422) {
                        claer_error();
                        $('#error_product_name').text(data.responseJSON.data.product_name?data.responseJSON.data.product_name[0]:'');
                        $('#error_alias').text(data.responseJSON.data.alias?data.responseJSON.data.alias[0]:'');

                    }

                }
            });
        });

    });
    //data validation data clear
    function claer_error() {
        $('#error_product_name').text('');
        $('#error_alias').text('');
    }

    $(document).ready(function() {
        var rowCount;
        addrow();
        $('#add').click(function() {
            rowCount += 5;
            addrow(rowCount);

        });

        function getId(element) {
            var id, idArr;
            id = element.attr('id');
            idArr = id.split("_");
            return idArr[idArr.length - 1];
        }

        function addrow(rowCount) {
            if (rowCount == null) {
                rowCount = 1;
            } else {
                rowCount = rowCount;
            }

            for (var row = 1; row < 6; row++) {
                rowCount++;
                $('#orders').append('<tr  id="row' + rowCount + '">' +
                    '<input class="form-control autocomplete_txt" type="hidden" data-type="product_id" id="product_id_' + rowCount + '"  for="' + rowCount + '"/>' +
                    '<td class="m-0 p-0"><button type="button" name="remove" id="' + rowCount + '" class="btn btn-danger btn_remove cicle m-1  py-1">-</button></td>' +
                    '<td class="m-0 p-0"><input class="form-control product_name  autocomplete_txt"  type="text" data-type="product_name" id="product_name_' + rowCount + '" " autocomplete="off" for="' + rowCount + '"/></td>' +
                    '<td class="m-0 p-0"><input class="form-control qty text-right "   data-field-name="qty" type="number" class="qty" id="qty_' + rowCount + '"  for="' + rowCount + '"/> </td>' +
                    '<td class="m-0 p-0"><input class="form-control rate text-right" data-field-name="rate" type="number" data-type="rate" id="rate_' + rowCount + '"  for="' + rowCount + '"/></td>' +
                    '<td class="m-0 p-0"><input class="form-control total_cost text-right" type="text" id="total_cost_' + rowCount + '"   for="' + rowCount + '" readonly/> </td>' +
                    '</tr>');
            }
        }
    });
</script>
@endpush
@endsection
