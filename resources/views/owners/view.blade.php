@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor restaurantTitle">{{trans('lang.client_detail')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('client') !!}">{{trans('lang.client')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.client_detail')}}</li>
            </ol>
        </div>

    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-6">
                                <div class="card card-block p-card">
                                    <div class="profile-box">
                                        <div class="profile-card rounded">
                                            <img src="https://goride.siswebapp.com/images/default_user.png"
                                                alt="profile-bg"
                                                class="avatar-100 d-block mx-auto img-fluid mb-3  avatar-rounded user-image">
                                            <h3 class="font-600 text-white text-center user-name"></h3>

                                            <h3 class="font-600 text-white text-center mb-5 user-wallet"></h3>
                                            
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
                                            <h3>{{trans('lang.buisness_detail')}}</h3>
                                            <div class="table-responsive user-list-table">
                                                <table class="table mb-0">
                                                    <tbody id="bank_information">
                                                        <tr>
                                                            <td class="py-2 px-0">
                                                                <span
                                                                    class="font-weight-bold w-100">{{trans("lang.buisness_name")}}:</span>
                                                            </td>
                                                            <td class="py-2 px-0 buisness_name"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-2 px-0">
                                                                <span
                                                                    class="font-weight-bold w-100">{{trans("lang.buisness_category")}}:</span>
                                                            </td>
                                                            <td class="py-2 px-0 buisness_category"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-2 px-0">
                                                                <span
                                                                    class="font-weight-bold w-100">{{trans("lang.address")}}:</span>
                                                            </td>
                                                            <td class="py-2 px-0 address"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-2 px-0">
                                                                <span
                                                                    class="font-weight-bold w-100">{{trans("lang.distributor_name")}}:</span>
                                                            </td>
                                                            <td class="py-2 px-0 distributor_name"></td>
                                                        </tr>

                                                        <tr>
                                                            <td class="py-2 px-0">
                                                                <a href="javascript:void(0)"><span
                                                                        class="font-weight-bold w-100"
                                                                        data-toggle="modal"
                                                                        data-target="#exampleModal">{{trans('lang.see_business_documents')}}
                                                                        </span></a>
                                                            </td>
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
                  
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn doc-footer">
                <a href="{!! route('client') !!}" class="btn btn-default"><i
                        class="fa fa-undo cancel-btn"></i>{{trans('lang.cancel')}}</a>
            </div>

        </div>

    </div>

</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner" id="carousel-inner">

                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
                <!-- <img class="img-responsive" src="" /> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')

<script>
    var id = "{{$id}}";
    var database = firebase.firestore();
    var ref = database.collection('clients').where('id', '==', id);
    var zone = database.collection("zone").where('id', '==', id);
    var refCurrency = database.collection('settings').doc("currency");
    var walletRef = database.collection('wallet_transaction').where('userType', '==', 'client').where("userId", "==", id).orderBy('createdAt', 'desc');
    // var refPayoutRequest = database.collection('withdrawal_history').where('userId', '==', id).orderBy('createdDate', 'desc');

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
    var decimal_degits = 0;
    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.data();
        currentCurrency = currencyData.currencySymbol;
        decimal_degits = currencyData.decimalDigit;
        if (currencyData.symbolAtRight) {
            symbolAtRight = true;
        }
    });
    refPlan = database.collection('plans').where('enable', '==', true);
    refPlan.get().then(async function (snapshots) {
        var html = '';
         html += '<div id="wallet_error" class="mb-2"></div>';
        snapshots.docs.forEach(async (listval) => {
            var val = listval.data();
            html += '<div class="wallet-checklist">';
            html += '<div class="wallet-checklist-row">';
            html += '<div class="wallet-checklist-box d-flex">';
            html += '<div class="wallet-checklist-content">';
            html += '<h4>' + val.planName + '</h4>';
            if (symbolAtRight) {
                amount = parseFloat(val.planPrice).toFixed(decimal_degits) + currentCurrency;
                planPoint = parseFloat(val.planPoint).toFixed(decimal_degits) + currentCurrency;
            } else {
                amount = currentCurrency + parseFloat(val.planPrice).toFixed(decimal_degits);
                planPoint = currentCurrency + parseFloat(val.planPoint).toFixed(decimal_degits)
            }

            html += '<p>' + amount + ' to ' + planPoint + ' amount credit</p>';
            html += '</div>';
            html += '<div class="wallet-checklist-right">';
            html += '<div class="radio-box">';
            html += '<input type="radio" class="is_open" name="plan_id" id="plan_' + val.id + '" value="' + val.id + '" >';
            html += '<label class="control-label" for="plan_' + val.id + '"></label>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';

        });
       
        $('#append_plan_list').html(html);
    })

    $(document).ready(async function () {

        $('.driver_sub_menu li').each(function () {

            var url = $(this).find('a').attr('href');

            if (url == document.referrer) {
                $(this).find('a').addClass('active');
                $('.driver_menu').addClass('active').attr('aria-expanded', true);

            }
            $('.driver_sub_menu').addClass('in').attr('aria-expanded', true);
        });


    });

    ref.get().then(async function (snapshot) {
        if (snapshot.docs != "") {
            let data = snapshot.docs[0].data();
           
            $(".user-name").text(data.firstName + " " + data.lastName);
            $(".user-email").text(data.email);
            var countryCode='';
            if(data.countryCode!='' && data.countryCode!=null){
                countryCode='+'+data.countryCode;
            }
            $(".user-phone").text(countryCode + data.mobileNumber);
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
            $(".buisness_name").text(data.businessName);
            $(".buisness_category").text(data.businessCategories);
            $(".address").text(data.address);
            var ima = data.businessPhotos;
            if (ima.length === 1) {
                $(".carousel-control-prev").hide();
                $(".carousel-control-next").hide();
            }

            var carouselInner = document.getElementById('carousel-inner');
            // carouselInner.innerHTML = ''; // Clear existing items

            ima.forEach(function (url, index) {
                var carouselItem = document.createElement('div');
                carouselItem.className = 'carousel-item';
                if (index === 0) {
                    carouselItem.classList.add('active');
                }

                var img = document.createElement('img');
                img.className = 'd-block w-100';
                img.src = url;

                carouselItem.appendChild(img);
                carouselInner.appendChild(carouselItem);
            });


            // Call the function to create initial carousel items

            // $(".carousel-item").each(function(index) {
            //      $(this).find("img").attr("src", ima[index]);
            // });

            await getUser(data.distributorId);
            // $(".distributor_name").text();


        }
        var walletAmount = 0;
    });

    $(document).on('click', '.wallet_transactions', function () {
        getWalletTransactions();
    });


    async function getUser(id) {
        var userName = '';
        await database.collection('zone').where('id', '==', id).get().then(async function (snapshots) {

            if (snapshots.docs.length > 0) {
                var user = snapshots.docs[0].data();
                $(".distributor_name").text(user.firstName + " " + user.lastName);
            }

        });
        return userName;
    }

</script>
@endsection