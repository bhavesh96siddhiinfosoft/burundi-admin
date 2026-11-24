@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.states_cities')}}</h3>
            </div>

            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('state') !!}">{{trans('lang.states')}}</a></li>
                    <li class="breadcrumb-item active">{{$id==''? trans('lang.add_state') : trans('lang.edit_state')}}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card pb-4">
                <div class="card-body">

                    <div class="error_top"></div>

                    <div class="row restaurant_payout_create">
                        <div class="restaurant_payout_create-inner">
                            <!-- State Section -->
                            <fieldset>
                                <legend class="">{{trans('lang.state_details')}}</legend>
                                
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.state_name')}}<span class="required-field"></span></label>
                                    <div class="col-7">
                                        <input type="text" class="form-control title" id="state_name">
                                        <div class="form-text text-muted">
                                            {{ trans("lang.state_name_help") }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <div class="form-check">
                                        <input type="checkbox" class="is_active" id="is_active" checked>
                                        <label class="col-3 control-label" for="is_active">{{trans('lang.active')}}</label>
                                    </div>
                                </div>
                            </fieldset>

                            <!-- Cities Section - Always visible -->
                            <div id="cities_section" style="margin-top: 30px;">
                                <fieldset>
                                    <legend class="">{{trans('lang.cities')}}</legend>
                                    
                                    <!-- Add New City Form -->
                                    <div class="form-group row width-100">
                                        <label class="col-3 control-label">{{trans('lang.add_city')}}</label>
                                        <div class="col-7">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="new_city_name" placeholder="{{trans('lang.enter_city_name')}}">
                                                <div class="input-group-append mt-2">
                                                    <button class="btn btn-primary" type="button" id="add_city_btn">
                                                        <i class="fa fa-plus"></i> {{trans('lang.add')}}
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.city_name_help") }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cities List -->
                                    <div class="form-group row width-100">
                                        <label class="col-3 control-label">{{trans('lang.city_list')}}</label>
                                        <div class="col-7">
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="cities_table">
                                                    <thead>
                                                        <tr>
                                                            <th width="70%">{{trans('lang.city_name')}}</th>
                                                            <th width="20%">{{trans('lang.status')}}</th>
                                                            <th width="10%">{{trans('lang.actions')}}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="cities_list">
                                                        <!-- Cities will be loaded here -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div id="no_cities_message" class="text-center text-muted">
                                                {{trans('lang.no_cities_added')}}
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary save_btn"><i class="fa fa-save"></i> {{ trans('lang.save')}}</button>
                    <a href="{!! route('state') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit City Modal -->
    <div class="modal fade" id="editCityModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('lang.edit_city')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_city_id">
                    <div class="form-group">
                        <label>{{trans('lang.city_name')}}<span class="required-field"></span></label>
                        <input type="text" class="form-control" id="edit_city_name">
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="edit_city_active">
                            <label class="form-check-label" for="edit_city_active">{{trans('lang.active')}}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans('lang.cancel')}}</button>
                    <button type="button" class="btn btn-primary" id="update_city_btn">{{trans('lang.update')}}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var database = firebase.firestore();
        var requestId = "{{$id}}";
        var cities = []; // Array to hold cities (both temporary and saved)
        var isEditing = requestId !== '';

        $(document).ready(function () {
            // Highlight active menu
            $('.states_sub_menu li').each(function () {
                var url = $(this).find('a').attr('href');
                if (url == document.referrer) {
                    $(this).find('a').addClass('active');
                    $('.states_menu').addClass('active').attr('aria-expanded', true);
                }
                $('.states_sub_menu').addClass('in').attr('aria-expanded', true);
            });

            // If editing, load state and cities data
            if (isEditing) {
                loadStateData();
            }

            // Add city button click
            $('#add_city_btn').click(addCity);

            // Edit city button (delegated event for dynamically added elements)
            $(document).on('click', '.edit-city-btn', function() {
                var cityIndex = $(this).data('index');
                editCity(cityIndex);
            });

            // Remove city button (delegated event for dynamically added elements)
            $(document).on('click', '.remove-city-btn', function() {
                var cityIndex = $(this).data('index');
                var cityName = $(this).data('name');
                removeCity(cityIndex, cityName);
            });

            // Update city button
            $('#update_city_btn').click(updateCity);

            // Enter key in city name field
            $('#new_city_name').keypress(function(e) {
                if (e.which == 13) {
                    addCity();
                    return false;
                }
            });

            // Initialize cities array
            updateCitiesTable();
        });

        // Load state data for editing
        function loadStateData() {
            $("#overlay").show();
            
            database.collection('states')
                .where("id", "==", requestId)
                .where('isDeleted', '==', false)
                .get()
                .then((snapshots) => {
                    if (!snapshots.empty) {
                        var data = snapshots.docs[0].data();
                        
                        $("#state_name").val(data.name);
                        
                        if (data.enable) {
                            $("#is_active").prop("checked", true);
                        }
                        
                        // Load existing cities
                        loadCities();
                    } else {
                        showError("{{trans('lang.state_not_found')}}");
                        setTimeout(() => {
                            window.location.href = "{{ route('state') }}";
                        }, 2000);
                    }
                    
                    $("#overlay").hide();
                })
                .catch((error) => {
                    console.error("Error loading state:", error);
                    $("#overlay").hide();
                    showError("{{trans('lang.error_loading_state')}}");
                });
        }

        // Load cities from database (for editing mode)
        function loadCities() {
            database.collection('cities')
                .where("state_id", "==", requestId)
                .where('isDeleted', '==', false)
                .orderBy('name')
                .get()
                .then((snapshots) => {
                    cities = [];
                    
                    if (!snapshots.empty) {
                        snapshots.forEach((doc) => {
                            const city = doc.data();
                            city.isNew = false; // Mark as existing city
                            city.firestoreId = city.id; // Keep original Firestore ID
                            cities.push(city);
                        });
                    }
                    
                    updateCitiesTable();
                })
                .catch((error) => {
                    console.error("Error loading cities:", error);
                    showError("{{trans('lang.error_loading_cities')}}");
                });
        }

        // Add new city (temporary for create mode, or saved for edit mode)
        function addCity() {
            const cityName = $('#new_city_name').val().trim();
            
            if (cityName === '') {
                showError("{{trans('lang.city_name_required')}}");
                return;
            }
            
            // Check if city already exists in the current list
            const existingCity = cities.find(c => 
                c.name.toLowerCase() === cityName.toLowerCase() && !c.markedForDeletion
            );
            
            if (existingCity) {
                showError("{{trans('lang.city_already_exists')}}");
                return;
            }
            
            const cityId = database.collection("tmp").doc().id;
            const cityData = {
                id: cityId,
                name: cityName,
                enable: true,
                isNew: true // Mark as new city
            };
            
            // If editing, add state_id
            if (isEditing) {
                cityData.state_id = requestId;
            }
            
            cities.push(cityData);
            $('#new_city_name').val('');
            updateCitiesTable();
            showSuccess("{{trans('lang.city_added_temp')}}");
        }

        // Edit city
        function editCity(cityIndex) {
            const city = cities[cityIndex];
            if (city) {
                $('#edit_city_id').val(cityIndex); // Store index instead of ID
                $('#edit_city_name').val(city.name);
                $('#edit_city_active').prop('checked', city.enable);
                $('#editCityModal').modal('show');
            }
        }

        // Update city
        function updateCity() {
            const cityIndex = $('#edit_city_id').val();
            const cityName = $('#edit_city_name').val().trim();
            const isActive = $('#edit_city_active').is(':checked');
            
            if (cityName === '') {
                showError("{{trans('lang.city_name_required')}}");
                return;
            }
            
            // Check for duplicate names (excluding current city and deleted ones)
            const existingCity = cities.find((c, index) => 
                index != cityIndex && 
                c.name.toLowerCase() === cityName.toLowerCase() && 
                !c.markedForDeletion
            );
            
            if (existingCity) {
                showError("{{trans('lang.city_already_exists')}}");
                return;
            }
            
            // Update the city in array
            cities[cityIndex].name = cityName;
            cities[cityIndex].enable = isActive;
            cities[cityIndex].isUpdated = true; // Mark as updated
            
            $('#editCityModal').modal('hide');
            updateCitiesTable();
            showSuccess("{{trans('lang.city_updated_temp')}}");
        }

        // Remove city from list
        function removeCity(cityIndex, cityName) {
            if (confirm("{{trans('lang.confirm_remove_city')}} '" + cityName + "'?")) {
                if (isEditing && cities[cityIndex].firestoreId) {
                    // For existing cities in edit mode, mark for deletion
                    cities[cityIndex].markedForDeletion = true;
                } else {
                    // For new cities or create mode, remove from array
                    cities.splice(cityIndex, 1);
                }
                
                updateCitiesTable();
                showSuccess("{{trans('lang.city_removed_temp')}}");
            }
        }

        // Update cities table display
        function updateCitiesTable() {
            $('#cities_list').empty();
            
            let hasCities = false;
            
            cities.forEach((city, index) => {
                // Skip cities marked for deletion
                if (city.markedForDeletion) return;
                
                hasCities = true;
                
                const statusBadge = city.enable 
                    ? '<span class="badge badge-success">{{trans("lang.active")}}</span>'
                    : '<span class="badge badge-danger">{{trans("lang.inactive")}}</span>';
                
                // Add indicator for new/updated cities
                const statusIndicator = city.isNew 
                    ? '<span class="badge badge-info mr-1">{{trans("lang.new")}}</span>' 
                    : (city.isUpdated ? '<span class="badge badge-warning mr-1">{{trans("lang.updated")}}</span>' : '');
                
                const row = `
                    <tr id="city_row_${index}">
                        <td>
                            ${statusIndicator}
                            ${city.name}
                            ${city.isNew ? ' <small class="text-muted">(New)</small>' : ''}
                        </td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-city-btn" data-index="${index}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger remove-city-btn" data-index="${index}" data-name="${city.name}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                
                $('#cities_list').append(row);
            });
            
            if (hasCities) {
                $('#no_cities_message').hide();
            } else {
                $('#no_cities_message').show();
            }
        }

        // Save state and cities
        $(".save_btn").click(function () {
            saveStateAndCities();
        });

        async function saveStateAndCities() {
            var stateName = $("#state_name").val().trim();
            var isActive = $("#is_active").is(":checked");

            // Validate state name
            if (stateName === '') {
                showError("{{trans('lang.state_name_required')}}");
                return;
            }

            $("#overlay").show();

            try {
                let stateId = requestId;
                
                // Save or update state
                if (!isEditing) {
                    // Create new state
                    stateId = database.collection("tmp").doc().id;
                    const stateData = {
                        id: stateId,
                        name: stateName,
                        enable: isActive,
                        isDeleted: false,
                        createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                        updatedAt: firebase.firestore.FieldValue.serverTimestamp()
                    };
                    
                    await database.collection('states').doc(stateId).set(stateData);
                    showSuccess("{{trans('lang.state_created_success')}}");
                } else {
                    // Update existing state
                    const stateData = {
                        name: stateName,
                        enable: isActive,
                        updatedAt: firebase.firestore.FieldValue.serverTimestamp()
                    };
                    
                    await database.collection('states').doc(stateId).update(stateData);
                    showSuccess("{{trans('lang.state_updated_success')}}");
                }

                // Save cities
                await saveCities(stateId);

                // Redirect after successful save
                setTimeout(() => {
                   window.location.href = "{{ route('state') }}";
                }, 1500);

            } catch (error) {
                console.error("Error saving:", error);
                $("#overlay").hide();
                showError(isEditing ? "{{trans('lang.error_updating_state')}}" : "{{trans('lang.error_creating_state')}}");
            }
        }

        // Save cities to Firestore
        async function saveCities(stateId) {
            const batch = database.batch();
            
            for (const city of cities) {
                if (city.markedForDeletion) {
                    // Delete existing city from Firestore
                    if (city.firestoreId) {
                        const cityRef = database.collection('cities').doc(city.firestoreId);
                        batch.update(cityRef, {
                            isDeleted: true,
                            updatedAt: firebase.firestore.FieldValue.serverTimestamp()
                        });
                    }
                } else if (city.isNew) {
                    // Add new city to Firestore
                    const cityId = database.collection("tmp").doc().id;
                    const cityData = {
                        id: cityId,
                        state_id: stateId,
                        name: city.name,
                        enable: city.enable,
                        isDeleted: false,
                        createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                        updatedAt: firebase.firestore.FieldValue.serverTimestamp()
                    };
                    
                    const cityRef = database.collection('cities').doc(cityId);
                    batch.set(cityRef, cityData);
                } else if (city.isUpdated && city.firestoreId) {
                    // Update existing city
                    const cityRef = database.collection('cities').doc(city.firestoreId);
                    batch.update(cityRef, {
                        name: city.name,
                        enable: city.enable,
                        updatedAt: firebase.firestore.FieldValue.serverTimestamp()
                    });
                }
            }
            
            // Commit all city operations
            if (cities.length > 0) {
                await batch.commit();
            }
        }

        // Show error message
        function showError(message) {
            $(".error_top").removeClass('alert-success').addClass('alert-danger').show();
            $(".error_top").html("<p><i class='fa fa-exclamation-circle'></i> " + message + "</p>");
            window.scrollTo(0, 0);
        }

        // Show success message
        function showSuccess(message) {
            $(".error_top").removeClass('alert-danger').addClass('alert-success').show();
            $(".error_top").html("<p><i class='fa fa-check-circle'></i> " + message + "</p>");
            window.scrollTo(0, 0);
        }

        // Handle Enter key in state name
        $('#state_name').keypress(function (e) {
            if (e.which == 13) {
                saveStateAndCities();
                return false;
            }
        });
    </script>

    <style>
        .badge {
            padding: 5px 10px;
            font-size: 12px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        legend {
            font-size: 18px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }
        #cities_table {
            background: #fff;
            border-radius: 5px;
        }
        .input-group-append .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        small.text-muted {
            font-size: 11px;
        }
    </style>
@endsection