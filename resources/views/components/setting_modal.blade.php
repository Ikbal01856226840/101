<style>
    input[type=checkbox] {
    width: 20px;
    height: 20px;
}
input[type=radio] {
    width: 22px;
    height: 22px;
}
 </style>
 @php
   $page_wise_setting_data=page_wise_setting(Auth::user()->id,$page_unique_id);
 @endphp
 <div class="modal fade page-wise-setting_modal {{ $class ?? '' }}" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}-title" aria-modal="true">
    <div class="modal-dialog the-modal  {{ $size ?? '' }}" role="document">
        <div class="modal-content  the-modal__container m-0 p-0">
            <div class="modal-header the-modal__header m-1 p-1">
                <h5 class="modal-title" id="{{ Str::slug($title) }}-title">{{ $title }}    Settings</h5>
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            
            </div>
           
            <form   method="POST" id="page_wise_setting" enctype="multipart/form-data">
                @csrf
                {{ method_field('POST') }}
                <div class="modal-body m-1 p-1">
                @if(empty($insert_settings))
                <fieldset  class="border" >
                    <legend  class="float-none w-auto " style="font-size: 20px;">Insert Settings</legend>
                        <div class="form-group m-1">
                            <div class="row">
                                <div class="col-md-12 {{$redirect_page_true ?? 'd-none' }}">
                                    <label class="form-check-label fs-6" for="flexRadioDefault1" >
                                        After Redirect :
                                    </label>
                                    <input class="form-check-input"  type="radio"  name="redirect_page" {{$page_wise_setting_data?($page_wise_setting_data->redirect_page==1 ? 'checked': ''):'' }}  value="1" >
                                    <label class="form-check-label fs-6" for="flexRadioDefault1" >
                                       View Page
                                    </label>
                                    <input class="form-check-input "  type="radio"  {{$page_wise_setting_data?($page_wise_setting_data->redirect_page==0 ? 'checked': ''):'' }}  name="redirect_page"  value="0" >
                                    <label class="form-check-label fs-6" for="flexRadioDefault1" >
                                       Add Page
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 {{$last_inset_true ?? 'd-none' }}">
                                    <input  class="form-check-input " type="checkbox" name="last_insert_data_set" {{$page_wise_setting_data?($page_wise_setting_data->last_insert_data_set==5 ? 'checked': ''):'' }} value="5">
                                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                                       Last Insert Data Set
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-check-label fs-6" for="flexRadioDefault1">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    @endif
                    <fieldset  class="border " >
                        <legend  class="float-none w-auto " style="font-size: 20px;">@if(empty($view_settings))View Settings @else Advence Settings @endif </legend>
                            <div class="form-group m-1">
                                <div class="row {{$tree_collapser ?? 'd-none' }}">
                                    <div class="col-md-6" >
                                        <button type="button" id="expander">expand</button>
                                        <button type="button" id="collapser">collapse</button>
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 {{$bangla_true ?? 'd-none' }}" >
                                        <input id="bangla_name" class="form-check-input " type="checkbox" {{$page_wise_setting_data?($page_wise_setting_data->bangla_name==5 ? 'checked': ''):'' }} name="bangla_name"  value="5">
                                        <label class="form-check-label fs-6" for="flexRadioDefault1">
                                            Bangla
                                        </label>
                                    </div>
                                    <div class="col-md-6 {{$alias_true ?? 'd-none' }}">
                                        <input id="alias" class="form-check-input " type="checkbox"  name="alias" {{$page_wise_setting_data?($page_wise_setting_data->alias==9 ? 'checked': ''):'' }} value="9">
                                        <label class="form-check-label fs-6" for="flexRadioDefault1">
                                            Alias
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input id="user_name" class="form-check-input " type="checkbox" {{$page_wise_setting_data?($page_wise_setting_data->created_by==5 ? 'checked': ''):'' }} name="user_name"  value="5">
                                        <input type="hidden"  name="page_wise_id" value="{{$page_wise_setting_data?($page_wise_setting_data->id):'' }}" >
                                        <input type="hidden"  name="page_title" value="{{$page_title}}" >
                                        <input type="hidden"  name="page_unique_id" value="{{$page_unique_id}}">
                                        <label class="form-check-label fs-6" for="flexRadioDefault1">
                                            Created By
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input id="last_update" class="form-check-input" type="checkbox"  {{$page_wise_setting_data?($page_wise_setting_data->history==6 ? 'checked': ''):'' }} name="last_update"   value="6" >
                                        <label class="form-check-label fs-6" for="flexRadioDefault1">
                                            History
                                        </label>
                                    </div>
                                </div>
                                @if(empty($sort_by))
                                <div class="row ">
                                    <div class="col-md-6">
                                        <input class="form-check-input sort_by_asc"  type="radio"  name="sort_by" {{$page_wise_setting_data?($page_wise_setting_data->sort_by==1 ? 'checked': ''):'' }} value="1" >
                                        <label class="form-check-label fs-6" for="flexRadioDefault1" >
                                            Sort type :  A to Z 
                                        </label>
                                        
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-check-input sort_by_desc" id="sort_by_desc_1" type="radio"  name="sort_by" {{$page_wise_setting_data?($page_wise_setting_data->sort_by==0 ? 'checked': ''):'' }} value="0" >
                                        <label class="form-check-label fs-6" for="flexRadioDefault1" >
                                            Sort type :  Z to A
                                        </label>
                                    </div>
                                </div>
                                @endif
                                <div class="row">
                                    <div class="col-md-6 {{$sort_by_group_true ?? 'd-none' }}">
                                        <input id="sort_by_group" class="form-check-input " type="radio"   {{$page_wise_setting_data?($page_wise_setting_data->sort_by_group==10 ? 'checked': ''):'' }} name="sort_by_group" value="10">
                                        <label class="form-check-label fs-6" for="flexRadioDefault1">
                                            Sort By Stock Group
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger " data-dismiss="modal">Close</button> 
                    <button type="submit" id="page_wise_setting_btn" class="btn btn-primary">Submit</button>
              </div>
         </form>
        </div>
    </div>
</div>
<script>
    // add page wise setting  ajax request
    $("#page_wise_setting").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#page_wise_setting_btn").text('Submit');
        $.ajax({
                url: '{{ url("page-wise-setting") }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data,status,xhr) {
                 $(".page-wise-setting_modal").modal('hide');
                 location.reload();
                 swal_message(data.message,'success','Successfullly');
                },
                error : function(data,status,xhr){
            
                }
        });
    });
    function swal_message(data,message,title_mas){
    swal({
        title:title_mas,
        text: data,
        type: message,
        timer: '1500'
    });
}
</script>

 