@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Fuel Purchases</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item active">Fuel Purchases</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card pb-4">
            <div class="card-body">
                
                <!-- ✅ Fuel Type Circle Charts (Admin Only) -->
                @if(Auth::user()->id == 1)
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
                        <input type="date" id="filter_from" class="form-control" placeholder="From date">
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="filter_to" class="form-control" placeholder="To date">
                    </div>
                    <div class="col-md-3">
                        <select id="filter_fuel_type" class="form-control">
                            <option value="">All fuel types</option>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" id="filter_company" class="form-control" placeholder="Company name">
                    </div>
                </div>

                <div id="purchases_table_wrapper">
                    <table class="table table-bordered" id="purchases_table">
                        <thead>
                            <tr>
                                <th>Unique Id</th>
                                <th>Fuel Type</th>
                                <th>Quantity</th>
                                <th>Remaining Receive Qty</th>
                                <th>Rate</th>
                                <th>Company</th>
                                <th>Delivery Port</th>
                                <th>Paid GST</th>
                                <th>Paid Amount</th>
                                
                                @if(Auth::user()->id == 1)
                                    <th>Final Tax Approved</th>
                                @endif
                                <th>Purchase Date</th>
                                <th>Delivery Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="purchases_list"></tbody>
                    </table>
                </div>
            </div>

            @if(in_array('fuel.purchase.list', json_decode(@session('user_permissions'))))
                <div class="form-group col-12 text-center btm-btn">
                    <a href="{{ route('fuel.purchases.form', ['id' => '']) }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Create Purchase
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
<div class="modal fade" id="addInMainSettings" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add Into Main Fuel Quota</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="selected_purchase_id">
                <input type="hidden" id="selected_fuel_type">

                <div class="form-group">
                    <label>Fuel Type</label>
                    <input type="text" class="form-control" id="modal_fuel_type" readonly>
                </div>

                <div class="form-group">
                    <label>Total Quota (Before)</label>
                    <input type="text" class="form-control" id="modal_total_quota" readonly>
                </div>

                <div class="form-group">
                    <label>Available Quota (Before)</label>
                    <input type="text" class="form-control" id="modal_available_quota" readonly>
                </div>

                <div class="form-group">
                    <label>Add Fuel Quantity</label>
                    <input type="number" step="0.01" class="form-control" id="modal_add_qty" readonly>
                </div>

                <div class="form-group">
                    <label>Updated Total Quota</label>
                    <input type="text" class="form-control" id="modal_updated_total" readonly>
                </div>

                <div class="form-group">
                    <label>Updated Available Quota</label>
                    <input type="text" class="form-control" id="modal_updated_available" readonly>
                </div>

            </div>

            <div class="modal-footer">
                <!-- <button class="btn btn-success" id="btn_save_main_quota">Save</button> -->
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var database = firebase.firestore();
    var purchasesRef = database.collection('fuel_purchases');
    var currentUserId = "{{ Auth::user()->id }}"; 

    var fuelCharts = {};
    var fuelTypeData = {};
    var settingsRef = database.collection("settings").doc("fuelQuota");

    $(document).ready(function () {
        loadPurchases();
        $('#filter_from, #filter_to, #filter_fuel_type').on('change', loadPurchases);
        $('#filter_company').on('keyup', debounce(loadPurchases, 400));
    });

    function loadPurchases() {
        jQuery("#overlay").show();

        // reset chart data
        fuelTypeData = {};

        let query = purchasesRef.where("isDeleted", "==", false);

        if (currentUserId != 1) {
            query = query.where("auth_id", "==", Number(currentUserId));
        }

        query = query.orderBy('purchased_date', 'desc');

        query.get().then(function (snapshots) {
            var html = '';
            snapshots.docs.forEach(function (doc) {
                var data = doc.data();
                if (!passesFilters(data)) return;

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

                let remaining_quantity_to_receive = parseFloat(data.remaining_quantity_to_receive).toFixed(2);
                
                html += '<tr id="row_' + data.id + '">';
                html += '<td>#' + (data.id || '') + '</td>';
                html += '<td>' + (data.fuel_type || '') + '</td>';
                html += '<td>' + totalQty.toFixed(2) + '</td>';
                html += '<td>' + remaining_quantity_to_receive + '</td>';
                html += '<td>' + (data.rate || '') + '</td>';
                html += '<td>' + (data.company_name || '') + '</td>';
                html += '<td>' + (data.delivery_port || '') + '</td>';
                html += '<td>' + (data.paid_gst_amount || '') + '</td>';
                html += '<td>' + (data.paid_amount || '') + '</td>';
               

                @if(Auth::user()->id == 1)
                    html += '<td>' + (data.all_qty_tax_verified ? 
                        '<span class="badge badge-success">Yes</span>' : 
                        '<span class="badge badge-warning">No</span>') + '</td>';
                @endif

                html += '<td>' + (data.purchased_date || '') + ' ' + (data.purchased_time || '') + '</td>';
                html += '<td>' + (data.delivery_date || '') +'</td>';
                html += '<td>';
                let final_tax_approved = false;
                if(data.all_qty_tax_verified && data.all_qty_tax_verified == true){
                    final_tax_approved = true;
                }

                @if(in_array('fuel.purchase.list', json_decode(@session('user_permissions'))))
                    if(final_tax_approved == false){
                        html += '<a class="btn btn-sm btn-info" href="{{ url("fuel-purchases/save") }}/' + data.id + '"><i class="fa fa-edit"></i></a> ';
                        html += '<button class="btn btn-sm btn-danger" onclick="deletePurchase(\'' + data.id + '\')"><i class="fa fa-trash"></i></button> ';   
                    }
                @endif

                @if(in_array('fuel.receive.list', json_decode(@session('user_permissions'))))
                    if(Number(remaining_quantity_to_receive) > 0){
                        html += '<a class="btn btn-sm btn-success mr-1" href="{{ url("fuel-receivings/save") }}/?purchase_id=' + data.id + '"><i class="fa fa-truck"></i></a>';
                    }
                    html += '<a class="btn btn-sm btn-success mr-1" href="{{ url("fuel-receivings") }}/?purchase_id=' + data.id + '"><i class="fa fa-clock"></i></a>';
                @endif
                if (currentUserId == 1 &&  Number(remaining_quantity_to_receive) == 0) {
                     html += '<a href="javascript:void(0)" onclick="openFuelQuotaModal(\''+data.id+'\', \''+data.fuel_type+'\', \''+totalQty+'\')" class="btn btn-success"><i class="fa fa-plus"></i> Add In Main Fuel Quota</a>';
                }

                html += '</td></tr>';
            });

            $('#purchases_list').html(html);

            @if(Auth::user()->id == 1)
                renderFuelCharts();
            @endif

            jQuery("#overlay").hide();
        }).catch(function (err) {
            console.error(err);
            jQuery("#overlay").hide();
        });
    }
    
    function openFuelQuotaModal(id, type,quota) {
        $("#selected_purchase_id").val(id);
        $("#selected_fuel_type").val(type);

        // load settings
        settingsRef.get().then(doc => {
            let s = doc.data()[type];

            $("#modal_fuel_type").val(type.toUpperCase());
            $("#modal_total_quota").val(s.total);
            $("#modal_available_quota").val(s.available);
            let total = parseFloat(quota) + parseFloat(s.total);
            let available = parseFloat(quota) + parseFloat(s.available);
            $("#modal_add_qty").val(quota);
            $("#modal_updated_total").val(total);
            $("#modal_updated_available").val(available);

            $("#addInMainSettings").modal('show');
        });
    }


    $("#btn_save_main_quota").click(function () {
        if (!confirm("Are you sure you want to update MAIN FUEL QUOTA?")) return;
        let purchaseId = $("#selected_purchase_id").val();
        let fuelType = $("#selected_fuel_type").val();

        let newTotal = $("#modal_updated_total").val();
        let newAvailable = $("#modal_updated_available").val();

        jQuery("#overlay").show();

        // update settings first
        let updateObj = {};
        updateObj[fuelType + ".total"] = newTotal;
        updateObj[fuelType + ".available"] = newAvailable;

        settingsRef.update(updateObj)
            .then(() => {
                // mark purchase as updated
                return purchasesRef.doc(purchaseId).update({
                    main_fuel_quota_updated: true
                });
            })
            .then(() => {
                jQuery("#overlay").hide();
                $("#addInMainSettings").modal("hide");
                alert("Main Fuel Quota Updated Successfully!");
                loadPurchases(); // refresh table
            })
            .catch(err => {
                jQuery("#overlay").hide();
                console.error(err);
                alert("Failed to update.");
            });
    });



   function renderFuelCharts() {
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


    function passesFilters(data) {
        var from = $('#filter_from').val();
        var to = $('#filter_to').val();
        var fuel = $('#filter_fuel_type').val();
        var company = $('#filter_company').val().toLowerCase();

        if (fuel && data.fuel_type != fuel) return false;
        if (company && (!data.company_name || data.company_name.toLowerCase().indexOf(company) === -1)) return false;
        if (from && (!data.purchased_date || data.purchased_date < from)) return false;
        if (to && (!data.purchased_date || data.purchased_date > to)) return false;

        return true;
    }

    function deletePurchase(id) {
        if (!confirm('Are you sure you want to delete this purchase?')) return;
        jQuery("#overlay").show();
        purchasesRef.doc(id).update({ isDeleted: true }).then(function () {
            $('#row_' + id).remove();
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