<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv='cache-control' content='no-cache'>
    <meta http-equiv='expires' content='0'>
    <meta http-equiv='pragma' content='no-cache'>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') {{ config('app.name', 'Account') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{asset('libraries\assets\images\favicon.ico')}}" type="image/x-icon">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries/bower_components/bootstrap/css/bootstrap5.css')}}">
    <!-- feather Awesome -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries/assets\icon\feather\css\feather.css')}}">
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\assets\icon\themify-icons\themify-icons.css')}}">
    <!-- Syntax highlighter Prism css -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\assets\pages\prism\prism.css')}}">
    <!-- Select 2 css -->
    <link rel="stylesheet" href="{{asset('libraries\bower_components\select2\css\select2.min.css')}}">
    <!-- Multi Select css -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\bower_components\bootstrap-multiselect\css\bootstrap-multiselect.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\bower_components\multiselect\css\multi-select.css')}}">
    <!-- Date-time picker css -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\assets\pages\advance-elements\css\bootstrap-datetimepicker.css')}}">
    <!-- Date-range picker css  -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\bower_components\bootstrap-daterangepicker\css\daterangepicker.css')}}">
    <!-- Date-Dropper css -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\bower_components\datedropper\css\datedropper.min.css')}}">
    <!-- Color Picker css -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\bower_components\spectrum\css\spectrum.css')}}">
    <!-- Mini-color css -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\bower_components\jquery-minicolors\css\jquery.minicolors.css')}}">
    <!-- Tags css -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\bower_components\bootstrap-tagsinput\css\bootstrap-tagsinput.css')}}">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\assets\css\style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\assets\css\dark.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\assets\css\navtype.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\assets\css\jquery.mCustomScrollbar.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\assets\css\pcoded-horizontal.min.css')}}">
    <!-- feather Awesome -->
    <link rel="stylesheet" href="{{asset('font-awesome-4.7.0/css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\assets\icon\feather\css\feather.css')}}">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,800" rel="stylesheet">
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries\assets\icon\themify-icons\themify-icons.css')}}">
    <!--common.css -->
    <link rel="stylesheet" type="text/css" href="{{asset('common_css/common.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('common_css/jquery-ui.css')}}">
    <!-- model style -->
    <link rel="stylesheet" type="text/css" href="{{asset('libraries/assets/modal-style.css')}}">

    <script type="text/javascript" src="{{asset('libraries\bower_components\jquery\js\jquery.min.js')}}"></script>
    <style type="text/css">
        .ui-datepicker .ui-icon {
            display: block !important;
            background-image: url("https://code.jquery.com/ui/1.12.1/themes/base/images/ui-icons_444444_256x240.png") !important;
            background-size: 256px 240px;
        }
    </style>
    @stack('css')
</head>

<body themebg-pattern="pattern6">
    <!-- Pre-loader start -->
    <!-- Pre-loader end -->
    <!-- Main Header Container -->
    @include('layouts.backend.partials.head')
    <!-- End Main Header Container -->
    <!-- Main Navbar Container -->
    @include('layouts.backend.partials.navbar')
    <a href="{{ route('show-login') }}" id="homeRoute" class="d-none"></a>
    <!-- End Main Navbar Container -->
    <!-- Main Content -->
    <div class="top-navber" style="margin-top: 50px;">
        <input type="hidden" id="dup_row" value="{{$voucher->dup_row ?? ''}}">
        @yield('admin_content')
    </div>
    <!-- End Main Footer Container -->
    @include('layouts.backend.partials.footer')
    <!-- table hover js -->
    <script type="text/javascript" src="{{asset('libraries/assets/table-hover.js')}}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="{{asset('libraries\bower_components\jquery-slimscroll\js\jquery.slimscroll.js')}}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{asset('libraries\bower_components\modernizr\js\modernizr.js')}}"></script>
    <script type="text/javascript" src="{{asset('libraries\bower_components\modernizr\js\css-scrollbars.js')}}"></script>
    <!-- Select 2 js -->
    <script type="text/javascript" src="{{asset('libraries\bower_components\select2\js\select2.full.min.js')}}"></script>
    <!-- Multiselect js -->
    <script type="text/javascript" src="{{asset('libraries\bower_components\bootstrap-multiselect\js\bootstrap-multiselect.js')}}"></script>
    <script type="text/javascript" src="{{asset('libraries\bower_components\multiselect\js\jquery.multi-select.js')}}"></script>
    <script type="text/javascript" src="{{asset('libraries\assets\js\jquery.quicksearch.js')}}"></script>
    <!-- Tags js -->
    <script type="text/javascript" src="{{asset('libraries\bower_components\bootstrap-tagsinput\js\bootstrap-tagsinput.js')}}"></script>
    <!-- Date-range picker js -->
    <script type="text/javascript" src="{{asset('libraries\bower_components\bootstrap-daterangepicker\js\daterangepicker.js')}}"></script>
    <!-- Date-dropper js -->
    <script type="text/javascript" src="{{asset('libraries\bower_components\datedropper\js\datedropper.min.js')}}"></script>
    <!-- Color picker js -->
    <script type="text/javascript" src="{{asset('libraries\bower_components\spectrum\js\spectrum.js')}}"></script>
    <script type="text/javascript" src="{{asset('libraries\bower_components\jscolor\js\jscolor.js')}}"></script>
    <!-- Mini-color js -->
    <script type="text/javascript" src="{{asset('libraries\bower_components\jquery-minicolors\js\jquery.minicolors.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('libraries\assets\pages\advance-elements\select2-custom.js')}}"></script>
    <!-- data-table js -->
    <script src="{{asset('libraries\assets\js\pcoded.min.js')}}"></script>
    <script src="{{asset('libraries\assets\js\horizontal-layout.min.js')}}"></script>
    <script src="{{asset('libraries\assets\js\jquery.mCustomScrollbar.concat.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('libraries\assets\js\script.js')}}"></script>
    <script src="{{asset('common_js\sweetalert2.js')}}"></script>

    <script src="{{asset('common_js/jspdf.min.js')}}"></script>
    <script src="{{asset('common_js/jspdf.plugin.autotable.min.js')}}"></script>
    <script src="{{asset('common_js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('common_js/jquery-ui.js')}}"></script>
    <script src="{{asset('common_js/localStorage.js')}}"></script>
    <!-- common  js -->
    <script src="{{asset('common_js\common_other.js')}}"></script>
    <script src="{{asset('common_js\navber_active.js')}}"></script>
    <script src="{{asset('common_js\pdf_excel_print.js')}}"></script>

    <!-- Excel  js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="{{asset('common_js\xlsx_style.js')}}"></script>

    <script>
        //  date formate
        $(document).ready(function() {
            $(".setup_date").datepicker({
                dateFormat: "yy-mm-dd",
                minDate: "{{company()->financial_year_start}}",
                maxDate: "{{company()->financial_year_end}}",
                changeMonth: true,
                changeYear: true,
                yearRange: "2000:c+1",
                showButtonPanel: true
            });

            $(document).on('click', ".setup_date", function() {
                $_this = $(this);
                $(document).on('click', "button.ui-datepicker-current", function() {
                    $_this.datepicker('setDate', new Date())
                });
            });
            $(".setup_date").on('keyup', function(event) {
                if ($.trim(this.value).length == 0) {
                    $(this).val("{{company()->financial_year_start}}");
                }

            });
        });

        // value divide calculation
        function dividevalue(dividend, divisor) {
            if (divisor !== 0) {
                return dividend / divisor;
            } else {

                return 0;
            }
        }

        //footer heigh check
        if ($('.pcoded-content').height() < window.innerHeight) {
            $('.pcoded-content').css("min-height", `${(window.innerHeight-75)}` + "px")
        } else {
            $('.pcoded-content').css("min-height", "");
        }
        $('#add,#add_commission').on('click', function() {
            $('.pcoded-content').css("min-height", "")
        })
        $(".icon-menu").click(function() {
            $('.pcoded-navbar').toggleClass('open');
        });

        // ledger commission liline search
        $(document).ready(function() {
            $(document).on('change click keyup ', '.select2-search__field', function() {
                if ($('.select2-results__options').text() == 'No results found') {
                    if ($(document).find('.commission_select2_' + lilineComRowCount).length) {
                        $.ajax({
                            url: '{{url("ledger_name") }}',
                            method: 'GET',
                            dataType: 'json',
                            async: false,
                            data: {
                                ledger_head_name: $('.select2-search__field').val()
                            },
                            success: (response) => {
                                $.each(response, function(key, value) {
                                    $('.select2-results__options').text('');
                                    $('.commission_select2_' + lilineComRowCount).empty();
                                    $('.commission_select2_' + lilineComRowCount).append('<option value="' + value.ledger_head_id + '">' + value.ledger_name + '</option>');
                                });
                            }
                        });
                    }
                }
            });
        });
        
         //swal
         function swal_data(data={}) {
            return {
                title: data?.title||'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: data?.confirmButtonText||'Yes',
                cancelButtonText: 'No',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
            }
        }

        function print_date() {
            let on_dated = $('.from_date').val() + ' to ' + $('.to_date').val();
            localStorage.setItem('on_dated', '');
            localStorage.setItem('on_dated', on_dated);
        }

        // duplicate invoice check
        function duplicate_invoice_check() {
            $('.invoice_no').on('keyup keypress blur change click', function() {
                let invoice_no = $('.invoice_no').val().trim();
                let voucher_id = $('.voucher_id').val();
                let invoice = $('.invoice').val();
                if (invoice.length == 0) {
                    $.ajax({
                        url: '{{ url("voucher-duplicate-check") }}',
                        method: 'GET',
                        data: {
                            "invoice_no": invoice_no,
                            "voucher_id": voucher_id
                        },
                        dataType: "json",
                        success: function(data) {
                            if (data.count == 1) {
                                $("#error_voucher_no").text("Invoice  Already Exists");
                                $("#orders").find("*").attr("disabled", "disabled");
                            } else {
                                let duplicates = data.duplicates.map(item => item.invoice_no).join(", ");
                               if(duplicates.length>0){
                                $("#error_voucher_no")
                                .html(`<span class="text-black font-weight-bold fs-7">${duplicates}</span>`)
                                .removeClass("bg-success")
                               }else{
                                $("#error_voucher_no").text("");
                               }
                             
                                $("#orders").find("*").removeAttr("disabled", "disabled");
                                
                            }
                        }
                    });
                }
            });
        }

        // Reset the form
        function resetForm(formId) {

            document.getElementById(formId).reset();
            // Reset all Select2 fields inside the form
            $(`#${formId} .select2`).val(null).trigger('change');
        }
        Number.prototype.formatBangladeshCurrency = function(sign = '') {
            if (this == null || this === '' || isNaN(this) || this == 0 || this == '0'|| parseFloat(this).toFixed("{{company()->amount_decimals??0}}")==0) return '';
            let x = (this || 0).toFixed("{{company()->amount_decimals??0}}").split('.');
            x[0] = x[0].replace(/(\d)(?=(\d\d)+\d$)/g, "$1,");
            return x.join('.')+sign;
        };

        // Number.prototype.formatBangladeshCurrencyType = function(type = null, symbol = '') {
        //     if (type == "quantity") {
        //         let qty_dcecimal = $('.quantity_decimals:checked').val() || 0;
        //         let qty_comma = $('.show_quantity_comma').is(':checked');
        //         if ($('.show_units_of_measure').is(':checked')) return MakeCurrency(this.toFixed(qty_dcecimal), qty_comma) + symbol;
        //         else return MakeCurrency(this.toFixed(qty_dcecimal), qty_comma)
        //     } else if (type == "rate") {
        //         let rate = $('.rate_decimals:checked').val() || 0;
        //         return MakeCurrency(this.toFixed(rate))
        //     } else if (type == "amount") {
        //         let amount = $('.amount_decimals:checked').val() || 0;
        //         return MakeCurrency(this.toFixed(amount))
        //     } else if (type == "accounts") {
        //         let accounts_decimals = $('.accounts_decimals:checked').val() || 0;
        //         return MakeCurrency(this.toFixed(accounts_decimals));
        //     } else {
        //         return MakeCurrency(this.toFixed(0))
        //     }
        // };
        // Number.prototype.formatBangladeshCurrencyType = function(type = null, symbol = '') {
        //     // if (this == null || this === '' || isNaN(this) || this == 0 || this == '0') return '';
        //     let num = parseFloat(this);
        //     if (type == "quantity") {
        //         let qty_dcecimal = $('.quantity_decimals:checked').val() || 0;
        //         let qty_comma = $('.show_quantity_comma').is(':checked');
        //         if ($('.show_units_of_measure').is(':checked')) return MakeCurrency(num.toFixed(qty_dcecimal), qty_comma,qty_dcecimal) + symbol;
        //         else return MakeCurrency(num.toFixed(qty_dcecimal), qty_comma,qty_dcecimal)
        //     } else if (type == "rate") {
        //         let rate = $('.rate_decimals:checked').val() || 0;
        //         return MakeCurrency(num?.toFixed(rate), true,rate)
        //     } else if (type == "amount") {
        //         let amount = $('.amount_decimals:checked').val() || 0;
        //         return MakeCurrency(num.toFixed(amount),true,amount)
        //     } else if (type == "accounts") {
        //         let accounts_decimals = $('.accounts_decimals:checked').val() || 0;
        //         return MakeCurrency(num.toFixed(accounts_decimals),true,accounts_decimals);
        //     } else {
        //         return MakeCurrency(num.toFixed(0))
        //     }
        // };
        // Number.prototype.formatBangladeshCurrencyAccountsType = function(type = null, data = '', sign = '') {
        //     // if (this == null || this === '' || isNaN(this) || this == 0 || this == '0') return '';
        //     if (type == "debit") {
        //         let accounts_decimals = $('.accounts_decimals:checked').val() || 0;
        //         if ($('#show_debit_is').is(':checked')) {
        //             return data > 0 ? MakeCurrency(this.toFixed(accounts_decimals)) : '';
        //         } else {
        //             return MakeCurrency(this.toFixed(accounts_decimals));
        //         }
        //     } else if (type == "crebit") {
        //         let accounts_decimals = $('.accounts_decimals:checked').val() || 0;
        //         if ($('#show_credit_is').is(':checked')) {
        //             return data > 0 ? MakeCurrency(this.toFixed(accounts_decimals)) : '';
        //         } else {
        //             return MakeCurrency(this.toFixed(accounts_decimals));
        //         }
        //     } else if (type == "closing") {

        //         let accounts_decimals = $('.accounts_decimals:checked').val() || 0;
        //         if ($('#show_closing_is').is(':checked')) {
        //             return data > 0 ? MakeCurrency(this.toFixed(accounts_decimals)) + sign : '';
        //         } else {

        //             return MakeCurrency(this.toFixed(accounts_decimals)) + sign;
        //         }
        //     } else if (type == "accounts") {
        //         let accounts_decimals = $('.accounts_decimals:checked').val() || 0;
        //         return MakeCurrency(this.toFixed(accounts_decimals));
        //     } else {
        //         return MakeCurrency(this.toFixed(0))
        //     }
        // };

        Number.prototype.formatBangladeshCurrencyType = function(type = null, symbol = '', sign = '') {
            if (this == null || this === '' || isNaN(this) || this == 0 || this == '0'|| parseFloat(this.toFixed(4))==0) return '';
            if (type == "quantity") {
                let qty_dcecimal = $('.quantity_decimals:checked').val() || 0;
                let qty_comma = $('.show_quantity_comma').is(':checked');
                if ($('.show_units_of_measure').is(':checked')) return MakeCurrency(this.toFixed(qty_dcecimal), qty_comma,qty_dcecimal) + symbol + sign;
                else return MakeCurrency(this.toFixed(qty_dcecimal), qty_comma,qty_dcecimal) + sign
            } else if (type == "rate") {
                let rate = $('.rate_decimals:checked').val() || 0;
                return MakeCurrency(this.toFixed(rate),true,rate) + sign
            } else if (type == "amount") {
                let amount = $('.amount_decimals:checked').val() || 0;
                return MakeCurrency(this.toFixed(amount),true,amount) + sign
            } else if (type == "accounts") {
                let accounts_decimals = $('.accounts_decimals:checked').val() || 0;
                return MakeCurrency(this.toFixed(accounts_decimals),true,accounts_decimals) + sign;
            } else {
                return MakeCurrency(this.toFixed(0)) + sign
            }
        };
        Number.prototype.formatBangladeshCurrencyAccountsType = function(type = null, data = '', sign = '') {
            if (this == null || this === '' || isNaN(this) || this == 0 || this == '0'|| parseFloat(this).toFixed(4)==0) return '';
            if (type == "debit") {
                let accounts_decimals = $('.accounts_decimals:checked').val() || 0;
                if ($('#show_debit_is').is(':checked')) {
                    return data > 0 ? MakeCurrency(this.toFixed(accounts_decimals)) : '';
                } else {
                    return MakeCurrency(this.toFixed(accounts_decimals));
                }
            } else if (type == "crebit") {
                let accounts_decimals = $('.accounts_decimals:checked').val() || 0;
                if ($('#show_credit_is').is(':checked')) {
                    return data > 0 ? MakeCurrency(this.toFixed(accounts_decimals)) : '';
                } else {
                    return MakeCurrency(this.toFixed(accounts_decimals));
                }
            } else if (type == "closing") {

                let accounts_decimals = $('.accounts_decimals:checked').val() || 0;
                if ($('#show_closing_is').is(':checked')) {
                    return data > 0 ? MakeCurrency(this.toFixed(accounts_decimals)) + sign : '';
                } else {

                    return MakeCurrency(this.toFixed(accounts_decimals)) + sign;
                }
            } else if (type == "accounts") {
                let accounts_decimals = $('.accounts_decimals:checked').val() || 0;
                return MakeCurrency(this.toFixed(accounts_decimals));
            } else {
                return MakeCurrency(this.toFixed(0))
            }
        };

        // function MakeCurrency(num, qty_comma = true) {
        //     if (!qty_comma) {
        //         return num;
        //     } else {
        //         let x = num?.split('.');
        //         x[0] = x[0].replace(/(\d)(?=(\d\d)+\d$)/g, "$1,");
        //         return x.join('.');
        //     }
        // }

        function openning_blance_cal(nature_group, new_DrCr, opening_balance) {
            if ((1 == nature_group || 3 == nature_group) && new_DrCr == 'Cr') {
                return 0 - parseFloat(opening_balance);
            } else if ((2 == nature_group || 4 == nature_group) && new_DrCr == 'Dr') {
                return 0 - parseFloat(opening_balance);
            } else {
                return parseFloat(opening_balance);
            }
        }

        function dateToMonthConvert(from,to) {
            const startDate = new Date(from);
            const endDate = new Date(to);
            const monthNames = [];
            let currentDate = new Date(startDate);

            while (currentDate <= endDate) {
                const monthName = currentDate.toLocaleString('en-US', {
                    month: 'long',
                    year: 'numeric'
                });
                if (!monthNames.includes(monthName)) {
                    monthNames.push(monthName);
                }
                currentDate.setMonth(currentDate.getMonth() + 1);

            }
            return monthNames;
        }

        function Unauthorized(status) {
            if (status == 401) {
                location.reload();
            }
        }

        function MakeCurrency(num, qty_comma = true, decimals = "{{company()->amount_decimals}}") {
            if(!num)return num;

            // Ensure num is a string and remove commas if needed
            let cleanedNum = typeof num === 'number' ? num.toString() : num;
            // cleanedNum = cleanedNum.replace(/,/g, '');
            // cleanedNum = cleanedNum?.replace(/[^0-9.]/g, '')?.replace(/(?<=\..*)\./g, '');
            cleanedNum = cleanedNum?.replace(/[^0-9.-]/g, '')?.replace(/(?<=\..*)\./g, '');

            // Convert to a number and fix decimal places
            let fixedNum = parseFloat(cleanedNum);
            // If qty_comma is false, just return the fixed number
            if (!qty_comma) {
                return fixedNum;
            }
            fixedNum = parseFloat(cleanedNum).toFixed(decimals)
            // Add commas for currency formatting
            let [integer, decimal] = fixedNum.split('.');
            // integer = integer.replace(/(\d)(?=(\d{3})+$)/g, "$1,");
            integer = integer.replace(/(\d)(?=(\d\d)+\d$)/g, "$1,");

            return decimal ? integer + '.' + decimal : integer;
        }

        function areApproxEqual(a, b) {
            return Math.abs(a - b) < 1;
        }

        // user refresh ajax request
        $(document).on('click', '.refresh-account', function(e) {
            swal(swal_data({title:'Are you Sure to Refresh Account?',confirmButtonText:'Yes Refresh Account'})).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    $.ajax({
                        url: "{{url('user-refresh') }}" ,
                        type : "GET",
                        success: function (data) {
                            swal_message(data.message,'success','Successfully');
                            setTimeout(function () {  window.location.href='{{ url()->previous() }}'; },100);
                        },
                        error: function () {
                            swal_message(data.responseJSON.message,'error','Error');
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

        function swal_message(data,message,m_title){
            swal({
                title:m_title,
                text: data,
                type: message,
                timer: '1500'
            });
        }


        $.fn.bindAutocompleteTabKeyHandler = function () {
            return this.each(function () {
                const $input = $(this);

                $input.on('keydown', function (event) {
                    if (event.key === "Tab") {
                        const menu = $input.autocomplete("widget");
                        if (menu.is(":visible")) {
                            event.preventDefault();
                            menu.find("li:first-child").trigger("click");
                        }
                    }
                });
            });
        };

    </script>

    @stack('js')
</body>


</html>
