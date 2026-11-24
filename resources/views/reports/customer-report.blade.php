@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.customer_report')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.customer_report')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card  pb-4">

            <div class="card-body">
                <div id="data-table_processing" class="dataTables_processing panel panel-default"
                    style="display: none;">{{trans('lang.processing')}}</div>
                <div class="error_top"></div>

                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{trans('lang.customer_report')}}</legend>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.select_customer')}}<span
                                        class="required-field"></span>
                                </label>
                                <div class="col-7">
                                    <select class="form-control user" id="customer">
                                        <option selected value="all">{{trans('lang.all')}}</option>
                                        <option value="specific">{{trans('lang.specific_customer')}}</option>
                                    </select>
                                </div>

                            </div>
                            <div class="form-group row width-50  d-none" id="vehicleNumber_div">
                                <label class="col-3 control-label">{{trans('lang.vehicle_number')}}<span
                                        class="required-field"></span>
                                </label>
                                <div class="col-7">
                                    <input type="text" id="vehicle_number" class="form-control">
                                </div>

                            </div>
                            <div class="form-group row width-50" id="vehicleType_div">
                                <label class="col-3 control-label">{{trans('lang.select_vehicle_type')}}<span
                                        class="required-field"></span>
                                </label>
                                <div class="col-7">
                                    <select class="form-control user" id="vehicleType">
                                        <option selected value="">{{trans('lang.select_vehicle_type')}}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.select_date')}}</label>
                                <div class="col-7">
                                    <div id="reportrange"
                                        style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span></span> <i class="fa fa-caret-down"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.file_format')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <select class="form-control file_format">
                                        <option value="">{{trans('lang.file_format')}}</option>
                                        <option value="csv">{{trans('lang.csv')}}</option>
                                        <option value="pdf">{{trans('lang.pdf')}}</option>
                                    </select>
                                </div>
                            </div>

                        </fieldset>
                    </div>
                </div>

                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" onclick="generateReport()" class="btn btn-primary download-user-report"><i
                            class="fa fa-save"></i> {{ trans('lang.download')}}</button>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/objectexporter.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script>
    var database=firebase.firestore();
    var customerRef=database.collection('users').where('role','==','customer');
    var vehicleTypeRef=database.collection('vehicle_type');
    $('#customer').on('change',function() {
        var customer=$(this).val();
        if(customer=='specific') {
            $('#vehicleNumber_div').removeClass('d-none');
            $('#vehicleType_div').addClass('d-none');
        } else {
            $('#vehicleNumber_div').addClass('d-none');
            $('#vehicleType_div').removeClass('d-none');
        }
    });


    vehicleTypeRef.get().then(function(snapShots) {

        if(snapShots.docs.length>0) {
            $('#vehicleType').append('<option value="all" selected>{{trans("lang.all_vehicle")}}</option>');
            snapShots.docs.forEach((listval) => {
                var data=listval.data();
                $('#vehicleType').append('<option value="'+data.id+'">'+data.name+'</option>');
            });

        }
    });


    $(function() {

        var start=moment().subtract(29,'days');
        var end=moment();

        function cb(start,end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY')+' - '+end.format('MMMM D, YYYY'));
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(),moment()],
                'Yesterday': [moment().subtract(1,'days'),moment().subtract(1,'days')],
                'Last 7 Days': [moment().subtract(6,'days'),moment()],
                'Last 30 Days': [moment().subtract(29,'days'),moment()],
                'This Month': [moment().startOf('month'),moment().endOf('month')],
                'Last Month': [moment().subtract(1,'month').startOf('month'),moment().subtract(1,'month').endOf('month')]
            }
        },cb);

        cb(start,end);

    });

    async function generateReport() {
        var customer=$("#customer :selected").val();
        var vehicleType=$('#vehicleType :selected').val();
        let start_date=moment($('#reportrange').data('daterangepicker').startDate).toDate();
        let end_date=moment($('#reportrange').data('daterangepicker').endDate).toDate();
        var vehicle_number=$('#vehicle_number').val();

        if(customer!="all") {
             if(vehicle_number=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.vehicleno_help')}}</p>");
                window.scrollTo(0,0);
                return false;
            } else {
                 vehicle_number=vehicle_number.toString().toUpperCase();
                
                 customerRef=customerRef.where('vehicleNumber','==',vehicle_number);
            }
            
        }
        if(vehicleType!="all") {
            customerRef=customerRef.where('vehicleId','==',vehicleType);
        }
        if(start_date!='') {
            customerRef=customerRef.where('createdAt','>=',start_date);
        }
        if(end_date!='') {
            customerRef=customerRef.where('createdAt','<=',end_date);
        }
        var fileFormat=$(".file_format :selected").val();
        var headerArray=['Name','Phone','Vehicle Number','Vehicle Type','Fuel Type','Date'];
        var headers=[];
        var customerData=[];
        if(fileFormat=='xls'||fileFormat=='csv') {
            headers=headerArray
            var script=document.createElement("script");
            script.setAttribute("src","https://unpkg.com/object-exporter@3.2.1/dist/objectexporter.min.js");
            script.setAttribute("async","false");
            var head=document.head;
            head.insertBefore(script,head.firstChild);
        } else {
            for(var k=0;k<headerArray.length;k++) {
                headers.push({
                    alias: headerArray[k],
                    name: headerArray[k],
                    flex: 1,
                });
            }
            var script=document.createElement("script");
            script.setAttribute("src","{{ asset('js/objectexporter.min.js') }}");
            script.setAttribute("async","false");
            var head=document.head;
            head.insertBefore(script,head.firstChild);
        }

        $(".error_top").html("");

        if(fileFormat=='') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.file_format_error')}}</p>");
            window.scrollTo(0,0);
        } else {
            jQuery("#overlay").show();

            customerRef.get().then(async function(customerSnapshots) {

                if(customerSnapshots.docs.length>0) {
                    var customerData=await getData(customerSnapshots);
                    ((fileFormat=="pdf")? document.title="customer-report":"");
  
                    objectExporter({
                        type: fileFormat,
                        exportable: customerData,
                        headers: headers,
                        columnSeparator: ',',
                        fileName: 'customer-report',
                        headerStyle: 'font-weight: bold; padding: 5px; border: 1px solid #dddddd;',
                        cellStyle: 'border: 1px solid lightgray; margin-bottom: -1px;',
                        documentTitle: '',
                        sheetName: 'customer-report'
                    })
                    jQuery("#overlay").hide();


                    $('.file_format option[value=""]').attr('selected','selected');
                } else {
                    jQuery("#overlay").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.not_found_data_error')}}</p>");
                    window.scrollTo(0,0);
                }
            });
        }
    }
    async function getVehicleType(id){
        try {
            const vehicleTypeRef = await database.collection('vehicle_type').doc(id).get();

            if(vehicleTypeRef.exists){
                const vehicleTypeData = vehicleTypeRef.data();
                return vehicleTypeData.name;
            }

            return '';
        } catch(error){
            console.log(error);
            return '';
        }
    }
    async function getFuelType(id){
        try {
            const fuelTypeRef = await database.collection('fuel_type').doc(id).get();

            if(fuelTypeRef.exists){
                const fuelTypeData = fuelTypeRef.data();
                return fuelTypeData.name;
            }

            return '';
        } catch(error){
            console.log(error);
            return '';
        }
    }

    async function getData(querySnapshot) {
        var customerData=[];
        await Promise.all(querySnapshot.docs.map(async (doc) => {
            var userObj=doc.data();
            var newUserObj={};

            var date=userObj.createdAt.toDate().toDateString();
            var time=userObj.createdAt.toDate().toLocaleTimeString('en-US');
            var dateTime=date+" "+time;
            newUserObj['Name']=(userObj.fullName)? userObj.fullName:'';
            if(userObj.hasOwnProperty('countryCode')&&userObj.countryCode!=null&&userObj.countryCode!='') {
                newUserObj['Phone']='(+'+(userObj.countryCode.includes('+')? userObj.countryCode.slice(1):userObj.countryCode)+') '+userObj.mobileNumber;
            } else {
                newUserObj['Phone']='';
            }
            newUserObj['Vehicle Number']=userObj.vehicleNumber;
            var vehicleType = await getVehicleType(userObj.vehicleId);
            newUserObj['Vehicle Type']=vehicleType;
            var fuelType = await getFuelType(userObj.fuelTypeId);
            newUserObj['Fuel Type']=fuelType;
            newUserObj['Date']=dateTime;  
            customerData.push(newUserObj);
        }));
        return customerData;
    }

</script>
@endsection