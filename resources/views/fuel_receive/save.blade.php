@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ $id=='' ? trans('lang.add_receiving') : trans('lang.edit_receiving') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('fuel.receivings') }}">{{ trans('lang.fuel_receivings') }}</a></li>
                    <li class="breadcrumb-item active">{{ $id=='' ? trans('lang.create') : trans('lang.edit') }}</li>
                </ol>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h4 class="card-title m-0 text-dark">{{ $id=='' ? trans('lang.create_new_receiving') : trans('lang.edit_receiving_details') }}</h4>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-danger error_top d-none" role="alert"></div>
                    
                    <div class="row">
                        <div class="col-12">
                            <form id="receivingForm">
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-md-6">
                                        <div class="card mb-4 border">
                                            <div class="card-header bg-light py-2">
                                                <h5 class="mb-0 text-dark">{{ trans('lang.receiving_information') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <!-- Purchase Selection -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">
                                                        {{ trans('lang.purchase') }}
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <select id="purchase_id" class="form-control purchase_id">
                                                            <option value="">{{ trans('lang.select_purchase') }}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Vehicle Details -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.plate_number') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" id="plate_number" class="form-control plate_number">
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.series_number') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" id="series_number" class="form-control series_number">
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.driver_name') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" id="driver_name" class="form-control driver_name">
                                                    </div>
                                                </div>

                                                <!-- Receive Date/Time -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">
                                                        {{ trans('lang.receive_date') }}
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <input type="date" id="receive_date" class="form-control receive_date">
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-0">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.receive_time') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="time" id="receive_time" class="form-control receive_time">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Column -->
                                    <div class="col-md-6">
                                        <div class="card mb-4 border">
                                            <div class="card-header bg-light py-2">
                                                <h5 class="mb-0 text-dark">{{ trans('lang.quantity_company_details') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <!-- Fuel Type & Company -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.fuel_type') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" id="fuel_type" class="form-control fuel_type bg-light" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.company_name') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" id="company_name" class="form-control company_name bg-light" readonly>
                                                    </div>
                                                </div>

                                                <!-- Quantity Information -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.total_quantity_expected') }}</label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" id="total_quantity" class="form-control total_quantity bg-light" readonly>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text bg-light">{{ trans('lang.liters') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Remaining Quantity -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.remaining_quantity') }}</label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" id="remaining_quantity" class="form-control remaining_quantity bg-light" readonly>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text bg-light">{{ trans('lang.liters') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">
                                                        {{ trans('lang.received_quantity') }}
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" id="received_quantity" class="form-control received_quantity">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text bg-light">{{ trans('lang.liters') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Transporter Company -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.transporter_company_name') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" id="transporter_company_name" class="form-control transporter_company_name">
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-0">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.exit_note') }}</label>
                                                    <div class="col-sm-8">
                                                        <textarea id="exit_note" class="form-control exit_note" rows="3"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light border-top">
                    <div class="form-group col-12 text-center mb-0">
                        <button type="button" class="btn btn-primary create_receiving_btn">
                            <i class="fa fa-save"></i> {{ trans('lang.save') }}
                        </button>
                        <a href="{{ route('fuel.receivings') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-undo"></i> {{ trans('lang.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    var database = firebase.firestore();
    var purchasesRef = database.collection('fuel_purchases');
    var receivingsRef = database.collection('fuel_receivings');
    var requestId = "{{ $id }}";
    var auth_user = @json(Auth::user());
    var selectedPurchaseIdData = '';
    const urlParams = new URLSearchParams(window.location.search);
    const purchaseIdFromUrl = urlParams.get('purchase_id');

    $(document).ready(function () {

        // populate purchase dropdown
        purchasesRef.where('isDeleted', '==', false).get().then(function (snapshots) {
            var html = '<option value="">{{ trans('lang.select_purchase') }}</option>';
            snapshots.docs.forEach(function (doc) {
                var d = doc.data();
                var name = (d.auth_data && d.auth_data.name) ? d.auth_data.name : 'UNKNOWN';
                var company = d.company_name || '';
                if (purchaseIdFromUrl && purchaseIdFromUrl === d.id) {
                    html += '<option selected value="' + d.id + '">' + d.id + ' - ' + name + ' - ' + company + '</option>';
                    $('#purchase_id').trigger("change");
                } else {
                    html += '<option value="' + d.id + '">' + d.id + ' - ' + name + ' - ' + company + '</option>';
                }
            });
            $('#purchase_id').html(html);
            if (purchaseIdFromUrl) {
                $('#purchase_id').trigger("change");
            }
        });
       
        // on purchase change, load details + remaining
        $('#purchase_id').on('change', function () {
            var pid = $(this).val();
            if (!pid) {
                $('#total_quantity, #fuel_type, #company_name, #remaining_quantity').val('');
                return;
            }

            purchasesRef.where('id', '==', pid).get().then(function (snap) {
                if (!snap.empty) {
                    var d = snap.docs[0].data();
                    selectedPurchaseIdData = d;
                    $('#total_quantity').val(d.quantity || '');
                    $('#fuel_type').val(d.fuel_type || '');
                    $('#company_name').val(d.company_name || '');
                    $('#remaining_quantity').val(d.remaining_quantity_to_receive || d.quantity);
                }
            });
        });

        // Load for edit
        if (requestId != '') {
            jQuery("#overlay").show();
            receivingsRef.doc(requestId).get().then(function (doc) {
                if (doc.exists) {
                    var data = doc.data();
                    $('#purchase_id').val(data.purchase_id).trigger('change');
                    $('#plate_number').val(data.plate_number || '');
                    $('#series_number').val(data.series_number || '');
                    $('#driver_name').val(data.driver_name || '');
                    $('#receive_date').val(data.receive_date || '');
                    $('#receive_time').val(data.receive_time || '');
                    $('#received_quantity').val(data.received_quantity || '');
                    $('#fuel_type').val(data.fuel_type || '');
                    $('#company_name').val(data.company_name || '');
                    $('#transporter_company_name').val(data.transporter_company_name || '');
                    $('#exit_note').val(data.exit_note || '');
                }
                jQuery("#overlay").hide();
            });
        }
    });

    function showError(message) {
        $('.error_top').removeClass('d-none').html('<p class="mb-0">' + message + '</p>');
        window.scrollTo(0,0);
    }

    function hideError() {
        $('.error_top').addClass('d-none');
    }

    $('.create_receiving_btn').click(async function () {
        hideError();

        var purchase_id = $('#purchase_id').val();
        var plate_number = $('#plate_number').val();
        var series_number = $('#series_number').val();
        var driver_name = $('#driver_name').val();
        var receive_date = $('#receive_date').val();
        var receive_time = $('#receive_time').val();
        var total_quantity = parseFloat($('#total_quantity').val() || 0);
        var received_quantity = parseFloat($('#received_quantity').val() || 0);
        var remaining_quantity = parseFloat($('#remaining_quantity').val() || 0);
        var fuel_type = $('#fuel_type').val();
        var company_name = $('#company_name').val();
        var transporter_company_name = $('#transporter_company_name').val();
        var exit_note = $('#exit_note').val();

        if (!purchase_id || !receive_date || isNaN(received_quantity)) {
            showError("{{ trans('lang.error_required_fields') }}");
            return;
        }

        // validation: cannot exceed remaining
        if (received_quantity > remaining_quantity) {
            showError("Received quantity cannot exceed remaining quantity (" + remaining_quantity + " liters).");
            return;
        }

        jQuery("#overlay").show();

        try {
            var id = requestId || Math.random().toString(36).substr(2, 8).toUpperCase();

            const receivingData = {
                id: id,
                auth_id: auth_user.id,
                auth_data: auth_user,
                purchase_id: purchase_id,
                purchase_data: selectedPurchaseIdData,
                plate_number: plate_number,
                series_number: series_number,
                driver_name: driver_name,
                receive_date: receive_date,
                receive_time: receive_time,
                total_quantity: total_quantity,
                received_quantity: received_quantity,
                fuel_type: fuel_type,
                company_name: company_name,
                transporter_company_name: transporter_company_name,
                exit_note: exit_note,
                isDeleted: false,
                updated_at: firebase.firestore.FieldValue.serverTimestamp()
            };

            if (requestId == '') {
                receivingData.created_at = firebase.firestore.FieldValue.serverTimestamp();
                await receivingsRef.doc(id).set(receivingData);
            } else {
                await receivingsRef.doc(id).update(receivingData);
            }

            // Update fuel_purchases (remaining + receiving list)
            const purchaseDocRef = purchasesRef.doc(purchase_id);
            const purchaseDoc = await purchaseDocRef.get();
            if (purchaseDoc.exists) {
                let pData = purchaseDoc.data();
                let currentReceivings = pData.receivings_data || [];
                let filteredReceivings = currentReceivings.filter(r => r.id !== id);
                filteredReceivings.push({
                    id: id,
                    receive_date: receive_date,
                    received_quantity: received_quantity,
                    driver_name: driver_name,
                    plate_number: plate_number
                });
                let totalReceivedNow = filteredReceivings.reduce((sum, r) => sum + parseFloat(r.received_quantity || 0), 0);
                let remainingNow = parseFloat(pData.quantity || 0) - totalReceivedNow;

                await purchaseDocRef.update({
                    receivings_data: filteredReceivings,
                    remaining_quantity_to_receive: remainingNow
                });
            }

            jQuery("#overlay").hide();
            window.location.href = '{{ route("fuel.receivings") }}';

        } catch (err) {
            console.error(err);
            jQuery("#overlay").hide();
            showError("{{ trans('lang.error_saving_data') }}: " + err.message);
        }
    });
</script>
@endsection
