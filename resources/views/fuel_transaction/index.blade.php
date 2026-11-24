@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">
                    {{ trans('lang.fuel_transaction') }}
                </h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.fuel_transaction_table') }}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="admin-top-section">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex top-title-section pb-4 justify-content-between">
                            <div class="d-flex top-title-left align-self-center">

                            </div>
                            <div class="d-flex top-title-right align-self-center">
                                <div class="d-flex top-title-right align-self-center">
                                    <div class="select-box pl-3">
                                        <div id="daterange"><i class="fa fa-calendar"></i>&nbsp;
                                            <span></span>&nbsp; <i class="fa fa-caret-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-list mr-2"></i>{{ trans('lang.fuel_transaction_table') }}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-10">
                                    <table id="userTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>{{ trans('lang.txn_id') }}</th>
                                                <th>{{ trans('lang.customer') }}</th>
                                                <th>{{ trans('lang.owner') }}</th>
                                                <th>{{ trans('lang.employee') }}</th>
                                                <th>{{ trans('lang.type_fuel') }}</th>
                                                <th>{{ trans('lang.fuel') }}</th>
                                                <th>{{ trans('lang.date') }}</th>
                                                <th>{{ trans('lang.phone') }}</th>
                                                <th>{{ trans('lang.vehicle_number') }}</th>
                                                <th>{{ trans('lang.status') }}</th>
                                                <th>{{ trans('lang.available_quota') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script type="text/javascript">
            var currentDateTime = new Date();
            var startOfToday = new Date(currentDateTime);
            startOfToday.setHours(0, 0, 0, 0);
            var endOfToday = new Date(currentDateTime);
            endOfToday.setHours(23, 59, 59, 999);
            var startTimestamp = firebase.firestore.Timestamp.fromDate(startOfToday);
            var endTimestamp = firebase.firestore.Timestamp.fromDate(endOfToday);
            var refTransaction = database.collection('fuel_transaction').where('createAt', '>=', startTimestamp).where('createAt', '<=', endTimestamp);
            let totalDocuments = 0;

            function setDate() {
                $('#daterange span').html('{{ trans('lang.select_range') }}');
                let today = moment().format('MMMM D, YYYY'); // Get today's date
                $('#daterange span').html(today + ' - ' + today); // Set default text

                $('#daterange').daterangepicker({
                    autoUpdateInput: false,
                }, function(start, end) {
                    $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

                });
                $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                    $('#daterange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
                    filterData();
                });
                $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                    $('#daterange span').html('{{ trans('lang.select_range') }}');
                    filterData();
                });
            }
            setDate();
            async function filterData() {

                var daterangepicker = $('#daterange').data('daterangepicker');

                ref = database.collection('fuel_transaction');

                if ($('#daterange span').html() != '{{ trans('lang.select_range') }}' && daterangepicker) {

                    var from = moment(daterangepicker.startDate).toDate();

                    var to = moment(daterangepicker.endDate).toDate();

                    if (from && to) {

                        var fromDate = firebase.firestore.Timestamp.fromDate(new Date(from));

                        ref = ref.where('createAt', '>=', fromDate);

                        var toDate = firebase.firestore.Timestamp.fromDate(new Date(to));

                        ref = ref.where('createAt', '<=', toDate);

                    }

                }
                refTransaction = ref;
                $('#userTable').DataTable().ajax.reload();
                //var table = $('#userTable').DataTable();
                //table.destroy();
                
                /*globalLargeDbHandler({
                    tableId: '#userTable',
                    collectionName: 'fuel_transaction',
                    defaultSearchField: 'createAt', // Add your preferred default search field
                    columns: [{
                            field: 'id',
                            orderable: true,
                            searchable: true,
                            render: (data) =>
                                `<a href="{{ route('fuel-transaction.view', ':id') }}".replace(':id', '${data}')">${data?.substring(0, 7)}</a>`,
                        },
                        {
                            field: 'driverName',
                            orderable: true,
                            searchable: true
                        },
                        {
                            field: 'ownerId',
                            orderable: false,
                            searchable: true,
                            renderAsync: getUserName,
                        },
                        {
                            field: 'employeeId',
                            orderable: false,
                            searchable: true,
                            renderAsync: getUserName,
                        },
                        {
                            field: 'fuelType',
                            orderable: true,
                            searchable: true
                        },
                        {
                            field: 'fuelInLiter',
                            orderable: true,
                            searchable: false,
                            render: (data) => `${data} {{ trans('lang.liter') }}`,
                            fieldType: 'numeric',
                        },
                        {
                            field: 'createAt',
                            orderable: true,
                            searchable: true,
                            render: (data) =>
                                data ? new Date(data.seconds * 1000).toLocaleString() : '',
                        },
                        {
                            field: 'driverMobileNo',
                            orderable: true,
                            searchable: true
                        },
                        {
                            field: 'driverVehicleNumber',
                            orderable: true,
                            searchable: true
                        },
                        {
                            field: 'status',
                            orderable: false,
                            searchable: true,
                            render: getStatusBadge,
                        },
                        {
                            field: 'adminAvailableFuelQuota',
                            orderable: true,
                            searchable: false,
                            render: (data) => `${data} {{ trans('lang.liter') }}`,
                            fieldType: 'numeric',
                        },
                        {
                            field: 'id',
                            orderable: false,
                            searchable: false,
                            render: (data) =>
                                `<a href="{{ route('fuel-transaction.view', ':id') }}".replace(':id', '${data}')"><i class="fa fa-eye"></i></a>`,
                        },

                    ],
                    defaultOrder: [6, 'desc'],
                    pageSize: 10,
                });*/

            }

            $(document).ready(function() {
                /*globalLargeDbHandler({
                    tableId: '#userTable',
                    collectionName: 'fuel_transaction',
                    defaultSearchField: 'createAt', // Add your preferred default search field
                    columns: [{
                            field: 'id',
                            orderable: true,
                            searchable: true,
                            render: (data) =>
                                `<a href="{{ route('fuel-transaction.view', ':id') }}".replace(':id', '${data}')">${data?.substring(0, 7)}</a>`,
                        },
                        {
                            field: 'driverName',
                            orderable: true,
                            searchable: true
                        },
                        {
                            field: 'ownerId',
                            orderable: false,
                            searchable: true,
                            renderAsync: getUserName,
                        },
                        {
                            field: 'employeeId',
                            orderable: false,
                            searchable: true,
                            renderAsync: getUserName,
                        },
                        {
                            field: 'fuelType',
                            orderable: true,
                            searchable: true
                        },
                        {
                            field: 'fuelInLiter',
                            orderable: true,
                            searchable: false,
                            render: (data) => `${data} {{ trans('lang.liter') }}`,
                            fieldType: 'numeric',
                        },
                        {
                            field: 'createAt',
                            orderable: true,
                            searchable: true,
                            render: (data) =>
                                data ? new Date(data.seconds * 1000).toLocaleString() : '',
                        },
                        {
                            field: 'driverMobileNo',
                            orderable: true,
                            searchable: true
                        },
                        {
                            field: 'driverVehicleNumber',
                            orderable: true,
                            searchable: true
                        },
                        {
                            field: 'status',
                            orderable: false,
                            searchable: true,
                            render: getStatusBadge,
                        },
                        {
                            field: 'adminAvailableFuelQuota',
                            orderable: true,
                            searchable: false,
                            render: (data) => `${data} {{ trans('lang.liter') }}`,
                            fieldType: 'numeric',
                        },
                        {
                            field: 'id',
                            orderable: false,
                            searchable: false,
                            render: (data) =>
                                `<a href="{{ route('fuel-transaction.view', ':id') }}".replace(':id', '${data}')"><i class="fa fa-eye"></i></a>`,
                        },

                    ],
                    defaultOrder: [6, 'desc'],
                    pageSize: 10,
                });*/


                const table = $('#userTable').DataTable({
                    pageLength: 10, // Number of rows per page
                    processing: false, // Show processing indicator
                    serverSide: true, // Enable server-side processing
                    responsive: true,
                    ajax: async function(data, callback, settings) {
                        const start = data.start;
                        const length = data.length;
                        const searchValue = data.search.value.toLowerCase();
                        const orderColumnIndex = data.order[0].column;
                        const orderDirection = data.order[0].dir;
                        const orderableColumns = ['id', 'driverName', 'ownerId', 'employeeId', 'fuelType', 'fuelInLiter', 'createAt', 'driverMobileNo', 'driverVehicleNumber', 'status', 'adminAvailableFuelQuota', '']
                        const orderByField = orderableColumns[orderColumnIndex]; // Adjust the index to match your table
                        if (searchValue.length >= 3 || searchValue.length === 0) {
                            $('#overlay').show();
                        }
                        refTransaction.orderBy('createAt', 'desc').get().then(async function(querySnapshot) {
                            if (querySnapshot.empty) {
                                $('.total_count').text(0);
                                console.error("No data found in Firestore.");
                                $('#overlay').hide(); // Hide loader
                                callback({
                                    draw: data.draw,
                                    recordsTotal: 0,
                                    recordsFiltered: 0,
                                    filteredData: [],
                                    data: [] // No data
                                });
                                return;
                            }
                            let records = [];
                            let filteredRecords = [];
                            let ownerNames = {};
                            let employeeNames = {};
                            // Fetch driver names
                            const ownerDocs = await database.collection('users').where('role', '==', 'owner').get();
                            ownerDocs.forEach(doc => {
                                ownerNames[doc.id] = doc.data().fullName;
                            });
                            const employeeDocs = await database.collection('users').where('role', '==', 'employee').get();
                            employeeDocs.forEach(doc => {
                                employeeNames[doc.id] = doc.data().fullName;
                            });
                            await Promise.all(querySnapshot.docs.map(async (doc) => {
                                childData = doc.data();
                                childData.id = doc.id; // Ensure the document ID is included in the data              
                                childData.ownerName = ownerNames[childData.ownerId] || '';
                                childData.employeeName = ownerNames[childData.employeeId] || '';


                                if (childData.hasOwnProperty("createAt") && childData.createAt != null) {
                                    try {
                                        date = childData.createAt.toDate().toDateString();
                                        time = childData.createAt.toDate().toLocaleTimeString('en-US');
                                    } catch (err) {}
                                    childData.createDate = date + ' ' + time;
                                }

                                if (searchValue) {


                                    if (
                                        (childData.ownerName && childData.ownerName.toLowerCase().toString().includes(searchValue)) ||
                                        (childData.employeeName && childData.employeeName.toLowerCase().toString().includes(searchValue)) ||
                                        (childData.createDate && childData.createDate.toString().toLowerCase().indexOf(searchValue) > -1) ||
                                        (childData.driverName && childData.driverName.toString().toLowerCase().includes(searchValue)) ||
                                        (childData.driverVehicleNumber && childData.driverVehicleNumber.toString().toLowerCase().includes(searchValue)) ||
                                        (childData.fuelType && childData.fuelType.toString().toLowerCase().includes(searchValue))

                                    ) {
                                        filteredRecords.push(childData);
                                    }
                                } else {
                                    filteredRecords.push(childData);
                                }
                            }));
                            filteredRecords.sort((a, b) => {
                                let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                                let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : '';
                                if (orderByField === 'createAt') {
                                    aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                    bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                                }
                                if (orderByField === 'subscriptionExpiryDate') {
                                    aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                    bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                                }
                                if (orderDirection === 'asc') {
                                    return (aValue > bValue) ? 1 : -1;
                                } else {
                                    return (aValue < bValue) ? 1 : -1;
                                }
                            });
                            const totalRecords = filteredRecords.length;
                            $('.total_count').text(totalRecords);
                            const paginatedRecords = filteredRecords.slice(start, start + length);
                            await Promise.all(paginatedRecords.map(async (childData) => {

                                var getData = await buildHTML(childData);
                                records.push(getData);
                            }));
                            $('#overlay').hide(); // Hide loader
                            callback({
                                draw: data.draw,
                                recordsTotal: totalRecords,
                                recordsFiltered: totalRecords,
                                filteredData: filteredRecords,
                                data: records
                            });
                        }).catch(function(error) {
                            console.error("Error fetching data from Firestore:", error);
                            $('#overlay').hide(); // Hide loader
                            callback({
                                draw: data.draw,
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                filteredData: [],
                                data: []
                            });
                        });
                    },
                    order: [
                        [6, 'desc']
                    ],
                    columnDefs: [{
                            targets: 6,
                            type: 'date',
                            render: function(data) {
                                return data;
                            }
                        },
                        {
                            orderable: false,
                            targets: [9]
                        },
                    ],
                    "language": {
                        "zeroRecords": "{{ trans('lang.no_record_found') }}",
                        "emptyTable": "{{ trans('lang.no_record_found') }}",
                        "processing": "" // Remove default loader
                    },
                    dom: 'lfrtipB',

                    initComplete: function() {
                        $(".dataTables_filter").append($(".dt-buttons").detach());
                        $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete', 'new-password').val('');
                        $('.dataTables_filter label').contents().filter(function() {
                            return this.nodeType === 3;
                        }).remove();
                    }
                });
                table.columns.adjust().draw();

                function debounce(func, wait) {
                    let timeout;
                    const context = this;
                    return function(...args) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(context, args), wait);
                    };
                }
                $('#search-input').on('input', debounce(function() {
                    const searchValue = $(this).val();
                    if (searchValue.length >= 3) {
                        $('#overlay').show();
                        table.search(searchValue).draw();
                    } else if (searchValue.length === 0) {
                        $('#overlay').show();
                        table.search('').draw();
                    }
                }, 300));

            });
            async function buildHTML(val) {
                var html = [];
                newdate = '';
                var id = val.id;
                html.push(val.id.substring(0, 7));
                html.push(val.driverName);
                html.push(val.ownerName);
                html.push(val.employeeName);
                html.push(val.fuelType);
                html.push(val.fuelInLiter+' {{trans("lang.liter")}}');
                html.push(val.createDate);
                html.push(val.driverMobileNo);
                html.push(val.driverVehicleNumber);
                html.push(val.status);
                html.push(val.adminAvailableFuelQuota+' {{trans("lang.liter")}}')
                newhtml = '';
                if(val.odometerReadingId && val.odometerReadingId != null && val.odometerReadingId != ''){
                    var viewRoute = '{{ route('customers.odometerReadingHistoryView', ':id') }}';
                    viewRoute = viewRoute.replace(':id', val.odometerReadingId);
                    newhtml = `<a href="${viewRoute}"><i class="fa fa-clock"></i> {{trans("lang.odometer_reading")}}</a>`;
                }
                html.push(newhtml);
                return html;
            }

            async function globalLargeDbHandler(config) {
                jQuery("#overlay").show();
                const {
                    tableId,
                    collectionName,
                    columns,
                    pageSize,
                    defaultOrder,
                    defaultSearchField
                } = config;
                let lastVisible = null;

                const pageCache = {};
                (async () => {

                    const totalDocuments = await getTotalCountParallel();

                    var orderByField = '';
                    const dataTable = $(tableId).DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: async (data, callback) => {
                            try {
                                const currentPage = Math.floor(data.start / pageSize);
                                const searchQuery = data.search.value;
                                const sortColumn = data.order[0]?.column ?? defaultOrder[0];
                                const sortDirection = data.order[0]?.dir || defaultOrder[1];
                                const sortField = columns[sortColumn]?.field || 'createAt';
                                orderByField = defaultOrder
                                let query = refTransaction;
                                console.log(orderByField);
                                // Apply search filter
                                if (searchQuery) {
                                    //orderByField=[8,'desc'];
                                    const searchableColumns = columns.filter((col) => col.searchable);
                                    if (defaultSearchField) {
                                        // Search only in the defaultSearchField
                                        query = query
                                            .orderBy(defaultSearchField)
                                            .where(defaultSearchField, '>=', searchQuery)
                                            .where(defaultSearchField, '<=', searchQuery + '\uf8ff');
                                    } else if (searchableColumns.length > 0) {
                                        // Search across all searchable fields (Firestore requires one field for ordering)
                                        query = query.orderBy(searchableColumns[0].field);
                                        // Apply where conditions for all searchable fields
                                        query = searchableColumns.reduce((q, col) => {
                                            return q.where(col.field, '>=', searchQuery).where(col.field, '<=', searchQuery + '\uf8ff');
                                        }, query);
                                    }
                                }

                                // Apply ordering
                                if (!searchQuery) {

                                    query = query.orderBy(sortField, sortDirection);
                                }

                                // Apply pagination
                                if (currentPage > 0 && pageCache[currentPage]) {
                                    query = query.startAfter(pageCache[currentPage]);
                                }
                                query = query.limit(pageSize);

                                const snapshot = await query.get();


                                // Update last visible document and cache
                                if (!snapshot.empty) {
                                    lastVisible = snapshot.docs[snapshot.docs.length - 1];
                                    pageCache[currentPage + 1] = lastVisible;
                                }

                                // Format rows for DataTable
                                const rows = await Promise.all(
                                    snapshot.docs.map(async (doc) => {
                                        const rowData = doc.data();
                                        return Promise.all(
                                            columns.map(async (col) => {
                                                if (col.renderAsync) {
                                                    return await col.renderAsync(rowData[col.field]);
                                                }
                                                return col.render ?
                                                    col.render(rowData[col.field]) :
                                                    rowData[col.field] || '';
                                            })
                                        );
                                    })
                                );

                                callback({
                                    draw: data.draw,
                                    recordsTotal: totalDocuments,
                                    recordsFiltered: totalDocuments,
                                    data: rows,
                                });
                            } catch (error) {
                                console.error('Error loading data:', error);
                            }
                        },
                        columns: columns.map((col) => ({
                            orderable: col.orderable !== false,
                            searchable: col.searchable !== false,
                        })),
                        language: {
                            zeroRecords: "{{ trans('lang.no_record_found') }}",
                            emptyTable: "{{ trans('lang.no_record_found') }}",
                        },
                        responsive: true,
                        order: [defaultOrder],
                    });
                })();
                jQuery("#overlay").hide();
            }

            async function getTotalCountParallel() {
                let documentCount = 0;
                let lastDoc = null;
                const batchSize = 1000;
                while (true) {
                    let query = refTransaction.orderBy("createAt")
                        .limit(batchSize);

                    if (lastDoc) {
                        query = query.startAfter(lastDoc);
                    }

                    const snapshot = await query.get();
                    if (snapshot.empty) {
                        break; // No more documents, exit loop
                    }

                    documentCount += snapshot.size; // Add batch size to total count
                    lastDoc = snapshot.docs[snapshot.docs.length - 1]; // Update lastDoc for next batch

                }
                totalDocuments = documentCount;
                return documentCount;
            }

            async function getUserName(userId) {
                if (!userId) return '';
                const snapshot = await database.collection('users').doc(userId).get();
                return snapshot.exists ? snapshot.data().fullName : '';
            }

            function getStatusBadge(status) {
                const badgeClasses = {
                    requested: 'badge-info',
                    cancelled: 'badge-danger',
                    default: 'badge-success',
                };
                const displayStatus = {
                    requested: 'Requested',
                    cancelled: 'Cancelled',
                    default: 'Approved',
                };
                const badgeClass = badgeClasses[status] || badgeClasses.default;
                const statusText = displayStatus[status] || displayStatus.default;
                return `<span class="badge ${badgeClass} py-2 px-3">${statusText}</span>`;
            }
        </script>
    @endsection
