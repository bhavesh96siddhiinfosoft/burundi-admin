@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.client_wallet_transactions')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.client_wallet_transactions')}}</li>
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
                        <div id="data-table_processing" class="dataTables_processing panel panel-default"
                            style="display: none;">{{trans('lang.processing')}}
                        </div>


                        <div class="table-responsive m-t-10">
                            <table id="userTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>


                                        <th>{{trans('lang.id')}}</th>
                                        <th>{{trans('lang.user_name')}}</th>
                                        <th>{{trans('lang.total_amount')}}</th>
                                        <th>{{trans('lang.title')}}</th>
                                        <th>{{trans('lang.date')}}</th>
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


@endsection

@section('scripts')


<script type="text/javascript">

    var database = firebase.firestore();

    var symbolAtRight = false;
    var ref = database.collection('wallet_transaction').where("userType", "==", "client").orderBy('createdAt', 'desc');
    var refCurrency = database.collection('settings').doc('currency');
    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.data();
        currentCurrency = currencyData.currencySymbol;
        decimal_degits = currencyData.decimalDigit;

        if (currencyData.symbolAtRight) {
            symbolAtRight = true;
        }
    });

    var append_list = '';

    var deleteMsg = "{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg = "{{trans('lang.selected_delete_alert')}}";

    $(document).ready(function () {
        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });
        jQuery("#overlay").show();
        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';
        ref.get().then(async function (snapshots) {
            var html = '';
            if (snapshots.docs.length > 0) {
                html = await buildHTML(snapshots);
                jQuery("#overlay").hide();
            }
            if (html != '') {
                append_list.innerHTML = html;


            }

            $('#userTable').DataTable({
                processing: true,
                order: [],
                columnDefs: [
                    {
                        targets: 4,
                        type: 'date',
                        render: function (data) {
                            return data;
                        }
                    },
                    { orderable: false, targets: [2, 4] },
                ],
                order: [['4', 'desc']],
                "language": {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}"
                }
            });

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
        var id = val.userId;
        var route1 = '{{route("client.view",":id")}}';
        route1 = route1.replace(':id', id);
        var trroute1 = '';
        trroute1 = trroute1.replace(':id', id);
        var t_id = val.id;
        var transaction = t_id.substring(0, 7);
        html = html + '<td>' + transaction + '</td>';

        if (val.userId) {
            var userName = await payoutuserfunction(val.userId);
            if (userName) {
                html = html + '<td><a class="user_role_' + val.userId + '" href="' + route1 + '">' + userName + '</a></td>';
            } else {
                html = html + '<td>{{trans("lang.unknown_user")}}</td>';
            }
        } else {
            html = html + '<td></td>';
        }
        if (val.isCredit == false) {
            amount = Math.abs(val.amount);
            if (symbolAtRight) {
                amount = parseFloat(amount).toFixed(decimal_degits) + currentCurrency;
            } else {
                amount = currentCurrency + parseFloat(amount).toFixed(decimal_degits);
            }
            html = html + '<td><span class="text-danger">(-' + amount + ')</span></td>';
        } else {
            if (symbolAtRight) {
                amount = parseFloat(val.amount).toFixed(decimal_degits) + currentCurrency;
            } else {
                amount = currentCurrency + parseFloat(val.amount).toFixed(decimal_degits);
            }
            html = html + '<td><span class="text-success">(' + amount + ')</span></td>';
        }

        html = html + '<td>' + val.title + '</td>';

        if (val.hasOwnProperty("createdAt")) {
            var date = val.createdAt.toDate().toDateString();
            var time = val.createdAt.toDate().toLocaleTimeString('en-US');
            html = html + '<td class="dt-time">' + date + ' ' + time + '</td>';
        } else {
            html = html + '<td></td>';
        }

        html = html + '</tr>';
        return html;
    }

    async function payoutuserfunction(id) {
        var payoutuser = '';
        await database.collection("clients").where('id', '==', id).get().then(async function (snapshotss) {
            if (snapshotss.docs[0]) {
                var user_data = snapshotss.docs[0].data();
                payoutuser = user_data.firstName + " " + user_data.lastName;
            }
        });
        return payoutuser;
    }


</script>

@endsection