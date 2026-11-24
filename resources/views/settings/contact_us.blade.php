@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.contact_us')}}</h3>
            </div>

            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('lang.contact_us')}}</li>
                </ol>
            </div>
        

        </div>
     <div class="container-fluid">
       <div class="card"> 
         <div class="card-body">

            <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{trans('lang.processing')}}</div>
            <div class="error_top"></div>

            <div class="terms-cond restaurant_payout_create row">
                <div class="restaurant_payout_create-inner">
                    <fieldset>
                        <legend>{{trans('lang.contact_us')}}</legend>

                        <div class="form-group width-100">
                            <textarea class="form-control col-7" name="contact_us" id="contact_us"></textarea>
                        </div>


                    </fieldset>

                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn" >
            <button type="button" class="btn btn-primary  create_user_btn" ><i class="fa fa-save"></i> {{ trans('lang.save')}}</button>
            <a href="{!! route('settings.contactUs') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
        </div>
        </div>

        
       </div>
   </div>
    </div>

@endsection

@section('scripts')
    <script>

        var database = firebase.firestore();
        var photo ="";
        var ref = database.collection('settings').doc('global');
        $(document).ready(function () {
            try {
                jQuery("#overlay").show();
                ref.get().then(async function (snapshots) {
                    var globalSetting = snapshots.data();
                    if (globalSetting == undefined) {
                        database.collection('settings').doc('global').set({ 'contactUs':'' });
                    }
                    if(globalSetting.contactUs)
                    {
                        $('#contact_us').summernote("code", globalSetting.contactUs);
                    }
                });
                jQuery("#overlay").hide();
            } catch (error) {
                jQuery("#overlay").hide();
            }
            $('#contact_us').summernote({
                height: 400,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['forecolor', ['forecolor']],
                    ['backcolor', ['backcolor']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['view', ['codeview']],
                ]
            });

            $(".create_user_btn").click(function(){

                var contact_us =  $('#contact_us').summernote('code');

                if(contact_us == ''){
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.contact_us_error')}}</p>");
                    window.scrollTo(0, 0);
                }else{
                    database.collection('settings').doc('global').update({'contactUs':contact_us}).then(function(result) {
                          logFirebaseActivity('{{trans("lang.contact_us")}} {{trans("lang.updated")}}');
                        window.location.href = '{{ route("settings.contactUs")}}';
                    })
                }
            })
        });

    </script>
@endsection
