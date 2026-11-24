@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.reports_transaction') }}</h3>
            </div>

            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.reports_transaction') }}</li>
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
                                    <label class="col-3 control-label">{{ trans('lang.select_customer') }}<span class="required-field"></span>
                                    </label>
                                    <div class="col-7">
                                        <select class="form-control user" id="customer">
                                            <option selected value="all">{{ trans('lang.all') }}</option>
                                            <option value="specific">{{ trans('lang.specific_customer') }}</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="form-group row width-50  d-none" id="vehicleNumber_div">
                                    <label class="col-3 control-label">{{ trans('lang.vehicle_number') }}<span class="required-field"></span>
                                    </label>
                                    <div class="col-7">
                                        <input type="text" id="vehicle_number" class="form-control">
                                    </div>

                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.select_owner') }}<span class="required-field"></span>
                                    </label>
                                    <div class="col-7">
                                        <select class="form-control user" id="owner">
                                            <option selected value="">{{ trans('lang.select_owner') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.select_employee') }}<span class="required-field"></span>
                                    </label>
                                    <div class="col-7">
                                        <select class="form-control user" id="employee">
                                            <option selected value="">{{ trans('lang.select_employee') }}</option>
                                        </select>
                                    </div>
                                </div>

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
        // 24-hour throughout: unambiguous in the exported file and consistent with
        // the francophone locale the panel runs in.
        var RANGE_DISPLAY_FORMAT = 'MMMM D, YYYY HH:mm';
        // Commas are deliberately avoided - the CSV writer joins values without
        // quoting them, so a comma here would split into an extra column.
        var PERIOD_LABEL_FORMAT = 'DD MMM YYYY HH:mm';
        var PERIOD_FILE_FORMAT = 'YYYY-MM-DD_HHmm';

        // Litre figures are written by the app as strings ("70", "9498.00"), so every
        // comparison has to parse first. Left as strings they compare lexicographically,
        // where "100" < "70" is true.
        var TOLERANCE = 0.001;

        function toFuelNumber(value) {
            if (value === null || value === undefined || value === '') {
                return 0;
            }
            var parsed = parseFloat(String(value).trim());
            return isNaN(parsed) ? 0 : parsed;
        }

        // toFixed never emits a thousands separator, which matters because the CSV
        // writer joins values without quoting them.
        function fmtFuel(value) {
            if (!isFinite(value)) {
                return '';
            }
            return value.toFixed(2);
        }

        var database = firebase.firestore();
        var employeeRef = database.collection('users').where('role', '==', 'employee')
        var ownerRef = database.collection('users').where('role', '==', 'owner');
        var customerRef = database.collection('users').where('role', '==', 'customer');
        $(document).ready(function() {
            $('#customer').on('change', function() {
                var customer = $(this).val();
                if (customer == 'specific') {
                    $('#vehicleNumber_div').removeClass('d-none');
                } else {
                    $('#vehicleNumber_div').addClass('d-none');
                }
            });

            ownerRef.get().then(function(snapShots) {

                if (snapShots.docs.length > 0) {
                    $('#owner').append('<option value="all" selected>{{ trans('lang.all_owner') }}</option>');
                    snapShots.docs.forEach((listval) => {
                        var data = listval.data();
                        $('#owner').append('<option value="' + data.id + '">' + data.fullName + '</option>');
                    });

                }
            });

            $('#owner').select2();
            employeeRef.get().then(function(snapShots) {
                if (snapShots.docs.length > 0) {
                    $('#employee').append('<option value="all" selected>{{ trans('lang.all_employee') }}</option>');
                    snapShots.docs.forEach((listval) => {
                        var data = listval.data();
                        $('#employee').append('<option value="' + data.id + '">' + data.fullName + '</option>');
                    });

                }
            });
            $('#employee').select2();
            $(function() {

                var start = moment().subtract(29, 'days').startOf('day');
                var end = moment().endOf('day');

                function cb(start, end) {
                    $('#reportrange span').html(start.format(RANGE_DISPLAY_FORMAT) + ' - ' + end.format(RANGE_DISPLAY_FORMAT));
                }

                $('#reportrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    // Time is part of the filter, so the picker must expose it.
                    timePicker: true,
                    timePicker24Hour: true,
                    timePickerIncrement: 1,
                    locale: {
                        format: RANGE_DISPLAY_FORMAT
                    },
                    // With timePicker on, daterangepicker no longer snaps a range to
                    // the whole day, so every preset has to state its own boundaries.
                    ranges: {
                        'Today': [moment().startOf('day'), moment().endOf('day')],
                        'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
                        'Last 7 Days': [moment().subtract(6, 'days').startOf('day'), moment().endOf('day')],
                        'Last 30 Days': [moment().subtract(29, 'days').startOf('day'), moment().endOf('day')],
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
            var customer = $("#customer :selected").val();
            var owner = $('#owner :selected').val();
            var employee = $('#employee :selected').val();
            let start_moment = moment($('#reportrange').data('daterangepicker').startDate);
            let end_moment = moment($('#reportrange').data('daterangepicker').endDate);
            // Carries the selected time of day, not just the date.
            let start_date = start_moment.toDate();
            // The picker builds the end time from hour/minute dropdowns, so seconds
            // are 0. Without this, choosing 18:00 would drop a sale at 18:00:30.
            let end_boundary = end_moment.clone();
            if (end_boundary.second() === 0 && end_boundary.millisecond() === 0) {
                end_boundary.second(59).millisecond(999);
            }
            let end_date = end_boundary.toDate();

            var periodLabel = start_moment.format(PERIOD_LABEL_FORMAT) + ' to ' + end_moment.format(PERIOD_LABEL_FORMAT);
            var periodFileName = 'transaction-report_' + start_moment.format(PERIOD_FILE_FORMAT) +
                '_to_' + end_moment.format(PERIOD_FILE_FORMAT);
            if (owner != "all") {
                transactionRef = transactionRef.where('ownerId', '==', owner);
            }
            if (employee != "all") {
                transactionRef = transactionRef.where('employeeId', '==', employee);
            }

            if (start_date != '') {
                transactionRef = transactionRef.where('createAt', '>=', start_date);
            }
            if (end_date != '') {
                transactionRef = transactionRef.where('createAt', '<=', end_date);
            }
            var fileFormat = $(".file_format :selected").val();

            var vehicle_number = $('#vehicle_number').val();
            if (customer != "all") {
                if (vehicle_number == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.vehicleno_help') }}</p>");
                    window.scrollTo(0, 0);
                    return false;
                } else {
                    vehicle_number = vehicle_number.toString().toUpperCase();
                    transactionRef = transactionRef.where('driverVehicleNumber', '==', vehicle_number);

                }
            }
            if (fileFormat == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.file_format_error') }}</p>");
                window.scrollTo(0, 0);
            } else {

                var headerArray = ['TxnId', 'Customer', 'Customer Phone', 'Vehicle Number', 'Owner', 'Fuel Station Name', 'Fuel Station Address', 'Employee', 'Date', 'Fuel', 'Fuel Type', 'Customer Current Quota', 'Customer Remaining Quota', 'Owner Current Quota', 'Owner Remaining Quota', 'Status', 'Expected Remaining', 'Variance', 'Stock Gap'];
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

                // The stock chain only makes sense when the export contains every sale
                // a station made. Narrowing to one vehicle or one attendant hides the
                // sales in between, which would show up as bogus gaps.
                var chainable = (customer == 'all' && employee == 'all');

                const auditOptions = {
                    chainable: chainable
                };
                const entries = await fetchBatches(transactionRef, batchSize, auditOptions);
                const fetchedData = entries.map(function(entry) {
                    return entry.row;
                });

                if (fetchedData.length > 0) {

                    /*transactionRef.get().then(async function(transactionSnapshots) {

                        if(transactionSnapshots.docs.length>0) {
                            const userCache={};
                            var transactionData=await getData(transactionSnapshots,userCache);
                            */
                    ((fileFormat == "pdf") ? document.title = periodFileName : "");

                    var exportable = fetchedData;
                    var exportHeaders = headers;

                    if (fileFormat == 'xls' || fileFormat == 'csv') {
                        // The CSV writer emits each row's values in key order and puts
                        // the supplied headers on the very first line. Passing null
                        // instead lets the period sit above the header row.
                        exportHeaders = null;

                        var periodRow = {
                            'c0': 'Report Period',
                            'c1': periodLabel
                        };
                        var spacerRow = {
                            'c0': ''
                        };
                        var headerRow = {};
                        for (var h = 0; h < headerArray.length; h++) {
                            headerRow['c' + h] = headerArray[h];
                        }

                        var summary = summarise(entries, chainable);
                        var summaryRows = [{
                            'c0': 'Transactions Exported',
                            'c1': summary.count
                        }, {
                            'c0': 'Excluded (Cancelled / Pending)',
                            'c1': summary.excluded
                        }, {
                            'c0': 'Total Fuel Sold (L)',
                            'c1': fmtFuel(summary.totalFuel)
                        }];

                        if (chainable) {
                            summaryRows.push({
                                'c0': 'Unexplained Stock Loss (L)',
                                'c1': fmtFuel(summary.unexplainedLoss)
                            });
                            summaryRows.push({
                                'c0': 'Stock Increases / Re-allocations (L)',
                                'c1': fmtFuel(summary.increases)
                            });
                            summaryRows.push({
                                'c0': 'Rows Failing Row-level Check',
                                'c1': summary.brokenRows
                            });
                        } else {
                            summaryRows.push({
                                'c0': 'Stock Gap Analysis',
                                'c1': 'Not available - report is filtered to a specific customer or employee'
                            });
                        }

                        exportable = [periodRow, spacerRow]
                            .concat(summaryRows)
                            .concat([spacerRow, headerRow])
                            .concat(fetchedData);
                    }

                    objectExporter({
                        type: fileFormat,
                        exportable: exportable,
                        headers: exportHeaders,
                        columnSeparator: ',',
                        fileName: periodFileName,
                        headerStyle: 'font-weight: bold; padding: 5px; border: 1px solid #dddddd;',
                        cellStyle: 'border: 1px solid lightgray; margin-bottom: -1px;',
                        documentTitle: 'Transaction Report - ' + periodLabel,
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
        async function fetchBatches(transactionRef, batchSize, options) {

            let transactionData = [];
            let lastDoc = null;
            const userCache = {};
            do {
                let query = transactionRef.limit(batchSize);
                if (lastDoc) {
                    query = query.startAfter(lastDoc);
                }

                const querySnapshot = await query.get();

                if (!querySnapshot.empty) {
                    var transactions = await getData(querySnapshot, userCache);
                    transactionData.push(...transactions);

                    lastDoc = querySnapshot.docs[querySnapshot.docs.length - 1];
                } else {
                    lastDoc = null;
                }
            } while (lastDoc);

            transactionData.sort(function(a, b) {
                return a.sortAt - b.sortAt;
            });

            annotateAudit(transactionData, options);

            return transactionData;
        }

        // Cancelled and still-requested sales are exported like any other row, but they
        // never reduced the station's stock. Letting them into the totals or the stock
        // chain would recreate the very discrepancy this report exists to measure.
        var NON_DEDUCTING_STATUSES = ['cancelled', 'requested'];

        function deductsStock(entry) {
            return NON_DEDUCTING_STATUSES.indexOf(entry.status) === -1;
        }

        // Turns the stored snapshots into a check. Two independent tests:
        //
        //   row integrity - currentQuota - fuelInLiter should equal availableQuota,
        //                   using only values on the transaction itself.
        //   continuity    - this sale's currentQuota should equal the previous sale's
        //                   availableQuota for the same station and fuel. A break means
        //                   stock moved with no transaction to account for it, and that
        //                   is the litre residue that stops stock reaching zero.
        function annotateAudit(entries, options) {
            entries.forEach(function(entry) {
                // A cancelled or pending sale never moved stock, so its snapshot has
                // nothing to be checked against. Left blank rather than showing a
                // variance the reader would take for a fault.
                if (!entry.hasOwnerSnapshot || !deductsStock(entry)) {
                    return;
                }
                var expected = entry.ownerCurrent - entry.fuel;
                entry.row['Expected Remaining'] = fmtFuel(expected);
                entry.row['Variance'] = fmtFuel(entry.ownerAvailable - expected);
            });

            if (!options.chainable) {
                return;
            }

            var groups = {};
            entries.forEach(function(entry) {
                if (!entry.hasOwnerSnapshot || !entry.ownerId || !deductsStock(entry)) {
                    return;
                }
                // Grouped per fuel type as well as per station: each fuel carries its
                // own stock balance, so their snapshots form separate chains.
                var key = entry.ownerId + '||' + entry.fuelType;
                if (!groups[key]) {
                    groups[key] = [];
                }
                groups[key].push(entry);
            });

            Object.keys(groups).forEach(function(key) {
                var list = groups[key];
                // The first sale in the window has no predecessor inside the report, so
                // its opening balance cannot be checked and is left blank.
                for (var i = 1; i < list.length; i++) {
                    var gap = list[i].ownerCurrent - list[i - 1].ownerAvailable;
                    list[i].gap = gap;
                    list[i].row['Stock Gap'] = fmtFuel(Math.abs(gap) <= TOLERANCE ? 0 : gap);
                }
            });
        }

        function summarise(entries, chainable) {
            var summary = {
                count: entries.length,
                excluded: 0,
                totalFuel: 0,
                unexplainedLoss: 0,
                increases: 0,
                brokenRows: 0
            };

            entries.forEach(function(entry) {
                if (!deductsStock(entry)) {
                    summary.excluded++;
                    return;
                }

                summary.totalFuel += entry.fuel;

                if (entry.hasOwnerSnapshot) {
                    var expected = entry.ownerCurrent - entry.fuel;
                    if (Math.abs(entry.ownerAvailable - expected) > TOLERANCE) {
                        summary.brokenRows++;
                    }
                }

                if (chainable && typeof entry.gap === 'number') {
                    if (entry.gap < -TOLERANCE) {
                        // Stock fell between two sales with nothing to explain it.
                        summary.unexplainedLoss += Math.abs(entry.gap);
                    } else if (entry.gap > TOLERANCE) {
                        // A refill or admin re-allocation. Expected, but it means the
                        // period total cannot be compared against a single allocation.
                        summary.increases += entry.gap;
                    }
                }
            });

            return summary;
        }
        async function getData(querySnapshot, userCache) {
            // Promise.all keeps the results in document order. Pushing from inside the
            // callbacks instead ordered rows by however fast each owner/employee lookup
            // resolved, which scrambled the report out of date order.
            var transactionData = await Promise.all(querySnapshot.docs.map(async (doc) => {
                var userObj = doc.data();
                if (!userObj.createAt) {
                    return null;
                }
                var createdAt = userObj.createAt.toDate();
                var newUserObj = {};
                const ownerName = await getCachedUserData(userObj.ownerId, userCache);
                const employeeName = await getCachedUserData(userObj.employeeId, userCache);
                // 24-hour, and free of commas so the unquoted CSV writer keeps it in
                // a single column.
                var dateTime = moment(createdAt).format('DD MMM YYYY HH:mm:ss');
                newUserObj['TxnId'] = (userObj.id).substring(0, 7);
                newUserObj['Customer'] = (userObj.driverName) ? userObj.driverName : '';
                newUserObj['Customer Phone'] = userObj.driverMobileNo;
                newUserObj['Vehicle Number'] = userObj.driverVehicleNumber;
                newUserObj['Owner'] = ownerName;
                newUserObj['Fuel Station Name'] = userObj.ownerPumpName;
                var pumpAdrs = '';
                if (userObj.ownerPumpAddress != null && userObj.ownerPumpAddress != '') {
                    pumpAdrs = userObj.ownerPumpAddress.split(',').join('\t');
                }
                newUserObj['Fuel Station Address'] = pumpAdrs;
                newUserObj['Employee'] = employeeName;
                newUserObj['Date'] = dateTime;
                newUserObj['Fuel'] = userObj.fuelInLiter;
                newUserObj['Fuel Type'] = userObj.fuelType;
                if (userObj.hasOwnProperty('customer')) {
                    newUserObj['Customer Current Quota'] = userObj.customer.currentQuota;
                    newUserObj['Customer Remaining Quota'] = userObj.customer.availableQuota;
                } else {
                    newUserObj['Customer Current Quota'] = '';
                    newUserObj['Customer Remaining Quota'] = '';

                }
                var hasOwnerSnapshot = userObj.hasOwnProperty('owner');
                if (hasOwnerSnapshot) {
                    newUserObj['Owner Current Quota'] = userObj.owner.currentQuota;
                    newUserObj['Owner Remaining Quota'] = userObj.owner.availableQuota;
                } else {
                    newUserObj['Owner Current Quota'] = '';
                    newUserObj['Owner Remaining Quota'] = '';
                }
                // Seeded here so the CSV writer, which emits every own key in insertion
                // order, always sees these four in the same column positions. The audit
                // pass overwrites the values without disturbing that order.
                newUserObj['Status'] = userObj.status ? userObj.status : '';
                newUserObj['Expected Remaining'] = '';
                newUserObj['Variance'] = '';
                newUserObj['Stock Gap'] = '';

                return {
                    sortAt: createdAt.getTime(),
                    ownerId: userObj.ownerId ? userObj.ownerId : '',
                    fuelType: userObj.fuelType ? userObj.fuelType : '',
                    status: userObj.status ? userObj.status : '',
                    fuel: toFuelNumber(userObj.fuelInLiter),
                    ownerCurrent: hasOwnerSnapshot ? toFuelNumber(userObj.owner.currentQuota) : 0,
                    ownerAvailable: hasOwnerSnapshot ? toFuelNumber(userObj.owner.availableQuota) : 0,
                    hasOwnerSnapshot: hasOwnerSnapshot,
                    row: newUserObj
                };
            }));
            return transactionData.filter(function(entry) {
                return entry !== null;
            });
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
