{{--
    Distribution status helpers, shared by the distribution detail page and the fuel request page.
    F2: only the admin moves the status, one step at a time, inside a transaction.
    F3: every status change notifies the customers with an active registration.
    F4: when the distribution starts, the first customers in line are told their turn is approaching.
--}}
<script>
    var DISTRIBUTION_STATUS_ORDER = ['scheduled', 'being_supplied', 'supplied', 'ready', 'in_progress', 'completed'];
    var DISTRIBUTION_FINAL_STATUSES = ['completed', 'cancelled'];
    var BOOKING_ACTIVE_STATUSES = ['registered', 'turn_approaching', 'called'];
    var DISTRIBUTION_ADMIN_ID = "{{ auth()->id() }}";

    var DISTRIBUTION_STATUS_LABELS = {
        scheduled: "{{ trans('lang.distribution_status_scheduled') }}",
        being_supplied: "{{ trans('lang.distribution_status_being_supplied') }}",
        supplied: "{{ trans('lang.distribution_status_supplied') }}",
        ready: "{{ trans('lang.distribution_status_ready') }}",
        in_progress: "{{ trans('lang.distribution_status_in_progress') }}",
        completed: "{{ trans('lang.distribution_status_completed') }}",
        cancelled: "{{ trans('lang.distribution_status_cancelled') }}"
    };
    var DISTRIBUTION_STATUS_BADGES = {
        scheduled: 'secondary',
        being_supplied: 'info',
        supplied: 'primary',
        ready: 'warning',
        in_progress: 'success',
        completed: 'dark',
        cancelled: 'danger'
    };

    // Push texts are sent in English: the apps store no language per customer.
    var BOOKING_PUSH_TEXTS = {
        being_supplied: {
            type: 'booking_being_supplied',
            title: 'Fuel is on its way',
            body: '@station is being supplied with fuel. Your spot is reserved.'
        },
        supplied: {
            type: 'booking_supplied',
            title: 'Fuel has arrived',
            body: '@station has received fuel. Distribution will start soon.'
        },
        ready: {
            type: 'booking_ready',
            title: 'Ready to distribute',
            body: '@station is ready to start distribution. Stay tuned.'
        },
        in_progress: {
            type: 'booking_in_progress',
            title: 'Distribution started',
            body: '@station has started distribution. Stay nearby, you will be notified when your turn approaches.'
        },
        completed: {
            type: 'booking_completed',
            title: 'Distribution ended',
            body: 'The distribution at @station has ended.'
        },
        cancelled: {
            type: 'booking_cancelled',
            title: 'Distribution cancelled',
            body: 'The distribution at @station has been cancelled.'
        },
        turn_approaching: {
            type: 'booking_turn_approaching',
            title: 'Your turn is approaching!',
            body: 'Head to @station soon and show your QR code to the pump attendant.'
        }
    };

    function escapeHtml(value) {
        return String(value === undefined || value === null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatFirestoreDate(timestamp) {
        if (!timestamp || typeof timestamp.toDate !== 'function') {
            return '';
        }
        var date = timestamp.toDate();
        return date.toDateString() + ' ' + date.toLocaleTimeString('en-US');
    }

    function distributionStatusLabel(status) {
        status = status || 'scheduled';
        return DISTRIBUTION_STATUS_LABELS[status] || status;
    }

    function distributionStatusBadge(status) {
        status = status || 'scheduled';
        var badge = DISTRIBUTION_STATUS_BADGES[status] || 'secondary';
        return '<span class="badge badge-' + badge + '">' + escapeHtml(distributionStatusLabel(status)) + '</span>';
    }

    // The only status the admin can move to next, or null when there is none
    function distributionNextStatus(status) {
        var index = DISTRIBUTION_STATUS_ORDER.indexOf(status || 'scheduled');
        if (index === -1 || index === DISTRIBUTION_STATUS_ORDER.length - 1) {
            return null;
        }
        return DISTRIBUTION_STATUS_ORDER[index + 1];
    }

    function distributionCanCancel(status) {
        return DISTRIBUTION_FINAL_STATUSES.indexOf(status || 'scheduled') === -1;
    }

    /**
     * F2 + F3 + F4. Moves a distribution to newStatus (the next status, or 'cancelled').
     * The status is checked again inside the transaction, so two admins can't skip a step.
     * Notifications run after the status is saved: a failed push never undoes the change.
     * Returns a summary of what was sent; throws when the status change itself is refused.
     */
    async function changeDistributionStatus(distributionId, newStatus) {
        var distributionRef = database.collection('distributions').doc(distributionId);
        var distribution = null;

        await database.runTransaction(async function (transaction) {
            var snapshot = await transaction.get(distributionRef);
            if (!snapshot.exists) {
                throw new Error("{{ trans('lang.distribution_not_found') }}");
            }
            var data = snapshot.data();
            var currentStatus = data.status || 'scheduled';

            if (newStatus === 'cancelled') {
                if (!distributionCanCancel(currentStatus)) {
                    throw new Error("{{ trans('lang.distribution_final_status_error') }}".replace(':status', distributionStatusLabel(currentStatus)));
                }
            } else if (distributionNextStatus(currentStatus) !== newStatus) {
                throw new Error("{{ trans('lang.distribution_status_changed_error') }}".replace(':status', distributionStatusLabel(currentStatus)));
            }

            // Supplied only once the linked fuel request's payment is approved
            if (newStatus === 'supplied' && data.fuelRequestId) {
                var requestSnapshot = await transaction.get(database.collection('fuel_request').doc(data.fuelRequestId));
                if (!requestSnapshot.exists || requestSnapshot.data().status !== 'completedByAdmin') {
                    throw new Error("{{ trans('lang.distribution_supplied_gate_error') }}");
                }
            }

            var now = firebase.firestore.FieldValue.serverTimestamp();
            var updateData = {
                status: newStatus,
                statusUpdatedAt: now,
                updatedAt: now,
                statusUpdatedBy: DISTRIBUTION_ADMIN_ID
            };
            if (newStatus === 'in_progress') {
                updateData.distributionStartedAt = now;
            }
            if (newStatus === 'completed' || newStatus === 'cancelled') {
                updateData.completedAt = now;
            }
            transaction.update(distributionRef, updateData);
            distribution = data;
        });

        logFirebaseActivity('#' + distributionId + ' {{ trans("lang.distribution") }} : ' + distributionStatusLabel(newStatus));

        var summary = {
            status: { notified: 0, sent: 0, invalid: 0, failed: 0 },
            turn: { notified: 0, sent: 0, invalid: 0, failed: 0 },
            errors: []
        };

        // F3: notify every active registration
        try {
            var registrationsSnapshot = await database.collection('booking_registrations')
                .where('distributionId', '==', distributionId)
                .get();
            var activeRegistrations = registrationsSnapshot.docs
                .map(function (doc) { return doc.data(); })
                .filter(function (registration) {
                    return BOOKING_ACTIVE_STATUSES.indexOf(registration.status) !== -1;
                });
            summary.status = await sendBookingNotifications(activeRegistrations, distributionId,
                BOOKING_PUSH_TEXTS[newStatus], distribution.stationName || '', summary.errors);
        } catch (error) {
            console.error(error);
            summary.errors.push(error.message);
        }

        // F4: once, when the distribution starts
        if (newStatus === 'in_progress') {
            try {
                summary.turn = await notifyTurnApproaching(distributionId, summary.errors);
            } catch (error) {
                console.error(error);
                summary.errors.push(error.message);
            }
        }

        return summary;
    }

    // F4: the first <turnThreshold> customers still waiting are moved to turn_approaching and notified
    async function notifyTurnApproaching(distributionId, errors) {
        var threshold = 15;
        var settingSnapshot = await database.collection('settings').doc('booking_setting').get();
        if (settingSnapshot.exists) {
            var settingThreshold = parseInt(settingSnapshot.data().turnThreshold, 10);
            if (settingThreshold >= 1) {
                threshold = settingThreshold;
            }
        }

        var snapshot = await database.collection('distributions').doc(distributionId).get();
        if (!snapshot.exists) {
            return { notified: 0, sent: 0, invalid: 0, failed: 0 };
        }
        var distribution = snapshot.data();

        var done = {};
        (distribution.servedPositions || []).concat(distribution.cancelledPositions || []).forEach(function (position) {
            done[parseInt(position, 10)] = true;
        });

        var nextPositions = [];
        var positionCounter = parseInt(distribution.positionCounter, 10) || 0;
        for (var position = 1; position <= positionCounter && nextPositions.length < threshold; position++) {
            if (!done[position]) {
                nextPositions.push(position);
            }
        }

        var queue = distribution.queue || {};
        var changed = await Promise.all(nextPositions.map(function (position) {
            var customerId = queue[String(position)];
            if (!customerId) {
                return null;
            }
            var registrationRef = database.collection('booking_registrations').doc(distributionId + '_' + customerId);
            // Only a registration still 'registered' is changed, so nobody is told twice
            return database.runTransaction(async function (transaction) {
                var registrationSnapshot = await transaction.get(registrationRef);
                if (!registrationSnapshot.exists || registrationSnapshot.data().status !== 'registered') {
                    return null;
                }
                transaction.update(registrationRef, {
                    status: 'turn_approaching',
                    turnNotifiedAt: firebase.firestore.FieldValue.serverTimestamp()
                });
                return registrationSnapshot.data();
            }).catch(function (error) {
                console.error(error);
                errors.push(error.message);
                return null;
            });
        }));

        var registrations = changed.filter(function (registration) { return registration; });
        return await sendBookingNotifications(registrations, distributionId,
            BOOKING_PUSH_TEXTS.turn_approaching, distribution.stationName || '', errors);
    }

    // F3: one notifications document per registration, then one push per token
    async function sendBookingNotifications(registrations, distributionId, pushText, stationName, errors) {
        var result = { notified: 0, sent: 0, invalid: 0, failed: 0 };
        if (!registrations.length || !pushText) {
            return result;
        }

        var title = pushText.title.replace(/@station/g, function () { return stationName; });
        var body = pushText.body.replace(/@station/g, function () { return stationName; });

        // A batch holds at most 500 writes
        for (var i = 0; i < registrations.length; i += 500) {
            var batch = database.batch();
            var count = 0;
            registrations.slice(i, i + 500).forEach(function (registration) {
                if (!registration.customerId) {
                    return;
                }
                var notificationRef = database.collection('notifications').doc();
                batch.set(notificationRef, {
                    id: notificationRef.id,
                    userId: registration.customerId,
                    title: title,
                    desc: body,
                    createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                    isSeen: false,
                    type: pushText.type,
                    distributionId: distributionId
                });
                count++;
            });
            await batch.commit();
            result.notified += count;
        }

        // The registration's token, or else the customer's; skip the push when both are empty
        var tokens = [];
        var customersWithoutToken = [];
        registrations.forEach(function (registration) {
            if (registration.fcmToken) {
                tokens.push(registration.fcmToken);
            } else if (registration.customerId) {
                customersWithoutToken.push(registration.customerId);
            }
        });
        var userSnapshots = await Promise.all(customersWithoutToken.map(function (customerId) {
            return database.collection('users').doc(customerId).get();
        }));
        userSnapshots.forEach(function (userSnapshot) {
            if (userSnapshot.exists && userSnapshot.data().fcmToken) {
                tokens.push(userSnapshot.data().fcmToken);
            }
        });

        for (var j = 0; j < tokens.length; j += 500) {
            var chunk = tokens.slice(j, j + 500);
            try {
                var response = await $.ajax({
                    method: 'POST',
                    url: "{{ route('sendBookingNotification') }}",
                    data: {
                        fcm: chunk,
                        title: title,
                        body: body,
                        type: pushText.type,
                        distributionId: distributionId,
                        _token: "{{ csrf_token() }}"
                    }
                });
                if (response && response.status) {
                    result.sent += response.sent || 0;
                    result.invalid += response.invalid || 0;
                    result.failed += response.failed || 0;
                } else {
                    result.failed += chunk.length;
                    errors.push((response && response.message) || 'Push failed');
                }
            } catch (error) {
                console.error(error);
                result.failed += chunk.length;
                errors.push('Push failed');
            }
        }

        return result;
    }

    // Text shown to the admin after a status change
    function distributionSummaryText(summary) {
        var text = "{{ trans('lang.distribution_summary_notified') }}"
            .replace(':count', summary.status.notified)
            .replace(':sent', summary.status.sent + summary.turn.sent)
            .replace(':invalid', summary.status.invalid + summary.turn.invalid)
            .replace(':failed', summary.status.failed + summary.turn.failed);
        if (summary.turn.notified > 0) {
            text += ' ' + "{{ trans('lang.distribution_summary_turn') }}".replace(':count', summary.turn.notified);
        }
        return text;
    }
</script>
