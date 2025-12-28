<link rel="stylesheet" type="text/css" href="{{asset('custom_responsive/custom_master_responsive.css')}}">
<div class="coded-main-container navChild ">
    <div class="pcoded-content">
        <div class="pcoded-inner-content">
            <!-- Main-body start -->
            <div class="main-body p-0  side-component">
                <div class="page-wrapper m-t-0 p-0">
                    <!-- Page-header start -->
                    <div class="page-header m-2 p-0">
                        <div class="row align-items-end" style="margin-bottom: 0%px !important;">
                            <div class="col-lg-3">
                                <div class="page-header-title m p-0" style="margin-bottom:7px !important;">
                                    <div class="d-inline ">
                                        <h4 style="color: green;font-weight: bold;">{{ $title ?? '' }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div style="float: right; margin-left: 5px;">
                                    <a style=" float:right;text-decoration: none; " class="{{$close_route ?? 'd-none' }}" href="{{ $close_route ?? '' }}"><span class="fa fa-times-circle-o m-1" style="font-size:27px; color:#ff6666;"></span><span style="float:right;margin:2px; padding-top:5px; ">Close</span></a>
                                </div>
                                <div style="float: right; margin-left: 5px;"> <a style=" float:right; text-decoration: none; cursor: pointer" data-toggle="modal" data-target="#exampleModal" class="{{$setting_model ?? 'd-none' }}"><span class="fa fa-cog m-1" style="font-size:27px;  color:Green;"></span><span style="float:right;margin:2px; padding-top:5px; ">Setting</span></a>
                                </div>
                                @if(user_privileges_check($user_privilege_status_type,$user_privilege_title,'print_role'))
                                <div style="float: right;margin-left:9px">
                                    <a style="float:right;text-decoration: none;cursor: pointer; " class="{{ $print ?? 'd-none' }}" onclick="print_html('{{$print_layout}}','{{$print_header}}')"><span class="fa fa-print m-1" style="font-size:27px; color:teal;"></span><span style="float:right;margin:2px; padding-top:5px; ">Print</span></a>
                                </div>
                                <div style="float: right;margin-left:9px">
                                    <a style="float:right;text-decoration: none;cursor: pointer; " class="{{ $excel ?? 'd-none' }}" onclick="exportTableToExcel('{{$print_header}}')"><span class="fa fa-file-excel-o m-1" style="font-size:25px; color:Gray;"></span><span style="float:right;margin:2px; padding-top:5px; ">Excel</span></a>
                                </div>
                                <div style="float: right;margin-left:9px">
                                    <a style="float:right;text-decoration: none;cursor: pointer;" class="{{ $pdf ?? 'd-none' }}" onclick="generateTable('{{$print_header}}')"><span class="fa fa-file-pdf-o m-1" style="font-size:25px; color:MediumSeaGree;"></span><span style="float:right;margin:2px; padding-top:5px; ">Pdf</span></a>
                                </div>
                                @endif
                                @if(user_privileges_check($user_privilege_status_type,$user_privilege_title,$user_privilege_type))
                                <div style="float: right; margin-left:9px">
                                    <a style=" float: right;text-decoration: none;cursor: pointer; " class=" btn-print-invoice {{ $add_modal_data ?? 'd-none' }}" data-toggle="modal" data-target="{{ $add_modal_data ?? '' }}" data: { turbolinks: false }><span class="fa fa-plus-circle m-1" style="font-size:27px; color:#00b8e6;"></span><span style="float:right;margin:2px; padding-top:5px; ">New</span></a>
                                    <a style=" float: right;text-decoration: none;cursor: pointer" class="{{$add_route ?? 'd-none' }}" href="{{ $add_route ?? '' }}" data-turbolinks="false"><span class="fa fa-plus-circle m-1" style="font-size:27px; color:#00b8e6;"></span><span style="float:right;margin:2px; padding-top:5px; ">New</span></a>
                                </div>
                                @endif
                                <div style="float: right; margin-left:9px">
                                    <a style=" float: right;text-decoration: none;cursor: pointer " class=" btn-print-invoice plain_id {{ $plan_view ?? 'd-none' }}"><span class="fa fa-reorder m-1" style="font-size:27px; color:SlateBlue;"></span><span style="float:right;margin:2px; padding-top:5px; ">{{ $plan_view ?? '' }}</span></a>
                                    <a style=" float: right;text-decoration: none;cursor: pointer " class=" btn-print-invoice  tree_id d-none {{ $plan_view ?? 'd-none' }}"><span class="fa fa-reorder m-1" style="font-size:27px; color:SlateBlue"></span><span style="float:right;margin:2px; padding-top:5px; ">{{ $tree_view ?? '' }}</span></a>
                                </div>
                                <div style="float: right; width:200px;">
                                    <input type="text" id="myInput" style="border-radius: 5px" class="form-control form-control pb-1" width="100%" placeholder="searching">
                                </div>
                            </div>
                            <hr style="margin-bottom: 0px;">
                        </div>
                    </div>
                </div>
                <!-- Page-body start -->
                <div class="page-body left-data">
                    <div class="row">
                        <div class="col-xl-12">
                            <!-- Zero config.table start -->
                            <div class="card ">
                                <div class="card-block table-responsive table_content">
                                    {{ $body }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" >
    <div class="center">
        <img alt="" src="{{asset('loding-image/loading_icon.gif')}}" />
    </div>
</div>
<script type="text/javascript" src="{{asset('js/jquerySortEelement.js')}}"></script>
<script type="text/javascript" src="{{asset('pageWiseSetting/page_wise_setting.js')}}"></script>
<script>
    page_wise_setting();

    function page_wise_setting() {
        $('input[type="checkbox"]').on('change', function(e) {
            if (e.target.click) {
                page_wise_setting_checkbox();
            }
        });

        $('.sort_by_asc').on('change', function(e) {
            if ($(".sort_by_asc").prop('checked') === true) {
                page_wise_setting_table_row_sort_by($(this).val())
            }
        });
        $('#sort_by_desc_1').on('change', function(e) {
            if ($("#sort_by_desc_1").prop('checked') === true) {
                page_wise_setting_table_row_sort_by($(this).val())
            }
        });
    }

    let scrollValue = 0;
    $('.tableFixHead').scroll(function() {
        scrollValue = $('.tableFixHead').scrollTop();
    });

    function set_scroll_table() {
        $(document).find('.tableFixHead').scrollTop(localStorage.getItem('scrollValue'));
        $(document).find(`.${localStorage.getItem('scrollValueId')}`).addClass('current-row');
        localStorage.setItem('scrollValue', 0);
    }

    function scroll_table() {
        if (scrollValue > 0) {
            localStorage.setItem('scrollValue', scrollValue);
        }
    }
    $(document).on('click', 'tr', function() {
        let id = $(this).closest('tr').attr('id');
        localStorage.setItem('scrollValueId', id);
        scroll_table();
    })
</script>