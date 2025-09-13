<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Trips\TripController;
use App\Http\Controllers\Admin\Vehicles\VehicleTypesController;
use App\Http\Controllers\Admin\Wallet\AdminWalletCodeController;
use App\Http\Controllers\Admin\Language\LanguageController;

use App\Models\Invoice;
use App\Events\PaymentCompleted;
use App\Http\Controllers\Admin\Currency\{
    CurrencyController,
    CountryCurrenciesController
};
use App\Http\Controllers\Admin\Shared\{
    ContactUsController,
};
use App\Http\Controllers\Admin\Profile\{
    AddressController,
    ProfileController,
    DriverProfileController,
    DriverVehicleController,
    WalletController

};
use App\Http\Controllers\Admin\Users\{
    CouponController,
    UsersController
};
use App\Http\Controllers\Admin\Locations\{
    CountriesController,
    CitiesController,
    DistrictsController,
};
use App\Http\Controllers\Notification\{
    FcmTokenController,
    NotificationController
};
use App\Http\Controllers\Admin\Drivers\{
    DriversAvailabilityController,
    DriversController
};
use App\Http\Controllers\Admin\Management\{
    RolePermissionController,
    AudiLogController,
    DashboardController,
};
use App\Http\Controllers\Admin\Tickets\{
    TicketController,
    TicketCategoryController,
};

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/test-roles', function () {
    try {
        $roles = \Spatie\Permission\Models\Role::all();
        $permissions = \Spatie\Permission\Models\Permission::all();
        return response()->json([
            'roles' => $roles->count(),
            'permissions' => $permissions->count(),
            'status' => 'Roles & Permissions system is working!'
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::middleware('auth','language')->group(function () {

    Route::get('countries/{country}/cities', [CitiesController::class, 'getCities'])->name('countries.cities');
    Route::get('/profile/{userId}/addresses', [ProfileController::class, 'getUserAddresses'])->name('profile.getAddresses');
    Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/{user}/update-extra', [ProfileController::class, 'updateExtra'])->name('profile.update.extra');
    Route::delete('/profile/{id}', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/drivers/{id}/verify', [DriverProfileController::class, 'verify'])->name('profile.driver.verify')->middleware('permission:verify_drivers');

    Route::prefix('profile/vehicle')->name('profile.vehicle.')->group(function () {
        Route::put('{vehicle}', [DriverVehicleController::class, 'update'])->name('update')->middleware('permission:edit_vehicles');
    });

    Route::prefix('dashboard/admin/profile/addresses')->name('admin.profile.addresses.')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('index');
        Route::get('/data/{userId}', [AddressController::class, 'getUserAddresses'])->name('data');
        Route::get('/{userId}/create', [AddressController::class, 'create']);
        Route::get('/{cityId}/districts', [AddressController::class, 'getDistricts']);
        Route::post('/{userId}/store', [AddressController::class, 'store']);
        Route::get('/{userId}/{id}/edit', [AddressController::class, 'edit'])->name('edit');
        Route::put('/{userId}/{id}', [AddressController::class, 'update'])->name('update');
        Route::delete('/{id}', [AddressController::class, 'destroy'])->name('destroy');
    });

    // Dashboard - requires view_dashboard permission
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['verified', 'permission:view_dashboard'])->name('dashboard');

    // Roles & Permissions Management
    Route::prefix('dashboard/admin/roles')->name('admin.roles.')->group(function () {
        Route::get('/', [RolePermissionController::class, 'rolesIndex'])->name('index')->middleware('permission:view_roles');
        Route::get('/data', [RolePermissionController::class, 'getRolesData'])->name('data')->middleware('permission:view_roles');
        Route::post('/', [RolePermissionController::class, 'storeRole'])->name('store')->middleware('permission:create_roles');
        Route::put('/{id}', [RolePermissionController::class, 'updateRole'])->name('update')->middleware('permission:edit_roles');
        Route::delete('/{id}', [RolePermissionController::class, 'destroyRole'])->name('destroy')->middleware('permission:delete_roles');
        Route::get('/{roleId}/permissions', [RolePermissionController::class, 'rolePermissions'])->name('permissions')->middleware('permission:assign_permissions');
        Route::post('/{roleId}/permissions', [RolePermissionController::class, 'updateRolePermissions'])->name('permissions.update')->middleware('permission:assign_permissions');
    });

    Route::prefix('dashboard/admin/permissions')->name('admin.permissions.')->group(function () {
        Route::get('/', [RolePermissionController::class, 'permissionsIndex'])->name('index')->middleware('permission:view_permissions');
        Route::get('/data', [RolePermissionController::class, 'getPermissionsData'])->name('data')->middleware('permission:view_permissions');
        Route::post('/', [RolePermissionController::class, 'storePermission'])->name('store')->middleware('permission:create_permissions');
        Route::put('/{id}', [RolePermissionController::class, 'updatePermission'])->name('update')->middleware('permission:edit_permissions');
        Route::delete('/{id}', [RolePermissionController::class, 'destroyPermission'])->name('destroy')->middleware('permission:delete_permissions');
    });

    Route::prefix('dashboard/admin/users/roles')->name('admin.users.roles.')->group(function () {
        Route::get('/', [RolePermissionController::class, 'userRoles'])->name('index')->middleware('permission:assign_roles');
        Route::get('/data', [RolePermissionController::class, 'getUserRolesData'])->name('data')->middleware('permission:assign_roles');
        Route::get('/{userId}/edit', [RolePermissionController::class, 'editUserRoles'])->name('edit')->middleware('permission:assign_roles');
        Route::post('/{userId}', [RolePermissionController::class, 'updateUserRoles'])->name('update')->middleware('permission:assign_roles');
        Route::post('/preview-permissions', [RolePermissionController::class, 'previewPermissions'])->name('preview-permissions')->middleware('permission:assign_roles');
    });

    // Additional routes for role permissions preview
    Route::get('/dashboard/admin/roles/{roleName}/permissions-preview', [RolePermissionController::class, 'rolePermissionsPreview'])->name('admin.roles.permissions.preview')->middleware('permission:view_roles');

    // User management
    Route::prefix('dashboard/admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('index')->middleware('permission:view_users');
        Route::post('/', [UsersController::class, 'store'])->name('store')->middleware('permission:create_users');
        Route::get('/{id}/edit', [UsersController::class, 'edit'])->name('edit')->middleware('permission:edit_users');
        Route::put('/{id}', [UsersController::class, 'update'])->name('update')->middleware('permission:edit_users');
        Route::delete('/{id}', [UsersController::class, 'destroy'])->name('destroy')->middleware('permission:delete_users');
        Route::get('/data', [UsersController::class, 'getUsersData'])->name('data')->middleware('permission:view_users');
    });

    // Driver management
    Route::prefix('dashboard/admin/drivers')->name('admin.drivers.')->group(function () {
        Route::get('/', [DriversController::class, 'index'])->name('index')->middleware('permission:view_drivers');
        Route::post('/', [DriversController::class, 'store'])->name('store')->middleware('permission:create_drivers');
        Route::get('/{id}/show', [DriversController::class, 'show'])->name('show')->middleware('permission:view_drivers');
        Route::get('/{id}/edit', [DriversController::class, 'edit'])->name('edit')->middleware('permission:edit_drivers');
        Route::put('/{id}', [DriversController::class, 'update'])->name('update')->middleware('permission:edit_drivers');
        Route::delete('/{id}', [DriversController::class, 'destroy'])->name('destroy')->middleware('permission:delete_drivers');
        Route::get('/data', [DriversController::class, 'getDriversData'])->name('data')->middleware('permission:view_drivers');

        // unverified Drivers
        Route::get('/unverified', [DriversController::class, 'unverifiedIndex'])->name('unverified.index')->middleware('permission:view_drivers');
        Route::get('/unverified/data', [DriversController::class, 'getUnverifiedDrivers'])->name('unverified.data')->middleware('permission:view_drivers');
        Route::get('/{id}/licenses', [DriversController::class, 'licenses'])->name('licenses')->middleware('permission:view_drivers');

        // available Drivers
        Route::get('/available', [DriversAvailabilityController::class, 'index'])->name('available.index')->middleware('permission:view_driver_availability');
        Route::get('/available/data', [DriversAvailabilityController::class, 'getDriversAvailabilityData'])->name('available.data')->middleware('permission:view_driver_availability');

        // unavailable Drivers
        Route::get('/unavailable', [DriversAvailabilityController::class, 'UnavailableIndex'])->name('unavailable.index')->middleware('permission:view_driver_availability');
        Route::get('/unavailable/data', [DriversAvailabilityController::class, 'getUnavailableDriversData'])->name('unavailable.data')->middleware('permission:view_driver_availability');
    });

    // Countries management
    Route::prefix('dashboard/admin/countries')->name('admin.countries.')->group(function () {
        Route::get('/', [CountriesController::class, 'index'])->name('index')->middleware('permission:view_countries');
        Route::post('/', [CountriesController::class, 'store'])->name('store')->middleware('permission:create_countries');
        Route::get('/{id}/edit', [CountriesController::class, 'edit'])->name('edit')->middleware('permission:edit_countries');
        Route::put('/{id}', [CountriesController::class, 'update'])->name('update')->middleware('permission:edit_countries');
        Route::delete('/{id}', [CountriesController::class, 'destroy'])->name('destroy')->middleware('permission:delete_countries');
        Route::get('/data', [CountriesController::class, 'getCountriesData'])->name('data')->middleware('permission:view_countries');
        Route::get('/getAll', [CountriesController::class, 'getAllCountries'])->name('getAll')->middleware('permission:view_countries');
    });

    // Cities management
    Route::prefix('dashboard/admin/cities')->name('admin.cities.')->group(function () {
        Route::get('/', [CitiesController::class, 'index'])->name('index')->middleware('permission:view_cities');
        Route::post('/', [CitiesController::class, 'store'])->name('store')->middleware('permission:create_cities');
        Route::get('/{id}/edit', [CitiesController::class, 'edit'])->name('edit')->middleware('permission:edit_cities');
        Route::put('/{id}', [CitiesController::class, 'update'])->name('update')->middleware('permission:edit_cities');
        Route::delete('/{id}', [CitiesController::class, 'destroy'])->name('destroy')->middleware('permission:delete_cities');
        Route::get('/data', [CitiesController::class, 'getCitiesData'])->name('data')->middleware('permission:view_cities');
        Route::get('/getAll', [CitiesController::class, 'getAllCities'])->name('getAll')->middleware('permission:view_cities');
    });

    // Districts management
    Route::prefix('dashboard/admin/districts')->name('admin.districts.')->group(function () {
        Route::get('/', [DistrictsController::class, 'index'])->name('index')->middleware('permission:view_districts');
        Route::post('/', [DistrictsController::class, 'store'])->name('store')->middleware('permission:create_districts');
        Route::get('/{id}/edit', [DistrictsController::class, 'edit'])->name('edit')->middleware('permission:edit_districts');
        Route::put('/{id}', [DistrictsController::class, 'update'])->name('update')->middleware('permission:edit_districts');
        Route::delete('/{id}', [DistrictsController::class, 'destroy'])->name('destroy')->middleware('permission:delete_districts');
        Route::get('/data', [DistrictsController::class, 'getDistrictsData'])->name('data')->middleware('permission:view_districts');
        Route::get('/getAll', [DistrictsController::class, 'getAllDistricts'])->name('getAll')->middleware('permission:view_districts');
    });

    // Contacts management
    Route::prefix('dashboard/admin/contacts')->name('admin.contacts.')->group(function () {
        Route::get('/users-page', [ContactUsController::class, 'index'])->name('users.page')->middleware('permission:view_contacts');
        Route::get('/driver-page', [ContactUsController::class, 'driverPage'])->name('drivers.page')->middleware('permission:view_contacts');
        Route::get('/all', [ContactUsController::class, 'allContacts'])->name('all.page')->middleware('permission:view_contacts');
        Route::get('/users', [ContactUsController::class, 'getUserContacts'])->name('users')->middleware('permission:view_contacts');
        Route::get('/drivers', [ContactUsController::class, 'getDriverContacts'])->name('drivers')->middleware('permission:view_contacts');
        Route::get('/all-data', [ContactUsController::class, 'getAllContacts'])->name('all')->middleware('permission:view_contacts');
        Route::get('/{id}', [ContactUsController::class, 'show'])->name('show')->middleware('permission:view_contacts');
        Route::delete('/{id}', [ContactUsController::class, 'destroy'])->name('destroy')->middleware('permission:delete_contacts');
        Route::get('/stats/overview', [ContactUsController::class, 'getStats'])->name('stats')->middleware('permission:view_contacts');
    });

    // Vehicle Types management
    Route::prefix('dashboard/admin/vehicle_types')->name('admin.vehicle_types.')->group(function () {
        Route::get('/', [VehicleTypesController::class, 'index'])->name('index')->middleware('permission:view_vehicle_types');
        Route::get('/data', [VehicleTypesController::class, 'getVehicleTypesData'])->name('data')->middleware('permission:view_vehicle_types');
        Route::get('/create', [VehicleTypesController::class, 'create'])->name('create')->middleware('permission:create_vehicle_types');
        Route::post('/store', [VehicleTypesController::class, 'store'])->name('store')->middleware('permission:create_vehicle_types');
        Route::get('/{id}/edit', [VehicleTypesController::class, 'edit'])->name('edit')->middleware('permission:edit_vehicle_types');
        Route::put('/{id}', [VehicleTypesController::class, 'update'])->name('update')->middleware('permission:edit_vehicle_types');
        Route::delete('/{id}', [VehicleTypesController::class, 'destroy'])->name('destroy')->middleware('permission:delete_vehicle_types');
        Route::get('/commission', [VehicleTypesController::class, 'getCommissionPercentage'])->name('commission.get')->middleware('permission:edit_vehicle_types');
        Route::post('/commission', [VehicleTypesController::class, 'updateCommissionPercentage'])->name('commission.update')->middleware('permission:edit_vehicle_types');
    });

    // Trips management
    Route::prefix('dashboard/admin/trips')->name('admin.trips.')->group(function () {
        Route::get('/', [TripController::class, 'index'])->name('index')->middleware('permission:view_trips');
        Route::get('/data', [TripController::class, 'getTripData'])->name('data')->middleware('permission:view_trips');
        Route::get('/by-status', [TripController::class, 'getTripsByStatus'])->name('byStatus')->middleware('permission:view_trips');
        Route::get('/{tripId}/driver-location', [TripController::class, 'getDriverLocation'])->name('driver.location')->middleware('permission:view_trips');
    });

    // Currencies management
    Route::prefix('dashboard/admin/currencies')->name('admin.currencies.')->group(function () {
        Route::get('/', [CurrencyController::class, 'index'])->name('index')->middleware('permission:view_currencies');
        Route::post('/', [CurrencyController::class, 'store'])->name('store')->middleware('permission:create_currencies');
        Route::get('/data', [CurrencyController::class, 'getData'])->name('data')->middleware('permission:view_currencies');
        Route::delete('/{id}', [CurrencyController::class, 'destroy'])->name('destroy')->middleware('permission:delete_currencies');
    });

    // Country Currencies management
    Route::prefix('dashboard/admin/countrycurrencies')->name('admin.countrycurrencies.')->group(function () {
        Route::get('/', [CountryCurrenciesController::class, 'index'])->name('index')->middleware('permission:manage_country_currencies');
        Route::post('/', [CountryCurrenciesController::class, 'store'])->name('store')->middleware('permission:manage_country_currencies');
        Route::get('/form-options', [CountryCurrenciesController::class, 'getFormOptions'])->name('getFormOptions')->middleware('permission:manage_country_currencies');
        Route::get('/data', [CountryCurrenciesController::class, 'getData'])->name('data')->middleware('permission:manage_country_currencies');
        Route::delete('/{id}', [CountryCurrenciesController::class, 'destroy'])->name('destroy')->middleware('permission:manage_country_currencies');
        Route::get('/currencies/{country_id}', [CountryCurrenciesController::class, 'getCountryCurrencies'])->name('currencies')->middleware('permission:manage_country_currencies');
    });

    // Notifications management
    Route::prefix('dashboard/admin/notfiction')->name('admin.notfiction.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index')->middleware('permission:view_notifications');
        Route::get('/send', [NotificationController::class, 'sendForm'])->name('.send.form')->middleware('permission:send_notifications');
        Route::post('/send/firebase', [NotificationController::class, 'sendToUsersFirebase'])->name('send.firebase')->middleware('permission:send_notifications');
        Route::post('send/email', [NotificationController::class, 'sendToUsersEmail'])->name('send.email')->middleware('permission:send_notifications');
    });

    // Audit Logs
    Route::prefix('dashboard/admin/audi-logs')->group(function () {
        Route::get('/', [AudiLogController::class, 'index'])->name('admin.audi_logs.index')->middleware('permission:view_audit_logs');
        Route::get('/data', [AudiLogController::class, 'dataTableAjax'])->name('admin.audi_logs.data')->middleware('permission:view_audit_logs');
    });

    // Coupons management
    Route::prefix('dashboard/admin/coupons')->name('admin.coupons.')->group(function () {
        Route::get('/', [CouponController::class, 'index'])->name('index')->middleware('permission:view_coupons');
        Route::get('/data', [CouponController::class, 'getCouponsData'])->name('data')->middleware('permission:view_coupons');
        Route::post('/', [CouponController::class, 'store'])->name('store')->middleware('permission:create_coupons');
        Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('edit')->middleware('permission:edit_coupons');
        Route::put('/{coupon}', [CouponController::class, 'update'])->name('update')->middleware('permission:edit_coupons');
        Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('destroy')->middleware('permission:delete_coupons');
    });

    // Tickets management
    Route::prefix('dashboard/admin/tickets')->name('admin.tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index')->middleware('permission:view_tickets');
        Route::get('/data', [TicketController::class, 'getData'])->name('data')->middleware('permission:view_tickets');
        Route::get('/stats', [TicketController::class, 'getStats'])->name('stats')->middleware('permission:view_ticket_stats');
        Route::get('/{id}', [TicketController::class, 'show'])->name('show')->middleware('permission:view_tickets');
        Route::post('/{id}/reply', [TicketController::class, 'reply'])->name('reply')->middleware('permission:reply_tickets');
        Route::put('/{id}/status', [TicketController::class, 'updateStatus'])->name('update-status')->middleware('permission:manage_ticket_status');
        Route::put('/{id}/priority', [TicketController::class, 'updatePriority'])->name('update-priority')->middleware('permission:manage_ticket_priority');
        Route::put('/{id}/assign', [TicketController::class, 'assign'])->name('assign')->middleware('permission:assign_tickets');
        Route::delete('/{id}', [TicketController::class, 'destroy'])->name('destroy')->middleware('permission:delete_tickets');
        Route::put('/{id}/category', [TicketController::class, 'updateCategory'])->name('update-category')->middleware('permission:manage_ticket_category');
    });

    // Ticket Categories management
    Route::prefix('dashboard/admin/ticket-categories')->name('admin.ticket_categories.')->group(function () {
        Route::get('/', [TicketCategoryController::class, 'index'])->name('index')->middleware('permission:view_ticket_categories');
        Route::get('/data', [TicketCategoryController::class, 'getData'])->name('data')->middleware('permission:view_ticket_categories');
        Route::get('/{id}', [TicketCategoryController::class, 'show'])->name('show')->middleware('permission:view_ticket_categories');
        Route::post('/', [TicketCategoryController::class, 'store'])->name('store')->middleware('permission:create_ticket_categories');
        Route::get('/{id}/edit', [TicketCategoryController::class, 'edit'])->name('edit')->middleware('permission:edit_ticket_categories');
        Route::put('/{id}', [TicketCategoryController::class, 'update'])->name('update')->middleware('permission:edit_ticket_categories');
        Route::delete('/{id}', [TicketCategoryController::class, 'destroy'])->name('destroy')->middleware('permission:delete_ticket_categories');
    });

    Route::prefix('dashboard/admin/wallet-codes')->name('admin.wallet-codes.')->group(function () {
        Route::get('/', [AdminWalletCodeController::class, 'index'])->name('index')->middleware('permission:manage_wallet_codes');
        Route::get('/data', [AdminWalletCodeController::class, 'getCodesData'])->name('data')->middleware('permission:manage_wallet_codes');
        Route::post('/', [AdminWalletCodeController::class, 'store'])->name('store')->middleware('permission:manage_wallet_codes');
        Route::delete('/{id}', [AdminWalletCodeController::class, 'destroy'])->name('destroy')->middleware('permission:manage_wallet_codes');
        Route::get('/export', [AdminWalletCodeController::class, 'export'])->name('export')->middleware('permission:manage_wallet_codes');
    });

      Route::prefix('dashboard/admin/profile/wallets/show')->name('admin.profile.wallets.')->group(function () {
        Route::get('/{userId}', [WalletController::class, 'show'])->name('show')->middleware('permission:view_user_wallet');
        Route::get('/{userId}/data', [WalletController::class, 'getWalletTransactions'])->name('transactions.data')->middleware('permission:view_user_wallet');
      });
});

Route::put("/reade-notification", [NotificationController::class, 'readNotficion']);
Route::get('/notifications/paginated', [NotificationController::class, 'getPaginatedNotifications']);
Route::put('/update-device-token', [FcmTokenController::class, "store"]);
// Route::post('/set-lang', function (\Illuminate\Http\Request $request) {
//     session(['lang' => $request->lang]);
//     app()->setLocale($request->lang);
//     return response()->json(['status' => 'ok']);
// });
Route::post('/set-lang', [LanguageController::class, 'switchLang']);

require __DIR__ . '/auth.php';

Route::get('/test-payment-event', function () {
    $invoice = Invoice::latest()->first();
    event(new PaymentCompleted($invoice));
    return 'PaymentCompleted Event fired!';
});
