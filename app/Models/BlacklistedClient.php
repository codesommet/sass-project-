<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlacklistedClient extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blacklisted_clients';

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

    /**
     * Get the client that is blacklisted
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the agency that blacklisted the client
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the user who blacklisted the client
     */
    public function blacklistedBy()
    {
        return $this->belongsTo(User::class, 'blacklisted_by');
    }

    /**
     * Get formatted created at date
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }
}