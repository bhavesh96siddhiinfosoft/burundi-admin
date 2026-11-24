@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">
                {{trans('lang.document')}}
            </h3>

        </div>

        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                <li class="breadcrumb-item active">{{trans('lang.document')}}</li>

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

                            <table id="driverTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">

                                <thead>

                                    <tr>
                                        <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active"><a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i> {{trans('lang.all')}}</a></label>
                                        </th>

                                        <th>{{trans('lang.title')}}</th>
                                        <th>{{trans('lang.distributor_comision')}}</th>
                                        <th>{{trans('lang.price')}}</th>
                                        <th>{{trans('lang.active')}}</th>
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

    var ref = database.collection('documents');
    var placeholderImage = "{{ asset('/images/default_user.png') }}";
    var deleteMsg = "{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg = "{{trans('lang.selected_delete_alert')}}";

    var append_list = '';

    $(document).ready(function() {
        jQuery("#overlay").show();
        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';
        ref.get().then(async function(snapshots) {
            var html = '';
            if (snapshots.docs.length > 0) {
                html = await buildHTML(snapshots);
            }
            if (html != '') {
                append_list.innerHTML = html;
            }

            var table = $('#driverTable').DataTable();

            table.destroy();

            table = $('#driverTable').DataTable({
                order: [],
                columnDefs: [{
                        // targets: 6,
                        // type: 'date',
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        orderable: false,
                        targets: [0, 3]
                    },
                ],
                order: ['2', 'desc'],
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
            html = html + '<tr>';
            var id = val.id;
            var route1 = '{{route("document.edit",":id")}}';
            route1 = route1.replace(':id', id);

            html = html + '<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +
                'for="is_open_' + id + '" ></label></td>';
            /* html=html+'<td>'+val.id+'</td>'; */

            if (val.hasOwnProperty("title") && val.title != "") {
                html = html + '<td>' + val.title + '</td>';
            } else {
                html = html + '<td></td>';
            }

            if (val.hasOwnProperty("distributorCommission") && val.distributorCommission != "") {
                html = html + '<td>' + val.distributorCommission + '</td>';
            } else {
                html = html + '<td></td>';
            }

            if (val.hasOwnProperty("price") && val.price != "") {
                html = html + '<td>' + val.price + '</td>';
            } else {
                html = html + '<td></td>';
            }

            if (val.enable) {
                html = html + '<td><label class="switch"><input type="checkbox" checked id="' + val.id + '" name="publish"><span class="slider round"></span></label></td>';
            } else {
                html = html + '<td><label class="switch"><input type="checkbox" id="' + val.id + '" name="publish"><span class="slider round"></span></label></td>';
            }
            html = html + '<td class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a></td>';
            html = html + '</tr>';
        }));
        return html;
    }


    $(document).on("click", "input[name='publish']", function(e) {
        var ischeck = $(this).is(':checked') ? true : false;
        var id = this.id;
        var is_published = ischeck ? 'published' : 'unpublished';
        database.collection('documents').doc(id).update({
            'enable': ischeck
        }).then(function(result) {
            logFirebaseActivity('#' + id + ' document '+ is_published +'');
        });
    });

    $("#is_active").click(function() {
        $("#driverTable .is_open").prop('checked', $(this).prop('checked'));
    });

    $("#deleteAll").click(function() {
        if ($('#driverTable .is_open:checked').length) {
            jQuery("#overlay").show();

            if (confirm("{{trans('lang.selected_delete_alert')}}")) {

                jQuery("#overlay").show();

                $('#driverTable .is_open:checked').each(function() {

                    var dataId = $(this).attr('dataId');

                    database.collection('documents').doc(dataId).delete().then(function() {
                        deleteUserData(dataId);
                        logFirebaseActivity('#' + dataId + ' document deleted');
                        setTimeout(function() {
                            window.location.reload();
                        }, 7000);

                    });

                });

            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });

    async function deleteUserData(userId) {
        //delete user from authentication
        var dataObject = {
            "data": {
                "uid": userId
            }
        };
        var projectId = '<?php echo env('FIREBASE_PROJECT_ID') ?>';
        jQuery.ajax({
            url: 'https://us-central1-' + projectId + '.cloudfunctions.net/deleteUser',
            method: 'POST',
            crossDomain: true,
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify(dataObject),
            success: function(data) {
                console.log('Delete user success:', data.result);
            },
            error: function(xhr, status, error) {
                var responseText = JSON.parse(xhr.responseText);
                console.log('Delete user error:', responseText.error);
            }
        });
    }

    $(document).on("click", "a[name='driver-delete']", function(e) {

        if (confirm(deleteMsg)) {
            jQuery("#overlay").show();
            var id = this.id;
            jQuery("#overlay").show();
            database.collection('driver_users').doc(id).delete().then(function(result) {
                logFirebaseActivity('#' + id + ' user deleted');
                setTimeout(function() {
                    window.location.href = '{{ url()->current() }}';
                }, 7000);
            });
        } else {
            return false;
        }
    });

    async function getServiceType(serviceId, id) {
        var title = '';
        if (serviceId != undefined) {
            await database.collection('service').doc(serviceId).get().then(async function(snapshots) {
                var service = snapshots.data();
                if (service != undefined) {
                    title = service.title;
                }
                //$('.driver_service' + id).html(service.title);
            });
        }
        return title;
    }

    async function deleteDriverData(driverId) {

        await database.collection('order_transactions').where('driverId', '==', driverId).get().then(async function(snapshotsOrderTransacation) {
            if (snapshotsOrderTransacation.docs.length > 0) {
                snapshotsOrderTransacation.docs.forEach((temData) => {
                    var item_data = temData.data();

                    database.collection('order_transactions').doc(item_data.id).delete().then(function() {

                    });
                });
            }

        });

        await database.collection('driver_payouts').where('driverID', '==', driverId).get().then(async function(snapshotsItem) {

            if (snapshotsItem.docs.length > 0) {
                snapshotsItem.docs.forEach((temData) => {
                    var item_data = temData.data();

                    database.collection('driver_payouts').doc(item_data.id).delete().then(function() {

                    });
                });
            }

        });

    }
</script>

@endsection