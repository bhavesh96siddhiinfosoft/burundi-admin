@extends('layouts.app')

@section('content')
    <div class="page-wrapper">

        <div class="row page-titles">

            <div class="col-md-5 align-self-center">

                <h3 class="text-themecolor">

                    {{ trans('lang.deleted_owner') }}
                </h3>

            </div>

            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.deleted_owner') }}</li>
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
                                            <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active">
                                                    <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i> {{ trans('lang.all') }}</a></label></th>
                                            <th>{{ trans('lang.image') }}</th>
                                            <th>{{ trans('lang.name') }}</th>
                                            <th>{{ trans('lang.phone') }}</th>
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
@endsection

@section('scripts')
    <script type="text/javascript">
        var database = firebase.firestore();

        var ref = database.collection('users').where('role', '==', 'owner').where('isDeletedAccount', '==', true);
        var append_list = '';
        var placeholderImage = '';
        var restoreMsg = "{{ trans('lang.restore_owner_msg') }}";
        var defaultImg = "{{ asset('/images/default_user.png') }}";
        var deleteMsg = "{{ trans('lang.delete_permanant_alert') }}";
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
                        [1, 'asc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [0, 3],
                    }],
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
            html = html + '<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +
                'for="is_open_' + id + '" ></label></td>';

            if (val.profilePic == '' || val.profilePic == null) {

                html = html + '<td><img class="rounded" style="width:50px" src="' + defaultImg + '" alt="image"></td>';
            } else {
                html = html + '<td><img class="rounded" style="width:50px" src="' + val.profilePic + '" alt="image"></td>';
            }

            html = html + '<td>' + val.fullName + '</td>';
            html = html + '<td>' + '+' + (val.countryCode.includes('+') ? val.countryCode.slice(1) : val.countryCode) + '-' + val.mobileNumber + '</td>';

            html = html + '<td class="action-btn"><a href="javascript:void(0)" id="' + id + '" name="restore-customer"><i class="fa fa-refresh action-btn"></i></a>';
            html = html + '<a id="' + val.id + '" name="owner-delete" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a>';

            html = html + '</td>';

            html = html + '</tr>';
            return html;
        }
        $("#is_active").click(function() {
            $("#example24 .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(function() {
            if ($('#example24 .is_open:checked').length) {

                if (confirm('Are You Sure want to Delete Selected Records Permanently?')) {
                    jQuery("#overlay").show();
                    $('#example24 .is_open:checked').each(function() {
                        var dataId = $(this).attr('dataId');
                        database.collection('users').doc(dataId).delete().then(async function(result) {
                            var refEmployee = await database.collection('users').where('role', '==', 'employee').where('ownerId', '==', dataId).get();
                            if (refEmployee.empty) {
                                window.location.href = '{{ url()->current() }}';
                            } else {
                                for (const doc of refEmployee.docs) {
                                    await database.collection('users').doc(doc.id).delete().then(async function(result) {
                                        logFirebaseActivity('#' + doc.id + ' {{trans("lang.user")}} {{trans("lang.deleted")}}');
                                        window.location.href = '{{ url()->current() }}';
                                    })
                                }
                            }
                        });
                    });
                    
                }
            } else {
                alert('Please Select Any One Record .');
            }
        });



        $(document).on("click", "a[name='owner-delete']", function(e) {

            if (confirm(deleteMsg)) {
                var id = this.id;
                jQuery("#overlay").show();
                console.log(id);
                database.collection('users').doc(id).delete().then(async function(result) {
                    var refEmployee = await database.collection('users').where('role', '==', 'employee').where('ownerId', '==', id).get();
                    if (refEmployee.empty) {
                        window.location.href = '{{ url()->current() }}';
                    } else {
                        for (const doc of refEmployee.docs) {
                            await database.collection('users').doc(doc.id).delete().then(async function(result) {
                                logFirebaseActivity('#' + doc.id + ' {{trans("lang.user")}} {{trans("lang.deleted")}}');
                                window.location.href = '{{ url()->current() }}';
                            })
                        }
                    }


                });
            } else {
                return false;
            }
        });


        $(document).on("click", "a[name='restore-customer']", function(e) {
            if (confirm(restoreMsg)) {
                jQuery("#overlay").show();
                var id = this.id;

                database.collection('users').doc(id).update({
                    'isDeletedAccount': false
                }).then(async function(result) {

                    var refEmployee = await database.collection('users').where('role', '==', 'employee').where('ownerId', '==', id).get();
                    for (const doc of refEmployee.docs) {
                        await database.collection('users').doc(doc.id).update({
                            'enable': true
                        });
                    }
                    logFirebaseActivity('#' + id + ' {{trans("lang.user")}} {{trans("lang.restored")}}');
                    window.location.href = '{{ url()->current() }}';
                });

            } else {
                return false;
            }

        });
    </script>
@endsection
