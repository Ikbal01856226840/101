<div class="pcoded-main-container navChild">
    <div class="pcoded-content  ">
        <div class="pcoded-inner-content  " >
                <!-- Main-body start -->
            <div class="main-body  side-component">
                <div class="page-wrapper m-t-0 p-0">
                <!-- Page-header start -->
                <div class="page-header m-2 p-0">
                    <div class="row align-items-end" style="margin-bottom: 0%px !important;" >
                        <div class="col-lg-8 ">
                            <div class="page-header-title m p-0" style="margin-bottom:7px !important;">
                            <div class="d-inline ">
                                <h4 style="color: green;font-weight: bold;">{{ $title ?? '' }}</h4>
                            </div>
                            </div>
                        </div>
                        <div class="col-lg-4 " >
                            <div style="float: right; margin-left: 5px;"  >
                                <a  style=" float:righttext-decoration: none; " class="{{$help_route ?? 'd-none' }}"  href="{{ $help_route ?? '' }}"><span class="fa fa-info-circle m-1" style="font-size:27px; color:#00b8e6;"></span><span style="float:right;margin:2px; padding-top:5px; color: color: white;#">Help</span></a>
                            </div>
                            <div style="float: right;margin-left:9px" >
                                <a  style=" float:righttext-decoration: none; " class="{{$close_route ?? 'd-none' }}" href="{{ $close_route ?? '' }}"><span class="fa fa-times-circle-o m-1" style="font-size:27px; color:#ff6666;"></span><span style="float:right;margin:2px; padding-top:5px; ">Close</span></a>
                            </div>
                            <div style="float: right; margin-left:9px">
                                <a  style=" float: righttext-decoration: none; " class="{{$veiw_route ?? 'd-none' }}"  href="{{ $veiw_route ?? '' }}"><span class="fa fa-eye m-1" style="font-size:27px; color:#00b8e6;"></span><span style="float:right;margin:2px; padding-top:5px; ">View</span></a>
                            </div>
                        </div>
                            <hr style="margin-bottom: 0px;">
                    </div>
                </div>
                    <!-- Page body start -->
                    <div class="page-body left-data">
                            <!-- Basic Form Inputs card start -->
                          <form action="{{ $action ?? ''}}" class="{{ $form_class ?? '' }}"  method="POST" id="{{ $form_id ?? '' }}" enctype="multipart/form-data">
                                @csrf
                             {{ method_field($method) }}
                            <div class="card">
                                {{ $body }}
                            </div>
                             <div class="row">
                                {{ $footer }}
                            </div>
                                <!-- Input Alignment card end -->
                        </form>
                    </div>
                </div>
           </div>
       </div>
   </div>
</div>
