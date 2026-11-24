@extends('layouts.app')

@section('content')
    <div class="page-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">
                    {{ trans('lang.deleted_customers') }}
                </h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.customer_table') }}</li>
                </ol>
            </div>
            <div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header" style="margin-left:300px">

                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="userTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active">
                                                    <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i> {{ trans('lang.all') }}</a></label></th>

                                            <th>{{ trans('lang.image') }}</th>
                                            <th>{{ trans('lang.name') }}</th>
                                            <th>{{ trans('lang.phone') }}</th>
                                            <th>{{ trans('lang.vehicle_number') }}</th>
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
        var defaultImg = "{{ asset('/images/default_user.png') }}";
        var offest = 1;
        var pagesize = 10;
        var end = null;
        var endarray = [];
        var start = null;
        var user_number = [];
        var placeholderImage = '';
        var append_list = '';
        var url = window.location.href;
        var ref = database.collection('users').where('role', '==', 'customer').where('isDeletedAccount', '==', true);

        let lastVisible = null;
        const batchSize = 10;
        let totalRecords = 0;
        var restoreMsg = "{{ trans('lang.restore_msg') }}";
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
            await fetchTotalRecords();
            $('#userTable').DataTable({
                serverSide: true,
                processing: true,
                pageLength: batchSize,
                order: [
                    [1, 'desc']
                ],
                ajax: function(data, callback) {
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
                    {
                        data: 'deleteAll'
                    }, 
                    {
                        data: 'profilePic'
                    },
                    {
                        data: 'fullName'
                    },
                    {
                        data: 'mobileNumber'
                    },
                    {
                        data: 'vehicleNumber'
                    },
                    {
                        data: 'actions'
                    }
                ].filter(Boolean),
                columnDefs: [{
                        targets: 1,
                        type: 'date'
                    },
                    {
                        targets: '_all',
                        orderable: false
                    }
                ],
                language: {
                    zeroRecords: "{{ trans('lang.no_record_found') }}",
                    emptyTable: "{{ trans('lang.no_record_found') }}"
                }
            });
            $(document.body).on('click', '.redirecttopage', function() {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });

            jQuery("#overlay").hide();


        });

        async function fetchFirestoreData(startIndex, limit, searchValue) {

            let query = ref.limit(limit);

            let searchResults = [];

            if (searchValue) {
                let searchPromises = ['fullName', 'mobileNumber', 'vehicleNumber'].map(field => {
                    let searchQuery = ref.orderBy(field)
                        .where(field, '>=', searchValue)
                        .where(field, '<=', searchValue + '\uf8ff')
                        .limit(limit);
                    return searchQuery.get();
                });
                if (startIndex === 0) {
                    lastVisible = null;
                } else if (lastVisible) {
                    query = query.startAfter(lastVisible);
                }

                const results = await Promise.all(searchPromises);
                results.forEach(snapshot => {
                    snapshot.forEach(doc => {
                        if (!searchResults.find(item => item.id === doc.id)) {
                            searchResults.push({
                                id: doc.id,
                                ...doc.data()
                            });
                        }
                    });
                });
            } else {
                if (startIndex === 0) {
                    lastVisible = null;
                } else if (lastVisible) {
                    query = query.startAfter(lastVisible);
                }

                const snapshots = await query.get();
                lastVisible = snapshots.docs[snapshots.docs.length - 1];
                snapshots.forEach(doc => searchResults.push({
                    id: doc.id,
                    ...doc.data()
                }));
            }

            const data = await Promise.all(searchResults.map(async (val) => {

                const id = val.id;
                return {
                    deleteAll: `<span class="delete-all"><input type="checkbox" id="is_open_${id}" class="is_open" dataId="${id}"><label class="col-3 control-label"
                    for="is_open_${id}" ></label></span>`,
                    profilePic: `<img class="rounded" style="width:50px" src="${val.profilePic||defaultImg}" alt="image">`,
                    fullName: val.fullName ? val.fullName : '',
                    mobileNumber: val.mobileNumber ? `+${val.countryCode.includes('+')? val.countryCode.slice(1):val.countryCode}-${val.mobileNumber}` : '',
                    vehicleNumber: val.vehicleNumber ? val.vehicleNumber.toUpperCase() : '',
                    actions: `<a href="javascript:void(0)" id="${val.id}" name="restore-customer"><i class="fa fa-refresh action-btn"></i></a> <a id="${val.id}" name="customer-delete" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a>`
                };
            }));

            return {
                data: data
            };
        }


        $(document).on("click", "a[name='restore-customer']", function(e) {
            if (confirm(restoreMsg)) {
                jQuery("#overlay").show();
                var id = this.id;

                database.collection('users').doc(id).update({
                    'isDeletedAccount': false
                }).then(function(result) {
                    logFirebaseActivity('#' + id + ' restore deleted customer');
                    window.location.href = '{{ url()->current() }}';
                });

            } else {
                return false;
            }

        });
        $("#is_active").click(function() {
            $("#userTable .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(function() {
            if ($('#userTable .is_open:checked').length) {

                if (confirm('Are you sure you want to delete selected record permanently?')) {
                    jQuery("#overlay").show();
                    $('#userTable .is_open:checked').each(function() {
                        var dataId = $(this).attr('dataId');
                        database.collection('users').doc(dataId).delete().then(async function(result) {
                                  logFirebaseActivity('#' + dataId + ' delete deleted customer');
                                window.location.href = '{{ url()->current() }}';
                            
                        });
                    });

                }
            } else {
                alert('Please Select Any One Record .');
            }
        });



        $(document).on("click", "a[name='customer-delete']", function(e) {

            if (confirm('Are you sure you want to delete selected record permanently?')) {
                var id = this.id;
                console.log(id);
                jQuery("#overlay").show();
                database.collection('users').doc(id).delete().then(async function(result) {
                     logFirebaseActivity('#' + id + ' delete deleted customer');
                        window.location.href = '{{ url()->current() }}';
                    
                });
            } else {
                return false;
            }
        });
        


        /*database.collection('users').where('role','==','customer').get().then(async function(snapshot){
            const docs=snapshot.docs;
            await processInBatches(docs,5);
            console.log('All batches committed');
            window.location.reload();

          
        })
            const processInBatches=async (docs,maxConcurrentBatches) => {
                while(docs.length>0) {
                    // Create an array to hold up to `maxConcurrentBatches` promises
                    const batchPromises=[];

                    for(let i=0;i<maxConcurrentBatches&&docs.length>0;i++) {
                        const batch=database.batch();
                        const chunk=docs.splice(0,500); // Firestore allows max 500 operations per batch

                        // Add updates to the batch
                        chunk.forEach((doc) => {
                            batch.update(database.collection('users').doc(doc.id),{
                                isDeletedAccount: false
                            });
                        });

                        // Add the batch commit to the promises array
                        batchPromises.push(batch.commit());
                    }

                    // Wait for all the current batches to complete
                    await Promise.all(batchPromises);
                }
            };
            */
    </script>
@endsection
