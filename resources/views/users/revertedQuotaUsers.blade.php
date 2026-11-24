@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans("lang.reverted_quota_users")}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.app_setting_global')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" style="margin-left:300px">
                            <a name="reset-reverted-fuel-quota"
                                class="btn btn-primary view-page ml-3">{{trans('lang.reset_fuel_quota')}}</a>
                    </div>
                    <div class="card-body">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default"
                            style="display: none;">{{trans('lang.processing')}}</div>
                        <div class="error_top" style="display:none"></div>

                        <div class="row restaurant_payout_create">

                        <div class="table-responsive m-t-10">
                            <table id="userTable" class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{trans('lang.id')}}</th>
                                        <th>{{trans('lang.name')}}</th>
                                        <th>{{trans('lang.phone')}}</th>
                                    </tr>
                                </thead>
                                <tbody id="userData">
                                </tbody>
                            </table>
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

        const ids=@json($ids);
        const users=[];
        const today=new Date().toISOString().split('T')[0];

        async function fetchUserData(ids) {
            $('#overlay').show();
            const usersRef=database.collection('users');
            const chunkSize=10;
            for(let i=0;i<ids.length;i+=chunkSize) {
                const chunk=ids.slice(i,i+chunkSize);

                const userDocs=await usersRef.where('id','in',chunk).get();

                userDocs.forEach(doc => {
                    users.push(doc.data());
                });
            }
            let userRows='';
            users.forEach(user => {
                userRows+=`<tr>
                                <td>${user.id}</td>
                                <td>${user.fullName}</td>
                                <td>${user.mobileNumber}</td>
                            </tr>`;
            });

            document.getElementById('userData').innerHTML=userRows;
            $('#mismatchedQuotaTable').DataTable();
            $('#overlay').hide();

        }
        $(document).ready(function() {
            fetchUserData(ids);
            var resetMsg='{{trans("lang.reset_fuel_alert")}}';


            $(document).on("click","a[name='reset-reverted-fuel-quota']",async function() {
                if(!confirm(resetMsg)) return;

                Swal.fire({
                    title: 'Processing...',
                    onBeforeOpen: Swal.showLoading,
                    allowOutsideClick: false,
                    showConfirmButton: false
                });

                const today=new Date().toISOString().split('T')[0]; 
                const newDate=firebase.firestore.FieldValue.serverTimestamp();
                let updatedCount=0; 

                const updatePromises=users.map(async (val) => {
                    try {

                        const snapshot=await database.collection("vehicle_type")
                            .where('id','==',val.vehicleId)
                            .get();

                        let totalQuota=0;
                        
                        if(snapshot.docs.length>0) {
                            const data=snapshot.docs[0].data();
                            totalQuota=data?.fuelQuota?.find(f => f.fuelTypeId===val.fuelTypeId)?.fuelQuota||val.totalFuelQuota;
                        }
                       
                        await database.collection('users').doc(val.id).update({
                            allocatedFuelDate: newDate,
                            availableFuelQuota: totalQuota,
                            totalFuelQuota: totalQuota,
                            appIdentifier: 'web',
                            updatedAtWeb:newDate,
                            'updateFrom': 'Reverted'
                        });

                        updatedCount++; 
                    } catch(error) {
                        console.error(`Error updating user ${val.id}:`,error);
                    }
                });

                await Promise.all(updatePromises);

                Swal.close();
                logFirebaseActivity('{{trans("lang.reset_fuel_quota")}}');
                Swal.fire({
                    title: 'Update Complete',
                    text: `Total Users Updated: ${updatedCount}`,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href='settings/global-setting'; 
                });
            });

        });

    </script>

    @endsection