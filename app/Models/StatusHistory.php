<?php

namespace App\Models;

use App\Domains\Cotizaciones\Infraestructure\Eloquent\Models\CpqQuote;
use App\Domains\Facturas\Infraestructure\Enloquent\Models\BillingInvoice;
use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusHistory extends Model {
    public $timestamps = false;
    protected $fillable = ['quote_id', 'project_id', 'ticket_id', 'invoice_id', 'user_id', 'from_status', 'to_status'];

    public function quote(): BelongsTo { return $this->belongsTo(CpqQuote::class, 'quote_id'); }
    public function project(): BelongsTo { return $this->belongsTo(FsmProject::class, 'project_id'); }
    public function ticket(): BelongsTo { return $this->belongsTo(FsmTicket::class, 'ticket_id'); }
    public function invoice(): BelongsTo { return $this->belongsTo(BillingInvoice::class, 'invoice_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
