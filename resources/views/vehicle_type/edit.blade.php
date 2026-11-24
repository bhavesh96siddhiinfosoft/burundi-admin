@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.vehicle_edit')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('vehicle-type') !!}">{{trans('lang.vehicle_type_table')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.vehicle_edit')}}</li>
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
                            <legend>{{trans('lang.vehicle_type')}}</legend>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.vehicle_name')}}<span class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control vehicle_name">
                                    <div class="form-text text-muted">
                                        {{ trans("lang.vehicle_name_help") }}
                                    </div>
                                </div>
                            </div>

                            {{-- Fuel Quota --}}
                            <span id="fuel_quota_div"></span>

                            {{-- Mileage Quota --}}
                            <span id="mileage_quota_div"></span>

                            <div class="form-group row width-100">
                                <div class="form-check">
                                    <input type="checkbox" class="vehicle_active" id="active">
                                    <label class="col-3 control-label" for="active">{{trans('lang.enable')}}</label>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <div class="form-check">
                                    <input type="checkbox" class="refill_by_days" id="refill_by_days">
                                    <label class="col-3 control-label" for="refill_by_days">{{trans('lang.refill_by_vehicle_days')}}</label>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary edit_vehicle_btn">
                        <i class="fa fa-save"></i> {{ trans('lang.save') }}
                    </button>
                    <a href="{!! route('vehicle-type') !!}" class="btn btn-default">
                        <i class="fa fa-undo"></i> {{ trans('lang.cancel') }}
                    </a>
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
    var ref = database.collection('vehicle_type').where("id", "==", id);

    $(document).ready(async function () {
        $('.vehicle_type_menu').addClass('active');
        jQuery("#overlay").show();

        var fueltypeRef = database.collection('fuel_type').where('enable', '==', true).orderBy('placeId', 'asc');
        fueltypeRef.get().then(async function (snapshot) {
            if (snapshot.docs.length > 0) {
                var fuelHtml = '';
                var mileageHtml = '';

                snapshot.docs.forEach((listval) => {
                    var data = listval.data();

                    // ---- FUEL QUOTA ----
                    fuelHtml += '<div class="form-group row width-100">';
                    fuelHtml += '<label class="col-3 control-label">{{trans("lang.fuel_quota")}} (' + data.name + ')</label>';
                    fuelHtml += '<div class="col-7">';
                    fuelHtml += '<input type="number" class="fuel_quota form-control" data-id="' + data.id + '" name="fuel_quota">';
                    fuelHtml += '</div></div>';

                    // ---- MILEAGE QUOTA ----
                    mileageHtml += '<div class="form-group row width-100">';
                    mileageHtml += '<label class="col-3 control-label">{{trans("lang.mileage_quota")}} (' + data.name + ')</label>';
                    mileageHtml += '<div class="col-7">';
                    mileageHtml += '<input type="number" class="mileage_quota form-control" data-id="' + data.id + '" name="mileage_quota">';
                    mileageHtml += '</div></div>';
                });

                $('#fuel_quota_div').html(fuelHtml);
                $('#mileage_quota_div').html(mileageHtml);
            }
        });

        // Load existing vehicle data
        ref.get().then(async function (snapshots) {
            if (snapshots.docs.length) {
                var data = snapshots.docs[0].data();
                $(".vehicle_name").val(data.name);

                // Fuel quota set
                if (data.fuelQuota) {
                    data.fuelQuota.forEach(element => {
                        $(".fuel_quota").each(function () {
                            if ($(this).attr('data-id') == element.fuelTypeId) {
                                $(this).val(element.fuelQuota);
                            }
                        });
                    });
                }

                // Mileage quota set
                if (data.mileage) {
                    data.mileage.forEach(element => {
                        $(".mileage_quota").each(function () {
                            if ($(this).attr('data-id') == element.fuelTypeId) {
                                $(this).val(element.mileage);
                            }
                        });
                    });
                }

                if (data.enable) $("#active").prop('checked', true);
                if (data.refill_by_days) $("#refill_by_days").prop('checked', true);
            }
            jQuery("#overlay").hide();
        });
    });

    $(".edit_vehicle_btn").click(function () {
        var is_disable_delete = "{{ env('IS_DISABLE_DELETE', 0) }}";
        if (is_disable_delete == 1) {
            alert(doNotUpdateAlert);
            return false;
        }

        var name = $(".vehicle_name").val();
        var fuelQuotaArr = [];
        var mileageArr = [];
        var checkEmptyQuota = 0;

        // Fuel quota
        $(".fuel_quota").each(function () {
            var fuelQuota = $(this).val();
            if (fuelQuota != '') {
                checkEmptyQuota++;
                var fuelTypeId = $(this).attr('data-id');
                fuelQuotaArr.push({ 'fuelTypeId': fuelTypeId, 'fuelQuota': fuelQuota });
            }
        });

        // Mileage quota
        $(".mileage_quota").each(function () {
            var mileage = $(this).val();
            if (mileage != '') {
                var fuelTypeId = $(this).attr('data-id');
                mileageArr.push({ 'fuelTypeId': fuelTypeId, 'mileage': mileage });
            }
        });

        var enable = $(".vehicle_active").is(':checked');
        var refill_by_days = $("#refill_by_days").is(':checked');

        if (name == '') {
            $(".error_top").show().html("<p>{{trans('lang.vehicle_name_error')}}</p>");
            window.scrollTo(0, 0);
        } else if (checkEmptyQuota == 0) {
            $(".error_top").show().html("<p>{{trans('lang.enter_atleast_one_fuel_type_quota_error')}}</p>");
            window.scrollTo(0, 0);
        } else {
            jQuery("#overlay").show();
            database.collection('vehicle_type').doc(id).update({
                'name': name,
                'fuelQuota': fuelQuotaArr,
                'mileage': mileageArr,
                'enable': enable,
                'refill_by_days': refill_by_days
            }).then(function () {
                jQuery("#overlay").hide();
                logFirebaseActivity('#' + id + ' {{trans("lang.vehicle_type")}} {{trans("lang.updated")}}');
                window.location.href = '{{ route("vehicle-type")}}';
            }).catch(function (error) {
                $(".error_top").show().html("<p>" + error + "</p>");
            });
        }
    });
</script>
@endsection
