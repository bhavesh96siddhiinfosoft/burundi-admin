@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ $id=='' ? trans('lang.create_purchase') : trans('lang.edit_purchase') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('fuel.purchases') }}">{{ trans('lang.fuel_purchases') }}</a></li>
                    <li class="breadcrumb-item active">{{ $id=='' ? trans('lang.create') : trans('lang.edit') }}</li>
                </ol>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h4 class="card-title m-0 text-dark">{{ $id=='' ? trans('lang.create_new_purchase') : trans('lang.edit_purchase_details') }}</h4>
                </div>
                
                <div class="card-body">

                    <div class="alert alert-danger error_top d-none" role="alert"></div>

                    <div class="row">
                        <div class="col-12">
                            <form id="purchaseForm" class="needs-validation" novalidate>
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-md-6">
                                        <div class="card mb-4 border">
                                            <div class="card-header bg-light py-2">
                                                <h5 class="mb-0 text-dark">{{ trans('lang.purchase_details') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <!-- Purchased date/time -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">
                                                        {{ trans('lang.purchased_date') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <input type="date" class="form-control purchased_date" id="purchased_date" required>
                                                        <div class="invalid-feedback">{{ trans('lang.error_purchased_date_required') }}</div>
                                                        <small class="form-text text-muted">{{ trans('lang.date_format_hint') }}</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.purchased_time') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="time" class="form-control purchased_time" id="purchased_time">
                                                    </div>
                                                </div>

                                                <!-- Fuel type -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">
                                                        {{ trans('lang.fuel_type') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <select class="form-control fuel_type" id="fuel_type" required>
                                                            <option value="">{{ trans('lang.select_fuel_type') }}</option>
                                                            <option value="petrol">{{ trans('lang.petrol') }}</option>
                                                            <option value="diesel">{{ trans('lang.diesel') }}</option>
                                                            <option value="other">{{ trans('lang.other') }}</option>
                                                        </select>
                                                        <div class="invalid-feedback">{{ trans('lang.error_fuel_type_required') }}</div>
                                                    </div>
                                                </div>

                                                <!-- Quantity / Rate -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">
                                                        {{ trans('lang.quantity') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" class="form-control quantity" id="quantity" required>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text bg-light">{{ trans('lang.liters') }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="invalid-feedback">{{ trans('lang.error_quantity_required') }}</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.rate') }}</label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-light">{{ trans('lang.currency_symbol') }}</span>
                                                            </div>
                                                            <input type="number" step="0.01" class="form-control rate" id="rate">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text bg-light">/{{ trans('lang.liter') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Purchased from (country) -->
                                                <div class="form-group row mb-0">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.purchased_from_country') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control purchased_from_country" id="purchased_from_country">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Column -->
                                    <div class="col-md-6">
                                        <div class="card mb-4 border">
                                            <div class="card-header bg-light py-2">
                                                <h5 class="mb-0 text-dark">{{ trans('lang.delivery_financial_details') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <!-- Delivery date -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.delivery_date') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="date" class="form-control delivery_date" id="delivery_date">
                                                    </div>
                                                </div>

                                                <!-- Paid GST, company, delivery port, paid amount, notes -->
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.paid_gst_amount') }}</label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-light">{{ trans('lang.currency_symbol') }}</span>
                                                            </div>
                                                            <input type="number" step="0.01" class="form-control paid_gst_amount" id="paid_gst_amount">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.company_name') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control company_name" id="company_name">
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.delivery_port') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control delivery_port" id="delivery_port">
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.paid_amount') }}</label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text bg-light">{{ trans('lang.currency_symbol') }}</span>
                                                            </div>
                                                            <input type="number" step="0.01" class="form-control paid_amount" id="paid_amount">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-0">
                                                    <label class="col-sm-4 col-form-label font-weight-bold text-dark">{{ trans('lang.notes_comments') }}</label>
                                                    <div class="col-sm-8">
                                                        <textarea class="form-control notes" id="notes" rows="3"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Calculation Summary -->
                                        <div class="card border-primary">
                                            <div class="card-header bg-primary text-white py-2">
                                                <h6 class="mb-0">{{ trans('lang.calculation_summary') }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="font-weight-bold">{{ trans('lang.total_cost') }}:</span>
                                                    <span id="totalCost" class="font-weight-bold">0.00 {{ trans('lang.currency') }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>{{ trans('lang.gst_amount') }}:</span>
                                                    <span id="gstAmount">0.00 {{ trans('lang.currency') }}</span>
                                                </div>
                                                <hr class="my-2">
                                                <div class="d-flex justify-content-between">
                                                    <span class="font-weight-bold">{{ trans('lang.net_amount') }}:</span>
                                                    <span id="netAmount" class="font-weight-bold text-primary">0.00 {{ trans('lang.currency') }}</span>
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
                        <button type="button" class="btn btn-primary create_purchase_btn">
                            <i class="fa fa-save"></i> {{ trans('lang.save') }}
                        </button>
                        <a href="{{ route('fuel.purchases') }}" class="btn btn-outline-secondary">
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
    var requestId = "{{ $id }}";
    var auth_user = @json(Auth::user());

    $(document).ready(function () {
        // Auto-calculate total cost when quantity or rate changes
        $('#quantity, #rate, #paid_gst_amount').on('input', calculateTotal);
        
        // Bootstrap form validation
        var forms = document.querySelector('.needs-validation');
        
        // Load data if editing
        if (requestId != '') {
            jQuery("#overlay").show();
            purchasesRef.where("id", "==", requestId).get().then(function (snapshots) {
                if (!snapshots.empty) {
                    var data = snapshots.docs[0].data();
                    $('#purchased_date').val(data.purchased_date || '');
                    $('#purchased_time').val(data.purchased_time || '');
                    $('#fuel_type').val(data.fuel_type || '');
                    $('#quantity').val(data.quantity || '');
                    $('#rate').val(data.rate || '');
                    $('#purchased_from_country').val(data.purchased_from_country || '');
                    $('#delivery_date').val(data.delivery_date || '');
                    $('#paid_gst_amount').val(data.paid_gst_amount || '');
                    $('#company_name').val(data.company_name || '');
                    $('#delivery_port').val(data.delivery_port || '');
                    $('#paid_amount').val(data.paid_amount || '');
                    $('#notes').val(data.notes || '');
                    
                    // Calculate initial totals
                    calculateTotal();
                }
                jQuery("#overlay").hide();
            }).catch(function (err) {
                console.error(err);
                jQuery("#overlay").hide();
                showError("{{ trans('lang.error_loading_data') }}");
            });
        }
    });

    function calculateTotal() {
        var quantity = parseFloat($('#quantity').val() || 0);
        var rate = parseFloat($('#rate').val() || 0);
        var gstAmount = parseFloat($('#paid_gst_amount').val() || 0);
        
        var totalCost = quantity * rate;
        var netAmount = totalCost + gstAmount;
        
        $('#totalCost').text(totalCost.toFixed(2) + " {{ trans('lang.currency') }}");
        $('#gstAmount').text(gstAmount.toFixed(2) + " {{ trans('lang.currency') }}");
        $('#netAmount').text(netAmount.toFixed(2) + " {{ trans('lang.currency') }}");
    }

    function showError(message) {
        $('.error_top').removeClass('d-none').html('<p class="mb-0">' + message + '</p>');
        window.scrollTo(0,0);
    }

    function hideError() {
        $('.error_top').addClass('d-none');
    }

    function validateForm() {
        var form = document.getElementById('purchaseForm');
        
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return false;
        }
        
        hideError();
        return true;
    }

    $('.create_purchase_btn').click(function () {
        
        if (!validateForm()) {
            return;
        }

        var purchased_date = $('#purchased_date').val();
        var purchased_time = $('#purchased_time').val();
        var fuel_type = $('#fuel_type').val();
        var quantity = parseFloat($('#quantity').val() || 0);
        var rate = parseFloat($('#rate').val() || 0);
        var purchased_from_country = $('#purchased_from_country').val();
        var delivery_date = $('#delivery_date').val();
        var paid_gst_amount = parseFloat($('#paid_gst_amount').val() || 0);
        var company_name = $('#company_name').val();
        var delivery_port = $('#delivery_port').val();
        var paid_amount = parseFloat($('#paid_amount').val() || 0);
        var notes = $('#notes').val();

        jQuery("#overlay").show();
        if (requestId == '') {
            var id = Math.random().toString(36).substr(2, 8).toUpperCase();
            purchasesRef.doc(id).set({
                id: id,
                auth_id: auth_user.id,
                auth_data: auth_user,
                purchased_date: purchased_date,
                purchased_time: purchased_time,
                fuel_type: fuel_type,
                quantity: quantity,
                rate: rate,
                purchased_from_country: purchased_from_country,
                delivery_date: delivery_date,
                paid_gst_amount: paid_gst_amount,
                company_name: company_name,
                delivery_port: delivery_port,
                paid_amount: paid_amount,
                notes: notes,
                isDeleted: false,
                remaining_quantity_to_receive: quantity,
                created_at: firebase.firestore.FieldValue.serverTimestamp()
            }).then(function () {
                jQuery("#overlay").hide();
                window.location.href = '{{ route("fuel.purchases") }}';
            }).catch(function (err) {
                jQuery("#overlay").hide();
                showError("{{ trans('lang.error_saving_data') }}: " + err.message);
            });
        } else {
            purchasesRef.doc(requestId).update({
                purchased_date: purchased_date,
                purchased_time: purchased_time,
                fuel_type: fuel_type,
                quantity: quantity,
                rate: rate,
                purchased_from_country: purchased_from_country,
                delivery_date: delivery_date,
                paid_gst_amount: paid_gst_amount,
                company_name: company_name,
                delivery_port: delivery_port,
                paid_amount: paid_amount,
                notes: notes,
                isDeleted: false,
                updated_at: firebase.firestore.FieldValue.serverTimestamp()
            }).then(function () {
                jQuery("#overlay").hide();
                window.location.href = '{{ route("fuel.purchases") }}';
            }).catch(function (err) {
                jQuery("#overlay").hide();
                showError("{{ trans('lang.error_updating_data') }}: " + err.message);
            });
        }
    });
</script>
@endsection