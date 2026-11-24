@extends('layouts.app')

@section('content')
@php
$type = 'all';
@endphp
<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                {{trans('lang.employee_table')}}
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.employee_table')}}</li>
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
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li class="nav-item">
                                <a class="nav-link active" href="{!! url()->current() !!}"><i
                                        class="fa fa-list mr-2"></i>{{trans('lang.employee_table')}}</a>
                            </li>
                            <!--<li class="nav-item">
                                <a class="nav-link" href="{!! route('employee.create') !!}"><i
                                        class="fa fa-plus mr-2"></i>{{trans('lang.employee_create')}}</a>
                            </li>-->
                        </ul>
                    </div>
                    <div class="card-body">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default"
                            style="display: none;">{{trans('lang.processing')}}
                        </div>

                        <div class="table-responsive m-t-10">
                            <table id="userTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        @if(in_array('employee.delete', json_decode(@session('user_permissions'))))

                                        <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                    class="do_not_delete" href="javascript:void(0)"><i
                                                        class="fa fa-trash"></i> {{trans('lang.all')}}</a></label></th>
                                        @endif
                                        <th>{{trans('lang.image')}}</th>
                                        <th>{{trans('lang.name')}}</th>
                                        <th>{{trans('lang.phone')}}</th>
                                        <th>{{trans('lang.owner')}}</th>
                                         <th>{{trans('lang.created_at')}}</th>
                                        <th>{{trans('lang.status')}}</th>
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

@endsection

@section('scripts')


<script type="text/javascript">
    var database = firebase.firestore();
    var defaultImg = "{{ asset('/images/default_user.png') }}";
    var offest = 1;
    var pagesize = 10;
    var end = null;
    var endarray = [];
    var start = null;
    var user_number = [];
    var placeholderImage = '';
    var append_list = '';
    var type = "{{$type}}";
    var currentDateTime = new Date();
    var startOfToday = new Date(currentDateTime);
    startOfToday.setHours(0, 0, 0, 0);
    var endOfToday = new Date(currentDateTime);
    endOfToday.setHours(23, 59, 59, 999);
    var startTimestamp = firebase.firestore.Timestamp.fromDate(startOfToday);
    var endTimestamp = firebase.firestore.Timestamp.fromDate(endOfToday);
    var url = window.location.href;
    var urlObj = new URL(url);
    if (urlObj.searchParams.has('today')) {
        var ref = database.collection('users').where('role', '==', 'employee').where('createdAt', '>=', startTimestamp).where('createdAt', '<=', endTimestamp).orderBy('createdAt', 'desc');

    } else {
        var ref = database.collection('users').where('role', '==', 'employee').orderBy('createdAt', 'desc');
    }
    
    var deleteMsg = "{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg = "{{trans('lang.selected_delete_alert')}}";
    var user_permissions = '<?php echo @session('user_permissions') ?>';
    user_permissions = JSON.parse(user_permissions);
    var checkDeletePermission = false;
    if ($.inArray('employee.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }
    $(document).ready(function () {
        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });
        jQuery("#overlay").show();
        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';
        ref.get().then(async function (snapshots) {
            html = '';
            html = await buildHTML(snapshots);
            if (html != '') {
                append_list.innerHTML = html;
            }
            $('#userTable').DataTable({
                order: [],
                columnDefs: [{
                    targets: (checkDeletePermission) ? 5 : 4,
                    type: 'date',
                    render: function (data) {
                        return data;
                    }
                },
                {
                    orderable: false,
                    targets: (checkDeletePermission) ? [0, 1, 5, 6,7] : [0, 4, 5,6]
                },
                ],
                "language": {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}"
                },
                responsive: true
            });

            jQuery("#overlay").hide();
        });
    });


    async function buildHTML(snapshots) {
        var html = '';
        await Promise.all(snapshots.docs.map(async (listval) => {
            var val = listval.data();
            var getData = await getListData(val);
            html += getData;
        }));
        return html;
    }

    async function getListData(val) {
        var html = '';
        html = html + '<tr>';
        var id = val.id;

        var route1 = '{{route("employee.edit", ":id")}}';
        route1 = route1.replace(':id', id);
        if (checkDeletePermission) {
            html = html + '<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +
                'for="is_open_' + id + '" ></label></td>';
        }
        if (val.profilePic == '' || val.profilePic == null) {

            html = html + '<td><img class="rounded" style="width:50px" src="' + defaultImg + '" alt="image"></td>';
        } else {
            html = html + '<td><img class="rounded" style="width:50px" src="' + val.profilePic + '" alt="image"></td>';
        }

        html = html + '<td><a href="' + route1 + '">' + val.fullName + '</a></td>';

        html = html + '<td>' + '+' + (val.countryCode.includes('+') ? val.countryCode.slice(1) : val.countryCode) + '-' + val.mobileNumber + '</td>';
        var owner=await getOwnerName(val.ownerId);
         html = html + '<td>'+owner+'</td>';
        var date = '';
        var time = '';
        if (val.hasOwnProperty("createdAt")) {
            try {
                date = val.createdAt.toDate().toDateString();
                time = val.createdAt.toDate().toLocaleTimeString('en-US');
            } catch (err) {

            }
            html = html + '<td class="dt-time">' + date + ' ' + time + '</td>';
        } else {
            html = html + '<td></td>';
        }
         if (val.enable) {
                html = html + '<td><label class="switch"><input type="checkbox" checked id="' + val.id + '" name="isActive"><span class="slider round"></span></label></td>';
            } else {
                html = html + '<td><label class="switch"><input type="checkbox" id="' + val.id + '" name="isActive"><span class="slider round"></span></label></td>';
            }
        html = html + '<td class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a>';
        if (checkDeletePermission) {
            html = html + '<a id="' + val.id + '" class="do_not_delete" name="user-delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a>';
        }
        html = html + '</td>';
        html = html + '</tr>';
        return html;
    }
    $(document).on("click", "input[name='isActive']", function (e) {
        var ischeck = $(this).is(':checked');
        var id = this.id;
        if (ischeck) {
            database.collection('users').doc(id).update({'enable': true}).then(function (result) {
                logFirebaseActivity('#' + id + ' user activated');
            });
        } else {
            database.collection('users').doc(id).update({'enable': false}).then(function (result) {
                logFirebaseActivity('#' + id + ' user deactivated');
            });
        }

    });

    async function getOwnerName(ownerId){
        var ownerName='';
        await database.collection('users').where('id','==',ownerId).get().then(async function(snapshots){
            if(snapshots.docs.length>0){
                ownerName=snapshots.docs[0].data().fullName;
            }
        })
        return ownerName;
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
    $("#is_active").click(function () {
        $("#userTable .is_open").prop('checked', $(this).prop('checked'));
    });

    $("#deleteAll").click(function () {
        if ($('#userTable .is_open:checked').length) {
            var is_disable_delete = "<?php echo env('IS_DISABLE_DELETE', 0); ?>";
            if (is_disable_delete == 1) {
                alert(doNotDeleteAlert);
                return false;
            }
            if (confirm(deleteSelectedRecordMsg)) {
                jQuery("#overlay").show();
                $('#userTable .is_open:checked').each(function () {
                    var dataId = $(this).attr('dataId');

                    database.collection('users').doc(dataId).delete().then(function () {
                        deleteUserData(dataId);
                        logFirebaseActivity('#' + dataId + ' user deleted');
                        setTimeout(function () {
                            window.location.reload();
                        }, 7000);
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
        var route1 = '{{route("delete-user", ":id")}}';
        route1 = route1.replace(':id', userId);
        jQuery.ajax({
            url: route1,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo csrf_token() ?>'
            },
            success: function (data) {
                console.log('Delete user success:', data);
            },
        });
    }

    $(document).on("click", "a[name='user-delete']", function (e) {

        var is_disable_delete = "<?php echo env('IS_DISABLE_DELETE'); ?>";
        if (is_disable_delete == 1) {
            alert(doNotDeleteAlert);
            return false;
        }

        if (confirm(deleteMsg)) {
            jQuery("#overlay").show();
            var id = this.id;

            database.collection('users').doc(id).delete().then(function (result) {
                deleteUserData(id);
                logFirebaseActivity('#' + id + ' user deleted');
                setTimeout(function () {
                    window.location.href = '{{ url()->current() }}';
                }, 7000);
            });

        } else {
            return false;
        }

    });


    $(document).on("click", "a[name='show-documents']", async function (e) {
        var id = this.id;

        await database.collection('users').where('id', '==', id).get().then(async function (snapshots) {

            if (snapshots.docs.length > 0) {
                var data = snapshots.docs[0].data();
                if (data.hasOwnProperty('documentApproved') && data.documentApproved == true) {
                    $('#approveDocBtn').html("{{trans('lang.approved')}}");
                    $('#approveDocBtn').prop('disabled', true);
                } else {
                    $('#approveDocBtn').html("{{trans('lang.approve')}}");
                    $('#approveDocBtn').prop('disabled', false);
                }
                var documents = [];
                vehicleRegistrationNumber = data.vehicleRegistrationNumber.url;

                documents.push(vehicleRegistrationNumber);
                documents.push(data.vehicleRegistrationcertificate.url);
                documents.push(data.vehicleChassisNumber.url);
                if (documents.length > 0) {
                    $('#exampleModal').modal('show');
                } else {
                    $('#exampleModal').modal('hide');
                    alert('{{trans("lang.no_document_uploaded")}}');
                }
                if (documents.length == 1) {
                    $(".carousel-control-prev").hide();
                    $(".carousel-control-next").hide();
                } else {
                    $(".carousel-control-prev").show();
                    $(".carousel-control-next").show();
                }
                $('#carousel-inner').empty();
                var carouselInner = document.getElementById('carousel-inner');

                documents.forEach(function (url, index) {

                    var carouselItem = document.createElement('div');
                    carouselItem.className = 'carousel-item';
                    if (index === 0) {
                        carouselItem.classList.add('active');
                    }

                    var img = document.createElement('img');
                    img.className = 'd-block w-100';
                    img.src = url;

                    carouselItem.appendChild(img);
                    carouselInner.appendChild(carouselItem);
                });
                $('#userId').val(data.id);
                $('#userEmail').val(data.email);
                // $('.availableFuelQuota').val(data.availableFuelQuota);
                // $('.totalFuelQuota').val(data.totalFuelQuota);
                // $('.capacity').val(data.vEngineCapacity);


            }

        });

    });
    $('#approveDocBtn').on('click', function () {
        var userId = $('#userId').val();
        var email = $('#userEmail').val();

        database.collection('users').doc(userId).update({
            'documentApproved': true
        }).then(async function (result) {
            logFirebaseActivity('#' + userId + ' user document approved');
            var url = "{{url('send-email')}}";
            var subject = "Document Approved";
            message = "<p>your document is verified by administrator.you can login now</p>";
            var sendEmailStatus = await sendEmail(url, subject, message, email);
            $('#exampleModal').modal('hide');
            /*if(sendEmailStatus){
             
            }*/
        });
    })

    async function sendEmail(url, subject, message, recipients) {

        var checkFlag = false;

        await $.ajax({

            type: 'POST',
            data: {
                subject: subject,
                message: btoa(message),
                recipients: recipients
            },
            url: url,
            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                checkFlag = true;
            },
            error: function (xhr, status, error) {
                checkFlag = true;
            }
        });

        return checkFlag;

    }
</script>

@endsection