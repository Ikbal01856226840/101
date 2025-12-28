@php
  $page_wise_setting_data=page_wise_setting(Auth::user()->id,4);
@endphp
<!-- Godown Add model  -->
@component('components.modal', [
    'id' => 'AddGodownModal',
    'class' => 'modal fade',
    'form_id' => 'add_godown_form',
    'method'=> 'POST',
])

@slot('title', 'Add New Godown')
@slot('body')
<div class="row">
    <div class="form-group ">
        <label for="exampleInputEmail1"> Name</label>
        <input type="text" name="godown_name" class="form-control " placeholder="Enter Godown Name" required>
        <span id='error_godown_name' class=" text-danger"></span>
    </div>
</div>
<div class="row">
    <div class="form-group ">
        <label for="exampleInputEmail1">Under Group :</label>
        <select name="godown_type" id="godown_type" class="form-control  js-example-basic-single godown_type   left-data godown_type_add" required>
            <option value="0">--Select--</option>
            <option value="Undefined">Undefined</option>
            <option value="General">General</option>
            <option value="Damage">Damage</option>
            <option value="GIT">GIT(Good in Transit)</option>
        </select>
    </div>
</div>
<div class="row">
    <div class="form-group ">
        <label for="exampleInputEmail1">Unit/Branch :</label>
        <select name="unit_or_branch" class="form-control  js-example-basic-single unit_or_branch" required>
            <option value="0">--Select--</option>
            @foreach (unit_branch() as $branch)
            <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row">
    <div class="form-group ">
        <label for="exampleInputEmail1">Alias :</label>
        <input type="text" name="alias" class="form-control" placeholder="Enter Alias ">
        <span id='error_alias' class=" text-danger"></span>
    </div>
</div>
<div class="row">
    <div class="form-group ">
        <label for="exampleInputEmail1">Address :</label>
        <textarea name="address" class="form-control"></textarea>
    </div>

</div>
@endslot
@slot('footer')
<button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
<button type="submit" id="add_godown_btn" class="btn btn-primary">Add</button>
@endslot
@endcomponent

<!-- Godown edit model  -->
@component('components.modal', [
    'id' => 'EditGodownModal',
    'class' => 'modal fade',
    'form_id' => 'edit_godown_form',
    'method'=> 'PUT',
])
@slot('title', 'Update Godown')
@slot('body')
<div class="row">
    <div class="form-group ">
        <label for="exampleInputEmail1"> Name</label>
        <input type="text" name="godown_name" class="form-control godown_name " placeholder="Enter Godown Name" required>
        <input type="hidden" name="id" class="id" >
        <span id='edit_error_godown_name' class=" text-danger"></span>
    </div>
</div>
<div class="row">
    <div class="form-group ">
        <label for="exampleInputEmail1">Under Group :</label>
        <select name="godown_type" id="godown_type" class="form-control  js-example-basic godown_type   left-data" required>
            <option value="Undefined">Undefined</option>
            <option value="General">General</option>
            <option value="Damage">Damage</option>
            <option value="GIT">GIT(Good in Transit)</option>
        </select>
    </div>
</div>
<div class="row">
    <div class="form-group ">
        <label for="exampleInputEmail1">Unit/Branch :</label>
        <select name="unit_or_branch" class="form-control  js-example-basic unit_or_branch" required>
            <option value="0">--Select--</option>
            @foreach (unit_branch() as $branch)
            <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row">
    <div class="form-group">
        <label for="exampleInputEmail1">Alias :</label>
        <input type="text" name="alias" id="edit_alias" class="form-control edit_alias" placeholder="Enter Alias ">
        <span id='edit_error_alias' class=" text-danger"></span>
    </div>
</div>
<div class="row">
    <div class="form-group ">
        <label for="exampleInputEmail1">Address :</label>
        <textarea name="address" class="form-control address"></textarea>
    </div>

</div>
@endslot
@slot('footer')
<button type="submit" id="edit_group_chart_btn" class="btn btn-primary">Update</button>
@if(user_privileges_check('master','Godown','delete_role'))
<button type="button" class="btn btn-danger deleteIcon " data-dismiss="modal">Delete</button>
@endif
<button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
@endslot
@endcomponent

<script>
    $('.godown_type_add').on('change', function() {
        localStorage.setItem("godown_type_add", $('.godown_type_add').val());
    });
  
    if({{$page_wise_setting_data?$page_wise_setting_data->last_insert_data_set:0}}==5){
        $('.godown_type_add').val(localStorage.getItem("godown_type_add"))
    }
    
</script>