@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">
                {{trans('lang.fuel_quota_settings')}}
            </h3>

        </div>

        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                <li class="breadcrumb-item active">{{trans('lang.fuel_quota_settings')}}</li>

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
                                        class="fa fa-list mr-2"></i>{{trans('lang.list')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('fuel-quota.create')}}"><i
                                        class="fa fa-plus mr-2"></i>{{trans('lang.create')}}</a>
                            </li>

                        </ul>
                        <div class="view_history_btn"><a href="{{route('fuel-quota.history')}}" class="btn btn-primary view-page">{{trans('lang.view_history')}}</a></div>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive m-t-10">

                            <table id="driverTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">

                                <thead>

                                    <tr>

                                        <th>{{trans('lang.type_fuel')}}</th>
                                        <th>{{trans('lang.total_fuel_quota')}}</th>
                                        <th>{{trans('lang.available_quota')}}</th>
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
    var ref = database.collection('settings').doc("fuelQuota");
    var append_list = '';

    $(document).ready(function () {
        jQuery("#overlay").show();
        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';
        ref.get().then(async function (snapshots) {
            var html = '';
            var settingData = snapshots.data();
            if (settingData != undefined) {
                html += '<tr>';
                var petrolRoute = "{{route('fuel-quota.edit',':id')}}";
                petrolRoute = petrolRoute.replace(':id', 'petrol');
                html += '<td>{{trans("lang.petrol")}}</td>';
                html += '<td>' + settingData.petrol.total + '</td>';
                html += '<td>' + settingData.petrol.available + '</td>';
                html = html + '<td class="action-btn"><a href="' + petrolRoute + '"><i class="fa fa-edit"></i></a>';
                html += '</tr>';

                html += '<tr>';
                var dieselRoute = "{{route('fuel-quota.edit',':id')}}";
                dieselRoute = dieselRoute.replace(':id', 'diesel');
                html += '<td>{{trans("lang.diesel")}}</td>';
                html += '<td>' + settingData.diesel.total + '</td>';
                html += '<td>' + settingData.diesel.available + '</td>';
                html = html + '<td class="action-btn"><a href="' + dieselRoute + '"><i class="fa fa-edit"></i></a>';
                html += '</tr>';

                html += '<tr>';
                var otherRoute = "{{route('fuel-quota.edit',':id')}}";
                otherRoute = otherRoute.replace(':id', 'other');
                html += '<td>{{trans("lang.other")}}</td>';
                html += '<td>' + settingData.other.total + '</td>';
                html += '<td>' + settingData.other.available + '</td>';
                html = html + '<td class="action-btn"><a href="' + otherRoute + '"><i class="fa fa-edit"></i></a>';
                html += '</tr>';

            }
            if (html != '') {
                append_list.innerHTML = html;
            } else {
                append_list.innerHTML = '<p class="text-center">{{trans("lang.no_record_found")}}</p>';
            }
            jQuery("#overlay").hide();
        });


    });
 
</script>

@endsection