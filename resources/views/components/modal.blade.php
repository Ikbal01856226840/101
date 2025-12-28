 
 <div class="modal fade {{ $class ?? '' }} " id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}-title" aria-modal="true">
    <div class="modal-dialog the-modal  {{ $size ?? '' }}" role="document">
        <div class="modal-content  the-modal__container">
            <div class="modal-header the-modal__header">
                <h5 class="modal-title" id="{{ Str::slug($title) }}-title">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="{{ $form_class ?? '' }} model_rest"  method="POST" id="{{ $form_id ?? '' }}" enctype="multipart/form-data">
                @csrf
                {{ method_field($method) }}
                <div class="modal-body ">
                    {{ $body }}
                </div>
                <div class="modal-footer">
                    {{ $footer }}
                </div>
         </form>
        </div>
    </div>
</div>
<script>
    $('.close').on('click',function(){
        $('.model_rest')[0].reset();
       
    })
    $('.model_rest_btn').on('click',function(){
    
        $('.model_rest')[0].reset();
       
    })
</script>
