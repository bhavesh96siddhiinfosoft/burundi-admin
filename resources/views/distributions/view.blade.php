@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.distribution_details') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('distributions') }}">{{ trans('lang.distributions') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.distribution_details') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="alert alert-danger error_top d-none" role="alert"></div>
        <div class="alert alert-success success_top d-none" role="alert"></div>

        <div class="row">
            <!-- Distribution details -->
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 text-dark">{{ trans('lang.distribution_details') }}</h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.station_name') }}</span>
                                <span id="dist_station" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.station_address') }}</span>
                                <span id="dist_address" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.city') }}</span>
                                <span id="dist_city" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.fuel_type') }}</span>
                                <span id="dist_fuel" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.vehicle_type') }}</span>
                                <span id="dist_vehicle_type" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.distribution_registered_spots') }}</span>
                                <span id="dist_spots" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.distribution_served') }}</span>
                                <span id="dist_served" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.distribution_cancelled_count') }}</span>
                                <span id="dist_cancelled" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.distribution_quota_per_visit') }}</span>
                                <span id="dist_quota_per_visit" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.distribution_capacity') }}</span>
                                <span id="dist_capacity" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.distribution_rate_per_hour') }}</span>
                                <span id="dist_rate" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.distribution_published_at') }}</span>
                                <span id="dist_published_at" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.fuel_request') }}</span>
                                <span id="dist_fuel_request" class="text-muted text-right"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Status (F2) -->
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 text-dark">{{ trans('lang.distribution_status') }}</h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.status') }}</span>
                                <span id="dist_status"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.distribution_status_updated_at') }}</span>
                                <span id="dist_status_updated_at" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.distribution_started_at') }}</span>
                                <span id="dist_started_at" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.distribution_completed_at') }}</span>
                                <span id="dist_completed_at" class="text-muted text-right"></span>
                            </li>
                        </ul>

                        <div id="dist_supplied_gate" class="alert alert-warning d-none">
                            {{ trans('lang.distribution_supplied_gate_error') }}
                        </div>
                        <div id="dist_final_note" class="alert alert-light d-none">
                            {{ trans('lang.distribution_final_note') }}
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-primary d-none" id="dist_next_btn"></button>
                            <button type="button" class="btn btn-danger d-none" id="dist_cancel_btn">
                                <i class="fa fa-ban"></i> {{ trans('lang.distribution_cancel_btn') }}
                            </button>
                        </div>
                        <div class="form-text text-muted text-center mt-2">
                            {{ trans('lang.distribution_status_help') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registered customers -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0 text-dark">{{ trans('lang.distribution_registered_customers') }}</h5>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ trans('lang.distribution_position') }}</th>
                                <th>{{ trans('lang.name') }}</th>
                                <th>{{ trans('lang.vehicle_number') }}</th>
                                <th>{{ trans('lang.vehicle_type') }}</th>
                                <th>{{ trans('lang.status') }}</th>
                                <th>{{ trans('lang.distribution_registered_at') }}</th>
                                <th>{{ trans('lang.distribution_served_at') }}</th>
                                <th>{{ trans('lang.distribution_litres_served') }}</th>
                            </tr>
                        </thead>
                        <tbody id="registration_list"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="form-group col-12 text-center btm-btn">
            <a href="{{ route('distributions') }}" class="btn btn-default"><i class="fa fa-undo"></i> {{ trans('lang.back') }}</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('distributions.status_functions')
<script>
    var database = firebase.firestore();
    var distributionId = "{{ $id }}";
    var distributionRef = database.collection('distributions').doc(distributionId);
    var currentDistribution = null;
    var statusChangeRunning = false;

    var FUEL_REQUEST_STATUS_LABELS = {
        requestedByOwner: "{{ trans('lang.requested_by_owner') }}",
        approvedByAdmin: "{{ trans('lang.approved_by_admin') }}",
        paymentByOwner: "{{ trans('lang.payment_by_owner') }}",
        completedByAdmin: "{{ trans('lang.completed_by_admin') }}",
        rejectedByAdmin: "{{ trans('lang.rejected_by_admin') }}"
    };

    var REGISTRATION_STATUS_LABELS = {
        registered: "{{ trans('lang.booking_status_registered') }}",
        turn_approaching: "{{ trans('lang.booking_status_turn_approaching') }}",
        called: "{{ trans('lang.booking_status_called') }}",
        served: "{{ trans('lang.booking_status_served') }}",
        no_show: "{{ trans('lang.booking_status_no_show') }}",
        cancelled: "{{ trans('lang.booking_status_cancelled') }}"
    };
    var REGISTRATION_STATUS_BADGES = {
        registered: 'info',
        turn_approaching: 'warning',
        called: 'primary',
        served: 'success',
        no_show: 'secondary',
        cancelled: 'danger'
    };

    $(document).ready(function () {
        jQuery("#overlay").show();

        // Live: the counters and the queue change while the employee app serves customers
        distributionRef.onSnapshot(function (snapshot) {
            jQuery("#overlay").hide();
            if (!snapshot.exists) {
                showError("{{ trans('lang.distribution_not_found') }}");
                return;
            }
            currentDistribution = snapshot.data();
            renderDistribution(currentDistribution);
        }, function (error) {
            jQuery("#overlay").hide();
            showError(error.message);
        });

        database.collection('booking_registrations').where('distributionId', '==', distributionId)
            .onSnapshot(function (snapshot) {
                var registrations = snapshot.docs.map(function (doc) { return doc.data(); });
                renderRegistrations(registrations);
            }, function (error) {
                showError(error.message);
            });

        $('#dist_next_btn').on('click', function () {
            var nextStatus = $(this).attr('data-status');
            if (!nextStatus) return;
            var message = "{{ trans('lang.distribution_move_confirm') }}".replace(':status', distributionStatusLabel(nextStatus));
            if (!confirm(message)) return;
            runStatusChange(nextStatus);
        });

        $('#dist_cancel_btn').on('click', function () {
            if (!confirm("{{ trans('lang.distribution_cancel_confirm') }}")) return;
            runStatusChange('cancelled');
        });
    });

    async function runStatusChange(newStatus) {
        if (statusChangeRunning) return;
        statusChangeRunning = true;
        hideMessages();
        $('#dist_next_btn, #dist_cancel_btn').prop('disabled', true);
        jQuery("#overlay").show();

        try {
            var summary = await changeDistributionStatus(distributionId, newStatus);
            var message = "{{ trans('lang.distribution_status_updated') }}".replace(':status', distributionStatusLabel(newStatus)) +
                ' ' + distributionSummaryText(summary);
            $('.success_top').removeClass('d-none').html('<p class="mb-0">' + escapeHtml(message) + '</p>');
            if (summary.errors.length) {
                showError("{{ trans('lang.distribution_notify_errors') }} " + summary.errors.join(', '));
            }
            window.scrollTo(0, 0);
        } catch (error) {
            console.error(error);
            showError(error.message);
        }

        jQuery("#overlay").hide();
        statusChangeRunning = false;
        $('#dist_cancel_btn').prop('disabled', false);
        if (currentDistribution) {
            renderDistribution(currentDistribution);
        }
    }

    async function renderDistribution(data) {
        var status = data.status || 'scheduled';

        $('#dist_station').text(data.stationName || '');
        $('#dist_address').text(data.stationAddress || '');
        $('#dist_city').text(data.cityName || '');
        $('#dist_fuel').text(data.fuelTypeName || '');
        $('#dist_vehicle_type').text(data.vehicleTypeName || "{{ trans('lang.distribution_all_vehicles') }}");
        $('#dist_spots').text((data.registeredCount || 0) + ' / ' + (data.totalSpots || 0));
        $('#dist_served').text(data.servedCount || 0);
        $('#dist_cancelled').text(data.cancelledCount || 0);
        $('#dist_quota_per_visit').text((data.quotaPerVisit || 0) + ' {{ trans("lang.liter") }}');
        $('#dist_capacity').text((data.capacityLitres || 0) + ' {{ trans("lang.liter") }}');
        $('#dist_rate').text(data.avgRatePerHour ? Math.round(data.avgRatePerHour * 10) / 10 : '-');
        $('#dist_published_at').text(formatFirestoreDate(data.publishedAt));

        $('#dist_status').html(distributionStatusBadge(status));
        $('#dist_status_updated_at').text(formatFirestoreDate(data.statusUpdatedAt) || '-');
        $('#dist_started_at').text(formatFirestoreDate(data.distributionStartedAt) || '-');
        $('#dist_completed_at').text(formatFirestoreDate(data.completedAt) || '-');

        // Status buttons: only the next status, and cancel until the distribution is final
        var nextStatus = distributionNextStatus(status);
        if (nextStatus && distributionCanCancel(status)) {
            $('#dist_next_btn').removeClass('d-none').attr('data-status', nextStatus)
                .html('<i class="fa fa-arrow-right"></i> ' + escapeHtml("{{ trans('lang.distribution_move_to') }}".replace(':status', distributionStatusLabel(nextStatus))));
        } else {
            $('#dist_next_btn').addClass('d-none').attr('data-status', '');
        }
        $('#dist_cancel_btn').toggleClass('d-none', !distributionCanCancel(status));
        $('#dist_final_note').toggleClass('d-none', distributionCanCancel(status));

        // Linked fuel request, and the Supplied gate
        var requestStatus = null;
        if (data.fuelRequestId) {
            var requestRoute = '{{ url("fuel-request/save") }}/' + encodeURIComponent(data.fuelRequestId);
            try {
                var requestSnapshot = await database.collection('fuel_request').doc(data.fuelRequestId).get();
                requestStatus = requestSnapshot.exists ? requestSnapshot.data().status : null;
            } catch (error) {
                console.error(error);
            }
            var requestLabel = requestStatus ? (FUEL_REQUEST_STATUS_LABELS[requestStatus] || requestStatus) : "{{ trans('lang.distribution_request_missing') }}";
            $('#dist_fuel_request').html('<a href="' + requestRoute + '">#' + escapeHtml(data.fuelRequestId) + '</a> ' +
                '<span class="badge badge-light">' + escapeHtml(requestLabel) + '</span>');
        } else {
            $('#dist_fuel_request').text('-');
        }
        var gated = nextStatus === 'supplied' && data.fuelRequestId && requestStatus !== 'completedByAdmin';
        $('#dist_supplied_gate').toggleClass('d-none', !gated);
        $('#dist_next_btn').prop('disabled', gated || statusChangeRunning);
    }

    function renderRegistrations(registrations) {
        registrations.sort(function (a, b) {
            return (parseInt(a.position, 10) || 0) - (parseInt(b.position, 10) || 0);
        });

        var html = '';
        registrations.forEach(function (registration) {
            var badge = REGISTRATION_STATUS_BADGES[registration.status] || 'secondary';
            var label = REGISTRATION_STATUS_LABELS[registration.status] || registration.status || '';
            html += '<tr>' +
                '<td>#' + escapeHtml(registration.position || '') + '</td>' +
                '<td>' + escapeHtml(registration.customerName || '') + '</td>' +
                '<td>' + escapeHtml((registration.vehicleNumber || '').toUpperCase()) + '</td>' +
                '<td>' + escapeHtml(registration.vehicleTypeName || '') + '</td>' +
                '<td><span class="badge badge-' + badge + '">' + escapeHtml(label) + '</span></td>' +
                '<td>' + escapeHtml(formatFirestoreDate(registration.registeredAt)) + '</td>' +
                '<td>' + escapeHtml(formatFirestoreDate(registration.servedAt)) + '</td>' +
                '<td>' + (registration.litresServed ? escapeHtml(registration.litresServed) + ' {{ trans("lang.liter") }}' : '') + '</td>' +
                '</tr>';
        });

        if (html === '') {
            html = '<tr><td colspan="8" class="text-center">{{ trans("lang.distribution_no_registrations") }}</td></tr>';
        }
        $('#registration_list').html(html);
    }

    function showError(message) {
        $('.error_top').removeClass('d-none').html('<p class="mb-0">' + escapeHtml(message) + '</p>');
        window.scrollTo(0, 0);
    }

    function hideMessages() {
        $('.error_top, .success_top').addClass('d-none');
    }
</script>
@endsection
