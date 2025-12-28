 <!-- size  add model  -->
 @component('components.modal', [
 'id' => 'AddSizeModal',
 'class' => 'modal fade',
 'form_id' => 'add_size_form',
 'method'=> 'POST',
 ])
 @slot('title', 'Add New Unit Of Size')
 @slot('body')
 <div class="row">
     <div class="form-group ">
         <label for="exampleInputEmail1">Size</label>
         <input type="text" name="symbol" class="form-control " placeholder="Enter Size" required>
         <span id='error_symbol_name' class=" text-danger"></span>
     </div>
 </div>
 <div class="row">
     <div class="form-group ">
         <label for="exampleInputEmail1">Formal Name</label>
         <input type="text" name="formal_name" class="form-control " placeholder="Enter Formal Name" >
         <span id='error_formal_name' class=" text-danger"></span>
     </div>
 </div>
 @endslot
 @slot('footer')
 <button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
 <button type="submit" id="add_size_btn" class="btn btn-primary">Add</button>
 @endslot
 @endcomponent

 <!-- size edit model  -->
 @component('components.modal', [
 'id' => 'EditSizeModal',
 'class' => 'modal fade',
 'form_id' => 'edit_size_form',
 'method'=> 'PUT',
 ])
 @slot('title', 'Edit Unit Of Size')
 @slot('body')
 <div class="row">
     <div class="form-group ">
         <label for="exampleInputEmail1">Size</label>
         <input type="hidden" class="id">
         <input type="text" name="symbol" class="form-control symbol" placeholder="Enter Size" required>
         <span id='error_update_symbol_name' class=" text-danger"></span>
     </div>
 </div>
 <div class="row">
     <div class="form-group ">
         <label for="exampleInputEmail1">Formal Name</label>
         <input type="text" name="formal_name" class="form-control formal_name" placeholder="Enter Formal Name">
         <span id='error_update_formal_name' class=" text-danger"></span>
     </div>
 </div>

 @endslot
 @slot('footer')
 <button type="submit" id="edit_size_btn" class="btn btn-primary">Upadte</button>
 @if(user_privileges_check('master','Unit of Size','delete_role'))
 <button type="button" class="btn btn-danger deleteIcon " data-dismiss="modal">Delete</button>
 <button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
 @endif
 @endslot
 @endcomponent