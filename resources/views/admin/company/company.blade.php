@extends('layouts.backend.app')
@section('title','Company')
@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    input[type=radio] {
      width: 20px;
      height: 20px;
  }
  </style>
@endpush
@section('admin_content')
<br>
<!-- company edit   -->
@component('components.create', [
    'title'    => 'Update Company',
    'form_id' => 'add_company_form',
    'help_route'=>route('showCompany'),
    'close_route'=>route('showCompany'),
    'method'=> 'PATCH',

])
    @slot('body')
      <div class="row">
        <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6" >
            <div class="card-block ">
                <fieldset class="border p-2">
                    <legend  class="float-none w-auto p-2">General Fields</legend>
                        <div class="form-group">
                            <label for="formGroupExampleInput">Company Name :</label>
                            <input type="hidden" name="id" class="form-control id" value="{{$data->company_id }}" >
                            <input type="text" name="company_name" class="form-control company_name"  id="formGroupExampleInput" value="{{$data->company_name }}" placeholder="Company Name"  >
                        </div>
                        <div class="form-group ">
                            <label for="formGroupExampleInput2">Unit/Branch :</label>
                            <select name="branch_id"  class="form-control  js-example-basic-single   left-data" >
                                <option value="0">--Select--</option>
                                @foreach ($branchs as $branch )
                                  <option {{$branch->id==$data->branch_id ? 'selected' : '' }}  value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput">Address :</label>
                            <input type="text" name="mailing_address" class="form-control mailing_address"  id="formGroupExampleInput" value="{{$data->mailing_address }}" placeholder="Company Address " >
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput">Country :</label>
                            <input type="text" name="country" class="form-control country"  id="formGroupExampleInput" value="{{$data->country }}" placeholder="Company Country " >
                        </div>
                        <div class="form-group ">
                            <label for="formGroupExampleInput2">Category of Software :</label>
                            <select name="category"  class="form-control  js-example-basic-single   left-data" >
                                <option {{ $data->category==1 ? 'selected' : '' }} value="1">Accounting & Inventory</option>
                                <option {{ $data->category==2 ? 'selected' : '' }} value="2">Manufacturing</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput">Financial Year Start : :</label>
                            <input type="date" name="financial_year_start" class="form-control financial_year_start" value="{{$data->financial_year_start}}" id="formGroupExampleInput">
                            <span id='error_gvoucher_name' class=" text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput">Financial Year End : :</label>
                            <input type="date" name="financial_year_end" class="form-control financial_year_end" value="{{$data->financial_year_end}}"  id="formGroupExampleInput">
                            <span id='error_gvoucher_name' class=" text-danger"></span>
                        </div>
                    </fieldset>
                    <fieldset class="border p-2">
                        <legend  class="float-none w-auto p-2">Default Setup</legend>
                            <div class="form-group " style="color: #000!important;background-color: #ddffff!important;">
                                <label class="m-2">Stock Value Type : FIFO</label>
                            </div>
                            <div class="form-group " style="color: #000!important;background-color: #ddffff!important;">
                                <label class="m-2">Closing Value Calculation Method :</label>
                                <div class="row">
                                    <div class="col-xl-1 col-md-1 col-sm-1 col-xs-1">
                                        <i class="fa fa-close m-2" style="font-size:24px;"></i>
                                    </div>
                                    <div class="col-xl-11 col-md-11 col-sm-11 col-xs-11">
                                        <label class="m-2">change rate while stock out.</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-1 col-md-1 col-sm-1 col-xs-1">
                                        <i class="fa fa-check m-2" aria-hidden="true" style="font-size:24px;"></i>

                                    </div>
                                    <div class="col-xl-11 col-md-11 col-sm-11 col-xs-11">
                                        <label class="m-2">do not change rate while stock out.</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-2" style="color: #000!important;background-color: #ddffff!important;">
                                <label for="formGroupExampleInput2">Opening Stock Item Customer Field: </label>
                                <select name="opening_stock_item_customer_is"  class="form-control  js-example-basic-single   left-data" >
                                    <option {{$data->opening_stock_item_customer_is==1 ? 'selected' : '' }} value="1">Show</option>
                                    <option {{ $data->opening_stock_item_customer_is==2 ? 'selected' : '' }} value="2">Hide</option>

                                </select>
                            </div>
                            <div class="form-group m-2" style="color: #000!important;background-color: #ddffff!important;">
                                <label for="formGroupExampleInput2">Quantity styling -> How many decimals : </label>
                                <select name="quantity_decimals"  class="form-control  js-example-basic-single   left-data" >
                                    <option {{ $data->quantity_decimals==1 ? 'selected' : '' }} value="1">ONE</option>
                                    <option {{ $data->quantity_decimals==2 ? 'selected' : '' }} value="2">TWO</option>
                                    <option {{ $data->quantity_decimals==3 ? 'selected' : '' }} value="3">THREE</option>
                                </select>
                            </div>
                            <div class="form-group m-2" style="color: #000!important;background-color: #ddffff!important;">
                                <label for="formGroupExampleInput2">Amount styling -> How many decimals : </label>
                                <select name="amount_decimals"  class="form-control  js-example-basic-single   left-data" >
                                    <option  {{ $data->amount_decimals==0 ? 'selected' : '' }} value="0">NONE</option>
                                    <option  {{ $data->amount_decimals==1 ? 'selected' : '' }} value="1">ONE</option>
                                    <option {{ $data->amount_decimals==2 ? 'selected' : '' }} value="2">TWO</option>
                                    <option {{ $data->amount_decimals==3 ? 'selected' : '' }} value="3">THREE</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-check-label fs-5" for="flexRadioDefault1" >
                                    Amount styling -> in Word Method :
                                </label>
                                <input class="form-check-input m-1" type="radio"  name="amount_in_word" value="1"  {{ $data->amount_in_word==1 ? 'checked' : '' }}  >
                                <label class="form-check-label fs-5" for="flexRadioDefault1" >
                                    in Million
                                </label>
                                <input class="form-check-input  m-1" type="radio"  name="amount_in_word" value="2" {{ $data->amount_in_word==2 ? 'checked' : '' }} >
                                <label class="form-check-label fs-5" for="flexRadioDefault1">
                                    in Lakh
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="form-check-label fs-5" for="flexRadioDefault1" >
                                    Customer
                                </label>
                                <input class="form-check-input m-1" type="radio"  name="customer_id" value="1"  {{ $data->customer_id==1 ? 'checked' : '' }}  >
                                <label class="form-check-label fs-5" for="flexRadioDefault1" >
                                    Show
                                </label>
                                <input class="form-check-input  m-1" type="radio"  name="customer_id" value="0" {{ $data->customer_id==0 ? 'checked' : '' }} >
                                <label class="form-check-label fs-5" for="flexRadioDefault1">
                                   Hidden
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="form-check-label fs-5" for="flexRadioDefault1" >
                                    Gate Pass Ref
                                </label>
                                <input class="form-check-input m-1" type="radio"  name="gate_pass_ref" value="1"  {{ $data->gate_pass_ref==1 ? 'checked' : '' }}  >
                                <label class="form-check-label fs-5" for="flexRadioDefault1" >
                                    Show
                                </label>
                                <input class="form-check-input  m-1" type="radio"  name="gate_pass_ref" value="0" {{ $data->gate_pass_ref==0 ? 'checked' : '' }} >
                                <label class="form-check-label fs-5" for="flexRadioDefault1">
                                   Hidden
                                </label>
                            </div>
                        </fieldset>
            </div>
        </div>
    @endslot
    @slot('footer')
        <div class="col-lg-6 ">
            <div class="form-group">
                <button type="submit"  id="add_company_btn" class="btn hor-grd btn-grd-primary btn-block submit" style="width:100%" ><u>S</u>ave</button>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group">
            <a class=" btn hor-grd btn-grd-success btn-block " href="{{route('showCompany')}}" style="width:100%"><u>B</u>ack</a>
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
    // edit Company ajax request
    $("#add_company_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        let id=$('.id').val();
        $("#add_company_btn").text('Save');
        $.ajax({
                url:"{{url('company')}}" + '/' + id,
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data,status,xhr) {
                        swal({
                            title: 'Success!',
                            text: data.message,
                            type: 'success',
                            timer: '1500'
                        })
                        location.reload();
                    $("#add_company_btn").text('Edit Company');

                },
                error : function(data,status,xhr){
                    if(data.status==404){
                        swal({
                        title: 'Oops...',
                        text: data.message,
                        type: 'error',
                        timer: '1500'
                    });
                    } if(data.status==422){

                    }

                }
        });
    });
</script>
@endpush
@endsection

