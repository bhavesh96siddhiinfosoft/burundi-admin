@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.receive_fuel_title') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('fuel.request') }}">{{ trans('lang.fuel_request') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.receive_fuel_title') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="alert alert-danger error_top d-none" role="alert"></div>
        <div class="alert alert-info d-none" id="receive_intro"></div>

        <div class="row">
            <!-- Fuel request -->
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 text-dark">{{ trans('lang.fuel_request_details') }}</h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.request_id') }}</span>
                                <span id="receive_request_id" class="text-muted"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.station_name') }}</span>
                                <span id="receive_station" class="text-muted text-right"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.fuel_type') }}</span>
                                <span id="receive_fuel" class="text-muted"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.receive_fuel_requested') }}</span>
                                <span id="receive_requested" class="text-muted"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.receive_fuel_completed_on') }}</span>
                                <span id="receive_completed_on" class="text-muted"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Station stock for this fuel -->
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 text-dark">{{ trans('lang.receive_fuel_station_stock') }}</h5>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.receive_fuel_tank_capacity') }}</span>
                                <span id="receive_capacity" class="text-muted"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.receive_fuel_total_quota') }}</span>
                                <span id="receive_total" class="text-muted"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="font-weight-bold">{{ trans('lang.receive_fuel_current_stock') }}</span>
                                <span id="receive_available" class="text-muted"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step: received fuel -->
        <div class="card shadow-sm mb-4 d-none" id="receive_form_card">
            <div class="card-header bg-light">
                <h5 class="mb-0 text-dark">{{ trans('lang.receive_fuel_title') }}</h5>
            </div>
            <div class="card-body">
                <div class="form-group row width-50">
                    <label class="col-4 control-label">{{ trans('lang.receive_fuel_received_litres') }}<span class="required-field"></span></label>
                    <div class="col-7">
                        <input type="number" id="receive_litres" class="form-control" min="0" step="any">
                        <div class="form-text text-muted">{{ trans('lang.receive_fuel_received_litres_help') }}</div>
                    </div>
                </div>
                <div class="form-group row width-50">
                    <label class="col-4 control-label">{{ trans('lang.fuel_receipt_received_at') }}<span class="required-field"></span></label>
                    <div class="col-7">
                        <input type="datetime-local" id="receive_received_at" class="form-control">
                    </div>
                </div>
                <div class="form-group row width-50">
                    <label class="col-4 control-label">{{ trans('lang.note') }}</label>
                    <div class="col-7">
                        <input type="text" id="receive_note" class="form-control">
                    </div>
                </div>

                <div class="alert alert-light border mb-2" id="receive_preview"></div>
                <div class="form-text text-muted">{{ trans('lang.receive_fuel_help') }}</div>
            </div>
            <div class="card-footer bg-light border-top text-center">
                <button type="button" class="btn btn-primary" id="receive_add_btn">
                    <i class="fa fa-plus"></i> {{ trans('lang.receive_fuel_add_btn') }}
                </button>
                <a href="{{ route('fuel.request') }}" class="btn btn-outline-secondary">
                    {{ trans('lang.receive_fuel_skip') }}
                </a>
            </div>
        </div>

        <!-- Done, or already added -->
        <div class="card shadow-sm mb-4 d-none" id="receive_done_card">
            <div class="card-body">
                <div class="alert alert-success mb-3" id="receive_done_text"></div>
                <div class="alert alert-info mb-3 d-none" id="receive_distribution_next">
                    {{ trans('lang.receive_fuel_distribution_next') }}
                    <a href="#" id="receive_distribution_link" class="btn btn-sm btn-info ml-2"><i class="fa fa-eye"></i> {{ trans('lang.distribution_open') }}</a>
                </div>
                <div class="text-center">
                    <a href="#" id="receive_history_link" class="btn btn-info"><i class="fa fa-history"></i> {{ trans('lang.fuel_receiving_history') }}</a>
                    <a href="{{ route('fuel.request') }}" class="btn btn-default"><i class="fa fa-undo"></i> {{ trans('lang.receive_fuel_back_to_requests') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('distributions.status_functions')
@include('fuel_request.receipt_functions')
<script>
    var database = firebase.firestore();
    var requestId = "{{ $id }}";
    var fuelRequest = null;
    var station = null;
    var stationFuel = null;
    var linkedDistribution = null;
    var addRunning = false;

    $(document).ready(async function () {
        $('#receive_litres').on('input change', updatePreview);
        $('#receive_add_btn').on('click', addToStock);

        jQuery("#overlay").show();
        try {
            var requestSnapshot = await database.collection('fuel_request').doc(requestId).get();
            if (!requestSnapshot.exists) {
                showError("{{ trans('lang.fuel_request_not_found') }}");
                return;
            }
            fuelRequest = requestSnapshot.data();
            fuelRequest.id = fuelRequest.id || requestSnapshot.id;

            $('#receive_request_id').html('<a href="{{ url("fuel-request/save") }}/' + encodeURIComponent(requestId) + '">#' + escapeHtml(requestId) + '</a>');
            $('#receive_requested').text((fuelRequest.fuelQuota || 0) + ' {{ trans("lang.liter") }}');
            $('#receive_completed_on').text(formatFirestoreDate(fuelRequest.status === 'completedByAdmin' ? fuelRequest.updatedAt : null) || '-');

            if (fuelRequest.ownerId) {
                var ownerSnapshot = await database.collection('users').doc(fuelRequest.ownerId).get();
                station = ownerSnapshot.exists ? ownerSnapshot.data() : null;
            }
            if (!station) {
                showError("{{ trans('lang.fuel_receipt_station_not_found') }}");
                return;
            }
            $('#receive_station').text((station.fuelStationName || '') + (station.fullName ? ' (' + station.fullName + ')' : ''));
            $('#receive_history_link').attr('href', '{{ route("owners.fuelReceipts", ":id") }}'.replace(':id', encodeURIComponent(fuelRequest.ownerId)));

            stationFuel = (station.fuel || []).find(function (fuel) { return fuel.fuelTypeId === fuelRequest.fuelTypeId; });
            if (!stationFuel) {
                showError("{{ trans('lang.fuel_receipt_fuel_error') }}");
                return;
            }
            renderStock(stationFuel);

            await loadLinkedDistribution();

            if (fuelRequest.status !== 'completedByAdmin') {
                showError("{{ trans('lang.receive_fuel_not_completed') }}");
                return;
            }

            var receipt = await findFuelRequestReceipt(requestId);
            if (receipt) {
                showAlreadyAdded(receipt);
                return;
            }

            $('#receive_intro').removeClass('d-none').text("{{ trans('lang.receive_fuel_intro') }}".replace(':id', '#' + requestId));
            $('#receive_litres').val(parseFloat(fuelRequest.fuelQuota) > 0 ? parseFloat(fuelRequest.fuelQuota) : '');
            $('#receive_received_at').val(toInputDateTime(new Date()));
            $('#receive_form_card').removeClass('d-none');
            updatePreview();
        } catch (error) {
            console.error(error);
            showError(error.message);
        } finally {
            jQuery("#overlay").hide();
        }
    });

    function renderStock(fuel) {
        $('#receive_fuel').text(fuel.name || '');
        $('#receive_capacity').text((fuel.fuelCapacity || 0) + ' {{ trans("lang.liter") }}');
        $('#receive_total').text((fuel.totalFuelQuota || 0) + ' {{ trans("lang.liter") }}');
        $('#receive_available').text((fuel.availableFuelQuota || 0) + ' {{ trans("lang.liter") }}');
    }

    function updatePreview() {
        var litres = parseFloat($('#receive_litres').val());
        if (!stationFuel || isNaN(litres) || litres <= 0) {
            $('#receive_preview').removeClass('alert-danger').addClass('alert-light').text("{{ trans('lang.fuel_receipt_litres_error') }}");
            return;
        }
        var stock = stationStockAfterReceipt([stationFuel], stationFuel.fuelTypeId, litres);
        if (stock.error === 'capacity') {
            $('#receive_preview').removeClass('alert-light').addClass('alert-danger').text(
                "{{ trans('lang.receive_fuel_capacity_error') }}".replace(':new', stock.newAvailable).replace(':capacity', stock.capacity));
            return;
        }
        $('#receive_preview').removeClass('alert-danger').addClass('alert-light').text(
            "{{ trans('lang.receive_fuel_new_stock') }}"
                .replace(':new', stock.after.available)
                .replace(':current', parseFloat(stationFuel.availableFuelQuota) || 0)
                .replace(':received', litres));
    }

    async function addToStock() {
        if (addRunning) return;
        $('.error_top').addClass('d-none');

        var litres = parseFloat($('#receive_litres').val());
        var receivedAtValue = $('#receive_received_at').val();
        var receivedAt = receivedAtValue ? new Date(receivedAtValue) : null;
        var note = $('#receive_note').val().trim();

        if (isNaN(litres) || litres <= 0) {
            return showError("{{ trans('lang.fuel_receipt_litres_error') }}");
        }
        if (!receivedAt || isNaN(receivedAt.getTime()) || receivedAt.getTime() > Date.now()) {
            return showError("{{ trans('lang.fuel_receipt_received_at_error') }}");
        }
        var message = "{{ trans('lang.receive_fuel_confirm') }}"
            .replace(':litres', litres)
            .replace(':fuel', stationFuel.name || '')
            .replace(':station', station.fuelStationName || '');
        if (!confirm(message)) return;

        addRunning = true;
        $('#receive_add_btn').prop('disabled', true);
        jQuery("#overlay").show();
        try {
            // A receipt written before the request_<id> naming also counts as added
            var existing = await findFuelRequestReceipt(requestId);
            if (existing) {
                showAlreadyAdded(existing);
                return;
            }
            var stock = await addReceivedFuel(requestId, litres, receivedAt, note);
            logFirebaseActivity('#' + requestId + ' {{ trans("lang.fuel_request") }} : {{ trans("lang.receive_fuel_title") }} ' + litres + ' L');

            stationFuel = stock.fuel.find(function (fuel) { return fuel.fuelTypeId === fuelRequest.fuelTypeId; });
            renderStock(stationFuel);
            $('#receive_intro, #receive_form_card').addClass('d-none');
            $('#receive_done_text').text("{{ trans('lang.receive_fuel_done') }}"
                .replace(':litres', litres)
                .replace(':fuel', stock.fuelName)
                .replace(':station', station.fuelStationName || '')
                .replace(':stock', stock.after.available));
            showDistributionNext();
            $('#receive_done_card').removeClass('d-none');
            window.scrollTo(0, 0);
        } catch (error) {
            console.error(error);
            showError(error.message);
        } finally {
            jQuery("#overlay").hide();
            addRunning = false;
            $('#receive_add_btn').prop('disabled', false);
        }
    }

    function showAlreadyAdded(receipt) {
        $('#receive_intro, #receive_form_card').addClass('d-none');
        $('#receive_done_text').removeClass('alert-success').addClass('alert-warning').text(
            "{{ trans('lang.receive_fuel_already_added') }}"
                .replace(':litres', receipt.litres)
                .replace(':date', formatFirestoreDate(receipt.receivedAt)));
        showDistributionNext();
        $('#receive_done_card').removeClass('d-none');
    }

    async function loadLinkedDistribution() {
        var snapshot = await database.collection('distributions').where('fuelRequestId', '==', requestId).get();
        if (!snapshot.empty) {
            linkedDistribution = snapshot.docs[0].data();
            linkedDistribution.id = linkedDistribution.id || snapshot.docs[0].id;
        }
    }

    // After the payment is approved, the next step for an open distribution is Supplied
    function showDistributionNext() {
        if (linkedDistribution && distributionNextStatus(linkedDistribution.status) === 'supplied') {
            $('#receive_distribution_link').attr('href', '{{ route("distributions.view", ":id") }}'.replace(':id', encodeURIComponent(linkedDistribution.id)));
            $('#receive_distribution_next').removeClass('d-none');
        }
    }

    function toInputDateTime(date) {
        var pad = function (number) { return String(number).padStart(2, '0'); };
        return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) + 'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());
    }

    function showError(message) {
        $('.error_top').removeClass('d-none').html('<p class="mb-0">' + escapeHtml(message) + '</p>');
        window.scrollTo(0, 0);
    }
</script>
@endsection
