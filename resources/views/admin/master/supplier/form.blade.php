 <!-- supplier  add model  -->
 @component('components.modal', [
    'id'    => 'AddSupplierModal',
    'class' => 'modal fade',
    'form_id' => 'add_supplier_form',
    'method'=> 'POST',
])
    @slot('title', 'Add New Supplier')
    @slot('body')
        <div class="row">
            <div class="form-group ">
                    <label  for="exampleInputEmail1">Supplier Name</label>
                    <input type="text" name="supplier_name" class="form-control " placeholder="Enter Supplier" required>
                    <span id='error_supplier_name' class="text-danger"></span>
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                <select name="ledger_id"  class="form-control js-example-basic-single ledger_id" required>
                    <option value="0">--Select--</option>
                        {!!html_entity_decode($ledger_tree)!!}
                    </select>
            </div>
        </div>

        <div class="row">
            <div class="form-group ">
                    <label  for="exampleInputEmail1">Phone</label>
                    <input type="text" name="phone1" class="form-control " placeholder="Enter Phone Name" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                    <label  for="exampleInputEmail1">District</label>
                    <input type="text" name="district" class="form-control " placeholder="Enter District Name" >
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                    <label  for="exampleInputEmail1">Proprietor</label>
                    <input type="text" name="proprietor" class="form-control " placeholder="Enter Proprietor Name" >
            </div>
        </div>
    @endslot
    @slot('footer')
        <button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
        <button type="submit" id="add_measure_btn" class="btn btn-primary">Add</button>
    @endslot
@endcomponent

 <!-- supplier edit model  -->
 @component('components.modal', [
    'id'    => 'EditSupplierModal',
    'class' => 'modal fade',
    'form_id' => 'edit_supplier_form',
    'method'=> 'PUT',
])
    @slot('title', 'Edit Supplier')
    @slot('body')
        <div class="row">
            <div class="form-group ">
                    <label  for="exampleInputEmail1">Supplier Name</label>
                    <input type="hidden" name="supplier_id" class="id">
                    <input type="text" name="supplier_name" class="form-control supplier_name" placeholder="Enter Supplier" required>
                    <span id='update_error_supplier_name' class="text-danger"></span>
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                <select name="ledger_id"  class="form-control js-example-basic ledger_id" required>
                        {!!html_entity_decode($ledger_tree)!!}
                    </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                    <label  for="exampleInputEmail1">Phone</label>
                    <input type="text" name="phone1" class="form-control phone1" placeholder="Enter Phone Name" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                    <label  for="exampleInputEmail1">District</label>
                    <input type="text" name="district" class="form-control district" placeholder="Enter District Name" >
            </div>
        </div>
        <div class="row">
            <div class="form-group ">
                    <label  for="exampleInputEmail1">Proprietor</label>
                    <input type="text" name="proprietor" class="form-control proprietor" placeholder="Enter Proprietor Name" >
            </div>
        </div>
    @endslot
    @slot('footer')
        <button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
        <button type="submit" id="edit_supplier_btn" class="btn btn-primary">Upadte</button>
        @if(user_privileges_check('master','Supplier','delete_role'))
         <button  type="button" class="btn btn-danger deleteIcon "  data-dismiss="modal">Delete</button>
        @endif
    @endslot
@endcomponent
