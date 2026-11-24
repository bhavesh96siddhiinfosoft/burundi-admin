@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.owner_transaction_report') }}</h3>
            </div>

            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.owner_transaction_report') }}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card  pb-4">

                <div class="card-body">
                    <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{ trans('lang.processing') }}</div>
                    <div class="error_top"></div>

                    <div class="row restaurant_payout_create">
                        <div class="restaurant_payout_create-inner">
                            <fieldset>
                                <legend>{{ trans('lang.reports_transaction') }}</legend>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.select_owner') }}<span class="required-field"></span>
                                    </label>
                                    <div class="col-7">
                                        <select class="form-control user" id="owner">
                                            <option selected value="" disabled>{{ trans('lang.select_owner') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.select_fuel_type') }}<span class="required-field"></span>
                                    </label>
                                    <div class="col-7">
                                        <select class="form-control" id="fuelType">
                                            <option selected value="Petrol">{{ trans('lang.petrol') }}</option>
                                            <option value="Diesel">{{ trans('lang.diesel') }}</option>
                                            <option value="Other">{{ trans('lang.other') }}</option>
                                        </select>
                                    </div>
                                </div>
                                {{-- <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.select_employee') }}<span class="required-field"></span>
                                    </label>
                                    <div class="col-7">
                                        <select class="form-control user" id="employee">
                                            <option selected value="">{{ trans('lang.select_employee') }}</option>
                                        </select>
                                    </div>
                                </div> --}}

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.select_date') }}</label>
                                    <div class="col-7">
                                        <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                            <i class="fa fa-calendar"></i>&nbsp;
                                            <span></span> <i class="fa fa-caret-down"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row width-100">
                                    <label class="col-3 control-label">{{ trans('lang.file_format') }}<span class="required-field"></span></label>
                                    <div class="col-7">
                                        <select class="form-control file_format">
                                            <option value="">{{ trans('lang.file_format') }}</option>
                                            {{-- <option value="xls">{{trans('lang.xls')}}</option> --}}
                                            <option value="csv">{{ trans('lang.csv') }}</option>
                                            <option value="pdf">{{ trans('lang.pdf') }}</option>
                                        </select>
                                    </div>
                                </div>

                            </fieldset>
                        </div>
                    </div>

                    <div class="form-group col-12 text-center btm-btn">
                        <button type="button" onclick="generateReport()" class="btn btn-primary download-user-report"><i class="fa fa-save"></i> {{ trans('lang.download') }}</button>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/objectexporter.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script>
        var database = firebase.firestore();
        // var employeeRef = database.collection('users').where('role', '==', 'employee')
        var ownerRef = database.collection('users').where('role', '==', 'owner');
        $(document).ready(function() {

            ownerRef.get().then(function(snapShots) {

                if (snapShots.docs.length > 0) {
                    snapShots.docs.forEach((listval) => {
                        var data = listval.data();
                        $('#owner').append('<option value="' + data.id + '">' + data.fullName + '</option>');
                    });

                }
            });

            $('#owner').select2();

            /*employeeRef.get().then(function(snapShots) {
                if (snapShots.docs.length > 0) {
                    $('#employee').append('<option value="all" selected>{{ trans('lang.all_employee') }}</option>');
                    snapShots.docs.forEach((listval) => {
                        var data = listval.data();
                        $('#employee').append('<option value="' + data.id + '">' + data.fullName + '</option>');
                    });

                }
            });*/
            //$('#employee').select2();
            $(function() {

                var start = moment().subtract(29, 'days');
                var end = moment();

                function cb(start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }

                $('#reportrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                }, cb);

                cb(start, end);

            });


        });

        async function generateReport() {
            var batchSize = 1000;
            let currentBatch = 0;
            var transactionRef = database.collection('fuel_transaction');
            var resetHistoryRef = database.collection('owner_reset_history');
            //var customer = $("#customer :selected").val();
            var owner = $('#owner :selected').val();
            if (owner == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.select_owner') }}</p>");
                window.scrollTo(0, 0);
            }
            // var employee = $('#employee :selected').val();
            let start_date = moment($('#reportrange').data('daterangepicker').startDate).toDate();
            let end_date = moment($('#reportrange').data('daterangepicker').endDate).toDate();
            let fuelType = $('#fuelType :selected').val();
            transactionRef = transactionRef.where('ownerId', '==', owner).where('fuelType', '==', fuelType);
            resetHistoryRef = resetHistoryRef.where('ownerId', '==', owner).where('fuelType', '==', fuelType);
            /*if (employee != "all") {
                transactionRef = transactionRef.where('employeeId', '==', employee);
            }*/

            if (start_date != '') {
                transactionRef = transactionRef.where('createAt', '>=', start_date);
                resetHistoryRef = resetHistoryRef.where('createAt', '>=', start_date);
            }
            if (end_date != '') {
                transactionRef = transactionRef.where('createAt', '<=', end_date);
                resetHistoryRef = resetHistoryRef.where('createAt', '<=', end_date);
            }
            transactionRef = transactionRef.orderBy('createAt', 'asc');
            resetHistoryRef = resetHistoryRef.orderBy('createAt', 'asc');
            var fileFormat = $(".file_format :selected").val();

            if (fileFormat == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.file_format_error') }}</p>");
                window.scrollTo(0, 0);
            } else {

                //var headerArray = ['Id', 'Customer', 'Customer Phone', 'Vehicle Number', 'Employee', 'Date', 'Fuel', 'Fuel Type', 'Owner Current Quota', 'Owner Remaining Quota'];
                var headerArray = ['Id', 'Customer', 'Customer Phone', 'Vehicle Number', 'Employee', 'Fuel', 'Fuel Type', 'Total Quota Before Txn', 'Remaining Quota After Txn', 'Total Quota Before Reset', 'Remaning Quota When Reset', 'New Allocated Quota', 'Date'];
                var headers = [];
                var transactionData = [];
                if (fileFormat == 'xls' || fileFormat == 'csv') {
                    headers = headerArray
                    var script = document.createElement("script");
                    script.setAttribute("src", "https://unpkg.com/object-exporter@3.2.1/dist/objectexporter.min.js");
                    script.setAttribute("async", "false");
                    var head = document.head;
                    head.insertBefore(script, head.firstChild);
                } else {
                    for (var k = 0; k < headerArray.length; k++) {
                        headers.push({
                            alias: headerArray[k],
                            name: headerArray[k],
                            flex: 1,
                        });
                    }
                    var script = document.createElement("script");
                    script.setAttribute("src", "{{ asset('js/objectexporter.min.js') }}");
                    script.setAttribute("async", "false");
                    var head = document.head;
                    head.insertBefore(script, head.firstChild);
                }

                $(".error_top").html("");


                jQuery("#overlay").show();
                const fetchedData = await fetchBatches(transactionRef, resetHistoryRef, batchSize);

                if (fetchedData.length > 0) {

                   
                    ((fileFormat == "pdf") ? document.title = "transaction-report" : "");
                    
                    objectExporter({
                        type: fileFormat,
                        exportable: fetchedData,
                        headers: headers,
                        columnSeparator: ',',
                        fileName: 'transaction-report',
                        headerStyle: 'font-weight: bold; padding: 5px; border: 1px solid #dddddd;',
                        cellStyle: 'border: 1px solid lightgray; margin-bottom: -1px;',
                        documentTitle: '',
                        sheetName: 'transaction-report'
                    });
                    currentBatch++;
                    jQuery("#overlay").hide();
                    $('.file_format option[value=""]').attr('selected', 'selected');
                } else {
                    jQuery("#overlay").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.not_found_data_error') }}</p>");
                    window.scrollTo(0, 0);
                }
                /*});
                } else {
                    console.error("No data found");
                }*/
            }
        }
        async function fetchBatches(transactionRef, resetHistoryRef, batchSize) {

            let transactionData = [];
            let resetHistoryData = [];
            let lastDoc = null;
            let lastHistoryDoc = null;
            const userCache = {};
            var historyClear=false;
            do {
                let query = transactionRef.limit(batchSize);
                let historyQuery = resetHistoryRef.limit(batchSize);
                if (lastDoc) {
                    query = query.startAfter(lastDoc);
                }
                if (lastHistoryDoc) {
                    historyQuery = historyQuery.startAfter(lastHistoryDoc);
                }

                const querySnapshot = await query.get();
                const historySnapshot = await historyQuery.get();
                if (!querySnapshot.empty) {
                    var transactions = await getData(querySnapshot, userCache, 'Transaction');
                    transactionData.push(...transactions);

                    lastDoc = querySnapshot.docs[querySnapshot.docs.length - 1];
                } else {
                    lastDoc = null;
                }
                if (!historySnapshot.empty && !historyClear) {
                   
                    const historyRecords = await getData(historySnapshot, userCache, 'Reset');
                    resetHistoryData.push(...historyRecords);
                    if(historySnapshot.docs.length>batchSize){
                        lastHistoryDoc = historySnapshot.docs[historySnapshot.docs.length - 1];
                    }else{
                        historyClear=true;
                    }
                } else {
                    lastHistoryDoc = null;
                }

            } while (lastDoc || lastHistoryDoc);
            let mergedData = [...transactionData, ...resetHistoryData];
            mergedData.sort((a, b) => a.createdAt - b.createdAt);
            return mergedData;
        }
        async function getData(querySnapshot, userCache, type) {
            var transactionData = [];
            let uniqueData = [];
            let idSet = new Set();
            await Promise.all(querySnapshot.docs.map(async (doc) => {
                var userObj = doc.data();
                var newUserObj = {};
                var date = userObj.createAt.toDate().toDateString();
                var time = userObj.createAt.toDate().toLocaleTimeString('en-US');
                var dateTime = date + " " + time;
                if (type == "Transaction") {
                    const ownerName = await getCachedUserData(userObj.ownerId, userCache);
                    const employeeName = await getCachedUserData(userObj.employeeId, userCache);
                    newUserObj['Id'] = (userObj.id).substring(0, 7);
                    newUserObj['Customer'] = (userObj.driverName) ? userObj.driverName : '';
                    newUserObj['Customer Phone'] = userObj.driverMobileNo;
                    newUserObj['Vehicle Number'] = userObj.driverVehicleNumber;
                   
                    newUserObj['Employee'] = employeeName;

                    newUserObj['Fuel'] = userObj.fuelInLiter;
                    newUserObj['Fuel Type'] = userObj.fuelType;
                   
                    if (userObj.hasOwnProperty('owner')) {
                        newUserObj['Total Quota Before Txn'] = userObj.owner.currentQuota;
                        newUserObj['Remaining Quota After Txn'] = userObj.owner.availableQuota;
                    } else {
                        newUserObj['Total Quota Before Txn'] = '';
                        newUserObj['Remaining Quota After Txn'] = '';
                    }
                    newUserObj['Total Quota Before Reset'] = '';
                    newUserObj['Remaning Quota When Reset'] = '';
                    newUserObj['New Allocated Quota'] = '';
                    newUserObj['Date'] = dateTime;
                } else {

                    newUserObj['Id'] = (userObj.id).substring(0, 7);
                    newUserObj['Customer'] = '--Reset By Admin--';
                    newUserObj['Customer Phone'] = '';
                    newUserObj['Vehicle Number'] = '';
                    newUserObj['Employee'] = '';
                    newUserObj['Fuel'] = '';
                    newUserObj['Fuel Type'] = userObj.fuelType;
                    newUserObj['Total Quota Before Txn'] = '';
                    newUserObj['Remaining Quota After Txn'] = '';
                    newUserObj['Total Quota Before Reset'] = userObj.lastAllocatedFuel;
                    newUserObj['Remaning Quota When Reset'] = userObj.lastRemainingFuel;
                    newUserObj['New Allocated Quota'] = userObj.newAllocatedFuel;
                    newUserObj['Date'] = dateTime;
                }
                transactionData.push(newUserObj);
               
            }));
            return transactionData;
        }

        async function getCachedUserData(userId, userCache) {
            if (!userId) return '';
            if (userCache[userId]) return userCache[userId];
            const userData = await getUserData(userId);
            userCache[userId] = userData || '';
            return userCache[userId];
        }

        async function getUserData(id) {


            var userName = '';
            await database.collection('users').where("id", "==", id).get().then(async function(snapshotss) {

                if (snapshotss.docs[0]) {
                    var user_data = snapshotss.docs[0].data();
                    userName = user_data.fullName;
                }
            });
            return userName;
        }
    </script>
@endsection
