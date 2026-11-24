@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">
                {{trans('lang.quota_update_history')}}
            </h3>

        </div>

        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a
                        href="{{route('fuel-quota.index')}}">{{trans('lang.fuel_quota_list')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.quota_update_history')}}</li>

            </ol>

        </div>

        <div>

        </div>

    </div>


    <div class="container-fluid">

        <div class="row">

            <div class="col-12">

                <div class="card">

                    <div class="card-body">

                        <div class="table-responsive m-t-10">

                            <table id="fuelTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">

                                <thead>

                                    <tr>

                                        <th>{{trans('lang.name')}}</th>
                                        <th>{{trans('lang.vehicle_type')}}</th>
                                        <th>{{trans('lang.type_fuel')}}</th>
                                        <th>{{trans('lang.total_fuel_quota')}}</th>
                                        <th>{{trans('lang.date')}}</th>
                                       

                                    </tr>

                                </thead>

                                <tbody id="append_list1">

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</div>
</div>


@endsection

@section('scripts')
<script type="text/javascript">
    var database = firebase.firestore();
    var ref = database.collection('customer_fuel_reset_history').orderBy('date', 'desc');
    var append_list = '';

    $(document).ready(function () {
        jQuery("#overlay").show();
        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';
            const table=$('#fuelTable').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: function(data,callback,settings) {
                const start=data.start;
                const length=data.length;
                const searchValue=data.search.value.toLowerCase();
                const orderColumnIndex=data.order[0].column;
                const orderDirection=data.order[0].dir;
                const orderableColumns= ['userName','vehicleType','fuelType','quota','date'];
                const orderByField=orderableColumns[orderColumnIndex]; // Adjust the index to match your table
                if(searchValue.length>=3||searchValue.length===0) {
                    $('#overlay').show();
                }

                ref.get().then(async function(querySnapshot) {

                    if(querySnapshot.empty) {

                        console.error("No data found in Firestore.");

                        $('#overlay').hide(); // Hide loader

                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: [] // No data
                        });
                        return;
                    }
                    var vehicleTypes={};
                    const vehicleDocs=await database.collection('vehicle_type').get();
                    vehicleDocs.forEach(doc => {
                        vehicleTypes[doc.id]=doc.data().name;
                    });
                    var fuelTypes={};
                    const fuelDocs=await database.collection('fuel_type').get();
                    fuelDocs.forEach(doc => {
                        fuelTypes[doc.id]=doc.data().name;
                    });
                   
                    var userNames={};
                    const userDocs=await database.collection('users').where('role','==','customer').get();
                    userDocs.forEach(doc => {
                        userNames[doc.id]=doc.data().fullName;
                    });
                    let records=[];
                    let filteredRecords=[];
                    querySnapshot.forEach(function(doc) {
                        let childData=doc.data();
                        childData.id=doc.id; // Ensure the document ID is included in the data              
                        childData.fullName=childData.fullName;
                        if(childData.hasOwnProperty('countryCode')&&childData.countryCode!=null&&childData.countryCode!='') {
                            childData.mobileNumber='+'+(childData.countryCode.includes('+')? childData.countryCode.slice(1):childData.countryCode)+'-'+childData.mobileNumber;
                        }
                        childData.vehicleType=vehicleTypes[childData.vehicleId]||'';
                        childData.fuelType=fuelTypes[childData.fuelTypeId]||'';
                        childData.userName=userNames[childData.customerId]||'';

                        if(searchValue) {
                            var date='';
                            var time='';
                            if(childData.hasOwnProperty("date")) {
                                try {
                                    date=childData.date.toDate().toDateString();
                                    time=childData.date.toDate().toLocaleTimeString('en-US');
                                } catch(err) {

                                }
                            }
                            var createdAt=date+' '+time;

                            if(
                                (childData.userName&&childData.userName.toLowerCase().toString().includes(searchValue))||
                                (createdAt&&createdAt.toString().toLowerCase().indexOf(searchValue)>-1)||
                                (childData.fuelType&&childData.fuelType.toString().toLowerCase().includes(searchValue))||
                                (childData.vehicleType&&childData.vehicleType.toString().toLowerCase().includes(searchValue))||
                                (childData.quota&&childData.quota.toString().toLowerCase().includes(searchValue))

                            ) {

                                filteredRecords.push(childData);

                            }

                        } else {

                            filteredRecords.push(childData);

                        }

                    });



                    filteredRecords.sort((a,b) => {

                        let aValue=a[orderByField]? a[orderByField].toString().toLowerCase():'';
                        let bValue=b[orderByField]? b[orderByField].toString().toLowerCase():'';
                        if(orderByField==='date') {
                            aValue=a[orderByField]? new Date(a[orderByField].toDate()).getTime():0;
                            bValue=b[orderByField]? new Date(b[orderByField].toDate()).getTime():0;
                        }

                        if(orderDirection==='asc') {
                            return (aValue>bValue)? 1:-1;
                        } else {
                            return (aValue<bValue)? 1:-1;
                        }

                    });



                    const totalRecords=filteredRecords.length;
                    const paginatedRecords=filteredRecords.slice(start,start+length);
                    await Promise.all(paginatedRecords.map(async (childData) => {
                        var getData=await buildHTML(childData);
                        records.push(getData);
                    }));

                    $('#overlay').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords, // Total number of records in Firestore
                        recordsFiltered: totalRecords, // Number of records after filtering (if any)
                        data: records // The actual data to display in the table
                    });

                }).catch(function(error) {

                    console.error("Error fetching data from Firestore:",error);
                    $('#overlay').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        data: [] // No data due to error
                    });
                });
            },

            order:[4,'desc'],
            columnDefs: [
                {
                    targets: 4,
                    type: 'date',
                    render: function(data) {
                        return data;
                    }

                },


            ],

            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}",
                "processing": "" // Remove default loader
            },
        });

        function debounce(func,wait) {

            let timeout;
            const context=this;
            return function(...args) {
                clearTimeout(timeout);
                timeout=setTimeout(() => func.apply(context,args),wait);
            };

        }
                

      /*  ref.get().then(async function (snapshots) {
            var html = '';
            if (snapshots.docs.length > 0) {
                html = await buildHTML(snapshots);
            }
            if (html != '') {
                append_list.innerHTML = html;
            }

            $('#fuelTable').DataTable({
                order: [[4, 'desc']],
                columnDefs: [{
                    targets: 4,
                    type: 'date',
                    render: function (data) {
                        return data;
                    }
                }],
                "language": {
                    "zeroRecords": "{{trans("lang.no_record_found")}}",
                    "emptyTable": "{{trans("lang.no_record_found")}}"
                },
            });

            jQuery("#overlay").hide();
        });

*/
    });

    /*async function buildHTML(snapshots) {
        var html = '';
        await Promise.all(snapshots.docs.map(async (listval) => {
            var val = listval.data();
            var getData = await getListData(val);
            html += getData;
        }));
        return html;
    }*/

    async function buildHTML(val) {
        var html = [];
        newdate = '';
        var id = val.id;

       // var customer=await getCustomer(val.customerId);
        var route="{{route('customers.edit', ':id')}}";
        route= route.replace(':id',val.customerId);
        html.push('<a href="'+route+'">'+val.userName+'</a>');
        
       
        html.push(val.vehicleType);
        html.push(val.fuelType);
         html.push(val.quota+' {{trans("lang.liter")}}');
        var date = '';
        var time = '';
        if (val.hasOwnProperty("date")) {
            try {
                date = val.date.toDate().toDateString();
                time = val.date.toDate().toLocaleTimeString('en-US');
            } catch (err) {

            }
             html.push('<td class="dt-time">' + date + ' ' + time+'</td>' );
        } else {
             html.push('');
        }
        
        return html;
    }
async function getCustomer(customerId){
    var customer='';
    await database.collection('users').where('id','==',customerId).get().then(async function(snapshots){
        if(snapshots.docs.length>0){
            var data=snapshots.docs[0].data();
            customer=data.fullName;
        }
    })
    return customer;
}
async function getFuelType(fuelTypeId) {
        var fuelType = '';
        await database.collection('fuel_type').where('id', '==', fuelTypeId).get().then(async function (snapshots) {
            if (snapshots.docs.length > 0) {
                var data = snapshots.docs[0].data();
                fuelType = data.name;
            }
        })
        return fuelType;
    }
    async function getVehicleType(vehId) {
        var vehType = '';
        await database.collection('vehicle_type').where('id', '==', vehId).get().then(async function (snapshots) {
            if (snapshots.docs.length > 0) {
                var data = snapshots.docs[0].data();
                vehType = data.name;
            }
        })
        return vehType;
    }
</script>

@endsection