@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor restaurantTitle">{{trans('lang.distributor_detail')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('distributors') !!}">{{trans('lang.distributors')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.distributor_detail')}}</li>
            </ol>
        </div>

    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-12 col-md-6">
                                <div class="card card-block p-card">
                                    <div class="profile-box">
                                        <div class="profile-card rounded">
                                            <img src="https://goride.siswebapp.com/images/default_user.png" alt="profile-bg" class="avatar-100 d-block mx-auto img-fluid mb-3  avatar-rounded user-image">
                                            <h3 class="font-600 text-white text-center user-name"></h3>

                                            <h3 class="font-600 text-white text-center mb-5 user-wallet"></h3>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#addWalletModal" class="ml-3 mb-2 text-white add-wallate btn btn-sm btn-success"><i class="fa fa-plus"></i>{{trans("lang.add_wallet_amount")}}</a>

                                        </div>
                                        <div class="pro-content rounded">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="p-icon mr-3">
                                                    <i class="fa fa-envelope"></i>
                                                </div>
                                                <p class="mb-0 eml user-email"></p>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="p-icon mr-3">
                                                    <i class="fa fa-phone"></i>
                                                </div>
                                                <p class="mb-0 user-phone"></p>
                                            </div>
                                        </div>


                                        <div class="personal-detail mb-0">
                                            
                                            <div class="table-responsive user-list-table">
                                                <table class="table mb-0">
                                                    <tbody id="distributor_information">
                                                        <tr>
                                                            <td class="py-2 px-0">
                                                                <span class="font-weight-bold w-100">{{trans("lang.distributor_code")}}:</span>
                                                            </td>
                                                            <td class="py-2 px-0 "><span class="num-plat distributor_code"></span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="personal-detail mb-0">
                                            <h3>{{trans('lang.bankdetails')}}</h3>
                                            <div class="table-responsive user-list-table">
                                                <table class="table mb-0">
                                                    <tbody id="bank_information">
                                                        <tr>
                                                            <td class="py-2 px-0">
                                                                <span class="font-weight-bold w-100">{{trans("lang.bank_name")}}:</span>
                                                            </td>
                                                            <td class="py-2 px-0 bank_name"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-2 px-0">
                                                                <span class="font-weight-bold w-100">{{trans("lang.branch_name")}}:</span>
                                                            </td>
                                                            <td class="py-2 px-0 branch_name"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-2 px-0">
                                                                <span class="font-weight-bold w-100">{{trans("lang.holder_name")}}:</span>
                                                            </td>
                                                            <td class="py-2 px-0 holder_name"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-2 px-0">
                                                                <span class="font-weight-bold w-100">{{trans("lang.account_number")}}:</span>
                                                            </td>
                                                            <td class="py-2 px-0"><span class="num-plat account_number"></span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-2 px-0">
                                                                <span class="font-weight-bold w-100">{{trans("lang.other_information")}}:</span>
                                                            </td>
                                                            <td class="py-2 px-0 other_info"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card card-block card-stretch">
                                    <div class="card-header bg-white">
                                        <ul class="nav nav-pills mb-3" role="tablist">


                                            <li class="nav-item">
                                                <a class="nav-link wallet_transactions active" data-toggle="pill" href="#wallet_transactions" role="tab">{{trans('lang.wallet_transactions')}}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link client_info" data-toggle="pill" href="#client_info" role="tab">{{trans('lang.client_info')}}</a>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="card-body">
                                        <div class="tab-content">

                                            <div class="tab-pane active" id="wallet_transactions" role="tabpanel">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-valign-middle" id="transactionListTable">
                                                        <thead class="table-color-heading">
                                                            <tr class="text-secondary">
                                                                <th>{{trans('lang.id')}}</th>
                                                                <!-- <th>{{trans('lang.user_name')}}</th> -->
                                                                <th>{{trans('lang.total_amount')}}</th>
                                                                <th>{{trans('lang.title')}}</th>
                                                                <th>{{trans('lang.date')}}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="wallet_transactions_rows"></tbody>
                                                    </table>
                                                </div>

                                            </div>


                                            <div class="tab-pane" id="client_info" role="tabpanel">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-valign-middle" id="clientListTable">
                                                        <thead class="table-color-heading">
                                                            <tr class="text-secondary">
                                                                
                                                                <!-- <th>{{trans('lang.user_name')}}</th> -->
                                                                <th>{{trans('lang.image')}}</th>
                                                                <th>{{trans('lang.name')}}</th>
                                                                <th>{{trans('lang.email')}}</th>
                                                                <th>{{trans('lang.status')}}</th>
                                                                <th>{{trans('lang.actions')}}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="client_info_rows"></tbody>
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
            </div>
            <div class="form-group col-12 text-center btm-btn doc-footer">
                <a href="{!! route('distributors') !!}" class="btn btn-default"><i class="fa fa-undo cancel-btn"></i>{{trans('lang.cancel')}}</a>
            </div>

        </div>

    </div>

</div>
<div class="modal fade" id="addWalletModal" tabindex="-1" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered location_modal">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title locationModalTitle">{{trans('lang.add_wallet_amount')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <form class="">

                    <div class="form-row">

                        <div class="form-group row">

                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{
                                    trans('lang.amount')}}</label>
                                <div class="col-12">
                                    <input type="number" name="amount" class="form-control" id="amount">
                                    <div id="wallet_error" style="color:red"></div>
                                </div>
                            </div>

                            <div class="form-group row width-100">

                                <div id="user_account_not_found_error" class="align-items-center" style="color:red"></div>
                            </div>


                        </div>

                    </div>

                </form>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="add-wallet-btn">{{trans('submit')}}</a>
                    </button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                        {{trans('close')}}</a>
                    </button>

                </div>

            </div>
        </div>

    </div>

</div>

@endsection

@section('scripts')

<script>
    var id = "{{$id}}";
    var database = firebase.firestore();
    var ref = database.collection('wallet_transaction').where('userId','==',id);
    var zone = database.collection("zone").where('id','==',id);
    var refCurrency = database.collection('settings').doc("currency");
    var walletRef = database.collection('wallet_transaction').where("userId", "==", id).orderBy('createdAt', 'desc');
    var clientRef = database.collection('clients').where("distributorId", "==", id);
    
    var refPayoutRequest = database.collection('withdrawal_history').where('userId', '==', id).orderBy('createdDate', 'desc');

    var decimal_degits = 0;
    var symbolAtRight = false;
    var currentCurrency = '';
    var storageRef = firebase.storage().ref('images');
    var storage = firebase.storage();
    var placeholderImage = "{{ asset('/images/default_user.png') }}";
    var notFound = "{{ trans('lang.doc_not_found') }}"
    var back_photo = '';
    var front_photo = '';
    var backFileName = '';
    var frontFileName = '';
    var backFileOld = '';
    var frontFileOld = '';
    var currentCurrency = '';
    var decimal_degits=0;
    var defaultImg = "{{ asset('/images/default_user.png') }}";
    var requestUrl = "{{request()->is('distributors/view/*')}}";
    refCurrency.get().then(async function(snapshots) {
        var currencyData = snapshots.data();
        currentCurrency = currencyData.currencySymbol;
        decimal_degits = currencyData.decimalDigit;
        if (currencyData.symbolAtRight) {
            symbolAtRight = true;
        }
    });
    $(document).ready(async function() {
     
         

        $('.driver_sub_menu li').each(function() {

            var url = $(this).find('a').attr('href');

            if (url == document.referrer) {
                $(this).find('a').addClass('active');
                $('.driver_menu').addClass('active').attr('aria-expanded', true);

            }
            $('.driver_sub_menu').addClass('in').attr('aria-expanded', true);
        });

        getWalletTransactions();
    });


    ref.get().then(async function(snapshot)
    {     
        if(snapshot.docs != ""){
        let data = snapshot.docs[0].data();
        
        }
        var walletAmount = 0;
    });


   
     zone.get().then(async function(snap) {
            var data = snap.docs[0].data();
            console.log(data);
            $(".user-name").text(data.firstName + " " + data.lastName);
            $(".user-email").text(data.email);
            $(".user-phone").text(data.countryCode + data.mobileNumber);
            if (data.hasOwnProperty('walletAmount') && data.walletAmount != null) {
                walletAmount = data.walletAmount;
            }
            if (symbolAtRight) {
                $(".user-wallet").text("{{trans('lang.wallet_Balance')}} : " + parseFloat(walletAmount).toFixed(decimal_degits) + currentCurrency);
            } else {
                $(".user-wallet").text("{{trans('lang.wallet_Balance')}} : " + currentCurrency + parseFloat(walletAmount).toFixed(decimal_degits));
            }

            if (data.profilePic != null && data.profilePic != '') {
                $(".user-image").attr('src', data.profilePic);
            }

            if (data != undefined && (data.bankDetails.bankName != "" || data.bankDetails.branchName != '' || data.bankDetails.accountNumber != "" || data.bankDetails.holderName != "" || data.bankDetails.otherInformation != "")) {
                $(".bank_name").text(data.bankDetails.bankName);
                $(".branch_name").text(data.bankDetails.branchName);
                $(".account_number").text(data.bankDetails.accountNumber);
                $(".holder_name").text(data.bankDetails.holderName);
                $(".other_info").text(data.bankDetails.otherInformation);
            } else {
                $("#bank_information").html('<tr><td><span class="font-weight-bold w-100">{{trans("lang.no_bank_details")}}</span></td></tr>');
            }
            $('.distributor_code').text(data.uniqueCode);
        });
    




    $(document).on('click', '.wallet_transactions', function() {
        getWalletTransactions();
    });
    $(document).on('click', '.client_info', function() {
        getClient();
    });
    function getClient() {

$("#client_info_rows").html('');

jQuery("#overlay").show();

clientRef.get().then(async function(docSnapshot) {

    let html = '';
    html = await buildClientHtml(docSnapshot);

    if (html != '') {
        $("#client_info_rows").html(html);
    }

    var table = $('#clientListTable').DataTable();

    table.destroy();
    //$('#rideListTable').empty();

    table = $('#clientListTable').DataTable({
        order: [],
        columnDefs: [{
            orderable: false,
            targets: 1
        }, {
            targets: 3,
            type: 'date',
        }, ],
        language: {
            "zeroRecords": "{{trans('lang.no_record_found')}}",
            "emptyTable": "{{trans('lang.no_record_found')}}"
        },
        order: [
            [3, "desc"]
        ],

        responsive: true

    });

    jQuery("#overlay").hide();

});

}
async function buildClientHtml(snapshots) {
        var html = '';
        await Promise.all(snapshots.docs.map(async (listval) => {
            var val = listval.data();
            var getData = await getClientListData(val);
            html += getData;
        }));
        return html;
    }

    async function getClientListData(data) {
        let html = '';
        var id = data.id;
        var route1 = '{{route("client.edit",":id")}}';
        route1 = route1.replace(':id', id);

        var view = '{{route("client.view",":id") }}';
        view = view.replace(':id',id);
        html += '<tr>';

        
          
       if (data.profilePic == '' || data.profilePic == null) {
            html = html + '<td><img class="rounded" style="width:50px" src="' + defaultImg + '" alt="image"></td>';
        } else {
            html = html + '<td><img class="rounded" style="width:50px" src="' + data.profilePic + '" alt="image"></td>';
        }
        html += '<td>' + data.firstName+' '+ data.lastName + '</td>';
        html += '<td>' + data.email+ '</td>';
        if (data.enable) {
            html = html + '<td><label class="switch"><input type="checkbox" checked id="' + data.id + '" name="isActive"><span class="slider round"></span></label></td>';
        } else {
            html = html + '<td><label class="switch"><input type="checkbox" id="' + data.id + '" name="isActive"><span class="slider round"></span></label></td>';
        }

        html = html + '<td class="action-btn"><a href="' + view + '"><i class="fa fa-eye"></i></a><a href="' + route1 + '"><i class="fa fa-edit"></i></a>';

html = html + '<a id="' + data.id + '" name="cms-delete" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a>';
html = html + '</td>';




        html += '</tr>';

        return html;
    }

//over client info



    function getWalletTransactions() {

        $("#wallet_transactions_rows").html('');

        jQuery("#overlay").show();

        walletRef.get().then(async function(docSnapshot) {

            let html = '';
            html = await buildWalletTransactionsHtml(docSnapshot);

            if (html != '') {
                $("#wallet_transactions_rows").html(html);
            }

            var table = $('#transactionListTable').DataTable();

            table.destroy();
            //$('#rideListTable').empty();

            table = $('#transactionListTable').DataTable({
                order: [],
                columnDefs: [{
                    orderable: false,
                    targets: 1
                }, {
                    targets: 3,
                    type: 'date',
                }, ],
                language: {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}"
                },
                order: [
                    [3, "desc"]
                ],

                responsive: true

            });

            jQuery("#overlay").hide();

        });

    }

    async function buildWalletTransactionsHtml(snapshots) {
        var html = '';
        await Promise.all(snapshots.docs.map(async (listval) => {
            var val = listval.data();
            var getData = await getWalletTransactionsListData(val);
            html += getData;
        }));
        return html;
    }

    async function getWalletTransactionsListData(data) {
        let html = '';
        html += '<tr>';

        html += '<td>' + data.id.substring(0, 7) + '</td>';
        var amount = parseFloat(data.amount);

        if (symbolAtRight) {
            if (amount.toFixed(decimal_degits) <= 0) {
                amount = Math.abs(amount);
                html += '<td><span style="color:red">(-' + amount.toFixed(decimal_degits) + currentCurrency + ')</span></td>';
            } else {
                html += '<td><span style="color:green">' + amount.toFixed(decimal_degits) + currentCurrency + '</sapn></td>';
            }
        } else {
            if (amount.toFixed(decimal_degits) <= 0) {
                amount = Math.abs(amount);

                html += '<td><span style="color:red">(-' + currentCurrency + amount.toFixed(decimal_degits) + ')</span></td>';
            } else {
                html += '<td><span style="color:green">' + currentCurrency + amount.toFixed(decimal_degits) + '</sapn></td>';
            }
        }
        html += '<td>' + data.title + '</td>';

        if (data.hasOwnProperty("createdAt")) {
            date = data.createdAt.toDate().toDateString();
            time = data.createdAt.toDate().toLocaleTimeString('en-US');
            html = html + '<td class="dt-time"><span class="date">' + date + '</span><span class="time"> ' + time + '</span></td>';
        } else {
            html = html + '<td></td>';
        }




        html += '</tr>';

        return html;
    }


    async function payoutuserfunction(user) {
            var payoutuser = '';
            await database.collection('driver_users').where("id", "==", user).get().then(async function (snapshotss) {
                if (snapshotss.docs[0]) {
                    var user_data = snapshotss.docs[0].data();
                    payoutuser = user_data.fullName;
                }
            });
            return payoutuser;
        }

    $("#add-wallet-btn").click(function() 
    {
        var date = firebase.firestore.FieldValue.serverTimestamp();
        var amount = $('#amount').val();
        if (amount == '') {
            $('#wallet_error').text('{{trans("lang.add_wallet_amount_error")}}');
            return false;
        }

        database.collection('zone').where('id', '==', id).get().then(async function(snapshot) 
        {
            if (snapshot.docs.length > 0) 
            {
                var data = snapshot.docs[0].data();
                var walletAmount = 0;   

                if (data.hasOwnProperty('walletAmount') && !isNaN(data.walletAmount) && data.walletAmount != null) {
                    walletAmount = data.walletAmount;
                }

                var user_id = data.id;

                var newWalletAmount = parseFloat(walletAmount) + parseFloat(amount);
                                        // 'txnId':'<?php echo  uniqid()?>',

                database.collection('zone').doc(data.id).update({
                    'walletAmount': newWalletAmount.toString()
                }).then(function(result) {
                    var tempId = database.collection("tmp").doc().id;
                    var transactionId = (new Date()).getTime();
                    database.collection('wallet_transaction').doc(tempId).set({
                        'amount': amount.toString(),
                        'createdAt': date,
                        'id': tempId,
                        'isCredit':true,
                        'title': 'Wallet top up',
                        'txnId':'<?php echo  uniqid()?>',
                        'userId': user_id,
                        'userType': "distributor",
                    }).then(async function(result) {
                        logFirebaseActivity('#' + data.id + ' '+ newWalletAmount +' wallet amount credited');
                        window.location.reload();
                    });
                })
            }
        });
    })
</script>
@endsection