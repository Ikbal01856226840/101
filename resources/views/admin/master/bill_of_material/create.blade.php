
@extends('layouts.backend.app')
@section('title','Bill Of Material')
@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/jquery-ui.theme.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/jquery-ui.min.css')}}">
<!-- model style -->
<style>
.form-control {
    font-size: 15px;
    border-radius: 3px;
    border: 1px solid #ccc;
    }
    textarea.form-control {
    min-height: calc(1.5em + 0.75rem + 42px);
   }
   .customers th {
    background-color: #1AB0C3;
    color: white;
   }
    .table > :not(:first-child) {
        border-top: 0px solid currentColor;
    }
    legend {
        float: initial;
    }
</style>
@endpush
@section('admin_content')
<div class="pcoded-content  " style="background-color: #e5e5cd!important">
    <div class="pcoded-inner-content  " >
      <br>
        <!-- Main-body start -->
        <div class="main-body ">
          <div class="page-wrapper m-t-0 m-l-1  p-10">
            <!-- Page-header start -->
            <div class="page-header m-2 p-0">
              <div class="row align-items-end" style="margin-bottom: 0%px !important;" >
                <div class="col-lg-8 ">
                  <div class="page-header-title m p-0" style="margin-bottom:7px !important;">
                    <div class="d-inline ">
                      <h4 style="color: green;font-weight: bold;"> [Bill Of Material Create]</h4>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 " >
                    <div style="float: right; margin-left: 5px;"  >
                        <a  style=" float:right; text-decoration: none; " href="{{route('master-dashboard')}}"><span class="fa fa-info-circle m-1" style="font-size:27px; color:#00b8e6;"></span><span style="float:right;margin:2px; padding-top:5px; color: color: white;#">Help</span></a>
                    </div>
                    <div style="float: right;margin-left:9px" >
                      <a  style="float:right; text-decoration: none; " href="{{route('master-dashboard')}}"><span class="fa fa-times-circle-o m-1" style="font-size:27px; color:#ff6666;"></span><span style="float:right;margin:2px; padding-top:5px; ">Close</span></a>
                   </div>
                   <div style="float: right; margin-left:9px">
                      <a  style=" float: right; text-decoration: none; " href="{{route('bill-of-material.index')}}"><span class="fa fa-eye m-1" style="font-size:27px; color:#00b8e6;"></span><span style="float:right;margin:2px; padding-top:5px; ">View</span></a>
                  </div>
                </div>
                    <hr style="margin-bottom: 0px;">
              </div>
            </div>
            <form id="bill_of_material_id"  method="POST">
                    @csrf
                    <div class="page-body">
                        <div class="row">
                            <div class="col-sm-6" >
                                <label>Bill of Material :</label>
                                <input class="form-control " type="text" name="bom_name" >
                                <span id='error_bill_of_material' class="text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <fieldset style="border:1px rgb(32, 30, 30) solid;">
                        <legend style="width: 100%;  font-size: 18px;">Raw Materials / Source / Consumption / Stock Out</legend>
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table customers" style=" border: none !important; margin-top:5px;" >
                                    <thead>
                                        <tr>
                                            <th class="col-0.5" >#</th>
                                            <th class="col-6">Product Name</th>
                                            <th class="col-3">Unit</th>
                                            <th class="col-6">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody id="orders_out">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="m-0 p-0"><button type="button"  id="add_out" class="btn btn-success  cicle m-0  py-1">+</button></td>
                                            <td></td>
                                            <td colspan="1" class="text-right">Total: </td>
                                            <td><input type="text " style="border-radius: 15px;" class="total_qty_out form-control text-right" readonly></td>
                                        </tr>
                                    </tfoot>
                                    </table>
                                </div>
                            </div>
                    </fieldset>
                    <fieldset style="border:1px rgb(32, 30, 30) solid;">
                        <legend style="width: 100%;font-size: 18px;">Finished Goods / Destination / Production / Stock In</legend>
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table customers" style=" border: none !important; margin-top:5px;" >
                                    <thead>
                                        <tr >
                                            <th class="col-0.5" >#</th>
                                            <th class="col-6">Product Name</th>
                                            <th class="col-3">Unit</th>
                                            <th class="col-6">Quantity</th>

                                        </tr>
                                    </thead>
                                    <tbody id="orders_in">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="m-0 p-0"><button type="button"  id="add" class="btn btn-success  cicle m-0  py-1">+</button></td>
                                            <td></td>
                                            <td colspan="1" class="text-right">Total: </td>
                                            <td><input type="text " style="border-radius: 15px;" class="total_qty_in form-control text-right" readonly></td>
                                        </tr>
                                    </tfoot>
                                    </table>
                                </div>
                            </div>
                    </fieldset>
                    <div align="center" class="m-1">
                        <button  type="submit" class="btn btn-info bill_of_material_btn" style="width:116px;border-radius: 15px;"><span class="m-1 m-t-1" style="color:#404040"><i class="fa fa-save" style="font-size:18px;" ></i></span><span  >Add</span></button>
                        <a href="{{route('master-dashboard')}}"  class="btn btn-danger " style="border-radius: 15px;" ><span class="m-1 m-t-1" style="color:#404040;!important"><i class="fa fa-times-circle" style="font-size:20px;" ></i></span><span>Cancel</span></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@push('js')
<script type="text/javascript" src="{{asset('libraries/js/jquery-ui.min.js')}}"></script>

<script>
$(document).ready(function(){
  var amount_decimals="{{company()->amount_decimals}}";
  var rowCount_in=1,rowCount_out=1;
    //source
    addrow_in();
    $('#add').click(function() {
      rowCount_in+=5;
      addrow_in (rowCount_in);
    });

    //destination
    addrow_out();
    $('#add_out').click(function() {
      rowCount_out+=5;
      addrow_out (rowCount_out);
    });

    function getId(element){
      var id, idArr;
      id = element.attr('id');
      idArr = id.split("_");
      return idArr[idArr.length - 1];
    }
   //source  remove
    $(document).on('click', '.btn_remove_in', function() {
      var button_id = $(this).attr('id');
      $('#row_in'+button_id+'').remove();
    });
    //destination remove
    $(document).on('click', '.btn_remove_out', function() {
      var button_id = $(this).attr('id');
      $('#row_out'+button_id+'').remove();
    });

// add row source
function addrow_in (rowCount){
      if(rowCount==null){
          rowCount=1;
      }else{
          rowCount=rowCount;
      }
      for(var row=1; row<6;row++) {
          rowCount++;
              $('#orders_in').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row_in${rowCount}">
                  <input class="form-control  product_in_id m-0 p-0"  name="product_in_id[]" type="hidden" data-type="product_in_id" id="product_in_id_${rowCount}"  for="${rowCount}"/>
                  <td  class="m-0 p-0"><button  type="button" name="remove" id="${rowCount}" class="btn btn-danger btn_remove_in cicle m-0  py-1">-</button></td>
                  <td  class="m-0 p-0">
                    <input class="form-control  autocomplete_txt" name="product_in_name[]" data-field-name="product_name"  type="text" data-type="product_in_name" id="product_in_name_${rowCount}"  autocomplete="off" for="${rowCount}"  />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control  unit_in" name="unit_in[]"  data-field-name="unit_in" type="text"  id="unit_in_${rowCount}"  for="${rowCount}"  />
                  </td>
                  <td  class="m-0 p-0">
                      <input class="form-control qty_in text-right " name="qty_in[]"  data-field-name="qty_in" type="number" class="qty_in" id="qty_in_${rowCount}"  for="${rowCount}"  />
                  </td>

            </tr>`);
      }
  }
  //add row destination
  function addrow_out (rowCount){

      if(rowCount==null){
          rowCount=1;
      }else{
          rowCount=rowCount;
      }
      let godown_id=$('.godown_out').val();
      let godown_name=$('.godown_out option:selected').text()
      for(var row=1; row<6;row++) {
          rowCount++;
          $('#orders_out').append(`<tr  style="margin:0px;padding:0px;" class="p-0 m-0"  id="row_out${rowCount}">
                <input class="form-control  product_out_id m-0 p-0"  name="product_out_id[]" type="hidden" data-type="product_out_id" id="product_out_id_${rowCount}"  for="${rowCount}"/>
                <td  class="m-0 p-0"><button  type="button" name="btn_remove_out" id="${rowCount}" class="btn btn-danger btn_remove_out cicle m-0  py-1">-</button></td>
                <td  class="m-0 p-0">
                  <input class="form-control  autocomplete_txt" name="product_out_name[]" data-field-name="product_name"  type="text" data-type="product_out_name" id="product_out_name_${rowCount}"  autocomplete="off" for="${rowCount}"  />
                </td>
                <td  class="m-0 p-0">
                      <input class="form-control  out_unit" name="out_unit[]"  data-field-name="out_unit" type="text"  id="out_unit_${rowCount}"  for="${rowCount}"  />
                  </td>
                <td  class="m-0 p-0">
                    <input class="form-control qty_out text-right " name="qty_out[]"  data-field-name="qty_out" type="number" class="qty_out" id="qty_out_${rowCount}"  for="${rowCount}"  />
                </td>
          </tr>`);
      }
  }

// source calculation
  function button_total_cal_in(){
      let qty_in=0;
      $('#orders_in tr').each(function(i){
          if(parseFloat($(this).find('.qty_in').val())) qty_in+=parseFloat($(this).find('.qty_in').val());
      })
      $('.total_qty_in').val(parseFloat(qty_in).toFixed(amount_decimals));
  }
  $('#orders_in').on('keyup click','.qty_in',function(){
       let qty=$(this).closest('tr').find('.qty_in').val();
       button_total_cal_in();
  });

  // destiontion calculation
  function button_total_cal_out(){
      let qty_out=0;
      let amount_out=0;
      $('#orders_out tr').each(function(i){
          if(parseFloat($(this).find('.qty_out').val())) qty_out+=parseFloat($(this).find('.qty_out').val());
      })
      $('.total_qty_out').val(parseFloat(qty_out).toFixed(amount_decimals));
  }

  $('#orders_out').on('keyup click','.qty_out',function(){
       let qty=$(this).closest('tr').find('.qty_out').val();
       button_total_cal_out();
  });
  $(document).on('click', '.btn_remove_in,.btn_remove_out', function() {
    button_total_cal_out();
    button_total_cal_in();
 });

  function getId(element){
      var id, idArr;
      id = element.attr('id');
      idArr = id.split("_");
      return idArr[idArr.length - 1];
    }

    var item_check =[];
    var item_check_out =[];

  function handleAutocomplete() {
      var fieldName, currentEle
      currentEle = $(this);
      fieldName = currentEle.data('field-name');
      if(typeof fieldName === 'undefined') {
          return false;
      }
      currentEle.autocomplete({
          delay: 500,
          source: function( data, cb ) {
              $.ajax({
                  url: '{{route("searching-item-data") }}',
                  method: 'GET',
                  dataType: 'json',
                  data: {
                      name:  data.term,
                      fieldName: fieldName,

                  },
                  success: function(res){
                      var result;
                      result = [
                          {
                              label: 'There is no matching record found for '+data.term,
                              value: ''
                          }
                      ];
                      if (res.length) {
                          result=$.map(res, function(obj){

                              return {
                                  label: obj[fieldName],
                                  value: obj[fieldName],
                                  data : obj
                              };
                          });
                      }
                      cb(result);
                  }
              });
          },
          autoFocus: true,
          minLength: 1,

          select: function( event, selectedData ) {
              if(selectedData && selectedData.item && selectedData.item.data){
                  var rowNo, data;
                  rowNo = getId(currentEle);
                  data = selectedData.item.data;
                    if(currentEle.data('type')=='product_in_name'){
                        $('#product_in_id_'+rowNo).val(data.stock_item_id);
                        $('#unit_in_'+rowNo).val(data.symbol);
                    }else{
                        $('#product_out_id_'+rowNo).val(data.stock_item_id);
                        $('#out_unit_'+rowNo).val(data.symbol);
                    }

              }
          }
      });
  }
function registerEvents() {
$(document).on('focus','.autocomplete_txt', handleAutocomplete);
  }
registerEvents();
});
</script>
<script>
$(document).ready(function(){
$("#bill_of_material_id").submit(function(e) {
  e.preventDefault();
  const fd = new FormData(this);
  $("#bill_of_material_btn").text('Add');
  $.ajax({
          url: '{{ route("bill-of-material.store") }}',
          method: 'post',
          data: fd,
          cache: false,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function(data,status,xhr) {
              swal_message(data.message,'success','Successfully');
              location.reload();
              $('#error_bill_of_material').text('');
          },
          error : function(data,status,xhr){
              if(data.status==404){
                  swal_message(data.message,'error','Error');
              } if(data.status==422){
                $('#error_bill_of_material').text(data.responseJSON.data.bom_name[0]);

              }
          }
  });
});
    function swal_message(data,message,m_title){
    swal({
        title:m_title,
        text: data,
        type: message,
        timer: '1500'
    });
    }
});

</script>

@endpush
@endsection

