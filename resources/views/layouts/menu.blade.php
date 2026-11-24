@php
$user = Auth::user();
$role_has_permission = App\Models\Permission::where('role_id', $user->role_id)->pluck('permission')->toArray();

@endphp
<nav class="sidebar-nav non-printable">

    <ul id="sidebarnav">

        <li>
            <a class="waves-effect waves-dark" href="{!! url('dashboard') !!}" aria-expanded="false">

                <i class="mdi mdi-home"></i>

                <span class="hide-menu">{{ trans('lang.dashboard') }}</span>

            </a>
        </li>
        @if(
            in_array('fuel-purchase', $role_has_permission) ||
            in_array('fuel-receive', $role_has_permission) ||
            in_array('fuel-tax', $role_has_permission) || 
            in_array('fuel-request', $role_has_permission)
        )
            <li class="nav-subtitle">
                <span class="nav-subtitle-span">{{ trans('lang.fuel_management') }}</span>
            </li>
        @endif
        @if(in_array('fuel-purchase', $role_has_permission) || in_array('fuel-receive', $role_has_permission))
            <li>
                <a href="{!! url('fuel-purchases') !!}" class="waves-effect waves-dark">
                    <i class="mdi mdi-gas-station"></i>
                    <span class="hide-menu">{{trans('lang.fuel_purchase')}}</span>
                </a>
            </li>
        @endif
        @if(in_array('fuel-receive', $role_has_permission) || in_array('fuel-tax', $role_has_permission))
            <li>
                <a href="{!! url('fuel-receivings') !!}" class="waves-effect waves-dark">
                    <i class="mdi mdi-truck-delivery"></i>
                    <span class="hide-menu">{{trans('lang.fuel_receive')}}</span>
                </a>
            </li>
        @endif
        @if(in_array('fuel-tax', $role_has_permission))
            <li>
                <a href="{!! url('fuel/tax_verifications') !!}" class="waves-effect waves-dark">
                    <i class="mdi mdi-file-document"></i>
                    <span class="hide-menu">{{trans('lang.fuel_tax_verification')}}</span>
                </a>
            </li>
        @endif
        @if(in_array('fuel-request', $role_has_permission))
            <li>
                <a href="{!! url('fuel-request') !!}" class="waves-effect waves-dark">
                    <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                    <span class="hide-menu">{{trans('lang.fuel_request')}}</span>
                </a>
            </li>
        @endif
        @if(
    in_array('admins', $role_has_permission) || in_array('roles', $role_has_permission) || in_array('customers', $role_has_permission) || in_array('approve_customers', $role_has_permission) ||
    in_array('pending_customers', $role_has_permission) || in_array('owners', $role_has_permission) || in_array('approve_owners', $role_has_permission) ||
    in_array('pending_owners', $role_has_permission) || in_array('employee', $role_has_permission) || in_array('report', $role_has_permission)
)
                                <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.account_management') }}</span></li>
        @endif          @if(in_array('admins', $role_has_permission) || in_array('roles', $role_has_permission))

                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">

                            <i class="mdi mdi-lock-outline"></i>

                            <span class="hide-menu">{{trans('lang.access_control')}}</span>

                        </a>

                        <ul aria-expanded="false" class="collapse">
                            @if(in_array('roles', $role_has_permission))
                                <li><a href="{!! url('role') !!}">{{trans('lang.role_plural')}}</a></li>
                            @endif

                            @if(in_array('admins', $role_has_permission))
                                <li><a href="{!! url('admin-users') !!}">{{trans('lang.admin_plural')}}</a></li>
                            @endif

                            @if(in_array('admins', $role_has_permission))
                                <li><a href="{!! url('admin-users/activity') !!}">{{trans('lang.user_activity')}}</a></li>
                            @endif
                        </ul>

                    </li>

        @endif
        @if(
                in_array('assignFuelStation', $role_has_permission) || in_array('customers', $role_has_permission) || in_array('approve_customers', $role_has_permission) ||
                in_array('pending_customers', $role_has_permission)
            )
                                <li>
                                    <a class="has-arrow waves-effect waves-dark customer_menu" href="#" aria-expanded="false">

                                        <i class="mdi mdi-account-multiple"></i>

                                        <span class="hide-menu">{{trans('lang.customers')}}</span>

                                    </a>

                                    <ul aria-expanded="false" class="collapse customer_sub_menu">
                                        @if(in_array('customers', $role_has_permission))
                                            <li class="all_customer_menu"><a href="{!! url('customers') !!}">{{trans('lang.all_customers')}}</a>
                                            </li>
                                        @endif

                                        @if(in_array('approve_customers', $role_has_permission))
                                            <li class="approve_customer_menu"><a
                                                    href="{!! url('customers/approved') !!}">{{trans('lang.approved_customers')}}</a></li>
                                        @endif
                                        @if(in_array('pending_customers', $role_has_permission))
                                            <li class="pending_customer_menu"><a
                                                    href="{!! url('customers/pending') !!}">{{trans('lang.approval_pending_customers')}}</a></li>
                                        @endif
                                        <li class="deleted_customer_menu">
                                            <a href="{!! url('customers/deleted') !!}">{{trans('lang.deleted_customers')}}</a>
                                        </li>

                                    </ul>

                                </li>
                                @if(in_array('assignFuelStation', $role_has_permission))
                                 <li>
                                    <a href="{!! url('customers/assign-petrol-pump') !!}" class="waves-effect waves-dark">
                                            <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                                            <span class="hide-menu">{{trans('lang.assign')}} {{trans('lang.fuel_station')}}</span>
                                    </a>
                                </li>
                                @endif
        @endif


        @if(
    in_array('owners', $role_has_permission) || in_array('approve_owners', $role_has_permission) ||
    in_array('pending_owners', $role_has_permission)
)
                    <li>
                        <a class="has-arrow waves-effect waves-dark owner_menu" href="#" aria-expanded="false">

                            <i class="mdi mdi-account-multiple"></i>

                            <span class="hide-menu">{{trans('lang.owners')}}</span>

                        </a>

                        <ul aria-expanded="false" class="collapse owners_sub_menu">
                            @if(in_array('owners', $role_has_permission))

                                <li class="all_owner_menu"><a href="{!! url('owners') !!}">{{trans('lang.all_owner')}}</a></li>
                            @endif
                            @if(in_array('approve_owners', $role_has_permission))

                                <li class="approve_owner_menu"><a
                                        href="{!! url('owners/approved') !!}">{{trans('lang.approved_owner')}}</a></li>
                            @endif
                            @if(in_array('pending_owners', $role_has_permission))

                                <li class="pending_owner_menu"><a
                                        href="{!! url('owners/pending') !!}">{{trans('lang.approval_pending_owner')}}</a></li>
                            @endif
                             <li class="deleted_owner_menu">
                                            <a href="{!! url('owners/deleted') !!}">{{trans('lang.deleted_owner')}}</a>
                            </li>
                        </ul>

                    </li>
        @endif
        
        @if(in_array('employee', $role_has_permission))
            <li>
                <a class="waves-effect waves-dark employee_menu" href="{!! url('employee') !!}" aria-expanded="false">
                    <i class="mdi mdi-account-multiple"></i>
                    <span class="hide-menu">{{ trans('lang.employee') }}</span>
                </a>
            </li>
        @endif
        @if(in_array('states', $role_has_permission))
        <li>
                <a class="waves-effect waves-dark city_state" href="{!! url('states') !!}" aria-expanded="false">
                    <i class="mdi mdi-city"></i>
                    <span class="hide-menu">{{ trans('lang.states') }}</span>
                </a>
            </li>
        @endif
        @if(in_array('report', $role_has_permission) || in_array('user-report', $role_has_permission) ||
    in_array('owner-report', $role_has_permission)
)
                    <li>
                        <a class="has-arrow waves-effect waves-dark owner_menu" href="#" aria-expanded="false">

                            <i class="mdi mdi-file-document"></i>

                            <span class="hide-menu">{{trans('lang.report')}}</span>

                        </a>

                        <ul aria-expanded="false" class="collapse report_sub_menu">
                        @if(in_array('report', $role_has_permission))
                        <li>
                            <a  href="{!! url('transaction-report') !!}">{{ trans('lang.fuel_transaction_report') }}</a>
                        </li>
                        @endif
                        <li>
                            <a  href="{!! url('owner-transaction-report') !!}">{{ trans('lang.owner_transaction_report') }}</a>
                        </li>
                        <li>
                            <a  href="{!! url('customer-report') !!}">{{ trans('lang.customer_report') }}</a>
                        </li>
                        <li>
                            <a  href="{!! url('owner-report') !!}">{{ trans('lang.owner_report') }}</a>
                        </li>
                        </ul>

                    </li>
        @endif

        @if(
    in_array('vehicle_days', $role_has_permission) || in_array('cms', $role_has_permission) || in_array('vehicle-type', $role_has_permission)
    || in_array('fuel-transaction', $role_has_permission) || in_array('onboard', $role_has_permission)
)

                <li class="nav-subtitle"><span class="nav-subtitle-span">{{trans('lang.app_management')}}</span></li>
        @endif

        @if(in_array('fuel-transaction', $role_has_permission))

          <li>
                <a class="waves-effect waves-dark fuel_transaction_menu" href="{!! url('fuel-transaction') !!}"
                    aria-expanded="false">
                    <i class="mdi mdi-gas-station"></i>
                    <span class="hide-menu">{{ trans('lang.fuel_transaction') }}</span>
                </a>
            </li>
            @endif
        @if(in_array('vehicle_days', $role_has_permission))

            <li>
                <a class="waves-effect waves-dark vehicle_days_menu" href="{!! url('vehicle-days') !!}"
                    aria-expanded="false">
                    <i class="mdi mdi-calendar"></i>
                    <span class="hide-menu">{{ trans('lang.assign_vehicle_days') }}</span>
                </a>
            </li>
        @endif

        {{--@if(in_array('cms', $role_has_permission))
            <li>
                <a class="waves-effect waves-dark cms_menu" href="{!! url('cms') !!}" aria-expanded="false">
                    <i class="mdi mdi-note"></i>
                    <span class="hide-menu">{{ trans('lang.cms_plural') }}</span>
                </a>
            </li>
        @endif
        --}}
        
        @if(in_array('onboard', $role_has_permission))

        <li>
                <a class="waves-effect waves-dark cms_menu" href="{!! url('on-board') !!}" aria-expanded="false">
                    <i class="mdi mdi-monitor"></i>
                    <span class="hide-menu">{{ trans('lang.on_board_plural') }}</span>
                </a>
        </li>
       @endif

        @if(in_array('vehicle-type', $role_has_permission))
            <li>
                <a class="waves-effect waves-dark vehicle_type_menu" href="{!! url('vehicle-type') !!}"
                    aria-expanded="false">
                    <i class="mdi mdi-car"></i>
                    <span class="hide-menu">{{ trans('lang.vehicle_type') }}</span>
                </a>
            </li>
        @endif

        @if(in_array('privacy', $role_has_permission) || in_array('terms', $role_has_permission) || in_array('sms', $role_has_permission) || in_array('language', $role_has_permission))
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{trans('lang.setting_management')}}</span></li>
            <li>
                <a class="has-arrow waves-effect waves-dark setting_menu" href="#" aria-expanded="false">

                    <i class="mdi mdi-settings"></i>

                    <span class="hide-menu">{{trans('lang.app_setting')}}</span>

                </a>

                <ul aria-expanded="false" class="collapse setting_sub_menu">
                    @if(in_array('fuel-quota', $role_has_permission))
                        <li><a href="{!! url('settings/fuel-quota') !!}">{{trans('lang.fuel_quota_settings')}}</a></li>
                    @endif
                    @if(in_array('global-setting', $role_has_permission))
                        <li><a href="{!! url('settings/global-setting') !!}">{{trans('lang.app_setting_global')}}</a></li>
                    @endif
                    @if(in_array('privacy', $role_has_permission))
                        <li><a href="{!! url('settings/privacyPolicy') !!}">{{trans('lang.privacy_policy')}}</a></li>
                    @endif
                     @if(in_array('contactUs', $role_has_permission))
                        <li><a href="{!! url('settings/contactUs') !!}">{{trans('lang.contact_us')}}</a></li>
                    @endif
                    @if(in_array('terms', $role_has_permission))
                        <li><a href="{!! url('settings/termsAndConditions') !!}">{{trans('lang.terms_and_conditions')}}</a></li>
                    @endif
                    @if(in_array('about-us', $role_has_permission))
                        <li><a href="{!! url('settings/aboutus') !!}">{{trans('lang.about_us')}}</a></li>
                    @endif
                    @if(in_array('language', $role_has_permission))
                         <li><a  href="{!! url('settings/languages') !!}">{{trans('lang.languages')}}</a></li>
                    @endif

                </ul>

            </li>
        @endif



    </ul>

    <p class="web_version"></p>

</nav>