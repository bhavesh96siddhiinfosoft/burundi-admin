@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.vehicle_detail') }}</h3>
            </div>

            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('vehicle-type') !!}">{{ trans('lang.vehicle_type_table') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('lang.vehicle_detail') }}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card pb-4">
                <div class="card-body p-2">

                    <div class="error_top"></div>

                    <div class="row restaurant_payout_create w-auto max-w-inherit d-flex" style="max-width:30%">

                        <div class="restaurant_payout_create-inner col-md-4 m-0">
                            <fieldset>
                                <legend>{{ trans('lang.petrol') }}</legend>

                                <div class="form-group row width-100">
                                    <div id="petrol_vehicles"></div>

                                </div>
                            </fieldset>
                        </div>
                        <div class="restaurant_payout_create-inner col-md-4 m-0">
                            <fieldset>

                                <legend>{{ trans('lang.diesel') }}</legend>
                                <div class="form-group row width-100">
                                    <div id="diesel_vehicles"></div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="restaurant_payout_create-inner col-md-4 m-0">
                            <fieldset>
                                <legend>{{ trans('lang.other') }}</legend>
                                <div class="form-group row width-100">
                                    <div id="other_vehicles"></div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="form-group col-12 text-center btm-btn">

                        <a href="{!! route('vehicle-type') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel') }}</a>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        var database = firebase.firestore();

        $(document).ready(async function() {

            $('.vehicle_type_menu').addClass('active');
            await database.collection('fuel_type').get().then(async function(fuelsnapshot) {
                var petrolCountHtml = '';
                var dieselCountHtml = '';
                var otherCountHtml = '';
                await Promise.all(fuelsnapshot.docs.map(async (listval) => {
                    var fuelData = listval.data();
                    await database.collection('vehicle_type').where('enable', '==', true).get().then(async function(snapshot) {
                          jQuery("#overlay").show();
                        await Promise.all(snapshot.docs.map(async (element) => {
                            var data = element.data();
                            if (fuelData.name == 'Petrol') {
                                var petrolUserCount = await getUserCount(data.id, fuelData.id);
                                petrolCountHtml += '<label class="col-3 control-label"><strong>' + data.name + ' : </strong><span>' + petrolUserCount + '</span></label>';

                            } else if (fuelData.name == 'Diesel') {
                                var dieselUserCount = await getUserCount(data.id, fuelData.id);
                                dieselCountHtml += '<label class="col-3 control-label"><strong>' + data.name + ' : </strong><span>' + dieselUserCount + '</span></label>';

                            } else {
                                var otherUserCount = await getUserCount(data.id, fuelData.id);
                                otherCountHtml += '<label class="col-3 control-label"><strong>' + data.name + ' : </strong><span>' + otherUserCount + '</span></label>';

                            }

                        }));
                          jQuery("#overlay").hide();
                    })

                }));

                $('#petrol_vehicles').html(petrolCountHtml);
                $('#diesel_vehicles').html(dieselCountHtml);
                $('#other_vehicles').html(otherCountHtml);

            })
            async function getUserCount(vehId, fuelId) {

                var count = 0;
                await database.collection('users').where('documentApproved', '==', true).where('fuelTypeId', '==', fuelId).where('vehicleId', '==', vehId).get().then(async function(snapshot) {
                    count = snapshot.docs.length;
                });
                return count;
            }

            database.collection('users').where('role', '==', 'customer').get().then(async function(snapshot) {
                $('#total_user').html('{{ trans('lang.total_user') }} = ' + snapshot.docs.length);

            })

        });
        async function getFuelName(fuelId) {
            var fuelName = '';
            await database.collection('fuel_type').where('id', '==', fuelId).get().then(async function(snapshot) {
                var data = snapshot.docs[0].data();
                fuelName = data.name;
            })
            return fuelName;
        }
        var fueltypeRef = database.collection('fuel_type').where('enable', '==', true).orderBy('placeId', 'asc');
        fueltypeRef.get().then(async function(snapshot) {
            if (snapshot.docs.length > 0) {
                var html = '';

                snapshot.docs.forEach((listval) => {
                    var data = listval.data();
                    html = html + '<div class="form-group row width-100">';
                    html = html + '<label class="col-3 control-label">{{ trans('lang.fuel_quota') }} (' + data.name + ' )</label>'
                    html = html + '<div class="col-7">';
                    html = html + '<input type="number" class="fuel_quota form-control" id="' + data.name + '" value="' + data.name + '"  data-id="' + data.id + '" name="fuel_quota">';
                    html = html + '</div></div>';

                })
                $('#fuel_quota_div').html(html);
            }
        })
    </script>
@endsection
