<style>
    @media only screen and (max-width: 1200px) {
        .top-navber {
            margin-top: 5px !important;
        }

        .main-body .page-wrapper .align-items-end .page-header-title {
            margin-top: 0px !important;
        }

        .margin {
            margin-left: 0px !important;
        }

        .width {
            width: 170px !important;
        }

        .fa {
            display: math !important;
        }

        .title {
            text-align: center !important;
        }

        .dis {
            display: none !important;
        }

        .display {
            margin: 1px !important;
        }

        .margin {
            margin: 4px !important;
        }

        .form-control {
            margin: 0px !important;
        }

        .th {
            min-width: 150px !important;
        }

        .pcoded-content {
            height: "" !important;
        }

        .foot {
            display: none !important;
        }

        .display {
            display: block !important;
        }
    }
</style>
<div class="pcoded-content" style="background-color: {{ $background_color ?? '' }}">
    <div class="pcoded-inner-content  ">
        <!-- Main-body start -->
        <div class="main-body ">
            <div class="page-wrapper m-t-0 m-l-1  p-10">
                <!-- Page-header start -->
                <div class="page-header m-2 p-0">
                    <div class="row align-items-end" style="margin-bottom: 0%px !important;">
                        <div class="col-lg-8 ">
                            <div class="page-header-title m p-0" style="margin-bottom:7px !important;">
                                <div class="d-inline ">
                                    <h4 style="color: green;font-weight: bold;">{{ $title ?? '' }} [{{ $opration ?? '' }}]</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div style="float: right; margin-left: 5px;">
                                <a style=" float:right;text-decoration: none; " href="{{route('voucher-dashboard')}}"><span class="fa fa-info-circle m-1" style="font-size:27px; color:#00b8e6;"></span><span style="float:right;margin:2px; padding-top:5px; color: color: white;#">Help</span></a>
                            </div>
                            <div style="float: right;margin-left:9px">
                                <a style=" float:right;text-decoration: none; " href="{{ url()->previous() }}"><span class="fa fa-times-circle-o m-1" style="font-size:27px; color:#ff6666;"></span><span style="float:right;margin:2px; padding-top:5px; ">Close</span></a>
                            </div>
                            <div style="float: right; margin-left:9px">
                                <a style=" float: right;text-decoration: none; " href="{{route('daybook-report.index')}}"><span class="fa fa-eye m-1" style="font-size:27px; color:#00b8e6;"></span><span style="float:right;margin:2px; padding-top:5px; ">View</span></a>
                            </div>
                        </div>
                        <hr style="margin-bottom: 0px;">
                    </div>
                </div>
                <div>
                    {{ $voucher_body}}
                </div>

            </div>
        </div>
    </div>
    <a href="{{  route('show-login') }}" id="homeRoute" class="d-none"></a>

    <script>

        let amountDecimals = "{{company()->amount_decimals}}";
        const url_get = $("#homeRoute").attr("href");
        function adjustFontSize(inputElement, textValue) {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            let fontSize = parseInt(window.getComputedStyle(inputElement).fontSize, 10);
            const inputWidth = inputElement.clientWidth;
            let padding = 2;
            // Continuously decrease font size until the text fits
            while (fontSize > 8) { // Minimum font size
                context.font = `${fontSize}px ${window.getComputedStyle(inputElement).fontFamily}`;
                const textWidth = context.measureText(textValue).width;

                if (textWidth <= inputWidth) {
                    break;
                }
                fontSize--;
                padding = parseFloat(padding) + parseFloat(0.71428571);
            }
            // Apply the calculated font size
            inputElement.style.fontSize = `${fontSize}px`;
            inputElement.style.setProperty('padding-top', `${padding}px`, 'important');
            inputElement.style.setProperty('padding-bottom', `${padding}px`, 'important');
        }

        function checkDuplicateItem(duplicateValue, duplicateValueClassName = ".product_id", parentId = "#orders") {
            let isDuplicate = false;
            let dup_row=$('#dup_row').val();
            if(dup_row==1){
                // Loop through all the elements in the parent container
                $(`${parentId} tr`).each(function() {
                    let elementValue = $(this).find(duplicateValueClassName).val() || $(this).find(duplicateValueClassName).text(); // check .val() for input, .text() for others
                    if (duplicateValue == elementValue) {
                        isDuplicate = true;
                        Swal.fire({
                            icon: "warning",
                            title: "Oops...",
                            text: "This Item Already Exists",
                        });
                        return false; // Exit loop early once duplicate is found
                    }
                });
            }
            return isDuplicate;

        }



        $("[name='ref_no'], [name='invoice_no'],[name='narration']").keyup(function(event) {
            // Check if either Alt+'-' or Alt+'+' was pressed
            if (!$(this).attr('readonly') && event.altKey && (event.key === '-' || event.key === '+' || event.key === '=')) {
                let url = `{{route("voucher-backward-forward-data") }}`;  // Define your URL here
                let voucherId = $('.voucher_id').val();
                let type = $(this).attr("name");
                let tran_id = $(this).attr('tran_id') || '';

                // Determine the direction (backward or forward)
                let direction = (event.key === '-') ? 'backward' : 'forward';

                // Make the AJAX call
                $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        backward_forward: direction, // Use 'backward' or 'forward' depending on the key pressed
                        voucher_id: voucherId,
                        type: type,
                        tran_id: tran_id
                    },
                    success: (response)=> {
                        if(response?.data){
                            $(this).val(response?.data?.ref_no || response?.data?.invoice_no || response?.data?.narration || "");
                            $(this).attr('tran_id',response?.data?.tran_id);
                        }else{
                            $(this).val("");
                            $(this).attr('tran_id',"");
                        }
                    }
                });
            }
        });

        function StoreCreditValue(data = "") {
            if(data){
                $(this).find(".credit").val(data);
                $(this).find(".display_credit").val(data);
            }else{

                $(this).find(".credit").val(MakeCurrency(data,false,amountDecimals));
                $(this).find(".display_credit").val(MakeCurrency(data,false,amountDecimals));
            }

        }

        function StoreDebitValue(data = "") {
            if(data){
                $(this).find(".debit").val(data);
                $(this).find(".display_debit").val(data);
            }else{
                $(this).find(".debit").val(MakeCurrency(data,false,amountDecimals));
                $(this).find(".display_debit").val(MakeCurrency(data,false,amountDecimals));
            }
        }
        // key down current price
       $(document).on('keydown', '.product_name', function(e) {
            if(!$(this).attr('readonly')){
                $.ajax({
                    url:`${url_get}/searching-stock-item-price`,
                    method: 'GET',
                    dataType: 'json',
                    async: false,
                    data: {
                        stock_item_id:$(this).closest('tr').find('.product_id').val(),
                        voucher_id:$('.voucher_id').val(),
                        tran_date:$('.invoice_date').val()
                    },
                    success: (response)=>{
                        if(response){
                            if(response.rate){
                                $(this).closest('tr').find('.rate').val((response.rate||0).toFixed(amountDecimals)).trigger('change');
                            } 
                            if(response.commission){
                                      $(this).closest('tr').find('.product_wise_commission_amount').val(response.commission||0).trigger('change');

                                    if(response.commission_type){
                                        $(this).closest('tr').find('.product_wise_commision_cal').val(response.commission_type).trigger('change');
                                    }else{
                                        $(this).closest('tr').find('.product_wise_commision_cal').val(1).trigger('change');
                                    }
                                   
                            }else{
                                $(this).closest('tr').find('.product_wise_commission_amount').val(0).trigger('change');
                                $(this).closest('tr').find('.product_wise_commision_cal').val(1).trigger('change');
                            }

                        }
                    }
                });
            }

       });

    //     $('#orders').on('keyup', '.blance', function(event) {
    //         event.preventDefault(); // Prevent default touch behavior
    //         if ((event.key === 'Tab')) {
    //                 var currentInput = $(this);
    //                 var currentRow = currentInput.closest('tr');
    //                 var nextInput = currentRow.find('input').eq(currentRow.find('input').index(currentInput) + 1);

    //                 if (nextInput.length) {
    //                     nextInput.focus();
    //                 } else {
    //                     currentRow.find('input').eq(0).focus();
    //                 }
    //         }
    //    });

    $(document).on('blur','.product_name',function(){
        if($(this).closest('tr').find('.stock').length>0){
            let id, idArr;
            id = $(this).attr('id');
            idArr = id.split("_");
            let rowNo = idArr[idArr.length - 1];
            let check_current_stock=$('.check_current_stock').val()||0;
            current_stock(rowNo,$(this).closest('tr').find('.product_id').val(),$(this).closest('tr').find('.godown_id').val(),'{{url("current-stock") }}',check_current_stock)
        }        

    })
    function checkGodownValidity(name = '') {
        const defaultValue = ['', '--Select--', '-Select-', 'Select', '--select--', '-select-', 'select','--Select One--'];
        return defaultValue.includes(name)?'':name; // fixed typo here
    }

    // document.addEventListener('keydown', e => {
    //     if (e.key === 'Tab') {
    //         const focusable = Array.from(document.querySelectorAll('input, select, textarea, button, a[href]'))
    //         .filter(el => !el.readOnly && !el.disabled && el.offsetParent !== null);

    //         let index = focusable.indexOf(document.activeElement);

    //         if (index !== -1) {
    //         e.preventDefault();

    //         let nextIndex = e.shiftKey ? index - 1 : index + 1;
    //         if (nextIndex >= focusable.length) nextIndex = 0;
    //         if (nextIndex < 0) nextIndex = focusable.length - 1;

    //         focusable[nextIndex].focus();
    //         }
    //     }
    // });
    </script>
