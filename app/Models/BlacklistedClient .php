<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlacklistedClient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'agency_id',
        'blacklisted_by',
        'reason',
        'internal_notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function blacklistedBy()
    {
        return $this->belongsTo(User::class, 'blacklisted_by');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }
}