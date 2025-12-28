// alert message
function swal_message(data,message,m_title){
  swal({
      title:m_title,
      text: data,
      type: message,
      timer: '1500'
  });

}
// input checking
function input_checking(class_name){
    $('#orders').on('keyup  selected blur',`.${class_name}_name`,function(){
        let id=$(this).closest('tr').find(`.${class_name}_id`).val();
        let name=$(this).closest('tr').find(`.${class_name}_name`).val();
        if((id.length!=0)||(name.length==0)){
             $(this).closest('tr').find(`.${class_name}_name`).css({backgroundColor: 'white'}); 
             $('#orders').on('click','input',function(){
                $(this).focus();
             })
        }else{
            $(this).css('backgroundColor','red');
            $(".cansale_btn").attr("disabled", true);
            $(this).focus(); 
        }    
   });
}
// checking is null
function check_item_null(total_debit,product_id){
          if((total_debit==0) && ($('.total_dedit').val())>0){
                $(":submit").attr("disabled", false);
            }else if(total_debit!=0){
                let product='';
                $(document).find('.ledger_name').each(function(){
                    if($(this).val())product=$(this).val();
                });
                if(product=='' ||product==null)$(":submit").attr("disabled", true);
                else $(":submit").attr("disabled", false);
            }else{
                $(":submit").attr("disabled", true);
            }
}

