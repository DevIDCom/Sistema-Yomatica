<?php

namespace App\Domains\Tickets\Infrastructure\Eloquent\Models;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use App\Domains\Facturas\Infraestructure\Eloquent\Models\BillingInvoice;
use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use App\Domains\Tickets\Domain\Observers\EvidenceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
#[ObservedBy([EvidenceObserver::class])]
class Evidence extends Model {
    Protected $table = 'evidences';
    public $timestamps = false;
    protected $fillable = [
        'client_uuid', 'ticket_id', 'project_id', 'invoice_id', 'client_id',
        'file_path', 's3_key', 'file_type', 'location', 'is_out_of_bounds'
    ];

    public function ticket(): BelongsTo { return $this->belongsTo(FsmTicket::class, 'ticket_id'); }
    public function project(): BelongsTo { return $this->belongsTo(FsmProject::class, 'project_id'); }
    public function invoice(): BelongsTo { return $this->belongsTo(BillingInvoice::class, 'invoice_id'); }
    public function client(): BelongsTo { return $this->belongsTo(CrmClient::class, 'client_id'); }
}
