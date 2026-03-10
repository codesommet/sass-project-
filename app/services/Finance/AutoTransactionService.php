<?php

namespace App\Services\Finance;

use App\Models\FinancialTransaction;
use App\Models\FinancialAccount;
use App\Models\RentalContract;
use App\Models\VehicleVignette;
use App\Models\VehicleInsurance;
use App\Models\VehicleTechnicalCheck;
use App\Models\VehicleOilChange;
use App\Models\VehicleCredit;
use App\Models\CreditPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoTransactionService
{
    protected $defaultAccountId;

    public function __construct()
    {
        $this->defaultAccountId = $this->getDefaultAccount();
    }

    /**
     * Get or create default cash account
     */
    protected function getDefaultAccount()
    {
        $agencyId = auth()->user()->agency_id;
        
        $account = FinancialAccount::where('agency_id', $agencyId)
            ->where('is_default', true)
            ->first();
            
        if (!$account) {
            $account = FinancialAccount::create([
                'agency_id' => $agencyId,
                'name' => 'Caisse principale',
                'type' => 'cash',
                'initial_balance' => 0,
                'current_balance' => 0,
                'currency' => 'MAD',
                'is_default' => true
            ]);
        }
        
        return $account->id;
    }

    /**
     * Créer une transaction de revenu automatique lors d'une nouvelle réservation
     */
    public function createRevenueFromReservation(RentalContract $contract)
    {
        try {
            DB::beginTransaction();

            // Vérifier si une transaction existe déjà pour ce contrat
            $exists = FinancialTransaction::where('source_type', 'rental_contract')
                ->where('source_id', $contract->id)
                ->exists();

            if ($exists) {
                DB::commit();
                return null;
            }

            // Calculer le montant total
            $amount = $contract->total_amount;
            
            // Créer la transaction
            $transaction = FinancialTransaction::create([
                'agency_id' => $contract->agency_id,
                'financial_account_id' => $this->defaultAccountId,
                'date' => $contract->start_date,
                'type' => 'income',
                'amount' => $amount,
                'description' => "Revenu location - Contrat #{$contract->contract_number}",
                'reference' => $contract->contract_number,
                'source_type' => 'rental_contract',
                'source_id' => $contract->id,
                'created_by' => auth()->id(),
                'currency' => 'MAD',
                'metadata' => json_encode([
                    'client_principal' => $contract->primaryClient ? $contract->primaryClient->full_name : null,
                    'client_secondaire' => $contract->secondaryClient ? $contract->secondaryClient->full_name : null,
                    'vehicule' => $contract->vehicle ? $contract->vehicle->registration_number : null,
                    'jours' => $contract->planned_days,
                    'tarif_journalier' => $contract->daily_rate,
                    'remise' => $contract->discount_amount,
                    'date_debut' => $contract->start_date->format('d/m/Y'),
                    'date_fin' => $contract->end_date->format('d/m/Y')
                ])
            ]);

            // Mettre à jour le solde du compte
            $this->updateAccountBalance($transaction);

            DB::commit();
            
            Log::info('Transaction de revenu créée automatiquement', [
                'transaction_id' => $transaction->id,
                'contrat' => $contract->contract_number,
                'montant' => $amount
            ]);
            
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création revenu réservation: ' . $e->getMessage(), [
                'contrat_id' => $contract->id,
                'exception' => $e
            ]);
            return null;
        }
    }

    /**
     * Créer une transaction de dépense automatique pour une vignette
     */
    public function createExpenseFromVignette(VehicleVignette $vignette)
    {
        return $this->createExpenseFromDocument(
            $vignette,
            'vignette',
            "Vignette - {$vignette->year}",
            $vignette->amount,
            [
                'annee' => $vignette->year,
                'date' => $vignette->date ? $vignette->date->format('d/m/Y') : null,
                'notes' => $vignette->notes
            ]
        );
    }

    /**
     * Créer une transaction de dépense automatique pour une assurance
     */
    public function createExpenseFromInsurance(VehicleInsurance $insurance)
    {
        return $this->createExpenseFromDocument(
            $insurance,
            'insurance',
            "Assurance - {$insurance->company_name}",
            $insurance->amount,
            [
                'compagnie' => $insurance->company_name,
                'police' => $insurance->policy_number,
                'date' => $insurance->date ? $insurance->date->format('d/m/Y') : null,
                'prochaine_echeance' => $insurance->next_insurance_date ? $insurance->next_insurance_date->format('d/m/Y') : null,
                'notes' => $insurance->notes
            ]
        );
    }

    /**
     * Créer une transaction de dépense automatique pour un contrôle technique
     */
    public function createExpenseFromTechnicalCheck(VehicleTechnicalCheck $technicalCheck)
    {
        return $this->createExpenseFromDocument(
            $technicalCheck,
            'technical_check',
            "Contrôle technique - " . ($technicalCheck->date ? $technicalCheck->date->format('d/m/Y') : ''),
            $technicalCheck->amount,
            [
                'date' => $technicalCheck->date ? $technicalCheck->date->format('d/m/Y') : null,
                'prochain_controle' => $technicalCheck->next_check_date ? $technicalCheck->next_check_date->format('d/m/Y') : null,
                'notes' => $technicalCheck->notes
            ]
        );
    }

    /**
     * Créer une transaction de dépense automatique pour une vidange
     */
    public function createExpenseFromOilChange(VehicleOilChange $oilChange)
    {
        return $this->createExpenseFromDocument(
            $oilChange,
            'oil_change',
            "Vidange - " . ($oilChange->date ? $oilChange->date->format('d/m/Y') : ''),
            $oilChange->amount,
            [
                'mecanicien' => $oilChange->mechanic_name,
                'kilometrage' => $oilChange->mileage,
                'prochain_kilometrage' => $oilChange->next_mileage,
                'date' => $oilChange->date ? $oilChange->date->format('d/m/Y') : null,
                'observations' => $oilChange->observations
            ]
        );
    }

    /**
     * Créer une transaction de dépense automatique pour un paiement de crédit
     */
    public function createExpenseFromCreditPayment(CreditPayment $payment)
    {
        try {
            DB::beginTransaction();

            $credit = $payment->credit;
            
            // Vérifier si une transaction existe déjà pour ce paiement
            $exists = FinancialTransaction::where('source_type', 'credit_payment')
                ->where('source_id', $payment->id)
                ->exists();

            if ($exists) {
                DB::commit();
                return null;
            }

            $montantTotal = $payment->amount + ($payment->penalty ?? 0);
            
            $transaction = FinancialTransaction::create([
                'agency_id' => $credit->agency_id,
                'financial_account_id' => $this->defaultAccountId,
                'date' => $payment->paid_date ?? now(),
                'type' => 'expense',
                'amount' => $montantTotal,
                'description' => "Paiement crédit #{$credit->credit_number} - Mensualité {$payment->payment_number}",
                'reference' => $credit->credit_number,
                'source_type' => 'credit_payment',
                'source_id' => $payment->id,
                'created_by' => auth()->id(),
                'currency' => 'MAD',
                'metadata' => json_encode([
                    'crediteur' => $credit->creditor_name,
                    'numero_mensualite' => $payment->payment_number,
                    'capital' => $payment->principal,
                    'interets' => $payment->interest,
                    'penalite' => $payment->penalty ?? 0,
                    'montant_total' => $montantTotal,
                    'date_echeance' => $payment->due_date ? $payment->due_date->format('d/m/Y') : null
                ])
            ]);

            $this->updateAccountBalance($transaction);

            DB::commit();
            
            Log::info('Transaction de dépense créée automatiquement pour paiement de crédit', [
                'transaction_id' => $transaction->id,
                'credit' => $credit->credit_number,
                'mensualite' => $payment->payment_number,
                'montant' => $montantTotal
            ]);
            
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création dépense crédit: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'exception' => $e
            ]);
            return null;
        }
    }

    /**
     * Méthode générique pour créer une dépense à partir d'un document
     */
    protected function createExpenseFromDocument($document, $type, $description, $amount, $additionalMetadata = [])
    {
        try {
            DB::beginTransaction();

            // Vérifier si une transaction existe déjà pour ce document
            $exists = FinancialTransaction::where('source_type', $type)
                ->where('source_id', $document->id)
                ->exists();

            if ($exists) {
                DB::commit();
                return null;
            }

            $vehicle = $document->vehicle;
            $agencyId = $vehicle ? $vehicle->agency_id : auth()->user()->agency_id;

            // Métadonnées de base
            $metadata = array_merge([
                'vehicule_id' => $vehicle ? $vehicle->id : null,
                'vehicule_immatriculation' => $vehicle ? $vehicle->registration_number : null,
                'vehicule_modele' => $vehicle && $vehicle->model ? $vehicle->model->name : null,
                'date_creation' => now()->format('d/m/Y H:i')
            ], $additionalMetadata);

            $transaction = FinancialTransaction::create([
                'agency_id' => $agencyId,
                'financial_account_id' => $this->defaultAccountId,
                'date' => $document->date ?? $document->created_at ?? now(),
                'type' => 'expense',
                'amount' => $amount,
                'description' => $description,
                'reference' => (string) $document->id,
                'source_type' => $type,
                'source_id' => $document->id,
                'created_by' => auth()->id(),
                'currency' => 'MAD',
                'metadata' => json_encode($metadata)
            ]);

            $this->updateAccountBalance($transaction);

            DB::commit();
            
            Log::info("Transaction de dépense créée automatiquement pour {$type}", [
                'transaction_id' => $transaction->id,
                'document_id' => $document->id,
                'montant' => $amount
            ]);
            
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur création dépense {$type}: " . $e->getMessage(), [
                'document_id' => $document->id,
                'exception' => $e
            ]);
            return null;
        }
    }

    /**
     * Mettre à jour le solde du compte
     */
    protected function updateAccountBalance(FinancialTransaction $transaction)
    {
        $account = $transaction->account;
        
        if ($transaction->type === 'income') {
            $account->current_balance += $transaction->amount;
        } else {
            $account->current_balance -= $transaction->amount;
        }
        
        $account->save();
        
        Log::info('Solde du compte mis à jour', [
            'compte_id' => $account->id,
            'compte_nom' => $account->name,
            'nouveau_solde' => $account->current_balance,
            'transaction_id' => $transaction->id,
            'type' => $transaction->type,
            'montant' => $transaction->amount
        ]);
    }

    /**
     * Supprimer la transaction associée à un document (utilisé lors de la suppression)
     */
    public function deleteTransactionForSource($sourceType, $sourceId)
    {
        try {
            $transaction = FinancialTransaction::where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->first();
                
            if ($transaction) {
                DB::beginTransaction();
                
                // Inverser l'effet sur le solde du compte
                $account = $transaction->account;
                if ($transaction->type === 'income') {
                    $account->current_balance -= $transaction->amount;
                } else {
                    $account->current_balance += $transaction->amount;
                }
                $account->save();
                
                $transaction->delete();
                
                DB::commit();
                
                Log::info('Transaction supprimée', [
                    'source_type' => $sourceType,
                    'source_id' => $sourceId
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur suppression transaction: ' . $e->getMessage());
        }
    }
}