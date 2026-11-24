<?php

use Illuminate\Support\Facades\Route;

/*
   |--------------------------------------------------------------------------
   | Web Routes
   |--------------------------------------------------------------------------
   |
   | Here is where you can register web routes for your application. These
   | routes are loaded by the RouteServiceProvider within a group which
   | contains the "web" middleware group. Now create something great!
   |
   */

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/customers-list', function () {
   return view('customers.index2');
})->name('customers.list');

Route::get('/run', function () {
    Artisan::call('cache:clear');
    return 'run completed';
});


Route::post('/log-activity', [App\Http\Controllers\HomeController::class, 'saveLogActivity'])->name('saveLogActivity');

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
Route::get('lang/change', [App\Http\Controllers\LangController::class, 'change'])->name('changeLang');
Route::post('notification/send', [App\Http\Controllers\NotificationController::class, 'sendNotification'])->name('sendNotification');

Route::middleware(['permission:fuel-request,fuel.request.list'])->group(function () {
   Route::get('/fuel-request', [App\Http\Controllers\FuelPurchaseManageController::class, 'fuelRequestListing'])->name('fuel.request');
   Route::get('/fuel-request/save/{id?}', [App\Http\Controllers\FuelPurchaseManageController::class, 'fuelRequestSave'])->name('fuel.request.form');
});

Route::get('/fuel-purchases', [App\Http\Controllers\FuelPurchaseManageController::class, 'fuelPurchasesListing'])->name('fuel.purchases');
Route::middleware(['permission:fuel-purchase,fuel.purchase.list'])->group(function () {
   Route::get('/fuel-purchases/save/{id?}', [App\Http\Controllers\FuelPurchaseManageController::class, 'fuelPurchasesSave'])->name('fuel.purchases.form');
});

Route::get('/fuel-receivings', [App\Http\Controllers\FuelPurchaseManageController::class, 'fuelReceivingsListing'])->name('fuel.receivings');
Route::middleware(['permission:fuel-receive,fuel.receive.list'])->group(function () {
   Route::get('/fuel-receivings/save/{id?}', [App\Http\Controllers\FuelPurchaseManageController::class, 'fuelReceivingsSave'])->name('fuel.receivings.form');
});
Route::middleware(['permission:fuel-tax,fuel.tax.list'])->group(function () {
   Route::get('/fuel/tax_verifications', [App\Http\Controllers\FuelPurchaseManageController::class, 'fuelTaxVerificationsListing'])->name('fuel.tax_verifications');
   Route::get('/fuel/tax_verifications/save/{id?}', [App\Http\Controllers\FuelPurchaseManageController::class, 'fuelTaxVerificationsSave'])->name('fuel.tax_verifications.form');
});

Route::middleware(['permission:customers,customer.list'])->group(function () {
   Route::get('/customers', [App\Http\Controllers\CustomersController::class, 'index'])->name('customers');
});
Route::middleware(['permission:assignFuelStation,assignFuelStation.list'])->group(function () {
   Route::get('/customers/assign-petrol-pump', [App\Http\Controllers\CustomersController::class, 'assignPetrolPumpToCustomer'])->name('assignPetrolPumpToCustomer');
});
Route::middleware(['permission:approve_customers,approve.customer.list'])->group(function () {
   Route::get('/customers/approved', [App\Http\Controllers\CustomersController::class, 'index'])->name('customers.approved');
});
Route::middleware(['permission:pending_customers,pending.customer.list'])->group(function () {
   Route::get('/customers/pending', [App\Http\Controllers\CustomersController::class, 'index'])->name('customers.pending');
});
Route::middleware(['permission:customers,customer.edit'])->group(function () {
   Route::get('/customers/edit/{id}', [App\Http\Controllers\CustomersController::class, 'edit'])->name('customers.edit');
});
Route::middleware(['permission:customers,customer.edit'])->group(function () {
   Route::get('/customers/odometer-reading/history/{id}', [App\Http\Controllers\CustomersController::class, 'odometerReadingHistory'])->name('customers.odometerReadingHistory');
   Route::get('/odometer-reading/history/view/{id}', [App\Http\Controllers\CustomersController::class, 'odometerReadingHistoryView'])->name('customers.odometerReadingHistoryView');
});
Route::middleware(['permission:customers,customer.create'])->group(function () {
   Route::get('/customers/create', [App\Http\Controllers\CustomersController::class, 'create'])->name('customers.create');
});


Route::middleware(['permission:owners,owner.list'])->group(function () {
   Route::get('/owners', [App\Http\Controllers\OwnersController::class, 'index'])->name('owners');
});
Route::middleware(['permission:approve_owners,approve.owner.list'])->group(function () {
   Route::get('/owners/approved', [App\Http\Controllers\OwnersController::class, 'index'])->name('owners.approved');
});
Route::middleware(['permission:pending_owners,pending.owner.list'])->group(function () {
   Route::get('/owners/pending', [App\Http\Controllers\OwnersController::class, 'index'])->name('owners.pending');
});
Route::middleware(['permission:owners,owner.edit'])->group(function () {
   Route::get('/owners/edit/{id}', [App\Http\Controllers\OwnersController::class, 'edit'])->name('owners.edit');
});
Route::middleware(['permission:owners,owner.create'])->group(function () {
   Route::get('/owners/create', [App\Http\Controllers\OwnersController::class, 'create'])->name('owners.create');
});


Route::get('/users/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('users.profile');
Route::post('/reverted-quota-users-list', [App\Http\Controllers\UserController::class, 'showMismatchedQuotaUsers']);
Route::post('/users/profile/update/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('users.profile.update');

Route::middleware(['permission:states,states.list'])->group(function () {
   Route::get('/states', [App\Http\Controllers\UserController::class, 'state'])->name('state');
   Route::get('/state/save/{id?}', [App\Http\Controllers\UserController::class, 'stateSave'])->name('stateSave');
});


Route::prefix('settings')->group(function () {
   Route::get('globals', [App\Http\Controllers\SettingsController::class, 'globals'])->name('settings.globals');
   Route::middleware(['permission:privacy,privacy'])->group(function () {
      Route::get('/privacyPolicy', [App\Http\Controllers\SettingsController::class, 'privacyPolicy'])->name('settings.privacyPolicy');
   });
   Route::middleware(['permission:contactUs,contactUs'])->group(function () {
      Route::get('/contactUs', [App\Http\Controllers\SettingsController::class, 'contactUs'])->name('settings.contactUs');
   });
   Route::middleware(['permission:global-setting,global-setting'])->group(function () {
      Route::get('/global-setting', [App\Http\Controllers\SettingsController::class, 'global'])->name('settings.global');
   });
   Route::middleware(['permission:terms,terms'])->group(function () {
      Route::get('/termsAndConditions', [App\Http\Controllers\SettingsController::class, 'termsAndConditions'])->name('settings.termsAndConditions');
   });
   Route::middleware(['permission:about-us,about-us'])->group(function () {
      Route::get('/aboutus', [App\Http\Controllers\SettingsController::class, 'about'])->name('settings.aboutus');
   });
   Route::middleware(['permission:fuel-quota,fuel.quota.list'])->group(function () {
      Route::get('fuel-quota', [App\Http\Controllers\AdminFuelQuotaController::class, 'index'])->name('fuel-quota.index');
   });
   Route::middleware(['permission:fuel-quota,fuel.quota.edit'])->group(function () {
      Route::get('fuel-quota/edit/{id}', [App\Http\Controllers\AdminFuelQuotaController::class, 'edit'])->name('fuel-quota.edit');
   });
   Route::middleware(['permission:fuel-quota,fuel.quota.create'])->group(function () {
      Route::get('fuel-quota/create', [App\Http\Controllers\AdminFuelQuotaController::class, 'create'])->name('fuel-quota.create');
   });
   Route::middleware(['permission:language,language.list'])->group(function () {

      Route::get('/languages', [App\Http\Controllers\SettingsController::class, 'languages'])->name('settings.languages');
   });
   Route::middleware(['permission:language,language.' . ((str_contains(Request::url(), 'save')) ? (explode("save", Request::url())[1] ? "edit" : "create") : Request::url())])->group(function () {


      Route::get('/languages/save/{id?}', [App\Http\Controllers\SettingsController::class, 'saveLanguage'])->name('settings.languages.save');
   });


});

Route::post('send-email', [App\Http\Controllers\SendEmailController::class, 'sendMail'])->name('sendMail');

Route::post('/delete-user/{id}', [App\Http\Controllers\DeleteUserAuthenticationController::class, 'deleteUser'])->name('delete-user');
Route::post('/update-auth-email/{id}', [App\Http\Controllers\DeleteUserAuthenticationController::class, 'updateEmail'])->name('update-auth-email');

Route::middleware(['permission:cms,cms.list'])->group(function () {
   Route::get('cms', [App\Http\Controllers\CmsController::class, 'index'])->name('cms');
});
Route::middleware(['permission:cms,cms.edit'])->group(function () {
   Route::get('/cms/edit/{id}', [App\Http\Controllers\CmsController::class, 'edit'])->name('cms.edit');
});
Route::middleware(['permission:cms,cms.create'])->group(function () {
   Route::get('/cms/create', [App\Http\Controllers\CmsController::class, 'create'])->name('cms.create');
});

Route::middleware(['permission:vehicle-type,vehicle.type.list'])->group(function () {
   Route::get('/vehicle-type', [App\Http\Controllers\VehicleTypeController::class, 'index'])->name('vehicle-type');
});
Route::middleware(['permission:vehicle-type,vehicle.type.edit'])->group(function () {
   Route::get('/vehicle-type/edit/{id}', [App\Http\Controllers\VehicleTypeController::class, 'edit'])->name('vehicle-type.edit');
});
Route::middleware(['permission:vehicle-type,vehicle.type.create'])->group(function () {
   Route::get('/vehicle-type/create', [App\Http\Controllers\VehicleTypeController::class, 'create'])->name('vehicle-type.create');
});

Route::middleware(['permission:vehicle_days,vehicle.days.list'])->group(function () {
   Route::get('/vehicle-days', [App\Http\Controllers\VehicleDaysController::class, 'index'])->name('vehicle-days');
});
Route::middleware(['permission:vehicle_days,vehicle.days.edit'])->group(function () {
   Route::get('/vehicle-days/edit/{id}', [App\Http\Controllers\VehicleDaysController::class, 'edit'])->name('vehicle-days.edit');
});

Route::middleware(['permission:admins,admin.list'])->group(function () {
   Route::get('admin-users', [App\Http\Controllers\AdminUsersController::class, 'index'])->name('admin.users.index');
   Route::get('admin-users/activity', [App\Http\Controllers\AdminUsersController::class, 'userActivity'])->name('admin.activity.index');
   Route::post('admin/activities/search-users', [App\Http\Controllers\AdminUsersController::class, 'searchByUsers'])->name('admin.activities.searchByUsers');

});

Route::middleware(['permission:admins,admin.create'])->group(function () {
   Route::get('admin-users/create', [App\Http\Controllers\AdminUsersController::class, 'create'])->name('admin.users.create');
});

Route::middleware(['permission:admins,admin.store'])->group(function () {
   Route::post('admin-users/store', [App\Http\Controllers\AdminUsersController::class, 'store'])->name('admin.users.store');
});
Route::middleware(['permission:admins,admin.edit'])->group(function () {
   Route::get('admin-users/edit/{id}', [App\Http\Controllers\AdminUsersController::class, 'edit'])->name('admin.users.edit');
});
Route::middleware(['permission:admins,admin.update'])->group(function () {
   Route::post('admin-users/update/{id}', [App\Http\Controllers\AdminUsersController::class, 'update'])->name('admin.users.update');
});
Route::middleware(['permission:admins,admin.delete'])->group(function () {
   Route::get('admin/activity/delete/{id}', [App\Http\Controllers\AdminUsersController::class, 'deleteActivity'])->name('admin.activity.delete');
   Route::get('admin/activity/delete/multiple/{ids}', [App\Http\Controllers\AdminUsersController::class, 'deleteMultipleActivities'])->name('admin.activity.deleteMultiple');
   Route::get('admin-users/delete/{userid}', [App\Http\Controllers\AdminUsersController::class, 'delete'])->name('admin.users.delete');
});

Route::middleware(['permission:roles,roles.list'])->group(function () {
   Route::get('role', [App\Http\Controllers\RolesController::class, 'index'])->name('role.index');
});
Route::middleware(['permission:roles,roles.create'])->group(function () {
   Route::get('role/create', [App\Http\Controllers\RolesController::class, 'create'])->name('role.create');
});
Route::middleware(['permission:roles,roles.store'])->group(function () {
   Route::post('role/store', [App\Http\Controllers\RolesController::class, 'store'])->name('role.store');
});
Route::middleware(['permission:roles,roles.edit'])->group(function () {
   Route::get('role/edit/{id}', [App\Http\Controllers\RolesController::class, 'edit'])->name('role.edit');
});
Route::middleware(['permission:roles,roles.update'])->group(function () {
   Route::post('role/update/{id}', [App\Http\Controllers\RolesController::class, 'update'])->name('role.update');
});
Route::middleware(['permission:roles,roles.delete'])->group(function () {
   Route::get('role/delete/{userid}', [App\Http\Controllers\RolesController::class, 'delete'])->name('role.delete');
});
Route::middleware(['permission:employee,employee.list'])->group(function () {
   Route::get('employee', [App\Http\Controllers\EmployeeController::class, 'index'])->name('employee');
});
Route::middleware(['permission:employee,employee.create'])->group(function () {
   Route::get('employee/create', [App\Http\Controllers\EmployeeController::class, 'create'])->name('employee.create');
});
Route::middleware(['permission:employee,employee.edit'])->group(function () {
   Route::get('employee/edit/{id}', [App\Http\Controllers\EmployeeController::class, 'edit'])->name('employee.edit');
});
Route::middleware(['permission:onboard,onboard.list'])->group(function () {
   Route::get('on-board', [App\Http\Controllers\OnBoardController::class, 'index'])->name('on-board');
});
Route::middleware(['permission:onboard,onboard.edit'])->group(function () {
   Route::get('on-board/edit/{id}', [App\Http\Controllers\OnBoardController::class, 'edit'])->name('on-board.edit');
});
Route::middleware(['permission:fuel-transaction,fuel.transaction.list'])->group(function () {
   Route::get('fuel-transaction', [App\Http\Controllers\FuelTransactionController::class, 'index'])->name('fuel-transaction');
});
Route::middleware(['permission:fuel-transaction,fuel.transaction.view'])->group(function () {
   Route::get('fuel-transaction/view/{id}', [App\Http\Controllers\FuelTransactionController::class, 'view'])->name('fuel-transaction.view');
});
Route::middleware(['permission:report,report'])->group(function () {
   Route::get('transaction-report', [App\Http\Controllers\ReportController::class, 'transcationReport'])->name('transaction-report');
});
Route::middleware(['permission:fuel-quota,fuel.quota.history'])->group(function () {
   Route::get('fuel-quota/history', [App\Http\Controllers\AdminFuelQuotaController::class, 'history'])->name('fuel-quota.history');
});
Route::middleware(['permission:reset-fuel-quota,reset.fuel.quota.history'])->group(function () {
   Route::get('reset-fuel-quota/history', [App\Http\Controllers\CustomersController::class, 'history'])->name('reset-fuel-quota.history');
});

Route::get('/download-apk/{apk}', [App\Http\Controllers\APKController::class, 'download'])->name('apk.download');

Route::get('customer-report', [App\Http\Controllers\ReportController::class, 'customerReport'])->name('customer-report');
Route::get('owner-report', [App\Http\Controllers\ReportController::class, 'ownerReport'])->name('owner-report');

Route::get('privacy-policy', [App\Http\Controllers\SharePrivacyPolicyController::class, 'share']);
Route::get('contact-us', [App\Http\Controllers\SharePrivacyPolicyController::class, 'shareContactUs']);

Route::get('/customers/deleted', [App\Http\Controllers\CustomersController::class, 'deletedCustomer'])->name('customers.deleted');

Route::get('/owners/deleted', [App\Http\Controllers\OwnersController::class, 'deletedOwner'])->name('owners.deleted');

Route::get('owner-transaction-report', [App\Http\Controllers\ReportController::class, 'ownerTransactionReport'])->name('owner-transaction-report');

Route::get('/vehicle-type/view/{id}', [App\Http\Controllers\VehicleTypeController::class, 'view'])->name('vehicle-type.view');
