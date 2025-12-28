@extends('layouts.backend.app')
@section('title','Dashboard User')
@push('css')
@endpush
@section('admin_content')
<br>
@component('components.create', [
    'title'    => 'User [Edit]',
    'help_route'=>route('user.index'),
    'close_route'=>route('user.index'),
    'veiw_route'=>route('user.index'),
    'form_id' => 'edit_user_form',
    'method'=> 'PUT',
])
    @slot('body')
    <div class="row">
        <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6" >
            <div class="card-block ">
                <h6 class="m-t-0">Add User</h6>
                <hr>
                <div class="form-group my-2 row">
                    <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">User Id :</label>
                    <div class="col-xl-9 col-md-10 col-sm-10 col-xs-10">
                        <input type="text" name="log_in_id" class="form-control " placeholder="Enter User Name" required value="{{$get_user_data->log_in_id}}">
                        <input type="hidden"  class="form-control id"  required value="{{$get_user_data->id}}">
                        <span id='error_user_name' class=" text-danger"></span>
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">Password :</label>
                    <div class="col-xl-9 col-md-10 col-sm-10 col-xs-10">
                        <input type="password" name="password" class="form-control" placeholder="Enter Password" >
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">User Level :</label>
                    <div class="col-xl-9 col-md-10 col-sm-10">
                        <select name="user_level" id="" class="form-control status js-example-basic-single" required>
                            <option  value="1">1</option>
                            <option {{$get_user_data->user_level==2 ? 'selected' : ''}} value="2">2</option>
                            <option {{$get_user_data->user_level==3 ? 'selected' : ''}} value="3">3</option>
                            <option {{$get_user_data->user_level==4 ? 'selected' : ''}} value="4">4</option>
                            <option {{$get_user_data->user_level==5 ? 'selected' : ''}} value="5">5</option>
                        </select>
                    </div>
                </div>
                <div class="form-group my-2 row dealer">
                </div>
                <div class="form-group my-2 row">
                    <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">Lock this User : ?</label>
                    <div class="col-xl-9 col-md-10 col-sm-10 col-xs-2">
                        <select name="locked" id="" class="form-control status js-example-basic-single" required>
                            <option value="1">Yes</option>
                            <option {{$get_user_data->locked==0 ? 'selected' : ''}} value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-md-3 col-form-label">Active this User : ?</label>
                    <div class="col-md-9">
                        <select name="activity" id="" class="form-control status js-example-basic-single" required>
                            <option value="1">Yes</option>
                            <option  {{$get_user_data->activity==0 ? 'selected' : ''}} value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-md-3 col-form-label">Active Time Start : </label>
                    <div class="col-md-9">
                        <input type="date" name="active_time_start" class="form-control" value="{{$get_user_data->active_time_start	}}" >
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-md-3 col-form-label">Active Time End :</label>
                    <div class="col-md-9">
                        <input type="date" name="active_time_end" class="form-control" value="{{$get_user_data->active_time_end}}" >
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-md-3 col-form-label">Unit/Branch : </label>
                    <div class="col-md-9">
                        <select name="unit_or_branch" id="" class="form-control status js-example-basic-single" required>
                            <option value="0">--select--</option>
                            @foreach ($branchs as $branch)
                             <option {{$get_user_data->unit_or_branch==$branch->id ? 'selected' : ''}} value="{{$branch->id}}">{{$branch->branch_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="  row" style="margin-top: 50px;">
                    <label class="col-md-12 col-form-label">Godown Access Range : </label>

                </div>
               @php
                   $godown_array=explode(',', $get_user_data->godown_id);

               @endphp
                <div class=" my-2  row">
                    <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Godown 1 :</label>
                    <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">{{$get_user_data->godown_id_array}}
                        <select name="godown_id_array[]" id="" class="form-control status js-example-basic-single" required>
                            <option value="0">--select one--</option>
                            @foreach ($godowns as $godown)
                               <option value="{{$godown->godown_id}}" {{$godown->godown_id==$godown_array[0]?'selected' : ''}}>{{$godown->godown_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Godown 2 :</label>
                    <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                        <select name="godown_id_array[]" id="" class="form-control status js-example-basic-single" required>
                            <option value="0">--select one--</option>
                            @foreach ($godowns as $godown)
                               <option value="{{$godown->godown_id}}" {{$godown->godown_id==$godown_array[1]?'selected' : ''}}>{{$godown->godown_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Godown 3 :</label>
                    <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                        <select name="godown_id_array[]" id="" class="form-control status js-example-basic-single" required>
                            <option value="0">--select one--</option>
                            @foreach ($godowns as $godown)
                             <option value="{{$godown->godown_id}}" {{$godown->godown_id==$godown_array[2]?'selected' : ''}} >{{$godown->godown_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Godown 4 :</label>
                    <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                        <select name="godown_id_array[]" id="" class="form-control status js-example-basic-single" required>
                            <option value="0">--select one--</option>
                            @foreach ($godowns as $godown)
                               <option value="{{$godown->godown_id}}" {{$godown->godown_id==$godown_array[3]?'selected' : ''}} >{{$godown->godown_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Godown 5 :</label>
                    <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                        <select name="godown_id_array[]" id="" class="form-control status js-example-basic-single" required>
                            <option value="0">--select one--</option>
                            @foreach ($godowns as $godown)
                               <option value="{{$godown->godown_id}}" {{$godown->godown_id==$godown_array[4]?'selected' : ''}} >{{$godown->godown_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6 "  >
            <div class="card-block  " >
                <h6 class="">Basic Inputs</h6>
                <hr>
                <div class=" b-l-default m-t-0 " style="margin-left:-36px;">
                    <div style="margin-left: 36px;">
                        <div class="form-group my-2 row">
                            <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">Full Name :</label>
                            <div class="col-xl-9 col-md-10 col-sm-10">
                                <input type="text" name="user_name" class="form-control" placeholder="Enter Full Name" value="{{$get_user_data->user_name}}"  required>
                            </div>
                        </div>
                        <div class="form-group my-2 row">
                            <label class="col-xl-3 col-md-2 col-sm-2 col-form-label">e-mail :</label>
                            <div class="col-xl-9 col-md-10 col-sm-10">
                                <input type="email" name="email" class="form-control" placeholder="Enter Email Address" value="{{$get_user_data->email}}">
                            </div>
                        </div>
                        <div class="form-group my-2 row">
                            <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">Phone (work) :</label>
                            <div class="col-xl-9 col-md-10 col-sm-10">
                                <input type="text" name="phone" class="form-control"   value="{{$get_user_data->phone}}">
                            </div>
                        </div>
                        <div class="form-group my-2 row">
                            <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">Phone (own) : </label>

                            <div class="col-xl-9 col-md-10 col-sm-10">
                                <input type="text" name="phone_2" class="form-control" value="{{$get_user_data->phone_2}}" >
                            </div>
                        </div>
                        <div class="form-group my-2 row">
                            <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">  Phone (other) : </label>

                            <div class="col-xl-9 col-md-10 col-sm-10">
                                <input type="text" name="phone_3" class="form-control" value="{{$get_user_data->phone_3}}">
                            </div>
                        </div>
                        <div class="form-group my-2 row">
                            <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">Address :</label>
                            <div class="col-xl-9 col-md-10 col-sm-10">
                                <input type="text" name="address" class="form-control" placeholder="Enter User Address" alue="{{$get_user_data->address}}">
                            </div>
                        </div>
                        <div class=" row" style="margin-top: 40px;">
                            <div class="col-md-12">
                                <label class=" col-form-label">Distribution Center / Shop Setup:</label>
                                <select name="dis_cen_id[]" class="form-control status js-example-basic-multiple" multiple="multiple">
                                    <option value="0">--select one --</option>
                                    @foreach ($distributions as $distribution)
                                        <option value="{{ $distribution->dis_cen_id }}" 
                                            @if(in_array($distribution->dis_cen_id, explode(',', $get_user_data->dis_cen_id))) selected @endif>
                                            {{ $distribution->dis_cen_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="  row" style="margin-top: 30px;">
                                <label class="col-md-12 col-form-label">Accounts Group Access Range :</label>

                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 1 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="group_id_array[]"  class="form-control status js-example-basic-single group_id_1" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($groupCharts)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group  2 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="group_id_array[]"  class="form-control status js-example-basic-single group_id_2" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($groupCharts)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 3 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="group_id_array[]"  class="form-control status js-example-basic-single group_id_3" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($groupCharts)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 4 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="group_id_array[]"  class="form-control status js-example-basic-single group_id_4" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($groupCharts)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 5 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="group_id_array[]"  class="form-control status js-example-basic-single group_id_5" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($groupCharts)!!}
                                    </select>
                                </div>
                            </div>
                        </div>
                          <div class="  row" style="margin-top: 30px;">
                                <label class="col-md-12 col-form-label">Stock Group Access Range :</label>

                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 1 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="stock_group_id_multiple[]" id="" class="form-control status js-example-basic-single stock_group_id_multiple_1" >
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($stockGroup)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group  2 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="stock_group_id_multiple[]" id="" class="form-control status js-example-basic-single stock_group_id_multiple_2" >
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($stockGroup)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 3 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="stock_group_id_multiple[]" id="" class="form-control status js-example-basic-single stock_group_id_multiple_3" >
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($stockGroup)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 4 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="stock_group_id_multiple[]" id="" class="form-control status js-example-basic-single stock_group_id_multiple_4" >
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($stockGroup)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 5 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="stock_group_id_multiple[]" id="" class="form-control status js-example-basic-single stock_group_id_multiple_5" >
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($stockGroup)!!}
                                    </select>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endslot
    @slot('footer')
        <div class="col-lg-4 ">
            <div class="form-group">
                <button type="submit"  id="edit_user_btn" class="btn hor-grd btn-grd-primary btn-block submit" style="width:100%" ><u>S</u>ave</button>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
               <a class=" btn hor-grd btn-grd-success btn-block " href="<?php echo route('dashboard') ?>" style="width:100%"><u>B</u>ack</a>
            </div>
        </div>
        <div class="col-lg-4">
            @if(user_privileges_check('master','User','delete_role'))
                <div class="form-group">
                    <button  type="button" class=" btn hor-grd btn-grd-danger deleteIcon "  data-dismiss="modal" style="width:100%">Delete</button>
                </div>
            @endif
        </div>
    @endslot
 @endcomponent
@push('js')
<script>
    $(document).ready(function () {
        let agar='{{$get_user_data->agar}}';
        let agar_arr=agar.split(",");
        $('.group_id_1').val(agar_arr[0]).trigger('change');
        $('.group_id_2').val(agar_arr[1]).trigger('change');
        $('.group_id_3').val(agar_arr[2]).trigger('change');
        $('.group_id_4').val(agar_arr[3]).trigger('change');
        $('.group_id_5').val(agar_arr[4]).trigger('change');
         let stock_group_id_multiple='{{$get_user_data->stock_group_id_multiple}}';
        let stock_group_id_multiple_arr=stock_group_id_multiple.split(",");
        console.log(stock_group_id_multiple_arr)
        $('.stock_group_id_multiple_1').val(stock_group_id_multiple_arr[0]||0).trigger('change');
        $('.stock_group_id_multiple_2').val(stock_group_id_multiple_arr[1]||0).trigger('change');
        $('.stock_group_id_multiple_3').val(stock_group_id_multiple_arr[2]||0).trigger('change');
        $('.stock_group_id_multiple_4').val(stock_group_id_multiple_arr[3]||0).trigger('change');
        $('.stock_group_id_multiple_5').val(stock_group_id_multiple_arr[4]||0).trigger('change');
     })
 $(function() {
     // edit new user ajax request
     $("#edit_user_form").submit(function(e) {
         e.preventDefault();
         const fd = new FormData(this);
         var id = $('.id').val();
         $("#edit_user_btn").text('Adding...');
         $.ajax({
                 url: "{{ url('user') }}" + '/' + id ,
                 method: 'post',
                 data: fd,
                 cache: false,
                 contentType: false,
                 processData: false,
                 dataType: 'json',
                 success: function(data) {
                     claer_error();
                         swal({
                             title: 'Success!',
                             text: data.message,
                             type: 'success',
                             timer: '1500'
                         })
                         location.replace('{{ route("user-list-show") }}')
                     $("#edit_user_btn").text('Add User');

                 },
                 error : function(data,status,xhr){
                     claer_error();
                     if(data.status==400){
                         swal({
                         title: 'Oops...',
                         text: data.message,
                         type: 'error',
                         timer: '1500'
                     });
                     } if(data.status==422){
                         claer_error();
                       $('#error_user_name').text(data.responseJSON.data.voucher_name[0]);
                     }

                 }
         });
     });

 });
 // delete  ajax request
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
                     url: "{{ url('user') }}" + '/' + id ,
                     type : "POST",
                     data : {'_method' : 'DELETE', '_token' : csrf_token},
                     success: function (data) {
                         swal_message(data.message,'success');
                         location.replace('{{ route("user-list-show") }}');
                     },
                     error: function () {
                         swal_message(data.message,'error');
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
     function swal_message(data,message){
         swal({
         title: 'Success',
         text: data,
         type: message,
         timer: '1500'
         });
     }

 //data validation data clear
 function claer_error(){
     $('#error_user_name').text('');
 }
 </script>

@endpush
@endsection
