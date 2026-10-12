<?php

namespace App\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PwaSyncLog extends Model {
    public $timestamps = false;
    protected $fillable = ['user_id', 'batch_uuid', 'payload', 'status', 'has_conflict', 'conflict_details'];

    protected $casts = [
        'payload' => 'array',
        'conflict_details' => 'array',
        'has_conflict' => 'boolean',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
