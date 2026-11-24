@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.owner_report')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.owner_report')}}</li>
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
                            <legend>{{trans('lang.owner_report')}}</legend>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.select_owner')}}<span
                                        class="required-field"></span>
                                </label>
                                <div class="col-7">
                                    <select class="form-control user" id="owner">
                                        <option selected value="">{{trans('lang.select_owner')}}</option>
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
    var ownerRef=database.collection('users').where('role','==','owner');
    $('#owner').select2();
    ownerRef.get().then(function(snapShots) {

        if(snapShots.docs.length>0) {
            $('#owner').append('<option value="all" selected>{{trans("lang.all_owner")}}</option>');
            snapShots.docs.forEach((listval) => {
                var data=listval.data();
                $('#owner').append('<option value="'+data.id+'">'+data.fullName+'</option>');
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
        var owner=$("#owner :selected").val();
        let start_date=moment($('#reportrange').data('daterangepicker').startDate).toDate();
        let end_date=moment($('#reportrange').data('daterangepicker').endDate).toDate();

        if(owner!="all") {
            ownerRef=ownerRef.where('id','==',owner);
        }
       
        if(start_date!='') {
            ownerRef=ownerRef.where('createdAt','>=',start_date);
        }
        if(end_date!='') {
            ownerRef=ownerRef.where('createdAt','<=',end_date);
        }
        var fileFormat=$(".file_format :selected").val();
        var headerArray=['Name','Phone','Fuel Station Name','Address','Date','Available Quota'];
        var headers=[];
        var ownerData=[];
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

            ownerRef.get().then(async function(ownerSnapshots) {

                if(ownerSnapshots.docs.length>0) {
                    var ownerData=await getData(ownerSnapshots,fileFormat);

                    ((fileFormat=="pdf")? document.title="owner-report":"");

                    objectExporter({
                        type: fileFormat,
                        exportable: ownerData,
                        headers: headers,
                        columnSeparator: ',',
                        fileName: 'owner-report',
                        headerStyle: 'font-weight: bold; padding: 5px; border: 1px solid #dddddd;',
                        cellStyle: 'border: 1px solid lightgray; margin-bottom: -1px;',
                        documentTitle: '',
                        sheetName: 'owner-report'
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

    async function getData(querySnapshot,fileFormat) {
        var ownerData=[];
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
             newUserObj['Fuel Station Name']=userObj.fuelStationName;
            if(fileFormat=='csv') {
                newUserObj['Address']=userObj.fuelStationAddress.replace(/,/g,'');
            }else{
                newUserObj['Address']=userObj.fuelStationAddress;
            }
           
            newUserObj['Date']=dateTime;
            newUserObj['Available Quota']=userObj.availableFuelQuota;

            ownerData.push(newUserObj);
        }));
        return ownerData;
    }

</script>
@endsection