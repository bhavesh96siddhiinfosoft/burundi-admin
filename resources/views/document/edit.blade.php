@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.edit_document')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('document.index') !!}">{{trans('lang.document')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.edit_document')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card pb-4">
            <div class="card-body">

                <div id="data-table_processing" class="dataTables_processing panel panel-default"
                    style="display: none;">{{trans('lang.processing')}}</div>

                <div class="row daes-top-sec mb-3">

                </div>

                <div class="error_top"></div>
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{trans('lang.edit_document')}}</legend>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.distributor_comision')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control comision">
                                    <div id="error2" class="err"></div>

                                    <div class="form-text text-muted w-50">
                                        {{ trans("lang.comision_help") }}
                                    </div>
                                </div>
                            </div>
                             <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.price')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control price">
                                    <div id="error2" class="err"></div>

                                    <div class="form-text text-muted w-50">
                                        {{ trans("lang.price_help") }}
                                    </div>
                                </div>
                            </div>

                            <div id="passport_div" style="display:none">
                                <div class="form-group row width-50">
                                    <label
                                        class="col-3 control-label">{{trans('lang.distributor_foriegn_comision')}}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control foreign_comision">
                                        <div id="error2" class="err"></div>

                                        <div class="form-text text-muted w-50">
                                            {{ trans("lang.comision_help") }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.foreign_price')}}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control foreign_price">
                                        <div id="error2" class="err"></div>
                                        <div class="form-text text-muted w-50">
                                            {{ trans("lang.price_help") }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                           


                            <div class="form-group row width-50">

                                <div class="form-check">
                                    <input type="checkbox" class="isActive" id="isActive">
                                    <label class="col-3 control-label" for="isActive">{{ trans('lang.active')
                                        }}</label>
                                </div>
                            </div>

                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary  save_user_btn"><i class="fa fa-save"></i> {{
                    trans('lang.save')}}</button>
                <a href="{!! route('document.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
                    trans('lang.cancel')}}</a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var id = "<?php echo $id; ?>";
    var database = firebase.firestore();
    var ref = database.collection('documents').where('id', '==', id);
    var placeholderImage = "{{ asset('/images/default_user.png') }}";
    $('.phone_code').select2();

    $(document).ready(function () {

        $('.document_menu').addClass('active');
        jQuery("#overlay").show();
        ref.get().then(async function (snapshots) {
            var documents = snapshots.docs[0].data();
           
            $(".comision").val(documents.distributorCommission);
            $(".price").val(documents.price);

            if (documents.enable) {
                $(".isActive").prop('checked', true);
            }
            if(documents.title=='Passport'){
                $('#passport_div').show();
                $('.foreign_comision').val(documents.distributorForeignCommission);
                $('.foreign_price').val(documents.foreignPrice);
            }
            jQuery("#overlay").hide();
        })
    });
    $(".save_user_btn").click(function () {

        var comision = $(".comision").val();
        var price = $(".price").val();
        var active = $(".isActive").is(":checked");
        var foreign_comision=$('.foreign_comision').val();
        var foreign_price=$('.foreign_price').val();

        if (comision == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.comision_help')}}</p>");
            window.scrollTo(0, 0);
        } else if (price == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.price_help')}}</p>");
            window.scrollTo(0, 0);
        } else {
            jQuery("#overlay").show();
            database.collection('documents').doc(id).update({
                'distributorCommission': comision,
                'enable': active,
                'price': price,
                'distributorForeignCommission':foreign_comision,
                'foreignPrice':foreign_price
            }).then(function (result) {
                jQuery("#overlay").hide();
                logFirebaseActivity('#' + id + ' document updated');
                window.location.href = '{{ route("document.index")}}';
            });
        }
    })
</script>
@endsection