@extends('layouts.app')

@section('content')
<style>
    #imageModal .modal-body {
        max-height: 80vh;
        overflow: auto;       /* ✅ scroll inside modal */
    }

    #imageModal img {
        display: block;
        margin: auto;
        transform-origin: center center;
        cursor: grab;         /* ✅ for better UX */
    }

</style>
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.fuel_request_status') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('fuel.request') }}">{{ trans('lang.fuel_request') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.update_status') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h4 class="card-title m-0 text-dark">{{ trans('lang.fuel_request_details') }}</h4>
            </div>

            <div class="card-body">
                <div class="alert alert-danger error_top d-none" role="alert"></div>

                <form id="requestForm">

    <!-- Request Details -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-dark">{{ trans('lang.fuel_request_details') }}</h5>
                </div>
                <div class="card-body p-3">

                    <ul class="list-group list-group-flush">

                        <li class="list-group-item d-flex justify-content-between">
                            <span class="font-weight-bold">{{ trans('lang.request_id') }}</span>
                            <span id="request_id" class="text-muted"></span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span class="font-weight-bold">{{ trans('lang.fuel_type') }}</span>
                            <span id="fuel_type" class="text-muted"></span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span class="font-weight-bold">{{ trans('lang.fuel_quota_ltr') }}</span>
                            <span id="fuel_quota" class="text-muted"></span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span class="font-weight-bold">{{ trans('lang.current_fuel_stock') }}</span>
                            <span id="current_fuel_stock" class="text-muted"></span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span class="font-weight-bold">{{ trans('lang.fuel_capacity') }}</span>
                            <span id="fuel_capacity" class="text-muted"></span>
                        </li>

                    </ul>

                </div>
            </div>
        </div>

        <!-- Station & Owner Details -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-dark">{{ trans('lang.station_details') }}</h5>
                </div>
                <div class="card-body p-3">

                    <ul class="list-group list-group-flush">

                        <li class="list-group-item d-flex justify-content-between">
                            <span class="font-weight-bold">{{ trans('lang.station_name') }}</span>
                            <span id="station_name" class="text-muted"></span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span class="font-weight-bold">{{ trans('lang.owner') }}</span>
                            <span id="owner_name" class="text-muted"></span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span class="font-weight-bold">{{ trans('lang.station_address') }}</span>
                            <span id="station_address" class="text-muted"></span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span class="font-weight-bold">{{ trans('lang.last_updated') }}</span>
                            <span id="last_updated" class="text-muted"></span>
                        </li>

                        <li class="list-group-item">
                            <label class="font-weight-bold">{{ trans('lang.status') }}</label>
                            <select id="status" class="form-control mt-2">
                                <option value="requestedByOwner" disabled>{{ trans('lang.requested_by_owner') }}</option>
                                <option value="approvedByAdmin">{{ trans('lang.approved_by_admin') }}</option>
                                <option value="paymentByOwner" disabled>{{ trans('lang.payment_by_owner') }}</option>
                                <option value="completedByAdmin">{{ trans('lang.completed_by_admin') }}</option>
                                <option value="rejectedByAdmin">{{ trans('lang.rejected_by_admin') }}</option>
                            </select>
                        </li>

                    </ul>

                </div>
            </div>
        </div>
    </div>

    <!-- Fuel Stock Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ trans('lang.fuel_stock_information') }}</h5>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ trans('lang.fuel_type') }}</th>
                            <th>{{ trans('lang.total_capacity_ltr') }}</th>
                            <th>{{ trans('lang.total_quota_ltr') }}</th>
                            <th>{{ trans('lang.available_quota_ltr') }}</th>
                            <th>{{ trans('lang.requested_amount_ltr') }}</th>
                        </tr>
                    </thead>
                    <tbody id="fuel_stock_table"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Linked distribution (advance booking) -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ trans('lang.distribution') }}</h5>
        </div>
        <div class="card-body p-3">
            <div id="distribution_container">
                <p class="text-muted mb-0">{{ trans('lang.fuel_request_no_distribution') }}</p>
            </div>
            <div class="form-text text-muted mt-2">
                {{ trans('lang.fuel_request_distribution_help') }}
            </div>
        </div>
    </div>

    <!-- Payment Proof -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ trans('lang.payment_proof') }}</h5>
        </div>
        <div class="card-body">
            <div id="proof_container"></div>
        </div>
    </div>

</form>

            </div>

            <div class="card-footer bg-light border-top text-center">
                <button type="button" class="btn btn-primary update_status_btn">
                    <i class="fa fa-save"></i> {{ trans('lang.update_status') }}
                </button>
                <a href="{{ route('fuel.request') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-undo"></i> {{ trans('lang.cancel') }}
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Image View Modal -->
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
              <img id="modalImage" src="" class="img-fluid" style="max-height: 80vh; transform: scale(1); transition: 0.2s;">
          </div>
          <div class="modal-footer">

              <button id="zoomIn" class="btn btn-primary">
                  <i class="fa fa-search-plus"></i> {{ trans('lang.zoom_in') }}
              </button>

              <button id="zoomOut" class="btn btn-secondary">
                  <i class="fa fa-search-minus"></i> {{ trans('lang.zoom_out') }}
              </button>

              <button id="resetZoom" class="btn btn-info">
                  <i class="fa fa-undo"></i> {{ trans('lang.reset_zoom') }}
              </button>

              <button class="btn btn-outline-dark" data-dismiss="modal">
                  {{ trans('lang.close') }}
              </button>
          </div>
      </div>
  </div>
</div>

@endsection

@section('scripts')
@include('distributions.status_functions')
<script>
    var database = firebase.firestore();
    var requestsRef = database.collection('fuel_request');
    var fuelTypesRef = database.collection('fuel_type');
    var usersRef = database.collection('users');
    var requestId = "{{ $id }}";
    var requestOwnerId = '';
    var linkedDistribution = null;

    $(document).ready(async function () {
        if (!requestId) return;

        jQuery("#overlay").show();
        try {
            const docSnap = await requestsRef.doc(requestId).get();
            if (!docSnap.exists) {
                showError("{{ trans('lang.fuel_request_not_found') }}");
                jQuery("#overlay").hide();
                return;
            }

            const data = docSnap.data();
            requestOwnerId = data.ownerId || '';
            $('#request_id').text(data.id);
            $('#fuel_quota').text((data.fuelQuota || '') + ' L');
            $('#status').val(data.status || '');

            // Fetch linked fuel type
            if (data.fuelTypeId) {
                const fuelSnap = await fuelTypesRef.doc(data.fuelTypeId).get();
                if (fuelSnap.exists) {
                    $('#fuel_type').text(fuelSnap.data().name || '');
                }
            }

            // Fetch owner info
            if (data.ownerId) {
                const userSnap = await usersRef.doc(data.ownerId).get();
                if (userSnap.exists) {
                    const u = userSnap.data();
                    $('#owner_name').text(u.fullName || '');
                    $('#station_name').text(u.fuelStationName || '');
                    $('#station_address').text(u.fuelStationAddress || '');
                    
                    // Populate fuel stock information
                    populateFuelStockInfo(u.fuel || [], data.fuelTypeId, data.fuelQuota);
                    
                    // Set last updated date
                    if (u.fuelUpdatedAt) {
                        $('#last_updated').text(formatDate(u.fuelUpdatedAt.toDate()));
                    }
                }
            }

            // Proof display (if payment proof uploaded)
            if (data.paymentRequestDetails && data.paymentRequestDetails.path) {
                $('#proof_container').html(`
                    <div class="text-center">
                        <img src="${data.paymentRequestDetails.path}" width="150" class="img-thumbnail mb-2">

                        <br>

                        <button type="button" class="btn btn-dark btn-sm viewImageBtn" 
                            data-img="${data.paymentRequestDetails.path}">
                            <i class="fa fa-eye"></i> {{ trans('lang.view_image') }}
                        </button>

                        <p class="mt-2 mb-0 text-muted">
                            ${data.paymentRequestDetails.name || ''} (${data.paymentRequestDetails.size || ''})
                        </p>
                    </div>
                `);
            } else {
                $('#proof_container').html('<p class="text-muted">{{ trans("lang.no_proof_uploaded") }}</p>');
            }


            updateStatusDropdown(data.status);
            await loadLinkedDistribution();
        } catch (err) {
            console.error(err);
            showError("{{ trans('lang.error_loading_request') }}: " + err.message);
        }
        jQuery("#overlay").hide();
    });

    function populateFuelStockInfo(fuelArray, requestedFuelTypeId, requestedQuota) {
        const tableBody = $('#fuel_stock_table');
        tableBody.empty();
        
        if (!fuelArray || fuelArray.length === 0) {
            tableBody.append('<tr><td colspan="5" class="text-center">{{ trans("lang.no_fuel_data") }}</td></tr>');
            return;
        }
        
        fuelArray.forEach(fuel => {
            const isRequestedType = fuel.fuelTypeId === requestedFuelTypeId;
            const requestedAmount = isRequestedType ? requestedQuota : '-';
            
            tableBody.append(`
                <tr ${isRequestedType ? 'class="table-info"' : ''}>
                    <td>${fuel.name || 'N/A'}</td>
                    <td>${fuel.fuelCapacity || '0'} L</td>
                    <td>${fuel.totalFuelQuota || '0'} L</td>
                    <td>${fuel.availableFuelQuota || '0'} L</td>
                    <td>${requestedAmount}</td>
                </tr>
            `);
            
            // Set current fuel stock and capacity for the requested fuel type
            if (isRequestedType) {
                $('#current_fuel_stock').text((fuel.availableFuelQuota || '0') + ' L');
                $('#fuel_capacity').text((fuel.fuelCapacity || '0') + ' L');
            }
        });
    }

    // U1: the distribution the owner published with this request (distributions.fuelRequestId)
    async function loadLinkedDistribution() {
        var snapshot = await database.collection('distributions').where('fuelRequestId', '==', requestId).get();
        if (snapshot.empty) {
            linkedDistribution = null;
            return;
        }
        var doc = snapshot.docs[0];
        linkedDistribution = doc.data();
        linkedDistribution.id = linkedDistribution.id || doc.id;

        var viewRoute = '{{ route("distributions.view", ":id") }}'.replace(':id', encodeURIComponent(linkedDistribution.id));
        $('#distribution_container').html(
            '<ul class="list-group list-group-flush">' +
                '<li class="list-group-item d-flex justify-content-between">' +
                    '<span class="font-weight-bold">{{ trans("lang.status") }}</span>' +
                    '<span>' + distributionStatusBadge(linkedDistribution.status) + '</span>' +
                '</li>' +
                '<li class="list-group-item d-flex justify-content-between">' +
                    '<span class="font-weight-bold">{{ trans("lang.distribution_registered_spots") }}</span>' +
                    '<span class="text-muted">' + escapeHtml(linkedDistribution.registeredCount || 0) + ' / ' + escapeHtml(linkedDistribution.totalSpots || 0) + '</span>' +
                '</li>' +
                '<li class="list-group-item text-right">' +
                    '<a href="' + viewRoute + '" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> {{ trans("lang.distribution_open") }}</a>' +
                '</li>' +
            '</ul>'
        );
    }

    function formatDate(date) {
        if (!date) return 'N/A';
        
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return date.toLocaleDateString('en-US', options);
    }

    function updateStatusDropdown(currentStatus) {
        // disable invalid transitions
        $("#status option").prop('disabled', false);

        if (currentStatus === 'requestedByOwner') {
            // admin can approve or reject
            $("#status option").prop('disabled', true);
            $("#status option[value='approvedByAdmin'], #status option[value='rejectedByAdmin']").prop('disabled', false);
        }
        else if (currentStatus === 'approvedByAdmin') {
            // waiting for owner payment proof - admin cannot change until payment done
            $("#status option").prop('disabled', true);
            $("#status option[value='approvedByAdmin']").prop('disabled', false);
        }
        else if (currentStatus === 'paymentByOwner') {
            // admin can mark completed or reject
            $("#status option").prop('disabled', true);
            $("#status option[value='completedByAdmin'], #status option[value='rejectedByAdmin']").prop('disabled', false);
        }
        else if (currentStatus === 'completedByAdmin' || currentStatus === 'rejectedByAdmin') {
            // final states - cannot change
            $("#status option").prop('disabled', true);
        }

        $('#status').val(currentStatus);
    }

    $('.update_status_btn').click(async function () {
        hideError();
        var newStatus = $('#status').val();

        if (!newStatus) {
            showError('{{ trans("lang.please_select_status") }}');
            return;
        }

        // U1: rejecting a request cancels its distribution, so its booked customers are told (F3)
        var cancelDistribution = false;
        if (newStatus === 'rejectedByAdmin' && linkedDistribution && distributionCanCancel(linkedDistribution.status)) {
            cancelDistribution = confirm("{{ trans('lang.fuel_request_reject_cancel_confirm') }}"
                .replace(':count', linkedDistribution.registeredCount || 0));
        }

        jQuery("#overlay").show();
        try {
            await requestsRef.doc(requestId).update({
                status: newStatus,
                updatedAt: firebase.firestore.FieldValue.serverTimestamp()
            });
        } catch (err) {
            console.error(err);
            jQuery("#overlay").hide();
            showError('{{ trans("lang.error_updating_status") }}: ' + err.message);
            return;
        }

        var message = '';
        if (cancelDistribution) {
            try {
                var summary = await changeDistributionStatus(linkedDistribution.id, 'cancelled');
                message = "{{ trans('lang.distribution_status_updated') }}".replace(':status', distributionStatusLabel('cancelled')) +
                    ' ' + distributionSummaryText(summary);
            } catch (err) {
                console.error(err);
                message = "{{ trans('lang.fuel_request_cancel_distribution_failed') }} " + err.message;
            }
        }

        jQuery("#overlay").hide();
        if (message !== '') {
            alert(message);
        }

        // F5: the delivery is recorded when the station's allocation is updated on the owner page
        if (newStatus === 'completedByAdmin' && requestOwnerId) {
            var completedMessage = "{{ trans('lang.fuel_request_completed_allocate_confirm') }}";
            if (linkedDistribution && distributionCanCancel(linkedDistribution.status)) {
                completedMessage = "{{ trans('lang.fuel_request_supplied_unlocked') }} " + completedMessage;
            }
            if (confirm(completedMessage)) {
                window.location.href = '{{ route("owners.edit", ":id") }}'.replace(':id', encodeURIComponent(requestOwnerId));
                return;
            }
        }
        window.location.href = "{{ route('fuel.request') }}";
    });

    // OPEN IMAGE MODAL
    $(document).on('click', '.viewImageBtn', function () {
        const imgPath = $(this).data('img');
        $("#modalImage").attr("src", imgPath).css("transform", "scale(1)");
        $("#imageModal").modal("show");
    });

    let zoom = 1;

    $("#zoomIn").click(function () {
        zoom += 0.2;
        $("#modalImage").css("transform", `scale(${zoom})`);
    });

    $("#zoomOut").click(function () {
        zoom -= 0.2;
        if (zoom < 0.2) zoom = 0.2; // never disappear
        $("#modalImage").css("transform", `scale(${zoom})`);
    });

    $("#resetZoom").click(function () {
        zoom = 1;
        $("#modalImage").css("transform", "scale(1)");
    });

    $("#imageModal").on("hidden.bs.modal", function () {
        zoom = 1;
        $("#modalImage").css("transform", "scale(1)");
    });



    function showError(message) {
        $('.error_top').removeClass('d-none').html('<p class="mb-0">' + message + '</p>');
        window.scrollTo(0, 0);
    }

    function hideError() {
        $('.error_top').addClass('d-none');
    }
</script>
@endsection