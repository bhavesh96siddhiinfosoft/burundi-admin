@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.fuel_receiving_history') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('owners') !!}">{{ trans('lang.owners') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('owners.edit', $id) }}">{{ trans('lang.edit_owner') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.fuel_receiving_history') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="alert alert-danger error_top d-none" role="alert"></div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark" id="receipt_station_name"></h5>
                <button type="button" class="btn btn-primary btn-sm" id="add_receipt_btn">
                    <i class="fa fa-plus"></i> {{ trans('lang.fuel_receipt_add_past') }}
                </button>
            </div>
            <div class="card-body p-3">
                <div class="row" id="receipt_totals"></div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ trans('lang.fuel_receipt_received_at') }}</th>
                                <th>{{ trans('lang.fuel_type') }}</th>
                                <th>{{ trans('lang.fuel_receipt_litres') }}</th>
                                <th>{{ trans('lang.fuel_receipt_source') }}</th>
                                <th>{{ trans('lang.note') }}</th>
                                <th>{{ trans('lang.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="receipt_list"></tbody>
                    </table>
                </div>
                <div class="form-text text-muted">
                    {{ trans('lang.fuel_receipt_page_help') }}
                </div>
            </div>
        </div>

        <div class="form-group col-12 text-center btm-btn">
            <a href="{{ route('owners.edit', $id) }}" class="btn btn-default"><i class="fa fa-undo"></i> {{ trans('lang.back') }}</a>
        </div>
    </div>
</div>

<!-- Add / correct a receipt -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receipt_modal_title"></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger receipt_error d-none" role="alert"></div>

                <div class="form-group" id="receipt_request_group">
                    <label class="control-label">{{ trans('lang.fuel_request') }}</label>
                    <select id="receipt_request" class="form-control"></select>
                    <div class="form-text text-muted">{{ trans('lang.fuel_receipt_request_help') }}</div>
                </div>
                <div class="form-group" id="receipt_fuel_group">
                    <label class="control-label">{{ trans('lang.fuel_type') }}<span class="required-field"></span></label>
                    <select id="receipt_fuel" class="form-control"></select>
                </div>
                <div class="form-group">
                    <label class="control-label">{{ trans('lang.fuel_receipt_litres') }}<span class="required-field"></span></label>
                    <input type="number" id="receipt_litres" class="form-control" min="0" step="any">
                </div>
                <div class="form-group">
                    <label class="control-label">{{ trans('lang.fuel_receipt_received_at') }}<span class="required-field"></span></label>
                    <input type="datetime-local" id="receipt_received_at" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">{{ trans('lang.note') }}</label>
                    <input type="text" id="receipt_note" class="form-control">
                </div>
                <div class="form-text text-muted" id="receipt_stock_help">{{ trans('lang.fuel_receipt_stock_help') }}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save_receipt_btn"><i class="fa fa-save"></i> {{ trans('lang.save') }}</button>
                <button type="button" class="btn btn-outline-dark" data-dismiss="modal">{{ trans('lang.close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('fuel_request.receipt_functions')
<script>
    var database = firebase.firestore();
    var ownerId = "{{ $id }}";
    var stationFuels = [];
    var receipts = [];
    var completedRequests = [];
    var editingReceiptId = null;

    $(document).ready(function () {
        $('.owner_menu').addClass('active');
        jQuery("#overlay").show();

        database.collection('users').doc(ownerId).get().then(function (snapshot) {
            if (!snapshot.exists) {
                showError("{{ trans('lang.fuel_receipt_station_not_found') }}");
                return;
            }
            var owner = snapshot.data();
            stationFuels = owner.fuel || [];
            $('#receipt_station_name').text((owner.fuelStationName || '') + (owner.fullName ? ' (' + owner.fullName + ')' : ''));

            var html = '';
            stationFuels.forEach(function (fuel) {
                html += '<option value="' + escapeHtml(fuel.fuelTypeId) + '">' + escapeHtml(fuel.name || '') + '</option>';
            });
            $('#receipt_fuel').html(html);
        });

        // Completed fuel requests, to link a past delivery to its request
        database.collection('fuel_request').where('ownerId', '==', ownerId).get().then(function (snapshot) {
            completedRequests = snapshot.docs.map(function (doc) {
                var data = doc.data();
                data.id = data.id || doc.id;
                return data;
            }).filter(function (request) {
                return request.status === 'completedByAdmin';
            });
        });

        database.collection('fuel_receipts').where('ownerId', '==', ownerId).onSnapshot(function (snapshot) {
            jQuery("#overlay").hide();
            receipts = snapshot.docs.map(function (doc) {
                var data = doc.data();
                data.id = data.id || doc.id;
                return data;
            });
            renderReceipts();
        }, function (error) {
            jQuery("#overlay").hide();
            showError(error.message);
        });

        $('#add_receipt_btn').on('click', openAddModal);

        $(document).on('click', '.edit-receipt', function () {
            openEditModal($(this).attr('data-id'));
        });

        $('#receipt_request').on('change', function () {
            var request = completedRequests.find(function (item) { return item.id === $('#receipt_request').val(); });
            if (request) {
                $('#receipt_fuel').val(request.fuelTypeId);
                $('#receipt_litres').val(request.fuelQuota || '');
                var requestDate = request.updatedAt || request.createdAt;
                if (requestDate && requestDate.toDate) {
                    $('#receipt_received_at').val(toInputDateTime(requestDate.toDate()));
                }
            }
        });

        $('#save_receipt_btn').on('click', saveReceipt);
    });

    function renderReceipts() {
        receipts.sort(function (a, b) {
            var aTime = a.receivedAt && a.receivedAt.toMillis ? a.receivedAt.toMillis() : 0;
            var bTime = b.receivedAt && b.receivedAt.toMillis ? b.receivedAt.toMillis() : 0;
            return bTime - aTime;
        });

        var totals = {};
        var html = '';
        receipts.forEach(function (receipt) {
            var fuelName = receipt.fuelTypeName || '';
            totals[fuelName] = (totals[fuelName] || 0) + (parseFloat(receipt.litres) || 0);

            var source = "{{ trans('lang.fuel_receipt_source_allocation') }}";
            if (receipt.source === 'fuel_request' && receipt.fuelRequestId) {
                source = '{{ trans("lang.fuel_request") }} <a href="{{ url("distribution-fuel-request") }}/' + encodeURIComponent(receipt.fuelRequestId) + '">#' + escapeHtml(receipt.fuelRequestId) + '</a>';
            }

            html += '<tr>' +
                '<td>' + escapeHtml(formatDate(receipt.receivedAt)) + '</td>' +
                '<td>' + escapeHtml(fuelName) + '</td>' +
                '<td>' + escapeHtml(receipt.litres) + ' {{ trans("lang.liter") }}</td>' +
                '<td>' + source + '</td>' +
                '<td>' + escapeHtml(receipt.note || '') + '</td>' +
                '<td class="action-btn"><a href="javascript:void(0)" class="edit-receipt" data-id="' + escapeHtml(receipt.id) + '" title="{{ trans("lang.edit") }}"><i class="fa fa-edit"></i></a></td>' +
                '</tr>';
        });

        if (html === '') {
            html = '<tr><td colspan="6" class="text-center">{{ trans("lang.fuel_receipt_none") }}</td></tr>';
        }
        $('#receipt_list').html(html);

        var totalsHtml = '';
        Object.keys(totals).forEach(function (fuelName) {
            totalsHtml += '<div class="col-md-3 mb-3"><div class="card card-body bg-light p-2">' +
                '<small class="text-muted">{{ trans("lang.fuel_receipt_total") }} ' + escapeHtml(fuelName) + '</small>' +
                '<strong>' + escapeHtml(Math.round(totals[fuelName] * 100) / 100) + ' {{ trans("lang.liter") }}</strong>' +
                '</div></div>';
        });
        $('#receipt_totals').html(totalsHtml);
    }

    function openAddModal() {
        editingReceiptId = null;
        $('.receipt_error').addClass('d-none');
        $('#receipt_modal_title').text("{{ trans('lang.fuel_receipt_add_past') }}");

        // Completed requests that have no receipt yet
        var linkedRequestIds = receipts.map(function (receipt) { return receipt.fuelRequestId; });
        var html = '<option value="">{{ trans("lang.fuel_receipt_no_request") }}</option>';
        completedRequests.forEach(function (request) {
            if (linkedRequestIds.indexOf(request.id) === -1) {
                html += '<option value="' + escapeHtml(request.id) + '">#' + escapeHtml(request.id) + ' - ' +
                    escapeHtml(request.fuelQuota || '') + ' {{ trans("lang.liter") }} - ' + escapeHtml(formatDate(request.createdAt)) + '</option>';
            }
        });
        $('#receipt_request').html(html).val('');
        $('#receipt_request_group, #receipt_fuel_group, #receipt_stock_help').removeClass('d-none');
        $('#receipt_litres').val('');
        $('#receipt_received_at').val(toInputDateTime(new Date()));
        $('#receipt_note').val('');
        $('#receiptModal').modal('show');
    }

    function openEditModal(receiptId) {
        var receipt = receipts.find(function (item) { return item.id === receiptId; });
        if (!receipt) return;
        editingReceiptId = receiptId;
        $('.receipt_error').addClass('d-none');
        $('#receipt_modal_title').text("{{ trans('lang.fuel_receipt_correct') }} - " + (receipt.fuelTypeName || ''));
        $('#receipt_request_group, #receipt_fuel_group').addClass('d-none');
        $('#receipt_stock_help').removeClass('d-none');
        $('#receipt_litres').val(receipt.litres);
        $('#receipt_received_at').val(receipt.receivedAt && receipt.receivedAt.toDate ? toInputDateTime(receipt.receivedAt.toDate()) : '');
        $('#receipt_note').val(receipt.note || '');
        $('#receiptModal').modal('show');
    }

    async function saveReceipt() {
        var litres = parseFloat($('#receipt_litres').val());
        var receivedAtValue = $('#receipt_received_at').val();
        var receivedAt = receivedAtValue ? new Date(receivedAtValue) : null;
        var note = $('#receipt_note').val().trim();

        if (isNaN(litres) || litres <= 0) {
            return showModalError("{{ trans('lang.fuel_receipt_litres_error') }}");
        }
        if (!receivedAt || isNaN(receivedAt.getTime()) || receivedAt.getTime() > Date.now()) {
            return showModalError("{{ trans('lang.fuel_receipt_received_at_error') }}");
        }

        jQuery("#overlay").show();
        try {
            if (editingReceiptId) {
                // Correction: litres, receivedAt and note only
                await database.collection('fuel_receipts').doc(editingReceiptId).update({
                    'litres': litres,
                    'receivedAt': firebase.firestore.Timestamp.fromDate(receivedAt),
                    'note': note !== '' ? note : null,
                    'updatedBy': '{{ auth()->id() }}',
                    'updatedAt': firebase.firestore.FieldValue.serverTimestamp()
                });
                logFirebaseActivity('#' + editingReceiptId + ' {{ trans("lang.fuel_receiving_history") }} {{ trans("lang.updated") }}');
            } else {
                var fuelTypeId = $('#receipt_fuel').val();
                var fuel = stationFuels.find(function (item) { return item.fuelTypeId === fuelTypeId; });
                if (!fuel) {
                    jQuery("#overlay").hide();
                    return showModalError("{{ trans('lang.fuel_receipt_fuel_error') }}");
                }
                var fuelRequestId = $('#receipt_request').val() || null;
                // A request's receipt is always request_<id>, so a request is never recorded twice
                if (fuelRequestId && await findFuelRequestReceipt(fuelRequestId)) {
                    jQuery("#overlay").hide();
                    return showModalError("{{ trans('lang.receive_fuel_already_added_short') }}");
                }
                var receiptRef = fuelRequestId
                    ? database.collection('fuel_receipts').doc(fuelRequestReceiptId(fuelRequestId))
                    : database.collection('fuel_receipts').doc();
                var receiptData = {
                    'id': receiptRef.id,
                    'ownerId': ownerId,
                    'fuelTypeId': fuel.fuelTypeId,
                    'fuelTypeName': fuel.name || '',
                    'litres': litres,
                    'receivedAt': firebase.firestore.Timestamp.fromDate(receivedAt),
                    'source': fuelRequestId ? 'fuel_request' : 'allocation',
                    'fuelRequestId': fuelRequestId,
                    'note': note !== '' ? note : null,
                    'createdBy': '{{ auth()->id() }}',
                    'createdAt': firebase.firestore.FieldValue.serverTimestamp()
                };
                await database.runTransaction(async function (transaction) {
                    var existing = await transaction.get(receiptRef);
                    if (existing.exists) {
                        throw new Error("{{ trans('lang.receive_fuel_already_added_short') }}");
                    }
                    transaction.set(receiptRef, receiptData);
                });
                logFirebaseActivity('#' + receiptRef.id + ' {{ trans("lang.fuel_receiving_history") }} {{ trans("lang.created") }}');
            }
            $('#receiptModal').modal('hide');
        } catch (error) {
            console.error(error);
            showModalError(error.message);
        }
        jQuery("#overlay").hide();
    }

    function toInputDateTime(date) {
        var pad = function (number) { return String(number).padStart(2, '0'); };
        return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) + 'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());
    }

    function formatDate(timestamp) {
        if (!timestamp || typeof timestamp.toDate !== 'function') return '';
        var date = timestamp.toDate();
        return date.toDateString() + ' ' + date.toLocaleTimeString('en-US');
    }

    function escapeHtml(value) {
        return String(value === undefined || value === null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showModalError(message) {
        $('.receipt_error').removeClass('d-none').html('<p class="mb-0">' + escapeHtml(message) + '</p>');
    }

    function showError(message) {
        jQuery("#overlay").hide();
        $('.error_top').removeClass('d-none').html('<p class="mb-0">' + escapeHtml(message) + '</p>');
    }
</script>
@endsection
