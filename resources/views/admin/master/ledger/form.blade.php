 <!-- ledger add model  -->
 @component('components.modal', [
    'id'    => 'AddLedgerModel',
    'class' => 'modal fade',
    'size' => 'modal-xl',
    'form_id' => 'add_ledger_form',
    'method'=> 'POST',
])
    @slot('title', 'Add New Ledger')
    @slot('body')
        <div class="row">
            <div class="form-group col-lg-6">
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Ledger Name :</label>
                        <input type="text" name="ledger_name" class="form-control form-control-lg" placeholder="Enter Ledger Name" required>
                        <span id='error_ledger_name' class=" text-danger"></span>
                </div>
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Alias :</label>
                        <input type="text" name="alias" class="form-control form-control-lg" placeholder="Enter Alias" >
                </div>
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Under Group :</label>
                        <select name="group_id" id="group_id" class="form-control  js-example-basic-single_1  group_id left-data" required>
                            <option value="0">Select</option>
                            {!!html_entity_decode($group_chart_id)!!}
                        </select>
                </div>
                <div class="row">
                    <div class="form-group ">
                        <label  for="exampleInputEmail1">Unit/Branch :</label>
                            <select name="unit_or_branch"  class="form-control  js-example-basic-single_2 unit_or_branch" required>
                                <option value="0">--Select--</option>
                                @foreach (unit_branch() as $branch)
                                <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                @endforeach
                            </select>
                    </div>
                </div>
                <div class="border border-dark m-1">
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">Nature of Activities:</label>
                        <select name="nature_activity" id="nature_activity" class="form-control  js-example-basic-single_3  nature_activity left-data" >
                            <option value="Not Selected">Not Selected</option>
                            <option value="Operating">Operating</option>
                            <option value="Investing">Investing</option>
                            <option value="Financing">Financing</option>
                        </select>
                    </div>
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">Inventory Value Affected ? </label>
                            <select name="inventory_value" id="inventory_value" class="form-control  js-example-basic-single_4  inventory_value " >
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                    </div>
                </div>
                <div class="border border-success m-1 ">
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">Starting Balance :</label>
                            <input type="number" name="opening_balance" class="form-control form-control-lg" placeholder="Enter Starting Balance">
                    </div>
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">Dr/Cr : </label>
                            <select name="DrCr" id="DrCr" class="form-control  js-example-basic-single_5  DrCr " >
                                <option value="Dr">Dr</option>
                                <option value="Cr">Cr</option>
                            </select>
                    </div>
                </div>
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Credit Limit :</label>
                        <input type="number" name="credit_limit" class="form-control form-control-lg" placeholder="Enter Credit Limit" >
                </div>
            </div>
            <div class="form-group col-lg-6 ">
                <div class="border border-success">
                    <div class="form-group m-2 ">
                        <label  for="exampleInputEmail1">Mailing Name : </label>
                            <input type="text" name="mailing_name" class="form-control form-control-lg" placeholder="Enter Mailing mailing_name" >
                    </div>
                    <div class="form-group m-2 ">
                        <label  for="exampleInputEmail1">Mobile : </label>
                            <input type="text" name="mobile" class="form-control form-control-lg" placeholder="Enter Mobile">
                    </div>
                    <div class="form-group m-2 ">
                        <label  for="exampleInputEmail1">Mailing Address : </label>
                        <textarea name="mailing_add" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">National ID : </label>
                            <input type="text" name="national_id" class="form-control form-control-lg" placeholder="Enter National ID">

                    </div>
                    <div class="form-group m-2 ">
                        <label  for="exampleInputEmail1">Trade Licence No :</label>
                            <input type="text" name="trade_licence_no" class="form-control form-control-lg" placeholder="Enter Trade Licence No">
                    </div>
                </div>
            </div>
        </div>
    @endslot
    @slot('footer')
        <button type="button" class="btn btn-danger " data-dismiss="modal">Close</button>
        <button type="submit" id="add_ledger_btn" class="btn btn-primary">Add</button>
    @endslot
@endcomponent

 <!-- ledger edit model  -->
 @component('components.modal', [
    'id'    => 'EditLedgerModel',
    'class' => 'modal fade',
    'size' => 'modal-xl',
    'form_id' => 'edit_ledger_form',
    'method'=> 'PUT',
])
    @slot('title', 'Update New Ledger')
    @slot('body')
        <div class="row">
            <div class="form-group col-lg-6">
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Ledger Name :</label>
                        <input type="hidden" class="id" >
                        <input type="text" name="ledger_name" class="form-control form-control-lg ledger_name" placeholder="Enter Ledger Name" required>
                        <span id='error_ledger_name' class=" text-danger"></span>
                </div>
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Alias :</label>
                        <input type="text" name="alias" class="form-control form-control-lg alias" placeholder="Enter Alias" >
                </div>
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Under Group :</label>
                        <select name="group_id" id="group_id" class="form-control  js-example-basic_1  group_id left-data" required>
                            <option value="0">Select</option>
                            {!!html_entity_decode($group_chart_id)!!}
                        </select>
                </div>
                <div class="row">
                    <div class="form-group ">
                        <label  for="exampleInputEmail1">Unit/Branch :</label>
                            <select name="unit_or_branch"  class="form-control  unit_or_branch" required>
                                <option value="0">--Select--</option>
                                @foreach (unit_branch() as $branch)
                                <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                @endforeach
                            </select>
                    </div>
                </div>
                <div class="border border-dark m-1">
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">Nature of Activities:</label>
                        <select name="nature_activity" id="nature_activity" class="form-control    nature_activity left-data" >
                            <option value="Not Selected">Not Selected</option>
                            <option value="Operating">Operating</option>
                            <option value="Investing">Investing</option>
                            <option value="Financing">Financing</option>
                        </select>
                    </div>
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">Inventory Value Affected ? </label>
                            <select name="inventory_value" id="inventory_value" class="form-control   inventory_value left-data" >
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                    </div>
                </div>
                <div class="border border-success m-1 ">
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">Starting Balance :</label>
                            <input type="number" name="opening_balance" class="form-control form-control-lg opening_balance" placeholder="Enter Starting Balance">
                    </div>
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">Dr/Cr : </label>
                            <select name="DrCr" id="DrCr" class="form-control   DrCr left-data" >
                                <option value="Dr">Dr</option>
                                <option value="Cr">Cr</option>
                            </select>
                    </div>
                </div>
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Credit Limit :</label>
                        <input type="number" name="credit_limit" class="form-control form-control-lg credit_limit" placeholder="Enter Credit Limit"   >
                </div>
            </div>
            <div class="form-group col-lg-6 ">
                <div class="border border-success">
                    <div class="form-group m-2 ">
                        <label  for="exampleInputEmail1">Mailing Name : </label>
                            <input type="text" name="mailing_name" class="form-control form-control-lg mailing_name" placeholder="Enter Mailing mailing_name" >
                    </div>
                    <div class="form-group m-2 ">
                        <label  for="exampleInputEmail1">Mobile : </label>
                            <input type="text" name="mobile" class="form-control form-control-lg mobile" placeholder="Enter Mobile">
                    </div>
                    <div class="form-group m-2 ">
                        <label  for="exampleInputEmail1">Mailing Address : </label>
                        <textarea name="mailing_add" class="form-control  mailing_add" id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">National ID : </label>
                            <input type="text" name="national_id" class="form-control form-control-lg national_id" placeholder="Enter National ID">

                    </div>
                    <div class="form-group m-2 ">
                        <label  for="exampleInputEmail1">Trade Licence No :</label>
                            <input type="text" name="trade_licence_no" class="form-control form-control-lg trade_licence_no" placeholder="Enter Trade Licence No">
                    </div>
                </div>
            </div>
        </div>
    @endslot
    @slot('footer')
        <button type="submit" id="edit_ledger_btn" class="btn btn-primary">Update</button>
        <button  type="button" class="btn btn-danger deleteIcon"  data-dismiss="modal">Delete</button>
        <button type="button" class="btn btn-danger " data-dismiss="modal">Close</button>
    @endslot
@endcomponent
