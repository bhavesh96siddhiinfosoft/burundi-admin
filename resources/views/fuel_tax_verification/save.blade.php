@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ $id=='' ? trans('lang.add_tax_verification') : trans('lang.edit_tax_verification') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('fuel.tax_verifications') }}">{{ trans('lang.tax_verifications') }}</a></li>
                    <li class="breadcrumb-item active">{{ $id=='' ? trans('lang.create') : trans('lang.edit') }}</li>
                </ol>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h4 class="card-title m-0 text-dark">{{ $id=='' ? trans('lang.create_new_tax_verification') : trans('lang.edit_tax_verification_details') }}</h4>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-danger error_top d-none" role="alert"></div>
                    
                    <div class="row">
                        <div class="col-12">
                            <form id="taxVerificationForm">
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-md-6">
                                        <!-- Reference Information -->
                                        <div class="card mb-4 border">
                                            <div class="card-header bg-light py-2">
                                                <h5 class="mb-0 text-dark">{{ trans('lang.reference_information') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <!-- Purchase Information (Read-only) -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">
                                                        {{ trans('lang.purchase') }}
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" id="t_purchase_display" class="form-control bg-light" readonly>
                                                        <input type="hidden" id="t_purchase_id" class="form-control t_purchase_id">
                                                    </div>
                                                </div>

                                                <!-- Receiving Information (Read-only) -->
                                                <div class="form-group row mb-0">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">
                                                        {{ trans('lang.receiving') }}
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" id="t_receiving_display" class="form-control bg-light" readonly>
                                                        <input type="hidden" id="t_receiving_id" class="form-control t_receiving_id">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Purchase Details -->
                                        <div class="card mb-4 border">
                                            <div class="card-header bg-light py-2">
                                                <h5 class="mb-0 text-dark">{{ trans('lang.purchase_details') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group row mb-2">
                                                    <label class="col-sm-4 col-form-label text-dark">{{ trans('lang.company') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" id="p_company_name" class="form-control bg-light" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-2">
                                                    <label class="col-sm-4 col-form-label text-dark">{{ trans('lang.fuel_type') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" id="p_fuel_type" class="form-control bg-light" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-2">
                                                    <label class="col-sm-4 col-form-label text-dark">{{ trans('lang.quantity') }}</label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group">
                                                            <input type="text" id="p_quantity" class="form-control bg-light" readonly>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text bg-light">{{ trans('lang.liters') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-2">
                                                    <label class="col-sm-4 col-form-label text-dark">{{ trans('lang.rate') }}</label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-light">{{ trans('lang.currency_symbol') }}</span>
                                                            </div>
                                                            <input type="text" id="p_rate" class="form-control bg-light" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-2">
                                                    <label class="col-sm-4 col-form-label text-dark">{{ trans('lang.total_price') }}</label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-light">{{ trans('lang.currency_symbol') }}</span>
                                                            </div>
                                                            <input type="text" id="p_total_price" class="form-control bg-light" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-0">
                                                    <label class="col-sm-4 col-form-label text-dark">{{ trans('lang.paid_gst') }}</label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-light">{{ trans('lang.currency_symbol') }}</span>
                                                            </div>
                                                            <input type="text" id="p_paid_gst" class="form-control bg-light" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Column -->
                                    <div class="col-md-6">
                                        <!-- Receiving Details -->
                                        <div class="card mb-4 border">
                                            <div class="card-header bg-light py-2">
                                                <h5 class="mb-0 text-dark">{{ trans('lang.receiving_details') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group row mb-2">
                                                    <label class="col-sm-4 col-form-label text-dark">{{ trans('lang.plate_number') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" id="r_plate_number" class="form-control bg-light" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-2">
                                                    <label class="col-sm-4 col-form-label text-dark">{{ trans('lang.driver_name') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" id="r_driver_name" class="form-control bg-light" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-0">
                                                    <label class="col-sm-4 col-form-label text-dark">{{ trans('lang.received_quantity') }}</label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group">
                                                            <input type="text" id="r_received_quantity" class="form-control bg-light" readonly>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text bg-light">{{ trans('lang.liters') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tax Verification Details -->
                                        <div class="card mb-4 border">
                                            <div class="card-header bg-light py-2">
                                                <h5 class="mb-0 text-dark">{{ trans('lang.tax_verification_details') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <!-- Verified Date -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">
                                                        {{ trans('lang.verified_date') }}
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <input type="date" id="verified_date" class="form-control verified_date">
                                                    </div>
                                                </div>

                                                <!-- Tax Status -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">
                                                        {{ trans('lang.tax_status') }}
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <select id="tax_status" class="form-control tax_status">
                                                            <option value="pending">{{ trans('lang.pending') }}</option>
                                                            <option value="approved">{{ trans('lang.approved') }}</option>
                                                            <option value="rejected">{{ trans('lang.rejected') }}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Notes -->
                                                <div class="form-group row mb-0">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">
                                                        {{ trans('lang.notes') }}
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <textarea id="tax_notes" class="form-control tax_notes" rows="4" placeholder="{{ trans('lang.notes_placeholder') }}"></textarea>
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
                        <button type="button" class="btn btn-primary create_tax_btn">
                            <i class="fa fa-save"></i> {{ trans('lang.save') }}
                        </button>
                        <a href="{{ route('fuel.tax_verifications') }}" class="btn btn-outline-secondary">
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
    var taxRef = database.collection('fuel_tax_verifications');
    var requestId = "{{ $id }}";
    var auth_user = @json(Auth::user());
    var urlParams = new URLSearchParams(window.location.search);
    var receiving_q = urlParams.get('receiving_id');
    var purchase_q = urlParams.get('purchase_id');
    var tax_id = urlParams.get('tax_id');

    async function loadPurchaseDetails(pid) {
        if (!pid) {
            $('#p_company_name,#p_fuel_type,#p_quantity,#p_rate,#p_total_price,#p_paid_gst').val('');
            return;
        }
        var snap = await purchasesRef.where('id','==', pid).limit(1).get();
        if (!snap.empty) {
            var p = snap.docs[0].data();
            $('#p_company_name').val(p.company_name || '');
            $('#p_fuel_type').val(p.fuel_type || '');
            $('#p_quantity').val(p.quantity || '');
            $('#p_rate').val(p.rate || '');
            var total = (p.quantity && p.rate) ? (parseFloat(p.quantity) * parseFloat(p.rate)).toFixed(2) : '';
            $('#p_total_price').val(total);
            $('#p_paid_gst').val(p.paid_gst_amount || '');
            // Set purchase display text
            $('#t_purchase_display').val(p.id + ' - ' + (p.company_name || ''));
        }
    }

    async function loadReceivingDetails(rid) {
        if (!rid) {
            $('#r_plate_number,#r_driver_name,#r_received_quantity').val('');
            return;
        }
        var snap = await receivingsRef.where('id','==', rid).limit(1).get();
        if (!snap.empty) {
            var r = snap.docs[0].data();
            $('#r_plate_number').val(r.plate_number || '');
            $('#r_driver_name').val(r.driver_name || '');
            $('#r_received_quantity').val(r.received_quantity || '');
            // Set receiving display text
            $('#t_receiving_display').val(r.id + ' - ' + (r.plate_number || ''));
        }
    }

    function showError(message) {
        $('.error_top').removeClass('d-none').html('<p class="mb-0">' + message + '</p>');
        window.scrollTo(0,0);
    }

    function hideError() {
        $('.error_top').addClass('d-none');
    }

    $(document).ready(async function () {
        // Set the purchase and receiving IDs from URL parameters
        if (purchase_q) {
            $('#t_purchase_id').val(purchase_q);
            await loadPurchaseDetails(purchase_q);
        }
        
        if (receiving_q) {
            $('#t_receiving_id').val(receiving_q);
            await loadReceivingDetails(receiving_q);
        }

        if (requestId == '' && tax_id && tax_id != ''){
            requestId = tax_id;
        }
        
        if (requestId != '') {
            jQuery("#overlay").show();
            try {
                var snap = await taxRef.where("id","==",requestId).get();
                if (!snap.empty) {
                    var data = snap.docs[0].data();
                    $('#t_purchase_id').val(data.purchase_id || '');
                    $('#t_receiving_id').val(data.receiving_id || '');
                    
                    // Load and display purchase and receiving details
                    if (data.purchase_id) {
                        await loadPurchaseDetails(data.purchase_id);
                    }
                    if (data.receiving_id) {
                        await loadReceivingDetails(data.receiving_id);
                    }
                    
                    $('#verified_date').val(data.verified_date || '');
                    if (data.tax_status == 'approved') {
                        $('#tax_status').prop('disabled', true);
                        $('#verified_date').prop('readonly', true);
                        $('#tax_notes').prop('readonly', true);
                        $('.create_tax_btn').hide();
                    }
                    $('#tax_status').val(data.tax_status || 'pending');
                    $('#tax_notes').val(data.tax_notes || '');
                }
            } catch (err) {
                console.error(err);
                showError("{{ trans('lang.error_loading_data') }}");
            }
            jQuery("#overlay").hide();
        } else {
            // new: sensible default verified date = today
            var today = new Date().toISOString().slice(0,10);
            $('#verified_date').val(today);
        }
    });

    $('.create_tax_btn').click(async function () {
        var purchase_id = $('#t_purchase_id').val();
        var receiving_id = $('#t_receiving_id').val();
        var verified_date = $('#verified_date').val();
        var tax_status = $('#tax_status').val();
        var tax_notes = $('#tax_notes').val();

        if (!purchase_id || !receiving_id || !verified_date) {
            showError("{{ trans('lang.error_required_fields') }}");
            return;
        }

        jQuery("#overlay").show();
        try {
            let id = requestId || Math.random().toString(36).substr(2, 8).toUpperCase();
            let taxData = {
                id: id,
                auth_id: auth_user.id,
                auth_data: auth_user,
                purchase_id: purchase_id,
                receiving_id: receiving_id,
                verified_date: verified_date,
                tax_status: tax_status,
                tax_notes: tax_notes || '',
                isDeleted: false,
                updated_at: firebase.firestore.FieldValue.serverTimestamp()
            };

            if (requestId == '') {
                taxData.created_at = firebase.firestore.FieldValue.serverTimestamp();
                await taxRef.doc(id).set(taxData);
            } else {
                await taxRef.doc(id).update(taxData);
            }

            let purchaseDocRef = purchasesRef.doc(purchase_id);
            let purchaseDoc = await purchaseDocRef.get();
            if (purchaseDoc.exists) {
                let p = purchaseDoc.data();
                let receivings_data = p.receivings_data || [];
                let taxList = p.receivings_tax_verifications || [];
                if (receivings_data && receivings_data.length > 0) {
                    receivings_data = receivings_data.map(r => {
                        if (r.id == receiving_id) {
                            return {
                                ...r,
                                tax_status: tax_status,      
                                verified_date: verified_date 
                            };
                        }
                        return r;
                    });
                }

                // Get all receivings for this purchase
                let recSnap = await receivingsRef
                    .where('purchase_id', '==', purchase_id)
                    .where('isDeleted', '==', false)
                    .get();

                let recSelectedSnap = await receivingsRef
                    .doc(receiving_id)
                    .update({
                    tax_verified: true,
                    tax_verification_id: id
                })

                let allReceivedQty = 0;
                let fullyReceived = false;
                let allTaxVerified = true;

                recSnap.forEach(r => {
                    let d = r.data();
                    allReceivedQty += parseFloat(d.received_quantity || 0);
                    let match = taxList.find(t => t.receiving_id === d.id);
                    if (!match || match.tax_status !== 'approved') {
                        allTaxVerified = false;
                    }
                });

                if (parseFloat(p.quantity || 0) <= allReceivedQty) {
                    fullyReceived = true;
                }

                let allDone = (fullyReceived && allTaxVerified);

                await purchaseDocRef.update({
                    receivings_data: receivings_data,
                    all_qty_tax_verified: allDone
                });
            }

            jQuery("#overlay").hide();
            if (tax_id && tax_id != ''){
                window.location.href = '{{ route("fuel.receivings") }}';
            } else {
                window.location.href = '{{ route("fuel.tax_verifications") }}';
            }
        } catch (err) {
            console.error(err);
            jQuery("#overlay").hide();
            showError("{{ trans('lang.error_saving_data') }}: " + err.message);
        }
    });
</script>
@endsection