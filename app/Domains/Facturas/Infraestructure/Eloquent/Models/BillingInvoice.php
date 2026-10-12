<?php

namespace App\Domains\Facturas\Infraestructure\Eloquent\Models;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use App\Domains\Productos\Infraestructure\Eloquent\Models\BillingInvoiceItem;
use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\Evidence;
use App\Models\BillingPaymentComplement;
use App\Models\ProjectBillingMilestone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingInvoice extends Model {
    public $timestamps = false;
    protected $fillable = [
        'uuid', 'project_id', 'milestone_id', 'quote_id', 'client_id',
        'facturapi_id', 'fiscal_uuid', 'status', 'cfdi_use', 'tax_regime',
        'receiver_zip_code', 'total'
    ];

    public function client(): BelongsTo { return $this->belongsTo(CrmClient::class, 'client_id'); }
    public function project(): BelongsTo { return $this->belongsTo(FsmProject::class, 'project_id'); }
    public function milestone(): BelongsTo { return $this->belongsTo(ProjectBillingMilestone::class, 'milestone_id'); }
    public function items(): HasMany { return $this->hasMany(BillingInvoiceItem::class, 'invoice_id'); }
    public function paymentComplements(): HasMany { return $this->hasMany(BillingPaymentComplement::class, 'invoice_id'); }
    public function evidences(): HasMany { return $this->hasMany(Evidence::class, 'invoice_id'); }
}
