@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- ============================================================== -->
    <!-- Bread crumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor"><span id="userName"></span> {{ trans('lang.driver_odometer_reading_history') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.driver_odometer_reading_history') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="alert alert-warning">{{ trans('lang.odometer_historical_notice') }}</div>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="table-responsive m-t-10">
                            <table id="odometerTable"
                                class="display nowrap table table-hover table-striped table-bordered"
                                cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th class="delete-all">
                                        <input type="checkbox" id="is_active">
                                        <label class="col-3 control-label" for="is_active">
                                            <a id="deleteAll" class="do_not_delete" href="javascript:void(0)">
                                                <i class="fa fa-trash"></i> {{ trans('lang.all') }}
                                            </a>
                                        </label>
                                    </th>
                                    <th>{{ trans('lang.image') }}</th>
                                    <th>{{ trans('lang.driver_id') }}</th>
                                    <th>{{ trans('lang.odometer_reading') }}</th>
                                    <th>{{ trans('lang.last_reading') }}</th>
                                    <th>{{ trans('lang.vehicle_mileage') }}</th>
                                    <th>{{ trans('lang.status') }}</th>
                                    <th>{{ trans('lang.date') }}</th>
                                    <th>{{ trans('lang.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody id="append_list1"></tbody>
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
var database = firebase.firestore();
var id = "{{$id}}";
var ref = database.collection('driver_odometer_reading_history').where('driverId','==',id);
var userref = database.collection('users').doc(id);
var append_list = document.getElementById('append_list1');
var deleteMsg = "{{ trans('lang.delete_alert') }}";
var deleteSelectedRecordMsg = "{{ trans('lang.selected_delete_alert') }}";

$(document).ready(function() {
    jQuery("#overlay").show();
    append_list.innerHTML = '';

    userref.get().then(async function(snapshots) {
       var userData = snapshots.data();
        if(userData){
            $('#userName').text(userData.fullName);
        }
    });
    ref.orderBy('createAt', 'desc').get().then(async function(snapshots) {
        let html = await buildHTML(snapshots);
        if (html !== '') append_list.innerHTML = html;

        $('#odometerTable').DataTable({
            order: [[6, 'desc']],
            columnDefs: [{ orderable: false, targets: [0,1,7] }],
            language: {
                zeroRecords: "{{ trans('lang.no_record_found') }}",
                emptyTable: "{{ trans('lang.no_record_found') }}"
            }
        });
        jQuery("#overlay").hide();
    });
});

async function buildHTML(snapshots) {
    let html = '';
    snapshots.docs.forEach(doc => {
        let val = doc.data();
        html += buildRow(val);
    });
    return html;
}

function buildRow(val) {
    let id = val.id || '';
    let odometerPic = val.odometerPic || '{{ asset("images/default_user.png") }}';
    let driverId = val.driverId || '-';
    let reading = val.odometerReading || '-';
    let lastreading = val.lastReading || '-';
    let mileage = val.vehicleMillage || '-';
    let status = val.status || '-';
    let date = val.createAt ? val.createAt.toDate().toLocaleString() : '-';
    var editRoute = '{{ route('customers.odometerReadingHistoryView', ':id') }}';
    editRoute = editRoute.replace(':id', id);

    return `
        <tr>
       <td class="delete-all">
                <input type="checkbox" class="is_open" id="is_open_${id}" dataId="${id}">
                <label for="is_open_${id}"></label>
            </td>
            <td><img src="${odometerPic}" style="width:60px; height:auto;" class="rounded"></td>
            <td>${driverId}</td>
            <td>${reading}</td>
            <td>${lastreading}</td>
            <td>${mileage}</td>
            <td>${status}</td>
            <td>${date}</td>
            <td class="action-btn">
                <a href="${editRoute}"><i class="fa fa-eye"></i></a>
                <a id="${id}" class="do_not_delete" name="deleteRecord" href="javascript:void(0)">
                    <i class="fa fa-trash"></i>
                </a>
            </td>
        </tr>`;
}

// Bulk select
$(document).on('click', '#is_active', function() {
    $(".is_open").prop('checked', this.checked);
});
// Uncheck "Select All" if any box is unchecked
$(document).on('click', '.is_open', function() {
    if (!this.checked) {
        $('#is_active').prop('checked', false);
    } else if ($('.is_open:checked').length === $('.is_open').length) {
        $('#is_active').prop('checked', true);
    }
});

// Bulk delete
$("#deleteAll").click(function() {
    if ($('#odometerTable .is_open:checked').length) {
        if (confirm(deleteSelectedRecordMsg)) {
            jQuery("#overlay").show();
            $('#odometerTable .is_open:checked').each(function() {
                let dataId = $(this).attr('dataId');
                console.log(dataId);
               
                database.collection('driver_odometer_reading_history').doc(dataId).delete().then(() => {
                    window.location.reload();
                });
            });
        }
    } else {
        alert("{{ trans('lang.select_delete_alert') }}");
    }
});

// Single delete
$(document).on("click", "a[name='deleteRecord']", function() {
    let id = this.id;
    if (confirm(deleteMsg)) {
        jQuery("#overlay").show();
            console.log(id);
        database.collection('driver_odometer_reading_history').doc(id).delete().then(() => {
            window.location.href = '{{ url()->current() }}';
        });
    }
});
</script>
@endsection
