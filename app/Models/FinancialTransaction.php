<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialTransaction extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $dates = ['deleted_at'];
    protected $table = 'financial_transactions';

    protected $fillable = [
        'agency_id',
        'financial_account_id',
        'transaction_category_id',
        'date',
        'amount',
        'type',
        'description',
        'reference',
        'source_type',
        'source_id',
        'metadata',
        'created_by',
        'currency',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the agency that owns the transaction.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the account for this transaction.
     */
    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    /**
     * Get the category for this transaction.
     */
    public function category()
    {
        return $this->belongsTo(TransactionCategory::class, 'transaction_category_id');
    }

    /**
     * Get the user who created the transaction.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the source model (polymorphic-like relationship).
     */
    public function source()
    {
        return $this->morphTo('source', 'source_type', 'source_id');
    }

    /**
     * Get the rental contract associated with this transaction.
     */
    public function rentalContract()
    {
        return $this->belongsTo(RentalContract::class, 'source_id')
            ->where('source_type', 'rental_contract');
    }

    /**
     * Get the vignette associated with this transaction.
     */
    public function vignette()
    {
        return $this->belongsTo(VehicleVignette::class, 'source_id')
            ->where('source_type', 'vignette');
    }

    /**
     * Get the insurance associated with this transaction.
     */
    public function insurance()
    {
        return $this->belongsTo(VehicleInsurance::class, 'source_id')
            ->where('source_type', 'insurance');
    }

    /**
     * Get the technical check associated with this transaction.
     */
    public function technicalCheck()
    {
        return $this->belongsTo(VehicleTechnicalCheck::class, 'source_id')
            ->where('source_type', 'technical_check');
    }

    /**
     * Get the oil change associated with this transaction.
     */
    public function oilChange()
    {
        return $this->belongsTo(VehicleOilChange::class, 'source_id')
            ->where('source_type', 'oil_change');
    }

    /**
     * Get the credit payment associated with this transaction.
     */
    public function creditPayment()
    {
        return $this->belongsTo(CreditPayment::class, 'source_id')
            ->where('source_type', 'credit_payment');
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->type === 'income' ? '+' : '-';
        return $prefix . ' ' . number_format($this->amount, 2, ',', ' ') . ' ' . ($this->currency ?? 'MAD');
    }

    /**
     * Get formatted date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date ? $this->date->format('d/m/Y') : 'N/A';
    }

    /**
     * Get type badge class.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return $this->type === 'income' ? 'badge-success' : 'badge-danger';
    }

    /**
     * Get type text.
     */
    public function getTypeTextAttribute(): string
    {
        return $this->type === 'income' ? 'Revenu' : 'Dépense';
    }

    /**
     * Get source label and icon.
     */
    public function getSourceInfoAttribute(): array
    {
        $sources = [
            'rental_contract' => ['label' => 'Contrat de location', 'icon' => 'ti ti-file-text', 'color' => 'primary'],
            'vignette' => ['label' => 'Vignette', 'icon' => 'ti ti-ticket', 'color' => 'info'],
            'insurance' => ['label' => 'Assurance', 'icon' => 'ti ti-shield', 'color' => 'success'],
            'technical_check' => ['label' => 'Contrôle technique', 'icon' => 'ti ti-clipboard-check', 'color' => 'warning'],
            'oil_change' => ['label' => 'Vidange', 'icon' => 'ti ti-droplet', 'color' => 'danger'],
            'credit_payment' => ['label' => 'Paiement de crédit', 'icon' => 'ti ti-credit-card', 'color' => 'secondary'],
        ];

        return $sources[$this->source_type] ?? ['label' => 'Manuelle', 'icon' => 'ti ti-file', 'color' => 'secondary'];
    }

    /**
     * Get source badge HTML.
     */
    public function getSourceBadgeAttribute(): string
    {
        $info = $this->source_info;
        return '<span class="badge bg-' . $info['color'] . ' text-white"><i class="' . $info['icon'] . ' me-1"></i>' . $info['label'] . '</span>';
    }

    /**
     * Get metadata as array.
     */
    public function getMetadataArrayAttribute(): array
    {
        return $this->metadata ? (is_string($this->metadata) ? json_decode($this->metadata, true) : $this->metadata) : [];
    }

    /**
     * Get a specific metadata value.
     */
    public function getMetadataValue(string $key, $default = null)
    {
        $metadata = $this->metadata_array;
        return $metadata[$key] ?? $default;
    }

    /**
     * Scope a query to only include income.
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope a query to only include expenses.
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeInDateRange($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    /**
     * Scope a query to filter by source type.
     */
    public function scopeOfSource($query, $type, $id = null)
    {
        if ($id) {
            return $query->where('source_type', $type)->where('source_id', $id);
        }
        return $query->where('source_type', $type);
    }

    /**
     * Scope a query to get transactions for a specific vehicle.
     */
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where(function($q) use ($vehicleId) {
            $q->whereHas('rentalContract', function($sub) use ($vehicleId) {
                $sub->where('vehicle_id', $vehicleId);
            })->orWhereHas('vignette', function($sub) use ($vehicleId) {
                $sub->where('vehicle_id', $vehicleId);
            })->orWhereHas('insurance', function($sub) use ($vehicleId) {
                $sub->where('vehicle_id', $vehicleId);
            })->orWhereHas('technicalCheck', function($sub) use ($vehicleId) {
                $sub->where('vehicle_id', $vehicleId);
            })->orWhereHas('oilChange', function($sub) use ($vehicleId) {
                $sub->where('vehicle_id', $vehicleId);
            })->orWhereHas('creditPayment', function($sub) use ($vehicleId) {
                $sub->whereHas('credit', function($c) use ($vehicleId) {
                    $c->where('vehicle_id', $vehicleId);
                });
            });
        });
    }

    /**
     * Get the color class for amount.
     */
    public function getAmountColorClassAttribute(): string
    {
        return $this->type === 'income' ? 'amount-income' : 'amount-expense';
    }

    /**
     * Get the source link if available.
     */
    public function getSourceLinkAttribute(): ?string
    {
        switch ($this->source_type) {
            case 'rental_contract':
                return $this->source_id ? route('backoffice.rental-contracts.show', $this->source_id) : null;
            case 'vignette':
                return $this->source_id && $this->vignette && $this->vignette->vehicle 
                    ? route('backoffice.vehicles.vignettes.show', ['vehicle' => $this->vignette->vehicle_id, 'vignette' => $this->source_id])
                    : null;
            case 'insurance':
                return $this->source_id && $this->insurance && $this->insurance->vehicle
                    ? route('backoffice.vehicles.insurances.show', ['vehicle' => $this->insurance->vehicle_id, 'insurance' => $this->source_id])
                    : null;
            case 'technical_check':
                return $this->source_id && $this->technicalCheck && $this->technicalCheck->vehicle
                    ? route('backoffice.vehicles.technical-checks.show', ['vehicle' => $this->technicalCheck->vehicle_id, 'technicalCheck' => $this->source_id])
                    : null;
            case 'oil_change':
                return $this->source_id && $this->oilChange && $this->oilChange->vehicle
                    ? route('backoffice.vehicles.oil-changes.show', ['vehicle' => $this->oilChange->vehicle_id, 'oilChange' => $this->source_id])
                    : null;
            default:
                return null;
        }
    }

    /**
     * Get the transaction summary for display.
     */
    public function getSummaryAttribute(): string
    {
        $info = $this->source_info;
        
        if ($this->source_type) {
            return $info['label'] . ' - ' . $this->description;
        }
        
        return $this->description ?? 'Transaction manuelle';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($transaction) {
            if ($transaction->account) {
                $transaction->account->updateBalance();
            }
        });

        static::updated(function ($transaction) {
            if ($transaction->account) {
                $transaction->account->updateBalance();
            }
        });

        static::deleted(function ($transaction) {
            if ($transaction->account) {
                $transaction->account->updateBalance();
            }
        });
    }
}