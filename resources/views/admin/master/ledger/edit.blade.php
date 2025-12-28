@extends('layouts.backend.app')
@section('title','Ledger')
@push('css')
<style>
  input[type=radio] {
    width: 20px;
    height: 20px;
}
input[type=checkbox] {
    width: 20px;
    height: 20px;
}
</style>
@endpush
@section('admin_content')
<br>
<!-- ledger add model  -->
@component('components.create', [
    'title' => 'Accounts Ledger [edit]',
    'help_route'=>route('ledger.index'),
    'close_route'=>route('master-dashboard'),
    'veiw_route'=>route('ledger.index'),
    'form_id' => 'edit_ledger_form',
    'method'=> 'PUT',
])
    @slot('body')
        <div class="row m-1" >
            <div class="form-group col-lg-6">
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Ledger Name :</label>
                        <input type="hidden" class="id" name="id" >
                        <input type="text" name="ledger_name" class="form-control form-control-lg ledger_name" placeholder="Enter Ledger Name" required>
                        <span id='edit_error_ledger_name' class=" text-danger"></span>
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput"> Bangla Name Optional :</label>
                    <input type="text" name="bangla_ledger_name" class="form-control bangla_ledger_name" id="formGroupExampleInput" placeholder="Bangla Ledger Name" >
               </div>
               <div class="form-group">
                    <label>Alias Type:</label>            
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="alias_type" id="alias_type_auto" value="0" checked>
                        <label class="form-check-label" for="alias_type_auto">Auto</label>
                    </div>
                
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="alias_type" id="alias_type_manual" value="1">
                        <label class="form-check-label" for="alias_type_manual">Manual</label>
                    </div>
                </div>
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Alias :</label>
                    <span id="dup_alias" class="text-black font-weight-bold fs-7"></span>
                    <input type="text" name="alias" class="form-control form-control-lg alias" id="alias" placeholder="Enter Alias" readonly >
                    <span id='edit_error_alias' class="text-danger"></span>
                </div>
                <div class="form-group ">
                    <label  for="exampleInputEmail1">Under Group :</label>
                        <select name="group_id" id="group_id" class="form-control  js-example-basic-single  group_id left-data" required>
                            <option value="0">Select</option>
                            {!!html_entity_decode($group_chart_id)!!}
                        </select>
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
                <div class="form-group m-2">
                <label  for="exampleInputEmail1">Ledger Type </label>
                    <select name="ledger_type" id="ledger_type" class="form-control  js-example-basic-single  ledger_type " >
                        <option value="0">None</option>
                        <option value="1">Dealer</option>
                        <option value="2">Retrailer</option>

                    </select>
            </div>
            <div class="form-group m-2 ledger_select_option d-none" >
                <label  for="exampleInputEmail1">Under Dealer Name:</label>
                <select name="under_ledger_id"  class="form-control  js-example-basic-single ledger_id"  disabled>
                    <option value="">--Select--</option>
                    {!!html_entity_decode($ledgers)!!}
                </select>
            </div>
                <div class="border border-dark m-1">
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">Nature of Activities:</label>
                        <select name="nature_activity" id="nature_activity" class="form-control  js-example-basic-single    nature_activity left-data" >
                            <option value="Not Selected">Not Selected</option>
                            <option value="Operating">Operating</option>
                            <option value="Investing">Investing</option>
                            <option value="Financing">Financing</option>
                        </select>
                    </div>
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">Inventory Value Affected ? </label>
                            <select name="inventory_value" id="inventory_value" class="form-control   js-example-basic-single  inventory_value left-data" >
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                    </div>
                </div>
                <div class="border border-success m-1 ">
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">Starting Balance :</label>
                            <input type="number" name="opening_balance" step="any" class="form-control form-control-lg opening_balance" placeholder="Enter Starting Balance">
                    </div>
                    <div class="form-group m-2">
                        <label  for="exampleInputEmail1">Dr/Cr : </label>
                            <select name="DrCr" id="DrCr" class="form-control  js-example-basic-single   DrCr left-data" >
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
                    <div class="form-group m-2 ">
                    <label  for="exampleInputEmail1">TIN Certificate :</label>
                        <input type="text" name="tin_certificate" class="form-control form-control-lg tin_certificate " placeholder="Enter TIN Certificate">
                </div>
                <div class="form-group m-2 ">
                    <label  for="exampleInputEmail1">Bank Cheque :</label>
                    <textarea name="bank_cheque" class="form-control bank_cheque" id="exampleFormControlTextarea1" rows="2"></textarea>
                </div>
               </div>
            </div>
        </div>
    @endslot
    @slot('footer')
        <div class="col-lg-4 ">
            <div class="form-group" style="margin-left:2px; ">
                <button type="submit" id="edit_ledger_btn" class="btn btn-primary" style="width:100%">Update</button>
            </div>
        </div>
        <div class="col-lg-4">
            @if(user_privileges_check('master','Ledger','delete_role'))
                <div class="form-group">
                    <button  type="button" class="btn btn-danger deleteIcon"  data-dismiss="modal" style="width:100%">Delete</button>
                </div>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <a class=" btn hor-grd btn-grd-success btn-block " href="{{route('master-dashboard')}}" style="width:100%">Close</a>
            </div>
        </div>
    @endslot
 @endcomponent
@push('js')
<!-- table hover js -->
<script>
let data="{{$data}}";
let autoAlias='';
let manualAlias='';
edit_ledger()
//ledger edit function
//ledger edit function
function edit_ledger(){
    $.ajax({
            url: "{{ url('ledger/edit-data') }}" + '/' +"{{$data->ledger_head_id}}" ,
            method: 'GET',
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                console.log(response.data);
                $(".id").val(response.data.ledger_head_id );
                $(".ledger_name").val(response.data.ledger_name);
                $('.bangla_ledger_name').val(response.data.bangla_ledger_name);
                $('.alias').val(response.data.alias);
                $(".group_id").val(response.data.group_id).trigger('change');
                $(".unit_or_branch").val(response.data.unit_or_branch).trigger('change');
                $(".nature_activity").val(response.data.nature_activity).trigger('change');
                $(".inventory_value").val(response.data.inventory_value).trigger('change');
                $('.opening_balance').val(Math.abs(response.data.opening_balance));
                $(".DrCr").val(response.data.DrCr).trigger('change');
                $('.credit_limit').val(response.data.credit_limit);
                $('.mailing_name').val(response.data.mailing_name);
                $('.mobile').val(response.data.mobile);
                $('.mailing_add').val(response.data.mailing_add).trigger('change');
                $('.national_id').val(response.data.national_id);
                $('.trade_licence_no').val(response.data.trade_licence_no);
                $('.tin_certificate').val(response.data.tin_certificate);
                $('.bank_cheque').val(response.data.bank_cheque).trigger('change');
                $('.ledger_type').val(response.data.ledger_type).trigger('change');
                // console.log(response.data.alias_type);
                if(response.data.alias_type==0){
                    autoAlias=response.data.alias;
                    $('#alias_type_auto').attr('checked','checked');
                    $('#alias').attr('readonly','readonly');
                    $('#alias_type_auto').click();
                }else if(response.data.alias_type==1){
                    manualAlias=response.data.alias;
                    $('#alias_type_auto').attr('checked','checked');
                    $('#alias').removeAttr('readonly');
                    $('#alias_type_manual').click();
                }
                if(response.data.ledger_type==2){
                    $(".ledger_id").attr("disabled", false);
                    $(".ledger_select_option").removeClass("d-none");

                    $('.ledger_id').val(response.data.under_ledger_id).trigger('change');
               }
            }
        });
}


     // update group chart ajax request
     $("#edit_ledger_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        var id = $('.id').val();
        $("#edit_ledger_btn").text('Adding...');
        $.ajax({
            url: "{{ url('ledger') }}" + '/' + id ,
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data,status,xhr) {
                    claer_error()
                    swal_message(data.message, 'success', 'Successfully');
                    claer_error()
                    $("#edit_ledger_btn").text('Update');
                    $("#edit_ledger_form")[0].reset();
                    $("#EditLedgerModel").modal('hide');
                    // setTimeout(function () {  window.location.href='{{route("ledger.index")}}'; },100);
                    setTimeout(function () {  window.location.href='{{ url()->previous() }}'; },100);
            },
            error : function(data,status,xhr){
                    if(data.status==404){
                        swal_message(data.responseJSON.message, 'error', 'Error');
                    } if(data.status==422){
                        claer_error();
                        $('#edit_error_ledger_name').text(data.responseJSON.data.ledger_name?data.responseJSON.data.ledger_name[0]:'');
                        $('#edit_error_alias').text(data.responseJSON.data.alias?data.responseJSON.data.alias[0]:'');
                    }

                }
        });
    })
//data validation data clear
function claer_error(){
    $('#error_group_chart_name').text('');
}
// delete ledger ajax request
    $(document).on('click', '.deleteIcon', function(e) {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var id = $('.id').val();
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
            buttonsStyling: false,
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                event.preventDefault();
                $.ajax({
                    url: "{{ url('ledger') }}" + '/' + id ,
                    type : "POST",
                    data : {'_method' : 'DELETE', '_token' : csrf_token},
                    success: function (data) {
                        swal_message(data.message, 'success', 'Successfully');
                        location.replace('{{route("ledger.index") }}')
                    },
                    error: function () {
                        location.replace('{{ route("ledger.index") }}')
                        swal_message(data.responseJSON.message, 'error', 'Error');
                    }
                });
            } else if (
                // Read more about handling dismissals
                result.dismiss === swal.DismissReason.cancel
            ) {
                swal(
                    'Cancelled',
                    'Your data is safe :)',
                    'error'
                )
            }
        })
    });
    function swal_message(data, message, title_mas) {
        swal({
            title: title_mas,
            text: data,
            type: message,
            timer: '1500'
        });
   }
   $('.ledger_type').on('change',function(){
       if($('.ledger_type').val()==2){
            $(".ledger_id").attr("disabled", false);
            $(".ledger_id").attr("required", true);
            $(".ledger_select_option").removeClass("d-none");
       }else{
            $(".ledger_id").attr("disabled", true);
            $(".ledger_id").attr("required", false);
            $(".ledger_select_option").addClass("d-none");
       }
   });

   $("#alias_type_auto").click(function(){
        $('#alias').attr('readonly','readonly');
        $("#alias").attr('placeholder','');
        if(autoAlias){
            $("#alias").val(autoAlias);
        }else{
            getAutoAlias();
        }
    })
    $("#alias_type_manual").click(function(){
        $('#alias').removeAttr('readonly');  
        $("#alias").attr('placeholder','');
        if(manualAlias){
            $("#alias").val(manualAlias);
        }else{
            $.ajax({
                url: "{{ url('get-last-manual-alias') }}",
                method: 'GET',
                dataType: "json",
                success: function(data,status,xhr) {
                    if(data?.data){
                        $("#alias").attr('placeholder',`Last Manual Alias ${data?.data||''}`);
                    }else{
                        $("#alias").attr('placeholder','No manual alias found');
                    }
                }
            })
        }
        
    })

    $('#alias').on('keyup blur paste',function(){
        let alias=$(this).val();
        $.ajax({
            url: "{{ url('duplicate-alias-check') }}",
            method: 'get',      
            dataType: 'json',
            data: {"alias": alias},
            success: function(data,status,xhr) {
                if (data?.data?.count == 1) {
                    $("#dup_alias").text("Alias  Already Exists");
                    $("#add_ledger_btn").attr("disabled", "disabled");
                } else {
                    let duplicates = data?.data?.duplicates.map(item => item.alias).join(", ");
                    if(duplicates.length>0){
                        $("#dup_alias").text(duplicates).removeClass("bg-success")
                    }else{
                        $("#dup_alias").text("");
                    }
                    
                    $("#add_ledger_btn").removeAttr("disabled", "disabled");
                    
                }
            }
        })
    });
    function getAutoAlias(){
        $.ajax({
            url: "{{ url('get-auto-alias') }}",
            method: 'GET',
            dataType: "json",
            success: function(data,status,xhr) {
                $("#alias").val(data?.data||'');
            }
        });
    }
</script>
@endpush
@endsection

