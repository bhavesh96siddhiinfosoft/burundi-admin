@extends('layouts.app')

@section('content')
@php
$type = 'all';
@endphp
<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                @if(request()->is('customers/approved'))
                    @php    $type = 'approved'; @endphp
                    {{trans('lang.approved_customers')}}
                @elseif(request()->is('customers/pending'))
                    @php    $type = 'pending'; @endphp
                    {{trans('lang.approval_pending_customers')}}
                @else
                    {{trans('lang.all_customers')}}
                @endif
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.customer_table')}}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">

                    
                        @if(in_array('reset.fuel.quota.history', json_decode(@session('user_permissions'))))
                            <a href="{{route('reset-fuel-quota.history')}}"
                                class="btn btn-primary view-page ml-3">{{trans('lang.view_fuel_reset_history')}}</a>
                        @endif
                    </div>
                    <div class="card-body">

                        <div id="users-table_filter" class="pull-right">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <select id="date_filter_selector" class="form-control allModules" style="width:100%">
                                    <option value="today" selected>{{ trans("lang.today") }}</option>
                                    <option value="this_week">{{ trans("lang.this_week") }}</option>
                                    <option value="this_month">{{ trans("lang.this_month") }}</option>
                                    <option value="custom">{{ trans("lang.custom") }}</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3" id="custom_date_range" style="display:none;">
                                <input type="date" id="from_date" class="form-control" placeholder="{{ trans('lang.from_date') }}">
                            </div>
                            <div class="col-md-3 mb-3" id="to_date_wrapper" style="display:none;">
                                <input type="date" id="to_date" class="form-control" placeholder="{{ trans('lang.to_date') }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <select id="vehicle_type_selector" class="form-control allModules" style="width:100%">
                                    <option value="" disabled selected>{{ trans("lang.vehicle_typee_help") }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select id="fuel_type_selector" class="form-control allModules" style="width:100%">
                                    <option value="" disabled selected>{{ trans("lang.fuel_type_help") }}</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3 d-flex align-items-start">
                                <button class="btn btn-primary w-50" type="button" onclick="submitFilters()">{{trans('lang.submit')}}</button>
                            </div>
                        </div>



                        </div>
                        

                        <div class="table-responsive m-t-10">
                            <table id="userTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        @if(in_array('customer.delete', json_decode(@session('user_permissions'))))

                                            <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                    class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                        class="do_not_delete" href="javascript:void(0)"><i
                                                            class="fa fa-trash"></i> {{trans('lang.all')}}</a></label></th>
                                        @endif
                                        <th>{{trans('lang.image')}}</th>
                                        <th>{{trans('lang.name')}}</th>
                                        <th>{{trans('lang.phone')}}</th>
                                        <th>{{trans('lang.vehicle_type')}}</th>
                                        <th>{{trans('lang.vehicle_number')}}</th>
                                        <th>{{trans('lang.type_fuel')}}</th>
                                        <th>{{trans('lang.total_fuel_quota')}}</th>
                                        <th>{{trans('lang.available_quota')}}</th>
                                        <th>{{trans('lang.created_at')}}</th>
                                        <th>{{trans('lang.document_verification')}}</th>
                                        <th>{{trans('lang.actions')}}</th>
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
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{trans('lang.documents')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" id="userId" hidden>
                <input type="text" id="userEmail" hidden>
                <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner" id="carousel-inner">

                    </div>

                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
                <!-- <img class="img-responsive" src="" /> -->
            </div>
            <div class="modal-footer">
                <div class="approveBtnDiv"><button type="button" class="btn btn-primary" data-dismiss="modal"
                        id="approveDocBtn">{{trans('lang.approve')}}</button></div>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans('lang.close')}}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')


<script type="text/javascript">
    var database=firebase.firestore();
    var defaultImg="{{ asset('/images/default_user.png') }}";
    var offest=1;
    var pagesize=10;
    var end=null;
    var endarray=[];
    var start=null;
    var user_number=[];
    var placeholderImage='';
    var append_list='';
    var type="{{$type}}";
 
    var base_ref=database.collection('users').where('role','==','customer');
    if(type=='pending') {
        base_ref=database.collection('users').where('role','==','customer').where("documentApproved","==",false);
    } else if(type=='approved') {
        base_ref=database.collection('users').where('role','==','customer').where("documentApproved","==",true);
    }

    var params = new URLSearchParams(window.location.search);
    var vehicleType = params.get('vehicleType');
    var fuelType = params.get('fuelType');
    var dateFilter = params.get('dateFilter');
    var fromDate = new Date(params.get('fromDate'));
    var toDate = new Date(params.get('toDate'));
    var ref = base_ref;

    if(vehicleType != '' && vehicleType != undefined && vehicleType != null && vehicleType!='all') {
        ref=ref.where('vehicleId','==',vehicleType);
    }

    if(fuelType != '' && fuelType != undefined && fuelType != null && fuelType != 'all') {
        ref=ref.where('fuelTypeId','==',fuelType);
    }

    if (dateFilter && dateFilter != '' && dateFilter != null) {
        ref = applyDateFilter(ref);
    } else {
        $('#date_filter_selector').val('today');
        ref = ref.where('createdAt', '>=', new Date(new Date().setHours(0, 0, 0, 0)));
    }
    var deleteMsg="{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg="{{trans('lang.selected_delete_alert')}}";
    var user_permissions='<?php echo @session('user_permissions') ?>';
    user_permissions=JSON.parse(user_permissions);
    var checkDeletePermission=false;
    if($.inArray('customer.delete',user_permissions)>=0) {
        checkDeletePermission=true;
    }
    let lastVisible = null;
    const batchSize = 10;
    let totalRecords = 0;

    async function fetchTotalRecords() {
        try {
            const snapshot = await ref.get();
            totalRecords = snapshot.size;
        } catch (error) {
            console.error("Error fetching total records:", error);
        }
    }
  
    $(document).ready(async function() {
        jQuery("#overlay").show();
        setDefaultFilters();
        await fetchTotalRecords();
        $('#userTable').DataTable({
            serverSide: true,
            processing: true,
            pageLength: batchSize,
            order: [[checkDeletePermission ? 9 : 8, 'desc']],
            ajax: function (data, callback) {
                fetchFirestoreData(data.start, data.length || batchSize, data.search.value).then((result) => {
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords,
                        recordsFiltered: result.totalFiltered || totalRecords,
                        data: result.data,
                    });
                });
            },
            columns: [
                checkDeletePermission ? { data: 'deleteCheckbox' } : null,
                { data: 'profilePic' },
                { data: 'fullName' },
                { data: 'mobileNumber' },
                { data: 'vehicle' },
                { data: 'vehicleNumber' },
                { data: 'fuelType' },
                { data: 'totalFuelQuota' },
                { data: 'availableFuelQuota' },
                { data: 'createdAt' },
                { data: 'documentStatus' },
                { data: 'actions' }
            ].filter(Boolean),
            columnDefs: [
                { targets: checkDeletePermission ? 9 : 8, type: 'date' },
                { targets: '_all', orderable: false }
            ],
            language: {
                zeroRecords: "{{trans('lang.no_record_found')}}",
                emptyTable: "{{trans('lang.no_record_found')}}"
            }
        });
        $(document.body).on('click','.redirecttopage',function() {
            var url=$(this).attr('data-url');
            window.location.href=url;
        });
        var refVeh=database.collection('vehicle_type');

        refVeh.get().then(async function(snapshots) {
            $('#vehicle_type_selector').append($("<option></option>")
                .attr("value",'all')
                .text("{{ trans('lang.all') }}"));
            snapshots.docs.forEach(doc => {
                var data=doc.data();

                $('#vehicle_type_selector').append($("<option></option>")
                    .attr("value",data.id)
                    .text(data.name));
            })
            var vehicleType = new URLSearchParams(window.location.search).get('vehicleType');
            if(vehicleType!=''&&vehicleType!=undefined&&vehicleType!=null) {
                $('#vehicle_type_selector').val(vehicleType);
            }
        });
        var fueltypeRef=database.collection('fuel_type').where('enable','==',true);
        fueltypeRef.get().then(async function(snapshot) {
            if(snapshot.docs.length>0) {
                $('#fuel_type_selector').append($("<option></option>")
                    .attr("value",'all')
                    .text("{{ trans('lang.all') }}"));
                snapshot.docs.forEach(doc => {
                    var data=doc.data();

                    $('#fuel_type_selector').append($("<option></option>")
                        .attr("value",data.id)
                        .text(data.name));
                })
                var fuelType = new URLSearchParams(window.location.search).get('fuelType');
                if(fuelType != '' && fuelType != undefined && fuelType != null) {
                    $('#fuel_type_selector').val(fuelType);
                }
            }
        })
        jQuery("#overlay").hide();
        

    });
    $('#date_filter_selector').on('change', function() {
        var dateFilter = $('#date_filter_selector').val();
        if (dateFilter === 'custom') {
            $('#custom_date_range').show();  
            $('#to_date_wrapper').show();    
            $('#from_date').prop('required', true);  
            $('#to_date').prop('required', true);   
        } else {
            $('#custom_date_range').hide(); 
            $('#to_date_wrapper').hide();    
            $('#from_date').prop('required', false); 
            $('#to_date').prop('required', false);  
        }
    });
    function submitFilters() {
        const params = new URLSearchParams();
        params.set('vehicleType', $('#vehicle_type_selector').val() || '');
        params.set('fuelType', $('#fuel_type_selector').val() || '');
        params.set('dateFilter', $('#date_filter_selector').val() || '');
        params.set('fromDate', $('#from_date').val() || '');
        params.set('toDate', $('#to_date').val() || '');
        window.location.href = '?' + params.toString();
    }
    function applyDateFilter(ref) {
        const params = new URLSearchParams(window.location.search);
        const dateFilter = params.get('dateFilter');
        const fromDate = new Date(params.get('fromDate'));
        const toDate = new Date(params.get('toDate'));

        // Apply date filters based on the selected date filter
        if (dateFilter === 'today') {
            const startOfDay = new Date();
            startOfDay.setHours(0, 0, 0, 0);
            ref = ref.where('createdAt', '>=', startOfDay);
        } else if (dateFilter === 'this_week') {
            const today = new Date();
            const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay())); 
            startOfWeek.setHours(0, 0, 0, 0);
            ref = ref.where('createdAt', '>=', startOfWeek);
        } else if (dateFilter === 'this_month') {
            const startOfMonth = new Date();
            startOfMonth.setDate(1);
            startOfMonth.setHours(0, 0, 0, 0); 
            ref = ref.where('createdAt', '>=', startOfMonth);
        } else if (dateFilter === 'custom' && fromDate && toDate) {
            ref = ref.where('createdAt', '>=', fromDate).where('createdAt', '<=', toDate);
        }

        return ref;
    }

    function setDefaultFilters() {
        const params = new URLSearchParams(window.location.search);

        $('#vehicle_type_selector').val(params.get('vehicleType') || '');
        $('#fuel_type_selector').val(params.get('fuelType') || '');
        $('#date_filter_selector').val(params.get('dateFilter') || 'today');
        $('#from_date').val(params.get('fromDate') || '');
        $('#to_date').val(params.get('toDate') || '');

        if (params.get('dateFilter') === 'custom') {
            $('#custom_date_range, #to_date_wrapper').show();
            $('#from_date, #to_date').prop('required', true);
        } else {
            $('#custom_date_range, #to_date_wrapper').hide();
            $('#from_date, #to_date').removeAttr('required');
        }
    }

    async function fetchFirestoreData(startIndex, limit, searchValue) {

        let query = ref.limit(limit);
        let searchResults = [];

        if (searchValue) {
            let searchPromises = ['fullName', 'mobileNumber', 'vehicleNumber'].map(field => {
                let searchQuery = base_ref.where(field, '>=', searchValue)
                    .where(field, '<=', searchValue + '\uf8ff')
                    .orderBy(field)
                    .limit(limit);
                return searchQuery.get();
            });
            query = query.orderBy("createdAt","desc");
            if (startIndex === 0) {
                lastVisible = null;
            } else if (lastVisible) {
                query = query.startAfter(lastVisible);
            }
       
            const results = await Promise.all(searchPromises);
            results.forEach(snapshot => {
                snapshot.forEach(doc => {
                    if (!searchResults.find(item => item.id === doc.id)) {
                        searchResults.push({ id: doc.id, ...doc.data() });
                    }
                });
            });
        } else {
            query = query.orderBy("createdAt","desc");
            if (startIndex === 0) {
                lastVisible = null;
            } else if (lastVisible) {
                query = query.startAfter(lastVisible);
            }
        
            const snapshots = await query.get();
            lastVisible = snapshots.docs[snapshots.docs.length - 1];
            snapshots.forEach(doc => searchResults.push({ id: doc.id, ...doc.data() }));
        }

        const data = await Promise.all(searchResults.map(async (val) => {
 
            const id = val.id;
            let route1 = '{{route("customers.edit", ":id")}}'.replace(':id', id);

            const vehicleType = await getVehicleType(val.vehicleId, val.id);
            const fuelType = await getFuelType(val.fuelTypeId, val.id);

            return {
                deleteCheckbox: checkDeletePermission ? `<input type="checkbox" id="is_open_${id}" class="is_open" dataId="${id}"><label class="col-3 control-label" for="is_open_${id}"></label>` : '',
                profilePic: `<img class="rounded" style="width:50px" src="${val.profilePic || defaultImg}" alt="image">`,
                fullName: `<a href="${route1}">${val.documentApproved ? `${val.fullName}<sup class="badge badge-success ml-2"><i class="fa fa-check"></i> {{trans("lang.verified")}}</sup>` : val.fullName}</a>`,
                mobileNumber: val.mobileNumber ? `+${val.countryCode.includes('+') ? val.countryCode.slice(1) : val.countryCode}-${val.mobileNumber}` : '',
                vehicle: vehicleType || '',
                vehicleNumber: val.vehicleNumber ? val.vehicleNumber.toUpperCase() : '',
                fuelType: fuelType || '',
                totalFuelQuota: val.totalFuelQuota || '',
                availableFuelQuota: val.availableFuelQuota || '',
                createdAt: val.createdAt ? val.createdAt.toDate().toDateString() + ' ' + val.createdAt.toDate().toLocaleTimeString('en-US') : '',
                documentStatus: val.documentApproved ? `<a href="javascript:void(0)" class="btn btn-primary btn-sm" name="show-documents" data-verified="true" id="${val.id}">{{trans("lang.document_verified")}}</a>` : `<a href="javascript:void(0)" class="btn btn-primary btn-sm" name="show-documents" data-verified="false" id="${val.id}">{{trans("lang.verify_document")}}</a>`,
                actions: `<a href="${route1}"><i class="fa fa-edit action-btn"></i></a> ` + (checkDeletePermission ? `<a id="${val.id}" class="do_not_delete" name="user-delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a>` : '')
            };
        }));

        return { data: data };
    }


    async function getFuelType(fuelTypeId, custId) {
        let fuelType = '';
        const snapshots = await database.collection('fuel_type').where('id', '==', fuelTypeId).get();
        if (snapshots.docs.length > 0) {
            fuelType = snapshots.docs[0].data().name;
            $(`.fuel_${custId}`).html(fuelType);
        }
        return fuelType;
    }

    async function getVehicleType(vehId, custId) {
        let vehType = '';
        const snapshots = await database.collection('vehicle_type').where('id', '==', vehId).get();
        if (snapshots.docs.length > 0) {
            vehType = snapshots.docs[0].data().name;
            $(`.vehicle_${custId}`).html(vehType);
        }
        return vehType;
    }

    $("#is_active").click(function() {
        $("#userTable .is_open").prop('checked',$(this).prop('checked'));
    });

    $("#deleteAll").click(function() {
        if($('#userTable .is_open:checked').length) {
            var is_disable_delete="<?php echo env('IS_DISABLE_DELETE', 0); ?>";
            if(is_disable_delete==1) {
                alert(doNotDeleteAlert);
                return false;
            }
            if(confirm(deleteSelectedRecordMsg)) {
                jQuery("#overlay").show();
                $('#userTable .is_open:checked').each(function() {
                    var dataId=$(this).attr('dataId');

                    database.collection('users').doc(dataId).update({'isDeletedAccount':true}).then(function() {
                        //deleteUserData(dataId);
                        logFirebaseActivity('#' + dataId + ' user deleted');
                        setTimeout(function() {
                            window.location.reload();
                        },7000);
                    });
                });
            } else {
                return false;
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });

    async function deleteUserData(userId) {
        var route1='{{route("delete-user", ":id")}}';
        route1=route1.replace(':id',userId);
        jQuery.ajax({
            url: route1,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo csrf_token() ?>'
            },
            success: function(data) {
                console.log('Delete user success:',data);
            },
        });
    }

    $(document).on("click","a[name='user-delete']",function(e) {
        if(confirm(deleteMsg)) {
            jQuery("#overlay").show();
            var id=this.id;

            database.collection('users').doc(id).update({'isDeletedAccount':true}).then(function(result) {
               // deleteUserData(id);
               logFirebaseActivity('#' + id + ' user deleted');
                setTimeout(function() {
                    window.location.href='{{ url()->current() }}';
                },7000);
            });

        } else {
            return false;
        }

    });


    $(document).on("click","a[name='show-documents']",async function(e) {
        var id=this.id;
        var isVerified=$(this).attr('data-verified');
        if(isVerified=='true'||isVerified==true) {
            $('.approveBtnDiv').addClass('d-none');
        }
        await database.collection('users').where('id','==',id).get().then(async function(snapshots) {

            if(snapshots.docs.length>0) {
                var data=snapshots.docs[0].data();
                if(data.hasOwnProperty('documentApproved')&&data.documentApproved==true) {
                    $('.approveBtnDiv').html("<span class='badge badge-primary' style='padding:12px 12px'>{{trans('lang.approved')}}</span>");

                } else {
                    $('#approveDocBtn').html("{{trans('lang.approve')}}");
                    $('#approveDocBtn').prop('disabled',false);
                }
                var documents=[];
                vehicleRegistrationNumber=data.vehicleRegistrationNumber.url;
                documents.push(vehicleRegistrationNumber);
                documents.push(data.vehicleRegistrationcertificate.url);
                if(data.hasOwnProperty('vehicleChassisNumber')&&data.vehicleChassisNumber!=null&&data.vehicleChassisNumber!='') {
                    documents.push(data.vehicleChassisNumber.url);
                }
                if(documents.length>0) {
                    $('#exampleModal').modal('show');
                } else {
                    $('#exampleModal').modal('hide');
                    alert('{{trans("lang.no_document_uploaded")}}');
                }
                if(documents.length==1) {
                    $(".carousel-control-prev").hide();
                    $(".carousel-control-next").hide();
                } else {
                    $(".carousel-control-prev").show();
                    $(".carousel-control-next").show();
                }
                $('#carousel-inner').empty();
                var carouselInner=document.getElementById('carousel-inner');

                documents.forEach(function(url,index) {

                    var carouselItem=document.createElement('div');
                    carouselItem.className='carousel-item';
                    if(index===0) {
                        carouselItem.classList.add('active');
                    }

                    var img=document.createElement('img');
                    img.className='d-block w-100';
                    img.src=url;

                    carouselItem.appendChild(img);
                    carouselInner.appendChild(carouselItem);
                });
                $('#userId').val(data.id);
                $('#userEmail').val(data.email);



            }

        });

    });
    $('#approveDocBtn').on('click',function() {
        var userId=$('#userId').val();
        var email=$('#userEmail').val();

        database.collection('users').doc(userId).update({
            'documentApproved': true
        }).then(async function(result) {
            logFirebaseActivity('#' + userId + ' user document approved');
            $('#exampleModal').modal('hide');
            window.location.reload();
        });
    })
  

</script>

@endsection