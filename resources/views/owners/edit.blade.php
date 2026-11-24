@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.owners') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('owners') !!}">{{ trans('lang.owners') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.edit_owner') }}</li>
                </ol>
            </div>

        </div>
        <div class="container-fluid">
            <div class="card pb-4">
                <div class="card-body">

                    <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{ trans('lang.processing') }}</div>

                    <div class="row daes-top-sec mb-3">

                    </div>

                    <div class="error_top"></div>
                    <div class="row restaurant_payout_create">
                        <div class="restaurant_payout_create-inner">

                            <fieldset>
                                <legend>{{ trans('lang.edit_owner') }}</legend>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.full_name') }}<span class="required-field"></span></label>
                                    <div class="col-7">
                                        <input type="text" class="form-control full_name" onkeypress="return chkAlphabets(event,'error')">
                                        <div id="error" class="err"></div>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.full_name_help') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row width-50" id="phone-box">
                                    <label class="col-3 control-label">{{ trans('lang.country_code') }}<span class="required-field"></span></label>
                                    <div class="col-7">
                                        <select name="country" id="country_code" class="form-control country_code phone_code">
                                            <option value="">{{ trans('lang.country_code_help') }}</option>
                                            @foreach ($countries_data as $country)
                                                <option value="{{ $country->phoneCode }}">{{ $country->countryName }}
                                                    (+{{ $country->phoneCode }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.country_code_help') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50" id="phone-box">
                                    <label class="col-3 control-label">{{ trans('lang.state') }}<span class="required-field"></span></label>
                                    <div class="col-7">
                                        <select name="state" id="state_select_box" class="form-control state_select_box">
                                        </select>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.state_help') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50" id="phone-box">
                                    <label class="col-3 control-label">{{ trans('lang.city') }}<span class="required-field"></span></label>
                                    <div class="col-7">
                                        <select name="city" id="city_select_box" class="form-control city_select_box">
                                            <option value="">{{ trans('lang.select_city') }}</option>
                                        </select>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.city_help') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.phone') }}<span class="required-field"></span></label>
                                    <div class="col-7">
                                        <input type="text" class="form-control phone" onkeypress="return chkAlphabets2(event,'error2')">
                                        <div id="error2" class="err"></div>

                                        <div class="form-text text-muted w-50">
                                            {{ trans('lang.phone_help') }}
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.password') }}<span class="required-field"></span></label>
                                    <div class="col-7">
                                        <input type="text" class="form-control password">
                                        <div class="form-text text-muted">
                                            {{ trans('lang.password_help') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.fuel_station_name') }}<span class="required-field"></span></label>
                                    <div class="col-7">
                                        <input type="text" class="form-control fuel_station_name">
                                        <div class="form-text text-muted">
                                            {{ trans('lang.fuel_station_name_help') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.fuel_station_address') }}<span class="required-field"></span></label>
                                    <div class="col-7">
                                        <input type="text" class="form-control fuel_station_address">
                                        <div class="form-text text-muted">
                                            {{ trans('lang.fuel_station_address_help') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.select_fuel_type') }}<span class="required-field"></span></label>

                                    <div id="fuel_type_div"></div>

                                </div>
                                <div id="owner_fuel_capacity_div"></div>
                                <div id="fuel_capacity_div"></div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.business_registration_certificate') }}<span class="required-field"></span></label>
                                    <input type="file" onChange="handleFileSelect(event,'business_registration')" class="col-7">
                                    <div class="placeholder_img_thumb business_registration"></div>
                                    <div id="uploding_image"></div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{ trans('lang.fuel_station_image') }}<span class="required-field"></span></label>
                                    <input type="file" onChange="handleFileSelect(event,'petrol_pump_documents')" class="col-7">
                                    <div class="placeholder_img_thumb petrol_pump_documents"></div>
                                    <div id="uploding_image"></div>
                                </div>

                            </fieldset>
                            <fieldset>
                                <legend>{{ trans('lang.active_deactive_lable') }}</legend>

                                <div class="form-check">
                                    <input type="checkbox"
                                        id="isActive"
                                        class="isActive" checked>
                                    <label class="control-label" for="isActive">
                                        {{ trans('lang.active') }}
                                    </label>
                                </div>
                                <div class="form-group row width-50 d-none" id="deactive_reason_div">
                                    <label class="col-3 control-label">
                                        {{ trans('lang.deactive_reason') }}
                                    </label>
                                    <div class="col-7">
                                        <input type="text"
                                            class="form-control deactiveReason"
                                            id="deactiveReason">
                                        <div class="form-text text-muted">
                                            {{ trans('lang.enter_deactive_message_show_in_apk') }}
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                        </div>
                    </div>
                </div>
                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary  save_user_btn"><i class="fa fa-save"></i> {{ trans('lang.save') }}</button>
                    <a href="{!! route('owners') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var id = "<?php echo $id; ?>";
        var database = firebase.firestore();
        var ref = database.collection('users').where("id", "==", id);
        var currentCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = 0;

        var storageRef = firebase.storage().ref('images');
        var storage = firebase.storage();
        var photo = "";
        var fileName = "";
        var profileImageFile = '';
        var passportPhoto = "";
        var passportFileName = "";
        var passportImageFile = '';
        var businessPhoto = "";
        var businessFileName = "";
        var businessImageFile = '';
        var placeholderImage = "{{ asset('/images/default_user.png') }}";
        var append_list = '';
        var documentPhoto = '';
        var documentFileName = '';
        var documentImageFile = '';
        var fuelStationObj = {};
        var registrationCertificateObj = {};
        var allocatedFuelDate = '';
        var lastPetrolCapacity = '';
        var lastDieselCapacity = '';
        var lastOtherCapacity = '';
        $('.phone_code').select2();
        var fueltypeRef = database.collection('fuel_type').where('enable', '==', true);
        var allocatedPetrolDate = '';
        var allocatedDieselDate = '';
        var allocatedOtherDate = '';
        var lastStatus = '';
        var fcmToken = '';
        $(document).ready(function() {

            $('.owner_menu').addClass('active');

            jQuery("#overlay").show();
            toggleDeactiveReason();

            $('#isActive').on('change', function () {
                toggleDeactiveReason();
            });

            function toggleDeactiveReason() {
                if ($('#isActive').is(':checked')) {
                    $('#deactive_reason_div').addClass('d-none');
                    $('#deactiveReason').val('');
                } else {
                    $('#deactive_reason_div').removeClass('d-none');
                }
            }
            database.collection('states')
                .where('enable', '==', true)
                .where('isDeleted', '==', false)
                .get()
                .then(function (snapshot) {

                var html = '<option value="">{{ trans("lang.select_state") }}</option>';

                if (snapshot.docs.length > 0) {
                    snapshot.docs.forEach((doc) => {
                        var data = doc.data();
                        html += `<option value="${data.id}">${data.name}</option>`;
                    });
                }

                $('#state_select_box').html(html);
            });

            $('#state_select_box').on('change', function () {
                var selected_state = $(this).val();
                $('#city_select_box').html('<option value="">{{ trans("lang.select_city") }}</option>');

                if (selected_state === '') {
                    return;
                }
                database.collection('cities')
                    .where('state_id', '==', selected_state)
                    .where('enable', '==', true)
                    .where('isDeleted', '==', false)
                    .get()
                    .then(function (snapshot) {

                        var html = '<option value="">{{ trans("lang.select_city") }}</option>';

                        if (snapshot.docs.length > 0) {
                            snapshot.docs.forEach((doc) => {
                                var data = doc.data();
                                html += `<option value="${data.id}">${data.name}</option>`;
                            });
                        }

                        $('#city_select_box').html(html);
                    });
            });

            fueltypeRef.get().then(async function(snapshot) {
                if (snapshot.docs.length > 0) {
                    var html = '';
                    var capacityhtml = '';
                    var ownerAddedCapacity = '';
                    snapshot.docs.forEach((listval) => {
                        var data = listval.data();
                        html = html + '<div class="form-check">';
                        html = html + '<input type="checkbox" class="fuel_type" id="' + data.name + '" value="' + data.name + '"  data-id="' + data.id + '" name="fuel_type" onclick="toggleCapacityFields()">';
                        html = html + '<label class="col-3 control-label" for="' + data.name + '">' + data.name + '</label>';
                        html = html + '</div>';
                        ownerAddedCapacity = ownerAddedCapacity + '<div class="form-group row width-50 d-none owner_' + data.name + '_capacity">';
                        ownerAddedCapacity = ownerAddedCapacity + '<label class="col-3 control-label">{{ trans('lang.fuel_capacity') }} ' + data.name + ' ({{ trans('lang.in_litre') }})<span class="required-field"></span></label>';
                        ownerAddedCapacity = ownerAddedCapacity + '<div class="col-7">';
                        ownerAddedCapacity = ownerAddedCapacity + '<input type="text" class="form-control" id="owner_' + data.name + '_capacity" readonly>';
                        ownerAddedCapacity = ownerAddedCapacity + '</div>';
                        ownerAddedCapacity = ownerAddedCapacity + '</div>';

                        capacityhtml = capacityhtml + '<div class="form-group row width-50 d-none ' + data.name + '_capacity">';
                        capacityhtml = capacityhtml + '<label class="col-3 control-label">{{ trans('lang.allocate_fuel_capacity') }} ' + data.name + ' ({{ trans('lang.in_litre') }})<span class="required-field"></span></label>';
                        capacityhtml = capacityhtml + '<div class="col-7">';
                        capacityhtml = capacityhtml + '<input type="text" class="form-control" id="' + data.name + '_capacity">';
                        capacityhtml = capacityhtml + '<input type="hidden" class="form-control" id="' + data.name + '_available_capacity">';

                        capacityhtml = capacityhtml + '</div>';
                        capacityhtml = capacityhtml + '</div>';
                    })
                    $('#fuel_type_div').html(html);
                    $('#owner_fuel_capacity_div').html(ownerAddedCapacity);
                    $('#fuel_capacity_div').html(capacityhtml);

                }
            })
            $('.fuel_type').on('click', function() {
                toggleCapacityFields();
            });

            ref.get().then(async function(snapshots) {
                var user = snapshots.docs[0].data();
                if (user.hasOwnProperty(allocatedPetrolDate)) {
                    allocatedPetrolDate = user.allocatedPetrolDate;
                }
                if (user.hasOwnProperty(allocatedDieselDate)) {
                    allocatedDieselDate = user.allocatedDieselDate;
                }
                if (user.hasOwnProperty(allocatedOtherDate)) {
                    allocatedOtherDate = user.allocatedOtherDate;
                }

                $(".full_name").val(user.fullName);
                $(".phone").val(user.mobileNumber);
                $(".password").val(atob(user.password));
                $('#isActive').prop('checked', user.isActive === true);
                lastStatus = user.isActive;
                fcmToken = user.fcmToken;
                $('#deactiveReason').val(user.deactiveReason);
                toggleDeactiveReason();
                if (user.countryCode != null) {
                    countryCode = user.countryCode.includes('+') ? user.countryCode.slice(1) : user.countryCode;
                    $('#country_code').val(countryCode).trigger('change');
                }
                if (user.stateId != null) {
                    $('#state_select_box').val(user.stateId).trigger('change');
                }
                if (user.cityId != null) {
                    setTimeout(function () {
                        $('#city_select_box').val(user.cityId).trigger('change');
                    }, 1000);
                }
                $(".fuel_station_name").val(user.fuelStationName);
                $(".fuel_station_address").val(user.fuelStationAddress);
              
                allocatedFuelDate = user.allocatedFuelDate;

                user.fuel.forEach(element => {
                    $('#' + element.name).prop('checked', true);
                    $('#' + element.name + '_capacity').val(element.totalFuelQuota);
                    $('#' + element.name + '_available_capacity').val(element.availableFuelQuota);
                    if (element.name == 'Petrol') {
                        lastPetrolCapacity = element.totalFuelQuota;
                    } else if (element.name == 'Diesel') {
                        lastDieselCapacity = element.totalFuelQuota;
                    } else if (element.name == 'Other') {
                        lastOtherCapacity = element.totalFuelQuota;
                    }

                    $('#owner_' + element.name + '_capacity').val(element.fuelCapacity);
                    var capacityFieldClass = '.' + element.name + '_capacity';
                    var ownerCapacityclass = '.owner_' + element.name + '_capacity'
                    $(capacityFieldClass).removeClass('d-none');
                    $(ownerCapacityclass).removeClass('d-none');
                });

                documentPhoto = user.fuelStationImage.url;
                documentImageFile = user.fuelStationImage.url;
                fuelStationObj = user.fuelStationImage;
                var document_photo_html = '<img width="100px" id="" height="auto" src="' + documentPhoto + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"></span>';

                if (document_photo_html) {
                    $(".petrol_pump_documents").html(document_photo_html);
                } else {
                    $(".petrol_pump_documents").html('<p>photos not available.</p>');
                }

                businessPhoto = user.registrationCertificate.url;
                businessImageFile = user.registrationCertificate.url;
                registrationCertificateObj = user.registrationCertificate;
                var business_registration_html = '<img width="100px" id="" height="auto" src="' + businessPhoto + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"></span>';
                if (business_registration_html) {
                    $(".business_registration").html(business_registration_html);
                } else {
                    $(".business_registration").html('<p>photos not available.</p>');
                }

                jQuery("#overlay").hide();
            })

            $(".save_user_btn").click(async function() {

                var fullName = $(".full_name").val();
                var phone = $(".phone").val();
                var countryCode = $('.country_code').val();
                var stateId = $('#state_select_box').val();
                var cityId = $('#city_select_box').val();
                var fuelStationName = $('.fuel_station_name').val();
                var address = $('.fuel_station_address').val();
            
                var password = btoa($('.password').val());
                
                var isActive = $('#isActive').is(':checked');
                var deactiveReason = $('#deactiveReason').val();
                if(lastStatus && !isActive && deactiveReason != '' && fcmToken != ''){
                    var fcm = [];
                    fcm.push(fcmToken);
                    var subject = "{{trans('lang.deactive_notification_subject')}}";
                    $.ajax({
                        method: 'POST',
                        url: '<?php echo route('sendNotification'); ?>',
                        data: {
                            'fcm': fcm,
                            'subject': subject,
                            'message': deactiveReason,
                            '_token': '<?php echo csrf_token() ?>'
                        }
                    }).done(function (resultdata) {
                        console.log(resultdata);
                    }).fail(function (xhr, textStatus, errorThrown) {
                        console.log(xhr);
                    });  
                }

                var selectedCheckbox = false;
                var fuel = [];
                var checkCapacityGreaterCount = 0;
                var checkEmptyCapacityValCount = 0;
                const updateData = {};
                await Promise.all($('.fuel_type').map(async function() {
                    var checkbox = $(this);
                    var value = checkbox.val();
                    var fuelTypeId = $(this).attr('data-id');
                    var capacityFieldClass = '.' + value + '_capacity';
                    if (checkbox.is(':checked')) {
                        selectedCheckbox = true;
                        var capacityVal = $('#' + value + '_capacity').val();
                        var ownerCapacity = $('#owner_' + value + '_capacity').val();

                        if (capacityVal == '') {
                            checkEmptyCapacityValCount++;
                        } else if (parseInt(capacityVal) > parseInt(ownerCapacity)) {
                            checkCapacityGreaterCount++;
                        } else {
                        
                            var fuelObj = {
                                'availableFuelQuota': $('#' + value + '_available_capacity').val(),
                                'fuelTypeId': fuelTypeId,
                                'fuelCapacity': ownerCapacity == '' ? capacityVal : ownerCapacity,
                                'name': value,
                                'totalFuelQuota': capacityVal
                            }

                            if (value == "Petrol" && parseInt(lastPetrolCapacity) !== parseInt(capacityVal)) {
                                var remaining = $('#Petrol_available_capacity').val();
                                updateData['allocatedPetrolDate'] = firebase.firestore.FieldValue.serverTimestamp();
                                //fuelObj.availableFuelQuota = capacityVal;
                                var fuelObj={
                                    'availableFuelQuota': capacityVal,
                                    'fuelTypeId': fuelTypeId,
                                    'fuelCapacity': ownerCapacity,
                                    'name': value,
                                    'totalFuelQuota': capacityVal,
                                }
                                await addResetHistory(lastPetrolCapacity, capacityVal, remaining, 'Petrol');

                            } else if (value == 'Diesel' && parseInt(lastDieselCapacity) !== parseInt(capacityVal)) {
                                updateData['allocatedDieselDate'] = firebase.firestore.FieldValue.serverTimestamp();
                                var remaining = $('#Diesel_available_capacity').val();
                                //fuelObj.availableFuelQuota = capacityVal;
                                var fuelObj={
                                    'availableFuelQuota': capacityVal,
                                    'fuelTypeId': fuelTypeId,
                                    'fuelCapacity': ownerCapacity,
                                    'name': value,
                                    'totalFuelQuota': capacityVal,

                                }
                                await addResetHistory(lastDieselCapacity, capacityVal, remaining, 'Diesel');

                            } else if (value == 'Other' && parseInt(lastOtherCapacity) !== parseInt(capacityVal)) {
                                var remaining = $('#Other_available_capacity').val();
                                updateData['allocatedOtherDate'] = firebase.firestore.FieldValue.serverTimestamp();
                                //fuelObj.availableFuelQuota = capacityVal;
                                var fuelObj={
                                    'availableFuelQuota': capacityVal,
                                    'fuelTypeId': fuelTypeId,
                                    'fuelCapacity': ownerCapacity,
                                    'name': value,
                                    'totalFuelQuota': capacityVal,

                                }
                                await addResetHistory(lastOtherCapacity, capacityVal, remaining, 'Other');

                            }


                            fuel.push(fuelObj);


                        }
                    }
                }));
                
                updateData['fuel'] = fuel;

                if (fullName == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.full_name_help') }}</p>");
                    window.scrollTo(0, 0);
                } else if (!isActive && deactiveReason === '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.deactive_reason_help')}}</p>");
                    window.scrollTo(0,0);
                } else if (countryCode == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.country_code_help') }}</p>");
                    window.scrollTo(0, 0);
                } else if (stateId == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.state_help') }}</p>");
                    window.scrollTo(0, 0);
                } else if (cityId == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.city_help') }}</p>");
                    window.scrollTo(0, 0);
                } else if (phone == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.phone_help') }}</p>");
                    window.scrollTo(0, 0);
                } else if (selectedCheckbox == false) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.select_fuel_type') }}</p>");
                    window.scrollTo(0, 0);
                } else if (password == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.password_help') }}</p>");
                    window.scrollTo(0, 0);
                } else if (businessPhoto == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.business_registration_photo_help') }}</p>");
                    window.scrollTo(0, 0);
                } else if (documentPhoto == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.petrol_pump_documents_help') }}</p>");
                    window.scrollTo(0, 0);
                } else if (address == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.fuel_station_address_help') }}</p>");
                    window.scrollTo(0, 0);
                } else if (fuelStationName == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.fuel_station_name_help') }}</p>");
                    window.scrollTo(0, 0);
                } else if (checkCapacityGreaterCount > 0) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.allocate_fuel_capacity_grater_err') }}</p>");
                    window.scrollTo(0, 0);
                } else if (checkEmptyCapacityValCount > 0) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.allocate_fuel_capacity_empty_err') }}</p>");
                    window.scrollTo(0, 0);
                } else if (fuel.length === 0) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.allocate_fuel_capacity_err') }}</p>");
                    window.scrollTo(0, 0);
                } else {
                    var checkPhone = await checkPhoneNumberExist(phone);
                    if (checkPhone != true) {
                        jQuery("#overlay").show();
                        updateData['fullName'] = fullName;
                        updateData['countryCode'] = '+' + countryCode;
                        updateData['cityId'] = cityId;
                        updateData['stateId'] = stateId;
                        updateData['mobileNumber'] = phone;
                        updateData['fuelStationAddress'] = address;
                        updateData['fuelStationName'] = fuelStationName;
                        updateData['password'] = password;


                        await storeBusinessImageData().then(async BusinessImage => {
                            await storeDocumentsImageData().then(async DocumentImage => {
                                updateData['registrationCertificate'] = BusinessImage;
                                updateData['fuelStationImage'] = DocumentImage;
                                updateData['isActive'] = isActive;
                                updateData['deactiveReason'] = deactiveReason;
                                updateData['fuelUpdatedAt'] = firebase.firestore.FieldValue.serverTimestamp();
                               
                                await database.collection('users').doc(id).update(updateData).then(async function(result) {
                                    await database.collection('users').doc(id).update({
                                        fuel: updateData.fuel 
                                    }); 
                                    logFirebaseActivity('#' + id + ' {{trans("lang.owners")}} {{trans("lang.fuel_quota")}} {{trans("lang.updated")}}');
                                    window.location.href = '{{ route('owners') }}';
                                });
                            }).catch(err => {
                                jQuery("#overlay").hide();
                                $(".error_top").show();
                                $(".error_top").html("");
                                $(".error_top").append("<p>" + err + "</p>");
                                window.scrollTo(0, 0);
                            });
                        }).catch(err => {
                            jQuery("#overlay").hide();
                            $(".error_top").show();
                            $(".error_top").html("");
                            $(".error_top").append("<p>" + err + "</p>");
                            window.scrollTo(0, 0);
                        });

                    } else {
                        jQuery("#overlay").hide();
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>{{ trans('lang.phone_already_exist') }}</p>");
                        window.scrollTo(0, 0);

                    }
                }
            })
        })

        async function addResetHistory(lastCapacity, capacityVal, remaining, fuelType) {
            var tempId = database.collection('temp').doc().id;
            await database.collection('owner_reset_history').doc(tempId).set({
                'id': tempId,
                'ownerId': id,
                'fuelType': fuelType,
                'lastAllocatedFuel': lastCapacity,
                'lastRemainingFuel': remaining,
                'newAllocatedFuel': capacityVal,
                'createAt': firebase.firestore.Timestamp.fromDate(new Date())
            })
        }
        async function checkPhoneNumberExist(phoneNumber) {
            var checkPhone = false;
            await database.collection('users').where('mobileNumber', '==', phoneNumber).get().then(async function(snapshots) {
                if (snapshots.docs.length > 0) {
                    snapshots.docs.forEach(listval => {
                        var data = listval.data();
                        if (data.id != id) {
                            checkPhone = true;
                        }
                    });

                }
            })
            return checkPhone;
        }

        function toggleCapacityFields() {

            $('.fuel_type').each(function() {
                var checkbox = $(this);
                var value = checkbox.val();

                var capacityFieldClass = '.' + value + '_capacity';
                if (checkbox.is(':checked')) {
                    $(capacityFieldClass).removeClass('d-none');
                } else {
                    $(capacityFieldClass).addClass('d-none');
                }
            });
        }

        async function storeBusinessImageData() {
            var newPhoto = registrationCertificateObj;
            try {
                if (businessImageFile != "" && businessPhoto != businessImageFile) {
                    var userOldImageUrlRef = await storage.refFromURL(businessImageFile);
                    imageBucket = userOldImageUrlRef.bucket;
                    var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                    if (imageBucket == envBucket) {
                        await userOldImageUrlRef.delete().then(() => {
                            console.log("Old file deleted!")
                        }).catch((error) => {
                            console.log("ERR File delete ===", error);
                        });
                    } else {
                        console.log('Bucket not matched');
                    }
                }
                if (businessPhoto != businessImageFile) {
                    businessPhoto = businessPhoto.replace(/^data:image\/[a-z]+;base64,/, "")
                    var uploadTask = await storageRef.child(businessFileName).putString(businessPhoto, 'base64', {
                        contentType: 'image/jpg'
                    });
                    var downloadURL = await uploadTask.ref.getDownloadURL();
                    newPhoto.url = downloadURL;
                    businessPhoto = downloadURL;

                }
            } catch (error) {
                console.log("ERR ===", error);
            }
            return newPhoto;
        }
        async function storeDocumentsImageData() {
            var newPhoto = fuelStationObj;
            try {
                if (documentImageFile != "" && documentPhoto != documentImageFile) {
                    var userOldImageUrlRef = await storage.refFromURL(documentImageFile);
                    imageBucket = userOldImageUrlRef.bucket;
                    var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                    if (imageBucket == envBucket) {
                        await userOldImageUrlRef.delete().then(() => {
                            console.log("Old file deleted!")
                        }).catch((error) => {
                            console.log("ERR File delete ===", error);
                        });
                    } else {
                        console.log('Bucket not matched');
                    }
                }
                if (documentPhoto != documentImageFile) {
                    documentPhoto = documentPhoto.replace(/^data:image\/[a-z]+;base64,/, "")
                    var uploadTask = await storageRef.child(documentFileName).putString(documentPhoto, 'base64', {
                        contentType: 'image/jpg'
                    });
                    var downloadURL = await uploadTask.ref.getDownloadURL();
                    newPhoto.url = downloadURL;
                    documentPhoto = downloadURL;

                }
            } catch (error) {
                console.log("ERR ===", error);
            }

            return newPhoto;
        }

        function handleFileSelect(evt, type) {
            var f = evt.target.files[0];
            var reader = new FileReader();

            reader.onload = (function(theFile) {
                return function(e) {

                    var filePayload = e.target.result;
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var docName = val.split('fakepath')[1];
                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                    var timestamp = Number(new Date());
                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;

                    if (type == "business_registration") {
                        businessPhoto = filePayload;
                        businessFileName = filename;
                        $(".business_registration").empty();
                        $(".business_registration").append('<img class="rounded" style="width:50px" src="' + businessPhoto + '" alt="image">');
                        registrationCertificateObj = {
                            'name': filename,
                            'path': '',
                            size: ''
                        }
                    }

                    if (type == "petrol_pump_documents") {
                        photos_html = '<img width="100px" id="" height="auto" src="' + filePayload + '">';
                        $(".petrol_pump_documents").append(photos_html);
                        documentPhoto = filePayload;
                        documentFileName = filename;
                        fuelStationObj = {
                            'name': filename,
                            'path': '',
                            size: ''
                        }
                    }
                };
            })(f);
            reader.readAsDataURL(f);
        }

        function chkAlphabets2(event, msg) {
            if (!(event.which >= 48 && event.which <= 57)) {
                document.getElementById(msg).innerHTML = "Accept only Number";
                return false;
            } else {
                document.getElementById(msg).innerHTML = "";
                return true;
            }
        }
    </script>
@endsection
