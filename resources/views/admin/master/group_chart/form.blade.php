 <!-- Chart group add model  -->
 @component('components.modal', [
    'id'    => 'AddGroupChartModal',
    'class' => 'modal fade',
    'form_id' => 'add_group_chart_form',
    'method'=> 'POST',
]);
@php
   $page_wise_setting_data=page_wise_setting(Auth::user()->id,1);
@endphp  
    @slot('title', 'Add New Accounts Group')
    @slot('body')
        <div class="row">
            <div class="form-group ">
                <label  for="exampleInputEmail1">Group Name</label>
                    <input type="text" name="group_chart_name" class="form-control " placeholder="Enter Group Name" required>
                    <span id='error_group_chart_name' class=" text-danger"></span>
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                <label  for="exampleInputEmail1">Under Group :</label>
                    <select name="under" id="group_id" class="form-control  js-example-basic-single  group_id left-data group_id_add" required>
                        <option value="0">--Select--</option>
                        {!!html_entity_decode($group_chart_id)!!}
                    </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                <label  for="exampleInputEmail1">Unit/Branch :</label>
                    <select name="unit_or_branch"  class="form-control  js-example-basic-single " required>
                        <option value="0">--Select--</option>
                        @foreach (unit_branch() as $branch)
                        <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                        @endforeach
                    </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                <label  for="exampleInputEmail1">Alias :</label>
                    <input type="text" name="alias"  class="form-control" placeholder="Enter Alias " >
                    <span id='error_alias' class=" text-danger"></span>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label  for="exampleInputEmail1">Nature of Group :</label>
                    <select name="nature_group" id="nature_group1" class="form-control js-example-basic-single nature_group1" >
                        <option value="0">Select</option>
                    </select>
            </div>
        </div>
    @endslot
    @slot('footer')
        <button type="button" class="btn btn-danger " data-dismiss="modal">Close</button>
        <button type="submit" id="add_group_chart_btn" class="btn btn-primary">Add</button>
    @endslot
@endcomponent
 <!-- Chart group edit model  -->
 @component('components.modal', [
    'id' => 'EditGroupChartModal',
    'class' => 'modal fade',
    'form_id' => 'edit_group_chart_form',
    'method'=> 'PUT',
])
    @slot('title', 'Upadte Accounts Group')
    @slot('body')
        <div class="row">
            <div class="form-group ">
                <label  for="exampleInputEmail1">Group Name</label>
                    <input type="text" name="group_chart_name" class="form-control group_chart_name" placeholder="Enter Group Name" required>
                    <span id='edit_error_group_chart_name' class=" text-danger"></span>
                    <input type="hidden" name="id" class="id" >
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                <label  for="exampleInputEmail1">Under Group :</label>
                    <select name="under"  id="group_id" class="form-control   js-example-basic group_id" required>
                        {!!html_entity_decode($group_chart_id)!!}
                    </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                <label  for="exampleInputEmail1">Unit/Branch :</label>
                    <select name="unit_or_branch"  class="form-control  js-example-basic unit_or_branch" required>
                        <option value="0">--Select--</option>
                        @foreach (unit_branch() as $branch)
                        <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                        @endforeach
                    </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                <label  for="exampleInputEmail1">Alias :</label>
                    <input type="text" name="alias" class="form-control" id="edit_alias" placeholder="Enter Alias ">
                    <span id='edit_error_alias' class=" text-danger"></span>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label  for="exampleInputEmail1">Nature of Group :</label>
                <select name="nature_group" id="nature_group1" class="form-control  nature_group1 js-example-basic" >
                </select>
            </div>
        </div>
    @endslot
    @slot('footer')
        <button type="submit" id="edit_group_chart_btn" class="btn btn-primary">Upadte</button>
        @if(user_privileges_check('master','Group','delete_role'))
          <button  type="button" class="btn btn-danger deleteIcon "  data-dismiss="modal">Delete</button>
        @endif
        <button type="button" class="btn btn-danger " data-dismiss="modal">Close</button>
    @endslot
@endcomponent
<script>
     $('.group_id_add').on('change', function() {
        localStorage.setItem("group_id_add", $('.group_id_add').val());  
    });
    if({{$page_wise_setting_data?$page_wise_setting_data->last_insert_data_set:''}}==5){ 
        if(localStorage.getItem("group_id_add")){
            $('.group_id_add').val(localStorage.getItem("group_id_add"))
        }else{
            
            $('.group_id_add').val(0);
        }
       
    }
   
</script>
