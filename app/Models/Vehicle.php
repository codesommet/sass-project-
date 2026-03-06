<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Vehicle extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'agency_id',
        'vehicle_model_id',
        'registration_number',
        'registration_city',
        'year',
        'color',
        'current_mileage',
        'status',
        'daily_rate',
        'deposit_amount',
        'has_gps',
        'has_air_conditioning',
        'has_bluetooth',
        'has_baby_seat', // Changed from has_usb
        'has_camera_recul',
        'has_regulateur_vitesse',
        'has_siege_chauffant',
        'notes',
        'vin',
        'fuel_policy',
        'fuel_level_out',
        'fuel_level_in',
    ];

    protected $casts = [
        'year' => 'integer',
        'current_mileage' => 'integer',
        'daily_rate' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'has_gps' => 'boolean',
        'has_air_conditioning' => 'boolean',
        'has_bluetooth' => 'boolean',
        'has_baby_seat' => 'boolean', // Changed from has_usb
        'has_camera_recul' => 'boolean',
        'has_regulateur_vitesse' => 'boolean',
        'has_siege_chauffant' => 'boolean',
        'fuel_level_out' => 'decimal:2',
        'fuel_level_in' => 'decimal:2',
    ];

    // Alias accessor for Blade compatibility
    public function getFeaturedImageUrlAttribute(): string
    {
        return $this->getMainPhotoUrlAttribute();
    }

/**
 * Get the credits for the vehicle.
 */
public function credits()
{
    return $this->hasMany(VehicleCredit::class);
}

/**
 * Get the most recent credit for the vehicle.
 */
public function latestCredit()
{
    return $this->hasOne(VehicleCredit::class)->latestOfMany();
}

public function activeCredit()
{
    return $this->hasOne(VehicleCredit::class)->where('status', 'active');
}

    /* =======================
     |  MEDIA LIBRARY
     ======================= */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('vehicle_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->useFallbackUrl('/assets/place-holder.webp')
            ->useFallbackPath(public_path('assets/place-holder.webp'));

        $this->addMediaCollection('vehicle_documents')
            ->acceptsMimeTypes([
                'application/pdf',
                'image/jpeg',
                'image/png',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);
    }

    // Helpers
    public function getMainPhotoUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('vehicle_photos') ?: '/assets/place-holder.webp';
    }

    public function getPhotosAttribute()
    {
        return $this->getMedia('vehicle_photos');
    }

    public function getDocumentsAttribute()
    {
        return $this->getMedia('vehicle_documents');
    }

    /* =======================
     |  EQUIPMENT ACCESSORS
     ======================= */

    /**
     * Get all equipment options with labels and icons.
     */
    public function getEquipmentListAttribute(): array
    {
        return [
            'has_gps' => [
                'label' => 'GPS',
                'value' => $this->has_gps,
                'icon' => 'ti ti-map-pin',
                'color' => 'primary'
            ],
            'has_air_conditioning' => [
                'label' => 'Climatisation',
                'value' => $this->has_air_conditioning,
                'icon' => 'ti ti-snowflake',
                'color' => 'info'
            ],
            'has_bluetooth' => [
                'label' => 'Bluetooth',
                'value' => $this->has_bluetooth,
                'icon' => 'ti ti-bluetooth',
                'color' => 'primary'
            ],
            'has_baby_seat' => [
                'label' => 'Siège bébé',
                'value' => $this->has_baby_seat,
                'icon' => 'ti ti-baby-carriage',
                'color' => 'success'
            ],
            'has_camera_recul' => [
                'label' => 'Caméra de recul',
                'value' => $this->has_camera_recul,
                'icon' => 'ti ti-camera',
                'color' => 'warning'
            ],
            'has_regulateur_vitesse' => [
                'label' => 'Régulateur de vitesse',
                'value' => $this->has_regulateur_vitesse,
                'icon' => 'ti ti-speedometer',
                'color' => 'danger'
            ],
            'has_siege_chauffant' => [
                'label' => 'Sièges chauffants',
                'value' => $this->has_siege_chauffant,
                'icon' => 'ti ti-heat',
                'color' => 'warning'
            ],
        ];
    }

    /**
     * Get formatted equipment list for display.
     */
    public function getFormattedEquipmentAttribute(): string
    {
        $equipment = [];
        foreach ($this->equipment_list as $item) {
            if ($item['value']) {
                $equipment[] = $item['label'];
            }
        }
        
        return empty($equipment) ? 'Aucun équipement' : implode(' • ', $equipment);
    }

    /**
     * Get equipment count.
     */
    public function getEquipmentCountAttribute(): int
    {
        $count = 0;
        foreach ($this->equipment_list as $item) {
            if ($item['value']) {
                $count++;
            }
        }
        return $count;
    }

    /* =======================
     |  RELATIONSHIPS
     ======================= */

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }

    // Accessor simple (pas une relation)
    public function getBrandAttribute()
    {
        return $this->relationLoaded('model')
            ? ($this->model?->brand)
            : $this->model()->with('brand')->first()?->brand;
    }

    public function vignettes()
    {
        return $this->hasMany(VehicleVignette::class);
    }

    public function insurances()
    {
        return $this->hasMany(VehicleInsurance::class);
    }

    public function technicalChecks()
    {
        return $this->hasMany(VehicleTechnicalCheck::class);
    }

    public function oilChanges()
    {
        return $this->hasMany(VehicleOilChange::class);
    }

    public function controls()
    {
        return $this->hasMany(VehicleControl::class);
    }
}