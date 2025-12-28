<!-- stock group add model  -->
@component('components.modal', [
'id'=> 'AddStockGroupModal',
'class' => 'modal fade',
'form_id' => 'add_stock_group_form',
'method'=> 'POST',
])
@php
  $page_wise_setting_data=page_wise_setting(Auth::user()->id,10);
@endphp
@slot('title', 'Add Stock Group')
@slot('body')
<div class="row">
    <div class="form-group pb-0 mb-0">
        <label for="exampleInputEmail1">Stock Group Name</label>
        <input type="text" name="stock_group_name" class="form-control stock_group_name" placeholder="Enter Stock Group Name" required>
        <span id='error_stock_group_name' class=" text-danger"></span>
    </div>
    <div class="form-group pb-0 mb-0">
        <label for="exampleInputEmail1">Under Group :</label>
        <select name="under" class="form-control  js-example-basic  under left-data under_add" required>
            <option value="0">Select</option>
            <option value="1">Primary</option>
            {!!html_entity_decode($select_option_tree)!!}
        </select>
    </div>
    <div class="form-group pb-0 mb-0">
        <label for="exampleInputEmail1">Allow Items in this Group :</label>
        <select name="item_add" class="form-control  js-example-basic  item_add  left-data" required>
            <option value="Yes">Yes</option>
            <option value="No">NO</option>
        </select>
    </div>
    <div class="form-group pb-0 mb-0">
        <label for="exampleInputEmail1">Unit/Branch :</label>
        <select name="unit_or_branch" class="form-control   js-example-basic unit_or_branch" required>
            <option value="0">--select--</option>
            @foreach (unit_branch() as $branch)
            <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group pb-0 mb-0">
        <label for="exampleInputEmail1">Alias :</label>
        <input type="text" name="alias" class="form-control" placeholder="Enter Alias ">
        <span id='error_alias' class=" text-danger"></span>
    </div>

    <div class="form-group pb-0 mb-0">
        <label for="exampleInputEmail1">Category :</label>
        <select name="group_category" id="group_category" class="form-control js-example-basic  group_category " required>
            <option value="0">Not_Selected</option>
            <option value="1">Raw_Materials</option>
            <option value="2">Finished_Goods</option>
            <option value="3">Work_in_progress</option>
            <option value="4">Spare_Parts</option>
            <option value="9">Others</option>
        </select>
    </div>
    <div class="form-group  pb-0 mb-0">
        <label for="exampleInputEmail1">Sales Ledger  :</label>
        <select name="sales_ledger" class="form-control sales_ledger  js-example-basic" required>
            <option value="0">Select</option>

            {!!html_entity_decode($ledger_tree_salse)!!}
        </select>
    </div>
    <div class="form-group  pb-0 mb-0">
        <label for="exampleInputEmail1">Purchase Ledger :</label>
        <select name="purchase_ledger" class="form-control purchase_ledger  js-example-basic " required>
            <option value="0">Select</option>
            {!!html_entity_decode($ledger_tree_purchase)!!}
        </select>
    </div>
</div>
@endslot
@slot('footer')
<button type="submit" id="add_stock_group_btn" class="btn btn-primary">Add</button>
<button type="button" class="btn btn-danger " data-dismiss="modal">Close</button>
@endslot
@endcomponent
<!-- stock group edit model  -->
@component('components.modal', [
'id'=>'EditStockGroupModal',
'class' =>'modal fade',
'form_id' =>'edit_stock_group_form',
'method'=> 'PUT',
])
@slot('title', 'Update Stock Group')
@slot('body')
<div class="row">
    <div class="form-group pb-0 mb-0">
        <label for="exampleInputEmail1">Stock Group Name</label>
        <input type="text" name="stock_group_name" class="form-control stock_group_name" placeholder="Enter Stock Group Name" required>
        <span id='edit_error_stock_group_name' class=" text-danger"></span>
        <input type="hidden" name="id" class="id" >
    </div>
    <div class="form-group pb-0 mb-0">
        <label for="exampleInputEmail1">Under Group :</label>
        <select name="under" class="form-control   js-example under left-data" required>
            <option value="0">Select</option>
             <option value="1">Primary</option>
            {!!html_entity_decode($select_option_tree)!!}
        </select>
    </div>

    <div class="form-group pb-0 mb-0">
        <label for="exampleInputEmail1">Allow Items in this Group :</label>
        <select name="item_add" class="form-control   js-example  item_add  left-data" required>
            <option value="Yes">Yes</option>
            <option value="No">NO</option>
        </select>
    </div>

    <div class="form-group pb-0 mb-0">
        <label for="exampleInputEmail1">Unit/Branch :</label>
        <select name="unit_or_branch" class="form-control  js-example unit_or_branch" required>
            <option value="0">--select--</option>
            @foreach (unit_branch() as $branch)
            <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group pb-0 mb-0">
        <label for="exampleInputEmail1">Alias :</label>
        <input type="text" name="alias" class="form-control edit_alias" id="edit_alias" placeholder="Enter Alias ">
        <span id='edit_error_alias' class="text-danger"></span>
    </div>

    <div class="form-group pb-0 mb-0">
        <label for="exampleInputEmail1">Category :</label>
        <select name="group_category" id="group_category" class="form-control  js-example group_category " required>
            <option value="0">Not_Selected</option>
            <option value="1">Raw_Materials</option>
            <option value="2">Finished_Goods</option>
            <option value="3">Work_in_progress</option>
            <option value="4">Spare_Parts</option>
            <option value="9">Others</option>
        </select>
    </div>
    <div class="form-group  pb-0 mb-0">
        <label for="exampleInputEmail1">Sales Ledger  :</label>
        <select name="sales_ledger" class="form-control sales_ledger js-example" required>
            <option value="0">Select</option>

            {!!html_entity_decode($ledger_tree_salse)!!}
        </select>
    </div>
    <div class="form-group  pb-0 mb-0">
        <label for="exampleInputEmail1">Purchase Ledger :</label>
        <select name="purchase_ledger" class="form-control purchase_ledger js-example " required>
            <option value="0">Select</option>
            {!!html_entity_decode($ledger_tree_purchase)!!}
        </select>
    </div>
</div>
@endslot
@slot('footer')
<button type="submit" id="edit_stock_group_btn" class="btn btn-primary">Update</button>
@if(user_privileges_check('master','Stock Group','delete_role'))
<button type="button" class="btn btn-danger deleteIcon " data-dismiss="modal">Delete</button>
@endif
<button type="button" class="btn btn-danger " data-dismiss="modal">Close</button>
@endslot
@endcomponent
<script>
    $('.under_add').on('change', function() {
        localStorage.setItem("under_add", $('.under_add').val());

    });
    if({{$page_wise_setting_data?$page_wise_setting_data->last_insert_data_set:0}}==5){
        $('.under_add').val(localStorage.getItem("under_add"))
    }
</script>