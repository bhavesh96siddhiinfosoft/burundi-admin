@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Fuel Receivings</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">Fuel Receivings</li>
                </ol>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card pb-4">
                <div class="card-body">
                    @if(Auth::user()->id == 1 || Auth::user()->role_id == 14)
                    <div class="row mb-4" id="fuel_chart_container">
                        <div class="col-12">
                            <h4 class="mb-3 text-center">Fuel Type Summary</h4>
                            <div class="row" id="fuel_charts_row">
                                <!-- Charts will be dynamically inserted here -->
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="date" id="r_filter_from" class="form-control" placeholder="From date">
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="r_filter_to" class="form-control" placeholder="To date">
                        </div>
                        <div class="col-md-3">
                            <select id="r_filter_purchase" class="form-control">
                                <option value="">Select Purchase</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select id="r_filter_fuel_type" class="form-control">
                                <option value="">All fuel types</option>
                                <option value="petrol">Petrol</option>
                                <option value="diesel">Diesel</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <table class="table table-bordered" id="receivings_table">
                        <thead>
                            <tr>
                                <th>Unique Id</th>
                                <th>Purchase</th>
                                <th>Total Purchased Quantity</th>
                                <th>Received Quantity</th>
                                <th>Plate No</th>
                                <th>Driver</th>
                                <th>Transporter</th>
                                <th>Tax Verified</th>
                                <th>Receive Date / Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="receivings_list"></tbody>
                    </table>

                </div>
                @if(in_array('fuel.receive.list', json_decode(@session('user_permissions'))))
                <div class="form-group col-12 text-center btm-btn">
                    <a href="{{ route('fuel.receivings.form', ['id' => '']) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Receiving</a>
                </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var database = firebase.firestore();
    var receivingsRef = database.collection('fuel_receivings');
    var currentUserId = "{{ Auth::user()->id }}"; 
    var purchasesRef = database.collection('fuel_purchases');
    var purchaseIdFromUrl = "{{ request()->get('purchase_id') }}";
    var fuelTypeData = {};
    var fuelCharts = {};
    $(document).ready(function () {
        loadReceivings();
        loadPurchaseDropdown();
        $("#r_filter_purchase").on("change", loadReceivings);
        @if(Auth::user()->id == 1 || Auth::user()->role_id == 14)
            renderFuelCharts();
        @endif
        $('#r_filter_from, #r_filter_to, #r_filter_fuel_type').on('change', loadReceivings);
        $('#r_filter_company').on('keyup', debounce(loadReceivings, 400));
    });

    function loadReceivings() {
        jQuery("#overlay").show();
        if (currentUserId != 1) {
            receivingsRef = receivingsRef.where("auth_id", "==", Number(currentUserId));
        }
        receivingsRef.orderBy('receive_date', 'desc').get().then(function (snapshots) {
            var html = '';
            snapshots.docs.forEach(function (doc) {
                var data = doc.data();
                if (!receivingPassesFilters(data)) return;

                html += '<tr id="rrow_' + data.id + '">';
                html += '<td>#'+ data.id + '</td>';
                html += '<td>#'+ (data.purchase_data && data.purchase_data.id ? data.purchase_data.id : 'UNKNOWN') + ' - ' + (data.purchase_data && data.purchase_data.auth_data && data.purchase_data.auth_data.name ? data.purchase_data.auth_data.name : 'UNKNOWN') + '</td>';
                html += '<td>' + (data.total_quantity || '') + '</td>';
                html += '<td>' + (data.received_quantity || '') + '</td>';
                html += '<td>' + (data.plate_number || '') + '</td>';
                html += '<td>' + (data.driver_name || '') + '</td>';
                html += '<td>' + (data.transporter_company_name || '') + '</td>';
                let tax_verified = false;
                if (data.tax_verified && data.tax_verified == true){
                    tax_verified = true;
                    tax_verified_text = "YES";
                }
                html += '<td>' + (tax_verified ? 
                        '<span class="badge badge-success">Yes</span>' : 
                        '<span class="badge badge-warning">No</span>') + '</td>';
                html += '<td>' + (data.receive_date || '') + ' ' + (data.receive_time || '') + '</td>';
                html += '<td>';

                @if(in_array('fuel.receive.list', json_decode(@session('user_permissions'))))
                    if (tax_verified == false){
                        html += '<a class="btn btn-sm btn-info" href="{{ url("fuel-receivings/save") }}/' + data.id + '"><i class="fa fa-edit"></i></a> ';
                        html += '<button class="btn btn-sm btn-danger" onclick="deleteReceiving(\'' + data.id + '\')"><i class="fa fa-trash"></i></button> ';
                    }
                @endif

                @if(in_array('fuel.tax.list', json_decode(@session('user_permissions'))))
                    let tax_id = '';
                    if (tax_verified == true){
                        tax_id = data.tax_verification_id;
                        if (currentUserId == 1) {
                            html += '<a class="btn btn-sm btn-success" href="{{ url("fuel/tax_verifications/save") }}?receiving_id=' + data.id + '&purchase_id=' + data.purchase_id + '&tax_id=' + tax_id + '"><i class="mdi mdi-file-document"></i></a>';
                        }
                    }else {
                        html += '<a class="btn btn-sm btn-success" href="{{ url("fuel/tax_verifications/save") }}?receiving_id=' + data.id + '&purchase_id=' + data.purchase_id + '&tax_id=' + tax_id + '"><i class="mdi mdi-file-document"></i></a>';
                    }
                   
                @endif

                html += '</td></tr>';
            });
            $('#receivings_list').html(html);
            jQuery("#overlay").hide();
        }).catch(function (err) {
            console.error(err);
            jQuery("#overlay").hide();
        });
    }

    function loadPurchaseDropdown() {
        purchasesRef
            .where('isDeleted', '==', false)
            .get()
            .then(function (snapshots) {

                var html = '<option value="">Select Purchase</option>';

                snapshots.docs.forEach(function (doc) {
                    var d = doc.data();
                    var name = (d.auth_data && d.auth_data.name) ? d.auth_data.name : 'UNKNOWN';
                    var company = d.company_name || '';

                    if (purchaseIdFromUrl && purchaseIdFromUrl === d.id) {
                        html += '<option selected value="' + d.id + '">' +
                            d.id + ' - ' + name + ' - ' + company +
                            '</option>';
                    } else {
                        html += '<option value="' + d.id + '">' +
                            d.id + ' - ' + name + ' - ' + company +
                            '</option>';
                    }
                });

                $("#r_filter_purchase").html(html);

                if (purchaseIdFromUrl) {
                    $("#r_filter_purchase").trigger("change");
                }
            });
    }

    function receivingPassesFilters(data) {
        var from = $('#r_filter_from').val();
        var to = $('#r_filter_to').val();
        var fuel = $('#r_filter_fuel_type').val();
        var purchaseId = $('#r_filter_purchase').val();

        // Filter by Purchase
        if (purchaseId && data.purchase_id != purchaseId) return false;

        // If purchase_id is in URL, also force filter
        if (purchaseIdFromUrl && data.purchase_id != purchaseIdFromUrl) return false;

        if (fuel && data.fuel_type != fuel) return false;

        if (from) {
            if (!data.receive_date || data.receive_date < from) return false;
        }
        if (to) {
            if (!data.receive_date || data.receive_date > to) return false;
        }

        return true;
    }


    function deleteReceiving(id) {
        if (!confirm('Are you sure you want to delete this receiving?')) return;
        jQuery("#overlay").show();
        receivingsRef.doc(id).update({ isDeleted: true }).then(function () {
            $('#rrow_' + id).remove();
            jQuery("#overlay").hide();
        }).catch(function (err) {
            console.error(err);
            jQuery("#overlay").hide();
            alert('Delete failed');
        });
    }

    function debounce(fn, wait) {
        var t;
        return function () {
            clearTimeout(t);
            t = setTimeout(fn, wait);
        };
    }
    async function renderFuelCharts() {
        let query = purchasesRef.where("isDeleted", "==", false);
        await query.get().then(function (snapshots) {
            var html = '';
            snapshots.docs.forEach(function (doc) {
                var data = doc.data();
                var totalQty = parseFloat(data.quantity || 0);
                var totalReceived = 0;
                var totalTaxAprroved = 0;
                var remainingQty = parseFloat(data.remaining_quantity_to_receive);
                var isTaxVerified = data.all_qty_tax_verified || false;
                // totalReceived += totalQty - remainingQty;
                if (data.receivings_data && data.receivings_data.length > 0) {
                    data.receivings_data.forEach(r => {
                        totalReceived += parseFloat(r.received_quantity || 0);
                        if(r.tax_status && r.tax_status == 'approved'){
                            totalTaxAprroved += parseFloat(r.received_quantity || 0);
                        }
                    });
                }

                // ✅ Get fuel type
                let type = (data.fuel_type || 'other').toLowerCase();
                if (!['petrol', 'diesel', 'other'].includes(type)) type = 'other';

                // ✅ Initialize fuel type data if not exists
                if (!fuelTypeData[type]) {
                    fuelTypeData[type] = {
                        total: 0,
                        received: 0,
                        remaining: 0,
                        taxVerified: 0
                    };
                }
                console.log(fuelTypeData);

                fuelTypeData[type].total += Number(totalQty);
                fuelTypeData[type].received += Number(totalReceived);
                fuelTypeData[type].remaining += Number(remainingQty);
                if (isTaxVerified) {
                    fuelTypeData[type].taxVerified += Number(totalTaxAprroved);
                }
            });
        });

        const chartsContainer = document.getElementById('fuel_charts_row');
        chartsContainer.innerHTML = '';

        const chartColors = {
            received: '#28a745',
            remaining: '#ffc107',
            taxVerified: '#007bff'
        };

        Object.keys(fuelTypeData).forEach(fuelType => {
            const data = fuelTypeData[fuelType];
            const fuelTypeName = fuelType.charAt(0).toUpperCase() + fuelType.slice(1);

            // Percentages
            const receivedPercentage = data.total > 0 ? (data.received / data.total * 100).toFixed(1) : 0;
            const remainingPercentage = data.total > 0 ? (data.remaining / data.total * 100).toFixed(1) : 0;
            const taxVerifiedPercentage = data.total > 0 ? (data.taxVerified / data.total * 100).toFixed(1) : 0;

            // Proper subset breakdown
            const verified = data.taxVerified;
            const receivedNotVerified = data.received - data.taxVerified;
            const remaining = data.remaining;

            // Create card
            const colDiv = document.createElement('div');
            colDiv.className = 'col-md-4 mb-4';

            colDiv.innerHTML = `
                <div class="chart-card shadow-sm">
                    <h5 class="text-center chart-title">${fuelTypeName} Summary</h5>

                    <div class="chart-container">
                        <canvas id="chart_${fuelType}" width="270" height="270"></canvas>
                        <div class="chart-center-text">
                            <div class="total-value">${data.total.toFixed(2)}</div>
                            <div class="total-label">Total Liters</div>
                        </div>
                    </div>

                    <div class="chart-legend mt-3">
                        <div class="legend-item">
                            <span class="legend-color tax-verified"></span>
                            <span class="legend-text">Tax Approved: ${verified.toFixed(2)}L (${taxVerifiedPercentage}%)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color received"></span>
                            <span class="legend-text">Received (Tax unapproved): ${receivedNotVerified.toFixed(2)}L (${receivedPercentage}%)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color remaining"></span>
                            <span class="legend-text">Remaining: ${remaining.toFixed(2)}L (${remainingPercentage}%)</span>
                        </div>
                    </div>
                </div>
            `;

            chartsContainer.appendChild(colDiv);

            // Chart.js Data
            const chartData = {
                labels: ['Tax Approved', 'Received (Unverified)', 'Remaining'],
                datasets: [{
                    data: [verified, receivedNotVerified, remaining],
                    backgroundColor: [
                        chartColors.taxVerified,
                        chartColors.received,
                        chartColors.remaining
                    ],
                    borderWidth: 3,
                    borderColor: '#fff',
                }]
            };

            const ctx = document.getElementById(`chart_${fuelType}`).getContext('2d');

            if (fuelCharts[fuelType]) fuelCharts[fuelType].destroy();

            fuelCharts[fuelType] = new Chart(ctx, {
                type: 'doughnut',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label;
                                    const value = context.raw;
                                    const percent = data.total > 0 ? ((value / data.total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value.toFixed(2)} L (${percent}%)`;
                                }
                            }
                        }
                    },
                    animation: { animateRotate: true, animateScale: true }
                }
            });
        });

        if (Object.keys(fuelTypeData).length === 0) {
            chartsContainer.innerHTML = `
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> No fuel purchase data found for the selected filters.
                    </div>
                </div>
            `;
        }
    }
   
</script>
<style>
.chart-card {
    background: #fff;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
    transition: transform 0.3s ease;
}

.chart-card:hover {
    transform: translateY(-5px);
}

.chart-container {
    position: relative;
    height: 250px;
    margin: 0 auto;
}

.chart-title {
    font-weight: 600;
    color: #495057;
    margin-bottom: 20px;
    font-size: 1.2rem;
}

.chart-center-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.total-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #007bff;
    line-height: 1;
}

.total-label {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 5px;
}

.chart-legend {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
}

.legend-item {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.legend-item:last-child {
    margin-bottom: 0;
}

.legend-color {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    margin-right: 10px;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.legend-color.received {
    background-color: #28a745;
}

.legend-color.remaining {
    background-color: #ffc107;
}

.legend-color.tax-verified {
    background-color: #007bff;
}

.legend-text {
    font-size: 0.9rem;
    color: #495057;
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .col-md-4 {
        margin-bottom: 25px;
    }
    
    .chart-container {
        height: 220px;
    }
    
    .total-value {
        font-size: 1.5rem;
    }
}
</style>
@endsection
