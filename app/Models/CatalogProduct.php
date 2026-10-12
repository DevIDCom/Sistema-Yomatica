<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogProduct extends Model {
    public $timestamps = false;
    protected $fillable = ['code', 'name', 'unit_cost', 'unit_price', 'sat_product_key', 'sat_unit_key'];

    public function stocks(): HasMany { return $this->hasMany(InventoryStock::class, 'product_id'); }
    public function movements(): HasMany { return $this->hasMany(InventoryMovement::class, 'product_id'); }
}