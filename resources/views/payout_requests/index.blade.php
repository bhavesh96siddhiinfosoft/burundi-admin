@extends('layouts.app')


@section('content')

<div class="page-wrapper">


    <div class="row page-titles">


        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.payout_request')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.payout_request')}}</li>

            </ol>

        </div>

    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">

                        <div class="table-responsive m-t-10">


                            <table id="payoutTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">

                                <thead>


                                    <tr>

                                        <th>{{ trans('lang.distributor_name')}}</th>
                                        <th>{{trans('lang.amount')}}</th>
                                        <th>{{trans('lang.note')}}</th>
                                        <th>{{trans('lang.paid_date')}}</th>
                                        <th>{{trans('lang.status')}}</th>
                                        <th>{{trans('lang.admin_note')}}</th>
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


<div class="modal fade" id="bankdetailsModal" tabindex="-1" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered location_modal">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title locationModalTitle">{{trans('lang.bankdetails')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <form class="">

                    <div class="form-row">

                        <input type="hidden" name="distributorId" id="distributorId">

                        <div class="form-group row">

                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{
                                    trans('lang.bank_name')}}</label>
                                <div class="col-12">
                                    <input type="text" name="bank_name" class="form-control" id="bankName">
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{
                                    trans('lang.branch_name')}}</label>
                                <div class="col-12">
                                    <input type="text" name="branch_name" class="form-control" id="branchName">
                                </div>
                            </div>


                            <div class="form-group row width-100">
                                <label class="col-4 control-label">{{
                                    trans('lang.holder_name')}}</label>
                                <div class="col-12">
                                    <input type="text" name="holer_name" class="form-control" id="holderName">
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{
                                    trans('lang.account_number')}}</label>
                                <div class="col-12">
                                    <input type="text" name="account_number" class="form-control" id="accountNumber">
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{
                                    trans('lang.other_information')}}</label>
                                <div class="col-12">
                                    <input type="text" name="other_information" class="form-control" id="otherDetails">
                                </div>
                            </div>

                        </div>

                    </div>

                </form>

                <div class="modal-footer">

                    {{--
                    <button type="button" class="btn btn-primary" data-dismiss="modal"
                        aria-label="Close">{{trans('close')}}</a>
                    </button>
                    --}}
                    <a class="btn btn-primary acceptBtn" href="javascript:void(0)">{{trans("lang.accept")}}</a>
                    <a name="reject-request" class="btn btn-primary rejectBtn" href="javascript:void(0)"
                        data-toggle="modal" data-target="#reasonModal">{{trans("lang.reject")}}</a>

                </div>
            </div>
        </div>

    </div>

</div>
<div class="modal fade" id="reasonModal" tabindex="-1" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered location_modal">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title locationModalTitle">{{trans('lang.reason')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <form class="">

                    <div class="form-row">

                        <div class="form-group row">

                            <div class="form-group row width-100">

                                <div class="col-12">
                                    <label class="col-12 control-label">{{trans('lang.reason_for_rejection')}}</label>
                                    <input type="text" name="reason" class="form-control" id="reason">
                                    <input type="text" name="request_id" class="form-control" id="request_id" hidden>
                                    <input type="text" name="distributor_id" class="form-control" id="distributor_id"
                                        hidden>
                                    <input type="text" name="price_add" class="form-control" id="price_add" hidden>
                                </div>
                            </div>

                        </div>

                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary save_reason">{{trans('submit')}}</a>
                        </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"
                            aria-label="Close">{{trans('close')}}</a>
                        </button>


                    </div>
                </form>
            </div>
        </div>

    </div>

</div>


@endsection


@section('scripts')

<script>
    var distributorCheckConfirm = "{{ trans('lang.distributor_check_confirm') }}";

    var database = firebase.firestore();

    var refData = database.collection('withdrawal_history');

    var ref = refData.orderBy('paidDate', 'desc');
    var append_list = '';
    var currentCurrency = '';

    var symbolAtRight = false;
    var refCurrency = database.collection('settings').doc('currency');

    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.data();
        currentCurrency = currencyData.currencySymbol;
        decimal_degits = currencyData.decimalDigit;

        if (currencyData.symbolAtRight) {
            symbolAtRight = true;
        }
    });


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

            $('#payoutTable').DataTable({
                order: [],
                columnDefs: [
                    {
                        targets: 3,
                        type: 'date',
                        render: function (data) {
                            return data;
                        }
                    },
                    { orderable: false, targets: [4, 6] },

                ],
                order: [['3', 'desc']],
                "language": {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}"
                }
            });


            jQuery("#overlay").hide();

        });


    });

    $(document.body).on('click', '.redirecttopage', function () {
        var url = $(this).attr('data-url');
        window.location.href = url;
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

    function getListData(val) {
        var html = '';
        html = html + '<tr>';


        const payoutUser = payoutUserfunction(val.userId, val.id, val.amount);

        html = html + '<td class="distributor_' + val.id + '"></td>';

        if (symbolAtRight) {

            html = html + '<td>' + parseFloat(val.amount).toFixed(decimal_degits) + '' + currentCurrency + '</td>';

        } else {

            html = html + '<td>' + currentCurrency + '' + parseFloat(val.amount).toFixed(decimal_degits) + '</td>';

        }


        var date = val.paidDate.toDate().toDateString();

        var time = val.paidDate.toDate().toLocaleTimeString('en-US');

        html = html + '<td>' + val.note + '</td>';

        html = html + '<td class="dt-time">' + date + ' ' + time + '</td>';

        if (val.paymentStatus) {

            if (val.paymentStatus == "approved") {
                html = html + '<td><span  class="badge badge-success py-2 px-3">Approved</span></td>';
            } else if (val.paymentStatus == "pending") {
                html = html + '<td><span class="badge badge-warning py-2 px-3">Pending</span></td>';
            } else if (val.paymentStatus == "rejected") {
                html = html + '<td><span class="badge badge-danger py-2 px-3">Rejected</span></td>';
            }

        } else {
            html = html + '<td></td>';

        }

        if (val.adminNote) {
            html = html + '<td>' + val.adminNote + '</td>';

        } else {
            html = html + '<td></td>';
        }

        if (val.paymentStatus && val.paymentStatus == "pending") {
            html += '<td class="action-btn"><div class="actionBtn_' + val.id + '"></div>';
            html += '</td>';
        } else {
            html = html + '<td></td>';
        }

        html = html + '</tr>';
        return html;
    }

    async function getDistributorBankDetails() {
        var distributorId = $('#distributorId').val();

        await database.collection('zone').where("id", "==", distributorId).get().then(async function (snapshotss) {

            if (snapshotss.docs[0]) {
                var user_data = snapshotss.docs[0].data();
                if (user_data) {
                    user_data = user_data.bankDetails;
                    $('#bankName').val(user_data.bankName);
                    $('#branchName').val(user_data.branchName);
                    $('#holderName').val(user_data.holderName);
                    $('#accountNumber').val(user_data.accountNumber);
                    $('#otherDetails').val(user_data.otherDetails);

                }

            }
        });

    }

    $(document).on("click", "a[name='distributor_view']", function (e) {
        $('#bankName').val("");
        $('#branchName').val("");
        $('#holderName').val("");
        $('#accountNumber').val("");
        $('#otherDetails').val("");

        var id = this.id;
        var auth = $(this).attr('data-auth');
        var price = $(this).attr('data-price');
        $('#distributorId').val(auth);
        $('.acceptBtn').attr('data-auth', auth);
        $('.acceptBtn').attr('data-price', price);
        $('.acceptBtn').attr('id', id);
        $('.rejectBtn').attr('data-auth', auth);
        $('.rejectBtn').attr('data-price', price);
        $('.rejectBtn').attr('id', id);
        getDistributorBankDetails();

    });

    async function payoutUserfunction(userId, id, amount) {

        var payoutUser = '';

        await database.collection('zone').where("id", "==", userId).get().then(async function (snapshotss) {


            if (snapshotss.docs[0]) {

                var data = snapshotss.docs[0].data();

                payoutUser = data.firstName + " " + data.lastName;

                var route = '{{route("distributors.view",":id")}}';

                route = route.replace(':id', userId);

                jQuery(".distributor_" + id).html('<a href="' + route + '" class="redirecttopage">' + payoutUser + '</a>').addClass('redirecttopage');
                jQuery(".actionBtn_" + id).html('<a id="' + id + '" data-price="' + amount + '" name="distributor_view" data-auth="' + userId + '" href="javascript:void(0)" data-toggle="modal" data-target="#bankdetailsModal"><i class="fa fa-eye"></i></a><a id="' + id + '" name="distributor_check" data-price="' + amount + '" data-auth="' + userId + '" href="javascript:void(0)" data-toggle="modal" data-target="#bankdetailsModal"><i class="fa fa-check" style="color:green"></i></a>' +'<a id="' + id + '" data-price="' + amount + '" name="reject-request" data-auth="' + userId + '" href="javascript:void(0)" data-toggle="modal" data-target="#reasonModal"><i class="fa fa-close" ></i></a>');

            } else {

                jQuery(".distributor_" + id).html('{{trans("lang.unknown_user")}}');
            }

        });

        return payoutUser;

    }


    $(document).on("click", "a[name='distributor_check']", function (e) {
        var id = this.id;
        var fullname = $(this).attr('data-name');
        var auth = $(this).attr('data-auth');
        var price = $(this).attr('data-price');
        $('.acceptBtn').attr('data-auth', auth);
        $('.acceptBtn').attr('data-price', price);
        $('.acceptBtn').attr('id', id);
        $('.rejectBtn').attr('data-auth', auth);
        $('.rejectBtn').attr('data-price', price);
        $('.rejectBtn').attr('id', id);
        $('#distributorId').val(auth);


        getDistributorBankDetails();


    });

    $(document).on("click", "a[name='reject-request']", function (e) {
        $('#bankdetailsModal').modal('hide');
        var id = this.id;
        var auth = $(this).attr('data-auth');
        var priceadd = $(this).attr('data-price');
        $('#request_id').val(id);
        $('#distributor_id').val(auth);
        $('#price_add').val(priceadd);
    })
    $('.save_reason').click(function () {
        
        var id = $('#request_id').val();
        var auth = $('#distributor_id').val();
        var priceadd = $('#price_add').val();
        var reason = $('#reason').val();
        console.log(id);
        console.log(auth);
        console.log(priceadd);
        jQuery("#overlay").show().html("{{trans('lang.saving')}}");
        database.collection('zone').where("id", "==", auth).get().then(function (snapshots) {
            if (snapshots.docs.length) {
                var distributor = snapshots.docs[0].data();
                var walletAmount = 0;
                if (isNaN(distributor.walletAmount) || distributor.walletAmount == undefined) {
                    walletAmount = 0;
                } else {
                    walletAmount = distributor.walletAmount;
                }

                price = parseFloat(walletAmount) + parseFloat(priceadd);
                price = price.toString();
                database.collection('withdrawal_history').doc(id).update({
                    'paymentStatus': 'rejected',
                    'adminNote': reason
                }).then(function (result) {
                    database.collection('zone').doc(distributor.id).update({ 'walletAmount': price }).then(function (result) {
                        logFirebaseActivity('#' + id + ' {{trans("lang.withdrawal")}} {{trans("lang.rejected")}}');
                        window.location.href = '{{ url()->current() }}';
                    });
                });

            } else {
                alert('distributor not found.');
            }
        });
    });

    $('.acceptBtn').click(function () {

        var id = this.id;
        var auth = $(this).attr('data-auth');
        jQuery("#overlay").show().html("{{trans('lang.saving')}}");
        database.collection('withdrawal_history').doc(id).update({ 'paymentStatus': 'approved' }).then(function (result) {
            logFirebaseActivity('#' + id + ' {{trans("lang.withdrawal")}} {{trans("lang.accepted")}}');
            window.location.href = '{{ url()->current() }}';
        });
    });


</script>


@endsection