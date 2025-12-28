<style>
    input[type=checkbox] {
        width: 20px;
        height: 20px;
    }

    input[type=radio] {
        width: 22px;
        height: 22px;
    }

    .hidder_display {
        display: none !important;
    }
</style>
@php
$page_wise_report_setting_data=page_wise_report_setting(Auth::user()->id,$page_unique_id);
@endphp
<div class="modal fade page-wise-setting_modal {{ $class ?? '' }}" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}-title" aria-modal="true">
    <div class="modal-dialog the-modal  {{ $size ?? '' }}" role="document">
        <div class="modal-content  the-modal__container m-0 p-0">
            <div class="modal-header the-modal__header m-1 p-1">
                <h6 style="font-weight: bold;" class="modal-title" id="{{ Str::slug($title) }}-title">{{ $title }} Settings</h6>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <form method="POST" id="page_wise_setting" enctype="multipart/form-data">
                @csrf
                {{ method_field('POST') }}
                <div class="modal-body m-1 p-1">
                    @if(empty($insert_settings))
                    <div class="row">
                        <div class="col-md-6">
                            <fieldset style="border: 1px solid black; padding: 10px; margin: 10px;">
                                <legend style="font-size: 18px; color: black; font-weight: bold;">Report Advanced Settings</legend>
                                <input type="hidden" name="id" value="{{$page_wise_report_setting_data?($page_wise_report_setting_data->id):'' }}">
                                <input type="hidden" name="page_title" value="{{$page_title}}">
                                <input type="hidden" name="page_unique_id" value="{{$page_unique_id}}">
                                
                                 <!-- Show Debit  Decimal Points Section -->
                                 <div style="margin-bottom: 10px;">
                                        <label style="color: black;">
                                            <input class="form-check-input" id="show_accounts_decimal" type="checkbox" name="show_account_decimal" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->accounts_decimals!=0 ? 'checked': ''):'checked' }}>
                                            Show Decimal Points
                                        </label>
                                        <div style="padding-left: 20px; margin-top: 5px;">
                                            <span style="color: black;">Decimals:</span>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input accounts_decimals" type="radio" name="accounts_decimals" value="0" {{$page_wise_report_setting_data?($page_wise_report_setting_data->accounts_decimals==0 ? 'checked': ''):'' }}> None
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input accounts_decimals" type="radio" name="accounts_decimals" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->accounts_decimals==1 ? 'checked': ''):'' }}> One
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input accounts_decimals" type="radio" name="accounts_decimals" value="2" {{$page_wise_report_setting_data?($page_wise_report_setting_data->accounts_decimals==2 ? 'checked': ''):'checked' }}> Two
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input accounts_decimals" type="radio" name="accounts_decimals" value="3" {{$page_wise_report_setting_data?($page_wise_report_setting_data->accounts_decimals==3 ? 'checked': ''):'' }}> Three
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input accounts_decimals" type="radio" name="accounts_decimals" value="4" {{$page_wise_report_setting_data?($page_wise_report_setting_data->accounts_decimals==4 ? 'checked': ''):'' }}> Four
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Show debit Section -->
                                    <div style="margin-bottom: 10px;">
                                        <label style="color: black;">
                                            <input class="form-check-input show_debit_is" id="show_debit_credit_is" type="checkbox" name="show_debit_is" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->show_debit_is==1 ? 'checked': ''):'checked' }}>
                                              No Debit AND Credit Transaction (Only Opening Balance).
                                        </label>
                                    </div>
                                    
                                    <!-- Show debit Section -->
                                    <div style="margin-bottom: 10px;">
                                        <label style="color: black;">
                                            <input class="form-check-input show_closing_is" id="show_closing_is" type="checkbox" name="show_closing_is" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->show_closing_is==1 ? 'checked': ''):'checked' }}>
                                             Do Not Show If Closing Balance is 0 .
                                        </label>
                                    </div>

                                    <h6><b>Stock Item Summary</b></h6>
                                    <!-- Show Quantity Decimal Points Section -->
                                    <div style="margin-bottom: 10px;">
                                        <label style="color: black;">
                                            <input class="form-check-input" id="show_quantity_decimal" type="checkbox" name="show_quantity_decimal" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->qty_dcecimal!=0 ? 'checked': ''):'checked' }}>
                                            Show Quantity Decimal Points
                                        </label>
                                        <div style="padding-left: 20px; margin-top: 5px;">
                                            <span style="color: black;">Decimals:</span>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input quantity_decimals" type="radio" name="quantity_decimals" value="0" {{$page_wise_report_setting_data?($page_wise_report_setting_data->qty_dcecimal==0 ? 'checked': ''):'' }}> None
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input quantity_decimals" type="radio" name="quantity_decimals" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->qty_dcecimal==1 ? 'checked': ''):'' }}> One
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input quantity_decimals" type="radio" name="quantity_decimals" value="2" {{$page_wise_report_setting_data?($page_wise_report_setting_data->qty_dcecimal==2 ? 'checked': ''):'checked' }}> Two
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input quantity_decimals" type="radio" name="quantity_decimals" value="3" {{$page_wise_report_setting_data?($page_wise_report_setting_data->qty_dcecimal==3 ? 'checked': ''):'' }}> Three
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input quantity_decimals" type="radio" name="quantity_decimals" value="4" {{$page_wise_report_setting_data?($page_wise_report_setting_data->qty_dcecimal==4 ? 'checked': ''):'' }}> Four
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Show Quantity Comma Section -->
                                    <div style="margin-bottom: 10px;">
                                        <label style="color: black;">
                                            <input class="form-check-input show_quantity_comma" id="show_quantity_comma" type="checkbox" name="show_quantity_comma" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->qty_comma==1 ? 'checked': ''):'checked' }}>
                                            Show Quantity Comma
                                        </label>
                                    </div>

                                    <!-- Show Units of Measure Section -->
                                    <div style="margin-bottom: 10px;">
                                        <label style="color: black;">
                                            <input class="form-check-input show_units_of_measure" id="show_units_of_measure" type="checkbox" name="show_units_of_measure" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->units_of_measure!=0 ? 'checked': ''):'checked' }}>
                                            Show Units of Measure for Quantity
                                        </label>
                                    </div>

                                    <!-- Show Rate Decimal Points Section -->
                                    <div style="margin-bottom: 10px;">
                                        <label style="color: black; ">
                                            <input class="form-check-input" type="checkbox" id="show_rate_decimal" name="show_rate_decimal" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->rate_decimal!=0 ? 'checked': ''):'checked' }}>
                                            Show Rate Decimal Points
                                        </label>
                                        <div style="padding-left: 20px; margin-top: 5px;">
                                            <span style="color: black;">Decimals:</span>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input rate_decimals" type="radio" name="rate_decimals" value="0" {{$page_wise_report_setting_data?($page_wise_report_setting_data->rate_decimal==0 ? 'checked': ''):'' }}> None
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input rate_decimals" type="radio" name="rate_decimals" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->rate_decimal==1 ? 'checked': ''):'' }}> One
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input rate_decimals" type="radio" name="rate_decimals" value="2" {{$page_wise_report_setting_data?($page_wise_report_setting_data->rate_decimal==2 ? 'checked': ''):'checked' }}> Two
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input rate_decimals" type="radio" name="rate_decimals" value="3" {{$page_wise_report_setting_data?($page_wise_report_setting_data->rate_decimal==3 ? 'checked': ''):'' }}> Three
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input rate_decimals" type="radio" name="rate_decimals" value="4" {{$page_wise_report_setting_data?($page_wise_report_setting_data->rate_decimal==4 ? 'checked': ''):'' }}> Four
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Show Amount Decimal Points Section -->
                                    <div style="margin-bottom: 10px;">
                                        <label style="color: black;">
                                            <input class="form-check-input" type="checkbox" id="show_amount_decimal" name="show_amount_decimal" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->amount_decimal!=0 ? 'checked': ''):'checked' }}>
                                            Show Amount Decimal Points
                                        </label>
                                        <div style="padding-left: 20px; margin-top: 5px;">
                                            <span style="color: black;">Decimals:</span>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input amount_decimals" type="radio" name="amount_decimals" value="0" {{$page_wise_report_setting_data?($page_wise_report_setting_data->amount_decimal==0 ? 'checked': ''):'' }}> None
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input amount_decimals" type="radio" name="amount_decimals" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->amount_decimal==1 ? 'checked': ''):'' }}> One
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input amount_decimals" type="radio" name="amount_decimals" value="2" {{$page_wise_report_setting_data?($page_wise_report_setting_data->amount_decimal==2 ? 'checked': ''):'checked' }}> Two
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input amount_decimals" type="radio" name="amount_decimals" value="3" {{$page_wise_report_setting_data?($page_wise_report_setting_data->amount_decimal==3 ? 'checked': ''):'' }}> Three
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input amount_decimals" type="radio" name="amount_decimals" value="4" {{$page_wise_report_setting_data?($page_wise_report_setting_data->amount_decimal==4 ? 'checked': ''):'' }}> Four
                                            </label>
                                        </div>
                                    </div>
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset style="border: 1px solid black; padding: 15px; margin: 15px;">
                                <legend style="font-size: 18px; color: black; font-weight: bold;">Report Printing Setup</legend>
                               
                                    <!-- Show Company Name Section -->
                                    <div style="margin-bottom: 15px;">
                                        <label style="color: black; ">
                                            <input class="form-check-input show_company_name" id="show_company_name" type="checkbox" name="show_company_name" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->company_name!=0 ? 'checked': ''):'checked' }}>
                                            Show <i>'Company Name'</i> while printing a report?
                                        </label>
                                        <div style="padding-left: 20px; margin-top: 5px;">
                                            <span style="color: black;">Font Size:</span>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input company_name" type="radio" name="company_name" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->company_name==1 ? 'checked': ''):'' }}> tiny
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input company_name" type="radio" name="company_name" value="2" {{$page_wise_report_setting_data?($page_wise_report_setting_data->company_name==2 ? 'checked': ''):'' }}> small
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input company_name" type="radio" name="company_name" value="3" {{$page_wise_report_setting_data?($page_wise_report_setting_data->company_name==3 ? 'checked': ''):'' }}> medium
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input company_name" type="radio" name="company_name" value="4" {{$page_wise_report_setting_data?($page_wise_report_setting_data->company_name==4 ? 'checked': ''):'checked' }}> large
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Show Company Mailing Address Section -->
                                    <div style="margin-bottom: 15px;">
                                        <label style="color: black;">
                                            <input class="form-check-input" type="checkbox" id="show_company_mailing_address" name="show_company_mailing_address" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->company_mailingaddress!=0 ? 'checked': ''):'checked' }}>
                                            Show <i>'Company Mailing Address'</i> while printing a report?
                                        </label>
                                        <div style="padding-left: 20px; margin-top: 5px;">
                                            <span style="color: black; ">Font Size:</span>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input company_mailing_address" type="radio" name="company_mailing_address" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->company_mailingaddress==1 ? 'checked': ''):'' }}> tiny
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input company_mailing_address" type="radio" name="company_mailing_address" value="2" {{$page_wise_report_setting_data?($page_wise_report_setting_data->company_mailingaddress==2 ? 'checked': ''):'' }}> small
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input company_mailing_address" type="radio" name="company_mailing_address" value="3" {{$page_wise_report_setting_data?($page_wise_report_setting_data->company_mailingaddress==3 ? 'checked': ''):'' }}> medium
                                            </label>
                                            <label style="margin-left: 10px;">
                                                <input class="form-check-input company_mailing_address" type="radio" name="company_mailing_address" value="4" {{$page_wise_report_setting_data?($page_wise_report_setting_data->company_mailingaddress==4 ? 'checked': ''):'checked' }}> large
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Show Print Date Section -->
                                    <div style="margin-bottom: 15px;">
                                        <label style="color: black;">
                                            <input class="form-check-input print_date" type="checkbox" id="print_date" name="print_date" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->print_date==1 ? 'checked': ''):'checked' }}>
                                            Show <i>'Print Date'</i> while printing a report?
                                        </label>
                                    </div>
                                <!-- Show Report Name Section -->
                                <div style="margin-bottom: 15px;">
                                    <label style="color: black; ">
                                        <input class="form-check-input" type="checkbox" id="show_report_name" name="show_report_name" {{$page_wise_report_setting_data?($page_wise_report_setting_data->report_name!=0 ? 'checked': ''):'checked' }} value="1">
                                        Show <i>'Report Name'</i> while printing a report?
                                    </label>
                                    <div style="padding-left: 20px; margin-top: 5px;">
                                        <span style="color: black;">Font Size:</span>
                                        <label style="margin-left: 10px;">
                                            <input class="form-check-input report_name" type="radio" name="report_name" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->report_name==1 ? 'checked': ''):'' }}> tiny
                                        </label>
                                        <label style="margin-left: 10px;">
                                            <input class="form-check-input report_name" type="radio" name="report_name" value="2" {{$page_wise_report_setting_data?($page_wise_report_setting_data->report_name==2 ? 'checked': ''):'' }}> small
                                        </label>
                                        <label style="margin-left: 10px;">
                                            <input class="form-check-input report_name" type="radio" name="report_name" value="3" {{$page_wise_report_setting_data?($page_wise_report_setting_data->report_name==3 ? 'checked': ''):'' }}> medium
                                        </label>
                                        <label style="margin-left: 10px;">
                                            <input class="form-check-input report_name" type="radio" name="report_name" value="4" {{$page_wise_report_setting_data?($page_wise_report_setting_data->report_name==4 ? 'checked': ''):'checked' }}> large
                                        </label>
                                    </div>
                                </div>
                                <!-- Show Report Details Section -->
                                <div style="margin-bottom: 15px;">
                                    <label style="color: black; ">

                                        <input class="form-check-input" type="checkbox" id="show_report_details" name="show_report_details" {{$page_wise_report_setting_data?($page_wise_report_setting_data->report_details==1 ? 'checked': ''):'checked' }} value="1">
                                        Show <i>'Report Details Description'</i> while printing a report?
                                    </label>
                                    <div style="padding-left: 20px; margin-top: 5px;">
                                        <span style="color: black; ">Report Details Description:</span>


                                        <label style="margin-left: 10px;">
                                            <input class="form-check-input report_details date_print" type="checkbox" name="show_date" value="1" {{$page_wise_report_setting_data?($page_wise_report_setting_data->show_date==1 ? 'checked': ''):'checked' }}> Date
                                        </label>

                                        @if($godown??'')
                                        <label style="margin-left: 10px;">
                                            <input class="form-check-input report_details godown_name_print" type="checkbox" name="show_godown" value="2" {{$page_wise_report_setting_data?($page_wise_report_setting_data->show_godown==2 ? 'checked': ''):'' }}>Grodown
                                        </label>
                                        @endif

                                        @if($stockGroup??'')
                                        <label style="margin-left: 10px;">
                                            <input class="form-check-input report_details stock_group_ptint" type="checkbox" name="show_stock_group" {{$page_wise_report_setting_data?($page_wise_report_setting_data->show_stock_group==3 ? 'checked': ''):'checked' }} value="3">Stock Group
                                        </label>
                                        @endif

                                        @if($stock_item??'')
                                        <label style="margin-left: 10px;">
                                            <input class="form-check-input report_details stock_item_ptint" type="checkbox" name="show_item" value="4" {{$page_wise_report_setting_data?($page_wise_report_setting_data->show_item==4 ? 'checked': ''):'' }}>Stock Item
                                        </label>
                                        @endif

                                        @if($groupChart??'')
                                        <label style="margin-left: 10px;">
                                            <input class="form-check-input report_details group_chart_print" type="checkbox" name="show_group_chart" value="5" {{$page_wise_report_setting_data?($page_wise_report_setting_data->show_group_chart==5 ? 'checked': ''):'' }}>Accounts Group
                                        </label>
                                        @endif

                                        @if($ledger??'')
                                        <label style="margin-left: 10px;">
                                            <input class="form-check-input report_details ledger_name_print" type="checkbox" name="show_ledger" value="6" {{$page_wise_report_setting_data?($page_wise_report_setting_data->show_ledger==6 ? 'checked': ''):'' }}>Ledger Name
                                        </label>
                                        @endif

                                    </div>
                                </div>
                                <!-- Show Report Details Section -->
                                <div style="margin-bottom: 15px;">
                                    <label style="color: black; ">

                                        <input class="form-check-input" type="checkbox" id="show_show_footer" name="show_footer" {{$page_wise_report_setting_data?($page_wise_report_setting_data->show_footer==1 ? 'checked': ''):'checked' }} value="1">
                                        Show <i>Report Footer</i>
                                    </label>
                                    <div style="padding-left: 2px;text-align: right; margin-top: 5px;display:flex;">
                                        <span style="color: black;">Report Left Footer Description:</span>
                                        <label style="margin-left: 30px;">
                                            <input type="text" class="form-control left_footer footer_text" name="left_footer" placeholder="Report Left Footer" value="{{$page_wise_report_setting_data->left_footer??''}}">
                                        </label>
                                    </div>
                                    <div style="padding-left: 2px;text-align: right; margin-top: 5px;display:flex;">
                                        <span style="color: black;">Report Middle Footer Description:</span>
                                        <label style="margin-left: 10px;">
                                            <input type="text" class="form-control middle_footer footer_text" name="middle_footer" placeholder="Report Middele Footer" value="{{$page_wise_report_setting_data->middle_footer??''}}">
                                        </label>
                                    </div>
                                    <div style="padding-left: 2px; margin-top: 5px;display:flex;">
                                        <span style="color: black;">Report Right Footer Description:</span>
                                        <label style="margin-left: 20px; text-align: right;">
                                            <input type="text" class="form-control right_footer footer_text" placeholder="Report Right Footer" name="right_footer" value="{{$page_wise_report_setting_data->right_footer??''}}">
                                        </label>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger " id="report_model_rest_btn" data-dismiss="modal">Close</button>
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
        footer();
        $("#page_wise_setting_btn").text('Submit');
        $.ajax({
            url: '{{ url("report-page-wise-setting") }}',
            method: 'post',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data, status, xhr) {
                $(".page-wise-setting_modal").modal('hide');
                let $daynamic_function = "{{$daynamic_function??''}}";
                if ($daynamic_function && typeof window[$daynamic_function] === 'function') {
                    console.log('Calling function:', $daynamic_function);
                    window[$daynamic_function]();
                } else {
                    console.error('Function not found:', $daynamic_function);
                }
                //location.reload();
                swal_message(data.message, 'success', 'Successfullly');
            },
            error: function(data, status, xhr) {}
        });
    });

    function swal_message(data, message, title_mas) {
        swal({
            title: title_mas,
            text: data,
            type: message,
            timer: '1500'
        });
    }

    $('.close').on('click', function() {
        document.getElementById('page_wise_setting').reset(); // Remove the #
    });
    $('.report_model_rest_btn').on('click', function() {
        document.getElementById('page_wise_setting').reset();
    });

    $(document).ready(function() {
        $('.quantity_decimals').on('click', function() {
            if ($('.quantity_decimals').is(':checked')) {
                $('#show_quantity_decimal').prop('checked', true);
            } else {
                $('#show_quantity_decimal').prop('checked', false);
            }
        });

        $('#show_quantity_decimal').on('click', function() {

            if ($('#show_quantity_decimal').is(':checked')) {
                $('.quantity_decimals[value="2"]').prop('checked', true);
            } else {
                $('.quantity_decimals').prop('checked', false);
            }
        });

        $('.rate_decimals').on('click', function() {
            if ($('.rate_decimals').is(':checked')) {
                $('#show_rate_decimal').prop('checked', true);
            } else {
                $('#show_rate_decimal').prop('checked', false);
            }
        });

        $('#show_rate_decimal').on('click', function() {
            if ($('#show_rate_decimal').is(':checked')) {
                $('.rate_decimals[value="2"]').prop('checked', true);
            } else {
                $('.rate_decimals').prop('checked', false);
            }
        });

        $('.amount_decimals').on('click', function() {
            if ($('.amount_decimals').is(':checked')) {
                $('#show_amount_decimal').prop('checked', true);
            } else {
                $('#show_amount_decimal').prop('checked', false);
            }
        });

        $('#show_amount_decimal').on('click', function() {
            if ($('#show_amount_decimal').is(':checked')) {
                $('.amount_decimals[value="2"]').prop('checked', true);
            } else {
                $('.amount_decimals').prop('checked', false);
            }
        });

        $('.company_name').on('click', function() {
            if ($('.company_name').is(':checked')) {
                $('#show_company_name').prop('checked', true);
            } else {
                $('#show_company_name').prop('checked', false);
            }
        });

        $('#show_company_name').on('click', function() {
            if ($('#show_company_name').is(':checked')) {
                $('.company_name[value="4"]').prop('checked', true);
            } else {
                $('.company_name').prop('checked', false);
            }
        });

        $('.company_mailing_address').on('click', function() {
            if ($('.company_mailing_address').is(':checked')) {
                $('#show_company_mailing_address').prop('checked', true);
            } else {
                $('#show_company_mailing_address').prop('checked', false);
            }
        });

        $('#show_company_mailing_address').on('click', function() {
            if ($('#show_company_mailing_address').is(':checked')) {
                $('.company_mailing_address[value="4"]').prop('checked', true);
            } else {
                $('.company_mailing_address').prop('checked', false);
            }
        });

        $('.report_name').on('click', function() {
            if ($('.report_name').is(':checked')) {
                $('#show_report_name').prop('checked', true);
            } else {
                $('#show_report_name').prop('checked', false);
            }
        });

        $('#show_report_name').on('click', function() {
            if ($('#show_report_name').is(':checked')) {
                $('.report_name[value="4"]').prop('checked', true);
            } else {
                $('.report_name').prop('checked', false);
            }
        });

        $('.date_print').on('click', function() {
            if ($('.date_print').is(':checked')) {
                $('#show_report_details').prop('checked', true);
            } else if ($('.godown_name_print').is(':checked')) {
                $('#show_report_details').prop('checked', true);
            } else if ($('.group_chart_print').is(':checked')) {
                $('#show_report_details').prop('checked', true);
            } else {
                $('#show_report_details').prop('checked', false);
            }
        });

        $('.godown_name_print').on('click', function() {
            if ($('.godown_name_print').is(':checked')) {
                $('#show_report_details').prop('checked', true);
            } else if ($('.date_print').is(':checked')) {
                $('#show_report_details').prop('checked', true);
            } else {
                $('#show_report_details').prop('checked', false);
            }
        });
        $('.group_chart_print').on('click', function() {
            if ($('.date_print').is(':checked')) {
                $('#show_report_details').prop('checked', true);
            } else if ($('.godown_name_print').is(':checked')) {
                $('#show_report_details').prop('checked', true);
            } else if ($('.group_chart_print').is(':checked')) {
                $('#show_report_details').prop('checked', true);
            } else {
                $('#show_report_details').prop('checked', false);
            }
        });
        $('#show_report_details').on('click', function() {
            if ($('#show_report_details').is(':checked')) {
                $('.report_details[value="1"]').prop('checked', true);
            } else {
                $('.godown_name_print').prop('checked', false);
                $('.date_print').prop('checked', false);
            }
        });
        $('#show_show_footer').on('click', function() {
            if ($('#show_show_footer').is(':checked')) {
                $('.footer_text').removeAttr('disabled');
            } else {
                $('.footer_text').attr('disabled', 'disabled');
            }
        });
        $('.footer_text').on('click', function() {
            if ($('#show_show_footer').is(':checked')) {
                $('.footer_text').removeAttr('disabled');
            } else {
                $('.footer_text').attr('disabled', 'disabled');
            }
        });
        footer();

    });

    function footer() {
        if ($('#show_show_footer').is(':checked')) {
            let left_footer = $('.left_footer').val();
            let middle_footer = $('.middle_footer').val();
            let right_footer = $('.right_footer').val();
            let footer = `<span class="hide-btn"><b>Copyright &copy; 2014-2022 <a href="http://www.hamko-ict.com/">HAMKO-ICT.</a> All rights
                                        reserved.</b></span>
                                    <div class="row mt-3 hidder_display" style="display:flex;">
                                ${left_footer.length===0?'':`<div class="col-sm-${middle_footer.length==0?6:4}">
                                        <h5 style="text-align:left ;min-width:${middle_footer.length==0?500:300}px;">${left_footer} : . . . . . . . . . . . . . . . . . . . . .</h5>
                                    </div>`}
                                ${middle_footer.length==0?'':`<div class="col-sm-${middle_footer.length==0?6:4}">
                                    <h5 style="text-align:right;min-width:300px;">${middle_footer} : . . . . . . . . . . . . . . . . . . . . . .</h5>
                                </div>`}

                                ${right_footer.length==0?'':`<div class="col-sm-${middle_footer.length==0?6:4}">
                                    <h5 style="text-align:right;min-width:${middle_footer.length==0?500:300}px;">${right_footer} : . . . . . . . . . . . . . . . . . . . . . .</h5>
                                </div>`}
                            </div>`;

            $('.footer_class').html(footer);
        }
    }
</script>