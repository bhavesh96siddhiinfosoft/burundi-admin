@extends('layouts.app')

@section('content')
<style>
    .flow-timeline {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        overflow-x: auto;
    }
    .flow-timeline li {
        flex: 1 1 0;
        min-width: 90px;
        position: relative;
        text-align: center;
        font-size: 12px;
        color: #6c757d;
        padding-top: 34px;
    }
    .flow-timeline li::before {
        content: '';
        position: absolute;
        top: 13px;
        left: -50%;
        width: 100%;
        height: 3px;
        background: #dee2e6;
        z-index: 0;
    }
    .flow-timeline li:first-child::before {
        display: none;
    }
    .flow-timeline .flow-step-dot {
        position: absolute;
        top: 0;
        left: 50%;
        margin-left: -14px;
        width: 28px;
        height: 28px;
        line-height: 24px;
        border-radius: 50%;
        border: 2px solid #ced4da;
        background: #fff;
        z-index: 1;
        font-size: 13px;
    }
    .flow-timeline li.done { color: #212529; }
    .flow-timeline li.done::before { background: #28a745; }
    .flow-timeline li.done .flow-step-dot { background: #28a745; border-color: #28a745; color: #fff; }
    .flow-timeline li.current { color: #007bff; font-weight: bold; }
    .flow-timeline li.current .flow-step-dot { border-color: #007bff; border-width: 4px; }
    .flow-timeline li.failed { color: #dc3545; font-weight: bold; }
    .flow-timeline li.failed .flow-step-dot { background: #dc3545; border-color: #dc3545; color: #fff; }
    .flow-timeline li.skipped .flow-step-dot { border-style: dashed; }

    #imageModal .modal-body {
        max-height: 80vh;
        overflow: auto;
    }
    #imageModal img {
        display: block;
        margin: auto;
        transform-origin: center center;
    }
</style>
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.dfr_details') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('distribution.fuel.request') }}">{{ trans('lang.distribution_fuel_request') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.dfr_details') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="alert alert-danger error_top d-none" role="alert"></div>

        <!-- Flow header, timeline and the next step (F7 3.1) -->
        <div class="card shadow-sm mb-4 d-none" id="flow_header_card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="mb-1 text-dark" id="flow_station"></h4>
                        <div class="text-muted" id="flow_owner"></div>
                        <div class="mt-2" id="flow_facts"></div>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                        <div class="text-muted small">{{ trans('lang.dfr_col_stage') }}</div>
                        <h4 class="text-primary mb-0" id="flow_stage"></h4>
                        <div class="small text-muted" id="flow_stage_note"></div>
                    </div>
                </div>

                <ol class="flow-timeline my-4" id="flow_timeline"></ol>

                <div class="text-center" id="flow_actions"></div>
                <div class="text-center small text-muted mt-1" id="flow_action_reason"></div>
            </div>
        </div>

        <!-- Request and payment (F7 3.2) -->
        <div class="card shadow-sm mb-4 d-none" id="flow_request_card">
            <div class="card-header bg-light">
                <h5 class="mb-0 text-dark">{{ trans('lang.dfr_request_and_payment') }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0 d-none" id="flow_no_request"></p>

                <div id="flow_request_details">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.request_id') }}</span>
                                    <span class="text-muted" id="flow_request_id"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.status') }}</span>
                                    <span id="flow_request_status"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.dfr_col_requested') }}</span>
                                    <span class="text-muted" id="flow_request_quota"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.dfr_created') }}</span>
                                    <span class="text-muted text-right" id="flow_request_created"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.dfr_col_updated') }}</span>
                                    <span class="text-muted text-right" id="flow_request_updated"></span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-dark">{{ trans('lang.payment_proof') }}</h6>
                            <div id="flow_proof"></div>
                        </div>
                    </div>

                    <h6 class="text-dark mt-4">{{ trans('lang.fuel_stock_information') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ trans('lang.fuel_type') }}</th>
                                    <th>{{ trans('lang.total_capacity_ltr') }}</th>
                                    <th>{{ trans('lang.total_quota_ltr') }}</th>
                                    <th>{{ trans('lang.available_quota_ltr') }}</th>
                                    <th>{{ trans('lang.requested_amount_ltr') }}</th>
                                </tr>
                            </thead>
                            <tbody id="flow_stock_table"></tbody>
                        </table>
                    </div>

                    <h6 class="text-dark mt-4">{{ trans('lang.receive_fuel_received') }}</h6>
                    <div id="flow_receipts"></div>
                    <div class="mt-3" id="flow_receive_container" style="max-width: 640px;"></div>
                </div>
            </div>
        </div>

        <!-- Distribution and customers (F7 3.3) -->
        <div class="card shadow-sm mb-4 d-none" id="flow_distribution_card">
            <div class="card-header bg-light">
                <h5 class="mb-0 text-dark">{{ trans('lang.dfr_distribution_and_customers') }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0 d-none" id="flow_no_distribution">{{ trans('lang.dfr_no_distribution') }}</p>

                <div id="flow_distribution_details">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.status') }}</span>
                                    <span id="dist_status"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.distribution_status_updated_at') }}</span>
                                    <span class="text-muted text-right" id="dist_status_updated_at"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.dfr_status_updated_by') }}</span>
                                    <span class="text-muted text-right" id="dist_status_updated_by"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.distribution_started_at') }}</span>
                                    <span class="text-muted text-right" id="dist_started_at"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.distribution_completed_at') }}</span>
                                    <span class="text-muted text-right" id="dist_completed_at"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.distribution_published_at') }}</span>
                                    <span class="text-muted text-right" id="dist_published_at"></span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.distribution_registered_spots') }}</span>
                                    <span class="text-muted text-right" id="dist_spots"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.distribution_served') }}</span>
                                    <span class="text-muted text-right" id="dist_served"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.distribution_cancelled_count') }}</span>
                                    <span class="text-muted text-right" id="dist_cancelled"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.distribution_quota_per_visit') }}</span>
                                    <span class="text-muted text-right" id="dist_quota_per_visit"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.distribution_capacity') }}</span>
                                    <span class="text-muted text-right" id="dist_capacity"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.distribution_rate_per_hour') }}</span>
                                    <span class="text-muted text-right" id="dist_rate"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ trans('lang.vehicle_type') }}</span>
                                    <span class="text-muted text-right" id="dist_vehicle_type"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-text text-muted mt-2">{{ trans('lang.dfr_capacity_read_only') }}</div>

                    <h6 class="text-dark mt-4">{{ trans('lang.distribution_registered_customers') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
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
        </div>

        <div class="form-group col-12 text-center btm-btn">
            <a href="{{ route('distribution.fuel.request') }}" class="btn btn-default"><i class="fa fa-undo"></i> {{ trans('lang.back') }}</a>
        </div>
    </div>
</div>

<!-- Payment proof, full size -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('lang.view_image') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" style="max-height: 80vh; transform: scale(1); transition: 0.2s;" alt="">
            </div>
            <div class="modal-footer">
                <button type="button" id="zoomIn" class="btn btn-primary"><i class="fa fa-search-plus"></i> {{ trans('lang.zoom_in') }}</button>
                <button type="button" id="zoomOut" class="btn btn-secondary"><i class="fa fa-search-minus"></i> {{ trans('lang.zoom_out') }}</button>
                <button type="button" id="resetZoom" class="btn btn-info"><i class="fa fa-undo"></i> {{ trans('lang.reset_zoom') }}</button>
                <button type="button" class="btn btn-outline-dark" data-dismiss="modal">{{ trans('lang.close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('distributions.status_functions')
@include('fuel_request.receipt_functions')
@include('distribution_fuel_request.flow_functions')
@include('distribution_fuel_request.action_functions')
<script>
    var database = firebase.firestore();
    var pageId = "{{ $id }}";
    var ADMIN_NAMES = @json($adminNames);
    var flow = { key: pageId, request: null, distribution: null, receipt: null, requestMissing: false };
    var receipts = [];
    var station = null;
    var cityNames = {};
    var pageMode = null;
    var watchedOwnerId = null;
    var ownerUnsubscribe = null;
    var watchedDistributionId = null;
    var registrationsUnsubscribe = null;
    var receiveFormKey = null;
    var scrollToFormPending = "{{ $step }}" === 'receive';
    var renderQueued = false;
    var badgeStageKey = null;
    var zoom = 1;

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

    // The page follows the flow live, so after an action the listeners redraw it
    var DETAIL_HANDLERS = {
        updated: function () {},
        verify: verifyPaymentHere,
        receive: function () {
            scrollToFormPending = true;
            scrollToReceiveForm();
        }
    };

    $(document).ready(async function () {
        $(document).on('click', '#flow_actions .flow-action', function () {
            runFlowAction(flow, $(this).attr('data-action'), DETAIL_HANDLERS);
        });
        bindImageModal();

        jQuery("#overlay").show();
        try {
            var requestSnapshot = await database.collection('fuel_request').doc(pageId).get();
            if (requestSnapshot.exists) {
                watchRequest(pageId);
                return;
            }
            // A distribution id (old Distributions URLs): its request's page, or a distribution without a request
            var distributionSnapshot = await database.collection('distributions').doc(pageId).get();
            if (!distributionSnapshot.exists) {
                jQuery("#overlay").hide();
                showError("{{ trans('lang.dfr_not_found') }}");
                return;
            }
            var fuelRequestId = distributionSnapshot.data().fuelRequestId;
            if (fuelRequestId) {
                var linkedRequest = await database.collection('fuel_request').doc(fuelRequestId).get();
                if (linkedRequest.exists) {
                    window.location.replace(flowUrl(fuelRequestId) + window.location.search);
                    return;
                }
            }
            watchDistribution(pageId);
        } catch (error) {
            console.error(error);
            jQuery("#overlay").hide();
            showError(error.message);
        }
    });

    function watchRequest(requestId) {
        pageMode = 'request';
        var loaded = {};
        var partLoaded = function (part) {
            loaded[part] = true;
            if (loaded.request && loaded.distributions && loaded.receipts) {
                jQuery("#overlay").hide();
            }
            scheduleRender();
        };

        database.collection('fuel_request').doc(requestId).onSnapshot(function (snapshot) {
            flow.request = null;
            if (snapshot.exists) {
                flow.request = snapshot.data();
                flow.request.id = snapshot.id;
                watchOwner(flow.request.ownerId);
            }
            partLoaded('request');
        }, onListenError);

        database.collection('distributions').where('fuelRequestId', '==', requestId).onSnapshot(function (snapshot) {
            flow.distribution = flowDocs(snapshot).sort(flowNewestDistributionFirst)[0] || null;
            watchRegistrations(flow.distribution ? flow.distribution.id : null);
            partLoaded('distributions');
        }, onListenError);

        database.collection('fuel_receipts').where('fuelRequestId', '==', requestId).onSnapshot(function (snapshot) {
            receipts = flowDocs(snapshot);
            // The request's receipt is request_<id>, or one written before that naming
            flow.receipt = receipts.find(function (receipt) {
                return receipt.id === fuelRequestReceiptId(requestId);
            }) || receipts[0] || null;
            partLoaded('receipts');
        }, onListenError);
    }

    function watchDistribution(distributionId) {
        pageMode = 'distribution';
        database.collection('distributions').doc(distributionId).onSnapshot(function (snapshot) {
            flow.distribution = null;
            if (snapshot.exists) {
                flow.distribution = snapshot.data();
                flow.distribution.id = snapshot.id;
                // Its request was looked up when the page opened and does not exist
                flow.requestMissing = !!flow.distribution.fuelRequestId;
                watchOwner(flow.distribution.ownerId);
            }
            watchRegistrations(snapshot.exists ? distributionId : null);
            jQuery("#overlay").hide();
            scheduleRender();
        }, onListenError);
    }

    function watchOwner(ownerId) {
        if (!ownerId || ownerId === watchedOwnerId) {
            return;
        }
        watchedOwnerId = ownerId;
        if (ownerUnsubscribe) {
            ownerUnsubscribe();
        }
        ownerUnsubscribe = database.collection('users').doc(ownerId).onSnapshot(function (snapshot) {
            station = snapshot.exists ? snapshot.data() : null;
            loadCityName(station && station.cityId);
            scheduleRender();
        }, onListenError);
    }

    function watchRegistrations(distributionId) {
        if (distributionId === watchedDistributionId) {
            return;
        }
        watchedDistributionId = distributionId;
        if (registrationsUnsubscribe) {
            registrationsUnsubscribe();
            registrationsUnsubscribe = null;
        }
        if (!distributionId) {
            renderRegistrations([]);
            return;
        }
        registrationsUnsubscribe = database.collection('booking_registrations').where('distributionId', '==', distributionId)
            .onSnapshot(function (snapshot) {
                renderRegistrations(snapshot.docs.map(function (doc) { return doc.data(); }));
            }, onListenError);
    }

    async function loadCityName(cityId) {
        if (!cityId || cityNames[cityId] !== undefined) {
            return;
        }
        cityNames[cityId] = '';
        try {
            var snapshot = await database.collection('cities').doc(cityId).get();
            cityNames[cityId] = snapshot.exists ? (snapshot.data().name || '') : '';
            scheduleRender();
        } catch (error) {
            console.error(error);
        }
    }

    function onListenError(error) {
        console.error(error);
        jQuery("#overlay").hide();
        showError(error.message);
    }

    function scheduleRender() {
        if (renderQueued) {
            return;
        }
        renderQueued = true;
        setTimeout(renderFlowPage, 0);
    }

    function renderFlowPage() {
        renderQueued = false;
        if ((pageMode === 'request' && !flow.request) || (pageMode === 'distribution' && !flow.distribution)) {
            $('#flow_header_card, #flow_request_card, #flow_distribution_card').addClass('d-none');
            showError("{{ trans('lang.dfr_not_found') }}");
            return;
        }
        $('.error_top').addClass('d-none');
        var stage = flowStage(flow);
        renderHeader(stage);
        renderTimeline();
        $('#flow_actions').html(flowActionButtons(flow, stage, true));
        $('#flow_action_reason').text(stage.disabledReason || '');
        renderRequestCard();
        renderDistributionCard();
        $('#flow_header_card, #flow_request_card, #flow_distribution_card').removeClass('d-none');
        scrollToReceiveForm();

        // The menu badge is counted again when this flow reaches another stage
        if (stage.key !== badgeStageKey) {
            badgeStageKey = stage.key;
            loadNeedsActionFlows().then(function (flows) {
                updateFlowMenuBadge(flows.length);
            }).catch(function (error) {
                console.error(error);
            });
        }
    }

    function stationFuelEntry() {
        var fuelTypeId = (flow.request && flow.request.fuelTypeId) || (flow.distribution && flow.distribution.fuelTypeId);
        return station ? (station.fuel || []).find(function (fuel) { return fuel.fuelTypeId === fuelTypeId; }) : null;
    }

    function renderHeader(stage) {
        var distribution = flow.distribution;
        var fuel = stationFuelEntry();
        var cityName = (distribution && distribution.cityName) || (station && cityNames[station.cityId]) || '';
        $('#flow_station').text((station && station.fuelStationName) || (distribution && distribution.stationName) || '');
        $('#flow_owner').text([station && station.fullName, cityName].filter(function (value) { return value; }).join(' · '));

        var facts = [
            '<span class="mr-4"><span class="text-muted">{{ trans("lang.dfr_col_fuel") }}:</span> <strong>' +
                escapeHtml((fuel && fuel.name) || (distribution && distribution.fuelTypeName) || '') + '</strong></span>'
        ];
        if (flow.request) {
            facts.push('<span class="mr-4"><span class="text-muted">{{ trans("lang.dfr_col_requested") }}:</span> <strong>' + flowLitres(flow.request.fuelQuota || 0) + '</strong></span>');
            facts.push('<span class="mr-4"><span class="text-muted">{{ trans("lang.dfr_col_received") }}:</span> <strong>' +
                (flow.receipt ? flowLitres(flow.receipt.litres) : '{{ trans("lang.dfr_not_yet") }}') + '</strong></span>');
        }
        $('#flow_facts').html(facts.join(''));
        $('#flow_stage').text(stage.label);
        $('#flow_stage_note').text(stage.note || '');
    }

    // Requested → Approved → Payment uploaded → Completed, fuel added → Being supplied → Supplied → Ready → In progress → Completed
    function flowTimelineSteps() {
        var request = flow.request;
        var distribution = flow.distribution;
        var requestStatus = request ? request.status : null;
        var distributionStatus = flowDistributionStatus(flow);
        var steps = [];
        var add = function (label, done) {
            steps.push({ label: label, state: done ? 'done' : 'todo' });
        };
        var endWith = function (label) {
            var lastDone = -1;
            steps.forEach(function (step, index) {
                if (step.state === 'done' || step.state === 'skipped') {
                    lastDone = index;
                }
            });
            steps = steps.slice(0, lastDone + 1);
            steps.push({ label: label, state: 'failed' });
        };

        var requestLabels = [
            "{{ trans('lang.dfr_step_requested') }}",
            "{{ trans('lang.dfr_step_approved') }}",
            "{{ trans('lang.dfr_step_payment') }}",
            "{{ trans('lang.dfr_step_completed_fuel') }}"
        ];
        if (request) {
            var requestIndex = ['requestedByOwner', 'approvedByAdmin', 'paymentByOwner', 'completedByAdmin'].indexOf(requestStatus);
            if (requestStatus === 'rejectedByAdmin') {
                // A payment proof means the request had been approved before it was rejected
                requestIndex = flowProofDetails(request) ? 2 : 0;
            }
            add(requestLabels[0], requestIndex >= 0);
            add(requestLabels[1], requestIndex >= 1);
            add(requestLabels[2], requestIndex >= 2);
            add(requestLabels[3], requestIndex >= 3 && !!flow.receipt);
            if (requestStatus === 'rejectedByAdmin') {
                endWith("{{ trans('lang.dfr_step_rejected') }}");
                return steps;
            }
        } else {
            requestLabels.forEach(function (label) {
                steps.push({ label: label, state: 'skipped' });
            });
        }

        if (distribution) {
            var distributionIndex = ['scheduled', 'being_supplied', 'supplied', 'ready', 'in_progress', 'completed'].indexOf(distributionStatus);
            if (distributionStatus === 'cancelled') {
                distributionIndex = distribution.distributionStartedAt ? 4 : 0;
            }
            add("{{ trans('lang.dfr_step_being_supplied') }}", distributionIndex >= 1);
            add("{{ trans('lang.dfr_step_supplied') }}", distributionIndex >= 2);
            add("{{ trans('lang.dfr_step_ready') }}", distributionIndex >= 3);
            add("{{ trans('lang.dfr_step_in_progress') }}", distributionIndex >= 4);
            add(distribution.completedReason === 'stock_empty' ? "{{ trans('lang.dfr_step_completed_stock_empty') }}" : "{{ trans('lang.dfr_step_completed') }}",
                distributionIndex >= 5);
            if (distributionStatus === 'cancelled') {
                endWith("{{ trans('lang.dfr_step_cancelled') }}");
                return steps;
            }
        }

        var current = steps.findIndex(function (step) { return step.state === 'todo'; });
        if (current !== -1) {
            steps[current].state = 'current';
        }
        return steps;
    }

    function renderTimeline() {
        var icons = { done: '<i class="fa fa-check"></i>', failed: '<i class="fa fa-times"></i>', skipped: '&ndash;', current: '', todo: '' };
        $('#flow_timeline').html(flowTimelineSteps().map(function (step) {
            return '<li class="' + step.state + '"><span class="flow-step-dot">' + icons[step.state] + '</span>' + escapeHtml(step.label) + '</li>';
        }).join(''));
    }

    function renderRequestCard() {
        var request = flow.request;
        if (!request) {
            $('#flow_request_details').addClass('d-none');
            $('#flow_no_request').removeClass('d-none').text(flow.requestMissing
                ? "{{ trans('lang.dfr_request_missing_text') }}".replace(':id', '#' + flow.distribution.fuelRequestId)
                : "{{ trans('lang.dfr_no_request') }}");
            return;
        }
        $('#flow_no_request').addClass('d-none');
        $('#flow_request_details').removeClass('d-none');

        $('#flow_request_id').text('#' + request.id);
        $('#flow_request_status').html(flowRequestBadge(request.status));
        $('#flow_request_quota').html(flowLitres(request.fuelQuota || 0));
        $('#flow_request_created').text(formatFirestoreDate(request.createdAt) || '-');
        $('#flow_request_updated').text(formatFirestoreDate(request.updatedAt) || '-');

        var proof = flowProofDetails(request);
        $('#flow_proof').html(proof
            ? '<div class="text-center">' +
                '<img src="' + escapeHtml(proof.path) + '" width="150" class="img-thumbnail mb-2" alt=""><br>' +
                '<button type="button" class="btn btn-dark btn-sm viewImageBtn" data-img="' + escapeHtml(proof.path) + '"><i class="fa fa-eye"></i> {{ trans("lang.view_image") }}</button>' +
                '<p class="mt-2 mb-0 text-muted">' + escapeHtml(proof.text) + '</p>' +
              '</div>'
            : '<p class="text-muted">{{ trans("lang.no_proof_uploaded") }}</p>');

        renderStockTable(request);
        renderReceipts(request);
    }

    function renderStockTable(request) {
        var fuels = station ? (station.fuel || []) : [];
        if (!fuels.length) {
            $('#flow_stock_table').html('<tr><td colspan="5" class="text-center">{{ trans("lang.no_fuel_data") }}</td></tr>');
            return;
        }
        $('#flow_stock_table').html(fuels.map(function (fuel) {
            var requested = fuel.fuelTypeId === request.fuelTypeId;
            return '<tr' + (requested ? ' class="table-info"' : '') + '>' +
                '<td>' + escapeHtml(fuel.name || '') + '</td>' +
                '<td>' + flowLitres(fuel.fuelCapacity || 0) + '</td>' +
                '<td>' + flowLitres(fuel.totalFuelQuota || 0) + '</td>' +
                '<td>' + flowLitres(fuel.availableFuelQuota || 0) + '</td>' +
                '<td>' + (requested ? flowLitres(request.fuelQuota || 0) : '-') + '</td>' +
                '</tr>';
        }).join(''));
    }

    // F5: the receipt(s) of this request, or the Add received fuel form when there is none yet
    function renderReceipts(request) {
        if (receipts.length) {
            var historyUrl = '{{ route("owners.fuelReceipts", ":id") }}'.replace(':id', encodeURIComponent(request.ownerId));
            $('#flow_receipts').html(
                '<div class="table-responsive"><table class="table table-bordered mb-1"><thead class="thead-light"><tr>' +
                    '<th>{{ trans("lang.fuel_receipt_litres") }}</th>' +
                    '<th>{{ trans("lang.fuel_receipt_received_at") }}</th>' +
                    '<th>{{ trans("lang.note") }}</th>' +
                    '<th>{{ trans("lang.dfr_receipt_added_by") }}</th>' +
                '</tr></thead><tbody>' +
                receipts.map(function (receipt) {
                    return '<tr>' +
                        '<td>' + flowLitres(receipt.litres) + '</td>' +
                        '<td>' + escapeHtml(formatFirestoreDate(receipt.receivedAt)) + '</td>' +
                        '<td>' + escapeHtml(receipt.note || '') + '</td>' +
                        '<td>' + escapeHtml(ADMIN_NAMES[receipt.createdBy] || receipt.createdBy || '') + '</td>' +
                        '</tr>';
                }).join('') +
                '</tbody></table></div>' +
                '<a href="' + historyUrl + '" class="small"><i class="fa fa-history"></i> {{ trans("lang.dfr_receipt_correct_link") }}</a>'
            );
        } else if (request.status === 'completedByAdmin') {
            $('#flow_receipts').html('<p class="text-muted mb-0">{{ trans("lang.dfr_received_none") }}</p>');
        } else {
            $('#flow_receipts').html('<p class="text-muted mb-0">{{ trans("lang.dfr_received_after_payment") }}</p>');
        }

        var showForm = request.status === 'completedByAdmin' && !receipts.length && !!station;
        if (!showForm) {
            if (receiveFormKey) {
                $('#flow_receive_container').empty();
                receiveFormKey = null;
            }
            return;
        }
        // Built once, so a live update never wipes what the admin is typing
        if (receiveFormKey === request.id) {
            return;
        }
        receiveFormKey = request.id;
        renderReceiveForm($('#flow_receive_container'), flow, station, {
            intro: true,
            onAdded: async function (stock, litres) {
                var message = flowReceiveDoneText(stock, litres, station);
                var next = flowStage(await reloadFlow(flow));
                if (next.action) {
                    message += ' ' + "{{ trans('lang.dfr_next_step') }}".replace(':stage', next.label);
                }
                showFlowMessage('success', message);
            }
        });
    }

    function scrollToReceiveForm() {
        var container = $('#flow_receive_container');
        if (!scrollToFormPending || container.is(':empty')) {
            return;
        }
        scrollToFormPending = false;
        $('html, body').animate({ scrollTop: container.offset().top - 120 }, 300);
        container.find('.receive-litres').trigger('focus');
    }

    // Verify payment here: the proof is on this page; confirming goes on to Add received fuel below
    async function verifyPaymentHere(fresh) {
        if (!(await confirmFlowPayment(fresh))) {
            return;
        }
        showFlowMessage('success', "{{ trans('lang.dfr_payment_confirmed') }}".replace(':id', '#' + fresh.request.id));
        scrollToFormPending = true;
        scheduleRender();
    }

    // Who made the last status change: an admin, or the employee app when the stock ran out (B2)
    function statusUpdatedByText(distribution) {
        if (distribution.completedReason === 'stock_empty') {
            return "{{ trans('lang.dfr_by_employee_stock_empty') }}";
        }
        var userId = distribution.statusUpdatedBy;
        if (!userId) {
            return '-';
        }
        if (ADMIN_NAMES[userId] !== undefined) {
            return "{{ trans('lang.dfr_by_admin') }}".replace(':name', ADMIN_NAMES[userId]);
        }
        return "{{ trans('lang.dfr_by_app') }}".replace(':id', userId);
    }

    function renderDistributionCard() {
        var distribution = flow.distribution;
        if (!distribution) {
            $('#flow_distribution_details').addClass('d-none');
            $('#flow_no_distribution').removeClass('d-none');
            return;
        }
        $('#flow_no_distribution').addClass('d-none');
        $('#flow_distribution_details').removeClass('d-none');

        $('#dist_status').html(distributionStatusBadge(distribution.status));
        $('#dist_status_updated_at').text(formatFirestoreDate(distribution.statusUpdatedAt) || '-');
        $('#dist_status_updated_by').text(statusUpdatedByText(distribution));
        $('#dist_started_at').text(formatFirestoreDate(distribution.distributionStartedAt) || '-');
        $('#dist_completed_at').text(formatFirestoreDate(distribution.completedAt) || '-');
        $('#dist_published_at').text(formatFirestoreDate(distribution.publishedAt) || '-');
        $('#dist_spots').text((distribution.registeredCount || 0) + ' / ' + (distribution.totalSpots || 0));
        $('#dist_served').text(distribution.servedCount || 0);
        $('#dist_cancelled').text(distribution.cancelledCount || 0);
        $('#dist_quota_per_visit').text((distribution.quotaPerVisit || 0) + ' {{ trans("lang.liter") }}');
        $('#dist_capacity').text((distribution.capacityLitres || 0) + ' {{ trans("lang.liter") }}');
        $('#dist_rate').text(distribution.avgRatePerHour ? Math.round(distribution.avgRatePerHour * 10) / 10 : '-');
        $('#dist_vehicle_type').text(distribution.vehicleTypeName || "{{ trans('lang.distribution_all_vehicles') }}");
    }

    function renderRegistrations(registrations) {
        registrations.sort(function (a, b) {
            return (parseInt(a.position, 10) || 0) - (parseInt(b.position, 10) || 0);
        });
        var html = registrations.map(function (registration) {
            var badge = REGISTRATION_STATUS_BADGES[registration.status] || 'secondary';
            var label = REGISTRATION_STATUS_LABELS[registration.status] || registration.status || '';
            return '<tr>' +
                '<td>#' + escapeHtml(registration.position || '') + '</td>' +
                '<td>' + escapeHtml(registration.customerName || '') + '</td>' +
                '<td>' + escapeHtml((registration.vehicleNumber || '').toUpperCase()) + '</td>' +
                '<td>' + escapeHtml(registration.vehicleTypeName || '') + '</td>' +
                '<td><span class="badge badge-' + badge + '">' + escapeHtml(label) + '</span></td>' +
                '<td>' + escapeHtml(formatFirestoreDate(registration.registeredAt)) + '</td>' +
                '<td>' + escapeHtml(formatFirestoreDate(registration.servedAt)) + '</td>' +
                '<td>' + (registration.litresServed ? flowLitres(registration.litresServed) : '') + '</td>' +
                '</tr>';
        }).join('');
        if (html === '') {
            html = '<tr><td colspan="8" class="text-center">{{ trans("lang.distribution_no_registrations") }}</td></tr>';
        }
        $('#registration_list').html(html);
    }

    function bindImageModal() {
        $(document).on('click', '.viewImageBtn', function () {
            zoom = 1;
            $("#modalImage").attr("src", $(this).data('img')).css("transform", "scale(1)");
            $("#imageModal").modal("show");
        });
        $("#zoomIn").on('click', function () {
            zoom += 0.2;
            $("#modalImage").css("transform", "scale(" + zoom + ")");
        });
        $("#zoomOut").on('click', function () {
            zoom = Math.max(0.2, zoom - 0.2);
            $("#modalImage").css("transform", "scale(" + zoom + ")");
        });
        $("#resetZoom").on('click', function () {
            zoom = 1;
            $("#modalImage").css("transform", "scale(1)");
        });
        $("#imageModal").on("hidden.bs.modal", function () {
            zoom = 1;
            $("#modalImage").css("transform", "scale(1)");
        });
    }

    function showError(message) {
        $('.error_top').removeClass('d-none').html('<p class="mb-0">' + escapeHtml(message) + '</p>');
        window.scrollTo(0, 0);
    }
</script>
@endsection
