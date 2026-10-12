<?php

namespace App\Domains\Productos\Infraestructure\Eloquent\Models;

use App\Domains\Facturas\Infraestructure\Eloquent\Models\BillingInvoice;
use App\Models\BillingInvoiceItemTax;
use Database\Factories\BillingInvoiceItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingInvoiceItem extends Model {
    use HasFactory;

    protected static function newFactory():BillingInvoiceItemFactory{
        return BillingInvoiceItemFactory::new();
    }

    public $timestamps = false;
    protected $fillable =
        ['invoice_id', 'sat_product_key', 'sat_unit_key', 'description', 'total','file_path', 'file_type'];

    public function invoice(): BelongsTo { return $this->belongsTo(BillingInvoice::class, 'invoice_id'); }
    public function taxes(): HasMany { return $this->hasMany(BillingInvoiceItemTax::class, 'invoice_item_id'); }
}
