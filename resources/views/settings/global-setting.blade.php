@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">

            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.app_setting_global') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.app_setting_global') }}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div id="data-table_processing" class="dataTables_processing panel panel-default"
                        style="display: none;">{{ trans('lang.processing') }}</div>
                    <div class="error_top" style="display:none"></div>

                    <div class="row restaurant_payout_create">

                        <div class="restaurant_payout_create-inner">


                            <fieldset>
                                <legend>{{ trans('lang.contact_us') }}</legend>

                                <div class="form-group row width-50">
                                    <label class="col-4 control-label">{{ trans('lang.email') }}<span
                                            class="required-field"></span></label>
                                    <div class="col-7">
                                        <input type="text" name="contact_us_email" id="contact_us_email"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-4 control-label">{{ trans('lang.contact_us_phone') }}<span
                                            class="required-field"></span></label>
                                    <div class="col-7">
                                        <input type="text" name="contact_us_phone_number" id="contact_us_phone_number"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-4 control-label">{{ trans('lang.address') }}<span
                                            class="required-field"></span></label>
                                    <div class="col-7">
                                        <textarea name="contact_us_address" id="contact_us_address" class="form-control"></textarea>
                                    </div>
                                </div>


                            </fieldset>

                            <fieldset>

                                <legend><i class="mr-3 mdi mdi-comment-alert"></i>{{ trans('lang.notification_setting') }}
                                </legend>

                                <div class="form-group row width-100">
                                    <label class="col-5 control-label">{{ trans('lang.sender_id') }}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control" id="sender_id">
                                    </div>
                                    <div class="form-text pl-3 text-muted">
                                        {{ trans('lang.notification_sender_id_help') }}
                                    </div>
                                </div>

                                <div class="form-group row width-100">
                                    <label class="col-3 control-label">{{ trans('lang.upload_json_file') }}</label>
                                    <input type="file" class="col-7 pb-2" onChange="handleUploadJsonFile(event)">
                                    <div id="uploding_json_file"></div>
                                    <div id="uploded_json_file"></div>
                                    <div class="form-text pl-3 text-muted">
                                        {{ trans('lang.notification_json_file_help') }}
                                    </div>
                                </div>

                            </fieldset>
                            <fieldset>

                                <legend><i class="mr-3 mdi mdi-comment-alert"></i>{{ trans('lang.app_version') }}
                                </legend>

                                <div class="form-group row width-100">
                                    <label class="col-5 control-label">{{ trans('lang.customer_app_version') }}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control" id="customer_app_version">
                                    </div>
                                    <div class="form-text pl-3 text-muted">
                                        {{ trans('lang.version_help') }}
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-5 control-label">{{ trans('lang.customer_app_link') }}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control" id="customer_app_link">
                                    </div>
                                    <div class="form-text pl-3 text-muted">
                                        {{ trans('lang.app_link_help') }}
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-5 control-label">{{ trans('lang.owner_app_version') }}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control" id="owner_app_version">
                                    </div>
                                    <div class="form-text pl-3 text-muted">
                                        {{ trans('lang.version_help') }}
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-5 control-label">{{ trans('lang.owner_app_link') }}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control" id="owner_app_link">
                                    </div>
                                    <div class="form-text pl-3 text-muted">
                                        {{ trans('lang.app_link_help') }}
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-5 control-label">{{ trans('lang.employee_app_version') }}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control" id="employee_app_version">
                                    </div>
                                    <div class="form-text pl-3 text-muted">
                                        {{ trans('lang.version_help') }}
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-5 control-label">{{ trans('lang.employee_app_link') }}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control" id="employee_app_link">
                                    </div>
                                    <div class="form-text pl-3 text-muted">
                                        {{ trans('lang.app_link_help') }}
                                    </div>
                                </div>


                            </fieldset>
                            @if (in_array('reset.fuel.quota.customer', json_decode(@session('user_permissions'))))
                                <fieldset>
                                    <legend>{{ trans('lang.reset__customer_fuel_quota') }}</legend>
                                    <div class="form-group row width-100">
                                        <label class="col-3 control-label">{{ trans('lang.vehicle_type') }}<span
                                                class="required-field"></span></label>
                                        <div class="col-7">
                                            <select id="vehicleType" class="form-control">
                                                <option value="" disabled selected>
                                                    {{ trans('lang.select_vehicle_type') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row width-100">
                                        <label class="col-3 control-label">{{ trans('lang.fuel_type') }}<span
                                                class="required-field"></span></label>
                                        <div class="col-7">
                                            <select id="fuelType" class="form-control">
                                                <option value="" disabled selected>
                                                    {{ trans('lang.select_fuel_type') }}</option>
                                                <option value="All">{{ trans('lang.all') }}</option>
                                                <option value="HrvJenq9ZDnpBcakC18A">{{ trans('lang.petrol') }}</option>
                                                <option value="dxPwNWqebokjRiKl0Nhq">{{ trans('lang.diesel') }}</option>
                                                <option value="5KpCEAdAKG4lIuPSAzu0">{{ trans('lang.other') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row width-100 d-flex">
                                        <p class="text-danger font-weight-bold col-12">
                                            {{ trans('lang.click_btn_to_reset') }}</p>

                                        <div class="col-6 ml-auto">
                                            <a name="reset-fuel-btn"
                                                class="btn btn-primary reset-fuel-btn text-white">{{ trans('lang.reset_fuel_quota') }}</a>
                                        </div>
                                        <div class="col-6">
                                            <a name="pending-user-reset-fuel-btn" style="display: none;"
                                                class="btn btn-primary pending-user-reset-fuel-btn text-white"></a>
                                        </div>

                                    </div>

                                </fieldset>
                            @endif

                            <!-- Other Settings -->
                            <fieldset>

                                <legend>
                                    <i class="mr-3 mdi mdi-comment-alert"></i>{{ trans('lang.other_settings') }}
                                </legend>
                                
                                <div class="form-group row width-50">
                                    <div class="form-check">
                                        <input type="checkbox" class="allow_for_all_petrol_pump" id="allow_for_all_petrol_pump">
                                        <label class="col-3 control-label" for="allow_for_all_petrol_pump">{{trans('lang.all_for_all_petrolpump_setting')}}</label>
                                    </div>
                                    <div class="form-text pl-3 text-muted">
                                        {{ trans('lang.all_for_all_petrolpump_setting_help') }}
                                    </div>
                                </div>
                            </fieldset>

                            <!-- Booking Settings (settings/booking_setting) -->
                            <fieldset>

                                <legend>
                                    <i class="mr-3 mdi mdi-calendar-clock"></i>{{ trans('lang.booking_settings') }}
                                </legend>

                                <div class="form-group row width-50">
                                    <label class="col-5 control-label">{{ trans('lang.booking_turn_threshold') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control" id="booking_turn_threshold" min="1" max="100" step="1">
                                        <div class="form-text text-muted">
                                            {{ trans('lang.booking_turn_threshold_help') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-5 control-label">{{ trans('lang.booking_eta_window_minutes') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control" id="booking_eta_window_minutes" min="0" max="120" step="1">
                                        <div class="form-text text-muted">
                                            {{ trans('lang.booking_eta_window_minutes_help') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-5 control-label">{{ trans('lang.booking_default_rate_per_hour') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control" id="booking_default_rate_per_hour" min="0" max="500" step="any">
                                        <div class="form-text text-muted">
                                            {{ trans('lang.booking_default_rate_per_hour_help') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-5 control-label">{{ trans('lang.booking_qr_refresh_seconds') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control" id="booking_qr_refresh_seconds" min="15" max="600" step="1">
                                        <div class="form-text text-muted">
                                            {{ trans('lang.booking_qr_refresh_seconds_help') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-5 control-label">{{ trans('lang.booking_qr_max_age_minutes') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control" id="booking_qr_max_age_minutes" min="1" step="1">
                                        <div class="form-text text-muted">
                                            {{ trans('lang.booking_qr_max_age_minutes_help') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text pl-3 text-muted">
                                    {{ trans('lang.booking_settings_apply_help') }}
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group col-12 text-center btm-btn">
                        <button type="button" class="btn btn-primary save_global_settings_btn"><i
                                class="fa fa-save"></i>
                            {{ trans('lang.save') }}</button>
                        <a href="{{ url('/dashboard') }}" class="btn btn-default"><i
                                class="fa fa-undo"></i>{{ trans('lang.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script>
            var serviceJsonFile = '';
            var storageRef = firebase.storage().ref('images');
            var storage = firebase.storage();

            var database = firebase.firestore();

            var contactUsRef = database.collection('settings').doc("contactUs");
            var refNotificationSetting = database.collection('settings').doc("notification_setting");
            var versionRef = database.collection('settings').doc("update_app");
            var otherSettingsRef = database.collection('settings').doc("other_settings");
            var bookingSettingRef = database.collection('settings').doc("booking_setting");

            // Defaults used by the customer and employee apps when settings/booking_setting is missing
            var bookingSettingDefaults = {
                turnThreshold: 15,
                etaWindowMinutes: 15,
                defaultRatePerHour: 20,
                qrRefreshSeconds: 60,
                qrMaxAgeMinutes: 10
            };

            var refVeh = database.collection('vehicle_type').orderBy('name','asc');
            var vehicleType = '';
            var fuelType = '';
            const today = new Date().toISOString().split('T')[0];

            $(document).ready(function() {

                contactUsRef.get().then(async function(contactusSnap) {
                    var contactData = contactusSnap.data();
                    if (contactData != undefined) {
                        $("#contact_us_email").val(contactData.email);
                        $("#contact_us_phone_number").val(contactData.phone);
                        $("#contact_us_address").text(contactData.address);
                    }
                });
                versionRef.get().then(async function(snapshots) {
                    var versionData = snapshots.data();

                    if (versionData != undefined) {
                        $("#customer_app_version").val(versionData.customer_app.version);
                        $("#customer_app_link").val(versionData.customer_app.link);
                        $("#owner_app_version").val(versionData.owner_app.version);
                        $("#owner_app_link").val(versionData.owner_app.link);
                        $("#employee_app_version").val(versionData.employee_app.version);
                        $("#employee_app_link").val(versionData.employee_app.link);

                    }
                });

                refNotificationSetting.get().then(async function(snapshots) {
                    var notificationData = snapshots.data();
                    if (notificationData == undefined) {
                        database.collection('settings').doc('notification_setting').set({});
                    } else {
                        if (notificationData.senderId != '' && notificationData.senderId != null) {
                            $('#sender_id').val(notificationData.senderId);
                        }
                        if (notificationData.serviceJson != '' && notificationData.serviceJson != null) {
                            $('#uploded_json_file').html("<a href='" + notificationData.serviceJson +
                                "' class='btn-link pl-3' target='_blank'>See Uploaded File</a>");
                            serviceJsonFile = notificationData.serviceJson;
                        }
                    }

                });

                otherSettingsRef.get().then(function(snapshot) {

                    var data = snapshot.data();

                    if (data != undefined) {
                        $("#allow_for_all_petrol_pump").prop(
                            "checked",
                            data.allow_for_all_petrol_pump === true
                        );
                    } else {
                        // Create document with default value
                        otherSettingsRef.set({
                            allow_for_all_petrol_pump: false
                        });

                        $("#allow_for_all_petrol_pump").prop("checked", false);
                    }

                });

                bookingSettingRef.get().then(function(snapshot) {
                    var data = snapshot.exists ? snapshot.data() : {};
                    var value = function(key) {
                        return (data[key] !== undefined && data[key] !== null && data[key] !== '') ? data[key] : bookingSettingDefaults[key];
                    };
                    $("#booking_turn_threshold").val(value('turnThreshold'));
                    $("#booking_eta_window_minutes").val(value('etaWindowMinutes'));
                    $("#booking_default_rate_per_hour").val(value('defaultRatePerHour'));
                    $("#booking_qr_refresh_seconds").val(value('qrRefreshSeconds'));
                    $("#booking_qr_max_age_minutes").val(value('qrMaxAgeMinutes'));
                });

                $(".save_global_settings_btn").click(async function() {


                    var email = $("#contact_us_email").val();
                    var phone = $("#contact_us_phone_number").val();
                    var address = $("#contact_us_address").val();
                    var senderId = $("#sender_id").val();
                    var customer_app_version = $("#customer_app_version").val();
                    var customer_app_link = $("#customer_app_link").val();
                    var owner_app_version = $("#owner_app_version").val();
                    var owner_app_link = $("#owner_app_link").val();
                    var employee_app_version = $("#employee_app_version").val();
                    var employee_app_link = $("#employee_app_link").val();
                    var isAllowForAllPetrolPump = $("#allow_for_all_petrol_pump").is(":checked");
                    var bookingSettingError = validateBookingSetting();

                    if (email == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{ trans('lang.contact_us_email_help') }}</p>");
                        window.scrollTo(0, 0);
                    } else if (phone == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{ trans('lang.contact_us_phone_help') }}</p>");
                        window.scrollTo(0, 0);
                    } else if (address == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{ trans('lang.contact_us_address_help') }}</p>");
                        window.scrollTo(0, 0);
                    } else if (senderId == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{ trans('lang.notification_sender_id_error') }}</p>");
                        window.scrollTo(0, 0);
                    } else if (serviceJsonFile == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append(
                            "<p>{{ trans('lang.notification_service_json_error') }}</p>");
                        window.scrollTo(0, 0);
                    } else if (customer_app_version == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{ trans('lang.customer_version_help') }}</p>");
                        window.scrollTo(0, 0);
                    } else if (customer_app_link == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{ trans('lang.customer_link_help') }}</p>");
                        window.scrollTo(0, 0);
                    } else if (owner_app_version == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{ trans('lang.owner_version_help') }}</p>");
                        window.scrollTo(0, 0);
                    } else if (owner_app_link == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{ trans('lang.owner_link_help') }}</p>");
                        window.scrollTo(0, 0);
                    } else if (employee_app_version == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{ trans('lang.employee_version_help') }}</p>");
                        window.scrollTo(0, 0);
                    } else if (employee_app_link == '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{ trans('lang.employee_link_help') }}</p>");
                        window.scrollTo(0, 0);
                    } else if (bookingSettingError != '') {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>" + bookingSettingError + "</p>");
                        window.scrollTo(0, 0);
                    } else {

                        jQuery("#overlay").show();
                        await database.collection('settings').doc("notification_setting").update({
                            'senderId': senderId,
                            'serviceJson': serviceJsonFile,
                        });
                        var customer = {
                            'version': customer_app_version,
                            'link': customer_app_link
                        }
                        var owner = {
                            'version': owner_app_version,
                            'link': owner_app_link
                        }
                        var employee = {
                            'version': employee_app_version,
                            'link': employee_app_link
                        }

                        await database.collection('settings').doc("update_app").update({
                            'customer_app': customer,
                            'employee_app': employee,
                            'owner_app': owner
                        });

                        await otherSettingsRef.set({
                            allow_for_all_petrol_pump: isAllowForAllPetrolPump
                        }, { merge: true });

                        await bookingSettingRef.set({
                            turnThreshold: parseInt($("#booking_turn_threshold").val(), 10),
                            etaWindowMinutes: parseInt($("#booking_eta_window_minutes").val(), 10),
                            defaultRatePerHour: parseFloat($("#booking_default_rate_per_hour").val()),
                            qrRefreshSeconds: parseInt($("#booking_qr_refresh_seconds").val(), 10),
                            qrMaxAgeMinutes: parseInt($("#booking_qr_max_age_minutes").val(), 10)
                        }, { merge: true });

                        await database.collection('settings').doc("contactUs").update({
                            'email': email,
                            'phone': phone,
                            'address': address,
                        }).then(function(result) {
                            window.location.href = '{{ url('settings/global-setting') }}';
                        });
                        logFirebaseActivity('{{trans("lang.app_setting_global")}} {{trans("lang.updated")}}');

                    }
                })
                refVeh.get().then(async function(snapshot) {
                    await snapshot.docs.forEach(async doc => {
                        var data = doc.data();
                       
                        $('#vehicleType').append($("<option></option>")
                            .attr("value", data.id)
                            .text(data.name));
                    })
                })
            })
            
            $('#vehicleType').on('change', async function() {
                $('#fuelType').val('');
            });
            
            let mismatchedQuotaCustomerIds = [];
            let mismatchedDateCustomerIds = [];
            let mergedCustomerIds = [];
            var vehData = '';

            $('#fuelType').on('change', async function() {
            
                jQuery("#overlay").show();
                vehicleType = $('#vehicleType').val();
                fuelType = $('#fuelType').val();
            
                var customers = 0;
                if (vehicleType == '' || vehicleType == null) {
                    alert('select vehicle type');
                    jQuery("#overlay").hide();
                    return false;
                }
            
                if (fuelType == '' || fuelType == null) {
                    alert('select fuel type');
                    jQuery("#overlay").hide();
                    return false;
                }
            
                const today = new Date().toISOString().split('T')[0];

                var lastupdatedDate = firebase.firestore.FieldValue.serverTimestamp();

                await database.collection('vehicle_type').where('id', '==', vehicleType).get().then(function(
                    snapshot) {
                    vehData = snapshot.docs[0].data();
                })
            
                var allCustomers = database.collection('users')
                    .where('role', '==', 'customer')
                    .where('vehicleId', '==', vehicleType);

                var refHistory = database.collection("customer_fuel_reset_history")
                    .where("date", ">=", firebase.firestore.Timestamp.fromDate(new Date(`${today}T00:00:00`)))
                    .where("date", "<", firebase.firestore.Timestamp.fromDate(new Date(`${today}T23:59:59`)))
                    .where("vehicleId", '==', vehicleType);

                var refLastHistory = database.collection("customer_fuel_reset_history").where("vehicleId", '==', vehicleType);

                if (fuelType !== 'All') {

                    allCustomers = allCustomers.where('fuelTypeId', '==', fuelType);
                    refHistory = refHistory.where('fuelTypeId', '==', fuelType);
                    refLastHistory = refLastHistory.where('fuelTypeId', '==', fuelType);

                    if (fuelType == '5KpCEAdAKG4lIuPSAzu0') {
                        lastupdatedDate = vehData.lastUpdatedOther;
                    } else if (fuelType == 'HrvJenq9ZDnpBcakC18A') {
                        lastupdatedDate = vehData.lastUpdatedPetrol;
                    } else if (fuelType == 'dxPwNWqebokjRiKl0Nhq') {
                        lastupdatedDate = vehData.lastUpdatedDiesel;
                    }

                    var startDate = lastupdatedDate?.toDate();
                    var endDate = new Date();

                } else {
                    const lastUpdatedDiesel = vehData.lastUpdatedDiesel?.toDate() || null;
                    const lastUpdatedPetrol = vehData.lastUpdatedPetrol?.toDate() || null;
                    const lastUpdatedOther = vehData.lastUpdatedOther?.toDate() || null;

                    const dates = [lastUpdatedDiesel, lastUpdatedPetrol, lastUpdatedOther].filter(date => date !== null);
                    var startDate = new Date(Math.min(...dates));
                    var endDate = new Date();
                }

                refLastHistory = refLastHistory
                    .where("date", ">=", firebase.firestore.Timestamp.fromDate(new Date(
                        `${startDate.toISOString().split('T')[0]}T00:00:00`)))
                    .where("date", "<", firebase.firestore.Timestamp.fromDate(new Date(
                        `${endDate.toISOString().split('T')[0]}T23:59:59`)));

                var [historySnapshot, lastHistorySnapshot, vehicleTypesSnapshot, customerSnapshot] = await Promise.all([
                        refHistory.get(),
                        refLastHistory.get(),
                        database.collection("vehicle_type").where('id', '==', vehicleType).get(),
                        allCustomers.get()
                    ]);

                var updatedUserIds = new Set(historySnapshot.docs.map(doc => doc.data().customerId));
                var lastRemainingUsers = customerSnapshot.docs.filter(doc => !updatedUserIds.has(doc.data().id)).length;
                var historyQuotaMap = new Map(lastHistorySnapshot.docs.map(doc => [doc.data().customerId, doc.data().quota]));
                mismatchedQuotaCustomerIds = customerSnapshot.docs
                    .filter(doc => {
                        let customerId = doc.data().id;
                        let totalFuelQuota = doc.data().totalFuelQuota;
                        return historyQuotaMap.has(customerId) && historyQuotaMap.get(customerId) !== totalFuelQuota;
                    })
                    .map(doc => doc.data().id);
                
                if (fuelType != 'All') {

                    var checkLastUpdatedDate = lastupdatedDate.toDate().toISOString().split('T')[0];

                    mismatchedDateCustomerIds = customerSnapshot.docs
                    .filter(doc => {
                        let customerId = doc.data().id;
                        let fuelAllocatedDate = doc.data().allocatedFuelDate.toDate().toISOString().split('T')[0];
                        return checkLastUpdatedDate !== fuelAllocatedDate;
                    })
                    .map(doc => doc.data().id);


                    mergedCustomerIds = Array.from(new Set([
                        ...mismatchedQuotaCustomerIds,
                        ...mismatchedDateCustomerIds
                    ]));


                } else {

                    mergedCustomerIds = mismatchedQuotaCustomerIds;
                }
                logFirebaseActivity('{{trans("lang.fuel_quota")}} {{trans("lang.updated")}}');


                jQuery("#overlay").hide();
                
                if (mergedCustomerIds.length > 0) {
                    jQuery(".pending-user-reset-fuel-btn").show();
                } else {
                    jQuery(".pending-user-reset-fuel-btn").hide();
                }

                $('.reset-fuel-btn').html("{{ trans('lang.reset_fuel_of') }}" + lastRemainingUsers + ' {{ trans('lang.customers') }}')
                
                $('.pending-user-reset-fuel-btn').html("reverted " + mergedCustomerIds.length + ' Users of old apk')
            });

            // Returns an error message, or '' when the booking settings are valid
            function validateBookingSetting() {
                var isWholeNumber = function(value) {
                    return /^\d+$/.test(String(value).trim());
                };
                var turnThreshold = $("#booking_turn_threshold").val();
                var etaWindowMinutes = $("#booking_eta_window_minutes").val();
                var defaultRatePerHour = $("#booking_default_rate_per_hour").val();
                var qrRefreshSeconds = $("#booking_qr_refresh_seconds").val();
                var qrMaxAgeMinutes = $("#booking_qr_max_age_minutes").val();

                if (!isWholeNumber(turnThreshold) || parseInt(turnThreshold, 10) < 1 || parseInt(turnThreshold, 10) > 100) {
                    return "{{ trans('lang.booking_turn_threshold_error') }}";
                }
                if (!isWholeNumber(etaWindowMinutes) || parseInt(etaWindowMinutes, 10) > 120) {
                    return "{{ trans('lang.booking_eta_window_minutes_error') }}";
                }
                if (defaultRatePerHour === '' || isNaN(defaultRatePerHour) || parseFloat(defaultRatePerHour) <= 0 || parseFloat(defaultRatePerHour) > 500) {
                    return "{{ trans('lang.booking_default_rate_per_hour_error') }}";
                }
                if (!isWholeNumber(qrRefreshSeconds) || parseInt(qrRefreshSeconds, 10) < 15 || parseInt(qrRefreshSeconds, 10) > 600) {
                    return "{{ trans('lang.booking_qr_refresh_seconds_error') }}";
                }
                if (!isWholeNumber(qrMaxAgeMinutes) || parseInt(qrMaxAgeMinutes, 10) < 1 || parseInt(qrMaxAgeMinutes, 10) < parseInt(qrRefreshSeconds, 10) / 60) {
                    return "{{ trans('lang.booking_qr_max_age_minutes_error') }}";
                }
                return '';
            }

            jQuery(".pending-user-reset-fuel-btn").on("click", function() {
                var form = $('<form action="/reverted-quota-users-list" method="POST"></form>');
                form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
                mergedCustomerIds.forEach(function(id) {
                    form.append('<input type="hidden" name="ids[]" value="' + id + '">');
                });
                $('body').append(form);
                form.submit();
            });

            function handleUploadJsonFile(evt) {

                var f = evt.target.files[0];
                var reader = new FileReader();

                reader.onload = (function(theFile) {
                    return function(e) {

                        var filePayload = e.target.result;
                        var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                        var val = f.name;
                        var ext = val.split('.')[1];
                        var docName = val.split('fakepath')[1];
                        var filename = (f.name).replace(/C:\\fakepath\\/i, '')

                        var timestamp = Number(new Date());
                        var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                        var uploadTask = firebase.storage().ref('/').child(filename).put(theFile);
                        uploadTask.on('state_changed', function(snapshot) {
                            var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                            jQuery("#uploding_json_file").text("File is uploading...");
                        }, function(error) {}, function() {
                            uploadTask.snapshot.ref.getDownloadURL().then(function(downloadURL) {
                                jQuery("#uploding_json_file").text("Upload is completed");
                                serviceJsonFile = downloadURL;
                                setTimeout(function() {
                                    jQuery("#uploding_json_file").hide();
                                }, 3000);
                            });
                        });
                    };
                })(f);
                reader.readAsDataURL(f);
            }

            var resetMsg = '{{ trans('lang.reset_fuel_alert') }}';

            $(document).on("click", "a[name='reset-fuel-btn']", async function() {

                fuelType = $('#fuelType').val();
                vehicleType = $('#vehicleType').val();
                
                const today = new Date().toISOString().split('T')[0];
                
                if (vehicleType == '' || vehicleType == null) {
                    alert('Select vehicle type');
                    return false;
                }
                
                if (fuelType == '' || fuelType == null) {
                    alert('Select fuel type');
                    return false;
                }
                
                if (!confirm(resetMsg)) return;
                
                Swal.fire({
                    title: 'Processing...',
                    onBeforeOpen: Swal.showLoading,
                    allowOutsideClick: false,
                    showConfirmButton: false
                });

                var refCustomer = database.collection('users')
                    .where('role', '==', 'customer')
                    .where('vehicleId', '==', vehicleType);

                var refHistory = database.collection("customer_fuel_reset_history")
                    .where("date", ">=", firebase.firestore.Timestamp.fromDate(new Date(`${today}T00:00:00`)))
                    .where("date", "<", firebase.firestore.Timestamp.fromDate(new Date(`${today}T23:59:59`)))
                    .where("vehicleId", '==', vehicleType);

                if (fuelType !== 'All') {
                    refCustomer = refCustomer.where('fuelTypeId', '==', fuelType);
                    refHistory = refHistory.where('fuelTypeId', '==', fuelType);

                    if (fuelType == '5KpCEAdAKG4lIuPSAzu0') {
                        var updateData = {
                            'lastUpdatedOther': firebase.firestore.FieldValue.serverTimestamp()
                        };
                    } else if (fuelType == 'HrvJenq9ZDnpBcakC18A') {
                        var updateData = {
                            'lastUpdatedPetrol': firebase.firestore.FieldValue.serverTimestamp()
                        };
                    } else if (fuelType == 'dxPwNWqebokjRiKl0Nhq') {
                        var updateData = {
                            'lastUpdatedDiesel': firebase.firestore.FieldValue.serverTimestamp()
                        };
                    }

                } else {
                    var updatedDate = firebase.firestore.FieldValue.serverTimestamp();
                    var updateData = {
                        'lastUpdatedDiesel': updatedDate,
                        'lastUpdatedPetrol': updatedDate,
                        'lastUpdatedOther': updatedDate
                    };
                }

                const [historySnapshot, vehicleTypesSnapshot, customerSnapshot] = await Promise.all([
                    refHistory.get(),
                    database.collection("vehicle_type").where('id', '==', vehicleType).get(),
                    refCustomer.get()
                ]);

                const updatedUserIds = new Set(historySnapshot.docs.map(doc => doc.data().customerId));
                const vehicleTypeMap = Object.fromEntries(vehicleTypesSnapshot.docs.map(doc => [doc.id, doc.data()]));

                const totalUsers = customerSnapshot.docs.length;
                // const lastRemainingUsers = totalUsers - updatedUserIds.size;
                // Correct logic to calculate last remaining users

                const lastRemainingUsers = customerSnapshot.docs.filter(doc => !updatedUserIds.has(doc.data().id)).length;


                Swal.update({
                    title: 'Processing...',
                    text: `Total Users: ${totalUsers}\nRemaining Users: ${lastRemainingUsers}`,
                });

                const newDate = firebase.firestore.FieldValue.serverTimestamp();
                
                let updatedCount = 0;
                let batchCount = 0;
                const batchPromises = [];
                const BATCH_SIZE = 200;

                for (let i = 0; i < customerSnapshot.docs.length; i += BATCH_SIZE) {

                    const batch = database.batch();

                    customerSnapshot.docs.slice(i, i + BATCH_SIZE).forEach(doc => {

                        const data = doc.data();
                        if (updatedUserIds.has(data.id)) return;

                        const vehicleData = vehicleTypeMap[data.vehicleId];
                        const newFuelQuota = vehicleData?.fuelQuota?.find(f => f.fuelTypeId === data.fuelTypeId)?.fuelQuota || data.totalFuelQuota;

                        try {
                            batch.update(database.collection("users").doc(data.id), {
                                allocatedFuelDate: newDate,
                                availableFuelQuota: newFuelQuota,
                                totalFuelQuota: newFuelQuota,
                                appIdentifier: 'web',
                                updatedAtWeb: newDate,
                                'updateFrom': 'GlobalSetting'
                            });

                            batch.set(database.collection("customer_fuel_reset_history").doc(), {
                                date: newDate,
                                quota: newFuelQuota,
                                customerId: data.id,
                                vehicleId: data.vehicleId,
                                fuelTypeId: data.fuelTypeId,
                                id: database.collection("tmp").doc().id
                            });

                        } catch (error) {
                            console.error(`Error updating document for user ID ${data.id}:`, error);
                        }
                        updatedCount++;
                    });

                    const commitWithRetry = async (batch, maxRetries = 5, delay = 1000) => {
                        for (let attempt = 0; attempt <= maxRetries; attempt++) {
                            try {
                                await batch.commit();
                                return; // Successful commit, exit retry loop
                            } catch (error) {
                                if (error.code === 'resource-exhausted') {
                                    console.error(`Quota exceeded on attempt ${attempt+1}. Retrying...`,
                                        error);
                                } else if (error.message.includes('Using maximum backoff delay')) {
                                    console.error(
                                        `Max backoff delay encountered on attempt ${attempt+1}. Retrying...`,
                                        error);
                                } else {
                                    throw error; // Re-throw for unexpected errors
                                }

                                if (attempt === maxRetries) {
                                    console.error('Max retries reached. Operation failed.');
                                    alert('Quota exceeded. Please try again later.');
                                    return;
                                }

                                // Exponential backoff
                                await new Promise(resolve => setTimeout(resolve, delay * Math.pow(2,attempt)));
                            }
                        }
                    };

                    batchPromises.push(commitWithRetry(batch));


                    // batchPromises.push(batch.commit());

                    batchCount++;

                    if (batchCount === 5) {
                        await Promise.all(batchPromises);
                        batchPromises.length = 0;
                        batchCount = 0;

                        Swal.update({
                            title: 'Processing...',
                            text: `${lastRemainingUsers-updatedCount} users left to update out of ${lastRemainingUsers}`
                        });
                    }
                }

                if (batchPromises.length > 0) await Promise.all(batchPromises);

                Swal.close();
                logFirebaseActivity('{{trans("lang.fuel_quota")}} {{trans("lang.reset")}}');
                Swal.fire({
                    title: 'Update Complete',
                    text: `Total Users Updated: ${updatedCount}`,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
                
                database.collection('vehicle_type').doc(vehicleType).update(updateData).then(function(result) {
                    window.location.reload();
                })

            });

            /*database.collection('users').where('updatedAtWeb','!=',null).orderBy('updatedAtWeb').get().then(async function(snapshot) {
                console.log(snapshot.docs.length);
                let batchCount=0;
                const batchPromises=[];
                const BATCH_SIZE=200;

                for(let i=0;i<snapshot.docs.length;i+=BATCH_SIZE) {
                    const batch=database.batch();

                    snapshot.docs.slice(i,i+BATCH_SIZE).forEach(doc => {
                        const data=doc.data();
                        console.log(data.id);
                        batch.update(database.collection("users").doc(data.id),{
                                'updatedAtWeb':null
                        })
                    });
                    const commitWithRetry=async (batch,maxRetries=5,delay=1000) => {
                        for(let attempt=0;attempt<=maxRetries;attempt++) {
                            try {
                                await batch.commit();
                                return; // Successful commit, exit retry loop
                            } catch(error) {
                                if(error.code==='resource-exhausted') {
                                    console.error(`Quota exceeded on attempt ${attempt+1}. Retrying...`,error);
                                } else if(error.message.includes('Using maximum backoff delay')) {
                                    console.error(`Max backoff delay encountered on attempt ${attempt+1}. Retrying...`,error);
                                } else {
                                    throw error; // Re-throw for unexpected errors
                                }

                                if(attempt===maxRetries) {
                                    console.error('Max retries reached. Operation failed.');
                                    alert('Quota exceeded. Please try again later.');
                                    return;
                                }

                                // Exponential backoff
                                await new Promise(resolve => setTimeout(resolve,delay*Math.pow(2,attempt)));
                            }
                        }
                    };
                    batchPromises.push(commitWithRetry(batch));

                    batchCount++;

                    if(batchCount===5) {
                        await Promise.all(batchPromises);
                        batchPromises.length=0;
                        batchCount=0;

                    }
                }

                if(batchPromises.length>0) await Promise.all(batchPromises);


            
            })*/
        </script>
    @endsection
