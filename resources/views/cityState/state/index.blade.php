@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.states')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.states')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li class="nav-item">
                                <a class="nav-link active" href="{!! url()->current() !!}">
                                    <i class="fa fa-list mr-2"></i>{{trans('lang.states_list')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{!! route('stateSave') !!}">
                                    <i class="fa fa-plus mr-2"></i>{{trans('lang.add_state')}}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <!-- State Count and Summary -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h2 class="mb-0" id="states_count">0</h2>
                                                <h6 class="text-white mb-0">{{trans('lang.total_states')}}</h6>
                                            </div>
                                            <div class="ml-auto">
                                                <i class="fa fa-globe fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h2 class="mb-0" id="active_states_count">0</h2>
                                                <h6 class="text-white mb-0">{{trans('lang.active_states')}}</h6>
                                            </div>
                                            <div class="ml-auto">
                                                <i class="fa fa-check-circle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h2 class="mb-0" id="total_cities_count">0</h2>
                                                <h6 class="text-white mb-0">{{trans('lang.total_cities')}}</h6>
                                            </div>
                                            <div class="ml-auto">
                                                <i class="fa fa-building fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search and Filter -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <!-- <div class="input-group">
                                        <input type="text" class="form-control" id="search_state" 
                                               placeholder="{{trans('lang.search_states')}}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="clear_search">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                            <!-- <div class="col-md-6 text-right">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary" id="filter_all">
                                        {{trans('lang.all')}}
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="filter_active">
                                        {{trans('lang.active')}}
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="filter_inactive">
                                        {{trans('lang.inactive')}}
                                    </button>
                                </div>
                            </div> -->
                        </div>

                        <!-- States Table -->
                        <div class="table-responsive m-t-10">
                            <table id="statesTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <?php if (in_array('state.delete', json_decode(@session('user_permissions')))) { ?>
                                            <th class="delete-all" width="5%">
                                                <input type="checkbox" id="select_all">
                                                <label for="select_all">
                                                    <a id="deleteSelected" class="do_not_delete" href="javascript:void(0)">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </label>
                                            </th>
                                        <?php } ?>
                                        <th width="30%">{{trans('lang.state_name')}}</th>
                                        <th width="25%">{{trans('lang.cities')}}</th>
                                        <th width="15%">{{trans('lang.status')}}</th>
                                        <th width="10%">{{trans('lang.updated')}}</th>
                                        <th width="15%">{{trans('lang.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody id="states_list">
                                    <!-- States will be loaded here -->
                                </tbody>
                            </table>
                        </div>

                        <!-- No Results Message -->
                        <div id="no_results" class="text-center py-5" style="display: none;">
                            <i class="fa fa-database fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">{{trans('lang.no_states_found')}}</h4>
                            <p class="text-muted">{{trans('lang.start_by_adding_states')}}</p>
                            <a href="{!! route('stateSave') !!}" class="btn btn-primary mt-2">
                                <i class="fa fa-plus mr-2"></i>{{trans('lang.add_first_state')}}
                            </a>
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
    var database = firebase.firestore();
    var placeholderImage = "{{ asset('/images/default_user.png') }}";
    var deleteMsg = "{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg = "{{trans('lang.selected_delete_alert')}}";
    var user_permissions = '<?php echo @session('user_permissions')?>';
    user_permissions = JSON.parse(user_permissions);
    var checkDeletePermission = $.inArray('state.delete', user_permissions) >= 0;
    var currentFilter = 'all';
    var allStates = [];
    var citiesData = {};

    $(document).ready(function () {
        loadStates();
        
        // Search functionality
        $('#search_state').on('keyup', function() {
            filterStates();
        });
        
        $('#clear_search').click(function() {
            $('#search_state').val('');
            filterStates();
        });
        
        // Filter buttons
        $('#filter_all').click(function() {
            currentFilter = 'all';
            updateFilterButtons();
            filterStates();
        });
        
        $('#filter_active').click(function() {
            currentFilter = 'active';
            updateFilterButtons();
            filterStates();
        });
        
        $('#filter_inactive').click(function() {
            currentFilter = 'inactive';
            updateFilterButtons();
            filterStates();
        });
        
        // Select all checkbox
        $('#select_all').click(function () {
            $("#statesTable .state-checkbox").prop('checked', $(this).prop('checked'));
        });
        
        // Delete selected
        $("#deleteSelected").click(function () {
            deleteSelectedStates();
        });
    });
    
    function updateFilterButtons() {
        $('#filter_all, #filter_active, #filter_inactive').removeClass('btn-primary').addClass('btn-outline-secondary');
        $('#filter_' + currentFilter).removeClass('btn-outline-secondary').addClass('btn-primary');
    }
    
    async function loadStates() {
        $("#overlay").show();
        
        try {
            // Load all states
            const statesSnapshot = await database.collection('states')
                .where('isDeleted', '==', false)
                .orderBy('name')
                .get();
            
            allStates = [];
            citiesData = {};
            
            // Load all cities for counting
            const citiesSnapshot = await database.collection('cities')
                .where('isDeleted', '==', false)
                .get();
            
            // Count cities per state
            citiesSnapshot.forEach((doc) => {
                const city = doc.data();
                if (!citiesData[city.state_id]) {
                    citiesData[city.state_id] = [];
                }
                citiesData[city.state_id].push(city);
            });
            
            // Process states
            let states = [];
            let activeCount = 0;
            let totalCitiesCount = 0;
            
            statesSnapshot.forEach((doc) => {
                const state = doc.data();
                state.cities_count = citiesData[state.id] ? citiesData[state.id].length : 0;
                totalCitiesCount += state.cities_count;
                
                if (state.enable) activeCount++;
                
                states.push(state);
                allStates.push(state);
            });
            
            // Update counts
            $('#states_count').text(states.length);
            $('#active_states_count').text(activeCount);
            $('#total_cities_count').text(totalCitiesCount);
            
            // Display states
            displayStates(states);
            
        } catch (error) {
            console.error("Error loading states:", error);
            showError("{{trans('lang.error_loading_states')}}");
        }
        
        $("#overlay").hide();
    }
    
    function displayStates(states) {
        const statesList = $('#states_list');
        statesList.empty();
        
        if (states.length === 0) {
            $('#no_results').show();
            $('#statesTable').hide();
            return;
        }
        
        $('#no_results').hide();
        $('#statesTable').show();
        
        states.forEach((state) => {
            const route = '{{route("stateSave",":id")}}'.replace(':id', state.id);
            const cities = citiesData[state.id] || [];
            const cityNames = cities.slice(0, 3).map(c => c.name).join(', ');
            const moreCities = cities.length > 3 ? ` +${cities.length - 3} more` : '';
            const updatedDate = state.updatedAt ? new Date(state.updatedAt.seconds * 1000).toLocaleDateString() : '-';
            
            let row = '<tr>';
            
            // Checkbox column (if delete permission)
            if (checkDeletePermission) {
                row += `<td class="delete-all">
                            <input type="checkbox" id="state_${state.id}" class="state-checkbox" data-id="${state.id}">
                            <label for="state_${state.id}"></label>
                        </td>`;
            }
            
            // State name column
            row += `<td>
                        <a href="${route}" class="font-weight-bold">${state.name}</a>
                        <br><small class="text-muted">ID: ${state.id}</small>
                    </td>`;
            
            // Cities column
            row += `<td>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-info mr-2">${state.cities_count}</span>
                            <span class="city-list">${cityNames}${moreCities}</span>
                        </div>`;
            
            if (cities.length > 0) {
                row += `<button class="btn btn-sm btn-outline-info mt-1 view-cities-btn" data-state-id="${state.id}" data-state-name="${state.name}">
                            <i class="fa fa-eye"></i> {{trans('lang.view_all_cities')}}
                        </button>`;
            }
            row += `</td>`;
            
            // Status column
            const statusBadge = state.enable 
                ? '<span class="badge badge-success">{{trans("lang.active")}}</span>'
                : '<span class="badge badge-danger">{{trans("lang.inactive")}}</span>';
            
            row += `<td>${statusBadge}</td>`;
            
            // Updated date column
            row += `<td><small class="text-muted">${updatedDate}</small></td>`;
            
            // Actions column
            row += `<td class="action-btn">
                        <a href="${route}" class="btn btn-sm btn-info mr-1" title="{{trans('lang.edit')}}">
                            <i class="fa fa-edit"></i>
                        </a>`;
            
            if (checkDeletePermission) {
                row += `<button class="btn btn-sm btn-danger delete-state-btn" data-id="${state.id}" data-name="${state.name}" title="{{trans('lang.delete')}}">
                            <i class="fa fa-trash"></i>
                        </button>`;
            }
            row += `</td></tr>`;
            
            statesList.append(row);
        });
        
        // Initialize DataTable
        if (checkDeletePermission) {
            $('#statesTable').DataTable({
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [0, 5] },
                    { searchable: false, targets: [0, 4, 5] }
                ],
                "language": {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}"
                },
                destroy: true
            });
        } else {
            $('#statesTable').DataTable({
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [4] },
                    { searchable: false, targets: [4] }
                ],
                "language": {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}"
                },
                destroy: true
            });
        }
        
        // Bind event handlers
        bindEventHandlers();
    }
    
    function filterStates() {
        const searchTerm = $('#search_state').val().toLowerCase();
        
        let filteredStates = allStates.filter(state => {
            // Search filter
            const matchesSearch = searchTerm === '' || 
                state.name.toLowerCase().includes(searchTerm) ||
                state.id.toLowerCase().includes(searchTerm);
            
            // Status filter
            let matchesFilter = true;
            if (currentFilter === 'active') {
                matchesFilter = state.enable === true;
            } else if (currentFilter === 'inactive') {
                matchesFilter = state.enable === false;
            }
            
            return matchesSearch && matchesFilter;
        });
        
        // Update counts for filtered results
        const activeCount = filteredStates.filter(s => s.enable).length;
        const totalCitiesCount = filteredStates.reduce((sum, state) => sum + state.cities_count, 0);
        
        $('#states_count').text(filteredStates.length);
        $('#active_states_count').text(activeCount);
        $('#total_cities_count').text(totalCitiesCount);
        
        displayStates(filteredStates);
    }
    
    function bindEventHandlers() {
        // View cities button
        $('.view-cities-btn').click(function() {
            const stateId = $(this).data('state-id');
            const stateName = $(this).data('state-name');
            viewCities(stateId, stateName);
        });
        
        // Toggle state active status
        $('.status-toggle').change(function() {
            const stateId = $(this).data('id');
            const isActive = $(this).is(':checked');
            toggleStateStatus(stateId, isActive);
        });
        
        // Delete single state
        $('.delete-state-btn').click(function() {
            const stateId = $(this).data('id');
            const stateName = $(this).data('name');
            deleteState(stateId, stateName);
        });
    }
    
    async function viewCities(stateId, stateName) {
        $("#overlay").show();
        
        try {
            const snapshot = await database.collection('cities')
                .where('state_id', '==', stateId)
                .where('isDeleted', '==', false)
                .orderBy('name')
                .get();
            
            let cities = [];
            snapshot.forEach((doc) => {
                cities.push(doc.data());
            });
            
            // Show cities in modal
            let citiesList = '';
            if (cities.length > 0) {
                cities.forEach(city => {
                    const statusBadge = city.enable 
                        ? '<span class="badge badge-success">{{trans("lang.active")}}</span>'
                        : '<span class="badge badge-danger">{{trans("lang.inactive")}}</span>';
                    
                    citiesList += `<tr>
                        <td>${city.name}</td>
                        <td>${statusBadge}</td>
                        <td><small class="text-muted">${city.id}</small></td>
                    </tr>`;
                });
            } else {
                citiesList = `<tr><td colspan="3" class="text-center">{{trans('lang.no_cities_found')}}</td></tr>`;
            }
            
            const modalHtml = `
                <div class="modal fade" id="citiesModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${stateName} - {{trans('lang.cities')}} (${cities.length})</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{trans('lang.city_name')}}</th>
                                                <th>{{trans('lang.status')}}</th>
                                                <th>{{trans('lang.id')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>${citiesList}</tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans('lang.close')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            $('#citiesModal').remove();
            $('body').append(modalHtml);
            $('#citiesModal').modal('show');
            
        } catch (error) {
            console.error("Error loading cities:", error);
            showError("{{trans('lang.error_loading_cities')}}");
        }
        
        $("#overlay").hide();
    }
    
    async function toggleStateStatus(stateId, isActive) {
        try {
            await database.collection('states').doc(stateId).update({
                enable: isActive,
                updatedAt: firebase.firestore.FieldValue.serverTimestamp()
            });
            
            // Update the state in allStates array
            const stateIndex = allStates.findIndex(s => s.id === stateId);
            if (stateIndex !== -1) {
                allStates[stateIndex].enable = isActive;
            }
            
            showSuccess(`State ${isActive ? 'activated' : 'deactivated'} successfully`);
            filterStates();
        } catch (error) {
            console.error("Error updating state status:", error);
            showError("{{trans('lang.error_updating_status')}}");
        }
    }
    
    function deleteState(stateId, stateName) {
        if (confirm(`{{trans('lang.confirm_delete_state')}} "${stateName}"?`)) {
            $("#overlay").show();
            
            // Soft delete state
            database.collection('states').doc(stateId).update({
                isDeleted: true,
                updatedAt: firebase.firestore.FieldValue.serverTimestamp()
            })
            .then(() => {
                // Remove from allStates array
                allStates = allStates.filter(s => s.id !== stateId);
                
                // Refresh display
                filterStates();
                showSuccess("{{trans('lang.state_deleted_success')}}");
                $("#overlay").hide();
            })
            .catch((error) => {
                console.error("Error deleting state:", error);
                $("#overlay").hide();
                showError("{{trans('lang.error_deleting_state')}}");
            });
        }
    }
    
    function deleteSelectedStates() {
        const selectedIds = [];
        $('.state-checkbox:checked').each(function() {
            selectedIds.push($(this).data('id'));
        });
        
        if (selectedIds.length === 0) {
            alert("{{trans('lang.select_delete_alert')}}");
            return;
        }
        
        if (confirm(`{{trans('lang.confirm_delete_selected')}} ${selectedIds.length} state(s)?`)) {
            $("#overlay").show();
            
            const batch = database.batch();
            selectedIds.forEach(id => {
                const stateRef = database.collection('states').doc(id);
                batch.update(stateRef, {
                    isDeleted: true,
                    updatedAt: firebase.firestore.FieldValue.serverTimestamp()
                });
            });
            
            batch.commit()
                .then(() => {
                    // Remove from allStates array
                    selectedIds.forEach(id => {
                        allStates = allStates.filter(s => s.id !== id);
                    });
                    
                    // Refresh display
                    filterStates();
                    $('#select_all').prop('checked', false);
                    showSuccess(`{{trans('lang.selected_states_deleted')}} ${selectedIds.length} state(s)`);
                    $("#overlay").hide();
                })
                .catch((error) => {
                    console.error("Error deleting selected states:", error);
                    $("#overlay").hide();
                    showError("{{trans('lang.error_deleting_selected')}}");
                });
        }
    }
    
    function showError(message) {
        // You can implement a toast or alert here
        alert('Error: ' + message);
    }
    
    function showSuccess(message) {
        // You can implement a toast or alert here
        // alert('Success: ' + message);
        console.log('Success:', message);
    }
</script>

<style>
    .badge {
        font-size: 12px;
        font-weight: 500;
    }
    .action-btn a, .action-btn button {
        margin: 0 2px;
    }
    .city-list {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .view-cities-btn {
        font-size: 11px;
        padding: 2px 8px;
    }
    .table-responsive {
        border-radius: 5px;
        border: 1px solid #f0f0f0;
    }
    #search_state {
        border-radius: 4px;
    }
    .btn-group .btn {
        border-radius: 4px !important;
    }
    .card.bg-primary, .card.bg-success, .card.bg-info {
        border-radius: 8px;
        border: none;
    }
    .card.bg-primary:hover, .card.bg-success:hover, .card.bg-info:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
</style>
@endsection