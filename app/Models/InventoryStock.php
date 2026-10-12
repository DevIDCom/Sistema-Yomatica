<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStock extends Model {
    const CREATED_AT = null;
    protected $fillable = ['warehouse_id', 'product_id', 'stock_quantity'];

    public function warehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id'); }
    public function product(): BelongsTo { return $this->belongsTo(CatalogProduct::class, 'product_id'); }
}