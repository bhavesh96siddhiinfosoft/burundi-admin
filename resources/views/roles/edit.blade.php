@extends('layouts.app')

@section('content')
<div class="page-wrapper">
       <div class="row page-titles">
              <div class="col-md-5 align-self-center">
                     <h3 class="text-themecolor">{{trans('lang.edit_role')}}</h3>

              </div>

              <div class="col-md-7 align-self-center">
                     <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ url('role') }}">{{trans('lang.role_plural')}}</a>
                            </li>

                            <li class="breadcrumb-item active">{{trans('lang.edit_role')}}</li>

                     </ol>
              </div>

       </div>

       <div class="card-body">

              <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
                     {{trans('lang.processing')}}
              </div>

              <div class="error_top" style="display:none"></div>

              <div class="success_top" style="display:none"></div>

              <form action="{{route('role.update', $id)}}" method="post" id="roleForm">
                     @csrf
                     <div class="row restaurant_payout_create">

                            <div class="restaurant_payout_create-inner">

                                   <fieldset>
                                          <legend>{{trans('lang.role_details')}}</legend>
                                          <div class="form-group row width-100 d-flex">
                                                 <label class="col-3 control-label">{{trans('lang.name')}}</label>
                                                 <div class="col-6">
                                                        <input type="text" class="form-control" id="name" name="name"
                                                               value="{{$roles->role_name}}" {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                 </div>
                                                 <div class="col-6 text-right">
                                                        <label for="permissions"
                                                               class="form-label">{{trans('lang.assign_permissions')}}</label>
                                                        <div class="text-right">
                                                               <input type="checkbox" name="all_permission"
                                                                      id="all_permission" {{$roles->id == 1 ? 'disabled'
       : ''}}>
                                                               <label class="control-label"
                                                                      for="all_permission">{{trans('lang.all_permissions')}}</label>
                                                        </div>
                                                 </div>

                                          </div>

                                          <div class="form-group row width-100">

                                                 <div class="role-table width-100">
                                                        <div class="col-12">
                                                               <table class="table table-striped">
                                                                      <thead>
                                                                             <th>Menu</th>
                                                                             <th>Permission</th>
                                                                      </thead>
                                                                      <tbody>
                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.role_plural')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="role-list"
                                                                                                  value="roles.list"
                                                                                                  name="roles[]"
                                                                                                  class="permission" {{in_array(
       'roles.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="role-list">{{trans('lang.list')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="role-save"
                                                                                                  value="roles.create"
                                                                                                  name="roles[]"
                                                                                                  class="permission" {{in_array(
       'roles.create',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="role-save">{{trans('lang.create')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="role-store"
                                                                                                  value="roles.store"
                                                                                                  name="roles[]"
                                                                                                  class="permission" {{in_array(
       'roles.store',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="role-store">{{trans('lang.store')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="role-edit"
                                                                                                  value="roles.edit"
                                                                                                  name="roles[]"
                                                                                                  class="permission" {{in_array(
       'roles.edit',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="role-edit">{{trans('lang.edit')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="role-update"
                                                                                                  value="roles.update"
                                                                                                  name="roles[]"
                                                                                                  class="permission" {{in_array(
       'roles.update',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="role-update">{{trans('lang.update')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="role-delete"
                                                                                                  value="roles.delete"
                                                                                                  name="roles[]"
                                                                                                  class="permission" {{in_array(
       'roles.delete',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="role-delete">{{trans('lang.delete')}}</label>

                                                                                    </td>
                                                                             </tr>
                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.admin_plural')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="admin-list"
                                                                                                  value="admin.list"
                                                                                                  name="admins[]"
                                                                                                  class="permission" {{in_array(
       'admin.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="admin-list">{{trans('lang.list')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="admin-create"
                                                                                                  value="admin.create"
                                                                                                  name="admins[]"
                                                                                                  class="permission" {{in_array(
       'admin.create',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="admin-create">{{trans('lang.create')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="admin-store"
                                                                                                  value="admin.store"
                                                                                                  name="admins[]"
                                                                                                  class="permission" {{in_array(
       'admin.store',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="admin-store">{{trans('lang.store')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="admin-edit"
                                                                                                  value="admin.edit"
                                                                                                  name="admins[]"
                                                                                                  class="permission" {{in_array(
       'admin.edit',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="admin-edit">{{trans('lang.edit')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="admin-update"
                                                                                                  value="admin.update"
                                                                                                  name="admins[]"
                                                                                                  class="permission" {{in_array(
       'admin.update',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="admin-update">{{trans('lang.update')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="admin-delete"
                                                                                                  value="admin.delete"
                                                                                                  name="admins[]"
                                                                                                  class="permission" {{in_array(
       'admin.delete',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="admin-delete">{{trans('lang.delete')}}</label>

                                                                                    </td>
                                                                             </tr>


                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.customers')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="customer-list"
                                                                                                  value="customer.list"
                                                                                                  name="customers[]"
                                                                                                  class="permission" {{in_array(
       'customer.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="customer-list">{{trans('lang.list')}}</label>


                                                                                           <input type="checkbox"
                                                                                                  id="customer-edit"
                                                                                                  value="customer.edit"
                                                                                                  name="customers[]"
                                                                                                  class="permission" {{in_array(
       'customer.edit',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="customer-edit">{{trans('lang.edit')}}</label>


                                                                                           <input type="checkbox"
                                                                                                  id="customer-delete"
                                                                                                  value="customer.delete"
                                                                                                  name="customers[]"
                                                                                                  class="permission" {{in_array(
       'customer.delete',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="customer-delete">{{trans('lang.delete')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="reset-fuel-quota-customer"
                                                                                                  value="reset.fuel.quota.customer"
                                                                                                  name="reset-fuel-quota[]"
                                                                                                  class="permission"
                                                                                                  {{in_array('reset.fuel.quota.customer', $permissions) ? "checked" : "" }}
                                                                                                  {{$roles->id == 1 ? 'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="reset-fuel-quota-customer">{{trans('lang.reset_fuel_quota')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="reset-fuel-quota-history"
                                                                                                  value="reset.fuel.quota.history"
                                                                                                  name="reset-fuel-quota[]"
                                                                                                  class="permission" {{in_array(
       'reset.fuel.quota.history',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="reset-fuel-quota-history">{{trans('lang.view_fuel_reset_history')}}</label>

                                                                                    </td>
                                                                             </tr>
                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.approved_customers')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="approve-customer-list"
                                                                                                  value="approve.customer.list"
                                                                                                  name="approve_customers[]"
                                                                                                  class="permission" {{in_array(
       'approve.customer.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class="control-label2"
                                                                                                  for="approve-customer-list">{{trans('lang.list')}}</label>

                                                                                    </td>
                                                                             </tr>
                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.approval_pending_customers')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="pending-customer-list"
                                                                                                  value="pending.customer.list"
                                                                                                  name="pending_customers[]"
                                                                                                  class="permission" {{in_array(
       'pending.customer.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="pending-customer-list">{{trans('lang.list')}}</label>


                                                                                    </td>
                                                                             </tr>


                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.owners')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="owner-list"
                                                                                                  value="owner.list"
                                                                                                  name="owners[]"
                                                                                                  class="permission" {{in_array(
       'owner.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="owner-list">{{trans('lang.list')}}</label>


                                                                                           <input type="checkbox"
                                                                                                  id="owner-edit"
                                                                                                  value="owner.edit"
                                                                                                  name="owners[]"
                                                                                                  class="permission" {{in_array(
       'owner.edit',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="owner-edit">{{trans('lang.edit')}}</label>


                                                                                           <input type="checkbox"
                                                                                                  id="owner-delete"
                                                                                                  value="owner.delete"
                                                                                                  name="owners[]"
                                                                                                  class="permission" {{in_array(
       'owner.delete',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="owner-delete">{{trans('lang.delete')}}</label>

                                                                                    </td>
                                                                             </tr>

                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.approved_owner')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="approve-owner-list"
                                                                                                  value="approve.owner.list"
                                                                                                  name="approve_owners[]"
                                                                                                  class="permission" {{in_array(
       'approve.owner.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class="control-label2"
                                                                                                  for="approve-owner-list">{{trans('lang.list')}}</label>

                                                                                    </td>
                                                                             </tr>


                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.approval_pending_owner')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="pending-owner-list"
                                                                                                  value="pending.owner.list"
                                                                                                  name="pending_owners[]"
                                                                                                  class="permission" {{in_array(
       'pending.owner.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="pending-owner-list">{{trans('lang.list')}}</label>


                                                                                    </td>
                                                                             </tr>

                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.employee')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="employee-list"
                                                                                                  value="employee.list"
                                                                                                  name="employee[]"
                                                                                                  class="permission" {{in_array(
       'employee.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="employee-list">{{trans('lang.list')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="employee-edit"
                                                                                                  value="employee.edit"
                                                                                                  name="employee[]"
                                                                                                  class="permission" {{in_array(
       'employee.edit',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="employee-edit">{{trans('lang.edit')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="employee-delete"
                                                                                                  value="employee.delete"
                                                                                                  name="employee[]"
                                                                                                  class="permission" {{in_array(
       'employee.delete',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="employee-delete">{{trans('lang.delete')}}</label>

                                                                                    </td>
                                                                             </tr>
                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.report')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="report"
                                                                                                  value="report"
                                                                                                  name="report[]"
                                                                                                  class="permission" {{in_array(
       'report',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class="control-label2"
                                                                                                  for="report">{{trans('lang.report')}}</label>

                                                                                    </td>
                                                                             </tr>
                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.fuel_transaction')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="fuel-transaction-list"
                                                                                                  value="fuel.transaction.list"
                                                                                                  name="fuel-transaction[]"
                                                                                                  class="permission" {{in_array(
       'fuel.transaction.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="fuel-transaction-list">{{trans('lang.list')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="fuel-transaction-view"
                                                                                                  value="fuel.transaction.view"
                                                                                                  name="fuel-transaction[]"
                                                                                                  class="permission" {{in_array(
       'fuel.transaction.view',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="fuel-transaction-view">{{trans('lang.view')}}</label>

                                                                                    </td>
                                                                             </tr>
                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.assign_vehicle_days')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="vehicle-days-list"
                                                                                                  value="vehicle.days.list"
                                                                                                  name="vehicle_days[]"
                                                                                                  class="permission" {{in_array(
       'vehicle.days.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="vehicle-days-list">{{trans('lang.list')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="vehicle-days-edit"
                                                                                                  value="vehicle.days.edit"
                                                                                                  name="vehicle_days[]"
                                                                                                  class="permission" {{in_array(
       'vehicle.days.edit',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="vehicle-days-edit">{{trans('lang.edit')}}</label>

                                                                                    </td>
                                                                             </tr>

                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.vehicle_type')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="vehicle-type-list"
                                                                                                  value="vehicle.type.list"
                                                                                                  name="vehicle-type[]"
                                                                                                  class="permission" {{in_array(
       'vehicle.type.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="vehicle-type-list">{{trans('lang.list')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="vehicle-type-create"
                                                                                                  value="vehicle.type.create"
                                                                                                  name="vehicle-type[]"
                                                                                                  class="permission" {{in_array(
       'vehicle.type.create',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="vehicle-type-create">{{trans('lang.create')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="vehicle-type-edit"
                                                                                                  value="vehicle.type.edit"
                                                                                                  name="vehicle-type[]"
                                                                                                  class="permission" {{in_array(
       'vehicle.type.edit',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="vehicle-type-edit">{{trans('lang.edit')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="vehicle-type-delete"
                                                                                                  value="vehicle.type.delete"
                                                                                                  name="vehicle-type[]"
                                                                                                  class="permission" {{in_array(
       'vehicle.type.delete',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="vehicle-type-delete">{{trans('lang.delete')}}</label>
                                                                                    </td>
                                                                             </tr>

                                                                             {{-- <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.cms_plural')}}</strong>
                                                                             </td>
                                                                             <td>
                                                                                    <input type="checkbox" id="cms"
                                                                                           value="cms.list" name="cms[]"
                                                                                           class="permission" {{in_array(
       'cms.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                    <label class=" control-label2"
                                                                                           for="cms">{{trans('lang.list')}}</label>
                                                                                    <input type="checkbox"
                                                                                           id="cms-create"
                                                                                           value="cms.create"
                                                                                           name="cms[]"
                                                                                           class="permission" {{in_array(
       'cms.create',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                    <label class=" control-label2"
                                                                                           for="cms-create">{{trans('lang.create')}}</label>
                                                                                    <input type="checkbox" id="cms-edit"
                                                                                           value="cms.edit" name="cms[]"
                                                                                           class="permission" {{in_array(
       'cms.edit',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                    <label class=" control-label2"
                                                                                           for="cms-edit">{{trans('lang.edit')}}</label>

                                                                                    <input type="checkbox"
                                                                                           id="cms-delete"
                                                                                           value="cms.delete"
                                                                                           name="cms[]"
                                                                                           class="permission" {{in_array(
       'cms.delete',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                    <label class=" control-label2"
                                                                                           for="cms-delete">{{trans('lang.delete')}}</label>
                                                                             </td>
                                                                             </tr>
                                                                             --}}
                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.on_board_plural')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="onboard-list"
                                                                                                  value="onboard.list"
                                                                                                  name="onboard[]"
                                                                                                  class="permission" {{in_array(
       'onboard.list',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="onboard-list">{{trans('lang.list')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="onboard-edit"
                                                                                                  value="onboard.edit"
                                                                                                  name="onboard[]"
                                                                                                  class="permission" {{in_array(
       'onboard.edit',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="onboard-edit">{{trans('lang.edit')}}</label>

                                                                                    </td>
                                                                             </tr>
                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.fuel_quota_settings')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="fuel-quota-list"
                                                                                                  value="fuel.quota.list"
                                                                                                  name="fuel-quota[]"
                                                                                                  class="permission" {{in_array('fuel.quota.list', $permissions) ? "checked"
       : "" }} {{$roles->id
       == 1 ? 'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="fuel-quota-list">{{trans('lang.list')}}</label>


                                                                                           <input type="checkbox"
                                                                                                  id="fuel-quota-create"
                                                                                                  value="fuel.quota.create"
                                                                                                  name="fuel-quota[]"
                                                                                                  class="permission" {{in_array(
       'fuel.quota.create',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="fuel-quota-create">{{trans('lang.create')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="fuel-quota-edit"
                                                                                                  value="fuel.quota.edit"
                                                                                                  name="fuel-quota[]"
                                                                                                  class="permission" {{in_array('fuel.quota.edit', $permissions)
       ? "checked" : "" }} {{$roles->id == 1
       ? 'disabled' : ''}}>

                                                                                           <label class=" control-label2"
                                                                                                  for="fuel-quota-edit">{{trans('lang.edit')}}</label>

                                                                                           <input type="checkbox"
                                                                                                  id="fuel-quota-history"
                                                                                                  value="fuel.quota.history"
                                                                                                  name="fuel-quota[]"
                                                                                                  class="permission" {{in_array('fuel.quota.history', $permissions)
       ? "checked" : "" }} {{$roles->id == 1
       ? 'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="fuel-quota-history">{{trans('lang.view_history')}}</label>
                                                                                    </td>
                                                                             </tr>
                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.app_setting_global')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="global-setting"
                                                                                                  value="global-setting"
                                                                                                  name="global-setting[]"
                                                                                                  class="permission"
                                                                                                  {{in_array('global-setting', $permissions) ? "checked" : "" }}
                                                                                                  {{$roles->id == 1 ? 'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="global-setting">{{trans('lang.edit')}}</label>
                                                                                    </td>
                                                                             </tr>
                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.languages')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="language-list"
                                                                                                  value="language.list"
                                                                                                  name="language[]"
                                                                                                  class="permission" {{in_array('language.list', $permissions)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="language-list">{{trans('lang.list')}}</label>
                                                                                           <input type="checkbox"
                                                                                                  id="language-create"
                                                                                                  value="language.create"
                                                                                                  name="language[]"
                                                                                                  class="permission" {{in_array('language.create', $permissions)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="language-create">{{trans('lang.create')}}</label>
                                                                                           <input type="checkbox"
                                                                                                  id="language-edit"
                                                                                                  value="language.edit"
                                                                                                  name="language[]"
                                                                                                  class="permission" {{in_array('language.edit', $permissions)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="language-edit">{{trans('lang.edit')}}</label>



                                                                                    </td>
                                                                             </tr>

                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.privacy_policy')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="privacy"
                                                                                                  value="privacy"
                                                                                                  name="privacy[]"
                                                                                                  class="permission" {{in_array(
       'privacy',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="privacy">{{trans('lang.update')}}</label>
                                                                                    </td>
                                                                             </tr>
                                                                                                                                                          <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.contact_us')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="contactUs"
                                                                                                  value="contactUs"
                                                                                                  name="contactUs[]"
                                                                                                  class="permission" {{in_array(
       'contactUs',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="contactUs">{{trans('lang.update')}}</label>
                                                                                    </td>
                                                                             </tr>

                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.terms_and_conditions')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="terms"
                                                                                                  value="terms"
                                                                                                  name="terms[]"
                                                                                                  class="permission" {{in_array(
       'terms',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="terms">{{trans('lang.update')}}</label>
                                                                                    </td>
                                                                             </tr>
                                                                             <tr>
                                                                                    <td>
                                                                                           <strong>{{trans('lang.about_us')}}</strong>
                                                                                    </td>
                                                                                    <td>
                                                                                           <input type="checkbox"
                                                                                                  id="about-us"
                                                                                                  value="about-us"
                                                                                                  name="about-us[]"
                                                                                                  class="permission" {{in_array(
       'about-us',
       $permissions
)
       ? "checked" : "" }} {{$roles->id == 1 ?
       'disabled' : ''}}>
                                                                                           <label class=" control-label2"
                                                                                                  for="about-us">{{trans('lang.update')}}</label>
                                                                                    </td>
                                                                             </tr>
                                                                             <tr>
                                                                                    <td><strong>{{trans('lang.fuel_purchase')}}</strong></td>
                                                                                    <td>
                                                                                           <input type="checkbox" id="fuel-purchase-list"
                                                                                           value="fuel.purchase.list" name="fuel-purchase[]" class="permission"
                                                                                           {{ in_array('fuel.purchase.list', $permissions) ? 'checked' : '' }}
                                                                                           {{ $roles->id == 1 ? 'disabled' : '' }}>
                                                                                           <label class="control-label2" for="fuel-purchase-list">{{trans('lang.manage')}}</label>

                                                                                           <!-- <input type="checkbox" id="fuel-purchase-create"
                                                                                           value="fuel.purchase.create" name="fuel-purchase[]" class="permission"
                                                                                           {{ in_array('fuel.purchase.create', $permissions) ? 'checked' : '' }}
                                                                                           {{ $roles->id == 1 ? 'disabled' : '' }}>
                                                                                           <label class="control-label2" for="fuel-purchase-create">{{trans('lang.create')}}</label> -->

                                                                                           <!-- <input type="checkbox" id="fuel-purchase-edit"
                                                                                           value="fuel.purchase.edit" name="fuel-purchase[]" class="permission"
                                                                                           {{ in_array('fuel.purchase.edit', $permissions) ? 'checked' : '' }}
                                                                                           {{ $roles->id == 1 ? 'disabled' : '' }}>
                                                                                           <label class="control-label2" for="fuel-purchase-edit">{{trans('lang.edit')}}</label> -->
                                                                                    </td>
                                                                             </tr>

                                                                             <tr>
                                                                                    <td><strong>{{trans('lang.fuel_receive')}}</strong></td>
                                                                                    <td>
                                                                                           <input type="checkbox" id="fuel-receive-list"
                                                                                           value="fuel.receive.list" name="fuel-receive[]" class="permission"
                                                                                           {{ in_array('fuel.receive.list', $permissions) ? 'checked' : '' }}
                                                                                           {{ $roles->id == 1 ? 'disabled' : '' }}>
                                                                                           <label class="control-label2" for="fuel-receive-list">{{trans('lang.manage')}}</label>
<!-- 
                                                                                           <input type="checkbox" id="fuel-receive-create"
                                                                                           value="fuel.receive.create" name="fuel-receive[]" class="permission"
                                                                                           {{ in_array('fuel.receive.create', $permissions) ? 'checked' : '' }}
                                                                                           {{ $roles->id == 1 ? 'disabled' : '' }}>
                                                                                           <label class="control-label2" for="fuel-receive-create">{{trans('lang.create')}}</label> -->

                                                                                           <!-- <input type="checkbox" id="fuel-receive-edit"
                                                                                           value="fuel.receive.edit" name="fuel-receive[]" class="permission"
                                                                                           {{ in_array('fuel.receive.edit', $permissions) ? 'checked' : '' }}
                                                                                           {{ $roles->id == 1 ? 'disabled' : '' }}>
                                                                                           <label class="control-label2" for="fuel-receive-edit">{{trans('lang.edit')}}</label> -->
                                                                                    </td>
                                                                             </tr>

                                                                             <tr>
                                                                                    <td><strong>{{trans('lang.fuel_tax_verification')}}</strong></td>
                                                                                    <td>
                                                                                           <input type="checkbox" id="fuel-tax-list"
                                                                                           value="fuel.tax.list" name="fuel-tax[]" class="permission"
                                                                                           {{ in_array('fuel.tax.list', $permissions) ? 'checked' : '' }}
                                                                                           {{ $roles->id == 1 ? 'disabled' : '' }}>
                                                                                           <label class="control-label2" for="fuel-tax-list">{{trans('lang.manage')}}</label>

                                                                                           <!-- <input type="checkbox" id="fuel-tax-create"
                                                                                           value="fuel.tax.create" name="fuel-tax[]" class="permission"
                                                                                           {{ in_array('fuel.tax.create', $permissions) ? 'checked' : '' }}
                                                                                           {{ $roles->id == 1 ? 'disabled' : '' }}>
                                                                                           <label class="control-label2" for="fuel-tax-create">{{trans('lang.create')}}</label> -->

                                                                                           <!-- <input type="checkbox" id="fuel-tax-edit"
                                                                                           value="fuel.tax.edit" name="fuel-tax[]" class="permission"
                                                                                           {{ in_array('fuel.tax.edit', $permissions) ? 'checked' : '' }}
                                                                                           {{ $roles->id == 1 ? 'disabled' : '' }}>
                                                                                           <label class="control-label2" for="fuel-tax-edit">{{trans('lang.edit')}}</label> -->
                                                                                    </td>
                                                                             </tr>
                                                                              <tr>
                                                                                    <td><strong>{{trans('lang.fuel_request')}}</strong></td>
                                                                                    <td>
                                                                                           <input type="checkbox" id="fuel-request-list"
                                                                                           value="fuel.request.list" name="fuel-request[]" class="permission"
                                                                                           {{ in_array('fuel.request.list', $permissions) ? 'checked' : '' }}
                                                                                           {{ $roles->id == 1 ? 'disabled' : '' }}>
                                                                                           <label class="control-label2" for="fuel-request-list">{{trans('lang.manage')}}</label>
                                                                                    </td>
                                                                             </tr>
                                                                             <tr>
                                                                                    <td><strong>{{trans('lang.distributions')}}</strong></td>
                                                                                    <td>
                                                                                           <input type="checkbox" id="distributions-list"
                                                                                           value="distributions.list" name="distributions[]" class="permission"
                                                                                           {{ in_array('distributions.list', $permissions) ? 'checked' : '' }}
                                                                                           {{ $roles->id == 1 ? 'disabled' : '' }}>
                                                                                           <label class="control-label2" for="distributions-list">{{trans('lang.manage')}}</label>
                                                                                    </td>
                                                                             </tr>
                                                                             <tr>
                                                                                    <td><strong>{{trans('lang.states')}}</strong></td>
                                                                                    <td>
                                                                                           <input type="checkbox" id="states-list"
                                                                                           value="states.list" name="states[]" class="permission"
                                                                                           {{ in_array('states.list', $permissions) ? 'checked' : '' }}
                                                                                           {{ $roles->id == 1 ? 'disabled' : '' }}>
                                                                                           <label class="control-label2" for="states-list">{{trans('lang.manage')}}</label>
                                                                                    </td>
                                                                             </tr>

                                                                               <tr>
                                                                                    <td><strong>{{trans('lang.assign_fuel_station')}}</strong></td>
                                                                                    <td>
                                                                                           <input type="checkbox" id="assign-fuel-station-list"
                                                                                           value="assignFuelStation.list" name="assignFuelStation[]" class="permission"
                                                                                           {{ in_array('assignFuelStation.list', $permissions) ? 'checked' : '' }}
                                                                                           {{ $roles->id == 1 ? 'disabled' : '' }}>
                                                                                           <label class="control-label2" for="assign-fuel-station-list">{{trans('lang.manage')}}</label>
                                                                                    </td>
                                                                             </tr>

                                                                      </tbody>
                                                               </table>
                                                        </div>
                                                 </div>

                                          </div>

                                   </fieldset>
                            </div>

                     </div>

                     <div class="form-group col-12 text-center btm-btn">
                            @if($roles->id != 1)

                            <button type="button" class="btn btn-primary save_role"><i class="fa fa-save"></i> {{
              trans('lang.save')}}
                            </button>
                            @endif




                            <a href="{{url('role')}}" class="btn btn-default"><i class="fa fa-undo"></i>{{
       trans('lang.cancel')}}</a>
                     </div>
              </form>

       </div>
</div>
@endsection

@section('scripts')

<script>
       $(".save_role").click(async function() {

              var is_disable_delete="<?php echo env('IS_DISABLE_DELETE'); ?>";
              if(is_disable_delete==1) {
                     alert(doNotUpdateAlert);
                     return false;
              }

              $(".success_top").hide();
              $(".error_top").hide();
              var name=$("#name").val();

              if(name=="") {
                     $(".error_top").show();
                     $(".error_top").html("");
                     $(".error_top").append("<p>{{trans('lang.user_name_help')}}</p>");
                     window.scrollTo(0,0);
                     return false;
              } else {
                     $('form#roleForm').submit();

              }

       });

       $('#all_permission').on('click',function() {

              if($(this).is(':checked')) {
                     $.each($('.permission'),function() {
                            $(this).prop('checked',true);
                     });
              } else {
                     $.each($('.permission'),function() {
                            $(this).prop('checked',false);
                     });
              }

       });
</script>

@endsection