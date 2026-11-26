{{--
    Received fuel of a completed fuel request (F5).
    The receipt of a request always has the id request_<requestId>, so a request is never recorded twice.
--}}
<script>
    function fuelRequestReceiptId(requestId) {
        return 'request_' + requestId;
    }

    // The fuel_receipts record of a fuel request, or null
    async function findFuelRequestReceipt(requestId) {
        var receiptSnapshot = await database.collection('fuel_receipts').doc(fuelRequestReceiptId(requestId)).get();
        if (receiptSnapshot.exists) {
            return receiptSnapshot.data();
        }
        // Receipts written before the request_<id> naming
        var querySnapshot = await database.collection('fuel_receipts').where('fuelRequestId', '==', requestId).get();
        return querySnapshot.empty ? null : querySnapshot.docs[0].data();
    }

    /**
     * The station's fuel array after adding the received litres to one fuel:
     * current stock (availableFuelQuota) and total quota (totalFuelQuota) both go up by the litres.
     * Returns { error: 'fuel' } when the station doesn't have that fuel, and
     * { error: 'capacity' } when the stock would be more than the tank capacity.
     */
    function stationStockAfterReceipt(fuelArray, fuelTypeId, litres) {
        var index = fuelArray.findIndex(function (fuel) { return fuel.fuelTypeId === fuelTypeId; });
        if (index === -1) {
            return { error: 'fuel' };
        }
        var entry = fuelArray[index];
        var round = function (value) { return Math.round(value * 100) / 100; };
        var available = parseFloat(entry.availableFuelQuota) || 0;
        var total = parseFloat(entry.totalFuelQuota) || 0;
        var capacity = parseFloat(entry.fuelCapacity) || 0;
        var newAvailable = round(available + litres);
        var newTotal = round(total + litres);

        if (capacity > 0 && newAvailable > capacity) {
            return { error: 'capacity', capacity: capacity, available: available, newAvailable: newAvailable };
        }

        // Keep each value's type: the panel stores text, the apps may store numbers
        var sameType = function (original, value) {
            return typeof original === 'string' ? String(value) : value;
        };
        var updated = Object.assign({}, entry, {
            availableFuelQuota: sameType(entry.availableFuelQuota, newAvailable),
            totalFuelQuota: sameType(entry.totalFuelQuota, newTotal)
        });
        var fuel = fuelArray.slice();
        fuel[index] = updated;

        return {
            fuel: fuel,
            fuelName: entry.name || '',
            capacity: capacity,
            before: { available: entry.availableFuelQuota, total: entry.totalFuelQuota },
            after: { available: newAvailable, total: newTotal }
        };
    }

    /**
     * Adds the received litres of a completed fuel request to the station stock, in one transaction with:
     * the fuel_receipts record (Fuel Receiving History), the allocated date and the owner_reset_history row
     * (owner transaction report). Throws when the request isn't completed or was already added.
     */
    async function addReceivedFuel(requestId, litres, receivedAt, note) {
        var requestRef = database.collection('fuel_request').doc(requestId);
        var receiptRef = database.collection('fuel_receipts').doc(fuelRequestReceiptId(requestId));

        return database.runTransaction(async function (transaction) {
            var requestSnapshot = await transaction.get(requestRef);
            if (!requestSnapshot.exists) {
                throw new Error("{{ trans('lang.fuel_request_not_found') }}");
            }
            var request = requestSnapshot.data();
            if (request.status !== 'completedByAdmin') {
                throw new Error("{{ trans('lang.receive_fuel_not_completed') }}");
            }
            var receiptSnapshot = await transaction.get(receiptRef);
            if (receiptSnapshot.exists) {
                throw new Error("{{ trans('lang.receive_fuel_already_added_short') }}");
            }
            if (!request.ownerId) {
                throw new Error("{{ trans('lang.fuel_receipt_station_not_found') }}");
            }
            var ownerRef = database.collection('users').doc(request.ownerId);
            var ownerSnapshot = await transaction.get(ownerRef);
            if (!ownerSnapshot.exists) {
                throw new Error("{{ trans('lang.fuel_receipt_station_not_found') }}");
            }

            var stock = stationStockAfterReceipt(ownerSnapshot.data().fuel || [], request.fuelTypeId, litres);
            if (stock.error === 'fuel') {
                throw new Error("{{ trans('lang.fuel_receipt_fuel_error') }}");
            }
            if (stock.error === 'capacity') {
                throw new Error("{{ trans('lang.receive_fuel_capacity_error') }}"
                    .replace(':new', stock.newAvailable).replace(':capacity', stock.capacity));
            }

            var now = firebase.firestore.FieldValue.serverTimestamp();
            var ownerUpdate = { fuel: stock.fuel, fuelUpdatedAt: now };
            if (['Petrol', 'Diesel', 'Other'].indexOf(stock.fuelName) !== -1) {
                ownerUpdate['allocated' + stock.fuelName + 'Date'] = now;
            }
            transaction.update(ownerRef, ownerUpdate);

            transaction.set(receiptRef, {
                id: receiptRef.id,
                ownerId: request.ownerId,
                fuelTypeId: request.fuelTypeId,
                fuelTypeName: stock.fuelName,
                litres: litres,
                receivedAt: firebase.firestore.Timestamp.fromDate(receivedAt),
                source: 'fuel_request',
                fuelRequestId: requestId,
                note: note !== '' ? note : null,
                createdBy: '{{ auth()->id() }}',
                createdAt: now
            });

            var historyRef = database.collection('owner_reset_history').doc();
            transaction.set(historyRef, {
                id: historyRef.id,
                ownerId: request.ownerId,
                fuelType: stock.fuelName,
                lastAllocatedFuel: stock.before.total,
                lastRemainingFuel: stock.before.available,
                newAllocatedFuel: stock.after.total,
                createAt: firebase.firestore.Timestamp.fromDate(new Date())
            });

            return stock;
        });
    }
</script>
