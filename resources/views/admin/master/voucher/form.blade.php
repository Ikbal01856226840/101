 <!-- Chart group add model  -->
 <div class="modal fade " id="AddStockGroupModal"  aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog the-modal">
        <div class="modal-content the-modal__container">
            <div class="modal-header .the-modal__header ">
                <h5 class="modal-title" id="exampleModalLabel"> Add New Stock Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form  method="POST" id="add_stock_group_form" enctype="multipart/form-data">
                @csrf
               {{ method_field('POST') }}
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group ">
                            <label  for="exampleInputEmail1">Stock Group Name</label>
                                <input type="text" name="stock_group_name" class="form-control " placeholder="Enter Stock Group Name" required>
                                <span id='error_stock_group_name' class=" text-danger"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group ">
                            <label  for="exampleInputEmail1">Under Group :</label>
                                <select name="under" id="group_id" class="form-control  js-example-basic-single  group_id left-data" required>
                                    <option value="0">Select</option>
                                    {!!html_entity_decode($select_option_tree)!!}
                                </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group ">
                            <label  for="exampleInputEmail1">Allow Items in this Group : :</label>
                                <select name="item_add"  class="form-control  js-example-basic-single   left-data" required>
                                    <option value="Yes">Yes</option>
                                    <option value="No">NO</option>
                                </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group ">
                            <label  for="exampleInputEmail1">Alias :</label>
                                <input type="text" name="alias" class="form-control" placeholder="Enter Alias " >
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label  for="exampleInputEmail1">Category :</label>
                                <select name="group_category" id="group_category" class="form-control js-example-basic-single " required>
                                    <option value="0">Not_Selected</option>
                                    <option value="1">Raw_Materials</option>
                                    <option value="2">Finished_Goods</option>
                                    <option value="3">Work_in_progress</option>
                                    <option value="4">Spare_Parts</option>
                                    <option value="9">Others</option>
                                </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger " data-dismiss="modal">Close</button>
                    <button type="submit" id="add_stock_group_btn" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
 <!-- Chart group edit model  -->
<div class="modal fade " id="EditStockGroupModal"  aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog the-modal">
        <div class="modal-content the-modal__container">
            <div class="modal-header .the-modal__header ">
                <h5 class="modal-title" id="exampleModalLabel"> Edit Stock Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form  method="POST" id="edit_stock_group_form" enctype="multipart/form-data">
                @csrf
                {{ method_field('PUT') }}
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group ">
                            <label  for="exampleInputEmail1">Stock Group Name</label>
                                <input type="text" name="stock_group_name" class="form-control stock_group_name" placeholder="Enter Stock Group Name" required>
                                <span id='error_stock_group_name' class=" text-danger"></span>
                                <input type="hidden" class="id">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group ">
                            <label  for="exampleInputEmail1">Under Group :</label>
                                <select name="under"  class="form-control js-example-basic-single  js-example-basic under left-data" required>
                                    <option value="0">Select</option>
                                    {!!html_entity_decode($select_option_tree)!!}
                                </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group ">
                            <label  for="exampleInputEmail1">Allow Items in this Group :</label>
                                <select name="item_add"  class="form-control js-example-basic-single  js-example-basic  item_add  left-data" required>
                                    <option value="Yes">Yes</option>
                                    <option value="No">NO</option>
                                </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group ">
                            <label  for="exampleInputEmail1">Alias :</label>
                                <input type="text" name="alias" class="form-control alias" placeholder="Enter Alias " >
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label  for="exampleInputEmail1">Category :</label>
                                <select name="group_category" id="group_category" class="form-control js-example-basic-single js-example-basic group_category " required>
                                    <option value="0">Not_Selected</option>
                                    <option value="1">Raw_Materials</option>
                                    <option value="2">Finished_Goods</option>
                                    <option value="3">Work_in_progress</option>
                                    <option value="4">Spare_Parts</option>
                                    <option value="9">Others</option>
                                </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger " data-dismiss="modal">Close</button>
                    <button type="submit" id="edit_stock_group_btn" class="btn btn-primary">Edit</button>
                    <button  type="button" class="btn btn-danger deleteIcon "  data-dismiss="modal">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
