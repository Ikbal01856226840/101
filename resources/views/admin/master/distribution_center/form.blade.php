 <!-- Distribution Crenter  Add model  -->
 @component('components.modal', [
 'id' => 'AddDistributionModel',
 'class' => 'modal fade',
 'size' => 'modal-xl',
 'form_id' => 'add_distribution_form',
 'method'=> 'POST',
 ])
 @slot('title', ' Add New Distribution Center')
 @slot('body')
 <div class="row">
     <div class="form-group col-lg-6">
         <div class="border border-dark m-1">
             <div class="form-group  m-2">
                 <label for="exampleInputEmail1"> Name :</label>
                 <input type="text" name="dis_cen_name" class="form-control form-control-lg" placeholder="Enter Ledger Name" required>
                 <span id='error_distribution_name' class=" text-danger"></span>
             </div>
             <div class="form-group  m-2">
                 <label for="exampleInputEmail1">Under Group :</label>
                 <select name="dis_cen_under" id="dis_cen_under" class="form-control  js-example-basic-single  dis_cen_under left-data" required>
                     <option value="0">Select</option>
                     <option value="1">Main Location</option>
                     {!!html_entity_decode($select_option_tree)!!}
                 </select>
             </div>
             <div class="form-group m-2">
                 <label for="exampleInputEmail1">Unit/Branch :</label>
                 <select name="unit_or_branch" class="form-control  js-example-basic-single unit_or_branch" required>
                     <option value="0">--select--</option>
                     @foreach (unit_branch() as $branch)
                     <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                     @endforeach
                 </select>
             </div>
             <div class="form-group  m-2">
                 <label for="exampleInputEmail1">Address :</label>
                 <textarea name="address" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
             </div>
             <div class="form-group m-2">
                 <label for="exampleInputEmail1">Discount</label>
                 <input type="number" name="discount" class="form-control form-control-lg" placeholder="Enter Discount">
             </div>
         </div>
     </div>
     <div class="form-group col-lg-6 ">
         <div class="border border-success">
             <div class="form-group m-2 ">
                 <label for="exampleInputEmail1">Alias :</label>
                 <input type="text" name="alias" class="form-control form-control-lg" placeholder="Enter Alais">
             </div>
             <div class="form-group m-2 ">
                 <label for="exampleInputEmail1">Start Date</label>
                 <input type="date" name="date_start" class="form-control form-control-lg" value="{{ date('Y-m-d') }}">
             </div>
             <div class="form-group m-2 ">
                 <label for="exampleInputEmail1">End Date : </label>
                 <input type="date" name="date_end" class="form-control form-control-lg" value="{{ date('Y-m-d') }}">
             </div>
         </div>
     </div>
 </div>
 @endslot
 @slot('footer')
 <button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
 <button type="submit" id="add_distribution_btn" class="btn btn-primary">Add</button>
 @endslot
 @endcomponent

 <!-- Distribution Center Edit  model  -->
 @component('components.modal', [
 'id' => 'EditDistributionModel',
 'class' => 'amodal fade',
 'size' => 'modal-xl',
 'form_id' => 'edit_distribution_form',
 'method'=> 'PUT',
 ])
 @slot('title', 'Update New Distribution Center')
 @slot('body')
 <div class="row">
     <div class="form-group col-lg-6">
         <div class="border border-dark m-1">
             <div class="form-group  m-2">
                 <label for="exampleInputEmail1"> Name :</label>
                 <input type="text" name="dis_cen_name" class="form-control form-control-lg dis_cen_name" placeholder="Enter Distribution Name" required>
                 <span id='error_distribution_name' class="text-danger"></span>
                 <input type="hidden" class="id">
             </div>
             <div class="form-group  m-2">
                 <label for="exampleInputEmail1">Under Group :</label>
                 <select name="dis_cen_under" id="dis_cen_under" class="form-control  js-example-basic  dis_cen_under left-data" required>
                     <option value="0">Select</option>
                     {!!html_entity_decode($select_option_tree)!!}
                 </select>
             </div>
             <div class="form-group m-2">
                 <label for="exampleInputEmail1">Unit/Branch :</label>
                 <select name="unit_or_branch" class="form-control  js-example-basic unit_or_branch" required>
                     <option value="0">--select--</option>
                     @foreach (unit_branch() as $branch)
                     <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                     @endforeach
                 </select>
             </div>
             <div class="form-group  m-2">
                 <label for="exampleInputEmail1">Address :</label>
                 <textarea name="address" class="form-control address" id="exampleFormControlTextarea1" rows="3"></textarea>
             </div>
             <div class="form-group m-2">
                 <label for="exampleInputEmail1">Discount</label>
                 <input type="number" name="discount" class="form-control form-control-lg discount" placeholder="Enter Discount">
             </div>
         </div>
     </div>
     <div class="form-group col-lg-6 ">
         <div class="border border-success">
             <div class="form-group m-2 ">
                 <label for="exampleInputEmail1">Alias :</label>
                 <input type="text" name="alias" class="form-control form-control-lg alias_edit" placeholder="Enter Alais">
             </div>

             <div class="form-group m-2 ">
                 <label for="exampleInputEmail1">Start Date</label>
                 <input type="date" name="date_start" class="form-control form-control-lg date_start" value="{{ date('Y-m-d') }}">
             </div>
             <div class="form-group m-2 ">
                 <label for="exampleInputEmail1">End Date : </label>
                 <input type="date" name="date_end" class="form-control form-control-lg end_start" value="{{ date('Y-m-d') }}">
             </div>
         </div>
     </div>
 </div>
 @endslot
 @slot('footer')
 <button type="submit" id="edit_distribution_btn" class="btn btn-primary">Update</button>
 @if(user_privileges_check('master','Distribution Center','delete_role'))
 <button type="button" class="btn btn-danger deleteIcon " data-dismiss="modal">Delete</button>
 @endif
 <button type="button" class="btn btn-danger model_rest_btn" data-dismiss="modal">Close</button>
 @endslot
 @endcomponent