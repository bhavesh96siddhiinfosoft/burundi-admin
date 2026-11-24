@extends('layouts.app')

@section('content')
<style>
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #d1d3e2;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
</style>
<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                    {{trans('lang.all_customers')}}
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.customer_table')}}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                 
                    <div class="card-body">
                         <div class="mb-4 p-3 rounded"
                             style="background:#f8f9fa;border:1px solid #e3e6f0;">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0 font-weight-bold text-primary">
                                    <i class="fa fa-filter mr-2"></i> {{ trans('lang.filters') }}
                                </h5>
                            </div>

                            <div class="row">

                                <div class="col-md-3 mb-3">
                                    <select id="date_filter_selector"
                                            class="form-control shadow-sm"
                                            style="height:42px;border:1px solid #d1d3e2;">
                                        <option value="today" selected>{{ trans("lang.today") }}</option>
                                        <option value="this_week">{{ trans("lang.this_week") }}</option>
                                        <option value="this_month">{{ trans("lang.this_month") }}</option>
                                        <option value="custom">{{ trans("lang.custom") }}</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3" id="custom_date_range" style="display:none;">
                                    <input type="date"
                                           id="from_date"
                                           class="form-control shadow-sm"
                                           style="height:42px;border:1px solid #d1d3e2;"
                                           placeholder="{{ trans('lang.from_date') }}">
                                </div>

                                <div class="col-md-3 mb-3" id="to_date_wrapper" style="display:none;">
                                    <input type="date"
                                           id="to_date"
                                           class="form-control shadow-sm"
                                           style="height:42px;border:1px solid #d1d3e2;"
                                           placeholder="{{ trans('lang.to_date') }}">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <select id="vehicle_type_selector"
                                            class="form-control shadow-sm"
                                            style="height:42px;border:1px solid #d1d3e2;">
                                        <option value="" disabled selected>
                                            {{ trans("lang.vehicle_typee_help") }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <select id="fuel_type_selector"
                                            class="form-control shadow-sm"
                                            style="height:42px;border:1px solid #d1d3e2;">
                                        <option value="" disabled selected>
                                            {{ trans("lang.fuel_type_help") }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select id="verified_selector"
                                            class="form-control shadow-sm"
                                            style="height:42px;border:1px solid #d1d3e2;">
                                        <option value="all">{{ trans("lang.all") }}</option>
                                        <option value="verified">Verified Users</option>
                                        <option value="non_verified">Non Verified Users</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <button class="btn btn-success mr-2 px-4 shadow-sm"
                                            style="height:42px;font-weight:600;border-radius:25px;"
                                            type="button"
                                            onclick="submitFilters()">
                                        <i class="fa fa-search mr-1"></i>
                                        {{trans('lang.submit')}}
                                    </button>
                                    <button class="btn btn-outline-secondary shadow-sm"
                                            style="height:42px;font-weight:600;border-radius:25px;"
                                            type="button"
                                            onclick="restoreFilters()">
                                        <i class="fa fa-refresh mr-1"></i>
                                        Restore
                                    </button>
                                </div>

                            </div>
                        </div>

                        <!-- <div id="users-table_filter" class="pull-right">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <select id="date_filter_selector" class="form-control allModules" style="width:100%">
                                    <option value="today" selected>{{ trans("lang.today") }}</option>
                                    <option value="this_week">{{ trans("lang.this_week") }}</option>
                                    <option value="this_month">{{ trans("lang.this_month") }}</option>
                                    <option value="custom">{{ trans("lang.custom") }}</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3" id="custom_date_range" style="display:none;">
                                <input type="date" id="from_date" class="form-control" placeholder="{{ trans('lang.from_date') }}">
                            </div>
                            <div class="col-md-3 mb-3" id="to_date_wrapper" style="display:none;">
                                <input type="date" id="to_date" class="form-control" placeholder="{{ trans('lang.to_date') }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <select id="vehicle_type_selector" class="form-control allModules" style="width:100%">
                                    <option value="" disabled selected>{{ trans("lang.vehicle_typee_help") }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select id="fuel_type_selector" class="form-control allModules" style="width:100%">
                                    <option value="" disabled selected>{{ trans("lang.fuel_type_help") }}</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3 d-flex align-items-start">
                                <button class="btn btn-primary w-50" type="button" onclick="submitFilters()">{{trans('lang.submit')}}</button>
                            </div>
                        </div> -->



                        </div>
                        

                        <div class="table-responsive m-t-10">
                            <table id="userTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>

                                            <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                    class="col-3 control-label" for="is_active"><a id="assignPetrolPumpMultiple"
                                                        class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-gas-station"></i> {{trans('lang.all')}}</a></label></th>
                                        
                                        <th>{{trans('lang.image')}}</th>
                                        <th>{{trans('lang.name')}}</th>
                                        <th>{{trans('lang.phone')}}</th>
                                        <th>{{trans('lang.vehicle_type')}}</th>
                                        <th>{{trans('lang.vehicle_number')}}</th>
                                        <th>{{trans('lang.type_fuel')}}</th>
                                        <th>{{trans('lang.created_at')}}</th>
                                        <th>{{trans('lang.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody id="append_list1">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<div class="modal fade" id="assignFuelStationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title">{{trans('lang.assign')}} {{trans('lang.fuel_station')}}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label>{{ trans('lang.state') }}</label>
                    <select id="state_select_box" class="form-control">
                        <option value="">{{ trans('lang.select_state') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ trans('lang.city') }}</label>
                    <select id="city_select_box" class="form-control">
                        <option value="">{{ trans('lang.select_city') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ trans('lang.fuel_station') }}</label>
                    <select id="fuel_station_select_box" class="form-control">
                        <option value="">{{ trans('lang.select_fuel_station') }}</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="assignFuelStationBtn">
                    {{trans('lang.assign')}}
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    {{trans('lang.close')}}
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')


<script type="text/javascript">
    var database=firebase.firestore();
    var defaultImg="{{ asset('/images/default_user.png') }}";
    var offest=1;
    var pagesize=10;
    var end=null;
    var endarray=[];
    var start=null;
    var user_number=[];
    var placeholderImage='';
    var append_list='';
 
    var base_ref=database.collection('users').where('role','==','customer');

    var params = new URLSearchParams(window.location.search);
    var verifiedType = params.get('verifiedType');
    var vehicleType = params.get('vehicleType');
    var fuelType = params.get('fuelType');
    var dateFilter = params.get('dateFilter');
    var fromDate = new Date(params.get('fromDate'));
    var toDate = new Date(params.get('toDate'));
    var ref = base_ref;

    if(vehicleType != '' && vehicleType != undefined && vehicleType != null && vehicleType!='all') {
        ref=ref.where('vehicleId','==',vehicleType);
    }

    if(fuelType != '' && fuelType != undefined && fuelType != null && fuelType != 'all') {
        ref=ref.where('fuelTypeId','==',fuelType);
    }
    if (verifiedType != '' && verifiedType != undefined && verifiedType != null && verifiedType != 'all') {

        if (verifiedType == 'verified') {
            ref = ref.where('documentApproved', '==', true);
        }

        if (verifiedType == 'non_verified') {
            ref = ref.where('documentApproved', '==', false);
        }
    }
    if (dateFilter && dateFilter != '' && dateFilter != null) {
        ref = applyDateFilter(ref);
    } else {
        $('#date_filter_selector').val('today');
        ref = ref.where('createdAt', '>=', new Date(new Date().setHours(0, 0, 0, 0)));
    }
    var deleteMsg="{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg="{{trans('lang.selected_delete_alert')}}";

    let lastVisible = null;
    const batchSize = 10;
    let totalRecords = 0;
    var selectedUserIds = [];

    $('#vehicle_type_selector').select2({
        placeholder: "{{ trans('lang.vehicle_typee_help') }}",
        width: '100%'
    });
    $('#verified_selector').select2({
        placeholder: "{{ trans('lang.all') }}",
        width: '100%',
        minimumResultsForSearch: Infinity
    });
    $('#fuel_type_selector').select2({
        placeholder: "{{ trans('lang.fuel_type_help') }}",
        width: '100%',
        minimumResultsForSearch: Infinity
    });
    $('#date_filter_selector').select2({
        width: '100%',
        minimumResultsForSearch: Infinity
    });

    async function fetchTotalRecords() {
        try {
            const snapshot = await ref.get();
            totalRecords = snapshot.size;
        } catch (error) {
            console.error("Error fetching total records:", error);
        }
    }
  
    $(document).ready(async function() {
        jQuery("#overlay").show();
        setDefaultFilters();
        await fetchTotalRecords();
        $('#userTable').DataTable({
            serverSide: true,
            processing: true,
            pageLength: batchSize,
            order: [[ 7 , 'desc']],
            ajax: function (data, callback) {
                fetchFirestoreData(data.start, data.length || batchSize, data.search.value).then((result) => {
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords,
                        recordsFiltered: result.totalFiltered || totalRecords,
                        data: result.data,
                    });
                });
            },
            columns: [
                { data: 'deleteCheckbox' },
                { data: 'profilePic' },
                { data: 'fullName' },
                { data: 'mobileNumber' },
                { data: 'vehicle' },
                { data: 'vehicleNumber' },
                { data: 'fuelType' },
                { data: 'createdAt' },
                { data: 'actions' }
            ].filter(Boolean),
            columnDefs: [
                { targets:  7 , type: 'date' },
                { targets: '_all', orderable: false }
            ],
            language: {
                zeroRecords: "{{trans('lang.no_record_found')}}",
                emptyTable: "{{trans('lang.no_record_found')}}"
            }
        });
        $(document.body).on('click','.redirecttopage',function() {
            var url=$(this).attr('data-url');
            window.location.href=url;
        });
        var refVeh=database.collection('vehicle_type');

        refVeh.get().then(async function(snapshots) {
            $('#vehicle_type_selector').append($("<option></option>")
                .attr("value",'all')
                .text("{{ trans('lang.all') }}"));
            snapshots.docs.forEach(doc => {
                var data=doc.data();

                $('#vehicle_type_selector').append($("<option></option>")
                    .attr("value",data.id)
                    .text(data.name));
            })
            var vehicleType = new URLSearchParams(window.location.search).get('vehicleType');
            if(vehicleType!=''&&vehicleType!=undefined&&vehicleType!=null) {
                $('#vehicle_type_selector').val(vehicleType);
            }
        });
        var fueltypeRef=database.collection('fuel_type').where('enable','==',true);
        fueltypeRef.get().then(async function(snapshot) {
            if(snapshot.docs.length>0) {
                $('#fuel_type_selector').append($("<option></option>")
                    .attr("value",'all')
                    .text("{{ trans('lang.all') }}"));
                snapshot.docs.forEach(doc => {
                    var data=doc.data();

                    $('#fuel_type_selector').append($("<option></option>")
                        .attr("value",data.id)
                        .text(data.name));
                })
                var fuelType = new URLSearchParams(window.location.search).get('fuelType');
                if(fuelType != '' && fuelType != undefined && fuelType != null) {
                    $('#fuel_type_selector').val(fuelType);
                }
            }
        })
        jQuery("#overlay").hide();
        

    });
    $('#date_filter_selector').on('change', function() {
        var dateFilter = $('#date_filter_selector').val();
        if (dateFilter === 'custom') {
            $('#custom_date_range').show();  
            $('#to_date_wrapper').show();    
            $('#from_date').prop('required', true);  
            $('#to_date').prop('required', true);   
        } else {
            $('#custom_date_range').hide(); 
            $('#to_date_wrapper').hide();    
            $('#from_date').prop('required', false); 
            $('#to_date').prop('required', false);  
        }
    });
    function submitFilters() {
        const params = new URLSearchParams();
        params.set('vehicleType', $('#vehicle_type_selector').val() || '');
        params.set('verifiedType', $('#verified_selector').val() || '');
        params.set('fuelType', $('#fuel_type_selector').val() || '');
        params.set('dateFilter', $('#date_filter_selector').val() || '');
        params.set('fromDate', $('#from_date').val() || '');
        params.set('toDate', $('#to_date').val() || '');
        window.location.href = '?' + params.toString();
    }
    function applyDateFilter(ref) {
        const params = new URLSearchParams(window.location.search);
        const dateFilter = params.get('dateFilter');
        const fromDate = new Date(params.get('fromDate'));
        const toDate = new Date(params.get('toDate'));

        // Apply date filters based on the selected date filter
        if (dateFilter === 'today') {
            const startOfDay = new Date();
            startOfDay.setHours(0, 0, 0, 0);
            ref = ref.where('createdAt', '>=', startOfDay);
        } else if (dateFilter === 'this_week') {
            const today = new Date();
            const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay())); 
            startOfWeek.setHours(0, 0, 0, 0);
            ref = ref.where('createdAt', '>=', startOfWeek);
        } else if (dateFilter === 'this_month') {
            const startOfMonth = new Date();
            startOfMonth.setDate(1);
            startOfMonth.setHours(0, 0, 0, 0); 
            ref = ref.where('createdAt', '>=', startOfMonth);
        } else if (dateFilter === 'custom' && fromDate && toDate) {
            ref = ref.where('createdAt', '>=', fromDate).where('createdAt', '<=', toDate);
        }

        return ref;
    }

    function setDefaultFilters() {
        const params = new URLSearchParams(window.location.search);

        $('#vehicle_type_selector').val(params.get('vehicleType') || '');
        $('#fuel_type_selector').val(params.get('fuelType') || '');
        $('#date_filter_selector').val(params.get('dateFilter') || 'today');
        $('#from_date').val(params.get('fromDate') || '');
        $('#to_date').val(params.get('toDate') || '');
        $('#verified_selector').val(params.get('verifiedType') || 'all');

        if (params.get('dateFilter') === 'custom') {
            $('#custom_date_range, #to_date_wrapper').show();
            $('#from_date, #to_date').prop('required', true);
        } else {
            $('#custom_date_range, #to_date_wrapper').hide();
            $('#from_date, #to_date').removeAttr('required');
        }
    }

    async function fetchFirestoreData(startIndex, limit, searchValue) {

        let query = ref.limit(limit);
        let searchResults = [];

        if (searchValue) {
            let searchPromises = ['fullName', 'mobileNumber', 'vehicleNumber'].map(field => {
                let searchQuery = base_ref.where(field, '>=', searchValue)
                    .where(field, '<=', searchValue + '\uf8ff')
                    .orderBy(field)
                    .limit(limit);
                return searchQuery.get();
            });
            query = query.orderBy("createdAt","desc");
            if (startIndex === 0) {
                lastVisible = null;
            } else if (lastVisible) {
                query = query.startAfter(lastVisible);
            }
       
            const results = await Promise.all(searchPromises);
            results.forEach(snapshot => {
                snapshot.forEach(doc => {
                    if (!searchResults.find(item => item.id === doc.id)) {
                        searchResults.push({ id: doc.id, ...doc.data() });
                    }
                });
            });
        } else {
            query = query.orderBy("createdAt","desc");
            if (startIndex === 0) {
                lastVisible = null;
            } else if (lastVisible) {
                query = query.startAfter(lastVisible);
            }
        
            const snapshots = await query.get();
            lastVisible = snapshots.docs[snapshots.docs.length - 1];
            snapshots.forEach(doc => searchResults.push({ id: doc.id, ...doc.data() }));
        }

        const data = await Promise.all(searchResults.map(async (val) => {
 
            const id = val.id;
            let route1 = '{{route("customers.edit", ":id")}}'.replace(':id', id);

            const vehicleType = await getVehicleType(val.vehicleId, val.id);
            const fuelType = await getFuelType(val.fuelTypeId, val.id);
            var selected_city = val.cityId ? val.cityId : '';
            if (selected_city && selected_city != ''){
                database.collection('users')
                    .where('role', '==', "owner")
                    .where('cityId', '==', selected_city)
                    .get()
                    .then(function (snapshot) {
                        var fuel_station_html = '<option value="">{{ trans("lang.select_fuel_station") }}</option>';
                        if (snapshot.docs.length > 0) {
                            snapshot.docs.forEach((doc) => {
                                var data = doc.data();
                                fuel_station_html += `<option value="${data.id}">${data.fuelStationName}</option>`;
                            });
                        }
                    $('#fuel_station_select_box').html(fuel_station_html);
                });
            }
      
            return {
                deleteCheckbox: `<input type="checkbox" id="is_open_${id}" class="is_open" dataId="${id}"><label class="col-3 control-label" for="is_open_${id}"></label>`,
                profilePic: `<img class="rounded" style="width:50px" src="${val.profilePic || defaultImg}" alt="image">`,
                fullName: `<a href="${route1}">${val.documentApproved ? `${val.fullName}<sup class="badge badge-success ml-2"><i class="fa fa-check"></i> {{trans("lang.verified")}}</sup>` : val.fullName}</a>`,
                mobileNumber: val.mobileNumber ? `+${val.countryCode.includes('+') ? val.countryCode.slice(1) : val.countryCode}-${val.mobileNumber}` : '',
                vehicle: vehicleType || '',
                vehicleNumber: val.vehicleNumber ? val.vehicleNumber.toUpperCase() : '',
                fuelType: fuelType || '',
                createdAt: val.createdAt ? val.createdAt.toDate().toDateString() + ' ' + val.createdAt.toDate().toLocaleTimeString('en-US') : '',
                actions: `
                    <button 
                        id="${val.id}" 
                        class="btn btn-sm btn-outline-success shadow-sm assign-petrol-pump"
                        style="font-weight:600;border-radius:20px;padding:4px 14px;"
                        type="button">
                        <i class="fa fa-plus"></i> {{ trans('lang.assign') }}
                    </button>
                `
            };
        }));

        return { data: data };
    }

    function restoreFilters() {
        window.location.href = window.location.pathname;
    }

    async function getFuelType(fuelTypeId, custId) {
        let fuelType = '';
        const snapshots = await database.collection('fuel_type').where('id', '==', fuelTypeId).get();
        if (snapshots.docs.length > 0) {
            fuelType = snapshots.docs[0].data().name;
            $(`.fuel_${custId}`).html(fuelType);
        }
        return fuelType;
    }

    async function getVehicleType(vehId, custId) {
        let vehType = '';
        const snapshots = await database.collection('vehicle_type').where('id', '==', vehId).get();
        if (snapshots.docs.length > 0) {
            vehType = snapshots.docs[0].data().name;
            $(`.vehicle_${custId}`).html(vehType);
        }
        return vehType;
    }

    $("#is_active").click(function() {
        $("#userTable .is_open").prop('checked',$(this).prop('checked'));
    });

    $("#assignPetrolPumpMultiple").click(function(){

        selectedUserIds = [];
        $('#userTable .is_open:checked').each(function(){
            selectedUserIds.push($(this).attr('dataId'));
        });

        if(selectedUserIds.length === 0){
            alert("{{trans('lang.select_assign_alert')}}");
            return;
        }

        loadStates();
        $('#assignFuelStationModal').modal('show');
    });

    $(document).on("click",".assign-petrol-pump",function(){

        selectedUserIds = [];
        selectedUserIds.push(this.id);

        loadStates();
        $('#assignFuelStationModal').modal('show');
    });

    function loadStates() {
        database.collection('states')
            .where('enable','==',true)
            .where('isDeleted','==',false)
            .get()
            .then(function(snapshot){

                var state_html = '<option value="">{{ trans("lang.select_state") }}</option>';

                snapshot.forEach(function(doc){
                    var data = doc.data();
                    state_html += `<option value="${data.id}">${data.name}</option>`;
                });

                $('#state_select_box').html(state_html);
            });
    }
    $('#state_select_box').on('change', function(){

        var selected_state = $(this).val();

        $('#city_select_box').html('<option value="">{{ trans("lang.select_city") }}</option>');
        $('#fuel_station_select_box').html('<option value="">{{ trans("lang.select_fuel_station") }}</option>');

        if(!selected_state) return;

        database.collection('cities')
            .where('state_id','==',selected_state)
            .where('enable','==',true)
            .where('isDeleted','==',false)
            .get()
            .then(function(snapshot){

                var city_html = '<option value="">{{ trans("lang.select_city") }}</option>';

                snapshot.forEach(function(doc){
                    var data = doc.data();
                    city_html += `<option value="${data.id}">${data.name}</option>`;
                });

                $('#city_select_box').html(city_html);
            });
    });
    $('#city_select_box').on('change', function(){

        var selected_city = $(this).val();

        $('#fuel_station_select_box').html('<option value="">{{ trans("lang.select_fuel_station") }}</option>');

        if(!selected_city) return;

        database.collection('users')
            .where('role','==',"owner")
            .where('cityId','==',selected_city)
            .get()
            .then(function(snapshot){

                var fuel_html = '<option value="">{{ trans("lang.select_fuel_station") }}</option>';

                snapshot.forEach(function(doc){
                    var data = doc.data();
                    fuel_html += `<option value="${data.id}">${data.fuelStationName}</option>`;
                });

                $('#fuel_station_select_box').html(fuel_html);
            });
    });
    $(document).on("click","#assignFuelStationBtn",async function(){

        var stateId = $('#state_select_box').val();
        var cityId = $('#city_select_box').val();
        var pumpOwnerId = $('#fuel_station_select_box').val();

        if(!stateId || !cityId || !pumpOwnerId){
            alert("Please select state, city and fuel station");
            return;
        }

        if(selectedUserIds.length === 0){
            alert("No users selected");
            return;
        }

        jQuery("#overlay").show();

        try{

            const batch = database.batch();

            selectedUserIds.forEach(function(userId){

                const userRef = database.collection('users').doc(userId);

                batch.update(userRef,{
                    stateId: stateId,
                    cityId: cityId,
                    pumpOwnerId: pumpOwnerId
                });

            });

            await batch.commit();

            alert("Fuel station assigned successfully");

            $('#assignFuelStationModal').modal('hide');
            $('#userTable').DataTable().ajax.reload();

        }catch(error){
            console.error(error);
            alert("Something went wrong");
        }

        jQuery("#overlay").hide();
    });
    $('#assignFuelStationModal').on('hidden.bs.modal', function(){

        $('#state_select_box').html('<option value="">{{ trans("lang.select_state") }}</option>');
        $('#city_select_box').html('<option value="">{{ trans("lang.select_city") }}</option>');
        $('#fuel_station_select_box').html('<option value="">{{ trans("lang.select_fuel_station") }}</option>');

        selectedUserIds = [];
    });
</script>
@endsection