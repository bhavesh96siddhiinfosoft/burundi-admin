@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.owners')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('owners') !!}">{{trans('lang.owners')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.owners_create')}}</li>
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
                            <legend>{{trans('lang.owners_create')}}</legend>
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
                                <label class="col-3 control-label">{{trans('lang.password')}}<span class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control password">
                                    <div class="form-text text-muted">
                                        {{ trans("lang.password_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.fuel_station_name')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control fuel_station_name">
                                    <div class="form-text text-muted">
                                        {{ trans("lang.fuel_station_name_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.fuel_station_address')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control fuel_station_address">
                                    <div class="form-text text-muted">
                                        {{ trans("lang.fuel_station_address_help") }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.select_fuel_type')}}<span
                                        class="required-field"></span></label>

                                <div id="fuel_type_div"></div>

                            </div>
                            <div id="owner_fuel_capacity_div"></div>
                            <div id="fuel_capacity_div"></div>

                            
                            <div class="form-group row width-50">
                                <label
                                    class="col-3 control-label">{{trans('lang.business_registration_certificate')}}<span
                                        class="required-field"></span></label>
                                <input type="file" onChange="handleFileSelect(event,'business_registration')"
                                    class="col-7">
                                <div class="placeholder_img_thumb business_registration"></div>
                                <div id="uploding_image"></div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.fuel_station_image')}}<span
                                        class="required-field"></span></label>
                                <input type="file" onChange="handleFileSelect(event,'petrol_pump_documents')"
                                    class="col-7">
                                <div class="placeholder_img_thumb petrol_pump_documents"></div>
                                <div id="uploding_image"></div>
                            </div>

                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary  save_user_btn"><i class="fa fa-save"></i> {{
    trans('lang.save')}}</button>
                <a href="{!! route('owners') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
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
    var passportPhoto = "";
    var passportFileName = "";
    var businessPhoto = "";
    var businessFileName = "";
    var documentPhoto = '';
    var documentFileName = "";
    var fuelStationObj = {};
    var registrationCertificateObj = {};
    $('.phone_code').select2();
    var fueltypeRef = database.collection('fuel_type').where('enable', '==', true);
    $(document).ready(function () {
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

        fueltypeRef.get().then(async function (snapshot) {
            if (snapshot.docs.length > 0) {
                var html = '';
                var capacityhtml = '';
                var ownerAddedCapacity='';
                snapshot.docs.forEach((listval) => {
                    var data = listval.data();
                    html = html + '<div class="form-check">';
                    html = html + '<input type="checkbox" class="fuel_type" id="' + data.name + '" value="' + data.name + '"  data-id="' + data.id + '" name="fuel_type" onclick="toggleCapacityFields()">';
                    html = html + '<label class="col-3 control-label" for="' + data.name + '">' + data.name + '</label>';
                    html = html + '</div>';
                    ownerAddedCapacity = ownerAddedCapacity + '<div class="form-group row width-50 d-none owner_' + data.name + '_capacity">';
                    ownerAddedCapacity = ownerAddedCapacity + '<label class="col-3 control-label">{{trans('lang.fuel_capacity')}} ' +data.name+ ' ({{trans('lang.in_litre')}})<span class="required-field"></span></label>';
                    ownerAddedCapacity = ownerAddedCapacity + '<div class="col-7">';
                    ownerAddedCapacity = ownerAddedCapacity + '<input type="text" class="form-control" id="owner_' + data.name + '_capacity">';
                    ownerAddedCapacity = ownerAddedCapacity + '</div>';
                    ownerAddedCapacity = ownerAddedCapacity + '</div>';
                    capacityhtml = capacityhtml + '<div class="form-group row width-50 d-none ' + data.name + '_capacity">';
                    capacityhtml = capacityhtml + '<label class="col-3 control-label">{{trans('lang.allocate_fuel_capacity')}} ' +data.name+ ' ({{trans('lang.in_litre')}})<span class="required-field"></span></label>';
                    capacityhtml = capacityhtml + '<div class="col-7">';
                    capacityhtml = capacityhtml + '<input type="text" class="form-control" id="' + data.name + '_capacity">';
                    capacityhtml = capacityhtml + '</div>';
                    capacityhtml = capacityhtml + '</div>';
                })
                $('#fuel_type_div').html(html);
                $('#owner_fuel_capacity_div').html(ownerAddedCapacity);
                $('#fuel_capacity_div').html(capacityhtml);
            }
        })
        $('.fuel_type').on('click', function () {
            toggleCapacityFields();
        });

    });
    function toggleCapacityFields() {

        $('.fuel_type').each(function () {
            var checkbox = $(this);
            var value = checkbox.val();

            var capacityFieldClass = '.' + value + '_capacity';
            var ownerCapacityFieldClass= '.owner_' + value + '_capacity';
            if (checkbox.is(':checked')) {
                $(capacityFieldClass).removeClass('d-none');
                $(ownerCapacityFieldClass).removeClass('d-none');
            } else {
                $(capacityFieldClass).addClass('d-none');
                $(ownerCapacityFieldClass).addClass('d-none');
            }
        });
    }
    $(".save_user_btn").click(async function () {

        var fullName = $(".full_name").val();
        var phone = $(".phone").val();
        var countryCode = $('.country_code').val();
        var fuelStationName = $('.fuel_station_name').val();
        var address = $('.fuel_station_address').val();
        var stateId = $('#state_select_box').val();
        var cityId = $('#city_select_box').val();
        var password = btoa($('.password').val());
        var id = "<?php echo uniqid(); ?>";
        var selectedCheckbox = false;
        var fuel = [];
        var checkCapacityGreaterCount = 0;
        var checkEmptyCapacityValCount = 0;
        var checkEmptyownerCapacityCount=0;
        $('.fuel_type').each(function () {
            var checkbox = $(this);
            var value = checkbox.val();
            var fuelTypeId = $(this).attr('data-id');
            var capacityFieldClass = '.' + value + '_capacity';
            var ownerCapacity = $('#owner_' + value + '_capacity').val();
            if (checkbox.is(':checked')) {
                selectedCheckbox = true;
                var capacityVal = $('#' + value + '_capacity').val();
                 if (capacityVal == '') {
                    checkEmptyCapacityValCount++;
                } else if (ownerCapacity=='') {
                     checkEmptyownerCapacityCount++;
                 } 
                 else if (capacityVal > ownerCapacity) {
                    checkCapacityGreaterCount++;
                } 
                else {
                    var fuelObj = {
                        'availableFuelQuota': capacityVal,
                        'fuelTypeId': fuelTypeId,
                        'fuelCapacity': ownerCapacity ,
                        'name': value,
                        'totalFuelQuota': capacityVal
                    }
                    fuel.push(fuelObj);
                }
            }
        });
        if (fullName == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.full_name_help')}}</p>");
            window.scrollTo(0, 0);

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
        }
        else if (phone == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.phone_help')}}</p>");
            window.scrollTo(0, 0);
        } else if (password == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.password_help')}}</p>");
            window.scrollTo(0, 0);
        }
       
        else if (businessPhoto == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.business_registration_photo_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (documentPhoto == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.petrol_pump_documents_help')}}</p>");
            window.scrollTo(0, 0);
        } else if (selectedCheckbox == false) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.select_fuel_type')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (address == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.fuel_station_address_help')}}</p>");
            window.scrollTo(0, 0);
        } else if (fuelStationName == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.fuel_station_name_help')}}</p>");
            window.scrollTo(0, 0);
        } else if (checkEmptyownerCapacityCount > 0) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.owner_fuel_capacity_err')}}</p>");
            window.scrollTo(0, 0);
        }
         else if (checkCapacityGreaterCount > 0) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.allocate_fuel_capacity_grater_err')}}</p>");
            window.scrollTo(0, 0);
        } else if (checkEmptyCapacityValCount > 0) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.allocate_fuel_capacity_empty_err')}}</p>");
            window.scrollTo(0, 0);
        } else if (fuel.length === 0) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.allocate_fuel_capacity_err')}}</p>");
            window.scrollTo(0, 0);
        }
        else {
            var checkPhone = await checkPhoneNumberExist(phone);
            if (checkPhone != true) {
            jQuery("#overlay").show();
            storeBusinessImageData().then(BusinessImage => {
                storeDocumentsImageData().then(DocumentImage => {

                    // The new station and its first allocation (fuel_receipts) are written together
                    var createBatch = database.batch();
                    fuel.forEach(function (fuelObj) {
                        if (parseFloat(fuelObj.totalFuelQuota) > 0) {
                            var receiptRef = database.collection('fuel_receipts').doc();
                            createBatch.set(receiptRef, {
                                'id': receiptRef.id,
                                'ownerId': id,
                                'fuelTypeId': fuelObj.fuelTypeId,
                                'fuelTypeName': fuelObj.name,
                                'litres': parseFloat(fuelObj.totalFuelQuota),
                                'receivedAt': createdAt,
                                'source': 'allocation',
                                'fuelRequestId': null,
                                'note': null,
                                'createdBy': '{{ auth()->id() }}',
                                'createdAt': createdAt
                            });
                        }
                    });
                    createBatch.set(database.collection('users').doc(id), {
                        'fullName': fullName,
                        'countryCode': countryCode,
                        'mobileNumber': phone,
                        'id': id,
                        'fuelStationAddress': address,
                        'fuelStationName': fuelStationName,
                        'cityId': cityId,
                        'stateId': stateId,
                        'registrationCertificate': BusinessImage,
                        'fuelStationImage': DocumentImage,
                        'createdAt': createdAt,
                        'documentApproved': false,
                        'role': 'owner',
                        'fuel': fuel,
                        'allocatedFuelDate': createdAt,
                        'password': password

                    });
                    return createBatch.commit().then(async function (result) {
                         logFirebaseActivity('#' + id + ' {{trans("lang.user")}} {{trans("lang.created")}}');
                        window.location.href = '{{ route("owners")}}';

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

        } else {
                jQuery("#overlay").hide();
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.phone_already_exist')}}</p>");
                window.scrollTo(0, 0);

            }
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

    var storageRef = firebase.storage().ref('images');
    async function storeDocumentsImageData() {
        var newPhoto = fuelStationObj;
        try {
            if (documentPhoto != "") {
                documentPhoto = documentPhoto.replace(/^data:image\/[a-z]+;base64,/, "")
                var uploadTask = await storageRef.child(documentFileName).putString(documentPhoto, 'base64', { contentType: 'image/jpg' });
                var downloadURL = await uploadTask.ref.getDownloadURL();
                newPhoto.url = downloadURL;
                documentPhoto = downloadURL;
            }
        } catch (error) {
            console.log("ERR ===", error);
        }
        return newPhoto;
    }
    async function storeBusinessImageData() {
        var newPhoto = registrationCertificateObj;
        try {
            if (businessPhoto != "") {
                businessPhoto = businessPhoto.replace(/^data:image\/[a-z]+;base64,/, "")
                var uploadTask = await storageRef.child(businessFileName).putString(businessPhoto, 'base64', { contentType: 'image/jpg' });
                var downloadURL = await uploadTask.ref.getDownloadURL();
                newPhoto.url = downloadURL;
                businessPhoto = downloadURL;
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
                    documentPhoto=filePayload;
                    documentFileName=filename;
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
    async function sendEmail(url, subject, message, recipients) {

        var checkFlag = false;

        await $.ajax({

            type: 'POST',
            data: {
                subject: subject,
                message: btoa(message),
                recipients: recipients
            },
            url: url,
            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                checkFlag = true;
            },
            error: function (xhr, status, error) {
                checkFlag = true;
            }
        });

        return checkFlag;

    }

    $(document).on("click", ".remove-btn", function () {
        var id = $(this).attr('data-id');
        var photo_remove = $(this).attr('data-img');
        $("#photo_" + id).remove();
        index = documentPhoto.indexOf(photo_remove);
        if (index > -1) {
            documentFileName.splice(index, 1); // 2nd parameter means remove one item only
        }

    });
</script>
@endsection