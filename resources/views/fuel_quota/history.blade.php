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

                                        <th>{{trans('lang.added_quota')}}</th>
                                        <th>{{trans('lang.total_fuel_quota')}}</th>
                                        <th>{{trans('lang.available_quota')}}</th>
                                        <th>{{trans('lang.date')}}</th>
                                        <th>{{trans('lang.note')}}</th>

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
    var ref = database.collection('fuel_reset_transaction').orderBy('date', 'desc');
    var append_list = '';

    $(document).ready(function () {
        jQuery("#overlay").show();
        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';
        ref.get().then(async function (snapshots) {
            var html = '';
            if (snapshots.docs.length > 0) {
                html = await buildHTML(snapshots);
            }
            if (html != '') {
                append_list.innerHTML = html;
            }

            $('#fuelTable').DataTable({
                order: [[3, 'desc']],
                columnDefs: [{
                    targets: 3,
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
        newdate = '';
        var id = val.id;

        html = html + '<td><strong>{{trans("lang.petrol")}} : </strong>' + ((val.petrol.newAdded!='') ? val.petrol.newAdded : '-')+ '<br><strong>{{trans("lang.diesel")}} :</strong>' + ((val.diesel.newAdded!=null) ? val.diesel.newAdded : '-') + '<br><strong>{{trans("lang.other")}} :</strong>' + ((val.other.newAdded) ? val.other.newAdded : '-') + '</td>';
        html = html + '<td><strong>{{trans("lang.petrol")}} : </strong>' + val.petrol.total + '<br><strong>{{trans("lang.diesel")}} :</strong>' + val.diesel.total + '<br><strong>{{trans("lang.other")}} :</strong>' + val.other.total + '</td>';
        html = html + '<td><strong>{{trans("lang.petrol")}} : </strong>' + val.petrol.available + '<br><strong>{{trans("lang.diesel")}} :</strong>' + val.diesel.available + '<br><strong>{{trans("lang.other")}} :</strong>' + val.other.available + '</td>';
        var date = '';
        var time = '';
        if (val.hasOwnProperty("date")) {
            try {
                date = val.date.toDate().toDateString();
                time = val.date.toDate().toLocaleTimeString('en-US');
            } catch (err) {

            }
            html = html + '<td class="dt-time">' + date + ' ' + time + '</td>';
        } else {
            html = html + '<td></td>';
        }
        if (val.type == "reset") {
            var type = 'Fuel Quota Reset';
        } else {
            var type = 'Fuel Quota Added';
        }
        html = html + '<td>' + type + '</td>';
        html = html + '</tr>';
        return html;
    }


</script>

@endsection