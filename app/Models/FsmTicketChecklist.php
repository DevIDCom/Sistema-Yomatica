<?php

namespace App\Models;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FsmTicketChecklist extends Model {
    public $timestamps = false;
    protected $fillable = ['ticket_id', 'data', 'completed_at', 'client_updated_at', 'server_updated_at'];

    protected $casts = [
        'data' => 'array',
        'completed_at' => 'datetime',
        'client_updated_at' => 'datetime',
        'server_updated_at' => 'datetime',
    ];

    public function ticket(): BelongsTo { return $this->belongsTo(FsmTicket::class, 'ticket_id'); }
}
