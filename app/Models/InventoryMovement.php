<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model {
    public $timestamps = false;
    protected $fillable = ['warehouse_id', 'product_id', 'movement_type', 'quantity', 'reference_type', 'reference_id'];

    public function warehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id'); }
    public function product(): BelongsTo { return $this->belongsTo(CatalogProduct::class, 'product_id'); }
}