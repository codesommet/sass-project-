<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Client extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;
    
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'agency_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'nationality',
        'birth_date',
        'cin_number',
        'cin_valid_until',
        'passport_number',
        'passport_issue_date',
        'driving_license_number',
        'driving_license_issue_date',
        'status',
        'rating_average',
        'rating_count',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'cin_valid_until' => 'date',
        'passport_issue_date' => 'date',
        'driving_license_issue_date' => 'date',
        'rating_average' => 'decimal:2',
        'rating_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /* =======================
     |  MEDIA LIBRARY
     ======================= */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('client_avatar')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'])
            ->useFallbackUrl('/images/placeholder-client.jpg')
            ->useFallbackPath('/images/placeholder-client.jpg');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10)
            ->nonQueued();
    }

    /* =======================
     |  ACCESSORS
     ======================= */

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('client_avatar') ?: null;
    }

    public function getAvatarThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('client_avatar', 'thumb') ?: null;
    }

    public function getAvatarMediaAttribute()
    {
        return $this->getFirstMedia('client_avatar');
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getInitialsAttribute(): string
    {
        $first = substr($this->first_name, 0, 1);
        $last = substr($this->last_name, 0, 1);
        return strtoupper($first . $last);
    }

    public function hasAvatar(): bool
    {
        return $this->getFirstMedia('client_avatar') !== null;
    }

    /* =======================
     |  RELATIONSHIPS
     ======================= */

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function blacklistEntries()
    {
        return $this->hasMany(BlacklistedClient::class);
    }

    public function isBlacklisted(): bool
    {
        return $this->status === 'blacklisted';
    }

    public function currentBlacklistEntry()
    {
        return $this->hasOne(BlacklistedClient::class)->latestOfMany();
    }
}