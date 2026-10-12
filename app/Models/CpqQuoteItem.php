<?php

namespace App\Models;

use App\Domains\Cotizaciones\Infraestructure\Eloquent\Models\CpqQuote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpqQuoteItem extends Model {
    public $timestamps = false;
    protected $fillable = ['quote_id', 'product_id', 'description', 'quantity', 'unit_price', 'unit_cost'];

    public function quote(): BelongsTo { return $this->belongsTo(CpqQuote::class, 'quote_id'); }
    public function product(): BelongsTo { return $this->belongsTo(CatalogProduct::class, 'product_id'); }
}
