@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.distributions') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.distributions') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card pb-4">
            <div class="card-body">
                <div class="alert alert-danger error_top d-none" role="alert"></div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <select id="dist_filter_station" class="form-control">
                            <option value="">{{ trans('lang.all_stations') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="dist_filter_city" class="form-control">
                            <option value="">{{ trans('lang.all_cities') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="dist_filter_status" class="form-control">
                            <option value="">{{ trans('lang.all_status') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="dist_filter_fuel" class="form-control">
                            <option value="">{{ trans('lang.all_fuel_types') }}</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="distribution_table">
                        <thead>
                            <tr>
                                <th>{{ trans('lang.station_name') }}</th>
                                <th>{{ trans('lang.fuel_type') }}</th>
                                <th>{{ trans('lang.status') }}</th>
                                <th>{{ trans('lang.distribution_registered_spots') }}</th>
                                <th>{{ trans('lang.distribution_served') }}</th>
                                <th>{{ trans('lang.distribution_quota_per_visit') }}</th>
                                <th>{{ trans('lang.distribution_capacity') }}</th>
                                <th>{{ trans('lang.fuel_request') }}</th>
                                <th>{{ trans('lang.distribution_published_at') }}</th>
                                <th>{{ trans('lang.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="distribution_list"></tbody>
                    </table>
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
    var distributionsRef = database.collection('distributions');

    var FUEL_REQUEST_STATUS_LABELS = {
        requestedByOwner: "{{ trans('lang.requested_by_owner') }}",
        approvedByAdmin: "{{ trans('lang.approved_by_admin') }}",
        paymentByOwner: "{{ trans('lang.payment_by_owner') }}",
        completedByAdmin: "{{ trans('lang.completed_by_admin') }}",
        rejectedByAdmin: "{{ trans('lang.rejected_by_admin') }}"
    };

    $(document).ready(function () {
        loadDistributionFilters();
        loadDistributions();

        $('#dist_filter_station, #dist_filter_city, #dist_filter_status, #dist_filter_fuel').on('change', loadDistributions);
    });

    function loadDistributionFilters() {
        var statusHtml = '<option value="">{{ trans("lang.all_status") }}</option>';
        DISTRIBUTION_STATUS_ORDER.concat(['cancelled']).forEach(function (status) {
            statusHtml += '<option value="' + status + '">' + escapeHtml(distributionStatusLabel(status)) + '</option>';
        });
        $('#dist_filter_status').html(statusHtml);

        database.collection('users').where('role', '==', 'owner').get().then(function (snapshot) {
            var owners = snapshot.docs.map(function (doc) { return doc.data(); });
            owners.sort(function (a, b) {
                return (a.fuelStationName || '').localeCompare(b.fuelStationName || '');
            });
            var html = '<option value="">{{ trans("lang.all_stations") }}</option>';
            owners.forEach(function (owner) {
                html += '<option value="' + escapeHtml(owner.id) + '">' + escapeHtml(owner.fuelStationName || owner.fullName || owner.id) + '</option>';
            });
            $('#dist_filter_station').html(html);
        });

        database.collection('cities').where('enable', '==', true).where('isDeleted', '==', false).get().then(function (snapshot) {
            var cities = snapshot.docs.map(function (doc) { return doc.data(); });
            cities.sort(function (a, b) {
                return (a.name || '').localeCompare(b.name || '');
            });
            var html = '<option value="">{{ trans("lang.all_cities") }}</option>';
            cities.forEach(function (city) {
                html += '<option value="' + escapeHtml(city.id) + '">' + escapeHtml(city.name) + '</option>';
            });
            $('#dist_filter_city').html(html);
        });

        database.collection('fuel_type').get().then(function (snapshot) {
            var html = '<option value="">{{ trans("lang.all_fuel_types") }}</option>';
            snapshot.forEach(function (doc) {
                html += '<option value="' + escapeHtml(doc.id) + '">' + escapeHtml(doc.data().name || '') + '</option>';
            });
            $('#dist_filter_fuel').html(html);
        });
    }

    var distributionLoadSequence = 0;

    async function loadDistributions() {
        var loadSequence = ++distributionLoadSequence;
        jQuery("#overlay").show();
        $('.error_top').addClass('d-none');

        var stationId = $('#dist_filter_station').val();
        var cityId = $('#dist_filter_city').val();
        var status = $('#dist_filter_status').val();
        var fuelTypeId = $('#dist_filter_fuel').val();

        try {
            // One equality filter only (no composite index needed); the others are applied here
            var query = distributionsRef;
            if (stationId) {
                query = query.where('ownerId', '==', stationId);
            } else if (cityId) {
                query = query.where('cityId', '==', cityId);
            }
            var snapshot = await query.get();

            var distributions = snapshot.docs.map(function (doc) {
                var data = doc.data();
                data.id = data.id || doc.id;
                return data;
            }).filter(function (data) {
                if (cityId && data.cityId !== cityId) return false;
                if (status && (data.status || 'scheduled') !== status) return false;
                if (fuelTypeId && data.fuelTypeId !== fuelTypeId) return false;
                return true;
            });

            // Newest first
            distributions.sort(function (a, b) {
                var aTime = a.publishedAt && a.publishedAt.toMillis ? a.publishedAt.toMillis() : 0;
                var bTime = b.publishedAt && b.publishedAt.toMillis ? b.publishedAt.toMillis() : 0;
                return bTime - aTime;
            });

            // Linked fuel requests
            var requestIds = [];
            distributions.forEach(function (data) {
                if (data.fuelRequestId && requestIds.indexOf(data.fuelRequestId) === -1) {
                    requestIds.push(data.fuelRequestId);
                }
            });
            var requestSnapshots = await Promise.all(requestIds.map(function (requestId) {
                return database.collection('fuel_request').doc(requestId).get();
            }));
            var requestMap = {};
            requestSnapshots.forEach(function (requestSnapshot) {
                if (requestSnapshot.exists) {
                    requestMap[requestSnapshot.id] = requestSnapshot.data();
                }
            });

            // A newer filter change is already loading
            if (loadSequence !== distributionLoadSequence) {
                return;
            }

            var html = '';
            distributions.forEach(function (data) {
                var viewRoute = '{{ route("distributions.view", ":id") }}'.replace(':id', encodeURIComponent(data.id));
                var requestHtml = '-';
                if (data.fuelRequestId) {
                    var request = requestMap[data.fuelRequestId];
                    var requestRoute = '{{ url("fuel-request/save") }}/' + encodeURIComponent(data.fuelRequestId);
                    var requestStatus = request ? (FUEL_REQUEST_STATUS_LABELS[request.status] || request.status || '') : '{{ trans("lang.distribution_request_missing") }}';
                    requestHtml = '<a href="' + requestRoute + '">#' + escapeHtml(data.fuelRequestId) + '</a>' +
                        '<br><span class="badge badge-light">' + escapeHtml(requestStatus) + '</span>';
                }

                html += '<tr>' +
                    '<td><a href="' + viewRoute + '">' + escapeHtml(data.stationName || '') + '</a>' +
                    (data.cityName ? '<br><small class="text-muted">' + escapeHtml(data.cityName) + '</small>' : '') + '</td>' +
                    '<td>' + escapeHtml(data.fuelTypeName || '') + '</td>' +
                    '<td>' + distributionStatusBadge(data.status) + '</td>' +
                    '<td>' + escapeHtml(data.registeredCount || 0) + ' / ' + escapeHtml(data.totalSpots || 0) + '</td>' +
                    '<td>' + escapeHtml(data.servedCount || 0) + '</td>' +
                    '<td>' + escapeHtml(data.quotaPerVisit || 0) + ' {{ trans("lang.liter") }}</td>' +
                    '<td>' + escapeHtml(data.capacityLitres || 0) + ' {{ trans("lang.liter") }}</td>' +
                    '<td>' + requestHtml + '</td>' +
                    '<td>' + escapeHtml(formatFirestoreDate(data.publishedAt)) + '</td>' +
                    '<td class="action-btn"><a href="' + viewRoute + '" title="{{ trans("lang.view") }}"><i class="fa fa-eye"></i></a></td>' +
                    '</tr>';
            });

            if (html === '') {
                html = '<tr><td colspan="10" class="text-center">{{ trans("lang.no_distributions_found") }}</td></tr>';
            }
            $('#distribution_list').html(html);
        } catch (error) {
            console.error(error);
            $('.error_top').removeClass('d-none').html('<p class="mb-0">' + escapeHtml(error.message) + '</p>');
        }

        jQuery("#overlay").hide();
    }
</script>
@endsection
