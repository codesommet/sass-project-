<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalContract extends Model
{
    use HasFactory, SoftDeletes;
  protected $dates = ['deleted_at'];
    protected $table = 'rental_contracts';

    protected $fillable = [
        'agency_id',
        'contract_number',
        'vehicle_id',
        'primary_client_id',
        'secondary_client_id',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'pickup_location',
        'dropoff_location',
        'planned_days',
        'daily_rate',
        'discount_amount',
        'total_amount',
        'deposit_amount',
        'status',
        'acceptance_status',
        'source',
        'observations',
        'created_by',
        'updated_by',
        'start_at',
        'end_at',
        'actual_start_at',
        'actual_end_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'actual_start_at' => 'datetime',
        'actual_end_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'planned_days' => 'integer',
        'daily_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    // Relationships
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
 * Get the financial transaction associated with this contract.
 */
public function financialTransaction()
{
    return $this->morphOne(FinancialTransaction::class, 'source', 'source_type', 'source_id');
}

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function primaryClient()
    {
        return $this->belongsTo(Client::class, 'primary_client_id');
    }

    public function secondaryClient()
    {
        return $this->belongsTo(Client::class, 'secondary_client_id');
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'contract_clients')
            ->withPivot('role', 'order')
            ->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getFormattedContractNumberAttribute(): string
    {
        return $this->contract_number ?? 'N/A';
    }

    public function getFormattedStartDateAttribute(): string
    {
        return $this->start_date ? $this->start_date->format('d/m/Y') : 'N/A';
    }

    public function getFormattedEndDateAttribute(): string
    {
        return $this->end_date ? $this->end_date->format('d/m/Y') : 'N/A';
    }

    public function getFormattedStartTimeAttribute(): string
    {
        return $this->start_time ? date('H:i', strtotime($this->start_time)) : 'N/A';
    }

    public function getFormattedEndTimeAttribute(): string
    {
        return $this->end_time ? date('H:i', strtotime($this->end_time)) : 'N/A';
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return number_format($this->total_amount, 2, ',', ' ') . ' MAD';
    }

    public function getFormattedDailyRateAttribute(): string
    {
        return number_format($this->daily_rate, 2, ',', ' ') . ' MAD';
    }

    public function getFormattedDepositAttribute(): string
    {
        return $this->deposit_amount ? number_format($this->deposit_amount, 2, ',', ' ') . ' MAD' : '—';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft' => 'badge-secondary',
            'pending' => 'badge-warning',
            'accepted' => 'badge-info',
            'in_progress' => 'badge-primary',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Brouillon',
            'pending' => 'En attente',
            'accepted' => 'Accepté',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
            default => $this->status,
        };
    }

    public function getAcceptanceBadgeClassAttribute(): string
    {
        return match($this->acceptance_status) {
            'pending' => 'badge-warning',
            'accepted' => 'badge-success',
            'rejected' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    public function getAcceptanceTextAttribute(): string
    {
        return match($this->acceptance_status) {
            'pending' => 'En attente',
            'accepted' => 'Accepté',
            'rejected' => 'Rejeté',
            default => $this->acceptance_status,
        };
    }

    public function getSourceIconAttribute(): string
    {
        return match($this->source) {
            'backoffice' => 'ti ti-device-laptop',
            'website' => 'ti ti-world',
            'mobile' => 'ti ti-device-mobile',
            default => 'ti ti-file',
        };
    }
}