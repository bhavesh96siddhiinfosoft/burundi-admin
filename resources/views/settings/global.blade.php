@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.app_setting_global')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.app_setting_global')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div id="data-table_processing" class="dataTables_processing panel panel-default"
                    style="display: none;">{{trans('lang.processing')}}</div>
                <div class="error_top" style="display:none"></div>

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
                            <div class="form-group row width-50 mt-3">
                                <button class="btn btn-primary mt-3"><i class="fa fa-plus font-weight-bold"></i></button>
                                <button class="btn btn-primary mt-3"><i class="fa  fa-edit"></i></button>
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

                            <div class="form-group row width-50">
                                <label class="col-5 control-label">{{trans('lang.available_petrol')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control available_petrol" readonly>
                                </div>
                            </div>

                            <div class="form-group row width-50">
                                <label class="col-5 control-label">{{trans('lang.available_diesel')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control available_diesel" readonly>
                                </div>
                            </div>


                            <div class="form-group row width-50">
                                <label class="col-5 control-label">{{trans('lang.available_other')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control available_other" readonly>
                                </div>
                            </div>

                        </fieldset>

                    </div>
                </div>
                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary save_global_settings_btn"><i class="fa fa-save"></i>
                        {{trans('lang.save')}}</button>
                    <a href="{{url('/dashboard')}}" class="btn btn-default"><i
                            class="fa fa-undo"></i>{{trans('lang.cancel')}}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @endsection

    @section('scripts')

    <script>
        var database = firebase.firestore();
        var ref = database.collection('settings').doc('fuelQuota');
        var isQuotaExist = false;
        var availablePetrol = '';
        var availableDiesel = '';
        var availableOther = '';
        $(document).ready(function () {

            ref.get().then(async function (snapshots) {
                var settingData = snapshots.data();
                if (settingData == undefined) {
                    database.collection('settings').doc('fuelQuota').set({
                        'totalPetrol': '',
                        'totalDiesel': '',
                        'totalOther': '',
                        'availablePetrol': '',
                        'availableDiesel': '',
                        'availableOther': ''
                    });
                }

                $(".total_petrol").val(settingData.totalPetrol);
                $(".total_diesel").val(settingData.totalDiesel);
                $(".total_other").val(settingData.totalOther);
                $(".available_petrol").val(settingData.availablePetrol);
                $(".available_diesel").val(settingData.availableDiesel);
                $(".available_other").val(settingData.availableOther);

                if (settingData.hasOwnProperty('totalPetrol') && settingData.hasOwnProperty('totalDiesel') && settingData.hasOwnProperty('totalOther')) {
                    isQuotaExist = true;
                    availablePetrol = settingData.availablePetrol;
                    availableDiesel = settingData.availableDiesel;
                    availableOther = settingData.availableOther;
                }

            });

            $(".save_global_settings_btn").click(function () {
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
                    if (availablePetrol != '' && parseInt(availablePetrol) > 0) {
                        totalPetrol += availablePetrol;
                    }
                    if (availableDiesel != '' && parseInt(availableDiesel) > 0) {
                        totalDiesel += availableDiesel;
                    }
                    if (availableOther != '' && parseInt(availableOther) > 0) {
                        totalOther += availableOther;
                    }

                    database.collection('settings').doc('fuelQuota').update({
                        'totalPetrol': totalPetrol,
                        'totalDiesel': totalDiesel,
                        'totalOther': totalOther,
                        'availablePetrol': totalPetrol,
                        'availableDiesel': totalDiesel,
                        'availableOther': totalOther
                    }).then(async function (result) {
                        var id = database.collection('tmp').doc().id;
                        await database.collection('fuel_reset_transaction').doc(id).set({
                            'totalPetrol': totalPetrol,
                            'totalDiesel': totalDiesel,
                            'totalOther': totalOther,
                            'availablePetrol': totalPetrol,
                            'availableDiesel': totalDiesel,
                            'availableOther': totalOther,
                            'date': firebase.firestore.FieldValue.serverTimestamp(),
                        })
                          logFirebaseActivity('{{trans("lang.fuel_quota")}} {{trans("lang.updated")}}');
                        window.location.href = '{{ url("settings/globals")}}';

                    });


                }
            });
        })

    </script>

    @endsection