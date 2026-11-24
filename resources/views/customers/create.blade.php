@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.customers')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('customers') !!}">{{trans('lang.customers')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.customers_create')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card pb-4">
            <div class="card-body">

                <div class="row daes-top-sec mb-3">

                </div>

                <div class="error_top"></div>
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">

                        <fieldset>
                            <legend>{{trans('lang.customers_create')}}</legend>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.full_name')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control full_name"
                                        onkeypress="return chkAlphabets(event,'error')">
                                    <div id="error" class="err"></div>
                                    <div class="form-text text-muted">
                                        {{ trans("lang.full_name_help") }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row width-50" id="phone-box">
                                <label class="col-3 control-label">{{trans('lang.country_code')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <select name="country" id="country_selector"
                                        class="form-control country_code phone_code">
                                        <option value="">{{ trans("lang.country_code_help") }}</option>
                                        @foreach($countries_data as $country)
                                            <option value="{{$country->phoneCode}}">{{$country->countryName}}
                                                (+{{$country->phoneCode}})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text text-muted">
                                        {{ trans("lang.country_code_help") }}
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
                            <div class="form-group row width-50" id="phone-box">
                                    <label class="col-3 control-label">{{ trans('lang.fuel_station') }}<span class="required-field"></span></label>
                                    <div class="col-7">
                                        <select name="fuel_station" id="fuel_station_select_box" class="form-control fuel_station_select_box">
                                            <option value="">{{ trans('lang.select_fuel_station') }}</option>
                                        </select>
                                    </div>
                            </div>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.phone')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control phone"
                                        onkeypress="return chkAlphabets2(event,'error2')">
                                    <div id="error2" class="err"></div>

                                    <div class="form-text text-muted w-50">
                                        {{ trans("lang.phone_help") }}
                                    </div>
                                </div>

                            </div>
                             <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.password')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control password">
                                    <div class="form-text text-muted">
                                        {{ trans("lang.password_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label
                                    class="col-3 control-label">{{trans('lang.profile_pic')}}</label>
                                <input type="file" onChange="handleFileSelect(event,'profile')"
                                    class="col-7">
                                <div class="placeholder_img_thumb profile_img"></div>
                               
                            </div>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.vehicle_no')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control vehicle_no" id="vehicle_no">
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.vehicle_engine_capacity')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control engine_capacity" id="engine_capacity">
                                </div>
                                <div class="form-text text-muted">
                                    {{ trans("lang.vehicle_engine_capacity_help") }}
                                </div>
                            </div>
                            <div class="form-group row width-50" id="vehicle_type">
                                <label class="col-3 control-label">{{trans('lang.vehicle_type')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <select name="vehicle_type" id="vehicle_type_selector"
                                        class="form-control vehicle_type_selector">
                                        <option value="" disabled selected>{{ trans("lang.vehicle_typee_help") }}
                                        </option>

                                    </select>
                                    <div class="form-text text-muted">
                                        {{ trans("lang.vehicle_typee_help") }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.vehicle_model')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control vehicle_model" id="vehicle_model">
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.fuel_type')}}<span
                                        class="required-field"></span></label>
                                <div id="fuel_type_div"></div>
                            </div>
                            <input type="hidden" id="veh_id" value="">

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.allocate_fuel_quota')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control vehicleType_fuel_quota"
                                        id="vehicleType_fuel_quota" disabled>
                                </div>
                            </div>



                            <div class="form-group row width-50 d-none other_detail_div">
                                <label class="col-3 control-label">{{trans('lang.description')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control other_detail" id="other_detail">
                                </div>
                                <div class="form-text text-muted">
                                    {{ trans("lang.enter_specific_details") }}
                                </div>
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

                        <fieldset>
                            <legend>{{trans('lang.additional_information')}}</legend>

                            <div class="form-group row width-50">
                                <label
                                    class="col-3 control-label">{{trans('lang.vehicle_registration_certificate')}}<span
                                        class="required-field"></span></label>
                                <input type="file" onChange="handleFileSelect(event,'registration_certificate')"
                                    class="col-7">
                                <div class="placeholder_img_thumb registration_certificate"></div>
                                <div id="uploding_image"></div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.vehicle_registration_number')}}<span
                                        class="required-field"></span></label>
                                <input type="file" onChange="handleFileSelect(event,'registration_number')"
                                    class="col-7">
                                <div class="placeholder_img_thumb registration_number"></div>
                                <div id="uploding_image"></div>
                            </div>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.chassis_no')}}<span
                                        class="required-field"></span></label>

                                <input type="file" onChange="handleFileSelect(event,'chassis_no')" class="col-7">
                                <div class="placeholder_img_thumb chassis_no"></div>
                                <div id="uploding_image"></div>
                            </div>


                        </fieldset>

                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary  save_user_btn"><i class="fa fa-save"></i> {{
    trans('lang.save')}}</button>
                <a href="{!! route('customers') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
    trans('lang.cancel')}}</a>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
    var database = firebase.firestore();
    var createdAt = firebase.firestore.FieldValue.serverTimestamp();
    var photo = "";
    var fileName = '';
    var vRegNoPhoto = "";
    var vRegNoFileName = "";
    var vRegPhoto = "";
    var vRegFileName = "";
    var fuelQuotaArr = [];
    var fuelTypeId = '';
    var fuleQuota = '';
    var profilePhoto='';
    var profileFileName='';
    var chassisObj = {};
    var regCertificateObj = {};
    var regNoObj = {};

    $('.phone_code').select2();
    $('#vehicle_type_selector').select2();

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
    $('#city_select_box').on('change', function () {
                var selected_city = $(this).val();
                $('#fuel_station_select_box').html('<option value="">{{ trans("lang.select_fuel_station") }}</option>');

                if (selected_city === '') {
                    return;
                }
                database.collection('users')
                    .where('role', '==', "owner")
                    .where('cityId', '==', selected_city)
                    .get()
                    .then(function (snapshot) {
                        var fuel_station_html = '<option value="">{{ trans("lang.select_fuel_station") }}</option>';
                        if (snapshot.docs.length > 0) {
                            snapshot.docs.forEach((doc) => {
                                var data = doc.data();
                                fuel_station_html += `<option value="${data.id}">${data.fuelStationName}</option>`;
                            });
                        }

                        $('#fuel_station_select_box').html(fuel_station_html);
                    });
    });

    var fueltypeRef = database.collection('fuel_type').where('enable', '==', true);
    fueltypeRef.get().then(async function (snapshot) {
        if (snapshot.docs.length > 0) {
            var html = '';
            snapshot.docs.forEach((listval) => {
                var data = listval.data();
                html = html + '<div class="form-check">';
                html = html + '<input type="radio" class="fuel_type" name="fuel_type" id="' + data.id + '"  value="' + data.name + '"  data-id="' + data.id + '" >';
                html = html + '<label class="col-3 control-label" for="' + data.id + '">' + data.name + '</label>';
                html = html + '</div>';

            })
            $('#fuel_type_div').html(html);
        }
    })


    var ref = database.collection('vehicle_type').where('enable', '==', true);

    ref.get().then(async function (snapshots) {
        snapshots.docs.forEach(doc => {
            var data = doc.data();
            $('#vehicle_type_selector').append($("<option></option>")
                .attr("value", data.id)
                .attr("data-quota", JSON.stringify(data.fuelQuota))
                .text(data.name));
        })
    });

    $("#vehicle_type_selector").change(function () {
        fuelQuotaArr = JSON.parse($(this).find(':selected').attr('data-quota'));
        fuelQuotaArr.forEach(element => {
            if (element.fuelTypeId == fuelTypeId) {
                fuelQuota = element.fuelQuota;
                $('#vehicleType_fuel_quota').val(fuelQuota);
            } else {
                $('#vehicleType_fuel_quota').val('');
            }
        });
        $("#veh_id").val($(this).val());
    });

    $(document).on('click', 'input[name="fuel_type"]', function () {
        fuelTypeId = $(this).attr('data-id');
        fuelQuotaArr.forEach(element => {
            if (element.fuelTypeId == fuelTypeId) {
                fuelQuota = element.fuelQuota;
                $('#vehicleType_fuel_quota').val(fuelQuota);
            } else {
                $('#vehicleType_fuel_quota').val('');
            }
        });

        if ($(this).val() == "Other") {
            $('.other_detail_div').removeClass('d-none');
        } else {
            $('.other_detail_div').addClass('d-none');
        }
    });



    $(".save_user_btn").click(async function () {

        var fullName = $(".full_name").val();
        var phone = $(".phone").val();
        var countryCode = $('.country_code').val();
        var stateId = $('#state_select_box').val();
        var cityId = $('#city_select_box').val();
         var pumpOwnerId = $('#fuel_station_select_box').val();
        var vehicleNo = $('.vehicle_no').val();
        var vehicle_type = $("#vehicle_type_selector option:selected").text();
        var vehicle_id = $("#veh_id").val();
        var vehicle_model = $('.vehicle_model').val();
        var fuel_quota = $('.vehicleType_fuel_quota').val();
        var fuel_type = $("input[type='radio'][name='fuel_type']:checked").val();
        var fuelTypeId = $("input[type='radio'][name='fuel_type']:checked").attr('data-id');
        var password = btoa($('.password').val());
        var engine_capacity = $('#engine_capacity').val();
        if (fuel_type == 'other') {
            var fuelTypeDetail = $('#other_detail').val();
        } else {
            var fuelTypeDetail = '';
        }
        var isActive = $('#isActive').is(':checked');
        var deactiveReason = $('#deactiveReason').val();
        var id = "<?php echo uniqid(); ?>";

        if (fullName == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.first_name_help')}}</p>");
            window.scrollTo(0, 0);

        } else if (!isActive && deactiveReason === '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.deactive_reason_help')}}</p>");
            window.scrollTo(0,0);
        } else if (countryCode == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.country_code_help')}}</p>");
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
        } else if (pumpOwnerId == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.select_fuel_station') }}</p>");
                    window.scrollTo(0, 0);
        }
        else if (phone == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.phone_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (vRegPhoto == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.business_photo_help')}}</p>");
            window.scrollTo(0, 0);
        }
         else if (vRegNoPhoto == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.veh_reg_number_photo_help')}}</p>");
            window.scrollTo(0, 0);
        }
         else if (photo == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.chassis_photo_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (vehicleNo == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.vehicleno_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (vehicle_id == null || vehicle_id == '' || vehicle_id == undefined) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.vehicletype_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (vehicle_model == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.vehiclemodel_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (fuel_quota == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.allocate_fuel_quota_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (engine_capacity == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.vehicle_engine_capacity_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (fuel_type == '' || fuel_type == null || fuel_type == undefined) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.fueltype_help')}}</p>");
            window.scrollTo(0, 0);
        } else if (password == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.password_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else {
            var checkPhone = await checkPhoneNumberExist(phone);
            if (checkPhone != true) {
                jQuery("#overlay").show();
                var user_id = database.collection("tmp").doc().id;
                storeProfileData().then(ProfileIMG => {
                    storeImageData().then(IMG => {
                        storeRegNoImage().then(BusinessImage => {
                            storeRegCertificateImage().then(CertiImage => {
                                database.collection('users').doc(user_id).set({
                                    'fullName': fullName,
                                    'countryCode': countryCode,
                                    'mobileNumber': phone,
                                    'id': user_id,
                                    'vehicleNumber': vehicleNo,
                                    'vehicleModal': vehicle_model,
                                    'vehicleId': vehicle_id,
                                    'role': 'customer',
                                    'documentApproved': false,
                                    'profilePic': ProfileIMG,
                                    'fuelTypeDesc': fuelTypeDetail,
                                    'fuelTypeId': fuelTypeId,
                                    'allocatedFuelDate': createdAt,
                                    'createdAt': createdAt,
                                    'availableFuelQuota': fuel_quota,
                                    'totalFuelQuota': fuel_quota,
                                    'vEngineCapacity': engine_capacity,
                                    'vehicleChassisNumber': IMG,
                                    'vehicleRegistrationcertificate': CertiImage,
                                    'vehicleRegistrationNumber': BusinessImage,
                                    'password':password,
                                    'cityId': cityId,
                                    'stateId': stateId,
                                    'pumpOwnerId': pumpOwnerId,
                                    'isActive': isActive,
                                    'deactiveReason': deactiveReason
                                }).then(async function (result) {
                                    logFirebaseActivity('#' + user_id + ' Customer Created');
                                    window.location.href = '{{ route("customers")}}';
                                });
                            }).catch(function (error) {
                                jQuery("#overlay").hide();
                                $(".error_top").show();
                                $(".error_top").html("");
                                $(".error_top").append("<p>" + error + "</p>");
                            })
                        }).catch(function (error) {
                            jQuery("#overlay").hide();
                            $(".error_top").show();
                            $(".error_top").html("");
                            $(".error_top").append("<p>" + error + "</p>");
                        })
                    }).catch(function (error) {
                        jQuery("#overlay").hide();
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>" + error + "</p>");
                    })
                }).catch(function (error) {
                    jQuery("#overlay").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>" + error + "</p>");
                })
            } else {
                jQuery("#overlay").hide();
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.phone_already_exist')}}</p>");
            }

        }
    })

    var storageRef = firebase.storage().ref('images');
    async function storeImageData() {
        var newPhoto = chassisObj;
        try {
            if (photo != "") {
                photo = photo.replace(/^data:image\/[a-z]+;base64,/, "")
                var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', { contentType: 'image/jpg' });
                var downloadURL = await uploadTask.ref.getDownloadURL();
                newPhoto.url = downloadURL;
                photo = downloadURL;
            }
        } catch (error) {
            console.log("ERR ===", error);
        }
        return newPhoto;
    }
      async function storeProfileData() {
            var newPhoto = '';
            try {
                if (profilePhoto != "") {
                    profilePhoto = profilePhoto.replace(/^data:image\/[a-z]+;base64,/, "")
                    var uploadTask = await storageRef.child(profileFileName).putString(profilePhoto, 'base64', { contentType: 'image/jpg' });
                    var downloadURL = await uploadTask.ref.getDownloadURL();
                    newPhoto = downloadURL;
                    profilePhoto = downloadURL;
                }
            } catch (error) {
                console.log("ERR ===", error);
            }
            return newPhoto;
        }
    async function storeRegNoImage() {
        var newPhoto = regNoObj;
        try {
            if (vRegNoPhoto != "") {
                vRegNoPhoto = vRegNoPhoto.replace(/^data:image\/[a-z]+;base64,/, "")
                var uploadTask = await storageRef.child(vRegNoFileName).putString(vRegNoPhoto, 'base64', { contentType: 'image/jpg' });
                var downloadURL = await uploadTask.ref.getDownloadURL();
                newPhoto.url = downloadURL;
                vRegNoPhoto = downloadURL;
            }
        } catch (error) {
            console.log("ERR ===", error);
        }
        return newPhoto;
    }

    async function storeRegCertificateImage() {
        var newPhoto = regCertificateObj;
        try {
            if (vRegPhoto != "") {
                vRegPhoto = vRegPhoto.replace(/^data:image\/[a-z]+;base64,/, "")
                var uploadTask = await storageRef.child(vRegFileName).putString(vRegPhoto, 'base64', { contentType: 'image/jpg' });
                var downloadURL = await uploadTask.ref.getDownloadURL();
                newPhoto.url = downloadURL;
                vRegPhoto = downloadURL;
            }
        } catch (error) {
            console.log("ERR ===", error);
        }
        return newPhoto;
    }

    function handleFileSelect(evt, type) {
        var f = evt.target.files[0];
        var reader = new FileReader();

        reader.onload = (function (theFile) {
            return function (e) {

                var filePayload = e.target.result;
                var val = f.name;
                var ext = val.split('.')[1];
                var docName = val.split('fakepath')[1];
                var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                var timestamp = Number(new Date());
                var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                  if (type == "profile") {
                    profilePhoto = filePayload;
                    profileFileName = filename;
                   
                    $(".profile_img").empty();
                    $(".profile_img").append('<img class="rounded" style="width:50px" src="' + profilePhoto + '" alt="image">');
                }
                if (type == "chassis_no") {
                    photo = filePayload;
                    fileName = filename;
                    chassisObj = {
                        'name': filename,
                        'path': '',
                        size: ''
                    }
                    $(".chassis_no").empty();
                    $(".chassis_no").append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image">');
                }
                if (type == "registration_certificate") {
                    vRegPhoto = filePayload;
                    vRegFileName = filename;
                    regCertificateObj = {
                        'name': filename,
                        'path': '',
                        size: ''
                    }
                    $(".registration_certificate").empty();
                    $(".registration_certificate").append('<img class="rounded" style="width:50px" src="' + vRegPhoto + '" alt="image">');

                }
                if (type == "registration_number") {
                    vRegNoPhoto = filePayload;
                    vRegNoFileName = filename;
                    regNoObj = {
                        'name': filename,
                        'path': '',
                        size: ''
                    }
                    $(".registration_number").empty();
                    $(".registration_number").append('<img class="rounded" style="width:50px" src="' + vRegNoPhoto + '" alt="image">');
                }
            };
        })(f);
        reader.readAsDataURL(f);
    }


    function chkAlphabets(event, msg) {
        if (!(event.which >= 97 && event.which <= 122) && !(event.which >= 65 && event.which <= 90)) {
            document.getElementById(msg).innerHTML = "Accept only Alphabets";
            return false;
        }
        else {
            document.getElementById(msg).innerHTML = "";
            return true;
        }
    }

    function chkAlphabets2(event, msg) {
        if (!(event.which >= 48 && event.which <= 57)
        ) {
            document.getElementById(msg).innerHTML = "Accept only Number";
            return false;
        }
        else {
            document.getElementById(msg).innerHTML = "";
            return true;
        }
    }

    $('input[name="fuel_type"]').on('change', function () {

        var checkedVal = $("input[type='radio'][name='fuel_type']:checked").val();

        if (checkedVal == 'other') {
            $('.other_detail_div').removeClass('d-none');
        } else {
            $('.other_detail_div').addClass('d-none');
        }
    })
    async function checkPhoneNumberExist(phoneNumber) {
        var checkPhone = false;
        await database.collection('users').where('mobileNumber', '==', phoneNumber).get().then(async function (snapshots) {
            if (snapshots.docs.length > 0) {
                checkPhone = true;
            }
        })
        return checkPhone;
    }
</script>
@endsection