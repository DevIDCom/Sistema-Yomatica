<?php

namespace App\Models;

use App\Domains\Productos\Infraestructure\Eloquent\Models\BillingInvoiceItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingInvoiceItemTax extends Model {
    public $timestamps = false;
    protected $fillable = ['invoice_item_id', 'tax_type', 'factor_type', 'rate_or_fee', 'amount'];

    public function item(): BelongsTo { return $this->belongsTo(BillingInvoiceItem::class, 'invoice_item_id'); }
}
