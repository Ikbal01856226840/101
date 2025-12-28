<style>
    .ui-button.ui-state-active:hover {
	border: 1px solid #2dcee3;
	background: #2dcee3;
	font-weight: normal;
	color: #ffffff;
}
</style>
<div  class="m-0 p-0" >
    <nav class="pcoded-navbar  d-print-none" pcoded-navbar-position="absolute" style="background-color: #ffff;">
        <div class="pcoded-inner-navbar">
                <ul class="pcoded-item pcoded-left-item " id="menu">
                    <li class="pcoded-hasmenu frist main-dashbaord" dropdown-icon="style1">
                        <a href="{{route('main-dashboard')}}" id="page0" >
                            <span class="p-3"><i class="ti-home"></i></span>
                            <span class="pcoded-mtext">Dashboard</span>
                            <span class="pcoded-mcaret"></span>
                            <span class="d-none text">dashboard-component</span>
                        </a>
                    </li>
                    {{-- @php
                         $user=user_privileges_role(Auth::user()->id,'master','User');
                    @endphp --}}
                    {{-- @if ($user?$user->create_role==1:'') --}}
                        <li class="pcoded-hasmenu user {{Route::is('user-dashboard') ? 'activedata' : ''}}"  dropdown-icon="style1">
                            <a href="{{route('user-dashboard') }}" id="page1" >
                                 <span class="p-3"><i class="feather icon-users"></i></span>
                                <span class="pcoded-mtext">User</span>
                                <span class="pcoded-mcaret"></span>
                                <span class="d-none text">user-component</span>
                            </a>
                        </li>
                    {{-- @endif --}}
                    @if(user_privileges_check('menu','tab_master_','create_role'))
                        <li class="pcoded-hasmenu master"  dropdown-icon="style1">
                            <a  href="{{route('master-dashboard')}}" id="page2">
                                 <span class="p-3"><i class="feather icon-box"></i></span>
                                <span class="pcoded-mtext">Master</span>
                                <span class="pcoded-mcaret"></span>
                                <span class="d-none text">master-component</span>
                            </a>
                        </li>
                    @endif
                    @if(user_privileges_check('menu','tab_voucher','create_role'))
                        <li class="pcoded-hasmenu voucher {{Route::is('voucher-dashboard') ? 'activedata' : ''}}"  dropdown-icon="style1" >
                            <a href="{{route('voucher-dashboard') }}" id="page3" data-turbolinks="false">
                             <span class="p-3"><i class="feather icon-clipboard"></i></span>
                            <span class="pcoded-mtext">Voucher</span>
                                <span class="pcoded-mcaret"></span>
                                <span class="d-none text">voucher-component</span>
                            </a>
                        </li>
                    @endif
                    @if(user_privileges_check('menu','tab_o_sheet','create_role'))
                        <li class="pcoded-hasmenu approve {{Route::is('show-approve-page') ? 'activedata' : ''}}"  dropdown-icon="style1">
                            <a href="{{route('show-approve-page')}}" id="page4" data-turbolinks="false">
                             <span class="p-3"><i class="feather icon-unlock"></i></span>
                            <span class="pcoded-mtext">Approve </span>
                                <span class="pcoded-mcaret"></span>
                                <span class="d-none text">approve-component</span>
                            </a>
                        </li>
                    @endif
                   @if(user_privileges_check('menu','tab_report_','create_role'))
                        <li class="pcoded-hasmenu report {{Route::is('report-dashboard') ? 'activedata' : ''}}"  dropdown-icon="style1">
                            <a href="{{route('report-dashboard')}}" id="page5">
                            <span class="p-3"><i class="feather icon-file-minus"></i></span>
                            <span class="pcoded-mtext">Reports</span>
                                <span class="pcoded-mcaret"></span>
                                <span class="d-none text">report-component</span>
                            </a>
                        </li>
                  @endif
                  @if(user_privileges_check('menu','tab_company','create_role'))
                    <li class="pcoded-hasmenu company {{Route::is('showCompany') ? 'activedata' : ''}}"  dropdown-icon="style1">
                        <a href="{{route('showCompany') }}" id="page6">
                         <span class="p-3"><i class="feather icon-briefcase"></i></span>
                        <span class="pcoded-mtext">Company</span>
                            <span class="pcoded-mcaret"></span>
                            <span class="d-none text">company-component</span>
                        </a>
                   </li>
                  @endif
              </ul>
        </div>
    </nav>
</div>
</div>
