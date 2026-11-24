@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- ============================================================== -->
    <!-- Bread crumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-6 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.view_odometer_details') }}</h3>
        </div>
        <div class="col-md-6 align-self-center text-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="#" id="backToListLink">{{ trans('lang.driver_odometer_reading_history') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.view_odometer_details') }}</li>
            </ol>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Container Fluid -->
    <!-- ============================================================== -->
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body" id="odometerDetails">

                        <!-- HEADER -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="text-primary font-weight-bold mb-0">
                                <i class="fa fa-clock mr-2"></i> {{ trans('lang.odometer_info') }}
                            </h4>
                            <span id="createdAt" class="text-muted small">
                                <i class="fa fa-calendar mr-1"></i> -
                            </span>
                        </div>

                        <!-- ODOMETER INFO + IMAGE -->
                        <div class="row mb-5">
                            <div class="col-md-7">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <tbody>
                                            <tr>
                                                <th class="bg-light" width="35%">{{ trans('lang.driver_id') }}</th>
                                                <td id="driverId">-</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-light">{{ trans('lang.odometer_reading') }}</th>
                                                <td id="odometerReading">-</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-light">{{ trans('lang.last_reading') }}</th>
                                                <td id="lastReading">-</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-light">{{ trans('lang.vehicle_mileage') }}</th>
                                                <td id="vehicleMileage">-</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-light">{{ trans('lang.status') }}</th>
                                                <td id="status">-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-5 text-center">
                                <div class="border rounded p-2 bg-light">
                                    <img id="odometerPic" src="{{ asset('images/default_user.png') }}" 
                                         alt="Odometer Image" class="img-fluid rounded shadow-sm"
                                         style="max-height: 220px; object-fit: cover;">
                                    <p class="mt-2 text-muted small">
                                        <i class="fa fa-image mr-1"></i>{{ trans('lang.odometer_photo') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- DRIVER DETAILS -->
                        <h4 class="text-primary font-weight-bold mb-3 border-bottom pb-2">
                            <i class="fa fa-user mr-2"></i> {{ trans('lang.driver_details') }}
                        </h4>

                        <div class="table-responsive mb-5">
                            <table class="table table-sm table-bordered" id="driverDetails">
                                <tbody>
                                    <tr>
                                        <th class="bg-light" width="35%">{{ trans('lang.full_name') }}</th>
                                        <td id="fullName">-</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">{{ trans('lang.mobile_number') }}</th>
                                        <td id="mobileNumber">-</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">{{ trans('lang.available_fuel_quota') }}</th>
                                        <td id="availableFuelQuota">-</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">{{ trans('lang.vehicle_number') }}</th>
                                        <td id="vehicleNumber">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- FUEL TRANSACTION DETAILS (shown dynamically if available) -->
                        <div id="fuelTransactionSection" class="d-none">
                            <h4 class="text-primary font-weight-bold mb-3 border-bottom pb-2">
                                <i class="fa fa-gas-pump mr-2"></i> {{ trans('lang.fuel_transaction_details') }}
                            </h4>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="bg-light" width="35%">{{ trans('lang.owner_pump_name') }}</th>
                                            <td id="ownerPumpName">-</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">{{ trans('lang.owner_pump_address') }}</th>
                                            <td id="ownerPumpAddress">-</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">{{ trans('lang.transaction_date') }}</th>
                                            <td id="fuelTransactionDate">-</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">{{ trans('lang.fuel_quantity') }}</th>
                                            <td id="fuelQuantity">-</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">{{ trans('lang.fuel_type') }}</th>
                                            <td id="fuelType">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- BACK BUTTON -->
                        <div class="text-right mt-5">
                            <a href="#" id="backToListBtn" class="btn btn-secondary btn-rounded px-4 py-2 shadow-sm">
                                <i class="fa fa-arrow-left"></i> {{ trans('lang.back_to_list') }}
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script type="text/javascript">
var database = firebase.firestore();
var id = "{{ request()->route('id') }}"; // odometer record ID

$(document).ready(async function() {
    jQuery("#overlay").show();

    try {
        const doc = await database.collection('driver_odometer_reading_history').doc(id).get();

        if (!doc.exists) {
            $('#odometerDetails').html('<div class="alert alert-danger">{{ trans("lang.no_record_found") }}</div>');
            jQuery("#overlay").hide();
            return;
        }

        const data = doc.data();
        const driverId = data.driverId || '';

        // Populate odometer details
        $('#odometerPic').attr('src', data.odometerPic || '{{ asset("images/default_user.png") }}');
        $('#driverId').text(driverId || '-');
        $('#odometerReading').text(data.odometerReading || '-');
        $('#lastReading').text(data.lastReading || '-');
        $('#vehicleMileage').text(data.vehicleMillage || '-');
        $('#status').html(
            data.status === 'approved'
                ? '<span class="badge badge-success px-3 py-2">{{ trans("lang.approved") }}</span>'
                : data.status === 'pending'
                ? '<span class="badge badge-warning px-3 py-2">{{ trans("lang.pending") }}</span>'
                : '<span class="badge badge-danger px-3 py-2">{{ trans("lang.rejected") }}</span>'
        );
        $('#createdAt').html(
            data.createAt 
                ? `<i class="fa fa-calendar mr-1"></i> ${data.createAt.toDate().toLocaleDateString()} 
                   <small class="text-muted">${data.createAt.toDate().toLocaleTimeString()}</small>`
                : '-'
        );

        // Back link
        if (driverId) {
            const backUrl = `{{ route('customers.odometerReadingHistory', ['id' => ':driverId']) }}`.replace(':driverId', driverId);
            $('#backToListBtn').attr('href', backUrl);
            $('#backToListLink').attr('href', backUrl);
        }

        // Fetch driver details
        if (driverId) {
            const driverDoc = await database.collection('users').doc(driverId).get();
            if (driverDoc.exists) {
                const driver = driverDoc.data();
                $('#fullName').text(driver.fullName || '-');
                const phone = (driver.countryCode ? driver.countryCode + ' ' : '') + (driver.mobileNumber || '');
                $('#mobileNumber').text(phone || '-');
                $('#availableFuelQuota').text(driver.availableFuelQuota || '-');
                $('#vehicleNumber').text(driver.vehicleNumber || '-');
            } else {
                $('#driverDetails tbody').append('<tr><td colspan="2" class="text-danger text-center">{{ trans("lang.driver_not_found") }}</td></tr>');
            }
        }

        // 🔥 Fetch Fuel Transaction (if connected)
        const fuelQuery = await database.collection('fuel_transaction')
            .where('odometerReadingId', '==', id)
            .limit(1)
            .get();

        if (!fuelQuery.empty) {
            const fuelData = fuelQuery.docs[0].data();
            $('#fuelTransactionSection').removeClass('d-none');
            $('#ownerPumpName').text(fuelData.ownerPumpName || '-');
            $('#ownerPumpAddress').text(fuelData.ownerPumpAddress || '-');
            $('#fuelTransactionDate').text(
                fuelData.createAt ? fuelData.createAt.toDate().toLocaleString() : '-'
            );
            $('#fuelQuantity').text(fuelData.fuelInLiter || '-');
            $('#fuelType').text(fuelData.fuelType || '-');
        }

    } catch (error) {
        console.error("Error:", error);
        $('#odometerDetails').html('<div class="alert alert-danger">{{ trans("lang.error_occurred") }}</div>');
    }

    jQuery("#overlay").hide();
});
</script>
@endsection
