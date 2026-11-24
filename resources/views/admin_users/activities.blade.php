@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Str;

    // helper to detect UUID-like strings
    function extractIdAndAction($text) {
        $text = trim($text);

        // not starting with # -> no id
        if (!Str::startsWith($text, '#')) {
            return ['id' => '-', 'action' => $text];
        }

        // starts with #[ ... ]  -> capture up to the closing bracket
        if (Str::startsWith($text, '#[')) {
            $closingPos = strpos($text, ']');
            if ($closingPos !== false) {
                $id = substr($text, 0, $closingPos + 1); // include closing ]
                $action = trim(substr($text, $closingPos + 1));
                return [
                    'id' => $id,
                    'action' => $action !== '' ? $action : '-',
                ];
            }
            // no closing bracket found -> fallback to splitting on whitespace
        }

        // default: take first whitespace-separated token as id
        $parts = preg_split('/\s+/', $text, 2);
        return [
            'id' => $parts[0],
            'action' => isset($parts[1]) && trim($parts[1]) !== '' ? $parts[1] : '-',
        ];
    }
@endphp
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.activity_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.activity_plural')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li class="nav-item">
                                <a class="nav-link active" href="{!! url()->current() !!}">
                                    <i class="fa fa-list mr-2"></i>{{trans('lang.activity_table')}}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
                            Processing...
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" id="searchMobile" class="form-control" placeholder="Search by Mobile Number">
                            </div>
                            <div class="col-md-6">
                                <input type="text" id="searchVehicle" class="form-control" placeholder="Search by Vehicle Number">
                            </div>
                            <!-- <div class="col-md-4">
                                <input type="text" id="searchCountry" class="form-control" placeholder="Search by Country Code">
                            </div> -->
                        </div>

                        <div class="table-responsive m-t-10">
                            <table id="activityTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th class="delete-all">
                                        <input type="checkbox" id="is_active">
                                        <label class="col-3 control-label" for="is_active">
                                            <a id="deleteAllActivity" class="do_not_delete" href="javascript:void(0)">
                                                <i class="fa fa-trash"></i> {{trans('lang.all')}}
                                            </a>
                                        </label>
                                    </th>
                                    <th>{{trans('lang.performed_by')}}</th>
                                    <th>{{trans('lang.action')}}</th>
                                    <th>{{trans('lang.performed_on_id')}}</th>
                                    <th>{{trans('lang.date')}}</th>
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>
                                </thead>

                                <tbody id="append_list1">
                                @foreach($activities as $activity)
                                    @php
                                        $parsed = extractIdAndAction($activity->action);
                                    @endphp
                                    <tr>
                                        <td class="delete-all">
                                            <input type="checkbox" id="is_open_{{$activity->id}}" class="activity_checkbox" data-id="{{$activity->id}}">
                                            <label class="col-3 control-label" for="is_open_{{$activity->id}}"></label>
                                        </td>
                                        <td>{{ optional($activity->user)->name ?? 'N/A' }}</td>
                                        <td>{{ $parsed['action'] }}</td>
                                       <td class="affected-id text-primary" style="cursor:pointer" data-id="{{ $parsed['id'] }}">
                                            {{ $parsed['id'] }}
                                        </td>
                                        <td>{{ $activity->created_at }}</td>
                                        <td class="action-btn">
                                            <a href="{{route('admin.activity.delete', ['id' => $activity->id])}}" class="do_not_delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
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
<script type="text/javascript">
    var user_permissions = '<?php echo @session('user_permissions')?>';
    user_permissions = JSON.parse(user_permissions);

    $('#activityTable').DataTable({
        order: [],
        columnDefs: [{orderable: false, targets: [0, 4]}],
        "language": {
            "zeroRecords": "{{trans("lang.no_record_found")}}",
            "emptyTable": "{{trans("lang.no_record_found")}}"
        },
        responsive: true
    });

    function searchUsersByField(field, value) {
        
        // If input cleared → show all activities
        if (!value || value.trim() === "") {
            $.ajax({
                url: "{{ route('admin.activities.searchByUsers') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    user_ids: [] // empty → controller returns all
                },
                success: function(response) {
                    $('#activityTable').DataTable().clear().destroy();
                    $("#append_list1").html(response.html);

                    // Reinitialize DataTable
                 
                    $('#activityTable').DataTable({
                        order: [],
                        columnDefs: [{orderable: false, targets: [0, 4]}],
                        "language": {
                            "zeroRecords": "{{trans("lang.no_record_found")}}",
                            "emptyTable": "{{trans("lang.no_record_found")}}"
                        },
                        responsive: true
                    });
                }
            });
            return;
        }

        var db = firebase.firestore();
        db.collection("users").where(field, "==", value).get().then(function(querySnapshot) {
            let userIds = [];
            querySnapshot.forEach(function(doc) {
                userIds.push(doc.id);
            });

            // Send userIds to Laravel
            $.ajax({
                url: "{{ route('admin.activities.searchByUsers') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    user_ids: userIds
                },
                success: function(response) {
                    console.log(response.html);
                    $('#activityTable').DataTable().clear().destroy();
                    $("#append_list1").html(response.html);

                    // Reinitialize DataTable
                 
                    $('#activityTable').DataTable({
                        order: [],
                        columnDefs: [{orderable: false, targets: [0, 4]}],
                        "language": {
                            "zeroRecords": "{{trans("lang.no_record_found")}}",
                            "emptyTable": "{{trans("lang.no_record_found")}}"
                        },
                        responsive: true
                    });
                }
            });
        });
    }

    // Bind inputs
    $("#searchMobile").on("keyup", function() {
        searchUsersByField("mobileNumber", $(this).val());
    });
    $("#searchVehicle").on("keyup", function() {
        searchUsersByField("vehicleNumber", $(this).val());
    });
    $("#searchCountry").on("keyup", function() {
        searchUsersByField("countryCode", $(this).val());
    });


    // Select all checkbox
    $("#is_active").click(function () {
        $(".activity_checkbox").prop('checked', $(this).prop('checked'));
    });

    // Delete all selected activities
    $("#deleteAllActivity").click(function () {
        var is_disable_delete = "{{ env('IS_DISABLE_DELETE', 0) }}";
        if (is_disable_delete == 1) {
            alert("Do not allow to change in demo content!");
            return false;
        }

        let selected = [];
        $('.activity_checkbox:checked').each(function () {
            selected.push($(this).attr('data-id'));
        });

        if (selected.length > 0) {
            if (confirm('Are you sure want to delete selected activity records?')) {
                var url = "{{ url('admin/activity/delete/multiple/') }}/" + selected.join(",");
                window.location.href = url;
            }
        } else {
            alert('Please select at least one record.');
        }
    });

    $(document).on("click", ".affected-id", function () {
        let userId = $(this).data("id");
        if (userId.startsWith("#")) {
                userId = userId.substring(1);
            }
        if (!userId || userId === "-") {
            return; // nothing to do
        }

        var db = firebase.firestore();
        db.collection("users").doc(userId).get().then(function (doc) {
            if (doc.exists) {
                let user = doc.data();
                if (user.role === "employee") {
                    window.location.href = "{{ url('employee/edit') }}/" + userId;
                } else if (user.role === "customer") {
                    window.location.href = "{{ url('customers/edit') }}/" + userId;
                } else if (user.role === "owner") {
                    window.location.href = "{{ url('owners/edit') }}/" + userId;
                } else {
                    alert("Unknown role: " + user.role);
                }
            } else {
                window.location.href = "#"; // no user found
            }
        }).catch(function (error) {
            console.error("Error fetching user:", error);
            window.location.href = "#";
        });
    });

    // Prevent delete on demo
    $(document).on("click", "a.do_not_delete", function (e) {
        var is_disable_delete = "{{ env('IS_DISABLE_DELETE', 0) }}";
        if (is_disable_delete == 1) {
            alert("Do not allow to change in demo content!");
            return false;
        }
    });
</script>
@endsection
