<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\RentalContract;
use App\Models\Vehicle;
use App\Models\Client;
use App\Models\Booking;
use App\Models\Agency;
use App\Models\Agent;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Models\AgencySubscription;
use App\Models\ContractClient;
use App\Models\FinancialAccount;
use App\Models\TransactionCategory;
use App\Models\FinancialTransaction;
use App\Models\InvoiceItem;
use App\Models\VehicleControl;
use App\Models\VehicleControlItem;
use App\Models\VehicleInsurance;
use App\Models\VehicleOilChange;
use App\Models\VehicleTechnicalCheck;
use App\Models\VehicleVignette;
use App\Models\VehicleCredit; // AJOUTER CETTE LIGNE
use App\Models\CreditPayment; // AJOUTER CETTE LIGNE
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrashController extends Controller
{
    public function index()
    {
        $modules = [
            'rental-contracts' => ['model' => RentalContract::class, 'name' => 'Contrats', 'icon' => 'ti ti-file-text'],
            'vehicles' => ['model' => Vehicle::class, 'name' => 'Véhicules', 'icon' => 'ti ti-car'],
            'clients' => ['model' => Client::class, 'name' => 'Clients', 'icon' => 'ti ti-users'],
            'bookings' => ['model' => Booking::class, 'name' => 'Réservations', 'icon' => 'ti ti-calendar-stats'],
            'agencies' => ['model' => Agency::class, 'name' => 'Agences', 'icon' => 'ti ti-building'],
            'agents' => ['model' => Agent::class, 'name' => 'Agents', 'icon' => 'ti ti-user-circle'],
            'users' => ['model' => User::class, 'name' => 'Utilisateurs', 'icon' => 'ti ti-users'],
            'invoices' => ['model' => Invoice::class, 'name' => 'Factures', 'icon' => 'ti ti-file-invoice'],
            'payments' => ['model' => Payment::class, 'name' => 'Paiements', 'icon' => 'ti ti-currency-dollar'],
            'vehicle-brands' => ['model' => VehicleBrand::class, 'name' => 'Marques', 'icon' => 'ti ti-brand'],
            'vehicle-models' => ['model' => VehicleModel::class, 'name' => 'Modèles', 'icon' => 'ti ti-model'],
            'agency-subscriptions' => ['model' => AgencySubscription::class, 'name' => 'Abonnements', 'icon' => 'ti ti-crown'],
            'contract-clients' => ['model' => ContractClient::class, 'name' => 'Clients contrat', 'icon' => 'ti ti-users'],
            'financial-accounts' => ['model' => FinancialAccount::class, 'name' => 'Comptes', 'icon' => 'ti ti-building-bank'],
            'transaction-categories' => ['model' => TransactionCategory::class, 'name' => 'Catégories', 'icon' => 'ti ti-category'],
            'financial-transactions' => ['model' => FinancialTransaction::class, 'name' => 'Transactions', 'icon' => 'ti ti-transfer'],
            'invoice-items' => ['model' => InvoiceItem::class, 'name' => 'Items facture', 'icon' => 'ti ti-file-description'],
            'vehicle-vignettes' => ['model' => VehicleVignette::class, 'name' => 'Vignettes', 'icon' => 'ti ti-ticket'],
            'vehicle-insurances' => ['model' => VehicleInsurance::class, 'name' => 'Assurances', 'icon' => 'ti ti-shield'],
            'vehicle-oil-changes' => ['model' => VehicleOilChange::class, 'name' => 'Vidanges', 'icon' => 'ti ti-droplet'],
            'vehicle-technical-checks' => ['model' => VehicleTechnicalCheck::class, 'name' => 'Contrôles tech.', 'icon' => 'ti ti-clipboard-check'],
            'vehicle-controls' => ['model' => VehicleControl::class, 'name' => 'Contrôles', 'icon' => 'ti ti-clipboard-list'],
            'vehicle-control-items' => ['model' => VehicleControlItem::class, 'name' => 'Items contrôle', 'icon' => 'ti ti-checklist'],
            // AJOUTER LES CRÉDITS ICI
            'vehicle-credits' => ['model' => VehicleCredit::class, 'name' => 'Crédits véhicules', 'icon' => 'ti ti-credit-card'],
        ];

        $data = [];
        $total = 0;

        foreach ($modules as $key => $module) {
            try {
                $count = $module['model']::onlyTrashed()->count();
                
                if ($count > 0) {
                    $data[$key] = [
                        'name' => $module['name'],
                        'icon' => $module['icon'],
                        'count' => $count,
                        'items' => $module['model']::onlyTrashed()->latest('deleted_at')->get()
                    ];
                    $total += $count;
                }
            } catch (\Exception $e) {
                // Skip modules that have errors
                continue;
            }
        }

        return view('backoffice.trash.index', [
            'data' => $data,
            'total' => $total
        ]);
    }

    public function getModuleData($module)
    {
        $models = [
            'rental-contracts' => ['model' => RentalContract::class, 'fields' => ['id', 'contract_number as name', 'deleted_at']],
            'vehicles' => ['model' => Vehicle::class, 'fields' => ['id', 'registration_number as name', 'deleted_at']],
            'clients' => ['model' => Client::class, 'fields' => ['id', DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'deleted_at']],
            'bookings' => ['model' => Booking::class, 'fields' => ['id', DB::raw("CONCAT('#', id) as name"), 'deleted_at']],
            'agencies' => ['model' => Agency::class, 'fields' => ['id', 'name', 'deleted_at']],
            'agents' => ['model' => Agent::class, 'fields' => ['id', DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'deleted_at']],
            'users' => ['model' => User::class, 'fields' => ['id', 'name', 'deleted_at']],
            'invoices' => ['model' => Invoice::class, 'fields' => ['id', 'invoice_number as name', 'deleted_at']],
            'payments' => ['model' => Payment::class, 'fields' => ['id', DB::raw("CONCAT('Paiement #', id) as name"), 'deleted_at']],
            'vehicle-brands' => ['model' => VehicleBrand::class, 'fields' => ['id', 'name', 'deleted_at']],
            'vehicle-models' => ['model' => VehicleModel::class, 'fields' => ['id', 'name', 'deleted_at']],
            'agency-subscriptions' => ['model' => AgencySubscription::class, 'fields' => ['id', 'plan_name as name', 'deleted_at']],
            'contract-clients' => ['model' => ContractClient::class, 'fields' => ['id', DB::raw("CONCAT('Client #', client_id) as name"), 'deleted_at']],
            'financial-accounts' => ['model' => FinancialAccount::class, 'fields' => ['id', 'name', 'deleted_at']],
            'transaction-categories' => ['model' => TransactionCategory::class, 'fields' => ['id', 'name', 'deleted_at']],
            'financial-transactions' => ['model' => FinancialTransaction::class, 'fields' => ['id', DB::raw("CONCAT('Transaction #', id) as name"), 'deleted_at']],
            'invoice-items' => ['model' => InvoiceItem::class, 'fields' => ['id', 'description as name', 'deleted_at']],
            'vehicle-vignettes' => ['model' => VehicleVignette::class, 'fields' => ['id', DB::raw("CONCAT('Vignette #', id) as name"), 'deleted_at']],
            'vehicle-insurances' => ['model' => VehicleInsurance::class, 'fields' => ['id', DB::raw("CONCAT('Assurance #', id) as name"), 'deleted_at']],
            'vehicle-oil-changes' => ['model' => VehicleOilChange::class, 'fields' => ['id', DB::raw("CONCAT('Vidange #', id) as name"), 'deleted_at']],
            'vehicle-technical-checks' => ['model' => VehicleTechnicalCheck::class, 'fields' => ['id', DB::raw("CONCAT('Contrôle #', id) as name"), 'deleted_at']],
            'vehicle-controls' => ['model' => VehicleControl::class, 'fields' => ['id', 'name', 'deleted_at']],
            'vehicle-control-items' => ['model' => VehicleControlItem::class, 'fields' => ['id', 'name', 'deleted_at']],
            // AJOUTER LES CRÉDITS ICI
            'vehicle-credits' => ['model' => VehicleCredit::class, 'fields' => ['id', 'credit_number as name', 'deleted_at']],
        ];

        if (!isset($models[$module])) {
            return response()->json(['error' => 'Module introuvable'], 404);
        }

        $config = $models[$module];
        $items = $config['model']::onlyTrashed()
            ->select($config['fields'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name ?? 'N/A',
                    'deleted_at' => $item->deleted_at->format('d/m/Y H:i')
                ];
            });

        return response()->json($items);
    }

    public function restore($module, $id)
    {
        $models = $this->getModelMap();
        
        if (!isset($models[$module])) {
            return redirect()->back()->with('error', 'Module non trouvé');
        }

        $model = $models[$module]::onlyTrashed()->findOrFail($id);
        $model->restore();

        return redirect()->route('backoffice.trash.index')
            ->with('toast', [
                'title' => 'Restauré',
                'message' => 'L\'élément a été restauré avec succès.',
                'dot' => '#28a745',
                'delay' => 3500,
                'time' => 'now'
            ]);
    }

    public function forceDelete($module, $id)
    {
        $models = $this->getModelMap();
        
        if (!isset($models[$module])) {
            return redirect()->back()->with('error', 'Module non trouvé');
        }

        $model = $models[$module]::onlyTrashed()->findOrFail($id);
        
        // Cas spécial pour les crédits - supprimer aussi les paiements
        if ($module === 'vehicle-credits') {
            $model->payments()->forceDelete();
        }
        
        $model->forceDelete();

        return redirect()->route('backoffice.trash.index')
            ->with('toast', [
                'title' => 'Supprimé',
                'message' => 'L\'élément a été supprimé définitivement.',
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now'
            ]);
    }

    public function restoreAll($module)
    {
        $models = $this->getModelMap();
        
        if (!isset($models[$module])) {
            return redirect()->back()->with('error', 'Module non trouvé');
        }

        $count = $models[$module]::onlyTrashed()->count();
        $models[$module]::onlyTrashed()->restore();

        return redirect()->route('backoffice.trash.index')
            ->with('toast', [
                'title' => 'Restaurés',
                'message' => $count . ' élément(s) ont été restaurés.',
                'dot' => '#28a745',
                'delay' => 3500,
                'time' => 'now'
            ]);
    }

    public function forceDeleteAll($module)
    {
        $models = $this->getModelMap();
        
        if (!isset($models[$module])) {
            return redirect()->back()->with('error', 'Module non trouvé');
        }

        $count = $models[$module]::onlyTrashed()->count();
        
        // Cas spécial pour les crédits - supprimer aussi les paiements
        if ($module === 'vehicle-credits') {
            $credits = $models[$module]::onlyTrashed()->get();
            foreach ($credits as $credit) {
                $credit->payments()->forceDelete();
            }
        }
        
        $models[$module]::onlyTrashed()->forceDelete();

        return redirect()->route('backoffice.trash.index')
            ->with('toast', [
                'title' => 'Supprimés',
                'message' => $count . ' élément(s) ont été supprimés définitivement.',
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now'
            ]);
    }

    public function emptyAll()
    {
        $models = $this->getModelMap();
        
        $total = 0;
        DB::beginTransaction();
        
        try {
            foreach ($models as $key => $model) {
                $count = $model::onlyTrashed()->count();
                $total += $count;
                
                // Cas spécial pour les crédits - supprimer les paiements d'abord
                if ($key === 'vehicle-credits') {
                    $credits = $model::onlyTrashed()->get();
                    foreach ($credits as $credit) {
                        $credit->payments()->forceDelete();
                    }
                }
                
                $model::onlyTrashed()->forceDelete();
            }
            
            DB::commit();
            
            return redirect()
                ->route('backoffice.trash.index')
                ->with('toast', [
                    'title' => 'Corbeille vidée',
                    'message' => $total . ' élément(s) ont été supprimés définitivement.',
                    'dot' => '#dc3545',
                    'delay' => 3500,
                    'time' => 'now'
                ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->route('backoffice.trash.index')
                ->with('toast', [
                    'title' => 'Erreur',
                    'message' => 'Une erreur est survenue lors du vidage de la corbeille.',
                    'dot' => '#dc3545',
                    'delay' => 3500,
                    'time' => 'now'
                ]);
        }
    }

    private function getModelMap()
    {
        return [
            'rental-contracts' => RentalContract::class,
            'vehicles' => Vehicle::class,
            'clients' => Client::class,
            'bookings' => Booking::class,
            'agencies' => Agency::class,
            'agents' => Agent::class,
            'users' => User::class,
            'invoices' => Invoice::class,
            'payments' => Payment::class,
            'vehicle-brands' => VehicleBrand::class,
            'vehicle-models' => VehicleModel::class,
            'agency-subscriptions' => AgencySubscription::class,
            'contract-clients' => ContractClient::class,
            'financial-accounts' => FinancialAccount::class,
            'transaction-categories' => TransactionCategory::class,
            'financial-transactions' => FinancialTransaction::class,
            'invoice-items' => InvoiceItem::class,
            'vehicle-vignettes' => VehicleVignette::class,
            'vehicle-insurances' => VehicleInsurance::class,
            'vehicle-oil-changes' => VehicleOilChange::class,
            'vehicle-technical-checks' => VehicleTechnicalCheck::class,
            'vehicle-controls' => VehicleControl::class,
            'vehicle-control-items' => VehicleControlItem::class,
            // AJOUTER LES CRÉDITS ICI
            'vehicle-credits' => VehicleCredit::class,
        ];
    }
}