@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.distribution_create') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('distributions') }}">{{ trans('lang.distributions') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.distribution_create') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card pb-4">
            <div class="card-body p-2">
                <div class="alert alert-danger error_top d-none" role="alert"></div>

                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{ trans('lang.distribution') }}</legend>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.station_name') }}<span class="required-field"></span></label>
                                <div class="col-7">
                                    <select id="dist_station" class="form-control">
                                        <option value="">{{ trans('lang.distribution_select_station') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.fuel_type') }}<span class="required-field"></span></label>
                                <div class="col-7">
                                    <select id="dist_fuel" class="form-control">
                                        <option value="">{{ trans('lang.distribution_select_fuel') }}</option>
                                    </select>
                                    <div class="form-text text-muted" id="dist_fuel_stock">{{ trans('lang.distribution_fuel_help') }}</div>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.vehicle_type') }}</label>
                                <div class="col-7">
                                    <select id="dist_vehicle_type" class="form-control">
                                        <option value="">{{ trans('lang.distribution_all_vehicles') }}</option>
                                    </select>
                                    <div class="form-text text-muted">{{ trans('lang.distribution_vehicle_type_help') }}</div>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.distribution_total_spots') }}<span class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="number" min="1" step="1" id="dist_total_spots" class="form-control">
                                    <div class="form-text text-muted">{{ trans('lang.distribution_total_spots_help') }}</div>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.distribution_quota_per_visit') }}<span class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="number" min="0" step="any" id="dist_quota_per_visit" class="form-control">
                                    <div class="form-text text-muted">{{ trans('lang.distribution_quota_per_visit_help') }}</div>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.distribution_capacity') }}<span class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="number" min="0" step="any" id="dist_capacity_litres" class="form-control">
                                    <div class="form-text text-muted">{{ trans('lang.distribution_capacity_help') }}</div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary create_distribution_btn"><i class="fa fa-save"></i> {{ trans('lang.save') }}</button>
                    <a href="{{ route('distributions') }}" class="btn btn-default"><i class="fa fa-undo"></i> {{ trans('lang.cancel') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('distributions.status_functions')
<script>
    var database = firebase.firestore();
    var owners = {};
    var vehicleTypes = {};
    var fuelTypeNames = {};
    var saving = false;

    $(document).ready(async function () {
        $('#dist_station').on('change', renderStationFuels);
        $('#dist_fuel').on('change', renderFuelStock);
        $('.create_distribution_btn').on('click', createDistribution);

        jQuery("#overlay").show();
        try {
            var results = await Promise.all([
                database.collection('users').where('role', '==', 'owner').get(),
                database.collection('vehicle_type').where('enable', '==', true).get(),
                database.collection('fuel_type').get()
            ]);

            var stationIds = [];
            results[0].forEach(function (doc) {
                var owner = doc.data();
                if (owner.isDeletedAccount === true) {
                    return;
                }
                owners[doc.id] = owner;
                stationIds.push(doc.id);
            });
            stationIds.sort(function (a, b) {
                return (owners[a].fuelStationName || '').localeCompare(owners[b].fuelStationName || '');
            });
            var stationHtml = '<option value="">{{ trans("lang.distribution_select_station") }}</option>';
            stationIds.forEach(function (id) {
                stationHtml += '<option value="' + escapeHtml(id) + '">' + escapeHtml(owners[id].fuelStationName || owners[id].fullName || id) + '</option>';
            });
            // Hundreds of stations: searchable, as the other long selects of the panel
            $('#dist_station').html(stationHtml).select2({ width: '100%' });

            var vehicleHtml = '<option value="">{{ trans("lang.distribution_all_vehicles") }}</option>';
            results[1].forEach(function (doc) {
                vehicleTypes[doc.id] = doc.data().name || '';
                vehicleHtml += '<option value="' + escapeHtml(doc.id) + '">' + escapeHtml(doc.data().name || '') + '</option>';
            });
            $('#dist_vehicle_type').html(vehicleHtml);

            results[2].forEach(function (doc) {
                fuelTypeNames[doc.id] = doc.data().name || '';
            });
        } catch (error) {
            console.error(error);
            showError(error.message);
        }
        jQuery("#overlay").hide();
    });

    function stationFuels(ownerId) {
        var owner = owners[ownerId];
        return owner && Array.isArray(owner.fuel) ? owner.fuel.filter(function (fuel) { return fuel && fuel.fuelTypeId; }) : [];
    }

    // The fuel is one of the station's fuels (users/{ownerId}.fuel)
    function renderStationFuels() {
        var html = '<option value="">{{ trans("lang.distribution_select_fuel") }}</option>';
        stationFuels($('#dist_station').val()).forEach(function (fuel) {
            html += '<option value="' + escapeHtml(fuel.fuelTypeId) + '">' + escapeHtml(fuel.name || fuelTypeNames[fuel.fuelTypeId] || '') + '</option>';
        });
        $('#dist_fuel').html(html);
        renderFuelStock();
    }

    function renderFuelStock() {
        var fuelTypeId = $('#dist_fuel').val();
        var fuel = stationFuels($('#dist_station').val()).find(function (item) { return item.fuelTypeId === fuelTypeId; });
        $('#dist_fuel_stock').text(fuel
            ? "{{ trans('lang.distribution_fuel_stock') }}".replace(':stock', fuel.availableFuelQuota || 0).replace(':capacity', fuel.fuelCapacity || 0)
            : "{{ trans('lang.distribution_fuel_help') }}");
    }

    function readNumber(selector) {
        var value = $(selector).val().trim();
        return value === '' ? NaN : Number(value);
    }

    async function createDistribution() {
        if (saving) {
            return;
        }
        hideError();

        var ownerId = $('#dist_station').val();
        var owner = owners[ownerId];
        var fuelTypeId = $('#dist_fuel').val();
        var fuel = stationFuels(ownerId).find(function (item) { return item.fuelTypeId === fuelTypeId; });
        var vehicleTypeId = $('#dist_vehicle_type').val();
        var totalSpots = readNumber('#dist_total_spots');
        var quotaPerVisit = readNumber('#dist_quota_per_visit');
        var capacityLitres = readNumber('#dist_capacity_litres');

        if (!owner) {
            return showError("{{ trans('lang.distribution_station_required') }}");
        }
        if (!fuel) {
            return showError("{{ trans('lang.distribution_fuel_required') }}");
        }
        if (!owner.cityId) {
            return showError("{{ trans('lang.distribution_station_city_error') }}");
        }
        if (!Number.isInteger(totalSpots) || totalSpots < 1) {
            return showError("{{ trans('lang.distribution_total_spots_error') }}");
        }
        if (!(quotaPerVisit > 0)) {
            return showError("{{ trans('lang.distribution_quota_per_visit_error') }}");
        }
        if (!(capacityLitres > 0)) {
            return showError("{{ trans('lang.distribution_capacity_error') }}");
        }

        saving = true;
        jQuery("#overlay").show();
        try {
            var citySnapshot = await database.collection('cities').doc(owner.cityId).get();
            var city = citySnapshot.exists ? citySnapshot.data() : {};
            var distributionRef = database.collection('distributions').doc();
            var now = firebase.firestore.FieldValue.serverTimestamp();

            // B1.1, as the owner app wrote it: station fields copied from the owner and the city, counters at 0.
            // A distribution made in the panel belongs to no fuel request, so fuelRequestId is not written.
            await distributionRef.set({
                id: distributionRef.id,
                ownerId: ownerId,
                stationName: owner.fuelStationName || owner.fullName || '',
                stationAddress: owner.fuelStationAddress || '',
                stationImage: (owner.fuelStationImage && owner.fuelStationImage.url) || null,
                stationPhone: owner.stationPhone || null,
                openingHours: owner.openingHours || null,
                services: Array.isArray(owner.stationServices) ? owner.stationServices : [],
                stateId: owner.stateId || city.state_id || null,
                cityId: owner.cityId,
                cityName: city.name || '',
                fuelTypeId: fuelTypeId,
                fuelTypeName: fuel.name || fuelTypeNames[fuelTypeId] || '',
                vehicleTypeId: vehicleTypeId || null,
                vehicleTypeName: vehicleTypeId ? (vehicleTypes[vehicleTypeId] || null) : null,
                totalSpots: totalSpots,
                capacityLitres: capacityLitres,
                quotaPerVisit: quotaPerVisit,
                status: 'scheduled',
                positionCounter: 0,
                registeredCount: 0,
                servedCount: 0,
                cancelledCount: 0,
                servedPositions: [],
                cancelledPositions: [],
                queue: {},
                avgRatePerHour: null,
                publishedAt: now,
                publishedBy: DISTRIBUTION_ADMIN_ID,
                statusUpdatedAt: now,
                updatedAt: now,
                distributionStartedAt: null,
                completedAt: null
            });
            logFirebaseActivity('#' + distributionRef.id + ' {{ trans("lang.distribution") }} : {{ trans("lang.created") }}');
            window.location.href = '{{ route("distributions.view", ":id") }}'.replace(':id', encodeURIComponent(distributionRef.id));
        } catch (error) {
            console.error(error);
            jQuery("#overlay").hide();
            saving = false;
            showError(error.message);
        }
    }

    function showError(message) {
        $('.error_top').removeClass('d-none').html('<p class="mb-0">' + escapeHtml(message) + '</p>');
        window.scrollTo(0, 0);
    }

    function hideError() {
        $('.error_top').addClass('d-none');
    }
</script>
@endsection
