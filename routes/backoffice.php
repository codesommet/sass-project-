<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backoffice\AuthController;
use App\Http\Controllers\Backoffice\UserController;
use App\Http\Controllers\Backoffice\ProfileController;
use App\Http\Controllers\Backoffice\VehicleBrandController;
use App\Http\Controllers\Backoffice\VehicleModelController;
use App\Http\Controllers\Backoffice\VehicleController;
use App\Http\Controllers\Backoffice\AgencyController;
use App\Http\Controllers\Backoffice\AgentController;
use App\Http\Controllers\Backoffice\ClientController;
use App\Http\Controllers\Backoffice\AgencySubscriptionController;
use App\Http\Controllers\Backoffice\BookingController;
use App\Http\Controllers\Backoffice\RoleController;
use App\Http\Controllers\Backoffice\PermissionController;
use App\Http\Controllers\Backoffice\RolesPermissionsController;
use App\Http\Controllers\Backoffice\AgencySettingsController;
use App\Http\Controllers\Backoffice\Vehicles\VignetteController;
use App\Http\Controllers\Backoffice\Vehicles\InsuranceController;
use App\Http\Controllers\Backoffice\Vehicles\TechnicalCheckController;
use App\Http\Controllers\Backoffice\Vehicles\OilChangeController;
use App\Http\Controllers\Backoffice\Vehicles\ControlController;
use App\Http\Controllers\Backoffice\Vehicles\ControlItemController;
use App\Http\Controllers\Backoffice\RentalContractController;
use App\Http\Controllers\Backoffice\ContractClientController;
use App\Http\Controllers\Backoffice\InvoiceController;
use App\Http\Controllers\Backoffice\Finance\FinancialAccountController;
use App\Http\Controllers\Backoffice\Finance\TransactionCategoryController;
use App\Http\Controllers\Backoffice\Finance\FinancialTransactionController;
use App\Http\Controllers\Backoffice\InvoiceItemController;
use App\Http\Controllers\Backoffice\PaymentController;
use App\Http\Controllers\Backoffice\NotificationController;
use App\Http\Controllers\Backoffice\ContractPDFController;
use App\Http\Controllers\Backoffice\InvoicePDFController;
use App\Http\Controllers\Backoffice\VehicleCreditController;
use App\Http\Controllers\Backoffice\DashboardController;
use App\Http\Controllers\Backoffice\TrashController;

/*
|--------------------------------------------------------------------------
| Backoffice Routes
|--------------------------------------------------------------------------
*/

Route::prefix('backoffice')
    ->name('backoffice.')
    ->group(function () {

        // ==================== GUEST ROUTES ====================
        Route::middleware('guest:backoffice')->group(function () {
            Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
            Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
            Route::get('/login/demo', [AuthController::class, 'demoLogin'])->name('login.demo');
        });

        // ==================== AUTHENTICATED ROUTES ====================
        Route::middleware(['auth:backoffice'])->group(function () {

            // ----- PROFILE ROUTES (No permissions needed) -----
            Route::get('/profile/change-password', [ProfileController::class, 'showChangePassword'])
                ->name('profile.change-password');
            Route::put('/profile/change-password', [ProfileController::class, 'updatePassword'])
                ->name('profile.update-password');
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

            // ----- DASHBOARD -----
            Route::get('/dashboard', [DashboardController::class, 'index'])
                ->name('dashboard')
                ->middleware('can:dashboard.general.view,backoffice');
            Route::post('/dashboard/filter', [DashboardController::class, 'getFilteredStats'])
                ->name('dashboard.filter')
                ->middleware('can:dashboard.general.view,backoffice');

            // ==================== USERS ====================
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index')
                    ->middleware('can:users.general.view,backoffice');
                Route::get('/create', [UserController::class, 'create'])->name('create')
                    ->middleware('can:users.general.create,backoffice');
                Route::post('/', [UserController::class, 'store'])->name('store')
                    ->middleware('can:users.general.create,backoffice');
                Route::get('/{user}', [UserController::class, 'show'])->name('show')
                    ->middleware('can:users.general.view,backoffice');
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit')
                    ->middleware('can:users.general.edit,backoffice');
                Route::put('/{user}', [UserController::class, 'update'])->name('update')
                    ->middleware('can:users.general.edit,backoffice');
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy')
                    ->middleware('can:users.general.delete,backoffice');
            });

// ==================== CLIENTS ====================
Route::prefix('clients')->name('clients.')->group(function () {
    // Blacklist Routes - MUST come BEFORE the {client} routes
    Route::get('/blacklist', [ClientController::class, 'blacklistIndex'])
        ->name('blacklist.index')
        ->middleware('can:clients.general.view,backoffice');
    Route::post('/check-blacklist', [ClientController::class, 'checkBlacklist'])
        ->name('check-blacklist')
        ->middleware('can:clients.general.view,backoffice');

    // CRUD Routes - with {client} parameter (these come AFTER)
    Route::get('/', [ClientController::class, 'index'])->name('index')
        ->middleware('can:clients.general.view,backoffice');
    Route::get('/create', [ClientController::class, 'create'])->name('create')
        ->middleware('can:clients.general.create,backoffice');
    Route::post('/', [ClientController::class, 'store'])->name('store')
        ->middleware('can:clients.general.create,backoffice');
    Route::get('/{client}', [ClientController::class, 'show'])->name('show')
        ->middleware('can:clients.general.view,backoffice');
    Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit')
        ->middleware('can:clients.general.edit,backoffice');
    Route::put('/{client}', [ClientController::class, 'update'])->name('update')
        ->middleware('can:clients.general.edit,backoffice');
    Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy')
        ->middleware('can:clients.general.delete,backoffice');
    
    // These can stay here (they also have {client} but come after)
    Route::post('/{client}/blacklist', [ClientController::class, 'addToBlacklist'])
        ->name('add-to-blacklist')
        ->middleware('can:clients.general.edit,backoffice');
    Route::delete('/{client}/unblacklist', [ClientController::class, 'removeFromBlacklist'])
        ->name('remove-from-blacklist')
        ->middleware('can:clients.general.edit,backoffice');
});

            // ==================== AGENCIES ====================
            Route::prefix('agencies')->name('agencies.')->group(function () {
                Route::get('/', [AgencyController::class, 'index'])->name('index')
                    ->middleware('can:agencies.general.view,backoffice');
                Route::get('/create', [AgencyController::class, 'create'])->name('create')
                    ->middleware('can:agencies.general.create,backoffice');
                Route::post('/', [AgencyController::class, 'store'])->name('store')
                    ->middleware('can:agencies.general.create,backoffice');
                Route::get('/{agency}', [AgencyController::class, 'show'])->name('show')
                    ->middleware('can:agencies.general.view,backoffice');
                Route::get('/{agency}/edit', [AgencyController::class, 'edit'])->name('edit')
                    ->middleware('can:agencies.general.edit,backoffice');
                Route::put('/{agency}', [AgencyController::class, 'update'])->name('update')
                    ->middleware('can:agencies.general.edit,backoffice');
                Route::delete('/{agency}', [AgencyController::class, 'destroy'])->name('destroy')
                    ->middleware('can:agencies.general.delete,backoffice');

                // ==================== AGENCY SETTINGS (ALL IN ONE PLACE) ====================
                Route::prefix('{agency}/settings')->name('settings.')->group(function () {
                    // GET routes
                    Route::get('/', [AgencySettingsController::class, 'edit'])->name('edit')
                        ->middleware('can:agencies.general.edit,backoffice');
                    Route::get('/profile', [AgencyController::class, 'profile'])->name('profile')
                        ->middleware('can:agencies.general.edit,backoffice');
                    Route::get('/notifications', [AgencySettingsController::class, 'notifications'])->name('notifications')
                        ->middleware('can:agencies.general.edit,backoffice');
                    Route::get('/invoice-template', [AgencySettingsController::class, 'invoiceTemplate'])->name('invoice-template')
                        ->middleware('can:agencies.general.edit,backoffice');
                    Route::get('/company', [AgencySettingsController::class, 'company'])->name('company')
                        ->middleware('can:agencies.general.edit,backoffice');
                    Route::get('/signatures', [AgencySettingsController::class, 'signatures'])->name('signatures')
                        ->middleware('can:agencies.general.edit,backoffice');
                    
                    // NEW WEBSITE SETTINGS
                    Route::get('/website', [AgencySettingsController::class, 'website'])->name('website')
                        ->middleware('can:agencies.general.edit,backoffice');
                    Route::post('/website', [AgencySettingsController::class, 'updateWebsite'])->name('website.update')
                        ->middleware('can:agencies.general.edit,backoffice');
                    
                    // NEW INVOICE SETTINGS
                    Route::get('/invoice-settings', [AgencySettingsController::class, 'invoiceSettings'])->name('invoice-settings')
                        ->middleware('can:agencies.general.edit,backoffice');
                    Route::post('/invoice-settings', [AgencySettingsController::class, 'updateInvoiceSettings'])->name('invoice-settings.update')
                        ->middleware('can:agencies.general.edit,backoffice');
                    
                    // UPDATE routes
                    Route::patch('/', [AgencySettingsController::class, 'update'])->name('update')
                        ->middleware('can:agencies.general.edit,backoffice');
                    Route::post('/company', [AgencySettingsController::class, 'updateCompany'])->name('update.company')
                        ->middleware('can:agencies.general.edit,backoffice');
                    Route::post('/profile', [AgencyController::class, 'updateProfile'])->name('update.profile')
                        ->middleware('can:agencies.general.edit,backoffice');
                    
                    // DELETE routes
                    Route::delete('/delete-logo', [AgencySettingsController::class, 'deleteLogo'])->name('delete-logo')
                        ->middleware('can:agencies.general.edit,backoffice');
                    Route::delete('/delete-signature', [AgencySettingsController::class, 'deleteSignature'])->name('delete-signature')
                        ->middleware('can:agencies.general.edit,backoffice');
                });
            });

            // ==================== AGENTS ====================
            Route::prefix('agents')->name('agents.')->group(function () {
                Route::get('/', [AgentController::class, 'index'])->name('index')
                    ->middleware('can:agents.general.view,backoffice');
                Route::get('/create', [AgentController::class, 'create'])->name('create')
                    ->middleware('can:agents.general.create,backoffice');
                Route::post('/', [AgentController::class, 'store'])->name('store')
                    ->middleware('can:agents.general.create,backoffice');
                Route::get('/{agent}', [AgentController::class, 'show'])->name('show')
                    ->middleware('can:agents.general.view,backoffice');
                Route::get('/{agent}/edit', [AgentController::class, 'edit'])->name('edit')
                    ->middleware('can:agents.general.edit,backoffice');
                Route::put('/{agent}', [AgentController::class, 'update'])->name('update')
                    ->middleware('can:agents.general.edit,backoffice');
                Route::delete('/{agent}', [AgentController::class, 'destroy'])->name('destroy')
                    ->middleware('can:agents.general.delete,backoffice');
            });

            // ==================== AGENCY SUBSCRIPTIONS ====================
            Route::prefix('agency-subscriptions')->name('agency-subscriptions.')->group(function () {
                Route::get('/', [AgencySubscriptionController::class, 'index'])->name('index')
                    ->middleware('can:agency-subscriptions.general.view,backoffice');
                Route::get('/create', [AgencySubscriptionController::class, 'create'])->name('create')
                    ->middleware('can:agency-subscriptions.general.create,backoffice');
                Route::post('/', [AgencySubscriptionController::class, 'store'])->name('store')
                    ->middleware('can:agency-subscriptions.general.create,backoffice');
                Route::get('/{agencySubscription}', [AgencySubscriptionController::class, 'show'])->name('show')
                    ->middleware('can:agency-subscriptions.general.view,backoffice');
                Route::get('/{agencySubscription}/edit', [AgencySubscriptionController::class, 'edit'])->name('edit')
                    ->middleware('can:agency-subscriptions.general.edit,backoffice');
                Route::put('/{agencySubscription}', [AgencySubscriptionController::class, 'update'])->name('update')
                    ->middleware('can:agency-subscriptions.general.edit,backoffice');
                Route::delete('/{agencySubscription}', [AgencySubscriptionController::class, 'destroy'])->name('destroy')
                    ->middleware('can:agency-subscriptions.general.delete,backoffice');
            });

            Route::get('my-subscription', [AgencySubscriptionController::class, 'mySubscription'])
                ->name('my-subscription')
                ->middleware('can:agency-subscriptions.general.view,backoffice');

            // ==================== ROLES & PERMISSIONS ====================
            Route::prefix('roles-permissions')->name('roles-permissions.')->group(function () {
                Route::get('/', [RolesPermissionsController::class, 'indexRoles'])->name('roles')
                    ->middleware('can:roles-permissions.general.view,backoffice');
                Route::get('/{role}/permissions', [RolesPermissionsController::class, 'showPermissions'])->name('permissions')
                    ->middleware('can:roles-permissions.general.view,backoffice');
                Route::put('/{role}/permissions', [RolesPermissionsController::class, 'updatePermissions'])->name('permissions.update')
                    ->middleware('can:roles-permissions.general.edit,backoffice');
            });

            Route::prefix('roles')->name('roles.')->group(function () {
                Route::post('/', [RoleController::class, 'store'])->name('store')
                    ->middleware('can:roles-permissions.general.create,backoffice');
                Route::put('/{role}', [RoleController::class, 'update'])->name('update')
                    ->middleware('can:roles-permissions.general.edit,backoffice');
                Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy')
                    ->middleware('can:roles-permissions.general.delete,backoffice');
            });

            Route::prefix('permissions')->name('permissions.')->group(function () {
                Route::post('/', [PermissionController::class, 'store'])->name('store')
                    ->middleware('can:roles-permissions.general.create,backoffice');
                Route::put('/{permission}', [PermissionController::class, 'update'])->name('update')
                    ->middleware('can:roles-permissions.general.edit,backoffice');
                Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy')
                    ->middleware('can:roles-permissions.general.delete,backoffice');
            });

            // ==================== VEHICLE BRANDS ====================
            Route::prefix('vehicle-brands')->name('vehicle-brands.')->group(function () {
                Route::get('/', [VehicleBrandController::class, 'index'])->name('index')
                    ->middleware('can:vehicle-brands.general.view,backoffice');
                Route::get('/create', [VehicleBrandController::class, 'create'])->name('create')
                    ->middleware('can:vehicle-brands.general.create,backoffice');
                Route::post('/', [VehicleBrandController::class, 'store'])->name('store')
                    ->middleware('can:vehicle-brands.general.create,backoffice');
                Route::get('/{vehicleBrand}', [VehicleBrandController::class, 'show'])->name('show')
                    ->middleware('can:vehicle-brands.general.view,backoffice');
                Route::get('/{vehicleBrand}/edit', [VehicleBrandController::class, 'edit'])->name('edit')
                    ->middleware('can:vehicle-brands.general.edit,backoffice');
                Route::put('/{vehicleBrand}', [VehicleBrandController::class, 'update'])->name('update')
                    ->middleware('can:vehicle-brands.general.edit,backoffice');
                Route::delete('/{vehicleBrand}', [VehicleBrandController::class, 'destroy'])->name('destroy')
                    ->middleware('can:vehicle-brands.general.delete,backoffice');
            });

            // ==================== VEHICLE MODELS ====================
            Route::prefix('vehicle-models')->name('vehicle-models.')->group(function () {
                Route::get('/', [VehicleModelController::class, 'index'])->name('index')
                    ->middleware('can:vehicle-models.general.view,backoffice');
                Route::get('/create', [VehicleModelController::class, 'create'])->name('create')
                    ->middleware('can:vehicle-models.general.create,backoffice');
                Route::post('/', [VehicleModelController::class, 'store'])->name('store')
                    ->middleware('can:vehicle-models.general.create,backoffice');
                Route::get('/{vehicleModel}', [VehicleModelController::class, 'show'])->name('show')
                    ->middleware('can:vehicle-models.general.view,backoffice');
                Route::get('/{vehicleModel}/edit', [VehicleModelController::class, 'edit'])->name('edit')
                    ->middleware('can:vehicle-models.general.edit,backoffice');
                Route::put('/{vehicleModel}', [VehicleModelController::class, 'update'])->name('update')
                    ->middleware('can:vehicle-models.general.edit,backoffice');
                Route::delete('/{vehicleModel}', [VehicleModelController::class, 'destroy'])->name('destroy')
                    ->middleware('can:vehicle-models.general.delete,backoffice');
            });

            // ==================== VEHICLES ====================
            Route::prefix('vehicles')->name('vehicles.')->group(function () {
                Route::get('/', [VehicleController::class, 'index'])->name('index')
                    ->middleware('can:vehicles.general.view,backoffice');
                Route::delete('/bulk-destroy', [VehicleController::class, 'bulkDestroy'])->name('bulkDestroy')
                    ->middleware('can:vehicles.general.delete,backoffice');
                Route::post('/check-duplicate', [VehicleController::class, 'checkDuplicate'])->name('check-duplicate')
                    ->middleware('can:vehicles.general.create,backoffice');
                Route::get('/create', [VehicleController::class, 'create'])->name('create')
                    ->middleware('can:vehicles.general.create,backoffice');
                Route::post('/', [VehicleController::class, 'store'])->name('store')
                    ->middleware('can:vehicles.general.create,backoffice');
                Route::get('/{vehicle}', [VehicleController::class, 'show'])->name('show')
                    ->middleware('can:vehicles.general.view,backoffice');
                Route::get('/{vehicle}/edit', [VehicleController::class, 'edit'])->name('edit')
                    ->middleware('can:vehicles.general.edit,backoffice');
                Route::put('/{vehicle}', [VehicleController::class, 'update'])->name('update')
                    ->middleware('can:vehicles.general.edit,backoffice');
                Route::delete('/{vehicle}', [VehicleController::class, 'destroy'])->name('destroy')
                    ->middleware('can:vehicles.general.delete,backoffice');

                // Vehicle Documents
                Route::prefix('{vehicle}/vignettes')->name('vignettes.')->group(function () {
                    Route::get('/', [VignetteController::class, 'index'])->name('index')
                        ->middleware('can:vehicle-vignettes.general.view,backoffice');
                    Route::get('/create', [VignetteController::class, 'create'])->name('create')
                        ->middleware('can:vehicle-vignettes.general.create,backoffice');
                    Route::post('/', [VignetteController::class, 'store'])->name('store')
                        ->middleware('can:vehicle-vignettes.general.create,backoffice');
                    Route::get('/{vignette}', [VignetteController::class, 'show'])->name('show')
                        ->middleware('can:vehicle-vignettes.general.view,backoffice');
                    Route::get('/{vignette}/edit', [VignetteController::class, 'edit'])->name('edit')
                        ->middleware('can:vehicle-vignettes.general.edit,backoffice');
                    Route::put('/{vignette}', [VignetteController::class, 'update'])->name('update')
                        ->middleware('can:vehicle-vignettes.general.edit,backoffice');
                    Route::delete('/{vignette}', [VignetteController::class, 'destroy'])->name('destroy')
                        ->middleware('can:vehicle-vignettes.general.delete,backoffice');
                });

                Route::prefix('{vehicle}/insurances')->name('insurances.')->group(function () {
                    Route::get('/', [InsuranceController::class, 'index'])->name('index')
                        ->middleware('can:vehicle-insurances.general.view,backoffice');
                    Route::get('/create', [InsuranceController::class, 'create'])->name('create')
                        ->middleware('can:vehicle-insurances.general.create,backoffice');
                    Route::post('/', [InsuranceController::class, 'store'])->name('store')
                        ->middleware('can:vehicle-insurances.general.create,backoffice');
                    Route::get('/{insurance}', [InsuranceController::class, 'show'])->name('show')
                        ->middleware('can:vehicle-insurances.general.view,backoffice');
                    Route::get('/{insurance}/edit', [InsuranceController::class, 'edit'])->name('edit')
                        ->middleware('can:vehicle-insurances.general.edit,backoffice');
                    Route::put('/{insurance}', [InsuranceController::class, 'update'])->name('update')
                        ->middleware('can:vehicle-insurances.general.edit,backoffice');
                    Route::delete('/{insurance}', [InsuranceController::class, 'destroy'])->name('destroy')
                        ->middleware('can:vehicle-insurances.general.delete,backoffice');
                });

                Route::prefix('{vehicle}/oil-changes')->name('oil-changes.')->group(function () {
                    Route::get('/', [OilChangeController::class, 'index'])->name('index')
                        ->middleware('can:vehicle-oil-changes.general.view,backoffice');
                    Route::get('/create', [OilChangeController::class, 'create'])->name('create')
                        ->middleware('can:vehicle-oil-changes.general.create,backoffice');
                    Route::post('/', [OilChangeController::class, 'store'])->name('store')
                        ->middleware('can:vehicle-oil-changes.general.create,backoffice');
                    Route::get('/{oilChange}', [OilChangeController::class, 'show'])->name('show')
                        ->middleware('can:vehicle-oil-changes.general.view,backoffice');
                    Route::get('/{oilChange}/edit', [OilChangeController::class, 'edit'])->name('edit')
                        ->middleware('can:vehicle-oil-changes.general.edit,backoffice');
                    Route::put('/{oilChange}', [OilChangeController::class, 'update'])->name('update')
                        ->middleware('can:vehicle-oil-changes.general.edit,backoffice');
                    Route::delete('/{oilChange}', [OilChangeController::class, 'destroy'])->name('destroy')
                        ->middleware('can:vehicle-oil-changes.general.delete,backoffice');
                });

                Route::prefix('{vehicle}/technical-checks')->name('technical-checks.')->group(function () {
                    Route::get('/', [TechnicalCheckController::class, 'index'])->name('index')
                        ->middleware('can:vehicle-technical-checks.general.view,backoffice');
                    Route::get('/create', [TechnicalCheckController::class, 'create'])->name('create')
                        ->middleware('can:vehicle-technical-checks.general.create,backoffice');
                    Route::post('/', [TechnicalCheckController::class, 'store'])->name('store')
                        ->middleware('can:vehicle-technical-checks.general.create,backoffice');
                    Route::get('/{technicalCheck}', [TechnicalCheckController::class, 'show'])->name('show')
                        ->middleware('can:vehicle-technical-checks.general.view,backoffice');
                    Route::get('/{technicalCheck}/edit', [TechnicalCheckController::class, 'edit'])->name('edit')
                        ->middleware('can:vehicle-technical-checks.general.edit,backoffice');
                    Route::put('/{technicalCheck}', [TechnicalCheckController::class, 'update'])->name('update')
                        ->middleware('can:vehicle-technical-checks.general.edit,backoffice');
                    Route::delete('/{technicalCheck}', [TechnicalCheckController::class, 'destroy'])->name('destroy')
                        ->middleware('can:vehicle-technical-checks.general.delete,backoffice');
                });

                Route::prefix('{vehicle}/controls')->name('controls.')->group(function () {
                    Route::get('/', [ControlController::class, 'index'])->name('index')
                        ->middleware('can:vehicle-controls.general.view,backoffice');
                    Route::get('/create', [ControlController::class, 'create'])->name('create')
                        ->middleware('can:vehicle-controls.general.create,backoffice');
                    Route::post('/', [ControlController::class, 'store'])->name('store')
                        ->middleware('can:vehicle-controls.general.create,backoffice');
                    Route::get('/{control}', [ControlController::class, 'show'])->name('show')
                        ->middleware('can:vehicle-controls.general.view,backoffice');
                    Route::get('/{control}/edit', [ControlController::class, 'edit'])->name('edit')
                        ->middleware('can:vehicle-controls.general.edit,backoffice');
                    Route::put('/{control}', [ControlController::class, 'update'])->name('update')
                        ->middleware('can:vehicle-controls.general.edit,backoffice');
                    Route::delete('/{control}', [ControlController::class, 'destroy'])->name('destroy')
                        ->middleware('can:vehicle-controls.general.delete,backoffice');
                        
                });
            });

            // ==================== CONTROLS (Standalone) ====================
            Route::prefix('controls')->name('controls.')->group(function () {
                Route::get('/', [ControlController::class, 'index'])->name('index')
                    ->middleware('can:vehicle-controls.general.view,backoffice');
                Route::get('/create', [ControlController::class, 'create'])->name('create')
                    ->middleware('can:vehicle-controls.general.create,backoffice');
                Route::post('/', [ControlController::class, 'store'])->name('store')
                    ->middleware('can:vehicle-controls.general.create,backoffice');
                Route::get('/{control}', [ControlController::class, 'show'])->name('show')
                    ->middleware('can:vehicle-controls.general.view,backoffice');
                Route::get('/{control}/edit', [ControlController::class, 'edit'])->name('edit')
                    ->middleware('can:vehicle-controls.general.edit,backoffice');
                Route::put('/{control}', [ControlController::class, 'update'])->name('update')
                    ->middleware('can:vehicle-controls.general.edit,backoffice');
                Route::delete('/{control}', [ControlController::class, 'destroy'])->name('destroy')
                    ->middleware('can:vehicle-controls.general.delete,backoffice');
            });

            // ==================== CONTROL ITEMS ====================
            Route::prefix('control-items')->name('control-items.')->group(function () {
                Route::get('/', [ControlItemController::class, 'index'])->name('index')
                    ->middleware('can:vehicle-control-items.general.view,backoffice');
                Route::get('/create', [ControlItemController::class, 'create'])->name('create')
                    ->middleware('can:vehicle-control-items.general.create,backoffice');
                Route::post('/', [ControlItemController::class, 'store'])->name('store')
                    ->middleware('can:vehicle-control-items.general.create,backoffice');
                Route::get('/{item}', [ControlItemController::class, 'show'])->name('show')
                    ->middleware('can:vehicle-control-items.general.view,backoffice');
                Route::get('/{item}/edit', [ControlItemController::class, 'edit'])->name('edit')
                    ->middleware('can:vehicle-control-items.general.edit,backoffice');
                Route::put('/{item}', [ControlItemController::class, 'update'])->name('update')
                    ->middleware('can:vehicle-control-items.general.edit,backoffice');
                Route::delete('/{item}', [ControlItemController::class, 'destroy'])->name('destroy')
                    ->middleware('can:vehicle-control-items.general.delete,backoffice');
            });

            // ==================== RENTAL CONTRACTS ====================
            Route::prefix('rental-contracts')->name('rental-contracts.')->group(function () {
                Route::get('/', [RentalContractController::class, 'index'])->name('index')
                    ->middleware('can:rental-contracts.general.view,backoffice');
                Route::get('/create', [RentalContractController::class, 'create'])->name('create')
                    ->middleware('can:rental-contracts.general.create,backoffice');
                Route::post('/', [RentalContractController::class, 'store'])->name('store')
                    ->middleware('can:rental-contracts.general.create,backoffice');
                Route::get('/{rentalContract}', [RentalContractController::class, 'show'])->name('show')
                    ->middleware('can:rental-contracts.general.view,backoffice');
                Route::get('/{rentalContract}/edit', [RentalContractController::class, 'edit'])->name('edit')
                    ->middleware('can:rental-contracts.general.edit,backoffice');
                Route::put('/{rentalContract}', [RentalContractController::class, 'update'])->name('update')
                    ->middleware('can:rental-contracts.general.edit,backoffice');
                Route::delete('/{rentalContract}', [RentalContractController::class, 'destroy'])->name('destroy')
                    ->middleware('can:rental-contracts.general.delete,backoffice');
                Route::post('/{rentalContract}/status', [RentalContractController::class, 'updateStatus'])->name('status')
                    ->middleware('can:rental-contracts.general.edit,backoffice');

                Route::prefix('{rentalContract}/clients')->name('clients.')->group(function () {
                    Route::get('/', [ContractClientController::class, 'index'])->name('index')
                        ->middleware('can:contract-clients.general.view,backoffice');
                    Route::get('/create', [ContractClientController::class, 'create'])->name('create')
                        ->middleware('can:contract-clients.general.create,backoffice');
                    Route::post('/', [ContractClientController::class, 'store'])->name('store')
                        ->middleware('can:contract-clients.general.create,backoffice');
                    Route::get('/{contractClient}/edit', [ContractClientController::class, 'edit'])->name('edit')
                        ->middleware('can:contract-clients.general.edit,backoffice');
                    Route::put('/{contractClient}', [ContractClientController::class, 'update'])->name('update')
                        ->middleware('can:contract-clients.general.edit,backoffice');
                    Route::delete('/{contractClient}', [ContractClientController::class, 'destroy'])->name('destroy')
                        ->middleware('can:contract-clients.general.delete,backoffice');
                });
            });

            // ==================== CONTRACT CLIENTS ====================
            Route::prefix('contract-clients')->name('contract-clients.')->group(function () {
                Route::get('/', [ContractClientController::class, 'index'])->name('index')
                    ->middleware('can:contract-clients.general.view,backoffice');
                Route::get('/create', [ContractClientController::class, 'create'])->name('create')
                    ->middleware('can:contract-clients.general.create,backoffice');
                Route::post('/', [ContractClientController::class, 'store'])->name('store')
                    ->middleware('can:contract-clients.general.create,backoffice');
                Route::get('/{contractClient}', [ContractClientController::class, 'show'])->name('show')
                    ->middleware('can:contract-clients.general.view,backoffice');
                Route::get('/{contractClient}/edit', [ContractClientController::class, 'edit'])->name('edit')
                    ->middleware('can:contract-clients.general.edit,backoffice');
                Route::put('/{contractClient}', [ContractClientController::class, 'update'])->name('update')
                    ->middleware('can:contract-clients.general.edit,backoffice');
                Route::delete('/{contractClient}', [ContractClientController::class, 'destroy'])->name('destroy')
                    ->middleware('can:contract-clients.general.delete,backoffice');
            });

            // ==================== BOOKINGS ====================
            Route::prefix('bookings')->name('bookings.')->group(function () {
                Route::get('/', [BookingController::class, 'index'])->name('index')
                    ->middleware('can:bookings.general.view,backoffice');
                Route::get('/calendar/view', [BookingController::class, 'calendar'])->name('calendar')
                    ->middleware('can:bookings.general.view,backoffice');
                Route::get('/create', [BookingController::class, 'create'])->name('create')
                    ->middleware('can:bookings.general.create,backoffice');
                Route::post('/', [BookingController::class, 'store'])->name('store')
                    ->middleware('can:bookings.general.create,backoffice');
                Route::get('/{booking}', [BookingController::class, 'show'])->name('show')
                    ->middleware('can:bookings.general.view,backoffice');
                Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('edit')
                    ->middleware('can:bookings.general.edit,backoffice');
                Route::put('/{booking}', [BookingController::class, 'update'])->name('update')
                    ->middleware('can:bookings.general.edit,backoffice');
                Route::delete('/{booking}', [BookingController::class, 'destroy'])->name('destroy')
                    ->middleware('can:bookings.general.delete,backoffice');
                Route::post('/{booking}/status', [BookingController::class, 'updateStatus'])->name('status')
                    ->middleware('can:bookings.general.edit,backoffice');
                Route::post('/{booking}/convert-to-contract', [BookingController::class, 'convertToContract'])->name('convert-to-contract')
                    ->middleware('can:bookings.general.edit,backoffice');
            });

            // ==================== FINANCE ====================
            Route::prefix('finance/accounts')->name('finance.accounts.')->group(function () {
                Route::get('/', [FinancialAccountController::class, 'index'])->name('index')
                    ->middleware('can:financial-accounts.general.view,backoffice');
                Route::get('/create', [FinancialAccountController::class, 'create'])->name('create')
                    ->middleware('can:financial-accounts.general.create,backoffice');
                Route::post('/', [FinancialAccountController::class, 'store'])->name('store')
                    ->middleware('can:financial-accounts.general.create,backoffice');
                Route::get('/{financialAccount}', [FinancialAccountController::class, 'show'])->name('show')
                    ->middleware('can:financial-accounts.general.view,backoffice');
                Route::get('/{financialAccount}/edit', [FinancialAccountController::class, 'edit'])->name('edit')
                    ->middleware('can:financial-accounts.general.edit,backoffice');
                Route::put('/{financialAccount}', [FinancialAccountController::class, 'update'])->name('update')
                    ->middleware('can:financial-accounts.general.edit,backoffice');
                Route::delete('/{financialAccount}', [FinancialAccountController::class, 'destroy'])->name('destroy')
                    ->middleware('can:financial-accounts.general.delete,backoffice');
            });

            Route::prefix('finance/categories')->name('finance.categories.')->group(function () {
                Route::get('/', [TransactionCategoryController::class, 'index'])->name('index')
                    ->middleware('can:transaction-categories.general.view,backoffice');
                Route::get('/create', [TransactionCategoryController::class, 'create'])->name('create')
                    ->middleware('can:transaction-categories.general.create,backoffice');
                Route::post('/', [TransactionCategoryController::class, 'store'])->name('store')
                    ->middleware('can:transaction-categories.general.create,backoffice');
                Route::get('/{transactionCategory}', [TransactionCategoryController::class, 'show'])->name('show')
                    ->middleware('can:transaction-categories.general.view,backoffice');
                Route::get('/{transactionCategory}/edit', [TransactionCategoryController::class, 'edit'])->name('edit')
                    ->middleware('can:transaction-categories.general.edit,backoffice');
                Route::put('/{transactionCategory}', [TransactionCategoryController::class, 'update'])->name('update')
                    ->middleware('can:transaction-categories.general.edit,backoffice');
                Route::delete('/{transactionCategory}', [TransactionCategoryController::class, 'destroy'])->name('destroy')
                    ->middleware('can:transaction-categories.general.delete,backoffice');
            });

            Route::prefix('finance/transactions')->name('finance.transactions.')->group(function () {
                Route::get('/', [FinancialTransactionController::class, 'index'])->name('index')
                    ->middleware('can:financial-transactions.general.view,backoffice');
                Route::get('/summary/data', [FinancialTransactionController::class, 'summary'])->name('summary')
                    ->middleware('can:financial-transactions.general.view,backoffice');
                Route::get('/create', [FinancialTransactionController::class, 'create'])->name('create')
                    ->middleware('can:financial-transactions.general.create,backoffice');
                Route::post('/', [FinancialTransactionController::class, 'store'])->name('store')
                    ->middleware('can:financial-transactions.general.create,backoffice');
                Route::get('/{financialTransaction}', [FinancialTransactionController::class, 'show'])->name('show')
                    ->middleware('can:financial-transactions.general.view,backoffice');
                Route::get('/{financialTransaction}/edit', [FinancialTransactionController::class, 'edit'])->name('edit')
                    ->middleware('can:financial-transactions.general.edit,backoffice');
                Route::put('/{financialTransaction}', [FinancialTransactionController::class, 'update'])->name('update')
                    ->middleware('can:financial-transactions.general.edit,backoffice');
                Route::delete('/{financialTransaction}', [FinancialTransactionController::class, 'destroy'])->name('destroy')
                    ->middleware('can:financial-transactions.general.delete,backoffice');
            });

            // ==================== INVOICES ====================
            Route::prefix('invoices')->name('invoices.')->group(function () {
                Route::get('/', [InvoiceController::class, 'index'])->name('index')
                    ->middleware('can:invoices.general.view,backoffice');
                Route::get('/create', [InvoiceController::class, 'create'])->name('create')
                    ->middleware('can:invoices.general.create,backoffice');
                Route::post('/', [InvoiceController::class, 'store'])->name('store')
                    ->middleware('can:invoices.general.create,backoffice');
                Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show')
                    ->middleware('can:invoices.general.view,backoffice');
                Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit')
                    ->middleware('can:invoices.general.edit,backoffice');
                Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update')
                    ->middleware('can:invoices.general.edit,backoffice');
                Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy')
                    ->middleware('can:invoices.general.delete,backoffice');
                Route::post('/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('status')
                    ->middleware('can:invoices.general.edit,backoffice');
            });

            Route::prefix('invoices/pdf')->name('invoices.pdf.')->group(function () {
                Route::get('/{id}', [InvoicePDFController::class, 'exportSingle'])->name('single')
                    ->middleware('can:invoices.general.view,backoffice');
                Route::get('/{id}/view', [InvoicePDFController::class, 'view'])->name('view')
                    ->middleware('can:invoices.general.view,backoffice');
                Route::post('/export-multiple', [InvoicePDFController::class, 'exportMultiple'])->name('multiple')
                    ->middleware('can:invoices.general.view,backoffice');
            });

            // ==================== INVOICE ITEMS ====================
            Route::prefix('invoice-items')->name('invoice-items.')->group(function () {
                Route::get('/', [InvoiceItemController::class, 'index'])->name('index')
                    ->middleware('can:invoice-items.general.view,backoffice');
                Route::get('/create', [InvoiceItemController::class, 'create'])->name('create')
                    ->middleware('can:invoice-items.general.create,backoffice');
                Route::post('/', [InvoiceItemController::class, 'store'])->name('store')
                    ->middleware('can:invoice-items.general.create,backoffice');
                Route::get('/{invoiceItem}', [InvoiceItemController::class, 'show'])->name('show')
                    ->middleware('can:invoice-items.general.view,backoffice');
                Route::get('/{invoiceItem}/edit', [InvoiceItemController::class, 'edit'])->name('edit')
                    ->middleware('can:invoice-items.general.edit,backoffice');
                Route::put('/{invoiceItem}', [InvoiceItemController::class, 'update'])->name('update')
                    ->middleware('can:invoice-items.general.edit,backoffice');
                Route::delete('/{invoiceItem}', [InvoiceItemController::class, 'destroy'])->name('destroy')
                    ->middleware('can:invoice-items.general.delete,backoffice');
            });

            // ==================== PAYMENTS ====================
            Route::prefix('payments')->name('payments.')->group(function () {
                Route::get('/', [PaymentController::class, 'index'])->name('index')
                    ->middleware('can:payments.general.view,backoffice');
                Route::get('/create', [PaymentController::class, 'create'])->name('create')
                    ->middleware('can:payments.general.create,backoffice');
                Route::post('/', [PaymentController::class, 'store'])->name('store')
                    ->middleware('can:payments.general.create,backoffice');
                Route::get('/{payment}', [PaymentController::class, 'show'])->name('show')
                    ->middleware('can:payments.general.view,backoffice');
                Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('edit')
                    ->middleware('can:payments.general.edit,backoffice');
                Route::put('/{payment}', [PaymentController::class, 'update'])->name('update')
                    ->middleware('can:payments.general.edit,backoffice');
                Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy')
                    ->middleware('can:payments.general.delete,backoffice');
                Route::post('/{payment}/status', [PaymentController::class, 'updateStatus'])->name('status')
                    ->middleware('can:payments.general.edit,backoffice');
            });

            // ==================== VEHICLE CREDITS ====================
            Route::prefix('vehicle-credits')->name('vehicle-credits.')->group(function () {
                Route::get('/', [VehicleCreditController::class, 'index'])->name('index')
                    ->middleware('can:vehicle-credits.general.view,backoffice');
                Route::get('/dashboard', [VehicleCreditController::class, 'dashboard'])->name('dashboard')
                    ->middleware('can:vehicle-credits.general.view,backoffice');
                Route::get('/create', [VehicleCreditController::class, 'create'])->name('create')
                    ->middleware('can:vehicle-credits.general.create,backoffice');
                Route::post('/', [VehicleCreditController::class, 'store'])->name('store')
                    ->middleware('can:vehicle-credits.general.create,backoffice');
                Route::get('/{vehicleCredit}', [VehicleCreditController::class, 'show'])->name('show')
                    ->middleware('can:vehicle-credits.general.view,backoffice');
                Route::get('/{vehicleCredit}/edit', [VehicleCreditController::class, 'edit'])->name('edit')
                    ->middleware('can:vehicle-credits.general.edit,backoffice');
                Route::put('/{vehicleCredit}', [VehicleCreditController::class, 'update'])->name('update')
                    ->middleware('can:vehicle-credits.general.edit,backoffice');
                Route::delete('/{vehicleCredit}', [VehicleCreditController::class, 'destroy'])->name('destroy')
                    ->middleware('can:vehicle-credits.general.delete,backoffice');
                Route::post('/{vehicleCredit}/record-payment', [VehicleCreditController::class, 'recordPayment'])->name('record-payment')
                    ->middleware('can:vehicle-credits.general.edit,backoffice');
                Route::get('/{vehicleCredit}/payment-schedule', [VehicleCreditController::class, 'getPaymentSchedule'])->name('payment-schedule')
                    ->middleware('can:vehicle-credits.general.view,backoffice');
            });

            // ==================== GLOBAL VEHICLE DOCUMENTS ====================
            Route::prefix('vehicle-documents')->name('vehicle-documents.')->group(function () {
                Route::get('/vignettes', [VignetteController::class, 'globalIndex'])->name('vignettes.index')
                    ->middleware('can:vehicle-vignettes.general.view,backoffice');
                Route::get('/vignettes/create', [VignetteController::class, 'create'])->name('vignettes.create')
                    ->middleware('can:vehicle-vignettes.general.create,backoffice');
                Route::post('/vignettes', [VignetteController::class, 'store'])->name('vignettes.store')
                    ->middleware('can:vehicle-vignettes.general.create,backoffice');
                Route::get('/insurances', [InsuranceController::class, 'globalIndex'])->name('insurances.index')
                    ->middleware('can:vehicle-insurances.general.view,backoffice');
                Route::get('/insurances/create', [InsuranceController::class, 'create'])->name('insurances.create')
                    ->middleware('can:vehicle-insurances.general.create,backoffice');
                Route::post('/insurances', [InsuranceController::class, 'store'])->name('insurances.store')
                    ->middleware('can:vehicle-insurances.general.create,backoffice');
                Route::get('/oil-changes', [OilChangeController::class, 'globalIndex'])->name('oil-changes.index')
                    ->middleware('can:vehicle-oil-changes.general.view,backoffice');
                Route::get('/oil-changes/create', [OilChangeController::class, 'create'])->name('oil-changes.create')
                    ->middleware('can:vehicle-oil-changes.general.create,backoffice');
                Route::post('/oil-changes', [OilChangeController::class, 'store'])->name('oil-changes.store')
                    ->middleware('can:vehicle-oil-changes.general.create,backoffice');
                Route::get('/technical-checks', [TechnicalCheckController::class, 'globalIndex'])->name('technical-checks.index')
                    ->middleware('can:vehicle-technical-checks.general.view,backoffice');
                Route::get('/technical-checks/create', [TechnicalCheckController::class, 'create'])->name('technical-checks.create')
                    ->middleware('can:vehicle-technical-checks.general.create,backoffice');
                Route::post('/technical-checks', [TechnicalCheckController::class, 'store'])->name('technical-checks.store')
                    ->middleware('can:vehicle-technical-checks.general.create,backoffice');
            });

            // ==================== CONTRACT PDF ====================
            Route::prefix('contracts/pdf')->name('contracts.pdf.')->group(function () {
                Route::get('/{id}', [ContractPDFController::class, 'exportSingle'])->name('single')
                    ->middleware('can:rental-contracts.general.view,backoffice');
                Route::get('/{id}/view', [ContractPDFController::class, 'view'])->name('view')
                    ->middleware('can:rental-contracts.general.view,backoffice');
                Route::post('/export-multiple', [ContractPDFController::class, 'exportMultiple'])->name('multiple')
                    ->middleware('can:rental-contracts.general.view,backoffice');
            });

            // ==================== API ROUTES ====================
            Route::get('/api/control-items/by-control', [ControlItemController::class, 'getByControl'])
                ->name('api.control-items.by-control')
                ->middleware('can:vehicle-control-items.general.view,backoffice');

            // ==================== NOTIFICATIONS ====================
            Route::prefix('notifications')->name('notifications.')->group(function () {
                Route::get('/', [NotificationController::class, 'index'])->name('index');
                Route::get('/archived', [NotificationController::class, 'archived'])->name('archived');
                Route::get('/recent', [NotificationController::class, 'getRecent'])->name('recent');
                Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
                Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
                Route::post('/archive-all-read', [NotificationController::class, 'archiveAllRead'])->name('archive-all-read');
                Route::post('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
                Route::post('/delete-all-archived', [NotificationController::class, 'deleteAllArchived'])->name('delete-all-archived');
                Route::delete('/delete-all', [NotificationController::class, 'deleteAll'])->name('delete-all');
                Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
                Route::post('/{notification}/archive', [NotificationController::class, 'archive'])->name('archive');
                Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
            });

            // ==================== PROFILE SETTINGS ====================
            Route::prefix('admin')->name('profile.')->group(function () {
                Route::get('/profile-setting', [ProfileController::class, 'edit'])->name('setting');
                Route::post('/profile-setting', [ProfileController::class, 'update'])->name('update');
                Route::delete('/profile/delete-photo', [ProfileController::class, 'deletePhoto'])->name('delete-photo');
                Route::get('/security-setting', [ProfileController::class, 'showChangePassword'])->name('security');
                Route::put('/security-setting', [ProfileController::class, 'updatePassword'])->name('security.update');
                Route::get('/edit-profile', [ProfileController::class, 'edit'])->name('edit');
                Route::get('/change-password', [ProfileController::class, 'showChangePassword'])->name('change-password');
                Route::put('/change-password', [ProfileController::class, 'updatePassword'])->name('update-password');
            });

            // ==================== TRASH ====================
            Route::prefix('trash')->name('trash.')->group(function () {
                Route::get('/', [TrashController::class, 'index'])->name('index')
                    ->middleware('can:trash.general.view,backoffice');
                Route::patch('/{module}/restore/{id}', [TrashController::class, 'restore'])->name('restore')
                    ->middleware('can:trash.general.restore,backoffice');
                Route::patch('/{module}/restore-all', [TrashController::class, 'restoreAll'])->name('restore-all')
                    ->middleware('can:trash.general.restore,backoffice');
                Route::delete('/{module}/force-delete/{id}', [TrashController::class, 'forceDelete'])->name('force-delete')
                    ->middleware('can:trash.general.delete,backoffice');
                Route::delete('/{module}/force-delete-all', [TrashController::class, 'forceDeleteAll'])->name('force-delete-all')
                    ->middleware('can:trash.general.delete,backoffice');
                Route::delete('/empty-all', [TrashController::class, 'emptyAll'])->name('empty-all')
                    ->middleware('can:trash.general.delete,backoffice');
            });

        }); // END AUTH GROUP
    }); // END BACKOFFICE PREFIX