@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.vehicle_days_edit')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a
                        href="{!! route('vehicle-days') !!}">{{trans('lang.vehicle_days_table')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.vehicle_days_edit')}}</li>
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
                                <label class="col-3 control-label">{{trans('lang.vehicle_number_last_digit')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <select class="form-control vehicle_number">
                                        <option value='0'>0</option>
                                        <option value='1'>1</option>
                                        <option value='2'>2</option>
                                        <option value='3'>3</option>
                                        <option value='4'>4</option>
                                        <option value='5'>5</option>
                                        <option value='6'>6</option>
                                        <option value='7'>7</option>
                                        <option value='8'>8</option>
                                        <option value='9'>9</option>
                                    </select>
                                    <div class="form-text text-muted">
                                        {{ trans("lang.select_vehicle_number_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.days')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <select class="form-control vehicle_days" multiple>
                                        <option value='0'>{{trans('lang.monday')}}</option>
                                        <option value='1'>{{trans('lang.tuesday')}}</option>
                                        <option value='2'>{{trans('lang.wednesday')}}</option>
                                        <option value='3'>{{trans('lang.thursday')}}</option>
                                        <option value='4'>{{trans('lang.friday')}}</option>
                                        <option value='5'>{{trans('lang.saturday')}}</option>
                                        <option value='6'>{{trans('lang.sunday')}}</option>
                                    </select>
                                    <div class="form-text text-muted">
                                        {{ trans("lang.select_vehicle_days_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <div class="form-check">
                                    <input type="checkbox" class="isActive" id="isActive">
                                    <label class="col-3 control-label" for="isActive">{{ trans('lang.active')}}</label>
                                </div>
                            </div>

                        </fieldset>
                    </div>
                </div>

                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary  edit_vehicle_btn"><i class="fa fa-save"></i> {{
                        trans('lang.save')}}
                    </button>
                    <a href="{!! route('vehicle-days') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
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
    var ref = database.collection('vehicle_days').where("id", "==", id);

    $(document).ready(function () {

        $('.vehicle_days_menu').addClass('active');

        jQuery("#overlay").show();
        ref.get().then(async function (snapshots) {
            var data = snapshots.docs[0].data();

            $(".vehicle_number").val(data.lastDigit);
            $(".vehicle_days").val(data.days);
            if (data.enable) {
                $(".isActive").prop('checked', true);
            }
            jQuery("#overlay").hide();
        });
    });

    $(".edit_vehicle_btn").click(function () {

      
        var vehicleNumber = $(".vehicle_number").val();
        var vehicleDays = $(".vehicle_days").val();
        var vehicleDaysIntegers = vehicleDays.map(function (day) {
            return parseInt(day);
        });
        var active = $(".isActive").is(":checked");

        if (vehicleNumber == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.select_vehicle_number_help')}}</p>");
            window.scrollTo(0, 0);
        } else if (vehicleDays == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.select_vehicle_days_help')}}</p>");
            window.scrollTo(0, 0);
        }
        else {
            jQuery("#overlay").show();

            database.collection('vehicle_days').doc(id).update({
                'lastDigit': vehicleNumber,
                'days': vehicleDaysIntegers,
                'enable': active,
            }).then(function (result) {
                jQuery("#overlay").hide();
                window.location.href = '{{ route("vehicle-days")}}';
            }).catch(function (error) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>" + error + "</p>");
            });
        }
    });
</script>
@endsection