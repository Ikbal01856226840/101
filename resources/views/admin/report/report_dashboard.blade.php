
@extends('layouts.backend.app')
@section('title','Report Dashboard')
@push('css')
<!-- model style -->
<style>
    @media only screen and (max-width: 991px) {
        .main-body .page-wrapper .align-items-end .page-header-title,
        .main-body .page-wrapper .align-items-end .page-header-breadcrumb {
            margin-top: 0px !important;
        }
        .main-body .page-wrapper .page-header {
            margin-bottom: 0px;
        }
    }
</style>
@endpush
@section('admin_content')
<div class="pcoded-main-container navChild">
    <div class="pcoded-content  ">
        <div class="pcoded-inner-content  " >
                <!-- Main-body start -->
            <div class="main-body  side-component">
                <div class="page-wrapper m-t-0 p-0">
                <!-- Page-header start -->
                    <div class="page-header m-0 p-0">
                        <div class="row align-items-end">
                            <div class="col-lg-8">
                                <div class="page-header-title">
                                    <div class="d-inline ">
                                        <h4 class="text-center mx-auto"></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="page-header-breadcrumb">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Page-header end -->
                    <!-- Page body start -->
                    <div class="page-body left-data">
                            <!-- Basic Form Inputs card start -->
                            <div class="card">
                                <div class="row">
                                    <div class="col-md-4" >
                                            <div class="inventory_summary d-none" id="inventory_summary">
                                                @foreach ($inventory_summary as $row )
                                                    @if ($row->report_id==1)
                                                    <h5 style="background-color:#CCCCCC" class="text-center ">{{ $row->report_name }}</h5>
                                                    @else
                                                        @if(user_privileges_check('report',$row->report_title,'display_role'))
                                                        <li  class="m-1 " ><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{ url($row->page_name) }}" data-turbolinks="false">{{$row->report_name}}</a></li>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                            </ul>
                                            <div class="account_summary  d-none" id="account_summary">
                                                @foreach ($account_summary as $row )
                                                    @if ($row->report_id==5)
                                                    <h5 style="background-color:#CCCCCC" class="text-center">{{ $row->report_name }}</h5>
                                                    @else
                                                        @if(user_privileges_check('report',$row->report_title,'display_role'))
                                                        <li  class="m-1 " ><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{ url($row->page_name) }}" data-turbolinks="false">{{$row->report_name}}</a></li>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                           </ul>
                                           <div class="movement_analysis_1  d-none" id="movement_analysis_1">
                                                @foreach ($movement_analysis_1 as $row )
                                                    @if ($row->report_id==9)
                                                    <h5 style="background-color:#CCCCCC" class="text-center movement_analysis_1">{{ $row->report_name }}</h5>
                                                    @else
                                                        @if(user_privileges_check('report',$row->report_title,'display_role'))
                                                        <li  class="m-1 movement_analysis_1_check" ><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{ url($row->page_name) }}" data-turbolinks="false">{{$row->report_name}}</a></li>
                                                        @endif
                                                    @endif
                                                @endforeach
                                           </div>

                                          </ul>
                                    </div>
                                    <div class="col-md-4">
                                           <div class="general_reports  d-none" id="general_reports">
                                                @foreach ($general_reports as $row )
                                                    @if ($row->report_id==2)
                                                    <h5 style="background-color:#CCCCCC" class="text-center">{{ $row->report_name }}</h5>
                                                    @else
                                                        @if(user_privileges_check('report',$row->report_title,'display_role'))
                                                        <li  class="m-1 " ><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{ url($row->page_name) }}" data-turbolinks="false">{{$row->report_name}}</a></li>
                                                        @endif
                                                    @endif
                                                @endforeach
                                           </div>
                                            </ul>
                                            <div class="inventory_books  d-none" id="inventory_books">
                                                @foreach ($inventory_books as $row )
                                                    @if ($row->report_id==6)
                                                    <h5 style="background-color:#CCCCCC" class="text-center ">{{ $row->report_name }}</h5>
                                                    @else
                                                        @if(user_privileges_check('report',$row->report_title,'display_role'))
                                                        <li  class="m-1" ><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{ url($row->page_name) }}" data-turbolinks="false">{{$row->report_name}}</a></li>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                            </ul>
                                            <div class="movement_analysis_2  d-none" id="movement_analysis_2">
                                                @foreach ($movement_analysis_2 as $row )
                                                    @if ($row->report_id==3)
                                                    <h5 style="background-color:#CCCCCC" class="text-center movement_analysis_2">{{ $row->report_name }}</h5>
                                                    @else
                                                        @if(user_privileges_check('report',$row->report_title,'display_role'))
                                                        <li  class="m-1 movement_analysis_2_check" ><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{ url($row->page_name) }}" data-turbolinks="false">{{$row->report_name}}</a></li>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                            </ul>
                                    </div>
                                    <div class="col-md-4 ">
                                        <div class="company_statistics_check_2  d-none" id="company_statistics_check_2">
                                            @foreach ($company_statistics as $row )
                                                @if ($row->report_id==8)
                                                <h5 style="background-color:#CCCCCC" class="text-center">{{ $row->report_name }}</h5>
                                                @else
                                                    @if(user_privileges_check('report',$row->report_title,'display_role'))
                                                    <li  class="m-1" ><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{ url($row->page_name) }}" data-turbolinks="false">{{$row->report_name}}</a></li>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                        </ul>
                                         <div class="party_ledger  d-none" id="party_ledger">
                                            @foreach ($party_ledger as $row )
                                                @if ($row->report_id==4)
                                                <h5 style="background-color:#CCCCCC" class="text-center ">{{ $row->report_name }}</h5>
                                                @else
                                                    @if(user_privileges_check('report',$row->report_title,'display_role'))
                                                    <li  class="m-1" ><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{ url($row->page_name) }}" data-turbolinks="false">{{$row->report_name}}</a></li>
                                                    @endif
                                                @endif
                                            @endforeach
                                          </ul>
                                        </div>
                                        <div class="pos  d-none" id="pos">
                                            @foreach ($pos as $row )
                                                @if ($row->report_id==10)
                                                <h5 style="background-color:#CCCCCC" class="text-center movement_analysis_2">{{ $row->report_name }}</h5>
                                                @else
                                                    @if(user_privileges_check('report',$row->report_title,'display_role'))
                                                    <li  class="m-1 movement_analysis_2_check" ><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{ url($row->page_name) }}" data-turbolinks="false">{{$row->report_name}}</a></li>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                        </ul>
                                    </div>
                                <div>
                            </div>
                    </div>
                </div>
           </div>
       </div>
   </div>
</div>
@push('js')
<script>




    $(document).ready(function () {

        let inventory_summary=($('#inventory_summary').find('li').length);
        let account_summary=($('#account_summary').find('li').length);
        let movement_analysis_1=($('#movement_analysis_1').find('li').length);

        let general_reports=($('#general_reports').find('li').length);
        let inventory_books=($('#inventory_books').find('li').length);
        let movement_analysis_2=($('#movement_analysis_2').find('li').length);

        let company_statistics_check_2=($('#company_statistics_check_2').find('li').length);
        let party_ledger=($('#party_ledger').find('li').length);
        let pos=($('#pos').find('li').length);

        let left=[];
        let mid=[];
        let right=[];

        if(inventory_summary){
            $('#inventory_summary').removeClass('d-none');
            left.push('inventory_summary')
        }
        if(account_summary){
            $('#account_summary').removeClass('d-none');
            left.push('account_summary')
        }
        if(movement_analysis_1){
            $('#movement_analysis_1').removeClass('d-none');
            left.push('movement_analysis_1')
        }

        if(!inventory_summary && !account_summary && !movement_analysis_1){
            $('#inventory_summary').closest('.col-md-4').addClass('d-none');
        }

        if(general_reports){
            $('#general_reports').removeClass('d-none');
            mid.push('general_reports');
        }
        if(inventory_books){
            $('#inventory_books').removeClass('d-none');
            mid.push('inventory_books');
        }
        if(movement_analysis_2){
            $('#movement_analysis_2').removeClass('d-none');
            mid.push('movement_analysis_2');
        }

        if(!general_reports && !inventory_books && !movement_analysis_2){
            $('#general_reports').closest('.col-md-4').addClass('d-none');
        }

        if(company_statistics_check_2){
            $('#company_statistics_check_2').removeClass('d-none');
            right.push('company_statistics_check_2');
        }
        if(party_ledger){
            $('#party_ledger').removeClass('d-none');
            right.push('party_ledger');
        }
        if(pos){
            $('#pos').removeClass('d-none');
            right.push('pos');
        }



        const heights = {
            inventory_summary: $('#inventory_summary').height(),
            account_summary: $('#account_summary').height(),
            general_reports: $('#general_reports').height(),
            inventory_books: $('#inventory_books').height(),
            movement_analysis_1: $('#movement_analysis_1').height(),
            movement_analysis_2: $('#movement_analysis_2').height(),
            company_statistics_check_2: $('#company_statistics_check_2').height(),
            party_ledger: $('#party_ledger').height(),
            pos: $('#pos').height(),
        };

        function calculateMaxHeight(group) {
            return Math.max(...group.map(cls => heights[cls] || 0));
        }
        let MaxHeight=calculateMaxHeight([left[0]||'',mid[0]||'',right[0]||'']);
        if(left[0]||''){
            $(`#${left[0]}`).css('height',MaxHeight)
        }
        if(mid[0]||''){
            $(`#${mid[0]}`).css('height',MaxHeight)
        }
        if(right[0]||''){
            $(`#${right[0]}`).css('height',MaxHeight)
        }
        MaxHeight=calculateMaxHeight([left[1]||'',mid[1]||'',right[1]||'']);
        if(left[1]||''){
            $(`#${left[1]}`).css('height',MaxHeight)
        }
        if(mid[1]||''){
            $(`#${mid[1]}`).css('height',MaxHeight)
        }
        if(right[1]||''){
            $(`#${right[1]}`).css('height',MaxHeight)
        } 

});




</script>
@endpush
@endsection

