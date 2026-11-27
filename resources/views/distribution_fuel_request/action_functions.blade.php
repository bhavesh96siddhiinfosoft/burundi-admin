{{--
    Actions of the Distribution Fuel Request tab (F7), shared by the list and the detail page.
    Needs distributions.status_functions, fuel_request.receipt_functions and distribution_fuel_request.flow_functions.
    The fields written are the same as before: the request status (U1), the distribution status (F2, with the
    notifications of F3 and F4) and the fuel receipt (F5).
--}}
<script>
    var FLOW_REQUEST_STATUS_LABELS = {
        requestedByOwner: "{{ trans('lang.dfr_request_requested') }}",
        approvedByAdmin: "{{ trans('lang.dfr_request_approved') }}",
        paymentByOwner: "{{ trans('lang.dfr_request_payment') }}",
        completedByAdmin: "{{ trans('lang.dfr_request_completed') }}",
        rejectedByAdmin: "{{ trans('lang.dfr_request_rejected') }}"
    };
    var FLOW_REQUEST_STATUS_BADGES = {
        requestedByOwner: 'warning',
        approvedByAdmin: 'info',
        paymentByOwner: 'primary',
        completedByAdmin: 'success',
        rejectedByAdmin: 'danger'
    };

    function flowRequestStatusLabel(status) {
        return FLOW_REQUEST_STATUS_LABELS[status] || status || '';
    }

    function flowRequestBadge(status) {
        return '<span class="badge badge-' + (FLOW_REQUEST_STATUS_BADGES[status] || 'secondary') + '">' +
            escapeHtml(flowRequestStatusLabel(status)) + '</span>';
    }

    function flowUrl(id) {
        return '{{ route("distribution.fuel.request.view", ":id") }}'.replace(':id', encodeURIComponent(id));
    }

    /**
     * The one next-step button of a flow (F7 2.2), or "Waiting for owner" / "Finished", followed by the
     * secondary Reject request and Cancel distribution links. Buttons carry data-key and data-action.
     */
    function flowActionButtons(flow, stage, large) {
        var key = escapeHtml(flow.key);
        var size = large ? '' : ' btn-sm';
        var button = function (action, css, icon, label, disabled) {
            return '<button type="button" class="btn' + size + ' ' + css + ' flow-action mb-1" data-key="' + key + '" data-action="' + action + '"' +
                (disabled ? ' disabled' : '') + '><i class="fa ' + icon + '"></i> ' + escapeHtml(label) + '</button>';
        };
        var link = function (action, label) {
            return '<a href="javascript:void(0)" class="text-danger flow-action" data-key="' + key + '" data-action="' + action + '">' + escapeHtml(label) + '</a>';
        };

        var action = stage.action;
        var html = '';
        if (action && action.type === 'approve') {
            html += button('approve', 'btn-primary', 'fa-check', "{{ trans('lang.dfr_approve_request') }}");
        } else if (action && action.type === 'verify') {
            html += button('verify', 'btn-primary', 'fa-search', "{{ trans('lang.dfr_verify_payment') }}");
        } else if (action && action.type === 'receive') {
            html += button('receive', 'btn-primary', 'fa-plus', "{{ trans('lang.dfr_add_received_fuel') }}");
        } else if (action && action.type === 'move') {
            html += button('move', 'btn-primary', 'fa-arrow-right',
                "{{ trans('lang.distribution_move_to') }}".replace(':status', distributionStatusLabel(action.status)), !!stage.disabledReason);
        } else if (action && action.type === 'cancel') {
            html += button('cancel', 'btn-danger', 'fa-ban', "{{ trans('lang.distribution_cancel_btn') }}");
        } else if (stage.waiting) {
            html += '<span class="text-muted">{{ trans("lang.dfr_waiting_for_owner") }}</span>';
        } else {
            html += '<span class="text-muted">{{ trans("lang.dfr_stage_finished") }}</span>';
        }
        if (stage.disabledReason && !large && action) {
            html += '<div class="small text-muted">' + escapeHtml(stage.disabledReason) + '</div>';
        }

        var links = [];
        if (stage.canReject) {
            links.push(link('reject', "{{ trans('lang.dfr_reject_request') }}"));
        }
        if (stage.canCancel && !(action && action.type === 'cancel')) {
            links.push(link('cancel', "{{ trans('lang.distribution_cancel_btn') }}"));
        }
        if (links.length) {
            html += '<div class="' + (large ? 'mt-2' : 'small') + '">' + links.join(' &middot; ') + '</div>';
        }
        return html;
    }

    function flowLitres(value) {
        return escapeHtml(value) + ' {{ trans("lang.liter") }}';
    }

    // The flow read again from Firestore, so an action never runs on a stale status
    async function reloadFlow(flow) {
        var fresh = { key: flow.key, request: null, distribution: null, receipt: null, requestMissing: false, gone: false };
        var requestId = flow.request ? flow.request.id : (flow.distribution && flow.distribution.fuelRequestId) || null;

        if (requestId) {
            var requestSnapshot = await database.collection('fuel_request').doc(requestId).get();
            if (requestSnapshot.exists) {
                fresh.request = requestSnapshot.data();
                fresh.request.id = requestSnapshot.id;
            }
        }
        if (flow.distribution) {
            var distributionSnapshot = await database.collection('distributions').doc(flow.distribution.id).get();
            if (distributionSnapshot.exists) {
                fresh.distribution = distributionSnapshot.data();
                fresh.distribution.id = distributionSnapshot.id;
            }
        } else if (fresh.request) {
            var distributionsSnapshot = await database.collection('distributions').where('fuelRequestId', '==', fresh.request.id).get();
            fresh.distribution = flowDocs(distributionsSnapshot).sort(flowNewestDistributionFirst)[0] || null;
        }
        if (fresh.request) {
            fresh.receipt = await findFuelRequestReceipt(fresh.request.id);
        }
        fresh.requestMissing = !!(fresh.distribution && fresh.distribution.fuelRequestId && !fresh.request);
        fresh.gone = !fresh.request && !fresh.distribution;
        return fresh;
    }

    // U1: the request status moves only from the statuses given (checked inside a transaction)
    async function setFuelRequestStatus(requestId, fromStatuses, newStatus) {
        var requestRef = database.collection('fuel_request').doc(requestId);
        await database.runTransaction(async function (transaction) {
            var snapshot = await transaction.get(requestRef);
            if (!snapshot.exists) {
                throw new Error("{{ trans('lang.fuel_request_not_found') }}");
            }
            var status = snapshot.data().status;
            if (fromStatuses.indexOf(status) === -1) {
                throw new Error("{{ trans('lang.dfr_request_status_changed') }}".replace(':status', flowRequestStatusLabel(status)));
            }
            transaction.update(requestRef, {
                status: newStatus,
                updatedAt: firebase.firestore.FieldValue.serverTimestamp()
            });
        });
        logFirebaseActivity('#' + requestId + ' {{ trans("lang.fuel_request") }} : ' + flowRequestStatusLabel(newStatus));
    }

    function showFlowMessage(type, text) {
        var container = $('#flow_messages');
        if (!container.length) {
            container = $('<div id="flow_messages"></div>').css({
                position: 'fixed', top: '80px', right: '20px', zIndex: 2000, width: '440px', maxWidth: '90%'
            }).appendTo('body');
        }
        var alert = $('<div class="alert alert-dismissible shadow" role="alert"></div>').addClass('alert-' + type);
        alert.append('<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>');
        alert.append($('<span></span>').text(text));
        container.append(alert);
        if (type === 'success') {
            setTimeout(function () {
                alert.fadeOut(400, function () { alert.remove(); });
            }, 12000);
        }
    }

    var flowActionRunning = false;

    /**
     * Runs one action of a flow (F7 2.5). The flow is read again first: when its stage changed since the page
     * loaded (another admin, the owner app, or the employee app completing it when the stock ran out), nothing
     * is done and the page shows the current stage.
     * handlers.updated(flow) redraws the flow; handlers.verify(flow) and handlers.receive(flow) open the
     * page's own payment check and received fuel form.
     */
    async function runFlowAction(flow, type, handlers) {
        if (flowActionRunning) {
            return;
        }
        flowActionRunning = true;
        jQuery("#overlay").show();
        try {
            var before = flowStage(flow);
            var fresh = await reloadFlow(flow);
            var stage = flowStage(fresh);
            handlers.updated(fresh);
            if (fresh.gone) {
                showFlowMessage('warning', "{{ trans('lang.dfr_not_found') }}");
                return;
            }
            if (!flowActionAllowed(type, before, stage)) {
                showFlowMessage('warning', "{{ trans('lang.dfr_flow_changed') }}".replace(':stage', stage.label));
                return;
            }
            jQuery("#overlay").hide();
            await FLOW_ACTIONS[type](fresh, stage, handlers);
        } catch (error) {
            console.error(error);
            showFlowMessage('danger', error.message);
            try {
                handlers.updated(await reloadFlow(flow));
            } catch (reloadError) {
                console.error(reloadError);
            }
        } finally {
            jQuery("#overlay").hide();
            flowActionRunning = false;
        }
    }

    function flowActionAllowed(type, before, stage) {
        if (type === 'cancel') {
            return stage.canCancel;
        }
        if (type === 'reject') {
            return stage.canReject;
        }
        return !!stage.action && !!before.action && stage.action.type === type && stage.key === before.key &&
            !stage.disabledReason && (type !== 'move' || stage.action.status === before.action.status);
    }

    var FLOW_ACTIONS = {
        approve: async function (flow, stage, handlers) {
            var requestId = flow.request.id;
            if (!confirm("{{ trans('lang.dfr_approve_confirm') }}".replace(':id', '#' + requestId))) {
                return;
            }
            jQuery("#overlay").show();
            await setFuelRequestStatus(requestId, ['requestedByOwner'], 'approvedByAdmin');
            handlers.updated(await reloadFlow(flow));
            showFlowMessage('success', "{{ trans('lang.dfr_request_approved_done') }}".replace(':id', '#' + requestId));
        },

        // U1: rejecting a request cancels its open distribution, so its booked customers are told (F3)
        reject: async function (flow, stage, handlers) {
            var requestId = flow.request.id;
            var cancelDistribution = flowDistributionIsOpen(flow);
            var question = cancelDistribution
                ? "{{ trans('lang.dfr_reject_cancel_confirm') }}".replace(':count', parseInt(flow.distribution.registeredCount, 10) || 0)
                : "{{ trans('lang.dfr_reject_confirm') }}";
            if (!confirm(question.replace(':id', '#' + requestId))) {
                return;
            }
            jQuery("#overlay").show();
            await setFuelRequestStatus(requestId, ['requestedByOwner', 'paymentByOwner'], 'rejectedByAdmin');

            var type = 'success';
            var text = "{{ trans('lang.dfr_request_rejected_done') }}".replace(':id', '#' + requestId);
            if (cancelDistribution) {
                try {
                    var summary = await changeDistributionStatus(flow.distribution.id, 'cancelled');
                    text += ' ' + "{{ trans('lang.distribution_status_updated') }}".replace(':status', distributionStatusLabel('cancelled')) +
                        ' ' + distributionSummaryText(summary);
                    if (summary.errors.length) {
                        type = 'warning';
                        text += ' ' + "{{ trans('lang.distribution_notify_errors') }} " + summary.errors.join(', ');
                    }
                } catch (error) {
                    console.error(error);
                    type = 'warning';
                    text += ' ' + "{{ trans('lang.fuel_request_cancel_distribution_failed') }} " + error.message;
                }
            }
            handlers.updated(await reloadFlow(flow));
            showFlowMessage(type, text);
        },

        move: async function (flow, stage, handlers) {
            var newStatus = stage.action.status;
            var question = "{{ trans('lang.distribution_move_confirm') }}".replace(':status', distributionStatusLabel(newStatus));
            if (newStatus === 'completed') {
                question += ' ' + "{{ trans('lang.dfr_complete_hint') }}";
            }
            if (!confirm(question)) {
                return;
            }
            jQuery("#overlay").show();
            await flowChangeDistributionStatus(flow, newStatus, handlers);
        },

        cancel: async function (flow, stage, handlers) {
            if (!confirm("{{ trans('lang.distribution_cancel_confirm') }}")) {
                return;
            }
            jQuery("#overlay").show();
            await flowChangeDistributionStatus(flow, 'cancelled', handlers);
        },

        verify: function (flow, stage, handlers) {
            return handlers.verify(flow);
        },

        receive: function (flow, stage, handlers) {
            return handlers.receive(flow);
        }
    };

    // F2 + F3 + F4, exactly as the old Distributions tab
    async function flowChangeDistributionStatus(flow, newStatus, handlers) {
        var summary = await changeDistributionStatus(flow.distribution.id, newStatus);
        handlers.updated(await reloadFlow(flow));
        showFlowMessage('success', "{{ trans('lang.distribution_status_updated') }}".replace(':status', distributionStatusLabel(newStatus)) +
            ' ' + distributionSummaryText(summary));
        if (summary.errors.length) {
            showFlowMessage('warning', "{{ trans('lang.distribution_notify_errors') }} " + summary.errors.join(', '));
        }
    }

    // Verify payment: confirming sets completedByAdmin (U1). Returns false when the admin says no.
    async function confirmFlowPayment(flow) {
        var requestId = flow.request.id;
        if (!confirm("{{ trans('lang.dfr_verify_confirm') }}".replace(':id', '#' + requestId))) {
            return false;
        }
        jQuery("#overlay").show();
        try {
            await setFuelRequestStatus(requestId, ['paymentByOwner'], 'completedByAdmin');
        } finally {
            jQuery("#overlay").hide();
        }
        return true;
    }

    function flowProofDetails(request) {
        var proof = request && request.paymentRequestDetails;
        if (!proof || !proof.path) {
            return null;
        }
        return {
            path: proof.path,
            text: (proof.name || '') + (proof.size ? ' (' + proof.size + ')' : '')
        };
    }

    /**
     * The Add Received Fuel to Stock form (F5) inside a container: received litres (prefilled with the requested
     * litres), received at and note, with the new stock previewed. options:
     *   intro: show the explanation,
     *   onAdded(stock, litres): called once the fuel is in the stock, onSkip(): shows a Skip for now button.
     */
    function renderReceiveForm(container, flow, station, options) {
        var request = flow.request;
        var stationFuel = station ? (station.fuel || []).find(function (fuel) { return fuel.fuelTypeId === request.fuelTypeId; }) : null;
        if (!stationFuel) {
            container.html('<div class="alert alert-danger mb-0">' +
                escapeHtml(station ? "{{ trans('lang.fuel_receipt_fuel_error') }}" : "{{ trans('lang.fuel_receipt_station_not_found') }}") + '</div>');
            return;
        }

        var stockLine = function (label, value) {
            return '<li class="list-group-item d-flex justify-content-between px-0"><span class="font-weight-bold">' + label + '</span>' +
                '<span class="text-muted">' + flowLitres(value || 0) + '</span></li>';
        };
        container.html(
            (options.intro ? '<div class="alert alert-info">' + escapeHtml("{{ trans('lang.receive_fuel_intro') }}".replace(':id', '#' + request.id)) + '</div>' : '') +
            '<div class="alert alert-danger receive-error d-none"></div>' +
            '<h6 class="text-dark">{{ trans("lang.receive_fuel_station_stock") }}: ' + escapeHtml(stationFuel.name || '') + '</h6>' +
            '<ul class="list-group list-group-flush mb-3">' +
                stockLine("{{ trans('lang.receive_fuel_tank_capacity') }}", stationFuel.fuelCapacity) +
                stockLine("{{ trans('lang.receive_fuel_total_quota') }}", stationFuel.totalFuelQuota) +
                stockLine("{{ trans('lang.receive_fuel_current_stock') }}", stationFuel.availableFuelQuota) +
            '</ul>' +
            '<div class="form-group">' +
                '<label class="control-label font-weight-bold">{{ trans("lang.receive_fuel_received_litres") }}<span class="required-field"></span></label>' +
                '<input type="number" class="form-control receive-litres" min="0" step="any">' +
                '<div class="form-text text-muted">{{ trans("lang.receive_fuel_received_litres_help") }}</div>' +
            '</div>' +
            '<div class="form-group">' +
                '<label class="control-label font-weight-bold">{{ trans("lang.fuel_receipt_received_at") }}<span class="required-field"></span></label>' +
                '<input type="datetime-local" class="form-control receive-received-at">' +
            '</div>' +
            '<div class="form-group">' +
                '<label class="control-label font-weight-bold">{{ trans("lang.note") }}</label>' +
                '<input type="text" class="form-control receive-note">' +
            '</div>' +
            '<div class="alert alert-light border mb-2 receive-preview"></div>' +
            '<div class="form-text text-muted mb-3">{{ trans("lang.receive_fuel_help") }}</div>' +
            '<button type="button" class="btn btn-primary receive-add"><i class="fa fa-plus"></i> {{ trans("lang.receive_fuel_add_btn") }}</button> ' +
            (options.onSkip ? '<button type="button" class="btn btn-outline-secondary receive-skip">{{ trans("lang.receive_fuel_skip") }}</button>' : '')
        );

        var litresInput = container.find('.receive-litres');
        var preview = container.find('.receive-preview');
        var error = container.find('.receive-error');
        var showError = function (message) {
            error.removeClass('d-none').text(message);
        };
        var updatePreview = function () {
            var litres = parseFloat(litresInput.val());
            if (isNaN(litres) || litres <= 0) {
                preview.removeClass('alert-danger').addClass('alert-light').text("{{ trans('lang.fuel_receipt_litres_error') }}");
                return;
            }
            var stock = stationStockAfterReceipt([stationFuel], stationFuel.fuelTypeId, litres);
            if (stock.error === 'capacity') {
                preview.removeClass('alert-light').addClass('alert-danger').text(
                    "{{ trans('lang.receive_fuel_capacity_error') }}".replace(':new', stock.newAvailable).replace(':capacity', stock.capacity));
                return;
            }
            preview.removeClass('alert-danger').addClass('alert-light').text(
                "{{ trans('lang.receive_fuel_new_stock') }}"
                    .replace(':new', stock.after.available)
                    .replace(':current', parseFloat(stationFuel.availableFuelQuota) || 0)
                    .replace(':received', litres));
        };

        litresInput.val(parseFloat(request.fuelQuota) > 0 ? parseFloat(request.fuelQuota) : '');
        container.find('.receive-received-at').val(flowInputDateTime(new Date()));
        litresInput.on('input change', updatePreview);
        updatePreview();

        if (options.onSkip) {
            container.find('.receive-skip').on('click', options.onSkip);
        }

        var running = false;
        container.find('.receive-add').on('click', async function () {
            if (running) {
                return;
            }
            error.addClass('d-none');
            var litres = parseFloat(litresInput.val());
            var receivedAtValue = container.find('.receive-received-at').val();
            var receivedAt = receivedAtValue ? new Date(receivedAtValue) : null;
            var note = container.find('.receive-note').val().trim();

            if (isNaN(litres) || litres <= 0) {
                return showError("{{ trans('lang.fuel_receipt_litres_error') }}");
            }
            if (!receivedAt || isNaN(receivedAt.getTime()) || receivedAt.getTime() > Date.now()) {
                return showError("{{ trans('lang.fuel_receipt_received_at_error') }}");
            }
            var question = "{{ trans('lang.receive_fuel_confirm') }}"
                .replace(':litres', litres)
                .replace(':fuel', stationFuel.name || '')
                .replace(':station', station.fuelStationName || '');
            if (!confirm(question)) {
                return;
            }

            running = true;
            container.find('.receive-add').prop('disabled', true);
            jQuery("#overlay").show();
            try {
                // A receipt written before the request_<id> naming also counts as added
                var existing = await findFuelRequestReceipt(request.id);
                if (existing) {
                    throw new Error("{{ trans('lang.receive_fuel_already_added') }}"
                        .replace(':litres', existing.litres)
                        .replace(':date', formatFirestoreDate(existing.receivedAt)));
                }
                var stock = await addReceivedFuel(request.id, litres, receivedAt, note);
                logFirebaseActivity('#' + request.id + ' {{ trans("lang.fuel_request") }} : {{ trans("lang.receive_fuel_title") }} ' + litres + ' L');
                await options.onAdded(stock, litres);
            } catch (addError) {
                console.error(addError);
                showError(addError.message);
            } finally {
                jQuery("#overlay").hide();
                running = false;
                container.find('.receive-add').prop('disabled', false);
            }
        });
    }

    function flowReceiveDoneText(stock, litres, station) {
        return "{{ trans('lang.receive_fuel_done') }}"
            .replace(':litres', litres)
            .replace(':fuel', stock.fuelName)
            .replace(':station', (station && station.fuelStationName) || '')
            .replace(':stock', stock.after.available);
    }

    function flowInputDateTime(date) {
        var pad = function (number) { return String(number).padStart(2, '0'); };
        return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) + 'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());
    }
</script>
