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
@php
  function getTreeViewSelectOption($arr, $depth = 0,$h){
    $html = '';
    $t='';
    foreach ($arr as $v) {
        if($v['under']!=0){
        if($v['stock_group_id']==$h){
        $t='selected';
        }else{
        $t='';
        }
        $html .= '<option value="' . $v['stock_group_id'] . '" '.$t.'>';
        $html .= str_repeat('&nbsp;&nbsp;&nbsp; ', $depth);
        $html .= '&nbsp;&nbsp;&nbsp;'. $v['stock_group_name'] .'</option>' . PHP_EOL;
        }
        if (array_key_exists('children', $v)) {
            $html .=getTreeViewSelectOption($v['children'], $depth + 1,$h);
        }
    }
    return $html;
}
@endphp
@section('admin_content')
<br>
<!-- stock item add model  -->
@component('components.create', [
    'title'    => 'Stock Item [Update]',
    'help_route'=>route('stock-item.index'),
    'close_route'=>route('master-dashboard'),
    'veiw_route'=>route('stock-item.index'),
    'form_id' => 'edit_stock_item_form',
    'method'=> 'PUT',

])
@php
$page_wise_setting_data=page_wise_setting(Auth::user()->id,13);
@endphp
    @slot('body')
      <div class="row">
        <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6" >
            <div class="card-block ">
                <fieldset class="border p-2">
                    <legend  class="float-none w-auto p-2">Stock Item</legend>
                        <div class="form-group ">
                            <label  for="exampleInputEmail1">Under Group :</label>
                                <select name="stock_group_id"  class="form-control js-example-basic-single  js-example-basic stock_group_id left-data" required>
                                    
                                    <option {{$data->stock_group_id==1 ? 'selected' : '' }} value="1">Primary</option>
                                    {!!html_entity_decode(getTreeViewSelectOption($stock_group_tree, $depth = 0,$data->stock_group_id))!!}
                                </select>
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput">Name :</label>
                            <input type="text" name="product_name" class="form-control "  id="formGroupExampleInput" placeholder=" Name" value="{{ $data->product_name }}">
                            <input type="hidden" name="id" class="form-control id "   value="{{ $data->stock_item_id }}">
                            <span id='edit_error_product_name' class=" text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput"> Bangla Name Optional :</label>
                            <input type="text" name="bangla_product_name" class="form-control " id="formGroupExampleInput" placeholder="Bangla Name" value="{{ $data->bangla_product_name}}">
                        </div>
                        <div class="form-group ">
                            <label for="formGroupExampleInput2">Unit of Measure : </label>
                            <select name="unit_of_measure_id" class="form-control  js-example-basic-single   left-data" required>
                                <option value="0">--Select--</option>
                                @foreach ($sizes as $size)
                                <option {{ $data->unit_of_measure_id==$size->unit_of_measure_id ? 'selected' : '' }} value="{{$size->unit_of_measure_id}}">{{$size->symbol}}</option>
                                @endforeach
                            </select>
                            
                        </div>
                        <div class="form-group ">
                            <label  for="exampleInputEmail1">Unit/Branch :</label>
                                <select name="unit_or_branch"  class="form-control  js-example-basic-single unit_or_branch" required>
                                    <option value="0">--select--</option>
                                    @foreach (unit_branch() as $branch)
                                    <option {{ $data->unit_or_branch==$branch->id ? 'selected' : '' }} value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                    @endforeach
                                </select>
                        </div>
                </fieldset>
            </div>
        </div>
        <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6 "  >
            <div class="card-block  " >
                <div class=" m-t-0 " style="margin-left:-36px;">
                    <div style="margin-left: 36px;">
                        <fieldset class="border p-2">
                            <legend  class="float-none w-auto p-2">Optional Settings</legend>
                                <div class="form-group">
                                    <label for="formGroupExampleInput">Alias :</label>
                                    <input type="text" name="alias" class="form-control"  id="formGroupExampleInput" placeholder="Alias Name"  value="{{ $data->alias }}">
                                    <span id='edit_error_alias' class=" text-danger"></span>
                                </div>
                                <div class="form-group">
                                    <label for="formGroupExampleInput">Rate of Duty (optional) :</label>
                                    <input type="number" name="rateofduty" class="form-control"  id="formGroupExampleInput" placeholder="Rate of Duty " value="{{ $data->rateofduty }}">
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
                <legend  class="float-none w-auto p-2">Opening Balance</legend>
                    <div class="form-group col-sm-3">
                        <label  for="exampleInputEmail1">Godowns :</label>
                            <select name="godown"  class="form-control js-example-basic-single  js-example-basic godown left-data" >
                                @foreach ($godowns as $godown)
                                <option value="{{ $godown->godown_id }}">{{$godown->godown_name }}</option>
                                @endforeach
                            </select>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                          <table class="table " id="orders">
                            <tr>
                              <th class="col-0.5" >#</th>
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
        <div class="col-lg-4 ">
            <div class="form-group">
                <button type="submit"  id="edit_stock_item_btn" class="btn hor-grd btn-grd-primary btn-block submit" style="width:100%" >Update</button>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
            <a class=" btn hor-grd btn-grd-success btn-block " href="{{route('master-dashboard')}}" style="width:100%">Close</a>
            </div>
        </div>
        <div class="col-lg-4">
            @if(user_privileges_check('master','Stock Item','delete_role'))
                <div class="form-group">
                    <button  type="button" class="btn btn-danger deleteIcon "  style="width:100%" data-dismiss="modal">Delete</button>
                </div>
            @endif
        </div>
    @endslot
 @endcomponent
@push('js')
<!-- table hover js -->
<script>
  $(document).ready(function() {
    $('.js-example-basic-single').select2();
  });
  $(function() {
    // add new stock item ajax request
    $("#edit_stock_item_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        let id=$('.id').val();
        $("#edit_stock_item_btn").text('Update');
        $.ajax({
                url:"{{ url('stock-item') }}" + '/' + id,
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data,status,xhr) {
                    claer_error();
                        swal({
                            title: 'Success!',
                            text: data.message,
                            type: 'success',
                            timer: '1500'
                        })
                location.replace('{{ route("stock-item.index") }}')
                $("#edit_stock_item_btn").text('Update ');
                },
                error : function(data,status,xhr){
                    claer_error();
                    if(data.status==400){
                        swal({
                        title: 'Oops...',
                        text: data.responseJSON.message,
                        type: 'error',
                        timer: '1500'
                    });
                    } if(data.status==422){
                        claer_error();
                        $('#edit_error_product_name').text(data.responseJSON.data.product_name?data.responseJSON.data.product_name[0]:'');
                        $('#edit_error_alias').text(data.responseJSON.data.alias?data.responseJSON.data.alias[0]:'');
                    }

                }
        });
    });

});


 // delete stock item ajax request
 $(document).on('click', '.deleteIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id = $('.id').val();
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
            buttonsStyling: false,
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('stock-item') }}" + '/' + id ,
                    type : "POST",
                    data : {'_method' : 'DELETE', '_token' : csrf_token},
                    success: function (data) {

                        swal({
                            title: 'Success!',
                            text: data.message,
                            type: 'success',
                            timer: '1500'
                        })
                        location.replace('{{ route("stock-item.index") }}')
                    },
                    error: function () {
                        swal({
                            title: 'Oops...',
                            text: data.responseJSON.message,
                            type: 'error',
                            timer: '1500'
                        })
                    }
                });
            } else if (
                // Read more about handling dismissals
                result.dismiss === swal.DismissReason.cancel
            ) {
                swal(
                    'Cancelled',
                    'Your data is safe :)',
                    'error'
                )
            }
        })
    });
function claer_error(){
    $('#error_product_name').text('');
}

      $(document).ready(function(){

          addrow ();
          $('#add').click(function() {
            rowCount+=5;
            addrow (rowCount);

          });
          function getId(element){
            var id, idArr;
            id = element.attr('id');
            idArr = id.split("_");
            return idArr[idArr.length - 1];
          }


      function addrow (rowCount)
       {
        if(rowCount==null){
            rowCount=1;
        }else{
            rowCount=rowCount;
        }

        for(var row=1; row<6;row++) {
            rowCount++;
            $('#orders').append('<tr  id="row'+rowCount+'">'+
            '<input class="form-control autocomplete_txt" type="hidden" data-type="product_id" id="product_id_'+rowCount+'"  for="'+rowCount+'"/>'+
            '<td class="m-0 p-0"><button type="button" name="remove" id="'+rowCount+'" class="btn btn-danger btn_remove cicle m-1  py-1">-</button></td>'+
            '<td class="m-0 p-0"><input class="form-control product_name  autocomplete_txt"  type="text" data-type="product_name" id="product_name_'+rowCount+'" " autocomplete="off" for="'+rowCount+'"/></td>'+
            '<td class="m-0 p-0"><input class="form-control qty text-right "   data-field-name="qty" type="number" class="qty" id="qty_'+rowCount+'"  for="'+rowCount+'"/> </td>'+
            '<td class="m-0 p-0"><input class="form-control rate text-right" data-field-name="rate" type="number" data-type="rate" id="rate_'+rowCount+'"  for="'+rowCount+'"/></td>'+
            '<td class="m-0 p-0"><input class="form-control total_cost text-right" type="text" id="total_cost_'+rowCount+'"   for="'+rowCount+'" readonly/> </td>'+
            '</tr>');
            }
        }
});

</script>





@endpush
@endsection
