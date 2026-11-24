@extends('layouts.app')

@section('content')
<div class="page-wrapper pb-5">
    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.fuel_transaction')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                <li class="breadcrumb-item"><a
                        href="{!! route('fuel-transaction') !!}">{{trans('lang.fuel_transaction')}}</a>
                </li>

                <li class="breadcrumb-item">{{trans('lang.transaction_detail')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card-body p-0 no_data_found">
            <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
                {{trans('lang.processing')}}
            </div>

            <div class="col-md-12">
                <div class="print-top non-printable mt-3">
                    <div class="text-right print-btn non-printable">
                        <button type="button" class="fa fa-print non-printable"
                            onclick="printDiv('printableArea')"></button>
                    </div>
                </div>

                <hr class="non-printable">
            </div>

            <div class="row restaurant_payout_create" style="max-width:100%;" role="tabpanel" id="printableArea">

                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane active" id="category_information">
                        <div class="order_detail" id="order_detail">

                            <div class="order_detail-top mb-3 printableArea">
                                <div class="row">


                                    <div class="order_edit-genrl col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-white">
                                                <h3>{{trans('lang.general_details')}}</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="order_detail-top-box">
                                                    <div class="form-group row widt-100 gendetail-col">
                                                        <label class="col-12 control-label"><strong>{{trans('lang.txn_id')}}
                                                                : </strong><span id="txn_id"></span></label>

                                                    </div>

                                                    <div class="form-group row widt-100 gendetail-col">
                                                        <label class="col-12 control-label"><strong>{{trans('lang.filled_date')}}
                                                                : </strong><span id="createdAt"></span></label>

                                                    </div>
                                                    <div class="form-group row widt-100 gendetail-col">
                                                        <label class="col-12 control-label"><strong>{{trans('lang.filled_fuel')}}
                                                                :</strong> <span id="fuel"></span></label>
                                                    </div>

                                                    <div class="form-group row widt-100 gendetail-col payment_method">
                                                        <label class="col-12 control-label"><strong>{{trans('lang.status')}}
                                                                : </strong><span id="status"></span></label>

                                                    </div>


                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class=" order_edit-genrl col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-white">
                                                <h3>{{ trans('lang.customer_details')}}</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="address order_detail-top-box user-details">
                                                    <p><strong>{{trans('lang.name')}}: </strong><span id="customer_name"
                                                            class="d-flex"></span></p>

                                                    <p><strong>{{trans('lang.phone')}}:</strong>
                                                        <span id="customer_phone"></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="row">

                                <div class="order_edit-genrl col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-white">
                                            <h3>{{trans('lang.vehicle_details')}}</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="order_detail-top-box">
                                                <div class="form-group row widt-100 gendetail-col">
                                                    <label class="col-12 control-label"><strong>{{trans('lang.vehicle_number')}}
                                                            : </strong><span id="vehicle_number"></span></label>

                                                </div>

                                                <div class="form-group row widt-100 gendetail-col">
                                                    <label class="col-12 control-label"><strong>{{trans('lang.type_fuel')}}
                                                            :</strong> <span id="fuel_type"></span></label>
                                                </div>


                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class=" order_edit-genrl col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-white">
                                            <h3>{{ trans('lang.owner_details')}}</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="address order_detail-top-box user-details">
                                                <p><strong>{{trans('lang.name')}}: </strong><span id="owner_name"
                                                        class="d-flex"></span></p>

                                                <p><strong>{{trans('lang.phone')}}:</strong>
                                                    <span id="owner_phone"></span>
                                                </p>
                                                <p><strong>{{trans('lang.fuel_station_name')}}:</strong>
                                                    <span id="pump_name"></span>
                                                </p>
                                                <p><strong>{{trans('lang.fuel_station_address')}}:</strong>
                                                    <span id="pump_address"></span>
                                                </p>
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">

                                <div class="order_edit-genrl col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-white">
                                            <h3>{{trans('lang.employee_details')}}</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="order_detail-top-box">
                                                <div class="form-group row widt-100 gendetail-col">
                                                    <label class="col-12 control-label"><strong>{{trans('lang.employee_name')}}
                                                            : </strong><span id="employee_name"></span></label>

                                                </div>

                                                <div class="form-group row widt-100 gendetail-col">
                                                    <label class="col-12 control-label"><strong>{{trans('lang.employee_phone')}}
                                                            :</strong> <span id="employee_phone"></span></label>
                                                </div>
                                                <div class="form-group row widt-100 gendetail-col">
                                                    <label class="col-12 control-label"><strong>{{trans('lang.address')}}
                                                            :</strong> <span id="employee_adrs"></span></label>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>



                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <a href="{!! route('fuel-transaction') !!}" class="btn btn-default"><i
                        class="fa fa-undo"></i>{{trans('lang.cancel')}}
                </a>

            </div>

        </div>


    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

    var database = firebase.firestore();


    var refData = database.collection('fuel_transaction').where('id', '==', '{{$id}}');


    $(document).ready(async function () {


        $('.fuel_transaction_menu').addClass('active');

        // getRideDeatils();
        refData.get().then(async function (snapshots) {
            var data = snapshots.docs[0].data();
            $('#txn_id').html((data.id).substring(0, 7));
            var date = '';
            var time = '';
            if (data.hasOwnProperty("createAt")) {
                try {
                    date = data.createAt.toDate().toDateString();
                    time = data.createAt.toDate().toLocaleTimeString('en-US');
                } catch (err) {

                }
            }
                $('#createdAt').html(date + ' ' + time);
                $('#fuel').html(data.fuelInLiter + ' Liter');
                var badgeClass = 'badge-success';
                var status = 'Approved';
                if (data.status == "requested") {
                    status = 'Requested';
                    badgeClass = 'badge-info';
                } else if (data.status == 'cancelled') {
                    status = 'Cancelled';
                    badgeClass = 'badge-danger';
                }
                $('#status').html('<span class="badge ' + badgeClass + ' py-2 px-3">' + status + '</span>');
                $('#customer_name').html(data.driverName);
                $('#customer_phone').html(data.driverMobileNo);
                $('#vehicle_number').html(data.driverVehicleNumber);
                $('#fuel_type').html(data.fuelType);
                $('#pump_address').html(data.ownerPumpAddress);
                $('#pump_name').html(data.ownerPumpName);
                await getUSerInfo(data.ownerId);
                await getUSerInfo(data.employeeId);
            })

    });
    async function getUSerInfo(userId){
        await database.collection('users').where('id','==', userId).get().then(async function(snapshots){
            if(snapshots.docs.length>0){
              var data=snapshots.docs[0].data();
              if(data.role=='owner'){
                $('#owner_name').html(data.fullName);
                $('#owner_phone').html('+'+data.countryCode+data.mobileNumber);
              }else{
                  $('#employee_name').html(data.fullName);
                  var code= ((data.countryCode).slice(0, 1) == '+') ? data.countryCode : '+'+data.countryCode;
                  $('#employee_phone').html(code+ data.mobileNumber);
                  $('#employee_adrs').html(data.address);
                }  
            }
        })
    }
    function printDiv(divName) {

        var css = '@page { size: portrait; }',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

        style.type = 'text/css';
        style.media = 'print';

        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);

        document.getElementById('ride-map-dredetail').innerHTML = '';

        var printContents = document.getElementById(divName).innerHTML;

        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        document.getElementById('ride-map-dredetail').innerHTML = '<div class="card">\n' +
            '                                            <div class="box card-body p-0">\n' +
            '                                                <div class="box-header bb-2 card-header bg-white">\n' +
            '                                                    <h3 class="box-title">{{trans('lang.map_view')}}</h3>\n' +
            '                                                </div>\n' +
            '                                                <div class="card-body">\n' +
            '                                                    <div id="map" style="height:300px">\n' +
            '                                                    </div>\n' +
            '                                                </div>\n' +
            '                                            </div>\n' +
            '                                        </div>';
        getRideDeatils();

    }


</script>


@endsection