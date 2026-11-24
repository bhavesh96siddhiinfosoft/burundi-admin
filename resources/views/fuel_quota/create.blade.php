@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.add_fuel_quota')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a
                        href="{{route('fuel-quota.index')}}">{{trans('lang.fuel_quota_list')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.add_fuel_quota')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card pb-4">
            <div class="card-body p-2">

                <div class="error_top"></div>

                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{trans('lang.fuel_quota_settings')}}</legend>
                            <div class="form-group row width-50">
                                <label class="col-5 control-label">{{trans('lang.total_petrol')}}<span
                                        class="required-field"></span></label>

                                <div class="col-7">
                                    <input type="text" class="form-control total_petrol">
                                </div>

                            </div>


                            <div class="form-group row width-50">
                                <label class="col-5 control-label">{{trans('lang.total_diesel')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control total_diesel">
                                </div>
                            </div>


                            <div class="form-group row width-50">
                                <label class="col-5 control-label">{{trans('lang.total_other')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control total_other">
                                </div>
                            </div>

                        </fieldset>
                    </div>

                </div>

                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary  create_fuel_quota_btn"><i class="fa fa-save"></i> {{
                        trans('lang.save')}}
                    </button>
                    <a href="{{route('fuel-quota.index')}}" class="btn btn-default"><i class="fa fa-undo"></i>{{
                        trans('lang.cancel')}}</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')

<script>

    var database = firebase.firestore();
    var photo = "";
    var database = firebase.firestore();
    var ref = database.collection('settings').doc('fuelQuota');
    var availablePetrol = '';
    var availableDiesel = '';
    var availableOther = '';
    var existingPetrol = '';
    var existingDiesel = '';
    var existingOther = '';
    $(document).ready(async function () {


        ref.get().then(async function (snapshots) {
            var settingData = snapshots.data();
            if (settingData == undefined) {
                var petrol = { 'name': 'petrol', 'total': '', 'available': '' };
                var diesel = { 'name': 'diesel', 'total': '', 'available': '' };
                var other = { 'name': 'other', 'total': '', 'available': '' };
               await database.collection('settings').doc('fuelQuota').set({
                    'petrol': petrol,
                    'diesel': diesel,
                    'other': other
                });
            }
        });
    });

    database.collection('settings').doc('fuelQuota').get().then(async function (snapshots) {
        var settingData = snapshots.data();
        if (settingData.hasOwnProperty('petrol') && settingData.hasOwnProperty('diesel') && settingData.hasOwnProperty('other')) {
            availablePetrol = settingData.petrol.available;
            availableDiesel = settingData.diesel.available;
            availableOther = settingData.other.available;
            existingDiesel = settingData.diesel.total;
            existingOther = settingData.other.total;
            existingPetrol = settingData.petrol.total;
            $(".total_petrol").val(settingData.petrol.total);
            $(".total_diesel").val(settingData.diesel.total);
            $(".total_other").val(settingData.other.total);
        }

    });

    $(".create_fuel_quota_btn").click(function () {
        var totalPetrol = $(".total_petrol").val();
        var totalDiesel = $(".total_diesel").val();
        var totalOther = $(".total_other").val();

        if (totalPetrol == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_petrol_error')}}</p>");
            window.scrollTo(0, 0);
        } else if (totalDiesel == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_diesel_error')}}</p>");
            window.scrollTo(0, 0);
        } else if (totalOther == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_other_error')}}</p>");
            window.scrollTo(0, 0);
        } else {
            jQuery("#overlay").show();
            var newTotalPetrol = totalPetrol;
            var newTotalDiesel = totalDiesel;
            var newTotalOther = totalOther;
            var newAvailablePetrol = totalPetrol;
            var newAvailableDiesel = totalDiesel;
            var newAvailableOther = totalOther;
            if (availablePetrol != '' && parseInt(availablePetrol) > 0 && existingPetrol != totalPetrol) {
                newTotalPetrol = parseInt(totalPetrol) + parseInt(existingPetrol);
                newAvailablePetrol = parseInt(totalPetrol) + parseInt(availablePetrol);
                newTotalPetrol = newTotalPetrol.toString();
            }
            if (availableDiesel != '' && parseInt(availableDiesel) > 0 && existingDiesel != totalDiesel) {
                newTotalDiesel = parseInt(totalDiesel) + parseInt(existingDiesel);
                newAvailableDiesel = parseInt(totalDiesel) + parseInt(availableDiesel);
                newTotalDiesel = newTotalDiesel.toString();
            }
            if (availableOther != '' && parseInt(availableOther) > 0 && existingOther != totalOther) {
                newTotalOther = parseInt(totalOther) + parseInt(existingOther);
                newAvailableOther = parseInt(totalOther) + parseInt(availableOther);
                newTotalOther = newTotalOther.toString();
            }

            database.collection('settings').doc('fuelQuota').update({
                'petrol': { 'name': 'petrol', 'total': newTotalPetrol, 'available': newAvailablePetrol },
                'diesel': { 'name': 'diesel', 'total': newTotalDiesel, 'available': newAvailableDiesel },
                'other': { 'name': 'other', 'total': newTotalOther, 'available': newAvailableOther },

            }).then(async function (result) {
                var id = database.collection('tmp').doc().id;
                await database.collection('fuel_reset_transaction').doc(id).set({
                    'petrol': { 'newAdded': $(".total_petrol").val(), 'total': newTotalPetrol, 'available': newAvailablePetrol },
                    'diesel': { 'newAdded': $(".total_diesel").val(), 'total': newTotalDiesel, 'available': newAvailableDiesel },
                    'other': { 'newAdded': $(".total_other").val(), 'total': newTotalOther, 'available': newAvailableOther },
                    'date': firebase.firestore.FieldValue.serverTimestamp(),
                    'type':'addon'
                })
                logFirebaseActivity("{{trans('lang.fuel_quota')}} {{trans('lang.created')}}");
                window.location.href = '{{ url("settings/fuel-quota")}}';

            });


        }
    });



</script>
@endsection