@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.vehicle_add')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a
                        href="{!! route('vehicle-type') !!}">{{trans('lang.vehicle_type_table')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.vehicle_add')}}</li>
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
                            <legend>{{trans('lang.vehicle_type')}}</legend>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.vehicle_name')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control vehicle_name">
                                    <div class="form-text text-muted">
                                        {{ trans("lang.vehicle_name_help") }}
                                    </div>
                                </div>
                            </div>

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
                    <button type="button" class="btn btn-primary  create_vehicle_btn"><i class="fa fa-save"></i> {{
    trans('lang.save')}}
                    </button>
                    <a href="{!! route('vehicle-type') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
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

    $(document).ready(async function () {

        $('.vehicle_type_menu').addClass('active');
      
    });
    
    var fueltypeRef = database.collection('fuel_type').where('enable', '==', true).orderBy('placeId','asc');
    fueltypeRef.get().then(async function (snapshot) {
        if (snapshot.docs.length > 0) {
            var html = '';
            var mileageHtml = '';

            snapshot.docs.forEach((listval) => {
                var data = listval.data();
                html = html + '<div class="form-group row width-100">';
                html = html + '<label class="col-3 control-label">{{trans('lang.fuel_quota')}} (' + data.name + ' )</label>'
                html = html + '<div class="col-7">';
                html = html + '<input type="number" class="fuel_quota form-control" id="' + data.name + '" value="' + data.name + '"  data-id="' + data.id + '" name="fuel_quota">';
                html = html + '</div></div>';

                 // ---- MILEAGE QUOTA ----
                    mileageHtml += '<div class="form-group row width-100">';
                    mileageHtml += '<label class="col-3 control-label">{{trans("lang.mileage_quota")}} (' + data.name + ')</label>';
                    mileageHtml += '<div class="col-7">';
                    mileageHtml += '<input type="number" class="mileage_quota form-control" data-id="' + data.id + '" name="mileage_quota">';
                    mileageHtml += '</div></div>';

            })
            $('#fuel_quota_div').html(html);
              $('#mileage_quota_div').html(mileageHtml);
        }
    })

    $(".create_vehicle_btn").click(function () {

        var createdAt=firebase.firestore.FieldValue.serverTimestamp();
        var name = $(".vehicle_name").val();
        var fuelQuotaArr = [];
        var checkEmptyQuota = 0;
        var fuel_quota = $(".fuel_quota").each(function () {
            var fuelQuota = $(this).val();
            if (fuelQuota != '') {
                checkEmptyQuota += 1;
                var fuelTypeId = $(this).attr('data-id');
                obj = { 'fuelTypeId': fuelTypeId, 'fuelQuota': fuelQuota }
                fuelQuotaArr.push(obj);
            }

        })
        var mileageArr = [];
           // Mileage quota
        $(".mileage_quota").each(function () {
            var mileage = $(this).val();
            if (mileage != '') {
                var fuelTypeId = $(this).attr('data-id');
                mileageArr.push({ 'fuelTypeId': fuelTypeId, 'mileage': mileage });
            }
        });
        var enable = false;

        if ($(".vehicle_active").is(':checked')) {
            enable = true;
        }
        var refill_by_days = false;

        if ($("#refill_by_days").is(':checked')) {
            refill_by_days = true;
        }
        var id = database.collection("tmp").doc().id;

        if (name == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.vehicle_name_error')}}</p>");
            window.scrollTo(0, 0);
        }
        else if (checkEmptyQuota == 0) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_atleast_one_fuel_type_quota_error')}}</p>");
            window.scrollTo(0, 0);
        }

        else {
            jQuery("#overlay").show();

            database.collection('vehicle_type').doc(id).set({
                'name': name,
                'fuelQuota': fuelQuotaArr,
                'mileage': mileageArr,
                'id': id,
                'enable': enable,
                'refill_by_days':refill_by_days,
                'lastUpdatedDiesel': createdAt,
                'lastUpdatedOther': createdAt,
                'lastUpdatedPetrol': createdAt,
            }).then(function (result) {
                jQuery("#overlay").hide();
                logFirebaseActivity('#' + id + ' {{trans("lang.vehicle_type")}} {{trans("lang.created")}}');
                window.location.href = '{{ route("vehicle-type")}}';
            }).catch(function (error) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>" + error + "</p>");
            });
        }
    });


</script>
@endsection