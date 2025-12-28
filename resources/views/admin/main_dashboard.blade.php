
@extends('layouts.backend.app')
@section('title','MainDashboar')
@push('css')
@endpush
@section('admin_content')<br>
<div class="pcoded-main-container ">
    <p class='component-name d-none'>dashboard</p>
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">
                        <div class="page-body ">
                            <div class="row">
                                <!-- task, page, download counter  start -->
                                <div class="col-xl-3 col-md-6">
                                    <div class="card bg-c-yellow update-card">
                                        <div class="card-block">
                                            <div class="row align-items-end">
                                                <div class="col-8">
                                                    <h4 class="text-white">{{$group_chart}}</h4>
                                                    <h6 class="text-white m-b-0">All Group Chart</h6>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <canvas id="update-chart-1" height="50"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <p class="text-white m-b-0"><i class="feather icon-clock text-white f-14 m-r-10"></i>update : 2:15 am</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <div class="card bg-c-green update-card">
                                        <div class="card-block">
                                            <div class="row align-items-end">
                                                <div class="col-8">
                                                    <h4 class="text-white">{{$ledger_head}}</h4>
                                                    <h6 class="text-white m-b-0">Acount Ledger</h6>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <canvas id="update-chart-2" height="50"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <p class="text-white m-b-0"><i class="feather icon-clock text-white f-14 m-r-10"></i>update : 2:15 am</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <div class="card bg-c-pink update-card">
                                        <div class="card-block">
                                            <div class="row align-items-end">
                                                <div class="col-8">
                                                    <h4 class="text-white">{{$stock_group}}</h4>
                                                    <h6 class="text-white m-b-0">Stock Group</h6>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <canvas id="update-chart-3" height="50"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <p class="text-white m-b-0"><i class="feather icon-clock text-white f-14 m-r-10"></i>update : 2:15 am</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <div class="card bg-c-lite-green update-card">
                                        <div class="card-block">
                                            <div class="row align-items-end">
                                                <div class="col-8">
                                                    <h4 class="text-white">{{$stock_item}}</h4>
                                                    <h6 class="text-white m-b-0">Stock Item</h6>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <canvas id="update-chart-4" height="50"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <p class="text-white m-b-0"><i class="feather icon-clock text-white f-14 m-r-10"></i>update : 2:15 am</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- task, page, download counter  end -->
                                <!--  sale analytics start -->
                                <div class="col-xl-9 col-md-9 ">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Sales Analytics</h5>
                                            <div class="card-header-right">
                                                <ul class="list-unstyled card-option">
                                                    <li><i class="feather icon-maximize full-card"></i></li>
                                                    <li><i class="feather icon-minus minimize-card"></i></li>
                                                    <li><i class="feather icon-trash-2 close-card"></i></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-block table-responsive-md">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td>
                                                        <canvas id="barChart" width="400" height="155"></canvas>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-12">
                                    <div class="card user-card2">
                                        <div class="card-block text-center">
                                            <h6 class="m-b-15">Top Sales Return Customer</h6>
                                             <!-- <div class="risk-rate"> -->
                                              <canvas id="myChart" width="400" height="525"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <!--  sale analytics end -->
                                <div class="col-xl-8 col-md-12">
                                    <div class="card table-card">
                                        <div class="card-header">
                                            <h5>Top  Sales Customer</h5>
                                            <div class="card-header-right">
                                                <ul class="list-unstyled card-option">
                                                    <li><i class="feather icon-maximize full-card"></i></li>
                                                    <li><i class="feather icon-minus minimize-card"></i></li>
                                                    <li><i class="feather icon-trash-2 close-card"></i></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-block">
                                            <div class="table-responsive">
                                                <table class="table table-hover  table-borderless">
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                <div class="chk-option">
                                                                    <div class="checkbox-fade fade-in-primary">
                                                                        <label class="check-task">
                                                                            <input type="checkbox" value="">
                                                                            <span class="cr">
                                                                                <i class="cr-icon feather icon-check txt-default"></i>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                Application
                                                            </th>
                                                            <th>Sales</th>
                                                            <th>Change</th>
                                                            <th>Avg Price</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($top_sales as $key=>$top_sale)
                                                            <tr>
                                                                <td>
                                                                    <div class="chk-option">
                                                                        <div class="checkbox-fade fade-in-primary">
                                                                            <label class="check-task">
                                                                                <input type="checkbox" value="">
                                                                                <span class="cr">
                                                                                    <i class="cr-icon feather icon-check txt-default"></i>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-inline-block align-middle">
                                                                        <h6>{{$top_sale->ledger_name }}</h6>
                                                                    </div>
                                                                </td>
                                                                <td>{{$top_sale->stock_total_debit}}</td>
                                                                <td><canvas id="app-sale{{$key+1}}" height="50" width="100"></canvas></td>
                                                                <td>{{($top_sale->stock_total_debit/$top_sale->stock_total_count) }}</td>
                                                                <td class="text-c-blue">{{$top_sale->stock_total_debit}}</td>
                                                            </tr>
                                                            @endforeach
                                                    </tbody>
                                                </table>
                                                <div class="text-center">
                                                    <a href="#!" class=" b-b-primary text-primary">View all Projects</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-12">
                                    <div class="card user-activity-card">
                                        <div class="card-header">
                                            <h5>User Activity</h5>
                                        </div>
                                        <div class="card-block">
                                            @foreach ($users as $user)
                                            <div class="row m-b-25">
                                                <div class="col-auto p-r-0">
                                                    <div class="u-img">
                                                        <img src="{{asset('libraries\assets\images\defaultUserAvatarImages.png')}}" alt="user image" class="img-radius cover-img">
                                                        <!-- <img src="{{asset('libraries\assets\images\avatar-2.jpg')}}" alt="user image" class="img-radius profile-img"> -->
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <h6 class="m-b-5">{{$user->user_name }}</h6>
                                                    <p class="text-muted m-b-0">{{$user->address}}</p>
                                                    <p class="text-muted m-b-0"><i class="feather icon-clock m-r-10"></i>{{$user->created_at}}</p>
                                                </div>
                                            </div>
                                            @endforeach
                                            <div class="text-center">
                                                <a href="{{route('user-list-show')}}" class="b-b-primary text-primary">View all Projects</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- social download  start -->
                                <div class="col-xl-4 col-md-6">
                                    <div class="card social-card bg-simple-c-blue">
                                        <div class="card-block">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <i class="feather icon-mail f-34 text-c-blue social-icon"></i>
                                                </div>
                                                <div class="col">
                                                    <h6 class="m-b-0">Mail</h6>
                                                    <p>231.2w downloads</p>
                                                    <p class="m-b-0">Lorem Ipsum is simply dummy text of the printing</p>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="#!" class="download-icon"><i class="feather icon-arrow-down"></i></a>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="card social-card bg-simple-c-pink">
                                        <div class="card-block">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <i class="feather icon-twitter f-34 text-c-pink social-icon"></i>
                                                </div>
                                                <div class="col">
                                                    <h6 class="m-b-0">twitter</h6>
                                                    <p>231.2w downloads</p>
                                                    <p class="m-b-0">Lorem Ipsum is simply dummy text of the printing</p>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="#!" class="download-icon"><i class="feather icon-arrow-down"></i></a>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-12">
                                    <div class="card social-card bg-simple-c-green">
                                        <div class="card-block">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <i class="feather icon-instagram f-34 text-c-green social-icon"></i>
                                                </div>
                                                <div class="col">
                                                    <h6 class="m-b-0">Dropbox</h6>
                                                    <p>231.2w downloads</p>
                                                    <p class="m-b-0">Lorem Ipsum is simply dummy text of the printing</p>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="#!" class="download-icon"><i class="feather icon-arrow-down"></i></a>
                                    </div>
                                </div>
                                <!-- social download  end -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript" src="{{asset('libraries\assets\pages\dashboard\custom-dashboard.min.js')}}"></script>
 <!-- Chart js -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
 <!-- amchart js -->
 <script type="text/javascript" src="{{asset('libraries\assets\pages\dashboard\amchart\js\amcharts.js')}}"></script>
 <script type="text/javascript" src="{{asset('libraries\assets\pages\dashboard\amchart\js\serial.js')}}"></script>
 <script type="text/javascript" src="{{asset('libraries\assets\pages\dashboard\amchart\js\light.js')}}"></script>
 <script>
    function drawChart() {
        $.ajax({
                url: "{{url('top-sales-return-customer')}}",
                type: 'GET',
                dataType: 'json',
                    success: function(response) {

                        let maxvalue=[];
                        let lable=[];
                        let value=[];
                        for(i=0;i<response.data.length;i++){
                            lable.push(response.data[i]['ledger_name']);
                            value.push(response.data[i]['ratio']);

                        }

                        function my_max(_data){
                            var out = 0;
                            for(var key in _data){
                                out = Math.max(out, _data[key]['ratio']);
                            }
                            return out;
                        }
                    let max =my_max(response);
                    var gh;
                    for(i=0;i<response.data.length;i++){
                        if(max==response.data[i]['ratio']){
                            $('.max_ratio_name').text(response.data[i]['ledger_name']);
                            $('.max_ratio').text(response.data[i]['ratio']);
                        }

                        }

                        /*Doughnut chart*/
                        var ctx = document.getElementById("myChart");
                        var data = {
                            labels: lable,
                            datasets: [{
                                data: value,
                                backgroundColor: [
                                    "#1ABC9C",
                                    "#FCC9BA",
                                    "#B8EDF0",
                                    "#B4C1D7"
                                ],
                                borderWidth: [
                                    "0px",
                                    "0px",
                                    "0px",
                                    "0px"
                                ],
                                borderColor: [
                                    "#1ABC9C",
                                    "#FCC9BA",
                                    "#B8EDF0",
                                    "#B4C1D7"

                                ]
                            }]
                        };

                        var myDoughnutChart = new Chart(ctx, {
                            type: 'doughnut',
                            data: data
                        });
                    }

                })
        }
    drawChart();

    //get  all data show
    function dayWiseSales() {
        $.ajax({
            url: "{{ url('day-wise-sales')}}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let maxvalue=[];
                    let lable1=[];
                    let value1=[];
                    const today = new Date(); // Get current date
                    const lastMonthDate = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate()); // Get last month's date
                    // Loop from last month's date to current date
                    for (let date = lastMonthDate; date <= today; date.setDate(date.getDate() + 1)) {
                        let d=formatDate(date);
                        lable1.push(d);
                        let val=response.data.find(x=>x?.transaction_date==d) ||0;
                        value1.push(val?.stock_total_sales ||0);
                    }
                    /*Bar chart*/
                    var data1 = {
                        labels: lable1,
                        datasets: [{
                            label: "Sales Analysis",
                            backgroundColor: [
                                "#1ABC9C",
                                "#FCC9BA",
                                "#B8EDF0",
                                "#B4C1D7",
                                "#1ABC9C",
                                "#FCC9BA",
                                "#B8EDF0",
                                "#B4C1D7",
                                "#1ABC9C",
                                "#FCC9BA",
                                "#B8EDF0",
                                "#B4C1D7",
                                "#1ABC9C",
                                "#FCC9BA",
                                "#B8EDF0",
                                "#B4C1D7",
                                "#1ABC9C",
                                "#FCC9BA",
                                "#B8EDF0",
                                "#B4C1D7",
                                "#1ABC9C",
                                "#FCC9BA",
                                "#B8EDF0",
                                "#B4C1D7",
                                // 'rgba(95, 190, 170, 0.99)',
                                'rgba(93, 156, 236, 0.93)'
                            ],
                            hoverBackgroundColor: [
                                'rgba(26, 188, 156, 0.88)',
                                'rgba(26, 188, 156, 0.88)',
                                'rgba(26, 188, 156, 0.88)',
                                'rgba(26, 188, 156, 0.88)',
                                'rgba(26, 188, 156, 0.88)',
                                'rgba(26, 188, 156, 0.88)',
                                'rgba(26, 188, 156, 0.88)',
                                'rgba(26, 188, 156, 0.88)',
                                'rgba(26, 188, 156, 0.88)',
                                'rgba(26, 188, 156, 0.88)',
                                'rgba(26, 188, 156, 0.88)',
                                'rgba(26, 188, 156, 0.88)',
                                'rgba(26, 188, 156, 0.88)',
                                'rgba(26, 188, 156, 0.88)'
                            ],
                            data: value1,
                        }]
                    };

                    var bar = document.getElementById("barChart").getContext('2d');
                    var myBarChart = new Chart(bar, {
                        type: 'bar',
                        data: data1,
                        options: {
                            barValueSpacing: 20
                        }
                    });

            }
        })

    }
    dayWiseSales();
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Adding leading zero if necessary
        const day = String(date.getDate()).padStart(2, '0'); // Adding leading zero if necessary
        return `${year}-${month}-${day}`;
    }
</script>
@endpush
@endsection
