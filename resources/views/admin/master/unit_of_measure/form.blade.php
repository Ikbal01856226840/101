 <!-- Measure  add model  -->
 @component('components.modal', [
    'id'    => 'AddMeasureModal',
    'class' => 'modal fade',
    'form_id' => 'add_measure_form',
    'method'=> 'POST',
])
    @slot('title', 'Add New Unit Of measure')
    @slot('body')
        <div class="row">
            <div class="form-group ">
                    <label  for="exampleInputEmail1">Measure</label>
                    <input type="text" name="symbol" class="form-control " placeholder="Enter Measure" required>
                    <span id='error_symbol_name' class=" text-danger"></span>
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                    <label  for="exampleInputEmail1">Formal Name</label>
                    <input type="text" name="formal_name" class="form-control " placeholder="Enter Formal Name" >
                    <span id='error_formal_name' class=" text-danger"></span>
            </div>
        </div>
    @endslot
    @slot('footer')
        <button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
        <button type="submit" id="add_measure_btn" class="btn btn-primary">Add</button>
    @endslot
@endcomponent

 <!-- Measure edit model  -->
 @component('components.modal', [
    'id'    => 'EditMeasureModal',
    'class' => 'modal fade',
    'form_id' => 'edit_measure_form',
    'method'=> 'PUT',
])
    @slot('title', 'Edit Unit Of Measure')
    @slot('body')
    <div class="row">
        <div class="form-group ">
                <label  for="exampleInputEmail1">Measure</label>
                 <input type="hidden" class="id" name="id">
                <input type="text" name="symbol" class="form-control symbol" placeholder="Enter Measure" required>
                <span id='error_update_symbol_name' class=" text-danger"></span>
        </div>
    </div>
    <div class="row">
        <div class="form-group ">
                <label  for="exampleInputEmail1">Formal Name</label>
                <input type="text" name="formal_name" class="form-control formal_name" placeholder="Enter Formal Name">
                <span id='error_update_formal_name' class=" text-danger"></span>
        </div>
    </div>

    @endslot
    @slot('footer')
        <button type="submit" id="edit_measure_btn" class="btn btn-primary">Update</button>
        @if(user_privileges_check('master','Units of Measure','delete_role'))
          <button  type="button" class="btn btn-danger deleteIcon "  data-dismiss="modal">Delete</button>
        @endif
        <button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
    @endslot
@endcomponent
