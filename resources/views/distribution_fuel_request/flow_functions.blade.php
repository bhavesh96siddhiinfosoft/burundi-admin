{{--
    Distribution Fuel Request (F7). A flow is a fuel request plus the distribution it published
    (distributions.fuelRequestId == fuel_request.id). A request without a distribution (made before advance
    booking) and a distribution without a request are flows of their own.
    The stage of a flow decides the one next step offered to the admin.
    Used by the list and detail pages, and by the menu badge (flows that need admin action) on every page,
    so it depends on nothing but the global `database`.
--}}
<script>
    var FLOW_OPEN_DISTRIBUTION_STATUSES = ['scheduled', 'being_supplied', 'supplied', 'ready', 'in_progress'];
    var FLOW_NEXT_DISTRIBUTION_STATUS = {
        scheduled: 'being_supplied',
        being_supplied: 'supplied',
        supplied: 'ready',
        ready: 'in_progress',
        in_progress: 'completed'
    };
    var FLOW_STAGE_ORDER = ['new_request', 'waiting_payment', 'verify_payment', 'add_fuel', 'ready_to_supply', 'on_its_way',
        'arrived', 'ready', 'distributing', 'finished', 'cancelled', 'rejected'];
    var FLOW_NEEDS_ACTION_STAGES = ['new_request', 'verify_payment', 'add_fuel', 'ready_to_supply', 'on_its_way', 'arrived', 'ready'];
    var FLOW_STAGE_LABELS = {
        new_request: "{{ trans('lang.dfr_stage_new_request') }}",
        waiting_payment: "{{ trans('lang.dfr_stage_waiting_payment') }}",
        verify_payment: "{{ trans('lang.dfr_stage_verify_payment') }}",
        add_fuel: "{{ trans('lang.dfr_stage_add_fuel') }}",
        ready_to_supply: "{{ trans('lang.dfr_stage_ready_to_supply') }}",
        on_its_way: "{{ trans('lang.dfr_stage_on_its_way') }}",
        arrived: "{{ trans('lang.dfr_stage_arrived') }}",
        ready: "{{ trans('lang.dfr_stage_ready') }}",
        distributing: "{{ trans('lang.dfr_stage_distributing') }}",
        finished: "{{ trans('lang.dfr_stage_finished') }}",
        cancelled: "{{ trans('lang.dfr_stage_cancelled') }}",
        rejected: "{{ trans('lang.dfr_stage_rejected') }}"
    };

    function flowDistributionStatus(flow) {
        return flow.distribution ? (flow.distribution.status || 'scheduled') : null;
    }

    function flowDistributionIsOpen(flow) {
        return !!flow.distribution && FLOW_OPEN_DISTRIBUTION_STATUSES.indexOf(flowDistributionStatus(flow)) !== -1;
    }

    /**
     * The stage of a flow (F7 2.2). Request steps come first while the distribution is open; a closed
     * distribution makes the flow read-only. Returns:
     *   key, label, needsAction,
     *   action: { type: 'approve' | 'verify' | 'receive' | 'move' | 'cancel', status: <next status for move> } or null,
     *   waiting: true while the owner has to act, disabledReason: why the action can't run yet,
     *   note: extra line under the stage, canCancel / canReject: the secondary actions.
     */
    function flowStage(flow) {
        var request = flow.request;
        var distribution = flow.distribution;
        var requestStatus = request ? request.status : null;
        var distributionStatus = flowDistributionStatus(flow);
        var distributionOpen = flowDistributionIsOpen(flow);
        var stage = {
            key: 'finished',
            label: '',
            needsAction: false,
            action: null,
            waiting: false,
            disabledReason: '',
            note: '',
            canCancel: distributionOpen,
            // As the old status dropdown: reject while the request waits for an admin decision
            canReject: requestStatus === 'requestedByOwner' || requestStatus === 'paymentByOwner'
        };

        if (requestStatus === 'rejectedByAdmin') {
            stage.key = 'rejected';
            if (distributionOpen) {
                stage.action = { type: 'cancel' };
            }
        } else if (distribution && !distributionOpen) {
            stage.key = distributionStatus === 'cancelled' ? 'cancelled' : 'finished';
        } else if (requestStatus === 'requestedByOwner') {
            stage.key = 'new_request';
            stage.action = { type: 'approve' };
        } else if (requestStatus === 'approvedByAdmin') {
            stage.key = 'waiting_payment';
            stage.waiting = true;
            stage.disabledReason = "{{ trans('lang.dfr_waiting_owner_reason') }}";
        } else if (requestStatus === 'paymentByOwner') {
            stage.key = 'verify_payment';
            stage.action = { type: 'verify' };
        } else if (requestStatus === 'completedByAdmin' && !flow.receipt && distribution) {
            stage.key = 'add_fuel';
            stage.action = { type: 'receive' };
        } else if (!distribution) {
            // A completed request made before advance booking: its fuel went into stock by allocation
            stage.key = 'finished';
        } else {
            var stageByStatus = {
                scheduled: 'ready_to_supply',
                being_supplied: 'on_its_way',
                supplied: 'arrived',
                ready: 'ready',
                in_progress: 'distributing'
            };
            stage.key = stageByStatus[distributionStatus];
            stage.action = { type: 'move', status: FLOW_NEXT_DISTRIBUTION_STATUS[distributionStatus] };
        }

        // Supplied only once the linked request's payment is approved (F2); the transaction checks it again
        var suppliedGated = distribution && distributionStatus === 'being_supplied' && !!distribution.fuelRequestId &&
            (!request || requestStatus !== 'completedByAdmin');
        if (suppliedGated) {
            var gateReason = flow.requestMissing ? "{{ trans('lang.dfr_request_missing_reason') }}" : "{{ trans('lang.dfr_supplied_gate_reason') }}";
            if (stage.key === 'on_its_way') {
                stage.disabledReason = gateReason;
            } else if (distributionOpen) {
                stage.note = gateReason;
            }
        }
        if (stage.key === 'distributing') {
            stage.note = "{{ trans('lang.dfr_complete_hint') }}";
        }

        stage.label = flowStageLabel(stage.key, flow);
        stage.needsAction = FLOW_NEEDS_ACTION_STAGES.indexOf(stage.key) !== -1;
        return stage;
    }

    function flowStageLabel(key, flow) {
        if (key === 'distributing' && flow && flow.distribution) {
            return "{{ trans('lang.dfr_stage_distributing_count') }}".replace(':count', parseInt(flow.distribution.servedCount, 10) || 0);
        }
        if (key === 'finished' && flow && flow.distribution && flow.distribution.completedReason === 'stock_empty') {
            return "{{ trans('lang.dfr_stage_stock_empty') }}";
        }
        return FLOW_STAGE_LABELS[key] || key;
    }

    function flowDocs(snapshot) {
        return snapshot.docs.map(function (doc) {
            var data = doc.data();
            data.id = doc.id;
            return data;
        });
    }

    function flowMillis(timestamp) {
        return timestamp && typeof timestamp.toMillis === 'function' ? timestamp.toMillis() : 0;
    }

    // Newest distribution first (same publish time: by id), so every page picks the same one for a request
    function flowNewestDistributionFirst(a, b) {
        return (flowMillis(b.publishedAt) - flowMillis(a.publishedAt)) || (a.id < b.id ? -1 : (a.id > b.id ? 1 : 0));
    }

    // Firestore 'in' queries take at most 10 values
    async function flowQueryIn(collection, field, values) {
        var chunks = [];
        for (var i = 0; i < values.length; i += 10) {
            chunks.push(values.slice(i, i + 10));
        }
        var snapshots = await Promise.all(chunks.map(function (chunk) {
            return database.collection(collection).where(field, 'in', chunk).get();
        }));
        var docs = [];
        snapshots.forEach(function (snapshot) {
            docs = docs.concat(flowDocs(snapshot));
        });
        return docs;
    }

    /**
     * Joins requests, distributions and fuel receipts into flows. Each request takes its newest distribution;
     * its receipt is fuel_receipts/request_<id>, or one written before that naming.
     */
    function buildFlows(requests, distributions, receipts) {
        var requestMap = {};
        requests.forEach(function (request) {
            requestMap[request.id] = request;
        });

        var receiptMap = {};
        receipts.forEach(function (receipt) {
            if (!receipt.fuelRequestId) {
                return;
            }
            if (!receiptMap[receipt.fuelRequestId] || receipt.id === 'request_' + receipt.fuelRequestId) {
                receiptMap[receipt.fuelRequestId] = receipt;
            }
        });

        var flows = [];
        var distributionByRequest = {};
        distributions.slice().sort(flowNewestDistributionFirst).forEach(function (distribution) {
            var requestId = distribution.fuelRequestId || null;
            if (requestId && requestMap[requestId] && !distributionByRequest[requestId]) {
                distributionByRequest[requestId] = distribution;
                return;
            }
            flows.push({
                key: distribution.id,
                request: requestId && requestMap[requestId] ? requestMap[requestId] : null,
                distribution: distribution,
                receipt: requestId ? (receiptMap[requestId] || null) : null,
                requestMissing: !!requestId && !requestMap[requestId]
            });
        });

        requests.forEach(function (request) {
            flows.push({
                key: request.id,
                request: request,
                distribution: distributionByRequest[request.id] || null,
                receipt: receiptMap[request.id] || null,
                requestMissing: false
            });
        });
        return flows;
    }

    // Every flow (list page with "Needs action only" off)
    async function loadAllFlows() {
        var results = await Promise.all([
            database.collection('fuel_request').get(),
            database.collection('distributions').get(),
            database.collection('fuel_receipts').where('source', '==', 'fuel_request').get()
        ]);
        return buildFlows(flowDocs(results[0]), flowDocs(results[1]), flowDocs(results[2]));
    }

    /**
     * Only the flows that need admin action, read with a few small queries (the menu badge runs on every page).
     * A flow can need action only while its request waits for the admin or its distribution is open,
     * so these give exactly the same flows as loadAllFlows() filtered on needsAction.
     */
    async function loadNeedsActionFlows() {
        var results = await Promise.all([
            database.collection('fuel_request').where('status', 'in', ['requestedByOwner', 'paymentByOwner']).get(),
            database.collection('distributions').where('status', 'in', FLOW_OPEN_DISTRIBUTION_STATUSES).get()
        ]);
        var requests = flowDocs(results[0]);
        var openDistributions = flowDocs(results[1]);

        var requestMap = {};
        requests.forEach(function (request) {
            requestMap[request.id] = request;
        });

        // The requests of the open distributions
        var missingRequestIds = [];
        openDistributions.forEach(function (distribution) {
            if (distribution.fuelRequestId && !requestMap[distribution.fuelRequestId] &&
                missingRequestIds.indexOf(distribution.fuelRequestId) === -1) {
                missingRequestIds.push(distribution.fuelRequestId);
            }
        });
        var requestSnapshots = await Promise.all(missingRequestIds.map(function (requestId) {
            return database.collection('fuel_request').doc(requestId).get();
        }));
        requestSnapshots.forEach(function (snapshot) {
            if (snapshot.exists) {
                var request = snapshot.data();
                request.id = snapshot.id;
                requests.push(request);
            }
        });

        // Every distribution of these requests (a request takes its newest one; a closed one makes the
        // flow read-only), and the receipts of the completed ones (Fuel to add to stock)
        var requestIds = requests.map(function (request) {
            return request.id;
        });
        var completedIds = requests.filter(function (request) {
            return request.status === 'completedByAdmin';
        }).map(function (request) {
            return request.id;
        });
        var linked = await Promise.all([
            flowQueryIn('distributions', 'fuelRequestId', requestIds),
            flowQueryIn('fuel_receipts', 'fuelRequestId', completedIds)
        ]);
        var linkedDistributions = linked[0];
        var receipts = linked[1];

        var distributionMap = {};
        openDistributions.concat(linkedDistributions).forEach(function (distribution) {
            distributionMap[distribution.id] = distribution;
        });
        var distributions = Object.keys(distributionMap).map(function (id) {
            return distributionMap[id];
        });

        return buildFlows(requests, distributions, receipts).filter(function (flow) {
            return flowStage(flow).needsAction;
        });
    }

    function updateFlowMenuBadge(count) {
        $('#dfr_menu_badge').text(count).toggleClass('d-none', !count);
    }
</script>
