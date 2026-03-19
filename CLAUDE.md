# Projet-Stage — Vehicle Rental Management System (SaaS)

A multi-tenant Laravel SaaS platform for managing vehicle rental agency operations, built for the Moroccan market (MAD currency, French validation messages).

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+, SQLite (dev)
- **Frontend:** Blade templates, Vite 7, SCSS, Tabler Icons (`ti ti-*`)
- **Key Packages:** spatie/laravel-permission (RBAC), spatie/laravel-medialibrary (file uploads), barryvdh/laravel-dompdf (PDF export)
- **Auth:** Dual guards (`web` + `backoffice`), session-based

## Architecture Overview

```
Multi-tenant: Agency → Users/Vehicles/Clients/Contracts/Finance
              └─ All data scoped by agency_id (cascade on delete)

Auth:         backoffice guard → CheckBackofficePermission middleware
              Permission format: "module.resource.action" (e.g., "vehicles.general.view")
              Super-admin bypasses all via Gate::before()

File Storage: Spatie MediaLibrary → 'media' disk
              Collections: avatar, logo, signature, stamp, vehicle_photos, vehicle_documents,
                          agent_avatar, client_avatar, vehicle_brand_logo
```

## Directory Structure

```
app/
├── Http/Controllers/Backoffice/     # All admin controllers (~30)
│   ├── Finance/                     # FinancialAccount, FinancialTransaction, TransactionCategory
│   └── Vehicles/                    # Vignette, Insurance, OilChange, TechnicalCheck, Control, ControlItem
├── Http/Requests/Backoffice/        # Form request validation (~54 classes)
├── Http/Middleware/                 # CheckBackofficePermission
├── Models/                          # 27 Eloquent models
├── Policies/                        # AgencyPolicy, VehiclePolicy, etc.
├── Traits/                          # Notifiable (used by base Controller)
├── services/Finance/                # AutoTransactionService
└── helpers/                         # NotificationHelper

config/
├── sidebar.php                      # Config-driven sidebar menu structure
├── auth.php                         # Dual guards: 'web' + 'backoffice'
├── permission.php                   # Spatie Permission config
└── media-library.php                # Disk: 'media', max: 50MB

routes/
├── web.php                          # Requires backoffice.php + frontoffice.php
├── backoffice.php                   # All admin routes (prefix: /backoffice)
└── frontoffice.php                  # Public-facing routes (prefix: /)

resources/views/
├── Backoffice/                      # Admin templates (~300+ blade files)
│   ├── layout/                      # mainlayout_admin, partials (header, sidebar, footer)
│   ├── dashboard/                   # Charts, reports, statistics, exports
│   ├── reports/                     # income, earnings, rentals report views
│   ├── vehicles/                    # Vehicle CRUD + nested maintenance docs
│   ├── bookings/                    # Booking CRUD + calendar
│   ├── rental-contracts/            # Contract CRUD + PDF export
│   ├── clients/                     # Client CRUD + blacklist
│   ├── invoices/                    # Invoice CRUD + PDF export
│   ├── finance/                     # Accounts, transactions, categories
│   ├── payments/                    # Payment CRUD
│   └── ...                          # agencies, agents, users, roles, notifications, trash, profile
├── components/                      # Reusable Blade components
│   ├── sidebar/                     # section.blade.php, menu-item.blade.php (config-driven)
│   ├── breadcrumb.blade.php
│   ├── pagination.blade.php
│   └── modalpopup.blade.php
└── layout/                          # Delegates to Backoffice/layout/ (canonical source)
```

## Sidebar Architecture

The sidebar is **config-driven** via `config/sidebar.php`:

```
config/sidebar.php          → Defines sections, items, permissions, routes, icons
components/sidebar/section  → Renders a section (title + items), checks section-level permission
components/sidebar/menu-item → Renders individual item or submenu, checks item-level permission
Backoffice/layout/partials/sidebar_admin → Loops over config('sidebar.sections')
```

**Adding a new sidebar item:**
1. Add the item to `config/sidebar.php` under the appropriate section
2. Include: `label`, `icon` (ti ti-*), `route`, `routeMatch`, and optionally `permission`
3. For submenus, use `children` array instead of `route`
4. The sidebar auto-renders based on config — no Blade editing needed

**Supported features:**
- Permission-gated sections and items (`@can` / `@canany`)
- Active state highlighting via `routeMatch` patterns
- Dynamic badges (e.g., unread notification count)
- Collapsible submenus
- Agency-conditional items (`requiresAgency: true`)

## Database Schema (Key Tables)

### Core Entities
| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `agencies` | Tenant root entity | name, settings (JSON), default_currency (MAD) |
| `users` | System users | agency_id, status (active/inactive/blocked), backoffice guard |
| `agents` | Agency staff | agency_id, user_id, full_name |
| `clients` | Rental customers | agency_id, cin_number, passport_number, driving_license_number, status, rating_average |
| `blacklisted_clients` | Client blacklist | client_id, agency_id, reason, blacklisted_by |

### Vehicle Management
| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `vehicles` | Fleet inventory | agency_id, vehicle_model_id, registration_number (unique per agency), status, daily_rate, 7 boolean equipment fields |
| `vehicle_brands` | Manufacturers | agency_id, name |
| `vehicle_models` | Car models | vehicle_brand_id, transmission, fuel_type, doors, seats |
| `vehicle_vignettes` | Annual tax records | vehicle_id, date, amount, year |
| `vehicle_insurances` | Insurance policies | vehicle_id, company_name, policy_number, next_insurance_date |
| `vehicle_technical_checks` | Inspections | vehicle_id, date, amount, next_check_date |
| `vehicle_oil_changes` | Service records | vehicle_id, mileage, next_mileage, mechanic_name |
| `vehicle_controls` | Pre/post-rental checks | vehicle_id, rental_contract_id, start_mileage, end_mileage |
| `vehicle_control_items` | Checklist items | vehicle_control_id, item_key, status (yes/no/na) |
| `vehicle_credits` | Vehicle financing | vehicle_id, total_amount, monthly_payment, duration_months, remaining_amount, status |
| `credit_payments` | Credit installments | vehicle_credit_id, due_date, paid_date, amount, principal, interest, status |

### Rental Operations
| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `bookings` | Reservation requests | client_id, vehicle_id, start_at, end_at, status (pending/confirmed/cancelled/converted) |
| `rental_contracts` | Active rentals | contract_number (CTR-YYYYMM-NNNN), vehicle_id, daily_rate, total_amount, status, acceptance_status |
| `contract_clients` | Many-to-many pivot | rental_contract_id, client_id, role (primary/secondary) |

### Financial System
| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `financial_accounts` | Bank/cash accounts | agency_id, type (bank/cash/other), current_balance, is_default |
| `transaction_categories` | Categorization | agency_id, name, type (income/expense/both) |
| `financial_transactions` | Ledger entries | type (income/expense), amount, source_type/source_id (polymorphic), metadata (JSON) |
| `invoices` | Client invoices | invoice_number, vat_rate, total_ht, total_vat, total_ttc, status |
| `invoice_items` | Line items | invoice_id, description, quantity, unit_price_ttc, total_ttc, total_ht |
| `payments` | Payment records | invoice_id, financial_account_id, method (cash/card/bank_transfer/cheque), status |

### System Tables
| Table | Purpose |
|-------|---------|
| `roles` / `permissions` / `model_has_*` | Spatie RBAC (guard: backoffice) |
| `notifications` | In-app notifications (user_id, type, title, message, is_read, is_archived) |
| `activity_logs` | Audit trail (polymorphic subject) |
| `agency_subscriptions` | SaaS subscription plans |
| `media` | Spatie MediaLibrary (polymorphic file storage) |

## Key Model Relationships

```
Agency
├── hasMany: User, Agent, Client, Vehicle, Booking, RentalContract,
│            FinancialAccount, TransactionCategory, Invoice, Payment
└── hasOne:  AgencySubscription

Vehicle
├── belongsTo: Agency, VehicleModel
├── hasMany: VehicleVignette, VehicleInsurance, VehicleTechnicalCheck,
│            VehicleOilChange, VehicleControl, VehicleCredit
└── VehicleModel → belongsTo: VehicleBrand

RentalContract
├── belongsTo: Agency, Vehicle
├── belongsToMany: Client (via contract_clients, with role + order)
├── belongsTo: User (createdBy, updatedBy)
└── morphOne: FinancialTransaction

FinancialTransaction
├── belongsTo: FinancialAccount, TransactionCategory, User (createdBy)
├── morphTo: source (RentalContract, VehicleVignette, VehicleInsurance, etc.)
└── Boot: auto-updates account balance on create/update/delete

Invoice → hasMany: InvoiceItem, Payment
Payment → belongsTo: Invoice, RentalContract, FinancialAccount, FinancialTransaction
```

## Route Structure

All backoffice routes: `prefix: /backoffice`, `name: backoffice.*`, `middleware: auth:backoffice`

### Key Route Groups
| Prefix | Controller | Notes |
|--------|-----------|-------|
| `/dashboard` | DashboardController | Stats + AJAX filtered stats |
| `/bookings` | BookingController | CRUD + calendar + status + convert-to-contract |
| `/rental-contracts` | RentalContractController | CRUD + status + nested contract-clients |
| `/clients` | ClientController | CRUD + blacklist management |
| `/vehicles` | VehicleController | CRUD + check-duplicate |
| `/vehicles/{vehicle}/vignettes` | VignetteController | Nested per-vehicle |
| `/vehicle-documents/vignettes` | VignetteController | Standalone global view |
| `/controls` | ControlController | Vehicle inspections |
| `/vehicle-credits` | VehicleCreditController | CRUD + dashboard + record-payment |
| `/invoices` | InvoiceController | CRUD + status + PDF export |
| `/payments` | PaymentController | CRUD + status |
| `/finance/accounts` | FinancialAccountController | Bank/cash accounts |
| `/finance/transactions` | FinancialTransactionController | Income/expense ledger |
| `/finance/categories` | TransactionCategoryController | Transaction categories |
| `/reports/income` | ReportController | Income vs expense report |
| `/reports/earnings` | ReportController | Earnings report |
| `/reports/rentals` | ReportController | Rental statistics report |
| `/agencies` | AgencyController | CRUD + nested settings |
| `/users` | UserController | CRUD with role assignment |
| `/roles-permissions` | RolesPermissionsController | Permission matrix |
| `/trash` | TrashController | Soft-delete restore/force-delete |
| `/notifications` | NotificationController | Read/archive/delete |

### PDF Export Routes
- `GET /invoices/pdf/{id}` — Download invoice PDF
- `GET /invoices/pdf/{id}/view` — View invoice in browser
- `GET /contracts/pdf/{id}` — Download contract PDF

## Business Logic Highlights

1. **Contract Creation Flow:** Booking → Convert to Contract → Auto-creates financial transaction (income)
2. **Payment Flow:** Payment confirmed → Creates FinancialTransaction → Updates FinancialAccount balance → Updates Invoice status (paid/partially_paid)
3. **Contract Number Generation:** `CTR-YYYYMM-NNNN` format, auto-incremented per agency
4. **Financial Transaction Boot:** On create/update/delete, auto-updates the linked FinancialAccount's `current_balance`
5. **AutoTransactionService:** Creates automatic financial transactions for contracts (income), vignettes, insurance, technical checks, oil changes, and credit payments (expenses). Uses `getDefaultAccountId($agencyId)` to resolve agency-specific accounts.
6. **Polymorphic Transactions:** FinancialTransaction.source links to RentalContract, VehicleVignette, VehicleInsurance, VehicleTechnicalCheck, VehicleOilChange, or CreditPayment
7. **Multi-Client Contracts:** RentalContract supports primary + secondary clients via `contract_clients` pivot
8. **Vehicle Credit System:** Monthly payment tracking with principal/interest split, auto-generated payment schedules
9. **Soft Deletes:** All major entities use SoftDeletes with a dedicated Trash module for restore/permanent-delete
10. **Notification System:** In-app notifications triggered by CRUD actions via `Notifiable` trait, notifies super-admins and agency admins
11. **Booking Day Calculation:** Uses `diffInDays() + 1` to include both start and end day (consistent with contracts)
12. **Invoice Status:** Uses `bccomp()` for precise decimal comparison when checking if invoice is fully paid

## Permission System

Uses Spatie Laravel-Permission with guard `backoffice`.

**Format:** `module.resource.action`

### All Permission Modules
| Module | Actions | Notes |
|--------|---------|-------|
| `dashboard` | view | Dashboard access |
| `agencies` | view, create, edit, delete | Super-admin only |
| `agency-subscriptions` | view, create, edit, delete | Super-admin only |
| `roles-permissions` | view, create, edit, delete | Super-admin only |
| `users` | view, create, edit, delete | Super-admin only |
| `agents` | view, create, edit, delete | |
| `clients` | view, create, edit, delete | |
| `vehicles` | view, create, edit, delete | |
| `vehicle-brands` | view, create, edit, delete | |
| `vehicle-models` | view, create, edit, delete | |
| `vehicle-credits` | view, create, edit, delete | |
| `vehicle-vignettes` | view, create, edit, delete | |
| `vehicle-insurances` | view, create, edit, delete | |
| `vehicle-oil-changes` | view, create, edit, delete | |
| `vehicle-technical-checks` | view, create, edit, delete | |
| `vehicle-controls` | view, create, edit, delete | |
| `vehicle-control-items` | view, create, edit, delete | |
| `bookings` | view, create, edit, delete | |
| `rental-contracts` | view, create, edit, delete | |
| `contract-clients` | view, create, edit, delete | |
| `financial-accounts` | view, create, edit, delete | |
| `transaction-categories` | view, create, edit, delete | |
| `financial-transactions` | view, create, edit, delete | |
| `invoices` | view, create, edit, delete | |
| `invoice-items` | view, create, edit, delete | |
| `payments` | view, create, edit, delete | |
| `reports` | view | Report access |
| `notifications` | view | Notification access |
| `trash` | view, restore, delete | Super-admin only |

### Role → Permission Mapping
| Role | Scope | Access Level |
|------|-------|-------------|
| `super-admin` | Global | ALL permissions, Gate::before bypass |
| `agency-admin` | Agency | All except: agencies, agency-subscriptions, users, roles-permissions, trash |
| `agency-manager` | Agency | View + Create + Edit only (no Delete), same exclusions as admin |
| `agency-staff` | Agency | View only, same exclusions as admin |

### Test Accounts (from seeders)
| Email | Role | Password |
|-------|------|----------|
| super@admin.com | super-admin | password123 |
| admin@agency1.com | agency-admin | password123 |
| manager@agency1.com | agency-manager | password123 |
| staff@agency1.com | agency-staff | password123 |

## Seeders

Run order: `AgencyUserSeeder` → `RolesAndSuperAdminSeeder` → `AgencyRolesPermissionsSeeder`

## Development Commands

```bash
composer run dev          # Runs: artisan serve + npm dev + queue:listen + pail
npm run build             # Production frontend build
npm run sass              # Compile SCSS
php artisan migrate:fresh --seed  # Reset DB with seed data
```

## Best Practices for Future Development

1. **Always scope by agency_id** — Every query for agency-specific data must filter by `agency_id`
2. **Use named routes** — Always `route('backoffice.xxx')`, never hardcoded `url('/admin/xxx')`
3. **View paths lowercase** — Use `view('backoffice.module.view')` (lowercase), never PascalCase
4. **Permissions on routes** — Add `->middleware('can:module.general.action,backoffice')` to all new routes
5. **Sidebar entries** — Add new items to `config/sidebar.php`, not directly in Blade
6. **Financial operations** — Always use DB transactions, reverse balance on delete, use `bccomp()` for decimal comparison
7. **Day calculations** — Use `diffInDays() + 1` to include both start and end day
8. **Form requests** — Create Store/Update request classes in `app/Http/Requests/Backoffice/`
9. **Notifications** — Call `$this->createNotification('action', 'module', $model)` after CRUD operations
10. **New permissions** — Add to `$modulesConfig` in `RolesAndSuperAdminSeeder`, then re-seed

## Important Notes

- Currency: MAD (Moroccan Dirham), configurable per agency
- VAT: Default 20%, configurable per agency via settings JSON
- Financial amounts: `decimal(12,2)` or `decimal(15,2)` for precision
- All dates follow Y-m-d format, times H:i format
- Agency settings stored as nested JSON in `agencies.settings` column
- Media uploads max: 50MB (configurable in config/media-library.php)
- French validation messages used throughout form requests
- Icons: Tabler Icons (`ti ti-*` classes) — see https://tabler.io/icons
