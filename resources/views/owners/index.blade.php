@extends('layouts.app')

@section('content')
    @php
        $type = 'all';
    @endphp
    <div class="page-wrapper">

        <div class="row page-titles">

            <div class="col-md-5 align-self-center">

                <h3 class="text-themecolor">
                    @if (request()->is('owners/approved'))
                        @php $type = 'approved'; @endphp
                        {{ trans('lang.approved_owner') }}
                    @elseif(request()->is('owners/pending'))
                        @php $type = 'pending'; @endphp
                        {{ trans('lang.approval_pending_owner') }}
                    @else
                        {{ trans('lang.all_owner') }}
                    @endif
                </h3>

            </div>

            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.petrol_pump_owner') }}</li>
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
                                    <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-list mr-2"></i>{{ trans('lang.owners_list') }}</a>
                                </li>

                            </ul>
                        </div>

                        <div class="card-body">

                            <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">Processing...
                            </div>

                            <div class="table-responsive m-t-10">

                                <table id="example24" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">

                                    <thead>

                                        <tr>
                                            @if (in_array('owner.delete', json_decode(@session('user_permissions'))))
                                                <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active">
                                                        <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i> {{ trans('lang.all') }}</a></label></th>
                                            @endif
                                            <th>{{ trans('lang.image') }}</th>
                                            <th>{{ trans('lang.name') }}</th>
                                            <th>{{ trans('lang.phone') }}</th>
                                            <th>{{ trans('lang.fuel_station_name') }}</th>
                                            <th>{{ trans('lang.last_fuel_allocated') }}</th>
                                            <th>{{ trans('lang.fuel_quota') }}</th>
                                            <th>{{ trans('lang.document_verification') }}</th>
                                            <th>{{ trans('lang.actions') }}</th>

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

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('lang.documents') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" id="OwnerId" hidden>
                    <input type="text" id="OwnerEmail" hidden>
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
                    <div class="modal-footer">
                        <div class="approveBtnDiv"><button type="button" class="btn btn-primary" data-dismiss="modal" id="approveDocBtn">{{ trans('lang.approve') }}</button></div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('lang.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        var database = firebase.firestore();
        var type = "{{ $type }}";
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
            var ref = database.collection('users').where('role', '==', 'owner').where('createdAt', '>=', startTimestamp).where('createdAt', '<=', endTimestamp);
        } else {
            var ref = database.collection('users').where('role', '==', 'owner');
        }
        if (type == 'pending') {
            ref = database.collection('users').where('role', '==', 'owner').where("documentApproved", "==", false);
        } else if (type == 'approved') {
            ref = database.collection('users').where('role', '==', 'owner').where("documentApproved", "==", true);
        }
        var append_list = '';
        var placeholderImage = '';
        var deleteMsg = "{{ trans('lang.delete_alert') }}";
        var defaultImg = "{{ asset('/images/default_user.png') }}";
        var user_permissions = '<?php echo @session('user_permissions'); ?>';
        user_permissions = JSON.parse(user_permissions);
        var checkDeletePermission = false;
        if ($.inArray('owner.delete', user_permissions) >= 0) {
            checkDeletePermission = true;
        }
        $(document).ready(function() {

            jQuery("#overlay").show();
            append_list = document.getElementById('append_list1');
            append_list.innerHTML = '';
            ref.get().then(async function(snapshots) {
                html = '';
                if (snapshots.docs.length > 0) {
                    html = await buildHTML(snapshots);
                }
                if (html != '') {
                    append_list.innerHTML = html;

                }

                $('#example24').DataTable({
                    order: [
                        (checkDeletePermission) ? [2, 'asc'] : [1, 'asc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: (checkDeletePermission) ? [0, 1, 6, 7, 8] : [0, 5, 6, 7]
                    }, ],
                    "language": {
                        "zeroRecords": "{{ trans('lang.no_record_found') }}",
                        "emptyTable": "{{ trans('lang.no_record_found') }}"
                    },
                    // responsive: true
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
            var route1 = '{{ route('owners.edit', ':id') }}';
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
            if (val.documentApproved) {
                html = html + '<td><a href="' + route1 + '">' + val.fullName + '<sup class="badge badge-success ml-2"><i class="fa fa-check"></i> {{ trans('lang.verified') }}</sup></a></td>';

            } else {
                html = html + '<td><a href="' + route1 + '">' + val.fullName + '</a></td>';

            }
            html = html + '<td>' + '+' + (val.countryCode.includes('+') ? val.countryCode.slice(1) : val.countryCode) + '-' + val.mobileNumber + '</td>';
            html = html + '<td>' + val.fuelStationName + '</td>'
            var allocatedPetrolDate = '';
            var allocatedPetrolTime = '';

            var allocatedDieselDate = '';
            var allocatedDieselTime = '';

            var allocatedOtherDate = '';
            var allocatedOtherTime = '';
            if (val.hasOwnProperty("allocatedPetrolDate")) {
                try {
                    allocatedPetrolDate = val.allocatedPetrolDate.toDate().toDateString();
                    allocatedPetrolTime = val.allocatedPetrolDate.toDate().toLocaleTimeString('en-US');
                } catch (err) {

                }
            }
            if (val.hasOwnProperty("allocatedDieselDate")) {
                try {
                    allocatedDieselDate = val.allocatedDieselDate.toDate().toDateString();
                    allocatedDieselTime = val.allocatedDieselDate.toDate().toLocaleTimeString('en-US');
                } catch (err) {

                }
            }
            if (val.hasOwnProperty("allocatedOtherDate")) {
                try {
                    allocatedOtherDate = val.allocatedOtherDate.toDate().toDateString();
                    allocatedOtherTime = val.allocatedOtherDate.toDate().toLocaleTimeString('en-US');
                } catch (err) {

                }
            }
            var allocatedDateHtml = '';
            allocatedDateHtml += '<div><strong>{{ trans('lang.allocatedPetrolDate') }}</strong> : <span>' + allocatedPetrolDate + '' + allocatedPetrolTime + '\n</span><br><strong>{{ trans('lang.allocatedDieselDate') }}</strong> : <span>' + allocatedDieselDate + ' ' + allocatedDieselTime + '\n</span><br><strong>{{ trans('lang.allocatedOtherDate') }}</strong> : <span>' + allocatedOtherDate + ' ' + allocatedOtherTime + '\n</span></div>';

            html += '<td class="dt-time"><strong class="mb-2">' + allocatedDateHtml + '</td>';


            var totalFuelHtml = '';
            var availableFuelHtml = '';
            if (val.fuel.length > 0) {
                val.fuel.forEach(element => {
                    totalFuelHtml += '<div><strong>' + element.name + '</strong> : <span>' + element.totalFuelQuota + ' {{ trans('lang.liter') }}\n</span></div>';
                    availableFuelHtml += '<div><strong>' + element.name + '</strong> : <span>' + element.availableFuelQuota + ' {{ trans('lang.liter') }}\n</span></div>';

                });
            }
            html = html + '<td><strong class="mb-2">{{ trans('lang.total_fuel_quota') }} : ' + totalFuelHtml + '</strong><br><strong>{{ trans('lang.availableFuelQuota') }} : ' + availableFuelHtml + '</td>';
            if (val.documentApproved) {
                html = html + '<td class="action-btn"><a href="javascript:void(0)" class="btn btn-primary btn-sm" name="show-documents" data-verified="true" id="' + val.id + '">{{ trans('lang.document_verified') }}</a></td>';

            } else {
                html = html + '<td class="action-btn"><a href="javascript:void(0)" class="btn btn-primary btn-sm" name="show-documents" data-verified="false" id="' + val.id + '">{{ trans('lang.verify_document') }}</a></td>';

            }
            var receiptsRoute = '{{ route('owners.fuelReceipts', ':id') }}'.replace(':id', id);
            html = html + '<td class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a>';
            html = html + '<a href="' + receiptsRoute + '" title="{{ trans('lang.fuel_receiving_history') }}"><i class="fa fa-history"></i></a>';
            if (checkDeletePermission) {
                html = html + '<a id="' + val.id + '" name="owner-delete" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a>';
            }
            html = html + '</td>';
            html = html + '</tr>';
            return html;
        }

        $("#is_active").click(function() {
            $("#example24 .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(function() {
            if ($('#example24 .is_open:checked').length) {
                var is_disable_delete = "<?php echo env('IS_DISABLE_DELETE', 0); ?>";
                if (is_disable_delete == 1) {
                    alert(doNotDeleteAlert);
                    return false;
                }
                if (confirm('Are You Sure want to Delete Selected Data ?')) {
                    jQuery("#overlay").show();
                    $('#example24 .is_open:checked').each(function() {
                            var dataId = $(this).attr('dataId');
                            database.collection('users').doc(dataId).update({
                                'isDeletedAccount': true
                            }).then(async function(result) {
                                    logFirebaseActivity('#' + dataId + ' {{trans("lang.user")}} {{trans("lang.soft_deleted")}}');
                                    //deleteUserData(dataId);
                                    var refEmployee = await database.collection('users').where('role', '==', 'employee').where('ownerId', '==', dataId).get();
                                    for (const doc of refEmployee.docs) {
                                        logFirebaseActivity('#' + doc.id + ' {{trans("lang.user")}} {{trans("lang.soft_deleted")}}');
                                        await database.collection('users').doc(doc.id).update({
                                            'enable': false
                                        });
                                    }

                            });
                    });
                setTimeout(function() {
                    window.location.href = '{{ url()->current() }}';
                }, 5000);
            }
        } else {
            alert('Please Select Any One Record .');
        }
        });



        $(document).on("click", "a[name='owner-delete']", function(e) {

            if (confirm(deleteMsg)) {
                var id = this.id;
                jQuery("#overlay").show();
                database.collection('users').doc(id).update({
                    'isDeletedAccount': true
                }).then(async function(result) {
                        //deleteUserData(id);
                        var refEmployee = await database.collection('users').where('role', '==', 'employee').where('ownerId', '==', id).get();
                         logFirebaseActivity('#' + id + ' {{trans("lang.user")}} {{trans("lang.soft_deleted")}}');
                        for (const doc of refEmployee.docs) {
                             logFirebaseActivity('#' + doc.id + ' {{trans("lang.user")}} {{trans("lang.soft_deleted")}}');
                            await database.collection('users').doc(doc.id).update({
                                'enable': false
                            });
                        }
                    setTimeout(function() {
                        window.location.href = '{{ url()->current() }}';
                    }, 5000);
                });
        } else {
            return false;
        }
        });

        function disableClick() {
            var is_disable_delete = "<?php echo env('IS_DISABLE_DELETE', 0); ?>";
            if (is_disable_delete == 1) {
                jQuery("a.do_not_delete").removeAttr("name");
                jQuery("a.do_not_delete").attr("name", "alert_demo");
            }
        }

        $(document).on("click", "a[name='alert_demo']", function(e) {
            alert(doNotDeleteAlert);
        });

        $(document).on("click", "a[name='show-documents']", async function(e) {
            var id = this.id;
            var isVerified = $(this).attr('data-verified');
            if (isVerified == 'true' || isVerified == true) {
                $('.approveBtnDiv').addClass('d-none');
            }
            await database.collection('users').where('id', '==', id).get().then(async function(snapshots) {

                if (snapshots.docs.length > 0) {
                    var data = snapshots.docs[0].data();
                    if (data.hasOwnProperty('documentApproved') && data.documentApproved == true) {
                        $('.approveBtnDiv').html("<span class='badge badge-primary' style='padding:12px 12px'>{{ trans('lang.approved') }}</span>");

                    } else {
                        $('#approveDocBtn').html("{{ trans('lang.approve') }}");
                        $('#approveDocBtn').prop('disabled', false);
                    }
                    var images = [];
                    images.push(data.registrationCertificate.url);
                    if (data.hasOwnProperty('fuelStationImage') && data.fuelStationImage != '' && data.fuelStationImage != null) {
                        images.push(data.fuelStationImage.url);
                    }

                    if (images.length > 0) {
                        $('#exampleModal').modal('show');
                    } else {
                        $('#exampleModal').modal('hide');
                        alert('{{ trans('lang.no_document_uploaded ') }}');
                    }
                    if (images.length == 1) {
                        $(".carousel-control-prev").hide();
                        $(".carousel-control-next").hide();
                    } else {
                        $(".carousel-control-prev").show();
                        $(".carousel-control-next").show();
                    }
                    $('#carousel-inner').empty();
                    var carouselInner = document.getElementById('carousel-inner');

                    images.forEach(function(url, index) {
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
                    $('#OwnerId').val(data.id);
                    $('#OwnerEmail').val(data.email);
                }

            });

        });
        $('#approveDocBtn').on('click', function() {
            var OwnerId = $('#OwnerId').val();

            database.collection('users').doc(OwnerId).update({
                'documentApproved': true
            }).then(async function(result) {
                logFirebaseActivity('#' + OwnerId + ' {{trans("lang.user")}} {{trans("lang.document")}} {{trans("lang.approved")}}');
                window.location.reload();
            });
        })
        async function deleteUserData(userId) {
            var route1 = '{{ route('delete-user', ':id') }}';
            route1 = route1.replace(':id', userId);
            jQuery.ajax({
                url: route1,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>'
                },
                success: function(data) {
                    console.log('Delete user success:', data);
                },
            });

        }
    </script>
@endsection
