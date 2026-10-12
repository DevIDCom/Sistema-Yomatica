<?php

namespace App\Models;

use App\Domains\Facturas\Infraestructure\Enloquent\Models\BillingInvoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingPaymentComplement extends Model {
    public $timestamps = false;
    protected $fillable = ['invoice_id', 'facturapi_payment_id', 'amount', 'status'];

    public function invoice(): BelongsTo { return $this->belongsTo(BillingInvoice::class, 'invoice_id'); }
}
