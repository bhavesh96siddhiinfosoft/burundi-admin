@extends('layouts.app')
@section('content')
<div id="main-wrapper" class="page-wrapper" style="min-height: 207px;">

    <div class="container-fluid">

        <div id="data-table_processing" class="dataTables_processing panel panel-default"
            style="display: none;margin-top:20px;">{{ trans('lang.processing') }}
        </div>

        <!-- <div class="card mb-3 business-analytics">
            <div class="card-body">
                <div class="row flex-between align-items-center g-2 mb-3 order_stats_header">
                    <div class="col-sm-6">
                        <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                            {{ trans('lang.dashboard_today_data') }}
                        </h4>
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end justify-content-start">

                        <input type="text"
                            id="dashboardDateRange"
                            class="form-control d-inline-block"
                            style="max-width:280px;"
                            placeholder="Select Date Range">

                    </div>
                </div>
                
                <div class="row business-analytics_list">

                    <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{{route('fuel-transaction')}}'">
                        <div class="card-box">
                            <h5>{{ trans('lang.total_transaction') }}</h5>
                            <h2 id="total_transaction_today"></h2>
                            <i class="mdi mdi-check-circle"></i>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{{route('customers')}}'">
                        <div class="card-box">
                            <h5>{{ trans('lang.total_customers') }}</h5>
                            <h2 id="total_customers_today"></h2>
                            <i class="mdi mdi-account-multiple"></i>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{{route('owners')}}'">
                        <div class="card-box">
                            <h5>{{ trans('lang.total_owners') }}</h5>
                            <h2 id="total_owners_today"></h2>
                            <i class="mdi mdi-account-multiple"></i>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{{route('employee')}}'">
                        <div class="card-box">
                            <h5>{{ trans('lang.total_employee') }}</h5>
                            <h2 id="total_employee_today"></h2>
                            <i class="mdi mdi-account-multiple"></i>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{{route('fuel-transaction')}}'">
                        <div class="card-box">
                            <h5>{{ trans('lang.transaction_approved') }}</h5>
                            <h2 id="total_transaction_approved_today"></h2>
                            <i class="mdi mdi-account-multiple"></i>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{{route('fuel-transaction')}}'">
                        <div class="card-box">
                            <h5>{{ trans('lang.transaction_cancelled') }}</h5>
                            <h2 id="total_transaction_cancelled_today"></h2>
                            <i class="mdi mdi-account-multiple"></i>
                        </div>
                    </div>


                    <div class="col-sm-6 col-lg-3 mb-3 d-none">
                        <div class="card-box">
                            <h2 id="earnings_count_today"></h2>
                            <i class="mdi mdi-cash-usd"></i>
                        </div>
                    </div>

                </div>

            </div>


        </div> -->

        <div class="card mb-3 business-analytics">

            <div class="card-body">
                <div class="row flex-between align-items-center g-2 mb-3 order_stats_header">
                    <div class="col-sm-6">
                        <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                            {{ trans('lang.dashboard_total_data') }}
                        </h4>
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end justify-content-start">

                        <input type="text"
                            id="dashboardDateRange"
                            class="form-control d-inline-block"
                            style="max-width:280px;"
                            placeholder="Select Date Range">

                    </div>
                </div>

                <div class="row business-analytics_list">

                    <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{{route('fuel-transaction')}}'">
                        <div class="card-box">
                            <h5>{{ trans('lang.total_transaction') }}</h5>
                            <h2 id="total_transaction"></h2>
                            <i class="mdi mdi-check-circle"></i>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{{route('customers')}}'">
                        <div class="card-box">
                            <h5>{{ trans('lang.total_customers') }}</h5>
                            <h2 id="total_customers"></h2>
                            <i class="mdi mdi-account-multiple"></i>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{{route('owners')}}'">
                        <div class="card-box">
                            <h5>{{ trans('lang.total_owners') }}</h5>
                            <h2 id="total_owners"></h2>
                            <i class="mdi mdi-account-multiple"></i>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{{route('employee')}}'">
                        <div class="card-box">
                            <h5>{{ trans('lang.total_employee') }}</h5>
                            <h2 id="total_employee"></h2>
                            <i class="mdi mdi-account-multiple"></i>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{{route('fuel-transaction')}}'">
                        <div class="card-box">
                            <h5>{{ trans('lang.transaction_approved') }}</h5>
                            <h2 id="total_transaction_approved"></h2>
                            <i class="mdi mdi-account-multiple"></i>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-3" onclick="location.href='{{route('fuel-transaction')}}'">
                        <div class="card-box">
                            <h5>{{ trans('lang.transaction_cancelled') }}</h5>
                            <h2 id="total_transaction_cancelled"></h2>
                            <i class="mdi mdi-account-multiple"></i>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mb-3 d-none">
                        <div class="card-box">
                            <h2 id="total_earnings_count"></h2>
                            <i class="mdi mdi-cash-usd"></i>
                        </div>
                    </div>

                </div>

            </div>


        </div>

        <div class="row">

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">{{ trans('lang.total_transaction') }}</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="position-relative">
                            <canvas id="sales-chart" height="200"></canvas>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2"> <i class="fa fa-square" style="color:#80b140"></i>
                                {{ trans('lang.dashboard_this_year') }} </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">{{ trans('lang.dashboard_service_overview') }}</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex-row">
                            <canvas id="service-overview" height="222"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">{{ trans('lang.dashboard_sales_overview') }}</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex-row">
                            <canvas id="sales-overview" height="222"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="{{ asset('js/chart.js') }}"></script>
<script>
    //$('#overlay').show();
    var database = firebase.firestore();
    const todayDate = new Date();
    todayDate.setHours(0, 0, 0, 0);
    var txn_data = [];
    var labels = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV',
        'DEC'
    ];
    var startDate = moment().startOf('day');
    var endDate = moment().endOf('day');
    $(document).ready(function () {  
        $('#dashboardDateRange').daterangepicker({
            startDate: startDate,
            endDate: endDate,
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD'
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [
                    moment().subtract(1, 'days'),
                    moment().subtract(1, 'days')
                ],
                'Last 7 Days': [
                    moment().subtract(6, 'days'),
                    moment()
                ],
                'Last 30 Days': [
                    moment().subtract(29, 'days'),
                    moment()
                ],
                'This Month': [
                    moment().startOf('month'),
                    moment().endOf('month')
                ],

                'Last Month': [
                    moment().subtract(1, 'month').startOf('month'),
                    moment().subtract(1, 'month').endOf('month')
                ]

            }

        }, function(start, end, label) {

            if(label && label !== "Custom Range"){
                $('#dashboardDateRange').val(label);
            }else{
                $('#dashboardDateRange').val(
                    start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD')
                );
            }
            startDate = start;
            endDate = end;

            var startFirestore = start.startOf('day').toDate();
            var endFirestore = end.endOf('day').toDate();

            console.log(startFirestore);
            console.log(endFirestore);

            loadDashboardData(startFirestore, endFirestore);
        });
        var defaultStart = startDate.clone().startOf('day').toDate();
        var defaultEnd = endDate.clone().endOf('day').toDate();
        loadDashboardData(defaultStart, defaultEnd);
        $('#dashboardDateRange').val('Today');
    });
    function loadDashboardData(startFirestore, endFirestore){
        $('#overlay').show();
        // database.collection('users').where('role', '==', 'customer').where('createdAt', '>=', startFirestore).where('createdAt', '<=', endFirestore).get().then((snapshot) => {

        //     jQuery("#total_customers_today").empty();
        //     jQuery("#total_customers_today").text(snapshot.docs.length);
        // });
        // database.collection('users').where('role', '==', 'employee').where('createdAt', '>=', startFirestore).where('createdAt', '<=', endFirestore).get().then((snapshot) => {

        //     jQuery("#total_employee_today").empty();
        //     jQuery("#total_employee_today").text(snapshot.docs.length);
        // });
        // database.collection('users').where('role', '==', 'owner').where('createdAt', '>=', startFirestore).where('createdAt', '<=', endFirestore).get().then((snapshot) => {

        //     jQuery("#total_owners_today").empty();
        //     jQuery("#total_owners_today").text(snapshot.docs.length);
        // });
        // database.collection('fuel_transaction').where('createAt', '>=', startFirestore).where('createAt', '<=', endFirestore).get().then((snapshot) => {

        //     jQuery("#total_transaction_today").empty();
        //     jQuery("#total_transaction_today").text(snapshot.docs.length);
        // });
        // database.collection('fuel_transaction').where('createAt', '>=', startFirestore).where('createAt', '<=', endFirestore).where('status', '==', 'approved').get().then((snapshot) => {
        //     jQuery("#total_transaction_approved_today").empty();
        //     jQuery("#total_transaction_approved_today").text(snapshot.docs.length);
        // });
        // database.collection('fuel_transaction').where('createAt', '>=', startFirestore).where('createAt', '<=', endFirestore).where('status', '==', 'cancelled').get().then((snapshot) => {
        //     jQuery("#total_transaction_cancelled_today").empty();
        //     jQuery("#total_transaction_cancelled_today").text(snapshot.docs.length);
        // });
        database.collection('users').where('createdAt', '>=', startFirestore).where('createdAt', '<=', endFirestore).where('role', '==', 'customer').get().then((snapshot) => {

            jQuery("#total_customers").empty();
            jQuery("#total_customers").text(snapshot.docs.length);
        });
        database.collection('users').where('createdAt', '>=', startFirestore).where('createdAt', '<=', endFirestore).where('role', '==', 'employee').get().then((snapshot) => {

            jQuery("#total_employee").empty();
            jQuery("#total_employee").text(snapshot.docs.length);
        });

        database.collection('users').where('createdAt', '>=', startFirestore).where('createdAt', '<=', endFirestore).where('role', '==', 'owner').get().then((snapshot) => {

            jQuery("#total_owners").empty();
            jQuery("#total_owners").text(snapshot.docs.length);
        });
        database.collection('fuel_transaction').where('createAt', '>=', startFirestore).where('createAt', '<=', endFirestore).where('status', '==', 'approved').get().then((snapshot) => {
            jQuery("#total_transaction_approved").empty();
            jQuery("#total_transaction_approved").text(snapshot.docs.length);
        });
        database.collection('fuel_transaction').where('createAt', '>=', startFirestore).where('createAt', '<=', endFirestore).where('status', '==', 'cancelled').get().then((snapshot) => {
            jQuery("#total_transaction_cancelled").empty();
            jQuery("#total_transaction_cancelled").text(snapshot.docs.length);
        });
        database.collection('fuel_transaction').where('createAt', '>=', startFirestore).where('createAt', '<=', endFirestore).get().then((snapshot) => {

            jQuery("#total_transaction").empty();
            jQuery("#total_transaction").text(snapshot.docs.length);
            if (snapshot.docs.length > 0) {
              
                var v01 = 0;
                var v02 = 0;
                var v03 = 0;
                var v04 = 0;
                var v05 = 0;
                var v06 = 0;
                var v07 = 0;
                var v08 = 0;
                var v09 = 0;
                var v10 = 0;
                var v11 = 0;
                var v12 = 0;
                var currentYear = new Date().getFullYear();
                snapshot.docs.forEach(listval => {
                    var transactionData = listval.data();
                    if (transactionData.createAt) {
                        var transactionMonth = transactionData.createAt.toDate().getMonth() + 1;
                        var transactionYear = transactionData.createAt.toDate().getFullYear();
                       
                        if (currentYear == transactionYear) {
                            switch (parseInt(transactionMonth)) {
                                case 1:

                                    v01 = parseFloat(v01) + 1;
                                    break;
                                case 2:
                                    v02 = parseFloat(v02) + 1;
                                    break;
                                case 3:
                                    v03 = parseFloat(v03) + 1;
                                    break;
                                case 4:
                                    v04 = parseFloat(v04) + 1;
                                    break;
                                case 5:
                                    v05 = parseFloat(v05) + 1;
                                    break;
                                case 6:
                                    v06 = parseFloat(v06) + 1;
                                    break;
                                case 7:
                                    v07 = parseFloat(v07) + 1;
                                    break;
                                case 8:
                                    v08 = parseFloat(v08) + 1;
                                    break;
                                case 9:
                                    v09 = parseFloat(v09) + 1;
                                    break;
                                case 10:
                                    v10 = parseFloat(v10) + 1;
                                    break;
                                case 11:
                                    v11 = parseFloat(v11) + 1;
                                    break;
                                default:
                                    v12 = parseFloat(v12) + 1;
                                    break;
                            }
                        }
                    }

                });
                txn_data = [v01, v02, v03, v04, v05, v06, v07, v08, v09, v10, v11, v12];

            }
        });
        var $salesChart = $('#sales-chart');
        setTimeout(() => {
            salesChart($salesChart, txn_data, labels);
            serviceOverview();
            salesOverview();
            $('#overlay').hide();

        }, 3000);
    }
    function serviceOverview() {
        const data = {
            labels: [
                "{{ trans('lang.total_transaction') }}",
                "{{ trans('lang.total_owners') }}",
                "{{ trans('lang.total_customers') }}",
                "{{ trans('lang.total_employee') }}",

            ],
            datasets: [{
                data: [
                    jQuery("#total_transaction").text(),
                    jQuery("#total_owners").text(),
                    jQuery("#total_customers").text(),
                    jQuery("#total_employee").text(),

                ],
                backgroundColor: [
                    '#218be1',
                    '#80b140',
                    '#FF0000',
                    '#FFAB2E',

                ],
                hoverOffset: 4
            }]
        };

        return new Chart('service-overview', {
            type: 'doughnut',
            data: data,
            options: {
                maintainAspectRatio: false,
            }
        });
    }
    function salesOverview() {

        const data = {
            labels: [
                "{{ trans('lang.total_transaction') }}",
                "{{ trans('lang.transaction_approved') }}",
                "{{ trans('lang.transaction_cancelled') }}",
            ],
            datasets: [{
                data: [
                    jQuery("#total_transaction").text(),
                    jQuery("#total_transaction_approved").text(),
                    jQuery("#total_transaction_cancelled").text(),
                ],
                backgroundColor: [
                    '#80b140',
                    '#000000',
                    '#5865F2',
                ],
                hoverOffset: 3
            }]
        };
        return new Chart('sales-overview', {
            type: 'doughnut',
            data: data,
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    callbacks: {
                        label: function (tooltipItems, data) {
                            return data.labels[tooltipItems.index] + ': ' + data.datasets[
                                0].data[tooltipItems.index];
                        }
                    }
                }
            }
        })
    }
    function salesChart(chartNode, txn_data, labels) {
        console.log(txn_data);
        var ticksStyle = {
            fontColor: '#666',
            fontStyle: 'bold'
        };

        var mode = 'index';
        var intersect = true;
        return new Chart(chartNode, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: "{{ trans('lang.total_transaction') }}",
                    backgroundColor: '#80b140',
                    data: txn_data
                },

                ]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    mode: mode,
                    intersect: intersect,
                    callbacks: {
                        label: function (tooltipItem, data) {
                            let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                            return parseFloat(value).toFixed(2);
                        }
                    },
                },
                hover: {
                    mode: mode,
                    intersect: intersect
                },
                legend: {
                    display: true
                },
                scales: {
                    yAxes: [{
                        // display: false,
                        gridLines: {
                            display: true,
                            lineWidth: '4px',
                            color: 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks: $.extend({
                            beginAtZero: true,
                            callback: function (value, index, values) {
                                return parseFloat(value).toFixed(2);
                            }


                        }, ticksStyle)
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: ticksStyle
                    }]
                }
            }
        })
    }

</script>
@endsection