@extends('layouts.backend.app')
@section('title','Dashboard User')
@push('css')
@endpush
@section('admin_content')
<br>
@component('components.create', [
    'title'    => 'User [New]',
    'help_route'=>route('user-dashboard'),
    'close_route'=>route('user-dashboard'),
    'veiw_route'=>route('user-list-show'),
    'form_id' => 'add_user_form',
    'method'=> 'POST',

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
                        <input type="text" name="log_in_id" class="form-control " placeholder="Enter User Name" required>
                        <span id="error_user_name" class="text-danger"></span>
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">Password :</label>
                    <div class="col-xl-9 col-md-10 col-sm-10 col-xs-10">
                        <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
                        <span id="error_password" class="text-danger"></span>
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">User Level :</label>
                    <div class="col-xl-9 col-md-10 col-sm-10">
                        <select name="user_level" id="" class="form-control status js-example-basic-single" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
                <div class="form-group my-2 row dealer">
                </div>
                <div class="form-group my-2 row">
                    <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">Lock this User : ?</label>
                    <div class="col-xl-9 col-md-10 col-sm-10 col-xs-2">
                        <select name="locked" id="" class="form-control status js-example-basic-single" required>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                            
                        </select>
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-md-3 col-form-label">Active this User : ?</label>
                    <div class="col-md-9">
                        <select name="activity" id="" class="form-control status js-example-basic-single" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-md-3 col-form-label">Active Time Start : </label>
                    <div class="col-md-9">
                        <input type="date" name="active_time_start" class="form-control" value="<?php  echo date('Y-m-d'); ?>" >
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-md-3 col-form-label">Active Time End : </label>
                    <div class="col-md-9">
                        <input type="date" name="active_time_end" class="form-control" value="<?php  echo date('Y-m-d'); ?>" >
                    </div>
                </div>
                <div class="form-group my-2 row">
                    <label class="col-md-3 col-form-label">Unit/Branch : </label>
                    <div class="col-md-9">
                        <select name="unit_or_branch" id="" class="form-control status js-example-basic-single" required>
                            <option value="0">--select--</option>
                            @foreach ($branchs as $branch)
                            <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="  row" style="margin-top: 50px;">
                    <label class="col-md-12 col-form-label">Godown Access Range : </label>

                </div>
                <div class=" my-2  row">
                    <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Godown 1 :</label>
                    <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                        <select name="godown_id_array[]" id="" class="form-control status js-example-basic-single" required>
                            <option value="0">--select one--</option>
                            @foreach ($godowns as $godown)
                               <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
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
                               <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
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
                             <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
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
                               <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
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
                               <option value="{{$godown->godown_id}}">{{$godown->godown_name}}</option>
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
                                <input type="text" name="user_name" class="form-control" placeholder="Enter Full Name" required>
                            </div>
                        </div>
                        <div class="form-group my-2 row">
                            <label class="col-xl-3 col-md-2 col-sm-2 col-form-label">e-mail :</label>
                            <div class="col-xl-9 col-md-10 col-sm-10">
                                <input type="email" name="email" class="form-control" placeholder="Enter Email Address">
                            </div>
                        </div>
                        <div class="form-group my-2 row">
                            <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">Phone (work) :</label>
                            <div class="col-xl-9 col-md-10 col-sm-10">
                                <input type="text" name="phone" class="form-control"  required>
                            </div>
                        </div>
                        <div class="form-group my-2 row">
                            <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">Phone (own) : </label>

                            <div class="col-xl-9 col-md-10 col-sm-10">
                                <input type="text" name="phone_2" class="form-control"  >
                            </div>
                        </div>
                        <div class="form-group my-2 row">
                            <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">  Phone (other) : </label>

                            <div class="col-xl-9 col-md-10 col-sm-10">
                                <input type="text" name="phone_3" class="form-control" >
                            </div>
                        </div>
                        <div class="form-group my-2 row">
                            <label class="col-xl-3 col-md-2 col-sm-2 col-xs-2 col-form-label">Address :</label>
                            <div class="col-xl-9 col-md-10 col-sm-10">
                                <input type="text" name="address" class="form-control" placeholder="Enter User Address">
                            </div>
                        </div>
                        <div class=" row" style="margin-top: 40px;">
                            <div class="col-md-12">
                                <label class=" col-form-label">Distribution Center / Shop Setup:</label>
                                <select name="dis_cen_id[]" id="" class="form-control status js-example-basic-multiple" multiple="multiple">
                                    @foreach ($distributions as  $distribution)
                                      <option value="{{ $distribution->dis_cen_id}}">{{ $distribution->dis_cen_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="  row" style="margin-top: 30px;">
                                <label class="col-md-12 col-form-label">Accounts Group Access Range :</label>

                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 1 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="group_id_array[]" id="" class="form-control status js-example-basic-single" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($groupCharts)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group  2 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="group_id_array[]" id="" class="form-control status js-example-basic-single" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($groupCharts)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 3 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="group_id_array[]" id="" class="form-control status js-example-basic-single" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($groupCharts)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 4 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="group_id_array[]" id="" class="form-control status js-example-basic-single" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($groupCharts)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 5 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="group_id_array[]" id="" class="form-control status js-example-basic-single" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($groupCharts)!!}
                                    </select>
                                </div>
                            </div>
                              <div class="  row" style="margin-top: 30px;">
                                <label class="col-md-12 col-form-label">Stock Group Access Range :</label>

                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 1 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="stock_group_id_multiple[]" id="" class="form-control status js-example-basic-single" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($stockGroup)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group  2 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="stock_group_id_multiple[]" id="" class="form-control status js-example-basic-single" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($stockGroup)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 3 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="stock_group_id_multiple[]" id="" class="form-control status js-example-basic-single" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($stockGroup)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 4 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="stock_group_id_multiple[]" id="" class="form-control status js-example-basic-single" required>
                                        <option value="0">--select one--</option>
                                        {!!html_entity_decode($stockGroup)!!}
                                    </select>
                                </div>
                            </div>
                            <div class=" my-1 row">
                                <label class="col-xl-2 col-md-2 col-sm-2 col-xs-2 col-form-label">Group 5 :</label>
                                <div class="col-xl-10 col-md-10 col-sm-10 col-xs-2">
                                    <select name="stock_group_id_multiple[]" id="" class="form-control status js-example-basic-single" required>
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
    </div>
    @endslot
    @slot('footer')
        <div class="col-lg-6 ">
            <div class="form-group">
                <button type="submit"  id="add_user_btn" class="btn hor-grd btn-grd-primary btn-block submit" style="width:100%" ><u>S</u>ave</button>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group">
            <a class=" btn hor-grd btn-grd-success btn-block " href="<?php echo route('dashboard') ?>" style="width:100%"><u>B</u>ack</a>
            </div>
        </div>
    @endslot
 @endcomponent
@push('js')
<script>
    $(function() {
        // add new user ajax request
        $("#add_user_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            $("#add_user_btn").text('Adding...');
            $.ajax({
                    url: '{{ route("user.store") }}',
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
                        $("#add_user_btn").text('Add User');

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
                           $('#error_user_name').text(data.responseJSON.data.log_in_id ?.[0]);
                          $('#error_password').text(data.responseJSON.data.password ?.[0]);
                        }

                    }
            });
        });

    });
    //data validation data clear
    function claer_error(){
        $('#error_user_name').text('');
        $('#error_password').text('');
    }
    </script>

@endpush
@endsection
