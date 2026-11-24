@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.employee')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('employee') !!}">{{trans('lang.employee')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.employee_create')}}</li>
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
                            <legend>{{trans('lang.employee_create')}}</legend>
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
                                <label class="col-3 control-label">{{trans('lang.profile_pic')}}</label>
                                <input type="file" onChange="handleFileSelect(event,'profile_pic')" class="col-7">
                                <div class="placeholder_img_thumb profile_pic"></div>
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
                                <label class="col-3 control-label">{{trans('lang.owner')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <select type="text" class="form-control owner" id="owner">

                                    </select>

                                </div>
                            </div>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.address')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control address">
                                    <div class="form-text text-muted">
                                        {{ trans("lang.address_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.state')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control state">
                                    <div class="form-text text-muted">
                                        {{ trans("lang.state_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.city')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control city">
                                    <div class="form-text text-muted">
                                        {{ trans("lang.city_help") }}
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>{{trans('lang.additional_information')}}</legend>
                            <div class="form-group row width-50" id="phone-box">
                                <label class="col-3 control-label">{{trans('lang.country_code')}}</label>
                                <div class="col-7">
                                    <select name="country" id="emergecy_country_selector"
                                        class="form-control emergecy_country_code phone_code">
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

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.emergency_phone')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control emergency_phone"
                                        onkeypress="return chkAlphabets2(event,'errorE')">
                                    <div id="errorE" class="err"></div>

                                    <div class="form-text text-muted w-50">
                                        {{ trans("lang.emergeny_phone_help") }}
                                    </div>
                                </div>

                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.notes')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control notes">
                                    <div class="form-text text-muted">
                                        {{ trans("lang.notes_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.passport_number')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control passport_number">
                                    <div class="form-text text-muted">
                                        {{ trans("lang.passport_number_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.nationalId/passport')}}<span
                                        class="required-field"></span></label>
                                <input type="file" onChange="handleFileSelect(event,'nationalId')" class="col-7">
                                <div class="placeholder_img_thumb nationalId"></div>
                            </div>

                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary  save_user_btn"><i class="fa fa-save"></i> {{
    trans('lang.save')}}</button>
                <a href="{!! route('employee') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
    trans('lang.cancel')}}</a>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
    var id = "{{$id}}";
    var database = firebase.firestore();
    var photo = "";
    var fileName = '';
    var nationIdPhoto = "";
    var nationalIdFileName = "";
    var nationalIdObj = {};
    $('.phone_code').select2();
    var placeholderImage = "{{ asset('/images/default_user.png') }}";
    var ref = database.collection('users').where('id', '==', id);
    var ownerRef = database.collection('users').where('role', '==', 'owner');
    $(document).ready(function () {
        jQuery("#overlay").show();
        ownerRef.get().then(async function (snapshot) {
            if (snapshot.docs.length > 0) {
                var html = '';
                snapshot.docs.forEach(doc => {
                    var data = doc.data();
                    $('#owner').append($("<option></option>")
                        .attr("value", data.id)
                        .text(data.fullName));
                })
            }
        })

        ref.get().then(async function (snapshots) {
            var user = snapshots.docs[0].data();
            $(".full_name").val(user.fullName);
            $(".phone").val(user.mobileNumber);
            if (user.countryCode != null) {
                countryCode = user.countryCode.includes('+') ? user.countryCode.slice(1) : user.countryCode;
                $('#country_selector').val(countryCode).trigger('change');
            }
            $(".emergency_phone").val(user.emergencyMobileNumber);
            if (user.emergencyCountryCode != null) {
                countryCode = user.emergencyCountryCode.includes('+') ? user.emergencyCountryCode.slice(1) : user.emergencyCountryCode;
                $('#emergecy_country_selector').val(countryCode).trigger('change');
            }
            if (user.profilePic != null && user.profilePic != '') {
                $(".profile_pic").append('<img class="rounded" style="width:50px" src="' + user.profilePic + '" alt="image">');
            } else {
                $(".profile_pic").append('<img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');

            }
            if (user.nationalIdImage.url != null && user.nationalIdImage.url != '') {
                nationIdPhoto=user.nationalIdImage.url;
                nationalIdObj= user.nationalIdImage;
                $(".nationalId").append('<img class="rounded" style="width:50px" src="' + user.nationalIdImage.url + '" alt="image">');
            } else {
                $(".nationalId").append('<img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');
            }
            $('#owner').val(user.ownerId);
            $(".notes").val(user.notes);
            $(".password").val(atob(user.password));
            $(".passport_number").val(user.passport);
            $(".state").val(user.state);
            $(".city").val(user.city);
            $(".address").val(user.address);
        });
        jQuery("#overlay").hide();
    });
    $(".save_user_btn").click(async function () {

        var fullName = $(".full_name").val();
        var phone = $(".phone").val();
        var countryCode = '+'+$('.country_code').val();
        var ePhone = $(".emergency_phone").val();
        var eCountryCode = '+'+$('.emergecy_country_code').val();
        var ownerId = $('#owner').val();
        var address = $('.address').val();
        var state = $('.state').val();
        var city = $('.city').val();
        var notes = $('.notes').val();
        var passport_number = $('.passport_number').val();
        var password = btoa($('.password').val());

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
        }
        else if (phone == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.phone_help')}}</p>");
            window.scrollTo(0, 0);
        }

        else if (nationIdPhoto == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.national_Id_photo_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (state == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.state_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (city == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.city_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (address == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.fuel_station_address_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (password == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.password_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else {
            var checkPhone = await checkPhoneNumberExist(phone);
            if (checkPhone != true) {
                jQuery("#overlay").show();
                storeImageData().then(IMG => {
                    storeDocumentsImageData().then(DocumentImage => {

                        database.collection('users').doc(id).update({
                            'fullName': fullName,
                            'countryCode': countryCode,
                            'mobileNumber': phone,
                            'address': address,
                            'state': state,
                            'city': city,
                            'emergencyCountryCode': eCountryCode,
                            'emergencyMobileNumber': ePhone,
                            'enable': true,
                            'role': 'employee',
                            'profilePic': IMG,
                            'nationalIdImage': DocumentImage,
                            'notes': notes,
                            'ownerId': ownerId,
                            'passport': passport_number,
                            'password': password
                        }).then(async function (result) {
                            logFirebaseActivity('#' + id + ' user updated');
                            window.location.href = '{{ route("employee")}}';

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

    var storageRef = firebase.storage().ref('images');
    async function storeDocumentsImageData() {
        var newPhoto = nationalIdObj;
        try {
            if (nationIdPhoto != "") {
                nationIdPhoto = nationIdPhoto.replace(/^data:image\/[a-z]+;base64,/, "")
                var uploadTask = await storageRef.child(nationalIdFileName).putString(nationIdPhoto, 'base64', { contentType: 'image/jpg' });
                var downloadURL = await uploadTask.ref.getDownloadURL();
                newPhoto.url = downloadURL;
                nationIdPhoto = downloadURL;
            }
        } catch (error) {
            console.log("ERR ===", error);
        }
        return newPhoto;
    }
    async function storeImageData() {
        var newPhoto = '';
        try {
            if (photo != "") {
                photo = photo.replace(/^data:image\/[a-z]+;base64,/, "")
                var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', { contentType: 'image/jpg' });
                var downloadURL = await uploadTask.ref.getDownloadURL();
                newPhoto = downloadURL;
                photo = downloadURL;
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

                if (type == "profile_pic") {
                    photo = filePayload;
                    fileName = filename;
                    $(".profile_pic").empty();
                    $(".profile_pic").append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image">');

                }

                if (type == "nationalId") {

                    photos_html = '<img width="100px" id="" height="auto" src="' + filePayload + '">';
                    $(".nationalId").append(photos_html);
                    nationIdPhoto = filePayload;
                    nationalIdFileName = filename;
                    nationalIdObj = {
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
    async function checkPhoneNumberExist(phoneNumber) {
        var checkPhone = false;
        await database.collection('users').where('mobileNumber', '==', phoneNumber).get().then(async function (snapshots) {
            if (snapshots.docs.length > 0) {
               snapshots.docs.forEach(listval => {
                    var data=listval.data();
                    if(data.id!=id){
                        checkPhone = true;
                    }
               });
                
            }
        })
        return checkPhone;
    }

</script>
@endsection