// alert message
function swal_message(data,message,m_title){
  swal({
      title:m_title,
      text: data,
      type: message,
      timer: '3000'
  });

}
// data value change
function  selected_auto_value_change(currentEle,response,amount_decimals){
        let qty=currentEle.closest('tr').find('.qty').val();
        currentEle.closest('tr').find('.amount').val(parseFloat(qty*response).toFixed(amount_decimals));
}

 function check_item_null(total_qty_is,product_id){
         
          if((total_qty_is==0) && ($('.total_qty').val())>0){
                $(":submit").attr("disabled", false);
            }else if(total_qty_is!=0){
                let product='';
                $(document).find('.product_name').each(function(){
                    if($(this).val())product=$(this).val();
                });
                if(product=='' || product==null)$(":submit").attr("disabled", true);
                else $(":submit").attr("disabled", false);
            }else{
                $(":submit").attr("disabled", true);
            }
}

$('.godown').on('change',function(){
    let godown_val=$('.godown').val();
    let godown=godown_val?$('.godown option:selected').text():"";
    
    $('#orders tr').find('.godown_name').each(function(){
        $('.godown_name').val(godown);
        $('.godown_id').val(godown_val);
    })
})
// input checking
function input_checking(class_name){
    $('#orders').on('keyup blur',`.${class_name}_name`,function(){

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