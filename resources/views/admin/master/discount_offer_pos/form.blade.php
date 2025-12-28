 <!-- Distribution Crenter  Add model  -->
 @component('components.modal', [
 'id' => 'AddDiscountOfferModel',
 'class' => 'modal fade',
 'size' => 'modal-xl',
 'form_id' => 'add_discount_offer_form',
 'method'=> 'POST',
 ])
 @slot('title', 'Add New Discount Offer POS')
 @slot('body')
 <div class="row">
    <div class="form-group col-lg-6">
        <div class="border border-dark m-1">
            <div class="form-group  m-2">
                <label for="exampleInputEmail1">Shop Name :</label>
                <select name="dis_cen_id" id="dis_cen_under" class="form-control  js-example-basic-single  dis_cen_under left-data" required>
                    <option value="">Select</option>
                    <option value="1">Main Location</option>
                    {!!html_entity_decode($select_option_tree)!!}
                </select>
            </div>
            <div class="form-group m-2">
                <label for="exampleInputEmail1">Under Group :</label>
                <select name="stock_group_id" class="form-control  js-example-basic-single  under left-data under_add" required>
                    <option value="">Select</option>
                    <option value="1">Primary</option>
                    {!!html_entity_decode($select_option_stock_group_tree)!!}
                </select>
            </div>
            <div class="form-group m-2">
                <label for="exampleInputEmail1">Unit/Branch :</label>
                <select name="unit_or_branch" class="form-control  js-example-basic-single unit_or_branch" required>
                    <option value="">--select--</option>
                    @foreach (unit_branch() as $branch)
                    <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group  m-2">
                <label for="exampleInputEmail1">New Selling Price :</label>
                <input type="number" name="price" class="form-control form-control-lg new_price" placeholder="New Selling Price" readonly>
            </div>
            <div class="form-group  m-2">
               <label for="exampleInputEmail1">Remarks :</label>
               <textarea name="remarks" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
           </div>
        </div>
    </div>
    <div class="form-group col-lg-6 ">
        <div class="border border-success">
           <div class="form-group  m-2">
               <label for="exampleInputEmail1">Current Selling Price :</label>
               <input type="number" name="discount" class="form-control form-control-lg current_price" placeholder="Current Selling Price">
           </div>
           <div class="form-group  m-2">
                <label for="exampleInputEmail1">Discount:</label>
                <input type="number" name="discount" class="form-control form-control-lg discount" placeholder="Enter Discount">
          </div>
            <div class="form-group m-2 ">
                <label for="exampleInputEmail1">Start Date</label>
                <input type="date" name="date_from" class="form-control form-control-lg date_start" value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group m-2 ">
                <label for="exampleInputEmail1">End Date : </label>
                <input type="date" name="date_to" class="form-control form-control-lg end_start" value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group m-2">
               <label for="exampleInputEmail1">Responsible Officer /Approved by : </label>
               <select name="approved_by" id="" class="form-control status js-example-basic-single" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
            </select>
           </div>
        </div>
    </div>
</div>
 @endslot
 @slot('footer')
 <button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
 <button type="submit" id="add_discount_offer_btn" class="btn btn-primary">Add</button>
 @endslot
 @endcomponent

 <!-- Distribution Center Edit  model  -->
 @component('components.modal', [
 'id' => 'EditDiscountOfferModel',
 'class' => 'amodal fade',
 'size' => 'modal-xl',
 'form_id' => 'edit_discount_offer_form',
 'method'=> 'PUT',
 ])
 @slot('title', 'Update Discount Offer POS')
 @slot('body')
     <div class="row">
        <div class="form-group col-lg-6">
            <div class="border border-dark m-1">
                <div class="form-group  m-2">
                    <label for="exampleInputEmail1">Shop Name :</label>
                    <select name="dis_cen_id" id="dis_cen_under" class="form-control  js-example-basic  dis_cen_id left-data" required>
                        <option value="">Select</option>
                        <option value="1">Main Location</option>
                        {!!html_entity_decode($select_option_tree)!!}
                    </select>
                    <input type="hidden" class="form-control id">
                </div>
                <div class="form-group m-2">
                    <label for="exampleInputEmail1">Under Group :</label>
                    <select name="stock_group_id" class="form-control  js-example-basic  under left-data stock_group_id" required>
                        <option value="">Select</option>
                        <option value="1">Primary</option>
                        {!!html_entity_decode($select_option_stock_group_tree)!!}
                    </select>
                </div>
                <div class="form-group m-2">
                    <label for="exampleInputEmail1">Unit/Branch :</label>
                    <select name="unit_or_branch" class="form-control  js-example-basic unit_or_branch" required>
                        <option value="">--select--</option>
                        @foreach (unit_branch() as $branch)
                        <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group  m-2">
                    <label for="exampleInputEmail1">New Selling Price :</label>
                    <input type="number" name="price" class="form-control form-control-lg new_price" placeholder="New Selling Price" readonly>
                </div>
                <div class="form-group  m-2">
                   <label for="exampleInputEmail1">Remarks :</label>
                   <textarea name="remarks" class="form-control remarks" id="exampleFormControlTextarea1" rows="3"></textarea>
               </div>
            </div>
        </div>
        <div class="form-group col-lg-6 ">
            <div class="border border-success">
               <div class="form-group  m-2">
                   <label for="exampleInputEmail1">Current Selling Price :</label>
                   <input type="number" name="discount" class="form-control form-control-lg current_price" placeholder="Current Selling Price">
               </div>
               <div class="form-group  m-2">
                    <label for="exampleInputEmail1">Discount:</label>
                    <input type="number" name="discount" class="form-control form-control-lg discount" placeholder="Enter Discount">
              </div>
                <div class="form-group m-2 ">
                    <label for="exampleInputEmail1">Start Date</label>
                    <input type="date" name="date_from" class="form-control form-control-lg date_from" value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group m-2 ">
                    <label for="exampleInputEmail1">End Date : </label>
                    <input type="date" name="date_to" class="form-control form-control-lg end_start date_to" value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group m-2">
                   <label for="exampleInputEmail1">Responsible Officer /Approved by : </label>
                   <select name="approved_by" id="" class="form-control status js-example-basic approved_by" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
               </div>
            </div>
        </div>
    </div>
 @endslot
 @slot('footer')
 <button type="submit" id="edit_discount_offer_btn" class="btn btn-primary">Update</button>
    @if(user_privileges_check('master','Distribution Center','delete_role'))
 <button type="button" class="btn btn-danger deleteIcon " data-dismiss="modal">Delete</button>
 @endif
    <button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
 @endslot
 @endcomponent
