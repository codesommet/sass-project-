<?php

use Illuminate\Http\Request;
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
use App\Http\Controllers\Backoffice\ReportController;

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
                ->middleware('bo_permissions:dashboard.general.view');
            Route::post('/dashboard/filter', [DashboardController::class, 'getFilteredStats'])
                ->name('dashboard.filter')
                ->middleware('bo_permissions:dashboard.general.view');

            // ==================== USERS ====================
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:users.general.view');
                Route::get('/create', [UserController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:users.general.create');
                Route::post('/', [UserController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:users.general.create');
                Route::get('/{user}', [UserController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:users.general.view');
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:users.general.edit');
                Route::put('/{user}', [UserController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:users.general.edit');
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:users.general.delete');
            });

// ==================== CLIENTS ====================
Route::prefix('clients')->name('clients.')->group(function () {
    // Blacklist Routes - MUST come BEFORE the {client} routes
    Route::get('/blacklist', [ClientController::class, 'blacklistIndex'])
        ->name('blacklist.index')
        ->middleware('bo_permissions:clients.general.view');
    Route::post('/check-blacklist', [ClientController::class, 'checkBlacklist'])
        ->name('check-blacklist')
        ->middleware('bo_permissions:clients.general.view');

    // CRUD Routes - with {client} parameter (these come AFTER)
    Route::get('/', [ClientController::class, 'index'])->name('index')
        ->middleware('bo_permissions:clients.general.view');
    Route::get('/create', [ClientController::class, 'create'])->name('create')
        ->middleware('bo_permissions:clients.general.create');
    Route::post('/', [ClientController::class, 'store'])->name('store')
        ->middleware('bo_permissions:clients.general.create');
    Route::get('/{client}', [ClientController::class, 'show'])->name('show')
        ->middleware('bo_permissions:clients.general.view');
    Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit')
        ->middleware('bo_permissions:clients.general.edit');
    Route::put('/{client}', [ClientController::class, 'update'])->name('update')
        ->middleware('bo_permissions:clients.general.edit');
    Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy')
        ->middleware('bo_permissions:clients.general.delete');
    
    // These can stay here (they also have {client} but come after)
    Route::post('/{client}/blacklist', [ClientController::class, 'addToBlacklist'])
        ->name('add-to-blacklist')
        ->middleware('bo_permissions:clients.general.edit');
    Route::delete('/{client}/unblacklist', [ClientController::class, 'removeFromBlacklist'])
        ->name('remove-from-blacklist')
        ->middleware('bo_permissions:clients.general.edit');
});

            // ==================== AGENCIES ====================
            Route::prefix('agencies')->name('agencies.')->group(function () {
                Route::get('/', [AgencyController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:agencies.general.view');
                Route::get('/create', [AgencyController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:agencies.general.create');
                Route::post('/', [AgencyController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:agencies.general.create');
                Route::get('/{agency}', [AgencyController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:agencies.general.view');
                Route::get('/{agency}/edit', [AgencyController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:agencies.general.edit');
                Route::put('/{agency}', [AgencyController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:agencies.general.edit');
                Route::delete('/{agency}', [AgencyController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:agencies.general.delete');

                // ==================== AGENCY SETTINGS (ALL IN ONE PLACE) ====================
                Route::prefix('{agency}/settings')->name('settings.')->group(function () {
                    // GET routes
                    Route::get('/', [AgencySettingsController::class, 'edit'])->name('edit')
                        ->middleware('bo_permissions:agency-settings.general.edit');
                    Route::get('/profile', [AgencyController::class, 'profile'])->name('profile')
                        ->middleware('bo_permissions:agency-settings.general.edit');
                    Route::get('/notifications', [AgencySettingsController::class, 'notifications'])->name('notifications')
                        ->middleware('bo_permissions:agency-settings.general.edit');
                    Route::get('/invoice-template', [AgencySettingsController::class, 'invoiceTemplate'])->name('invoice-template')
                        ->middleware('bo_permissions:agency-settings.general.edit');
                    Route::get('/company', [AgencySettingsController::class, 'company'])->name('company')
                        ->middleware('bo_permissions:agency-settings.general.edit');
                    Route::get('/signatures', [AgencySettingsController::class, 'signatures'])->name('signatures')
                        ->middleware('bo_permissions:agency-settings.general.edit');

                    // NEW WEBSITE SETTINGS
                    Route::get('/website', [AgencySettingsController::class, 'website'])->name('website')
                        ->middleware('bo_permissions:agency-settings.general.edit');
                    Route::post('/website', [AgencySettingsController::class, 'updateWebsite'])->name('website.update')
                        ->middleware('bo_permissions:agency-settings.general.edit');

                    // NEW INVOICE SETTINGS
                    Route::get('/invoice-settings', [AgencySettingsController::class, 'invoiceSettings'])->name('invoice-settings')
                        ->middleware('bo_permissions:agency-settings.general.edit');
                    Route::post('/invoice-settings', [AgencySettingsController::class, 'updateInvoiceSettings'])->name('invoice-settings.update')
                        ->middleware('bo_permissions:agency-settings.general.edit');

                    // UPDATE routes
                    Route::patch('/', [AgencySettingsController::class, 'update'])->name('update')
                        ->middleware('bo_permissions:agency-settings.general.edit');
                    Route::post('/company', [AgencySettingsController::class, 'updateCompany'])->name('update.company')
                        ->middleware('bo_permissions:agency-settings.general.edit');
                    Route::post('/profile', [AgencyController::class, 'updateProfile'])->name('update.profile')
                        ->middleware('bo_permissions:agency-settings.general.edit');

                    // DELETE routes
                    Route::delete('/delete-logo', [AgencySettingsController::class, 'deleteLogo'])->name('delete-logo')
                        ->middleware('bo_permissions:agency-settings.general.edit');
                    Route::delete('/delete-signature', [AgencySettingsController::class, 'deleteSignature'])->name('delete-signature')
                        ->middleware('bo_permissions:agency-settings.general.edit');
                });
            });

            // ==================== AGENTS ====================
            Route::prefix('agents')->name('agents.')->group(function () {
                Route::get('/', [AgentController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:agents.general.view');
                Route::get('/create', [AgentController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:agents.general.create');
                Route::post('/', [AgentController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:agents.general.create');
                Route::get('/{agent}', [AgentController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:agents.general.view');
                Route::get('/{agent}/edit', [AgentController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:agents.general.edit');
                Route::put('/{agent}', [AgentController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:agents.general.edit');
                Route::delete('/{agent}', [AgentController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:agents.general.delete');
            });

            // ==================== AGENCY SUBSCRIPTIONS ====================
            Route::prefix('agency-subscriptions')->name('agency-subscriptions.')->group(function () {
                Route::get('/', [AgencySubscriptionController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:agency-subscriptions.general.view');
                Route::get('/create', [AgencySubscriptionController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:agency-subscriptions.general.create');
                Route::post('/', [AgencySubscriptionController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:agency-subscriptions.general.create');
                Route::get('/{agencySubscription}', [AgencySubscriptionController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:agency-subscriptions.general.view');
                Route::get('/{agencySubscription}/edit', [AgencySubscriptionController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:agency-subscriptions.general.edit');
                Route::put('/{agencySubscription}', [AgencySubscriptionController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:agency-subscriptions.general.edit');
                Route::delete('/{agencySubscription}', [AgencySubscriptionController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:agency-subscriptions.general.delete');
            });

            Route::get('my-subscription', [AgencySubscriptionController::class, 'mySubscription'])
                ->name('my-subscription')
                ->middleware('bo_permissions:agency-settings.general.view');

            // ==================== ROLES & PERMISSIONS ====================
            Route::prefix('roles-permissions')->name('roles-permissions.')->group(function () {
                Route::get('/', [RolesPermissionsController::class, 'indexRoles'])->name('roles')
                    ->middleware('bo_permissions:roles-permissions.general.view');
                Route::get('/{role}/permissions', [RolesPermissionsController::class, 'showPermissions'])->name('permissions')
                    ->middleware('bo_permissions:roles-permissions.general.view');
                Route::put('/{role}/permissions', [RolesPermissionsController::class, 'updatePermissions'])->name('permissions.update')
                    ->middleware('bo_permissions:roles-permissions.general.edit');
            });

            Route::prefix('roles')->name('roles.')->group(function () {
                Route::post('/', [RoleController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:roles-permissions.general.create');
                Route::put('/{role}', [RoleController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:roles-permissions.general.edit');
                Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:roles-permissions.general.delete');
            });

            Route::prefix('permissions')->name('permissions.')->group(function () {
                Route::post('/', [PermissionController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:roles-permissions.general.create');
                Route::put('/{permission}', [PermissionController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:roles-permissions.general.edit');
                Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:roles-permissions.general.delete');
            });

            // ==================== VEHICLE BRANDS ====================
            Route::prefix('vehicle-brands')->name('vehicle-brands.')->group(function () {
                Route::get('/', [VehicleBrandController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:vehicle-brands.general.view');
                Route::get('/create', [VehicleBrandController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:vehicle-brands.general.create');
                Route::post('/', [VehicleBrandController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:vehicle-brands.general.create');
                Route::get('/{vehicleBrand}', [VehicleBrandController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:vehicle-brands.general.view');
                Route::get('/{vehicleBrand}/edit', [VehicleBrandController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:vehicle-brands.general.edit');
                Route::put('/{vehicleBrand}', [VehicleBrandController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:vehicle-brands.general.edit');
                Route::delete('/{vehicleBrand}', [VehicleBrandController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:vehicle-brands.general.delete');
            });

            // ==================== VEHICLE MODELS ====================
            Route::prefix('vehicle-models')->name('vehicle-models.')->group(function () {
                Route::get('/', [VehicleModelController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:vehicle-models.general.view');
                Route::get('/create', [VehicleModelController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:vehicle-models.general.create');
                Route::post('/', [VehicleModelController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:vehicle-models.general.create');
                Route::get('/{vehicleModel}', [VehicleModelController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:vehicle-models.general.view');
                Route::get('/{vehicleModel}/edit', [VehicleModelController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:vehicle-models.general.edit');
                Route::put('/{vehicleModel}', [VehicleModelController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:vehicle-models.general.edit');
                Route::delete('/{vehicleModel}', [VehicleModelController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:vehicle-models.general.delete');
            });

            // ==================== VEHICLES ====================
            Route::prefix('vehicles')->name('vehicles.')->group(function () {
                Route::get('/', [VehicleController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:vehicles.general.view');
                Route::delete('/bulk-destroy', [VehicleController::class, 'bulkDestroy'])->name('bulkDestroy')
                    ->middleware('bo_permissions:vehicles.general.delete');
                Route::post('/check-duplicate', [VehicleController::class, 'checkDuplicate'])->name('check-duplicate')
                    ->middleware('bo_permissions:vehicles.general.create');
                Route::get('/create', [VehicleController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:vehicles.general.create');
                Route::post('/', [VehicleController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:vehicles.general.create');
                Route::get('/{vehicle}', [VehicleController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:vehicles.general.view');
                Route::get('/{vehicle}/edit', [VehicleController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:vehicles.general.edit');
                Route::put('/{vehicle}', [VehicleController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:vehicles.general.edit');
                Route::delete('/{vehicle}', [VehicleController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:vehicles.general.delete');

                // Vehicle Documents
                Route::prefix('{vehicle}/vignettes')->name('vignettes.')->group(function () {
                    Route::get('/', [VignetteController::class, 'index'])->name('index')
                        ->middleware('bo_permissions:vehicle-vignettes.general.view');
                    Route::get('/create', [VignetteController::class, 'create'])->name('create')
                        ->middleware('bo_permissions:vehicle-vignettes.general.create');
                    Route::post('/', [VignetteController::class, 'store'])->name('store')
                        ->middleware('bo_permissions:vehicle-vignettes.general.create');
                    Route::get('/{vignette}', [VignetteController::class, 'show'])->name('show')
                        ->middleware('bo_permissions:vehicle-vignettes.general.view');
                    Route::get('/{vignette}/edit', [VignetteController::class, 'edit'])->name('edit')
                        ->middleware('bo_permissions:vehicle-vignettes.general.edit');
                    Route::put('/{vignette}', [VignetteController::class, 'update'])->name('update')
                        ->middleware('bo_permissions:vehicle-vignettes.general.edit');
                    Route::delete('/{vignette}', [VignetteController::class, 'destroy'])->name('destroy')
                        ->middleware('bo_permissions:vehicle-vignettes.general.delete');
                });

                Route::prefix('{vehicle}/insurances')->name('insurances.')->group(function () {
                    Route::get('/', [InsuranceController::class, 'index'])->name('index')
                        ->middleware('bo_permissions:vehicle-insurances.general.view');
                    Route::get('/create', [InsuranceController::class, 'create'])->name('create')
                        ->middleware('bo_permissions:vehicle-insurances.general.create');
                    Route::post('/', [InsuranceController::class, 'store'])->name('store')
                        ->middleware('bo_permissions:vehicle-insurances.general.create');
                    Route::get('/{insurance}', [InsuranceController::class, 'show'])->name('show')
                        ->middleware('bo_permissions:vehicle-insurances.general.view');
                    Route::get('/{insurance}/edit', [InsuranceController::class, 'edit'])->name('edit')
                        ->middleware('bo_permissions:vehicle-insurances.general.edit');
                    Route::put('/{insurance}', [InsuranceController::class, 'update'])->name('update')
                        ->middleware('bo_permissions:vehicle-insurances.general.edit');
                    Route::delete('/{insurance}', [InsuranceController::class, 'destroy'])->name('destroy')
                        ->middleware('bo_permissions:vehicle-insurances.general.delete');
                });

                Route::prefix('{vehicle}/oil-changes')->name('oil-changes.')->group(function () {
                    Route::get('/', [OilChangeController::class, 'index'])->name('index')
                        ->middleware('bo_permissions:vehicle-oil-changes.general.view');
                    Route::get('/create', [OilChangeController::class, 'create'])->name('create')
                        ->middleware('bo_permissions:vehicle-oil-changes.general.create');
                    Route::post('/', [OilChangeController::class, 'store'])->name('store')
                        ->middleware('bo_permissions:vehicle-oil-changes.general.create');
                    Route::get('/{oilChange}', [OilChangeController::class, 'show'])->name('show')
                        ->middleware('bo_permissions:vehicle-oil-changes.general.view');
                    Route::get('/{oilChange}/edit', [OilChangeController::class, 'edit'])->name('edit')
                        ->middleware('bo_permissions:vehicle-oil-changes.general.edit');
                    Route::put('/{oilChange}', [OilChangeController::class, 'update'])->name('update')
                        ->middleware('bo_permissions:vehicle-oil-changes.general.edit');
                    Route::delete('/{oilChange}', [OilChangeController::class, 'destroy'])->name('destroy')
                        ->middleware('bo_permissions:vehicle-oil-changes.general.delete');
                });

                Route::prefix('{vehicle}/technical-checks')->name('technical-checks.')->group(function () {
                    Route::get('/', [TechnicalCheckController::class, 'index'])->name('index')
                        ->middleware('bo_permissions:vehicle-technical-checks.general.view');
                    Route::get('/create', [TechnicalCheckController::class, 'create'])->name('create')
                        ->middleware('bo_permissions:vehicle-technical-checks.general.create');
                    Route::post('/', [TechnicalCheckController::class, 'store'])->name('store')
                        ->middleware('bo_permissions:vehicle-technical-checks.general.create');
                    Route::get('/{technicalCheck}', [TechnicalCheckController::class, 'show'])->name('show')
                        ->middleware('bo_permissions:vehicle-technical-checks.general.view');
                    Route::get('/{technicalCheck}/edit', [TechnicalCheckController::class, 'edit'])->name('edit')
                        ->middleware('bo_permissions:vehicle-technical-checks.general.edit');
                    Route::put('/{technicalCheck}', [TechnicalCheckController::class, 'update'])->name('update')
                        ->middleware('bo_permissions:vehicle-technical-checks.general.edit');
                    Route::delete('/{technicalCheck}', [TechnicalCheckController::class, 'destroy'])->name('destroy')
                        ->middleware('bo_permissions:vehicle-technical-checks.general.delete');
                });

                Route::prefix('{vehicle}/controls')->name('controls.')->group(function () {
                    Route::get('/', [ControlController::class, 'index'])->name('index')
                        ->middleware('bo_permissions:vehicle-controls.general.view');
                    Route::get('/create', [ControlController::class, 'create'])->name('create')
                        ->middleware('bo_permissions:vehicle-controls.general.create');
                    Route::post('/', [ControlController::class, 'store'])->name('store')
                        ->middleware('bo_permissions:vehicle-controls.general.create');
                    Route::get('/{control}', [ControlController::class, 'show'])->name('show')
                        ->middleware('bo_permissions:vehicle-controls.general.view');
                    Route::get('/{control}/edit', [ControlController::class, 'edit'])->name('edit')
                        ->middleware('bo_permissions:vehicle-controls.general.edit');
                    Route::put('/{control}', [ControlController::class, 'update'])->name('update')
                        ->middleware('bo_permissions:vehicle-controls.general.edit');
                    Route::delete('/{control}', [ControlController::class, 'destroy'])->name('destroy')
                        ->middleware('bo_permissions:vehicle-controls.general.delete');
                        
                });
            });

            // ==================== CONTROLS (Standalone) ====================
            Route::prefix('controls')->name('controls.')->group(function () {
                Route::get('/', [ControlController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:vehicle-controls.general.view');
                Route::get('/create', [ControlController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:vehicle-controls.general.create');
                Route::post('/', [ControlController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:vehicle-controls.general.create');
                Route::get('/{control}', [ControlController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:vehicle-controls.general.view');
                Route::get('/{control}/edit', [ControlController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:vehicle-controls.general.edit');
                Route::put('/{control}', [ControlController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:vehicle-controls.general.edit');
                Route::delete('/{control}', [ControlController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:vehicle-controls.general.delete');
            });

            // ==================== CONTROL ITEMS ====================
            Route::prefix('control-items')->name('control-items.')->group(function () {
                Route::get('/', [ControlItemController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:vehicle-control-items.general.view');
                Route::get('/create', [ControlItemController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:vehicle-control-items.general.create');
                Route::post('/', [ControlItemController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:vehicle-control-items.general.create');
                Route::get('/{item}', [ControlItemController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:vehicle-control-items.general.view');
                Route::get('/{item}/edit', [ControlItemController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:vehicle-control-items.general.edit');
                Route::put('/{item}', [ControlItemController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:vehicle-control-items.general.edit');
                Route::delete('/{item}', [ControlItemController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:vehicle-control-items.general.delete');
            });

            // ==================== RENTAL CONTRACTS ====================
            Route::prefix('rental-contracts')->name('rental-contracts.')->group(function () {
                Route::get('/', [RentalContractController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:rental-contracts.general.view');
                Route::get('/create', [RentalContractController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:rental-contracts.general.create');
                Route::post('/', [RentalContractController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:rental-contracts.general.create');
                Route::get('/{rentalContract}', [RentalContractController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:rental-contracts.general.view');
                Route::get('/{rentalContract}/edit', [RentalContractController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:rental-contracts.general.edit');
                Route::put('/{rentalContract}', [RentalContractController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:rental-contracts.general.edit');
                Route::delete('/{rentalContract}', [RentalContractController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:rental-contracts.general.delete');
                Route::post('/{rentalContract}/status', [RentalContractController::class, 'updateStatus'])->name('status')
                    ->middleware('bo_permissions:rental-contracts.general.edit');

                Route::prefix('{rentalContract}/clients')->name('clients.')->group(function () {
                    Route::get('/', [ContractClientController::class, 'index'])->name('index')
                        ->middleware('bo_permissions:contract-clients.general.view');
                    Route::get('/create', [ContractClientController::class, 'create'])->name('create')
                        ->middleware('bo_permissions:contract-clients.general.create');
                    Route::post('/', [ContractClientController::class, 'store'])->name('store')
                        ->middleware('bo_permissions:contract-clients.general.create');
                    Route::get('/{contractClient}/edit', [ContractClientController::class, 'edit'])->name('edit')
                        ->middleware('bo_permissions:contract-clients.general.edit');
                    Route::put('/{contractClient}', [ContractClientController::class, 'update'])->name('update')
                        ->middleware('bo_permissions:contract-clients.general.edit');
                    Route::delete('/{contractClient}', [ContractClientController::class, 'destroy'])->name('destroy')
                        ->middleware('bo_permissions:contract-clients.general.delete');
                });
            });

            // ==================== CONTRACT CLIENTS ====================
            Route::prefix('contract-clients')->name('contract-clients.')->group(function () {
                Route::get('/', [ContractClientController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:contract-clients.general.view');
                Route::get('/create', [ContractClientController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:contract-clients.general.create');
                Route::post('/', [ContractClientController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:contract-clients.general.create');
                Route::get('/{contractClient}', [ContractClientController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:contract-clients.general.view');
                Route::get('/{contractClient}/edit', [ContractClientController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:contract-clients.general.edit');
                Route::put('/{contractClient}', [ContractClientController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:contract-clients.general.edit');
                Route::delete('/{contractClient}', [ContractClientController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:contract-clients.general.delete');
            });

            // ==================== BOOKINGS ====================
            Route::prefix('bookings')->name('bookings.')->group(function () {
                Route::get('/', [BookingController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:bookings.general.view');
                Route::get('/calendar/view', [BookingController::class, 'calendar'])->name('calendar')
                    ->middleware('bo_permissions:bookings.general.view');
                Route::get('/create', [BookingController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:bookings.general.create');
                Route::post('/', [BookingController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:bookings.general.create');
                Route::get('/{booking}', [BookingController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:bookings.general.view');
                Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:bookings.general.edit');
                Route::put('/{booking}', [BookingController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:bookings.general.edit');
                Route::delete('/{booking}', [BookingController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:bookings.general.delete');
                Route::post('/{booking}/status', [BookingController::class, 'updateStatus'])->name('status')
                    ->middleware('bo_permissions:bookings.general.edit');
                Route::post('/{booking}/convert-to-contract', [BookingController::class, 'convertToContract'])->name('convert-to-contract')
                    ->middleware('bo_permissions:bookings.general.edit');
            });

            // ==================== FINANCE ====================
            Route::prefix('finance/accounts')->name('finance.accounts.')->group(function () {
                Route::get('/', [FinancialAccountController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:financial-accounts.general.view');
                Route::get('/create', [FinancialAccountController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:financial-accounts.general.create');
                Route::post('/', [FinancialAccountController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:financial-accounts.general.create');
                Route::get('/{financialAccount}', [FinancialAccountController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:financial-accounts.general.view');
                Route::get('/{financialAccount}/edit', [FinancialAccountController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:financial-accounts.general.edit');
                Route::put('/{financialAccount}', [FinancialAccountController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:financial-accounts.general.edit');
                Route::delete('/{financialAccount}', [FinancialAccountController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:financial-accounts.general.delete');
            });

            Route::prefix('finance/categories')->name('finance.categories.')->group(function () {
                Route::get('/', [TransactionCategoryController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:transaction-categories.general.view');
                Route::get('/create', [TransactionCategoryController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:transaction-categories.general.create');
                Route::post('/', [TransactionCategoryController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:transaction-categories.general.create');
                Route::get('/{transactionCategory}', [TransactionCategoryController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:transaction-categories.general.view');
                Route::get('/{transactionCategory}/edit', [TransactionCategoryController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:transaction-categories.general.edit');
                Route::put('/{transactionCategory}', [TransactionCategoryController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:transaction-categories.general.edit');
                Route::delete('/{transactionCategory}', [TransactionCategoryController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:transaction-categories.general.delete');
            });

            Route::prefix('finance/transactions')->name('finance.transactions.')->group(function () {
                Route::get('/', [FinancialTransactionController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:financial-transactions.general.view');
                Route::get('/summary/data', [FinancialTransactionController::class, 'summary'])->name('summary')
                    ->middleware('bo_permissions:financial-transactions.general.view');
                Route::get('/create', [FinancialTransactionController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:financial-transactions.general.create');
                Route::post('/', [FinancialTransactionController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:financial-transactions.general.create');
                Route::get('/{financialTransaction}', [FinancialTransactionController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:financial-transactions.general.view');
                Route::get('/{financialTransaction}/edit', [FinancialTransactionController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:financial-transactions.general.edit');
                Route::put('/{financialTransaction}', [FinancialTransactionController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:financial-transactions.general.edit');
                Route::delete('/{financialTransaction}', [FinancialTransactionController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:financial-transactions.general.delete');
            });

            // ==================== INVOICES ====================
            Route::prefix('invoices')->name('invoices.')->group(function () {
                Route::get('/', [InvoiceController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:invoices.general.view');
                Route::get('/create', [InvoiceController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:invoices.general.create');
                Route::post('/', [InvoiceController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:invoices.general.create');
                Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:invoices.general.view');
                Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:invoices.general.edit');
                Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:invoices.general.edit');
                Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:invoices.general.delete');
                Route::post('/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('status')
                    ->middleware('bo_permissions:invoices.general.edit');
            });

            Route::prefix('invoices/pdf')->name('invoices.pdf.')->group(function () {
                Route::get('/{id}', [InvoicePDFController::class, 'exportSingle'])->name('single')
                    ->middleware('bo_permissions:invoices.general.view');
                Route::get('/{id}/view', [InvoicePDFController::class, 'view'])->name('view')
                    ->middleware('bo_permissions:invoices.general.view');
                Route::post('/export-multiple', [InvoicePDFController::class, 'exportMultiple'])->name('multiple')
                    ->middleware('bo_permissions:invoices.general.view');
            });

            // ==================== INVOICE ITEMS ====================
            Route::prefix('invoice-items')->name('invoice-items.')->group(function () {
                Route::get('/', [InvoiceItemController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:invoice-items.general.view');
                Route::get('/create', [InvoiceItemController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:invoice-items.general.create');
                Route::post('/', [InvoiceItemController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:invoice-items.general.create');
                Route::get('/{invoiceItem}', [InvoiceItemController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:invoice-items.general.view');
                Route::get('/{invoiceItem}/edit', [InvoiceItemController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:invoice-items.general.edit');
                Route::put('/{invoiceItem}', [InvoiceItemController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:invoice-items.general.edit');
                Route::delete('/{invoiceItem}', [InvoiceItemController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:invoice-items.general.delete');
            });

            // ==================== PAYMENTS ====================
            Route::prefix('payments')->name('payments.')->group(function () {
                Route::get('/', [PaymentController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:payments.general.view');
                Route::get('/create', [PaymentController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:payments.general.create');
                Route::post('/', [PaymentController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:payments.general.create');
                Route::get('/{payment}', [PaymentController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:payments.general.view');
                Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:payments.general.edit');
                Route::put('/{payment}', [PaymentController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:payments.general.edit');
                Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:payments.general.delete');
                Route::post('/{payment}/status', [PaymentController::class, 'updateStatus'])->name('status')
                    ->middleware('bo_permissions:payments.general.edit');
            });

            // ==================== VEHICLE CREDITS ====================
            Route::prefix('vehicle-credits')->name('vehicle-credits.')->group(function () {
                Route::get('/', [VehicleCreditController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:vehicle-credits.general.view');
                Route::get('/dashboard', [VehicleCreditController::class, 'dashboard'])->name('dashboard')
                    ->middleware('bo_permissions:vehicle-credits.general.view');
                Route::get('/create', [VehicleCreditController::class, 'create'])->name('create')
                    ->middleware('bo_permissions:vehicle-credits.general.create');
                Route::post('/', [VehicleCreditController::class, 'store'])->name('store')
                    ->middleware('bo_permissions:vehicle-credits.general.create');
                Route::get('/{vehicleCredit}', [VehicleCreditController::class, 'show'])->name('show')
                    ->middleware('bo_permissions:vehicle-credits.general.view');
                Route::get('/{vehicleCredit}/edit', [VehicleCreditController::class, 'edit'])->name('edit')
                    ->middleware('bo_permissions:vehicle-credits.general.edit');
                Route::put('/{vehicleCredit}', [VehicleCreditController::class, 'update'])->name('update')
                    ->middleware('bo_permissions:vehicle-credits.general.edit');
                Route::delete('/{vehicleCredit}', [VehicleCreditController::class, 'destroy'])->name('destroy')
                    ->middleware('bo_permissions:vehicle-credits.general.delete');
                Route::post('/{vehicleCredit}/record-payment', [VehicleCreditController::class, 'recordPayment'])->name('record-payment')
                    ->middleware('bo_permissions:vehicle-credits.general.edit');
                Route::get('/{vehicleCredit}/payment-schedule', [VehicleCreditController::class, 'getPaymentSchedule'])->name('payment-schedule')
                    ->middleware('bo_permissions:vehicle-credits.general.view');
            });

            // ==================== GLOBAL VEHICLE DOCUMENTS ====================
            Route::prefix('vehicle-documents')->name('vehicle-documents.')->group(function () {
                Route::get('/vignettes', fn(Request $request) => app(VignetteController::class)->index($request, 'all'))->name('vignettes.index')
                    ->middleware('bo_permissions:vehicle-vignettes.general.view');
                Route::get('/vignettes/create', [VignetteController::class, 'create'])->name('vignettes.create')
                    ->middleware('bo_permissions:vehicle-vignettes.general.create');
                Route::post('/vignettes', [VignetteController::class, 'store'])->name('vignettes.store')
                    ->middleware('bo_permissions:vehicle-vignettes.general.create');
                Route::get('/insurances', fn(Request $request) => app(InsuranceController::class)->index($request, 'all'))->name('insurances.index')
                    ->middleware('bo_permissions:vehicle-insurances.general.view');
                Route::get('/insurances/create', [InsuranceController::class, 'create'])->name('insurances.create')
                    ->middleware('bo_permissions:vehicle-insurances.general.create');
                Route::post('/insurances', [InsuranceController::class, 'store'])->name('insurances.store')
                    ->middleware('bo_permissions:vehicle-insurances.general.create');
                Route::get('/oil-changes', fn(Request $request) => app(OilChangeController::class)->index($request, 'all'))->name('oil-changes.index')
                    ->middleware('bo_permissions:vehicle-oil-changes.general.view');
                Route::get('/oil-changes/create', [OilChangeController::class, 'create'])->name('oil-changes.create')
                    ->middleware('bo_permissions:vehicle-oil-changes.general.create');
                Route::post('/oil-changes', [OilChangeController::class, 'store'])->name('oil-changes.store')
                    ->middleware('bo_permissions:vehicle-oil-changes.general.create');
                Route::get('/technical-checks', fn(Request $request) => app(TechnicalCheckController::class)->index($request, 'all'))->name('technical-checks.index')
                    ->middleware('bo_permissions:vehicle-technical-checks.general.view');
                Route::get('/technical-checks/create', [TechnicalCheckController::class, 'create'])->name('technical-checks.create')
                    ->middleware('bo_permissions:vehicle-technical-checks.general.create');
                Route::post('/technical-checks', [TechnicalCheckController::class, 'store'])->name('technical-checks.store')
                    ->middleware('bo_permissions:vehicle-technical-checks.general.create');
            });

            // ==================== CONTRACT PDF ====================
            Route::prefix('contracts/pdf')->name('contracts.pdf.')->group(function () {
                Route::get('/{id}', [ContractPDFController::class, 'exportSingle'])->name('single')
                    ->middleware('bo_permissions:rental-contracts.general.view');
                Route::get('/{id}/view', [ContractPDFController::class, 'view'])->name('view')
                    ->middleware('bo_permissions:rental-contracts.general.view');
                Route::post('/export-multiple', [ContractPDFController::class, 'exportMultiple'])->name('multiple')
                    ->middleware('bo_permissions:rental-contracts.general.view');
            });

            // ==================== API ROUTES ====================
            Route::get('/api/control-items/by-control', [ControlItemController::class, 'getByControl'])
                ->name('api.control-items.by-control')
                ->middleware('bo_permissions:vehicle-control-items.general.view');

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

            // ==================== REPORTS ====================
            Route::prefix('reports')->name('reports.')->middleware('bo_permissions:reports.general.view')->group(function () {
                Route::get('/income', [ReportController::class, 'incomeReport'])->name('income');
                Route::get('/earnings', [ReportController::class, 'earningsReport'])->name('earnings');
                Route::get('/rentals', [ReportController::class, 'rentalReport'])->name('rentals');
            });

            // ==================== TRASH ====================
            Route::prefix('trash')->name('trash.')->group(function () {
                Route::get('/', [TrashController::class, 'index'])->name('index')
                    ->middleware('bo_permissions:trash.general.view');
                Route::patch('/{module}/restore/{id}', [TrashController::class, 'restore'])->name('restore')
                    ->middleware('bo_permissions:trash.general.restore');
                Route::patch('/{module}/restore-all', [TrashController::class, 'restoreAll'])->name('restore-all')
                    ->middleware('bo_permissions:trash.general.restore');
                Route::delete('/{module}/force-delete/{id}', [TrashController::class, 'forceDelete'])->name('force-delete')
                    ->middleware('bo_permissions:trash.general.delete');
                Route::delete('/{module}/force-delete-all', [TrashController::class, 'forceDeleteAll'])->name('force-delete-all')
                    ->middleware('bo_permissions:trash.general.delete');
                Route::delete('/empty-all', [TrashController::class, 'emptyAll'])->name('empty-all')
                    ->middleware('bo_permissions:trash.general.delete');
            });

        }); // END AUTH GROUP
    }); // END BACKOFFICE PREFIX