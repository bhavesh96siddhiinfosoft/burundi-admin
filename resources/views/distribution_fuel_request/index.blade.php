@extends('layouts.app')

@section('content')
<style>
    /* Verify payment and Add received fuel open on the right, with the list still visible */
    #flowSidePanel .modal-dialog {
        position: fixed;
        top: 0;
        right: 0;
        margin: 0;
        width: 460px;
        max-width: 100%;
        height: 100%;
    }
    #flowSidePanel.fade .modal-dialog {
        transform: translateX(100%);
        transition: transform .2s ease-out;
    }
    #flowSidePanel.show .modal-dialog {
        transform: translateX(0);
    }
    #flowSidePanel .modal-content {
        height: 100%;
        border-radius: 0;
        overflow-y: auto;
    }
    .modal-backdrop.show {
        opacity: .25;
    }
    #flow_table td {
        vertical-align: middle;
    }
    #flow_table td:last-child {
        min-width: 210px;
    }
</style>
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.distribution_fuel_request') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.distribution_fuel_request') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card pb-4">
            <div class="card-body">
                <div class="alert alert-danger error_top d-none" role="alert"></div>

                <div class="row mb-2">
                    <div class="col-md-3 mb-2">
                        <select id="flow_filter_station" class="form-control">
                            <option value="">{{ trans('lang.all_stations') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select id="flow_filter_city" class="form-control">
                            <option value="">{{ trans('lang.all_cities') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select id="flow_filter_fuel" class="form-control">
                            <option value="">{{ trans('lang.all_fuel_types') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select id="flow_filter_stage" class="form-control">
                            <option value="">{{ trans('lang.dfr_all_stages') }}</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <div class="col-md-3 mb-2">
                        <input type="date" id="flow_filter_from" class="form-control" title="{{ trans('lang.dfr_from_date') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="date" id="flow_filter_to" class="form-control" title="{{ trans('lang.dfr_to_date') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="text" id="flow_filter_search" class="form-control" placeholder="{{ trans('lang.dfr_search_placeholder') }}">
                    </div>
                    <div class="col-md-3 mb-2 d-flex align-items-center justify-content-between">
                        <div class="form-check mb-0">
                            <input type="checkbox" id="flow_needs_action" checked>
                            <label class="control-label mb-0" for="flow_needs_action">{{ trans('lang.dfr_needs_action_only') }}</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="flow_refresh">
                            <i class="fa fa-refresh"></i> {{ trans('lang.dfr_refresh') }}
                        </button>
                    </div>
                </div>
                <div class="form-text text-muted mb-3">{{ trans('lang.dfr_help') }}</div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="flow_table">
                        <thead>
                            <tr>
                                <th>{{ trans('lang.dfr_col_station') }}</th>
                                <th>{{ trans('lang.dfr_col_fuel') }}</th>
                                <th>{{ trans('lang.dfr_col_requested') }}</th>
                                <th>{{ trans('lang.dfr_col_received') }}</th>
                                <th>{{ trans('lang.dfr_col_request') }}</th>
                                <th>{{ trans('lang.dfr_col_distribution') }}</th>
                                <th>{{ trans('lang.dfr_col_booked') }}</th>
                                <th>{{ trans('lang.dfr_col_stage') }}</th>
                                <th>{{ trans('lang.dfr_col_updated') }}</th>
                                <th>{{ trans('lang.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="flow_list"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Side panel: Verify payment, then Add received fuel -->
<div class="modal fade" id="flowSidePanel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="flow_panel_title"></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger flow_panel_error d-none" role="alert"></div>
                <div id="flow_panel_body"></div>
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
    var flows = [];
    var flowMap = {};
    var owners = {};
    var cities = {};
    var fuelTypes = {};
    var flowLoadSequence = 0;

    var LIST_HANDLERS = {
        updated: replaceFlow,
        verify: openVerifyPanel,
        receive: openReceivePanel
    };

    $(document).ready(async function () {
        $('#flow_filter_station, #flow_filter_city, #flow_filter_fuel, #flow_filter_from, #flow_filter_to').on('change', renderFlows);
        $('#flow_filter_search').on('keyup change', renderFlows);
        $('#flow_filter_stage').on('change', function () {
            // A stage that never needs action is shown with every flow
            var stageKey = $(this).val();
            if (stageKey && FLOW_NEEDS_ACTION_STAGES.indexOf(stageKey) === -1 && $('#flow_needs_action').is(':checked')) {
                $('#flow_needs_action').prop('checked', false);
                loadFlows();
                return;
            }
            renderFlows();
        });
        $('#flow_needs_action').on('change', loadFlows);
        $('#flow_refresh').on('click', loadFlows);

        $(document).on('click', '#flow_list .flow-action', function () {
            var flow = flowMap[$(this).attr('data-key')];
            if (flow) {
                runFlowAction(flow, $(this).attr('data-action'), LIST_HANDLERS);
            }
        });
        $(document).on('click', '.flow-delete', function () {
            deleteFuelRequest($(this).attr('data-key'));
        });

        jQuery("#overlay").show();
        try {
            await loadFilterOptions();
        } catch (error) {
            console.error(error);
        }
        loadFlows();
    });

    async function loadFilterOptions() {
        var stageHtml = '<option value="">{{ trans("lang.dfr_all_stages") }}</option>';
        FLOW_STAGE_ORDER.forEach(function (key) {
            stageHtml += '<option value="' + key + '">' + escapeHtml(FLOW_STAGE_LABELS[key]) + '</option>';
        });
        $('#flow_filter_stage').html(stageHtml);

        var results = await Promise.all([
            database.collection('users').where('role', '==', 'owner').get(),
            database.collection('cities').get(),
            database.collection('fuel_type').get()
        ]);

        results[0].forEach(function (doc) {
            owners[doc.id] = doc.data();
        });
        var ownerIds = Object.keys(owners).sort(function (a, b) {
            return (owners[a].fuelStationName || '').localeCompare(owners[b].fuelStationName || '');
        });
        var stationHtml = '<option value="">{{ trans("lang.all_stations") }}</option>';
        ownerIds.forEach(function (id) {
            stationHtml += '<option value="' + escapeHtml(id) + '">' + escapeHtml(owners[id].fuelStationName || owners[id].fullName || id) + '</option>';
        });
        $('#flow_filter_station').html(stationHtml);

        results[1].forEach(function (doc) {
            cities[doc.id] = doc.data();
        });
        var cityIds = Object.keys(cities).filter(function (id) {
            return cities[id].isDeleted !== true;
        }).sort(function (a, b) {
            return (cities[a].name || '').localeCompare(cities[b].name || '');
        });
        var cityHtml = '<option value="">{{ trans("lang.all_cities") }}</option>';
        cityIds.forEach(function (id) {
            cityHtml += '<option value="' + escapeHtml(id) + '">' + escapeHtml(cities[id].name || id) + '</option>';
        });
        $('#flow_filter_city').html(cityHtml);

        var fuelHtml = '<option value="">{{ trans("lang.all_fuel_types") }}</option>';
        results[2].forEach(function (doc) {
            fuelTypes[doc.id] = doc.data().name || '';
            fuelHtml += '<option value="' + escapeHtml(doc.id) + '">' + escapeHtml(doc.data().name || '') + '</option>';
        });
        $('#flow_filter_fuel').html(fuelHtml);
    }

    async function loadFlows() {
        var sequence = ++flowLoadSequence;
        jQuery("#overlay").show();
        $('.error_top').addClass('d-none');
        try {
            var needsActionOnly = $('#flow_needs_action').is(':checked');
            var loaded = needsActionOnly ? await loadNeedsActionFlows() : await loadAllFlows();
            await loadMissingOwners(loaded);
            // A newer load is already running
            if (sequence !== flowLoadSequence) {
                return;
            }
            flows = loaded;
            flowMap = {};
            flows.forEach(function (flow) {
                flowMap[flow.key] = flow;
            });
            refreshFlowBadge();
            renderFlows();
        } catch (error) {
            console.error(error);
            $('.error_top').removeClass('d-none').html('<p class="mb-0">' + escapeHtml(error.message) + '</p>');
        } finally {
            if (sequence === flowLoadSequence) {
                jQuery("#overlay").hide();
            }
        }
    }

    async function loadMissingOwners(list) {
        var missing = [];
        list.forEach(function (flow) {
            var ownerId = flowOwnerId(flow);
            if (ownerId && !owners[ownerId] && missing.indexOf(ownerId) === -1) {
                missing.push(ownerId);
            }
        });
        var snapshots = await Promise.all(missing.map(function (ownerId) {
            return database.collection('users').doc(ownerId).get();
        }));
        snapshots.forEach(function (snapshot) {
            if (snapshot.exists) {
                owners[snapshot.id] = snapshot.data();
            }
        });
    }

    function refreshFlowBadge() {
        updateFlowMenuBadge(flows.filter(function (flow) {
            return flowStage(flow).needsAction;
        }).length);
    }

    function flowOwnerId(flow) {
        return (flow.request && flow.request.ownerId) || (flow.distribution && flow.distribution.ownerId) || '';
    }

    function flowFuelTypeId(flow) {
        return (flow.request && flow.request.fuelTypeId) || (flow.distribution && flow.distribution.fuelTypeId) || '';
    }

    function flowCityId(flow) {
        var owner = owners[flowOwnerId(flow)];
        return (flow.distribution && flow.distribution.cityId) || (owner && owner.cityId) || '';
    }

    // Request created, or distribution published for a distribution without a request
    function flowCreatedMillis(flow) {
        return flow.request ? flowMillis(flow.request.createdAt) : flowMillis(flow.distribution && flow.distribution.publishedAt);
    }

    function flowUpdatedMillis(flow) {
        var updated = Math.max(
            flowMillis(flow.request && flow.request.updatedAt),
            flowMillis(flow.distribution && flow.distribution.statusUpdatedAt),
            flowMillis(flow.distribution && flow.distribution.updatedAt)
        );
        return updated || Math.max(flowMillis(flow.request && flow.request.createdAt), flowMillis(flow.distribution && flow.distribution.publishedAt));
    }

    function renderFlows() {
        var stationId = $('#flow_filter_station').val();
        var cityId = $('#flow_filter_city').val();
        var fuelTypeId = $('#flow_filter_fuel').val();
        var stageKey = $('#flow_filter_stage').val();
        var from = $('#flow_filter_from').val();
        var to = $('#flow_filter_to').val();
        var fromMillis = from ? new Date(from + 'T00:00:00').getTime() : null;
        var toMillis = to ? new Date(to + 'T23:59:59').getTime() : null;
        var search = $('#flow_filter_search').val().trim().toLowerCase();

        var rows = flows.map(function (flow) {
            return { flow: flow, stage: flowStage(flow), updated: flowUpdatedMillis(flow) };
        }).filter(function (row) {
            var flow = row.flow;
            if (stationId && flowOwnerId(flow) !== stationId) return false;
            if (cityId && flowCityId(flow) !== cityId) return false;
            if (fuelTypeId && flowFuelTypeId(flow) !== fuelTypeId) return false;
            if (stageKey && row.stage.key !== stageKey) return false;
            if (fromMillis || toMillis) {
                var created = flowCreatedMillis(flow);
                if (!created) return false;
                if (fromMillis && created < fromMillis) return false;
                if (toMillis && created > toMillis) return false;
            }
            if (search !== '') {
                var owner = owners[flowOwnerId(flow)] || {};
                var haystack = [
                    flow.request ? flow.request.id : '',
                    flow.distribution ? flow.distribution.id : '',
                    owner.fuelStationName || '',
                    owner.fullName || '',
                    flow.distribution ? flow.distribution.stationName || '' : ''
                ].join(' ').toLowerCase();
                if (haystack.indexOf(search) === -1) return false;
            }
            return true;
        });

        // Rows that need admin action first, then the most recently updated
        rows.sort(function (a, b) {
            if (a.stage.needsAction !== b.stage.needsAction) {
                return a.stage.needsAction ? -1 : 1;
            }
            return b.updated - a.updated;
        });

        var html = rows.map(function (row) {
            return flowRowHtml(row.flow);
        }).join('');
        if (html === '') {
            var empty = $('#flow_needs_action').is(':checked') ? "{{ trans('lang.dfr_no_flows_action') }}" : "{{ trans('lang.dfr_no_flows') }}";
            html = '<tr><td colspan="10" class="text-center">' + escapeHtml(empty) + '</td></tr>';
        }
        $('#flow_list').html(html);
    }

    function flowRowHtml(flow) {
        var stage = flowStage(flow);
        var request = flow.request;
        var distribution = flow.distribution;
        var owner = owners[flowOwnerId(flow)] || null;
        var city = cities[flowCityId(flow)];
        var stationName = (owner && owner.fuelStationName) || (distribution && distribution.stationName) || '-';
        var cityName = (distribution && distribution.cityName) || (city && city.name) || '';
        var fuelName = fuelTypes[flowFuelTypeId(flow)] || (distribution && distribution.fuelTypeName) || '';
        var dash = '<span class="text-muted">&ndash;</span>';
        var url = flowUrl(flow.key);

        var stationHtml = '<a href="' + url + '" class="font-weight-bold">' + escapeHtml(stationName) + '</a>' +
            (owner && owner.fullName ? '<br><small>' + escapeHtml(owner.fullName) + '</small>' : '') +
            (cityName ? '<br><small class="text-muted">' + escapeHtml(cityName) + '</small>' : '');

        var requestedHtml = request ? flowLitres(request.fuelQuota || 0) : dash;
        var receivedHtml = request ? (flow.receipt ? flowLitres(flow.receipt.litres) : '<span class="text-muted">{{ trans("lang.dfr_not_yet") }}</span>') : dash;
        var requestHtml = dash;
        if (request) {
            requestHtml = flowRequestBadge(request.status) + '<br><small class="text-muted">#' + escapeHtml(request.id) + '</small>';
        } else if (flow.requestMissing) {
            requestHtml = '<span class="badge badge-light">{{ trans("lang.distribution_request_missing") }}</span>';
        }
        var distributionHtml = distribution ? distributionStatusBadge(distribution.status) : dash;
        var bookedHtml = distribution ? escapeHtml("{{ trans('lang.dfr_booked_text') }}"
            .replace(':registered', parseInt(distribution.registeredCount, 10) || 0)
            .replace(':spots', parseInt(distribution.totalSpots, 10) || 0)
            .replace(':served', parseInt(distribution.servedCount, 10) || 0)) : dash;
        var stageHtml = '<span class="font-weight-bold' + (stage.needsAction ? ' text-primary' : '') + '">' + escapeHtml(stage.label) + '</span>' +
            (stage.note ? '<br><small class="text-muted">' + escapeHtml(stage.note) + '</small>' : '');
        var updated = flowUpdatedMillis(flow);
        var updatedHtml = updated ? escapeHtml(formatFirestoreDate(firebase.firestore.Timestamp.fromMillis(updated))) : '';

        var toolsHtml = '<div class="mt-1"><a href="' + url + '" title="{{ trans("lang.view") }}" class="mr-2"><i class="fa fa-eye"></i></a>' +
            (request ? '<a href="javascript:void(0)" class="flow-delete text-danger" data-key="' + escapeHtml(flow.key) + '" title="{{ trans("lang.delete") }}"><i class="fa fa-trash"></i></a>' : '') +
            '</div>';

        return '<tr id="flow_row_' + escapeHtml(flow.key) + '">' +
            '<td>' + stationHtml + '</td>' +
            '<td>' + escapeHtml(fuelName) + '</td>' +
            '<td>' + requestedHtml + '</td>' +
            '<td>' + receivedHtml + '</td>' +
            '<td>' + requestHtml + '</td>' +
            '<td>' + distributionHtml + '</td>' +
            '<td>' + bookedHtml + '</td>' +
            '<td>' + stageHtml + '</td>' +
            '<td>' + updatedHtml + '</td>' +
            '<td>' + flowActionButtons(flow, stage, false) + toolsHtml + '</td>' +
            '</tr>';
    }

    // After an action: the row changes in place and the badge is counted again
    function replaceFlow(fresh) {
        var row = $(document.getElementById('flow_row_' + fresh.key));
        var index = flows.findIndex(function (flow) { return flow.key === fresh.key; });
        if (fresh.gone) {
            if (index !== -1) {
                flows.splice(index, 1);
            }
            delete flowMap[fresh.key];
            row.remove();
        } else {
            if (index !== -1) {
                flows[index] = fresh;
            } else {
                flows.push(fresh);
            }
            flowMap[fresh.key] = fresh;
            if (row.length) {
                row.replaceWith(flowRowHtml(fresh));
            }
        }
        refreshFlowBadge();
    }

    function flowPanelSummary(flow) {
        var request = flow.request;
        var owner = owners[request.ownerId] || {};
        var line = function (label, value) {
            return '<li class="list-group-item d-flex justify-content-between px-0"><span class="font-weight-bold">' + label + '</span>' +
                '<span class="text-muted text-right">' + value + '</span></li>';
        };
        return '<ul class="list-group list-group-flush mb-3">' +
            line("{{ trans('lang.request_id') }}", '#' + escapeHtml(request.id)) +
            line("{{ trans('lang.station_name') }}", escapeHtml(owner.fuelStationName || '') + (owner.fullName ? '<br><small>' + escapeHtml(owner.fullName) + '</small>' : '')) +
            line("{{ trans('lang.dfr_col_fuel') }}", escapeHtml(fuelTypes[request.fuelTypeId] || '')) +
            line("{{ trans('lang.dfr_col_requested') }}", flowLitres(request.fuelQuota || 0)) +
            '</ul>';
    }

    function showPanelError(message) {
        $('.flow_panel_error').removeClass('d-none').text(message);
    }

    // Verify payment: the proof, then Confirm payment goes straight on to Add received fuel
    function openVerifyPanel(flow) {
        var proof = flowProofDetails(flow.request);
        $('.flow_panel_error').addClass('d-none');
        $('#flow_panel_title').text("{{ trans('lang.dfr_verify_payment') }}");
        $('#flow_panel_body').html(
            flowPanelSummary(flow) +
            '<h6 class="text-dark">{{ trans("lang.payment_proof") }}</h6>' +
            (proof
                ? '<a href="' + escapeHtml(proof.path) + '" target="_blank" rel="noopener"><img src="' + escapeHtml(proof.path) + '" class="img-fluid img-thumbnail mb-2" alt=""></a>' +
                  '<p class="small text-muted">' + escapeHtml(proof.text) + ' &middot; <a href="' + escapeHtml(proof.path) + '" target="_blank" rel="noopener">{{ trans("lang.dfr_open_full_size") }}</a></p>'
                : '<p class="text-muted">{{ trans("lang.no_proof_uploaded") }}</p>') +
            '<div class="form-text text-muted mb-3">{{ trans("lang.dfr_verify_help") }}</div>' +
            '<button type="button" class="btn btn-primary mb-1" id="flow_panel_confirm"><i class="fa fa-check"></i> {{ trans("lang.dfr_confirm_payment") }}</button> ' +
            '<button type="button" class="btn btn-outline-danger mb-1" id="flow_panel_reject"><i class="fa fa-times"></i> {{ trans("lang.dfr_reject_request") }}</button>'
        );

        $('#flow_panel_confirm').on('click', async function () {
            $('.flow_panel_error').addClass('d-none');
            try {
                if (!(await confirmFlowPayment(flow))) {
                    return;
                }
                var fresh = await reloadFlow(flow);
                replaceFlow(fresh);
                showFlowMessage('success', "{{ trans('lang.dfr_payment_confirmed') }}".replace(':id', '#' + flow.request.id));
                openReceivePanel(fresh);
            } catch (error) {
                console.error(error);
                showPanelError(error.message);
                replaceFlow(await reloadFlow(flow));
            }
        });
        $('#flow_panel_reject').on('click', function () {
            $('#flowSidePanel').modal('hide');
            runFlowAction(flow, 'reject', LIST_HANDLERS);
        });
        $('#flowSidePanel').modal('show');
    }

    // Add received fuel (F5): the existing Add Received Fuel to Stock form, in the side panel
    async function openReceivePanel(flow) {
        var body = $('#flow_panel_body');
        $('.flow_panel_error').addClass('d-none');
        $('#flow_panel_title').text("{{ trans('lang.receive_fuel_title') }}");
        body.html(flowPanelSummary(flow));
        $('#flowSidePanel').modal('show');

        var station = null;
        try {
            var stationSnapshot = await database.collection('users').doc(flow.request.ownerId).get();
            station = stationSnapshot.exists ? stationSnapshot.data() : null;
        } catch (error) {
            console.error(error);
        }

        var formContainer = $('<div></div>').appendTo(body);
        renderReceiveForm(formContainer, flow, station, {
            intro: true,
            onSkip: function () {
                $('#flowSidePanel').modal('hide');
            },
            onAdded: async function (stock, litres) {
                var fresh = await reloadFlow(flow);
                replaceFlow(fresh);
                var next = flowStage(fresh);
                body.html(
                    '<div class="alert alert-success">' + escapeHtml(flowReceiveDoneText(stock, litres, station)) + '</div>' +
                    (next.action ? '<p class="mb-3">' + escapeHtml("{{ trans('lang.dfr_next_step') }}".replace(':stage', next.label)) + '</p>' : '') +
                    '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans("lang.close") }}</button>'
                );
            }
        });
    }

    async function deleteFuelRequest(key) {
        var flow = flowMap[key];
        if (!flow || !flow.request) {
            return;
        }
        if (!confirm("{{ trans('lang.dfr_delete_confirm') }}".replace(':id', '#' + flow.request.id))) {
            return;
        }
        jQuery("#overlay").show();
        try {
            await database.collection('fuel_request').doc(flow.request.id).delete();
            logFirebaseActivity('#' + flow.request.id + ' {{ trans("lang.fuel_request") }} : {{ trans("lang.delete") }}');
            loadFlows();
        } catch (error) {
            console.error(error);
            jQuery("#overlay").hide();
            showFlowMessage('danger', error.message);
        }
    }
</script>
@endsection
