@extends('layouts.backend.app')
@section('title','Dashboard User')
@push('css')

<style>
  input[type=radio] {
    width: 25px;
    height: 25px;
}
input[type=checkbox] {
    width: 20px;
    height: 20px;
}
input[type="text"]	{ 	height: 25px;	width:50px;	}
	input[value="No"]		{	color: #bbb;	font-weight:300; }
	input[value="Yes"]		{	color: orange;	font-weight:900; }
	.allow_box	{
		border:1px solid red;
		margin-left:25px;
		margin-right:100px;	}
</style>
@endpush
@section('admin_content')<br>
@component('components.create', [
    'title'=> 'User Privilege [Add/Edit]',
    'help_route'=>route('user-dashboard'),
    'close_route'=>route('user-dashboard'),
    'veiw_route'=>route('user-list-show'),
    'form_id' => 'add_user_privilege_form',
    'method'=> 'POST',

])
    @slot('body')
        <h5 style="margin-left:10px; font-weight: bold;">User Name :{{$get_user_data->user_name}}</h5>
        <div class="m-2" style="border:1px  solid;">
            <h5 class="m-2">
                Want to allow voucher entry at  privious date?<br>
                পূর্বে তারিখে voucher entry করার অনুমতি দিতে চান ?
            </h5>
            <div class="form-group m-3">
                <div class="row">
                    @php
                        $privous_insert_update=user_privileges_insert_update($get_user_data->id,'insert',1);
                    @endphp
                    <div class="col-md-6">
                        <input type="hidden" class="p_financial_year_start" value="{{company()->financial_year_start}}">
                        <input type="hidden" class="p_financial_year_end" value="{{company()->financial_year_end}}">
                        <input type="hidden" name="p_id" value="{{ $privous_insert_update?($privous_insert_update->id):''}}">
                        <input class="form-check-input" type="radio"  name="p_create_or_update"  value="1"  {{$privous_insert_update ? ($privous_insert_update->create_or_update =1 ? 'checked':''):'' }}>
                        <label class="form-check-label fs-6" for="flexRadioDefault1">
                            Yes
                        </label>
                        <div class="w3-row" style="margin-left:35px;">
                            <div class="w3-rest">
                                <i class="w3-small">
                                    Can entry a voucher at privious date.<br>
                                    পূর্বে তারিখে voucher entry করতে পারবে।
                                </i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <input class="form-check-input allow_date" type="radio"  name="p_allow_date" value="1"  {{$privous_insert_update ? ($privous_insert_update->allow =1 ? 'checked':''):'' }} >
                        <label class="form-check-label fs-6" for="flexRadioDefault1">
                            allow till specific date
                        </label>
                        <div class="w3-row" style="margin-left:35px;">
                            <div class="w3-rest">
                                <input class="w3-input specific_date" type="text" style="width: 150px;"
                                    name="p_specific_date" id="datepicker" value="{{$privous_insert_update?($privous_insert_update->specific_date ? $privous_insert_update->specific_date: date('Y-m-d')):''}}"  {{$privous_insert_update ? ($privous_insert_update->allow =1 ? '':'isabled'):'disabled'}}/><br>
                                <i class="w3-small">
                                    Can entry a voucher on and after this date.<br>
                                    এই তারিখে এবং এই তারিখের পরে voucher entry করতে পারবে।
                                </i>
                            </div>
                        </div>
                    </div>
               </div>
               <div class="row">
                    <div class="col-md-6">
                        <input class="form-check-input allow_number" type="radio"  name="p_allow_date" {{$privous_insert_update ? ($privous_insert_update->allow =0 ? 'checked':''):'' }} value="0" >
                        <label class="form-check-label fs-6" for="flexRadioDefault1">
                            allow till specific number of day
                        </label>
                        <div class="w3-row" style="margin-left:35px;">
                            <div class="w3-rest">
                                <input class="w3-input p_number" type="text" style="width: 150px;"
                                    name="p_number" id="allow_days" value="{{$privous_insert_update?($privous_insert_update->number ? $privous_insert_update->number:''):''}}" {{$privous_insert_update ? ($privous_insert_update->allow =0 ? '':'disabled'):'disabled' }}  /><br>
                                <i class="w3-small" style="width:70px; ">
                                    Suppose, allowed for 3 days, and today is <?php echo date('Y-M-d'); ?>. That means,
                                    can entry a voucher from <b><?php date('Y-m-d')?></b>
                                    to 3 days ago.<br/>
                                </i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" style="margin-top:10px;">
                        <input class="form-check-input w3-radio" type="radio"  name="p_create_or_update" value="0" {{$privous_insert_update ? ($privous_insert_update->create_or_update = 0 ?'checked':''):'' }} >
                        <label class="form-check-label fs-5" for="flexRadioDefault1" >
                            NO
                        </label>
                        <div class="w3-row" style="margin-left:35px;">
                            <div class="w3-rest">
                                <i class="w3-small">
                                    Cannot make any voucher entry at privious date.<br>
                                    পিছনের তারিখে কোনো voucher entry করতে পারবে না।
                                </i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @php
            $future_insert_update=user_privileges_insert_update($get_user_data->id,'insert',2);
           @endphp
            <h5 class="m-2">
                Want to allow voucher entry at  future date?<br>
                সামনে তারিখে voucher entry করার অনুমতি দিতে চান ?
            </h5>
            <div class="form-group m-3">

                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" name="f_id" value="{{$future_insert_update?($future_insert_update->id):''}}">
                        <input class="form-check-input" type="radio"  name="f_create_or_update"  value="2" {{$future_insert_update ? ($future_insert_update->create_or_update=2 ? 'checked':''):'' }}  >
                        <label class="form-check-label fs-6" for="flexRadioDefault1">
                            Yes
                        </label>
                        <div class="w3-row" style="margin-left:35px;">
                            <div class="w3-rest">
                                <i class="w3-small">
                                    Can entry a voucher at feture date.<br>
                                    সামনে তারিখে voucher entry করতে পারবে।
                                </i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" >
                        <input class="form-check-input f_allow_date" type="radio"  name="f_allow_date" value="1" {{$future_insert_update ? ($future_insert_update->allow =1 ? 'checked':''):'' }}   >
                        <label class="form-check-label fs-6" for="flexRadioDefault1">
                            allow till specific date
                        </label>
                        <div class="w3-row" style="margin-left:35px;">
                            <div class="w3-rest">
                                <input class="w3-input f_specific_date" type="text" style="width: 150px;"
                                    name="f_specific_date"  id="datepicker2" value="{{$future_insert_update?($future_insert_update->specific_date ? $future_insert_update->specific_date:''):''}}" {{$future_insert_update ? ($future_insert_update->allow = 1 ? '':'disabled'):'disabled' }} /><br>
                                <i class="w3-small">
                                    Can entry a voucher on and after this date.<br>
                                    এই তারিখে এবং এই তারিখের পরে voucher entry করতে পারবে।
                                </i>
                            </div>
                        </div>
                    </div>
               </div>
               <div class="row">
                    <div class="col-md-6">
                        <input class="form-check-inpu f_allow_number" type="radio"  name="f_allow_date" value="0" {{$future_insert_update ? ($future_insert_update->allow =0 ? 'checked':''):'' }}>
                        <label class="form-check-label fs-6" for="flexRadioDefault1">
                            allow till specific number of day
                        </label>
                        <div class="w3-row" style="margin-left:35px;">
                            <div class="w3-rest">
                                <input class="w3-input f_numder" type="text" style="width: 150px;"
                                    name="f_numder" id="allow_days" value="{{$future_insert_update?($future_insert_update->number ? $future_insert_update->number:''):''}}" {{$future_insert_update ? ($future_insert_update->allow = 0 ? '':'disabled'):'disabled' }} /><br>
                                <i class="w3-small" style="width:70px; ">
                                    Suppose, allowed for 3 days, and today is <?php echo date('Y-M-d'); ?>. That means,
                                    can entry a voucher from <b><?php date('Y-m-d')?></b>
                                    to next 3 days .<br/>
                                </i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6"  style="margin-top:10px;">
                        <input class="form-check-input w3-radio" type="radio"  name="f_create_or_update" value="0"  {{$future_insert_update ? ($future_insert_update->create_or_update=0 ? 'checked':''):'' }}>
                        <label class="form-check-label fs-5" for="flexRadioDefault1" >
                            NO
                        </label>
                        <div class="w3-row" style="margin-left:35px;">
                            <div class="w3-rest">
                                <i class="w3-small">
                                    Cannot make any voucher entry at feture date.<br>
                                    সামনে তারিখে কোনো voucher entry করতে পারবে না।
                                </i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         @php
         $insert_update=user_privileges_insert_update($get_user_data->id,'update',0);
        @endphp
      <div class="m-2" style="border:1px  solid;">
          <h5 class="m-2">
               Allow to modify any back deletd voucher ?<br>
              পিছনের তারিখে voucher modify করার অনুমতি দিতে চান ?
          </h5>
          <div class="form-group m-3">
              <input type="hidden" name="f_id_update" value="{{$insert_update?($insert_update->id):''}}">
              <input class="form-check-input w3-radio" type="radio"  name="m_create_or_update" value="0"  {{$insert_update ? ($insert_update->create_or_update==0 ? 'checked':''):'' }} checked="checked">
              <label class="form-check-label fs-5" for="flexRadioDefault1" >
                 NO
              </label>
              <div class="w3-row" style="margin-left:35px;">
                  <div class="w3-rest">
                      <i class="w3-small">
                          Cannot modify any back deletd voucher<br>
                          পিছনের তারিখে কোনো voucher modify করতে পারবে না।
                      </i>
                  </div>
              </div>
              <input class="form-check-input" type="radio"  name="m_create_or_update" value="1"{{$insert_update ? ($insert_update->create_or_update ==1 ? 'checked':''):'' }}  >
              <label class="form-check-label fs-6" for="flexRadioDefault1">
                  Yes
              </label>
              <div class="w3-row" style="margin-left:35px;">
                  <div class="w3-rest">
                      <i class="w3-small">
                          Can modify  voucher of any date<br>
                          যে কোনো তারিখে voucher modify করতে পারবে।
                      </i>
                  </div>
              </div>
              <input class="form-check-input" type="radio"   name="m_create_or_update" value="2" {{$insert_update ? ($insert_update->create_or_update ==2 ? 'checked':''):'' }}>
              <label class="form-check-label fs-6" for="flexRadioDefault1">
                  allow till specific date
              </label>
              <div class="w3-row" style="margin-left:35px;">
                  <div class="w3-rest">
                      <input class="w3-input setup_date" type="text" style="width: 150px;" value="{{$insert_update?($insert_update->specific_date):''}}"
                          name="m_specific_date" id="datepicker3"  /><br>
                      <i class="w3-small">
                          Can modify  voucher for this date and beyond but cannot modify any voucher  before this date.<br>
                          এই তারিখে এবং এই তারিখের পরে voucher entry করতে পারবে, তবে এই তারিখের আগে কোনও তারিখে পারবে না।
                      </i>
                  </div>
              </div>
              <input class="form-check-input" type="radio"   name="m_create_or_update" value="3" {{$insert_update ? ($insert_update->create_or_update ==3 ? 'checked':''):'' }}>
              <label class="form-check-label fs-6" for="flexRadioDefault1">
                  allow till specific number of day
              </label>
              <div class="w3-row" style="margin-left:35px;">
                  <div class="w3-rest">
                      <input class="w3-input" type="text" style="width: 150px;" value="{{$insert_update?($insert_update->number):''}}"
                          name="m_number" id="allow_days"  /><br>
                      <i class="w3-small">
                          Suppose, allowed for 3 days, and today is <?php echo date('Y-M-d'); ?>. That means,
                          can entry a voucher from <b><?php date('Y-m-d')?></b>
                          to any future date.<br/>
                          Allowed for 3 days (<?php

                          ?>), and today (<?php echo date('Y-M-d'); ?>), and any future date from today.
                      </i>
                  </div>
              </div>
          </div>
      </div>
        <div class="m-2" style="border:1px  solid;">
            <h5 class="m-2" >
                Tab Settings for user
            </h5>
            <div class="form-group m-3">
                    <div class="row">
                        @php
                         $master=user_privileges_role($get_user_data->id,'menu','tab_master_');

                        @endphp
                        <div class="col-md-1" style="width:160px; padding-top: 7px;">
                            Masters

                        </div>
                        <div  class="col-md-4" style=" padding-top: 7px;">
                            <input type="hidden" name="m_privilege_id" value="{{$master->privileges_id ?? ''}}"/>
                            <input type="hidden" name="m_status" value="menu"/>
                            <input type="hidden" name="m_title" value="tab_master_"/>
                            <input type="radio" name="m_create_role" value="0"  {{ $master ? ($master->create_role==0 ? 'checked' : ''):'' }}  checked>
                            <label class="form-check-label" style="margin-bottom: 5px;">Don't allow</label>&nbsp;&nbsp;&nbsp;
                            <input  type="radio" name="m_create_role" value="1" {{ $master ? ($master->create_role==1 ? 'checked' : ''): ''}}   >
                            <label class="form-check-label ">Allow</label>
                        </div>
                     </div>
                     <div class="row">
                        @php
                          $vouchers=user_privileges_role($get_user_data->id,'menu','tab_voucher');
                        @endphp
                        <div class="col-md-1" style="width:160px; padding-top: 7px;">
                            Vouchers
                        </div>

                        <div  class="col-md-4" style=" padding-top: 7px;">
                            <input type="hidden" name="v_privilege_id" value="{{$vouchers->privileges_id ?? ''}}"/>
                            <input type="hidden" name="v_status" value="menu"/>
                            <input type="hidden" name="v_title" value="tab_voucher"/>

                            <input type="radio" name="v_create_role" value="0" {{$vouchers?($vouchers->create_role==0 ? 'checked' : ''):''}} checked>
                            <label class="form-check-label" style="margin-bottom: 5px;">Don't allow</label>&nbsp;&nbsp;&nbsp;
                            <input  type="radio" name="v_create_role" value="1" {{ $vouchers? ($vouchers->create_role==1 ? 'checked' : '') :''}}  >
                            <label class="form-check-label ">Allow</label>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-1" style="width:160px; padding-top: 7px;">
                           @php
                              $order_sheet=user_privileges_role($get_user_data->id,'menu','tab_o_sheet');
                           @endphp
                            Order Sheet
                        </div>
                        <div  class="col-md-4" style=" padding-top: 7px;">
                            <input type="hidden" name="o_privilege_id" value="{{$order_sheet->privileges_id ?? ''}}"/>
                            <input type="hidden" name="o_status" value="menu"/>
                            <input type="hidden" name="o_title" value="tab_o_sheet"/>
                            <input type="radio" name="o_create_role" value="0" {{ $order_sheet ? ($order_sheet->create_role==0 ? 'checked' : ''):''}} checked>
                            <label class="form-check-label" style="margin-bottom: 5px;">Don't allow</label>&nbsp;&nbsp;&nbsp;
                            <input  type="radio" name="o_create_role" value="1" {{ $order_sheet ? ($order_sheet->create_role==1 ? 'checked' : ''):''}} >
                            <label class="form-check-label">Allow</label>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-1" style="width:160px; padding-top: 7px;">
                            @php
                              $reports=user_privileges_role($get_user_data->id,'menu','tab_report_');
                            @endphp
                            Reports
                        </div>
                        <div  class="col-md-4" style=" padding-top: 7px;">
                            <input type="hidden" name="user_id" value="{{$get_user_data->id}}"/>
                            <input type="hidden" name="r_privilege_id" value="{{$reports->privileges_id ?? ''}}"/>
                            <input type="hidden" name="r_status" value="menu"/>
                            <input type="hidden" name="r_title" value="tab_report_"/>
                            <input type="radio" name="r_create_role" value="0" {{$reports? ($reports->create_role==0 ? 'checked' : ''):''}} checked>
                            <label class="form-check-label" style="margin-bottom: 5px;">Don't allow</label>&nbsp;&nbsp;&nbsp;
                            <input  type="radio" name="r_create_role" value="1" {{ $reports? ($reports->create_role==1 ? 'checked' : ''): ''}} >
                            <label class="form-check-label ">Allow</label>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-1" style="width:160px; padding-top: 7px;">
                            @php
                              $company=user_privileges_role($get_user_data->id,'menu','tab_company');
                            @endphp
                            Company
                        </div>
                        <div  class="col-md-4" style=" padding-top: 7px;">
                            <input type="hidden" name="c_privilege_id" value="{{$company->privileges_id ?? ''}}"/>
                            <input type="hidden" name="c_status" value="menu"/>
                            <input type="hidden" name="c_title" value="tab_company"/>
                            <input type="radio" name="c_create_role" value="0" {{$company? ($company->create_role==0 ? 'checked' : ''):''}} checked>
                            <label class="form-check-label" style="margin-bottom: 5px;">Don't allow</label>&nbsp;&nbsp;&nbsp;
                            <input  type="radio" name="c_create_role" value="1" {{$company? ($company->create_role==1 ? 'checked' : ''):''}} >
                            <label class="form-check-label ">Allow</label>
                        </div>
                     </div>
           </div>
        </div>
        <div class="table-responsive">
        <table class="table">
            <tr  style=" color: #000!important; background-color: #60c8f2!important; padding:10px; border-right: 1px solid #ddd;">
                <th colspan="2" style="font-size: 16px;padding-left: 16px;">Title</th>
                <th style=" border-right: 1px solid #ddd;"><input class="creat_all w3-check" type="checkbox" />Create</th>
                <th><input class="dsply_all w3-check" type="checkbox" />Display</th>
                <th><input class="alter_all w3-check"  type="checkbox" />Alter</th>
                <th><input class="delet_all w3-check" type="checkbox" />Delete</th>
                <th><input class="print_all w3-check" type="checkbox" />Print</th>
                <th style="max-width: 100px !important;"></th>
            </tr>
            <tr style="background-color: #f1f1f1">
                <td  style="font-size: 16px;"colspan="2"><b><i>Master-Accounts</i></b></td>
                <td><input class="creat_all_m w3-check" type="checkbox" />Create</td>
                <td><input class="dsply_all_m w3-check" type="checkbox" />Display</td>
                <td><input class="alter_all_m w3-check" type="checkbox" />Alter</td>
                <td><input class="delet_all_m w3-check" type="checkbox" />Delete</td>
                <td><input class="print_all_m w3-check" type="checkbox" />Print</td>
                <td style="max-width: 100px!important;"></td>
            </tr>
            @php
                $acc_group=user_privileges_role($get_user_data->id,'master','Group');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd;padding-left: 16px; width:7px;">1</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Group"/>
                    Accounts Group
                </td>
                 <input type="hidden" name="privilege_id[]" value="{{$acc_group->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{$acc_group ?($acc_group->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{$acc_group ?( $acc_group->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{$acc_group ?( $acc_group->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{$acc_group ?( $acc_group->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{$acc_group ?( $acc_group->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
                $acc_ledger=user_privileges_role($get_user_data->id,'master','Ledger');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd;padding-left:16px;"> 2</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Ledger"/>
                    Accounts Ledger
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$acc_ledger->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)"  value="{{$acc_ledger ?( $acc_ledger->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)"  value="{{$acc_ledger ?($acc_ledger->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)"  value="{{$acc_ledger ?( $acc_ledger->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)"  value="{{$acc_ledger ?($acc_ledger->delete_role==1?'Yes': 'No'): 'No'}}"  /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)"  value="{{$acc_ledger ? ($acc_ledger->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
              $acc_slase_target=user_privileges_role($get_user_data->id,'master','Sales Target');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd;padding-left: 16px; ">3</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Sales Target"/>
                    Accounts Ledger >> Sales Target
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$acc_slase_target->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)"  value="{{$acc_slase_target ?( $acc_slase_target->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)"  value="{{$acc_slase_target ?($acc_slase_target->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)"  value="{{$acc_slase_target ?( $acc_slase_target->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)"  value="{{$acc_slase_target ?($acc_slase_target->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)"  value="{{$acc_slase_target ? ($acc_slase_target->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
              $voucher_type=user_privileges_role($get_user_data->id,'master','Voucher Type');
            @endphp
            <tr class="table-row"  style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">4</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Voucher Type"/>
                    Accounts Voucher
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$voucher_type->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)"  value="{{$voucher_type ?( $voucher_type->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)"  value="{{$voucher_type ?($voucher_type->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)"  value="{{$voucher_type ?($voucher_type->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)"  value="{{$voucher_type ?($voucher_type->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)"  value="{{$voucher_type ? ($voucher_type->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
              $stock_group=user_privileges_role($get_user_data->id,'master','Stock Group');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">5</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Stock Group"/>
                    Stock Group
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$stock_group->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)"  value="{{$stock_group ?($stock_group->create_role==1?'Yes': 'No'): 'No'}}"  /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)"  value="{{$stock_group ?($stock_group->display_role==1?'Yes': 'No'): 'No'}}"  /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)"  value="{{$stock_group ?($stock_group->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)"  value="{{$stock_group ?($stock_group->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)"  value="{{$stock_group ? ($stock_group->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
             $selling_price=user_privileges_role($get_user_data->id,'master','Group Selling Price');
            @endphp
            <tr class="table-row"  style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">6 </td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Group Selling Price"/>
                    Stock Group >> Selling Price
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $selling_price->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{$selling_price ?($selling_price->create_role==1?'Yes': 'No'): 'No'}}"/></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{$selling_price ?($selling_price->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{$selling_price ?( $selling_price->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{$selling_price ?($selling_price->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{$selling_price ? ($selling_price->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
             $standard_price=user_privileges_role($get_user_data->id,'master','Group Standard Price');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">7</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Group Standard Price"/>
                    Stock Group >> Standard Price
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $standard_price->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{$standard_price ?($standard_price->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{$standard_price ?($standard_price->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{$standard_price ?( $standard_price->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{$standard_price ?( $standard_price->delete_role==1?'Yes': 'No'): 'No'}}"  /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{$standard_price ? ($standard_price->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
             $wholesale_price=user_privileges_role($get_user_data->id,'master','Group Wholesale Price');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">8</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Group Wholesale Price"/>
                    Stock Group >> Wholesale Price
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $wholesale_price->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{$wholesale_price ?($wholesale_price->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{$wholesale_price ?($wholesale_price->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{$wholesale_price ?($wholesale_price->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{$wholesale_price ?($wholesale_price->delete_role==1?'Yes': 'No'): 'No'}}"  /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{$wholesale_price ? ($wholesale_price->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
               $stock_group_commision=user_privileges_role($get_user_data->id,'master','stock_group__commission');
             @endphp
            <tr class="table-row">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">9</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="stock_group__commission"/>
                    Stock Group >> Commission
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$stock_group_commision->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{$stock_group_commision ?($stock_group_commision->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{$stock_group_commision ?($stock_group_commision->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{$stock_group_commision ?($stock_group_commision->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{$stock_group_commision ?($stock_group_commision->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{$stock_group_commision ? ($stock_group_commision->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
             @php
               $stock_item=user_privileges_role($get_user_data->id,'master','Stock Item');
             @endphp
            <tr class="table-row"  style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">10</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Stock Item"/>
                    Stock Item
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $stock_item->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{$stock_item ?($stock_item->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{$stock_item ?($stock_item->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{$stock_item ?($stock_item->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{$stock_item ?($stock_item->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{$stock_item ? ($stock_item->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr >
            @php
               $opening_balance=user_privileges_role($get_user_data->id,'master','stock_item__opening_balance');
            @endphp
            <tr  class="table-row">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">11</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="stock_item__opening_balance"/>
                    Stock Item >> Opening Balance
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $opening_balance->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{$opening_balance ?($opening_balance->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{$opening_balance ?($opening_balance->display_role==1?'Yes': 'No'): 'No'}}"/></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{$opening_balance ?($opening_balance->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{$opening_balance ?($opening_balance->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{$opening_balance ? ($opening_balance->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
              $stock_item_selling_price=user_privileges_role($get_user_data->id,'master','Selling Price');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">13</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Selling Price"/>
                    Stock Item >> Selling Price
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$stock_item_selling_price->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $stock_item_selling_price ?( $stock_item_selling_price->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $stock_item_selling_price ?( $stock_item_selling_price->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $stock_item_selling_price ?( $stock_item_selling_price->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $stock_item_selling_price ?( $stock_item_selling_price->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $stock_item_selling_price ? ($stock_item_selling_price->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
              $standard_selling_price=user_privileges_role($get_user_data->id,'master','Standard Price');
            @endphp
            <tr class="table-row"  style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">14</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Standard Price"/>
                    Stock Item >> Standard Price
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$standard_selling_price->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $standard_selling_price ?(  $standard_selling_price->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $standard_selling_price ?( $standard_selling_price->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $standard_selling_price ?( $standard_selling_price->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $standard_selling_price ?( $standard_selling_price->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $standard_selling_price ? ( $standard_selling_price->print_role==1?'Yes': 'No'): 'No'}}"  /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
             $item_wholesale_price=user_privileges_role($get_user_data->id,'master','Wholesale Price');
            @endphp
            <tr class="table-row">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">15</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Wholesale Price"/>
                    Stock Item >> Wholesale Price
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$item_wholesale_price->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $item_wholesale_price ?( $item_wholesale_price->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $item_wholesale_price ?( $item_wholesale_price->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $item_wholesale_price ?( $item_wholesale_price->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $item_wholesale_price ?( $item_wholesale_price->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $item_wholesale_price ? ( $item_wholesale_price->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
              $stock_item__commission=user_privileges_role($get_user_data->id,'master','stock_item__commission');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">16</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="stock_item__commission"/>
                    Stock Item >> Commission
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$stock_item__commission->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $stock_item__commission ?( $stock_item__commission->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $stock_item__commission ?( $stock_item__commission->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $stock_item__commission ?( $stock_item__commission->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $stock_item__commission ?( $stock_item__commission->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $stock_item__commission ? ( $stock_item__commission->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
              $unit_of_size=user_privileges_role($get_user_data->id,'master','Unit of Size');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">17</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Unit of Size"/>
                    Stock Item >> Unit of Size
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$unit_of_size->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $unit_of_size ?( $unit_of_size->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $unit_of_size ?( $unit_of_size->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $unit_of_size ?( $unit_of_size->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $unit_of_size ?( $unit_of_size->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $unit_of_size ? ( $unit_of_size->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
              $unit_of_measure=user_privileges_role($get_user_data->id,'master','Units of Measure');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">18</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Units of Measure"/>
                    Stock Item >> Units of Measure
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$unit_of_measure->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $unit_of_measure ?($unit_of_measure->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $unit_of_measure ?( $unit_of_measure->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $unit_of_measure ?($unit_of_measure->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $unit_of_measure ?($unit_of_measure->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $unit_of_measure ? ( $unit_of_measure->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
              $components=user_privileges_role($get_user_data->id,'master','Components');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">21</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Components"/>
                    Components
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $components->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $components ?($components->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $components ?( $components->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $components ?( $components->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $components ?($components->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $components ? ( $components->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
             $unit_of_measure=user_privileges_role($get_user_data->id,'master','bill_of_material');
            @endphp
            <tr class="table-row"  style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">22</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="bill_of_material"/>
                    Bill of Material
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $unit_of_measure->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $unit_of_measure ?( $unit_of_measure->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $unit_of_measure ?( $unit_of_measure->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $unit_of_measure ?( $unit_of_measure->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $unit_of_measure ?($unit_of_measure->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $unit_of_measure ? ( $unit_of_measure->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
             $godown=user_privileges_role($get_user_data->id,'master','Godown');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">24</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Godown"/>
                    Godown
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $godown->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)"  value="{{ $godown ?( $godown->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)"  value="{{ $godown ?( $godown->display_role==1?'Yes': 'No'): 'No'}}"/></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)"  value="{{ $godown ?( $godown->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)"  value="{{ $godown ?($godown->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)"  value="{{ $godown ? ( $godown->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
             $distribution_center=user_privileges_role($get_user_data->id,'master','Distribution Center');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">25</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="Distribution Center"/>
                    Distribution Center
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $distribution_center->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $distribution_center ?( $distribution_center->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $distribution_center ?( $distribution_center->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $distribution_center ?( $distribution_center->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $distribution_center ?($distribution_center->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $distribution_center ? ( $distribution_center->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
             $pos_discount=user_privileges_role($get_user_data->id,'master','POS Discount');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">26</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="POS Discount"/>
                    POS Discount
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $pos_discount->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $pos_discount ?( $pos_discount->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $pos_discount ?( $pos_discount->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $pos_discount ?( $pos_discount->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $pos_discount ?($pos_discount->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $pos_discount ? ( $pos_discount->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
           @php
            $customer=user_privileges_role($get_user_data->id,'master','Customer');
           @endphp
           <tr class="table-row"  style="background-color: #f1f1f1">
               <td style=" border-right: 1px solid #ddd;padding-left: 16px;">27</td>
               <td style=" border-right: 1px solid #ddd;">
                   <input type="hidden" name="status[]" value="master"/>
                   <input type="hidden" name="title[]" value="Customer"/>
                   Customer
               </td>
               <input type="hidden" name="privilege_id[]" value="{{ $customer->privileges_id ?? ''}}"/>
               <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $customer ?($customer->create_role==1?'Yes': 'No'): 'No'}}" /></td>
               <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $customer ?($customer->display_role==1?'Yes': 'No'): 'No'}}" /></td>
               <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $customer ?( $customer->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
               <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $customer ?($customer->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
               <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $customer ?($customer->print_role==1?'Yes': 'No'): 'No'}}" /></td>
               <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
           </tr>
           @php
            $supplier=user_privileges_role($get_user_data->id,'master','Supplier');
           @endphp
           <tr class="table-row" >
               <td style=" border-right: 1px solid #ddd;padding-left: 16px;">28</td>
               <td style=" border-right: 1px solid #ddd;">
                   <input type="hidden" name="status[]" value="master"/>
                   <input type="hidden" name="title[]" value="Supplier"/>
                   Supplier
               </td>
               <input type="hidden" name="privilege_id[]" value="{{ $supplier->privileges_id ?? ''}}"/>
               <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $supplier ?($supplier->create_role==1?'Yes': 'No'): 'No'}}" /></td>
               <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $supplier ?($supplier->display_role==1?'Yes': 'No'): 'No'}}" /></td>
               <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $supplier ?($supplier->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
               <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $supplier ?($supplier->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
               <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $supplier ?($supplier->print_role==1?'Yes': 'No'): 'No'}}" /></td>
               <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
           </tr>
          @php
           $material=user_privileges_role($get_user_data->id,'master','Material');
          @endphp
          <tr class="table-row"  style="background-color: #f1f1f1">
              <td style=" border-right: 1px solid #ddd;padding-left: 16px;">29</td>
              <td style=" border-right: 1px solid #ddd;">
                  <input type="hidden" name="status[]" value="master"/>
                  <input type="hidden" name="title[]" value="Material"/>
                  Bill of Material
              </td>
              <input type="hidden" name="privilege_id[]" value="{{ $material->privileges_id ?? ''}}"/>
              <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $material ?($material->create_role==1?'Yes': 'No'): 'No'}}" /></td>
              <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $material ?($material->display_role==1?'Yes': 'No'): 'No'}}" /></td>
              <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $material ?($material->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
              <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $material ?($material->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
              <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $material ?($material->print_role==1?'Yes': 'No'): 'No'}}" /></td>
              <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
          </tr>
         @php
          $group_price=user_privileges_role($get_user_data->id,'master','Group Price');
         @endphp
         <tr class="table-row" >
             <td style=" border-right: 1px solid #ddd;padding-left: 16px;">30</td>
             <td style=" border-right: 1px solid #ddd;">
                 <input type="hidden" name="status[]" value="master"/>
                 <input type="hidden" name="title[]" value="Group Price"/>
                  Stock Group Price
             </td>
             <input type="hidden" name="privilege_id[]" value="{{ $group_price->privileges_id ?? ''}}"/>
             <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $group_price ?($group_price->create_role==1?'Yes': 'No'): 'No'}}" /></td>
             <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $group_price ?($group_price->display_role==1?'Yes': 'No'): 'No'}}" /></td>
             <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $group_price ?($group_price->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
             <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $group_price ?($group_price->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
             <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $group_price ?($group_price->print_role==1?'Yes': 'No'): 'No'}}" /></td>
             <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
         </tr>
         @php
          $group_price=user_privileges_role($get_user_data->id,'master','Group Price');
         @endphp
         <tr class="table-row"  style="background-color: #f1f1f1">
             <td style=" border-right: 1px solid #ddd;padding-left: 16px;">31</td>
             <td style=" border-right: 1px solid #ddd;">
                 <input type="hidden" name="status[]" value="master"/>
                 <input type="hidden" name="title[]" value="Group Price"/>
                  Stock Group Price
             </td>
             <input type="hidden" name="privilege_id[]" value="{{ $group_price->privileges_id ?? ''}}"/>
             <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $group_price ?($group_price->create_role==1?'Yes': 'No'): 'No'}}" /></td>
             <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $group_price ?($group_price->display_role==1?'Yes': 'No'): 'No'}}" /></td>
             <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $group_price ?($group_price->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
             <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $group_price ?($group_price->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
             <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $group_price ?($group_price->print_role==1?'Yes': 'No'): 'No'}}" /></td>
             <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
         </tr>
            @php
             $user=user_privileges_role($get_user_data->id,'master','User');
            @endphp
            <tr class="table-row">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">32</td>
                <td>
                    <input type="hidden" name="status[]" value="master"/>
                    <input type="hidden" name="title[]" value="User"/>
                    User
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$user->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $user ?( $user->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $user ?( $user->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $user ?( $user->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $user ?($user->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $user ? ($user->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
              $company_global=user_privileges_role($get_user_data->id,'Global Setup','company');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">33</td>
                <td>
                    <input type="hidden" name="status[]" value="Global Setup"/>
                    <input type="hidden" name="title[]" value="company"/>
                    Company
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $company_global->privileges_id ?? ''}}"/>
                <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $company_global ?( $company_global->create_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $company_global ?( $company_global->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $company_global ?( $company_global->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $company_global ?($company_global->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $company_global ? ($company_global->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
            @php
            $branch_global=user_privileges_role($get_user_data->id,'Global Setup','unit_or_branch');
            @endphp
          <tr class="table-row">
              <td style=" border-right: 1px solid #ddd;padding-left: 16px;">34</td>
              <td>
                  <input type="hidden" name="status[]" value="Global Setup"/>
                  <input type="hidden" name="title[]" value="unit_or_branch"/>
                  Unit/Branch
              </td>
              <input type="hidden" name="privilege_id[]" value="{{  $branch_global->privileges_id ?? ''}}"/>
              <td><input type="text" name="creat_[]" class="cre_m" onclick="change(this)" value="{{ $branch_global ?( $branch_global->create_role==1?'Yes': 'No'): 'No'}}" /></td>
              <td><input type="text" name="dsply_[]" class="dis_m" onclick="change(this)" value="{{ $branch_global ?( $branch_global->display_role==1?'Yes': 'No'): 'No'}}" /></td>
              <td><input type="text" name="alter_[]" class="alt_m" onclick="change(this)" value="{{ $branch_global ?( $branch_global->alter_role==1?'Yes': 'No'): 'No'}}" /></td>
              <td><input type="text" name="delet_[]" class="del_m" onclick="change(this)" value="{{ $branch_global ?( $branch_global->delete_role==1?'Yes': 'No'): 'No'}}" /></td>
              <td><input type="text" name="print_[]" class="pri_m" onclick="change(this)" value="{{ $branch_global ?( $branch_global->print_role==1?'Yes': 'No'): 'No'}}" /></td>
               <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
          </tr>
            <tr class="table-row">
                <td colspan="2"><b><i>Voucher List</i></b></td>
                <td><input class="creat_all_v w3-check" type="checkbox" />Create</td>
                <td><input class="dsply_all_v w3-check" type="checkbox" />Display</td>
                <td><input class="alter_all_v w3-check" type="checkbox" />Alter</td>
                <td><input class="delet_all_v w3-check" type="checkbox" />Delete</td>
                <td><input class="print_all_v w3-check" type="checkbox" />Print</td>
                <td style="max-width: 100px;"><input class="authorized_dsply_all_v w3-check" type="checkbox" />Authorized voucher  Display</td>
            </tr>

            @foreach ($vouchers_type as $key=>$voucher)
            @php
                $voucher_privlege= App\Models\UserPrivilege::where('title_details',$voucher->voucher_id)->where('table_user_id',$get_user_data->id)->first();
            @endphp
                @if(isset($voucher_privlege->title_details))
                    <tr class="table-row">
                        <td style=" border-right: 1px solid #ddd;">{{$key+1}}</td>
                            <input type="hidden" name="status[]" value="{{ $voucher_privlege->status_type}}" />
                            <input type="hidden" name="title[]" value="{{ $voucher_privlege->title_details }}"/>
                            <input type="hidden" name="privilege_id[]" value="{{$voucher_privlege->privileges_id ?? ''}}"/>
                        <td>{{ $voucher->voucher_name}}</td>
                        <td><input type="text" name="creat_[]" class="cre_v" onclick="change(this)" value="{{ $voucher_privlege->create_role==1 ? 'Yes':'No'}}" /></td>
                        <td><input type="text" name="dsply_[]" class="dis_v" onclick="change(this)" value="{{$voucher_privlege->display_role==1 ? 'Yes':'No'}}" /></td>
                        <td><input type="text" name="alter_[]" class="alt_v" onclick="change(this)" value="{{$voucher_privlege->alter_role==1 ? 'Yes':'No'}}" /></td>
                        <td><input type="text" name="delet_[]" class="del_v" onclick="change(this)" value="{{$voucher_privlege->delete_role==1 ? 'Yes':'No'}}" /></td>
                        <td><input type="text" name="print_[]" class="pri_v" onclick="change(this)" value="{{$voucher_privlege->print_role==1 ? 'Yes':'No'}}" /></td>
                        <td><input type="text" name="authorized_dsply_[]" class="authorized_dis_v" onclick="change(this)" value="{{$voucher_privlege->authorized_dsply==1 ? 'Yes':'No'}}" /></td>
                    </tr>
                @else
                <tr class="table-row">
                    <td style=" border-right: 1px solid #ddd;">{{$key+1}}</td>
                        <input type="hidden" name="status[]" value="Voucher" />
                        <input type="hidden" name="title[]" value="{{$voucher->voucher_id}}"/>
                    <td>{{$voucher->voucher_name}}</td>
                    <td><input type="text" name="creat_[]" class="cre_v" onclick="change(this)" value="No" /></td>
                    <td><input type="text" name="dsply_[]" class="dis_v" onclick="change(this)" value="No" /></td>
                    <td><input type="text" name="alter_[]" class="alt_v" onclick="change(this)" value="No" /></td>
                    <td><input type="text" name="delet_[]" class="del_v" onclick="change(this)" value="No" /></td>
                    <td><input type="text" name="print_[]" class="pri_v" onclick="change(this)" value="No" /></td>
                     <td><input type="text" name="authorized_dsply_[]" class="authorized_dis_v" onclick="change(this)" value="No" /></td>
            </tr>
                @endif
          @endforeach
          <tr style="background-color: #f1f1f1" class="table-row">
            <td  style="font-size: 16px;"colspan="2"><b><i>Report</i></b></td>
            <td><input class="creat_all_r w3-check" type="checkbox" />Create</td>
            <td><input class="dsply_all_r w3-check" type="checkbox" />Display</td>
            <td><input class="alter_all_r w3-check" type="checkbox" />Alter</td>
            <td><input class="delet_all_r w3-check" type="checkbox" />Delete</td>
            <td><input class="print_all_r w3-check" type="checkbox" />Print</td>
             <td  style="max-width: 120px!important;"></td>
        </tr>
        @if(in_array("Daybook",array_column($report_access, 'report_title')))
            @php
                $acc_daybook=user_privileges_role($get_user_data->id,'report','Daybook');
            @endphp
            <tr class="table-row">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px; width:7px;">1</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="Daybook"/>
                    Day Book
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$acc_daybook->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$acc_daybook ?($acc_daybook->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r " onclick="change(this)" value="{{$acc_daybook ?($acc_daybook->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("Challan",array_column($report_access, 'report_title')))
            @php
                $acc_challan=user_privileges_role($get_user_data->id,'report','Challan');
            @endphp
            <tr style="background-color: #f1f1f1" class="table-row">
                <td style=" border-right: 1px solid #ddd;padding-left:16px;"> 2</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="Challan"/>
                    Challan
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$acc_challan->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)"  value="{{$acc_challan ?($acc_challan->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)"  value="{{$acc_challan ?($acc_challan->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("Bill",array_column($report_access, 'report_title')))
            @php
            $acc_bill=user_privileges_role($get_user_data->id,'report','Bill');
            @endphp
            <tr class="table-row">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px; ">3</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="Bill"/>
                    Bill
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$acc_bill->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$acc_bill ?($acc_bill->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$acc_bill ?($acc_bill->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("invoiceSummary",array_column($report_access, 'report_title')))
            @php
            $acc_invoice_summary=user_privileges_role($get_user_data->id,'report','invoiceSummary');
            @endphp
            <tr class="table-row">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px; ">3</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="invoiceSummary"/>
                    Invoice Summary
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$acc_invoice_summary->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$acc_invoice_summary ?($acc_invoice_summary->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$acc_invoice_summary ?($acc_invoice_summary->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("PurchaseOrder",array_column($report_access, 'report_title')))
            @php
            $purchase_order=user_privileges_role($get_user_data->id,'report','PurchaseOrder');
            @endphp
            <tr class="table-row">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px; ">3</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="PurchaseOrder"/>
                    Purchase Order
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$purchase_order->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$purchase_order ?($purchase_order->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$purchase_order ?($purchase_order->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("GoodsinTransit",array_column($report_access, 'report_title')))
            @php
            $acc_goodsin_transit=user_privileges_role($get_user_data->id,'report','GoodsinTransit');
            @endphp
            <tr class="table-row">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px; ">3</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="GoodsinTransit"/>
                    Goods in Transit
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$acc_goodsin_transit->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$acc_goodsin_transit ?($acc_goodsin_transit->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$acc_goodsin_transit ?($acc_goodsin_transit->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("SalesOrder",array_column($report_access, 'report_title')))
            @php
            $acc_sales_order=user_privileges_role($get_user_data->id,'report','SalesOrder');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px; ">4</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="SalesOrder"/>
                    Sales Order
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$acc_sales_order->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$acc_sales_order ?($acc_sales_order->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$acc_sales_order ?($acc_sales_order->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("SalesOrderList",array_column($report_access, 'report_title')))
            @php
            $sales_order_list=user_privileges_role($get_user_data->id,'report','SalesOrderList');
            @endphp
            <tr class="table-row">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">5</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="SalesOrderList"/>
                    Sales Order List
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$sales_order_list->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$sales_order_list ?($sales_order_list->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$sales_order_list ?($sales_order_list->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("LedgerVoucherRegister",array_column($report_access, 'report_title')))
            @php
            $ledger_voucher_register=user_privileges_role($get_user_data->id,'report','LedgerVoucherRegister');
            @endphp
            <tr  style="background-color: #f1f1f1" class="table-row">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">6</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="LedgerVoucherRegister"/>
                        Ledger Voucher Register
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$ledger_voucher_register->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text"   name="dsply_[]" class="dis_r" onclick="change(this)"  value="{{$ledger_voucher_register ?($ledger_voucher_register->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No"/></td>
                <td><input type="hidden" name="delet_[]" value="No"/></td>
                <td><input type="text"   name="print_[]" class="pri_r" onclick="change(this)"  value="{{$ledger_voucher_register ?($ledger_voucher_register->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
          </tr>
       @endif
       @if(in_array("LedgerDOHistory",array_column($report_access, 'report_title')))
            @php
            $ledger_do_history=user_privileges_role($get_user_data->id,'report','LedgerDOHistory');
            @endphp
            <tr   class="table-row">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">7</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="LedgerDOHistory"/>
                    Accounts Ledger > DO History
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$ledger_do_history->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text"   name="dsply_[]" class="dis_r" onclick="change(this)"  value="{{$ledger_do_history ?($ledger_do_history->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No"/></td>
                <td><input type="hidden" name="delet_[]" value="No"/></td>
                <td><input type="text"   name="print_[]" class="pri_r" onclick="change(this)"  value="{{$ledger_do_history ?($ledger_do_history->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("LedgerVoucherList",array_column($report_access, 'report_title')))
            @php
            $ledger_voucher_list=user_privileges_role($get_user_data->id,'report','LedgerVoucherList');
            @endphp
            <tr  style="background-color: #f1f1f1" class="table-row">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">8</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="LedgerVoucherList"/>
                    Ledger Voucher List / Register
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$ledger_voucher_list->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)"  value="{{$ledger_voucher_list ?($ledger_voucher_list->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No"/></td>
                <td><input type="hidden" name="delet_[]" value="No"/></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)"  value="{{$ledger_voucher_list ?($ledger_voucher_list->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("LedgerMonthly",array_column($report_access, 'report_title')))
            @php
            $ledger_monthly=user_privileges_role($get_user_data->id,'report','LedgerMonthly');
            @endphp
            <tr class="table-row">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">9</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="LedgerMonthly"/>
                    Accounts Ledger > Monthly Summary
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$ledger_monthly->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)"  value="{{$ledger_monthly?($ledger_monthly->display_role==1?'Yes': 'No'): 'No'}}"  /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)"  value="{{$ledger_monthly?($ledger_monthly->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("LedgerDaily",array_column($report_access, 'report_title')))
            @php
            $ledger_daily=user_privileges_role($get_user_data->id,'report','LedgerDaily');
            @endphp
            <tr  style="background-color: #f1f1f1" class="table-row">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">10</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="LedgerDaily"/>
                    Accounts Ledger > Daily Summary
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$ledger_daily->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$ledger_daily?($ledger_daily->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$ledger_daily?($ledger_daily->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("LedgerDaily",array_column($report_access, 'report_title')))
            @php
                $account_group_summary=user_privileges_role($get_user_data->id,'report','AccountGroupSummary');
            @endphp
            <tr class="table-row"  >
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">11</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="AccountGroupSummary"/>
                    Account Group Summary
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$account_group_summary->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$account_group_summary ?($account_group_summary->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$account_group_summary ? ($account_group_summary->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("AccountGroupMontlySummary",array_column($report_access, 'report_title')))
            @php
            $account_group_montly_summary=user_privileges_role($get_user_data->id,'report','AccountGroupMontlySummary');
            @endphp
            <tr class="table-row"  >
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">11</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="AccountGroupMontlySummary"/>
                    Account Group Monthly Summary
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$account_group_montly_summary->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$account_group_montly_summary ?($account_group_montly_summary->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$account_group_montly_summary ? ($account_group_montly_summary->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("BankBook",array_column($report_access, 'report_title')))
            @php
            $bank_book=user_privileges_role($get_user_data->id,'report','BankBook');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">12</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="BankBook"/>
                    Cash / Bank Book(s)
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$bank_book->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$bank_book ?($bank_book->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$bank_book ? ($bank_book->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("BalanceSheet",array_column($report_access, 'report_title')))
            @php
            $balance_sheet=user_privileges_role($get_user_data->id,'report','BalanceSheet');
            @endphp
            <tr class="table-row">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">13</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="BalanceSheet"/>
                    Balance Sheet
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$balance_sheet->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$balance_sheet ?($balance_sheet->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$balance_sheet ?($balance_sheet->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
         @endif
         @if(in_array("TrialBalance",array_column($report_access, 'report_title')))
            @php
            $trial_balance=user_privileges_role($get_user_data->id,'report','TrialBalance');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">14</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="TrialBalance"/>
                    Trial Balance
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$trial_balance->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$trial_balance ?($trial_balance->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$trial_balance ?($trial_balance->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("ProfitLoss",array_column($report_access, 'report_title')))
            @php
            $profit_loss=user_privileges_role($get_user_data->id,'report','ProfitLoss');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">15</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="ProfitLoss"/>
                    Profit & Loss
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$profit_loss->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$profit_loss ?($profit_loss->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$profit_loss ? ($profit_loss->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr >
        @endif
        @if(in_array("StockGroupSummary",array_column($report_access, 'report_title')))
        @php
          $stock_group_summary=user_privileges_role($get_user_data->id,'report','StockGroupSummary');
        @endphp
        <tr class="table-row"  style="background-color: #f1f1f1">
            <td style=" border-right: 1px solid #ddd; padding-left: 16px;">16</td>
            <td style=" border-right: 1px solid #ddd;">
                <input type="hidden" name="status[]" value="report"/>
                <input type="hidden" name="title[]" value="StockGroupSummary"/>
                Stock Group Summary
            </td>
            <input type="hidden" name="privilege_id[]" value="{{$stock_group_summary->privileges_id ?? ''}}"/>
            <td><input type="hidden" name="creat_[]"  value="No" /></td>
            <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $stock_group_summary ?($stock_group_summary->display_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td><input type="hidden" name="alter_[]" value="No" /></td>
            <td><input type="hidden" name="delet_[]" value="No" /></td>
            <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $stock_group_summary ? ($stock_group_summary->print_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
        </tr>
        @endif

        @if(in_array("StockGroupSummaryNew",array_column($report_access, 'report_title')))
            @php
            $stock_group_summary_new=user_privileges_role($get_user_data->id,'report','StockGroupSummaryNew');
            @endphp
            <tr class="table-row"  style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">16</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="StockGroupSummaryNew"/>
                    Stock Group Summary Short
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$stock_group_summary_new->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $stock_group_summary_new ?($stock_group_summary_new->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $stock_group_summary_new ? ($stock_group_summary_new->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("StockItemMonthly",array_column($report_access, 'report_title')))
            @php
            $stock_item_monthly=user_privileges_role($get_user_data->id,'report','StockItemMonthly');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">17</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="StockItemMonthly"/>
                    Stock Item Monthly Summary
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$stock_item_monthly->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $stock_item_monthly ?($stock_item_monthly->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $stock_item_monthly ? ($stock_item_monthly->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("StockItemRegister",array_column($report_access, 'report_title')))
            @php
            $stock_item_register=user_privileges_role($get_user_data->id,'report','StockItemRegister');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">18</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="StockItemRegister"/>
                    Stock Item Register
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$stock_item_register->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$stock_item_register ?($stock_item_register->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$stock_item_register ?($stock_item_register->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("StockGroupSummaryStore",array_column($report_access, 'report_title')))
        @php
          $stock_group_summary_store=user_privileges_role($get_user_data->id,'report','StockGroupSummaryStore');
        @endphp
        <tr class="table-row"  style="background-color: #f1f1f1">
            <td style=" border-right: 1px solid #ddd; padding-left: 16px;">19</td>
            <td style=" border-right: 1px solid #ddd;">
                <input type="hidden" name="status[]" value="report"/>
                <input type="hidden" name="title[]" value="StockGroupSummaryStore"/>
                Stock Group Summary[Store]
            </td>
            <input type="hidden" name="privilege_id[]" value="{{$stock_group_summary_store->privileges_id ?? ''}}"/>
            <td><input type="hidden" name="creat_[]"  value="No" /></td>
            <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $stock_group_summary_store ?($stock_group_summary_store->display_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td><input type="hidden" name="alter_[]" value="No" /></td>
            <td><input type="hidden" name="delet_[]" value="No" /></td>
            <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $stock_group_summary_store ? ($stock_group_summary_store->print_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
        </tr>
        @endif

        @if(in_array("StockGroupSummaryNewStore",array_column($report_access, 'report_title')))
        @php
          $stock_group_summary_new_store=user_privileges_role($get_user_data->id,'report','StockGroupSummaryNewStore');
        @endphp
        <tr class="table-row"  style="background-color: #f1f1f1">
            <td style=" border-right: 1px solid #ddd; padding-left: 16px;">19</td>
            <td style=" border-right: 1px solid #ddd;">
                <input type="hidden" name="status[]" value="report"/>
                <input type="hidden" name="title[]" value="StockGroupSummaryNewStore"/>
                Stock Group Summary Short [Store]
            </td>
            <input type="hidden" name="privilege_id[]" value="{{$stock_group_summary_new_store->privileges_id ?? ''}}"/>
            <td><input type="hidden" name="creat_[]"  value="No" /></td>
            <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $stock_group_summary_new_store ?($stock_group_summary_new_store->display_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td><input type="hidden" name="alter_[]" value="No" /></td>
            <td><input type="hidden" name="delet_[]" value="No" /></td>
            <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $stock_group_summary_new_store ? ($stock_group_summary_new_store->print_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
        </tr>
        @endif
        
        @if(in_array("StockItemMonthlyStore",array_column($report_access, 'report_title')))
            @php
            $stock_item_monthly_store=user_privileges_role($get_user_data->id,'report','StockItemMonthlyStore');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">20</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="StockItemMonthlyStore"/>
                    Stock Item > Monthly Summary[Store]
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$stock_item_monthly_store->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $stock_item_monthly_store ?($stock_item_monthly_store->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $stock_item_monthly_store ? ($stock_item_monthly_store->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("StockItemRegisterStore",array_column($report_access, 'report_title')))
            @php
            $stock_item_register_store=user_privileges_role($get_user_data->id,'report','StockItemRegisterStore');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">21</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="StockItemRegisterStore"/>
                    Stock Item > Register[Store]
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$stock_item_register_store->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$stock_item_register_store ?($stock_item_register_store->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$stock_item_register_store ?($stock_item_register_store->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("StockItemDaily",array_column($report_access, 'report_title')))
            @php
            $stock_item_daily=user_privileges_role($get_user_data->id,'report','StockItemDaily');
            @endphp
            <tr class="table-row"  style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">22</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="StockItemDaily"/>
                    Stock Item Daily Summary
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$stock_item_daily->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $stock_item_daily ?($stock_item_daily->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $stock_item_daily ? ($stock_item_daily->print_role==1?'Yes': 'No'): 'No'}}"  /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("StockVoucherRegister",array_column($report_access, 'report_title')))
            @php
            $stock_voucher_register=user_privileges_role($get_user_data->id,'report','StockVoucherRegister');
            @endphp
            <tr class="table-row"  style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">23</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="StockVoucherRegister"/>
                    Stock Item >Stock Voucher Register
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$stock_voucher_register->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $stock_voucher_register ?($stock_voucher_register->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $stock_voucher_register ? ($stock_voucher_register->print_role==1?'Yes': 'No'): 'No'}}"  /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("StockItemUnusedStock",array_column($report_access, 'report_title')))
            @php
            $stock_item_unused_stock=user_privileges_role($get_user_data->id,'report','StockItemUnusedStock');
            @endphp
            <tr class="table-row"  style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">24</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="StockItemUnusedStock"/>
                    Stock Item >Stock Item > Unused Stock
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$stock_item_unused_stock->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $stock_item_unused_stock ?($stock_item_unused_stock->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $stock_item_unused_stock ? ($stock_item_unused_stock->print_role==1?'Yes': 'No'): 'No'}}"  /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("AccountRatio",array_column($report_access, 'report_title')))
            @php
                $AccountRatio=user_privileges_role($get_user_data->id,'report','AccountRatio');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">53</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="AccountRatio"/>
                    Ratio Analysis
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$AccountRatio->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$AccountRatio ?($AccountRatio->display_role==1?'Yes': 'No'): 'No'}}"/></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$AccountRatio ? ($AccountRatio->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif

        @if(in_array("WarehousewiseStock",array_column($report_access, 'report_title')))
            @php
            $warehouse_wise_stock=user_privileges_role($get_user_data->id,'report','WarehousewiseStock');
            @endphp
            <tr class="table-row">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">25</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="WarehousewiseStock"/>
                    Warehousewise Stock
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$warehouse_wise_stock->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$warehouse_wise_stock ?($warehouse_wise_stock->display_role==1?'Yes': 'No'): 'No'}}"/></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$warehouse_wise_stock ? ($warehouse_wise_stock->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("WarehouseWithoutDamage",array_column($report_access, 'report_title')))
            @php
            $warehouse_without_damage=user_privileges_role($get_user_data->id,'report','WarehouseWithoutDamage');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">26</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="WarehouseWithoutDamage"/>
                    Warehousewise Stock [Without Damage Godown]
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$warehouse_without_damage->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$warehouse_without_damage ?($warehouse_without_damage->display_role==1?'Yes': 'No'): 'No'}}"/></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$warehouse_without_damage ? ($warehouse_without_damage->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("GodownwiseStockAnalysis",array_column($report_access, 'report_title')))
            @php
            $godownwise_stock_analysis=user_privileges_role($get_user_data->id,'report','GodownwiseStockAnalysis');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd; padding-left: 16px;">27</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="GodownwiseStockAnalysis"/>
                    Godownwise Stock Analysis
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$godownwise_stock_analysis->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$godownwise_stock_analysis ?($godownwise_stock_analysis->display_role==1?'Yes': 'No'): 'No'}}"/></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{$godownwise_stock_analysis ? ($godownwise_stock_analysis->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("VoucherListsStatistics",array_column($report_access, 'report_title')))
            @php
            $voucher_lists_statistics=user_privileges_role($get_user_data->id,'report','VoucherListsStatistics');
            @endphp
            <tr class="table-row" >
                <td style="border-right: 1px solid #ddd; padding-left: 16px;">28</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="VoucherListsStatistics"/>
                    Company Statistics Voucher Lists/Register
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$voucher_lists_statistics->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $voucher_lists_statistics ?( $voucher_lists_statistics->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $voucher_lists_statistics ? ( $voucher_lists_statistics->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("VoucherSearch",array_column($report_access, 'report_title')))
            @php
            $VoucherSearch=user_privileges_role($get_user_data->id,'report','VoucherSearch');
            @endphp
            <tr class="table-row" >
                <td style="border-right: 1px solid #ddd; padding-left: 16px;">28</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="VoucherSearch"/>
                    Voucher Search
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$VoucherSearch->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $VoucherSearch ?( $VoucherSearch->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $VoucherSearch ? ( $VoucherSearch->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("VoucherModeChange",array_column($report_access, 'report_title')))
            @php
            $VoucherModeChange=user_privileges_role($get_user_data->id,'report','VoucherModeChange');
            @endphp
            <tr class="table-row" >
                <td style="border-right: 1px solid #ddd; padding-left: 16px;">28</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="VoucherModeChange"/>
                    Voucher Mode Change
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$VoucherModeChange->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $VoucherModeChange ?( $VoucherModeChange->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $VoucherModeChange ? ( $VoucherModeChange->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif        
        @if(in_array("VoucherExchange",array_column($report_access, 'report_title')))
            @php
            $VoucherExchange=user_privileges_role($get_user_data->id,'report','VoucherExchange');
            @endphp
            <tr class="table-row" >
                <td style="border-right: 1px solid #ddd; padding-left: 16px;">28</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="VoucherExchange"/>
                    Voucher Exchange
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$VoucherExchange->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $VoucherExchange ?( $VoucherExchange->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $VoucherExchange ? ( $VoucherExchange->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("CompanyStatistics",array_column($report_access, 'report_title')))
            @php
            $company_statistics=user_privileges_role($get_user_data->id,'report','CompanyStatistics');
            @endphp
            <tr class="table-row"  style="background-color: #f1f1f1">
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">29</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="CompanyStatistics"/>
                    Company Statistics
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $company_statistics->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $company_statistics ?( $company_statistics->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $company_statistics ?($company_statistics->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("VoucherMonthlySummary",array_column($report_access, 'report_title')))
        @php
        $voucher_monthly_summary=user_privileges_role($get_user_data->id,'report','VoucherMonthlySummary');
        @endphp
        <tr class="table-row"  style="background-color: #f1f1f1">
            <td style="border-right: 1px solid #ddd;padding-left: 16px;">30</td>
            <td style="border-right: 1px solid #ddd;">
                <input type="hidden" name="status[]" value="report"/>
                <input type="hidden" name="title[]" value="VoucherMonthlySummary"/>
                Voucher Monthly Summary
            </td>
            <input type="hidden" name="privilege_id[]" value="{{ $voucher_monthly_summary->privileges_id ?? ''}}"/>
            <td><input type="hidden" name="creat_[]"  value="No" /></td>
            <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $voucher_monthly_summary ?( $voucher_monthly_summary->display_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td><input type="hidden" name="alter_[]" value="No" /></td>
            <td><input type="hidden" name="delet_[]" value="No" /></td>
            <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $voucher_monthly_summary ?($voucher_monthly_summary->print_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
        </tr>
    @endif

        @if(in_array("companyVoucherRegister",array_column($report_access, 'report_title')))
            @php
            $voucher_register_cash=user_privileges_role($get_user_data->id,'report','companyVoucherRegister');
            @endphp
            <tr class="table-row"  style="background-color: #f1f1f1">
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">31</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="companyVoucherRegister"/>
                    Voucher Lists/Register
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $voucher_register_cash->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $voucher_register_cash ?( $voucher_register_cash->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $voucher_register_cash ?($voucher_register_cash->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("CashFlow",array_column($report_access, 'report_title')))
            @php
            $cash_flow=user_privileges_role($get_user_data->id,'report','CashFlow');
            @endphp
            <tr class="table-row" >
                <td style="border-right: 1px solid #ddd; padding-left: 16px;">32</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="CashFlow"/>
                    Cash Flow Summary
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$cash_flow->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $cash_flow ?($cash_flow->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $cash_flow ? ($cash_flow->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("GroupCashFlow",array_column($report_access, 'report_title')))
            @php
            $group_cash_flow=user_privileges_role($get_user_data->id,'report','GroupCashFlow');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style="border-right: 1px solid #ddd; padding-left: 16px;">33</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="GroupCashFlow"/>
                    Group Cash Flow
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$group_cash_flow->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $group_cash_flow ?( $group_cash_flow->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $group_cash_flow ?($group_cash_flow->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("LedgerCashFlow",array_column($report_access, 'report_title')))
            @php
            $ledger_cash_flow=user_privileges_role($get_user_data->id,'report','LedgerCashFlow');
            @endphp
            <tr class="table-row" >
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">34</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="LedgerCashFlow"/>
                    Ledger Cash Flow
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $ledger_cash_flow->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $ledger_cash_flow ?( $ledger_cash_flow->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $ledger_cash_flow ?($ledger_cash_flow->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("BankReconciliation",array_column($report_access, 'report_title')))
            @php
            $bank_reconciliation=user_privileges_role($get_user_data->id,'report','BankReconciliation');
            @endphp
            <tr class="table-row" >
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">35</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="BankReconciliation"/>
                    Bank Reconciliation
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $bank_reconciliation->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $bank_reconciliation ?( $bank_reconciliation->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $bank_reconciliation ?($bank_reconciliation->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("PartyLedger",array_column($report_access, 'report_title')))
            @php
            $party_ledger=user_privileges_role($get_user_data->id,'report','PartyLedger');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">35</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="PartyLedger"/>
                    Party Ledger
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $party_ledger->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)"  value="{{ $party_ledger ?( $party_ledger->display_role==1?'Yes': 'No'): 'No'}}"/></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)"  value="{{ $party_ledger ?( $party_ledger->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("PartyLedgeDetails",array_column($report_access, 'report_title')))
            @php
            $party_ledger_details=user_privileges_role($get_user_data->id,'report','PartyLedgeDetails');
            @endphp
            <tr class="table-row" >
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">36</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="PartyLedgeDetails"/>
                    Party Ledger in Details
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$party_ledger_details->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $party_ledger_details ?($party_ledger_details->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $party_ledger_details ?($party_ledger_details->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
        @if(in_array("GroupWisePartyLedger",array_column($report_access, 'report_title')))
            @php
                $group_Wise_party_ledger=user_privileges_role($get_user_data->id,'report','GroupWisePartyLedger');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">37</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="GroupWisePartyLedger"/>
                    Group Wise Party Ledger
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$group_Wise_party_ledger->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{  $group_Wise_party_ledger ?($group_Wise_party_ledger->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{  $group_Wise_party_ledger ?($group_Wise_party_ledger->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
        @endif
       @if(in_array("GroupWisePartyLedgerCreditLimit",array_column($report_access, 'report_title')))
            @php
                $group_Wise_party_ledger_credit_limit=user_privileges_role($get_user_data->id,'report','GroupWisePartyLedgerCreditLimit');
            @endphp
            <tr class="table-row" >
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">38</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="GroupWisePartyLedgerCreditLimit"/>
                    Groupwise Party Ledger with Credit Limit
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$group_Wise_party_ledger_credit_limitr->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{  $group_Wise_party_ledger_credit_limit ?($group_Wise_party_ledger_credit_limit->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{  $group_Wise_party_ledger_credit_limit ?($group_Wise_party_ledger_credit_limit->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                 <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
      @endif
      @if(in_array("PartyLedgerClosingBalance",array_column($report_access, 'report_title')))
            @php
                $party_ledger_closing_balance=user_privileges_role($get_user_data->id,'report','PartyLedgerClosingBalance');
            @endphp
            <tr class="table-row" >
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">39</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="PartyLedgerClosingBalance"/>
                    Party Ledger Closing Balance
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$party_ledger_closing_balance->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{  $party_ledger_closing_balance ?($party_ledger_closing_balance->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{  $party_ledger_closing_balance ?($party_ledger_closing_balance->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
      @endif
      @if(in_array("PartyContactDetails",array_column($report_access, 'report_title')))
            @php
            $party_contact_details=user_privileges_role($get_user_data->id,'report','PartyContactDetails');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">39</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="PartyContactDetails"/>
                    Party Contact Details
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$party_contact_details->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{  $party_contact_details ?($party_contact_details->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{  $party_contact_details ?($party_contact_details->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
      @endif
      @if(in_array("DealerAndRetrailerSalesQuantity",array_column($report_access, 'report_title')))
            @php
            $retailer_sales_analysis=user_privileges_role($get_user_data->id,'report','DealerAndRetrailerSalesQuantity');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">39</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="DealerAndRetrailerSalesQuantity"/>
                    Retailer Sales Analysis
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$retailer_sales_analysis->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{  $retailer_sales_analysis ?($retailer_sales_analysis->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{  $retailer_sales_analysis ?($retailer_sales_analysis->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
       @endif
       @if(in_array("RetrailerActualSales",array_column($report_access, 'report_title')))
            @php
            $retrailer_actual_sales=user_privileges_role($get_user_data->id,'report','RetrailerActualSales');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">39</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="RetrailerActualSales"/>
                    Party Wise Actual Sales
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$retrailer_actual_sales->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{  $retrailer_actual_sales ?($retrailer_actual_sales->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{  $retrailer_actual_sales ?($retrailer_actual_sales->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
       @endif
       @if(in_array("DayWiseCurrentStock",array_column($report_access, 'report_title')))
            @php
            $day_Wise_current_stock=user_privileges_role($get_user_data->id,'report','DayWiseCurrentStock');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">39</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="DayWiseCurrentStock"/>
                    Day Wise Current Stock
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$day_Wise_current_stock->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{  $day_Wise_current_stock ?($day_Wise_current_stock->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{  $day_Wise_current_stock ?($day_Wise_current_stock->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
       @endif
       @if(in_array("ErrorConsole",array_column($report_access, 'report_title')))
            @php
            $errorConsole=user_privileges_role($get_user_data->id,'report','ErrorConsole');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">39</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="ErrorConsole"/>
                    Error Console
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$errorConsole->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{  $errorConsole ?($errorConsole->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{  $errorConsole ?($errorConsole->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
       @endif
      @if(in_array("StockGroupAnalysis",array_column($report_access, 'report_title')))
            @php
                $stock_group_analysis=user_privileges_role($get_user_data->id,'report','StockGroupAnalysis');
            @endphp
            <tr class="table-row" >
                <td style="border-right: 1px solid #ddd;padding-left: 16px;">40</td>
                <td style="border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="StockGroupAnalysis"/>
                    Stock Group Analysis
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $stock_group_analysis->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $stock_group_analysis ?($stock_group_analysis->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $stock_group_analysis ?($stock_group_analysis->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
      @endif
      @if(in_array("StockItemAnalysis",array_column($report_access, 'report_title')))
        @php
        $stock_item_analysis=user_privileges_role($get_user_data->id,'report','StockItemAnalysis');
        @endphp
        <tr class="table-row" style="background-color: #f1f1f1">
            <td style=" border-right: 1px solid #ddd;padding-left: 16px;">41</td>
            <td style=" border-right: 1px solid #ddd;">
                <input type="hidden" name="status[]" value="report"/>
                <input type="hidden" name="title[]" value="StockItemAnalysis"/>
                Stock Item Analysis
            </td>
            <input type="hidden" name="privilege_id[]" value="{{ $stock_item_analysis->privileges_id ?? ''}}"/>
            <td><input type="hidden" name="creat_[]"  value="No" /></td>
            <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $stock_item_analysis ?($stock_item_analysis->display_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td><input type="hidden" name="alter_[]" value="No" /></td>
            <td><input type="hidden" name="delet_[]" value="No" /></td>
            <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $stock_item_analysis ?($stock_item_analysis->print_role==1?'Yes': 'No'): 'No'}}" /></td>
             <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
        </tr>
      @endif
      @if(in_array("StockItemAnalysisDetails",array_column($report_access, 'report_title')))
            @php
            $stockItem_analysis_details=user_privileges_role($get_user_data->id,'report','StockItemAnalysisDetails');
            @endphp
            <tr class="table-row">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">42</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="StockItemAnalysisDetails"/>
                    Stock Item Analysis Details
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $stockItem_analysis_details->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $stockItem_analysis_details ?($stockItem_analysis_details->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $stockItem_analysis_details ?($stockItem_analysis_details->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
      @endif
      @if(in_array("ActualSales",array_column($report_access, 'report_title')))
            @php
            $actual_sales=user_privileges_role($get_user_data->id,'report','ActualSales');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">43</td>
                <td style=" border-right: 1px solid #ddd;">
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="ActualSales"/>
                    Actual Sales
                </td>
                <input type="hidden" name="privilege_id[]" value="{{ $actual_sales->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $actual_sales ?($actual_sales->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $actual_sales ?($actual_sales->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
     @endif
     @if(in_array("LedgerAnalysis",array_column($report_access, 'report_title')))
        @php
        $ledger_analysis=user_privileges_role($get_user_data->id,'report','LedgerAnalysis');
        @endphp
        <tr class="table-row">
            <td style=" border-right: 1px solid #ddd;padding-left: 16px;">44</td>
            <td style=" border-right: 1px solid #ddd;">
                <input type="hidden" name="status[]" value="report"/>
                <input type="hidden" name="title[]" value="LedgerAnalysis"/>
                Ledger Analysis
            </td>
            <input type="hidden" name="privilege_id[]" value="{{$ledger_analysis->privileges_id ?? ''}}"/>
            <td><input type="hidden" name="creat_[]"  value="No" /></td>
            <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$ledger_analysis ?($ledger_analysis->display_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td><input type="hidden" name="alter_[]" value="No" /></td>
            <td><input type="hidden" name="delet_[]" value="No" /></td>
            <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $ledger_analysis ?($ledger_analysis->print_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
        </tr>
     @endif
     @if(in_array("RetrailerAnalysis",array_column($report_access, 'report_title')))
        @php
        $retrailer_analysis=user_privileges_role($get_user_data->id,'report','RetrailerAnalysis');
        @endphp
        <tr class="table-row">
            <td style=" border-right: 1px solid #ddd;padding-left: 16px;">44</td>
            <td style=" border-right: 1px solid #ddd;">
                <input type="hidden" name="status[]" value="report"/>
                <input type="hidden" name="title[]" value="RetrailerAnalysis"/>
                Retrailer Analysis
            </td>
            <input type="hidden" name="privilege_id[]" value="{{$retrailer_analysis->privileges_id ?? ''}}"/>
            <td><input type="hidden" name="creat_[]"  value="No" /></td>
            <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{$retrailer_analysis ?($retrailer_analysis->display_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td><input type="hidden" name="alter_[]" value="No" /></td>
            <td><input type="hidden" name="delet_[]" value="No" /></td>
            <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $retrailer_analysis ?($retrailer_analysis->print_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
        </tr>
     @endif
     @if(in_array("ItemVoucherAnalysisLedger",array_column($report_access, 'report_title')))
            @php
            $item_voucher_analysis_ledger=user_privileges_role($get_user_data->id,'report','ItemVoucherAnalysisLedger');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">45</td>
                <td>
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="ItemVoucherAnalysisLedger"/>
                    Accounts Ledger & Stock Item Analysis with Voucher
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$item_voucher_analysis_ledger->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $item_voucher_analysis_ledger ?( $item_voucher_analysis_ledger->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $item_voucher_analysis_ledger ? ($item_voucher_analysis_ledger->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
     @endif
     @if(in_array("AccountsGroupAnalysis",array_column($report_access, 'report_title')))
            @php
            $accounts_group_analysis=user_privileges_role($get_user_data->id,'report','AccountsGroupAnalysis');
            @endphp
            <tr class="table-row" >
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">46</td>
                <td>
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="AccountsGroupAnalysis"/>
                    Accounts Group Analysis
                </td>
                <input type="hidden" name="privilege_id[]" value="{{$accounts_group_analysis->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{  $accounts_group_analysis ?($accounts_group_analysis->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{  $accounts_group_analysis ? ($accounts_group_analysis->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
            </tr>
      @endif
      @if(in_array("ItemVoucherAnalysisGroup",array_column($report_access, 'report_title')))
            @php
            $item_voucher_analysis_group=user_privileges_role($get_user_data->id,'report','ItemVoucherAnalysisGroup');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
            <td style=" border-right: 1px solid #ddd;padding-left: 16px;">47</td>
            <td>
                <input type="hidden" name="status[]" value="report"/>
                <input type="hidden" name="title[]" value="ItemVoucherAnalysisGroup"/>
                Item Voucher Analysis of Group
            </td>
            <input type="hidden" name="privilege_id[]" value="{{  $item_voucher_analysis_group->privileges_id ?? ''}}"/>
            <td><input type="hidden" name="creat_[]"  value="No" /></td>
            <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $item_voucher_analysis_group ?( $item_voucher_analysis_group->display_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td><input type="hidden" name="alter_[]" value="No" /></td>
            <td><input type="hidden" name="delet_[]" value="No" /></td>
            <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $item_voucher_analysis_group ?( $item_voucher_analysis_group->print_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
        </tr>
      @endif
      @if(in_array("AccountsLedgerAnalysisWithSalesType",array_column($report_access, 'report_title')))
        @php
            $accounts_ledger_analysis_with_sales_type=user_privileges_role($get_user_data->id,'report','AccountsLedgerAnalysisWithSalesType');
        @endphp
        <tr class="table-row">
            <td style=" border-right: 1px solid #ddd;padding-left: 16px;">48</td>
            <td>
                <input type="hidden" name="status[]" value="report"/>
                <input type="hidden" name="title[]" value="AccountsLedgerAnalysisWithSalesType"/>
                Accounts Ledger Analysis With Sales Type
            </td>
            <input type="hidden" name="privilege_id[]" value="{{  $accounts_ledger_analysis_with_sales_type->privileges_id ?? ''}}"/>
            <td><input type="hidden" name="creat_[]"  value="No" /></td>
            <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $accounts_ledger_analysis_with_sales_type ?( $accounts_ledger_analysis_with_sales_type->display_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td><input type="hidden" name="alter_[]" value="No" /></td>
            <td><input type="hidden" name="delet_[]" value="No" /></td>
            <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $accounts_ledger_analysis_with_sales_type ?( $accounts_ledger_analysis_with_sales_type->print_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
        </tr>
      @endif
      @if(in_array("AccountsgroupAnalysisWithSalesType",array_column($report_access, 'report_title')))
            @php
            $accounts_group_analysis_with_sales_type=user_privileges_role($get_user_data->id,'report','AccountsgroupAnalysisWithSalesType');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">49</td>
                <td>
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="AccountsgroupAnalysisWithSalesType"/>
                    Accounts Group Analysis with Sales Type
                </td>
                <input type="hidden" name="privilege_id[]" value="{{   $accounts_group_analysis_with_sales_type->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{  $accounts_group_analysis_with_sales_type ?(  $accounts_group_analysis_with_sales_type->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{  $accounts_group_analysis_with_sales_type ?(  $accounts_group_analysis_with_sales_type->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
          </tr>
      @endif
      @if(in_array("AccountsGroupVoucherWiseAnalysis",array_column($report_access, 'report_title')))
            @php
            $accounts_groupVoucher_wise_analysis=user_privileges_role($get_user_data->id,'report','AccountsGroupVoucherWiseAnalysis');
            @endphp
            <tr class="table-row" style="background-color: #f1f1f1">
                <td style=" border-right: 1px solid #ddd;padding-left: 16px;">50</td>
                <td>
                    <input type="hidden" name="status[]" value="report"/>
                    <input type="hidden" name="title[]" value="AccountsGroupVoucherWiseAnalysis"/>
                   Sales & Collection
                </td>
                <input type="hidden" name="privilege_id[]" value="{{  $accounts_groupVoucher_wise_analysis->privileges_id ?? ''}}"/>
                <td><input type="hidden" name="creat_[]"  value="No" /></td>
                <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{  $accounts_groupVoucher_wise_analysis ?(  $accounts_groupVoucher_wise_analysis->display_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="alter_[]" value="No" /></td>
                <td><input type="hidden" name="delet_[]" value="No" /></td>
                <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{  $accounts_groupVoucher_wise_analysis ?( $accounts_groupVoucher_wise_analysis->print_role==1?'Yes': 'No'): 'No'}}" /></td>
                <td><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
          </tr>
      @endif
      @if(in_array("POSSalesList",array_column($report_access, 'report_title')))
        @php
            $pos_sales_list=user_privileges_role($get_user_data->id,'report','POSSalesList');
            @endphp
        <tr class="table-row">
            <td style=" border-right: 1px solid #ddd;padding-left: 16px;">51</td>
            <td>
                <input type="hidden" name="status[]" value="report"/>
                <input type="hidden" name="title[]" value="POSSalesList"/>
                POS Sales List
            </td>
            <input type="hidden" name="privilege_id[]" value="{{  $pos_sales_list->privileges_id ?? ''}}"/>
            <td><input type="hidden" name="creat_[]"  value="No" /></td>
            <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $pos_sales_list ?( $pos_sales_list->display_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td><input type="hidden" name="alter_[]" value="No" /></td>
            <td><input type="hidden" name="delet_[]" value="No" /></td>
            <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $pos_sales_list ?( $pos_sales_list->print_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
        </tr>
      @endif
      @if(in_array("POSCurrentStock",array_column($report_access, 'report_title')))
            @php
            $pos_current_stock=user_privileges_role($get_user_data->id,'report','POSCurrentStock');
            @endphp
        <tr class="table-row" style="background-color: #f1f1f1">
            <td style=" border-right: 1px solid #ddd;padding-left: 16px;">52</td>
            <td>
                <input type="hidden" name="status[]" value="report"/>
                <input type="hidden" name="title[]" value="POSCurrentStock"/>
                POS Current Stock
            </td>
            <input type="hidden" name="privilege_id[]" value="{{  $pos_current_stock->privileges_id ?? ''}}"/>
            <td><input type="hidden" name="creat_[]"  value="No" /></td>
            <td><input type="text" name="dsply_[]" class="dis_r" onclick="change(this)" value="{{ $pos_current_stock ?( $pos_current_stock->display_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td><input type="hidden" name="alter_[]" value="No" /></td>
            <td><input type="hidden" name="delet_[]" value="No" /></td>
            <td><input type="text" name="print_[]" class="pri_r" onclick="change(this)" value="{{ $pos_current_stock ?( $pos_current_stock->print_role==1?'Yes': 'No'): 'No'}}" /></td>
            <td  style="max-width: 120px!important;"><input type="hidden" name="authorized_dsply_[]" value="No" /></td>
        </tr>
      @endif
    </table>
    </div>
    @endslot
    @slot('footer')
        <div class="col-lg-6 ">
            <div class="form-group">
                <button type="submit"  id="add_user_btn" class="btn hor-grd btn-grd-primary btn-block submit" style="width:100%" ><u>S</u>ave</text>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group">
            <a class=" btn hor-grd btn-grd-success btn-block " href="{{route('user-dashboard')}}" style="width:100%"><u>B</u>ack</a>
            </div>
        </div>
    @endslot
 @endcomponent
@push('js')
 <!-- table hover js -->
 <script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
<script>
    $( function() {
      $( "#datepicker" ).datepicker( {dateFormat: 'mm/dd/yy',minDate: new Date($('.p_financial_year_start').val()),changeMonth: true, changeYear: true});
      $( "#datepicker2" ).datepicker( {dateFormat: 'mm/dd/yy',maxDate: new Date($('.p_financial_year_end').val()),changeMonth: true, changeYear: true});
      $( "#datepicker3" ).datepicker( {dateFormat: 'dd/mm/yy'});
    } );
</script>
<script>

// user privlege select
  $(document).ready(function () {
    get_hover();
    $('.allow_date').on('click',function(){
        if ($(this).prop('checked')) {
            $('.specific_date').prop('disabled', false);
            $('.p_number').prop('disabled', true);
        }else{
            $('.specific_date').prop('disabled', true);
            $('.p_number').prop('disabled', false);
        }
    });
    $('.allow_number').on('click',function(){
        if ($(this).prop('checked')) {
            $('.p_number').prop('disabled', false);
            $('.specific_date').prop('disabled', true);
        }else{
            $('.p_number').prop('disabled', true);
            $('.specific_date').prop('disabled', false);
        }
    });
    $('.f_allow_date').on('click',function(){
        if ($(this).prop('checked')) {
            $('.f_specific_date').prop('disabled', false);
            $('.f_numder').prop('disabled', true);
        }else{
            $('.f_specific_date').prop('disabled', true);
            $('.f_numder').prop('disabled', false);
        }
    });
    $('.f_allow_number').on('click',function(){
        if ($(this).prop('checked')) {
            $('.f_numder').prop('disabled', false);
            $('.f_specific_date').prop('disabled', true);
        }else{
            $('.f_numder').prop('disabled', true);
            $('.f_specific_date').prop('disabled', false);
        }
       });

   $('.creat_all_m').on('click',function(){

        if ($(this).prop('checked')) {
            $('.cre_m').val("Yes");
            $(".cre_m").css("color","orange","font-weight","900");
        }else{
            $('.cre_m').val("No");
            $(".cre_m").css("color","#bbb","font-weight","900");
        }
   })
   $('.alter_all_m').on('click',function(){
        if ($(this).prop('checked')) {
            $('.alt_m').val("Yes");
            $(".alt_m").css("color","orange","font-weight","900");
        }else{
            $('.alt_m').val("No");
            $(".alt_m").css("color","#bbb","font-weight","900");
        }
   })
   $('.delet_all_m').on('click',function(){
        if ($(this).prop('checked')) {
            $('.del_m').val("Yes");
            $(".del_m").css("color","orange","font-weight","900");
        }else{
            $('.del_m').val("No");
            $(".del_m").css("color","#bbb","font-weight","900");
        }
   })
   $('.dsply_all_m').on('click',function(){
        if ($(this).prop('checked')) {
            $('.dis_m').val("Yes");
            $(".dis_m").css("color","orange","font-weight","900");
        }else{
            $('.dis_m').val("No");
            $(".dis_m").css("color","#bbb","font-weight","900");
        }
   })
   $('.print_all_m').on('click',function(){
        if ($(this).prop('checked')) {
            $('.pri_m').val("Yes");
            $(".pri_m").css("color","orange","font-weight","900");
        }else{
            $('.pri_m').val("No");
            $(".pri_m").css("color","#bbb","font-weight","900");
        }
   })
   $('.creat_all_m').on('click',function(){
        if ($(this).prop('checked')) {
            $('.cre_m').val("Yes");
            $(".cre_m").css("color","orange","font-weight","900");
        }else{
            $('.cre_m').val("No");
            $(".cre_m").css("color","#bbb","font-weight","900");
        }
    })
    $('.creat_all_v').on('click',function(){
        if ($(this).prop('checked')) {
            $('.cre_v').val("Yes");
            $(".cre_v").css("color","orange","font-weight","900");
        }else{
            $('.cre_v').val("No");
            $(".cre_v").css("color","#bbb","font-weight","900");
        }
    })
    $('.alter_all_v').on('click',function(){
        if ($(this).prop('checked')) {
            $('.alt_v').val("Yes");
            $(".alt_v").css("color","orange","font-weight","900");
        }else{
            $('.alt_v').val("No");
            $(".alt_v").css("color","#bbb","font-weight","900");
        }
    })
        $('.delet_all_v').on('click',function(){
            if ($(this).prop('checked')) {
                $('.del_v').val("Yes");
                $(".del_v").css("color","orange","font-weight","900");
            }else{
                $('.del_v').val("No");
                $(".del_v").css("color","#bbb","font-weight","900");
            }
        })
        $('.dsply_all_v').on('click',function(){
            if ($(this).prop('checked')) {
                $('.dis_v').val("Yes");
                $(".dis_v").css("color","orange","font-weight","900");
            }else{
                $('.dis_v').val("No");
                $(".dis_v").css("color","#bbb","font-weight","900");
            }
        })
        $('.print_all_v').on('click',function(){
            if ($(this).prop('checked')) {
                $('.pri_v').val("Yes");
                $(".pri_v").css("color","orange","font-weight","900");
            }else{
                $('.pri_v').val("No");
                $(".pri_v").css("color","#bbb","font-weight","900");
            }
        })
        $('.dsply_all_r').on('click',function(){
            if ($(this).prop('checked')) {
                $('.dis_r').val("Yes");
                $(".dis_r").css("color","orange","font-weight","900");
            }else{
                $('.dis_r').val("No");
                $(".dis_r").css("color","#bbb","font-weight","900");
            }
       })
       $('.print_all_r').on('click',function(){
            if ($(this).prop('checked')) {
                $('.pri_r').val("Yes");
                $(".pri_r").css("color","orange","font-weight","900");
            }else{
                $('.pri_r').val("No");
                $(".pri_r").css("color","#bbb","font-weight","900");
            }
        })
   $('.creat_all').on('click',function(){
    if ($(this).prop('checked')) {
        $('.cre_m').val("Yes");
        $(".cre_m").css("color","orange","font-weight","900");
        $('.cre_v').val("Yes");
        $(".cre_v").css("color","orange","font-weight","900");
    }else{
        $('.cre_m').val("No");
        $(".cre_m").css("color","#bbb","font-weight","900");
        $('.cre_v').val("No");
        $(".cre_v").css("color","#bbb","font-weight","900");
    }
   })
   $('.alter_all').on('click',function(){
    if ($(this).prop('checked')) {
        $('.alt_m').val("Yes");
        $(".alt_m").css("color","orange","font-weight","900");
        $('.alt_v').val("Yes");
        $(".alt_v").css("color","orange","font-weight","900");
    }else{
        $('.alt_m').val("No");
        $(".alt_m").css("color","#bbb","font-weight","900");
        $('.alt_v').val("No");
        $(".alt_v").css("color","#bbb","font-weight","900");
    }
   })
   $('.delet_all').on('click',function(){
    if ($(this).prop('checked')) {
        $('.del_m').val("Yes");
        $(".del_m").css("color","orange","font-weight","900");
        $('.del_v').val("Yes");
        $(".del_v").css("color","orange","font-weight","900");
    }else{
        $('.del_m').val("No");
        $(".del_m").css("color","#bbb","font-weight","900");
        $('.del_v').val("No");
        $(".del_v").css("color","#bbb","font-weight","900");
    }
   })
   $('.dsply_all').on('click',function(){
    if ($(this).prop('checked')) {
        $('.dis_m').val("Yes");
        $(".dis_m").css("color","orange","font-weight","900");
        $('.dis_v').val("Yes");
        $(".dis_v").css("color","orange","font-weight","900");

    }else{
        $('.dis_m').val("No");
        $(".dis_m").css("color","#bbb","font-weight","900");
        $('.dis_v').val("No");
        $(".dis_v").css("color","#bbb","font-weight","900");
    }
   })
   $('.print_all').on('click',function(){
    if ($(this).prop('checked')) {
        $('.pri_m').val("Yes");
        $(".pri_m").css("color","orange","font-weight","900");
        $('.pri_v').val("Yes");
        $(".pri_v").css("color","orange","font-weight","900");
    }else{
        $('.pri_m').val("No");
        $(".pri_m").css("color","#bbb","font-weight","900");
        $('.pri_v').val("No");
        $(".pri_v").css("color","#bbb","font-weight","900");
    }
   })
});
    $(function() {
        // add new user privlege ajax request
        $("#add_user_privilege_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            $("#add_user_btn").text('Adding...');
            $.ajax({
                    url: '{{ route("user-privilege-store") }}',
                    method: 'post',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(data) {
                        claer_error();

                            swal({
                                title: 'Success!',
                                text: data.message,
                                type: 'success',
                                timer: '1500'
                            })
                            location.replace('{{ route("user-list-show") }}')
                        $("#add_user_btn").text('Add User');

                    },
                    error : function(data,status,xhr){
                        claer_error();
                        if(data.status==400){
                            swal({
                            title: 'Oops...',
                            text: data.message,
                            type: 'error',
                            timer: '1500'
                        });
                        } if(data.status==422){
                            claer_error();
                           $('#error_user_name').text(data.responseJSON.data.log_in_id ?.[0]);
                          $('#error_password').text(data.responseJSON.data.password ?.[0]);
                        }

                    }
            });
        });

    });

    //data validation data clear
    function claer_error(){
        $('#error_user_name').text('');
        $('#error_password').text('');
    }
   // user privlege Yes or No
    function change(val){
     if(val.value =='No'){
          $(val).closest('tr').find('td .'+val.className).val('Yes');
          $(val).closest('tr').find('td .'+val.className).css("color","orange","font-weight","900");

     }else{

        $(val).closest('tr').find('td .'+val.className).val('No');
        $(val).closest('tr').find('td .'+val.className).css("color","#bbb","font-weight","900");
    }
    }
    </script>

@endpush
@endsection
