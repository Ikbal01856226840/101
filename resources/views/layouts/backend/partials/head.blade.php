<div id="pcoded" class="pcoded" pcoded-device-type="tablet" theme-layout="horizontal">
    <div class="pcoded-container">
        <!-- Menu header start -->
        <div class="fixed-top">
            <nav class="navbar header-navbar pcoded-header  d-print-none">
                <div class="navbar-wrapper">

                    <div class="navbar-logo">
                        <a class="mobile-menu" id="mobile-collapse" href="#!">
                            <i class="feather icon-menu"></i>
                        </a>
                        <a href="index-1.htm">
                            {{-- <img class="img-fluid" src="{{ asset('libraries/assets/images/logo.png') }}" alt="Theme-Logo"> --}}

                        </a>
                        {{-- <h5 style="text-decoration: none">Account</h5> --}}
                        <a class="mobile-options">
                            <i class="feather icon-more-horizontal"></i>
                        </a>
                    </div>

                    <div class="navbar-container container-fluid">
                        <ul class="nav-left">
                            {{-- <li class="header-search">
                                <div class="main-search morphsearch-search">
                                    <div class="input-group">
                                        <span class="input-group-addon search-close"><i class="feather icon-x"></i></span>
                                        <input type="text" class="form-control">
                                        <span class="input-group-addon search-btn"><i class="feather icon-search"></i></span>
                                    </div>
                                </div>
                            </li> --}}
                            <li>
                                <a href="#!" onclick="javascript:toggleFullScreen()">
                                    <i class="feather icon-maximize full-screen"></i>
                                </a>
                            </li>
                            <li>
                            <input class="form-control" type="hidden" id="current_company_mailing_address" value="{{ company()->mailing_address }}">
                                <h6 class="full-screen p-1" style="text-align: center">Welcome to <span id="current_company_name" class="me-2 fw-bold">{{ company()->company_name }}</span> Financial Year: {{date('d M Y', strtotime(company()->financial_year_start))  }} to {{date('d M Y', strtotime(company()->financial_year_end))  }}</h6>
                            </li>
                        </ul>
                        <ul class="nav-right">
                            <li class="user-profile header-notification px-0">
                                <div class="dropdown-primary dropdown user-popup">
                                    <div class="dropdown-toggle" data-toggle="dropdown">
                                        <span class="fw-bold">Settings</span>
                                        <i class="feather fs-6 fw-bold icon-settings"></i>
                                    </div>
                                    <ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                        <li class="refresh-account">
                                            <a>
                                                <i class="feather icon-lock"></i> Refresh Account
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="user-profile header-notification">
                                <div class="dropdown-primary dropdown">
                                    <div class="dropdown-toggle" data-toggle="dropdown">
                                        <img src="{{ asset('libraries/assets/images/defaultUserAvatarImages.png') }}" class="img-radius" alt="User-Profile-Image">
                                        <span>{{Auth::user()->user_name }}</span>
                                        <i class="feather icon-chevron-down"></i>
                                    </div>
                                    <ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                                onclick="event.preventDefault();
                                                                                    document.getElementById('logout-form').submit();">
                                                <i class="feather icon-log-out"></i> Logout
                                            </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>

                                        </li>
                                    </ul>

                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- Menu header end -->