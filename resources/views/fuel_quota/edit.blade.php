@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.edit_fuel_quota')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a
                        href="{{route('fuel-quota.index')}}">{{trans('lang.fuel_quota_list')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.edit_fuel_quota')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card pb-4">
            <div class="card-body">

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
                    <button type="button" class="btn btn-primary  edit_fuel_quota_btn"><i class="fa fa-save"></i> {{
                        trans('lang.save')}}
                    </button>
                    <a href="{{route('fuel-quota.index')}}" class="btn btn-default"><i class="fa fa-undo"></i>{{
                        trans('lang.cancel')}}</a>
                </div>

            </div>

        </div>
    </div>
</div>

</div>
</div>
@endsection

@section('scripts')


<script>

    var id = "{{$id}}";
    var database = firebase.firestore();
    var ref = database.collection('settings').doc("fuelQuota");
    $(document).ready(async function () {

        var database = firebase.firestore();
        var ref = database.collection('settings').doc('fuelQuota');
        var availablePetrol = '';
        var availableDiesel = '';
        var availableOther = '';
        $(document).ready(function () {

            ref.get().then(async function (snapshots) {
                var settingData = snapshots.data();
                $(".total_petrol").val(settingData.petrol.total);
                $(".total_diesel").val(settingData.diesel.total);
                $(".total_other").val(settingData.other.total);
            });

            $(".edit_fuel_quota_btn").click(function () {
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

                    database.collection('settings').doc('fuelQuota').update({
                        'petrol': { 'name': 'petrol', 'total': totalPetrol, 'available': totalPetrol },
                        'diesel': { 'name': 'diesel', 'total': totalDiesel, 'available': totalDiesel },
                        'other': { 'name': 'other', 'total': totalOther, 'available': totalOther },
                    }).then(async function (result) {
                        var id = database.collection('tmp').doc().id;
                        await database.collection('fuel_reset_transaction').doc(id).set({
                            'petrol': { 'newAdded': '', 'total': totalPetrol, 'available': totalPetrol },
                            'diesel': { 'newAdded': '', 'total': totalDiesel, 'available': totalDiesel },
                            'other': { 'newAdded': '', 'total': totalOther, 'available': totalOther },
                            'date': firebase.firestore.FieldValue.serverTimestamp(),
                            'type': 'reset'
                        })
                        logFirebaseActivity("{{trans('lang.fuel_quota')}} {{trans('lang.updated')}}");
                        window.location.href = '{{ url("settings/fuel-quota")}}';

                    });


                }
            });
        })

    });

</script>
@endsection