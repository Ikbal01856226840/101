 <!-- Components add model  -->
    @component('components.modal', [
        'id'    => 'AddComponentsModal',
        'class' => 'modal fade',
        'form_id' => 'add_components_form',
        'method'=> 'POST',
    ])
        @slot('title', 'Add New Components')
        @slot('body')
            <div class="row">
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Component Name</label>
                        <input type="text" name="comp_name" class="form-control " placeholder="Enter Components Name" required>
                        <span id='error_comp_name_name' class=" text-danger"></span>
                </div>
            </div>
            <div class="row">
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Unit/Branch :</label>
                    <select name="unit_or_branch"  class="form-control  js-example-basic-single unit_or_branch" required>
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
                    <input type="text" name="comp_alias" class="form-control" placeholder="Enter Alias " >
                </div>
            </div>
            <div class="row">
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Note :</label>
                    <textarea name="notes" class="form-control"></textarea>
                </div>
            </div>
        @endslot
        @slot('footer')
          <button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
          <button type="submit" id="add_components_btn" class="btn btn-primary">Add</button>
        @endslot
   @endcomponent
  <!-- Components edit model  -->
  @component('components.modal', [
        'id'    => 'EditComponentsModal',
        'class' => 'modal fade',
        'form_id' => 'edit_components_form',
        'method'=> 'PUT',
    ])
        @slot('title', 'Update Components')
        @slot('body')
            <div class="row">
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Component Name</label>
                        <input type="text" name="comp_name" class="form-control comp_name " placeholder="Enter Components Name" required>
                        <span id='error_comp_name_name' class=" text-danger"></span>
                        <input type="hidden" class="id">
                </div>
            </div>
            <div class="row">
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Unit/Branch :</label>
                        <select name="unit_or_branch"  class="form-control  js-example-basic unit_or_branch" required>
                            <option value="0">--select--</option>
                            @foreach (unit_branch() as $branch)
                            <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                            @endforeach
                        </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Alias :</label>
                    <input type="text" name="comp_alias" class="form-control comp_alias" placeholder="Enter Alias " >
                </div>
            </div>
            <div class="row">
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Note :</label>
                    <textarea name="notes" class="form-control notes"></textarea>
                </div>

            </div>
        @endslot
        @slot('footer')
            <button type="submit" id="edit_components_chart_btn" class="btn btn-primary">Update</button>
            @if(user_privileges_check('master','Components','delete_role'))
             <button  type="button" class="btn btn-danger deleteIcon "  data-dismiss="modal">Delete</button>
            @endif
            <button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
        @endslot
   @endcomponent

