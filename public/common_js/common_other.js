// backgroundColor color change 
$(document).on(' keyup keypress change click','textarea, input[type="text"],input[type="number"]', function() {
    if((window.getSelection().focusNode?window.getSelection().focusNode?.firstElementChild?.name=='product_name[]':'')||(window.getSelection().focusNode?window?.getSelection()?.focusNode?.firstElementChild?.name=='product_out_name[]':'')||(window.getSelection().focusNode?window?.getSelection()?.focusNode?.firstElementChild?.name=='product_in_name[]':'')||(window.getSelection().focusNode?window?.getSelection().focusNode?.firstElementChild?.name=='product_in_name[]':'')){
        $('input,textarea').css('backgroundColor','');
        $('#'+window.getSelection().focusNode.firstElementChild.id).css('backgroundColor','white');
        $('#'+window.getSelection().focusNode.firstElementChild.id).css('backgroundColor','#ABCDEF');
    }else if (window.getSelection().focusNode?(window.getSelection().focusNode.firstElementChild?.name=='godown_name[]')||(window.getSelection().focusNode.firstElementChild?.name=='godown_in_name[]')||(window.getSelection().focusNode.firstElementChild?.name=='godown_out_name[]'):'') {
        $('input, textarea').css('backgroundColor','');
        $('#'+window.getSelection().focusNode.firstElementChild.id).css('backgroundColor','white');
        $('#'+window.getSelection().focusNode.firstElementChild.id).css('backgroundColor','#ABCDEF');
    } else if(window.getSelection().focusNode?(window.getSelection().focusNode.firstElementChild?.name=='ledger_name[]'):''){
        $('input, textarea').css('backgroundColor','');
        $('#'+window.getSelection().focusNode.firstElementChild.id).css('backgroundColor','white');
        $('#'+window.getSelection().focusNode.firstElementChild.id).css('backgroundColor','#ABCDEF');
    } 
    else {
        $('input,textarea').css('backgroundColor','');
         $(this).css('backgroundColor','white');
         $(this).css('backgroundColor','#abcdef');
    }
});
    window.onbeforeunload = function() {
        window.location.reload(true);
    }

     $(document).ready(function(){
             // table header fixed
             let display_height=$(window).height();
             $('.tableFixHead').css('height',`${display_height-170}px`);
             $('.tableFixHead_double_header').css('height',`${display_height-250}px`);
             $('.tableFixHead_report').css('height',`${display_height-250}px`);
             
   
           // table value searching
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
    });
    //date formet
    var options = [{
        day: 'numeric'
    }, {
        month: 'short'
    }, {
        year: 'numeric'
    }];
    function join(date, options, separator) {
        function format(option) {
            let formatter = new Intl.DateTimeFormat('en', option);
            return formatter.format(date);
        }
        return options.map(format).join(separator);
    }
