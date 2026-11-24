@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.tax_verifications') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.tax_verifications') }}</li>
                </ol>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h4 class="card-title m-0 text-dark">{{ trans('lang.tax_verifications_list') }}</h4>
                </div>
                
                <div class="card-body">
                    <!-- Filters Section -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-2">
                            <label class="form-label text-dark">{{ trans('lang.filter_by_status') }}</label>
                            <select id="t_filter_status" class="form-control">
                                <option value="">{{ trans('lang.all_status') }}</option>
                                <option value="approved">{{ trans('lang.approved') }}</option>
                                <option value="pending">{{ trans('lang.pending') }}</option>
                                <option value="rejected">{{ trans('lang.rejected') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label text-dark">{{ trans('lang.filter_by_company') }}</label>
                            <input type="text" id="t_filter_company" class="form-control" placeholder="{{ trans('lang.company_name_placeholder') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label text-dark">{{ trans('lang.filter_by_purchase') }}</label>
                            <input type="text" id="t_filter_purchase_id" class="form-control" placeholder="{{ trans('lang.purchase_id_placeholder') }}">
                        </div>
                          <div class="col-md-3 mb-2">
                            <label class="form-label text-dark">{{ trans('lang.filter_by_receive') }}</label>
                            <input type="text" id="t_filter_receive_id" class="form-control" placeholder="{{ trans('lang.receive_id_placeholder') }}">
                        </div>
                        <!-- <div class="col-md-3 mb-2 d-flex align-items-end">
                            <a href="{{ route('fuel.tax_verifications.form', ['id' => '']) }}" class="btn btn-primary w-100">
                                <i class="fa fa-plus"></i> {{ trans('lang.add_verification') }}
                            </a>
                        </div> -->
                    </div>

                    <!-- Table Section -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tax_table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ trans('lang.unique_id') }}</th>
                                    <th>{{ trans('lang.verified_date') }}</th>
                                    <th>{{ trans('lang.status') }}</th>
                                    <th>{{ trans('lang.purchase_id') }}</th>
                                    <th>{{ trans('lang.receiving_id') }}</th>
                                    <th>{{ trans('lang.company') }}</th>
                                    <th>{{ trans('lang.fuel_quantity') }}</th>
                                    <th>{{ trans('lang.rate') }}</th>
                                    <th>{{ trans('lang.total_price') }}</th>
                                    <th>{{ trans('lang.paid_gst') }}</th>
                                    <th>{{ trans('lang.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="tax_list"></tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div id="empty_state" class="text-center py-5 d-none">
                        <i class="fa fa-file-text-o fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">{{ trans('lang.no_tax_verifications') }}</h4>
                        <p class="text-muted">{{ trans('lang.no_tax_verifications_desc') }}</p>
                        
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    var database = firebase.firestore();
    var taxRef = database.collection('fuel_tax_verifications');
    var purchasesRef = database.collection('fuel_purchases');
    var receivingsRef = database.collection('fuel_receivings');
    var currentUserId = "{{ Auth::user()->id }}"; 

    $(document).ready(function () {
        loadTaxVerifications();
        $('#t_filter_status').on('change', loadTaxVerifications);
        $('#t_filter_company').on('keyup', debounce(loadTaxVerifications, 400));
        $('#t_filter_purchase_id').on('keyup', debounce(loadTaxVerifications, 400));
        $('#t_filter_receive_id').on('keyup', debounce(loadTaxVerifications, 400));
    });

    function getStatusBadge(status) {
        const statusClasses = {
            'approved': 'badge-success',
            'pending': 'badge-warning',
            'rejected': 'badge-danger'
        };
        const className = statusClasses[status] || 'badge-secondary';
        return '<span class="badge ' + className + '">' + status + '</span>';
    }

    async function loadTaxVerifications() {
        jQuery("#overlay").show();
        try {
            if (currentUserId != 1) {
                taxRef = taxRef.where("auth_id", "==", Number(currentUserId));
            }
            var snapshots = await taxRef.where('isDeleted', '==', false).orderBy('created_at', 'desc').get();
            var html = '';
            var hasRecords = false;

            for (const doc of snapshots.docs) {
                var data = doc.data();

                // fetch linked purchase & receiving (if available)
                var purchase = null;
                var receiving = null;
                if (data.purchase_id) {
                    const pSnap = await purchasesRef.where('id','==', data.purchase_id).limit(1).get();
                    if (!pSnap.empty) purchase = pSnap.docs[0].data();
                }
                if (data.receiving_id) {
                    const rSnap = await receivingsRef.where('id','==', data.receiving_id).limit(1).get();
                    if (!rSnap.empty) receiving = rSnap.docs[0].data();
                }

                // Filters
                var statusFilter = $('#t_filter_status').val();
                var companyFilter = $('#t_filter_company').val().toLowerCase();
                var purchaseFilter = $('#t_filter_purchase_id').val().toLowerCase();
                var receiveFilter = $('#t_filter_receive_id').val().toLowerCase();
                
                if (statusFilter && data.tax_status != statusFilter) continue;
                if (companyFilter) {
                    var cname = (purchase && purchase.company_name) ? purchase.company_name.toLowerCase() : '';
                    if (cname.indexOf(companyFilter) === -1) continue;
                }
                if (purchaseFilter) {
                    if (!data.purchase_id || data.purchase_id.toLowerCase().indexOf(purchaseFilter) === -1) continue;
                }
                if (purchaseFilter) {
                    if (!data.receiving_id || data.receiving_id.toLowerCase().indexOf(receiveFilter) === -1) continue;
                }

                hasRecords = true;

                var fuelQty = '';
                var rate = '';
                var totalPrice = '';
                var paidGst = '';
                if (purchase) {
                    fuelQty = (purchase.fuel_type || '') + ' / ' + (purchase.quantity || '');
                    rate = purchase.rate ? parseFloat(purchase.rate).toFixed(2) : '';
                    totalPrice = (purchase.quantity && purchase.rate) ? (parseFloat(purchase.quantity) * parseFloat(purchase.rate)).toFixed(2) : '';
                    paidGst = purchase.paid_gst_amount ? parseFloat(purchase.paid_gst_amount).toFixed(2) : '';
                }

                html += '<tr id="trow_' + data.id + '">';
                html += '<td>#' + (data.id || '') + '</td>';
                html += '<td>' + (data.verified_date || '') + '</td>';
                html += '<td>' + getStatusBadge(data.tax_status || 'pending') + '</td>';
                html += '<td><span class="font-weight-bold text-primary">#' + (data.purchase_id || '') + '</span></td>';
                html += '<td><span class="font-weight-bold text-info">#' + (data.receiving_id || '') + '</span></td>';

                html += '<td>' + (purchase ? (purchase.company_name || '') : '') + '</td>';
                html += '<td><small class="text-muted">' + fuelQty + '</small></td>';
                html += '<td>' + (rate ? '{{ trans("lang.currency_symbol") }}' + rate : '') + '</td>';
                html += '<td>' + (totalPrice ? '{{ trans("lang.currency_symbol") }}' + totalPrice : '') + '</td>';
                html += '<td>' + (paidGst ? '{{ trans("lang.currency_symbol") }}' + paidGst : '') + '</td>';
              
                html += '<td>';
                html += '<a class="btn btn-sm btn-info mr-1" href="{{ url("fuel/tax_verifications/save") }}/' + data.id + '" title="{{ trans("lang.edit") }}">';
                html += '<i class="fa fa-edit"></i>';
                html += '</a>';
                if(data.tax_status == "pending"){
                    html += '<button class="btn btn-sm btn-danger" onclick="deleteTax(\'' + data.id + '\')" title="{{ trans("lang.delete") }}">';
                    html += '<i class="fa fa-trash"></i>';
                    html += '</button>';
                }

                html += '</td></tr>';
            }
            
            $('#tax_list').html(html);
            
            // Show/hide empty state
            if (hasRecords) {
                $('#empty_state').addClass('d-none');
                $('#tax_table').removeClass('d-none');
            } else {
                $('#empty_state').removeClass('d-none');
                $('#tax_table').addClass('d-none');
            }
            
            jQuery("#overlay").hide();
        } catch (err) {
            console.error(err);
            jQuery("#overlay").hide();
            showAlert('{{ trans("lang.error_loading_data") }}', 'danger');
        }
    }

    function deleteTax(id) {
        if (!confirm('{{ trans("lang.confirm_delete_tax") }}')) return;
        jQuery("#overlay").show();
        taxRef.doc(id).update({ 
            isDeleted: true,
            deleted_at: firebase.firestore.FieldValue.serverTimestamp()
        }).then(function () {
            $('#trow_' + id).remove();
            jQuery("#overlay").hide();
            showAlert('{{ trans("lang.tax_deleted_success") }}', 'success');
        }).catch(function (err) {
            console.error(err);
            jQuery("#overlay").hide();
            showAlert('{{ trans("lang.error_deleting_tax") }}', 'danger');
        });
    }

    function showAlert(message, type) {
        var alertClass = 'alert-' + type;
        var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                       message +
                       '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                       '<span aria-hidden="true">&times;</span>' +
                       '</button></div>';
        $('.card-body').prepend(alertHtml);
        
        // Auto remove alert after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }

    function debounce(fn, wait) {
        var t;
        return function () {
            clearTimeout(t);
            t = setTimeout(fn, wait);
        };
    }
</script>
@endsection